<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Traceability_Sync extends SS_Controller {

    public function __construct() {
        parent::__construct(1);
        $this->load->model('mcommon','_common');
    }

    public function index() {

        $api = $this->config->item('api');
        $data['js'] = 'traceability_sync_report';
        $data['action'] = array(
          'api' => $api,
          'pedagang' => json_encode($this->_common->getComboPedagang(),JSON_HEX_APOS),
          'district' => json_encode($this->_common->getComboDistrict(),JSON_HEX_APOS),
          'subdistrict' => json_encode($this->_common->getComboSubDistrict(),JSON_HEX_APOS),
          'village' => json_encode($this->_common->getComboVillage(),JSON_HEX_APOS),
          'cpg' => json_encode($this->_common->getComboCpg(),JSON_HEX_APOS),
          'report_path' => $api . '/report/traceabilitysync'
        );
        
        //echo '<pre>';
        //var_dump($data);die;
        
        $this->LoadView($data);
    }

}
