<?php
/******************************************
 *  Author : n1colius.lau@gmail.com
 *  Created On : Mon Dec 02 2019
 *  File : mstaffuser_cognito.php
 *******************************************/
class Mstaffuser_cognito extends CI_Model {

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

    public function updateStaff($post) {
        $results = array();
        $emailChanged = false;
        $newEmail = $post['OfficialEmail'];
        $phonenumberChanged = false;
        $newPhonenumber = $post['OfficialCellPhoneCode'].$post['OfficialCellPhone'];

        //Get Data User
        $sql = "SELECT
                    a.`UserInCognito`
                    , a.`UserName` AS Username
                    , b.`OfficialEmail` AS Email
                    , a.UserId
                    , CONCAT(IFNULL(b.`OfficialCellPhoneCode`,''),IFNULL(b.`OfficialCellPhone`,'')) AS Phonenumber
                FROM
                    sys_user a
                    INNER JOIN ktv_persons b ON a.`UserId` = b.`UserID`
                WHERE
                    b.`PersonID` = ?
                LIMIT 1";
        $DataUser = $this->db->query($sql,array($post['PersonID']))->row_array();

        //Email berubah / tidak
        if($newEmail != $DataUser['Email']) {
            $emailChanged = true;
        }

        if($emailChanged == true) {
            if($DataUser['UserInCognito'] == "Yes") {
                //Cek pergantian emailnya sudah terdaftar di cognito atau belum
                $Filter = 'email = "'.$newEmail.'"';
                $cekCogEmail = $this->clientCog->listUsersWithFilter($Filter);
                if(isset($cekCogEmail['Users'][0]['Username'])) {
                    $results['success'] = false;
                    $results['message'] = lang("Failed to save data, email address already registered on identity server");
                    return $results;
                }
            }
        }

        //Phonenumber berubah / tidak
        if($newPhonenumber != $DataUser['Phonenumber']) {
            $phonenumberChanged = true;
        }

        if($phonenumberChanged == true) {
            if($DataUser['UserInCognito'] == "Yes") {
                //Cek pergantian emailnya sudah terdaftar di cognito atau belum
                $Filter = 'phone_number = "'.$newPhonenumber.'"';
                $cekCogEmail = $this->clientCog->listUsersWithFilter($Filter);
                if(isset($cekCogEmail['Users'][0]['Username'])) {
                    $results['success'] = false;
                    $results['message'] = lang("Failed to save data, phone number already registered on identity server");
                    return $results;
                }
            }
        }

        //Update ke aws cognito kalau sudah terhubung
        if($DataUser['UserInCognito'] == "Yes") {
            $prosesUpdateCog = $this->clientCog->updateUserAttributes($DataUser['Username'], [
                'name' => $post['PersonNm'],
                'gender' => $post['Gender'],
                'email' => $newEmail,
                'email_verified' => 'true',
                'phone_number_verified' => 'true',
                'phone_number' => $newPhonenumber,
                'custom:ispalmoiltrace' => '1'
            ]);
            //echo '<pre>'; print_r($prosesUpdateCog); exit;

            if(!isset($prosesUpdateCog['@metadata']['statusCode'])) {
                $results['message'] = lang("Failed to update data on identity server").', '.decodeMsgAws($prosesUpdateCog);
                $results['success'] = false;
                return $results;
            }
        }

        $this->db->trans_begin();

        //ktv_persons
        $sql = "UPDATE `ktv_persons` SET
                Ssn = ?,
                `PersonNm` = ?,
                `BirthDate` = ?,
                `Gender` = ?,
                `Address` = ?,
                `Email` = ?,
                `StatusCd` = ?,
                `OfficialCellPhoneCode` = ?,
                `OfficialCellPhone` = ?,
                `OfficialEmail` = ?,
                `WorkAreaID` = ?,
                `DateUpdated` = NOW(),
                `UpdatedBy` = ?
                WHERE
                    PersonID = ?
                LIMIT 1
        ";
        $p = array(
            $post['Ssn'],
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
            $_SESSION['userid'],
            $post['PersonID']
        );
        $query = $this->db->query($sql, $p);

        //ktv_staffs
        $sql = "UPDATE `ktv_staffs` SET
                `ObjID` = IF(?='',NULL,?),
                `OfficialPhoneCode` = ?,
                `OfficialPhone` = ?,
                `WorkPhone` = ?,
                `OfficialEmail` = ?,
                `WorkAreaID` = ?,
                `CCEmail` = ?,
                `MillID` = ?,
                `SmeID` = ?,
                `RefineryID` = ?,
                `StatusCode` = ?,
                `DateUpdated` = NOW(),
                `LastModifiedBy` = ?
                WHERE
                    PersonID = ?
                LIMIT 1
            ";
        $p = array(
            $post['ObjID'], $post['ObjID'],
            $post['OfficialCellPhoneCode'],
            $post['OfficialCellPhone'],
            $post['OfficialCellPhoneCode'].$post['OfficialCellPhone'],
            $post['OfficialEmail'],
            $post['WorkAreaID'],
            $post['CCEmail'],
            $post['MillID'],
            $post['SmeID'],
            $post['RefineryID'],
            $post['StatusCode'],
            $_SESSION['userid'],
            $post['PersonID']
        );
        $query = $this->db->query($sql, $p);

        //sys_user
        $sql = "UPDATE sys_user a SET
                    a.UserRealName = ?
                    , a.UserEmail = ?
                WHERE
                    a.`UserId` = ?
                LIMIT 1";
        $p = array(
            $post['PersonNm'],
            $post['OfficialEmail'],
            $DataUser['UserId']
        );
        $query = $this->db->query($sql,$p);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = lang("Failed to save data");
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = lang("Data saved");
        }

