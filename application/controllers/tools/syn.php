<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Syn extends SS_Controller {
   
   public function __construct() {
      parent::__construct();
   }

   public function index() {
      $this->lang->load('tools');
      $data['lang'] = $this->lang->language;
      $data['js'] = 'syn';
      $api = $this->config->item('api');
      $data['action'] = array(
         'crud'=>$api.'/tools/syn',
         'act_index'=> !$this->system->CekAksi('index'),
         'act_add'=> !$this->system->CekAksi('add'),
         'act_update'=> !$this->system->CekAksi('update'));
      $this->LoadView($data);
   }

}

