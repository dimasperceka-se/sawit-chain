<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends SS_Controller {

    public function __construct() {
        parent::__construct(1);
        $this->lang->load('dashboard');
        $this->lang->load('home');
        $this->api = $this->config->item('api');
        $url = (index_page()) ? (base_url() . index_page()) : rtrim(base_url(), '/');
        $this->action = array(
            'url' => $url,
            'api' => $this->api,
            'district' => $this->api . '/dashboard/district',
            'daer' => $_SESSION['daerah'],
            'partner' => $_SESSION['FlagAccess'] == 1 ? $_SESSION['PartnerID'] : '',
            'path' => (index_page() ? index_page() . '/' : '') . 'home/home/' . $this->router->fetch_method() . '/',
        );
        $this->titlet = lang('Dashboard') . ' > ';
    } 
	 
	//Ini Untuk Dasboard Tracebility Farmer 
	public function traceability_index($prov='',$private='',$kab='') {
        $data             = array('prov'=>$prov,'private'=>$private,'daer'=>$_SESSION['daerah']);
        $data['js']      = 'traceability/dashboard/traceability_index'; 
        $filter['awal'] =(date('Y')-1).date('-m-d');
		$filter['akhir'] = date('Y-m-d');
		$data['action'] = array(
            'crud'              => $this->api.'/dashboard/traceability_index',
			'urlFitler'         => $this->load->view('trace_hub_chart_filtering', $filter),
            'base_url'          => base_url(),
            'dash'              => $this->api.'/dashboard/',
			'awal'           	=> (date('Y')-1).date('-m-d'),
            'akhir'          	=> date('Y-m-d'),
            'realname'          => $_SESSION['realname'],
            'group'             => $_SESSION['group'],  
			'ch'				=> $this->input->get('ch'),
			'bs'					=> $this->input->get('bs') 
         );
        $data['url_param']  = $prov.'/'.$private.'/'.$kab;
		$this->LoadView($data);
	}
        
	//Ini untuk Dasboard tracebility New
    public function traceability_index_new($prov='',$private='',$kab='') {
        $data             = array('prov'=>$prov,'private'=>$private,'daer'=>$_SESSION['daerah']);
        $data['js']       = 'traceability/dashboard/traceability_index_new'; 
        $filter['awal']   = (date('Y')-1).date('-m-d');
		$filter['akhir']  = date('Y-m-d');
		$data['action']   = array(
            'crud'              => $this->api.'/dashboard/traceability_index_new',
			'urlFitler'         => $this->load->view('trace_hub_chart_filtering', $filter),
            'base_url'          => base_url(),
            'dash'              => $this->api.'/dashboard/',
			'awal'           	=> (date('Y')-1).date('-m-d'),
            'akhir'          	=> date('Y-m-d'),
            'realname'          => $_SESSION['realname'],
            'group'             => $_SESSION['group'],  
			'ch'				=> $this->input->get('ch'),
			'bs'					=> $this->input->get('bs') 
         );
        $data['url_param']  = $prov.'/'.$private.'/'.$kab;
		$this->LoadView($data);
	}   
        
    public function traceability($prov='',$private='',$kab='') {
        $data             = array('prov'=>$prov,'private'=>$private,'daer'=>$_SESSION['daerah']);
        $data['js']      = 'traceability/dashboard/traceability'; 
        $filter['awal'] =(date('Y')-1).date('-m-d');
        $filter['akhir'] = date('Y-m-d');
        $data['action'] = array(
            'crud'              => $this->api.'/dashboard/traceability_index_new',
            'urlFitler'         => $this->load->view('trace_hub_chart_filtering', $filter),
            'base_url'          => base_url(),
            'dash'              => $this->api.'/dashboard/',
            'awal'              => (date('Y')-1).date('-m-d'),
            'akhir'             => date('Y-m-d'),
            'realname'          => $_SESSION['realname'],
            'group'             => $_SESSION['group'],  
            'ch'                => $this->input->get('ch'),
            'bs'                => $this->input->get('bs') 
         );
        $data['url_param']  = $prov.'/'.$private.'/'.$kab;
        $this->LoadView($data);
    }    

}

