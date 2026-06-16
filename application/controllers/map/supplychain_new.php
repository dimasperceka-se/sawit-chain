<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Supplychain_new extends SS_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->language('map');
    }

    public function index()
    {
        $this->load->model('mmap');

        $data = array();

        $data['title'] = lang('Peta');

        $api = $this->config->item('api');
        $data['action'] = array(
            'base_url'  => base_url(),
            'url_api'   => $api,
            'partnerid' => $_SESSION['PartnerID'],
            );
        
        $this->LoadView($data, 'map/supplychain_new');
    }

}

