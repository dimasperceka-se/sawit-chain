<?php
/******************************************
 *  Author : fikrifauzul@gmail.com
 *  Created On : 13-05-2020
 *  File : polygon_over.php
 *******************************************/
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Polygon_over extends SS_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['js'] = 'data_adm/polygon_over';
        $api = $this->config->item('api');

        $data['action'] = array(
            'api_base_url' => $this->config->item('api_base_url'),
            'userid' => $_SESSION['userid'],
            'base_url' => base_url(),
            'act_index'   => !$this->system->CekAksi('index')
        );

        $this->LoadView($data);
    }
}