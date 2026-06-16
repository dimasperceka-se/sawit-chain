<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Service_provider extends SS_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['js'] = 'basic/service_provider';
        $api = $this->config->item('api');
        $data['action'] = array(
            'crud'              => $api.'/basic/service_provider',
            'province_list'     => $api.'/administration/province_list',
            'district_list'     => $api.'/administration/district_list',
            'sector_list'     => $api.'/basic/business_sector_list',
            'act_index'     => $this->system->CekAksi('index'),
            'act_detail'    => $this->system->CekAksi('detail'),
            'act_add'       => $this->system->CekAksi('add'),
            'act_update'    => $this->system->CekAksi('update'),
            'act_delete'    => $this->system->CekAksi('delete')
        );
        $this->LoadView($data);
    }

}

