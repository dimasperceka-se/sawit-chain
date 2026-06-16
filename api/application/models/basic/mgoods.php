<?php
/**
 * @Author: nikolius
 * @Date:   2016-12-29 15:13:32
 */
class Mgoods extends CI_Model {

    public function getMainList($sNama,$start,$limit,$sortingField,$sortingDir){
        if($sortingField == "") $sortingField = 'GoodsCode';
        if($sortingDir == "") $sortingDir = 'ASC';

        $sql = "SELECT
                  SQL_CALC_FOUND_ROWS
                  a.`GoodsID`,
                  a.`GoodsCode`,
                  a.`GoodsName`,
                  a.`GoodsUnitsID`,
                  b.`UnitsName`,
                  a.`GoodsUsage`,
                  CASE
                        WHEN a.`GoodsUsage` = '1' THEN '".lang('Participant')."'
                        WHEN a.`GoodsUsage` = '2' THEN '".lang('Activity')."'
                        WHEN a.`GoodsUsage` = '3' THEN '".lang('Both of them')."'
                  END AS GoodsUsageLabel,
                  a.`StatusCode`
                FROM
                  `ktv_goods` a
                  INNER JOIN ktv_ref_units b ON a.`GoodsUnitsID` = b.`UnitsID`
                WHERE
                    a.GoodsName LIKE ?
                ORDER BY $sortingField $sortingDir
                LIMIT ?,?";
        $p = array(
            '%'.$sNama.'%',
            (int) $start,
            (int) $limit,
        );
        $query = $this->db->query($sql, $p);
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    public function getRefUnit(){
        $sql = "SELECT
                    a.`UnitsID` AS id,
                    a.`UnitsName` AS label
                FROM
                    ktv_ref_units a
                WHERE
                    a.StatusCode = 'active'
                ORDER BY  a.`UnitsName` ASC";
        $query = $this->db->query($sql, array());
        return $query->result_array();
    }

    public function createGoods($varPost){
        $sql = "INSERT INTO `ktv_goods` SET
              `GoodsCode` = ?,
              `GoodsName` = ?,
              `GoodsUnitsID` = ?,
              `GoodsUsage` = ?,
              `StatusCode` = ?,
               DateCreated = NOW(),
               CreatedBy = ?
            ";
        $p = array(
            $varPost['GoodsCode'],
            $varPost['GoodsName'],
            $varPost['UnitsName'],
            $varPost['GoodsUsageLabel'],
            $varPost['StatusCode'],
            $varPost['userid']
        );
        $query = $this->db->query($sql, $p);
        if ($query) {
            $results['success'] = true;
            $results['message'] = "Record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    public function updateGoods($varPost){
        if(is_numeric($varPost['UnitsName'])){
            $updateUnit = "`GoodsUnitsID` = '".$varPost['UnitsName']."',";
        }else{
            $updateUnit = "";
        }

        if(is_numeric($varPost['GoodsUsageLabel'])){
            $updateUsage = "`GoodsUsage` = '".$varPost['GoodsUsageLabel']."',";
        }else{
            $updateUsage = "";
        }

        $sql="UPDATE `ktv_goods` SET
              `GoodsCode` = ?,
              `GoodsName` = ?,
              $updateUnit
              $updateUsage
              `StatusCode` = ?,
              `DateUpdated` = NOW(),
              `LastModifiedBy` = ?
            WHERE
                `GoodsID` = ?
            LIMIT 1";
        $p = array(
            $varPost['GoodsCode'],
            $varPost['GoodsName'],
            $varPost['StatusCode'],
            $varPost['userid'],
            $varPost['GoodsID']
        );
        $query = $this->db->query($sql, $p);
        if ($query) {
            $results['success'] = true;
            $results['message'] = "Record updated";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    public function deleteGoods($GoodsID, $userid){
        $sql="DELETE FROM ktv_goods WHERE GoodsID = ? LIMIT 1";
        $query = $this->db->query($sql, array($GoodsID));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "Record deleted.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

}
?>