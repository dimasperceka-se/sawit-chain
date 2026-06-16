<?php
class Muser extends CI_Model {

    // function readUsers($key,$start,$limit){
    //     $sql = "
    //         select %s
    //         from sys_user
    //         left join sys_user_group on UserId=UserGroupUserId
    //         left join sys_group on GroupId=UserGroupGroupId
    //         WHERE UserRealName like ? OR UserName like ?
    //         ORDER BY UserRealName %s";
    //     $query = $this->db->query(sprintf($sql,'UserId as id,UserRealName,UserName,UserActive,GroupId,GroupName','LIMIT ?,?'),
    //         array("%$key%","%$key%",(int)$start,(int)$limit));
    //     $result['data'] = $query->result_array();
    //     $query = $this->db->query(sprintf($sql,'count(*) as total',''), array("%$key%","%$key%",));
    //     $result['total'] = $query->row()->total;
    //     return $result;
    // }
    public function readUsers($key, $RoleId, $GroupId, $Status, $start = 0, $limit = 50)
    {
        $sql = "SELECT SQL_CALC_FOUND_ROWS
    u.UserId
    , u.UserRealName
    , u.UserName
    , u.UserActive
    , r.RoleName
    , GROUP_CONCAT(DISTINCT g.GroupName) AS GroupName
FROM sys_user u
LEFT JOIN ktv_persons p ON p.UserID = u.UserId
LEFT JOIN sys_user_role ur ON ur.UserId = u.UserId
LEFT JOIN sys_role r ON r.RoleId = ur.RoleId
LEFT JOIN sys_user_group ug ON ug.UserGroupUserId = u.UserId
LEFT JOIN sys_group g ON g.GroupId = ug.UserGroupGroupId
WHERE 1 = 1 AND StatusCd != 'nullified'
    --filter--
GROUP BY u.UserId
ORDER BY u.UserRealName
LIMIT ?, ?
        ";
        $filter = '';
        $params = array();
        if (!empty($key)) {
            $filter .= " AND (UserRealName LIKE ('%{$key}%') OR UserName LIKE ('%{$key}%'))";
        }
        if (!empty($RoleId)) {
            $filter .= " AND ur.RoleId = ?";
            $params[] = intval($RoleId);
        }
        if (!empty($GroupId)) {
            $filter .= " AND ug.UserGroupGroupId = ?";
            $params[] = intval($GroupId);
        }
        if (!empty($Status)) {
            $filter .= " AND u.UserActive = ?";
            $params[] = $Status;
        }
        $params[] = intval($start);
        $params[] = intval($limit);
        $sql = str_replace('--filter--',$filter,$sql);
        $query = $this->db->query($sql,$params);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $sql_total = "SELECT FOUND_ROWS() AS total";
        $query_total = $this->db->query($sql_total);
        if ($query->num_rows() > 0) {
            $total = $query_total->row_array(0);
            return array(
                'data'      => $query->result_array(),
                'total'     => $total['total']
                );
        }
        return false;
    }

