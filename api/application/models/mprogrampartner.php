<?php
class MProgrampartner extends CI_Model {


    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    function readProgramPartners(){

        $sql = "select partnerID, partnerName, partnerIndustry, partnerFullName, photo"
            . " from ktv_program_partner ";
        $query = $this->db->query($sql);
        $results = $query->result_array();
        return $results;
    }

    function readProgramPartner($id){

        $sql = "select partnerID, partnerName, partnerIndustry, partnerFullName, photo"
            . " from ktv_program_partner WHERE partnerID='".$id."'";
        $query = $this->db->query($sql);
        $results = $query->result_array();
        return $results;
    }

    function createProgramPartner($partnerName, $partnerIndustry, $partnerFullName, $photo){

        $sql = "INSERT INTO ktv_program_partner(partnerName, partnerIndustry, partnerFullName, photo) VALUES ('".$partnerName."','".$partnerIndustry."','".$partnerIndustry."','".$partnerFullName."','".$photo."')";
        $query = $this->db->query($sql);
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;

    }

    function updateProgramPartner($partnerID, $partnerName, $partnerIndustry, $partnerFullName, $photo){

        $sql = "UPDATE ktv_program_partner SET partnerName='".$partnerName."',partnerIndustry='".$partnerIndustry."',partnerFullName='".$partnerFullName."',photo='".$photo."'"
            . " WHERE "
            . " partnerID='".$partnerID."'";
        $query = $this->db->query($sql);
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;

    }


    function deleteProgramPartner($id){

        $sql = "DELETE FROM ktv_program_partner WHERE partnerID=".$id."";
        $query = $this->db->query($sql);
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;

    }



}
?>