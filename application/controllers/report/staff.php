<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Staff extends SS_Controller {
   
   public function __construct() {
      parent::__construct();
   }

   public function index() {
      $data['js'] = 'staff_progress';
      $api = $this->config->item('api');
      $data['action'] = array(
            'crud'=>$api.'/report/staff',
            'staff'=>$api.'/report/Staffs',
            'Kabupaten'=>$api.'/report/Kabupatens',
            'Provinsi'=>$api.'/report/Provinsis',
            'detail'=>$api.'/report/staff_details',
            'act_index'=> !$this->system->CekAksi('index'),
            'export_details'=>$api.'/report/export_excel_details/',
      );
      
      $data['style'] = ".x-toolbar-footer{background-color:#FFFFFF !important}";
      $this->LoadView($data);
   }

}

