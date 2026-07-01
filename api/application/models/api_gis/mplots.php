<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Mplots — sumber data plot petani (polygon) untuk Palm Oil Digital Twin.
 *
 * Scope disamakan dengan Dashboard KPI (Sawit KPI Management):
 *   - plot PETANI (ktv_survey_plot), survei terbaru per (MemberID, PlotNr)
 *   - milik Partner tertentu (ktv_access_partner_member.apmPartnerID)
 *   - punya polygon (GardenAreaPolygon > 0), member & plot StatusCode='active'
 * Geometry diambil dari ktv_survey_plot_polygon_geo (revisi terbaru) via ST_AsGeoJSON.
 *
 * CATATAN AXIS: geometry dibangun sbg POINT(latitude longitude); konsumen (cron)
 * yang membalik ke [lng, lat] standar GeoJSON.
 */
class Mplots extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * @param int $partnerId  ktv_access_partner_member.apmPartnerID (WAJIB, scope KPI)
     * @param int $limit       batas baris (0 = tanpa batas)
     * @return array rows: MemberID, PlotNr, SurveyNr, Revision, geojson, AreaHa, farmer, province, district
     */
    public function getPlotsGeoJSON($partnerId, $limit = 0) {
        $partnerId = intval($partnerId);
        if ($partnerId <= 0) {
            return array();
        }
        $limitSql = ($limit > 0) ? (' LIMIT ' . intval($limit)) : '';

        $sql = "SELECT
                    a.MemberID,
                    a.PlotNr,
                    geo.SurveyNr,
                    geo.Revision,
                    ST_AsGeoJSON(geo.Polygon) AS geojson,
                    a.GardenAreaPolygon        AS AreaHa,
                    IFNULL(me.agCompanyName, m.MemberName) AS farmer,
                    p.Province  AS province,
                    d.District  AS district
                FROM ktv_survey_plot a
                JOIN (
                    SELECT MemberID, PlotNr, MAX(SurveyNr) AS SurveyNr
                    FROM ktv_survey_plot
                    WHERE StatusCode = 'active'
                    GROUP BY MemberID, PlotNr
                ) gl ON gl.MemberID = a.MemberID AND gl.PlotNr = a.PlotNr AND gl.SurveyNr = a.SurveyNr
                JOIN ktv_members m ON m.MemberID = a.MemberID AND m.StatusCode = 'active'
                INNER JOIN ktv_access_partner_member acc
                    ON acc.apmMemberID = m.MemberID AND acc.apmPartnerID = ?
                LEFT JOIN ktv_members_extension me ON me.MemberID = m.MemberID
                LEFT JOIN ktv_village v      ON v.VillageID = m.VillageID
                LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
                LEFT JOIN ktv_district d     ON d.DistrictID = sd.DistrictID
                LEFT JOIN ktv_province p     ON p.ProvinceID = d.ProvinceID
                JOIN (
                    SELECT x.MemberID, x.PlotNr, x.SurveyNr, x.Revision, x.Polygon,
                           ROW_NUMBER() OVER (
                               PARTITION BY x.MemberID, x.PlotNr
                               ORDER BY x.SurveyNr DESC, x.Revision DESC
                           ) AS rn
                    FROM ktv_survey_plot_polygon_geo x
                    WHERE x.Polygon IS NOT NULL
                ) geo ON geo.MemberID = a.MemberID AND geo.PlotNr = a.PlotNr AND geo.rn = 1
                WHERE a.StatusCode = 'active'
                  AND a.GardenAreaPolygon > 0
                {$limitSql}";

        $query = $this->db->query($sql, array($partnerId));
        return $query->result_array();
    }
}
