<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Unit extends SS_Controller {

   public function __construct() {
      parent::__construct();
   }

   public function index() {
      $data['js'] = 'unit';
      $api = $this->config->item('api');
      $data['action'] = array('crud'=>$api.'/system/unit',
         'act_add'=> !$this->system->CekAksi('add')?'hide-icon':'',
         'act_update'=> !$this->system->CekAksi('update')?false:true,
         'act_delete'=> !$this->system->CekAksi('delete')?'hide-icon':'');
      $this->LoadView($data);
   }

}

