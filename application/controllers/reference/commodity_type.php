<?php
/**
 * @Author: nikolius
 * @Date:   2016-03-31 13:23:47
 */
class Commodity_type extends SS_Controller {

   public function __construct() {
      parent::__construct();
   }

   public function index() {
      $data['js'] = 'commodity_type';
      $api = $this->config->item('api');
      $data['action'] = array('crud'=>$api.'/reference/commodity_type',
         'act_add'=> !$this->system->CekAksi('add')?'hide-icon':'',
         'act_update'=> !$this->system->CekAksi('update')?'hide-icon':'',
         'act_delete'=> !$this->system->CekAksi('delete')?'hide-icon':'');
      $this->LoadView($data);
   }
}
?>