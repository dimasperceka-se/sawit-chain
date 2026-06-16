<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Transaction extends REST_Controller {
    
    public $_output = array('success' => false, 'error' => 'Data is not valid'); //response data
    
    public function __construct() {
        parent::__construct();
        date_default_timezone_set('UTC');
        $this->load->model('traceability_api/m_transaction','_model');
    }
    public function fetch_get() {
        $SIP = $this->input->get('SID'); 
        $PID = $this->input->get('PID');
        $STID = $this->input->get('STID');

        if($SIP){
            $transaction = $this->_model->get_data_transaction($SIP, $PID, $STID);
            if($transaction){ 
                return $this->response(array('success' => true, 
                                             'message' => 'Data Berhasil Ditampilkan',
                                             'total' => $transaction['total'], 
                                             'data' => $transaction['data']),  200);
            }
        }
        return $this->response($this->_output,401);
    }

    public function submit_post(){
        ini_set('display_errors',true);
        error_reporting(E_ALL);
        
        $data = $this->post(NULL);
        if($data){
            $transaction = $this->_model->submit($data['data']);
            if($transaction){ 
                return $this->response($transaction);
            }
        }else{
            return $this->response(array('success' => false, 'error' => 'Data post empty !'),401);
        }
    }

    public function palmoil_type_get()
    {
        $data = $this->_model->GetPalmoilType($this->get('SupplyTransID'));

        $this->response($data, 200);
    }
    
    
    
}
