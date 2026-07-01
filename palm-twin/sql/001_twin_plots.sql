-- Stage 1 — Tabel penampung plot petani dari dashboard + hasil compliance.
-- Dijalankan SEKALI di Postgres/PostGIS `wikipeat` (DB yang dipakai twin).
--   psql "$DATABASE_URL" -f palm-twin/sql/001_twin_plots.sql
-- Diisi & di-refresh tiap jam oleh palm-twin/server/jobs/sync-compliance.ts

CREATE EXTENSION IF NOT EXISTS postgis;

CREATE TABLE IF NOT EXISTS twin_plots (
  -- identitas plot (stabil) — kunci sinkron dari dashboard
  plot_uid        text PRIMARY KEY,          -- "<MemberID>-<PlotNr>-<SurveyNr>"
  member_id       integer,
  plot_nr         integer,
  survey_nr       integer,

  -- atribut dari dashboard
  farmer          text,
  province        text,
  district        text,
  area_ha         double precision,
  geom            geometry(Geometry, 4326),

  -- hasil /api/v1/deforestation
  eudr_compliant         boolean,
  deforestation_detected boolean,
  risk_level             text,               -- "High" | "Low"
  gfw_loss_ha            double precision,
  jrc_loss_ha            double precision,
  sbtn_loss_ha           double precision,
  defor_yearly           jsonb,              -- {"gfw":{...},"jrc":{...},"sbtn":{...}}

  -- hasil /api/v1/protected-area
  wdpa_status            text,               -- "compliant" | "indicative" | "non-compliant"
  wdpa_categories        jsonb,              -- ["II","IV"]

  -- meta sinkron
  last_checked    timestamptz,               -- kapan terakhir di-skor compliance
  synced_at       timestamptz NOT NULL DEFAULT now()
);

-- index spasial untuk query bbox dari peta
CREATE INDEX IF NOT EXISTS twin_plots_geom_gix ON twin_plots USING GIST (geom);
CREATE INDEX IF NOT EXISTS twin_plots_province_ix ON twin_plots (province);
CREATE INDEX IF NOT EXISTS twin_plots_risk_ix ON twin_plots (risk_level);
CREATE INDEX IF NOT EXISTS twin_plots_wdpa_ix ON twin_plots (wdpa_status);
