<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Logsync extends SS_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index($id = '')
    {
        $data['js'] = 'system/logsync';
        $api = $this->config->item('api');
        $now = date('Y-m-d H:i:s');
        $end = date_add(date_create($now), date_interval_create_from_date_string('+6 hours'));
        $end2 = date_format($end, 'Y-m-d H:i:s');

        $data['action'] = array(
            'datenow' => $now,
            'dateend' => $end2,
            'api'          => $api, // ke api controller
            'api_base_url' => $this->config->item('api_base_url'),
            'base_url'     => base_url(),
        );
        $this->LoadView($data);
    }


    
}
?>