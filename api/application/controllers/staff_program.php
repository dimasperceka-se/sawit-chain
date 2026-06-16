<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Staff_program extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Mstaff_program');
    }

    function data_get() {
        $data = $this->Mstaff_program->readData();
        if($data) $this->response($data, 200); // 200 being the HTTP response code
        else $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function data_post() {
        if(!$this->post('UserName')) $this->response(NULL, 400);
        $add = $this->Mpartner->createData($this->post('UserName'), $this->post('UserRealName'),$this->post('UserActive'));
        if($add) $this->response($add, 200); // 200 being the HTTP response code
        else $this->response(array('error' => 'Unit could not be found'), 404);
    }

    function unit_put() {
        if(!$this->put('UserId')) $this->response(NULL, 400);
        $update = $this->Mpartner->updateData($this->post('UserName'), $this->post('UserRealName'),$this->post('UserActive'),
            $this->post('UserUnitId'));
        if($update) $this->response($update, 200); // 200 being the HTTP response code
        else $this->response(array('error' => 'Unit could not be found'), 404);
    }

    function unit_delete() {
        if(!$this->deleteData('id')) $this->response(NULL, 400);
        $delete = $this->Mpartner->deleteUser($this->deleteData('id'));
        if($unit) $this->response($delete, 200); // 200 being the HTTP response code
        else $this->response(array('error' => 'Unit could not be delete'), 404);
    }

    function group_get() {
        $data = $this->Mpartner->readUnit();
        if($data) $this->response($data, 200); // 200 being the HTTP response code
        else $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }
}
