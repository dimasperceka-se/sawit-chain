<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Member extends SS_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index($id=null) {
        $data['js'] = 'member';
        $api = $this->config->item('api');
        $data['action'] = array(
            'api' => $api,
            'crud' => $api . '/member/coop_member',
            'tmp_cam' => $api . '/member/tmp_cam',
            'baseurl' => base_url(),
            'siteurl' => site_url(),
            'datafarmer' => $api . '/farmer/farmerl',
            'farmerkeluargas' => $api . '/farmer/farmerl_keluargas',
            'other_lands' => $api . '/farmer/other_land',
            'databank' => $api . '/farmer/databank',
            'api' => $this->config->item('api_base_url'),
            'farmer' => $api . '/member/coop_farmer',
            'photo' => $this->config->item('api_base_url') . 'images/Photo/Member/',
            'signature' => $this->config->item('api_base_url') . 'images/Photo/Member/',
            'withdrawal' => $this->config->item('api_base_url') . 'images/coop/',
            'membertype' => $api . '/member/combomembertype',
            'param' => $id,
            'district' => $api . '/member/combodistrict',
            'Desa' => $api . '/farmer/Desas',
            'Kecamatan' => $api . '/farmer/Kecamatans',
            'Kabupaten' => $api . '/farmer/Kabupatens',
            'Provinsi' => $api . '/farmer/Provinsis',
             'RegionID' => $api . '/cpg/RegionIDs',
            'GroupID' => $api . '/farmer/GroupIDs',
            'subdistrict' => $api . '/member/combosubdistrict',
            'village' => $api . '/member/combovillage',
            'identity' => $api . '/member/comboidentity',
            'status' => $api . '/member/combostatus',
            'saving' => $api . '/member/saving',
            'savingmember' => $api .'/member/savingmember',
            'save_member_saving' => $api . '/member/save_member_saving',
            'get_member_saving' => $api . '/member/coop_member_saving',
            'set_status_member_saving' => $api . '/member/coop_setstatus_saving',
            'loan' => $api . '/member/loan',
            'transactiontype' => $api . '/member/combo_transactiontype',
            'cashsource' => $api . '/transaction/combo_cashsource',
            'membersaving' => $api . '/transaction/combo_membersaving',
            'transaction' => $api . '/member/transaction',
            'savingtype' => $api . '/savingtype/coop_savingtypes',
            'act_add' => !$this->system->CekAksi('add'),
            'act_update' => !$this->system->CekAksi('update'),
            'act_delete' => !$this->system->CekAksi('delete')
        );
        $this->LoadView($data);
    }

    

}