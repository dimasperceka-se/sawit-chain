<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_affiliate extends SS_Controller {

	public function __construct()
	{
		parent::__construct();
		//Do your magic here
	}

	public function index()
	{
		$data['js'] = 'UserAffiliate';
		$api = $this->config->item('api');
		$data['action'] = array(
		 'crud'         => $api.'/system/user_affiliate',
		 'group'        => $api.'/system/grouplist',
		 'lang_list'          => $api.'/system/lang_list',
		 'role_list'          => $api.'/system/role_list',
		 'partner_list'       => $api.'/staff/partnerlist',
		 'province_list'      => $api.'/farmer/provinces',
		 'district_list'      => $api.'/farmer/districts',
		 'access_staffs'      => $api.'/farmer/access_staffs',
		 'work_area_list'      => $api.'/farmer/workareas',
		 'subdistrict_list'   => $api.'/farmer/subdistricts',
		 'village_list'       => $api.'/farmer/villages',
		 'cpg_list'           => $api.'/farmer/CPGlist',
		 'cooperative_list'   => $api.'/cooperatives/cooperative_list',
		 'sce_list'           => $api.'/sce/sce_list',
		 'trader_list'        => $api.'/trader/trader_list',
		 'warehouse_list'     => $api.'/warehouse/warehouse_list',
		 'farmer_list'        => $api.'/farmer/farmer_list',
		 'bank_list'          => $api.'/bank/banglist',
		 'bank_branch_list'   => $api.'/bank/branchlist',

		 'act_index'    => $this->system->CekAksi('index'),
		 'act_add'      => $this->system->CekAksi('add'),
		 'act_update'   => $this->system->CekAksi('update'),
		 'act_delete'   => $this->system->CekAksi('delete'));
      	$this->LoadView($data);
      // print_r($_SESSION);
	}

}

/* End of file user_affiliate.php */
/* Location: ./application/controllers/system/user_affiliate.php */