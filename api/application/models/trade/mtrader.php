<?php

class Mtrader extends CI_Model {
    public $user;

    public function __construct()
    {
        parent::__construct();
        $this->user = $this->muserprofile->getUserProfile();
    }
    function readDatas($key, $prov, $kab, $kec, $start, $limit) {

        if ($prov != '')
            $add = " AND g.ProvinceID = $prov ";
        if ($kab != '')
            $add .= " AND f.DistrictID = $kab";
        if ($kec != '')
            $add .= " AND e.SubDistrictID = $kec";
        if (!empty($this->user['district_access'])) {
            $add .= " AND f.DistrictID IN ({$this->user['district_access']})";
        }
        if ($key != '')
            $add .= " AND (kt.TraderID like '$key' OR kt.TraderName like '%$key%' OR Company like '%$key%')";

        $limit = $this->input->get('limit');
        $start = $this->input->get('start');

        $sql_cek = "select TraderID from ktv_trader_staff where UserId=?";
        $query = $this->db->query($sql_cek, array($_SESSION['userid']));
        $cek = $query->result_array();
        if ($cek[0]['TraderID']!='') $add .= " and kt.TraderID=".$cek[0]['TraderID'];

        $sql = "SELECT SQL_CALC_FOUND_ROWS %s
            from ktv_traders kt
            left join ktv_village d on kt.VillageID=d.VillageID
            left join ktv_subdistrict e on d.SubDistrictID=e.SubDistrictID
            left join ktv_district f on e.DistrictID=f.DistrictID
            left join ktv_province g on f.ProvinceID=g.ProvinceID
            WHERE kt.TraderID>0 AND kt.StatusCode != 'nullified' %s
            ORDER BY Company limit $start,$limit";
            //printf($sql,'kt.*,District,kt.TraderID as id,(PermanentEmployeeMale+PermanentEmployeeFemale+
              //      TemporaryEmployeeMale+TemporaryEmployeeFemale) as TotalPegawai',$add.' group by kt.TraderID');exit;
        $query = $this->db->query(sprintf($sql,'kt.*,District,kt.TraderID as id,(PermanentEmployeeMale+PermanentEmployeeFemale+
                    TemporaryEmployeeMale+TemporaryEmployeeFemale) as TotalPegawai',$add.' group by kt.TraderID'));
        $result['data'] = $query->result_array();
        
        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    function readData($id) {
        $sql = "select *, TraderID as id,e.SubDistrict as Kecamatan, f.District as Kabupaten,g.Province as Provinsi
            from ktv_traders a
            left join ktv_village d on a.VillageID=d.VillageID
            left join ktv_subdistrict e on d.SubDistrictID=e.SubDistrictID
            left join ktv_district f on e.DistrictID=f.DistrictID
            left join ktv_province g on f.ProvinceID=g.ProvinceID
            where TraderID = ?";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

    function createDataNurseryTransaction($uid)
    {
         $data = array(
                'NurseryID'=> $this->input->post('id_nursey'),
                'Buyer'=> $this->input->post('Buyer'),
                'Volume' => $this->input->post('Volume'),
                'Price'=> $this->input->post('Price'),
                'DateTransaction'=> str_replace("T00:00:00", "", $this->input->post('DateTransaction')),
                'CreatedBy' => $uid,
                'DateCreated' => date('Y-m-d H:m:s')
        );
        $this->db->insert('ktv_nursery_transaction', $data);

        $id = $this->db->insert_id();

        if ($this->db->affected_rows() > 0) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['id'] = null;
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateDataNurseryTransaction($id_nursey,$Buyer,$Volume,$Price,$DateTransaction,$uid,$id)
    {
         $data = array(
                'NurseryID'=> $id_nursey,
                'Buyer'=> $Buyer,
                'Volume' => $Volume,
                'Price'=> $Price,
                'DateTransaction'=> str_replace("T00:00:00", "", $DateTransaction),
                'LastModifiedBy' => $uid,
                'DateUpdated' => date('Y-m-d H:m:s')
        );
        $this->db->where('NurseryTransactionID',$id);
        $this->db->update('ktv_nursery_transaction', $data);
////
//          $sql = "
//            update ktv_nursery_transaction
//            set NurseryID=?, Buyer=?, Volume=?,Price=?, DateTransaction=?, LastModifiedBy=?, DateUpdated=now()
//            where NursserysTransactionID=?";
//        $query = $this->db->query($sql, array(
//            $id_nursey,
//            $Buyer,
//            $Volume,
//            $Price,
//            $DateTransaction,
//            $uid,
//            $id));

        if ($this->db->affected_rows()>0) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = " Failed to update record";
        }
        return $results;
    }

    function deleteTransaction($id)
    {
        //$sql = "DELETE FROM ktv_nursery_transaction WHERE NurseryTransactionID=?";
        $sql="UPDATE ktv_nursery_transaction SET StatusCode='nullified',LastModifiedBy='".$_SESSION['userid']."',DateUpdated=NOW() WHERE NurseryTransactionID = ? LIMIT 1";
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    function readDataNurseryTrans($id,$prov, $kab, $start, $limit)
    {
        $limit = $this->input->get('limit');
        $start = $this->input->get('start');

        $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    NurseryTransactionID,NurseryID,Buyer,Volume,Price,SUBSTR(DateTransaction,1,10) AS DateTransaction,sum(Volume*Price) as Total
                FROM
                    ktv_nursery_transaction
                WHERE
                    NurseryID=? AND StatusCode != 'nullified' group by Volume,Price having NurseryTransactionID is not null
                ORDER BY NurseryTransactionID
                #LIMIT ?,?";
        $query = $this->db->query($sql,array($id,(int)$start,(int)$limit));
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;
        return $result;
    }

    function readDataFormNursery($id,$NurseryID)
    {
        $sql = "select *, (Panjang*Lebar) AS Luas from ktv_nursery where ObjID = ? AND NurseryID=?";
        $q = $this->db->query($sql,array($id,$NurseryID));
        if($q->num_rows()>0)
        {
            $data = $q->result_array();
            $return['success'] = true;
            $return['data'] = $data[0];
            return $return;
        } else {
            return array('success'=>false);
        }
    }

    public function getNurseryResponByType($responsibleType,$TraderID){
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
                    a.`ObjType` = 'trader'
                    AND a.`StatusCode` = 'active'
                    AND b.`PersonNm` != ''
                    AND a.ObjID = ?
                ORDER BY b.`PersonNm` ASC";
            $query = $this->db->query($sql,array($TraderID));
        }

        $data = $query->result_array();
        $return['data'] = $data;
        return $return;
    }

    public function checkNurseryNr($varPro){
        $sql="SELECT
                NurseryID
            FROM
                ktv_nursery
            WHERE
                ObjID = ?
                AND ObjType = 'trader'
                AND NurseryNr = ?";
        $query = $this->db->query($sql,array($varPro['id_obj'],$varPro['NurseryNr']));
        $data = $query->row_array();
        if($data['NurseryID'] != "") return false; else return true;
    }

    function createDataNursery($varPro,$varNurseryCeklist) {
        //get DistrictID
        $sql="SELECT
            SUBSTR(sub_a.`VillageID`,1,4) AS DistrictID
        FROM
            ktv_traders sub_a
        WHERE
            sub_a.`TraderID` = ?
        LIMIT 1";
        $query = $this->db->query($sql,array($varPro['id_obj']));
        $dataDistrict = $query->row_array();

        $data = array(
            'ObjType' => $varPro['type_obj'],
            'ObjID' => $varPro['id_obj'],
            'NurseryNr'=> $varPro['NurseryNr'],
            'DistrictID' => $dataDistrict['DistrictID'],
            'ResponsibleType' => $varPro['ResponsibleType_idtrader'],
            'Responsible' => $varPro['Responsible_idtrader'],
            'ResponsibleName' => $varPro['ResponsibleName_idtrader'],
            'ResponsibleBirthday' => $varPro['ResponsibleBirthday_idtrader'],
            'ResponsiblePhone' => $varPro['ResponsiblePhone_idtrader'],
            'ResponsibleGender' => $varPro['ResponsibleGender_idtrader'],
            'ResponsiblePhoto' => $varPro['Photo_old_responsible_idtrader'],
            'Photo' => $varPro['Photo_old_idtrader'],
            'Established'=> $varPro['Established'],
            'Panjang'=> $varPro['Panjang'],
            'Lebar'=> $varPro['Lebar'],
            'Kapasitas'=> $varPro['Kapasitas'],
            'Latitude'=> $varPro['Latitude'],
            'Longitude'=> $varPro['Longitude'],
            'DateCertification'=> $varPro['DateCertification'],
            'DateAppliedCertification'=> $varPro['DateAppliedCertification'],
            'CertificationStatus'=> $varPro['CertificationStatus'],
            'CreatedBy' => $varPro['userid'],
            'DateCreated' => date('Y-m-d H:m:s')
        );
        $this->db->insert('ktv_nursery', $data);
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

        if ($query) {
            $results['id'] = $id;
            $results['success'] = true;
            $results['message'] = "Record created.";
        } else {
            $results['id'] = null;
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateDataNursery($varPro,$varNurseryCeklist) {
        $data = array(
            'ResponsibleType' => $varPro['ResponsibleType_idtrader'],
            'Responsible' => $varPro['Responsible_idtrader'],
            'ResponsibleName' => $varPro['ResponsibleName_idtrader'],
            'ResponsibleBirthday' => $varPro['ResponsibleBirthday_idtrader'],
            'ResponsiblePhone' => $varPro['ResponsiblePhone_idtrader'],
            'ResponsibleGender' => $varPro['ResponsibleGender_idtrader'],
            'ResponsiblePhoto' => $varPro['Photo_old_responsible_idtrader'],
            'Photo' => $varPro['Photo_old_idtrader'],
            'Established'=> $varPro['Established'],
            'Panjang'=> $varPro['Panjang'],
            'Lebar'=> $varPro['Lebar'],
            'Kapasitas'=> $varPro['Kapasitas'],
            'Latitude'=> $varPro['Latitude'],
            'Longitude'=> $varPro['Longitude'],
            'DateCertification'=> $varPro['DateCertification'],
            'DateAppliedCertification'=> $varPro['DateAppliedCertification'],
            'CertificationStatus'=> $varPro['CertificationStatus'],
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

        if ($query) {
            $results['id'] = $this->input->post('NurseryID');
            $results['success'] = true;
            $results['message'] = "Record updated.";
        } else {
            $results['id'] = null;
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function createData($gambar, $userid) {
//        $sql = "
//            insert into ktv_traders (Address, Handphone,VillageID, Company, CompanyStatus, CompanyYear, CompanyAlias,PermanentEmployeeMale,
//            	PermanentEmployeeFemale, TemporaryEmployeeMale, TemporaryEmployeeFemale,
//            	LatDeg, LatMin, LatSec, LongDeg, LongMin, LongSec, Elevation, Photo, DateCreated, CreatedBy)
//            VALUES (?,?,?,?,?,?,?,?,   ?,?,?,   ?,?,?,?,?,?,?,?,now(),?)";

        $data = array(
            'Address'                       => $this->input->post('Address'),
            'VillageID'                     => $this->input->post('Desa'),
            'Company'                       => $this->input->post('Name'),
            'CompanyStatus'                 => $this->input->post('Status'),
            'CompanyYear'                   => $this->input->post('Year'),
            'CompanyAlias'                  => $this->input->post('Alias'),
            'Handphone'                     => $this->input->post('Handphone'),
            'NoTelp'                        => $this->input->post('NoTelp'),
            'PermanentEmployeeMale'         => $this->input->post('PermanentEmployeeMale'),
            'PermanentEmployeeFemale'       => $this->input->post('PermanentEmployeeFemale'),
            'TemporaryEmployeeMale'         => $this->input->post('TemporaryEmployeeMale'),
            'TemporaryEmployeeFemale'       => $this->input->post('TemporaryEmployeeFemale'),
            'FamilyMembersMale'             => $this->input->post('FamilyMembersMale'),
            'FamilyMembersFemale'           => $this->input->post('FamilyMembersFemale'),
            'LatDeg'                        => $this->input->post('LatDeg'),
            'LatMin'                        => $this->input->post('LatMin'),
            'Latitude'                      => $this->input->post('LatSec'),
            'LongDeg'                       => $this->input->post('LongDeg'),
            'LongMin'                       => $this->input->post('LongMin'),
            'Longitude'                     => $this->input->post('LongSec'),
            'Elevation'                     => $this->input->post('Elevation'),
            'Photo'                         => $gambar,
            'CreatedBy'                     => $userid,
            'DateCreated'                   => date('Y-m-d H:m:s'),
            'TraderName'                    => $this->input->post('TraderName'),
            'IdentityNum'                   => $this->input->post('IdentityNum'),
            'Education'                     => $this->input->post('Education'),
//            'FamilyMembers'               => $this->input->post('FamilyMembers'),
            'Email'                         => $this->input->post('Email'),
            'Sex'                           => $this->input->post('Sex'),
            'Birthdate'                     => $this->input->post('Birthdate')
        );
        $this->db->insert('ktv_traders', $data);
//        $query = $this->db->query($sql, array($Address, $Handphone,$VillageID, $Company, $CompanyStatus, $CompanyYear, $CompanyAlias,
//            $PermanentEmployeeMale, $PermanentEmployeeFemale, $TemporaryEmployeeMale, $TemporaryEmployeeFemale,
//            $LatDeg, $LatMin, $LatSec, $LongDeg, $LongMin, $LongSec, $Elevation, $Photo,$userid));
        if ($this->db->affected_rows() > 0) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateData($Address, $Handphone,$NoTelp, $VillageID, $Company, $CompanyStatus, $CompanyYear, $CompanyAlias, $PermanentEmployeeMale, $PermanentEmployeeFemale, $TemporaryEmployeeMale, $TemporaryEmployeeFemale, $LatDeg, $LatMin, $LatSec, $LongDeg, $LongMin, $LongSec, $Elevation, $Photo, $userid, $id) {
//        $sql = "
//            update ktv_traders
//            set Address=?,Handphone=?,VillageID=?,Company=?,CompanyStatus=?,CompanyYear=?,CompanyAlias=?,PermanentEmployeeMale=?,
//            	PermanentEmployeeFemale=?,TemporaryEmployeeMale=?,TemporaryEmployeeFemale=?,
//            	LatDeg=?,LatMin=?,LatSec=?,LongDeg=?,LongMin=?,LongSec=?,Elevation=?,Photo=?, DateUpdated=now(), LastModifiedBy=?
//            where TraderID=?";
//        $query = $this->db->query($sql, array($Address, $Handphone, $VillageID, $Company, $CompanyStatus, $CompanyYear, $CompanyAlias,
//            $PermanentEmployeeMale, $PermanentEmployeeFemale, $TemporaryEmployeeMale, $TemporaryEmployeeFemale,
//            $LatDeg, $LatMin, $LatSec, $LongDeg, $LongMin, $LongSec, $Elevation, $Photo, $userid, $id));
        $data = array(
            'Address'                       => $this->input->post('Address'),
            'VillageID'                     => $this->input->post('Desa'),
            'Company'                       => $this->input->post('Name'),
            'CompanyStatus'                 => $this->input->post('Status'),
            'CompanyYear'                   => $this->input->post('Year'),
            'CompanyAlias'                  => $this->input->post('Alias'),
            'Handphone'                     => $this->input->post('Handphone'),
            'NoTelp'                        => $this->input->post('NoTelp'),
            'PermanentEmployeeMale'         => $this->input->post('PermanentEmployeeMale'),
            'PermanentEmployeeFemale'       => $this->input->post('PermanentEmployeeFemale'),
            'TemporaryEmployeeMale'         => $this->input->post('TemporaryEmployeeMale'),
            'TemporaryEmployeeFemale'       => $this->input->post('TemporaryEmployeeFemale'),
            'LatDeg'                        => $this->input->post('LatDeg'),
            'LatMin'                        => $this->input->post('LatMin'),
            'Latitude'                      => $this->input->post('LatSec'),
            'LongDeg'                       => $this->input->post('LongDeg'),
            'LongMin'                       => $this->input->post('LongMin'),
            'Longitude'                     => $this->input->post('LongSec'),
            'Elevation'                     => $this->input->post('Elevation'),
            'Photo'                         => $gambar,
            'LastModifiedBy'                => $userid,
            'DateUpdated'                   => date('Y-m-d H:m:s'),
            'TraderName'                    => $this->input->post('TraderName'),
            'IdentityNum'                   => $this->input->post('IdentityNum'),
            'Education'                     => $this->input->post('Education'),
//            'FamilyMembers'               => $this->input->post('FamilyMembers'),
            'FamilyMembersMale'             => $this->input->post('FamilyMembersMale'),
            'FamilyMembersFemale'           => $this->input->post('FamilyMembersFemale'),
            'Email'                         => $this->input->post('Email'),
            'Sex'                           => $this->input->post('Sex'),
            'Birthdate'                     => $this->input->post('Birthdate')
        );
        $this->db->where('TraderID',$this->input->post('TraderID'));
        $this->db->update('ktv_traders', $data);

        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deleteData($id) {
        //$sql = "DELETE FROM ktv_traders WHERE TraderID=?";
        $sql="UPDATE ktv_traders SET StatusCode = 'nullified',LastModifiedBy='".$_SESSION['userid']."',DateUpdated=NOW() WHERE TraderID = ? LIMIT 1";
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    //staff
    function readStaffs($id) {
//        $query = $this->db->query("select * ktv_trader_staff where TraderID=$id");
        $sql = "
            select %s
            from ktv_trader_staff
            WHERE TraderID=? AND StatusCode != 'nullified'
            ORDER BY StaffName";
        $query = $this->db->query(sprintf($sql, '*,date(StaffBirth) as StaffBirth,IF(StaffGender="1","Laki-laki",IF(StaffGender="2",
            "Perempuan","")) as StaffGende,IF(Education="1","Belum pernah sekolah",IF(Education="2","Tidak tamat SD",
            IF(Education="3","Tamat SD, tidak melanjutkan",IF(Education="4","Tamat SMP",IF(Education="5","Tamat SMA/SMK",
            IF(Education="6","Tamat perguruan tinggi","")))))) as Educatio'), array($id));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql, 'count(*) as total'), array($id));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function createStaff($TraderID, $StaffName, $PrivateCellphone, $PrivateStaffEmail, $StaffBirth, $StaffGender, $IdentityNumber, $Education, $FamilyMembers, $Address, $Position, $userid) {
        $this->db->trans_start();
        //user
        $sql_user = "
            INSERT INTO sys_user(UserRealName,UserName,UserActive)
            VALUES (?,?,?)";
        $query = $this->db->query($sql_user, array($StaffName, $PrivateStaffEmail, 'No'));
        $user = $this->db->insert_id();
        $sql_user_group = "
            INSERT INTO sys_user_group(UserGroupUserId,UserGroupGroupId,UserGroupIsDefault)
            values (?,?,'1')";
        $query = $this->db->query($sql_user_group, array($user, null));
        //end user
        $sql = "
            insert into ktv_trader_staff (TraderID,StaffName,PrivateCellphone,PrivateStaffEmail,StaffBirth,StaffGender,
               IdentityNumber,Education,FamilyMembers,Address,Position, UserId,DateCreated, CreatedBy)
            VALUES (?,?,?,?,?,?,   ?,?,?,?,?,?,now(),?)";
        $query = $this->db->query($sql, array($TraderID, $StaffName, $PrivateCellphone, $PrivateStaffEmail, $StaffBirth, $StaffGender,
            $IdentityNumber, $Education, $FamilyMembers, $Address, $Position, $user, $userid));
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateStaff($TraderID, $StaffName, $PrivateCellphone, $PrivateStaffEmail, $StaffBirth, $StaffGender, $IdentityNumber, $Education, $FamilyMembers, $Address, $Position, $userid, $id) {
        $sql = "
            update ktv_trader_staff
            set TraderID=?,StaffName=?,PrivateCellphone=?,PrivateStaffEmail=?,StaffBirth=?,StaffGender=?,
               IdentityNumber=?,Education=?,FamilyMembers=?,Address=?, Position=?,DateUpdated=now(), LastModifiedBy=?
            where TraderStaffID=?";
        $query = $this->db->query($sql, array($TraderID, $StaffName, $PrivateCellphone, $PrivateStaffEmail, $StaffBirth, $StaffGender,
            $IdentityNumber, $Education, $FamilyMembers, $Address, $Position, $userid, $id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deleteStaff($id) {
        //$sql = "DELETE FROM ktv_trader_staff WHERE TraderStaffID=?";
        $sql="UPDATE ktv_trader_staff SET StatusCode = 'nullified',LastModifiedBy='".$_SESSION['userid']."',DateUpdated=NOW() WHERE TraderStaffID = ? LIMIT 1";
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    //end staff
    //Quality Standard
    function readQualityStandards($id) {
        $sql = "
            select %s
            from ktv_trace_quality_standard
            WHERE StandardTraderID=?
            ORDER BY StandardName";
        $query = $this->db->query(sprintf($sql, '*'), array($id));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql, 'count(*) as total'), array($id));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function readQualityStandard($id) {
        $sql = "
            select *
            from ktv_trace_quality_standard
            WHERE StandardID=?";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

    function readQualityStandardCombos($id) {
        $sql = "
            select StandardID as id,StandardName as label
            from ktv_trace_quality_standard
            WHERE StandardTraderID=?
            ORDER BY StandardName";
        $query = $this->db->query($sql, array($id));
        $result['data'] = $query->result_array();
        return $result;
    }

    function createQualityStandard($TraderID, $StandardName, $Moisture, $BeanCount, $Waste, $Mouldy, $Insect, $Slaty, $userid) {
        $sql = "
            insert into ktv_trace_quality_standard (StandardTraderID, StandardName, Moisture, BeanCount, Waste, Mouldy, Insect,
               Slaty, DateCreated, CreatedBy)
            VALUES (?,?,?,?,?,?,?,   ?,now(),?)";
        $query = $this->db->query($sql, array($TraderID, $StandardName, $Moisture, $BeanCount, $Waste, $Mouldy,
            $Insect, $Slaty, $userid));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateQualityStandard($TraderID, $StandardName, $Moisture, $BeanCount, $Waste, $Mouldy, $Insect, $Slaty, $userid, $id) {
        $sql = "
            update ktv_trace_quality_standard
            set StandardTraderID=?, StandardName=?, Moisture=?, BeanCount=?, Waste=?, Mouldy=?, Insect=?,
               Slaty=?, DateUpdated=now(), LastModifiedBy=?
            where StandardID=?";
        $query = $this->db->query($sql, array($TraderID, $StandardName, $Moisture, $BeanCount, $Waste, $Mouldy,
            $Insect, $Slaty, $userid, $id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deleteQualityStandard($id) {
        $sql = "
            DELETE FROM ktv_trace_quality_standard WHERE StandardID=?";
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    //end Quality
    //Quality
    function readQualitys($id) {
        $sql = "
            select %s
            from ktv_trace_quality a
            LEFT JOIN ktv_trace_quality_standard b on a.StandardID=b.StandardID
            WHERE QualityTraderID=?
            ORDER BY QualityDate desc";
        $query = $this->db->query(sprintf($sql, '*'), array($id));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql, 'count(*) as total'), array($id));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function createQuality($QualityTraderID, $QualityDate, $StandardID, $Moisture, $BeanCount, $Waste, $Mouldy, $Insect, $Slaty, $userid) {
        $sql = "
            insert into ktv_trace_quality (QualityTraderID, QualityDate, StandardID, Moisture, BeanCount, Waste, Mouldy, Insect,
               Slaty, DateCreated, CreatedBy)
            VALUES (?,?,?,?,?,?,?,?,   ?,now(),?)";
        $query = $this->db->query($sql, array($QualityTraderID, $QualityDate, $StandardID, $Moisture, $BeanCount, $Waste, $Mouldy,
            $Insect, $Slaty, $userid));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateQuality($QualityTraderID, $QualityDate, $StandardID, $Moisture, $BeanCount, $Waste, $Mouldy, $Insect, $Slaty, $userid, $id) {
        $sql = "
            update ktv_trace_quality
            set QualityTraderID=?, QualityDate=?, StandardID=?,Moisture=?, BeanCount=?, Waste=?, Mouldy=?, Insect=?,
               Slaty=?, DateUpdated=now(), LastModifiedBy=?
            where QualityID=?";
        $query = $this->db->query($sql, array($QualityTraderID, $QualityDate, $StandardID, $Moisture, $BeanCount, $Waste, $Mouldy,
            $Insect, $Slaty, $userid, $id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deleteQuality($id) {
        $sql = "
            DELETE FROM ktv_trace_quality WHERE QualityID=?";
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    //end Quality
    //Price
    function readPrices($id) {
        $sql = "
            select %s
            from ktv_trace_price ktp
            left join ktv_district kd on ktp.DistrictID=kd.DistrictID
            WHERE PriceTraderID=?
            ORDER BY PriceDate desc";
        $query = $this->db->query(sprintf($sql, '*'), array($id));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql, 'count(*) as total'), array($id));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function createPrice($PriceTraderID, $PriceDate, $Price, $districtID, $userid) {
        $sql = "
            insert into ktv_trace_price (PriceTraderID,PriceDate,Price, DateCreated, CreatedBy,DistrictID)
            VALUES (?,?,?,now(),?,((SELECT DistrictID from ktv_district where District=?)))";
        $query = $this->db->query($sql, array($PriceTraderID, $PriceDate, $Price, $userid, $districtID));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updatePrice($PriceTraderID, $PriceDate, $Price, $districtID, $userid, $id) {
        $sql = "
            update ktv_trace_price
            set PriceTraderID=?,PriceDate=?,Price=?, DateUpdated=now(), LastModifiedBy=?,DistrictID=(SELECT DistrictID from ktv_district where District=?)
            where PriceID=?";
        $query = $this->db->query($sql, array($PriceTraderID, $PriceDate, $Price, $userid, $districtID, $id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deletePrice($id) {
        $sql = "
            DELETE FROM ktv_trace_price WHERE PriceID=?";
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    //end Price
    //Package
    function readPackages($id) {
        $sql = "
            select %s
            from ktv_trace_package
            WHERE PackageTraderID=?
            ORDER BY PackageType";
        $query = $this->db->query(sprintf($sql, '*'), array($id));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql, 'count(*) as total'), array($id));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function createPackage($PackageTraderID, $PackageType, $PackageWeight, $userid) {
        $sql = "
            insert into ktv_trace_package (PackageTraderID,PackageType,PackageWeight, DateCreated, CreatedBy)
            VALUES (?,?,?,now(),?)";
        $query = $this->db->query($sql, array($PackageTraderID, $PackageType, $PackageWeight, $userid));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updatePackage($PackageTraderID, $PackageType, $PackageWeight, $userid, $id) {
        $sql = "
            update ktv_trace_package
            set PackageTraderID=?,PackageType=?,PackageWeight=?, DateUpdated=now(), LastModifiedBy=?
            where PackageID=?";
        $query = $this->db->query($sql, array($PackageTraderID, $PackageType, $PackageWeight, $userid, $id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deletePackage($id) {
        $sql = "
            DELETE FROM ktv_trace_package WHERE PackageID=?";
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    //end Price

    public function updateLocation($lat, $lng, $id)
    {
        $sql = "
UPDATE `ktv_traders`
SET
    Longitude = ?,
    Latitude = ?
WHERE
    TraderID = ?
        ";
        return $this->db->query($sql, array($lat, $lng, $id));
    }

    public function listTrader($province=null,$district=null,$subdistrict=null)
    {
        $this->db->select('c.TraderID AS id, c.TraderName AS name');
        $this->db->from('ktv_traders c');
        $this->db->join('ktv_village v', 'v.VillageID = c.VillageID');
        $this->db->join('ktv_subdistrict sd', 'sd.SubDistrictID = v.SubDistrictID');
        $this->db->join('ktv_district d', 'd.DistrictID = sd.DistrictID');
        $this->db->join('ktv_province p', 'p.ProvinceID = d.ProvinceID');
        if (!empty($province)) {
            $this->db->where('p.ProvinceID', $province, FALSE);
        }
        if (!empty($district)) {
            $this->db->where('d.DistrictID', $district, FALSE);
        }
        if (!empty($subdistrict)) {
            $this->db->where('sd.SubDistrictID', $subdistrict, FALSE);
        }
        $this->db->order_by('name', 'asc');
        $query = $this->db->get();
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
    }

    public function readDataNurseyNumbers($ObjType, $ObjID) {
        $sql = "SELECT *, (Panjang*Lebar) AS Luas FROM ktv_nursery WHERE ObjType=? AND ObjID=? ORDER BY NurseryNr";
        $query = $this->db->query($sql, array($ObjType, $ObjID));
        $result['data'] = $query->result_array();
        return $result;
    }

    function deleteNursery($id) {
        $this->db->trans_start();
        $sql="DELETE FROM ktv_nursery_area WHERE NurseryID=?";
        $query = $this->db->query($sql, array($id));

        $sql2="DELETE FROM ktv_nursery WHERE NurseryID=?";
        $query2 = $this->db->query($sql2, array($id));

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Record deleted.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    function getNurseryPolygon($NurseryID,$NurseryNr,$lat,$long){
    	if($NurseryNr!=''){
            $sql = "SELECT Latitude, Longitude FROM ktv_nursery_area WHERE NurseryID=? AND NurseryNr=?";
            $query = $this->db->query($sql, array($NurseryID,$NurseryNr));
            //$result = $query->result_array();
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
                if( ($lat!='0.000000' || $long!='0.000000') && ($lat!='' || $long!='') ){
                    return "[[$lat,$long]]";
                }else{
                   	return "[[-1.2674336,113.6939433]]";
                }

            }
        }else{
            return "''";
        }
    }

    function updateNurseryPolygon($NurseryID, $NurseryNr, $area, $lat, $long){
        $result = false;
        $this->db->trans_start(FALSE);
        $this->db->where('NurseryID', $NurseryID);
        $this->db->where('NurseryNr', $NurseryNr);
        $this->db->delete('ktv_nursery_area');

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
        }
        $sql = "SELECT Latitude,Longitude FROM ktv_nursery WHERE NurseryID=? AND NurseryNr=?";
        $query = $this->db->query($sql, array($NurseryID, $NurseryNr))->row();
        if(($query->Latitude=='' && $query->Longitude=='') || ($query->Latitude=='0.000000' && $query->Longitude=='0.000000')){
        	if($lat!='0' && $long!='0'){
        		$sql_update = "UPDATE ktv_nursery SET Latitude=?, Longitude=? WHERE NurseryID=? AND NurseryNr=?";
        		$this->db->query($sql_update, array($lat, $long, $NurseryID, $NurseryNr));
        	}
        }
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
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

    function readNurseryArea($ObjType,$ObjID,$NurseryID,$NurseryNr){
		$sql = "SELECT Latitude,Longitude FROM ktv_nursery WHERE ObjType=? AND ObjID=? AND NurseryID=? AND NurseryNr=?";
        $query = $this->db->query($sql, array($ObjType, $ObjID, $NurseryID, $NurseryNr));
		$result= $query->result_array();
		return $result[0];
	}

	function updateClonalPolygonCenter($NurseryID, $NurseryNr, $lat, $long){
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
    public function readProvinsis() {
        $sql = "
            SELECT distinct Province as label,ProvinceID as id
            FROM ktv_province
            WHERE ProvinceID>0
            ORDER BY Province";
        $query = $this->db->query($sql);

        $result['data'] = $query->result_array();
        return $result;
    }
    public function readKabupatens($Province) {
        $sql = "
            SELECT distinct District as label,DistrictID as id
            FROM ktv_district d
            LEFT JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
            WHERE (p.ProvinceID = ? OR p.Province = ?)
            ORDER BY label";
        $query = $this->db->query($sql, array($Province, $Province));

        $result['data'] = $query->result_array();
        return $result;
    }
    public function readKecamatans($District) {
        $sql = "
            SELECT distinct SubDistrict as label,SubDistrictID as id
            FROM ktv_subdistrict sd
            lEFT JOIN ktv_district d ON d.DistrictID = sd.DistrictID
            WHERE (d.DistrictID = ? OR d.District = ?)
            ORDER BY label";
        $query = $this->db->query($sql, array($District, $District));

        $result['data'] = $query->result_array();
        return $result;
    }
    public function readDesas($SubDistrict) {
        $sql = "
            SELECT distinct Village as label,VillageID as id
            FROM ktv_village v
            LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
            WHERE (sd.SubDistrictID = ? OR sd.SubDistrict = ?)
            ORDER BY label";
        $query = $this->db->query($sql, array($SubDistrict, $SubDistrict));
        $result['data'] = $query->result_array();
        return $result;
    }

    public function getTraderSurList($TraderID){
        $sql="SELECT
                a.TraderSurID
                , a.SurveyYear
                , a.InterviewDate
                , a.`DateCreated`
                , IFNULL((SELECT UserRealName FROM sys_user WHERE UserId = a.`CreatedBy`),'-') AS CreatedBy
                , IFNULL(a.`DateUpdated`,'-') AS DateUpdated
                , IFNULL((SELECT UserRealName FROM sys_user WHERE UserId = a.`LastModifiedBy`),'-') AS LastModifiedBy
            FROM
                ktv_trader_surveys a
            WHERE
                a.`StatusCode` = 'active'
                AND a.`ObjType` = 'trader'
                AND a.`ObjID` = ?
            ORDER BY a.`SurveyYear` ASC";
        $query = $this->db->query($sql,array((int) $TraderID));
        $data = $query->result_array();

        $result['data'] = $data;
        return $result;
    }

    public function getFormTraderSurvey($TraderSurID){
        $sql="SELECT
              a.`TraderSurID` AS tSurTraderSurID,
              a.`SurveyYear` AS tSurSurveyYear,
              a.`InterviewDate` AS tSurInterviewDate,
              a.`Name` AS tSurName,
              a.`CompanyName` AS tSurCompanyName,
              a.`BirthDate` AS tSurBirthDate,
              a.`Address` AS tSurAddress,
              a.`NoKTP` AS tSurNoKTP,
              a.`Gender` AS tSurGender,
              a.`Handphone` AS tSurHandphone,
              a.`Email` AS tSurEmail,
              a.`Latitude` AS tSurLatitude,
              a.`Longitude` AS tSurLongitude,
              a.`LastEducation` AS tSurLastEducation,
              a.`FulltimeTrader` AS tSurFulltimeTrader,
              a.`StatusTrader` AS tSurStatusTrader,
              a.`YearRunningTrader` AS tSurYearRunningTrader,
              a.`NrFulltimeStaffMale` AS tSurNrFulltimeStaffMale,
              a.`NrFulltimeStaffFemale` AS tSurNrFulltimeStaffFemale,
              a.`NrParttimeStaffMale` AS tSurNrParttimeStaffMale,
              a.`NrParttimeStaffFemale` AS tSurNrParttimeStaffFemale,
              a.`ComodityCacaoSalePercentage` AS tSurComodityCacaoSalePercentage,
              a.`ComodityOtherSalePercentage` AS tSurComodityOtherSalePercentage,
              a.`BuyWetBeans` AS tSurBuyWetBeans,
              a.`BuyFermentBeans` AS tSurBuyFermentBeans,
              a.`BuyDryBeans` AS tSurBuyDryBeans,
              a.`NrTransWetBeansHighHarvest` AS tSurNrTransWetBeansHighHarvest,
              a.`NrVolumeWetBeansHighHarvest` AS tSurNrVolumeWetBeansHighHarvest,
              a.`NrTransWetBeansNormalHarvest` AS tSurNrTransWetBeansNormalHarvest,
              a.`NrVolumeWetBeansNormalHarvest` AS tSurNrVolumeWetBeansNormalHarvest,
              a.`NrTransWetBeansLowHarvest` AS tSurNrTransWetBeansLowHarvest,
              a.`NrVolumeWetBeansLowHarvest` AS tSurNrVolumeWetBeansLowHarvest,
              a.`NrTransFermentBeansHighHarvest` AS tSurNrTransFermentBeansHighHarvest,
              a.`NrVolumeFermentBeansHighHarvest` AS tSurNrVolumeFermentBeansHighHarvest,
              a.`NrTransFermentBeansNormalHarvest` AS tSurNrTransFermentBeansNormalHarvest,
              a.`NrVolumeFermentBeansNormalHarvest` AS tSurNrVolumeFermentBeansNormalHarvest,
              a.`NrTransFermentBeansLowHarvest` AS tSurNrTransFermentBeansLowHarvest,
              a.`NrVolumeFermentBeansLowHarvest` AS tSurNrVolumeFermentBeansLowHarvest,
              a.`NrTransDryBeansHighHarvest` AS tSurNrTransDryBeansHighHarvest,
              a.`NrVolumeDryBeansHighHarvest` AS tSurNrVolumeDryBeansHighHarvest,
              a.`NrTransDryBeansNormalHarvest` AS tSurNrTransDryBeansNormalHarvest,
              a.`NrVolumeDryBeansNormalHarvest` AS tSurNrVolumeDryBeansNormalHarvest,
              a.`NrTransDryBeansLowHarvest` AS tSurNrTransDryBeansLowHarvest,
              a.`NrVolumeDryBeansLowHarvest` AS tSurNrVolumeDryBeansLowHarvest,
              a.`NrCacaoFrequentBuyer` AS tSurNrCacaoFrequentBuyer,
              a.`NrCacaoNormalBuyer` AS tSurNrCacaoNormalBuyer,
              a.`CacaoActivitySellBuyDryBeans` AS tSurCacaoActivitySellBuyDryBeans,
              a.`CacaoActivitySellBuyFermentBeans` AS tSurCacaoActivitySellBuyFermentBeans,
              a.`CacaoActivitySellPest` AS tSurCacaoActivitySellPest,
              a.`CacaoActivitySellFertilizer` AS tSurCacaoActivitySellFertilizer,
              a.`CacaoActivityLoanToFarmer` AS tSurCacaoActivityLoanToFarmer,
              a.`UseToolDigitalScale` AS tSurUseToolDigitalScale,
              a.`UseToolManualScale` AS tSurUseToolManualScale,
              a.`UseToolAquaboy` AS tSurUseToolAquaboy,
              a.`UseToolSolarDryer` AS tSurUseToolSolarDryer,
              a.`UseToolFuelDryer` AS tSurUseToolFuelDryer,
              a.`UseToolAyakMachine` AS tSurUseToolAyakMachine,
              a.`UseToolFloorDryer` AS tSurUseToolFloorDryer,
              a.`UseToolWarehouse` AS tSurUseToolWarehouse,
              a.`UseToolFermentBox` AS tSurUseToolFermentBox,
              a.`FundValueFromSelf` AS tSurFundValueFromSelf,
              a.`FundValueFromLoan` AS tSurFundValueFromLoan,
              a.`PriceSource` AS tSurPriceSource,
              a.`AverageMarginCacaoPerKgReceivedMin` AS tSurAverageMarginCacaoPerKgReceivedMin,
              a.`AverageMarginCacaoPerKgReceivedMax` AS tSurAverageMarginCacaoPerKgReceivedMax,
              a.`QualityCheckCacaoBeans` AS tSurQualityCheckCacaoBeans,
              a.`WhenPayClient` AS tSurWhenPayClient,
              a.`PayClientMethod` AS tSurPayClientMethod,
              a.`SellCertifiedCacaoBeans` AS tSurSellCertifiedCacaoBeans,
              a.`KnownCertifiedCacaoBeans` AS tSurKnownCertifiedCacaoBeans,
              a.`KnownNonCertifiedCacaoBeans` AS tSurKnownNonCertifiedCacaoBeans,
              a.`UseSystemTraceCertifiedCacaoBeans` AS tSurUseSystemTraceCertifiedCacaoBeans,
              a.`UseSystemTraceNonCertifiedCacaoBeans` AS tSurUseSystemTraceNonCertifiedCacaoBeans,
              a.`TraceSellingCertifiedCacaoBeans` AS tSurTraceSellingCertifiedCacaoBeans,
              a.`TraceSellingNonCertifiedCacaoBeans` AS tSurTraceSellingNonCertifiedCacaoBeans,
              a.`RecordTransCertifiedCacaoBeans` AS tSurRecordTransCertifiedCacaoBeans,
              a.`RecordTransNonCertifiedCacaoBeans` AS tSurRecordTransNonCertifiedCacaoBeans,
              a.`AnalyzeTransCertifiedCacaoBeans` AS tSurAnalyzeTransCertifiedCacaoBeans,
              a.`AnalyzeTransNonCertifiedCacaoBeans` AS tSurAnalyzeTransNonCertifiedCacaoBeans,
              a.`ShowAnalyzeResult` AS tSurShowAnalyzeResult,
              a.`BusinessModel` AS tSurBusinessModel,
              a.`BusinessModelOther` AS tSurBusinessModelOther,
              a.`SellToBigTrader` AS tSurSellToBigTrader,
              a.`SellToCoop` AS tSurSellToCoop,
              a.`SellToBigCompany` AS tSurSellToBigCompany,
              a.`SellToFactory` AS tSurSellToFactory,
              a.`SellToExport` AS tSurSellToExport,
              a.`SellToLoaner` AS tSurSellToLoaner,
              a.`SellToOther` AS tSurSellToOther,
              a.`SellToOtherText` AS tSurSellToOtherText,
              a.`ChooseBuyerContract` AS tSurChooseBuyerContract,
              a.`ChooseBuyerHighestValue` AS tSurChooseBuyerHighestValue,
              a.`ChooseBuyerDistance` AS tSurChooseBuyerDistance,
              a.`ChooseBuyerFastPayment` AS tSurChooseBuyerFastPayment,
              a.`ChooseBuyerFacility` AS tSurChooseBuyerFacility,
              a.`ChooseBuyerFundingSource` AS tSurChooseBuyerFundingSource,
              a.`BuyerInfoDetail` AS tSurBuyerInfoDetail,
              a.`ProblemBuycacaoFund` AS tSurProblemBuycacaoFund,
              a.`ProblemBuycacaoQuality` AS tSurProblemBuycacaoQuality,
              a.`ProblemBuycacaoTransport` AS tSurProblemBuycacaoTransport,
              a.`ProblemBuycacaoPriceFluc` AS tSurProblemBuycacaoPriceFluc,
              a.`ProblemBuycacaoPriceComp` AS tSurProblemBuycacaoPriceComp,
              a.`IsCacaoFarmer` AS tSurIsCacaoFarmer,
              a.`CacaoLandSize` AS tSurCacaoLandSize,
              a.`AverageProduction` AS tSurAverageProduction,
              a.`IsExCacaoFarmer` AS tSurIsExCacaoFarmer,
              a.`ExCacaoLandSize` AS tSurExCacaoLandSize,
              a.`ExAverageProduction` AS tSurExAverageProduction,
              a.`ProvideFertPest` AS tSurProvideFertPest,
              a.`ProvideLoan` AS tSurProvideLoan,
              a.`LoanCreditCount` AS tSurLoanCreditCount,
              a.`LoanCreditValueTotal` AS tSurLoanCreditValueTotal,
              a.`PayLoanMethod` AS tSurPayLoanMethod,
              a.`PayLoanMethodOther` AS tSurPayLoanMethodOther,
              a.`LoanerHaveTo` AS tSurLoanerHaveTo,
              a.`LossAction` AS tSurLossAction,
              a.`IsBankAgent` AS tSurIsBankAgent,
              a.`HaveOtherBusiness` AS tSurHaveOtherBusiness,
              a.`FarmerMainProblemLowProd` AS tSurFarmerMainProblemLowProd,
              a.`FarmerMainProblemOldTree` AS tSurFarmerMainProblemOldTree,
              a.`FarmerMainProblemNoKnowledge` AS tSurFarmerMainProblemNoKnowledge,
              a.`FarmerMainProblemPest` AS tSurFarmerMainProblemPest,
              a.`FarmerMainProblemPestSolving` AS tSurFarmerMainProblemPestSolving,
              a.`FarmerMainProblemSeasonChanging` AS tSurFarmerMainProblemSeasonChanging,
              a.`FarmerMainProblemDisease` AS tSurFarmerMainProblemDisease,
              a.`FarmerMainProblemLand` AS tSurFarmerMainProblemLand,
              a.`FarmerMainProblemLackSkill` AS tSurFarmerMainProblemLackSkill,
              a.`FarmerMainProblemOtherComodity` AS tSurFarmerMainProblemOtherComodity,
              a.`FarmerMainProblemLowPrice` AS tSurFarmerMainProblemLowPrice,
              a.`MorethanOneBankAcc` AS tSurMorethanOneBankAcc,
              a.`BankTransactionFreq` AS tSurBankTransactionFreq,
              a.`SavingAsideFund` AS tSurSavingAsideFund,
              a.`FundValue` AS tSurFundValue,
              a.`HaveLoanBefore` AS tSurHaveLoanBefore,
              a.`LastLoanValue` AS tSurLastLoanValue,
              a.`LastLoanSettle` AS tSurLastLoanSettle,
              a.`LastLoanCreditValue` AS tSurLastLoanCreditValue,
              a.`LastLoanSourceTrader` AS tSurLastLoanSourceTrader,
              a.`LastLoanSourceFamily` AS tSurLastLoanSourceFamily,
              a.`LastLoanSourceLoaner` AS tSurLastLoanSourceLoaner,
              a.`LastLoanSourceBank` AS tSurLastLoanSourceBank,
              a.`LastLoanSourceCoop` AS tSurLastLoanSourceCoop,
              a.`LastLoanSourceOther` AS tSurLastLoanSourceOther,
              a.`LastLoanSourceOtherText` AS tSurLastLoanSourceOtherText,
              a.`PayingStaffFixedSalary` AS tSurPayingStaffFixedSalary,
              a.`PayingStaffCommision` AS tSurPayingStaffCommision,
              a.`PayingStaffFamilyNoPayment` AS tSurPayingStaffFamilyNoPayment,
              a.`TrustedTrader` AS tSurTrustedTrader,
              a.`NeedLoanAndQualify` AS tSurNeedLoanAndQualify,
              a.`CacaoTraderIsProfitable` AS tSurCacaoTraderIsProfitable,
              a.`WealthyPersonInSociety` AS tSurWealthyPersonInSociety
            FROM
                `ktv_trader_surveys` a
            WHERE
                a.`TraderSurID` = ?
            LIMIT 1";
        $query = $this->db->query($sql,array((int) $TraderSurID));

        $return['success'] = true;
        $return['data'] = $query->row_array();
        return $return;
    }

    public function insertTraderSurvey($post){
        $this->db->trans_start();

        //tambahkan yg perlu
        $post['ObjID'] = $post['tSurTraderID'];
        $post['ObjType'] = 'trader';

        //yg tidak diperlukan untuk insert
        unset($post['tSurTraderName']);
        unset($post['tSurTraderID']);

        foreach ($post as $k => $v) {
            $k = str_replace("tSur", "", $k);
            $insert[$k] = $v;

            //cek yg perlu default value
            if ($insert[$k] == "")
                $insert[$k] = NULL;
        }

        $insert['StatusCode'] = 'active';
        $insert['DateCreated'] = date('Y-m-d H:i:s');
        $insert['CreatedBy'] = $_SESSION['userid'];
        $this->db->insert('ktv_trader_surveys', $insert);

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Data saved";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        }
        return $results;
    }

    public function updateTraderSurvey($post){
        $this->db->trans_start();

        //tambahkan yg perlu
        $TraderSurID = $post['tSurTraderSurID'];
        $post['ObjID'] = $post['tSurTraderID'];
        $post['ObjType'] = 'trader';

        //yg tidak diperlukan untuk update
        unset($post['tSurTraderSurID']);
        unset($post['tSurTraderName']);
        unset($post['tSurTraderID']);

        foreach ($post as $k => $v) {
            $k = str_replace("tSur", "", $k);
            $update[$k] = $v;

            //cek yg perlu default value
            if ($update[$k] == "")
                $update[$k] = NULL;
        }
        $update['DateUpdated'] = date('Y-m-d H:i:s');
        $update['LastModifiedBy'] = $_SESSION['userid'];

        //reset semuanya dulu..
        $sql="UPDATE `ktv_trader_surveys` SET
                  `Name` = NULL,
                  `CompanyName` = NULL,
                  `BirthDate` = NULL,
                  `Address` = NULL,
                  `NoKTP` = NULL,
                  `Gender` = NULL,
                  `Handphone` = NULL,
                  `Email` = NULL,
                  `Latitude` = NULL,
                  `Longitude` = NULL,
                  `LastEducation` = NULL,
                  `FulltimeTrader` = NULL,
                  `StatusTrader` = NULL,
                  `YearRunningTrader` = NULL,
                  `NrFulltimeStaffMale` = NULL,
                  `NrFulltimeStaffFemale` = NULL,
                  `NrParttimeStaffMale` = NULL,
                  `NrParttimeStaffFemale` = NULL,
                  `ComodityCacaoSalePercentage` = NULL,
                  `ComodityOtherSalePercentage` = NULL,
                  `BuyWetBeans` = NULL,
                  `BuyFermentBeans` = NULL,
                  `BuyDryBeans` = NULL,
                  `NrTransWetBeansHighHarvest` = NULL,
                  `NrVolumeWetBeansHighHarvest` = NULL,
                  `NrTransWetBeansNormalHarvest` = NULL,
                  `NrVolumeWetBeansNormalHarvest` = NULL,
                  `NrTransWetBeansLowHarvest` = NULL,
                  `NrVolumeWetBeansLowHarvest` = NULL,
                  `NrTransFermentBeansHighHarvest` = NULL,
                  `NrVolumeFermentBeansHighHarvest` = NULL,
                  `NrTransFermentBeansNormalHarvest` = NULL,
                  `NrVolumeFermentBeansNormalHarvest` = NULL,
                  `NrTransFermentBeansLowHarvest` = NULL,
                  `NrVolumeFermentBeansLowHarvest` = NULL,
                  `NrTransDryBeansHighHarvest` = NULL,
                  `NrVolumeDryBeansHighHarvest` = NULL,
                  `NrTransDryBeansNormalHarvest` = NULL,
                  `NrVolumeDryBeansNormalHarvest` = NULL,
                  `NrTransDryBeansLowHarvest` = NULL,
                  `NrVolumeDryBeansLowHarvest` = NULL,
                  `NrCacaoFrequentBuyer` = NULL,
                  `NrCacaoNormalBuyer` = NULL,
                  `CacaoActivitySellBuyDryBeans` = NULL,
                  `CacaoActivitySellBuyFermentBeans` = NULL,
                  `CacaoActivitySellPest` = NULL,
                  `CacaoActivitySellFertilizer` = NULL,
                  `CacaoActivityLoanToFarmer` = NULL,
                  `UseToolDigitalScale` = NULL,
                  `UseToolManualScale` = NULL,
                  `UseToolAquaboy` = NULL,
                  `UseToolSolarDryer` = NULL,
                  `UseToolFuelDryer` = NULL,
                  `UseToolAyakMachine` = NULL,
                  `UseToolFloorDryer` = NULL,
                  `UseToolWarehouse` = NULL,
                  `UseToolFermentBox` = NULL,
                  `FundValueFromSelf` = NULL,
                  `FundValueFromLoan` = NULL,
                  `PriceSource` = NULL,
                  `AverageMarginCacaoPerKgReceivedMin` = NULL,
                  `AverageMarginCacaoPerKgReceivedMax` = NULL,
                  `QualityCheckCacaoBeans` = NULL,
                  `WhenPayClient` = NULL,
                  `PayClientMethod` = NULL,
                  `SellCertifiedCacaoBeans` = NULL,
                  `KnownCertifiedCacaoBeans` = NULL,
                  `KnownNonCertifiedCacaoBeans` = NULL,
                  `UseSystemTraceCertifiedCacaoBeans` = NULL,
                  `UseSystemTraceNonCertifiedCacaoBeans` = NULL,
                  `TraceSellingCertifiedCacaoBeans` = NULL,
                  `TraceSellingNonCertifiedCacaoBeans` = NULL,
                  `RecordTransCertifiedCacaoBeans` = NULL,
                  `RecordTransNonCertifiedCacaoBeans` = NULL,
                  `AnalyzeTransCertifiedCacaoBeans` = NULL,
                  `AnalyzeTransNonCertifiedCacaoBeans` = NULL,
                  `ShowAnalyzeResult` = NULL,
                  `BusinessModel` = NULL,
                  `BusinessModelOther` = NULL,
                  `SellToBigTrader` = NULL,
                  `SellToCoop` = NULL,
                  `SellToBigCompany` = NULL,
                  `SellToFactory` = NULL,
                  `SellToExport` = NULL,
                  `SellToLoaner` = NULL,
                  `SellToOther` = NULL,
                  `SellToOtherText` = NULL,
                  `ChooseBuyerContract` = NULL,
                  `ChooseBuyerHighestValue` = NULL,
                  `ChooseBuyerDistance` = NULL,
                  `ChooseBuyerFastPayment` = NULL,
                  `ChooseBuyerFacility` = NULL,
                  `ChooseBuyerFundingSource` = NULL,
                  `BuyerInfoDetail` = NULL,
                  `ProblemBuycacaoFund` = NULL,
                  `ProblemBuycacaoQuality` = NULL,
                  `ProblemBuycacaoTransport` = NULL,
                  `ProblemBuycacaoPriceFluc` = NULL,
                  `ProblemBuycacaoPriceComp` = NULL,
                  `IsCacaoFarmer` = NULL,
                  `CacaoLandSize` = NULL,
                  `AverageProduction` = NULL,
                  `IsExCacaoFarmer` = NULL,
                  `ExCacaoLandSize` = NULL,
                  `ExAverageProduction` = NULL,
                  `ProvideFertPest` = NULL,
                  `ProvideLoan` = NULL,
                  `LoanCreditCount` = NULL,
                  `LoanCreditValueTotal` = NULL,
                  `PayLoanMethod` = NULL,
                  `PayLoanMethodOther` = NULL,
                  `LoanerHaveTo` = NULL,
                  `LossAction` = NULL,
                  `IsBankAgent` = NULL,
                  `HaveOtherBusiness` = NULL,
                  `FarmerMainProblemLowProd` = NULL,
                  `FarmerMainProblemOldTree` = NULL,
                  `FarmerMainProblemNoKnowledge` = NULL,
                  `FarmerMainProblemPest` = NULL,
                  `FarmerMainProblemPestSolving` = NULL,
                  `FarmerMainProblemSeasonChanging` = NULL,
                  `FarmerMainProblemDisease` = NULL,
                  `FarmerMainProblemLand` = NULL,
                  `FarmerMainProblemLackSkill` = NULL,
                  `FarmerMainProblemOtherComodity` = NULL,
                  `FarmerMainProblemLowPrice` = NULL,
                  `MorethanOneBankAcc` = NULL,
                  `BankTransactionFreq` = NULL,
                  `SavingAsideFund` = NULL,
                  `FundValue` = NULL,
                  `HaveLoanBefore` = NULL,
                  `LastLoanValue` = NULL,
                  `LastLoanSettle` = NULL,
                  `LastLoanCreditValue` = NULL,
                  `LastLoanSourceTrader` = NULL,
                  `LastLoanSourceFamily` = NULL,
                  `LastLoanSourceLoaner` = NULL,
                  `LastLoanSourceBank` = NULL,
                  `LastLoanSourceCoop` = NULL,
                  `LastLoanSourceOther` = NULL,
                  `LastLoanSourceOtherText` = NULL,
                  `PayingStaffFixedSalary` = NULL,
                  `PayingStaffCommision` = NULL,
                  `PayingStaffFamilyNoPayment` = NULL,
                  `TrustedTrader` = NULL,
                  `NeedLoanAndQualify` = NULL,
                  `CacaoTraderIsProfitable` = NULL,
                  `WealthyPersonInSociety` = NULL
                WHERE
                    `TraderSurID` = ?
                LIMIT 1";
        $query = $this->db->query($sql, array($TraderSurID));

        $this->db->where('TraderSurID', $TraderSurID);
        $query = $this->db->update('ktv_trader_surveys', $update);

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Data saved";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        }
        return $results;
    }

    public function deleteTraderSurvey($TraderSurID){
        $this->db->trans_begin();

        $sql="INSERT INTO `his_ktv_trader_surveys` (
                  `DateHistory`,
                  `DeleteBy`,
                  `TraderSurID`,
                  `ObjType`,
                  `ObjID`,
                  `SurveyYear`,
                  `InterviewDate`,
                  `Name`,
                  `CompanyName`,
                  `BirthDate`,
                  `Address`,
                  `NoKTP`,
                  `Gender`,
                  `Handphone`,
                  `Email`,
                  `Latitude`,
                  `Longitude`,
                  `LastEducation`,
                  `FulltimeTrader`,
                  `StatusTrader`,
                  `YearRunningTrader`,
                  `NrFulltimeStaffMale`,
                  `NrFulltimeStaffFemale`,
                  `NrParttimeStaffMale`,
                  `NrParttimeStaffFemale`,
                  `ComodityCacaoSalePercentage`,
                  `ComodityOtherSalePercentage`,
                  `BuyWetBeans`,
                  `BuyFermentBeans`,
                  `BuyDryBeans`,
                  `NrTransWetBeansHighHarvest`,
                  `NrVolumeWetBeansHighHarvest`,
                  `NrTransWetBeansNormalHarvest`,
                  `NrVolumeWetBeansNormalHarvest`,
                  `NrTransWetBeansLowHarvest`,
                  `NrVolumeWetBeansLowHarvest`,
                  `NrTransFermentBeansHighHarvest`,
                  `NrVolumeFermentBeansHighHarvest`,
                  `NrTransFermentBeansNormalHarvest`,
                  `NrVolumeFermentBeansNormalHarvest`,
                  `NrTransFermentBeansLowHarvest`,
                  `NrVolumeFermentBeansLowHarvest`,
                  `NrTransDryBeansHighHarvest`,
                  `NrVolumeDryBeansHighHarvest`,
                  `NrTransDryBeansNormalHarvest`,
                  `NrVolumeDryBeansNormalHarvest`,
                  `NrTransDryBeansLowHarvest`,
                  `NrVolumeDryBeansLowHarvest`,
                  `NrCacaoFrequentBuyer`,
                  `NrCacaoNormalBuyer`,
                  `CacaoActivitySellBuyDryBeans`,
                  `CacaoActivitySellBuyFermentBeans`,
                  `CacaoActivitySellPest`,
                  `CacaoActivitySellFertilizer`,
                  `CacaoActivityLoanToFarmer`,
                  `UseToolDigitalScale`,
                  `UseToolManualScale`,
                  `UseToolAquaboy`,
                  `UseToolSolarDryer`,
                  `UseToolFuelDryer`,
                  `UseToolAyakMachine`,
                  `UseToolFloorDryer`,
                  `UseToolWarehouse`,
                  `UseToolFermentBox`,
                  `FundValueFromSelf`,
                  `FundValueFromLoan`,
                  `PriceSource`,
                  `AverageMarginCacaoPerKgReceivedMin`,
                  `AverageMarginCacaoPerKgReceivedMax`,
                  `QualityCheckCacaoBeans`,
                  `WhenPayClient`,
                  `PayClientMethod`,
                  `SellCertifiedCacaoBeans`,
                  `KnownCertifiedCacaoBeans`,
                  `KnownNonCertifiedCacaoBeans`,
                  `UseSystemTraceCertifiedCacaoBeans`,
                  `UseSystemTraceNonCertifiedCacaoBeans`,
                  `TraceSellingCertifiedCacaoBeans`,
                  `TraceSellingNonCertifiedCacaoBeans`,
                  `RecordTransCertifiedCacaoBeans`,
                  `RecordTransNonCertifiedCacaoBeans`,
                  `AnalyzeTransCertifiedCacaoBeans`,
                  `AnalyzeTransNonCertifiedCacaoBeans`,
                  `ShowAnalyzeResult`,
                  `BusinessModel`,
                  `BusinessModelOther`,
                  `SellToBigTrader`,
                  `SellToCoop`,
                  `SellToBigCompany`,
                  `SellToFactory`,
                  `SellToExport`,
                  `SellToLoaner`,
                  `SellToOther`,
                  `SellToOtherText`,
                  `ChooseBuyerContract`,
                  `ChooseBuyerHighestValue`,
                  `ChooseBuyerDistance`,
                  `ChooseBuyerFastPayment`,
                  `ChooseBuyerFacility`,
                  `ChooseBuyerFundingSource`,
                  `BuyerInfoDetail`,
                  `ProblemBuycacaoFund`,
                  `ProblemBuycacaoQuality`,
                  `ProblemBuycacaoTransport`,
                  `ProblemBuycacaoPriceFluc`,
                  `ProblemBuycacaoPriceComp`,
                  `IsCacaoFarmer`,
                  `CacaoLandSize`,
                  `AverageProduction`,
                  `IsExCacaoFarmer`,
                  `ExCacaoLandSize`,
                  `ExAverageProduction`,
                  `ProvideFertPest`,
                  `ProvideLoan`,
                  `LoanCreditCount`,
                  `LoanCreditValueTotal`,
                  `PayLoanMethod`,
                  `PayLoanMethodOther`,
                  `LoanerHaveTo`,
                  `LossAction`,
                  `IsBankAgent`,
                  `HaveOtherBusiness`,
                  `FarmerMainProblemLowProd`,
                  `FarmerMainProblemOldTree`,
                  `FarmerMainProblemNoKnowledge`,
                  `FarmerMainProblemPest`,
                  `FarmerMainProblemPestSolving`,
                  `FarmerMainProblemSeasonChanging`,
                  `FarmerMainProblemDisease`,
                  `FarmerMainProblemLand`,
                  `FarmerMainProblemLackSkill`,
                  `FarmerMainProblemOtherComodity`,
                  `FarmerMainProblemLowPrice`,
                  `MorethanOneBankAcc`,
                  `BankTransactionFreq`,
                  `SavingAsideFund`,
                  `FundValue`,
                  `HaveLoanBefore`,
                  `LastLoanValue`,
                  `LastLoanSettle`,
                  `LastLoanCreditValue`,
                  `LastLoanSourceTrader`,
                  `LastLoanSourceFamily`,
                  `LastLoanSourceLoaner`,
                  `LastLoanSourceBank`,
                  `LastLoanSourceCoop`,
                  `LastLoanSourceOther`,
                  `LastLoanSourceOtherText`,
                  `PayingStaffFixedSalary`,
                  `PayingStaffCommision`,
                  `PayingStaffFamilyNoPayment`,
                  `TrustedTrader`,
                  `NeedLoanAndQualify`,
                  `CacaoTraderIsProfitable`,
                  `WealthyPersonInSociety`,
                  `StatusCode`,
                  `DateSync`,
                  `DateSynced`,
                  `DateCreated`,
                  `CreatedBy`,
                  `DateUpdated`,
                  `LastModifiedBy`,
                  `uid`
                )
                SELECT
                    NOW(), ?, a.*
                FROM
                    ktv_trader_surveys a
                WHERE
                    a.TraderSurID = ?
                LIMIT 1
                ";
        $this->db->query($sql, array($_SESSION['userid'], $TraderSurID));

        $sql = "DELETE FROM ktv_trader_surveys WHERE TraderSurID = ? LIMIT 1";
        $this->db->query($sql, array($TraderSurID));

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to delete data";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Data deleted";
        }
        return $results;
    }

}
?>