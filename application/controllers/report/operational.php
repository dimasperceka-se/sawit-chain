<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Operational extends SS_Controller {

   public function __construct() {
      parent::__construct(true); 
   }

   public function index(){
       
      $data['js'] = 'report_operational'; //load /js/modules/report_saving.js
      $api = $this->config->item('api');
      
      $data['action'] = array(
         'api' => $api,
         'crud' => $api,
         'rpt' => $api . '/report',
      );

      $this->LoadView($data);
   }
   
}
