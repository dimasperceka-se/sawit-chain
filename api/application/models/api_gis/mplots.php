<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Mplots — sumber data plot petani (polygon) untuk Palm Oil Digital Twin.
 *
 * Mengambil polygon terbaru per (MemberID, PlotNr) dari
 * ktv_survey_plot_polygon_geo (kolom geometry `Polygon`) + atribut petani/lokasi.
 *
 * CATATAN AXIS: geometry di DB dibangun sebagai POINT(latitude longitude),
 * jadi ST_AsGeoJSON menghasilkan koordinat [lat, lng]. Konsumen (cron sync)
 * yang membalik ke [lng, lat] standar GeoJSON.
 */
class Mplots extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * @param int|null $ProvinceID  filter opsional
     * @param int      $limit       batas baris (0 = tanpa batas)
     * @return array rows: MemberID, PlotNr, SurveyNr, Revision, geojson, AreaHa, farmer, province, district
     */
    public function getPlotsGeoJSON($ProvinceID = null, $limit = 0) {
        $params = array();
        $where  = '';
        if (!empty($ProvinceID)) {
            $where .= ' AND p.ProvinceID = ?';
            $params[] = intval($ProvinceID);
        }
        $limitSql = ($limit > 0) ? (' LIMIT ' . intval($limit)) : '';

        $sql = "SELECT
                    geo.MemberID,
                    geo.PlotNr,
                    geo.SurveyNr,
                    geo.Revision,
                    ST_AsGeoJSON(geo.Polygon) AS geojson,
                    geo.AreaHa,
                    IFNULL(me.agCompanyName, m.MemberName) AS farmer,
                    p.Province  AS province,
                    d.District  AS district
                FROM (
                    SELECT x.MemberID, x.PlotNr, x.SurveyNr, x.Revision, x.Polygon, x.AreaHa,
                           ROW_NUMBER() OVER (
                               PARTITION BY x.MemberID, x.PlotNr
                               ORDER BY x.SurveyNr DESC, x.Revision DESC
                           ) AS rn
                    FROM ktv_survey_plot_polygon_geo x
                    WHERE x.Polygon IS NOT NULL
                ) geo
                JOIN ktv_members m              ON m.MemberID = geo.MemberID
                LEFT JOIN ktv_members_extension me ON me.MemberID = m.MemberID
                LEFT JOIN ktv_village v         ON v.VillageID = m.VillageID
                LEFT JOIN ktv_subdistrict sd    ON sd.SubDistrictID = v.SubDistrictID
                LEFT JOIN ktv_district d        ON d.DistrictID = sd.DistrictID
                LEFT JOIN ktv_province p        ON p.ProvinceID = d.ProvinceID
                WHERE geo.rn = 1
                  AND m.StatusCode = 'active'
                  {$where}
                {$limitSql}";

        $query = $this->db->query($sql, $params);
        return $query->result_array();
    }
}
