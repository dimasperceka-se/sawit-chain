<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cooperative_training extends SS_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['js'] = 'basic/cooperative_training';
        $api = $this->config->item('api');
        $data['action'] = array(
            'crud'          => $api.'/basic/cooperative_training',
            'act_index'     => $this->system->CekAksi('index'),
            'act_detail'    => $this->system->CekAksi('detail'),
            'act_add'       => $this->system->CekAksi('add'),
            'act_update'    => $this->system->CekAksi('update'),
            'act_delete'    => $this->system->CekAksi('delete')
        );
        $this->LoadView($data);
    }

}

