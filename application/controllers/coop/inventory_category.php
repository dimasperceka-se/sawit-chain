<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventory_category extends SS_Controller {

   public function __construct() {
      parent::__construct();
   }

   public function index(){

      $data['js'] = 'coop_inventory_category';
      $api = $this->config->item('api');
      $data['action'] = array(
         'baseurl'=>base_url(),
         'crud'=>$api.'/cooperatives/invcategory',
         'cruds'=>$api.'/cooperatives/invcategorys',
         'coadatas'=> $api . '/coa/fin_coas',
//         'add'=>$api.'/cooperatives/supplieradd',
//         'edit'=>$api.'/cooperatives/supplieredit',
         'param'=>$_SESSION['userid'],
         'act_index'=> !$this->system->CekAksi('index'),
         'act_add'=> !$this->system->CekAksi('add')?'hide-icon':'',
         'act_update'=> !$this->system->CekAksi('update')?'hide-icon':'',
         'act_delete'=> !$this->system->CekAksi('delete')?'hide-icon':'',
         'act_save'=> !$this->system->CekAksi('update')?'hide-icon':'',
      );

//      print_r($_SESSION);
      $this->LoadView($data);
   }



}
