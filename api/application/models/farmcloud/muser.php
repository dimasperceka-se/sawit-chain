<?php
/******************************************
 *  Author : Fashah Darullah   
 *  Created On : Wed Aug 14 2019
 *  File : mmessages.php
 *******************************************/

defined('BASEPATH') or exit('No direct script access allowed');

class Muser extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function GetFormUser($PersonExtID){
        $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    a.FarmerID PersonExtID
                    , a.FarmerID PersonID
                    , m.MemberName PersonName
                    , m.DateOfBirth
                    , m.HandPhone
                    , CASE
                            WHEN m.HandphoneType = 1 THEN 'Smartphone (Android/iPhone)'
                            WHEN m.HandphoneType = 2 THEN 'Feature Phone (Basic Mobile Phone)'
                            WHEN m.HandphoneType = 3 THEN 'No Handphone'
                            ELSE '-'
                        END as HandphoneType
                    , CASE
                            WHEN m.Gender = 'm' THEN 'Male'
                            WHEN m.Gender = 'f' THEN 'Female'
                            ELSE '-'
                        END AS Gender
                    , m.Email
                    , 'farmer' role
                    , m.Address
                    , m.StatusCode
                    , '' FarmerType
                    , '' isCertified
                    , m.VillageID
                    , vil.Village VillageName
                    , subd.SubDistrict SubDistrictName
                    , dis.District DistrictName
                    , prov.Province ProvinceName
                    , m.Photo
                    , m.Nin NIK
                    , m.Education
                    , m.MaritalStatus
                    , m.HandphoneType HandphoneTypeID
                    , pp.PartnerName GroupName
                    , m.PartnerID
                    , m.DateCollection Date_Load
            FROM
                sys_farmer_user a
            LEFT JOIN
                ktv_members m on m.MemberID = a.FarmerID
            LEFT JOIN
                ktv_village vil on vil.VillageID = m.VillageID
            LEFT JOIN 
                ktv_subdistrict subd on subd.SubDistrictID = vil.SubDistrictID
            LEFT JOIN 
                ktv_district dis on dis.DistrictID = subd.DistrictID
            LEFT JOIN 
                ktv_province prov on dis.ProvinceID = prov.ProvinceID
            LEFT JOIN
                ktv_program_partner pp on pp.PartnerID = m.PartnerID
            WHERE 1=1
                AND a.FarmerID = ?
                AND m.StatusCode = 'active'
                AND a.StatusUser = 'active'
            GROUP BY m.MemberID
        ";
        $query = $this->db->query($sql,array($PersonExtID));

        $result["success"]  = true;
        $result["data"]     = $query->row_array();

