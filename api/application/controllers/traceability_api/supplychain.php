<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Supplychain extends REST_Controller {
    
    public $_output = array('success' => false, 'error' => 'Data is not valid'); //response data
    
    public function __construct() {
        parent::__construct();
        date_default_timezone_set('UTC');
        $this->load->helper('file');
        $this->load->model('traceability/api/msupplychain','_model');
    }

    public function list_spb_get(){
        $SID = $this->get("SID");

        if($SID){
            $data = $this->_model->getListSPB($SID);

            return $this->response($data,200);
        }
    }
    
    public function submit_transaction_post() {
        $post = json_decode(json_encode($this->post()), true);
        if(empty($data)) {
            $post = json_decode(file_get_contents('php://input'), true);
            $data = $post;
        }
        
        $name = $data['SupplychainID'] . '-' . strtotime(date('YmdHis')). '-Trans-' . $data['TransNumber'].'-'.@$data['AutoTransNumber'];
        $dir = FCPATH . 'backup_traceability_sync';
        if(!is_dir($dir)) {
          make_directory($dir, 0777, true);
        }
        if(!write_file($dir.'/'.$name.'.json',json_encode($data))) {} else {}
        
        $process = $this->_model->_submitTransactionNew($data, true); 
        // echo "<pre> tes : ".print_r($process);die;
        
        if($process){
            if($process['status']==false){
                $ret = array( 'success'=>false, 'ErrorCode'=>$process['ErrorCode']);

                $curlTable = array( 'TableName' => 'ktv_tc_supplychain_transaction', 'TableField' => 'SupplyTransID', 'TableID' => @$data['data']['SupplyTransID'] );

                $version = !empty(@$data['Version']) ? @$data['Version']: null;
                           
                $param = checkLogTraceability($version, 'POST', current_url(), $this->post(), $curlTable);
            }else{
                unset($process['status']);
                $ret = array( 'success'=>true, 'data'=>$process);

                $curlTable = array( 'TableName' => 'ktv_tc_supplychain_transaction', 'TableField' => 'SupplyTransID', 'TableID' => @$data['data']['SupplyTransID'] );

                $version = !empty(@$data['Version']) ? @$data['Version']: null;
                           
                $param = checkLogTraceability($version, 'POST', current_url(), $this->post(), $curlTable);
            }
            return $this->response($ret);
        }else{
            return $this->response(array('success' => false, 'ErrorCode' => 1),  200);
        }
    }

    public function submit_batch_post() {
        $data = json_decode(json_encode($this->post()), true);
        if(empty($data)) {
            $post = json_decode(file_get_contents('php://input'), true);
            $data = $post;
        }
        
        $name = $data['data']['SupplyOrgID'] . '-' . strtotime(date('YmdHis')). '-Batch-' . $data['data']['SupplyBatchNumber'];
        $dir = FCPATH . 'backup_traceability_sync';
        if(!is_dir($dir)) {
          make_directory($dir, 0777, true);
        }
        if(!write_file($dir.'/'.$name.'.json',json_encode($data))) {} else {}

        $return = $this->_model->_submitBatchNew($data['data']);
        
        if($return){
            if($return['status']==false){
                $ret = array( 'success'=>false, 'ErrorCode'=>$return['ErrorCode']);

                $curlTable = array( 'TableName' => 'ktv_tc_supplychain_batch', 'TableField' => 'SupplyBatchNumber', 'TableID' => @$data['data']['SupplyBatchID'] );

                $version = !empty(@$data['data']['Version']) ? @$data['data']['Version']: null;
                
                $param = checkLogTraceability($version,'POST', current_url(), $this->post(), $curlTable);
            }else{
                unset($return['status']);
                $ret = array( 'success'=>true, 'data'=>$return);

                $curlTable = array( 'TableName' => 'ktv_tc_supplychain_batch', 'TableField' => 'SupplyBatchNumber', 'TableID' => @$data['data']['SupplyBatchID'] );

                $version = !empty(@$data['data']['Version']) ? @$data['data']['Version']: null;
                
                $param = checkLogTraceability($version,'POST', current_url(), $this->post(), $curlTable);
            }
            return $this->response($ret);
        }else{
            return $this->response(array('success' => false, 'ErrorCode' => 1),  200);
        }
    }

    public function delete_transaction_post() {
        //error_reporting(0);
		//ini_set('display_errors',false);

        $data = json_decode(json_encode($this->post()), true);

        //non standard CI, hmm... berarti REST Server CI ini tangkep headernya apa ya? haduehh...
        if(empty($data)) {
            $post = json_decode(file_get_contents('php://input'), true);
            $data = $post;
        }
        //echo "<pre> tes : ".print_r($data,1);die;
        
        $name = $data['data']['SupplychainID'] . '-' . strtotime(date('YmdHis')). '-DeleteTrans-' . $data['data']['FakturNumber'];
        $dir = FCPATH . 'backup_traceability_sync';
        if(!is_dir($dir)) {
          make_directory($dir, 0777, true);
        }
        if(!write_file($dir.'/'.$name.'.json',json_encode($data))) {} else {}
        $process = $this->_model->_deleteTransaction($data['data']); 
        
        if($process){
            if($process['status']==false){
                $ret = array( 'success'=>false, 'ErrorCode'=>$process['ErrorCode']);
            }else{
                unset($process['status']);
                $ret = array( 'success'=>true, 'data'=>$process);
            }
            return $this->response($ret);
        }else{
            return $this->response(array('success' => false, 'ErrorCode' => 1),  200);
        }
    }

    public function download_transaction_get() {
        $data = $this->_model->_getTransaction($this->get());
        if($data){
            return $this->response(array('success' => true, 'data' => $data),  200);
        }else{
            return $this->response(array('success' => false, 'data' => array()),  200);
        }
    }

    public function download_batch_get() {
        $data = $this->_model->_getBatch($this->get());
        if($data){
            return $this->response(array('success' => true, 'data' => $data),  200);
        }else{
            return $this->response(array('success' => false, 'data' => array()),  200);
        }
    }

    public function download_reception_get() {
        $get = array(
            'SupplyDestOrgID' => $this->get('sid')
        );
        $data = $this->_model->_getBatch($get);
        if($data){
            return $this->response(array('success' => true, 'data' => $data),  200);
        }else{
            return $this->response(array('success' => false, 'data' => array()),  200);
        }
    }
    
    
}
