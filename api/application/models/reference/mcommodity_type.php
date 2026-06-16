<?php
/**
 * @Author: nikolius
 * @Date:   2016-03-31 13:37:18
 */
class Mcommodity_type extends CI_Model {

   function commodity_type_list($start,$limit){
      $sql = "SELECT
               CommodityTypeID,
               CommodityTypeName,
               StatusCode
            FROM
               ktv_ref_commodity_type
            WHERE
               StatusCode != 'nullified'
            ORDER BY CommodityTypeID DESC
            LIMIT ?,?";
      $p = array(
         (int) $start,
         (int) $limit
      );
      $query = $this->db->query($sql,$p);
      return $query->result_array();
   }

   function create_commodity_type($CommodityTypeName, $StatusCode, $userid){
      $sql="INSERT INTO ktv_ref_commodity_type SET
         CommodityTypeName = ?,
         StatusCode = IF(?='','active',?),
         DateCreated = NOW(),
         CreatedBy = ?
      ";
      $p = array(
         $CommodityTypeName, $StatusCode, $StatusCode, $userid
      );
      $query = $this->db->query($sql,$p);
      if ($query) {
         $results['success'] = true;
         $results['message'] = "Record created.";
      } else {
         $results['success'] = false;
         $results['message'] = "Failed to create record";
      }
      return $results;
   }

   function update_commodity_type($CommodityTypeID, $CommodityTypeName, $StatusCode, $userid){
      $sql="UPDATE ktv_ref_commodity_type SET
            CommodityTypeName = ?,
            StatusCode = ?,
            DateUpdated = NOW(),
            LastModifiedBy = ?
         WHERE
            CommodityTypeID = ?
         LIMIT 1
      ";
      $p = array(
         $CommodityTypeName,$StatusCode,$userid,(int) $CommodityTypeID
      );
      $query = $this->db->query($sql,$p);
      if ($query) {
         $results['success'] = true;
         $results['message'] = "Record updated.";
      } else {
         $results['success'] = false;
         $results['message'] = "Failed to update record";
      }
      return $results;
   }

   function delete_commodity_type($CommodityTypeID,$userid){
      $sql="UPDATE ktv_ref_commodity_type SET statusCode='nullified', DateUpdated = NOW(), LastModifiedBy = ? WHERE CommodityTypeID = ? LIMIT 1";
      $p = array(
         $userid,
         (int) $CommodityTypeID
      );
      $query = $this->db->query($sql,$p);
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