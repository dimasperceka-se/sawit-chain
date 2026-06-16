<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cashier extends SS_Controller {

   public function __construct() {
      parent::__construct();
   }

   public function index(){
      // print_r($_SESSION);
      $data['js'] = 'accounting_cashier';
      $api = $this->config->item('api');
      $data['action'] = array(
         'crud'=>$api.'/cashier',
         'base_url'=> site_url(),
         'realname'=>$_SESSION['realname'],
         'userid'=>$_SESSION['userid'],
         'api'=>$api
      );
      
      $this->LoadView($data);
   }

 

}
