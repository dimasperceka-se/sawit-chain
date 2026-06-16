<?php
class MFarmer extends CI_Model {
    public $user;

    public function __construct()
    {
        parent::__construct();
        $this->user = $this->muserprofile->getUserProfile();
    }
    function readDatas($prov,$key,$dist,$subdist,$start,$limit){
        $add = '';
        // if (substr($kab,0,1)=='[') $kab = str_replace("[","",str_replace("]","",$kab));
        if ($key!='') $add .= "and b.FarmerName like '%$key%'";
        // if ($kab!='' and $kab!='null') $add .= " and District in ($kab)";
        if (!empty($prov)) {
            $add .= " AND f.ProvinceID = {$prov}";
        }
        if (!empty($dist)) {
            $add .= " AND f.DistrictID = {$dist}";
        }
        if (!empty($subdist)) {
            $add .= " AND e.SubDistrictID = {$subdist}";
        }
        if (!empty($this->user['district_access'])) {
            $add .= " AND f.DistrictID IN ({$this->user['district_access']})";
        }
        if (!empty($_SESSION['FlagAccess'])) {
            $add .= " AND b.CPGid IN (SELECT cp.CPGid FROM ktv_cpg_partner cp WHERE cp.PartnerID = {$_SESSION['PartnerID']})";
        }

        $sql_cek = "select SceID from sce_farmer_staff where UserId=?";
        $query = $this->db->query($sql_cek, array($_SESSION['userid']));
        $cek = $query->result_array();
        if ($cek[0]['SceID']!='') $add .= " and a.SceID=".$cek[0]['SceID'];

        $sql = "
            SELECT SQL_CALC_FOUND_ROWS 
                SceID sce_id, a.FarmerID,b.FarmerName,c.GroupName,Village Desa,b.Photo,
                SubDistrict Kecamatan,b.DateUpdated,b.DateSurvey,a.Latitude,a.Longitude,IF(g.SupplychainID IS NULL,'No','Yes') AS asBuyingUnit,IF(h.ClonalID IS NULL,'No','Yes') AS haveClonal,IF(i.NurseryID IS NULL,'No','Yes') AS haveNursery,IF(j.CompostID IS NULL,'No','Yes') AS haveCompost
            from sce_farmer a
            left join ktv_farmer b on a.FarmerID=b.FarmerID
            left join ktv_cpg c on b.CPGid=c.CPGid
            left join ktv_village d on b.VillageID=d.VillageID
            left join ktv_subdistrict e on d.SubDistrictID=e.SubDistrictID
            left join ktv_district f on e.DistrictID=f.DistrictID
            LEFT JOIN ktv_supplychain_org g ON a.SceID = g.OrgID AND g.OrgType = 'sce'
            LEFT JOIN ktv_clonal_garden h ON a.FarmerID = h.ObjID AND h.ObjType = 'farmer'
            LEFT JOIN ktv_nursery i ON a.FarmerID = i.ObjID AND i.ObjType = 'farmer'
            LEFT JOIN ktv_compost j ON a.FarmerID = j.ObjID AND j.ObjType = 'farmer'
            WHERE 1 = 1 AND a.StatusCode != 'nullified' %s
            GROUP BY a.SceID
            ORDER BY b.FarmerName
            LIMIT ?,?";
        $query = $this->db->query(sprintf($sql, $add),array((int)$start,(int)$limit));
        $result['data'] = $query->result_array();
        $query = $this->db->query("SELECT FOUND_ROWS() AS total");
        $result['total'] = $query->row(0)->total;
        return $result;
    }
    function readData($id){
        $sql = "
            select SceID sce_id, a.FarmerID,b.FarmerName,c.GroupName,Village Desa,SubDistrict Kecamatan
            from sce_farmer a
            left join ktv_farmer b on a.FarmerID=b.FarmerID
            left join ktv_cpg c on b.CPGid=c.CPGid
            left join ktv_village d on b.VillageID=d.VillageID
            left join ktv_subdistrict e on d.SubDistrictID=e.SubDistrictID
            left join ktv_district f on e.DistrictID=f.DistrictID
            WHERE SceID=?";
        $query = $this->db->query($sql,array($id));
        $result = $query->result_array();
        return $result[0];
    }

