<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Loan_proposed extends SS_Controller {

   public function __construct() {
      parent::__construct();
   }

   public function index(){
       
      $data['js'] = 'loan_proposed';
      $api = $this->config->item('api');
      $data['action'] = array(
         'crud'=>$api
      );
      
      $this->LoadView($data);
   }

}
