<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Farmer_sumary extends SS_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('mbank');
    }

    public function index() {
        $data['js'] = 'bank/farmer_sumary';
        $api = $this->config->item('api');

        $user = $this->mbank->userDetail();

        $data['action'] = array(
            'list'              => $api.'/bank/farmer_summary_list',
            'download'          => $api.'/bank/farmer_summary',
            'approval'          => $api.'/bank/farmer_summary_approval',
            'finalization'      => $api.'/bank/farmer_summary_finalization',
            'detail'            => $api.'/bank/farmer_summary_detail',
            'provinsi'          => $api.'/report/Provinsis',
            'kabupaten'         => $api.'/report/Kabupatens',
            'kecamatan'         => $api.'/report/Kecamatans',
            'cpg'               => $api.'/report/cpg',
            'bank'              => $api.'/bank/banglist',
            'branch'            => $api.'/bank/branchlist',
            'preview_url'       => $api.'/farmer/cetak_farmer_summary_loan',
            'user_province'         => $user['ProvinceID'],
            'user_district'         => $user['DistrictID'],
            'user_subdistrict'      => $user['SubDistrictID'],
            'is_bank'               => $_SESSION['role'] == 'Bank' ? 1 : 0,
            // 'act_index'     => !$this->system->CekAksi('index'),
            // 'act_add'       => !$this->system->CekAksi('add'),
            // 'act_update'    => !$this->system->CekAksi('update'),
            // 'act_delete'    => !$this->system->CekAksi('delete')
        );
        $this->LoadView($data);
    }

}

