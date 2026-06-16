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
        //$data['js'] = 'sce_farmer_20160815';
        $data['js']     = 'sce_farmer';
        $api            = $this->config->item('api');
        $prov = !empty($this->input->get('prov'))?$this->input->get('prov'):'';
        $dist = !empty($this->input->get('dist'))?$this->input->get('dist'):'';
        $data['action'] = array(
            'crud'                => $api . '/sce/farmer_sce',
            'sce_farmer_data'     => $api . '/sce/farmer_sce_by_id',
            'staff'               => $api . '/sce/farmer_sce_staff',
            'Kabupaten'           => $api . '/farmer/Kabupatens',
            'photo'               => $this->config->item('api_base_url') . 'images/Photo/',
            'param'               => $prov,
            'Kabupaten'           => $api . '/farmer/Kabupatens',
            'farmer_garden'       => $api.'/farmer/farmerl_garden_status',

            'act_index'           => $this->system->CekAksi('index'),
            'act_add'             => $this->system->CekAksi('add'),
            'act_update'          => $this->system->CekAksi('update'),
            'act_delete'          => $this->system->CekAksi('delete'),

            'hakakses_lat_short'  => $this->system->cekSettingPerUser('latShort'),
            'hakakses_long_short' => $this->system->cekSettingPerUser('longShort'),
            'hakakses_lat_long'   => $this->system->cekSettingPerUser('latLong'),
            'hakakses_long_long'  => $this->system->cekSettingPerUser('longLong'),
        );
        $data['style'] = "
         .invalid .x-grid-cell {
             background-color: #ffe2e2;
             color: #900;
         }

         .new .x-grid-cell {
             background-color: #F7FAB1;
             color: #8D9104;
         }";
        $this->LoadView($data, 'common_content_region');
    }

}
