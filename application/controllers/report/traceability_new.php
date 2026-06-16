<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Traceability_new extends SS_Controller {

    public function __construct() {
        parent::__construct(1);
    }

    public function index() {
        $data['js'] = 'report/traceability';
        $api = $this->config->item('api');
        $data['action'] = array(
            'crud' => $api . '/report_traceability_new/',
            'partner' => $_SESSION['PartnerID']=='' ? 7 : $_SESSION['PartnerID'],
            'date_start' => date('Y-m-d'),
            'date_end' => date('Y-m-d', strtotime('-1 years')),
        );
        $this->LoadView($data);
    }

}
