<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Report_transaction extends REST_Controller {
   
	
    public function __construct() {
        parent::__construct();
        date_default_timezone_set('UTC');
        $this->load->model('traceability_api/m_report_transaction','_model');
    }
	
	public function fetch_get(){
        $sorting = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';
		
		$InputForm = $this->get(NULL);  
        $data = $this->_model->get_data( $InputForm, $this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
		$xls= $this->get('xls');
		//print_r($data['data']);die;
		if ($data) { 
            if ($xls ==  true ) {
               ini_set('memory_limit',-1);
                header("Content-type: application/octet-stream");
                header("Content-Disposition: attachment; filename=exceldata.xls");
                header("Pragma: no-cache");
                header("Expires: 0"); 
                $this->load->view('report_transaction', array('data' => $data['data'])); 
            } else {
                $this->response($data, 200);
            }

        } else {
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
		 
    }

    public function ComboMill_get(){
        $output = $this->_model->comboMill();
        $this->response($output, 200);
    }

    public function ComboAgent_get(){
        $output = $this->_model->comboAgent($this->get());
        $this->response($output, 200);
    }
	 
}