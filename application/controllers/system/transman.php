<?php
/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Fri Sep 18 2020
 *  File : transman.php
 *******************************************/
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Transman extends SS_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['js'] = 'system/transman';
        $api = $this->config->item('api');

        $data['action'] = array(
            'api_base_url' => $this->config->item('api_base_url'),
            'base_url' => base_url(),
            'crud' => $api . '/translation/core_translation',
            'header' => $api . '/translation/header_translation',
            'validate' => $api . '/translation/validate_translation',
            'act_add' => !$this->system->CekAksi('add'),
            'act_update' => !$this->system->CekAksi('update'),
            'act_delete' => !$this->system->CekAksi('delete')
        );
        
        $this->LoadView($data);
    }
}