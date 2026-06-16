<?php
/**
 * @Author: nikolius
 * @Date:   2016-08-19 17:21:46
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Prog_sce extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->file = $_FILES;
        $this->load->model('prog_sce/mprofile');
        $this->load->model('prog_sce/mcompost');
        $this->load->model('prog_sce/mnursery');
        $this->load->model('prog_sce/mclonal');
    }

    public function profile_get(){
        $sce_id = getSceID();
        if($sce_id == ""){
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
        }else{
            $data = $this->mprofile->farmerSceById($sce_id);
            if($data['id'] != ""){
                $this->response($data, 200);
            }else{
                $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
            }
        }
    }

    public function profile_staff_get(){
        $sce_id = getSceID();
        $data = $this->mprofile->getFarmerSceStaff($sce_id);
        $this->response($data, 200);
    }

    public function compost_get(){
        $sce_id = getSceID();
        if($sce_id == ""){
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
        }else{
            $data = $this->mcompost->getFarmerCompostBySceId($sce_id);
            $this->response($data, 200);
        }
    }

    public function input_compost_post(){
        //insert
        $sce_id = getSceID();
        if($sce_id == ""){
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
        }else{
            $paramInsert = array(
                "SceID" => getSceID(),
                "Established" => $this->post('Established'),
                "MesinChooper" => $this->post('MesinChooper'),
                "RumahKompos" => $this->post('RumahKompos'),
                "CompostLatitude" => $this->post('CompostLatitude'),
                "CompostLongitude" => $this->post('CompostLongitude')
            );

            $CompostID = $this->mcompost->insertCompostFarmer($paramInsert);
            $data = array();
            if($CompostID == "") {
                $data['success'] = false;
                $data['prosesSave'] = "0";
            }else{
                $data['success'] = true;
                $data['prosesSave'] = "1";
                $data['CompostID'] = $CompostID;
            }
            $this->response($data, 200);
        }
    }

    public function input_compost_put(){
        //update
        $paramUpdate = array(
            "CompostID" => $this->put('CompostID'),
            "Established" => $this->put('Established'),
            "MesinChooper" => $this->put('MesinChooper'),
            "RumahKompos" => $this->put('RumahKompos'),
            "CompostLatitude" => $this->put('CompostLatitude'),
            "CompostLongitude" => $this->put('CompostLongitude')
        );

        $proses = $this->mcompost->updateCompostFarmer($paramUpdate);
        if($proses == false) {
            $data['success'] = false;
            $data['prosesSave'] = "0";
        }else{
            $data['success'] = true;
            $data['prosesSave'] = "1";
            $data['CompostID'] = $this->put('CompostID');
        }
        $this->response($data, 200);
    }

    public function compost_trans_get(){
        $data = $this->mcompost->getCompostTrans($this->get('compost_id'),$this->get('start'),$this->get('limit'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any RegionIDs!'), 404);
    }

    public function input_compost_trans_post(){
        //insert
        $paramInsert = array(
            "CompostID" => $this->post('id_compost'),
            "Buyer" => $this->post('Buyer'),
            "Volume" => $this->post('Volume'),
            "Price" => $this->post('Price'),
            "DateTransaction" => $this->post('DateTransaction'),
            "userid" => $_SESSION['userid']
        );
        $cpg = $this->mcompost->insertCompostTransFarmer($paramInsert);

        if ($cpg)
            $this->response($cpg, 200);
        else
            $this->response(array('error' => 'Cpg could not be found'), 404);
    }

    public function input_compost_trans_put(){
        //update
        if (!$this->put('id'))
            $this->response(NULL, 400);

        $paramUpdate = array(
            "CompostID" => $this->put('id_compost'),
            "Buyer" => $this->put('Buyer'),
            "Volume" => $this->put('Volume'),
            "Price" => $this->put('Price'),
            "DateTransaction" => $this->put('DateTransaction'),
            "userid" => $_SESSION['userid'],
            "id" => $this->put('id')
        );
        $cpg = $this->mcompost->updateCompostTransFarmer($paramUpdate);

        if ($cpg)
            $this->response($cpg, 200);
        else
            $this->response(array('error' => 'Cpg could not be found'), 404);
    }

    public function input_compost_trans_delete(){
        if (!$this->delete('id'))
            $this->response(NULL, 400);
        $cpg = $this->mcompost->deleteCompostTransFarmer($this->delete('id'));
        if ($cpg)
            $this->response($cpg, 200);
        else
            $this->response(array('error' => 'Cpg could not be found'), 404);
    }

    //===== NURSERY (BEGIN) ========================================================//
    public function nurserynr_combo_get(){
        $sce_id = getSceID();
        $data = $this->mnursery->getNurseryNrCombo($sce_id);
        $data = array_merge(array(array('id' => '-1', 'label' => '[Add New Nursery]')), $data);
        $this->response($data, 200);
    }

    public function nursery_by_id_get(){
        $sce_id = getSceID();
        $data = $this->mnursery->getNurseryById($sce_id,$this->get('NurseryID'));
        if($data){
            $this->response($data, 200);
        }else{
            $this->response(array('error' => 'Data not found'), 404);
        }
    }

    public function nursery_respon_by_type_get(){
        $responsibleType = $this->get('responsibleType');
        $SceID = $this->get('SceID');
        $data = $this->mnursery->getResponByType($responsibleType,$SceID);
        $this->response($data, 200);
    }

    public function nursery_photo_post(){
        if ($this->file['Photo']['name'] != '') {
            $gambar = date('Ymdhis') . '_' . $this->file['Photo']['name'];
            $upload = move_upload($this->file, 'images/nursery/' . $gambar);
            if (isset($upload['upload_data'])) {
                unlink('images/nursery/' . $this->post('Photo_old'));
                $result['success'] = true;
                $result['file']    = $gambar;
                $this->response($result, 200);
            }
        }
    }

    public function nursery_form_photo_responsible_post(){
        if ($this->file['PhotoResponsible']['name'] != '') {
            $gambar = date('Ymdhis') . '_' . $this->file['PhotoResponsible']['name'];
            $upload = move_upload($this->file, 'images/photo_responsible/' . $gambar);
            if (isset($upload['upload_data'])) {
                unlink('images/photo_responsible/' . $this->post('Photo_old_responsible'));
                $result['success'] = true;
                $result['file']    = $gambar;
                $this->response($result, 200);
            }
        }
    }

    public function nursery_post(){
        if($this->post('NursCertBp2YaTidak') == "") $NursCertBp2YaTidak = ""; else $NursCertBp2YaTidak = $this->post('NursCertBp2YaTidak');
        if($this->post('tglCertificate') == "") $tglCertificate = ""; else $tglCertificate = $this->post('tglCertificate');
        if($this->post('DateAppliedCertification') == "") $DateAppliedCertification = ""; else $DateAppliedCertification = $this->post('DateAppliedCertification');

        //var proses (begin)
        foreach ($this->post() as $key => $value) {
            switch ($key) {
                case 'Panjang':
                case 'Lebar':
                case 'Luas':
                case 'Kapasitas':
                    $value = str_replace(",","",$value);
                break;
            }

            if($value == ""){
                $varPro[$key] = null;
            }else{
                $varPro[$key] = $value;
            }
        }
        if($this->post('ResponsibleGender') == "") $varPro['ResponsibleGender'] = null;
        $varPro['userid'] = $_SESSION['userid'];
        $varPro['SceID'] = getSceID();
        //var proses (end)

        //validasi responsiblenya (begin)
        $valResponsible = true;
        switch ($this->post('ResponsibleType')) {
            case 'farmer':
            case 'staff':
                if($this->post('Responsible') == "") $valResponsible = false;
            break;
            case 'other':
                if($this->post('ResponsibleName') == "" || $this->post('ResponsibleGender') == "") $valResponsible = false;
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

        if($this->post('NurseryID') == ""){
            $proses = $this->mnursery->createNursery($varPro,$varPostNurseryChecklist);
        }else{
            //update
            $proses = $this->mnursery->updateNursery($varPro,$varPostNurseryChecklist);
        }

        if ($proses['success']) {
            $this->response($proses, 200);
        } else {
            $this->response(array('error' => 'Process not found'), 404);
        }
    }

    public function nursery_delete(){
        $proses = $this->mnursery->deleteNursery($this->delete('NurseryID'));
        $this->response($proses, 200);
    }

    public function nursery_penjualan_get(){
        $data = $this->mnursery->getNurseryPenjualan($this->get('NurseryID'),$this->get('start'),$this->get('limit'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any data'), 404);
    }

    public function nursery_penjualan_post(){
        //insert
        $paramInsert = array(
            "NurseryID" => $this->post("NurseryID"),
            "Buyer" => $this->post("Buyer"),
            "Volume" => $this->post("Volume"),
            "Price" => $this->post("Price"),
            "Total" => $this->post("Total"),
            "DateTransaction" => $this->post("DateTransaction"),
            "CloneTypeName" => $this->post("CloneTypeName"),
            "CloneTypeID" => $this->post("CloneTypeID")
        );
        $proses = $this->mnursery->insertNurseryPenjualan($paramInsert);
        $this->response($proses, 200);
    }

    public function nursery_penjualan_put(){
        //insert
        $paramUpdate = array(
            "id" => $this->put("id"),
            "Buyer" => $this->put("Buyer"),
            "Volume" => $this->put("Volume"),
            "Price" => $this->put("Price"),
            "Total" => $this->put("Total"),
            "DateTransaction" => $this->put("DateTransaction"),
            "CloneTypeName" => $this->put("CloneTypeName"),
            "CloneTypeID" => $this->put("CloneTypeID")
        );
        $proses = $this->mnursery->updateNurseryPenjualan($paramUpdate);
        $this->response($proses, 200);
    }

    public function nursery_penjualan_delete(){
        $proses = $this->mnursery->deleteNurseryPenjualan((int) $this->delete('id'));
        $this->response($proses, 200);
    }

    public function nursery_monitoring_get(){
        $data = $this->mnursery->getNurseryMonitoring($this->get('NurseryID'),$this->get('start'),$this->get('limit'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any data'), 404);
    }

    public function nursery_monitoring_post(){
        //insert
        $paramInsert = array(
            "NurseryID" => $this->post("NurseryID"),
            "MonitoringDate" => $this->post("MonitoringDate"),
            "MonitoringStatus" => $this->post("MonitoringStatus"),
            "Description" => $this->post("Description")
        );
        $proses = $this->mnursery->insertNurseryMonitoring($paramInsert);
        $this->response($proses, 200);
    }

    public function nursery_monitoring_put(){
        //insert
        $paramUpdate = array(
            "id" => $this->put("id"),
            "MonitoringDate" => $this->put("MonitoringDate"),
            "MonitoringStatus" => $this->put("MonitoringStatus"),
            "Description" => $this->put("Description")
        );
        $proses = $this->mnursery->updateNurseryMonitoring($paramUpdate);
        $this->response($proses, 200);
    }

    public function nursery_monitoring_delete(){
        $proses = $this->mnursery->deleteNurseryMonitoring((int) $this->delete('id'));
        $this->response($proses, 200);
    }

    public function nursery_polygon_get(){
        $data['nursery_polygon'] = site_url() . '/prog_sce/nursery_polygon';
        $data['nursery_polygon_center'] = site_url() . '/prog_sce/nursery_polygon_center';
        $data['NurseryID'] = $this->get('NurseryID');
        $data['NurseryNr'] = $this->get('NurseryNr');
        $data['latitude'] = $this->get('lati');
        $data['longitude'] = $this->get('longi');
        $data['hakAksesPolygon'] = $this->get('hakAksesPolygon');

        //get polygon area
        $data['area'] = $this->mnursery->getNurseryPolygonArea($data['NurseryID'],$data['latitude'],$data['longitude']);
        $this->load->view('nursery_map_sce', @$data);
    }

    public function nursery_polygon_put(){
        if (!$this->put('NurseryID'))
            $this->response(NULL, 400);

        $nursery_polygon = $this->mnursery->updateNurseryPolygon($this->put('NurseryID'), $this->put('NurseryNr'), $this->put('area'), $this->put('luas'), $this->put('lat'), $this->put('long'));
        if ($nursery_polygon)
            $this->response($nursery_polygon, 200);
        else
            $this->response(array('error' => 'Map Polygon could not be found'), 404);
    }

    public function nursery_polygon_center_put(){
        if (!$this->put('NurseryID'))
            $this->response(NULL, 400);

        $nursery_polygon = $this->mnursery->updateNurseryPolygonCenter($this->put('NurseryID'), $this->put('NurseryNr'), $this->put('lat'), $this->put('long'));
        if ($nursery_polygon)
            $this->response($nursery_polygon, 200);
        else
            $this->response(array('error' => 'Map Polygon could not be found'), 404);
    }

    public function update_nursery_area_get(){
        $data = $this->mnursery->updateNurseryAreaGet($this->get('NurseryID'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    //===== NURSERY (END) ========================================================//

    //===== CLONAL GARDEN (BEGIN) ========================================================//
    public function gardennr_combo_get(){
        $sce_id = getSceID();
        $data = $this->mclonal->getGardenNrCombo($sce_id);
        $data = array_merge(array(array('id' => '-1', 'label' => '[Select Garden Nr]')), $data);
        $this->response($data, 200);
    }

    public function clonal_get_farmer_garden_get(){
        $sce_id = getSceID();
        $data = $this->mclonal->getFarmerGarden($sce_id,$this->get('GardenNr'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    public function clonal_get_clonal_garden_get(){
        $data = $this->mclonal->getClonalGardenById($this->get('ClonalID'),$this->get('GardenNr'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    public function clonal_garden_post(){
        if($this->post('ClonalID') == ""){
            $clonal_garden = $this->mclonal->createClonalGarden($this->post());
        }else{
            $clonal_garden = $this->mclonal->updateClonalGarden($this->post());
        }
        if ($clonal_garden){
            $this->response($clonal_garden, 200);
        }else{
            $this->response(array('error' => 'Clone Garden could not be saved'), 404);
        }
    }

    public function clonal_garden_delete(){
        $clonal_garden = $this->mclonal->deleteClonalGarden($this->delete('ClonalID'));
        $this->response($clonal_garden, 200);
    }

    public function clonal_penjualan_get(){
        $data = $this->mclonal->getClonalPenjualan($this->get('ClonalID'),$this->get('start'),$this->get('limit'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any data'), 404);
    }

    public function clonal_penjualan_post(){
        $paramInsert = array(
            "ClonalID" => $this->post("ClonalID"),
            "Buyer" => $this->post("Buyer"),
            "Volume" => $this->post("Volume"),
            "Price" => $this->post("Price"),
            "Total" => $this->post("Total"),
            "DateTransaction" => $this->post("DateTransaction"),
            "CloneTypeName" => $this->post("CloneTypeName"),
            "CloneTypeID" => $this->post("CloneTypeID")
        );
        $data = $this->mclonal->createClonalPenjualan($paramInsert);
        $this->response($data, 200);
    }

    public function clonal_penjualan_put(){
        $paramUpdate = array(
            "id" => $this->put("id"),
            "Buyer" => $this->put("Buyer"),
            "Volume" => $this->put("Volume"),
            "Price" => $this->put("Price"),
            "Total" => $this->put("Total"),
            "DateTransaction" => $this->put("DateTransaction"),
            "CloneTypeName" => $this->put("CloneTypeName"),
            "CloneTypeID" => $this->put("CloneTypeID")
        );
        $data = $this->mclonal->updateClonalPenjualan($paramUpdate);
        $this->response($data, 200);
    }

    public function clonal_penjualan_delete(){
        $proses = $this->mclonal->deleteClonalPenjualan((int) $this->delete('id'));
        $this->response($proses, 200);
    }

    public function clonal_monitoring_get(){
        $data = $this->mclonal->getClonalMonitoring($this->get('ClonalID'),$this->get('start'),$this->get('limit'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any data'), 404);
    }

    public function clonal_monitoring_post(){
        $paramInsert = array(
            "ClonalID" => $this->post("ClonalID"),
            "MonitoringDate" => $this->post("MonitoringDate"),
            "MonitoringStatus" => $this->post("MonitoringStatus"),
            "Description" => $this->post("Description")
        );
        $proses = $this->mclonal->insertClonalMonitoring($paramInsert);
        $this->response($proses, 200);
    }

    public function clonal_monitoring_put(){
        $paramUpdate = array(
            "id" => $this->put("id"),
            "MonitoringDate" => $this->put("MonitoringDate"),
            "MonitoringStatus" => $this->put("MonitoringStatus"),
            "Description" => $this->put("Description")
        );
        $proses = $this->mclonal->updateClonalMonitoring($paramUpdate);
        $this->response($proses, 200);
    }

    public function clonal_monitoring_delete(){
        $proses = $this->mclonal->deleteClonalMonitoring((int) $this->delete('id'));
        $this->response($proses, 200);
    }

    public function clonal_garden_polygon_get(){
        $data['clonal_polygon'] = site_url() . '/prog_sce/clonal_garden_polygon';
        $data['clonal_polygon_center'] = site_url() . '/prog_sce/clonal_polygon_center';
        $data['ClonalID'] = $this->get('ClonalID');
        $data['latitude'] = $this->get('lati');
        $data['longitude'] = $this->get('longi');
        $data['GardenNr'] = $this->get('GardenNr');
        $data['hakAksesPolygon'] = $this->get('hakAksesPolygon');

        //get polygon area
        $data['area'] = $this->mclonal->getClonalGardenPolygonArea($data['ClonalID'],$data['latitude'],$data['longitude']);
        $this->load->view('clonal_garden_map_sce', @$data);
    }

    public function clonal_garden_polygon_put(){
        if (!$this->put('ClonalID'))
            $this->response(NULL, 400);

        $clonal_polygon = $this->mclonal->updateClonalPolygon($this->put('ClonalID'), $this->put('GardenNr'), $this->put('area'), $this->put('luas'), $this->put('lat'), $this->put('long'));
        if ($clonal_polygon)
            $this->response($clonal_polygon, 200);
        else
            $this->response(array('error' => 'Map Polygon could not be found'), 404);
    }

    public function clonal_polygon_center_put(){
        if (!$this->put('ClonalID'))
            $this->response(NULL, 400);

        $clonal_polygon = $this->mclonal->updateClonalPolygonCenter($this->put('ClonalID'), $this->put('GardenNr'), $this->put('lat'), $this->put('long'));
        if ($clonal_polygon)
            $this->response($clonal_polygon, 200);
        else
            $this->response(array('error' => 'Map Polygon could not be found'), 404);
    }

    public function update_clonal_garden_area_get(){
        $data = $this->mclonal->updateClonalGardenAreaGet($this->get('ClonalID'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    //===== CLONAL GARDEN (END) ==========================================================//
}
?>