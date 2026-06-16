<?php
 
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Dispatch extends SS_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
	
    public function index()
	{
		$data['js']     = 'traceability/Dispatch';
        $user = $this->db->query('SELECT UserRealName FROM sys_user where userid = ?', array($_SESSION['userid']))->row();
        $data['action'] = array(
            'api_base_url'                          => $this->config->item('api_base_url') .'/receipt',
            'base_url'                              => $this->config->item('base_url'),
            'act_add'                               => !$this->system->CekAksi('add'),
            'act_update'                            => !$this->system->CekAksi('update'),
            'act_delete'                            => !$this->system->CekAksi('delete'),
            'act_export'                            => !$this->system->CekAksi('export'),
            'act_approval'                          => !$this->system->CekAksi('approval'),
            'ud'                                    => $_SESSION['userid'],
			'person' 								=> $user->UserRealName 
        );

        $data['mentokDistrict'] = false; // untuk setting apakah filter region mau sampai district atau subdistrict
        $this->LoadView($data); //selalu load view "common_content_region" ini untuk filter region yg seragam
	} 
	
}
?>