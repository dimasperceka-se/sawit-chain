<?php defined('BASEPATH') or exit('No direct script access allowed');

class Collecting extends REST_Controller
{

    public $_output = array('success' => false, 'error' => 'Data is not valid'); //response data

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('UTC');
        $this->load->model('traceability_api/m_collecting', '_model');
    }
    public function fetch_get()
    {
        $SID = (int) $this->get('SID');
        $PID = (int) $this->get('PID');
        
        $collecting = $this->_model->get_data_collecting($SID,$PID);
        if ($collecting) {
            return $this->response(array(
                'success' => true,
                'message' => 'Data Berhasil Ditampilkan',
                'total' => $collecting['total'],
                'data' => $collecting['data']), 200);
        }
        return $this->response($this->_output, 401);
    }

    public function submit_post()
    {
        ini_set('display_errors', true);
        error_reporting(E_ALL);

        $data = $this->post(null);
        if ($data) {
            $collecting = $this->_model->submit($data);
            if ($collecting) {
                
                $curlTable = array( 'TableName' => 'ktv_collecting_point_member', 'TableField' => 'CollectpointID', 'TableID' => @$data['CollectpointID'] );

                $version = !empty(@$data['Version']) ? @$data['Version']: null;
                            
                $param = checkLogTraceability($version, 'POST', current_url(), $this->post(), $curlTable);

                return $this->response($collecting);
            }
        } else {
            return $this->response(array('success' => false, 'error' => 'Data post empty !'), 401);
        }
    }
}
