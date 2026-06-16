<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Salary_conf extends SS_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['js'] = 'coop_salary_conf';
        $api = $this->config->item('api');
        $data['action'] = array(
            'api' => $api,
            'act_add' => !$this->system->CekAksi('add'),
            'act_update' => !$this->system->CekAksi('update'),
            'act_delete' => !$this->system->CekAksi('delete')
        );
        $this->LoadView($data);
    }

}