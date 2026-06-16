<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Kml extends SS_Controller {
   
   public function __construct() {
      parent::__construct();
   }

   public function index() {
      $this->load->language('basic');
      $data['js'] = 'basic/kml';
      $prov = !empty($this->input->get('prov'))?$this->input->get('prov'):'';
      $dist = !empty($this->input->get('dist'))?$this->input->get('dist'):'';

      $data['action'] = array(
         'param'      => $prov,
         'act_index'  => $this->system->CekAksi('index'),
         'act_add'    => $this->system->CekAksi('add'),
         'act_update' => $this->system->CekAksi('update'),
         'act_delete' => $this->system->CekAksi('delete'));
      $this->LoadView($data);
   }

}

