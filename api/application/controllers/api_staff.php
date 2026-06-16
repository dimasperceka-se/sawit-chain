<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Staff extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('staff/mextension');
    }

    function extensions_get() {
        $extensions = $this->mextension->readExtensions($this->get('start'),$this->get('limit'));
        if($extensions) $this->response($extensions, 200);
        else $this->response(array('error' => 'Couldn\'t find any extensions!'), 404);
    }

    function extension_get() {
        if(!$this->get('id')) $this->response(NULL, 400);
        $extension = $this->mextension->readExtension($this->get('id'));
        if($extension) $this->response($extension, 200);
        else $this->response(array('error' => 'Extension could not be found'), 404);
    }

    function extension_post() {
        //if(!$this->post('name')) $this->response(NULL, 400);
        $extension = $this->mextension->createExtension($this->post('PersonID'),$this->post('WritingAwal'),$this->post('WritingAkhir'),$this->post('BallotAwal'),$this->post('BallotAkhir'),$this->post('InstitutionID'),$this->post('PositionID'));
        if($extension) $this->response($extension, 200);
        else $this->response(array('error' => 'Extension could not be found'), 404);
    }

    function extension_put() {
        if(!$this->put('id')) $this->response(NULL, 400);
        $extension = $this->mextension->updateExtension($this->put('PersonID'),$this->put('WritingAwal'),$this->put('WritingAkhir'),$this->put('BallotAwal'),$this->put('BallotAkhir'),$this->put('InstitutionID'),$this->put('PositionID'),$this->put('id'));
        if($extension) $this->response($extension, 200);
        else $this->response(array('error' => 'Extension could not be found'), 404);
    }

    function extension_delete() {
        if(!$this->delete('id')) $this->response(NULL, 400);
        $extension = $this->mextension->deleteExtension($this->delete('id'));
        if($extension) $this->response($extension, 200);
        else $this->response(array('error' => 'Extension could not be delete'), 404);
    }

    
}
