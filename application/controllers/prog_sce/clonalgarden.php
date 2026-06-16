<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @Author: nikolius
 * @Date:   2016-08-19 11:30:39
 */
class Clonalgarden extends SS_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['js'] = 'prog_sce_clonalgarden';
        $api        = $this->config->item('api');
        $SceID = getSceID();

        $data['action'] = array(
            'base_url' => base_url(),
            'SceID' => $SceID,
            'act_index'   => !$this->system->CekAksi('index'),
            'act_add'     => !$this->system->CekAksi('add'),
            'act_update'  => !$this->system->CekAksi('update'),
            'act_delete'  => !$this->system->CekAksi('delete'),
            'hakakses_lat_long'                => $this->system->cekSettingPerUser('latLong'),
            'hakakses_long_long'               => $this->system->cekSettingPerUser('longLong'),
            'hakakses_polygon'               => $this->system->cekSettingPerUser('polygon')
        );
        $this->LoadView($data);
    }
}
?>