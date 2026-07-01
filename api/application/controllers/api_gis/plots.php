<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Endpoint GeoJSON plot petani untuk Palm Oil Digital Twin.
 *
 *   GET /api_gis/plots/geojson            -> semua plot aktif
 *   GET /api_gis/plots/geojson?province=12  (filter ProvinceID, opsional)
 *   GET /api_gis/plots/geojson?limit=500    (batas baris, opsional)
 *
 * Output: GeoJSON FeatureCollection. Tiap feature membawa `properties.plot_uid`
 * = "<MemberID>-<PlotNr>-<SurveyNr>" sebagai kunci sinkron yang stabil.
 *
 * Dipakai oleh cron palm-twin/server/jobs/sync-compliance.ts (sekali per jam).
 * Koordinat keluar dalam axis DB [lat, lng]; cron yang membalik ke [lng, lat].
 */
class Plots extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('api_gis/mplots');
    }

    public function geojson_get() {
        // Proteksi opsional: bila env PLOTS_GEOJSON_KEY di-set, wajib cocok dgn ?key=.
        // Bila tidak di-set, endpoint terbuka (whitelist di REST_Controller).
        $secret = getenv('PLOTS_GEOJSON_KEY');
        if ($secret !== false && $secret !== '') {
            if ((string) $this->get('key') !== (string) $secret) {
                $this->response(array('success' => false, 'error' => 'Forbidden'), 403);
                return;
            }
        }

        $provinceId = $this->get('province');
        $limit      = intval($this->get('limit'));

        $rows = $this->mplots->getPlotsGeoJSON($provinceId, $limit);

        $features = array();
        $seq = 0;
        foreach ($rows as $r) {
            $geometry = json_decode($r['geojson'], true);
            if (!$geometry) {
                continue; // lewati geometry rusak
            }
            $features[] = array(
                'type'       => 'Feature',
                'id'         => $seq++,
                'geometry'   => $geometry,
                'properties' => array(
                    'plot_uid'  => $r['MemberID'] . '-' . $r['PlotNr'] . '-' . $r['SurveyNr'],
                    'member_id' => intval($r['MemberID']),
                    'plot_nr'   => intval($r['PlotNr']),
                    'survey_nr' => intval($r['SurveyNr']),
                    'name'      => $r['farmer'],   // alias `name` -> dipakai API compliance
                    'farmer'    => $r['farmer'],
                    'province'  => $r['province'],
                    'district'  => $r['district'],
                    'area_ha'   => $r['AreaHa'] !== null ? floatval($r['AreaHa']) : null,
                ),
            );
        }

        $payload = array(
            'type'     => 'FeatureCollection',
            'features' => $features,
        );

        $this->response($payload, 200);
    }
}
