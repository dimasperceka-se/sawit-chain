<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Group extends SS_Controller {

   public function __construct() {
      parent::__construct();
   }

   public function index() {
      $data['js'] = 'group';
      $api = $this->config->item('api');
      $data['action'] = array('crud'=>$api.'/system/group','unit'=>$api.'/system/units',
         'aksi'=>$api.'/system/groupaksi',
         'aksi_list'=>$api.'/system/groupaksilist',
         'report'=>$api.'/system/groupreport',
         'report_list'=>$api.'/system/groupreportlist',
         'act_add'=> !$this->system->CekAksi('add') ? 'hide-icon' : '',
         'act_update'=> !$this->system->CekAksi('update'),
         'is_admin' => $_SESSION['is_admin'],
         'act_delete'=> !$this->system->CekAksi('delete'));
      $data['style'] = "
         input.l-tcb {
         	height:13px;
         	width:13px;
         	margin-left:2px
         }
         .ext-ie .x-tree {
         	position:static !important;
         }";
      $this->LoadView($data);
   }

}

