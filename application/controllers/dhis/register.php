<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Register extends SS_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('mcommon','_common');
    }

    public function index() {

        $api = $this->config->item('api');
        $data['js'] = 'dhis_register';
        $data['action'] = array(
          'crud'            => $api.'/dhis/get-data',
          'request_sync'    => $api.'/dhis/sync-data',
          'request_sync_all'=> $api.'/dhis/sync-all',
          'Desa'            => $api.'/farmer/get_desa',
          'Kecamatan'       => $api.'/farmer/Kecamatans',
          'Kabupaten'       => $api.'/common/combo-kabupaten',
          'Provinsi'        => $api.'/farmer/Provinsis',
          'pedagang'        => json_encode($this->_common->getComboPedagang()),
          'report_path'     => $api . '/report/traceabilitysync'
        );
        $this->LoadView($data);
    }

}
