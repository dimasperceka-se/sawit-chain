<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Sme extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('traceability_api/m_sme');
    }

    public function add_sme_post() 
    {
        ini_set('display_errors', true);
        error_reporting(E_ALL);
        
        $post = json_decode(json_encode($this->post()), true);
        if(empty($data)) {
            $post = json_decode(file_get_contents('php://input'), true);
            $data = $post;
        }
        
        $name = strtotime(date('YmdHis')). '-sme-' . 'farmgate';
        $dir = FCPATH . 'backup_traceability_add_sme';
        if(!is_dir($dir)) {
          make_directory($dir, 0777, true);
        }
        if(!write_file($dir.'/'.$name.'.json',json_encode($data))) {} else {}
        
        $data = $this->post(null);
        if ($data) {
            $sme = $this->m_sme->add_sme_post($data);
            if ($sme) {
                unset($sme['status']);
                $sme = array( 'success'=>true, 'data'=>$sme);
                
                if ($sme) {
                    $curlTable = array( 'TableName' => 'ktv_members_extension', 'TableField' => 'MemberID', 'TableID' => @$data['MemberID'] );
    
                    $version = !empty(@$data['Version']) ? @$data['Version']: null;
                               
                    $param = checkLogTraceability($version, 'POST', current_url(), $this->post(), $curlTable);
                    
                    return $this->response($sme);
                }

                return $this->response($sme);
            }
        } else {
            return $this->response(array('success' => false, 'error' => 'Data post empty !'), 401);
        }
    }    

    public function fetch_api_get() 
    {
        $userid = (int) $this->get('userid');

        if($userid) {
            $data = $this->m_sme->GetSme($userid);
            $total = count($data);
            
            return $this->response(array('success' => true,
            'message' => 'Data Berhasil di tampilkan',
            'total' => $total,
            'data' => $data), 200);
        } else {
            return $this->response(array('false' => true,
            'message' => 'Data tidak ditemukan',
            'results' => $data), 400);
        }

        return $this->response($this->_output, 401);
    }
}