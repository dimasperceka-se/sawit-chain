<?php

class Mphoto extends CI_Model {

    function downloadPhoto($partner) {
        $sql = "SELECT 
                a.Photo
                , a.VillageID
                , CONCAT(
                     kd.ProvinceID
                     , '/'
                     , a.Photo
                ) AS Path 
           FROM
                ktv_members a
                LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
                LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
           WHERE a.Photo IS NOT NULL
           AND a.PartnerID = ?";
        $query = $this->db->query($sql, array($partner));

        $result = $query->result_array();
        return $result;
    }

    function downloadConsent($partner) {
        $sql = "SELECT 
                a.LearningContractSign AS Photo
                , a.VillageID
                , CONCAT(
                     kd.ProvinceID
                     , '/'
                     , a.LearningContractSign
                ) AS Path 
           FROM
                ktv_members a
                LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
                LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
           WHERE a.LearningContractSign IS NOT NULL
           AND a.PartnerID = ?";
        $query = $this->db->query($sql, array($partner));

        $result = $query->result_array();
        return $result;
    }
    
    function downloadReceipt($partner) {
        $sql = "SELECT 
                b.ReceiptPhotoLastSoldFFB AS Photo
                , a.VillageID
                , CONCAT(
                     kd.ProvinceID
                     , '/'
                     , b.MemberUid
                     , '/'
                     , b.ReceiptPhotoLastSoldFFB
                ) AS Path 
           FROM
                ktv_members a
                LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
                LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
                LEFT JOIN ktv_survey_main_buyer b 
                     ON a.MemberID = b.MemberID 
           WHERE b.ReceiptPhotoLastSoldFFB IS NOT NULL 
                AND a.PartnerID = ?";
        $query = $this->db->query($sql, array($partner));

        $result = $query->result_array();
        return $result;
    }
    
    function downloadPlantation($partner){
        $sql = "SELECT 
                b.PhotoOfVisit AS Photo
                , a.VillageID
                , CONCAT(
                     kd.ProvinceID
                     , '/'
                     , b.MemberUid
                     , '/'
                     , b.PhotoOfVisit
                ) AS Path 
           FROM
                ktv_members a
                LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
                LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
                LEFT JOIN ktv_survey_plot b 
                     ON a.MemberID = b.MemberID 
           WHERE b.PhotoOfVisit IS NOT NULL 
                AND a.PartnerID = ? ";
        $query = $this->db->query($sql, array($partner));

        $result = $query->result_array();
        return $result;
    }

}

?>
