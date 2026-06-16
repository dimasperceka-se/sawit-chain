<?php
/**
 * @Author: nikolius
 * @Date:   2016-09-27 16:59:03
 */
class Mbasic_staff extends CI_Model {

    var $muser;

    public function __construct()
    {
        parent::__construct();
        $this->muser = $this->muserprofile->getUserProfile($_SESSION['userid']);
    }

    public function getMainListBasicStaff($ObjType,$PersonNm,$start,$limit,$sortingField,$sortingDir){
        if($sortingField == "") $sortingField = 'PersonNm';
        if($sortingDir == "") $sortingDir = 'ASC';

        //if($_SESSION['is_admin'] == "1"){
            $sql="SELECT
                SQL_CALC_FOUND_ROWS
                StaffID,
                ObjType,
                StaffRegisteredNumber,
                PersonNm,
                Position,
                Status,
                DutyStation,
                UserAcc,
                UserApp,
                Role,
                IFNULL(UserUsername,'-') AS UserUsername,
                IFNULL(GroupName,'-') AS GroupName,
                UserStatus,
                UserAccountStatus
            FROM
            (
                SELECT
                    s.`StaffID`,
                    s.`StaffRegisteredNumber`,
                    ps.`PersonNm`,
                    IFNULL(rpos.PositionName,'-') AS `Position`,
                    s.`StatusCode` AS `Status`,
                    wa.`WorkAreaName` AS DutyStation,
                    IF(ps.UserId IS NULL,'No','Yes') AS UserAcc,
                    IF(su.UserExtId IS NULL,'No','Yes') AS UserApp,
                    CASE s.`ObjType`
                        WHEN 'program' THEN 'Program'
                        WHEN 'private' THEN 'Private'
                        WHEN 'extension' THEN 'Extension'
                        WHEN 'sce' THEN 'SCE'
                        WHEN 'trader' THEN 'Trader'
                        WHEN 'cooperative' THEN 'Cooperative'
                        WHEN 'warehouse' THEN 'Warehouse'
                        WHEN 'bank' THEN 'Bank'
                        WHEN 'farmergroup' THEN 'Farmer Group'
                        WHEN 'service' THEN 'Service Provider'
                        WHEN 'mill' THEN 'Mill'
                        WHEN 'agent' THEN 'Agent'
                        WHEN 'refinery' THEN 'Refinery'
                    END AS Role,
                    s.`ObjType`,
                    su.UserName AS UserUsername,
                    sy_g.GroupName,
                    IF(su.UserId IS NULL,'No','Yes') AS UserStatus,
                    IF(su.UserId IS NOT NULL,IF(su.UserActive = 'Yes', 'Active', 'Not Active'), '-') AS UserAccountStatus
                FROM
                    ktv_staffs s
                    LEFT JOIN ktv_persons ps ON s.`PersonID` = ps.`PersonID`
                    LEFT JOIN ktv_ref_work_area wa ON s.`WorkAreaID` = wa.`WorkAreaID`
                    LEFT JOIN ktv_staff_positions f ON s.`StaffID` = f.`StaffPosStaffID`
                        AND (CURDATE() BETWEEN f.`StaffPostStart` AND f.`StaffPostEnd`)
                        AND f.StatusCode = 'active'
                    LEFT JOIN ktv_ref_position_type rpos ON f.StaffPosPositionID = rpos.PositionID
                    LEFT JOIN sys_user su ON ps.UserId = su.UserId
                    LEFT JOIN sys_user_group su_g ON su.UserId = su_g.UserGroupUserId AND su_g.UserGroupIsDefault = '1'
                    LEFT JOIN sys_group sy_g ON su_g.UserGroupGroupId = sy_g.GroupId
                WHERE
                    ((s.`ObjType` = ?) OR ('' = ?)) AND
                    s.StatusCode != 'nullified' AND
                    ps.PersonNm LIKE ?
            ) AS tbl_for_sort
            GROUP BY StaffID
            ORDER BY $sortingField $sortingDir
            LIMIT ?,?";
            $query = $this->db->query($sql,array($ObjType,$ObjType,'%'.$PersonNm.'%',(int) $start,(int) $limit));
        /*}else{
            $sql="SELECT
                SQL_CALC_FOUND_ROWS
                StaffID,
                StaffRegisteredNumber,
                PersonNm,
                Position,
                Status,
                DutyStation,
                UserAcc,
                UserApp,
                Role,
                IFNULL(UserUsername,'-') AS UserUsername,
                IFNULL(GroupName,'-') AS GroupName,
                UserStatus
            FROM
            (
                SELECT
                    s.`StaffID`,
                    s.`StaffRegisteredNumber`,
                    ps.`PersonNm`,
                    IFNULL(rpos.PositionName,'-') AS `Position`,
                    s.`StatusCode` AS `Status`,
                    wa.`WorkAreaName` AS DutyStation,
                    IF(ps.UserId IS NULL,'No','Yes') AS UserAcc,
                    IF(su.UserExtId IS NULL,'No','Yes') AS UserApp,
                    CASE s.`ObjType`
                        WHEN 'program' THEN 'Program'
                        WHEN 'private' THEN 'Private'
                        WHEN 'extension' THEN 'Extension'
                        WHEN 'sce' THEN 'SCE'
                        WHEN 'trader' THEN 'Trader'
                        WHEN 'cooperative' THEN 'Cooperative'
                        WHEN 'warehouse' THEN 'Warehouse'
                        WHEN 'bank' THEN 'Bank'
                        WHEN 'farmergroup' THEN 'Farmer Group'
                    END AS Role,
                    su.UserName AS UserUsername,
                    sy_g.GroupName,
                    su.UserActive AS UserStatus
                FROM
                    ktv_staffs s
                    LEFT JOIN ktv_persons ps ON s.`PersonID` = ps.`PersonID`
                    LEFT JOIN ktv_ref_work_area wa ON s.`WorkAreaID` = wa.`WorkAreaID`
                    LEFT JOIN ktv_staff_positions f ON s.`StaffID` = f.`StaffPosStaffID`
                        AND (CURDATE() BETWEEN f.`StaffPostStart` AND f.`StaffPostEnd`)
                        AND f.StatusCode = 'active'
                    LEFT JOIN ktv_ref_position_type rpos ON f.StaffPosPositionID = rpos.PositionID
                    LEFT JOIN sys_user su ON ps.UserId = su.UserId
                    LEFT JOIN sys_user_group su_g ON su.UserId = su_g.UserGroupUserId AND su_g.UserGroupIsDefault = '1'
                    LEFT JOIN sys_group sy_g ON su_g.UserGroupGroupId = sy_g.GroupId

                    INNER JOIN `sys_user_access_object` acc_obj ON s.ObjID = acc_obj.`ObjID` AND acc_obj.`ObjType` = s.ObjType
                    INNER JOIN `sys_user_access_role` acc_role ON acc_obj.`UserAccRoleID` = acc_role.`UserAccRoleID`
                WHERE
                    ((s.`ObjType` = ?) OR ('' = ?))
                    AND s.StatusCode != 'nullified'
                    AND ps.PersonNm LIKE ?
                    AND acc_role.`UserId` = ?
                    AND su.UserIsAdmin != '1'
                    AND sy_g.GroupId != '1'
            ) AS tbl_for_sort
            GROUP BY StaffID
            ORDER BY $sortingField $sortingDir
            LIMIT ?,?";
            $query = $this->db->query($sql,array($ObjType,$ObjType,'%'.$PersonNm.'%',$_SESSION['userid'],(int) $start,(int) $limit));
        }*/
        // $result['query'] = $this->db->last_query();
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    public function getMainListStaffPosition($StaffID,$start,$limit,$sortingField,$sortingDir){
        if($sortingField == "") $sortingField = 'a.StaffPostEnd';
        if($sortingDir == "") $sortingDir = 'DESC';

        $sql="SELECT
                SQL_CALC_FOUND_ROWS
                a.StaffPosID
                , b.`PositionName`
                , a.`StaffPostStart`
                , a.`StaffPostEnd`
                , a.`StatusCode`
            FROM
                ktv_staff_positions a
                LEFT JOIN `ktv_ref_position_type` b ON a.`StaffPosPositionID` = b.`PositionID`
            WHERE
                a.`StaffPosStaffID` = ?
                AND a.`StatusCode` != 'nullified'
            ORDER BY $sortingField $sortingDir
            LIMIT ?,?";
        $query = $this->db->query($sql,array($StaffID, (int) $start, (int) $limit));
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    public function getPropinsi(){
        if($_SESSION['is_admin'] == "1"){
            $sqlWhere = "1 = 1";
        }else{
            $sqlWhere = "b.`DistrictID` IN (".$this->muser['accessStaff'].")";
        }

        $sql="SELECT
                a.`ProvinceID` AS id
                , a.`Province` AS label
                , ct.`PhoneCode`
            FROM
                ktv_province a
                INNER JOIN ktv_district b ON a.`ProvinceID` = b.`ProvinceID`
                LEFT JOIN ktv_country ct ON a.`CountryCode` = ct.`ISO2`
            WHERE
                $sqlWhere
            GROUP BY a.`ProvinceID`";
        $query = $this->db->query($sql,array());
        $return['data'] = $query->result_array();
        return $return;
    }

    public function getWorkarea($prov){
        $sql="SELECT
                    a.`WorkAreaID` AS id,
                    a.`WorkAreaName` AS label
                FROM
                    ktv_ref_work_area a
                WHERE
                    a.`ProvinceID` = ?
                ORDER BY a.`WorkAreaName` ASC";
        $query = $this->db->query($sql, array($prov));
        $return['data'] = $query->result_array();
        return $return;
    }

    public function getKecamatan($DistrictID){
        $sql = "
            SELECT distinct SubDistrict as label, a.SubDistrictID as id
            FROM ktv_subdistrict a
            LEFT JOIN ktv_district b ON a.DistrictID=b.DistrictID
            WHERE a.DistrictID = ?
            ORDER BY SubDistrict";
        $query          = $this->db->query($sql, array($DistrictID));
        $return['data'] = $query->result_array();
        return $return;
    }

    public function getDesa($SubDistrictID){
        $sql="SELECT
                VillageID AS id,
                Village AS label
            FROM
                ktv_village
            WHERE
                SubDistrictID = ? AND
                StatusCode = 'active'
            ORDER BY Village ASC";
        $query = $this->db->query($sql, array($SubDistrictID));
        $return['data'] = $query->result_array();
        return $return;
    }

    public function getCpg($DistrictID){
        $sql="SELECT
                a.`CPGid` AS id,
                a.`GroupName` AS label
            FROM
                ktv_cpg a
            WHERE
                a.`Status` = 'active' AND
                SUBSTR(a.CPGid,1,4) = ?
            ORDER BY a.`GroupName` ASC";
        $query = $this->db->query($sql, array($DistrictID));
        $return['data'] = $query->result_array();
        return $return;
    }

    public function getFarmer($CPGid){
        $sql="SELECT
                a.`FarmerID` AS id,
                CONCAT(a.`FarmerID`,' - ',a.`FarmerName`) AS label
            FROM
                ktv_farmer a
            WHERE
                a.`CPGid` = ?
            ORDER BY a.`FarmerID` ASC";
        $query = $this->db->query($sql, array($CPGid));
        $return['data'] = $query->result_array();
        return $return;
    }

    public function getObjIdBank($DistrictID){
        if($_SESSION['is_admin'] == "1"){
            $sql="SELECT
                a.`BranchID` AS id,
                CONCAT(b.`BankName`,' - ',a.`BranchName`) AS label
            FROM
                ktv_bank_branch a
                LEFT JOIN ktv_bank b ON a.`BranchBankID` = b.`BankID`
            WHERE
                a.BranchDistrictID = ? AND
                a.StatusCode = 'active'
            ORDER BY b.`BankName` ASC";
            $query = $this->db->query($sql, array($DistrictID));
        }else{
            $sql="SELECT
                a.`BranchID` AS id,
                CONCAT(b.`BankName`,' - ',a.`BranchName`) AS label
            FROM
                ktv_bank_branch a
                LEFT JOIN ktv_bank b ON a.`BranchBankID` = b.`BankID`
                INNER JOIN `sys_user_access_object` acc_obj ON a.`BranchID` = acc_obj.`ObjID` AND acc_obj.`ObjType` = 'bank'
                INNER JOIN `sys_user_access_role` acc_role ON acc_obj.`UserAccRoleID` = acc_role.`UserAccRoleID`
            WHERE
                a.BranchDistrictID = ? AND
                a.StatusCode = 'active' AND
                acc_role.`UserId` = ?
            ORDER BY b.`BankName` ASC";
            $query = $this->db->query($sql, array($DistrictID,$_SESSION['userid']));
        }

        $return['data'] = $query->result_array();
        return $return;
    }

    public function getObjIdBankAll(){
        $sql="SELECT
                CONCAT('bank-',a.`BranchID`) AS id,
                CONCAT('[Role Bank] ',b.`BankName`,' - ',a.`BranchName`) AS label
            FROM
                ktv_bank_branch a
                LEFT JOIN ktv_bank b ON a.`BranchBankID` = b.`BankID`
            WHERE
                a.StatusCode = 'active'
            ORDER BY b.`BankName` ASC";
        $query = $this->db->query($sql, array());
        $return['data'] = $query->result_array();
        return $return;
    }

    public function getObjIdAllSelected($RoleCode,$UserId){
        $sql="SELECT
                    CONCAT('".$RoleCode."-',a.ObjID) AS label
                FROM
                    sys_user_access_object a
                    INNER JOIN sys_user_access_role b ON a.`UserAccRoleID` = b.`UserAccRoleID`
                WHERE
                    a.ObjType = ? AND
                    b.`UserId` = ?";
        $query = $this->db->query($sql, array($RoleCode,$UserId));
        $data = $query->result_array();

        $arrReturn = array();
        foreach ($data as $key => $value) {
            $arrReturn[] = $value['label'];
        }

        return $arrReturn;
    }

    public function getObjIdCoop($DistrictID){
        if($_SESSION['is_admin'] == "1"){
            $sql="SELECT
                    a.`CoopID` AS id,
                    CONCAT(a.`Status`,' - ',a.`CoopName`) AS label
                FROM
                    ktv_cooperatives a
                    LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                    LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
                WHERE
                    kd.DistrictID = ? AND
                    a.`StatusCode` = 'active'
                ORDER BY a.`Status`, a.`CoopName`";
            $query = $this->db->query($sql, array($DistrictID));
        }else{
            $sql="SELECT
                    a.`CoopID` AS id,
                    CONCAT(a.`Status`,' - ',a.`CoopName`) AS label
                FROM
                    ktv_cooperatives a
                    INNER JOIN `sys_user_access_object` acc_obj ON a.`CoopID` = acc_obj.`ObjID` AND acc_obj.`ObjType` = 'cooperative'
                    INNER JOIN `sys_user_access_role` acc_role ON acc_obj.`UserAccRoleID` = acc_role.`UserAccRoleID`
                    LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                    LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
                WHERE
                    kd.DistrictID = ? AND
                    a.`StatusCode` = 'active' AND
                    acc_role.`UserId` = ?
                ORDER BY a.`Status`, a.`CoopName`";
            $query = $this->db->query($sql, array($DistrictID,$_SESSION['userid']));
        }

        $return['data'] = $query->result_array();
        return $return;
    }

    public function getObjIdCoopAll(){
        $sql="SELECT
                    CONCAT('cooperative-',a.`CoopID`) AS id,
                    CONCAT('[Role Coop] ',a.`Status`,' - ',a.`CoopName`) AS label
                FROM
                    ktv_cooperatives a
                WHERE
                    a.`StatusCode` = 'active'
                ORDER BY a.`Status`, a.`CoopName`";
        $query = $this->db->query($sql, array());
        $return['data'] = $query->result_array();
        return $return;
    }

    public function getObjIdCpg($DistrictID){
        if($_SESSION['is_admin'] == "1"){
            $sql="SELECT
                    a.`CPGid` AS id,
                    CONCAT(a.`CPGid`,' - ',a.`GroupName`) AS label
                FROM
                    ktv_cpg a
                    LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                    LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
                WHERE
                    kd.DistrictID = ? AND
                    a.`Status` = 'active'
                ORDER BY a.`CPGid` ASC";
            $query = $this->db->query($sql, array($DistrictID));
        }else{
            $sql="SELECT
                    a.`CPGid` AS id,
                    CONCAT(a.`CPGid`,' - ',a.`GroupName`) AS label
                FROM
                    ktv_cpg a
                    INNER JOIN `sys_user_access_object` acc_obj ON a.`CPGid` = acc_obj.`ObjID` AND acc_obj.`ObjType` = 'farmergroup'
                    INNER JOIN `sys_user_access_role` acc_role ON acc_obj.`UserAccRoleID` = acc_role.`UserAccRoleID`
                    LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                    LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
                WHERE
                    kd.DistrictID = ? AND
                    a.`Status` = 'active' AND
                    acc_role.`UserId` = ?
                ORDER BY a.`CPGid` ASC";
            $query = $this->db->query($sql, array($DistrictID,$_SESSION['userId']));
        }

        $return['data'] = $query->result_array();
        return $return;
    }

    public function getObjIdCpgAll(){
        $sql="SELECT
                    CONCAT('farmergroup-',a.`CPGid`) AS id,
                    CONCAT('[Role CPG] ',a.`CPGid`,' - ',a.`GroupName`) AS label
                FROM
                    ktv_cpg a
                WHERE
                    a.`Status` = 'active'
                ORDER BY a.`CPGid` ASC";
        $query = $this->db->query($sql, array());
        $return['data'] = $query->result_array();
        return $return;
    }

    public function getObjIdExtension(){
        if($_SESSION['is_admin'] == "1"){
            $sql="SELECT
                    a.`InstiId` AS id,
                    a.`InstiName` AS label
                FROM
                    ktv_ref_institution a
                WHERE
                    a.`StatusCode` = 'active'
                ORDER BY a.`InstiName` ASC";
            $query = $this->db->query($sql, array());
        }else{
            $sql="SELECT
                    a.`InstiId` AS id,
                    a.`InstiName` AS label
                FROM
                    ktv_ref_institution a
                    INNER JOIN `sys_user_access_object` acc_obj ON a.`InstiId` = acc_obj.`ObjID` AND acc_obj.`ObjType` = 'extension'
                    INNER JOIN `sys_user_access_role` acc_role ON acc_obj.`UserAccRoleID` = acc_role.`UserAccRoleID`
                WHERE
                    a.`StatusCode` = 'active' AND
                    acc_role.`UserId` = ?
                ORDER BY a.`InstiName` ASC";
            $query = $this->db->query($sql, array($_SESSION['userid']));
        }

        $return['data'] = $query->result_array();
        return $return;
    }

    public function getObjIdExtensionAll(){
        $sql="SELECT
                    CONCAT('extension-',a.`InstiId`) AS id,
                    CONCAT('[Role Extension] ',a.`InstiName`) AS label
                FROM
                    ktv_ref_institution a
                WHERE
                    a.`StatusCode` = 'active'
                ORDER BY a.`InstiName` ASC";
        $query = $this->db->query($sql, array());
        $return['data'] = $query->result_array();
        return $return;
    }

    public function getObjIdPrivate(){
        $sql="SELECT
                a.`PartnerID` AS id,
                a.`PartnerName` AS label
            FROM
                ktv_program_partner a
            WHERE
                a.`StatusCode` = 'active'
            ORDER BY a.`PartnerName` ASC";
        $query = $this->db->query($sql, array());

        $return['data'] = $query->result_array();
        return $return;
    }

    public function getObjIdProgram(){
        $sql="SELECT
                a.`PartnerID` AS id,
                a.`PartnerName` AS label
            FROM
                ktv_program_partner a
            WHERE
                a.`StatusCode` = 'active'
            ORDER BY a.`PartnerName` ASC";
        $query = $this->db->query($sql, array());

        $return['data'] = $query->result_array();
        return $return;
    }

    public function getObjIdService(){
        $sql="SELECT
                a.`ServiceProvID` AS id
                , a.`ServiceProvName` AS label
            FROM
                ktv_service_provider a
            WHERE
                a.StatusCode = 'active'
            ORDER BY a.`ServiceProvName` ASC";
        $query = $this->db->query($sql, array());

        $return['data'] = $query->result_array();
        return $return;
    }

    public function getObjIdPrivateAll(){
        $sql="SELECT
                    CONCAT('private-',a.`PartnerID`) AS id,
                    CONCAT('[Role Private] ',a.`PartnerName`) AS label
                FROM
                    ktv_program_partner a
                WHERE
                    a.`StatusCode` = 'active'
                ORDER BY a.`PartnerName` ASC";
        $query = $this->db->query($sql, array());
        $return['data'] = $query->result_array();
        return $return;
    }

    public function getObjIdProgramAll(){
        $sql="SELECT
                    CONCAT('program-',a.`PartnerID`) AS id,
                    CONCAT('[Role Program] ',a.`PartnerName`) AS label
                FROM
                    ktv_program_partner a
                WHERE
                    a.`StatusCode` = 'active'
                ORDER BY a.`PartnerName` ASC";
        $query = $this->db->query($sql, array());
        $return['data'] = $query->result_array();
        return $return;
    }

    public function getObjIdSce($DistrictID){
        if($_SESSION['is_admin'] == "1"){
            $sql="SELECT
                    a.`SceID` AS id,
                    CONCAT(b.`FarmerID`,' - ',b.`FarmerName`) AS label
                FROM
                    sce_farmer a
                    INNER JOIN ktv_farmer b ON a.`FarmerID` = b.`FarmerID`
                    LEFT JOIN ktv_village kv ON kv.VillageID = b.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                    LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
                WHERE
                    a.`StatusCode` = 'active' AND
                    kd.DistrictID = ?
                ORDER BY b.`FarmerID` ASC";
            $query = $this->db->query($sql, array($DistrictID));
        }else{
            $sql="SELECT
                    a.`SceID` AS id,
                    CONCAT(b.`FarmerID`,' - ',b.`FarmerName`) AS label
                FROM
                    sce_farmer a
                    INNER JOIN ktv_farmer b ON a.`FarmerID` = b.`FarmerID`
                    INNER JOIN `sys_user_access_object` acc_obj ON a.`SceID` = acc_obj.`ObjID` AND acc_obj.`ObjType` = 'sce'
                    INNER JOIN `sys_user_access_role` acc_role ON acc_obj.`UserAccRoleID` = acc_role.`UserAccRoleID`
                    LEFT JOIN ktv_village kv ON kv.VillageID = b.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                    LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
                WHERE
                    a.`StatusCode` = 'active' AND
                    kd.DistrictID = ? AND
                    acc_role.`UserId` = ?
                ORDER BY b.`FarmerID` ASC";
            $query = $this->db->query($sql, array($DistrictID,$_SESSION['userid']));
        }

        $return['data'] = $query->result_array();
        return $return;
    }

    public function getObjIdSceAll(){
        $sql="SELECT
                    CONCAT('sce-',a.`SceID`) AS id,
                    CONCAT('[Role SCE] ',b.`FarmerID`,' - ',b.`FarmerName`) AS label
                FROM
                    sce_farmer a
                    INNER JOIN ktv_farmer b ON a.`FarmerID` = b.`FarmerID`
                WHERE
                    a.`StatusCode` = 'active'
                ORDER BY b.`FarmerID` ASC";
        $query = $this->db->query($sql, array());
        $return['data'] = $query->result_array();
        return $return;
    }

    public function getObjIdTrader($DistrictID){
        if($_SESSION['is_admin'] == "1"){
            /*
            $sql="SELECT
                a.`TraderID` AS id,
                a.`TraderName` AS label
            FROM
                ktv_traders a
            WHERE
                a.`StatusCode` = 'active' AND
                SUBSTR(a.`VillageID`,1,4) = ?
            ORDER BY a.`TraderName` ASC";
            */
            $sql="SELECT
                    a.`MemberDisplayID` AS id
                    , a.`MemberName` AS label
                FROM
                    ktv_members a
                    INNER JOIN ktv_member_role b ON a.`MemberID` = b.`MemberID`
                    LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                    LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
                WHERE
                    b.`MRoleID` = '5'
                    AND a.`StatusCode` = 'active'
                    AND kd.DistrictID = ?
                ORDER BY a.`MemberName` ASC";
            $query = $this->db->query($sql, array($DistrictID));
        }else{
            /*
            $sql="SELECT
                a.`TraderID` AS id,
                a.`TraderName` AS label
            FROM
                ktv_traders a
                INNER JOIN `sys_user_access_object` acc_obj ON a.`TraderID` = acc_obj.`ObjID` AND acc_obj.`ObjType` = 'trader'
                INNER JOIN `sys_user_access_role` acc_role ON acc_obj.`UserAccRoleID` = acc_role.`UserAccRoleID`
            WHERE
                a.`StatusCode` = 'active' AND
                SUBSTR(a.`VillageID`,1,4) = ? AND
                acc_role.`UserId` = ?
            ORDER BY a.`TraderName` ASC";
            */

            $sql="SELECT
                a.`MemberDisplayID` AS id
                , a.`MemberName` AS label
            FROM
                ktv_members a
                INNER JOIN ktv_member_role b ON a.`MemberID` = b.`MemberID`
                INNER JOIN `sys_user_access_object` acc_obj ON a.`MemberID` = acc_obj.`ObjID` AND acc_obj.`ObjType` = 'trader'
                INNER JOIN `sys_user_access_role` acc_role ON acc_obj.`UserAccRoleID` = acc_role.`UserAccRoleID`
                LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
            WHERE
                a.`StatusCode` = 'active' AND
                kd.DistrictID = ? AND
                acc_role.`UserId` = ?
            ORDER BY a.`MemberName` ASC";

            $query = $this->db->query($sql, array($DistrictID,$_SESSION['userid']));
        }

        $return['data'] = $query->result_array();
        return $return;
    }

    public function getObjIdTraderAll(){
        /*
        $sql="SELECT
                CONCAT('trader-',a.`TraderID`) AS id,
                CONCAT('[Role Trader] ',a.`TraderName`) AS label
            FROM
                ktv_traders a
            WHERE
                a.`StatusCode` = 'active'
            ORDER BY a.`TraderName` ASC";
        */

        $sql="SELECT
                a.`MemberDisplayID` AS id
                , a.`MemberName` AS label
            FROM
                ktv_members a
                INNER JOIN ktv_member_role b ON a.`MemberID` = b.`MemberID`
            WHERE
                b.`MRoleID` = '5'
                AND a.`StatusCode` = 'active'
            ORDER BY a.`MemberName` ASC";

        $query = $this->db->query($sql, array());
        $return['data'] = $query->result_array();
        return $return;
    }

    public function getObjIdWarehouse($DistrictID){
        if($_SESSION['is_admin'] == "1"){
            $sql="SELECT
                a.`WarehouseID` AS id,
                a.`WarehouseName` AS label
            FROM
                ktv_warehouse a
                LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
            WHERE
                a.`StatusCode` = 'active' AND
                kd.DistrictID = ?
            ORDER BY a.`WarehouseName` ASC";
            $query = $this->db->query($sql, array($DistrictID));
        }else{
            $sql="SELECT
                a.`WarehouseID` AS id,
                a.`WarehouseName` AS label
            FROM
                ktv_warehouse a
                INNER JOIN `sys_user_access_object` acc_obj ON a.`WarehouseID` = acc_obj.`ObjID` AND acc_obj.`ObjType` = 'warehouse'
                INNER JOIN `sys_user_access_role` acc_role ON acc_obj.`UserAccRoleID` = acc_role.`UserAccRoleID`
                LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
            WHERE
                a.`StatusCode` = 'active' AND
                kd.DistrictID = ? AND
                acc_role.`UserId` = ?
            ORDER BY a.`WarehouseName` ASC";
            $query = $this->db->query($sql, array($DistrictID,$_SESSION['userid']));
        }

        $return['data'] = $query->result_array();
        return $return;
    }

    public function getObjIdWarehouseAll(){
        $sql="SELECT
                CONCAT('warehouse-',a.`WarehouseID`) AS id,
                CONCAT('[Role Warehouse] ',a.`WarehouseName`) AS label
            FROM
                ktv_warehouse a
            WHERE
                a.`StatusCode` = 'active'
            ORDER BY a.`WarehouseName` ASC";
        $query = $this->db->query($sql, array());
        $return['data'] = $query->result_array();
        return $return;
    }

    public function getObjIdMill(){
        $sql="SELECT
                a.`MillID` AS id
                , a.`MillName` AS label
            FROM
                ktv_mill a
            WHERE
                a.`StatusCode` = 'active'
            ORDER BY a.`MillName` ASC";

        $query = $this->db->query($sql, array());

        $return['data'] = $query->result_array();
        return $return;
    }

    public function getObjIdRefinery(){
        $sql="SELECT
                a.`RefineryID` AS id
                , a.`RefineryName` AS label
            FROM
                ktv_refinery a
            WHERE
                a.`StatusCode` = 'active'
            ORDER BY a.`RefineryID` ASC";
    
        $query = $this->db->query($sql, array());

        $return['data'] = $query->result_array();
        return $return;
    }

    public function getObjIdAgent($DistrictID){
        $sql="SELECT
                a.MemberID AS id
                , CONCAT(a.`MemberDisplayID`,' - ',a.`MemberName`,' (',GROUP_CONCAT(rrole.MRoleName SEPARATOR ', '),')') AS label
            FROM
                ktv_members a
                INNER JOIN (
                    SELECT
                        sub_a.MemberID
                    FROM
                        ktv_members sub_a
                        LEFT JOIN ktv_member_role sub_b ON sub_a.MemberID = sub_b.MemberID
                    WHERE
                        sub_a.StatusCode = 'active'
                        AND sub_b.`MRoleID` IN (5,6,7,8,9,10)
                    GROUP BY sub_a.MemberID
                ) AS tmp_member_filter ON a.MemberID = tmp_member_filter.MemberID
                LEFT JOIN ktv_member_role mrole ON a.MemberID = mrole.MemberID
                LEFT JOIN ktv_ref_member_role rrole ON mrole.MRoleID = rrole.MRoleID
                LEFT JOIN ktv_village v ON v.VillageID = a.VillageID
                LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
            WHERE
                a.`StatusCode` = 'active'
                AND sd.DistrictID = ?
            GROUP BY a.MemberID
            ORDER BY a.`MemberName` ASC";
        $query = $this->db->query($sql, array($DistrictID));

        $return['data'] = $query->result_array();
        return $return;
    }

    public function getPosition($ObjType){
        $sql="SELECT
                a.`PositionID` AS id,
                CONCAT( a.`PositionCode`, ' - ', a.`PositionName` ) AS label 
            FROM
                ktv_ref_position_type a
                INNER JOIN sys_role b ON a.`PositionRoleId` = b.`RoleId` 
            WHERE
                b.`RoleCode` = ?
                AND a.StatusCode = 'active' 
            ORDER BY
                a.`PositionID` ASC";
        $query = $this->db->query($sql, array($ObjType));

        $return['data'] = $query->result_array();
        return $return;
    }

    public function getFormStaff($StaffID){
        $sql="SELECT
                b.`StaffID`,
                a.`PersonNm`,
                a.`Ssn`,
                a.`BirthDate`,
                a.`BirthPlace`,
                a.`Gender`,
                a.`MaritalSt`,
                a.`NationalityNm`,
                a.`Photo` AS Photo_old,
                a.`Address`,
                a.`ProvinceID`,
                a.`SubDistrictID`,
                a.`DistrictID`,
                a.`VillageID`,
                b.StaffRegisteredNumber,
                b.`OldStaffID`,
                IF(c.`FarmerID` IS NULL,'2','1') AS isFarmerValue,
                d.`FarmerID`,
                d.`CPGid` AS FarmerCpgID,
                SUBSTR(d.`CPGid`,1,4) AS FarmerDistrictID,
                SUBSTR(d.`CPGid`,1,2) AS FarmerProvinceID,
                b.`WorkAreaID`,
                e.`ProvinceID` AS WorkAreaProvinceID,
                a.`PrivateCellPhone`,
                a.`OfficialCellPhone`,
                a.`PrivateEmail`,
                a.`OfficialEmail`,
                b.`CcEmail`,
                b.`WorkPeriod`,
                b.`MillID`,
                b.`SmeID`,
                b.`RefineryID`,
                b.`StatusCode`,
                b.`ObjType` AS ObjTypeValue,
                b.`ObjID` AS ObjIDValue,
                CASE b.`ObjType`
                    WHEN 'cooperative'
                        THEN
                            (SELECT
                                d.ProvinceID
                            FROM
                                ktv_cooperatives role_coop
                                LEFT JOIN ktv_village v ON v.`VillageID` = role_coop.`VillageID`
                                LEFT JOIN ktv_subdistrict sd ON sd.`SubDistrictID` = v.`SubDistrictID`
                                LEFT JOIN ktv_district d ON d.`DistrictID` = sd.`DistrictID`
                            WHERE
                                role_coop.CoopID = b.`ObjID`
                            LIMIT 1)
                    WHEN 'farmergroup'
                        THEN
                            (SELECT
                                d.ProvinceID
                            FROM
                                ktv_cpg role_cpg
                                LEFT JOIN ktv_village v ON v.`VillageID` = role_cpg.`VillageID`
                                LEFT JOIN ktv_subdistrict sd ON sd.`SubDistrictID` = v.`SubDistrictID`
                                LEFT JOIN ktv_district d ON d.`DistrictID` = sd.`DistrictID`
                            WHERE
                                role_cpg.CPGid = b.`ObjID`
                            LIMIT 1)
                    WHEN 'sce'
                        THEN
                            (SELECT
                                SUBSTR(role_sce.FarmerID,1,4)
                            FROM
                                sce_farmer role_sce
                            WHERE
                                role_sce.SceID = b.`ObjID`
                            LIMIT 1)
                    WHEN 'trader'
                        THEN
                            (SELECT
                                d.ProvinceID
                            FROM
                                ktv_traders role_trader
                                LEFT JOIN ktv_village v ON v.`VillageID` = role_trader.`VillageID`
                                LEFT JOIN ktv_subdistrict sd ON sd.`SubDistrictID` = v.`SubDistrictID`
                                LEFT JOIN ktv_district d ON d.`DistrictID` = sd.`DistrictID`
                            WHERE
                                role_trader.TraderID = b.`ObjID`
                            LIMIT 1)
                    WHEN 'warehouse'
                        THEN
                            (SELECT
                                d.ProvinceID
                            FROM
                                ktv_warehouse role_ware
                                LEFT JOIN ktv_village v ON v.`VillageID` = role_ware.`VillageID`
                                LEFT JOIN ktv_subdistrict sd ON sd.`SubDistrictID` = v.`SubDistrictID`
                                LEFT JOIN ktv_district d ON d.`DistrictID` = sd.`DistrictID`
                            WHERE
                                role_ware.WarehouseID = b.`ObjID`
                            LIMIT 1)
                    WHEN 'agent'
                        THEN
                            (SELECT
                                d.ProvinceID
                            FROM
                                ktv_members role_members
                                LEFT JOIN ktv_village v ON v.`VillageID` = role_members.`VillageID`
                                LEFT JOIN ktv_subdistrict sd ON sd.`SubDistrictID` = v.`SubDistrictID`
                                LEFT JOIN ktv_district d ON d.`DistrictID` = sd.`DistrictID`
                            WHERE
                                role_members.MemberID = b.`ObjID`
                            LIMIT 1)
                END AS RoleProvinceID,
                CASE b.`ObjType`
                    WHEN 'cooperative'
                        THEN
                            (SELECT
                                sd.DistrictID
                            FROM
                                ktv_cooperatives role_coop
                                LEFT JOIN ktv_village v ON v.`VillageID` = role_coop.`VillageID`
                                LEFT JOIN ktv_subdistrict sd ON sd.`SubDistrictID` = v.`SubDistrictID`
                            WHERE
                                role_coop.CoopID = b.`ObjID`
                            LIMIT 1)
                    WHEN 'farmergroup'
                        THEN
                            (SELECT
                                sd.DistrictID
                            FROM
                                ktv_cpg role_cpg
                                LEFT JOIN ktv_village v ON v.`VillageID` = role_cpg.`VillageID`
                                LEFT JOIN ktv_subdistrict sd ON sd.`SubDistrictID` = v.`SubDistrictID`
                            WHERE
                                role_cpg.CPGid = b.`ObjID`
                            LIMIT 1)
                    WHEN 'sce'
                        THEN
                            (SELECT
                                SUBSTR(role_sce.FarmerID,1,4)
                            FROM
                                sce_farmer role_sce
                            WHERE
                                role_sce.SceID = b.`ObjID`
                            LIMIT 1)
                    WHEN 'trader'
                        THEN
                            (SELECT
                                sd.DistrictID
                            FROM
                                ktv_traders role_trader
                                LEFT JOIN ktv_village v ON v.`VillageID` = role_trader.`VillageID`
                                LEFT JOIN ktv_subdistrict sd ON sd.`SubDistrictID` = v.`SubDistrictID`
                            WHERE
                                role_trader.TraderID = b.`ObjID`
                            LIMIT 1)
                    WHEN 'warehouse'
                        THEN
                            (SELECT
                                sd.DistrictID
                            FROM
                                ktv_warehouse role_ware
                                LEFT JOIN ktv_village v ON v.`VillageID` = role_ware.`VillageID`
                                LEFT JOIN ktv_subdistrict sd ON sd.`SubDistrictID` = v.`SubDistrictID`
                            WHERE
                                role_ware.WarehouseID = b.`ObjID`
                            LIMIT 1)
                    WHEN 'agent'
                        THEN
                            (SELECT
                                sd.DistrictID
                            FROM
                                ktv_members role_members
                                LEFT JOIN ktv_village v ON v.`VillageID` = role_members.`VillageID`
                                LEFT JOIN ktv_subdistrict sd ON sd.`SubDistrictID` = v.`SubDistrictID`
                            WHERE
                                role_members.MemberID = b.`ObjID`
                            LIMIT 1)
                END AS RoleDistrictID,
                f.`StaffPosPositionID` AS PositionID
                , g.Username AS userInfoUsername
                , IF(g.UserActive='Yes','Active',IF(g.UserActive='No','Inactive','-')) AS userInfoStatus
                , i.GroupName AS userInfoDefaultGroup
                , g.UserId AS userInfoUserId
            FROM
                ktv_persons a
                INNER JOIN ktv_staffs b ON a.`PersonID` = b.`PersonID`
                LEFT JOIN ktv_person_farmers c ON a.`PersonID` = c.`PersonID`
                LEFT JOIN ktv_farmer d ON c.`FarmerID` = d.`FarmerID`
                LEFT JOIN ktv_ref_work_area e ON b.`WorkAreaID` = e.`WorkAreaID`
                LEFT JOIN ktv_staff_positions f ON b.`StaffID` = f.`StaffPosStaffID`
                    AND CURDATE() BETWEEN f.`StaffPostStart` AND f.`StaffPostEnd`
                    AND f.`StatusCode` = 'active'
                LEFT JOIN sys_user g ON a.UserId = g.UserId
                LEFT JOIN sys_user_group h ON h.UserGroupUserId = g.UserId AND UserGroupIsDefault = '1'
                LEFT JOIN sys_group i ON h.UserGroupGroupId = i.GroupId
            WHERE
                b.`StaffID` = ?
            LIMIT 1";
        $query = $this->db->query($sql, array($StaffID));
        $data = $query->result_array();

        $return['success'] = true;
        $return['data'] = $data[0];
        return $return;
    }

    public function getUserInfoGroupUser($UserId){
        $sql="SELECT
                b.`GroupName` AS nama
                , b.`GroupDescription` AS deskripsi
                , c.`UnitName` AS unit
            FROM
                sys_user_group a
                LEFT JOIN sys_group b ON a.`UserGroupGroupId` = b.`GroupId`
                LEFT JOIN sys_unit c ON b.`GroupUnitId` = c.`UnitId`
            WHERE
                a.`UserGroupUserId` = ?
            ORDER BY a.`UserGroupIsDefault` DESC";
        $query = $this->db->query($sql,array($UserId));
        $data = $query->result_array();

        $return['data'] = $data;
        return $return;
    }

    public function getUserInfoDistrictAccess($UserId){
        $sql="SELECT
                c.`Province` AS provinsi
                , b.`District` AS kabupaten
            FROM
                ktv_access_staff a
                LEFT JOIN ktv_district b ON a.`DistrictID` = b.DistrictID
                LEFT JOIN ktv_province c ON b.`ProvinceID` = c.`ProvinceID`
            WHERE
                a.`UserId` = ?
            ORDER BY c.`Province`, b.`District`";
        $query = $this->db->query($sql,array($UserId));
        $data = $query->result_array();

        $return['data'] = $data;
        return $return;
    }

    public function insertStaff($post){
        $this->db->trans_begin();

        //prep var
        if($post['DistrictID'] == "") $post['DistrictID'] = '0';

        //insert ktv_persons
        $sql="INSERT INTO `ktv_persons` SET
                UserID = NULL,
               `WorkAreaID` = ?,
               `Ssn` = IF(?='',NULL,?),
               `EmpNr` = IF(?='',NULL,?),
               `PersonNm` = ?,
               `BirthDate` = IF(?='',NULL,?),
               `BirthPlace` = IF(?='',NULL,?),
               `Photo` = IF(?='',NULL,?),
               `Gender` = ?,
               `Address` = IF(?='',NULL,?),
               `ProvinceID` = IF(?='',NULL,?),
               `DistrictID` = IF(?='',NULL,?),
               `SubDistrictID` = IF(?='',NULL,?),
               `VillageID` = IF(?='',NULL,?),
               `Email` = ?,
               `MaritalSt` = ?,
               `NationalityNm` = ?,
               `StatusCd` = ?,
               `PrivateCellPhone` = IF(?='',NULL,?),
               `OfficialCellPhone` = IF(?='',NULL,?),
               `PrivateEmail` = IF(?='',NULL,?),
               `OfficialEmail` = IF(?='',NULL,?),
               `DateCreated` = NOW(),
               `CreatedBy` = ?";
        $p = array(
            $post['WorkAreaID'],
            $post['Ssn'],$post['Ssn'],
            $post['StaffRegisteredNumber'],$post['StaffRegisteredNumber'],
            $post['PersonNm'],
            $post['BirthDate'],$post['BirthDate'],
            $post['BirthPlace'],$post['BirthPlace'],
            $post['Photo_old'],$post['Photo_old'],
            $post['Gender'],
            $post['Address'],$post['Address'],
            $post['ProvinceID'],$post['ProvinceID'],
            $post['DistrictID'],$post['DistrictID'],
            $post['SubDistrictID'],$post['SubDistrictID'],
            $post['VillageID'],$post['VillageID'],
            $post['OfficialEmail'],
            $post['MaritalSt'],
            $post['NationalityNm'],
            $post['StatusCode'],
            $post['PrivateCellPhone'],$post['PrivateCellPhone'],
            $post['OfficialCellPhone'],$post['OfficialCellPhone'],
            $post['PrivateEmail'],$post['PrivateEmail'],
            $post['OfficialEmail'],$post['OfficialEmail'],
            $_SESSION['userid']
        );
        $query = $this->db->query($sql, $p);
        $PersonID = $this->db->insert_id();

        //insert ktv_staffs
        $sql="INSERT INTO `ktv_staffs` SET
               `OldStaffID` = IF(?='',NULL,?),
               `PersonID` = ?,
               `ObjType` = ?,
               `ObjID` = ?,
               `StaffRegisteredNumber` = IF(?='',NULL,?),
               `OfficialPhone` = IF(?='',NULL,?),
               `PrivatePhone` = IF(?='',NULL,?),
               `WorkPhone` = IF(?='',NULL,?),
               `OfficialEmail` = IF(?='',NULL,?),
               `PrivateEmail` = IF(?='',NULL,?),
               `CcEmail` = IF(?='Comma separated value',NULL,?),
               `WorkAreaID` = ?,
               `WorkPeriod` = ?,
               `MillID` = ?,
               `SmeID` = ?,
               `RefineryID` = ?,
               `StatusCode` = ?,
               `DateCreated` = NOW(),
               `CreatedBy` = ?";
        $p = array(
            $post['OldStaffID'],$post['OldStaffID'],
            $PersonID,
            $post['ObjType'],
            $post['ObjID'],
            $post['StaffRegisteredNumber'],$post['StaffRegisteredNumber'],
            $post['OfficialCellPhone'],$post['OfficialCellPhone'],
            $post['PrivateCellPhone'],$post['PrivateCellPhone'],
            $post['OfficialCellPhone'],$post['OfficialCellPhone'],
            $post['OfficialEmail'],$post['OfficialEmail'],
            $post['PrivateEmail'],$post['PrivateEmail'],
            $post['CcEmail'],$post['CcEmail'],
            $post['WorkAreaID'],
            $post['WorkPeriod'],
            $post['MillID'],
            $post['SmeID'],
            $post['RefineryID'],
            $post['StatusCode'],
            $_SESSION['userid']
        );
        $query = $this->db->query($sql, $p);
        $StaffID = $this->db->insert_id();

        if($post['isFarmer'] == "1"){
            //insert ke ktv_person_farmers
            $sql="INSERT INTO `ktv_person_farmers` SET
                   `PersonID` = ?,
                   `FarmerID` = ?";
            $p = array(
                $PersonID,
                $post['FarmerID']
            );
            $query = $this->db->query($sql, $p);
        }

        //insert ke ktv_staff_positions
        $sql="INSERT INTO `ktv_staff_positions` SET
               `StaffPosStaffID` = ?,
               `StaffPosPositionID` = ?,
               `StaffPostStart` = CURDATE(),
               `StaffPostEnd` = '9999-12-31',
               `StatusCode` = 'active',
               `DateCreated` = NOW(),
               `CreatedBy` = ?";
        $p = array(
            $StaffID,
            $post['PositionID'],
            $_SESSION['userid']
        );
        $query = $this->db->query($sql, $p);

        //================= insert ke 9 tabel staff (begin) ===========================//
        switch ($post['ObjType']) {
            case 'bank':
                if($post['Gender'] == "m") $bankGender = '1'; else $bankGender = '2';
                $sql="INSERT INTO `ktv_bank_branch_staff` SET
                       `BranchID` = ?,
                       `PersonID` = ?,
                       `StaffName` = ?,
                       `Phone` = ?,
                       `Email` = ?,
                       `StaffBirth` = IF(?='',NULL,?),
                       `StaffGender` = IF(?='',NULL,?),
                       `IdentityNumber` = ?,
                       `VillageID` = IF(?='',NULL,?),
                       `Address` = IF(?='',NULL,?),
                       `PositionID` = ?,
                       `DateCreated` = NOW(),
                       `CreatedBy` = ?";
                $p = array(
                    $post['ObjID'],
                    $PersonID,
                    $post['PersonNm'],
                    $post['OfficialCellPhone'],
                    $post['OfficialEmail'],
                    $post['BirthDate'],$post['BirthDate'],
                    $bankGender,$bankGender,
                    $post['Ssn'],
                    $post['VillageID'],$post['VillageID'],
                    $post['Address'],$post['Address'],
                    $post['PositionID'],
                    $_SESSION['userid']
                );
                $query = $this->db->query($sql, $p);
            break;
            case 'cooperative':
                if($post['Gender'] == "m") $coopGender = '1'; else $coopGender = '2';
                if($post['WorkPeriod'] == "Full-time") $coopStatusKerja = 'Full-Time'; else $coopStatusKerja = 'Part-Time';
                $sql="INSERT INTO `ktv_cooperative_staff` SET
                       `CoopID` = ?,
                       `PersonID` = ?,
                       `FarmerID` = IF(?='',NULL,?),
                       `StaffName` = ?,
                       `Position` = NULL,
                       `Phone` = ?,
                       `Email` = ?,
                       `StaffBirthday` = ?,
                       `StaffGender` = ?,
                       `StaffStatus` = ?,
                       `PaymentStatus` = NULL,
                       `CreatedBy` = ?,
                       `DateCreated` = NOW()
                    ";
                $p = array(
                    $post['ObjID'],
                    $PersonID,
                    $post['FarmerID'],$post['FarmerID'],
                    $post['PersonNm'],
                    $post['OfficialCellPhone'],
                    $post['OfficialEmail'],
                    $post['BirthDate'],
                    $coopGender,
                    $coopStatusKerja,
                    $_SESSION['userid']
                );
                $query = $this->db->query($sql, $p);
            break;
            case 'farmergroup':
                if($post['Gender'] == "m") $cpgGender = '1'; else $cpgGender = '2';
                $sql="INSERT INTO `ktv_cpg_staff` SET
                       `CPGid` = ?,
                       `PersonID` = ?,
                       `StaffName` = ?,
                       `FarmerID` = IF(?='',NULL,?),
                       `Phone` = ?,
                       `Email` = ?,
                       `StaffBirthday` = ?,
                       `StaffGender` = ?,
                       `DateCreated` = NOW(),
                       `CreatedBy` = ?";
                $p = array(
                    $post['ObjID'],
                    $PersonID,
                    $post['PersonNm'],
                    $post['FarmerID'],$post['FarmerID'],
                    $post['OfficialCellPhone'],
                    $post['OfficialEmail'],
                    $post['BirthDate'],
                    $cpgGender,
                    $_SESSION['userid']
                );
                $query = $this->db->query($sql, $p);
            break;
            case 'extension':
                $sql="INSERT INTO `ktv_extension_staff` SET
                       `PersonID` = ?,
                       `KTP` = ?,
                       `StaffName` = ?,
                       `Address` = IF(?='',NULL,?),
                       `Gender` = ?,
                       `BirthDttm` = ?,
                       `VillageID` = IF(?='',0,?),
                       `BirthPlace` = IF(?='',NULL,?),
                       `MaritalSt` = ?,
                       `Handphone` = ?,
                       `Email` = ?,
                       `DateCreated` = NOW(),
                       `CreatedBy` = ?";
                $p = array(
                    $PersonID,
                    $post['Ssn'],
                    $post['PersonNm'],
                    $post['Address'],$post['Address'],
                    $post['Gender'],
                    $post['BirthDate'],
                    $post['VillageID'],$post['VillageID'],
                    $post['BirthPlace'],$post['BirthPlace'],
                    $post['MaritalSt'],
                    $post['OfficialCellPhone'],
                    $post['OfficialEmail'],
                    $_SESSION['userid']
                );
                $query = $this->db->query($sql, $p);
            break;
            case 'private':
                if($post['Gender'] == "m") $privateGender = '1'; else $privateGender = '2';
                $sql="INSERT INTO `ktv_private_staff` SET
                       `PersonID` = ?,
                       `PartnerID` = ?,
                       `StaffName` = ?,
                       `PrivateCellphone` = IF(?='',NULL,?),
                       `OfficialCellphone` = IF(?='',NULL,?),
                       `PrivateStaffEmail` = IF(?='',NULL,?),
                       `OfficialStaffEmail` = IF(?='',NULL,?),
                       `StaffBirth` = ?,
                       `StaffGender` = ?,
                       `Location` = ?,
                       `DateCreated` = NOW(),
                       `CreatedBy` = ?";
                $p = array(
                    $PersonID,
                    $post['ObjID'],
                    $post['PersonNm'],
                    $post['PrivateCellPhone'],$post['PrivateCellPhone'],
                    $post['OfficialCellPhone'],$post['OfficialCellPhone'],
                    $post['PrivateEmail'],$post['PrivateEmail'],
                    $post['OfficialEmail'],$post['OfficialEmail'],
                    $post['BirthDate'],
                    $privateGender,
                    $post['DistrictID'],
                    $_SESSION['userid']
                );
                $query = $this->db->query($sql, $p);
            break;
            case 'program':
                $sql="INSERT INTO `ktv_program_staff` SET
                       `PartnerID` = ?,
                       `PersonID` = ?,
                       `Position` = ?,
                       `WorkArea` = IF(?=0,NULL,?),
                       `DateCreated` = NOW(),
                       `CreatedBy` = ?";
                $p = array(
                    $post['ObjID'],
                    $PersonID,
                    $post['PositionID'],
                    $post['DistrictID'],$post['DistrictID'],
                    $_SESSION['userid']
                );
                $query = $this->db->query($sql, $p);
            break;
            case 'sce':
                if($post['Gender'] == "m") $sceGender = '1'; else $sceGender = '2';
                $sql="INSERT INTO `sce_farmer_staff` SET
                       `SceID` = ?,
                       `PersonID` = ?,
                       `StaffName` = ?,
                       `FarmerID` = IF(?='',NULL,?),
                       `Phone` = ?,
                       `Email` = ?,
                       `StaffBirthday` = ?,
                       `StaffGender` = ?,
                       `DateCreated` = NOW(),
                       `CreatedBy` = ?";
                $p = array(
                    $post['ObjID'],
                    $PersonID,
                    $post['PersonNm'],
                    $post['FarmerID'],$post['FarmerID'],
                    $post['OfficialCellPhone'],
                    $post['OfficialEmail'],
                    $post['BirthDate'],
                    $sceGender,
                    $_SESSION['userid']
                );
                $query = $this->db->query($sql, $p);
            break;
            case 'trader':
                if($post['Gender'] == "m") $traderGender = '1'; else $traderGender = '2';
                $sql="INSERT INTO `ktv_trader_staff` SET
                   `TraderID` = ?,
                   `PersonID` = ?,
                   `StaffName` = ?,
                   `PrivateCellphone` = IF(?='',NULL,?),
                   `OfficialCellphone` = IF(?='',NULL,?),
                   `PrivateStaffEmail` = IF(?='',NULL,?),
                   `OfficialStaffEmail` = IF(?='',NULL,?),
                   `StaffBirth` = ?,
                   `StaffGender` = ?,
                   `IdentityNumber` = ?,
                   `VillageID` = IF(?='',NULL,?),
                   `Address` = ?,
                   `DateCreated` = NOW(),
                   `CreatedBy` = ?";
                $p = array(
                    $post['ObjID'],
                    $PersonID,
                    $post['PersonNm'],
                    $post['PrivateCellPhone'],$post['PrivateCellPhone'],
                    $post['OfficialCellPhone'],$post['OfficialCellPhone'],
                    $post['PrivateEmail'],$post['PrivateEmail'],
                    $post['OfficialEmail'],$post['OfficialEmail'],
                    $post['BirthDate'],
                    $traderGender,
                    $post['Ssn'],
                    $post['VillageID'],$post['VillageID'],
                    $post['Address'],
                    $_SESSION['userid']
                );
                $query = $this->db->query($sql, $p);
            break;
            case 'warehouse':
                if($post['Gender'] == "m") $wareGender = '1'; else $wareGender = '2';
                $sql="INSERT INTO `ktv_warehouse_staff` SET
                       `WarehouseID` = ?,
                       `PersonID` = ?,
                       `StaffName` = ?,
                       `Phone` = ?,
                       `Email` = ?,
                       `StaffBirth` = ?,
                       `StaffGender` = ?,
                       `IdentityNumber` = ?,
                       `VillageID` = IF(?='',NULL,?),
                       `Address` = ?,
                       `DateCreated` = NOW(),
                       `CreatedBy` = ?";
                $p = array(
                    $post['ObjID'],
                    $PersonID,
                    $post['PersonNm'],
                    $post['OfficialCellPhone'],
                    $post['OfficialEmail'],
                    $post['BirthDate'],
                    $wareGender,
                    $post['Ssn'],
                    $post['VillageID'],$post['VillageID'],
                    $post['Address'],
                    $_SESSION['userid']
                );
                $query = $this->db->query($sql, $p);
            break;
        }
        //================= insert ke 9 tabel staff (end)   ===========================//

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to save record";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Record saved";
        }

        return $results;
    }

    public function updateStaff($post){
        $this->db->trans_begin();

        //get PersonID
        $sql="SELECT
                    PersonID
                FROM
                    ktv_staffs
                WHERE
                    StaffID = ?
                LIMIT 1";
        $query = $this->db->query($sql, array($post['StaffID']));
        $data = $query->row_array();
        $PersonID = $data['PersonID'];

        //update tabel ktv_persons
        $sql="UPDATE `ktv_persons` SET
               `WorkAreaID` = ?,
               `Ssn` = IF(?='',NULL,?),
               `EmpNr` = IF(?='',NULL,?),
               `PersonNm` = ?,
               `BirthDate` = IF(?='',NULL,?),
               `BirthPlace` = IF(?='',NULL,?),
               `Photo` = IF(?='',NULL,?),
               `Gender` = ?,
               `Address` = IF(?='',NULL,?),
               `ProvinceID` = IF(?='',NULL,?),
               `DistrictID` = IF(?='',NULL,?),
               `SubDistrictID` = IF(?='',NULL,?),
               `VillageID` = IF(?='',NULL,?),
               `Email` = ?,
               `MaritalSt` = ?,
               `NationalityNm` = ?,
               `StatusCd` = ?,
               `PrivateCellPhone` = IF(?='',NULL,?),
               `OfficialCellPhone` = IF(?='',NULL,?),
               `PrivateEmail` = IF(?='',NULL,?),
               `OfficialEmail` = IF(?='',NULL,?),
               `DateUpdated` = NOW(),
               `UpdatedBy` = ?
            WHERE
                PersonID = ?
            LIMIT 1";
        $p = array(
            $post['WorkAreaID'],
            $post['Ssn'],$post['Ssn'],
            $post['StaffRegisteredNumber'],$post['StaffRegisteredNumber'],
            $post['PersonNm'],
            $post['BirthDate'],$post['BirthDate'],
            $post['BirthPlace'],$post['BirthPlace'],
            $post['Photo_old'],$post['Photo_old'],
            $post['Gender'],
            $post['Address'],$post['Address'],
            $post['ProvinceID'],$post['ProvinceID'],
            $post['DistrictID'],$post['DistrictID'],
            $post['SubDistrictID'],$post['SubDistrictID'],
            $post['VillageID'],$post['VillageID'],
            $post['OfficialEmail'],
            $post['MaritalSt'],
            $post['NationalityNm'],
            $post['StatusCode'],
            $post['PrivateCellPhone'],$post['PrivateCellPhone'],
            $post['OfficialCellPhone'],$post['OfficialCellPhone'],
            $post['PrivateEmail'],$post['PrivateEmail'],
            $post['OfficialEmail'],$post['OfficialEmail'],
            $_SESSION['userid'],
            $PersonID
        );
        $query = $this->db->query($sql, $p);

        $sql="UPDATE `ktv_staffs` SET
               `OldStaffID` = IF(?='',NULL,?),
               `ObjID` = ?,
               `StaffRegisteredNumber` = IF(?='',NULL,?),
               `OfficialPhone` = IF(?='',NULL,?),
               `PrivatePhone` = IF(?='',NULL,?),
               `WorkPhone` = IF(?='',NULL,?),
               `OfficialEmail` = IF(?='',NULL,?),
               `PrivateEmail` = IF(?='',NULL,?),
               `CcEmail` = IF(?='Comma separated value',NULL,?),
               `WorkAreaID` = ?,
               `WorkPeriod` = ?,
               `MillID` = ?,
               `SmeID` = ?,
               `RefineryID` = ?,
               `StatusCode` = ?,
               `DateUpdated` = NOW(),
               `LastModifiedBy` = ?
            WHERE
                StaffID = ?
            LIMIT 1";
        $p = array(
            $post['OldStaffID'],$post['OldStaffID'],
            $post['ObjID'],
            $post['StaffRegisteredNumber'],$post['StaffRegisteredNumber'],
            $post['OfficialCellPhone'],$post['OfficialCellPhone'],
            $post['PrivateCellPhone'],$post['PrivateCellPhone'],
            $post['OfficialCellPhone'],$post['OfficialCellPhone'],
            $post['OfficialEmail'],$post['OfficialEmail'],
            $post['PrivateEmail'],$post['PrivateEmail'],
            $post['CcEmail'],$post['CcEmail'],
            $post['WorkAreaID'],
            $post['WorkPeriod'],
            $post['MillID'],
            $post['SmeID'],
            $post['RefineryID'],
            $post['StatusCode'],
            $_SESSION['userid'],
            $post['StaffID']
        );
        $query = $this->db->query($sql, $p);

        if($post['isFarmer'] == "1"){
            //cek sudah ada record disini belum
            $sql="SELECT
                    PersonFarmerID
                FROM
                    ktv_person_farmers
                WHERE
                    PersonID = ?
                LIMIT 1";
            $query = $this->db->query($sql, array($PersonID));
            $data = $query->row_array();
            $PersonFarmerID = $data['PersonFarmerID'];

            if($PersonFarmerID != ""){
                $sql="UPDATE `ktv_person_farmers` SET
                   `FarmerID` = ?
                WHERE
                    PersonID = ?
                LIMIT 1";
                $p = array(
                    $post['FarmerID'],
                    $PersonID
                );
                $query = $this->db->query($sql, $p);
            }else{
                //insert ke ktv_person_farmers
                $sql="INSERT INTO `ktv_person_farmers` SET
                       `PersonID` = ?,
                       `FarmerID` = ?";
                $p = array(
                    $PersonID,
                    $post['FarmerID']
                );
                $query = $this->db->query($sql, $p);
                
            }
        }

        //================= update ke 9 tabel staff (begin) ===========================//
        switch ($post['ObjType']) {
            case 'bank':
                if($post['Gender'] == "m") $bankGender = '1'; else $bankGender = '2';
                $sql="UPDATE `ktv_bank_branch_staff` SET
                       `BranchID` = ?,
                       `StaffName` = ?,
                       `Phone` = ?,
                       `Email` = ?,
                       `StaffBirth` = IF(?='',NULL,?),
                       `StaffGender` = IF(?='',NULL,?),
                       `IdentityNumber` = ?,
                       `VillageID` = IF(?='',NULL,?),
                       `Address` = IF(?='',NULL,?),
                       `PositionID` = ?,
                       `DateUpdated` = NOW(),
                       `LastModifiedBy` = ?
                    WHERE
                        PersonID = ?
                    LIMIT 1";
                $p = array(
                    $post['ObjID'],
                    $post['PersonNm'],
                    $post['OfficialCellPhone'],
                    $post['OfficialEmail'],
                    $post['BirthDate'],$post['BirthDate'],
                    $bankGender,$bankGender,
                    $post['Ssn'],
                    $post['VillageID'],$post['VillageID'],
                    $post['Address'],$post['Address'],
                    $post['PositionID'],
                    $_SESSION['userid'],
                    $PersonID
                );
                $query = $this->db->query($sql, $p);
            break;
            case 'cooperative':
                if($post['Gender'] == "m") $coopGender = '1'; else $coopGender = '2';
                if($post['WorkPeriod'] == "Full-time") $coopStatusKerja = 'Full-Time'; else $coopStatusKerja = 'Part-Time';
                $sql="UPDATE `ktv_cooperative_staff` SET
                       `CoopID` = ?,
                       `FarmerID` = IF(?='',NULL,?),
                       `StaffName` = ?,
                       `Position` = NULL,
                       `Phone` = ?,
                       `Email` = ?,
                       `StaffBirthday` = ?,
                       `StaffGender` = ?,
                       `StaffStatus` = ?,
                       `PaymentStatus` = NULL,
                       `LastModifiedBy` = ?,
                       `DateUpdated` = NOW()
                    WHERE
                        PersonID = ?
                    LIMIT 1
                    ";
                $p = array(
                    $post['ObjID'],
                    $post['FarmerID'],$post['FarmerID'],
                    $post['PersonNm'],
                    $post['OfficialCellPhone'],
                    $post['OfficialEmail'],
                    $post['BirthDate'],
                    $coopGender,
                    $coopStatusKerja,
                    $_SESSION['userid'],
                    $PersonID
                );
                $query = $this->db->query($sql, $p);
            break;
            case 'farmergroup':
                if($post['Gender'] == "m") $cpgGender = '1'; else $cpgGender = '2';
                $sql="UPDATE `ktv_cpg_staff` SET
                       `CPGid` = ?,
                       `StaffName` = ?,
                       `FarmerID` = IF(?='',NULL,?),
                       `Phone` = ?,
                       `Email` = ?,
                       `StaffBirthday` = ?,
                       `StaffGender` = ?,
                       `DateUpdated` = NOW(),
                       `LastModifiedBy` = ?
                    WHERE
                        PersonID = ?
                    LIMIT 1";
                $p = array(
                    $post['ObjID'],
                    $post['PersonNm'],
                    $post['FarmerID'],$post['FarmerID'],
                    $post['OfficialCellPhone'],
                    $post['OfficialEmail'],
                    $post['BirthDate'],
                    $cpgGender,
                    $_SESSION['userid'],
                    $PersonID
                );
                $query = $this->db->query($sql, $p);
            break;
            case 'extension':
                $sql="UPDATE `ktv_extension_staff` SET
                       `KTP` = ?,
                       `StaffName` = ?,
                       `Address` = IF(?='',NULL,?),
                       `Gender` = ?,
                       `BirthDttm` = ?,
                       `VillageID` = IF(?='',0,?),
                       `BirthPlace` = IF(?='',NULL,?),
                       `MaritalSt` = ?,
                       `Handphone` = ?,
                       `Email` = ?,
                       `DateUpdated` = NOW(),
                       `LastModifiedBy` = ?
                    WHERE
                        PersonID = ?
                    LIMIT 1";
                $p = array(
                    $post['Ssn'],
                    $post['PersonNm'],
                    $post['Address'],$post['Address'],
                    $post['Gender'],
                    $post['BirthDate'],
                    $post['VillageID'],$post['VillageID'],
                    $post['BirthPlace'],$post['BirthPlace'],
                    $post['MaritalSt'],
                    $post['OfficialCellPhone'],
                    $post['OfficialEmail'],
                    $_SESSION['userid'],
                    $PersonID
                );
                $query = $this->db->query($sql, $p);
            break;
            case 'private':
                if($post['Gender'] == "m") $privateGender = '1'; else $privateGender = '2';
                $sql="UPDATE `ktv_private_staff` SET
                       `PartnerID` = ?,
                       `StaffName` = ?,
                       `PrivateCellphone` = IF(?='',NULL,?),
                       `OfficialCellphone` = IF(?='',NULL,?),
                       `PrivateStaffEmail` = IF(?='',NULL,?),
                       `OfficialStaffEmail` = IF(?='',NULL,?),
                       `StaffBirth` = ?,
                       `StaffGender` = ?,
                       `Location` = ?,
                       `DateUpdated` = NOW(),
                       `LastModifiedBy` = ?
                    WHERE
                        PersonID = ?
                    LIMIT 1";
                $p = array(
                    $post['ObjID'],
                    $post['PersonNm'],
                    $post['PrivateCellPhone'],$post['PrivateCellPhone'],
                    $post['OfficialCellPhone'],$post['OfficialCellPhone'],
                    $post['PrivateEmail'],$post['PrivateEmail'],
                    $post['OfficialEmail'],$post['OfficialEmail'],
                    $post['BirthDate'],
                    $privateGender,
                    $post['DistrictID'],
                    $_SESSION['userid'],
                    $PersonID
                );
                $query = $this->db->query($sql, $p);
            break;
            case 'program':
                $sql="UPDATE `ktv_program_staff` SET
                       `PartnerID` = ?,
                       `Position` = ?,
                       `WorkArea` = IF(?='',NULL,?),
                       `DateUpdated` = NOW(),
                       `LastModifiedBy` = ?
                    WHERE
                        PersonID = ?
                    LIMIT 1";
                $p = array(
                    $post['ObjID'],
                    $post['PositionID'],
                    $post['DistrictID'],$post['DistrictID'],
                    $_SESSION['userid'],
                    $PersonID
                );
                $query = $this->db->query($sql, $p);
            break;
            case 'sce':
                if($post['Gender'] == "m") $sceGender = '1'; else $sceGender = '2';
                $sql="UPDATE `sce_farmer_staff` SET
                       `SceID` = ?,
                       `StaffName` = ?,
                       `FarmerID` = IF(?='',NULL,?),
                       `Phone` = ?,
                       `Email` = ?,
                       `StaffBirthday` = ?,
                       `StaffGender` = ?,
                       `DateUpdated` = NOW(),
                       `LastModifiedBy` = ?
                    WHERE
                        PersonID = ?
                    LIMIT 1";
                $p = array(
                    $post['ObjID'],
                    $post['PersonNm'],
                    $post['FarmerID'],$post['FarmerID'],
                    $post['OfficialCellPhone'],
                    $post['OfficialEmail'],
                    $post['BirthDate'],
                    $sceGender,
                    $_SESSION['userid'],
                    $PersonID
                );
                $query = $this->db->query($sql, $p);
            break;
            case 'trader':
                if($post['Gender'] == "m") $traderGender = '1'; else $traderGender = '2';
                $sql="UPDATE `ktv_trader_staff` SET
                   `TraderID` = ?,
                   `StaffName` = ?,
                   `PrivateCellphone` = IF(?='',NULL,?),
                   `OfficialCellphone` = IF(?='',NULL,?),
                   `PrivateStaffEmail` = IF(?='',NULL,?),
                   `OfficialStaffEmail` = IF(?='',NULL,?),
                   `StaffBirth` = ?,
                   `StaffGender` = ?,
                   `IdentityNumber` = ?,
                   `VillageID` = IF(?='',NULL,?),
                   `Address` = ?,
                   `DateUpdated` = NOW(),
                   `LastModifiedBy` = ?
                WHERE
                    PersonID = ?
                LIMIT 1";
                $p = array(
                    $post['ObjID'],
                    $post['PersonNm'],
                    $post['PrivateCellPhone'],$post['PrivateCellPhone'],
                    $post['OfficialCellPhone'],$post['OfficialCellPhone'],
                    $post['PrivateEmail'],$post['PrivateEmail'],
                    $post['OfficialEmail'],$post['OfficialEmail'],
                    $post['BirthDate'],
                    $traderGender,
                    $post['Ssn'],
                    $post['VillageID'],$post['VillageID'],
                    $post['Address'],
                    $_SESSION['userid'],
                    $PersonID,
                );
                $query = $this->db->query($sql, $p);
            break;
            case 'warehouse':
                if($post['Gender'] == "m") $wareGender = '1'; else $wareGender = '2';
                $sql="UPDATE `ktv_warehouse_staff` SET
                       `WarehouseID` = ?,
                       `StaffName` = ?,
                       `Phone` = ?,
                       `Email` = ?,
                       `StaffBirth` = ?,
                       `StaffGender` = ?,
                       `IdentityNumber` = ?,
                       `VillageID` = IF(?='',NULL,?),
                       `Address` = ?,
                       `DateUpdated` = NOW(),
                       `LastModifiedBy` = ?
                    WHERE
                        PersonID = ?
                    LIMIT 1";
                $p = array(
                    $post['ObjID'],
                    $post['PersonNm'],
                    $post['OfficialCellPhone'],
                    $post['OfficialEmail'],
                    $post['BirthDate'],
                    $wareGender,
                    $post['Ssn'],
                    $post['VillageID'],$post['VillageID'],
                    $post['Address'],
                    $_SESSION['userid'],
                    $PersonID
                );
                $query = $this->db->query($sql, $p);
            break;
        }
        //================= update ke 9 tabel staff (end)   ===========================//

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to save record";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Record saved";
        }
        return $results;
    }

