<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Partner_mapping extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('basic/mpartner_mapping');
    }

    function tree_menus_get() {
        $data = $this->mpartner_mapping->readTraining($this->get('PartnerID'));
        if($data) $this->response(array('success' => true, 'data' => $data), 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function tree_menus_put() {
        if(!$this->put('PartnerID')) $this->response(NULL, 400);
        $data = $this->mpartner_mapping->updateTraining($this->put('PartnerParentID'),$this->put('PartnerID'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Data could not be found'), 404);
    }

    function tree_menu_get() {
        $data = $this->mpartner_mapping->readTrainings($this->get('start'),$this->get('limit'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function tree_menu_parent_get() {
        $root = array(array('id' => '0', 'label' => '.'));
        $data = $this->mpartner_mapping->getTrainingParent();
        if($data) $this->response(array_merge($root, $data), 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }
}
