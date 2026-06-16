<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Extension extends SS_Controller {
   
   public function __construct() {
      parent::__construct();
   }

   public function index() {
      $data['js'] = 'extension';
      $api = $this->config->item('api');
      $data['action'] = array('crud'=>$api.'/staff/extension',
         'Desa'=>$api.'/farmer/Desas',
         'Kecamatan'=>$api.'/farmer/Kecamatans',
         'Kabupaten'=>$api.'/farmer/Kabupatens',
         'Provinsi'=>$api.'/farmer/Provinsis',
         'AllProvinsi'=>$api.'/farmer/AllProvinsis',
         'photo'=> $this->config->item('api_base_url').'images/Photo/',
         'cetak'=>$api.'/staff/cetak_extension/',
         
         'InstitutionID'=>$api.'/staff/InstitutionIDs',
         'act_index'=> !$this->system->CekAksi('index'),
         'act_add'=> !$this->system->CekAksi('add'),
         'act_update'=> !$this->system->CekAksi('update'),
         'act_delete'=> !$this->system->CekAksi('delete'));
      $this->LoadView($data);
   }

}

