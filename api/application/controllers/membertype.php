<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Membertype extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('member/mmembertype');
    }

    function coop_membertypes_get() {
        $key = $this->get('key');
        $membertypes = $this->mmembertype->readMemberTypes($key, $this->get('start'), $this->get('limit'));
        if ($membertypes)
            $this->response($membertypes, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any MemberTypes!'), 404);
    }

    function coop_membertype_get() {
        if (!$this->get('id'))
            $this->response(NULL, 400);
        $membertype = $this->mmembertype->readMemberType($this->get('id'));
        if ($membertype)
            $this->response(array('success' => true, 'data' => $membertype), 200);
        else
            $this->response(array('error' => 'MemberType could not be found'), 404);
    }

    function coop_membertype_post() {
        $membertype = $this->mmembertype->createMemberType(
                $this->post('coopID'), $this->post('typeCode'), $this->post('typeName'), $this->post('typeMaxProfit'), $this->post('typeSimPokokAmount'), $this->post('typeSimPokokPeriod'), $this->post('typeSimWajibAmount'), $this->post('typeSimWajibPeriod'), $this->post('CoaRegMemberTypeID'),$this->post('RegistrationFee'),$_SESSION['userid']);

        if ($membertype) {
            $this->response($membertype, 200);
        } else {
            $this->response(array('error' => 'MemberType could not be added'), 404);
        }
    }

    function coop_membertype_put() {
        if (!$this->put('typeID') || $this->put('typeID') == ''){
            $this->response(NULL, 400);
        }
        $update = $this->mmembertype->updateMemberType($this->put('typeID'),$this->put());
        if ($update)
            $this->response($update, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'MemberType could not be edited'), 404);
    }

    function coop_membertype_delete() {
        if (!$this->delete('id'))
            $this->response(NULL, 400);
        $delete = $this->mmembertype->deleteMemberType($this->delete('id'));
        if ($delete)
            $this->response($delete, 200);
        else
            $this->response(array('error' => 'MemberType could not be deleted'), 404);
    }

}
