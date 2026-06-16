<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Profile extends SS_Controller
{

    public function __construct()
    {
        parent::__construct(1);
        $this->lang->load('profile');
    }

    public function index()
    {
        $data['js']     = 'profile';
        $api            = $this->config->item('api');
        $data['action'] = array(
            'crud'          => $api . '/system/user',
            'id'            => $_SESSION['userid'],
            'staff'         => $api . '/system/staff',
            'staff_profile' => $api . '/system/staff_profile',
            'pass'          => $api . '/system/userpass',

            'photo'         => $this->config->item('api_base_url') . 'images/Photo/',
            'profile'       => $api . '/system/user',
        );
        $this->LoadView($data);
    }

    public function change_group($groupid)
    {
        // check if group id belong to current user
        $sql = "SELECT
    UserGroupId,
    GroupFilterBy
FROM
    sys_user_group
LEFT JOIN sys_group ON GroupId = UserGroupGroupId
WHERE
    UserGroupUserId = ?
    AND UserGroupGroupId = ?";
        $query = $this->db->query($sql, array($_SESSION['userid'], $groupid));
        if ($query->num_rows() > 0) {
            // change group id
            $_SESSION['groupid']   = $groupid;
            $_SESSION['filter_by'] = $query->row_array(0)['GroupFilterBy'];
            $_SESSION['filter_id'] = null;
        }
        // echo '<pre>'; print_r($_SESSION); echo '</pre>'; exit;
        // exit('here');
        redirect('');
    }

    public function change_user_affiliate($UserIdGanti){
        $UserIdBeforeSwitch = $_SESSION['userid_beforeswitch'];
        $UserIdGanti = (int) $UserIdGanti;

        //Cek dl securitynya,apakah berhak ganti
        $sql="SELECT
                a.`UserId`
            FROM
                sys_user_affiliate a
            WHERE
                a.`UserId` = ?
                AND a.`UserIdAff` = ?
                AND a.`StatusCode` = 'active'
            LIMIT 1";
        $p = array(
            $UserIdBeforeSwitch,
            $UserIdGanti
        );
        $query = $this->db->query($sql,$p);
        $dataCek = $query->row_array();

        if($dataCek['UserId'] != ""){
            //Lakukan proses login dengan user yg dipilih

            $sql_login = "SELECT
                            a.*
                            , b.GroupName
                            , b.GroupId
                            , c.UnitId
                            , c.UnitName
                            , GROUP_CONCAT(DISTINCT e.DistrictId, '##', e.District) AS daerah
                            , GROUP_CONCAT(DISTINCT e.DistrictId) AS daerah_partner
                            , GROUP_CONCAT(DISTINCT e.ProvinceID) AS province
                            , GROUP_CONCAT(DISTINCT IFNULL(e.`District`, IFNULL(h.District, '-')) SEPARATOR ', ') AS district
                            , GROUP_CONCAT(DISTINCT h.DistrictId) AS daerah_access
                            , cp.DistrictID AS daerah_cpg
                            , i.FlagAccess
                            , IF(st.ObjType = 'private' || st.ObjType = 'program',st.ObjID,vss.PartnerID) AS PartnerID
                            , IFNULL(p.OfficialEmail, '-') AS official_email
                            , IFNULL(p.PrivateEmail, '-') AS private_email
                            , IFNULL(p.OfficialCellPhone, '-') AS official_phone
                            , IFNULL(p.PrivateCellPhone, '-') AS private_phone
                            , IFNULL(p.OfficialEmail, IFNULL(p.PrivateEmail, '-')) AS email
                            , IFNULL(p.OfficialCellPhone, IFNULL(p.PrivateCellPhone, '-')) AS phone
                            , b.GroupName AS group_name
                            , i.PartnerName AS partner_name
                            , r.RoleName AS role
                            , p.Photo AS Photo_staff
                            , GroupFilterBy
                            , st_p.ProjID
                            , p.Gender
                            , GROUP_CONCAT(DISTINCT aff.UserIdAff SEPARATOR ',') AS UserAff
                        FROM
                            sys_user a
                            LEFT JOIN view_tc_supplychain_staff vss ON vss.UserID=a.UserId
                            LEFT JOIN sys_user_group ON UserGroupUserId = a.UserId AND UserGroupIsDefault = '1'
                            LEFT JOIN sys_group b ON UserGroupGroupId = b.GroupId
                            LEFT JOIN sys_unit c ON b.GroupUnitId = c.UnitId
                            LEFT JOIN ktv_persons p ON p.UserID = a.UserId
                        
                            LEFT JOIN sys_user_role ur ON ur.UserId = a.UserId
                            LEFT JOIN sys_role r ON r.RoleId = ur.RoleId
                            LEFT JOIN ktv_staffs st ON p.PersonID = st.PersonID
                            LEFT JOIN ktv_staffs_project st_p ON st.StaffID = st_p.StaffID AND st_p.ProjDefault = '1'
                            LEFT JOIN sys_user_affiliate aff ON a.UserId = aff.UserId AND aff.StatusCode = 'active'
                        
                            LEFT JOIN ktv_access_staff g ON a.UserId = g.UserId
                            LEFT JOIN ktv_district h ON g.DistrictID = h.DistrictID
                        
                            LEFT JOIN ktv_program_partner i ON st.`ObjID` = i.`PartnerID` AND st.`ObjType` IN ('private','program')
                            LEFT JOIN ktv_district_partner z ON i.`PartnerID` = z.PartnerID
                            LEFT JOIN ktv_district e ON z.DistrictID = e.DistrictID
                        
                            LEFT JOIN (
                                SELECT
                                    GROUP_CONCAT(DISTINCT sd.DistrictID) AS DistrictID,
                                    cp.PartnerID
                                FROM ktv_cpg_partner cp
                                JOIN ktv_cpg c ON c.CPGid = cp.CPGid
                                LEFT JOIN ktv_village v ON v.VillageID = c.VillageID
                                LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
                                GROUP BY cp.PartnerID
                            ) cp ON cp.PartnerID = i.PartnerID
                                 WHERE a.UserId = ? GROUP BY a.UserId
                                 ";
            $query = $this->db->query($sql_login,array($UserIdGanti));
            $dataUser = $query->row_array();

            if($dataUser['UserId'] != ""){

                //Ganti Session
                $_SESSION['username']           = $dataUser['UserName'];
                $_SESSION['realname']           = $dataUser['UserRealName'];
                $_SESSION['userid']             = $dataUser['UserId'];
                $_SESSION['groupid']            = $dataUser['GroupId'];
                $_SESSION['ProjID']             = $dataUser['ProjID'];
                $_SESSION['unitid']             = $dataUser['UnitId'];
                $_SESSION['daerah']             = $dataUser['daerah'];
                $_SESSION['province']           = $dataUser['province'];
                $_SESSION['PartnerID']          = $dataUser['PartnerID'];
                $_SESSION['daerah_access']      = $dataUser['daerah_access'];
                $_SESSION['language']           = $dataUser['UserLanguage'];
                $_SESSION['official_email']     = $dataUser['official_email'];
                $_SESSION['private_email']      = $dataUser['private_email'];
                $_SESSION['email']              = $dataUser['email'];
                $_SESSION['official_phone']     = $dataUser['official_phone'];
                $_SESSION['private_phone']      = $dataUser['private_phone'];
                $_SESSION['phone']              = $dataUser['phone'];
                $_SESSION['group']              = $dataUser['group_name'];
                $_SESSION['partner']            = $dataUser['partner_name'];
                $_SESSION['district']           = $dataUser['district'];
                $_SESSION['Photo_staff']        = $dataUser['Photo_staff'];
                $_SESSION['role']               = $dataUser['role'];
                $_SESSION['filter_by']          = $dataUser['GroupFilterBy'];
                $_SESSION['is_admin']           = $dataUser['UserIsAdmin'];
                $_SESSION['FlagAccess']         = $dataUser['FlagAccess'];
                $_SESSION['Gender']         = $dataUser['Gender'];

                //SupplychainID
                $getSesSupp = $this->db->select('SupplychainID')->from('view_tc_supplychain_staff')->where('UserID', $_SESSION['userid'] )->get()->row(); 
                $SupplychainID ='';
                if($getSesSupp) {
                    $_SESSION['SupplychainID'] = $getSesSupp->SupplychainID;
                } else {
                    $_SESSION['SupplychainID'] = null;
                }

                redirect('');
            }else{
                //User tak ketemu
                UserLogout();
            }
        }else{
            //Tidak Berhak, Logout
            UserLogout();
        }
    }

    public function change_project($ProjID){
        $this->db->trans_begin();

        //get staff id
        $sql="SELECT
                b.StaffID
            FROM
                ktv_persons a
                INNER JOIN ktv_staffs b ON a.PersonID = b.PersonID
            WHERE
                a.UserId = ?
            LIMIT 1";
        $query = $this->db->query($sql,array($_SESSION['userid']));
        $data = $query->row_array();
        $StaffID = $data['StaffID'];

        $sql="UPDATE ktv_staffs_project SET
                ProjDefault = '0'
            WHERE
                StaffID = ?";
        $p = array(
            $StaffID
        );
        $query = $this->db->query($sql,$p);

        $sql="UPDATE ktv_staffs_project SET
                ProjDefault = '1'
            WHERE
                StaffID = ?
                AND ProjID = ?
            LIMIT 1";
        $p = array(
            $StaffID,
            (int) $ProjID
        );
        $query = $this->db->query($sql,$p);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();

            //ganti session
            $_SESSION['ProjID'] = $ProjID;
        }
        redirect('');
    }

    public function change_filter($filter_id)
    {
        if (!$this->getCoopID()) {
            $_SESSION['filter_id'] = $filter_id;
            if($this->_getUserGroupForCoop()){
                $_SESSION['coopID'] = $filter_id;
                echo 'coopid_change';
            } else {
                echo 'sce_filter_id_change';
            }
        } else {
            $_SESSION['coopID'] = $filter_id;
            echo 'coopid_change';
        }

        // echo '<pre>'; print_r($_SESSION['filter_id']); echo '</pre>'; exit;
    }

    public function filter_autocom_sce()
    {
        $textNya = $this->input->post('textNya');

        $sql="SELECT
                SceID AS id,
                CONCAT(f.`FarmerID`,' - ',f.FarmerName,' (',cpg.`GroupName`,', ',dis.`District`,')') AS label,
                CONCAT(f.FarmerID,' - ',f.FarmerName) AS farmer,
                cpg.GroupName AS cpg,
                CONCAT(v.Village,', ',dis.`District`) AS desa
            FROM
                sce_farmer s
                JOIN ktv_farmer f ON f.FarmerID = s.FarmerID
                LEFT JOIN ktv_cpg cpg ON f.`CPGid` = cpg.`CPGid`
                LEFT JOIN ktv_district dis ON dis.`DistrictID` = SUBSTR(cpg.`VillageID`,1,4)
                LEFT JOIN ktv_village v ON f.VillageID = v.VillageID
            WHERE
                s.StatusCode = 'active' AND
                (f.FarmerName LIKE ? OR f.`FarmerID` = ?)
            ORDER BY label";
        $query = $this->db->query($sql, array('%'.$textNya.'%',$textNya));
        $data = $query->result_array();

        //cek apakah ada data
        if($data[0]['id'] != ""){
            $jsonReturn = array(
                "totalNya" => count($data),
                "dataNya" => $data
            );
        }else{
            $jsonReturn = array(
                "totalNya" => 0
            );
        }

        echo json_encode($jsonReturn); exit;
    }

    public function getCoopID()
    {

        $this->db->select('coopID');
        $this->db->from('ktv_cooperative_staff');
        $this->db->where('userId', $_SESSION['userid']);
        $Q = $this->db->get();
        if ($Q->num_rows() > 0) {
            $row = $Q->row();
            return $row->coopID;
        }

        return false;
    }

    private function _getUserGroupForCoop() {
        $this->db->select('GroupFilterBy');
        $this->db->from('sys_group');
        $this->db->where('sys_group.GroupId',$_SESSION['groupid']);
        $Q = $this->db->get();
        if($Q->num_rows() > 0) {
            $row = $Q->row();
            if($row->GroupFilterBy == 4) {
                return true;
            }
        }

        return false;
    }
}
