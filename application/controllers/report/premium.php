<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Premium extends SS_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['js'] = 'report_premium';
        $api = $this->config->item('api');
        $data['action'] = array(
            'crud'=>$api.'/report/report_premium',
            'rpt'=>$api.'/scheduler/rpt_traceability',
            'Kabupaten'=>$api.'/report/Kabupatens',
            'Provinsi'=>$api.'/report/Provinsis',
            'Warehouse'=>$api.'/report/Warehouse',
            'certification_period'=>$api.'/report/certification_period',
            'bu'=>$api.'/traceability/data',
            'cetak'=>$api.'/traceability/premium_cetak',
            'viewreport' =>$api.'/traceability/viewreport',
            'viewreportf' =>$api.'/traceability/viewreportf',
            'viewreportc' =>$api.'/traceability/viewreportc', 
            'premium_org' =>$api.'/traceability/premium_org', 
            'act_index'=> !$this->system->CekAksi('index'),
            'user_province' => $_SESSION['province']
        );
        $this->LoadView($data);
    }

}

