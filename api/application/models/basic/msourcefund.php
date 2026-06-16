<?php
class Msourcefund extends CI_Model {

    function readSourceFunds($start,$limit){
        $sql = "
            select %s
            from coop_cash_source a
            LEFT JOIN accounting_coa b ON a.coaCode=b.coaCode
            ORDER BY cashSourceID %s";
        $query = $this->db->query(sprintf($sql,'cashSourceID as id,cashSourceName as name,cashSourceNo as no, a.coaCode AS code, CONCAT(a.coaCode, " - ", b.coaTitle) as codeName','LIMIT ?,?'),
            array((int)$start,(int)$limit));
        //echo $this->db->last_query(); exit;
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql,'count(*) as total',''));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function readCoas(){
        $sql = "
            SELECT coaCode AS id, CONCAT(coaCode, ' - ', coaTitle) as label
            from accounting_coa
            ORDER BY coaCode";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    function createSourceFund($name,$no,$codeName){
        $sql = "
            INSERT INTO coop_cash_source(cashSourceName,cashSourceNo,coaCode) 
            VALUES (?,?,?)";
        $query = $this->db->query($sql, array($name,$no,$codeName));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateSourceFund($id,$name,$no,$codeName){
       //echo "$id,$name,$no,$codeName";
        $sql = "
            UPDATE coop_cash_source 
            SET cashSourceName=?,cashSourceNo=?,coaCode=?
            WHERE cashSourceID=?";
        $query = $this->db->query($sql, array($name,$no,$codeName,$id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deleteSourceFund($id){
        $sql = "
            DELETE FROM coop_cash_source WHERE cashSourceID=?";
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
