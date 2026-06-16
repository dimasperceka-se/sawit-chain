<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Garden extends SS_Controller {

	public function __construct() {
		parent::__construct(1);
		$this->lang->load('dashboard');
      $this->lang->load('home');
		$this->api = $this->config->item('api');
		$url = (index_page())?(base_url().index_page()):rtrim(base_url(),'/');
		$this->action = array(
            'url'           => $url,
            'api'           => $this->api,
            'district'      => $this->api.'/dashboard/district',
            'daer'          => $_SESSION['daerah'],
            'partner'       => $_SESSION['FlagAccess'] == 1 ? $_SESSION['PartnerID'] : '',
            'path'			=> (index_page()?index_page().'/':'').'home/'.$this->router->fetch_class().'/index/'
		);
		$this->titlet = lang('Dashboard').' > ';
	}

	public function index($prov='',$private='',$kab='') {
		$data = array('prov'=>$prov,'private'=>$private,'daer'=>$_SESSION['daerah'],'petani'=>$_GET['petani']);
		$data['js'] = 'garden';
		$data['tahun'] = $this->input->get('tahun');
		$data['survey'] = $this->input->get('survey')!==false?$this->input->get('survey'):2;
		if (empty($data['tahun'])) {
			$data['tahun'] = date('Y');
		}
		$data['action'] = array_merge(array('prov'=>$prov,'kab'=>$kab,'priv'=>$private,'petani'=>$_GET['petani'],
			'data'=>$this->api.'/dashboard/garden', 'tahun' => $data['tahun'], 'survey' => $data['survey']),$this->action);
		$data['title'] = $this->titlet.lang('Kebun Kakao');
		$this->LoadView($data, 'garden');
	}

}

