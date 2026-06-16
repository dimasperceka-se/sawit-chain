<?php

/*******************************************
 * Author : aji.alhabsyi@koltiva.com
 * Created On : Tue June 28 2022
 * File : vehicle_type.php
********************************************/
defined('BASEPATH') or exit('No direct script access allowed');

class Vehicle_type extends SS_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['js'] = 'traceability_setting/vehicle_type';
        $api = $this->config->item('api');

        $data['action'] = array(
            'title' => 'Vechile Type',
            'type' => 'Vechile Type',
            'api_base_url' => $this->config->item('api_base_url'),
            'base_url' => base_url(),
            'act_add' => !$this->system->CekAksi('add'),
            'act_update' => !$this->system->CekAksi('update'),
            'act_delete' => !$this->system->CekAksi('delete'),
            'now' => date('Y-m-d H:i:s'),
            'date' => date('Y-m-d'),
            'time' => date('H:i'),
            'sys_date' => date('Ymd'),
        );

        $this->LoadView($data);
    }
}