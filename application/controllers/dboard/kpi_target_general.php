<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Kpi_target_general extends SS_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index() {
        $data['js'] = 'dboard/kpi_target_general';

        $data['action'] = array(
            'partner_id' => (int) $_SESSION['PartnerID'],
            'act_update' => !$this->system->CekAksi('update'),
            'label_prov' => 'Province'
        );
        
        $this->LoadView($data, 'common_content_region');
    }
}