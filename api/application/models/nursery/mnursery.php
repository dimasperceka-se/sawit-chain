<?php
/**
 * @Author: nikolius
 * @Date:   2016-12-13 10:18:12
 */
class Mnursery extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    public function getNurseryByObject($ObjType,$ObjID,$NurseryNr){
        $sql="SELECT
                  `NurseryID`,
                  `ObjType`,
                  `ObjID`,
                  `NurseryNr`,
                  ResponsibleType,
                  `Responsible`,
                  ResponsibleName,
                  ResponsibleBirthday,
                  ResponsiblePhone,
                  ResponsibleGender,
                  ResponsiblePhoto,
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
                    SUBSTR(CASE
                        WHEN ObjType = 'cpg' THEN ObjID
                        WHEN ObjType = 'farmer' THEN ObjID
                        WHEN ObjType = 'koperasi' THEN
                            (SELECT VillageID FROM ktv_cooperatives WHERE CoopID = `ObjID`)
                        WHEN `ObjType` = 'trader' THEN
                            (SELECT VillageID FROM ktv_traders WHERE TraderID = `ObjID`)
                    END,1,4) AS DistrictIDnya,
                  `LocationCloseToCommunity`,
                  IF(LocationCloseToCommunity='1','-',`LocationCloseToCommunityNo`) AS LocationCloseToCommunityNo,
                  `GoodLandArea`,
                  IF(GoodLandArea='1','-',`GoodLandAreaNo`) AS GoodLandAreaNo,
                  `LocationNearCocoaFarm`,
                  IF(LocationNearCocoaFarm='1','-',`LocationNearCocoaFarmNo`) AS LocationNearCocoaFarmNo,
                  `ContinuousWaterSupply`,
                  IF(ContinuousWaterSupply='1','-',`ContinuousWaterSupplyNo`) AS ContinuousWaterSupplyNo,
                  `IrrigationInstalled`,
                  IF(IrrigationInstalled='1','-',`IrrigationInstalledNo`) AS IrrigationInstalledNo,
                  `UseShadingNet`,
                  IF(UseShadingNet='1','-',`UseShadingNetNo`) AS UseShadingNetNo,
                  `AdequateSupplyTopSoil`,
                  IF(AdequateSupplyTopSoil='1','-',`AdequateSupplyTopSoilNo`) AS AdequateSupplyTopSoilNo,
                  `ImprovedVariety`,
                  IF(ImprovedVariety='1','-',`ImprovedVarietyNo`) AS ImprovedVarietyNo,
                  `ConstructStoring`,
                  IF(ConstructStoring='1','-',`ConstructStoringNo`) AS ConstructStoringNo,
                  `CorrectEquipment`,
                  IF(CorrectEquipment='1','-',`CorrectEquipmentNo`) AS CorrectEquipmentNo,
                  `WindBreakInstalled`,
                  IF(WindBreakInstalled='1','-',`WindBreakInstalledNo`) AS WindBreakInstalledNo,
                  `SecurityFenceInstalled`,
                  IF(SecurityFenceInstalled='1','-',`SecurityFenceInstalledNo`) AS SecurityFenceInstalledNo,
                  `FertilizerUsed`,
                  IF(FertilizerUsed='1','-',`FertilizerUsedNo`) AS FertilizerUsedNo,
                  `OperatorAdequateTraining`,
                  IF(OperatorAdequateTraining='1','-',`OperatorAdequateTrainingNo`) AS OperatorAdequateTrainingNo,
                  `AdequateFacility`,
                  IF(AdequateFacility='1','-',`AdequateFacilityNo`) AS AdequateFacilityNo,
                  `SustainablePestDisease`,
                  IF(SustainablePestDisease='1','-',`SustainablePestDiseaseNo`) AS SustainablePestDiseaseNo,
                  `CloneGrading`,
                  IF(CloneGrading='1','-',`CloneGradingNo`) AS CloneGradingNo,
                  `SeedlingCullingDone`,
                  IF(SeedlingCullingDone='1','-',`SeedlingCullingDoneNo`) AS SeedlingCullingDoneNo,
                  `ProperInputSalesRecord`,
                  IF(ProperInputSalesRecord='1','-',`ProperInputSalesRecordNo`) AS ProperInputSalesRecordNo,
                  `SeedsPreGerminated`,
                  IF(SeedsPreGerminated='1','-',`SeedsPreGerminatedNo`) AS SeedsPreGerminatedNo
                FROM
                  `ktv_nursery`
                WHERE
                    ObjType = ?
                    AND ObjID = ?
                    AND NurseryNr = ?
                LIMIT 1";
        $p = array($ObjType,$ObjID,$NurseryNr);
        $data = $this->db->query($sql,$p);
        $dataReturn = $data->row_array();

        //prep data checklist ===================================== (begin)
        if($dataReturn['LocationCloseToCommunity'] == ""){
            $dataReturn['LocationCloseToCommunity'] = "&nbsp;";
        }else{
            if($dataReturn['LocationCloseToCommunity'] == "1") $dataReturn['LocationCloseToCommunity'] = "Yes"; else $dataReturn['LocationCloseToCommunity'] = "No";
        }
        if($dataReturn['LocationCloseToCommunityNo'] == ""){
            $dataReturn['LocationCloseToCommunityNo'] = "&nbsp;";
        }


        if($dataReturn['GoodLandArea'] == ""){
            $dataReturn['GoodLandArea'] = "&nbsp;";
        }else{
            if($dataReturn['GoodLandArea'] == "1") $dataReturn['GoodLandArea'] = "Yes"; else $dataReturn['GoodLandArea'] = "No";
        }
        if($dataReturn['GoodLandAreaNo'] == ""){
            $dataReturn['GoodLandAreaNo'] = "&nbsp;";
        }

