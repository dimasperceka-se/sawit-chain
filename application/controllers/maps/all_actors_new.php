<?php
/******************************************
 *  Author      : sofyan.salim@koltiva.com
 *  Created On  : Mon May 23 2022
 *  File        : all_actors_new.php
 *******************************************/
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class All_actors_new extends SS_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index() {
        $api = $this->config->item('api');

        $data['action'] = array(
            'api_base_url' => $this->config->item('api_base_url'),
            'base_url' => base_url(),
            'partner_id' => (int) $_SESSION['PartnerID'],
            'PartnerAsParent' => $_SESSION['PartnerAsParent'],
            'url_awss3' => $this->config->item('CTCDN'),
            'export_map_polygon_excel' => $this->system->CekAksi('export_map_polygon_excel')
        );

        $this->LoadView($data,'maps/all_actors_new');
    }

}