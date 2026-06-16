<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Import extends SS_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index($id=null) {
        $data['js'] = 'member_import';
        $api = $this->config->item('api');
        $data['action'] = array(
            'api' => $api,
            'crud' => $api . '/member/coop_member_import_tmp',
            'save' => $api . '/member/coop_member_import_save',
            'act_add' => !$this->system->CekAksi('add'),
            'act_update' => !$this->system->CekAksi('update'),
            'act_delete' => !$this->system->CekAksi('delete')
        );
        $this->LoadView($data);
    }

}