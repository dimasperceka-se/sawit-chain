/**
 * sync-compliance — cron tiap jam.
 *
 * Alur:
 *   1. Tarik GeoJSON plot petani dari dashboard (DASHBOARD_GEOJSON_URL).
 *   2. Normalisasi koordinat ke [lng, lat] standar GeoJSON (DB simpan [lat, lng]).
 *   3. POST FeatureCollection ke API compliance:
 *        {COMPLIANCE_API_BASE}/api/v1/deforestation
 *        {COMPLIANCE_API_BASE}/api/v1/protected-area
 *   4. Cocokkan hasil per-plot (by plot_uid -> id -> index).
 *   5. UPSERT ke tabel twin_plots (Postgres/PostGIS), prune plot yang sudah hilang.
 *
 * Jalankan:  cd palm-twin && npx tsx server/jobs/sync-compliance.ts
 * Butuh env: DATABASE_URL, DASHBOARD_GEOJSON_URL, COMPLIANCE_API_BASE
 */
import "dotenv/config";
import pkg from "pg";

const { Pool } = pkg;

const DASHBOARD_GEOJSON_URL =
  process.env.DASHBOARD_GEOJSON_URL ||
  "https://portal.sawitchain.com/api/api_gis/plots/geojson";
const COMPLIANCE_API_BASE =
  process.env.COMPLIANCE_API_BASE || "https://global-compliance-system.com";

if (!process.env.DATABASE_URL) {
  console.error("❌ DATABASE_URL belum di-set");
  process.exit(1);
}

const pool = new Pool({ connectionString: process.env.DATABASE_URL });

type Feature = {
  type: "Feature";
  id?: number | string;
  geometry: any;
  properties: Record<string, any>;
};
type FC = { type: "FeatureCollection"; features: Feature[] };

const log = (...a: any[]) =>
  console.log(`[sync-compliance ${new Date().toISOString()}]`, ...a);

/* ── 2. Normalisasi koordinat ─────────────────────────────────────────────
 * Geometry dashboard tersimpan sebagai POINT(lat lng) -> ST_AsGeoJSON keluar
 * [lat, lng]. GeoJSON valid butuh [lng, lat]. Deteksi: utk Indonesia |lat|<=11
 * dan |lng|>11, jadi kalau |c[0]|<=11 && |c[1]|>11 -> tertukar, balik.        */
function looksSwapped(geom: any): boolean {
  const first = firstCoord(geom);
  if (!first) return false;
  const [a, b] = first;
  return Math.abs(a) <= 11 && Math.abs(b) > 11;
}
function firstCoord(geom: any): [number, number] | null {
  let c = geom?.coordinates;
  while (Array.isArray(c) && Array.isArray(c[0])) c = c[0];
  return Array.isArray(c) && typeof c[0] === "number"
    ? [c[0], c[1]]
    : null;
}
function swapCoords(node: any): any {
  if (
    Array.isArray(node) &&
    typeof node[0] === "number" &&
    typeof node[1] === "number"
  ) {
    return [node[1], node[0], ...node.slice(2)];
  }
  return Array.isArray(node) ? node.map(swapCoords) : node;
}
function normalizeGeometry(geom: any): any {
  if (geom && looksSwapped(geom)) {
    return { ...geom, coordinates: swapCoords(geom.coordinates) };
  }
  return geom;
}

/* ── 3. POST GeoJSON ke endpoint compliance (multipart/form-data) ────────── */
async function postCompliance(path: string, fc: FC): Promise<FC | null> {
  const url = `${COMPLIANCE_API_BASE}${path}`;
  try {
    const blob = new Blob([JSON.stringify(fc)], { type: "application/json" });
    const form = new FormData();
    form.append("file", blob, "plots.geojson");
    const res = await fetch(url, { method: "POST", body: form });
    if (!res.ok) {
      log(`⚠️  ${path} -> HTTP ${res.status}`, (await res.text()).slice(0, 300));
      return null;
    }
    const json: any = await res.json();
    return (json?.data as FC) || null;
  } catch (e: any) {
    log(`⚠️  ${path} gagal:`, e?.message || e);
    return null;
  }
}

/* ── 4. Index hasil agar bisa dicocokkan ke input ─────────────────────────
 * Prioritas: properties.plot_uid -> id -> urutan (index).                     */
function indexResults(input: FC, result: FC | null): Map<string, any> {
  const byUid = new Map<string, any>();
  if (!result?.features) return byUid;
  result.features.forEach((rf, i) => {
    const p = rf.properties || {};
    let uid: string | undefined = p.plot_uid;
    if (!uid && (rf.id !== undefined || p.id !== undefined)) {
      const fid = rf.id ?? p.id;
      uid = input.features.find((f) => (f.id ?? f.properties?.id) === fid)
        ?.properties?.plot_uid;
    }
    if (!uid) uid = input.features[i]?.properties?.plot_uid; // fallback index
    if (uid) byUid.set(uid, p);
  });
  return byUid;
}

