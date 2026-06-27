<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Muserprofile extends CI_Model {

    public $variable;

    public function __construct()
    {
        parent::__construct();

    }

    public function getUserProfile($userid='')
    {
        if (empty($userid)) {
            $userid = $_SESSION['userid'];
        }
        $sql = "
SELECT
    u.UserId AS userid
    , u.UserName AS username
    , p.PersonNm AS `name`
    , u.UserIsAdmin AS is_admin
    , s.ObjType AS `type`
    , pp.FlagAccess
    , IFNULL(pvs.PartnerID,pgs.PartnerID) AS PartnerID
    , GROUP_CONCAT(DISTINCT cp.DistrictID) AS accessCPG
    , GROUP_CONCAT(DISTINCT dp.DistrictID) AS accessDistrict
    , GROUP_CONCAT(DISTINCT sa.DistrictID) AS accessStaff
FROM sys_user u
LEFT JOIN ktv_persons p ON p.UserID = u.UserId
LEFT JOIN ktv_staffs s ON s.PersonID = p.PersonID
LEFT JOIN ktv_private_staff pvs ON pvs.PersonID = p.PersonID
LEFT JOIN ktv_program_staff pgs ON pgs.PersonID = p.PersonID
LEFT JOIN ktv_program_partner pp ON pp.PartnerID = IFNULL(pvs.PartnerID,pgs.PartnerID)
LEFT JOIN ktv_district_partner dp ON IFNULL(pvs.PartnerID,pgs.PartnerID)=dp.PartnerID
LEFT JOIN (
    SELECT
        GROUP_CONCAT(DISTINCT sd.DistrictID) AS DistrictID,
        cp.PartnerID
    FROM ktv_cpg_partner cp
    JOIN ktv_cpg c ON c.CPGid = cp.CPGid
    LEFT JOIN ktv_village v ON v.VillageID = c.VillageID
    LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
    GROUP BY cp.PartnerID
) cp ON IFNULL(pvs.PartnerID,pgs.PartnerID)=cp.PartnerID
LEFT JOIN ktv_access_staff sa ON sa.UserId=u.UserId
WHERE
    u.UserId = ?
        ";
        $query = $this->db->query($sql, array($userid));
        if ($query->num_rows()>0) {
            $user = $query->row_array(0);
            if ($user['is_admin'] == 1) {
                # no limit
            } else {
                //get lagi aja dari query ktv_access_staff
                $sql="SELECT
                        GROUP_CONCAT(a.`DistrictID` SEPARATOR ',') AS daerah_akses
                    FROM
                        ktv_access_staff a
                    WHERE
                        a.`UserId` = ?";
                $query = $this->db->query($sql,array($_SESSION['userid']));
                $data = $query->row_array();
                $user['district_access'] = $data['daerah_akses'];
                // $user['district_access'] = $data['accessStaff'];

                // see login.php on front end
                //$user['district_access'] = $_SESSION['daerah_access'];

                /*if ($_SESSION['FlagAccess'] == '1') {
                    $sql="
SELECT
    GROUP_CONCAT(DISTINCT c.`DistrictID`) AS DistrictID
FROM
    ktv_staffs a
    INNER JOIN ktv_persons p ON a.`PersonID` = p.`PersonID`
    -- LEFT JOIN ktv_program_partner b ON a.`ObjID` = b.`PartnerID`
    LEFT JOIN ktv_access_staff c ON p.`UserID` = c.`UserId`
WHERE
    a.`ObjType` = ?
    AND p.`UserID` = ?";
                    $query = $this->db->query($sql,array($user['type'],$_SESSION['userid']));
                    if ($query->num_rows()>0) {
                        $user['district_access'] = $query->row_array(0)['DistrictID'];
                        // $user['cpg_access'] = $query->row_array(0)['CPGid'];
                    }
                } else {
                    $q = $this->db->get_where('ktv_access_staff', array('UserId'=>$_SESSION['userid']),1);
                    if ($q->num_rows() > 0) {
                        $sql = "
SELECT
    GROUP_CONCAT(a.DistrictID) AS district
FROM ktv_access_staff a
WHERE
    a.UserId = ?
                        ";
                        $query = $this->db->query($sql, array($_SESSION['userid']));
                    } else {
                        $sql = "
SELECT
    GROUP_CONCAT(dp.DistrictID) AS DistrictID
FROM ktv_district_partner dp
WHERE
    dp.PartnerID = ?
                        ";
                        $query = $this->db->query($sql, array($_SESSION['PartnerID']));
                    }
                    if ($query->num_rows()>0) {
                        $user['district_access'] = $query->row_array(0)['DistrictID'];
                    }
                }*/
            }
            return $user;
        }
        return false;
    }

}

/* End of file mprofile.php */
/* Location: ./application/models/mprofile.php */