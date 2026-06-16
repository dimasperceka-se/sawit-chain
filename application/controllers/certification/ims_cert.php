<?php
/**
 * @Author: nikolius
 * @Date:   2017-10-26 17:05:45
 * @Last Modified by:   Nikolius Lau
 * @Last Modified time: 2018-08-21 16:53:29
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ims_cert extends SS_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['js'] = 'certification/ims_grid';
        $data['js_additional'] = 'certification/ims_event,certification/ims_detail,certification/ims_farmer_detail,certification/ims_staff,certification/ims_buying_unit';
        $prov   = !empty($this->input->get('prov'))?$this->input->get('prov'):'';
        $api = $this->config->item('api');
        $data['action'] = array(
            'crud'         => $api.'/training_farmer/data',
            'print'        => $api.'/ims/print',
            'api_base_url' => $this->config->item('api_base_url'),
            'file'         => $this->config->item('api_base_url') . 'files/ims_master/',
            'file_detail'  => $this->config->item('api_base_url') . 'files/ims/',
            'url_awss3'     => $this->config->item('CTCDN'),
            'param'                     => $prov,

            'act_index'    => $this->system->CekAksi('index'),
            'act_add'      => !$this->system->CekAksi('add'),
            'act_update'   => !$this->system->CekAksi('update'),
            'act_delete'   => !$this->system->CekAksi('delete'),
            'act_import'   => !$this->system->CekAksi('import'),
            'act_export'   => !$this->system->CekAksi('export'),
            'act_acq'   => !$this->system->CekAksi('ims_acq'),
            'store_cpg_batch'           => $api . '/cpg/batchs',
            'store_training'            => $api . '/cpg/trainingNames',
            'store_provinsi'            => $api . '/training_farmer/Provinsis',
            'store_kabupaten'           => $api . '/training_farmer/Kabupatens',
            'store_cpg'                 => $api . '/training_farmer/cpgs',
            // 'store_fasilitator'      => $api . '/cpg/fasilitators',
            'store_fasilitator'         => $api . '/training_farmer/fasilitator_scpp',
            'store_fasilitator_mitra'   => $api . '/training_farmer/fasilitator_mitra',
            'store_participant'         => $api . '/training_farmer/participant',
            'store_family'              => $api . '/training_farmer/families',
            'store_farmer'              => $api . '/training_farmer/farmers',
            'act_asset_receipt'   => !$this->system->CekAksi('ims_asset_receipt'),
            'act_approval'   => !$this->system->CekAksi('approval'),
            'act_gen_soc_sel'   => !$this->system->CekAksi('gen_soc_sel'),
            'act_gen_gap_coc'   => !$this->system->CekAksi('gen_gap_coc'),
            'cetak'                     => $api . '/training_farmer/cetak/',
            'act_signing_lock_soc_sel'   => !$this->system->CekAksi('signing_lock_soc_sel'),
            'act_signing_lock_gap_coc'   => !$this->system->CekAksi('signing_lock_gap_coc'),
            'act_process_to_candidate_selection'   => !$this->system->CekAksi('process_to_candidate_selection'),
            'act_process_to_candidate_training'   => !$this->system->CekAksi('process_to_candidate_training'),
            'participant_detail'            => $api . '/training_farmer/participant_detail',
            'participant_checklist'         => $api . '/training_farmer/participant_checklist',
            'participant_checklist_day'     => $api . '/training_farmer/participant_checklist_day',
            'attendance'        => $api . '/training_farmer/attendance',
            'attendance_day'    => $api . '/training_farmer/attendance_day',
            'family'                    => $api . '/cpg/familys',
            'act_training'   => !$this->system->CekAksi('ims_training'),
            'act_ims_afl_verify_cl'   => !$this->system->CekAksi('ims_afl_verify_cl'),
            'act_ims_afl_verify_imsmanager'   => !$this->system->CekAksi('ims_afl_verify_imsmanager'),
            'act_ims_finalization_period'   => !$this->system->CekAksi('ims_finalization_period'),
            'act_ics_reinspect_status'   => !$this->system->CekAksi('ics_reinspect_status'),
            'act_ics_reinspect_farmer'   => !$this->system->CekAksi('ics_reinspect_farmer'),
            'act_ics_reinspect_regenerate_ics'   => !$this->system->CekAksi('ics_reinspect_regenerate_ics'),
            'act_ims_regen_audit_summary'   => !$this->system->CekAksi('ims_regen_audit_summary'),
            'act_training_days_mapping' => !$this->system->CekAksi('training_days_mapping')
        );

        $this->LoadView($data);
    }

}