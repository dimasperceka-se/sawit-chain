<?php defined('BASEPATH') or exit('No direct script access allowed');

class Farmer extends REST_Controller
{

    public $_output = array('success' => false, 'error' => 'Data is not valid'); //response data

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('UTC');
        $this->load->model('traceability_api/m_farmer', '_model');
    }

    public function fetch_cert_get(){
        $PartnerID = $this->input->get('PID');
        $SupplychainID = $this->input->get('SID');

        if ($PartnerID) {
            $farmer = $this->_model->get_certification($PartnerID, $SupplychainID);
            if ($farmer) {
                return $this->response(array(
                    'success' => true,
                    'message' => 'Data Berhasil Ditampilkan',
                    'total' => $farmer['total'],
                    //'district' => $farmer['district'],
                    'data' => $farmer['data']), 200);
            }
        }
        return $this->response($this->_output, 401);
    }

    public function fetch_get()
    {
        $PartnerID = $this->input->get('PID');
        $SupplychainID = $this->input->get('SID');

        if ($PartnerID) {
            $farmer = $this->_model->get_data_farmer($PartnerID, $SupplychainID);
            
            if ($farmer) {
                return $this->response(array(
                    'success' => true,
                    'message' => 'Data Berhasil Ditampilkan',
                    'total' => $farmer['total'],
                    //'district' => $farmer['district'],
                    'data' => $farmer['data']), 200);
            }
        }
        return $this->response($this->_output, 401);
    }

    public function submit_post()
    {
        ini_set('display_errors', true);
        error_reporting(E_ALL);
        
        $this->load->model('mmiddleware');

        $data = $this->post(null);
        if ($data) {
            $farmer = $this->_model->submit($data);
            if ($farmer) {

                $FarmerID = $farmer['FarmerID'];
                $uid = 'QxauNvjcpBw'; // push by program
                
                if($uid != '' && $FarmerID) {
                    $mID = $FarmerID;
                    $onlyNew = true;
                    $programs = $this->mmiddleware->getAllProgramWithView($uid);
                    if (count($programs) > 0) {
                        foreach ($programs as $progkeys => $program) {
                            $datas = $this->mmiddleware->getDataBy($onlyNew, $program['uid'], $mID);
                            $this->mmiddleware->syncDataPerProgram($datas, $program['uid']);
                        }
                    }
                }

                if ($farmer) {
                    $curlTable = array( 'TableName' => 'ktv_members', 'TableField' => 'MemberID', 'TableID' => @$data['MemberID'] );
    
                    $version = !empty(@$data['Version']) ? @$data['Version']: null;
                               
                    $param = checkLogTraceability($version, 'POST', current_url(), $this->post(), $curlTable);
                    
                    return $this->response($farmer);
                }

                return $this->response($farmer);
            }
        } else {
            return $this->response(array('success' => false, 'error' => 'Data post empty !'), 401);
        }
    }

    public function submit_plot_post()
    {
        ini_set('display_errors', true);
        error_reporting(E_ALL);

        $data = $this->post(null);

        // $data = json_decode(json_encode($this->post()), true);
        if(empty($data)) {
            $post = json_decode(file_get_contents('php://input'), true);
            $data = $post;
        }
        // echo "<pre> tes : ".print_r($data,1);die;
        if ($data) {
            
            $plot = $this->_model->submit_plot($data);

            if ($plot) {

                $curlTable = array( 'TableName' => 'ktv_survey_plot', 'TableField' => 'MemberID', 'TableID' => @$data['MemberID'] );

                $version = !empty(@$data['Version']) ? @$data['Version']: null;
                            
                $param = checkLogTraceability($version, 'POST', current_url(), $this->post(), $curlTable);
                
                return $this->response($plot);
            }
        } else {
            return $this->response(array('success' => false, 'error' => 'Data post empty !'), 401);
        }
    }
}
