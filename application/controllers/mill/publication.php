<?php
/**
 * @Author: nikolius
 * @Date:   2017-08-03 15:19:56
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Publication extends SS_Controller
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
            'year'     => ($_GET['year']==''?(date('Y')):$_GET['year']),
        );
        $this->titlet = lang('Publication') . ' > ';
    }

    public function index()
    {
        $_GET['year'] = ($_GET['year']==''?(date('Y')):$_GET['year']);
        $data['js']     = 'mill/publication';

        $data['action'] = array_merge(
            array(
                'SID'      => $_SESSION["SupplychainID"],
                'PID'      => $_SESSION["PartnerID"],
                'traceability_partner'=>$_GET['traceability_partner'],
                'awal'      => $_GET['awal'],
                'akhir'     => $_GET['akhir'],
                'year'      => $_GET['year'],
                'data'      => $this->api.'/dboard/dash_get_traceability_mill',
                'now'       => $_GET['year']
            ),
            $this->action
        );
        $data['title']      = $this->titlet . lang('Mill Publication');
        $this->LoadView($data, 'mill/publication');
    }
}

?>