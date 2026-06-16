<?php
/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Thu Aug 08 2019
 *  File : bluenumber_sdg.php
 *******************************************/
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Bluenumber_sdg extends SS_Controller
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
        $data['js']    = 'progre_dboard/bluenumber_sdg';
        $data['action'] = array_merge(
            array(
                'prov' => $prov,
                'kab' => $kab,
                'priv' => $private,
                'petani' => $_GET['petani'],
                'tahun' => $data['tahun'],
                'data'  => $this->api . '/dboard/dash_get_bluenumber_sdg'),
                $this->action
        );
        $data['title'] = $this->titlet . lang('BlueNumber SDG');
        $data['mentokDistrict'] = true;
        $this->LoadView($data, 'progre_dboard/bluenumber_sdg');
    }

}