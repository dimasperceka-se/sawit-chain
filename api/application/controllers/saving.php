<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Saving extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('coop/msaving');
    }

    function coop_savings_get() {
        $key = $this->get('key');
        $status = $this->get('status');
        
        $savings = $this->msaving->readSavings($key, $status, $this->get('start'), $this->get('limit'));
        if ($savings)
            $this->response($savings, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any Savings!'), 404);
    }

    function coop_saving_get() {
        if (!$this->get('id'))
            $this->response(NULL, 400);
        $saving = $this->msaving->readSaving($this->get('id'));
        if ($saving)
            $this->response($saving, 200);
        else
            $this->response(array('error' => 'Saving could not be found'), 404);
    }

    function coop_saving_post() {
        if (!$this->post('memberID'))
            $this->response(NULL, 400);
        $saving = $this->msaving->createSaving($this->post('memberID'), $this->post('savingTypeID'), $this->post('memberSavingNo'), $this->post('memberSavingRemark'), $_SESSION['userid']);

        if ($saving) {
            $this->response($saving, 200);
        } else {
            $this->response(array('error' => 'Saving could not be added'), 404);
        }
    }

    function coop_saving_put() {
        if (!$this->put('id'))
            $this->response(NULL, 400);
        $update = $this->msaving->updateSaving($this->put('memberID'), $this->put('savingTypeID'), $this->put('memberSavingNo'), $this->put('memberSavingRemark'), $_SESSION['userid'], $this->put('id'));
        if ($update)
            $this->response($update, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Saving could not be edited'), 404);
    }

    function coop_saving_delete() {
        if (!$this->delete('id'))
            $this->response(NULL, 400);
        $delete = $this->msaving->deleteSaving($this->delete('id'));
        if ($delete)
            $this->response($delete, 200);
        else
            $this->response(array('error' => 'Saving could not be deleted'), 404);
    }

    function coop_saving_member_get() {
        $data = $this->msaving->getMember($this->get('id'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any Member!'), 404);
    }

    function coop_saving_savingtype_get() {
        $data = $this->msaving->getSavingType($this->get('id'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any Member!'), 404);
    }

    function combo_savingtype_get() {
        $data = $this->msaving->getComboSavingType();
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any Saving Type!'), 404);
    }

    function combo_member_get() {
        $data = $this->msaving->getComboMember();
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any Saving Type!'), 404);
    }

    function combostatus_get() {
        $data = $this->msaving->getComboStatus();
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any Status!'), 404);
    }

}
