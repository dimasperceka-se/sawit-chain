<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Business_sector extends SS_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['js'] = 'basic/business_sector';
        $api = $this->config->item('api');
        $data['action'] = array(
            'crud'          => $api.'/basic/business_sector',
            'act_index'     => $this->system->CekAksi('index'),
            'act_detail'    => $this->system->CekAksi('detail'),
            'act_add'       => $this->system->CekAksi('add'),
            'act_update'    => $this->system->CekAksi('update'),
            'act_delete'    => $this->system->CekAksi('delete')
        );
        $this->LoadView($data);
    }

}

