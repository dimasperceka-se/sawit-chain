<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Village extends REST_Controller {

    public function __construct() {
        $this->file = $_FILES;
        parent::__construct();
        $this->load->model('village/mvillage');
    }

    function villagels_get() {
        $data = $this->mvillage->readVillages(
                $this->get('prov'),
                $this->get('kab'),
                $this->get('kec'),
                $this->get('key'),
                $this->get('sort'),
                $this->get('start'),
                $this->get('limit')
        );
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
    }

	function Provinsis_get() {
		$sesPartner = ($_SESSION['FlagAccess'] > 0) ? $_SESSION['PartnerID'] : 'ALL';
        $data = $this->mvillage->readProvinsis($sesPartner);
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
    }

    function Kabupatens_get() {
		$sesPartner = ($_SESSION['PartnerID'] > 1)? $_SESSION['PartnerID']:'ALL';
        $data = $this->mvillage->readKabupatens($this->get('prov'),$sesPartner);
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
    }

	function KabupatenForms_get() {
		$sesPartner = ($_SESSION['PartnerID'] > 1)? $_SESSION['PartnerID']:'ALL';
        $data = $this->mvillage->readKabupatenForms($this->get('key'),$sesPartner);
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
    }

    function Kecamatans_get() {
        $data = $this->mvillage->readKecamatans($this->get('key'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
    }

	function VillageID_get() {
        if(!$this->get('id')) $this->response(NULL, 400);
        $data = $this->mvillage->readNewVillageID($this->get('id'),$this->get('id_old'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Village could not be found'), 404);
    }

	function villagel_post() {
        if(substr($this->post('VillageID'),0,7) != $this->post('Kecamatan')){
			$this->response(array('error' => 'VillageID error'), 404);
		}else{
			$data = $this->mvillage->createVillage(
                $this->post('Kecamatan'),
                $this->post('VillageID'),
                $this->post('Village'),
                $this->post('VillageHeadName'),
                $this->post('VillageHeadGender'),
                $this->post('VillageHeadLatitude'),
                $this->post('VillageHeadLongitude')
			);
			if($data) $this->response($data, 200);
			else $this->response(array('error' => 'Village could not be found'), 404);
		}
    }

	function villagel_get() {
        if(!$this->get('id')) $this->response(NULL, 400);
        $data = $this->mvillage->readVillage($this->get('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Farmer could not be found'), 404);
    }

	function villagel_put() {
		if(substr($this->put('VillageID'),0,7) != $this->put('Kecamatan')){
			$this->response(array('error' => 'VillageID error'), 404);
		}else{
			if(!$this->put('VillageID_old')) $this->response(NULL, 400);
			$data = $this->mvillage->updateVillage(
					$this->put('VillageID_old'),
					$this->put('Kecamatan'),
					$this->put('VillageID'),
					$this->put('Village'),
					$this->put('VillageHeadName'),
					$this->put('VillageHeadGender'),
					$this->put('VillageHeadLatitude'),
					$this->put('VillageHeadLongitude')
			);
			if($data) $this->response($data, 200);
			else $this->response(array('error' => 'Village could not be found'), 404);
		}
    }

	function villagel_delete() {
        if(!$this->delete('id')) $this->response(NULL, 400);
        $data = $this->mvillage->deleteVillage($this->delete('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Village could not be delete'), 404);
    }
	//**Start Crop Village**//
	function cropls_get(){
        if(!$this->get('id')) $this->response(NULL, 400);
        $data = $this->mvillage->readCrops(
                $this->get('id'),
                $this->get('start'),
                $this->get('limit')
         );
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Crop Village could not be found'), 404);
    }

	function cropl_post() {
        $data = $this->mvillage->createCrop(
                $this->post('CropVillageID'),
                $this->post('CropName'),
                $this->post('CropYear'),
                $this->post('CropFarmers'),
                $this->post('CropHectares'),
                $this->post('CropProduction')
        );
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Crop Village could not be found'), 404);
    }

	function cropl_get() {
        if(!$this->get('id')) $this->response(NULL, 400);
        $data = $this->mvillage->readCrop($this->get('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Crop Village could not be found'), 404);
    }

	function cropl_put() {
		if(!$this->put('VillageCropID')) $this->response(NULL, 400);
        $data = $this->mvillage->updateCrop(
                $this->put('VillageCropID'),
                $this->put('CropVillageID'),
                $this->put('CropName'),
                $this->put('CropYear'),
                $this->put('CropFarmers'),
                $this->put('CropHectares'),
                $this->put('CropProduction')
        );
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Crop Village could not be found'), 404);
    }

	function cropl_delete() {
        if(!$this->delete('id')) $this->response(NULL, 400);
        $data = $this->mvillage->deleteCrop($this->delete('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Crop Village could not be delete'), 404);
    }
	//**End Crop Village**//
	//**Start Infrastructure Village**//
	function infrastructurels_get(){
        if(!$this->get('id')) $this->response(NULL, 400);
        $data = $this->mvillage->readInfrastructures(
                $this->get('id'),
                $this->get('start'),
                $this->get('limit')
         );
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Infrastructure Village could not be found'), 404);
    }

	function infrastructurel_post() {
        $data = $this->mvillage->createInfrastructure(
                $this->post('InfrastructureVillageID'),
                $this->post('InfrastructureType'),
                $this->post('InfrastructureName'),
                $this->post('Latitude'),
                $this->post('Longitude')
        );
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Infrastructure Village could not be found'), 404);
    }

	function infrastructurel_get() {
        if(!$this->get('id')) $this->response(NULL, 400);
        $data = $this->mvillage->readInfrastructure($this->get('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Infrastructure Village could not be found'), 404);
    }

	function infrastructurel_put() {
		if(!$this->put('InfrastructureID')) $this->response(NULL, 400);
        $data = $this->mvillage->updateInfrastructure(
                $this->put('InfrastructureID'),
                $this->put('InfrastructureVillageID'),
                $this->put('InfrastructureType'),
                $this->put('InfrastructureName'),
                $this->put('Latitude'),
                $this->put('Longitude')
        );
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Infrastructure Village could not be found'), 404);
    }

	function infrastructurel_delete() {
        if(!$this->delete('id')) $this->response(NULL, 400);
        $data = $this->mvillage->deleteInfrastructure($this->delete('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Infrastructure Village could not be delete'), 404);
    }


    public function import_get()
    {
        $this->load->model('administration/mregion');

        $file = 'village.xlsx';
        $this->load->library('Excel', null, 'PHPExcel');
        $excel_data = $this->PHPExcel->import($file, false);
        // echo '<pre>'; print_r($excel_data); echo '</pre>'; exit;
        unset($excel_data[0]);
        $result = array();
        $this->db->trans_start(FALSE);
        foreach ($excel_data as $key => $value) {
            // Province
            if($this->mregion->getProvinceDetail($value[6]) == false) {
                $this->mregion->addProvince($value[6],$value[7]);
            }
            // District
            if($this->mregion->getDistrictDetail($value[4]) == false) {
                $this->mregion->addDistrict($value[4],$value[5],$value[6]);
            }
            // Sub District
            if($this->mregion->getSubDistrictDetail($value[2]) == false) {
                $this->mregion->addSubDistrict($value[2],$value[3],$value[4]);
            }
            // Village
            if($this->mregion->getVillageDetail($value[0]) == false) {
                $this->mvillage->getNewVillageID($val[2]);
                $this->mregion->addVillage($value[0],$value[1],$value[2]);
                echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
            }
        }
        $this->db->trans_rollback();
        exit;
        echo '<pre>'; print_r($result); echo '</pre>';
        exit;
    } 
	//**End Infrastructure Village**//
}
