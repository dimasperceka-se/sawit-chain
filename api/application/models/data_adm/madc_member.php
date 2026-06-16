<?php
/**
 * @Author: nikolius
 * @Date:   2017-10-10 12:11:40
 * @Last Modified by:   nikolius
 * @Last Modified time: 2017-10-11 15:50:54
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Madc_member extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getGridSetByMember($pSearch, $start, $limit, $sortingField, $sortingDir){

        if ($sortingField == "")
            $sortingField = 'Name';
        if ($sortingDir == "")
            $sortingDir = 'ASC';

        //sqlFilterRole
        if($pSearch['MemberType'] == "Farmer"){
            $sqlFilterRole = " AND mrole.MRoleID = '1' ";
        } elseif ($pSearch['MemberType'] == "Agent"){
            $sqlFilterRole = " AND mrole.MRoleID IN (5,6,7,8,9,10) ";
        } else {
            $sqlFilterRole = "";
        }

        $sql="SELECT
                SQL_CALC_FOUND_ROWS
                a.`MemberID` AS MemberIDInc
                , a.`MemberDisplayID` AS id
                , a.`MemberName` AS `Name`
                , subd.`SubDistrict` AS Kecamatan
                , vil.Village AS Desa
                , GROUP_CONCAT(DISTINCT p.PartnerName ORDER BY p.PartnerName) AS PartnerAccess
            FROM
                ktv_members a
                LEFT JOIN ktv_village vil ON a.`VillageID` = vil.VillageID
                LEFT JOIN ktv_subdistrict subd ON vil.SubDistrictID = subd.`SubDistrictID`
                LEFT JOIN ktv_district kd ON kd.`DistrictID` = subd.`DistrictID`
                LEFT JOIN ktv_member_role mrole ON a.MemberID = mrole.MemberID
                LEFT JOIN `ktv_access_partner_member` apm ON a.`MemberID` = apm.apmMemberID
                LEFT JOIN ktv_program_partner p ON apm.apmPartnerID = p.PartnerID
            WHERE
                a.`StatusCode` = 'active'
                AND a.`MemberDisplayID` LIKE ?
                AND a.`MemberName` LIKE ?
                AND ( (kd.ProvinceID = ?) OR ('' = ?) )
                AND ( (kd.DistrictID = ?) OR ('' = ?) )
                AND ( (subd.SubDistrictID = ?) OR ('' = ?) )
                AND ( (a.`VillageID` = ?) OR ('' = ?) )
                $sqlFilterRole
            GROUP BY a.`MemberID`
            ORDER BY $sortingField $sortingDir
            LIMIT ?,?";
        $p = array(
            '%'.$pSearch['MemberID'].'%',
            '%'.$pSearch['MemberName'].'%',
            $pSearch['ProvinceID'],$pSearch['ProvinceID'],
            $pSearch['DistrictID'],$pSearch['DistrictID'],
            $pSearch['SubDistrictID'],$pSearch['SubDistrictID'],
            $pSearch['VillageID'],$pSearch['VillageID'],
            (int) $start, (int) $limit
        );
        $query = $this->db->query($sql,$p);
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    public function getGridSetDataControl($arrMemberIDSelected, $start, $limit, $sortingField, $sortingDir){
        if ($sortingField == "")
            $sortingField = 'Name';
        if ($sortingDir == "")
            $sortingDir = 'ASC';

        $MemberIDSelected = implode(",",$arrMemberIDSelected);
        $sqlWhere = " a.MemberID IN ($MemberIDSelected) ";

        $sql="SELECT
                SQL_CALC_FOUND_ROWS
                a.`MemberID` AS MemberIDInc
                , a.`MemberDisplayID` AS id
                , a.`MemberName` AS `Name`
                , subd.`SubDistrict` AS Kecamatan
                , vil.Village AS Desa
                , GROUP_CONCAT(DISTINCT p.PartnerName ORDER BY p.PartnerName) AS PartnerAccess
            FROM
                ktv_members a
                LEFT JOIN ktv_village vil ON a.`VillageID` = vil.VillageID
                LEFT JOIN ktv_subdistrict subd ON vil.SubDistrictID = subd.`SubDistrictID`
                LEFT JOIN ktv_member_role mrole ON a.MemberID = mrole.MemberID
                LEFT JOIN `ktv_access_partner_member` apm ON a.`MemberID` = apm.apmMemberID
                LEFT JOIN ktv_program_partner p ON apm.apmPartnerID = p.PartnerID
            WHERE
                $sqlWhere
            GROUP BY a.`MemberID`
            ORDER BY $sortingField $sortingDir
            LIMIT ?,?";
        $p = array(
            (int) $start, (int) $limit
        );
        $query = $this->db->query($sql,$p);
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    public function updateDataControl($arrMemberIDSelected,$arrPartnerAccess){
        $this->db->trans_begin();

        //delete dl datanya, setelah itu baru insert lagi
        $MemberIDSelected = implode(",",$arrMemberIDSelected);

        $sql="DELETE FROM `ktv_access_partner_member` WHERE apmMemberID IN ($MemberIDSelected)";
        $query = $this->db->query($sql);

        for ($i=0; $i < count($arrMemberIDSelected); $i++) {
            for ($j=0; $j < count($arrPartnerAccess); $j++) {
                $sql="INSERT INTO `ktv_access_partner_member` SET
                        `apmPartnerID` = ?,
                        `apmMemberID` = ?,
                        `DateCreated` = NOW(),
                        `CreatedBy` = ?";
                $p = array(
                    $arrPartnerAccess[$j],
                    $arrMemberIDSelected[$i],
                    $_SESSION['userid']
                );
                $query = $this->db->query($sql,$p);
            }
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to update data";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Data updated";
        }

        return $results;
    }

    public function getGridSetDataControlByRegion($pSearch, $start, $limit, $sortingField, $sortingDir){
        if ($sortingField == "")
            $sortingField = 'Name';
        if ($sortingDir == "")
            $sortingDir = 'ASC';

        //SQL Filter ================================= (Begin)
        if($pSearch['ProvinceID'] != ""){
            $sqlFilter .= " AND kp.ProvinceID = '{$pSearch['ProvinceID']}' ";
        }
        if($pSearch['DistrictID'] != ""){
            $sqlFilter .= " AND kd.DistrictID = '{$pSearch['DistrictID']}' ";
        }
        if($pSearch['SubDistrictID'] != ""){
            $sqlFilter .= " AND subd.SubDistrictID = '{$pSearch['SubDistrictID']}' ";
        }
        if($pSearch['VillageID'] != ""){
            $sqlFilter .= " AND a.VillageID = '{$pSearch['VillageID']}' ";
        }

        //sqlFilterRole
        if($pSearch['MemberType'] == "Farmer"){
            $sqlFilterRole = " AND mrole.MRoleID = '1' ";
        } elseif ($pSearch['MemberType'] == "Agent"){
            $sqlFilterRole = " AND mrole.MRoleID IN (5,6,7,8,9,10) ";
        } else {
            $sqlFilterRole = "";
        }
        //SQL Filter ================================= (End)

        $sql="SELECT
                SQL_CALC_FOUND_ROWS
                a.`MemberID` AS MemberIDInc
                , a.`MemberDisplayID` AS id
                , a.`MemberName` AS `Name`
                , subd.`SubDistrict` AS Kecamatan
                , vil.Village AS Desa
                , GROUP_CONCAT(DISTINCT p.PartnerName ORDER BY p.PartnerName) AS PartnerAccess
            FROM
                ktv_members a
                LEFT JOIN ktv_village vil ON a.`VillageID` = vil.VillageID
                LEFT JOIN ktv_subdistrict subd ON vil.SubDistrictID = subd.`SubDistrictID`
                LEFT JOIN ktv_district kd ON kd.DistrictID = subd.DistrictID
                LEFT JOIN ktv_province kp ON kp.ProvinceID = kd.ProvinceID
                LEFT JOIN ktv_member_role mrole ON a.MemberID = mrole.MemberID
                LEFT JOIN `ktv_access_partner_member` apm ON a.`MemberID` = apm.apmMemberID
                LEFT JOIN ktv_program_partner p ON apm.apmPartnerID = p.PartnerID
            WHERE
                a.StatusCode = 'active'
                $sqlFilter
                $sqlFilterRole
            GROUP BY a.`MemberID`
            ORDER BY $sortingField $sortingDir
            LIMIT ?,?";
        $p = array(
            (int) $start, (int) $limit
        );
        $query = $this->db->query($sql,$p);
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    public function updateDataControlByRegion($ProvinceID,$DistrictID,$SubDistrictID,$VillageID,$MemberType,$arrPartnerAccess){
        $this->db->trans_begin();

        //ambil dl MemberID yg perlu diproses ===================================== (begin)
        //SQL Filter ================================= (Begin)
        if($ProvinceID != ""){
            $sqlFilter .= " AND kp.ProvinceID = '{$ProvinceID}' ";
        }
        if($DistrictID != ""){
            $sqlFilter .= " AND kd.DistrictID = '{$DistrictID}' ";
        }
        if($SubDistrictID != ""){
            $sqlFilter .= " AND subd.SubDistrictID = '{$SubDistrictID}' ";
        }
        if($VillageID != ""){
            $sqlFilter .= " AND a.VillageID = '{$VillageID}' ";
        }

        //sqlFilterRole
        if($MemberType == "Farmer"){
            $sqlFilterRole = " AND mrole.MRoleID = '1' ";
        } elseif ($MemberType == "Agent"){
            $sqlFilterRole = " AND mrole.MRoleID IN (5,6,7,8,9,10) ";
        } else {
            $sqlFilterRole = "";
        }
        //SQL Filter ================================= (End)

        $sql="
            SELECT
                GROUP_CONCAT(tbl_grouped.MemberID SEPARATOR ',') AS MemberIDString
            FROM
            (
                SELECT
                    a.`MemberID`
                FROM
                    ktv_members a
                    LEFT JOIN ktv_village vil ON a.`VillageID` = vil.VillageID
                    LEFT JOIN ktv_subdistrict subd ON vil.SubDistrictID = subd.`SubDistrictID`
                    LEFT JOIN ktv_district kd ON kd.DistrictID = subd.DistrictID
                    LEFT JOIN ktv_province kp ON kp.ProvinceID = kd.ProvinceID
                    LEFT JOIN ktv_member_role mrole ON a.MemberID = mrole.MemberID
                    LEFT JOIN `ktv_access_partner_member` apm ON a.`MemberID` = apm.apmMemberID
                    LEFT JOIN ktv_program_partner p ON apm.apmPartnerID = p.PartnerID
                WHERE
                    a.StatusCode = 'active'
                    $sqlFilter
                    $sqlFilterRole
                GROUP BY a.`MemberID`
            ) AS tbl_grouped
            ";
        $query = $this->db->query($sql);
        $data = $query->row_array();
        $MemberIDString = $data['MemberIDString'];

        $arrMemberIDSelected = explode(",",$MemberIDString);
        //ambil dl MemberID yg perlu diproses ===================================== (end)

        //delete dulu, habis itu baru di insertkan
        $sql="DELETE FROM `ktv_access_partner_member` WHERE apmMemberID IN ($MemberIDString)";
        $query = $this->db->query($sql);

        for ($i=0; $i < count($arrMemberIDSelected); $i++) {
            for ($j=0; $j < count($arrPartnerAccess); $j++) {
                $sql="INSERT INTO `ktv_access_partner_member` SET
                        `apmPartnerID` = ?,
                        `apmMemberID` = ?,
                        `DateCreated` = NOW(),
                        `CreatedBy` = ?";
                $p = array(
                    $arrPartnerAccess[$j],
                    $arrMemberIDSelected[$i],
                    $_SESSION['userid']
                );
                $query = $this->db->query($sql,$p);
            }
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to update data";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Data updated";
        }

        return $results;
    }

    public function getGridMemberNotAssignYet($start, $limit, $sortingField, $sortingDir){
        if ($sortingField == "")
            $sortingField = 'Name';
        if ($sortingDir == "")
            $sortingDir = 'ASC';

        $sql="SELECT
                a.`MemberDisplayID` AS id
                , a.`MemberName` AS `Name`
                , subd.`SubDistrict` AS Kecamatan
                , vil.Village AS Desa
                , dis.District
                , prov.Province
                , mrole.MRoleID
                , CASE
                    WHEN mrole.MRoleID = '1' THEN 'Farmer'
                    WHEN mrole.MRoleID = '5' THEN 'Agent'
                    WHEN mrole.MRoleID = '6' THEN 'Agent'
                    WHEN mrole.MRoleID = '7' THEN 'Agent'
                    WHEN mrole.MRoleID = '8' THEN 'Agent'
                    WHEN mrole.MRoleID = '9' THEN 'Agent'
                    WHEN mrole.MRoleID = '10' THEN 'Agent'
                END AS MemberType
            FROM
                ktv_members a
                LEFT JOIN ktv_village vil ON a.VillageID = vil.VillageID
                LEFT JOIN ktv_subdistrict subd ON vil.SubDistrictID = subd.SubDistrictID
                LEFT JOIN ktv_district dis ON subd.DistrictID = dis.DistrictID
                LEFT JOIN ktv_province prov ON dis.ProvinceID = prov.ProvinceID
                INNER JOIN ktv_member_role mrole ON a.MemberID = mrole.MemberID
                LEFT JOIN ktv_access_partner_member apm ON a.MemberID = apm.apmMemberID
            WHERE
                a.`StatusCode` = 'active'
                AND apm.apmPartnerID IS NULL
            GROUP BY a.`MemberID`
            ORDER BY $sortingField $sortingDir
            LIMIT ?,?";
        $p = array(
            (int) $start, (int) $limit
        );
        $query = $this->db->query($sql,$p);
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

}
?>