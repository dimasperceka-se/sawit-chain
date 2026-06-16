<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Saving extends SS_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['js'] = 'saving';
        $api = $this->config->item('api');
        $data['action'] = array(
            'baseurl_api' => str_replace('index.php', '', $api),
            'crud' => $api . '/transaction/coop_transaction',
            'save_withdrawal' => $api.'/transaction/add_withdrawal',
            'cooplimit' => $api . '/transaction/coop_limit',
            'approval' => $api . '/transaction/coop_approval',
            'savingtype' => $api . '/saving/combo_savingtype',
            'member' => $api . '/saving/combo_member',
            'status' => $api . '/saving/combostatus',
            'all_member' => $api . '/common/all_members',
            'getmembersaving' => $api . '/transaction/getmembersaving',
            'lasttrans'=>$api . '/transaction/lasttrans', 
            'act_add' => !$this->system->CekAksi('add'),
            'act_update' => !$this->system->CekAksi('update'),
            'act_delete' => !$this->system->CekAksi('delete')
        );
        $this->LoadView($data);
    }

}