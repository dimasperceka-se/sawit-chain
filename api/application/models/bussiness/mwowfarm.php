<?php
class Mwowfarm extends CI_Model {

    function readDatas($awal,$akhir,$type,$id,$start,$limit){
        $sql = "
            select %s
            from ktv_demoplot kd
            WHERE (DateHarvest between ? and ?) and OrgType=? and OrgID=?
            %s";
        $query = $this->db->query(sprintf($sql,'DemoplotID, OrgType, OrgID, DateHarvest, AmountWetBeans, DateSales, 
            DryingDay, AmountDryBeans, Price, Total, Description','LIMIT ?,?'),
            array($awal,$akhir,$type,$id,(int)$start,(int)$limit));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql,'count(*) as total',''), array($awal,$akhir,$type,$id));
        $total = $query->result_array();
        $result['total'] = $total[0]['total'];
        return $result;
    }
    function readData($DemoplotID){
        $sql = "
            select *
            from ktv_demoplot
            WHERE DemoplotID=?";
        $query = $this->db->query($sql, array($DemoplotID));
        $data = $query->result_array();
        return $data[0];
    }
    function createData($OrgType, $OrgID, $DateHarvest, $AmountWetBeans, $DateSales, $DryingDay, $AmountDryBeans, $Price, 
         $Total, $Description,$BuyerOrgType, $BuyerOrgID){
        $sql = "
            INSERT INTO ktv_demoplot(OrgType, OrgID, DateHarvest, AmountWetBeans, DateSales, DryingDay, 
               AmountDryBeans, Price, Total, Description,BuyerOrgType, BuyerOrgID) 
            VALUES (?,?,?,?,?,?,   ?,?,?,?,?,?)";
        $query = $this->db->query($sql, array($OrgType, $OrgID, $DateHarvest, $AmountWetBeans, $DateSales, $DryingDay, 
            $AmountDryBeans, str_replace(',','',$Price), str_replace(',','',$Total), $Description,$BuyerOrgType, $BuyerOrgID));
        if ($query) {
            $results['DemoplotID'] = $this->db->insert_id();
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }
    function updateData($DateHarvest, $AmountWetBeans, $DateSales, $DryingDay, $AmountDryBeans, $Price, $Total, 
         $Description,$BuyerOrgType, $BuyerOrgID,$DemoplotID) {
        $sql = "
            UPDATE ktv_demoplot
            SET DateHarvest=?, AmountWetBeans=?, DateSales=?, DryingDay=?, AmountDryBeans=?, Price=?, Total=?, Description=?,
               BuyerOrgType=?, BuyerOrgID=?
            WHERE DemoplotID=?";
        $query = $this->db->query($sql, array($DateHarvest, $AmountWetBeans, $DateSales, $DryingDay, $AmountDryBeans, 
         str_replace(',','',$Price), str_replace(',','',$Total), $Description,$BuyerOrgType, $BuyerOrgID,$DemoplotID));
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
            DELETE FROM ktv_demoplot_detail WHERE DemoplotID=?";
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
