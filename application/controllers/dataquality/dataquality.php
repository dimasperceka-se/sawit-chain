<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Dataquality extends SS_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['js'] = 'dataquality';
        $api = $this->config->item('api');
        $data['action'] = array(
            'crud' => $api . '/dataquality/dataquality',
            'delete' => $api . '/dataquality/dataqualities',
            'calculate' => $api . '/dataquality/calculate',
            'program' => $api . '/dataquality/program',
            'programsection' => $api . '/dataquality/programsection',
            'cetak_activity_detail' => $api . '/dataquality/dataquality_excel',
            'act_add' => !$this->system->CekAksi('add') ? 'hide-icon' : '',
            'run_query' => !$this->system->CekAksi('run_query')
        );
        $this->LoadView($data);
    }

}
