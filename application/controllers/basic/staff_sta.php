<?php
/**
 * @Author: nikolius
 * @Date:   2016-09-27 15:58:58
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Staff_sta extends SS_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['js'] = 'basic_staff_sta';
        $api        = $this->config->item('api');

        $data['action'] = array(
            'api_base_url' => $this->config->item('api_base_url'),
            'act_index'   => !$this->system->CekAksi('index'),
            'act_add'     => !$this->system->CekAksi('add'),
            'act_update'  => !$this->system->CekAksi('update'),
            'act_delete'  => !$this->system->CekAksi('delete'),
            'act_staff_position' => $this->system->CekAksi('staff_position'),
            'act_staff_position_add' => $this->system->CekAksi('staff_position_add'),
            'act_staff_position_update' => $this->system->CekAksi('staff_position_update'),
            'act_staff_position_delete' => $this->system->CekAksi('staff_position_delete'),
            'act_staff_user_management' => $this->system->CekAksi('staff_user_management'),
            'act_staff_user_app_management' => $this->system->CekAksi('staff_user_app_management')
        );
        $this->LoadView($data);
    }
}
?>