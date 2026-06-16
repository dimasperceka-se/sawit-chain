<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Mobile_Auth extends REST_Controller {
    
    public $_output = array('success' => false, 'error' => 'Data is not valid'); //response data
    
    public function __construct() {
        parent::__construct();
        $this->load->model('mauth','_model');
    }
    
    public function login_post() {
        $traceability = $this->post('traceability');
        
        if($this->post('username')){
            
            $data = $this->post();
            
            if($traceability === 'true'){
                $doLogin = $this->_model->doLoginTraceability($this->post('username'),  $this->post('passwd'));
            } else {
                $doLogin = $this->_model->doLogin($this->post('username'),  $this->post('passwd'));
            }
            
            if($doLogin){
                return $this->response(array('success' => true, 'message' => 'Berhasil Login', 'results' => $doLogin),  200);
            }
        }
        
        return $this->response($this->_output,401);
        
    }
    
    public function logout_post() {
        
        if($this->input->get_request_header('Authorization')){
            
            $doLogout = $this->_model->doLogout($this->input->get_request_header('Authorization'));
            if($doLogout){
                return $this->response(array('success' => true, 'message' => 'Berhasil Logout'),  200);
            }
        }
        
        return $this->response($this->_output,401);
        
    }
    
}
