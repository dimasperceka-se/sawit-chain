<?php
class Msync extends CI_Model {


    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

     function readCpgs($provID,$districtID){

        $sql = "SELECT CPGId, GroupName, Address,ksd.SubDistrict,kv.Village, 
                TahunTerbentuk, a.VillageID RegionID, Latitude,Longitude, Elevation,
                STATUS, a.DateCreated, a.CreatedBy, a.DateUpdated, a.LastModifiedBy, a.DateSynced
                FROM ktv_cpg a
                LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
                LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
                WHERE kd.ProvinceID = '$provID' 
                AND kd.DistrictID = '$districtID'";
         // 11,13,'72','73','74','76'
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

     function _generateCPGID($desa){
        $sql = "
            SELECT IFNULL(IF(length(max(CPGid))!=8,concat(kd.DistrictID,LPAD(substr(max(CPGid)+1,5,4),4,'0')),max(CPGid)+1),
               concat(substr(?,1,4),'0001')) as id
            FROM ktv_cpg
            LEFT JOIN ktv_village kv ON kv.VillageID = ktv_cpg.VillageID
            LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
            LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
            WHERE substr(CPGid,1,4)=substr(?,1,4)";
        $query = $this->db->query($sql,array($desa,$desa));
        $result = $query->result_array();        
        return $result[0]['id'];
    }

     function createCpg($GroupName,$Address,$TahunTerbentuk,$RegionID,$lat,$long,$ele,$status,$userid){
        $sql = "
            INSERT INTO ktv_cpg(CPGid,GroupName,Address, TahunTerbentuk, VillageID, Latitude, Longitude, 
               Elevation, Status,DateCreated,DateUpdated,CreatedBy,LastModifiedBy)
            VALUES (?,?,?,?,?,?,?,   ?,?,now(),now(),?,?)";
        $query = $this->db->query($sql, array($this->_generateCPGID($RegionID),$GroupName,$Address,$TahunTerbentuk,
            $RegionID,$lat,$long,$ele,$status,$userid,$userid));
        if ($query) {
            $results['id'] = $this->db->insert_id();
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

     function updateFarmer($Ssn,$PersonNm,$BirthDttm,$BirthPlace,$gambar,$Gender,$Address,$RegionalCd,$ZipCd,$Email,$BloodT,$MaritalSt,$Education,$NationalityNm,$Handphone,
      $FarmerGroupID,$LahanKosong,$Muge,$KeyFarmer,$DemoPlot,$OtherTraining,$CPGmembership,$OtherTrainingSiapa,$OtherTrainingTahun,$OtherTrainingLama,$DemoPlotLama,$DemoPlotRehab,$FarmerGroupFunctionsID,
      $personId,$date,$latitude,$longitude,$elevation,$farmerId){
      $sql_staff = "
         UPDATE ktv_farmer
         SET FarmerGroupID=?,LahanKosong=?,Muge=?,KeyFarmer=?,DemoPlot=?,OtherTraining=?,CPGmembership=?,
            OtherTrainingSiapa=?,OtherTrainingTahun=?,OtherTrainingLama=?,DemoPlotLama=?,DemoPlotRehab=?,
            FarmerGroupFunctionsID=?,DateUpdated=now(),DateCollection=?,
            FarmerName=?,Birthdate=?,Photo=?,Gender=?,Address=?,VillageID=?,MaritalStatus=?,Education=?,HandPhone=?,Latitude=?,Longitude=?,Elevation=?
            WHERE FarmerID=?";
      $this->db->trans_start();
      $query = $this->db->query($sql_staff, array($FarmerGroupID,$LahanKosong,$Muge,$KeyFarmer,$DemoPlot,$OtherTraining,
         $CPGmembership,$OtherTrainingSiapa,$OtherTrainingTahun,$OtherTrainingLama,$DemoPlotLama,$DemoPlotRehab,
         $FarmerGroupFunctionsID,$date,
         $PersonNm,$BirthDttm,$gambar,$Gender,$Address,$RegionalCd,$MaritalSt,$Education,$Handphone,$latitude,$longitude,$elevation,$farmerId));
      $this->db->trans_complete();
      if ($this->db->trans_status()) {
         $results['success'] = true;
         $results['message'] = "record updated.";
      } else {
         $results['success'] = false;
         $results['message'] = "Failed to update record";
      }
      return $results;
   }

    function updateGarden($FarmerID,$GardenNr,$DateCollection,$Latitude,$LatMin,$LatSec,$Longitude,$LongMin,$LongSec,
         $Elevation,$OwnershipCocoa,$TahunTanamanCocoa,$GardenDistance,$GardenHaUnCertified,$Production,$PanenBiasaMonths,
         $PanenBiasaPanenMonth,$PanenBiasaKg,$PanenTrekMonths,$PanenTrekPanenMonth,$PanenTrekKg,$PanenRayaMonths,
         $PanenRayaPanenMonth,$PanenRayaKg,$TimeHarvestBiasa,$TimeHarvestTrek,$TimeHarvestRaya,$LandCertificate,$PohonTBM,
         $PohonTM,$PohonRehab,$GraftedTrees,$ReplantedTrees,$RoadCondition,$Comment,$TSH858,$RCC70,$RCC71,$RCC72,$RCC73,
         $Hybrid,$S1,$S2,$ICRRI3,$ICRRI4,$ICRRI5,$CloneLain,$Gamal,$Kelapa,$Durian,$Pinang,$Karet,$JackFruit,$Lamtoro,
         $Mahoni,$Pisang,$Rambutan,$Mangga,$Langsat,$ShadeLain,$ShadeTreesNr,$TimeHarvest,$HarvestAwal,$HarvestMasak,
         $HarvestHama,$PruningPlants,$FrequentPruning,$HighPruning,$PruningProtectPlants,$FrequentPruningProtect,$CleanSkin,
         $HowToCleanSkin,$OrganicKotoran,$OrganicResidu,$OrganicMembeli,$TidakMemakaiOrganic,$Urea,$TSP,$NPK,$KCL,
         $TidakMemakaiKimia,$FrequentFertilizationOrganic,$DoseFertilizerOrganic,$FrequentFertilizationKimia,
         $DoseFertilizerKimia,$PakaiKompos,$FrequentFertilizationKompos,$DoseFertilizerKompos,$FrUrea,$FrTsp,$FrNpk,
         $FrKcl,$DpUrea,$DoTsp,$DoNpk,$DoKcl,$KimiaDana,$KimiaSupplier,$KimiaDilatih,$HamaBPK,$HamaHelopeltis,$HamaBatang,
         $PenyakitKanker,$PenyakitBusuk,$PenyakitUpas,$PenyakitAkar,$PenyakitVSD,$PenyakitAntraknose,$Herbisida,
         $MerekHerbisida,$FrequentHerbisida,$DoseHerbisida,$Herbisida1,$Herbisida2,$Herbisida3,$Herbisida4,$Herbisida5,
         $Herbisida6,$Herbisida7,$Herbisida8,$Herbisida9,$Herbisida10,$Insectisida,$MerekInsectisida,$FrequentInsectisida,
         $DoseInsectisida,$Insectisida1,$Insectisida2,$Insectisida3,$Insectisida4,$Insectisida5,$Insectisida6,$Insectisida7,
         $Insectisida8,$Insectisida9,$Insectisida10,$Fungisida,$MerekFungisida,$FrequentFungisida,$DoseFungisida,$Fungisida1,
         $Fungisida2,$Fungisida3,$Fungisida4,$Fungisida5,$Fungisida6,$Fungisida7,$Fungisida8,$Fungisida9,$Fungisida10,$APD,
         $TempatSimpanPestisida,$BuangKemasanPestisida,$SurveyNr){
        $sql = "
            UPDATE ktv_farmer_garden
            SET GardenNr=?,DateCollection=?,Latitude=?,LatMin=?,LatSec=?,Longitude=?,LongMin=?,LongSec=?,Elevation=?,OwnershipCocoa=?,TahunTanamanCocoa=?,GardenDistance=?,GardenHaUnCertified=?,Production=?,PanenBiasaMonths=?,PanenBiasaPanenMonth=?,PanenBiasaKg=?,PanenTrekMonths=?,PanenTrekPanenMonth=?,PanenTrekKg=?,PanenRayaMonths=?,PanenRayaPanenMonth=?,PanenRayaKg=?,TimeHarvestBiasa=?,TimeHarvestTrek=?,TimeHarvestRaya=?,LandCertificate=?,PohonTBM=?,PohonTM=?,PohonRehab=?,GraftedTrees=?,ReplantedTrees=?,RoadCondition=?,Comment=?,TSH858=?,RCC70=?,RCC71=?,RCC72=?,RCC73=?,Hybrid=?,S1=?,S2=?,ICRRI3=?,ICRRI4=?,ICRRI5=?,CloneLain=?,Gamal=?,Kelapa=?,Durian=?,Pinang=?,Karet=?,JackFruit=?,Lamtoro=?,Mahoni=?,Pisang=?,Rambutan=?,Mangga=?,Langsat=?,ShadeLain=?,ShadeTreesNr=?,TimeHarvest=?,HarvestAwal=?,HarvestMasak=?,HarvestHama=?,PruningPlants=?,FrequentPruning=?,HighPruning=?,PruningProtectPlants=?,FrequentPruningProtect=?,CleanSkin=?,HowToCleanSkin=?,OrganicKotoran=?,OrganicResidu=?,OrganicMembeli=?,TidakMemakaiOrganic=?,Urea=?,TSP=?,NPK=?,KCL=?,TidakMemakaiKimia=?,FrequentFertilizationOrganic=?,DoseFertilizerOrganic=?,FrequentFertilizationKimia=?,DoseFertilizerKimia=?,PakaiKompos=?,FrequentFertilizationKompos=?,DoseFertilizerKompos=?,FrUrea=?,FrTsp=?,FrNpk=?,FrKcl=?,DoUrea=?,DoTsp=?,DoNpk=?,DoKcl=?,KimiaDana=?,KimiaSupplier=?,KimiaDilatih=?,HamaBPK=?,HamaHelopeltis=?,HamaBatang=?,PenyakitKanker=?,PenyakitBusuk=?,PenyakitUpas=?,PenyakitAkar=?,PenyakitVSD=?,PenyakitAntraknose=?,Herbisida=?,MerekHerbisida=?,FrequentHerbisida=?,DoseHerbisida=?,Herbisida1=?,Herbisida2=?,Herbisida3=?,Herbisida4=?,Herbisida5=?,Herbisida6=?,Herbisida7=?,Herbisida8=?,Herbisida9=?,Herbisida10=?,Insectisida=?,MerekInsectisida=?,FrequentInsectisida=?,DoseInsectisida=?,Insectisida1=?,Insectisida2=?,Insectisida3=?,Insectisida4=?,Insectisida5=?,Insectisida6=?,Insectisida7=?,Insectisida8=?,Insectisida9=?,Insectisida10=?,Fungisida=?,MerekFungisida=?,FrequentFungisida=?,DoseFungisida=?,Fungisida1=?,Fungisida2=?,Fungisida3=?,Fungisida4=?,Fungisida5=?,Fungisida6=?,Fungisida7=?,Fungisida8=?,Fungisida9=?,Fungisida10=?,APD=?,TempatSimpanPestisida=?,BuangKemasanPestisida=?,
                DateUpdated=now()
            WHERE FarmerID=? and GardenNr=? and SurveyNr=?";
        $query = $this->db->query($sql, array($GardenNr,$DateCollection,$Latitude,$LatMin,$LatSec,$Longitude,$LongMin,$LongSec,
            $Elevation,$OwnershipCocoa,$TahunTanamanCocoa,$GardenDistance,$GardenHaUnCertified,$Production,$PanenBiasaMonths,
            $PanenBiasaPanenMonth,$PanenBiasaKg,$PanenTrekMonths,$PanenTrekPanenMonth,$PanenTrekKg,$PanenRayaMonths,
            $PanenRayaPanenMonth,$PanenRayaKg,$TimeHarvestBiasa,$TimeHarvestTrek,$TimeHarvestRaya,$LandCertificate,$PohonTBM,
            $PohonTM,$PohonRehab,$GraftedTrees,$ReplantedTrees,$RoadCondition,$Comment,$TSH858,$RCC70,$RCC71,$RCC72,$RCC73,
            $Hybrid,$S1,$S2,$ICRRI3,$ICRRI4,$ICRRI5,$CloneLain,$Gamal,$Kelapa,$Durian,$Pinang,$Karet,$JackFruit,$Lamtoro,$Mahoni,
            $Pisang,$Rambutan,$Mangga,$Langsat,$ShadeLain,$ShadeTreesNr,$TimeHarvest,$HarvestAwal,$HarvestMasak,$HarvestHama,
            $PruningPlants,$FrequentPruning,$HighPruning,$PruningProtectPlants,$FrequentPruningProtect,$CleanSkin,$HowToCleanSkin,
            $OrganicKotoran,$OrganicResidu,$OrganicMembeli,$TidakMemakaiOrganic,$Urea,$TSP,$NPK,$KCL,$TidakMemakaiKimia,
            $FrequentFertilizationOrganic,$DoseFertilizerOrganic,$FrequentFertilizationKimia,$DoseFertilizerKimia,$PakaiKompos,
            $FrequentFertilizationKompos,$DoseFertilizerKompos,$FrUrea,$FrTsp,$FrNpk,$FrKcl,$DpUrea,$DoTsp,$DoNpk,$DoKcl,
            $KimiaDana,$KimiaSupplier,$KimiaDilatih,$HamaBPK,$HamaHelopeltis,$HamaBatang,$PenyakitKanker,$PenyakitBusuk,
            $PenyakitUpas,$PenyakitAkar,$PenyakitVSD,$PenyakitAntraknose,$Herbisida,$MerekHerbisida,$FrequentHerbisida,
            $DoseHerbisida,$Herbisida1,$Herbisida2,$Herbisida3,$Herbisida4,$Herbisida5,$Herbisida6,$Herbisida7,$Herbisida8,
            $Herbisida9,$Herbisida10,$Insectisida,$MerekInsectisida,$FrequentInsectisida,$DoseInsectisida,$Insectisida1,
            $Insectisida2,$Insectisida3,$Insectisida4,$Insectisida5,$Insectisida6,$Insectisida7,$Insectisida8,$Insectisida9,
            $Insectisida10,$Fungisida,$MerekFungisida,$FrequentFungisida,$DoseFungisida,$Fungisida1,$Fungisida2,$Fungisida3,
            $Fungisida4,$Fungisida5,$Fungisida6,$Fungisida7,$Fungisida8,$Fungisida9,$Fungisida10,$APD,$TempatSimpanPestisida,
            $BuangKemasanPestisida,$FarmerID,$GardenNr,$SurveyNr));

        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }



    function readAreas(){

        $sql = "select a.VillageID RegionID, a.Village RegionName,c.ProvinceID ProvinceCode,c.DistrictID DistrictCode, 
                b.SubDistrictID SubDistrictCode,a.VillageID VillageCode, d.Province Provinsi,c.District  Kabupaten,
                b.SubDistrict Kecamatan,a.Village Desa, now() DateCreated, now() DateUpdated
                from ktv_village a,ktv_subdistrict b,ktv_district c, ktv_province d
                where 
                a.SubDistrictID=b.SubDistrictID
                AND b.DistrictID=c.DistrictID
                AND c.ProvinceID=d.ProvinceID AND c.ProvinceID=11";
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    //deprecated
    function readRegionals(){

        $sql = "select RegionID, RegionName, ProvinceCode, DistrictCode, SubDistrictCode, VillageCode, Status"
            . " from ktv_regional "
            . " where "
            . " ProvinceCode IN ('13')";

        // 11,13,'72','73','74','76'
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $rs)
        {
            $results[] = $rs;
        }
        return $results;
    }

    function readFarmers($provID,$districtID){
        $sql = "SELECT 
            a.FarmerID, a.FarmerGroupID,a.FarmerGroupFunctionsID, a.FarmerName,a.VillageID RegionID,
            a.Address, a.Handphone, a.Gender, a.MaritalStatus, a.Birthdate, a.Education, a.Photo_base64 Photo,
            a.latitude, a.Longitude, a.Elevation,'0' Family, a.LahanKosong, a.Muge, a.KeyFarmer,
            a.OtherTraining, a.CPGmembership, a.OtherTrainingSiapa, a.OtherTrainingTahun, a.OtherTrainingLama, a.DemoPlotLama,
            a.DemoPlotRehab,a.DateCreated DateCollection, a.DateCreated, a.DateUpdated, a.LastModifiedBy
            FROM ktv_farmer a
            LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
            LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
            LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
            LEFT JOIN ktv_province kp ON kp.ProvinceID = kd.ProvinceID
            WHERE 
            a.StatusCode='active' 
            AND kp.ProvinceID = '$provID' AND kd.DistrictID = '$districtID'";
            $query = $this->db->query($sql);
            $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
            $result['data'] = $query->result_array();
            return $result;
    }

    function readCpgBatchTrainings($provID,$districtID){

        $sql = "select a.CpgBatchTrainingID, a.CPGID, a.CPGtrainingsID,
             c.PersonNm ProgramStaffName,d.FarmerName KeyFarmerName, e.PersonNm Penyuluh,
             a.TrainingStart,a.TrainingEnd, a.DateCreated,a.DateUpdated,a.DateSynced  
             from ktv_cpg_batch_trainings a 
             INNER JOIN ktv_cpg b ON a.CPGID = b.CPGId 
             LEFT JOIN ktv_persons c ON a.ProgramStaffID = c.PersonID
             LEFT JOIN ktv_farmer d ON a.KeyFarmerID = d.FarmerID
             LEFT JOIN ktv_persons e ON a.ExtensionStaffID = e.PersonID
             WHERE 
             SUBSTR(d.VillageID FROM 1 FOR 2)='$provID' AND SUBSTR(d.VillageID FROM 1 FOR 4)='$districtID'";
            $query = $this->db->query($sql);
            $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
            $result['data'] = $query->result_array();
            return $result;
    }

     function readCpgBatchTrainingFarmers($provID,$districtID){

        $sql = "select a.CpgBatchTrainingsFarmerID, a.CpgBatchTrainingID, a.FarmerID, b.FarmerName,
             a.PetaniKakao, a.FamilyID, c.AnggotaName FamilyName,
             a.WritingAwal,a.WritingAkhir,a.BallotAwal,a.BallotAkhir,
             a.DateCreated, a.DateUpdated, a.LastModifiedBy, a.DateSynced
             from ktv_cpg_batch_trainings_farmers a
             INNER JOIN ktv_farmer b ON a.FarmerID = b.FarmerID
             LEFT JOIN ktv_family c ON a.FamilyID = c.FamilyID
             WHERE
             SUBSTR(b.VillageID FROM 1 FOR 2)='$provID' AND SUBSTR(b.VillageID FROM 1 FOR 4)='$districtID' limit 0,100";
            $query = $this->db->query($sql);
            $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
            $result['data'] = $query->result_array();
            return $result;
    }

    
    function readFarmerGardens($provID,$districtID){
        $sql ="SELECT a.FarmerID,a.GardenNr,a.SurveyNr,a.DateCollection,a.Latitude,a.Longitude,a.Elevation,a.OwnershipCocoa,"
            . " a.TahunTanamanCocoa,a.GardenDistance,a.GardenHaUnCertified,a.Production,a.PanenBiasaMonths,"
            . " a.PanenBiasaPanenMonth,a.PanenBiasaKg,a.PanenTrekMonths,a.PanenTrekPanenMonth,a.PanenTrekKg,"
            . " a.PanenRayaMonths,a.PanenRayaPanenMonth,a.PanenRayaKg,a.PohonTBM,a.PohonTM,a.PohonRehab,"
            . " a.GraftedTrees,a.ReplantedTrees,a.RoadCondition,a.TSH858,a.RCC70,a.RCC71,a.RCC72,a.RCC73,"
            . " a.Hybrid,a.S1,a.S2,a.ICRRI3,a.ICRRI4,a.ICRRI5,a.CloneLain,a.Gamal,a.Kelapa,a.Durian,a.Pinang,"
            . " a.Karet,a.JackFruit,a.Lamtoro,a.Mahoni,a.Pisang,a.Rambutan,a.Mangga,a.Langsat,a.ShadeLain,a.ShadeTreesNr,"
            . " a.TimeHarvest,a.HarvestAwal,a.HarvestMasak,a.HarvestHama,a.PruningPlants,a.FrequentPruning,"
            . " a.HighPruning,a.PruningProtectPlants,a.FrequentPruningProtect,a.CleanSkin,a.HowToCleanSkin,"
            . " a.OrganicKotoran,a.OrganicResidu,a.OrganicMembeli,a.TidakMemakaiOrganic,a.Urea,a.TSP,a.NPK,"
            . " a.KCL,a.TidakMemakaiKimia,a.FrequentFertilizationOrganic,a.DoseFertilizerOrganic,"
            . " a.FrequentFertilizationKimia,a.DoseFertilizerKimia,a.PakaiKompos,a.FrequentFertilizationKompos,"
            . " a.DoseFertilizerKompos,a.Herbisida,a.Fungisida,a.Insectisida,a.FrequentHerbisida,"
            . " a.FrequentInsectisida,a.FrequentFungisida,a.DoseHerbisida,a.DoseInsectisida,a.DoseFungisida,"
            . " a.MerekHerbisida,a.MerekInsectisida,a.MerekFungisida,a.TempatSimpanPestisida,a.BuangKemasanPestisida,"
            . " a.FrUrea,a.FrTsp,a.FrNpk,a.FrKcl,a.DoUrea,a.DoTsp,a.DoNpk,a.DoKcl,"
            . " a.KimiaDana,a.KimiaSupplier,a.KimiaDilatih,"
            . " a.HamaBPK,a.HamaHelopeltis,a.HamaBatang,a.PenyakitKanker,a.PenyakitBusuk,a.PenyakitUpas,a.PenyakitVSD,a.PenyakitAntraknose,"
            . " a.Herbisida1,a.Herbisida2,a.Herbisida3,a.Herbisida4,a.Herbisida5,a.Herbisida6,a.Herbisida7,a.Herbisida8,a.Herbisida9,a.Herbisida10,"
            . " a.Insectisida1,a.Insectisida2,a.Insectisida3,a.Insectisida4,a.Insectisida5,a.Insectisida6,a.Insectisida7,a.Insectisida8,a.Insectisida9,a.Insectisida10,"
            . " a.Fungisida1,a.Fungisida2,a.Fungisida3,a.Fungisida4,a.Fungisida5,a.Fungisida6,a.Fungisida7,a.Fungisida8,a.Fungisida9,a.Fungisida10,a.APD,"
            . " a.DateCreated, a.DateUpdated, a.DateSynced"
            . " FROM ktv_farmer_garden a, ktv_farmer b"
            . " WHERE"
            . " a.FarmerID = b.FarmerID AND SUBSTR(b.VillageID FROM 1 FOR 2)='$provID' AND SUBSTR(b.VillageID FROM 1 FOR 4)='$districtID' limit 0,100";
            $query = $this->db->query($sql);
            $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
            $result['data'] = $query->result_array();
            return $result;
    }

     function readPostHarvests($provID,$districtID){
        $sql ="SELECT a.FarmerID,
                a.PrePostSurvey,
                a.SurveyNr,
                a.DateCollection,
                a.AnggotaKerjaKebun,
                a.BuruhSeasonal,
                a.BuruhFulltime,
                a.Fermentation,
                a.FermentationDays,
                a.SunDryingSemen,
                a.DryingAlat,
                a.DryingDays,
                a.CocoaBuyers,
                a.NoFermentation,
                a.Sortasi,
                a.NoSortasi,
                a.SunDryingAspal,
                a.JemurYesNo,
                a.TidakJemur,
                a.SunDryingAlas,
                a.DateCreated,
                a.DateUpdated,
                a.LastModifiedBy,
                a.StatusCode,
                a.DateSynced
                FROM
                ktv_farmer_post_harvest a,
                ktv_farmer b
                WHERE
                a.FarmerID = b.FarmerID AND
                SUBSTR(b.VillageID FROM 1 FOR 2)='$provID' AND SUBSTR(b.VillageID FROM 1 FOR 4)='$districtID'";
            $query = $this->db->query($sql);
            $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
            $result['data'] = $query->result_array();
            return $result;
    }

    function readNutritions($provID,$districtID){
        $sql ="SELECT
                a.FarmerID,
                a.InterviewDate,
                a.SurveyNr,
                a.KebunPanjang,
                a.KebunLebar,
                a.KbBayam,
                a.KbCabai,
                a.KbKacangPanjang,
                a.KbKangkung,
                a.KbSawi,
                a.KbTerong,
                a.KbTomat,
                a.KbKambing,
                a.KbSapi,
                a.KbBebek,
                a.KbAyam,
                a.KbIkan,
                a.aSagu,
                a.aNasi,
                a.aJagung,
                a.aRoti,
                a.bUbiJalarKuning,
                a.bSingkongKuning,
                a.bWortel,
                a.bLabu,
                a.cUbiJalarPutih,
                a.cSingkongPutih,
                a.cTalas,
                a.cKentang,
                a.dBayam,
                a.dDaunMelinjo,
                a.dDaunPepaya,
                a.dDaunSingkong,
                a.dKangkung,
                a.dSawi,
                a.eKacangPanjang,
                a.eTomat,
                a.eTerong,
                a.fJambuMerah,
                a.fMangga,
                a.fPepaya,
                a.gJambuAir,
                a.gKelapa,
                a.gPisang,
                a.gRambutan,
                a.gSemangka,
                a.gSalak,
                a.hJeroan,
                a.hHati,
                a.iAyam,
                a.iBebek,
                a.iKambing,
                a.iKerbau,
                a.iSapi,
                a.iLainnya,
                a.jAyam,
                a.jBebek,
                a.jEntok,
                a.jPuyuh,
                a.kCumiCumi,
                a.kIkan,
                a.kIkanTeri,
                a.kKepiting,
                a.kKerang,
                a.kUdang,
                a.lAirTahuSusuKedelai,
                a.lSausKacang,
                a.lTahu,
                a.lTempe,
                a.mKeju,
                a.mSusu,
                a.nMinyakGoreng,
                a.nMentega,
                a.nSantan,
                a.Score,
                a.DateCreated,
                a.DateUpdated,
                a.LastModifiedBy,
                a.DateSynced,
                a.StatusCode
                FROM
                ktv_nutrition AS a ,
                ktv_farmer AS b
                WHERE
                a.FarmerID = b.FarmerID AND
                SUBSTR(b.VillageID FROM 1 FOR 2) = '$provID' AND SUBSTR(b.VillageID FROM 1 FOR 4) = '$districtID'";
            $query = $this->db->query($sql);
            $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
            $result['data'] = $query->result_array();
            return $result;
    }

    function readPPIScorecards($provID,$districtID){
        $sql ="SELECT
                a.FarmerID,
                a.SurveyNr,
                a.InterviewDate,
                a.Householdmembers,
                a.Schooling,
                a.Education,
                a.Employment,
                a.ToiletFacility,
                a.HouseFloor,
                a.CookingFuel,
                a.Refrigerator,
                a.GasCylinder,
                a.DateCreated,
                a.DateUpdated,
                a.DateSynced,
                a.LastModifiedBy,
                a.StatusCode
                FROM
                ktv_ppiscore2012 AS a ,
                ktv_farmer AS b
                WHERE
                a.FarmerID = b.FarmerID AND
                SUBSTR(b.VillageID FROM 1 FOR 2)='$provID' AND SUBSTR(b.VillageID FROM 1 FOR 4)='$districtID'";
            $query = $this->db->query($sql);
            $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
            $result['data'] = $query->result_array();
            return $result;
    }

    function readFamilies($provID,$districtID){
        $sql ="select a.* 
            FROM ktv_family a, 
            ktv_farmer b
            WHERE 
            a.FarmerID = b.FarmerID
            AND SUBSTR(b.VillageID FROM 1 FOR 2)='$provID' AND SUBSTR(b.VillageID FROM 1 FOR 4)='$districtID'";
            $query = $this->db->query($sql);
            $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
            $result['data'] = $query->result_array();
            return $result;
    }

	function readSurvey(){
			$sql ="select SurveyNr,concat(SurveyNr,'-',SurveyTxt) SurveyTxt FROM ktv_survey";
            $query = $this->db->query($sql);
            $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
            $result['data'] = $query->result_array();
            return $result;
    }

    function sync_up_farmer(){

        //indikator
        // $indikator1       = $this->input->post('rb1');
        // $_REQUEST["org_id"];
        $FarmerID =$_REQUEST["FarmerID"];
        $FarmerGroupID =$_REQUEST["FarmerGroupID"];
        $FarmerName=$_REQUEST["FarmerName"];
        $Province=$_REQUEST["Province"];
        $District=$_REQUEST["District"];
        $SubDistrict=$_REQUEST["SubDistrict"];
        $Address=$_REQUEST["Address"];
        $Handphone=$_REQUEST["Handphone"];
        $Gender=$_REQUEST["Gender"];
        $MaritalStatus=$_REQUEST["MaritalStatus"];
        $Birthdate=$_REQUEST["Birthdate"];
        $Education=$_REQUEST["Education"];
        // $Photo=$this->input->post('Photo');
        // $WritingAwal=$this->input->post('WritingAwal');
        // $WritingAkhir=$this->input->post('WritingAkhir');
        // $BallotAwal=$this->input->post('BallotAwal');
        // $BallotAkhir=$this->input->post('BallotAkhir');
        // $Muge=$this->input->post('Muge');
        // $KeyFarmer=$this->input->post('KeyFarmer');
        // $AnggotaKerjaKebun=$this->input->post('AnggotaKerjaKebun');
        // $BuruhSeasonal=$this->input->post('BuruhSeasonal');
        // $BuruhFulltime=$this->input->post('BuruhFulltime');
        // $HarvestYesNo=$this->input->post('HarvestYesNo');
        // $Fermentation=$this->input->post('Fermentation');
        // $FermentationDays=$this->input->post('FermentationDays');
        // $SunDryingSemen=$this->input->post('SunDryingSemen');
        // $DryingAlat=$this->input->post('DryingAlat');
        // $DryingDays=$this->input->post('DryingDays');
        // $CocoaBuyers=$this->input->post('CocoaBuyer');
        // $NoFermentation=$this->input->post('NoFermentation');
        // $Sortasi=$this->input->post('Sortasi');
        // $NoSortasi=$this->input->post('NoSortasi');
        // $LahanKosong=$this->input->post('LahanKosong');
        // $SunDryingAspal=$this->input->post('SunDryingAspal');
        // $JemurYesNo=$this->input->post('JemurYesNo');
        // $TidakJemur=$this->input->post('TidakJemur');
        // $SunDryingAlas=$this->input->post('SunDryingAlas');
        // $OtherTraining=$this->input->post('OtherTraining');
        // $CPGmembership=$this->input->post('CPGmembership');
        // $OtherTrainingSiapa=$this->input->post('OtherTrainingSiapa');
        // $OtherTrainingLama=$this->input->post('OtherTrainingLama');
        // $DemoPlotLama=$this->input->post('DemoPlotLama');
        // $DateCollection=$this->input->post('DateCollection');
        // $FarmerGroupFunctionsID=$this->input->post('FarmerGroupFunctionsID');
        // $DateCreated=$this->input->post('DateCreated');
        // $DateUpdated=$this->input->post('DateUpdated');
        // $LastModifiedBy=$this->input->post('LastModifiedBy');


        $sql  = "INSERT INTO tblcocoafarmer(FarmerID,FarmerGroupID, FarmerName,Province,District,SubDistrict,Address,Handphone,Gender,MaritalStatus,Birthdate,Education)";
        $sql .= "VALUES ('".$FarmerID."','".$FarmerGroupID."', '".$FarmerName."','".$Province."', '".$District."', '".$SubDistrict."','".$Address."', '".$Handphone."','".$Gender."', '".$MaritalStatus."','".$Birthdate."', '".$Education."')";
        $this->db->query($sql);

    }

    function fixTableFarmer(){
        $sql = "SELECT CPGid,FarmerID FROM ktv_cpg_farmer_member";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $rs)
        {
            $sql = "UPDATE ktv_farmer SET FarmerGroupID=".$rs["CPGid"]." WHERE FarmerID=".$rs["FarmerID"]."";
            $this->db->query($sql);
        }
        if ($query) {
            $results['success'] = true;
            $results['message'] = "table fixed.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to fixed table";
        }
        return $results;
       
    }



}
?>