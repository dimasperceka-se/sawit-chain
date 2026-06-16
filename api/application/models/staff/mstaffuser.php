<?php

/******************************************
 *  Author : n1colius.lau@gmail.com
 *  Created On : Mon Dec 02 2019
 *  File : mstaffuser.php
 *******************************************/
class Mstaffuser extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function GetComboAccessStaff()
    {
        $SqlFilter = "";
        if ($_SESSION['is_admin'] != "1") {
            $SqlFilter = " AND dis.DistrictID IN ({$_SESSION['daerah_access']}) ";
        }

        $sql = "SELECT
                    dis.`DistrictID` AS id
                    , CONCAT(prov.`Province`,' - ',dis.`District`) AS `name`
                FROM
                    ktv_province prov
                    LEFT JOIN ktv_district dis ON prov.`ProvinceID` = dis.`ProvinceID`
                WHERE 1=1
                    AND prov.`active` = 1
                    AND dis.`active` = 1
                    $SqlFilter
                ORDER BY `name` ASC";
        $data = $this->db->query($sql)->result_array();

        $return['success'] = true;
        $return['data'] = $data;
        return $return;
    }

    public function GetComboDhisRole() {
        $arrReturn = array();

        $url = $this->config->item('dhis_url').'api/userRoles';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Basic YWRtaW46S29sdGl2YTIwMTMh'
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        $curlresult = json_decode($result,true);
        //echo '<pre>'; print_r($curlresult); exit;

        $DataCurl = $curlresult['userRoles'];
        foreach ($DataCurl as $key => $value) {
            $arrReturn[$key]['id'] = $value['id'];
            $arrReturn[$key]['label'] = $value['displayName'];
        }
        return $arrReturn;
    }

    public function GetComboDhisGroup() {
        $arrReturn = array();

        $url = $this->config->item('dhis_url').'api/userGroups';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Basic YWRtaW46S29sdGl2YTIwMTMh'
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        $curlresult = json_decode($result,true);
        //echo '<pre>'; print_r($curlresult); exit;

        $DataCurl = $curlresult['userGroups'];
        foreach ($DataCurl as $key => $value) {
            $arrReturn[$key]['id'] = $value['id'];
            $arrReturn[$key]['label'] = $value['displayName'];
        }
        return $arrReturn;

    }

    public function GetGridMain($pSearch, $start, $limit, $sortingField, $sortingDir)
    {
        if ($sortingField == "") $sortingField = 'PersonNm';
        if ($sortingDir == "") $sortingDir = 'ASC';

        $SqlAdmin = " AND role.RoleId != 5 ";
        if ($_SESSION['is_admin'] == "1") $SqlAdmin = "";

        $sql = "SELECT SQL_CALC_FOUND_ROWS
                    b.`PersonNm`
                    , b.PersonID
                    , a.StaffID
                    , a.ObjType
                    , b.`Gender`
                    , role.`RoleName` AS Role
                    , b.`StatusCd` AS `Status`
                    , IFNULL(ct.`CountryName`,'-') AS `Country`
                    , b.`Email`
                    , IFNULL(su.UserName, '-') AS UserName
                    , IFNULL(su.`UserActive`,'-') AS AccountActive
                    , IFNULL(sy_g.GroupName,'-') AS AccountGroup
                    , IFNULL(su.UserInCognito,'-') AS AccountCognito
                FROM
                    ktv_staffs a
                    INNER JOIN ktv_persons b ON a.`PersonID` = b.`PersonID`
                    LEFT JOIN sys_role role ON role.`RoleCode` = a.`ObjType`
                    LEFT JOIN ktv_country ct ON b.`CountryID` = ct.`CountryID`
                    LEFT JOIN sys_user su ON b.`UserID` = su.`UserId`
                    LEFT JOIN sys_user_group su_g ON su.UserId = su_g.UserGroupUserId AND su_g.UserGroupIsDefault = '1'
                    LEFT JOIN sys_group sy_g ON su_g.UserGroupGroupId = sy_g.GroupId
                WHERE 1=1
                    AND a.`StatusCode` != 'nullified'
                    AND b.PersonNm != ''
                    AND b.PersonNm IS NOT NULL
                    AND b.`PersonNm` LIKE ?
                    AND ( (role.RoleCode = ?) OR (? = '') )
                ORDER BY `$sortingField` $sortingDir
                LIMIT ?,?
                ";
        $p = array(
            '%' . $pSearch['KeySearch'] . '%', $pSearch['CmbSearchRole'], $pSearch['CmbSearchRole'], $start, $limit
        );
        $query = $this->db->query($sql, $p);
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        if ($sortingDir == 'ASC') {
            $sortingInfo = 'ascending';
        }
        if ($sortingDir == 'DESC') {
            $sortingInfo = 'descending';
        }

        $_SESSION['informationGrid'] = '
            <div class="Sfr_BoxInfoDataGrid_Title"><strong>' . number_format($query->row()->total, 0, ".", ",") . '</strong> ' . lang('Data') . '</div>
            <ul class="Sft_UlListInfoDataGrid">
                <li class="Sft_ListInfoDataGrid">
                    <img class="Sft_ListIconInfoDataGrid" src="' . base_url() . '/assets/images/icons/sort.png" width="20" />&nbsp;&nbsp;Sorted by ' . lang($sortingField) . ' ' . $sortingInfo . '
                </li>
            </ul>';

        return $result;
    }

    public function CekDuplikatNoHp($Nohp, $OpsiDisplay, $PersonID)
    {
        $SqlFilter = "";
        $PersonID = (int) $PersonID;
        if ($OpsiDisplay == 'update') $SqlFilter = " AND a.PersonID != $PersonID ";

        $sql = "SELECT
                    a.`StaffID`
                FROM
                    ktv_staffs a
                WHERE 1=1
                    AND CONCAT(IFNULL(a.`OfficialPhoneCode`,''),IFNULL(a.`OfficialPhone`,'')) = ?
                    $SqlFilter
                LIMIT 1";
        $Data = $this->db->query($sql, array($Nohp))->row_array();
        if (isset($Data['StaffID'])) {
            return true;
        } else {
            return false;
        }
    }

    public function CekDuplikatEmail($Email, $OpsiDisplay, $PersonID)
    {
        $SqlFilter = "";
        $PersonID = (int) $PersonID;
        if ($OpsiDisplay == 'update') $SqlFilter = " AND a.PersonID != $PersonID ";

        $sql = "SELECT
                    a.`StaffID`
                FROM
                    ktv_staffs a
                WHERE 1=1
                    AND a.`OfficialEmail` = ?
                    #AND a.StatusCode != 'nullified'
                    $SqlFilter
                LIMIT 1";
        $Data = $this->db->query($sql, array($Email))->row_array();
        if (isset($Data['StaffID'])) {
            return true;
        } else {
            return false;
        }
    }

    public function CekDuplikatUsername($Username, $OpsiDisplay, $UserId)
    {
        $SqlFilter = "";
        $UserId = (int) $UserId;
        if ($OpsiDisplay == 'update') $SqlFilter = " AND a.UserId != $UserId ";

        $sql = "SELECT
                    a.`UserId`
                FROM
                    sys_user a
                WHERE 1=1
                    AND a.`UserName` = ?
                    $SqlFilter
                LIMIT 1";
        $Data = $this->db->query($sql, array($Username))->row_array();
        if (isset($Data['UserId'])) {
            return true;
        } else {
            return false;
        }
    }

    public function insertStaff($post)
    {
        //echo '<pre>'; print_r($post); exit;
        $results = array();
        $this->db->trans_begin();

        //ktv_persons
        $sql = "INSERT INTO `ktv_persons` SET
                `PersonID` = NULL,
                Ssn = ?,
                `PersonNm` = ?,
                `BirthDate` = ?,
                `Gender` = ?,
                `Address` = ?,
                `Email` = ?,
                `StatusCd` = ?,
                OfficialCellPhoneCode = ?,
                `OfficialCellPhone` = ?,
                `OfficialEmail` = ?,
                `WorkAreaID` = ?,
                `DateCreated` = NOW(),
                `CreatedBy` = ?";
        $p = array(
            (isset($post['Ssn']) ? $post['Ssn'] : '-'),
            $post['PersonNm'],
            $post['Birthdate'],
            $post['Gender'],
            $post['Address'],
            $post['OfficialEmail'],
            $post['StatusCode'],
            $post['OfficialCellPhoneCode'],
            $post['OfficialCellPhone'],
            $post['OfficialEmail'],
            $post['WorkAreaID'],
            $_SESSION['userid']
        );
        $query = $this->db->query($sql, $p);
        $PersonID = $this->db->insert_id();

        //ktv_staffs
        $sql = "INSERT INTO `ktv_staffs` SET
                `StaffID` = NULL,
                `PersonID` = ?,
                `ObjType` = ?,
                `ObjID` = IF(?='',NULL,?),
                `StaffRegisteredNumber` = NULL,
                `OfficialPhoneCode` = ?,
                `OfficialPhone` = ?,
                `WorkPhone` = ?,
                `OfficialEmail` = ?,
                `WorkAreaID` = ?,
                `CCEmail` = ?,
                `StatusCode` = ?,
                `DateCreated` = NOW(),
                `CreatedBy` = ?";
        $p = array(
            $PersonID,
            $post['StaffRole'],
            $post['ObjID'], $post['ObjID'],
            $post['OfficialCellPhoneCode'],
            $post['OfficialCellPhone'],
            $post['OfficialCellPhoneCode'].$post['OfficialCellPhone'],
            $post['OfficialEmail'],
            $post['WorkAreaID'],
            $post['CCEmail'],
            $post['StatusCode'],
            $_SESSION['userid']
        );
        $query = $this->db->query($sql, $p);
        $StaffID = $this->db->insert_id();

        //Staff Position ---- (Begin)
        $sql = "INSERT INTO `ktv_staff_positions` SET
            `StaffPosStaffID` = ?,
            `StaffPosPositionID` = ?,
            `StaffPostStart` = CURDATE(),
            `StaffPostEnd` = '9999-12-31',
            `StatusCode` = 'active',
            `DateCreated` = NOW(),
            `CreatedBy` = ?";
        $p = array(
            $StaffID,
            $post['PositionID'],
            $_SESSION['userid']
        );
        $query = $this->db->query($sql, $p);
        //Staff Position ---- (End)

        

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = lang("Failed to save data");
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['PersonID'] = $PersonID;
            $results['StaffID'] = $StaffID;
            $results['message'] = lang("Data saved");

            //Proses foto
            if ($post['PhotoInputData'] != "" && file_exists($post['PhotoInputData'])) {

                $Photo = "";
                $file = explode("files/tmp/",$post['PhotoInputData']);
                //Insert ada photonya pakai aws
                if(file_exists('files/tmp/'.$file[1])) {
                    $this->load->library('awsfileupload');
                    $upload = $this->awsfileupload->upload('files/tmp/'.$file[1],$file[1],AWSS3_STAFF_PHOTO, 'images');
                    if ($upload['success'] == true) {
                        delete_file($post['PhotoInputData']);
                        $Photo = $upload['filenamepath'];

                        $sql = "UPDATE `ktv_persons` SET  `Photo` =  ? WHERE  PersonID = ? LIMIT 1";
                        $query = $this->db->query($sql, array($Photo, $PersonID));
                    }
                }

                /*$path_parts = pathinfo($post['PhotoInputData']);
                $namafilenya = $path_parts['basename'];

                $this->load->library('awsfileupload');
                $upload = $this->awsfileupload->upload($post['PhotoInputData'], $namafilenya, AWSS3_STAFF_PHOTO_PATH, 'images');
                if ($upload['success'] == true) {
                    $sql = "UPDATE `ktv_persons` SET  `Photo` =  ? WHERE  PersonID = ? LIMIT 1";
                    $query = $this->db->query($sql, array($upload['filenamepath'], $PersonID));
                }

                //delete file temp
                delete_file($post['PhotoInputData']);*/
            }
        }
        return $results;
    }

    public function GetuserDhisFormOpen($PersonID) {
        $sql = "SELECT
                    a.`UserId`
                    , a.`UserName`
                    , a.`UserRealName` AS `Name`
                    , a.UserExtId
                    #, a.UserExtGroupId AS CmbDhisGroup
                    , a.UserExtGroupId
                    #, a.UserExtRoleId AS CmbDhisRole
                    , a.UserExtRoleId
                FROM
                    sys_user a
                    INNER JOIN ktv_persons b ON a.`UserId` = b.`UserID`
                WHERE 1=1
                    AND b.`PersonID` = ?
                LIMIT 1";
        $data = $this->db->query($sql,array($PersonID))->row_array();

        $data['CmbDhisGroup'] = explode(',',$data['UserExtGroupId']);
        $data['CmbDhisRole'] = explode(',',$data['UserExtRoleId']);

        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.Staffuser.PanelUserDhis-Form-" . $key;
            $dataRow[$keyNew] = $value;
        }

        $return['success'] = true;
        $return['data'] = $dataRow;
        return $return;
    }

    public function GetStaffDataForm($PersonID)
    {
        $this->load->library('awsfileupload');
        $sql = "SELECT
                    a.`PersonID`
                    , b.StaffID
                    , a.UserID
                    , su.UserInCognito
                    , a.`PersonNm`
                    , a.`BirthDate` AS Birthdate
                    , a.`Gender`
                    , a.`Address`
                    , a.`CountryID`
                    , b.`ObjType` AS StaffRole
                    , sr.RoleId
                    , b.`ObjID`
                    , a.`Ssn`
                    , b.`StaffRegisteredNumber`
                    , a.`OfficialCellPhone`
                    , ct.`PhoneCode` AS CountryPhoneCode
                    , IFNULL(a.`OfficialCellPhoneCode`, ct.PhoneCode) OfficialCellPhoneCode
                    , a.OfficialEmail
                    , b.`StatusCode`
                    , b.CCEmail
                    , a.Photo
                    , f.StaffPosPositionID AS PositionID
                    , a.`WorkAreaID`
                    , dis.`ProvinceID` AS WorkAreaProvinceID
                    , b.`MillID`
                    , b.`SmeID`
                    , b.`RefineryID`
                    , CASE b.`ObjType`
                        WHEN 'cooperative'
                            THEN
                                (SELECT
                                    d.ProvinceID
                                FROM
                                    ktv_cooperatives role_coop
                                    LEFT JOIN ktv_village v ON v.`VillageID` = role_coop.`VillageID`
                                    LEFT JOIN ktv_subdistrict sd ON sd.`SubDistrictID` = v.`SubDistrictID`
                                    LEFT JOIN ktv_district d ON d.`DistrictID` = sd.`DistrictID`
                                WHERE
                                    role_coop.CoopID = b.`ObjID`
                                LIMIT 1)
                        WHEN 'farmergroup'
                            THEN
                                (SELECT
                                    d.ProvinceID
                                FROM
                                    ktv_cpg role_cpg
                                    LEFT JOIN ktv_village v ON v.`VillageID` = role_cpg.`VillageID`
                                    LEFT JOIN ktv_subdistrict sd ON sd.`SubDistrictID` = v.`SubDistrictID`
                                    LEFT JOIN ktv_district d ON d.`DistrictID` = sd.`DistrictID`
                                WHERE
                                    role_cpg.CPGid = b.`ObjID`
                                LIMIT 1)
                        WHEN 'sce'
                            THEN
                                (SELECT
                                    SUBSTR(role_sce.FarmerID,1,4)
                                FROM
                                    sce_farmer role_sce
                                WHERE
                                    role_sce.SceID = b.`ObjID`
                                LIMIT 1)
                        WHEN 'trader'
                            THEN
                                (SELECT
                                    d.ProvinceID
                                FROM
                                    ktv_traders role_trader
                                    LEFT JOIN ktv_village v ON v.`VillageID` = role_trader.`VillageID`
                                    LEFT JOIN ktv_subdistrict sd ON sd.`SubDistrictID` = v.`SubDistrictID`
                                    LEFT JOIN ktv_district d ON d.`DistrictID` = sd.`DistrictID`
                                WHERE
                                    role_trader.TraderID = b.`ObjID`
                                LIMIT 1)
                        WHEN 'warehouse'
                            THEN
                                (SELECT
                                    d.ProvinceID
                                FROM
                                    ktv_warehouse role_ware
                                    LEFT JOIN ktv_village v ON v.`VillageID` = role_ware.`VillageID`
                                    LEFT JOIN ktv_subdistrict sd ON sd.`SubDistrictID` = v.`SubDistrictID`
                                    LEFT JOIN ktv_district d ON d.`DistrictID` = sd.`DistrictID`
                                WHERE
                                    role_ware.WarehouseID = b.`ObjID`
                                LIMIT 1)
                        WHEN 'agent'
                            THEN
                                (SELECT
                                    d.ProvinceID
                                FROM
                                    ktv_members role_members
                                    LEFT JOIN ktv_village v ON v.`VillageID` = role_members.`VillageID`
                                    LEFT JOIN ktv_subdistrict sd ON sd.`SubDistrictID` = v.`SubDistrictID`
                                    LEFT JOIN ktv_district d ON d.`DistrictID` = sd.`DistrictID`
                                WHERE
                                    role_members.MemberID = b.`ObjID`
                                LIMIT 1)
                    END AS RoleProvinceID
                    , CASE b.`ObjType`
                        WHEN 'cooperative'
                            THEN
                                (SELECT
                                    sd.DistrictID
                                FROM
                                    ktv_cooperatives role_coop
                                    LEFT JOIN ktv_village v ON v.`VillageID` = role_coop.`VillageID`
                                    LEFT JOIN ktv_subdistrict sd ON sd.`SubDistrictID` = v.`SubDistrictID`
                                WHERE
                                    role_coop.CoopID = b.`ObjID`
                                LIMIT 1)
                        WHEN 'farmergroup'
                            THEN
                                (SELECT
                                    sd.DistrictID
                                FROM
                                    ktv_cpg role_cpg
                                    LEFT JOIN ktv_village v ON v.`VillageID` = role_cpg.`VillageID`
                                    LEFT JOIN ktv_subdistrict sd ON sd.`SubDistrictID` = v.`SubDistrictID`
                                WHERE
                                    role_cpg.CPGid = b.`ObjID`
                                LIMIT 1)
                        WHEN 'sce'
                            THEN
                                (SELECT
                                    SUBSTR(role_sce.FarmerID,1,4)
                                FROM
                                    sce_farmer role_sce
                                WHERE
                                    role_sce.SceID = b.`ObjID`
                                LIMIT 1)
                        WHEN 'trader'
                            THEN
                                (SELECT
                                    sd.DistrictID
                                FROM
                                    ktv_traders role_trader
                                    LEFT JOIN ktv_village v ON v.`VillageID` = role_trader.`VillageID`
                                    LEFT JOIN ktv_subdistrict sd ON sd.`SubDistrictID` = v.`SubDistrictID`
                                WHERE
                                    role_trader.TraderID = b.`ObjID`
                                LIMIT 1)
                        WHEN 'warehouse'
                            THEN
                                (SELECT
                                    sd.DistrictID
                                FROM
                                    ktv_warehouse role_ware
                                    LEFT JOIN ktv_village v ON v.`VillageID` = role_ware.`VillageID`
                                    LEFT JOIN ktv_subdistrict sd ON sd.`SubDistrictID` = v.`SubDistrictID`
                                WHERE
                                    role_ware.WarehouseID = b.`ObjID`
                                LIMIT 1)
                        WHEN 'agent'
                            THEN
                                (SELECT
                                    sd.DistrictID
                                FROM
                                    ktv_members role_members
                                    LEFT JOIN ktv_village v ON v.`VillageID` = role_members.`VillageID`
                                    LEFT JOIN ktv_subdistrict sd ON sd.`SubDistrictID` = v.`SubDistrictID`
                                WHERE
                                    role_members.MemberID = b.`ObjID`
                                LIMIT 1)
                    END AS RoleDistrictID
                FROM
                    ktv_persons a
                    INNER JOIN ktv_staffs b ON a.`PersonID` = b.`PersonID`
                    LEFT JOIN ktv_staff_positions f ON 1=1
                        AND b.StaffID = f.StaffPosStaffID
                        AND (CURDATE() BETWEEN f.StaffPostStart AND f.StaffPostEnd)
                    LEFT JOIN ktv_ref_work_area wa ON a.`WorkAreaID` = wa.`WorkAreaID`
                    LEFT JOIN ktv_district dis ON wa.`DistrictID` = dis.`DistrictID`
                    LEFT JOIN ktv_province pv ON dis.ProvinceID = pv.ProvinceID
                    LEFT JOIN ktv_country ct ON pv.`CountryCode` = ct.`ISO2`
                    LEFT JOIN sys_role sr ON b.ObjType = sr.RoleCode
                    LEFT JOIN sys_user su ON a.UserID = su.UserId
                WHERE 1=1
                    AND a.`PersonID` = ?
                LIMIT 1";
        $data = $this->db->query($sql, array($PersonID))->row_array();
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;

        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.Staffuser.MainForm-StaffForm-" . $key;
            $dataRow[$keyNew] = $value;
        }

        $dataRow['StatusCode'] = $data['StatusCode'];
        $dataRow['UserID'] = $data['UserID'];
        $dataRow['Photo'] = $data['Photo'];
        $dataRow['Email'] = $data['OfficialEmail'];
        $dataRow['Gender'] = $data['Gender'];
        $dataRow['Name'] = $data['PersonNm'];
        $dataRow['CountryPhoneCode'] = $data['CountryPhoneCode'];
        $dataRow['Phonenumber'] = $data['OfficialCellPhone'];
        $dataRow['PhonenumberWithCode'] = $data['OfficialCellPhoneCode'].$data['OfficialCellPhone'];
        $dataRow['PersonID'] = $data['PersonID'];
        $dataRow['WorkAreaID'] = $data['WorkAreaID'];
        $dataRow['WorkAreaProvinceID'] = $data['WorkAreaProvinceID'];
        $dataRow['PositionID'] = $data['PositionID'];
        $dataRow['ObjID'] = $data['ObjID'];
        $dataRow['MillID'] = $data['MillID'];
        $dataRow['SmeID'] = $data['SmeID'];
        $dataRow['RefineryID'] = $data['RefineryID'];
        $dataRow['RoleProvinceID'] = $data['RoleProvinceID'];
        $dataRow['RoleDistrictID'] = $data['RoleDistrictID'];
        $dataRow['StaffRole'] = $data['StaffRole'];
        $dataRow['RoleId'] = $data['RoleId'];
        $dataRow['StaffID'] = $data['StaffID'];
        $dataRow['UserInCognito'] = $data['UserInCognito'];

        //Unset beberapa field yg akan diset manual
        unset($dataRow['Koltiva.view.Staffuser.MainForm-StaffForm-WorkAreaID']);
        unset($dataRow['Koltiva.view.Staffuser.MainForm-StaffForm-WorkAreaProvinceID']);
        unset($dataRow['Koltiva.view.Staffuser.MainForm-StaffForm-PositionID']);
        unset($dataRow['Koltiva.view.Staffuser.MainForm-StaffForm-ObjID']);
        unset($dataRow['Koltiva.view.Staffuser.MainForm-StaffForm-RoleProvinceID']);
        unset($dataRow['Koltiva.view.Staffuser.MainForm-StaffForm-RoleDistrictID']);
        unset($dataRow['Koltiva.view.Staffuser.MainForm-StaffForm-StaffRole']);

        //Photo
        if ($dataRow['Photo'] != "") {
            if($this->awsfileupload->doesObjectExist($dataRow['Photo']) == true) {
                $dataRow['Koltiva.view.Staffuser.MainForm-StaffForm-Photo'] = $data['Photo'];
                $dataRow['Koltiva.view.Staffuser.MainForm-StaffForm-PhotoInputData'] = $data['Photo'];
                $dataRow['Photo'] = $this->config->item('CTCDN')."/".$data['Photo'];
            }else{
                if(file_exists('images/staff/'.$dataRow['Photo'])) {
                    $dataRow['Koltiva.view.Staffuser.MainForm-StaffForm-Photo']          = 'images/staff/'.$dataRow['Photo'];
                    $dataRow['Koltiva.view.Staffuser.MainForm-StaffForm-PhotoInputData'] = 'images/staff/'.$dataRow['Photo'];
                    $dataRow['Photo'] = base_url().'images/staff/'.$dataRow['Photo'];
                }else{
                    $dataRow['Photo'] = null;
                }
            }

            //Cek ada tidak filenya di AWS
            /*$this->load->library('awsfileupload');
            if ($this->awsfileupload->doesObjectExist($dataRow['Photo']) == false) {
                $dataRow['Photo'] = null;
            } else {
                $dataRow['Photo'] = $data['Photo'];
            }*/
        } else {
            $dataRow['Photo'] = null;
        }

        $return['success'] = true;
        $return['data'] = $dataRow;
        return $return;
    }

    public function UpdateStaffPhoto($gambar, $PersonID)
    {
        $sql = "UPDATE ktv_persons a SET
                    a.`Photo` = ?
                WHERE
                    a.`PersonID` = ?
                LIMIT 1";
        $p = array(
            $gambar, $PersonID
        );
        $query = $this->db->query($sql, $p);
    }

    public function deleteStaff($PersonID)
    {
        $results = array();
        $this->db->trans_begin();

        //$sql = "UPDATE ktv_persons a SET a.`StatusCd` = 'nullified' WHERE a.`PersonID` = ? LIMIT 1";
        $sql = "DELETE FROM ktv_persons WHERE PersonID = ? LIMIT 1";
        $query = $this->db->query($sql, array($PersonID));

        //$sql = "UPDATE ktv_staffs a SET a.`StatusCode` = 'nullified' WHERE a.`PersonID` = ? LIMIT 1";
        $sql = "DELETE FROM ktv_staffs WHERE PersonID = ? LIMIT 1";
        $query = $this->db->query($sql, array($PersonID));

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = lang("Failed to delete staff");
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = lang("Staff deleted");
        }

        return $results;
    }

    public function GetUserAccountDataForm($PersonID)
    {
        $sql = "SELECT
                    b.`UserId`
                    , b.`UserName` AS Username
                    , CASE
                        WHEN b.UserLanguage = 'English' THEN '1'
                        WHEN b.UserLanguage = 'Indonesia' THEN '2'
                        WHEN b.UserLanguage = 'Malaysia' THEN '3'
                    END AS UserLanguage
                    , b.`UserIsAdmin`
                    , b.`UserActive`
                    , b.UserInCognito
                    , b.CognitoUserSub
                    , b.CognitoUserStatus
                FROM
                    ktv_persons a
                    INNER JOIN sys_user b ON a.`UserID` = b.`UserId`
                WHERE
                    a.`PersonID` = ?
                LIMIT 1";
        $data = $this->db->query($sql, array($PersonID))->row_array();
        //echo '<pre>'; print_r($data); exit;

        //group
        $groups = array();
        $default_group = 0;
        $query = $this->db->get_where('sys_user_group', array('UserGroupUserId' => $data['UserId']));
        $dataGroup = $query->result_array();
        if (!empty($dataGroup)) {
            foreach ($dataGroup as $key => $value) {
                $groups[] = $value['UserGroupGroupId'];
                if ($value['UserGroupIsDefault'] == '1') {
                    $default_group = $value['UserGroupGroupId'];
                }
            }
        }
        $data['UserGroupIsDefault'] = $default_group;
        $data['GroupIds'] = $groups;

        //access staff
        $accessStaff = array();
        $sql = "SELECT
                    a.`DistrictID`
                FROM
                    ktv_access_staff a
                WHERE
                    a.`UserId` = ?";
        $dataAccessStaff = $this->db->query($sql, array($data['UserId']))->result_array();
        if (!empty($dataAccessStaff)) {
            foreach ($dataAccessStaff as $key => $value) {
                $accessStaff[] = $value['DistrictID'];
            }
        }
        $data['AccessStaff'] = $accessStaff;

        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.Staffuser.PanelUserMgt-Form-" . $key;
            $dataRow[$keyNew] = $value;
        }

        $dataRow['UserGroupIsDefault'] = $data['UserGroupIsDefault'];
        $dataRow['UserInCognito'] = $data['UserInCognito'];
        $dataRow['CognitoUserStatus'] = $data['CognitoUserStatus'];
        $dataRow['Username'] = $data['Username'];

        //StateStaff
        if ($dataRow['CognitoUserStatus'] == "CONFIRMED") {
            $dataRow['Koltiva.view.Staffuser.PanelUserMgt-Form-StateStaff'] = "LINKEDCONFIRMED";
            $dataRow['StateStaff'] = "LINKEDCONFIRMED";
        } else {
            $dataRow['Koltiva.view.Staffuser.PanelUserMgt-Form-StateStaff'] = "LINKEDUNCONFIRMED";
            $dataRow['StateStaff'] = "LINKEDUNCONFIRMED";
        }

        //echo '<pre>'; print_r($dataRow); exit;
        return $dataRow;
    }

    public function GetDataUserAcc($UserId)
    {
        $sql = "SELECT
                `UserId`,
                `UserRealName`,
                `UserName`,
                `UserPassword`,
                `UserEmail`,
                `UserActive`,
                `UserLanguage`,
                `UserNotification`,
                `UserInCognito`,
                `CognitoUserSub`,
                `CognitoUserStatus`,
                `UserUnitId`,
                `UserIsAdmin`,
                `UserDescription`
            FROM
                sys_user a
            WHERE
                a.`UserId` = ?
            LIMIT 1";
        return $this->db->query($sql, array($UserId))->row_array();
    }

    public function GetLinkedUserForm($PersonID)
    {
        $sql = "SELECT
                    b.`UserName` AS Username
                    , a.`OfficialEmail` AS Email
                    , CONCAT(IFNULL(a.`OfficialCellPhoneCode`,''),IFNULL(a.`OfficialCellPhone`,'')) AS Phonenumber
                    , a.`PersonID`
                    , b.`UserId`
                FROM
                    ktv_persons a
                    INNER JOIN sys_user b ON a.`UserID` = b.`UserId`
                WHERE
                    a.`PersonID` = ?
                LIMIT 1";
        $Data = $this->db->query($sql, array($PersonID))->row_array();

        //prep variable
        $DataRow = array();
        foreach ($Data as $key => $value) {
            $keyNew = "Koltiva.view.Staffuser.WinFormLinkedUser-FormCreate-" . $key;
            $DataRow[$keyNew] = $value;
        }

        //$data['CmbUserGroup'] = $groups;

        $return['success'] = true;
        $return['data'] = $DataRow;
        return $return;
    }

    public function GetFarmerAssignmentGrid($StaffID, $start = null, $limit = null, $opsiLimit = 'limit', $sortingField = '', $sortingDir = ''){
        if ($sortingField == "") $sortingField = 'StatusCode';
        if ($sortingDir == "") $sortingDir = 'ASC';

        $sql = "SELECT SQL_CALC_FOUND_ROWS
                a.StaffAssignmentID
                , a.StaffAssignmentExtID
                , a.StartDate
                , a.EndDate
                , a.StatusCode
                , COUNT(b.MemberID) FarmerNr
            FROM
                ktv_staffs_assignment a
            LEFT JOIN
                ktv_staffs_assignment_member b on b.StaffAssignmentID = a.StaffAssignmentID
            WHERE
                a.StaffID = ?
            GROUP BY
                a.StaffAssignmentID";

        if ($opsiLimit == 'limit'){
            $sql = $sql . " ORDER BY `$sortingField` $sortingDir
                            LIMIT ?,?";
            $p = array($StaffID, $start, $limit);
        } elseif($opsiLimit == 'no_limit'){
            $sql = $sql;
            $p = array($StaffID);
        }

        $Data = $this->db->query($sql,$p)->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $return['total'] = $query->row()->total;

        $return['data'] = $Data;
        $return['success'] = true;
        return $return;
    }

    public function GetFarmerAssignForm($StaffAssignmentID){
        $sql = "SELECT
            a.StaffAssignmentID
            , a.StaffAssignmentExtID
            , a.StaffID
            , a.StartDate
            , a.EndDate
            , a.Description
            , a.StatusCode
        FROM
            ktv_staffs_assignment a
        WHERE
            a.StaffAssignmentID = ?";

        $query = $this->db->query($sql,array($StaffAssignmentID));
        $data  = $query->row_array();

        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.Staffuser.WinFormFarmerAssignment-Form-" . $key;
            $dataRow[$keyNew] = $value;
        }

        $dataRow['StatusCode'] = $data["StatusCode"];
        
        $result['success']  = true;
        $result['data']     = $dataRow;

        return $result;
    }

    private function GenerateFarmerAssignID(){
        return "EXT-".date('ymdHis').mt_rand(0,9);
    }

    public function InsertFarmerAssign($paramPost){
        $datapost['StaffAssignmentExtID']        = $this->GenerateFarmerAssignID();
        $datapost['StaffID']        = $paramPost['StaffID'];
        $datapost['StartDate']      = $paramPost['StartDate'];
        $datapost['EndDate']        = $paramPost['EndDate'];
        $datapost['Description']    = $paramPost['Description'];
        $datapost['StatusCode']     = $paramPost['StatusCode'];
        $datapost['DateCreated']    = date('Y-m-d H:i:s');
        $datapost['CreatedBy']      = $_SESSION['userid'];

        $query = $this->db->insert('ktv_staffs_assignment',$datapost);

        if($query){
            $return['success'] = true;
            $return['success'] = lang('Data Saved');
        }else{
            $return['success'] = false;
            $return['success'] = lang('Failed Save Data');
        }
        
        return $return;
    }

    public function UpdateFarmerAssign($paramPost){
        $datapost['StartDate']      = $paramPost['StartDate'];
        $datapost['EndDate']        = $paramPost['EndDate'];
        $datapost['Description']    = $paramPost['Description'];
        $datapost['StatusCode']     = $paramPost['StatusCode'];
        $datapost['DateUpdated']    = date('Y-m-d H:i:s');
        $datapost['LastModifiedBy'] = $_SESSION['userid'];

        $this->db->where('StaffAssignmentID',$paramPost['StaffAssignmentID']);
        $query = $this->db->update('ktv_staffs_assignment',$datapost);

        if($query){
            $return['success'] = true;
            $return['success'] = lang('Data Saved');
        }else{
            $return['success'] = false;
            $return['success'] = lang('Failed Save Data');
        }
        
        return $return;
    }

    public function GetFarmerListGrid($StaffAssignmentID, $StaffID, $start = null, $limit = null, $opsiLimit = 'limit', $sortingField = '', $sortingDir = ''){
        if ($sortingField == "") $sortingField = 'StaffAssignmentMemberID';
        if ($sortingDir == "") $sortingDir = 'DESC';

        $sql = "SELECT SQL_CALC_FOUND_ROWS
                sas.StaffAssignmentMemberID
                , sas.MemberID
                , s.MemberDisplayID
                , s.MemberName
                , CASE 
                        WHEN s.Gender = 'm' THEN 'Male'
                        WHEN s.Gender = 'f' THEN 'Female'
                        WHEN s.Gender = 'o' THEN 'Others'
                        ELSE '-'
                    END Gender
                , pv.Province Province
                , ds.District District
            FROM
                ktv_staffs_assignment_member sas
            LEFT JOIN
                ktv_staffs_assignment sa on sa.StaffAssignmentID = sas.StaffAssignmentID
            LEFT JOIN
                ktv_members s on s.MemberID = sas.MemberID
            LEFT JOIN
                ktv_village vil on vil.VillageID = s.VillageID
            LEFT JOIN
                ktv_subdistrict subd on subd.SubDistrictID = vil.SubDistrictID
            LEFT JOIN
                ktv_district ds on ds.DistrictID = subd.DistrictID
            LEFT JOIN
                ktv_province pv on ds.ProvinceID = pv.ProvinceID
            WHERE
                sas.StaffAssignmentID = ?
            AND
                sa.StaffID = ?";
            
            if ($opsiLimit == 'limit'){
                $sql = $sql . " ORDER BY `$sortingField` $sortingDir
                                LIMIT ?,?";
                $p = array($StaffAssignmentID, $StaffID, $start, $limit);
            } elseif($opsiLimit == 'no_limit'){
                $sql = $sql;
                $p = array($StaffAssignmentID, $StaffID);
            }
    
            $Data = $this->db->query($sql,$p)->result_array();
    

            $query = $this->db->query('SELECT FOUND_ROWS() AS total');
            $return['total'] = $query->row()->total;
            $return['data'] = $Data;
            $return['success'] = true;
            return $return;
    }

    public function getMemberID($MemberDisplayID){
        $sql    = "SELECT MemberID FROM ktv_members WHERE MemberDisplayID = ?";
        $query  = $this->db->query($sql, array($MemberDisplayID))->row_array();

        return $query['MemberID'];
    }
    
    public function insertMember($SupplierID, $StaffAssignmentID, $StaffID){
        $this->db->trans_begin();

        $sukses = 0;
        $exist  = 0;
        foreach ($SupplierID as $key => $value) {
            # code...
            $sql    = "SELECT
                    ksam.MemberID
                FROM
                    ktv_staffs_assignment_member ksam
                LEFT JOIN
                    ktv_staffs_assignment ksa on ksa.StaffAssignmentID = ksam.StaffAssignmentID
                WHERE
                    ksa.StatusCode = 'active' AND ksam.MemberID = ?";
            $query  = $this->db->query($sql, array($value));
            if($query->num_rows()>0){
                $exist++;
            }else{
                $this->db->insert('ktv_staffs_assignment_member', array(
                    'MemberID' =>  $value,
                    'StaffAssignmentID' => $StaffAssignmentID,
                    'CreatedBy' => $_SESSION['userid'],
                    'DateCreated' => date('Y-m-d H:i:s')
                ));
                $sukses++;
            }
        }

        if ($this->db->trans_complete() === false) {
            $results['success'] = false;
            $results['message'] = lang("Failed to save data");
        } else {
            $results['success'] = true;
            $results['message'] = lang("Data Imported");
            $results['Insert']  = $sukses;
            $results['Exist']   = $exist;
        }

        return $results;
    }

    public function getMemberAdd($StaffAssignmentID, $StaffID, $textSearch, $ProvinceID, $DistrictID, $SubdistrictID, $VillageID, $start, $limit, $sortingField, $sortingDir){
        $this->load->model('grower/mgrower');
        if ($sortingField == "")
            $sortingField = 'a.MemberDisplayID';
        if ($sortingDir == "")
            $sortingDir = 'ASC';
        
        //========== Hak akses (Begin) =====================
        $sqlHakAkses = $this->mgrower->generateSqlHakAkses();
        //========== Hak akses (End) =======================

        $sqlwhere = "";

        if($ProvinceID != ""){
            $sqlHakAkses["where"] = " AND pv.ProvinceID = '$ProvinceID'";
        }

        if($DistrictID != ""){
            $sqlHakAkses["where"] = " AND f.DistrictID = '$DistrictID'";
        }

        if($SubdistrictID != ""){
            $sqlHakAkses["where"] = " AND subd.SubDistrictID = '$SubdistrictID'";
        }

        if($VillageID != ""){
            $sqlHakAkses["where"] = " AND vil.VillageID = '$VillageID'";
        }

        $sql = "SELECT SQL_CALC_FOUND_ROWS
                a.MemberID
                , a.MemberDisplayID
                , a.MemberName FarmerName
                , f.District
                , subd.SubDistrict
                , vil.Village
            FROM
                ktv_members a
            LEFT JOIN 
                (SELECT
	                ksam.MemberID
                FROM
	                ktv_staffs_assignment_member ksam
                LEFT JOIN
	                ktv_staffs_assignment ksa on ksa.StaffAssignmentID = ksam.StaffAssignmentID
                WHERE
	                ksa.StatusCode = 'active'
            ) ksa ON ksa.MemberID = a.MemberID
            INNER JOIN
                ktv_member_role ep on ep.MemberID = a.MemberID AND ep.MRoleID = 1
            LEFT JOIN
                ktv_village vil on vil.VillageID = a.VillageID
            LEFT JOIN
                ktv_subdistrict subd on subd.SubDistrictID = vil.SubDistrictID
            LEFT JOIN
                ktv_district f on f.DistrictID = subd.DistrictID
            LEFT JOIN
                ktv_province pv on f.ProvinceID = pv.ProvinceID
            $sqlHakAkses[join]
            WHERE
                a.StatusCode = 'active'
            AND 
                ksa.MemberID IS NULL
            AND 
                (a.MemberName LIKE ? OR a.MemberDisplayID LIKE ?)
            $sqlHakAkses[where] 
            GROUP BY a.MemberID
            ORDER BY $sortingField $sortingDir
            LIMIT ?,?";

        $query = $this->db->query($sql, array(
            '%'.$textSearch.'%', 
            '%'.$textSearch.'%',
            (int) $start, (int) $limit)
        );
        $result['data'] = $query->result_array();
        // echo '<pre>'; print_r($this->db->last_query()); exit;

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    public function setUserSessionLogin($UserId, $sso = false, $remark = null)
    {
        $this->db->query("SET SESSION group_concat_max_len = 1000000");
        
        $sql_login = "SELECT
                        a.*
                        , b.GroupName
                        , b.GroupId
                        , c.UnitId
                        , c.UnitName
                        , GROUP_CONCAT(DISTINCT e.DistrictId, '##', e.District) AS daerah
                        , GROUP_CONCAT(DISTINCT e.DistrictId) AS daerah_partner
                        , GROUP_CONCAT(DISTINCT e.ProvinceID) AS province
                        , GROUP_CONCAT(DISTINCT IFNULL(e.`District`, IFNULL(h.District, '-')) SEPARATOR ', ') AS district
                        , GROUP_CONCAT(DISTINCT h.DistrictId) AS daerah_access
                        , cp.DistrictID AS daerah_cpg
                        , i.FlagAccess
                        , IF(st.ObjType = 'private' || st.ObjType = 'program',st.ObjID,vss.PartnerID) AS PartnerID
                        , IFNULL(p.OfficialEmail, '-') AS official_email
                        , IFNULL(p.PrivateEmail, '-') AS private_email
                        , IFNULL(p.OfficialCellPhone, '-') AS official_phone
                        , IFNULL(p.PrivateCellPhone, '-') AS private_phone
                        , IFNULL(p.OfficialEmail, IFNULL(p.PrivateEmail, '-')) AS email
                        , IFNULL(p.OfficialCellPhone, IFNULL(p.PrivateCellPhone, '-')) AS phone
                        , b.GroupName AS group_name
                        , i.PartnerName AS partner_name
                        , IFNULL(i.AsParent, 'No') AsParent
                        , r.RoleName AS role
                        , p.Photo AS Photo_staff
                        , GroupFilterBy
                        , st_p.ProjID
                        , p.Gender
                        , GROUP_CONCAT(DISTINCT aff.UserIdAff SEPARATOR ',') AS UserAff
                    FROM
                        sys_user a
                        LEFT JOIN view_tc_supplychain_staff vss ON vss.UserID=a.UserId
                        LEFT JOIN sys_user_group ON UserGroupUserId = a.UserId AND UserGroupIsDefault = '1'
                        LEFT JOIN sys_group b ON UserGroupGroupId = b.GroupId
                        LEFT JOIN sys_unit c ON b.GroupUnitId = c.UnitId
                        LEFT JOIN ktv_persons p ON p.UserID = a.UserId

                        LEFT JOIN sys_user_role ur ON ur.UserId = a.UserId
                        LEFT JOIN sys_role r ON r.RoleId = ur.RoleId
                        LEFT JOIN ktv_staffs st ON p.PersonID = st.PersonID
                        LEFT JOIN ktv_staffs_project st_p ON st.StaffID = st_p.StaffID AND st_p.ProjDefault = '1'
                        LEFT JOIN sys_user_affiliate aff ON a.UserId = aff.UserId AND aff.StatusCode = 'active'

                        LEFT JOIN ktv_access_staff g ON a.UserId = g.UserId
                        LEFT JOIN ktv_district h ON g.DistrictID = h.DistrictID

                        LEFT JOIN ktv_program_partner i ON st.`ObjID` = i.`PartnerID` AND st.`ObjType` IN ('private','program')
                        LEFT JOIN ktv_district_partner z ON i.`PartnerID` = z.PartnerID
                        LEFT JOIN ktv_district e ON z.DistrictID = e.DistrictID

                        LEFT JOIN (
                            SELECT
                                GROUP_CONCAT(DISTINCT sd.DistrictID) AS DistrictID,
                                cp.PartnerID
                            FROM ktv_cpg_partner cp
                            JOIN ktv_cpg c ON c.CPGid = cp.CPGid
                            LEFT JOIN ktv_village v ON v.VillageID = c.VillageID
                            LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
                            GROUP BY cp.PartnerID
                        ) cp ON cp.PartnerID = i.PartnerID
                            WHERE a.UserId = ?";
        $DataLogin = $this->db->query($sql_login, array($UserId))->row_array();

        //Partner Commodity
        $sql = "SELECT 
                    GROUP_CONCAT(p.PartnerID) PartnerMultiple 
                FROM 
                    ktv_program_partner p 
                WHERE 
                    p.PartnerParentID = ?";
        $DataPartCom = $this->db->query($sql,array($DataLogin['PartnerID']))->row_array();

        $_SESSION['PartnerAsParent']     = $DataLogin['AsParent'];
        $_SESSION['PartnerChild']        = $DataPartCom['PartnerMultiple'];
        $_SESSION['username']           = $DataLogin['UserName'];
        $_SESSION['realname']           = $DataLogin['UserRealName'];
        $_SESSION['userid']             = $DataLogin['UserId'];
        $_SESSION['groupid']            = $DataLogin['GroupId'];
        $_SESSION['ProjID']             = $DataLogin['ProjID'];
        $_SESSION['unitid']             = $DataLogin['UnitId'];
        $_SESSION['daerah']             = $DataLogin['daerah'];
        $_SESSION['province']           = $DataLogin['province'];
        $_SESSION['PartnerID']          = $DataLogin['PartnerID'];
        $_SESSION['daerah_access']      = $DataLogin['daerah_access'];
        $_SESSION['language']           = $DataLogin['UserLanguage'];
        $_SESSION['official_email']     = $DataLogin['official_email'];
        $_SESSION['private_email']      = $DataLogin['private_email'];
        $_SESSION['email']              = $DataLogin['email'];
        $_SESSION['official_phone']     = $DataLogin['official_phone'];
        $_SESSION['private_phone']      = $DataLogin['private_phone'];
        $_SESSION['phone']              = $DataLogin['phone'];
        $_SESSION['group']              = $DataLogin['group_name'];
        $_SESSION['partner']            = $DataLogin['partner_name'];
        $_SESSION['district']           = $DataLogin['district'];
        $_SESSION['Photo_staff']        = $DataLogin['Photo_staff'];
        $_SESSION['role']               = $DataLogin['role'];
        $_SESSION['filter_by']          = $DataLogin['GroupFilterBy'];
        $_SESSION['is_admin']           = $DataLogin['UserIsAdmin'];
        $_SESSION['FlagAccess']         = $DataLogin['FlagAccess'];
        $_SESSION['Gender']         = $DataLogin['Gender'];
        $_SESSION['userid_beforeswitch'] = $DataLogin['UserId'];
        $_SESSION['UserAff'] = $DataLogin['UserAff'];

        //SupplychainID
        $getSesSupp = $this->db->select('SupplychainID, PartnerID')->from('view_tc_supplychain_staff')->where('UserID', $_SESSION['userid'] )->get()->row(); 
        $SupplychainID ='';
        if($getSesSupp) {
            $_SESSION['SupplychainID'] = $getSesSupp->SupplychainID;
            $_SESSION['PartnerID'] = $getSesSupp->PartnerID;
        } else {
            $_SESSION['SupplychainID'] = null;
        }

        //write log login
        writeLogUserAccess('Login', $_SESSION['userid'], 'Success', $remark);
    }

    public function GetDataUserFromByUserId($UserId)
    {
        $return = array();
        $sql = "SELECT
                    b.`PersonNm` AS fullname
                    , b.`OfficialEmail` AS email
                    , b.`OfficialCellPhone` AS phonenumber
                    , a.`UserName` AS username
                    , b.PersonID
                FROM
                    sys_user a
                    INNER JOIN ktv_persons b ON a.`UserId` = b.`UserID`
                WHERE 1=1
                    AND a.`UserId` = ?
                LIMIT 1";
        $p = array($UserId);
        $data = $this->db->query($sql, $p)->row_array();

        if ($data['fullname'] != "") {
            $return['success'] = true;
            $return['data'] = $data;
        } else {
            $return['success'] = false;
            $return['data'] = array();
        }
        return $return;
    }

    public function UpdateUserTor($post) {
        $return = array();
        $sql = "UPDATE sys_user a SET
                    a.`UserTorStatus` = '1'
                WHERE
                    a.UserId = ?
                LIMIT 1";
        $proses = $this->db->query($sql,array($_SESSION['userid']));
        if($proses == true) {
            $return['success'] = true;
            $return['message'] = lang('Process Success');
        } else {
            $return['success'] = false;
            $return['message'] = lang('Process Failed');
        }
        return $return;
    }

    //erdeha
    //Set User Login by Email
    //User has been validated by SSO
    public function setUserSessionLoginByEmail($email,$remark){
        $result = false;

        $q = $this->db->select('UserId as id')
                    ->from('sys_user')
                    ->where('UserEmail', $email)
                    ->get();

        if($q->num_rows()){
            $result = true;
            $user = $q->row_array();
            $_SESSION['userid'] = $user['id'];

            $this->setUserSessionLogin($user['id'], true, $remark);
        }

        return $result;
    }

    public function GetGridLogUserLogin($PersonID) {
        $return = array();

        $sql = "SELECT
                    b.`type` AS `Type`
                    , b.`SessionIP` AS IPAddress
                    , DATE_FORMAT(b.`Timestamp`, '%d %M %Y %H:%i:%s') AS `Timestamp`
                    , b.`Remark`
                FROM
                    ktv_persons a
                    INNER JOIN sys_log_access b ON a.`UserID` = b.`UserID`
                WHERE 1=1
                    AND a.`PersonID` = ?
                ORDER BY b.`Timestamp` DESC
                LIMIT 10";
        $p = array(
            $PersonID
        );
        $data = $this->db->query($sql,$p)->result_array();

        $return['data'] = $data;
        $return['success'] = true;
        return $return;
    }

    public function UpdateUserDhisForm($paramPost) {
        $PredefinedPass = 'KtvMd2020!';

        //Data sys_user
        $sql = "SELECT
                    a.`UserId`
                    , a.`UserName`
                    , a.`UserRealName` AS `Name`
                    , a.UserExtId
                    , a.UserExtGroupId
                    , a.UserExtRoleId
                FROM
                    sys_user a
                WHERE 1=1
                    AND a.`UserId` = ?
                LIMIT 1";
        $DataUser = $this->db->query($sql,array($paramPost['UserId']))->row_array();

        $tmpName   = explode(" ", $paramPost['Name']);
        $firstName = $tmpName[0];
        unset($tmpName[0]);
        $lastName = implode(" ", $tmpName);
        if(strlen($lastName) < 2){
            switch (strlen($lastName)) {
                case 0:
                    $lastName = "..";
                break;
                case 1:
                    $lastName = $lastName.".";
                break;
            }
        }

        $sql="SELECT
                a.`DistrictID`,
                b.`District`,
                mworg.uid
            FROM
                ktv_access_staff a
                LEFT JOIN ktv_district b ON a.`DistrictID` = b.`DistrictID`
                LEFT JOIN mw_organisationunit mworg ON b.`District` = mworg.`name`
            WHERE
                a.`UserId` = ?";
        $query = $this->db->query($sql,array($paramPost['UserId']));
        $dataOrgUnit = $query->result_array();
        $tmpJson = array();

        foreach ($dataOrgUnit as $key => $value) {
            if($value['uid'] != ""){
                $tmpJson[]['id'] = $value['uid'];
            }
        }
        $jsonOrgUnit = json_encode($tmpJson);

        //User Group DHIS ============================= (Begin)
        if($paramPost['CmbDhisGroup'] != ""){
            $AppGroupUidRaw = $paramPost['CmbDhisGroup'];

            $TmpAppGroupUid = explode(',',$paramPost['CmbDhisGroup']);
            $TmpJsonAppGroupUid = array();            
            foreach ($TmpAppGroupUid as $key => $value) {
                $TmpJsonAppGroupUid[]['id'] = $value;
            }
            $JsonAppGroupUid = json_encode($TmpJsonAppGroupUid);
        }else{
            $JsonAppGroupUid = null;
            $AppGroupUidRaw = null;
        }
        //User Group DHIS ============================= (End)

        //User Role DHIS ============================= (Begin)
        if($paramPost['CmbDhisRole'] != ""){
            $AppRoleUidRaw = $paramPost['CmbDhisRole'];

            $TmpAppRoleUid = explode(',',$paramPost['CmbDhisRole']);
            $TmpJsonAppRoleUid = array();
            foreach ($TmpAppRoleUid as $key => $value) {
                $TmpJsonAppRoleUid[]['id'] = $value;
            }
            $JsonAppRoleUid = json_encode($TmpJsonAppRoleUid);
        }else{
            $JsonAppRoleUid = null;
            $AppRoleUidRaw = null;
        }
        //User Role DHIS ============================= (End)

        $bodyJson = '{
            "firstName": "'.$firstName.'",
            "surname": "'.$lastName.'",
            "userCredentials": {
                "username": "'.$paramPost['UserName'].'",
                "password": "'.$PredefinedPass.'",
                "userRoles": '.$JsonAppRoleUid.'
            },
            "organisationUnits": '.$jsonOrgUnit.',
            "userGroups": '.$JsonAppGroupUid.'
        }';

        if($DataUser['UserExtId'] == "") { //CREATE
            $url = $this->config->item('dhis_url').'api/users/';
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Basic YWRtaW46S29sdGl2YTIwMTMh'
            ));
            curl_setopt($ch, CURLOPT_POSTFIELDS, ($bodyJson));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            $curlresult = json_decode($result,true);
            //echo '<pre>'; print_r($curlresult);

            if($curlresult['lastImported'] != "") {
                $uidDhis = $curlresult['lastImported'];

                //update user id di ktv_persons
                $sql="UPDATE sys_user SET
                            UserExtId = ?,
                            UserExtPassword = ?,
                            UserExtGroupId = ?,
                            UserExtRoleId = ?
                        WHERE
                            UserId = ?
                        LIMIT 1";
                $query = $this->db->query($sql,array($uidDhis,md5($PredefinedPass),$AppGroupUidRaw,$AppRoleUidRaw,$paramPost['UserId']));

                $return['success'] = true;
                $return['message'] = lang('User Created');
            }else{
                $return['success'] = false;
                $return['message'] = lang('Failed to create user on DHIS');
            }

        } else { //UPDATE
            $url = $this->config->item('dhis_url').'api/users/'.$paramPost['UserExtId'];
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Basic YWRtaW46S29sdGl2YTIwMTMh'
            ));
            curl_setopt($ch, CURLOPT_POSTFIELDS, ($bodyJson));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            $curlresult = json_decode($result,true);

            if($curlresult['status'] == "SUCCESS") {
                //update
                $sql="UPDATE sys_user SET
                            UserExtGroupId = ?,
                            UserExtRoleId = ?
                        WHERE
                            UserId = ?
                        LIMIT 1";
                $query = $this->db->query($sql,array($AppGroupUidRaw,$AppRoleUidRaw,$paramPost['UserId']));

                $return['success'] = true;
                $return['message'] = lang('User Updated');
            } else {
                $return['success'] = false;
                $return['message'] = lang('Failed to update user on DHIS');
            }
        }

        return $return;
    }

    public function GetDataUserAccByUsername($Username) {
        $sql = "SELECT
                `UserId`,
                `UserRealName`,
                `UserName`,
                `UserPassword`,
                `UserEmail`,
                `UserActive`,
                `UserLanguage`,
                `UserNotification`,
                `UserInCognito`,
                `CognitoUserSub`,
                `CognitoUserStatus`,
                `UserUnitId`,
                `UserIsAdmin`,
                `UserDescription`
            FROM
                sys_user a
            WHERE
                a.`UserName` = ?
            LIMIT 1";
        return $this->db->query($sql,array($Username))->row_array();
    }

}