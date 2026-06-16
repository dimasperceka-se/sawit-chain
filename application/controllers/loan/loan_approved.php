<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Loan_approved extends SS_Controller {

   public function __construct() {
      parent::__construct();
   }

   public function index(){
       
      $data['js'] = 'loan_approved';
      $api = $this->config->item('api');
      $data['action'] = array(
         'crud'=>$api
      );
      
      $this->LoadView($data);
   }

}
