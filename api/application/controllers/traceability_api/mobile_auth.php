<?php defined('BASEPATH') or exit('No direct script access allowed');

class mobile_auth extends REST_Controller
{

    public $_output = array('success' => false, 'error' => 'Data is not valid'); //response data

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('UTC');
        $this->load->model('traceability_api/m_auth', '_model');
    }

    public function login_post()
    {
        if ($this->post('username')) {
            $doLogin = $this->_model->doLogin($this->post('username'), $this->post('passwd'));
            if ($doLogin) {
                return $this->response(array('success' => true,
                    'message' => 'Berhasil Login',
                    'results' => $doLogin), 200);
            }
        }
        return $this->response($this->_output, 401);
    }

    public function login_app_post(){
        $this->load->model('staff/mstaffuser_cognito');
        $this->load->model('staff/mstaffuser');
        if($this->post('username')){
            $data = $this->post();
            
            $proses = $this->mstaffuser_cognito->frontLoginApp($data["username"],$data["password"]);
            $this->response($proses, 200); 
            if($proses['success'] == true) {
                //Lakukan proses login (set session)
                if($proses['process'] != "new_password_required") { //only login_success or tor_required
                    $this->mstaffuser->setUserSessionLogin($_SESSION['userid'],false,'Cognito');
                }

                //Cek sync basic data aws
                $prosesSync = $this->mstaffuser_cognito->CekSyncBasicDataCognito($Username);
                $proses['CekDuplikatEmail'] = $prosesSync['CekDuplikatEmail'];
                $proses['CekDuplikatPhonenumber'] = $prosesSync['CekDuplikatPhonenumber'];

                return $this->response(array('success' => true,
                    'message' => 'Berhasil Login',
                    'results' => $proses), 200);
            } else {
                $this->response($proses, 400);
            }
        }
        return $this->response($this->_output,401);
    }

    //new version farmgate
    public function new_post() 
    {
        $this->load->model('staff/mstaffuser_cognito');
        $this->load->model('staff/mstaffuser');
        if($this->post('username')){
            $data = $this->post();           
            
            $proses = $this->mstaffuser_cognito->frontLoginV2($data["username"],$data["passwd"]);
            $doLogin = $this->_model->getDataUserFarmgateNew($this->post('username'));

            if($proses['success'] == true) {
                //Lakukan proses login (set session)
                if($proses['process'] != "new_password_required") { //only login_success or tor_required
                    $this->mstaffuser->setUserSessionLogin($_SESSION['userid'],false,'Cognito');
                }

                //Cek sync basic data aws
                $prosesSync = $this->mstaffuser_cognito->CekSyncBasicDataCognito($Username);
                
                $proses['CekDuplikatEmail'] = $prosesSync['CekDuplikatEmail'];
                $proses['CekDuplikatPhonenumber'] = $prosesSync['CekDuplikatPhonenumber'];

                //jika UserID atau sme kosong
                if($doLogin['UserID'] == '' || $doLogin['sme'] == ''){
                    return $this->response(array('failed' => true,
                    'message' => 'Failed Login User ID or SME Not Found',), 200);
                }

                return $this->response(array('success' => true,
                    'message' => 'Berhasil Login',
                    'data' => $proses,
                    'results' => $doLogin), 200);
            } else {
                $this->response($proses, 400);
            }
        }
        
        return $this->response($this->_output,401);
    }

    //v2 farmgate
    public function v2_post() 
    {
        $this->load->model('staff/mstaffuser_cognito');
        $this->load->model('staff/mstaffuser');
        if($this->post('username')){
            $data = $this->post();           
            
            $proses = $this->mstaffuser_cognito->frontLoginV2($data["username"],$data["passwd"]);
            $doLogin = $this->_model->getDataUser($this->post('username'));

            if($proses['success'] == true) {
                //Lakukan proses login (set session)
                if($proses['process'] != "new_password_required") { //only login_success or tor_required
                    $this->mstaffuser->setUserSessionLogin($_SESSION['userid'],false,'Cognito');
                }

                //Cek sync basic data aws
                $prosesSync = $this->mstaffuser_cognito->CekSyncBasicDataCognito($Username);
                
                $proses['CekDuplikatEmail'] = $prosesSync['CekDuplikatEmail'];
                $proses['CekDuplikatPhonenumber'] = $prosesSync['CekDuplikatPhonenumber'];

                return $this->response(array('success' => true,
                    'message' => 'Berhasil Login',
                    'data' => $proses,
                    'results' => $doLogin), 200);
            } else {
                $this->response($proses, 400);
            }
        }
        return $this->response($this->_output,401);
        
    }

    public function sme_post() 
    {
        $UserID = $this->post('UserID');
        $SID    = $this->post('SID');
        $PID    = $this->post('PID');
      
        if($UserID) {
            $doSme = $this->_model->doSme($UserID,$SID,$PID);
            if ($doSme) {
                return $this->response(array('success' => true,
                'message' => 'Berhasil Login',
                'results' => $doSme), 200);
            }
        }

        return $this->response($this->_output, 401);
    }

    public function relation_get()
    {
        $data = $this->_model->getRelation($this->get());
        if ($data) {
            return $this->response(array('success' => true,
                'results' => $data), 200);
        }
        return $this->response($this->_output, 401);
    }

    public function plantation_get()
    {
        $data = $this->_model->getPlantation($this->get());
        if ($data) {
            return $this->response(array('success' => true,
                'results' => $data), 200);
        }
        return $this->response($this->_output, 401);
    }
}