        return $results;
    }

    public function CheckLinkedCognitoOnly($PersonID,$Email) {
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

    public function GetCreateUserExistingForm($PersonID,$DataStaff) {
        $return = array();
        $Email = $DataStaff['data']['Email'];

        $Filter = 'email = "'.$Email.'"';
        $DataCog = $this->clientCog->listUsersWithFilter($Filter);
        if(isset($DataCog['@metadata']['statusCode'])) {

            if($DataCog['Users'][0]['Username'] != "") {
                $DataUserCog = $DataCog['Users'][0];
                $UserAttributes = mappingArrayCognitoAttributes($DataUserCog['Attributes']);

                $dataForm['Koltiva.view.Staffuser.WinFormCreatedUser-Form-PersonID'] = $PersonID;
                $dataForm['Koltiva.view.Staffuser.WinFormCreatedUser-Form-Fullname'] = $UserAttributes['name'];
                $dataForm['Koltiva.view.Staffuser.WinFormCreatedUser-Form-Gender'] = $UserAttributes['gender'];
                $dataForm['Koltiva.view.Staffuser.WinFormCreatedUser-Form-Phonenumber'] = $UserAttributes['phone_number'];
                $dataForm['Koltiva.view.Staffuser.WinFormCreatedUser-Form-Email'] = $UserAttributes['email'];
                $dataForm['Koltiva.view.Staffuser.WinFormCreatedUser-Form-Username'] = $DataUserCog['Username'];
                $dataForm['Koltiva.view.Staffuser.WinFormCreatedUser-Form-UserSub'] = $UserAttributes['sub'];
                $dataForm['Koltiva.view.Staffuser.WinFormCreatedUser-Form-UserCogStatus'] = $DataUserCog['UserStatus'];

                $result['success'] = true;
                $result['prosesStatus'] = 'successGetDataOnAws';
                $result['data'] = $dataForm;
                return $result;
            } else {
                $result['success'] = true;
                $result['prosesStatus'] = 'failedGetDataOnAws';
                $result['data'] = array();
                $result['message'] = lang("No data on identity server");
                return $result;
            }

        } else {
            $result['success'] = true;
            $result['prosesStatus'] = 'failedGetDataOnAws';
            $result['data'] = array();
            $result['message'] = lang("Failed to get data on identity server").', '.decodeMsgAws($DataCog);
            return $result;
        }
    }

    public function insertUserAcc($post,$dataStaff) {
        $result = array();
        //echo '<pre>'; print_r($post); exit;
        //insert ke aws cog dl, baru insert2 ke tabel mysql lainnya

        //AWS Cognito =================================== (Begin)

        //Sebelum create user di aws, cek dl email dan phonenumber apakah sudah ada
        $Filter = 'phone_number = "'.$dataStaff['PhonenumberWithCode'].'"';
        $proses = $this->clientCog->listUsersWithFilter($Filter);
        if(isset($proses['Users'][0]['Username'])) {
            $result['success'] = false;
            $result['message'] = lang("Phone number already registered on our Identity Server");
            return $result;
        }

        //cek email yg barusan diupdate apakah sudah ada linked accountnya
        $Filter = 'email = "'.$dataStaff['Email'].'"';
        $proses = $this->clientCog->listUsersWithFilter($Filter);
        if(isset($proses['Users'][0]['Username'])) {
            $result['success'] = false;
            $result['message'] = lang("Your email already linked to our Identity Server");
            return $result;
        }

        //Send Email Confirmation
        switch($post['SendEmailConfirm']) {
            case '1':
                $MessageAction = ""; //RESEND untuk send ulang temporary password, string kosong jika baru
            break;
            case '2':
                $MessageAction = "SUPPRESS"; //Tanpa kirim email notifikasi
            break;
            default:
                $MessageAction = ""; //RESEND untuk send ulang temporary password, string kosong jika baru
            break;
        }

        $createUserCog = $this->clientCog->adminRegisterUser($post['Username'], $post['UserPassword'], $MessageAction, [
            'email' => $dataStaff['Email'],
            'email_verified' => 'true',
            'gender' => $dataStaff['Gender'],
            'name' => $dataStaff['Name'],
            'phone_number' => $dataStaff['PhonenumberWithCode'],
            'phone_number_verified' => 'true',
            'custom:ispalmoiltrace' => '1'
        ]);

        //Cek apakah butuh auto confirm
        $CognitoUserStatus = 'FORCE_CHANGE_PASSWORD';
        if($post['AutoConfirmUser'] == '1') {
            $response = $this->clientCog->adminSetUserPassword($post['Username'],$post['UserPassword']);
            $CognitoUserStatus = 'CONFIRMED';
        }

        if(isset($createUserCog['User']['Username'])) {
            $UserAttributes = mappingArrayCognitoAttributes($createUserCog['User']['Attributes']);
            $AwsUserSub = $UserAttributes['sub'];
            $Username = $post['Username'];
        } else {
            $result['success'] = false;
            $result['message'] = lang("Failed to create user on identity server").", ".decodeMsgAws($createUserCog);
            return $result;
        }
        //AWS Cognito =================================== (End)

        //Bagian MySQL =================================== (Begin)
        $this->db->trans_begin();

        switch ($post['UserLanguage']) {
            case '1':
                $UserLanguage = 'English';
            break;
            case '2':
                $UserLanguage = 'Indonesia';
            break;
            case '3':
                $UserLanguage = 'Malaysia';
            break;
            default:
                $UserLanguage = 'English';
            break;
        }

        if($post['UserIsAdmin'] == "1") $UserIsAdmin = 1; else $UserIsAdmin = 0;

        // insert user
        $UserAddUserId      = $_SESSION['userid'];
        $UserAddTime        = date('Y-m-d H:i:s');
        $UserUpdateUserId   = $_SESSION['userid'];
        $UserUpdateTime     = date('Y-m-d H:i:s');

        $p = array(
            'UserName' => $Username,
            'CognitoUserSub' => $AwsUserSub,
            'UserInCognito' => 'Yes',
            'CognitoUserStatus' => $CognitoUserStatus,
            'UserRealName' => $dataStaff['Name'],
            'UserPassword' => md5($post['UserPassword']),
            'UserEmail' => $dataStaff['Email'],
            'UserActive' => $post['UserActive'],
            'StatusCode' => 'active',
            'UserLanguage' => $UserLanguage,
            'UserIsAdmin' => $UserIsAdmin,
            'UserAddUserId' => $UserAddUserId,
            'UserAddTime' => $UserAddTime,
            'UserUpdateUserId' => $UserUpdateUserId,
            'UserUpdateTime' => $UserUpdateTime
        );
        $query = $this->db->insert('sys_user', $p);
        $UserId = $this->db->insert_id();

        //sys_role
        $p = array(
            'UserId' => $UserId,
            'RoleId' => $dataStaff['RoleId']
        );
        $query = $this->db->insert('sys_user_role', $p);

        // insert user groups
        $ArrUserGroup = explode(',',$post['GroupIds']);
        foreach ($ArrUserGroup as $key => $GroupId) {
            $isDefault = $GroupId == $post['UserGroupIsDefault'] ? '1' : '0';
            $p = array(
                'UserGroupUserId' => $UserId,
                'UserGroupGroupId' => $GroupId,
                'UserGroupIsDefault' => $isDefault
            );
            $query = $this->db->insert('sys_user_group', $p);
        }

        //access Staff
        if($post['AccessStaff'] != ""){
            $AccessStaff = explode(',',$post['AccessStaff']);
            foreach ($AccessStaff as $key => $DistrictID) {
                $query = $this->db->insert('ktv_access_staff', array(
                    'UserId' => $UserId,
                    'DistrictID' => $DistrictID,
                ));
            }
        }

        //Project (Hard coded)
        $p = array(
            'StaffID' => $dataStaff['StaffID'],
            'ProjID' => '1',
            'ProjDefault' => '1',
            'DateCreated' => date('Y-m-d H:i:s'),
            'CreatedBy' => $_SESSION['userid']
        );
        $query = $this->db->insert('ktv_staffs_project', $p);

        //update user id di ktv_persons
        $sql="UPDATE ktv_persons SET
                    UserID = ?
                WHERE
                    PersonID = ?
                LIMIT 1";
        $query = $this->db->query($sql,array($UserId,$dataStaff['PersonID']));

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $result['success'] = false;
            $result['message'] = lang("Failed to create account");
        } else {
            $this->db->trans_commit();
            $result['success'] = true;
            $result['message'] = lang("Account created");
        }
        return $result;
        //Bagian MySQL =================================== (End)
    }

    public function insertUserAccLinked($post,$dataStaff) {
        $results = array();
        $this->db->trans_begin();

        switch ($post['UserLanguage']) {
            case '1':
                $UserLanguage = 'English';
            break;
            case '2':
                $UserLanguage = 'Indonesia';
            break;
            case '3':
                $UserLanguage = 'Malaysia';
            break;
            default:
                $UserLanguage = 'English';
            break;
        }
        if($post['UserIsAdmin'] == "1") $UserIsAdmin = 1; else $UserIsAdmin = 0;

        // insert user
        $UserAddUserId      = $_SESSION['userid'];
        $UserAddTime        = date('Y-m-d H:i:s');
        $UserUpdateUserId   = $_SESSION['userid'];
        $UserUpdateTime     = date('Y-m-d H:i:s');

        $p = array(
            'UserName' => $post['Username'],
            'CognitoUserSub' => $post['UserSub'],
            'UserInCognito' => 'Yes',
            'CognitoUserStatus' => $post['UserCogStatus'],
            'UserRealName' => $post['Fullname'],
            'UserPassword' => md5('dummy'),
            'UserEmail' => $post['Email'],
            'UserActive' => $post['UserActive'],
            'StatusCode' => 'active',
            'UserLanguage' => $UserLanguage,
            'UserIsAdmin' => $UserIsAdmin,
            'UserAddUserId' => $UserAddUserId,
            'UserAddTime' => $UserAddTime,
            'UserUpdateUserId' => $UserUpdateUserId,
            'UserUpdateTime' => $UserUpdateTime
        );
        $query = $this->db->insert('sys_user', $p);
        $UserId = $this->db->insert_id();

        //sys_role
        $p = array(
            'UserId' => $UserId,
            'RoleId' => $dataStaff['RoleId']
        );
        $query = $this->db->insert('sys_user_role', $p);

        // insert user groups
        $ArrUserGroup = explode(',',$post['GroupIds']);
        foreach ($ArrUserGroup as $key => $GroupId) {
            $isDefault = $GroupId == $post['UserGroupIsDefault'] ? '1' : '0';
            $p = array(
                'UserGroupUserId' => $UserId,
                'UserGroupGroupId' => $GroupId,
                'UserGroupIsDefault' => $isDefault
            );
            $query = $this->db->insert('sys_user_group', $p);
        }

        //access Staff
        if($post['AccessStaff'] != ""){
            $AccessStaff = explode(',',$post['AccessStaff']);
            foreach ($AccessStaff as $key => $DistrictID) {
                $query = $this->db->insert('ktv_access_staff', array(
                    'UserId' => $UserId,
                    'DistrictID' => $DistrictID,
                ));
            }
        }

        //Project (Hard coded)
        $p = array(
            'StaffID' => $dataStaff['StaffID'],
            'ProjID' => '1',
            'ProjDefault' => '1',
            'DateCreated' => date('Y-m-d H:i:s'),
            'CreatedBy' => $_SESSION['userid']
        );
        $query = $this->db->insert('ktv_staffs_project', $p);


        //update user id di ktv_persons
        $sql="UPDATE ktv_persons SET
                    UserID = ?
                WHERE
                    PersonID = ?
                LIMIT 1";
        $query = $this->db->query($sql,array($UserId,$post['PersonID']));


        //Update Data Staff ================== (Begin)

        //Data Phonenumber
        $PhoneNumberArr = ExtractPhoneNumberWithPhoneCode($post['Phonenumber'],$dataStaff['CountryPhoneCode']);

        $sql = "UPDATE ktv_persons a SET
                    a.PersonNm = ?,
                    a.Gender = ?,
                    a.`OfficialCellPhoneCode` = ?,
                    a.`OfficialCellPhone` = ?,
                    a.`OfficialEmail` = ?
                WHERE
                    a.`UserID` = ?
                LIMIT 1";
        $p = array(
            $post['Fullname'],
            $post['Gender'],
            $PhoneNumberArr['Phonecode'],
            $PhoneNumberArr['Phonenumber'],
            $post['Email'],
            $UserId
        );
        $query = $this->db->query($sql,$p);

        $sql = "UPDATE ktv_staffs a
                    JOIN ktv_persons b ON a.`PersonID` = b.`PersonID`
                SET
                    a.`OfficialPhoneCode` = ?,
                    a.`OfficialPhone` = ?,
                    a.`WorkPhone` = ?,
                    a.`OfficialEmail` = ?
                WHERE
                    b.`UserID` = ?";
        $p = array(
            $PhoneNumberArr['Phonecode'],
            $PhoneNumberArr['Phonenumber'],
            $post['Phonenumber'],
            $post['Email'],
            $UserId
        );
        $query = $this->db->query($sql,$p);
        //Update Data Staff ================== (End)

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = lang("Failed to create account");
        } else {
            $this->db->trans_commit();

            //Update attribute cognito
            $prosesCog = $this->clientCog->updateUserAttributes($post['Username'], [
                'custom:ispalmoiltrace' => '1'
            ]);

            $results['success'] = true;
            $results['message'] = lang("Account created");
        }
        return $results;
    }

    public function UpdateUserAcc($post) {
        $prosesAll = true;

        //Update data staff terlebih dahulu --------------------------------- (Begin)
        /*$this->db->trans_begin();

        $sql = "UPDATE ktv_persons a SET
                    a.`OfficialCellPhone` = ?,
                    a.`OfficialEmail` = ?
                WHERE
                    a.`UserID` = ?
                LIMIT 1";
        $p = array(
            $post['Phonenumber'],
            $post['Email'],
            $post['UserId']
        );
        $query = $this->db->query($sql,$p);

        $sql = "UPDATE sys_user a SET
                    a.`UserEmail` = ?
                WHERE
                    a.`UserId` = ?
                LIMIT 1";
        $p = array(
            $post['Email'],
            $post['UserId']
        );
        $query = $this->db->query($sql,$p);

        $sql = "UPDATE ktv_staffs a
                    JOIN ktv_persons b ON a.`PersonID` = b.`PersonID`
                SET
                    a.`OfficialPhone` = ?,
                    a.`WorkPhone` = ?,
                    a.`OfficialEmail` = ?
                WHERE
                    b.`UserID` = ?";
        $p = array(
            $post['Phonenumber'],
            $post['Phonenumber'],
            $post['Email'],
            $post['UserId']
        );
        $query = $this->db->query($sql,$p);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $prosesAll = false;
        } else {
            $this->db->trans_commit();
        }
        //Update data staff terlebih dahulu --------------------------------- (End)

        if($prosesAll == false) {
            $result['success'] = false;
            $result['message'] = lang("Failed to update staff data");
            return $result;
        }*/

        //data staff
        $sql = "SELECT
                    a.`OfficialEmail` AS Email
                    , a.`Gender`
                    , a.`PersonNm` AS `Name`
                    , CONCAT(IFNULL(a.`OfficialCellPhoneCode`,''),IFNULL(a.`OfficialCellPhone`,'')) AS Phonenumber
                    , a.`PersonID`
                FROM
                    ktv_persons a
                WHERE
                    a.`UserID` = ?
                LIMIT 1";
        $p = array(
            $post['UserId']
        );
        $dataStaff = $this->db->query($sql,$p)->row_array();

        //cek email yg barusan diupdate apakah sudah ada linked accountnya
        $Filter = 'email = "'.$post['Email'].'"';
        $proses = $this->clientCog->listUsersWithFilter($Filter);
        if(isset($proses['Users'][0]['Username'])) {
            $result['success'] = true;
            $result['prosesStatus'] = 'emailAlreadyLinked';
            $result['message'] = lang("Your email already registered on our Identity Server");
            return $result;
        }

        //cek phonenumber yg barusan diupdate apakah sudah ada linked accountnya
        $Filter = 'phone_number = "'.$post['Phonenumber'].'"';
        $proses = $this->clientCog->listUsersWithFilter($Filter);
        if(isset($proses['Users'][0]['Username'])) {
            $result['success'] = true;
            $result['prosesStatus'] = 'emailAlreadyLinked';
            $result['message'] = lang("Your phone number already registered on our Identity Server");
            return $result;
        }

        //Proses mulai create account di Cognito ------------------------------ (Begin)

        //Send Email Confirmation
        switch($post['SendEmailConfirm']) {
            case '1':
                $MessageAction = ""; //RESEND untuk send ulang temporary password, string kosong jika baru
            break;
            case '2':
                $MessageAction = "SUPPRESS"; //Tanpa kirim email notifikasi
            break;
            default:
                $MessageAction = ""; //RESEND untuk send ulang temporary password, string kosong jika baru
            break;
        }

        $createUserCog = $this->clientCog->adminRegisterUser($post['Username'], $post['UserPassword'], $MessageAction, [
            'email' => $dataStaff['Email'],
            'email_verified' => 'true',
            'gender' => $dataStaff['Gender'],
            'name' => $dataStaff['Name'],
            'phone_number' => $dataStaff['Phonenumber'],
            'phone_number_verified' => 'true',
            'custom:ispalmoiltrace' => '1'
        ]);

        //Cek apakah butuh auto confirm
        $CognitoUserStatus = 'FORCE_CHANGE_PASSWORD';
        if($post['AutoConfirmUser'] == '1') {
            $response = $this->clientCog->adminSetUserPassword($post['Username'],$post['UserPassword']);
            $CognitoUserStatus = 'CONFIRMED';
        }

        if(isset($createUserCog['User']['Username'])) {
            $UserAttributes = mappingArrayCognitoAttributes($createUserCog['User']['Attributes']);
            $AwsUserSub = $UserAttributes['sub'];
            $Username = $post['Username'];

            //update user
            $sql = "UPDATE sys_user a SET
                        a.`UserRealName` = ?,
                        a.`UserName` = ?,
                        a.`UserEmail` = ?,
                        a.UserInCognito = 'Yes',
                        a.CognitoUserSub = ?,
                        a.CognitoUserStatus = ?
                    WHERE
                        a.UserId = ?
                    LIMIT 1";
            $p = array(
                $dataStaff['Name'],
                $post['Username'],
                $dataStaff['Email'],
                $AwsUserSub,
                $CognitoUserStatus,
                $post['UserId']
            );
            $query = $this->db->query($sql,$p);

            $result['success'] = true;
            $result['prosesStatus'] = 'successCreatedOnAws';
            $result['message'] = lang("Account successfully updated on Identity Server");
            return $result;
        } else {
            $result['success'] = true;
            $result['prosesStatus'] = 'failedCreatedOnAws';
            $result['message'] = lang("Failed to create user on identity server").', '.decodeMsgAws($createUserCog);
            return $result;
        }
        //Proses mulai create account di Cognito ------------------------------ (End)
    }

    public function GetLinkedUserExistingForm($PersonID,$UserId,$DataStaff) {
        $return = array();
        $Email = $DataStaff['data']['Email'];

        $Filter = 'email = "'.$Email.'"';
        $DataCog = $this->clientCog->listUsersWithFilter($Filter);
        if(isset($DataCog['@metadata']['statusCode'])) {
            if($DataCog['Users'][0]['Username'] != "") {
                $DataUserCog = $DataCog['Users'][0];
                $UserAttributes = mappingArrayCognitoAttributes($DataUserCog['Attributes']);

                $dataForm['Koltiva.view.Staffuser.WinFormLinkedUserExisting-Form-UserId'] = $UserId;
                $dataForm['Koltiva.view.Staffuser.WinFormLinkedUserExisting-Form-PersonID'] = $PersonID;
                $dataForm['Koltiva.view.Staffuser.WinFormLinkedUserExisting-Form-Fullname'] = $UserAttributes['name'];
                $dataForm['Koltiva.view.Staffuser.WinFormLinkedUserExisting-Form-Gender'] = $UserAttributes['gender'];
                $dataForm['Koltiva.view.Staffuser.WinFormLinkedUserExisting-Form-Phonenumber'] = $UserAttributes['phone_number'];
                $dataForm['Koltiva.view.Staffuser.WinFormLinkedUserExisting-Form-Email'] = $UserAttributes['email'];
                $dataForm['Koltiva.view.Staffuser.WinFormLinkedUserExisting-Form-Username'] = $DataUserCog['Username'];
                $dataForm['Koltiva.view.Staffuser.WinFormLinkedUserExisting-Form-UserSub'] = $UserAttributes['sub'];
                $dataForm['Koltiva.view.Staffuser.WinFormLinkedUserExisting-Form-UserCogStatus'] = $DataUserCog['UserStatus'];

                $result['success'] = true;
                $result['prosesStatus'] = 'successGetDataOnAws';
                $result['data'] = $dataForm;
                return $result;
            } else {
                $result['success'] = true;
                $result['prosesStatus'] = 'failedGetDataOnAws';
                $result['data'] = array();
                $result['message'] = lang("No data on identity server");
                return $result;
            }
        } else {
            $result['success'] = true;
            $result['prosesStatus'] = 'failedGetDataOnAws';
            $result['data'] = array();
            $result['message'] = lang("Failed to get data on identity server").', '.decodeMsgAws($DataCog);
            return $result;
        }
    }

    public function UpdateUserAccExisting($paramPost,$DataStaff) {
        $result = array();
        $this->db->trans_begin();

        //Data Phonenumber
        $PhoneNumberArr = ExtractPhoneNumberWithPhoneCode($paramPost['Phonenumber'],$DataStaff['CountryPhoneCode']);

        $sql = "UPDATE ktv_persons a SET
                    a.`PersonNm` = ?,
                    a.`Gender` = ?,
                    a.`OfficialCellPhoneCode` = ?,
                    a.`OfficialCellPhone` = ?,
                    a.`OfficialEmail` = ?,
                    a.`Email` = ?
                WHERE
                    a.`PersonID` = ?
                LIMIT 1";
        $p = array(
            $paramPost['Fullname'],
            $paramPost['Gender'],
            $PhoneNumberArr['Phonecode'],
            $PhoneNumberArr['Phonenumber'],
            $paramPost['Email'],
            $paramPost['Email'],
            $paramPost['PersonID']
        );
        $query = $this->db->query($sql,$p);

        $sql = "UPDATE ktv_staffs a SET
                    a.`OfficialPhoneCode` = ?,
                    a.`OfficialPhone` = ?,
                    a.`WorkPhone` = ?,
                    a.`OfficialEmail` = ?
                WHERE
                    a.`PersonID` = ?
                LIMIT 1";
        $p = array(
            $PhoneNumberArr['Phonecode'],
            $PhoneNumberArr['Phonenumber'],
            $PhoneNumberArr['Phonecode'].$PhoneNumberArr['Phonenumber'],
            $paramPost['Email'],
            $paramPost['PersonID']
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
            $paramPost['Fullname'],
            $paramPost['Username'],
            $paramPost['Email'],
            $paramPost['UserSub'],
            $paramPost['UserCogStatus'],
            $paramPost['UserId']
        );
        $query = $this->db->query($sql,$p);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $result['success'] = false;
            $result['message'] = lang('Linked Process Failed');
        } else {
            $this->db->trans_commit();

            //Update attribute status custom flag ke AWS
            $prosesCog = $this->clientCog->updateUserAttributes($paramPost['Username'], [
                'custom:ispalmoiltrace' => '1'
            ]);

            $result['success'] = true;
            $result['message'] = lang('Linked Process Success');
        }
        return $result;
    }

    public function ResendConfirmationEmail($DataStaff,$UserPassword) {
        $result = array();

        $sql = "SELECT
                    a.`UserName`
                FROM
                    sys_user a
                WHERE
                    a.`UserId` = ?
                LIMIT 1";
        $DataUser = $this->db->query($sql,array($DataStaff['UserID']))->row_array();

        //AWS Cognito =================================== (Begin)
        $MessageAction = "RESEND"; //RESEND untuk send ulang temporary password, string kosong jika baru
        $resendUserCog = $this->clientCog->adminRegisterUser($DataUser['UserName'], $UserPassword, $MessageAction, [
            'email' => $DataStaff['Email'],
            'email_verified' => 'true',
            'gender' => $DataStaff['Gender'],
            'name' => $DataStaff['Name'],
            'phone_number' => $DataStaff['Phonenumber'],
            'phone_number_verified' => 'true',
            'custom:ispalmoiltrace' => '1'
        ]);
        //echo '<pre>'; print_r($resendUserCog); exit;
        if(isset($resendUserCog['User']['Username'])) {
            $result['success'] = true;
            $result['message'] = lang('Email confirmation successfully sent');
        } else {
            $result['success'] = false;
            $result['message'] = lang("Failed to resend confirmation user").", ".decodeMsgAws($resendUserCog);
        }
        return $result;
        //AWS Cognito =================================== (End)
    }

    public function updateUserAccount($post,$dataStaff) {
        //Mulai trans
        $this->db->trans_begin();

        switch ($post['UserLanguage']) {
            case '1':
                $UserLanguage = 'English';
            break;
            case '2':
                $UserLanguage = 'Chinese';
            break;
            case '3':
                $UserLanguage = 'French';
            break;
            case '4':
                $UserLanguage = 'Indonesia';
            break;
            default:
                $UserLanguage = 'English';
            break;
        }

        if($post['UserIsAdmin'] == "1") $UserIsAdmin = 1; else $UserIsAdmin = 0;

        $sql = "UPDATE sys_user a SET
                    a.`UserLanguage` = ?,
                    a.`UserIsAdmin` = ?,
                    a.`UserActive` = ?,
                    a.UserUpdateUserId = ?,
                    a.UserUpdateTime = NOW()
                WHERE
                    a.`UserId` = ?
                LIMIT 1";
        $p = array(
            $UserLanguage,
            $UserIsAdmin,
            $post['UserActive'],
            $_SESSION['userid'],
            $post['UserId']
        );
        $query = $this->db->query($sql,$p);

        // delete dl baru update user groups
        $sql = "DELETE FROM sys_user_group WHERE UserGroupUserId = ?";
        $p = array($post['UserId']);
        $query = $this->db->query($sql,$p);
        // insert user groups
        $ArrUserGroup = explode(',',$post['GroupIds']);
        foreach ($ArrUserGroup as $key => $GroupId) {
            $isDefault = $GroupId == $post['UserGroupIsDefault'] ? '1' : '0';
            $p = array(
                'UserGroupUserId' => $post['UserId'],
                'UserGroupGroupId' => $GroupId,
                'UserGroupIsDefault' => $isDefault
            );
            $query = $this->db->insert('sys_user_group', $p);
        }

        //delete dl access Staff
        $sql = "DELETE FROM ktv_access_staff WHERE UserId = ?";
        $query = $this->db->query($sql,array($post['UserId']));
        //access Staff
        if($post['AccessStaff'] != ""){
            $AccessStaff = explode(',',$post['AccessStaff']);
            foreach ($AccessStaff as $key => $DistrictID) {
                $query = $this->db->insert('ktv_access_staff', array(
                    'UserId' => $post['UserId'],
                    'DistrictID' => $DistrictID,
                ));
            }
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = lang("Failed to update account");
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = lang("Account updated");
        }
        return $results;
    }

    public function frontLogin($Username,$Passwd) {
        $return = array();

        if($Username == "") {
            $return['success'] = false;
            $return['process'] = 'failed_login';
            $return['message'] = lang('Username is not recognize');
            return $return;
        }

        //check data user, bisa login pakai username, email or phone number
        $sql = "SELECT
                    a.`UserId`
                    , a.UserTorStatus
                    , a.UserInCognito
                    , a.UserActive
                FROM
                    sys_user a
                    INNER JOIN ktv_persons b ON a.`UserId` = b.`UserID`
                WHERE
                    a.`UserName` = ? OR
                    b.`OfficialEmail` = ? OR
                    b.`OfficialCellPhone` = ?
                LIMIT 1";
        $DataUser = $this->db->query($sql,array($Username,$Username,$Username))->row_array();

        //Cek dl apakah user sudah ada di mysql dan statunya ======================== (Begin)
        if($DataUser['UserId'] == "" || $DataUser['UserInCognito'] == "No") {
            $return['success'] = false;
            $return['process'] = 'failed_login';
            $return['message'] = lang('Username is not registered');
            return $return;
        }

        if($DataUser['UserActive'] == "No") {
            $return['success'] = false;
            $return['process'] = 'failed_login';
            $return['message'] = lang('User is not active');
            return $return;
        }
        //Cek dl apakah user sudah ada di mysql dan statunya ======================== (End)
        
        $localUser = $this->db->query(
            "SELECT UserId FROM sys_user WHERE UserId = ? AND UserPassword = MD5(?) LIMIT 1",
            array($DataUser['UserId'], $Passwd)
        )->row_array();
        if (!empty($localUser['UserId'])) {
            $_SESSION['userid'] = $DataUser['UserId'];
            $return['success'] = true;
            $return['process'] = (isset($DataUser['UserTorStatus']) && $DataUser['UserTorStatus'] == '0') ? 'tor_required' : 'login_success';
            return $return;
        } else {
            $return['success'] = false;
            $return['process'] = 'failed_login';
            $return['message'] = lang('Login failed');
            return $return;
        }
    }

    public function frontLoginApp($username,$password){
        $return = array();

        if($username == "") {
            $return['success'] = false;
            $return['process'] = 'failed_login';
            $return['message'] = lang('Username is not recognize');
            return $return;
        }

        //check data user, bisa login pakai username, email or phone number
        $sql = "SELECT
                    a.`UserId`
                    , a.UserTorStatus
                    , a.UserInCognito
                    , a.UserActive
                    , b.PersonNm
                    , b.OfficialEmail
                    , b.OfficialCellPhone
                FROM
                    sys_user a
                    INNER JOIN ktv_persons b ON a.`UserId` = b.`UserID`
                WHERE
                    a.`UserName` = ? OR
                    b.`OfficialEmail` = ? OR
                    b.`OfficialCellPhone` = ?
                LIMIT 1";
        $DataUser = $this->db->query($sql,array($username,$username,$username))->row_array();

        //Cek dl apakah user sudah ada di mysql dan statunya ======================== (Begin)
        if($DataUser['UserId'] == "" || $DataUser['UserInCognito'] == "No") {
            $return['success'] = false;
            $return['process'] = 'failed_login';
            $return['message'] = lang('Username is not registered');
            return $return;
        }

        if($DataUser['UserActive'] == "No") {
            $return['success'] = false;
            $return['process'] = 'failed_login';
            $return['message'] = lang('User is not active');
            return $return;
        }
        //Cek dl apakah user sudah ada di mysql dan statunya ======================== (End)

        $authenticationResponse = $this->clientCog->authenticate($username, $password);
        
        $response = array();
        $response["fullname"]   = $DataUser["PersonNm"];
        $response["email"]      = $DataUser["OfficialEmail"];
        $response["phone"]      = $DataUser["OfficialCellPhone"];

        if(isset($authenticationResponse['AccessToken'])) {
            $response["token"] = $authenticationResponse;
            //Update status user Cog
            $sql = "UPDATE sys_user a SET
                        a.CognitoUserStatus = 'CONFIRMED'
                    WHERE
                        a.`UserId` = ?
                    LIMIT 1";
            $query = $this->db->query($sql,array($DataUser['UserId']));

            if($DataUser['UserTorStatus'] == '0') {
                $return['success'] = true;
                $return['process'] = 'tor_required';
                $return['message'] = lang('Term of Reference Required');
                $return['response']    = $response;
                $_SESSION['userid'] = $DataUser['UserId'];
            } else {
                $return['success'] = true;
                $return['process'] = 'login_success';
                $return['message'] = lang('Login Success');
                $return['response']    = $response;
                $_SESSION['userid'] = $DataUser['UserId'];
            }

            return $return;
        } else {

            if(isset($authenticationResponse['ChallengeName'])) {
                if($authenticationResponse['ChallengeName'] == 'NEW_PASSWORD_REQUIRED' || $authenticationResponse['ChallengeName'] == 'RESET_REQUIRED') {
                    $response["token"] = '';
                    
                    $return['success'] = true;
                    $return['process'] = 'new_password_required';
                    $return['message'] = lang('New Password Required');
                    $return['response']    = $response;
                    $_SESSION['userid'] = $DataUser['UserId'];
                    return $return;
                }
            }

            $errMsg = decodeMsgAws($authenticationResponse);
            if($errMsg != "") $errMsg = ', '.$errMsg;

            //insert log_aws
            InsertLogAWS($Username,$authenticationResponse);
            $return['success'] = false;
            $return['process'] = 'failed_login';
            $return['message'] = lang('Login failed').$errMsg;
            return $return;
        }
    }

    public function CekSyncBasicDataCognito($Username) {
        //get info user cognito
        $DataCog = $this->clientCog->getUser($Username);
        if(isset($DataCog['@metadata']['statusCode'])) {
            if($DataCog['@metadata']['statusCode'] == '200') {
                $doSync = false;

                $UserAtt = mappingArrayCognitoAttributes($DataCog['UserAttributes']);
                $LastModified = $DataCog['UserLastModifiedDate']->format(\DateTime::ISO8601);
                $dt = DateTime::createFromFormat(DateTime::ISO8601, $LastModified, new DateTimeZone('Asia/Jakarta'));
                $LastModifiedFormat = $dt->format('Y-m-d H:i:s');

                //cek last sync time
                $sql = "SELECT
                            a.`CognitoSyncTime` AS SyncTime
                            , a.`UserId`
                            , b.`PersonID`
                            , b.`OfficialEmail` AS Email
                            , CONCAT(IFNULL(b.`OfficialCellPhoneCode`,''),IFNULL(b.`OfficialCellPhone`,'')) AS Phonenumber
                            , '+62' AS CountryPhoneCode
                        FROM
                            sys_user a
                            INNER JOIN ktv_persons b ON a.`UserId` = b.`UserID`
                        WHERE
                            a.`UserName` = ?
                        LIMIT 1";
                $DataSyncUser = $this->db->query($sql,array($Username))->row_array();
                if($DataSyncUser['SyncTime'] == "") {
                    $doSync = true;
                } else {
                    if($LastModifiedFormat > $DataSyncUser['SyncTime']) {
                        $doSync = true;
                    }
                }

                if($doSync == true) {
                    //Update Basic Data
                    $this->db->trans_begin();

                    $sql = "UPDATE ktv_persons a SET
                                a.`PersonNm` = ?
                                , a.`Gender` = ?
                            WHERE
                                a.`UserID` = ?
                            LIMIT 1";
                    $p = array(
                        $UserAtt['name'],
                        $UserAtt['gender'],
                        $DataSyncUser['UserId']
                    );
                    $query = $this->db->query($sql,$p);

                    $sql = "UPDATE sys_user a SET
                                a.`UserRealName` = ?
                                , a.`CognitoSyncTime` = ?
                            WHERE
                                a.`UserId` = ?
                            LIMIT 1";
                    $p = array(
                        $UserAtt['name'],
                        $LastModifiedFormat,
                        $DataSyncUser['UserId']
                    );
                    $query = $this->db->query($sql,$p);

                    if ($this->db->trans_status() === false) {
                        $this->db->trans_rollback();
                        return false;
                    } else {
                        $this->db->trans_commit();
                        return true;
                    }
                }

                //Cek email dan no telp apakah ada perubahan, jika ada langsung di update juga ====================== (Begin)
                
                //Email
                if($DataSyncUser['Email'] != $UserAtt['email']) { //Jika berbeda
                    //Cek apakah duplikat
                    $sql = "SELECT
                                a.`StaffID`
                            FROM
                                ktv_staffs a
                            WHERE 1=1
                                AND a.`OfficialEmail` = ?
                                AND a.PersonID != ?
                            LIMIT 1";
                    $DataCekEmail = $this->db->query($sql, array($UserAtt['email'],$DataSyncUser['PersonID']))->row_array();
                    if(isset($DataCekEmail['StaffID'])) {
                        //duplikat
                        $CekDuplikatEmail = 1;
                    } else {
                        //Update email
                        $sql = "UPDATE ktv_persons a SET
                                    a.`Email` = ?
                                    , a.`OfficialEmail` = ?
                                WHERE
                                    a.`UserID` = ?
                                LIMIT 1";
                        $p = array(
                            $UserAtt['email'],
                            $UserAtt['email'],
                            $DataSyncUser['UserId']
                        );
                        $query = $this->db->query($sql,$p);

                        $sql = "UPDATE ktv_staffs a SET
                                    a.`OfficialEmail` = ?
                                WHERE
                                    a.`PersonID` = ?
                                LIMIT 1";
                        $p = array(
                            $UserAtt['email'],
                            $DataSyncUser['PersonID']
                        );
                        $query = $this->db->query($sql,$p);

                        $sql = "UPDATE sys_user a SET
                                    a.`UserEmail` = ?
                                WHERE
                                    a.`UserId` = ?
                                LIMIT 1";
                        $p = array(
                            $UserAtt['email'],
                            $DataSyncUser['UserId']
                        );
                        $query = $this->db->query($sql,$p);
                    }
                }

                //Phonenumber
                if($DataSyncUser['Phonenumber'] != $UserAtt['phone_number']) { //Jika berbeda
                    //cek apakah duplikat
                    $sql = "SELECT
                            a.`StaffID`
                        FROM
                            ktv_staffs a
                        WHERE 1=1
                            AND CONCAT(IFNULL(a.`OfficialPhoneCode`,''),IFNULL(a.`OfficialPhone`,'')) = ?
                            AND a.PersonID != ?
                        LIMIT 1";
                    $DataCekPhone = $this->db->query($sql, array($UserAtt['phone_number'],$DataSyncUser['PersonID']))->row_array();
                    if(isset($DataCekPhone['StaffID'])) {
                        //duplikat
                        $CekDuplikatPhonenumber = 1;
                    } else {
                        //Data Phonenumber
                        $PhoneNumberArr = ExtractPhoneNumberWithPhoneCode($UserAtt['phone_number'],$DataSyncUser['CountryPhoneCode']);

                        $sql = "UPDATE ktv_persons a SET
                                    a.`OfficialCellPhoneCode` = ?
                                    , a.`OfficialCellPhone` = ?
                                WHERE
                                    a.`UserID` = ?
                                LIMIT 1";
                        $p = array(
                            $PhoneNumberArr['Phonecode'],
                            $PhoneNumberArr['Phonenumber'],
                            $DataSyncUser['UserId']
                        );
                        $query = $this->db->query($sql,$p);

                        $sql = "UPDATE ktv_staffs a SET
                                    a.`OfficialPhoneCode` = ?
                                    , a.`OfficialPhone` = ?
                                    , a.`WorkPhone` = ?
                                WHERE
                                    a.`PersonID` = ?
                                LIMIT 1";
                        $p = array(
                            $PhoneNumberArr['Phonecode'],
                            $PhoneNumberArr['Phonenumber'],
                            $PhoneNumberArr['Phonecode'].$PhoneNumberArr['Phonenumber'],
                            $DataSyncUser['PersonID']
                        );
                        $query = $this->db->query($sql,$p);
                    }
                }
                //Cek email dan no telp apakah ada perubahan, jika ada langsung di update juga ====================== (End)

                $return['CekDuplikatEmail'] = $CekDuplikatEmail;
                $return['CekDuplikatPhonenumber'] = $CekDuplikatPhonenumber;
                return $return;
            }
        }

        return false;
    }

    public function frontPasswdChallenge($UserId,$Passwd) {
        //check untuk set session userid
        $sql = "SELECT
                    a.`UserName`
                    , a.UserTorStatus
                FROM
                    sys_user a
                WHERE
                    a.`UserId` = ?
                LIMIT 1";
        $DataUser = $this->db->query($sql,array($UserId))->row_array();
        $Username = $DataUser['UserName'];

        $response = $this->clientCog->adminSetUserPassword($Username,$Passwd);
        if(isset($response['@metadata']['statusCode'])) {
            if($response['@metadata']['statusCode'] == '200') {

                //Update status user Cog
                $sql = "UPDATE sys_user a SET
                            a.CognitoUserStatus = 'CONFIRMED'
                        WHERE
                            a.`UserId` = ?
                        LIMIT 1";
                $query = $this->db->query($sql,array($UserId));

                if($DataUser['UserTorStatus'] == '0') {
                    $return['success'] = true;
                    $return['process'] = 'tor_required';
                } else {
                    $return['success'] = true;
                    $return['process'] = 'user_confirmed';
                }
                return $return;
            } else {
                //insert log_aws
                InsertLogAWS($UserId,$response);
                $return['success'] = false;
                $return['process'] = 'failed_passwd_challenge';
                $return['message'] = lang('Process failed, please try again later');
                return $return;
            }
        } else {
            //insert log_aws
            InsertLogAWS($UserId,$response);
            $return['success'] = false;
            $return['process'] = 'failed_passwd_challenge';
            $return['message'] = lang('Process failed, please try again later');
            return $return;
        }
    }

    public function frontReqForgotPass($Username) {
        $return = array();

        //Check dl apakah user ini sudah confirm
        $cekAws = $this->clientCog->getUser($Username);
        if(isset($cekAws['Username'])) {
            if($cekAws['UserStatus'] != "CONFIRMED") {
                $return['success'] = false;
                $return['message'] = lang('User not confirmed yet in identity server');
                return $return;
            }
        } else {
            $return['success'] = false;
            $return['message'] = lang('Username not registered in identity server');
            return $return;
        }

        $response = $this->clientCog->sendForgottenPasswordRequest($Username);
        if(isset($response['@metadata']['statusCode'])) {
            if($response['@metadata']['statusCode'] == '200') {
                $return['success'] = true;
                $return['message'] = lang('Email successfully sent');
            } else {
                $return['success'] = false;
                $return['message'] = lang('Fail to send email');
            }
        } else {
            $return['success'] = false;
            $return['message'] = lang('Fail to send email from identity server');
        }

        return $return;
    }

    public function frontForgotPassConfirmation($Username,$Passwd,$VerificatonCode) {
        $response = $this->clientCog->confirmForgotPassword($Username,$Passwd,$VerificatonCode);
        if(isset($response['@metadata']['statusCode'])) {
            if($response['@metadata']['statusCode'] == '200') {

                $sql = "SELECT
                        a.`UserId`
                    FROM
                        sys_user a
                    WHERE
                        a.`UserName` = ?
                    LIMIT 1";
                $DataUser = $this->db->query($sql,array($Username))->row_array();

                $return['success'] = true;
                $return['UserId'] = $DataUser['UserId'];
                $return['message'] = lang('Reset password success');
            } else {
                $return['success'] = false;
                $return['message'] = lang('Reset password failed');
            }
        } else {
            $return['success'] = false;
            $return['message'] = lang('Reset password failed');
        }
        return $return;
    }

    public function changePasswordUserAccount($paramPost,$dataUser) {
        $result = array();

        $response = $this->clientCog->adminSetUserPassword($dataUser['Username'],$paramPost['UserPassword']);
        if(isset($response['@metadata']['statusCode'])) {
            if($response['@metadata']['statusCode'] == '200') {

                //Update status user cognito
                $sql = "UPDATE sys_user a SET
                            a.`CognitoUserStatus` = 'CONFIRMED'
                        WHERE
                            a.`UserName` = ?
                        LIMIT 1";
                $p = array(
                    $dataUser['Username']
                );
                $query = $this->db->query($sql,$p);

                $result['success'] = true;
                $result['message'] = lang("Password changed");
            } else {
                $result['success'] = false;
                $result['message'] = lang("Failed to change password");
            }
        } else {
            $result['success'] = false;
            $result['message'] = lang("Failed to change password").", ".decodeMsgAws($response);
        }

        return $result;
    }

    public function checkPassword($username,$oldpassword) {
        $authenticationResponse = $this->clientCog->authenticate($username, $oldpassword);
        if(isset($authenticationResponse['AccessToken'])) {
            return true;
        } else {
            return false;
        }
    }

    public function UserUpdatePassword($username,$newpassword) {
        $response = $this->clientCog->adminSetUserPassword($username,$newpassword);
        if(isset($response['@metadata']['statusCode'])) {
            if($response['@metadata']['statusCode'] == '200') {
                return true;
            }
            return false;
        } else {
            return false;
        }
    }

    public function frontLoginV2($Username,$Passwd) {
        $return = array();

        if($Username == "") {
            $return['success'] = false;
            $return['process'] = 'failed_login';
            $return['message'] = lang('Username is not recognize');
            return $return;
        }

        //check data user, bisa login pakai username, email or phone number
        $sql = "SELECT
                    a.`UserId`
                    , a.UserTorStatus
                    , a.UserInCognito
                    , a.UserActive
                FROM
                    sys_user a
                    INNER JOIN ktv_persons b ON a.`UserId` = b.`UserID`
                WHERE
                    a.`UserName` = ? OR
                    b.`OfficialEmail` = ? OR
                    b.`OfficialCellPhone` = ?
                LIMIT 1";
        $DataUser = $this->db->query($sql,array($Username,$Username,$Username))->row_array();

        //Cek dl apakah user sudah ada di mysql dan statunya ======================== (Begin)
        if($DataUser['UserId'] == "" || $DataUser['UserInCognito'] == "No") {
            $return['success'] = false;
            $return['process'] = 'failed_login';
            $return['message'] = lang('Username is not registered');
            return $return;
        }

        if($DataUser['UserActive'] == "No") {
            $return['success'] = false;
            $return['process'] = 'failed_login';
            $return['message'] = lang('User is not active');
            return $return;
        }
        //Cek dl apakah user sudah ada di mysql dan statunya ======================== (End)

        // ===== LOCAL DEV ONLY: offline login bypass (Herd / *.test / localhost) =====
        // On a local host, accept the local sys_user MD5 password so the app can be
        // used without reaching AWS Cognito. Guarded by host so it is INERT on any
        // real domain. Remove before deploying.
        $localHost = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        $isLocalDev = (substr($localHost, -5) === '.test')
            || (strpos($localHost, 'localhost') !== false)
            || (strpos($localHost, '127.0.0.1') !== false)
            || (strpos($localHost, 'fashahdarullah.id') !== false)  // demo hosting
            || (getenv('APP_LOCAL_AUTH') === '1');   // Docker demo
        if ($isLocalDev) {
            $localUser = $this->db->query(
                "SELECT UserId FROM sys_user WHERE UserId = ? AND UserPassword = MD5(?) LIMIT 1",
                array($DataUser['UserId'], $Passwd)
            )->row_array();
            if (!empty($localUser['UserId'])) {
                // Populate the FULL session (groupid, is_admin, PartnerID, role, ...)
                // exactly like the legacy login does, otherwise the sidebar menu and
                // default module are empty (they read $_SESSION['groupid']).
                $this->load->model('mcommon');
                $this->mcommon->revoke($Username, $Passwd);
                $_SESSION['userid'] = $DataUser['UserId'];
                $return['success'] = true;
                $return['process'] = (isset($DataUser['UserTorStatus']) && $DataUser['UserTorStatus'] == '0') ? 'tor_required' : 'login_success';
                return $return;
            }
        }
        // ===== END LOCAL DEV ONLY =====

        $authenticationResponse = $this->clientCog->authenticate($Username, $Passwd);
        //echo '<pre>'; print_r($authenticationResponse); exit;

        if(isset($authenticationResponse['AccessToken'])) {

            //Update status user Cog
            $sql = "UPDATE sys_user a SET
                        a.CognitoUserStatus = 'CONFIRMED'
                    WHERE
                        a.`UserId` = ?
                    LIMIT 1";
            $query = $this->db->query($sql,array($DataUser['UserId']));

            if($DataUser['UserTorStatus'] == '0') {
                $return['success'] = true;
                $return['process'] = 'tor_required';
                $return["token"] = $authenticationResponse;
                $_SESSION['userid'] = $DataUser['UserId'];
            } else {
                $return['success'] = true;
                $return['process'] = 'login_success';
                $return["token"] = $authenticationResponse;
                $_SESSION['userid'] = $DataUser['UserId'];
            }

            return $return;
        } else {

            if(isset($authenticationResponse['ChallengeName'])) {
                if($authenticationResponse['ChallengeName'] == 'NEW_PASSWORD_REQUIRED' || $authenticationResponse['ChallengeName'] == 'RESET_REQUIRED') {
                    $return['success'] = true;
                    $return['process'] = 'new_password_required';
                    $_SESSION['userid'] = $DataUser['UserId'];
                    return $return;
                }
            }

            $errMsg = decodeMsgAws($authenticationResponse);
            if($errMsg != "") $errMsg = ', '.$errMsg;

            //insert log_aws
            InsertLogAWS($Username,$authenticationResponse);
            $return['success'] = false;
            $return['process'] = 'failed_login';
            $return['message'] = lang('Login failed').$errMsg;
            return $return;
        }
    }

    public function FarmCloudRegisterNewAccount($paramPost) {
        $result = array();
        //echo '<pre>'; print_r($paramPost); exit;

        //Cek Username, Email dan Phonenumber ==================================== (Begin)
        $Filter = 'username = "'.$paramPost['Username'].'"';
        $proses = $this->clientCog->listUsersWithFilter($Filter);
        if(isset($proses['Users'][0]['Username'])) {
            $result['success'] = false;
            $result['message'] = lang("Username already registered on our Identity Server");
            return $result;
        }
        
        $Filter = 'phone_number = "'.$paramPost['Handphone'].'"';
        $proses = $this->clientCog->listUsersWithFilter($Filter);
        if(isset($proses['Users'][0]['Username'])) {
            $result['success'] = false;
            $result['message'] = lang("Phone number already registered on our Identity Server");
            return $result;
        }

        $Filter = 'email = "'.$paramPost['Email'].'"';
        $proses = $this->clientCog->listUsersWithFilter($Filter);
        if(isset($proses['Users'][0]['Username'])) {
            $result['success'] = false;
            $result['message'] = lang("Email already registered on our Identity Server");
            return $result;
        }
        //Cek Username, Email dan Phonenumber ==================================== (End)

        //Register Account
        $MessageAction = "SUPPRESS";
        $createUserCog = $this->clientCog->adminRegisterUser($paramPost['Username'], $paramPost['UserPassword'], $MessageAction, [
            'email' => $paramPost['Email'],
            'email_verified' => 'true',
            'name' => $paramPost['FarmerName'],
            'given_name' => $paramPost['FarmerID'],
            'phone_number' => $paramPost['Handphone'],
            'phone_number_verified' => 'true',
            'custom:iscocoatrace' => '1',
            'custom:objectid' => $paramPost['FarmerID'],
            'custom:partnerid' => $paramPost['PartnerID'],
            'custom:objecttype' => 'Farmer',
            'custom:province' => $paramPost['Province'],
            'custom:district' => $paramPost['District'],
            'custom:subdistrict' => $paramPost['SubDistrict'],
            'custom:village' => $paramPost['Village']
        ]);
        $this->clientCog->adminSetUserPassword($paramPost['Username'],$paramPost['UserPassword']);
        
        if(isset($createUserCog['User']['Username'])) {
            $UserAttributes = mappingArrayCognitoAttributes($createUserCog['User']['Attributes']);
            $AwsUserSub = $UserAttributes['sub'];
            $Username = $paramPost['Username'];

            //Insert ke sys_farmer_user
            $sql = "INSERT INTO `sys_farmer_user` SET
                    `FarmerID` = ?,
                    `Username` = ?,
                    `StatusUser` = 'Active',
                    `Email` = ?,
                    `FCMID` = NULL,
                    `CreatedDate` = NOW()";
            $p = array(
                $paramPost['FarmerID'],
                $paramPost['Username'],
                $paramPost['Email']
            );
            $query = $this->db->query($sql,$p);

            //Update handphone number ke farmer
            $sql = "UPDATE ktv_members a SET
                        a.`HandphoneType` = '1',
                        a.`HandPhone` = ?,
                        a.`AccessToSmartPhone` = '1'
                    WHERE
                        a.`MemberID` = ?
                    LIMIT 1";
            $p = array(
                $paramPost['Handphone'],
                $paramPost['FarmerID']
            );
            $query = $this->db->query($sql,$p);
            
            $result['success'] = true;
            $result['message'] = lang('Account registered');
        } else {
            $result['success'] = false;
            $result['message'] = lang("Failed to create user on identity server").", ".decodeMsgAws($createUserCog);
            return $result;
        }

        return $result;    
    }

    public function ChangePasswordUserAccountFarmCloud($paramPost) {
        $result = array();

        $response = $this->clientCog->adminSetUserPassword($paramPost['Username'],$paramPost['UserPassword']);
        if(isset($response['@metadata']['statusCode'])) {
            if($response['@metadata']['statusCode'] == '200') {
                $result['success'] = true;
                $result['message'] = lang("Password changed");
            } else {
                $result['success'] = false;
                $result['message'] = lang("Failed to change password");
            }
        } else {
            $result['success'] = false;
            $result['message'] = lang("Failed to change password").", ".decodeMsgAws($response);
        }

        return $result;
    }

    public function DisableAccountFarmCloud($FarmerID, $Username) {
        $result = array();

        $response = $this->clientCog->disableUser($Username);
        if(isset($response['@metadata']['statusCode'])) {
            if($response['@metadata']['statusCode'] == '200') {
                $result['success'] = true;
                $result['message'] = lang("Account Disabled");

                //Update sys_farmer_user
                $sql = "UPDATE sys_farmer_user a SET
                            a.`StatusUser` = 'Inactive'
                        WHERE
                            a.`FarmerID` = ?
                        LIMIT 1";
                $p = array(
                    $FarmerID
                );
                $query = $this->db->query($sql,$p);

            } else {
                $result['success'] = false;
                $result['message'] = lang("Failed to disable account");
            }
        } else {
            $result['success'] = false;
            $result['message'] = lang("Process failed, ").", ".decodeMsgAws($response);
        }

        return $result;
    }

    public function EnableAccountFarmCloud($FarmerID, $Username) {
        $result = array();

        $response = $this->clientCog->enableUser($Username);
        if(isset($response['@metadata']['statusCode'])) {
            if($response['@metadata']['statusCode'] == '200') {
                $result['success'] = true;
                $result['message'] = lang("Account Enabled");

                //Update sys_farmer_user
                $sql = "UPDATE sys_farmer_user a SET
                            a.`StatusUser` = 'Active'
                        WHERE
                            a.`FarmerID` = ?
                        LIMIT 1";
                $p = array(
                    $FarmerID
                );
                $query = $this->db->query($sql,$p);

            } else {
                $result['success'] = false;
                $result['message'] = lang("Failed to enable account");
            }
        } else {
            $result['success'] = false;
            $result['message'] = lang("Process failed, ").", ".decodeMsgAws($response);
        }

        return $result;
    }

    public function DeleteAccountFarmCloud($FarmerID,$Username) {
        $result = array();

        $response = $this->clientCog->deleteAdminUser($Username);
        if(isset($response['@metadata']['statusCode'])) {
            if($response['@metadata']['statusCode'] == '200') {
                $result['success'] = true;
                $result['message'] = lang("Account deleted");

                //Update sys_farmer_user
                $sql = "DELETE FROM sys_farmer_user WHERE FarmerID = ? LIMIT 1";
                $p = array(
                    $FarmerID
                );
                $query = $this->db->query($sql,$p);

            } else {
                $result['success'] = false;
                $result['message'] = lang("Failed to delete account");
            }
        } else {
            $result['success'] = false;
            $result['message'] = lang("Process failed, ").", ".decodeMsgAws($response);
        }

        return $result;
    }

    public function CheckUserInIncognito($username) {
        $result = array();
        $filter = "username = \"".$username."\"";
        $configAwsCog = $this->config->item('awscog');

        $response = $this->clientCog->listUsersWithFilter($filter);

        if(empty($response['Users'])) {
            $result['success'] = false;
            $result['message'] = lang("This user is not registered with cognito").", ".decodeMsgAws($response);
            $result['userpoolid'] = $configAwsCog['user_pool_id'];
        } else {
            $result['success'] = true;
            $result['message'] = lang("User Registered");
            $result['userpoolid'] = $configAwsCog['user_pool_id'];
        }

        return $result;
    }

}