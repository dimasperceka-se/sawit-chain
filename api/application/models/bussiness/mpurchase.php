<?php
class Mpurchase extends CI_Model {

    function readPembelians($Awal,$Akhir,$OrgType,$OrgID,$start,$limit){
        $sql = "
            select %s
            from ktv_purchase ks
            LEFT JOIN ktv_supplier kc on ks.SupplierID=kc.SupplierID
            WHERE (Date between ? and ?) and ks.OrgType=? and ks.OrgID=?
            ORDER BY Date desc
            %s";
        $query = $this->db->query(sprintf($sql,'PurchaseID,ks.OrgID,ks.OrgType,Date,Number,kc.SupplierID,Name,Total,
            Pembayaran','LIMIT ?,?'), array($Awal,$Akhir,$OrgType,$OrgID,(int)$start,(int)$limit));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql,'count(*) as total',''),array($Awal,$Akhir,$OrgType,$OrgID));
        $total = $query->result_array();
        $result['total'] = $total[0]['total'];
        return $result;
    }
    function readPembelian($PurchaseID){
        $sql = "
            select *
            from ktv_purchase
            WHERE PurchaseID=?";
        $query = $this->db->query($sql, array($PurchaseID));
        $data = $query->result_array();
        $result['data'] = $data[0];
        $sql_detail = "
            select *
            from ktv_purchase_detail
            WHERE PurchaseID=?";
        $query = $this->db->query($sql, array($PurchaseID));
        $result['detail'] = $query->result_array();
        return $result;
    }
    function createPembelian($Date,$OrgType,$OrgID,$SupplierID,$Total,$Pembayaran){
        $sql = "
            SELECT CONCAT(IF(?='koperasi','K','S'),YEAR(NOW()),LPAD(COUNT(PurchaseID)+1,7,0)) nomor
            FROM ktv_purchase
            WHERE OrgType=? AND OrgID=? AND YEAR(DATE)=YEAR(NOW())";
        $query = $this->db->query($sql, array($OrgType,$OrgType,$OrgID));
        $data = $query->result_array();
        $number = $data[0]['nomor'];
        $sql = "
            INSERT INTO ktv_purchase(OrgType,OrgID,Number,SupplierID,Date,Total,Pembayaran)
            VALUES (?,?,?,?,?,?,?)";
        $query = $this->db->query($sql, array($OrgType,$OrgID,$number,$SupplierID,$Date,str_replace(',','',$Total),
            str_replace(',','',$Pembayaran)));
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
    function updatePembelian($Date,$Total,$Pembayaran,$PurchaseID) {
        $sql = "
            UPDATE ktv_purchase
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
    function deletePembelian($id){
        $this->db->trans_start();
        $sql = "
            DELETE FROM ktv_purchase_detail WHERE PurchaseID=?";
        $query = $this->db->query($sql, array($id));
        $sql = "
            DELETE FROM ktv_purchase WHERE PurchaseID=?";
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
    function readPembelianDetail($id){
        $sql = "
            select ksd.*,concat('[',Number,'] ',Name) name,Qty*Price Total
            from ktv_purchase_detail ksd
            left join ktv_inventory ki on ksd.InventoryID=ki.InventoryID
            WHERE PurchaseID=?";
        $query = $this->db->query($sql, array($id));
        return $query->result_array();
    }
    function createPembelianDetail($PurchaseID,$InventoryID,$Qty,$Price){
        $sql = "
            INSERT INTO ktv_purchase_detail(PurchaseID,InventoryID,Qty,Price)
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
    function updatePembelianDetail($InventoryID,$Qty,$Price,$DetailID) {
        $sql = "
            UPDATE ktv_purchase_detail
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
    function deletePembelianDetail($id){
        $sql = "
            DELETE FROM ktv_purchase_detail WHERE DetailID=?";
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
    function readPembelianOrg($type){
        $sql = "
            select OrgID id,OrgName label
            from ktv_bussiness_org_view
            WHERE OrgType=?
            ORDER BY Name";
        $query = $this->db->query($sql, array($type));
        return $query->result_array();
    }
    function readPembelianSupplier($type,$id){
        $sql = "
            select kc.SupplierID id,concat('[',kc.SupplierID,'] ',kc.Name) label
            from ktv_supplier kc
            left join ktv_supplier_sce kcs on kcs.SupplierID=kc.SupplierID
            WHERE IF(?='sce',kcs.SceID,'')=?";
        $query = $this->db->query($sql, array($type,$id));
        return $query->result_array();
    }
    function readPembelianBarang($type,$id,$name){
        $sql = "
            select InventoryID id,concat('[',Number,'] ',Name) label, Name name,SellingPrice Price
            from ktv_inventory
            WHERE OrgType=? and OrgID=? and Name like ?";
        $query = $this->db->query($sql, array($type,$id,"%$name%"));
        return $query->result_array();
    }
    function createPurchaseSupplier($OrgType,$OrgID,$Name,$Email,$Address,$Phone,$Note,$Provinsi,$KabupatenTmp,$Kecamatan,
         $Kabupaten,$Desa){
        $sql = "
            INSERT IGNORE INTO ktv_supplier(OrgType,OrgID,Name,Email,Address,Phone,Note,VillageID)
            VALUES (?,?,?,?,?,?,?)";
        $query = $this->db->query($sql, array($OrgType,$OrgID,$Name,$Email,$Address,$Phone,$Note,$Desa));
        $suppid = $this->db->insert_id();
        if ($query) {
            $results['SupplierID'] = (string)$suppid;
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }
}
?>
