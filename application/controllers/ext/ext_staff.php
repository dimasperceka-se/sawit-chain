<?php
/******************************************
 *  Author : n1colius.lau@gmail.com
 *  Created On : Wed Jan 15 2020
 *  File : farmers.php
 *******************************************/
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ext_staff extends SS_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['js'] = 'ext_staff';
        $api = $this->config->item('api');

        $data['action'] = array(
            'api_base_url' => $this->config->item('api_base_url'),
            'base_url' => base_url(),
            'url_awss3' => $this->config->item('CTCDN'),
            'partner_id' => (int) $_SESSION['PartnerID'],
            'id_admin' => (int) $_SESSION['is_admin'],
            'PartnerAsParent' => $_SESSION['PartnerAsParent'],
            'act_add' => !$this->system->CekAksi('add'),
            'act_update' => !$this->system->CekAksi('update'),
            'act_delete' => !$this->system->CekAksi('delete'),
            'act_export_excel' => !$this->system->CekAksi('export_excel')
        );

        $this->LoadView($data);
    }
}