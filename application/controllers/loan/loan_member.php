<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Loan_member extends SS_Controller {

   public function __construct() {
      parent::__construct(true); //why true??
   }

   public function index(){
       
      $data['js'] = 'loan_member'; //load /js/modules/loan_member.js
      $api = $this->config->item('api');
      $data['action'] = array(
         'api' => $api,
         'crud'=>$api,
         // 'memberLoanID'=>$this->input->get('memberLoanID')
      );
//      echo 'notifid:'.$this->input->get('notifid');
      $this->LoadView($data);
   }
   
}
