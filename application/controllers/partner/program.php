<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Program extends SS_Controller {

   public function __construct() {
      parent::__construct();
   }

   public function index() {
      $data['js'] = 'program';
      $api = $this->config->item('api');
      $data['action'] = array('crud'=>$api.'/partner/program',
         'api_base_url' => $this->config->item('api_base_url'),
         'act_index'=> !$this->system->CekAksi('index'),
         'photo'=> $this->config->item('api_base_url').'images/Photo/',

         'districtInPartner'=> $api.'/partner/data_districtInPartner',
         'cpgInPartner'=> $api.'/partner/data_cpgInPartner',
         'Province'=> $api.'/partner/data_province',
         'District'=> $api.'/partner/data_district',
         'list_district' => $api.'/partner/list_district',
         'list_cpg' => $api.'/partner/list_cpg',
         'districtPartners'=> $api.'/partner/districtPartners',
         'district_find'=> $api.'/partner/district_find',
         'act_add'=> !$this->system->CekAksi('add')?'hidden':'',
         'act_update'=> !$this->system->CekAksi('update')?'hidden':'',
         'act_delete'=> !$this->system->CekAksi('delete')?'hidden':'',
         'act_assign_cpg'=> $this->system->CekAksi('partner_assign_cpg')
         );
      $this->LoadView($data);
   }

}

