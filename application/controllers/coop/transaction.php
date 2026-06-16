<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Transaction extends SS_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['js'] = 'coop_transaction';
        $api = $this->config->item('api');
        $data['action'] = array(
            'crud' => $api . '/transaction/coop_transaction',
            'transaction' => $api . '/transaction/combo_transactiontype',
            'cashsource' => $api . '/transaction/combo_cashsource',
            'membersaving' => $api . '/transaction/combo_membersaving',
            'act_add' => !$this->system->CekAksi('add'),
            'act_update' => !$this->system->CekAksi('update'),
            'act_delete' => !$this->system->CekAksi('delete')
        );
        $this->LoadView($data);
    }

}