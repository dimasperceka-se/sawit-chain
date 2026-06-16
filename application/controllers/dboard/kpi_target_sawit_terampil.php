<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Kpi_target_sawit_terampil extends SS_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index() {
        $data['js'] = 'dboard/kpi_target_sawit_terampil';

        $data['action'] = array(
            'api_base_url'  => $this->config->item('api_base_url'),
            'partner_id'    => (int) $_SESSION['PartnerID'],
            'act_add'       => !$this->system->CekAksi('add'),
            'act_update'    => !$this->system->CekAksi('update'),
            'act_delete'    => !$this->system->CekAksi('delete'),
            'act_export'    => !$this->system->CekAksi('export'),
            'label_prov'    => 'Province'
        );
        
        $this->LoadView($data, 'common_content_region'); //selalu load view "common_content_region" ini untuk filter region yg seragam
    }
}