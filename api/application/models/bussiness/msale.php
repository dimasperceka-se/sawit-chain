<?php
class Msale extends CI_Model {

    function readPenjualans($Awal,$Akhir,$OrgType,$OrgID,$start,$limit){
        $sql = "
            select %s
            from ktv_sale ks
            LEFT JOIN ktv_customer kc on ks.CustomerID=kc.CustomerID
            WHERE (date(Date) between ? and ?) and ks.OrgType=? and ks.OrgID=?
            ORDER BY Date desc
            %s";
        $query = $this->db->query(sprintf($sql,'SaleID,ks.OrgID,ks.OrgType,Date,Number,kc.CustomerID,Name,Total,Pembayaran','LIMIT ?,?'),
            array($Awal,$Akhir,$OrgType,$OrgID,(int)$start,(int)$limit));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql,'count(*) as total',''),array($Awal,$Akhir,$OrgType,$OrgID));
        $total = $query->result_array();
        $result['total'] = $total[0]['total'];
        return $result;
    }
    function readPenjualan($SaleID){
        $sql = "
            select *
            from ktv_sale
            WHERE SaleID=?";
        $query = $this->db->query($sql, array($SaleID));
        $data = $query->result_array();
        $result['data'] = $data[0];
        $sql_detail = "
            select *
            from ktv_sale_detail
            WHERE SaleID=?";
        $query = $this->db->query($sql, array($SaleID));
        $result['detail'] = $query->result_array();
        return $result;
    }
    function createPenjualan($Date,$OrgType,$OrgID,$CustomerID,$Total,$Pembayaran){
        $sql = "
            SELECT CONCAT(IF(?='koperasi','K','S'),YEAR(NOW()),LPAD(COUNT(SaleID)+1,7,0)) nomor
            FROM ktv_sale
            WHERE OrgType=? AND OrgID=? AND YEAR(DATE)=YEAR(NOW())";
        $query = $this->db->query($sql, array($OrgType,$OrgType,$OrgID));
        $data = $query->result_array();
        $number = $data[0]['nomor'];
        $sql = "
            INSERT INTO ktv_sale(OrgType,OrgID,Number,CustomerID,Date,Total,Pembayaran)
            VALUES (?,?,?,?,?,?,?)";
        $query = $this->db->query($sql, array($OrgType,$OrgID,$number,$CustomerID,$Date,str_replace(',','',$Total),
            str_replace(',','',$Pembayaran)));
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
    function updatePenjualan($Date,$Total,$Pembayaran,$SaleID) {
        $sql = "
            UPDATE ktv_sale
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
    function deletePenjualan($id){
        $this->db->trans_start();
        $sql = "
            DELETE FROM ktv_sale_detail WHERE SaleID=?";
        $query = $this->db->query($sql, array($id));
        $sql = "
            DELETE FROM ktv_sale WHERE SaleID=?";
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
    function readPenjualanDetail($id){
        $sql = "
            select ksd.*,concat('[',Number,'] ',Name) name,Qty*Price Total
            from ktv_sale_detail ksd
            left join ktv_inventory ki on ksd.InventoryID=ki.InventoryID
            WHERE SaleID=?";
        $query = $this->db->query($sql, array($id));
        return $query->result_array();
    }
    function createPenjualanDetail($SaleID,$InventoryID,$Problem,$Solution,$DateStart,$DateEnd,$Qty,$Price){
        $sql = "
            INSERT INTO ktv_sale_detail(SaleID,InventoryID,Problem,Solution,DateStart,DateEnd,Qty,Price)
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
    function updatePenjualanDetail($InventoryID,$Problem,$Solution,$DateStart,$DateEnd,$Qty,$Price,$DetailID) {
        $sql = "
            UPDATE ktv_sale_detail
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
    function deletePenjualanDetail($id){
        $sql = "
            DELETE FROM ktv_sale_detail WHERE DetailID=?";
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
    function readPenjualanOrg($type){
        $sql = "
            select OrgID id,OrgName label
            from ktv_bussiness_org_view
            WHERE OrgType=?
            ORDER BY Name";
        $query = $this->db->query($sql, array($type));
        return $query->result_array();
    }
    function readPenjualanBuyerOrg($typeBuyer,$type,$id){
//         $typeBuyer = $typeBuyer=='koperasi'?'Organisasi Petani':$typeBuyer=='warehouse'?'Gudang':'';
  //       $type = $type=='koperasi'?'Organisasi Petani':$typeBuyer=='warehouse'?'Gudang':'';
        $sql = "
            select a.OrgID id,a.Name label
            from ktv_bussiness_org_view a
            left join ktv_bussiness_org_view b on substr(a.VillageID,1,4)=substr(b.VillageID,1,4)
            WHERE a.OrgType=? and b.OrgType=? and b.OrgID=?
            ORDER BY a.Name";
        $query = $this->db->query($sql, array($typeBuyer,$type,$id));
        //echo $this->db->last_query();exit;
        return $query->result_array();
    }
    function readPenjualanCustomer($type,$id){
        $sql = "
            select kc.CustomerID id,concat('[',kc.CustomerID,'] ',kc.Name) label
            from ktv_customer kc
            left join ktv_customer_sce kcs on kcs.CustomerID=kc.CustomerID
            WHERE IF(?='sce',kcs.SceID,'')=?";
        $query = $this->db->query($sql, array($type,$id));
        return $query->result_array();
    }
    function readPenjualanBarang($type,$id,$name){
        $sql = "
            select InventoryID id,concat('[',Number,'] ',Name) label, Name name,SellingPrice Price
            from ktv_inventory
            WHERE OrgType=? and OrgID=? and Name like ?";
        $query = $this->db->query($sql, array($type,$id,"%$name%"));
        return $query->result_array();
    }
    function createSaleCustomer($FarmerID,$OrgType,$OrgID,$Name,$Email,$Address,$Phone,$Note,$Provinsi,$KabupatenTmp,$Kecamatan,
         $Kabupaten,$Desa){
        $sql = "
            INSERT IGNORE INTO  ktv_customer(FarmerID,Name,Email,Address,Phone,Note,VillageID)
            VALUES (?,?,?,?,?,?,?)";
        $query = $this->db->query($sql, array($FarmerID,$Name,$Email,$Address,$Phone,$Note,$Desa));
        $custid = $this->db->insert_id();
        if ($OrgType=='sce') {
           $sql = "
               INSERT INTO ktv_customer_sce(CustomerID,SceID) VALUES (?,?)";
           $query = $this->db->query($sql, array($this->db->insert_id(),$OrgID));
        }
        if ($query) {
            $results['CustomerID'] = (string)$custid;
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }
    function readFarmer($key,$start=0,$limit=10){
        $sql = "
            select %s
            from ktv_farmer kcf
            left join ktv_village kv ON kv.VillageID=kcf.VillageID
            left join ktv_subdistrict ks ON ks.SubDistrictID=kv.SubDistrictID
            left join ktv_district kd ON kd.DistrictID=ks.DistrictID
            left join ktv_province kp ON kp.ProvinceID=kd.ProvinceID
            WHERE FarmerName like ?
            ORDER BY FarmerName desc
            %s";
        $query = $this->db->query(sprintf($sql,'FarmerID,FarmerName,kcf.VillageID,"" Email,HandPhone Phone,Address,
            District Kabupaten,SubDistrict Kecamatan,Province Provinsi','LIMIT ?,?'),
            array("%$key%",(int)$start,(int)$limit));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql,'count(*) as total',''),array("%$key%"));
        $total = $query->result_array();
        $result['total'] = $total[0]['total'];
        return $result;
    }

}
?>
