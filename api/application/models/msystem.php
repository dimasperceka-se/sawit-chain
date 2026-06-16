<?php
class Msystem extends CI_Model {


    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    function readUnits(){

        $sql = "select UnitId,UnitName, UnitDescription"
            . " from sys_unit ";
        $query = $this->db->query($sql);
        $results = $query->result_array();
        return $results;
    }

    function readUnit($id){

        $sql = "select UnitId, UnitName, UnitParentId, UnitHirarki"
            . " from sys_unit WHERE UnitId='".$id."'";
        $query = $this->db->query($sql);
        $results = $query->result_array();
        return $results;
    }

    function createUnit($name,$description){

        $sql = "INSERT INTO sys_unit(UnitName,UnitDescription) VALUES ('".$name."','".$description."')";
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

    function updateUnit($id, $name,$description){

        $sql = "UPDATE sys_unit SET UnitName='".$name."',UnitDescription='".$description."'"
             . " WHERE "
             . " UnitId='".$id."'";
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


    function deleteUnit($id){

        $sql = "DELETE FROM sys_unit WHERE UnitId=".$id."";
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