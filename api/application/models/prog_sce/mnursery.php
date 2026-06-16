<?php
/**
 * @Author: nikolius
 * @Date:   2016-08-26 10:44:34
 */
class Mnursery extends CI_Model
{
    public function getNurseryNrCombo($sce_id)
    {
        $sql="SELECT
                    NurseryID AS id,
                    NurseryNr AS label
                FROM
                    ktv_nursery
                WHERE
                    StatusCode != 'nullified' AND
                    ObjType = 'farmer' AND
                    ObjID = (SELECT FarmerID FROM sce_farmer WHERE SceID = ? LIMIT 1)
                ORDER BY NurseryNr ASC";
        $query = $this->db->query($sql,array($sce_id));
        return $query->result_array();
    }

    public function getNurseryById($sce_id,$NurseryID){
        $sql="SELECT
                    `NurseryID`,
                    `ObjType`,
                    `ObjID`,
                    `NurseryNr` AS NurseryNrSend,
                    `ResponsibleType`,
                    `Responsible`,
                    `ResponsibleName`,
                    `ResponsibleBirthday`,
                    `ResponsiblePhone`,
                    `ResponsibleGender`,
                    `ResponsiblePhoto`,
                    `Established`,
                    `Panjang`,
                    `Lebar`,
                    `Kapasitas`,
                    `Latitude`,
                    `Longitude`,
                    `LatitudeDeg1`,
                    `LatitudeDeg2`,
                    `LatitudeDeg3`,
                    `LongitudeDeg1`,
                    `LongitudeDeg2`,
                    `LongitudeDeg3`,
                    `CertificationStatus`,
                    `DateCertification`,
                    `DateAppliedCertification`,
                    `Area`,
                    `Photo`,
                    `LocationCloseToCommunity`,
                    `LocationCloseToCommunityNo`,
                    `GoodLandArea`,
                    `GoodLandAreaNo`,
                    `LocationNearCocoaFarm`,
                    `LocationNearCocoaFarmNo`,
                    `ContinuousWaterSupply`,
                    `ContinuousWaterSupplyNo`,
                    `IrrigationInstalled`,
                    `IrrigationInstalledNo`,
                    `UseShadingNet`,
                    `UseShadingNetNo`,
                    `AdequateSupplyTopSoil`,
                    `AdequateSupplyTopSoilNo`,
                    `ImprovedVariety`,
                    `ImprovedVarietyNo`,
                    `ConstructStoring`,
                    `ConstructStoringNo`,
                    `CorrectEquipment`,
                    `CorrectEquipmentNo`,
                    `WindBreakInstalled`,
                    `WindBreakInstalledNo`,
                    `SecurityFenceInstalled`,
                    `SecurityFenceInstalledNo`,
                    `FertilizerUsed`,
                    `FertilizerUsedNo`,
                    `OperatorAdequateTraining`,
                    `OperatorAdequateTrainingNo`,
                    `AdequateFacility`,
                    `AdequateFacilityNo`,
                    `SustainablePestDisease`,
                    `SustainablePestDiseaseNo`,
                    `CloneGrading`,
                    `CloneGradingNo`,
                    `SeedlingCullingDone`,
                    `SeedlingCullingDoneNo`,
                    `ProperInputSalesRecord`,
                    `ProperInputSalesRecordNo`,
                    `SeedsPreGerminated`,
                    `SeedsPreGerminatedNo`,
                    `StatusCode`
                FROM
                  `ktv_nursery`
                WHERE
                    NurseryID = ?
                LIMIT 1";
        $query = $this->db->query($sql,array($NurseryID));
        $data = $query->row_array();

        $return['success'] = true;
        $return['data'] = $data;
        return $return;
    }

