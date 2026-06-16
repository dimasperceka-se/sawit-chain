<?php

/**
 * @Author: nikolius
 * @Date:   2017-09-08 11:18:11
 * @Last Modified by:   nikolius
 * @Last Modified time: 2017-12-13 20:54:25
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Mpro_supplychain_kpi_full extends CI_Model {

    public $PartnerIDHirar;

    function __construct() {
        parent::__construct();
    }

    private function truncateTable($namaTabel) {
        $sql = "DELETE FROM `$namaTabel`";
        $query = $this->db->query($sql);
    }

    public function generateDashProSupplyChainKpi() {
        $this->db->trans_begin();

        //truncate dl tabelnya
        $this->truncateTable('dash_pro_supplychain_kpi_full');

        //ambil data village nya yg ada dari semua nilai2 yg dipakai di tabel ini (members dan mill)
        $sql = "SELECT
                tgrup_region.ProvinceID
                , tgrup_region.DistrictID
                , tgrup_region.SubDistrictID
            FROM
            (
            SELECT
                kd.ProvinceID AS ProvinceID
                , kd.DistrictID AS DistrictID
                , ksd.SubDistrictID AS SubDistrictID
            FROM
                ktv_members a
                JOIN ktv_member_role r ON a.MemberID = r.MemberID
                LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`

            WHERE
                a.`StatusCode` = 'active'
                AND a.`VillageID` IS NOT NULL
                AND a.VillageID != ''
                AND a.VillageID != '0'
            GROUP BY ksd.`SubDistrictID`

            UNION

            SELECT
                kd.ProvinceID AS ProvinceID
                , kd.DistrictID AS DistrictID
                , ksd.SubDistrictID AS SubDistrictID
            FROM
                ktv_mill a
                LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
            WHERE
                a.`StatusCode` = 'active'
                AND a.`VillageID` IS NOT NULL
                AND a.VillageID != ''
                AND a.VillageID != '0'
            GROUP BY ksd.`SubDistrictID`
            ) AS tgrup_region
            GROUP BY tgrup_region.SubDistrictID
            ORDER BY tgrup_region.SubDistrictID ASC";
        $query = $this->db->query($sql);
        $dataRegion = $query->result_array();

        //ambil data Partner yg ada
        $sql = "SELECT
                a.`PartnerID`
                , a.`PartnerName`
            FROM
                ktv_program_partner a
            WHERE
                a.`StatusCode` = 'active'
                AND a.IsGenDashboard = 'Yes'
            ORDER BY a.PartnerID ASC
            ";
        $query = $this->db->query($sql);
        $dataPartner = $query->result_array();

        for ($i = 0; $i < count($dataRegion); $i++) {
            for ($j = 0; $j < count($dataPartner); $j++) {

                $sql = "
                    INSERT INTO dash_pro_supplychain_kpi_full
                    SELECT
                        tbl_region.ProvinceID
                        , tbl_region.DistrictID
                        , tbl_region.SubDistrictID
                        , tbl_region.PartnerID
                        , IFNULL(tbl_farmer.total_farmer,0) AS farmer_registered
                        , IFNULL(tbl_farmer.total_farmer_mapped,0) AS farmer_mapped
                        , IFNULL(tbl_farmer.total_consent_agree,0) AS consent_signed
                        , IFNULL(tbl_garden_farmer.mapped_garden_total,0) AS plantation_mapped_farmer
                        , IFNULL(tbl_garden_farmer.garden_total,0) AS plantation_registered_farmer
                        , IFNULL(tbl_garden_farmer.garden_ha,0) AS plant_ha_registered_farmer
                        , IFNULL(tbl_garden_farmer.mapped_garden_ha,0) AS plant_ha_mapped_farmer
                        , IFNULL(tbl_garden_farmer.garden_polygon_total,0) AS plantation_polygon_mapped_farmer
                        , IFNULL(tbl_garden_farmer.garden_polygon_ha,0) AS plant_polygon_ha_mapped_farmer
                        , IFNULL(tbl_garden_sme.mapped_garden_total,0) AS plantation_mapped_sme
                        , IFNULL(tbl_garden_sme.garden_total,0) AS plantation_registered_sme
                        , IFNULL(tbl_garden_sme.garden_ha,0) AS plant_ha_registered_sme
                        , IFNULL(tbl_garden_sme.mapped_garden_ha,0) AS plant_ha_mapped_sme
                        , IFNULL(tbl_garden_sme.garden_polygon_total,0) AS plantation_polygon_mapped_sme
                        , IFNULL(tbl_garden_sme.garden_polygon_ha,0) AS plant_polygon_ha_mapped_sme
                        , IFNULL(tbl_garden_mill.mapped_garden_total,0) AS plantation_mapped_mill
                        , IFNULL(tbl_garden_mill.garden_total,0) AS plantation_registered_mill
                        , IFNULL(tbl_garden_mill.garden_ha,0) AS plant_ha_registered_mill
                        , IFNULL(tbl_garden_mill.mapped_garden_ha,0) AS plant_ha_mapped_mill
                        , IFNULL(tbl_garden_mill.garden_polygon_total,0) AS plantation_polygon_mapped_mill
                        , IFNULL(tbl_garden_mill.garden_polygon_ha,0) AS plant_polygon_ha_mapped_mill
                        , IFNULL(tbl_agent.total_agent,0) AS agents_mapped
                        , IFNULL(tbl_mill.total_mill,0) AS mills_mapped
                        , NOW() AS DateGenerated
                    FROM
                        (
                            SELECT
                                '{$dataRegion[$i]['ProvinceID']}' AS ProvinceID
                                , '{$dataRegion[$i]['DistrictID']}' AS DistrictID
                                , '{$dataRegion[$i]['SubDistrictID']}' AS SubDistrictID
                                , '{$dataPartner[$j]['PartnerID']}' AS PartnerID
                        ) AS tbl_region
                        
                        LEFT JOIN (
                            SELECT
                                kv.`SubDistrictID`
                                , '{$dataPartner[$j]['PartnerID']}' AS PartnerID
                                , COUNT(a.`MemberID`) AS total_farmer
                                , SUM(IF(a.FarmerCategory = 'Mapped', 1, 0)) AS total_farmer_mapped
                                , IFNULL(SUM(IF(a.`LearningContractStatus` = '1',1,0)),0) AS total_consent_agree
                            FROM
                                ktv_members a
                                LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                                JOIN ktv_member_role r ON a.MemberID = r.MemberID AND r.MRoleID = 1 #ROLE PETANI
                                INNER JOIN ktv_access_partner_member acc_pm ON a.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$dataPartner[$j]['PartnerID']}'
                                /*LEFT JOIN (
                                        SELECT g.MemberID,
                                                g.PlotNr
                                        FROM ktv_survey_plot g
                                        WHERE (g.Latitude IS NOT NULL AND g.Longitude IS NOT NULL) AND (g.Latitude != 0 AND g.Longitude != 0)
                                        GROUP BY g.MemberID
                                ) gar ON gar.MemberID=a.MemberID*/
                            WHERE
                                a.`StatusCode` = 'active'
                                AND kv.`SubDistrictID` = '{$dataRegion[$i]['SubDistrictID']}'
                            GROUP BY kv.`SubDistrictID`
                        ) AS tbl_farmer
                            ON tbl_region.PartnerID = tbl_farmer.PartnerID AND tbl_region.SubDistrictID = tbl_farmer.SubDistrictID
                        
                        LEFT JOIN (
                            SELECT
                                kv.`SubDistrictID`
                                , '{$dataPartner[$j]['PartnerID']}' AS PartnerID
                                , SUM(IF(a.PlotNr IS NOT NULL AND a.PlotNr != 0, 1, 0)) AS garden_total
                                , SUM(IF(((a.Latitude IS NOT NULL AND a.Longitude IS NOT NULL) OR (a.Latitude != 0 AND a.Longitude != 0)) AND (a.PlotNr IS NOT NULL AND a.PlotNr != 0), 1, 0)) AS mapped_garden_total
                                , IFNULL(SUM(a.GardenAreaHa),0) AS garden_ha
                                , SUM(IF(((a.Latitude IS NOT NULL AND a.Longitude IS NOT NULL) OR (a.Latitude != 0 AND a.Longitude != 0)) AND a.GardenAreaHa IS NOT NULL, a.GardenAreaHa, 0)) AS mapped_garden_ha
                                , IFNULL(SUM(IF(a.`GardenAreaPolygon` > 0,1,0)),0) AS garden_polygon_total
                                , IFNULL(SUM(a.GardenAreaPolygon),0) AS garden_polygon_ha
                            FROM
                                ktv_survey_plot a
                                JOIN (SELECT
                                    p.MemberID, p.PlotNr, MAX(p.SurveyNr) AS SurveyNr
                                FROM ktv_survey_plot p WHERE p.`StatusCode` = 'active'
                                GROUP BY p.MemberID, p.PlotNr) AS gar_latest
                                    ON a.MemberID = gar_latest.MemberID AND a.PlotNr = gar_latest.PlotNr AND a.SurveyNr = gar_latest.SurveyNr
                                JOIN ktv_members m ON m.MemberID = a.MemberID
                                LEFT JOIN ktv_village kv ON kv.VillageID = m.VillageID
                                INNER JOIN ktv_access_partner_member acc_pm ON m.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$dataPartner[$j]['PartnerID']}'
                            WHERE
                                a.`StatusCode` = 'active'
                                AND m.`StatusCode` = 'active'
                                AND kv.`SubDistrictID` = '{$dataRegion[$i]['SubDistrictID']}'
                            GROUP BY kv.`SubDistrictID`
                        ) AS tbl_garden_farmer
                            ON tbl_region.PartnerID = tbl_garden_farmer.PartnerID AND tbl_region.SubDistrictID = tbl_garden_farmer.SubDistrictID
                        
                        LEFT JOIN (
                            SELECT
                                kv.`SubDistrictID`
                                , '{$dataPartner[$j]['PartnerID']}' AS PartnerID
                                , SUM(IF(a.PlotNr IS NOT NULL AND a.PlotNr != 0, 1, 0)) AS garden_total
                                , SUM(IF(((a.Latitude IS NOT NULL AND a.Longitude IS NOT NULL) OR (a.Latitude != 0 AND a.Longitude != 0)) AND (a.PlotNr IS NOT NULL AND a.PlotNr != 0), 1, 0)) AS mapped_garden_total
                                , IFNULL(SUM(a.GardenAreaHa),0) AS garden_ha
                                , SUM(IF(((a.Latitude IS NOT NULL AND a.Longitude IS NOT NULL) OR (a.Latitude != 0 AND a.Longitude != 0)) AND a.GardenAreaHa IS NOT NULL, a.GardenAreaHa, 0)) AS mapped_garden_ha
                                , IFNULL(SUM(IF(a.`GardenAreaPolygon` > 0,1,0)),0) AS garden_polygon_total
                                , IFNULL(SUM(a.GardenAreaPolygon),0) AS garden_polygon_ha
                            FROM
                                ktv_survey_plot_sme a
                                JOIN (SELECT
                                    p.MemberID, p.PlotNr, MAX(p.SurveyNr) AS SurveyNr
                                FROM ktv_survey_plot_sme p WHERE p.`StatusCode` = 'active'
                                GROUP BY p.MemberID, p.PlotNr) AS gar_latest
                                    ON a.MemberID = gar_latest.MemberID AND a.PlotNr = gar_latest.PlotNr AND a.SurveyNr = gar_latest.SurveyNr
                                JOIN ktv_members m ON m.MemberID = a.MemberID
                                LEFT JOIN ktv_village kv ON kv.VillageID = m.VillageID
                                INNER JOIN ktv_access_partner_member acc_pm ON m.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$dataPartner[$j]['PartnerID']}'
                            WHERE
                                a.`StatusCode` = 'active'
                                AND m.`StatusCode` = 'active'
                                AND kv.`SubDistrictID` = '{$dataRegion[$i]['SubDistrictID']}'
                            GROUP BY kv.`SubDistrictID`
                        ) AS tbl_garden_sme
                            ON tbl_region.PartnerID = tbl_garden_farmer.PartnerID AND tbl_region.SubDistrictID = tbl_garden_farmer.SubDistrictID
                        
                        LEFT JOIN (
                            SELECT
                                kv.`SubDistrictID`
                                , '{$dataPartner[$j]['PartnerID']}' AS PartnerID
                                , SUM(IF(a.PlotNr IS NOT NULL AND a.PlotNr != 0, 1, 0)) AS garden_total
                                , SUM(IF(((a.Latitude IS NOT NULL AND a.Longitude IS NOT NULL) OR (a.Latitude != 0 AND a.Longitude != 0)) AND (a.PlotNr IS NOT NULL AND a.PlotNr != 0), 1, 0)) AS mapped_garden_total
                                , IFNULL(SUM(a.GardenAreaHa),0) AS garden_ha
                                , SUM(IF(((a.Latitude IS NOT NULL AND a.Longitude IS NOT NULL) OR (a.Latitude != 0 AND a.Longitude != 0)) AND a.GardenAreaHa IS NOT NULL, a.GardenAreaHa, 0)) AS mapped_garden_ha
                                , IFNULL(SUM(IF(a.`GardenAreaPolygon` > 0,1,0)),0) AS garden_polygon_total
                                , IFNULL(SUM(a.GardenAreaPolygon),0) AS garden_polygon_ha
                            FROM
                                ktv_survey_plot_status_mill a
                            JOIN ktv_mill m ON m.MillID = a.MillID
                            LEFT JOIN ktv_village kv ON kv.VillageID = m.VillageID
                            INNER JOIN ktv_access_partner_mill acc_pm ON m.MillID = acc_pm.apmiMillID AND acc_pm.apmiPartnerID = '{$dataPartner[$j]['PartnerID']}'
                            WHERE
                                a.`StatusCode` = 'active' 
                                AND m.`StatusCode` = 'active' 
                                AND kv.`SubDistrictID` = '{$dataRegion[$i]['SubDistrictID']}'
                            GROUP BY kv.`SubDistrictID`
                        ) AS tbl_garden_mill
                            ON tbl_region.PartnerID = tbl_garden_farmer.PartnerID AND tbl_region.SubDistrictID = tbl_garden_farmer.SubDistrictID
                        
                        LEFT JOIN (
                            SELECT
                                tgrup_agent.SubDistrictID
                                , '{$dataPartner[$j]['PartnerID']}' AS PartnerID
                                , COUNT(tgrup_agent.MemberID) AS total_agent
                            FROM
                            (
                                SELECT
                                    kv.`SubDistrictID`
                                    , a.`MemberID`
                                FROM
                                    ktv_members a
                                    LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                                    JOIN ktv_member_role r ON a.MemberID = r.MemberID AND r.MRoleID IN (5,6,7,8,9,10,12,13,14) #ROLE AGENT
                                    INNER JOIN ktv_access_partner_member acc_pm ON a.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$dataPartner[$j]['PartnerID']}'
                                WHERE
                                    a.`StatusCode` = 'active'
                                GROUP BY a.`MemberID`
                            ) AS tgrup_agent
                            WHERE
                                tgrup_agent.SubDistrictID = '{$dataRegion[$i]['SubDistrictID']}'
                            GROUP BY tgrup_agent.SubDistrictID
                        ) AS tbl_agent
                            ON tbl_region.PartnerID = tbl_agent.PartnerID AND tbl_region.SubDistrictID = tbl_agent.SubDistrictID
                        
                        LEFT JOIN (
                            SELECT
                                kv.`SubDistrictID`
                                , '{$dataPartner[$j]['PartnerID']}' AS PartnerID
                                , COUNT(a.`MillID`) AS total_mill
                            FROM
                                ktv_mill a
                                LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                                INNER JOIN ktv_access_partner_mill acc_pml ON a.`MillID` = acc_pml.`apmiMillID` AND acc_pml.`apmiPartnerID` = '{$dataPartner[$j]['PartnerID']}'
                            WHERE
                                a.`StatusCode` = 'active'
                                AND kv.`SubDistrictID` = '{$dataRegion[$i]['SubDistrictID']}'
                                AND a.`NDAAgree`=1
                            GROUP BY kv.`SubDistrictID`
                        ) AS tbl_mill
                            ON tbl_region.PartnerID = tbl_mill.PartnerID AND tbl_region.SubDistrictID = tbl_mill.SubDistrictID
                ";
                $query = $this->db->query($sql);
