<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MCommon extends CI_Model {

    protected $access_area = array();
    protected $traceability_trans_access = false;
    protected $warehouses = array();
    protected $traders = array();

    public function __construct() {
        parent::__construct();
        $this->_getUserAccessArea(); 
        $this->_getPartnerWarehouses();
        $this->_getUserAccessTrans();
        
    }

    public function listStaffType() {
        $sql = "SELECT r.RoleCode AS id,r.RoleName AS `name` FROM sys_role r WHERE r.RoleMasterTrainingParticipant = 1 ORDER BY `name`";
        $query = $this->db->query($sql, array($var));
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    protected function _getPartnerWarehouses() {

        $this->db->select('SupplychainID');
        $this->db->from('ktv_supplychain_org');
        $this->db->where('OrgType', 'warehouse');
        $this->db->join('ktv_warehouse', 'ktv_warehouse.WarehouseID = ktv_supplychain_org.OrgID', 'left');
        if ((string) $_SESSION['role'] === 'Private') {
            $this->db->where('PartnerID', (int) $_SESSION['PartnerID']);
        }
        $Q = $this->db->get();        
        
        if ($Q->num_rows() > 0) { 
            $result = $Q->result_array();
            foreach ($result as $keys => $values) {
                array_push($this->warehouses, $values['SupplychainID']);
            }
        }
    }

    public function getComboPedagang() {
        
        if (count($this->access_area) > 0) {
            $pedagang = $this->db->query('SELECT a.ChildOrgId orgid, b.Name name
            FROM
                ktv_supplychain_org_rel a
                LEFT JOIN ktv_supplychain_org_view b ON a.ChildOrgId=b.SupplychainID
                LEFT JOIN ktv_traders traders ON traders.TraderID=b.OrgID
                LEFT JOIN ktv_village v ON v.VillageID = traders.VillageID
                LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
            WHERE
             a.ParentOrgId IN(' . implode(',', $this->warehouses) . ')  AND b.OrgType != "Organisasi Petani" AND sd.DistrictID IN(' . implode(',', $this->access_area) . ')');
            if ($pedagang->num_rows() > 0) {
                return $pedagang->result_array();
            }
        }
        
        return array();
    }

    protected function _getUserAccessArea() {

        $output = array();

        $this->db->select('DistrictID');
        $this->db->from('ktv_access_staff');
        $this->db->where('UserId', (int) $_SESSION['userid']);
        $Q = $this->db->get();
        if ($Q->num_rows() > 0) {
            $row = $Q->result_array();
            foreach ($row as $key => $value) {
                array_push($this->access_area, $value['DistrictID']);
            }
        }

    }

    protected function _getUserAccessTrans() {

        $this->db->select('UserSetValue');
        $this->db->from('sys_user_setting');
        $this->db->where('UserSetSetTmplID', 2);
        $this->db->where('UserSetUserId', (int) $_SESSION['userid']);
        $Q = $this->db->get();
        if ($Q->num_rows() > 0) {
            $row = $Q->row();
            if ($row->UserSetValue === 'Yes') {
                $this->traceability_trans_access = true;
            }
        }
    }

    public function getComboDistrict() {
        if (count($this->access_area) > 0) {
            $this->db->select('DistrictID,District');
            $this->db->from('ktv_district');
            $this->db->where_in('DistrictID', $this->access_area);
            $Q = $this->db->get();
            if ($Q->num_rows() > 0) {
                $result = $Q->result_array();
                return $result;
            }
        }
        return array();
    }

    public function getComboSubDistrict($district = false) {
        if (count($this->access_area) > 0) {
            $this->db->select('SubDistrictID,SubDistrict,ktv_subdistrict.DistrictID');
            $this->db->from('ktv_subdistrict');
            $this->db->where_in('DistrictID', $this->access_area);
            if ($district) {
                $this->db->where('DistrictID', $district);
            }
            $Q = $this->db->get();
            if ($Q->num_rows() > 0) {
                $result = $Q->result_array();
                return $result;
            }
        }
        return array();
    }

    public function getComboVillage($subdistrict = false) {
        if (count($this->access_area) > 0) {
            $this->db->select('VillageID,Village,ktv_village.SubDistrictID');
            $this->db->from('ktv_village');
            $this->db->join('ktv_subdistrict', 'ktv_subdistrict.SubDistrictID = ktv_village.SubDistrictID', 'LEFT');
            $this->db->where_in('DistrictID', $this->access_area);
            if ($subdistrict) {
                $this->db->where('ktv_village.SubDistrictID', $subdistrict, false);
            }
            $Q = $this->db->get();
            if ($Q->num_rows() > 0) {
                $result = $Q->result_array();
                return $result;
            }
        }

        return array();
    }

    public function getComboCpg($village = false) {
        
        $this->db->select('CPGid,GroupName,ktv_cpg.VillageID');
        $this->db->from('ktv_cpg');
        $this->db->join('ktv_village', 'ktv_village.VillageID = ktv_cpg.VillageID', 'LEFT');
        $this->db->join('ktv_subdistrict', 'ktv_subdistrict.SubDistrictID = ktv_village.SubDistrictID', 'LEFT');
        if(count($this->access_area) > 0){
            $this->db->where_in('DistrictID', $this->access_area);
        }
        if ($village) {
            $this->db->where('ktv_cpg.VillageID', $village, false);
        }
        $Q = $this->db->get();
        if ($Q->num_rows() > 0) {
            $result = $Q->result_array();
            foreach($result as $keys => $values) {
                $result[$keys]['GroupName'] = str_replace("'","",$values['GroupName']);
            }
            
            return $result;
        }
    }

}