<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Shu extends SS_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['js'] = 'coop_shu';
        $api = $this->config->item('api');
        $data['action'] = array(
            'api' => $api,
            'baseurl' => base_url(),
            'crud' => $api . '/coop_cashbox',
            'act_add' => !$this->system->CekAksi('add'),
            'act_update' => !$this->system->CekAksi('update'),
            'act_delete' => !$this->system->CekAksi('delete')
        );
        $this->LoadView($data);
    }

}