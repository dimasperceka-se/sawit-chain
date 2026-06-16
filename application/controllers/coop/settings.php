<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Settings extends SS_Controller {

   public function __construct() {
      parent::__construct();
   }

   public function index(){
       
      $data['js'] = 'coop_settings';
      $api = $this->config->item('api');
      $data['action'] = array(
         'api'=>$api,
         'crud'=>$api.'/cooperatives/settingdatacoop',
         'staff'=>$api.'/cooperatives/staffs',
         'board'=>$api.'/cooperatives/board',
         'cruddoc'=>$api.'/cooperatives/document',
         'param'=>$_SESSION['userid'],
         'Desa'=>$api.'/farmer/Desas',
         'Kecamatan'=>$api.'/farmer/Kecamatans',
         'Kabupaten'=>$api.'/farmer/Kabupatens',
         'Provinsi'=>$api.'/farmer/Provinsis',
         'act_index'=> !$this->system->CekAksi('index'),
         'act_add'=> !$this->system->CekAksi('add')?'hide-icon':'',
         'act_update'=> !$this->system->CekAksi('update')?'hide-icon':'',
         'act_delete'=> !$this->system->CekAksi('delete')?'hide-icon':'',
         'act_save'=> !$this->system->CekAksi('update')?'hide-icon':'',
      );
      
//      print_r($_SESSION);
      $this->LoadView($data);
   }

}
