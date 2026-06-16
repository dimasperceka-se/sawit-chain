<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class User_dashboard extends SS_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index($id = '') {
        $data['js'] = 'user_dashboard';
        $api = $this->config->item('api');
        $url_awss3 = $this->config->item('CTCDN');

        $data['action'] = array(
            'api_base_url' => $this->config->item('api_base_url'),
            'userid' => $_SESSION['userid'],
            'is_admin' => $_SESSION['is_admin'],
            'base_url' => base_url(),
            'url_awss3' => $url_awss3,
            'act_add' => !$this->system->CekAksi('add'),
            'act_update' => !$this->system->CekAksi('update'),
            'act_delete' => !$this->system->CekAksi('delete'),
        );
        $this->LoadView($data, 'common_content_region');
    }

}
