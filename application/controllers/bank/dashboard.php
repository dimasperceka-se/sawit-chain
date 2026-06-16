<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends SS_Controller {

    public function __construct() {
        parent::__construct();
        $this->api = $this->config->item('api');
        $url = (index_page())?(base_url().index_page()):rtrim(base_url(),'/');
        $this->action = array(
            'url'           => $url,
            'api'           => $this->api,
            'district'      => $this->api.'/dashboard/district',
            'daer'          => $_SESSION['daerah'],
            'partner'       => $_SESSION['FlagAccess'] == 1 ? $_SESSION['PartnerID'] : '',
            'path'          => 'home/home/'.$this->router->fetch_method().'/'
        );
    }

    public function index($prov='',$private='',$kab='') {
        $data = array('prov'=>$prov,'private'=>$private,'daer'=>$_SESSION['daerah'],'petani'=>$_GET['petani']);
        $data['js'] = 'demographic';
        $data['tahun'] = $this->input->get('tahun');
        if (empty($data['tahun'])) {
            $data['tahun'] = date('Y');
        }
        $data['action'] = array_merge(array('prov'=>$prov,'kab'=>$kab,'priv'=>$private,'petani'=>$_GET['petani'],'tahun'=>$data['tahun'],
            'data'=>$this->api.'/dashboard/demographic'),$this->action);
        $data['title'] = $this->titlet.lang('Demographics');
        $this->LoadView($data, 'demographic');
    }

}

