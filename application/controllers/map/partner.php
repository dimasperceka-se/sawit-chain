<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Partner extends SS_Controller {

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
            'partner' => $_SESSION['PartnerID'],
            'act_print' => $this->system->CekAksi('print'),
            'act_area'  => $this->system->CekAksi('area'),
            'act_landuse' => $_SESSION['PartnerID'] == '1' ? true : false
            );
        $data['supplychain_access']     = $this->mmap->checkSupplyAccess($_SESSION['userid']);

        $this->LoadView($data, 'map/partner');
    }

}

