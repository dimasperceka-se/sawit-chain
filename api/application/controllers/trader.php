<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Trader extends REST_Controller {

    public function __construct() {
        $this->file = $_FILES;
        parent::__construct();
        $this->load->model('trade/mtrader');
        $this->load->model('farmer/mfarmer');
    }

    function datas_get() {
        $data = $this->mtrader->readDatas($this->get('key'),$this->get('prov'),$this->get('kab'),$this->get('kec'),
            $this->get('start'),$this->get('limit'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function data_get() {
//        if(!$this->get('id')) $this->response(NULL, 400);
//        $data = $this->mtrader->readData($this->get('id'));
//        if($data) $this->response($data, 200);
//        else $this->response(array('error' => 'data could not be found'), 404);

         if ($this->get('id') == '') {
            $data = $this->mtrader->readDatas($this->get('key'), $this->get('prov'), $this->get('kab'), $this->get('kec'), $this->get('start'), $this->get('limit'));
        } else {
            $data = $this->mtrader->readData($this->get('id'));
        }
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function dataFormNursery_get()
    {
        $data = $this->mtrader->readDataFormNursery($this->get('id'),$this->get('nursery_id'));
        $this->response($data, 200);
//        if ($data)
//            $this->response($data, 200);
//        else
//            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function trans_get() {

       $data = $this->mtrader->readDataNurseryTrans($this->get('id'), $this->get('prov'), $this->get('kab'), $this->get('start'), $this->get('limit'));

        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function data_image_post() {
        if ($this->file['Photo']['name']!='') {
            $gambar = date('Ymdhis').'_'.$this->file['Photo']['name'];
            $upload = move_upload($this->file, 'images/Photo_trader/'.$gambar);
            if (isset($upload['upload_data'])) {
                @unlink('images/Photo_trader/'.$this->post('Photo_old'));
                $result['success'] = true;
                $result['file'] = $gambar;
                $this->response($result, 200);
            }
        }
    }

    function data_post() {
        if(!$this->post('Name')) $this->response(NULL, 400);
        if ($this->file['Photo']['name']!='') {
            $gambar = date('Ymdhis').'_'.$this->file['Photo']['name'];
            move_upload($this->file, 'images/Photo_trader/'.$gambar);
        } else $gambar = $this->post('Photo_old');

        if($this->post('TraderID')=='')
        {
            $data = $this->mtrader->createData($gambar,$_SESSION['userid']);
        } else {
            $data = $this->mtrader->updateData($gambar,$_SESSION['userid']);
        }

        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }

    function transaction_delete() {
        if(!$this->delete('id')) $this->response(NULL, 400);
        $data = $this->mtrader->deleteTransaction($this->delete('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be delete'), 404);
    }

    function transaction_put()
    {
        $data = $this->mtrader->updateDataNurseryTransaction($this->put('id_nursey'),$this->put('Buyer'),$this->put('Volume'),$this->put('Price'),$this->put('DateTransaction'),$_SESSION['userid'],$this->put('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }

    function transaction_post()
    {
        $data = $this->mtrader->createDataNurseryTransaction($_SESSION['userid']);

        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }

    public function nursery_respon_by_type_get(){
        $responsibleType = $this->get('responsibleType');
        $TraderID = $this->get('TraderID');
        $data = $this->mtrader->getNurseryResponByType($responsibleType,$TraderID);
        $this->response($data, 200);
    }

    public function nursery_form_photo_post(){
        if ($this->file['Photo_idtrader']['name'] != '') {
            $gambar = date('Ymdhis') . '_' . $this->file['Photo_idtrader']['name'];
            $upload = move_upload($this->file, 'images/nursery/' . $gambar);
            if (isset($upload['upload_data'])) {
                unlink('images/nursery/' . $this->post('Photo_old_idtrader'));
                $result['success'] = true;
                $result['file']    = $gambar;
                $this->response($result, 200);
            }
        }
    }

    public function nursery_form_photo_responsible_post(){
        if ($this->file['PhotoResponsible_idtrader']['name'] != '') {
            $gambar = date('Ymdhis') . '_' . $this->file['PhotoResponsible_idtrader']['name'];
            $upload = move_upload($this->file, 'images/photo_responsible/' . $gambar);
            if (isset($upload['upload_data'])) {
                unlink('images/photo_responsible/' . $this->post('Photo_old_responsible_idtrader'));
                $result['success'] = true;
                $result['file']    = $gambar;
                $this->response($result, 200);
            }
        }
    }

    function nursery_post() {
        //var proses (begin)
        foreach ($this->post() as $key => $value) {
            if($value == ""){
                $varPro[$key] = null;
            }else{
                $varPro[$key] = $value;
            }
        }
        if($this->post('ResponsibleGender_idtrader') == "") $varPro['ResponsibleGender_idtrader'] = null;
        $varPro['userid'] = $_SESSION['userid'];
        //var proses (end)

        //validasi responsiblenya (begin)
        $valResponsible = true;
        switch ($this->post('ResponsibleType_idtrader')) {
            case 'staff':
                if($this->post('Responsible_idtrader') == "") $valResponsible = false;
            break;
            case 'other':
                if($this->post('ResponsibleName_idtrader') == "" || $this->post('ResponsibleGender_idtrader') == "") $valResponsible = false;
            break;
        }
        if($valResponsible == false){
            $this->response('Responsible information is empty', 400);
        }
        //validasi responsiblenya (end)

        //saring variabel nursery checklist (begin)
        $varPostNurseryChecklist = array();

        $varPostNurseryChecklist['LocationCloseToCommunityNo'] = ($this->post('LocationCloseToCommunityNo') == '') ? null : $this->post('LocationCloseToCommunityNo');
        $varPostNurseryChecklist['LocationCloseToCommunity'] = ($this->post('LocationCloseToCommunity') == '') ? null : $this->post('LocationCloseToCommunity');
        $varPostNurseryChecklist['GoodLandAreaNo'] = ($this->post('GoodLandAreaNo') == '') ? null : $this->post('GoodLandAreaNo');
        $varPostNurseryChecklist['GoodLandArea'] = ($this->post('GoodLandArea') == '') ? null : $this->post('GoodLandArea');
        $varPostNurseryChecklist['LocationNearCocoaFarmNo'] = ($this->post('LocationNearCocoaFarmNo') == '') ? null : $this->post('LocationNearCocoaFarmNo');
        $varPostNurseryChecklist['LocationNearCocoaFarm'] = ($this->post('LocationNearCocoaFarm') == '') ? null : $this->post('LocationNearCocoaFarm');
        $varPostNurseryChecklist['ContinuousWaterSupplyNo'] = ($this->post('ContinuousWaterSupplyNo') == '') ? null : $this->post('ContinuousWaterSupplyNo');
        $varPostNurseryChecklist['ContinuousWaterSupply'] = ($this->post('ContinuousWaterSupply') == '') ? null : $this->post('ContinuousWaterSupply');
        $varPostNurseryChecklist['IrrigationInstalledNo'] = ($this->post('IrrigationInstalledNo') == '') ? null : $this->post('IrrigationInstalledNo');
        $varPostNurseryChecklist['IrrigationInstalled'] = ($this->post('IrrigationInstalled') == '') ? null : $this->post('IrrigationInstalled');
        $varPostNurseryChecklist['UseShadingNetNo'] = ($this->post('UseShadingNetNo') == '') ? null : $this->post('UseShadingNetNo');
        $varPostNurseryChecklist['UseShadingNet'] = ($this->post('UseShadingNet') == '') ? null : $this->post('UseShadingNet');
        $varPostNurseryChecklist['AdequateSupplyTopSoilNo'] = ($this->post('AdequateSupplyTopSoilNo') == '') ? null : $this->post('AdequateSupplyTopSoilNo');
        $varPostNurseryChecklist['AdequateSupplyTopSoil'] = ($this->post('AdequateSupplyTopSoil') == '') ? null : $this->post('AdequateSupplyTopSoil');
        $varPostNurseryChecklist['ImprovedVarietyNo'] = ($this->post('ImprovedVarietyNo') == '') ? null : $this->post('ImprovedVarietyNo');
        $varPostNurseryChecklist['ImprovedVariety'] = ($this->post('ImprovedVariety') == '') ? null : $this->post('ImprovedVariety');
        $varPostNurseryChecklist['ConstructStoringNo'] = ($this->post('ConstructStoringNo') == '') ? null : $this->post('ConstructStoringNo');
        $varPostNurseryChecklist['ConstructStoring'] = ($this->post('ConstructStoring') == '') ? null : $this->post('ConstructStoring');
        $varPostNurseryChecklist['CorrectEquipmentNo'] = ($this->post('CorrectEquipmentNo') == '') ? null : $this->post('CorrectEquipmentNo');
        $varPostNurseryChecklist['CorrectEquipment'] = ($this->post('CorrectEquipment') == '') ? null : $this->post('CorrectEquipment');
        $varPostNurseryChecklist['WindBreakInstalledNo'] = ($this->post('WindBreakInstalledNo') == '') ? null : $this->post('WindBreakInstalledNo');
        $varPostNurseryChecklist['WindBreakInstalled'] = ($this->post('WindBreakInstalled') == '') ? null : $this->post('WindBreakInstalled');
        $varPostNurseryChecklist['SecurityFenceInstalledNo'] = ($this->post('SecurityFenceInstalledNo') == '') ? null : $this->post('SecurityFenceInstalledNo');
        $varPostNurseryChecklist['SecurityFenceInstalled'] = ($this->post('SecurityFenceInstalled') == '') ? null : $this->post('SecurityFenceInstalled');
        $varPostNurseryChecklist['FertilizerUsedNo'] = ($this->post('FertilizerUsedNo') == '') ? null : $this->post('FertilizerUsedNo');
        $varPostNurseryChecklist['FertilizerUsed'] = ($this->post('FertilizerUsed') == '') ? null : $this->post('FertilizerUsed');
        $varPostNurseryChecklist['OperatorAdequateTrainingNo'] = ($this->post('OperatorAdequateTrainingNo') == '') ? null : $this->post('OperatorAdequateTrainingNo');
        $varPostNurseryChecklist['OperatorAdequateTraining'] = ($this->post('OperatorAdequateTraining') == '') ? null : $this->post('OperatorAdequateTraining');
        $varPostNurseryChecklist['AdequateFacilityNo'] = ($this->post('AdequateFacilityNo') == '') ? null : $this->post('AdequateFacilityNo');
        $varPostNurseryChecklist['AdequateFacility'] = ($this->post('AdequateFacility') == '') ? null : $this->post('AdequateFacility');
        $varPostNurseryChecklist['SustainablePestDiseaseNo'] = ($this->post('SustainablePestDiseaseNo') == '') ? null : $this->post('SustainablePestDiseaseNo');
        $varPostNurseryChecklist['SustainablePestDisease'] = ($this->post('SustainablePestDisease') == '') ? null : $this->post('SustainablePestDisease');
        $varPostNurseryChecklist['CloneGradingNo'] = ($this->post('CloneGradingNo') == '') ? null : $this->post('CloneGradingNo');
        $varPostNurseryChecklist['CloneGrading'] = ($this->post('CloneGrading') == '') ? null : $this->post('CloneGrading');
        $varPostNurseryChecklist['SeedlingCullingDoneNo'] = ($this->post('SeedlingCullingDoneNo') == '') ? null : $this->post('SeedlingCullingDoneNo');
        $varPostNurseryChecklist['SeedlingCullingDone'] = ($this->post('SeedlingCullingDone') == '') ? null : $this->post('SeedlingCullingDone');
        $varPostNurseryChecklist['ProperInputSalesRecordNo'] = ($this->post('ProperInputSalesRecordNo') == '') ? null : $this->post('ProperInputSalesRecordNo');
        $varPostNurseryChecklist['ProperInputSalesRecord'] = ($this->post('ProperInputSalesRecord') == '') ? null : $this->post('ProperInputSalesRecord');
        $varPostNurseryChecklist['SeedsPreGerminatedNo'] = ($this->post('SeedsPreGerminatedNo') == '') ? null : $this->post('SeedsPreGerminatedNo');
        $varPostNurseryChecklist['SeedsPreGerminated'] = ($this->post('SeedsPreGerminated') == '') ? null : $this->post('SeedsPreGerminated');
        //saring variabel nursery checklist (end)


        if($this->post('NurseryID')=='')
        {
            //cek nursery nr apakah sudah ada
            $cek = $this->mtrader->checkNurseryNr($varPro);
            if($cek == false){
                $this->response('NurseryNr already existed!', 400);
            }
//
            $data = $this->mtrader->createDataNursery($varPro,$varPostNurseryChecklist);
        } else {
        	$data = $this->mtrader->updateDataNursery($varPro,$varPostNurseryChecklist);
        }

        if($data)
            $this->response($data, 200);
        else
            $this->response('Process Failed!', 400);
    }

    function datau_post() {
        if(!$this->post('TraderID')) $this->response(NULL, 400);
        if ($this->file['Photo']['name']!='') {
            $gambar = date('Ymdhis').'_'.$this->file['Photo']['name'];
            move_upload($this->file, 'images/Photo/'.$gambar);
        } else $gambar = $this->post('Photo_old');
        $data = $this->mtrader->updateData($this->post('Address'),$this->post('Handphone'),$this->post('NoTelp'),$this->post('Desa'),$this->post('Company'),
            $this->post('CompanyStatus'),$this->post('CompanyYear'),$this->post('CompanyAlias'),$this->post('PermanentEmployeeMale'),
            $this->post('PermanentEmployeeFemale'),$this->post('TemporaryEmployeeMale'),
            $this->post('TemporaryEmployeeFemale'),$this->post('LatDeg'),
            $this->post('LatMin'),$this->post('LatSec'),$this->post('LongDeg'),$this->post('LongMin'),
            $this->post('LongSec'),$this->post('Elevation'),$gambar,$_SESSION['userid'],$this->post('TraderID'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }

    function data_delete() {
        if(!$this->delete('id')) $this->response(NULL, 400);
        $data = $this->mtrader->deleteData($this->delete('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be delete'), 404);
    }

   //staff
    function staff_get() {
        $data = $this->mtrader->readStaffs($this->get('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }
    function staff_post() {
        if(!$this->post('StaffName')) $this->response(NULL, 400);
        $data = $this->mtrader->createStaff($this->post('TraderID'),$this->post('StaffName'),$this->post('PrivateCellphone'),
            $this->post('PrivateStaffEmail'),$this->post('StaffBirth'),$this->post('StaffGender'),
            $this->post('IdentityNumber'),$this->post('Education'),
            $this->post('FamilyMembers'),$this->post('Address'),$this->post('Position'),$_SESSION['userid']);
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }

    function staff_put() {
        if(!$this->put('StaffName')) $this->response(NULL, 400);
        $data = $this->mtrader->updateStaff($this->put('TraderID'),$this->put('StaffName'),$this->put('PrivateCellphone'),
            $this->put('PrivateStaffEmail'),$this->put('StaffBirth'),
            $this->put('StaffGender')!=$this->put('StaffGende')?$this->put('StaffGende'):$this->put('StaffGender'),
            $this->put('IdentityNumber'),
            $this->put('Education')!=$this->put('Educatio')?$this->put('Educatio'):$this->put('Education'),
            $this->put('FamilyMembers'),$this->put('Address'),$this->put('Position'),$_SESSION['userid'],$this->put('TraderStaffID'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }

    function staff_delete() {
        if(!$this->delete('id')) $this->response(NULL, 400);
        $data = $this->mtrader->deleteStaff($this->delete('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be delete'), 404);
    }
   //end staff

   //quality standard
    function quality_standards_get() {
        $data = $this->mtrader->readQualityStandards($this->get('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }
    function quality_standard_get() {
        $data = $this->mtrader->readQualityStandard($this->get('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }
    function quality_standard_combo_get() {
        $data = $this->mtrader->readQualityStandardCombos($this->get('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }
    function quality_standard_post() {
        if(!$this->post('StandardName')) $this->response(NULL, 400);
        $data = $this->mtrader->createQualityStandard($this->post('StandardTraderID'),$this->post('StandardName'),$this->post('Moisture'),
            $this->post('BeanCount'),$this->post('Waste'),$this->post('Mouldy'),$this->post('Insect'),$this->post('Slaty'),
            $_SESSION['userid']);
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }

    function quality_standard_put() {
        if(!$this->put('StandardName')) $this->response(NULL, 400);
        $data = $this->mtrader->updateQualityStandard($this->put('StandardTraderID'),$this->put('StandardName'),$this->put('Moisture'),
            $this->put('BeanCount'),$this->put('Waste'),$this->put('Mouldy'),$this->put('Insect'),$this->put('Slaty'),
            $_SESSION['userid'],$this->put('StandardID'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }

    function quality_standard_delete() {
        if(!$this->delete('id')) $this->response(NULL, 400);
        $data = $this->mtrader->deleteQualityStandard($this->delete('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be delete'), 404);
    }
   //end quality

   //quality
    function qualitys_get() {
        $data = $this->mtrader->readQualitys($this->get('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }
    function quality_post() {
        if(!$this->post('QualityDate')) $this->response(NULL, 400);
        $data = $this->mtrader->createQuality($this->post('QualityTraderID'),$this->post('QualityDate'),$this->post('StandardID'),$this->post('Moisture'),
            $this->post('BeanCount'),$this->post('Waste'),$this->post('Mouldy'),$this->post('Insect'),$this->post('Slaty'),
            $_SESSION['userid']);
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }

    function quality_put() {
        if(!$this->put('QualityDate')) $this->response(NULL, 400);
        $data = $this->mtrader->updateQuality($this->put('QualityTraderID'),$this->put('QualityDate'),$this->put('StandardID'),$this->put('Moisture'),
            $this->put('BeanCount'),$this->put('Waste'),$this->put('Mouldy'),$this->put('Insect'),$this->put('Slaty'),
            $_SESSION['userid'],$this->put('QualityID'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }

    function quality_delete() {
        if(!$this->delete('id')) $this->response(NULL, 400);
        $data = $this->mtrader->deleteQuality($this->delete('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be delete'), 404);
    }
   //end quality

   //price
    function prices_get() {
        $data = $this->mtrader->readPrices($this->get('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }
    function price_post() {
        if(!$this->post('PriceDate')) $this->response(NULL, 400);
        $data = $this->mtrader->createPrice($this->post('PriceTraderID'),$this->post('PriceDate'),$this->post('Price'),
            $this->post('District'),$_SESSION['userid']);
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }
    function price_put() {
        if(!$this->put('PriceDate')) $this->response(NULL, 400);
        $data = $this->mtrader->updatePrice($this->put('PriceTraderID'),$this->put('PriceDate'),$this->put('Price'),
            $this->put('District'),$_SESSION['userid'],$this->put('PriceID'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }

    function price_delete() {
        if(!$this->delete('id')) $this->response(NULL, 400);
        $data = $this->mtrader->deletePrice($this->delete('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be delete'), 404);
    }
   //end price

   //package
    function packages_get() {
        $data = $this->mtrader->readPackages($this->get('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }
    function package_post() {
        if(!$this->post('PackageType')) $this->response(NULL, 400);
        $data = $this->mtrader->createPackage($this->post('PackageTraderID'),$this->post('PackageType'),$this->post('PackageWeight'),
            $_SESSION['userid']);
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }

    function package_put() {
        if(!$this->put('PackageType')) $this->response(NULL, 400);
        $data = $this->mtrader->updatePackage($this->put('PackageTraderID'),$this->put('PackageType'),$this->put('PackageWeight'),
            $_SESSION['userid'],$this->put('PackageID'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }

    function package_delete() {
        if(!$this->delete('id')) $this->response(NULL, 400);
        $data = $this->mtrader->deletePackage($this->delete('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be delete'), 404);
    }
   //end package

    function cetak_get($id,$partner,$jenis="") {
         if ($jenis=='Form%20Hasil' OR $jenis=="") {
            $data['data'] = $this->mtrader->readData($id);
            $data['detail'] = $this->mtrader->readStaffs($id);
         }
         $data['logo'] = $this->mfarmer->readPartnerLogo('1',$partner);
         $this->load->view('cetak_trader', $data);
    }

    public function import_get()
    {
        $file = 'trader.xls';
        $this->load->library('Excel', null, 'PHPExcel');
        $excel_data = $this->PHPExcel->import($file, false);
        unset($excel_data[0]);
        $result = array();
        foreach ($excel_data as $key => $value) {
            $this->mtrader->updateLocation($value[2], $value[1], $value[0]);
            echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
            // $result[] = $this->db->last_query();
        }
        exit;
        echo '<pre>'; print_r($result); echo '</pre>';
        exit;
    }

    public function trader_list_get()
    {
        $province = $this->get('ProvinceID');
        $district = $this->get('DistrictID');
        $subdistrict = $this->get('SubDistrictID');
        $data = $this->mtrader->listTrader($province, $district, $subdistrict);
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any sce!'), 200);
    }

    public function nursery_list_get(){
    	$data = $this->mtrader->readDataNurseyNumbers($this->get('ObjType'),$this->get('ObjID'));
    	if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any Transactions!'), 404);
    }

    function nursery_delete() {
        if (!$this->delete('NurseryID'))
            $this->response(NULL, 400);
        $nursery = $this->mtrader->deleteNursery($this->delete('NurseryID'));
        if ($nursery)
            $this->response($nursery, 200);
        else
            $this->response(array('error' => 'Nursery could not be found'), 404);
    }

    function nursery_polygon_get($type=''){
        $data['nursery_polygon'] = site_url() . '/trader/nursery_polygon';
        $data['nursery_polygon_center'] = site_url() . '/trader/nursery_polygon_center';
        $data['NurseryID'] = $this->get('NurseryID');
        $data['latitude'] = $this->get('lati');
        $data['longitude'] = $this->get('longi');
        $data['NurseryNr'] = $this->get('NurseryNr');
        $data['hakAksesPolygon'] = $this->get('hakAksesPolygon');

        $data['area'] = $this->mtrader->getNurseryPolygon($this->get('NurseryID'),$this->get('NurseryNr'),$this->get('lati'),$this->get('longi'));

        //echo "<pre>".print_r($data['area'],1);exit;
        if($type=='trader'){
            $view = 'nursery_map_polygon_trader';
        }else if($type=='koperasi'){
            $view = 'nursery_map_polygon_coop';
        }
        $this->load->view($view, @$data);
    }

    function nursery_polygon_put(){
        if (!$this->put('nursery_id'))
            $this->response(NULL, 400);
        $nursery_polygon = $this->mtrader->updateNurseryPolygon($this->put('nursery_id'), $this->put('nursery_nr'), $this->put('area'), $this->put('lat'), $this->put('long'));
        if ($nursery_polygon)
            $this->response($nursery_polygon, 200);
        else
            $this->response(array('error' => 'Map Polygon could not be found'), 404);
    }

    function nursery_polygon_area_get(){
    	$data = $this->mtrader->readNurseryArea($this->get('ObjType'),$this->get('ObjID'),$this->get('NurseryID'),$this->get('NurseryNr'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function nursery_polygon_center_put(){
        if (!$this->put('nursery_id'))
            $this->response(NULL, 400);
        $clonal_polygon = $this->mtrader->updateNuseryPolygonCenter($this->put('nursery_id'), $this->put('nursery_nr'), $this->put('lat'), $this->put('long'));
        if ($clonal_polygon)
            $this->response($clonal_polygon, 200);
        else
            $this->response(array('error' => 'Map Polygon could not be found'), 404);
    }


    public function Provinsis_get() {
        $data = $this->mtrader->readProvinsis($this->get('key'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function Kabupatens_get() {
        $data = $this->mtrader->readKabupatens($this->get('key'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function Kecamatans_get() {
        $data = $this->mtrader->readKecamatans($this->get('key'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function Desas_get() {
        $data = $this->mtrader->readDesas($this->get('key'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function comboSurveyYear_get(){
        $yearNya = date('Y');
        $data = array();
        $incre = 0;

        for ($i = ($yearNya+5); $i >= ($yearNya - 20); $i--) {
            $data[$incre]['id'] = $i;
            $data[$incre]['label'] = $i;
            $incre++;
        }

        $this->response($data, 200);
    }

    public function traderSurList_get(){
        $data = $this->mtrader->getTraderSurList($this->get('TraderID'));
        $this->response($data, 200);
    }

    public function traderSurveyGetForm_get(){
        $data = $this->mtrader->getFormTraderSurvey($this->get('TraderSurID'));
        $this->response($data, 200);
    }

    public function traderSurvey_post(){
        $post = $this->post();

        //replace datanya jika ada koma (begin)
        $post['tSurLatitude'] = str_replace(",", "", $post['tSurLatitude']);
        $post['tSurLongitude'] = str_replace(",", "", $post['tSurLongitude']);
        $post['tSurFundValueFromSelf'] = str_replace(",", "", $post['tSurFundValueFromSelf']);
        $post['tSurFundValueFromLoan'] = str_replace(",", "", $post['tSurFundValueFromLoan']);
        $post['tSurAverageMarginCacaoPerKgReceivedMin'] = str_replace(",", "", $post['tSurAverageMarginCacaoPerKgReceivedMin']);
        $post['tSurAverageMarginCacaoPerKgReceivedMax'] = str_replace(",", "", $post['tSurAverageMarginCacaoPerKgReceivedMax']);
        $post['tSurCacaoLandSize'] = str_replace(",", "", $post['tSurCacaoLandSize']);
        $post['tSurAverageProduction'] = str_replace(",", "", $post['tSurAverageProduction']);
        $post['tSurExCacaoLandSize'] = str_replace(",", "", $post['tSurExCacaoLandSize']);
        $post['tSurExAverageProduction'] = str_replace(",", "", $post['tSurExAverageProduction']);
        $post['tSurLoanCreditCount'] = str_replace(",", "", $post['tSurLoanCreditCount']);
        $post['tSurLoanCreditValueTotal'] = str_replace(",", "", $post['tSurLoanCreditValueTotal']);
        $post['tSurFundValue'] = str_replace(",", "", $post['tSurFundValue']);
        $post['tSurLastLoanValue'] = str_replace(",", "", $post['tSurLastLoanValue']);
        $post['tSurLastLoanCreditValue'] = str_replace(",", "", $post['tSurLastLoanCreditValue']);

        $post['tSurYearRunningTrader'] = str_replace(",", "", $post['tSurYearRunningTrader']);
        $post['tSurNrFulltimeStaffFemale'] = str_replace(",", "", $post['tSurNrFulltimeStaffFemale']);
        $post['tSurNrFulltimeStaffMale'] = str_replace(",", "", $post['tSurNrFulltimeStaffMale']);
        $post['tSurNrParttimeStaffFemale'] = str_replace(",", "", $post['tSurNrParttimeStaffFemale']);
        $post['tSurNrParttimeStaffMale'] = str_replace(",", "", $post['tSurNrParttimeStaffMale']);
        $post['tSurComodityCacaoSalePercentage'] = str_replace(",", "", $post['tSurComodityCacaoSalePercentage']);
        $post['tSurComodityOtherSalePercentage'] = str_replace(",", "", $post['tSurComodityOtherSalePercentage']);

        $post['tSurNrCacaoFrequentBuyer'] = str_replace(",", "", $post['tSurNrCacaoFrequentBuyer']);
        $post['tSurNrCacaoNormalBuyer'] = str_replace(",", "", $post['tSurNrCacaoNormalBuyer']);

        $post['tSurNrTransWetBeansHighHarvest'] = str_replace(",","",$post['tSurNrTransWetBeansHighHarvest']);
        $post['tSurNrVolumeWetBeansHighHarvest'] = str_replace(",","",$post['tSurNrVolumeWetBeansHighHarvest']);
        $post['tSurNrTransWetBeansNormalHarvest'] = str_replace(",","",$post['tSurNrTransWetBeansNormalHarvest']);
        $post['tSurNrVolumeWetBeansNormalHarvest'] = str_replace(",","",$post['tSurNrVolumeWetBeansNormalHarvest']);
        $post['tSurNrTransWetBeansLowHarvest'] = str_replace(",","",$post['tSurNrTransWetBeansLowHarvest']);
        $post['tSurNrVolumeWetBeansLowHarvest'] = str_replace(",","",$post['tSurNrVolumeWetBeansLowHarvest']);
        $post['tSurNrTransFermentBeansHighHarvest'] = str_replace(",","",$post['tSurNrTransFermentBeansHighHarvest']);
        $post['tSurNrVolumeFermentBeansHighHarvest'] = str_replace(",","",$post['tSurNrVolumeFermentBeansHighHarvest']);
        $post['tSurNrTransFermentBeansNormalHarvest'] = str_replace(",","",$post['tSurNrTransFermentBeansNormalHarvest']);
        $post['tSurNrVolumeFermentBeansNormalHarvest'] = str_replace(",","",$post['tSurNrVolumeFermentBeansNormalHarvest']);
        $post['tSurNrTransFermentBeansLowHarvest'] = str_replace(",","",$post['tSurNrTransFermentBeansLowHarvest']);
        $post['tSurNrVolumeFermentBeansLowHarvest'] = str_replace(",","",$post['tSurNrVolumeFermentBeansLowHarvest']);
        $post['tSurNrTransDryBeansHighHarvest'] = str_replace(",","",$post['tSurNrTransDryBeansHighHarvest']);
        $post['tSurNrVolumeDryBeansHighHarvest'] = str_replace(",","",$post['tSurNrVolumeDryBeansHighHarvest']);
        $post['tSurNrTransDryBeansNormalHarvest'] = str_replace(",","",$post['tSurNrTransDryBeansNormalHarvest']);
        $post['tSurNrVolumeDryBeansNormalHarvest'] = str_replace(",","",$post['tSurNrVolumeDryBeansNormalHarvest']);
        $post['tSurNrTransDryBeansLowHarvest'] = str_replace(",","",$post['tSurNrTransDryBeansLowHarvest']);
        $post['tSurNrVolumeDryBeansLowHarvest'] = str_replace(",","",$post['tSurNrVolumeDryBeansLowHarvest']);
        //replace datanya jika ada koma (end)

        if($post['tSurTraderSurID'] == ""){
            $proses = $this->mtrader->insertTraderSurvey($post);
        }else{
            $proses = $this->mtrader->updateTraderSurvey($post);
        }
        $this->response($proses, 200);
    }

    public function traderSurvey_delete(){
        $proses = $this->mtrader->deleteTraderSurvey($this->delete('TraderSurID'));
        $this->response($proses, 200);
    }

}