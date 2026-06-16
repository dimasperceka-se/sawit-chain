<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Retur_penjualan extends SS_Controller {

   public function __construct() {
      parent::__construct();
   }

   public function index(){
      $data['js'] = 'bussiness_retur_penjualan';
      $api = $this->config->item('api');
      $data['action'] = array(
         'crud'=>$api.'/bussiness/retur_penjualan',

         'act_index'=> !$this->system->CekAksi('index'),
         'act_add'=> !$this->system->CekAksi('add'),
         'act_update'=> !$this->system->CekAksi('update'),
         'act_delete'=> !$this->system->CekAksi('delete'));
      $data['style'] = "
         .biggertext{font-size: 16px;font-weight:bold}"; 
      $this->LoadView($data);
   }

}
