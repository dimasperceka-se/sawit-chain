<?php

/**
 * @Author: mawwatudi
 * @Date:   2018-01-03 11:18:00
 * @Last Modified by:   
 * @Last Modified time:
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Reception extends SS_Controller
{
    public function __construct()  
    {
        parent::__construct(1);
    }

    public function index($id = '')
    {
        $role_tc = $this->system->getAccess();
        $data['js'] = 'traceability/reception';
        $api = $this->config->item('api');
        $data['action'] = array(
            'api_base_url'  => $this->config->item('api_base_url'),
            'act_add'       => !$this->system->CekAksi('add'),
            'act_update'    => !$this->system->CekAksi('update'),
            'act_delete'    => !$this->system->CekAksi('delete'),
            'act_view'      => !$this->system->CekAksi('view'),
            'daerah_access' => $_SESSION['daerah_access'],
            'now'           => date('Y-m-d H:i:s'),
            'date'          => date('Y-m-d'),
            'time'          => date('H:i'),
            'sys_date'      => date('Ymd'),
            'isFarmer'      => $role_tc['IsFarmer'],
            'IsNonFarmer'   => $role_tc['IsNonFarmer'],
            'IsCompany'     => $role_tc['IsCompany'],
            'IsBatch'       => $role_tc['IsBatch'],
            'sid'           => $_SESSION['SupplychainID'],
            'pid'           => $_SESSION['PartnerID']  
        );
        $this->LoadView($data);
    }
}
?>