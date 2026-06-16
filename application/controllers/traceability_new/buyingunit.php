<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Buyingunit extends SS_Controller {
   
   public function __construct() {
      parent::__construct();
   }

   public function index() {
      $data['js'] = 'buyingunit';
      $api = $this->config->item('api');
      $data['action'] = array(
         'title'=>'Buying Unit',
         'type'=>'buyingunit',

         'crud'=>$api.'/traceability/',
         'Desa'=>$api.'/farmer/Desas',
         'Kecamatan'=>$api.'/farmer/Kecamatans',
         'Kabupaten'=>$api.'/farmer/Kabupatens',
         'Provinsi'=>$api.'/farmer/Provinsis',
         'photo'=> $this->config->item('api_base_url').'images/Photo_traceability/',
         'farmer_staff' => 'show',

         'staff'=>$api.'/traceability/staff',
         'var_quality'=>$api.'/traceability/var_quality',
         'quality_standard'=>$api.'/traceability/quality_standard',
         'relasi'=>$api.'/traceability/relasi',
         'perwakilan'=>$api.'/traceability/perwakilan',
         'standard'=>$api.'/traceability/quality_standard_combo',
         'reward'=>$api.'/traceability/reward',
         'quality'=>$api.'/traceability/quality',
         'quality_var'=>$api.'/traceability/quality_var',
         'price'=>$api.'/traceability/price',
         'package'=>$api.'/traceability/package',
         'kurs'=>$api.'/traceability/kurs',
         'premium'=>$api.'/traceability/premium',
         'store_nursey_penjualans'=>$api.'/cpg/nursey_penjualans',
         'objid'=>$api.'/traceability/objids',

         'act_setting'=> !$this->system->CekAksi('setting'),
         'act_relasi'=> !$this->system->CekAksi('relasi'),
         'act_perwakilan'=> !$this->system->CekAksi('perwakilan'),
         'act_standard_quality'=> !$this->system->CekAksi('standard_quality'),
         'act_harga'=> !$this->system->CekAksi('harga'),
         'act_quality'=> !$this->system->CekAksi('quality'),
         'act_kemasan'=> !$this->system->CekAksi('kemasan'),
         'act_reward'=> !$this->system->CekAksi('reward'),
         'act_premium'=> !$this->system->CekAksi('premium'),
         'act_kurs'=> !$this->system->CekAksi('kurs'),

         'act_index'=> !$this->system->CekAksi('index'),
         'act_add'=> !$this->system->CekAksi('add')?'hide-icon':'',
         'act_update'=> $this->system->CekAksi('update'),
         'act_delete'=> !$this->system->CekAksi('delete')?'hide-icon':'',
         'act_save'=> !$this->system->CekAksi('index'));
      $this->LoadView($data);
   }

}