        if($dataReturn['LocationNearCocoaFarm'] == ""){
            $dataReturn['LocationNearCocoaFarm'] = "&nbsp;";
        }else{
            if($dataReturn['LocationNearCocoaFarm'] == "1") $dataReturn['LocationNearCocoaFarm'] = "Yes"; else $dataReturn['LocationNearCocoaFarm'] = "No";
        }
        if($dataReturn['LocationNearCocoaFarmNo'] == ""){
            $dataReturn['LocationNearCocoaFarmNo'] = "&nbsp;";
        }

        if($dataReturn['ContinuousWaterSupply'] == ""){
            $dataReturn['ContinuousWaterSupply'] = "&nbsp;";
        }else{
            if($dataReturn['ContinuousWaterSupply'] == "1") $dataReturn['ContinuousWaterSupply'] = "Yes"; else $dataReturn['ContinuousWaterSupply'] = "No";
        }
        if($dataReturn['ContinuousWaterSupplyNo'] == ""){
            $dataReturn['ContinuousWaterSupplyNo'] = "&nbsp;";
        }

        if($dataReturn['IrrigationInstalled'] == ""){
            $dataReturn['IrrigationInstalled'] = "&nbsp;";
        }else{
            if($dataReturn['IrrigationInstalled'] == "1") $dataReturn['IrrigationInstalled'] = "Yes"; else $dataReturn['IrrigationInstalled'] = "No";
        }
        if($dataReturn['IrrigationInstalledNo'] == ""){
            $dataReturn['IrrigationInstalledNo'] = "&nbsp;";
        }

        if($dataReturn['UseShadingNet'] == ""){
            $dataReturn['UseShadingNet'] = "&nbsp;";
        }else{
            if($dataReturn['UseShadingNet'] == "1") $dataReturn['UseShadingNet'] = "Yes"; else $dataReturn['UseShadingNet'] = "No";
        }
        if($dataReturn['UseShadingNetNo'] == ""){
            $dataReturn['UseShadingNetNo'] = "&nbsp;";
        }

        if($dataReturn['AdequateSupplyTopSoil'] == ""){
            $dataReturn['AdequateSupplyTopSoil'] = "&nbsp;";
        }else{
            if($dataReturn['AdequateSupplyTopSoil'] == "1") $dataReturn['AdequateSupplyTopSoil'] = "Yes"; else $dataReturn['AdequateSupplyTopSoil'] = "No";
        }
        if($dataReturn['AdequateSupplyTopSoilNo'] == ""){
            $dataReturn['AdequateSupplyTopSoilNo'] = "&nbsp;";
        }

        if($dataReturn['ImprovedVariety'] == ""){
            $dataReturn['ImprovedVariety'] = "&nbsp;";
        }else{
            if($dataReturn['ImprovedVariety'] == "1") $dataReturn['ImprovedVariety'] = "Yes"; else $dataReturn['ImprovedVariety'] = "No";
        }
        if($dataReturn['ImprovedVarietyNo'] == ""){
            $dataReturn['ImprovedVarietyNo'] = "&nbsp;";
        }

        if($dataReturn['ConstructStoring'] == ""){
            $dataReturn['ConstructStoring'] = "&nbsp;";
        }else{
            if($dataReturn['ConstructStoring'] == "1") $dataReturn['ConstructStoring'] = "Yes"; else $dataReturn['ConstructStoring'] = "No";
        }
        if($dataReturn['ConstructStoringNo'] == ""){
            $dataReturn['ConstructStoringNo'] = "&nbsp;";
        }

        if($dataReturn['CorrectEquipment'] == ""){
            $dataReturn['CorrectEquipment'] = "&nbsp;";
        }else{
            if($dataReturn['CorrectEquipment'] == "1") $dataReturn['CorrectEquipment'] = "Yes"; else $dataReturn['CorrectEquipment'] = "No";
        }
        if($dataReturn['CorrectEquipmentNo'] == ""){
            $dataReturn['CorrectEquipmentNo'] = "&nbsp;";
        }

        if($dataReturn['WindBreakInstalled'] == ""){
            $dataReturn['WindBreakInstalled'] = "&nbsp;";
        }else{
            if($dataReturn['WindBreakInstalled'] == "1") $dataReturn['WindBreakInstalled'] = "Yes"; else $dataReturn['WindBreakInstalled'] = "No";
        }
        if($dataReturn['WindBreakInstalledNo'] == ""){
            $dataReturn['WindBreakInstalledNo'] = "&nbsp;";
        }

        if($dataReturn['SecurityFenceInstalled'] == ""){
            $dataReturn['SecurityFenceInstalled'] = "&nbsp;";
        }else{
            if($dataReturn['SecurityFenceInstalled'] == "1") $dataReturn['SecurityFenceInstalled'] = "Yes"; else $dataReturn['SecurityFenceInstalled'] = "No";
        }
        if($dataReturn['SecurityFenceInstalledNo'] == ""){
            $dataReturn['SecurityFenceInstalledNo'] = "&nbsp;";
        }

        if($dataReturn['FertilizerUsed'] == ""){
            $dataReturn['FertilizerUsed'] = "&nbsp;";
        }else{
            if($dataReturn['FertilizerUsed'] == "1") $dataReturn['FertilizerUsed'] = "Yes"; else $dataReturn['FertilizerUsed'] = "No";
        }
        if($dataReturn['FertilizerUsedNo'] == ""){
            $dataReturn['FertilizerUsedNo'] = "&nbsp;";
        }

        if($dataReturn['OperatorAdequateTraining'] == ""){
            $dataReturn['OperatorAdequateTraining'] = "&nbsp;";
        }else{
            if($dataReturn['OperatorAdequateTraining'] == "1") $dataReturn['OperatorAdequateTraining'] = "Yes"; else $dataReturn['OperatorAdequateTraining'] = "No";
        }
        if($dataReturn['OperatorAdequateTrainingNo'] == ""){
            $dataReturn['OperatorAdequateTrainingNo'] = "&nbsp;";
        }

