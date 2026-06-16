<?php
/**
 * @Author: Aprianto 
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Register extends SS_Controller
{
    public function __construct()
    {
        parent::__construct(1);
    }  

    public function index($id = ''){
                                                                                                                       
        $data['js']     = 'application_form/register';
		$prov = !empty($this->input->get('prov'))?$this->input->get('prov'):'';
		$dist = !empty($this->input->get('dist'))?$this->input->get('dist'):''; 
        $subdist = !empty($this->input->get('subdist'))?$this->input->get('subdist'):''; 
		
        $data['action'] = array(
            'api_base_url'                          => $this->config->item('api_base_url'), 
			'act_view'                              => $this->system->CekAksi('view'),
            'act_add'                               => $this->system->CekAksi('add'),
            'act_update'                            => $this->system->CekAksi('update'),
            'act_delete'                            => $this->system->CekAksi('delete'), 
			'prov'									=> $prov,
			'dist'									=> $dist,
			'subdist'								=> $subdist
        );
		$data['mentokDistrict'] = false; // untuk setting apakah filter region mau sampai district atau subdistrict
        $this->LoadView($data, 'common_content_region'); //selalu load view "common_content_region" ini untuk filter region yg seragam
    }
}
?>