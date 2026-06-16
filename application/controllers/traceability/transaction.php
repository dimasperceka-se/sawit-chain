<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Transaction extends SS_Controller {
   
   public function __construct() {
      parent::__construct();
   }

   public function index() {
      $data['js'] = 'transaction';
      $api = $this->config->item('api');
      $data['action'] = array(
         'crud'=>$api.'/traceability/transaction',
         'perwakilan'=>$api.'/traceability/perwakilans',
         'now'=>date('Y-m-d'),

         'cetak_kuitansi'=>$api.'/buying/cetak_kuitansi/',
         'cetak_kuitansi_batch'=>$api.'/buying/cetak_kuitansi_batch/',
         'cetak_packing_list'=>$api.'/buying/cetak_packing_list/',
         'preview_file'=>$this->config->item('api_base_url'),
         
         'act_index'=> !$this->system->CekAksi('index'),
         'act_save'=> !$this->system->CekAksi('add'),
         'act_add'=> !$this->system->CekAksi('add'),
         'act_update'=> !$this->system->CekAksi('update'),
         'act_delete'=> !$this->system->CekAksi('delete'));
      $this->LoadView($data);
   }

}

