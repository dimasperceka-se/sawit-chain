<?php

/**
 * @Author: nikolius
 * @Date:   2017-09-08 10:47:48
 * @Last Modified by:   nikolius
 * @Last Modified time: 2017-09-15 14:49:35
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Kpi extends SS_Controller
{

    public function __construct()
    {
        parent::__construct(1);
        $this->lang->load('dashboard');
        $this->lang->load('home');
        $this->api    = $this->config->item('api');
        $url          = (index_page()) ? (base_url() . index_page()) : rtrim(base_url(), '/');

        $this->action = array(
            'url'            => $url,
            'api'            => $this->api,
            'district'       => $this->api . '/dashboard/district',
            'daer'           => $_SESSION['daerah'],
            'partner'        => $_SESSION['FlagAccess'] == 1 ? $_SESSION['PartnerID'] : '',
            'label_prov'     => 'Province',
            'label_district' => 'District',
            'partner_id'     => (int) $_SESSION['PartnerID'],
            'path'           => (index_page() ? index_page() . '/' : '') . 'progre_dboard/' . $this->router->fetch_class() . '/index/',
        );

        $this->titlet = lang('Dashboard') . ' > ';
    }

    public function index($country='',$prov = '', $kab = '',$year = '')
    {
        $data          = array('country'=>$country, 'prov' => $prov, 'private' => $private, 'daer' => $_SESSION['daerah'], 'petani' => $_GET['petani']);
        $data['js']    = 'progre_dboard/kpi';
        $data['action'] = array_merge(
            array(
                'country' => $country,
                'prov' => $prov,
                'kab' => $kab,
                'priv' => $private,
                'petani' => $_GET['petani'],
                'farm_type' => $_GET['farm_type'] ? $_GET['farm_type'] : 1,
                'tahun' => empty($data['tahun']) == true ? date('Y') : $data['tahun'],
                'year' => $year == '' ? date('Y') : $year,
                'data'  => $this->api . '/dboard/dash_get_kpi'),
                $this->action
        );
        $data['title'] = $this->titlet . ($_SESSION["PartnerID"] == "194" OR $_SESSION["PartnerID"] == "14") ? lang('Wild Asia Malaysia KPI') : lang('Program KPI');

        $data['mentokDistrict'] = true;

        $this->LoadView($data, 'progre_dboard/kpi');
    }

}

?>