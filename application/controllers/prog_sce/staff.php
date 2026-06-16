<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * @Author: nikolius
 * @Date:   2016-08-19 11:30:39
 */
class Staff extends SS_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['js'] = 'prog_sce_staff';
        $api        = $this->config->item('api');

        $data['action'] = array(
            'get_staff'  => $api . '/prog_sce/profile_staff',
            'act_index'  => !$this->system->CekAksi('index'),
            'act_add'    => !$this->system->CekAksi('add'),
            'act_update' => !$this->system->CekAksi('update'),
            'act_delete' => !$this->system->CekAksi('delete'));
        $this->LoadView($data);
    }
}
