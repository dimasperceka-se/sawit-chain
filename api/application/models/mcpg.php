<?php
class MCpg extends CI_Model {


    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    function readCpgs(){

        $sql = "select CPGId, GroupName, Address, TahunTerbentuk, RegionID, CpgBatchID, Status, DateCreated, CreatedBy, DateUpdated, ModifiedBy"
            . " from ktv_cpg ";
        $query = $this->db->query($sql);
        $results = $query->result_array();
        return $results;
    }

    function readCpg($id){

        $sql = "select CPGId, GroupName, Address, TahunTerbentuk, RegionID, CpgBatchID, DateCreated, CreatedBy, DateUpdated, ModifiedBy"
            . " from ktv_cpg WHERE CPGId='".$id."'";
        $query = $this->db->query($sql);
        $results = $query->result_array();
        return $results;
    }

    function createCpg($groupName, $tahunTerbentuk, $regionID){

        $sql = "INSERT INTO ktv_cpg(GroupName, TahunTerbentuk, RegionID) VALUES ('".$groupName."','".$tahunTerbentuk."','".$regionID."')";
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

    function updateCpg($cpgID, $groupName, $tahunTerbentuk, $regionID){

        $sql = "UPDATE ktv_cpg SET GroupName='".$groupName."',RegionID='".$regionID."',TahunTerbentuk='".$tahunTerbentuk."'"
            . " WHERE "
            . " CPGId='".$cpgID."'";
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

    function deleteCpg($id){

        $sql = "DELETE FROM ktv_cpg WHERE CPGId=".$id."";
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