<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Savingtype extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('coop/msavingtype', '_model');
    }

    function coop_savingtypes_get() {
        $start = $this->input->get('start');
        $limit = $this->input->get('limit');
        $sort = '';
        $dir = 'DESC';

        $filter = array();

        $data = $this->_model->readSavingTypes($start,$limit,$sort,$dir,$filter);

        $this->_num = 200;
        $this->_output = array('success' => true, 'data' => $data['data'], 'total' => $data['total']);

        return $this->response($this->_output,  $this->_num);
    }

    function coop_savingtype_get() {
        if (!$this->get('id'))
            $this->response(NULL, 400);
        $savingtype = $this->_model->readSavingType($this->get('id'));
        if ($savingtype)
            $this->response(array('success' => true, 'data' => $savingtype, 'total'=> 1), 200);
        else
            $this->response(array('error' => 'SavingType could not be found'), 200);
    }

    function coop_savingtype_post() {
        $data = $this->post();
        
        if(strlen($data['SavingTypeID']) >= 1){
            $id = $this->_model->editSavingType($this->post());
        }else{
            $id = $this->_model->addSavingType($this->post());
        }

        $this->_num = 200;
        $this->_output = array('success' => true, 'data' => array('id'=>$id, 'msg'=>'Data has been saved'), 'total' => 1);

        return $this->response($this->_output,  $this->_num); 
    }

    function coop_savingtype_put() {

        if (!$this->put('id')){
            $this->response(NULL, 400);
        }
        $update = $this->msavingtype->updateSavingType($this->put('id'),$this->put());
               
        if ($update){
            $this->response(array('success' => $update), 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'SavingType could not be edited'), 404);
        }
    }

    function coop_savingtype_delete() {
        if (!$this->delete('id'))
            $this->response(NULL, 400);
        $delete = $this->_model->deleteSavingType($this->delete('id'));
        if ($delete)
            $this->response($delete, 200);
        else
            $this->response(array('error' => 'SavingType could not be deleted'), 404);
    }
    
    public function getcombomembertype_get() {
        
        $data = $this->_model->getComboMemberType();
        
        $this->_num = 200;
        $this->_output = array('success' => true, 'data' => $data['data'], 'total' => $data['total']);

        return $this->response($this->_output,  $this->_num);
    }

    function getCOA_get(){
        $start = $this->input->get('start');
        $limit = $this->input->get('limit');
        $sort = '';
        $dir = 'DESC';

        $filter = array();

        $data = $this->_model->readCOA($start,$limit,$sort,$dir,$filter);
        $this->_num = 200;
        $this->_output = array('success' => true, 'data' => $data['data'], 'total' => $data['total']);

        return $this->response($this->_output,  $this->_num);   
    }

}
