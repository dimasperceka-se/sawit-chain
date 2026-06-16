<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Dashboard_mill extends SS_Controller
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
            'path'     => (index_page() ? index_page() . '/' : '') . 'progre_dboard/' . $this->router->fetch_class() . '/index/',
        );
        $this->titlet = lang('Dashboard') . ' > ';
    }

    public function index($mill = '', $millGroup = '', $startDate = '', $endDate = '')
    {
        $data          = array('mill' => $mill, 'millGroup' => $millGroup, 'starDate' => $startDate, 'endDate' => $endDate, 'daer' => $_SESSION['daerah'], 'petani' => $_GET['petani']);
        $data['js']    = 'progre_dboard/dashboard_mill';
        $data['action'] = array_merge(
            array(
                'mill' => $mill,
                'millGroup' => $millGroup,
                'stardate' => $startDate,
                'endDate' => $endDate,
                'priv' => $private,
                'petani' => $_GET['petani'],
                'tahun' => $data['tahun'],
                'data'  => $this->api . '/dboard/dash_get_mill'),
                $this->action
        );
        $data['title'] = $this->titlet . lang('Mill');
        $data['mentokDistrict'] = true;
        $this->LoadView($data, 'progre_dboard/dashboard_mill');
    }

}

?>