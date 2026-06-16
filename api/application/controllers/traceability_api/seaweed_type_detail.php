<?php defined('BASEPATH') OR exit('No direct script access allowed');

class seaweed_type_detail extends REST_Controller {
    
    public $_output = array('success' => false, 'error' => 'Data error'); //response data

    public function __construct() {
        parent::__construct();
        $this->load->model('traceability_api/m_seaweed_type_detail','_model');
    }
    public function fetch_get() {
        $data = $this->_model->get_data();
        if($data){
            return $this->response(array('success' => true, 
                                         'message' => 'Data Berhasil Ditampilkan',
                                         'total' => $data['total'], 
                                         'data' => $data['data']
                                         ),  200);
        }
        return $this->response($this->_output,401);
    }
}