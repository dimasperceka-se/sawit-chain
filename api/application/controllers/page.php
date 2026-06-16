<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page extends REST_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mpage');
        ini_set('display_errors',false); error_reporting(0); //Untuk didemo ntah kenapa selalu muncul notice2.
    }

    public function info_get()
    {
        $module     = $this->get('module');
        $data       = $this->mpage->getInfo($module);
        $this->response($data, 200);
    }

    public function front_user_login_post() {
        $this->load->model('staff/mstaffuser');
        $this->load->model('staff/mstaffuser_cognito');
        $Username = $this->post('username');
        $Passwd = $this->post('passwd');
        //ini_set('display_errors',true); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

        $proses = $this->mstaffuser_cognito->frontLogin($Username,$Passwd);
        if($proses['success'] == true) {
            //Lakukan proses login (set session)
            if($proses['process'] != "new_password_required") { //only login_success or tor_required
                $this->mstaffuser->setUserSessionLogin($_SESSION['userid'],false,'Cognito');
            }

            //Cek sync basic data aws
            $prosesSync = $this->mstaffuser_cognito->CekSyncBasicDataCognito($Username);
            $proses['CekDuplikatEmail'] = $prosesSync['CekDuplikatEmail'];
            $proses['CekDuplikatPhonenumber'] = $prosesSync['CekDuplikatPhonenumber'];

            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function front_passwd_challenge_post() {
        $this->load->model('staff/mstaffuser');
        $this->load->model('staff/mstaffuser_cognito');
        $Passwd = $this->post('passwd');
        $UserId = $_SESSION['userid'];

        //validasi password
        $cekValidasiPassword = cekValidasiPassword($Passwd);
        if($cekValidasiPassword['success'] == false) {
            $return['success'] = false;
            $return['message'] = $cekValidasiPassword['message'];
            $this->response($return, 400);
        }

        $DataUser = $this->mstaffuser->GetDataUserFromByUserId($UserId);

        $proses = $this->mstaffuser_cognito->frontPasswdChallenge($UserId,$Passwd);
        if($proses['success'] == true) {
            //Lakukan proses login (set session)
            $this->mstaffuser->setUserSessionLogin($UserId,false,'Cognito');

            //Cek sync basic data aws
            $this->mstaffuser_cognito->CekSyncBasicDataCognito($DataUser['data']['username']);

            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function front_user_tor_post() {
        $this->load->model('staff/mstaffuser');
        $post = $this->post();

        $proses = $this->mstaffuser->UpdateUserTor($post);
        if($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function front_req_forgot_password_post() {
        $this->load->model('staff/mstaffuser_cognito');
        $this->load->model('staff/mstaffuser');
        $Username = $this->post('username');

        $_SESSION['username'] = $Username;

        //Cek Username
        $CekUsername = $this->mstaffuser->CekDuplikatUsername($Username,'insert',null);
        if($CekUsername == false) {
            $return['success'] = false;
            $return['message'] = lang('Username is not registered');
            $this->response($return, 400);
        }

        $proses = $this->mstaffuser_cognito->frontReqForgotPass($Username);
        $proses['page'] = '/system/login/forgot_verification_code';
        
        if($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function front_forgot_pass_confirm_post() {
        session_start();
        $this->load->model('staff/mstaffuser');
        $this->load->model('staff/mstaffuser_cognito');
        $Username = $_SESSION['username'];
        $Passwd = $this->post('passwd');
        $VerificatonCode = $this->post('verification_code');
        // echo $Username;exit;

        //validasi password
        $cekValidasiPassword = cekValidasiPassword($Passwd);
        if($cekValidasiPassword['success'] == false) {
            $return['success'] = false;
            $return['message'] = $cekValidasiPassword['message'];
            $this->response($return, 400);
        }
        $proses = $this->mstaffuser_cognito->frontForgotPassConfirmation($Username,$Passwd,$VerificatonCode);
        if($proses['success'] == true) {
            //Destroy session
            // $this->session->sess_destroy();
            $_SESSION = array();
            // Session untuk flag
            $_SESSION['done'] = true;
            //Direct page
            $proses['page'] = '/system/login/done';

            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }
    
}