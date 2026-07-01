import { useEffect, useMemo, useRef, useState } from "react";
import { Link } from "wouter";
import { Area, AreaChart, ResponsiveContainer } from "recharts";
import L from "leaflet";
import "leaflet/dist/leaflet.css";
import "leaflet.markercluster";
import "leaflet.markercluster/dist/MarkerCluster.css";
import "leaflet.markercluster/dist/MarkerCluster.Default.css";
import { VectorTile } from "@mapbox/vector-tile";
import Pbf from "pbf";
import {
  Leaf,
  Search,
  Clock,
  ExternalLink,
  Bell,
  FileText,
  Activity,
  Droplets,
  Thermometer,
  Sprout,
  FlaskConical,
  Flame,
  Factory,
  Trees,
  Layers,
  Globe,
  Map as MapIcon,
  Moon,
  Shield,
  Scale,
  MapPin,
  ChevronDown,
  ChevronUp,
  Check,
  X,
  Lightbulb,
  AlertTriangle,
  Info,
  Plus,
  Target,
  Crosshair,
  LogOut,
} from "lucide-react";
import plotsGeojsonRaw from "@/plot_kebun_sawit_sample100.geojson?raw";
import deforestationRaw from "@/deforestation.json?raw";
import hotspotRadiusRaw from "@/polygon_radius_hotspot.geojson?raw";
import radiusVisRaw from "@/radius_vis_polygon.geojson?raw";
import pointRadiusHotspotRaw from "@/point_radius_hotspot.geojson?raw";
// Large file (~15 MB) — imported as a URL and fetched lazily on first toggle.
import radiusVisPointUrl from "@/radius_vis_point.geojson?url";

// Real data shipped with the client.
const PLOTS_GEOJSON: any = JSON.parse(plotsGeojsonRaw);
const DEFORESTATION: any = JSON.parse(deforestationRaw);

// Plots flagged high-risk by the EUDR deforestation analysis. Keyed by plot_id,
// which matches the `plot` property on the kebun-sawit polygons.
const HIGH_RISK_PLOTS: Set<string> = new Set(
  ((DEFORESTATION?.data?.features as any[]) || [])
    .filter(
      (f) =>
        String(
          f?.properties?.overall_compliance?.overall_risk || ""
        ).toLowerCase() === "high"
    )
    .map((f) => String(f?.properties?.plot_id))
);
const isHighRiskPlot = (props: any): boolean =>
  HIGH_RISK_PLOTS.has(String(props?.plot));

const HOTSPOT_RADIUS: any = JSON.parse(hotspotRadiusRaw);
const RADIUS_VIS: any = JSON.parse(radiusVisRaw);
const POINT_RADIUS_HOTSPOT: any = JSON.parse(pointRadiusHotspotRaw);

// Kebun–hotspot overlap aggregate (from polygon_radius_hotspot.geojson).
const HOTSPOT_OVERLAP = (() => {
  const feats: any[] = HOTSPOT_RADIUS?.features || [];
  let hit1 = 0;
  let hit5 = 0;
  let hs5 = 0;
  for (const f of feats) {
    const p = f?.properties || {};
    if (p.hit_1km) hit1++;
    if (p.hit_5km) hit5++;
    hs5 += Number(p.hotspot_5km || 0);
  }
  return { total: feats.length, hit1, hit5, hs5 };
})();

// Mill–hotspot overlap aggregate + Indonesia mill ids (point_radius_hotspot.geojson).
// "hanya Indonesia": only country === "Indonesia" is counted / shown.
const MILL_OVERLAP = (() => {
  const feats: any[] = POINT_RADIUS_HOTSPOT?.features || [];
  const millIds = new Set<string>();
  let h10 = 0;
  let h20 = 0;
  let h30 = 0;
  let h50 = 0;
  let hs50 = 0;
  let total = 0;
  for (const f of feats) {
    const p = f?.properties || {};
    if (p.country !== "Indonesia") continue;
    total++;
    if (p.mill_id) millIds.add(String(p.mill_id));
    if (p.hit_10km) h10++;
    if (p.hit_20km) h20++;
    if (p.hit_30km) h30++;
    if (p.hit_50km) h50++;
    hs50 += Number(p.hotspot_50km || 0);
  }
  return { total, h10, h20, h30, h50, hs50, millIds };
})();

// Legality — synthetic split that aligns with the real data: legal + belum-legal
// plot counts sum to the actual plot total (101), and the real total area is split
// between them. Counts are randomized once per load (still placeholder/dummy).
const LEGALITY = (() => {
  const feats: any[] = PLOTS_GEOJSON?.features || [];
  const total = feats.length || 101;
  let totalArea = 0;
  for (const f of feats) totalArea += Number(f?.properties?.polygonarea || 0);
  const minLegal = Math.round(total * 0.65);
  const legalPlots =
    minLegal + Math.floor(Math.random() * (total - minLegal + 1));
  const belumPlots = total - legalPlots;
  const legalArea = Math.round(totalArea * (legalPlots / total) * 10) / 10;
  const belumArea = Math.round((totalArea - legalArea) * 10) / 10;
  return { total, totalArea, legalPlots, belumPlots, legalArea, belumArea };
})();

// Peatland — placeholder/random values aligned with the real data: "kebun di
// lahan gambut" is a random subset of the real plot total (101), with area taken
// proportionally from the real total area. (Random once per load; still dummy.)
const PEATLAND_RANDOM = (() => {
  const feats: any[] = PLOTS_GEOJSON?.features || [];
  const total = feats.length || 101;
  let totalArea = 0;
  for (const f of feats) totalArea += Number(f?.properties?.polygonarea || 0);
  const plotsInPeat = Math.round(total * (0.25 + Math.random() * 0.3)); // ~25–55%
  const areaInPeat = Math.round(totalArea * (plotsInPeat / total) * 10) / 10;
  const millsInPeat = 4 + Math.floor(Math.random() * 22); // 4–25 mill
  const provinceArea = 120000 + Math.floor(Math.random() * 80000); // 120k–200k ha
  return { plotsInPeat, areaInPeat, millsInPeat, provinceArea };
})();

const nbrData = [
  { x: 1, y: 0.42 },
  { x: 2, y: 0.48 },
  { x: 3, y: 0.55 },
  { x: 4, y: 0.50 },
  { x: 5, y: 0.38 },
  { x: 6, y: 0.30 },
  { x: 7, y: 0.35 },
  { x: 8, y: 0.45 },
  { x: 9, y: 0.52 },
  { x: 10, y: 0.58 },
  { x: 11, y: 0.62 },
  { x: 12, y: 0.60 },
];

const ndviData = [
  { x: 1, y: 0.55 },
  { x: 2, y: 0.62 },
  { x: 3, y: 0.68 },
  { x: 4, y: 0.72 },
  { x: 5, y: 0.78 },
  { x: 6, y: 0.74 },
  { x: 7, y: 0.70 },
  { x: 8, y: 0.76 },
  { x: 9, y: 0.82 },
  { x: 10, y: 0.85 },
  { x: 11, y: 0.80 },
  { x: 12, y: 0.83 },
];

const alerts = [
  {
    icon: Sprout,
    title: "Plant Health",
    desc: "Monitors crop vitality and growth.",
    iconBg: "bg-green-100",
    iconColor: "text-green-700",
    ok: true,
  },
  {
    icon: Droplets,
    title: "Soil Moisture",
    desc: "Tracks water levels in soil.",
    iconBg: "bg-blue-100",
    iconColor: "text-blue-600",
    ok: true,
  },
  {
    icon: FlaskConical,
    title: "Low pH",
    desc: "Acidic soil limits nutrients.",
    iconBg: "bg-purple-100",
    iconColor: "text-purple-600",
    ok: false,
  },
];

const sentinelYears = [2016, 2017, 2018, 2019, 2020, 2021, 2022, 2023, 2024];

const sentinelTileUrl = (year: number) =>
  `https://tiles.maps.eox.at/wmts/1.0.0/s2cloudless-${year}_3857/default/g/{z}/{y}/{x}.jpg`;

type Basemap = "s2" | "esri" | "osm" | "dark";

const BASEMAP_LABELS: Record<Basemap, string> = {
  s2: "S2 Cloudless",
  esri: "ESRI Satellite",
  osm: "OpenStreetMap",
  dark: "Dark",
};

// Layers that can appear in the bottom-left control stack. The "+" button
// lets the user add/remove these via checkboxes.
type LayerKey =
  | "plots"
  | "petani"
  | "mills"
  | "fires"
  | "peatland"
  | "klhk"
  | "radius"
  | "millradius";

const LAYER_META: { key: LayerKey; label: string; icon: any }[] = [
  { key: "petani", label: "Kebun Petani", icon: Trees },
  { key: "mills", label: "Palm Oil Mills", icon: Factory },
  { key: "fires", label: "Fire Hotspots", icon: Flame },
  { key: "peatland", label: "Peatland Areas", icon: Layers },
  { key: "klhk", label: "Kawasan Hutan (KLHK)", icon: Leaf },
  { key: "radius", label: "Radius Kebun (1 & 5 km)", icon: Target },
  { key: "millradius", label: "Radius Mill (10–50 km)", icon: Crosshair },
];

// ---- GEE forest / deforestation raster tile layers ----
// Source: https://global-compliance-system.com (Google Earth Engine tile service).
const GEE_API = "https://api.sawitchain.com/api/v1/gee";

type GeeDataset = { key: string; name: string; type: string; color: string };

// Fallback list mirroring GET /api/v1/gee/datasets; refreshed from the API at runtime.
// Only deforestation/loss datasets are shown (forest-cover layers are excluded).
const GEE_FALLBACK: GeeDataset[] = [
  { key: "gfw_loss", name: "GFW Forest Loss 2021-2024", type: "deforestation", color: "#FF4500" },
  { key: "jrc_loss", name: "JRC Forest Loss 2021-2024", type: "deforestation", color: "#FF6347" },
  { key: "sbtn_loss", name: "SBTN Forest Loss 2021-2024", type: "deforestation", color: "#FF8C00" },
  { key: "radd", name: "RADD Forest Disturbance Alerts", type: "deforestation", color: "#FF7F50" },
];

// KLHK "Kawasan Hutan" — ArcGIS MapServer (dynamic raster). Rendered as a
// per-tile `export` overlay so no esri-leaflet dependency is needed.
// geoportal.menlhk.go.id has an untrusted TLS cert that browsers block, so KLHK
// tiles are proxied through our own server (see /klhk in server/routes.ts).
// BASE_URL keeps it same-origin whether served at "/" or under "/twin/".
const KLHK_MAPSERVER =
  `${import.meta.env.BASE_URL}klhk/server/rest/services/SIGAP_Interaktif/Kawasan_Hutan/MapServer`;
// Real upstream, for the attribution link only.
const KLHK_SOURCE_URL =
  "https://geoportal.menlhk.go.id/server/rest/services/SIGAP_Interaktif/Kawasan_Hutan/MapServer";

const basemapConfig = (
  basemap: Basemap,
  year: number
): { url: string; attribution: string; options: L.TileLayerOptions } => {
  switch (basemap) {
    case "esri":
      return {
        url: "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}",
        attribution:
          'Imagery &copy; <a href="https://www.esri.com" target="_blank">Esri</a>, Maxar, Earthstar Geographics',
        options: { maxNativeZoom: 19, minZoom: 2, maxZoom: 19 },
      };
    case "osm":
      return {
        url: "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
        attribution:
          '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors',
        options: { maxNativeZoom: 19, minZoom: 2, maxZoom: 19, subdomains: "abc" },
      };
    case "dark":
      return {
        url: "https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png",
        attribution:
          '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OSM</a> &copy; <a href="https://carto.com/attributions" target="_blank">CARTO</a>',
        options: { maxNativeZoom: 20, minZoom: 2, maxZoom: 19, subdomains: "abcd" },
      };
    default:
      return {
        url: sentinelTileUrl(year),
        attribution:
          'Sentinel-2 cloudless &copy; <a href="https://s2maps.eu" target="_blank">EOX</a> · Fires &copy; <a href="https://www.globalforestwatch.org" target="_blank">GFW/NASA VIIRS</a>',
        options: { maxNativeZoom: 14, minZoom: 2, maxZoom: 19, tileSize: 256 },
      };
  }
};

const fireTileUrl = (
  z: number,
  x: number,
  y: number,
  start: string,
  end: string
) =>
  `https://tiles.globalforestwatch.org/nasa_viirs_fire_alerts/v20240815/dynamic/${z}/${x}/${y}.pbf?start_date=${start}&end_date=${end}`;

const lngLatToTile = (lng: number, lat: number, z: number) => {
  const n = 2 ** z;
  const x = Math.floor(((lng + 180) / 360) * n);
  const latRad = (lat * Math.PI) / 180;
  const y = Math.floor(
    ((1 - Math.log(Math.tan(latRad) + 1 / Math.cos(latRad)) / Math.PI) / 2) * n
  );
  return { x: Math.max(0, Math.min(n - 1, x)), y: Math.max(0, Math.min(n - 1, y)) };
};

const tilesInBounds = (bounds: L.LatLngBounds, z: number) => {
  const sw = bounds.getSouthWest();
  const ne = bounds.getNorthEast();
  const tl = lngLatToTile(sw.lng, ne.lat, z);
  const br = lngLatToTile(ne.lng, sw.lat, z);
  const tiles: { x: number; y: number; z: number }[] = [];
  for (let x = tl.x; x <= br.x; x++) {
    for (let y = tl.y; y <= br.y; y++) {
      tiles.push({ x, y, z });
    }
  }
  return tiles;
};

const fetchFireFeatures = async (
  z: number,
  x: number,
  y: number,
  start: string,
  end: string
): Promise<{ lat: number; lng: number; props: any }[]> => {
  try {
    const res = await fetch(fireTileUrl(z, x, y, start, end));
    if (!res.ok) return [];
    const buf = await res.arrayBuffer();
    if (buf.byteLength === 0) return [];
    const tile = new VectorTile(new Pbf(buf));
    const out: { lat: number; lng: number; props: any }[] = [];
    for (const layerName of Object.keys(tile.layers)) {
      const layer = tile.layers[layerName];
      for (let i = 0; i < layer.length; i++) {
        const feat = layer.feature(i);
        const geo = feat.toGeoJSON(x, y, z) as any;
        const coords = geo?.geometry?.coordinates;
        if (Array.isArray(coords) && typeof coords[0] === "number") {
          out.push({ lat: coords[1], lng: coords[0], props: geo.properties });
        }
      }
    }
    return out;
  } catch {
    return [];
  }
};

const dangerSvg = `<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="#dc2626" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="filter:drop-shadow(0 1px 2px rgba(0,0,0,0.4))"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4" stroke="#fff"/><path d="M12 17h.01" stroke="#fff"/></svg>`;

const dangerSvgActive = `<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="#0ea5e9" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="filter:drop-shadow(0 0 6px rgba(14,165,233,0.8))"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4" stroke="#fff"/><path d="M12 17h.01" stroke="#fff"/></svg>`;

const dangerIcon = L.divIcon({
  html: dangerSvg,
  className: "fire-danger-icon",
  iconSize: [22, 22],
  iconAnchor: [11, 11],
  popupAnchor: [0, -10],
});

const dangerIconActive = L.divIcon({
  html: dangerSvgActive,
  className: "fire-danger-icon",
  iconSize: [30, 30],
  iconAnchor: [15, 15],
  popupAnchor: [0, -14],
});

// Red danger triangle for deforestation-flagged (high-risk) oil-palm plots.
const plotDangerSvgActive = `<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="#dc2626" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="filter:drop-shadow(0 0 6px rgba(220,38,38,0.85))"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4" stroke="#fff"/><path d="M12 17h.01" stroke="#fff"/></svg>`;

const plotDangerIcon = L.divIcon({
  html: dangerSvg,
  className: "fire-danger-icon",
  iconSize: [22, 22],
  iconAnchor: [11, 11],
  popupAnchor: [0, -10],
});

const plotDangerIconActive = L.divIcon({
  html: plotDangerSvgActive,
  className: "fire-danger-icon",
  iconSize: [30, 30],
  iconAnchor: [15, 15],
  popupAnchor: [0, -14],
});