//                echo "<pre>";
//                print_r($this->db->last_query());
//                echo "</pre>";
            }
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Success";
        }
        return $results;
    }

    public function getDisplaySupplyChainKpi($ProvinceID, $DistrictID) {
        //data display langsung ================================================== (begin)
        if($ProvinceID == "" || $ProvinceID == "all_province"){
            $sqldWherePropinsi = "";
            $sqldWherePropinsiTarget = "";
        } else {
            $sqldWherePropinsi = " AND kd.ProvinceID = '$ProvinceID' ";
            $sqldWherePropinsiTarget = " AND SUBSTR(a.DistrictID,1,2) = '$ProvinceID' ";
        }
        if($DistrictID == "" || $DistrictID == "all_district"){
            $sqldWhereDistrict = "";
            $sqldWhereDistrictTarget = "";
        } else {
            $sqldWhereDistrict = " AND kd.DistrictID = '$DistrictID' ";
            $sqldWhereDistrictTarget = " AND SUBSTR(a.DistrictID,1,4) = '$DistrictID' ";
        }

        //buat SqlHakAksesKontrol (begin)
        if ($_SESSION['is_admin'] == "1") {
            $sqlHakAkses = " AND a.PartnerID = '1' #Partner Koltiva ";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program") {
            //cek ktv_access_staff
            $sqlHakAkses = " AND kd.DistrictID IN (" . $_SESSION['daerah_access'] . ")";
            $sqlHakAkses .= " AND a.PartnerID = '{$_SESSION['PartnerID']}' ";
        } else {
            //cek ktv_access_staff
            $sqlHakAkses = " AND kd.DistrictID IN (" . $_SESSION['daerah_access'] . ")";
            $sqlHakAkses .= " AND a.PartnerID = '1' #Partner Koltiva ";
        }
        //buat SqlHakAksesKontrol (end)

        $sql = "SELECT
                SUM(a.farmer_registered) AS farmer_registered
                , SUM(a.farmer_mapped) AS farmer_mapped
                , SUM(a.consent_signed) AS consent_signed
                , SUM(a.plantation_mapped_farmer) AS plantation_mapped_farmer
                , SUM(a.plantation_registered_farmer) AS plantation_registered_farmer
                , SUM(a.plant_ha_registered_farmer) AS plant_ha_registered_farmer
                , SUM(a.plant_ha_mapped_farmer) AS plant_ha_mapped_farmer
                , SUM(a.plantation_polygon_mapped_farmer) AS plantation_polygon_mapped_farmer
                , SUM(a.plant_polygon_ha_mapped_farmer) AS plant_polygon_ha_mapped_farmer
                , SUM(a.plantation_mapped_sme) AS plantation_mapped_sme
                , SUM(a.plantation_registered_sme) AS plantation_registered_sme
                , SUM(a.plant_ha_registered_sme) AS plant_ha_registered_sme
                , SUM(a.plant_ha_mapped_sme) AS plant_ha_mapped_sme
                , SUM(a.plantation_polygon_mapped_sme) AS plantation_polygon_mapped_sme
                , SUM(a.plant_polygon_ha_mapped_sme) AS plant_polygon_ha_mapped_sme
                , SUM(a.plantation_mapped_mill) AS plantation_mapped_mill
                , SUM(a.plantation_registered_mill) AS plantation_registered_mill
                , SUM(a.plant_ha_registered_mill) AS plant_ha_registered_mill
                , SUM(a.plant_ha_mapped_mill) AS plant_ha_mapped_mill
                , SUM(a.plantation_polygon_mapped_mill) AS plantation_polygon_mapped_mill
                , SUM(a.plant_polygon_ha_mapped_mill) AS plant_polygon_ha_mapped_mill
                , SUM(a.agents_mapped) AS agents_mapped
                , SUM(a.mills_mapped) AS mills_mapped
                , DateGenerated
            FROM
                dash_pro_supplychain_kpi_full a
                LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = a.SubDistrictID
                LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
            WHERE
                1 = 1
                $sqldWherePropinsi
                $sqldWhereDistrict
                $sqlHakAkses
            ";
        $query = $this->db->query($sql, array());
        $result['dataDisplay'] = $query->row_array();

        //data display langsung ================================================== (end)
        //data target kpi ==================================================== (begin)

        //buat SqlHakAksesKontrol (begin)
        if ($_SESSION['is_admin'] == "1") {
            $sqlHakAksesTarget = " AND a.PartnerID = '1' #Partner Koltiva ";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program") {
            //cek ktv_access_staff
            $sqlHakAksesTarget = " AND SUBSTR(a.DistrictID,1,4) IN (" . $_SESSION['daerah_access'] . ")";
            $sqlHakAksesTarget .= " AND a.PartnerID = '{$_SESSION['PartnerID']}' ";
        } else {
            //cek ktv_access_staff
            $sqlHakAksesTarget = " AND SUBSTR(a.DistrictID,1,4) IN (" . $_SESSION['daerah_access'] . ")";
            $sqlHakAksesTarget .= " AND a.PartnerID = '1' #Partner Koltiva ";
        }
        //buat SqlHakAksesKontrol (end)

        $sql = "
            SELECT
                SUM(a.farmer_registered) AS farmer_registered
                , SUM(a.farmer_mapped) AS farmer_mapped
                , SUM(a.farmer_sales) AS farmer_sales
                , SUM(a.plantation_registered) AS plantation_registered
                , SUM(a.plantation_mapped) AS plantation_mapped
                , SUM(a.plant_ha_registered) AS plant_ha_registered
                , SUM(a.plant_ha_mapped) AS plant_ha_mapped
                , SUM(a.plantation_polygon_mapped) AS plantation_polygon_mapped
                , SUM(a.plant_polygon_ha_mapped) AS plant_polygon_ha_mapped
                , SUM(a.consent_signed) AS consent_signed
                , SUM(a.mills_mapped) AS mills_mapped
                , SUM(a.agents_mapped) AS agents_mapped
            FROM
                dash_pro_supplychain_kpi_target a
            WHERE
                1 = 1
                $sqldWherePropinsiTarget
                $sqldWhereDistrictTarget
                $sqlHakAksesTarget
        ";
        $query = $this->db->query($sql, array());
        $result['dataTarget'] = $query->row_array();

        return $result;
    }

}

?>