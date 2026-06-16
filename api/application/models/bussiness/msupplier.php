<?php
class Msupplier extends CI_Model {

    function readDatas($start,$limit,$SceID=null){
        $sql = "
            select %s
            from ktv_supplier ks
            WHERE 1 = 1
            %s
            %s";
        $where = '';
        if (!empty($SceID)) {
            $where = " AND OrgType = 'sce' AND OrgID = {$SceID}";
        }
        $query = $this->db->query(sprintf($sql,'SupplierID, OrgType, OrgID, Name, Address, Phone, Email, VillageID, Note',$where,
            'LIMIT ?,?'), array((int)$start,(int)$limit));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql,'count(*) as total',$where,''));
        $total = $query->result_array();
        $result['total'] = $total[0]['total'];
        return $result;
    }
    function readData($SupplierID){
        $sql = "
            select *
            from ktv_supplier
            WHERE SupplierID=?";
        $query = $this->db->query($sql, array($SupplierID));
        $data = $query->result_array();
        return $data[0];
    }
    function createData($OrgType, $OrgID, $Name, $Address, $Phone, $Email, $VillageID, $Note){
        $sql = "
            INSERT INTO ktv_supplier(OrgType, OrgID, Name, Address, Phone, Email, VillageID, Note)
            VALUES (?,?,?,?,?,?,?,?)";
        $query = $this->db->query($sql, array($OrgType, $OrgID, $Name, $Address, $Phone, $Email, $VillageID, $Note));
        if ($query) {
            $results['SupplierID'] = $this->db->insert_id();
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }
    function updateData($Name, $Address, $Phone, $Email, $VillageID, $Note,$SupplierID) {
        $sql = "
            UPDATE ktv_supplier
            SET Name=?, Address=?, Phone=?, Email=?, VillageID=?, Note=?
            WHERE SupplierID=?";
        $query = $this->db->query($sql, array($Name, $Address, $Phone, $Email, $VillageID, $Note,$SupplierID));
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
            DELETE FROM ktv_supplier WHERE SupplierID=?";
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
