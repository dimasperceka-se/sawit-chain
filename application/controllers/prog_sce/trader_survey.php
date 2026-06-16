<?php
/**
 * @Author: nikolius
 * @Date:   2017-03-17 16:06:29
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Trader_survey extends SS_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['js'] = 'prog_sce_trader_survey';
        $api        = $this->config->item('api');

        $SceID      = getSceID();
        $dataFarmer = getFarmerInfoForSce($SceID);

        $data['action'] = array(
            'baseUrlNya' => $this->config->item('base_url'),
            'api' => $this->config->item('api'),
            'SceID' => $SceID,
            'FarmerID'   => $dataFarmer['FarmerID'],
            'FarmerName' => $dataFarmer['FarmerName'],
            'act_index'  => $this->system->CekAksi('index'),
            'act_add'    => $this->system->CekAksi('add'),
            'act_update' => $this->system->CekAksi('update'),
            'act_delete' => $this->system->CekAksi('delete')
        );

        $this->LoadView($data);
    }
}
