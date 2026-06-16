<?php

/**
 * @Author: nikolius
 * @Date:   2017-09-08 11:18:11
 * @Last Modified by:   nikolius
 * @Last Modified time: 2017-12-13 20:54:25
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mpro_kpi extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    private function truncateTable($namaTabel, $year){
        $sql  ="DELETE FROM `$namaTabel`";
        $sql .= "WHERE Year = $year";
        $query = $this->db->query($sql);
    }

    public function generateDashProKpi(){
        $this->db->trans_begin();

        $getLastKPI = $this->db->select('MAX(Year) AS Year', FALSE)
                               ->get('dash_pro_kpi')
                               ->result()[0]->Year;

        $getLastKPITarget = $this->db->select('MAX(Year) AS Year', FALSE)
                               ->get('dash_pro_kpi_target')
                               ->result()[0]->Year;


        if ((int) date('Y') === (int) $getLastKPI) {
            $this->truncateTable('dash_pro_kpi', (int) $getLastKPI);
        }

        if ((int) date('Y') != (int) $getLastKPITarget) {
            $this->generateDashProKpiTarget();
        }

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
                    INSERT INTO dash_pro_kpi
                    SELECT
                        tbl_region.ProvinceID
                        , tbl_region.DistrictID
                        , tbl_region.SubDistrictID
                        , tbl_region.VillageID
                        , tbl_region.PartnerID
                        , IFNULL(tbl_farmer.total_farmer,0) AS farmer_registered
                        , IFNULL(tbl_garden.garden_total,0) AS plantation_mapped
                        , IFNULL(tbl_garden.garden_ha,0) AS plant_ha_mapped
                        , IFNULL(tbl_garden.garden_polygon_total,0) AS plantation_polygon_mapped
                        , IFNULL(tbl_garden.garden_polygon_ha,0) AS plant_polygon_ha_mapped
                        , IFNULL(tbl_farmer.total_consent_agree,0) AS consent_signed
                        , IFNULL(tbl_mill.total_mill,0) AS mills_mapped
                        , IFNULL(tbl_agent.total_agent,0) AS agents_mapped
                        , NOW() AS DateGenerated
                        , YEAR(CURDATE()) AS Year
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
                                AND a.`NDAAgree`=1
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

    public function generateDashProKpiTarget() {
        $getLastKPITarget = $this->db->select('MAX(Year) AS Year', FALSE)
                               ->get('dash_pro_kpi_target')
                               ->result()[0]->Year;

        if ((int) date('Y') != (int) $getLastKPITarget) {
            $this->db->trans_begin();

            $getDataDashProKpiTarget = $this->db->get('dash_pro_kpi_target')
                                                ->result();

            foreach ($getDataDashProKpiTarget as $key => $value) {
                $value->Year = date('Y');

                $query = $this->db->insert('dash_pro_kpi_target', $value);
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
        } else {
            $results['success'] = true;
            $results['message'] = "No data generate";
        }

        return $results;
    }

    public function getDisplayKpi($CountryID,$ProvinceID, $DistrictID, $FarmType, $Year){
        if ($Year == null) {
            $getLastKPI = $this->db->select('MAX(Year) AS Year', FALSE)
                               ->get('dash_pro_kpi')
                               ->result()[0]->Year;

            $Year = $getLastKPI;
        }

        //data display langsung ================================================== (begin)

        // original script

        // if($ProvinceID != ""){
        //     $sqldWherePropinsi = " AND kd.ProvinceID = '$ProvinceID' ";
        // }else{
        //     $sqldWherePropinsi = "";
        // }

        // if($DistrictID != ""){
        //     $sqldWhereDistrict = " AND kd.DistrictID = '$DistrictID' ";
        // }else{
        //     $sqldWhereDistrict = "";
        // }

        // modified 13-4-2021
        if($CountryID != ""){
            $sqldWhereCountry = " AND p.CountryCode = '$CountryID'";
        }else{
            $sqldWhereCountry = "";
        }
        if ($ProvinceID != "") {
            $sqldWherePropinsi = " AND a.ProvinceID = '$ProvinceID' ";
        } else {
            $sqldWherePropinsi = "";
        }

        if ($DistrictID != "") {
            $sqldWhereDistrict = "";
            $sqldWhereDistrict = " AND a.DistrictID = '$DistrictID' ";
        } else {
            $sqldWhereDistrict = "";
        }

        // original script

        //buat SqlHakAksesKontrol (begin)
        // if($_SESSION['is_admin'] == "1"){
        //     $sqlHakAkses = " AND a.PartnerID = '1' #Partner Koltiva ";
        // } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
        //     //cek ktv_access_staff
        //     $sqlHakAkses = " AND kd.DistrictID IN (".$_SESSION['daerah_access'].")";
        //     $sqlHakAkses .= " AND a.PartnerID = '{$_SESSION['PartnerID']}' ";
        // } else {
        //     //cek ktv_access_staff
        //     $sqlHakAkses = " AND kd.DistrictID IN (".$_SESSION['daerah_access'].")";
        //     $sqlHakAkses .= " AND a.PartnerID = '1' #Partner Koltiva ";
        // }
        //buat SqlHakAksesKontrol (end)

        // modified 13-4-2021
        
        $sqlHakAkses ='';
        $sqlHakAksesPartner = '';
        if($_SESSION['is_admin'] == "1"){
            $sqlHakAksesPartner = " AND a.PartnerID = '1' #Partner Koltiva ";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            //cek ktv_access_staff
            $sqlHakAkses = " AND a.DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlHakAksesPartner .= " AND a.PartnerID = '{$_SESSION['PartnerID']}' ";
        } else {
            //cek ktv_access_staff
            $sqlHakAkses = " AND a.DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlHakAksesPartner .= " AND a.PartnerID = '1' #Partner Koltiva ";
        }

        $sqldWhereYear = " AND a.Year = '$Year' ";

        $sql="SELECT
                SUM(a.farmer_registered) AS farmer_registered
                , SUM(a.plantation_mapped) AS plantation_mapped
                , SUM(a.plant_ha_mapped) AS plant_ha_mapped
                , SUM(a.plantation_polygon_mapped) AS plantation_polygon_mapped
                , SUM(a.plant_polygon_ha_mapped) AS plant_polygon_ha_mapped
                , SUM(a.consent_signed) AS consent_signed
                , SUM(a.mills_mapped) AS mills_mapped
                , SUM(a.agents_mapped) AS agents_mapped
                , DateGenerated
            FROM
                dash_pro_kpi a
                /* LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
                LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID */
            LEFT JOIN ktv_province as p on p.ProvinceID = a.ProvinceID
            WHERE
                1 = 1
                $sqldWhereCountry
                $sqldWherePropinsi
                $sqldWhereDistrict
                $sqlHakAkses
                $sqlHakAksesPartner
                $sqldWhereYear
            ";
        $query = $this->db->query($sql,array());
        $result['dataDisplay'] = $query->row_array();
        //data display langsung ================================================== (end)

        // original script

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

        //modified 13-4-2021

        if ($CountryID != "") {
            // $sqldWherePropinsiTarget = "";
           $sqldWhereCountryTarget = " AND p.CountryCode = '$CountryID' ";
        } else {
            $sqldWhereCountryTarget = "";
        }
        if ($ProvinceID != "") {
            // $sqldWherePropinsiTarget = "";
           $sqldWherePropinsiTarget = " AND p.ProvinceID = '$ProvinceID' ";
        } else {
            $sqldWherePropinsiTarget = "";
        }

        if ($DistrictID != "") {
            // $sqldWhereDistrictTarget = "";
           $sqldWhereDistrictTarget = " AND a.DistrictID = '$DistrictID' ";
        } else {
            $sqldWhereDistrictTarget = "";
        }

        // original script

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

        // modified 13-4-2021

        $sqlcHakAkses = '';
        $sqlcHakAksesPartner = '';
        if($_SESSION['is_admin'] == "1"){
            $sqlcHakAksesPartner = " AND a.PartnerID = '1' #Partner Koltiva ";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            //cek ktv_access_staff
            $sqlcHakAkses = " AND d.DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlcHakAksesPartner .= " AND a.PartnerID = '{$_SESSION['PartnerID']}' ";
        } else {
            //cek ktv_access_staff
            $sqlcHakAkses = " AND d.DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlcHakAksesPartner .= " AND a.PartnerID = '1' #Partner Koltiva ";
        }

        $sql="
            SELECT
                SUM(a.farmer_registered) AS farmer_registered
                , SUM(a.plantation_mapped) AS plantation_mapped
                , SUM(a.plant_ha_mapped) AS plant_ha_mapped
                , SUM(a.plantation_polygon_mapped) AS plantation_polygon_mapped
                , SUM(a.plant_polygon_ha_mapped) AS plant_polygon_ha_mapped
                , SUM(a.consent_signed) AS consent_signed
                , SUM(a.mills_mapped) AS mills_mapped
                , SUM(a.agents_mapped) AS agents_mapped
            FROM
                dash_pro_kpi_target a
            LEFT JOIN ktv_district as d on d.DistrictID = a.DistrictID
            LEFT JOIN ktv_province as p on p.ProvinceID = d.ProvinceID
            WHERE
                1 = 1
                $sqldWhereCountryTarget
                $sqldWherePropinsiTarget
                $sqldWhereDistrictTarget
                $sqlcHakAkses
                $sqlcHakAksesPartner
                $sqldWhereYear
        ";
        $query = $this->db->query($sql,array());
        $result['dataTarget'] = $query->row_array();
        //data target kpi ==================================================== (end)

        return $result;
    }

    public function GetComboFilterYearDashKpi() {
        $return  = array();

        if ($_SESSION['is_admin'] == "1"){
            $conditionPartner = '';
        } else {
            $conditionPartner = " AND a.PartnerID = '{$_SESSION['PartnerID']}' ";
        }

        $sql = "SELECT
                    a.`Year`
                FROM
                    `dash_pro_kpi` a
                WHERE 1=1
                    /* AND a.`PartnerID` = {$_SESSION['PartnerID']} */
                    /* $conditionPartner */
                GROUP BY a.`Year`
                ORDER BY a.`Year` DESC";
        $data = $this->db->query($sql)->result_array();
        
        for ($i=0; $i < count($data); $i++) {
            $return[$i]['id'] = $data[$i]['Year'];
            $return[$i]['label'] = $data[$i]['Year'];
        }

        return $return;
    }

    public function GetComboFilterYearDashKpiSawitTerampil() {
        $return  = array();

        $sql = "SELECT
                    a.`Year`
                FROM
                    `ktv_kpi_st_certification_target_ims_district` a
                WHERE 1=1
                    /* AND a.`PartnerID` = {$_SESSION['PartnerID']} */
                    /* $conditionPartner */
                GROUP BY a.`Year`
                ORDER BY a.`Year` DESC";
        $data = $this->db->query($sql)->result_array();
        
        for ($i=0; $i < count($data); $i++) {
            $return[$i]['id'] = $data[$i]['Year'];
            $return[$i]['label'] = $data[$i]['Year'];
        }

        return $return;
    }

    public function GetLockDateSawit($ProgID){
        $SqlSearch = '';

        if($_SESSION['is_admin'] == "1"){
            $SqlSearch .= "";
        }else{
            $SqlSearch .= " AND a.`ReportStatus` = '1'";
        }

        if ($ProgID != 0){
            $SqlSearch .= " AND b.ProgID = {$ProgID}";
        }

        $sql = "SELECT
                DATE_FORMAT(a.`DateProcess`,'%Y-%m-%d') AS id
                , ReportName AS label
            FROM
                ktv_certification_progress_st_process a
            LEFT JOIN ktv_certification_progress_st_process_program b ON b.LockID = a.id
            WHERE
                1=1
                AND b.StatusCode = 'active'
                $SqlSearch
            GROUP BY DATE_FORMAT(a.`DateProcess`,'%Y-%m')
            ORDER BY a.DateProcess DESC";
        $data = $this->db->query($sql,array($wave))->result_array();
        return $data;
    }

    public function GetProgIDKS($WaveID){
        $sql    = "SELECT ProgID FROM ktv_ks_kpi_process_project WHERE id = ?";
        $query  = $this->db->query($sql, array($WaveID))->row_array();

        return $query["ProgID"];
    }

    public function GetLockDateKS($ProgID){

        $SqlSearch = '';

        if($_SESSION['is_admin'] == "1"){
            $SqlSearch .= "";
        }else{
            $SqlSearch .= " AND a.`ReportStatus` = '1'";
        }

        if ($ProgID != 0){
            $SqlSearch .= " AND b.ProgID = {$ProgID}";
        }

        $sql = "SELECT
                DATE_FORMAT(a.`DateProcess`,'%Y-%m-%d') AS id
                , ReportName AS label
            FROM
                ktv_ks_kpi_process a
            LEFT JOIN ktv_ks_kpi_process_project b ON b.LockID = a.id
            WHERE
                1=1
                AND b.StatusCode = 'active'
                $SqlSearch
            GROUP BY DATE_FORMAT(a.`DateProcess`,'%Y-%m')
            ORDER BY a.DateProcess DESC";
        $data = $this->db->query($sql)->result_array();
        // echo "<pre>";print_r($this->db->last_query());die;
        return $data;
    }

    public function GetWaveSawit(){
        $sql = "SELECT
            a.ProgID id
            , b.ProgramName label
        FROM
            `ktv_certification_progress_st_process_program` a
        LEFT JOIN
            ktv_first_buyer_program b on b.ProgID = a.ProgID
        WHERE
            a.StatusCode = 'active'
        GROUP BY
            a.ProgID";
        $data = $this->db->query($sql)->result_array();
        return $data;
    }

    public function GetWaveKS(){
        $sql = "SELECT
            a.ProgID id
            , b.ProgramName label
        FROM
            `ktv_ks_kpi_process_project` a
        LEFT JOIN
            ktv_first_buyer_program b on b.ProgID = a.ProgID
        WHERE
            a.StatusCode = 'active'
        GROUP BY
            a.ProgID";
        $data = $this->db->query($sql)->result_array();
        return $data;
    }

    public function getDefaultWaveKsatriaSawit(){
        $sql = "SELECT
                a.ProgID id
                , b.ProgramName label
            FROM
                `ktv_ks_kpi_process_project` a
            LEFT JOIN
                ktv_first_buyer_program b on b.ProgID = a.ProgID
            WHERE
                a.StatusCode = 'active'
            GROUP BY
                a.ProgID
            ORDER BY a.ProgID DESC
            LIMIT 1";
        return $this->db->query($sql)->row_array();
    }

    public function getDefaultWaveSawit(){
        $sql = "SELECT
                a.ProgID id
                , b.ProgramName label
            FROM
                `ktv_certification_progress_st_process_program` a
            LEFT JOIN
                ktv_first_buyer_program b on b.ProgID = a.ProgID
            WHERE
                a.StatusCode = 'active'
            GROUP BY
                a.ProgID
            ORDER BY a.ProgID DESC
            LIMIT 1";
        return $this->db->query($sql)->row_array();
    }

    public function palm_sme_ks_export($lockdate,$ProgID,$chdistrict){
        $sqlwhere = "";
        $sqlparam = array();

        if($lockdate != "" AND $ProgID != "all_wave"){
            $sqlwhere .= " AND DATE(a.DateGenerated) = ?";
            $sqlparam[] = $lockdate;
            
            $sqlparamtarget[] = date("Y", strtotime($lockdate));
        }

        if($ProgID != "" AND $ProgID != "all_wave"){
            $sqlwhere .= " AND a.ProgID = ?";
            $sqlparam[] = $ProgID;
            
            $sqlparamtarget[] = $ProgID;
        }

        if($chdistrict != "" AND $chdistrict != "all_cluster"){
            $sqlwhere .= " AND a.DistrictID = ?";
            $sqlparam[] = $chdistrict;
            
            $sqlparamtarget[] = $chdistrict;
        }

        $sql = "SELECT
                * 
            FROM
                `ktv_ks_kpi_progress_detail_sme_mapped` a
            WHERE
                1=1
            $sqlwhere
            GROUP BY a.MemberID
        ";
        $query = $this->db->query($sql,$sqlparam);
        $return['data']         = $query->result_array();
        $return['count_data']   = $query->num_rows();

        $return['success'] = true;
        return $return;
    }

    public function palm_oil_mill_ks_export($lockdate,$ProgID,$chdistrict){
        $sqlwhere = "";
        $sqlparam = array();

        if($lockdate != "" AND $ProgID != "all_wave"){
            $sqlwhere .= " AND DATE(a.LockedDate) = ?";
            $sqlparam[] = $lockdate;
            
            $sqlparamtarget[] = date("Y", strtotime($lockdate));
        }

        if($ProgID != "" AND $ProgID != "all_wave"){
            $sqlwhere .= " AND a.ProgID = ?";
            $sqlparam[] = $ProgID;
            
            $sqlparamtarget[] = $ProgID;
        }

        if($chdistrict != "" AND $chdistrict != "all_cluster"){
            $sqlwhere .= " AND a.DistrictID = ?";
            $sqlparam[] = $chdistrict;
            
            $sqlparamtarget[] = $chdistrict;
        }

        $sql = "SELECT
                * 
            FROM
                `ktv_ks_kpi_progress_detail_mills` a
            WHERE
                1=1
            $sqlwhere
            GROUP BY a.MillID
        ";
        $query = $this->db->query($sql,$sqlparam);
        $return['data']         = $query->result_array();
        $return['count_data']   = $query->num_rows();

        $return['success'] = true;
        return $return;
    }

    public function palm_oil_mill_export($DateGenerated,$ProgID,$ClusterID){
        $sqlwhere = "";
        $sqlparam = array();

        if($DateGenerated != ""){
            $sqlwhere .= " AND DATE(a.DateGenerated) = ?";
            $sqlparam[] = $DateGenerated;
        }

        if($ProgID != ""){
            $sqlwhere .= " AND a.ProgID = ?";
            $sqlparam[] = $ProgID;
        }

        if($ClusterID != "" AND $ClusterID != "all_cluster"){
            $sqlwhere .= " AND a.ClusterID = ?";
            $sqlparam[] = $ClusterID;
        }

        $sql = "SELECT
                * 
            FROM
                `ktv_st_kpi_summary_mills` a
            WHERE
                1=1
            $sqlwhere
        ";
        $query = $this->db->query($sql,$sqlparam);
        $return['data']         = $query->result_array();
        $return['count_data']   = $query->num_rows();

        $return['success'] = true;
        return $return;
    }

    public function farmer_registered_ks_export($DateGenerated,$ProgID,$ClusterID){
        $sqlwhere = "";
        $sqlparam = array();

        if($DateGenerated != ""){
            $sqlwhere .= " AND DATE(a.DateGenerated) = ?";
            $sqlparam[] = $DateGenerated;
        }

        if($ProgID != ""){
            $sqlwhere .= " AND a.ProgID = ?";
            $sqlparam[] = $ProgID;
        }

        if($ClusterID != "" AND $ClusterID != "all_cluster"){
            $sqlwhere .= " AND a.ClusterID = ?";
            $sqlparam[] = $ClusterID;
        }

        $sql = "SELECT
                * 
            FROM
                `ktv_ks_kpi_progress_detail_farmer` a
            WHERE
                1=1
            $sqlwhere
            GROUP BY a.MemberID
        ";
        $query = $this->db->query($sql,$sqlparam);
        $return['data']         = $query->result_array();
        $return['count_data']   = $query->num_rows();

        $return['success'] = true;
        return $return;
    }

    public function farmers_registered_export($DateGenerated,$ProgID,$ClusterID){
        $sqlwhere = "";
        $sqlparam = array();

        if($DateGenerated != ""){
            $sqlwhere .= " AND DATE(a.DateGenerated) = ?";
            $sqlparam[] = $DateGenerated;
        }

        if($ProgID != ""){
            $sqlwhere .= " AND a.ProgID = ?";
            $sqlparam[] = $ProgID;
        }

        if($ClusterID != "" AND $ClusterID != "all_cluster"){
            $sqlwhere .= " AND a.ClusterID = ?";
            $sqlparam[] = $ClusterID;
        }

        $sql = "SELECT
                * 
            FROM
                `ktv_st_kpi_summary_farmer_register` a
            WHERE
                1=1
            $sqlwhere
        ";
        $query = $this->db->query($sql,$sqlparam);
        $return['data']         = $query->result_array();
        $return['count_data']   = $query->num_rows();

        $return['success'] = true;
        return $return;
    }

    public function palm_plantation_ks_export($DateGenerated,$ProgID,$ClusterID){
        $sqlwhere = "";
        $sqlparam = array();

        if($DateGenerated != ""){
            $sqlwhere .= " AND DATE(a.DateGenerated) = ?";
            $sqlparam[] = $DateGenerated;
        }

        if($ProgID != ""){
            $sqlwhere .= " AND a.ProgID = ?";
            $sqlparam[] = $ProgID;
        }

        if($ClusterID != "" AND $ClusterID != "all_cluster"){
            $sqlwhere .= " AND a.ClusterID = ?";
            $sqlparam[] = $ClusterID;
        }

        $sql = "SELECT
                * 
            FROM
                `ktv_ks_kpi_progress_detail_plantation` a
            WHERE
                1=1
            $sqlwhere
            GROUP BY a.MemberID, a.PlotNr, a.SurveyNr
        ";
        $query = $this->db->query($sql,$sqlparam);
        $return['data']         = $query->result_array();
        $return['count_data']   = $query->num_rows();

        $return['success'] = true;
        return $return;
    }

    public function farm_registered_export($DateGenerated,$ProgID,$ClusterID){
        $sqlwhere = "";
        $sqlparam = array();

        if($DateGenerated != ""){
            $sqlwhere .= " AND DATE(a.DateGenerated) = ?";
            $sqlparam[] = $DateGenerated;
        }

        if($ProgID != ""){
            $sqlwhere .= " AND a.ProgID = ?";
            $sqlparam[] = $ProgID;
        }

        if($ClusterID != "" AND $ClusterID != "all_cluster"){
            $sqlwhere .= " AND a.ClusterID = ?";
            $sqlparam[] = $ClusterID;
        }

        $sql = "SELECT
                * 
            FROM
                `ktv_st_kpi_summary_farm_register` a
            WHERE
                1=1
            $sqlwhere
        ";
        $query = $this->db->query($sql,$sqlparam);
        $return['data']         = $query->result_array();
        $return['count_data']   = $query->num_rows();

        $return['success'] = true;
        return $return;
    }

    public function palm_plantation_area_ks_export($DateGenerated,$ProgID,$ClusterID){
        $sqlwhere = "";
        $sqlparam = array();

        if($DateGenerated != ""){
            $sqlwhere .= " AND DATE(a.DateGenerated) = ?";
            $sqlparam[] = $DateGenerated;
        }

        if($ProgID != ""){
            $sqlwhere .= " AND a.ProgID = ?";
            $sqlparam[] = $ProgID;
        }

        if($ClusterID != "" AND $ClusterID != "all_cluster"){
            $sqlwhere .= " AND a.ClusterID = ?";
            $sqlparam[] = $ClusterID;
        }

        $sql = "SELECT
                * 
            FROM
                `ktv_ks_kpi_progress_detail_plantation` a
            WHERE
                1=1
            $sqlwhere
            GROUP BY a.MemberID, a.PlotNr, a.SurveyNr
        ";
        $query = $this->db->query($sql,$sqlparam);
        $return['data']         = $query->result_array();
        $return['count_data']   = $query->num_rows();

        $return['success'] = true;
        return $return;
    }

    public function farm_ha_export($DateGenerated,$ProgID,$ClusterID){
        $sqlwhere = "";
        $sqlparam = array();

        if($DateGenerated != ""){
            $sqlwhere .= " AND DATE(a.DateGenerated) = ?";
            $sqlparam[] = $DateGenerated;
        }

        if($ProgID != ""){
            $sqlwhere .= " AND a.ProgID = ?";
            $sqlparam[] = $ProgID;
        }

        if($ClusterID != "" AND $ClusterID != "all_cluster"){
            $sqlwhere .= " AND a.ClusterID = ?";
            $sqlparam[] = $ClusterID;
        }

        $sql = "SELECT
                * 
            FROM
                `ktv_st_kpi_summary_farm_register` a
            WHERE
                1=1
            $sqlwhere
        ";
        $query = $this->db->query($sql,$sqlparam);
        $return['data']         = $query->result_array();
        $return['count_data']   = $query->num_rows();

        $return['success'] = true;
        return $return;
    }

    public function soc_sel_export($DateGenerated,$ProgID,$ClusterID){
        $sqlwhere = "";
        $sqlparam = array();

        if($DateGenerated != ""){
            $sqlwhere .= " AND DATE(a.DateGenerated) = ?";
            $sqlparam[] = $DateGenerated;
        }

        if($ProgID != ""){
            $sqlwhere .= " AND a.ProgID = ?";
            $sqlparam[] = $ProgID;
        }

        if($ClusterID != "" AND $ClusterID != "all_cluster"){
            $sqlwhere .= " AND a.ClusterID = ?";
            $sqlparam[] = $ClusterID;
        }

        $sql = "SELECT
                * 
            FROM
                `ktv_st_kpi_summary_soc_sel` a
            WHERE
                1=1
            $sqlwhere
        ";
        $query = $this->db->query($sql,$sqlparam);
        $return['data']         = $query->result_array();
        $return['count_data']   = $query->num_rows();

        $return['success'] = true;
        return $return;
    }

    public function farmer_survey_export($DateGenerated,$ProgID,$ClusterID){
        $sqlwhere = "";
        $sqlparam = array();

        if($DateGenerated != ""){
            $sqlwhere .= " AND DATE(a.DateGenerated) = ?";
            $sqlparam[] = $DateGenerated;
        }

        if($ProgID != ""){
            $sqlwhere .= " AND a.ProgID = ?";
            $sqlparam[] = $ProgID;
        }

        if($ClusterID != "" AND $ClusterID != "all_cluster"){
            $sqlwhere .= " AND a.ClusterID = ?";
            $sqlparam[] = $ClusterID;
        }

        $sql = "SELECT
                * 
            FROM
                `ktv_st_kpi_summary_farmer_survey` a
            WHERE
                1=1
            $sqlwhere
        ";
        $query = $this->db->query($sql,$sqlparam);
        $return['data']         = $query->result_array();
        $return['count_data']   = $query->num_rows();

        $return['success'] = true;
        return $return;
    }

    public function farm_survey_export($DateGenerated,$ProgID,$ClusterID){
        $sqlwhere = "";
        $sqlparam = array();

        if($DateGenerated != ""){
            $sqlwhere .= " AND DATE(a.DateGenerated) = ?";
            $sqlparam[] = $DateGenerated;
        }

        if($ProgID != ""){
            $sqlwhere .= " AND a.ProgID = ?";
            $sqlparam[] = $ProgID;
        }

        if($ClusterID != "" AND $ClusterID != "all_cluster"){
            $sqlwhere .= " AND a.ClusterID = ?";
            $sqlparam[] = $ClusterID;
        }

        $sql = "SELECT
                * 
            FROM
                `ktv_st_kpi_summary_farm_survey` a
            WHERE
                1=1
            $sqlwhere
        ";
        $query = $this->db->query($sql,$sqlparam);
        $return['data']         = $query->result_array();
        $return['count_data']   = $query->num_rows();

        $return['success'] = true;
        return $return;
    }

    public function polygon_mapping_export($DateGenerated,$ProgID,$ClusterID){
        $sqlwhere = "";
        $sqlparam = array();

        if($DateGenerated != ""){
            $sqlwhere .= " AND DATE(a.DateGenerated) = ?";
            $sqlparam[] = $DateGenerated;
        }

        if($ProgID != ""){
            $sqlwhere .= " AND a.ProgID = ?";
            $sqlparam[] = $ProgID;
        }

        if($ClusterID != "" AND $ClusterID != "all_cluster"){
            $sqlwhere .= " AND a.ClusterID = ?";
            $sqlparam[] = $ClusterID;
        }

        $sql = "SELECT
                a.*
                ,ST_ASTEXT(a.Polygon, 'axis-order=long-lat') as polygon_format 
            FROM
                `ktv_st_kpi_summary_polygon_mapping` a
            WHERE
                1=1
            $sqlwhere
        ";
        $query = $this->db->query($sql,$sqlparam);
        $data  = $query->result_array();

        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $polygon_format = $value["polygon_format"];
                unset($data[$key]["polygon_format"]);

                $data[$key]["Polygon"] = $polygon_format;
            }
        }

        $return['data']         = $data;
        $return['count_data']   = $query->num_rows();

        $return['success'] = true;
        return $return;
    }

    public function individual_farmer_coaching_export($DateGenerated,$ProgID,$ClusterID){
        $sqlwhere = "";
        $sqlparam = array();

        if($DateGenerated != ""){
            $sqlwhere .= " AND DATE(a.DateGenerated) = ?";
            $sqlparam[] = $DateGenerated;
        }

        if($ProgID != ""){
            $sqlwhere .= " AND a.ProgID = ?";
            $sqlparam[] = $ProgID;
        }

        if($ClusterID != "" AND $ClusterID != "all_cluster"){
            $sqlwhere .= " AND a.ClusterID = ?";
            $sqlparam[] = $ClusterID;
        }

        $sql = "SELECT
                * 
            FROM
                `ktv_st_kpi_summary_farmer_coaching` a
            WHERE
                1=1
            $sqlwhere
        ";
        $query = $this->db->query($sql,$sqlparam);
        $return['data']         = $query->result_array();
        $return['count_data']   = $query->num_rows();

        $return['success'] = true;
        return $return;
    }

    public function individual_farmer_coaching_session_export($DateGenerated,$ProgID,$ClusterID){
        $sqlwhere = "";
        $sqlparam = array();

        if($DateGenerated != ""){
            $sqlwhere .= " AND DATE(a.DateGenerated) = ?";
            $sqlparam[] = $DateGenerated;
        }

        if($ProgID != ""){
            $sqlwhere .= " AND a.ProgID = ?";
            $sqlparam[] = $ProgID;
        }

        if($ClusterID != "" AND $ClusterID != "all_cluster"){
            $sqlwhere .= " AND a.ClusterID = ?";
            $sqlparam[] = $ClusterID;
        }

        $sql = "SELECT
                * 
            FROM
                `ktv_st_kpi_summary_farmer_coaching_session` a
            WHERE
                1=1
            $sqlwhere
        ";
        $query = $this->db->query($sql,$sqlparam);
        $return['data']         = $query->result_array();
        $return['count_data']   = $query->num_rows();

        $return['success'] = true;
        return $return;
    }

    public function broadcast_sms_export($DateGenerated,$ProgID,$ClusterID){
        $sqlwhere = "";
        $sqlparam = array();

        if($DateGenerated != ""){
            $sqlwhere .= " AND DATE(a.DateGenerated) = ?";
            $sqlparam[] = $DateGenerated;
        }

        if($ProgID != ""){
            $sqlwhere .= " AND a.ProgID = ?";
            $sqlparam[] = $ProgID;
        }

        if($ClusterID != "" AND $ClusterID != "all_cluster"){
            $sqlwhere .= " AND a.ClusterID = ?";
            $sqlparam[] = $ClusterID;
        }

        $sql = "SELECT
                * 
            FROM
                `ktv_st_kpi_summary_broadcast_sms` a
            WHERE
                1=1
            $sqlwhere
        ";
        $query = $this->db->query($sql,$sqlparam);
        $return['data']         = $query->result_array();
        $return['count_data']   = $query->num_rows();

        $return['success'] = true;
        return $return;
    }

    public function farmer_id_card_export($DateGenerated,$ProgID,$ClusterID){
        $sqlwhere = "";
        $sqlparam = array();

        if($DateGenerated != ""){
            $sqlwhere .= " AND DATE(a.DateGenerated) = ?";
            $sqlparam[] = $DateGenerated;
        }

        if($ProgID != ""){
            $sqlwhere .= " AND a.ProgID = ?";
            $sqlparam[] = $ProgID;
        }

        if($ClusterID != "" AND $ClusterID != "all_cluster"){
            $sqlwhere .= " AND a.ClusterID = ?";
            $sqlparam[] = $ClusterID;
        }

        $sql = "SELECT
                * 
            FROM
                `ktv_st_kpi_summary_farmer_idcard` a
            WHERE
                1=1
            $sqlwhere
        ";
        $query = $this->db->query($sql,$sqlparam);
        $return['data']         = $query->result_array();
        $return['count_data']   = $query->num_rows();

        $return['success'] = true;
        return $return;
    }

    public function farmx_export($DateGenerated,$ProgID,$ClusterID){
        $sqlwhere = "";
        $sqlparam = array();

        if($DateGenerated != ""){
            $sqlwhere .= " AND DATE(a.DateGenerated) = ?";
            $sqlparam[] = $DateGenerated;
        }

        if($ProgID != ""){
            $sqlwhere .= " AND a.ProgID = ?";
            $sqlparam[] = $ProgID;
        }

        if($ClusterID != "" AND $ClusterID != "all_cluster"){
            $sqlwhere .= " AND a.ClusterID = ?";
            $sqlparam[] = $ClusterID;
        }

        $sql = "SELECT
                * 
            FROM
                `ktv_st_kpi_summary_farmxuser` a
            WHERE
                1=1
            $sqlwhere
        ";
        $query = $this->db->query($sql,$sqlparam);
        $return['data']         = $query->result_array();
        $return['count_data']   = $query->num_rows();

        $return['success'] = true;
        return $return;
    }

    public function farmgatetrace_ks_export($DateGenerated,$ProgID,$ClusterID){
        $sqlwhere = "";
        $sqlparam = array();

        if($DateGenerated != ""){
            $sqlwhere .= " AND DATE(a.DateGenerated) = ?";
            $sqlparam[] = $DateGenerated;
        }

        if($ProgID != ""){
            $sqlwhere .= " AND a.ProgID = ?";
            $sqlparam[] = $ProgID;
        }

        if($ClusterID != "" AND $ClusterID != "all_cluster"){
            $sqlwhere .= " AND a.ClusterID = ?";
            $sqlparam[] = $ClusterID;
        }

        $sql = "SELECT
                * 
            FROM
                `ktv_ks_kpi_progress_detail_farmgate` a
            WHERE
                1=1
            $sqlwhere
            GROUP BY a.UserId
        ";
        $query = $this->db->query($sql,$sqlparam);
        $return['data']         = $query->result_array();
        $return['count_data']   = $query->num_rows();

        $return['success'] = true;
        return $return;
    }

    public function farmgate_ks_export($DateGenerated,$ProgID,$ClusterID){
        $sqlwhere = "";
        $sqlparam = array();

        if($DateGenerated != ""){
            $sqlwhere .= " AND DATE(a.DateGenerated) = ?";
            $sqlparam[] = $DateGenerated;
        }

        if($ProgID != ""){
            $sqlwhere .= " AND a.ProgID = ?";
            $sqlparam[] = $ProgID;
        }

        if($ClusterID != "" AND $ClusterID != "all_cluster"){
            $sqlwhere .= " AND a.ClusterID = ?";
            $sqlparam[] = $ClusterID;
        }

        $sql = "SELECT
                * 
            FROM
                `ktv_ks_kpi_progress_detail_farmgate` a
            WHERE
                1=1
            $sqlwhere
            GROUP BY a.UserId
        ";
        $query = $this->db->query($sql,$sqlparam);
        $return['data']         = $query->result_array();
        $return['count_data']   = $query->num_rows();

        $return['success'] = true;
        return $return;
    }

    public function farmgate_export($DateGenerated,$ProgID,$ClusterID){
        $sqlwhere = "";
        $sqlparam = array();

        if($DateGenerated != ""){
            $sqlwhere .= " AND DATE(a.DateGenerated) = ?";
            $sqlparam[] = $DateGenerated;
        }

        if($ProgID != ""){
            $sqlwhere .= " AND a.ProgID = ?";
            $sqlparam[] = $ProgID;
        }

        if($ClusterID != "" AND $ClusterID != "all_cluster"){
            $sqlwhere .= " AND a.ClusterID = ?";
            $sqlparam[] = $ClusterID;
        }

        $sql = "SELECT
                * 
            FROM
                `ktv_st_kpi_summary_farmguser` a
            WHERE
                1=1
            $sqlwhere
        ";
        $query = $this->db->query($sql,$sqlparam);
        $return['data']         = $query->result_array();
        $return['count_data']   = $query->num_rows();

        $return['success'] = true;
        return $return;
    }

    public function farmretail_export($DateGenerated,$ProgID,$ClusterID){
        $sqlwhere = "";
        $sqlparam = array();

        if($DateGenerated != ""){
            $sqlwhere .= " AND DATE(a.DateGenerated) = ?";
            $sqlparam[] = $DateGenerated;
        }

        if($ProgID != ""){
            $sqlwhere .= " AND a.ProgID = ?";
            $sqlparam[] = $ProgID;
        }

        if($ClusterID != "" AND $ClusterID != "all_cluster"){
            $sqlwhere .= " AND a.ClusterID = ?";
            $sqlparam[] = $ClusterID;
        }

        $sql = "SELECT
                * 
            FROM
                `ktv_st_kpi_summary_farmruser` a
            WHERE
                1=1
            $sqlwhere
        ";
        $query = $this->db->query($sql,$sqlparam);
        $return['data']         = $query->result_array();
        $return['count_data']   = $query->num_rows();

        $return['success'] = true;
        return $return;
    }

    public function farmcloud_export($DateGenerated,$ProgID,$ClusterID){
        $sqlwhere = "";
        $sqlparam = array();

        if($DateGenerated != ""){
            $sqlwhere .= " AND DATE(a.DateGenerated) = ?";
            $sqlparam[] = $DateGenerated;
        }

        if($ProgID != ""){
            $sqlwhere .= " AND a.ProgID = ?";
            $sqlparam[] = $ProgID;
        }

        if($ClusterID != "" AND $ClusterID != "all_cluster"){
            $sqlwhere .= " AND a.ClusterID = ?";
            $sqlparam[] = $ClusterID;
        }

        $sql = "SELECT
                * 
            FROM
                `ktv_st_kpi_summary_farmcuser` a
            WHERE
                1=1
            $sqlwhere
        ";
        $query = $this->db->query($sql,$sqlparam);
        $return['data']         = $query->result_array();
        $return['count_data']   = $query->num_rows();

        $return['success'] = true;
        return $return;
    }

    public function farmcloud_ks_export($DateGenerated,$ProgID,$ClusterID){
        $sqlwhere = "";
        $sqlparam = array();

        if($DateGenerated != ""){
            $sqlwhere .= " AND DATE(a.DateGenerated) = ?";
            $sqlparam[] = $DateGenerated;
        }

        if($ProgID != ""){
            $sqlwhere .= " AND a.ProgID = ?";
            $sqlparam[] = $ProgID;
        }

        if($ClusterID != "" AND $ClusterID != "all_cluster"){
            $sqlwhere .= " AND a.ClusterID = ?";
            $sqlparam[] = $ClusterID;
        }

        $sql = "SELECT
                * 
            FROM
                `ktv_ks_kpi_progress_detail_farmcloud` a
            WHERE
                1=1
            $sqlwhere
            GROUP BY a.MemberID
        ";
        $query = $this->db->query($sql,$sqlparam);
        $return['data']         = $query->result_array();
        $return['count_data']   = $query->num_rows();

        $return['success'] = true;
        return $return;
    }

    public function ExportSummary($DateGenerated,$ProgID){
        $result['targeted'] = $this->GetSummaryTargeted($DateGenerated,$ProgID);
        $result['achieved'] = $this->GetSummaryAchieved($DateGenerated,$ProgID);

        return $result;
    }

    private function GetSummaryTargeted($DateGenerated,$ProgID){
        $sql = "SELECT
            fbp.ClusterName Cluster
            , SUM(IFNULL(KsMill , 0) + IFNULL(StMill , 0)) 'TargetedPalmOilMill'
            , SUM(IFNULL(FarmerReg , 0)) 'TargetedFarmerReg'
            , SUM(IFNULL(FarmReg , 0)) 'TargetedFarmReg'
            , SUM(IFNULL(Ha , 0)) 'TargetedHa'
            , SUM(IFNULL(SocSel , 0)) 'TargetedSocSel'
            , SUM(IFNULL(FarmerSurveyBP , 0)) 'TargetedFarmerSurveyBP'
            , SUM(IFNULL(FarmSurvey , 0)) 'TargetedFarmSurvey'
            , SUM(IFNULL(Polygon , 0)) 'TargetedPolygon'
            , SUM(IFNULL(FarmerCoach , 0)) 'TargetedFarmerCoach'
            , SUM(IFNULL(CoachingSess , 0)) 'TargetedCoachingSess'
            , SUM(IFNULL(Sms , 0)) 'TargetedSms'
            , SUM(IFNULL(IdCard , 0)) 'TargetedIdCard'
            , SUM(IFNULL(FarmX , 0)) 'TargetedFarmX'
            , SUM(IFNULL(FarmG , 0)) 'TargetedFarmG'
            , SUM(IFNULL(FarmR , 0)) 'TargetedFarmR'
            , SUM(IFNULL(FarmC , 0)) 'TargetedFarmC'
        FROM
            ktv_kpi_st_certification_target_ims_district kcp
        LEFT JOIN
            ktv_first_buyer_program_cluster fbp on fbp.ClusterID = kcp.ClusterID
        WHERE
            kcp.ProgID = ?
        GROUP BY
            kcp.ClusterID
        ";
        $query = $this->db->query($sql, array($ProgID));

        return $query;
    }

    private function GetSummaryAchieved($DateGenerated,$ProgID){
        $sql = "SELECT
                fbp.ClusterName Cluster
                , SUM(IFNULL(KsMill , 0) + IFNULL(StMill , 0)) 'AchievedPalmOilMill'
                , SUM(IFNULL(FarmerReg , 0)) 'AchievedFarmerReg'
                , SUM(IFNULL(FarmReg , 0)) 'AchievedFarmReg'
                , SUM(IFNULL(Ha , 0)) 'AchievedHa'
                , SUM(IFNULL(SocSel , 0)) 'AchievedSocSel'
                , SUM(IFNULL(FarmerSurveyBP , 0)) 'AchievedFarmerSurveyBP'
                , SUM(IFNULL(FarmSurvey , 0)) 'AchievedFarmSurvey'
                , SUM(IFNULL(Polygon , 0)) 'AchievedPolygon'
                , SUM(IFNULL(FarmerCoach , 0)) 'AchievedFarmerCoach'
                , SUM(IFNULL(CoachingSess , 0)) 'AchievedCoachingSess'
                , SUM(IFNULL(Sms , 0)) 'AchievedSms'
                , SUM(IFNULL(IdCard , 0)) 'AchievedIdCard'
                , SUM(IFNULL(FarmX , 0)) 'AchievedFarmX'
                , SUM(IFNULL(FarmG , 0)) 'AchievedFarmG'
                , SUM(IFNULL(FarmR , 0)) 'AchievedFarmR'
                , SUM(IFNULL(FarmC , 0)) 'AchievedFarmC'
            FROM
                ktv_certification_progress_st_ims_district_history kcp
            LEFT JOIN
                ktv_first_buyer_program_cluster fbp on fbp.ClusterID = kcp.ClusterID
            WHERE
                DATE(kcp.HisDateCreated) = ?
                AND 
                kcp.ProgID = ?
            GROUP BY
                kcp.ClusterID
        ";
        $query = $this->db->query($sql, array($DateGenerated, $ProgID));

        return $query;
    }
    
}
?>