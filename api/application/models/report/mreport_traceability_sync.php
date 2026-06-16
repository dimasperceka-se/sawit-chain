<?php
class Mreport_traceability_sync extends CI_Model
{
    
    protected $access_area = array(); 
    protected $traceability_trans_access = false;
    protected $warehouses = array();
    protected $traders = array();
    
    public function __construct() {
        parent::__construct();
        
        $this->_getUserAccessArea(); 
        $this->_getPartnerWarehouses();
        $this->_getPartnerBuyingUnit();
        $this->_getUserAccessTrans();
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
    
    public function _getPartnerBuyingUnit() {
        
        if (count($this->access_area) > 0) {
            $pedagang = $this->db->query('SELECT a.ChildOrgId orgid, b.Name name
            FROM
                ktv_supplychain_org_rel a
                LEFT JOIN ktv_supplychain_org_view b ON a.ChildOrgId=b.SupplychainID
                LEFT JOIN ktv_traders traders ON traders.TraderID=b.OrgID
            WHERE
             a.ParentOrgId IN(' . implode(',', $this->warehouses) . ')  AND b.OrgType != "Organisasi Petani" AND SUBSTR(traders.VillageID,1,4) IN(' . implode(',', $this->access_area) . ')');
            if ($pedagang->num_rows() > 0) {
                $result = $pedagang->result_array();
                foreach($result as $keys => $values) {
                    array_push($this->traders, $values['orgid']);
                }
            }
        }
        
        return array();
    }
    
    public function getComboPedagang() {
        
        if (count($this->access_area) > 0) {
            $pedagang = $this->db->query('SELECT a.ChildOrgId orgid, b.Name name
            FROM
                ktv_supplychain_org_rel a
                LEFT JOIN ktv_supplychain_org_view b ON a.ChildOrgId=b.SupplychainID
                LEFT JOIN ktv_traders traders ON traders.TraderID=b.OrgID
            WHERE
             a.ParentOrgId IN(' . implode(',', $this->warehouses) . ')  AND b.OrgType != "Organisasi Petani" AND SUBSTR(traders.VillageID,1,4) IN(' . implode(',', $this->access_area) . ')');
            if ($pedagang->num_rows() > 0) {
                
                foreach($pedagang as $keys => $values) {
                    array_push($this->traders, $values['orgid']);
                }
                
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
            $this->db->select('SubDistrictID,SubDistrict');
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
            $this->db->select('VillageID,Village');
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
        
        $this->db->select('CPGid,GroupName');
        $this->db->from('ktv_cpg');
        $this->db->join('ktv_village', 'ktv_village.VillageID = ktv_cpg.VillageID', 'LEFT');
        $this->db->join('ktv_subdistrict', 'ktv_subdistrict.SubDistrictID = ktv_village.SubDistrictID', 'LEFT');
        $this->db->where_in('DistrictID', $this->access_area);
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
    
    public function get_data_traceability_sync($limit = 50, $start = 0, $xls = false, $orgid = false, $batch_from = false, $batch_to = false, 
        $trans_from = false, $trans_to = false, $district = false, $subdistrict = false, $village = false, $batch_number = false, $nopo = false, $farmer = false) {
        
        $output = array();
        
        if($orgid == 0) { $orgid = ''; }
        
        $sql = "SELECT
       CONCAT(TraderID,' - ',TraderName) BuyingUnit,
       SupplyBatchNumber,
       InvoiceNumber,
       SupplyBatchDate BatchDate,
       DestPO,
       trans.DateTransaction,
       trans.SupplyID FarmerID,
       FarmerName,
       Village,
       SubDistrict,
       District,
       farmer.CPGid,
       GroupName,
       trans.FakturNumber,
       trans.FAQVolumeBruto Bruto,
       FAQVolumeNetto Netto,
       FAQQualityKA AS Moisture,
        FAQQualityBC AS BeanCount,
       FAQQualityMouldy AS Mouldy,
       FAQQualityWaste AS Waste,
       FAQQualityInsect AS Insect,
       FAQQualitySlaty AS Slaty,
       trans.FAQContractPrice ContractPrice,
       trans.FAQNetPrice NetPrice,
        trans.FAQTotalPayment TotalPayment
      FROM
       ktv_supplychain_transaction trans
      LEFT JOIN ktv_supplychain_batch batch ON batch.SupplyBatchID = trans.SupplyBatchID
      LEFT JOIN ktv_supplychain_org org ON org.SupplychainID = batch.SupplyOrgID
      LEFT JOIN ktv_traders trader ON trader.TraderID = org.OrgID
      LEFT JOIN ktv_farmer farmer ON farmer.FarmerID = trans.SupplyID
      LEFT JOIN ktv_village village ON village.VillageID = farmer.VillageID
      LEFT JOIN ktv_subdistrict subdistrict ON subdistrict.SubDistrictID = village.SubDistrictID
      LEFT JOIN ktv_district district ON district.DistrictID = subdistrict.DistrictID
      LEFT JOIN ktv_cpg cpg ON cpg.CPGid = farmer.CPGid";
        
        if(count($this->access_area) > 0 && count($this->traders) > 0){
            $sql .= " WHERE SUBSTR(trader.VillageID,1,4) in(". implode(',', $this->access_area) .") AND SupplychainID IN(". implode(',', $this->traders) .")";
        } else {
            $sql .= " WHERE SupplyOrgID = '" . $orgid . "'";
        }
        
        if (strlen($orgid) > 0) {
            $sql .= " AND SupplyOrgID = '" . $orgid . "'";
        }

        if (strlen($nopo) > 0) {
            $sql .= " AND DestPO like '%" . $nopo . "%'";
        }

        if (strlen($batch_number) > 0) {
            $sql .= " AND SupplyBatchNumber like '%" . $batch_number . "%'";
        }
        
        if (strlen($district) > 0) {
            $sql .= " AND subdistrict.DistrictID = '" . $district . "'";
        }
        
        if (strlen($subdistrict) > 0) {
            $sql .= " AND village.SubDistrictID = '" . $subdistrict . "'";
        }
        
        if (strlen($village) > 0) {
            $sql .= " AND farmer.VillageID = '" . $village . "'";
        }
        
        if (strlen($batch_from) > 0 && strlen($batch_to) > 0) {
            $sql .= " AND (SupplyBatchDate BETWEEN '" . date('Y-m-d',strtotime($batch_from)) . "' AND '" . date('Y-m-d',strtotime($batch_to)) . "')";
        }
        
        if (strlen($trans_from) > 0 && strlen($trans_to) > 0) {
            $sql .= " AND (DateTransaction BETWEEN '" . date('Y-m-d',strtotime($trans_from)) . "' AND '" . date('Y-m-d',strtotime($trans_to)) . "')";
        }
        
        if (strlen($farmer) > 0) {
            $sql .= " AND trans.SupplyID = '" . $farmer . "'";
        }
        //var_dump($sql);die;
        $Q     = $this->db->query($sql);
        $total = $Q->num_rows();

        if ($xls == 'true') { 
            $output = $Q->result_array();
            return $output;
        }

        //terakhir set limit
        $sql .= " LIMIT " . $start . "," . $limit;
        $Q = $this->db->query($sql);
        if ($Q->num_rows() > 0) {
            $output = $Q->result_array();
        }

        $data['data']  = $output;
        $data['total'] = $total;

        return $data;

    }

}
