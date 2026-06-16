<?php

/**
 * @Author: nikolius
 * @Date:   2017-09-08 11:18:11
 * @Last Modified by:   nikolius
 * @Last Modified time: 2017-12-13 20:54:25
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Mpro_supplychain_kpi_gar extends CI_Model {

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
        $this->truncateTable('dash_pro_supplychain_kpi_gar');

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
                    INSERT INTO dash_pro_supplychain_kpi_gar
                    SELECT
                        tbl_region.ProvinceID
                        , tbl_region.DistrictID
                        , tbl_region.SubDistrictID
                        , tbl_region.PartnerID
                        , IFNULL(tbl_mill.total_mill,0) AS jml_mill
                        , IFNULL(tbl_core_mill.mill_inti,0) AS jml_inti
                        , IFNULL(tbl_core_mill.garden_inti,0) AS garden_inti
                        , IFNULL(tbl_agent.Plasma,0) AS jml_plasma
                        , IFNULL(tbl_agent.GardenPlasma,0) AS garden_plasma
                        , IFNULL(tbl_agent.External,0) AS jml_external
                        , IFNULL(tbl_agent.GardenExternal,0) AS garden_external
                        , IFNULL(tbl_agent.Agent,0) AS dealer
                        , IFNULL(tbl_agent.GardenAgent,0) AS garden_dealer
                        , IFNULL(tbl_farmer.jml_smallholder,0) AS jml_smallholder
                        , IFNULL(tbl_garden_farmer.garden_smallholder,0) AS garden_smallholder
                        , IFNULL(tbl_garden_farmer.garden_total,0) AS farmer_garden
                        , IFNULL(tbl_farmer.dav,0) AS dav
                        , IFNULL(tbl_garden_farmer.garden_dav,0) + IFNULL(tbl_garden_farmer.garden_dav2,0) AS garden_dav
                        , IFNULL(tbl_farmer.total_farmer,0) AS farmer_registered
                        , IFNULL(tbl_farmer.total_farmer_mapped,0) AS farmer_traceble
                        , IFNULL(tbl_garden_farmer.mapped_garden_ha,0) AS garden_traceble
                        , IFNULL(tbl_farmer.total_farmer_unmapped,0) AS farmer_untraceble
                        , IFNULL(tbl_garden_farmer.unmapped_garden_ha,0) AS garden_untraceble
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
                                , SUM(IF(a.FarmerCategory = 'Unmapped', 1, 0)) AS total_farmer_unmapped
                                , SUM(IF(a.DirectSalesToMill = '0', 1, 0)) dav
                                , SUM(IF(a.DirectSalesToMill = '1', 1, 0)) jml_smallholder
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
                                , SUM(IF(a.PlotNr IS NOT NULL AND a.PlotNr != 0 AND m.DirectSalesToMill = '1', 1, 0)) AS garden_smallholder
                                , SUM(IF(((a.Latitude IS NOT NULL AND a.Longitude IS NOT NULL) OR (a.Latitude != 0 AND a.Longitude != 0)) AND (a.PlotNr IS NOT NULL AND a.PlotNr != 0), 1, 0)) AS mapped_garden_total
                                , IFNULL(SUM(a.GardenAreaHa),0) AS garden_ha
                                , SUM(IF(((a.Latitude IS NOT NULL AND a.Longitude IS NOT NULL) OR (a.Latitude != 0 AND a.Longitude != 0)) AND a.GardenAreaHa IS NOT NULL, a.GardenAreaHa, 0)) AS mapped_garden_ha
                                , SUM(IF(((a.Latitude IS NOT NULL AND a.Longitude IS NOT NULL) OR (a.Latitude != 0 AND a.Longitude != 0)) AND a.GardenAreaHa IS NOT NULL AND m.DirectSalesToMill = '0', a.GardenAreaHa, 0)) AS garden_dav
                                , SUM(IF(((a.Latitude IS NULL AND a.Longitude IS NULL) OR (a.Latitude = 0 AND a.Longitude = 0)) AND a.GardenAreaHa IS NOT NULL, a.GardenAreaHa, 0)) AS unmapped_garden_ha
                                , SUM(IF(((a.Latitude IS NULL AND a.Longitude IS NULL) OR (a.Latitude = 0 AND a.Longitude = 0)) AND a.GardenAreaHa IS NOT NULL AND m.DirectSalesToMill = '0', a.GardenAreaHa, 0)) AS garden_dav2
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
                                , SUM(IF(tgrup_agent.MRoleID = 14,1,0)) AS Plasma
                                , SUM(IF(tgrup_agent.MRoleID = 14,tgrup_agent.GardenAreaHa,0)) AS GardenPlasma
                                , SUM(IF(tgrup_agent.MRoleID = 12,1,0)) AS External
                                , SUM(IF(tgrup_agent.MRoleID = 12,tgrup_agent.GardenAreaHa,0)) AS GardenExternal
                                , SUM(IF(tgrup_agent.MRoleID = 11,1,0)) AS KebunInti
                                , SUM(IF(tgrup_agent.MRoleID = 5 OR tgrup_agent.MRoleID = 7 OR tgrup_agent.MRoleID = 13,1,0)) AS Agent
                                , SUM(IF(tgrup_agent.MRoleID = 5 OR tgrup_agent.MRoleID = 7 OR tgrup_agent.MRoleID = 13,tgrup_agent.GardenAreaHa,0)) AS GardenAgent
                            FROM
                            (
                                SELECT
                                    kv.`SubDistrictID`
                                    , a.`MemberID`
                                    , r.MRoleID
                                    , SUM(spe.GardenAreaHa) GardenAreaHa
                                FROM
                                    ktv_members a
                                    LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                                    JOIN ktv_member_role r ON a.MemberID = r.MemberID AND r.MRoleID IN (5,6,7,8,9,10,12,13,14) #ROLE AGENT
                                    INNER JOIN ktv_access_partner_member acc_pm ON a.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$dataPartner[$j]['PartnerID']}'
                                    LEFT JOIN ktv_survey_plot_sme spe on spe.MemberID = a.MemberID
                                WHERE
                                    a.`StatusCode` = 'active'
                                GROUP BY a.`MemberID`
                            ) AS tgrup_agent
                            WHERE 1=1
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
                        LEFT JOIN (
                            SELECT 
                                kv.SubDistrictID
                                , '{$dataPartner[$j]['PartnerID']}' AS PartnerID
                                , COUNT(cm.PlotNr) mill_inti
                                , SUM(cm.GardenAreaHa) garden_inti
                            FROM ktv_survey_plot_core_mill cm
                            LEFT JOIN ktv_mill m ON m.MillID=cm.MillID
                            INNER JOIN ktv_access_partner_mill acc_pm ON m.MillID = acc_pm.apmiMillID AND acc_pm.apmiPartnerID = '{$dataPartner[$j]['PartnerID']}'
                            LEFT JOIN ktv_village kv ON cm.VillageID=kv.VillageID
                            WHERE 
                                cm.`StatusCode` = 'active'
                                AND m.`StatusCode` = 'active'
                                #AND kv.`SubDistrictID` = '{$dataRegion[$i]['SubDistrictID']}'
                            GROUP BY kv.SubDistrictID
                        ) AS tbl_core_mill
                            ON tbl_region.PartnerID = tbl_core_mill.PartnerID AND tbl_region.SubDistrictID = tbl_core_mill.SubDistrictID
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
        $sqlHakAkses = "";
        $sqlHakAksesForest = "";
        $sqldWherePropinsi = "";
        $sqldWhereRegForest = "";
        if($ProvinceID == "" || $ProvinceID == "all_province"){
            $sqldWherePropinsi = "";
            $sqldWhereRegForest .= "";
        } else {
            $sqldWherePropinsi = " AND a.ProvinceID = '$ProvinceID' ";
            $sqldWhereRegForest .= " AND kd.ProvinceID = '$ProvinceID' ";
        }
        if($DistrictID == "" || $DistrictID == "all_district"){
            $sqldWhereDistrict = "";
            $sqldWhereDistrictTarget = "";
        } else {
            $sqldWhereDistrict = " AND a.DistrictID = '$DistrictID' ";
            $sqldWhereRegForest .= " AND kd.DistrictID = '$DistrictID' ";
        }

        //buat SqlHakAksesKontrol (begin)
        if ($_SESSION['is_admin'] == "1") {
            $sqlHakAkses = " AND a.PartnerID = '1' #Partner Koltiva ";
            $sqlHakAksesForest = " AND acc_pm.apmPartnerID = '1' #Partner Koltiva ";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program") {
            //cek ktv_access_staff
            $sqlHakAkses = " AND a.DistrictID IN (" . $_SESSION['daerah_access'] . ")";
            $sqlHakAkses .= " AND a.PartnerID = '{$_SESSION['PartnerID']}' ";
            $sqlHakAksesForest = " AND kd.DistrictID IN (" . $_SESSION['daerah_access'] . ")";
            $sqlHakAksesForest .= " AND acc_pm.apmPartnerID = '{$_SESSION['PartnerID']}' ";
        } else {
            //cek ktv_access_staff
            $sqlHakAkses = " AND a.DistrictID IN (" . $_SESSION['daerah_access'] . ")";
            $sqlHakAkses .= " AND a.PartnerID = '1' #Partner Koltiva ";
            $sqlHakAksesForest = " AND kd.DistrictID IN (" . $_SESSION['daerah_access'] . ")";
            $sqlHakAksesForest .= " AND acc_pm.apmPartnerID = '1' #Partner Koltiva ";
        }
        //buat SqlHakAksesKontrol (end)

        $sql = "SELECT
                SUM(a.jml_mill) AS jml_mill
                , (
                    SELECT COUNT(DISTINCT(a.ProvinceID))
                    FROM  dash_pro_supplychain_kpi_gar a
                    WHERE a.jml_mill != 0
                    $sqldWherePropinsi
                    $sqldWhereDistrict
                    $sqlHakAkses
                ) mill_prov
                , (
                    SELECT COUNT(DISTINCT(a.DistrictID))
                    FROM  dash_pro_supplychain_kpi_gar a
                    WHERE a.jml_mill != 0
                    $sqldWherePropinsi
                    $sqldWhereDistrict
                    $sqlHakAkses
                ) mill_dis
                , SUM(a.jml_inti) AS jml_inti
                , SUM(a.garden_inti) AS garden_inti
                , SUM(a.jml_plasma) AS jml_plasma
                , SUM(a.garden_plasma) AS garden_plasma
                , SUM(a.jml_external) AS jml_external
                , SUM(a.garden_external) AS garden_external
                , SUM(a.dealer) AS dealer
                , SUM(a.jml_smallholder) AS jml_smallholder
                , SUM(a.garden_smallholder) AS garden_smallholder
                , SUM(a.farmer_garden) AS farmer_garden
                , SUM(a.dav) AS dav
                , SUM(a.garden_dav) AS garden_dav
                , SUM(a.farmer_registered) AS farmer_registered
                , SUM(a.farmer_traceble) AS farmer_traceble
                , SUM(a.garden_traceble) AS garden_traceble
                , SUM(a.farmer_untraceble) AS farmer_untraceble
                , SUM(a.garden_untraceble) AS garden_untraceble
                , DateGenerated
            FROM
                dash_pro_supplychain_kpi_gar a
            WHERE
                1 = 1
                $sqldWherePropinsi
                $sqldWhereDistrict
                $sqlHakAkses
            ";
        $query = $this->db->query($sql, array());
        $result['data'] = $query->row_array();
        
        
        $sql = "SELECT 
                    kv.SubDistrictID,
                    e.AreaCode, 
                    e.AreaName,
                    e.Function,
                    e.AreaCategory,
                    e.OrderNr,
                    COUNT(a.`MemberID`) AS GardenCount,
                    SUM(IFNULL(a.GardenAreaHa,0)) AS AreaHa
                FROM ktv_survey_plot a
                LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                LEFT JOIN ktv_subdistrict ks ON ks.SubDistrictID = kv.SubDistrictID
                LEFT JOIN ktv_district kd ON kd.DistrictID = ks.DistrictID
                LEFT JOIN ktv_members b ON b.MemberID=a.`MemberID`
                LEFT JOIN ktv_member_role c ON c.`MemberID`=b.MemberID
                LEFT JOIN ktv_ref_member_role d ON d.`MRoleID`=c.`MRoleID`
                LEFT JOIN ktv_ref_forest_area e ON e.AreaCode=a.`StatusCheckGPSForestArea`
                LEFT JOIN ktv_access_partner_member acc_pm ON b.MemberID = acc_pm.apmMemberID 
                WHERE
                    b.StatusCode='active' 
                    AND a.`StatusCode`='active' 
                    AND e.StatusCode='active' 
                    AND d.MRoleType='Farmer' 
                    AND a.StatusCheckGPSForestArea IS NOT NULL
                    $sqldWhereRegForest
                    $sqlHakAksesForest
                GROUP BY a.StatusCheckGPSForestArea
                ORDER BY e.OrderNr ASC
            ";
        $query = $this->db->query($sql, array());
        $result['dataForest'] = $query->result_array();

        return $result;
    }

}

?>