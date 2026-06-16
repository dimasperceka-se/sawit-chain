<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Branch extends SS_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['js'] = 'bank/branch';
        $api = $this->config->item('api');
        $data['action'] = array(
            'crud'          => $api.'/bank/branch',
            'staff'         => $api.'/bank/branch_staff',
            'bank'          => $api.'/bank/banglist',
            'province'      => $api.'/bank/province',
            'district'      => $api.'/bank/district',
            'subdistrict'   => $api.'/bank/subdistrict',
            'village'       => $api.'/bank/village',
            'group'         => $api.'/bank/group',
            'user_check'    => $api.'/system/cek_username',
            'act_index'     => !$this->system->CekAksi('index'),
            'act_add'       => !$this->system->CekAksi('add'),
            'act_update'    => !$this->system->CekAksi('update'),
            'act_delete'    => !$this->system->CekAksi('delete')
        );
        $this->LoadView($data);
    }

}

