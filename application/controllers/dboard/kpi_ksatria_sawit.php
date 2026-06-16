<?php
/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Jul 20 2020
 *  File : kpi_koltiva.php
 *******************************************/
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Kpi_ksatria_sawit extends SS_Controller
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

    public function index($wave= '', $chdistrict='', $lockdate='') {
        $data = array('wave' => $wave, 'chdistrict' => $chdistrict, 'lockdate' => $lockdate);

        $data['js'] = 'dboard/kpi_ksatria_sawit';

        $wave = $this->input->get('wave');
        if (!$wave) {
            $wave = 14;
        }
        $chdistrict = $this->input->get('chdistrict');
        $lockdate = $this->input->get('lockdate');
        
        $data['action'] = array_merge(
            array(
                'data'      => $this->api.'/dboard/kpi_ksatria_sawit',
                'wave' => $wave, 
                'chdistrict' => $chdistrict,
                'lockdate'=>$lockdate,
            ), $this->action
        );
        $data['title'] = $this->titlet.lang('KPI Ksatria Sawit');
        $this->LoadView($data, 'dboard/kpi_ksatria_sawit');
    }
}
?>