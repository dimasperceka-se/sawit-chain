<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Extention extends SS_Controller {
   
   public function __construct() {
      parent::__construct(1);
   }

   public function index() {
      $data['js'] = 'extention';
      $api = $this->config->item('api');
      $data['action'] = array('crud'=>$api.'/extention/data');
      $this->LoadView($data);
   }

}

