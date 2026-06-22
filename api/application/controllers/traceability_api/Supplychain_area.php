<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Supplychain_area extends REST_Controller {
   

    public function __construct() {
        parent::__construct();
        date_default_timezone_set('UTC');
        $this->load->model('traceability_api/m_supplychain_area','_model');
    }
    public function fetch_get(){
        $sorting = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $data = $this->_model->get_data($this->get('SupplychainID'),  $this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        if($data) $this->response($data, 200); else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }
	
    public function submit_post(){ 
        $data = $this->post(NULL);
        if($data){
            $s = $this->_model->submit($data);
            if($s){ 
                return $this->response($s);
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
}