<?php
class Mcpg extends CI_Model {

    function readRegionIDs($char,$start,$limit){
        $sql = "
            select %s
            from ktv_village w
            left join ktv_subdistrict x on w.SubDistrictID=x.SubDistrictID
            left join ktv_district y on x.DistrictID=y.DistrictID
            left join ktv_province z on y.ProvinceID=z.ProvinceID            
            WHERE concat(Province,' ',ifnull(District,''),' ',ifnull(SubDistrict,''),' ',ifnull(Village,'')) like ?
            ORDER BY Province %s";
        $query = $this->db->query(sprintf($sql,"VillageId as id,
            concat(Province,', ',ifnull(District,''),', ',ifnull(SubDistrict,''),', ',ifnull(Village,'')) as label",'LIMIT ?,?'),
            array("%$char%",(int)$start,(int)$limit));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql,'count(*) as total',''), array("%$char%"));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function readCpgs($prov,$kab,$key,$start,$limit){
        $sql = "
            select %s
            from ktv_cpg a
            left join ktv_village w on a.VillageID=w.VillageID
            left join ktv_subdistrict x on w.SubDistrictID=x.SubDistrictID
            left join ktv_district y on x.DistrictID=y.DistrictID
            left join ktv_province z on y.ProvinceID=z.ProvinceID            
            WHERE y.ProvinceID=? and District=? and GroupName like ? 
            ORDER BY GroupName %s";
        $query = $this->db->query(sprintf($sql,"CPGid as id,GroupName,Address,a.VillageID,TahunTerbentuk,
            concat(Province,', ',ifnull(District,''),', ',ifnull(SubDistrict,''),', ',ifnull(Village,'')) as RegionName,
            Latitude,Longitude,Elevation,a.Status",'LIMIT ?,?'),
            array($prov,$kab,"%$key%",(int)$start,(int)$limit));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql,'count(*) as total',''), array($prov,$kab,"%$key%"));
        $result['total'] = $query->row()->total;
        return $result;
    }



    function readCpg($id){
        $sql = "
            select CPGid as id, GroupName,Address,a.VillageID,TahunTerbentuk,
               Province as Provinsi,District as Kabupaten,SubDistrict as Kecamatan,Village as Desa,CpgBatchID,
               concat(Province,', ',ifnull(District,''),', ',ifnull(SubDistrict,''),', ',ifnull(Village,'')) as RegionName,
               Latitude,Longitude,Elevation,a.Status
            from ktv_cpg a
            left join ktv_village w on a.VillageID=w.VillageID
            left join ktv_subdistrict x on w.SubDistrictID=x.SubDistrictID
            left join ktv_district y on x.DistrictID=y.DistrictID
            left join ktv_province z on y.ProvinceID=z.ProvinceID            
            WHERE CPGid=?";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

    function _generateCPGID($desa){
        $sql = "
            SELECT IF(length(max(CPGid))<7,concat(sd.`DistrictID`,LPAD(max(CPGid)+1,3,'0')),max(CPGid)+1) as id
            FROM ktv_cpg
            LEFT JOIN ktv_village v ON v.VillageID = ktv_cpg.VillageID
            LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
            WHERE sd.DistrictID=substr(?,1,4)";
        $query = $this->db->query($sql,array($desa));
        $result = $query->result_array();        
        return $result[0]['id'];
    }

    function readBatchs(){
        $sql = "
            select CpgBatchID as id, concat(CpgBatchID,' - ',PartnerName) as label
            from ktv_cpg_batch a
            left join ktv_program_partner b on a.PartnerID=b.PartnerID";
        $query = $this->db->query($sql,array());
        return $query->result_array();        
    }

    function createCpg($GroupName,$Address,$TahunTerbentuk,$RegionID,$CpgBatchID,$lat,$long,$ele,$status){
        $sql = "
            INSERT INTO ktv_cpg(CPGid,GroupName,Address, TahunTerbentuk, VillageID, Latitude, Longitude, 
               Elevation, Status,DateCreated,DateUpdated)
            VALUES (?,?,?,?,?,?,?,   ?,?,now(),now())";
        $query = $this->db->query($sql, array($this->_generateCPGID($RegionID),$GroupName,$Address,$TahunTerbentuk,
            $RegionID,$lat,$long,$ele,$status));
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

    function createTraining($CPGID,$CPGtrainingsID,$ProgramStaffID,$ExtensionStaffID,$KeyFarmerID,$DemoplotOwnerID,
         $TrainingStart,$TrainingEnd,$PetaniKakao,$FamilyID){
        $sql = "
            INSERT INTO ktv_cpg_batch_trainings(CPGid,CPGtrainingsID,ProgramStaffID,ExtensionStaffID,KeyFarmerID,
	              DemoplotOwnerID,TrainingStart,TrainingEnd,PetaniKakao,FamilyID,DateCreated)
            VALUES (?,?,?,?,?,   ?,?,?,?,?,now())";
        $query = $this->db->query($sql, array($CPGID,$CPGtrainingsID,$ProgramStaffID,$ExtensionStaffID,$KeyFarmerID==''?NULL:$KeyFarmerID,
            $DemoplotOwnerID,$TrainingStart,$TrainingEnd,$PetaniKakao,$FamilyID));
        if ($query) {
            $results['idt'] = $this->db->insert_id();
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateTraining($CPGtrainingsID,$ProgramStaffID,$ExtensionStaffID,$KeyFarmerID,$DemoplotOwnerID,
         $TrainingStart,$TrainingEnd,$PetaniKakao,$FamilyID,$id){
        $sql = "
            UPDATE ktv_cpg_batch_trainings
            SET CPGtrainingsID=?,ProgramStaffID=?,ExtensionStaffID=?,KeyFarmerID=?,DemoplotOwnerID=?,TrainingStart=?,
                TrainingEnd=?,PetaniKakao=?,FamilyID=?,DateUpdated=now()
            WHERE CpgBatchTrainingID=?";
        $query = $this->db->query($sql, array($CPGtrainingsID,$ProgramStaffID,$ExtensionStaffID,$KeyFarmerID==''?NULL:$KeyFarmerID,
         $DemoplotOwnerID,$TrainingStart,$TrainingEnd,$PetaniKakao,$FamilyID,$id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function createParticipant($CpgBatchTrainingID,$FarmerID,$PetaniKakao,$FamilyID,$WritingAwal,$WritingAkhir,
        $BallotAwal,$BallotAkhir){
        $sql = "
            INSERT INTO ktv_cpg_batch_trainings_farmers(CpgBatchTrainingID, FarmerID, PetaniKakao, FamilyID, WritingAwal,
                WritingAkhir, BallotAwal, BallotAkhir,DateCreated)
            VALUES (?,?,?,?,?,   ?,?,?,now())";
        $query = $this->db->query($sql, array($CpgBatchTrainingID,$FarmerID,$PetaniKakao,$FamilyID,$WritingAwal,
            $WritingAkhir,$BallotAwal,$BallotAkhir));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateParticipant($CpgBatchTrainingID,$FarmerID,$PetaniKakao,$FamilyID,$WritingAwal,$WritingAkhir,
        $BallotAwal,$BallotAkhir,$id){
        $sql = "
            UPDATE ktv_cpg_batch_trainings_farmers
            SET CpgBatchTrainingID=?, FarmerID=?, PetaniKakao=?, FamilyID=?, WritingAwal=?, WritingAkhir=?, BallotAwal=?,
                BallotAkhir=?, DateUpdated=now()
            WHERE CpgBatchTrainingsFarmerID=?";
        $query = $this->db->query($sql, array($CpgBatchTrainingID,$FarmerID,$PetaniKakao,$FamilyID,$WritingAwal,$WritingAkhir,
            $BallotAwal,$BallotAkhir,$id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateCpg($GroupName,$Address,$TahunTerbentuk,$RegionID,$CpgBatchID,$lat,$long,$ele,$status,$id){
        $sql = "
            UPDATE ktv_cpg 
            SET GroupName=?,Address=?, TahunTerbentuk=?, VillageID=?, Latitude=?, Longitude=?, Elevation=?, Status=?,
               DateUpdated=now()
            WHERE CPGid=?";
        $query = $this->db->query($sql, array($GroupName,$Address,$TahunTerbentuk,$RegionID,$lat,$long,$ele,$status,$id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deleteCpg($id){
        $sql = "
            DELETE FROM ktv_cpg WHERE CPGid=?";
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
    
    function deleteTraining($id){
        $sql = "
            DELETE FROM ktv_cpg_batch_trainings WHERE CpgBatchTrainingID=?";
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

    function deleteParticipant($id){
        $sql = "
            DELETE FROM ktv_cpg_batch_trainings_farmers WHERE CpgBatchTrainingsFarmerID=?";
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

    function readTrainings($key){
        $sql = "
            SELECT %s
            FROM ktv_cpg_batch_trainings a
            LEFT JOIN ktv_cpg_trainings b ON a.CPGtrainingsID=b.CpgTrainingsID
            LEFT JOIN ktv_cpg_batch_trainings_farmers c ON a.CpgBatchTrainingID=c.CpgBatchTrainingID
            WHERE CPGid=?
            GROUP BY a.CpgBatchTrainingID
            ORDER BY CpgTrainings %s";
        $query = $this->db->query(sprintf($sql,"a.CpgBatchTrainingID as id,CpgTrainings as label,CpgTrainings,
            DATE_FORMAT(TrainingStart, '%m/%d/%Y') as TrainingStart,DATE_FORMAT(TrainingEnd, '%m/%d/%Y') as TrainingEnd,
            count(CpgBatchTrainingsFarmerID) as participant,a.CPGID,a.CpgTrainingsID,
            ProgramStaffID,ExtensionStaffID,KeyFarmerID,DemoplotOwnerID,a.PetaniKakao,a.FamilyID",''),
            array($key));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql,'count(distinct a.CpgBatchTrainingID) as total',''), array($key));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function readTraining($id){
        $sql = "
            SELECT a.*,b.CpgTrainings,d.GroupName,g.PersonNm as koordinator,h.FarmerName as pemandu,j.StaffName as penyuluh,
               w.Village as Desa,x.SubDistrict as Kecamatan, y.District as Kabupaten,z.Province as Provinsi,d.OldCPGid
            FROM ktv_cpg_batch_trainings a
            LEFT JOIN ktv_cpg_trainings b ON a.CPGtrainingsID=b.CpgTrainingsID
            LEFT JOIN ktv_cpg_batch_trainings_farmers c ON a.CpgBatchTrainingID=c.CpgBatchTrainingID
            LEFT JOIN ktv_cpg d ON a.CPGid=d.CPGid
            left join ktv_village w on d.VillageID=w.VillageID
            left join ktv_subdistrict x on w.SubDistrictID=x.SubDistrictID
            left join ktv_district y on x.DistrictID=y.DistrictID
            left join ktv_province z on y.ProvinceID=z.ProvinceID            
            LEFT JOIN ktv_program_staff f ON a.ProgramStaffID=f.StaffID
            LEFT JOIN ktv_persons g ON f.PersonID=g.PersonID
            LEFT JOIN ktv_farmer h ON h.StatusCode='active' and a.KeyFarmerID=h.FarmerId
            LEFT JOIN ktv_extension_staff j ON a.ExtensionStaffID=j.ExtensionID
            WHERE a.CpgBatchTrainingID=?";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

    function readFamilyTrainings($id){
        $sql = "
            SELECT FamilyID as id,AnggotaName as label
            FROM ktv_family
            WHERE FarmerID=?";
        $query = $this->db->query($sql, array($id));
        $result['data'] = $query->result_array();
        return $result;
    }

    function readTrainingNames(){
        $sql = "
            SELECT CpgTrainingsID as id,CpgTrainings as label
            FROM ktv_cpg_trainings WHERE StatusCode='active'";
        $query = $this->db->query($sql, array());
        $result['data'] = $query->result_array();
        return $result;
    }

    function readFamily($key){
        $sql = "
            SELECT FamilyID as id,AnggotaName as label
            FROM ktv_family
            WHERE FarmerID=?";
        $query = $this->db->query($sql, array($key));
        $result['data'] = $query->result_array();
        return $result;
    }

    function readParticipants($key){
        $sql = "
            SELECT %s
            FROM ktv_cpg_batch_trainings_farmers a
            left join ktv_farmer b on b.StatusCode='active' and a.FarmerID=b.FarmerID
            left join ktv_family d on a.FamilyID=d.FamilyID
            left join ktv_village w on b.VillageID=w.VillageID
            WHERE a.CpgBatchTrainingID=? %s";
        $query = $this->db->query(sprintf($sql,"a.CpgBatchTrainingsFarmerID,CpgBatchTrainingID,a.FarmerID as pFarmerID,
            PetaniKakao,a.FamilyID,AnggotaName,WritingAwal,WritingAkhir,BallotAwal,BallotAkhir,FarmerName as PersonNm,
            IF(PetaniKakao='1','Ya','Tidak') as partisipan,Village as Desa,Gender",''),
            array($key));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql,'count(*) as total',''), array($key));
        $result['total'] = $query->row()->total;
        return $result;
    }
    function readFarmers($cpg){
        $sql = "
            SELECT %s
            FROM ktv_farmer a
            WHERE StatusCode='active' and CPGid=? %s";
        $query = $this->db->query(sprintf($sql,"FarmerID as id,FarmerID as label",''), array($cpg));
        $result['data'] = $query->result_array();
        return $result;
    }
    function readFasilitators(){
        $sql = "
            SELECT %s
            FROM ktv_program_staff a
            left join ktv_persons b on a.PersonID=b.PersonID %s
            WHERE Position=1";
        $query = $this->db->query(sprintf($sql,"StaffID as id,PersonNm as label",''));
        $result['data'] = $query->result_array();
        return $result;
    }
    function readPenyuluhs($prov){
        $sql = "
            SELECT %s
            FROM ktv_extension_staff a
            LEFT JOIN ktv_village v ON v.VillageID = a.VillageID
            LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
            LEFT JOIN ktv_district d ON d.DistrictID = sd.DistrictID
            WHERE PositionID=5 and d.ProvinceID=substr(?,1,2)";
        $query = $this->db->query(sprintf($sql,"ExtensionID as id,StaffName as label"), array($prov));
        $result['data'] = $query->result_array();
        return $result;
    }

}
?>
