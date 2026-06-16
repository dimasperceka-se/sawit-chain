<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Farmer_mill extends SS_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index($prov = '', $kab = '') {
        $data['js'] = 'grower/farmer_mill';
        $api = $this->config->item('api');

        $PartnerID = $_SESSION['PartnerID'];
        if ($PartnerID == "")
            $PartnerID = "TidakAda";

        $data['action'] = array(
            'prov'                        => $prov,
            'kab'                         => $kab,
            'api_base_url'                => $this->config->item('api_base_url'),
            'user_role'                   => $_SESSION['role'],
            'user_partnerid'              => $_SESSION['PartnerID'],
            'district'                    => $this->api . '/dashboard/district',
            'daer'                        => $_SESSION['daerah'],
            'partner'                     => $_SESSION['PartnerID'],
            'all_partner'                 => '',
            'cetak_beneficiary_profiles'  => $api . '/farmer/cetak_beneficiary_profiles/',
            'cetak_farmer_summary'        => $api . '/farmer/cetak_p1_p2/',
            'act_export'                  => !$this->system->CekAksi('export'),
            'act_search_desa'             => !$this->system->CekAksi('search_desa'),
            'act_set_partner_member'      => !$this->system->CekAksi('set_partner_member'),
            'grid_filter_farmer_category' => (isset($_SESSION['grid_filter']['FarmerCategory'])) ? $_SESSION['grid_filter']['FarmerCategoryMill'] : '',
            'grid_filter_farmer_text'     => (isset($_SESSION['grid_filter']['Text'])) ? $_SESSION['grid_filter']['TextMill'] : '',
            'grid_filter_farmer_desa'     => (isset($_SESSION['grid_filter']['Desa'])) ? $_SESSION['grid_filter']['DesaMill'] : ''
        );

        $data['mentokDistrict'] = false; // untuk setting apakah filter region mau sampai district atau subdistrict
        $this->LoadView($data, 'common_content_region'); //selalu load view "common_content_region" ini untuk filter region yg seragam
    }

}
