<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Supplychain_org extends REST_Controller {
    
	/*
    public $_output = array('success' => false, 'error' => 'Data error'); //response data
    public $recordStart  = 0;
    public $recordLimit  = 12;
    public $sortingField = 'SupplychainID';
    public $sortingDir   = 'DESC';
    public $searchKey    = '';
    public $weekNew      = false;
    public $weekModified = false;
    */
	
    public function __construct() {
        parent::__construct();
        date_default_timezone_set('UTC');
        $this->load->model('traceability_api/m_supplychain_org','_model');
    }
	
	public function fetch_get(){
        $sorting = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $data = $this->_model->get_data($this->get('ObjType'),$this->get('Name'), $this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        if($data) $this->response($data, 200); else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }
	
	 public function partner_get(){
        $output = $this->_model->partner();
        $this->response($output, 200);
    }
	
	public function submit_post(){ 
        $data = $this->post(NULL); 
        
        $id = $data['Koltiva_view_Traceability_new_Reference_Supplychain_org-dataForm-ObjID'];

        if($data['Koltiva_view_Traceability_new_Reference_Supplychain_org-dataForm-ObjType'] == 'refinery'){
            $s = $this->_model->updateRefinery($id);
        }

        if($data['Koltiva_view_Traceability_new_Reference_Supplychain_org-dataForm-ObjType'] == 'bulking' || $data['Koltiva_view_Traceability_new_Reference_Supplychain_org-dataForm-ObjType'] == 'kcp'){
            $s = $this->_model->updateBulkingKCP($id);
        }

        $s = $this->_model->submit($data);
        
        if ($s) {
            $this->response($s, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }
	
	public function sid_get(){
        $output = $this->_model->sid( $this->get('ObjID') ,  $this->get('PartnerID'), $this->get('ObjType') );
        $this->response($output, 200);
    }
	
	public function objtype_list_get(){
        $data = $this->_model->getObjTypeList();
        $this->response($data, 200);
    }
	
	public function fetch_supplyorg_get(){
        $output = $this->_model->fetch_supplyorg( $this->get('SupplychainID') );
        $this->response($output, 200);
    }
	 
	public function objectid_get(){
        $this->load->language('general', $_SESSION['language']);
        $DistrictID = $this->get('DistrictID');
		$PartnerID = $this->get('PartnerID');
        $SupplyChainID = $this->get('SupplyChainID');
        $RefineryID = $this->get('RefineryID');

        switch ($this->get('ObjType')) {        
            case 'trader':
                $data = $this->_model->getObjIdTrader($SupplyChainID, $PartnerID, $DistrictID);
            break;  
            case 'mill':
                $data = $this->_model->getObjIdMill($SupplyChainID, $PartnerID);
            break;
            case 'agent':
                $data = $this->_model->getObjIdAgent($SupplyChainID, $PartnerID);
            break; 
            case 'refinery':
                $data = $this->_model->getObjIdRefinery($SupplyChainID, $RefineryID);
            break;
            case 'farmer_group':
                $data = $this->_model->getObjIdFarmerGroup($SupplyChainID, $PartnerID);
            break;
            case 'cooperative':
                $data = $this->_model->getObjIdCooperative($SupplyChainID, $DistrictID);
            break;
            default:
                $data = $this->_model->getObjIdKCPBulking($this->get('ObjType'));
        }
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }
	
	public function del_post(){
        // var_dump($data);exit;
		$data = $this->post(NULL);  
        $output = array('success' => false);  
        $output = $this->_model->delete($data['SupplychainID']); 
        $this->response($output, 200);
    }
	
	/*
    public function fetch_get() {
        if($this->get('sort')) {
            $sorting      = json_decode($this->get('sort'));
            $this->sortingField = $sorting[0]->property;
            $this->sortingDir   = $sorting[0]->direction;
        }

        //parameters
        if($this->get('start')){
            $this->recordStart  = (int)$this->get('start');
            $this->recordLimit  = (int)$this->get('limit');
        }
        
        $data  = $this->_model->get_data($this->recordStart, $this->recordLimit, $this->sortingField, $this->sortingDir);
        if($data){
            return $this->response(array('success' => true, 
                                         'message' => 'Data Berhasil Ditampilkan',
                                         'total' => $data['total'], 
                                         'data' => $data['data']
                                         ),  200);
        }
        return $this->response($this->_output,401);
    }
	
    public function submit_post(){
        ini_set('display_errors',true);
        error_reporting(E_ALL);
        $data = $this->post(NULL);
        if($data){
            $batch = $this->_model->submit($data);
            if($batch){ 
                return $this->response($batch);
            }
        }else{
            return $this->response(array('success' => false, 'error' => 'Data post empty !'),200);
        }
    }

    public function del_DELETE($id=0){
        $output = array('success' => false);

        if((int)$id > 0) {
            $output = $this->_model->delete($id);
        }

        $this->response($output, 200);
    }

    public function obj_get($id=0){
        $output = array('success' => false);

        if((int)$id > 0) {
            $output = $this->_model->obj($id);
        }

        $this->response($output, 200);
    }

    public function sid_get(){
        $output = $this->_model->sid();
        $this->response($output, 200);
    }
    public function sidChild_get(){
        $output = $this->_model->sid_child($this->get('sid'));
        $this->response($output, 200);
    }

   
	
	*/

}