<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cpg extends SS_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index($id = '', $add = '') {
        $data['js'] = 'cpg';
        $api = $this->config->item('api');
        $prov = $this->input->get('prov');
        $dist = $this->input->get('dist');
        $data['action']                 = array(
            'api_base_url' => $this->config->item('api_base_url'),
            'crud'                      => $api . '/cpg/cpgc',
            'list_excel'                => $api . '/cpg/cpgc_excel',
            'batch_training_search'     => $api . '/cpg/batch_training_combo',
            'RegionID'                  => $api . '/cpg/RegionIDs',
            'training'                  => $api . '/cpg/training',
            'family_training'           => $api . '/cpg/family_trainings',
            'batch'                     => $api . '/cpg/batchs',
            'cetak'                     => $api . '/cpg/cetak/',
            'param'                     => $prov,
            'Desa'                      => $api . '/farmer/Desas',
            'Kecamatan'                 => $api . '/farmer/Kecamatans',
            'Kabupaten'                 => $api . '/cpg/kabupatens',
            'Provinsi'                  => $api . '/cpg/provinsis',
            'area'                      => $api . '/farmer/Area',
            'family'                    => $api . '/cpg/familys',
            'training_name'                 => $api . '/cpg/trainingNames',
            'participant'                   => $api . '/cpg/participant',
            'participant_detail'            => $api . '/cpg/participant_detail',
            'participant_checklist'         => $api . '/cpg/participant_checklist',
            'participant_checklist_day'         => $api . '/cpg/participant_checklist_day',
            'attendance'                    => $api . '/cpg/attendance',
            'attendance_day'                    => $api . '/cpg/attendance_day',
            'check'                         => $api . '/cpg/check',
            'certificates'                  => $api . '/farmer/certificates/',
            'certificate'                   => $api . '/farmer/certificate/',
            'key_farmer'                    => $api . '/cpg/key_farmers',
            'demo_plot'                     => $api . '/cpg/demo_plots',
            'demoplot'                      => $api . '/cpg/demoplot',
            'batch_training'                => $api . '/cpg/batch_training',
            'demoplot_owner'                => $api . '/cpg/demoplot_owner',
            'garden_number'                 => $api . '/cpg/garden_number',
            'fasilitator'                   => $api . '/cpg/fasilitator_alls',
            'penyuluh'                      => $api . '/cpg/penyuluhs',
            'store_staff_access'            => $api . '/cpg/staff_access',
            'store_staff'                   => $api . '/cpg/staff',
            'CekSurvey'                     => $api . '/farmer/data_CekSurvey',
            'DayNumber'                     => $api . '/cpg/data_DayNumber',
            'cetak_basic_farmer'            => $api . '/farmer/cetak_basic_farmer/',
            'cetak_basic_nutrisi'           => $api . '/farmer/cetak_basic_nutrisi/',
            'cetak_basic_ppi2012'           => $api . '/farmer/cetak_basic_ppi2012/',
            'cetak_basic_aff'               => $api . '/farmer/cetak_basic_aff/',
            'cetak_learning_contract'       => $api . '/farmer/cetak_learning_contract/',
            'store_composts'                => $api . '/cpg/composts',
            'store_compost_penjualans'      => $api . '/cpg/compost_penjualans',
            'compost'                       => $api . '/cpg/compost',
            'clone_ref'                     => $api . '/cpg/clone_ref',
            'store_nurseys'                 => $api . '/cpg/nurseys',
            'store_nursey_penjualans'       => $api . '/cpg/nursey_penjualans',
            'store_nursey_monitorings'      => $api . '/cpg/nursey_monitorings',
            'nursey'                        => $api . '/cpg/nursey',
            'staff'                         => $api . '/cpg/staff_cpg',
            'member'                        => $api . '/cpg/membercpg',
            'add'                           => $add,
            'act_index'             => $this->system->CekAksi('index'),
            'act_add'               => $this->system->CekAksi('add'),
            'act_update'            => $this->system->CekAksi('update'),
            'act_access'            => $this->system->CekAksi('access') ? 'hide-icon' : 'hide-icon',
            'act_save'              => $this->system->CekAksi('update') ? 'hide-icon' : '',
            'act_delete'            => $this->system->CekAksi('delete'),
            'act_training'          => $this->system->CekAksi('training'),
            'act_compost'           => $this->system->CekAksi('compost'),
            'act_nursery'           => $this->system->CekAksi('nursery'),
            'act_demoplot'          => $this->system->CekAksi('demoplot'),
            'act_cpg_assign_partner' => $this->system->CekAksi('cpg_assign_partner'),
            'hakakses_lat_short'    => $this->system->cekSettingPerUser('latShort'),
            'hakakses_long_short'   => $this->system->cekSettingPerUser('longShort'),
            'hakakses_lat_long'     => $this->system->cekSettingPerUser('latLong'),
            'hakakses_long_long'    => $this->system->cekSettingPerUser('longLong'),
            'hakakses_polygon'    => $this->system->cekSettingPerUser('polygon')
        );
        $data['style'] = "
         .x-toolbar-garis {
            border-bottom: 3px solid #799143 !important;
         }";
        $this->LoadView($data, 'common_content_region');
    }

}
