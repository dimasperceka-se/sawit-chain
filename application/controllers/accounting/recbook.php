<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Recbook extends SS_Controller {

   public function __construct() {
      parent::__construct();
   }

   public function index(){
      $data['js'] = 'recbook5';
      $api = $this->config->item('api');
      $data['action'] = array(
         'crud'=>$api,
         'baseurl'=>base_url(),
         'coadatas'=> $api . '/coa/fin_coas',
         'assetdatas'=> $api .'/cooperatives/getDataInventorys'
      );
      
      $this->LoadView($data);
   }

}
