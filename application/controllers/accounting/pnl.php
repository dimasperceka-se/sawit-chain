<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pnl extends SS_Controller {

   public function __construct() {
      parent::__construct();
   }

   public function index(){
      $data['js'] = 'profitnloss';
      $api = $this->config->item('api');
      $data['action'] = array(
         'crud'=>$api,
         'startdate'=>intval(date('m')),
         'enddate'=>intval(date('m')),
         'tahun'=>date('Y')
      );
      
      $this->LoadView($data);
   }

}
