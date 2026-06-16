# 🌴 Palm Oil Digital Twin

A self-contained, deployable web app: an interactive geospatial **digital twin** for oil-palm
plantations. It renders oil-palm plots, palm-oil mills, peatland areas, and live fire hotspots
on a multi-basemap map, with a header-driven analysis panel (Deforestation, Hotspot, Protected
Area, Peatland, Legality — currently dummy data, ready to wire to real queries).

This is a standalone extraction — it does **not** depend on the WikiPeat platform.

## Stack

- **Frontend:** React 19 + Vite, Leaflet (+ markercluster, vector-tile for GFW fire tiles), Recharts, Tailwind v4, lucide-react.
- **Backend:** Express + `pg` (raw SQL against PostGIS). Single process serves both the API and the client.
- **Dev:** Vite middleware + HMR through the Express server (one port).
- **Prod:** `vite build` → static bundle; Express serves it + the API.

## Prerequisites

- Node.js v20+
- A **PostGIS-enabled PostgreSQL** containing these tables (SRID 4326 geometries):
  - `peatland_idn` — peatland polygons (`geom`, `provinsi`, `kubah__gbt`, `nama_khg`, `status_khg`, `kabupaten`, `luas__ha`)
  - `palmoil_mill` — mill points (`geom`, `uml_id`, `mill_name`, `group_name`, `country`, `province`, `district`)
  - `plots_ady` — oil-palm plot polygons (`geom` MultiPolygon, `province`, `district`, `farmer`, `plot`, `polygonarea`)

> Fire hotspots are fetched client-side from Global Forest Watch public tiles (NASA VIIRS) — no key or DB needed.

## Quick start

```bash
# 1. Install
npm install

# 2. Configure
cp .env.example .env        # set DATABASE_URL (and PORT)

# 3. Run (dev, with HMR)
npm run dev
```

App runs at **http://localhost:5003**.

## Production

```bash
npm install
npm run build      # builds client → dist/public
npm start          # NODE_ENV=production, serves dist/public + /api
```

### Docker

```bash
docker build -t palm-oil-digital-twin .
docker run -p 5003:5003 -e DATABASE_URL="postgresql://user:pass@host:5432/db" palm-oil-digital-twin
```

## Environment variables

| Variable       | Required | Description                                              |
|----------------|----------|----------------------------------------------------------|
| `DATABASE_URL` | yes      | PostGIS Postgres connection string                       |
| `PORT`         | no       | Port to serve on (default `5003`)                        |

## API

| Endpoint                          | Description                                                        |
|-----------------------------------|-------------------------------------------------------------------|
| `GET /api/health`                 | Liveness probe                                                    |
| `GET /api/peatland/filters`       | Distinct provinsi / kubah / KHG for the peatland filter           |
| `GET /api/peatland/features`      | Peatland polygons as GeoJSON (filter by provinsi/kubah/khg/bbox)  |
| `GET /api/palmoil/mills`          | Mill points as GeoJSON (bbox/country)                             |
| `GET /api/kebun-sawit/provinces`  | Plot provinces + extent (drives the required province filter)     |
| `GET /api/kebun-sawit/plots`      | Plot polygons as GeoJSON — **requires** `province`; bbox + cap    |

## Project structure

```
palm-oil-digital-twin/
├── client/
│   ├── index.html
│   └── src/
│       ├── main.tsx
│       ├── App.tsx
│       ├── index.css
│       ├── components/layout/ProductSidebar.tsx
│       └── pages/PalmOilDigitalTwin.tsx     # the whole feature
├── server/
│   ├── index.ts        # express entry (dev: vite middleware, prod: static)
│   ├── routes.ts       # the 5 map endpoints + health
│   ├── db.ts           # pg Pool
│   ├── vite.ts         # dev middleware
│   └── static.ts       # prod static serving
├── vite.config.ts
├── tsconfig.json
├── Dockerfile
└── .env.example
```

## Notes

- The analysis panel (header tabs) uses **dummy data** in `buildThemes()` inside
  `client/src/pages/PalmOilDigitalTwin.tsx` — replace each entry with real queries when ready.
- Large plot layers are intentionally gated behind a **required province filter** + bbox + row cap
  to keep the map responsive.
