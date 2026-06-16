<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Action extends SS_Controller {

	public function __construct()
	{
		parent::__construct();
		//Do your magic here
	}

	public function index()
	{
		$data['js'] = 'Action';
		$api = $this->config->item('api');
		$data['action'] = array(
		 'crud'         => $api.'/system/action',

		 'act_index'    => $this->system->CekAksi('index'),
		 'act_add'      => $this->system->CekAksi('add'),
		 'act_update'   => $this->system->CekAksi('update'),
		 'act_delete'   => $this->system->CekAksi('delete'));
      	$this->LoadView($data);
	}

}

/* End of file user_affiliate.php */
/* Location: ./application/controllers/system/user_affiliate.php */