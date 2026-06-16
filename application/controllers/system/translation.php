<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Translation extends SS_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['js'] = 'translation';
        $api = $this->config->item('api');
        $data['action'] = array(
            'baseurl'=>base_url(),
            'crud' => $api . '/translation/core_translation',
            'header' => $api . '/translation/header_translation',
            'validate' => $api . '/translation/validate_translation',
            'act_add' => !$this->system->CekAksi('add')?'hide-icon':'',
            'act_update' => !$this->system->CekAksi('update')?'hide-icon':'',
            'act_delete' => !$this->system->CekAksi('delete')?'hide-icon':''
        );
        $this->LoadView($data);
    }

}

