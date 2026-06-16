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

class Supplychain_org_rel extends SS_Controller
{
    public function __construct()
    {
        parent::__construct(1);
    }

    public function index($id = '')
    {
        $data['js'] = 'traceability_new/Supplychain_org_rel';
        $api = $this->config->item('api');
        $data['action'] = array(
            'api_base_url' => $this->config->item('api_base_url'),
            'act_add'=> !$this->system->CekAksi('add'),
            'act_update'=> !$this->system->CekAksi('update'),
            'act_delete'=> !$this->system->CekAksi('delete'),
            'now' => date('Y-m-d H:i:s'),
            'date' => date('Y-m-d'),
            'time' => date('H:i'),
            'sys_date' => date('Ymd'),
             'sid' => $_SESSION['SupplychainID'],
             'pid' => $_SESSION['PartnerID']  
        );
        $this->LoadView($data);
    }
}
?>