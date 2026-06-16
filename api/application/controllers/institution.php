<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Institution extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('institution/minstitution');
    }

    function instituts_get() {
        $institutions = $this->minstitution->readInstitutions($this->get('start'),$this->get('limit'));
        if($institutions) $this->response($institutions, 200);
        else $this->response(array('error' => 'Couldn\'t find any institutions!'), 404);
    }

    function institut_get() {
        if(!$this->get('id')) $this->response(NULL, 400);
        $institution = $this->minstitution->readInstitution($this->get('id'));
        if($institution) $this->response($institution, 200);
        else $this->response(array('error' => 'Institution could not be found'), 404);
    }

    function institut_post() {
        if(!$this->post('InstitutionName')) $this->response(NULL, 400);
        $institution = $this->minstitution->createInstitution($this->post('InstitutionName'),$this->post('PrivatePublic'));
        if($institution) $this->response($institution, 200);
        else $this->response(array('error' => 'Institution could not be found'), 404);
    }

    function institut_put() {
        if(!$this->put('id')) $this->response(NULL, 400);
        $institution = $this->minstitution->updateInstitution($this->put('InstitutionName'),$this->put('PrivatePublic'),$this->put('id'));
        if($institution) $this->response($institution, 200);
        else $this->response(array('error' => 'Institution could not be found'), 404);
    }

    function institut_delete() {
        if(!$this->delete('id')) $this->response(NULL, 400);
        $institution = $this->minstitution->deleteInstitution($this->delete('id'));
        if($institution) $this->response($institution, 200);
        else $this->response(array('error' => 'Institution could not be delete'), 404);
    }

    
}
