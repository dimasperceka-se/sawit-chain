<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Member extends SS_Controller {
   
   public function __construct() {
      parent::__construct();
   }

   public function index() {
        $data['js'] = 'report_member';
        $api = $this->config->item('api');
        $data['action'] = array(
            'api' => $api,
            'baseurl' => base_url(),
            'rpt' => $api . '/report',
            'act_index'=> !$this->system->CekAksi('index'),
        );
        $this->LoadView($data);
    }

} // end of class