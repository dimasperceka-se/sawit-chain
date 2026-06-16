<?php

/**
 * @Author: nikolius
 * @Date:   2017-11-08 15:44:39
 * @Last Modified by:   nikolius
 * @Last Modified time: 2017-11-08 15:45:49
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Farmer_group extends SS_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($id = '')
    {
        $data['js']     = 'farmer_group';

        $PartnerID = $_SESSION['PartnerID'];
        if($PartnerID == "") $PartnerID = "TidakAda";

        $data['action'] = array(
            'api_base_url'                          => $this->config->item('api_base_url'),
            'user_role' => $_SESSION['role'],
            'user_partnerid' => $_SESSION['PartnerID'],
            'url_farmer_profile'        => $this->config->item('api') . '/farmer/cetak_beneficiary_profiles/',
            'act_add'                               => !$this->system->CekAksi('add'),
            'act_update'                            => !$this->system->CekAksi('update'),
            'act_delete'                            => !$this->system->CekAksi('delete'),
            'act_export'                            => !$this->system->CekAksi('export')
        );

        $data['mentokDistrict'] = true; // untuk setting apakah filter region mau sampai district atau subdistrict
        $this->LoadView($data, 'common_content_region'); //selalu load view "common_content_region" ini untuk filter region yg seragam
    }
}

?>