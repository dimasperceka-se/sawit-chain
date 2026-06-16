<?php defined('BASEPATH') OR exit('No direct script access allowed');

class seaweed_type extends REST_Controller {
    
    public $_output = array('success' => false, 'error' => 'Data error'); //response data

    public function __construct() {
        parent::__construct();
        $this->load->model('traceability_api/m_seaweed_type','_model');
    }
    public function fetch_get() {
        $seaweed_type = $this->_model->get_data();
        if($seaweed_type){
            return $this->response(array('success' => true, 
                                         'message' => 'Data Berhasil Ditampilkan',
                                         'total' => $seaweed_type['total'], 
                                         'data' => $seaweed_type['data']
                                         ),  200);
        }
        return $this->response($this->_output,401);
    }
}