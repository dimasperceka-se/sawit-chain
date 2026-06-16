<?php defined('BASEPATH') OR exit('No direct script access allowed');

class farming_type extends REST_Controller {

    public $_output = array('success' => false, 'error' => 'Data error'); //response data

    public function __construct() {
        parent::__construct();
        date_default_timezone_set('UTC');
        $this->load->model('traceability_api/m_farming_type','_model');
    }
    public function fetch_get() {
        $farmer = $this->_model->get_data_farmer_type();
        if($farmer){
            return $this->response(array('success' => true, 
                                         'message' => 'Data Berhasil Ditampilkan',
                                         'total' => $farmer['total'], 
                                         'data' => $farmer['data']
                                         ),  200);
        }
        return $this->response($this->_output,401);
    }
}