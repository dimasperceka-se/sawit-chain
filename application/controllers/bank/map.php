<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Map extends SS_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('mbank');
    }

    public function index()
    {
        $data = array();

        // $data['title'] = lang('Peta');
        
        $user = $this->mbank->userDetail();

        $api = $this->config->item('api');
        $data['action'] = array(
            'base_url'              => base_url(),
            'url_province'          => $api.'/bank/province',
            'url_district'          => $api.'/bank/district',
            'url_subdistrict'       => $api.'/bank/subdistrict',
            'url_bank_branch'       => $api.'/bank/branchs',
            'url_geospatial_bank'   => $api.'/bank/geospatial',
            'url_geospatial_'       => $api.'/bank/geospatial_',
            'url_photo'             => $this->config->item('api_base_url').'images/Photo/',
            'user_province'         => $user['ProvinceID'],
            'user_district'         => $user['DistrictID'],
            'user_branch'           => $user['BranchID'],
            // 'url_geospatial_farmer_certified'       => $api.'/bank/geospatial_farmer_certified',
            // 'url_geospatial_nursery'                => $api.'/bank/geospatial_nursery',
            // 'url_geospatial_demoplot'               => $api.'/bank/geospatial_demoplot',
            // 'url_geospatial_farmer_organization'    => $api.'/bank/geospatial_farmer_organization',
            // 'url_geospatial_warehouse'              => $api.'/bank/geospatial_warehouse',
            // 'url_geospatial_trader'                 => $api.'/bank/geospatial_trader',
            );

        $this->LoadView($data, 'bank/map');
    }

}

