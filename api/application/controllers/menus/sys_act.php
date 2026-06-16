<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Sys_act extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('system/msys_act');
    }


    public function show_sys_act_get()
    {
        $data  = $this->msys_act->getSysAct($this->get());
        $this->response($data, 200);
    }


    
}