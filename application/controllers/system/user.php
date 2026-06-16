<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends SS_Controller {

   public function __construct() {
      parent::__construct();
   }

   public function index() {
      $data['js'] = 'user';
      $api = $this->config->item('api');
      $data['action'] = array('crud'=>$api.'/system/user','group'=>$api.'/system/grouplist','group_search'=>$api.'/system/grouplist_search',
         'act_index'=> !$this->system->CekAksi('index'),
         'act_add'=> !$this->system->CekAksi('add')?'hide-icon':'',
         'act_update'=> !$this->system->CekAksi('update')?'hide-icon':'',
         'act_delete'=> !$this->system->CekAksi('delete')?'hide-icon':'');
      $this->LoadView($data);
   }

}

