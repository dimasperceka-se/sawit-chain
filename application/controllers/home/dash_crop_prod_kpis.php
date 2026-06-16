<?php
/**
 * @Author: nikolius
 * @Date:   2017-05-10 17:17:50
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Dash_crop_prod_kpis extends SS_Controller
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

    public function index($prov = '', $private = '', $kab = '')
    {
        $data          = array('prov' => $prov, 'private' => $private, 'daer' => $_SESSION['daerah'], 'petani' => $_GET['petani']);
        $data['js']    = 'home/dash_crop_prod_kpis';
        $data['tahun'] = $this->input->get('tahun');
        if (empty($data['tahun'])) {
            $data['tahun'] = date('Y');
        }
        $data['action'] = array_merge(
            array(
                'prov' => $prov,
                'kab' => $kab,
                'priv' => $private,
                'petani' => $_GET['petani'],
                'tahun' => $data['tahun'],
                'data'  => $this->api . '/dashboard/dash_crop_prod_kpis'),
                $this->action
        );
        $data['title'] = $this->titlet . lang('Crop Production KPIs');
        $this->LoadView($data, 'dashboard/dash_crop_prod_kpis');
    }

}
