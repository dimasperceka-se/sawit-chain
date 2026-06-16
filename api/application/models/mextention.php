<?php
class Mextention extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function readData(){
        $sql = "select * from ci_cpg";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    function createData($name,$description,$unitid){
        $sql = "INSERT INTO ci_cpg(UserName,UserDescription,UserUnitId) VALUES ('$name','$description','$unitid')";
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

    function updateData($id, $name,$description,$unitid){
        $sql = "UPDATE ci_cpg SET UserName='$name',UserDescription='$description',UserUnitId='$unitid' 
            WHERE UserId='$id'";
        $delete = $this->db->query($sql);
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;

    }


    function deleteData($id){
        $sql = "DELETE FROM ci_cpg WHERE UserId=$id";
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

    function readUnit(){
        $sql = "select UnitId as id,UnitName as label from ci_unit order by UnitName";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

}
?>
