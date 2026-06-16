<?php
class Mgroup extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function readData(){
        $sql = "select GroupId,GroupName, GroupDescription,UnitName from ci_group left join ci_unit ON UnitId=GroupUnitId";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    function createData($name,$description,$unitid){
        $sql = "INSERT INTO ci_group(GroupName,GroupDescription,GroupUnitId) VALUES ('$name','$description','$unitid')";
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
        $sql = "UPDATE ci_group SET GroupName='$name',GroupDescription='$description',GroupUnitId='$unitid' 
            WHERE GroupId='$id'";
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
        $sql = "DELETE FROM ci_group WHERE GroupId=$id";
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
