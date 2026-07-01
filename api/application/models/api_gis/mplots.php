<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Mplots — sumber data plot petani (polygon) untuk Palm Oil Digital Twin.
 *
 * Digerakkan dari ktv_survey_plot_polygon_geo (yang PUNYA geometry) + scope Partner:
 *   - polygon terbaru per (MemberID, PlotNr) dari ktv_survey_plot_polygon_geo
 *   - milik Partner tertentu (ktv_access_partner_member.apmPartnerID), member aktif
 *   - area/petani/lokasi via LEFT JOIN (ktv_survey_plot, members, wilayah)
 *
 * CATATAN AXIS: geometry dibangun sbg POINT(latitude longitude); konsumen (cron)
 * yang membalik ke [lng, lat] standar GeoJSON.
 */
class Mplots extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * @param int $partnerId  apmPartnerID (WAJIB, scope KPI)
     * @param int $limit      batas baris (0 = tanpa batas)
     * @return array rows: MemberID, PlotNr, SurveyNr, Revision, geojson, AreaHa, farmer, province, district
     */
    public function getPlotsGeoJSON($partnerId, $limit = 0) {
        $partnerId = intval($partnerId);
        if ($partnerId <= 0) {
            return array();
        }
        $limitSql = ($limit > 0) ? (' LIMIT ' . intval($limit)) : '';

        $sql = "SELECT
                    geo.MemberID,
                    geo.PlotNr,
                    geo.SurveyNr,
                    geo.Revision,
                    ST_AsGeoJSON(geo.Polygon) AS geojson,
                    COALESCE(sp.GardenAreaPolygon, geo.AreaHa) AS AreaHa,
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
                JOIN ktv_members m ON m.MemberID = geo.MemberID AND m.StatusCode = 'active'
                INNER JOIN ktv_access_partner_member acc
                    ON acc.apmMemberID = m.MemberID AND acc.apmPartnerID = ?
                LEFT JOIN ktv_members_extension me ON me.MemberID = m.MemberID
                LEFT JOIN ktv_village v      ON v.VillageID = m.VillageID
                LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
                LEFT JOIN ktv_district d     ON d.DistrictID = sd.DistrictID
                LEFT JOIN ktv_province p     ON p.ProvinceID = d.ProvinceID
                LEFT JOIN ktv_survey_plot sp
                    ON sp.MemberID = geo.MemberID AND sp.PlotNr = geo.PlotNr AND sp.SurveyNr = geo.SurveyNr
                WHERE geo.rn = 1
                {$limitSql}";

        $query = $this->db->query($sql, array($partnerId));
        return $query->result_array();
    }
}