        return $result;
    }

    public function GetGridMainUser($textSearch, $start, $limit){
        $sqlwhere = "";
        if($textSearch != ""){
            $sqlwhere .= " AND m.MemberName like '%$textSearch%'";
        }

        if($_SESSION['is_admin'] != "1"){
        	if (!empty($this->user['accessStaff'])) {
	            $sqlWhereAccessStaff = " AND dis.DistrictID IN ({$this->user['accessStaff']})";
			}

			if($this->user['type'] == 'program' || $this->user['type'] == 'private'){
				$sqlWhereAccessStaff .= " AND rt.FarmerType IS NOT NULL ";
			}
        }else{
        	$sqlWhereAccessStaff = "";
        }
        
        $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    a.FarmerID PersonExtID
                    , a.FarmerID PersonID
                    , m.MemberName PersonName
                    , m.DateOfBirth
                    , m.Handphone
                    , CASE
                            WHEN m.HandphoneType = 1 THEN 'Smartphone (Android/iPhone)'
                            WHEN m.HandphoneType = 2 THEN 'Feature Phone (Basic Mobile Phone)'
                            WHEN m.HandphoneType = 3 THEN 'No Handphone'
                            ELSE '-'
                        END as HandphoneType
                    , CASE
                            WHEN m.Gender = 'm' THEN 'Male'
                            WHEN m.Gender = 'f' THEN 'Female'
                            ELSE '-'
                        END AS Gender
                    , m.Email
                    , 'farmer' role
                    , m.Address
                    , m.StatusCode
                    , '' FarmerType
                    , '' isCertified
                    , m.VillageID
                    , vil.Village VillageName
                    , subd.SubDistrict SubDistrictName
                    , dis.District DistrictName
                    , prov.Province ProvinceName
                    , m.Photo
                    , m.Nin NIK
                    , m.Education
                    , m.MaritalStatus
                    , m.HandphoneType HandphoneTypeID
                    , pp.PartnerName GroupName
                    , m.PartnerID
                    , m.DateCollection
                FROM
                    sys_farmer_user a
                LEFT JOIN
                    ktv_members m on m.MemberID = a.FarmerID
                LEFT JOIN
                    ktv_village vil on vil.VillageID = m.VillageID
                LEFT JOIN 
                    ktv_subdistrict subd on subd.SubDistrictID = vil.SubDistrictID
                LEFT JOIN 
                    ktv_district dis on dis.DistrictID = subd.DistrictID
                LEFT JOIN 
                    ktv_province prov on dis.ProvinceID = prov.ProvinceID
                LEFT JOIN
                    ktv_program_partner pp on pp.PartnerID = m.PartnerID
                WHERE 1=1
                    AND m.StatusCode = 'active'
                    AND a.StatusUser = 'active'
                $sqlwhere
                $sqlWhereAccessStaff
                GROUP BY m.MemberID
                ORDER BY m.MemberID
                LIMIT ?,?";
        $p = array(
            (int) $start, (int) $limit
        );

        $query = $this->db->query($sql, $p);
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

    	return $result;
    }

    public function GetUseraccGridMain($pSearch,$start,$limit,$sortingField,$sortingDir, $opsiCall = 'no_limit') {
        $result = array();
        if ($sortingField == "") $sortingField = 'MemberName';
        if ($sortingDir == "") $sortingDir = 'ASC';

        $sqlSelect = " b.MemberID FarmerID
                        , b.MemberName
                        , b.`Gender`
                        , CONCAT(cpg.`FarmerGroupID`,' - ',cpg.`GroupName`) AS GroupName
                        , a.`Username`
                        , a.`Email`
                        , b.HandPhone
                        , dis.`District`
                        , subd.`SubDistrict`
                        , CONCAT(part.`PartnerName`,' - ',rt.`FarmerType`) AS Partner
                        , a.`StatusUser` AS StatusAccount ";
        $sqlSelectCount = " COUNT(*) AS total ";
        $sqlOrderLimit = " LIMIT ?,? ";

        $sqlMain = "SELECT
                    -- sqlSelect --
                FROM
                    sys_farmer_user a
                    INNER JOIN ktv_members b ON a.`FarmerID` = b.`MemberID`
                    LEFT JOIN ktv_farmer_group cpg ON b.`FarmerGroupID` = cpg.`FarmerGroupID`
                    LEFT JOIN ktv_member_farmer_type t ON t.FarmerID = b.MemberID
                    LEFT JOIN ktv_ref_farmer_type rt ON rt.FarmertypeID = t.FarmertypeID
                    LEFT JOIN ktv_program_partner part ON rt.`PartnerID` = part.`PartnerID`
                    LEFT JOIN ktv_village vil ON vil.VillageID = b.VillageID
                    LEFT JOIN ktv_subdistrict subd ON subd.SubDistrictID = vil.SubDistrictID
                    LEFT JOIN ktv_district dis ON dis.DistrictID = subd.DistrictID
                    LEFT JOIN ktv_province prov ON dis.ProvinceID = prov.ProvinceID
                WHERE 1=1
                    AND (b.`MemberName` LIKE ? OR b.`MemberID` LIKE ?)
                    AND b.StatusCode = 'active'
                ORDER BY `$sortingField` $sortingDir 
                -- sqlOrderLimit --
                ";

        $sqlList = str_replace("-- sqlSelect --", $sqlSelect, $sqlMain);

        if ($opsiCall == 'limit') {
            $sqlList = str_replace("-- sqlOrderLimit --", $sqlOrderLimit, $sqlList);
            $p = array(
                '%' . $pSearch['KeySearch'] . '%', '%' . $pSearch['KeySearch'] . '%', $start, $limit
            );
            $query = $this->db->query($sqlList, $p);
        } else {
            $sqlList = str_replace("-- sqlOrderLimit --", '', $sqlList);
            $p = array(
                '%' . $pSearch['KeySearch'] . '%', '%' . $pSearch['KeySearch'] . '%'
            );
            $query = $this->db->query($sqlList, $p);
        }
        $result['data'] = $query->result_array();

        $sqlTotal = str_replace("-- sqlSelect --",$sqlSelectCount,$sqlMain);
        $sqlTotal = str_replace("-- sqlOrderLimit --","",$sqlTotal);
        $query = $this->db->query($sqlTotal, array('%' . $pSearch['KeySearch'] . '%', '%' . $pSearch['KeySearch'] . '%'));
        $result['total'] = $query->row()->total;

        return $result;
    }

    public function GetComboAutoFarmerSearch($queryString,$start,$limit) {
        $result = array();

        $sqlSelect = " CONCAT(far.MemberID, ' - ', far.`MemberName`, ' (', IFNULL(part.PartnerName,''),'-', IFNULL(rt.FarmerType, ''),') ') AS label
        , far.MemberID FarmerID
        , far.MemberName FarmerName
        , CONCAT(part.`PartnerName`,' - ',rt.`FarmerType`) AS PartnerLabel
        , rt.`PartnerID`
        , prov.Province
        , dis.`District`
        , subd.`SubDistrict`
        , vil.Village
        , far.`MemberID` AS Username
        , CONCAT(far.MemberID,'@palmoiltrace.com') AS Email
        , far.`HandPhone` AS Handphone ";
        $sqlSelectCount = " COUNT(*) AS total ";
        $sqlOrderLimit = " ORDER BY far.`MemberID` LIMIT ?,? ";

        $sqlMain = "SELECT
                        -- sqlSelect --
                    FROM
                        ktv_members far
                        LEFT JOIN ktv_member_farmer_type t ON t.FarmerID = far.MemberID
                        LEFT JOIN ktv_ref_farmer_type rt ON rt.FarmertypeID = t.FarmertypeID
                        LEFT JOIN ktv_program_partner part ON rt.`PartnerID` = part.`PartnerID`
                        LEFT JOIN sys_farmer_user sfar ON far.`MemberID` = sfar.`FarmerID`
                        LEFT JOIN ktv_village vil ON vil.VillageID = far.VillageID
                        LEFT JOIN ktv_subdistrict subd ON subd.SubDistrictID = vil.SubDistrictID
                        LEFT JOIN ktv_district dis ON dis.DistrictID = subd.DistrictID
                        LEFT JOIN ktv_province prov ON dis.ProvinceID = prov.ProvinceID
                    WHERE 1=1
                        AND far.`StatusCode` = 'active'
                        AND sfar.`FarmerID` IS NULL
                        AND ( (far.`MemberID` LIKE ?) OR (far.`MemberName` LIKE ?) )
                    -- sqlOrderLimit --
                    ";

        $sqlList = str_replace("-- sqlSelect --",$sqlSelect,$sqlMain);
        $sqlList = str_replace("-- sqlOrderLimit --",$sqlOrderLimit,$sqlList);
        $p = array(
            '%' . $queryString . '%','%' . $queryString . '%', $start, $limit
        );
        $query = $this->db->query($sqlList, $p);
        $result['data'] = $query->result_array();
        $result['sql'] = $this->db->last_query();

        $sqlTotal = str_replace("-- sqlSelect --",$sqlSelectCount,$sqlMain);
        $sqlTotal = str_replace("-- sqlOrderLimit --","",$sqlTotal);
        $query = $this->db->query($sqlTotal, array('%' . $queryString . '%','%' . $queryString . '%'));
        $result['total'] = $query->row()->total;

        return $result;
    }
}