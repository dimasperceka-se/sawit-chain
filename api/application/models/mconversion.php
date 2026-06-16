<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mconversion extends CI_Model {

    public $variable;

    public function __construct()
    {
        parent::__construct();
        
    }

    public function insertPerson($data)
    {
        $result = $this->db->insert('ktv_persons', $data);
        if ($result) {
            return $this->db->insert_id();
        }
        return false;
    }

    public function getBankStaffs()
    {
        $query = $this->db->get('ktv_bank_branch_staff');
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function updateBankStaff($data, $StaffID)
    {
        return $this->db->update('ktv_bank_branch_staff', $data, array('StaffID' => $StaffID));
    }

    public function getCooperativeStaffs()
    {
        // $this->db->where('UserId IS NOT NULL');
        $this->db->select('cs.*, f.FarmerName', FALSE);
        $this->db->join('ktv_farmer f', 'f.FarmerID = cs.FarmerID', 'left');
        $query = $this->db->get('ktv_cooperative_staff cs');
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function updateCooperativeStaff($data, $StaffID)
    {
        return $this->db->update('ktv_cooperative_staff', $data, array('StaffID' => $StaffID));
    }

    public function getCPGStaffs()
    {
        // $this->db->where('UserId IS NOT NULL');
        $query = $this->db->get('ktv_cpg_staff');
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function updateCPGStaffData() 
    {
        $sql = "UPDATE ktv_cpg_staff s, ktv_farmer f
SET
    s.StaffName = f.FarmerName,
    s.StaffBirthday = f.Birthdate,
    s.StaffGender = f.Gender
WHERE
    s.FarmerID = f.FarmerID";
        return $this->db->query($sql);
    }

    public function updateCPGStaff($data, $StaffID)
    {
        return $this->db->update('ktv_cpg_staff', $data, array('StaffID' => $StaffID));
    }

    public function getExtensionStaffs()
    {
        // $this->db->where('UserId IS NOT NULL');
        $query = $this->db->get('ktv_extension_staff');
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function updateExtensionStaff($data, $ExtensionID)
    {
        return $this->db->update('ktv_extension_staff', $data, array('ExtensionID' => $ExtensionID));
    }

    public function getPrivateStaffs()
    {
        // $this->db->where('UserId IS NOT NULL');
        $query = $this->db->get('ktv_private_staff');
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function updatePrivateStaff($data, $PrivateStaffID)
    {
        return $this->db->update('ktv_private_staff', $data, array('PrivateStaffID' => $PrivateStaffID));
    }

    public function getSCEStaffs()
    {
        // $this->db->where('UserId IS NOT NULL');
        $query = $this->db->get('sce_farmer_staff');
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function updateSCEStaff($data, $StaffID)
    {
        return $this->db->update('sce_farmer_staff', $data, array('StaffID' => $StaffID));
    }

    public function getTraderStaffs()
    {
        // $this->db->where('UserId IS NOT NULL');
        $query = $this->db->get('ktv_trader_staff');
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function updateTraderStaff($data, $TraderStaffID)
    {
        return $this->db->update('ktv_trader_staff', $data, array('TraderStaffID' => $TraderStaffID));
    }

    public function getWarehouseStaffs()
    {
        // $this->db->where('UserId IS NOT NULL');
        $query = $this->db->get('ktv_warehouse_staff');
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function updateWarehouseStaff($data, $StaffID)
    {
        return $this->db->update('ktv_warehouse_staff', $data, array('StaffID' => $StaffID));
    }

}

/* End of file mconversion.php */
/* Location: ./application/models/mconversion.php */