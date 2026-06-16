<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @Author: nikolius
 * @Date:   2016-08-19 11:30:39
 */
class Nursery extends SS_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['js'] = 'prog_sce_nursery';
        $api        = $this->config->item('api');
        $SceID = getSceID();

        $data['action'] = array(
            'base_url' => base_url(),
            'SceID' => $SceID,
            'FarmerID' => getFarmerIDForSce($SceID),
            'api_base_url' => $this->config->item('api_base_url'),
            'get_profile' => $api . '/prog_sce/profile',
            'act_index'   => !$this->system->CekAksi('index'),
            'act_add'     => !$this->system->CekAksi('add'),
            'act_update'  => !$this->system->CekAksi('update'),
            'act_delete'  => !$this->system->CekAksi('delete'),
            'api_base_url' => $this->config->item('api_base_url'),
            'hakakses_lat_long'                => $this->system->cekSettingPerUser('latLong'),
            'hakakses_long_long'               => $this->system->cekSettingPerUser('longLong'),
            'hakakses_polygon'               => $this->system->cekSettingPerUser('polygon')
        );
        $this->LoadView($data);
    }
}
?>