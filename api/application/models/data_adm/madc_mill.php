<?php

/**
 * @Author: nikolius
 * @Date:   2017-10-11 16:47:58
 * @Last Modified by:   nikolius
 * @Last Modified time: 2017-10-11 18:25:43
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Madc_mill extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getGridSetByMill($pSearch, $start, $limit, $sortingField, $sortingDir){
        if ($sortingField == "")
            $sortingField = 'Name';
        if ($sortingDir == "")
            $sortingDir = 'ASC';

        $sql="SELECT
                SQL_CALC_FOUND_ROWS
                a.`MillID`
                , a.`MillDisplayID` AS id
                , a.`MillName` AS `Name`
                , subd.`SubDistrict` AS Kecamatan
                , vil.Village AS Desa
                , GROUP_CONCAT(DISTINCT p.PartnerName ORDER BY p.PartnerName) AS PartnerAccess
            FROM
                ktv_mill a
                LEFT JOIN ktv_village vil ON a.`VillageID` = vil.VillageID
                LEFT JOIN ktv_subdistrict subd ON vil.SubDistrictID = subd.`SubDistrictID`
                left join ktv_district kd on kd.`DistrictID` = subd.`DistrictID`
                LEFT JOIN `ktv_access_partner_mill` apml ON a.`MillID` = apml.apmiMillID
                LEFT JOIN ktv_program_partner p ON apml.apmiPartnerID = p.PartnerID
            WHERE
                a.`StatusCode` = 'active'
                AND a.`MillDisplayID` LIKE ?
                AND a.`MillName` LIKE ?
                AND ( (kd.ProvinceID = ?) OR ('' = ?) )
                AND ( (kd.DistrictID = ?) OR ('' = ?) )
                AND ( (subd.SubDistrictID = ?) OR ('' = ?) )
                AND ( (a.`VillageID` = ?) OR ('' = ?) )
            GROUP BY a.`MillID`
            ORDER BY $sortingField $sortingDir
            LIMIT ?,?";
        $p = array(
            '%'.$pSearch['MillID'].'%',
            '%'.$pSearch['MillName'].'%',
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

    public function getGridSetDataControl($arrMillIDSelected, $start, $limit, $sortingField, $sortingDir){
        if ($sortingField == "")
            $sortingField = 'Name';
        if ($sortingDir == "")
            $sortingDir = 'ASC';

        $MillIDSelected = implode(",",$arrMillIDSelected);
        $sqlWhere = " a.MillID IN ($MillIDSelected) ";

        $sql="SELECT
                SQL_CALC_FOUND_ROWS
                a.`MillID`
                , a.`MillDisplayID` AS id
                , a.`MillName` AS `Name`
                , subd.`SubDistrict` AS Kecamatan
                , vil.Village AS Desa
                , GROUP_CONCAT(DISTINCT p.PartnerName ORDER BY p.PartnerName) AS PartnerAccess
            FROM
                ktv_mill a
                LEFT JOIN ktv_village vil ON a.`VillageID` = vil.VillageID
                LEFT JOIN ktv_subdistrict subd ON vil.SubDistrictID = subd.`SubDistrictID`
                LEFT JOIN `ktv_access_partner_mill` apml ON a.`MillID` = apml.apmiMillID
                LEFT JOIN ktv_program_partner p ON apml.apmiPartnerID = p.PartnerID
            WHERE
                $sqlWhere
            GROUP BY a.`MillID`
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

    public function updateDataControl($arrMillIDSelected,$arrPartnerAccess){
        $this->db->trans_begin();

        //delete dl datanya, setelah itu baru insert lagi
        $MillIDSelected = implode(",",$arrMillIDSelected);

        $sql="DELETE FROM `ktv_access_partner_mill` WHERE apmiMillID IN ($MillIDSelected)";
        $query = $this->db->query($sql);

        for ($i=0; $i < count($arrMillIDSelected); $i++) {
            for ($j=0; $j < count($arrPartnerAccess); $j++) {
                $sql="INSERT INTO `ktv_access_partner_mill` SET
                        `apmiPartnerID` = ?,
                        `apmiMillID` = ?,
                        `DateCreated` = NOW(),
                        `CreatedBy` = ?";
                $p = array(
                    $arrPartnerAccess[$j],
                    $arrMillIDSelected[$i],
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

        $sql="SELECT
                SQL_CALC_FOUND_ROWS
                a.`MillID`
                , a.`MillDisplayID` AS id
                , a.`MillName` AS `Name`
                , subd.`SubDistrict` AS Kecamatan
                , vil.Village AS Desa
                , GROUP_CONCAT(DISTINCT p.PartnerName ORDER BY p.PartnerName) AS PartnerAccess
            FROM
                ktv_mill a
                LEFT JOIN ktv_village vil ON a.`VillageID` = vil.VillageID
                LEFT JOIN ktv_subdistrict subd ON vil.SubDistrictID = subd.`SubDistrictID`
                left join ktv_district kd on kd.`DistrictID` = subd.`DistrictID`
                LEFT JOIN `ktv_access_partner_mill` apml ON a.`MillID` = apml.apmiMillID
                LEFT JOIN ktv_program_partner p ON apml.apmiPartnerID = p.PartnerID
            WHERE
                a.`StatusCode` = 'active'
                AND ( (kd.ProvinceID = ?) OR ('' = ?) )
                AND ( (kd.DistrictID = ?) OR ('' = ?) )
                AND ( (subd.SubDistrictID = ?) OR ('' = ?) )
                AND ( (a.`VillageID` = ?) OR ('' = ?) )
            GROUP BY a.`MillID`
            ORDER BY $sortingField $sortingDir
            LIMIT ?,?";
        $p = array(
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

    public function updateDataControlByRegion($ProvinceID,$DistrictID,$SubDistrictID,$VillageID,$arrPartnerAccess){
        $this->db->trans_begin();

        $sql="
            SELECT
                GROUP_CONCAT(tbl_grouped.MillID SEPARATOR ',') AS MillIDString
            FROM
            (
                SELECT
                    a.`MillID`
                FROM
                    ktv_mill a
                    LEFT JOIN ktv_village vil ON a.`VillageID` = vil.VillageID
                    LEFT JOIN ktv_subdistrict subd ON vil.SubDistrictID = subd.`SubDistrictID`
                    LEFT JOIN ktv_district kd ON kd.`DistrictID` = subd.`DistrictID`
                    LEFT JOIN `ktv_access_partner_mill` apml ON a.`MillID` = apml.apmiMillID
                    LEFT JOIN ktv_program_partner p ON apml.apmiPartnerID = p.PartnerID
                WHERE
                    a.`StatusCode` = 'active'
                    AND ( (kd.ProvinceID = ?) OR ('' = ?) )
                    AND ( (kd.DistrictID = ?) OR ('' = ?) )
                    AND ( (subd.SubDistrictID = ?) OR ('' = ?) )
                    AND ( (a.`VillageID` = ?) OR ('' = ?) )
                GROUP BY a.`MillID`
            ) AS tbl_grouped
            ";
        $p = array(
            $ProvinceID,$ProvinceID,
            $DistrictID,$DistrictID,
            $SubDistrictID,$SubDistrictID,
            $VillageID,$VillageID
        );
        $query = $this->db->query($sql,$p);
        $data = $query->row_array();
        $MillIDString = $data['MillIDString'];
        $arrMillIDString = explode(",",$MillIDString);

        $sql="DELETE FROM `ktv_access_partner_mill` WHERE apmiMillID IN ($MillIDString)";
        $query = $this->db->query($sql);

        for ($i=0; $i < count($arrMillIDString); $i++) {
            for ($j=0; $j < count($arrPartnerAccess); $j++) {
                $sql="INSERT INTO `ktv_access_partner_mill` SET
                        `apmiPartnerID` = ?,
                        `apmiMillID` = ?,
                        `DateCreated` = NOW(),
                        `CreatedBy` = ?";
                $p = array(
                    $arrPartnerAccess[$j],
                    $arrMillIDString[$i],
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

    public function getGridMillNotAssignYet($start, $limit, $sortingField, $sortingDir){
        if ($sortingField == "")
            $sortingField = 'Name';
        if ($sortingDir == "")
            $sortingDir = 'ASC';

        $sql="SELECT
                SQL_CALC_FOUND_ROWS
                a.`MillDisplayID` AS id
                , a.`MillName` AS `Name`
                , subd.`SubDistrict` AS Kecamatan
                , vil.Village AS Desa
            FROM
                ktv_mill a
                LEFT JOIN ktv_village vil ON a.`VillageID` = vil.VillageID
                LEFT JOIN ktv_subdistrict subd ON vil.SubDistrictID = subd.`SubDistrictID`
                LEFT JOIN `ktv_access_partner_mill` apml ON a.`MillID` = apml.apmiMillID
                LEFT JOIN ktv_program_partner p ON apml.apmiPartnerID = p.PartnerID
            WHERE
                a.`StatusCode` = 'active'
                AND apml.apmiPartnerID IS NULL
            GROUP BY a.`MillID`
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