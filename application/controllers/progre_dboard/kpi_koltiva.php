<?php
/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Jul 20 2020
 *  File : kpi_koltiva.php
 *******************************************/
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Kpi_koltiva extends SS_Controller
{
    public function __construct() {
        parent::__construct(1);
        
        $this->api = $this->config->item('api');
        $url = (index_page())?(base_url().index_page()):rtrim(base_url(),'/');
        $this->action = array(
            'url'           => $url,
            'api'           => $this->api,
            'daer'          => $_SESSION['daerah'],
            'partner'       => $_SESSION['FlagAccess'] == 1 ? $_SESSION['PartnerID'] : '',
            'path'          => (index_page()?index_page().'/':'').'home/'.$this->router->fetch_class().'/index/',
            'partner_id'    => 1 //koltiva
        );
        $this->titlet = lang('Dashboard').' > ';
    }

    public function index($PartnerID = '', $year = '') {
        $data = array('PartnerID'=>1, 'year'=>$year);

        $data['js'] = 'progre_dboard/kpi_koltiva';
        
        $data['action'] = array_merge(
            array(
                // 'prov'      => $prov,
                // 'kab'       => $kab,
                // 'priv'      => $private,
                'data'      => $this->api.'/dboard/kpi_koltiva',
                'year'      => $year == '' ? date('Y') : $year,
                'PartnerID' => $PartnerID == '' ? 1 : $PartnerID
            ), $this->action
        );
        $data['title'] = $this->titlet.lang('Demografis');
        $this->LoadView($data, 'progre_dboard/kpi_koltiva');
    }
}
?>