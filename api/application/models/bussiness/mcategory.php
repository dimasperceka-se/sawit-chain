<?php
class Mcategory extends CI_Model {

    function readDatas($sce_id,$start,$limit){
        $sql = "
            select %s
            from ktv_inventory_category kic
            where
                kic.OrgType = 'sce' AND
                kic.OrgID = ?
            %s";
        $query = $this->db->query(sprintf($sql,'CategoryID,OrgType, OrgID, ParentCategoryID, Name, Description','LIMIT ?,?'),
            array($sce_id,(int)$start,(int)$limit));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql,'count(*) as total',''),array($sce_id));
        $total = $query->result_array();
        $result['total'] = $total[0]['total'];
        return $result;
    }
    function readParentDatas($start,$limit){
        $sql = "
            select %s
            from ktv_inventory_category kic
            WHERE ParentCategoryID is null
            %s";
        $query = $this->db->query(sprintf($sql,'OrgType, OrgID, ParentCategoryID, Name, Description','LIMIT ?,?'),
            array((int)$start,(int)$limit));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql,'count(*) as total',''));
        $total = $query->result_array();
        $result['total'] = $total[0]['total'];
        return $result;
    }
    function readData($CategoryID){
        $sql = "
            select *
            from ktv_inventory_category
            WHERE CategoryID=?";
        $query = $this->db->query($sql, array($CategoryID));
        $data = $query->result_array();
        return $data[0];
    }
    function createData($sce_id, $ParentCategoryID, $Name, $Description){
        $sql = "
            INSERT INTO ktv_inventory_category(OrgType, OrgID, ParentCategoryID, Name, Description)
            VALUES ('sce',?,?,?,?)";
        $query = $this->db->query($sql, array($sce_id, NULL, $Name, $Description));
        if ($query) {
            $results['CategoryID'] = $this->db->insert_id();
            $results['success'] = true;
            $results['message'] = "Record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }
    function updateData($Name, $Description,$CategoryID) {
        $sql = "
            UPDATE ktv_inventory_category
            SET ParentCategoryID=?, Name=?, Description=?
            WHERE CategoryID=?";
        $query = $this->db->query($sql, array(NULL, $Name, $Description,$CategoryID));
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
        $sql = "
            DELETE FROM ktv_inventory_category WHERE CategoryID=?";
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
