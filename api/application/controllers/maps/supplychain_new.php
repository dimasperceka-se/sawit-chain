<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Supplychain_new extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('map/msupplychain_new', 'msupplychain');
    }

    public function supplychain_get()
    {
        $id    = $this->get('id');
        $start = $this->get('start');
        $end   = $this->get('end');

        $data = $this->msupplychain->getSupplyChain($start, $end, $id);
        
        $this->response($data, 200);
    }

    public function supply_profile_mill_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $wh     = $this->get('warehouse');
        $data = $this->msupplychain->getSupplyProfileMill($id, $start, $end, $wh);

        $this->response($data, 200);
    }

    public function supply_profile_do_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $cert   = $this->get('certification');
        $wh     = $this->get('warehouse');
        $data = $this->msupplychain->getSupplyProfileDo($id, $start, $end, $wh, $cert);

        $this->response($data, 200);
    }

    public function supply_profile_farmer_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $cert   = $this->get('certification');
        $wh     = $this->get('warehouse');
        $parent = $this->get('parent');
        $data = $this->msupplychain->getSupplyProfileFarmer($id, $start, $end, $wh, $cert, $parent);

        $this->response($data, 200);
    }

}