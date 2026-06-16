<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mbank extends CI_Model {

    public $variable;

    public function __construct()
    {
        parent::__construct();   
    }

    public function userDetail($UserId = null)
    {
        if (empty($UserId)) {
            $UserId = $_SESSION['userid'];
        }
        $sql = "SELECT
    bb.BranchName,
    bb.BranchID,
    p.ProvinceID,
    p.Province,
    d.DistrictID,
    d.District,
    sd.SubDistrictID,
    sd.SubDistrict
FROM ktv_bank_branch_staff bs
JOIN ktv_persons ps ON ps.PersonID = bs.PersonID
JOIN ktv_bank_branch bb ON bb.BranchID = bs.BranchID
LEFT JOIN ktv_province p ON p.ProvinceID = bb.BranchProvinceID
LEFT JOIN ktv_district d ON d.DistrictID = bb.BranchDistrictID
LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = bb.BranchSubDistrictID
WHERE
    ps.UserID = ?
        ";
        $query = $this->db->query($sql, array($UserId));
        if ($query->num_rows()>0) {
            return $query->row_array(0);
        }
        return false;
    }

}

/* End of file mbank.php */
/* Location: ./application/models/mbank.php */