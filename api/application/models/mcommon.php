<?php

/**
 * Authentication Model for API
 *
 * @author hostune
 */
class Mcommon extends CI_Model {

    var $coopID = false;

    function __construct() {
        parent::__construct();
        $this->coopID = getCoopID();
    }
    
    public function CekKoordinat($Latitude, $Longitude){
        $Latitude = (float) $Latitude;
        $Longitude = (float) $Longitude;
        if (($Latitude >= -90 && $Latitude <= 90) && ($Longitude >= -180 && $Longitude <= 180)) {
            //Cek valid tidak koordinatnya
            $sql = "SELECT ST_IsValid(ST_GeomFromText('POINT({$Latitude} {$Longitude})', 4326)) AS HasilCek";
            $DataCekKoordinat = $this->db->query($sql)->row_array();
            if ($DataCekKoordinat['HasilCek'] == "1") {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function GetComboAutoStaff($queryString,$start,$limit) {
        $result = array();

        $SqlHakAkses = "";
        if($_SESSION['PartnerID'] != 1) { #Koltiva
            $SqlHakAkses = " AND part.PartnerID = {$_SESSION['PartnerID']} ";
        }

        $sql = "SELECT
                a.`PersonID` AS id
                , CONCAT(a.`PersonNm`, ' (',part.PartnerName,')') AS label
                , a.`PersonNm` AS `name`
                , part.`PartnerName` AS partner
            FROM
                ktv_persons a
                INNER JOIN ktv_staffs b ON a.`PersonID` = b.`PersonID`
                LEFT JOIN ktv_program_partner part ON b.`ObjID` = part.`PartnerID`
            WHERE 1=1
                AND a.`StatusCd` = 'active'
                AND b.`ObjType` IN ('program','private')
                AND ( (a.PersonNm LIKE ?) OR (part.PartnerName LIKE ?) )
                $SqlHakAkses
            ORDER BY a.`PersonNm`";           

        $p = array(
            '%'.$queryString.'%','%'.$queryString.'%',$start,$limit
        );
        $result['data'] = $this->db->query($sql,$p)->result_array();

        $query = $this->db->query("SELECT FOUND_ROWS() AS total");
        $result['total'] = $query->row(0)->total;

        return $result;
    }

    public function GetComboAutoFarmer($queryString,$start,$limit) {
        $result = array();

        //========== Hak akses (Begin) =====================
        if ($_SESSION['is_admin'] == "1") {
            $sqlHakAkses['join'] = "";
            $sqlHakAkses['where'] = "";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program") {

            $sqlHakAkses['where'] = " AND SUBSTR(a.VillageID,1,4) IN (" . $_SESSION['daerah_access'] . ")";
            $sqlHakAkses['join'] = " INNER JOIN ktv_access_partner_member acc_pm ON a.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$_SESSION['PartnerID']}' ";

        } else {
            //cek ktv_access_staff
            $sqlHakAkses['where'] = " AND SUBSTR(a.VillageID,1,4) IN (" . $_SESSION['daerah_access'] . ")";
            $sqlHakAkses['join'] = "";
        }
        //========== Hak akses (End) =======================

        $sql = "SELECT SQL_CALC_FOUND_ROWS
                    a.`MemberID` AS id
                    , a.`MemberDisplayID` AS displayid
                    , a.`MemberName` AS name
                    , CONCAT(a.`MemberDisplayID`,' - ',a.MemberName) AS label
                FROM
                    `ktv_members` a
                    LEFT JOIN ktv_district ds ON SUBSTR(a.`VillageID`,1,4) = ds.DistrictID
                    #View Access
                    $sqlHakAkses[join]
                WHERE 1=1
                    AND a.`StatusCode` = 'active'
                    -- AND a.EntID = 1 #Farmer Type
                    AND ( (a.MemberDisplayID LIKE ?) OR (a.MemberName LIKE ?) )
                    $sqlHakAkses[where]
                GROUP BY a.MemberID
                ORDER BY a.MemberName
                LIMIT ?,?";

        $p = array(
            '%'.$queryString.'%','%'.$queryString.'%',$start,$limit
        );
        $result['data'] = $this->db->query($sql,$p)->result_array();

        // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;

        $query = $this->db->query("SELECT FOUND_ROWS() AS total");
        $result['total'] = $query->row(0)->total;

        return $result;
    }

    public function combo_cluster($ProgID = null, $wave = null){
        $ProgID = "";
        $where = "";
        if($ProgID != '' AND $ProgID != 'all_wave'){
            $where .= " AND ProgID = '$ProgID'";
        }

        if($wave != '' AND $wave != 'all_wave'){
            $where .= " AND ProgID = '$wave'";
        }

        $sql="SELECT
            a.DistrictID id
            , b.District label
        FROM
            `ktv_ks_kpi_project_area_history` a
        LEFT JOIN
            ktv_district b on b.DistrictID = a.DistrictID
        WHERE
            1=1
            $where
        GROUP BY a.DistrictID
        ORDER BY b.District ASC
        ";
        $query = $this->db->query($sql, array());

        $return['data'] = $query->result_array();
        $return['success'] = true;
        return $return;
    }

    public function combo_transactionstatus(){
        $sql="SELECT * FROM ref_transaction_status";
        $query = $this->db->query($sql, array());

        $return['data'] = $query->result_array();
        $return['success'] = true;
        return $return;
    }

    public function getComboStaffCertification(){
        $sql="SELECT
                a.`StaffID` AS id
                , b.`PersonNm` AS label
            FROM
                ktv_staffs a
                INNER JOIN ktv_persons b ON a.`PersonID` = b.`PersonID`
            WHERE
                a.`ObjType` = 'Program'
                AND a.`StatusCode` = 'active'
                AND b.`StatusCd` = 'active'
            ORDER BY b.`PersonNm`";
        $data = $this->db->query($sql)->result_array();

        $result['data'] = $data;
        return $result;
    }

    public function getCombo($table,$value,$display,$query = false,$val = false) {

        $this->db->select(array($value,$display));
        $this->db->from($table);
        if($this->coopID) {
            $this->db->where('CoopID', $this->coopID);
        }
        if($val){
            $tab = explode('=', $val);
            $this->db->where($tab[0],$tab[1]);
        }

        if(mysql_real_escape_string($query) != ''){
            $key = $query;
            $query = explode(' ', $query);
            $query = implode('|', $query);
            $this->db->where($display . ' REGEXP "' . $query . '"');
        }

        $Q = $this->db->get();
        if($Q->num_rows()){
            return $Q->result_array();
        }

        return array();
    }

    public function getComboPartnerCommon(){
        $sql = "SELECT
                    a.`PartnerID` AS id
                    , a.`PartnerName` AS label
                FROM
                    ktv_program_partner a
                WHERE
                    a.`StatusCode` = 'active'
                ORDER BY a.`PartnerName` ASC";
        $data = $this->db->query($sql)->result_array();

        $result['data'] = $data;
        return $result;
    }

    public function getColumns($table,$value,$display) {
        $this->db->select(array($value,$display));
        $this->db->from($table);

        $Q = $this->db->get();

        if($Q->num_rows()){

            $output = array('columns' => array(), 'fields' => array());

            foreach ($Q->result_array() as $key => $value) {

                $output['fields'][$key] = $value['USER_TYPE_ID'];

                $output['columns'][] = array(
                    'text'  => $value['USER_TYPE_NAME'],
                    'id'    => $value['USER_TYPE_ID'],
                    'xtype' => 'checkcolumn',
                    'align' =>  'center',
                    'dataIndex' => $value['USER_TYPE_ID']
                );

            }

            return $output;
        }

        return array();
    }

    public function getUserGroup($data) {
            $this->db->select('GROUP_ID');
            $this->db->from('s_user_group');
            $this->db->where('USER_ID',$data);
            $Q = $this->db->get();
            if($Q->num_rows()){
                    $row = $Q->row();
                    return $row->GROUP_ID;
            }
            return false;
    }

    public function readMembers($key, $status, $start, $limit) {
        $sql = "
            SELECT
                a.`memberID` AS id,
                a.`farmerID`,
                a.`primaryNo`,
                a.`registeredDate`,
                a.`typeID`,
                a.`name`,
                a.`identityType`,
                a.`identityNumber`,
                a.`gender`,
                a.`placeOfBirth`,
                a.`dateOfBirth`,
                a.`address`,
                a.`villageID`,
                b.`Village`,
                c.`subDistrict`,
                d.`district`,
                a.`phone`,
                a.`maritalStatus`,
                a.`education`,
                a.`status`,
                a.`remark`,
                CONCAT('/images/coop/members/', a.`photo`) AS memberPhoto,
                CONCAT('/images/coop/members/', a.`signature`) AS memberSignature,
                a.`familyName`,
                a.`familyAddress`,
                a.`familyRelation`,
                a.`familyIdentityType`,
                a.`familyIdentityNumber`,
				cpg.GroupName,
                a.`familyPhone`
            FROM
                `coop_member` a
            LEFT JOIN ktv_village b ON a.`villageID` = b.`VillageID`
            LEFT JOIN ktv_subdistrict c ON c.`SubDistrictID` = b.`SubDistrictID`
            LEFT JOIN coop_member_type e ON e.`typeID` = a.`typeID`
            LEFT JOIN ktv_farmer farmer ON farmer.farmerID = a.farmerID
            LEFT JOIN ktv_cpg cpg ON cpg.CPGid = farmer.CPGid
            LEFT JOIN ktv_district d ON d.DistrictID = c.DistrictID";
            $sql .= " WHERE e.coopID = ".getCoopID()." ";
            if(strlen($key) > 0){
                $sql .= " AND a.name LIKE ? ";
            }

            // if(strlen($status) > 0){
            //     $sql .= " AND a.status = ? ";
            // }
            // if(isset($status))
            // {
            //     $sql.=" and a.status = $status ";
            // }

            $sql .= "ORDER BY a.primaryNo %s";

        $query = $this->db->query(sprintf($sql, 'LIMIT '.$start.','.$limit), array("%$key%", (int) $start, (int) $limit));
        $result['data'] = $query->result_array();

        // get total
        $row = $this->db->query(sprintf($sql, ''), array("%$key%", "%$status%"));
        $total_row = $row->result_array();
        $result['total'] = count($total_row);

        return $result;
    }

    public function getCoopSyncData($id) {

      $data = array(
        0 => array(
          'name' => 'Jenis Anggota',
          'count' => $this->getSyncDataByTable('coop_member_type',$id)['total'],
          'status' => 'Waiting',
          'data' => json_encode($this->getSyncDataByTable('coop_member_type',$id))
        ),
        1 => array(
          'name' => 'Jenis Simpanan',
          'count' => $this->getSyncDataByTable('coop_saving_type',$id)['total'],
          'status' => 'Waiting',
          'data' => json_encode($this->getSyncDataByTable('coop_saving_type',$id))
        ),
        2 => array(
          'name' => 'Anggota',
          'count' => $this->getSyncDataByTable('coop_member',$id)['total'],
          'status' => 'Waiting',
          'data' => json_encode($this->getSyncDataByTable('coop_member',$id))
        ),
        3 => array(
          'name' => 'Akun Simpanan',
          'count' => $this->getSyncDataByTable('coop_member_saving',$id)['total'],
          'status' => 'Waiting',
          'data' => json_encode($this->getSyncDataByTable('coop_member_saving',$id))
        )
      );
      return array('data' => $data, 'total' => count($data));
    }

    public function getSyncDataByTable($table,$coop) {

      $this->db->where('CoopID',$coop);
      $this->db->where('SyncedDate IS NULL',NULL,false);
      $Q = $this->db->get($table);
      if($Q->num_rows() > 0){
        $result = $Q->result_array();
        return array('total' => count($result), 'data' => $result);
      }
      return array('total' => 0, 'data' => array());
    }

    public function getCommonCombo($table,$value,$display,$query = false) {

        $this->db->select($value . ' AS id, '. $display . ' AS label',false);
        $this->db->from($table);

        if(($query) != ''){
            $this->db->where($query,null,false);
        }

        $Q = $this->db->get();
        if($Q->num_rows()){
            return $Q->result_array();
        }

        return array();
    }

    public function getMasterSubDistrict() {

        $this->db->select('SubDistrictID,SubDistrict');
        $this->db->from('ktv_subdistrict');

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }

        return false;
    }

    public function getMasterVillage() {

        $this->db->select('VillageID,Village');
        $this->db->from('ktv_village');

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }

        return false;
    }

    public function revoke($username, $password) {
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
        WHERE LOWER(a.UserName)=LOWER(?) AND a.UserPassword=?";
        $user = $this->db->query($sql_login, array($username, md5($password)))->result_array();

        if ($user[0]['UserName'] == $username
            and $user[0]['UserPassword'] == md5($password)
            and $user[0]['UserActive'] == 'Yes') {
                
                $_SESSION['username']           = $user[0]['UserName'];
                $_SESSION['realname']           = $user[0]['UserRealName'];
                $_SESSION['userid']             = $user[0]['UserId'];
                $_SESSION['groupid']            = $user[0]['GroupId'];
                $_SESSION['ProjID']             = $user[0]['ProjID'];
                $_SESSION['unitid']             = $user[0]['UnitId'];
                $_SESSION['daerah']             = $user[0]['daerah'];
                $_SESSION['province']           = $user[0]['province'];
                $_SESSION['PartnerID']          = $user[0]['PartnerID'];
                $_SESSION['daerah_access']      = $user[0]['daerah_access'];
                $_SESSION['language']           = $user[0]['UserLanguage'];
                $_SESSION['official_email']     = $user[0]['official_email'];
                $_SESSION['private_email']      = $user[0]['private_email'];
                $_SESSION['email']              = $user[0]['email'];
                $_SESSION['official_phone']     = $user[0]['official_phone'];
                $_SESSION['private_phone']      = $user[0]['private_phone'];
                $_SESSION['phone']              = $user[0]['phone'];
                $_SESSION['group']              = $user[0]['group_name'];
                $_SESSION['partner']            = $user[0]['partner_name'];
                $_SESSION['district']           = $user[0]['district'];
                $_SESSION['Photo_staff']        = $user[0]['Photo_staff'];
                $_SESSION['role']               = $user[0]['role'];
                $_SESSION['filter_by']          = $user[0]['GroupFilterBy'];
                $_SESSION['is_admin']           = $user[0]['UserIsAdmin'];
                $_SESSION['FlagAccess']         = $user[0]['FlagAccess'];
                $_SESSION['Gender']         = $user[0]['Gender'];

                //SupplychainID
                $getSesSupp = $this->db->select('SupplychainID')->from('view_tc_supplychain_staff')->where('UserID', $_SESSION['userid'] )->get()->row(); 
                $SupplychainID ='';
                if($getSesSupp) {
                    $_SESSION['SupplychainID'] = $getSesSupp->SupplychainID;
                } else {
                    $_SESSION['SupplychainID'] = null;
                }

            return true;
        }

        return false;
    }

    public function getProvinceIDWithFarmerID($FarmerID){
        $sql="SELECT
                kd.ProvinceID AS ProvinceID
            FROM
                ktv_members a
                LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
            WHERE
                a.`MemberDisplayID` = ?
            LIMIT 1";
        $query = $this->db->query($sql,array($FarmerID));
        $data = $query->row_array();
        return $data['ProvinceID'];
    }

    public function updateFotoFarmer($namaFilePhotoUpdate,$MemberID){
        $sql="UPDATE ktv_members a SET
                a.Photo = ?
            WHERE
                a.MemberID = ?
            LIMIT 1";
        $p = array(
            $namaFilePhotoUpdate,$MemberID
        );
        $result = $this->db->query($sql,$p);
        return $result;
    }

    public function updateFotoFarmerConsentNote($namaFilePhotoUpdate,$MemberID){
        $sql="UPDATE ktv_members a SET
                a.LearningContractStatus = '1',
                a.LearningContractSign = ?
            WHERE
                a.MemberID = ?
            LIMIT 1";
        $p = array(
            $namaFilePhotoUpdate,$MemberID
        );
        return $this->db->query($sql,$p);
    }

    public function cekSurveyMainBuyerByUID($eventUID){
        $sql="SELECT
                MemberID
            FROM
                ktv_survey_main_buyer a
            WHERE
                a.`uid` = ?
            LIMIT 1";
        $query = $this->db->query($sql,array($eventUID));
        $data = $query->row_array();
        if($data['MemberID'] != ""){
            return true;
        }else{
            return false;
        }
    }

    public function cekSurveyGardenByUID($eventUID){
        $sql="SELECT
                MemberID
            FROM
                ktv_survey_plot a
            WHERE
                a.`uid` = ?
            LIMIT 1";
        $query = $this->db->query($sql,array($eventUID));
        $data = $query->row_array();
        if($data['MemberID'] != ""){
            return true;
        }else{
            return false;
        }
    }

    public function updateFotoReceiptMainBuyer($namaFilePhotoUpdate,$eventUID){
        $sql="UPDATE ktv_survey_main_buyer a SET
                a.`ReceiptPhotoLastSoldFFB` = ?
            WHERE
                a.`uid` = ?
            LIMIT 1";
        return $this->db->query($sql, array($namaFilePhotoUpdate,$eventUID));
    }

    public function updateFotoGardenVisit($namaFilePhotoUpdate,$eventUID){
        $sql="UPDATE ktv_survey_plot a SET
                a.`PhotoOfVisit` = ?
            WHERE
                a.`uid` = ?
            LIMIT 1";
        return $this->db->query($sql, array($namaFilePhotoUpdate,$eventUID));
    }

    public function getComboPartner(){
        $sql="SELECT
                a.`PartnerID` AS id
                , a.`PartnerName` AS label
            FROM
                ktv_program_partner a
            WHERE
                a.`StatusCode` = 'active'
            ORDER BY a.`PartnerName` ASC";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function getComboProvinceAccess() {
        //cek district akses
        if($_SESSION['is_admin'] != "1"){
            $sqlDistrikAkses = " AND b.DistrictID IN ({$_SESSION['daerah_access']}) ";
        }else{
            $sqlDistrikAkses = "";
        }

        $sql = "SELECT
                a.`ProvinceID` AS id
                , a.`Province` AS label
            FROM
                ktv_province a
                INNER JOIN ktv_district b ON b.ProvinceID = a.ProvinceID
            WHERE
                a.`active` = '1'
                $sqlDistrikAkses
            GROUP BY a.ProvinceID
            ORDER BY a.`Province` ASC";
        $query = $this->db->query($sql);
        
        return $query->result_array();
    }

    public function getComboDistrictAccess($ProvinceID){
        if($ProvinceID == 0) return array();

        //cek district akses
        if($_SESSION['is_admin'] != "1"){
            $sqlDistrikAkses = " AND a.DistrictID IN ({$_SESSION['daerah_access']}) ";
        }else{
            $sqlDistrikAkses = "";
        }


        $sql = "SELECT
            a.`DistrictID` AS id
            , a.`District` AS label
        FROM
            ktv_district a
        WHERE
            a.`active` = '1'
            AND a.ProvinceID = ?
            $sqlDistrikAkses
        ORDER BY a.`District` ASC";
        $p = array(
            (int) $ProvinceID
        );
        $query = $this->db->query($sql, $p);
        return $query->result_array();
    }

    public function getComboProvince(){
        $sql="SELECT
                a.`ProvinceID` AS id
                , a.`Province` AS label
            FROM
                ktv_province a
            WHERE
                a.`active` = '1'
                AND a.`StatusCode` = 'active'
            ORDER BY a.`ProvinceID` ASC";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function getComboDistrict($ProvinceID){
        $sql="SELECT
                a.`DistrictID` AS id
                , a.`District` AS label
            FROM
                ktv_district a
            WHERE
                a.`active` = '1'
                AND a.`StatusCode` = 'active'
                AND a.`ProvinceID` = '{$ProvinceID}'
            ORDER BY a.`District` ASC";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function getComboFarmerGroup($DistrictID,$ProvinceID){
        if($DistrictID != 0){
            $SqlWhereDaerah = " AND a.`DistrictID` = {$DistrictID} ";
        }

        if($ProvinceID != 0){
            $SqlWhereDaerah = " AND a.`ProvinceID` = {$ProvinceID} ";
        }

        if ($_SESSION['is_admin'] == "1") {
            $sqlHakAkses = "";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program") {
            $sqlHakAkses = " AND a.`DistrictID` IN (" . $_SESSION['daerah_access'] . ")";
        } else {
            //cek ktv_access_staff
            $sqlHakAkses = " AND a.`DistrictID` IN (" . $_SESSION['daerah_access'] . ")";
        }

        $sql="SELECT
                a.`FarmerGroupID` AS id
                , CONCAT('[',a.`FarmerGroupID`,'] ',a.`GroupName`) AS label
            FROM
                ktv_farmer_group a
            WHERE
                a.`StatusCode` = 'active'
                $sqlHakAkses
                $SqlWhereDaerah
            ORDER BY a.`FarmerGroupID` ASC";
        $query = $this->db->query($sql, array());

        $return['data'] = $query->result_array();
        $return['success'] = true;
        return $return;
    }

    public function getComboFarmerGroupMember($FarmerGroupID){

        //generate filter hak akses (begin)
        if ($_SESSION['is_admin'] == "1") {
            $sqlHakAkses['join'] = "";
            $sqlHakAkses['where'] = "";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program") {
            //cek ktv_access_staff
            $sqlHakAkses['where'] = " AND kd.DistrictID IN (" . $_SESSION['daerah_access'] . ")";

            //cek ktv_access_partner_member
            $sqlHakAkses['join'] = " INNER JOIN ktv_access_partner_member acc_pm ON a.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$_SESSION['PartnerID']}' ";
        } else {
            //cek ktv_access_staff
            $sqlHakAkses['where'] = " AND kd.DistrictID IN (" . $_SESSION['daerah_access'] . ")";
            $sqlHakAkses['join'] = "";
        }
        //generate filter hak akses (end)

        $sql="SELECT
                a.`MemberID` AS id
                , CONCAT(a.`MemberDisplayID`,' - ',a.`MemberName`) AS label
            FROM
                ktv_members a
                LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
                LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
                INNER JOIN ktv_member_role b ON a.`MemberID` = b.`MemberID` AND b.`MRoleID` = '1'
                {$sqlHakAkses['join']}
            WHERE
                a.`StatusCode` = 'active'
                AND a.`FarmerGroupID` = ?
                {$sqlHakAkses['where']}
            ORDER BY a.`MemberDisplayID` ASC";
        $query = $this->db->query($sql, array($FarmerGroupID));

        $return['data'] = $query->result_array();
        $return['success'] = true;
        return $return;
    }

    public function getComboCooperativesMember($CoopID){

        //generate filter hak akses (begin)
        if ($_SESSION['is_admin'] == "1") {
            $sqlHakAkses['join'] = "";
            $sqlHakAkses['where'] = "";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program") {
            //cek ktv_access_staff
            $sqlHakAkses['where'] = " AND kd.DistrictID IN (" . $_SESSION['daerah_access'] . ")";

            //cek ktv_access_partner_member
            $sqlHakAkses['join'] = " INNER JOIN ktv_access_partner_member acc_pm ON a.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$_SESSION['PartnerID']}' ";
        } else {
            //cek ktv_access_staff
            $sqlHakAkses['where'] = " AND kd.DistrictID IN (" . $_SESSION['daerah_access'] . ")";
            $sqlHakAkses['join'] = "";
        }
        //generate filter hak akses (end)

        $sql="SELECT
                a.`MemberID` AS id
                , CONCAT(a.`MemberDisplayID`,' - ',a.`MemberName`) AS label
            FROM
                ktv_members a
                LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
                LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
                INNER JOIN ktv_member_role b ON a.`MemberID` = b.`MemberID` AND b.`MRoleID` = '1'
                {$sqlHakAkses['join']}
            WHERE
                a.`StatusCode` = 'active'
                AND a.`CoopID` = ?
                {$sqlHakAkses['where']}
            ORDER BY a.`MemberDisplayID` ASC";
        $query = $this->db->query($sql, array($CoopID));

        $return['data'] = $query->result_array();
        $return['success'] = true;
        return $return;
    }

    public function GetComboWageCurr(){
        $sql = "SELECT
                    a.`CurrID` AS id
                    , a.`CurrCode` AS label
                FROM
                    ktv_ref_currency a
                WHERE
                    a.`StatusCode` = 'active'";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
	
	
	
	public function getComboSubDistrict($DistrictID){
        $sql = "SELECT
                a.`SubDistrictID` AS id
                , a.`SubDistrict` AS label
            FROM
                ktv_subdistrict a
            WHERE
                a.`active` = '1'
                AND a.`DistrictID` = ?
            ORDER BY a.`SubDistrict` ASC";
        $p = array(
            (int) $DistrictID
        );
        $query = $this->db->query($sql, $p);
        return $query->result_array();
    }
	
	public function getComboVillage($SubDistrictID,$loadAll){
        $sql = "SELECT
                a.`VillageID` AS id
                , a.`Village` AS label
            FROM
                ktv_village a
            WHERE
                a.`StatusCode` = 'active'
                AND a.`SubDistrictID` = ?
            ORDER BY a.`VillageID` ASC";
        $p = array(
            (int) $SubDistrictID
        );
        $query = $this->db->query($sql, $p);
        $data = $query->result_array();

        if($loadAll == "Yes"){
            array_unshift($data, array(
                "id" => "all",
                "label"=> lang("All")
            ));
        }

        return $data;
    }
	
	public function getComboBuyingUnit($PartnerID){
        $sql = "SELECT
                  a.SupplychainID AS id
                , a.Name AS label
            FROM
                view_tc_supplychain_org a
            WHERE 
               a.PartnerID = ?
            ORDER BY a.PartnerID ASC";
        $p = array(
            (int) $PartnerID
        );
        $query = $this->db->query($sql, $p);
        return $query->result_array();
    }

    public function getComboHolder($CertProgID){
        $sql="SELECT
                ch.CertHolderID AS id,
                ch.CertHolderOrgName AS label 
            FROM
                ktv_certification_holders ch
            WHERE
                CertProgID = ? 
                AND ch.StatusCode = 'active' 
            ORDER BY
                ch.CertHolderID ASC";
         $query = $this->db->query($sql, array('CertProgID' => $CertProgID ));

        $return['data'] = $query->result_array();
        $return['success'] = true;
        return $return;
    }

    public function getComboSMEDealer($ProvinceID){
        $sql = "SELECT
            b.MemberID id,
            b.MemberUID uid,
            CONCAT(b.MemberDisplayID,' - ', IFNULL( c.agCompanyName, b.MemberName )) label 
        FROM
            ktv_access_partner_member a
        LEFT JOIN 
            ktv_members b ON b.MemberID = a.apmMemberID
        LEFT JOIN 
            ktv_members_extension c ON c.MemberID = b.MemberID
        LEFT JOIN 
            ktv_member_role e ON e.MemberID = b.MemberID
        LEFT JOIN
            ktv_ref_member_role rm on rm.MRoleID = e.MRoleID                
        LEFT JOIN 
            ktv_village vil ON b.VillageID = vil.VillageID
        LEFT JOIN 
            ktv_subdistrict subd ON subd.SubDistrictID = vil.SubDistrictID
        LEFT JOIN 
            ktv_district dis ON dis.DistrictID = subd.DistrictID
        LEFT JOIN 
            ktv_province prov ON prov.ProvinceID = dis.ProvinceID
        WHERE
        -- 	a.apmPartnerID = ? 
            rm.MRoleType = 'Agent'
        AND 
            b.StatusCode = 'active'
        AND
            prov.ProvinceID = '$ProvinceID'
        GROUP BY
            b.MemberID";
        
        $query = $this->db->query($sql);

        $return['data'] = $query->result_array();
        $return['success'] = true;
        return $return;
    }

    public function getCombocertPrograms(){
        $sql="SELECT CertProgID as id, CertProgName as label from ktv_ref_certification_program where StatusCode ='active' ORDER BY CertProgID ASC";
        $query = $this->db->query($sql);

        $return['data'] = $query->result_array();
        $return['success'] = true;
        return $return;
    }

    public function getComboImsEvent($CertHolderID){
        $sql="SELECT IMSID  AS id, CertEventName AS label, IMSMasterID from ktv_ims where CertHolderID = ? and StatusCode = 'active' ORDER BY IMSID ASC";
        $query = $this->db->query($sql, array($CertHolderID));
        return $query->result_array();
    }

    public function getCombo_farmer_type($IMSID){
        $sql="select  C.FarmertypeID as id , C.FarmerType as label, C.PartnerID from ktv_first_buyer A, ktv_ims B,  ktv_ref_farmer_type C
			  where A.FirstBuyerID = B.FirstBuyerID and A.FirstBuyerPartnerID = C.PartnerID and B.IMSID = ?  group by id";
        $query = $this->db->query($sql, array($IMSID));
		//echo $this->db->last_query();die;
        return $query->result_array();
    }

    public function geteventtrainingtype(){
        $sql="SELECT CpgTrainingsID as id, CpgTrainings as label from ktv_cpg_trainings where  StatusCode = 'active' ORDER BY CpgTrainingsID ASC";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function cmbBatchGeneral(){
        $sql="SELECT
                a.`CpgBatchID` AS id
                , CONCAT(a.BatchNumber,' - ',b.`PartnerName`,' (',IFNULL(a.BatchName,'-'),'/',IFNULL(a.BatchYear,'-'),')') AS label
            FROM
                ktv_cpg_batch a
                INNER JOIN ktv_program_partner b ON a.`PartnerID` = b.`PartnerID`
            WHERE
                a.`StatusCode` = 'active'
            ORDER BY a.`BatchNumber` asc";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function cmbStaffGeneral(){
        $sql="SELECT B.StaffID AS id,CONCAT(A.PersonNm,' (',B.ObjType,')') AS label FROM ktv_persons A, ktv_staffs B
        WHERE A.PersonID = B.PersonID AND B.StatusCode = 'active' ORDER BY StaffID ASC";
        //change request luqman,  edited by komar
        /*  Query old
        $sql="select B.StaffID as id, A.PersonNm as label from ktv_persons A, ktv_staffs B
              where A.PersonID = B.PersonID and B.StatusCode = 'active' ORDER BY StaffID ASC";
        */
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function getComboFarmerGroupByDistrict($DistrictID){
        $sql="SELECT
                a.`FarmerGroupID` AS id
                , CONCAT(a.`FarmerGroupID`,' | ',a.`GroupName`) AS label
            FROM
                ktv_farmer_group a
            #WHERE
            #    SUBSTR(a.`FarmerGroupID`,1,4) = ?
            ORDER BY a.`FarmerGroupID`";
        $query = $this->db->query($sql, array($DistrictID));

        $return['data'] = $query->result_array();
        $return['success'] = true;
        return $return;
    }

    public function GetComboFilterCountry() {
        // modified 25-2-2021 restrict district dilepas untuk admin
        if ($_SESSION['is_admin'] == "1"){
            $sqlAccess = "";
        } else {
            if ($_SESSION['daerah_access'] != "") {
                $sqlAccess = "AND c.`DistrictID` IN ({$_SESSION['daerah_access']})";
            } else {
                $sqlAccess = "AND c.`DistrictID` IN ('')";
            }
        }

        // if($_SESSION['daerah_access'] != "") $sqlAccess = "AND c.`DistrictID` IN ({$_SESSION['daerah_access']})"; else $sqlAccess = "AND c.`DistrictID` IN ('')";

        $sql = "SELECT
                    a.`ISO2` AS id
                    , a.`CountryName` AS label
                FROM
                    ktv_country a
                    INNER JOIN ktv_province b ON a.`ISO2` = b.`CountryCode`
                    INNER JOIN ktv_district c ON b.`ProvinceID` = c.`ProvinceID`
                WHERE 1=1
                    $sqlAccess
                GROUP BY a.`CountryID`
                ORDER BY label";
        $data = $this->db->query($sql)->result_array();

        return $data;
    }

    public function GetComboFilterProvince($CountryID) {
        // modified 25-2-2021 restrict district dilepas untuk admin
        if ($_SESSION['is_admin'] == "1"){
            $sqlAccess = "";
        } else {
            if ($_SESSION['daerah_access'] != "") {
                $sqlAccess = "AND c.`DistrictID` IN ({$_SESSION['daerah_access']})";
            } else {
                $sqlAccess = "AND c.`DistrictID` IN ('')";
            }
        }

        // if($_SESSION['daerah_access'] != "") $sqlAccess = "AND c.`DistrictID` IN ({$_SESSION['daerah_access']})"; else $sqlAccess = "AND c.`DistrictID` IN ('')";

        $sql = "SELECT
                    b.`ProvinceID` AS id
                    , b.`Province` AS label
                FROM
                    ktv_country a
                    INNER JOIN ktv_province b ON a.`ISO2` = b.`CountryCode`
                    INNER JOIN ktv_district c ON b.`ProvinceID` = c.`ProvinceID`
                WHERE 1=1
                    AND a.`ISO2` = ?
                    $sqlAccess
                GROUP BY b.`ProvinceID`
                ORDER BY label";
        $data = $this->db->query($sql,array($CountryID))->result_array();

        return $data;
    }

    public function GetCmbMenuCategory(){
        /*$sql = "( SELECT 'general' AS id, 'general' AS label ) UNION
        ( SELECT REPLACE ( MenuName, ' ', '' ) AS id, MenuName AS label FROM `sys_menu` WHERE MenuParentId = 0 )";*/
        $sql = "SELECT 'general' AS id, 'general' AS label";
        $data = $this->db->query($sql)->result_array();

        $return['data'] = $data;
        $return['success'] = true;
        return $return;
    }

    public function GetCmbPartner($additional = NULL) {
        //Cek apakah partner koltiva
        $SqlPartner = "";
        if($_SESSION['PartnerID'] != 1) {
            $SqlPartner = " AND a.PartnerID = {$_SESSION['PartnerID']} ";
        }

        if ($additional != NULL) {
            $SqlPartner = " AND a.PartnerID = {$additional} ";
        }

        $sql = "SELECT
                    a.`PartnerID` AS id
                    , a.`PartnerName` AS label
                FROM
                    ktv_program_partner a
                WHERE
                    a.`StatusCode` = 'active'
                    $SqlPartner
                ORDER BY a.`PartnerName`";
        $data = $this->db->query($sql)->result_array();

        $return['data'] = $data;
        $return['success'] = true;
        return $return;
    }

    public function getComboFilterDistrict($ProvinceID){
        // modified 25-2-2021 restrict district dilepas untuk admin

        if ($_SESSION['is_admin'] == "1"){
            $sqlAccess = "";
        } else {
            if ($_SESSION['daerah_access'] != "") {
                $sqlAccess = "AND a.`DistrictID` IN ({$_SESSION['daerah_access']})";
            } else {
                $sqlAccess = "AND a.`DistrictID` IN ('')";
            }
        }

        // if($_SESSION['daerah_access'] != "") $sqlAccess = "AND a.`DistrictID` IN ({$_SESSION['daerah_access']})"; else $sqlAccess = "AND a.`DistrictID` IN ('')";

        $sql="SELECT
                a.`DistrictID` AS id
                , a.`District` AS label
            FROM
                ktv_district a
            WHERE
                a.`active` = '1'
                AND a.`StatusCode` = 'active'
                AND a.`ProvinceID` = '{$ProvinceID}'
                $sqlAccess
            ORDER BY a.`District` ASC";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
}
?>