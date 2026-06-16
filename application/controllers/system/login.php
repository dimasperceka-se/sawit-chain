<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Login extends SS_Controller
{

    public function __construct()
    {
        parent::__construct(0);
        $this->load->library('session');
        $this->load->model('system');
    }

    public function index()
    {
        //reset session
        $_SESSION = array();
        // Request 30-09-20 agar ketika button back diklik tidak langsung ke halaman login
        if (!empty($_SESSION['userid'])) {
            redirect(base_url(), 'refresh');
        }

        unset($_SESSION['torSes']);

        $data['msg'] = $this->session->flashdata('msg');
        $data['api_url']    = $this->config->item('api');

        //from original
        // $this->load->view('common_header_front');
        // $this->load->view('login_v3', $data);
        // $this->load->view('common_footer_front');

        //modified 27-4-2021

        // $this->load->view('common_header_front_30');
        // $this->load->view('login30', $data);
        // $this->load->view('common_header_front_30');
        $this->load->view('login/login', $data);

    }

    public function tor_view($ViewOnly='no'){
        $data = array();
        $data['ViewOnly'] = $ViewOnly;
        $this->load->view('tor_view', $data);
    }

    public function changepass_first(){
        $data = array();
        $this->load->view('changepass_first', $data);
    }

    public function forgot_pass() {
        $data = array();
        $data['api_url']    = $this->config->item('api');

        //from original
        // $this->load->view('common_header_front');
        // $this->load->view('login/forgot_pass', $data);
        // $this->load->view('common_footer_front');

        //modified 5-5-2021
        // $this->load->view('common_header_front_30');
        // $this->load->view('login/forgot_pass_30', $data);
        // $this->load->view('common_footer_front_30');

        //modified 23-03-2022
        $this->load->view('login/header');
        $this->load->view('login/forgot_pass', $data);
        $this->load->view('login/footer');
    }

    public function forgot_verification_code() {
        if (empty($_SESSION['username'])) {
            redirect('system/login', 'refresh');
        }
            
        $data = array();
        $data['api_url']    = $this->config->item('api');

        $this->load->view('login/header');
        $this->load->view('login/forgot_verification_code', $data);
        $this->load->view('login/footer');
    }

    private function check_database()
    {
        
        //ini_set('display_errors', 'Off');
        
        //  Load the database config file.
        if(file_exists($file_path = APPPATH.'config/database.php'))
        {
            include($file_path);
        }
        
        $config = $db[$active_group];
        
        //  Check database connection if using mysqli driver
        if( $config['dbdriver'] === 'mysqli' )
        {
            $mysqli = new mysqli( $config['hostname'] , $config['username'] , $config['password'] , $config['database'] );
            if( !$mysqli->connect_error )
            {
                return true;
            }
            else{
                return false;
            }
        }
        else
        {
            return false;
        }
    } 

    public function log_in()
    {
        /* cek koneksi */
        if ($this->db->initialize() === FALSE) {
            $msg = 'Server is under maintenance.';
            $this->session->set_flashdata('msg', $msg);
            redirect('https://pr.koltiva.com/maintenance-page/', 'location');
        }

        $this->db->trans_begin();

        $username  = $_POST['username'];
        $password  = $_POST['password'];
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
            , st.MillID
            , st.SmeID
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
            WHERE LOWER(a.UserName)=LOWER(?) AND a.UserPassword=?";
        $user = $this->system->GetSql($sql_login, array($username, md5($password)));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        // echo '<pre>'; print_r($username); echo '</pre>'; exit;
        if (
            strtolower($user[0]['UserName']) == strtolower($username) and
            $user[0]['UserPassword'] == md5($password) and
            $user[0]['UserActive'] == 'Yes'
        ) {
            //berhasil login

            //cek tor
            if($user[0]['UserTorStatus'] == "0"){
                //belum tor
                $_SESSION['torSes'] = $user[0]['UserName'];
                redirect('system/login/tor_view', 'location');
            }else{
                //sudah tor
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
                $_SESSION['MillID']         = $user[0]['MillID'];
                $_SESSION['SMEID']         = $user[0]['SmeID'];
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
              
                //write log login
                $this->system->writeLogAccess('Login',$user[0]['UserId'],'Success');
            }

            //setcookie("username", $user[0]['UserName'], time()+7600);
            //setcookie("password", $password, time()+7600);

        } else {
            //gagal login
            $mod = 'system/login/index/1';

            //write log login
            //cari usernya siapa
            $sql="SELECT UserId FROM sys_user WHERE UserName = ? LIMIT 1";
            $query = $this->db->query($sql,array($username));
            $data = $query->row_array();
            if($data['UserId'] != ""){
                $this->system->writeLogAccess('Login',$data['UserId'],'Invalid Login');
            }
        }

        
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            redirect('https://pr.koltiva.com/maintenance-page/', 'location');
        }else{
            $this->db->trans_commit();
            //redirect ke index

            $msg = '';
            if (empty($user[0]['UserName'])) {
                $msg = "Username Password combination doesn't match."; //
            } elseif ($user['UserActive'] != 'Yes') {
                $msg = "User is not active";
            }
            $this->session->set_flashdata('msg', $msg);
            redirect($mod, 'location');
        }
    }

    public function logout()
    {
        unset($_SESSION['username']);
        unset($_SESSION['realname']);
        unset($_SESSION['userid']);
        unset($_SESSION['groupid']);
        unset($_SESSION['ProjID']);
        unset($_SESSION['unitid']);
        unset($_SESSION['daerah']);
        unset($_SESSION['province']);
        unset($_SESSION['PartnerID']);
        unset($_SESSION['daerah_access']);
        unset($_SESSION['language']);
        unset($_SESSION['official_email']);
        unset($_SESSION['private_email']);
        unset($_SESSION['email']);
        unset($_SESSION['official_phone']);
        unset($_SESSION['private_phone']);
        unset($_SESSION['phone']);
        unset($_SESSION['group']);
        unset($_SESSION['partner']);
        unset($_SESSION['district']);
        unset($_SESSION['Photo_staff']);
        unset($_SESSION['role']);
        unset($_SESSION['filter_by']);
        unset($_SESSION['is_admin']);
        unset($_SESSION['FlagAccess']);
        unset($_SESSION['Gender']);
        unset($_SESSION['userid_beforeswitch']);
        unset($_SESSION['UserAff']);
        unset($_SESSION['SupplychainID']);
        // tambahan 15-1-2020 untuk filter pada grid farmer (dipindah dari localStorage ke session)
        unset($_SESSION['grid_filter']);

        redirect('system/login', 'location');
    }

    public function term_condition(){
        $this->load->view("term_condition");
    }

    public function forgot()
    {
        $result = false;

        //ispost
        if($this->input->post('isPost') == "1"){
            $data['isPost'] = 1;
        }else{
            $data['isPost'] = 0;
        }

        //cek variable $username untuk XSS
        $_POST['username'] = $this->input->post('username', true);

        $this->load->library('form_validation');

        $this->form_validation->set_rules('username', 'Username', 'required|trim');

        if ($this->form_validation->run() == true) {
            $username = $this->input->post('username');

            $user = $this->isUserByUsername($username);

            // if user exist
            if (is_object($user)) {

                //cek apakah sudah setuju tor
                if($user->UserTorStatus == "1"){

                    // if user doesn't have unprocessed request
                    if ($this->isRequested($user) == false) {
                        // start transaction
                        $this->db->trans_start(false);

                        // insert new pass request
                        //$pass   = $this->randomChar(6);
                        $key    = $this->randomChar(30);
                        $result = $this->insertNewPass($user, $key);
                        // send email

                        //$this->db->trans_rollback();
                        
                        if ($result) {
                            $result = @$this->sendAutoNewPassEmail($user, $pass, $key);
                        }

                        if($result) {
                            //insert log
                            $logProcess = $this->system->writeLogAccess('Request Reset Password',$user->UserId,'Success');
                        }

                        // end transactinon
                        if ($result == true) {
                            $this->db->trans_commit();
                            $data['msg'] = "Your request has been sent, please check your email.";
                        } else {
                            $this->db->trans_rollback();
                            $data['msg'] = "Your request can not be processed right now, please try again later.";
                        }
                    } else {
                        $data['msg'] = "Your request is being processed, you can not request another password!";
                    }

                }else{
                    $data['msg'] = "Your account is not active yet";
                }

            } else {
                $data['msg'] = "Username : {$username} doesn't exist";
            }
        }

        $data['type'] = ($result == true) ? 'success' : 'danger';
        $this->load->view('forgot_v2', $data);
    }

    public function reset($key){
        $data = array();

        //cek keynya pass dan sudah expired belum
        $data_key = $this->keyIsExist($key);
        if (is_object($data_key)) {
            $data['validKey'] = true;
            $data['emailAdd'] = $data_key->UserEmail;
        }else{
            $data['validKey'] = false;
        }

        $this->load->view('reset_password_v2', $data);
    }

    public function changepass_first_proc(){
        $data = array();
        $newPass = $this->input->post('newPass');
        $newPassConf = $this->input->post('newPassConf');
        $username = $_SESSION['torSes'];
        $user = $this->isUserByUsername($username);
        $userid = $user->UserId;
        $username = $user->UserName;

        $prosesAll = true;
        $failMsg = array();

        //cek matching password
        if($newPass != $newPassConf){
            $prosesAll = false;
            $failMsg[] = lang('New password and password confirmation doesn\'t match!');
        }

        //cek pattern password
        if (!preg_match("/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9])(?!.*\s).{8,14}$/", $newPass)) {
            $prosesAll = false;
            $failMsg[] = lang('New password doesn\'t fit password criteria!');
        }

        //cek previous password
        $cekPrevPassword = $this->system->checkPreviousPassword($newPass,$userid);
        if($cekPrevPassword == false){
            $prosesAll = false;
            $failMsg[] = lang('New password cannot be the same with your previous password!');
        }

        //reset password & login (begin)
        if($prosesAll == true){
            $proses = $this->system->resetPassword($user,$newPass);
            $prosesAll = $prosesAll && $proses;
            if($proses == false){
                $failMsg[] = lang('Reset password failed!');
            }
        }
        //reset password & login (end)

        //update user tor
        if($prosesAll == true){
            $proses = $this->system->updateUserTor($userid);
        }

        $data['prosesAll'] = $prosesAll;
        $pesanGagal = implode("<br />",$failMsg);
        $data['pesanGagal'] = $pesanGagal;

        if($prosesAll == true){
            //kirim email notifikasi
            $this->sendEmailConfirmChangePass($user);
        }

        $this->load->view('reset_password_proc', $data);
    }

    public function reset_proc(){
        $data = array();
        $newPass = $this->input->post('newPass');
        $newPassConf = $this->input->post('newPassConf');
        $emailAdd = $this->input->post('emailAdd');
        $user = $this->isUser($emailAdd);
        $userid = $user->UserId;
        $username = $user->UserName;

        $prosesAll = true;
        $failMsg = array();

        //cek matching password
        if($newPass != $newPassConf){
            $prosesAll = false;
            $failMsg[] = lang('New password and password confirmation doesn\'t match!');
        }

        //cek pattern password
        if (!preg_match("/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9])(?!.*\s).{8,14}$/", $newPass)) {
            $prosesAll = false;
            $failMsg[] = lang('New password doesn\'t fit password criteria!');
        }

        //cek previous password
        $cekPrevPassword = $this->system->checkPreviousPassword($newPass,$userid);
        if($cekPrevPassword == false){
            $prosesAll = false;
            $failMsg[] = lang('New password cannot be the same with your previous password!');
        }

        //reset password & login (begin)
        if($prosesAll == true){
            $proses = $this->system->resetPassword($user,$newPass);
            $prosesAll = $prosesAll && $proses;
            if($proses == false){
                $failMsg[] = lang('Reset password failed!');
            }
        }
        //reset password & login (end)

        $data['prosesAll'] = $prosesAll;
        $pesanGagal = implode("<br />",$failMsg);
        $data['pesanGagal'] = $pesanGagal;
        $this->load->view('reset_password_proc', $data);
    }

    public function reset_old($key)
    {
        $result = false;

        $data_key = $this->keyIsExist($key);
        // if key exist
        if (is_object($data_key)) {
            // start transaction
            $this->db->trans_start(false);
            // insert new pass request
            $user_new_id = $data_key->id;
            $user_id     = $data_key->user_id;
            $password    = $data_key->password;
            $result      = $this->updateUser($user_id, $password);
            $result      = $result && $this->updateIsProcess($user_new_id);
            // send email
            if ($result) {
                $mail_result = $this->sendInfoNewPassEmail($data_key);
            }

            // end transactinon
            if ($result == true) {
                $this->db->trans_commit();
                $data['msg'] = "Password successfully changed.";
            } else {
                $this->db->trans_rollback();
                $data['msg'] = "Password failed to change, please try again later.";
            }
        } else {
            $data['msg'] = "Following Link doesn't exist";
        }

        $data['type'] = ($result == true) ? 'success' : 'danger';
        $this->load->view('reset_password', $data);
    }

    public function randomChar($length)
    {
        $char   = 'abcdefghijkl1234567890';
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $pos = rand(0, strlen($char) - 1);
            $string .= $char[$pos];
        }
        return $string;
    }

    public function isUserByUsername($username){
        if (empty($username)) {
            return false;
        }

        //cek apakah terdaftar
        $sql="SELECT
            *
        FROM
            sys_user a
        WHERE
            a.`UserName` = ?
        LIMIT 1";
        $query = $this->db->query($sql, array($username));
        if ($query->num_rows > 0) {
            return $query->row();
        }

        return false;
    }

    public function isUser($username)
    {
        if (empty($username)) {
            return false;
        }

        //cek apakah terdaftar
        $sql="SELECT
            *
        FROM
            sys_user a
        WHERE
            a.`UserEmail` = ?
        LIMIT 1";
        $query = $this->db->query($sql, array($username));
        if ($query->num_rows > 0) {
            return $query->row();
        }

        return false;
    }

    private function isRequested($user)
    {
        $sql = <<<SQL
SELECT
    *
FROM
    `sys_user_newpass`
WHERE
   user_id = ?
   AND   is_processed = 0
SQL;
        $query = $this->db->query($sql, array($user->UserId));
        if (count($query->result_array()) > 0) {
            return true;
        } else {
            return false;
        }
    }

    private function insertNewPass($user, $key)
    {
        $sql = <<<SQL
INSERT INTO `sys_user_newpass` (
    `user_id`,
    `password`,
    `key`
)
VALUES
    (
        ?,
        NULL,
        ?
    )
SQL;
        $result = $this->db->query($sql, array($user->UserId, $key));
        return $result;
    }

    public function keyIsExist($key)
    {
        if (empty($key)) {
            return false;
        }

        $sql="SELECT
                a.*
                ,b.`UserName`,b.`UserRealName`,b.`UserEmail`
            FROM
                sys_user_newpass a
                INNER JOIN sys_user b ON b.`UserId` = a.`user_id`
            WHERE
                `key` = ?
                AND a.is_processed = 0
                AND (a.`created` + INTERVAL 1 DAY) >= NOW()
            LIMIT 1";
        $query = $this->db->query($sql, array($key));
        if ($query->num_rows > 0) {
            return $query->row();
        }

        return false;
    }

    private function updateUser($user_id, $password)
    {
        $sql = <<<SQL
UPDATE
`sys_user`
SET
`UserPassword` = ?,
`UserUpdateUserId` = ?,
`UserUpdateTime` = NOW()
WHERE `UserId` = ?
SQL;

        $result = $this->db->query($sql, array($password, $user_id, $user_id));
        return $result;
    }

    private function updateIsProcess($user_new_id)
    {
        $sql = <<<SQL
UPDATE
  `sys_user_newpass`
SET
  `is_processed` = '1',
  `updated` = NOW()
WHERE `id` = ?
SQL;
        $result = $this->db->query($sql, array($user_new_id));
        return $result;
    }

    private function sendInfoNewPassEmail($user)
    {
        $this->load->library('email');
        $url = base_url() . 'system/login';

        // if username is not valid email
        if ($this->email->valid_email($user->UserEmail) == false) {
            return false;
        }
        // send email to user
        $subject = 'Password successfully changed';
        // TODO: move to dynamic source
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
                                    <b>Hi ' . $user->UserRealName . '</b>
                                    <br /><br />
                                    <br />
                                    <font style="font-family: Georgia, \'Times New Roman\', Times, serif; color:#010101; font-size:24px"><strong><em>Password successfully changed</em></strong>
                                    </font><br /><br />
                                    <font style="font-family: Verdana, Geneva, sans-serif; color:#666766; font-size:13px; line-height:21px">
                                    Your new Palmoiltrace password for the account ' . $user->UserName . ' has been set.
                                    <br />
                                    You can now <a href=' . $url . '>access your account</a>, view your profile or change your account settings.
                                    <br /><br /><br />
                                    <b>Palmoiltrace</b>
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
        // Also, for getting full html you may use the following internal method:
        //$body = $this->email->full_html($subject, $message);

        $result = $this->email
            ->from('support@koltiva.com')
        // ->reply_to('support@cocoatrace.com')    // Optional, an account where a human being reads.
            ->to($user->UserEmail)
            ->subject($subject)
            ->message($body)
            ->send();

        $this->email->clear();

        return $result;
    }

    private function sendEmailConfirmChangePass($user){
        $this->load->library('email');
        $this->load->helper('security');

        if ($this->email->valid_email($user->UserEmail) == false) {
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
                                    <b>Hi ' . $user->UserRealName . '</b>
                                    <br /><br />
                                    <br />
                                    <font style="font-family: Georgia, \'Times New Roman\', Times, serif; color:#010101; font-size:24px"><strong><em>Your Palmoiltrace details</em></strong>
                                    </font><br /><br />
                                    <font style="font-family: Verdana, Geneva, sans-serif; color:#666766; font-size:13px; line-height:21px">
                                    You just change your password on Palmoiltrace on '.date('Y-m-d H:i:s').'
                                    <br /><br />

                                    <b>Palmoiltrace</b>
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

        $result = $this->email
            ->from('support@koltiva.com')
            ->to($user->UserEmail)
            ->subject($subject)
            ->message($body)
            ->send();
        $this->email->clear();
        return $result;
    }

    private function sendAutoNewPassEmail($user, $pass, $key)
    {
        
        $this->load->library('email');
        $this->load->helper('security');

        $url = base_url() . 'system/login/reset/' . $key;
        // if username is not valid email
        if ($this->email->valid_email($user->UserEmail) == false) {
            return false;
        }
        // send email to user
        $subject = 'Password Reset';
        // TODO: move to dynamic source
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
                                    <b>Hi ' . $user->UserRealName . '</b>
                                    <br /><br />
                                    <br />
                                    <font style="font-family: Georgia, \'Times New Roman\', Times, serif; color:#010101; font-size:24px"><strong><em>Your Palmoiltrace details</em></strong>
                                    </font><br /><br />
                                    <font style="font-family: Verdana, Geneva, sans-serif; color:#666766; font-size:13px; line-height:21px">
                                    To reset your password in Palmoiltrace, please <a href=' . $url . '>follow this link</a>
                                    <br /><br />
                                    This request come from<br />
                                    IP Address : '.ip_address().'<br />
                                    User Agent : '.user_agent().'
                                    <br /><br />

                                    This link will valid for 24 hours from now.
                                    <br /><br />
                                    If you dont request reset password, please ignore this email
                                    <br /><br /><br />
                                    <b>Palmoiltrace</b>
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
        // Also, for getting full html you may use the following internal method:
        //$body = $this->email->full_html($subject, $message);

        $result = $this->email
            ->from('contact@koltiva.com')
        // ->reply_to('support@cocoatrace.com')    // Optional, an account where a human being reads.
            ->to($user->UserEmail)
            ->subject($subject)
            ->message($body)
            ->send();

        $this->email->clear();
        return $result;
    }

    private function sendNewPassEmail($user)
    {
        $this->load->library('email');

        // if username is not valid email
        if ($this->email->valid_email($user->UserName) == false) {
            return false;
        }
        // send email to user
        $subject = 'Password Reset Request';
        // TODO: move to dynamic source
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
                <font style="font-family: Georgia, \'Times New Roman\', Times, serif; color:#010101; font-size:24px"><strong><em>Thank you,</em></strong>
                </font><br /><br />
                <font style="font-family: Verdana, Geneva, sans-serif; color:#666766; font-size:13px; line-height:21px">
Your request has been sent to administrator.
<br />
You will be notified when your new password is ready.
<br /><br />
Palmoiltrace
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
        // Also, for getting full html you may use the following internal method:
        //$body = $this->email->full_html($subject, $message);

        $result = $this->email
            ->from('support@koltiva.com')
        // ->reply_to('support@cocoatrace.com')    // Optional, an account where a human being reads.
            ->to($user->UserName)
            ->subject($subject)
            ->message($body)
            ->send();

        $this->email->clear();

        // send email to admin
        $subject = 'Password Reset Request - ' . $user->UserName;
        $message =
        '
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
                <font style="font-family: Georgia, \'Times New Roman\', Times, serif; color:#010101; font-size:24px"><strong><em>Information,</em></strong>
                </font><br /><br />
                <font style="font-family: Verdana, Geneva, sans-serif; color:#666766; font-size:13px; line-height:21px">
User with username ' . $user->UserName . ' is requesting new password.
<br />
Please follow up his request.
<br /><br />
Palmoiltrace System
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
        // Also, for getting full html you may use the following internal method:
        //$body = $this->email->full_html($subject, $message);

        $result = $this->email
            ->from('support@koltiva.com')
        // ->reply_to('support@cocoatrace.com')    // Optional, an account where a human being reads.
            ->to('info@koltiva.com')
            ->subject($subject)
            ->message($body)
            ->send();

        // echo $this->email->print_debugger();

        return $result;

    }

    public function privacy_policy() {
        $data = array();
        $data['api_url']    = $this->config->item('api');
        
        $this->load->view('common_header_front_30');
        $this->load->view('login/privacy_policy', $data);
        $this->load->view('common_footer_front_30');
    }

    public function term_of_use() {
        $data = array();
        $data['api_url']    = $this->config->item('api');
        
        $this->load->view('common_header_front_30');
        $this->load->view('login/term_of_use', $data);
        $this->load->view('common_footer_front_30');
    }

    public function done() {
        if (empty($_SESSION['done'])) {
            redirect('system/login', 'refresh');
        }
        // //Destroy session
        // $this->session->sess_destroy();
        $_SESSION = array();

        $data = array();
        $data['api_url']    = $this->config->item('api');

        $this->load->view('login/header');
        $this->load->view('login/proc_done', $data);
        $this->load->view('login/footer');
    }
}
