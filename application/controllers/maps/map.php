<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Map extends SS_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->language('map');
    }

    public function index()
    {
        $this->load->model('mmap');

        $data = array();

        $data['title'] = lang('Peta');

        $api = $this->config->item('api');
        $data['action'] = array(
            'base_url' => base_url(),
            'url_api'                           => $api,
            'url_geospatial'                    => $api.'/geospatial/maps',
            'url_supplychain'                   => $api.'/geospatial/supplychain',
            'url_supplychain_cpg'               => $api.'/geospatial/supplychain_cpg',
            'url_supplychain_farmer'            => $api.'/geospatial/supplychain_farmer',
            'url_districtmap'                   => $api.'/geospatial/districtmap',
            'url_districtmap_bank'              => $api.'/geospatial/bank_districtmap',
            'url_geospatialbydistrict'          => $api.'/geospatial/map',
            'url_geospatialbydistrict_bank'     => $api.'/geospatial/bank_map',
            'url_geospatialbank'                => $api.'/geospatial/bank',
            'url_province'                      => $api.'/geospatial/province',
            'url_weather'                       => $api.'/geospatial/weather',
            'url_photo'                         => $this->config->item('api_base_url').'images/Photo/',
            'm_cetak_basic_farmer'              => $api.'/farmer/cetak_basic_farmer/',
            'm_cetak_result_farmer'             => $api.'/farmer/cetak_result_farmer/',
            'm_cetak_basic_nutrisi'             => $api.'/farmer/cetak_basic_nutrisi/',
            'm_cetak_result_nutrisi'            => $api.'/farmer/cetak_result_nutrisi/',
            'm_cetak_basic_aff'                 => $api.'/farmer/cetak_basic_aff/',
            'm_cetak_result_aff'                => $api.'/farmer/cetak_aff/',
            'm_cetak_basic_ppi2012'             => $api.'/farmer/cetak_basic_ppi2012/',
            'm_cetak_result_ppi2012'            => $api.'/farmer/cetak_result_ppi2012/',
            'm_cetak_beneficiary_profiles'      => $api.'/farmer/cetak_beneficiary_profiles/',
            'm_cetak_farmer_summary'            => $api.'/farmer/cetak_farmer_summary_loan/',
            'url_ceksurvey'             => $api.'/farmer/data_CekSurvey',
            'url_district'              => $api.'/geospatial/district',
            'url_province_partner'      => $api.'/geospatial/partner',
            'url_bank'                  => $api.'/bank/banglist',
            'url_info'                  => site_url('maps/map'),
            'url_area'                  => $api.'/geospatial/area',
            'url_clone_area'            => $api.'/geospatial/clonal_area',
            'url_partner'               => $api.'/farmer/data_partner',
            'act_default'               => $this->system->CekAksi('map_default'),
            'act_supplychain'           => $this->system->CekAksi('map_supplychain'),
            'act_bank'                  => $this->system->CekAksi('map_bank'),
            );
        $data['supplychain_access']     = $this->mmap->checkSupplyAccess($_SESSION['userid']);
        $data['bank_access']            = $this->mmap->checkBankAccess($_SESSION['userid']);
        
        $get = $this->input->get();
        // if (!empty($get['farmer'])) {
        //     $api_url = $data['action']['url_geospatialbydistrict'].'/ProvinceID/'.$get['province'].'/DistrictID/'.$get['district'].'/Keyword/'.$get['farmer'].'/skop/2';
        //     $content = @json_decode(file_get_contents($api_url), true);
        //     $farmer = $content['data'][0];
        //     $farmer['url_photo'] = $data['action']['url_photo'];

        //     $this->load->view('map/farmer', $farmer);
        //     return;
        // }
        if (!empty($get['area'])) {
            $api_farmer = $api."/geospatial/garden_detail/FarmerID/{$get['farmer']}/GardenNr/{$get['garden']}";

            // $content = @json_decode(file_get_contents($api_url), true);
            
            // $area['farmer'] = $content;
            $area['api_farmer'] = $api_farmer;
            
            $api_polygon = $data['action']['url_area']."?farmer={$get['farmer']}&garden={$get['garden']}&survey={$get['survey']}";
            $area['area'] = json_encode(0);
            // $polygon = @file_get_contents($api_url);
            $area['api_polygon'] = $api_polygon;
            // if ($polygon) {
            //     $area['area'] = $polygon;
            // }
            $area['api_area'] = $api.'/geospatial/area';

            $this->load->view('map/area', $area);
            return;
        }
        if (!empty($get['clone_area'])) {            
            $api_url = $data['action']['url_clone_area']."?id={$get['id']}";

            $area = @file_get_contents($api_url);
            $data = json_decode($area, true);

            $this->load->view('map/clone_area', array(
                'data' => $data['data'],
                'area' => $area
                ));
            return;
        }

        $this->LoadView($data, 'map');
    }

}

