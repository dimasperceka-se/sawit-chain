<?php
/**
 * @Author: nikolius
 * @Date:   2016-03-31 16:53:12
 */
class Minfrastructure_type extends CI_Model {

   function infrastructure_type_list($start,$limit){
      $sql = "SELECT
               InfrastructureTypeID,
               InfrastructureTypeName,
               StatusCode
            FROM
               ktv_ref_infrastructure_type
            WHERE
               StatusCode != 'nullified'
            ORDER BY InfrastructureTypeID DESC
            LIMIT ?,?";
      $p = array(
         (int) $start,
         (int) $limit
      );
      $query = $this->db->query($sql,$p);
      return $query->result_array();
   }

   function create_infrastructure_type($InfrastructureTypeName, $StatusCode, $userid){
      $sql="INSERT INTO ktv_ref_infrastructure_type SET
         InfrastructureTypeName = ?,
         StatusCode = IF(?='','active',?),
         DateCreated = NOW(),
         CreatedBy = ?
      ";
      $p = array(
         $InfrastructureTypeName, $StatusCode, $StatusCode, $userid
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

   function update_infrastructure_type($InfrastructureTypeID, $InfrastructureTypeName, $StatusCode, $userid){
      $sql="UPDATE ktv_ref_infrastructure_type SET
            InfrastructureTypeName = ?,
            StatusCode = ?,
            DateUpdated = NOW(),
            LastModifiedBy = ?
         WHERE
            InfrastructureTypeID = ?
         LIMIT 1
      ";
      $p = array(
         $InfrastructureTypeName,$StatusCode,$userid,(int) $InfrastructureTypeID
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

   function delete_infrastructure_type($InfrastructureTypeID,$userid){
      $sql="UPDATE ktv_ref_infrastructure_type SET statusCode='nullified', DateUpdated = NOW(), LastModifiedBy = ? WHERE InfrastructureTypeID = ? LIMIT 1";
      $p = array(
         $userid,
         (int) $InfrastructureTypeID
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