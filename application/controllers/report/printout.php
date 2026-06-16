<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Printout extends SS_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['js']     = 'printout';
        $api            = $this->config->item('api');
        $data['action'] = array(
            'crud'                              => $api . '/report/printout',
            'Kabupaten'                         => $api . '/report/Kabupatens',
            'Warehouse'                         => $api . '/report/Warehouse',
            'Provinsi'                          => $api . '/report/Provinsis',
            'Survey'                            => $api . '/report/Surveys',
            'cooperatives'                      => $api . '/report/cooperatives',
            'partner'                           => $api . '/farmer/data_partner',
            'po'                                => $api . '/report/po',
            'Pwarehouse'                        => $api . '/report/purchase_warehouses',
            'Pcooperative'                      => $api . '/report/purchase_cooperatives',
            'PbuyingStation'                    => $api . '/report/purchase_buying_stations',
            'rekap'                             => $api . '/report/rekap',
            'Unit'                              => $api . '/report/unit',
            'Batch'                             => $api . '/report/batch',
            'bu'                                => $api . '/report/bu',
            'act_index'                         => !$this->system->CekAksi('index'),

            'cetak_beneficiary_profiles'        => $api . '/farmer/cetak_beneficiary_profiles/',
            'cetak_agent_profiles' => $api . '/grower/cetak_agent_profiles/',
            'cetak_mill_profiles' => $api . '/mill/cetak_mill_profiles/',
            'cetak_p1_p2'        => $api . '/farmer/cetak_p1_p2/',
            
            'act_printout_beneficiary_profiles'     => !$this->system->CekAksi('printout_beneficiary_profiles'),
            'act_printout_consent_notes'            => !$this->system->CekAksi('printout_consent_notes'),
            'act_printout_withdrawal_consent_notes' => !$this->system->CekAksi('printout_withdrawal_consent_notes'),
            'act_printout_agent_profile'            => !$this->system->CekAksi('printout_agent_profile'),
            'act_printout_mill_profile'             => !$this->system->CekAksi('printout_mill_profile'),
            'act_printout_p1_p2'                    => !$this->system->CekAksi('printout_p1_p2')
        );

        $this->LoadView($data);
    }

}
