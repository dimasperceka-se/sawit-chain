<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gl extends SS_Controller {

   public function __construct() {
      parent::__construct();
   }

   public function index(){
      $data['js'] = 'ledger';
      $api = $this->config->item('api');
      $data['action'] = array(
         'crud'=>$api.'/jurnal',
         'startdate'=>date('m-'.'01-'.'Y'),
         'enddate'=>date('m-t-Y')
      );
      
      $this->LoadView($data);
   }

}
