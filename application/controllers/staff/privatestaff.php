<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Privatestaff extends SS_Controller {
   
   public function __construct() {
      parent::__construct();
   }

   public function index() {
      $data['js'] = 'privatestaff';
      $api = $this->config->item('api');
      $data['action'] = array('crud'=>$api.'/staff/privatestaff',
         'group'=>$api.'/system/grouplist',
         'cek_username'=>$api.'/system/cek_username',
         'partner'=>$api.'/staff/partnerlist',
         'Desa'=>$api.'/farmer/Desas',
         'Kecamatan'=>$api.'/farmer/Kecamatans',
         'Kabupaten'=>$api.'/farmer/Kabupatens',
         'Provinsi'=>$api.'/farmer/Provinsis',
         'photo'=> $this->config->item('api_base_url').'images/Photo/',

         'act_index'=> !$this->system->CekAksi('index'),
         'act_add'=> !$this->system->CekAksi('add'),
         'act_update'=> !$this->system->CekAksi('update'),
         'act_delete'=> !$this->system->CekAksi('delete'));
      $this->LoadView($data);
   }

}

