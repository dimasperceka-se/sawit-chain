<?php

/**
 * @Author: gitandi
 * @Date:   2019-07-02 11:45:02
 * @Last Modified by:   gitandi
 * @Last Modified time: 2019-07-02 11:45:02
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Dash_supplychain_mill_traceability extends SS_Controller
{

    public function __construct()
    {
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

    public function index($prov = '', $kab = '')
    {
        $data          = array('prov' => $prov, 'private' => $private, 'daer' => $_SESSION['daerah'], 'petani' => $_GET['petani']);
        $data['js']    = 'dashboard/dash_supplychain_mill_traceability';
        $millgroup = $this->input->get('millgroup');
        $mill = $this->input->get('mill');
        $data['action'] = array_merge(
            array(
                'prov' => $prov,
                'kab' => $kab,
                'priv' => $private,
                'millgroup' => $millgroup,
                'mill' => $mill,
                'data'  => $this->api . '/dboard/dash_supplychain_mill_traceability'),
                $this->action
        );
        $data['action']['awal']         = ($_GET['awal']==''?(date('Y')).'-01-01':$_GET['awal']);
        $data['action']['akhir']        = ($_GET['akhir']==''?date('Y-m-d'):$_GET['akhir']);
        // $data['action']['wh']           = $this->input->get('wh');
        // $data['action']['bs']           = $this->input->get('bs');
        // $data['action']['cert']         = $this->input->get('cert');
        $data['action']['partnerid']    = 50;
        $data['action']['current_url']  = current_url();

        $data['title'] = $this->titlet . lang('Supply Chain Mill Traceability');
        $this->LoadView($data, 'dashboard/dash_supplychain_mill_traceability');
    }

}
?>