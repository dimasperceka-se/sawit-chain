<?php
/**
 * @Author: nikolius
 * @Date:   2016-03-17 16:43:03
 * @Last Modified by:   nikolius
 * @Last Modified time: 2016-03-17 17:04:29
 */
class Position extends SS_Controller {

   public function __construct() {
      parent::__construct();
   }

   public function index() {
      $data['js'] = 'position';
      $api = $this->config->item('api');
      $data['action'] = array(
         'crud' => $api.'/reference/position',
         'combo_role' => $api.'/reference/position_combo_role',
         'act_add'=> !$this->system->CekAksi('add')?'hide-icon':'',
         'act_update'=> !$this->system->CekAksi('update')?'hide-icon':'',
         'act_delete'=> !$this->system->CekAksi('delete')?'hide-icon':'');
      $this->LoadView($data);
   }
}
?>