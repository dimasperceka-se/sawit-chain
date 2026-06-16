import type { Express } from "express";
import { type Server } from "http";
import { pool } from "./db";

export async function registerRoutes(
  _httpServer: Server,
  app: Express
): Promise<void> {
  // Login — validates against server-side env (AUTH_EMAIL / AUTH_PASSWORD).
  // Credentials never live in the client bundle or the repo.
  app.post("/api/login", (req, res) => {
    const { email, password } = (req.body || {}) as {
      email?: string;
      password?: string;
    };
    const okEmail = (process.env.AUTH_EMAIL || "").trim().toLowerCase();
    const okPass = process.env.AUTH_PASSWORD || "";
    const match =
      !!okEmail &&
      !!okPass &&
      String(email || "").trim().toLowerCase() === okEmail &&
      String(password || "") === okPass;
    if (match) return res.json({ ok: true });
    return res
      .status(401)
      .json({ ok: false, message: "Incorrect email or password." });
  });

  // Health check
  app.get("/api/health", (_req, res) => {
    res.status(200).json({
      status: "ok",
      timestamp: new Date().toISOString(),
      uptime: process.uptime(),
      environment: process.env.NODE_ENV,
    });
  });

  // ===== Peatland (peatland_idn) =====
  app.get("/api/peatland/filters", async (_req, res) => {
    try {
      const result = await pool.query(`
        SELECT
          ARRAY(SELECT DISTINCT provinsi FROM peatland_idn WHERE provinsi IS NOT NULL AND provinsi <> '' ORDER BY provinsi) AS provinsi,
          ARRAY(SELECT DISTINCT kubah__gbt FROM peatland_idn WHERE kubah__gbt IS NOT NULL AND kubah__gbt <> '' ORDER BY kubah__gbt) AS kubah,
          ARRAY(SELECT DISTINCT nama_khg FROM peatland_idn WHERE nama_khg IS NOT NULL AND nama_khg <> '' ORDER BY nama_khg) AS khg
      `);
      res.json(result.rows[0] || { provinsi: [], kubah: [], khg: [] });
    } catch (error: any) {
      console.error("❌ peatland/filters:", error);
      res.status(500).json({ error: error.message || "Failed to load filters" });
    }
  });

  // Peatland features as GeoJSON FeatureCollection (filtered + bbox)
  app.get("/api/peatland/features", async (req, res) => {
    try {
      const { provinsi, kubah, khg, bbox, limit } = req.query as Record<string, string>;
      const conditions: string[] = [];
      const params: any[] = [];
      let p = 1;

      if (provinsi) {
        conditions.push(`provinsi = $${p++}`);
        params.push(provinsi);
      }
      if (kubah) {
        conditions.push(`kubah__gbt = $${p++}`);
        params.push(kubah);
      }
      if (khg) {
        conditions.push(`nama_khg = $${p++}`);
        params.push(khg);
      }

      const hasExplicitFilter = !!(provinsi || kubah || khg);
      if (!hasExplicitFilter && bbox) {
        const parts = bbox.split(",").map(Number);
        if (parts.length === 4 && parts.every((n) => Number.isFinite(n))) {
          const [w, s, e, n] = parts;
          conditions.push(
            `geom && ST_MakeEnvelope($${p++}, $${p++}, $${p++}, $${p++}, 4326)`
          );
          params.push(w, s, e, n);
        }
      }

      const where = conditions.length ? `WHERE ${conditions.join(" AND ")}` : "";
      const cap = Math.min(parseInt(limit || "2000", 10) || 2000, 5000);

      const sql = `
        WITH rows AS (
          SELECT gid, nama_khg, kubah__gbt, status_khg, provinsi, kabupaten, luas__ha,
                 ST_AsGeoJSON(ST_SimplifyPreserveTopology(geom, 0.001))::json AS geometry
          FROM peatland_idn
          ${where}
          LIMIT ${cap}
        )
        SELECT json_build_object(
          'type', 'FeatureCollection',
          'features', COALESCE(json_agg(
            json_build_object(
              'type', 'Feature',
              'geometry', geometry,
              'properties', json_build_object(
                'gid', gid,
                'nama_khg', nama_khg,
                'kubah__gbt', kubah__gbt,
                'status_khg', status_khg,
                'provinsi', provinsi,
                'kabupaten', kabupaten,
                'luas_ha', luas__ha
              )
            )
          ), '[]'::json)
        ) AS geojson
        FROM rows
      `;

      const result = await pool.query(sql, params);
      res.json(result.rows[0]?.geojson || { type: "FeatureCollection", features: [] });
    } catch (error: any) {
      console.error("❌ peatland/features:", error);
      res.status(500).json({ error: error.message || "Failed to load features" });
    }
  });

  // ===== Palm oil mills (palmoil_mill) =====
  app.get("/api/palmoil/mills", async (req, res) => {
    try {
      const { bbox, country, limit } = req.query as Record<string, string>;
      const conditions: string[] = ["geom IS NOT NULL"];
      const params: any[] = [];
      let p = 1;

      if (country) {
        conditions.push(`country = $${p++}`);
        params.push(country);
      }
      if (bbox) {
        const parts = bbox.split(",").map(Number);
        if (parts.length === 4 && parts.every((n) => Number.isFinite(n))) {
          const [w, s, e, n] = parts;
          conditions.push(
            `geom && ST_MakeEnvelope($${p++}, $${p++}, $${p++}, $${p++}, 4326)`
          );
          params.push(w, s, e, n);
        }
      }

      const where = `WHERE ${conditions.join(" AND ")}`;
      const cap = Math.min(parseInt(limit || "5000", 10) || 5000, 10000);

      const sql = `
        WITH rows AS (
          SELECT gid, uml_id, mill_name, group_name, country, province, district,
                 ST_AsGeoJSON(geom)::json AS geometry
          FROM palmoil_mill
          ${where}
          LIMIT ${cap}
        )
        SELECT json_build_object(
          'type', 'FeatureCollection',
          'features', COALESCE(json_agg(
            json_build_object(
              'type', 'Feature',
              'geometry', geometry,
              'properties', json_build_object(
                'gid', gid,
                'uml_id', uml_id,
                'mill_name', mill_name,
                'group_name', group_name,
                'country', country,
                'province', province,
                'district', district
              )
            )
          ), '[]'::json)
        ) AS geojson
        FROM rows
      `;

      const result = await pool.query(sql, params);
      res.json(result.rows[0]?.geojson || { type: "FeatureCollection", features: [] });
    } catch (error: any) {
      console.error("❌ palmoil/mills:", error);
      res.status(500).json({ error: error.message || "Failed to load mills" });
    }
  });

  // ===== Oil-palm plots (plots_ady) =====
  // Province list with extent, for the required filter dropdown
  app.get("/api/kebun-sawit/provinces", async (_req, res) => {
    try {
      // Extent computed only over plots inside Indonesia's bounds so a stray
      // bad coordinate doesn't blow up the "fit to province" zoom.
      const idnEnv = `ST_MakeEnvelope(94, -11, 142, 7, 4326)`;
      const result = await pool.query(`
        SELECT province,
               count(*)::int AS count,
               ST_XMin(ST_Extent(geom) FILTER (WHERE geom && ${idnEnv})) AS w,
               ST_YMin(ST_Extent(geom) FILTER (WHERE geom && ${idnEnv})) AS s,
               ST_XMax(ST_Extent(geom) FILTER (WHERE geom && ${idnEnv})) AS e,
               ST_YMax(ST_Extent(geom) FILTER (WHERE geom && ${idnEnv})) AS n
        FROM plots_ady
        WHERE geom IS NOT NULL AND province IS NOT NULL
        GROUP BY province
        ORDER BY count DESC
      `);
      res.json(result.rows);
    } catch (error: any) {
      console.error("❌ kebun-sawit/provinces:", error);
      res.status(500).json({ error: error.message || "Failed to load provinces" });
    }
  });

  // Plots as GeoJSON — REQUIRES a province; optional bbox; capped
  app.get("/api/kebun-sawit/plots", async (req, res) => {
    try {
      const { province, bbox, limit } = req.query as Record<string, string>;
      if (!province) {
        return res.json({ type: "FeatureCollection", features: [] });
      }

      const conditions: string[] = ["geom IS NOT NULL", "province = $1"];
      const params: any[] = [province];
      let p = 2;

      if (bbox) {
        const parts = bbox.split(",").map(Number);
        if (parts.length === 4 && parts.every((n) => Number.isFinite(n))) {
          const [w, s, e, n] = parts;
          conditions.push(
            `geom && ST_MakeEnvelope($${p++}, $${p++}, $${p++}, $${p++}, 4326)`
          );
          params.push(w, s, e, n);
        }
      }

      const where = `WHERE ${conditions.join(" AND ")}`;
      const cap = Math.min(parseInt(limit || "2000", 10) || 2000, 4000);

      const sql = `
        WITH rows AS (
          SELECT plot, farmer, province, district, polygonarea,
                 ST_AsGeoJSON(geom)::json AS geometry
          FROM plots_ady
          ${where}
          LIMIT ${cap}
        )
        SELECT json_build_object(
          'type', 'FeatureCollection',
          'features', COALESCE(json_agg(
            json_build_object(
              'type', 'Feature',
              'geometry', geometry,
              'properties', json_build_object(
                'plot', plot,
                'farmer', farmer,
                'province', province,
                'district', district,
                'polygonarea', polygonarea
              )
            )
          ), '[]'::json)
        ) AS geojson
        FROM rows
      `;

      const result = await pool.query(sql, params);
      res.json(result.rows[0]?.geojson || { type: "FeatureCollection", features: [] });
    } catch (error: any) {
      console.error("❌ kebun-sawit/plots:", error);
      res.status(500).json({ error: error.message || "Failed to load plots" });
    }
  });
}
