<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Buying extends SS_Controller {
   
   public function __construct() {
      parent::__construct();
   }

   public function index() {
      $data['js'] = 'buying';
      $api = $this->config->item('api');
      $data['action'] = array('crud'=>$api.'/buying/data',
         'act_index'=> !$this->system->CekAksi('index'),
         'detail'=>$api.'/buying/detail',
         'cetak_kuitansi'=>$api.'/buying/cetak_kuitansi/',

         'Province'=>$api.'/partner/data_province',
         'District'=>$api.'/partner/data_district',
         'package'=>$api.'/partner/data_package',

         'act_save'=> !$this->system->CekAksi('add'),
         'act_add'=> !$this->system->CekAksi('add'),
         'act_update'=> !$this->system->CekAksi('update'),
         'act_delete'=> !$this->system->CekAksi('delete'));
      $this->LoadView($data);
   }

}

