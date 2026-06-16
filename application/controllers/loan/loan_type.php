<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Loan_type extends SS_Controller {

   public function __construct() {
      parent::__construct();
   }

   public function index(){
       
      $data['js'] = 'loantype';
      $api = $this->config->item('api');
      $data['action'] = array(
//         'aksi_list'=>$api.'/system/groupaksilist',
         'membertype_list'=>$api.'/system/membertypelist',
         'crud'=>$api
      );
      
      $this->LoadView($data);
   }

}
