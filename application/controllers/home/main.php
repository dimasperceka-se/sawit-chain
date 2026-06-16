<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends SS_Controller {

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
		$sql['get_org'] = "
		select StaffID id, StaffSupplychainID orgid from ktv_supplychain_staff
		left join ktv_supplychain_org_view on SupplychainID=StaffSupplychainID
		where UserID=? and OrgType in ('Organisasi Petani','Pedagang','Kelompok Petani')";
		$menu['org'] = $this->system->GetSql($sql['get_org'], array($_SESSION['userid']));
		$data = array('prov'=>$prov,'private'=>$private,'daer'=>$_SESSION['daerah']);
		if ($menu['org'][0]['id']!='') {
			$_GET['awal'] = ($_GET['awal']==''?(date('Y')-1).date('-m-d'):$_GET['awal']);
			$_GET['akhir'] = ($_GET['akhir']==''?date('Y-m-d'):$_GET['akhir']);
			$data['action'] = array_merge(array('prov'=>$prov,'kab'=>$kab,'priv'=>$private,'awal'=>$_GET['awal'],
				'akhir'=>$_GET['akhir'],'data'=>$this->api.'/dashboard/traceability_','orgid'=>$menu['org'][0]['orgid'],
				'now'=>date('Y')),$this->action);
			$data['title'] = $this->titlet.lang('Traceability');
			$data['tgl'] = $_GET;
			$data['url_param'] = $prov.'/'.$private.'/'.$kab;
			$template = 'home_';
			$data['js'] = 'dashboard_';
		} else {
			$template='home';
			$data['js'] = 'dashboard';
			$data['action'] = array_merge(array('prov'=>$prov,'kab'=>$kab,'priv'=>$private,'data'=>$this->api.'/dashboard/dashboards'),$this->action);
		}

		$data['title'] = $this->titlet.lang('Main Dashboard');
		// echo '<pre>'; print_r($data); echo '</pre>';
		$this->LoadView($data, $template);
	}

}

