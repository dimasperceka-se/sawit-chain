<?php

class Msync extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    function readLogin($username, $password) {
        $sql = "SELECT
              su.UserID,
              su.UserRealName,
              su.UserName,
              IFNULL(kpp.PartnerID,kpp2.PartnerID) PartnerID,
              IFNULL(kpp.PartnerName,kpp2.PartnerName) PartnerName
            FROM
              sys_user su
              LEFT JOIN ktv_program_staff kps ON su.UserID = kps.UserID
              LEFT JOIN ktv_program_partner kpp ON kps.PartnerID = kpp.PartnerID
              LEFT JOIN ktv_private_staff kps2 ON su.UserID = kps2.UserID
              LEFT JOIN ktv_program_partner kpp2 ON kps2.PartnerID = kpp2.PartnerID
            WHERE su.UserName='$username' AND su.UserPassword=MD5('$password')";
        // 11,13,'72','73','74','76'
        $query = $this->db->query($sql);
        // $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result = $query->result_array();
        return $result[0];
    }

    function readCpgs($provID, $districtID) {
        $sql = "SELECT CPGid,OldCPGid, GroupName, Address,b.SubDistrict,c.Village,
                TahunTerbentuk, a.VillageID RegionID, Latitude,Longitude, Elevation,
                STATUS, DateCreated, CreatedBy, DateUpdated, LastModifiedBy, DateSynced
                FROM ktv_cpg a, ktv_subdistrict b, ktv_village c
                WHERE SUBSTRING( a.VillageID, 1, 2 ) = '$provID'
                AND SUBSTRING( a.VillageID, 1, 4 ) = '$districtID'
                AND SUBSTRING( a.VillageID, 1, 7 ) = b.SubDistrictID
                AND a.VillageID = c.VillageID ";
        // 11,13,'72','73','74','76'
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readCpg($provID, $districtID, $LastDownloadDateUpdated) {

        $sql = "SELECT a.*,b.SubDistrict,c.Village
                FROM ktv_cpg a, ktv_subdistrict b, ktv_village c
                WHERE SUBSTRING( a.VillageID, 1, 2 ) = '$provID'
                AND SUBSTRING( a.VillageID, 1, 4 ) = '$districtID'
                AND SUBSTRING( a.VillageID, 1, 7 ) = b.SubDistrictID
                AND a.VillageID = c.VillageID
                AND (a.DateCreated>TIMESTAMP('$LastDownloadDateUpdated') OR a.DateUpdated>TIMESTAMP('$LastDownloadDateUpdated'))";
        // 11,13,'72','73','74','76'
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function _generateCPGID($desa) {
        $sql = "
            SELECT IFNULL(IF(length(max(CPGid))!=8,concat(kd.DistrictID,LPAD(substr(max(CPGid)+1,5,4),4,'0')),max(CPGid)+1),
               concat(substr(?,1,4),'0001')) as id
            FROM ktv_cpg
            LEFT JOIN ktv_village kv ON kv.VillageID = ktv_cpg.VillageID
            LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
            LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
            WHERE substr(CPGid,1,4)=substr(?,1,4)";
        $query = $this->db->query($sql, array($desa, $desa));
        $result = $query->result_array();
        return $result[0]['id'];
    }

    function createCpg($GroupName, $Address, $TahunTerbentuk, $RegionID, $lat, $long, $ele, $status, $datecreated, $createdby) {
        $sql = "
            INSERT INTO ktv_cpg(CPGid,GroupName,Address, TahunTerbentuk, VillageID, Latitude, Longitude,
               Elevation, Status,DateCreated,DateUpdated,CreatedBy,LastModifiedBy)
            VALUES (?,?,?,?,?,?,?, ?,?,?,?,?,?)";
        $query = $this->db->query($sql, array($this->_generateCPGID($RegionID), $GroupName, $Address, $TahunTerbentuk,
            $RegionID, $lat, $long, $ele, $status, $datecreated, $datecreated, $createdby, $createdby));
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

    function updateCpg($GroupName, $Address, $TahunTerbentuk, $RegionID, $lat, $long, $ele, $status, $id, $lastmodifiedby, $dateupdated) {
        $sql = "
            UPDATE ktv_cpg
            SET GroupName=?,Address=?, TahunTerbentuk=?, VillageID=?, Latitude=?, Longitude=?, Elevation=?, Status=?,
               DateUpdated=?,LastModifiedBy=?
            WHERE CPGid=?";
        $query = $this->db->query($sql, array($GroupName, $Address, $TahunTerbentuk, $RegionID, $lat, $long, $ele, $status, $dateupdated, $lastmodifiedby, $id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function _generateFarmerID($district) {
        $sql = "
            SELECT IFNULL(IF(length(max(FarmerId))!=9,concat(substr(FarmerId,1,4),LPAD(substr(max(FarmerId)+1,5,5),5,'0')),max(FarmerID)+1),
               concat(?,'00001')) as id
            FROM ktv_farmer
            WHERE substr(FarmerId,1,4)=substr(?,1,4)";
        $query = $this->db->query($sql, array($district, $district));
        $result = $query->result_array();
        return $result[0]['id'];
    }

    function createFarmer($FarmerName, $BirthDttm, $Gender, $Address, $RegionalCd, $MaritalSt, $Education, $Handphone, $FarmerGroupID, $LahanKosong, $Muge, $gambar, $LahanKakao, $LahanProduksiLain, $TotalLahan, $KebunKakao, $DateUpdated, $LastModifiedBy, $DateCollection, $CreatedBy, $DateCreated) {

        $sql = "
            INSERT INTO ktv_farmer(FarmerID,CPGid,LahanKosong,Muge,DateCollection,
              FarmerName,Birthdate,Photo_base64,Gender,Address,VillageID,MaritalStatus,Education,HandPhone,
              LahanKakao,LahanProduksiLain,TotalLahan,KebunKakao, DateCreated,CreatedBy,DateUpdated,LastModifiedBy)
            VALUES (?,?,?,?,?,?,?,   ?,?,?,?,?,?,   ?,?,   ?,?,?,?,?,?,?)";
        if ($FarmerGroupID == '')
            $FarmerGroupID = NULL;
        $idf = $this->_generateFarmerID(substr($RegionalCd, 0, 4));
        $query = $this->db->query($sql, array($idf, $FarmerGroupID, $LahanKosong, $Muge, $DateCollection,
            $FarmerName, $BirthDttm, $gambar, $Gender, $Address, $RegionalCd, $MaritalSt, $Education, $Handphone,
            $LahanKakao, $LahanProduksiLain, $TotalLahan, $KebunKakao, $DateCreated, $CreatedBy, $DateUpdated, $LastModifiedBy));

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

    function updateFarmer($FarmerID, $FarmerName, $Birthdate, $Gender, $Address, $VillageID, $MaritalStatus, $Education, $Handphone, $CPGid, $LahanKosong, $Muge, $Photo, $LahanKakao, $LahanProduksiLain, $TotalLahan, $KebunKakao, $DateUpdated, $LastModifiedBy, $DateCollection, $CreatedBy, $DateCreated) {
        $sql_staff = "
         UPDATE ktv_farmer
         SET FarmerName=?,Birthdate=?,Photo_base64=?,Gender=?,Address=?,VillageID=?,MaritalStatus=?,Education=?,HandPhone=?,LahanKosong=?,Muge=?,LahanKakao=?,LahanProduksiLain=?,TotalLahan=?,KebunKakao=?,DateUpdated=?,DateCollection=?,LastModifiedBy=?
            WHERE FarmerID=?";
        $this->db->trans_start();
        $query = $this->db->query($sql_staff, array($FarmerName, $Birthdate, $Photo, $Gender, $Address, $VillageID, $MaritalStatus, $Education, $Handphone, $LahanKosong, $Muge, $LahanKakao, $LahanProduksiLain, $TotalLahan, $KebunKakao, $DateUpdated, $DateCollection, $LastModifiedBy, $FarmerID));
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

    function readAreas($provID) {

        $sql = "select a.VillageID RegionID, a.Village RegionName,c.ProvinceID ProvinceCode,c.DistrictID DistrictCode,
                b.SubDistrictID SubDistrictCode,a.VillageID VillageCode, d.Province Provinsi,c.District  Kabupaten,
                b.SubDistrict Kecamatan,a.Village Desa, now() DateCreated, now() DateUpdated
                from ktv_village a,ktv_subdistrict b,ktv_district c, ktv_province d
                where
                a.SubDistrictID=b.SubDistrictID
                AND b.DistrictID=c.DistrictID
                AND c.ProvinceID=d.ProvinceID AND c.ProvinceID=$provID";
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    //deprecated
    function readRegionals() {

        $sql = "select RegionID, RegionName, ProvinceCode, DistrictCode, SubDistrictCode, VillageCode, Status"
                . " from ktv_regional "
                . " where "
                . " ProvinceCode IN ('13')";

        // 11,13,'72','73','74','76'
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $rs) {
            $results[] = $rs;
        }
        return $results;
    }

    function readFarmers($provID, $districtID) {
        $sql = "SELECT
            a.FarmerID,a.OldFarmerID, a.CPGid,a.OldCPGid, a.FarmerName,a.VillageID RegionID,
            a.Address, a.Handphone, a.Gender, a.MaritalStatus, a.Birthdate, a.Education, a.Photo_base64 Photo,
            a.latitude, a.Longitude, a.Elevation,'0' Family,  a.Muge, a.LahanKakao,a.LahanKosong,
            a.LahanProduksiLain, a.TotalLahan, a.KebunKakao,
            a.DateCollection, a.DateCreated, a.DateUpdated,a.CreatedBy, a.LastModifiedBy
            FROM ktv_farmer a
            WHERE
            a.StatusCode='active'
            AND substring(a.VillageID, 1, 2 ) = '$provID' AND substring(a.VillageID, 1, 4 ) = '$districtID'";
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readFarmer($provID, $districtID, $LastDownloadDateUpdated) {
        $sql = "SELECT a.*,a.VillageID RegionID 
                FROM ktv_farmer a
                LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
                LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
                LEFT JOIN ktv_province kp ON kp.ProvinceID = kd.ProvinceID
                WHERE kp.ProvinceID = '$provID' AND kd.DistrictID = '$districtID' AND (a.DateCreated>TIMESTAMP('$LastDownloadDateUpdated') OR a.DateUpdated>TIMESTAMP('$LastDownloadDateUpdated'))";
        log_message('debug', $sql);
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readCpgBatchTrainings($provID, $districtID) {

        $sql = "SELECT kcbt.CpgBatchTrainingID, kcbt.CPGid, kcbt.CPGtrainingsID,kct.CPGtrainings,kcb.BatchNumber,kpp.PartnerName,IFNULL(kp.PersonNm,kps2.StaffName) ProgramStaffName,  kcf.FarmerName KeyFarmerName,kcf2.FarmerName DemoPlotFarmer, kes.StaffName Penyuluh,
             kcbt.TrainingStart,kcbt.TrainingEnd,kcbt.TrainingDays,kfy.AnggotaName, kcbt.DateCreated,kcbt.DateUpdated,kcbt.DateSynced
             FROM ktv_cpg_batch_trainings kcbt
             INNER JOIN ktv_cpg_trainings kct ON kcbt.CPGtrainingsID = kct.CPGtrainingsID
             INNER JOIN ktv_cpg_batch kcb ON kcbt.CpgBatchID = kcb.CpgBatchID
             INNER JOIN ktv_program_partner kpp ON kcb.PartnerID = kpp.PartnerID
             INNER JOIN ktv_cpg kc ON kcbt.CPGid = kc.CPGid
             LEFT JOIN ktv_program_staff kps ON kcbt.ProgramStaffID = kps.StaffID
             LEFT JOIN ktv_persons kp ON kps.PersonID = kp.PersonID
             LEFT JOIN ktv_private_staff kps2 ON kcbt.ProgramStaffID = kps2.PrivateStaffID
             LEFT JOIN ktv_farmer kcf ON kcbt.KeyFarmerID = kcf.FarmerID
             LEFT JOIN ktv_farmer kcf2 ON kcbt.DemoplotOwnerID = kcf2.FarmerID
             LEFT JOIN ktv_extension_staff kes ON kcbt.ExtensionStaffID = kes.ExtensionID
             LEFT JOIN ktv_family kfy ON kcbt.FamilyID = kfy.FamilyID
             WHERE
             SUBSTR(kc.VillageID FROM 1 FOR 2)='$provID' AND SUBSTR(kc.VillageID FROM 1 FOR 4)='$districtID'";
        log_message('debug', $sql);
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readCpgBatchTraining($provID, $districtID, $LastDownloadDateUpdated) {

        $sql = "SELECT kcbt.CpgBatchTrainingID, kcbt.CPGid, kcbt.CPGtrainingsID,kct.CPGtrainings,kcbt.CpgBatchID,kcb.BatchNumber,kpp.PartnerName,kcbt.ProgramStaffID,IFNULL(kp.PersonNm,kps2.StaffName) ProgramStaffName,kcbt.KeyFarmerID,  kcf.FarmerName KeyFarmerName,kcbt.DemoplotOwnerID,kcf2.FarmerName DemoPlotFarmer, kcbt.ExtensionStaffID, kes.StaffName Penyuluh,
             kcbt.TrainingStart,kcbt.TrainingEnd,kcbt.TrainingDays,kcbt.PetaniKakao,kcbt.FamilyID,kfy.AnggotaName, kcbt.DateCreated,kcbt.DateUpdated,kcbt.DateSynced,kcbt.CreatedBy,kcbt.LastModifiedBy
             FROM ktv_cpg_batch_trainings kcbt
             INNER JOIN ktv_cpg_trainings kct ON kcbt.CPGtrainingsID = kct.CPGtrainingsID
             INNER JOIN ktv_cpg_batch kcb ON kcbt.CpgBatchID = kcb.CpgBatchID
             INNER JOIN ktv_program_partner kpp ON kcb.PartnerID = kpp.PartnerID
             INNER JOIN ktv_cpg kc ON kcbt.CPGid = kc.CPGid
             LEFT JOIN ktv_program_staff kps ON kcbt.ProgramStaffID = kps.StaffID
             LEFT JOIN ktv_persons kp ON kps.PersonID = kp.PersonID
             LEFT JOIN ktv_private_staff kps2 ON kcbt.ProgramStaffID = kps2.PrivateStaffID
             LEFT JOIN ktv_farmer kcf ON kcbt.KeyFarmerID = kcf.FarmerID
             LEFT JOIN ktv_farmer kcf2 ON kcbt.DemoplotOwnerID = kcf2.FarmerID
             LEFT JOIN ktv_extension_staff kes ON kcbt.ExtensionStaffID = kes.ExtensionID
             LEFT JOIN ktv_family kfy ON kcbt.FamilyID = kfy.FamilyID
             WHERE
             SUBSTR(kc.VillageID FROM 1 FOR 2)='$provID' AND SUBSTR(kc.VillageID FROM 1 FOR 4)='$districtID' AND (kcbt.DateCreated>TIMESTAMP('$LastDownloadDateUpdated')OR kcbt.DateUpdated>TIMESTAMP('$LastDownloadDateUpdated'))";
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readCpgBatchTrainingFarmers($provID, $districtID) {

        $sql = "select a.CpgBatchTrainingsFarmerID, a.CpgBatchTrainingID, a.FarmerID,a.OldFarmerID, b.FarmerName,
             a.PetaniKakao, a.FamilyID, c.AnggotaName FamilyName,
             a.WritingAwal,a.WritingAkhir,a.BallotAwal,a.BallotAkhir,
             a.DateCreated, a.DateUpdated,a.CreatedBy, a.LastModifiedBy, a.DateSynced
             from ktv_cpg_batch_trainings_farmers a
             INNER JOIN ktv_farmer b ON a.FarmerID = b.FarmerID
             LEFT JOIN ktv_family c ON a.FamilyID = c.FamilyID
             WHERE
             SUBSTR(b.VillageID FROM 1 FOR 2)='$provID' AND SUBSTR(b.VillageID FROM 1 FOR 4)='$districtID'";
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function createParticipant($CpgBatchTrainingID, $FarmerID, $PetaniKakao, $FamilyID, $WritingAwal, $WritingAkhir, $BallotAwal, $BallotAkhir, $DateCreated, $CreatedBy) {
        $sql = "
            INSERT INTO ktv_cpg_batch_trainings_farmers(CpgBatchTrainingID, FarmerID, PetaniKakao, FamilyID, WritingAwal, WritingAkhir, BallotAwal, BallotAkhir, DateCreated, CreatedBy)
            VALUES (?,?,?,?,?, ?,?,?,?,?)";
        $query = $this->db->query($sql, array($CpgBatchTrainingID, $FarmerID, $PetaniKakao, $FamilyID, $WritingAwal, $WritingAkhir, $BallotAwal, $BallotAkhir, $DateCreated, $CreatedBy));
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

    function readCpgBatchTrainingFarmer($provID, $districtID, $LastDownloadDateUpdated) {
        $sql = "select a.CpgBatchTrainingsFarmerID, a.CpgBatchTrainingID, a.FarmerID, b.FarmerName,
             a.PetaniKakao, a.FamilyID, c.AnggotaName FamilyName,
             a.WritingAwal,a.WritingAkhir,a.BallotAwal,a.BallotAkhir,
             a.DateCreated, a.DateUpdated,a.CreatedBy, a.LastModifiedBy, a.DateSynced
             from ktv_cpg_batch_trainings_farmers a
             INNER JOIN ktv_farmer b ON a.FarmerID = b.FarmerID
             LEFT JOIN ktv_family c ON a.FamilyID = c.FamilyID
             WHERE
             SUBSTR(b.VillageID FROM 1 FOR 2)='$provID' AND SUBSTR(b.VillageID FROM 1 FOR 4)='$districtID' AND (a.DateCreated>TIMESTAMP('$LastDownloadDateUpdated')OR a.DateUpdated>TIMESTAMP('$LastDownloadDateUpdated'))";
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readAccessStaff() {
        $sql = "SELECT * FROM ktv_access_staff";
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readIcsMember($LastDownloadDateUpdated) {
        $sql = "SELECT a.* FROM ktv_ics_members a WHERE (a.DateCreated>TIMESTAMP('$LastDownloadDateUpdated') OR a.DateUpdated>TIMESTAMP('$LastDownloadDateUpdated'))";
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readFarmerGardens($provID, $districtID) {
        $sql = "SELECT a.FarmerID,a.GardenNr,a.SurveyNr,a.DateCollection,a.Latitude,a.Longitude,a.Elevation,a.OwnershipCocoa,"
                . " a.TahunTanamanCocoa,a.GardenDistance,a.LandCertificate,a.GardenHaUnCertified,a.Production,a.PanenBiasaMonths,"
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
                . " a.Insectisida1,a.Insectisida2,a.Insectisida3,a.Insectisida4,a.Insectisida5,a.Insectisida6,a.Insectisida7,a.Insectisida8,a.Insectisida9,a.Insectisida10,a.Insectisida11,"
                . " a.Fungisida1,a.Fungisida2,a.Fungisida3,a.Fungisida4,a.Fungisida5,a.Fungisida6,a.Fungisida7,a.Fungisida8,a.Fungisida9,a.Fungisida10,a.Fungisida11,a.APD,"
                . " a.CreatedBy, a.LastModifiedBy, a.DateCreated, a.DateUpdated, a.DateSynced"
                . " FROM ktv_farmer_garden a, ktv_farmer b"
                . " WHERE"
                . " a.FarmerID = b.FarmerID AND SUBSTR(b.VillageID FROM 1 FOR 2)='$provID' AND SUBSTR(b.VillageID FROM 1 FOR 4)='$districtID' ORDER BY a.DateCollection DESC";
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readFarmerGarden($provID, $districtID, $LastDownloadDateUpdated) {
        $sql = "SELECT a.*"
                . " FROM ktv_farmer_garden a, ktv_farmer b"
                . " WHERE"
                . " a.FarmerID = b.FarmerID AND SUBSTR(b.VillageID FROM 1 FOR 2)='$provID' AND SUBSTR(b.VillageID FROM 1 FOR 4)='$districtID' AND (a.DateCreated>TIMESTAMP('$LastDownloadDateUpdated') OR a.DateUpdated>TIMESTAMP('$LastDownloadDateUpdated'))";
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readGardenStatus($provID, $districtID, $LastDownloadDateUpdated) {
        $sql = "SELECT a.*"
                . " FROM ktv_farmer_garden_status a, ktv_farmer b"
                . " WHERE"
                . " a.FarmerID = b.FarmerID AND SUBSTR(b.VillageID FROM 1 FOR 2)='$provID' AND SUBSTR(b.VillageID FROM 1 FOR 4)='$districtID' AND (a.DateCreated>TIMESTAMP('$LastDownloadDateUpdated') OR a.DateUpdated>TIMESTAMP('$LastDownloadDateUpdated'))";
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readOtherLand($provID, $districtID, $LastDownloadDateUpdated) {
        $sql = "SELECT a.*"
                . " FROM ktv_farmer_other_land a, ktv_farmer b"
                . " WHERE"
                . " a.FarmerID = b.FarmerID AND SUBSTR(b.VillageID FROM 1 FOR 2)='$provID' AND SUBSTR(b.VillageID FROM 1 FOR 4)='$districtID' AND (a.DateCreated>TIMESTAMP('$LastDownloadDateUpdated') OR a.DateUpdated>TIMESTAMP('$LastDownloadDateUpdated'))";
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readProvinces() {
        $sql = "SELECT a.* FROM ktv_province a";
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readDistricts() {
        $sql = "SELECT a.* FROM ktv_district a";
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readSubdistricts() {
        $sql = "SELECT a.* FROM ktv_subdistrict a";
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readSavingPilot($provID, $districtID, $LastDownloadDateUpdated) {
        $sql = "SELECT a.*"
                . " FROM ktv_saving_pilot a"
                . " WHERE"
                . " SUBSTR(a.FarmerID FROM 1 FOR 2)='$provID' AND SUBSTR(a.FarmerID FROM 1 FOR 4)='$districtID' AND (a.DateUpdated>TIMESTAMP('$LastDownloadDateUpdated') OR a.DateCreated>TIMESTAMP('$LastDownloadDateUpdated'))";
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readGardenPolygon($provID, $districtID, $LastDownloadDateUpdated) {
        $sql = "SELECT a.*"
                . " FROM ktv_farmer_garden_area a"
                . " WHERE"
                . " SUBSTR(a.FarmerID FROM 1 FOR 2)='$provID' AND SUBSTR(a.FarmerID FROM 1 FOR 4)='$districtID' AND (a.DateUpdated>TIMESTAMP('$LastDownloadDateUpdated') OR a.DateCreated>TIMESTAMP('$LastDownloadDateUpdated'))";
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readGardenPolygonDetail($provID, $districtID, $LastDownloadDateUpdated) {
        $sql = "SELECT a.*"
                . " FROM ktv_farmer_garden_area_detail a"
                . " WHERE"
                . " SUBSTR(a.FarmerID FROM 1 FOR 2)='$provID' AND SUBSTR(a.FarmerID FROM 1 FOR 4)='$districtID' AND (a.DateUpdated>TIMESTAMP('$LastDownloadDateUpdated') OR a.DateCreated>TIMESTAMP('$LastDownloadDateUpdated'))";
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readVillages($provID, $districtID, $LastDownloadDateUpdated) {
        $sql = "SELECT a.*"
                . " FROM ktv_village a"
                . " WHERE"
                . " SUBSTR(a.VillageID FROM 1 FOR 2)='$provID' AND SUBSTR(a.VillageID FROM 1 FOR 4)='$districtID' AND a.DateUpdated>TIMESTAMP('$LastDownloadDateUpdated')";
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readVillageCrops($provID, $districtID, $LastDownloadDateUpdated) {
        $sql = "SELECT a.*"
                . " FROM ktv_village_crop a"
                . " WHERE"
                . " SUBSTR(a.VillageID FROM 1 FOR 2)='$provID' AND SUBSTR(a.VillageID FROM 1 FOR 4)='$districtID'";
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readVillageInfrastructures($provID, $districtID, $LastDownloadDateUpdated) {
        $sql = "SELECT a.*"
                . " FROM ktv_village_infrastructure a"
                . " WHERE"
                . " SUBSTR(a.VillageID FROM 1 FOR 2)='$provID' AND SUBSTR(a.VillageID FROM 1 FOR 4)='$districtID'";
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readTrainingAttendance($provID, $districtID, $LastDownloadDateUpdated) {
        $sql = "SELECT a.* FROM `ktv_cpg_batch_trainings_attendance` a LEFT JOIN `ktv_cpg_batch_trainings` b ON b.`CpgBatchTrainingID` = a.`CpgBatchTrainingID` LEFT JOIN ktv_cpg c ON c.`CPGid` = b.`CPGid` WHERE SUBSTR(c.VillageID FROM 1 FOR 2) = '$provID' AND SUBSTR(c.VillageID FROM 1 FOR 4) = '$districtID' AND a.DateUpdated > TIMESTAMP('$LastDownloadDateUpdated')";
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function updateGarden($FarmerID, $GardenNr, $DateCollection, $Latitude, $Longitude, $Elevation, $OwnershipCocoa, $TahunTanamanCocoa, $GardenDistance, $GardenHaUnCertified, $Production, $PanenBiasaMonths, $PanenBiasaPanenMonth, $PanenBiasaKg, $PanenTrekMonths, $PanenTrekPanenMonth, $PanenTrekKg, $PanenRayaMonths, $PanenRayaPanenMonth, $PanenRayaKg, $TimeHarvestBiasa, $TimeHarvestTrek, $TimeHarvestRaya, $LandCertificate, $PohonTBM, $PohonTM, $PohonRehab, $GraftedTrees, $ReplantedTrees, $RoadCondition, $Comment, $TSH858, $RCC70, $RCC71, $RCC72, $RCC73, $Hybrid, $S1, $S2, $S3, $ICRRI3, $ICRRI4, $ICRRI5, $CloneLain, $Gamal, $Kelapa, $Durian, $Pinang, $Karet, $JackFruit, $Lamtoro, $Mahoni, $Pisang, $Rambutan, $Mangga, $Langsat, $ShadeLain, $ShadeTreesNr, $TimeHarvest, $HarvestAwal, $HarvestMasak, $HarvestHama, $PruningPlants, $FrequentPruning, $HighPruning, $PruningProtectPlants, $FrequentPruningProtect, $CleanSkin, $HowToCleanSkin, $OrganicKotoran, $OrganicResidu, $OrganicMembeli, $TidakMemakaiOrganic, $Urea, $TSP, $NPK, $KCL, $TidakMemakaiKimia, $FrequentFertilizationOrganic, $DoseFertilizerOrganic, $FrequentFertilizationKimia, $DoseFertilizerKimia, $PakaiKompos, $FrequentFertilizationKompos, $DoseFertilizerKompos, $FrUrea, $FrTsp, $FrNpk, $FrKcl, $DoUrea, $DoTsp, $DoNpk, $DoKcl, $KimiaDana, $KimiaSupplier, $KimiaDilatih, $HamaBPK, $HamaHelopeltis, $HamaBatang, $PenyakitKanker, $PenyakitBusuk, $PenyakitUpas, $PenyakitAkar, $PenyakitVSD, $PenyakitAntraknose, $Herbisida, $MerekHerbisida, $FrequentHerbisida, $DoseHerbisida, $Herbisida1, $Herbisida2, $Herbisida3, $Herbisida4, $Herbisida5, $Herbisida6, $Herbisida7, $Herbisida8, $Herbisida9, $Herbisida10, $Insectisida, $MerekInsectisida, $FrequentInsectisida, $DoseInsectisida, $Insectisida1, $Insectisida2, $Insectisida3, $Insectisida4, $Insectisida5, $Insectisida6, $Insectisida7, $Insectisida8, $Insectisida9, $Insectisida10, $Insectisida11, $Fungisida, $MerekFungisida, $FrequentFungisida, $DoseFungisida, $Fungisida1, $Fungisida2, $Fungisida3, $Fungisida4, $Fungisida5, $Fungisida6, $Fungisida7, $Fungisida8, $Fungisida9, $Fungisida10, $Fungisida11, $APD, $TempatSimpanPestisida, $BuangKemasanPestisida, $TopGraftedTrees, $GraftedTreesTahun, $TopGraftedTreesTahun, $ReplantedTreesTahun, $M01, $M06, $THR, $RCL, $J45, $kTBM, $kTM, $kTR, $pTBM, $pTM, $pTR, $SurveyNr, $DateCreated, $DateUpdated, $CreatedBy, $LastModifiedBy, $TSH858Nr, $RCC70Nr, $RCC71Nr, $RCC72Nr, $RCC73Nr, $LokalNr, $S1Nr, $S2Nr, $ICRRI3Nr, $ICRRI4Nr, $ICRRI5Nr, $M01Nr, $M06Nr, $THRNr, $RCLNr, $J45Nr, $CloneLainNr, $Sukun, $Jengkol, $Petai, $Jabon, $Uru, $Biti, $Jati, $Jeruk, $Manggis, $Pepaya, $Alpukat, $Kemiri, $Pala, $Aren, $Sawit, $Cengkeh, $GamalNr, $KelapaNr, $DurianNr, $PinangNr, $KaretNr, $JackFruitNr, $LamtoroNr, $MahoniNr, $PisangNr, $RambutanNr, $CengkehNr, $SawitNr, $ArenNr, $ManggaNr, $LangsatNr, $PalaNr, $KemiriNr, $AlpukatNr, $SukunNr, $PepayaNr, $ManggisNr, $JerukNr, $JatiNr, $BitiNr, $UruNr, $JabonNr, $PetaiNr, $JengkolNr, $ShadeLainNr, $FrLain, $DoLain, $KimiaTidakSuka, $KimiaLain) {

        $sql = "
            UPDATE ktv_farmer_garden
            SET GardenNr=?,DateCollection=?,Latitude=?,Longitude=?,Elevation=?,
            OwnershipCocoa=?,TahunTanamanCocoa=?,GardenDistance=?,GardenHaUnCertified=?,Production=?,PanenBiasaMonths=?,
            PanenBiasaPanenMonth=?,PanenBiasaKg=?,PanenTrekMonths=?,PanenTrekPanenMonth=?,PanenTrekKg=?,PanenRayaMonths=?,
            PanenRayaPanenMonth=?,PanenRayaKg=?,TimeHarvestBiasa=?,TimeHarvestTrek=?,TimeHarvestRaya=?,LandCertificate=?,
            PohonTBM=?,PohonTM=?,PohonRehab=?,GraftedTrees=?,ReplantedTrees=?,RoadCondition=?,Comment=?,TSH858=?,RCC70=?,
            RCC71=?,RCC72=?,RCC73=?,Hybrid=?,S1=?,S2=?,S3=?,ICRRI3=?,ICRRI4=?,ICRRI5=?,CloneLain=?,Gamal=?,Kelapa=?,Durian=?,
            Pinang=?,Karet=?,JackFruit=?,Lamtoro=?,Mahoni=?,Pisang=?,Rambutan=?,Mangga=?,Langsat=?,ShadeLain=?,ShadeTreesNr=?,
            TimeHarvest=?,HarvestAwal=?,HarvestMasak=?,HarvestHama=?,PruningPlants=?,FrequentPruning=?,HighPruning=?,
            PruningProtectPlants=?,FrequentPruningProtect=?,CleanSkin=?,HowToCleanSkin=?,OrganicKotoran=?,OrganicResidu=?,
            OrganicMembeli=?,TidakMemakaiOrganic=?,Urea=?,TSP=?,NPK=?,KCL=?,TidakMemakaiKimia=?,FrequentFertilizationOrganic=?,
            DoseFertilizerOrganic=?,FrequentFertilizationKimia=?,DoseFertilizerKimia=?,PakaiKompos=?,FrequentFertilizationKompos=?,
            DoseFertilizerKompos=?,FrUrea=?,FrTsp=?,FrNpk=?,FrKcl=?,DoUrea=?,DoTsp=?,DoNpk=?,DoKcl=?,KimiaDana=?,KimiaSupplier=?,
            KimiaDilatih=?,HamaBPK=?,HamaHelopeltis=?,HamaBatang=?,PenyakitKanker=?,PenyakitBusuk=?,PenyakitUpas=?,
            PenyakitAkar=?,PenyakitVSD=?,PenyakitAntraknose=?,Herbisida=?,MerekHerbisida=?,FrequentHerbisida=?,DoseHerbisida=?,
            Herbisida1=?,Herbisida2=?,Herbisida3=?,Herbisida4=?,Herbisida5=?,Herbisida6=?,Herbisida7=?,Herbisida8=?,Herbisida9=?,
            Herbisida10=?,Insectisida=?,MerekInsectisida=?,FrequentInsectisida=?,DoseInsectisida=?,Insectisida1=?,Insectisida2=?,
            Insectisida3=?,Insectisida4=?,Insectisida5=?,Insectisida6=?,Insectisida7=?,Insectisida8=?,Insectisida9=?,Insectisida10=?,Insectisida11=?,
            Fungisida=?,MerekFungisida=?,FrequentFungisida=?,DoseFungisida=?,Fungisida1=?,Fungisida2=?,Fungisida3=?,Fungisida4=?,
            Fungisida5=?,Fungisida6=?,Fungisida7=?,Fungisida8=?,Fungisida9=?,Fungisida10=?,Fungisida11=?,APD=?,TempatSimpanPestisida=?,
            BuangKemasanPestisida=?, TopGraftedTrees=?,GraftedTreesTahun=?,TopGraftedTreesTahun=?,ReplantedTreesTahun=?,
            M01=?,M06=?,THR=?,RCL=?,J45=?,KomposTBM=?,KomposTM=?,KomposTR=?,PupukTBM=?,PupukTM=?,PupukTR=?, DateCreated=?,DateUpdated=?,CreatedBy=?,LastModifiedBy=?, TSH858Nr=?,RCC70Nr=?,RCC71Nr=?,RCC72Nr=?,RCC73Nr=?,LokalNr=?,S1Nr=?,
            S2Nr=?,ICRRI3Nr=?,ICRRI4Nr=?,ICRRI5Nr=?,M01Nr=?,M06Nr=?,THRNr=?,RCLNr=?,J45Nr=?,
            CloneLainNr=?,Sukun=?,Jengkol=?,Petai=?,Jabon=?,Uru=?,Biti=?,
            Jati=?,Jeruk=?,Manggis=?,Pepaya=?,Alpukat=?,Kemiri=?,Pala=?,Aren=?,
            Sawit=?,Cengkeh=?,GamalNr=?,KelapaNr=?,DurianNr=?,PinangNr=?,KaretNr=?,JackFruitNr=?,
            LamtoroNr=?,MahoniNr=?,PisangNr=?,RambutanNr=?,CengkehNr=?,SawitNr=?,ArenNr=?,ManggaNr=?,
            LangsatNr=?,  PalaNr=?,KemiriNr=?,AlpukatNr=?,SukunNr=?,
            PepayaNr=?,ManggisNr=?,JerukNr=?,JatiNr=?,BitiNr=?,UruNr=?,JabonNr=?,PetaiNr=?,JengkolNr=?,ShadeLainNr=?,FrLain=?, DoLain=?,KimiaTidakSuka=?,KimiaLain=?, Comment =?
            WHERE FarmerID=? and GardenNr=? and SurveyNr=?";
        //$sql_date_survey = "UPDATE ktv_farmer SET DateSurvey=now() WHERE FarmerID=?";
        //$query = $this->db->query($sql_date_survey, array($FarmerID));
        $query = $this->db->query($sql, array($GardenNr, $DateCollection, $Latitude, $Longitude,
            $Elevation, $OwnershipCocoa, $TahunTanamanCocoa, $GardenDistance, $GardenHaUnCertified, $Production, $PanenBiasaMonths,
            $PanenBiasaPanenMonth, $PanenBiasaKg, $PanenTrekMonths, $PanenTrekPanenMonth, $PanenTrekKg, $PanenRayaMonths,
            $PanenRayaPanenMonth, $PanenRayaKg, $TimeHarvestBiasa, $TimeHarvestTrek, $TimeHarvestRaya, $LandCertificate, $PohonTBM,
            $PohonTM, $PohonRehab, $GraftedTrees, $ReplantedTrees, $RoadCondition, $Comment, $TSH858, $RCC70, $RCC71, $RCC72, $RCC73,
            $Hybrid, $S1, $S2, $S3, $ICRRI3, $ICRRI4, $ICRRI5, $CloneLain, $Gamal, $Kelapa, $Durian, $Pinang, $Karet, $JackFruit, $Lamtoro, $Mahoni,
            $Pisang, $Rambutan, $Mangga, $Langsat, $ShadeLain, $ShadeTreesNr, $TimeHarvest, $HarvestAwal, $HarvestMasak, $HarvestHama,
            $PruningPlants, $FrequentPruning, $HighPruning, $PruningProtectPlants, $FrequentPruningProtect, $CleanSkin, $HowToCleanSkin,
            $OrganicKotoran, $OrganicResidu, $OrganicMembeli, $TidakMemakaiOrganic, $Urea, $TSP, $NPK, $KCL, $TidakMemakaiKimia,
            $FrequentFertilizationOrganic, $DoseFertilizerOrganic, $FrequentFertilizationKimia, $DoseFertilizerKimia, $PakaiKompos,
            $FrequentFertilizationKompos, $DoseFertilizerKompos, $FrUrea, $FrTsp, $FrNpk, $FrKcl, $DoUrea, $DoTsp, $DoNpk, $DoKcl,
            $KimiaDana, $KimiaSupplier, $KimiaDilatih, $HamaBPK, $HamaHelopeltis, $HamaBatang, $PenyakitKanker, $PenyakitBusuk,
            $PenyakitUpas, $PenyakitAkar, $PenyakitVSD, $PenyakitAntraknose, $Herbisida, $MerekHerbisida, $FrequentHerbisida,
            $DoseHerbisida, $Herbisida1, $Herbisida2, $Herbisida3, $Herbisida4, $Herbisida5, $Herbisida6, $Herbisida7, $Herbisida8,
            $Herbisida9, $Herbisida10, $Insectisida, $MerekInsectisida, $FrequentInsectisida, $DoseInsectisida, $Insectisida1,
            $Insectisida2, $Insectisida3, $Insectisida4, $Insectisida5, $Insectisida6, $Insectisida7, $Insectisida8, $Insectisida9,
            $Insectisida10, $Insectisida11, $Fungisida, $MerekFungisida, $FrequentFungisida, $DoseFungisida, $Fungisida1, $Fungisida2, $Fungisida3,
            $Fungisida4, $Fungisida5, $Fungisida6, $Fungisida7, $Fungisida8, $Fungisida9, $Fungisida10, $Fungisida11, $APD, $TempatSimpanPestisida,
            $BuangKemasanPestisida,
            $TopGraftedTrees, $GraftedTreesTahun, $TopGraftedTreesTahun, $ReplantedTreesTahun,
            $M01, $M06, $THR, $RCL, $J45, $kTBM, $kTM, $kTR, $pTBM, $pTM, $pTR, $DateCreated, $DateUpdated, $CreatedBy, $LastModifiedBy, $TSH858Nr, $RCC70Nr, $RCC71Nr, $RCC72Nr, $RCC73Nr, $LokalNr, $S1Nr, $S2Nr, $ICRRI3Nr, $ICRRI4Nr, $ICRRI5Nr, $M01Nr, $M06Nr, $THRNr, $RCLNr, $J45Nr, $CloneLainNr, $Sukun, $Jengkol, $Petai, $Jabon, $Uru, $Biti, $Jati, $Jeruk, $Manggis, $Pepaya, $Alpukat, $Kemiri, $Pala, $Aren, $Sawit, $Cengkeh, $GamalNr, $KelapaNr, $DurianNr, $PinangNr, $KaretNr, $JackFruitNr, $LamtoroNr, $MahoniNr, $PisangNr, $RambutanNr, $CengkehNr, $SawitNr, $ArenNr, $ManggaNr, $LangsatNr, $PalaNr, $KemiriNr, $AlpukatNr, $SukunNr, $PepayaNr, $ManggisNr, $JerukNr, $JatiNr, $BitiNr, $UruNr, $JabonNr, $PetaiNr, $JengkolNr, $ShadeLainNr, $FrLain, $DoLain, $KimiaTidakSuka, $KimiaLain, $Comment, $FarmerID, $GardenNr, $SurveyNr));

        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function createGarden($FarmerID, $GardenNr, $DateCollection, $Latitude, $Longitude, $Elevation, $OwnershipCocoa, $TahunTanamanCocoa, $GardenDistance, $GardenHaUnCertified, $Production, $PanenBiasaMonths, $PanenBiasaPanenMonth, $PanenBiasaKg, $PanenTrekMonths, $PanenTrekPanenMonth, $PanenTrekKg, $PanenRayaMonths, $PanenRayaPanenMonth, $PanenRayaKg, $TimeHarvestBiasa, $TimeHarvestTrek, $TimeHarvestRaya, $LandCertificate, $PohonTBM, $PohonTM, $PohonRehab, $GraftedTrees, $ReplantedTrees, $RoadCondition, $Comment, $TSH858, $RCC70, $RCC71, $RCC72, $RCC73, $Hybrid, $S1, $S2, $S3, $ICRRI3, $ICRRI4, $ICRRI5, $CloneLain, $Gamal, $Kelapa, $Durian, $Pinang, $Karet, $JackFruit, $Lamtoro, $Mahoni, $Pisang, $Rambutan, $Mangga, $Langsat, $ShadeLain, $ShadeTreesNr, $TimeHarvest, $HarvestAwal, $HarvestMasak, $HarvestHama, $PruningPlants, $FrequentPruning, $HighPruning, $PruningProtectPlants, $FrequentPruningProtect, $CleanSkin, $HowToCleanSkin, $OrganicKotoran, $OrganicResidu, $OrganicMembeli, $TidakMemakaiOrganic, $Urea, $TSP, $NPK, $KCL, $TidakMemakaiKimia, $FrequentFertilizationOrganic, $DoseFertilizerOrganic, $FrequentFertilizationKimia, $DoseFertilizerKimia, $PakaiKompos, $FrequentFertilizationKompos, $DoseFertilizerKompos, $FrUrea, $FrTsp, $FrNpk, $FrKcl, $DoUrea, $DoTsp, $DoNpk, $DoKcl, $KimiaDana, $KimiaSupplier, $KimiaDilatih, $HamaBPK, $HamaHelopeltis, $HamaBatang, $PenyakitKanker, $PenyakitBusuk, $PenyakitUpas, $PenyakitAkar, $PenyakitVSD, $PenyakitAntraknose, $Herbisida, $MerekHerbisida, $FrequentHerbisida, $DoseHerbisida, $Herbisida1, $Herbisida2, $Herbisida3, $Herbisida4, $Herbisida5, $Herbisida6, $Herbisida7, $Herbisida8, $Herbisida9, $Herbisida10, $Insectisida, $MerekInsectisida, $FrequentInsectisida, $DoseInsectisida, $Insectisida1, $Insectisida2, $Insectisida3, $Insectisida4, $Insectisida5, $Insectisida6, $Insectisida7, $Insectisida8, $Insectisida9, $Insectisida10, $Insectisida11, $Fungisida, $MerekFungisida, $FrequentFungisida, $DoseFungisida, $Fungisida1, $Fungisida2, $Fungisida3, $Fungisida4, $Fungisida5, $Fungisida6, $Fungisida7, $Fungisida8, $Fungisida9, $Fungisida10, $Fungisida11, $APD, $TempatSimpanPestisida, $BuangKemasanPestisida, $TopGraftedTrees, $GraftedTreesTahun, $TopGraftedTreesTahun, $ReplantedTreesTahun, $M01, $M06, $THR, $RCL, $J45, $KomposTBM, $KomposTM, $KomposTR, $PupukTBM, $PupukTM, $PupukTR, $SurveyNr, $DateCreated, $DateUpdated, $CreatedBy, $LastModifiedBy, $TSH858Nr, $RCC70Nr, $RCC71Nr, $RCC72Nr, $RCC73Nr, $LokalNr, $S1Nr, $S2Nr, $ICRRI3Nr, $ICRRI4Nr, $ICRRI5Nr, $M01Nr, $M06Nr, $THRNr, $RCLNr, $J45Nr, $CloneLainNr, $Sukun, $Jengkol, $Petai, $Jabon, $Uru, $Biti, $Jati, $Jeruk, $Manggis, $Pepaya, $Alpukat, $Kemiri, $Pala, $Aren, $Sawit, $Cengkeh, $GamalNr, $KelapaNr, $DurianNr, $PinangNr, $KaretNr, $JackFruitNr, $LamtoroNr, $MahoniNr, $PisangNr, $RambutanNr, $CengkehNr, $SawitNr, $ArenNr, $ManggaNr, $LangsatNr, $PalaNr, $KemiriNr, $AlpukatNr, $SukunNr, $PepayaNr, $ManggisNr, $JerukNr, $JatiNr, $BitiNr, $UruNr, $JabonNr, $PetaiNr, $JengkolNr, $ShadeLainNr, $FrLain, $DoLain, $KimiaTidakSuka, $KimiaLain) {
        /* $sql = "
          REPLACE INTO ktv_farmer_garden(FarmerID,GardenNr,SurveyNr,DateCollection,Latitude,Longitude,Elevation,OwnershipCocoa,TahunTanamanCocoa,GardenDistance,GardenHaUnCertified,LandCertificate,Production,PanenBiasaMonths,PanenBiasaPanenMonth,PanenBiasaKg,PanenTrekMonths,PanenTrekPanenMonth,PanenTrekKg,PanenRayaMonths,PanenRayaPanenMonth,PanenRayaKg,PohonTBM,PohonTM,PohonRehab,GraftedTrees,GraftedTreesTahun,TopGraftedTrees,TopGraftedTreesTahun,ReplantedTrees,ReplantedTreesTahun,RoadCondition,TSH858,RCC70,RCC71,RCC72,RCC73, Hybrid,S1,S2,ICRRI3,ICRRI4,ICRRI5,S3,M01,M06,THR,RCL,J45,CloneLain,Gamal,Kelapa,Durian,Pinang,Karet,JackFruit,Lamtoro,Mahoni,Pisang,Rambutan,Mangga,Langsat,ShadeLain,ShadeTreesNr, TimeHarvest,HarvestAwal,HarvestMasak,HarvestHama,PruningPlants,FrequentPruning, HighPruning,PruningProtectPlants,FrequentPruningProtect,CleanSkin,HowToCleanSkin,OrganicKotoran,OrganicResidu,OrganicMembeli,TidakMemakaiOrganic,Urea,TSP,NPK, KCL,TidakMemakaiKimia,FrequentFertilizationOrganic,DoseFertilizerOrganic,FrequentFertilizationKimia,DoseFertilizerKimia,PakaiKompos,FrequentFertilizationKompos,DoseFertilizerKompos,Herbisida,Fungisida,Insectisida,FrequentHerbisida, FrequentInsectisida,FrequentFungisida,DoseHerbisida,DoseInsectisida,DoseFungisida,MerekHerbisida,MerekInsectisida,MerekFungisida,TempatSimpanPestisida,BuangKemasanPestisida,FrUrea,FrTsp,FrNpk,FrKcl,DoUrea,DoTsp,DoNpk,DoKcl,KimiaDana,KimiaSupplier,KimiaDilatih,HamaBPK,HamaHelopeltis,HamaBatang,PenyakitKanker,PenyakitBusuk,PenyakitUpas,PenyakitAkar,PenyakitVSD,PenyakitAntraknose,Herbisida1,Herbisida2,Herbisida3,Herbisida4,Herbisida5,Herbisida6,Herbisida7,Herbisida8,Herbisida9,Herbisida10,Insectisida1,Insectisida2,Insectisida3,Insectisida4,Insectisida5,Insectisida6,Insectisida7,Insectisida8,Insectisida9,Insectisida10,Fungisida1,Fungisida2,Fungisida3,Fungisida4,Fungisida5,Fungisida6,Fungisida7,Fungisida8,Fungisida9,Fungisida10,APD, komposTBM,komposTM,komposTR,pupukTBM,pupukTM,pupukTR,DateCreated, DateUpdated,CreatedBy,LastModifiedBy) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?, ?,?,?)";
         */
        $sql = "
            REPLACE INTO ktv_farmer_garden(FarmerID,GardenNr,SurveyNr,DateCollection,Latitude,Longitude,Elevation,OwnershipCocoa,TahunTanamanCocoa,GardenDistance,GardenHaUnCertified,LandCertificate,Production,PanenBiasaMonths,PanenBiasaPanenMonth,PanenBiasaKg,PanenTrekMonths,PanenTrekPanenMonth,PanenTrekKg,PanenRayaMonths,PanenRayaPanenMonth,PanenRayaKg,PohonTBM,PohonTM,PohonRehab,GraftedTrees,GraftedTreesTahun,TopGraftedTrees,TopGraftedTreesTahun,ReplantedTrees,ReplantedTreesTahun,RoadCondition,TSH858,RCC70,RCC71,RCC72,RCC73, Hybrid,S1,S2,ICRRI3,ICRRI4,ICRRI5,S3,M01,M06,THR,RCL,J45,CloneLain,Gamal,Kelapa,Durian,Pinang,Karet,JackFruit,Lamtoro,Mahoni,Pisang,Rambutan,Mangga,Langsat,ShadeLain,ShadeTreesNr,HarvestAwal,HarvestMasak,HarvestHama,PruningPlants,FrequentPruning, HighPruning,PruningProtectPlants,FrequentPruningProtect,CleanSkin,HowToCleanSkin,PakaiKompos,FrequentFertilizationKompos,DoseFertilizerKompos,Herbisida,Fungisida,Insectisida,FrequentHerbisida, FrequentInsectisida,FrequentFungisida,DoseHerbisida,DoseInsectisida,DoseFungisida,MerekHerbisida,MerekInsectisida,MerekFungisida,TempatSimpanPestisida,BuangKemasanPestisida,FrUrea,FrTsp,FrNpk,FrKcl,DoUrea,DoTsp,DoNpk,DoKcl,KimiaDana,KimiaSupplier,KimiaDilatih,HamaBPK,HamaHelopeltis,HamaBatang,PenyakitKanker,PenyakitBusuk,PenyakitUpas,PenyakitAkar,PenyakitVSD,PenyakitAntraknose,Herbisida1,Herbisida2,Herbisida3,Herbisida4,Herbisida5,Herbisida6,Herbisida7,Herbisida8,Herbisida9,Herbisida10,Insectisida1,Insectisida2,Insectisida3,Insectisida4,Insectisida5,Insectisida6,Insectisida7,Insectisida8,Insectisida9,Insectisida10,Insectisida11,Fungisida1,Fungisida2,Fungisida3,Fungisida4,Fungisida5,Fungisida6,Fungisida7,Fungisida8,Fungisida9,Fungisida10,Fungisida11,APD, komposTBM,komposTM,komposTR,pupukTBM,pupukTM,pupukTR,DateCreated, DateUpdated,CreatedBy,LastModifiedBy,TSH858Nr, RCC70Nr ,RCC71Nr, RCC72Nr, RCC73Nr, LokalNr, S1Nr, S2Nr, ICRRI3Nr, ICRRI4Nr, ICRRI5Nr, M01Nr,  M06Nr, THRNr, RCLNr, J45Nr, CloneLainNr,Sukun, Jengkol, Petai,   Jabon, Uru, Biti, Jati, Jeruk, Manggis, Pepaya, Alpukat, Kemiri, Pala, Aren, Sawit, Cengkeh, GamalNr, KelapaNr,  DurianNr, PinangNr,   KaretNr,   JackFruitNr,   LamtoroNr,   MahoniNr,   PisangNr,   RambutanNr,   CengkehNr,   SawitNr,   ArenNr,   ManggaNr,   LangsatNr,   PalaNr, KemiriNr,  AlpukatNr, SukunNr, PepayaNr,   ManggisNr,   JerukNr,   JatiNr,   BitiNr,   UruNr,   JabonNr,   PetaiNr, JengkolNr, ShadeLainNr,  FrLain,DoLain,TidakMemakaiKimia,KimiaTidakSuka,KimiaLain,Comment ) VALUES($FarmerID,$GardenNr,$SurveyNr,'$DateCollection',$Latitude,$Longitude,$Elevation,$OwnershipCocoa,$TahunTanamanCocoa,$GardenDistance,$GardenHaUnCertified,$LandCertificate,$Production,$PanenBiasaMonths,$PanenBiasaPanenMonth,$PanenBiasaKg,$PanenTrekMonths,$PanenTrekPanenMonth,$PanenTrekKg,$PanenRayaMonths,$PanenRayaPanenMonth,$PanenRayaKg,$PohonTBM,$PohonTM,$PohonRehab,$GraftedTrees,$GraftedTreesTahun,$TopGraftedTrees,$TopGraftedTreesTahun,$ReplantedTrees,$ReplantedTreesTahun,$RoadCondition,$TSH858,$RCC70,$RCC71,$RCC72,$RCC73, $Hybrid,$S1,$S2,$ICRRI3,$ICRRI4,$ICRRI5,$S3,$M01,$M06,$THR,$RCL,$J45,'$CloneLain',$Gamal,$Kelapa,$Durian,$Pinang,$Karet,$JackFruit,$Lamtoro,$Mahoni,$Pisang,$Rambutan,$Mangga,$Langsat,'$ShadeLain',$ShadeTreesNr,$HarvestAwal,$HarvestMasak,$HarvestHama,$PruningPlants,$FrequentPruning, $HighPruning,$PruningProtectPlants,$FrequentPruningProtect,$CleanSkin,$HowToCleanSkin,$PakaiKompos,$FrequentFertilizationKompos,$DoseFertilizerKompos,$Herbisida,$Fungisida,$Insectisida,$FrequentHerbisida, $FrequentInsectisida,$FrequentFungisida,$DoseHerbisida,$DoseInsectisida,$DoseFungisida,'$MerekHerbisida','$MerekInsectisida','$MerekFungisida',$TempatSimpanPestisida,$BuangKemasanPestisida,$FrUrea,$FrTsp,$FrNpk,$FrKcl,$DoUrea,$DoTsp,$DoNpk,$DoKcl,$KimiaDana,$KimiaSupplier,$KimiaDilatih,$HamaBPK,$HamaHelopeltis,$HamaBatang,$PenyakitKanker,$PenyakitBusuk,$PenyakitUpas,$PenyakitAkar,$PenyakitVSD,$PenyakitAntraknose,$Herbisida1,$Herbisida2,$Herbisida3,$Herbisida4,$Herbisida5,$Herbisida6,$Herbisida7,$Herbisida8,$Herbisida9,$Herbisida10,$Insectisida1,$Insectisida2,$Insectisida3,$Insectisida4,$Insectisida5,$Insectisida6,$Insectisida7,$Insectisida8,$Insectisida9,$Insectisida10,$Insectisida11,$Fungisida1,$Fungisida2,$Fungisida3,$Fungisida4,$Fungisida5,$Fungisida6,$Fungisida7,$Fungisida8,$Fungisida9,$Fungisida10,$Fungisida11,$APD,$KomposTBM,$KomposTM,$KomposTR,$PupukTBM,$PupukTM,$PupukTR,'$DateCreated', '$DateUpdated',$LastModifiedBy,$LastModifiedBy,$TSH858Nr, $RCC70Nr ,$RCC71Nr, $RCC72Nr, $RCC73Nr, $LokalNr, $S1Nr, $S2Nr, $ICRRI3Nr, $ICRRI4Nr, $ICRRI5Nr, $M01Nr,  $M06Nr, $THRNr, $RCLNr, $J45Nr, $CloneLainNr,$Sukun, $Jengkol, $Petai,   $Jabon, $Uru, $Biti, $Jati, $Jeruk, $Manggis, $Pepaya, $Alpukat, $Kemiri, $Pala, $Aren, $Sawit, $Cengkeh, $GamalNr, $KelapaNr,  $DurianNr, $PinangNr,   $KaretNr,   $JackFruitNr,   $LamtoroNr,   $MahoniNr, $PisangNr,   $RambutanNr,   $CengkehNr,   $SawitNr,   $ArenNr,   $ManggaNr,   $LangsatNr,   $PalaNr, $KemiriNr,  $AlpukatNr, $SukunNr, $PepayaNr,   $ManggisNr,   $JerukNr,   $JatiNr,   $BitiNr,   $UruNr,   $JabonNr,   $PetaiNr, $JengkolNr, $ShadeLainNr,  $FrLain,$DoLain,$TidakMemakaiKimia,$KimiaTidakSuka,$KimiaLain,'$Comment')";

        //$sql_date_survey = "UPDATE ktv_farmer SET DateSurvey=now() WHERE FarmerID=?";
        //$query = $this->db->query($sql_date_survey, array($FarmerID));
        $query = $this->db->query($sql);
        // $query = $this->db->query($sql, array($FarmerID,$GardenNr,$SurveyNr,$DateCollection,$Latitude,$Longitude,$Elevation,$OwnershipCocoa,$TahunTanamanCocoa,$GardenDistance,$GardenHaUnCertified,$LandCertificate,$Production,$PanenBiasaMonths,$PanenBiasaPanenMonth,$PanenBiasaKg,$PanenTrekMonths,$PanenTrekPanenMonth,$PanenTrekKg,$PanenRayaMonths,$PanenRayaPanenMonth,$PanenRayaKg,$PohonTBM,$PohonTM,$PohonRehab,$GraftedTrees,$GraftedTreesTahun,$TopGraftedTrees,$TopGraftedTreesTahun,$ReplantedTrees,$ReplantedTreesTahun,$RoadCondition,$TSH858,$RCC70,$RCC71,$RCC72,$RCC73, $Hybrid,$S1,$S2,$ICRRI3,$ICRRI4,$ICRRI5,$S3,$M01,$M06,$THR,$RCL,$J45,$CloneLain,$Gamal,$Kelapa,$Durian,$Pinang,$Karet,$JackFruit,$Lamtoro,$Mahoni,$Pisang,$Rambutan,$Mangga,$Langsat,$ShadeLain,$ShadeTreesNr, $TimeHarvest,$HarvestAwal,$HarvestMasak,$HarvestHama,$PruningPlants,$FrequentPruning, $HighPruning,$PruningProtectPlants,$FrequentPruningProtect,$CleanSkin,$$HowToCleanSkin,$OrganicKotoran,$OrganicResidu,$OrganicMembeli,$TidakMemakaiOrganic,$Urea,$TSP,$NPK, $KCL,$TidakMemakaiKimia,$FrequentFertilizationOrganic,$DoseFertilizerOrganic,$FrequentFertilizationKimia,$DoseFertilizerKimia,$PakaiKompos,$FrequentFertilizationKompos,$DoseFertilizerKompos,$Herbisida,$Fungisida,$Insectisida,$FrequentHerbisida, $FrequentInsectisida,$FrequentFungisida,$DoseHerbisida,$DoseInsectisida,$DoseFungisida,$MerekHerbisida,$MerekInsectisida,$MerekFungisida,$TempatSimpanPestisida,$BuangKemasanPestisida,$FrUrea,$FrTsp,$FrNpk,$FrKcl,$DoUrea,$DoTsp,$DoNpk,$DoKcl,$KimiaDana,$KimiaSupplier,$KimiaDilatih,$HamaBPK,$HamaHelopeltis,$HamaBatang,$PenyakitKanker,$PenyakitBusuk,$PenyakitUpas,$PenyakitAkar,$PenyakitVSD,$PenyakitAntraknose,$Herbisida1,$Herbisida2,$Herbisida3,$Herbisida4,$Herbisida5,$Herbisida6,$Herbisida7,$Herbisida8,$Herbisida9,$Herbisida10,$Insectisida1,$Insectisida2,$Insectisida3,$Insectisida4,$Insectisida5,$Insectisida6,$Insectisida7,$Insectisida8,$Insectisida9,$Insectisida10,$Fungisida1,$Fungisida2,$Fungisida3,$Fungisida4,$Fungisida5,$$Fungisida6,$Fungisida7,$Fungisida8,$Fungisida9,$Fungisida10,$APD,$komposTBM,$komposTM,$komposTR,$pupukTBM,$pupukTM,$pupukTR,$DateCreated, $DateUpdated,$CreatedBy,$LastModifiedBy));

        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function readFarmerFinancial($provID, $districtID, $LastDownloadDateUpdated) {
        $sql = "SELECT a.*"
                . " FROM ktv_farmer_financial a, ktv_farmer b"
                . " WHERE"
                . " a.FarmerID = b.FarmerID AND SUBSTR(b.VillageID FROM 1 FOR 2)='$provID' AND SUBSTR(b.VillageID FROM 1 FOR 4)='$districtID' AND (a.DateCreated>TIMESTAMP('$LastDownloadDateUpdated') OR a.DateUpdated>TIMESTAMP('$LastDownloadDateUpdated'))";
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readCertification($provID, $districtID, $LastDownloadDateUpdated) {
        $sql = "SELECT a.*"
                . " FROM ktv_certification a, ktv_farmer b"
                . " WHERE"
                . " a.FarmerID = b.FarmerID AND SUBSTR(b.VillageID FROM 1 FOR 2)='$provID' AND SUBSTR(b.VillageID FROM 1 FOR 4)='$districtID' AND a.DateCreated>TIMESTAMP('$LastDownloadDateUpdated')";
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readCertificationAuditLog($provID, $districtID, $LastDownloadDateUpdated) {
        $sql = "SELECT a.*"
                . " FROM ktv_certification_audit_log a, ktv_farmer b"
                . " WHERE"
                . " a.FarmerID = b.FarmerID AND SUBSTR(b.VillageID FROM 1 FOR 2)='$provID' AND SUBSTR(b.VillageID FROM 1 FOR 4)='$districtID' AND a.DateCreated>TIMESTAMP('$LastDownloadDateUpdated')";
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readPostHarvests($provID, $districtID) {
        $sql = "SELECT a.FarmerID,
                a.SurveyNr,
                a.DateCollection,
                a.AdaProduksi,
                a.AnggotaKerjaKebun,
                a.BuruhSeasonal,
                a.BuruhSeasonalRupiah,
                a.BuruhSeasonalPersen,
                a.BuruhFulltime,
                a.BuruhFulltimeRupiah,
                a.BuruhFulltimePersen,
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
                a.Distance,
                a.AntarSendiri,
                a.Comment,
                a.DateCreated,
                a.DateUpdated,
                a.CreatedBy,
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

    function readPostHarvest($provID, $districtID, $LastDownloadDateUpdated) {
        $sql = "SELECT a.*
                FROM
                ktv_farmer_post_harvest a,
                ktv_farmer b
                WHERE
                a.FarmerID = b.FarmerID AND
                SUBSTR(b.VillageID FROM 1 FOR 2)='$provID' AND SUBSTR(b.VillageID FROM 1 FOR 4)='$districtID' AND a.DateCreated>TIMESTAMP('$LastDownloadDateUpdated')";

        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function createHarvest($SurveyNr, $DateCollection, $AdaProduksi, $AnggotaKerjaKebun, $BuruhSeasonal, $BuruhSeasonalRupiah, $BuruhSeasonalPersen, $BuruhFulltime, $BuruhFulltimeRupiah, $BuruhFulltimePersen, $Fermentation, $FermentationDays, $SunDryingSemen, $DryingAlat, $DryingDays, $CocoaBuyers, $NoFermentation, $Sortasi, $NoSortasi, $SunDryingAspal, $JemurYesNo, $TidakJemur, $SunDryingAlas, $Distance, $AntarSendiri, $Comment, $DateCreated, $DateUpdated, $CreatedBy, $LastModifiedBy, $FarmerID) {
        $sql = "
            REPLACE INTO ktv_farmer_post_harvest (FarmerID,SurveyNr,DateCollection,AdaProduksi,AnggotaKerjaKebun,BuruhSeasonal,BuruhSeasonalRupiah,BuruhSeasonalPersen,BuruhFulltime,BuruhFulltimeRupiah,BuruhFulltimePersen,Fermentation,FermentationDays,SunDryingSemen,DryingAlat,DryingDays,CocoaBuyers,NoFermentation,Sortasi,NoSortasi,SunDryingAspal,JemurYesNo,TidakJemur,SunDryingAlas,Distance,AntarSendiri,Comment,CreatedBy,LastModifiedBy,DateCreated,DateUpdated) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        // $sql_date_survey = "UPDATE ktv_farmer SET DateSurvey=now() WHERE FarmerID=?";
        // $query = $this->db->query($sql_date_survey, array($FarmerID));
        $query = $this->db->query($sql, array($FarmerID, $SurveyNr, $DateCollection, $AdaProduksi, $AnggotaKerjaKebun, $BuruhSeasonal, $BuruhSeasonalRupiah, $BuruhSeasonalPersen, $BuruhFulltime, $BuruhFulltimeRupiah, $BuruhFulltimePersen, $Fermentation, $FermentationDays, $SunDryingSemen, $DryingAlat, $DryingDays, $CocoaBuyers, $NoFermentation, $Sortasi, $NoSortasi, $SunDryingAspal, $JemurYesNo, $TidakJemur, $SunDryingAlas, $Distance, $AntarSendiri, $Comment, $CreatedBy, $LastModifiedBy, $DateCreated, $DateUpdated));


        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function updateHarvest($surveyNr, $DateCollection, $AdaProduksi, $AnggotaKerjaKebun, $BuruhSeasonal, $BuruhSeasonalRupiah, $BuruhSeasonalPersen, $BuruhFulltime, $BuruhFulltimeRupiah, $BuruhFulltimePersen, $Fermentation, $FermentationDays, $SunDryingSemen, $DryingAlat, $DryingDays, $CocoaBuyers, $NoFermentation, $Sortasi, $NoSortasi, $SunDryingAspal, $JemurYesNo, $TidakJemur, $SunDryingAlas, $Distance, $AntarSendiri, $Comment, $DateCreated, $DateUpdated, $CreatedBy, $LastModifiedBy, $FarmerID) {
        $sql = "
            UPDATE ktv_farmer_post_harvest
            SET DateCollection=?,AdaProduksi=?,AnggotaKerjaKebun=?,AnggotaKerjaKebun=?,BuruhSeasonal=?,BuruhSeasonalRupiah=?,BuruhSeasonalPersen=?,BuruhFulltime=?,BuruhFulltimeRupiah=?,BuruhFulltimePersen=?,Fermentation=?,FermentationDays=?,
               SunDryingSemen=?,DryingAlat=?,DryingDays=?,CocoaBuyers=?,NoFermentation=?,Sortasi=?,NoSortasi=?,
               SunDryingAspal=?,JemurYesNo=?,TidakJemur=?,SunDryingAlas=?,Distance=?,AntarSendiri=?,Comment=?,DateUpdated=?,DateCreated=?,CreatedBy=?,LastModifiedBy=?
            WHERE FarmerID=? and SurveyNr=?";
        // $sql_date_survey = "UPDATE ktv_farmer SET DateSurvey=now() WHERE FarmerID=?";
        // $query = $this->db->query($sql_date_survey, array($FarmerID));
        $query = $this->db->query($sql, array($DateCollection, $AdaProduksi, $AnggotaKerjaKebun, $BuruhSeasonal, $BuruhSeasonalRupiah, $BuruhSeasonalPersen, $BuruhFulltime, $BuruhFulltimeRupiah, $BuruhFulltimePersen, $Fermentation, $FermentationDays, $SunDryingSemen, $DryingAlat, $DryingDays, $CocoaBuyers, $NoFermentation, $Sortasi, $NoSortasi, $SunDryingAspal, $JemurYesNo, $TidakJemur, $SunDryingAlas, $Distance, $AntarSendiri, $Comment, $DateUpdated, $DateCreated, $CreatedBy, $LastModifiedBy, $FarmerID, $surveyNr));
        // log_message('debug', $FarmerID .'-'.$surveyNr);
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function readNutritions($provID, $districtID) {
        $sql = "SELECT
                a.*
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

    function readNutrition($provID, $districtID, $LastDownloadDateUpdated) {
        $sql = "SELECT
                a.*
                FROM
                ktv_nutrition AS a ,
                ktv_farmer AS b
                WHERE
                a.FarmerID = b.FarmerID AND
                SUBSTR(b.VillageID FROM 1 FOR 2) = '$provID' AND SUBSTR(b.VillageID FROM 1 FOR 4) = '$districtID' AND a.DateCreated>TIMESTAMP('$LastDownloadDateUpdated')";

        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function createNutrition($SurveyNr, $InterviewDate, $KebunPanjang, $KebunLebar, $KbBayam, $KbCabai, $KbKacangPanjang, $KbKangkung, $KbSawi, $KbTerong, $KbTomat, $KbKambing, $KbSapi, $KbBebek, $KbAyam, $KbIkan, $aSagu, $aNasi, $aMie, $aJagung, $aRoti, $bUbiJalarKuning, $bSingkongKuning, $bWortel, $bLabu, $cUbiJalarPutih, $cSingkongPutih, $cTalas, $cKentang, $dBayam, $dDaunMelinjo, $dDaunPepaya, $dDaunSingkong, $dKangkung, $dSawi, $eKacangPanjang, $eTomat, $eTerong, $fJambuMerah, $fMangga, $fPepaya, $gJambuAir, $gKelapa, $gPisang, $gRambutan, $gSemangka, $gSalak, $hJeroan, $hHati, $iAyam, $iBebek, $iKambing, $iKerbau, $iSapi, $iLainnya, $jAyam, $jBebek, $jEntok, $jPuyuh, $kCumiCumi, $kIkan, $kIkanTeri, $kKepiting, $kKerang, $kUdang, $lAirTahuSusuKedelai, $lSausKacang, $lTahu, $lTempe, $mKeju, $mSusu, $nMinyakGoreng, $nMentega, $nSantan, $Score, $FarmerID, $DateCreated, $DateUpdated, $CreatedBy, $LastModifiedBy) {
        $sql = "
           REPLACE INTO ktv_nutrition (FarmerID,SurveyNr,InterviewDate,KebunPanjang,KebunLebar,KbBayam,KbCabai,KbKacangPanjang,KbKangkung,KbSawi,KbTerong,KbTomat,KbKambing,KbSapi,KbBebek,KbAyam,KbIkan,aSagu,aNasi,aMie,aJagung,aRoti,bUbiJalarKuning,bSingkongKuning,bWortel,bLabu,cUbiJalarPutih,cSingkongPutih,cTalas,cKentang,dBayam,dDaunMelinjo,dDaunPepaya,dDaunSingkong,dKangkung,dSawi,eKacangPanjang,eTomat,eTerong,fJambuMerah,fMangga,fPepaya,gJambuAir,gKelapa,gPisang,gRambutan,gSemangka,gSalak,hJeroan,hHati,iAyam,iBebek,iKambing,iKerbau,iSapi,iLainnya,jAyam,jBebek,jEntok,jPuyuh,kCumiCumi,kIkan,kIkanTeri,kKepiting,kKerang,kUdang,lAirTahuSusuKedelai,lSausKacang,lTahu,lTempe,mKeju,mSusu,nMinyakGoreng,nMentega,nSantan,Score,CreatedBy,LastModifiedBy,DateCreated,DateUpdated) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        //$sql_date_survey = "UPDATE ktv_farmer SET DateSurvey=now() WHERE FarmerID=?";
        //$query = $this->db->query($sql_date_survey, array($farmerid));
        $query = $this->db->query($sql, array($FarmerID, $SurveyNr, $InterviewDate, $KebunPanjang, $KebunLebar, $KbBayam, $KbCabai, $KbKacangPanjang, $KbKangkung, $KbSawi, $KbTerong, $KbTomat, $KbKambing, $KbSapi, $KbBebek, $KbAyam, $KbIkan, $aSagu, $aNasi, $aMie, $aJagung, $aRoti, $bUbiJalarKuning, $bSingkongKuning, $bWortel, $bLabu, $cUbiJalarPutih, $cSingkongPutih, $cTalas, $cKentang, $dBayam, $dDaunMelinjo, $dDaunPepaya, $dDaunSingkong, $dKangkung, $dSawi, $eKacangPanjang, $eTomat, $eTerong, $fJambuMerah, $fMangga, $fPepaya, $gJambuAir, $gKelapa, $gPisang, $gRambutan, $gSemangka, $gSalak, $hJeroan, $hHati, $iAyam, $iBebek, $iKambing, $iKerbau, $iSapi, $iLainnya, $jAyam, $jBebek, $jEntok, $jPuyuh, $kCumiCumi, $kIkan, $kIkanTeri, $kKepiting, $kKerang, $kUdang, $lAirTahuSusuKedelai, $lSausKacang, $lTahu, $lTempe, $mKeju, $mSusu, $nMinyakGoreng, $nMentega, $nSantan, $Score, $CreatedBy, $LastModifiedBy, $DateCreated, $DateUpdated));
        //log_message('debug', $FarmerID .'-'.$surveyNr);

        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function updateNutrition($SurveyNr, $InterviewDate, $KebunPanjang, $KebunLebar, $KbBayam, $KbCabai, $KbKacangPanjang, $KbKangkung, $KbSawi, $KbTerong, $KbTomat, $KbKambing, $KbSapi, $KbBebek, $KbAyam, $KbIkan, $aSagu, $aNasi, $aMie, $aJagung, $aRoti, $bUbiJalarKuning, $bSingkongKuning, $bWortel, $bLabu, $cUbiJalarPutih, $cSingkongPutih, $cTalas, $cKentang, $dBayam, $dDaunMelinjo, $dDaunPepaya, $dDaunSingkong, $dKangkung, $dSawi, $eKacangPanjang, $eTomat, $eTerong, $fJambuMerah, $fMangga, $fPepaya, $gJambuAir, $gKelapa, $gPisang, $gRambutan, $gSemangka, $gSalak, $hJeroan, $hHati, $iAyam, $iBebek, $iKambing, $iKerbau, $iSapi, $iLainnya, $jAyam, $jBebek, $jEntok, $jPuyuh, $kCumiCumi, $kIkan, $kIkanTeri, $kKepiting, $kKerang, $kUdang, $lAirTahuSusuKedelai, $lSausKacang, $lTahu, $lTempe, $mKeju, $mSusu, $nMinyakGoreng, $nMentega, $nSantan, $Score, $farmerid, $DateCreated, $DateUpdated, $CreatedBy, $LastModifiedBy) {
        $sql = "
            UPDATE ktv_nutrition
            SET SurveyNr=?,InterviewDate=?,KebunPanjang=?,KebunLebar=?,KbBayam=?,KbCabai=?,KbKacangPanjang=?,KbKangkung=?,
               KbSawi=?,KbTerong=?,KbTomat=?,KbKambing=?,KbSapi=?,KbBebek=?,KbAyam=?,KbIkan=?,aSagu=?,aNasi=?,aMie=?,aJagung=?,
               aRoti=?,bUbiJalarKuning=?,bSingkongKuning=?,bWortel=?,bLabu=?,cUbiJalarPutih=?,cSingkongPutih=?,cTalas=?,
               cKentang=?,dBayam=?,dDaunMelinjo=?,dDaunPepaya=?,dDaunSingkong=?,dKangkung=?,dSawi=?,eKacangPanjang=?,
               eTomat=?,eTerong=?,fJambuMerah=?,fMangga=?,fPepaya=?,gJambuAir=?,gKelapa=?,gPisang=?,gRambutan=?,gSemangka=?,
               gSalak=?,hJeroan=?,hHati=?,iAyam=?,iBebek=?,iKambing=?,iKerbau=?,iSapi=?,iLainnya=?,jAyam=?,jBebek=?,jEntok=?,
               jPuyuh=?,kCumiCumi=?,kIkan=?,kIkanTeri=?,kKepiting=?,kKerang=?,kUdang=?,lAirTahuSusuKedelai=?,lSausKacang=?,
               lTahu=?,lTempe=?,mKeju=?,mSusu=?,nMinyakGoreng=?,nMentega=?,nSantan=?,Score=?,LastModifiedBy=?,DateUpdated=?,DateCreated=?,CreatedBy=?
            WHERE SurveyNr=? and FarmerID=?";
        //$sql_date_survey = "UPDATE ktv_farmer SET DateSurvey=now() WHERE FarmerID=?";
        //$query = $this->db->query($sql_date_survey, array($farmerid));
        $query = $this->db->query($sql, array($SurveyNr, $InterviewDate, $KebunPanjang, $KebunLebar, $KbBayam, $KbCabai,
            $KbKacangPanjang, $KbKangkung, $KbSawi, $KbTerong, $KbTomat, $KbKambing, $KbSapi, $KbBebek, $KbAyam, $KbIkan, $aSagu,
            $aNasi, $aMie, $aJagung, $aRoti, $bUbiJalarKuning, $bSingkongKuning, $bWortel, $bLabu, $cUbiJalarPutih, $cSingkongPutih,
            $cTalas, $cKentang, $dBayam, $dDaunMelinjo, $dDaunPepaya, $dDaunSingkong, $dKangkung, $dSawi, $eKacangPanjang, $eTomat,
            $eTerong, $fJambuMerah, $fMangga, $fPepaya, $gJambuAir, $gKelapa, $gPisang, $gRambutan, $gSemangka, $gSalak, $hJeroan,
            $hHati, $iAyam, $iBebek, $iKambing, $iKerbau, $iSapi, $iLainnya, $jAyam, $jBebek, $jEntok, $jPuyuh, $kCumiCumi, $kIkan,
            $kIkanTeri, $kKepiting, $kKerang, $kUdang, $lAirTahuSusuKedelai, $lSausKacang, $lTahu, $lTempe, $mKeju, $mSusu,
            $nMinyakGoreng, $nMentega, $nSantan, $Score, $LastModifiedBy, $DateUpdated, $DateCreated, $CreatedBy,
            $SurveyNr, $farmerid));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function readPPIScorecards($provID, $districtID) {
        $sql = "SELECT
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
                a.CreatedBy,
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

    function readPPIScorecard($provID, $districtID, $LastDownloadDateUpdated) {
        $sql = "SELECT
                a.*
                FROM
                ktv_ppiscore2012 AS a ,
                ktv_farmer AS b
                WHERE
                a.FarmerID = b.FarmerID AND
                SUBSTR(b.VillageID FROM 1 FOR 2)='$provID' AND SUBSTR(b.VillageID FROM 1 FOR 4)='$districtID' AND a.DateCreated>TIMESTAMP('$LastDownloadDateUpdated')";

        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function createPPIScorecard($SurveyNr, $InterviewDate, $Householdmembers, $Schooling, $Education, $Employment, $HouseFloor, $ToiletFacility, $CookingFuel, $GasCylinder, $Refrigerator, $Motorcycle, $DateCreated, $DateUpdated, $CreatedBy, $LastModifiedBy, $FarmerID) {
        $sql = "
           REPLACE INTO ktv_ppiscore2012 (FarmerID, SurveyNr, InterviewDate, Householdmembers,Schooling, Education, Employment, HouseFloor, ToiletFacility, CookingFuel, GasCylinder, Refrigerator, Motorcycle,CreatedBy,LastModifiedBy, DateCreated,DateUpdated) VALUES(?, ?, ?, ?,?, ?, ?, ?, ?, ?, ?, ?, ?,?,?, ?,?)";
        //$sql_date_survey = "UPDATE ktv_farmer SET DateSurvey=now() WHERE FarmerID=?";
        //$query = $this->db->query($sql_date_survey, array($FarmerID));
        $query = $this->db->query($sql, array($FarmerID, $SurveyNr, $InterviewDate, $Householdmembers, $Schooling, $Education, $Employment, $HouseFloor, $ToiletFacility, $CookingFuel, $GasCylinder, $Refrigerator, $Motorcycle, $CreatedBy, $LastModifiedBy, $DateCreated, $DateUpdated));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function updatePPIScorecard($SurveyNr, $InterviewDate, $Householdmembers, $Schooling, $pEducation, $pEmployment, $pHouseFloor, $pToiletFacility, $pCookingFuel, $pGasCylinder, $pRefrigerator, $pMotorcycle, $DateCreated, $DateUpdated, $CreatedBy, $LastModifiedBy, $FarmerID) {
        $sql = "
            UPDATE ktv_ppiscore2012
            SET SurveyNr=?,InterviewDate=?,Householdmembers=?,Schooling=?,Education=?,Employment=?,HouseFloor=?,
               ToiletFacility=?,CookingFuel=?,GasCylinder=?,Refrigerator=?,Motorcycle=?,DateCreated=?,DateUpdated=?,CreatedBy=?,LastModifiedBy=?,DateUpdated=now()
            WHERE SurveyNr=? and FarmerID=?";
        //$sql_date_survey = "UPDATE ktv_farmer SET DateSurvey=now() WHERE FarmerID=?";
        //$query = $this->db->query($sql_date_survey, array($FarmerID));
        $query = $this->db->query($sql, array($SurveyNr, $InterviewDate, $Householdmembers, $Schooling, $pEducation, $pEmployment,
            $pHouseFloor, $pToiletFacility, $pCookingFuel, $pGasCylinder, $pRefrigerator, $pMotorcycle, $DateCreated, $DateUpdated, $CreatedBy, $LastModifiedBy, $SurveyNr, $FarmerID));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function readFamilies($provID, $districtID) {
        $sql = "select a.*
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

    function readFamily($provID, $districtID, $LastDownloadDateUpdated) {
        $sql = "select a.*
            FROM ktv_family a,
            ktv_farmer b
            WHERE
            a.FarmerID = b.FarmerID
            AND SUBSTR(b.VillageID FROM 1 FOR 2)='$provID' AND SUBSTR(b.VillageID FROM 1 FOR 4)='$districtID' AND (a.DateCreated>TIMESTAMP('$LastDownloadDateUpdated') OR a.DateUpdated>TIMESTAMP('$LastDownloadDateUpdated'))";
        //log_message('debug',$sql);
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function createFamily($farmerId, $AnggotaName, $HubunganKeluarga, $AnggotaAge, $AnggotaGender, $StatusSekolah, $DateCreated, $DateUpdated, $LastModifiedBy) {

        $sql_check = "SELECT FarmerID,AnggotaName,MAX(FamilyID) AS total FROM ktv_family
                    WHERE FarmerID=$farmerId AND AnggotaName='$AnggotaName'
                    ";
        $query1 = $this->db->query($sql_check);

        if ($query1) {
            foreach ($query1->result_array() as $rs) {
                if ($rs['total'] > 0) {
                    $sql_family = "
                    UPDATE ktv_family
                    SET AnggotaAge=?,HubunganKeluarga=?,AnggotaGender=?,StatusSekolah=?,LastModifiedBy=?,DateUpdated=?
                    WHERE FarmerID=? AND AnggotaName=? AND AnggotaAge=?";
                    $query = $this->db->query($sql_family, array($AnggotaAge, $HubunganKeluarga, $AnggotaGender, $StatusSekolah, $LastModifiedBy, $DateUpdated, $farmerId, $AnggotaName, $AnggotaAge));
                } else {
                    $sql_family = "
                     INSERT INTO ktv_family (FarmerID, AnggotaName, HubunganKeluarga, AnggotaAge, AnggotaGender,StatusSekolah,LastModifiedBy,DateCreated,DateUpdated)
                     VALUES (?,?,?,?,?,?,?,?,?)";
                    $query = $this->db->query($sql_family, array($farmerId, $AnggotaName, $HubunganKeluarga, $AnggotaAge, $AnggotaGender, $StatusSekolah, $LastModifiedBy, $DateCreated, $DateUpdated));
                }

                if ($query) {
                    $results['success'] = true;
                    $results['message'] = "record created.";
                } else {
                    $results['success'] = false;
                    $results['message'] = "Failed to create record";
                }
                return $results;
            }
        }
    }

    function readUser($LastDownloadDateUpdated) {
        $sql = "select a.*
            FROM sys_user a
            WHERE
             a.UserAddTime>TIMESTAMP('$LastDownloadDateUpdated') OR a.UserUpdateTime>TIMESTAMP('$LastDownloadDateUpdated')";
        //log_message('debug',$sql);
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readRole() {
        $sql = "SELECT a.*
            FROM sys_role a";
        //log_message('debug',$sql);
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readUserRole() {
        $sql = "SELECT a.*
            FROM sys_user_role a";
        //log_message('debug',$sql);
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readPerson() {
        $sql = "SELECT a.*
            FROM ktv_persons a";
        //log_message('debug',$sql);
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readProgramPartner() {
        $sql = "select a.*
            FROM ktv_program_partner a";
        //log_message('debug',$sql);
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readProgramStaff($LastDownloadDateUpdated) {
        $sql = "select a.*
            FROM ktv_program_staff a
            WHERE
             a.DateCreated>TIMESTAMP('$LastDownloadDateUpdated') OR a.DateUpdated>TIMESTAMP('$LastDownloadDateUpdated')";
        //log_message('debug',$sql);
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readPrivateStaff($LastDownloadDateUpdated) {
        $sql = "select a.*
            FROM ktv_private_staff a
            WHERE
             a.DateCreated>TIMESTAMP('$LastDownloadDateUpdated') OR a.DateUpdated>TIMESTAMP('$LastDownloadDateUpdated')";
        //log_message('debug',$sql);
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readDistrictPartner() {
        $sql = "select a.*
            FROM ktv_district_partner a";
        //log_message('debug',$sql);
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function getDetailUser($user_id) {
        $sql = "
            SELECT
                b.PersonNm AS name,
                b.Gender AS gender,
                b.`OfficialEmail` AS email
            FROM
                sys_user a
            LEFT JOIN ktv_persons b ON b.`UserID` = a.`UserId`
            WHERE a.UserID = ?";
        $query = $this->db->query($sql, array($user_id));
        $result = $query->result_array();
        $return['data'] = $result[0];
        return $result[0];
    }

    function syncUpdateData($data) {
        // table yang boleh update all data, meskipun di db sudah ada value nya
        $table_allowed_update = array(
            'ktv_farmer',
            'ktv_family'
        );

//        if (jsonParser($data, $whitelist)) {
//            $result['data'] = $data;
//            return $result;
//        }
        // fields yang di IGNORE
        $excluded_fields = array(
            'FlagSyncInsert', 'GardenCount', 'FlagSyncUpdate', 'id', 'PrePostSurvey',
            'Photo', 'Photo_path', 'Family', 'IsCertification', // farmer
            'InfrastructureID', 'VillageCropID'
        );

        $return = jsonParser($data, $table_allowed_update, $excluded_fields);

        return array('success' => true, 'data' => $data);
    }

    function insertSyncLog($user_id, $date_sync, $content) {
        $sql = "INSERT INTO sys_log_sync (UserId,MachineID,Content,DateFrom,Timestamp,Source) VALUES (?,null,?,?,?,?)";
        $query = $this->db->query($sql, array($user_id, $content, $date_sync, date('Y-m-d H:i:s'), '1'));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function readTraderStaff($LastDownloadDateUpdated) {
        $sql = "select a.*
            FROM ktv_trader_staff a
            WHERE
             a.DateCreated>TIMESTAMP('$LastDownloadDateUpdated') OR a.DateUpdated>TIMESTAMP('$LastDownloadDateUpdated')";
        //log_message('debug',$sql);
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readTraders($LastDownloadDateUpdated) {
        $sql = "select a.*
            FROM ktv_traders a
            WHERE
             a.DateCreated>TIMESTAMP('$LastDownloadDateUpdated') OR a.DateUpdated>TIMESTAMP('$LastDownloadDateUpdated')";
        //log_message('debug',$sql);
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readSurvey() {
        $sql = "select SurveyNr,concat(SurveyNr,'-',SurveyTxt) SurveyTxt FROM ktv_survey";
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readSurveys() {
        $sql = "SELECT * FROM ktv_survey";
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function fixTableFarmer() {
        $sql = "SELECT CPGid,FarmerID FROM ktv_cpg_farmer_member";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $rs) {
            $sql = "UPDATE ktv_farmer SET FarmerGroupID=" . $rs["CPGid"] . " WHERE FarmerID=" . $rs["FarmerID"] . "";
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

    function readActivityGarden($UserID, $DateCollection) {
        $sql = "SELECT a.FarmerID, b.FarmerName, a.GardenNr,a.SurveyNr,c.SurveyTxt, a.DateCollection,
                d.Village, a.GardenHaUnCertified,a.DateCreated
                FROM ktv_farmer_garden a,
                ktv_farmer b,
                ktv_survey c,
                ktv_village d
                WHERE
                a.FarmerID = b.FarmerID
                AND a.SurveyNr = c.SurveyNr
                AND b.VillageID = d.VillageID
                AND a.CreatedBy='$UserID'
                AND DATE(a.DateCollection)='$DateCollection'";
        // 11,13,'72','73','74','76'
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readActivityPostHarvest($UserID, $DateCollection) {
        $sql = "SELECT a.FarmerID, b.FarmerName,a.SurveyNr,c.SurveyTxt, a.DateCollection,
                        d.Village, a.DateCreated
                FROM ktv_farmer_post_harvest a,
                ktv_farmer b,
                ktv_survey c,
                ktv_village d
                WHERE
                a.FarmerID = b.FarmerID
                AND a.SurveyNr = c.SurveyNr
                AND b.VillageID = d.VillageID
                AND a.CreatedBy=$UserID
                AND DATE(a.DateCollection)='$DateCollection'";
        // 11,13,'72','73','74','76'
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readActivityPPI($UserID, $DateCollection) {
        $sql = "SELECT a.FarmerID, b.FarmerName,a.SurveyNr,c.SurveyTxt, a.InterviewDate DateCollection,
                        d.Village, a.DateCreated
                FROM ktv_ppiscore2012 a,
                ktv_farmer b,
                ktv_survey c,
                ktv_village d
                WHERE
                a.FarmerID = b.FarmerID
                AND a.SurveyNr = c.SurveyNr
                AND b.VillageID = d.VillageID
                AND a.CreatedBy=$UserID
                AND DATE(a.InterviewDate)='$DateCollection'";
        // 11,13,'72','73','74','76'
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readActivityNutrition($UserID, $DateCollection) {
        $sql = "SELECT a.FarmerID, b.FarmerName,a.SurveyNr,c.SurveyTxt, a.InterviewDate DateCollection,
                        d.Village, a.DateCreated
                FROM ktv_nutrition a,
                ktv_farmer b,
                ktv_survey c,
                ktv_village d
                WHERE
                a.FarmerID = b.FarmerID
                AND a.SurveyNr = c.SurveyNr
                AND b.VillageID = d.VillageID
                AND a.CreatedBy=$UserID
                AND DATE(a.InterviewDate)='$DateCollection'";
        // 11,13,'72','73','74','76'
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readActivityFarmer($UserID, $DateCollection) {
        $sql = "SELECT a.FarmerID, a.FarmerName,'' SurveyNr,'' SurveyTxt, a.DateUpdated DateCollection,
                        d.Village, a.DateCreated
                FROM ktv_farmer a,
                ktv_village d
                WHERE
                 a.VillageID = d.VillageID
                AND a.LastModifiedBy=$UserID
                AND DATE(a.DateUpdated)='$DateCollection'";
        // 11,13,'72','73','74','76'
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readActivityFamily($UserID, $DateCollection) {
        $sql = "SELECT a.FarmerID, a.FarmerName,f.AnggotaName SurveyNr,
                (CASE WHEN f.HubunganKeluarga=1 THEN 'Suami/Istri'
                WHEN f.HubunganKeluarga=1 THEN 'Anak' ELSE 'Lainnya' END) SurveyTxt,
                f.DateCreated DateCollection,
                d.Village, a.DateCreated
                FROM ktv_farmer a,
                ktv_family f,
                ktv_village d
                WHERE
                 a.FarmerID = f.FarmerID
                AND a.VillageID = d.VillageID
                AND a.LastModifiedBy=$UserID
                AND DATE(a.DateUpdated)='$DateCollection'";
        // 11,13,'72','73','74','76'
        $query = $this->db->query($sql);
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readAllActivity($UserID, $DateCollection) {
        $sql = "SELECT ff.FarmerID FarmerID,fr.FarmerName FarmerName,SUM(ff.TotalFarmer) TotalFarmer, SUM(ff.TotalGarden) TotalGarden, SUM(ff.TotalPostHarvest) TotalPostHarvest, SUM(ff.TotalNutrition) TotalNutrition, SUM(ff.TotalPPI2012) TotalPPI2012, SUM(ff.TotalGFP) TotalGFP FROM (SELECT x.FarmerID, (case when x.Type='Farmers' then IFNULL(x.Total,0) else 0 end) TotalFarmer, (case when x.Type='Gardens' then IFNULL(x.Total,0) else 0 end) TotalGarden, (case when x.Type='PostHarvest' then IFNULL(x.Total,0) else 0 end) TotalPostHarvest, (case when x.Type='PPI2012' then IFNULL(x.Total,0) else 0 end) TotalPPI2012, (case when x.Type='Nutrition' then IFNULL(x.Total,0) else 0 end) TotalNutrition, (case when x.Type='GFP' then IFNULL(x.Total,0) else 0 end) TotalGFP FROM (select 'Farmers' Type, a.FarmerID, count(*) Total from ktv_farmer a where (DATE(a.DateUpdated)=? OR DATE(a.DateCreated)=?) AND (a.CreatedBy=? OR a.LastModifiedBy=?) group by a.FarmerID UNION select 'Gardens' Type,a.FarmerID, count(*) Total from ktv_farmer_garden a where (DATE(a.DateUpdated)=? OR DATE(a.DateCreated)=?) AND (a.CreatedBy=? OR a.LastModifiedBy=?) group by a.FarmerID UNION select 'PostHarvest' Type,a.FarmerID, count(*) Total from ktv_farmer_post_harvest a where (DATE(a.DateUpdated)=? OR DATE(a.DateCreated)=?) AND (a.CreatedBy=? OR a.LastModifiedBy=?) group by a.FarmerID UNION select 'PPI2012' Type,a.FarmerID, count(*) Total from ktv_ppiscore2012 a where (DATE(a.DateUpdated)=? OR DATE(a.DateCreated)=?) AND (a.CreatedBy=? OR a.LastModifiedBy=?) group by a.FarmerID UNION select 'Nutrition' Type,a.FarmerID, count(*) Total from ktv_nutrition a where (DATE(a.DateUpdated)=? OR DATE(a.DateCreated)=?) AND (a.CreatedBy=? OR a.LastModifiedBy=?) group by a.FarmerID UNION select 'GFP' Type,a.FarmerID, count(*) Total from ktv_farmer_financial a where (DATE(a.DateUpdated)=? OR DATE(a.DateCreated)=?) AND (a.CreatedBy=? OR a.LastModifiedBy=?) group by a.FarmerID ) x) ff, ktv_farmer fr WHERE ff.FarmerID=fr.FarmerID GROUP BY ff.FarmerID, fr.FarmerName";
        // 11,13,'72','73','74','76'
        $query = $this->db->query($sql, array($DateCollection, $DateCollection, $UserID, $UserID, $DateCollection, $DateCollection, $UserID, $UserID, $DateCollection, $DateCollection, $UserID, $UserID, $DateCollection, $DateCollection, $UserID, $UserID, $DateCollection, $DateCollection, $UserID, $UserID, $DateCollection, $DateCollection, $UserID, $UserID));
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readFarmerSummary($FarmerID) {
        $sql = "SELECT
              all1.*
            FROM
              (SELECT
                a.FarmerID,
                'farmer' Obj,
                '' Survey,
                DATE_FORMAT(a.DateUpdated, '%d %b %Y %T') LastUpdated,
                b.UserRealName
              FROM
                ktv_farmer a
                LEFT JOIN sys_user b
                  ON a.LastModifiedBy = b.UserId
              WHERE FarmerID = ?
              UNION
              SELECT
                a.FarmerID,
                'family' Obj,
                '' Survey,
                MAX(
                  DATE_FORMAT(a.DateUpdated, '%d %b %Y %T')
                ) LastUpdated,
                b.UserRealName
              FROM
                ktv_farmer a
                LEFT JOIN sys_user b
                  ON a.LastModifiedBy = b.UserId
              WHERE FarmerID = ?
              UNION
            SELECT
  a.FarmerID,
  'garden' Obj,
  a.SurveyNr Survey,
  DATE_FORMAT(a.DateUpdated, '%d %b %Y %T') LastUpdated,
  b.UserRealName
FROM
  ktv_farmer_garden a
  INNER JOIN
    (SELECT
      FarmerID,
      MAX(SurveyNr) SurveyNr
    FROM
      ktv_farmer_garden
    GROUP BY FarmerID) latest
    ON a.FarmerID = latest.FarmerID
    AND a.SurveyNr = latest.SurveyNr
  LEFT JOIN sys_user b
    ON a.LastModifiedBy = b.UserId
WHERE a.FarmerID = ?
              UNION
              SELECT
  a.FarmerID,
  'post_harvest' Obj,
  a.SurveyNr Survey,
  DATE_FORMAT(a.DateUpdated, '%d %b %Y %T') LastUpdated,
  b.UserRealName
FROM
  ktv_farmer_post_harvest a
  INNER JOIN
    (SELECT
      FarmerID,
      MAX(SurveyNr) SurveyNr
    FROM
      ktv_farmer_post_harvest
    GROUP BY FarmerID) latest
    ON a.FarmerID = latest.FarmerID
    AND a.SurveyNr = latest.SurveyNr
  LEFT JOIN sys_user b
    ON a.LastModifiedBy = b.UserId
WHERE a.FarmerID = ?
              UNION
              SELECT
  a.FarmerID,
  'ppiscore2012' Obj,
  a.SurveyNr Survey,
  DATE_FORMAT(a.DateUpdated, '%d %b %Y %T') LastUpdated,
  b.UserRealName
FROM
  ktv_ppiscore2012 a
  INNER JOIN
    (SELECT
      FarmerID,
      MAX(SurveyNr) SurveyNr
    FROM
      ktv_ppiscore2012
    GROUP BY FarmerID) latest
    ON a.FarmerID = latest.FarmerID
    AND a.SurveyNr = latest.SurveyNr
  LEFT JOIN sys_user b
    ON a.LastModifiedBy = b.UserId
WHERE a.FarmerID = ?
              UNION
              SELECT
  a.FarmerID,
  'nutrition' Obj,
  a.SurveyNr Survey,
  DATE_FORMAT(a.DateUpdated, '%d %b %Y %T') LastUpdated,
  b.UserRealName
FROM
  ktv_nutrition a
  INNER JOIN
    (SELECT
      FarmerID,
      MAX(SurveyNr) SurveyNr
    FROM
      ktv_nutrition
    GROUP BY FarmerID) latest
    ON a.FarmerID = latest.FarmerID
    AND a.SurveyNr = latest.SurveyNr
  LEFT JOIN sys_user b
    ON a.LastModifiedBy = b.UserId
WHERE a.FarmerID = ?
UNION
              SELECT
  a.FarmerID,
  'finance' Obj,
  a.SurveyNr Survey,
  DATE_FORMAT(a.DateUpdated, '%d %b %Y %T') LastUpdated,
  b.UserRealName
FROM
  ktv_farmer_financial a
  INNER JOIN
    (SELECT
      FarmerID,
      MAX(SurveyNr) SurveyNr
    FROM
      ktv_farmer_financial
    GROUP BY FarmerID) latest
    ON a.FarmerID = latest.FarmerID
    AND a.SurveyNr = latest.SurveyNr
  LEFT JOIN sys_user b
    ON a.LastModifiedBy = b.UserId
WHERE a.FarmerID = ?) all1 ";
        // 11,13,'72','73','74','76'
        $query = $this->db->query($sql, array($FarmerID, $FarmerID, $FarmerID, $FarmerID, $FarmerID, $FarmerID, $FarmerID));
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $result['data'] = $query->result_array();
        return $result;
    }

    /**
     * Fungsi-fungsi untuk syncronize traceability app
     * @author Ardi <ardiantoro@koltiva.com>
     */

    /**
     * ini fungsi sementara aja ya
     * @param type $id
     * @return boolean
     */
    function getStandardID($id) {
        $output = false;

        $this->db->select('StandardID');
        $this->db->from('ktv_supplychain_quality_standard');
        //$this->db->join('ktv_supplychain_quality_standard','ktv_supplychain_quality_standard.StandardID = ktv_supplychain_quality_standard_detail.StandardID','left');
        $this->db->where('StandardSupplychainID',$id);
        //$this->db->where('StandardSupplychainID',56);
        //$this->db->group_by('StandardSupplychainID');
        $Q = $this->db->get();
        if($Q->num_rows() > 0){
            $result = $Q->result_array();
            $output['StandardID'] = $result[0]['StandardID'];
            foreach($result as $key => $value) {
                $this->db->select('*');
                $this->db->from('ktv_supplychain_quality_standard_detail');
                $this->db->where('StandardID',$value['StandardID']);
                $Q2 = $this->db->get();
                $details = $Q2->result_array();
                foreach($details as $kdet => $detail) {
                    //echo '<pre>';
                    //var_dump($detail);
                    $output['Detail'][] = $detail;
                }
            }
        }
        //var_dump($output);die;
        return $output;
    }

    function calculateReward($formula,$standard,$result) {
            //echo '<hr>';
        $output = 0;
        if($result != NULL && $result > 0 && strlen($formula) > 0) {
            //echo 'formula: ' . $formula . '; ';
            //echo 'standard: ' . $standard . '; ';
            //echo 'result: ' . $result . '; ';

            $reward = false;
            $claim = false;
            $find = array('[R]','[S]');
            $replace = array('$result','$standard');
            $result = floatval($result);
            $standard = floatval($standard);
            $formula = str_replace($find,$replace,$formula); //echo '<br>formula: ' . $result . '-' . $standard;
            eval("\$hasil = $formula;"); //dangerous but, nevermind~
            //echo '<br>reward: ' . $hasil . '; ';

            $output = $hasil;
        }

        return $output;
    }

    function sync_traceability($data) {

        $this->load->helper('file');
        //var_dump($data);die;
        ini_set('display_errors', true);
        error_reporting(E_ALL);
        
        $success = false;
        $return = array();

        // backup dulu ke file, siapa tau ada masalah di datanya
        $name = $data['SupplyBatchNumber'] . '-' . $data['CreatedBy'] . '-' . $data['SupplyOrgID'] . '-' . strtotime(date('Y-m-d H:i:s'));
        $dir = FCPATH . 'backup_traceability_sync';

        if(!is_dir($dir)) {
          mkdir($dir, 0777, true);
        }

        if(!write_file($dir.'/'.$name.'.json',json_encode($data))) {} else {}

        //start sync
        // check if batch number is exist
        $this->db->select('SupplyBatchID,SupplyBatchNumber');
        $this->db->from('ktv_supplychain_batch');
        $this->db->where('SupplyBatchNumber', $data['SupplyBatchNumber']);
        $Q = $this->db->get();
        if ($Q->num_rows() > 0) {
            $success = true;
            $return = $Q->row();
        } else {
            $insert = array(
                'SupplyOrgID' => $data['SupplyOrgID'],
                'SupplyDestOrgID' => $data['SupplyDestOrgID'],
                'SupplyDestStatus' => $data['SupplyStatus'],
                'SupplyBatchNumber' => $data['SupplyBatchNumber'],
                'VolumeBruto' => $data['SupplyVolumeBruto'],
                'VolumeNetto' => $data['SupplyVolumeNetto'],
                'SupplyBatchDate' => $data['SupplyBatchDate'],
                'DeliveryDate' => $data['DestDeliveryDate'],
                'DestPO' => $data['DestPO'],
                'DestWeight' => $data['DestWeight'],
                'DestDriver' => $data['DestDriver'],
                'DestNoPolisi' => $data['DestNoPolisi'],
            );

            $this->db->insert('ktv_supplychain_batch', $insert);

            $id = $this->db->insert_id();

            if ($id) {

                $return['OldSupplyBatchID'] = $data['SupplyBatchID'];
                $return['NewSupplyBatchID'] = $id;
                $return['SupplyBatchNumber'] = $data['SupplyBatchNumber'];

                $return['trans'] = array();
                $return['packing'] = array();
                $return['dtl'] = array();

                //insert trans
                foreach ($data['trans'] as $key => $value) {

                    $trans = array(
                        'SupplyBatchID' => $id,
                        'DateTransaction' => $value['SupplyTransDate'],
                        'SupplyType' => $value['SupplyTransType'],
                        'SupplyID' => $value['SupplyID'],
                        'FAQVolumeBruto' => $value['SupplyVolumeBruto'],
                        'FAQVolumeNetto' => $value['SupplyVolumeNetto'],
                        'FAQContractPrice' => $value['SupplyContractPrice'],
                        'FAQTotalPayment' => $value['SupplyTotalPayment'],
                    );

                    $this->db->insert('ktv_supplychain_transaction', $trans);

                    $transid = $this->db->insert_id();

                    foreach ($value['dtl'] as $dkey => $dvalue) {

                        $detail = array(
                            'FromBatchID' => NULL,
                            'SupplyTransID' => $transid,
                            'PackageID' => $dvalue['PackageID'],
                            'Type' => $dvalue['Type'],
                            'Weight' => $dvalue['Weight'],
                            'Tandan' => $dvalue['Tandan'],
                            'Brondol' => $dvalue['Brondol'],
                        );

                        $this->db->insert('ktv_supplychain_transaction_dtl', $detail);

                        $detailid = $this->db->insert_id();
                        array_push($return['dtl'], array(
                            'OldDetailID' => $dvalue['DetailID'],
                            'NewDetailID' => $detailid,
                            'SupplyTransID' => $transid,
                        ));
                    }
                }

                // packing
                
                foreach ($data['packing'] as $pkey => $pvalue) {
                    $packing = array(
                        'FromBatchID' => $id,
                        'Weight' => $pvalue['PackingWeight'],
                    );

                    $this->db->insert('ktv_supplychain_transaction_dtl', $packing);

                    $pid = $this->db->insert_id();
                    /*
                    array_push($return['packing'], array(
                        'OldDetailID' => $pvalue['DetailID'],
                        'NewDetailID' => $pid,
                        'FromBatchID' => $id,
                        'SupplyTransID' => $pvalue['SupplyTransID'],
                    ));
                    */
                }
                
                $success = true;
            }
        }
        //end sync

        return array('success' => $success, 'data' => $return);
    }

    function _createBatchForCargill($id,$warehouse) {

      $sql_batch = "SELECT * FROM ktv_supplychain_batch WHERE SupplyBatchID=?";
      $data_batch = $this->db->query($sql_batch,array($id))->row();
      $no['number'] = getBatchNumber($warehouse);//$this->readNumber(0,$SupplyOrgID);
      $sql_insert_batch = "INSERT INTO ktv_supplychain_batch(SupplyOrgID,SupplyDestStatus,SupplyBatchNumber,VolumeBruto,VolumeNetto,SupplyBatchDate,PerwakilanOrgID,DeliveryDate,DestPO,DestWeight)
                          VALUES (?,?,?,?,?,?,?,?,?,?)";
      $insert_batch = $this->db->query($sql_insert_batch,array($warehouse, 'Open', $no['number'], $data_batch->VolumeBruto, $data_batch->VolumeNetto, $data_batch->SupplyBatchDate, $data_batch->PerwakilanOrgID, $data_batch->DeliveryDate, $data_batch->DestPO, $data_batch->DestWeight));
      $batch_id = $this->db->insert_id();//var_dump($this->db->_error_message());die;

      //insert ke trasaction dan transaction dtl
      $sql_transaction = "SELECT SUM(FFVolumeBruto) FFVolumeBruto, SUM(FFVolumeNetto) FFVolumeNetto, SUM(FAQVolumeBruto) FAQVolumeBruto, SUM(FAQVolumeNetto) FAQVolumeNetto, FFContractPrice, FFNetPrice, SUM(FFTotalPayment) FFTotalPayment, FAQContractPrice, FAQNetPrice, SUM(FAQTotalPayment) FAQTotalPayment FROM ktv_supplychain_transaction WHERE SupplyBatchID=?";
      $data_transaction = $this->db->query($sql_transaction,array($id))->row();
      $sql_insert_transaction = "INSERT INTO ktv_supplychain_transaction(SupplyBatchID,DateTransaction,SupplyType,SupplyID,FFVolumeBruto,FFVolumeNetto,FAQVolumeBruto,FAQVolumeNetto,FFContractPrice,FFNetPrice,FFTotalPayment,FAQContractPrice,FAQNetPrice,FAQTotalPayment,DateCreated,CreatedBy) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),?)";
      $insert_transaction = $this->db->query($sql_insert_transaction, array($batch_id, $data_batch->SupplyBatchDate, 'Batch', $data_batch->SupplyBatchNumber, $data_transaction->FFVolumeBruto, $data_transaction->FFVolumeNetto, $data_transaction->FAQVolumeBruto, $data_transaction->FAQVolumeNetto, $data_transaction->FFContractPrice, $data_transaction->FFNetPrice, $data_transaction->FFTotalPayment, $data_transaction->FAQContractPrice, $data_transaction->FAQNetPrice, $data_transaction->FAQTotalPayment, 896));
      $transaction_id = $this->db->insert_id(); //var_dump($this->db->_error_message());die;
      $sql_transaction_dtl = "INSERT INTO ktv_supplychain_transaction_dtl(SupplyTransID,PackageID,Type,Weight,DateCreated,CreatedBy) VALUES(?,?,?,?,NOW(),896)";
      $insert_dtl = $this->db->query($sql_transaction_dtl,array($transaction_id,4,'FAQ',$data_transaction->FAQVolumeNetto));
      $dtl_id = $this->db->insert_id();
      if($dtl_id) { return $dtl_id; } else { return $this->db->_error_message(); }


      $sql_update_batch = "UPDATE ktv_supplychain_batch SET SupplyDestStatus='Delivered' WHERE SupplyBatchID=?";
      $this->db->query($sql_update_batch,array($id));
      
    }

    function sync_traceability_download($data, $unit, $last) {

        $delivered = array();

        //update status batch menjadi delivered
        if (count($data) > 0) {

            $this->db->select('SupplyBatchNumber,SupplyDestStatus');
            $this->db->from('ktv_supplychain_batch');
            $this->db->where_in('SupplyBatchNumber', $data);
            $this->db->where('SupplyDestStatus', 'Delivered');
            $Q = $this->db->get();
            if ($Q->num_rows() > 0) {
                $delivered = $Q->result_array();
            }
        }

        return array('success' => true, 'data' => $delivered);
    }
    
    function get_district_from_supplychainid($id) {
        $this->db->select('ktv_subdistrict.DistrictID AS DistrictID',false);
        $this->db->from('ktv_supplychain_org_view');
        $this->db->join('ktv_village', 'ktv_village.VillageID = ktv_supplychain_org_view.VillageID', 'left');
        $this->db->join('ktv_subdistrict', 'ktv_subdistrict.SubDistrictID = ktv_village.SubDistrictID', 'left');
        $this->db->where('SupplychainID',$id);
        $Q = $this->db->get();
        if($Q->num_rows() > 0){
            $row = $Q->row();
            return $row->DistrictID;
        }
        return false;
    }
    
    function sync_traceability_get_farmer_quota($FarmerID) {
        
        $sql = "( SELECT kcf.FarmerID id, CertificationStart, CertificationEnd, sum(( IFNULL(PanenTrekMonths, 0) * IFNULL(PanenTrekPanenMonth, 0) * IFNULL(PanenTrekKg, 0)) + ( IFNULL(PanenBiasaMonths, 0) * IFNULL(PanenBiasaPanenMonth, 0) * IFNULL(PanenBiasaKg, 0) ) + ( IFNULL(PanenRayaMonths, 0) * IFNULL(PanenRayaPanenMonth, 0) * IFNULL(PanenRayaKg, 0) ) ) batas_atas, IFNULL(IF( ((sum( ( IFNULL(PanenTrekMonths, 0) * IFNULL(PanenTrekPanenMonth, 0) * IFNULL(PanenTrekKg, 0) ) + ( IFNULL(PanenBiasaMonths, 0) * IFNULL(PanenBiasaPanenMonth, 0) * IFNULL(PanenBiasaKg, 0) ) + ( IFNULL(PanenRayaMonths, 0) * IFNULL(PanenRayaPanenMonth, 0) * IFNULL(PanenRayaKg, 0) ) ) - ((SELECT SUM((IFNULL(FAQVolumeBruto,0) + IFNULL(FFVolumeBruto,0))) FROM ktv_supplychain_transaction WHERE SupplyType = 'Farmer' AND SupplyID = kcf.FarmerID AND (DateTransaction BETWEEN CertificationStart AND CertificationEnd) GROUP BY SupplyID)))) < 0,0,(sum( ( IFNULL(PanenTrekMonths, 0) * IFNULL(PanenTrekPanenMonth, 0) * IFNULL(PanenTrekKg, 0) ) + ( IFNULL(PanenBiasaMonths, 0) * IFNULL(PanenBiasaPanenMonth, 0) * IFNULL(PanenBiasaKg, 0) ) + ( IFNULL(PanenRayaMonths, 0) * IFNULL(PanenRayaPanenMonth, 0) * IFNULL(PanenRayaKg, 0) ) ) - (SELECT SUM((IFNULL(FAQVolumeBruto,0) + IFNULL(FFVolumeBruto,0))) FROM ktv_supplychain_transaction WHERE SupplyType = 'Farmer' AND SupplyID = kcf.FarmerID AND (DateTransaction BETWEEN CertificationStart AND CertificationEnd) GROUP BY SupplyID))),0) sisa FROM ktv_farmer kcf LEFT JOIN ktv_cpg kc ON kcf.CPGid = kc.CPGid LEFT JOIN ktv_district kd ON substr(kcf.VillageID, 1, 4) = kd.DistrictID LEFT JOIN ( SELECT FarmerID, GardenNr, max(SurveyNr) LatestSurveyNr, CertificationStart, CertificationEnd FROM ktv_certification WHERE ( date(now()) BETWEEN CertificationStart AND CertificationEnd ) AND ExternalDate != '0000-00-00' AND ExternalDate IS NOT NULL GROUP BY FarmerID, GardenNr ) z ON kcf.FarmerID = z.FarmerID LEFT JOIN ktv_farmer_garden kcfg ON kcf.FarmerID = kcfg.FarmerID AND kcfg.GardenNr = z.GardenNr AND kcfg.SurveyNr = z.LatestSurveyNr WHERE ( kcf.FarmerID LIKE '".$FarmerID."' ) GROUP BY kcf.FarmerID )";
        
        $Q = $this->db->query($sql);
        if($Q->num_rows() > 0) {
            $row = $Q->row_array();
            return $row;
        }
        
        return false;
    }
    
    function sync_traceability_download_farmer($supply, $last) {
        
        $district = $this->get_district_from_supplychainid($supply);
        $farmer = array();
        
        //update farmers
        $sql = "SELECT MemberDisplayID FarmerID, MemberName FarmerName, '' CPGid, '' GroupName, v.VillageID, Village VillageName, 0 CertificationStatus, 10000 Kuota FROM
            ktv_members
            INNER JOIN ktv_member_role mr ON mr.MemberID = ktv_members.MemberID
            INNER JOIN ktv_access_partner_member amr ON amr.ApmMemberID = ktv_members.MemberID
            LEFT JOIN ktv_village v ON v.VillageID = ktv_members.VillageID
            WHERE
            mr.MroleID = 1 AND amr.ApmPartnerID = 10";
        $Q = $this->db->query($sql); 
        if ($Q->num_rows() > 0) { //var_dump($Q->num_rows());die;
            $farmer = $Q->result_array();
        }

        return array('success' => true, 'data' => $farmer);
    }

    function sync_traceability_download_setting($partner, $unit, $supplychain, $last) {

        $output = array(
            'trader' => array(),
            'trader_staff' => array(),
            'supplychain' => array(),
            'area' => array(),
            'price' => array()
        );

        //grab trader
        $this->db->select('*');
        $this->db->from('ktv_traders');

        $Q = $this->db->get();
        if ($Q->num_rows() > 0) {

            $data = $Q->result_array();
            $output['trader'] = $data;

            $trader = array();

            foreach ($data as $key => $value) {
                array_push($trader, $value['TraderID']);
            }

            //grab staff
            $this->db->select('*');
            $this->db->from('ktv_trader_staff');
            $this->db->where_in('TraderID', $trader);
            $Q = $this->db->get();
            if ($Q->num_rows() > 0) {
                $staff = $Q->result_array();
                $output['trader_staff'] = $staff;

                //grab suplychain
                $this->db->select('*');
                $this->db->from('ktv_supplychain_org');
                $this->db->where('OrgType', 'trader');
                $this->db->where_in('OrgID', $trader);
                $Q = $this->db->get();
                if ($Q->num_rows() > 0) {
                    $sc = $Q->result_array();

                    $supply = array();

                    foreach ($sc as $key => $value) {
                        array_push($supply, $value['SupplychainID']);
                    }

                    $output['supplychain'] = $sc;

                    //grab area
                    $this->db->select('*');
                    $this->db->from('ktv_supplychain_area');
                    $this->db->where_in('SupplyChainID', $supply);
                    $Q = $this->db->get();
                    if ($Q->num_rows() > 0) {
                        $area = $Q->result_array();
                        $output['area'] = $area;

                        //grab price
                        $this->db->select('*');
                        $this->db->from('ktv_supplychain_price');
                        $this->db->where_in('SupplyChainID', $supply);
                        $Q = $this->db->get();
                        if ($Q->num_rows() > 0) {
                            $price = $Q->result_array();
                            $output['price'] = $price;
                        }
                    }
                }
            }
        }
        //var_dump($output);die;
        return array('success' => true, 'data' => $output);
    }

    function sync_traceability_download_village($partner, $last) {

        $cpg = array();

        //update village
        $this->db->select('*');
        $this->db->from('ktv_village');
        $this->db->where('VillageID IN(SELECT VillageID from ktv_cpg WHERE CPGid IN(SELECT CPGid FROM ktv_cpg_partner WHERE PartnerID = ' . $partner . ' ))', NULL, false);
        //$this->db->limit(100);
        $Q = $this->db->get();
        //var_dump($this->db->_error_message());die;
        if ($Q->num_rows() > 0) {
            $village = $Q->result_array();
            //$village = $this->db->last_query();
        }

        return array('success' => true, 'data' => $village);
    }

    function sync_traceability_download_cpg($partner, $last) {

        $cpg = array();

        //update cpg
        $this->db->select('*');
        $this->db->from('ktv_cpg');
        $this->db->where('CPGid IN(SELECT CPGid FROM ktv_cpg_partner WHERE PartnerID = ' . $partner . ' )', NULL, false);
        $this->db->where('(DateCreated > "' . date('Y-m-d H:i:s', strtotime($last)) . '")', NULL, FALSE);
        //$this->db->where('(DateCreated > "' . date('Y-m-d H:i:s',strtotime($last)) . '" OR '.'DateUpdated > "' . date('Y-m-d H:i:s',strtotime($last)) . '")' ,NULL,FALSE);
        //$this->db->limit(100);
        $Q = $this->db->get();

        if ($Q->num_rows() > 0) {
            $cpg = $Q->result_array();
            //$cpg = $this->db->last_query();
        }

        return array('success' => true, 'data' => $cpg);
    }

    function sync_traceability_download_batch($supplychain) {

        ini_set('display_errors',true);

        $sent = array();
        
        //ambil data batch milik buying unit yang statusnya sent
        if ($supplychain > 0) {

            $this->db->select('SupplyBatchID id, SupplyBatchNumber number, SupplyBatchDate date, DeliveryDate deliverdate, VolumeBruto bruto, VolumeNetto netto, SupplyOrgID supplychainid, MemberName orgname, DestPO nomorpo, (SELECT IFNULL(SUM(IFNULL(NumberPackage, 0)),0) FROM ktv_supplychain_transaction WHERE SupplyBatchID = ktv_supplychain_batch.SupplyBatchID) cluster',false);
            $this->db->from('ktv_supplychain_batch');
            $this->db->join('ktv_supplychain_org','ktv_supplychain_org.SupplychainID = ktv_supplychain_batch.SupplyOrgID','LEFT');
            $this->db->join('ktv_members','ktv_members.MemberID = ktv_supplychain_org.OrgID','LEFT');
            $this->db->where('SupplyDestStatus', 'Sent');
            $this->db->where('SupplyDestOrgID', $supplychain);
            //echo $this->db->_compile_select();die;
            $Q = $this->db->get();
            if ($Q->num_rows() > 0) {
                $sent = $Q->result_array();
                foreach($sent as $key => $values) {
                    $sent[$key]['trans'] = $this->_getTransactionSummary($values['id']);
                }
            }
            
        }

        return array('success' => true, 'data' => $sent);
    }

    private function _getTransactionSummary($id) {
        $this->db->select('SupplyTransID transid,SupplyBatchID batchid,DateTransaction transdate,VolumeBruto1 bruto,VolumeNetto netto, NumberPackage cluster, FakturNumber fakturnumber,InvoiceNumber invoicenumber,SupplyID farmerid, MemberName farmername');
        $this->db->from('ktv_supplychain_transaction');
        $this->db->join('ktv_members','ktv_members.MemberUID=ktv_supplychain_transaction.SupplyID','left');
        $this->db->where('SupplyBatchID',$id); //var_dump($this->db->_compile_select());die;
        $Q = $this->db->get(); 
        if($Q->num_rows() > 0) {
            $result = $Q->result_array();
            return $result;
        }
        return array();
    }

}

?>
