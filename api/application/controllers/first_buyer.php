<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class First_buyer extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('certification/mfirst_buyer');
    }
    
    public function data_get(){
        $data = $this->mfirst_buyer->readFirstBuyers($this->get('key'), $this->get('start'), $this->get('limit'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array(), 200);
    }
    
    public function partners_get(){
        $data = $this->mfirst_buyer->listPartners($this->get('PartnerID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any roles!'), 404);
        }
    }
    
    public function set_partner_get(){
        $data = $this->mfirst_buyer->readpartnerDetail($this->get('PartnerID'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array(), 200);
    }
    
    function data_post() {
        if (!$this->post('FirstBuyerPartnerID'))
            $this->response(NULL, 400);
        $data = $this->mfirst_buyer->createFirstBuyer($this->post('FirstBuyerPartnerID'), $_SESSION['userid']);
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Data could not be found'), 404);
    }
    
    public function detail_get(){
        $data = $this->mfirst_buyer->readFirstBuyerDetail($this->get('FirstBuyerID'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array(), 200);
    }
    
    function data_put() {
        if (!$this->put('FirstBuyerID'))
            $this->response(NULL, 400);
        $data = $this->mfirst_buyer->updateFirstBuyer($this->put('FirstBuyerPartnerID'), $_SESSION['userid'], $this->put('FirstBuyerID'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Data could not be found'), 404);
    }
    
    function data_delete() {
        if (!$this->delete('FirstBuyerID'))
            $this->response(NULL, 400);
        $data = $this->mfirst_buyer->deleteFirstBuyer($_SESSION['userid'], $this->delete('FirstBuyerID'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Data could not be found'), 404);
    }
}
