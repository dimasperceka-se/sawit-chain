<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Batching extends SS_Controller 
{
    public function __construct() 
    {
        parent::__construct();
    }

    public function index() 
    {   
        $data['js'] = 'traceability_new/Batching';
        
        $api = $this->config->item('api');

        $data['action'] = array(
            'api_base_url'     => $this->config->item('api_base_url'),
            'base_url'         => base_url(),
            'act_add'          => !$this->system->CekAksi('add'),
            'act_update'       => !$this->system->CekAksi('update'),
            'act_delete'       => !$this->system->CekAksi('delete'),
            'now'              => date('Y-m-d H:i:s'),
            'date'             => date('Y-m-d'),
            'time'             => date('H:i'),
        );

        $this->LoadView($data, 'common_content');
    }
}
