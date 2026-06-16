<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dash_traceability_mill extends SS_Controller {

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
                'private'=>$private,
                'daer'=>$_SESSION['daerah']
            );
            $data['js'] = 'home/dash_traceability_mill';
            $data['action'] = array_merge(
                    array(
                    'mill'      => $this->input->get('mill'),
                    'priv'      => $private,
                    'traceability_partner'=>$_GET['traceability_partner'],
                    'awal'      => $_GET['awal'],
                    'akhir'     => $_GET['akhir'],
                    'data'      => $this->api.'/dboard/dash_get_traceability_mill',
                    'now'       => date('Y')
                ),
                $this->action
            );
            $data['title'] = $this->titlet.lang('Traceability');
            $data['tgl']['awal']    = $_GET['awal'];
            $data['tgl']['akhir']   = $_GET['akhir'];
            $data['url_param'] = $prov.'/'.$private.'/'.$kab;
            $data['viewAdditional'] = 'home_traceability_mill';
            $this->LoadView($data);
    }


}

