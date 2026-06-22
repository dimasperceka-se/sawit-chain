<?php
/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Thu May 02 2019
 *  File : mtph.php
 *******************************************/
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mtph extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function GetGridMainTph($pSearch,$start,$limit,$sortingField,$sortingDir){
        //========== Search (Begin) =====================
        $sqlSearch = "";
        if($pSearch['ArrFilter'] != "") {
            $ArrTmp = explode(',',$pSearch['ArrFilter']);
            for ($i=0; $i < count($ArrTmp); $i++) { 
                switch($ArrTmp[$i]){
                    case 'id':
                        $sqlSearch = $sqlSearch." AND a.CollectpointDisplayID LIKE '%{$pSearch['TextFilterID']}%' ";
                    break;
                    case 'name':
                        $sqlSearch = $sqlSearch." AND a.CollectpointName LIKE '%{$pSearch['TextFilterName']}%' ";
                    break;
                    case 'region':
                        $sqlSearch = $sqlSearch."   AND ( (prov.ProvinceID = {$pSearch['CmbFilterProvince']}) OR (0={$pSearch['CmbFilterProvince']}) )
                                                    AND ( (dis.DistrictID = {$pSearch['CmbFilterDistrict']}) OR (0={$pSearch['CmbFilterDistrict']}) )
                                                    AND ( (subd.SubDistrictID = {$pSearch['CmbFilterSubDistrict']}) OR (0={$pSearch['CmbFilterSubDistrict']}) )
                                                    AND ( (vil.VillageID = {$pSearch['CmbFilterVillage']}) OR (0={$pSearch['CmbFilterVillage']}) ) ";
                    break;
                }
            }
        }
        //========== Search (End) =====================

        //BENTUK QUERY HAK AKSES =============================================== (BEGIN)
        $sqlHakAkses = array();
        if($_SESSION['is_admin'] == "1"){
            $sqlHakAkses['join'] = "";
            $sqlHakAkses['where'] = "";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            //cek ktv_access_staff
            $sqlHakAkses['where'] = " AND dis.DistrictID IN (".$_SESSION['daerah_access'].")";

            //cek ktv_access_partner_mill
            $sqlHakAkses['join'] = "";
        } else {
            //cek ktv_access_staff
            $sqlHakAkses['join'] = "";
            $sqlHakAkses['where'] = " AND dis.DistrictID IN (".$_SESSION['daerah_access'].")";
        }
        //BENTUK QUERY HAK AKSES =============================================== (End)

        if($sortingField == "") $sortingField = 'Name';
        if($sortingDir == "") $sortingDir = 'ASC';

        $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    a.`CollectpointID`
                    , a.`CollectpointDisplayID`
                    , a.`CollectpointName` AS `Name`
                    , a.OrgType AS OrgTypeLabel
                    , ( SELECT CONCAT(sm.MemberDisplayID,' - ',sm.MemberName) FROM ktv_members sm WHERE sm.MemberID = a.`OrgID` LIMIT 1 ) AS OrgIDLabel
                    , subd.`SubDistrict`
                    , vil.`Village`
                    , a.`Latitude`
                    , a.`Longitude`
                    , IF(a.`DateUpdated` IS NULL,
                        #Created
                        CONCAT((SELECT ss.UserRealName FROM sys_user ss WHERE ss.UserId = a.`CreatedBy`),', ',DATE_FORMAT(a.`DateCreated`,'%Y-%m-%d')),
                        #Updated
                        CONCAT((SELECT ss.UserRealName FROM sys_user ss WHERE ss.UserId = a.`LastModifiedBy`),', ',DATE_FORMAT(a.`DateUpdated`,'%Y-%m-%d'))
                    ) AS LastUpdated
                FROM
                    `ktv_collecting_point` a
                    LEFT JOIN ktv_village vil ON vil.`VillageID` = a.`VillageID`
                    LEFT JOIN ktv_subdistrict subd ON vil.`SubDistrictID` = subd.`SubDistrictID`
                    LEFT JOIN ktv_district dis ON subd.`DistrictID` = dis.`DistrictID`
                    LEFT JOIN ktv_province prov ON dis.`ProvinceID` = prov.`ProvinceID`
                    {$sqlHakAkses['join']}
                WHERE
                    1=1
                    AND a.`StatusCode` = 'active'
                    $sqlSearch
                    {$sqlHakAkses['where']}
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
        if($sortingDir == 'ASC'){
            $sortingInfo = 'ascending';
        }
        if($sortingDir == 'DESC'){
            $sortingInfo = 'descending';
        }

        $infoFilter = '';
        foreach ($pSearch as $key => $value) {
            if($value != ""){
                switch ($key) {
                    case 'prov':
                        $infoFilter .= '<li>'.lang('Filter by').' '.lang('Province').'</li>';
                    break;
                    case 'kab':
                        $infoFilter .= '<li>'.lang('Filter by').' '.lang('District').'</li>';
                    break;
                    case 'kec':
                        $infoFilter .= '<li>'.lang('Filter by').' '.lang('Kecamatan').'</li>';
                    break;
                    case 'TextSearch':
                        $infoFilter .= '<li>'.lang('Filter by').' '.lang('ID / Name').'</li>';
                    break;
                }
            }
        }

        $_SESSION['informationGrid'] = '<div class="gridInformationContainer">
                                <h4>Information</h4>
                                <ul>
                                    <li>'.$query->row()->total.' '.lang('datas, Sorted by').' '.lang($sortingField).' '.$sortingInfo.'</li>
                                    '.$infoFilter.'
                                </ul>
                            </div>';
        //generate information grid result (end)

        return $result;
    }

    public function GetGridMainTphNew($MemberID,$start,$limit,$sortingField,$sortingDir){

        if($sortingField == "") $sortingField = 'a.DateCreated';
        if($sortingDir == "") $sortingDir = 'DESC';

        $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    a.`CollectpointID`
                    , a.`CollectpointDisplayID`
                    , a.`CollectpointName` AS `Name`
                    , a.OrgType AS OrgTypeLabel
                    , ( SELECT CONCAT(sm.MemberDisplayID,' - ',sm.MemberName) FROM ktv_members sm WHERE sm.MemberID = a.`OrgID` LIMIT 1 ) AS OrgIDLabel
                    , subd.`SubDistrict`
                    , vil.`Village`
                    , a.`Latitude`
                    , a.`Longitude`
                    , IF(a.`DateUpdated` IS NULL,
                        #Created
                        CONCAT((SELECT ss.UserRealName FROM sys_user ss WHERE ss.UserId = a.`CreatedBy`),', ',DATE_FORMAT(a.`DateCreated`,'%Y-%m-%d')),
                        #Updated
                        CONCAT((SELECT ss.UserRealName FROM sys_user ss WHERE ss.UserId = a.`LastModifiedBy`),', ',DATE_FORMAT(a.`DateUpdated`,'%Y-%m-%d'))
                    ) AS LastUpdated
                FROM
                    `ktv_collecting_point` a
                    LEFT JOIN ktv_village vil ON vil.`VillageID` = a.`VillageID`
                    LEFT JOIN ktv_subdistrict subd ON vil.`SubDistrictID` = subd.`SubDistrictID`
                    LEFT JOIN ktv_district dis ON subd.`DistrictID` = dis.`DistrictID`
                    LEFT JOIN ktv_province prov ON dis.`ProvinceID` = prov.`ProvinceID`
                WHERE
                    1=1
                    AND a.`StatusCode` = 'active'
                    AND a.`OrgID` = ?
                ORDER BY $sortingField $sortingDir
                LIMIT ?,?";
        $p = array(
            $MemberID, (int) $start, (int) $limit
        );
        $query = $this->db->query($sql,$p);
        $result['data'] = $query->result_array();
        // $result['sql'] = $this->db->last_query();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    public function GetTphBasicForm($CollectpointID){
        $sql = "SELECT
                    a.`CollectpointID` AS \"Koltiva.view.Tph.FormMain-CollectpointID\",
                    a.`CollectpointDisplayID` AS \"Koltiva.view.Tph.FormMain-CollectpointDisplayID\",
                    a.`OrgType` AS \"Koltiva.view.Tph.FormMain-OrgType\",
                    a.`OrgID` AS \"Koltiva.view.Tph.FormMain-OrgID\",
                    (
                        SELECT CONCAT(sm.MemberDisplayID,' - ',sm.MemberName) FROM ktv_members sm WHERE sm.MemberID = a.`OrgID` LIMIT 1
                    ) AS \"Koltiva.view.Tph.FormMain-OrgIDLabel\",
                    a.`CollectpointName` AS \"Koltiva.view.Tph.FormMain-CollectpointName\",
                    a.`CollectpointDisplayID`,
                    a.`CollectpointName`,
                    prov.`ProvinceID`,
                    dis.`DistrictID`,
                    subd.`SubDistrictID`,
                    vil.`VillageID`,
                    a.`CollectpointAddress` AS \"Koltiva.view.Tph.FormMain-CollectpointAddress\",
                    IFNULL(a.`Longitude`, ST_Y(a.`LatLong`)) AS \"Koltiva.view.Tph.FormMain-Longitude\",
                    IFNULL(a.`Latitude`, ST_X(a.`LatLong`)) AS \"Koltiva.view.Tph.FormMain-Latitude\"
                FROM
                    `ktv_collecting_point` a
                    LEFT JOIN ktv_village vil ON vil.`VillageID` = a.`VillageID`
                    LEFT JOIN ktv_subdistrict subd ON vil.`SubDistrictID` = subd.`SubDistrictID`
                    LEFT JOIN ktv_district dis ON subd.`DistrictID` = dis.`DistrictID`
                    LEFT JOIN ktv_province prov ON dis.`ProvinceID` = prov.`ProvinceID`
                WHERE
                    a.`CollectpointID` = ?
                LIMIT 1";
        $query = $this->db->query($sql,array((int) $CollectpointID));
        $data = $query->row_array();

        $return['success'] = true;
        $return['data'] = $data;
        return $return;
    }

    public function GetTphBasicFormNew($CollectpointID){
        $sql = "SELECT
                    a.`CollectpointID` AS \"Koltiva.view.SME.WinFormCollectingPoint-Form-CollectpointID\",
                    a.`CollectpointDisplayID` AS \"Koltiva.view.SME.WinFormCollectingPoint-Form-CollectpointDisplayID\",
                    a.`OrgType` AS \"Koltiva.view.SME.WinFormCollectingPoint-Form-OrgType\",
                    a.`OrgID` AS \"Koltiva.view.SME.WinFormCollectingPoint-Form-OrgID\",
                    a.`CollectpointName` AS \"Koltiva.view.SME.WinFormCollectingPoint-Form-CollectpointName\",
                    a.`CollectpointDisplayID`,
                    a.`CollectpointName`,
                    prov.`ProvinceID`,
                    dis.`DistrictID`,
                    subd.`SubDistrictID`,
                    vil.`VillageID`,
                    a.`CollectpointAddress` AS \"Koltiva.view.SME.WinFormCollectingPoint-Form-CollectpointAddress\",
                    IFNULL(a.`Longitude`, ST_Y(a.`LatLong`)) AS \"Koltiva.view.SME.WinFormCollectingPoint-Form-Longitude\",
                    IFNULL(a.`Latitude`, ST_X(a.`LatLong`)) AS \"Koltiva.view.SME.WinFormCollectingPoint-Form-Latitude\"
                FROM
                    `ktv_collecting_point` a
                    LEFT JOIN ktv_village vil ON vil.`VillageID` = a.`VillageID`
                    LEFT JOIN ktv_subdistrict subd ON vil.`SubDistrictID` = subd.`SubDistrictID`
                    LEFT JOIN ktv_district dis ON subd.`DistrictID` = dis.`DistrictID`
                    LEFT JOIN ktv_province prov ON dis.`ProvinceID` = prov.`ProvinceID`
                WHERE
                    a.`CollectpointID` = ?
                LIMIT 1";
        $query = $this->db->query($sql,array((int) $CollectpointID));
        $data = $query->row_array();

        $return['success'] = true;
        $return['data'] = $data;
        return $return;
    }

    private function GenTphID($OrgType){
        switch($OrgType){
            case 'agent':
                $Prefix = 'TPHAG-';
            break;
            case 'farmer':
                $Prefix = 'TPHFR-';
            break;
            case 'collective':
                $Prefix = 'TPHCL-';
            break;
        }

        $sql = "SELECT
                    a.`CollectpointID`
                FROM
                    ktv_collecting_point a
                ORDER BY a.`CollectpointID` DESC
                LIMIT 1";
        $data = $this->db->query($sql)->row_array();
        if(isset($data['CollectpointID'])){
            $inc = (int) $data['CollectpointID'];
            $inc++;
            return $Prefix.$inc;
        }else{
            return $Prefix.'1';
        }
    }

    public function InsertTph($VarPost){
        //echo '<pre>'; print_r($VarPost); exit;
        //generate MemberID dan MemberDisplayID
        $CollectpointDisplayID = $this->GenTphID($VarPost['Koltiva_view_Tph_FormMain-OrgType']);

        $this->db->trans_begin();

        $p = array(
            $CollectpointDisplayID,
            $VarPost['Koltiva_view_Tph_FormMain-OrgType'],
            $VarPost['Koltiva_view_Tph_FormMain-OrgID'],
            $VarPost['Koltiva_view_Tph_FormMain-CollectpointName'],
            $VarPost['Koltiva_view_Tph_FormMain-VillageID'],
            $VarPost['Koltiva_view_Tph_FormMain-CollectpointAddress'],
            $VarPost['Koltiva_view_Tph_FormMain-Longitude'],
            $VarPost['Koltiva_view_Tph_FormMain-Latitude'],
            $_SESSION['userid']
        );
        $sql = "INSERT INTO `ktv_collecting_point` SET
                    `CollectpointDisplayID` = ?,
                    `OrgType` = ?,
                    `OrgID` = ?,
                    `CollectpointName` = ?,
                    `VillageID` = ?,
                    `CollectpointAddress` = ?,
                    `Longitude` = ?,
                    `Latitude` = ?,
                    `DateCreated` = NOW(),
                    `CreatedBy` = ?";
        $query = $this->db->query($sql,$p);
        $CollectpointID = $this->db->insert_id();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Data saved";
            $results['CollectpointID'] = $CollectpointID;

        }

        return $results;
    }

    public function InsertTphNew($VarPost){
        // generate MemberID dan MemberDisplayID
        $CollectpointDisplayID = $this->GenTphID($VarPost['Koltiva_view_SME_WinFormCollectingPoint-Form-OrgType']);

        $this->db->trans_begin();

        $p = array(
            $CollectpointDisplayID,
            $VarPost['Koltiva_view_SME_WinFormCollectingPoint-Form-OrgType'],
            $VarPost['Koltiva_view_SME_WinFormCollectingPoint-Form-OrgID'],
            $VarPost['Koltiva_view_SME_WinFormCollectingPoint-Form-CollectpointName'],
            $VarPost['Koltiva_view_SME_WinFormCollectingPoint-Form-VillageID'],
            $VarPost['Koltiva_view_SME_WinFormCollectingPoint-Form-CollectpointAddress'],
            $VarPost['Koltiva_view_SME_WinFormCollectingPoint-Form-Longitude'],
            $VarPost['Koltiva_view_SME_WinFormCollectingPoint-Form-Latitude'],
            $_SESSION['userid']
        );
        
        $sql = "INSERT INTO `ktv_collecting_point` SET
                    `CollectpointDisplayID` = ?,
                    `OrgType` = ?,
                    `OrgID` = ?,
                    `CollectpointName` = ?,
                    `VillageID` = ?,
                    `CollectpointAddress` = ?,
                    `Longitude` = ?,
                    `Latitude` = ?,
                    `DateCreated` = NOW(),
                    `CreatedBy` = ?";
        $query = $this->db->query($sql,$p);
        $CollectpointID = $this->db->insert_id();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Data saved";
            $results['CollectpointID'] = $CollectpointID;

        }

        return $results;
    }

    public function UpdateTph($VarPost){
        $this->db->trans_begin();

        $sql = "UPDATE ktv_collecting_point a SET
                    a.OrgType = ?,
                    a.`OrgID` = ?,
                    a.CollectpointName = ?,
                    a.VillageID = ?,
                    a.CollectpointAddress = ?,
                    a.Longitude = ?,
                    a.`Latitude` = ?,
                    a.`DateUpdated` = NOW(),
                    a.`LastModifiedBy` = ?
                WHERE
                    a.`CollectpointID` = ?
                LIMIT 1";
        $p = array(
            $VarPost['Koltiva_view_Tph_FormMain-OrgType'],
            $VarPost['Koltiva_view_Tph_FormMain-OrgID'],
            $VarPost['Koltiva_view_Tph_FormMain-CollectpointName'],
            $VarPost['Koltiva_view_Tph_FormMain-VillageID'],
            $VarPost['Koltiva_view_Tph_FormMain-CollectpointAddress'],
            $VarPost['Koltiva_view_Tph_FormMain-Longitude'],
            $VarPost['Koltiva_view_Tph_FormMain-Latitude'],
            $_SESSION['userid'],
            $VarPost['Koltiva_view_Tph_FormMain-CollectpointID']
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

    public function UpdateTphNew($VarPost){
        $this->db->trans_begin();

        $sql = "UPDATE ktv_collecting_point a SET
                    a.OrgType = ?,
                    a.`OrgID` = ?,
                    a.CollectpointName = ?,
                    a.VillageID = ?,
                    a.CollectpointAddress = ?,
                    a.Longitude = ?,
                    a.`Latitude` = ?,
                    a.`DateUpdated` = NOW(),
                    a.`LastModifiedBy` = ?
                WHERE
                    a.`CollectpointID` = ?
                LIMIT 1";
        $p = array(
            $VarPost['Koltiva_view_SME_WinFormCollectingPoint-Form-OrgType'],
            $VarPost['Koltiva_view_SME_WinFormCollectingPoint-Form-OrgID'],
            $VarPost['Koltiva_view_SME_WinFormCollectingPoint-Form-CollectpointName'],
            $VarPost['Koltiva_view_SME_WinFormCollectingPoint-Form-VillageID'],
            $VarPost['Koltiva_view_SME_WinFormCollectingPoint-Form-CollectpointAddress'],
            $VarPost['Koltiva_view_SME_WinFormCollectingPoint-Form-Longitude'],
            $VarPost['Koltiva_view_SME_WinFormCollectingPoint-Form-Latitude'],
            $_SESSION['userid'],
            $VarPost['Koltiva_view_SME_WinFormCollectingPoint-Form-CollectpointID']
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

    public function DeleteTph($CollectpointID){
        $this->db->trans_begin();

        $sql = "UPDATE ktv_collecting_point SET 
            StatusCode = 'nullified',
            DateUpdated = NOW(),
            LastModifiedBy = ?
        WHERE
            CollectpointID = ?
        LIMIT 1
        ";
        $p = array(
            $_SESSION['userid'],
            $CollectpointID
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

    public function GetCollectiveMemberMainGrid($CollectpointID){
        $return = array();

        $sql = "SELECT
                    a.`CollectpointID`
                    , a.`MemberID`
                    , b.`MemberDisplayID`
                    , b.`MemberName`
                    , FLOOR(DATEDIFF(CURDATE(), b.DateOfBirth) / 365.25) AS Age
                    , c.`Village`
                FROM
                    ktv_collecting_point_member a
                    INNER JOIN ktv_members b ON a.`MemberID` = b.`MemberID`
                    LEFT JOIN ktv_village c ON b.`VillageID` = c.`VillageID`
                WHERE
                    a.`CollectpointID` = ?
                ORDER BY b.`MemberName` ASC";
        $p = array(
            $CollectpointID
        );
        $return['data'] = $this->db->query($sql,$p)->result_array();

        $return['success'] = true;
        return $return;
    }

    public function CollectiveAddMember($CollectpointID,$MemberIDSel){
        $this->db->trans_begin();

        $ArrMemberID = explode(',',$MemberIDSel);
        if(count($ArrMemberID) > 0){
            for ($i=0; $i < count($ArrMemberID); $i++) { 
                $sql = "INSERT INTO `ktv_collecting_point_member` SET
                        `CollectpointID` = ?,
                        `MemberID` = ?,
                        `DateGenerated` = NOW(),
                        `CreatedBy` = ?";
                $p = array(
                    $CollectpointID,
                    $ArrMemberID[$i],
                    $_SESSION['userid']
                );
                $query = $this->db->query($sql,$p);
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
        }
        return $results;
    }

    public function CollectiveDeleteMember($CollectpointID,$MemberID){
        $sql = "DELETE FROM ktv_collecting_point_member WHERE CollectpointID = ? AND MemberID = ? LIMIT 1";
        $p = array(
            $CollectpointID,$MemberID
        );
        $query = $this->db->query($sql,$p);

        if($query == true){
            $results['success'] = true;
            $results['message'] = "Data deleted";
        }else{
            $results['success'] = false;
            $results['message'] = "Failed to delete data";
        }
        return $results;
    }
}