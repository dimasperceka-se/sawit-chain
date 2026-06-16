<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Farmers extends SS_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->lang->load('tools');
        $data['lang'] = $this->lang->language;
        $data['js'] = 'tools/farmers';
        $api = $this->config->item('api');

        $data['action'] = array(
            'api_base_url' => $this->config->item('api_base_url'),
            'base_url' => base_url(),
            'api' => $api
        );
        $this->LoadView($data);
    }

}
