<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Import extends SS_Controller {
   
   public function __construct() {
      parent::__construct();
   }

   public function index() {
      $data['js'] = 'traceability_import';
      $api = $this->config->item('api');
      $data['action'] = array(
         'crud'=>$api.'/traceability/import',
         'bu'=>$api.'/traceability/data',
         'url'=>base_url());
      $this->LoadView($data);
   }

}

