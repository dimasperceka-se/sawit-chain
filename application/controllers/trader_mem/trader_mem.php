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
        $data['js']     = 'trader_mem/trader_mem';

        $data['action'] = array(
            'api_base_url'                          => $this->config->item('api_base_url'),
            'act_add'                               => !$this->system->CekAksi('add'),
            'act_update'                            => !$this->system->CekAksi('update'),
            'act_delete'                            => !$this->system->CekAksi('delete'),
            'act_export'                            => !$this->system->CekAksi('export')
        );

        $data['mentokDistrict'] = false; // untuk setting apakah filter region mau sampai district atau subdistrict
        $this->LoadView($data, 'common_content_region'); //selalu load view "common_content_region" ini untuk filter region yg seragam
    }
}
?>