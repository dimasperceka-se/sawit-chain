<?php
class Muser extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function readData(){
        $sql = "select UserId,UserName, UserRealName,UserActive from ci_user";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    function createData($name,$description,$unitid){
        $sql = "INSERT INTO ci_user(UserName,UserDescription,UserUnitId) VALUES ('$name','$description','$unitid')";
        $query = $this->db->query($sql);
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;

    }

    function updateData($id, $name,$description,$unitid){
        $sql = "UPDATE ci_user SET UserName='$name',UserDescription='$description',UserUnitId='$unitid'
            WHERE UserId='$id'";
        $delete = $this->db->query($sql);
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;

    }


    function deleteData($id){
        $sql = "DELETE FROM ci_user WHERE UserId=$id";
        $query = $this->db->query($sql);
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;

    }

    function readUnit(){
        $sql = "select UnitId as id,UnitName as label from ci_unit order by UnitName";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function getProfile($username)
    {
        $this->db->select('UserName,UserRealName,UserLanguage,UserNotification,UserUnitId,UserActive');
        $query = $this->db->get_where('sys_user', array('UserName' => $username), 1);
        return $query->row_array(0);
    }

    public function updateProfile($data, $username)
    {
        $_SESSION['language'] = $data['UserLanguage'];
        $fillable = array('UserLanguage', 'UserNotification');
        foreach ($data as $key => $value) {
            if (!in_array($key, $fillable)) {
                unset($data[$key]);
            }
        }
        return $this->db->update('sys_user', $data, array('UserName' => $username));

    }

    public function checkPassword($password, $username)
    {
        $query = $this->db->get_where('sys_user', array('UserName' => $username, 'UserPassword' => md5($password)));
        if ($query->num_rows() > 0) {
            return true;
        }
        return false;
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

    public function updatePassword($password, $username)
    {
        //ini_set('display_errors',true); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        $prosesAll = true;

        //get data user
        $sql="SELECT
                a.UserExtId,
                a.UserExtRoleId,
                a.UserExtGroupId,
                a.UserId,
                b.`PersonNm`,
                b.`OfficialEmail`
            FROM
                sys_user a
                INNER JOIN ktv_persons b ON a.`UserId` = b.`UserID`
            WHERE
                a.UserName = ?
            LIMIT 1";
        $query = $this->db->query($sql,array($username));
        $dataUser = $query->row_array();

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
        $query = $this->db->query($sql,array($dataUser['UserId']));
        $dataOrgUnit = $query->result_array();

        $tmpJson = array();
        foreach ($dataOrgUnit as $key => $value) {
            if($value['uid'] != ""){
                $tmpJson[]['id'] = $value['uid'];
            }
        }
        $jsonOrgUnit = json_encode($tmpJson);
        //org unit (end)

        //update password ke dhis (begin)
        if($dataUser['UserExtId'] != ""){
            $tmpName   = explode(" ", $dataUser['PersonNm']);
            $firstName = $tmpName[0];
            unset($tmpName[0]);
            $lastName = implode(" ", $tmpName);

            //User Group DHIS ============================= (Begin)
            if($dataUser['UserExtGroupId'] != ""){
                $AppGroupUidRaw = $dataUser['UserExtGroupId'];

                $TmpAppGroupUid = explode(',',$dataUser['UserExtGroupId']);
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
            if($dataUser['UserExtRoleId'] != ""){
                $AppRoleUidRaw = $dataUser['UserExtRoleId'];

                $TmpAppRoleUid = explode(',',$dataUser['UserExtRoleId']);
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
                    "username": "'.$username.'",
                    "password": "'.$password.'",
                    "userRoles": '.$JsonAppRoleUid.'
                },
                "organisationUnits": '.$jsonOrgUnit.',
                "userGroups": '.$JsonAppGroupUid.'
            }';

            $url = $this->config->item('dhis_url').'api/users/'.$dataUser['UserExtId'];
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

            if($curlresult['status'] != "SUCCESS") {
                $prosesAll = false;
            }
        }
        //update password ke dhis (end)

        if($prosesAll == true){
            $sql="UPDATE sys_user SET
                UserPassword = ?,
                UserExtPassword = ?
            WHERE
                UserName = ?
            LIMIT 1";
            $proses = $this->db->query($sql,array(md5($password),md5($password),$username));
            //$proses = $this->db->update('sys_user', array('UserPassword' => md5($password)), array('UserName' => $username));
            if($proses == false) $prosesAll = false;
        }

        return $prosesAll;
    }

    public function writeLogAccess($type,$UserId,$attempProses){
        $sql = "INSERT INTO `sys_log_access` SET
              `type` = ?,
              `UserID` = ?,
              `SessionIP` = ?,
              `UserAgent` = ?,
              `AttempProcess` = ?";
        $p = array(
            $type,
            $UserId,
            $this->input->ip_address(),
            $_SERVER['HTTP_USER_AGENT'],
            $attempProses
        );
        return $this->db->query($sql, $p);
    }

    public function writeLogChangePass($UserId,$newPass){
        $sql = "INSERT INTO `sys_log_account` SET
                  `UserId` = ?,
                  `Passwd` = ?";
        $p = array(
            $_SESSION['userid'],
            $newPass
        );
        return $this->db->query($sql, $p);
    }

    public function sendEmailConfirmChangePass($userid){
        //cek apakah terdaftar
        $sql="SELECT
            *
        FROM
            sys_user a
        WHERE
            a.`UserId` = ?
        LIMIT 1";
        $query = $this->db->query($sql, array($userid));
        $dataUser = $query->row_array();

        $this->load->library('email');
        $this->load->helper('security');

        if ($this->email->valid_email($dataUser['UserEmail']) == false) {
            return false;
        }

        $subject = 'Password Change Confirmation';
        $message = '
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#8d8e90">
    <tr>
        <td><table width="600" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" align="center">
                <tr>
                    <td align="center">&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="10%">&nbsp;</td>
                                <td width="80%" align="left" valign="top">
                                    <b>Hi ' . $dataUser['UserRealName'] . '</b>
                                    <br /><br />
                                    <br />
                                    <font style="font-family: Georgia, \'Times New Roman\', Times, serif; color:#010101; font-size:24px"><strong><em>Your Cocoatrace details</em></strong>
                                    </font><br /><br />
                                    <font style="font-family: Verdana, Geneva, sans-serif; color:#666766; font-size:13px; line-height:21px">
                                    You just change your password on Cocoatrace on '.date('Y-m-d H:i:s').'
                                    <br /><br />

                                    <b>Cocoatrace</b>
                                    </font>
                                </td>
                                <td width="10%">&nbsp;</td>
                            </tr>
                        </table></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
            </table></td>
    </tr>
</table>
      ';

      // Get full html:
        $body =
        '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>' . htmlspecialchars($subject, ENT_QUOTES, $this->email->charset) . '</title>
</head>
<body bgcolor="#8d8e90">
' . $message . '
</body>
</html>';

        $kirim = $this->email->from('support@koltiva.com')->to($dataUser['UserEmail'])->subject($subject)->message($body)->send();
        $this->email->clear();
    }

}
