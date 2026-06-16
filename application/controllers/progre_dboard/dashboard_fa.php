<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard_fa extends SS_Controller {

    public function __construct() {
        parent::__construct(1);
        $this->lang->load('dashboard');
        $this->lang->load('home');
        $this->api    = $this->config->item('api');
        $url          = (index_page()) ? (base_url() . index_page()) : rtrim(base_url(), '/');
        $this->action = array(
            'url'      => $url,
            'api'      => $this->api,
            'district' => $this->api . '/dashboard/district',
            'daer'     => $_SESSION['daerah'],
            'partner'  => $_SESSION['FlagAccess'] == 1 ? $_SESSION['PartnerID'] : '',
            'path'     => (index_page() ? index_page() . '/' : '') . 'home/' . $this->router->fetch_class() . '/index/',
        );
        $this->titlet = lang('Dashboard') . ' > ';
    }

    public function index($prov='',$kab='',$kec='',$desa='',$private='') {
            $_GET['awal'] = ($_GET['awal']==''?(date('Y'))."-01-01":$_GET['awal']);
            $_GET['akhir'] = ($_GET['akhir']==''?date('Y-m-d'):$_GET['akhir']);
            $data = array(
                'prov'=>$prov,
                'kab'=>$kab,
                'kec'=>$kec,
                'desa'=>$desa,
                'private'=>$private,
                'daer'=>$_SESSION['daerah']
            );
            $data['js']    = 'progre_dboard/dashboard_fa';
            $data['action'] = array_merge(
                    array(
                    'mill'=>$this->input->get('mill'),
                    'type'=>$this->input->get('type'),
                    'priv'=>$private,
                    'traceability_partner'=>$_GET['traceability_partner'],
                    'awal'=>$_GET['awal'],
                    'akhir'=>$_GET['akhir'],
                    'data'=>$this->api.'/dboard/dash_get_fa',
                    'now'=>date('Y')
                ),
                $this->action
            );
            $data['title'] = $this->titlet.lang('Dashboard');
            $data['tgl'] = $_GET;
            $data['url_param'] = $prov.'/'.$private.'/'.$kab;
            $this->LoadView($data, 'progre_dboard/dashboard_fa');
    }


}

