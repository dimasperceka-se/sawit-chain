<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Jurnal extends SS_Controller {

   public function __construct() {
      parent::__construct();
   }

   public function index(){
      $data['js'] = 'jurnal';
      $api = $this->config->item('api');
      $data['action'] = array(
         'crud'=>$api.'/jurnal',
         'api'=>$api
      );
      
      $this->LoadView($data);
   }

}
