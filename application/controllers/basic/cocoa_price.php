<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cocoa_price extends SS_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index($id = '')
    {
        $data['js'] = 'cocoa_price';
        $api = $this->config->item('api');
        $prov = !empty($this->input->get('prov'))?$this->input->get('prov'):'';
        $data['action'] = array(
            'crud' => $api.'/cocoa_price/data',
            //'combo_role' => $api.'/reference/position_combo_role',
            //'villageID' => $api . '/village/VillageID',
            //'crudCrop' => $api . '/village/cropl',
            //'crudInfrastructure' => $api . '/village/infrastructurel',
            'prov' => $prov,
            'prov_name' => $api . '/cocoa_price/Province_name/'.$prov,
            //'Kecamatan' => $api . '/village/Kecamatans',
            'Kabupaten' => $api . '/cocoa_price/Kabupatens/',
            'Provinsi' => $api . '/cocoa_price/Provinsis',
            'act_add' => ! $this->system->CekAksi('add') ? 'hide-icon' : '',
            'act_update' => ( ! $this->system->CekAksi('update') AND ! $this->system->CekAksi('detail')) ? 'hide-icon' : '',
            'act_delete' => ( ! $this->system->CekAksi('update')) ? 'hide-icon' : '',
            'act_save' => ( ! $this->system->CekAksi('update')) ? 'hide-icon' : '',
            'hakakses_lat_short'               => $this->system->cekSettingPerUser('latShort'),
            'hakakses_long_short'              => $this->system->cekSettingPerUser('longShort')
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

