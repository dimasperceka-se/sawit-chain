<?php

class System extends CI_Model
{

    public function Model()
    {
        parent::Model();
    }

    public function getStaffId($userId) {
        $staff_id = $this->post['staff_id'];
        $sql = "SELECT
            `ktv_persons`.`Photo` ,
            `ktv_persons`.`PersonID`,
            `ktv_persons`.`PersonNm`,
            ktv_staffs.StaffID,
            ktv_staffs.ObjType as Roles

            FROM `ktv_persons`

            LEFT JOIN sys_user ON `ktv_persons`.UserID = sys_user.UserId
            LEFT JOIN ktv_staffs ON `ktv_staffs`.PersonID = ktv_persons.PersonID

            where `ktv_persons`.UserID = ?";
        return $this->db->query($sql,$userId)->row_array()['StaffID'];

    }

    public function getListAccessStaffApp($UserId){
        $sql="SELECT
                    a.`DistrictID`,
                    b.`District`
                FROM
                    ktv_access_staff a
                    LEFT JOIN ktv_district b ON a.`DistrictID` = b.`DistrictID`
                WHERE
                    a.`UserId` = ?
                ORDER BY a.DistrictID ASC";
        $query = $this->db->query($sql,array($UserId));
        $dataReturn = $query->result_array();

        $return['success'] = true;
        $return['data'] = $dataReturn;
        return $return;
    }

