<?php
class Mprivatestaff extends CI_Model {

    function readPrivatestaffs($key,$start,$limit){
        $sql = "
            select %s
            from ktv_private_staff a
            LEFT JOIN ktv_program_partner b ON a.PartnerID=b.PartnerID
            WHERE StaffName like ? AND a.StatusCode != 'nullified'
            ORDER BY PrivateStaffID %s";
        $query = $this->db->query(sprintf($sql,'PrivateStaffID as id,StaffName,PartnerName,OfficialCellphone,OfficialStaffEmail','LIMIT ?,?'),
            array("%$key%",(int)$start,(int)$limit));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql,'count(*) as total',''), array("%$key%"));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function readPrivatestaff($id){
        $sql = "
            select a.*,d.*,f.*, a.PrivateStaffID as id,g.District as kabupaten,h.Province as provinsi
            from ktv_private_staff a
            LEFT JOIN ktv_program_partner b ON a.PartnerId=b.PartnerID
            LEFT JOIN sys_user d ON a.UserId=d.UserId
            LEFT JOIN sys_user_group f ON f.UserGroupUserId=d.UserId
            LEFT JOIN ktv_district g ON g.DistrictID=a.Location
            LEFT JOIN ktv_province h ON g.ProvinceID=h.ProvinceID
            WHERE PrivateStaffID=?";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

    function createPrivatestaff($ExtId, $PartnerId,
                $PersonNm, $BirthDttm, $gambar, $userid,
                $PrivatePhone, $OfficialPhone, $PrivateEmail, $OfficialEmail,
                $Gender, $realName, $userName, $pass,
                $active, $groupId,$userid,$location
                ){
         if ($userName=='') $userName = NULL;
         $sql = "
            INSERT INTO ktv_private_staff(PrivateStaffID,PartnerID,StaffName,PrivateCellphone,OfficialCellphone,
            PrivateStaffEmail,OfficialStaffEmail,StaffBirth,StaffGender,Photo,Location,UserId,
            DateCreated,CreatedBy,DateUpdated,LastModifiedBy)
            VALUES (?,?,?,?,?, ?,?,?,?,?,?,?, now(),?,now(),?)";
         $sql_user = "
            INSERT INTO sys_user(UserRealName,UserName,UserPassword,UserActive)
            VALUES (?,?,md5(?),?)";
        $sql_user_group = "
            INSERT INTO sys_user_group(UserGroupUserId,UserGroupGroupId,UserGroupIsDefault)
            values (?,?,'1')";
         $this->db->trans_start();

         $realName = ($realName==''?NULL:$realName);
         $userName = ($userName==''?NULL:$userName);
         $pass = ($pass==''?NULL:$pass);
         $active = ($active==''?NULL:$active);
         $unitId = ($unitId==''?NULL:$unitId);
         $this->db->query($sql_user, array($realName,$userName,$pass,$active,$unitId));
         $groupId = ($groupId==''?NULL:$groupId);
         $id_user = $this->db->insert_id();
         $this->db->query($sql_user_group, array($id_user,$groupId));
         $this->db->query($sql, array($ExtId,$PartnerId,$PersonNm,$PrivatePhone,$OfficialPhone,$PrivateEmail,
            $OfficialEmail,$BirthDttm,$Gender,$gambar,$location,$id_user,$userid,$userid));
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

    function updatePrivatestaff($ExtId, $PartnerId,
                                $PersonNm, $BirthDttm, $gambar, $userid,
                                $PrivatePhone, $OfficialPhone, $PrivateEmail, $OfficialEmail,
                                $Gender, $realName, $userName, $pass,
                                $active, $groupId, $id,$useri,$location){
         if ($userName=='') $userName = NULL;
         $sql = "
            UPDATE ktv_private_staff
            SET PartnerID=? ,StaffName=? ,PrivateCellphone=? ,OfficialCellphone=? ,
            PrivateStaffEmail=? ,OfficialStaffEmail=? ,StaffBirth=? ,StaffGender=? ,Photo=? ,
            Location=(SELECT DistrictID FROM ktv_district WHERE District=?) ,
            UserId=?,
            DateUpdated=now(),LastModifiedBy=?
            WHERE PrivateStaffID=? ";
         if ($pass!='') $pass = ",UserPassword=md5('$pass')";
         $sql_user = "
            UPDATE sys_user
            SET UserName=?,UserRealName=?$pass,UserActive=?
            WHERE UserId=?";
        $sql_user_group_delete = "
            DELETE FROM sys_user_group WHERE UserGroupUserId=?";
        $sql_user_group_add = "
            INSERT INTO sys_user_group(UserGroupUserId,UserGroupGroupId,UserGroupIsDefault)
            VALUES (?,?,'1')";
         $this->db->trans_start();
         $this->db->query($sql, array($PartnerId,$PersonNm,$PrivatePhone,$OfficialPhone,$PrivateEmail,
             $OfficialEmail,$BirthDttm,$Gender,$gambar,$location,$userid,$useri,$ExtId));
         $this->db->query($sql_user, array($userName,$realName,$active,$userid));
         $groupId = ($groupId==''?NULL:$groupId);
         $this->db->query($sql_user_group_delete, array($userid));
         $this->db->query($sql_user_group_add, array($userid,$groupId));
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

    function deletePrivatestaff($id){
         /*
        $sql_user_group = "
            DELETE FROM sys_user_group WHERE UserGroupUserId=(SELECT UserId FROM ktv_private_staff WHERE PrivateStaffID=?)";
        $sql_get_user = "
            SELECT UserId FROM ktv_private_staff WHERE PrivateStaffID=?";
        $sql = "
            DELETE FROM ktv_private_staff WHERE PrivateStaffID=?";
        $sql_user = "
            DELETE FROM sys_user WHERE UserId=?";
        $query = $this->db->query($sql_user_group, array($id));
        $user = $this->db->query($sql_get_user, array($id));
        $us = $user->result_array();
        $query = $this->db->query($sql, array($id));
        $query = $this->db->query($sql_user, array($us[0]['UserId']));
        */

        $sql="UPDATE ktv_private_staff SET StatusCode='nullified',LastModifiedBy='".$_SESSION['userid']."',DateUpdated=NOW() WHERE PrivateStaffID = ? LIMIT 1";
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
