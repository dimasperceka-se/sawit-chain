<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_mill_refinery extends SS_Controller {
   
    public function __construct() {
        parent::__construct(1);
    }

    public function index() {
        $this->load->language('basic');
        $data['js'] = 'basic/mill_refinery';
        $api = $this->config->item('api');

        $data['action'] = array(            
            'crud'         => $api.'/report_mill_refinery/tree_menu',
            'parent'       => $api.'/report_mill_refinery/tree_menu_parent',
            'act_index'    => $this->system->CekAksi('index'),
        );
        $this->LoadView($data);
    }

}

