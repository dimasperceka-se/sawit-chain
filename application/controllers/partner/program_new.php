<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Program_new extends SS_Controller {

   public function __construct() {
      parent::__construct();
   }

   public function index() {
      $data['js'] = 'program_new';
      $api = $this->config->item('api');
      $data['action'] = array('crud'=>$api.'/partner_new/program',
         'api_base_url' => $this->config->item('api_base_url'),
         'base_url_additional' => $this->config->item('base_url'),
         'act_index'=> !$this->system->CekAksi('index'),
         'photo'=> $this->config->item('api_base_url').'images/Photo/',

         'districtInPartner'=> $api.'/partner/data_districtInPartner',
         'url_awss3' => $this->config->item('CTCDN'),
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

