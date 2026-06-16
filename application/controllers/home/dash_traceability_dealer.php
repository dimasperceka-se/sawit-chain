<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dash_traceability_dealer extends SS_Controller {

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
            $data['js'] = 'home/dash_traceability_dealer';
            $data['action'] = array_merge(
                    array(
                    'mill'=>$this->input->get('mill'),
                    'do'=>$this->input->get('do'),
                    'agent'=>$this->input->get('agent'),
                    'prov'=>$prov,
                    'kab'=>$kab,
                    'kec'=>$kec,
                    'desa'=>$desa,
                    'priv'=>$private,
                    'traceability_partner'=>$_GET['traceability_partner'],
                    'awal'=>$_GET['awal'],
                    'akhir'=>$_GET['akhir'],
                    'data'=>$this->api.'/dboard/dash_get_traceability_dealer',
                    'now'=>date('Y')
                ),
                $this->action
            );
            $data['action']['do'] = $this->input->get('do');
            $data['title'] = $this->titlet.lang('Traceability');
            $data['tgl'] = $_GET;
            $data['url_param'] = $prov.'/'.$private.'/'.$kab;
            $data['viewAdditional'] = 'home_traceability_dealer';

            $this->LoadView($data);
    }


}

