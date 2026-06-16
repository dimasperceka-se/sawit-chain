<?php
class MInstitution extends CI_Model {


    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    function readInstitutions(){

        $sql = "select InstitutionID, InstitutionName"
            . " from ktv_institution ";
        $query = $this->db->query($sql);
        $results = $query->result_array();
        return $results;
    }

    function readInstitution($id){

        $sql = "select InstitutionID,InstitutionName"
            . " from ktv_institution WHERE InstitutionID='".$id."'";
        $query = $this->db->query($sql);
        $results = $query->result_array();
        return $results;
    }

    function createInstitution($name){

        $sql = "INSERT INTO ktv_institution(InstitutionName) VALUES ('".$name."')";
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

    function updateInstitution($id, $name){

        $sql = "UPDATE ktv_institution SET InstitutionName='".$name."'"
            . " WHERE "
            . " InstitutionID='".$id."'";
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


    function deleteInstitution($id){

        $sql = "DELETE FROM ktv_institution WHERE InstitutionID=".$id."";
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