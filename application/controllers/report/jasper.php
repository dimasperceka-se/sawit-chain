<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Jasper extends SS_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        
        $api = $this->config->item('api');
        $data['js'] = 'jasper';
        $data['action'] = array('report' => $api.'/jasperctrl/report/');
        
        $this->LoadView($data);
    }
    

}
