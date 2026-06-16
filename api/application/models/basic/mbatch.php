<?php
class Mbatch extends CI_Model {

    function readBatchs($start,$limit){
        $sql = "
            select %s
            from ktv_cpg_batch a
            left join ktv_program_partner b on a.PartnerID=b.PartnerID
            WHERE
               a.StatusCode != 'nullified'
            ORDER BY a.CpgBatchID %s";
        $query = $this->db->query(sprintf($sql,'a.CpgBatchID as id,a.BatchNumber as number,a.PartnerID as partner_id, PartnerName as partner',
            'LIMIT ?,?'),array((int)$start,(int)$limit));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql,'count(*) as total',''));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function createBatch($number,$partner){
        $sql = "
            INSERT INTO ktv_cpg_batch(BatchNumber,PartnerID)
            VALUES (?,?)";
        $query = $this->db->query($sql, array($number,$partner));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateBatch($number,$name,$id){
        $sql = "
            UPDATE ktv_cpg_batch
            SET BatchNumber=?,PartnerID=?
            WHERE CpgBatchID=?";
        $query = $this->db->query($sql, array($number,$name,$id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deletebatch($id){
        //$sql = "DELETE FROM ktv_cpg_batch WHERE CpgBatchID=?";
        $sql="UPDATE ktv_cpg_batch SET StatusCode = 'nullified',LastModifiedBy='".$_SESSION['userid']."',DateUpdated=NOW() WHERE CpgBatchID=? LIMIT 1";
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

    function readCpgs(){
        $sql = "
            select CPGid as id,GroupName as label
            from ktv_cpg
            ORDER BY GroupName";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    function readPartners(){
        $sql = "
            select PartnerID as id,PartnerName as label
            from ktv_program_partner
            ORDER BY PartnerName";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

}
?>
