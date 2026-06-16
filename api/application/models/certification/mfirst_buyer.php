<?php
class Mfirst_buyer extends CI_Model {
    
    public function readFirstBuyers($key, $start = 0, $limit = 50)
    {
        $sql = "SELECT SQL_CALC_FOUND_ROWS
                    a.FirstBuyerID, b.PartnerName, CASE b.PartnerIndustry WHEN '0' THEN 'Implementer' WHEN '1' THEN 'Donor' WHEN '2' THEN 'Trader' WHEN '3' THEN 'Processor' WHEN '4' THEN 'Manufacturer' WHEN '5' THEN 'Inpput Supplier' ELSE '' END PartnerIndustry
                    , b.PartnerFullName, b.PartnerProgramName
                FROM ktv_first_buyer a
                LEFT JOIN ktv_program_partner b ON a.FirstBuyerPartnerID=b.PartnerID
                WHERE 1 = 1 AND a.StatusCode!='nullified'
                AND (b.PartnerName LIKE ? OR b.PartnerFullName LIKE ? OR b.PartnerProgramName LIKE ?)
                LIMIT ?, ?";
        $query = $this->db->query($sql,array("%$key%", "%$key%", "%$key%", intval($start), intval($limit)));
        //echo "<pre>".$this->db->last_query();exit;
        $sql_total = "SELECT FOUND_ROWS() AS total";
        $query_total = $this->db->query($sql_total);
        if ($query->num_rows() > 0) {
            $total = $query_total->row_array(0);
            return array(
                'data'      => $query->result_array(),
                'total'     => $total['total']
                );
        }
        return false;
    }
    
    public function listPartners($PartnerID=''){
        if($PartnerID==""){ $ed = ") #"; }else{ $ed=""; }
        $sql = "SELECT a.PartnerID id, a.PartnerName label FROM ktv_program_partner a LEFT JOIN ktv_first_buyer b ON a.PartnerID=b.FirstBuyerPartnerID AND b.StatusCode='active' WHERE a.StatusCode='active'
                AND (b.FirstBuyerPartnerID IS NULL $ed OR b.FirstBuyerPartnerID=?)";
        $query = $this->db->query($sql,array($PartnerID));
        //echo "<pre>".$this->db->last_query();exit;
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
    }
    
    public function readpartnerDetail($PartnerID){
        $sql = "SELECT b.PartnerName, CASE b.PartnerIndustry WHEN '0' THEN 'Implementer' WHEN '1' THEN 'Donor' WHEN '2' THEN 'Trader' WHEN '3' THEN 'Processor' WHEN '4' THEN 'Manufacturer' WHEN '5' THEN 'Inpput Supplier' ELSE '' END PartnerIndustry
                    , b.PartnerFullName, b.PartnerProgramName FROM ktv_program_partner b WHERE b.PartnerID=?";
        $query = $this->db->query($sql,array($PartnerID));
        $return = $query->result_array();
        return $return[0];
    }
    
    public function createFirstBuyer($PartnerID, $userid){
        $sql = " INSERT INTO ktv_first_buyer(FirstBuyerPartnerID, CreatedBy, DateCreated, StatusCode) VALUES (?,?,now(),'active')";
        $query = $this->db->query($sql, array($PartnerID, $userid));
        if ($query) {
            $results['success']     = true;
            $results['message']     = "record created.";
        } else {
            $results['success']     = false;
            $results['message']     = "Failed to create record";
        }
        return $results;
    }
    
    public function readFirstBuyerDetail($FirstBuyerID){
        $sql = "SELECT * FROM ktv_first_buyer WHERE FirstBuyerID=?";
        $query = $this->db->query($sql,array($FirstBuyerID));
        $return = $query->result_array();
        return $return[0];
    }
    
    public function updateFirstBuyer($PartnerID, $userid, $FirstBuyerID){
        $sql = " UPDATE ktv_first_buyer SET FirstBuyerPartnerID=?, LastModifiedBy=?, DateUpdated=NOW() WHERE FirstBuyerID=?";
        $query = $this->db->query($sql, array($PartnerID, $userid, $FirstBuyerID));
        if ($query) {
            $results['success']     = true;
            $results['message']     = "record updated.";
        } else {
            $results['success']     = false;
            $results['message']     = "Failed to update record.";
        }
        return $results;
    }
    
    public function deleteFirstBuyer($userid, $FirstBuyerID){
        $sql = "UPDATE ktv_first_buyer SET StatusCode='nullified', LastModifiedBy=?, DateUpdated=NOW() WHERE FirstBuyerID=?";
        $query = $this->db->query($sql, array($userid, $FirstBuyerID));
        if ($query) {
            $results['success']     = true;
            $results['message']     = "record deleted.";
        } else {
            $results['success']     = false;
            $results['message']     = "Failed to delete record.";
        }
        return $results;
    }
}
?>
