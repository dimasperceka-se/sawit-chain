<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cooperatives extends SS_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {

        $api = $this->config->item('api');
        $data['js'] = 'cooperative_report';
        $data['action'] = array('report' => $api.'/jasperctrl/report/');

        $this->LoadView($data);
    }


}
