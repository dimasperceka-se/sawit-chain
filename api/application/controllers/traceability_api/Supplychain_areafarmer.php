<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Supplychain_areafarmer extends REST_Controller {
   

    public function __construct() {
        parent::__construct();
        date_default_timezone_set('UTC');
        $this->load->model('traceability_api/m_supplychain_areafarmer','_model');
    }
	
    public function fetch_get(){
        $sorting = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $data = $this->_model->get_data($this->get('SupplychainID'),  $this->get('textSearch'),  $this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        if($data) $this->response($data, 200); else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    public function formAccessFarmer_get(){
        $SupplychainFarmerID = $this->get("SupplychainFarmerID");

        $data = $this->_model->getFormAccessFarmer($SupplychainFarmerID);
        $this->response($data,200);
    }

    public function formAccessFarmer_post(){
        $post = $this->_model->postFormAccessFarmer($this->post());

        $this->response($post,200);
    }

    public function generate_farmer_access_post(){
        $post = $this->_model->generate_farmer_access($this->post('SupplychainID'));

        $this->response($post,200);
    }

    public function generate_farmer_access_all_get(){
        $post = $this->_model->generate_farmer_access_all();

        $this->response($post,200);
    }
	
	public function fetchallFarmer_get(){
        $sorting = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';
		
		//get param
        $pSearch = array(
            'DateStart' => $this->get('DateStart'),
			'DateEnd'   => $this->get('DateEnd'),
			'prov'      => $this->get('ComboProvince'),
            'kab'       => $this->get('ComboDistrict'),
            'kec'       => $this->get('ComboSubDistrict'),
            'desa'      => $this->get('ComboVillage'),
            'textSearch'      => $this->get('SearchName')
        );
		
        $data = $this->_model->get_AllFarmerdata($this->get('SupplychainID'), $pSearch,  $this->get('start'),$this->get('limit'),$sortingField,$sortingDir,$this->get('Role'));
        if($data) $this->response($data, 200); else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }
	
	
	public function sentcheckeddata_post(){ 
        $data = $this->post(NULL);
        if($data){
            $s = $this->_model->submitchecked($data);
            if($s){ 
                return $this->response($s);
            }
        }else{
            return $this->response(array('success' => false, 'error' => 'Data post empty !'),200);
        }
    }
	
     

    public function del_post(){ 
        $data = $this->post(NULL);
		 if($data){
            $output = $this->_model->delete($this->post('SupplychainFarmerID'));
         }

        $this->response($output, 200);
    } 
}