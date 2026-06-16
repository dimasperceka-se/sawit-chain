<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Farmer extends SS_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index($id = '')
    {
        //pengecekan untuk form P1 khusus mars (begin)
        //cek apakah user private dan partnernya mars
        $sql="SELECT
                c.`ObjType`
                , c.`ObjID`
            FROM
                sys_user a
                INNER JOIN ktv_persons b ON a.`UserId` = b.`UserID`
                INNER JOIN ktv_staffs c ON b.`PersonID` = c.`PersonID`
            WHERE
                a.`UserId` = '{$_SESSION['userid']}'
            LIMIT 1";
        $query = $this->db->query($sql);
        $data = $query->row_array();

        if($data['ObjType'] == "private" && $data['ObjID'] == '9'){
            //user private dan mars
            $data['js']     = 'farmer-mars';
        }else{
            switch ($_SESSION['ProjID']) {
                case '1':
                    //Project SCPP
                    $data['js']     = 'farmer';
                break;
                default:
                    //cek proyek ini untuk Partner mana
                    $sql="SELECT
                            a.`PartnerID`
                        FROM
                            ktv_program_partner_project a
                        WHERE
                            a.`ProjID` = '{$_SESSION['ProjID']}'
                        LIMIT 1";
                    $query = $this->db->query($sql);
                    $data = $query->row_array();

                    switch ($data['PartnerID']) {
                        default:
                            //jika belum ada form P! farmer nya, kasih defautlnya sama dengan scpp
                            $data['js']     = 'farmer';
                        break;
                    }
                break;
            }
        }
        //pengecekan untuk form P1 khusus mars (begin)

        $api            = $this->config->item('api');
        $prov = !empty($this->input->get('prov'))?$this->input->get('prov'):'';
        $dist = !empty($this->input->get('dist'))?$this->input->get('dist'):'';
        $data['action'] = array(
            'crud'                             => $api . '/farmer/farmerl',
            'list_excel'                       => $api . '/farmer/farmerl_excel',
            'farmer_nursery'                   => $api . '/farmer/farmer_nursery',
            'farmer_filter_subdistrict'        => $api . '/farmer/farmer_filter_subdistrict',
            'farmer_filter_village'            => $api . '/farmer/farmer_filter_village',
            'crud_family'                      => $api . '/farmer/family',
            'photo'                            => $this->config->item('api_base_url') . 'images/Photo/',
            'harvest'                          => $api . '/farmer/harvest',
            'saving_pilot'                     => $api . '/farmer/saving_pilot',
            'farmer_profile'                   => $api . '/farmer/cetak_beneficiary_profiles/',
            'garden'                           => $api . '/farmer/garden',
            'sertifikasi'                      => $api . '/farmer/sertifikasi',
            'param'                            => $prov,
            'surveys'                          => $api . '/farmer/Surveys',
            'hsurvey'                          => $api . '/farmer/Survey_harvests',
            'spsurvey'                         => $api . '/farmer/Survey_saving_pilots',
            'spinfo'                           => $api . '/farmer/Survey_saving_pilot_info',
            'cetak'                            => $api . '/farmer/cetak/',
            'area'                             => $api . '/farmer/Area',
            'Desa'                             => $api . '/farmer/Desas',
            'Kecamatan'                        => $api . '/farmer/Kecamatans',
            'Kabupaten'                        => $api . '/farmer/Kabupatens',
            'Provinsi'                         => $api . '/farmer/Provinsis',
            'ProvinsiNama'                     => $api . '/farmer/ProvinsiNama',
            'RegionID'                         => $api . '/cpg/RegionIDs',
            'GroupID'                          => $api . '/farmer/GroupIDs',

            'psurvey'                          => $api . '/farmer/Survey_ppis',
            'ppsurvey'                         => $api . '/farmer/Survey_ppi_2012s',
            'ppi'                              => $api . '/farmer/ppi',
            'ppi_2012'                         => $api . '/farmer/ppi_2012',
            'nsurvey'                          => $api . '/farmer/Survey_nutritions',
            'nutrition'                        => $api . '/farmer/nutrition',
            //aff
            'asurvey'                          => $api . '/farmer/Survey_affs',
            'aff'                              => $api . '/farmer/aff',
            'auditactivity'                    => $api . '/farmer/farmerl_audit_log',
            'loadActivity'                     => $api . '/farmer/load_activity',
            'deleteActivity'                   => $api . '/farmer/delete_activity',
            'cetak_aff'                        => $api . '/farmer/cetak_aff/',
            'cetak_basic_aff'                  => $api . '/farmer/cetak_basic_aff/',
            'cetak_result_aff'                 => $api . '/farmer/cetak_result_aff/',

            'cetak_basic_farmer'               => $api . '/farmer/cetak_basic_farmer/',
            'cetak_result_farmer'              => $api . '/farmer/cetak_result_farmer/',
            'cetak_basic_nutrisi'              => $api . '/farmer/cetak_basic_nutrisi/',
            'cetak_result_nutrisi'             => $api . '/farmer/cetak_result_nutrisi/',
            'cetak_basic_ppi2012'              => $api . '/farmer/cetak_basic_ppi2012/',
            'cetak_result_ppi2012'             => $api . '/farmer/cetak_result_ppi2012/',

            'cetak_nutrisi'                    => $api . '/farmer/cetak_nutrisi/',
            'cetak_ppi2010'                    => $api . '/farmer/cetak_ppi2010/',
            'cetak_ppi2012'                    => $api . '/farmer/cetak_ppi2012/',
            'act_index'                        => !$this->system->CekAksi('index'),

            'cetak_sum_garden'                 => $api . '/farmer/cetak_sum_garden/',

            'cetak_basic_saving_pilot'         => $api . '/farmer/cetak_basic_saving_pilot/',
            'cetak_result_saving_pilot'        => $api . '/farmer/cetak_result_saving_pilot/',

            'act_add'               => $this->system->CekAksi('add'),
            'act_update'            => $this->system->CekAksi('update'),
            'act_delete'            => $this->system->CekAksi('delete'),
            'act_garden'            => $this->system->CekAksi('garden'),
            'act_detail'            => $this->system->CekAksi('detail'),
            'act_harvest'           => $this->system->CekAksi('harvest'),
            'act_ppi'               => $this->system->CekAksi('ppi'),
            'act_nutrition'         => $this->system->CekAksi('nutrition'),
            'act_ppi_2012'          => $this->system->CekAksi('ppi_2012'),
            'act_finance'           => $this->system->CekAksi('finance'),
            'act_environment'       => $this->system->CekAksi('environment'),
            'act_saving_pilot'      => $this->system->CekAksi('saving_pilot'),
            'act_adopt_obs'          => $this->system->CekAksi('adopt_obs'),
            'act_gsp'                => $this->system->CekAksi('farmer_gsp'),
            'act_profile'           => $this->system->CekAksi('profile'),
            'act_compost'           => $this->system->CekAksi('compost'),
            'act_nursery'           => $this->system->CekAksi('nursery'),
            'act_clonal_garden'     => $this->system->CekAksi('clonal_garden'),
            'act_summary'           => $this->system->CekAksi('summary'),
            'act_save'              => (!$this->system->CekAksi('update')) ? 'hide-icon' : '',

            //'CekGarden'=>$api.'/farmer/data_CekGarden',
            'CekSurvey'                        => $api . '/farmer/data_CekSurvey',
            'partner'                          => $api . '/farmer/data_partner',

            'act_summary'                      => !$this->system->CekAksi('summary'),
            'sum_garden'                       => $api . '/farmer/sum_garden',
            'sum_post'                         => $api . '/farmer/sum_post',
            'sum_nutrition'                    => $api . '/farmer/sum_nutrition',
            'sum_ppi'                          => $api . '/farmer/sum_ppi',
            'sum_aff'                          => $api . '/farmer/sum_aff',
            'sum_trainings'                    => $api . '/farmer/sum_trainings',

            'batch'                            => $api . '/cpg/batchs',
            'training_name'                    => $api . '/cpg/trainingNames',
            'key_farmer'                       => $api . '/cpg/key_farmers',
            'demo_plot'                        => $api . '/cpg/demo_plots',
            'fasilitator'                      => $api . '/cpg/fasilitator_alls',
            'penyuluh'                         => $api . '/cpg/penyuluhs',
            'family'                           => $api . '/cpg/familys',
            'family_training'                  => $api . '/cpg/family_trainings',
            'participant'                      => $api . '/cpg/participant',
            'holder'                           => $api . '/farmer/cert_holders',
            'holder2'                          => $api . '/farmer/certholder',
            'staff_cert'                       => $api . '/farmer/cert_staffs',
            'last_audit'                       => $api . '/farmer/lastAuditLog',
            'store_composts'                   => $api . '/cpg/composts',
            'store_compost_penjualans'         => $api . '/cpg/compost_penjualans',
            'compost'                          => $api . '/cpg/compost',
            'fam_relation'                     => $api . '/farmer/famrelation',
            'store_nurseys'                    => $api . '/cpg/nurseys',
            'store_nursey_penjualans'          => $api . '/cpg/nursey_penjualans',
            'nursey'                           => $api . '/cpg/nursey',
            'store_nursey_monitorings'         => $api . '/cpg/nursey_monitorings',
            'crud_other_land'                  => $api . '/farmer/other_land',
            'crud_garden_status'               => $api . '/farmer/garden_status',
            'cetak_learning_contract_template'          => $api . '/farmer/cetak_learning_contract_template/',
            'cetak_certification_contract_template'     => $api . '/farmer/cetak_certification_contract_template/',
            'crud_rotate_photo'                => $api . '/farmer/rotate_photo',
            'clone_ref'                        => $api . '/cpg/clone_ref',
            'hakakses_lat_short'               => $this->system->cekSettingPerUser('latShort'),
            'hakakses_long_short'              => $this->system->cekSettingPerUser('longShort'),
            'hakakses_lat_long'                => $this->system->cekSettingPerUser('latLong'),
            'hakakses_long_long'               => $this->system->cekSettingPerUser('longLong'),
            'hakakses_elevation'               => $this->system->cekSettingPerUser('elevation'),
            'hakakses_polygon'                 => $this->system->cekSettingPerUser('polygon'),
            'store_clonal_penjualans'          => $api.'/cpg/clonal_penjualans',
            'store_clonal_monitorings'         => $api.'/cpg/clonal_monitorings',
            'store_clonal_polygons'            => $api.'/cpg/clonal_polygons',
            'coop'                             => $api.'/cooperatives/coop',
            'clonal'                           => $api.'/farmer/clonal',
            'cpg_clonal'                       => $api.'/cpg/clonal',
            'bank'                             => $api.'/bank/banglist',
            'polygon'                          => $api.'/farmer/polygon',
            'survey'                           => $api.'/farmer/survey',
            'base_url' => base_url()
        );
        $data['style'] = "
         .invalid .x-grid-cell {
             background-color: #ffe2e2;
             color: #900;
         }

         .new .x-grid-cell {
             background-color: #F7FAB1;
             color: #8D9104;
         }
         .x-column-header-inner .x-column-header-text {
             white-space: normal;
         }
         .x-column-header-inner {
             line-height: normal;
             padding-top: 3px !important;
             padding-bottom: 3px !important;
             text-align: center;
         }
         legend {
            line-height: 1em;
         }
         ";
        $this->LoadView($data, 'common_content_region');
        /*      $q = $this->db->query("SELECT
    COLUMN_NAME
    FROM
    INFORMATION_SCHEMA.COLUMNS
    WHERE
    TABLE_SCHEMA = 'cocoatrace'
    AND TABLE_NAME = 'ktv_farmer_financial'");
    foreach ($q->result() as $r) {
    # code...
    echo "sthis->post('".$r->COLUMN_NAME."'),<br>";
    echo "'".$r->COLUMN_NAME."'=>sss".$r->COLUMN_NAME.",<br>";
    }*/
    }

    public function kolom()
    {
        $q = $this->db->query("SELECT
               COLUMN_NAME
            FROM
               INFORMATION_SCHEMA.COLUMNS
            WHERE
               TABLE_SCHEMA = 'cocoatrace'
            AND TABLE_NAME = 'ktv_farmer_financial'");
        foreach ($q->result() as $r) {
            # code...
            echo $r->COLUMN_NAME . ',';
        }
    }

}
