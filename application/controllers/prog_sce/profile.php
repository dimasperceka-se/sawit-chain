<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * @Author: nikolius
 * @Date:   2016-08-19 11:30:39
 */
class Profile extends SS_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['js'] = 'prog_sce_profile';
        $api        = $this->config->item('api');

        $data['action'] = array(
            'get_profile' => $api . '/prog_sce/profile',
            'get_farmer_garden' => $api.'/farmer/farmerl_garden_status',

            'photo'       => $this->config->item('api_base_url') . 'images/Photo/',
            'act_index'   => !$this->system->CekAksi('index'),
            'act_add'     => !$this->system->CekAksi('add'),
            'act_update'  => !$this->system->CekAksi('update'),
            'act_delete'  => !$this->system->CekAksi('delete'));
        $this->LoadView($data);
    }
}
