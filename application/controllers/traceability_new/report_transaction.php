<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Report_transaction extends SS_Controller {

    public function __construct()
    {
        parent::__construct();
    }



    public function index() { 
        $api = $this->config->item('api');
        $data['js'] = 'traceability_new/report/report_transaction';
		
        $data['action'] = array(
          'api' => $api,
          //'crud' => $api . '/report_transaction_mars/',
          'apicin_basicauth' => $this->config->item('apicin_basicauth'),
          'userid' => $_SESSION['userid'],
          'date_end' => date('Y-m-d'),
          'date_start' => date('Y-m-d', strtotime('-7 days')) 
        );
        
        //echo '<pre>';
        //var_dump($data);die;
        $this->LoadView($data);
    }

}
