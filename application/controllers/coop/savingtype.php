<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Savingtype extends SS_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['js'] = 'savingtype';
        $api = $this->config->item('api');
        $data['action'] = array(
            'api' => $api,
            'baseurl' => base_url(),
            'coadatas'=> $api . '/coa/fin_coas',
            'crud' => $api . '/savingtype/coop_savingtype',
            'member' => $api . '/common/getcombo',
            'act_add' => !$this->system->CekAksi('add'),
            'act_update' => !$this->system->CekAksi('update'),
            'act_delete' => !$this->system->CekAksi('delete')
        );
        $this->LoadView($data);
    }

}