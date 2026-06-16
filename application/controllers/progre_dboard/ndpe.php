<?php

/**
 * @Author: nikolius
 * @Date:   2017-09-08 16:57:54
 * @Last Modified by:   nikolius
 * @Last Modified time: 2017-09-15 14:49:41
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Ndpe extends SS_Controller
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

    public function index($prov = '', $kab = '')
    {
        $data          = array('prov' => $prov, 'private' => $private, 'daer' => $_SESSION['daerah'], 'petani' => $_GET['petani']);
        $data['js']    = 'progre_dboard/ndpe';
        $data['action'] = array_merge(
            array(
                'prov' => $prov,
                'kab' => $kab,
                'priv' => $private,
                'petani' => $_GET['petani'],
                'tahun' => $data['tahun'],
                'data'  => $this->api . '/dboard/dash_get_pro_ndpe'),
                $this->action
        );
        $data['title'] = $this->titlet . lang('NDPE (No Deforestation, No Peat, No Exploitation Policy Compliance)');
        $data['mentokDistrict'] = true;
        $this->LoadView($data, 'progre_dboard/ndpe');
    }

}

?>