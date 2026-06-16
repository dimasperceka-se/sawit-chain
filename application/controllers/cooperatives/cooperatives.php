<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class cooperatives extends SS_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index($id = '')
    {
        $data['js'] = 'cooperatives/cooperatives';
        $api = $this->config->item('api');
        $prov = !empty($this->input->get('prov'))?$this->input->get('prov'):'';
        $dist = !empty($this->input->get('dist'))?$this->input->get('dist'):'';
        $data['action'] = array(
            'api_base_url'      => $this->config->item('api_base_url'),
            'crud'              => $api.'/cooperatives/coop',
            'list_excel'        => $api.'/cooperatives/coop_excel',
            'staff'             => $api.'/cooperatives/staffs',
            'param'             => $prov,
            'Desa'              => $api.'/farmer/Desas',
            'Kecamatan'         => $api.'/farmer/Kecamatans',
            'Kabupaten'         => $api.'/farmer/Kabupatens',
            'Provinsi'          => $api.'/farmer/Provinsis',
            'photo'             => $api.'/images/coop/',
            'training'                  => $api . '/cooperatives/training',
            'training_type'             => $api . '/cooperatives/training_type',
            'service_provider'          => $api . '/cooperatives/service_provider',
            'participant'               => $api . '/cooperatives/participant',
            'participant_detail'        => $api . '/cooperatives/participant_detail',
            'coop_member'               => $api . '/cooperatives/coop_member',
            'participant_checklist'     => $api . '/cooperatives/participant_checklist',
            'attendance'                => $api . '/cooperatives/attendance',
            'cetak'                     => $api . '/cooperatives/cetak/',

            'store_composts'              => $api.'/cpg/composts',
            'store_compost_penjualans'    => $api.'/cpg/compost_penjualans',
            'compost'                     => $api.'/cpg/compost',

            'clone_ref'                   => $api . '/cpg/clone_ref',

            'store_nurseys'               => $api.'/cpg/nurseys',
            'store_nursey_penjualans'     => $api.'/cpg/nursey_penjualans',
            'store_nursey_monitorings'    => $api.'/cpg/nursey_monitorings',
            'nursey'                      => $api.'/cpg/nursey',

            'store_clonal_penjualans'     => $api.'/cpg/clonal_penjualans',
            'store_clonal_monitorings'    => $api.'/cpg/clonal_monitorings',
            'store_clonal_polygons'       => $api.'/cpg/clonal_polygons',
            'clonal'                      => $api.'/cpg/clonal',
            'trader'                      => $api.'/trader/',

            'prep_adv_filter_coop'        => $api.'/cooperatives/prep_adv_filter_coop',

            'act_index'    => $this->system->CekAksi('index'),
            'act_add'      => $this->system->CekAksi('add'),
            'act_update'   => $this->system->CekAksi('update'),
            'act_delete'   => $this->system->CekAksi('delete'),
            'act_save'     => $this->system->CekAksi('update'),
            'act_compost'           => $this->system->CekAksi('compost'),
            'act_nursery'           => $this->system->CekAksi('nursery'),
            'act_clonal_garden'     => $this->system->CekAksi('clonal_garden'),
            'act_ics_member'        => $this->system->CekAksi('ics_member'),
            'act_training'          => $this->system->CekAksi('training'),

            'ics_group'             => $api.'/cooperatives/icsgroup',
            'ics_member'            => $api.'/cooperatives/icsmember',
            'hakakses_lat_short'    => $this->system->cekSettingPerUser('latShort'),
            'hakakses_long_short'   => $this->system->cekSettingPerUser('longShort'),
            'hakakses_lat_long'     => $this->system->cekSettingPerUser('latLong'),
            'hakakses_long_long'    => $this->system->cekSettingPerUser('longLong'),
            'hakakses_polygon'      => $this->system->cekSettingPerUser('polygon'),
            );

        $this->LoadView($data, 'common_content_region');
    }
}
