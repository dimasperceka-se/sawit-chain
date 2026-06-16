<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Traceability extends SS_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->language('map');
    }

    public function index()
    {
        $this->load->model('mmap');
        $data = array();
        $data['title'] = lang('Maps');

        $api = $this->config->item('api');
        $data['action'] = array(
            'base_url'  => base_url(),
            'url_api'   => $api,
            'act_print' => $this->system->CekAksi('print'),
            'act_area'  => $this->system->CekAksi('area'),
            'url_awss3' => $this->config->item('CTCDN')
            );

        $this->LoadView($data, 'maps/traceability/v_maps_traceability');
    }

}