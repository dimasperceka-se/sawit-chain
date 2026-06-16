<?php defined('BASEPATH') or exit('No direct script access allowed');

class trans_batch extends REST_Controller
{

    public $_output = array('success' => false, 'error' => 'Data is not valid'); //response data

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('UTC');
        $this->load->model('traceability_api/m_trans_batch', '_model');
    }
    public function fetch_get()
    {
        $SID = $this->input->get('SID');
        $PID = $this->input->get('PID');
        
        if ($SID) {
            $trans_batch = $this->_model->get_data_trans($SID, $PID);
            
            if ($trans_batch['total'] != '0') {
                return $this->response(array('success' => true,
                    'message' => 'Data Success',
                    'total' => $trans_batch['total'],
                    'data' => $trans_batch['data']), 200);
            } else{
                return $this->response(array('success' => true,
                    'message' => 'Data is empty',
                    'total' => $trans_batch['total'],
                    'data' => $trans_batch['data']), 200);
            }
        }
        return $this->response($this->_output, 401);
    }

    public function submit_post()
    {
        ini_set('display_errors', true);
        error_reporting(E_ALL);
        $data = $this->post(null);
        if ($data) {
            $trans_batch = $this->_model->submit($data);
            if ($trans_batch) {
                return $this->response($trans_batch);
            }
        } else {
            return $this->response(array('success' => false, 'error' => 'Data post empty !'), 401);
        }
    }
}
