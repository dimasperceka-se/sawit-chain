<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Agriinput extends SS_Controller {

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
		$data = array('prov'=>$prov,'private'=>$private,'daer'=>$_SESSION['daerah']);
		$data['js'] = 'agriinput';
		$data['action'] = array_merge(array('prov'=>$prov,'kab'=>$kab,'priv'=>$private,'data'=>$this->api.'/dashboard/agriinput',
			'now'=>date('Y')),$this->action);
		$data['title'] = $this->titlet.lang('Agri Inputs');

		$this->LoadView($data, 'agriinput');
	}

}

