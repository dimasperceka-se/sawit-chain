<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Supplychain_package extends REST_Controller {
    
    public $_output = array('success' => false, 'error' => 'Data error'); //response data
    public $recordStart  = 0;
    public $recordLimit  = 12;
    public $sortingField = 'PackageID';
    public $sortingDir   = 'DESC';
    public $searchKey    = '';
    public $weekNew      = false;
    public $weekModified = false;

    public function __construct() {
        parent::__construct();
        date_default_timezone_set('UTC');
        $this->load->model('traceability_api/m_supplychain_package','_model');
    }
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
        
        $data  = $this->_model->get_data($this->get('SupplychainID'), $this->recordStart, $this->recordLimit, $this->sortingField, $this->sortingDir);
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

}