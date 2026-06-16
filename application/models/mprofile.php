<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mprofile extends CI_Model {

    public $variable;

    public function __construct()
    {
        parent::__construct();
    }

    public function getHistoryLogin($userid){
        $this->db->limit(10);
        $this->db->order_by('LogId', 'desc');
        $query = $this->db->get_where('sys_log_access', array('UserID' => $userid))->result_array();
        return $query;

    }

    public function getLastAccess($UserId = null)
    {
        if (empty($UserId)) {
            $UserId = $_SESSION['userid'];
        }

        $sql="SELECT
                a.`Timestamp`
            FROM
                sys_log_access a
            WHERE
                a.`UserID` = ?
                AND a.type = 'Login'
                AND a.`AttempProcess` = 'Success'
            ORDER BY a.`Timestamp` DESC
            LIMIT 1";
        $query = $this->db->query($sql,array($UserId));

        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
        return array();
    }

    public function getLastPageAccess($UserId = null)
    {
        if (empty($UserId)) {
            $UserId = $_SESSION['userid'];
        }
        $this->db->order_by('Timestamp', 'desc');
        $base_url = base_url();
        $this->db->where("Page NOT LIKE('{$base_url}')");
        $this->db->where("Page NOT LIKE('%system/login/logout%')");
        $query = $this->db->get_where('sys_log_page_access', array('UserID' => $UserId), 5);

        $data = array();
        if ($query->num_rows() > 0) {
            $sql = "SELECT
    p.MenuName AS parent_menu,
    m.MenuName AS menu
    -- IF(p.MenuName IS NULL, m.MenuName, CONCAT(p.MenuName, ' - ',m.MenuName)) AS Menu
FROM
    sys_menu m
LEFT JOIN sys_menu p ON m.MenuParentId = p.MenuId
WHERE
    m.MenuModule = ?
            ";
            $result = $query->result_array();
            foreach ($result as &$access) {
                $tmp        = explode('?', $access['Page']);
                //$url_part   = explode('/', $tmp[0]);
                $module = str_replace(base_url(),'',$tmp[0]);
                //$param      = !empty($url_part[6])?$url_part[6]:'';
                // $q = $this->db->get_where('sys_menu', array('MenuModule' => $module, 'MenuParam' => $param), 1);
                $q = $this->db->query($sql, array($module));

                if ($q->num_rows() > 0) {
                    $data[] = array_merge($access, $q->row_array(0));
                } else {
                    $data[] = $access;
                }
            }
            return $data;
        }

        return array();
    }

    public function getParentMenu($id)
    {

    }

    public function isAdmin()
    {
        $query = $this->db->get_where('sys_user', array('UserId'=>$_SESSION['userid']), 1);
        if ($user = $query->row_array(0)) {
            if ($user['UserIsAdmin'] == 1) {
                return true;
            }
        }
        return false;
    }

    public function check_user($userid = null)
    {
        $user = array();
        if (empty($userid)) {
            $userid = $_SESSION['userid'];
        }
        $sql = "SELECT
            u.UserId,
            u.UserIsAdmin,
            IF(pgs.StaffID,1,0) AS isProgramStaff,
            pgs.PartnerID AS programPartner,
            IF(pvs.PrivateStaffID,1,0) AS isPrivateStaff,
            pvs.PartnerID AS privatePartner,
            pp.FlagAccess,
            GROUP_CONCAT(DISTINCT cp.DistrictID) AS cpgPartner,
            GROUP_CONCAT(DISTINCT dp.DistrictID) AS districtPartner,
            GROUP_CONCAT(DISTINCT sa.DistrictID) AS accessStaff
        FROM sys_user u
        LEFT JOIN ktv_persons p ON p.UserID = u.UserId
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
        GROUP BY u.UserId
        ";
        $query = $this->db->query($sql, array($userid));
        if ($query->num_rows() > 0) {
            $result = $query->row_array(0);
            if ($result['isPrivateStaff'] || $result['isProgramStaff']) {
                // if (!empty($result['cpgPartner'])) {
                //   $result['district_access'] = $result['cpgPartner'];
                // } elseif (!empty($result['accessStaff'])) {
                //   $result['district_access'] = $result['accessStaff'];
                // } elseif (!empty($result['districtPartner'])) {
                //   $result['district_access'] = $result['districtPartner'];
                // }
                $result['district_access'] = implode(',', array_intersect(explode(',', $result['accessStaff']), explode(',', $result['cpgPartner'])));
            } else {
                $result['district_access'] = !empty($result['accessStaff']) ? $result['accessStaff'] : $result['districtPartner'];
            }

            //cek pengecualian terakhir, kalau tidak ada, maka ambil dari access staff
            if($result['district_access'] == "") {
                $result['district_access'] = $result['accessStaff'];
            }

            return $result;
        }
        return false;
    }

    public function getUserAccess($prov = '', $dist = '', $subdist = '', $mentokDistrict = false)
    {
        $user       = $this->check_user();
        $label      = '';
        $params     = array();
        $dir        = $this->router->fetch_directory();
        $class      = $this->router->fetch_class();

        if (self::isAdmin() == false) {

            //province dan district tanpa cek status
            switch ($dir.$class) {
                case 'training/master':
                    // http://redmine.koltiva.com/issues/2472
                    if ($user['isPrivateStaff'] || $user['isProgramStaff']) {
                        if (!empty($user['accessStaff'])) {
                            $user['district_access'] = $user['accessStaff'];
                        } elseif (!empty($user['districtPartner'])) {
                            $user['district_access'] = $user['districtPartner'];
                        }
                    }
                    // End of : http://redmine.koltiva.com/issues/2472
                    $sql = "SELECT
                            %s AS id,
                            %s AS label
                            FROM ktv_district d
                            LEFT JOIN ktv_subdistrict sd ON sd.DistrictID = d.DistrictID
                            JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
                            WHERE 1 = 1
                            %s
                            GROUP BY label
                    ";
                    $where          = " AND d.DistrictID IN ({$user['district_access']})";
                break;
                default:
                    $sql = "SELECT
                            %s AS id,
                            %s AS label
                            FROM ktv_district d
                            LEFT JOIN ktv_subdistrict sd ON sd.DistrictID = d.DistrictID AND sd.StatusCode = 'active'
                            JOIN ktv_province p ON p.ProvinceID = d.ProvinceID AND p.StatusCode = 'active'
                            WHERE 1 = 1
                            %s
                            GROUP BY label
                    ";
                    $where          = " AND d.DistrictID IN ({$user['district_access']})";
                break;
            }
        } else {

            //province dan district tanpa cek status
            switch ($class) {
                case 'master':
                    $sql = "SELECT
                            %s AS id,
                            %s AS label
                            FROM ktv_district d
                            LEFT JOIN ktv_subdistrict sd ON sd.DistrictID = d.DistrictID
                            JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
                            WHERE 1 = 1
                            %s
                            GROUP BY label
                                    ";
                    $where          = '';
                break;
                default:
                    $sql = "SELECT
                            %s AS id,
                            %s AS label
                            FROM ktv_district d
                            LEFT JOIN ktv_subdistrict sd ON sd.DistrictID = d.DistrictID AND sd.StatusCode = 'active'
                            JOIN ktv_province p ON p.ProvinceID = d.ProvinceID AND p.StatusCode = 'active' and p.active = 1
                            WHERE 1 = 1
                            %s
                            GROUP BY label
                                    ";
                    $where          = '';
                break;
            }

        }

        $curr_region    = 'All Province';

        $province       = self::getProvince($prov);
        $district       = self::getDistrict($dist);
        $subdistrict    = self::getSubDistrict($subdist);

        if (empty($prov)) {
            $id     = 'p.ProvinceID';
            $label  = 'Province';
        } else {
            if (empty($dist)) {
                $id             = 'd.DistrictID';
                $label          = 'District';
                $where         .= " AND p.ProvinceID = ?";
                $params[]       = $prov;
                $curr_region    = $province['name'];
            } else {
                if ($mentokDistrict == true){
                    $id             = 'd.DistrictID';
                    $label          = 'District';
                    $where         .= " AND p.ProvinceID = ?";
                    $params[]       = $prov;
                    $curr_region    = $district['name'];
                } else {
                    $id             = 'sd.SubDistrictID';
                    $label          = 'SubDistrict';
                    $where         .= " AND d.DistrictID = ?";
                    $params[]       = $dist;
                }
                if (empty($subdist)) {
                    $curr_region    = $district['name'];
                } else {
                    $curr_region    = $subdistrict['name'];
                }
            }
        }
        $sql = sprintf($sql, $id, $label, $where);
        $query = $this->db->query($sql, $params);

        if ($result = $query->result_array()) {
            return array(
                'class'             => $class,
                'prov'              => $prov,
                'prov_name'         => $province['name'],
                'dist'              => $dist,
                'dist_name'         => $district['name'],
                'subdist'           => $subdist,
                'subdist_name'      => $subdistrict['name'],
                'current_region'    => $curr_region,
                'data'              => $result,
            );
        }
        return false;
    }

    public function getProvince($id)
    {
        $this->db->select('ProvinceID AS id, Province AS name', FALSE);
        $query = $this->db->get_where('ktv_province', array('ProvinceID' => $id), 1);
        if ($query->num_rows()>0) {
            return $query->row_array(0);
        }
        // return false;
    }

    public function getDistrict($id)
    {
        $this->db->select('DistrictID AS id, District AS name', FALSE);
        $query = $this->db->get_where('ktv_district', array('DistrictID' => $id), 1);
        if ($query->num_rows()>0) {
            return $query->row_array(0);
        }
        // return false;
    }

    public function getSubDistrict($id)
    {
        $this->db->select('SubDistrictID AS id, SubDistrict AS name', FALSE);
        $query = $this->db->get_where('ktv_subdistrict', array('SubDistrictID' => $id), 1);
        if ($query->num_rows()>0) {
            return $query->row_array(0);
        }
        // return false;
    }
}

/* End of file mprofile.php */
/* Location: ./application/models/mprofile.php */