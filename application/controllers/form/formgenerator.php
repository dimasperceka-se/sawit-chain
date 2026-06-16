<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class FormGenerator extends SS_Controller {

    public function __construct() {
        parent::__construct(1);
    }

    public function index($id=null) {
        $data['js'] = 'generatorForm';
        $api = $this->config->item('api');
        $data['action'] = array(
            'api' => $api,
        );
        $this->LoadView($data);
    }

    

}