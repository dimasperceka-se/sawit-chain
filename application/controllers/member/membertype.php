<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Membertype extends SS_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['js'] = 'membertype';
        $api = $this->config->item('api');
        $data['action'] = array(
            'baseurl'=>base_url(),
            'crud' => $api . '/membertype/coop_membertype',
            'act_add' => !$this->system->CekAksi('add'),
            'act_update' => !$this->system->CekAksi('update'),
            'act_delete' => !$this->system->CekAksi('delete'),
            'coadatas'=> $api . '/coa/fin_coas',
        );
        $this->LoadView($data);
    }

}