    public function getFormUser($StaffID){
        $sql="SELECT
                a.`StaffID`,
                c.`UserId`,
                b.`PersonNm`,
                srole.`RoleName` AS RoleLabel,
                srole.RoleId,
                c.StatusCode,
                c.UserName,
                IF(c.UserLanguage='English','1','2') AS UserLanguage
            FROM
                ktv_staffs a
                INNER JOIN sys_role srole ON a.`ObjType` = srole.`RoleCode`
                INNER JOIN ktv_persons b ON a.`PersonID` = b.`PersonID`
                LEFT JOIN sys_user c ON b.`UserID` = c.`UserId`
            WHERE
                a.`StaffID` = ?
            LIMIT 1";
        $query = $this->db->query($sql, array($StaffID));
        $dataReturn = $query->result_array();

        $groups = array();
        $default_group = 0;
        $query = $this->db->get_where('sys_user_group', array('UserGroupUserId' => $dataReturn[0]['UserId']));
        $data = $query->result_array();
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $groups[] = $value['UserGroupGroupId'];
                if ($value['UserGroupIsDefault'] == '1') {
                    $default_group = $value['UserGroupGroupId'];
                }
            }
        }
        $dataReturn[0]['UserGroupIsDefault'] = $default_group;
        $dataReturn[0]['groups'] = $groups;

        $access = array();
        $query = $this->db->get_where('ktv_access_staff', array('UserId' => $dataReturn[0]['UserId']));
        $data = $query->result_array();
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $access[] = $value['DistrictID'];
            }
        }
        $dataReturn[0]['access'] = $access;

        //access role
        $accessRoleId = array();
        $sql="SELECT
                RoleId
            FROM
                sys_user_access_role
            WHERE
                UserId = ?
            ORDER BY UserAccRoleID ASC";
        $query = $this->db->query($sql,array($dataReturn[0]['UserId']));
        $data = $query->result_array();
        foreach ($data as $key => $value) {
            $accessRoleId[] = $value['RoleId'];
        }
        $dataReturn[0]['accessRoleId'] = $accessRoleId;

        $return['success'] = true;
        $return['data'] = $dataReturn[0];
        return $return;
    }
    public function getFullName($userName){
        $sql = "SELECT 
                    sys_user.UserName,
                    ktv_persons.PersonNm,
                    ktv_persons.BirthDate,
                    ktv_persons.Gender,
                    ktv_persons.Photo,
                    CONCAT(IFNULL(ktv_persons.`OfficialCellPhoneCode`,''),IFNULL(ktv_persons.`OfficialCellPhone`,'')) AS OfficialCellPhone,
                    ktv_persons.OfficialEmail
                FROM 
                    sys_user 
                    LEFT JOIN ktv_persons ON sys_user.UserId = ktv_persons.UserID 
                where sys_user.UserName = ?";
        return $this->db->query($sql,array($userName))->row_array();

    }
    public function GetSql($sql, $arrayParam)
    {
        $query  = $this->db->query($sql, $arrayParam);
        $result = $query->result_array();
        return $result;
    }

    public function DoSql($sql, $arrayParam)
    {
        return $this->db->query($sql, $arrayParam);
    }

    public function StartTrans()
    {
        $this->db->trans_start();
    }

    public function EndTrans()
    {
        $this->db->trans_complete();
    }

    public function StatusTrans()
    {
        return $this->db->trans_status();
    }

    public function RollTrans()
    {
        $this->db->trans_rollback();
    }

    public function GetLastId()
    {
        return $this->db->insert_id();
    }

    public function CekAksi($func, $module = '')
    {
        if ($module == '') {
            $module = $this->uri->segment(1) . '/' . $this->uri->segment(2);
        }

        $sql    = "SELECT GroupMenuSegmen as id FROM sys_group_menu_act WHERE GroupMenuGroupId=? and GroupMenuSegmen like ?";
        $query  = $this->db->query($sql, array($_SESSION['groupid'], $module . '/' . $func . '%'));
        $result = $query->result_array();
        if ($result[0]['id'] == '') {
            return false;
        } else {
            return true;
        }

    }

    function getAccess(){
        $sql = "SELECT 
                    a.IsFarmer, 
                    a.IsNonFarmer, 
                    a.IsCompany, 
                    a.IsBatch, 
                    a.CurrID, 
                    a.IsSent
                From 
                    ktv_tc_supplychain_org a
                WHERE
                    a.SupplychainID = ?
            ";
        $query = $this->db->query($sql,array($_SESSION['SupplychainID']));

        $result["IsFarmer"]     = $query->row()->IsFarmer;
        $result["IsNonFarmer"]  = $query->row()->IsNonFarmer;
        $result["IsCompany"]    = $query->row()->IsCompany;
        $result["IsBatch"]      = $query->row()->IsBatch;
        $result["CurrID"]       = $query->row()->CurrID;
        $result["IsSent"]       = $query->row()->IsSent;

        return $result;
    }

    public function cekSettingPerUser($fieldNama)
    {
        $userid = $_SESSION['userid'];
        $sql    = "SELECT
               UserSetID,
               UserSetUserId,
               SetTmplKey,
               UserSetSetTmplID,
               UserSetValue
            FROM
               sys_user_setting
               INNER JOIN sys_setting_template ON UserSetSetTmplID = SetTmplID
            WHERE
               UserSetUserId = ?
            ORDER BY UserSetSetTmplID ASC";
        $p = array(
            $userid,
        );
        $query       = $this->db->query($sql, $p);
        $dataSetting = $query->result_array();

        //============ convert jadi variabel array (begin) ================//
        $settingArray = array();
        foreach ($dataSetting as $key => $value) {
            $settingArray[$value['SetTmplKey']] = $value['UserSetValue'];
        }
        //============ convert jadi variabel array (end) ==================//

        //===== latitude short (begin) ============//
        if ($fieldNama == "latShort") {
            // 1 = Check GIS Status
            if ($settingArray['check_gis_status'] == "Yes") {
                return false;
            } else {
                return true;
            }
        }
        //===== latitude short (end) ==============//

        //===== longtitude short (begin) ============//
        if ($fieldNama == "longShort") {
            // 1 = Check GIS Status
            if ($settingArray['check_gis_status'] == "Yes") {
                return false;
            } else {
                return true;
            }
        }
        //===== longtitude short (end) ==============//

        //===== latitude long (begin) ============//
        if ($fieldNama == "latLong") {
            // 1 = Check GIS Status
            if ($settingArray['check_gis_status'] == "Yes") {
                return false;
            } else {
                return true;
            }
        }
        //===== latitude long (end) ==============//

        //===== longtitude long (begin) ============//
        if ($fieldNama == "longLong") {
            // 1 = Check GIS Status
            if ($settingArray['check_gis_status'] == "Yes") {
                return false;
            } else {
                return true;
            }
        }
        //===== longtitude long (end) ==============//

        //===== elevation (begin) ============//
        if ($fieldNama == "elevation") {
            // 1 = Check GIS Status
            if ($settingArray['check_gis_status'] == "Yes") {
                return false;
            } else {
                return true;
            }
        }
        //===== elevation (end) ==============//

        //===== polygon (begin) ============//
        if ($fieldNama == "polygon") {
            // 1 = Check GIS Status
            if ($settingArray['check_gis_status'] == "Yes") {
                return false;
            } else {
                return true;
            }
        }
        //===== polygon (end) ==============//

        //jika tidak tertangkap diatas semua, maka balikkan true
        return true;
    }

    public function writeLogAccess($type, $UserID, $AttempProcess)
    {
        $this->load->helper('security');

        $sql = "INSERT INTO `sys_log_access` SET
              `type` = ?,
              `UserID` = ?,
              `SessionIP` = ?,
              `UserAgent` = ?,
              `AttempProcess` = ?";
        $p = array(
            $type,
            $UserID,
            ip_address(),
            user_agent(),
            $AttempProcess
        );
        return $this->db->query($sql, $p);
    }

    public function writeLogChangePass($UserID,$passwd){
        $sql = "INSERT INTO `sys_log_account` SET
                  `UserId` = ?,
                  `Passwd` = ?";
        $p = array(
            $UserID,
            $passwd
        );
        return $this->db->query($sql, $p);
    }

    public function checkPreviousPassword($newpassword,$userid){
        $sql="SELECT
                logID
            FROM
                sys_log_account a
            WHERE
                a.`UserId` = ?
                AND a.Passwd = ?";
        $query = $this->db->query($sql,array($userid,md5($newpassword)));
        $data = $query->row_array();

        if(empty($data)){
            //bandingkan ke passwordnya yg sekarang lagi dipakai
            $sql="SELECT
                    a.`UserId`
                FROM
                    sys_user a
                WHERE
                    a.`UserId` = ?
                    AND a.UserPassword = ?";
            $query = $this->db->query($sql,array($userid,$newpassword));
            $data2 = $query->row_array();

            if(empty($data2)){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function changePasswordDhis($dataUser,$newPass){
        $dataUserStaff = $dataUser[0];

        if($dataUserStaff['UserExtId'] != ""){
            //update user ke dhis (begin)
            $tmpName   = explode(" ", $dataUserStaff['PersonNm']);
            $firstName = $tmpName[0];
            unset($tmpName[0]);
            $lastName = implode(" ", $tmpName);

            //org unit (begin)
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
            $query = $this->db->query($sql,array($dataUserStaff['UserId']));
            $dataOrgUnit = $query->result_array();

            $tmpJson = array();
            foreach ($dataOrgUnit as $key => $value) {
                if($value['uid'] != ""){
                    $tmpJson[]['id'] = $value['uid'];
                }
            }
            $jsonOrgUnit = json_encode($tmpJson);
            //org unit (end)

            //User Group DHIS ============================= (Begin)
            if($dataUserStaff['UserExtGroupId'] != ""){
                $AppGroupUidRaw = $dataUserStaff['UserExtGroupId'];

                $TmpAppGroupUid = explode(',',$dataUserStaff['UserExtGroupId']);
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
            if($dataUserStaff['UserExtRoleId'] != ""){
                $AppRoleUidRaw = $dataUserStaff['UserExtRoleId'];

                $TmpAppRoleUid = explode(',',$dataUserStaff['UserExtRoleId']);
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
                    "username": "'.$dataUserStaff['UserName'].'",
                    "password": "'.$newPass.'",
                    "userRoles": '.$JsonAppRoleUid.'
                },
                "organisationUnits": '.$jsonOrgUnit.',
                "userGroups": '.$JsonAppGroupUid.'
            }';

            $url = $this->config->item('dhis_url').'api/users/'.$dataUserStaff['UserExtId'];
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
                return true;
            }else{
                return false;
            }

        }else{
            return true;
        }
    }

    public function resetPassword($userObj, $newPass)
    {
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
         WHERE a.UserId = ? GROUP BY a.UserId";
        $user = $this->GetSql($sql_login, array($userObj->UserId));
        
        if ($user[0]['UserName'] != "" && $user[0]['UserActive'] == 'Yes') {
            
            $this->db->trans_begin();

            $sql = "UPDATE sys_user SET
                UserPassword = ?
            WHERE
                UserId = ?
            LIMIT 1";
            $query = $this->db->query($sql, array(md5($newPass), $userObj->UserId));

            if($query == true){
                //hapus request reset password
                $sql="DELETE FROM sys_user_newpass WHERE user_id = ?";
                $query = $this->db->query($sql,array($userObj->UserId));
            }

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();

                //write log
                $this->writeLogAccess('Reset Password',$userObj->UserId,'Failed');

                return false;
            } else {

                //update password ke dhis
                $prosesPassDhis = $this->changePasswordDhis($user,$newPass);
                if($prosesPassDhis ==  true){

                    $this->db->trans_commit();

                    //write log
                    $this->writeLogAccess('Reset Password',$userObj->UserId,'Success');

                    //write log password database
                    $this->writeLogChangePass($userObj->UserId,$user[0]['UserPassword']);

                    //write log login
                    $this->writeLogAccess('Login',$userObj->UserId,'Success');

                    $_SESSION['username']           = $user[0]['UserName'];
                    $_SESSION['realname']           = $user[0]['UserRealName'];
                    $_SESSION['userid']             = $user[0]['UserId'];
                    $_SESSION['groupid']            = $user[0]['GroupId'];
                    $_SESSION['ProjID']             = $user[0]['ProjID'];
                    $_SESSION['unitid']             = $user[0]['UnitId'];
                    $_SESSION['daerah']             = $user[0]['daerah'];
                    $_SESSION['province']           = $user[0]['province'];
                    $_SESSION['PartnerID']          = $user[0]['PartnerID'];
                    $_SESSION['daerah_access']      = $user[0]['daerah_access'];
                    $_SESSION['language']           = $user[0]['UserLanguage'];
                    $_SESSION['official_email']     = $user[0]['official_email'];
                    $_SESSION['private_email']      = $user[0]['private_email'];
                    $_SESSION['email']              = $user[0]['email'];
                    $_SESSION['official_phone']     = $user[0]['official_phone'];
                    $_SESSION['private_phone']      = $user[0]['private_phone'];
                    $_SESSION['phone']              = $user[0]['phone'];
                    $_SESSION['group']              = $user[0]['group_name'];
                    $_SESSION['partner']            = $user[0]['partner_name'];
                    $_SESSION['district']           = $user[0]['district'];
                    $_SESSION['Photo_staff']        = $user[0]['Photo_staff'];
                    $_SESSION['role']               = $user[0]['role'];
                    $_SESSION['filter_by']          = $user[0]['GroupFilterBy'];
                    $_SESSION['is_admin']           = $user[0]['UserIsAdmin'];
                    $_SESSION['FlagAccess']         = $user[0]['FlagAccess'];
                    $_SESSION['Gender']         = $user[0]['Gender'];
                    $_SESSION['userid_beforeswitch'] = $user[0]['UserId'];
                    $_SESSION['UserAff'] = $user[0]['UserAff'];

                    //SupplychainID
                    $getSesSupp = $this->db->select('SupplychainID, PartnerID')->from('view_tc_supplychain_staff')->where('UserID', $_SESSION['userid'] )->get()->row(); 
                    $SupplychainID ='';
                    if($getSesSupp) {
                        $_SESSION['SupplychainID'] = $getSesSupp->SupplychainID;
                        $_SESSION['PartnerID'] = $getSesSupp->PartnerID;
                    } else {
                        $_SESSION['SupplychainID'] = null;
                    }

                    return true;
                }else{ //update password dhis failed
                    $this->db->trans_rollback();
                    return false;
                }

            }

        }else{
            return false;
        }
    }

    public function updateUserTor($userid){
        $sql="UPDATE sys_user SET
                UserTorStatus = '1'
            WHERE
                UserId = ?
            LIMIT 1";
        $query = $this->db->query($sql,array($userid));
        return $query;
    }

    public function getDataRegisterStaff($RegID,$Username){
        $sql="SELECT
                a.Fullname
                , a.Email
                , a.Username
                , a.ObjType
                , a.ObjID
                ,CASE
                    WHEN a.ObjType = 'program' THEN 'Program'
                    WHEN a.ObjType = 'private' THEN 'Private'
                    WHEN a.ObjType = 'service' THEN 'Service Provider'
                    WHEN a.ObjType = 'mill' THEN 'Mill'
                    WHEN a.ObjType = 'agent' THEN 'Agent'
                END AS UserRole
                , CASE
                    WHEN a.ObjType = 'program' THEN
                        (
                            SELECT PartnerName FROM ktv_program_partner WHERE PartnerID = a.ObjID
                        )
                    WHEN a.ObjType = 'private' THEN
                        (
                            SELECT PartnerName FROM ktv_program_partner WHERE PartnerID = a.ObjID
                        )
                    WHEN a.ObjType = 'service' THEN
                        (
                            SELECT OfficialName FROM ktv_service_provider WHERE ServiceProvID = a.ObjID
                        )
                    WHEN a.ObjType = 'mill' THEN
                        (
                            SELECT CONCAT(MillDisplayID,' - ',MillName) FROM ktv_mill WHERE MillID = a.ObjID
                        )
                    WHEN a.ObjType = 'agent' THEN
                        (
                            SELECT CONCAT(MemberDisplayID,' - ',MemberName) FROM ktv_members WHERE MemberID = a.ObjID
                        )
                END AS ObjLabel
                , pos.PositionName AS `Position`
                , GROUP_CONCAT(DISTINCT dis.District ORDER BY dis.DistrictID SEPARATOR '@') AS AccessArea
                , a.GroupIDDefa
                , a.IsMobileDhisUser
                , a.UserExtRoleId
                , a.UserExtGroupId
                , a.PositionID
                , GROUP_CONCAT(DISTINCT dis.DistrictID SEPARATOR '@') AS AccessAreaID
                , GROUP_CONCAT(DISTINCT ugrup.GroupId SEPARATOR '@') AS UserGroupID
            FROM
                register_staff a
                LEFT JOIN `ktv_ref_position_type` pos ON a.PositionID = pos.PositionID
                LEFT JOIN register_staff_access_area acc ON a.RegID = acc.RegID
                LEFT JOIN ktv_district dis ON acc.DistrictID = dis.DistrictID
                LEFT JOIN `register_staff_user_group` ugrup ON a.RegID = ugrup.RegID
            WHERE
                a.`RegID` = ?
                AND a.Username = ?
                AND a.StatusRegistered = '0'
            GROUP BY a.RegID
            LIMIT 1";
        $query = $this->db->query($sql, array((int) $RegID, $Username));
        return $query->row_array();
    }

    public function getComboPropinsi(){
        $sql="SELECT
                a.`ProvinceID` AS id
                , a.`Province` AS label
            FROM
                ktv_province a
            WHERE
                a.`active` = '1'
                AND a.`StatusCode` = 'active'";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function getComboDistrict($ProvinceID){
        $sql="SELECT
                a.`DistrictID` AS id
                , a.`District` AS label
            FROM
                ktv_district a
            WHERE
                a.`active` = '1'
                AND a.`StatusCode` = 'active'
                AND a.`ProvinceID` = ?";
        $query = $this->db->query($sql, array((int) $ProvinceID));
        return $query->result_array();
    }

    public function prosesRegisterStaff($paramPost){
        $this->db->trans_start();

        //ambil data ====================================== (begin)
        $paramRegis = pack("H*", $paramPost['paramSegment']);
        $arrTmp = explode('@',$paramRegis);
        $RegID = $arrTmp[0];
        $Username = $arrTmp[1];

        $dataRegister = $this->getDataRegisterStaff($RegID, $Username);

        //get WorkAreaID
        $sql="SELECT
                a.`WorkAreaID`
            FROM
                ktv_ref_work_area a
            WHERE
                a.`DistrictID` = '{$paramPost['work_area_district']}'
            LIMIT 1";
        $query = $this->db->query($sql);
        $data = $query->row_array();
        $WorkAreaID = $data['WorkAreaID'];

        //get RoleId
        $sql="SELECT
                a.`RoleId`
            FROM
                sys_role a
            WHERE
                a.RoleCode = ?
            LIMIT 1";
        $p = array(
            $dataRegister['ObjType']
        );
        $query = $this->db->query($sql,$p);
        $data = $query->row_array();
        $RoleId = $data['RoleId'];
        //ambil data ====================================== (end)

        //insert ke ktv_persons
        $sql="INSERT INTO `ktv_persons` SET
                `WorkAreaID` = ?,
                `PersonNm` = ?,
                `BirthDate` = ?,
                `Gender` = ?,
                `Email` = ?,
                `WorkPhone` = ?,
                `PrivateCellPhone` = ?,
                `OfficialCellPhone` = ?,
                `PrivateEmail` = ?,
                `OfficialEmail` = ?,
                `DateCreated` = NOW(),
                `CreatedBy` = 1";
        $p = array(
            $WorkAreaID,
            $dataRegister['Fullname'],
            $paramPost['tglLahir'],
            $paramPost['gender'],
            $dataRegister['Email'],
            $paramPost['cellphone'],
            $paramPost['cellphone'],
            $paramPost['cellphone'],
            $dataRegister['Email'],
            $dataRegister['Email']
        );
        $query = $this->db->query($sql,$p);
        $PersonID = $this->db->insert_id();

        //insert ke ktv_staffs
        $sql="INSERT INTO `ktv_staffs` SET
                `PersonID` = ?,
                `ObjType` = ?,
                `ObjID` = ?,
                `OfficialPhone` = ?,
                `PrivatePhone` = ?,
                `WorkPhone` = ?,
                `OfficialEmail` = ?,
                `PrivateEmail` = ?,
                `WorkAreaID` = ?,
                `DateCreated` = NOW(),
                `CreatedBy` = '1'";
        $p = array(
            $PersonID,
            $dataRegister['ObjType'],
            $dataRegister['ObjID'],
            $paramPost['cellphone'],
            $paramPost['cellphone'],
            $paramPost['cellphone'],
            $dataRegister['Email'],
            $dataRegister['Email'],
            $WorkAreaID
        );
        $query = $this->db->query($sql,$p);
        $StaffID = $this->db->insert_id();

        //insert ke ktv_staff_positions
        $sql="INSERT INTO `ktv_staff_positions` SET
               `StaffPosStaffID` = ?,
               `StaffPosPositionID` = ?,
               `StaffPostStart` = CURDATE(),
               `StaffPostEnd` = '9999-12-31',
               `StatusCode` = 'active',
               `DateCreated` = NOW(),
               `CreatedBy` = '1'";
        $p = array(
            $StaffID,
            $dataRegister['PositionID']
        );
        $query = $this->db->query($sql, $p);

        //insert ke sys_user
        $sql="INSERT INTO `sys_user` SET
            `UserRealName` = ?,
            `UserName` = ?,
            `UserPassword` = ?,
            `UserEmail` = ?,
            `UserActive` = 'Yes',
            `UserLanguage` = ?,
            `UserIsAdmin` = '0',
            `UserTorStatus` = '1',
            `UserAddUserId` = '1',
            `UserAddTime` = NOW()";
        $p = array(
            $dataRegister['Fullname'],
            $dataRegister['Username'],
            md5($paramPost['password']),
            $dataRegister['Email'],
            $paramPost['app_lang']
        );
        $query = $this->db->query($sql,$p);
        $UserId = $this->db->insert_id();

        //insert ke sys_user_role
        $p = array(
            'UserId' => $UserId,
            'RoleId' => $RoleId
        );
        $query = $this->db->insert('sys_user_role', $p);

        //insert user groups
        $GroupIds = explode('@',$dataRegister['UserGroupID']);
        foreach ($GroupIds as $key => $GroupId) {
            $isDefault = $GroupId == $dataRegister['GroupIDDefa'] ? '1' : '0';
            $p = array(
                'UserGroupUserId' => $UserId,
                'UserGroupGroupId' => $GroupId,
                'UserGroupIsDefault' => $isDefault
            );
            $query = $this->db->insert('sys_user_group', $p);
        }

        //staffs_project
        $p = array(
            'StaffID' => $StaffID,
            'ProjID' => '1',
            'ProjDefault' => '1',
            'DateCreated' => date('Y-m-d H:i:s'),
            'CreatedBy' => '1'
        );
        $query = $this->db->insert('ktv_staffs_project', $p);

        //insert user access
        if($dataRegister['AccessAreaID'] != ""){
            $AccessStaff = explode('@',$dataRegister['AccessAreaID']);
            foreach ($AccessStaff as $key => $DistrictID) {
                $query = $this->db->insert('ktv_access_staff', array(
                    'UserId' => $UserId,
                    'DistrictID' => $DistrictID,
                ));
            }
        }

        //update user id di ktv_persons
        $sql="UPDATE ktv_persons SET
                    UserID = ?
                WHERE
                    PersonID = ?
                LIMIT 1";
        $query = $this->db->query($sql,array($UserId,$PersonID));


        //=============================== User Mobile (Begin) =================================================//
        if($dataRegister['IsMobileDhisUser'] == "1"){
            //Proses Fullname
            $tmpName   = explode(" ", $dataRegister['Fullname']);
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

            //org unit (begin)
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
            $query = $this->db->query($sql,array($UserId));
            $dataOrgUnit = $query->result_array();

            $tmpJson = array();
            foreach ($dataOrgUnit as $key => $value) {
                if($value['uid'] != ""){
                    $tmpJson[]['id'] = $value['uid'];
                }
            }
            $jsonOrgUnit = json_encode($tmpJson);
            //org unit (end)

            $bodyJson = '{
                "firstName": "'.$firstName.'",
                "surname": "'.$lastName.'",
                "email": "'.$dataRegister['Email'].'",
                "userCredentials": {
                    "username": "'.$dataRegister['Username'].'",
                    "password": "'.$paramPost['password'].'",
                "userRoles": [ {
                    "id": "'.$dataRegister['UserExtRoleId'].'"
                } ]
                },
                "organisationUnits": '.$jsonOrgUnit.',
                "userGroups": [ {
                    "id": "'.$dataRegister['UserExtGroupId'].'"
                } ]
            }';

            $url = $this->config->item('dhis_url').'api/users';
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
                $query = $this->db->query($sql,array($uidDhis,md5($paramPost['password']),$dataRegister['UserExtGroupId'],$dataRegister['UserExtRoleId'],$UserId));
            }
        }
        //=============================== User Mobile (End)   =================================================//

        //update kembali data di register staff
        $sql="UPDATE register_staff SET
                StatusRegistered = '1'
                , DateRegistered = NOW()
            WHERE
                RegID = ?
            LIMIT 1";
        $query = $this->db->query($sql, array($RegID));

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Success";

            //query data untuk login
            $results['userLogin'] = $this->getDataForSessionLogin($UserId);
        }else{
            $results['success'] = false;
            $results['message'] = "Failed";
        }

        return $results;
    }

    public function getDataForSessionLogin($UserId){
        $sql="SELECT
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
            WHERE
                a.UserId = ?
            LIMIT 1";
        $query = $this->db->query($sql, array($UserId));
        return $query->row_array();
    }

}