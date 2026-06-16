<?php
/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Jul 13 2020
 *  File : staffuser.php
 *******************************************/
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Staffuser extends SS_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['js'] = 'staffuser';
        $api = $this->config->item('api');
        $url_awss3 = $this->config->item('CTCDN');

        $data['action'] = array(
            'api_base_url' => $this->config->item('api_base_url'),
            'base_url' => base_url(),
            'id_admin' => (int) $_SESSION['is_admin'],
            'act_add' => !$this->system->CekAksi('add'),
            'act_update' => !$this->system->CekAksi('update'),
            'act_delete' => !$this->system->CekAksi('delete'),
            'sess_username' => $_SESSION['username'],
            'act_staff_position' => $this->system->CekAksi('staff_position'),
            'act_staff_position_add' => $this->system->CekAksi('staff_position_add'),
            'act_staff_position_update' => $this->system->CekAksi('staff_position_update'),
            'act_staff_position_delete' => $this->system->CekAksi('staff_position_delete')
        );

        $this->LoadView($data);
    }
}