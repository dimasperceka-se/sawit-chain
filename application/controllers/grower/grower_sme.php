<?php
/**
 * @Author: nikolius
 * @Date:   2017-05-16 10:43:14
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Grower_sme extends SS_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($id = '')
    {
        $data['js']     = 'grower/grower_sme';
        $api = $this->config->item('api');

        $PartnerID = $_SESSION['PartnerID'];
        if($PartnerID == "") $PartnerID = "TidakAda";


        $wags_access = $this->array_search_partial((object) $_SESSION['daerah_access'], '43');
        $wags_access2 = $this->array_search_partial((object) $_SESSION['daerah_access'], '44');
        
        $wags_access_area = true;
        if($wags_access != '' || $wags_access2 != ''){
            $wags_access_area = false;
        } 

        $data['action'] = array(
            'api_base_url'                => $this->config->item('api_base_url'),
            'user_role'                   => $_SESSION['role'],
            'user_partnerid'              => $_SESSION['PartnerID'],
            'supplychain_id'              => $_SESSION['SupplychainID'],
            'act_add'                     => !$this->system->CekAksi('add'),
            'act_dealer_assign'           => !$this->system->CekAksi('dealer_assign'),
            'act_update'                  => !$this->system->CekAksi('update'),
            'act_delete'                  => !$this->system->CekAksi('delete'),
            'act_export'                  => !$this->system->CekAksi('export'),
            'act_update_audit_imsmanager' => !$this->system->CekAksi('update_audit_imsmanager'),
            'act_ics_audit_log_lock'      => !$this->system->CekAksi('ics_audit_log_lock'),
            'act_farmer_new_audit_log'    => !$this->system->CekAksi('farmer_new_audit_log'),
            'act_set_partner_member'      => !$this->system->CekAksi('set_partner_member'),
            'act_search_desa'             => !$this->system->CekAksi('search_desa'),
            'wags_access_area'            => $wags_access_area,
            'cetak_beneficiary_profiles'  => $api . '/farmer/cetak_beneficiary_profiles/',
            'grid_filter_farmer_category' => (isset($_SESSION['grid_filter']['FarmerCategory'])) ? $_SESSION['grid_filter']['FarmerCategory'] : '',
            'grid_filter_farmer_text'     => (isset($_SESSION['grid_filter']['Text'])) ? $_SESSION['grid_filter']['Text'] : '',
            'grid_filter_farmer_desa'     => (isset($_SESSION['grid_filter']['Desa'])) ? $_SESSION['grid_filter']['Desa'] : ''
        );

        $data['mentokDistrict'] = false; // untuk setting apakah filter region mau sampai district atau subdistrict
        $this->LoadView($data, 'common_content_region'); //selalu load view "common_content_region" ini untuk filter region yg seragam
    }

    function array_search_partial($arr, $keyword) {
        foreach($arr as $index => $string) {
            if (strpos($string, $keyword) !== FALSE)
                return $index;
        }
    }
    
}
?>