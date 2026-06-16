<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Certification_holder extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('certification/mcertification_holder');
    }
    
    public function holder_type_get(){
        $data = $this->mcertification_holder->listHolderType();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any roles!'), 404);
        }
    }
    
    public function data_get(){
        $data = $this->mcertification_holder->readCertificationHolders($this->get('holderType'), $this->get('key'), $this->get('start'), $this->get('limit'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array(), 200);
    }
    
    public function holders_get(){
        $data = array();
        if($this->get('OrgType') == 'farmer_group'){
            $data = $this->mcertification_holder->listFarmerGroup();
        }

        if($this->get('OrgType') == 'cooperative'){
            $data = $this->mcertification_holder->listCooperative();
        }

        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any roles!'), 404);
        }
    }
    
    public function certification_programs_get(){
        $data = $this->mcertification_holder->listPrograms();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any roles!'), 404);
        }
    }
    
    function data_post() {
        if (!$this->post('CertProgID'))
            $this->response(NULL, 400);
        $data = $this->mcertification_holder->createCertificationHolder($this->post('ObjType'), $this->post('ObjID'), $this->post('CertProgID'), $this->post('GIPNumber'), $this->post('CertProgMemberID'),  $this->post('CertProgMemberDate'), $_SESSION['userid']);
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Data could not be found'), 404);
    }
    
    public function detail_get(){
        $data = $this->mcertification_holder->readCertificationHolderDetail($this->get('CertHolderID'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array(), 200);
    }
    
    function data_put() {
        if (!$this->put('CertHolderID'))
            $this->response(NULL, 400);
        $data = $this->mcertification_holder->updateCertificationHolder($this->put('ObjType'), $this->put('ObjID'), $this->put('CertProgID'), $this->put('GIPNumber'), $this->put('CertProgMemberID'),  $this->put('CertProgMemberDate'), $_SESSION['userid'], $this->put('CertHolderID'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Data could not be found'), 404);
    }
    
    function data_delete() {
        if (!$this->delete('CertHolderID'))
            $this->response(NULL, 400);
        $data = $this->mcertification_holder->deleteCertificationHolder($_SESSION['userid'], $this->delete('CertHolderID'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Data could not be found'), 404);
    }
    

}
