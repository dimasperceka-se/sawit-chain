<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Loan_appraisal extends SS_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {

        $data['js'] = 'loanappraisal';
        $api = $this->config->item('api');
        $data['action'] = array(
            'crud' => $api,
            'district' => $api . '/member/combodistrict',
            'subdistrict' => $api . '/member/combosubdistrict',
            'village' => $api . '/member/combovillage',
        );

        $this->LoadView($data);
    }

}
