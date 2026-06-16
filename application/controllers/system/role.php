<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Role extends SS_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['js'] = 'role';
        $api = $this->config->item('api');
        $data['action'] = array(
            'crud'          => $api.'/system/role',
            'object_list'   => $api.'/system/objectlist',
            'group_list'    => $api.'/system/grouplist',
            'role_group'    => $api.'/system/role_group',
            'act_add'=> !$this->system->CekAksi('add'),
            'act_update'=> !$this->system->CekAksi('update'),
            'act_delete'=> !$this->system->CekAksi('delete'));
        $this->LoadView($data);
    }

}

