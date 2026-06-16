<?php
class Mprogram extends CI_Model {

    function readPrograms($key,$start,$limit){
        $sql = "
            select %s
            from ktv_program_staff a
            LEFT JOIN ktv_program_partner b ON a.PartnerId=b.PartnerID
            LEFT JOIN ktv_persons c ON a.PersonId=c.PersonID
            WHERE PersonNm like ? AND a.StatusCd != 'nullified'
            ORDER BY PersonNm %s";
        $query = $this->db->query(sprintf($sql,'StaffID as id,PersonNm,IF(Gender="m","Laki-laki",
            IF(Gender="f","Wanita","")) as Gender,PrivateCellPhone AS StaffCellphone,PartnerName','LIMIT ?,?'),
            array("%$key%",(int)$start,(int)$limit));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql,'count(*) as total',''), array("%$key%"));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function readProgram($id){
        $sql = "
            select *,w.Village as Desa,x.SubDistrict as Kecamatan, y.District as Kabupaten,z.Province as Provinsi,
               a.PersonID as id, Ssn,PersonNm,ParentNm,BirthDttm,StaffID as StaffId,a.PartnerID as PartnerId,a.PersonId,
               a.UserId,v.District as WorkArea,ww.Province as waprovince
            from ktv_program_staff a
            LEFT JOIN ktv_program_partner b ON a.PartnerId=b.PartnerID
            LEFT JOIN ktv_persons c ON a.PersonId=c.PersonID
            LEFT JOIN sys_user d ON a.UserId=d.UserId
            left join ktv_village w on c.VillageID=w.VillageID
            left join ktv_subdistrict x on w.SubDistrictID=x.SubDistrictID
            left join ktv_district y on x.DistrictID=y.DistrictID
            left join ktv_province z on y.ProvinceID=z.ProvinceID
            LEFT JOIN sys_user_group f ON f.UserGroupUserId=d.UserId
            left join ktv_district v on v.DistrictID=a.WorkArea
            left join ktv_province ww on ww.ProvinceID=v.ProvinceID
            WHERE StaffID=?";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

    function createProgram($Ssn,$ExtId,$PersonNm,$ParentNm,$BirthDttm,$BirthPlace,$Photo,$MetaphoneNm,$Gender,
         $Address,$RegionalCd,$ZipCd,$Latitude,$Email,$ReligionCd,$BloodT,$MaritalSt,$Education,$Jobclass,$JobAddr,
         $NationalityNm,$RaceNm,$StatusCd,$Longitude,$Handphone,
         $realName,$userName,$pass,$active,$groupId,   $partnerId,$status,$Position,$area,
         $phone,$phone1,$email,$emil2,$nip,$userid){
         if ($userName=='') $userName = NULL;
         $sql = "
            INSERT INTO ktv_persons(Ssn,ExtId,PersonNm,ParentNm,BirthDttm,BirthPlace,Photo,MetaphoneNm,Gender,
	              Address,VillageID,ZipCd,Latitude,Email,ReligionCd,BloodT,MaritalSt,Education,Jobclass,JobAddr,
	              NationalityNm,RaceNm,StatusCd,Longitude,Handphone,
	              StaffCellphone,StaffCellphone2,StaffEmail,StaffEmail2,Nip)
            VALUES (?,?,?,?,?,?,?,?,?,   ?,?,?,?,?,?,?,?,?,?,?,   ?,?,?,?,?,   ?,?,?,?,?)";
         $sql_user = "
            INSERT INTO sys_user(UserRealName,UserName,UserPassword,UserActive,UserAddUserId,UserAddTime)
            VALUES (?,?,md5(?),?,".$_SESSION['userid'].",now())";
        $sql_user_group = "
            INSERT INTO sys_user_group(UserGroupUserId,UserGroupGroupId,UserGroupIsDefault)
            values (?,?,'1')";
         $sql_program_staff = "
            INSERT INTO ktv_program_staff(PartnerID,PersonID,StatusCd,UserId,CreatedBy,LastModifiedBy,
                Position,WorkArea,DateCreated,DateUpdated)
            VALUES (?,?,?,?,".$_SESSION['userid'].",".$_SESSION['userid'].",?,
               (SELECT DistrictID FROM ktv_district WHERE District=?),   now(),now())";
         $this->db->trans_start();
         if ($RegionalCd=='') $RegionalCd = null;
         $this->db->query($sql, array($Ssn,$ExtId,$PersonNm,$ParentNm,$BirthDttm,$BirthPlace,$Photo,$MetaphoneNm,$Gender,
            $Address,$RegionalCd,$ZipCd,$Latitude,$Email,$ReligionCd,$BloodT,$MaritalSt,$Education,$Jobclass,$JobAddr,
            $NationalityNm,$RaceNm,$StatusCd,$Longitude,$Handphone,
            $phone,$phone1,$email,$emil2,$nip));
         $id = $this->db->insert_id();
         $realName = ($realName==''?NULL:$realName);
         $userName = ($userName==''?NULL:$userName);
         $pass = ($pass==''?NULL:$pass);
         $active = ($active==''?NULL:$active);
         $unitId = ($unitId==''?NULL:$unitId);
         $this->db->query($sql_user, array($realName,$userName,$pass,$active,$unitId));
         $id_user = $this->db->insert_id();
         $groupId = ($groupId==''?NULL:$groupId);
         $this->db->query($sql_user_group, array($id_user,$groupId));
         $this->db->query($sql_program_staff, array($partnerId,$id,$status,$id_user,$Position,$area));
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

    function updateProgram($Ssn,$ExtId,$PersonNm,$ParentNm,$BirthDttm,$BirthPlace,$Photo,$MetaphoneNm,$Gender,$Address,
         $RegionalCd,$ZipCd,$Latitude,$Email,$ReligionCd,$BloodT,$MaritalSt,$Education,$Jobclass,$JobAddr,$NationalityNm,
         $RaceNm,$StatusCd,$ModifiedDttm,$CreatedBy,$ModifiedBy,$Longitude,$Handphone,
         $realName,$userName,$pass,$active,$groupId,   $partnerId,$status,$Position,$area,
         $phone,$phone1,$email,$emil2,$nip,$id,$user_id,$userid){
         if ($userName=='') $userName = NULL;
         $sql = "
            UPDATE ktv_persons
            SET Ssn=?,ExtId=?,PersonNm=?,ParentNm=?,BirthDttm=?,BirthPlace=?,Photo=?,MetaphoneNm=?,Gender=?,Address=?,
               VillageID=?,ZipCd=?,Latitude=?,Email=?,ReligionCd=?,BloodT=?,MaritalSt=?,Education=?,Jobclass=?,
               JobAddr=?,NationalityNm=?,RaceNm=?,StatusCd=?,ModifiedDttm=now(),Longitude=?,Handphone=?,
               StaffCellphone=?,StaffCellphone2=?,StaffEmail=?,StaffEmail2=?,Nip=?
            WHERE PersonID=?";
         $sql_program_staff = "
            UPDATE ktv_program_staff
            SET PartnerID=?,StatusCd=?,LastModifiedBy=".$_SESSION['userid'].",Position=?,UserId=?,
               WorkArea=(SELECT DistrictID FROM ktv_district WHERE District=?),DateUpdated=now(),LastModifiedBy=?
            WHERE StaffID=?";
         if ($pass!='') $pass = ",UserPassword=md5('$pass')";
         $sql_add_user = "
            INSERT INTO sys_user(UserRealName,UserName,UserPassword,UserActive,UserAddUserId,UserAddTime)
            VALUES (?,?,md5(?),?,".$_SESSION['userid'].",now())";
        $sql_add_user_group = "
            INSERT INTO sys_user_group(UserGroupUserId,UserGroupGroupId,UserGroupIsDefault)
            values (?,?,'1')";
         $sql_user = "
            UPDATE sys_user
            SET UserName=?,UserRealName=?$pass,UserActive=?,UserUpdateUserId=".$_SESSION['userid'].",UserUpdateTime=now()
            WHERE UserId=?";
        $sql_user_group = "
            UPDATE sys_user_group
            SET UserGroupGroupId=?
            WHERE UserGroupUserId=?";
         $this->db->trans_start();
         $staff = $this->readProgram($id);
         if ($RegionalCd=='') $RegionalCd = null;
         $this->db->query($sql, array($Ssn,$ExtId,$PersonNm,$ParentNm,$BirthDttm,$BirthPlace,$Photo,$MetaphoneNm,$Gender,
            $Address,$RegionalCd,$ZipCd,$Latitude,$Email,$ReligionCd,$BloodT,$MaritalSt,$Education,$Jobclass,$JobAddr,
            $NationalityNm,$RaceNm,$StatusCd,$Longitude,$Handphone,$phone,$phone1,$email,$emil2,$nip,$staff['PersonId']));
         if ($user_id=='') {
            $this->db->query($sql_add_user, array($realName,$userName,$pass,$active));
            $groupId = ($groupId==''?NULL:$groupId);
            $id_user = $this->db->insert_id();
            $this->db->query($sql_add_user_group, array($id_user,$groupId));
         } else {
            $this->db->query($sql_user, array($userName,$realName,$active,$staff['UserId']));
            $groupId = ($groupId==''?NULL:$groupId);
            $id_user = $staff['UserId'];
            $this->db->query($sql_user_group, array($groupId,$id_user));
         }
         $this->db->query($sql_program_staff, array($partnerId,$status,$Position,$id_user,$area,$userid,$id));
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

    function deleteProgram($id){
         /*
        $sql_person = "
            DELETE FROM ktv_persons WHERE PersonId=(SELECT PersonID FROM ktv_program_staff WHERE StaffID=?)";
        $sql_user_group = "
            DELETE FROM sys_user_group WHERE UserGroupUserId=(SELECT UserId FROM ktv_program_staff WHERE StaffID=?)";
        $sql_user = "
            DELETE FROM sys_user WHERE UserId=(SELECT UserId FROM ktv_program_staff WHERE StaffID=?)";
        $sql = "
            DELETE FROM ktv_program_staff WHERE StaffID=?";
        $query = $this->db->query($sql_person, array($id));
        $query = $this->db->query($sql_user_group, array($id));
        $query = $this->db->query($sql_user, array($id));
        $query = $this->db->query($sql, array($id));
        */

        $sql = "UPDATE ktv_program_staff SET StatusCd = 'nullified',LastModifiedBy='".$_SESSION['userid']."',DateUpdated=NOW() WHERE StaffID = ? LIMIT 1";
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

    function readPartnerlist(){
        $sql = "
            select %s
            from ktv_program_partner
            ORDER BY PartnerName %s";
        $query = $this->db->query(sprintf($sql,'PartnerID as id,PartnerName as label',''));
        return $query->result_array();
    }
}
?>
