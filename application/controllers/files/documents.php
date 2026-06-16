<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Documents extends SS_Controller {
   
   public function __construct() {
      parent::__construct();
   }

   public function index() {
      $data['js'] = 'documents';
      $api = $this->config->item('api');
      $data['action'] = array('crud'=>$api.'/files/documents',
         'act_index'=> !$this->system->CekAksi('index'),
         'act_add'=> !$this->system->CekAksi('add'),
         'act_update'=> !$this->system->CekAksi('update'),
         'act_delete'=> !$this->system->CekAksi('delete'),
         'api'=>str_replace('index.php','',$api));
      $this->LoadView($data);
   }

}

