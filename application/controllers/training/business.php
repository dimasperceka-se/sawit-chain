<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Business extends SS_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index($id = '') {
        $data['js'] = 'training_business';
        $api = $this->config->item('api');
        $prov = !empty($this->input->get('prov'))?$this->input->get('prov'):'';
        $dist = !empty($this->input->get('dist'))?$this->input->get('dist'):'';
        $data['action'] = array(
            'crud'                      => $api . '/training_business/data',
            'param'                     => $prov,
            'label_provinsi'            => $api . '/training_kader/provinsi_label',
            'Kabupaten'                 => $api . '/farmer/Kabupatens',
            'store_cpg'                 => $api . '/cpg/batchs',
            'list_service_provider'     => $api . '/basic/service_provider_list',
            'store_training'            => $api . '/cpg/trainingNames',
            'store_provinsi'            => $api . '/training_business/Provinsis',
            'store_kabupaten'           => $api . '/training_business/Kabupatens',
            'store_kecamatan'           => $api . '/training_business/Kecamatans',
            // 'store_cpg'                 => $api . '/training_business/CPGs',
            'store_fasilitator'         => $api . '/training_business/fasilitator_scpp',
            'store_fasilitator_mitra'   => $api . '/training_business/fasilitator_mitra',
            'store_staff'               => $api . '/training_business/staffs',
            'store_participant'         => $api . '/training_business/participant',
            'participant_checklist_day'     => $api . '/training_business/participant_checklist_day',
            'attendance_day'                => $api . '/training_business/attendance_day',
            'check'                     => $api . '/training_business/check',
            'district_data'             => $api . '/partner/data_district',
            'cetak'                     => $api . '/training_business/cetak/',
            'act_index'                 => !$this->system->CekAksi('index'),
            'act_add'       => $this->system->CekAksi('add'),
            'act_update'    => $this->system->CekAksi('update'),
            'act_save'      => $this->system->CekAksi('update'),
            'act_delete'    => $this->system->CekAksi('delete')
        );

        $this->LoadView($data, 'common_content_region');
    }

}

