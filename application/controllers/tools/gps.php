<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gps extends SS_Controller {
   
   public function __construct() {
      parent::__construct();
   }

   public function index() {
      $data['js'] = 'gps';
      $api = $this->config->item('api');
      $data['action'] = array(
         'crud'=>$api.'/tools/gps',
         'url'=>base_url(),
         'act_index'=> !$this->system->CekAksi('index'),
         'act_add'=> !$this->system->CekAksi('add'),
         'act_update'=> !$this->system->CekAksi('update'));
      $data['style'] = "
      .error .x-grid-cell { 
         background-color: #F2DEDE;
         color: #333;
      }           
      .no-error .x-grid-cell { 
         background-color: #DFF0D8;
         color: #333;
      }
     ";
      $this->LoadView($data);
   }

}