    function readFarmers($prov,$kab,$key,$start,$limit){
      if ($prov!='') $add = "and kp.ProvinceID='$prov'";
      if (substr($kab,0,1)=='[') $kab = str_replace("[","",str_replace("]","",$kab));
      if ($kab!='' and $kab!='null') $add .= " and District in ($kab)";

      $sql = "
            SELECT %s
            FROM ktv_farmer kcf
            LEFT JOIN sce_farmer sf ON kcf.FarmerID=sf.FarmerID
            LEFT JOIN ktv_cpg kc ON kcf.CPGid=kc.CPGid
            LEFT JOIN ktv_village kv ON kv.VillageID=kcf.VillageID
            LEFT JOIN ktv_subdistrict ks ON ks.SubDistrictID=kv.SubDistrictID
            LEFT JOIN ktv_district kd ON ks.`DistrictID` = kd.`DistrictID`
            LEFT JOIN ktv_province kp ON kd.`ProvinceID` = kp.`ProvinceID`
            WHERE sf.FarmerID is null and kcf.`StatusCode` != 'nullified' and concat(kcf.FarmerID,kcf.FarmerName) like ? %s";

         $fieldSelect = 'kcf.FarmerID id,
                        FarmerName name,
                        GroupName grup,
                        SubDistrict sub_district,
                        kd.`District` AS district,
                        kp.`Province` AS province,
                        Village village,
                        Photo photo,
                        CONCAT(kcf.FarmerID," - ",FarmerName) AS displayField,
                        kcf.`Address` AS address,
                        kcf.HandPhone AS handphone,
                        CONCAT(UCASE(SUBSTRING(kcf.`StatusCode`, 1, 1)),LCASE(SUBSTRING(kcf.`StatusCode`, 2))) AS `status`
                        ';

        $query = $this->db->query(sprintf($sql,$fieldSelect, $add.' LIMIT ?,?'),array("%$key%",(int)$start,(int)$limit));

        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql,'count(*) as total',$add), array("%$key%"));
        $result['total'] = $query->row()->total;
        return $result;
    }

    public function farmerSceById($sce_id){
        $sql="SELECT
                sf.SceID AS sce_id,
                sf.Latitude,
                sf.Longitude,
                kcf.FarmerID id,
                FarmerName NAME,
                GroupName grup,
                SubDistrict sub_district,
                kd.`District` AS district,
                kp.`Province` AS province,
                Village village,
                Photo photo,
                CONCAT(kcf.FarmerID,\" - \",FarmerName) AS displayField,
                kcf.`Address` AS address,
                kcf.HandPhone AS handphone,
                CONCAT(UCASE(SUBSTRING(kcf.`StatusCode`, 1, 1)),LCASE(SUBSTRING(kcf.`StatusCode`, 2))) AS `status`
            FROM
                ktv_farmer kcf
                LEFT JOIN sce_farmer sf ON kcf.FarmerID=sf.FarmerID
                LEFT JOIN ktv_cpg kc ON kcf.CPGid=kc.CPGid
                LEFT JOIN ktv_village kv ON kv.VillageID=kcf.VillageID
                LEFT JOIN ktv_subdistrict ks ON ks.SubDistrictID=kv.SubDistrictID
                LEFT JOIN ktv_district kd ON ks.`DistrictID` = kd.`DistrictID`
                LEFT JOIN ktv_province kp ON kd.`ProvinceID` = kp.`ProvinceID`
            WHERE
                sf.`SceID` = ?
            LIMIT 1";
        $query = $this->db->query($sql,array($sce_id));
        $data = $query->result_array();
        return $data[0];
    }

    function readStaff($SceID){
        $sql = "SELECT
    s.SceID,
    s.StaffID,
    p.UserID,
    p.PersonNm AS StaffName,
    p.OfficialCellPhone AS Phone,
    p.OfficialEmail AS Email,
    p.BirthDate AS StaffBirthday,
    p.Gender AS StaffGender,
    IF(p.Gender='1','Laki-laki',IF(p.Gender='2','Perempuan','')) StaffGende,
    s.Position
FROM ktv_persons p
JOIN sce_farmer_staff s ON s.PersonID = p.PersonID
WHERE s.SceID=? AND p.StatusCd != 'nullified'";
        $query = $this->db->query($sql, array($SceID));
        return $query->result_array();
    }
    function createStaff($SceID,$StaffName,$Position,$Phone,$Email,$StaffBirthday,$StaffGender){
        $sql_user = "
            INSERT INTO sys_user(UserName,UserRealName,UserActive,   UserAddUserId,UserAddTime)
            VALUES (?,?,'No',   ?,now())";
        $this->db->query($sql_user, array($Email,$StaffName,$_SESSION['userid']));
        $sql = "
            INSERT INTO sce_farmer_staff(SceID,StaffName,UserId,Position,Phone,Email,StaffBirthday,StaffGender,
               DateCreated,CreatedBy)
            VALUES (?,?,?,?,?,?,?,?,   now(),?)";
        $query = $this->db->query($sql, array($SceID,$StaffName,$this->db->insert_id(),$Position,$Phone,$Email,$StaffBirthday,
            $StaffGender,$_SESSION['userid']));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }
    function updateStaff($StaffName,$Position,$Phone,$Email,$StaffBirthday,$StaffGender,$id){
        $sql = "
            UPDATE sce_farmer_staff
            SET StaffName=?,Position=?,Phone=?,Email=?,StaffBirthday=?,StaffGender=?
            WHERE StaffID=?";
        $query = $this->db->query($sql, array($StaffName,$Position,$Phone,$Email,$StaffBirthday,$StaffGender,$id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }
    function deleteStaff($id){
        //$sql = "DELETE FROM sce_farmer_staff WHERE StaffID=?";
        $sql="UPDATE sce_farmer_staff SET StatusCode = 'nullified',LastModifiedBy='".$_SESSION['userid']."',DateUpdated=NOW() WHERE StaffID = ? LIMIT 1";
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
    function createSce($FarmerID,$Latitude,$Longitude){
        $sql = "
            INSERT INTO sce_farmer(SceID,FarmerID,Latitude,Longitude,StatusCode,DateCreated,CreatedBy)
            SELECT max(SceId)+1,?,?,?,'active',now(),? from sce_farmer";
        $query = $this->db->query($sql, array($FarmerID,$Latitude,$Longitude,$_SESSION['userid']));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
            $results['sce_id'] = $this->db->insert_id();
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }
    function updateSce($FarmerID,$Latitude,$Longitude,$id){
        $sql = "
            UPDATE sce_farmer
            SET FarmerID=?,Latitude=?,Longitude=?,DateUpdated=now(),LastModifiedBy=?
            WHERE SceID=?";
        $query = $this->db->query($sql, array($FarmerID,$Latitude,$Longitude,$_SESSION['userid'],$id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }
    function deleteSce($id){
        $sql = "
            UPDATE sce_farmer SET
            StatusCode='nullified',
            LastModifiedBy = '".$_SESSION['userid']."',
            DateUpdated = NOW()
            WHERE SceID=? LIMIT 1";
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

    public function listSCE($province=null,$district=null,$subdistrict=null)
    {
        $this->db->select('c.SceID AS id, f.FarmerName AS name');
        $this->db->from('sce_farmer c');
        $this->db->join('ktv_farmer f', 'f.FarmerID = c.FarmerID', 'inner');
        $this->db->join('ktv_village v', 'v.VillageID = f.VillageID');
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


}
?>
