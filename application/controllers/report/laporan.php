<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Laporan extends SS_Controller {
   
   public function __construct() {
      parent::__construct();
   }

   public function index() {
      $data['js'] = 'laporan';
      $api = $this->config->item('api');
      $data['action'] = array('crud'=>$api.'/report/laporan',
         'Kabupaten'=>$api.'/report/Kabupatens',
         'Provinsi'=>$api.'/report/Provinsis',
         'Survey'=>$api.'/report/Surveys',
         'year' => $api . '/report/year',
         'piestore'=>$api.'/report/Piestore',
         'menu' => $api.'/report/menu',
         'act_index'=> !$this->system->CekAksi('index'),
         'act_baseline'=> !$this->system->CekAksi('baseline'));
      $data['style'] = "
         .x-toolbar-footer{background-color:#FFFFFF !important}";
      $this->LoadView($data);
   }

}

