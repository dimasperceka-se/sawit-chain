<?php
/******************************************
 *  Author : sofyan.salim@koltiva.com 
 *  Created On : 08-11-2021
 *  File : farm_survey_loc_geo.php
 *******************************************/
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Farm_survey_loc_geo extends SS_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index($id = '')
    {
        $data['js'] = 'data_adm/farm_survey_loc_geo';

        $data['action'] = array(
            'api_base_url' => $this->config->item('api_base_url'),
            'base_url' => base_url(),
            'curr_year' => date('Y'),
            'act_add' => !$this->system->CekAksi('add'),
            'act_update' => !$this->system->CekAksi('update'),
            'act_delete' => !$this->system->CekAksi('delete')
        );
        $this->LoadView($data);
    }

}