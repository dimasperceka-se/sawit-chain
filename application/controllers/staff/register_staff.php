<?php

/**
 * @Author: nikolius
 * @Date:   2017-10-13 13:06:36
 * @Last Modified by:   nikolius
 * @Last Modified time: 2017-10-13 13:31:15
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Register_staff extends SS_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($id = '')
    {
        $data['js']     = 'staff/register_staff';
        $api        = $this->config->item('api');

        $data['action'] = array(
            'api_base_url' => $this->config->item('api_base_url'),
            'act_add'=> !$this->system->CekAksi('add'),
            'act_update'=> !$this->system->CekAksi('update'),
            'act_delete'=> !$this->system->CekAksi('delete'),
            'sys_date' => date('Ymd')
        );
        $this->LoadView($data);
    }

}
?>