<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Menu extends SS_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index($id = '')
    {
        $data['js'] = 'menu/menu_';
        $api = $this->config->item('api');

        $data['action'] = array(
            'api'                  => $api, // ke api controller
            'api_base_url' => $this->config->item('api_base_url'),
            'base_url' => base_url(),
            'curr_year' => date('Y'),
        
            'act_add'    => !$this->system->CekAksi('add'),
            'act_update' => !$this->system->CekAksi('update'),
            'act_delete' => !$this->system->CekAksi('delete'),
        );
        $this->LoadView($data);
    }


    
}
?>