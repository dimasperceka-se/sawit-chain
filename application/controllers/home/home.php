<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends SS_Controller {

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
            'path'			=> (index_page()?index_page().'/':'').'home/home/'.$this->router->fetch_method().'/',
		);
		$this->titlet = lang('Dashboard').' > ';
	}

	public function index()
	{
		$sql = "SELECT
    m.MenuModule,
    m.MenuParam
FROM
    sys_group g
JOIN sys_menu m ON m.MenuId = GroupMenuID
WHERE
	GroupId = ?
		";
        $group      = $this->system->GetSql($sql, array($_SESSION['groupid']));
        $tmp        = explode('/', $group[0]['MenuModule']);
        $module     = count($tmp)==2 ? ($group[0]['MenuModule'].'/index') : $group[0]['MenuModule'];
        $param      = ($group[0]['MenuParam']?('/'.$group[0]['MenuParam']):'');
        $default_module = $module.$param;
        if ($default_module) {
			$default_module = site_url($module.$param);
        }
        $data['action'] = $this->action;
		$data['default_module'] = $default_module;
		$this->LoadView($data, 'default');

	}

	public function main($prov='',$private='',$kab='') {
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

	public function demographic($prov='',$private='',$kab='') {
		$data = array('prov'=>$prov,'private'=>$private,'daer'=>$_SESSION['daerah'],'petani'=>$_GET['petani']);
		$data['js'] = 'demographic';
		$data['tahun'] = $this->input->get('tahun');
		if (empty($data['tahun'])) {
			$data['tahun'] = date('Y');
		}
		$data['action'] = array_merge(array('prov'=>$prov,'kab'=>$kab,'priv'=>$private,'petani'=>$_GET['petani'],'tahun'=>$data['tahun'],
			'data'=>$this->api.'/dashboard/demographic'),$this->action);
		$data['title'] = $this->titlet.lang('Demografis');
		$this->LoadView($data, 'demographic');
	}

	public function groups($prov='',$private='',$kab='') {
		$data = array('prov'=>$prov,'private'=>$private,'daer'=>$_SESSION['daerah']);
		$data['js'] = 'groups';
		$data['action'] = array_merge(array('prov'=>$prov,'kab'=>$kab,'priv'=>$private,'data'=>$this->api.'/dashboard/groups'),$this->action);
		$data['title'] = $this->titlet.lang('Grup & Bisnis');
		$this->LoadView($data, 'groups');
	}

	public function certification($prov='',$private='',$kab='') {
		$data = array('prov'=>$prov,'private'=>$private,'daer'=>$_SESSION['daerah']);
		$data['js'] = 'certification';
		$data['startdate'] = $this->input->get('startdate');
		if (empty($data['startdate'])) {
			$data['startdate'] = date('Y-m-d');
		}
		$data['enddate'] = $this->input->get('enddate');
		if (empty($data['enddate'])) {
			$data['enddate'] = date('Y-m-d');
		}
		$data['action'] = array_merge(array('prov'=>$prov,'kab'=>$kab,'priv'=>$private,'data'=>$this->api.'/dashboard/certification', 'startdate' => $data['startdate'], 'enddate' => $data['enddate']),$this->action);
		$data['title'] = $this->titlet.lang('Sertifikasi');
		$data['url_param'] = $prov.'/'.$private.'/'.$kab;
		$this->LoadView($data, 'certification');
	}

	public function garden($prov='',$private='',$kab='') {
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

	public function nutrition($prov='',$private='',$kab='') {
		$data = array('prov'=>$prov,'private'=>$private,'daer'=>$_SESSION['daerah']);
		$data['js'] = 'nutrition';
		$data['action'] = array_merge(array('prov'=>$prov,'kab'=>$kab,'priv'=>$private,'data'=>$this->api.'/dashboard/nutrition'),$this->action);
		$data['title'] = $this->titlet.lang('Nutrisi');
		$this->LoadView($data, 'nutrition');
	}

	public function survey($prov='',$private='',$kab='') {
		$data = array('prov'=>$prov,'private'=>$private,'daer'=>$_SESSION['daerah']);
		$data['js'] = 'survey';
		$data['action'] = array_merge(array('prov'=>$prov,'kab'=>$kab,'priv'=>$private,'data'=>$this->api.'/dashboard/survey',
			'now'=>date('Y')),$this->action);
		$data['title'] = $this->titlet.lang('Survey Overview');
		$this->LoadView($data, 'survey');
	}

	public function training($prov='',$private='',$kab='')
	{
		$data = array('prov'=>$prov,'private'=>$private,'daer'=>$_SESSION['daerah']);
		$data['js'] = 'training';
		$data['training'] = $this->input->get('training')!==false?$this->input->get('training'):all;
		$data['action'] = array_merge(array('prov'=>$prov,'kab'=>$kab,'priv'=>$private,'data'=>$this->api.'/dashboard/training',
			'now'=>date('Y'),'training'=>$data['training']),$this->action);
		$data['title'] = $this->titlet.lang('Farmer Training');

		$this->LoadView($data, 'training');
	}

	public function master_training($prov='',$private='',$kab='')
	{
		$this->load->model('mcommon');
		$data = array('prov'=>$prov,'private'=>$private,'daer'=>$_SESSION['daerah']);
		$data['js'] = 'master_training';
		$data['action'] = array_merge(array('prov'=>$prov,'kab'=>$kab,'priv'=>$private,'data'=>$this->api.'/dashboard/master_training',
			'now'=>date('Y'),'staff_type'=>$this->input->get('staff_type')),$this->action);
		$data['title'] = $this->titlet.lang('Master Training');
		$data['staff_type'] = $this->mcommon->listStaffType();

		$this->LoadView($data, 'master_training');
	}

	public function kader_training($prov='',$private='',$kab='')
	{
		$data = array('prov'=>$prov,'private'=>$private,'daer'=>$_SESSION['daerah']);
		$data['js'] = 'kader_training';
		$data['action'] = array_merge(array('prov'=>$prov,'kab'=>$kab,'priv'=>$private,'data'=>$this->api.'/dashboard/kader_training',
			'now'=>date('Y')),$this->action);
		$data['title'] = $this->titlet.lang('Kader Training');

		$this->LoadView($data, 'kader_training');
	}

	public function finance($prov='',$private='',$kab='')
	{
		$data = array('prov'=>$prov,'private'=>$private,'daer'=>$_SESSION['daerah']);
		$data['js'] = 'finance';
		$data['action'] = array_merge(array('prov'=>$prov,'kab'=>$kab,'priv'=>$private,'data'=>$this->api.'/dashboard/finance',
			'now'=>date('Y')),$this->action);
		$data['title'] = $this->titlet.lang('Finance');

		$this->LoadView($data, 'finance');
	}

	public function coop($CoopID=null)
	{
		if($CoopID==null)
		{
			$CoopID = getCoopID();
		}

		$data = array('CoopID'=>$CoopID);
		$data['js'] = 'cooperatives_dashboard';
		$data['action'] = array_merge(array('coop_id'=>$CoopID,
			'data'=>$this->api.'/dashboard/cooperatives',
			'now'=>date('Y')),$this->action);
		$data['title'] = $this->titlet.lang('Cooperatives');

		$this->LoadView($data, 'coop');
	}

	public function environment($prov='',$private='',$kab='')
	{
		$data = array('prov'=>$prov,'private'=>$private,'daer'=>$_SESSION['daerah']);
		$data['js'] = 'environment';
		$data['action'] = array_merge(array('prov'=>$prov,'kab'=>$kab,'priv'=>$private,'data'=>$this->api.'/dashboard/environment',
			'now'=>date('Y')),$this->action);
		$data['title'] = $this->titlet.lang('Enviroment');

		$this->LoadView($data, 'environment');
	}

	public function agriinput($prov='',$private='',$kab='')
	{
		$data = array('prov'=>$prov,'private'=>$private,'daer'=>$_SESSION['daerah']);
		$data['js'] = 'agriinput';
		$data['action'] = array_merge(array('prov'=>$prov,'kab'=>$kab,'priv'=>$private,'data'=>$this->api.'/dashboard/agriinput',
			'now'=>date('Y')),$this->action);
		$data['title'] = $this->titlet.lang('Agri Inputs');

		$this->LoadView($data, 'agriinput');
	}

	public function set_lang($lang,$mod) {
		$_SESSION['language'] = $lang;
		redirect(str_replace('-','/',$mod), 'location');
	}

	public function traceability($prov='',$private='',$kab='') {
		$_GET['awal'] = ($_GET['awal']==''?(date('Y')-1).date('-m-d'):$_GET['awal']);
		$_GET['akhir'] = ($_GET['akhir']==''?date('Y-m-d'):$_GET['akhir']);
		$data = array('prov'=>$prov,'private'=>$private,'daer'=>$_SESSION['daerah']);
		$data['js'] = 'home_traceability';
		$data['action'] = array_merge(array('prov'=>$prov,'kab'=>$kab,'priv'=>$private,'traceability_partner'=>$_GET['traceability_partner'],'awal'=>$_GET['awal'],
			'akhir'=>$_GET['akhir'],'data'=>$this->api.'/dashboard/traceability',
			'now'=>date('Y')),$this->action);
		$data['title'] = $this->titlet.lang('Traceability');
		$data['tgl'] = $_GET;
		$data['url_param'] = $prov.'/'.$private.'/'.$kab;
		$this->LoadView($data, 'home_traceability');
	}

    public function bank($prov='',$private='',$kab='') {
        $data = array('prov'=>$prov,'private'=>$private,'daer'=>$_SESSION['daerah'],'petani'=>$_GET['petani']);
        $data['js'] = 'bank';
        $data['action'] = array_merge(array('prov' => $prov,'kab' => $kab,'priv' => $private,'petani' => $_GET['petani'],'tahun' => $data['tahun'],
            'data' => $this->api.'/dashboard/bank'), $this->action);
        $data['title'] = $this->titlet.lang('Banks');
        $this->LoadView($data, 'bank');
    }

    public function cocoa_price($prov='',$private='',$kab='') {
        $data = array('prov'=>$prov,'private'=>$private,'daer'=>$_SESSION['daerah'],'petani'=>$_GET['petani']);
        $data['js'] = 'dashboard/cocoa_price';
        $data['action'] = array_merge(array('prov' => $prov,'kab' => $kab,'priv' => $private,
            'data' => $this->api.'/dashboard/cocoa_price'), $this->action
        );
        $data['title'] = $this->titlet.lang('Farm Gate Price');
        $this->LoadView($data, 'cocoa_price');
    }

    public function kpi($prov='',$private='',$kab='') {
        $data = array('prov'=>$prov,'private'=>$private,'daer'=>$_SESSION['daerah'],'petani'=>$_GET['petani']);
        $data['js'] = 'dashboard/kpi';
        $data['action'] = array_merge(array('prov' => $prov,'kab' => $kab,'priv' => $private,
            'data' => $this->api.'/dashboard/kpi'), $this->action
        );
        $data['title'] = $this->titlet.lang('KPI');
        $this->LoadView($data, 'dash_kpi');
    }

	public function curl_upload()
	{
	    $url = 'http://cocoatrace.dev/api/tools/upload_farmer_photo';
	    $file = '/home/abdullah/Documents/Screenshot_2015-10-08-11-39-36.jpg';
	    $ch = curl_init($url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_POST, true);
	    $post = array(
	        "userfile" => "@$file",
	        "FarmerID" => '1'
	    );
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	    $response = curl_exec($ch);
	    echo $response;

	    curl_close($ch);
	}


}

