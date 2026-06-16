<?php
/**
 * @Author: nikolius
 * @Date:   2017-08-03 15:19:56
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Kcp_bulking extends SS_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($id = '')
    {
        $data['js']     = 'kcp/kcp_bulking';

        $data['action'] = array(
            'api_base_url'                          => $this->config->item('api_base_url'),
            'partner'                               => $_SESSION['PartnerID'],
            'act_add'                               => !$this->system->CekAksi('add'),
            'act_update'                            => !$this->system->CekAksi('update'),
            'act_delete'                            => !$this->system->CekAksi('delete'),
            'act_export'                            => !$this->system->CekAksi('export'),
            'act_set_partner_mill'                  => !$this->system->CekAksi('set_partner_mill'),
            'act_tracebility_declaration'           => !$this->system->CekAksi('tracebility_declaration'),
            'act_reported_locked'                   => !$this->system->CekAksi('reported_locked'),
            'year'                                  => date("Y"),
            'period'                                => "half"
        );

        $data['mentokDistrict'] = false; // untuk setting apakah filter region mau sampai district atau subdistrict
        $this->LoadView($data, 'common_content_region'); //selalu load view "common_content_region" ini untuk filter region yg seragam
    }
}

?>