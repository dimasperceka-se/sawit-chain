<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Group extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Mgroup');
    }

    function data_get() {
        $data = $this->Mgroup->readData();
        if($data) $this->response($data, 200); // 200 being the HTTP response code
        else $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function data_post() {
        if(!$this->post('GroupName')) $this->response(NULL, 400);
        $add = $this->Mgroup->createData($this->post('GroupName'), $this->post('GroupDescription'),$this->post('GroupUnitId'));
        if($add) $this->response($unit, 200); // 200 being the HTTP response code
        else $this->response(array('error' => 'Unit could not be found'), 404);
    }

    function unit_put() {
        if(!$this->put('GroupId')) $this->response(NULL, 400);
        $update = $this->Mgroup->updateData($this->post('GroupName'), $this->post('GroupDescription'),$this->post('GroupUnitId'),
            $this->post('GroupUnitId'));
        if($update) $this->response($unit, 200); // 200 being the HTTP response code
        else $this->response(array('error' => 'Unit could not be found'), 404);
    }

    function unit_delete() {
        if(!$this->deleteData('GroupUnitId')) $this->response(NULL, 400);
        $delete = $this->Mgroup->deleteGroup($this->deleteData('GroupUnitId'));
        if($unit) $this->response($unit, 200); // 200 being the HTTP response code
        else $this->response(array('error' => 'Unit could not be delete'), 404);
    }

    function unit_get() {
        $data = $this->Mgroup->readUnit();
        if($data) $this->response($data, 200); // 200 being the HTTP response code
        else $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }
}
