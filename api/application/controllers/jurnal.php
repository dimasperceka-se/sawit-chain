<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Jurnal extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('accounting/mjurnal', '_model');
    }  
    
    public function getdata_get() {
        
        $start = $this->get('start');
        $limit = $this->get('limit');
        $sort = 'journalDate';
        $dir = 'DESC';
        
        if($this->get('sort')){
            $sort = json_decode($this->get('sort'),true);
            $dir = $sort[0]['direction'];
            $sort = $sort[0]['property'];
        }
        
        $filter = array();
        
        if(strlen($this->get('journalTypeCode')) > 0){
            $filter['journalTypeCode'] = $this->get('journalTypeCode');
        }
        
        if(strlen($this->get('journalIsPosted')) > 0){
            $filter['journalIsPosted'] = $this->get('journalIsPosted');
        }
        
        if(strlen($this->get('journalStartDate')) > 0){
            $filter['JOURNAL_START'] = $this->get('journalStartDate');
        }
        
        if(strlen($this->get('journalEndDate')) > 0){
            $filter['JOURNAL_END'] = $this->get('journalEndDate');
        }
        
        $data = $this->_model->getData($start,$limit,$sort,$dir,$filter);
        $this->_num = 200;
        $this->_output = array('success' => true, 'data' => $data['data'], 'total' => $data['total']);
        
        return $this->response($this->_output,  $this->_num);
    }
    
    public function add_post() {
        $data = $this->post();
        $data['JournalCRBY'] = $_SESSION['userid'];
        $data['CoopID'] = getCoopID();
        $add = $this->_model->add($data);
        if($add){
            $this->_num = 200;
            $this->_output = $add;
        }
        return $this->response($this->_output,  $this->_num);
    }
    
    public function edit_put($id) {
        $data = $this->put();
        $data['JournalUPBY'] = $_SESSION['userid'];
        $data['CoopID'] = getCoopID();
        $edit = $this->_model->edit($id,$data);
        if($edit){
            $this->_num = 200;
            $this->_output = $edit;
        } die;
        return $this->response($this->_output,  $this->_num);
    }
    
    public function delete_delete($id) {
        $data = $this->put();
        $edit = $this->_model->delete($id);
        if($edit){
            $this->_num = 200;
            $this->_output = $edit;
        }
        return $this->response($this->_output,  $this->_num);
    }
    
    public function get_get($id) {
        $data = $this->_model->getById($id);
        if($data){
            $this->_num = 200;
            $this->_output = array('success' => true, 'data' => $data, 'total' => count($data));
        }
        return $this->response($this->_output,  $this->_num);
    }
    
    public function ledger_post() {
        ini_set('display_errors',true);
        error_reporting(E_ALL);
        $start_date = $this->input->post('START_DATE');
        $end_date   = $this->input->post('END_DATE');
        $coa   = $this->input->post('COA_CODE');
        $dir   	= $this->input->post('dir');
        $sort  	= $this->input->post('sort');
        $status = $this->input->post('status');
        
        //if($coa_start>$coa_end)die('Please select from COA '.$coa_end.' to COA '.$coa_start.'');
        $recs = $this->_model->get_coa_code($start_date,$end_date,$coa);
        
        if(empty($recs)) die('No Data. Please select another COA');

        $data['coa_balance'] = $this->_model->get_coa_balance($recs[0]['journalClosedDate'], $start_date,$coa);
        
        
        $data['coa_code'] = $recs;
                
        $data['result'] = $this->_model->get_data($start_date,$end_date,$coa,$status);
        
        $data['gl_balance'] = $this->_model->getGLBalance($start_date,$end_date,$coa,$status);
        //var_dump($this->db->last_query());die;
        $balance_detail = $this->_model->get_data_balance($start_date);
        if($balance_detail){
            $data['forward'] = $this->_model->get_forward_value($start_date, $coa,$balance_detail);
        }else{
            $data['forward'] = array();
        }
        $balance_amount = $this->_model->get_balance_amount($coa,$balance_detail);
        $data['balance_amount'] = $balance_amount;
        
        $data['print_out'] = date('d M Y H:i:s');
        $data['period'] = date('d M Y',strtotime($start_date)).' to '.date('d M Y',strtotime($end_date));
        $this->load->view('ledger', $data);
    }
    
    public function getdetail_get($id) {
        
        $start = $this->get('start');
        $limit = $this->get('limit');
        $sort = 'journalDetailID';
        $dir = 'DESC';
        
        if($this->get('sort')){
            $sort = json_decode($this->get('sort'),true);
            $dir = $sort[0]['direction'];
            $sort = $sort[0]['property'];
        }
        
        $query = $this->get('query');
        
        $data = $this->_model->getDetail($id,$start,$limit,$sort,$dir);
        
        $this->_num = 200;
        $this->_output = array('success' => true, 'data' => $data['data'], 'total' => $data['total']);
        
        return $this->response($this->_output,  $this->_num);
    }
    
    public function getcombo_get() {
        
        $table = $this->get('table');
        $value = $this->get('id');
        $display = $this->get('name');
        $query = $this->get('query');
        $val = $this->get('val'); 
        
        $data = $this->_model->getCombo($table,$value,$display,$query,$val);
        $this->_num = 200;
        $this->_output = array('success' => true, 'data' => $data, 'total' => count($data));
        
        return $this->response($this->_output,  $this->_num);
    }

}