    public function getResponByType($responsibleType,$SceID){
        if($responsibleType == 'farmer'){
            $sql="SELECT
                    a.`FarmerID` AS id,
                    CONCAT(a.`FarmerID`,' - ',a.`FarmerName`) AS label
                FROM
                    ktv_farmer a
                    INNER JOIN sce_farmer b ON a.`FarmerID` = b.`FarmerID`
                WHERE
                    b.`SceID` = ?
                    AND a.`StatusCode` = 'active'
                ORDER BY a.`FarmerID` ASC";
            $query = $this->db->query($sql,array($SceID));
        }

        if($responsibleType == 'staff'){
            $sql="SELECT
                    a.`StaffID` AS id,
                    CONCAT('[',a.`StaffID`,'] ',b.`PersonNm`,' - ',IFNULL(d.`PositionName`,'No Position')) AS label
                FROM
                    ktv_staffs a
                    INNER JOIN ktv_persons b ON a.`PersonID` = b.`PersonID`
                    LEFT JOIN `ktv_staff_positions` c ON a.`StaffID` = c.StaffPosStaffID
                        AND CURDATE() BETWEEN c.`StaffPostStart` AND c.`StaffPostEnd`
                        AND c.StatusCode = 'active'
                    LEFT JOIN `ktv_ref_position_type` d ON c.`StaffPosPositionID` = d.`PositionID`
                WHERE
                    a.`ObjType` = 'sce'
                    AND a.`StatusCode` = 'active'
                    AND b.`PersonNm` != ''
                    AND a.ObjID = ?
                ORDER BY b.`PersonNm` ASC";
            $query = $this->db->query($sql,array($SceID));
        }

        $data = $query->result_array();
        $return['data'] = $data;
        return $return;
    }

