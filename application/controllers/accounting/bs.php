<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bs extends SS_Controller {

   public function __construct() {
      parent::__construct();
   }

   public function index(){
      $data['js'] = 'balance';
      $api = $this->config->item('api');
      $data['action'] = array(
         'crud'=>$api,
         'bulan'=>intval(date('m')),
         'tahun'=>date('Y')
      );
      
      $this->LoadView($data);
   }

}