    public function deleteStaff($StaffID){
        $this->db->trans_begin();

        //get PersonID
        $sql="SELECT
                    PersonID
                FROM
                    ktv_staffs
                WHERE
                    StaffID = ?
                LIMIT 1";
        $query = $this->db->query($sql, array($StaffID));
        $data = $query->row_array();
        $PersonID = $data['PersonID'];

        $sql="UPDATE ktv_persons SET StatusCd = 'nullified' WHERE PersonID = ? LIMIT 1";
        $query = $this->db->query($sql, array($PersonID));

        $sql="UPDATE ktv_staffs SET StatusCode = 'nullified' WHERE StaffID = ? LIMIT 1";
        $query = $this->db->query($sql, array($StaffID));

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Record deleted";
        }
        return $results;
    }

    public function updateFoto($gambar,$userId){
        $sql = "UPDATE  `ktv_persons` SET  `Photo` =  ? WHERE  `ktv_persons`.`UserID` = ?";
        return $this->db->query($sql,array($gambar,$userId));
    }    

    public function updateFotoProfile($gambarPath) {
        $userID = $_SESSION['userid'];

        //Cek terlebih dahulu, apakah ada foto lama, kalau ada dihapus dl
        $sql = "SELECT
                    b.`PersonID`
                    , b.`Photo` AS PhotoPath
                FROM
                    ktv_staffs a
                    INNER JOIN ktv_persons b ON a.`PersonID` = b.`PersonID`
                WHERE
                    b.UserID = ?
                LIMIT 1";
        $DataCek = $this->db->query($sql, array($userID))->row_array();
        if (isset($DataCek['PhotoPath']) && $DataCek['PhotoPath'] != "") {
            $this->load->library('awsfileupload');
            $this->awsfileupload->delete($DataCek['PhotoPath']);
        }

        $sql = "UPDATE `ktv_persons` SET  `Photo` =  ? WHERE PersonID  = ? LIMIT 1";
        $query = $this->db->query($sql,array($gambarPath,$DataCek['PersonID']));
        if($query == true) {
            //Set Session Photo
            $_SESSION['Photo_staff'] = $gambarPath;
            return true;
        } else {
            return false;
        }
    }

    public function getFormUser($StaffID){
        $sql="SELECT
                a.`StaffID`,
                c.`UserId`,
                b.`PersonNm`,
                srole.`RoleName` AS RoleLabel,
                srole.RoleId,
                c.StatusCode,
                c.UserName,
                IF(c.UserLanguage='English','1','2') AS UserLanguage
            FROM
                ktv_staffs a
                INNER JOIN sys_role srole ON a.`ObjType` = srole.`RoleCode`
                INNER JOIN ktv_persons b ON a.`PersonID` = b.`PersonID`
                LEFT JOIN sys_user c ON b.`UserID` = c.`UserId`
            WHERE
                a.`StaffID` = ?
            LIMIT 1";
        $query = $this->db->query($sql, array($StaffID));
        $dataReturn = $query->result_array();

        //group
        $groups = array();
        $default_group = 0;
        $query = $this->db->get_where('sys_user_group', array('UserGroupUserId' => $dataReturn[0]['UserId']));
        $data = $query->result_array();
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $groups[] = $value['UserGroupGroupId'];
                if ($value['UserGroupIsDefault'] == '1') {
                    $default_group = $value['UserGroupGroupId'];
                }
            }
        }
        $dataReturn[0]['UserGroupIsDefault'] = $default_group;
        $dataReturn[0]['groups'] = $groups;

        //project
        $projects = array();
        $default_project = 0;
        $query = $this->db->get_where('ktv_staffs_project', array('StaffID' => $dataReturn[0]['StaffID']));
        $data = $query->result_array();
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $projects[] = $value['ProjID'];
                if ($value['ProjDefault'] == '1') {
                    $default_project = $value['ProjID'];
                }
            }
        }
        $dataReturn[0]['UserProjectIsDefault'] = $default_project;
        $dataReturn[0]['projects'] = $projects;

        $access = array();
        $query = $this->db->get_where('ktv_access_staff', array('UserId' => $dataReturn[0]['UserId']));
        $data = $query->result_array();
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $access[] = $value['DistrictID'];
            }
        }
        $dataReturn[0]['access'] = $access;

        //access role
        $accessRoleId = array();
        $sql="SELECT
                RoleId
            FROM
                sys_user_access_role
            WHERE
                UserId = ?
            ORDER BY UserAccRoleID ASC";
        $query = $this->db->query($sql,array($dataReturn[0]['UserId']));
        $data = $query->result_array();
        foreach ($data as $key => $value) {
            $accessRoleId[] = $value['RoleId'];
        }
        $dataReturn[0]['accessRoleId'] = $accessRoleId;

        $return['success'] = true;
        $return['data'] = $dataReturn[0];
        return $return;
    }

    public function insertUser($post){
        $this->db->trans_begin();

        //cek username sama
        $sql="SELECT
                UserId
            FROM
                sys_user
            WHERE
                UserName = ?
            LIMIT 1";
        $query = $this->db->query($sql,array($post['UserName']));
        $dataCekUsername = $query->row_array();
        if($dataCekUsername['UserId'] != ""){
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Username already existed";

            return $results;
        }

        //data ktv_person
        $sql="SELECT
                    a.OfficialEmail,
                    b.PersonID
                FROM
                    ktv_staffs a
                    INNER JOIN ktv_persons b ON a.`PersonID` = b.`PersonID`
                WHERE
                    a.`StaffID` = ?
                LIMIT 1";
        $query = $this->db->query($sql,array($post['StaffID']));
        $dataStaff = $query->row_array();

        if($post['StatusCode'] == 'active'){
            $UserActive = 'Yes';
            $StatusCode = 'active';
        }else{
            $UserActive = 'No';
            $StatusCode = 'inactive';
        }

        if($post['UserLanguage'] == '1'){
            $UserLanguage = 'English';
        }else{
            $UserLanguage = 'Indonesia';
        }

        // insert user
        $UserAddUserId      = $_SESSION['userid'];
        $UserAddTime        = date('Y-m-d H:i:s');
        $UserUpdateUserId   = $_SESSION['userid'];
        $UserUpdateTime     = date('Y-m-d H:i:s');

        $p = array(
            'UserName' => $post['UserName'],
            'UserRealName' => $post['PersonNm'],
            'UserPassword' => md5($post['UserPassword']),
            'UserEmail' => $dataStaff['OfficialEmail'],
            'UserActive' => $UserActive,
            'StatusCode' => $StatusCode,
            'UserLanguage' => $UserLanguage,
            'UserIsAdmin' => '0',
            'UserAddUserId' => $UserAddUserId,
            'UserAddTime' => $UserAddTime,
            'UserUpdateUserId' => $UserUpdateUserId,
            'UserUpdateTime' => $UserUpdateTime
        );
        $query = $this->db->insert('sys_user', $p);
        $UserId = $this->db->insert_id();

        //insert role
        $p = array(
            'UserId' => $UserId,
            'RoleId' => $post['RoleId']
        );
        $query = $this->db->insert('sys_user_role', $p);

        // insert user groups
        $GroupIds = explode(',',$post['GroupIds']);
        foreach ($GroupIds as $key => $GroupId) {
            $isDefault = $GroupId == $post['UserGroupIsDefault'] ? '1' : '0';
            $p = array(
                'UserGroupUserId' => $UserId,
                'UserGroupGroupId' => $GroupId,
                'UserGroupIsDefault' => $isDefault
            );
            $query = $this->db->insert('sys_user_group', $p);
        }

        //insert user projects
        $ProjIDs = explode(',',$post['ProjIDs']);
        foreach ($ProjIDs as $key => $ProjID) {
            $isDefault = $ProjID == $post['UserProjectIsDefault'] ? '1' : '0';
            $p = array(
                'StaffID' => $post['StaffID'],
                'ProjID' => $ProjID,
                'ProjDefault' => $isDefault,
                'DateCreated' => date('Y-m-d H:i:s'),
                'CreatedBy' => $_SESSION['userid']
            );
            $query = $this->db->insert('ktv_staffs_project', $p);
        }

        // insert user access
        if($post['AccessStaff'] != ""){
            $AccessStaff = explode(',',$post['AccessStaff']);
            foreach ($AccessStaff as $key => $DistrictID) {
                $query = $this->db->insert('ktv_access_staff', array(
                    'UserId' => $UserId,
                    'DistrictID' => $DistrictID,
                ));
            }
        }

        //update user id di ktv_persons
        $sql="UPDATE ktv_persons SET
                    UserID = ?
                WHERE
                    PersonID = ?
                LIMIT 1";
        $query = $this->db->query($sql,array($UserId,$dataStaff['PersonID']));


        //=========================== Access Role (BEGIN) ====================================================//
        if($post['AccessRoleId'] != ""){
            $post['AccessRoleId'] = explode(",",$post['AccessRoleId']);
            foreach ($post['AccessRoleId'] as $key => $value) {
                $sql="INSERT INTO `sys_user_access_role` SET
                      `UserId` = ?,
                      `RoleId` = ?,
                      `DateCreated` = NOW(),
                      `CreatedBy` = ?";
                $query = $this->db->query($sql,array($UserId,$value,$_SESSION['userid']));
            }
        }

        if($post['AccessObjId'] != ""){
            $post['AccessObjId'] = explode(",",$post['AccessObjId']);
            foreach ($post['AccessObjId'] as $key => $value) {
                $arrTmp = explode("-",$value);
                $objType = $arrTmp[0];
                $objId = $arrTmp[1];

                //get AccRoleId
                $sql="SELECT
                        b.`UserAccRoleID`
                    FROM
                        sys_role a
                        INNER JOIN sys_user_access_role b ON a.`RoleId` = b.`RoleId`
                    WHERE
                        a.`RoleCode` = ? AND
                        b.`UserId` = ?
                    LIMIT 1";
                $query = $this->db->query($sql,array($objType,$UserId));
                $data = $query->row_array();
                $UserAccRoleID = $data['UserAccRoleID'];

                $sql="INSERT INTO `sys_user_access_object` SET
                      `UserAccRoleID` = ?,
                      `ObjType` = ?,
                      `ObjID` = ?,
                      `DateCreated` = NOW(),
                      `CreatedBy` = ?";
                $p = array(
                    $UserAccRoleID,
                    $objType,
                    $objId,
                    $_SESSION['userid']
                );
                $query = $this->db->query($sql,$p);
            }
        }
        //=========================== Access Role (END)   ====================================================//

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Data saved";
        }

        return $results;
    }

    public function updateUser($post){
        $this->db->trans_begin();
        $UserId = $post['UserId'];

        //cek username sama
        $sql="SELECT
                UserId,
                UserExtId
            FROM
                sys_user
            WHERE
                UserName = ? AND
                UserId != ?
            LIMIT 1";
        $query = $this->db->query($sql,array($post['UserName'],$UserId));
        $dataCekUsername = $query->row_array();
        if($dataCekUsername['UserId'] != ""){
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Username already existed";

            return $results;
        }

        //get data user
        $sql="SELECT
                UserExtId,
                UserExtGroupId,
                UserExtRoleId
            FROM
                sys_user
            WHERE
                UserId = ?
            LIMIT 1";
        $query = $this->db->query($sql,array($UserId));
        $dataUser = $query->row_array();

        //data ktv_person
        $sql="SELECT
                    a.OfficialEmail,
                    b.PersonID,
                    b.PersonNm
                FROM
                    ktv_staffs a
                    INNER JOIN ktv_persons b ON a.`PersonID` = b.`PersonID`
                WHERE
                    a.`StaffID` = ?
                LIMIT 1";
        $query = $this->db->query($sql,array($post['StaffID']));
        $dataStaff = $query->row_array();

        if($post['StatusCode'] == 'active'){
            $UserActive = 'Yes';
            $StatusCode = 'active';
        }else{
            $UserActive = 'No';
            $StatusCode = 'inactive';
        }

        if($post['UserLanguage'] == '1'){
            $UserLanguage = 'English';
        }else{
            $UserLanguage = 'Indonesia';
        }

        $UserUpdateUserId   = $_SESSION['userid'];
        $UserUpdateTime     = date('Y-m-d H:i:s');

        //update sys_user
        $p = array(
            'UserName' => $post['UserName'],
            'UserRealName' => $dataStaff['PersonNm'],
            'UserActive' => $UserActive,
            'StatusCode' => $StatusCode,
            'UserLanguage' => $UserLanguage,
            'UserUpdateUserId' => $UserUpdateUserId,
            'UserUpdateTime' => $UserUpdateTime
        );
        $query = $this->db->update('sys_user', $p, compact('UserId'));

        //update password
        $isGantiPass = false;
        $jsonPassword = '';
        if($post['UserPassword'] != ""){
            $isGantiPass = true;
            $p = array(
                'UserPassword' => md5($post['UserPassword'])
            );
            $query = $this->db->update('sys_user', $p, compact('UserId'));

            $jsonPassword = '"password": "'.$post['UserPassword'].'",';
        }

        //delete group lalu insert
        $query = $this->db->delete('sys_user_group', array('UserGroupUserId' => $UserId));
        $GroupIds = explode(',',$post['GroupIds']);
        foreach ($GroupIds as $key => $GroupId) {
            $isDefault = $GroupId == $post['UserGroupIsDefault'] ? '1' : '0';
            $p = array(
                'UserGroupUserId' => $UserId,
                'UserGroupGroupId' => $GroupId,
                'UserGroupIsDefault' => $isDefault
            );
            $query = $this->db->insert('sys_user_group', $p);
        }

        //delete project lalu insert
        $query = $this->db->delete('ktv_staffs_project', array('StaffID' => $post['StaffID']));
        $ProjIDs = explode(',',$post['ProjIDs']);
        foreach ($ProjIDs as $key => $ProjID) {
            $isDefault = $ProjID == $post['UserProjectIsDefault'] ? '1' : '0';
            $p = array(
                'StaffID' => $post['StaffID'],
                'ProjID' => $ProjID,
                'ProjDefault' => $isDefault,
                'DateCreated' => date('Y-m-d H:i:s'),
                'CreatedBy' => $_SESSION['userid']
            );
            $query = $this->db->insert('ktv_staffs_project', $p);
        }

        // delete dl lalu insert user access
        if($post['AccessStaff'] != ""){
            $query = $this->db->delete('ktv_access_staff', array('UserId' => $UserId));

            $AccessStaff = explode(',',$post['AccessStaff']);
            foreach ($AccessStaff as $key => $DistrictID) {
                $query = $this->db->insert('ktv_access_staff', array(
                    'UserId' => $UserId,
                    'DistrictID' => $DistrictID,
                ));
            }
        }

        //=========================== Access Role (BEGIN) ====================================================//
        //delete data yg ada dl
        $sql="DELETE a
                FROM
                    sys_user_access_object a
                    INNER JOIN `sys_user_access_role` b ON a.`UserAccRoleID` = b.`UserAccRoleID`
                WHERE
                    b.`UserId` = ?";
        $query = $this->db->query($sql,array($UserId));
        $query = $this->db->delete('sys_user_access_role', array('UserId' => $UserId));

        if($post['AccessRoleId'] != ""){
            $post['AccessRoleId'] = explode(",",$post['AccessRoleId']);
            foreach ($post['AccessRoleId'] as $key => $value) {
                $sql="INSERT INTO `sys_user_access_role` SET
                      `UserId` = ?,
                      `RoleId` = ?,
                      `DateCreated` = NOW(),
                      `CreatedBy` = ?";
                $query = $this->db->query($sql,array($UserId,$value,$_SESSION['userid']));
            }
        }

        if($post['AccessObjId'] != ""){
            $post['AccessObjId'] = explode(",",$post['AccessObjId']);
            foreach ($post['AccessObjId'] as $key => $value) {
                $arrTmp = explode("-",$value);
                $objType = $arrTmp[0];
                $objId = $arrTmp[1];

                //get AccRoleId
                $sql="SELECT
                        b.`UserAccRoleID`
                    FROM
                        sys_role a
                        INNER JOIN sys_user_access_role b ON a.`RoleId` = b.`RoleId`
                    WHERE
                        a.`RoleCode` = ? AND
                        b.`UserId` = ?
                    LIMIT 1";
                $query = $this->db->query($sql,array($objType,$UserId));
                $data = $query->row_array();
                $UserAccRoleID = $data['UserAccRoleID'];

                $sql="INSERT INTO `sys_user_access_object` SET
                      `UserAccRoleID` = ?,
                      `ObjType` = ?,
                      `ObjID` = ?,
                      `DateCreated` = NOW(),
                      `CreatedBy` = ?";
                $p = array(
                    $UserAccRoleID,
                    $objType,
                    $objId,
                    $_SESSION['userid']
                );
                $query = $this->db->query($sql,$p);
            }
        }
        //=========================== Access Role (END)   ====================================================//

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        } else {

            if($dataUser['UserExtId'] != ""){
                //update user ke dhis (begin)
                $tmpName   = explode(" ", $dataStaff['PersonNm']);
                $firstName = $tmpName[0];
                unset($tmpName[0]);
                $lastName = implode(" ", $tmpName);

                //org unit (begin)
                $sql="SELECT
                        a.`DistrictID`,
                        b.`District`,
                        mworg.uid
                    FROM
                        ktv_access_staff a
                        LEFT JOIN ktv_district b ON a.`DistrictID` = b.`DistrictID`
                        LEFT JOIN mw_organisationunit mworg ON b.`District` = mworg.`name`
                    WHERE
                        a.`UserId` = ?";
                $query = $this->db->query($sql,array($UserId));
                $dataOrgUnit = $query->result_array();

                $tmpJson = array();
                foreach ($dataOrgUnit as $key => $value) {
                    if($value['uid'] != ""){
                        $tmpJson[]['id'] = $value['uid'];
                    }
                }
                $jsonOrgUnit = json_encode($tmpJson);
                //org unit (end)

                //User Group DHIS ============================= (Begin)
                if($dataUser['UserExtGroupId'] != ""){
                    $AppGroupUidRaw = $dataUser['UserExtGroupId'];

                    $TmpAppGroupUid = explode(',',$dataUser['UserExtGroupId']);
                    $TmpJsonAppGroupUid = array();            
                    foreach ($TmpAppGroupUid as $key => $value) {
                        $TmpJsonAppGroupUid[]['id'] = $value;
                    }
                    $JsonAppGroupUid = json_encode($TmpJsonAppGroupUid);
                }else{
                    $JsonAppGroupUid = null;
                    $AppGroupUidRaw = null;
                }
                //User Group DHIS ============================= (End)

                //User Role DHIS ============================= (Begin)
                if($dataUser['UserExtRoleId'] != ""){
                    $AppRoleUidRaw = $dataUser['UserExtRoleId'];

                    $TmpAppRoleUid = explode(',',$dataUser['UserExtRoleId']);
                    $TmpJsonAppRoleUid = array();            
                    foreach ($TmpAppRoleUid as $key => $value) {
                        $TmpJsonAppRoleUid[]['id'] = $value;
                    }
                    $JsonAppRoleUid = json_encode($TmpJsonAppRoleUid);
                }else{
                    $JsonAppRoleUid = null;
                    $AppRoleUidRaw = null;
                }
                //User Role DHIS ============================= (End)

                $bodyJson = '{
                    "firstName": "'.$firstName.'",
                    "surname": "'.$lastName.'",
                    "userCredentials": {
                        "username": "'.$post['UserName'].'",
                        '.$jsonPassword.'
                        "userRoles": '.$JsonAppRoleUid.'
                    },
                    "organisationUnits": '.$jsonOrgUnit.',
                    "userGroups": '.$JsonAppGroupUid.'
                }';

                $url = $this->config->item('dhis_url').'api/users/'.$dataUser['UserExtId'];
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                  'Content-Type: application/json',
                  'Authorization: Basic YWRtaW46S29sdGl2YTIwMTMh'
                ));
                curl_setopt($ch, CURLOPT_POSTFIELDS, ($bodyJson));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $result = curl_exec($ch);
                $curlresult = json_decode($result,true);

                if($curlresult['status'] == "SUCCESS") {
                    $this->db->trans_commit();
                    $results['success'] = true;
                    $results['message'] = "Data saved";
                }else{
                    $this->db->trans_rollback();
                    $results['success'] = false;
                    $results['message'] = "Failed to update dhis user";
                }
                //update user ke dhis (end)

            }else{
                $this->db->trans_commit();
                $results['success'] = true;
                $results['message'] = "Data saved";
            }

        }
        return $results;
    }

    public function deleteUser($UserId,$StaffID){
        $this->db->trans_begin();

        $query = $this->db->delete('ktv_access_staff', array('UserId' => $UserId));
        $query = $this->db->delete('sys_user_group', array('UserGroupUserId' => $UserId));
        $query = $this->db->delete('ktv_staffs_project', array('StaffID' => $StaffID));
        $query = $this->db->delete('sys_user', array('UserId' => $UserId));

        //delete data yg ada dl
        $sql="DELETE a
                FROM
                    sys_user_access_object a
                    INNER JOIN `sys_user_access_role` b ON a.`UserAccRoleID` = b.`UserAccRoleID`
                WHERE
                    b.`UserId` = ?";
        $query = $this->db->query($sql,array($UserId));
        $query = $this->db->delete('sys_user_access_role', array('UserId' => $UserId));

        //data ktv_person
        $sql="SELECT
                    a.OfficialEmail,
                    b.PersonID
                FROM
                    ktv_staffs a
                    INNER JOIN ktv_persons b ON a.`PersonID` = b.`PersonID`
                WHERE
                    a.`StaffID` = ?
                LIMIT 1";
        $query = $this->db->query($sql,array($StaffID));
        $dataStaff = $query->row_array();

        //update user id di ktv_persons
        $sql="UPDATE ktv_persons SET
                    UserID = NULL
                WHERE
                    PersonID = ?
                LIMIT 1";
        $query = $this->db->query($sql,array($dataStaff['PersonID']));

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Record deleted";
        }
        return $results;
    }

    public function getAppRefRole(){
        $arrReturn = array();

        $url = $this->config->item('dhis_url').'api/userRoles';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'Content-Type: application/json',
          'Authorization: Basic '.$this->config->item('dhis_basic_auth')
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, ($bodyJson));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        $curlresult = json_decode($result,true);
        //echo '<pre>'; print_r($curlresult); exit;

        $DataCurl = $curlresult['userRoles'];
        foreach ($DataCurl as $key => $value) {
            $arrReturn[$key]['id'] = $value['id'];
            $arrReturn[$key]['name'] = $value['displayName'];
        }
        return $arrReturn;
    }

    public function getAppRefGroup(){
        $arrReturn = array();

        $url = $this->config->item('dhis_url').'api/userGroups';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'Content-Type: application/json',
          'Authorization: Basic '.$this->config->item('dhis_basic_auth')
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, ($bodyJson));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        $curlresult = json_decode($result,true);
        //echo '<pre>'; print_r($curlresult); exit;

        $DataCurl = $curlresult['userGroups'];
        foreach ($DataCurl as $key => $value) {
            $arrReturn[$key]['id'] = $value['id'];
            $arrReturn[$key]['name'] = $value['displayName'];
        }
        return $arrReturn;
    }

    public function getFormUserApp($StaffID){
        $sql="SELECT
                a.UserId,
                c.`StaffID`,
                b.`PersonID`,
                a.`UserName`,
                a.`UserExtId`,
                IF(a.`UserExtId` IS NULL,'insert','update') AS formMethod,
                a.`UserExtGroupId` AS AppGroupUid,
                a.`UserExtRoleId` AS AppRoleUid
            FROM
                sys_user a
                INNER JOIN ktv_persons b ON a.`UserId` = b.`UserID`
                INNER JOIN ktv_staffs c ON b.`PersonID` = c.`PersonID`
            WHERE
                c.`StaffID` = ?
            LIMIT 1";
        $query = $this->db->query($sql,array($StaffID));
        $dataReturn = $query->row_array();

        if($dataReturn['UserId'] != ""){
            $return['success'] = true;
            $return['data'] = $dataReturn;
        }else{
            $return['success'] = false;
        }
        return $return;
    }

    public function getListAccessStaffApp($UserId){
        $sql="SELECT
                    a.`DistrictID`,
                    b.`District`
                FROM
                    ktv_access_staff a
                    LEFT JOIN ktv_district b ON a.`DistrictID` = b.`DistrictID`
                WHERE
                    a.`UserId` = ?
                ORDER BY a.DistrictID ASC";
        $query = $this->db->query($sql,array($UserId));
        $dataReturn = $query->result_array();

        $return['success'] = true;
        $return['data'] = $dataReturn;
        return $return;
    }

    public function insertUserApp($post){
        $prosesAll = true;
        $this->db->trans_begin();

        $sql="SELECT
                    a.OfficialEmail,
                    b.PersonID,
                    b.PersonNm
                FROM
                    ktv_staffs a
                    INNER JOIN ktv_persons b ON a.`PersonID` = b.`PersonID`
                WHERE
                    a.`StaffID` = ?
                LIMIT 1";
        $query = $this->db->query($sql,array($post['StaffID']));
        $dataStaff = $query->row_array();

        //insert ke dhis (begin)
        $tmpName   = explode(" ", $dataStaff['PersonNm']);
        $firstName = $tmpName[0];
        unset($tmpName[0]);
        $lastName = implode(" ", $tmpName);
        if(strlen($lastName) < 2){
            switch (strlen($lastName)) {
                case 0:
                    $lastName = "..";
                break;
                case 1:
                    $lastName = $lastName.".";
                break;
            }
        }

        //org unit (begin)
        $sql="SELECT
                a.`DistrictID`,
                b.`District`,
                mworg.uid
            FROM
                ktv_access_staff a
                LEFT JOIN ktv_district b ON a.`DistrictID` = b.`DistrictID`
                LEFT JOIN mw_organisationunit mworg ON b.`District` = mworg.`name`
            WHERE
                a.`UserId` = ?";
        $query = $this->db->query($sql,array($post['UserId']));
        $dataOrgUnit = $query->result_array();

        $tmpJson = array();
        foreach ($dataOrgUnit as $key => $value) {
            if($value['uid'] != ""){
                $tmpJson[]['id'] = $value['uid'];
            }
        }
        $jsonOrgUnit = json_encode($tmpJson);
        //org unit (end)

        //User Group DHIS ============================= (Begin)
        if($post['AppGroupUid'] != ""){
            $AppGroupUidRaw = $post['AppGroupUid'];

            $TmpAppGroupUid = explode(',',$post['AppGroupUid']);
            $TmpJsonAppGroupUid = array();            
            foreach ($TmpAppGroupUid as $key => $value) {
                $TmpJsonAppGroupUid[]['id'] = $value;
            }
            $JsonAppGroupUid = json_encode($TmpJsonAppGroupUid);
        }else{
            $JsonAppGroupUid = null;
            $AppGroupUidRaw = null;
        }
        //User Group DHIS ============================= (End)

        //User Role DHIS ============================= (Begin)
        if($post['AppRoleUid'] != ""){
            $AppRoleUidRaw = $post['AppRoleUid'];

            $TmpAppRoleUid = explode(',',$post['AppRoleUid']);
            $TmpJsonAppRoleUid = array();
            foreach ($TmpAppRoleUid as $key => $value) {
                $TmpJsonAppRoleUid[]['id'] = $value;
            }
            $JsonAppRoleUid = json_encode($TmpJsonAppRoleUid);
        }else{
            $JsonAppRoleUid = null;
            $AppRoleUidRaw = null;
        }
        //User Role DHIS ============================= (End)

        $bodyJson = '{
            "firstName": "'.$firstName.'",
            "surname": "'.$lastName.'",
            "email": "'.$dataStaff['OfficialEmail'].'",
            "userCredentials": {
                "username": "'.$post['UserName'].'",
                "password": "'.$post['UserPassword'].'",
                "userRoles": '.$JsonAppRoleUid.'
            },
            "organisationUnits": '.$jsonOrgUnit.',
            "userGroups": '.$JsonAppGroupUid.'
        }';
        //echo '<pre>'; print_r($bodyJson); exit;

        $url = $this->config->item('dhis_url').'api/users';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'Content-Type: application/json',
          'Authorization: Basic YWRtaW46S29sdGl2YTIwMTMh'
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, ($bodyJson));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        $curlresult = json_decode($result,true);

        if($curlresult['lastImported'] != "") {
            $uidDhis = $curlresult['lastImported'];

            //update user id di ktv_persons
            $sql="UPDATE sys_user SET
                        UserExtId = ?,
                        UserExtPassword = ?,
                        UserExtGroupId = ?,
                        UserExtRoleId = ?
                    WHERE
                        UserId = ?
                    LIMIT 1";
            $query = $this->db->query($sql,array($uidDhis,md5($post['UserPassword']),$AppGroupUidRaw,$AppRoleUidRaw,$post['UserId']));
        }else{
            $prosesAll = false;
        }
        //insert ke dhis (end)

        if ($prosesAll == false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to create user";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "User created";
        }
        return $results;
    }

    public function updateUserApp($post){
        $prosesAll = true;
        $this->db->trans_begin();

        $sql="SELECT
                    a.OfficialEmail,
                    b.PersonID,
                    b.PersonNm
                FROM
                    ktv_staffs a
                    INNER JOIN ktv_persons b ON a.`PersonID` = b.`PersonID`
                WHERE
                    a.`StaffID` = ?
                LIMIT 1";
        $query = $this->db->query($sql,array($post['StaffID']));
        $dataStaff = $query->row_array();

        //update password
        $isGantiPass = false;
        if($post['UserPassword'] != ""){
            $isGantiPass = true;
        }

        //update user ke dhis (begin)
        $tmpName   = explode(" ", $dataStaff['PersonNm']);
        $firstName = $tmpName[0];
        unset($tmpName[0]);
        $lastName = implode(" ", $tmpName);
        if(strlen($lastName) < 2){
            switch (strlen($lastName)) {
                case 0:
                    $lastName = "..";
                break;
                case 1:
                    $lastName = $lastName.".";
                break;
            }
        }

        if($isGantiPass == true){
            $jsonPassword = '"password": "'.$post['UserPassword'].'",';
        }else{
            $jsonPassword = '';
        }

        //org unit (begin)
        $sql="SELECT
                a.`DistrictID`,
                b.`District`,
                mworg.uid
            FROM
                ktv_access_staff a
                LEFT JOIN ktv_district b ON a.`DistrictID` = b.`DistrictID`
                LEFT JOIN mw_organisationunit mworg ON b.`District` = mworg.`name`
            WHERE
                a.`UserId` = ?";
        $query = $this->db->query($sql,array($post['UserId']));
        $dataOrgUnit = $query->result_array();

        $tmpJson = array();
        foreach ($dataOrgUnit as $key => $value) {
            if($value['uid'] != ""){
                $tmpJson[]['id'] = $value['uid'];
            }
        }
        $jsonOrgUnit = json_encode($tmpJson);
        //org unit (end)

        //User Group DHIS ============================= (Begin)
        if($post['AppGroupUid'] != ""){
            $AppGroupUidRaw = $post['AppGroupUid'];

            $TmpAppGroupUid = explode(',',$post['AppGroupUid']);
            $TmpJsonAppGroupUid = array();            
            foreach ($TmpAppGroupUid as $key => $value) {
                $TmpJsonAppGroupUid[]['id'] = $value;
            }
            $JsonAppGroupUid = json_encode($TmpJsonAppGroupUid);
        }else{
            $JsonAppGroupUid = null;
            $AppGroupUidRaw = null;
        }
        //User Group DHIS ============================= (End)

        //User Role DHIS ============================= (Begin)
        if($post['AppRoleUid'] != ""){
            $AppRoleUidRaw = $post['AppRoleUid'];

            $TmpAppRoleUid = explode(',',$post['AppRoleUid']);
            $TmpJsonAppRoleUid = array();
            foreach ($TmpAppRoleUid as $key => $value) {
                $TmpJsonAppRoleUid[]['id'] = $value;
            }
            $JsonAppRoleUid = json_encode($TmpJsonAppRoleUid);
        }else{
            $JsonAppRoleUid = null;
            $AppRoleUidRaw = null;
        }
        //User Role DHIS ============================= (End)

        $bodyJson = '{
            "firstName": "'.$firstName.'",
            "surname": "'.$lastName.'",
            "userCredentials": {
                "username": "'.$post['UserName'].'",
                '.$jsonPassword.'
                "userRoles": '.$JsonAppRoleUid.'
            },
            "organisationUnits": '.$jsonOrgUnit.',
            "userGroups": '.$JsonAppGroupUid.'
        }';

        $url = $this->config->item('dhis_url').'api/users/'.$post['UserExtId'];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'Content-Type: application/json',
          'Authorization: Basic YWRtaW46S29sdGl2YTIwMTMh'
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, ($bodyJson));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        $curlresult = json_decode($result,true);

        if($curlresult['status'] == "SUCCESS") {
            //update
            $sql="UPDATE sys_user SET
                        UserExtGroupId = ?,
                        UserExtRoleId = ?
                    WHERE
                        UserId = ?
                    LIMIT 1";
            $query = $this->db->query($sql,array($AppGroupUidRaw,$AppRoleUidRaw,$post['UserId']));

            if($isGantiPass == true){
                $sql="UPDATE sys_user SET
                        UserExtPassword = ?
                    WHERE
                        UserId = ?
                    LIMIT 1";
                $query = $this->db->query($sql,array(md5($post['UserPassword']),$post['UserId']));
            }
        }else{
            $prosesAll = false;
        }
        //update user ke dhis (end)

        if($prosesAll == false){
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to update user";
        }else{
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "User updated";
        }

        return $results;
    }

    public function getAccessRole(){
        $sql="SELECT
                RoleId AS `id`,
                RoleName AS `name`
            FROM
                sys_role
            ORDER BY RoleId ASC";
        $query = $this->db->query($sql,array());
        return $query->result_array();
    }

    public function getObjTypeList(){
        //cek is admin
        if($_SESSION['is_admin'] == "1"){
            $sql="SELECT
                b.`RoleCode` AS id,
                b.`RoleName` AS label
            FROM
                sys_role b
            WHERE
                b.StatusCode = 'active'
            ORDER BY b.`RoleId` ASC";
            $query = $this->db->query($sql, array());
        }else{
            $sql="SELECT
                b.`RoleCode` AS id,
                b.`RoleName` AS label
            FROM
                sys_role b
            WHERE
                b.StatusCode = 'active'
            ORDER BY b.`RoleId` ASC";
            $query = $this->db->query($sql, array());
        }


        $result['data'] = $query->result_array();
        return $result;
    }

    public function getPositionReference($ObjType){
        $sql="SELECT
                a.`PositionID` AS id,
                a.PositionName AS label
            FROM
                ktv_ref_position_type a
            WHERE
                a.`ObjType` = ?
            ORDER BY a.`PositionName` ASC";
        $query = $this->db->query($sql,array($ObjType));
        $result['data'] = $query->result_array();
        return $result;
    }

    public function insertStaffPosition($varPost){
        $this->db->trans_begin();

        //yg aktif hanya boleh satu
        if($varPost['StatusCode'] == "active"){
            $sql="UPDATE ktv_staff_positions SET StatusCode = 'inactive' WHERE `StaffPosStaffID` = ? AND StatusCode != 'nullified'";
            $p = array(
                $varPost['StaffID']
            );
            $query = $this->db->query($sql,$p);
        }

        $sql="INSERT INTO `ktv_staff_positions` SET
              `StaffPosStaffID` = ?,
              `StaffPosPositionID` = ?,
              `StaffPostStart` = ?,
              `StaffPostEnd` = ?,
              `StatusCode` = ?,
              `DateCreated` = NOW(),
              `CreatedBy` = ?";
        $p = array(
            $varPost['StaffID'],
            $varPost['PositionID'],
            $varPost['StaffPostStartDate'],
            $varPost['StaffPostEndDate'],
            $varPost['StatusCode'],
            $varPost['userid']
        );
        $query = $this->db->query($sql,$p);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to update data";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Data Updated";
        }
        return $results;
    }

    public function updateStaffPosition($varPost){
        $this->db->trans_begin();

        //yg aktif hanya boleh satu
        if($varPost['StatusCode'] == "active"){
            $sql="UPDATE ktv_staff_positions SET StatusCode = 'inactive' WHERE `StaffPosStaffID` = ? AND StatusCode != 'nullified'";
            $p = array(
                $varPost['StaffID']
            );
            $query = $this->db->query($sql,$p);
        }

        //cek bug pakai combo di rowEditing
        $cekPosition = (int) $varPost['PositionID'];
        if($cekPosition == 0){
            $sqlPosition = "";
        }else{
            $sqlPosition = "`StaffPosPositionID` = $cekPosition,";
        }

        $sql="UPDATE `ktv_staff_positions` SET
              $sqlPosition
              `StaffPostStart` = ?,
              `StaffPostEnd` = ?,
              `StatusCode` = ?,
              DateUpdated = NOW(),
              LastModifiedBy = ?
            WHERE
                StaffPosID = ?
            LIMIT 1";
        $p = array(
            $varPost['StaffPostStartDate'],
            $varPost['StaffPostEndDate'],
            $varPost['StatusCode'],
            $varPost['userid'],
            $varPost['StaffPosID']
        );
        $query = $this->db->query($sql,$p);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to update data";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Data Updated";
        }
        return $results;
    }

    public function deleteStaffPosition($id){
        $sql="UPDATE ktv_staff_positions SET
              `StatusCode` = 'nullified',
              DateUpdated = NOW(),
              LastModifiedBy = ?
            WHERE
                StaffPosID = ?
            LIMIT 1
        ";
        $query = $this->db->query($sql,array($_SESSION['userid'],(int) $id));

        if ($query == false) {
            $results['success'] = false;
            $results['message'] = "Failed to delete data";
        } else {
            $results['success'] = true;
            $results['message'] = "Data Deleted";
        }
        return $results;
    }

    public function getProject(){
        $sql="SELECT
                a.`ProjID`
                , CONCAT(b.`PartnerName`,' - ',a.`ProjName`) AS ProjLabel
            FROM
                ktv_program_partner_project a
                INNER JOIN ktv_program_partner b ON a.`PartnerID` = b.`PartnerID`
            ORDER BY ProjLabel DESC";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function getRegistersMainGridList($pSearch,$start,$limit,$sortingField,$sortingDir){
        if($sortingField == "") $sortingField = 'Fullname';
        if($sortingDir == "") $sortingDir = 'ASC';

        $sql="SELECT
                SQL_CALC_FOUND_ROWS
                a.`RegID`
                , a.`Email`
                , a.`Username`
                , a.`Fullname`
                , CASE
                    WHEN a.ObjType = 'program' THEN 'Program'
                    WHEN a.ObjType = 'private' THEN 'Private'
                    WHEN a.ObjType = 'service' THEN 'Service Provider'
                    WHEN a.ObjType = 'mill' THEN 'Mill'
                    WHEN a.ObjType = 'agent' THEN 'Agent'
                    WHEN a.ObjType = 'refinery' THEN 'Refinery'
                END AS UserRole
                , CASE
                    WHEN a.ObjType = 'program' THEN
                        (
                            SELECT PartnerName FROM ktv_program_partner WHERE PartnerID = a.ObjID
                        )
                    WHEN a.ObjType = 'private' THEN
                        (
                            SELECT PartnerName FROM ktv_program_partner WHERE PartnerID = a.ObjID
                        )
                    WHEN a.ObjType = 'service' THEN
                        (
                            SELECT OfficialName FROM ktv_service_provider WHERE ServiceProvID = a.ObjID
                        )
                    WHEN a.ObjType = 'mill' THEN
                        (
                            SELECT CONCAT(MillDisplayID,' - ',MillName) FROM ktv_mill WHERE MillID = a.ObjID
                        )
                    WHEN a.ObjType = 'agent' THEN
                        (
                            SELECT CONCAT(MemberDisplayID,' - ',MemberName) FROM ktv_members WHERE MemberID = a.ObjID
                        )
                    WHEN a.ObjType = 'refinery' THEN
                    (
                        SELECT CONCAT(MillDisplayID,' - ',RefineryName) FROM ktv_refinery WHERE RefineryID = a.ObjID
                    )
                END AS ObjLabel
                , CASE
                    WHEN a.StatusRegistered = '1' THEN 'Yes'
                    WHEN a.StatusRegistered = '0' THEN 'No'
                END AS StatusRegistered
                , IF((a.`LastModifiedBy` IS NOT NULL) || (a.`LastModifiedBy` != ''),
                    (SELECT CONCAT(sub_a.UserRealname,', ',a.DateUpdated) FROM sys_user sub_a WHERE sub_a.UserId = a.`LastModifiedBy` LIMIT 1),
                    (SELECT CONCAT(sub_a.UserRealname,', ',a.DateCreated) FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy` LIMIT 1)
                ) AS LastUpdatedLabel
            FROM
                register_staff a
            WHERE
                ( (a.ObjType = ?) OR ('' = ?) )
                AND ( (a.Fullname LIKE ?) OR (a.Username LIKE ?) )
            ORDER BY $sortingField $sortingDir
            LIMIT ?,?";
        $p = array(
            $pSearch['Role'], $pSearch['Role'],
            '%'.$pSearch['StringNameUsername'].'%', '%'.$pSearch['StringNameUsername'].'%',
            (int) $start, (int) $limit
        );
        $query = $this->db->query($sql,$p);
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    private function cekRegisterStaffEmailUsername($paramPost){
        if($paramPost['RegID'] == ""){
            $sql="SELECT
                    a.`RegID`
                FROM
                    register_staff a
                WHERE
                    a.`Username` = ?
                    OR a.`Email` = ?";
            $p = array(
                $paramPost['Username'],
                $paramPost['Email']
            );
            $query = $this->db->query($sql,$p);
            $data = $query->result_array();
            if($data[0]['RegID'] != ""){
                return false;
            }else{
                return true;
            }
        }else{
            $sql="SELECT
                    a.`RegID`
                FROM
                    register_staff a
                WHERE
                    ( (a.`Username` = ?) OR (a.`Email` = ?) )
                    AND a.`RegID` != ?";
            $p = array(
                $paramPost['Username'],
                $paramPost['Email'],
                $paramPost['RegID']
            );
            $query = $this->db->query($sql,$p);
            $data = $query->result_array();

            if($data[0]['RegID'] != ""){
                return false;
            }else{
                return true;
            }
        }
    }

    private function cekRegisterStaffUsernameSystem($paramPost){
        $sql="SELECT
                a.`UserId`
            FROM
                sys_user a
            WHERE
                a.`StatusCode` = 'active'
                AND a.`UserName` = ?";
        $query = $this->db->query($sql,array($paramPost['Username']));
        $data = $query->result_array();

        if($data[0]['UserId'] != ""){
            return false;
        }else{
            return true;
        }
    }

    public function getDataFillFormRegisterStaff($RegID){
        $sql="SELECT
                `RegID`,
                `Email`,
                `Username`,
                `Fullname`,
                CASE
                    WHEN ObjType = 'agent' THEN
                        (
                            SELECT 
                                d.`ProvinceID` 
                            FROM 
                                ktv_members sub_a
                                LEFT JOIN ktv_village v ON v.`VillageID` = sub_a.`VillageID`
                                LEFT JOIN ktv_subdistrict sd ON sd.`SubDistrictID` = v.`SubDistrictID`
                                LEFT JOIN ktv_district d ON d.`DistrictID` = sd.`DistrictID`
                            WHERE 
                                sub_a.MemberID = ObjID 
                            LIMIT 1
                        )
                END AS ProvinceID,
                CASE
                    WHEN ObjType = 'agent' THEN
                        (
                            SELECT 
                                sd.`DistrictID` 
                            FROM 
                                ktv_members sub_a
                                LEFT JOIN ktv_village v ON v.`VillageID` = sub_a.`VillageID`
                                LEFT JOIN ktv_subdistrict sd ON sd.`SubDistrictID` = v.`SubDistrictID`
                            WHERE 
                                sub_a.MemberID = ObjID 
                            LIMIT 1
                        )
                END AS DistrictID,
                `ObjType`,
                `ObjID`,
                `PositionID`,
                `GroupIDDefa`,
                `IsMobileDhisUser`,
                `UserExtRoleId`,
                `UserExtGroupId`,
                `StatusRegistered`,
                `DateRegistered`
            FROM
                `register_staff`
            WHERE
                RegID = ?
            LIMIT 1";
        $query = $this->db->query($sql, array($RegID));
        $data = $query->row_array();

        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-".$key;
            $dataRow[$keyNew] = $value;
        }

        //yg diperlukan untuk proses lebih lanjut
        $dataRow['ProvinceID'] = $dataRow['Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-ProvinceID'];
        $dataRow['DistrictID'] = $dataRow['Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-DistrictID'];
        $dataRow['ObjType'] = $dataRow['Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-ObjType'];
        $dataRow['ObjID'] = $dataRow['Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-ObjID'];
        $dataRow['GroupIDDefa'] = $dataRow['Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-GroupIDDefa'];

        //user group ================================================ (begin)
        $groups = array();
        $sql="SELECT
                a.`GroupId`
            FROM
                `register_staff_user_group` a
            WHERE
                a.`RegID` = '$RegID'";
        $query = $this->db->query($sql);
        $data = $query->result_array();
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $groups[] = $value['GroupId'];
            }
        }
        $dataRow['groups'] = $groups;
        //user group ================================================ (end)

        //access area ================================================ (begin)
        $access_area = array();
        $sql="SELECT
                a.`DistrictID`
            FROM
                `register_staff_access_area` a
            WHERE
                a.`RegID` = '$RegID'";
        $query = $this->db->query($sql);
        $data = $query->result_array();
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $access_area[] = $value['DistrictID'];
            }
        }
        $dataRow['access_area'] = $access_area;
        //access area ================================================ (end)

        $return['success'] = true;
        $return['data'] = $dataRow;
        return $return;
    }

    public function insertRegisterStaff($paramPost){
        //cek apakah ada email / username yg sama
        $cekEmailUsername = $this->cekRegisterStaffEmailUsername($paramPost);
        if($cekEmailUsername == false){
            $results['success'] = false;
            $results['message'] = "Failed to save data, Username / Email already exist!";
            return $results;
        }

        //cek apakah username sudah terpakai di sys_user
        $cekUsernameSystem = $this->cekRegisterStaffUsernameSystem($paramPost);
        if($cekUsernameSystem == false){
            $results['success'] = false;
            $results['message'] = "Failed to save data, Username / Email already registered in the system!";
            return $results;
        }

        $this->db->trans_start();

        //insert "register_staff"
        $sql="INSERT INTO `register_staff` SET
                `Email` = ?,
                `Username` = ?,
                `Fullname` = ?,
                `ObjType` = ?,
                `ObjID` = ?,
                `PositionID` = ?,
                `GroupIDDefa` = ?,
                `IsMobileDhisUser` = ?,
                `UserExtRoleId` = ?,
                `UserExtGroupId` = ?,
                `DateCreated` = NOW(),
                `CreatedBy` = ?
            ";
        $p = array(
            $paramPost['Email'],
            $paramPost['Username'],
            $paramPost['Fullname'],
            $paramPost['ObjType'],
            $paramPost['ObjID'],
            $paramPost['PositionID'],
            $paramPost['GroupIDDefa'],
            $paramPost['IsMobileDhisUser'],
            $paramPost['UserExtRoleId'],
            $paramPost['UserExtGroupId'],
            $_SESSION['userid']
        );
        $query = $this->db->query($sql,$p);
        $RegID = $this->db->insert_id();

        if($paramPost['UserGroupIDs'] != ""){
            $arrUserGroup = explode(",",$paramPost['UserGroupIDs']);

            foreach ($arrUserGroup as $key => $value) {
                $sql="INSERT INTO `register_staff_user_group` SET
                        `RegID` = $RegID,
                        `GroupId` = $value";
                $query = $this->db->query($sql);
            }
        }

        if($paramPost['AccessAreas'] != ""){
            $arrAccessArea = explode(",",$paramPost['AccessAreas']);

            foreach ($arrAccessArea as $key => $value) {
                $sql="INSERT INTO `register_staff_access_area` SET
                    `RegID` = $RegID,
                    `DistrictID` = $value";
                $query = $this->db->query($sql);
            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Data saved";
        }else{
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        }

        return $results;
    }

    public function updateRegisterStaff($paramPost){
        //cek apakah ada email / username yg sama
        $cekEmailUsername = $this->cekRegisterStaffEmailUsername($paramPost);
        if($cekEmailUsername == false){
            $results['success'] = false;
            $results['message'] = "Failed to save data, Username / Email already exist!";
            return $results;
        }

        //cek apakah username sudah terpakai di sys_user
        $cekUsernameSystem = $this->cekRegisterStaffUsernameSystem($paramPost);
        if($cekUsernameSystem == false){
            $results['success'] = false;
            $results['message'] = "Failed to save data, Username / Email already registered in the system!";
            return $results;
        }

        $this->db->trans_start();

        //update "register_staff"
        $sql="UPDATE `register_staff` SET
                `Email` = ?,
                `Username` = ?,
                `Fullname` = ?,
                `ObjType` = ?,
                `ObjID` = ?,
                `PositionID` = ?,
                `GroupIDDefa` = ?,
                `IsMobileDhisUser` = ?,
                `UserExtRoleId` = ?,
                `UserExtGroupId` = ?,
                `DateUpdated` = NOW(),
                `LastModifiedBy` = ?
            WHERE
                `RegID` = ?
            LIMIT 1";
        $p = array(
            $paramPost['Email'],
            $paramPost['Username'],
            $paramPost['Fullname'],
            $paramPost['ObjType'],
            $paramPost['ObjID'],
            $paramPost['PositionID'],
            $paramPost['GroupIDDefa'],
            $paramPost['IsMobileDhisUser'],
            $paramPost['UserExtRoleId'],
            $paramPost['UserExtGroupId'],
            $_SESSION['userid'],
            $paramPost['RegID']
        );
        $query = $this->db->query($sql,$p);

        if($paramPost['UserGroupIDs'] != ""){
            //hapus dl datanya, baru insert lagi
            $sql="DELETE FROM register_staff_user_group WHERE RegID = '{$paramPost['RegID']}'";
            $query = $this->db->query($sql);

            $arrUserGroup = explode(",",$paramPost['UserGroupIDs']);
            foreach ($arrUserGroup as $key => $value) {
                $sql="INSERT INTO `register_staff_user_group` SET
                        `RegID` = {$paramPost['RegID']},
                        `GroupId` = $value";
                $query = $this->db->query($sql);
            }
        }

        if($paramPost['AccessAreas'] != ""){
            //hapus dl datanya, baru insert lagi
            $sql="DELETE FROM register_staff_access_area WHERE RegID = '{$paramPost['RegID']}'";
            $query = $this->db->query($sql);

            $arrAccessArea = explode(",",$paramPost['AccessAreas']);
            foreach ($arrAccessArea as $key => $value) {
                $sql="INSERT INTO `register_staff_access_area` SET
                    `RegID` = {$paramPost['RegID']},
                    `DistrictID` = $value";
                $query = $this->db->query($sql);
            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Data saved";
        }else{
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        }

        return $results;
    }

    public function deleteRegisterStaff($RegID){
        $this->db->trans_start();

        $sql="DELETE FROM register_staff WHERE RegID = $RegID LIMIT 1";
        $query = $this->db->query($sql);

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Data deleted";
        }else{
            $results['success'] = false;
            $results['message'] = "Failed to delete data";
        }
        return $results;
    }

    public function sendEmailRegistrationLink($RegID){
        //ammbil informasi
        $sql="SELECT
                `RegID`,
                `Email`,
                `Username`,
                `Fullname`,
                CASE
                    WHEN ObjType = 'agent' THEN
                        (
                            SELECT 
                                d.`ProvinceID` 
                            FROM 
                                ktv_members sub_a
                                LEFT JOIN ktv_village v ON v.`VillageID` = sub_a.`VillageID`
                                LEFT JOIN ktv_subdistrict sd ON sd.`SubDistrictID` = v.`SubDistrictID`
                                LEFT JOIN ktv_district d ON d.`DistrictID` = sd.`DistrictID`
                            WHERE 
                                sub_a.MemberID = ObjID 
                            LIMIT 1
                        )
                END AS ProvinceID,
                CASE
                    WHEN ObjType = 'agent' THEN
                        (
                            SELECT 
                                sd.`DistrictID` 
                            FROM 
                                ktv_members sub_a
                                LEFT JOIN ktv_village v ON v.`VillageID` = sub_a.`VillageID`
                                LEFT JOIN ktv_subdistrict sd ON sd.`SubDistrictID` = v.`SubDistrictID`
                            WHERE 
                                sub_a.MemberID = ObjID 
                            LIMIT 1
                        )
                END AS DistrictID,
                `ObjType`,
                `ObjID`,
                `PositionID`,
                `GroupIDDefa`,
                `IsMobileDhisUser`,
                `UserExtRoleId`,
                `UserExtGroupId`,
                `StatusRegistered`,
                `DateRegistered`
            FROM
                `register_staff`
            WHERE
                RegID = ?
            LIMIT 1";
        $query = $this->db->query($sql, array($RegID));
        $dataStaff = $query->row_array();

        //========================================================= Mulai Kirim Email (BEGIN) =============================================================//
            require_once 'application/third_party/phpmailer-hr/class.phpmailer.php';
            $this->config->load('email'); //$this->config->item('smtp_host');
            // Create new PHPExcel object
            $ObjMail = new PHPMailer();
            $ObjMail->IsSMTP();
            //$ObjMail->SMTPDebug = 2; // enables SMTP debug information (for testing)
            $ObjMail->SMTPSecure = 'tls';
            $ObjMail->SMTPAuth = true; // enable SMTP authentication
            $ObjMail->Host = $this->config->item('smtp_host'); // sets the SMTP server
            $ObjMail->Port = $this->config->item('smtp_port'); // set the SMTP port for the GMAIL server
            $ObjMail->Username = $this->config->item('smtp_user'); // SMTP account username
            $ObjMail->Password = $this->config->item('smtp_pass'); // SMTP account password

            $ObjMail->Priority = 0;
            $ObjMail->SetFrom($this->config->item('email_from'), 'Koltiva Support');

            $emailBodyHtml = $this->emailBodyRegistrationLink($dataStaff);

            $ObjMail->Subject = 'PalmoilTrace Registration Link';
            $ObjMail->Body = $emailBodyHtml;
            $ObjMail->IsHTML(true);

            $ObjMail->AddAddress($dataStaff['Email']);

            $sendEmailProses = $ObjMail->Send();
            $ObjMail->ClearAddresses();
            $ObjMail->ClearAllRecipients();
            $ObjMail->IsHTML(false);
            //========================================================= Mulai Kirim Email (END)   =============================================================//

            if($sendEmailProses == true){
                $return['success'] = true;
                $return['message'] = 'Sent';
            }else{
                $return['success'] = false;
                $return['message'] = 'Email gagal terkirim, mohon dicoba beberapa saat lagi.';
            }
            return $return;
    }

    public function emailBodyRegistrationLink($dataStaff){
        $urlBase = base_url();

        //hilangkan apinya
        $urlBase = str_replace('api/','',$urlBase);

        //var enkrip bin2hex
        $paramNya = bin2hex($dataStaff['RegID'].'@'.$dataStaff['Username']);

        $linkNya = $urlBase.'system/register/form/'.$paramNya;
        $regisHtml = '<a target="_blank" href="'.$linkNya.'">'.$linkNya.'</a>';

        $html = '
        Kepada '.$dataStaff['Fullname'].', / <i>Dear '.$dataStaff['Fullname'].',</i><br />
        Silahkan klik link dibawah ini untuk melakukan registrasi pada PalmoilTrace / <i>Please click the following link to process your registration on PalmoilTrace</i><br />
        '.$regisHtml.'<br /><br /><br />
        &copy;PalmoilTrace Automatic Email<br />
        Please do not reply this email
        ';

        return $html;
    }

    public function getStaffGeneralForm($StaffID){
        $sql="SELECT
                a.`StaffID`
                , a.`PersonID`
                , b.`PersonNm` AS `Name`
                , b.`BirthDate` AS DateBirth
                , b.`Gender`
                , b.OfficialEmail AS Email
                , b.OfficialCellPhone AS Handphone
                , a.`WageAmount`
                , a.`WagePeriod`
                , a.`StatusCode`
                , f.`StaffPosPositionID` AS PositionID
            FROM
                ktv_staffs a
                INNER JOIN ktv_persons b ON a.`PersonID` = b.`PersonID`
                LEFT JOIN ktv_staff_positions f ON 1=1
                    AND a.`StaffID` = f.`StaffPosStaffID`
                    AND (CURDATE() BETWEEN f.`StaffPostStart` AND f.`StaffPostEnd`)
            WHERE
                a.`StaffID` = ?
            LIMIT 1";
        $query = $this->db->query($sql, array($StaffID));
        $data = $query->row_array();

        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.Staff.WinFormStaffGeneral-Form-".$key;
            $dataRow[$keyNew] = $value;
        }

        $return['success'] = true;
        $return['data'] = $dataRow;
        return $return;
    }

    public function insertStaffGeneral($paramPost){
        $this->db->trans_begin();

        //ambil ObjID DistrictID nya
        switch ($paramPost['callFromRole']) {
            case 'agent':
                $sql="SELECT
                        kd.DistrictID AS DistrictID
                    FROM
                        ktv_members a
                        LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                        LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                        LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
                    WHERE
                        a.`MemberID` = ?
                    LIMIT 1";
                $query = $this->db->query($sql, array($paramPost['callerObjID']));
                $data = $query->row_array();
                $DistrictID = $data['DistrictID'];

                //get workarea
                $sql="SELECT
                        a.`WorkAreaID`
                    FROM
                        ktv_ref_work_area a
                    WHERE
                        a.`DistrictID` = ?
                    LIMIT 1";
                $query = $this->db->query($sql,array($DistrictID));
                $data = $query->row_array();
                $WorkAreaID = $data['WorkAreaID'];
            break;
            case 'mill':
                $sql="SELECT
                        kd.DistrictID AS DistrictID
                    FROM
                        ktv_mill a
                        LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                        LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                        LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
                    WHERE
                        a.`MillID` = ?
                    LIMIT 1";
                $query = $this->db->query($sql, array($paramPost['callerObjID']));
                $data = $query->row_array();
                $DistrictID = $data['DistrictID'];

                //get workarea
                $sql="SELECT
                        a.`WorkAreaID`
                    FROM
                        ktv_ref_work_area a
                    WHERE
                        a.`DistrictID` = ?
                    LIMIT 1";
                $query = $this->db->query($sql,array($DistrictID));
                $data = $query->row_array();
                $WorkAreaID = $data['WorkAreaID'];
            break;
            case 'refinery':
                $sql="SELECT
                        kd.DistrictID AS DistrictID
                    FROM
                        ktv_refinery a
                        LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                        LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                        LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
                    WHERE
                        a.`RefineryID` = ?
                    LIMIT 1";
                $query = $this->db->query($sql, array($paramPost['callerObjID']));
                $data = $query->row_array();
                $DistrictID = $data['DistrictID'];

                //get workarea
                $sql="SELECT
                        a.`WorkAreaID`
                    FROM
                        ktv_ref_work_area a
                    WHERE
                        a.`DistrictID` = ?
                    LIMIT 1";
                $query = $this->db->query($sql,array($DistrictID));
                $data = $query->row_array();
                $WorkAreaID = $data['WorkAreaID'];
            break;
        }

        // if($WorkAreaID == null){            
        //     $results['success'] = false;
        //     $results['message'] = lang("Please Fill The Region of Data");

        //     return $results;
        // }

        //insert ktv_persons
        $sql="INSERT INTO `ktv_persons` SET
            `WorkAreaID` = ?,
            `PersonNm` = ?,
            `BirthDate` = ?,
            `Gender` = ?,
            `NationalityNm` = 'local',
            Email = ?,
            OfficialEmail = ?,
            OfficialCellPhone = ?,
            `StatusCd` = ?,
            `DateCreated` = NOW(),
            `uid` = random_string(11,null),
            `CreatedBy` = ?";
        $p = array(
            $WorkAreaID,
            $paramPost['Name'],
            $paramPost['DateBirth'],
            $paramPost['Gender'],
            $paramPost['Email'],
            $paramPost['Email'],
            $paramPost['Handphone'],
            $paramPost['StatusCode'],
            $_SESSION['userid']
        );
        $query = $this->db->query($sql, $p);
        $PersonID = $this->db->insert_id();

        //insert ktv_staffs
        $sql="INSERT INTO `ktv_staffs` SET
                `PersonID` = ?,
                `ObjType` = ?,
                `ObjID` = ?,
                `WorkAreaID` = ?,
                `OfficialEmail` = ?,
                `OfficialPhone` = ?,
                `WagePeriod` = ?,
                `WageAmount` = ?,
                `StatusCode` = ?,
                `DateCreated` = NOW(),
                `CreatedBy` = ?
        ";
        $p = array(
            $PersonID,
            $paramPost['callFromRole'],
            $paramPost['callerObjID'],
            $WorkAreaID,
            $paramPost['Email'],
            $paramPost['Handphone'],
            $paramPost['WagePeriod'],
            $paramPost['WageAmount'],
            $paramPost['StatusCode'],
            $_SESSION['userid']
        );
        $query = $this->db->query($sql,$p);
        $StaffID = $this->db->insert_id();

        //insert ke ktv_staff_positions
        $sql="INSERT INTO `ktv_staff_positions` SET
               `StaffPosStaffID` = ?,
               `StaffPosPositionID` = ?,
               `StaffPostStart` = CURDATE(),
               `StaffPostEnd` = '9999-12-31',
               `StatusCode` = 'active',
               `DateCreated` = NOW(),
               `CreatedBy` = ?";
        $p = array(
            $StaffID,
            $paramPost['PositionID'],
            $_SESSION['userid']
        );
        $query = $this->db->query($sql, $p);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to save record";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Record saved";
        }

        return $results;
    }

    public function updateStaffGeneral($paramPost){
        $this->db->trans_begin();

        //update ktv_persons
        $sql="
        UPDATE `ktv_persons` SET
            `PersonNm` = ?,
            `BirthDate` = ?,
            `Gender` = ?,
            Email = ?,
            OfficialEmail = ?,
            OfficialCellPhone = ?,
            `StatusCd` = ?,
            DateUpdated = NOW(),
            UpdatedBy = ?
        WHERE
            PersonID = ?
        LIMIT 1
        ";
        $p = array(
            $paramPost['Name'],
            $paramPost['DateBirth'],
            $paramPost['Gender'],
            $paramPost['Email'],
            $paramPost['Email'],
            $paramPost['Handphone'],
            $paramPost['StatusCode'],
            $_SESSION['userid'],
            $paramPost['PersonID']
        );
        $query = $this->db->query($sql,$p);


        $sql="UPDATE `ktv_staffs` SET
            `OfficialEmail` = ?,
            `OfficialPhone` = ?,
            `WagePeriod` = ?,
            `WageAmount` = ?,
            `StatusCode` = ?,
            DateUpdated = NOW(),
            LastModifiedBy = ?
        WHERE
            StaffID = ?
        LIMIT 1
        ";
        $p = array(
            $paramPost['Email'],
            $paramPost['Handphone'],
            $paramPost['WagePeriod'],
            $paramPost['WageAmount'],
            $paramPost['StatusCode'],
            $_SESSION['userid'],
            $paramPost['StaffID']
        );
        $query = $this->db->query($sql,$p);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to save record";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Record saved";
        }

        return $results;
    }

    public function getPhoneCode(){
        if($_SESSION['is_admin'] == "1"){
            $sqlWhere = " ";
            $sqljoin = "";
        }else{
            $sqljoin = " LEFT JOIN ktv_province as b on b.CountryCode = a.ISO2"
                     . " INNER JOIN ktv_district c ON b.ProvinceID = c.ProvinceID";
            $sqlWhere = " AND c.DistrictID IN (".$this->muser['accessStaff'].")";
        }

        $sql = "
        SELECT
            a.PhoneCode AS id,
            CONCAT('(',  a.PhoneCode, ')', ' - ', a.ISO2) AS label
        FROM
            `ktv_country` as a
            $sqljoin
        WHERE
            a.ISO2 IN ( 'ID','MY')
            $sqlWhere
            GROUP BY a.ISO2 ORDER BY a.CountryName ASC
        ";

        $query = $this->db->query($sql,array());
        $return['data'] = $query->result_array();
        return $return;
    }
}
?>