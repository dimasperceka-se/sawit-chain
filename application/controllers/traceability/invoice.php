<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invoice extends SS_Controller {
   
   public function __construct() {
      parent::__construct();
   }

   public function index() {
      $data['js'] = 'traceability_invoice';
      $api = $this->config->item('api');
      $data['action'] = array(
         'crud'=>$api.'/traceability/invoice',
         
         'act_index'=> !$this->system->CekAksi('index'),
         'act_update'=> !$this->system->CekAksi('update'));
      $this->LoadView($data);
   }

}

