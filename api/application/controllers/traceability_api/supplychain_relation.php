<?php defined('BASEPATH') or exit('No direct script access allowed');

class Supplychain_relation extends REST_Controller
{

    public function __construct(){
        parent::__construct();
        $this->load->model('traceability_api/msupplychain_relation', '_model');
    }

    public function getRelation_get(){
        $data = $this->_model->generateSupplychainRelation($this->get());
        return $this->response($data);
    }
}
