<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Landuse extends SS_Controller {

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
            'sess_partner_id' => $_SESSION['PartnerID'],
            'act_print' => $this->system->CekAksi('print'),
            'act_area'  => $this->system->CekAksi('area'),
            'act_landuse'  => $this->system->CekAksi('map_landuse'),
            );
        $data['supplychain_access']     = $this->mmap->checkSupplyAccess($_SESSION['userid']);

        $this->LoadView($data, 'map/landuse');
    }

}

