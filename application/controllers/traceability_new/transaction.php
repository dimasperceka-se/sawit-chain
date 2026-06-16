<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Transaction extends SS_Controller {
   
   public function __construct() {
      parent::__construct();
   }

    public function index($id = '')
    {
        $data['js'] = 'traceability_new/transaction';
        $api = $this->config->item('api'); 
       
      $data['action'] = array(
            'api_base_url'  => $this->config->item('api_base_url'),
            'act_add'       => !$this->system->CekAksi('add'),
            'act_update'    => !$this->system->CekAksi('update'),
            'act_delete'    => !$this->system->CekAksi('delete'),
            'act_view'      => !$this->system->CekAksi('view'),
            'now'           => date('Y-m-d H:i:s'),
            'date'          => date('Y-m-d'),
            'time'          => date('H:i'),
            'sys_date'      => date('Ymd'),
            'sid'           => $_SESSION['SupplychainID'],
            'pid'           => $_SESSION['PartnerID']  
        );
        $this->LoadView($data);
    }


}
