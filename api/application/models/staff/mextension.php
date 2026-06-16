<?php
class Mextension extends CI_Model {

    function readExtensions($key,$start,$limit){
        $sql = "
            select %s
            from ktv_extension_staff a
            left join ktv_village d on a.VillageID=d.VillageID
            left join ktv_subdistrict e on d.SubDistrictID=e.SubDistrictID
            left join ktv_district f on e.DistrictID=f.DistrictID
            WHERE StaffName like ? AND a.StatusCd != 'nullified'
            ORDER BY StaffName %s";
        $query = $this->db->query(sprintf($sql,'ExtensionID as id,StaffName as PersonNm,District as district,IF(GovInstitute=1,
            "Dinas Perkebunan dan Kehutanan",
            IF(GovInstitute=2,"Dinas Kesehatan",IF(GovInstitute=3,"Dinas Koperasi",IF(GovInstitute=4,"Badan Penyuluhan",
            IF(GovInstitute=5,"Balai Proteksi Tanaman",""))))) as InstitutionName,
            IF(StaffPosition="1","Penyuluh",IF(StaffPosition="2","Petugas Teknis",IF(StaffPosition="3","Petugas Administratif",
            IF(StaffPosition="4","Kepala Balai/unit/Dinas","")))) as PositionName','LIMIT ?,?'),
            array("%$key%",(int)$start,(int)$limit));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql,'count(*) as total',''), array("%$key%"));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function readExtension($id){
        $sql = "
            select *,d.Village as Desa,e.SubDistrict as Kecamatan, f.District as Kabupaten,g.Province as Provinsi,
               ExtensionID as id,StaffName as PersonNm
            from ktv_extension_staff a
            left join ktv_village d on a.VillageID=d.VillageID
            left join ktv_subdistrict e on d.SubDistrictID=e.SubDistrictID
            left join ktv_district f on e.DistrictID=f.DistrictID
            left join ktv_province g on f.ProvinceID=g.ProvinceID
            WHERE ExtensionID=?";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

    function createExtension($Ssn,$ExtId,$PersonNm,$ParentNm,$BirthDttm,$BirthPlace,$Photo,$MetaphoneNm,$Gender,
         $Address,$RegionalCd,$ZipCd,$Latitude,$Email,$ReligionCd,$BloodT,$MaritalSt,$Education,$Jobclass,$JobAddr,
         $NationalityNm,$RaceNm,$StatusCd,$Longitude,$Handphone,$ktp,
         $InstitutionID,$PositionID,$userid){
        $sql_extension = "
            INSERT INTO ktv_extension_staff(GovInstitute,StaffPosition,
               StaffName,BirthDttm,BirthPlace,Photo,Gender,
	              Address,VillageID,Education,StatusCd,Handphone,
                 DateCreated,CreatedBy,DateUpdated,LastModifiedBy,Email,KTP,MaritalSt)
            VALUES (?,?,
               ?,?,?,?,?,   ?,?,?,?,?,now(),?,now(),?,?,?,?)";
         $this->db->trans_start();
        $query = $this->db->query($sql_extension, array($InstitutionID,$PositionID,
            $PersonNm,$BirthDttm,$BirthPlace,$Photo,$Gender,
            $Address,$RegionalCd,$Education,$StatusCd,$Handphone,$userid,$userid,$Email,$ktp,$MaritalSt,));
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

    function updateExtension($Ssn,$ExtId,$PersonNm,$ParentNm,$BirthDttm,$BirthPlace,$Photo,$MetaphoneNm,$Gender,
         $Address,$RegionalCd,$ZipCd,$Latitude,$Email,$ReligionCd,$BloodT,$MaritalSt,$Education,$Jobclass,$JobAddr,
         $NationalityNm,$RaceNm,$StatusCd,$Longitude,$Handphone,$ktp,
         $InstitutionID,$PositionID,$id,$userid){
        $sql_staff = "
            UPDATE ktv_extension_staff
            SET GovInstitute=?,StaffPosition=?,
               StaffName=?,BirthDttm=?,BirthPlace=?,Photo=?,Gender=?,
	              Address=?,VillageID=?,Education=?,StatusCd=?,Handphone=?,DateUpdated=now(),LastModifiedBy=?,Email=?,KTP=?,MaritalSt=?
            WHERE ExtensionID=?";
         $this->db->trans_start();
        $query = $this->db->query($sql_staff, array($InstitutionID,$PositionID,
            $PersonNm,$BirthDttm,$BirthPlace,$Photo,$Gender,
            $Address,$RegionalCd,$Education,$StatusCd,$Handphone,$userid,$Email,$ktp,$MaritalSt,$id));
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

    function deleteExtension($id){
        //$sql = "DELETE FROM ktv_extension_staff WHERE ExtensionID=?";
        $sql="UPDATE ktv_extension_staff SET StatusCd = 'nullified',LastModifiedBy='".$_SESSION['userid']."',DateUpdated=NOW() WHERE ExtensionID = ? LIMIT 1";
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

    function readInstitutionIDs(){
        $sql = "
            select %s
            from ktv_institution
            ORDER BY InstitutionID %s";
        $query = $this->db->query(sprintf($sql,'InstitutionID as id, InstitutionName as label',''),
            array((int)$start,(int)$limit));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql,'count(*) as total',''));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function readDistrictInStaff($id){
        $sql = "
            select b.DistrictID as id, a.District as district, c.Province as province
            from ktv_access_staff b
            left join ktv_district a on b.DistrictID=a.DistrictID
			left join ktv_province c on c.ProvinceID=a.ProvinceID
            WHERE b.StaffID=?";
        $query = $this->db->query($sql,array($id));
        $result['data'] = $query->result_array();
        return $result;
    }
    function AddDistrict($StaffID,$DistrictID) {
        $sql = "
            INSERT INTO ktv_access_staff(StaffID,DistrictID)
            VALUES($StaffID,$DistrictID)";
        $query = $this->db->query($sql, array($StaffID,$DistrictID));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record deleted.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    function DeleteDistrict($StaffID,$DistrictID){
        $sql = "
            DELETE FROM  ktv_access_staff
            WHERE StaffID=?
            AND DistrictID=?";
        $query = $this->db->query($sql, array($StaffID,$DistrictID));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record deleted.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    function districts(){
        $sql = "
            select DistrictID as id, District as district
            from ktv_district";
        $query = $this->db->query($sql,array(100));
        $result['data'] = $query->result_array();
        return $result;
    }

}
?>
