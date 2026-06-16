<?php
/**
 * @Author: nikolius
 * @Date:   2017-07-18 14:41:19
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Trader_mem extends SS_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($id = '')
    {
        $data['js']     = 'sme/trader_mem';

        $wags_access = ($_SESSION['daerah_access'] == '43');
        $wags_access2 = ($_SESSION['daerah_access'] == '44');
        
        $wags_access_area = true;
        if($wags_access != '' || $wags_access2 != ''){
            $wags_access_area = true;
        }
		 
        $data['action'] = array(
            'api_base_url'                          => $this->config->item('api_base_url'),
            'partner'                               => $_SESSION['PartnerID'],
            'act_add'                               => !$this->system->CekAksi('add'),
            'act_update'                            => !$this->system->CekAksi('update'),
            'act_delete'                            => !$this->system->CekAksi('delete'),
            'act_export'                            => !$this->system->CekAksi('export'),
            'act_search_desa'                       => !$this->system->CekAksi('search_desa'),
            'daerah_access'                         => $_SESSION['daerah_access'],
            'wags_access_area'                      => $wags_access_area,
            'act_set_partner_trader'                => !$this->system->CekAksi('set_partner_trader')
        );

        $data['mentokDistrict'] = false; // untuk setting apakah filter region mau sampai district atau subdistrict
        $this->LoadView($data, 'common_content_region'); //selalu load view "common_content_region" ini untuk filter region yg seragam
    }
}
?>