<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends REST_Controller {
    
    public $_output = array('success' => false, 'error' => 'Data is not valid'); //response data
    
    public function __construct() {
        parent::__construct();
        $this->load->model('traceability_api/mlogin','_model');
    }
    
    public function index_post() {
        //ini_set('display_errors',true);
        //error_reporting(E_ALL);
        if($this->post('username')){
            $data = $this->post();
            $doLogin = $this->_model->doLoginTraceability($this->post('username'),  $this->post('passwd'));
            if($doLogin){
                return $this->response(array('success' => true, 'message' => 'Berhasil Login', 'results' => $doLogin),  200);
            }
        }
        return $this->response($this->_output,401);
        
    }

    public function user_login_post(){
        $this->load->library('verifikasitoken');
        $header = $this->input->request_headers();
        if(isset($header['Token'])){
            $cekValisasiToken = $this->verifikasitoken->cekValisasiToken($header['Token'], true);
            if($cekValisasiToken['success']){
                $check = $this->_model->checkUserLoginStatus($cekValisasiToken['data']['cognito:username'], $header['Token']);
                $data['IsLoggedIn'] = $check;
                if($check==false){
                    return $this->response(array('success' => true, 'data'=> $data),  200);die;
                }else{
                    return $this->response(array('success' => false, 'message' => 'The user is already logged in on another device. Please log out first so you can log in on the new device', 'data'=> $data),  200);    
                }
            }else{
                return $this->response(array('success' => false, 'message' => $cekValisasiToken['message']),  200);
            }
        }
        return $this->response(array('success' => false, 'message' => 'No token found'),  200);
    }

    public function user_logout_post(){
        $this->load->library('verifikasitoken');
        $header = $this->input->request_headers();
        if(isset($header['Token'])){
            $cekValisasiToken = $this->verifikasitoken->cekValisasiToken($header['Token'], true);
            if($cekValisasiToken['success']){
                $check = $this->_model->checkUserLogoutStatus($cekValisasiToken['data']['cognito:username'], $header['Token']);
                return $this->response(array('success' => true, 'auth'=> @$check['auth']),  200);die;
            }else{
                return $this->response(array('success' => false, 'auth'=> false, 'message' => $cekValisasiToken['message']),  200);
            }
        }
        return $this->response(array('success' => false, 'auth'=> false, 'message' => 'No token found'),  200);
    }

    public function user_detail_get() {

        $this->load->library('verifikasitoken');
        $header = $this->input->request_headers();
        if(isset($header['Token'])){
            $cekValisasiToken = $this->verifikasitoken->cekValisasiToken($header['Token'], true);
            //$cekValisasiToken['success'] = true;
            //$cekValisasiToken['data']['cognito:username'] = 'firman';
            if($cekValisasiToken['success']){
                $check = $this->_model->checkUserLoginStatus($cekValisasiToken['data']['cognito:username'], $header['Token']);
                $data['IsLoggedIn'] = $check;
                if($check==false){
                    $doLogin = $this->_model->doLoginTraceability_v2($cekValisasiToken['data']['cognito:username'], $header['Token']);
                    if($doLogin){
                        return $this->response(array('success' => true, 'message' => 'Berhasil Login', 'results' => $doLogin),  200);
                    }
                }else{
                    return $this->response(array('success' => false, 'message' => 'The user is already logged in on another device. Please log out first so you can log in on the new device', 'data'=> $data),  200);    
                }
            }else{
                return $this->response(array('success' => false, 'message' => $cekValisasiToken['message']),  200);
            }
        }
        return $this->response(array('success' => false, 'message' => 'No token found'),  200);
        
    }

    public function v2_post() {
        if($this->post('username')){
            $data = $this->post();
            $doLogin = loginCognito($this->post('username'),  $this->post('passwd'));
            return $this->response(array('success' => true, 'results' => $doLogin),  200);
        }
        return $this->response($this->_output,401);
        
    }

    public function logout_post() {
        $data = $this->_model->doLogoutTraceability($this->post());
        if($data){
            return $this->response(array('success' => true, 'message' => 'Logout success'),  200);
        }else{
            return $this->response(array('success' => false, 'message' => 'Logout failed'),  200);
        }
    }

    public function logout_v2_post() {
        $header = $this->input->request_headers();
        $param = checkTokenTraceability($header['Token']);
        if($param['success']=='true' && $param['auth']==true){
            $get = $this->get();
            $get['sid'] = $param['SupplychainID'];
            $get['userid'] = $param['UserID'];
        }else{
            return $this->response(array('success' => false, 'auth'=> $param['auth'], 'message' => $param['message']),  200);die;
        }
        $data = $this->_model->doLogoutTraceability($get);
        if($data){
            return $this->response(array('success' => true, 'auth'=> $param['auth'], 'message' => 'Logout success'),  200);
        }else{
            return $this->response(array('success' => false, 'auth'=> $param['auth'], 'message' => 'Logout failed'),  200);
        }
    }

    public function manual_post() {
        //ini_set('display_errors',true);
        //error_reporting(E_ALL);
        if($this->post('username')){
            
            $data = $this->post();
            $doLogin = $this->_model->doLoginTraceabilityManual($this->post('username'),  $this->post('passwd'));
            if($doLogin['success']==true){
                return $this->response(array('success' => true, 'message' => 'Berhasil Login', 'results' => $doLogin),  200);
            }else{
                return $this->response(array('success' => false, 'message' => 'Gagal Login', 'results' => $doLogin),  200);
            }
        }
        return $this->response($this->_output,401);
        
    }
}
