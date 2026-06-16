<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Menu_pull_engine_check extends SS_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['js'] = 'menu_pull_engine_check';
        $api = $this->config->item('api');

        $data['action'] = array(
            'api_base_url' => $this->config->item('api_base_url'),
            'base_url' => base_url(),
            'url_awss3' => $this->config->item('CTCDN'),
            'partner_id' => (int) $_SESSION['PartnerID'],
            'person_staff' => (int) $_SESSION['PersonID'],
            'act_add' => !$this->system->CekAksi('add'),
            'act_update' => !$this->system->CekAksi('update'),
            'act_delete' => !$this->system->CekAksi('delete'),
            'act_export_excel' => !$this->system->CekAksi('export_excel'),
            'partner_group' => $_SESSION['group']
        );

        $this->LoadView($data, "common_content_pull_engine_check");
    }
}