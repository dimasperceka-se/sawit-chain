<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Coa extends SS_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['js'] = 'coa';
        $api = $this->config->item('api');
        $data['action'] = array(
            'crud' => $api . '/coa/fin_coa',            
            'coadatas'=> $api . '/coa/fin_coas',
            'baseurl'=>base_url(),
            'crudcellgrid' => $api . '/coa/fin_coa_celledit', //dasdsad
            'qwe'=>'ad',
            'tree' => $api . '/coa/tree',
            'closingdate' => $api . '/coa/combo_closingdate',
            'act_add' => !$this->system->CekAksi('add'),
            'act_update' => !$this->system->CekAksi('update'),
            'act_delete' => !$this->system->CekAksi('delete')
        );
        $this->LoadView($data);
    }

}