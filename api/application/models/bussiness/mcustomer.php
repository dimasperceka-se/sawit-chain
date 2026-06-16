<?php
class Mcustomer extends CI_Model {

    function readDatas($start,$limit){
        $sql = "
            select %s
            from ktv_customer
            %s";
        $query = $this->db->query(sprintf($sql,'CustomerID, Name, Email, Phone, Address, Note','LIMIT ?,?'),
            array((int)$start,(int)$limit));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql,'count(*) as total',''));
        $total = $query->result_array();
        $result['total'] = $total[0]['total'];
        return $result;
    }
    function readData($CustomerID){
        $sql = "
            select a.*,e.Province Provinsi,d.District Kabupaten,c.SubDistrict Kecamatan,a.VillageID Desa,FarmerName
            from ktv_customer a
            left join ktv_village b on a.VillageID=b.VillageID
            left join ktv_subdistrict c on b.SubDistrictID=c.SubDistrictID
            left join ktv_district d on c.DistrictID=d.DistrictID
            left join ktv_province e on d.ProvinceID=e.ProvinceID
            left join ktv_farmer f on a.FarmerID=f.FarmerID
            WHERE a.CustomerID=?";
        $query = $this->db->query($sql, array($CustomerID));
        $data = $query->result_array();
        $result['data'] = $data[0];
        $sql_sce = "
            select *
            from ktv_customer_sce
            WHERE CustomerID=?";
        $query = $this->db->query($sql_sce, array($CustomerID));
        $data = $query->result_array();
        $result['sce'] = $data[0];
        return $result;
    }
    function createData($OrgType,$OrgID,$FarmerID, $Name,$Email,$Phone,$Address,$Note,$VillageID){
        $sql = "
            INSERT INTO ktv_customer(FarmerID, Name,Email,Phone,Address,Note,VillageID) 
            VALUES (?,?,?,?,?,?,?)";
        $query = $this->db->query($sql, array($FarmerID==''?NULL:$FarmerID, $Name,$Email,$Phone,$Address,$Note,$VillageID));
        $sql_sce = "
            INSERT INTO ktv_customer_sce(CustomerID,SceID) VALUES (?,?)";
        $query = $this->db->query($sql_sce, array($this->db->insert_id(),$OrgID));
        if ($query) {
            $results['CustomerID'] = $this->db->insert_id();
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }
    function updateData($OrgType,$OrgID,$Name,$Email,$Phone,$Address,$Note,$VillageID,$CustomerID) {
        $sql = "
            UPDATE ktv_customer
            SET Name=?,Email=?,Phone=?,Address=?,Note=?,VillageID=?
            WHERE CustomerID=?";
        $query = $this->db->query($sql, array($Name,$Email,$Phone,$Address,$Note,$VillageID,$CustomerID));
        $sql_sce = "
            UPDATE ktv_customer_sce
            SET SceID=?
            WHERE CustomerID=? and SceID=?";
        $query = $this->db->query($sql_sce, array($OrgID,$CustomerID,$OrgID));
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
            DELETE FROM ktv_customer_sce WHERE CustomerID=?";
        $query = $this->db->query($sql, array($id));
        $sql = "
            DELETE FROM ktv_customer WHERE CustomerID=?";
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
