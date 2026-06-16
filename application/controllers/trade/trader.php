<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Trader extends SS_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index($id = '')
    {
        $data['js'] = 'trader';
        $data['js_additional'] = 'trader_survey';
        $api        = $this->config->item('api');
        $prov       = !empty($this->input->get('prov')) ? $this->input->get('prov') : '';
        $dist       = !empty($this->input->get('dist')) ? $this->input->get('dist') : '';
        // $qdistric = $this->db->query("SELECT District FROM ktv_district WHERE ProvinceID = $id ORDER BY District")->row();
        // $qprovince = $this->db->query("SELECT Province FROM ktv_province WHERE ProvinceID = $id")->row();
        $data['action'] = array(
            'api_base_url'             => $this->config->item('api_base_url'),
            'crud'                     => $api . '/trader/',
            'Desa'                     => $api . '/trader/Desas',
            'Kecamatan'                => $api . '/trader/Kecamatans',
            'Kabupaten'                => $api . '/trader/Kabupatens',
            'Provinsi'                 => $api . '/trader/Provinsis',
            'photo'                    => $this->config->item('api_base_url') . 'images/Photo_trader/',
            'label_provinsi'           => $api . '/training_kader/provinsi_label',
            'param'                    => $prov,
            // 'district'=>$qdistric->District,
            'district'                 => $dist,
            // 'province'=>$qprovince->Province,
            'province'                 => $prov,
            'staff'                    => $api . '/trader/staff',
            'quality_standard'         => $api . '/trader/quality_standard',
            'standard'                 => $api . '/trader/quality_standard_combo',
            'quality'                  => $api . '/trader/quality',
            'price'                    => $api . '/trader/price',
            'package'                  => $api . '/trader/package',
            'cetak'                    => $api . '/trader/cetak/',
            'partner'                  => $api . '/farmer/data_partner',

            'store_nurseys'            => $api . '/cpg/nurseys',
            'store_nursey_monitorings' => $api . '/cpg/nursey_monitorings',
            'nursey'                   => $api . '/cpg/nursey',

            'act_index'                => $this->system->CekAksi('index'),
            'act_add'                  => $this->system->CekAksi('add'),
            'act_update'               => $this->system->CekAksi('update'),
            'act_delete'               => $this->system->CekAksi('delete'),
            'act_save'                 => $this->system->CekAksi('index'),
            'act_trader_nursery'       => $this->system->CekAksi('trader_nursery'),
            'act_trader_survey'        => $this->system->CekAksi('trader_survey'),

            'hakakses_lat_short'       => $this->system->cekSettingPerUser('latShort'),
            'hakakses_long_short'      => $this->system->cekSettingPerUser('longShort'),
            'hakakses_lat_long'        => $this->system->cekSettingPerUser('latLong'),
            'hakakses_long_long'       => $this->system->cekSettingPerUser('longLong'),
            'hakakses_polygon'         => $this->system->cekSettingPerUser('polygon'),
        );
        $this->LoadView($data, 'common_content_region');
    }

}
