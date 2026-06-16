<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Program extends SS_Controller {
   
   public function __construct() {
      parent::__construct();
   }

   public function index() {
      $data['js'] = 'staff_program';
      $api = $this->config->item('api');
      $data['action'] = array('crud'=>$api.'/staff/program',
         'group'=>$api.'/system/grouplist',
         'cek_username'=>$api.'/system/cek_username',
         'partner'=>$api.'/staff/partnerlist',
         'Desa'=>$api.'/farmer/Desas',
         'Kecamatan'=>$api.'/farmer/Kecamatans',
         'Kabupaten'=>$api.'/farmer/Kabupatens',
         'Provinsi'=>$api.'/farmer/Provinsis',
         'AllProvinsi'=>$api.'/farmer/AllProvinsis',
         'photo'=> $this->config->item('api_base_url').'images/Photo/',

         'districtInStaff'=>$api.'/staff/data_districtInStaff',
         'Province'=>$api.'/partner/data_province',
         'District'=>$api.'/partner/data_district',

         'act_index'=> !$this->system->CekAksi('index'),
         'act_add'=> !$this->system->CekAksi('add'),
         'act_update'=> !$this->system->CekAksi('update'),
         'act_delete'=> !$this->system->CekAksi('delete'));
      $this->LoadView($data);
   }

}