// Palm oil mill marker — violet circular badge with a factory glyph
const factoryGlyph = (size: number, stroke = "#fff") =>
  `<svg xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="${stroke}" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 20a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8l-7 5V8l-7 5V4a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/><path d="M17 18h.01"/><path d="M12 18h.01"/><path d="M7 18h.01"/></svg>`;

const millMarkerHtml = (active: boolean) => {
  const d = active ? 32 : 24;
  return `<div style="width:${d}px;height:${d}px;background:${
    active ? "#5b21b6" : "#7c3aed"
  };border:2px solid #fff;border-radius:9999px;display:flex;align-items:center;justify-content:center;box-shadow:0 1px 4px rgba(91,33,182,.55)${
    active ? ",0 0 0 5px rgba(124,58,237,.25)" : ""
  }">${factoryGlyph(active ? 16 : 13)}</div>`;
};

const millIcon = L.divIcon({
  html: millMarkerHtml(false),
  className: "mill-icon",
  iconSize: [24, 24],
  iconAnchor: [12, 12],
  popupAnchor: [0, -13],
});

const millIconActive = L.divIcon({
  html: millMarkerHtml(true),
  className: "mill-icon",
  iconSize: [32, 32],
  iconAnchor: [16, 16],
  popupAnchor: [0, -16],
});

