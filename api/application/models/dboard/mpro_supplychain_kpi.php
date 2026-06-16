<?php

/**
 * @Author: nikolius
 * @Date:   2017-09-08 11:18:11
 * @Last Modified by:   nikolius
 * @Last Modified time: 2017-12-13 20:54:25
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mpro_supplychain_kpi extends CI_Model {

    public $PartnerIDHirar;

    function __construct() {
        parent::__construct();

    }

    private function truncateTable($namaTabel){
        $sql="DELETE FROM `$namaTabel`";
        $query = $this->db->query($sql);
    }

    public function generateDashProSupplyChainKpi(){
        $this->db->trans_begin();

        //truncate dl tabelnya
        $this->truncateTable('dash_pro_supplychain_kpi');

        //ambil data village nya yg ada dari semua nilai2 yg dipakai di tabel ini (members dan mill)
        $sql="SELECT
                tgrup_region.ProvinceID
                , tgrup_region.DistrictID
                , tgrup_region.SubDistrictID
                , tgrup_region.VillageID
            FROM
            (
            SELECT
                kd.ProvinceID AS ProvinceID
                , kd.DistrictID AS DistrictID
                , ksd.SubDistrictID AS SubDistrictID
                , a.`VillageID`
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
            GROUP BY a.`VillageID`

            UNION

            SELECT
                kd.ProvinceID AS ProvinceID
                , kd.DistrictID AS DistrictID
                , ksd.SubDistrictID AS SubDistrictID
                , a.`VillageID`
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
            GROUP BY a.`VillageID`
            ) AS tgrup_region
            GROUP BY tgrup_region.VillageID
            ORDER BY tgrup_region.VillageID ASC";
        $query = $this->db->query($sql);
        $dataRegion = $query->result_array();

        //ambil data Partner yg ada
        $sql="SELECT
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

        for ($i=0; $i < count($dataRegion); $i++) {
            for ($j=0; $j < count($dataPartner); $j++) {

                $sql="
                    INSERT INTO dash_pro_supplychain_kpi
                    SELECT
                        tbl_region.ProvinceID
                        , tbl_region.DistrictID
                        , tbl_region.SubDistrictID
                        , tbl_region.VillageID
                        , tbl_region.PartnerID
                        , IFNULL(tbl_farmer.total_farmer,0) AS farmer_registered
                        , IFNULL(tbl_farmer_sales.total_farmer_sales,0) AS farmer_sales
                        , IFNULL(tbl_garden.garden_total,0) AS plantation_mapped
                        , IFNULL(tbl_garden.garden_ha,0) AS plant_ha_mapped
                        , IFNULL(tbl_garden.garden_polygon_total,0) AS plantation_polygon_mapped
                        , IFNULL(tbl_garden.garden_polygon_ha,0) AS plant_polygon_ha_mapped
                        , IFNULL(tbl_farmer.total_consent_agree,0) AS consent_signed
                        , IFNULL(tbl_mill.total_mill,0) AS mills_mapped
                        , IFNULL(tbl_agent.total_agent,0) AS agents_mapped
                        , NOW() AS DateGenerated
                    FROM
                        (
                            SELECT
                                '{$dataRegion[$i]['ProvinceID']}' AS ProvinceID
                                , '{$dataRegion[$i]['DistrictID']}' AS DistrictID
                                , '{$dataRegion[$i]['SubDistrictID']}' AS SubDistrictID
                                , '{$dataRegion[$i]['VillageID']}' AS VillageID
                                , '{$dataPartner[$j]['PartnerID']}' AS PartnerID
                        ) AS tbl_region

                        LEFT JOIN (SELECT
                            a.`VillageID`
                            , '{$dataPartner[$j]['PartnerID']}' AS PartnerID
                            , COUNT(a.`MemberID`) AS total_farmer
                            , IFNULL(SUM(IF(a.`LearningContractStatus` = '1',1,0)),0) AS total_consent_agree
                        FROM
                            ktv_members a
                            JOIN ktv_member_role r ON a.MemberID = r.MemberID AND r.MRoleID = 1 #ROLE PETANI
                            INNER JOIN ktv_access_partner_member acc_pm ON a.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$dataPartner[$j]['PartnerID']}'
                        WHERE
                            a.`StatusCode` = 'active'
                            AND a.`VillageID` = '{$dataRegion[$i]['VillageID']}'
                        GROUP BY a.`VillageID`
                        ) AS tbl_farmer
                            ON tbl_region.PartnerID = tbl_farmer.PartnerID AND tbl_region.VillageID = tbl_farmer.VillageID
                        LEFT JOIN (
                            SELECT 
                            b.VillageID
                            ,'{$dataPartner[$j]['PartnerID']}' AS PartnerID
                            ,SUM(a.`VolumeBruto`) AS total_kg
                            ,COUNT(a.`SupplyTransID`) AS total_trans
                            ,COUNT(DISTINCT a.`SupplyID`) AS total_farmer_sales
                            FROM `ktv_tc_supplychain_transaction` a 
                            JOIN ktv_members b ON b.MemberID=a.SupplyID
                            JOIN ktv_member_role r ON r.MemberID = b.MemberID AND r.MRoleID = 1 #ROLE PETANI
                            INNER JOIN ktv_access_partner_member acc_pm ON b.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$dataPartner[$j]['PartnerID']}'
                            WHERE
                            b.`StatusCode`='active'
                            AND b.`VillageID` = '{$dataRegion[$i]['VillageID']}'
                            GROUP BY b.`VillageID`
                        ) AS tbl_farmer_sales
                            ON tbl_region.PartnerID = tbl_farmer_sales.PartnerID AND tbl_region.VillageID = tbl_farmer_sales.VillageID
                        LEFT JOIN (
                            SELECT
                                tgrup_agent.VillageID
                                , '{$dataPartner[$j]['PartnerID']}' AS PartnerID
                                , COUNT(tgrup_agent.MemberID) AS total_agent
                            FROM
                            (
                                SELECT
                                    a.`VillageID`
                                    , a.`MemberID`
                                FROM
                                    ktv_members a
                                    JOIN ktv_member_role r ON a.MemberID = r.MemberID AND r.MRoleID IN (5,6,7,8,9,10,12,13,14) #ROLE AGENT
                                    INNER JOIN ktv_access_partner_member acc_pm ON a.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$dataPartner[$j]['PartnerID']}'
                                WHERE
                                    a.`StatusCode` = 'active'
                                GROUP BY a.`MemberID`
                            ) AS tgrup_agent
                            WHERE
                                tgrup_agent.VillageID = '{$dataRegion[$i]['VillageID']}'
                            GROUP BY tgrup_agent.VillageID
                        ) AS tbl_agent
                            ON tbl_region.PartnerID = tbl_agent.PartnerID AND tbl_region.VillageID = tbl_agent.VillageID

                        LEFT JOIN (
                            SELECT
                                kv.`VillageID`
                                , '{$dataPartner[$j]['PartnerID']}' AS PartnerID
                                , SUM(IF(a.PlotNr IS NOT NULL AND a.PlotNr != 0, 1, 0)) AS garden_total
                                , IFNULL(SUM(a.GardenAreaHa),0) AS garden_ha
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
                                AND kv.`VillageID` = '{$dataRegion[$i]['VillageID']}'
                            GROUP BY kv.`VillageID`
                        ) AS tbl_garden
                            ON tbl_region.PartnerID = tbl_garden.PartnerID AND tbl_region.VillageID = tbl_garden.VillageID

                        LEFT JOIN (
                            SELECT
                                a.`VillageID`
                                , '{$dataPartner[$j]['PartnerID']}' AS PartnerID
                                , COUNT(a.`MillID`) AS total_mill
                            FROM
                                ktv_mill a
                                INNER JOIN ktv_access_partner_mill acc_pml ON a.`MillID` = acc_pml.`apmiMillID` AND acc_pml.`apmiPartnerID` = '{$dataPartner[$j]['PartnerID']}'
                            WHERE
                                a.`StatusCode` = 'active'
                                AND a.`VillageID` = '{$dataRegion[$i]['VillageID']}'
                                AND a.`NDAAgree`=1
                            GROUP BY a.`VillageID`
                        ) AS tbl_mill
                            ON tbl_region.PartnerID = tbl_mill.PartnerID AND tbl_region.VillageID = tbl_mill.VillageID
                ";
                $query = $this->db->query($sql);
                // echo "<pre>";
                // print_r($this->db->last_query());
                // echo "</pre>";

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

    public function getDisplaySupplyChainKpi($ProvinceID,$DistrictID){
        //data display langsung ================================================== (begin)
        if($ProvinceID != ""){
            $sqldWherePropinsi = " AND kd.ProvinceID = '$ProvinceID' ";
        }else{
            $sqldWherePropinsi = "";
        }

        if($DistrictID != ""){
            $sqldWhereDistrict = " AND kd.DistrictID = '$DistrictID' ";
        }else{
            $sqldWhereDistrict = "";
        }

        //buat SqlHakAksesKontrol (begin)
        if($_SESSION['is_admin'] == "1"){
            $sqlHakAkses = " AND a.PartnerID = '1' #Partner Koltiva ";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            //cek ktv_access_staff
            $sqlHakAkses = " AND kd.DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlHakAkses .= " AND a.PartnerID = '{$_SESSION['PartnerID']}' ";
        } else {
            //cek ktv_access_staff
            $sqlHakAkses = " AND kd.DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlHakAkses .= " AND a.PartnerID = '1' #Partner Koltiva ";
        }
        //buat SqlHakAksesKontrol (end)

        $sql="SELECT
                SUM(a.farmer_registered) AS farmer_registered
                , SUM(a.farmer_sales) AS farmer_sales
                , SUM(a.plantation_mapped) AS plantation_mapped
                , SUM(a.plant_ha_mapped) AS plant_ha_mapped
                , SUM(a.plantation_polygon_mapped) AS plantation_polygon_mapped
                , SUM(a.plant_polygon_ha_mapped) AS plant_polygon_ha_mapped
                , SUM(a.consent_signed) AS consent_signed
                , SUM(a.mills_mapped) AS mills_mapped
                , SUM(a.agents_mapped) AS agents_mapped
                , DateGenerated
            FROM
                dash_pro_supplychain_kpi a
                LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
                LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
            WHERE
                1 = 1
                $sqldWherePropinsi
                $sqldWhereDistrict
                $sqlHakAkses
            ";
        $query = $this->db->query($sql,array());
        $result['dataDisplay'] = $query->row_array();
        // echo "<pre>";
        // print_r($this->db->last_query());
        // // echo "</pre>";
        // die;

        //===== fof Kosong =======
        // $result['dataDisplay']['farmer_registered'] = 23880;
        // $result['dataDisplay']['plantation_mapped'] = 19428;
        // $result['dataDisplay']['plant_ha_mapped'] = 38771;
        // $result['dataDisplay']['plantation_polygon_mapped'] = 0;
        // $result['dataDisplay']['plant_polygon_ha_mapped'] = 0;
        // $result['dataDisplay']['farmer_sales'] = 0;
        // $result['dataDisplay']['mills_mapped'] = 81;
        // $result['dataDisplay']['agents_mapped'] = 132;
        // $result['dataDisplay']['DateGenerated'] = date('Y-m-d H:i:s');
        //===== eof Kosong =======

        //===== fof All Province =======
        // $result['dataDisplay']['farmer_registered'] = 20977;
        // $result['dataDisplay']['plantation_mapped'] = 15695;
        // $result['dataDisplay']['plant_ha_mapped'] = 29264;
        // $result['dataDisplay']['plantation_polygon_mapped'] = 0;
        // $result['dataDisplay']['plant_polygon_ha_mapped'] = 0;
        // $result['dataDisplay']['farmer_sales'] = 0;
        // $result['dataDisplay']['mills_mapped'] = 130;
        // $result['dataDisplay']['agents_mapped'] = 93;
        // $result['dataDisplay']['DateGenerated'] = date('Y-m-d H:i:s');
        //===== eof All Province =======

        //===== fof indragiri hulu =======
        // $result['dataDisplay']['farmer_registered'] = 50;
        // $result['dataDisplay']['plantation_mapped'] = 63;
        // $result['dataDisplay']['plant_ha_mapped'] = 147;
        // $result['dataDisplay']['plantation_polygon_mapped'] = 0;
        // $result['dataDisplay']['plant_polygon_ha_mapped'] = 0;
        // $result['dataDisplay']['farmer_sales'] = 0;
        // $result['dataDisplay']['mills_mapped'] = 6;
        // $result['dataDisplay']['agents_mapped'] = 0;
        // $result['dataDisplay']['DateGenerated'] = date('Y-m-d H:i:s');
        // //===== eof indragiri hulu =======

        //===== fof indragiri hilir =======
        // $result['dataDisplay']['farmer_registered'] = 636;
        // $result['dataDisplay']['plantation_mapped'] = 1031;
        // $result['dataDisplay']['plant_ha_mapped'] = 1759;
        // $result['dataDisplay']['plantation_polygon_mapped'] = 0;
        // $result['dataDisplay']['plant_polygon_ha_mapped'] = 0;
        // $result['dataDisplay']['farmer_sales'] = 0;
        // $result['dataDisplay']['mills_mapped'] = 3;
        // $result['dataDisplay']['agents_mapped'] = 3;
        // $result['dataDisplay']['DateGenerated'] = date('Y-m-d H:i:s');
        //===== eof indragiri hilir =======

        //data display langsung ================================================== (end)

        //data target kpi ==================================================== (begin)
        if($ProvinceID != ""){
            $sqldWherePropinsiTarget = " AND SUBSTR(a.DistrictID,1,2) = '$ProvinceID' ";
        }else{
            $sqldWherePropinsiTarget = "";
        }

        if($DistrictID != ""){
            $sqldWhereDistrictTarget = " AND SUBSTR(a.DistrictID,1,4) = '$DistrictID' ";
        }else{
            $sqldWhereDistrictTarget = "";
        }

        //buat SqlHakAksesKontrol (begin)
        if($_SESSION['is_admin'] == "1"){
            $sqlHakAksesTarget = " AND a.PartnerID = '1' #Partner Koltiva ";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            //cek ktv_access_staff
            $sqlHakAksesTarget = " AND SUBSTR(a.DistrictID,1,4) IN (".$_SESSION['daerah_access'].")";
            $sqlHakAksesTarget .= " AND a.PartnerID = '{$_SESSION['PartnerID']}' ";
        } else {
            //cek ktv_access_staff
            $sqlHakAksesTarget = " AND SUBSTR(a.DistrictID,1,4) IN (".$_SESSION['daerah_access'].")";
            $sqlHakAksesTarget .= " AND a.PartnerID = '1' #Partner Koltiva ";
        }
        //buat SqlHakAksesKontrol (end)

        $sql="
            SELECT
                SUM(a.farmer_registered) AS farmer_registered
                , SUM(a.farmer_sales) AS farmer_sales
                , SUM(a.plantation_mapped) AS plantation_mapped
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
        $query = $this->db->query($sql,array());
        $result['dataTarget'] = $query->row_array();

        //===== fof Kosong =======
        // $result['dataTarget']['farmer_registered'] = 61421;
        // $result['dataTarget']['plantation_mapped'] = 92142;
        // $result['dataTarget']['plant_ha_mapped'] = 276426;
        // $result['dataTarget']['plantation_polygon_mapped'] = 92142;
        // $result['dataTarget']['plant_polygon_ha_mapped'] = 276426;
        // $result['dataTarget']['farmer_sales'] = 61421;
        // $result['dataTarget']['mills_mapped'] = 140;
        // $result['dataTarget']['agents_mapped'] = 1994;
        //===== eof Kosong =======

        // $result['dataTarget']['farmer_registered'] = 61421;
        // $result['dataTarget']['plantation_mapped'] = 92142;
        // $result['dataTarget']['plant_ha_mapped'] = 276426;
        // $result['dataTarget']['plantation_polygon_mapped'] = 92142;
        // $result['dataTarget']['plant_polygon_ha_mapped'] = 276426;
        // $result['dataTarget']['farmer_sales'] = 61421;
        // $result['dataTarget']['mills_mapped'] = 140;
        // $result['dataTarget']['agents_mapped'] = 1994;
        // //===== fof indragiri hulu =======
        // $result['dataTarget']['farmer_registered'] = 2964;
        // $result['dataTarget']['plantation_mapped'] = 446;
        // $result['dataTarget']['plant_ha_mapped'] = 13338;
        // $result['dataTarget']['plantation_polygon_mapped'] = 92142;
        // $result['dataTarget']['plant_polygon_ha_mapped'] = 276426;
        // $result['dataTarget']['farmer_sales'] = 2964;
        // $result['dataTarget']['mills_mapped'] = 5;
        // $result['dataTarget']['agents_mapped'] = 80;
        // //===== eof indragiri hulu =======

        //===== fof indragiri hilir =======
        // $result['dataTarget']['farmer_registered'] = 1482;
        // $result['dataTarget']['plantation_mapped'] = 2223;
        // $result['dataTarget']['plant_ha_mapped'] = 6669;
        // $result['dataTarget']['plantation_polygon_mapped'] = 92142;
        // $result['dataTarget']['plant_polygon_ha_mapped'] = 276426;
        // $result['dataTarget']['farmer_sales'] = 1482;
        // $result['dataTarget']['mills_mapped'] = 3;
        // $result['dataTarget']['agents_mapped'] = 50;
        //===== eof indragiri hilir =======
        //data target kpi ==================================================== (end)

        return $result;
    }

    public function getMillGroup() {
        $sql = "SELECT
            a.MillGroupID as id,
            a.GroupName as `name`
        FROM
            ktv_mill_group AS a
            INNER JOIN ktv_mill AS b ON b.MillGroupID = a.MillGroupID
            INNER JOIN ktv_access_partner_mill AS c ON c.apmiMillID = b.MillID 
        WHERE
            c.apmiPartnerID = ?
            AND a.StatusCode = 'active'
        Group By a.MillGroupID";
        $query = $this->db->query($sql, array($_SESSION['PartnerID']));
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function getMill($mgID) {
        $sql = "SELECT
            b.MillID as id,
            b.MillName as `name`
        FROM
            ktv_mill_group AS a
            INNER JOIN ktv_mill AS b ON b.MillGroupID = a.MillGroupID
            INNER JOIN ktv_access_partner_mill AS c ON c.apmiMillID = b.MillID 
        WHERE
            c.apmiPartnerID = ?
            AND a.MillGroupID = ?
            AND a.StatusCode = 'active'";
        $query = $this->db->query($sql, array($_SESSION['PartnerID'], $mgID));
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }
    
    public function generateDashProSupplyChainMillKpi(){
        $this->db->trans_begin();

        //truncate dl tabelnya
        $this->truncateTable('dash_pro_supplychain_mill_kpi');

        //ambil data village nya yg ada dari semua nilai2 yg dipakai di tabel ini (members dan mill)
        $sql="SELECT
                tgrup_region.ProvinceID
                , tgrup_region.DistrictID
                , tgrup_region.SubDistrictID
                , tgrup_region.VillageID
            FROM
            (
            SELECT
                kd.ProvinceID AS ProvinceID
                , kd.DistrictID AS DistrictID
                , ksd.SubDistrictID AS SubDistrictID
                , a.`VillageID`
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
            GROUP BY a.`VillageID`

            UNION

            SELECT
                kd.ProvinceID AS ProvinceID
                , kd.DistrictID AS DistrictID
                , ksd.SubDistrictID AS SubDistrictID
                , a.`VillageID`
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
            GROUP BY a.`VillageID`
            ) AS tgrup_region
            GROUP BY tgrup_region.VillageID
            ORDER BY tgrup_region.VillageID ASC";
        $query = $this->db->query($sql);
        $dataRegion = $query->result_array();

        //ambil data Partner yg ada
        $sql="SELECT
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

        for ($i=0; $i < count($dataRegion); $i++) {
            for ($j=0; $j < count($dataPartner); $j++) {

                $sql="
                    INSERT INTO dash_pro_supplychain_mill_kpi
                    SELECT
                        tbl_region.ProvinceID
                        , tbl_region.DistrictID
                        , tbl_region.SubDistrictID
                        , tbl_region.VillageID
                        , tbl_region.PartnerID
                        , IFNULL(tbl_farmer.total_farmer,0) AS farmer_registered
                        , IFNULL(tbl_farmer.total_farmer,0) AS farmer_sales
                        , IFNULL(tbl_garden.garden_total,0) AS plantation_mapped
                        , IFNULL(tbl_garden.garden_ha,0) AS plant_ha_mapped
                        , IFNULL(tbl_garden.garden_polygon_total,0) AS plantation_polygon_mapped
                        , IFNULL(tbl_garden.garden_polygon_ha,0) AS plant_polygon_ha_mapped
                        , IFNULL(tbl_farmer.total_consent_agree,0) AS consent_signed
                        , IFNULL(tbl_mill.total_mill,0) AS mills_mapped
                        , IFNULL(tbl_agent.total_agent,0) AS agents_mapped
                        , NOW() AS DateGenerated
                    FROM
                        (
                            SELECT
                                '{$dataRegion[$i]['ProvinceID']}' AS ProvinceID
                                , '{$dataRegion[$i]['DistrictID']}' AS DistrictID
                                , '{$dataRegion[$i]['SubDistrictID']}' AS SubDistrictID
                                , '{$dataRegion[$i]['VillageID']}' AS VillageID
                                , '{$dataPartner[$j]['PartnerID']}' AS PartnerID
                        ) AS tbl_region

                        LEFT JOIN (SELECT
                            a.`VillageID`
                            , '{$dataPartner[$j]['PartnerID']}' AS PartnerID
                            , COUNT(a.`MemberID`) AS total_farmer
                            , IFNULL(SUM(IF(a.`LearningContractStatus` = '1',1,0)),0) AS total_consent_agree
                        FROM
                            ktv_members a
                            JOIN ktv_member_role r ON a.MemberID = r.MemberID AND r.MRoleID = 1 #ROLE PETANI
                            INNER JOIN ktv_access_partner_member acc_pm ON a.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$dataPartner[$j]['PartnerID']}'
                        WHERE
                            a.`StatusCode` = 'active'
                            AND a.`VillageID` = '{$dataRegion[$i]['VillageID']}'
                        GROUP BY a.`VillageID`
                        ) AS tbl_farmer
                            ON tbl_region.PartnerID = tbl_farmer.PartnerID AND tbl_region.VillageID = tbl_farmer.VillageID

                        LEFT JOIN (
                            SELECT
                                tgrup_agent.VillageID
                                , '{$dataPartner[$j]['PartnerID']}' AS PartnerID
                                , COUNT(tgrup_agent.MemberID) AS total_agent
                            FROM
                            (
                                SELECT
                                    a.`VillageID`
                                    , a.`MemberID`
                                FROM
                                    ktv_members a
                                    JOIN ktv_member_role r ON a.MemberID = r.MemberID AND r.MRoleID IN (5,6,7,8,9,10) #ROLE AGENT
                                    INNER JOIN ktv_access_partner_member acc_pm ON a.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$dataPartner[$j]['PartnerID']}'
                                WHERE
                                    a.`StatusCode` = 'active'
                                GROUP BY a.`MemberID`
                            ) AS tgrup_agent
                            WHERE
                                tgrup_agent.VillageID = '{$dataRegion[$i]['VillageID']}'
                            GROUP BY tgrup_agent.VillageID
                        ) AS tbl_agent
                            ON tbl_region.PartnerID = tbl_agent.PartnerID AND tbl_region.VillageID = tbl_agent.VillageID

                        LEFT JOIN (
                            SELECT
                                m.`VillageID`
                                , '{$dataPartner[$j]['PartnerID']}' AS PartnerID
                                , IFNULL(COUNT(a.PlotNr),0) AS garden_total
                                , IFNULL(SUM(a.GardenAreaHa),0) AS garden_ha
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
                                INNER JOIN ktv_access_partner_member acc_pm ON m.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$dataPartner[$j]['PartnerID']}'
                            WHERE
                                a.`StatusCode` = 'active'
                                AND m.`StatusCode` = 'active'
                                AND m.`VillageID` = '{$dataRegion[$i]['VillageID']}'
                            GROUP BY m.`VillageID`
                        ) AS tbl_garden
                            ON tbl_region.PartnerID = tbl_garden.PartnerID AND tbl_region.VillageID = tbl_garden.VillageID

                        LEFT JOIN (
                            SELECT
                                a.`VillageID`
                                , '{$dataPartner[$j]['PartnerID']}' AS PartnerID
                                , COUNT(a.`MillID`) AS total_mill
                            FROM
                                ktv_mill a
                                INNER JOIN ktv_access_partner_mill acc_pml ON a.`MillID` = acc_pml.`apmiMillID` AND acc_pml.`apmiPartnerID` = '{$dataPartner[$j]['PartnerID']}'
                            WHERE
                                a.`StatusCode` = 'active'
                                AND a.`VillageID` = '{$dataRegion[$i]['VillageID']}'
                            GROUP BY a.`VillageID`
                        ) AS tbl_mill
                            ON tbl_region.PartnerID = tbl_mill.PartnerID AND tbl_region.VillageID = tbl_mill.VillageID
                ";
                $query = $this->db->query($sql);

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
    
    public function getDisplaySupplyChainMillKpiPartner($PartnerIDStr) {
        //Ambil MillID dari PartnerId ini
        $sql = "SELECT
                    GROUP_CONCAT(DISTINCT a.`MillID` SEPARATOR ',') MillIDImp
                FROM
                    ktv_mill a
                WHERE
                    a.`PartnerID` IN ({$PartnerIDStr})";
        $data = $this->db->query($sql)->row_array();
        $MillIDImp = $data['MillIDImp'];
        if($MillIDImp == "") $MillIDImp = -1;
        
        $sql="SELECT
                SUM(a.farmer_registered) AS farmer_registered
                , SUM(a.farmer_sales) AS farmer_sales
                , SUM(a.plantation_mapped) AS plantation_mapped
                , SUM(a.plant_ha_mapped) AS plant_ha_mapped
                , SUM(a.plantation_polygon_mapped) AS plantation_polygon_mapped
                , SUM(a.plant_polygon_ha_mapped) AS plant_polygon_ha_mapped
                , SUM(a.consent_signed) AS consent_signed
                , SUM(a.mills_mapped) AS mills_mapped
                , SUM(a.agents_mapped) AS agents_mapped
                , SUM(a.agent) AS agents
                , SUM(a.owned_estate) AS owned_estate
                , SUM(a.external_estate) AS external_estate
                , SUM(a.plasma) AS plasma
                , SUM(a.direct_smallholder) AS direct_smallholder
                , SUM(a.garden_agent) AS garden_agent
                , SUM(a.garden_kebun_inti) AS garden_kebun_inti
                , SUM(a.garden_external_estate) AS garden_external_estate
                , SUM(a.garden_plasma) AS garden_plasma
                , SUM(a.garden_direct) AS garden_direct
                , SUM(a.garden_agent)+SUM(a.garden_kebun_inti)+SUM(a.garden_external_estate)+SUM(a.garden_plasma)+SUM(a.garden_direct) AS garden_total
                , DateGenerated
            FROM
                dash_pro_supplychain_mill_kpi_new a
            WHERE
                1 = 1
                AND a.MillID IN ({$MillIDImp})
            ";
        $query = $this->db->query($sql,array());
        $result['dataDisplay'] = $query->row_array();

        $sql="SELECT
                SUM(a.farmer_registered) AS farmer_registered
                , SUM(a.farmer_sales) AS farmer_sales
                , SUM(a.plantation_mapped) AS plantation_mapped
                , SUM(a.plant_ha_mapped) AS plant_ha_mapped
                , SUM(a.plantation_polygon_mapped) AS plantation_polygon_mapped
                , SUM(a.plant_polygon_ha_mapped) AS plant_polygon_ha_mapped
                , SUM(a.consent_signed) AS consent_signed
                , SUM(a.mills_mapped) AS mills_mapped
                , SUM(a.agents_mapped) AS agents_mapped
            FROM
                dash_pro_supplychain_mill_kpi_target_new a
            WHERE
                1 = 1
                AND a.MillID IN ({$MillIDImp})
        ";
        $query = $this->db->query($sql,array());
        $result['dataTarget'] = $query->row_array();

        return $result;
    }
    
    public function getDisplaySupplyChainMillKpiPartnerFull($PartnerIDStr) {
        //Ambil MillID dari PartnerId ini
        $sql = "SELECT
                    GROUP_CONCAT(DISTINCT a.`MillID` SEPARATOR ',') MillIDImp
                FROM
                    ktv_mill a
                WHERE
                    a.`PartnerID` IN ({$PartnerIDStr})";
        $data = $this->db->query($sql)->row_array();
        $MillIDImp = $data['MillIDImp'];
        if($MillIDImp == "") $MillIDImp = -1;
        
        
        $sql = "SELECT
                SUM(a.farmer_registered) AS farmer_registered
                , SUM(a.farmer_mapped) AS farmer_mapped
                , SUM(a.consent_signed) AS consent_signed
                , SUM(a.plantation_mapped_farmer) AS plantation_mapped_farmer
                , SUM(a.plantation_registered_farmer) AS plantation_registered_farmer
                , SUM(a.plant_ha_mapped_farmer) AS plant_ha_mapped_farmer
                , SUM(a.plantation_polygon_mapped_farmer) AS plantation_polygon_mapped_farmer
                , SUM(a.plant_polygon_ha_mapped_farmer) AS plant_polygon_ha_mapped_farmer
                , SUM(a.plantation_mapped_sme) AS plantation_mapped_sme
                , SUM(a.plantation_registered_sme) AS plantation_registered_sme
                , SUM(a.plant_ha_mapped_sme) AS plant_ha_mapped_sme
                , SUM(a.plantation_polygon_mapped_sme) AS plantation_polygon_mapped_sme
                , SUM(a.plant_polygon_ha_mapped_sme) AS plant_polygon_ha_mapped_sme
                , SUM(a.plantation_mapped_mill) AS plantation_mapped_mill
                , SUM(a.plantation_registered_mill) AS plantation_registered_mill
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
                AND a.PartnerID IN ({$PartnerIDStr})
            ";
        $query = $this->db->query($sql, array());
        $result['dataDisplay'] = $query->row_array();

        //data display langsung ================================================== (end)

        $sql="SELECT
                SUM(a.farmer_registered) AS farmer_registered
                , SUM(a.farmer_sales) AS farmer_sales
                , SUM(a.plantation_mapped) AS plantation_mapped
                , SUM(a.plant_ha_mapped) AS plant_ha_mapped
                , SUM(a.plantation_polygon_mapped) AS plantation_polygon_mapped
                , SUM(a.plant_polygon_ha_mapped) AS plant_polygon_ha_mapped
                , SUM(a.consent_signed) AS consent_signed
                , SUM(a.mills_mapped) AS mills_mapped
                , SUM(a.agents_mapped) AS agents_mapped
            FROM
                dash_pro_supplychain_mill_kpi_target_new a
            WHERE
                1 = 1
                AND a.MillID IN ({$MillIDImp})
        ";
        $query = $this->db->query($sql,array());
        $result['dataTarget'] = $query->row_array();

        return $result;
    }

    public function getDisplaySupplyChainMillKpi($millgroup,$mill){
        //data display langsung ================================================== (begin)
        if($millgroup != "" || $millgroup != false){
            if($mill == false) {
                $sql = "SELECT
                    GROUP_CONCAT(b.MillID) as MillIDs
                FROM
                    ktv_mill_group AS a
                    INNER JOIN ktv_mill AS b ON b.MillGroupID = a.MillGroupID
                    INNER JOIN ktv_access_partner_mill AS c ON c.apmiMillID = b.MillID 
                WHERE
                    c.apmiPartnerID = ?
                    AND a.MillGroupID = ?
                    AND a.StatusCode = 'active'
                Group By a.MillGroupID";
                $query = $this->db->query($sql, array($_SESSION['PartnerID'], $millgroup));
                $row = $query->row();
                if (isset($row))
                {
                    $sqlWhereMills = "AND a.MillID IN (".$row->MillIDs.")";
                }
            } else {
                $sqlWhereMills = "AND a.MillID IN (".$mill.")";
            }
        }else{
            $sqlWhereMills = "";
        }

        // if($DistrictID != ""){
        //     $sqldWhereDistrict = " AND SUBSTR(a.VillageID,1,4) = '$DistrictID' ";
        // }else{
        //     $sqldWhereDistrict = "";
        // }

        //buat SqlHakAksesKontrol (begin)
        // if($_SESSION['is_admin'] == "1"){
        //     $sqlHakAkses = " AND a.PartnerID = '1' #Partner Koltiva ";
        // } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
        //     //cek ktv_access_staff
        //     $sqlHakAkses = " AND SUBSTR(a.VillageID,1,4) IN (".$_SESSION['daerah_access'].")";
        //     $sqlHakAkses .= " AND a.PartnerID = '{$_SESSION['PartnerID']}' ";
        // } else {
        //     //cek ktv_access_staff
        //     $sqlHakAkses = " AND SUBSTR(a.VillageID,1,4) IN (".$_SESSION['daerah_access'].")";
        //     $sqlHakAkses .= " AND a.PartnerID = '1' #Partner Koltiva ";
        // }
        //buat SqlHakAksesKontrol (end)

        $sql="SELECT
                SUM(a.farmer_registered) AS farmer_registered
                , SUM(a.farmer_sales) AS farmer_sales
                , SUM(a.plantation_mapped) AS plantation_mapped
                , SUM(a.plant_ha_mapped) AS plant_ha_mapped
                , SUM(a.plantation_polygon_mapped) AS plantation_polygon_mapped
                , SUM(a.plant_polygon_ha_mapped) AS plant_polygon_ha_mapped
                , SUM(a.consent_signed) AS consent_signed
                , SUM(a.mills_mapped) AS mills_mapped
                , SUM(a.agents_mapped) AS agents_mapped
                , DateGenerated
            FROM
                dash_pro_supplychain_mill_kpi_new a
            WHERE
                1 = 1
                $sqlWhereMills
            ";
        $query = $this->db->query($sql,array());
        $result['dataDisplay'] = $query->row_array();

        //===== fof Kosong =======
        // $result['dataDisplay']['farmer_registered'] = 0;
        // $result['dataDisplay']['plantation_mapped'] = 0;
        // $result['dataDisplay']['plant_ha_mapped'] = 0;
        // $result['dataDisplay']['plantation_polygon_mapped'] = 0;
        // $result['dataDisplay']['plant_polygon_ha_mapped'] = 0;
        // $result['dataDisplay']['farmer_sales'] = 0;
        // $result['dataDisplay']['mills_mapped'] = 0;
        // $result['dataDisplay']['agents_mapped'] = 0;
        // $result['dataDisplay']['DateGenerated'] = date('Y-m-d H:i:s');
        //===== eof Kosong =======

        //===== fof All Province =======
        // $result['dataDisplay']['farmer_registered'] = 20977;
        // $result['dataDisplay']['plantation_mapped'] = 15695;
        // $result['dataDisplay']['plant_ha_mapped'] = 29264;
        // $result['dataDisplay']['plantation_polygon_mapped'] = 0;
        // $result['dataDisplay']['plant_polygon_ha_mapped'] = 0;
        // $result['dataDisplay']['farmer_sales'] = 0;
        // $result['dataDisplay']['mills_mapped'] = 130;
        // $result['dataDisplay']['agents_mapped'] = 93;
        // $result['dataDisplay']['DateGenerated'] = date('Y-m-d H:i:s');
        //===== eof All Province =======

        //===== fof indragiri hulu =======
        // $result['dataDisplay']['farmer_registered'] = 50;
        // $result['dataDisplay']['plantation_mapped'] = 63;
        // $result['dataDisplay']['plant_ha_mapped'] = 147;
        // $result['dataDisplay']['plantation_polygon_mapped'] = 0;
        // $result['dataDisplay']['plant_polygon_ha_mapped'] = 0;
        // $result['dataDisplay']['farmer_sales'] = 0;
        // $result['dataDisplay']['mills_mapped'] = 6;
        // $result['dataDisplay']['agents_mapped'] = 0;
        // $result['dataDisplay']['DateGenerated'] = date('Y-m-d H:i:s');
        // //===== eof indragiri hulu =======

        //===== fof indragiri hilir =======
        // $result['dataDisplay']['farmer_registered'] = 636;
        // $result['dataDisplay']['plantation_mapped'] = 1031;
        // $result['dataDisplay']['plant_ha_mapped'] = 1759;
        // $result['dataDisplay']['plantation_polygon_mapped'] = 0;
        // $result['dataDisplay']['plant_polygon_ha_mapped'] = 0;
        // $result['dataDisplay']['farmer_sales'] = 0;
        // $result['dataDisplay']['mills_mapped'] = 3;
        // $result['dataDisplay']['agents_mapped'] = 3;
        // $result['dataDisplay']['DateGenerated'] = date('Y-m-d H:i:s');
        //===== eof indragiri hilir =======

        //data display langsung ================================================== (end)

        //data target kpi ==================================================== (begin)
        // if($ProvinceID != ""){
        //     $sqldWherePropinsiTarget = " AND SUBSTR(a.DistrictID,1,2) = '$ProvinceID' ";
        // }else{
        //     $sqldWherePropinsiTarget = "";
        // }

        // if($DistrictID != ""){
        //     $sqldWhereDistrictTarget = " AND SUBSTR(a.DistrictID,1,4) = '$DistrictID' ";
        // }else{
        //     $sqldWhereDistrictTarget = "";
        // }

        //buat SqlHakAksesKontrol (begin)
        // if($_SESSION['is_admin'] == "1"){
        //     $sqlHakAksesTarget = " AND a.PartnerID = '1' #Partner Koltiva ";
        // } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
        //     //cek ktv_access_staff
        //     $sqlHakAksesTarget = " AND SUBSTR(a.DistrictID,1,4) IN (".$_SESSION['daerah_access'].")";
        //     $sqlHakAksesTarget .= " AND a.PartnerID = '{$_SESSION['PartnerID']}' ";
        // } else {
        //     //cek ktv_access_staff
        //     $sqlHakAksesTarget = " AND SUBSTR(a.DistrictID,1,4) IN (".$_SESSION['daerah_access'].")";
        //     $sqlHakAksesTarget .= " AND a.PartnerID = '1' #Partner Koltiva ";
        // }
        //buat SqlHakAksesKontrol (end)

        $sql="SELECT
                SUM(a.farmer_registered) AS farmer_registered
                , SUM(a.farmer_sales) AS farmer_sales
                , SUM(a.plantation_mapped) AS plantation_mapped
                , SUM(a.plant_ha_mapped) AS plant_ha_mapped
                , SUM(a.plantation_polygon_mapped) AS plantation_polygon_mapped
                , SUM(a.plant_polygon_ha_mapped) AS plant_polygon_ha_mapped
                , SUM(a.consent_signed) AS consent_signed
                , SUM(a.mills_mapped) AS mills_mapped
                , SUM(a.agents_mapped) AS agents_mapped
            FROM
                dash_pro_supplychain_mill_kpi_target_new a
            WHERE
                1 = 1
                $sqlWhereMills
        ";
        $query = $this->db->query($sql,array());
        $result['dataTarget'] = $query->row_array();

        //===== fof Kosong =======
        // $result['dataTarget']['farmer_registered'] = 61421;
        // $result['dataTarget']['plantation_mapped'] = 92142;
        // $result['dataTarget']['plant_ha_mapped'] = 276426;
        // $result['dataTarget']['plantation_polygon_mapped'] = 92142;
        // $result['dataTarget']['plant_polygon_ha_mapped'] = 276426;
        // $result['dataTarget']['farmer_sales'] = 61421;
        // $result['dataTarget']['mills_mapped'] = 140;
        // $result['dataTarget']['agents_mapped'] = 1994;
        //===== eof Kosong =======

        // $result['dataTarget']['farmer_registered'] = 61421;
        // $result['dataTarget']['plantation_mapped'] = 92142;
        // $result['dataTarget']['plant_ha_mapped'] = 276426;
        // $result['dataTarget']['plantation_polygon_mapped'] = 92142;
        // $result['dataTarget']['plant_polygon_ha_mapped'] = 276426;
        // $result['dataTarget']['farmer_sales'] = 61421;
        // $result['dataTarget']['mills_mapped'] = 140;
        // $result['dataTarget']['agents_mapped'] = 1994;
        // //===== fof indragiri hulu =======
        // $result['dataTarget']['farmer_registered'] = 2964;
        // $result['dataTarget']['plantation_mapped'] = 446;
        // $result['dataTarget']['plant_ha_mapped'] = 13338;
        // $result['dataTarget']['plantation_polygon_mapped'] = 92142;
        // $result['dataTarget']['plant_polygon_ha_mapped'] = 276426;
        // $result['dataTarget']['farmer_sales'] = 2964;
        // $result['dataTarget']['mills_mapped'] = 5;
        // $result['dataTarget']['agents_mapped'] = 80;
        // //===== eof indragiri hulu =======

        //===== fof indragiri hilir =======
        // $result['dataTarget']['farmer_registered'] = 1482;
        // $result['dataTarget']['plantation_mapped'] = 2223;
        // $result['dataTarget']['plant_ha_mapped'] = 6669;
        // $result['dataTarget']['plantation_polygon_mapped'] = 92142;
        // $result['dataTarget']['plant_polygon_ha_mapped'] = 276426;
        // $result['dataTarget']['farmer_sales'] = 1482;
        // $result['dataTarget']['mills_mapped'] = 3;
        // $result['dataTarget']['agents_mapped'] = 50;
        //===== eof indragiri hilir =======
        //data target kpi ==================================================== (end)

        return $result;
    }

    public function GetInfoPartner($PartnerID) {
        $sql = "SELECT
                    a.`PartnerID` AS id
                    , a.`PartnerName` AS label
                FROM
                    ktv_program_partner a
                WHERE
                    a.`PartnerID` = ?
                LIMIT 1";
        return $this->db->query($sql,array($PartnerID))->row_array();
    }

    public function GetHirarPartner($parent=0,$optionhtml,$increRekur) {
        $increRekur++;

        if($parent == 0 OR $parent == ""){
            $filter = " AND a.PartnerParentID IS NULL";
        }else{
            $filter = " AND a.PartnerParentID = '$parent'";
        }

        $sql = "SELECT
            a.PartnerID as id,
            #a.PartnerParentID,
            a.PartnerName as label
        FROM
            `ktv_program_partner` a
        WHERE 
            a.StatusCode != 'nullified'
            $filter  
        ORDER BY a.PartnerFullName ASC
        ";
        $query = $this->db->query($sql);
        if ($query->num_rows()>0) {
            $data = $query->result_array();
            foreach ($data as $key => $value) {

                $labelPanah = "";
                for ($i=0; $i < $increRekur; $i++) { 
                    $labelPanah .= '&raquo;&raquo;';
                }

                $optionhtml .= '<option value="'.$value['id'].'">'.$labelPanah.' '.$value['label'].'</option>';
                $children = $this->GetHirarPartner($value['id'],$optionhtml,$increRekur);

                if ($children != "") {
                    $optionhtml .= $children;
                }
            }
            return $optionhtml;
        }
        return false;
    }

    public function GetPanelGridPartnerHirar($PartnerID) {
        $sql = "SELECT
                a.`PartnerID`
                , a.`PartnerName`
            FROM
                ktv_program_partner a
            WHERE
                a.`PartnerID` = ?
            LIMIT 1";
        $DataSelfPartner = $this->db->query($sql,array($PartnerID))->row_array();

        $DataParent['PartnerID'] = $DataSelfPartner['PartnerID'];
        $DataParent['PartnerName'] = $DataSelfPartner['PartnerName'];
        $DataParent['expanded'] = true;
        $DataParent['children'] = $this->GetPartnerHirarData($PartnerID);

        $data['text'] = '.';
        $data['expanded'] = true;
        $data['children'] = $DataParent;
        return $data;
    }

    public function GetPartnerHirarData($PartnerID) {
        $sql = "SELECT
            a.PartnerID,
            a.PartnerName
        FROM
            `ktv_program_partner` a
        WHERE 
            a.StatusCode != 'nullified'
            AND a.PartnerParentID = ?
        ORDER BY a.PartnerFullName ASC";
        $query = $this->db->query($sql, array($PartnerID));

        if ($query->num_rows() > 0) {
            $data = $query->result_array();

            foreach ($data as $key => $value) {
                $children = $this->GetPartnerHirarData($value['PartnerID']);
                if (!empty($children)) {
                    $data[$key]['children'] = $children;
                } else {
                    $data[$key]['leaf'] = true;
                }
                $data[$key]['expanded'] = true;
            }
            return $data;
        }
        return array();
    }

    public function DataGetPartnerIDAllHirarki($PartnerID) {
        $this->PartnerIDHirar = array();
        $this->PartnerIDHirar[] = $PartnerID;
        $this->DataGetPartnerIDAllHirarkiRecursive($PartnerID);
        
        return implode(',',$this->PartnerIDHirar);
    }

    private function DataGetPartnerIDAllHirarkiRecursive($PartnerID) {
        $ArrResult = array();

        $sql = "SELECT a.PartnerID FROM
            `ktv_program_partner` a
        WHERE 
            a.StatusCode != 'nullified'
            AND a.PartnerParentID = ?";
        $query = $this->db->query($sql, array($PartnerID));

        if ($query->num_rows() > 0) {
            $data = $query->result_array();

            foreach ($data as $key => $value) {
                $this->PartnerIDHirar[] = $data[$key]['PartnerID'];
                $this->DataGetPartnerIDAllHirarkiRecursive($value['PartnerID']);
            }
        }
    }

    public function GenerateDashProSupplyChainMillKpiNew() {
        $this->db->trans_begin();

        //reset dl datanya
        $sql = "DELETE FROM dash_pro_supplychain_mill_kpi_new";
        $query = $this->db->query($sql);

        //Get List Mill nya untuk diproses (ambil dari target)
        $sql = "SELECT
                    a.`MillID`
                    , b.`PartnerID`
                FROM
                    `dash_pro_supplychain_mill_kpi_target_new` a
                    INNER JOIN ktv_mill b ON a.`MillID` = b.`MillID`
                WHERE
                    b.`PartnerID` != 0
                    AND b.`PartnerID` IS NOT NULL";
        $ListMill = $this->db->query($sql,array())->result_array();

        if(isset($ListMill)) {
            for ($i=0; $i < count($ListMill); $i++) { 
                $sql = "INSERT INTO `dash_pro_supplychain_mill_kpi_new` (
                            `MillID`,
                            `farmer_registered`,
                            `farmer_sales`,
                            `plantation_mapped`,
                            `plant_ha_mapped`,
                            `plantation_polygon_mapped`,
                            `plant_polygon_ha_mapped`,
                            `consent_signed`,
                            `mills_mapped`,
                            `agents_mapped`,
                            `agent`,
                            `owned_estate`,
                            `external_estate`,
                            `plasma`,
                            `direct_smallholder`,
                            `garden_agent`,
                            `garden_kebun_inti`,
                            `garden_external_estate`,
                            `garden_plasma`,
                            `garden_direct`,
                            `DateGenerated`
                        )
                        SELECT
                            {$ListMill[$i]['MillID']} AS MillID
                            , IFNULL(tbl_farmer.farmer_registered,0) AS farmer_registered
                            , 0 AS farmer_sales
                            , IFNULL(tbl_garden.plantation_mapped,0) AS plantation_mapped
                            , IFNULL(tbl_garden.plant_ha_mapped,0) AS plant_ha_mapped
                            , IFNULL(tbl_garden.plantation_polygon_mapped,0) AS plantation_polygon_mapped
                            , IFNULL(tbl_garden.plant_polygon_ha_mapped,0) AS plant_polygon_ha_mapped
                            , IFNULL(tbl_farmer.consent_signed,0) AS consent_signed
                            , IFNULL(tbl_mill.mills_mapped,0) AS mills_mapped
                            , IFNULL(tbl_agent.agents_mapped,0) AS agents_mapped
                            , IFNULL( tbl_agent.agent, 0 ) AS agents
                            , IFNULL( tbl_agent.kebun_inti, 0 ) AS owned_estate
                            , IFNULL( tbl_agent.external_estate, 0 ) AS external_estate
                            , IFNULL( tbl_agent.plasma, 0 ) AS plasma
                            , IFNULL( tbl_agent.direct, 0 ) AS direct
                            , IFNULL( tbl_sme_garden.garden_agent, 0 ) AS garden_agent
                            , IFNULL( tbl_sme_garden.garden_kebun_inti, 0 ) AS garden_kebun_inti
                            , IFNULL( tbl_sme_garden.garden_external_estate, 0 ) AS garden_external_estate
                            , IFNULL( tbl_sme_garden.garden_plasma, 0 ) AS garden_plasma
                            , IFNULL( tbl_sme_garden.garden_direct, 0 ) AS garden_direct
                            , NOW() AS DateGenerated
                        FROM (
                            SELECT
                                {$ListMill[$i]['MillID']} AS MillID
                                , COUNT(a.MemberID) AS farmer_registered
                                , IFNULL(SUM(IF(a.`LearningContractStatus` = '1',1,0)),0) AS consent_signed
                            FROM
                                ktv_members a
                                JOIN ktv_member_role r ON a.MemberID = r.MemberID AND r.MRoleID = 1 #ROLE PETANI
                                INNER JOIN ktv_access_partner_member acc_pm ON a.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = {$ListMill[$i]['PartnerID']}
                            WHERE
                                a.StatusCode = 'active'
                        ) AS tbl_farmer
                        LEFT JOIN (
                            SELECT
                                {$ListMill[$i]['MillID']} AS MillID
                                , COUNT(p.`MemberID`) AS plantation_mapped
                                , SUM(p.GardenAreaHa) AS plant_ha_mapped
                                , IFNULL(SUM(IF(p.`GardenAreaPolygon` > 0,1,0)),0) AS plantation_polygon_mapped
                                , IFNULL(SUM(p.GardenAreaPolygon),0) AS plant_polygon_ha_mapped
                            FROM
                                ktv_survey_plot p
                                JOIN (SELECT
                                    p.MemberID, p.PlotNr, MAX(p.SurveyNr) AS SurveyNr
                                FROM ktv_survey_plot p WHERE p.`StatusCode` = 'active'
                                GROUP BY p.MemberID, p.PlotNr) AS gar_latest ON p.MemberID = gar_latest.MemberID AND p.PlotNr = gar_latest.PlotNr AND p.SurveyNr = gar_latest.SurveyNr
                                INNER JOIN ktv_members m ON p.`MemberID` = m.`MemberID`
                                JOIN ktv_member_role r ON m.MemberID = r.MemberID AND r.MRoleID = 1 #ROLE PETANI
                                INNER JOIN ktv_access_partner_member acc_pm ON m.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = {$ListMill[$i]['PartnerID']}
                        ) AS tbl_garden ON tbl_farmer.MillID = tbl_garden.MillID
                        LEFT JOIN (
                            SELECT
                                {$ListMill[$i]['MillID']} AS MillID
                                , COUNT(a.`MillID`) AS mills_mapped
                            FROM
                                ktv_mill a
                                INNER JOIN ktv_access_partner_mill acc_pmill ON a.`MillID` = acc_pmill.`apmiMillID` AND acc_pmill.`apmiPartnerID` = {$ListMill[$i]['PartnerID']}
                            WHERE
                                a.`StatusCode` = 'active'
                        ) AS tbl_mill ON tbl_farmer.MillID = tbl_mill.MillID
                        LEFT JOIN (
                            SELECT
                                {$ListMill[$i]['MillID']} AS MillID
                                , COUNT( tg.MemberID ) AS agents_mapped
                                , SUM(IF(tg.MRoleID = 5 OR tg.MRoleID = 6 OR tg.MRoleID = 7 OR tg.MRoleID = 8 OR tg.MRoleID = 9 OR tg.MRoleID = 13,1,0)) AS agent
                                , SUM(IF(tg.MRoleID = 11,1,0)) AS kebun_inti
                                , SUM(IF(tg.MRoleID = 12,1,0)) AS external_estate
                                , SUM(IF(tg.MRoleID = 14,1,0)) AS plasma
                                , SUM(IF(tg.MRoleID = 'direct',1,0)) AS direct
							FROM
							(
                                SELECT
                                    a.MemberID 
                                    , rr.MRoleName
                                    , rr.MRoleID
                                    , a.MemberName
                                FROM
                                    ktv_members a
                                    JOIN ktv_member_role r ON a.`MemberID` = r.`MemberID`
                                    JOIN ktv_ref_member_role rr ON r.`MRoleID` = rr.`MRoleID` 
                                    AND rr.`MRoleType` = 'agent'
                                    INNER JOIN ktv_access_partner_member acc_pm ON a.MemberID = acc_pm.apmMemberID 
                                    AND acc_pm.apmPartnerID = {$ListMill[$i]['PartnerID']} 
                                WHERE
                                    1 = 1 
                                    AND a.StatusCode = 'active' 
                                GROUP BY
                                    a.MemberID 
                                    UNION
                                SELECT
                                        s_ma.apmMemberID MemberID
                                        , 'Direct Smallholder' MRoleName
                                        , 'direct' MRoleID
                                        , m.MemberName MemberName
                                FROM
                                        ktv_access_partner_member s_ma
                                        INNER JOIN ktv_mill s_mi ON s_ma.apmPartnerID = s_mi.PartnerID
                                        INNER JOIN ktv_program_partner s_par ON s_mi.PartnerID = s_par.PartnerID
                                        INNER JOIN view_tc_supplychain_org org ON org.ObjID = s_mi.MillID
                                        LEFT JOIN ktv_members m on m.MemberID = s_ma.apmMemberID
                                WHERE
                                -- 	s_par.PartnerIndustry = 3 
                                1=1
                                        AND s_mi.StatusCode = 'active'
                                        AND s_mi.PartnerID = {$ListMill[$i]['PartnerID']}
                                        AND m.SupplybaseType = 'direct'
                                GROUP BY
                                        s_ma.apmMemberID
                                ) AS tg
                        ) AS tbl_agent ON tbl_farmer.MillID = tbl_agent.MillID
                        LEFT JOIN (
                            SELECT
                                {$ListMill[$i]['MillID']} AS MillID
                                , SUM(IF(gsme.MRoleID = 5 OR gsme.MRoleID = 6 OR gsme.MRoleID = 7 OR gsme.MRoleID = 8 OR gsme.MRoleID = 9 OR gsme.MRoleID = 13, gsme.GardenAreaHa,0)) garden_agent
                                , SUM(IF(gsme.MRoleID = 11, gsme.GardenAreaHa,0)) garden_kebun_inti
                                , SUM(IF(gsme.MRoleID = 12, gsme.GardenAreaHa,0)) garden_external_estate
                                , SUM(IF(gsme.MRoleID = 14, gsme.GardenAreaHa,0)) garden_plasma
                                , SUM(IF(gsme.MRoleID = 'direct', gsme.GardenAreaHa,0)) garden_direct
                            FROM
                            (
                                SELECT
                                    p.GardenAreaHa
                                    , rm.MRoleName
                                    , rm.MRoleID
                                    , rm.MRoleType
                                FROM
                                    ktv_survey_plot_sme p
                                JOIN (
                                    SELECT
                                        p.MemberID,
                                        p.PlotNr,
                                        MAX( p.SurveyNr ) AS SurveyNr 
                                    FROM
                                        ktv_survey_plot_sme p 
                                    WHERE
                                        p.`StatusCode` = 'active' 
                                    GROUP BY
                                        p.MemberID,
                                        p.PlotNr 
                                ) AS gar_latest ON p.MemberID = gar_latest.MemberID AND p.PlotNr = gar_latest.PlotNr AND p.SurveyNr = gar_latest.SurveyNr
                                INNER JOIN 
                                    ktv_members m ON p.`MemberID` = m.`MemberID`
                                INNER JOIN 
                                    ktv_member_role r ON m.MemberID = r.MemberID
                                LEFT JOIN 
                                    ktv_ref_member_role rm on rm.MRoleID = r.MRoleID
                                INNER JOIN 
                                    ktv_access_partner_member acc_pm ON m.MemberID = acc_pm.apmMemberID
                                WHERE
                                    acc_pm.apmPartnerID = {$ListMill[$i]['PartnerID']}
                                AND
                                    rm.MRoleType = 'Agent'
                                AND
                                    m.StatusCode = 'active'
                                UNION
                                SELECT
                                    p.GardenAreaHa
                                    , 'Direct Smallholder' MRoleName
                                    , 'direct' MRoleID
                                    , 'Farmer' MRoleType
                                FROM
                                        ktv_survey_plot p
                                JOIN (
                                    SELECT
                                        p.MemberID,
                                        p.PlotNr,
                                        MAX( p.SurveyNr ) AS SurveyNr 
                                    FROM
                                        ktv_survey_plot p 
                                    WHERE
                                        p.`StatusCode` = 'active' 
                                    GROUP BY
                                        p.MemberID,
                                        p.PlotNr 
                                ) AS gar_latest ON p.MemberID = gar_latest.MemberID AND p.PlotNr = gar_latest.PlotNr AND p.SurveyNr = gar_latest.SurveyNr
                                INNER JOIN
                                    ktv_members m ON p.`MemberID` = m.`MemberID`
                                INNER JOIN
                                    ktv_access_partner_member apm on apm.apmMemberID = m.MemberID
                                INNER JOIN 
                                        ktv_member_role r ON m.MemberID = r.MemberID
                                LEFT JOIN 
                                        ktv_ref_member_role rm on rm.MRoleID = r.MRoleID
                                WHERE
                                    m.StatusCode = 'active'
                                AND
                                    apm.apmPartnerID = {$ListMill[$i]['PartnerID']}
                                AND
                                    rm.MRoleType = 'Farmer'
                            ) gsme
                        ) AS tbl_sme_garden ON tbl_farmer.MillID = tbl_sme_garden.MillID
                ";
                $query = $this->db->query($sql);
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
}
?>