        if($dataReturn['AdequateFacility'] == ""){
            $dataReturn['AdequateFacility'] = "&nbsp;";
        }else{
            if($dataReturn['AdequateFacility'] == "1") $dataReturn['AdequateFacility'] = "Yes"; else $dataReturn['AdequateFacility'] = "No";
        }
        if($dataReturn['AdequateFacilityNo'] == ""){
            $dataReturn['AdequateFacilityNo'] = "&nbsp;";
        }

        if($dataReturn['SustainablePestDisease'] == ""){
            $dataReturn['SustainablePestDisease'] = "&nbsp;";
        }else{
            if($dataReturn['SustainablePestDisease'] == "1") $dataReturn['SustainablePestDisease'] = "Yes"; else $dataReturn['SustainablePestDisease'] = "No";
        }
        if($dataReturn['SustainablePestDiseaseNo'] == ""){
            $dataReturn['SustainablePestDiseaseNo'] = "&nbsp;";
        }

        if($dataReturn['CloneGrading'] == ""){
            $dataReturn['CloneGrading'] = "&nbsp;";
        }else{
            if($dataReturn['CloneGrading'] == "1") $dataReturn['CloneGrading'] = "Yes"; else $dataReturn['CloneGrading'] = "No";
        }
        if($dataReturn['CloneGradingNo'] == ""){
            $dataReturn['CloneGradingNo'] = "&nbsp;";
        }

        if($dataReturn['SeedlingCullingDone'] == ""){
            $dataReturn['SeedlingCullingDone'] = "&nbsp;";
        }else{
            if($dataReturn['SeedlingCullingDone'] == "1") $dataReturn['SeedlingCullingDone'] = "Yes"; else $dataReturn['SeedlingCullingDone'] = "No";
        }
        if($dataReturn['SeedlingCullingDoneNo'] == ""){
            $dataReturn['SeedlingCullingDoneNo'] = "&nbsp;";
        }

        if($dataReturn['ProperInputSalesRecord'] == ""){
            $dataReturn['ProperInputSalesRecord'] = "&nbsp;";
        }else{
            if($dataReturn['ProperInputSalesRecord'] == "1") $dataReturn['ProperInputSalesRecord'] = "Yes"; else $dataReturn['ProperInputSalesRecord'] = "No";
        }
        if($dataReturn['ProperInputSalesRecordNo'] == ""){
            $dataReturn['ProperInputSalesRecordNo'] = "&nbsp;";
        }

        if($dataReturn['SeedsPreGerminated'] == ""){
            $dataReturn['SeedsPreGerminated'] = "&nbsp;";
        }else{
            if($dataReturn['SeedsPreGerminated'] == "1") $dataReturn['SeedsPreGerminated'] = "Yes"; else $dataReturn['SeedsPreGerminated'] = "No";
        }
        if($dataReturn['SeedsPreGerminatedNo'] == ""){
            $dataReturn['SeedsPreGerminatedNo'] = "&nbsp;";
        }
        //prep data checklist ===================================== (end)

        //get data range tahun transaksi nursery ini
        $sql="SELECT
                    MIN(YEAR(DateTransaction)) AS minYear,
                    MAX(YEAR(DateTransaction)) AS maxYear,
                    COUNT(DISTINCT YEAR(DateTransaction)) AS jumlahTahun,
                    SUM(Volume) AS totalVolume,
                    COUNT(NurseryTransactionID) AS jumlahTransaksi,
                    SUM(Price) AS totalPrice,
                    (SUM(Volume * Price)) AS totalTotal
                FROM
                    ktv_nursery_transaction
                WHERE
                    NurseryID = ?
                    AND StatusCode = 'active'
            ";
        $p = array($dataReturn['NurseryID']);
        $query = $this->db->query($sql,$p);
        $data = $query->row_array();


        if($data['minYear'] != ""){
            if($data['minYear'] == $data['maxYear']){
                $dataReturn['displayYearRangeTrans'] = '('.$data['minYear'].') *';
            }else{
                $dataReturn['displayYearRangeTrans'] = '('.$data['minYear'].' - '.$data['maxYear'].') *';
            }

            $dataReturn['volumePerYear'] = $data['totalVolume'] / $data['jumlahTahun'];
            $dataReturn['volumePerYear'] = number_format($dataReturn['volumePerYear'], 0, '.', ',');

            $dataReturn['avrPrice'] = $data['totalPrice'] / $data['jumlahTransaksi'];
            $dataReturn['avrPrice'] = number_format($dataReturn['avrPrice'], 0, '.', ',');

            $dataReturn['milPerYear'] = $data['totalTotal'] / $data['jumlahTahun'];
            $dataReturn['milPerYear'] = floor($dataReturn['milPerYear'] / 1000000);

        }else{
            $dataReturn['displayYearRangeTrans'] = 'No Data';
            $dataReturn['volumePerYear'] = 0;
            $dataReturn['avrPrice'] = 0;
            $dataReturn['milPerYear'] = 0;
        }

