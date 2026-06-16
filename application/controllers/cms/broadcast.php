<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Broadcast extends SS_Controller {

    public function __construct() {
        parent::__construct(1);
    }

    public function index() {
        $data['js'] = 'broadcast';
        $api = $this->config->item('api');
        $api_base_url = $this->config->item('api_base_url');
        $data['action'] = array(
            'crud' => $api . '/broadcast/',
            'base_url' => $api_base_url,
            'user' => $_SESSION['userid'],
            //'file' => $this->config->item('api_base_url') . 'files/ticketing',
            'partnerid' => $_SESSION['PartnerID'],
            'act_index' => $this->system->CekAksi('index'),
            'act_add' => $this->system->CekAksi('add'),
            'act_update' => $this->system->CekAksi('update'),
            'act_delete' => $this->system->CekAksi('delete'),
            'act_close' => $this->system->CekAksi('close'));
        $this->LoadView($data);
    }

}
