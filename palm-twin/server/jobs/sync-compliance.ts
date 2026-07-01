/**
 * sync-compliance — cron tiap jam.
 *
 * Alur:
 *   1. Tarik GeoJSON plot petani dari dashboard (DASHBOARD_GEOJSON_URL).
 *   2. Normalisasi koordinat ke [lng, lat] standar GeoJSON (DB simpan [lat, lng]).
 *   3. POST FeatureCollection ke API compliance:
 *        {COMPLIANCE_API_BASE}/api/v1/upload-geojson-notrounded-nobrwa
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

// API compliance timeout kalau dikirim ribuan fitur sekaligus -> kirim per-chunk.
// Semua tunable lewat env tanpa perlu rebuild image.
const CHUNK_SIZE = parseInt(process.env.SYNC_CHUNK_SIZE || "100", 10);
const CHUNK_CONCURRENCY = parseInt(process.env.SYNC_CHUNK_CONCURRENCY || "4", 10);
const REQUEST_TIMEOUT_MS = parseInt(
  process.env.SYNC_REQUEST_TIMEOUT_MS || "120000",
  10
);

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
    const res = await fetch(url, {
      method: "POST",
      body: form,
      signal: AbortSignal.timeout(REQUEST_TIMEOUT_MS),
    });
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

/* ── 4. Gabung hasil satu chunk ke map berdasar POSISI ────────────────────
 * API compliance mengembalikan fitur dgn urutan & jumlah sama dgn input
 * (total_processed = features_count), jadi cocokkan per-index dalam chunk.
 * plot_uid tidak di-echo API, index adalah cara paling andal.               */
function mergeChunk(
  chunkInput: Feature[],
  result: FC | null,
  target: Map<string, any>
): void {
  const feats = result?.features || [];
  feats.forEach((rf, i) => {
    const uid = chunkInput[i]?.properties?.plot_uid;
    if (uid) target.set(uid, rf.properties || {});
  });
}

/* Jalankan `worker` atas `items` dgn batas konkurensi. */
async function runPool<T>(
  items: T[],
  concurrency: number,
  worker: (item: T, index: number) => Promise<void>
): Promise<void> {
  let idx = 0;
  const n = Math.max(1, Math.min(concurrency, items.length));
  const runners = Array.from({ length: n }, async () => {
    while (idx < items.length) {
      const my = idx++;
      await worker(items[my], my);
    }
  });
  await Promise.all(runners);
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

  // 3. bagi jadi chunk lalu panggil endpoint compliance per-chunk (konkuren).
  const chunks: Feature[][] = [];
  for (let i = 0; i < input.features.length; i += CHUNK_SIZE) {
    chunks.push(input.features.slice(i, i + CHUNK_SIZE));
  }
  log(
    `memproses ${chunks.length} chunk @${CHUNK_SIZE} (konkurensi ${CHUNK_CONCURRENCY})`
  );

  const deforByUid = new Map<string, any>();
  const protByUid = new Map<string, any>();
  let okDefor = 0;
  let okProt = 0;

  await runPool(chunks, CHUNK_CONCURRENCY, async (chunk, ci) => {
    const fc: FC = { type: "FeatureCollection", features: chunk };
    const [defor, prot] = await Promise.all([
      postCompliance("/api/v1/upload-geojson-notrounded-nobrwa", fc),
      postCompliance("/api/v1/protected-area", fc),
    ]);
    if (defor) {
      mergeChunk(chunk, defor, deforByUid);
      okDefor++;
    }
    if (prot) {
      mergeChunk(chunk, prot, protByUid);
      okProt++;
    }
    if ((ci + 1) % 10 === 0 || ci + 1 === chunks.length) {
      log(
        `chunk ${ci + 1}/${chunks.length} — defor ok:${okDefor} prot ok:${okProt}`
      );
    }
  });
  log(
    `chunk sukses: deforestation ${okDefor}/${chunks.length} · ` +
      `protected-area ${okProt}/${chunks.length}`
  );

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
      // Skema respons API nyata:
      //   overall_compliance: { compliance_status: 'COMPLIANT'|'NON_COMPLIANT',
      //                         overall_risk: 'low'|'high', ... }
      //   <ds>_loss: { <ds>_loss_area, <ds>_loss_percent, <ds>_loss_stat,
      //                <ds>_loss_year_compilation }
      const riskLevel = oc.overall_risk
        ? String(oc.overall_risk).toLowerCase() === "high"
          ? "High"
          : "Low"
        : null;
      const eudrCompliant =
        oc.compliance_status != null
          ? String(oc.compliance_status).toUpperCase() === "COMPLIANT"
          : null;
      const deforDetected =
        oc.compliance_status != null
          ? String(oc.compliance_status).toUpperCase() !== "COMPLIANT"
          : null;
      const yearly = {
        gfw: d.gfw_loss?.gfw_loss_year_compilation ?? null,
        jrc: d.jrc_loss?.jrc_loss_year_compilation ?? null,
        sbtn: d.sbtn_loss?.sbtn_loss_year_compilation ?? null,
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
          eudrCompliant,
          deforDetected,
          riskLevel,
          num(d.gfw_loss?.gfw_loss_area),
          num(d.jrc_loss?.jrc_loss_area),
          num(d.sbtn_loss?.sbtn_loss_area),
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
