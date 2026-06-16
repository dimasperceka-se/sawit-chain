<?php
class Mreturpurchase extends CI_Model {

    function readReturPembelians($Awal,$Akhir,$OrgType,$OrgID,$start,$limit){
        $sql = "
            select %s
            from ktv_retur_purchase ks
            LEFT JOIN ktv_supplier kc on ks.SupplierID=kc.SupplierID
            WHERE (Date between ? and ?) and ks.OrgType=? and ks.OrgID=?
            ORDER BY Date desc 
            %s";
        $query = $this->db->query(sprintf($sql,'PurchaseID,PurchaseNumber,ks.OrgID,ks.OrgType,Date,Number,kc.SupplierID,Name,Total,
            Pembayaran','LIMIT ?,?'), array($Awal,$Akhir,$OrgType,$OrgID,(int)$start,(int)$limit));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql,'count(*) as total',''),array($Awal,$Akhir,$OrgType,$OrgID));
        $total = $query->result_array();
        $result['total'] = $total[0]['total'];
        return $result;
    }
    function readReturPembelianPembelian($OrgType,$OrgID,$query,$start,$limit){
        $sql = "
            select %s
            from ktv_purchase ks
            LEFT JOIN ktv_supplier kc on ks.SupplierID=kc.SupplierID
            WHERE ks.OrgType=? and ks.OrgID=? and Number like ?
            ORDER BY Date desc 
            %s";
        $query = $this->db->query(sprintf($sql,'PurchaseID,ks.OrgID,ks.OrgType,Date,Number,kc.SupplierID,Name,Total,
            Pembayaran','LIMIT ?,?'), array($OrgType,$OrgID,"%$query%",(int)$start,(int)$limit));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql,'count(*) as total',''),array($Awal,$Akhir,$OrgType,$OrgID));
        $total = $query->result_array();
        $result['total'] = $total[0]['total'];
        return $result;
    }
    function readReturPembelian($PurchaseID){
        $sql = "
            select *
            from ktv_retur_purchase
            WHERE PurchaseID=?";
        $query = $this->db->query($sql, array($PurchaseID));
        $data = $query->result_array();
        $result['data'] = $data[0];
        $sql_detail = "
            select *
            from ktv_retur_purchase_detail
            WHERE PurchaseID=?";
        $query = $this->db->query($sql, array($PurchaseID));
        $result['detail'] = $query->result_array();
        return $result;
    }
    function createReturPembelian($PurchaseNumber,$ReturPurchaseID,$Date,$OrgType,$OrgID,$SupplierID,$Total,$Pembayaran){
        $sql = "
            SELECT CONCAT(IF(?='koperasi','RK','RS'),YEAR(NOW()),LPAD(COUNT(PurchaseID)+1,7,0)) nomor
            FROM ktv_retur_purchase
            WHERE OrgType=? AND OrgID=? AND YEAR(DATE)=YEAR(NOW())";
        $query = $this->db->query($sql, array($OrgType,$OrgType,$OrgID));
        $data = $query->result_array();
        $number = $data[0]['nomor'];
        $sql = "
            INSERT INTO ktv_retur_purchase(OrgType,OrgID,PurchaseNumber,ReturPurchaseID,Number,SupplierID,Date,Total,Pembayaran) 
            VALUES (?,?,?,?,?,?,?,?,?)";
        $query = $this->db->query($sql, array($OrgType,$OrgID,$PurchaseNumber,$ReturPurchaseID,$number,$SupplierID,$Date,
            str_replace(',','',$Total),str_replace(',','',$Pembayaran)));
        //echo $this->db->last_query();exit;
        if ($query) {
            $results['PurchaseID'] = $this->db->insert_id();
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }
    function updateReturPembelian($Date,$Total,$Pembayaran,$PurchaseID) {
        $sql = "
            UPDATE ktv_retur_purchase
            SET Date=?,Total=?,Pembayaran=?
            WHERE PurchaseID=?";
        $query = $this->db->query($sql, array($Date,str_replace(',','',$Total),str_replace(',','',$Pembayaran),$PurchaseID));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }
    function deleteReturPembelian($id){
        $this->db->trans_start();
        $sql = "
            DELETE FROM ktv_retur_purchase_detail WHERE PurchaseID=?";
        $query = $this->db->query($sql, array($id));
        $sql = "
            DELETE FROM ktv_retur_purchase WHERE PurchaseID=?";
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
    function readReturPembelianDetail($id,$PurchaseID){
        if ($id!='') {
           $sql = "
               select ksd.*,concat('[',Number,'] ',Name) name,Qty*Price Total
               from ktv_retur_purchase_detail ksd
               left join ktv_inventory ki on ksd.InventoryID=ki.InventoryID
               WHERE PurchaseID=?";
           $query = $this->db->query($sql, array($id));
         } else {
           $sql = "
               select ksd.*,concat('[',Number,'] ',Name) name,0 Total,0 Qty,'' DetailID
               from ktv_purchase_detail ksd
               left join ktv_inventory ki on ksd.InventoryID=ki.InventoryID
               WHERE PurchaseID=?";
           $query = $this->db->query($sql, array($PurchaseID));
         }
        return $query->result_array();
    }
    function createReturPembelianDetail($PurchaseID,$InventoryID,$Qty,$Price){
        $sql = "
            INSERT INTO ktv_retur_purchase_detail(PurchaseID,InventoryID,Qty,Price) 
            VALUES (?,?,?,?)";
        $query = $this->db->query($sql, array($PurchaseID,$InventoryID,$Qty,$Price));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }
    function updateReturPembelianDetail($InventoryID,$Qty,$Price,$DetailID) {
        $sql = "
            UPDATE ktv_retur_purchase_detail
            SET InventoryID=?,Qty=?,Price=?
            WHERE DetailID=?";
        $query = $this->db->query($sql, array($InventoryID,$Qty,$Price,$DetailID));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }
    function deleteReturPembelianDetail($id){
        $sql = "
            DELETE FROM ktv_retur_purchase_detail WHERE DetailID=?";
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
