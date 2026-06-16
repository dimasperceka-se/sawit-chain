<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Loan_simulator extends SS_Controller {

   public function __construct() {
      parent::__construct();
   }

   public function index(){
       
      $data['js'] = 'loansimulator';
      $api = $this->config->item('api');
      $data['action'] = array(
         'crud'=>$api,
         'baseurl'=>base_url()
      );
      
      $this->LoadView($data);
   }

}
