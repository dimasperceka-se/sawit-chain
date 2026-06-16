<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Polygon extends SS_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($id = '')
    {
        // $data['js']     = 'data_adm_off_data';
        $api        = $this->config->item('api');

        $data['action'] = array(
            'api_base_url' => $this->config->item('api_base_url'),
            'act_index'   => !$this->system->CekAksi('index'),
            'act_view_detail'   => $this->checkViewAccess(),
        );
        $this->LoadView($data, 'data_adm/polygon');
    }

    private function checkViewAccess()
    {
        // hardcoded untuk grup Management Unilever
        if ($_SESSION['groupid'] == 161) {
            return false;
        }
        return true;
    }

}
?>