<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Sce extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('sce/mfarmer');
    }

    function farmer_sces_get() {
        $data = $this->mfarmer->readDatas($this->get('prov'),$this->get('key'),$this->get('dist'),$this->get('subdist'),$this->get('start'),$this->get('limit'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function farmer_sce_get() {
        $data = $this->mfarmer->readData($this->get('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function farmer_sce_by_id_get(){
        $data = $this->mfarmer->farmerSceById($this->get('sce_id'));
        if($data['id'] != ""){
            $this->response($data, 200);
        }else{
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
        }
    }

    function farmer_sce_farmers_get() {
        $data = $this->mfarmer->readFarmers($this->get('prov'),$this->get('kab'),$this->get('query'),
            $this->get('start'), $this->get('limit'));
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function farmer_sce_staff_get() {
       $data = $this->mfarmer->readStaff($this->get('id'));
        $this->response($data, 200);
    }

    function farmer_sce_staff_post() {
        if(!$this->post('StaffName')) $this->response(NULL, 400);
       $data = $this->mfarmer->createStaff($this->post('SceID'),$this->post('StaffName'),$this->post('Position'),
            $this->post('Phone'),$this->post('Email'),$this->post('StaffBirthday'),$this->post('StaffGender'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Data could not be found'), 404);
    }

    function farmer_sce_staff_put() {
        if(!$this->put('SceID')) $this->response(NULL, 400);
        $data = $this->mfarmer->updateStaff($this->put('StaffName'),$this->put('Position'),$this->put('Phone'),$this->put('Email'),
            $this->put('StaffBirthday'),$this->put('StaffGender'),$this->put('StaffID'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Data could not be found'), 404);
    }

    function farmer_sce_staff_delete() {
        if(!$this->delete('id')) $this->response(NULL, 400);
        $data = $this->mfarmer->deleteStaff($this->delete('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Data could not be delete'), 404);
    }

    function farmer_sce_post() {
        if(!$this->post('FarmerID')) $this->response(NULL, 400);
        $data = $this->mfarmer->createSce($this->post('FarmerID'),$this->post('Latitude'),$this->post('Longitude'));
        if($data){
            if($this->post('formFrom') == "sce"){
                //set session sce
                $_SESSION['filter_id'] = $data['sce_id'];
            }

            $this->response($data, 200);
        }
        else $this->response(array('error' => 'Data could not be found'), 404);
    }

    function farmer_sce_put() {
        if(!$this->put('sce_id')) $this->response(NULL, 400);
        $data = $this->mfarmer->updateSce($this->put('FarmerID'),$this->put('Latitude'),$this->put('Longitude'),$this->put('sce_id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Data could not be found'), 404);
    }

    function farmer_sce_delete() {
        if(!$this->delete('id')) $this->response(NULL, 400);
        $data = $this->mfarmer->deleteSce($this->delete('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Data could not be delete'), 404);
    }

    public function sce_list_get()
    {
        $province = $this->get('ProvinceID');
        $district = $this->get('DistrictID');
        $subdistrict = $this->get('SubDistrictID');
        $data = $this->mfarmer->listSCE($province, $district, $subdistrict);
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any sce!'), 200);
    }
}
