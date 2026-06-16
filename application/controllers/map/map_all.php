<?php
/**
 * @Author: nikolius
 * @Date:   2017-05-12 11:30:03
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Map_all extends SS_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->language('map');
    }

    public function index()
    {
        // echo '<pre>'; print_r($_SESSION); echo '</pre>'; exit;
        $data   = array();
        $api    = $this->config->item('api');
        $data['action'] = array(
            'url_province'                  => $api.'/geospatial/province',
            'url_district'                  => $api.'/geospatial/district',
            'url_partner'                   => $api.'/farmer/data_partner',
            'url_ceksurvey'                 => $api.'/farmer/data_CekSurvey',
            'url_geospatialbydistrict'      => $api.'/geospatial/map',
            'url_districtmap'                   => $api.'/geospatial/districtmap',
            'url_photo'                     => $this->config->item('api_base_url').'images/member/',
            'url_photo_agent'                     => $this->config->item('api_base_url').'images/trader/',
            'url_api_images' => $this->config->item('api_base_url').'images/',
            'url_info'                  => site_url('map/map_all'),
            'url_area'                  => $api.'/geospatial/area',
            // 'act_default'       => $this->system->CekAksi('map_default'),
            'act_default'       => true,
            'act_supplychain'   => $this->system->CekAksi('map_supplychain'),
            'act_bank'          => $this->system->CekAksi('map_bank'),
            'act_view_detail'   => $this->checkViewAccess(),
        );
        $get = $this->input->get();
        if (!empty($get['area'])) {
            $api_farmer = $api.'/geospatial/plot_detail/'."?MemberID={$get['MemberID']}&PlotNr={$get['PlotNr']}&SurveyNr={$get['SurveyNr']}";
            // echo '<pre>'; print_r($api_farmer); echo '</pre>'; exit;

            // $area = @file_get_contents($api_farmer);
            // $data = json_decode($area, true);
            // echo '<pre>'; print_r($data); echo '</pre>'; exit;

            $api_polygon = $api.'/geospatial/polygon/'."?MemberID={$get['MemberID']}&PlotNr={$get['PlotNr']}&SurveyNr={$get['SurveyNr']}";
            // $polygon = @file_get_contents($api_polygon);
            // $area = json_encode($polygon, true);
            // echo '<pre>'; print_r($area); echo '</pre>'; exit;

            $this->load->view('map/area', array(
                // 'data' => $data,
                'api_farmer' => $api_farmer,
                'api_polygon' => $api_polygon,
                // 'area' => $area
                ));
            return;
        }
        $this->LoadView($data, 'map');
    }

    private function checkViewAccess()
    {
        // hardcoded untuk grup Management Unilever
        if ($_SESSION['groupid'] == 161) {
            return false;
        }
        return true;
    }
}