<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cashio extends SS_Controller {

   public function __construct() {
      parent::__construct();
   }

   public function index(){
      $data['js'] = 'cashio';
      $api = $this->config->item('api');
      $data['action'] = array(
         'crud'=>$api.'/cashio',
         'api'=>$api
      );
      
      $this->LoadView($data);
   }

}
