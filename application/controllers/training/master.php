<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Master extends SS_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index($id = '') {
        $data['js'] = 'master_training_new';
        $api = $this->config->item('api');
        $prov = !empty($this->input->get('prov'))?$this->input->get('prov'):'';
        $dist = !empty($this->input->get('dist'))?$this->input->get('dist'):'';
        $data['action'] = array(
            'crud'                      => $api . '/training_master/data',
            'param'                     => $prov,
            'label_provinsi'            => $api . '/training_kader/provinsi_label',
            'act_index'                 => !$this->system->CekAksi('index'),
            'Kabupaten'                 => $api . '/farmer/Kabupatens',
            'store_cpg'                 => $api . '/cpg/batchs',
            'list_service_provider'     => $api . '/training_master/service_provider',
            'store_training'            => $api . '/cpg/trainingNames',
            'store_provinsi'            => $api . '/training_farmer/Provinsis',
            'store_kabupaten'           => $api . '/training_farmer/Kabupatens',
            'store_fasilitator'         => $api . '/training_farmer/fasilitator',
            'store_staff'               => $api . '/training_master/staffs',
            'store_participant'         => $api . '/training_master/participant',
            'check'                     => $api . '/training_master/check',
            'district_data'             => $api . '/partner/data_district',
            'cetak'                     => $api . '/training_master/cetak/',
            'act_add'                   => $this->system->CekAksi('add'),
            'act_update'                => $this->system->CekAksi('update'),
            'act_save'                  => $this->system->CekAksi('update'),
            'act_delete'                => $this->system->CekAksi('delete')
        );

        $this->LoadView($data, 'common_content_region');
    }

}

