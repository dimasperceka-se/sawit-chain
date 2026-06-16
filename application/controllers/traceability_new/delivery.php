<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Delivery extends SS_Controller 
{
    public function __construct() 
    {
        parent::__construct(1);
    }

    public function index() 
    {
        $data['js'] = 'traceability_new/Delivery';
        
        $api = $this->config->item('api');

        $date = new DateTime("now", new \DateTimeZone('Asia/Jakarta') );

        $dateNow = $date->format('Y-m-d H:i:s');

        $data['action'] = array(
            'api_base_url'     => $this->config->item('api_base_url'),
            'base_url'         => base_url(),
            'now'              => $dateNow,
            'act_add'          => !$this->system->CekAksi('add'),
            'act_update'       => !$this->system->CekAksi('update'),
            'act_delete'       => !$this->system->CekAksi('delete'),
        );

        $this->LoadView($data, 'common_content_delivery');
    }
}
