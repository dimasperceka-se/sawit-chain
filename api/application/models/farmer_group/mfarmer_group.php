<?php

/**
 * @Author: nikolius
 * @Date:   2017-11-08 16:13:07
 * @Last Modified by:   nikolius
 * @Last Modified time: 2018-01-04 14:24:08
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Mfarmer_group extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getGridMainFarmerGroupExcel($pSearch){

        if($pSearch["ProvinceID"] != ""){
            $sqlSearch = $sqlSearch." AND b.ProvinceID = '{$pSearch['ProvinceID']}' ";
        }
        if($pSearch["DistrictID"] != ""){
            $sqlSearch = $sqlSearch." AND c.DistrictID = '{$pSearch['DistrictID']}' ";
        }
        //========== Search (End) =====================


        //BENTUK QUERY HAK AKSES =============================================== (BEGIN)
        if ($_SESSION['is_admin'] == "1") {
            $sqlHakAkses['where'] = " ";
        }else{
            $sqlHakAkses['where'] = " AND a.DistrictID IN (" . $_SESSION['daerah_access'] . ")";
        }
        //BENTUK QUERY HAK AKSES =============================================== (END)



        $sql="SELECT
                SQL_CALC_FOUND_ROWS
                a.`FarmerGroupID`
                , a.`GroupName`
                , a.`YearEstablished`
                , b.`Province`
                , c.`District`
                , COUNT(mem.`MemberID`) AS FarmerRegistered
                , IF(a.`DateUpdated` IS NULL,a.`DateCreated`,a.`DateUpdated`) AS LastUpdated
            FROM
                ktv_farmer_group a
                LEFT JOIN ktv_province b ON a.`ProvinceID` = b.`ProvinceID`
                LEFT JOIN ktv_district c ON a.`DistrictID` = c.`DistrictID`
                LEFT JOIN ktv_subdistrict d ON a.`SubDistrictID` = d.`SubDistrictID`
                LEFT JOIN ktv_village e ON a.`VillageID` = e.`VillageID`
                LEFT JOIN ktv_members mem ON
                    a.`FarmerGroupID` = mem.`FarmerGroupID` AND
                    mem.`StatusCode` = 'active'
                LEFT JOIN ktv_member_role memr ON
                    mem.`MemberID` = memr.`MemberID` AND
                    memr.`MRoleID` = '1'
            WHERE
                a.`StatusCode` = 'active'
                $sqlSearch
                {$sqlHakAkses['where']}
            GROUP BY a.`FarmerGroupID`";
            
        $query = $this->db->query($sql);
        $result = $query->result_array();

        return $result;
    }

    public function getGridMainFarmerGroup($pSearch, $start, $limit, $sortingField, $sortingDir){
        if ($sortingField == "")
            $sortingField = 'GroupName';
        if ($sortingDir == "")
            $sortingDir = 'ASC';


        //========== Search (Begin) =====================
        $sqlSearch = "";
        if($pSearch['ArrFilter'] != "") {
            $ArrTmp = explode(',',$pSearch['ArrFilter']);
            for ($i=0; $i < count($ArrTmp); $i++) { 
                switch($ArrTmp[$i]){
                    case 'id':
                        $sqlSearch = $sqlSearch." AND a.`FarmerGroupID` LIKE '%{$pSearch['TextFilterID']}%' ";
                    break;
                    case 'name':
                        $sqlSearch = $sqlSearch." AND a.`GroupName` LIKE '%{$pSearch['TextFilterName']}%' ";
                    break;
                    case 'region':
                        $sqlSearch = $sqlSearch."   AND ( (a.ProvinceID = {$pSearch['CmbFilterProvince']}) OR (0={$pSearch['CmbFilterProvince']}) )
                                                    AND ( (a.DistrictID = {$pSearch['CmbFilterDistrict']}) OR (0={$pSearch['CmbFilterDistrict']}) )
                                                    AND ( (a.SubDistrictID = {$pSearch['CmbFilterSubDistrict']}) OR (0={$pSearch['CmbFilterSubDistrict']}) )
                                                    AND ( (a.VillageID = {$pSearch['CmbFilterVillage']}) OR (0={$pSearch['CmbFilterVillage']}) ) ";
                    break;
                }
            }
        }
        if($pSearch["ProvinceID"] != ""){
            $sqlSearch = $sqlSearch." AND b.ProvinceID = '{$pSearch['ProvinceID']}' ";
        }
        if($pSearch["DistrictID"] != ""){
            $sqlSearch = $sqlSearch." AND c.DistrictID = '{$pSearch['DistrictID']}' ";
        }
        //========== Search (End) =====================


        //BENTUK QUERY HAK AKSES =============================================== (BEGIN)
        if ($_SESSION['is_admin'] == "1") {
            $sqlHakAkses['where'] = " ";
        }else{
            $sqlHakAkses['where'] = " AND a.DistrictID IN (" . $_SESSION['daerah_access'] . ")";
        }
        //BENTUK QUERY HAK AKSES =============================================== (END)



        $sql="SELECT
                SQL_CALC_FOUND_ROWS
                a.`FarmerGroupID`
                , a.`GroupName`
                , a.`YearEstablished`
                , b.`Province`
                , c.`District`
                , COUNT(mem.`MemberID`) AS FarmerRegistered
                , IF(a.`DateUpdated` IS NULL,a.`DateCreated`,a.`DateUpdated`) AS LastUpdated
            FROM
                ktv_farmer_group a
                LEFT JOIN ktv_province b ON a.`ProvinceID` = b.`ProvinceID`
                LEFT JOIN ktv_district c ON a.`DistrictID` = c.`DistrictID`
                LEFT JOIN ktv_subdistrict d ON a.`SubDistrictID` = d.`SubDistrictID`
                LEFT JOIN ktv_village e ON a.`VillageID` = e.`VillageID`
                LEFT JOIN ktv_members mem ON
                    a.`FarmerGroupID` = mem.`FarmerGroupID` AND
                    mem.`StatusCode` = 'active'
                LEFT JOIN ktv_member_role memr ON
                    mem.`MemberID` = memr.`MemberID` AND
                    memr.`MRoleID` = '1'
            WHERE
                a.`StatusCode` = 'active'
                $sqlSearch
                {$sqlHakAkses['where']}
            GROUP BY a.`FarmerGroupID`
            ORDER BY $sortingField $sortingDir
            LIMIT ?,?";
        $p = array(
            (int) $start, (int) $limit
        );
        $query = $this->db->query($sql,$p);
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        //generate information grid result (begin)
        if ($sortingDir == 'ASC') {
            $sortingInfo = 'ascending';
        }
        if ($sortingDir == 'DESC') {
            $sortingInfo = 'descending';
        }

        foreach ($pSearch as $key => $value) {
            if ($value != "") {
                switch ($key) {
                    case 'prov':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('Province') . '</li>';
                        break;
                    case 'kab':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('District') . '</li>';
                        break;
                    case 'textSearch':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('ID / Name') . '</li>';
                        break;
                }
            }
        }

        $_SESSION['informationGrid'] = '<div class="gridInformationContainer">
                                <h4>Information</h4>
                                <ul>
                                    <li>' . $query->row()->total . ' '.lang('datas, Sorted by').' ' . lang($sortingField) . ' ' . $sortingInfo . '</li>
                                    ' . $infoFilter . '
                                </ul>
                            </div>';
        //generate information grid result (end)

        return $result;
    }

    public function getFarmerGroupBasicDataForm($FarmerGroupID){
        $sql="SELECT
                a.`FarmerGroupID` AS \"Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-FarmerGroupID\",
                a.`GroupExtID` AS \"Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-GroupExtID\",
                a.`GroupName` AS \"Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-GroupName\",
                a.`YearEstablished` AS \"Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-YearEstablished\",
                a.`ProvinceID`,
                a.`DistrictID`,
                a.`SubDistrictID`,
                a.`VillageID`,
                a.`Address` AS \"Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Address\",
                IFNULL(a.`Latitude`, ST_Latitude(a.LatLong)) AS \"Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Latitude\",
                IFNULL(a.`Longitude`, ST_Longitude(a.LatLong)) AS \"Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Longitude\",
                a.`LegalStatus` AS \"Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-LegalStatus\",
                a.`HaveManagement` AS \"Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-HaveManagement\",
                a.`Chairman` AS \"Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Chairman\",
                a.`Secretary` AS \"Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Secretary\",
                a.`Treasurer` AS \"Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Treasurer\",
                a.`Remarks` AS \"Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-Remarks\",
                a.`WagsGroupCat` AS \"Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-RowWagsGroupCat\",
                a.`ZipCode` AS \"Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-ZipCode\",
                a.HadSupportGroup AS \"Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-HadSupportGroup\",
                a.SuppGroupGovernment AS \"Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SuppGroupGovernment\",
                a.SuppGroupNGO AS \"Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SuppGroupNGO\",
                a.SuppGroupMill AS \"Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SuppGroupMill\",
                a.SuppGroupPrivate AS \"Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SuppGroupPrivate\",
                a.SuppGroupOther AS \"Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SuppGroupOther\",
                a.SuppGroupOtherText AS \"Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SuppGroupOtherText\",
                a.SuppTypeFinnance AS \"Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SuppTypeFinnance\",
                a.SuppTypeAdvisor AS \"Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SuppTypeAdvisor\",
                a.SuppTypeTraining AS \"Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SuppTypeTraining\",
                a.SuppTypeOther AS \"Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SuppTypeOther\",
                a.SuppTypeOtherText AS \"Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SuppTypeOtherText\",
                a.TrainingFinance AS \"Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-TrainingFinance\",
                a.TrainingAgriculture AS \"Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-TrainingAgriculture\",
                a.TrainingRights AS \"Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-TrainingRights\",
                a.TrainingOther AS \"Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-TrainingOther\",
                a.TrainingOtherText AS \"Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-TrainingOtherText\",
                a.TrainingHCVHCS AS \"Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-TrainingHCVHCS\",
                a.TrainingRSPO AS \"Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-TrainingRSPO\",
                a.TrainingBestManagement AS \"Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-TrainingBestManagement\",
                a.TrainingFireFighter AS \"Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-TrainingFireFighter\",
                a.SMERelation AS \"Koltiva.view.FarmerGroup.FormMainFarmerGroup-FormBasicData-SMERelation\"
            FROM
                `ktv_farmer_group` a
            WHERE
                a.`FarmerGroupID` = ?
            LIMIT 1";
        $query = $this->db->query($sql,array((int) $FarmerGroupID));
        $data = $query->row_array();

        $return['success'] = true;
        $return['data'] = $data;
        return $return;
    }

    public function genFarmerGroupID($DistrictID){
        $awalan = $DistrictID;
        $sql="SELECT
                a.`FarmerGroupID`
            FROM
                ktv_farmer_group a
            WHERE
                a.`FarmerGroupID` LIKE '$awalan%'
            ORDER BY a.`FarmerGroupID` DESC
            LIMIT 1";
        $query = $this->db->query($sql);
        $data = $query->row_array();

        if($data['FarmerGroupID'] != ""){
            $temp = (int) substr($data['FarmerGroupID'],-4);
            $temp++;

            switch (strlen($temp)) {
                case '1':
                    $temp = $awalan."000".$temp;
                break;
                case '2':
                    $temp = $awalan."00".$temp;
                break;
                case '3':
                    $temp = $awalan."0".$temp;
                break;
                default:
                    $temp = $awalan.$temp;
                break;
            }
            return $temp;
        }else{
            return $awalan."0001";
        }
    }

    public function insertFarmerGroup($varPost){
        $this->db->trans_begin();

        //rapikan variable post (begin)
        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_FarmerGroup_FormMainFarmerGroup-FormBasicData-", '', $key);
            if($varPost[$key] == ""){
                $paramPost[$keyNew] = null;
            }

            $paramPost[$keyNew] = $value;
        }

        $paramPost['ProvinceID'] = $paramPost['Province'];
        $paramPost['DistrictID'] = $paramPost['District'];
        $paramPost['SubDistrictID'] = $paramPost['Subdistrict'];
        $paramPost['VillageID'] = $paramPost['Village'];

        unset($paramPost['RowWagsGroupCat']);
        unset($paramPost['FarmerGroupID']);
        unset($paramPost['Province']);
        unset($paramPost['District']);
        unset($paramPost['Subdistrict']);
        unset($paramPost['Village']);
        unset($paramPost['RowWagsGroupCat']);

        //generate MemberID dan MemberDisplayID
        $this->load->model('grower/mgrower');
        $FarmerGroupID = $this->genFarmerGroupID($varPost['Koltiva_view_FarmerGroup_FormMainFarmerGroup-FormBasicData-District']);
        $uid = $this->mgrower->getUID();

        $paramPost['FarmerGroupID'] = $FarmerGroupID;
        $paramPost['uid']           = $uid;
        $paramPost['DateCreated'] = date("Y-m-d H:i:s");
        $paramPost['CreatedBy'] = $_SESSION['userid'];

        $query = $this->db->insert('ktv_farmer_group',$paramPost);

        //Koordinat Geometry ============= (Begin)
        if($paramPost['Latitude'] != "" && $paramPost['Longitude'] != "") {
            $PointInsert = "POINT({$paramPost['Latitude']} {$paramPost['Longitude']})";

            $sql = "UPDATE ktv_farmer_group a SET
                        a.`LatLong` = ST_GEOMFROMTEXT('$PointInsert', 4326)
                    WHERE
                        a.`FarmerGroupID` = ?
                    LIMIT 1";
            $p = array(
                $FarmerGroupID
            );
            $query = $this->db->query($sql,$p);
        }
        //Koordinat Geometry ============= (End)

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Data saved";
            $results['FarmerGroupID'] = $FarmerGroupID;
        }

        return $results;
    }

    public function updateFarmerGroup($varPost){
        $this->db->trans_begin();
        
        //rapikan variable post (begin)
        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_FarmerGroup_FormMainFarmerGroup-FormBasicData-", '', $key);
            if($varPost[$key] == ""){
                $paramPost[$keyNew] = null;
            }

            $paramPost[$keyNew] = $value;
        }

        $FarmerGroupID = $paramPost['FarmerGroupID'];
        $paramPost['ProvinceID'] = $paramPost['Province'];
        $paramPost['DistrictID'] = $paramPost['District'];
        $paramPost['SubDistrictID'] = $paramPost['Subdistrict'];
        $paramPost['VillageID'] = $paramPost['Village'];

        unset($paramPost['RowWagsGroupCat']);
        unset($paramPost['FarmerGroupID']);
        unset($paramPost['Province']);
        unset($paramPost['District']);
        unset($paramPost['Subdistrict']);
        unset($paramPost['Village']);

        // echo "<pre>";
        // print_r($paramPost);
        // die;

        $this->db->where('FarmerGroupID',$FarmerGroupID);
        $this->db->update("ktv_farmer_group",$paramPost);

        //Koordinat Geometry ============= (Begin)
        if($paramPost['Latitude'] != "" && $paramPost['Longitude'] != "") {

            $LatitudeProses = (float) $paramPost['Latitude'];
            $LongitudeProses = (float) $paramPost['Longitude'];
            
            //Check Latitude
            if( ($LatitudeProses >= -90 && $LatitudeProses <= 90) && ($LongitudeProses >= -180 && $LongitudeProses <= 180) ) {

                //Cek valid tidak koordinatnya
                $sql2 = "SELECT ST_IsValid(ST_GeomFromText('POINT({$LatitudeProses} {$LongitudeProses})', 4326)) AS HasilCek";
                $DataCekKoordinat = $this->db->query($sql2)->row_array();
                
                if ($DataCekKoordinat['HasilCek'] == "1") {
                    $PointInsert = "POINT({$LatitudeProses} {$LongitudeProses})";

                    $sql2 = "UPDATE ktv_farmer_group a SET
                                a.`LatLong` = ST_GEOMFROMTEXT('$PointInsert', 4326)
                            WHERE
                                a.`FarmerGroupID` = ?
                            LIMIT 1";
                    $p = array(
                        $paramPost["FarmerGroupID"]
                    );
                    $query = $this->db->query($sql2,$p);
                }

            }
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Data saved";
            $results['FarmerGroupID'] = $varPost['Koltiva_view_FarmerGroup_FormMainFarmerGroup-FormBasicData-FarmerGroupID'];
        }

        return $results;
    }

    public function deleteFarmerGroup($FarmerGroupID){
        $this->db->trans_begin();

        $sql="UPDATE `ktv_farmer_group` SET
                  StatusCode = 'nullified',
                  `DateUpdated` = NOW(),
                  `LastModifiedBy` = '{$_SESSION['userid']}'
                WHERE
                    `FarmerGroupID` = ?
                LIMIT 1";
        $query = $this->db->query($sql, array($FarmerGroupID));

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to delete data";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Data deleted";
        }

        return $results;
    }

    public function getFarmerGroupMemberPanelGrid($FarmerGroupID,$start,$limit,$sortingField, $sortingDir){
        //generate filter hak akses (begin)
        if ($_SESSION['is_admin'] == "1") {
            $sqlHakAkses['join'] = "";
            $sqlHakAkses['where'] = "";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program") {
            //cek ktv_access_staff
            $sqlHakAkses['where'] = " AND kd.DistrictID IN (" . $_SESSION['daerah_access'] . ")";

            //cek ktv_access_partner_member
            $sqlHakAkses['join'] = " INNER JOIN ktv_access_partner_member acc_pm ON a.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$_SESSION['PartnerID']}' ";
        } else {
            //cek ktv_access_staff
            $sqlHakAkses['where'] = " AND kd.DistrictID IN (" . $_SESSION['daerah_access'] . ")";
            $sqlHakAkses['join'] = "";
        }
        //generate filter hak akses (end)

        if ($sortingField == "")
            $sortingField = 'MemberDisplayID';
        if ($sortingDir == "")
            $sortingDir = 'ASC';

        $sql="SELECT
                SQL_CALC_FOUND_ROWS
                '{$FarmerGroupID}' AS FarmerGroupID
                , a.`MemberDisplayID`
                , a.`MemberName`
                , vil.`Village`
                , a.MemberID
                , s.UserRealName Enumerator
            FROM
                ktv_members a
                INNER JOIN ktv_member_role b ON a.`MemberID` = b.`MemberID` AND b.`MRoleID` = '1'
                LEFT JOIN ktv_village vil ON a.`VillageID` = vil.`VillageID`
                LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = vil.SubDistrictID
                LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
                LEFT JOIN sys_user s ON s.UserId = a.CreatedBy
                {$sqlHakAkses['join']}
            WHERE
                a.`StatusCode` = 'active'
                AND a.`FarmerGroupID` = ?
                {$sqlHakAkses['where']}
            ORDER BY $sortingField $sortingDir
            LIMIT ?,?
            ";
        $p = array(
            $FarmerGroupID, (int) $start, (int) $limit
        );
        $query = $this->db->query($sql, $p);
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    public function getFarmerGroupMemberPlotExcel($FarmerGroupID){
        //generate filter hak akses (begin)
        if ($_SESSION['is_admin'] == "1") {
            $sqlHakAkses['join'] = "";
            $sqlHakAkses['where'] = "";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program") {
            //cek ktv_access_staff
            $sqlHakAkses['where'] = " AND kd.DistrictID IN (" . $_SESSION['daerah_access'] . ")";

            //cek ktv_access_partner_member
            $sqlHakAkses['join'] = " INNER JOIN ktv_access_partner_member acc_pm ON a.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$_SESSION['PartnerID']}' ";
        } else {
            //cek ktv_access_staff
            $sqlHakAkses['where'] = " AND kd.DistrictID IN (" . $_SESSION['daerah_access'] . ")";
            $sqlHakAkses['join'] = "";
        }
        //generate filter hak akses (end)

        if ($sortingField == "")
            $sortingField = 'MemberDisplayID';
        if ($sortingDir == "")
            $sortingDir = 'ASC';
        
        $sql="SELECT
            SQL_CALC_FOUND_ROWS
            fg.FarmerGroupID 'Farmer Group  ID'
            , fg.GroupName 'Group Name'
            , a.`MemberDisplayID` 'Farmer ID'
            , a.`MemberName` 'Farmer Name'
            , sp.PlotNr
            , CONCAT(sp.SurveyNr, ' - ', ks.SurveyTxt) 'Survey Nr'
            , sp.FirstPlantingYear 'Planting Year'
            , sp.AnnualProduction 'Annual Production'
            , sp.PlantationProductivity 'Plantation Productivity (Ton/Ha)'
            , CASE
                WHEN sp.OwnershipDoc = 1 THEN '".lang('No Document')."'
                WHEN sp.OwnershipDoc = 2 THEN '".lang('SKT')."'
                WHEN sp.OwnershipDoc = 3 THEN '".lang('SHM/Sertifikat')."'
                WHEN sp.OwnershipDoc = 4 THEN '".lang('HGU')."'
                WHEN sp.OwnershipDoc = 5 THEN '".lang('SKGR')."'
                WHEN sp.OwnershipDoc = 6 THEN '".lang('Other')."'
                ELSE '-'
            END 'Land Ownership'
            , CASE
                WHEN sp.SoilType = 1 THEN '".lang('Mineral')."'
                WHEN sp.SoilType = 2 THEN '".lang('Peat')."'
                WHEN sp.SoilType = 3 THEN '".lang('Sandy')."'
                ELSE '-'
            END 'Soil Type'
        FROM
            ktv_members a
            INNER JOIN ktv_member_role b ON a.`MemberID` = b.`MemberID` AND b.`MRoleID` = '1'
            LEFT JOIN ktv_village vil ON a.`VillageID` = vil.`VillageID`
            LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = vil.SubDistrictID
            LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
            LEFT JOIN sys_user s ON s.UserId = a.CreatedBy
            LEFT JOIN ktv_farmer_group fg on fg.FarmerGroupID = a.FarmerGroupID
            LEFT JOIN ktv_survey_plot sp on sp.MemberID = a.MemberID
            LEFT JOIN ktv_survey ks on ks.SurveyNr = sp.SurveyNr
            {$sqlHakAkses['join']}
        WHERE
            a.`StatusCode` = 'active'
            AND a.`FarmerGroupID` = ?
            {$sqlHakAkses['where']}
            ORDER BY a.MemberName ASC";

        $p = array(
            $FarmerGroupID
        );
        
        $query = $this->db->query($sql, $p);

        return $query->result_array();
    }

    public function getFarmerGroupMemberExcel($FarmerGroupID){
        //generate filter hak akses (begin)
        if ($_SESSION['is_admin'] == "1") {
            $sqlHakAkses['join'] = "";
            $sqlHakAkses['where'] = "";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program") {
            //cek ktv_access_staff
            $sqlHakAkses['where'] = " AND kd.DistrictID IN (" . $_SESSION['daerah_access'] . ")";

            //cek ktv_access_partner_member
            $sqlHakAkses['join'] = " INNER JOIN ktv_access_partner_member acc_pm ON a.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$_SESSION['PartnerID']}' ";
        } else {
            //cek ktv_access_staff
            $sqlHakAkses['where'] = " AND kd.DistrictID IN (" . $_SESSION['daerah_access'] . ")";
            $sqlHakAkses['join'] = "";
        }
        //generate filter hak akses (end)

        if ($sortingField == "")
            $sortingField = 'MemberDisplayID';
        if ($sortingDir == "")
            $sortingDir = 'ASC';

        $sql="SELECT
                SQL_CALC_FOUND_ROWS
                fg.FarmerGroupID 'Farmer Group  ID'
                , fg.GroupName 'Group Name'
                , a.`MemberDisplayID` 'Farmer ID'
                , a.`MemberName` 'Farmer Name'
                , a.Nin 'National ID'
                , a.Handphone
                , ksd.SubDistrict 'Subdistrict'
                , vil.`Village` 'Village'
                , s.UserRealName Enumerator
            FROM
                ktv_members a
                INNER JOIN ktv_member_role b ON a.`MemberID` = b.`MemberID` AND b.`MRoleID` = '1'
                LEFT JOIN ktv_village vil ON a.`VillageID` = vil.`VillageID`
                LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = vil.SubDistrictID
                LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
                LEFT JOIN sys_user s ON s.UserId = a.CreatedBy
                LEFT JOIN ktv_farmer_group fg on fg.FarmerGroupID = a.FarmerGroupID
                {$sqlHakAkses['join']}
            WHERE
                a.`StatusCode` = 'active'
                AND a.`FarmerGroupID` = ?
                {$sqlHakAkses['where']}
                GROUP BY a.MemberID
                ORDER BY a.MemberName ASC
            ";
        $p = array(
            $FarmerGroupID
        );
        $query = $this->db->query($sql, $p);

        return $query->result_array();
    }

    public function getFarmerGroupMemberInputGrid($FarmerGroupID,$textSearch,$ProvinceIDnew,$DistrictID,$SubdistrictID,$VillageID,$start,$limit,$sortingField, $sortingDir,$Enumerator = null){

        //get ProvinceID (begin)
        $sql="SELECT
                a.`ProvinceID`
            FROM
                ktv_farmer_group a
            WHERE
                a.`FarmerGroupID` = ?
            LIMIT 1";
        $query = $this->db->query($sql, array($FarmerGroupID));
        $data = $query->row_array();
        $ProvinceID = $data['ProvinceID'];
        //get ProvinceID (end)

        $ProvinceID = ($ProvinceIDnew != '')? $ProvinceIDnew : $ProvinceID;

        //generate filter hak akses (begin)
        if ($_SESSION['is_admin'] == "1") {
            $sqlHakAkses['join'] = "";
            $sqlHakAkses['where'] = "";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program") {
            //cek ktv_access_staff
            $sqlHakAkses['where'] = " AND dis.DistrictID IN (" . $_SESSION['daerah_access'] . ")";

            //cek ktv_access_partner_member
            $sqlHakAkses['join'] = " INNER JOIN ktv_access_partner_member acc_pm ON a.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$_SESSION['PartnerID']}' ";
        } else {
            //cek ktv_access_staff
            $sqlHakAkses['where'] = " AND dis.DistrictID IN (" . $_SESSION['daerah_access'] . ")";
            $sqlHakAkses['join'] = "";
        }
        //generate filter hak akses (end)

        //filter region
        ($DistrictID != '')? $sqlHakAkses['where'] = " AND dis.DistrictID = '$DistrictID'" : '';
        ($SubdistrictID != '')? $sqlHakAkses['where'] = " AND subd.SubDistrictID = '$SubdistrictID'" : '';
        ($VillageID != '')? $sqlHakAkses['where'] = " AND a.VillageID = '$VillageID'" : '';

        if ($sortingField == "")
            $sortingField = 'MemberName';
        if ($sortingDir == "")
            $sortingDir = 'ASC';

        if($Enumerator != null || $Enumerator != ''){
            $sqlHakAkses['where'] = " AND a.CreatedBy = '$Enumerator'";
        }

        $sql="SELECT
                SQL_CALC_FOUND_ROWS
                a.MemberID
                , a.`MemberDisplayID`
                , a.`MemberName`
                , subd.SubDistrict
                , vil.`Village`
                , s.UserRealName Enumerator
            FROM
                ktv_members a
                INNER JOIN ktv_member_role b ON a.`MemberID` = b.`MemberID` AND b.`MRoleID` = '1'
                LEFT JOIN ktv_village vil ON a.`VillageID` = vil.`VillageID`
                LEFT JOIN ktv_subdistrict subd ON vil.SubDistrictID = subd.SubDistrictID
                LEFT JOIN ktv_district dis ON subd.DistrictID = dis.DistrictID
                LEFT JOIN ktv_province prov ON dis.ProvinceID = prov.ProvinceID
                LEFT JOIN sys_user s ON s.UserId = a.CreatedBy
                {$sqlHakAkses['join']}
            WHERE
                a.`StatusCode` = 'active'
                AND prov.ProvinceID = ?
                AND ( a.`FarmerGroupID` IS NULL AND a.CoopID IS NULL)
                AND ( a.MemberName LIKE ? OR a.MemberDisplayID LIKE ? )
                {$sqlHakAkses['where']}
            ORDER BY $sortingField $sortingDir
            LIMIT ?,?";
        $query = $this->db->query($sql, array(
            $ProvinceID,
            '%'.$textSearch.'%', '%'.$textSearch.'%',
            (int) $start, (int) $limit)
        );
		
        // echo "<pre>";print_r($sqlHakAkses);die;
        
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    public function inputFarmerGroupMember($arrMemberID,$FarmerGroupID){
        $this->db->trans_begin();

        $impMemberID = implode(",",$arrMemberID);

        $sql="UPDATE ktv_members a SET
                    a.`FarmerGroupID` = ?
                    , a.inGroup = '1'
                    , a.`DateUpdated` = NOW()
                    , a.`LastModifiedBy` = ?
                WHERE
                    a.`MemberID` IN ({$impMemberID})";
        $p = array(
            $FarmerGroupID,
            $_SESSION['userid'],
            $impMemberID
        );
        $query = $this->db->query($sql,$p);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Data saved";
        }
        return $results;
    }

    public function getUserList(){
        $sql = "SELECT
            s.UserId id,
            s.UserRealName label
        FROM
            sys_user s
        WHERE
            s.StatusCode = 'active'
        GROUP BY
            s.UserId
        ORDER BY s.UserRealName";
        $query = $this->db->query($sql,$p);
        $results['data'] = $query->result_array();
        $results['total'] = $query->num_rows();
        return $results;
    }

    public function deleteFarmerGroupMember($MemberID,$FarmerGroupID){
        $this->db->trans_begin();

        $sql="UPDATE ktv_members a SET
                    a.FarmerGroupID = null
                    , a.inGroup = '0'
                    , a.`DateUpdated` = NOW()
                    , a.`LastModifiedBy` = ?
                WHERE
                    a.MemberID = ?
                LIMIT 1";
        $p = array(
            $_SESSION['userid'],
            $MemberID
        );
        $query = $this->db->query($sql,$p);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to delete data";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Data deleted";
        }
        return $results;
    }
}