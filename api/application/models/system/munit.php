<?php
class Munit extends CI_Model {

    function readUnits($start,$limit){
        $sql = "
            select %s
            from sys_unit
            ORDER BY UnitName %s";
        $query = $this->db->query(sprintf($sql,'UnitId,UnitName,UnitDescription','LIMIT ?,?'), 
            array((int)$start,(int)$limit));
        //$result['data'] =
        return $query->result_array();
        $query = $this->db->query(sprintf($sql,'count(*) as total',''));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function readUnit($id){
        $sql = "
            select UnitId, UnitName, UnitParentId, UnitHirarki
            from sys_unit WHERE UnitId=?";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    function createUnit($name,$description,$userid){
        $sql = "
            INSERT INTO sys_unit(UnitName,UnitDescription,   UnitAddUserId,UnitAddTime) 
            VALUES (?,?,   ?,now())";
        $query = $this->db->query($sql, array($name,$description,$userid));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateUnit($id,$name,$description,$userid){
        $sql = "
            UPDATE sys_unit 
            SET UnitName=?,UnitDescription=?,   UnitUpdateUserId=?,UnitUpdateTime=now()
            WHERE UnitId=?";
        $query = $this->db->query($sql, array($name,$description,$userid,$id));
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
        $sql = "
            DELETE FROM sys_unit WHERE UnitId=?";
        $query = $this->db->query($sql, array($id));
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
