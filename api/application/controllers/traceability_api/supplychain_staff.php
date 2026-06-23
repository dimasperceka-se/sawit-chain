<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Supplychain_staff extends REST_Controller {
	
    public function __construct() {
        parent::__construct();
        date_default_timezone_set('UTC');
        $this->load->model('traceability_api/m_supplychain_staff','_model');
    }
	
	public function fetch_get(){
        $sorting = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $data = $this->_model->get_data($this->get('StaffID'),$this->get('key'),$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        if($data) $this->response($data, 200); else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }
	
	public function submit_post(){ 
        $data = $this->post(NULL);
        
        $id = $data['StaffRelID'];
        if($id == ""){
            unset($data['StaffRelID']);
            $data['StatusCode'] = 'active';
            $data['CreatedBy'] = $_SESSION['userid'];
            $data['DateCreated'] = date('Y-m-d H:i:s');
            
            $submit = $this->db->insert("ktv_tc_supplychain_staff_rel",$data);
        }else{
            $data['StatusCode'] = 'active';
            $data['LastModifiedBy'] = $_SESSION['userid'];
            $data['DateUpdated'] = date('Y-m-d H:i:s');
            
            $this->db->where("StaffRelID",$data['StaffRelID']);
            $submit = $this->db->update("ktv_tc_supplychain_staff_rel",$data);
        }
        
        if ($submit) {
            $this->response($submit, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }

    function data_get(){
        if($_GET["StaffRelID"]){
            $sql = "SELECT
                StaffRelID
                , StaffID
                , SupplychainID
                , StartDate
                , EndDate                
                FROM
                    `ktv_tc_supplychain_staff_rel`
                WHERE
                    StaffRelID = ?";
            $query = $this->db->query($sql,array($_GET["StaffRelID"]));

            $this->response(array("success"=>true,"data"=>$query->row_array()),200);
        }
    }

    function data_delete(){
        $data['StatusCode'] = 'nullified';
        $data['LastModifiedBy'] = $_SESSION['userid'];
        $data['DateUpdated'] = date('Y-m-d H:i:s');
        
        $this->db->where("StaffRelID",$this->delete('StaffRelID'));
        $submit = $this->db->update("ktv_tc_supplychain_staff_rel",$data);

        $this->response($submit,200);
    }
}