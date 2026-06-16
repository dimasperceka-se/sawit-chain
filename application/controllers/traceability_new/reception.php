<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Reception extends SS_Controller 
{
    public function __construct() 
    {
        parent::__construct();
    }

    public function index() 
    {   
        $data['js'] = 'traceability_new/Reception';
        
        $api = $this->config->item('api');

        $data['action'] = array(
            'api_base_url'     => $this->config->item('api_base_url'),
            'base_url'         => base_url(),
            // 'url_awss3'        => $this->config->item('CTCDN'),
            'act_add'          => !$this->system->CekAksi('add'),
            'act_update'       => !$this->system->CekAksi('update'),
            'act_delete'       => !$this->system->CekAksi('delete'),
            // 'act_export_excel' => !$this->system->CekAksi('export_excel'),
            'now'              => date('Y-m-d H:i:s'),
            'date'             => date('Y-m-d'),
            'time'             => date('H:i'),
            'IsPaymentMethod'  => IsPaymentMethod(),
        );

        // echo "<pre>";
        // var_dump($data['action']);
        // die;

        $this->LoadView($data, 'common_content');
    }
}
