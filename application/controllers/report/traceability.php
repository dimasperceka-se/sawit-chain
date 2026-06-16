<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Traceability extends SS_Controller {
   
   public function __construct() {
      parent::__construct();
   }

   public function index() {
      $data['js'] = 'report_traceability';
      $api = $this->config->item('api');
      $data['action'] = array(
         'crud'=>$api.'/report/report_premium',
         'rpt'=>$api.'/scheduler/rpt_traceability',
         'Kabupaten'=>$api.'/report/Kabupatens',
         'Provinsi'=>$api.'/report/Provinsis',
         'Warehouse'=>$api.'/report/Warehouse',
         'Koperasi'=>$api.'/report/Koperasi',
         'BuyingUnit'=>$api.'/report/BuyingUnit',
         'Farmer'=>$api.'/report/Farmer',
         'certification_period'=>$api.'/report/certification_period',
         'bu'=>$api.'/traceability/data',
         'cetak'=>$api.'/traceability/print_report',
         'viewreport' =>$api.'/traceability/viewreport',
         'viewreportf' =>$api.'/traceability/viewreportf',
         'viewreportc' =>$api.'/traceability/viewreportc', 
         'premium_org' =>$api.'/traceability/premium_org', 
         'act_index'=> !$this->system->CekAksi('index'),
         'user_province' => $_SESSION['province'],

         'viewreportw' => $api.'/traceability/viewreportw',
         'viewreportwtrans' => $api.'/traceability/viewreportwtrans',
        
         'viewreportcoop' => $api.'/traceability/viewreportcoop',
         'viewreportcooptrans' => $api.'/traceability/viewreportcooptrans',
          
         'viewreportbu' => $api.'/traceability/viewreportbu',
         'viewreportbutrans' => $api.'/traceability/viewreportbutrans',
          
         'viewreportfarmer' => $api.'/traceability/viewreportfarmer',
         'viewreportfarmertrans' => $api.'/traceability/viewreportfarmertrans', 
      );
      $this->LoadView($data);
   }

}

