<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @Author: nikolius
 * @Date:   2016-08-19 11:30:39
 */
class Compost extends SS_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['js'] = 'prog_sce_compost';
        $api        = $this->config->item('api');

        $data['action'] = array(
            'base_url' => base_url(),
            'get_compost' => $api . '/prog_sce/compost',
            'get_compost_trans' => $api . '/prog_sce/compost_trans',
            'input_compost' => $api . '/prog_sce/input_compost',
            'input_compost_trans' => $api . '/prog_sce/input_compost_trans',
            'act_index'   => !$this->system->CekAksi('index'),
            'act_add'     => !$this->system->CekAksi('add'),
            'act_update'  => !$this->system->CekAksi('update'),
            'act_delete'  => !$this->system->CekAksi('delete'),
            'hakakses_lat_short'    => $this->system->cekSettingPerUser('latShort'),
            'hakakses_long_short'   => $this->system->cekSettingPerUser('longShort'),
            'hakakses_polygon'               => $this->system->cekSettingPerUser('polygon')
        );
        $this->LoadView($data);
    }
}
?>