    public function createNursery($varPro,$varNurseryCeklist){
        //get NurseryNr
        $sql="SELECT NurseryNr FROM ktv_nursery WHERE ObjType = 'farmer' AND ObjID = (SELECT FarmerID FROM sce_farmer WHERE SceID = ? LIMIT 1) ORDER BY NurseryNr DESC LIMIT 1";
        $query = $this->db->query($sql,array($varPro['SceID']));
        $data = $query->row_array();
        if($data['NurseryNr'] == ""){
            $NurseryNr = 1;
        }else{
            $NurseryNr = $data['NurseryNr'] + 1;
        }

        //FarmerID
        $sql="SELECT FarmerID FROM sce_farmer WHERE SceID = ? LIMIT 1";
        $query = $this->db->query($sql,array($varPro['SceID']));
        $data = $query->row_array();
        $FarmerID = $data['FarmerID'];

        $DistrictID = substr($FarmerID,0,4);

        $data = array(
            'ObjType' => 'farmer',
            'ObjID' => $FarmerID,
            'NurseryNr'=> $NurseryNr,
            'DistrictID'=>$DistrictID,
            'ResponsibleType' => $varPro['ResponsibleType'],
            'Responsible' => $varPro['Responsible'],
            'ResponsibleName' => $varPro['ResponsibleName'],
            'ResponsibleBirthday' => $varPro['ResponsibleBirthday'],
            'ResponsiblePhone' => $varPro['ResponsiblePhone'],
            'ResponsibleGender' => $varPro['ResponsibleGender'],
            'ResponsiblePhoto' => $varPro['Photo_old_responsible'],
            'Photo' => $varPro['Photo_old'],
            'Established'=> $varPro['nEstablished'],
            'Panjang'=> $varPro['Panjang'],
            'Lebar'=> $varPro['Lebar'],
            'Kapasitas'=> $varPro['Kapasitas'],
            'Latitude'=> $varPro['Latitude'],
            'Longitude'=> $varPro['Longitude'],
            'DateCertification'=> $varPro['tglCertificate'],
            'DateAppliedCertification'=> $varPro['DateAppliedCertification'],
            'CertificationStatus'=> $varPro['NursCertBp2YaTidak'],
            'CreatedBy' => $varPro['userid'],
            'DateCreated' => date('Y-m-d H:m:s')
        );
        $query = $this->db->insert('ktv_nursery', $data);
        $id = $this->db->insert_id();

        $sql="UPDATE `ktv_nursery` SET
          `LocationCloseToCommunity` = ?,
          `LocationCloseToCommunityNo` = ?,
          `GoodLandArea` = ?,
          `GoodLandAreaNo` = ?,
          `LocationNearCocoaFarm` = ?,
          `LocationNearCocoaFarmNo` = ?,
          `ContinuousWaterSupply` = ?,
          `ContinuousWaterSupplyNo` = ?,
          `IrrigationInstalled` = ?,
          `IrrigationInstalledNo` = ?,
          `UseShadingNet` = ?,
          `UseShadingNetNo` = ?,
          `AdequateSupplyTopSoil` = ?,
          `AdequateSupplyTopSoilNo` = ?,
          `ImprovedVariety` = ?,
          `ImprovedVarietyNo` = ?,
          `ConstructStoring` = ?,
          `ConstructStoringNo` = ?,
          `CorrectEquipment` = ?,
          `CorrectEquipmentNo` = ?,
          `WindBreakInstalled` = ?,
          `WindBreakInstalledNo` = ?,
          `SecurityFenceInstalled` = ?,
          `SecurityFenceInstalledNo` = ?,
          `FertilizerUsed` = ?,
          `FertilizerUsedNo` = ?,
          `OperatorAdequateTraining` = ?,
          `OperatorAdequateTrainingNo` = ?,
          `AdequateFacility` = ?,
          `AdequateFacilityNo` = ?,
          `SustainablePestDisease` = ?,
          `SustainablePestDiseaseNo` = ?,
          `CloneGrading` = ?,
          `CloneGradingNo` = ?,
          `SeedlingCullingDone` = ?,
          `SeedlingCullingDoneNo` = ?,
          `ProperInputSalesRecord` = ?,
          `ProperInputSalesRecordNo` = ?,
          `SeedsPreGerminated` = ?,
          `SeedsPreGerminatedNo` = ?
        WHERE
            `NurseryID` = ?
        LIMIT 1";
        $p = array(
              $varNurseryCeklist['LocationCloseToCommunity'],
              $varNurseryCeklist['LocationCloseToCommunityNo'],
              $varNurseryCeklist['GoodLandArea'],
              $varNurseryCeklist['GoodLandAreaNo'],
              $varNurseryCeklist['LocationNearCocoaFarm'],
              $varNurseryCeklist['LocationNearCocoaFarmNo'],
              $varNurseryCeklist['ContinuousWaterSupply'],
              $varNurseryCeklist['ContinuousWaterSupplyNo'],
              $varNurseryCeklist['IrrigationInstalled'],
              $varNurseryCeklist['IrrigationInstalledNo'],
              $varNurseryCeklist['UseShadingNet'],
              $varNurseryCeklist['UseShadingNetNo'],
              $varNurseryCeklist['AdequateSupplyTopSoil'],
              $varNurseryCeklist['AdequateSupplyTopSoilNo'],
              $varNurseryCeklist['ImprovedVariety'],
              $varNurseryCeklist['ImprovedVarietyNo'],
              $varNurseryCeklist['ConstructStoring'],
              $varNurseryCeklist['ConstructStoringNo'],
              $varNurseryCeklist['CorrectEquipment'],
              $varNurseryCeklist['CorrectEquipmentNo'],
              $varNurseryCeklist['WindBreakInstalled'],
              $varNurseryCeklist['WindBreakInstalledNo'],
              $varNurseryCeklist['SecurityFenceInstalled'],
              $varNurseryCeklist['SecurityFenceInstalledNo'],
              $varNurseryCeklist['FertilizerUsed'],
              $varNurseryCeklist['FertilizerUsedNo'],
              $varNurseryCeklist['OperatorAdequateTraining'],
              $varNurseryCeklist['OperatorAdequateTrainingNo'],
              $varNurseryCeklist['AdequateFacility'],
              $varNurseryCeklist['AdequateFacilityNo'],
              $varNurseryCeklist['SustainablePestDisease'],
              $varNurseryCeklist['SustainablePestDiseaseNo'],
              $varNurseryCeklist['CloneGrading'],
              $varNurseryCeklist['CloneGradingNo'],
              $varNurseryCeklist['SeedlingCullingDone'],
              $varNurseryCeklist['SeedlingCullingDoneNo'],
              $varNurseryCeklist['ProperInputSalesRecord'],
              $varNurseryCeklist['ProperInputSalesRecordNo'],
              $varNurseryCeklist['SeedsPreGerminated'],
              $varNurseryCeklist['SeedsPreGerminatedNo'],
              $id
        );
        $query = $this->db->query($sql,$p);
        //update nursery ceklist (end)

        if ($query) {
            $results['id'] = (string) $id;
            $results['NurseryNr'] = (string) $NurseryNr;
            $results['prosesnya'] = 'insert';
            $results['success']    = true;
            $results['message']    = "Data saved";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        }
        return $results;
    }

