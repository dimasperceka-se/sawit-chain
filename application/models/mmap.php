<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mmap extends CI_Model {

    public $variable;

    public function __construct()
    {
        parent::__construct();
        
    }

    public function checkSupplyAccess($userid)
    {
        $query = $this->db->get_where('ktv_private_staff', array('UserId' => $userid), 1);

        if ($query->num_rows() > 0) {
            // deny access for private staff, temporary
            return false;
            // private staff, do more checking
            $sql = "SELECT
    -- wh.WarehouseName,
    IF(so.SupplychainID,1,0) AS is_bu
FROM ktv_private_staff ps
JOIN ktv_warehouse wh ON wh.PartnerID = ps.PartnerID
JOIN ktv_supplychain_org so ON so.OrgID = wh.WarehouseID AND (so.StatusCode IS NULL OR so.StatusCode != 'nullified')
JOIN rpt_traceability r ON r.wh_orgid = wh.WarehouseID
WHERE
    ps.UserId = ?
            ";
            $query = $this->db->query($sql, array($userid));
            if ($query->num_rows() > 0) {
                $wh_is_bu = false;
                foreach ($query->result_array() as $key => $value) {
                    if ($value['is_bu'] == '1') {
                        $wh_is_bu = true;
                        break;
                    }
                }
                return $wh_is_bu;
            }
            // there are no wh
            return false;            
        }
        // not a private staff, then allow it
        return true;
    }

    public function checkBankAccess($userid)
    {
        $sql = "SELECT
    u.UserId,
    IF(ps.UserId, 1, 0) AS program_staff,
    IF(ug.UserGroupGroupId, 1, 0) AS admin
FROM sys_user u
LEFT JOIN ktv_program_staff ps ON ps.UserId = u.UserId
LEFT JOIN sys_user_group ug ON ug.UserGroupUserId = u.UserId AND ug.UserGroupGroupId = 1
where
    u.UserId = ?
";
        $query = $this->db->query($sql, array($userid));
        if ($query->num_rows()>0) {
            foreach ($query->result_array() as $key => $value) {
                if ($value['program_staff'] == '1') {
                    return true;
                }
                if ($value['admin'] == '1') {
                    return true;
                }
            }
        }
        return false;
    }
}

/* End of file map.php */
/* Location: ./application/models/map.php */