<?php
/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Thu Aug 13 2020
 *  File : mtoolscognito.php
 *******************************************/
class Mtoolscognito extends CI_Model {

    public $clientCog;

    public function __construct() {
        parent::__construct();

        $this->load->config('awscognito');
        $configAwsCog = $this->config->item('awscog');
        $aws = new \Aws\Sdk($configAwsCog);
        $cognitoClient = $aws->createCognitoIdentityProvider();

        $this->clientCog = new \pmill\AwsCognito\CognitoClient($cognitoClient);
        $this->clientCog->setAppClientId($configAwsCog['app_client_id']);
        $this->clientCog->setAppClientSecret($configAwsCog['app_client_secret']);
        $this->clientCog->setRegion($configAwsCog['region']);
        $this->clientCog->setUserPoolId($configAwsCog['user_pool_id']);
    }

    public function CekDuplikatEmail($Email, $PersonID) {
        $sql = "SELECT
                    a.`StaffID`
                FROM
                    ktv_staffs a
                WHERE 1=1
                    AND a.`OfficialEmail` = ?
                    AND a.PersonID != $PersonID
                LIMIT 1";
        $Data = $this->db->query($sql, array($Email))->row_array();
        if (isset($Data['StaffID'])) {
            return true;
        } else {
            return false;
        }
    }

    public function CekDuplikatNoHp($Telp, $PersonID) {
        $sql = "SELECT
                    a.`StaffID`
                FROM
                    ktv_staffs a
                WHERE 1=1
                    AND a.`OfficialPhone` = ?
                    AND a.PersonID != $PersonID
                LIMIT 1";
        $Data = $this->db->query($sql, array($Telp))->row_array();
        if (isset($Data['StaffID'])) {
            return true;
        } else {
            return false;
        }
    }

    public function CekDuplikatUsername($Username, $UserId) {
        $sql = "SELECT
                    a.`UserId`
                FROM
                    sys_user a
                WHERE 1=1
                    AND a.`UserName` = ?
                    AND a.UserId != $UserId
                LIMIT 1";
        $Data = $this->db->query($sql, array($Username))->row_array();
        if (isset($Data['UserId'])) {
            return true;
        } else {
            return false;
        }
    }

    public function CheckLinkedCognitoOnly($Email) {
        $return = array();
        $Filter = 'email = "'.$Email.'"';
        $proses = $this->clientCog->listUsersWithFilter($Filter);
        if(isset($proses['Users'][0]['Username'])) {
            //Kalau sudah ada
            $return['statusLinked'] = 'Yes';
        } else {
            $return['statusLinked'] = 'No';
        }
        $return['success'] = true;
        return $return;
    }
    
    public function ImportPrepDataProcess($dataInsert) {
        $return = array();
        $this->db->trans_begin();

        for ($i=0; $i < count($dataInsert); $i++) {
            $sql = "INSERT INTO `tmp_migcog_proses` SET
                    `UserIdExcel` = ?,
                    `NameExcel` = ?,
                    `EmailExcel` = ?,
                    `PhonenumberExcel` = ?,
                    PredefPassExcel = ?,
                    UpdateDhis = ?
                    ";
            $p = array(
                trim($dataInsert[$i]['UserID']),
                trim($dataInsert[$i]['Name']),
                trim($dataInsert[$i]['Email']),
                trim($dataInsert[$i]['Phonenumber']),
                $dataInsert[$i]['PredefPass'],
                $dataInsert[$i]['UpdateDhis']
            );
            $query = $this->db->query($sql,$p);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $return['success'] = false;
            $return['message'] = lang("Failed to process data");
        } else {
            $this->db->trans_commit();
            $return['success'] = true;
            $return['message'] = lang("Process Success");
        }
        return $return;
    }

