<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Email extends SS_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['js'] = 'email';
        $api = $this->config->item('api');
        $data['action'] = array(
            'crud' => $api . '/email/emaillog',
            'resend' => $api . '/email/resend',
            'email' => $api . '/email/comboemail',
            'act_add' => !$this->system->CekAksi('add'),
            'act_update' => !$this->system->CekAksi('update'),
            'act_delete' => !$this->system->CekAksi('delete')
        );
        $this->LoadView($data);
    }

}

