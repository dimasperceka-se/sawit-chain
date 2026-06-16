<?php
/**
 * @Author: nikolius
 * @Date:   2016-06-01 17:04:13
 */
class Mcms_feedback extends CI_Model {

   function prosesInsertFeedback($title,$content,$userId){
      //get info user
      $dataUser = $this->getInfoUser($userId);

      $sql="INSERT INTO `cms_feedback` SET
            `FeedbackUserId` = ?,
            `FeedbackName` = ?,
            `FeedbackEmail` = ?,
            `FeedbackTitle` = ?,
            `FeedbackContent` = ?,
            `FeedbackDate` = CURDATE(),
            `DateCreated` = NOW(),
            `CreatedBy` = ?";
      $p = array(
         $userId,
         $dataUser['nama'],
         $dataUser['email'],
         $title,
         $content,
         $userId
      );
      return $this->db->query($sql,$p);
   }

   function getInfoUser($userId){
      $sql="SELECT
               PersonNm AS nama,
               OfficialEmail AS email
            FROM
               ktv_persons
            WHERE
               UserID = ?
            LIMIT 1";
      $query = $this->db->query($sql,array($userId));
      return $query->row_array();
   }
}
?>