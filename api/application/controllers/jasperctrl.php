<?php defined('BASEPATH') OR exit('No direct script access allowed');

class JasperCtrl extends REST_Controller {
                
    public function __construct() {
        parent::__construct();
    }

    public function report_get() {
        
        $this->config->set_item('compress_output',false);
        
        $this->load->library('jasper');
        echo $this->jasper->drawReportViewer();
    }
}