        $dataReturn['jumlahTransaksi'] = $data['jumlahTransaksi'];
        return $dataReturn;
    }

    public function getNurseryTransactionByNurseryId($NurseryID){
        $sql="SELECT
                Volume
                , Price
                , Volume * Price AS Total
            FROM
                ktv_nursery_transaction a
            WHERE
                a.`StatusCode` = 'active'
                AND NurseryID = ?
            ORDER BY DateTransaction DESC
            LIMIT 10";
        $p = array($NurseryID);
        $query = $this->db->query($sql,$p);
        return $query->result_array();
    }

    public function getNurseryMonitoringByNurseryId($NurseryID){
        $sql="SELECT
                MonitoringDate AS tanggalNya
                , MonitoringStatus AS statusNya
            FROM
                ktv_nursery_monitoring
            WHERE
                NurseryID = ?
            ORDER BY MonitoringDate DESC
            LIMIT 10";
        $p = array($NurseryID);
        $query = $this->db->query($sql,$p);
        return $query->result_array();
    }

    public function getNurseryListCpg($tipe,$ObjID,$NurseryNr){
        $sql="SELECT
                b.`FarmerName` AS Caretaker
                , DATE(a.Established) AS fDate
                , a.CertificationStatus
                , a.`Area`
                , (a.`Panjang` * a.Lebar) AS pjgLebarM2
                , a.Kapasitas
                , a.`Latitude` AS lat
                , a.`Longitude` AS `long`
            FROM
                ktv_nursery a
                LEFT JOIN ktv_farmer b ON a.`Responsible` = b.`FarmerID`
            WHERE
                a.ObjType = ?
                AND a.ObjID = ?
                AND a.NurseryNr = ?
            ORDER BY a.`NurseryNr` ASC";
        $p = array($tipe,$ObjID,$NurseryNr);
        $query = $this->db->query($sql,$p);
        return $query->result_array();
    }

    public function getNurseryListTrader($tipe,$ObjID,$NurseryNr){
        $sql="SELECT
                b.`TraderName` AS Caretaker
                , DATE(a.Established) AS fDate
                , a.CertificationStatus
                , a.`Area`
                , (a.`Panjang` * a.Lebar) AS pjgLebarM2
                , a.Kapasitas
                , a.`Latitude` AS lat
                , a.`Longitude` AS `long`
            FROM
                ktv_nursery a
                LEFT JOIN ktv_traders b ON a.`ObjID` = b.`TraderID`
            WHERE
                a.ObjType = ?
                AND a.ObjID = ?
                AND a.NurseryNr = ?
            ORDER BY a.`NurseryNr` ASC";
        $p = array($tipe,$ObjID,$NurseryNr);
        $query = $this->db->query($sql,$p);
        return $query->result_array();
    }

    public function getNurseryListCoop($tipe,$ObjID,$NurseryNr){
        $sql="SELECT
                (
                    SELECT
                        cr_d.PersonNm
                    FROM
                        ktv_cooperatives cr_a
                        INNER JOIN ktv_staffs cr_b ON cr_a.CoopID = cr_b.ObjID AND cr_b.ObjType = 'cooperative'
                        INNER JOIN ktv_staff_positions cr_c ON cr_b.StaffID = cr_c.StaffPosID
                        INNER JOIN ktv_persons cr_d ON cr_b.PersonID = cr_d.PersonID
                    WHERE
                        cr_a.CoopID = a.ObjID
                        AND cr_c.StaffPosPositionID = '82'
                        AND CURDATE() BETWEEN cr_c.StaffPostStart AND cr_c.StaffPostEnd
                        AND cr_c.StatusCode = 'active'
                    LIMIT 1
                ) AS Caretaker
                , DATE(a.Established) AS fDate
                , a.CertificationStatus
                , a.`Area`
                , (a.`Panjang` * a.Lebar) AS pjgLebarM2
                , a.Kapasitas
                , a.`Latitude` AS lat
                , a.`Longitude` AS `long`
            FROM
                ktv_nursery a
            WHERE
                a.ObjType = ?
                AND a.ObjID = ?
                AND a.NurseryNr = ?
            ORDER BY a.`NurseryNr` ASC";
        $p = array($tipe,$ObjID,$NurseryNr);
        $query = $this->db->query($sql,$p);
        return $query->result_array();
    }

    public function getProfileDataTrader($TraderID){
        $sql="SELECT
                a.`TraderID` AS ObjIDNya
                , a.`TraderName` AS ObjNameNya
                , a.`Handphone` AS HandPhone
                , a.`Sex` AS Gender
                , '-' AS GroupName
                , b.`Province` AS Provinsi
                , c.District AS Kabupaten
                , d.SubDistrict AS Kecamatan
                , e.Village AS Desa
            FROM
                ktv_traders a
                LEFT JOIN ktv_province b ON SUBSTR(a.`VillageID`,1,2) = b.`ProvinceID`
                LEFT JOIN ktv_district c ON SUBSTR(a.`VillageID`,1,4) = c.DistrictID
                LEFT JOIN ktv_subdistrict d ON SUBSTR(a.`VillageID`,1,7) = d.SubDistrictID
                LEFT JOIN ktv_village e ON a.`VillageID` = e.VillageID
            WHERE
                a.`TraderID` = ?
            LIMIT 1";
        $query = $this->db->query($sql,array($TraderID));
        return $query->row_array();
    }

    public function getProfileDataCoop($CoopID){
        $sql="SELECT
                cr_a.`CoopID` AS ObjIDNya
                , cr_a.`CoopName` AS ObjNameNya
                , cr_a.`Phone` AS HandPhone
                , IF(cr_d.`Gender`='m','1',IF(cr_d.`Gender`='f','2','-')) AS Gender
                , '-' AS GroupName
                , b.`Province` AS Provinsi
                , c.District AS Kabupaten
                , d.SubDistrict AS Kecamatan
                , e.Village AS Desa
            FROM
                ktv_cooperatives cr_a
                LEFT JOIN ktv_staffs cr_b ON cr_a.CoopID = cr_b.ObjID AND cr_b.ObjType = 'cooperative'
                LEFT JOIN ktv_staff_positions cr_c ON cr_b.StaffID = cr_c.StaffPosID AND
                                                        cr_c.StaffPosPositionID = '82' AND
                                                        CURDATE() BETWEEN cr_c.StaffPostStart AND cr_c.StaffPostEnd
                                                        AND cr_c.StatusCode = 'active'
                LEFT JOIN ktv_persons cr_d ON cr_b.PersonID = cr_d.PersonID
                LEFT JOIN ktv_province b ON SUBSTR(cr_a.`VillageID`,1,2) = b.`ProvinceID`
                LEFT JOIN ktv_district c ON SUBSTR(cr_a.`VillageID`,1,4) = c.DistrictID
                LEFT JOIN ktv_subdistrict d ON SUBSTR(cr_a.`VillageID`,1,7) = d.SubDistrictID
                LEFT JOIN ktv_village e ON cr_a.`VillageID` = e.VillageID
            WHERE
                cr_a.CoopID = ?
            LIMIT 1";
        $query = $this->db->query($sql,array($CoopID));
        return $query->row_array();
    }

    public function getPartnerLogoByDistrict($DistrictID){
        /*
        - 4 slot
        - 1 district max 1 donor
        - Donor tidak ada program
        - dahulukan yg partner kalau masih ada sisa slot baru kasih logo programnya partner
        - partner dan program partner harus berdampingan logonya
        - kalau pencarian by district tidak ada melihat sertifikasi

        array hasil example
        Array
        (
            [0] => Array
                (
                    [Photo] => 20160106100609_logo-mca-indonesia-s1.png
                )

            [1] => Array
                (
                    [Photo] => 20141014114614_Ecom_Cocoa.jpg
                )

        )
        */
        $remainSlot = 4;
        $increIndeks = 0;

        //ambil ada partner donor apa aja didistrict ini
        $sql="SELECT
                a.`PartnerID`
                , b.`PartnerName`
                , IF(b.`PartnerIndustry`='1',1,0) AS isDonor
                , b.Photo AS PhotoPartner
            FROM
                ktv_district_partner a
                INNER JOIN ktv_program_partner b ON a.`PartnerID` = b.`PartnerID`
            WHERE
                a.`DistrictID` = ?
                AND a.`PartnerID` NOT IN (1,2,23,26)
                AND b.`PartnerIndustry` = '1'
                AND b.`Photo` IS NOT NULL AND b.`Photo` != ''
            LIMIT 1";
        $query = $this->db->query($sql,array($DistrictID));
        $data = $query->row_array();

        if($data['PartnerID'] != ""){
            $arrReturn[$increIndeks]['Photo'] = $data['PhotoPartner'];
            $remainSlot--; //sisa 3
            $increIndeks++;
        }

        //ambil partner BUKAN donor didistrict ini
        $sql="SELECT
                a.`PartnerID`
                , b.`PartnerName`
                , IF(b.`PartnerIndustry`='1',1,0) AS isDonor
                , b.Photo AS PhotoPartner
                , b.`PhotoProgram` AS PhotoProgram
            FROM
                ktv_district_partner a
                INNER JOIN ktv_program_partner b ON a.`PartnerID` = b.`PartnerID`
            WHERE
                a.`DistrictID` = ?
                AND a.`PartnerID` NOT IN (1,2,23,26)
                AND b.`PartnerIndustry` != '1'
                AND b.`Photo` IS NOT NULL AND b.`Photo` != ''
            LIMIT ?
            ";
        $query = $this->db->query($sql,array($DistrictID, (int) $remainSlot));
        $data = $query->result_array();

        $jumlahPartner = count($data);
        $slotProgram = $remainSlot - $jumlahPartner;

        if($data[0]['PartnerID'] != ""){
            foreach ($data as $key => $value) {
                $arrReturn[$increIndeks]['Photo'] = $value['PhotoPartner'];
                $increIndeks++;

                if($slotProgram > 0){
                    if($value['PhotoPartner'] != ""){
                        $arrReturn[$increIndeks]['Photo'] = $value['PhotoProgram'];
                        $increIndeks++;
                        $slotProgram--;
                    }
                }
            }
        }

        //jika masih ada sisa slot, kasih icon SCPP Swisscontact
        if(count($arrReturn) < 4){
            $arrReturn[$increIndeks]['Photo'] = '20160315105236_SCPP 2015.jpg';
        }

        return $arrReturn;
    }

    public function getDataOwnerNurseryCpg($CPGid){
        $sql="SELECT
                a.CPGid AS owner_id,
                a.GroupName AS owner_name,
                prov.`Province` AS propinsi,
                dist.District AS district,
                sub_dis.SubDistrict AS kecamatan,
                Village AS desa
            FROM
                ktv_cpg a
                LEFT JOIN ktv_province prov ON SUBSTR(a.`VillageID`,1,2) = prov.`ProvinceID`
                LEFT JOIN ktv_district dist ON SUBSTR(a.`VillageID`,1,4) = dist.DistrictID
                LEFT JOIN ktv_subdistrict sub_dis ON SUBSTR(a.`VillageID`,1,7) = sub_dis.SubDistrictID
                LEFT JOIN ktv_village vil ON a.`VillageID` = vil.VillageID
            WHERE
                a.`CPGid` = ?
            LIMIT 1";
        $query = $this->db->query($sql,array($CPGid));
        return $query->row_array();
    }

    public function getDataOwnerNurseryFarmer($FarmerID){
        $sql="SELECT
                a.`FarmerID` AS owner_id,
                a.`FarmerName` AS owner_name,
                prov.`Province` AS propinsi,
                dist.District AS district,
                sub_dis.SubDistrict AS kecamatan,
                Village AS desa
            FROM
                ktv_farmer a
                LEFT JOIN ktv_province prov ON SUBSTR(a.`VillageID`,1,2) = prov.`ProvinceID`
                LEFT JOIN ktv_district dist ON SUBSTR(a.`VillageID`,1,4) = dist.DistrictID
                LEFT JOIN ktv_subdistrict sub_dis ON SUBSTR(a.`VillageID`,1,7) = sub_dis.SubDistrictID
                LEFT JOIN ktv_village vil ON a.`VillageID` = vil.VillageID
            WHERE
                a.`FarmerID` = ?
            LIMIT 1";
        $query = $this->db->query($sql,array($FarmerID));
        return $query->row_array();
    }

    public function getDataOwnerNurseryTrader($TraderID){
        $sql="SELECT
                a.`TraderID` AS owner_id,
                a.`TraderName` AS owner_name,
                prov.`Province` AS propinsi,
                dist.District AS district,
                sub_dis.SubDistrict AS kecamatan,
                Village AS desa
            FROM
                ktv_traders a
                LEFT JOIN ktv_province prov ON SUBSTR(a.`VillageID`,1,2) = prov.`ProvinceID`
                LEFT JOIN ktv_district dist ON SUBSTR(a.`VillageID`,1,4) = dist.DistrictID
                LEFT JOIN ktv_subdistrict sub_dis ON SUBSTR(a.`VillageID`,1,7) = sub_dis.SubDistrictID
                LEFT JOIN ktv_village vil ON a.`VillageID` = vil.VillageID
            WHERE
                a.`TraderID` = ?
            LIMIT 1";
        $query = $this->db->query($sql,array($TraderID));
        return $query->row_array();
    }

    public function getDataOwnerNurseryCoop($CoopID){
        $sql="SELECT
                a.`CoopID` AS owner_id,
                a.`CoopName` AS owner_name,
                prov.`Province` AS propinsi,
                dist.District AS district,
                sub_dis.SubDistrict AS kecamatan,
                Village AS desa
            FROM
                ktv_cooperatives a
                LEFT JOIN ktv_province prov ON SUBSTR(a.`VillageID`,1,2) = prov.`ProvinceID`
                LEFT JOIN ktv_district dist ON SUBSTR(a.`VillageID`,1,4) = dist.DistrictID
                LEFT JOIN ktv_subdistrict sub_dis ON SUBSTR(a.`VillageID`,1,7) = sub_dis.SubDistrictID
                LEFT JOIN ktv_village vil ON a.`VillageID` = vil.VillageID
            WHERE
                a.`CoopID` = ?
            LIMIT 1";
        $query = $this->db->query($sql,array($CoopID));
        return $query->row_array();
    }

    public function getDataManagerNurseryFarmer($FarmerID){
        $sql="SELECT
                    a.`FarmerName` AS nama,
                    a.`Birthdate` AS tgl_lahir,
                    a.`HandPhone` AS telp,
                    IF(a.Gender='1','m',IF(a.`Gender`='2','f','-')) AS jk,
                    a.Photo AS foto
                FROM
                    ktv_farmer a
                WHERE
                    a.`FarmerID` = ?
                LIMIT 1";
        $query = $this->db->query($sql,array($FarmerID));
        return $query->row_array();
    }

    public function getDataManagerNurseryStaff($StaffID){
        $sql="SELECT
                b.`PersonNm` AS nama,
                b.`BirthDate` AS tgl_lahir,
                b.`OfficialCellPhone` AS telp,
                b.`Gender` AS jk,
                b.`Photo` AS foto
            FROM
                ktv_staffs a
                INNER JOIN ktv_persons b ON a.`PersonID` = b.`PersonID`
            WHERE
                a.`StaffID` = ?
            LIMIT 1";
        $query = $this->db->query($sql,array($StaffID));
        return $query->row_array();
    }

    public function getNurseryOwner($kabupaten,$objType,$printtype){
        //get District ID
        $sql="SELECT
                a.`DistrictID`
            FROM
                ktv_district a
            WHERE
                a.`District` = ?
            LIMIT 1";
        $query = $this->db->query($sql,array($kabupaten));
        $data = $query->row_array();
        $DistrictID = $data['DistrictID'];

        switch ($objType) {
            case 'cpg':
                if($printtype == "empty"){
                    $sql="SELECT
                        a.`CPGid` AS id
                        , CONCAT(a.`CPGid`,' - ',a.`GroupName`) AS label
                    FROM
                        ktv_cpg a
                    WHERE
                        a.`Status` = 'active'
                        AND SUBSTR(a.`CPGid`,1,4) = ?
                    ORDER BY a.`CPGid` ASC";
                    $query = $this->db->query($sql,array($DistrictID));
                }

                if($printtype == "result" || $printtype == "profile"){
                    $sql="SELECT
                        a.`ObjID` AS id
                        , CONCAT(b.`CPGid`,' - ',b.`GroupName`) AS label
                    FROM
                        ktv_nursery a
                        INNER JOIN ktv_cpg b ON a.`ObjID` = b.`CPGid`
                    WHERE
                        a.`ObjType` = ?
                        AND SUBSTR(b.`CPGid`,1,4) = ?
                    ORDER BY a.`ObjID` ASC";
                    $query = $this->db->query($sql,array($objType,$DistrictID));
                }
            break;
            case 'farmer':
                if($printtype == "empty"){
                    $sql="SELECT
                        a.`FarmerID` AS id
                        , CONCAT(a.`FarmerID`,' - ',a.`FarmerName`) AS label
                    FROM
                        ktv_farmer a
                    WHERE
                        a.`StatusCode` = 'active'
                        AND SUBSTR(a.`FarmerID`,1,4) = ?
                    ORDER BY a.`FarmerID` ASC";
                    $query = $this->db->query($sql,array($DistrictID));
                }

                if($printtype == "result" || $printtype == "profile"){
                    $sql="SELECT
                        a.`ObjID` AS id
                        , CONCAT(b.`FarmerID`,' - ',b.`FarmerName`) AS label
                    FROM
                        ktv_nursery a
                        INNER JOIN ktv_farmer b ON a.`ObjID` = b.`FarmerID`
                    WHERE
                        a.`ObjType` = ?
                        AND SUBSTR(b.`FarmerID`,1,4) = ?
                    ORDER BY b.`FarmerID` ASC";
                    $query = $this->db->query($sql,array($objType,$DistrictID));
                }
            break;
            case 'trader':
                if($printtype == "empty"){
                    $sql="SELECT
                            a.`TraderID` AS id
                            , a.`TraderName` AS label
                        FROM
                            ktv_traders a
                        WHERE
                            a.`StatusCode` = 'active'
                            AND SUBSTR(a.`VillageID`,1,4) = ?
                        ORDER BY a.`TraderID` ASC";
                    $query = $this->db->query($sql,array($DistrictID));
                }

                if($printtype == "result" || $printtype == "profile"){
                    $sql="SELECT
                        a.`ObjID` AS id
                        , b.`TraderName` AS label
                    FROM
                        ktv_nursery a
                        INNER JOIN ktv_traders b ON a.`ObjID` = b.`TraderID`
                    WHERE
                        a.`ObjType` = ?
                        AND SUBSTR(b.`VillageID`,1,4) = ?
                    ORDER BY b.`TraderName` ASC";
                    $query = $this->db->query($sql,array($objType,$DistrictID));
                }
            break;
            case 'koperasi':
                if($printtype == "empty"){
                    $sql="SELECT
                            a.`CoopID` AS id
                            , a.`CoopName` AS label
                        FROM
                            ktv_cooperatives a
                        WHERE
                            a.`StatusCode` = 'active'
                            AND SUBSTR(a.`VillageID`,1,4) = ?
                        ORDER BY a.`CoopID` ASC";
                    $query = $this->db->query($sql,array($DistrictID));
                }

                if($printtype == "result" || $printtype == "profile"){
                    $sql="SELECT
                        a.`ObjID` AS id
                        , b.`CoopName` AS label
                    FROM
                        ktv_nursery a
                        INNER JOIN ktv_cooperatives b ON a.`ObjID` = b.`CoopID`
                    WHERE
                        a.`ObjType` = ?
                        AND SUBSTR(b.`VillageID`,1,4) = ?
                    ORDER BY b.`CoopName` ASC";
                    $query = $this->db->query($sql,array($objType,$DistrictID));
                }
            break;
        }

        $result['data'] = $query->result_array();
        return $result;
    }

    public function getNurseryNumber($ObjID,$ObjType){
        $sql="SELECT
                a.`NurseryNr` AS id
                , a.`NurseryNr` AS label
            FROM
                ktv_nursery a
            WHERE
                a.`ObjID` = ?
                AND a.`ObjType` = ?
            ORDER BY a.`NurseryNr` ASC";
        $query = $this->db->query($sql,array($ObjID,$ObjType));
        $data = $query->result_array();

        //cek apakah ada data
        if($data[0]['id'] == ""){
            $data[0]['id'] = "0";
            $data[0]['label'] = lang('No Nursery');
        }

        $result['data'] = $data;
        return $result;
    }

    public function getDataNurseryOwner($tipe,$ObjID){
        switch ($tipe) {
            case 'cpg':
                $sql="SELECT
                        a.CPGid AS id
                        , a.GroupName AS `name`
                        , SUBSTR(a.`VillageID`,1,2) AS ProvinceID
                        , SUBSTR(a.`VillageID`,1,4) AS DistrictID
                        , b.`Province`
                        , c.District
                    FROM
                        ktv_cpg a
                        LEFT JOIN ktv_province b ON SUBSTR(a.`VillageID`,1,2) = b.`ProvinceID`
                        LEFT JOIN ktv_district c ON SUBSTR(a.`VillageID`,1,4) = c.DistrictID
                    WHERE
                        a.`CPGid` = ?
                    LIMIT 1";
            break;
            case 'farmer':
                $sql="SELECT
                        a.`FarmerID` AS id
                        , a.`FarmerName` AS `name`
                        , SUBSTR(a.`VillageID`,1,2) AS ProvinceID
                        , SUBSTR(a.`VillageID`,1,4) AS DistrictID
                        , b.`Province`
                        , c.District
                    FROM
                        ktv_farmer a
                        LEFT JOIN ktv_province b ON SUBSTR(a.`VillageID`,1,2) = b.`ProvinceID`
                        LEFT JOIN ktv_district c ON SUBSTR(a.`VillageID`,1,4) = c.DistrictID
                    WHERE
                        a.`FarmerID` = ?
                    LIMIT 1";
            break;
            case 'trader':
                $sql="SELECT
                        a.`TraderID` AS id
                        , a.`TraderName` AS `name`
                        , SUBSTR(a.`VillageID`,1,2) AS ProvinceID
                        , SUBSTR(a.`VillageID`,1,4) AS DistrictID
                        , b.`Province`
                        , c.District
                    FROM
                        ktv_traders a
                        LEFT JOIN ktv_province b ON SUBSTR(a.`VillageID`,1,2) = b.`ProvinceID`
                        LEFT JOIN ktv_district c ON SUBSTR(a.`VillageID`,1,4) = c.DistrictID
                    WHERE
                        a.`TraderID` = ?
                    LIMIT 1";
            break;
            case 'koperasi':
                $sql="SELECT
                        a.`CoopID` AS id
                        , a.`CoopName` AS `name`
                        , SUBSTR(a.`VillageID`,1,2) AS ProvinceID
                        , SUBSTR(a.`VillageID`,1,4) AS DistrictID
                        , b.`Province`
                        , c.District
                    FROM
                        ktv_cooperatives a
                        LEFT JOIN ktv_province b ON SUBSTR(a.`VillageID`,1,2) = b.`ProvinceID`
                        LEFT JOIN ktv_district c ON SUBSTR(a.`VillageID`,1,4) = c.DistrictID
                    WHERE
                        a.`CoopID` = ?
                    LIMIT 1";
            break;
        }

        $query = $this->db->query($sql,array($ObjID));
        return $query->row_array();
    }

    public function getDataNurseryFormPrint($tipe,$ObjID,$NurseryNr){
        $dataReturn = array();

        $sql="SELECT
                a.ResponsibleType
                , a.Responsible
                , a.ResponsibleName
                , a.ResponsibleBirthday
                , a.ResponsiblePhone
                , a.ResponsibleGender
                , a.Established
                , a.CertificationStatus
                , a.DateCertification
                , a.DateAppliedCertification
                , a.Panjang
                , a.Lebar
                , a.Latitude
                , a.Longitude
            FROM
                ktv_nursery a
            WHERE
                a.`ObjType` = ?
                AND a.`ObjID` = ?
                AND a.`NurseryNr` = ?
            LIMIT 1";
        $query = $this->db->query($sql,array($tipe,$ObjID,$NurseryNr));
        $data = $query->row_array();

        switch ($data['ResponsibleType']) {
            case 'farmer':
                $sql="SELECT
                        a.FarmerID
                        , a.`FarmerName`
                        , a.Birthdate
                        , a.HandPhone
                        , IF(a.`Gender`='1','m',IF(a.`Gender`='2','f',NULL)) AS Gender
                    FROM
                        ktv_farmer a
                    WHERE
                        a.`FarmerID` = ?
                    LIMIT 1";
                $query = $this->db->query($sql,array($data['Responsible']));
                $dataRespon = $query->row_array();

                $dataReturn['ResponsibleType'] = 'farmer';
                $dataReturn['ResponsibleID'] = $dataRespon['FarmerID'];
                $dataReturn['ResponsibleName'] = $dataRespon['FarmerName'];
                $dataReturn['ResponsibleBirthday'] = $dataRespon['Birthdate'];
                $dataReturn['ResponsiblePhone'] = $dataRespon['HandPhone'];
                $dataReturn['ResponsibleGender'] = $dataRespon['Gender'];
            break;
            case 'staff':
                $sql="SELECT
                        a.`StaffID`
                        , b.`PersonNm`
                        , b.`BirthDate`
                        , a.`OfficialPhone`
                        , b.`Gender`
                    FROM
                        ktv_staffs a
                        INNER JOIN ktv_persons b ON a.`PersonID` = b.`PersonID`
                    WHERE
                        a.`StaffID` = ?
                    LIMIT 1";
                $query = $this->db->query($sql,array($data['Responsible']));
                $dataRespon = $query->row_array();

                $dataReturn['ResponsibleType'] = 'staff';
                $dataReturn['ResponsibleID'] = $dataRespon['StaffID'];
                $dataReturn['ResponsibleName'] = $dataRespon['PersonNm'];
                $dataReturn['ResponsibleBirthday'] = $dataRespon['BirthDate'];
                $dataReturn['ResponsiblePhone'] = $dataRespon['OfficialPhone'];
                $dataReturn['ResponsibleGender'] = $dataRespon['Gender'];
            break;
            case 'other':
                $dataReturn['ResponsibleType'] = 'other';
                $dataReturn['ResponsibleID'] = null;
                $dataReturn['ResponsibleName'] = $data['ResponsibleName'];
                $dataReturn['ResponsibleBirthday'] = $data['ResponsibleBirthday'];
                $dataReturn['ResponsiblePhone'] = $data['ResponsiblePhone'];
                $dataReturn['ResponsibleGender'] = $data['ResponsibleGender'];
            break;
        }

        $dataReturn['Established'] = $data['Established'];
        $dataReturn['CertificationStatus'] = $data['CertificationStatus'];
        $dataReturn['DateCertification'] = $data['DateCertification'];
        $dataReturn['DateAppliedCertification'] = $data['DateAppliedCertification'];
        $dataReturn['Panjang'] = $data['Panjang'];
        $dataReturn['Lebar'] = $data['Lebar'];
        $dataReturn['Latitude'] = $data['Latitude'];
        $dataReturn['Longitude'] = $data['Longitude'];

        //kasih nilai default
        foreach ($dataReturn as $key => $value) {
            if($dataReturn[$key] == ""){
                $dataReturn[$key] = "-";
            }
        }

        return $dataReturn;
    }

    public function getNurseryDataPenjualanPrint($NurseryID){
        $sql="SELECT
                a.Buyer
                , a.Volume
                , a.CloneTypeID
                , b.`CloneTypeName`
                , a.`Price`
                , DATE(a.`DateTransaction`) AS DateTransaction
            FROM
                ktv_nursery_transaction a
                LEFT JOIN ktv_clone_type b ON a.`CloneTypeID` = b.`CloneTypeID`
            WHERE
                a.`NurseryID` = ?
                AND a.`StatusCode` = 'active'
            ORDER BY a.`NurseryTransactionID` DESC
            LIMIT 50";
        $query = $this->db->query($sql,array($NurseryID));
        return $query->result_array();
    }

    public function getNurseryDataMonitoringPrint($NurseryID){
        $sql="SELECT
                a.MonitoringDate
                , a.`MonitoringStatus`
                , a.`Description`
            FROM
                ktv_nursery_monitoring a
            WHERE
                a.`NurseryID` = ?
                AND a.`StatusCode` = 'active'
            ORDER BY a.`NurseryMonitoringID` DESC
            LIMIT 50";
        $query = $this->db->query($sql,array($NurseryID));
        return $query->result_array();
    }

}
?>