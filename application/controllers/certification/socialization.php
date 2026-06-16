<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Socialization extends SS_Controller
{
    public function __construct()
    {
        parent::__construct(1);
    }  

    public function index($id = ''){
                                                                                                                       
        $data['js'] = 'certification/socialization';
		$prov = !empty($this->input->get('prov'))?$this->input->get('prov'):'';
		$dist = !empty($this->input->get('dist'))?$this->input->get('dist'):''; 
        $subdist = !empty($this->input->get('subdist'))?$this->input->get('subdist'):''; 
		
        $data['action'] = array(
            'api_base_url'                          => $this->config->item('api_base_url'), 
            'act_add'                               => $this->system->CekAksi('add'),
            'act_update'                            => $this->system->CekAksi('update'),
            'act_delete'                            => $this->system->CekAksi('delete'), 
			'act_add_socializ_participant'          => $this->system->CekAksi('add_socializ_participant'),
            'act_update_socializ_participant'       => $this->system->CekAksi('update_socializ_participant'),
            'act_delete_socializ_participant'       => $this->system->CekAksi('delete_socializ_participant'), 
			'act_delete_socializ_staff'				=> !$this->system->CekAksi('delete_socializ_staff'),
			'act_add_socializ_staff'       			=> !$this->system->CekAksi('add_socializ_staff'),
			'prov'									=> $prov,
			'dist'									=> $dist,
			'subdist'								=> $subdist
        );
		$data['mentokDistrict'] = true; // untuk setting apakah filter region mau sampai district atau subdistrict
        $this->LoadView($data, 'common_content_region'); //selalu load view "common_content_region" ini untuk filter region yg seragam
    }
}
?>