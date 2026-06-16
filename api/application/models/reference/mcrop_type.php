<?php
/**
 * @Author: nikolius
 * @Date:   2016-03-31 15:55:36
 */
class Mcrop_type extends CI_Model {

   function crop_type_list($start,$limit){
      $sql = "SELECT
               CropTypeID,
               CropTypeName,
               StatusCode
            FROM
               ktv_ref_crop_type
            WHERE
               StatusCode != 'nullified'
            ORDER BY CropTypeID DESC
            LIMIT ?,?";
      $p = array(
         (int) $start,
         (int) $limit
      );
      $query = $this->db->query($sql,$p);
      return $query->result_array();
   }

   function create_crop_type($CropTypeName, $StatusCode, $userid){
      $sql="INSERT INTO ktv_ref_crop_type SET
         CropTypeName = ?,
         StatusCode = IF(?='','active',?),
         DateCreated = NOW(),
         CreatedBy = ?
      ";
      $p = array(
         $CropTypeName, $StatusCode, $StatusCode, $userid
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

   function update_crop_type($CropTypeID, $CropTypeName, $StatusCode, $userid){
      $sql="UPDATE ktv_ref_crop_type SET
            CropTypeName = ?,
            StatusCode = ?,
            DateUpdated = NOW(),
            LastModifiedBy = ?
         WHERE
            CropTypeID = ?
         LIMIT 1
      ";
      $p = array(
         $CropTypeName,$StatusCode,$userid,(int) $CropTypeID
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

   function delete_crop_type($CropTypeID,$userid){
      $sql="UPDATE ktv_ref_crop_type SET statusCode='nullified', DateUpdated = NOW(), LastModifiedBy = ? WHERE CropTypeID = ? LIMIT 1";
      $p = array(
         $userid,
         (int) $CropTypeID
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