    public function MakeStatusActiveAndGenPhoneNumber() {
        $return = array();
        $this->db->trans_begin();


        $sql = "SET SESSION group_concat_max_len = 1000000";
        $this->db->query($sql);
        
        $sql = "SELECT
                    GROUP_CONCAT(a.UserIdExcel) AS UserIdConcat
                FROM
                    tmp_migcog_proses a";
        $DataList = $this->db->query($sql)->row_array();

        $sql = "UPDATE sys_user a
                    INNER JOIN ktv_persons b ON a.`UserId` = b.`UserID`
                    INNER JOIN ktv_staffs c ON b.`PersonID` = c.`PersonID`
                SET
                    a.StatusCode = 'active'
                    , a.`UserActive` = 'Yes'
                    , a.UserUpdateUserId = 4454
                    , a.UserUpdateTime = NOW()

                    , b.`StatusCd` = 'active'
                    , b.`OfficialCellPhone` = CONCAT('+62',a.`UserId`,b.`PersonID`,c.`StaffID`)
                    , b.DateUpdated = NOW()
                    , b.UpdatedBy = 4454

                    , c.`StatusCode` = 'active'
                    , c.`OfficialPhone` = CONCAT('+62',a.`UserId`,b.`PersonID`,c.`StaffID`)
                    , c.`WorkPhone` = CONCAT('+62',a.`UserId`,b.`PersonID`,c.`StaffID`)
                    , c.`DateUpdated` = NOW()
                    , c.LastModifiedBy = 4454
                WHERE 1=1
                    AND a.`UserId` IN ({$DataList['UserIdConcat']})
                ";
        $query = $this->db->query($sql);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $return['success'] = false;
            $return['message'] = lang("Failed to process data");
        } else {
            $this->db->trans_commit();
            $return['success'] = true;
            $return['message'] = lang("Process Success");
        }
        return $return;
    }

    public function SamakanBasicData() {
        $return = array();
        $this->db->trans_begin();

        $sql = "SELECT
                    a.`UserIdExcel`
                    , a.`NameExcel`
                    , a.`EmailExcel`
                    , a.PhonenumberExcel
                    , b.`PersonID`
                    , c.`StaffID`
                FROM
                    tmp_migcog_proses a
                    INNER JOIN ktv_persons b ON a.`UserIdExcel` = b.`UserID`
                    INNER JOIN ktv_staffs c ON b.`PersonID` = c.`PersonID`
                WHERE 1=1
                ORDER BY a.`UserIdExcel`";
        $DataList = $this->db->query($sql)->result_array();

        for ($i=0; $i < count($DataList); $i++) {
            //sys_user
            $sql = "UPDATE sys_user a SET
                        a.`UserRealName` = ?
                        , a.`UserEmail` = ?
                    WHERE
                        a.`UserId` = ?
                    LIMIT 1";
            $p = array(
                $DataList[$i]['NameExcel'],
                $DataList[$i]['EmailExcel'],
                $DataList[$i]['UserIdExcel'],
            );
            $query = $this->db->query($sql,$p);

            //ktv_persons
            $sql = "UPDATE ktv_persons a SET
                        a.`PersonNm` = ?
                        , a.`Email` = ?
                        , a.`OfficialEmail` = ?
                        , a.OfficialCellPhone = ?
                    WHERE
                        a.`PersonID` = ?
                    LIMIT 1";
            $p = array(
                $DataList[$i]['NameExcel'],
                $DataList[$i]['EmailExcel'],
                $DataList[$i]['EmailExcel'],
                $DataList[$i]['PhonenumberExcel'],
                $DataList[$i]['PersonID']
            );
            $query = $this->db->query($sql,$p);

            //ktv_staffs
            $sql = "UPDATE ktv_staffs a SET
                        a.`OfficialEmail` = ?
                        , a.OfficialPhone = ?
                        , a.WorkPhone = ?
                    WHERE
                        a.`StaffID` = ?
                    LIMIT 1";
            $p = array(
                $DataList[$i]['EmailExcel'],
                $DataList[$i]['PhonenumberExcel'],
                $DataList[$i]['PhonenumberExcel'],
                $DataList[$i]['StaffID']
            );
            $query = $this->db->query($sql,$p);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $return['success'] = false;
            $return['message'] = lang("Failed to process data");
        } else {
            $this->db->trans_commit();
            $return['success'] = true;
            $return['message'] = lang("Process Success");
        }
        return $return;
    }

    public function CleanStatusNotActive() {
        $return = array();
        $this->db->trans_begin();

        $UserIdPengecualian = "1,2,4454";

        $sql = "SET SESSION group_concat_max_len = 1000000";
        $this->db->query($sql);

        $sql = "SELECT
                    GROUP_CONCAT(a.UserIdExcel) AS UserIdConcat
                FROM
                    tmp_migcog_proses a";
        $DataList = $this->db->query($sql)->row_array();
        $UserIdListExcel = $DataList['UserIdConcat'];

        // echo "<pre>";
        // print_r($UserIdListExcel);
        // die;

        //Tabel sys_user
        $sql = "UPDATE sys_user a SET
                    a.UserActive = 'No'
                    , a.StatusCode = 'nullified'
                    , a.UserUpdateUserId = 1
                    , a.UserUpdateTime = NOW()
                    , a.`UserEmail` = CONCAT(a.`UserEmail`,'-notactive')
                    , a.UserRealName = CONCAT(a.UserRealName,'-notactive')
                WHERE 1=1
                    AND a.`UserId` NOT IN ($UserIdPengecualian)
                    AND a.`UserId` NOT IN ($UserIdListExcel)";
        $query = $this->db->query($sql);
        $return['sys_user'] = $this->db->affected_rows();

        //Tabel ktv_person, ktv_staffs
        $sql = "UPDATE ktv_persons a
                    JOIN ktv_staffs b ON a.`PersonID` = b.`PersonID`
                SET
                    a.StatusCd = 'nullified'
                    , a.`PersonNm` = CONCAT(a.`PersonNm`,'-notactive')
                    , a.`Email` = CONCAT(a.`Email`,'-notactive')
                    , a.`OfficialEmail` = CONCAT(a.`OfficialEmail`,'-notactive')
                    , a.`OfficialCellPhone` = CONCAT(a.`OfficialCellPhone`,'-notactive')
                    , a.DateUpdated = NOW()
                    , a.UpdatedBy = 1

                    , b.`StatusCode` = 'nullified'
                    , b.`OfficialPhone` = CONCAT(b.`OfficialPhone`,'-notactive')
                    , b.`WorkPhone` = CONCAT(b.`WorkPhone`,'-notactive')
                    , b.`OfficialEmail` = CONCAT(b.`OfficialEmail`,'-notactive')
                    , b.`DateUpdated` = NOW()
                    , b.LastModifiedBy = 1
                WHERE 1=1
                    AND a.`UserID` NOT IN ($UserIdPengecualian)
                    AND a.`UserID` NOT IN ($UserIdListExcel)
                    AND a.`UserID` > 0
                ";
        $query = $this->db->query($sql);
        $return['person_staff'] = $this->db->affected_rows();

        //Tabel ktv_person saja
        $sql = "UPDATE ktv_persons a SET
                    a.StatusCd = 'nullified'
                    , a.`PersonNm` = CONCAT(a.`PersonNm`,'-notactive')
                    , a.`Email` = CONCAT(a.`Email`,'-notactive')
                    , a.`OfficialEmail` = CONCAT(a.`OfficialEmail`,'-notactive')
                    , a.`OfficialCellPhone` = CONCAT(a.`OfficialCellPhone`,'-notactive')
                    , a.DateUpdated = NOW()
                    , a.UpdatedBy = 1
                WHERE 1=1
                    AND a.`UserID` NOT IN ($UserIdPengecualian)
                    AND a.`UserID` NOT IN ($UserIdListExcel)
                    AND a.`UserID` > 0";
        $query = $this->db->query($sql);
        $return['person_saja'] = $this->db->affected_rows();

        $sql = "UPDATE ktv_persons a
                    INNER JOIN ktv_staffs b ON a.`PersonID` = b.`PersonID`
                SET
                    a.StatusCd = 'nullified'
                    , a.`PersonNm` = CONCAT(a.`PersonNm`,'-notactive')
                    , a.`Email` = CONCAT(a.`Email`,'-notactive')
                    , a.`OfficialEmail` = CONCAT(a.`OfficialEmail`,'-notactive')
                    , a.`OfficialCellPhone` = CONCAT(a.`OfficialCellPhone`,'-notactive')
                    , a.DateUpdated = NOW()
                    , a.UpdatedBy = 1

                    , b.`StatusCode` = 'nullified'
                    , b.`OfficialPhone` = CONCAT(b.`OfficialPhone`,'-notactive')
                    , b.`WorkPhone` = CONCAT(b.`WorkPhone`,'-notactive')
                    , b.`OfficialEmail` = CONCAT(b.`OfficialEmail`,'-notactive')
                    , b.`DateUpdated` = NOW()
                    , b.LastModifiedBy = 1
                WHERE 1=1
                    AND a.`StatusCd` = 'active'
                    AND b.`StatusCode` <> 'active'";
        $query = $this->db->query($sql);
        $return['person_staff_statuscode_not_active'] = $this->db->affected_rows();

        $sql = "UPDATE ktv_persons a
                    INNER JOIN ktv_staffs b ON a.`PersonID` = b.`PersonID`
                SET
                    a.StatusCd = 'nullified'
                    , a.`PersonNm` = CONCAT(a.`PersonNm`,'-notactive')
                    , a.`Email` = CONCAT(a.`Email`,'-notactive')
                    , a.`OfficialEmail` = CONCAT(a.`OfficialEmail`,'-notactive')
                    , a.`OfficialCellPhone` = CONCAT(a.`OfficialCellPhone`,'-notactive')
                    , a.DateUpdated = NOW()
                    , a.UpdatedBy = 1

                    , b.`StatusCode` = 'nullified'
                    , b.`OfficialPhone` = CONCAT(b.`OfficialPhone`,'-notactive')
                    , b.`WorkPhone` = CONCAT(b.`WorkPhone`,'-notactive')
                    , b.`OfficialEmail` = CONCAT(b.`OfficialEmail`,'-notactive')
                    , b.`DateUpdated` = NOW()
                    , b.LastModifiedBy = 1
                WHERE 1=1
                    AND a.`StatusCd` <> 'active'
	                AND b.`StatusCode` = 'active'";
        $query = $this->db->query($sql);
        $return['person_staff_statuscode_not_active_2'] = $this->db->affected_rows();

        $sql = "UPDATE ktv_persons a SET
                    a.StatusCd = 'nullified'
                    , a.`PersonNm` = CONCAT(a.`PersonNm`,'-notactive')
                    , a.`Email` = CONCAT(a.`Email`,'-notactive')
                    , a.`OfficialEmail` = CONCAT(a.`OfficialEmail`,'-notactive')
                    , a.`OfficialCellPhone` = CONCAT(a.`OfficialCellPhone`,'-notactive')
                    , a.DateUpdated = NOW()
                    , a.UpdatedBy = 1
                WHERE 1=1
                    AND a.`StatusCd` = ''";
        $query = $this->db->query($sql);
        $return['person_staff_statuscode_not_active_3'] = $this->db->affected_rows();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $return['success'] = false;
            $return['message'] = lang("Failed to process data");
        } else {
            $this->db->trans_commit();
            $return['success'] = true;
            $return['message'] = lang("Process Success");
        }
        return $return;
    }

    public function MigCogUpdatePassDhis() {
        $PredefinedPass = 'KtvMd2020!';

        $UserIdProses = "5338,5339,5343,5346,5347,5348,5349,5350,5351,5355,5356,5359,5364,5367,5371,5372,5219,5221,5224,5229,5230,5231,5232,5233,5236,5255,5256,5259,5262,5263,5265,5267,5276,5301,5302,5303,5304,5305,5307,5308,5309,5312,5313,5319,5320,5321,5322,5323,5328,5331";
        $ArrUserIdProses = explode(",",$UserIdProses);

        for ($i=0; $i < count($ArrUserIdProses); $i++) {
            $StatusProses = 'Success';
            $UserIdProses = $ArrUserIdProses[$i];

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
            $DataUser = $this->db->query($sql,array($UserIdProses))->row_array();

            $tmpName   = explode(" ", $DataUser['Name']);
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
            $query = $this->db->query($sql,array($UserIdProses));
            $dataOrgUnit = $query->result_array();
            $tmpJson = array();

            foreach ($dataOrgUnit as $key => $value) {
                if($value['uid'] != ""){
                    $tmpJson[]['id'] = $value['uid'];
                }
            }
            $jsonOrgUnit = json_encode($tmpJson);

            $bodyJson = '{
                "firstName": "'.$firstName.'",
                "surname": "'.$lastName.'",
                "userCredentials": {
                    "username": "'.$DataUser['UserName'].'",
                    "password": "'.$PredefinedPass.'",
                "userRoles": [ {
                    "id": "'.$DataUser['UserExtRoleId'].'"
                } ]
                },
                "organisationUnits": '.$jsonOrgUnit.',
                "userGroups": [ {
                    "id": "'.$DataUser['UserExtGroupId'].'"
                } ]
            }';

            //User Group DHIS ============================= (Begin)
            if($DataUser['UserExtGroupId'] != ""){
                $AppGroupUidRaw = $DataUser['UserExtGroupId'];

                $TmpAppGroupUid = explode(',',$DataUser['UserExtGroupId']);
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
            if($DataUser['UserExtRoleId'] != ""){
                $AppRoleUidRaw = $DataUser['UserExtRoleId'];

                $TmpAppRoleUid = explode(',',$DataUser['UserExtRoleId']);
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

            //Cek CREATE/UPDATE ================================================================
            if($DataUser['UserExtId'] == "") {
                $StatusProses = 'Failed, User not registered on DHIS';
            } else {

                $url = $this->config->item('dhis_url').'api/users/'.$DataUser['UserExtId'];
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
                    $MessageProses = 'Update Password DHIS berhasil';
                } else {
                    $StatusProses = 'Failed';
                    $MessageProses = 'Update Password DHIS gagal';
                }
            }

            $sql = "UPDATE `tmp_migcog_proses` SET
                        `SummaryDhis` = ?,
                        `TimestampDhis` = NOW()
                    WHERE
                        `UserIdExcel` = ?
                    LIMIT 1";
            $p = array(
                $StatusProses,
                $UserIdProses
            );
            $query = $this->db->query($sql,$p);
        }

        $return['success'] = true;
        return $return;
    }

    public function MigCogStaffKoltiva($userid) {
        $UserIdProses = $userid;
        $ArrUserIdProses = explode(",",$UserIdProses);
        $UrlProses = 'migrasi_cognito_staff_koltiva';

        for ($i=0; $i < count($ArrUserIdProses); $i++) {
            $UserIdProses = $ArrUserIdProses[$i];
            $PersonIDProses = null;
            $StaffIDProses = null;
            $PreProsesName = null;
            $PreProsesUsername = null;
            $PreProsesEmail = null;
            $PreProsesPhoneNumber = null;
            $PascaProsesName = null;
            $PascaProsesUsername = null;
            $PascaProsesEmail = null;
            $PascaProsesPhoneNumber = null;
            $StatusTransaction = 'Success';
            $Summary = '';

            //Ambil data staff nya
            $sql = "SELECT
                        a.`UserId`
                        , a.`UserName` AS `Username`
                        , a.`UserRealName` AS `Name`
                        , a.`UserEmail` AS `Email`
                        , b.OfficialCellPhone AS `Phone`
                        , b.Gender
                        , b.PersonID
                        , c.StaffID
                        , a.UserInCognito
                    FROM
                        sys_user a
                        INNER JOIN ktv_persons b ON a.`UserId` = b.`UserID`
                        INNER JOIN ktv_staffs c ON b.`PersonID` = c.`PersonID`
                    WHERE 1=1
                        AND a.`UserId` = ?
                    LIMIT 1";
            $p = array(
                $UserIdProses
            );
            $DataStaffUser = $this->db->query($sql,$p)->row_array();
            if($DataStaffUser['UserId'] != "" && $DataStaffUser['UserInCognito'] == 'No') {
                $PersonIDProses = $DataStaffUser['PersonID'];
                $StaffIDProses = $DataStaffUser['StaffID'];
                $PreProsesName = $DataStaffUser['Name'];
                $PreProsesUsername = $DataStaffUser['Username'];
                $PreProsesEmail = $DataStaffUser['Email'];
                $PreProsesPhoneNumber = $DataStaffUser['Phone'];

                //1. Cek alamat email apakah valid
                if (!filter_var($PreProsesEmail, FILTER_VALIDATE_EMAIL)) {
                    $StatusTransaction = 'Failed';
                    $Summary = $Summary."Alamat Email $PreProsesEmail tidak valid\n";
                }

                //2.Cek apakah alamat email staff ini ada duplikat dengan staff lain
                $CekEmail = $this->CekDuplikatEmail($PreProsesEmail,$PersonIDProses);
                if($CekEmail == true) {
                    $StatusTransaction = 'Failed';
                    $Summary = $Summary."Alamat Email $PreProsesEmail duplikat dengan staff lain\n";
                }

                //3. Cek No Hp Duplikat
                $CekNoHp = $this->CekDuplikatNoHp($PreProsesPhoneNumber,$PersonIDProses);
                if($CekNoHp == true) {
                    $StatusTransaction = 'Failed';
                    $Summary = $Summary."No telepon $PreProsesPhoneNumber duplikat dengan staff lain\n";
                }

                //4. Cek username apakah alamat email
                /*if (filter_var($PreProsesUsername, FILTER_VALIDATE_EMAIL)) {
                    $StatusTransaction = 'Failed';
                    $Summary = $Summary."Username $PreProsesUsername tidak boleh berupa alamat email\n";
                }*/

                //Cek data valid, jika valid lanjut lagi
                if($StatusTransaction == 'Success') {
                    $ResCekEmailCog = $this->CheckLinkedCognitoOnly($PreProsesEmail);

                    if($ResCekEmailCog['statusLinked'] == 'Yes') { //Sudah ada dicognito, jadi mode LINK
                        //Ambil data basic attribute di cognito
                        $Filter = 'email = "'.$PreProsesEmail.'"';
                        $DataCog = $this->clientCog->listUsersWithFilter($Filter);
                        if(isset($DataCog['@metadata']['statusCode'])) {
                            $DataUserCog = $DataCog['Users'][0];
                            $UserAttributes = mappingArrayCognitoAttributes($DataUserCog['Attributes']);
                            $StatusValidDataLink = true;

                            //Cek No Hp Duplikat
                            $CekNoHp = $this->CekDuplikatNoHp($UserAttributes['phone_number'],$PersonIDProses);
                            if($CekNoHp == true) {
                                $StatusValidDataLink = false;
                                $Summary = $Summary."No telepon dari cognito {$UserAttributes['phone_number']} duplikat dengan staff lain\n";
                            }

                            //Cek duplikat username
                            $CekDuplikatUsername = $this->CekDuplikatUsername($DataUserCog['Username'],$UserIdProses);
                            if($CekDuplikatUsername == true) {
                                $StatusValidDataLink = false;
                                $Summary = $Summary."Username dari cognito {$DataUserCog['Username']} duplikat dengan user lain\n";
                            }

                            if($StatusValidDataLink == true) {
                                //Mulai Update proses LINK =============================== (Begin)

                                $sql = "UPDATE ktv_persons a SET
                                            a.`PersonNm` = ?,
                                            a.`Gender` = ?,
                                            a.`OfficialCellPhone` = ?,
                                            a.`OfficialEmail` = ?,
                                            a.`Email` = ?
                                        WHERE
                                            a.`PersonID` = ?
                                        LIMIT 1";
                                $p = array(
                                    $UserAttributes['name'],
                                    $UserAttributes['gender'],
                                    $UserAttributes['phone_number'],
                                    $PreProsesEmail,
                                    $PreProsesEmail,
                                    $PersonIDProses
                                );
                                $query = $this->db->query($sql,$p);

                                $sql = "UPDATE ktv_staffs a SET
                                            a.`OfficialPhone` = ?,
                                            a.`WorkPhone` = ?,
                                            a.`OfficialEmail` = ?
                                        WHERE
                                            a.`PersonID` = ?
                                        LIMIT 1";
                                $p = array(
                                    $UserAttributes['phone_number'],
                                    $UserAttributes['phone_number'],
                                    $PreProsesEmail,
                                    $PersonIDProses
                                );
                                $query = $this->db->query($sql,$p);

                                $sql = "UPDATE sys_user a SET
                                            a.`UserRealName` = ?,
                                            a.`UserName` = ?,
                                            a.`UserEmail` = ?,
                                            a.UserInCognito = 'Yes',
                                            a.CognitoUserSub = ?,
                                            a.CognitoUserStatus = ?
                                        WHERE
                                            a.`UserId` = ?
                                        LIMIT 1";
                                $p = array(
                                    $UserAttributes['name'],
                                    $DataUserCog['Username'],
                                    $PreProsesEmail,
                                    $UserAttributes['sub'],
                                    $DataUserCog['UserStatus'],
                                    $UserIdProses
                                );
                                $query = $this->db->query($sql,$p);

                                //Update attribute status custom flag ke AWS
                                $prosesCog = $this->clientCog->updateUserAttributes($DataUserCog['Username'], [
                                    'custom:ispalmoiltrace' => '1'
                                ]);

                                //Update variable log
                                $PascaProsesName = $UserAttributes['name'];
                                $PascaProsesUsername = $DataUserCog['Username'];
                                $PascaProsesEmail = $PreProsesEmail;
                                $PascaProsesPhoneNumber = $UserAttributes['phone_number'];

                                //Mulai Update proses LINK =============================== (End)
                            } else {
                                $StatusTransaction = 'Failed';
                            }

                        } else {
                            $StatusTransaction = 'Failed';
                            $Summary = $Summary."Ambil data basic attribute di cognito gagal\n";
                        }
                    }

                    if($ResCekEmailCog['statusLinked'] == 'No') { //Belum ada dicognito, jadi mode CREATE
                        $PascaProsesUsername = $PreProsesUsername;
                        $PascaProsesEmail = $PreProsesEmail;
                        $PascaProsesName = $PreProsesName;
                        $PasswordProses = passwordGenerator(8);
                        $PascaProsesPhoneNumber = $PreProsesPhoneNumber;
                        $StatusValidDataCreate = true;

                        //Cek username, jika email, maka ganti jadi non email
                        if (filter_var($PreProsesUsername, FILTER_VALIDATE_EMAIL)) {
                            $PascaProsesUsername = usernameOfEmail($PreProsesUsername);
                        }

                        //Cek username duplikat
                        $CekDuplikatUsername = $this->CekDuplikatUsername($PascaProsesUsername,$UserIdProses);
                        if($CekDuplikatUsername == true) {
                            $StatusValidDataCreate = false;
                            $Summary = $Summary."Username $PascaProsesUsername duplikat di sys_user (mode: CREATE)\n";
                        }

                        //Cek apakah phone number duplikat di cognito
                        $Filter = 'phone_number = "'.$PascaProsesPhoneNumber.'"';
                        $proses = $this->clientCog->listUsersWithFilter($Filter);
                        if(isset($proses['Users'][0]['Username'])) {
                            $StatusValidDataCreate = false;
                            $Summary = $Summary."Phone number $PascaProsesPhoneNumber duplikat di cognito (mode: CREATE)\n";
                        }

                        if($StatusValidDataCreate == true) {
                            //Mulai Update proses CREATE =============================== (Begin)

                            //Create User
                            $MessageAction = "";
                            $createUserCog = $this->clientCog->adminRegisterUser($PascaProsesUsername, $PasswordProses, $MessageAction, [
                                'email' => $PascaProsesEmail,
                                'email_verified' => 'true',
                                'gender' => $DataStaffUser['Gender'],
                                'name' => $PascaProsesName,
                                'phone_number' => $PascaProsesPhoneNumber,
                                'phone_number_verified' => 'true',
                                'custom:ispalmoiltrace' => '1'
                            ]);

                            if(isset($createUserCog['User']['Username'])) {
                                $UserAttributes = mappingArrayCognitoAttributes($createUserCog['User']['Attributes']);
                                $AwsUserSub = $UserAttributes['sub'];

                                //update user
                                $sql = "UPDATE sys_user a SET
                                            a.`UserName` = ?,
                                            a.UserInCognito = 'Yes',
                                            a.CognitoUserSub = ?,
                                            a.CognitoUserStatus = ?
                                        WHERE
                                            a.UserId = ?
                                        LIMIT 1";
                                $p = array(
                                    $PascaProsesUsername,
                                    $AwsUserSub,
                                    'FORCE_CHANGE_PASSWORD',
                                    $UserIdProses
                                );
                                $query = $this->db->query($sql,$p);
                                //Sampai sini selesai, proses berakhir

                            } else {
                                $StatusTransaction = 'Failed';
                                $Summary = $Summary."Failed to create user on identity server".', '.decodeMsgAws($createUserCog)." (mode: CREATE)\n";
                            }

                            //Mulai Update proses CREATE =============================== (End)
                        } else {
                            $StatusTransaction = 'Failed';
                        }
                    }
                }

            } else {
                $StatusTransaction = 'Failed';
                $Summary = "Data User tidak lengkap di tabel sys_user, ktv_persons, ktv_staffs / User sudah terdaftar di cognito\n";
            }

            //Insert log summary (Begin)
            $sql = "UPDATE `tmp_migcog_proses` SET
                    `UserIDProses` = ?,
                    `PersonIDProses` = ?,
                    `StaffIDProses` = ?,
                    `PreProsesName` = ?,
                    `PreProsesUsername` = ?,
                    `PreProsesEmail` = ?,
                    `PreProsesPhoneNumber` = ?,
                    `UrlProses` = ?,
                    `PascaProsesName` = ?,
                    `PascaProsesUsername` = ?,
                    `PascaProsesEmail` = ?,
                    `PascaProsesPhoneNumber` = ?,
                    `StatusTransaction` = ?,
                    `Summary` = ?,
                    Timestamp = NOW()
                    WHERE
                        UserIdExcel = ?
                    LIMIT 1";
            $p = array(
                $UserIdProses,
                $PersonIDProses,
                $StaffIDProses,
                $PreProsesName,
                $PreProsesUsername,
                $PreProsesEmail,
                $PreProsesPhoneNumber,
                $UrlProses,
                $PascaProsesName,
                $PascaProsesUsername,
                $PascaProsesEmail,
                $PascaProsesPhoneNumber,
                $StatusTransaction,
                $Summary,
                $UserIdProses
            );
            $query = $this->db->query($sql,$p);
            //Insert log summary (End)
        }

        $return['success'] = true;
        return $return;
    }

    public function MigCogNoemailnfixed($userid) {
        $UserIdProses = $userid;
        $ArrUserIdProses = explode(",",$UserIdProses);
        $UrlProses = 'migrasi_cognito_noemailnfixed';
        $PredefinedPassword = 'KtvSso@2020!';

        for ($i=0; $i < count($ArrUserIdProses); $i++) {
            $UserIdProses = $ArrUserIdProses[$i];
            $PersonIDProses = null;
            $StaffIDProses = null;
            $PreProsesName = null;
            $PreProsesUsername = null;
            $PreProsesEmail = null;
            $PreProsesPhoneNumber = null;
            $PascaProsesName = null;
            $PascaProsesUsername = null;
            $PascaProsesEmail = null;
            $PascaProsesPhoneNumber = null;
            $StatusTransaction = 'Success';
            $Summary = '';

            //Ambil data staff nya
            $sql = "SELECT
                        a.`UserId`
                        , a.`UserName` AS `Username`
                        , a.`UserRealName` AS `Name`
                        , a.`UserEmail` AS `Email`
                        , b.OfficialCellPhone AS `Phone`
                        , b.Gender
                        , b.PersonID
                        , c.StaffID
                        , a.UserInCognito
                    FROM
                        sys_user a
                        INNER JOIN ktv_persons b ON a.`UserId` = b.`UserID`
                        INNER JOIN ktv_staffs c ON b.`PersonID` = c.`PersonID`
                    WHERE 1=1
                        AND a.`UserId` = ?
                    LIMIT 1";
            $p = array(
                $UserIdProses
            );
            $DataStaffUser = $this->db->query($sql,$p)->row_array();
            if($DataStaffUser['UserId'] != "" && $DataStaffUser['UserInCognito'] == 'No') {
                $PersonIDProses = $DataStaffUser['PersonID'];
                $StaffIDProses = $DataStaffUser['StaffID'];
                $PreProsesName = $DataStaffUser['Name'];
                $PreProsesUsername = $DataStaffUser['Username'];
                $PreProsesEmail = $DataStaffUser['Email'];
                $PreProsesPhoneNumber = $DataStaffUser['Phone'];

                //1. Cek alamat email apakah valid
                if (!filter_var($PreProsesEmail, FILTER_VALIDATE_EMAIL)) {
                    $StatusTransaction = 'Failed';
                    $Summary = $Summary."Alamat Email $PreProsesEmail tidak valid\n";
                }

                //2.Cek apakah alamat email staff ini ada duplikat dengan staff lain
                $CekEmail = $this->CekDuplikatEmail($PreProsesEmail,$PersonIDProses);
                if($CekEmail == true) {
                    $StatusTransaction = 'Failed';
                    $Summary = $Summary."Alamat Email $PreProsesEmail duplikat dengan staff lain\n";
                }

                //3. Cek No Hp Duplikat
                $CekNoHp = $this->CekDuplikatNoHp($PreProsesPhoneNumber,$PersonIDProses);
                if($CekNoHp == true) {
                    $StatusTransaction = 'Failed';
                    $Summary = $Summary."No telepon $PreProsesPhoneNumber duplikat dengan staff lain\n";
                }

                //4. Cek username apakah alamat email
                /*if (filter_var($PreProsesUsername, FILTER_VALIDATE_EMAIL)) {
                    $StatusTransaction = 'Failed';
                    $Summary = $Summary."Username $PreProsesUsername tidak boleh berupa alamat email\n";
                }*/

                //Cek data valid, jika valid lanjut lagi
                if($StatusTransaction == 'Success') {
                    $ResCekEmailCog = $this->CheckLinkedCognitoOnly($PreProsesEmail);

                    if($ResCekEmailCog['statusLinked'] == 'Yes') { //Sudah ada dicognito, jadi mode LINK
                        //Ambil data basic attribute di cognito
                        $Filter = 'email = "'.$PreProsesEmail.'"';
                        $DataCog = $this->clientCog->listUsersWithFilter($Filter);
                        if(isset($DataCog['@metadata']['statusCode'])) {
                            $DataUserCog = $DataCog['Users'][0];
                            $UserAttributes = mappingArrayCognitoAttributes($DataUserCog['Attributes']);
                            $StatusValidDataLink = true;

                            //Cek No Hp Duplikat
                            $CekNoHp = $this->CekDuplikatNoHp($UserAttributes['phone_number'],$PersonIDProses);
                            if($CekNoHp == true) {
                                $StatusValidDataLink = false;
                                $Summary = $Summary."No telepon dari cognito {$UserAttributes['phone_number']} duplikat dengan staff lain\n";
                            }

                            //Cek duplikat username
                            $CekDuplikatUsername = $this->CekDuplikatUsername($DataUserCog['Username'],$UserIdProses);
                            if($CekDuplikatUsername == true) {
                                $StatusValidDataLink = false;
                                $Summary = $Summary."Username dari cognito {$DataUserCog['Username']} duplikat dengan user lain\n";
                            }

                            if($StatusValidDataLink == true) {
                                //Mulai Update proses LINK =============================== (Begin)
                                $sql = "UPDATE ktv_persons a SET
                                            a.`PersonNm` = ?,
                                            a.`Gender` = ?,
                                            a.`OfficialCellPhone` = ?,
                                            a.`OfficialEmail` = ?,
                                            a.`Email` = ?
                                        WHERE
                                            a.`PersonID` = ?
                                        LIMIT 1";
                                $p = array(
                                    $UserAttributes['name'],
                                    $UserAttributes['gender'],
                                    $UserAttributes['phone_number'],
                                    $PreProsesEmail,
                                    $PreProsesEmail,
                                    $PersonIDProses
                                );
                                $query = $this->db->query($sql,$p);

                                $sql = "UPDATE ktv_staffs a SET
                                            a.`OfficialPhone` = ?,
                                            a.`WorkPhone` = ?,
                                            a.`OfficialEmail` = ?
                                        WHERE
                                            a.`PersonID` = ?
                                        LIMIT 1";
                                $p = array(
                                    $UserAttributes['phone_number'],
                                    $UserAttributes['phone_number'],
                                    $PreProsesEmail,
                                    $PersonIDProses
                                );
                                $query = $this->db->query($sql,$p);

                                $sql = "UPDATE sys_user a SET
                                            a.`UserRealName` = ?,
                                            a.`UserName` = ?,
                                            a.`UserEmail` = ?,
                                            a.UserInCognito = 'Yes',
                                            a.CognitoUserSub = ?,
                                            a.CognitoUserStatus = ?
                                        WHERE
                                            a.`UserId` = ?
                                        LIMIT 1";
                                $p = array(
                                    $UserAttributes['name'],
                                    $DataUserCog['Username'],
                                    $PreProsesEmail,
                                    $UserAttributes['sub'],
                                    $DataUserCog['UserStatus'],
                                    $UserIdProses
                                );
                                $query = $this->db->query($sql,$p);

                                //Update attribute status custom flag ke AWS
                                $prosesCog = $this->clientCog->updateUserAttributes($DataUserCog['Username'], [
                                    'custom:ispalmoiltrace' => '1'
                                ]);

                                //Update variable log
                                $PascaProsesName = $UserAttributes['name'];
                                $PascaProsesUsername = $DataUserCog['Username'];
                                $PascaProsesEmail = $PreProsesEmail;
                                $PascaProsesPhoneNumber = $UserAttributes['phone_number'];
                                //Mulai Update proses LINK =============================== (End)
                            } else {
                                $StatusTransaction = 'Failed';
                            }

                        } else {
                            $StatusTransaction = 'Failed';
                            $Summary = $Summary."Ambil data basic attribute di cognito gagal\n";
                        }
                    }

                    if($ResCekEmailCog['statusLinked'] == 'No') { //Belum ada dicognito, jadi mode CREATE
                        $PascaProsesUsername = $PreProsesUsername;
                        $PascaProsesEmail = $PreProsesEmail;
                        $PascaProsesName = $PreProsesName;
                        $PasswordProses = $PredefinedPassword;
                        $PascaProsesPhoneNumber = $PreProsesPhoneNumber;
                        $StatusValidDataCreate = true;

                        //Cek username, jika email, maka ganti jadi non email
                        if (filter_var($PreProsesUsername, FILTER_VALIDATE_EMAIL)) {
                            $PascaProsesUsername = usernameOfEmail($PreProsesUsername);
                        }

                        //Cek username duplikat
                        $CekDuplikatUsername = $this->CekDuplikatUsername($PascaProsesUsername,$UserIdProses);
                        if($CekDuplikatUsername == true) {
                            $StatusValidDataCreate = false;
                            $Summary = $Summary."Username $PascaProsesUsername duplikat di sys_user (mode: CREATE)\n";
                        }

                        //Cek apakah phone number duplikat di cognito
                        $Filter = 'phone_number = "'.$PascaProsesPhoneNumber.'"';
                        $proses = $this->clientCog->listUsersWithFilter($Filter);
                        if(isset($proses['Users'][0]['Username'])) {
                            $StatusValidDataCreate = false;
                            $Summary = $Summary."Phone number $PascaProsesPhoneNumber duplikat di cognito (mode: CREATE)\n";
                        }

                        if($StatusValidDataCreate == true) {
                            //Mulai Update proses CREATE =============================== (Begin)

                            //Create User
                            $MessageAction = "SUPPRESS"; //not send email confirmation
                            $createUserCog = $this->clientCog->adminRegisterUser($PascaProsesUsername, $PasswordProses, $MessageAction, [
                                'email' => $PascaProsesEmail,
                                'email_verified' => 'true',
                                'gender' => $DataStaffUser['Gender'],
                                'name' => $PascaProsesName,
                                'phone_number' => $PascaProsesPhoneNumber,
                                'phone_number_verified' => 'true',
                                'custom:ispalmoiltrace' => '1'
                            ]);

                            if(isset($createUserCog['User']['Username'])) {
                                //Auto confirm user
                                $response = $this->clientCog->adminSetUserPassword($PascaProsesUsername,$PasswordProses);

                                $UserAttributes = mappingArrayCognitoAttributes($createUserCog['User']['Attributes']);
                                $AwsUserSub = $UserAttributes['sub'];

                                //update user
                                $sql = "UPDATE sys_user a SET
                                            a.`UserName` = ?,
                                            a.UserInCognito = 'Yes',
                                            a.CognitoUserSub = ?,
                                            a.CognitoUserStatus = ?
                                        WHERE
                                            a.UserId = ?
                                        LIMIT 1";
                                $p = array(
                                    $PascaProsesUsername,
                                    $AwsUserSub,
                                    'CONFIRMED',
                                    $UserIdProses
                                );
                                $query = $this->db->query($sql,$p);
                                //Sampai sini selesai, proses berakhir

                            } else {
                                $StatusTransaction = 'Failed';
                                $Summary = $Summary."Failed to create user on identity server".', '.decodeMsgAws($createUserCog)." (mode: CREATE)\n";
                            }

                            //Mulai Update proses CREATE =============================== (End)
                        } else {
                            $StatusTransaction = 'Failed';
                        }
                    }

                }

            } else {
                $StatusTransaction = 'Failed';
                $Summary = "Data User tidak lengkap di tabel sys_user, ktv_persons, ktv_staffs / User sudah terdaftar di cognito\n";
            }

            //Insert log summary (Begin)
            $sql = "UPDATE `tmp_migcog_proses` SET
                    `UserIDProses` = ?,
                    `PersonIDProses` = ?,
                    `StaffIDProses` = ?,
                    `PreProsesName` = ?,
                    `PreProsesUsername` = ?,
                    `PreProsesEmail` = ?,
                    `PreProsesPhoneNumber` = ?,
                    `UrlProses` = ?,
                    `PascaProsesName` = ?,
                    `PascaProsesUsername` = ?,
                    `PascaProsesEmail` = ?,
                    `PascaProsesPhoneNumber` = ?,
                    `StatusTransaction` = ?,
                    `Summary` = ?,
                    Timestamp = NOW()
                    WHERE
                        UserIdExcel = ?
                    LIMIT 1";
            $p = array(
                $UserIdProses,
                $PersonIDProses,
                $StaffIDProses,
                $PreProsesName,
                $PreProsesUsername,
                $PreProsesEmail,
                $PreProsesPhoneNumber,
                $UrlProses,
                $PascaProsesName,
                $PascaProsesUsername,
                $PascaProsesEmail,
                $PascaProsesPhoneNumber,
                $StatusTransaction,
                $Summary,
                $UserIdProses
            );
            $query = $this->db->query($sql,$p);
            //Insert log summary (End)
        }

        $return['success'] = true;
        return $return;
    }

    public function MigCogEmailconfnrandom($userid) {
        $UserIdProses = $userid;
        $ArrUserIdProses = explode(",",$UserIdProses);
        $UrlProses = 'migrasi_cognito_emailconfnrandom';

        for ($i=0; $i < count($ArrUserIdProses); $i++) {
            $UserIdProses = $ArrUserIdProses[$i];
            $PersonIDProses = null;
            $StaffIDProses = null;
            $PreProsesName = null;
            $PreProsesUsername = null;
            $PreProsesEmail = null;
            $PreProsesPhoneNumber = null;
            $PascaProsesName = null;
            $PascaProsesUsername = null;
            $PascaProsesEmail = null;
            $PascaProsesPhoneNumber = null;
            $StatusTransaction = 'Success';
            $Summary = '';

            //Ambil data staff nya
            $sql = "SELECT
                        a.`UserId`
                        , a.`UserName` AS `Username`
                        , a.`UserRealName` AS `Name`
                        , a.`UserEmail` AS `Email`
                        , b.OfficialCellPhone AS `Phone`
                        , b.Gender
                        , b.PersonID
                        , c.StaffID
                        , a.UserInCognito
                    FROM
                        sys_user a
                        INNER JOIN ktv_persons b ON a.`UserId` = b.`UserID`
                        INNER JOIN ktv_staffs c ON b.`PersonID` = c.`PersonID`
                    WHERE 1=1
                        AND a.`UserId` = ?
                    LIMIT 1";
            $p = array(
                $UserIdProses
            );
            $DataStaffUser = $this->db->query($sql,$p)->row_array();
            if($DataStaffUser['UserId'] != "" && $DataStaffUser['UserInCognito'] == 'No') {
                $PersonIDProses = $DataStaffUser['PersonID'];
                $StaffIDProses = $DataStaffUser['StaffID'];
                $PreProsesName = $DataStaffUser['Name'];
                $PreProsesUsername = $DataStaffUser['Username'];
                $PreProsesEmail = $DataStaffUser['Email'];
                $PreProsesPhoneNumber = $DataStaffUser['Phone'];

                //1. Cek alamat email apakah valid
                if (!filter_var($PreProsesEmail, FILTER_VALIDATE_EMAIL)) {
                    $StatusTransaction = 'Failed';
                    $Summary = $Summary."Alamat Email $PreProsesEmail tidak valid\n";
                }

                //2.Cek apakah alamat email staff ini ada duplikat dengan staff lain
                $CekEmail = $this->CekDuplikatEmail($PreProsesEmail,$PersonIDProses);
                if($CekEmail == true) {
                    $StatusTransaction = 'Failed';
                    $Summary = $Summary."Alamat Email $PreProsesEmail duplikat dengan staff lain\n";
                }

                //3. Cek No Hp Duplikat
                $CekNoHp = $this->CekDuplikatNoHp($PreProsesPhoneNumber,$PersonIDProses);
                if($CekNoHp == true) {
                    $StatusTransaction = 'Failed';
                    $Summary = $Summary."No telepon $PreProsesPhoneNumber duplikat dengan staff lain\n";
                }

                //4. Cek username apakah alamat email
                /*if (filter_var($PreProsesUsername, FILTER_VALIDATE_EMAIL)) {
                    $StatusTransaction = 'Failed';
                    $Summary = $Summary."Username $PreProsesUsername tidak boleh berupa alamat email\n";
                }*/

                //Cek data valid, jika valid lanjut lagi
                if($StatusTransaction == 'Success') {
                    $ResCekEmailCog = $this->CheckLinkedCognitoOnly($PreProsesEmail);

                    if($ResCekEmailCog['statusLinked'] == 'Yes') { //Sudah ada dicognito, jadi mode LINK
                        //Ambil data basic attribute di cognito
                        $Filter = 'email = "'.$PreProsesEmail.'"';
                        $DataCog = $this->clientCog->listUsersWithFilter($Filter);
                        if(isset($DataCog['@metadata']['statusCode'])) {
                            $DataUserCog = $DataCog['Users'][0];
                            $UserAttributes = mappingArrayCognitoAttributes($DataUserCog['Attributes']);
                            $StatusValidDataLink = true;

                            //Cek No Hp Duplikat
                            $CekNoHp = $this->CekDuplikatNoHp($UserAttributes['phone_number'],$PersonIDProses);
                            if($CekNoHp == true) {
                                $StatusValidDataLink = false;
                                $Summary = $Summary."No telepon dari cognito {$UserAttributes['phone_number']} duplikat dengan staff lain\n";
                            }

                            //Cek duplikat username
                            $CekDuplikatUsername = $this->CekDuplikatUsername($DataUserCog['Username'],$UserIdProses);
                            if($CekDuplikatUsername == true) {
                                $StatusValidDataLink = false;
                                $Summary = $Summary."Username dari cognito {$DataUserCog['Username']} duplikat dengan user lain\n";
                            }

                            if($StatusValidDataLink == true) {
                                //Mulai Update proses LINK =============================== (Begin)

                                $sql = "UPDATE ktv_persons a SET
                                            a.`PersonNm` = ?,
                                            a.`Gender` = ?,
                                            a.`OfficialCellPhone` = ?,
                                            a.`OfficialEmail` = ?,
                                            a.`Email` = ?
                                        WHERE
                                            a.`PersonID` = ?
                                        LIMIT 1";
                                $p = array(
                                    $UserAttributes['name'],
                                    $UserAttributes['gender'],
                                    $UserAttributes['phone_number'],
                                    $PreProsesEmail,
                                    $PreProsesEmail,
                                    $PersonIDProses
                                );
                                $query = $this->db->query($sql,$p);

                                $sql = "UPDATE ktv_staffs a SET
                                            a.`OfficialPhone` = ?,
                                            a.`WorkPhone` = ?,
                                            a.`OfficialEmail` = ?
                                        WHERE
                                            a.`PersonID` = ?
                                        LIMIT 1";
                                $p = array(
                                    $UserAttributes['phone_number'],
                                    $UserAttributes['phone_number'],
                                    $PreProsesEmail,
                                    $PersonIDProses
                                );
                                $query = $this->db->query($sql,$p);

                                $sql = "UPDATE sys_user a SET
                                            a.`UserRealName` = ?,
                                            a.`UserName` = ?,
                                            a.`UserEmail` = ?,
                                            a.UserInCognito = 'Yes',
                                            a.CognitoUserSub = ?,
                                            a.CognitoUserStatus = ?
                                        WHERE
                                            a.`UserId` = ?
                                        LIMIT 1";
                                $p = array(
                                    $UserAttributes['name'],
                                    $DataUserCog['Username'],
                                    $PreProsesEmail,
                                    $UserAttributes['sub'],
                                    $DataUserCog['UserStatus'],
                                    $UserIdProses
                                );
                                $query = $this->db->query($sql,$p);

                                //Update attribute status custom flag ke AWS
                                $prosesCog = $this->clientCog->updateUserAttributes($DataUserCog['Username'], [
                                    'custom:ispalmoiltrace' => '1'
                                ]);

                                //Update variable log
                                $PascaProsesName = $UserAttributes['name'];
                                $PascaProsesUsername = $DataUserCog['Username'];
                                $PascaProsesEmail = $PreProsesEmail;
                                $PascaProsesPhoneNumber = $UserAttributes['phone_number'];

                                //Mulai Update proses LINK =============================== (End)
                            } else {
                                $StatusTransaction = 'Failed';
                            }

                        } else {
                            $StatusTransaction = 'Failed';
                            $Summary = $Summary."Ambil data basic attribute di cognito gagal\n";
                        }
                    }

                    if($ResCekEmailCog['statusLinked'] == 'No') { //Belum ada dicognito, jadi mode CREATE
                        $PascaProsesUsername = $PreProsesUsername;
                        $PascaProsesEmail = $PreProsesEmail;
                        $PascaProsesName = $PreProsesName;
                        $PasswordProses = passwordGenerator(8);
                        $PascaProsesPhoneNumber = $PreProsesPhoneNumber;
                        $StatusValidDataCreate = true;

                        //Cek username, jika email, maka ganti jadi non email
                        if (filter_var($PreProsesUsername, FILTER_VALIDATE_EMAIL)) {
                            $PascaProsesUsername = usernameOfEmail($PreProsesUsername);
                        }

                        //Cek username duplikat
                        $CekDuplikatUsername = $this->CekDuplikatUsername($PascaProsesUsername,$UserIdProses);
                        if($CekDuplikatUsername == true) {
                            $StatusValidDataCreate = false;
                            $Summary = $Summary."Username $PascaProsesUsername duplikat di sys_user (mode: CREATE)\n";
                        }

                        //Cek apakah phone number duplikat di cognito
                        $Filter = 'phone_number = "'.$PascaProsesPhoneNumber.'"';
                        $proses = $this->clientCog->listUsersWithFilter($Filter);
                        if(isset($proses['Users'][0]['Username'])) {
                            $StatusValidDataCreate = false;
                            $Summary = $Summary."Phone number $PascaProsesPhoneNumber duplikat di cognito (mode: CREATE)\n";
                        }

                        if($StatusValidDataCreate == true) {
                            //Mulai Update proses CREATE =============================== (Begin)

                            //Create User
                            $MessageAction = "";
                            $createUserCog = $this->clientCog->adminRegisterUser($PascaProsesUsername, $PasswordProses, $MessageAction, [
                                'email' => $PascaProsesEmail,
                                'email_verified' => 'true',
                                'gender' => $DataStaffUser['Gender'],
                                'name' => $PascaProsesName,
                                'phone_number' => $PascaProsesPhoneNumber,
                                'phone_number_verified' => 'true',
                                'custom:ispalmoiltrace' => '1'
                            ]);

                            if(isset($createUserCog['User']['Username'])) {
                                $UserAttributes = mappingArrayCognitoAttributes($createUserCog['User']['Attributes']);
                                $AwsUserSub = $UserAttributes['sub'];

                                //update user
                                $sql = "UPDATE sys_user a SET
                                            a.`UserName` = ?,
                                            a.UserInCognito = 'Yes',
                                            a.CognitoUserSub = ?,
                                            a.CognitoUserStatus = ?
                                        WHERE
                                            a.UserId = ?
                                        LIMIT 1";
                                $p = array(
                                    $PascaProsesUsername,
                                    $AwsUserSub,
                                    'FORCE_CHANGE_PASSWORD',
                                    $UserIdProses
                                );
                                $query = $this->db->query($sql,$p);
                                //Sampai sini selesai, proses berakhir

                            } else {
                                $StatusTransaction = 'Failed';
                                $Summary = $Summary."Failed to create user on identity server".', '.decodeMsgAws($createUserCog)." (mode: CREATE) (passwordnya: $PasswordProses)\n";
                            }

                            //Mulai Update proses CREATE =============================== (End)
                        } else {
                            $StatusTransaction = 'Failed';
                        }
                    }
                }

            } else {
                $StatusTransaction = 'Failed';
                $Summary = "Data User tidak lengkap di tabel sys_user, ktv_persons, ktv_staffs / User sudah terdaftar di cognito\n";
            }

            //Insert log summary (Begin)
            $sql = "UPDATE `tmp_migcog_proses` SET
                    `UserIDProses` = ?,
                    `PersonIDProses` = ?,
                    `StaffIDProses` = ?,
                    `PreProsesName` = ?,
                    `PreProsesUsername` = ?,
                    `PreProsesEmail` = ?,
                    `PreProsesPhoneNumber` = ?,
                    `UrlProses` = ?,
                    `PascaProsesName` = ?,
                    `PascaProsesUsername` = ?,
                    `PascaProsesEmail` = ?,
                    `PascaProsesPhoneNumber` = ?,
                    `StatusTransaction` = ?,
                    `Summary` = ?,
                    Timestamp = NOW()
                    WHERE
                        UserIdExcel = ?
                    LIMIT 1";
            $p = array(
                $UserIdProses,
                $PersonIDProses,
                $StaffIDProses,
                $PreProsesName,
                $PreProsesUsername,
                $PreProsesEmail,
                $PreProsesPhoneNumber,
                $UrlProses,
                $PascaProsesName,
                $PascaProsesUsername,
                $PascaProsesEmail,
                $PascaProsesPhoneNumber,
                $StatusTransaction,
                $Summary,
                $UserIdProses
            );
            $query = $this->db->query($sql,$p);
            //Insert log summary (End)
        }

        $return['success'] = true;
        return $return;
    }

    public function MigCogPredefPass($userid) {
        $UserIdProses = $userid;
        $ArrUserIdProses = explode(",",$UserIdProses);
        $UrlProses = 'migrasi_cognito_predefinedpass';

        for ($i=0; $i < count($ArrUserIdProses); $i++) {
            $UserIdProses = $ArrUserIdProses[$i];
            $PersonIDProses = null;
            $StaffIDProses = null;
            $PreProsesName = null;
            $PreProsesUsername = null;
            $PreProsesEmail = null;
            $PreProsesPhoneNumber = null;
            $PascaProsesName = null;
            $PascaProsesUsername = null;
            $PascaProsesEmail = null;
            $PascaProsesPhoneNumber = null;
            $StatusTransaction = 'Success';
            $Summary = '';

            //Ambil data staff nya
            $sql = "SELECT
                        a.`UserId`
                        , a.`UserName` AS `Username`
                        , a.`UserRealName` AS `Name`
                        , a.`UserEmail` AS `Email`
                        , b.OfficialCellPhone AS `Phone`
                        , b.Gender
                        , b.PersonID
                        , c.StaffID
                        , a.UserInCognito
                        , d.`PredefPassExcel`
                    FROM
                        sys_user a
                        INNER JOIN ktv_persons b ON a.`UserId` = b.`UserID`
                        INNER JOIN ktv_staffs c ON b.`PersonID` = c.`PersonID`
                        INNER JOIN tmp_migcog_proses d ON a.`UserId` = d.`UserIdExcel`
                    WHERE 1=1
                        AND a.`UserId` = ?
                    LIMIT 1";
            $p = array(
                $UserIdProses
            );
            $DataStaffUser = $this->db->query($sql,$p)->row_array();
            if($DataStaffUser['UserId'] != "" && $DataStaffUser['UserInCognito'] == 'No') {
                $PersonIDProses = $DataStaffUser['PersonID'];
                $StaffIDProses = $DataStaffUser['StaffID'];
                $PreProsesName = $DataStaffUser['Name'];
                $PreProsesUsername = $DataStaffUser['Username'];
                $PreProsesEmail = $DataStaffUser['Email'];
                $PreProsesPhoneNumber = $DataStaffUser['Phone'];
                $PredefPassExcel = $DataStaffUser['PredefPassExcel'];

                //1. Cek alamat email apakah valid
                if (!filter_var($PreProsesEmail, FILTER_VALIDATE_EMAIL)) {
                    $StatusTransaction = 'Failed';
                    $Summary = $Summary."Alamat Email $PreProsesEmail tidak valid\n";
                }

                //2.Cek apakah alamat email staff ini ada duplikat dengan staff lain
                $CekEmail = $this->CekDuplikatEmail($PreProsesEmail,$PersonIDProses);
                if($CekEmail == true) {
                    $StatusTransaction = 'Failed';
                    $Summary = $Summary."Alamat Email $PreProsesEmail duplikat dengan staff lain\n";
                }

                //3. Cek No Hp Duplikat
                $CekNoHp = $this->CekDuplikatNoHp($PreProsesPhoneNumber,$PersonIDProses);
                if($CekNoHp == true) {
                    $StatusTransaction = 'Failed';
                    $Summary = $Summary."No telepon $PreProsesPhoneNumber duplikat dengan staff lain\n";
                }

                //4. Cek username apakah alamat email
                /*if (filter_var($PreProsesUsername, FILTER_VALIDATE_EMAIL)) {
                    $StatusTransaction = 'Failed';
                    $Summary = $Summary."Username $PreProsesUsername tidak boleh berupa alamat email\n";
                }*/

                //Cek data valid, jika valid lanjut lagi
                if($StatusTransaction == 'Success') {
                    $ResCekEmailCog = $this->CheckLinkedCognitoOnly($PreProsesEmail);

                    if($ResCekEmailCog['statusLinked'] == 'Yes') { //Sudah ada dicognito, jadi mode LINK
                        $StatusTransaction = 'Failed';
                        $Summary = $Summary."User account dengan email $PreProsesEmail sudah terdaftar di cognito sehingga tidak bisa diproses dengan predefined password \n";
                    }

                    if($ResCekEmailCog['statusLinked'] == 'No') { //Belum ada dicognito, jadi mode CREATE
                        $PascaProsesUsername = $PreProsesUsername;
                        $PascaProsesEmail = $PreProsesEmail;
                        $PascaProsesName = $PreProsesName;
                        $PasswordProses = $PredefPassExcel;
                        $PascaProsesPhoneNumber = $PreProsesPhoneNumber;
                        $StatusValidDataCreate = true;

                        //Cek username, jika email, maka ganti jadi non email
                        if (filter_var($PreProsesUsername, FILTER_VALIDATE_EMAIL)) {
                            $PascaProsesUsername = usernameOfEmail($PreProsesUsername);
                        }

                        //Cek username duplikat
                        $CekDuplikatUsername = $this->CekDuplikatUsername($PascaProsesUsername,$UserIdProses);
                        if($CekDuplikatUsername == true) {
                            $StatusValidDataCreate = false;
                            $Summary = $Summary."Username $PascaProsesUsername duplikat di sys_user (mode: CREATE)\n";
                        }

                        //Cek apakah phone number duplikat di cognito
                        $Filter = 'phone_number = "'.$PascaProsesPhoneNumber.'"';
                        $proses = $this->clientCog->listUsersWithFilter($Filter);
                        if(isset($proses['Users'][0]['Username'])) {
                            $StatusValidDataCreate = false;
                            $Summary = $Summary."Phone number $PascaProsesPhoneNumber duplikat di cognito (mode: CREATE)\n";
                        }

                        //Cek password valid tidak
                        $cekValidasiPassword = cekValidasiPassword($PasswordProses);
                        if($cekValidasiPassword['success'] == false) {
                            $StatusValidDataCreate = false;
                            $Summary = $Summary."Password $PasswordProses tidak sesuai dengan standard, {$cekValidasiPassword['message']}  (mode: CREATE)\n";
                        }

                        if($StatusValidDataCreate == true) {
                            //Mulai Update proses CREATE =============================== (Begin)

                            //Create User
                            $MessageAction = "SUPPRESS"; //not send email confirmation
                            $createUserCog = $this->clientCog->adminRegisterUser($PascaProsesUsername, $PasswordProses, $MessageAction, [
                                'email' => $PascaProsesEmail,
                                'email_verified' => 'true',
                                'gender' => $DataStaffUser['Gender'],
                                'name' => $PascaProsesName,
                                'phone_number' => $PascaProsesPhoneNumber,
                                'phone_number_verified' => 'true',
                                'custom:ispalmoiltrace' => '1'
                            ]);

                            if(isset($createUserCog['User']['Username'])) {
                                //Auto confirm user
                                $response = $this->clientCog->adminSetUserPassword($PascaProsesUsername,$PasswordProses);

                                $UserAttributes = mappingArrayCognitoAttributes($createUserCog['User']['Attributes']);
                                $AwsUserSub = $UserAttributes['sub'];

                                //update user
                                $sql = "UPDATE sys_user a SET
                                            a.`UserName` = ?,
                                            a.UserInCognito = 'Yes',
                                            a.CognitoUserSub = ?,
                                            a.CognitoUserStatus = ?
                                        WHERE
                                            a.UserId = ?
                                        LIMIT 1";
                                $p = array(
                                    $PascaProsesUsername,
                                    $AwsUserSub,
                                    'CONFIRMED',
                                    $UserIdProses
                                );
                                $query = $this->db->query($sql,$p);
                                //Sampai sini selesai, proses berakhir

                            } else {
                                $StatusTransaction = 'Failed';
                                $Summary = $Summary."Failed to create user on identity server".', '.decodeMsgAws($createUserCog)." (mode: CREATE)\n";
                            }

                            //Mulai Update proses CREATE =============================== (End)
                        } else {
                            $StatusTransaction = 'Failed';
                        }

                    }
                }

            } else {
                $StatusTransaction = 'Failed';
                $Summary = "Data User tidak lengkap di tabel sys_user, ktv_persons, ktv_staffs / User sudah terdaftar di cognito\n";
            }

            //Insert log summary (Begin)
            $sql = "UPDATE `tmp_migcog_proses` SET
                    `UserIDProses` = ?,
                    `PersonIDProses` = ?,
                    `StaffIDProses` = ?,
                    `PreProsesName` = ?,
                    `PreProsesUsername` = ?,
                    `PreProsesEmail` = ?,
                    `PreProsesPhoneNumber` = ?,
                    `UrlProses` = ?,
                    `PascaProsesName` = ?,
                    `PascaProsesUsername` = ?,
                    `PascaProsesEmail` = ?,
                    `PascaProsesPhoneNumber` = ?,
                    `StatusTransaction` = ?,
                    `Summary` = ?,
                    Timestamp = NOW()
                    WHERE
                        UserIdExcel = ?
                    LIMIT 1";
            $p = array(
                $UserIdProses,
                $PersonIDProses,
                $StaffIDProses,
                $PreProsesName,
                $PreProsesUsername,
                $PreProsesEmail,
                $PreProsesPhoneNumber,
                $UrlProses,
                $PascaProsesName,
                $PascaProsesUsername,
                $PascaProsesEmail,
                $PascaProsesPhoneNumber,
                $StatusTransaction,
                $Summary,
                $UserIdProses
            );
            $query = $this->db->query($sql,$p);
            //Insert log summary (End)
        }

        $return['success'] = true;
        return $return;
    }

    public function FixNotelp() {
        $UsernameCog = "";
        $ArrUserCog = explode(",",$UsernameCog);

        for ($i=0; $i < count($ArrUserCog); $i++) {
            $UsernameProses = $ArrUserCog[$i];
            $notelpCog = '+62812000100'.$i;

            $prosesUpdateCog = $this->clientCog->updateUserAttributes($UsernameProses, [
                'phone_number_verified' => 'true',
                'phone_number' => $notelpCog
            ]);
        }
    }

}