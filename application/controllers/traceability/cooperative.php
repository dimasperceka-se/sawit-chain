<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cooperative extends SS_Controller {
   
   public function __construct() {
      parent::__construct();
   }

   public function index() {
      $data['js'] = 'buyingunit';
      $api = $this->config->item('api');
      $data['action'] = array(
         'title'=>'Koperasi',
         'type'=>'cooperation',

         'crud'=>$api.'/traceability/',
         'Desa'=>$api.'/farmer/Desas',
         'Kecamatan'=>$api.'/farmer/Kecamatans',
         'Kabupaten'=>$api.'/farmer/Kabupatens',
         'Provinsi'=>$api.'/farmer/Provinsis',
         'photo'=> $this->config->item('api_base_url').'images/Photo_traceability/',
         'farmer_staff' => 'show',

         'staff'=>$api.'/traceability/staff',
         'quality_standard'=>$api.'/traceability/quality_standard',
         'standard'=>$api.'/traceability/quality_standard_combo',
         'reward'=>$api.'/traceability/reward',
         'quality'=>$api.'/traceability/quality',
         'price'=>$api.'/traceability/price',
         'package'=>$api.'/traceability/package',
         'store_nursey_penjualans'=>$api.'/cpg/nursey_penjualans',

         'act_index'=> !$this->system->CekAksi('index'),
         'act_add'=> !$this->system->CekAksi('add'),
         'act_update'=> !$this->system->CekAksi('update'),
         'act_delete'=> !$this->system->CekAksi('delete'),
         'act_save'=> !$this->system->CekAksi('index'));
      $this->LoadView($data);
   }

}

