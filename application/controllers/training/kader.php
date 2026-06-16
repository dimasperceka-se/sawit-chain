<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Kader extends SS_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index($id = '') {
        $data['js'] = 'kader';
        $api = $this->config->item('api');
        $prov = !empty($this->input->get('prov'))?$this->input->get('prov'):'';
        $dist = !empty($this->input->get('dist'))?$this->input->get('dist'):'';
        $data['action'] = array(
            'crud'                      => $api . '/training_kader/data',
            'param'                     => $prov,
            'label_provinsi'            => $api . '/training_kader/provinsi_label',
            'act_index'                 => !$this->system->CekAksi('index'),
            'store_training'            => $api . '/cpg/trainingNames',
            'store_provinsi'            => $api . '/training_farmer/Provinsis',
            'store_kabupaten'           => $api . '/training_farmer/Kabupatens',
            // 'store_fasilitator'      => $api . '/cpg/fasilitators',
            'store_fasilitator'         => $api . '/training_farmer/fasilitator',
            // 'store_fasilitator_mitra'   => $api . '/training_kader/fasilitator_mitra',
            'store_participant'         => $api . '/training_kader/participant',
            'store_family'              => $api . '/training_kader/families',
            'store_farmer'              => $api . '/training_kader/farmers',
            'check'                     => $api . '/training_kader/check',
            'cetak'                     => $api . '/training_kader/cetak/',
            'CekSurvey'                 => $api . '/farmer/data_CekSurvey',
            'cetak_basic_farmer'        => $api . '/farmer/cetak_basic_farmer/',
            'cetak_basic_nutrisi'       => $api . '/farmer/cetak_basic_nutrisi/',
            'cetak_basic_ppi2012'       => $api . '/farmer/cetak_basic_ppi2012/',
            'cetak_basic_aff'           => $api . '/farmer/cetak_basic_aff/',
            'act_add'                   => $this->system->CekAksi('add'),
            'act_update'                => $this->system->CekAksi('update'),
            'act_save'                  => ( ! $this->system->CekAksi('update')),
            'act_delete'                => $this->system->CekAksi('delete')
            );

        $this->LoadView($data, 'common_content_region');
    }

}