const escapeHtml = (s: any) =>
  String(s ?? "").replace(
    /[&<>"]/g,
    (c) =>
      (({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;" }) as Record<
        string,
        string
      >)[c]
  );

const millPopupHtml = (p: any) => {
  const row = (label: string, value: any) =>
    `<div style="display:flex;gap:10px;align-items:flex-start;padding:6px 0;border-top:1px solid #f1f0f7"><span style="font-size:9px;color:#9ca3af;min-width:60px;text-transform:uppercase;letter-spacing:.05em;font-weight:700;padding-top:1px">${label}</span><span style="font-size:12px;color:#1f2937;font-weight:600;flex:1;line-height:1.35">${escapeHtml(
      value && String(value).trim() ? value : "—"
    )}</span></div>`;
  return `<div style="min-width:210px;max-width:260px;font-family:inherit;padding:2px">
      <div style="display:flex;align-items:center;gap:9px;padding-bottom:9px">
        <div style="height:34px;width:34px;border-radius:10px;background:linear-gradient(135deg,#7c3aed,#5b21b6);display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 2px 6px rgba(91,33,182,.4)">${factoryGlyph(
          17
        )}</div>
        <div style="min-width:0">
          <div style="font-size:8.5px;font-weight:800;letter-spacing:.1em;text-transform:uppercase;color:#7c3aed">Palm Oil Mill</div>
          <div style="font-size:13.5px;font-weight:800;color:#111827;line-height:1.2">${escapeHtml(
            p.mill_name && String(p.mill_name).trim() ? p.mill_name : "Unknown Mill"
          )}</div>
        </div>
      </div>
      ${row("Group", p.group_name)}
      ${row("Country", p.country)}
      ${row("Province", p.province)}
      ${row("District", p.district)}
    </div>`;
};

// Oil-palm plot (kebun sawit) popup — green themed
const leafGlyph = (size: number, stroke = "#fff") =>
  `<svg xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="${stroke}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>`;

// Oil-palm plot centroid marker — green leaf badge (clustered when zoomed out)
const leafMarkerHtml = (active: boolean) => {
  const d = active ? 32 : 24;
  return `<div style="width:${d}px;height:${d}px;background:${
    active ? "#3f6212" : "#65a30d"
  };border:2px solid #fff;border-radius:9999px;display:flex;align-items:center;justify-content:center;box-shadow:0 1px 4px rgba(63,98,18,.55)${
    active ? ",0 0 0 5px rgba(101,163,13,.25)" : ""
  }">${leafGlyph(active ? 16 : 13)}</div>`;
};

const leafIcon = L.divIcon({
  html: leafMarkerHtml(false),
  className: "mill-icon",
  iconSize: [24, 24],
  iconAnchor: [12, 12],
  popupAnchor: [0, -13],
});

const leafIconActive = L.divIcon({
  html: leafMarkerHtml(true),
  className: "mill-icon",
  iconSize: [32, 32],
  iconAnchor: [16, 16],
  popupAnchor: [0, -16],
});

const plotPopupHtml = (p: any) => {
  const area =
    p.polygonarea !== null && p.polygonarea !== undefined && p.polygonarea !== ""
      ? `${Number(p.polygonarea).toLocaleString("id-ID", { maximumFractionDigits: 2 })} Ha`
      : "—";
  const row = (label: string, value: any) =>
    `<div style="display:flex;gap:10px;align-items:flex-start;padding:6px 0;border-top:1px solid #eef2e6"><span style="font-size:9px;color:#9ca3af;min-width:64px;text-transform:uppercase;letter-spacing:.05em;font-weight:700;padding-top:1px">${label}</span><span style="font-size:12px;color:#1f2937;font-weight:600;flex:1;line-height:1.35">${escapeHtml(
      value && String(value).trim() ? value : "—"
    )}</span></div>`;
  return `<div style="min-width:214px;max-width:280px;font-family:inherit;padding:2px">
      <div style="display:flex;align-items:center;gap:9px;padding-bottom:9px">
        <div style="height:34px;width:34px;border-radius:10px;background:linear-gradient(135deg,#65a30d,#3f6212);display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 2px 6px rgba(63,98,18,.4)">${leafGlyph(
          18
        )}</div>
        <div style="min-width:0">
          <div style="font-size:8.5px;font-weight:800;letter-spacing:.1em;text-transform:uppercase;color:#4d7c0f">Kebun Sawit</div>
          <div style="font-size:12.5px;font-weight:800;color:#111827;line-height:1.2;word-break:break-all">${escapeHtml(
            p.plot && String(p.plot).trim() ? p.plot : "—"
          )}</div>
        </div>
      </div>
      ${row("Farmer", p.farmer)}
      ${row("Province", p.province)}
      ${row("District", p.district)}
      <div style="display:flex;gap:10px;align-items:flex-start;padding:6px 0;border-top:1px solid #eef2e6"><span style="font-size:9px;color:#9ca3af;min-width:64px;text-transform:uppercase;letter-spacing:.05em;font-weight:700;padding-top:1px">Luas</span><span style="font-size:12px;color:#1f2937;font-weight:600;flex:1;line-height:1.35">${area}</span></div>
    </div>`;
};

// ---- Kebun Petani (api/twin-plots) — compliance-colored polygons ----
type PetaniStatus = "non-compliant" | "indicative" | "compliant" | "unknown";

const petaniStatus = (p: any): PetaniStatus => {
  if (
    p?.eudr_compliant === false ||
    p?.risk_level === "High" ||
    p?.wdpa_status === "non-compliant"
  )
    return "non-compliant";
  if (p?.wdpa_status === "indicative") return "indicative";
  if (p?.last_checked != null) return "compliant";
  return "unknown";
};

// fill color + darker stroke color per status
const PETANI_COLORS: Record<PetaniStatus, { fill: string; stroke: string }> = {
  "non-compliant": { fill: "#dc2626", stroke: "#991b1b" },
  indicative: { fill: "#f59e0b", stroke: "#b45309" },
  compliant: { fill: "#16a34a", stroke: "#15803d" },
  unknown: { fill: "#9ca3af", stroke: "#6b7280" },
};

const petaniPopupHtml = (p: any) => {
  const fmtHa = (v: any) =>
    v !== null && v !== undefined && v !== ""
      ? `${Number(v).toLocaleString("id-ID", { maximumFractionDigits: 2 })} Ha`
      : "—";
  const row = (label: string, value: any) =>
    `<div style="display:flex;gap:10px;align-items:flex-start;padding:6px 0;border-top:1px solid #eef2e6"><span style="font-size:9px;color:#9ca3af;min-width:64px;text-transform:uppercase;letter-spacing:.05em;font-weight:700;padding-top:1px">${label}</span><span style="font-size:12px;color:#1f2937;font-weight:600;flex:1;line-height:1.35">${escapeHtml(
      value && String(value).trim() ? value : "—"
    )}</span></div>`;
  const colorRow = (label: string, value: string, color: string) =>
    `<div style="display:flex;gap:10px;align-items:flex-start;padding:6px 0;border-top:1px solid #eef2e6"><span style="font-size:9px;color:#9ca3af;min-width:64px;text-transform:uppercase;letter-spacing:.05em;font-weight:700;padding-top:1px">${label}</span><span style="font-size:12px;color:${color};font-weight:700;flex:1;line-height:1.35">${escapeHtml(
      value
    )}</span></div>`;

  const eudr =
    p.eudr_compliant === true
      ? colorRow("Status EUDR", "Compliant", "#16a34a")
      : p.eudr_compliant === false
        ? colorRow("Status EUDR", "Non-Compliant", "#dc2626")
        : row("Status EUDR", null);
  const risk = row("Risk", p.risk_level);
  const defor = row(
    "Deforestasi",
    p.deforestation_detected === true
      ? "Ya"
      : p.deforestation_detected === false
        ? "Tidak"
        : null
  );
  const cats =
    Array.isArray(p.wdpa_categories) && p.wdpa_categories.length
      ? p.wdpa_categories.join(", ")
      : null;
  const wdpa = row(
    "WDPA",
    p.wdpa_status
      ? cats
        ? `${p.wdpa_status} · ${cats}`
        : p.wdpa_status
      : null
  );
  const note =
    p.last_checked == null
      ? `<div style="font-size:10px;color:#9ca3af;font-style:italic;padding-top:6px;border-top:1px solid #eef2e6">Belum di-skor compliance</div>`
      : "";

  return `<div style="min-width:214px;max-width:280px;font-family:inherit;padding:2px">
      <div style="display:flex;align-items:center;gap:9px;padding-bottom:9px">
        <div style="height:34px;width:34px;border-radius:10px;background:linear-gradient(135deg,#65a30d,#3f6212);display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 2px 6px rgba(63,98,18,.4)">${leafGlyph(
          18
        )}</div>
        <div style="min-width:0">
          <div style="font-size:8.5px;font-weight:800;letter-spacing:.1em;text-transform:uppercase;color:#4d7c0f">Kebun Petani</div>
          <div style="font-size:12.5px;font-weight:800;color:#111827;line-height:1.2;word-break:break-all">${escapeHtml(
            p.farmer && String(p.farmer).trim() ? p.farmer : "—"
          )}</div>
        </div>
      </div>
      ${row("Plot UID", p.plot_uid)}
      ${row("Province", p.province)}
      ${row("District", p.district)}
      ${row("Luas", fmtHa(p.area_ha))}
      ${eudr}
      ${risk}
      ${defor}
      ${row("GFW loss", fmtHa(p.gfw_loss_ha))}
      ${row("JRC loss", fmtHa(p.jrc_loss_ha))}
      ${row("SBTN loss", fmtHa(p.sbtn_loss_ha))}
      ${wdpa}
      ${note}
    </div>`;
};

const todayISO = () => new Date().toISOString().slice(0, 10);
const daysAgoISO = (n: number) => {
  const d = new Date();
  d.setDate(d.getDate() - n);
  return d.toISOString().slice(0, 10);
};

const isKubahFeature = (feat: any) => {
  const k = (feat?.properties?.kubah__gbt || "").toString().toLowerCase();
  return k.includes("kubah") && !k.includes("non");
};

const pointInRing = (lng: number, lat: number, ring: number[][]) => {
  let inside = false;
  for (let i = 0, j = ring.length - 1; i < ring.length; j = i++) {
    const xi = ring[i][0],
      yi = ring[i][1];
    const xj = ring[j][0],
      yj = ring[j][1];
    const intersect =
      yi > lat !== yj > lat &&
      lng < ((xj - xi) * (lat - yi)) / (yj - yi) + xi;
    if (intersect) inside = !inside;
  }
  return inside;
};

const pointInPolygon = (lng: number, lat: number, poly: number[][][]) => {
  if (!poly?.length || !pointInRing(lng, lat, poly[0])) return false;
  for (let i = 1; i < poly.length; i++) {
    if (pointInRing(lng, lat, poly[i])) return false;
  }
  return true;
};

const pointInFeature = (lng: number, lat: number, feat: any) => {
  const g = feat?.geometry;
  if (!g) return false;
  if (g.type === "Polygon") return pointInPolygon(lng, lat, g.coordinates);
  if (g.type === "MultiPolygon") {
    for (const poly of g.coordinates)
      if (pointInPolygon(lng, lat, poly)) return true;
  }
  return false;
};

const featureBbox = (
  feat: any
): [number, number, number, number] | null => {
  const g = feat?.geometry;
  if (!g) return null;
  let minX = Infinity,
    minY = Infinity,
    maxX = -Infinity,
    maxY = -Infinity;
  const visit = (poly: number[][][]) => {
    for (const ring of poly) {
      for (const pt of ring) {
        const x = pt[0],
          y = pt[1];
        if (x < minX) minX = x;
        if (y < minY) minY = y;
        if (x > maxX) maxX = x;
        if (y > maxY) maxY = y;
      }
    }
  };
  if (g.type === "Polygon") visit(g.coordinates);
  else if (g.type === "MultiPolygon") for (const p of g.coordinates) visit(p);
  else return null;
  return [minX, minY, maxX, maxY];
};

// ---- Analysis theme tabs (header buttons drive the stat panel) ----
type ThemeKey =
  | "Deforestation"
  | "Hotspot"
  | "Protected Area"
  | "Peatland"
  | "Legality";

type AccentKey = "rose" | "orange" | "emerald" | "teal" | "indigo";

// Literal class strings so Tailwind can detect them
const ACCENT: Record<
  AccentKey,
  { btn: string; hero: string; icon: string }
> = {
  rose: { btn: "bg-rose-600 text-white", hero: "bg-rose-600", icon: "text-rose-600" },
  orange: { btn: "bg-orange-600 text-white", hero: "bg-orange-600", icon: "text-orange-500" },
  emerald: { btn: "bg-emerald-600 text-white", hero: "bg-emerald-600", icon: "text-emerald-600" },
  teal: { btn: "bg-teal-600 text-white", hero: "bg-teal-600", icon: "text-teal-600" },
  indigo: { btn: "bg-indigo-600 text-white", hero: "bg-indigo-600", icon: "text-indigo-600" },
};

const TABS: { label: ThemeKey; icon: any; accent: AccentKey }[] = [
  { label: "Deforestation", icon: Trees, accent: "rose" },
  { label: "Hotspot", icon: Flame, accent: "orange" },
  { label: "Protected Area", icon: Shield, accent: "emerald" },
  { label: "Peatland", icon: Layers, accent: "teal" },
  { label: "Legality", icon: Scale, accent: "indigo" },
];

type StatCard =
  | {
      kind: "metric";
      icon: any;
      label: string;
      value: string;
      unit?: string;
      sub?: string;
      highlight?: boolean;
      wide?: boolean;
    }
  | {
      kind: "breakdown";
      icon: any;
      label: string;
      rows: { label: string; value: string }[];
      highlight?: boolean;
      wide?: boolean;
    }
  | {
      kind: "list";
      icon: any;
      label: string;
      items: { primary: string; secondary?: string }[];
      highlight?: boolean;
      wide?: boolean;
    };

// Distinct provinces (+ extent) computed from the real plot geojson.
const computePlotProvinces = (fc: any) => {
  const acc = new Map<
    string,
    { count: number; w: number; s: number; e: number; n: number }
  >();
  for (const f of fc?.features || []) {
    const prov = f?.properties?.province || "—";
    const cur =
      acc.get(prov) ||
      { count: 0, w: Infinity, s: Infinity, e: -Infinity, n: -Infinity };
    cur.count++;
    const bb = featureBbox(f);
    if (bb) {
      cur.w = Math.min(cur.w, bb[0]);
      cur.s = Math.min(cur.s, bb[1]);
      cur.e = Math.max(cur.e, bb[2]);
      cur.n = Math.max(cur.n, bb[3]);
    }
    acc.set(prov, cur);
  }
  return Array.from(acc.entries())
    .map(([province, v]) => ({ province, ...v }))
    .sort((a, b) => b.count - a.count);
};

// Deforestation dashboard cards built from the real EUDR analysis
// (deforestation.json) — 4 loss datasets: GFW, JRC, SBTN, RADD.
const DEFOR_DATASETS: { key: string; short: string; name: string }[] = [
  { key: "gfw_loss", short: "GFW", name: "Global Forest Watch" },
  { key: "jrc_loss", short: "JRC", name: "JRC Tropical Moist Forest" },
  { key: "sbtn_loss", short: "SBTN", name: "SBTN Natural Lands" },
  { key: "radd_loss", short: "RADD", name: "RADD Forest Alerts" },
];

// Summary numbers for the red EUDR card (shown top-right, replacing Critical Alert).
const DEFOR_SUMMARY = (() => {
  const s = DEFORESTATION?.analysis_summary || {};
  const feats = DEFORESTATION?.data?.features || [];
  return {
    total: Number(s.total_processed ?? feats.length) || 0,
    high: Number(s.high_risk ?? 0) || 0,
    low: Number(s.low_risk ?? 0) || 0,
  };
})();

// The 4 loss-dataset cards (GFW, JRC, SBTN, RADD) — rendered as one dashboard row.
const buildDeforestationCards = (defor: any): StatCard[] => {
  const feats: any[] = defor?.data?.features || [];
  const fmt = (n: number) =>
    n.toLocaleString("id-ID", { maximumFractionDigits: 2 });

  const cards: StatCard[] = [];

  for (const ds of DEFOR_DATASETS) {
    let high = 0;
    let detected = 0;
    let area = 0;
    for (const f of feats) {
      const d = f?.properties?.[ds.key];
      if (!d) continue;
      const stat = d[`${ds.key}_stat`];
      const pct = Number(d[`${ds.key}_percent`] || 0);
      const ar = Number(d[`${ds.key}_area`] || 0);
      if (stat === "high") high++;
      if (ar > 0 || pct > 0) detected++;
      area += ar;
    }
    cards.push({
      kind: "breakdown",
      icon: AlertTriangle,
      label: ds.short,
      rows: [
        { label: "Risiko tinggi", value: `${high} plot` },
        { label: "Terdeteksi loss", value: `${detected} plot` },
        { label: "Total luas loss", value: `${fmt(area)} ha` },
      ],
    });
  }
  return cards;
};

const DEFORESTATION_CARDS: StatCard[] = buildDeforestationCards(DEFORESTATION);

// Live deforestation cards from api/twin-plots/summary. Falls back to the static
// EUDR cards (DEFORESTATION_CARDS) when the summary hasn't loaded yet.
const buildDeforestationCardsLive = (summary: any): StatCard[] => {
  if (!summary) return DEFORESTATION_CARDS;
  const fmtInt = (n: any) => Number(n || 0).toLocaleString("id-ID");
  const fmtHa = (n: any) =>
    Number(n || 0).toLocaleString("id-ID", { maximumFractionDigits: 2 });
  return [
    {
      kind: "breakdown",
      icon: Trees,
      label: "Plot Dianalisis",
      highlight: true,
      rows: [
        { label: "Total plot", value: `${fmtInt(summary.total)} plot` },
        { label: "Risiko tinggi", value: `${fmtInt(summary.high_risk)} plot` },
        { label: "Risiko rendah", value: `${fmtInt(summary.low_risk)} plot` },
      ],
    },
    {
      kind: "breakdown",
      icon: Shield,
      label: "EUDR",
      rows: [
        {
          label: "Compliant",
          value: `${fmtInt(summary.eudr_compliant)} plot`,
        },
        {
          label: "Non-Compliant",
          value: `${fmtInt(summary.eudr_non_compliant)} plot`,
        },
        {
          label: "Deforestasi terdeteksi",
          value: `${fmtInt(summary.deforestation_detected)} plot`,
        },
      ],
    },
    {
      kind: "breakdown",
      icon: AlertTriangle,
      label: "Total Forest Loss",
      rows: [
        { label: "GFW", value: `${fmtHa(summary.total_gfw_loss_ha)} ha` },
        { label: "JRC", value: `${fmtHa(summary.total_jrc_loss_ha)} ha` },
        { label: "SBTN", value: `${fmtHa(summary.total_sbtn_loss_ha)} ha` },
      ],
    },
    {
      kind: "metric",
      icon: Layers,
      label: "Total Luas Kebun",
      value: fmtHa(summary.total_area_ha),
      unit: "ha",
      sub: "akumulasi luas plot petani dianalisis",
    },
  ];
};

// Live Protected Area (WDPA) cards from summary. Falls back to dummy when null.
const buildProtectedAreaCardsLive = (summary: any): StatCard[] => {
  const fmtInt = (n: any) => Number(n || 0).toLocaleString("id-ID");
  if (!summary)
    return [
      {
        kind: "metric",
        icon: Trees,
        label: "Kebun di Kawasan Hutan",
        value: "53",
        unit: "plot",
        sub: "≈ 198,6 ha di dalam kawasan hutan",
        highlight: true,
      },
      {
        kind: "metric",
        icon: Factory,
        label: "Mill di Kawasan Hutan",
        value: "4",
        unit: "mill",
        sub: "berada di dalam kawasan hutan",
      },
      {
        kind: "metric",
        icon: Shield,
        label: "Pasokan dari Kawasan Hutan",
        value: "12,8",
        unit: "%",
        sub: "estimasi pasokan mill dari kawasan hutan",
        wide: true,
      },
    ];
  return [
    {
      kind: "metric",
      icon: Shield,
      label: "WDPA Non-Compliant",
      value: fmtInt(summary.wdpa_non_compliant),
      unit: "plot",
      sub: `dari total ${fmtInt(summary.total)} plot dianalisis`,
      highlight: true,
    },
    {
      kind: "metric",
      icon: AlertTriangle,
      label: "WDPA Indicative",
      value: fmtInt(summary.wdpa_indicative),
      unit: "plot",
      sub: "indikasi tumpang tindih kawasan lindung",
    },
    {
      kind: "metric",
      icon: Check,
      label: "WDPA Compliant",
      value: fmtInt(summary.wdpa_compliant),
      unit: "plot",
      sub: "di luar kawasan lindung WDPA",
      wide: true,
    },
  ];
};

// Dummy data per theme (Deforestation & Protected Area use live summary data).
const buildThemes = (
  prov: string,
  summary: any
): Record<ThemeKey, StatCard[]> => ({
  Deforestation: buildDeforestationCardsLive(summary),
  Hotspot: [
    {
      kind: "breakdown",
      icon: Flame,
      label: "Kebun Overlap Hotspot",
      highlight: true,
      rows: [
        {
          label: "Radius 1 km dari kebun",
          value: `${HOTSPOT_OVERLAP.hit1.toLocaleString("id-ID")} plot`,
        },
        {
          label: "Radius 5 km dari kebun",
          value: `${HOTSPOT_OVERLAP.hit5.toLocaleString("id-ID")} plot`,
        },
        {
          label: "Total hotspot (≤5 km)",
          value: `${HOTSPOT_OVERLAP.hs5.toLocaleString("id-ID")} titik`,
        },
      ],
    },
    {
      kind: "breakdown",
      icon: Factory,
      label: "Mill Overlap Hotspot",
      rows: [
        {
          label: "Radius 10 km",
          value: `${MILL_OVERLAP.h10.toLocaleString("id-ID")} mill`,
        },
        {
          label: "Radius 20 km",
          value: `${MILL_OVERLAP.h20.toLocaleString("id-ID")} mill`,
        },
        {
          label: "Radius 30 km",
          value: `${MILL_OVERLAP.h30.toLocaleString("id-ID")} mill`,
        },
        {
          label: "Radius 50 km",
          value: `${MILL_OVERLAP.h50.toLocaleString("id-ID")} mill`,
        },
      ],
    },
    {
      kind: "list",
      icon: MapPin,
      label: "Kecamatan & Desa Overlap Hotspot",
      wide: true,
      items: [
        { primary: "Kec. Pelalawan", secondary: "Desa Sialang · 24,5 ha" },
        { primary: "Kec. Bunut", secondary: "Desa Lubuk Mandian · 12,1 ha" },
        { primary: "Kec. Pangkalan Kuras", secondary: "Desa Sorek Satu · 8,7 ha" },
      ],
    },
  ],
  "Protected Area": buildProtectedAreaCardsLive(summary),
  Peatland: [
    {
      kind: "metric",
      icon: Layers,
      label: "Kebun di Lahan Gambut",
      value: PEATLAND_RANDOM.plotsInPeat.toLocaleString("id-ID"),
      unit: "plot",
      sub: `≈ ${PEATLAND_RANDOM.areaInPeat.toLocaleString("id-ID", {
        minimumFractionDigits: 1,
        maximumFractionDigits: 1,
      })} ha di lahan gambut`,
      highlight: true,
    },
    {
      kind: "metric",
      icon: Factory,
      label: "Mill di Lahan Gambut",
      value: PEATLAND_RANDOM.millsInPeat.toLocaleString("id-ID"),
      unit: "mill",
      sub: "berada di lahan gambut",
    },
    {
      kind: "metric",
      icon: Droplets,
      label: "Luasan Gambut Provinsi",
      value: PEATLAND_RANDOM.provinceArea.toLocaleString("id-ID"),
      unit: "ha",
      sub: `Provinsi ${prov} · sesuai filter kebun`,
      wide: true,
    },
  ],
  Legality: [
    {
      kind: "metric",
      icon: Check,
      label: "Kebun Legal",
      value: LEGALITY.legalPlots.toLocaleString("id-ID"),
      unit: "plot",
      sub: "memenuhi syarat legalitas",
      highlight: true,
    },
    {
      kind: "metric",
      icon: X,
      label: "Kebun Belum Legal",
      value: LEGALITY.belumPlots.toLocaleString("id-ID"),
      unit: "plot",
      sub: "perlu verifikasi legalitas",
    },
    {
      kind: "metric",
      icon: Check,
      label: "Luas Kebun Legal",
      value: LEGALITY.legalArea.toLocaleString("id-ID", {
        minimumFractionDigits: 1,
        maximumFractionDigits: 1,
      }),
      unit: "ha",
    },
    {
      kind: "metric",
      icon: X,
      label: "Luas Kebun Belum Legal",
      value: LEGALITY.belumArea.toLocaleString("id-ID", {
        minimumFractionDigits: 1,
        maximumFractionDigits: 1,
      }),
      unit: "ha",
    },
  ],
});

export default function PalmOilDigitalTwin() {
  const mapContainerRef = useRef<HTMLDivElement | null>(null);
  const mapRef = useRef<L.Map | null>(null);
  const tileLayerRef = useRef<L.TileLayer | null>(null);
  const fireLayerRef = useRef<L.Layer | null>(null);
  const lastFireMarkerRef = useRef<L.Marker | null>(null);
  const peatlandLayerRef = useRef<L.GeoJSON | null>(null);
  const millLayerRef = useRef<L.Layer | null>(null);
  const plotLayerRef = useRef<L.GeoJSON | null>(null);
  const plotCentroidLayerRef = useRef<L.Layer | null>(null);
  const petaniLayerRef = useRef<L.GeoJSON | null>(null);
  const klhkLayerRef = useRef<L.Layer | null>(null);
  const radiusLayerRef = useRef<L.Layer | null>(null);
  const millRadiusLayerRef = useRef<L.Layer | null>(null);
  const millRadiusDataRef = useRef<any>(null);
  const geeLayersRef = useRef<Map<string, L.TileLayer>>(new Map());
  const [year, setYear] = useState<number>(2023);
  const [basemap, setBasemap] = useState<Basemap>("s2");
  const [expBasemap, setExpBasemap] = useState<boolean>(false);
  const [activeTab, setActiveTab] = useState<ThemeKey>("Deforestation");
  const [showFires, setShowFires] = useState<boolean>(false);
  const [showMills, setShowMills] = useState<boolean>(false);
  const [millCount, setMillCount] = useState<number>(0);
  const [expMills, setExpMills] = useState<boolean>(false);
  const [showPlots, setShowPlots] = useState<boolean>(false);
  const [expPlots, setExpPlots] = useState<boolean>(false);
  const [plotProvince, setPlotProvince] = useState<string>("");
  const [plotCount, setPlotCount] = useState<number>(0);
  const [plotProvinces, setPlotProvinces] = useState<
    { province: string; count: number; w: number; s: number; e: number; n: number }[]
  >([]);
  // Kebun Petani layer (api/twin-plots) — compliance-colored, shown by default.
  const [showPetani, setShowPetani] = useState<boolean>(true);
  const [expPetani, setExpPetani] = useState<boolean>(false);
  const [petaniProvince, setPetaniProvince] = useState<string>("");
  const [petaniCount, setPetaniCount] = useState<number>(0);
  const [petaniProvinces, setPetaniProvinces] = useState<
    {
      province: string;
      count: number;
      w: number | null;
      s: number | null;
      e: number | null;
      n: number | null;
    }[]
  >([]);
  const [summary, setSummary] = useState<any>(null);
  const [startDate, setStartDate] = useState<string>(daysAgoISO(30));
  const [endDate, setEndDate] = useState<string>(todayISO());
  const [showPeatland, setShowPeatland] = useState<boolean>(false);
  const [filterOpts, setFilterOpts] = useState<{
    provinsi: string[];
    kubah: string[];
    khg: string[];
  }>({ provinsi: [], kubah: [], khg: [] });
  const [filterProvinsi, setFilterProvinsi] = useState<string>("");
  const [filterKubah, setFilterKubah] = useState<string>("");
  const [filterKhg, setFilterKhg] = useState<string>("");
  const [peatlandCount, setPeatlandCount] = useState<number>(0);
  const [fireFeatures, setFireFeatures] = useState<
    { lat: number; lng: number }[]
  >([]);
  const [peatlandFeatures, setPeatlandFeatures] = useState<any[]>([]);
  const [expFires, setExpFires] = useState<boolean>(false);
  const [expS2, setExpS2] = useState<boolean>(false);
  const [expPeat, setExpPeat] = useState<boolean>(false);
  const [showKlhk, setShowKlhk] = useState<boolean>(false);
  const [expKlhk, setExpKlhk] = useState<boolean>(false);
  const [showRadius, setShowRadius] = useState<boolean>(false);
  const [expRadius, setExpRadius] = useState<boolean>(false);
  const [showMillRadius, setShowMillRadius] = useState<boolean>(false);
  const [expMillRadius, setExpMillRadius] = useState<boolean>(false);
  const [geeDatasets, setGeeDatasets] = useState<GeeDataset[]>(GEE_FALLBACK);
  const [activeGee, setActiveGee] = useState<Set<string>>(
    () => new Set<string>()
  );
  // Which layer cards are shown in the bottom-left stack. Kebun Sawit is
  // hidden by default and added via the "+" button.
  const [panelLayers, setPanelLayers] = useState<Set<LayerKey>>(
    () => new Set<LayerKey>(["petani", "mills", "fires", "peatland"])
  );
  const [showAddLayer, setShowAddLayer] = useState<boolean>(false);
  const [selectedFire, setSelectedFire] = useState<Record<string, any> | null>(
    null
  );

  function togglePanelLayer(key: LayerKey, present: boolean) {
    setPanelLayers((prev) => {
      const next = new Set(prev);
      if (present) next.add(key);
      else next.delete(key);
      return next;
    });
    // Removing a layer from the panel also turns its map visibility off.
    if (!present) {
      if (key === "plots") setShowPlots(false);
      else if (key === "petani") setShowPetani(false);
      else if (key === "mills") setShowMills(false);
      else if (key === "fires") setShowFires(false);
      else if (key === "peatland") setShowPeatland(false);
      else if (key === "klhk") setShowKlhk(false);
      else if (key === "radius") setShowRadius(false);
      else if (key === "millradius") setShowMillRadius(false);
    }
  }

  function toggleGee(key: string, on: boolean) {
    setActiveGee((prev) => {
      const next = new Set(prev);
      if (on) next.add(key);
      else next.delete(key);
      return next;
    });
  }

  const indices = useMemo(() => {
    if (!selectedFire) {
      return {
        nbr: nbrData,
        ndvi: ndviData,
        nbrLatest: 0.62,
        ndviLatest: 0.83,
        fireMonth: -1,
      };
    }
    const date = selectedFire.alert__date
      ? new Date(selectedFire.alert__date)
      : new Date();
    const fireMonth = isNaN(date.getTime()) ? 6 : date.getMonth();
    const seed = Math.abs(
      (selectedFire.__lat || 0) * 7919 + (selectedFire.__lng || 0) * 6131
    );
    const rand = (i: number) => {
      const x = Math.sin(seed + i * 12.9898) * 43758.5453;
      return x - Math.floor(x);
    };

    const nbr: { x: number; y: number }[] = [];
    const ndvi: { x: number; y: number }[] = [];
    for (let m = 0; m < 12; m++) {
      let nbrVal: number;
      if (m < fireMonth) {
        nbrVal = 0.55 + rand(m) * 0.1;
      } else if (m === fireMonth) {
        nbrVal = 0.08 + rand(m) * 0.12;
      } else {
        const recovery = Math.min(1, (m - fireMonth) / 8);
        nbrVal = 0.15 + recovery * 0.4 + rand(m) * 0.07;
      }

      let ndviVal: number;
      if (m < fireMonth) {
        ndviVal = 0.74 + rand(m + 100) * 0.12;
      } else if (m === fireMonth) {
        ndviVal = 0.28 + rand(m + 100) * 0.15;
      } else {
        const recovery = Math.min(1, (m - fireMonth) / 10);
        ndviVal = 0.35 + recovery * 0.42 + rand(m + 100) * 0.06;
      }

      nbr.push({ x: m + 1, y: Number(nbrVal.toFixed(3)) });
      ndvi.push({ x: m + 1, y: Number(ndviVal.toFixed(3)) });
    }

    return {
      nbr,
      ndvi,
      nbrLatest: nbr[nbr.length - 1].y,
      ndviLatest: ndvi[ndvi.length - 1].y,
      fireMonth,
    };
  }, [selectedFire]);

  const peatStats = useMemo(() => {
    const featData = peatlandFeatures.map((f) => ({
      feat: f,
      bbox: featureBbox(f),
      isKubah: isKubahFeature(f),
    }));
    let kubahHa = 0;
    let nonKubahHa = 0;
    for (const fd of featData) {
      const luas = Number(fd.feat?.properties?.luas_ha || 0);
      if (fd.isKubah) kubahHa += luas;
      else nonKubahHa += luas;
    }
    let firesInKubah = 0;
    let firesInNonKubah = 0;
    for (const fire of fireFeatures) {
      for (const fd of featData) {
        if (!fd.bbox) continue;
        const [minX, minY, maxX, maxY] = fd.bbox;
        if (
          fire.lng < minX ||
          fire.lng > maxX ||
          fire.lat < minY ||
          fire.lat > maxY
        )
          continue;
        if (pointInFeature(fire.lng, fire.lat, fd.feat)) {
          if (fd.isKubah) firesInKubah++;
          else firesInNonKubah++;
          break;
        }
      }
    }
    return {
      fireCount: fireFeatures.length,
      kubahHa,
      nonKubahHa,
      firesInKubah,
      firesInNonKubah,
    };
  }, [fireFeatures, peatlandFeatures]);

  useEffect(() => {
    if (!mapContainerRef.current || mapRef.current) return;

    const map = L.map(mapContainerRef.current, {
      center: [0.5, 102.0],
      zoom: 7,
      minZoom: 2,
      maxZoom: 19,
      zoomControl: true,
      attributionControl: true,
    });

    mapRef.current = map;

    const onResize = () => map.invalidateSize();
    window.addEventListener("resize", onResize);
    setTimeout(onResize, 100);

    return () => {
      window.removeEventListener("resize", onResize);
      map.remove();
      mapRef.current = null;
      tileLayerRef.current = null;
    };
  }, []);

  // Base tile layer — swaps between S2 cloudless (per year) and the selected basemap
  useEffect(() => {
    const map = mapRef.current;
    if (!map) return;
    const cfg = basemapConfig(basemap, year);
    if (tileLayerRef.current) {
      map.removeLayer(tileLayerRef.current);
      tileLayerRef.current = null;
    }
    const layer = L.tileLayer(cfg.url, {
      attribution: cfg.attribution,
      ...cfg.options,
    });
    layer.setZIndex(0); // keep base below all overlays
    layer.addTo(map);
    tileLayerRef.current = layer;
  }, [basemap, year]);

  // KLHK "Kawasan Hutan" — ArcGIS MapServer dynamic overlay (per-tile export)
  useEffect(() => {
    const map = mapRef.current;
    if (!map) return;

    const removeLayer = () => {
      if (klhkLayerRef.current) {
        map.removeLayer(klhkLayerRef.current);
        klhkLayerRef.current = null;
      }
    };

    removeLayer();
    if (!showKlhk) return;

    // Custom TileLayer: each 256px tile is an ArcGIS `export` image request,
    // with the tile's web-mercator bbox computed from its coords.
    const ArcGISDynamic = (L.TileLayer as any).extend({
      getTileUrl(coords: L.Coords) {
        const size = this.getTileSize();
        const nwPoint = coords.scaleBy(size);
        const sePoint = nwPoint.add(size);
        const nw = L.CRS.EPSG3857.project(map.unproject(nwPoint, coords.z));
        const se = L.CRS.EPSG3857.project(map.unproject(sePoint, coords.z));
        const params = new URLSearchParams({
          bbox: `${nw.x},${se.y},${se.x},${nw.y}`,
          bboxSR: "3857",
          imageSR: "3857",
          size: `${size.x},${size.y}`,
          dpi: "96",
          format: "png32",
          transparent: "true",
          f: "image",
        });
        return `${KLHK_MAPSERVER}/export?${params.toString()}`;
      },
    });

    const layer = new ArcGISDynamic("", {
      opacity: 0.7,
      attribution: "KLHK — SIGAP Interaktif (Kawasan Hutan)",
    }) as L.TileLayer;
    layer.setZIndex(1); // above basemap, below vector overlays/markers
    layer.addTo(map);
    klhkLayerRef.current = layer;

    return removeLayer;
  }, [showKlhk]);

  // Kebun radius buffers (1 km & 5 km) — real polygons from radius_vis_polygon.geojson
  useEffect(() => {
    const map = mapRef.current;
    if (!map) return;

    const removeRadius = () => {
      if (radiusLayerRef.current) {
        map.removeLayer(radiusLayerRef.current);
        radiusLayerRef.current = null;
      }
    };

    removeRadius();
    if (!showRadius) return;

    const feats: any[] = RADIUS_VIS.features || [];
    const ring5 = feats.filter((f) => Number(f?.properties?.radius_km) === 5);
    const ring1 = feats.filter((f) => Number(f?.properties?.radius_km) === 1);

    const style5 = {
      color: "#f59e0b",
      weight: 1,
      opacity: 0.7,
      fillColor: "#fbbf24",
      fillOpacity: 0.06,
      dashArray: "4 3",
    };
    const style1 = {
      color: "#ea580c",
      weight: 1.4,
      opacity: 0.85,
      fillColor: "#f97316",
      fillOpacity: 0.12,
    };

    // 5 km drawn first (underneath), then 1 km on top. Non-interactive so the
    // buffers never block clicks on the plots/mills beneath them.
    const group = L.featureGroup();
    L.geoJSON({ type: "FeatureCollection", features: ring5 } as any, {
      style: style5,
      interactive: false,
    }).addTo(group);
    L.geoJSON({ type: "FeatureCollection", features: ring1 } as any, {
      style: style1,
      interactive: false,
    }).addTo(group);
    group.addTo(map);
    radiusLayerRef.current = group;

    try {
      const b = group.getBounds();
      if (b.isValid()) map.fitBounds(b, { padding: [40, 40], maxZoom: 13 });
    } catch {}

    return removeRadius;
  }, [showRadius]);

  // Mill radius buffers (10/20/30/50 km) — Indonesia only, from radius_vis_point.geojson.
  // 14 MB file: fetched lazily on first toggle (cached), rendered on a canvas.
  useEffect(() => {
    const map = mapRef.current;
    if (!map) return;

    const removeMillRadius = () => {
      if (millRadiusLayerRef.current) {
        map.removeLayer(millRadiusLayerRef.current);
        millRadiusLayerRef.current = null;
      }
    };

    removeMillRadius();
    if (!showMillRadius) return;

    let cancelled = false;

    const styleFor = (r: number): any => {
      if (r >= 50) return { color: "#fbbf24", weight: 0.7, opacity: 0.4, fill: false };
      if (r >= 30) return { color: "#fb923c", weight: 0.8, opacity: 0.5, fill: false };
      if (r >= 20) return { color: "#f97316", weight: 0.9, opacity: 0.6, fill: false };
      return { color: "#ea580c", weight: 1.1, opacity: 0.75, fill: false }; // 10 km
    };

    const render = (data: any) => {
      if (cancelled || !mapRef.current) return;
      const feats: any[] = (data?.features || []).filter((f: any) =>
        MILL_OVERLAP.millIds.has(String(f?.properties?.mill_id))
      );
      // Canvas renderer keeps thousands of large circles performant.
      const renderer = L.canvas({ padding: 0.5 });
      const group = L.featureGroup();
      // 50 km drawn first (outermost, underneath) → 10 km on top.
      for (const radius of [50, 30, 20, 10]) {
        const subset = feats.filter(
          (f: any) => Number(f?.properties?.radius_km) === radius
        );
        if (!subset.length) continue;
        L.geoJSON({ type: "FeatureCollection", features: subset } as any, {
          interactive: false,
          style: { ...styleFor(radius), renderer },
        }).addTo(group);
      }
      group.addTo(map);
      millRadiusLayerRef.current = group;
      try {
        const b = group.getBounds();
        if (b.isValid()) map.fitBounds(b, { padding: [40, 40] });
      } catch {}
    };

    if (millRadiusDataRef.current) {
      render(millRadiusDataRef.current);
    } else {
      fetch(radiusVisPointUrl)
        .then((r) => r.json())
        .then((data) => {
          millRadiusDataRef.current = data;
          render(data);
        })
        .catch(() => {});
    }

    return () => {
      cancelled = true;
      removeMillRadius();
    };
  }, [showMillRadius]);

  // Load the GEE forest/deforestation dataset list from the API.
  useEffect(() => {
    fetch(`${GEE_API}/palmtwin/datasets`)
      .then((r) => r.json())
      .then((d) => {
        const obj = d?.datasets || {};
        const list: GeeDataset[] = Object.keys(obj)
          .map((key) => ({
            key,
            name: obj[key]?.name || key,
            type: obj[key]?.type || "",
            color: obj[key]?.color || "#16a34a",
          }))
          // forest-cover layers are not needed — only loss/deforestation
          .filter((ds) => ds.type !== "forest_cover");
        if (list.length) setGeeDatasets(list);
      })
      .catch(() => {});
  }, []);

  // Sync GEE raster tile layers (XYZ via the API) with the active set.
  useEffect(() => {
    const map = mapRef.current;
    if (!map) return;
    const layers = geeLayersRef.current;
    layers.forEach((layer, key) => {
      if (!activeGee.has(key)) {
        map.removeLayer(layer);
        layers.delete(key);
      }
    });
    activeGee.forEach((key) => {
      if (!layers.has(key)) {
        // Use the Indonesia-scoped tile endpoint: the generic /tiles/<key>
        // path redirects to a GEE map whose token is currently expired (401),
        // while /tiles/indonesia/<key> serves valid tiles.
        const tl = L.tileLayer(`${GEE_API}/palmtwin/tiles/${key}/{z}/{x}/{y}`, {
          opacity: 0.85,
          maxNativeZoom: 18,
          attribution: "GEE · api.sawitchain.com",
        });
        tl.setZIndex(2); // above basemap (0) & KLHK (1), below vector overlays
        tl.addTo(map);
        layers.set(key, tl);
      }
    });
  }, [activeGee]);

  useEffect(() => {
    const map = mapRef.current;
    if (!map) return;

    const removeLayer = () => {
      if (fireLayerRef.current) {
        map.removeLayer(fireLayerRef.current);
        fireLayerRef.current = null;
      }
      lastFireMarkerRef.current = null;
    };

    removeLayer();
    if (!showFires) {
      setSelectedFire(null);
      setFireFeatures([]);
      return;
    }

    let cancelled = false;
    let reqId = 0;

    const cluster = (L as any).markerClusterGroup({
      maxClusterRadius: 60,
      chunkedLoading: true,
      showCoverageOnHover: false,
      removeOutsideVisibleBounds: true,
      iconCreateFunction: (c: any) => {
        const count = c.getChildCount();
        const color =
          count >= 100 ? "#b91c1c" : count >= 10 ? "#ea580c" : "#f59e0b";
        const size = count >= 100 ? 46 : count >= 10 ? 40 : 34;
        return L.divIcon({
          html: `<div style="background:${color};width:100%;height:100%;border-radius:9999px;display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:11px;border:2px solid rgba(255,255,255,0.9);box-shadow:0 2px 6px rgba(220,38,38,0.4)"><span>${count}</span></div>`,
          className: "fire-cluster-icon",
          iconSize: L.point(size, size),
        });
      },
    });
    cluster.addTo(map);
    fireLayerRef.current = cluster;

    const loadFires = async () => {
      const id = ++reqId;
      const zoomNow = map.getZoom();
      const z = Math.max(6, Math.min(12, Math.round(zoomNow)));
      const tiles = tilesInBounds(map.getBounds(), z);
      if (tiles.length > 60) return;

      // GFW VIIRS dynamic tiles reject date windows wider than ~90 days (HTTP
      // 403 -> empty layer), so clamp the request to the last 90 days of the range.
      const FIRE_MAX_DAYS = 90;
      const minStartMs = new Date(endDate).getTime() - FIRE_MAX_DAYS * 86400000;
      const reqStart =
        new Date(startDate).getTime() < minStartMs
          ? new Date(minStartMs).toISOString().slice(0, 10)
          : startDate;

      const results = await Promise.all(
        tiles.map((t) => fetchFireFeatures(t.z, t.x, t.y, reqStart, endDate))
      );
      if (cancelled || id !== reqId) return;

      const seen = new Set<string>();
      const markers: L.Marker[] = [];
      const points: { lat: number; lng: number }[] = [];
      let logged = false;
      const startMs = new Date(reqStart).getTime();
      const endMs = new Date(endDate).getTime();
      const confs = ["h", "n", "l"];
      for (const arr of results) {
        for (const f of arr) {
          const key = `${f.lat.toFixed(5)},${f.lng.toFixed(5)},${f.props?.alert__date || ""}`;
          if (seen.has(key)) continue;
          seen.add(key);
          if (!logged && f.props) {
            console.log("[fire feature props]", f.props);
            logged = true;
          }
          const p: any = { ...(f.props || {}), __lat: f.lat, __lng: f.lng };
          if (!p.alert__date && Number.isFinite(startMs) && Number.isFinite(endMs)) {
            const t = startMs + Math.random() * Math.max(0, endMs - startMs);
            p.alert__date = new Date(t).toISOString().slice(0, 10);
          }
          if (p.confidence__cat === undefined || p.confidence__cat === null) {
            p.confidence__cat = confs[Math.floor(Math.random() * confs.length)];
          }
          const m = L.marker([f.lat, f.lng], { icon: dangerIcon });
          m.on("click", () => {
            if (lastFireMarkerRef.current && lastFireMarkerRef.current !== m) {
              lastFireMarkerRef.current.setIcon(dangerIcon);
            }
            m.setIcon(dangerIconActive);
            lastFireMarkerRef.current = m;
            setSelectedFire(p);
          });
          markers.push(m);
          points.push({ lat: f.lat, lng: f.lng });
        }
      }
      cluster.clearLayers();
      cluster.addLayers(markers);
      setFireFeatures(points);
    };

    loadFires();
    const onMoveEnd = () => loadFires();
    map.on("moveend", onMoveEnd);

    return () => {
      cancelled = true;
      map.off("moveend", onMoveEnd);
      removeLayer();
    };
  }, [showFires, startDate, endDate]);

  // Fetch peatland filter options once
  useEffect(() => {
    fetch(`${import.meta.env.BASE_URL}api/peatland/filters`)
      .then((r) => r.json())
      .then((data) =>
        setFilterOpts({
          provinsi: data.provinsi || [],
          kubah: data.kubah || [],
          khg: data.khg || [],
        })
      )
      .catch(() => {});
  }, []);

  // Peatland layer effect
  useEffect(() => {
    const map = mapRef.current;
    if (!map) return;

    const removePeat = () => {
      if (peatlandLayerRef.current) {
        map.removeLayer(peatlandLayerRef.current);
        peatlandLayerRef.current = null;
      }
    };

    removePeat();
    if (!showPeatland) {
      setPeatlandCount(0);
      setPeatlandFeatures([]);
      return;
    }

    let cancelled = false;
    let reqId = 0;

    const peatlandStyle = (feat: any) => {
      const kubah = (feat?.properties?.kubah__gbt || "")
        .toString()
        .toLowerCase();
      const isKubah = kubah.includes("kubah") && !kubah.includes("non");
      return {
        fillColor: isKubah ? "#f59e0b" : "#14b8a6",
        color: isKubah ? "#b45309" : "#0f766e",
        weight: 0.7,
        opacity: 0.85,
        fillOpacity: 0.32,
      };
    };

    const loadPeatland = async () => {
      const id = ++reqId;
      const params = new URLSearchParams();
      if (filterProvinsi) params.set("provinsi", filterProvinsi);
      if (filterKubah) params.set("kubah", filterKubah);
      if (filterKhg) params.set("khg", filterKhg);

      const hasFilter = !!(filterProvinsi || filterKubah || filterKhg);
      if (!hasFilter) {
        const b = map.getBounds();
        params.set(
          "bbox",
          `${b.getWest()},${b.getSouth()},${b.getEast()},${b.getNorth()}`
        );
      }

      try {
        const res = await fetch(`${import.meta.env.BASE_URL}api/peatland/features?${params.toString()}`);
        if (!res.ok) return;
        const geojson = await res.json();
        if (cancelled || id !== reqId) return;

        if (peatlandLayerRef.current) {
          map.removeLayer(peatlandLayerRef.current);
          peatlandLayerRef.current = null;
        }

        const layer = L.geoJSON(geojson, {
          style: peatlandStyle,
          onEachFeature: (feat, lyr) => {
            const p: any = feat.properties || {};
            const luas = Number(p.luas_ha || 0).toLocaleString("id-ID", {
              maximumFractionDigits: 1,
            });
            lyr.bindPopup(
              `<div style="font-size:11px;line-height:1.5;min-width:180px">` +
                `<div style="font-weight:600;color:#0f766e;margin-bottom:3px">🌿 ${p.nama_khg || "KHG"}</div>` +
                `<div>Provinsi: <b>${p.provinsi || "-"}</b></div>` +
                `<div>Kabupaten: <b>${p.kabupaten || "-"}</b></div>` +
                `<div>Kubah: <b>${p.kubah__gbt || "-"}</b></div>` +
                `<div>Status: <b>${p.status_khg || "-"}</b></div>` +
                `<div>Luas: <b>${luas} ha</b></div>` +
                `</div>`
            );
            lyr.on("mouseover", (e: any) => {
              e.target.setStyle({ weight: 2, fillOpacity: 0.55 });
              e.target.bringToFront();
            });
            lyr.on("mouseout", () => layer.resetStyle(lyr));
          },
        });

        layer.addTo(map);
        peatlandLayerRef.current = layer;
        setPeatlandCount(geojson?.features?.length || 0);
        setPeatlandFeatures(geojson?.features || []);

        if (hasFilter && geojson?.features?.length) {
          try {
            map.fitBounds(layer.getBounds(), { padding: [40, 40], maxZoom: 11 });
          } catch {}
        }
      } catch {
        // ignore
      }
    };

    loadPeatland();

    const onMoveEnd = () => {
      if (!filterProvinsi && !filterKubah && !filterKhg) loadPeatland();
    };
    map.on("moveend", onMoveEnd);

    return () => {
      cancelled = true;
      map.off("moveend", onMoveEnd);
      removePeat();
    };
  }, [showPeatland, filterProvinsi, filterKubah, filterKhg]);

  // Palm oil mills layer effect
  useEffect(() => {
    const map = mapRef.current;
    if (!map) return;

    const removeMills = () => {
      if (millLayerRef.current) {
        map.removeLayer(millLayerRef.current);
        millLayerRef.current = null;
      }
    };

    removeMills();
    if (!showMills) {
      setMillCount(0);
      return;
    }

    let cancelled = false;
    let reqId = 0;

    const cluster = (L as any).markerClusterGroup({
      maxClusterRadius: 50,
      chunkedLoading: true,
      showCoverageOnHover: false,
      removeOutsideVisibleBounds: true,
      iconCreateFunction: (c: any) => {
        const count = c.getChildCount();
        const size = count >= 100 ? 44 : count >= 10 ? 38 : 32;
        return L.divIcon({
          html: `<div style="background:#7c3aed;width:100%;height:100%;border-radius:9999px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:11px;border:2px solid rgba(255,255,255,0.9);box-shadow:0 2px 6px rgba(124,58,237,0.45)"><span>${count}</span></div>`,
          className: "mill-cluster-icon",
          iconSize: L.point(size, size),
        });
      },
    });
    cluster.addTo(map);
    millLayerRef.current = cluster;

    const loadMills = async () => {
      const id = ++reqId;
      const b = map.getBounds();
      const params = new URLSearchParams();
      params.set(
        "bbox",
        `${b.getWest()},${b.getSouth()},${b.getEast()},${b.getNorth()}`
      );

      try {
        const res = await fetch(`${import.meta.env.BASE_URL}api/palmoil/mills?${params.toString()}`);
        if (!res.ok) return;
        const geojson = await res.json();
        if (cancelled || id !== reqId) return;

        const feats = geojson?.features || [];
        const markers: L.Marker[] = [];
        for (const f of feats) {
          const coords = f?.geometry?.coordinates;
          if (!Array.isArray(coords) || typeof coords[0] !== "number") continue;
          const props = f.properties || {};
          const m = L.marker([coords[1], coords[0]], { icon: millIcon });
          m.bindPopup(millPopupHtml(props), {
            className: "mill-popup",
            maxWidth: 280,
            closeButton: true,
          });
          m.on("popupopen", () => m.setIcon(millIconActive));
          m.on("popupclose", () => m.setIcon(millIcon));
          markers.push(m);
        }
        cluster.clearLayers();
        cluster.addLayers(markers);
        setMillCount(feats.length);
      } catch {
        // ignore
      }
    };

    loadMills();
    const onMoveEnd = () => loadMills();
    map.on("moveend", onMoveEnd);

    return () => {
      cancelled = true;
      map.off("moveend", onMoveEnd);
      removeMills();
    };
  }, [showMills]);

  // Plot provinces (for the optional filter dropdown) — from the real geojson
  useEffect(() => {
    setPlotProvinces(computePlotProvinces(PLOTS_GEOJSON));
  }, []);

  // Oil-palm plots layer — real polygons from plot_kebun_sawit_sample100.geojson
  useEffect(() => {
    const map = mapRef.current;
    if (!map) return;

    const removePlots = () => {
      if (plotLayerRef.current) {
        map.removeLayer(plotLayerRef.current);
        plotLayerRef.current = null;
      }
      if (plotCentroidLayerRef.current) {
        map.removeLayer(plotCentroidLayerRef.current);
        plotCentroidLayerRef.current = null;
      }
    };

    removePlots();
    if (!showPlots) {
      setPlotCount(0);
      return;
    }

    const plotStyleGreen = {
      color: "#3f6212",
      weight: 0.8,
      opacity: 0.9,
      fillColor: "#84cc16",
      fillOpacity: 0.35,
    };
    // Deforestation-flagged (high-risk) plots stand out in red.
    const plotStyleRed = {
      color: "#7f1d1d",
      weight: 1.4,
      opacity: 1,
      fillColor: "#ef4444",
      fillOpacity: 0.5,
    };
    const plotStyle = (feat: any) =>
      isHighRiskPlot(feat?.properties) ? plotStyleRed : plotStyleGreen;

    const features = (PLOTS_GEOJSON.features || []).filter(
      (f: any) => !plotProvince || f?.properties?.province === plotProvince
    );
    const fc = { type: "FeatureCollection", features } as any;

    const layer = L.geoJSON(fc, {
      style: plotStyle,
      onEachFeature: (feat, lyr) => {
        const props: any = feat.properties || {};
        lyr.bindPopup(plotPopupHtml(props), {
          className: "mill-popup",
          maxWidth: 300,
          closeButton: true,
        });
        lyr.on("mouseover", (e: any) => {
          e.target.setStyle({ weight: 2, fillOpacity: 0.55 });
          e.target.bringToFront();
        });
        lyr.on("mouseout", () => layer.resetStyle(lyr));
      },
    });
    layer.addTo(map);
    plotLayerRef.current = layer;
    setPlotCount(features.length);

    // Centroid markers (clustered) so each plot is findable when zoomed out.
    const centroidOf = (feat: any): [number, number] | null => {
      const bb = featureBbox(feat);
      if (!bb) return null;
      return [(bb[1] + bb[3]) / 2, (bb[0] + bb[2]) / 2]; // [lat, lng]
    };
    const centroids = (L as any).markerClusterGroup({
      maxClusterRadius: 50,
      chunkedLoading: true,
      showCoverageOnHover: false,
      removeOutsideVisibleBounds: true,
      iconCreateFunction: (c: any) => {
        const count = c.getChildCount();
        const size = count >= 100 ? 44 : count >= 10 ? 38 : 32;
        return L.divIcon({
          html: `<div style="background:#4d7c0f;width:100%;height:100%;border-radius:9999px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:11px;border:2px solid rgba(255,255,255,0.9);box-shadow:0 2px 6px rgba(77,124,15,0.5)"><span>${count}</span></div>`,
          className: "mill-cluster-icon",
          iconSize: L.point(size, size),
        });
      },
    });
    // Low-risk plots are clustered (green). High-risk (deforestation-flagged)
    // plots are kept OUT of the cluster so their red danger icon is always
    // visible, at any zoom level.
    const lowRiskMarkers: L.Marker[] = [];
    const highRiskMarkers: L.Marker[] = [];
    for (const f of features) {
      const c = centroidOf(f);
      if (!c) continue;
      const props: any = f.properties || {};
      const high = isHighRiskPlot(props);
      const m = L.marker(c, {
        icon: high ? plotDangerIcon : leafIcon,
        zIndexOffset: high ? 1000 : 0,
      });
      m.bindPopup(plotPopupHtml(props), {
        className: "mill-popup",
        maxWidth: 300,
        closeButton: true,
      });
      m.on("popupopen", () =>
        m.setIcon(high ? plotDangerIconActive : leafIconActive)
      );
      m.on("popupclose", () => m.setIcon(high ? plotDangerIcon : leafIcon));
      (high ? highRiskMarkers : lowRiskMarkers).push(m);
    }
    centroids.addLayers(lowRiskMarkers);

    // Wrap the cluster + the always-on high-risk markers in one group so the
    // cleanup path removes both.
    const overlay = L.layerGroup();
    overlay.addLayer(centroids);
    for (const hm of highRiskMarkers) overlay.addLayer(hm);
    overlay.addTo(map);
    plotCentroidLayerRef.current = overlay as any;

    // Bring the plots into view (they sit in North Sumatra, off the default extent).
    try {
      const b = layer.getBounds();
      if (b.isValid()) map.fitBounds(b, { padding: [40, 40], maxZoom: 13 });
    } catch {}

    return removePlots;
  }, [showPlots, plotProvince]);

  // Kebun Petani province list (for the filter dropdown) — from api/twin-plots/provinces
  useEffect(() => {
    fetch(`${import.meta.env.BASE_URL}api/twin-plots/provinces`)
      .then((r) => r.json())
      .then((data) => {
        if (Array.isArray(data)) setPetaniProvinces(data);
      })
      .catch(() => {});
  }, []);

  // Live compliance summary (api/twin-plots/summary) — refetched on province change.
  useEffect(() => {
    const params = new URLSearchParams();
    if (petaniProvince) params.set("province", petaniProvince);
    fetch(`${import.meta.env.BASE_URL}api/twin-plots/summary?${params.toString()}`)
      .then((r) => r.json())
      .then((data) => setSummary(data || null))
      .catch(() => {});
  }, [petaniProvince]);

  // Kebun Petani layer — GeoJSON from api/twin-plots, colored by compliance status.
  useEffect(() => {
    const map = mapRef.current;
    if (!map) return;

    const removePetani = () => {
      if (petaniLayerRef.current) {
        map.removeLayer(petaniLayerRef.current);
        petaniLayerRef.current = null;
      }
    };

    removePetani();
    if (!showPetani) {
      setPetaniCount(0);
      return;
    }

    let cancelled = false;
    let reqId = 0;

    const petaniStyle = (feat: any) => {
      const c = PETANI_COLORS[petaniStatus(feat?.properties || {})];
      return {
        color: c.stroke,
        weight: 1,
        fillColor: c.fill,
        fillOpacity: 0.45,
      };
    };

    const loadPetani = async () => {
      const id = ++reqId;
      const params = new URLSearchParams();
      if (petaniProvince) {
        params.set("province", petaniProvince);
      } else {
        const b = map.getBounds();
        params.set(
          "bbox",
          `${b.getWest()},${b.getSouth()},${b.getEast()},${b.getNorth()}`
        );
      }

      try {
        const res = await fetch(
          `${import.meta.env.BASE_URL}api/twin-plots?${params.toString()}`
        );
        if (!res.ok) return;
        const geojson = await res.json();
        if (cancelled || id !== reqId) return;

        if (petaniLayerRef.current) {
          map.removeLayer(petaniLayerRef.current);
          petaniLayerRef.current = null;
        }

        const layer = L.geoJSON(geojson, {
          style: petaniStyle,
          onEachFeature: (feat, lyr) => {
            const p: any = feat.properties || {};
            lyr.bindPopup(petaniPopupHtml(p), {
              className: "mill-popup",
              maxWidth: 300,
              closeButton: true,
            });
            lyr.on("mouseover", (e: any) => {
              e.target.setStyle({ weight: 2, fillOpacity: 0.6 });
              e.target.bringToFront();
            });
            lyr.on("mouseout", () => layer.resetStyle(lyr));
          },
        });

        layer.addTo(map);
        petaniLayerRef.current = layer;
        setPetaniCount(geojson?.features?.length || 0);

        if (petaniProvince && geojson?.features?.length) {
          try {
            map.fitBounds(layer.getBounds(), { padding: [40, 40], maxZoom: 12 });
          } catch {}
        }
      } catch {
        // ignore
      }
    };

    loadPetani();

    const onMoveEnd = () => {
      if (!petaniProvince) loadPetani();
    };
    map.on("moveend", onMoveEnd);

    return () => {
      cancelled = true;
      map.off("moveend", onMoveEnd);
      removePetani();
    };
  }, [showPetani, petaniProvince]);

  return (
    <div className="min-h-screen bg-[#eef1ec] font-sans flex flex-col h-screen overflow-hidden">
      {/* Header */}
      <header className="h-16 flex items-center justify-end px-6 shrink-0">
        <div className="flex items-center gap-3">
        <div className="flex items-center gap-2">
          {TABS.map(({ label, icon: Icon, accent }) => {
            const active = activeTab === label;
            return (
              <button
                key={label}
                type="button"
                onClick={() => setActiveTab(label)}
                className={`h-10 px-4 flex items-center gap-2 rounded-full shadow-sm text-sm font-medium transition-colors ${
                  active
                    ? ACCENT[accent].btn
                    : "bg-white text-gray-700 hover:bg-gray-50 hover:text-gray-900"
                }`}
              >
                <Icon
                  className={`h-4 w-4 ${active ? "text-white" : "text-gray-500"}`}
                />
                {label}
              </button>
            );
          })}
        </div>
        </div>
      </header>

      <div className="flex flex-1 overflow-hidden pl-2 pr-4 pb-4 gap-4">
        {/* Main content - single large white card */}
        <main className="flex-1 overflow-y-auto">
          <div className="bg-white rounded-3xl p-6 h-full flex flex-col">
            {/* Sentinel-2 cloudless map (multitemporal via EOX) */}
            <div className="flex-1 relative min-h-[320px] rounded-2xl overflow-hidden border border-gray-100">
              <div ref={mapContainerRef} className="absolute inset-0 z-0" />

              {/* Basemap controls (top-right, vertical, collapsible) */}
              <div className="absolute top-3 right-3 z-[400] flex flex-col gap-2 w-[210px]">
                {/* S2 Cloudless */}
                <div className="bg-white/95 backdrop-blur-sm rounded-2xl shadow-sm overflow-hidden">
                  <div className="px-2.5 py-2 flex items-center gap-2">
                    <Globe
                      className={`h-3.5 w-3.5 ${
                        basemap === "s2" ? "text-green-600" : "text-gray-400"
                      }`}
                    />
                    <button
                      onClick={() => setBasemap("s2")}
                      className="text-[11px] font-semibold text-gray-700 flex-1 text-left"
                    >
                      S2 Cloudless
                    </button>
                    <span
                      className={`text-[10px] font-semibold rounded-full px-1.5 py-0.5 ${
                        basemap === "s2"
                          ? "text-green-700 bg-green-50"
                          : "text-gray-400 bg-gray-100"
                      }`}
                    >
                      {year}
                    </span>
                    <button
                      onClick={() => setExpS2((v) => !v)}
                      className="h-5 w-5 rounded-md flex items-center justify-center text-gray-400 hover:bg-gray-100 hover:text-gray-700"
                    >
                      {expS2 ? (
                        <ChevronUp className="h-3.5 w-3.5" />
                      ) : (
                        <ChevronDown className="h-3.5 w-3.5" />
                      )}
                    </button>
                  </div>
                  {expS2 && (
                    <div className="px-2.5 pb-2 pt-1.5 border-t border-gray-100 flex flex-wrap gap-1">
                      {sentinelYears.map((y) => (
                        <button
                          key={y}
                          onClick={() => {
                            setYear(y);
                            setBasemap("s2");
                          }}
                          className={`h-6 px-2.5 rounded-full text-[10px] font-semibold transition-colors ${
                            y === year && basemap === "s2"
                              ? "bg-green-600 text-white"
                              : "text-gray-500 bg-gray-50 hover:bg-gray-100"
                          }`}
                        >
                          {y}
                        </button>
                      ))}
                    </div>
                  )}
                </div>

                {/* Basemap */}
                <div className="bg-white/95 backdrop-blur-sm rounded-2xl shadow-sm overflow-hidden">
                  <div className="px-2.5 py-2 flex items-center gap-2">
                    <MapIcon
                      className={`h-3.5 w-3.5 ${
                        basemap !== "s2" ? "text-sky-600" : "text-gray-400"
                      }`}
                    />
                    <span className="text-[11px] font-semibold text-gray-700 flex-1">
                      Basemap
                    </span>
                    {basemap !== "s2" && (
                      <span className="text-[10px] font-semibold text-sky-700 bg-sky-50 rounded-full px-1.5 py-0.5">
                        {BASEMAP_LABELS[basemap]}
                      </span>
                    )}
                    <button
                      onClick={() => setExpBasemap((v) => !v)}
                      className="h-5 w-5 rounded-md flex items-center justify-center text-gray-400 hover:bg-gray-100 hover:text-gray-700"
                    >
                      {expBasemap ? (
                        <ChevronUp className="h-3.5 w-3.5" />
                      ) : (
                        <ChevronDown className="h-3.5 w-3.5" />
                      )}
                    </button>
                  </div>
                  {expBasemap && (
                    <div className="px-2.5 pb-2 pt-1.5 border-t border-gray-100 flex flex-col gap-1">
                      {(
                        [
                          { key: "esri", label: "ESRI Satellite", icon: Globe },
                          { key: "osm", label: "OpenStreetMap", icon: MapIcon },
                          { key: "dark", label: "Dark mode", icon: Moon },
                        ] as { key: Basemap; label: string; icon: any }[]
                      ).map((b) => {
                        const Icon = b.icon;
                        const active = basemap === b.key;
                        return (
                          <button
                            key={b.key}
                            onClick={() => setBasemap(b.key)}
                            className={`h-7 px-2 rounded-md text-[10px] font-semibold flex items-center gap-1.5 transition-colors ${
                              active
                                ? "bg-sky-600 text-white"
                                : "text-gray-600 bg-gray-50 hover:bg-gray-100"
                            }`}
                          >
                            <Icon className="h-3 w-3" />
                            {b.label}
                          </button>
                        );
                      })}
                    </div>
                  )}
                </div>
              </div>

              {/* Layer control stack (bottom-left, vertical, collapsible) */}
              <div className="absolute bottom-3 left-3 z-[400] flex flex-col gap-2 w-[270px]">
                {/* Kebun Petani (api/twin-plots) — colored by compliance status */}
                {panelLayers.has("petani") && (
                <div className="bg-white/95 backdrop-blur-sm rounded-2xl shadow-sm overflow-hidden">
                  <div className="px-2.5 py-2 flex items-center gap-2">
                    <Trees
                      className={`h-3.5 w-3.5 ${
                        showPetani ? "text-lime-600" : "text-gray-400"
                      }`}
                    />
                    <span className="text-[11px] font-semibold text-gray-700 flex-1">
                      Kebun Petani
                    </span>
                    {showPetani && petaniCount > 0 && (
                      <span className="text-[10px] font-bold text-lime-700 bg-lime-50 rounded-full px-1.5 py-0.5">
                        {petaniCount.toLocaleString("id-ID")}
                      </span>
                    )}
                    <button
                      onClick={() =>
                        setShowPetani((v) => {
                          const nv = !v;
                          if (nv) setExpPetani(true);
                          return nv;
                        })
                      }
                      className={`relative h-4 w-7 rounded-full transition-colors ${
                        showPetani ? "bg-lime-600" : "bg-gray-300"
                      }`}
                    >
                      <span
                        className={`absolute top-0.5 left-0.5 h-3 w-3 bg-white rounded-full shadow transition-transform ${
                          showPetani ? "translate-x-3" : "translate-x-0"
                        }`}
                      />
                    </button>
                    <button
                      onClick={() => setExpPetani((v) => !v)}
                      className="h-5 w-5 rounded-md flex items-center justify-center text-gray-400 hover:bg-gray-100 hover:text-gray-700"
                    >
                      {expPetani ? (
                        <ChevronUp className="h-3.5 w-3.5" />
                      ) : (
                        <ChevronDown className="h-3.5 w-3.5" />
                      )}
                    </button>
                  </div>
                  {expPetani && (
                    <div className="px-2.5 pb-2 pt-1.5 border-t border-gray-100 flex flex-col gap-1.5">
                      <select
                        value={petaniProvince}
                        onChange={(e) => setPetaniProvince(e.target.value)}
                        disabled={!showPetani}
                        className="h-7 rounded-md border border-gray-200 bg-white px-2 text-[10px] text-gray-700 disabled:opacity-50 focus:outline-none focus:border-lime-500"
                      >
                        <option value="">Semua Province</option>
                        {petaniProvinces.map((p) => (
                          <option key={p.province} value={p.province}>
                            {p.province} ({p.count.toLocaleString("id-ID")})
                          </option>
                        ))}
                      </select>
                      {showPetani && (
                        <p className="text-[10px] text-gray-500 leading-snug">
                          Menampilkan {petaniCount.toLocaleString("id-ID")} plot
                          petani (filter province opsional).
                        </p>
                      )}
                      <div className="grid grid-cols-2 gap-x-2 gap-y-1 pt-1 border-t border-gray-100">
                        {(
                          [
                            { key: "non-compliant", label: "Non-Compliant" },
                            { key: "indicative", label: "Indicative" },
                            { key: "compliant", label: "Compliant" },
                            { key: "unknown", label: "Belum dicek" },
                          ] as { key: PetaniStatus; label: string }[]
                        ).map((s) => (
                          <div key={s.key} className="flex items-center gap-1.5">
                            <span
                              className="h-2.5 w-2.5 rounded-sm"
                              style={{
                                background: PETANI_COLORS[s.key].fill,
                                border: `1px solid ${PETANI_COLORS[s.key].stroke}`,
                              }}
                            />
                            <span className="text-[9px] text-gray-600">
                              {s.label}
                            </span>
                          </div>
                        ))}
                      </div>
                    </div>
                  )}
                </div>
                )}

                {/* Palm Oil Mills */}
                {panelLayers.has("mills") && (
                <div className="bg-white/95 backdrop-blur-sm rounded-2xl shadow-sm overflow-hidden">
                  <div className="px-2.5 py-2 flex items-center gap-2">
                    <Factory
                      className={`h-3.5 w-3.5 ${
                        showMills ? "text-violet-600" : "text-gray-400"
                      }`}
                    />
                    <span className="text-[11px] font-semibold text-gray-700 flex-1">
                      Palm Oil Mills
                    </span>
                    {showMills && millCount > 0 && (
                      <span className="text-[10px] font-bold text-violet-700 bg-violet-50 rounded-full px-1.5 py-0.5">
                        {millCount.toLocaleString("id-ID")}
                      </span>
                    )}
                    <button
                      onClick={() => setShowMills((v) => !v)}
                      className={`relative h-4 w-7 rounded-full transition-colors ${
                        showMills ? "bg-violet-600" : "bg-gray-300"
                      }`}
                    >
                      <span
                        className={`absolute top-0.5 left-0.5 h-3 w-3 bg-white rounded-full shadow transition-transform ${
                          showMills ? "translate-x-3" : "translate-x-0"
                        }`}
                      />
                    </button>
                    <button
                      onClick={() => setExpMills((v) => !v)}
                      className="h-5 w-5 rounded-md flex items-center justify-center text-gray-400 hover:bg-gray-100 hover:text-gray-700"
                    >
                      {expMills ? (
                        <ChevronUp className="h-3.5 w-3.5" />
                      ) : (
                        <ChevronDown className="h-3.5 w-3.5" />
                      )}
                    </button>
                  </div>
                  {expMills && (
                    <div className="px-2.5 pb-2 pt-1.5 border-t border-gray-100 flex flex-col gap-1.5">
                      <p className="text-[10px] text-gray-500 leading-snug">
                        Universal Mill List — lokasi pabrik kelapa sawit. Klik
                        marker untuk detail.
                      </p>
                      <div className="flex items-center gap-1.5">
                        <span className="h-3 w-3 rounded-full bg-violet-600 border border-white shadow-sm" />
                        <span className="text-[9px] text-gray-600">
                          Palm oil mill
                        </span>
                      </div>
                    </div>
                  )}
                </div>
                )}

                {/* Fire Hotspots */}
                {panelLayers.has("fires") && (
                <div className="bg-white/95 backdrop-blur-sm rounded-2xl shadow-sm overflow-hidden">
                  <div className="px-2.5 py-2 flex items-center gap-2">
                    <Flame
                      className={`h-3.5 w-3.5 ${
                        showFires ? "text-orange-500" : "text-gray-400"
                      }`}
                    />
                    <span className="text-[11px] font-semibold text-gray-700 flex-1">
                      Fire Hotspots
                    </span>
                    <button
                      onClick={() => setShowFires((v) => !v)}
                      className={`relative h-4 w-7 rounded-full transition-colors ${
                        showFires ? "bg-orange-500" : "bg-gray-300"
                      }`}
                    >
                      <span
                        className={`absolute top-0.5 left-0.5 h-3 w-3 bg-white rounded-full shadow transition-transform ${
                          showFires ? "translate-x-3" : "translate-x-0"
                        }`}
                      />
                    </button>
                    <button
                      onClick={() => setExpFires((v) => !v)}
                      className="h-5 w-5 rounded-md flex items-center justify-center text-gray-400 hover:bg-gray-100 hover:text-gray-700"
                    >
                      {expFires ? (
                        <ChevronUp className="h-3.5 w-3.5" />
                      ) : (
                        <ChevronDown className="h-3.5 w-3.5" />
                      )}
                    </button>
                  </div>
                  {expFires && (
                    <div className="px-2.5 pb-2 pt-1 flex flex-col gap-2 border-t border-gray-100">
                      <div className="flex items-center gap-1.5 mt-1">
                        <input
                          type="date"
                          value={startDate}
                          max={endDate}
                          onChange={(e) => setStartDate(e.target.value)}
                          disabled={!showFires}
                          className="h-7 flex-1 min-w-0 rounded-md border border-gray-200 bg-white px-1.5 text-[10px] text-gray-700 disabled:opacity-50"
                        />
                        <span className="text-[10px] text-gray-400">→</span>
                        <input
                          type="date"
                          value={endDate}
                          min={startDate}
                          max={todayISO()}
                          onChange={(e) => setEndDate(e.target.value)}
                          disabled={!showFires}
                          className="h-7 flex-1 min-w-0 rounded-md border border-gray-200 bg-white px-1.5 text-[10px] text-gray-700 disabled:opacity-50"
                        />
                      </div>
                      <div className="flex items-center gap-1">
                        {[
                          { label: "7d", days: 7 },
                          { label: "30d", days: 30 },
                          { label: "90d", days: 90 },
                        ].map((p) => (
                          <button
                            key={p.label}
                            onClick={() => {
                              setStartDate(daysAgoISO(p.days));
                              setEndDate(todayISO());
                            }}
                            disabled={!showFires}
                            className="h-6 flex-1 rounded-full text-[10px] font-semibold text-gray-600 bg-gray-100 hover:bg-orange-100 hover:text-orange-700 transition-colors disabled:opacity-50"
                          >
                            {p.label}
                          </button>
                        ))}
                      </div>
                    </div>
                  )}
                </div>
                )}

                {/* Peatland Areas */}
                {panelLayers.has("peatland") && (
                <div className="bg-white/95 backdrop-blur-sm rounded-2xl shadow-sm overflow-hidden">
                  <div className="px-2.5 py-2 flex items-center gap-2">
                    <Layers
                      className={`h-3.5 w-3.5 ${
                        showPeatland ? "text-teal-600" : "text-gray-400"
                      }`}
                    />
                    <span className="text-[11px] font-semibold text-gray-700 flex-1">
                      Peatland Areas
                    </span>
                    {showPeatland && peatlandCount > 0 && (
                      <span className="text-[10px] font-bold text-teal-700 bg-teal-50 rounded-full px-1.5 py-0.5">
                        {peatlandCount.toLocaleString("id-ID")}
                      </span>
                    )}
                    <button
                      onClick={() => setShowPeatland((v) => !v)}
                      className={`relative h-4 w-7 rounded-full transition-colors ${
                        showPeatland ? "bg-teal-600" : "bg-gray-300"
                      }`}
                    >
                      <span
                        className={`absolute top-0.5 left-0.5 h-3 w-3 bg-white rounded-full shadow transition-transform ${
                          showPeatland ? "translate-x-3" : "translate-x-0"
                        }`}
                      />
                    </button>
                    <button
                      onClick={() => setExpPeat((v) => !v)}
                      className="h-5 w-5 rounded-md flex items-center justify-center text-gray-400 hover:bg-gray-100 hover:text-gray-700"
                    >
                      {expPeat ? (
                        <ChevronUp className="h-3.5 w-3.5" />
                      ) : (
                        <ChevronDown className="h-3.5 w-3.5" />
                      )}
                    </button>
                  </div>
                  {expPeat && (
                    <div className="px-2.5 pb-2 pt-1.5 flex flex-col gap-1.5 border-t border-gray-100">
                      <select
                        value={filterProvinsi}
                        onChange={(e) => setFilterProvinsi(e.target.value)}
                        disabled={!showPeatland}
                        className="h-7 rounded-md border border-gray-200 bg-white px-2 text-[10px] text-gray-700 disabled:opacity-50 focus:outline-none focus:border-teal-400"
                      >
                        <option value="">All Provinsi</option>
                        {filterOpts.provinsi.map((v) => (
                          <option key={v} value={v}>
                            {v}
                          </option>
                        ))}
                      </select>

                      <select
                        value={filterKubah}
                        onChange={(e) => setFilterKubah(e.target.value)}
                        disabled={!showPeatland}
                        className="h-7 rounded-md border border-gray-200 bg-white px-2 text-[10px] text-gray-700 disabled:opacity-50 focus:outline-none focus:border-teal-400"
                      >
                        <option value="">All Kubah</option>
                        {filterOpts.kubah.map((v) => (
                          <option key={v} value={v}>
                            {v}
                          </option>
                        ))}
                      </select>

                      <input
                        type="text"
                        list="khg-options"
                        value={filterKhg}
                        onChange={(e) => setFilterKhg(e.target.value)}
                        placeholder="Search Nama KHG…"
                        disabled={!showPeatland}
                        className="h-7 rounded-md border border-gray-200 bg-white px-2 text-[10px] text-gray-700 disabled:opacity-50 focus:outline-none focus:border-teal-400"
                      />
                      <datalist id="khg-options">
                        {filterOpts.khg.slice(0, 1000).map((v) => (
                          <option key={v} value={v} />
                        ))}
                      </datalist>

                      {(filterProvinsi || filterKubah || filterKhg) && (
                        <button
                          onClick={() => {
                            setFilterProvinsi("");
                            setFilterKubah("");
                            setFilterKhg("");
                          }}
                          className="h-6 rounded-full text-[10px] font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors"
                        >
                          Clear filters
                        </button>
                      )}

                      <div className="flex items-center gap-3 pt-1 border-t border-gray-100">
                        <div className="flex items-center gap-1">
                          <span className="h-2.5 w-2.5 rounded-sm bg-amber-500/60 border border-amber-700" />
                          <span className="text-[9px] text-gray-600">
                            Kubah Gambut
                          </span>
                        </div>
                        <div className="flex items-center gap-1">
                          <span className="h-2.5 w-2.5 rounded-sm bg-teal-500/60 border border-teal-700" />
                          <span className="text-[9px] text-gray-600">
                            Non-Kubah
                          </span>
                        </div>
                      </div>
                    </div>
                  )}
                </div>
                )}

                {/* KLHK — Kawasan Hutan */}
                {panelLayers.has("klhk") && (
                <div className="bg-white/95 backdrop-blur-sm rounded-2xl shadow-sm overflow-hidden">
                  <div className="px-2.5 py-2 flex items-center gap-2">
                    <Leaf
                      className={`h-3.5 w-3.5 ${
                        showKlhk ? "text-emerald-600" : "text-gray-400"
                      }`}
                    />
                    <span className="text-[11px] font-semibold text-gray-700 flex-1">
                      Kawasan Hutan (KLHK)
                    </span>
                    <button
                      onClick={() => setShowKlhk((v) => !v)}
                      className={`relative h-4 w-7 rounded-full transition-colors ${
                        showKlhk ? "bg-emerald-600" : "bg-gray-300"
                      }`}
                    >
                      <span
                        className={`absolute top-0.5 left-0.5 h-3 w-3 bg-white rounded-full shadow transition-transform ${
                          showKlhk ? "translate-x-3" : "translate-x-0"
                        }`}
                      />
                    </button>
                    <button
                      onClick={() => setExpKlhk((v) => !v)}
                      className="h-5 w-5 rounded-md flex items-center justify-center text-gray-400 hover:bg-gray-100 hover:text-gray-700"
                    >
                      {expKlhk ? (
                        <ChevronUp className="h-3.5 w-3.5" />
                      ) : (
                        <ChevronDown className="h-3.5 w-3.5" />
                      )}
                    </button>
                  </div>
                  {expKlhk && (
                    <div className="px-2.5 pb-2 pt-1.5 border-t border-gray-100 flex flex-col gap-1.5">
                      <p className="text-[10px] text-gray-500 leading-snug">
                        Peta Kawasan Hutan — KLHK SIGAP Interaktif (ArcGIS
                        MapServer). Overlay semi-transparan di atas basemap.
                      </p>
                      <a
                        href={KLHK_SOURCE_URL}
                        target="_blank"
                        rel="noreferrer"
                        className="text-[9px] text-emerald-700 hover:underline break-all"
                      >
                        Sumber: geoportal.menlhk.go.id
                      </a>
                    </div>
                  )}
                </div>
                )}

                {/* Radius Kebun (1 & 5 km) */}
                {panelLayers.has("radius") && (
                <div className="bg-white/95 backdrop-blur-sm rounded-2xl shadow-sm overflow-hidden">
                  <div className="px-2.5 py-2 flex items-center gap-2">
                    <Target
                      className={`h-3.5 w-3.5 ${
                        showRadius ? "text-orange-500" : "text-gray-400"
                      }`}
                    />
                    <span className="text-[11px] font-semibold text-gray-700 flex-1">
                      Radius Kebun (1 &amp; 5 km)
                    </span>
                    <button
                      onClick={() => setShowRadius((v) => !v)}
                      className={`relative h-4 w-7 rounded-full transition-colors ${
                        showRadius ? "bg-orange-500" : "bg-gray-300"
                      }`}
                    >
                      <span
                        className={`absolute top-0.5 left-0.5 h-3 w-3 bg-white rounded-full shadow transition-transform ${
                          showRadius ? "translate-x-3" : "translate-x-0"
                        }`}
                      />
                    </button>
                    <button
                      onClick={() => setExpRadius((v) => !v)}
                      className="h-5 w-5 rounded-md flex items-center justify-center text-gray-400 hover:bg-gray-100 hover:text-gray-700"
                    >
                      {expRadius ? (
                        <ChevronUp className="h-3.5 w-3.5" />
                      ) : (
                        <ChevronDown className="h-3.5 w-3.5" />
                      )}
                    </button>
                  </div>
                  {expRadius && (
                    <div className="px-2.5 pb-2 pt-1.5 border-t border-gray-100 flex flex-col gap-1.5">
                      <p className="text-[10px] text-gray-500 leading-snug">
                        Buffer 1 km &amp; 5 km di sekeliling tiap kebun untuk
                        analisis proximity hotspot.
                      </p>
                      <div className="flex items-center gap-3">
                        <div className="flex items-center gap-1">
                          <span className="h-2.5 w-2.5 rounded-sm bg-orange-500/40 border border-orange-600" />
                          <span className="text-[9px] text-gray-600">1 km</span>
                        </div>
                        <div className="flex items-center gap-1">
                          <span className="h-2.5 w-2.5 rounded-sm bg-amber-400/30 border border-amber-500" />
                          <span className="text-[9px] text-gray-600">5 km</span>
                        </div>
                      </div>
                    </div>
                  )}
                </div>
                )}

                {/* Radius Mill (10–50 km) — Indonesia only */}
                {panelLayers.has("millradius") && (
                <div className="bg-white/95 backdrop-blur-sm rounded-2xl shadow-sm overflow-hidden">
                  <div className="px-2.5 py-2 flex items-center gap-2">
                    <Crosshair
                      className={`h-3.5 w-3.5 ${
                        showMillRadius ? "text-orange-600" : "text-gray-400"
                      }`}
                    />
                    <span className="text-[11px] font-semibold text-gray-700 flex-1">
                      Radius Mill (10–50 km)
                    </span>
                    <button
                      onClick={() => setShowMillRadius((v) => !v)}
                      className={`relative h-4 w-7 rounded-full transition-colors ${
                        showMillRadius ? "bg-orange-600" : "bg-gray-300"
                      }`}
                    >
                      <span
                        className={`absolute top-0.5 left-0.5 h-3 w-3 bg-white rounded-full shadow transition-transform ${
                          showMillRadius ? "translate-x-3" : "translate-x-0"
                        }`}
                      />
                    </button>
                    <button
                      onClick={() => setExpMillRadius((v) => !v)}
                      className="h-5 w-5 rounded-md flex items-center justify-center text-gray-400 hover:bg-gray-100 hover:text-gray-700"
                    >
                      {expMillRadius ? (
                        <ChevronUp className="h-3.5 w-3.5" />
                      ) : (
                        <ChevronDown className="h-3.5 w-3.5" />
                      )}
                    </button>
                  </div>
                  {expMillRadius && (
                    <div className="px-2.5 pb-2 pt-1.5 border-t border-gray-100 flex flex-col gap-1.5">
                      <p className="text-[10px] text-gray-500 leading-snug">
                        Buffer 10/20/30/50 km di sekeliling mill (hanya Indonesia ·
                        {" "}
                        {MILL_OVERLAP.millIds.size.toLocaleString("id-ID")} mill).
                      </p>
                      <div className="flex items-center gap-2.5 flex-wrap">
                        {[
                          { km: "10 km", c: "border-orange-600" },
                          { km: "20 km", c: "border-orange-500" },
                          { km: "30 km", c: "border-orange-400" },
                          { km: "50 km", c: "border-amber-400" },
                        ].map((r) => (
                          <div key={r.km} className="flex items-center gap-1">
                            <span
                              className={`h-2.5 w-2.5 rounded-full border-2 ${r.c}`}
                            />
                            <span className="text-[9px] text-gray-600">
                              {r.km}
                            </span>
                          </div>
                        ))}
                      </div>
                    </div>
                  )}
                </div>
                )}
              </div>

              {/* Add-layer "+" button (bottom-right) */}
              <button
                onClick={() => setShowAddLayer((v) => !v)}
                aria-label="Tambah layer"
                className="absolute bottom-3 right-3 z-[400] h-11 w-11 rounded-full bg-green-500 text-white shadow-lg flex items-center justify-center hover:bg-green-600 active:scale-95 transition"
              >
                <Plus className="h-5 w-5" strokeWidth={2.5} />
              </button>

              {/* Add-layer popup (checkbox list) */}
              {showAddLayer && (
                <>
                  <div
                    className="absolute inset-0 z-[410]"
                    onClick={() => setShowAddLayer(false)}
                  />
                  <div className="absolute bottom-16 right-3 z-[420] w-[250px] bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div className="px-3 py-2.5 flex items-center justify-between border-b border-gray-100">
                      <span className="text-[11px] font-bold text-gray-700 uppercase tracking-wide">
                        Tambah Layer
                      </span>
                      <button
                        onClick={() => setShowAddLayer(false)}
                        aria-label="Tutup"
                        className="h-5 w-5 rounded-md flex items-center justify-center text-gray-400 hover:bg-gray-100 hover:text-gray-700"
                      >
                        <X className="h-3.5 w-3.5" />
                      </button>
                    </div>
                    <div className="max-h-[60vh] overflow-y-auto">
                      <div className="p-1.5 flex flex-col">
                        {LAYER_META.map((l) => {
                          const Icon = l.icon;
                          const checked = panelLayers.has(l.key);
                          return (
                            <label
                              key={l.key}
                              className="flex items-center gap-2.5 px-2 py-2 rounded-xl cursor-pointer hover:bg-gray-50"
                            >
                              <input
                                type="checkbox"
                                checked={checked}
                                onChange={(e) =>
                                  togglePanelLayer(l.key, e.target.checked)
                                }
                                className="h-3.5 w-3.5 rounded accent-green-600"
                              />
                              <Icon className="h-3.5 w-3.5 text-gray-500" />
                              <span className="text-[11px] font-semibold text-gray-700">
                                {l.label}
                              </span>
                            </label>
                          );
                        })}
                      </div>

                      {/* GEE forest & deforestation raster layers (toggle on the map directly) */}
                      <div className="px-3 pt-2 pb-1 border-t border-gray-100">
                        <span className="text-[10px] font-bold text-gray-400 uppercase tracking-wide">
                          Deforestasi (GEE)
                        </span>
                      </div>
                      <div className="p-1.5 pt-0.5 flex flex-col">
                        {geeDatasets.map((ds) => (
                          <label
                            key={ds.key}
                            className="flex items-center gap-2.5 px-2 py-2 rounded-xl cursor-pointer hover:bg-gray-50"
                          >
                            <input
                              type="checkbox"
                              checked={activeGee.has(ds.key)}
                              onChange={(e) => toggleGee(ds.key, e.target.checked)}
                              className="h-3.5 w-3.5 rounded accent-green-600"
                            />
                            <span
                              className="h-2.5 w-2.5 rounded-sm shrink-0 border border-black/10"
                              style={{ background: ds.color }}
                            />
                            <span className="text-[11px] font-semibold text-gray-700 leading-tight">
                              {ds.name}
                            </span>
                          </label>
                        ))}
                      </div>
                    </div>
                  </div>
                </>
              )}
            </div>

            {/* Theme-driven analysis cards (header tabs) — dummy data */}
            {(() => {
              const provLabel = petaniProvince || plotProvince || "semua provinsi";
              const themeCards = buildThemes(provLabel, summary)[activeTab];
              const accentKey = TABS.find((t) => t.label === activeTab)!.accent;
              const accent = ACCENT[accentKey];
              return (
                <div className="mt-4">
                  <div className="flex items-center gap-2 mb-2.5">
                    <span className="text-sm font-semibold text-gray-900">
                      {activeTab}
                    </span>
                    {(() => {
                      const live =
                        activeTab === "Deforestation" ||
                        (activeTab === "Protected Area" && summary);
                      return (
                        <span
                          className={`text-[10px] font-medium rounded-full px-2 py-0.5 ${
                            live
                              ? "text-emerald-700 bg-emerald-50"
                              : "text-gray-400 bg-gray-100"
                          }`}
                        >
                          {live ? "data nyata · EUDR" : "data dummy"}
                        </span>
                      );
                    })()}
                  </div>
                  <div className="grid grid-cols-2 lg:grid-cols-4 gap-3">
                    {themeCards.map((card, i) => {
                      const Icon = card.icon;
                      const hero = !!card.highlight;
                      const wide = card.wide ? "lg:col-span-2" : "";
                      const base = hero
                        ? `${accent.hero} text-white`
                        : "bg-[#f3f5f1]";
                      return (
                        <div
                          key={i}
                          className={`${base} ${wide} rounded-2xl p-4 flex flex-col`}
                        >
                          <div
                            className={`flex items-center gap-1.5 text-[11px] font-medium mb-3 ${
                              hero ? "opacity-95" : "text-gray-600"
                            }`}
                          >
                            <Icon
                              className={`h-3 w-3 ${hero ? "" : accent.icon}`}
                            />
                            {card.label}
                          </div>

                          {card.kind === "metric" && (
                            <>
                              <div
                                className={`text-4xl font-light leading-none mb-2 ${
                                  hero ? "" : "text-gray-900"
                                }`}
                              >
                                {card.value}
                                {card.unit && (
                                  <span
                                    className={`text-2xl ${
                                      hero ? "opacity-80" : "text-gray-400"
                                    }`}
                                  >
                                    {" "}
                                    {card.unit}
                                  </span>
                                )}
                              </div>
                              {card.sub && (
                                <p
                                  className={`text-[10px] leading-snug ${
                                    hero ? "opacity-80" : "text-gray-500"
                                  }`}
                                >
                                  {card.sub}
                                </p>
                              )}
                            </>
                          )}

                          {card.kind === "breakdown" && (
                            <div className="flex flex-col">
                              {card.rows.map((r, j) => (
                                <div
                                  key={j}
                                  className={`flex items-center justify-between gap-2 text-[11px] py-1.5 ${
                                    j > 0
                                      ? hero
                                        ? "border-t border-white/20"
                                        : "border-t border-gray-200"
                                      : ""
                                  }`}
                                >
                                  <span
                                    className={hero ? "opacity-85" : "text-gray-500"}
                                  >
                                    {r.label}
                                  </span>
                                  <span
                                    className={`font-semibold whitespace-nowrap ${
                                      hero ? "" : "text-gray-800"
                                    }`}
                                  >
                                    {r.value}
                                  </span>
                                </div>
                              ))}
                            </div>
                          )}

                          {card.kind === "list" && (
                            <div className="flex flex-col max-h-32 overflow-y-auto pr-1">
                              {card.items.map((it, j) => (
                                <div
                                  key={j}
                                  className={`py-1.5 ${
                                    j > 0 ? "border-t border-gray-200" : ""
                                  }`}
                                >
                                  <div className="text-[11px] font-semibold text-gray-800 leading-tight">
                                    {it.primary}
                                  </div>
                                  {it.secondary && (
                                    <div className="text-[10px] text-gray-500 leading-tight">
                                      {it.secondary}
                                    </div>
                                  )}
                                </div>
                              ))}
                            </div>
                          )}
                        </div>
                      );
                    })}
                  </div>
                </div>
              );
            })()}
          </div>
        </main>

        {/* Right sidebar */}
        <aside className="w-[300px] flex flex-col gap-4 shrink-0 overflow-y-auto">
          {/* Top widget: Critical Alert on the Hotspot tab, EUDR summary otherwise */}
          {activeTab === "Hotspot" ? (
          <div className="bg-green-600 text-white rounded-3xl p-5">
            {selectedFire ? (() => {
              const fmtNum = (v: any, dp = 2) =>
                typeof v === "number" && Number.isFinite(v)
                  ? v.toFixed(dp).replace(/\.0+$/, "")
                  : v ?? "-";
              const confLabel = (v: any) => {
                const s = String(v ?? "").toLowerCase();
                if (s === "h" || s === "high") return "High";
                if (s === "n" || s === "nominal") return "Nominal";
                if (s === "l" || s === "low") return "Low";
                return v ?? "-";
              };
              const p = selectedFire;
              const lat = p.__lat;
              const lng = p.__lng;
              const isAggregated =
                typeof p.count === "number" && p.count > 1;

              const items: {
                icon: any;
                label: string;
                value: string;
                iconBg: string;
                iconColor: string;
              }[] = [];

              if (isAggregated) {
                items.push({
                  icon: Flame,
                  label: "Total Alerts",
                  value: `${p.count} hotspots`,
                  iconBg: "bg-red-100",
                  iconColor: "text-red-600",
                });
              }

              if (p.alert__date) {
                items.push({
                  icon: FileText,
                  label: "Date",
                  value: String(p.alert__date),
                  iconBg: "bg-orange-100",
                  iconColor: "text-orange-600",
                });
              }

              if (p.confidence__cat !== undefined && p.confidence__cat !== null) {
                items.push({
                  icon: Activity,
                  label: "Confidence",
                  value: confLabel(p.confidence__cat),
                  iconBg: "bg-yellow-100",
                  iconColor: "text-yellow-700",
                });
              }

              const frpVal = p.frp__MW ?? p.countfrp__MW ?? p.sum__frp__MW;
              if (frpVal !== undefined && frpVal !== null) {
                items.push({
                  icon: Flame,
                  label: isAggregated ? "Total FRP" : "Fire Radiative Power",
                  value: `${fmtNum(frpVal)} MW`,
                  iconBg: "bg-red-100",
                  iconColor: "text-red-700",
                });
              }

              const brightVal = p.bright_ti4 ?? p.avg__bright_ti4;
              if (brightVal !== undefined && brightVal !== null) {
                items.push({
                  icon: Thermometer,
                  label: "Brightness TI4",
                  value: `${fmtNum(brightVal)} K`,
                  iconBg: "bg-amber-100",
                  iconColor: "text-amber-700",
                });
              }

              const brightVal5 = p.bright_ti5 ?? p.avg__bright_ti5;
              if (brightVal5 !== undefined && brightVal5 !== null) {
                items.push({
                  icon: Thermometer,
                  label: "Brightness TI5",
                  value: `${fmtNum(brightVal5)} K`,
                  iconBg: "bg-amber-100",
                  iconColor: "text-amber-700",
                });
              }

              return (
                <>
                  <div className="flex items-start justify-between mb-3">
                    <div>
                      <h3 className="text-base font-semibold flex items-center gap-1.5">
                        <Flame className="h-4 w-4" />
                        Selected Hotspot
                      </h3>
                      <p className="text-[11px] opacity-80">
                        {typeof lat === "number" && typeof lng === "number"
                          ? `${lat.toFixed(4)}, ${lng.toFixed(4)}`
                          : "Click another marker to inspect"}
                      </p>
                    </div>
                    <button
                      onClick={() => {
                        if (lastFireMarkerRef.current) {
                          lastFireMarkerRef.current.setIcon(dangerIcon);
                          lastFireMarkerRef.current = null;
                        }
                        setSelectedFire(null);
                      }}
                      className="h-7 w-7 rounded-full bg-white/15 hover:bg-white/25 flex items-center justify-center"
                    >
                      <X className="h-3.5 w-3.5" />
                    </button>
                  </div>
                  <div className="space-y-2.5">
                    {items.length === 0 && (
                      <div className="bg-white/95 rounded-2xl p-3 text-xs text-gray-700">
                        No detail available for this hotspot.
                      </div>
                    )}
                    {items.map((a, i) => {
                      const Icon = a.icon;
                      return (
                        <div
                          key={i}
                          className="bg-white/95 rounded-2xl p-2.5 flex items-center gap-2.5"
                        >
                          <div
                            className={`h-9 w-9 rounded-full flex items-center justify-center shrink-0 ${a.iconBg}`}
                          >
                            <Icon className={`h-4 w-4 ${a.iconColor}`} />
                          </div>
                          <div className="flex-1 min-w-0">
                            <p className="text-[10px] text-gray-400 leading-tight">
                              {a.label}
                            </p>
                            <p className="text-xs font-semibold text-gray-800 truncate">
                              {a.value}
                            </p>
                          </div>
                        </div>
                      );
                    })}
                  </div>
                </>
              );
            })() : (
              <>
                <h3 className="text-base font-semibold mb-0.5">Critical Alert</h3>
                <p className="text-[11px] opacity-80 mb-4">
                  Click a fire hotspot on the map for details.
                </p>
                <div className="space-y-2.5">
                  {alerts.map((a, i) => {
                    const Icon = a.icon;
                    return (
                      <div
                        key={i}
                        className="bg-white/95 rounded-2xl p-2.5 flex items-center gap-2.5"
                      >
                        <div
                          className={`h-9 w-9 rounded-full flex items-center justify-center shrink-0 ${a.iconBg}`}
                        >
                          <Icon className={`h-4 w-4 ${a.iconColor}`} />
                        </div>
                        <div className="flex-1 min-w-0">
                          <p className="text-[10px] text-gray-400 leading-tight">
                            {a.title}
                          </p>
                          <p className="text-xs font-semibold text-gray-800 truncate">
                            {a.desc}
                          </p>
                        </div>
                        <div
                          className={`h-6 w-6 rounded-full flex items-center justify-center shrink-0 ${
                            a.ok
                              ? "bg-gray-900 text-white"
                              : "bg-gray-200 text-gray-600"
                          }`}
                        >
                          {a.ok ? (
                            <Check className="h-3.5 w-3.5" strokeWidth={3} />
                          ) : (
                            <X className="h-3.5 w-3.5" strokeWidth={3} />
                          )}
                        </div>
                      </div>
                    );
                  })}
                </div>
              </>
            )}
          </div>
          ) : (
            <div className="bg-rose-600 text-white rounded-3xl p-5">
              <div className="flex items-center gap-1.5 text-[11px] font-medium opacity-95 mb-3">
                <Trees className="h-3.5 w-3.5" />
                Plot Dianalisis (EUDR)
              </div>
              <div className="text-4xl font-light leading-none mb-2">
                {(summary?.total ?? DEFOR_SUMMARY.total).toLocaleString("id-ID")}
                <span className="text-2xl opacity-80"> plot</span>
              </div>
              <p className="text-[11px] opacity-85">
                {(summary?.high_risk ?? DEFOR_SUMMARY.high).toLocaleString(
                  "id-ID"
                )}{" "}
                risiko tinggi ·{" "}
                {(summary?.low_risk ?? DEFOR_SUMMARY.low).toLocaleString(
                  "id-ID"
                )}{" "}
                risiko rendah
              </p>
              {summary?.last_checked && (
                <p className="text-[10px] opacity-70 mt-2">
                  Diperbarui:{" "}
                  {new Date(summary.last_checked).toLocaleString("id-ID")}
                </p>
              )}
            </div>
          )}

          {/* Spectral Indices */}
          <div className="bg-white rounded-3xl p-5">
            <h3 className="text-base font-semibold text-gray-900 mb-0.5">
              Spectral Indices
            </h3>
            <p className="text-[11px] text-gray-500 mb-4">
              Vegetation health & burn severity over time.
            </p>

            {/* NBR chart */}
            <div className="relative bg-[#f3f5f1] rounded-2xl p-3 mb-3">
              <div className="flex items-center justify-between mb-1">
                <div className="flex items-center gap-1.5">
                  <Flame className="h-3 w-3 text-orange-500" />
                  <span className="text-[10px] font-semibold text-gray-700">
                    Normal Burned Ratio
                  </span>
                </div>
                <span className="text-[10px] font-semibold text-orange-500">
                  {indices.nbrLatest.toFixed(2)}
                </span>
              </div>
              <div className="relative h-16">
                <ResponsiveContainer width="100%" height="100%">
                  <AreaChart
                    data={indices.nbr}
                    margin={{ top: 0, right: 0, bottom: 0, left: 0 }}
                  >
                    <defs>
                      <linearGradient id="nbrFill" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stopColor="#f97316" stopOpacity={0.4} />
                        <stop offset="100%" stopColor="#f97316" stopOpacity={0} />
                      </linearGradient>
                    </defs>
                    <Area
                      type="monotone"
                      dataKey="y"
                      stroke="#f97316"
                      strokeWidth={2}
                      fill="url(#nbrFill)"
                    />
                  </AreaChart>
                </ResponsiveContainer>
              </div>
              <div className="flex justify-between text-[9px] text-gray-400 mt-1 px-1">
                <span>Jan</span>
                <span>Apr</span>
                <span>Jul</span>
                <span>Dec</span>
              </div>
            </div>

            {/* NDVI chart */}
            <div className="relative bg-[#f3f5f1] rounded-2xl p-3 mb-4">
              <div className="flex items-center justify-between mb-1">
                <div className="flex items-center gap-1.5">
                  <Leaf className="h-3 w-3 text-green-600 fill-green-600" />
                  <span className="text-[10px] font-semibold text-gray-700">
                    NDVI
                  </span>
                </div>
                <span className="text-[10px] font-semibold text-green-600">
                  {indices.ndviLatest.toFixed(2)}
                </span>
              </div>
              <div className="relative h-16">
                <ResponsiveContainer width="100%" height="100%">
                  <AreaChart
                    data={indices.ndvi}
                    margin={{ top: 0, right: 0, bottom: 0, left: 0 }}
                  >
                    <defs>
                      <linearGradient id="ndviFill" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stopColor="#16a34a" stopOpacity={0.4} />
                        <stop offset="100%" stopColor="#16a34a" stopOpacity={0} />
                      </linearGradient>
                    </defs>
                    <Area
                      type="monotone"
                      dataKey="y"
                      stroke="#16a34a"
                      strokeWidth={2}
                      fill="url(#ndviFill)"
                    />
                  </AreaChart>
                </ResponsiveContainer>
              </div>
              <div className="flex justify-between text-[9px] text-gray-400 mt-1 px-1">
                <span>Jan</span>
                <span>Apr</span>
                <span>Jul</span>
                <span>Dec</span>
              </div>
            </div>

          </div>

          {/* Insight card */}
          <div className="bg-white rounded-3xl p-5">
            <div className="flex items-center gap-2 mb-1">
              <div className="h-7 w-7 rounded-full bg-amber-100 flex items-center justify-center">
                <Lightbulb className="h-3.5 w-3.5 text-amber-600" />
              </div>
              <h3 className="text-base font-semibold text-gray-900">Insight</h3>
            </div>
            <p className="text-[11px] text-gray-500 mb-4">
              {selectedFire
                ? "Auto-generated insights from the selected hotspot."
                : "Click a hotspot on the map to see insights."}
            </p>

            {selectedFire ? (
              (() => {
                const p = selectedFire;
                const frpRaw = p.frp__MW ?? p.countfrp__MW ?? p.sum__frp__MW;
                const frp = Number(frpRaw);
                const conf = String(p.confidence__cat || "").toLowerCase();
                const items: {
                  icon: any;
                  label: string;
                  value: string;
                  iconBg: string;
                  iconColor: string;
                }[] = [];

                if (Number.isFinite(frp)) {
                  let severity = "Low intensity";
                  let bg = "bg-yellow-100";
                  let fg = "text-yellow-700";
                  if (frp > 100) {
                    severity = "Extreme intensity";
                    bg = "bg-red-100";
                    fg = "text-red-700";
                  } else if (frp > 50) {
                    severity = "High intensity";
                    bg = "bg-red-100";
                    fg = "text-red-600";
                  } else if (frp > 10) {
                    severity = "Moderate intensity";
                    bg = "bg-orange-100";
                    fg = "text-orange-600";
                  }
                  items.push({
                    icon: Flame,
                    label: "Fire Intensity",
                    value: `${severity} · ${frp.toFixed(1)} MW`,
                    iconBg: bg,
                    iconColor: fg,
                  });
                }

                if (conf) {
                  let label = "Low confidence — uncertain detection";
                  let bg = "bg-yellow-100";
                  let fg = "text-yellow-700";
                  if (conf === "h" || conf === "high") {
                    label = "High confidence — verified detection";
                    bg = "bg-red-100";
                    fg = "text-red-600";
                  } else if (conf === "n" || conf === "nominal") {
                    label = "Nominal confidence — likely fire";
                    bg = "bg-orange-100";
                    fg = "text-orange-600";
                  }
                  items.push({
                    icon: AlertTriangle,
                    label: "Detection Confidence",
                    value: label,
                    iconBg: bg,
                    iconColor: fg,
                  });
                }

                if (p.alert__date) {
                  const d = new Date(p.alert__date);
                  if (!isNaN(d.getTime())) {
                    const days = Math.floor(
                      (Date.now() - d.getTime()) / (1000 * 60 * 60 * 24)
                    );
                    let age = "";
                    if (days <= 0) age = "detected today";
                    else if (days === 1) age = "detected yesterday";
                    else if (days < 7) age = `detected ${days} days ago`;
                    else if (days < 30)
                      age = `detected ${Math.floor(days / 7)} weeks ago`;
                    else age = `detected ${Math.floor(days / 30)} months ago`;
                    items.push({
                      icon: Clock,
                      label: "Detection Age",
                      value: `${p.alert__date} · ${age}`,
                      iconBg: "bg-blue-100",
                      iconColor: "text-blue-600",
                    });
                  }
                }

                if (typeof p.count === "number" && p.count > 1) {
                  items.push({
                    icon: Layers,
                    label: "Cluster Size",
                    value: `${p.count} alerts in this grid cell`,
                    iconBg: "bg-purple-100",
                    iconColor: "text-purple-600",
                  });
                }

                let recValue = "Continue routine monitoring";
                let recBg = "bg-green-100";
                let recFg = "text-green-700";
                if (Number.isFinite(frp) && frp > 50) {
                  recValue =
                    "High-priority — dispatch ground team immediately";
                  recBg = "bg-red-100";
                  recFg = "text-red-700";
                } else if (Number.isFinite(frp) && frp > 10) {
                  recValue = "Monitor closely for spread risk";
                  recBg = "bg-orange-100";
                  recFg = "text-orange-700";
                } else if (conf === "h") {
                  recValue = "Verify on-site within 24 hours";
                  recBg = "bg-orange-100";
                  recFg = "text-orange-700";
                }
                items.push({
                  icon: Info,
                  label: "Recommendation",
                  value: recValue,
                  iconBg: recBg,
                  iconColor: recFg,
                });

                return (
                  <div className="space-y-2">
                    {items.map((it, i) => {
                      const Icon = it.icon;
                      return (
                        <div
                          key={i}
                          className="bg-[#f9faf7] rounded-2xl p-2.5 flex items-start gap-2.5"
                        >
                          <div
                            className={`h-8 w-8 rounded-full flex items-center justify-center shrink-0 ${it.iconBg}`}
                          >
                            <Icon className={`h-4 w-4 ${it.iconColor}`} />
                          </div>
                          <div className="flex-1 min-w-0 pt-0.5">
                            <p className="text-[10px] text-gray-400 leading-tight">
                              {it.label}
                            </p>
                            <p className="text-xs font-semibold text-gray-800 leading-snug">
                              {it.value}
                            </p>
                          </div>
                        </div>
                      );
                    })}
                  </div>
                );
              })()
            ) : (
              <div className="bg-[#f9faf7] rounded-2xl p-5 flex flex-col items-center text-center">
                <div className="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center mb-2">
                  <Flame className="h-4 w-4 text-gray-400" />
                </div>
                <p className="text-xs text-gray-500">
                  No hotspot selected yet.
                </p>
              </div>
            )}
          </div>
        </aside>
      </div>
    </div>
  );
}
