<?php
/**
 * @Author: nikolius
 * @Date:   2016-10-27 13:33:52
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Sce_farmer_add extends SS_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['js'] = 'prog_sce_farmer_add';
        $api        = $this->config->item('api');

        $data['action'] = array(
            'baseUrlNya' => $this->config->item('base_url'),
            'photo'             => $this->config->item('api_base_url') . 'images/Photo/',
            'act_index'         => !$this->system->CekAksi('index'),
            'act_add'           => !$this->system->CekAksi('add'),
            'act_update'        => !$this->system->CekAksi('update'),
            'act_delete'        => !$this->system->CekAksi('delete'),

            'hakakses_lat_short'  => $this->system->cekSettingPerUser('latShort'),
            'hakakses_long_short' => $this->system->cekSettingPerUser('longShort'),
            'hakakses_lat_long'   => $this->system->cekSettingPerUser('latLong'),
            'hakakses_long_long'  => $this->system->cekSettingPerUser('longLong')
        );
        $this->LoadView($data);
    }
}
