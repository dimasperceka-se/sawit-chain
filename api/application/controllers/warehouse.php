<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Warehouse extends REST_Controller {

    public function __construct() {
        $this->file = $_FILES;
        parent::__construct();
        $this->load->model('warehouse/mwarehouse');
    }

    public function warehouse_list_get()
    {
        $province = $this->get('ProvinceID');
        $district = $this->get('DistrictID');
        $subdistrict = $this->get('SubDistrictID');
        $data = $this->mwarehouse->listWarehouse($province, $district, $subdistrict);
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any sce!'), 200);
    }

}