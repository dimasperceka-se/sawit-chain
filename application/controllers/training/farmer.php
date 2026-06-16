<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Farmer extends SS_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index($id = '') {
        $data['js'] = 'training_farmer_new';
        $api    = $this->config->item('api');
        $prov   = !empty($this->input->get('prov'))?$this->input->get('prov'):'';
        $dist   = !empty($this->input->get('dist'))?$this->input->get('dist'):'';
        $data['action'] = array(
            'api_base_url'              => $this->config->item('api_base_url'),
            'crud'                      => $api . '/training_farmer/data',
            'param'                     => $prov,
            'label_provinsi'            => $api . '/training_farmer/provinsi_label',
            'act_index'                 => !$this->system->CekAksi('index'),
            'store_cpg_batch'           => $api . '/cpg/batchs',
            'store_training'            => $api . '/cpg/trainingNames',
            'store_provinsi'            => $api . '/training_farmer/Provinsis',
            'store_kabupaten'           => $api . '/training_farmer/Kabupatens',
            'store_cpg'                 => $api . '/training_farmer/cpgs',
            // 'store_fasilitator'      => $api . '/cpg/fasilitators',
            'store_fasilitator'         => $api . '/training_farmer/fasilitator',
            'store_participant'         => $api . '/training_farmer/participant',
            'store_family'              => $api . '/training_farmer/families',
            'store_farmer'              => $api . '/training_farmer/farmers',
            'check'                     => $api . '/training_farmer/check',
            'cetak'                     => $api . '/training_farmer/cetak/',
            'CekSurvey'                 => $api . '/farmer/data_CekSurvey',
            'cetak_basic_farmer'        => $api . '/farmer/cetak_basic_farmer/',
            'cetak_basic_nutrisi'       => $api . '/farmer/cetak_basic_nutrisi/',
            'cetak_basic_ppi2012'       => $api . '/farmer/cetak_basic_ppi2012/',
            'cetak_basic_aff'           => $api . '/farmer/cetak_basic_aff/',
            'participant_detail'            => $api . '/training_farmer/participant_detail',
            'participant_checklist'         => $api . '/training_farmer/participant_checklist',
            'participant_checklist_day'     => $api . '/training_farmer/participant_checklist_day',
            'attendance'        => $api . '/training_farmer/attendance',
            'attendance_day'    => $api . '/training_farmer/attendance_day',
            'family'                    => $api . '/training_farmer/familys',
            'act_add'                   => $this->system->CekAksi('add'),
            'act_update'                => $this->system->CekAksi('update'),
            'act_save'                  => ( ! $this->system->CekAksi('update')),
            'act_delete'                => $this->system->CekAksi('delete')
            );

        $this->LoadView($data, 'common_content_region');
    }

}