    public function updateNursery($varPro,$varNurseryCeklist){
        $data = array(
            'ResponsibleType' => $varPro['ResponsibleType'],
            'Responsible' => $varPro['Responsible'],
            'ResponsibleName' => $varPro['ResponsibleName'],
            'ResponsibleBirthday' => $varPro['ResponsibleBirthday'],
            'ResponsiblePhone' => $varPro['ResponsiblePhone'],
            'ResponsibleGender' => $varPro['ResponsibleGender'],
            'ResponsiblePhoto' => $varPro['Photo_old_responsible'],
            'Photo' => $varPro['Photo_old'],
            'Established'=> $varPro['nEstablished'],
            'Panjang'=> $varPro['Panjang'],
            'Lebar'=> $varPro['Lebar'],
            'Kapasitas'=> $varPro['Kapasitas'],
            'Latitude'=> $varPro['Latitude'],
            'Longitude'=> $varPro['Longitude'],
            'DateCertification'=> $varPro['tglCertificate'],
            'DateAppliedCertification'=> $varPro['DateAppliedCertification'],
            'CertificationStatus'=> $varPro['NursCertBp2YaTidak'],
            'LastModifiedBy' => $varPro['userid'],
            'DateUpdated' => date('Y-m-d H:m:s')
        );
        $this->db->where('NurseryID',$this->input->post('NurseryID'));
        $query = $this->db->update('ktv_nursery', $data);

        $sql="UPDATE `ktv_nursery` SET
          `LocationCloseToCommunity` = ?,
          `LocationCloseToCommunityNo` = ?,
          `GoodLandArea` = ?,
          `GoodLandAreaNo` = ?,
          `LocationNearCocoaFarm` = ?,
          `LocationNearCocoaFarmNo` = ?,
          `ContinuousWaterSupply` = ?,
          `ContinuousWaterSupplyNo` = ?,
          `IrrigationInstalled` = ?,
          `IrrigationInstalledNo` = ?,
          `UseShadingNet` = ?,
          `UseShadingNetNo` = ?,
          `AdequateSupplyTopSoil` = ?,
          `AdequateSupplyTopSoilNo` = ?,
          `ImprovedVariety` = ?,
          `ImprovedVarietyNo` = ?,
          `ConstructStoring` = ?,
          `ConstructStoringNo` = ?,
          `CorrectEquipment` = ?,
          `CorrectEquipmentNo` = ?,
          `WindBreakInstalled` = ?,
          `WindBreakInstalledNo` = ?,
          `SecurityFenceInstalled` = ?,
          `SecurityFenceInstalledNo` = ?,
          `FertilizerUsed` = ?,
          `FertilizerUsedNo` = ?,
          `OperatorAdequateTraining` = ?,
          `OperatorAdequateTrainingNo` = ?,
          `AdequateFacility` = ?,
          `AdequateFacilityNo` = ?,
          `SustainablePestDisease` = ?,
          `SustainablePestDiseaseNo` = ?,
          `CloneGrading` = ?,
          `CloneGradingNo` = ?,
          `SeedlingCullingDone` = ?,
          `SeedlingCullingDoneNo` = ?,
          `ProperInputSalesRecord` = ?,
          `ProperInputSalesRecordNo` = ?,
          `SeedsPreGerminated` = ?,
          `SeedsPreGerminatedNo` = ?
        WHERE
            `NurseryID` = ?
        LIMIT 1";
        $p = array(
              $varNurseryCeklist['LocationCloseToCommunity'],
              $varNurseryCeklist['LocationCloseToCommunityNo'],
              $varNurseryCeklist['GoodLandArea'],
              $varNurseryCeklist['GoodLandAreaNo'],
              $varNurseryCeklist['LocationNearCocoaFarm'],
              $varNurseryCeklist['LocationNearCocoaFarmNo'],
              $varNurseryCeklist['ContinuousWaterSupply'],
              $varNurseryCeklist['ContinuousWaterSupplyNo'],
              $varNurseryCeklist['IrrigationInstalled'],
              $varNurseryCeklist['IrrigationInstalledNo'],
              $varNurseryCeklist['UseShadingNet'],
              $varNurseryCeklist['UseShadingNetNo'],
              $varNurseryCeklist['AdequateSupplyTopSoil'],
              $varNurseryCeklist['AdequateSupplyTopSoilNo'],
              $varNurseryCeklist['ImprovedVariety'],
              $varNurseryCeklist['ImprovedVarietyNo'],
              $varNurseryCeklist['ConstructStoring'],
              $varNurseryCeklist['ConstructStoringNo'],
              $varNurseryCeklist['CorrectEquipment'],
              $varNurseryCeklist['CorrectEquipmentNo'],
              $varNurseryCeklist['WindBreakInstalled'],
              $varNurseryCeklist['WindBreakInstalledNo'],
              $varNurseryCeklist['SecurityFenceInstalled'],
              $varNurseryCeklist['SecurityFenceInstalledNo'],
              $varNurseryCeklist['FertilizerUsed'],
              $varNurseryCeklist['FertilizerUsedNo'],
              $varNurseryCeklist['OperatorAdequateTraining'],
              $varNurseryCeklist['OperatorAdequateTrainingNo'],
              $varNurseryCeklist['AdequateFacility'],
              $varNurseryCeklist['AdequateFacilityNo'],
              $varNurseryCeklist['SustainablePestDisease'],
              $varNurseryCeklist['SustainablePestDiseaseNo'],
              $varNurseryCeklist['CloneGrading'],
              $varNurseryCeklist['CloneGradingNo'],
              $varNurseryCeklist['SeedlingCullingDone'],
              $varNurseryCeklist['SeedlingCullingDoneNo'],
              $varNurseryCeklist['ProperInputSalesRecord'],
              $varNurseryCeklist['ProperInputSalesRecordNo'],
              $varNurseryCeklist['SeedsPreGerminated'],
              $varNurseryCeklist['SeedsPreGerminatedNo'],
              $this->input->post('NurseryID')
        );
        $query = $this->db->query($sql,$p);
        //update nursery ceklist (end)

        if ($query) {
            $results['prosesnya'] = 'update';
            $results['success']    = true;
            $results['message']    = "Data saved";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        }
        return $results;
    }

