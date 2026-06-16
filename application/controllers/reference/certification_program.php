<?php
class Certification_program extends SS_Controller {

   public function __construct() {
      parent::__construct();
   }

   public function index() {
      $data['js'] = 'certification_program';
      $api = $this->config->item('api');
      $data['action'] = array(
         'crud'         => $api.'/reference/certification_program',
         'photo'        => $this->config->item('api_base_url') . 'images/certification_provider',
         'act_index'    => $this->system->CekAksi('index'),
         'act_add'      => $this->system->CekAksi('add'),
         'act_update'   => $this->system->CekAksi('update'),
         'act_delete'   => $this->system->CekAksi('delete'));
      $this->LoadView($data);
   }
}
?>