<?php

class Mwarehouse extends CI_Model {    

    public function listWarehouse($province=null,$district=null,$subdistrict=null)
    {
        $this->db->select('c.WarehouseID AS id, c.WarehouseName AS name');
        $this->db->from('ktv_warehouse c');
        $this->db->join('ktv_village v', 'v.VillageID = c.VillageID');
        $this->db->join('ktv_subdistrict sd', 'sd.SubDistrictID = v.SubDistrictID');
        $this->db->join('ktv_district d', 'd.DistrictID = sd.DistrictID');
        $this->db->join('ktv_province p', 'p.ProvinceID = d.ProvinceID');
        if (!empty($province)) {
            $this->db->where('p.ProvinceID', $province, FALSE);
        }
        if (!empty($district)) {
            $this->db->where('d.DistrictID', $district, FALSE);
        }
        if (!empty($subdistrict)) {
            $this->db->where('sd.SubDistrictID', $subdistrict, FALSE);
        }
        $this->db->order_by('name', 'asc');
        $query = $this->db->get();
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
    }
}