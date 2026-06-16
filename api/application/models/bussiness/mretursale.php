<?php
class Mretursale extends CI_Model {

    function readReturPenjualans($Awal,$Akhir,$OrgType,$OrgID,$start,$limit){
        $sql = "
            select %s
            from ktv_retur_sale ks
            LEFT JOIN ktv_customer kc on ks.CustomerID=kc.CustomerID
            WHERE (date(Date) between ? and ?) and ks.OrgType=? and ks.OrgID=?
            ORDER BY Date desc 
            %s";
        $query = $this->db->query(sprintf($sql,'SaleID,ks.OrgID,ks.OrgType,Date,Number,SaleNumber,kc.CustomerID,Name,
            Total,Pembayaran','LIMIT ?,?'),array($Awal,$Akhir,$OrgType,$OrgID,(int)$start,(int)$limit));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql,'count(*) as total',''),array($Awal,$Akhir,$OrgType,$OrgID));
        $total = $query->result_array();
        $result['total'] = $total[0]['total'];
        return $result;
    }
    function readReturPenjualanPenjualan($OrgType,$OrgID,$query,$start,$limit){
        $sql = "
            select %s
            from ktv_sale ks
            LEFT JOIN ktv_customer kc on ks.CustomerID=kc.CustomerID
            WHERE ks.OrgType=? and ks.OrgID=? and Number like ?
            ORDER BY Date desc 
            %s";
        $query = $this->db->query(sprintf($sql,'SaleID,ks.OrgID,ks.OrgType,Date,Number,Number,kc.CustomerID,Name,
            Total,Pembayaran','LIMIT ?,?'), array($OrgType,$OrgID,"%$query%",(int)$start,(int)$limit));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql,'count(*) as total',''),array($Awal,$Akhir,$OrgType,$OrgID));
        $total = $query->result_array();
        $result['total'] = $total[0]['total'];
        return $result;
    }
    function readReturPenjualan($SaleID){
        $sql = "
            select *
            from ktv_retur_sale
            WHERE SaleID=?";
        $query = $this->db->query($sql, array($SaleID));
        $data = $query->result_array();
        $result['data'] = $data[0];
        $sql_detail = "
            select *
            from ktv_retur_sale_detail
            WHERE SaleID=?";
        $query = $this->db->query($sql, array($SaleID));
        $result['detail'] = $query->result_array();
        return $result;
    }
    function createReturPenjualan($SaleNumber,$ReturSaleID,$Date,$OrgType,$OrgID,$CustomerID,$Total,$Pembayaran){
        $sql = "
            SELECT CONCAT(IF(?='koperasi','RK','RS'),YEAR(NOW()),LPAD(COUNT(SaleID)+1,7,0)) nomor
            FROM ktv_retur_sale
            WHERE OrgType=? AND OrgID=? AND YEAR(DATE)=YEAR(NOW())";
        $query = $this->db->query($sql, array($OrgType,$OrgType,$OrgID));
        $data = $query->result_array();
        $number = $data[0]['nomor'];
        $sql = "
            INSERT INTO ktv_retur_sale(OrgType,OrgID,Number,SaleNumber,ReturSaleID,CustomerID,Date,Total,Pembayaran) 
            VALUES (?,?,?,?,?,?,?,?,?)";
        $query = $this->db->query($sql, array($OrgType,$OrgID,$number,$SaleNumber,$ReturSaleID,$CustomerID,$Date,
            str_replace(',','',$Total),str_replace(',','',$Pembayaran)));
        //echo $this->db->last_query();exit;
        if ($query) {
            $results['SaleID'] = $this->db->insert_id();
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }
    function updateReturPenjualan($Date,$Total,$Pembayaran,$SaleID) {
        $sql = "
            UPDATE ktv_retur_sale
            SET Date=?,Total=?,Pembayaran=?
            WHERE SaleID=?";
        $query = $this->db->query($sql, array($Date,str_replace(',','',$Total),str_replace(',','',$Pembayaran),$SaleID));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }
    function deleteReturPenjualan($id){
        $this->db->trans_start();
        $sql = "
            DELETE FROM ktv_retur_sale_detail WHERE SaleID=?";
        $query = $this->db->query($sql, array($id));
        $sql = "
            DELETE FROM ktv_retur_sale WHERE SaleID=?";
        $query = $this->db->query($sql, array($id));
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }
    function readReturPenjualanDetail($id,$SaleID){
        if ($id!='') {
           $sql = "
               select ksd.*,concat('[',Number,'] ',Name) name,Qty*Price Total
               from ktv_retur_sale_detail ksd
               left join ktv_inventory ki on ksd.InventoryID=ki.InventoryID
               WHERE SaleID=?";
           $query = $this->db->query($sql, array($id));
        } else {
           $sql = "
               select ksd.*,concat('[',Number,'] ',Name) name,0 Total,0 Qty,'' DetailID
               from ktv_sale_detail ksd
               left join ktv_inventory ki on ksd.InventoryID=ki.InventoryID
               WHERE SaleID=?";
           $query = $this->db->query($sql, array($SaleID));
        }
        return $query->result_array();
    }
    function createReturPenjualanDetail($SaleID,$InventoryID,$Problem,$Solution,$DateStart,$DateEnd,$Qty,$Price){
        $sql = "
            INSERT INTO ktv_retur_sale_detail(SaleID,InventoryID,Problem,Solution,DateStart,DateEnd,Qty,Price) 
            VALUES (?,?,?,?,?,?,?,?)";
        $query = $this->db->query($sql, array($SaleID,$InventoryID,$Problem,$Solution,$DateStart,$DateEnd,$Qty,$Price));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }
    function updateReturPenjualanDetail($InventoryID,$Problem,$Solution,$DateStart,$DateEnd,$Qty,$Price,$DetailID) {
        $sql = "
            UPDATE ktv_retur_sale_detail
            SET InventoryID=?,Problem=?,Solution=?,DateStart=?,DateEnd=?,Qty=?,Price=?
            WHERE DetailID=?";
        $query = $this->db->query($sql, array($InventoryID,$Problem,$Solution,$DateStart,$DateEnd,$Qty,$Price,$DetailID));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }
    function deleteReturPenjualanDetail($id){
        $sql = "
            DELETE FROM ktv_retur_sale_detail WHERE DetailID=?";
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
