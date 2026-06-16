<?php
/**
 * @Author: nikolius
 * @Date:   2016-08-19 17:23:02
 */
class Mprofile extends CI_Model
{
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

    public function getFarmerSceStaff($sce_id){
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
        $query = $this->db->query($sql, array($sce_id));
        return $query->result_array();
    }

}
?>