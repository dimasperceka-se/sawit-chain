<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Warehouse extends SS_Controller {

   public function __construct() {
      parent::__construct();
   }

   public function index($id = '') {
      $data['js'] = 'warehouse';
      $api = $this->config->item('api');
      $prov = $this->input->get('prov');
      $dist = $this->input->get('dist');
      // $qprov = $this->db->get_where('ktv_province',array('ProvinceID' => $id))->row();
      // $qdistric = $this->db->query("SELECT District FROM ktv_district WHERE ProvinceID = $id ORDER BY District")->row();
      $data['action'] = array(
         'title'                       => 'Warehouse',
         'type'                        => 'warehouse',

         'crud'                        => $api.'/traceability/',
         'Desa'                        => $api.'/farmer/Desas',
         'Kecamatan'                   => $api.'/farmer/Kecamatans',
         'Kabupaten'                   => $api.'/farmer/Kabupatens',
         'Provinsi'                    => $api.'/farmer/Provinsis',
         'photo'                       => $this->config->item('api_base_url').'images/Photo_traceability/',
         'farmer_staff'                => 'hidden',
         'paramval'                    => $prov,
         'param'                       => $prov==false?'':$prov,
         'district'                    => $dist==false?'':$dist,
         'staff'                       => $api.'/traceability/staff',
         'quality_standard'            => $api.'/traceability/quality_standard',
         'standard'                    => $api.'/traceability/quality_standard_combo',
         'quality'                     => $api.'/traceability/quality',
         'price'                       => $api.'/traceability/price',
         'package'                     => $api.'/traceability/package',
         'reward'                      => $api.'/traceability/reward',
         'partner'                     => $api.'/traceability/partner',
         'store_nursey_penjualans'     => $api.'/cpg/nursey_penjualans',

         'act_index'    => $this->system->CekAksi('index'),
         'act_add'      => $this->system->CekAksi('add'),
         'act_update'   => $this->system->CekAksi('update'),
         'act_delete'   => $this->system->CekAksi('delete'),
         'act_save'     => $this->system->CekAksi('index'),
         'hakakses_lat_short'               => $this->system->cekSettingPerUser('latShort'),
         'hakakses_long_short'              => $this->system->cekSettingPerUser('longShort'),
         'hakakses_lat_long'                => $this->system->cekSettingPerUser('latLong'),
         'hakakses_long_long'               => $this->system->cekSettingPerUser('longLong'),
         'hakakses_elevation'               => $this->system->cekSettingPerUser('elevation')
      );
      $this->LoadView($data, 'common_content_region');
   }

}

