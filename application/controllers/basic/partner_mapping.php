<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Partner_mapping extends SS_Controller {
   
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->load->language('basic');
        $data['js'] = 'basic/partner_mapping';
        $api = $this->config->item('api');

        $data['action'] = array(            
            'crud'         => $api.'/partner_mapping/tree_menu',
            'parent'       => $api.'/partner_mapping/tree_menu_parent',
            'act_index'    => $this->system->CekAksi('index'),
            'act_update'   => $this->system->CekAksi('update')
        );
        $this->LoadView($data);
    }

}

