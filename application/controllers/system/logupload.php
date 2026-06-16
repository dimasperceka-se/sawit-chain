<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Logupload extends SS_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['js'] = 'logupload';
        $api = $this->config->item('api');
        $data['action'] = array(
            'crud' => $api . '/system/logupload',
            'cetak_xls_log_upload' => $api . '/system/logupload_excel',
        );
        $this->LoadView($data);
    }

}

