<?php
/**
 * @Author: nikolius
 * @Date:   2017-08-03 15:19:56
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Dashboard extends SS_Controller
{
    public function __construct()
    {
        parent::__construct(1);
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

    public function index()
    {
        $data['js']     = 'mill/dashboard';

        $data['action'] = array_merge(
            array(
                'SID'       => $_SESSION["SupplychainID"],
                'PID'       => $_SESSION["PartnerID"],
                'traceability_partner'=>$_GET['traceability_partner'],
                'awal'      => ($_GET['awal'] != '') ? $_GET['awal'] : date("Y"),
                'akhir'     => ($_GET['akhir'] != '') ? $_GET['akhir'] : date("Y"),
                'data'      => $this->api.'/dboard/dash_get_traceability_mill',
                'now'       => date('Y')
            ),
            $this->action
        );
        $data['title']          = $this->titlet . lang('Dashboard');
        $data['tgl']['awal']    = ($_GET['awal'] != '') ? $_GET['awal'] : date("Y");
        $data['tgl']['akhir']   = ($_GET['akhir'] != '') ? $_GET['akhir'] : date("Y");
        $this->LoadView($data, 'mill/dashboard');
    }
}

?>