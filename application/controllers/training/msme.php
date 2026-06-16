<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Msme extends SS_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index($id = '') {
        $data['js'] = 'training_msme';
        $api = $this->config->item('api');
        $prov = !empty($this->input->get('prov'))?$this->input->get('prov'):'';
        $dist = !empty($this->input->get('dist'))?$this->input->get('dist'):'';
        $data['action'] = array(
            'crud'                          => $api . '/training_msme/data',
            'param'                         => $prov,
            'label_provinsi'                => $api . '/training_kader/provinsi_label',
            'Kabupaten'                     => $api . '/farmer/Kabupatens',
            'store_cpg'                     => $api . '/cpg/batchs',
            'list_service_provider'         => $api . '/training_master/service_provider',
            'store_training'                => $api . '/cpg/trainingNames',
            'store_provinsi'                => $api . '/training_msme/Provinsis',
            'store_kabupaten'               => $api . '/training_msme/Kabupatens',
            'store_kecamatan'               => $api . '/training_msme/Kecamatans',
            // 'store_cpg'                  => $api . '/training_msme/CPGs',
            'store_fasilitator'             => $api . '/training_farmer/fasilitator',
            'store_staff'                   => $api . '/training_msme/staffs',
            'store_participant'             => $api . '/training_msme/participant',
            'participant_checklist_day'     => $api . '/training_msme/participant_checklist_day',
            'attendance_day'                => $api . '/training_msme/attendance_day',
            'check'                         => $api . '/training_msme/check',
            'district_data'                 => $api . '/partner/data_district',
            'cetak'                         => $api . '/training_msme/cetak/',
            'act_index'                     => !$this->system->CekAksi('index'),
            'act_add'       => $this->system->CekAksi('add'),
            'act_update'    => $this->system->CekAksi('update'),
            'act_save'      => $this->system->CekAksi('update'),
            'act_delete'    => $this->system->CekAksi('delete')
        );

        $this->LoadView($data, 'common_content_region');
    }

}

