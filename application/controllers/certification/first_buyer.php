<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class First_buyer extends SS_Controller {
   
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['js'] = 'first_buyer';
        $api = $this->config->item('api');
        $data['action'] = array(
            'crud'               => $api.'/first_buyer/',
            'act_index'    => $this->system->CekAksi('index'),
            'act_add'      => $this->system->CekAksi('add'),
            'act_update'   => $this->system->CekAksi('update'),
            'act_delete'   => $this->system->CekAksi('delete'));
      $this->LoadView($data);
    }

}