const num = (v: any): number | null =>
  v === null || v === undefined || v === "" || Number.isNaN(Number(v))
    ? null
    : Number(v);

async function main() {
  log("mulai sync");

  // 1. tarik dari dashboard
  const res = await fetch(DASHBOARD_GEOJSON_URL);
  if (!res.ok) throw new Error(`dashboard geojson HTTP ${res.status}`);
  const raw: FC = await res.json();
  const input: FC = {
    type: "FeatureCollection",
    features: (raw.features || []).map((f) => ({
      ...f,
      geometry: normalizeGeometry(f.geometry),
    })),
  };
  log(`dashboard: ${input.features.length} plot`);
  if (!input.features.length) {
    log("tidak ada plot — selesai");
    await pool.end();
    return;
  }

  // 3. panggil kedua endpoint compliance (paralel)
  const [defor, prot] = await Promise.all([
    postCompliance("/api/v1/deforestation", input),
    postCompliance("/api/v1/protected-area", input),
  ]);
  log(
    `deforestation: ${defor?.features?.length ?? "gagal"} · ` +
      `protected-area: ${prot?.features?.length ?? "gagal"}`
  );

  // 4. cocokkan
  const deforByUid = indexResults(input, defor);
  const protByUid = indexResults(input, prot);

  // 5. UPSERT per plot + prune
  const client = await pool.connect();
  let upserted = 0;
  try {
    await client.query("BEGIN");
    const seenUids: string[] = [];

    for (const f of input.features) {
      const pr = f.properties || {};
      const uid: string | undefined = pr.plot_uid;
      if (!uid || !f.geometry) continue;
      seenUids.push(uid);

      const d = deforByUid.get(uid) || {};
      const oc = d.overall_compliance || {};
      const yearly = {
        gfw: d.gfw_loss?.yearly ?? null,
        jrc: d.jrc_loss?.yearly ?? null,
        sbtn: d.sbtn_loss?.yearly ?? null,
      };
      const w = protByUid.get(uid) || {};

      await client.query(
        `INSERT INTO twin_plots (
           plot_uid, member_id, plot_nr, survey_nr, farmer, province, district, area_ha, geom,
           eudr_compliant, deforestation_detected, risk_level,
           gfw_loss_ha, jrc_loss_ha, sbtn_loss_ha, defor_yearly,
           wdpa_status, wdpa_categories, last_checked, synced_at
         ) VALUES (
           $1,$2,$3,$4,$5,$6,$7,$8, ST_SetSRID(ST_GeomFromGeoJSON($9),4326),
           $10,$11,$12,$13,$14,$15,$16::jsonb,$17,$18::jsonb, now(), now()
         )
         ON CONFLICT (plot_uid) DO UPDATE SET
           member_id = EXCLUDED.member_id, plot_nr = EXCLUDED.plot_nr,
           survey_nr = EXCLUDED.survey_nr, farmer = EXCLUDED.farmer,
           province = EXCLUDED.province, district = EXCLUDED.district,
           area_ha = EXCLUDED.area_ha, geom = EXCLUDED.geom,
           eudr_compliant = EXCLUDED.eudr_compliant,
           deforestation_detected = EXCLUDED.deforestation_detected,
           risk_level = EXCLUDED.risk_level,
           gfw_loss_ha = EXCLUDED.gfw_loss_ha, jrc_loss_ha = EXCLUDED.jrc_loss_ha,
           sbtn_loss_ha = EXCLUDED.sbtn_loss_ha, defor_yearly = EXCLUDED.defor_yearly,
           wdpa_status = EXCLUDED.wdpa_status, wdpa_categories = EXCLUDED.wdpa_categories,
           last_checked = EXCLUDED.last_checked, synced_at = EXCLUDED.synced_at`,
        [
          uid,
          num(pr.member_id),
          num(pr.plot_nr),
          num(pr.survey_nr),
          pr.farmer ?? pr.name ?? null,
          pr.province ?? null,
          pr.district ?? null,
          num(pr.area_ha),
          JSON.stringify(f.geometry),
          oc.eudr_compliant ?? null,
          oc.deforestation_detected ?? null,
          d.risk_level ?? null,
          num(d.gfw_loss?.area_ha),
          num(d.jrc_loss?.area_ha),
          num(d.sbtn_loss?.area_ha),
          JSON.stringify(yearly),
          w.wdpa_status ?? null,
          JSON.stringify(w.wdpa_categories ?? []),
        ]
      );
      upserted++;
    }

    // prune: buang plot yang sudah tidak ada di dashboard
    const pruned = await client.query(
      `DELETE FROM twin_plots WHERE plot_uid <> ALL($1::text[])`,
      [seenUids]
    );
    await client.query("COMMIT");
    log(`✅ upsert ${upserted} plot · prune ${pruned.rowCount} plot`);
  } catch (e) {
    await client.query("ROLLBACK");
    throw e;
  } finally {
    client.release();
  }

  await pool.end();
  log("selesai");
}

main().catch(async (e) => {
  log("❌ error:", e?.message || e);
  await pool.end().catch(() => {});
  process.exit(1);
});
