<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @Author: Fashah Darullah
 * @Date:   2019-06-11 14:00:00
 */
class Messages_management extends SS_Controller {

   public function __construct() {
      parent::__construct();
   }

   public function index() {
      $data['js'] = 'farmcloud/messages_management';
      $api = $this->config->item('api');
      $data['action'] = array(
         'api_base_url' => $this->config->item('api_base_url'),
         'base_url' => base_url(),
         'curr_year' => date('Y'),
         'user_role' => $_SESSION['role'],
		   'grid_main'=> $api.'/farmcloud/grid_main',
		   'grid_mains'=> $api.'/farmcloud/grid_main_messages',
		   'sync_metadata'=> $api.'/farmer_type/sync_metadata',
         'act_add' => !$this->system->CekAksi('add'),
         'act_update' => !$this->system->CekAksi('update'),
         'act_delete' => !$this->system->CekAksi('delete')
      );
      $this->LoadView($data,'common_content_region');
   }

}