    public function deleteNursery($NurseryID){
        $this->db->trans_start();

        //update jadikan nullified
        $sql="UPDATE ktv_nursery SET StatusCode='nullified' WHERE NurseryID=? LIMIT 1";
        $query = $this->db->query($sql,array($NurseryID));

        /*
        $sql="DELETE FROM ktv_nursery_area WHERE NurseryID = ?";
        $query = $this->db->query($sql,array($NurseryID));

        $sql="DELETE FROM ktv_nursery WHERE NurseryID = ? LIMIT 1";
        $query = $this->db->query($sql,array($NurseryID));
        */

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Data deleted";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    public function getNurseryPenjualan($NurseryID,$start,$limit){
        $sql="SELECT
                    SQL_CALC_FOUND_ROWS
                  a.`NurseryTransactionID`,
                  a.`NurseryTransactionID` AS id,
                  a.`NurseryID`,
                  a.`Buyer`,
                  a.`Volume`,
                  a.`CloneTypeID`,
                  a.`Price`,
                  a.Volume * a.Price AS Total,
                  date(a.`DateTransaction`) AS DateTransaction,
                  b.CloneTypeName
                FROM
                  `ktv_nursery_transaction` a
                  LEFT JOIN ktv_clone_type b ON a.CloneTypeID = b.CloneTypeID
                WHERE
                    a.NurseryID = ? AND
                    a.StatusCode != 'nullified'
                ORDER BY a.NurseryTransactionID DESC
                #LIMIT ?,?";
        $query = $this->db->query($sql, array($NurseryID,(int) $start,(int) $limit));
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    public function insertNurseryPenjualan($paramInsert){
        $tgl = explode("T",$paramInsert['DateTransaction']);

        $sql="INSERT INTO  `ktv_nursery_transaction` SET
              `NurseryID` = ?,
              `Buyer` = ?,
              `Volume` = ?,
              `CloneTypeID` = ?,
              `Price` = ?,
              `DateTransaction` = ?,
              `DateCreated` = NOW(),
              `CreatedBy` = ?";
        $p = array(
            $paramInsert['NurseryID'],
            $paramInsert['Buyer'],
            $paramInsert['Volume'],
            $paramInsert['CloneTypeID'],
            $paramInsert['Price'],
            $tgl[0],
            $_SESSION['userid']
        );
        $query = $this->db->query($sql, $p);

        if ($query) {
            $results['success'] = true;
            $results['message'] = "Record created";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    public function updateNurseryPenjualan($paramUpdate){
        $tgl = explode("T",$paramUpdate['DateTransaction']);

        $sql="UPDATE `ktv_nursery_transaction` SET
                  `Buyer` = ?,
                  `Volume` = ?,
                  `CloneTypeID` = ?,
                  `Price` = ?,
                  `DateTransaction` = ?,
                  `DateUpdated` = NOW(),
                  `LastModifiedBy` = ?
                WHERE
                    NurseryTransactionID = ?
                LIMIT 1";
        $p = array(
            $paramUpdate['Buyer'],
            $paramUpdate['Volume'],
            $paramUpdate['CloneTypeID'],
            $paramUpdate['Price'],
            $tgl[0],
            $_SESSION['userid'],
            $paramUpdate['id']
        );
        $query = $this->db->query($sql, $p);

        if ($query) {
            $results['success'] = true;
            $results['message'] = "Record updated";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    public function deleteNurseryPenjualan($id){
        $sql="UPDATE `ktv_nursery_transaction` SET
                  StatusCode = 'nullified',
                  `DateUpdated` = NOW(),
                  `LastModifiedBy` = ?
                WHERE
                    NurseryTransactionID = ?
                LIMIT 1";
        $p = array(
            $_SESSION['userid'],
            $id
        );
        $query = $this->db->query($sql, $p);

        if ($query) {
            $results['success'] = true;
            $results['message'] = "Record deleted";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    public function getNurseryMonitoring($NurseryID,$start,$limit){
        $sql="SELECT
                SQL_CALC_FOUND_ROWS
              `NurseryMonitoringID`,
              `NurseryMonitoringID` AS id,
              `NurseryID`,
              `MonitoringDate`,
              `MonitoringStatus`,
              `Description`
            FROM
                `ktv_nursery_monitoring`
            WHERE
                StatusCode != 'nullified' AND
                NurseryID = ?
            ORDER BY NurseryMonitoringID DESC
            #LIMIT ?,?";
        $query = $this->db->query($sql, array($NurseryID,(int) $start,(int) $limit));
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    public function insertNurseryMonitoring($paramInsert){
        $tgl = explode("T",$paramInsert['MonitoringDate']);

        $sql="INSERT INTO `ktv_nursery_monitoring` SET
              `NurseryID` = ?,
              `MonitoringDate` = ?,
              `MonitoringStatus` = ?,
              `Description` = ?,
              `DateCreated` = NOW(),
              `CreatedBy` = ?";
        $p = array(
            $paramInsert['NurseryID'],
            $tgl[0],
            $paramInsert['MonitoringStatus'],
            $paramInsert['Description'],
            $_SESSION['userid']
        );
        $query = $this->db->query($sql, $p);

        if ($query) {
            $results['success'] = true;
            $results['message'] = "Record created";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    public function updateNurseryMonitoring($paramUpdate){
        $tgl = explode("T",$paramUpdate['MonitoringDate']);

        $sql="UPDATE `ktv_nursery_monitoring` SET
              `MonitoringDate` = ?,
              `MonitoringStatus` = ?,
              `Description` = ?,
              `DateUpdated` = NOW(),
              `LastModifiedBy` = ?
            WHERE
              NurseryMonitoringID = ?
            LIMIT 1";
        $p = array(
            $tgl[0],
            $paramUpdate['MonitoringStatus'],
            $paramUpdate['Description'],
            $_SESSION['userid'],
            $paramUpdate['id']
        );
        $query = $this->db->query($sql, $p);

        if ($query) {
            $results['success'] = true;
            $results['message'] = "Record updated";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    public function deleteNurseryMonitoring($id){
        $sql="UPDATE `ktv_nursery_monitoring` SET
                  StatusCode = 'nullified',
                  `DateUpdated` = NOW(),
                  `LastModifiedBy` = ?
                WHERE
                    NurseryMonitoringID = ?
                LIMIT 1";
        $p = array(
            $_SESSION['userid'],
            $id
        );
        $query = $this->db->query($sql, $p);

        if ($query) {
            $results['success'] = true;
            $results['message'] = "Record deleted";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    public function getNurseryPolygonArea($NurseryID,$latitude,$longitude){
        $sql="SELECT
                Latitude,
                Longitude
            FROM
                ktv_nursery_area
            WHERE
                NurseryID = ?
            ORDER BY OrderNr ASC";
        $query = $this->db->query($sql,array($NurseryID));

        if($query->num_rows() > 0){
            $result = "[";
            $no = 0;
            foreach ($query->result() as $row) {
                if($no!=0){
                    $result .= ",";
                }
                $result .= "[";
                $result .= $row->Latitude;
                $result .= ",";
                $result .= $row->Longitude;
                $result .= "]";
                $no++;
            }
            $result .= "]";
            return $result;
        }else{
            if(($latitude!='0.000000'||$longitude!='0.000000') && ($latitude!=''||$longitude!='')){
                return "[[$latitude,$longitude]]";
            }else{
                return "[[-1.2674336,113.6939433]]";
            }
        }
    }

    public function updateNurseryPolygon($NurseryID, $NurseryNr, $area, $luas, $lat, $long){
        $result = false;

        if($luas=='0.00'){
            $lat = null; $long = null;
        }

        $this->db->trans_start(FALSE);

        //hapus datanya terlebih dahulu
        $sql="DELETE FROM ktv_nursery_area WHERE NurseryID = ?";
        $query = $this->db->query($sql,array($NurseryID));

        // insert new area
        if (is_array($area)) {
            $no = 1;
            $data = array();
            foreach ($area as $val) {
                $data[] = array(
                    'NurseryID'    => $NurseryID,
                    'NurseryNr'    => $NurseryNr,
                    'OrderNr'     => $no,
                    'DateCreated' => date('Y-m-d H:i:s'),
                    'CreatedBy'   => $_SESSION['userid'],
                    'Latitude'    => $val[0],
                    'Longitude'   => $val[1]
                );
                $no++;
            }
            $this->db->insert_batch('ktv_nursery_area', $data);
            $sql = "UPDATE ktv_nursery SET Area=?,Latitude=?,Longitude=? WHERE NurseryID=? AND NurseryNr=?";
            $query = $this->db->query($sql, array($luas, $lat,$long, $NurseryID, $NurseryNr));
        }

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Success.";
        } else {
            $results['success'] = false;
            $results['message'] = "Error. Please reload page and try again.";
        }
        return $results;
    }

    public function updateNurseryPolygonCenter($NurseryID, $NurseryNr, $lat, $long){
        $sql = "SELECT * FROM ktv_nursery WHERE NurseryID=? AND NurseryNr=?";
        $query = $this->db->query($sql, array($NurseryID, $NurseryNr));
        if($lat)
        if((@$query->row()->Latitude == '' || @$query->row()->Latitude == '0.000000') && (@$query->row()->Longitude == '' || @$query->row()->Longitude == '0.000000')){
            $sql = "UPDATE ktv_nursery SET Latitude=?, Longitude=? WHERE NurseryID=? AND NurseryNr=?";
            $query = $this->db->query($sql, array($lat, $long, $NurseryID, $NurseryNr));
            if ($query) {
                $results['success'] = true;
                $results['message'] = "Success.";
            } else {
                $results['success'] = false;
                $results['message'] = "Error. Please reload page and try again.";
            }
            return $results;
        }else{
            $results['success'] = true;
            $results['message'] = "No Update";
            return $results;
        }
    }

    public function updateNurseryAreaGet($NurseryID){
        $sql = "SELECT Area,Latitude,Longitude FROM ktv_nursery WHERE NurseryID=? LIMIT 1";
        $query = $this->db->query($sql, array($NurseryID));
        $result= $query->result_array();
        return $result[0];
    }

}
?>