    function readGroupList($RoleId=null){
        // $sql = "
        //     select GroupId,GroupName
        //     from sys_group
        //     ORDER BY GroupName";
        // $query = $this->db->query($sql);
        $this->db->select('g.GroupId, g.GroupName', FALSE);
        $this->db->from('sys_group g');
        if (!empty($RoleId)) {
            $this->db->join('sys_role_group rg', 'rg.GroupId = g.GroupId', 'inner');
            $this->db->where('rg.RoleId', $RoleId, FALSE);

            if($_SESSION['is_admin'] != "1"){
                $this->db->where('g.GroupId !=', '1');
            }
        }
        $this->db->order_by('g.GroupName', 'asc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }

    // function readUser($id){
    //     $sql = "
    //         select UserId as id, UserRealName,UserName,UserActive,GroupId,GroupName,UserPassword
    //         from sys_user
    //         left join sys_user_group on UserId=UserGroupUserId
    //         left join sys_group on GroupId=UserGroupGroupId
    //         WHERE UserId=?";
    //     $query = $this->db->query($sql, array($id));
    //     $result = $query->result_array();
    //     $return['data'] = $result[0];
    //     //return $return;
    //     return $result[0];
    // }

    public function readUser($UserId)
    {
        $sql = "SELECT
    u.UserId
    , u.UserRealName
    , u.UserName
    , u.UserActive
    , u.UserIsAdmin
    , u.UserLanguage
    , r.RoleId
    , r.RoleName
    , p.PersonID
    , p.PersonNm
    , p.Ssn
    , p.EmpNr
    , p.BirthDate
    , p.BirthPlace
    , p.Gender
    , p.Address
    , p.VillageID
    , wa.WorkAreaID
    , pv.ProvinceID
    , p.MaritalSt
    , p.NationalityNm
    , p.StatusCd
    , p.WorkPhone
    , p.PrivateCellPhone
    , p.OfficialCellPhone
    , p.PrivateEmail
    , p.OfficialEmail
FROM sys_user u
LEFT JOIN ktv_persons p ON p.UserID = u.UserId
LEFT JOIN sys_user_role ur ON ur.UserId = u.UserId
LEFT JOIN sys_role r ON r.RoleId = ur.RoleId
LEFT JOIN ktv_ref_work_area wa ON wa.WorkAreaID = p.WorkAreaID
LEFT JOIN ktv_province pv ON pv.ProvinceID = wa.ProvinceID
WHERE u.UserId = ?
";
        $query = $this->db->query($sql, array($UserId));
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
        return false;
    }

    public function get_bank_staff($PersonID) {
        $this->db->select('bs.StaffID, bs.BranchID, bs.PersonID, bb.BranchBankID AS BankID', FALSE);
        $this->db->join('ktv_bank_branch bb', 'bb.BranchID = bs.BranchID', 'inner');
        $this->db->where('PersonID', $PersonID, FALSE);
        $query = $this->db->get('ktv_bank_branch_staff bs', 1, 0);
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
        return false;
    }
    
    public function get_cooperative_staff($PersonID) {
        $this->db->select('cs.StaffID, cs.CoopID, cs.PersonID, cs.FarmerID, cs.Position, cs.StaffStatus, cs.PaymentStatus, v.VillageID, sd.SubDistrictID, d.DistrictID, p.ProvinceID', FALSE);
        $this->db->join('ktv_cooperatives c', 'c.CoopID = cs.CoopID', 'inner');
        $this->db->join('ktv_village v', 'v.VillageID = c.VillageID');
        $this->db->join('ktv_subdistrict sd', 'sd.SubDistrictID = v.SubDistrictID');
        $this->db->join('ktv_district d', 'd.DistrictID = sd.DistrictID');
        $this->db->join('ktv_province p', 'p.ProvinceID = d.ProvinceID');
        $this->db->where('PersonID', $PersonID, FALSE);
        $query = $this->db->get('ktv_cooperative_staff cs', 1, 0);
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
        return false;
    }
    public function get_cpg_staff($PersonID) {
        $this->db->select('cs.StaffID, cs.CPGid, cs.PersonID, cs.FarmerID, cs.Position, v.VillageID, sd.SubDistrictID, d.DistrictID, p.ProvinceID', FALSE);
        $this->db->join('ktv_cpg c', 'c.CPGid = cs.CPGid', 'left');
        $this->db->join('ktv_village v', 'v.VillageID = c.VillageID', 'left');
        $this->db->join('ktv_subdistrict sd', 'sd.SubDistrictID = v.SubDistrictID', 'left');
        $this->db->join('ktv_district d', 'd.DistrictID = sd.DistrictID', 'left');
        $this->db->join('ktv_province p', 'p.ProvinceID = d.ProvinceID', 'left');
        $this->db->where('PersonID', $PersonID, FALSE);
        $query = $this->db->get('ktv_cpg_staff cs', 1, 0);
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
        return false;
    }
    public function get_extension_staff($PersonID) {
        $this->db->select('ExtensionID, PersonID, StaffPosition, GovInstitute', FALSE);
        $this->db->where('PersonID', $PersonID, FALSE);
        $query = $this->db->get('ktv_extension_staff', 1, 0);
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
        return false;
    }
    public function get_private_staff($PersonID) {
        $this->db->select('PrivateStaffID, PersonID, PartnerID, StatusCode', FALSE);
        $this->db->where('PersonID', $PersonID, FALSE);
        $query = $this->db->get('ktv_private_staff', 1, 0);
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
        return false;
    }
    public function get_program_staff($PersonID) {
        $this->db->select('ps.StaffID, ps.PersonID, ps.PartnerID, ps.Position', FALSE);
        // $this->db->join('ktv_district d', 'd.DistrictID = ps.WorkArea');
        // $this->db->join('ktv_province p', 'p.ProvinceID = d.ProvinceID');
        $this->db->where('PersonID', $PersonID, FALSE);
        $query = $this->db->get('ktv_program_staff ps', 1, 0);
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
        return false;
    }
    public function get_sce_staff($PersonID) {
        $this->db->select('cs.StaffID, cs.PersonID, cs.SceID, cs.FarmerID, cs.Position, v.VillageID, sd.SubDistrictID, d.DistrictID, p.ProvinceID', FALSE);
        $this->db->join('sce_farmer c', 'c.SceID = cs.SceID', 'inner');
        $this->db->join('ktv_farmer f', 'f.FarmerID = c.FarmerID', 'inner');
        $this->db->join('ktv_village v', 'v.VillageID = f.VillageID');
        $this->db->join('ktv_subdistrict sd', 'sd.SubDistrictID = v.SubDistrictID');
        $this->db->join('ktv_district d', 'd.DistrictID = sd.DistrictID');
        $this->db->join('ktv_province p', 'p.ProvinceID = d.ProvinceID');
        $this->db->where('PersonID', $PersonID, FALSE);
        $query = $this->db->get('sce_farmer_staff cs', 1, 0);
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
        return false;
    }
    public function get_trader_staff($PersonID) {
        $this->db->select('ts.TraderStaffID, ts.PersonID, ts.TraderID, ts.Position, v.VillageID, sd.SubDistrictID, d.DistrictID, p.ProvinceID', FALSE);
        $this->db->join('ktv_traders t', 't.TraderID = ts.TraderID', 'inner');
        $this->db->join('ktv_village v', 'v.VillageID = t.VillageID');
        $this->db->join('ktv_subdistrict sd', 'sd.SubDistrictID = v.SubDistrictID');
        $this->db->join('ktv_district d', 'd.DistrictID = sd.DistrictID');
        $this->db->join('ktv_province p', 'p.ProvinceID = d.ProvinceID');
        $this->db->where('PersonID', $PersonID, FALSE);
        $query = $this->db->get('ktv_trader_staff ts', 1, 0);
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
        return false;
    }
    public function get_warehouse_staff($PersonID) {
        $this->db->select('StaffID, PersonID, WarehouseID, Position', FALSE);
        $this->db->where('PersonID', $PersonID, FALSE);
        $query = $this->db->get('ktv_warehouse_staff', 1, 0);
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
        return false;
    }

    function readStaff($id){
        $sql = '
            SELECT *,IF(Gender="m","Laki-laki",IF(Gender="f","Wanita","")) as Gender
            FROM ktv_program_staff a
            LEFT JOIN ktv_persons b ON a.PersonID=b.PersonID
            WHERE UserId=?';
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        $return['data'] = $result[0];
        //return $return;
        return $result[0];
    }

    function readUsername($id,$username){
        $sql = '
            SELECT UserId FROM sys_user WHERE UserName=?';
        $query = $this->db->query($sql, array($username));
        $result = $query->result_array();
        if ($result[0]['UserId']=='' OR ($result[0]['UserId']!='' and $id==$result[0]['UserId'])) $result['success'] = true;
        else $result['success'] = false;
        return $result;
    }

    function createUser($UserRealName,$UserName,$UserActive,$groupId,$pass,$userid){
        $sql = "
            INSERT INTO sys_user(UserRealName,UserName,UserActive,UserPassword,   UserAddUserId,UserAddTime)
            VALUES (?,?,?,md5(?),   ?,now())";
        $sql_user_group = "
            INSERT INTO sys_user_group(UserGroupUserId,UserGroupGroupId,UserGroupIsDefault)
            values (?,?,'1')";
        $this->db->trans_start();
        $this->db->query($sql, array($UserRealName,$UserName,$UserActive,$pass,$userid));
        $userid = $this->db->insert_id();
        $this->db->query($sql_user_group, array($this->db->insert_id(),$groupId));
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success']     = true;
            $results['userid']      = $userid;
            $results['message']     = "record created.";
        } else {
            $results['success']     = false;
            $results['message']     = "Failed to create record";
        }
        return $results;
    }

    function updateUser($UserRealName,$UserName,$UserActive,$groupId,$pass,$id,$userid){
        if ($pass!='') $pass = ",UserPassword=md5('$pass')";
        $sql = "
            UPDATE sys_user
            SET UserRealName=?,UserName=?,UserActive=?$pass,UserUpdateUserId=?,UserUpdateTime=now()
            WHERE UserId=?";
        $sql_cek_user_group = "
            SELECT UserGroupUserId id FROM sys_user_group WHERE UserGroupUserId=?";
        $sql_add_user_group = "
            INSERT INTO sys_user_group(UserGroupUserId,UserGroupGroupId,UserGroupIsDefault)
            values (?,?,'1')";
        $sql_user_group = "
            UPDATE sys_user_group SET UserGroupGroupId=?
            WHERE UserGroupUserId=?";
        $this->db->trans_start();
        $this->db->query($sql, array($UserRealName,$UserName,$UserActive,$userid,$id));
        $query = $this->db->query($sql_cek_user_group, array($id));
        $result = $query->result_array();
        if ($result[0]['id']!='') $this->db->query($sql_user_group, array($groupId,$id));
        else $this->db->query($sql_add_user_group, array($id,$groupId));
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

    function deleteUser($id){
        //$sql = "DELETE FROM sys_user WHERE UserId=?";
        //$sql_user_group = "DELETE FROM sys_user_group WHERE UserGroupUserId=?";
        $this->db->trans_start();
        //$this->db->query($sql_user_group, array($id));
        //$this->db->query($sql, array($id));

        $sql="UPDATE sys_user SET StatusCode = 'nullified', UserUpdateUserId = '".$_SESSION['userid']."', UserUpdateTime = NOW() WHERE UserId = ? LIMIT 1";
        $this->db->query($sql, array($id));

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    function changeUserPassword($pass,$id,$userid){
        $sql = "
            UPDATE sys_user
            SET UserPassword=md5(?),UserUpdateUserId=?,UserUpdateTime=now()
            WHERE UserId=?";
        $stat = $this->db->query($sql, array($pass,$userid,$id));
        if ($stat) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }
    //profile
    function readStaffProfile($id){
        $sql = '
            SELECT IFNULL(c.PersonNm,IFNULL(f.StaffName,IFNULL(j.FarmerName,i.StaffName))) Name,
            	date(IFNULL(c.BirthDttm,IFNULL(f.StaffBirth,IFNULL(j.Birthdate,i.StaffBirth)))) TanggalLahir,
            	IFNULL(d.PartnerName,g.PartnerName) PartnerName,
            	IFNULL(c.StaffCellphone,IFNULL(f.PrivateCellphone,IFNULL(j.HandPhone,i.PrivateCellphone))) PrivatePhone,
            	IFNULL(c.StaffCellphone2,IFNULL(f.OfficialCellphone,i.OfficialCellphone)) OfficialPhone,
            	IFNULL(c.StaffEmail,IFNULL(f.PrivateStaffEmail,i.PrivateStaffEmail)) PrivateEMail,
            	IFNULL(c.StaffEmail2,IFNULL(f.OfficialStaffEmail,i.OfficialStaffEmail)) OfficialEmail,
            	IFNULL(c.Photo,f.Photo) PersonPhoto,
            	IFNULL(d.Photo,g.Photo) PartnerPhoto,
            	a.UserId, a.UserPassword as Oldpassword,a.UserLanguage,a.UserNotification
            FROM sys_user a
            LEFT JOIN ktv_program_staff b ON a.UserId=b.UserId
            LEFT JOIN ktv_persons c ON b.PersonID=c.PersonID
            LEFT JOIN ktv_program_partner d ON b.PartnerID=d.PartnerID
            LEFT JOIN ktv_private_staff f ON a.UserId = f.UserId
            LEFT JOIN ktv_program_partner g ON f.PartnerID = g.PartnerID
            LEFT JOIN ktv_supplychain_staff i ON a.UserId = i.UserId
            LEFT JOIN ktv_farmer j ON i.FarmerID = j.FarmerID
            WHERE a.UserId=?';
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        $return['data'] = $result[0];
        //return $return;
        return $result[0];
    }

    function updateStaffProfile($lang,$notif,$userid){
        $sql = "
            UPDATE sys_user
            SET UserLanguage=?,UserNotification=?,   UserUpdateUserId=?,UserUpdateTime=now()
            WHERE UserId=?";
        $this->db->trans_start();
        $this->db->query($sql, array($lang,$notif,$userid,$userid));
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $_SESSION['language'] = $lang;
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    public function insert_user($UserName, $UserRealName, $UserPassword, $UserEmail, $UserActive, $UserLanguage, $UserIsAdmin)
    {
        $UserAddUserId      = $_SESSION['userid'];
        $UserAddTime        = date('Y-m-d H:i:s');
        $UserUpdateUserId   = $_SESSION['userid'];
        $UserUpdateTime     = date('Y-m-d H:i:s');
        $query = $this->db->insert('sys_user', compact('UserName', 'UserRealName', 'UserPassword', 'UserEmail', 'UserActive', 'UserLanguage', 'UserIsAdmin','UserAddUserId','UserAddTime','UserUpdateUserId','UserUpdateTime'));
        if ($query !== false) {
            return $this->db->insert_id();
        }
        return false;
    }

    public function update_user($UserName, $UserRealName, $UserEmail, $UserActive, $UserLanguage, $UserIsAdmin, $UserId)
    {
        $UserUpdateUserId   = $_SESSION['userid'];
        $UserUpdateTime     = date('Y-m-d H:i:s');
        return $this->db->update('sys_user', compact('UserName', 'UserRealName', 'UserEmail', 'UserActive', 'UserLanguage', 'UserIsAdmin','UserUpdateUserId','UserUpdateTime'), compact('UserId'));
    }

    public function update_user_password($UserPassword, $UserId)
    {
        return $this->db->update('sys_user', compact('UserPassword'), compact('UserId'));
    }

    public function delete_user($UserId)
    {
        return $this->db->update('sys_user', array('StatusCode' => 'nullified'), compact('UserId'));
        // return $this->db->update('sys_user', compact('UserId'));
    }

    public function get_user_person($UserID)
    {
        $query = $this->db->get_where('ktv_persons', compact('UserID'), 1, 0);
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
        return false;
    }

    public function insert_person($UserID,$Ssn,$EmpNr,$PersonNm,$BirthDate,$BirthPlace,$Gender,$Address,$WorkAreaID,$MaritalSt,$Education,$NationalityNm,$StatusCd,$WorkPhone,$PrivateCellPhone,$OfficialCellPhone,$PrivateEmail,$OfficialEmail)
    {
        $query = $this->db->insert('ktv_persons', compact('UserID','Ssn','EmpNr','PersonNm','BirthDate','BirthPlace','Gender','Address','WorkAreaID','MaritalSt','Education','NationalityNm','StatusCd','WorkPhone','PrivateCellPhone','OfficialCellPhone','PrivateEmail','OfficialEmail'));
        if ($query !== false) {
            return $this->db->insert_id();
        }
        return false;
    }

    public function update_person($UserID,$Ssn,$EmpNr,$PersonNm,$BirthDate,$BirthPlace,$Gender,$Address,$WorkAreaID,$MaritalSt,$Education,$NationalityNm,$StatusCd,$WorkPhone,$PrivateCellPhone,$OfficialCellPhone,$PrivateEmail,$OfficialEmail,$PersonID)
    {
        return $this->db->update('ktv_persons', compact('UserID','Ssn','EmpNr','PersonNm','BirthDate','BirthPlace','Gender','Address','WorkAreaID','MaritalSt','Education','NationalityNm','StatusCd','WorkPhone','PrivateCellPhone','OfficialCellPhone','PrivateEmail','OfficialEmail'), array('PersonID' => $PersonID));
    }

    public function delete_person($PersonID)
    {
        return $this->db->update('ktv_persons', array('StatusCd'=>'nullified'), compact('PersonID'));
        // return $this->db->delete('ktv_persons', compact('PersonID'));
    }

    public function get_user_role($UserId)
    {
        $query = $this->db->get_where('sys_user_role', compact('UserId'), 1, 0);
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
        return false;
    }

    public function insert_user_role($UserId, $RoleId)
    {
        $query = $this->db->insert('sys_user_role', compact('UserId', 'RoleId'));
        if ($query !== false) {
            return true;
        }
        return false;
    }

    public function update_user_role($UserId,$RoleId)
    {
        return $this->db->update('sys_user_role', compact('RoleId'), compact('UserId'));
    }

    public function delete_user_role($UserId)
    {
        return $this->db->delete('sys_user_role', array('UserId' => $UserId));
    }

    public function insert_user_group($UserGroupUserId, $UserGroupGroupId, $UserGroupIsDefault)
    {
        $query = $this->db->insert('sys_user_group', compact('UserGroupUserId', 'UserGroupGroupId', 'UserGroupIsDefault'));
        if ($query !== false) {
            return true;
        }
        return false;
    }

    public function delete_user_group($UserGroupUserId)
    {
        return $this->db->delete('sys_user_group', compact('UserGroupUserId'));
    }

    public function get_role_detail($id)
    {
        $this->db->select('r.RoleId AS `id`,r.RoleName AS `name`,o.ObjectName AS `object`,o.ObjectTable AS `table`');
        $this->db->from('sys_role r');
        $this->db->join('sys_object o', 'o.ObjectId = r.RoleObjectId', 'inner');
        $this->db->where('r.RoleId', $id, FALSE);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
        return false;
    }

    public function insert_bank_staff($BranchID, $PersonID, $StatusCode, $UserId)
    {
        $DateCreated    = date('Y-m-d H:i:d');
        $CreatedBy      = $_SESSION['userid'];
        $query = $this->db->insert('ktv_bank_branch_staff', compact('BranchID', 'PersonID', 'StatusCode', 'DateCreated', 'CreatedBy', 'UserId'));
        if ($query !== false) {
            return true;
        }
        return false;
    }

    public function update_bank_staff($BranchID, $PersonID, $StatusCode, $StaffID, $UserId)
    {
        $DateUpdated        = date('Y-m-d H:i:s');
        $LastModifiedBy     = $_SESSION['userid'];
        return $this->db->update('ktv_bank_branch_staff', compact('BranchID', 'PersonID', 'StatusCode', 'DateUpdated', 'LastModifiedBy', 'UserId'), compact('StaffID'));
    }

    public function delete_bank_staff($PersonID)
    {
        return $this->db->update('ktv_bank_branch_staff', array('StatusCode'=>'nullified'), compact('PersonID'));
        // return $this->db->delete('ktv_bank_branch_staff', compact('PersonID'));
    }

    public function insert_cooperative_staff($CoopID, $PersonID, $FarmerID, $Position, $StaffStatus, $PaymentStatus, $StatusCode, $UserId)
    {
        $DateCreated    = date('Y-m-d H:i:d');
        $CreatedBy      = $_SESSION['userid'];
        $query = $this->db->insert('ktv_cooperative_staff', compact('CoopID', 'PersonID', 'FarmerID', 'Position', 'StaffStatus', 'PaymentStatus', 'StatusCode', 'DateCreated', 'CreatedBy', 'UserId'));
        if ($query !== false) {
            return true;
        }
        return false;
    }

    public function update_cooperative_staff($CoopID, $PersonID, $FarmerID, $Position, $StaffStatus, $PaymentStatus, $StatusCode, $StaffID, $UserId)
    {
        $DateUpdated        = date('Y-m-d H:i:s');
        $LastModifiedBy     = $_SESSION['userid'];
        return $this->db->update('ktv_cooperative_staff', compact('CoopID', 'PersonID', 'FarmerID', 'Position', 'StaffStatus', 'PaymentStatus', 'StatusCode', 'DateUpdated', 'LastModifiedBy', 'UserId'), compact('StaffID'));
    }

    public function delete_cooperative_staff($PersonID)
    {
        return $this->db->update('ktv_cooperative_staff', array('StatusCode'=>'nullified'), compact('PersonID'));
        // return $this->db->delete('ktv_cooperative_staff', compact('PersonID'));
    }

    public function insert_cpg_staff($CPGid, $PersonID, $FarmerID, $Position, $StatusCode, $UserId)
    {
        $DateCreated    = date('Y-m-d H:i:d');
        $CreatedBy      = $_SESSION['userid'];
        $query = $this->db->insert('ktv_cpg_staff', compact('CPGid', 'PersonID', 'FarmerID', 'Position', 'StatusCode', 'DateCreated', 'CreatedBy', 'UserId'));
        if ($query !== false) {
            return true;
        }
        return false;
    }

    public function update_cpg_staff($CPGid, $PersonID, $FarmerID, $Position, $StatusCode, $StaffID, $UserId)
    {
        $DateUpdated        = date('Y-m-d H:i:s');
        $LastModifiedBy     = $_SESSION['userid'];
        return $this->db->update('ktv_cpg_staff', compact('CPGid', 'PersonID', 'FarmerID', 'Position', 'StatusCode', 'DateUpdated', 'LastModifiedBy', 'UserId'), compact('StaffID'));
    }

    public function delete_cpg_staff($PersonID)
    {
        return $this->db->update('ktv_cpg_staff', array('StatusCode'=>'nullified'), compact('PersonID'));
        // return $this->db->delete('ktv_cpg_staff', compact('PersonID'));
    }

    public function insert_extension_staff($PersonID,$StaffPosition,$GovInstitute,$StatusCd, $UserId)
    {
        $DateCreated    = date('Y-m-d H:i:d');
        $CreatedBy      = $_SESSION['userid'];
        $query = $this->db->insert('ktv_extension_staff', compact('PersonID','StaffPosition','GovInstitute','StatusCd', 'DateCreated', 'CreatedBy', 'UserId'));
        if ($query !== false) {
            return true;
        }
        return false;
    }

    public function update_extension_staff($PersonID,$StaffPosition,$GovInstitute,$StatusCd, $ExtensionID, $UserId)
    {
        $DateUpdated        = date('Y-m-d H:i:s');
        $LastModifiedBy     = $_SESSION['userid'];
        return $this->db->update('ktv_extension_staff', compact('PersonID','StaffPosition','GovInstitute','StatusCd', 'DateUpdated', 'LastModifiedBy', 'UserId'), compact('ExtensionID'));
    }

    public function delete_extension_staff($PersonID)
    {
        return $this->db->update('ktv_extension_staff', array('StatusCd'=>'nullified'), compact('PersonID'));
        // return $this->db->delete('ktv_extension_staff', compact('PersonID'));
    }

    public function insert_private_staff($PersonID,$PartnerID,$StatusCode, $UserId)
    {
        $DateCreated    = date('Y-m-d H:i:d');
        $CreatedBy      = $_SESSION['userid'];
        $query = $this->db->insert('ktv_private_staff', compact('PersonID','PartnerID','StatusCode', 'DateCreated', 'CreatedBy', 'UserId'));
        if ($query !== false) {
            return true;
        }
        return false;
    }

    public function update_private_staff($PersonID,$PartnerID,$StatusCode, $PrivateStaffID, $UserId)
    {
        $DateUpdated        = date('Y-m-d H:i:s');
        $LastModifiedBy     = $_SESSION['userid'];
        return $this->db->update('ktv_private_staff', compact('PersonID','PartnerID','StatusCode', 'DateUpdated', 'LastModifiedBy', 'UserId'), compact('PrivateStaffID'));
    }

    public function delete_private_staff($PersonID)
    {
        return $this->db->update('ktv_private_staff', array('StatusCode'=>'nullified'), compact('PersonID'));
        // return $this->db->delete('ktv_private_staff', compact('PersonID'));
    }

    public function insert_program_staff($PersonID,$PartnerID,$WorkArea,$Position,$StatusCd, $UserId)
    {
        $DateCreated    = date('Y-m-d H:i:d');
        $CreatedBy      = $_SESSION['userid'];
        $query = $this->db->insert('ktv_program_staff', compact('PersonID','PartnerID','WorkArea','Position','StatusCd', 'DateCreated', 'CreatedBy', 'UserId'));
        if ($query !== false) {
            return true;
        }
        return false;
    }

    public function update_program_staff($PersonID,$PartnerID,$WorkArea,$Position,$StatusCd, $StaffID, $UserId)
    {
        $DateUpdated        = date('Y-m-d H:i:s');
        $LastModifiedBy     = $_SESSION['userid'];
        return $this->db->update('ktv_program_staff', compact('PersonID','PartnerID','WorkArea','Position','StatusCd', 'DateUpdated', 'LastModifiedBy', 'UserId'), compact('StaffID'));
    }

    public function delete_program_staff($PersonID)
    {
        return $this->db->update('ktv_program_staff', array('StatusCd'=>'nullified'), compact('PersonID'));
        // return $this->db->delete('ktv_program_staff', compact('PersonID'));
    }

    public function insert_sce_staff($PersonID,$SceID,$FarmerID,$Position,$StatusCode, $UserId)
    {
        $DateCreated    = date('Y-m-d H:i:d');
        $CreatedBy      = $_SESSION['userid'];
        $query = $this->db->insert('sce_farmer_staff', compact('PersonID','SceID','FarmerID','Position','StatusCode', 'DateCreated', 'CreatedBy', 'UserId'));
        if ($query !== false) {
            return true;
        }
        return false;
    }

    public function update_sce_staff($PersonID,$SceID,$FarmerID,$Position,$StatusCode,$StaffID, $UserId)
    {
        $DateUpdated        = date('Y-m-d H:i:s');
        $LastModifiedBy     = $_SESSION['userid'];
        return $this->db->update('sce_farmer_staff', compact('PersonID','SceID','FarmerID','Position','StatusCode', 'DateUpdated', 'LastModifiedBy', 'UserId'), compact('StaffID'));
    }

    public function delete_sce_staff($PersonID)
    {
        return $this->db->update('sce_farmer_staff', array('StatusCode'=>'nullified'), compact('PersonID'));
        // return $this->db->delete('sce_farmer_staff', compact('PersonID'));
    }

    public function insert_trader_staff($PersonID,$TraderID,$Position,$StatusCode, $UserId)
    {
        $DateCreated    = date('Y-m-d H:i:d');
        $CreatedBy      = $_SESSION['userid'];
        $query = $this->db->insert('ktv_trader_staff', compact('PersonID','TraderID','Position','StatusCode', 'DateCreated', 'CreatedBy', 'UserId'));
        if ($query !== false) {
            return true;
        }
        return false;
    }

    public function update_trader_staff($PersonID,$TraderID,$Position,$StatusCode,$TraderStaffID, $UserId)
    {
        $DateUpdated        = date('Y-m-d H:i:s');
        $LastModifiedBy     = $_SESSION['userid'];
        return $this->db->update('ktv_trader_staff', compact('PersonID','TraderID','Position','StatusCode', 'DateUpdated', 'LastModifiedBy', 'UserId'),compact('TraderStaffID'));
    }

    public function delete_trader_staff($PersonID)
    {
        return $this->db->update('ktv_trader_staff', array('StatusCode'=>'nullified'), compact('PersonID'));
        // return $this->db->delete('ktv_trader_staff', compact('PersonID'));
    }

    public function insert_warehouse_staff($PersonID,$WarehouseID,$Position,$StatusCode, $UserId)
    {
        $DateCreated    = date('Y-m-d H:i:d');
        $CreatedBy      = $_SESSION['userid'];
        $query = $this->db->insert('ktv_warehouse_staff', compact('PersonID','WarehouseID','Position','StatusCode', 'DateCreated', 'CreatedBy', 'UserId'));
        if ($query !== false) {
            return true;
        }
        return false;
    }

    public function update_warehouse_staff($PersonID,$WarehouseID,$Position,$StatusCode,$StaffID, $UserId)
    {
        $DateUpdated        = date('Y-m-d H:i:s');
        $LastModifiedBy     = $_SESSION['userid'];
        return $this->db->update('ktv_warehouse_staff', compact('PersonID','WarehouseID','Position','StatusCode', 'DateUpdated', 'LastModifiedBy', 'UserId'),compact('StaffID'));
    }

    public function delete_warehouse_staff($PersonID)
    {
        return $this->db->update('ktv_warehouse_staff', array('StatusCode'=>'nullified'), compact('PersonID'));
        // return $this->db->delete('ktv_warehouse_staff', compact('PersonID'));
    }

    public function getGroups($UserId)
    {
        $query = $this->db->get_where('sys_user_group', array('UserGroupUserId' => $UserId));
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function getAccess($UserId)
    {
        $query = $this->db->get_where('ktv_access_staff', array('UserId' => $UserId));
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function delete_user_access($UserId)
    {
        return $this->db->delete('ktv_access_staff', array('UserId' => $UserId));
    }

    public function insert_user_access($UserId, $DistrictID)
    {
        return $this->db->insert('ktv_access_staff', array(
            'UserId' => $UserId,
            'DistrictID' => $DistrictID,
        ));
    }

    public function get_username($username, $count = 0)
    {
        $query = $this->db->get_where('sys_user', array('UserName' => $username), 1);
        if ($query->num_rows() > 0) {
            $this->get_username($username.'_'.($count+1), $count+1);
        } else {
            return $username;
        }
    }

     public function readUserAffiliates($key, $RoleId, $GroupId, $Status, $start = 0, $limit = 50)
    {
        $sql = "SELECT SQL_CALC_FOUND_ROWS
    u.UserId
    , u.UserRealName
    , u.UserName
    , u.UserActive
    , r.RoleName
    , GROUP_CONCAT(DISTINCT g.GroupName) AS GroupName
    , IF(COUNT(ua.UserId) > 0, COUNT(ua.UserId) - 1, '-') as Affiliated
FROM sys_user u
LEFT JOIN ktv_persons p ON p.UserID = u.UserId
LEFT JOIN sys_user_role ur ON ur.UserId = u.UserId
LEFT JOIN sys_role r ON r.RoleId = ur.RoleId
LEFT JOIN sys_user_group ug ON ug.UserGroupUserId = u.UserId
LEFT JOIN sys_group g ON g.GroupId = ug.UserGroupGroupId
LEFT JOIN sys_user_affiliate ua ON ua.UserId = u.UserId
WHERE 1 = 1 AND StatusCd != 'nullified' AND u.UserActive != 'No' AND u.UserId != ".$_SESSION['userid']."
    --filter--
GROUP BY u.UserId
ORDER BY u.UserRealName
LIMIT ?, ?
        ";
        $filter = '';
        $params = array();
        if (!empty($key)) {
            $filter .= " AND (UserRealName LIKE ('%{$key}%') OR UserName LIKE ('%{$key}%'))";
        }
        if (!empty($RoleId)) {
            $filter .= " AND ur.RoleId = ?";
            $params[] = intval($RoleId);
        }
        if (!empty($GroupId)) {
            $filter .= " AND ug.UserGroupGroupId = ?";
            $params[] = intval($GroupId);
        }
        if (!empty($Status)) {
            $filter .= " AND u.UserActive = ?";
            $params[] = $Status;
        }
        $params[] = intval($start);
        $params[] = intval($limit);
        $sql = str_replace('--filter--',$filter,$sql);
        $query = $this->db->query($sql,$params);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $sql_total = "SELECT FOUND_ROWS() AS total";
        $query_total = $this->db->query($sql_total);
        if ($query->num_rows() > 0) {
            $total = $query_total->row_array(0);
            return array(
                'data'      => $query->result_array(),
                'total'     => $total['total']
                );
        }
        return false;
    }

    public function readUserAffiliate($UserId)
    {
        $this->db->select('*, u.UserId as SelfId');
        $this->db->from('sys_user u');
        $this->db->join('sys_user_affiliate ua', 'ua.UserId = u.UserId', 'left');
        $this->db->where('u.UserId', $UserId);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
        return false;
    }

    public function getAffiliated($UserId){
        $sql = "select * from sys_user u 
                where u.UserId in(select UserIdAff from sys_user_affiliate where UserId = ? and UserIdAff != ?)";
        $query = $this->db->query($sql, array($UserId, $UserId));

        return $query;
    }

    public function insertAffiliate($data){
        $this->db->trans_start();
            $AffSelf = $this->db->from('sys_user_affiliate')->where('UserId', $data['UserId'])->where('UserIdAff', $data['UserId'])->get();
            if ($AffSelf->num_rows() == 0) {
                # code...
                $this->db->insert('sys_user_affiliate', 
                                        array(
                                            'UserId'      => $data['UserId'], 
                                            'UserIdAff'   => $data['UserId'],
                                            'CreatedBy'   => $_SESSION['userid'],
                                            'DateCreated' => date('Y-m-d H:i:s')
                                        )
                                    );
            }
            if (count($data['Affiliates']) > 0) {
                # code...
                foreach ($data['Affiliates'] as $key => $value) {
                    # code...
                    $this->db->insert('sys_user_affiliate', 
                                        array(
                                            'UserId'      => $data['UserId'], 
                                            'UserIdAff'   => $value,
                                            'CreatedBy'   => $_SESSION['userid'],
                                            'DateCreated' => date('Y-m-d H:i:s')
                                        )
                                    );
                }
            }
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE){
            return false;
        } else{
            return true;
        }
    }

    public function deleteAffiliated($UserId, $UserIdAff){
        return $this->db->where('UserId', $UserId)->where('UserIdAff', $UserIdAff)->delete('sys_user_affiliate');
    }

    public function resetAffiliated($UserId){
        return $this->db->where('UserId', $UserId)->delete('sys_user_affiliate');
    }

    public function getOtherUsers($UserId, $Search){
        $sql = "SELECT * FROM sys_user 
                WHERE 
                UserId NOT IN (SELECT UserIdAff FROM sys_user_affiliate WHERE UserId = ?)
                AND
                UserId != ?
                AND
                UserActive = 'Yes'";
        if (!empty($Search)){
            $sql .= "AND (UserRealName LIKE ('%{$Search}%') OR UserName LIKE ('%{$Search}%'))";
        }
        $query = $this->db->query($sql, array($UserId, $UserId));

        return $query;
    }

}
?>
