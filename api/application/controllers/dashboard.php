<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('mdashboard');
        $this->load->model('home/magri');
        // $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
        $this->user = $this->mdashboard->check_user();
        // $this->user = $this->muserprofile->getUserProfile();
    }

    function dashboards_get() {
        $this->load->model('home/mmain');
        // if ($this->get('daer')=='') {
        // if (empty($_SESSION['daerah_access'])) {
        if ($this->user['UserIsAdmin']) {
            // if ( ! $data = $this->cache->get('dashboards_get'))
            // {
                $data = $this->mmain->readData($this->get('prov'),$this->get('kab'));
            //     $this->cache->save('dashboards_get', $data, 300);
            // }
        // } else {
        } elseif($this->user['isProgramStaff'] || $this->user['isPrivateStaff']) {
            $data = $this->mmain->readDataDistrict($this->user, $this->get('daer'),$this->get('priv'),$this->get('partner'),$this->get('prov'));
            // $data['district'] = $this->mdashboard->readDistrict($this->get('daer'));
        }
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function mars_get() {
        $this->load->model('home/mmars');
        // if ($this->get('daer')=='') {
        // if (empty($_SESSION['daerah_access'])) {
        if ($this->user['UserIsAdmin']) {
            // if ( ! $data = $this->cache->get('dashboards_get'))
            // {
                $data = $this->mmars->readData($this->get('prov'),$this->get('kab'),$this->get('cert_holder'));
            //     $this->cache->save('dashboards_get', $data, 300);
            // }
        // } else {
        } elseif($this->user['isProgramStaff'] || $this->user['isPrivateStaff']) {
            $data = $this->mmars->readDataDistrict($this->user, $this->get('daer'),$this->get('priv'),$this->get('partner'),$this->get('prov'),$this->get('cert_holder'));
            // $data['district'] = $this->mdashboard->readDistrict($this->get('daer'));
        }
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function cargill_get() {
        $this->load->model('home/mcargill');
        // if ($this->get('daer')=='') {
        // if (empty($_SESSION['daerah_access'])) {
        if ($this->user['UserIsAdmin']) {
            // if ( ! $data = $this->cache->get('dashboards_get'))
            // {
                $data = $this->mcargill->readData($this->get('prov'),$this->get('kab'));
            //     $this->cache->save('dashboards_get', $data, 300);
            // }
        // } else {
        } elseif($this->user['isProgramStaff'] || $this->user['isPrivateStaff']) {
            $data = $this->mcargill->readDataDistrict($this->user, $this->get('daer'),$this->get('priv'),$this->get('partner'),$this->get('prov'));
            // $data['district'] = $this->mdashboard->readDistrict($this->get('daer'));
        }
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function demographic_get() {
        $this->load->model('home/mdemographic');
        // if ($this->get('daer')=='') {
        // if (empty($_SESSION['daerah_access'])) {
        if ($this->user['UserIsAdmin']) {
            // $data = $this->mdashboard->readDataDemographic($this->get('prov'),$this->get('kab'),$this->get('petani'),$this->get('tahun'));
            $data = $this->mdemographic->readDataDemographic($this->get('prov'),$this->get('kab'),$this->get('petani'),$this->get('tahun'));
        // } else {
        } elseif($this->user['isProgramStaff'] || $this->user['isPrivateStaff']) {
            // $data = $this->mdashboard->readDataDistrictDemographic($this->user, $this->get('daer'),$this->get('priv'),$this->get('petani'),$this->get('partner'),$this->get('prov'),$this->get('tahun'));
            $data = $this->mdemographic->readDataDistrictDemographic($this->user, $this->get('daer'),$this->get('priv'),$this->get('petani'),$this->get('partner'),$this->get('prov'),$this->get('tahun'));
            // $data['district'] = $this->mdashboard->readDistrict($this->get('daer'));
        }
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function district_get() {
        $data = $this->mdashboard->readDistrictByProvince($this->get('prov'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function groups_get() {
        $this->load->model('home/mgroups');
        // if ($this->get('daer')=='') {
        // if (empty($_SESSION['daerah_access'])) {
        if ($this->user['UserIsAdmin']) {
            // $data = $this->mdashboard->readDataGroups($this->get('prov'),$this->get('kab'));
            $data = $this->mgroups->readDataGroups($this->get('prov'),$this->get('kab'));
        // } else {
        } elseif($this->user['isProgramStaff'] || $this->user['isPrivateStaff']) {
            // $data = $this->mdashboard->readDataDistrictGroups($this->user, $this->get('daer'),$this->get('priv'),$this->get('partner'),$this->get('prov'),$this->get('partner'),$this->get('prov'));
            $data = $this->mgroups->readDataDistrictGroups($this->user, $this->get('daer'),$this->get('priv'),$this->get('partner'),$this->get('prov'),$this->get('partner'),$this->get('prov'));
            // $data['district'] = $this->mdashboard->readDistrict($this->get('daer'));
        }
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function certification_get() {
        $this->load->model('home/mcertification');
        // if ($this->get('daer')=='') {
        // if (empty($_SESSION['daerah_access'])) {
        if ($this->user['UserIsAdmin']) {
            // $data = $this->mdashboard->readDataCertification($this->get('prov'),$this->get('kab'),$this->get('startdate'),$this->get('enddate'));
            $data = $this->mcertification->readDataCertification($this->get('prov'),$this->get('kab'),$this->get('startdate'),$this->get('enddate'));
        // } else {
        } elseif($this->user['isProgramStaff'] || $this->user['isPrivateStaff']) {
            // $data = $this->mdashboard->readDataDistrictCertification($this->user, $this->get('daer'),$this->get('priv'),$this->get('partner'),$this->get('prov'),$this->get('startdate'),$this->get('enddate'));
            $data = $this->mcertification->readDataDistrictCertification($this->user, $this->get('daer'),$this->get('priv'),$this->get('partner'),$this->get('prov'),$this->get('startdate'),$this->get('enddate'));
            // $data['district'] = $this->mdashboard->readDistrict($this->get('daer'));
        }

        if($data) $this->response($data, 200);
        else $this->response(array('data'=>array()), 200);
        // else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function garden_get() {
        $this->load->model('home/mgarden');
        // if ($this->get('daer')=='') {
        // if (empty($_SESSION['daerah_access'])) {
        if ($this->user['UserIsAdmin']) {
            $data = $this->mgarden->readDataGarden($this->get('prov'),$this->get('kab'),$this->get('petani'),$this->get('tahun'),$this->get('survey'));
        // } else {
        } elseif($this->user['isProgramStaff'] || $this->user['isPrivateStaff']) {
            $data = $this->mgarden->readDataDistrictGarden($this->user, $this->get('daer'),$this->get('priv'),$this->get('petani'),$this->get('partner'),$this->get('prov'),$this->get('tahun'),$this->get('survey'));
            $data['district'] = $this->mdashboard->readDistrict($this->get('daer'));
        }
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function nutrition_get() {
        $this->load->model('home/mnutrition');
        // if ($this->get('daer')=='') {
        // if (empty($_SESSION['daerah_access'])) {
        if ($this->user['UserIsAdmin']) {
            // $data = $this->mdashboard->readDataNutrition($this->get('prov'),$this->get('kab'));
            $data = $this->mnutrition->readDataNutrition($this->get('prov'),$this->get('kab'));
        // } else {
        } elseif($this->user['isProgramStaff'] || $this->user['isPrivateStaff']) {
            // $data = $this->mdashboard->readDataDistrictNutrition($this->user, $this->get('daer'),$this->get('priv'),$this->get('partner'),$this->get('prov'));
            $data = $this->mnutrition->readDataDistrictNutrition($this->user, $this->get('daer'),$this->get('priv'),$this->get('partner'),$this->get('prov'));
            // $data['district'] = $this->mdashboard->readDistrict($this->get('daer'));
        }
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function survey_get() {
        $this->load->model('home/msurvey');
        // if ($this->get('daer')=='') {
        // if (empty($_SESSION['daerah_access'])) {
        if ($this->user['UserIsAdmin']) {
            $data = $this->msurvey->readDataSurvey($this->get('prov'),$this->get('kab'));
            // $data = $this->mdashboard->readDataSurvey($this->get('prov'),$this->get('kab'));
        // } else {
        } elseif($this->user['isProgramStaff'] || $this->user['isPrivateStaff']) {
            $data = $this->msurvey->readDataDistrictSurvey($this->user, $this->get('daer'),$this->get('priv'),$this->get('partner'),$this->get('prov'));
            // $data = $this->mdashboard->readDataDistrictSurvey($this->user, $this->get('daer'),$this->get('priv'),$this->get('partner'),$this->get('prov'));
            // $data['district'] = $this->mdashboard->readDistrict($this->get('daer'));
        }
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function traceability_get() {
      //echo $this->get('awal').','.$this->get('akhir');exit;
      // if ($this->get('daer')=='') {
      // if (empty($_SESSION['daerah_access'])) {
      if ($this->user['UserIsAdmin']) {
         $data = $this->mdashboard->readDataTraceability($this->get('prov'),$this->get('kab'),$this->get('awal'),$this->get('akhir'),$this->get('traceability_partner'));
      } else {
         $traceability_partner = null;
         if ($this->user['isProgramStaff']) {
             $traceability_partner = $this->user['programPartner'];
         } elseif ($this->user['isPrivateStaff']) {
             $traceability_partner = $this->user['privatePartner'];
         }
         $data = $this->mdashboard->readDataDistrictTraceability($this->user, $this->get('daer'),$this->get('priv'),$this->get('awal'),$this->get('akhir'),$this->get('partner'),$this->get('prov'),$this->get('traceability_partner'));
         // $data['district'] = $this->mdashboard->readDistrict($this->get('daer'));
      }
      if($data) $this->response($data, 200);
      else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function traceability__get() {
      $data = $this->mdashboard->readDataTraceability_($this->get('awal'),$this->get('akhir'),$this->get('orgid'));
      if($data) $this->response($data, 200);
      else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function training_get() {
        $this->load->model('home/mtraining');
        $training = $this->get('training');
        // switch ($training) {
        //     case 'kader':
        //         $func = 'Kader';
        //         break;
        //     case 'farmer':
        //         $func = 'Farmer';
        //         break;
        //     default:
        //         $func = '';
        //         break;
        // }
        if (empty($training)) {
            $training = 'all';
        }
        // if (empty($_SESSION['daerah_access'])) {
        if ($this->user['UserIsAdmin']) {
            // $func = "readDataTraining{$func}";
            // $data = $this->mdashboard->$func($this->get('prov'),$this->get('kab'));
            $data = $this->mtraining->readDataTraining($this->get('prov'),$this->get('kab'),$training);
        // } else {
        } elseif($this->user['isProgramStaff'] || $this->user['isPrivateStaff']) {
            // $func = "readDataDistrictTraining{$func}";
            // $data = $this->mdashboard->$func($this->user, $this->get('daer'),$this->get('priv'),$this->get('partner'),$this->get('prov'));
            $data = $this->mtraining->readDataDistrictTraining($this->user, $this->get('daer'),$this->get('priv'),$this->get('partner'),$this->get('prov'),$training);
            // $data['district'] = $this->mdashboard->readDistrict($this->get('daer'));
        }
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function master_training_get() {
        $this->load->model('home/mtraining');
        $training = 'master';
        // if ($this->get('daer')=='') {
        // if (empty($_SESSION['daerah_access'])) {
        if ($this->user['UserIsAdmin']) {
            // $data = $this->mdashboard->readDataTrainingMaster($this->get('prov'),$this->get('kab'));
            $data = $this->mtraining->readDataTrainingMaster($this->get('prov'),$this->get('kab'),$this->get('staff_type'));
        // } else {
        } elseif($this->user['isProgramStaff'] || $this->user['isPrivateStaff']) {
            // $data = $this->mdashboard->readDataDistrictTrainingMaster($this->user, $this->get('daer'),$this->get('priv'),$this->get('partner'),$this->get('prov'));
            $data = $this->mtraining->readDataDistrictTrainingMaster($this->user, $this->get('daer'),$this->get('priv'),$this->get('partner'),$this->get('prov'),$this->get('staff_type'));
            // $data['district'] = $this->mdashboard->readDistrict($this->get('daer'));
        }
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function kader_training_get() {
        // if ($this->get('daer')=='') {
        // if (empty($_SESSION['daerah_access'])) {
        if ($this->user['UserIsAdmin']) {
            $data = $this->mdashboard->readDataTrainingKader($this->get('prov'),$this->get('kab'));
        // } else {
        } elseif($this->user['isProgramStaff'] || $this->user['isPrivateStaff']) {
            $data = $this->mdashboard->readDataDistrictTrainingKader($this->user, $this->get('daer'),$this->get('priv'),$this->get('partner'),$this->get('prov'));
            $data['district'] = $this->mdashboard->readDistrict($this->get('daer'));
        }
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function finance_get() {
        $this->load->model('home/mfinance');
        // if ($this->get('daer')=='') {
        // if (empty($_SESSION['daerah_access'])) {
        if ($this->user['UserIsAdmin']) {
            // $data = $this->mdashboard->readDataFinance($this->get('prov'),$this->get('kab'));
            $data = $this->mfinance->readDataFinance($this->get('prov'),$this->get('kab'));
        // } else {
        } elseif($this->user['isProgramStaff'] || $this->user['isPrivateStaff']) {
            // $data = $this->mdashboard->readDataDistrictFinance($this->user, $this->get('daer'),$this->get('priv'),$this->get('partner'),$this->get('prov'));
            $data = $this->mfinance->readDataDistrictFinance($this->user, $this->get('daer'),$this->get('priv'),$this->get('partner'),$this->get('prov'));
            // $data['district'] = $this->mdashboard->readDistrict($this->get('daer'));
        }
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }

    function cooperatives_get()
    {
        $data = $this->mdashboard->readDataCoop(getCoopID());
        // $data['active_member'] = 0;
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any data!'), 200);
    }

    function environment_get() {
        $this->load->model('home/menvironment');
        // if ($this->get('daer')=='') {
        // if (empty($_SESSION['daerah_access'])) {
        if ($this->user['UserIsAdmin']) {
            // $data = $this->mdashboard->readDataEnvironment($this->get('prov'),$this->get('kab'));
            $data = $this->menvironment->readDataEnvironment($this->get('prov'),$this->get('kab'));
        // } else {
        } elseif($this->user['isProgramStaff'] || $this->user['isPrivateStaff']) {
            // $data = $this->mdashboard->readDataDistrictEnvironment($this->user, $this->get('daer'),$this->get('priv'),$this->get('partner'),$this->get('prov'));
            $data = $this->menvironment->readDataDistrictEnvironment($this->user, $this->get('daer'),$this->get('priv'),$this->get('partner'),$this->get('prov'));
            // $data['district'] = $this->mdashboard->readDistrict($this->get('daer'));
        }
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }

    function agriinput_get() {
        // if ($this->get('daer')=='') {
        // if (empty($_SESSION['daerah_access'])) {
        if ($this->user['UserIsAdmin']) {
            $data = $this->magri->readDataAgriinput($this->get('prov'),$this->get('kab'));
        // } else {
        } elseif($this->user['isProgramStaff'] || $this->user['isPrivateStaff']) {
            $data = $this->magri->readDataDistrictAgriinput($this->user, $this->get('daer'),$this->get('priv'),$this->get('partner'),$this->get('prov'));
            $data['district'] = $this->mdashboard->readDistrict($this->get('daer'));
        }
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }

    public function region_get()
    {
        $prov       = $this->get('prov');
        $kab        = $this->get('kab');
        $daer       = $this->get('daer');
        $region_status = $this->get('region_status');
        $region = $this->mdashboard->getRegions($this->user, $prov, $kab, $daer,$region_status);

        $province = '';
        if ($prov) {
            $province = $this->mdashboard->getProvince($prov);
        }

        $this->response(array(
            'data' => $region,
            'province' => $province
        ), 200);
    }

    public function region_mars_get()
    {
        $prov       = $this->get('prov');
        $kab        = $this->get('kab');
        $daer       = $this->get('daer');
        $region = $this->mdashboard->getRegionMars($this->user, $prov, $kab, $daer);
        $province = '';
        if ($prov) {
            $province = $this->mdashboard->getProvince($prov);
        }
        $this->response(array(
            'data' => $region,
            'province' => $province
        ), 200);
    }

    public function region_cargill_get()
    {
        $prov       = $this->get('prov');
        $kab        = $this->get('kab');
        $daer       = $this->get('daer');
        $region = $this->mdashboard->getRegionCargill($this->user, $prov, $kab, $daer);
        $province = '';
        if ($prov) {
            $province = $this->mdashboard->getProvince($prov);
        }
        $this->response(array(
            'data' => $region,
            'province' => $province
        ), 200);
    }

    public function region_master_get()
    {
        $prov   = $this->get('prov');
        $kab    = $this->get('kab');
        $daer   = $this->get('daer');
        $region = $this->mdashboard->getRegionMaster($this->user, $prov, $kab, $daer);
        $province = '';
        if ($prov) {
            $province = $this->mdashboard->getProvince($prov);
        }
        $this->response(array(
            'data' => $region,
            'province' => $province
        ), 200);
    }

    public function region_session_get(){
        $province = $this->mdashboard->getProvinceSession();
        $this->response(array(
            'data' => $province,
        ), 200);
    }

    public function district_session_get(){
        $district = $this->mdashboard->getDistrictSession();
        $this->response(array(
            'data' => $district,
        ), 200);
    }

    public function subdistrict_session_get(){
        
        $id   = $this->get('id');
        
        $subdistrict = $this->mdashboard->getSubDistrictSession($id);
        $this->response(array(
            'data' => $subdistrict,
        ), 200);
    }

    public function village_session_get(){
        
        $id   = $this->get('id');
        
        $village = $this->mdashboard->getVillageNew($id);
        $this->response(array(
            'data' => $village,
        ), 200);
    }

    public function region_kpi_get()
    {
        $prov   = $this->get('prov');
        $kab    = $this->get('kab');
        $daer   = $this->get('daer');
        $region = $this->mdashboard->getRegionKpi($this->user, $prov, $kab, $daer);
        $province = '';
        if ($prov) {
            $province = $this->mdashboard->getProvince($prov);
        }
        $this->response(array(
            'data' => $region,
            'province' => $province
        ), 200);
    }

    public function traceability_partner_get()
    {
        $prov   = $this->get('prov');
        $kab    = $this->get('kab');
        $daer   = $this->get('daer');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $traceability_partner    = $this->get('traceability_partner');
        // echo '<pre>'; print_r($this->user); echo '</pre>';
        $partners = $this->mdashboard->getPartners($this->user, $prov, $kab, $daer, $start, $end);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $partner = '';
        if ($traceability_partner) {
            $partner = $this->mdashboard->getPartner($traceability_partner);
        }
        $this->response(array(
            'data' => $partners,
            'partner' => $partner
        ), 200);
    }

    public function coop_get()
    {
         $coop = $this->mdashboard->getCoops();
         $this->response(array(
            'data' => $coop
        ), 200);
    }

    public function bank_get() {
        $this->load->model('home/mbank');
        if ($this->get('daer') == '') {
            // $data = $this->mdashboard->readBank($this->get('prov'),$this->get('kab'),$this->get('petani'),$this->get('tahun'));
            $data = $this->mbank->readBank($this->get('prov'),$this->get('kab'),$this->get('petani'),$this->get('tahun'));
        // } else {
        } elseif($this->user['isProgramStaff'] || $this->user['isPrivateStaff']) {
            // $data = $this->mdashboard->readDistrictBank($this->get('daer'),$this->get('priv'),$this->get('petani'),$this->get('partner'),$this->get('prov'),$this->get('tahun'));
            $data = $this->mbank->readDistrictBank($this->get('daer'),$this->get('priv'),$this->get('petani'),$this->get('partner'),$this->get('prov'),$this->get('tahun'));
        }
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    public function cocoa_price_get() {
        $this->load->model('home/mcocoaprice');
        // if (empty($_SESSION['daerah_access'])) {
        if ($this->user['UserIsAdmin']) {
            $data = $this->mcocoaprice->readDataPrice($this->get('prov'),$this->get('kab'),$this->get('petani'),$this->get('tahun'));
        // } else {
        } elseif($this->user['isProgramStaff'] || $this->user['isPrivateStaff']) {
            $data = $this->mcocoaprice->readDistrictDataPrice($this->user, $this->get('daer'),$this->get('priv'),$this->get('partner'),$this->get('prov'));
        }
        if($data) $this->response($data, 200);
        else $this->response(array('data' => array()), 200);
        // else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    public function kpi_get() {
        $this->load->model('home/mkpi');
        // if (empty($_SESSION['daerah_access'])) {
        if ($this->user['UserIsAdmin']) {
            $data = $this->mkpi->readData($this->get('prov'),$this->get('kab'),$this->get('petani'),$this->get('tahun'));
        // } else {
        } elseif($this->user['isProgramStaff'] || $this->user['isPrivateStaff']) {
            $data = $this->mkpi->readDistrictData($this->user, $this->get('daer'),$this->get('priv'),$this->get('partner'),$this->get('prov'));
        }
        if($data) $this->response($data, 200);
        else $this->response(array('data' => array()), 200);
        // else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    public function certification_period_get()
    {
        $year = $this->get('year');
        $prov = $this->get('prov');
    }

    public function region_traceability_get()
    {
        $prov       = $this->get('prov');
        $kab        = $this->get('kab');
        $kec        = $this->get('kec');
        $desa        = $this->get('desa');
        $daer       = $this->get('daer');
        $region = $this->mdashboard->getRegionsTraceability($this->user, $prov, $kab, $kec, $desa, $daer);
        $province = '';
        $district = '';
        $subdistrict = '';
        if ($prov) {
            $province = $this->mdashboard->getProvince($prov);
        }
        if ($kab) {
            $district = $this->mdashboard->getDistrict($kab);
        }
        if ($kec) {
            $subdistrict = $this->mdashboard->getSubDistrict($kec);
        }
        if ($desa) {
            $village = $this->mdashboard->getVillage($desa);
        }
        $this->response(array(
            'data' => $region,
            'province' => $province,
            'district' => $district,
            'subdistrict' => $subdistrict,
            'village' => $village
        ), 200);
    }

    public function district_traceability_get()
    {
        $prov       = $this->get('prov');
        $kab        = $this->get('kab');
        $kec        = $this->get('kec');
        $desa        = $this->get('desa');
        $daer       = $this->get('daer');
        $region = $this->mdashboard->getRegionsTraceability($this->user, $prov, $kab, $kec, $desa, $daer);
        $province = '';
        $district = '';
        $subdistrict = '';
        if ($prov) {
            $province = $this->mdashboard->getProvince($prov);
        }
        if ($kab) {
            $district = $this->mdashboard->getDistrict($kab);
        }
        if ($kec) {
            $subdistrict = $this->mdashboard->getSubDistrict($kec);
        }
        if ($desa) {
            $village = $this->mdashboard->getVillage($desa);
        }
        $this->response(array(
            'data' => $region,
            'province' => $province,
            'district' => $district,
            'subdistrict' => $subdistrict,
            'village' => $village
        ), 200);
    }
    
    public function do_get()
    {
        $this->response($this->mdashboard->listDO(), 200);
    }

    public function year_list_get(){
        // Year to start available options at
        $earliest_year = 2017; 
        // Set your latest year you want in the range, in this case we use PHP to just set it to the current year.
        $latest_year = date('Y'); 

        $year = array();
        // Loops over each int[year] from current year, back to the $earliest_year [1950]
        foreach ( range( $latest_year, $earliest_year ) as $i ) {
            array_push($year,array("id"=>$i,"name"=>$i));
        }

        $this->response($year,200);
    }
    
    public function group_mill_refinery_get()
    {
        $this->load->model('dboard/mdboardtraceability');
        $this->response($this->mdboardtraceability->lisGroupMillRefinery());
    }

    public function mill_list_refinery_get()
    {
        $this->load->model('dboard/mdboardtraceability');
        $this->response($this->mdboardtraceability->listMillRefinery());
    }

    public function mill_list_fa_get()
    {
        $this->load->model('dboard/mdboardtraceability');
        $this->response($this->mdboardtraceability->listMillFA());
    }
    
    public function group_mill_get()
    {
        $this->load->model('dboard/mdboardtraceability');
        $this->response($this->mdboardtraceability->listGroupMill());
    }

    public function mill_list_get()
    {   
        $this->load->model('dboard/mdboardtraceability');
        $this->response($this->mdboardtraceability->listMill());
    }

    public function supplier_list_get()
    {   
        $this->load->model('dboard/mdboardtraceability');
        $this->response($this->mdboardtraceability->ListSupplier());
    }
    
    public function do_list_get()
    {
        $this->load->model('dboard/mdboardtraceability');
        $this->response($this->mdboardtraceability->listDO($this->get('mill')), 200);
    }
    
    public function agent_list_get()
    {
        $this->load->model('dboard/mdboardtraceability');
        $this->response($this->mdboardtraceability->listAgent($this->get('mill'), $this->get('do')), 200);
    }
	
	
	function store_supplyorg_get()
	{ 
        $data = $this->mdashboard->readstore_supplyorg();
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
	}
	
	function store_supplyorgChild_get()
	{  
		$data = $this->mdashboard->readstore_supplyorgchild();
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
	}
	/*******************END  DASBOARD TRACEBILTIY SUPPLY CHAIN***************************/
	
	/*******************END  DASBOARD TRACEBILTIY SUPPLY CHAIN NEW***************************/
	function traceability_index_new_get() { 
		 
		$data = array();
        $data = $this->mdashboard->readDataTraceabilityNew2($this->get());
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }
	 
	function trace_hub_chartnew_get()
	{
		 $data = array();
		 $array_periode = array();  
		 $array_datas = array();
		  
		 $farmerTrans = $this->mdashboard->getTransactionFarmerPerMonthNew($this->get('DateStart'), $this->get('DateEnd'));
		 $SalesTrans = $this->mdashboard->getTransactionSalesPerMonth($this->get('DateStart'), $this->get('DateEnd'));
		 

		 $data = array( 
            'pie1_series' 		=> $this->mdashboard->getDataProdTracebilty(),
			'pie2_series' 	    => $this->mdashboard->getDataTraceSalesTracebiltyMonthNew($this->get('DateStart'), $this->get('DateEnd')),

			
			'CatFarmers' 		=> "'".implode("','",$farmerTrans['categories'])."'", 
			'farmers' 			=> json_encode($farmerTrans['results']),
			
			'CatSales' 			=> "'".implode("','",$SalesTrans['categories'])."'", 
			'Sales' 			=> json_encode($SalesTrans['results']),
			
         ); 
		 $this->load->view('trace_hub_chart', $data); 
	}
    
    /*******************END  DASBOARD TRACEBILTIY SUPPLY CHAIN ***************************/
    function traceability_new_get() { 
         
        $data = array();
        $data = $this->mdashboard->readDataTraceabilityNew($this->get());
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }
     
    function trace_hub_chart_get()
    {
         $data = array();
         $array_periode = array();  
         $array_datas = array();
          
         $farmerTrans = $this->mdashboard->getTransactionFarmerPerMonthNew($this->get('DateStart'), $this->get('DateEnd'));
         $SalesTrans = $this->mdashboard->getTransactionSalesPerMonth($this->get('DateStart'), $this->get('DateEnd'));
         

         $data = array( 
            'pie1_series'       => $this->mdashboard->getDataProdTracebilty(),
            'pie2_series'       => $this->mdashboard->getDataTraceSalesTracebiltyMonthNew($this->get('DateStart'), $this->get('DateEnd')),

            
            'CatFarmers'        => "'".implode("','",$farmerTrans['categories'])."'", 
            'farmers'           => json_encode($farmerTrans['results']),
            
            'CatSales'          => "'".implode("','",$SalesTrans['categories'])."'", 
            'Sales'             => json_encode($SalesTrans['results']),
            
         ); 
         $this->load->view('trace_hub_chart', $data); 
    }
	
	
	
}
