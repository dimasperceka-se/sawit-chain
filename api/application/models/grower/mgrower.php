<?php

/**
 * @Author: nikolius
 * @Date:   2017-05-16 15:53:34
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Mgrower extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Normalize a numeric form value: strip thousands separators (commas) and
     * return NULL for blanks. Empty strings would otherwise be rejected by
     * integer columns under MySQL strict mode (error 1366).
     */
    private function _numOrNull($value) {
        $value = str_replace(",", "", (string) $value);
        return ($value === '') ? null : $value;
    }

    /**
     * Grant member access to a partner AND all of its ancestors (PartnerParentID
     * chain), so an umbrella/parent partner keeps seeing members created by its
     * child partners. Idempotent against the unique key (apmPartnerID, apmMemberID).
     */
    private function _grantPartnerChainAccess($memberId, $partnerId) {
        $seen = array();
        while (!empty($partnerId) && !isset($seen[$partnerId])) {
            $seen[$partnerId] = true;
            $this->db->query(
                "INSERT INTO `ktv_access_partner_member` SET
                    `apmPartnerID` = ?,
                    `apmMemberID` = ?,
                    `DateCreated` = NOW(),
                    `CreatedBy` = ?
                 ON DUPLICATE KEY UPDATE `apmID` = `apmID`",
                array($partnerId, $memberId, $_SESSION['userid'])
            );
            $row = $this->db->query(
                "SELECT PartnerParentID FROM ktv_program_partner WHERE PartnerID = ? LIMIT 1",
                array($partnerId)
            )->row_array();
            $partnerId = ($row && !empty($row['PartnerParentID'])) ? $row['PartnerParentID'] : null;
        }
    }

    function updateWillingnessCommit($MemberID,$path){
        $sql = "
            UPDATE
                ktv_members
            SET
                WillingnesCommitSignature = '$path'
            WHERE MemberID = '$MemberID'
        ";

        $query = $this->db->query($sql);
    }

    function updateWillingness($MemberID,$path){
        $sql = "
            UPDATE
                ktv_members
            SET
                WillingnesSignature = '$path'
            WHERE MemberID = '$MemberID'
        ";

        $query = $this->db->query($sql);
    }

    function updateFotoProfile($MemberID,$path){
        $sql = "
            UPDATE
                ktv_members
            SET
                Photo = '$path'
            WHERE MemberID = '$MemberID'
        ";

        $query = $this->db->query($sql);
    }

    function updateFotoKTP($MemberID,$path){
        $sql = "
            UPDATE
                ktv_members
            SET
                KTPFile = '$path'
            WHERE MemberID = '$MemberID'
        ";

        $query = $this->db->query($sql);
    }

    function getComboEnum($pSearch){
        $sqlFilter = $this->generateSqlFilter($pSearch);
        $sqlHakAkses = $this->generateSqlHakAkses();

        //fixed tampilkan petani
        $sqlFilterRole .= " AND sub_b.MRoleID = 1 ";

        $sql = "
            SELECT
                a.`CreatedBy` as id
                , (SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy`) AS label
            FROM
                ktv_members a
                INNER JOIN (
                    SELECT
                        sub_a.MemberID
                    FROM
                        ktv_members sub_a
                        LEFT JOIN ktv_member_role sub_b ON sub_a.MemberID = sub_b.MemberID
                    WHERE
                        sub_a.StatusCode = 'active'
                        $sqlFilterRole
                    GROUP BY sub_a.MemberID
                ) AS tmp_member_filter ON a.MemberID = tmp_member_filter.MemberID
                LEFT JOIN ktv_member_role mrole ON a.MemberID = mrole.MemberID
                LEFT JOIN ktv_program_partner_survey partsur ON a.PartnerID = partsur.PartnerID
                LEFT JOIN ktv_ref_member_role rrole ON mrole.MRoleID = rrole.MRoleID
                LEFT JOIN ktv_village c ON a.`VillageID` = c.`VillageID`
                LEFT JOIN ktv_subdistrict d ON SUBSTR(a.`VillageID`,1,7) = d.`SubDistrictID`
                LEFT JOIN ktv_province e ON SUBSTR(a.`VillageID`,1,2) = e.ProvinceID
                LEFT JOIN ktv_district f ON SUBSTR(a.`VillageID`,1,4) = f.DistrictID
                {$sqlHakAkses['join']}
            WHERE
                a.`StatusCode` = 'active'
                $sqlFilter
                {$sqlHakAkses['where']}
            GROUP BY a.MemberID
        ";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function getComboPropinsi($FarmerGroupID = null) {
        if($FarmerGroupID != null OR $FarmerGroupID != ''){
            //get ProvinceID (begin)
            $sql="SELECT
                    a.`ProvinceID`
                FROM
                    ktv_farmer_group a
                WHERE
                    a.`FarmerGroupID` = ?
                LIMIT 1";
            $query = $this->db->query($sql, array($FarmerGroupID));
            $data = $query->row_array();
            $ProvinceID = $data['ProvinceID'];

            $sqlDistrikAkses = " AND a.ProvinceID = '$ProvinceID'";
        }else{            
            //cek district akses
            if($_SESSION['is_admin'] != "1"){
                $sqlDistrikAkses = " AND b.DistrictID IN ({$_SESSION['daerah_access']}) ";
            }else{
                $sqlDistrikAkses = "";
            }
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

    public function getComboDistrict($ProvinceID) {
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

    public function getComboSubdistrict($DistrictID) {
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

    public function getComboVillage($SubdistrictID) {
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
            (int) $SubdistrictID
        );
        $query = $this->db->query($sql, $p);
        return $query->result_array();
    }

    public function getComboRoleMember() {
        $sql = "SELECT
                a.`MRoleID` AS id
                , a.`MRoleName` AS label
            FROM
                ktv_ref_member_role a
            WHERE
                a.`StatusCode` = 'active'
            ORDER BY a.`MRoleID` ASC";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function getComboBank() {
        $sql = "SELECT
                a.`BankID` AS id
                , a.`BankName` AS label
            FROM
                ktv_bank a
            WHERE
                a.`StatusCode` = 'active'
            ORDER BY a.`BankName` ASC";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    private function generateSqlFilter($pSearch) {
        $pSearch = array_merge([
            'prov' => '', 'kab' => '', 'kec' => '', 'pAdvInternalProgram' => '',
            'textSearch' => '', 'textSearchDesa' => '', 'categorySearch' => '', 'roleSearch' => '',
            'AdvRowEnumerator' => '', 'AdvTextEnumerator' => '',
            'AdvRowHandphone' => '', 'AdvTextHandphone' => '',
            'AdvRowAge' => '', 'AdvOpAge' => '', 'AdvTextAge' => '',
            'AdvRowMaritalStatus' => '', 'AdvMaritalStatus' => '',
            'AdvRowDateCollection' => '', 'AdvDateCollectionBegin' => '', 'AdvDateCollectionEnd' => '',
            'AdvRowDateCreated' => '', 'AdvDateCreatedBegin' => '', 'AdvDateCreatedEnd' => '',
            'AdvRowDateSynced' => '', 'AdvDateSyncedBegin' => '', 'AdvDateSyncedEnd' => '',
            'AdvRowLastUpdatedDate' => '', 'AdvLastUpdatedDateBegin' => '', 'AdvLastUpdatedDateEnd' => '',
            'AdvEnumerator' => '', 'SupplychainID' => '',
        ], $pSearch);
        $sqlFilter = "";

        //BENTUK QUERY FILTER =============================================== (BEGIN)
        if ($pSearch['prov'] != "") {
            $sqlFilter .= " AND e.ProvinceID = " . $pSearch['prov'];
        }

        if ($pSearch['kab'] != "") {
            $sqlFilter .= " AND f.DistrictID = " . $pSearch['kab'];
        }

        if ($pSearch['kec'] != "") {
            $sqlFilter .= " AND d.SubDistrictID = " . $pSearch['kec'];
        }

        if ($pSearch['pAdvInternalProgram'] != "") {
            $sqlFilter .= " AND membu.BusinessUnitID = " . $pSearch['pAdvInternalProgram'];
        }

        // if($pSearch['SupplychainID'] != ""){
        //     $sqlFilter .= " AND tsf.SupplychainID = " . $pSearch['SupplychainID'];
        // }

        if ($pSearch['textSearch'] != "") {
            $sqlFilter .= " AND (a.MemberName like '%{$pSearch['textSearch']}%' OR a.MemberDisplayID like '%{$pSearch['textSearch']}%' ) ";
            $_SESSION['grid_filter']['Text'] = $pSearch['textSearch'];
        } else {
            unset($_SESSION['grid_filter']['Text']);
        }

        if ($pSearch['textSearchDesa'] != "") {
            $sqlFilter .= " AND c.Village like '%{$pSearch['textSearchDesa']}%'";
            $_SESSION['grid_filter']['Desa'] = $pSearch['textSearchDesa'];
        } else {
            unset($_SESSION['grid_filter']['Desa']);
        }

        if ($pSearch['categorySearch'] != "") {
            $_SESSION['grid_filter']['FarmerCategory'] = $pSearch['categorySearch'];
            if ($pSearch['categorySearch'] != "Registered")
                $sqlFilter .= " AND a.FarmerCategory = '".$pSearch['categorySearch']."'";
        }

        //advanced filter
        if ($pSearch['AdvRowEnumerator'] == "true") {
            $sqlFilter .= " AND a.CreatedBy = '{$pSearch['AdvTextEnumerator']}' ";
        }
        if ($pSearch['AdvRowHandphone'] == "true") {
            $sqlFilter .= " AND a.HandPhone LIKE '%{$pSearch['AdvTextHandphone']}%' ";
        }

        if ($pSearch['AdvRowAge'] == "true") {
            if ($pSearch['AdvOpAge'] != "" && $pSearch['AdvTextAge'] != "") {
                $sqlFilter .= " AND (a.`DateOfBirth` IS NOT NULL AND a.`DateOfBirth` != '0000-00-00')
                                AND TIMESTAMPDIFF(YEAR, a.DateOfBirth, CURDATE()) " . $pSearch['AdvOpAge'] . " " . $pSearch['AdvTextAge'];
            }
        }

        if ($pSearch['AdvRowMaritalStatus'] == "true") {
            $sqlFilter .= " AND a.MaritalStatus = '{$pSearch['AdvMaritalStatus']}'";
        }

        if ($pSearch['AdvRowDateCollection'] == "true"){
            $sqlFilter .= " AND ( DATE(a.DateCollection) BETWEEN '{$pSearch['AdvDateCollectionBegin']}' AND '{$pSearch['AdvDateCollectionEnd']}' ) ";
        }

        if ($pSearch['AdvRowDateCreated'] == "true"){
            $sqlFilter .= " AND ( DATE(a.DateCreated) BETWEEN '{$pSearch['AdvDateCreatedBegin']}' AND '{$pSearch['AdvDateCreatedEnd']}' ) ";
        }

        if ($pSearch['AdvRowDateSynced'] == "true"){
            $sqlFilter .= " AND ( DATE(a.DateSync) BETWEEN '{$pSearch['AdvDateSyncedBegin']}' AND '{$pSearch['AdvDateSyncedEnd']}' ) ";
        }

        if ($pSearch['AdvRowLastUpdatedDate'] == "true"){
            $sqlFilter .= " AND ( DATE(a.DateUpdated) BETWEEN '{$pSearch['AdvLastUpdatedDateBegin']}' AND '{$pSearch['AdvLastUpdatedDateEnd']}' ) ";
        }

        return $sqlFilter;
        //BENTUK QUERY FILTER =============================================== (END)
    }

    /**
     * 15-01-2-2020 dipisah karena filter ambil dari session (bentrok sama farmer)
     * @param  [type] $pSearch [description]
     * @return [type]          [description]
     */
    private function generateSqlFilterMill($pSearch) {
        $pSearch = array_merge([
            'prov' => '', 'kab' => '', 'kec' => '',
            'textSearch' => '', 'textSearchDesa' => '', 'categorySearch' => '', 'roleSearch' => '',
            'AdvRowEnumerator' => '', 'AdvTextEnumerator' => '',
            'AdvRowHandphone' => '', 'AdvTextHandphone' => '',
            'AdvRowAge' => '', 'AdvOpAge' => '', 'AdvTextAge' => '',
            'AdvRowMaritalStatus' => '', 'AdvMaritalStatus' => '',
            'AdvRowDateCollection' => '', 'AdvDateCollectionBegin' => '', 'AdvDateCollectionEnd' => '',
            'AdvEnumerator' => '', 'pPartnerSearch' => '', 'pPartnerFirstLoad' => '',
        ], $pSearch);
        $sqlFilter = "";

        //BENTUK QUERY FILTER =============================================== (BEGIN)
        if ($pSearch['prov'] != "") {
            $sqlFilter .= " AND e.ProvinceID = " . $pSearch['prov'];
        }

        if ($pSearch['kab'] != "") {
            $sqlFilter .= " AND f.DistrictID = " . $pSearch['kab'];
        }

        if ($pSearch['kec'] != "") {
            $sqlFilter .= " AND d.SubDistrictID = " . $pSearch['kec'];
        }

        if ($pSearch['textSearch'] != "") {
            $sqlFilter .= " AND (a.MemberName like '%{$pSearch['textSearch']}%' OR a.MemberDisplayID like '%{$pSearch['textSearch']}%' ) ";
            $_SESSION['grid_filter']['TextMill'] = $pSearch['textSearch'];
        } else {
            unset($_SESSION['grid_filter']['TextMill']);
        }

        if ($pSearch['textSearchDesa'] != "") {
            $sqlFilter .= " AND c.Village like '%{$pSearch['textSearchDesa']}%'";
            $_SESSION['grid_filter']['DesaMill'] = $pSearch['textSearchDesa'];
        } else {
            unset($_SESSION['grid_filter']['DesaMill']);
        }

        if ($pSearch['categorySearch'] != "") {
            $_SESSION['grid_filter']['FarmerCategoryMill'] = $pSearch['categorySearch'];
            if ($pSearch['categorySearch'] != "Registered")
                $sqlFilter .= " AND a.FarmerCategory = '".$pSearch['categorySearch']."'";
        }

        //advanced filter
        if ($pSearch['AdvRowEnumerator'] == "true") {
            $sqlFilter .= " AND a.CreatedBy = '{$pSearch['AdvTextEnumerator']}' ";
        }
        if ($pSearch['AdvRowHandphone'] == "true") {
            $sqlFilter .= " AND a.HandPhone LIKE '%{$pSearch['AdvTextHandphone']}%' ";
        }

        if ($pSearch['AdvRowAge'] == "true") {
            if ($pSearch['AdvOpAge'] != "" && $pSearch['AdvTextAge'] != "") {
                $sqlFilter .= " AND (a.`DateOfBirth` IS NOT NULL AND a.`DateOfBirth` != '0000-00-00')
                                AND TIMESTAMPDIFF(YEAR, a.DateOfBirth, CURDATE()) " . $pSearch['AdvOpAge'] . " " . $pSearch['AdvTextAge'];
            }
        }

        if ($pSearch['AdvRowMaritalStatus'] == "true") {
            $sqlFilter .= " AND a.MaritalStatus = '{$pSearch['AdvMaritalStatus']}'";
        }

        if ($pSearch['AdvRowDateCollection'] == "true"){
            $sqlFilter .= " AND ( DATE(a.DateCollection) BETWEEN '{$pSearch['AdvDateCollectionBegin']}' AND '{$pSearch['AdvDateCollectionEnd']}' ) ";
        }

        return $sqlFilter;
        //BENTUK QUERY FILTER =============================================== (END)
    }

    public function generateSqlHakAkses() {
        $sqlHakAkses = array();

        $is_admin      = isset($_SESSION['is_admin'])      ? $_SESSION['is_admin']      : '0';
        $role          = isset($_SESSION['role'])          ? $_SESSION['role']          : '';
        $PartnerID     = isset($_SESSION['PartnerID'])     ? $_SESSION['PartnerID']     : '';
        $daerah_access = isset($_SESSION['daerah_access']) ? $_SESSION['daerah_access'] : '0';

        if ($is_admin == "1") {
            $sqlHakAkses['join'] = "";
            $sqlHakAkses['where'] = "";
        }else if($role != 'SME'){
            //cek ktv_access_staff
            $sqlHakAkses['where'] = " AND f.DistrictID IN (" . $daerah_access . ")";
            //cek ktv_access_partner_member
            $sqlHakAkses['join'] = " 
                INNER JOIN ktv_access_partner_member acc_pm ON a.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID IN ({$PartnerID})
                INNER JOIN ktv_program_partner pp on pp.PartnerID = acc_pm.apmPartnerID
            ";
            
            $sql = "SELECT
                    a.`consentLetterPermission`
                FROM
                    ktv_program_partner a
                WHERE
                    a.`PartnerID` IN (?)
                LIMIT 1";
            $query = $this->db->query($sql, $PartnerID);
            $dataConsent = $query->row_array();
            if ($dataConsent['consentLetterPermission'] == "Yes") {
                $sqlHakAkses['where'] .= " AND a.LearningContractStatus = '1' ";
            }
        } else {
            //cek ktv_access_staff
            $sqlHakAkses['where'] = " AND SUBSTR(a.VillageID,1,4) IN (" . $daerah_access . ")";
            $sqlHakAkses['join'] = "";
        }

        return $sqlHakAkses;
    }

    public function getGridMainGrowerOld($pSearch, $start, $limit, $sortingField, $sortingDir, $opsiCall = 'for_grid') {
        $sqlFilter = "";
        $infoFilter = "";
        $sqlFilter = $this->generateSqlFilter($pSearch);

        $sqlHakAkses = $this->generateSqlHakAkses();

        //fixed tampilkan petani
        $sqlFilterRole = '';
        $sqlFilterRole .= " AND sub_b.MRoleID = 1 ";

        if ($sortingField == "")
            $sortingField = 'Name';
        if ($sortingDir == "")
            $sortingDir = 'ASC';

        if ($opsiCall == 'for_grid') {
            $start = (int) $start;
            $limit = (int) $limit;
            $sqlLimit = " LIMIT {$start},{$limit}";
        } else {
            $sqlLimit = "";
        }

        $sql = "
            SELECT
                SQL_CALC_FOUND_ROWS
                splot.NrOfPlantation
                , splot.TotalHectare
                , splot.TotalHectarePolygon
                , tbl_member.MemberIDInc
                , tbl_member.id
                , tbl_member.Name
                , IFNULL((
                    SELECT
                        GROUP_CONCAT(DISTINCT s_mi.MillName)
                    FROM
                        ktv_access_partner_member s_ma
                        INNER JOIN ktv_mill s_mi ON s_ma.apmPartnerID = s_mi.PartnerID
                        INNER JOIN ktv_program_partner s_par ON s_mi.PartnerID = s_par.PartnerID
                    WHERE
                        s_ma.apmMemberID = tbl_member.MemberIDInc
                        AND s_par.PartnerIndustry = 3
                    GROUP BY s_ma.apmMemberID
                ),'-') AS MillName
                , tbl_member.Desa
                , tbl_member.Kecamatan
                , tbl_member.LastUpdated
                , tbl_member.Province
                , tbl_member.District
                , tbl_member.Birthdate
                , tbl_member.Age
                , tbl_member.DateCollection
                , tbl_member.DateCreated
                , tbl_member.Handphone
                , tbl_member.MaritalStatus
                , tbl_member.MemberRole
                , IFNULL(ksp.Garden,'-') Garden
                , tbl_member.Enumerator
                , tbl_member.PartnerSurvey
            FROM
            (
            SELECT
                a.MemberID AS MemberIDInc
                , a.`MemberDisplayID` AS id
                , a.`MemberName` AS Name
                , c.`Village` AS Desa
                , d.`SubDistrict` AS Kecamatan
                , a.DateUpdated AS LastUpdated
                , e.Province
                , f.District
                , a.`DateOfBirth` AS Birthdate
                , FLOOR(DATEDIFF(CURDATE(), a.DateOfBirth) / 365.25) AS Age
                , DATE_FORMAT(a.`DateCollection`,'%Y-%m-%d') AS DateCollection
                , DATE_FORMAT(a.`DateCreated`,'%Y-%m-%d') AS DateCreated
                , a.HandPhone AS Handphone
                , CASE
                    WHEN a.MaritalStatus = '1' THEN '" . lang('Married') . "'
                    WHEN a.MaritalStatus = '2' THEN '" . lang('Single') . "'
                    WHEN a.MaritalStatus = '3' THEN '" . lang('Janda/Duda') . "'
                END AS MaritalStatus
                , GROUP_CONCAT(rrole.MRoleName SEPARATOR ', ') AS MemberRole
                , (SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy`) AS Enumerator
                , GROUP_CONCAT(partsur.SurveyName SEPARATOR ',') AS PartnerSurvey
            FROM
                ktv_members a
                INNER JOIN ktv_member_role mrole ON a.MemberID = mrole.MemberID AND mrole.MRoleID = 1 #Petani
                {$sqlHakAkses['join']}
                
                LEFT JOIN ktv_program_partner_survey partsur ON a.PartnerID = partsur.PartnerID
                LEFT JOIN ktv_ref_member_role rrole ON mrole.MRoleID = rrole.MRoleID
                
                LEFT JOIN ktv_village c ON a.VillageID = c.VillageID
                LEFT JOIN ktv_subdistrict d ON c.SubDistrictID = d.SubDistrictID
                LEFT JOIN ktv_district f ON d.DistrictID = f.DistrictID
                LEFT JOIN ktv_province e ON f.ProvinceID = e.ProvinceID
            WHERE
                a.`StatusCode` = 'active'
                $sqlFilter
                {$sqlHakAkses['where']}
            GROUP BY a.MemberID
            ) AS tbl_member
            LEFT JOIN (
                SELECT
                    sub_a.`MemberID`
                    , COUNT(sub_a.PlotNr) AS NrOfPlantation
                    , SUM(sub_a.GardenAreaHa) AS TotalHectare
                    , SUM(sub_a.GardenAreaPolygon) AS TotalHectarePolygon
                FROM
                    ktv_survey_plot sub_a
                    INNER JOIN (SELECT
                        p.MemberID, p.PlotNr, MAX(p.SurveyNr) AS SurveyNr, p.DateCollection
                    FROM ktv_survey_plot p
                    WHERE p.`StatusCode` = 'active'
                    GROUP BY p.MemberID, p.PlotNr) sub_b
                        ON sub_a.MemberID = sub_b.MemberID AND sub_a.PlotNr = sub_b.PlotNr AND sub_a.SurveyNr = sub_b.SurveyNr
                WHERE
                    sub_a.`StatusCode` = 'active'
                GROUP BY sub_a.`MemberID`
            ) AS splot ON tbl_member.MemberIDInc = splot.MemberID
            LEFT JOIN 
            (
                SELECT IFNULL(GROUP_CONCAT(CONCAT('PlotNr:',ksp.PlotNr,'(',ksp.Latitude,',', ksp.Longitude,')')),'') AS Garden, ksp.MemberID
                FROM ktv_survey_plot ksp
                GROUP BY ksp.MemberID
            ) ksp ON ksp.MemberID=tbl_member.MemberIDInc
            GROUP BY tbl_member.MemberIDInc
            ORDER BY $sortingField $sortingDir
            $sqlLimit
            ";
        $query = $this->db->query($sql);
        $result['data'] = $query->result_array();
        $result['query'] = $this->db->last_query();

        if ($opsiCall != "for_grid") {
            //langsung return (contohnya buat export)
            return $query->result_array();
        }

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        //generate information grid result (begin)
        if ($sortingDir == 'ASC') {
            $sortingInfo = 'ascending';
        }
        if ($sortingDir == 'DESC') {
            $sortingInfo = 'descending';
        }

        foreach ($pSearch as $key => $value) {
            if ($value != "") {
                switch ($key) {
                    case 'prov':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('Province') . '</li>';
                        break;
                    case 'kab':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('District') . '</li>';
                        break;
                    case 'kec':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('Kecamatan') . '</li>';
                        break;
                    case 'textSearch':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('ID / Name') . '</li>';
                        break;
                }
            }

            if ($value == "true") {
                switch ($key) {
                    case 'AdvRowEnumerator':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('Enumerator') . '</li>';
                        break;
                    case 'AdvRowHandphone':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('HandPhone') . '</li>';
                        break;
                    case 'AdvRowAge':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('Age') . '</li>';
                        break;
                    case 'AdvRowMaritalStatus':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('Marital Status') . '</li>';
                        break;
                    case 'AdvRowEnumerator':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('Enumerator') . '</li>';
                        break;
                }
            }
        }

        $_SESSION['informationGrid'] = '<div class="gridInformationContainer">
                                <h4>Information</h4>
                                <ul>
                                    <li>' . $query->row()->total . ' '.lang('datas, Sorted by').' ' . lang($sortingField) . ' ' . $sortingInfo . '</li>
                                    ' . $infoFilter . '
                                </ul>
                            </div>';
        //generate information grid result (end)

        return $result;
    }

    public function getGridMainGrower($pSearch, $start, $limit, $sortingField, $sortingDir, $opsiCall = 'for_grid') {
        $sqlFilter = "";
        $infoFilter = "";
        $sqlBu = "";
        $sqlFilter = $this->generateSqlFilter($pSearch);

        $sqlHakAkses = $this->generateSqlHakAkses();

        $millName = " IFNULL(a.MillName, '-')";
        if($_SESSION["role"] == "Private" AND $_SESSION["PartnerID"] != '1'){
            $millName = " IFNULL(pp.PartnerName, '-')";
        }

        //fixed tampilkan petani
        $sqlFilterRole = '';
        $sqlFilterRole .= " AND sub_b.MRoleID = 1 ";

        if ($sortingField == "")
            $sortingField = 'MemberName';
        if ($sortingDir == "")
            $sortingDir = 'ASC';

        if ($opsiCall == 'for_grid') {
            $start = (int) $start;
            $limit = (int) $limit;
            $sqlLimit = " LIMIT {$start},{$limit}";
        } else {
            $sqlLimit = "";
        }

        $sql = "SELECT
                SQL_CALC_FOUND_ROWS
                a.MemberID
                , a.`MemberDisplayID`
                , a.`MemberName`
                , c.`Village`
                , d.`SubDistrict`
                , a.DateUpdated
                , e.Province
                , f.District
                , a.`DateOfBirth`
                , a.`DateCollection`
                , a.`DateCreated`
                , CONCAT(a.HandphoneCode, a.HandPhone) HandPhone
                , a.MaritalStatus
                , GROUP_CONCAT(rrole.MRoleName SEPARATOR ', ') AS MemberRole
                , s.UserRealName
                , GROUP_CONCAT(partsur.SurveyName SEPARATOR ',') AS PartnerSurvey
                , a.NrOfPlantation
                , a.TotalHectare
                , a.TotalHectarePolygon
                , $millName MillName
                , a.Garden
                , a.isCertified
                , a.SupplybaseType
                , tsf.DateStart
                , tsf.DateEnd
            FROM
                ktv_members a
                INNER JOIN ktv_member_role mrole ON a.MemberID = mrole.MemberID 
                {$sqlHakAkses['join']}
                LEFT JOIN ktv_program_partner_survey partsur ON a.PartnerID = partsur.PartnerID
                LEFT JOIN ktv_ref_member_role rrole ON mrole.MRoleID = rrole.MRoleID
                INNER JOIN ktv_village c ON a.VillageID = c.VillageID
                INNER JOIN ktv_subdistrict d ON c.SubDistrictID = d.SubDistrictID
                INNER JOIN ktv_district f ON d.DistrictID = f.DistrictID
                INNER JOIN ktv_province e ON f.ProvinceID = e.ProvinceID
                LEFT JOIN ktv_tc_supplychain_farmer tsf on tsf.FarmerID = a.MemberID
                LEFT JOIN sys_user s on s.UserId = a.CreatedBy
            WHERE
                a.`StatusCode` = 'active'
                $sqlFilter
                AND 
                mrole.MRoleID IN ('1','5','6','7','8','9','10','11','12','13','14','15')
                {$sqlHakAkses['where']}
            GROUP BY a.MemberID
            ORDER BY $sortingField $sortingDir
            $sqlLimit
            ";
        $query = $this->db->query($sql);

        // echo "<pre>";print_r($this->db->last_query());die;

        if($query->num_rows()>0){
            $data = $query->result_array();
            foreach($query->result_array() as $row => $value){
                $data[$row]["MemberIDInc"]  = $value["MemberID"];
                $data[$row]["id"]           = $value["MemberDisplayID"];
                $data[$row]["Name"]         = $value["MemberName"];
                $data[$row]["Desa"]         = $value["Village"];
                $data[$row]["Kecamatan"]    = $value["SubDistrict"];
                $data[$row]["LastUpdated"]  = $value["DateUpdated"];
                $data[$row]["Birthdate"]    = $value["DateOfBirth"];
                $data[$row]["Enumerator"]   = $value["UserRealName"];
                $data[$row]["Handphone"]    = $value["HandPhone"];
                $data[$row]["Expired"]      = ($value["DateEnd"] < date("Y-m-d")) ? lang('Expired') : lang("Active");
                $data[$row]["isCertified"]  = ($value["isCertified"] == 1) ? lang('Yes') : lang("No");
                switch ($data[$row]["SupplybaseType"]){
                    case "farmer";
                        $data[$row]["SupplybaseType"] = lang("Ordinary Farmer");
                        break;
                    case "direct";
                        $data[$row]["SupplybaseType"] = lang("Direct Smallholder");
                        break;
                    case "plasma";
                        $data[$row]["SupplybaseType"] = lang("Plasma Farmer");
                        break;
                    default:
                        "-";
                }
                $birthday  = new DateTime($value["DateOfBirth"]);
                
                $sekarang = new DateTime();
                $usia = $sekarang->diff($birthday);
                $data[$row]["Age"] = $usia->y;
            }

            $result['data'] = $data;
            $result['query'] = $this->db->last_query();
        
            if ($opsiCall != "for_grid") {
                //langsung return (contohnya buat export)
                return $data;
            }

            $query = $this->db->query('SELECT FOUND_ROWS() AS total');
            $result['total'] = $query->row()->total;
        }else{
            $result['data'] = array();
            $result['query'] = $this->db->last_query();
            $result['total'] = 0;
        }

        //generate information grid result (begin)
        if ($sortingDir == 'ASC') {
            $sortingInfo = 'ascending';
        }
        if ($sortingDir == 'DESC') {
            $sortingInfo = 'descending';
        }

        foreach ($pSearch as $key => $value) {
            if ($value != "") {
                switch ($key) {
                    case 'prov':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('Province') . '</li>';
                        break;
                    case 'kab':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('District') . '</li>';
                        break;
                    case 'kec':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('Kecamatan') . '</li>';
                        break;
                    case 'textSearch':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('ID / Name') . '</li>';
                        break;
                }
            }

            if ($value == "true") {
                switch ($key) {
                    case 'AdvRowEnumerator':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('Enumerator') . '</li>';
                        break;
                    case 'AdvRowHandphone':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('HandPhone') . '</li>';
                        break;
                    case 'AdvRowAge':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('Age') . '</li>';
                        break;
                    case 'AdvRowMaritalStatus':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('Marital Status') . '</li>';
                        break;
                    case 'AdvRowEnumerator':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('Enumerator') . '</li>';
                        break;
                }
            }
        }

        $_SESSION['informationGrid'] = '<div class="gridInformationContainer">
                                <h4>Information</h4>
                                <ul>
                                    <li>' . $query->row()->total . ' '.lang('datas, Sorted by').' ' . lang($sortingField) . ' ' . $sortingInfo . '</li>
                                    ' . $infoFilter . '
                                </ul>
                            </div>';
        //generate information grid result (end)

        return $result;
    }

    public function getGridMillMainGrowerOld($pSearch, $start, $limit, $sortingField, $sortingDir, $opsiCall = 'for_grid') {

        $sqlFilter = "";
        $infoFilter = "";
        $sqlFilter = $this->generateSqlFilter($pSearch);

        $sqlHakAkses = $this->generateSqlHakAkses();

        //fixed tampilkan petani
        $sqlFilterRole = '';
        $sqlFilterRole .= " AND sub_b.MRoleID = 1 ";

        if ($pSearch['pPartnerSearch'] != '') {
            $filterPartner = ' WHERE a.PartnerID IN(' . $pSearch['pPartnerSearch'] . ')';
        } else {
            $filterPartner = '';
        }

        if ($sortingField == "")
            $sortingField = 'Name';
        if ($sortingDir == "")
            $sortingDir = 'ASC';

        if ($opsiCall == 'for_grid') {
            $start = (int) $start;
            $limit = (int) $limit;
            $sqlLimit = " LIMIT {$start},{$limit}";
        } else {
            $sqlLimit = "";
        }

        $sql = "
            SELECT
                SQL_CALC_FOUND_ROWS
                COUNT(splot.PlotNr) AS NrOfPlantation
                , SUM(splot.GardenAreaHa) AS TotalHectare
                , SUM(splot.GardenAreaPolygon) AS TotalHectarePolygon
                , tbl_member.MemberIDInc
                , tbl_member.id
                , tbl_member.Name
                , tbl_member.Desa
                , tbl_member.Kecamatan
                , tbl_member.LastUpdated
                , tbl_member.Province
                , tbl_member.District
                , tbl_member.Birthdate
                , tbl_member.Age
                , tbl_member.DateCollection
                , tbl_member.DateCreated
                , tbl_member.Handphone
                , tbl_member.MaritalStatus
                , tbl_member.MemberRole
                , tbl_member.Enumerator
                , tbl_member.PartnerSurvey
            FROM
            (
            SELECT
                a.MemberID AS MemberIDInc
                , a.`MemberDisplayID` AS id
                , a.`MemberName` AS Name
                , c.`Village` AS Desa
                , d.`SubDistrict` AS Kecamatan
                , a.DateUpdated AS LastUpdated
                , e.Province
                , f.District
                , a.`DateOfBirth` AS Birthdate
                , FLOOR(DATEDIFF(CURDATE(), a.DateOfBirth) / 365.25) AS Age
                , DATE_FORMAT(a.`DateCollection`,'%Y-%m-%d') AS DateCollection
                , DATE_FORMAT(a.`DateCreated`,'%Y-%m-%d') AS DateCreated
                , a.HandPhone AS Handphone
                , CASE
                    WHEN a.MaritalStatus = '1' THEN '" . lang('Married') . "'
                    WHEN a.MaritalStatus = '2' THEN '" . lang('Single') . "'
                    WHEN a.MaritalStatus = '3' THEN '" . lang('Janda/Duda') . "'
                END AS MaritalStatus
                , GROUP_CONCAT(rrole.MRoleName SEPARATOR ', ') AS MemberRole
                /*, CONCAT(
                    (SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy` LIMIT 1),
                    IF(a.`LastModifiedBy` IS NOT NULL OR a.`LastModifiedBy` != '',(SELECT CONCAT(', modified by ',sub_a.UserRealname) FROM sys_user sub_a WHERE sub_a.UserId = a.`LastModifiedBy` LIMIT 1),'')
                ) AS Enumerator*/
                , (SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy`) AS Enumerator
                , GROUP_CONCAT(partsur.SurveyName SEPARATOR ',') AS PartnerSurvey
            FROM
                ktv_members a
                INNER JOIN (
                    SELECT
                        sub_a.MemberID
                    FROM
                        ktv_members sub_a
                        LEFT JOIN ktv_member_role sub_b ON sub_a.MemberID = sub_b.MemberID
                    WHERE
                        sub_a.StatusCode = 'active'
                        $sqlFilterRole
                    GROUP BY sub_a.MemberID
                ) AS tmp_member_filter ON a.MemberID = tmp_member_filter.MemberID
                INNER JOIN (
                    SELECT 
                        a.PartnerID,a.PartnerParentID,b.apmMemberID
                    FROM ktv_program_partner a 
                    LEFT JOIN ktv_access_partner_member b on a.PartnerID=b.apmPartnerID 
                    $filterPartner
                    GROUP BY b.apmMemberID
                ) AS tmp_partner ON a.MemberID = tmp_partner.apmMemberID
                LEFT JOIN ktv_member_role mrole ON a.MemberID = mrole.MemberID
                LEFT JOIN ktv_program_partner_survey partsur ON a.PartnerID = partsur.PartnerID
                LEFT JOIN ktv_ref_member_role rrole ON mrole.MRoleID = rrole.MRoleID
                LEFT JOIN ktv_village c ON a.`VillageID` = c.`VillageID`
                LEFT JOIN ktv_subdistrict d ON SUBSTR(a.`VillageID`,1,7) = d.`SubDistrictID`
                LEFT JOIN ktv_province e ON SUBSTR(a.`VillageID`,1,2) = e.ProvinceID
                LEFT JOIN ktv_district f ON SUBSTR(a.`VillageID`,1,4) = f.DistrictID
            WHERE
                a.`StatusCode` = 'active'
                $sqlFilter
            GROUP BY a.MemberID
            ) AS tbl_member
            LEFT JOIN (
                SELECT
                    sub_a.`MemberID`
                    , sub_a.`PlotNr`
                    , sub_a.`GardenAreaHa`
                    , sub_a.GardenAreaPolygon
                FROM
                    ktv_survey_plot sub_a
                    INNER JOIN (SELECT
                        p.MemberID, p.PlotNr, MAX(p.SurveyNr) AS SurveyNr, p.DateCollection
                    FROM ktv_survey_plot p WHERE p.`StatusCode` = 'active'
                    GROUP BY p.MemberID, p.PlotNr) sub_b
                        ON sub_a.MemberID = sub_b.MemberID AND sub_a.PlotNr = sub_b.PlotNr AND sub_a.SurveyNr = sub_b.SurveyNr
                WHERE
                    sub_a.`StatusCode` = 'active'
            ) AS splot ON tbl_member.MemberIDInc = splot.MemberID
            GROUP BY tbl_member.MemberIDInc
            ORDER BY $sortingField $sortingDir
            $sqlLimit
            ";
        $query = $this->db->query($sql);
        $result['data'] = $query->result_array();

        if ($opsiCall != "for_grid") {
            //langsung return (contohnya buat export)
            return $query->result_array();
        }
        
        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        //generate information grid result (begin)
        if ($sortingDir == 'ASC') {
            $sortingInfo = 'ascending';
        }
        if ($sortingDir == 'DESC') {
            $sortingInfo = 'descending';
        }

        foreach ($pSearch as $key => $value) {
            if ($value != "") {
                switch ($key) {
                    case 'prov':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('Province') . '</li>';
                        break;
                    case 'kab':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('District') . '</li>';
                        break;
                    case 'kec':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('Kecamatan') . '</li>';
                        break;
                    case 'textSearch':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('ID / Name') . '</li>';
                        break;
                }
            }

            if ($value == "true") {
                switch ($key) {
                    case 'AdvRowEnumerator':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('Enumerator') . '</li>';
                        break;
                    case 'AdvRowHandphone':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('HandPhone') . '</li>';
                        break;
                    case 'AdvRowAge':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('Age') . '</li>';
                        break;
                    case 'AdvRowMaritalStatus':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('Marital Status') . '</li>';
                        break;
                    case 'AdvRowEnumerator':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('Enumerator') . '</li>';
                        break;
                }
            }
        }

        $_SESSION['informationGrid'] = '<div class="gridInformationContainer">
                                <h4>Information</h4>
                                <ul>
                                    <li>' . $query->row()->total . ' ' . lang('datas, Sorted by') . ' ' . lang($sortingField) . ' ' . $sortingInfo . '</li>
                                    ' . $infoFilter . '
                                </ul>
                            </div>';
        //generate information grid result (end)
        
        return $result;
    }

    public function getGridMillMainGrower($pSearch, $start, $limit, $sortingField, $sortingDir, $opsiCall = 'for_grid') {

        $sqlFilter = "";
        $infoFilter = "";
        $sqlwhere = "";
        $sqlFilter = $this->generateSqlFilterMill($pSearch);

        $sqlHakAkses = $this->generateSqlHakAkses();

        //fixed tampilkan petani
        $sqlFilterRole = '';
        $sqlFilterRole .= " AND sub_b.MRoleID = 1 ";

        if ($pSearch['pPartnerSearch'] != '') {
            $filterPartner = ' WHERE a.PartnerID IN(' . $pSearch['pPartnerSearch'] . ')';
        } else {
            $filterPartner = '';
        }

        if($_SESSION["role"] == "Private"){
            $sqlwhere .=  " AND c.PartnerID = $_SESSION[PartnerID] ";
        }

        if($opsiCall == 'for_grid'){
            $label = '';
            $table_join = '';
        }else{
            $label = '
            , agent.AgentID
            , agent.AgentName
            , agent.AgentLatitude
            , agent.AgentLongitude
            , vendor.VendorName
            , vendor.VendorLatitude
            , vendor.VendorLongitude
            , mill.MillName';
            $table_join = "
            LEFT JOIN (
                SELECT
                    a.MemberID,
                    b.SupplychainID,
                    GROUP_CONCAT( b2.MemberDisplayID SEPARATOR ' | ') AgentID,
                    GROUP_CONCAT( b2.Alias SEPARATOR ' | ') AgentName,
                    IF(GROUP_CONCAT( c.`Name` SEPARATOR ' | ') <>'',GROUP_CONCAT(a.Latitude SEPARATOR ' | '),'') AgentLatitude,
                    IF(GROUP_CONCAT( c.`Name` SEPARATOR ' | ') <>'',GROUP_CONCAT(a.Longitude SEPARATOR ' | '),'') AgentLongitude
                FROM
                    `ktv_members` a
                    LEFT JOIN ktv_tc_supplychain_farmer b ON b.FarmerID = a.MemberID
                    LEFT JOIN view_tc_supplychain_org c ON c.SupplychainID = b.SupplychainID 
                    LEFT JOIN ktv_members b2 ON b2.MemberID = c.ObjID
                WHERE
                    a.StatusCode = 'active' 
                    AND b.StatusCode = 'active' 
                    AND c.ObjType = 'agent' 
                GROUP BY
                    a.MemberID
            ) agent on agent.MemberID = a.MemberID
            LEFT JOIN (
                SELECT
                    a.MemberID,
                    b.SupplychainID,
                    GROUP_CONCAT( c.vendor SEPARATOR ' | ') VendorName,
                    IF(GROUP_CONCAT( c.vendor SEPARATOR ' | ') <>'',GROUP_CONCAT(a.Latitude SEPARATOR ' | '),'') VendorLatitude,
                    IF(GROUP_CONCAT( c.vendor SEPARATOR ' | ') <>'',GROUP_CONCAT(a.Longitude SEPARATOR ' | '),'') VendorLongitude
                FROM
                    `ktv_members` a
                    LEFT JOIN ktv_tc_supplychain_farmer b ON b.FarmerID = a.MemberID
                    LEFT JOIN (
                        SELECT DISTINCT child.SupplychainID, child.ObjType ObjTypeVendor, child.`Name` vendor, parent.`Name` mill, parent.ObjType ObjTypeMill
                        FROM
                            ktv_tc_supplychain_org_rel rel
                            LEFT JOIN view_tc_supplychain_org parent ON parent.SupplychainID=rel.ParentID
                            LEFT JOIN view_tc_supplychain_org child ON child.SupplychainID=rel.ChildID
                        WHERE
                            rel.StatusCode='active'
                            AND parent.ObjType='mill'
                            AND child.ObjType='agent'
                    ) c on c.SupplychainID = b.SupplychainID
                WHERE
                    a.StatusCode = 'active' 
                    AND b.StatusCode = 'active'
                GROUP BY
                    a.MemberID
            ) vendor on vendor.MemberID = a.MemberID
            LEFT JOIN (
                SELECT
                    a.MemberID,
                    GROUP_CONCAT( c.PartnerName ) MillName,
                    GROUP_CONCAT( DISTINCT apmi.apmiPartnerID ) 'PartnerID' 
                FROM
                    `ktv_members` a
                    LEFT JOIN ktv_access_partner_member b ON apmMemberID = a.MemberID
                    LEFT JOIN ktv_program_partner c ON c.PartnerID = b.apmPartnerID
                    LEFT JOIN ktv_mill AS m ON m.PartnerID = c.PartnerID
                    LEFT JOIN `ktv_access_partner_mill` apmi ON apmiMillID = m.MillID 
                WHERE
                    a.StatusCode = 'active' 
                    AND c.StatusCode = 'active' 
                    AND m.StatusCode = 'active'
                    $sqlwhere
                    AND c.PartnerID NOT IN (
                        1,
                        2,
                        3,
                        4,
                        5,
                        6,
                        7,
                        8,
                        9,
                        10,
                        11,
                        12,
                        13,
                        14 
                    ) 
                GROUP BY
                    a.MemberID,
                    apmi.apmiPartnerID 
            ) mill ON mill.MemberID = a.MemberID
            ";
        }

        if ($sortingField == "")
            $sortingField = 'Name';
        if ($sortingDir == "")
            $sortingDir = 'ASC';

        if ($opsiCall == 'for_grid') {
            $start = (int) $start;
            $limit = (int) $limit;
            $sqlLimit = " LIMIT {$start},{$limit}";
        } else {
            $sqlLimit = "";
        }

        $sql = "
            SELECT
                SQL_CALC_FOUND_ROWS
                a.MemberID AS MemberIDInc
                , a.`MemberDisplayID` AS id
                , a.`MemberName` AS Name
                , c.`Village` AS Desa
                , d.`SubDistrict` AS Kecamatan
                , a.DateUpdated AS LastUpdated
                $label
                , e.Province
                , f.District
                , a.`DateOfBirth` AS Birthdate
                , FLOOR(DATEDIFF(CURDATE(), a.DateOfBirth) / 365.25) AS Age
                , DATE_FORMAT(a.`DateCollection`,'%Y-%m-%d') AS DateCollection
                , DATE_FORMAT(a.`DateCreated`,'%Y-%m-%d') AS DateCreated
                , a.HandPhone AS Handphone
                , CASE
                    WHEN a.MaritalStatus = '1' THEN '" . lang('Married') . "'
                    WHEN a.MaritalStatus = '2' THEN '" . lang('Single') . "'
                    WHEN a.MaritalStatus = '3' THEN '" . lang('Janda/Duda') . "'
                END AS MaritalStatus
                /*, GROUP_CONCAT(rrole.MRoleName SEPARATOR ', ') AS MemberRole*/
                /*, CONCAT(
                    (SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy` LIMIT 1),
                    IF(a.`LastModifiedBy` IS NOT NULL OR a.`LastModifiedBy` != '',(SELECT CONCAT(', modified by ',sub_a.UserRealname) FROM sys_user sub_a WHERE sub_a.UserId = a.`LastModifiedBy` LIMIT 1),'')
                ) AS Enumerator*/
                , (SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy`) AS Enumerator
                /*, GROUP_CONCAT(partsur.SurveyName SEPARATOR ',') AS PartnerSurvey*/
                , a.NrOfPlantation
                , a.TotalHectare
                , a.TotalHectarePolygon
                , a.Latitude
                , a.Longitude
            FROM
                ktv_members a
                INNER JOIN (
                    SELECT
                        sub_a.MemberID
                    FROM
                        ktv_members sub_a
                        LEFT JOIN ktv_member_role sub_b ON sub_a.MemberID = sub_b.MemberID
                    WHERE
                        sub_a.StatusCode = 'active'
                        $sqlFilterRole
                    GROUP BY sub_a.MemberID
                ) AS tmp_member_filter ON a.MemberID = tmp_member_filter.MemberID
                INNER JOIN (
                    SELECT 
                        a.PartnerID,a.PartnerParentID,b.apmMemberID
                    FROM ktv_program_partner a 
                    LEFT JOIN ktv_access_partner_member b on a.PartnerID=b.apmPartnerID 
                    $filterPartner
                    GROUP BY b.apmMemberID
                ) AS tmp_partner ON a.MemberID = tmp_partner.apmMemberID
                LEFT JOIN ktv_member_role mrole ON a.MemberID = mrole.MemberID
                LEFT JOIN ktv_program_partner_survey partsur ON a.PartnerID = partsur.PartnerID
                LEFT JOIN ktv_ref_member_role rrole ON mrole.MRoleID = rrole.MRoleID
                LEFT JOIN ktv_village c ON a.`VillageID` = c.`VillageID`
                LEFT JOIN ktv_subdistrict d ON SUBSTR(a.`VillageID`,1,7) = d.`SubDistrictID`
                LEFT JOIN ktv_province e ON SUBSTR(a.`VillageID`,1,2) = e.ProvinceID
                LEFT JOIN ktv_district f ON SUBSTR(a.`VillageID`,1,4) = f.DistrictID
                $table_join
            WHERE
                a.`StatusCode` = 'active'
                $sqlFilter
            GROUP BY a.MemberID
            ORDER BY $sortingField $sortingDir
            $sqlLimit
            ";
        $query = $this->db->query($sql);
        $result['data'] = $query->result_array();

        if ($opsiCall != "for_grid") {
            //langsung return (contohnya buat export)
            return $query->result_array();
        }
        
        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        //generate information grid result (begin)
        if ($sortingDir == 'ASC') {
            $sortingInfo = 'ascending';
        }
        if ($sortingDir == 'DESC') {
            $sortingInfo = 'descending';
        }

        foreach ($pSearch as $key => $value) {
            if ($value != "") {
                switch ($key) {
                    case 'prov':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('Province') . '</li>';
                        break;
                    case 'kab':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('District') . '</li>';
                        break;
                    case 'kec':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('Kecamatan') . '</li>';
                        break;
                    case 'textSearch':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('ID / Name') . '</li>';
                        break;
                }
            }

            if ($value == "true") {
                switch ($key) {
                    case 'AdvRowEnumerator':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('Enumerator') . '</li>';
                        break;
                    case 'AdvRowHandphone':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('HandPhone') . '</li>';
                        break;
                    case 'AdvRowAge':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('Age') . '</li>';
                        break;
                    case 'AdvRowMaritalStatus':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('Marital Status') . '</li>';
                        break;
                    case 'AdvRowEnumerator':
                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('Enumerator') . '</li>';
                        break;
                }
            }
        }

        $_SESSION['informationGrid'] = '<div class="gridInformationContainer">
                                <h4>Information</h4>
                                <ul>
                                    <li>' . $query->row()->total . ' ' . lang('datas, Sorted by') . ' ' . lang($sortingField) . ' ' . $sortingInfo . '</li>
                                    ' . $infoFilter . '
                                </ul>
                            </div>';
        //generate information grid result (end)
        
        return $result;
    }

    public function getMemberBasicDataForm($MemberID) {
        $this->load->library('awsfileupload');
        $sql = "SELECT
                a.`MemberID` AS \"Koltiva.view.Grower.FormMainGrower-MemberID\"
                , a.`MemberDisplayID` AS \"Koltiva.view.Grower.FormMainGrower-MemberDisplayID\"
                , a.`DateCollection` AS \"Koltiva.view.Grower.FormMainGrower-DateCollection\"
                , a.`MemberName` AS \"Koltiva.view.Grower.FormMainGrower-Fullname\"
                , a.`DateOfBirth` AS \"Koltiva.view.Grower.FormMainGrower-DateOfBirth\"
                , a.`Gender` AS \"Koltiva.view.Grower.FormMainGrower-Gender\"
                , a.`Gender`
                , a.`SurveyNr` AS \"Koltiva.view.Grower.FormMainGrower-SurveyNr\"
                , a.`MaritalStatus` AS \"Koltiva.view.Grower.FormMainGrower-MaritalStatus\"
                , a.`Education` AS \"Koltiva.view.Grower.FormMainGrower-Education\"
                , SUBSTR(a.`VillageID`,1,2) AS \"Province\"
                , SUBSTR(a.`VillageID`,1,4) AS \"District\"
                , SUBSTR(a.`VillageID`,1,7) AS \"Subdistrict\"
                , a.`VillageID` AS \"Village\"
                , a.`Address` AS \"Koltiva.view.Grower.FormMainGrower-Address\"
                , a.`RtRw` AS \"Koltiva.view.Grower.FormMainGrower-RtRw\"
                , a.`Handphone` AS \"Koltiva.view.Grower.FormMainGrower-Handphone\"
                , IFNULL(a.`HandphoneCode`, '+62') AS \"Koltiva.view.Grower.FormMainGrower-HandphoneCode\"
                , a.`HandphoneType` AS \"Koltiva.view.Grower.FormMainGrower-HandphoneType\"
                , a.`AccessToSmartphone` AS \"Koltiva.view.Grower.FormMainGrower-AccessToSmartphone\"
                #, a.`Photo` AS \"Koltiva.view.Grower.FormMainGrower-MemberPhotoOld\"
                , a.Photo AS PhotoSrc
                , a.KTPFile AS KTPSrc
                , a.LearningContractSign AS ConsentSrc
                , a.`PhotoDesc` AS \"Koltiva.view.Grower.FormMainGrower-PhotoDesc\"
                , a.`StatusMember` AS \"Koltiva.view.Grower.FormMainGrower-RbStatus\"
                , a.`InactiveReason` AS \"Koltiva.view.Grower.FormMainGrower-InactiveReason\"
                , GROUP_CONCAT(b.`MRoleID` SEPARATOR ',') AS MemRole
                , a.Nin AS \"Koltiva.view.Grower.FormMainGrower-Nin\"
                , a.inGroup AS \"Koltiva.view.Grower.FormMainGrower-inGroup\"
                , a.groupName AS \"Koltiva.view.Grower.FormMainGrower-groupName\"
                , a.FarmerGroupID AS \"FarmerGroupID\"
                , a.inCoop AS \"Koltiva.view.Grower.FormMainGrower-inCoop\"
                , a.CoopName AS \"Koltiva.view.Grower.FormMainGrower-CoopName\"
                , a.inGapoktan AS \"Koltiva.view.Grower.FormMainGrower-inGapoktan\"
                , a.GapoktanName AS \"Koltiva.view.Grower.FormMainGrower-GapoktanName\"
                , a.HowManyPlantation AS \"Koltiva.view.Grower.FormMainGrower-HowManyPlantation\"
                , a.HaveBankAccount AS \"Koltiva.view.Grower.FormMainGrower-HaveBankAccount\"
                , a.BankBeneficiary AS \"Koltiva.view.Grower.FormMainGrower-BankBeneficiary\"
                , a.BankID AS \"Koltiva.view.Grower.FormMainGrower-BankID\"
                , a.BankClientID AS \"Koltiva.view.Grower.FormMainGrower-BankClientID\"
                , a.BankBranchName AS \"Koltiva.view.Grower.FormMainGrower-BankBranchName\"
                , a.BankAccNumber AS \"Koltiva.view.Grower.FormMainGrower-BankAccNumber\"
                , a.BankHolderName AS \"Koltiva.view.Grower.FormMainGrower-BankHolderName\"
                , a.AccountHolderRelation AS \"Koltiva.view.Grower.FormMainGrower-AccountHolderRelation\"
                , a.ReceiveBankTransfer AS \"Koltiva.view.Grower.FormMainGrower-ReceiveBankTransfer\"

                , a.ExtID AS \"Koltiva.view.Grower.FormMainGrower-ExtID\"
                , a.CategoryFarmer AS \"Koltiva.view.Grower.FormMainGrower-CategoryFarmer\"
                , a.TotalProductionArea AS \"Koltiva.view.Grower.FormMainGrower-TotalProductionArea\"
                , a.MembershipStatus AS \"Koltiva.view.Grower.FormMainGrower-MembershipStatus\"
                , a.FarmerGroupWAGSID AS \"Koltiva.view.Grower.FormMainGrower-FarmerGroupWAGSID\"
                , a.HowManyPlot AS \"Koltiva.view.Grower.FormMainGrower-HowManyPlot\"
                , a.WorkInPlot AS \"Koltiva.view.Grower.FormMainGrower-WorkInPlot\"
                , a.UseAPD AS \"Koltiva.view.Grower.FormMainGrower-UseAPD\"
                , a.HadAccident AS \"Koltiva.view.Grower.FormMainGrower-HadAccident\"
                , a.WhatAccident AS \"Koltiva.view.Grower.FormMainGrower-WhatAccident\"
                , a.HaveBPJS AS \"Koltiva.view.Grower.FormMainGrower-HaveBPJS\"
                , a.HaveBPJSKetenagakerjaan AS \"Koltiva.view.Grower.FormMainGrower-HaveBPJSKetenagakerjaan\"
                , a.HaveBPJSNo AS \"Koltiva.view.Grower.FormMainGrower-HaveBPJSNo\"
                , a.isCertified AS \"Koltiva.view.Grower.FormMainGrower-isCertified\"
                , a.SupplybaseType AS \"Koltiva.view.Grower.FormMainGrower-SupplybaseType\"
                , igar.TotalHectare AS \"Koltiva.view.Grower.FormMainGrower-PlotTotalHectare\" 
                , a.PartnerID
                , c.`frRespondentName` AS \"Koltiva.view.Grower.FormMainGrower-frRespondentName\"
                , c.`frRelationToOwner` AS \"Koltiva.view.Grower.FormMainGrower-frRelationToOwner\"
                , c.`frRelationToOwnerText` AS \"Koltiva.view.Grower.FormMainGrower-frRelationToOwnerText\"
                , c.`frComment` AS \"Koltiva.view.Grower.FormMainGrower-frComment\"
                , c.`frChildrenCount` AS \"Koltiva.view.Grower.FormMainGrower-frChildrenCount\"
                , c.`frChildrenSchool` AS \"Koltiva.view.Grower.FormMainGrower-frChildrenSchool\"
                , c.`frChildrenWorkInFarm` AS \"Koltiva.view.Grower.FormMainGrower-frChildrenWorkInFarm\"
                , c.`frChildrenUnderAgeWork` AS \"Koltiva.view.Grower.FormMainGrower-frChildrenUnderAgeWork\"
                , c.`frChildrenTypeOfWork` AS \"Koltiva.view.Grower.FormMainGrower-frChildrenTypeOfWork\"
                , c.`labHaveWorkers`
                , c.`labHowManyWorker`
                , c.`labWorkerUseApd`
                , c.`labWhoBuyApd`
                , c.`labWorkerHadAccident` AS \"Koltiva.view.Grower.FormLabourExtension-labWorkerHadAccident\"
                , c.`labWorkerAccidentKnife` AS \"Koltiva.view.Grower.FormLabourExtension-labWorkerAccidentKnife\"
                , c.`labWorkerAccidentHitbyFruit` AS \"Koltiva.view.Grower.FormLabourExtension-labWorkerAccidentHitbyFruit\"
                , c.`labWorkerAccidentContimination` AS \"Koltiva.view.Grower.FormLabourExtension-labWorkerAccidentContimination\"
                , c.`labWorkerAccidentOther` AS \"Koltiva.view.Grower.FormLabourExtension-labWorkerAccidentOther\"
                , c.`labWorkerAccidentOtherText` AS \"Koltiva.view.Grower.FormLabourExtension-labWorkerAccidentOtherText\"
                , c.`labWhatAccident`
                , c.`labWorkerHaveBpjs` AS \"Koltiva.view.Grower.FormLabourExtension-labWorkerHaveBpjs\"
                , c.`labWorkerHaveBPJSKetenagakerjaan` AS \"Koltiva.view.Grower.FormLabourExtension-labWorkerHaveBPJSKetenagakerjaan\"
                , c.`labWorkerHaveBPJSNo` AS \"Koltiva.view.Grower.FormLabourExtension-labWorkerHaveBPJSNo\"
                , c.`labWhoPayBpjs`
                , c.`labGiveInfoHealthSafety`
                , c.labWorkerLivePlantation
                , c.labWorkerSafeHouse
                , c.labWorkerKeepIdentity
                , c.labWorkerAccessibleDocument
                , c.labWorkerRecruitmentFee
                , c.labWorkerWrittenContract
                , c.labWorkerUnderstandRight
                , c.labWorkerDeductionWage
                , c.labWorkerFamilyWage
                , c.labWorkerComplaintSystem
                , c.labWorkerComplaintStored
                , c.labWorkerOweMoney
                , c.labWorkerBasicSupplies
                , mr.ObjID AS \"Koltiva.view.Grower.FormMainGrower-DealerAssign\"
                , c.`labWhoPayBpjs` AS \"Koltiva.view.Grower.FormLabourExtension-labWhoPayBpjs\"
                , c.`labGiveInfoHealthSafety` AS \"Koltiva.view.Grower.FormLabourExtension-labGiveInfoHealthSafety\"
                , c.labWorkerLivePlantation  AS \"Koltiva.view.Grower.FormLabourExtension-labWorkerLivePlantation\"
                , c.labWorkerSafeHouse  AS \"Koltiva.view.Grower.FormLabourExtension-labWorkerSafeHouse\"
                , c.labWorkerKeepIdentity  AS \"Koltiva.view.Grower.FormLabourExtension-labWorkerKeepIdentity\"
                , c.labWorkerAccessibleDocument  AS \"Koltiva.view.Grower.FormLabourExtension-labWorkerAccessibleDocument\"
                , c.labWorkerRecruitmentFee  AS \"Koltiva.view.Grower.FormLabourExtension-labWorkerRecruitmentFee\"
                , c.labWorkerWrittenContract  AS \"Koltiva.view.Grower.FormLabourExtension-labWorkerWrittenContract\"
                , c.labWorkerUnderstandRight  AS \"Koltiva.view.Grower.FormLabourExtension-labWorkerUnderstandRight\"
                , c.labWorkerDeductionWage  AS \"Koltiva.view.Grower.FormLabourExtension-labWorkerDeductionWage\"
                , c.labWorkerFamilyWage  AS \"Koltiva.view.Grower.FormLabourExtension-labWorkerFamilyWage\"
                , c.labWorkerComplaintSystem  AS \"Koltiva.view.Grower.FormLabourExtension-labWorkerComplaintSystem\"
                , c.labWorkerComplaintStored  AS \"Koltiva.view.Grower.FormLabourExtension-labWorkerComplaintStored\"
                , c.labWorkerOweMoney  AS \"Koltiva.view.Grower.FormLabourExtension-labWorkerOweMoney\"
                , c.labWorkerBasicSupplies  AS \"Koltiva.view.Grower.FormLabourExtension-labWorkerBasicSupplies\"
                , c.SurveyNr  AS \"Koltiva.view.Grower.FormLabourExtension-SurveyNr\"
                , c.DateCreated  AS \"Koltiva.view.Grower.FormLabourExtension-DateCreated\"
                , c.DateUpdated  AS \"Koltiva.view.Grower.FormLabourExtension-DateUpdated\"                
                , CONCAT((SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = c.`CreatedBy` LIMIT 1),', ',c.DateCreated) AS \"Koltiva.view.Grower.FormLabourExtension-Enumerator\"
                , CONCAT((SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = c.`LastModifiedBy` LIMIT 1),', ',c.DateUpdated) AS \"Koltiva.view.Grower.FormLabourExtension-ModifiedBy\"

                /*, CONCAT(
                    (SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy` LIMIT 1),
                    IF(a.`LastModifiedBy` IS NOT NULL OR a.`LastModifiedBy` != '',(SELECT CONCAT(', modified by ',sub_a.UserRealname) FROM sys_user sub_a WHERE sub_a.UserId = a.`LastModifiedBy` LIMIT 1),'')
                ) */
                , CONCAT((SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy` LIMIT 1),', ',a.DateCreated) AS \"Koltiva.view.Grower.FormMainGrower-Enumerator\"
                , CONCAT((SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`LastModifiedBy` LIMIT 1),', ',a.DateUpdated) AS \"Koltiva.view.Grower.FormMainGrower-ModifiedBy\"
                , CertificationRSPO AS \"Koltiva.view.Grower.FormMainGrower-IsCertified\"
                , CertificationISCC AS \"Koltiva.view.Grower.FormMainGrower-CertificationISCC\"
                , CertificationISPO AS \"Koltiva.view.Grower.FormMainGrower-CertificationISPO\"
                , CertificationMSPO AS \"Koltiva.view.Grower.FormMainGrower-CertificationMSPO\"
                , CertificationRSPO AS \"Koltiva.view.Grower.FormMainGrower-CertificationRSPO\"
                , ReceiveTraining AS \"Koltiva.view.Grower.FormMainGrower-ReceiveTraining\"
                , CertificationSourceGovernment AS \"Koltiva.view.Grower.FormMainGrower-CertificationSourceGovernment\"
                , CertificationSourceNGO AS \"Koltiva.view.Grower.FormMainGrower-CertificationSourceNGO\"
                , CertificationSourceMill AS \"Koltiva.view.Grower.FormMainGrower-CertificationSourceMill\"
                , CertificationSourcePrivateOrg AS \"Koltiva.view.Grower.FormMainGrower-CertificationSourcePrivateOrg\"
                , CertificationSourceOthers AS \"Koltiva.view.Grower.FormMainGrower-CertificationSourceOthers\"
                , CertificationTypeFinancial AS \"Koltiva.view.Grower.FormMainGrower-CertificationTypeFinancial\"
                , CertificationTypeGoodAgriculture AS \"Koltiva.view.Grower.FormMainGrower-CertificationTypeGoodAgriculture\"
                , CertificationTypeHumanRights AS \"Koltiva.view.Grower.FormMainGrower-CertificationTypeHumanRights\"
                , CertificationTypeManagementPesticides AS \"Koltiva.view.Grower.FormMainGrower-CertificationTypeManagementPesticides\"
                , CertificationTypeFireFighting AS \"Koltiva.view.Grower.FormMainGrower-CertificationTypeFireFighting\"
                , CertificationTypeHCVHCS AS \"Koltiva.view.Grower.FormMainGrower-CertificationTypeHCVHCS\"
                , CertificationTypeRSPOIndependent AS \"Koltiva.view.Grower.FormMainGrower-CertificationTypeRSPOIndependent\"
                , WillingnesParticipate AS \"Koltiva.view.Grower.FormMainGrower-WillingnesParticipate\"
                , WillingnesCommit AS \"Koltiva.view.Grower.FormMainGrower-WillingnesCommit\"
                , JoinProgram AS \"Koltiva.view.Grower.FormMainGrower-JoinProgram\"
                , NotJoinProgramReason AS \"Koltiva.view.Grower.FormMainGrower-NotJoinProgramReason\"
                , NotJoinProgramReasonText AS \"Koltiva.view.Grower.FormMainGrower-NotJoinProgramReasonText\"
                , JoinComment AS \"Koltiva.view.Grower.FormMainGrower-JoinComment\"
                , StatusMember AS \"Koltiva.view.Grower.FormMainGrower-StatusMember\"
                , InactiveReason AS \"Koltiva.view.Grower.FormMainGrower-InactiveReason\"
                , InactiveReasonText AS \"Koltiva.view.Grower.FormMainGrower-InactiveReasonText\"
                , AccidentKnife AS \"Koltiva.view.Grower.FormMainGrower-AccidentKnife\"
                , AccidentHitbyFruit AS \"Koltiva.view.Grower.FormMainGrower-AccidentHitbyFruit\"
                , AccidentContimination AS \"Koltiva.view.Grower.FormMainGrower-AccidentContimination\"
                , AccidentOther AS \"Koltiva.view.Grower.FormMainGrower-AccidentOther\"
                , AccidentOtherText AS \"Koltiva.view.Grower.FormMainGrower-AccidentOtherText\"
                , DateLastVerfication AS \"Koltiva.view.Grower.FormMainGrower-DateLastVerfication\"
                , StoppedReasonText AS \"Koltiva.view.Grower.FormMainGrower-StoppedReasonText\"
                , StoppedReason AS \"Koltiva.view.Grower.FormMainGrower-StoppedReason\" 
                , HaveOtherCommodities AS \"Koltiva.view.Grower.FormMainGrower-HaveOtherCommodities\" 
                , WillingnesSignature
                , WillingnesCommitSignature
            FROM
                ktv_members a
                LEFT JOIN ktv_member_role b ON a.`MemberID` = b.`MemberID`
                LEFT JOIN ktv_members_extension c ON a.MemberID = c.MemberID
                LEFT JOIN ktv_members_relation mr on mr.MemberID = a.MemberID
                LEFT JOIN (
                    SELECT
                        suba.`MemberID`
                        , SUM(suba.GardenAreaHa) AS TotalHectare
                    FROM
                        ktv_survey_plot suba
                        JOIN (SELECT
                                    p.MemberID, p.PlotNr, MAX(p.SurveyNr) AS SurveyNr
                                FROM ktv_survey_plot p WHERE p.`StatusCode` = 'active'
                                GROUP BY p.MemberID, p.PlotNr) suba_lat
                                    ON suba.MemberID = suba_lat.MemberID 
                                    AND suba.PlotNr = suba_lat.PlotNr 
                                    AND suba.SurveyNr = suba_lat.SurveyNr
                    WHERE
                        suba.`MemberID` = ?
                ) AS igar ON 1=1
                    AND a.`MemberID` = igar.MemberID
            WHERE
                a.`MemberID` = ?
            GROUP BY a.`MemberID`
            LIMIT 1";
        $query = $this->db->query($sql, array((int) $MemberID, (int) $MemberID));
        $data = $query->row_array();    
        if($this->awsfileupload->doesObjectExist($data['PhotoSrc']) == true) {
            $data['PhotoSrcPath'] = $data['PhotoSrc'];
            $data['PhotoSrc'] = $this->config->item('CTCDN')."/".$data['PhotoSrc'];
        }else{
            $data['PhotoSrcPath'] = '/images/member/'.$data["Province"].'/'.$data['PhotoSrc'];
            $data['PhotoSrc'] = base_url().'/images/member/'.$data["Province"].'/'.$data['PhotoSrc'];
        }

        if($this->awsfileupload->doesObjectExist($data['KTPSrc']) == true) {
            $data['KTPSrcPath'] = $data['KTPSrc'];
            $data['KTPSrc'] = $this->config->item('CTCDN')."/".$data['KTPSrc'];
        }else{
            $data['KTPSrcPath'] = '/images/member/ktp/'.$data["Province"].'/'.$data['KTPSrc'];
            $data['KTPSrc'] = base_url().'/images/member/ktp/'.$data["Province"].'/'.$data['KTPSrc'];
        }

        if($this->awsfileupload->doesObjectExist($data['WillingnesSignature']) == true) {
            $data['WillingnesSignaturePath'] = $data['WillingnesSignature'];
            $data['WillingnesSignature'] = $this->config->item('CTCDN')."/".$data['WillingnesSignature'];
        }else{
            $data['WillingnesSignaturePath'] = '/images/member/certification/'.$data["Province"].'/'.$data['WillingnesSignature'];
            $data['WillingnesSignature'] = base_url().'/images/member/certification/'.$data["Province"].'/'.$data['WillingnesSignature'];
        }

        if($this->awsfileupload->doesObjectExist($data['WillingnesCommitSignature']) == true) {
            $data['WillingnesCommitSignaturePath'] = $data['WillingnesCommitSignature'];
            $data['WillingnesCommitSignature'] = $this->config->item('CTCDN')."/".$data['WillingnesCommitSignature'];
        }else{
            $data['WillingnesCommitSignaturePath'] = '/images/member/certification/'.$data["Province"].'/'.$data['WillingnesCommitSignature'];
            $data['WillingnesCommitSignature'] = base_url().'/images/member/certification/'.$data["Province"].'/'.$data['WillingnesCommitSignature'];
        }

        $sqlGetInternalProgram   = "SELECT a.BusinessUnitID 
                                    FROM ktv_members_business_unit AS a
                                    LEFT JOIN ktv_ref_bu_internal_external AS b ON a.BusinessUnitID = b.BuInExID
                                    WHERE a.MemberID = ? AND b.BuInExType = 'Internal'";
        $queryGetInternalProgram = $this->db->query($sqlGetInternalProgram, array((int) $MemberID));
        $dataGetInternalProgram  = $queryGetInternalProgram->result_array();

        if(!empty($dataGetInternalProgram)){           
            foreach ($dataGetInternalProgram as $arrdata => $arrval) {
                $keyNewx = "Koltiva.view.Grower.FormMainGrower-CmbInternalProgram";
                $data[$keyNewx][] = $arrval["BusinessUnitID"];
            }
        }

        $sqlGetExternalProgram   = "SELECT a.BusinessUnitID 
                                    FROM ktv_members_business_unit AS a
                                    LEFT JOIN ktv_ref_bu_internal_external AS b ON a.BusinessUnitID = b.BuInExID
                                    WHERE a.MemberID = ? AND b.BuInExType = 'External'";
        $queryGetExternalProgram = $this->db->query($sqlGetExternalProgram, array((int) $MemberID));
        $dataGetExternalProgram  = $queryGetExternalProgram->result_array();

        if(!empty($dataGetExternalProgram)){           
            foreach ($dataGetExternalProgram as $arrdata => $arrval) {
                $keyNewx = "Koltiva.view.Grower.FormMainGrower-CmbExternalProgram";
                $data[$keyNewx][] = $arrval["BusinessUnitID"];
            }
        }

        $return['success'] = true;
        $return['data'] = $data;
        return $return;
    }

    public function getMemberBasicDataFormSME($MemberID) {
        $this->load->library('awsfileupload');
        $sql = "SELECT
                a.`MemberID` AS \"Koltiva.view.GrowerSME.FormMainGrower-MemberID\"
                , a.`MemberDisplayID` AS \"Koltiva.view.GrowerSME.FormMainGrower-MemberDisplayID\"
                , a.`DateCollection` AS \"Koltiva.view.GrowerSME.FormMainGrower-DateCollection\"
                , a.`MemberName` AS \"Koltiva.view.GrowerSME.FormMainGrower-Fullname\"
                , a.`DateOfBirth` AS \"Koltiva.view.GrowerSME.FormMainGrower-DateOfBirth\"
                , a.`Gender` AS \"Koltiva.view.GrowerSME.FormMainGrower-Gender\"
                , a.`Gender`
                , a.`SurveyNr` AS \"Koltiva.view.GrowerSME.FormMainGrower-SurveyNr\"
                , a.`MaritalStatus` AS \"Koltiva.view.GrowerSME.FormMainGrower-MaritalStatus\"
                , a.`Education` AS \"Koltiva.view.GrowerSME.FormMainGrower-Education\"
                , SUBSTR(a.`VillageID`,1,2) AS \"Province\"
                , SUBSTR(a.`VillageID`,1,4) AS \"District\"
                , SUBSTR(a.`VillageID`,1,7) AS \"Subdistrict\"
                , a.`VillageID` AS \"Village\"
                , a.`Address` AS \"Koltiva.view.GrowerSME.FormMainGrower-Address\"
                , a.`RtRw` AS \"Koltiva.view.GrowerSME.FormMainGrower-RtRw\"
                , a.`Handphone` AS \"Koltiva.view.GrowerSME.FormMainGrower-Handphone\"
                , a.`HandphoneType` AS \"Koltiva.view.GrowerSME.FormMainGrower-HandphoneType\"
                , a.`AccessToSmartphone` AS \"Koltiva.view.GrowerSME.FormMainGrower-AccessToSmartphone\"
                #, a.`Photo` AS \"Koltiva.view.GrowerSME.FormMainGrower-MemberPhotoOld\"
                , a.Photo AS PhotoSrc
                , a.KTPFile AS KTPSrc
                , a.LearningContractSign AS ConsentSrc
                , a.`PhotoDesc` AS \"Koltiva.view.GrowerSME.FormMainGrower-PhotoDesc\"
                , a.`StatusMember` AS \"Koltiva.view.GrowerSME.FormMainGrower-RbStatus\"
                , a.`InactiveReason` AS \"Koltiva.view.GrowerSME.FormMainGrower-InactiveReason\"
                , GROUP_CONCAT(b.`MRoleID` SEPARATOR ',') AS MemRole
                , a.Nin AS \"Koltiva.view.GrowerSME.FormMainGrower-Nin\"
                , a.inGroup AS \"Koltiva.view.GrowerSME.FormMainGrower-inGroup\"
                , a.groupName AS \"Koltiva.view.GrowerSME.FormMainGrower-groupName\"
                , a.FarmerGroupID AS \"FarmerGroupID\"
                , a.inCoop AS \"Koltiva.view.GrowerSME.FormMainGrower-inCoop\"
                , a.CoopName AS \"Koltiva.view.GrowerSME.FormMainGrower-CoopName\"
                , a.inGapoktan AS \"Koltiva.view.GrowerSME.FormMainGrower-inGapoktan\"
                , a.GapoktanName AS \"Koltiva.view.GrowerSME.FormMainGrower-GapoktanName\"
                , a.HowManyPlantation AS \"Koltiva.view.GrowerSME.FormMainGrower-HowManyPlantation\"
                , a.HaveBankAccount AS \"Koltiva.view.GrowerSME.FormMainGrower-HaveBankAccount\"
                , a.BankBeneficiary AS \"Koltiva.view.GrowerSME.FormMainGrower-BankBeneficiary\"
                , a.BankID AS \"Koltiva.view.GrowerSME.FormMainGrower-BankID\"
                , a.BankClientID AS \"Koltiva.view.GrowerSME.FormMainGrower-BankClientID\"
                , a.BankBranchName AS \"Koltiva.view.GrowerSME.FormMainGrower-BankBranchName\"
                , a.BankAccNumber AS \"Koltiva.view.GrowerSME.FormMainGrower-BankAccNumber\"
                , a.BankHolderName AS \"Koltiva.view.GrowerSME.FormMainGrower-BankHolderName\"
                , a.AccountHolderRelation AS \"Koltiva.view.GrowerSME.FormMainGrower-AccountHolderRelation\"
                , a.ReceiveBankTransfer AS \"Koltiva.view.GrowerSME.FormMainGrower-ReceiveBankTransfer\"

                , a.ExtID AS \"Koltiva.view.GrowerSME.FormMainGrower-ExtID\"
                , a.CategoryFarmer AS \"Koltiva.view.GrowerSME.FormMainGrower-CategoryFarmer\"
                , a.TotalProductionArea AS \"Koltiva.view.GrowerSME.FormMainGrower-TotalProductionArea\"
                , a.MembershipStatus AS \"Koltiva.view.GrowerSME.FormMainGrower-MembershipStatus\"
                , a.FarmerGroupWAGSID AS \"Koltiva.view.GrowerSME.FormMainGrower-FarmerGroupWAGSID\"
                , a.HowManyPlot AS \"Koltiva.view.GrowerSME.FormMainGrower-HowManyPlot\"
                , a.WorkInPlot AS \"Koltiva.view.GrowerSME.FormMainGrower-WorkInPlot\"
                , a.UseAPD AS \"Koltiva.view.GrowerSME.FormMainGrower-UseAPD\"
                , a.HadAccident AS \"Koltiva.view.GrowerSME.FormMainGrower-HadAccident\"
                , a.WhatAccident AS \"Koltiva.view.GrowerSME.FormMainGrower-WhatAccident\"
                , a.HaveBPJS AS \"Koltiva.view.GrowerSME.FormMainGrower-HaveBPJS\"
                , a.HaveBPJSKetenagakerjaan AS \"Koltiva.view.GrowerSME.FormMainGrower-HaveBPJSKetenagakerjaan\"
                , a.HaveBPJSNo AS \"Koltiva.view.GrowerSME.FormMainGrower-HaveBPJSNo\"
                , a.isCertified AS \"Koltiva.view.GrowerSME.FormMainGrower-isCertified\"
                , a.SupplybaseType AS \"Koltiva.view.GrowerSME.FormMainGrower-SupplybaseType\"
                , igar.TotalHectare AS \"Koltiva.view.GrowerSME.FormMainGrower-PlotTotalHectare\"
                , a.PartnerID

                , c.`frRespondentName` AS \"Koltiva.view.GrowerSME.FormMainGrower-frRespondentName\"
                , c.`frRelationToOwner` AS \"Koltiva.view.GrowerSME.FormMainGrower-frRelationToOwner\"
                , c.`frRelationToOwnerText` AS \"Koltiva.view.GrowerSME.FormMainGrower-frRelationToOwnerText\"
                , c.`frComment` AS \"Koltiva.view.GrowerSME.FormMainGrower-frComment\"
                , c.`frChildrenCount` AS \"Koltiva.view.GrowerSME.FormMainGrower-frChildrenCount\"
                , c.`frChildrenSchool` AS \"Koltiva.view.GrowerSME.FormMainGrower-frChildrenSchool\"
                , c.`frChildrenWorkInFarm` AS \"Koltiva.view.GrowerSME.FormMainGrower-frChildrenWorkInFarm\"
                , c.`frChildrenUnderAgeWork` AS \"Koltiva.view.GrowerSME.FormMainGrower-frChildrenUnderAgeWork\"
                , c.`frChildrenTypeOfWork` AS \"Koltiva.view.GrowerSME.FormMainGrower-frChildrenTypeOfWork\"
                , c.`labHaveWorkers`
                , c.`labHowManyWorker`
                , c.`labWorkerUseApd`
                , c.`labWhoBuyApd`
                , c.`labWorkerHadAccident`
                , c.`labWhatAccident`
                , c.`labWorkerHaveBpjs`
                , c.`labWhoPayBpjs`
                , c.`labGiveInfoHealthSafety`
                , c.labWorkerLivePlantation
                , c.labWorkerSafeHouse
                , c.labWorkerKeepIdentity
                , c.labWorkerAccessibleDocument
                , c.labWorkerRecruitmentFee
                , c.labWorkerWrittenContract
                , c.labWorkerUnderstandRight
                , c.labWorkerDeductionWage
                , c.labWorkerFamilyWage
                , c.labWorkerComplaintSystem
                , c.labWorkerComplaintStored
                , c.labWorkerOweMoney
                , c.labWorkerBasicSupplies
                , mr.ObjID AS \"Koltiva.view.GrowerSME.FormMainGrower-DealerAssign\"

                /*, CONCAT(
                    (SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy` LIMIT 1),
                    IF(a.`LastModifiedBy` IS NOT NULL OR a.`LastModifiedBy` != '',(SELECT CONCAT(', modified by ',sub_a.UserRealname) FROM sys_user sub_a WHERE sub_a.UserId = a.`LastModifiedBy` LIMIT 1),'')
                ) AS \"Koltiva.view.GrowerSME.FormMainGrower-Enumerator\"*/
                , CONCAT((SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy` LIMIT 1),', ',a.DateCreated) AS \"Koltiva.view.GrowerSME.FormMainGrower-Enumerator\"
                , CONCAT((SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`LastModifiedBy` LIMIT 1),', ',a.DateUpdated) AS \"Koltiva.view.GrowerSME.FormMainGrower-ModifiedBy\"
                , CertificationRSPO AS \"Koltiva.view.GrowerSME.FormMainGrower-IsCertified\"
                , CertificationISCC AS \"Koltiva.view.GrowerSME.FormMainGrower-CertificationISCC\"
                , CertificationISPO AS \"Koltiva.view.GrowerSME.FormMainGrower-CertificationISPO\"
                , CertificationMSPO AS \"Koltiva.view.GrowerSME.FormMainGrower-CertificationMSPO\"
                , CertificationRSPO AS \"Koltiva.view.GrowerSME.FormMainGrower-CertificationRSPO\"
                , ReceiveTraining AS \"Koltiva.view.GrowerSME.FormMainGrower-ReceiveTraining\"
                , CertificationSourceGovernment AS \"Koltiva.view.GrowerSME.FormMainGrower-CertificationSourceGovernment\"
                , CertificationSourceNGO AS \"Koltiva.view.GrowerSME.FormMainGrower-CertificationSourceNGO\"
                , CertificationSourceMill AS \"Koltiva.view.GrowerSME.FormMainGrower-CertificationSourceMill\"
                , CertificationSourcePrivateOrg AS \"Koltiva.view.GrowerSME.FormMainGrower-CertificationSourcePrivateOrg\"
                , CertificationSourceOthers AS \"Koltiva.view.GrowerSME.FormMainGrower-CertificationSourceOthers\"
                , CertificationTypeFinancial AS \"Koltiva.view.GrowerSME.FormMainGrower-CertificationTypeFinancial\"
                , CertificationTypeGoodAgriculture AS \"Koltiva.view.GrowerSME.FormMainGrower-CertificationTypeGoodAgriculture\"
                , CertificationTypeHumanRights AS \"Koltiva.view.GrowerSME.FormMainGrower-CertificationTypeHumanRights\"
                , CertificationTypeManagementPesticides AS \"Koltiva.view.GrowerSME.FormMainGrower-CertificationTypeManagementPesticides\"
                , CertificationTypeFireFighting AS \"Koltiva.view.GrowerSME.FormMainGrower-CertificationTypeFireFighting\"
                , CertificationTypeHCVHCS AS \"Koltiva.view.GrowerSME.FormMainGrower-CertificationTypeHCVHCS\"
                , CertificationTypeRSPOIndependent AS \"Koltiva.view.GrowerSME.FormMainGrower-CertificationTypeRSPOIndependent\"
                , WillingnesParticipate AS \"Koltiva.view.GrowerSME.FormMainGrower-WillingnesParticipate\"
                , WillingnesCommit AS \"Koltiva.view.GrowerSME.FormMainGrower-WillingnesCommit\"
                , JoinProgram AS \"Koltiva.view.GrowerSME.FormMainGrower-JoinProgram\"
                , NotJoinProgramReason AS \"Koltiva.view.GrowerSME.FormMainGrower-NotJoinProgramReason\"
                , NotJoinProgramReasonText AS \"Koltiva.view.GrowerSME.FormMainGrower-NotJoinProgramReasonText\"
                , JoinComment AS \"Koltiva.view.GrowerSME.FormMainGrower-JoinComment\"
                , StatusMember AS \"Koltiva.view.GrowerSME.FormMainGrower-StatusMember\"
                , InactiveReason AS \"Koltiva.view.GrowerSME.FormMainGrower-InactiveReason\"
                , InactiveReasonText AS \"Koltiva.view.GrowerSME.FormMainGrower-InactiveReasonText\"
                , AccidentKnife AS \"Koltiva.view.GrowerSME.FormMainGrower-AccidentKnife\"
                , AccidentHitbyFruit AS \"Koltiva.view.GrowerSME.FormMainGrower-AccidentHitbyFruit\"
                , AccidentContimination AS \"Koltiva.view.GrowerSME.FormMainGrower-AccidentContimination\"
                , AccidentOther AS \"Koltiva.view.GrowerSME.FormMainGrower-AccidentOther\"
                , DateLastVerfication AS \"Koltiva.view.GrowerSME.FormMainGrower-DateLastVerfication\"
                , StoppedReasonText AS \"Koltiva.view.GrowerSME.FormMainGrower-StoppedReasonText\"
                , StoppedReason AS \"Koltiva.view.Grower.FormMainGrower-StoppedReason\"
                , tsf.DateStart AS \"Koltiva.view.GrowerSME.FormMainGrower-DateStart\"
                , tsf.DateEnd AS \"Koltiva.view.GrowerSME.FormMainGrower-DateEnd\"
            FROM
                ktv_members a
                LEFT JOIN ktv_member_role b ON a.`MemberID` = b.`MemberID`
                LEFT JOIN ktv_members_extension c ON a.MemberID = c.MemberID
                LEFT JOIN ktv_members_relation mr on mr.MemberID = a.MemberID
                LEFT JOIN ktv_tc_supplychain_farmer tsf on tsf.FarmerID = a.MemberID
                LEFT JOIN (
                    SELECT
                        suba.`MemberID`
                        , SUM(suba.GardenAreaHa) AS TotalHectare
                    FROM
                        ktv_survey_plot suba
                        JOIN (SELECT
                                    p.MemberID, p.PlotNr, MAX(p.SurveyNr) AS SurveyNr
                                FROM ktv_survey_plot p WHERE p.`StatusCode` = 'active'
                                GROUP BY p.MemberID, p.PlotNr) suba_lat
                                    ON suba.MemberID = suba_lat.MemberID 
                                    AND suba.PlotNr = suba_lat.PlotNr 
                                    AND suba.SurveyNr = suba_lat.SurveyNr
                    WHERE
                        suba.`MemberID` = ?
                ) AS igar ON 1=1
                    AND a.`MemberID` = igar.MemberID
            WHERE
                a.`MemberID` = ?
            GROUP BY a.`MemberID`
            LIMIT 1";
        $query = $this->db->query($sql, array((int) $MemberID, (int) $MemberID));
        $data = $query->row_array();
        if($this->awsfileupload->doesObjectExist($data['PhotoSrc']) == true) {
            $data['PhotoSrcPath'] = $data['PhotoSrc'];
            $data['PhotoSrc'] = $this->config->item('CTCDN')."/".$data['PhotoSrc'];
        }else{
            $data['PhotoSrcPath'] = '/images/member/'.$data["Province"].'/'.$data['PhotoSrc'];
            $data['PhotoSrc'] = base_url().'/images/member/'.$data["Province"].'/'.$data['PhotoSrc'];
        }
        if($this->awsfileupload->doesObjectExist($data['KTPSrc']) == true) {
            $data['KTPSrcPath'] = $data['KTPSrc'];
            $data['KTPSrc'] = $this->config->item('CTCDN')."/".$data['KTPSrc'];
        }else{
            $data['KTPSrcPath'] = '/images/member/ktp/'.$data["Province"].'/'.$data['KTPSrc'];
            $data['KTPSrc'] = base_url().'/images/member/ktp/'.$data["Province"].'/'.$data['KTPSrc'];
        }
        $return['success'] = true;
        $return['data'] = $data;
        return $return;
    }

    public function getGardenData($MemberID){
        $sql="SELECT
                sp.PlotNr
                , sp.MemberID
                , sp.SurveyNr
                , sp.Latitude
                , sp.Longitude
                , sp.OwnershipDoc
                , sp.BusinessModel
                , sp.AverageAgeTree
                , sp.SoilType
                , sp.GardenAreaHa
                , CONCAT(subd.`SubDistrict`,', ',vil.`Village`) AS Location
            FROM
                `ktv_survey_plot_sme` sp
            JOIN
                (
                    SELECT 
                        g.MemberID 
                        , g.PlotNr 
                        , MAX(g.SurveyNr) AS SurveyNr 
                    FROM ktv_survey_plot_sme g 
                    GROUP BY MemberID, PlotNr
                ) z ON sp.MemberID = z.MemberID AND sp.PlotNr = z.PlotNr AND sp.SurveyNr = z.SurveyNr
            LEFT JOIN 
                ktv_village vil ON sp.`VillageID` = vil.`VillageID`
            LEFT JOIN 
                ktv_subdistrict subd ON vil.`SubDistrictID` = subd.`SubDistrictID`
            WHERE 1=1 
                AND sp.MemberID = ?
                AND sp.StatusCode = 'active'
            ORDER BY sp.`PlotNr` ASC";
        $query = $this->db->query($sql, array((int) $MemberID));
        return $query->result_array();
    }

    public function getMainGrowerSTAExcel($pSearch){
        $sqlFilter = "";
        $infoFilter = "";
        $sqlFilter = $this->generateSqlFilter($pSearch);

        $sqlHakAkses = $this->generateSqlHakAkses();

        //fixed tampilkan petani
        $sqlFilterRole = '';
        $sqlFilterRole .= " AND sub_b.MRoleID = 1 ";

        if ($sortingField == "")
            $sortingField = 'Name';
        if ($sortingDir == "")
            $sortingDir = 'ASC';

        $wags_access = $this->array_search_partial((object) $_SESSION['daerah_access'], '43');
        $wags_access2 = $this->array_search_partial((object) $_SESSION['daerah_access'], '44');
        
        $wags_access_area = '';
        if($wags_access != '' || $wags_access2 != ''){
            $wags_access_area = ", IF(a.isCertified = 1,'Yes','No')  Certified";
        }

        $sql = "
            SELECT SQL_CALC_FOUND_ROWS
                a.MemberID AS MemberIDInc
                , a.`MemberDisplayID` AS MemberID
                , a.Nin NatinoalIdetificationNumber
                , DATE_FORMAT( a.`DateCollection`, '%Y-%m-%d' ) AS DateCollection
                , a.`MemberName` AS Name
                , mmr.MemberDisplayID 'RealSMEID'
                , mmr.Alias 'RealSME'
                , mmr.Latitude 'RealSMELatitude'
                , mmr.Longitude 'RealSMELongitude'
                , agent.AgentID 'PotentialAgentID'
                , agent.AgentName 'PotentialAgentName'
                , agent.AgentLatitude 'PotentialAgentLatitude'
                , agent.AgentLongitude 'PotentialAgentLongitude'
                , vendor.VendorName 'PotentialVendorName'
                , vendor.VendorLatitude 'PotentialVendorLatitude'
                , vendor.VendorLongitude 'PotentialVendorLongitude'
                , mill.MillName
                $wags_access_area
                , a.`DateOfBirth` AS Birthdate
                , FLOOR( DATEDIFF( CURDATE( ), a.DateOfBirth ) / 365.25 ) AS Age
                , CASE
                        WHEN a.`Gender` = 'm' THEN 'Male'
                        WHEN a.`Gender` = 'f' THEN 'Female'
                        ELSE '-'
                    END AS Gender
                , CASE		
                    WHEN a.MaritalStatus = '1' THEN
                    'Married' 
                    WHEN a.MaritalStatus = '2' THEN
                    'Single' 
                    WHEN a.MaritalStatus = '3' THEN
                    'Widow/widower' 
                END AS MaritalStatus
                , CASE
                        WHEN a.Education = '1' THEN 'No Education'
                        WHEN a.Education = '2' THEN 'Primary School Incompleted'
                        WHEN a.Education = '3' THEN 'Primary School Completed'
                        WHEN a.Education = '4' THEN 'Graduated Middle School'
                        WHEN a.Education = '5' THEN 'Graduated High School'
                        WHEN a.Education = '7' THEN 'Magister/S2'
                        WHEN a.Education = '8' THEN 'Doctor/S3'
                        ELSE '-'
                    END AS Education
                , e.Province
                , f.District
                , d.`SubDistrict`
                , c.`Village`
                , a.Address
                , a.HandPhone
                , CASE
                        WHEN a.HandphoneType = 1 THEN 'Smartphone (Android/iPhone)' 
                        WHEN a.HandphoneType = 2 THEN 'Feature Phone (Basic Mobile Phone)' 
                        WHEN a.HandphoneType = 3 THEN 'No Handphone'
                        ELSE '-'
                    END AS HandphoneType
                , CASE
                        WHEN a.AccessToSmartphone = 1 THEN 'Yes'
                        WHEN a.AccessToSmartphone = 2 THEN 'No'
                        ELSE '-'
                    END AS AccessToSmartphone
                , CASE
                        WHEN a.inGroup = 1 THEN 'Yes'
                        WHEN a.inGroup = 2 THEN 'No'
                        ELSE '-'
                    END AS inGroup
                , IFNULL(fg.GroupName,a.groupName) GroupName
                , CASE
                        WHEN a.inCoop = 1 THEN 'Yes'
                        WHEN a.inCoop = 2 THEN 'No'
                        ELSE '-'
                    END AS inCoop
                , a.CoopName
                , CASE
                        WHEN a.inGapoktan = 1 THEN 'Yes'
                        WHEN a.inGapoktan = 2 THEN 'No'
                        ELSE '-'
                    END AS inGapoktan
                , a.GapoktanName
                , a.HowManyPlot
                , CASE
                        WHEN a.WorkInPlot = 1 THEN 'Yes'
                        WHEN a.WorkInPlot = 2 THEN 'No'
                        ELSE '-'
                    END WorkInPlot	
                , CASE
                        WHEN a.UseAPD = 1 THEN 'Yes'
                        WHEN a.UseAPD = 2 THEN 'No'
                        ELSE '-'
                    END UseAPD
                , CASE
                        WHEN a.HadAccident = 1 THEN 'Yes'
                        WHEN a.HadAccident = 2 THEN 'No'
                        ELSE '-'
                    END HadAccident
                , a.WhatAccident
                , CASE
                        WHEN a.HaveBPJS = 1 THEN 'Yes'
                        WHEN a.HaveBPJS = 2 THEN 'No'
                        ELSE '-'
                    END HaveBPJS
                , GROUP_CONCAT( DISTINCT(rrole.MRoleName) SEPARATOR ', ' ) AS MemberRole
                , a.`DateCreated` AS `Date Created`
                , ( SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy` ) AS Enumerator
                , a.`DateSync` AS `Date Sync`
                , a.`DateUpdated` AS `Last Update`
                , ( SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`LastModifiedBy` ) AS `Modified By`            
            FROM 
                ktv_members a
            INNER JOIN ktv_member_role mrole ON a.MemberID = mrole.MemberID AND mrole.MRoleID = 1 #Petani
            {$sqlHakAkses['join']}            
            LEFT JOIN ktv_program_partner_survey partsur ON a.PartnerID = partsur.PartnerID
            LEFT JOIN ktv_ref_member_role rrole ON mrole.MRoleID = rrole.MRoleID
            LEFT JOIN ktv_farmer_group fg on fg.FarmerGroupID = a.FarmerGroupID
            
            LEFT JOIN ktv_village c ON a.VillageID = c.VillageID
            LEFT JOIN ktv_subdistrict d ON c.SubDistrictID = d.SubDistrictID
            LEFT JOIN ktv_district f ON d.DistrictID = f.DistrictID
            LEFT JOIN ktv_province e ON f.ProvinceID = e.ProvinceID
            LEFT JOIN ktv_members_relation mr on mr.MemberID = a.MemberID
            LEFT JOIN ktv_members mmr on mmr.MemberID = mr.ObjID
            LEFT JOIN (
                SELECT
                    a.MemberID,
                    b.SupplychainID,
                    GROUP_CONCAT( b2.MemberDisplayID SEPARATOR ' | ') AgentID,
                    GROUP_CONCAT( DISTINCT b2.`Alias` SEPARATOR ' | ' ) AgentName,
                IF
                    ( GROUP_CONCAT( DISTINCT c.`Name` SEPARATOR ' | ' ) <> '', GROUP_CONCAT( DISTINCT a.Latitude SEPARATOR ' | ' ), '' ) AgentLatitude,
                IF
                    ( GROUP_CONCAT( DISTINCT c.`Name` SEPARATOR ' | ' ) <> '', GROUP_CONCAT( DISTINCT a.Longitude SEPARATOR ' | ' ), '' ) AgentLongitude,
                    apma.apmPartnerID 
                FROM
                    `ktv_members` a
                    LEFT JOIN ktv_tc_supplychain_farmer b ON b.FarmerID = a.MemberID
                    LEFT JOIN view_tc_supplychain_org c ON c.SupplychainID = b.SupplychainID
                    LEFT JOIN ktv_members b2 ON b2.MemberID = c.ObjID
                    LEFT JOIN ktv_access_partner_member apma ON apma.apmMemberID = b2.MemberID 
                WHERE
                    a.StatusCode = 'active' 
                    AND b.StatusCode = 'active' 
                    AND c.ObjType = 'agent' 
                GROUP BY
                    a.MemberID,
                    apma.apmPartnerID 
            ) agent ON agent.MemberID = a.MemberID 
                AND agent.apmPartnerID = acc_pm.apmPartnerID
            LEFT JOIN (
                SELECT
                    a.MemberID,
                    GROUP_CONCAT( c.PartnerName ) MillName,
                    GROUP_CONCAT( DISTINCT apmi.apmiPartnerID ) 'PartnerID' 
                FROM
                    `ktv_members` a
                    LEFT JOIN ktv_access_partner_member b ON apmMemberID = a.MemberID
                    LEFT JOIN ktv_program_partner c ON c.PartnerID = b.apmPartnerID
                    LEFT JOIN ktv_mill AS m ON m.PartnerID = c.PartnerID
                    LEFT JOIN `ktv_access_partner_mill` apmi ON apmiMillID = m.MillID 
                WHERE
                    a.StatusCode = 'active' 
                    AND c.StatusCode = 'active' 
                    AND m.StatusCode = 'active' 
                    AND c.PartnerID NOT IN (
                        1,
                        2,
                        3,
                        4,
                        5,
                        6,
                        7,
                        8,
                        9,
                        10,
                        11,
                        12,
                        13,
                        14 
                    ) 
                GROUP BY
                    a.MemberID,
                    apmi.apmiPartnerID 
            ) mill ON mill.MemberID = a.MemberID 
                AND mill.PartnerID = acc_pm.apmPartnerID
            LEFT JOIN (
                SELECT
                    a.MemberID,
                    b.SupplychainID,
                    GROUP_CONCAT( DISTINCT c.vendor SEPARATOR ' | ' ) VendorName,
                IF
                    ( GROUP_CONCAT( DISTINCT c.vendor SEPARATOR ' | ' ) <> '', GROUP_CONCAT( DISTINCT a.Latitude SEPARATOR ' | ' ), '' ) VendorLatitude,
                IF
                    ( GROUP_CONCAT( DISTINCT c.vendor SEPARATOR ' | ' ) <> '', GROUP_CONCAT( DISTINCT a.Longitude SEPARATOR ' | ' ), '' ) VendorLongitude,
                    c.apmPartnerID 
                FROM
                    `ktv_members` a
                    LEFT JOIN ktv_tc_supplychain_farmer b ON b.FarmerID = a.MemberID
                    LEFT JOIN (
                    SELECT DISTINCT
                        apmv.apmPartnerID,
                        child.SupplychainID,
                        child.ObjType ObjTypeVendor,
                        child.`Name` vendor,
                        parent.`Name` mill,
                        parent.ObjType ObjTypeMill 
                    FROM
                        ktv_tc_supplychain_org_rel rel
                        LEFT JOIN view_tc_supplychain_org parent ON parent.SupplychainID = rel.ParentID
                        LEFT JOIN view_tc_supplychain_org child ON child.SupplychainID = rel.ChildID
                        LEFT JOIN ktv_members b2 ON b2.MemberID = child.ObjID
                        LEFT JOIN ktv_access_partner_member apmv ON apmv.apmMemberID = b2.MemberID 
                    WHERE
                        rel.StatusCode = 'active' 
                        AND parent.ObjType = 'mill' 
                        AND child.ObjType = 'agent' 
                    ) c ON c.SupplychainID = b.SupplychainID 
                WHERE
                    a.StatusCode = 'active' 
                    AND b.StatusCode = 'active' 
                GROUP BY
                    a.MemberID,
                    c.apmPartnerID 
                ) vendor ON vendor.MemberID = a.MemberID 
            WHERE 
                a.`StatusCode` = 'active'            
                $sqlFilter
                {$sqlHakAkses['where']}
            GROUP BY a.MemberID
            ORDER BY $sortingField $sortingDir
            $sqlLimit
        ";

        $query = $this->db->query($sql);
        if($query->num_rows()>0){
            return $query->result_array();
        }else{
            return false;
        }
    }

    public function getMainGrowerExcel($pSearch){
        $sqlFilter = "";
        $infoFilter = "";
        $sqlFilter = $this->generateSqlFilter($pSearch);

        $sqlHakAkses = $this->generateSqlHakAkses();

        //fixed tampilkan petani
        $sqlFilterRole = '';
        $sqlFilterRole .= " AND sub_b.MRoleID = 1 ";

        if ($sortingField == "")
            $sortingField = 'Name';
        if ($sortingDir == "")
            $sortingDir = 'ASC';

        $wags_access = $this->array_search_partial((object) $_SESSION['daerah_access'], '43');
        $wags_access2 = $this->array_search_partial((object) $_SESSION['daerah_access'], '44');
        
        $wags_access_area = '';
        if($wags_access != '' || $wags_access2 != ''){
            $wags_access_area = " IF(a.isCertified = 1,'Yes','No')  Certified, ";
        }

        $millName = " IFNULL(a.MillName, '-')";
        if($_SESSION["role"] == "Private" AND $_SESSION["PartnerID"] != '1'){
            $millName = " IFNULL(pp.PartnerName, '-')";
        }

        ($_SESSION['business_unit'] != "") ? $sqlBu = " AND membu.BusinessUnitID IN ({$_SESSION['business_unit']}) " : $sqlBu = "";

        $sql = "SELECT
            a.MemberID,
            a.MemberDisplayID,
            a.Nin NatinoalIdetificationNumber,
            DATE_FORMAT( a.`DateCollection`, '%Y-%m-%d' ) AS DateCollection,
            a.`MemberName` AS NAME,
            agent.AgentID,
            agent.AgentName,
            agent.AgentLatitude,
            agent.AgentLongitude,
            vendor.VendorName AS DealerName,
            vendor.VendorLatitude AS DealerLatitude,
            vendor.VendorLongitude AS DealerLongitude,
            $millName MillName,            
        CASE
                
                WHEN a.inGroup = 1 THEN
                'Yes' 
                WHEN a.inGroup = 2 THEN
                'No' ELSE '-' 
            END AS AnggotaGroup,
            IFNULL( fg.GroupName, a.groupName ) FarmerGroupName,
            $wags_access_area
            a.`DateOfBirth` AS Birthdate,
            FLOOR( DATEDIFF( CURDATE( ), a.DateOfBirth ) / 365.25 ) AS Age,
        CASE
                
                WHEN a.`Gender` = 'm' THEN
                'Male' 
                WHEN a.`Gender` = 'f' THEN
                'Female' ELSE '-' 
            END AS Gender,
        CASE
                
                WHEN a.MaritalStatus = '1' THEN
                'Married' 
                WHEN a.MaritalStatus = '2' THEN
                'Single' 
                WHEN a.MaritalStatus = '3' THEN
                'Widow/widower' 
            END AS MaritalStatus,
        CASE
                
                WHEN a.Education = '1' THEN
                'No Education' 
                WHEN a.Education = '2' THEN
                'Primary School Incompleted' 
                WHEN a.Education = '3' THEN
                'Primary School Completed' 
                WHEN a.Education = '4' THEN
                'Graduated Middle School' 
                WHEN a.Education = '5' THEN
                'Graduated High School' 
                WHEN a.Education = '7' THEN
                'Magister/S2' 
                WHEN a.Education = '8' THEN
                'Doctor/S3' ELSE '-' 
            END AS Education,
            e.Province,
            f.District,
            d.`SubDistrict`,
            c.`Village`,
            a.Address,
            a.HandPhone,
        CASE
                
                WHEN a.HandphoneType = 1 THEN
                'Smartphone (Android/iPhone)' 
                WHEN a.HandphoneType = 2 THEN
                'Feature Phone (Basic Mobile Phone)' 
                WHEN a.HandphoneType = 3 THEN
                'No Handphone' ELSE '-' 
            END AS HandphoneType,
        CASE
                
                WHEN a.AccessToSmartphone = 1 THEN
                'Yes' 
                WHEN a.AccessToSmartphone = 2 THEN
                'No' ELSE '-' 
            END AS AccessToSmartphone,
        CASE
                
                WHEN a.inCoop = 1 THEN
                'Yes' 
                WHEN a.inCoop = 2 THEN
                'No' ELSE '-' 
            END AS inCoop,
            a.CoopName,
        CASE
                
                WHEN a.inGapoktan = 1 THEN
                'Yes' 
                WHEN a.inGapoktan = 2 THEN
                'No' ELSE '-' 
            END AS inGapoktan,
            a.GapoktanName,
            a.HowManyPlot,
        CASE
                
                WHEN a.WorkInPlot = 1 THEN
                'Yes' 
                WHEN a.WorkInPlot = 2 THEN
                'No' ELSE '-' 
            END WorkInPlot,
        CASE
                
                WHEN a.UseAPD = 1 THEN
                'Yes' 
                WHEN a.UseAPD = 2 THEN
                'No' ELSE '-' 
            END UseAPD,
        CASE
                
                WHEN a.HadAccident = 1 THEN
                'Yes' 
                WHEN a.HadAccident = 2 THEN
                'No' ELSE '-' 
            END HadAccident,
            a.WhatAccident,
        CASE
                
                WHEN a.HaveBPJS = 1 THEN
                'Yes' 
                WHEN a.HaveBPJS = 2 THEN
                'No' ELSE '-' 
            END HaveBPJS,
            GROUP_CONCAT( DISTINCT ( rrole.MRoleName ) SEPARATOR ', ' ) AS MemberRole,
            a.`DateCreated` AS `Date Created`,
            ( SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy` ) AS Enumerator,
            a.`DateSync` AS `Date Sync`,
            a.`DateUpdated` AS `Last Update`,
            ( SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`LastModifiedBy` ) AS `Modified By` 
        FROM
            ktv_members a
            INNER JOIN ktv_member_role mrole ON a.MemberID = mrole.MemberID AND mrole.MRoleID = 1 #Petani
            {$sqlHakAkses['join']}
            INNER JOIN ktv_ref_member_role rrole ON mrole.MRoleID = rrole.MRoleID
            
            INNER JOIN ktv_village c ON a.VillageID = c.VillageID
            INNER JOIN ktv_subdistrict d ON c.SubDistrictID = d.SubDistrictID
            INNER JOIN ktv_district f ON d.DistrictID = f.DistrictID
            INNER JOIN ktv_province e ON f.ProvinceID = e.ProvinceID
            LEFT JOIN ktv_farmer_group fg ON fg.FarmerGroupID = a.FarmerGroupID
            LEFT JOIN ktv_tc_supplychain_farmer tsf on tsf.FarmerID = a.MemberID
            LEFT JOIN ktv_members_business_unit membu on membu.MemberID = a.MemberID
            LEFT JOIN (
                SELECT
                    a.FarmerID MemberID
                    , vso.SupplychainID
                    , GROUP_CONCAT( DISTINCT b.MemberDisplayID SEPARATOR ' | ' ) AgentID
                    , GROUP_CONCAT( DISTINCT b.`Alias` SEPARATOR ' | ' ) AgentName
                    , IF ( GROUP_CONCAT( DISTINCT vso.`Name` SEPARATOR ' | ' ) <> '', GROUP_CONCAT( DISTINCT b.Latitude SEPARATOR ' | ' ), '' ) AgentLatitude
                    , IF ( GROUP_CONCAT( DISTINCT vso.`Name` SEPARATOR ' | ' ) <> '', GROUP_CONCAT( DISTINCT b.Longitude SEPARATOR ' | ' ), '' ) AgentLongitude
                FROM
                    ktv_tc_supplychain_farmer a
                INNER JOIN
                    view_tc_supplychain_org vso on vso.SupplychainID = a.SupplychainID AND vso.ObjType = 'agent'
                JOIN
                    ktv_members b on b.MemberID = vso.ObjID
                WHERE
                    a.StatusCode = 'active'
                GROUP BY a.FarmerID 
            ) agent ON agent.MemberID = a.MemberID
            LEFT JOIN (
                SELECT
                    a.FarmerID MemberID
                    , a.SupplychainID
                    , GROUP_CONCAT( DISTINCT c.vendor SEPARATOR ' | ' ) VendorName
                    , IF ( GROUP_CONCAT( DISTINCT c.vendor SEPARATOR ' | ' ) <> '', GROUP_CONCAT( DISTINCT b.Latitude SEPARATOR ' | ' ), '' ) VendorLatitude
                    , IF
                            ( GROUP_CONCAT( DISTINCT c.vendor SEPARATOR ' | ' ) <> '', GROUP_CONCAT( DISTINCT b.Longitude SEPARATOR ' | ' ), '' ) VendorLongitude
                FROM
                    ktv_tc_supplychain_farmer a
                INNER JOIN
                    view_tc_supplychain_org vso on vso.SupplychainID = a.SupplychainID AND vso.ObjType = 'agent'
                INNER JOIN
                    ktv_members b on b.MemberID = vso.ObjID
                INNER JOIN(
                    SELECT DISTINCT
                        child.SupplychainID,
                        child.ObjType ObjTypeVendor,
                        child.`Name` vendor,
                        parent.`Name` mill,
                        parent.ObjType ObjTypeMill 
                    FROM
                        ktv_tc_supplychain_org_rel rel
                        LEFT JOIN view_tc_supplychain_org parent ON parent.SupplychainID = rel.ParentID
                        LEFT JOIN view_tc_supplychain_org child ON child.SupplychainID = rel.ChildID
                    WHERE
                        rel.StatusCode = 'active' 
                        AND parent.ObjType = 'mill' 
                        AND child.ObjType = 'agent' 
                ) c ON c.SupplychainID = a.SupplychainID
                WHERE
                    a.StatusCode = 'active'
                GROUP BY
                        a.FarmerID
            ) vendor ON vendor.MemberID = a.MemberID
        WHERE
            a.`StatusCode` = 'active'
            $sqlBu
            $sqlFilter
            {$sqlHakAkses['where']}
        GROUP BY a.MemberID
        ORDER BY $sortingField $sortingDir";

        $query = $this->db->query($sql);
        if($query->num_rows()>0){
            return $query->result_array();
        }else{
            return false;
        }
    }

    function array_search_partial($arr, $keyword) {
        foreach($arr as $index => $string) {
            if (strpos($string, $keyword) !== FALSE)
                return $index;
        }
    }

    public function getPlotSurvey($pSearch){
        $sqlFilter = $this->generateSqlFilter($pSearch);

        $sqlHakAkses = $this->generateSqlHakAkses();
        
        //fixed tampilkan petani
        $sqlFilterRole = '';
        $sqlFilterRole .= " AND sub_b.MRoleID = 1 ";

        if ($sortingField == "")
            $sortingField = 'Name';
        if ($sortingDir == "")
            $sortingDir = 'ASC';

        $millName = " IFNULL(a.MillName, '-')";
        if($_SESSION["role"] == "Private" AND $_SESSION["PartnerID"] != '1'){
            $millName = " IFNULL(pp.PartnerName, '-')";
        }

        ($_SESSION['business_unit'] != "") ? $sqlBu = " AND membu.BusinessUnitID IN ({$_SESSION['business_unit']}) " : $sqlBu = "";

        $sql = 'SELECT  
                    a.MemberDisplayID AS "Farmer ID",
                    a.MemberID,
                    a.MemberName AS "Farmer Name",
                    agent.AgentID,
                    agent.AgentName,
                    agent.AgentLatitude,
                    agent.AgentLongitude,
                    '.$millName.' MillName,          
                    CASE
                            
                        WHEN a.inGroup = 1 THEN
                        "Yes" 
                        WHEN a.inGroup = 2 THEN
                        "No" ELSE "-" 
                    END AS AnggotaGroup,
                    IFNULL( fg.GroupName, a.groupName ) FarmerGroupName,
                    b.PlotNr AS "Plantation Nr",
                    CONCAT(b.SurveyNr, " - ", g.SurveyTxt) AS "Survey Name",
                    b.DateCollection AS "Date Collection",
                    e.Province,
                    f.District,
                    d.`SubDistrict`,
                    c.`Village`,
                    b.GardenAreaHa,-- AS "Area of Plantation(Ha)",
                    b.GardenAreaPolygon,-- AS "Area of Garden Polygon (Ha)",
                    --IFNULL(ST_Y(b.LatLong), b.Latitude) Latitude,-- AS "Latitude",
                    --IFNULL(ST_X(b.LatLong), b.Longitude) Longitude,-- AS "Latitude",
                    CASE
                        WHEN b.LandOwnershipType=1 THEN "Owned"
                        WHEN b.LandOwnershipType=2 THEN "Profit Sharing"
                        WHEN b.LandOwnershipType=3 THEN "Rented"
                        ELSE "Others"
                    END  AS "Land Ownership",
                    CASE
                        WHEN b.OwnerOfTheGarden=1 THEN "Registered Farmer"
                        WHEN b.OwnerOfTheGarden=2 THEN "Family Members"
                        WHEN b.OwnerOfTheGarden=3 THEN "Other People"
                        ELSE "Do Not Know "
                    END AS "Owner of the Garden",
                    b.OwnerOfPlantationNameText,-- AS "Owner of this Plantation - Name",
                    b.OwnerOfPlantationLocationText,-- AS "Owner of this Plantation - Location",
                    CASE 
                        WHEN b.OwnershipDoc=1 THEN "No Document"
                        WHEN b.OwnershipDoc=2 THEN "SKT (Surat Keterangan Tanah)"
                        WHEN b.OwnershipDoc=3 THEN "SHM (Sertifikat Hak Milik)/Certificate"
                        WHEN b.OwnershipDoc=4 THEN "HGU (Hak Guna Usaha)"
                        WHEN b.OwnershipDoc=5 THEN "SKGR (Surat Keterangan Ganti Rugi)" 
                        ELSE b.OwnershipDocText
                    END AS "Ownership Document",
                    IF(b.OwnerDocIsOwner=1, "Yes", IF(b.OwnerDocIsOwner=2, "No", "Do Not Know")) AS "Is the ownership document in the name of the current owner",
                    IF(b.HaveSTDB=1, "Yes", IF(b.HaveSTDB=2, "No", "Do Not Know")) AS "Does the farm have a STD-B (operational / business letter)",
                    IF(b.HaveSPPL=1, "Yes", IF(b.HaveSPPL=2, "No", "Do Not Know")) AS "Does the farm have a SPPL (Environmental Management Letter)",
                    IF(b.BusinessModel=1, "Independence", IF(b.BusinessModel=2, "Independent - Ex Plasma", "Plasma(has existing contract with plantation)")) AS "Business Model",
                    CASE
                        WHEN b.HowObPlantation=1 THEN "Inheritance"
                        WHEN b.HowObPlantation=2 THEN "Purchased"
                        WHEN b.HowObPlantation=3 THEN "Convert Existing Plantation"
                        WHEN b.HowObPlantation=4 THEN "Received From Government (Transmigrate)" 
                        ELSE b.HowObPlantationText
                    END AS "How did you obtain the plantation",
                    CASE
                        WHEN b.PlantationConditionEst=2 THEN "Secondary Veg/Fallow" 
                        WHEN b.PlantationConditionEst=2 THEN "Food Crops" 
                        WHEN b.PlantationConditionEst=3 THEN "Mangrove" 
                        WHEN b.PlantationConditionEst=4 THEN "Other Plantation (rubber, coffee, etc)" 
                        WHEN b.PlantationConditionEst=5 THEN "Oil Palm Plantation" 
                        WHEN b.PlantationConditionEst=6 THEN "Forest"  
                        ELSE "I dont know"
                    END AS "Condition when establishing oil palm plantation",
                    b.AverageAgeTree AS "Average age of trees on plantation (years)",
                    IF(b.SoilType=1, "Mineral", IF(b.SoilType=2, "Peat", IF(b.SoilType = 3, "Sandy", "-"))) AS "Soil Type",
                    IF(b.TopographyType=1, "Flat", IF(b.TopographyType=2, "Hilly", IF(b.TopographyType=3, "Mountainous", "-"))) AS "Type of Topography Plantation",
                    b.FirstPlantingYear AS "Year of first planting palm trees",
                    b.YearPlantingCurrent AS "Year of planting current oil palms",
                    b.TreeTBM AS "TBM - Plants yet to produce",
                    b.TreeTM AS "TM - Producing plants",
                    b.TreeTR AS "TR - Old/diseased",
                    b.TreeTBM + b.TreeTM + b.TreeTR AS "Total Number of Trees",
                    IF(b.TypePlantMateMarihat=1, "Yes", "No") AS "Type of Planting Material - Marihat",
                    b.TypePlantMateMarihatNr AS "Type of Planting Material - Marihat Number Of Trees",
                    IF(b.TypePlantMateDumpy=1, "Yes", "No") AS "Type of Planting Material - Dumpy",
                    b.TypePlantMateDumpyNr AS "Type of Planting Material - Dumpy Number Of Trees",
                    IF(b.TypePlantMateLonsum=1, "Yes", "No") AS "Type of Planting Material - Lonsum",
                    b.TypePlantMateLonsumNr AS "Type of Planting Material - Lonsum Number Of Trees",
                    IF(b.TypePlantMateSimalungun=1, "Yes", "No") AS "Type of Planting Material - Simalungun",
                    b.TypePlantMateSimalungunNr AS "Type of Planting Material - Simalungun Number Of Trees",
                    IF(b.TypePlantMateDanimas=1, "Yes", "No") AS "Type of Planting Material - Dami Mas",
                    b.TypePlantMateDanimasNr AS "Type of Planting Material - Dami Mas Number Of Trees",
                    IF(b.TypePlantMateSriwijaya=1, "Yes", "No") AS "Type of Planting Material - Sriwijaya",
                    b.TypePlantMateSriwijayaNr AS "Type of Planting Material - Sriwijaya Number Of Trees",
                    IF(b.TypePlantMateSocfin=1, "Yes", "No") AS "Type of Planting Material - Socfin",
                    b.TypePlantMateSocfinNr AS "Type of Planting Material - Socfin Number Of Trees",
                    IF(b.TypePlantMateOther=1, b.TypePlantMateOtherText, "-") AS "Type of Planting Material - Other Method",
                    b.TypePlantMateOtherNr AS "Type of Planting Material - Other Method Number Of Trees",
                    IF(b.TypePlantMateDoNotKnow=1, "Yes", "No") AS "Type of Planting Material - Do Not Know",
                    b.TypePlantMateDoNotKnowNr AS "Type of Planting Material - Do Not Know Number Of Trees",
                    b.TreeTBM + b.TreeTM + b.TreeTR AS "Total Number of Oil Palm Trees",
                    b.HarvestRateDaysHighSeason AS "Harvest rate (once every how many days) in high season",
                    b.HarvestRateDaysLowSeason AS "Harvest rate (once every how many days) in low season",
                    b.AverageProdHighSeason AS "Average production per harvest (ton) in high season",
                    b.AverageProdLowSeason AS "Average production per harvest (ton) in low season",
                    b.NrHighSeasonMonths AS "Number of Months in High Season",
                    b.NrLowSeasonMonths AS "Number of Months in Low Season",
                    b.HighSeasonProduction AS "High Season Production (ton)",
                    b.LowSeasonProduction AS "Low Season Production (ton)",
                    b.AnnualProduction AS "Annual Production (ton)",
                    b.PlantationProductivity AS "Plantation Productivity (ton/ha)",
                    IF(b.LeanHarvestSeasonJan=1, "Yes", "No") AS "When is the High harvest season for oil palm in your area - January",
                    IF(b.LeanHarvestSeasonFeb=1, "Yes", "No") AS "When is the High harvest season for oil palm in your area - February",
                    IF(b.LeanHarvestSeasonMar=1, "Yes", "No") AS "When is the High harvest season for oil palm in your area - March",
                    IF(b.LeanHarvestSeasonApr=1, "Yes", "No") AS "When is the High harvest season for oil palm in your area - April",
                    IF(b.LeanHarvestSeasonMay=1, "Yes", "No") AS "When is the High harvest season for oil palm in your area - May",
                    IF(b.LeanHarvestSeasonJun=1, "Yes", "No") AS "When is the High harvest season for oil palm in your area - June",
                    IF(b.LeanHarvestSeasonJul=1, "Yes", "No") AS "When is the High harvest season for oil palm in your area - July",
                    IF(b.LeanHarvestSeasonAug=1, "Yes", "No") AS "When is the High harvest season for oil palm in your area - August",
                    IF(b.LeanHarvestSeasonSep=1, "Yes", "No") AS "When is the High harvest season for oil palm in your area - September",
                    IF(b.LeanHarvestSeasonOct=1, "Yes", "No") AS "When is the High harvest season for oil palm in your area - October",
                    IF(b.LeanHarvestSeasonNov=1, "Yes", "No") AS "When is the High harvest season for oil palm in your area - November",
                    IF(b.LeanHarvestSeasonDec=1, "Yes", "No") AS "When is the High harvest season for oil palm in your area - December",
                    IF(b.WhoHarvestFamily=1, "Yes", "No") AS "Who does the harvesting - Respondent and/or Family member",
                    IF(b.WhoHarvestLabor=1, "Yes", "No") AS "Who does the harvesting - Use of Hired Labor",
                    CASE
                    WHEN b.HowManyDiffBuyerSoldLastYear=1 THEN "1"
                    WHEN b.HowManyDiffBuyerSoldLastYear=1 THEN "2"
                    WHEN b.HowManyDiffBuyerSoldLastYear=1 THEN "3"
                    WHEN b.HowManyDiffBuyerSoldLastYear=1 THEN "4" 
                    ELSE "More than 4"
                    END AS "To how many different buyers have you sold your FFB from this plantation to within the past year",
                    CASE
                    WHEN b.HowManyDiffMillSoldLastYear=1 THEN "1"
                    WHEN b.HowManyDiffMillSoldLastYear=1 THEN "2"
                    WHEN b.HowManyDiffMillSoldLastYear=1 THEN "3"
                    WHEN b.HowManyDiffMillSoldLastYear=1 THEN "4" 
                    ELSE "More than 4"
                    END AS "How many different palm oil mills have you sold your FFB to within the past year",
                    b.Comment,-- AS "Any comments about plantation"
                    b.`DateCreated` AS `Date Created`,
                    ( SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = b.`CreatedBy` ) AS Enumerator,
                    b.`DateSync` AS `Date Sync`,
                    b.`DateUpdated` AS `Last Update`,
                    ( SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = b.`LastModifiedBy` ) AS `Modified By`
            FROM ktv_members a
            LEFT JOIN ktv_survey_plot b ON a.MemberID = b.MemberID
            LEFT JOIN ktv_farmer_group fg ON fg.FarmerGroupID = a.FarmerGroupID
            LEFT JOIN ktv_village c ON b.VillageID = c.VillageID
            LEFT JOIN ktv_subdistrict d ON c.SubDistrictID = d.SubDistrictID
            LEFT JOIN ktv_district f ON d.DistrictID = f.DistrictID
            LEFT JOIN ktv_province e ON f.ProvinceID = e.ProvinceID
            LEFT JOIN ktv_survey g ON b.SurveyNr = g.SurveyNr
            LEFT JOIN ktv_access_partner_member h ON a.MemberID = h.apmMemberID
            LEFT JOIN ktv_tc_supplychain_farmer tsf on tsf.FarmerID = a.MemberID
            INNER JOIN ktv_member_role mrole ON a.MemberID = mrole.MemberID AND mrole.MRoleID = 1 #Petani
            LEFT JOIN ktv_members_business_unit membu on membu.MemberID = a.MemberID
            LEFT JOIN (
                SELECT
                    a.MemberID,
                    b.SupplychainID,
                    GROUP_CONCAT( DISTINCT b2.MemberDisplayID SEPARATOR " | ") AgentID,
                    GROUP_CONCAT( DISTINCT b2.`Alias` SEPARATOR " | " ) AgentName,
                IF
                    ( GROUP_CONCAT( DISTINCT c.`Name` SEPARATOR " | " ) <> "", GROUP_CONCAT( DISTINCT b2.Latitude SEPARATOR " | " ), "" ) AgentLatitude,
                IF
                    ( GROUP_CONCAT( DISTINCT c.`Name` SEPARATOR " | " ) <> "", GROUP_CONCAT( DISTINCT b2.Longitude SEPARATOR " | " ), "" ) AgentLongitude,
                    apma.apmPartnerID 
                FROM
                    `ktv_members` a
                    LEFT JOIN ktv_tc_supplychain_farmer b ON b.FarmerID = a.MemberID
                    LEFT JOIN view_tc_supplychain_org c ON c.SupplychainID = b.SupplychainID
                    LEFT JOIN ktv_members b2 ON b2.MemberID = c.ObjID
                    LEFT JOIN ktv_access_partner_member apma ON apma.apmMemberID = b2.MemberID 
                WHERE
                    a.StatusCode = "active" 
                    AND b.StatusCode = "active" 
                    AND c.ObjType = "agent" 
                GROUP BY
                    a.MemberID,
                    apma.apmPartnerID 
            ) agent ON agent.MemberID = a.MemberID AND agent.apmPartnerID = h.apmPartnerID
            LEFT JOIN (
                SELECT
                    a.MemberID,
                    GROUP_CONCAT( c.PartnerName ) MillName,
                    GROUP_CONCAT( DISTINCT apmi.apmiPartnerID ) "PartnerID" 
                FROM
                    `ktv_members` a
                    LEFT JOIN ktv_access_partner_member b ON apmMemberID = a.MemberID
                    LEFT JOIN ktv_program_partner c ON c.PartnerID = b.apmPartnerID
                    LEFT JOIN ktv_mill AS m ON m.PartnerID = c.PartnerID
                    LEFT JOIN `ktv_access_partner_mill` apmi ON apmiMillID = m.MillID 
                WHERE
                    a.StatusCode = "active" 
                    AND c.StatusCode = "active" 
                    AND m.StatusCode = "active" 
                    AND c.PartnerID NOT IN (
                        1,
                        2,
                        3,
                        4,
                        5,
                        6,
                        7,
                        8,
                        9,
                        10,
                        11,
                        12,
                        13,
                        14 
                    ) 
                GROUP BY
                    a.MemberID,
                    apmi.apmiPartnerID
            ) mill ON mill.MemberID = a.MemberID AND mill.PartnerID = h.apmPartnerID
            '.$sqlHakAkses["join"].'
            WHERE 
                a.`StatusCode` = "active"
                AND b.StatusCode = "active"
                '.$sqlBu.'
                '.$sqlFilter.'
                '.$sqlHakAkses['where'].'
            GROUP BY a.MemberID, b.PlotNr, b.SurveyNr
            ORDER BY
            a.MemberName ASC
        ';

        $query = $this->db->query($sql);
        return $query->result_array();
    }


    public function getPlotSurveyMill($pSearch){
        $sqlFilter = "";
        $infoFilter = "";
        $sqlFilter = $this->generateSqlFilterMill($pSearch);

        $sqlHakAkses = $this->generateSqlHakAkses();

        //fixed tampilkan petani
        $sqlFilterRole = '';
        $sqlFilterRole .= " AND sub_b.MRoleID = 1 ";

        if ($pSearch['pPartnerSearch'] != '') {
            $filterPartner = ' WHERE a.PartnerID IN(' . $pSearch['pPartnerSearch'] . ')';
        } else {
            $filterPartner = '';
        }

        $sql = '
            SELECT  a.MemberDisplayID AS "Farmer ID",
                a.MemberID,
                    a.MemberName AS "Farmer Name", 
                    agent.AgentID, 
                    agent.AgentName, 
                    agent.AgentLatitude, 
                    agent.AgentLongitude,
                    b.PlotNr AS "Plantation Nr",
                    g.SurveyTxt AS "Survey Name",
                    b.DateCollection AS "Date Collection",
                    e.Province,
                    f.District,
                    d.`SubDistrict`,
                    c.`Village`,
                    b.GardenAreaHa,-- AS "Area of Plantation(Ha)",
                    b.GardenAreaPolygon,-- AS "Area of Garden Polygon (Ha)",
                    b.Latitude,-- AS "Latitude",
                    b.Longitude,-- AS "Longitude",
                    CASE
                        WHEN b.LandOwnershipType=1 THEN "Owned"
                        WHEN b.LandOwnershipType=2 THEN "Profit Sharing"
                        WHEN b.LandOwnershipType=3 THEN "Rented"
                        ELSE "Others"
                    END  AS "Land Ownership",
                    CASE
                        WHEN b.OwnerOfTheGarden=1 THEN "Registered Farmer"
                        WHEN b.OwnerOfTheGarden=2 THEN "Family Members"
                        WHEN b.OwnerOfTheGarden=3 THEN "Other People"
                        ELSE "Do Not Know "
                    END AS "Owner of the Garden",
                    b.OwnerOfPlantationNameText,-- AS "Owner of this Plantation - Name",
                    b.OwnerOfPlantationLocationText,-- AS "Owner of this Plantation - Location",
                    CASE 
                        WHEN b.OwnershipDoc=1 THEN "No Document"
                        WHEN b.OwnershipDoc=2 THEN "SKT (Surat Keterangan Tanah)"
                        WHEN b.OwnershipDoc=3 THEN "SHM (Sertifikat Hak Milik)/Certificate"
                        WHEN b.OwnershipDoc=4 THEN "HGU (Hak Guna Usaha)"
                        WHEN b.OwnershipDoc=5 THEN "SKGR (Surat Keterangan Ganti Rugi)" 
                        ELSE b.OwnershipDocText
                    END AS "Ownership Document",
                    IF(b.OwnerDocIsOwner=1, "Yes", IF(b.OwnerDocIsOwner=2, "No", "Do Not Know")) AS "Is the ownership document in the name of the current owner",
                    IF(b.HaveSTDB=1, "Yes", IF(b.HaveSTDB=2, "No", "Do Not Know")) AS "Does the farm have a STD-B (operational / business letter)",
                    IF(b.HaveSPPL=1, "Yes", IF(b.HaveSPPL=2, "No", "Do Not Know")) AS "Does the farm have a SPPL (Environmental Management Letter)",
                    IF(b.BusinessModel=1, "Independence", IF(b.BusinessModel=2, "Independent - Ex Plasma", "Plasma(has existing contract with plantation)")) AS "Business Model",
                    CASE
                        WHEN b.HowObPlantation=1 THEN "Inheritance"
                        WHEN b.HowObPlantation=2 THEN "Purchased"
                        WHEN b.HowObPlantation=3 THEN "Convert Existing Plantation"
                        WHEN b.HowObPlantation=4 THEN "Received From Government (Transmigrate)" 
                        ELSE b.HowObPlantationText
                    END AS "How did you obtain the plantation",
                    CASE
                        WHEN b.PlantationConditionEst=2 THEN "Secondary Veg/Fallow" 
                        WHEN b.PlantationConditionEst=2 THEN "Food Crops" 
                        WHEN b.PlantationConditionEst=3 THEN "Mangrove" 
                        WHEN b.PlantationConditionEst=4 THEN "Other Plantation (rubber, coffee, etc)" 
                        WHEN b.PlantationConditionEst=5 THEN "Oil Palm Plantation" 
                        WHEN b.PlantationConditionEst=6 THEN "Forest"  
                        ELSE "I dont know"
                    END AS "Condition when establishing oil palm plantation",
                    b.AverageAgeTree AS "Average age of trees on plantation (years)",
                    IF(b.SoilType=1, "Mineral", IF(b.SoilType=2, "Peat", IF(b.SoilType = 3, "Sandy", "-"))) AS "Soil Type",
                    IF(b.TopographyType=1, "Flat", IF(b.TopographyType=2, "Hilly", IF(b.TopographyType=3, "Mountainous", "-"))) AS "Type of Topography Plantation",
                    b.FirstPlantingYear AS "Year of first planting palm trees",
                    b.TreeTBM AS "TBM - Plants yet to produce",
                    b.TreeTM AS "TM - Producing plants",
                    b.TreeTR AS "TR - Old/diseased",
                    b.TreeTBM + b.TreeTM + b.TreeTR AS "Total Number of Trees",
                    IF(b.TypePlantMateMarihat=1, "Yes", "No") AS "Type of Planting Material - Marihat",
                    b.TypePlantMateMarihatNr AS "Type of Planting Material - Marihat Number Of Trees",
                    IF(b.TypePlantMateDumpy=1, "Yes", "No") AS "Type of Planting Material - Dumpy",
                    b.TypePlantMateDumpyNr AS "Type of Planting Material - Dumpy Number Of Trees",
                    IF(b.TypePlantMateLonsum=1, "Yes", "No") AS "Type of Planting Material - Lonsum",
                    b.TypePlantMateLonsumNr AS "Type of Planting Material - Lonsum Number Of Trees",
                    IF(b.TypePlantMateSimalungun=1, "Yes", "No") AS "Type of Planting Material - Simalungun",
                    b.TypePlantMateSimalungunNr AS "Type of Planting Material - Simalungun Number Of Trees",
                    IF(b.TypePlantMateDanimas=1, "Yes", "No") AS "Type of Planting Material - Dami Mas",
                    b.TypePlantMateDanimasNr AS "Type of Planting Material - Dami Mas Number Of Trees",
                    IF(b.TypePlantMateSriwijaya=1, "Yes", "No") AS "Type of Planting Material - Sriwijaya",
                    b.TypePlantMateSriwijayaNr AS "Type of Planting Material - Sriwijaya Number Of Trees",
                    IF(b.TypePlantMateSocfin=1, "Yes", "No") AS "Type of Planting Material - Socfin",
                    b.TypePlantMateSocfinNr AS "Type of Planting Material - Socfin Number Of Trees",
                    IF(b.TypePlantMateOther=1, b.TypePlantMateOtherText, "-") AS "Type of Planting Material - Other Method",
                    b.TypePlantMateOtherNr AS "Type of Planting Material - Other Method Number Of Trees",
                    IF(b.TypePlantMateDoNotKnow=1, "Yes", "No") AS "Type of Planting Material - Do Not Know",
                    b.TypePlantMateDoNotKnowNr AS "Type of Planting Material - Do Not Know Number Of Trees",
                    b.TreeTBM + b.TreeTM + b.TreeTR AS "Total Number of Oil Palm Trees",
                    b.HarvestRateDaysHighSeason AS "Harvest rate (once every how many days) in high season",
                    b.HarvestRateDaysLowSeason AS "Harvest rate (once every how many days) in low season",
                    b.AverageProdHighSeason AS "Average production per harvest (ton) in high season",
                    b.AverageProdLowSeason AS "Average production per harvest (ton) in low season",
                    b.NrHighSeasonMonths AS "Number of Months in High Season",
                    b.NrLowSeasonMonths AS "Number of Months in Low Season",
                    b.HighSeasonProduction AS "High Season Production (ton)",
                    b.LowSeasonProduction AS "Low Season Production (ton)",
                    b.AnnualProduction AS "Annual Production (ton)",
                    b.PlantationProductivity AS "Plantation Productivity (ton/ha)",
                    IF(b.LeanHarvestSeasonJan=1, "Yes", "No") AS "When is the lean harvest season for oil palm in your area - January",
                    IF(b.LeanHarvestSeasonFeb=1, "Yes", "No") AS "When is the lean harvest season for oil palm in your area - February",
                    IF(b.LeanHarvestSeasonMar=1, "Yes", "No") AS "When is the lean harvest season for oil palm in your area - March",
                    IF(b.LeanHarvestSeasonApr=1, "Yes", "No") AS "When is the lean harvest season for oil palm in your area - April",
                    IF(b.LeanHarvestSeasonMay=1, "Yes", "No") AS "When is the lean harvest season for oil palm in your area - May",
                    IF(b.LeanHarvestSeasonJun=1, "Yes", "No") AS "When is the lean harvest season for oil palm in your area - June",
                    IF(b.LeanHarvestSeasonJul=1, "Yes", "No") AS "When is the lean harvest season for oil palm in your area - July",
                    IF(b.LeanHarvestSeasonAug=1, "Yes", "No") AS "When is the lean harvest season for oil palm in your area - August",
                    IF(b.LeanHarvestSeasonSep=1, "Yes", "No") AS "When is the lean harvest season for oil palm in your area - September",
                    IF(b.LeanHarvestSeasonOct=1, "Yes", "No") AS "When is the lean harvest season for oil palm in your area - October",
                    IF(b.LeanHarvestSeasonNov=1, "Yes", "No") AS "When is the lean harvest season for oil palm in your area - November",
                    IF(b.LeanHarvestSeasonDec=1, "Yes", "No") AS "When is the lean harvest season for oil palm in your area - December",
                    IF(b.WhoHarvestFamily=1, "Yes", "No") AS "Who does the harvesting - Respondent and/or Family member",
                    IF(b.WhoHarvestLabor=1, "Yes", "No") AS "Who does the harvesting - Use of Hired Labor",
                    CASE
                    WHEN b.HowManyDiffBuyerSoldLastYear=1 THEN "1"
                    WHEN b.HowManyDiffBuyerSoldLastYear=1 THEN "2"
                    WHEN b.HowManyDiffBuyerSoldLastYear=1 THEN "3"
                    WHEN b.HowManyDiffBuyerSoldLastYear=1 THEN "4" 
                    ELSE "More than 4"
                    END AS "To how many different buyers have you sold your FFB from this plantation to within the past year",
                    CASE
                    WHEN b.HowManyDiffMillSoldLastYear=1 THEN "1"
                    WHEN b.HowManyDiffMillSoldLastYear=1 THEN "2"
                    WHEN b.HowManyDiffMillSoldLastYear=1 THEN "3"
                    WHEN b.HowManyDiffMillSoldLastYear=1 THEN "4" 
                    ELSE "More than 4"
                    END AS "How many different palm oil mills have you sold your FFB to within the past year",
                    b.Comment AS "Any comments about plantation",
                    ( SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = b.`CreatedBy` ) AS Enumerator
            FROM ktv_members a
            LEFT JOIN ktv_survey_plot b ON a.MemberID = b.MemberID
            LEFT JOIN ktv_village c ON b.VillageID = c.VillageID
            LEFT JOIN ktv_subdistrict d ON c.SubDistrictID = d.SubDistrictID
            LEFT JOIN ktv_district f ON d.DistrictID = f.DistrictID
            LEFT JOIN ktv_province e ON f.ProvinceID = e.ProvinceID
            LEFT JOIN ktv_survey g ON b.SurveyNr = g.SurveyNr
            LEFT JOIN (
                SELECT
                    a.MemberID,
                    b.SupplychainID,
                    GROUP_CONCAT( b2.MemberDisplayID SEPARATOR " | ") AgentID,
                    GROUP_CONCAT( b2.Alias SEPARATOR " | ") AgentName,
                    IF(GROUP_CONCAT( c.`Name` SEPARATOR " | ") <>"",GROUP_CONCAT(a.Latitude SEPARATOR " | "),"") AgentLatitude,
                    IF(GROUP_CONCAT( c.`Name` SEPARATOR " | ") <>"",GROUP_CONCAT(a.Longitude SEPARATOR " | "),"") AgentLongitude
                FROM
                    `ktv_members` a
                    LEFT JOIN ktv_tc_supplychain_farmer b ON b.FarmerID = a.MemberID
                    LEFT JOIN view_tc_supplychain_org c ON c.SupplychainID = b.SupplychainID 
                    LEFT JOIN ktv_members b2 ON b2.MemberID = c.ObjID
                WHERE
                    a.StatusCode = "active" 
                    AND b.StatusCode = "active" 
                    AND c.ObjType = "agent" 
                GROUP BY
                    a.MemberID
            ) agent on agent.MemberID = a.MemberID
            INNER JOIN (
                SELECT
                    sub_a.MemberID
                FROM
                    ktv_members sub_a
                    LEFT JOIN ktv_member_role sub_b ON sub_a.MemberID = sub_b.MemberID
                WHERE
                    sub_a.StatusCode = "active"
                    '.$sqlFilterRole.'
                GROUP BY sub_a.MemberID
            ) AS tmp_member_filter ON a.MemberID = tmp_member_filter.MemberID
            INNER JOIN (
                SELECT 
                    a.PartnerID,a.PartnerParentID,b.apmMemberID
                FROM ktv_program_partner a 
                LEFT JOIN ktv_access_partner_member b on a.PartnerID=b.apmPartnerID 
                '.$filterPartner.'
                GROUP BY b.apmMemberID
            ) AS tmp_partner ON a.MemberID = tmp_partner.apmMemberID
            LEFT JOIN ktv_member_role mrole ON a.MemberID = mrole.MemberID
            WHERE
                a.`StatusCode` = "active"
                AND b.`StatusCode` = "active"
                '.$sqlFilter.'
            GROUP BY a.MemberID, b.PlotNr
            ORDER BY
            a.MemberName ASC
        ';

        $query = $this->db->query($sql);
        // return $this->db->last_query();
        return $query->result_array();
    }

    public function getGardenPolygonData($dataGardens){
        $arrReturn = array();

        $increData = 0;
        for ($i=0; $i < count($dataGardens); $i++) {
            $sql="SELECT
                    a.`latitude` AS lat
                    , a.`longitude` AS lng
                FROM
                    ktv_survey_plot_polygon_sme a
                WHERE
                    a.`MemberID` = ?
                    AND a.`PlotNr` = ?
                    AND a.`SurveyNr` = ?
                    AND a.`StatusCheck` = 'verified'
                    AND a.`Revision` = (
                        SELECT
                            sub.Revision
                        FROM
                            ktv_survey_plot_polygon_sme sub
                        WHERE
                            sub.`MemberID` = ?
                            AND sub.`PlotNr` = ?
                            AND sub.`SurveyNr` = ?
                            AND sub.`StatusCheck` = 'verified'
                        ORDER BY sub.`Revision` DESC
                        LIMIT 1
                    )
                ORDER BY a.`Revision` ASC, a.`OrderNr` ASC";
            $p = array(
                $dataGardens[$i]['MemberID'],
                $dataGardens[$i]['PlotNr'],
                $dataGardens[$i]['SurveyNr'],
                $dataGardens[$i]['MemberID'],
                $dataGardens[$i]['PlotNr'],
                $dataGardens[$i]['SurveyNr']
            );
            $query = $this->db->query($sql, $p);
            $data = $query->result_array();

            if (!empty($data) && isset($data[0]['lat']) && $data[0]['lat'] !== "") {
                $arrReturn[$increData]['polygon_data'] = json_encode($data);
                //hilangkan petik biar bisa langsung dipakai di js
                $arrReturn[$increData]['polygon_data'] = str_replace('"','',$arrReturn[$increData]['polygon_data']);
                $arrReturn[$increData]['PlotNr'] = $dataGardens[$i]['PlotNr'];
                $increData++;
            }
        }

        return $arrReturn;
    }

    public function checkGardenCoordinateExist($dataGardens){
        $return = false;

        if($dataGardens[0]['PlotNr'] != ""){
            for ($i=0; $i < count($dataGardens); $i++) {
                if($dataGardens[$i]['Latitude'] != "" AND $dataGardens[$i]['Longitude'] != ""){
                    return true;
                }
            }
        }

        return $return;
    }

    public function getMemberDataDetail($MemberID) {
        $sql = "SELECT
                a.`MemberID`
                , a.`MemberDisplayID`
                , a.`DateCollection`
                , IFNULL(c.agCompanyName, a.MemberName) MemberName
                , a.`MemberName` AS MemberNameTtd
                , a.`DateOfBirth`
                , a.`Gender`
                , a.`MaritalStatus`
                , a.`Education`
                , SUBSTR(a.`VillageID`,1,2) AS ProvinceID
                , SUBSTR(a.`VillageID`,1,4) AS DistrictID
                , SUBSTR(a.`VillageID`,1,7) AS SubDistrictID
                , CONCAT(subd.`SubDistrict`,', ',vil.`Village`) AS MemberLocation
                , prov.CountryCode
                , prov.Province
                , dis.District
                , subd.SubDistrict
                , vil.Village
                , a.`VillageID`
                , a.`Address`
                , a.`RtRw`
                , a.`Handphone`
                , a.`Photo`
                , a.LearningContractStatus
                , a.LearningContractSign
                , a.WithdrawalConsentStatus
                , a.WithdrawalConsentSign
                , a.WillingnesSignature
                , a.WillingnesCommitSignature
                , a.PhotoDesc
                , a.`StatusMember`
                , a.`InactiveReason`
                , GROUP_CONCAT(b.`MRoleID` SEPARATOR ',')
                , GROUP_CONCAT(ref_role.`MRoleName` SEPARATOR ',') AS RoleLabel
                , a.Nin
                , a.inGroup
                , a.groupName
                , a.inCoop
                , a.CoopName
                , a.inGapoktan
                , a.GapoktanName
                , a.HowManyPlantation
                , a.BankBeneficiary
                , a.BankID
                , a.BankBranchName
                , a.BankAccNumber
                , c.`frRespondentName`
                , c.`frRelationToOwner`
                , c.`frRelationToOwnerText`
                , c.`frComment`
                , c.`frChildrenCount`
                , c.`frChildrenSchool`
                , c.`frChildrenWorkInFarm`
                , c.`frChildrenUnderAgeWork`
                , c.`frChildrenTypeOfWork`
                , c.agLegalStatusCompany
                , c.agCompanyName
                , c.agBusinessLocation
                , a.Latitude
                , a.Longitude
                , c.agYearEstablished
                , a.`MemberUID`
                , a.FarmerSignature
                , cert.Certification
            FROM
                ktv_members a
                LEFT JOIN ktv_member_role b ON a.`MemberID` = b.`MemberID`
                LEFT JOIN ktv_ref_member_role ref_role ON b.MRoleID = ref_role.MRoleID
                LEFT JOIN ktv_members_extension c ON a.MemberID = c.MemberID
                LEFT JOIN ktv_village vil ON a.`VillageID` = vil.`VillageID`
                LEFT JOIN ktv_subdistrict subd ON vil.`SubDistrictID` = subd.`SubDistrictID`
                LEFT JOIN ktv_district dis ON subd.DistrictID = dis.DistrictID
                LEFT JOIN ktv_province prov ON dis.ProvinceID = prov.ProvinceID
                LEFT JOIN ktv_certification cert on cert.FarmerID = a.MemberID
            WHERE
                a.`MemberID` = ?
            GROUP BY a.`MemberID`
            LIMIT 1";
        $query = $this->db->query($sql, array((int) $MemberID));
        $data = $query->row_array();
        $this->load->library('awsfileupload');
        
        if($data["WillingnesSignature"]){
            if($this->awsfileupload->doesObjectExist($data["WillingnesSignature"]) == true) {
                $data["WillingnesSignature"] = $this->config->item('CTCDN')."/".$data["WillingnesSignature"];
            }else{
                $data["WillingnesSignature"] = base_url().'/images/member/'.$data["Province"].'/'.$data["WillingnesSignature"];
            }
        }
        
        if($data["WillingnesCommitSignature"]){
            if($this->awsfileupload->doesObjectExist($data["WillingnesCommitSignature"]) == true) {
                $data["WillingnesCommitSignature"] = $this->config->item('CTCDN')."/".$data["WillingnesCommitSignature"];
            }else{
                $data["WillingnesCommitSignature"] = base_url().'/images/member/'.$data["Province"].'/'.$data["WillingnesCommitSignature"];
            }
        }
        
        if($data["WithdrawalConsentSign"]){
            if($this->awsfileupload->doesObjectExist($data["WithdrawalConsentSign"]) == true) {
                $data["WithdrawalConsentSign"] = $this->config->item('CTCDN')."/".$data["WithdrawalConsentSign"];
            }else{
                $data["WithdrawalConsentSign"] = base_url().'/images/member/'.$data["Province"].'/'.$data["WithdrawalConsentSign"];
            }
        }

        $return['success'] = true;
        $return['data'] = $data;
        return $return;
    }

    public function getMemberDataByUID($MemberUID){
        $sql = "SELECT
                a.`MemberID`
                , a.`MemberUID`
                , a.`MemberDisplayID`
                , a.`DateCollection`
                , a.`MemberName`
                , a.`DateOfBirth`
                , a.`Gender`
                , a.`MaritalStatus`
                , a.`Education`
                , SUBSTR(a.`VillageID`,1,2) AS ProvinceID
                , SUBSTR(a.`VillageID`,1,4) AS DistrictID
                , SUBSTR(a.`VillageID`,1,7) AS SubDistrictID
                , a.`VillageID`
                , a.`Address`
                , a.`RtRw`
                , a.`Handphone`
                , a.`Photo`
                , a.Photo
                , a.PhotoDesc
                , a.`StatusMember`
                , a.`InactiveReason`
                , GROUP_CONCAT(b.`MRoleID` SEPARATOR ',')
                , a.Nin
                , a.inGroup
                , a.groupName
                , a.inCoop
                , a.CoopName
                , a.inGapoktan
                , a.GapoktanName
                , a.HowManyPlantation
                , a.BankBeneficiary
                , a.BankID
                , a.BankBranchName
                , a.BankAccNumber
                , c.`frRespondentName`
                , c.`frRelationToOwner`
                , c.`frRelationToOwnerText`
                , c.`frComment`
                , c.`frChildrenCount`
                , c.`frChildrenSchool`
                , c.`frChildrenWorkInFarm`
                , c.`frChildrenUnderAgeWork`
                , c.`frChildrenTypeOfWork`
                , a.FarmerSignature
            FROM
                ktv_members a
                LEFT JOIN ktv_member_role b ON a.`MemberID` = b.`MemberID`
                LEFT JOIN ktv_members_extension c ON a.MemberID = c.MemberID
            WHERE
                a.`MemberUID` = ?
            GROUP BY a.`MemberID`
            LIMIT 1";
        $query = $this->db->query($sql, array($MemberUID));
        $data = $query->row_array();

        $return['success'] = true;
        $return['data'] = $data;
        return $return;
    }

    public function getMemberDataDetailByMemberDisplayID($MemberDisplayID) {
        $sql = "SELECT
                a.`MemberID`
                , a.`MemberUID`
                , a.`MemberDisplayID`
                , a.`DateCollection`
                , a.`MemberName`
                , a.`DateOfBirth`
                , a.`Gender`
                , a.`MaritalStatus`
                , a.`Education`
                , SUBSTR(a.`VillageID`,1,2) AS ProvinceID
                , SUBSTR(a.`VillageID`,1,4) AS DistrictID
                , SUBSTR(a.`VillageID`,1,7) AS SubDistrictID
                , a.`VillageID`
                , a.`Address`
                , a.`RtRw`
                , a.`Handphone`
                , a.`Photo`
                , a.Photo
                , a.PhotoDesc
                , a.`StatusMember`
                , a.`InactiveReason`
                , GROUP_CONCAT(b.`MRoleID` SEPARATOR ',')
                , a.Nin
                , a.inGroup
                , a.groupName
                , a.inCoop
                , a.CoopName
                , a.inGapoktan
                , a.GapoktanName
                , a.HowManyPlantation
                , a.BankBeneficiary
                , a.BankID
                , a.BankBranchName
                , a.BankAccNumber
                , c.`frRespondentName`
                , c.`frRelationToOwner`
                , c.`frRelationToOwnerText`
                , c.`frComment`
                , c.`frChildrenCount`
                , c.`frChildrenSchool`
                , c.`frChildrenWorkInFarm`
                , c.`frChildrenUnderAgeWork`
                , c.`frChildrenTypeOfWork`
            FROM
                ktv_members a
                LEFT JOIN ktv_member_role b ON a.`MemberID` = b.`MemberID`
                LEFT JOIN ktv_members_extension c ON a.MemberID = c.MemberID
            WHERE
                a.`MemberDisplayID` = ? OR a.`MemberID` = ?
            GROUP BY a.`MemberID`
            LIMIT 1";
        $query = $this->db->query($sql, array($MemberDisplayID, $MemberDisplayID));
        $data = $query->row_array();

        $return['success'] = true;
        $return['data'] = $data;
        return $return;
    }

    public function genMemberID($VillageID, $prefixId = 'F') {
        //MemberID
        $sql = "SELECT
                a.MemberID
            FROM
                ktv_members a
            ORDER BY a.`MemberID` DESC
            LIMIT 1";
        $query = $this->db->query($sql);
        $data = $query->row_array();
        if ($data['MemberID'] != "") {
            $return['MemberID'] = $data['MemberID'] + 1;
        } else {
            $return['MemberID'] = 1;
        }

        //MemberDisplayID
        $MemberID = $return['MemberID'];
        $IncMemberID = "";
        $awalan = $prefixId . substr($VillageID, 0, 4);

        //Gen Increment
        switch (strlen($MemberID)) {
            case '1':
                $IncMemberID = "00000000" . $MemberID;
                break;
            case '2':
                $IncMemberID = "0000000" . $MemberID;
                break;
            case '3':
                $IncMemberID = "000000" . $MemberID;
                break;
            case '4':
                $IncMemberID = "00000" . $MemberID;
                break;
            case '5':
                $IncMemberID = "0000" . $MemberID;
                break;
            case '6':
                $IncMemberID = "000" . $MemberID;
                break;
            case '7':
                $IncMemberID = "00" . $MemberID;
                break;
            case '8':
                $IncMemberID = "0" . $MemberID;
                break;
            default:
                $IncMemberID = $MemberID;
                break;
        }
        $return['MemberDisplayID'] = $awalan.$IncMemberID;

        //MemberUID
        $return['MemberUid'] = $this->getUID();

        return $return;
    }

    public function getPartnerMemberByDistrict($VillageID){
        $DistrictID = substr($VillageID,0,4);

        $sql="SELECT
                a.`PartnerID`
            FROM
                ktv_district_partner_member a
            WHERE
                a.`DistrictID` = ?
            LIMIT 1";
        $query = $this->db->query($sql,array($DistrictID));
        $data = $query->row_array();
        if(isset($data['PartnerID'])){
            return $data['PartnerID'];
        }else{
            return false;
        }
    }

    public function getPartnerSurveyByPartnerID($PartnerID){
        $sql="SELECT
                GROUP_CONCAT(a.`SurveyName` SEPARATOR ',') AS PartnerSurvey
            FROM
                ktv_program_partner_survey a
            WHERE
                a.`PartnerID` = ?";
        $query = $this->db->query($sql,array($PartnerID));
        $data = $query->row_array();
        return $data['PartnerSurvey'];
    }

    public function getUID(){        
        $Q = $this->db->query('SELECT random_string(11,null) AS uid');
        if($Q->num_rows() > 0) {
            $row = $Q->row();
            return $row->uid;
        }else{
            return '';
        }
    }

    public function insertMember($varPost) {
        $this->load->library('awsfileupload');
        $this->db->trans_begin();
        //rapikan variable post (begin)
        foreach ($varPost as $k => $v) {
            if ($varPost[$k] == "") {
                $varPost[$k] = null;
            }
        }
        if ($varPost['Koltiva_view_Grower_FormMainGrower-InactiveReason'] == "")
            $varPost['Koltiva_view_Grower_FormMainGrower-InactiveReason'] = null;
        //rapikan variable post (end)

        $varPost['Koltiva_view_Grower_FormMainGrower-frChildrenCount'] = $this->_numOrNull($varPost['Koltiva_view_Grower_FormMainGrower-frChildrenCount']);
        // numericfield groups thousands with commas (e.g. "1,000"); strip them and
        // turn blanks into NULL so the integer column accepts the value.
        $varPost['Koltiva_view_Grower_FormMainGrower-HowManyPlot'] = $this->_numOrNull($varPost['Koltiva_view_Grower_FormMainGrower-HowManyPlot']);

        //generate MemberID dan MemberDisplayID
        $id = $this->genMemberID($varPost['Koltiva_view_Grower_FormMainGrower-Village'], 'F');

        //get Partner Member
        $PartnerID = $_SESSION["PartnerID"];

        $PartnerSurvey = $this->getPartnerSurveyByPartnerID($PartnerID);

        //Get apakah GAR/WAGS
        $sql = "SELECT
                    prov.`CountryCode`
                FROM
                    ktv_village vil
                    LEFT JOIN ktv_subdistrict subd ON vil.`SubDistrictID` = subd.`SubDistrictID`
                    LEFT JOIN ktv_district dis ON subd.`DistrictID` = dis.`DistrictID`
                    LEFT JOIN ktv_province prov ON dis.`ProvinceID` = prov.`ProvinceID`
                WHERE
                    vil.`VillageID` = ?
                LIMIT 1";
        $DataCountry = $this->db->query($sql,array($varPost['Koltiva_view_Grower_FormMainGrower-Village']))->row_array();
        $CountryCode = $DataCountry['CountryCode'];
        $uid = $this->getUID();

        //ktv_members
        $p = array(
            $id['MemberID'],
            $uid,
            $uid,
            $id['MemberDisplayID'],
            $varPost['Koltiva_view_Grower_FormMainGrower-Fullname'],
            $varPost['Koltiva_view_Grower_FormMainGrower-DateCollection'],
            $varPost['Koltiva_view_Grower_FormMainGrower-DateOfBirth'],
            $varPost['Koltiva_view_Grower_FormMainGrower-Gender'],
            $varPost['Koltiva_view_Grower_FormMainGrower-PhotoDesc'],
            $varPost['Koltiva_view_Grower_FormMainGrower-MaritalStatus'],
            $varPost['Koltiva_view_Grower_FormMainGrower-Education'],
            $varPost['Koltiva_view_Grower_FormMainGrower-Village'],
            $varPost['Koltiva_view_Grower_FormMainGrower-Address'],
            $varPost['Koltiva_view_Grower_FormMainGrower-RtRw'],
            $varPost['Koltiva_view_Grower_FormMainGrower-Handphone'],
            $varPost['Koltiva_view_Grower_FormMainGrower-HandphoneCode'],
            $varPost['Koltiva_view_Grower_FormMainGrower-HandphoneType'],
            $varPost['Koltiva_view_Grower_FormMainGrower-AccessToSmartphone'],
            $varPost['Koltiva_view_Grower_FormMainGrower-Nin'],
            $varPost['Koltiva_view_Grower_FormMainGrower-inGroup'],
            $varPost['Koltiva_view_Grower_FormMainGrower-groupName'],
            $varPost['Koltiva_view_Grower_FormMainGrower-FarmerGroupID'],
            $varPost['Koltiva_view_Grower_FormMainGrower-inCoop'],
            $varPost['Koltiva_view_Grower_FormMainGrower-CoopName'],
            $varPost['Koltiva_view_Grower_FormMainGrower-inGapoktan'],
            $varPost['Koltiva_view_Grower_FormMainGrower-GapoktanName'],
            $varPost['Koltiva_view_Grower_FormMainGrower-ExtID'],
            $varPost['Koltiva_view_Grower_FormMainGrower-CategoryFarmer'],
            $varPost['Koltiva_view_Grower_FormMainGrower-TotalProductionArea'],
            $varPost['Koltiva_view_Grower_FormMainGrower-MembershipStatus'],
            $varPost['Koltiva_view_Grower_FormMainGrower-FarmerGroupWAGSID'],
            $varPost['Koltiva_view_Grower_FormMainGrower-HowManyPlot'],
            $varPost['Koltiva_view_Grower_FormMainGrower-WorkInPlot'],
            $varPost['Koltiva_view_Grower_FormMainGrower-UseAPD'],
            $varPost['Koltiva_view_Grower_FormMainGrower-HadAccident'],
            $varPost['Koltiva_view_Grower_FormMainGrower-WhatAccident'],
            $varPost['Koltiva_view_Grower_FormMainGrower-HaveBPJS'],
            $varPost['Koltiva_view_Grower_FormMainGrower-SupplybaseType'],
            $varPost['Koltiva_view_Grower_FormMainGrower-isCertified'],
            $varPost['Koltiva_view_Grower_FormMainGrower-CertificationRSPO'],
            $varPost['Koltiva_view_Grower_FormMainGrower-CertificationISCC'],
            $varPost['Koltiva_view_Grower_FormMainGrower-CertificationISPO'],
            $varPost['Koltiva_view_Grower_FormMainGrower-CertificationMSPO'],
            $varPost['Koltiva_view_Grower_FormMainGrower-ReceiveTraining'],
            $varPost['Koltiva_view_Grower_FormMainGrower-CertificationSourceGovernment'],
            $varPost['Koltiva_view_Grower_FormMainGrower-CertificationSourceNGO'],
            $varPost['Koltiva_view_Grower_FormMainGrower-CertificationSourceMill'],
            $varPost['Koltiva_view_Grower_FormMainGrower-CertificationSourcePrivateOrg'],
            $varPost['Koltiva_view_Grower_FormMainGrower-CertificationSourceOthers'],
            $varPost['Koltiva_view_Grower_FormMainGrower-CertificationTypeFinancial'],
            $varPost['Koltiva_view_Grower_FormMainGrower-CertificationTypeGoodAgriculture'],
            $varPost['Koltiva_view_Grower_FormMainGrower-CertificationTypeHumanRights'],
            $varPost['Koltiva_view_Grower_FormMainGrower-CertificationTypeManagementPesticides'],
            $varPost['Koltiva_view_Grower_FormMainGrower-CertificationTypeFireFighting'],
            $varPost['Koltiva_view_Grower_FormMainGrower-CertificationTypeHCVHCS'],
            $varPost['Koltiva_view_Grower_FormMainGrower-CertificationTypeRSPOIndependent'],
            $varPost['Koltiva_view_Grower_FormMainGrower-WillingnesParticipate'],
            $varPost['Koltiva_view_Grower_FormMainGrower-WillingnesCommit'],
            $varPost['Koltiva_view_Grower_FormMainGrower-JoinProgram'],
            $varPost['Koltiva_view_Grower_FormMainGrower-NotJoinProgramReason'],
            $varPost['Koltiva_view_Grower_FormMainGrower-NotJoinProgramReasonText'],
            $varPost['Koltiva_view_Grower_FormMainGrower-JoinComment'],
            $varPost['Koltiva_view_Grower_FormMainGrower-StatusMember'],
            $varPost['Koltiva_view_Grower_FormMainGrower-InactiveReason'],
            $varPost['Koltiva_view_Grower_FormMainGrower-InactiveReasonText'],
            $varPost['Koltiva_view_Grower_FormMainGrower-StoppedReason'],
            $varPost['Koltiva_view_Grower_FormMainGrower-StoppedReasonText'],
            $varPost['Koltiva_view_Grower_FormMainGrower-AccidentKnife'],
            $varPost['Koltiva_view_Grower_FormMainGrower-AccidentHitbyFruit'],
            $varPost['Koltiva_view_Grower_FormMainGrower-AccidentContimination'],
            $varPost['Koltiva_view_Grower_FormMainGrower-AccidentOther'],
            $varPost['Koltiva_view_Grower_FormMainGrower-AccidentOtherText'],
            $varPost['Koltiva_view_Grower_FormMainGrower-DateLastVerfication'],
            $varPost['Koltiva_view_Grower_FormMainGrower-HaveBankAccount'],
            $varPost['Koltiva_view_Grower_FormMainGrower-ReceiveBankTransfer'],
            $varPost['Koltiva_view_Grower_FormMainGrower-BankHolderName'],
            $varPost['Koltiva_view_Grower_FormMainGrower-BankAccNumber'],
            $varPost['Koltiva_view_Grower_FormMainGrower-BankID'],
            $varPost['Koltiva_view_Grower_FormMainGrower-BankClientID'],
            $varPost['Koltiva_view_Grower_FormMainGrower-BankBranchName'],
            $varPost['Koltiva_view_Grower_FormMainGrower-AccountHolderRelation'],
            $varPost['Koltiva_view_Grower_FormMainGrower-SurveyNr'],
            $varPost['Koltiva_view_Grower_FormMainGrower-HaveOtherCommodities'],
            $PartnerID,
            $_SESSION['userid']
        );
        $sql = "INSERT INTO `ktv_members` SET
                `MemberID` = ?,
                MemberUID = ?,
                `uid` = ?,
                `MemberDisplayID` = ?,
                `MemberName` = ?,
                `DateCollection` = ?,
                `DateOfBirth` = ?,
                Gender = ?,
                PhotoDesc = ?,
                `MaritalStatus` = ?,
                `Education` = ?,
                `VillageID` = ?,
                `Address` = ?,
                `RtRw` = ?,
                `Handphone` = ?,
                `HandphoneCode` = ?,
                `HandphoneType` = ?,
                `AccessToSmartphone` = ?,
                Nin = ?,
                inGroup = ?,
                groupName = ?,
                FarmerGroupID = ?,
                inCoop = ?,
                CoopName = ?,
                inGapoktan = ?,
                GapoktanName = ?,
                ExtID = ?,
                CategoryFarmer = ?,
                TotalProductionArea = ?,
                MembershipStatus = ?,
                FarmerGroupWAGSID = ?,
                HowManyPlot = ?,
                WorkInPlot = ?,
                UseAPD = ?,
                HadAccident = ?,
                WhatAccident = ?,
                HaveBPJS = ?,
                SupplybaseType = ?,
                isCertified = ?,
                CertificationRSPO = ?,
                CertificationISCC = ?,
                CertificationISPO = ?,
                CertificationMSPO = ?,
                ReceiveTraining = ?,
                CertificationSourceGovernment = ?,
                CertificationSourceNGO = ?,
                CertificationSourceMill = ?,
                CertificationSourcePrivateOrg = ?,
                CertificationSourceOthers = ?,
                CertificationTypeFinancial = ?,
                CertificationTypeGoodAgriculture = ?,
                CertificationTypeHumanRights = ?,
                CertificationTypeManagementPesticides = ?,
                CertificationTypeFireFighting = ?,
                CertificationTypeHCVHCS = ?,
                CertificationTypeRSPOIndependent = ?,
                WillingnesParticipate = ?,
                WillingnesCommit = ?,
                JoinProgram = ?,
                NotJoinProgramReason = ?,
                NotJoinProgramReasonText = ?,
                JoinComment = ?,
                StatusMember = ?,
                InactiveReason = ?,
                InactiveReasonText = ?,
                StoppedReason = ?,
                StoppedReasonText = ?,
                AccidentKnife = ?,
                AccidentHitbyFruit = ?,
                AccidentContimination = ?,
                AccidentOther = ?,
                AccidentOtherText = ?,
                DateLastVerfication = ?,
                HaveBankAccount = ?,
                ReceiveBankTransfer = ?,
                BankHolderName = ?,
                BankAccNumber = ?,
                BankID = ?,
                BankClientID = ?,
                BankBranchName = ?,
                AccountHolderRelation = ?,
                SurveyNr = ?,
                HaveOtherCommodities = ?,
                PartnerID = ?,
                `DateCreated` = NOW(),
                `CreatedBy` = ?";
        $query = $this->db->query($sql, $p);


        $sql3 = "INSERT INTO
                ktv_members_relation
            SET
            `MemberID` = ?,
            `ObjType` = 'agent',
            `ObjID` = ?,
            `DateCreated` = NOW(),
            `CreatedBy` = ?
        ";

        $p3 = array(
            $id['MemberID'],
            $varPost['Koltiva_view_Grower_FormMainGrower-DealerAssign'],
            $_SESSION['userid']
        );
        $query3 = $this->db->query($sql3, $p3);

        //ktv_members_extension
        $sql = "INSERT INTO `ktv_members_extension` SET
                `MemberID` = ?,
                `uid` = ?,
                `frRespondentName` = ?,
                `frRelationToOwner` = ?,
                `frRelationToOwnerText` = ?,
                `frComment` = ?,
                `frChildrenCount` = ?,
                `frChildrenSchool` = ?,
                `frChildrenWorkInFarm` = ?,
                `frChildrenUnderAgeWork` = ?,
                `frChildrenTypeOfWork` = ?,
                `DateCreated` = NOW(),
                `CreatedBy` = ?,
                `SurveyNr` = ?
            ";
        $p = array(
            $id['MemberID'],
            $uid,
            $varPost['Koltiva_view_Grower_FormMainGrower-frRespondentName'],
            $varPost['Koltiva_view_Grower_FormMainGrower-frRelationToOwner'],
            $varPost['Koltiva_view_Grower_FormMainGrower-frRelationToOwnerText'],
            $varPost['Koltiva_view_Grower_FormMainGrower-frComment'],
            $varPost['Koltiva_view_Grower_FormMainGrower-frChildrenCount'],
            $varPost['Koltiva_view_Grower_FormMainGrower-frChildrenSchool'],
            $varPost['Koltiva_view_Grower_FormMainGrower-frChildrenWorkInFarm'],
            $varPost['Koltiva_view_Grower_FormMainGrower-frChildrenUnderAgeWork'],
            $varPost['Koltiva_view_Grower_FormMainGrower-frChildrenTypeOfWork'],
            $_SESSION['userid'],
            $varPost['Koltiva_view_Grower_FormMainGrower-SurveyNr']
        );
        $query = $this->db->query($sql, $p);

        //insert member role (begin)
        $arrRole = array();
        //if($varPost['Koltiva_view_Grower_FormMainGrower-CbRolePlanter'] == "1") $arrRole[] = 1;
        $arrRole[] = 1;

        foreach ($arrRole as $key => $value) {
            $sql = "INSERT INTO `ktv_member_role` SET
                `MemberID` = ?,
                `MRoleID` = ?,
                `DateCreated` = NOW(),
                `CreatedBy` = ?";
            $p = array(
                $id['MemberID'],
                $value,
                $_SESSION['userid']
            );
            $query = $this->db->query($sql, $p);
        }
        //insert member role (end)

        $arrGrowerInternalProgram = array();
        $arrGrowerInternalProgram = $varPost['Koltiva_view_Grower_FormMainGrower-CmbInternalProgram'];
        if(!empty($arrGrowerInternalProgram)){
            foreach($arrGrowerInternalProgram as $k => $GrowerInternalProgramID){
                $sqlInternalProgram="INSERT INTO `ktv_members_business_unit` SET
                    `MemberID` = ?,
                    `BusinessUnitID` = ?,
                    `DateCreated` = NOW(),
                    `CreatedBy` = ?";
                $pInternalProgram = array(
                    $id['MemberID'],
                    $GrowerInternalProgramID,
                    $_SESSION['userid']
                );
                $this->db->query($sqlInternalProgram,$pInternalProgram);
            }
           
        }
        //insert internal program ======================================================================== (end)

        $arrMillExternalProgram = array();
        $arrMillExternalProgram = $varPost['Koltiva_view_Grower_FormMainGrower-CmbExternalProgram'];
        if(!empty($arrMillExternalProgram)){
            foreach($arrMillExternalProgram as $k => $MillExternalProgramID){
                $sqlExternalProgram="INSERT INTO `ktv_members_business_unit` SET
                    `MemberID` = ?,
                    `BusinessUnitID` = ?,
                    `DateCreated` = NOW(),
                    `CreatedBy` = ?";
                $pExternalProgram = array(
                    $id['MemberID'],
                    $MillExternalProgramID,
                    $_SESSION['userid']
                );
                $this->db->query($sqlExternalProgram,$pExternalProgram);
            }
           
        }
        //insert external program ======================================================================== (end)

        //insert hak akses data control (Begin)

            //beri akses ke partner pembuat DAN seluruh induknya (PartnerParentID),
            //kalau tidak member baru tidak muncul di grid partner-nya / induknya.
            $this->_grantPartnerChainAccess($id['MemberID'], $_SESSION['PartnerID']);

            $sql = "SELECT
                PartnerIDRef
            FROM
                ktv_partner_access_setting
            WHERE
                PartnerIDCanView = ? AND PartnerIDRef <> 1
            GROUP BY
                PartnerIDRef";
            $query = $this->db->query($sql, array($_SESSION["PartnerID"]));

            if($query->num_rows()>0){
                foreach($query->result_array() as $rows){
                    $sql = "INSERT INTO `ktv_access_partner_member` SET
                            `apmPartnerID` = ?,
                            `apmMemberID` = ?,
                            `DateCreated` = NOW(),
                            `CreatedBy` = ?";
                    $p = array(
                        $rows["PartnerIDRef"],
                        $id['MemberID'],
                        $_SESSION['userid']
                    );
                    $query = $this->db->query($sql, $p);
                }
            }

            //cek kalau bukan Partner Koltiva, maka ditambahkan juga ke Partner Koltiva
            if ($_SESSION['PartnerID'] != "1") {
                //insertkan ke Koltiva
                $sql = "INSERT INTO `ktv_access_partner_member` SET
                        `apmPartnerID` = ?,
                        `apmMemberID` = ?,
                        `DateCreated` = NOW(),
                        `CreatedBy` = ?
                        ON DUPLICATE KEY UPDATE `apmID` = `apmID`";
                $p = array(
                    '1',
                    $id['MemberID'],
                    $_SESSION['userid']
                );
                $query = $this->db->query($sql, $p);
            }
        //insert hak akses data control (End)

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Data saved";
            $results['MemberIDInc'] = $id['MemberID'];
            $results['PartnerSurvey'] = $PartnerSurvey;
            $results['CountryCode'] = $CountryCode;

            //apakah ada fotonya, kalau ada dipindahkan dan diupdate            

            //Proses Photo ============= (Begin)
            if ($varPost['Koltiva_view_Grower_FormMainGrower-MemberPhotoOld'] != "") {
                $Photo = "";
                $file = explode("/images/member/temp/",$varPost['Koltiva_view_Grower_FormMainGrower-MemberPhotoOld']);
                //Insert ada photonya pakai aws
                if(file_exists('images/member/temp/'.$file[1])) {
                    $this->load->library('awsfileupload');
                    $upload = $this->awsfileupload->upload('images/member/temp/'.$file[1],$file[1],AWSS3_FARMER_PATH, 'images');
                    if ($upload['success'] == true) {
                        delete_file($varPost['Koltiva_view_Grower_FormMainGrower-MemberPhotoOld']);
                        $Photo = $upload['filenamepath'];
                    }
                }

                $sql = "UPDATE ktv_members a SET
                        a.`Photo` = ?
                    WHERE
                        a.`MemberID` = ?
                    LIMIT 1";
                $p = array(
                    $Photo,
                    $id['MemberID']
                );
                $query = $this->db->query($sql, $p);
            }

            if ($varPost['Koltiva_view_Grower_FormMainGrower-KTPPhotoOld'] != "") {
                $KTPFile = "";
                $file = explode("/images/member/temp/ktp/",$varPost['Koltiva_view_Grower_FormMainGrower-KTPPhotoOld']);
                //Insert ada photonya pakai aws
                if(file_exists('images/member/temp/ktp/'.$file[1])) {
                    $this->load->library('awsfileupload');
                    $upload = $this->awsfileupload->upload('images/member/temp/ktp/'.$file[1],$file[1],AWSS3_FARMER_KTP_PATH, 'images');
                    if ($upload['success'] == true) {
                        delete_file($varPost['Koltiva_view_Grower_FormMainGrower-KTPPhotoOld']);
                        $KTPFile = $upload['filenamepath'];
                    }
                }

                $sql = "UPDATE ktv_members a SET
                        a.`KTPFile` = ?
                    WHERE
                        a.`MemberID` = ?
                    LIMIT 1";
                $p = array(
                    $KTPFile,
                    $id['MemberID']
                );
                $query = $this->db->query($sql, $p);
            }

            //apakah ada foto consent notes
            if ($varPost['Koltiva_view_Grower_FormMainGrower-LearningContractSignOld'] != "") {
                //get ext nya..
                $arrTemp = explode(".", $varPost['Koltiva_view_Grower_FormMainGrower-LearningContractSignOld']);
                $extNya = array_values(array_slice($arrTemp, -1))[0];
                $namaFileGambar = $id['MemberDisplayID'] . "." . $extNya;
                $ProvinceID = substr($varPost['Koltiva_view_Grower_FormMainGrower-Village'], 0, 2);

                //foto dipisah perdirectory ProvinceID, cek apakah folder tempat nyimpan foto sudah ada
                if (!file_exists('images/consent/' . $ProvinceID)) {
                    mkdir('images/consent/' . $ProvinceID, 0777, true);
                }
                $gambarTujuan = 'images/consent/' . $ProvinceID . '/' . $id['MemberDisplayID'] . "." . $extNya;

                rename('images/consent/' . $varPost['Koltiva_view_Grower_FormMainGrower-LearningContractSignOld'], $gambarTujuan);

                $sql = "UPDATE ktv_members a SET
                        a.`LearningContractStatus` = '1',
                        a.LearningContractSign = ?
                    WHERE
                        a.`MemberID` = ?
                    LIMIT 1";
                $p = array(
                    $namaFileGambar,
                    $id['MemberID']
                );
                $query = $this->db->query($sql, $p);
            }
        }

        return $results;
    }

    public function insertMemberSME($varPost) {
        $this->load->library('awsfileupload');
        $this->db->trans_begin();
        //rapikan variable post (begin)
        foreach ($varPost as $k => $v) {
            if ($varPost[$k] == "") {
                $varPost[$k] = null;
            }
        }
        if ($varPost['Koltiva_view_GrowerSME_FormMainGrower-InactiveReason'] == "")
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-InactiveReason'] = null;
        //rapikan variable post (end)

        $varPost['Koltiva_view_GrowerSME_FormMainGrower-frChildrenCount'] = $this->_numOrNull($varPost['Koltiva_view_GrowerSME_FormMainGrower-frChildrenCount']);
        // numericfield groups thousands with commas (e.g. "1,000"); strip them and
        // turn blanks into NULL so the integer column accepts the value.
        $varPost['Koltiva_view_GrowerSME_FormMainGrower-HowManyPlot'] = $this->_numOrNull($varPost['Koltiva_view_GrowerSME_FormMainGrower-HowManyPlot']);

        //generate MemberID dan MemberDisplayID
        $id = $this->genMemberID($varPost['Koltiva_view_GrowerSME_FormMainGrower-Village'], 'F');

        //get Partner Member
        $PartnerID = $this->getPartnerMemberByDistrict($varPost['Koltiva_view_GrowerSME_FormMainGrower-Village']);
        if($PartnerID == false){
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "No Partner assign to this farmer district yet";
            return $results;
        }
        $PartnerSurvey = $this->getPartnerSurveyByPartnerID($PartnerID);

        //Get apakah GAR/WAGS
        $sql = "SELECT
                    prov.`CountryCode`
                FROM
                    ktv_village vil
                    LEFT JOIN ktv_subdistrict subd ON vil.`SubDistrictID` = subd.`SubDistrictID`
                    LEFT JOIN ktv_district dis ON subd.`DistrictID` = dis.`DistrictID`
                    LEFT JOIN ktv_province prov ON dis.`ProvinceID` = prov.`ProvinceID`
                WHERE
                    vil.`VillageID` = ?
                LIMIT 1";
        $DataCountry = $this->db->query($sql,array($varPost['Koltiva_view_GrowerSME_FormMainGrower-Village']))->row_array();
        $CountryCode = $DataCountry['CountryCode'];


        //ktv_members
        $p = array(
            $id['MemberID'],
            $id['MemberUid'],
            $id['MemberDisplayID'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-Fullname'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-DateCollection'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-DateOfBirth'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-Gender'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-PhotoDesc'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-MaritalStatus'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-Education'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-Village'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-Address'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-RtRw'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-Handphone'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-HandphoneType'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-AccessToSmartphone'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-Nin'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-inGroup'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-groupName'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-FarmerGroupID'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-inCoop'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-CoopName'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-inGapoktan'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-GapoktanName'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-ExtID'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-CategoryFarmer'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-TotalProductionArea'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-MembershipStatus'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-FarmerGroupWAGSID'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-HowManyPlot'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-WorkInPlot'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-UseAPD'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-HadAccident'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-WhatAccident'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-HaveBPJS'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-SupplybaseType'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-isCertified'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-CertificationRSPO'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-CertificationISCC'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-CertificationISPO'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-CertificationMSPO'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-ReceiveTraining'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-CertificationSourceGovernment'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-CertificationSourceNGO'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-CertificationSourceMill'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-CertificationSourcePrivateOrg'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-CertificationSourceOthers'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-CertificationTypeFinancial'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-CertificationTypeGoodAgriculture'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-CertificationTypeHumanRights'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-CertificationTypeManagementPesticides'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-CertificationTypeFireFighting'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-CertificationTypeHCVHCS'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-CertificationTypeRSPOIndependent'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-WillingnesParticipate'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-WillingnesCommit'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-JoinProgram'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-NotJoinProgramReason'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-NotJoinProgramReasonText'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-JoinComment'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-StatusMember'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-InactiveReason'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-InactiveReasonText'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-StoppedReason'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-StoppedReasonText'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-AccidentKnife'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-AccidentHitbyFruit'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-AccidentContimination'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-AccidentOther'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-DateLastVerfication'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-HaveBankAccount'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-ReceiveBankTransfer'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-BankHolderName'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-BankAccNumber'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-BankID'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-BankClientID'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-BankBranchName'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-AccountHolderRelation'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-SurveyNr'],
            $PartnerID,
            $_SESSION['userid']
        );
        $sql = "INSERT INTO `ktv_members` SET
                `MemberID` = ?,
                MemberUID = ?,
                `MemberDisplayID` = ?,
                `MemberName` = ?,
                `DateCollection` = ?,
                `DateOfBirth` = ?,
                Gender = ?,
                PhotoDesc = ?,
                `MaritalStatus` = ?,
                `Education` = ?,
                `VillageID` = ?,
                `Address` = ?,
                `RtRw` = ?,
                `Handphone` = ?,
                `HandphoneType` = ?,
                `AccessToSmartphone` = ?,
                Nin = ?,
                inGroup = ?,
                groupName = ?,
                FarmerGroupID = ?,
                inCoop = ?,
                CoopName = ?,
                inGapoktan = ?,
                GapoktanName = ?,
                ExtID = ?,
                CategoryFarmer = ?,
                TotalProductionArea = ?,
                MembershipStatus = ?,
                FarmerGroupWAGSID = ?,
                HowManyPlot = ?,
                WorkInPlot = ?,
                UseAPD = ?,
                HadAccident = ?,
                WhatAccident = ?,
                HaveBPJS = ?,
                SupplybaseType = ?,
                isCertified = ?,
                CertificationRSPO = ?,
                CertificationISCC = ?,
                CertificationISPO = ?,
                CertificationMSPO = ?,
                ReceiveTraining = ?,
                CertificationSourceGovernment = ?,
                CertificationSourceNGO = ?,
                CertificationSourceMill = ?,
                CertificationSourcePrivateOrg = ?,
                CertificationSourceOthers = ?,
                CertificationTypeFinancial = ?,
                CertificationTypeGoodAgriculture = ?,
                CertificationTypeHumanRights = ?,
                CertificationTypeManagementPesticides = ?,
                CertificationTypeFireFighting = ?,
                CertificationTypeHCVHCS = ?,
                CertificationTypeRSPOIndependent = ?,
                WillingnesParticipate = ?,
                WillingnesCommit = ?,
                JoinProgram = ?,
                NotJoinProgramReason = ?,
                NotJoinProgramReasonText = ?,
                JoinComment = ?,
                StatusMember = ?,
                InactiveReason = ?,
                InactiveReasonText = ?,
                StoppedReason = ?,
                StoppedReasonText = ?,
                AccidentKnife = ?,
                AccidentHitbyFruit = ?,
                AccidentContimination = ?,
                AccidentOther = ?,
                DateLastVerfication = ?,
                HaveBankAccount = ?,
                ReceiveBankTransfer = ?,
                BankHolderName = ?,
                BankAccNumber = ?,
                BankID = ?,
                BankClientID = ?,
                BankBranchName = ?,
                AccountHolderRelation = ?,
                SurveyNr = ?,
                PartnerID = ?,
                `DateCreated` = NOW(),
                `CreatedBy` = ?";
        $query = $this->db->query($sql, $p);


        $sql3 = "INSERT INTO
                ktv_members_relation
            SET
            `MemberID` = ?,
            `ObjType` = 'agent',
            `ObjID` = ?,
            `DateCreated` = NOW(),
            `CreatedBy` = ?
        ";

        $p3 = array(
            $id['MemberID'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-DealerAssign'],
            $_SESSION['userid']
        );
        $query3 = $this->db->query($sql3, $p3);


        $dataPostFarmer['FarmerID']         = $id['MemberID'];
        $dataPostFarmer['SupplychainID']    = $_SESSION['SupplychainID'];
        $dataPostFarmer['DateStart']        = $varPost['Koltiva_view_GrowerSME_FormMainGrower-DateStart'];
        $dataPostFarmer['DateEnd']          = $varPost['Koltiva_view_GrowerSME_FormMainGrower-DateEnd'];
        $dataPostFarmer['StatusCode']       = 'active';
        $dataPostFarmer['CreatedBy']        = $_SESSION['userid'];
        $dataPostFarmer['DateCreated']      = date('Y-m-d H:i:s');

        $this->db->insert('ktv_tc_supplychain_farmer',$dataPostFarmer);

        //ktv_members_extension
        $sql = "INSERT INTO `ktv_members_extension` SET
                `MemberID` = ?,
                `frRespondentName` = ?,
                `frRelationToOwner` = ?,
                `frRelationToOwnerText` = ?,
                `frComment` = ?,
                `frChildrenCount` = ?,
                `frChildrenSchool` = ?,
                `frChildrenWorkInFarm` = ?,
                `frChildrenUnderAgeWork` = ?,
                `frChildrenTypeOfWork` = ?,
                `DateCreated` = NOW(),
                `CreatedBy` = ?
            ";
        $p = array(
            $id['MemberID'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-frRespondentName'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-frRelationToOwner'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-frRelationToOwnerText'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-frComment'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-frChildrenCount'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-frChildrenSchool'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-frChildrenWorkInFarm'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-frChildrenUnderAgeWork'],
            $varPost['Koltiva_view_GrowerSME_FormMainGrower-frChildrenTypeOfWork'],
            $_SESSION['userid']
        );
        $query = $this->db->query($sql, $p);

        //insert member role (begin)
        $arrRole = array();
        //if($varPost['Koltiva_view_GrowerSME_FormMainGrower-CbRolePlanter'] == "1") $arrRole[] = 1;
        $arrRole[] = 1;

        foreach ($arrRole as $key => $value) {
            $sql = "INSERT INTO `ktv_member_role` SET
                `MemberID` = ?,
                `MRoleID` = ?,
                `DateCreated` = NOW(),
                `CreatedBy` = ?";
            $p = array(
                $id['MemberID'],
                $value,
                $_SESSION['userid']
            );
            $query = $this->db->query($sql, $p);
        }
        //insert member role (end)

        //insert hak akses data control (Begin)
        if ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program" || $_SESSION['role'] == "SME") {
            $sql = "INSERT INTO `ktv_access_partner_member` SET
                    `apmPartnerID` = ?,
                    `apmMemberID` = ?,
                    `DateCreated` = NOW(),
                    `CreatedBy` = ?";
            $p = array(
                $_SESSION['PartnerID'],
                $id['MemberID'],
                $_SESSION['userid']
            );
            $query = $this->db->query($sql, $p);

            //cek kalau bukan Partner Koltiva, maka ditambahkan juga ke Partner Koltiva
            if ($_SESSION['PartnerID'] != "1") {
                //insertkan ke Koltiva
                $sql = "INSERT INTO `ktv_access_partner_member` SET
                        `apmPartnerID` = ?,
                        `apmMemberID` = ?,
                        `DateCreated` = NOW(),
                        `CreatedBy` = ?";
                $p = array(
                    '1',
                    $id['MemberID'],
                    $_SESSION['userid']
                );
                $query = $this->db->query($sql, $p);
            }
        } else {
            //insertkan ke Koltiva
            $sql = "INSERT INTO `ktv_access_partner_member` SET
                    `apmPartnerID` = ?,
                    `apmMemberID` = ?,
                    `DateCreated` = NOW(),
                    `CreatedBy` = ?";
            $p = array(
                '1',
                $id['MemberID'],
                $_SESSION['userid']
            );
            $query = $this->db->query($sql, $p);
        }
        //insert hak akses data control (End)

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Data saved";
            $results['MemberIDInc'] = $id['MemberID'];
            $results['PartnerSurvey'] = $PartnerSurvey;
            $results['CountryCode'] = $CountryCode;

            //apakah ada fotonya, kalau ada dipindahkan dan diupdate            

            //Proses Photo ============= (Begin)
            if ($varPost['Koltiva_view_GrowerSME_FormMainGrower-MemberPhotoOld'] != "") {
                $Photo = "";
                $file = explode("/images/member/temp/",$varPost['Koltiva_view_GrowerSME_FormMainGrower-MemberPhotoOld']);
                //Insert ada photonya pakai aws
                if(file_exists('images/member/temp/'.$file[1])) {
                    $this->load->library('awsfileupload');
                    $upload = $this->awsfileupload->upload('images/member/temp/'.$file[1],$file[1],AWSS3_FARMER_PATH, 'images');
                    if ($upload['success'] == true) {
                        delete_file($varPost['Koltiva_view_GrowerSME_FormMainGrower-MemberPhotoOld']);
                        $Photo = $upload['filenamepath'];
                    }
                }

                $sql = "UPDATE ktv_members a SET
                        a.`Photo` = ?
                    WHERE
                        a.`MemberID` = ?
                    LIMIT 1";
                $p = array(
                    $Photo,
                    $id['MemberID']
                );
                $query = $this->db->query($sql, $p);
            }

            if ($varPost['Koltiva_view_GrowerSME_FormMainGrower-KTPPhotoOld'] != "") {
                $KTPFile = "";
                $file = explode("/images/member/temp/ktp/",$varPost['Koltiva_view_GrowerSME_FormMainGrower-KTPPhotoOld']);
                //Insert ada photonya pakai aws
                if(file_exists('images/member/temp/ktp/'.$file[1])) {
                    $this->load->library('awsfileupload');
                    $upload = $this->awsfileupload->upload('images/member/temp/ktp/'.$file[1],$file[1],AWSS3_FARMER_KTP_PATH, 'images');
                    if ($upload['success'] == true) {
                        delete_file($varPost['Koltiva_view_GrowerSME_FormMainGrower-KTPPhotoOld']);
                        $KTPFile = $upload['filenamepath'];
                    }
                }

                $sql = "UPDATE ktv_members a SET
                        a.`KTPFile` = ?
                    WHERE
                        a.`MemberID` = ?
                    LIMIT 1";
                $p = array(
                    $KTPFile,
                    $id['MemberID']
                );
                $query = $this->db->query($sql, $p);
            }

            //apakah ada foto consent notes
            if ($varPost['Koltiva_view_GrowerSME_FormMainGrower-LearningContractSignOld'] != "") {
                //get ext nya..
                $arrTemp = explode(".", $varPost['Koltiva_view_GrowerSME_FormMainGrower-LearningContractSignOld']);
                $extNya = array_values(array_slice($arrTemp, -1))[0];
                $namaFileGambar = $id['MemberDisplayID'] . "." . $extNya;
                $ProvinceID = substr($varPost['Koltiva_view_GrowerSME_FormMainGrower-Village'], 0, 2);

                //foto dipisah perdirectory ProvinceID, cek apakah folder tempat nyimpan foto sudah ada
                if (!file_exists('images/consent/' . $ProvinceID)) {
                    mkdir('images/consent/' . $ProvinceID, 0777, true);
                }
                $gambarTujuan = 'images/consent/' . $ProvinceID . '/' . $id['MemberDisplayID'] . "." . $extNya;

                rename('images/consent/' . $varPost['Koltiva_view_GrowerSME_FormMainGrower-LearningContractSignOld'], $gambarTujuan);

                $sql = "UPDATE ktv_members a SET
                        a.`LearningContractStatus` = '1',
                        a.LearningContractSign = ?
                    WHERE
                        a.`MemberID` = ?
                    LIMIT 1";
                $p = array(
                    $namaFileGambar,
                    $id['MemberID']
                );
                $query = $this->db->query($sql, $p);
            }
        }

        return $results;
    }

    public function insertMemberWAGS($varPost) {
        $this->load->library('awsfileupload');
        $this->db->trans_begin();
        //rapikan variable post (begin)
        foreach ($varPost as $k => $v) {
            if ($varPost[$k] == "") {
                $varPost[$k] = null;
            }
        }
        if ($varPost['Koltiva_view_Grower_FormMainGrower-InactiveReason'] == "")
            $varPost['Koltiva_view_Grower_FormMainGrower-InactiveReason'] = null;
        //rapikan variable post (end)

        $varPost['Koltiva_view_Grower_FormMainGrower-frChildrenCount'] = $this->_numOrNull($varPost['Koltiva_view_Grower_FormMainGrower-frChildrenCount']);
        // numericfield groups thousands with commas (e.g. "1,000"); strip them and
        // turn blanks into NULL so the integer column accepts the value.
        $varPost['Koltiva_view_Grower_FormMainGrower-HowManyPlot'] = $this->_numOrNull($varPost['Koltiva_view_Grower_FormMainGrower-HowManyPlot']);

        //generate MemberID dan MemberDisplayID
        $id = $this->genMemberID($varPost['Koltiva_view_Grower_FormMainGrower-Village'], 'F');

        //get Partner Member
        $PartnerID = $this->getPartnerMemberByDistrict($varPost['Koltiva_view_Grower_FormMainGrower-Village']);
        if($PartnerID == false){
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "No Partner assign to this farmer district yet";
            return $results;
        }
        $PartnerSurvey = $this->getPartnerSurveyByPartnerID($PartnerID);

        //Get apakah GAR/WAGS
        $sql = "SELECT
                    prov.`CountryCode`
                FROM
                    ktv_village vil
                    LEFT JOIN ktv_subdistrict subd ON vil.`SubDistrictID` = subd.`SubDistrictID`
                    LEFT JOIN ktv_district dis ON subd.`DistrictID` = dis.`DistrictID`
                    LEFT JOIN ktv_province prov ON dis.`ProvinceID` = prov.`ProvinceID`
                WHERE
                    vil.`VillageID` = ?
                LIMIT 1";
        $DataCountry = $this->db->query($sql,array($varPost['Koltiva_view_Grower_FormMainGrower-Village']))->row_array();
        $CountryCode = $DataCountry['CountryCode'];


        //ktv_members
        $p = array(
            $id['MemberID'],
            $id['MemberUid'],
            $id['MemberDisplayID'],
            $varPost['Koltiva_view_Grower_FormMainGrower-Fullname'],
            $varPost['Koltiva_view_Grower_FormMainGrower-DateCollection'],
            $varPost['Koltiva_view_Grower_FormMainGrower-DateOfBirth'],
            $varPost['Koltiva_view_Grower_FormMainGrower-Gender'],
            $varPost['Koltiva_view_Grower_FormMainGrower-PhotoDesc'],
            $varPost['Koltiva_view_Grower_FormMainGrower-MaritalStatus'],
            $varPost['Koltiva_view_Grower_FormMainGrower-Education'],
            $varPost['Koltiva_view_Grower_FormMainGrower-Village'],
            $varPost['Koltiva_view_Grower_FormMainGrower-Address'],
            $varPost['Koltiva_view_Grower_FormMainGrower-RtRw'],
            $varPost['Koltiva_view_Grower_FormMainGrower-Handphone'],
            $varPost['Koltiva_view_Grower_FormMainGrower-HandphoneType'],
            $varPost['Koltiva_view_Grower_FormMainGrower-AccessToSmartphone'],
            $varPost['Koltiva_view_Grower_FormMainGrower-Nin'],
            $varPost['Koltiva_view_Grower_FormMainGrower-inGroup'],
            $varPost['Koltiva_view_Grower_FormMainGrower-groupName'],
            $varPost['Koltiva_view_Grower_FormMainGrower-FarmerGroupID'],
            $varPost['Koltiva_view_Grower_FormMainGrower-inCoop'],
            $varPost['Koltiva_view_Grower_FormMainGrower-CoopName'],
            $varPost['Koltiva_view_Grower_FormMainGrower-inGapoktan'],
            $varPost['Koltiva_view_Grower_FormMainGrower-GapoktanName'],
            $varPost['Koltiva_view_Grower_FormMainGrower-ExtID'],
            $varPost['Koltiva_view_Grower_FormMainGrower-CategoryFarmer'],
            $varPost['Koltiva_view_Grower_FormMainGrower-TotalProductionArea'],
            $varPost['Koltiva_view_Grower_FormMainGrower-MembershipStatus'],
            $varPost['Koltiva_view_Grower_FormMainGrower-FarmerGroupWAGSID'],
            $varPost['Koltiva_view_Grower_FormMainGrower-HowManyPlot'],
            $varPost['Koltiva_view_Grower_FormMainGrower-WorkInPlot'],
            // $varPost['Koltiva_view_Grower_FormMainGrower-ManageFarm'],
            $varPost['Koltiva_view_Grower_FormMainGrower-UseAPD'],
            $varPost['Koltiva_view_Grower_FormMainGrower-HadAccident'],
            $varPost['Koltiva_view_Grower_FormMainGrower-WhatAccident'],
            $varPost['Koltiva_view_Grower_FormMainGrower-HaveBPJS'],
            $varPost['Koltiva_view_Grower_FormMainGrower-HaveBPJSKetenagakerjaan'],
            $varPost['Koltiva_view_Grower_FormMainGrower-HaveBPJSNo'],
            $varPost['Koltiva_view_Grower_FormMainGrower-SupplybaseType'],
            // $varPost['Koltiva_view_Grower_FormMainGrower-isCertified'],
            // $varPost['Koltiva_view_Grower_FormMainGrower-CertificationRSPO'],
            // $varPost['Koltiva_view_Grower_FormMainGrower-CertificationISCC'],
            // $varPost['Koltiva_view_Grower_FormMainGrower-CertificationISPO'],
            // $varPost['Koltiva_view_Grower_FormMainGrower-CertificationMSPO'],
            // $varPost['Koltiva_view_Grower_FormMainGrower-ReceiveTraining'],
            // $varPost['Koltiva_view_Grower_FormMainGrower-CertificationSourceGovernment'],
            // $varPost['Koltiva_view_Grower_FormMainGrower-CertificationSourceNGO'],
            // $varPost['Koltiva_view_Grower_FormMainGrower-CertificationSourceMill'],
            // $varPost['Koltiva_view_Grower_FormMainGrower-CertificationSourcePrivateOrg'],
            // $varPost['Koltiva_view_Grower_FormMainGrower-CertificationSourceOthers'],
            // $varPost['Koltiva_view_Grower_FormMainGrower-CertificationTypeFinancial'],
            // $varPost['Koltiva_view_Grower_FormMainGrower-CertificationTypeGoodAgriculture'],
            // $varPost['Koltiva_view_Grower_FormMainGrower-CertificationTypeHumanRights'],
            // $varPost['Koltiva_view_Grower_FormMainGrower-CertificationTypeManagementPesticides'],
            // $varPost['Koltiva_view_Grower_FormMainGrower-CertificationTypeFireFighting'],
            // $varPost['Koltiva_view_Grower_FormMainGrower-CertificationTypeHCVHCS'],
            // $varPost['Koltiva_view_Grower_FormMainGrower-CertificationTypeRSPOIndependent'],
            $varPost['Koltiva_view_Grower_FormMainGrower-JoinProgram'],
            $varPost['Koltiva_view_Grower_FormMainGrower-NotJoinProgramReason'],
            $varPost['Koltiva_view_Grower_FormMainGrower-NotJoinProgramReasonText'],
            $varPost['Koltiva_view_Grower_FormMainGrower-JoinComment'],
            $varPost['Koltiva_view_Grower_FormMainGrower-StatusMember'],
            $varPost['Koltiva_view_Grower_FormMainGrower-InactiveReason'],
            $varPost['Koltiva_view_Grower_FormMainGrower-InactiveReasonText'],
            $varPost['Koltiva_view_Grower_FormMainGrower-StoppedReason'],
            $varPost['Koltiva_view_Grower_FormMainGrower-StoppedReasonText'],
            $PartnerID,
            $_SESSION['userid']
        );
        $sql = "INSERT INTO `ktv_members` SET
                `MemberID` = ?,
                MemberUID = ?,
                `MemberDisplayID` = ?,
                `MemberName` = ?,
                `DateCollection` = ?,
                `DateOfBirth` = ?,
                Gender = ?,
                PhotoDesc = ?,
                `MaritalStatus` = ?,
                `Education` = ?,
                `VillageID` = ?,
                `Address` = ?,
                `RtRw` = ?,
                `Handphone` = ?,
                `HandphoneType` = ?,
                `AccessToSmartphone` = ?,
                Nin = ?,
                inGroup = ?,
                groupName = ?,
                FarmerGroupID = ?,
                inCoop = ?,
                CoopName = ?,
                inGapoktan = ?,
                GapoktanName = ?,
                ExtID = ?,
                CategoryFarmer = ?,
                TotalProductionArea = ?,
                MembershipStatus = ?,
                FarmerGroupWAGSID = ?,
                HowManyPlot = ?,
                WorkInPlot = ?,
                -- ManageFarm=?,
                UseAPD = ?,
                HadAccident = ?,
                WhatAccident = ?,
                HaveBPJS = ?,
                HaveBPJSKetenagakerjaan = ?,
                HaveBPJSNo = ?,
                SupplybaseType = ?,
                JoinProgram = ?,
                NotJoinProgramReason = ?,
                NotJoinProgramReasonText = ?,
                JoinComment = ?,
                StatusMember = ?,
                InactiveReason = ?,
                InactiveReasonText = ?,
                StoppedReason = ?,
                StoppedReasonText = ?,
                PartnerID = ?,
                `DateCreated` = NOW(),
                `CreatedBy` = ?";
        $query = $this->db->query($sql, $p);


        $sql3 = "INSERT INTO
                ktv_members_relation
            SET
            `MemberID` = ?,
            `ObjType` = 'agent',
            `ObjID` = ?,
            `DateCreated` = NOW(),
            `CreatedBy` = ?
        ";

        $p3 = array(
            $id['MemberID'],
            $varPost['Koltiva_view_Grower_FormMainGrower-DealerAssign'],
            $_SESSION['userid']
        );
        $query3 = $this->db->query($sql3, $p3);

        //ktv_members_extension
        $sql = "INSERT INTO `ktv_members_extension` SET
                `MemberID` = ?,
                `frRespondentName` = ?,
                `frRelationToOwner` = ?,
                `frRelationToOwnerText` = ?,
                `frComment` = ?,
                `frChildrenCount` = ?,
                `frChildrenSchool` = ?,
                `frChildrenWorkInFarm` = ?,
                `frChildrenUnderAgeWork` = ?,
                `frChildrenTypeOfWork` = ?,
                `DateCreated` = NOW(),
                `CreatedBy` = ?
            ";
        $p = array(
            $id['MemberID'],
            $varPost['Koltiva_view_Grower_FormMainGrower-frRespondentName'],
            $varPost['Koltiva_view_Grower_FormMainGrower-frRelationToOwner'],
            $varPost['Koltiva_view_Grower_FormMainGrower-frRelationToOwnerText'],
            $varPost['Koltiva_view_Grower_FormMainGrower-frComment'],
            $varPost['Koltiva_view_Grower_FormMainGrower-frChildrenCount'],
            $varPost['Koltiva_view_Grower_FormMainGrower-frChildrenSchool'],
            $varPost['Koltiva_view_Grower_FormMainGrower-frChildrenWorkInFarm'],
            $varPost['Koltiva_view_Grower_FormMainGrower-frChildrenUnderAgeWork'],
            $varPost['Koltiva_view_Grower_FormMainGrower-frChildrenTypeOfWork'],
            $_SESSION['userid']
        );
        $query = $this->db->query($sql, $p);

        //insert member role (begin)
        $arrRole = array();
        //if($varPost['Koltiva_view_Grower_FormMainGrower-CbRolePlanter'] == "1") $arrRole[] = 1;
        $arrRole[] = 1;

        foreach ($arrRole as $key => $value) {
            $sql = "INSERT INTO `ktv_member_role` SET
                `MemberID` = ?,
                `MRoleID` = ?,
                `DateCreated` = NOW(),
                `CreatedBy` = ?";
            $p = array(
                $id['MemberID'],
                $value,
                $_SESSION['userid']
            );
            $query = $this->db->query($sql, $p);
        }
        //insert member role (end)

        //insert hak akses data control (Begin)
        if ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program") {
            $sql = "INSERT INTO `ktv_access_partner_member` SET
                    `apmPartnerID` = ?,
                    `apmMemberID` = ?,
                    `DateCreated` = NOW(),
                    `CreatedBy` = ?";
            $p = array(
                $_SESSION['PartnerID'],
                $id['MemberID'],
                $_SESSION['userid']
            );
            $query = $this->db->query($sql, $p);

            //cek kalau bukan Partner Koltiva, maka ditambahkan juga ke Partner Koltiva
            if ($_SESSION['PartnerID'] != "1") {
                //insertkan ke Koltiva
                $sql = "INSERT INTO `ktv_access_partner_member` SET
                        `apmPartnerID` = ?,
                        `apmMemberID` = ?,
                        `DateCreated` = NOW(),
                        `CreatedBy` = ?";
                $p = array(
                    '1',
                    $id['MemberID'],
                    $_SESSION['userid']
                );
                $query = $this->db->query($sql, $p);
            }
        } else {
            //insertkan ke Koltiva
            $sql = "INSERT INTO `ktv_access_partner_member` SET
                    `apmPartnerID` = ?,
                    `apmMemberID` = ?,
                    `DateCreated` = NOW(),
                    `CreatedBy` = ?";
            $p = array(
                '1',
                $id['MemberID'],
                $_SESSION['userid']
            );
            $query = $this->db->query($sql, $p);
        }
        //insert hak akses data control (End)

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Data saved";
            $results['MemberIDInc'] = $id['MemberID'];
            $results['PartnerSurvey'] = $PartnerSurvey;
            $results['CountryCode'] = $CountryCode;

            //apakah ada fotonya, kalau ada dipindahkan dan diupdate            

            //Proses Photo ============= (Begin)
            if ($varPost['Koltiva_view_Grower_FormMainGrower-MemberPhotoOld'] != "") {
                $Photo = "";
                $file = explode("/images/member/temp/",$varPost['Koltiva_view_Grower_FormMainGrower-MemberPhotoOld']);
                //Insert ada photonya pakai aws
                if(file_exists('images/member/temp/'.$file[1])) {
                    $this->load->library('awsfileupload');
                    $upload = $this->awsfileupload->upload('images/member/temp/'.$file[1],$file[1],AWSS3_FARMER_PATH, 'images');
                    if ($upload['success'] == true) {
                        delete_file($varPost['Koltiva_view_Grower_FormMainGrower-MemberPhotoOld']);
                        $Photo = $upload['filenamepath'];
                    }
                }

                $sql = "UPDATE ktv_members a SET
                        a.`Photo` = ?
                    WHERE
                        a.`MemberID` = ?
                    LIMIT 1";
                $p = array(
                    $Photo,
                    $id['MemberID']
                );
                $query = $this->db->query($sql, $p);
            }

            //apakah ada foto consent notes
            if ($varPost['Koltiva_view_Grower_FormMainGrower-LearningContractSignOld'] != "") {
                //get ext nya..
                $arrTemp = explode(".", $varPost['Koltiva_view_Grower_FormMainGrower-LearningContractSignOld']);
                $extNya = array_values(array_slice($arrTemp, -1))[0];
                $namaFileGambar = $id['MemberDisplayID'] . "." . $extNya;
                $ProvinceID = substr($varPost['Koltiva_view_Grower_FormMainGrower-Village'], 0, 2);

                //foto dipisah perdirectory ProvinceID, cek apakah folder tempat nyimpan foto sudah ada
                if (!file_exists('images/consent/' . $ProvinceID)) {
                    mkdir('images/consent/' . $ProvinceID, 0777, true);
                }
                $gambarTujuan = 'images/consent/' . $ProvinceID . '/' . $id['MemberDisplayID'] . "." . $extNya;

                rename('images/consent/' . $varPost['Koltiva_view_Grower_FormMainGrower-LearningContractSignOld'], $gambarTujuan);

                $sql = "UPDATE ktv_members a SET
                        a.`LearningContractStatus` = '1',
                        a.LearningContractSign = ?
                    WHERE
                        a.`MemberID` = ?
                    LIMIT 1";
                $p = array(
                    $namaFileGambar,
                    $id['MemberID']
                );
                $query = $this->db->query($sql, $p);
            }
        }

        return $results;
    }

    function setPartnerFarmer($varPost){
        $sql = "UPDATE `ktv_members` SET
                PartnerID = ?,
                `DateUpdated` = NOW(),
                `LastModifiedBy` = ?
            WHERE
                `MemberID` = ?
            LIMIT 1";
        $p = array(
            $varPost['PartnerID'],
            $_SESSION['userid'],
            $varPost['MemberID']
        );
        $query = $this->db->query($sql, $p);
        if ($query) {
            $results['success'] = true;
            $results['message'] = "Data updated";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update data";
        }
        return $results;
    }

    public function updateMember($varPost) {
        $this->db->trans_begin();

        //Get apakah GAR/WAGS
        $sql = "SELECT
                    prov.`CountryCode`
                FROM
                    ktv_village vil
                    LEFT JOIN ktv_subdistrict subd ON vil.`SubDistrictID` = subd.`SubDistrictID`
                    LEFT JOIN ktv_district dis ON subd.`DistrictID` = dis.`DistrictID`
                    LEFT JOIN ktv_province prov ON dis.`ProvinceID` = prov.`ProvinceID`
                WHERE
                    vil.`VillageID` = ?
                LIMIT 1";
        $DataCountry = $this->db->query($sql,array($varPost['Koltiva_view_Grower_FormMainGrower-Village']))->row_array();
        $CountryCode = $DataCountry['CountryCode'];

        //rapikan variable post (begin)
        foreach ($varPost as $k => $v) {
            if ($varPost[$k] == "") {
                $varPost[$k] = null;
            }
        }
        if ($varPost['Koltiva_view_Grower_FormMainGrower-InactiveReason'] == "")
            $varPost['Koltiva_view_Grower_FormMainGrower-InactiveReason'] = null;
        //rapikan variable post (end)
        //photo
        if ($varPost['Koltiva_view_Grower_FormMainGrower-MemberPhotoOld'] != "") {
            $tmpGambar = $varPost['Koltiva_view_Grower_FormMainGrower-MemberPhotoOld'];
            $tmpGambar1 = substr($tmpGambar, 3);
            $tmpGambar2 = explode("?", $tmpGambar1);
            $sqlPhoto = " `Photo` = '{$tmpGambar2[0]}', ";
        } else {
            $Photo = null;
            $sqlPhoto = "";
        }

        //consent
        if ($varPost['Koltiva_view_Grower_FormMainGrower-LearningContractSignOld'] != "") {
            $tmpGambar = $varPost['Koltiva_view_Grower_FormMainGrower-LearningContractSignOld'];
            $tmpGambar1 = substr($tmpGambar, 3);
            $tmpGambar2 = explode("?", $tmpGambar1);
            $sqlConsent = " `LearningContractSign` = '{$tmpGambar2[0]}', `LearningContractStatus` = '1', ";
        } else {
            $Consent = null;
            $sqlConsent = "";
        }

        //get Partner Member
        $PartnerID = $this->getPartnerMemberByDistrict($varPost['Koltiva_view_Grower_FormMainGrower-Village']);
        if($PartnerID == false){
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "No Partner assign to this farmer district yet";
            return $results;
        }
        $PartnerSurvey = $this->getPartnerSurveyByPartnerID($PartnerID);

        //Farmer Group
        if($varPost['Koltiva_view_Grower_FormMainGrower-inGroup'] == "0"){
            $varPost['Koltiva_view_Grower_FormMainGrower-FarmerGroupID'] = null;
        }

        // echo "<pre>";
        // print_r($varPost);
        // die;

        $sql = "UPDATE `ktv_members` SET
                `MemberName` = ?,
                `DateCollection` = ?,
                `DateOfBirth` = ?,
                `Gender` = ?,
                PhotoDesc = ?,
                `MaritalStatus` = ?,
                `Education` = ?,
                `VillageID` = ?,
                `Address` = ?,
                `RtRw` = ?,
                `Handphone` = ?,
                `HandphoneCode` = ?,
                `HandphoneType` = ?,
                `AccessToSmartphone` = ?,
                $sqlConsent
                Nin = ?,
                inGroup = ?,
                groupName = ?,
                FarmerGroupID = ?,
                inCoop = ?,
                CoopName = ?,
                inGapoktan = ?,
                GapoktanName = ?,
                ExtID = ?,
                CategoryFarmer = ?,
                TotalProductionArea = ?,
                MembershipStatus = ?,
                FarmerGroupWAGSID = ?,
                HowManyPlot = ?,
                WorkInPlot = ?,
                UseAPD = ?,
                HadAccident = ?,
                WhatAccident = ?,
                HaveBPJS = ?,
                HaveBPJSKetenagakerjaan = ?,
                HaveBPJSNo = ?,
                SupplybaseType = ?,            
                isCertified = ?,
                CertificationRSPO = ?,
                CertificationISCC = ?,
                CertificationISPO = ?,
                CertificationMSPO = ?,
                ReceiveTraining = ?,
                CertificationSourceGovernment = ?,
                CertificationSourceNGO = ?,
                CertificationSourceMill = ?,
                CertificationSourcePrivateOrg = ?,
                CertificationSourceOthers = ?,
                CertificationTypeFinancial = ?,
                CertificationTypeGoodAgriculture = ?,
                CertificationTypeHumanRights = ?,
                CertificationTypeManagementPesticides = ?,
                CertificationTypeFireFighting = ?,
                CertificationTypeHCVHCS = ?,
                CertificationTypeRSPOIndependent = ?,
                WillingnesParticipate = ?,
                WillingnesCommit = ?,
                JoinProgram = ?,
                NotJoinProgramReason = ?,
                NotJoinProgramReasonText = ?,
                JoinComment = ?,
                StatusMember = ?,
                InactiveReason = ?,
                InactiveReasonText = ?,
                StoppedReason = ?,
                StoppedReasonText = ?,
                AccidentKnife = ?,
                AccidentHitbyFruit = ?,
                AccidentContimination = ?,
                AccidentOther = ?,
                AccidentOtherText = ?,
                DateLastVerfication = ?,
                HaveBankAccount = ?,
                ReceiveBankTransfer = ?,
                BankHolderName = ?,
                BankAccNumber = ?,
                BankID = ?,
                BankClientID = ?,
                BankBranchName = ?,
                AccountHolderRelation = ?,
                SurveyNr = ?,
                HaveOtherCommodities = ?,
                PartnerID = ?,
                `DateUpdated` = NOW(),
                `LastModifiedBy` = ?
            WHERE
                `MemberID` = ?
            LIMIT 1";
        $p = array(
            $varPost['Koltiva_view_Grower_FormMainGrower-Fullname'],
            $varPost['Koltiva_view_Grower_FormMainGrower-DateCollection'],
            $varPost['Koltiva_view_Grower_FormMainGrower-DateOfBirth'],
            $varPost['Koltiva_view_Grower_FormMainGrower-Gender'],
            $varPost['Koltiva_view_Grower_FormMainGrower-PhotoDesc'],
            $varPost['Koltiva_view_Grower_FormMainGrower-MaritalStatus'],
            $varPost['Koltiva_view_Grower_FormMainGrower-Education'],
            $varPost['Koltiva_view_Grower_FormMainGrower-Village'],
            $varPost['Koltiva_view_Grower_FormMainGrower-Address'],
            $varPost['Koltiva_view_Grower_FormMainGrower-RtRw'],
            $varPost['Koltiva_view_Grower_FormMainGrower-Handphone'],
            $varPost['Koltiva_view_Grower_FormMainGrower-HandphoneCode'],
            $varPost['Koltiva_view_Grower_FormMainGrower-HandphoneType'],
            $varPost['Koltiva_view_Grower_FormMainGrower-AccessToSmartphone'],
            $varPost['Koltiva_view_Grower_FormMainGrower-Nin'],
            $varPost['Koltiva_view_Grower_FormMainGrower-inGroup'],
            $varPost['Koltiva_view_Grower_FormMainGrower-groupName'],
            $varPost['Koltiva_view_Grower_FormMainGrower-FarmerGroupID'],
            $varPost['Koltiva_view_Grower_FormMainGrower-inCoop'],
            $varPost['Koltiva_view_Grower_FormMainGrower-CoopName'],
            $varPost['Koltiva_view_Grower_FormMainGrower-inGapoktan'],
            $varPost['Koltiva_view_Grower_FormMainGrower-GapoktanName'],
            $varPost['Koltiva_view_Grower_FormMainGrower-ExtID'],
            $varPost['Koltiva_view_Grower_FormMainGrower-CategoryFarmer'],
            $varPost['Koltiva_view_Grower_FormMainGrower-TotalProductionArea'],
            $varPost['Koltiva_view_Grower_FormMainGrower-MembershipStatus'],
            $varPost['Koltiva_view_Grower_FormMainGrower-FarmerGroupWAGSID'],
            $varPost['Koltiva_view_Grower_FormMainGrower-HowManyPlot'],
            $varPost['Koltiva_view_Grower_FormMainGrower-WorkInPlot'],
            $varPost['Koltiva_view_Grower_FormMainGrower-UseAPD'],
            $varPost['Koltiva_view_Grower_FormMainGrower-HadAccident'],
            $varPost['Koltiva_view_Grower_FormMainGrower-WhatAccident'],
            $varPost['Koltiva_view_Grower_FormMainGrower-HaveBPJS'],
            $varPost['Koltiva_view_Grower_FormMainGrower-HaveBPJSKetenagakerjaan'],
            $varPost['Koltiva_view_Grower_FormMainGrower-HaveBPJSNo'],
            $varPost['Koltiva_view_Grower_FormMainGrower-SupplybaseType'],            
            $varPost['Koltiva_view_Grower_FormMainGrower-isCertified'],
            $varPost['Koltiva_view_Grower_FormMainGrower-CertificationRSPO'],
            $varPost['Koltiva_view_Grower_FormMainGrower-CertificationISCC'],
            $varPost['Koltiva_view_Grower_FormMainGrower-CertificationISPO'],
            $varPost['Koltiva_view_Grower_FormMainGrower-CertificationMSPO'],
            $varPost['Koltiva_view_Grower_FormMainGrower-ReceiveTraining'],
            $varPost['Koltiva_view_Grower_FormMainGrower-CertificationSourceGovernment'],
            $varPost['Koltiva_view_Grower_FormMainGrower-CertificationSourceNGO'],
            $varPost['Koltiva_view_Grower_FormMainGrower-CertificationSourceMill'],
            $varPost['Koltiva_view_Grower_FormMainGrower-CertificationSourcePrivateOrg'],
            $varPost['Koltiva_view_Grower_FormMainGrower-CertificationSourceOthers'],
            $varPost['Koltiva_view_Grower_FormMainGrower-CertificationTypeFinancial'],
            $varPost['Koltiva_view_Grower_FormMainGrower-CertificationTypeGoodAgriculture'],
            $varPost['Koltiva_view_Grower_FormMainGrower-CertificationTypeHumanRights'],
            $varPost['Koltiva_view_Grower_FormMainGrower-CertificationTypeManagementPesticides'],
            $varPost['Koltiva_view_Grower_FormMainGrower-CertificationTypeFireFighting'],
            $varPost['Koltiva_view_Grower_FormMainGrower-CertificationTypeHCVHCS'],
            $varPost['Koltiva_view_Grower_FormMainGrower-CertificationTypeRSPOIndependent'],
            $varPost['Koltiva_view_Grower_FormMainGrower-WillingnesParticipate'],
            $varPost['Koltiva_view_Grower_FormMainGrower-WillingnesCommit'],
            $varPost['Koltiva_view_Grower_FormMainGrower-JoinProgram'],
            $varPost['Koltiva_view_Grower_FormMainGrower-NotJoinProgramReason'],
            $varPost['Koltiva_view_Grower_FormMainGrower-NotJoinProgramReasonText'],
            $varPost['Koltiva_view_Grower_FormMainGrower-JoinComment'],
            $varPost['Koltiva_view_Grower_FormMainGrower-StatusMember'],
            $varPost['Koltiva_view_Grower_FormMainGrower-InactiveReason'],
            $varPost['Koltiva_view_Grower_FormMainGrower-InactiveReasonText'],
            $varPost['Koltiva_view_Grower_FormMainGrower-StoppedReason'],
            $varPost['Koltiva_view_Grower_FormMainGrower-StoppedReasonText'],
            $varPost['Koltiva_view_Grower_FormMainGrower-AccidentKnife'],
            $varPost['Koltiva_view_Grower_FormMainGrower-AccidentHitbyFruit'],
            $varPost['Koltiva_view_Grower_FormMainGrower-AccidentContimination'],
            $varPost['Koltiva_view_Grower_FormMainGrower-AccidentOther'],
            $varPost['Koltiva_view_Grower_FormMainGrower-AccidentOtherText'],
            $varPost['Koltiva_view_Grower_FormMainGrower-DateLastVerfication'],
            $varPost['Koltiva_view_Grower_FormMainGrower-HaveBankAccount'],
            $varPost['Koltiva_view_Grower_FormMainGrower-ReceiveBankTransfer'],
            $varPost['Koltiva_view_Grower_FormMainGrower-BankHolderName'],
            $varPost['Koltiva_view_Grower_FormMainGrower-BankAccNumber'],
            $varPost['Koltiva_view_Grower_FormMainGrower-BankID'],
            $varPost['Koltiva_view_Grower_FormMainGrower-BankClientID'],
            $varPost['Koltiva_view_Grower_FormMainGrower-BankBranchName'],
            $varPost['Koltiva_view_Grower_FormMainGrower-AccountHolderRelation'],
            $varPost['Koltiva_view_Grower_FormMainGrower-SurveyNr'],
            $varPost['Koltiva_view_Grower_FormMainGrower-HaveOtherCommodities'],
            $PartnerID,
            $_SESSION['userid'],
            $varPost['Koltiva_view_Grower_FormMainGrower-MemberID']
        );
        $query = $this->db->query($sql, $p);

        $sql2 = "SELECT
                    * 
                FROM
                    `ktv_members_relation`
                WHERE MemberID = ?
        ";

        $query2 = $this->db->query($sql2,array($varPost['Koltiva_view_Grower_FormMainGrower-MemberID']));
        if($query2->num_rows()>0){
            $sql3 = "UPDATE
                    ktv_members_relation
                SET
                `ObjID` = ?,
                `ObjType` = 'agent',
                `DateUpdated` = NOW(),
                `LastModifiedBy` = ?
                WHERE
                    MemberID = ?
            ";

            $p3 = array(
                $varPost['Koltiva_view_Grower_FormMainGrower-DealerAssign'],
                $_SESSION['userid'],
                $varPost['Koltiva_view_Grower_FormMainGrower-MemberID']
            );
            $query3 = $this->db->query($sql3, $p3);
        }else{
            $sql3 = "INSERT INTO
                    ktv_members_relation
                SET
                `MemberID` = ?,
                `ObjType` = 'agent',
                `ObjID` = ?,
                `DateCreated` = NOW(),
                `CreatedBy` = ?
            ";

            $p3 = array(
                $varPost['Koltiva_view_Grower_FormMainGrower-MemberID'],
                $varPost['Koltiva_view_Grower_FormMainGrower-DealerAssign'],
                $_SESSION['userid']
            );
            $query3 = $this->db->query($sql3, $p3);
        }

        //ktv_members_extension
        $sql = "INSERT INTO ktv_members_extension (
                MemberID,
                `frRespondentName`,
                `frRelationToOwner`,
                `frRelationToOwnerText`,
                `frComment`,
                `frChildrenCount`,
                `frChildrenSchool`,
                `frChildrenWorkInFarm`,
                `frChildrenUnderAgeWork`,
                `frChildrenTypeOfWork`,
                `SurveyNr`,
                `DateCreated`,
                `CreatedBy`
                )
            VALUES(
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                NOW(),
                ?
                )
            ON DUPLICATE KEY UPDATE
                `frRespondentName` = ?,
                `frRelationToOwner` = ?,
                `frRelationToOwnerText` = ?,
                `frComment` = ?,
                `frChildrenCount` = ?,
                `frChildrenSchool` = ?,
                `frChildrenWorkInFarm` = ?,
                `frChildrenUnderAgeWork` = ?,
                `frChildrenTypeOfWork` = ?,
                `SurveyNr` = ?,
                `DateUpdated` = NOW(),
                `LastModifiedBy` = ?";
        $p = array(
            $varPost['Koltiva_view_Grower_FormMainGrower-MemberID'],
            $varPost['Koltiva_view_Grower_FormMainGrower-frRespondentName'],
            $varPost['Koltiva_view_Grower_FormMainGrower-frRelationToOwner'],
            $varPost['Koltiva_view_Grower_FormMainGrower-frRelationToOwnerText'],
            $varPost['Koltiva_view_Grower_FormMainGrower-frComment'],
            $varPost['Koltiva_view_Grower_FormMainGrower-frChildrenCount'],
            $varPost['Koltiva_view_Grower_FormMainGrower-frChildrenSchool'],
            $varPost['Koltiva_view_Grower_FormMainGrower-frChildrenWorkInFarm'],
            $varPost['Koltiva_view_Grower_FormMainGrower-frChildrenUnderAgeWork'],
            $varPost['Koltiva_view_Grower_FormMainGrower-frChildrenTypeOfWork'],
            $varPost['Koltiva_view_Grower_FormMainGrower-SurveyNr'],
            $_SESSION['userid'],
            $varPost['Koltiva_view_Grower_FormMainGrower-frRespondentName'],
            $varPost['Koltiva_view_Grower_FormMainGrower-frRelationToOwner'],
            $varPost['Koltiva_view_Grower_FormMainGrower-frRelationToOwnerText'],
            $varPost['Koltiva_view_Grower_FormMainGrower-frComment'],
            $varPost['Koltiva_view_Grower_FormMainGrower-frChildrenCount'],
            $varPost['Koltiva_view_Grower_FormMainGrower-frChildrenSchool'],
            $varPost['Koltiva_view_Grower_FormMainGrower-frChildrenWorkInFarm'],
            $varPost['Koltiva_view_Grower_FormMainGrower-frChildrenUnderAgeWork'],
            $varPost['Koltiva_view_Grower_FormMainGrower-frChildrenTypeOfWork'],
            $varPost['Koltiva_view_Grower_FormMainGrower-SurveyNr'],
            $_SESSION['userid']
        );
        $query = $this->db->query($sql, $p);

        // check external internal program existing

        $checkExistingInternalExternalProgram = $this->db->where('MemberID', (int) $varPost['Koltiva_view_Grower_FormMainGrower-MemberID'])
                                                         ->get('ktv_members_business_unit')->result();

        if (!empty($checkExistingInternalExternalProgram)) {
            $this->db->delete('ktv_members_business_unit', ['MemberID' => (int) $varPost['Koltiva_view_Grower_FormMainGrower-MemberID']]);
        }

        $arrGrowerInternalProgram = array();
        $arrGrowerInternalProgram = $varPost['Koltiva_view_Grower_FormMainGrower-CmbInternalProgram'];
        if(!empty($arrGrowerInternalProgram)){
            foreach($arrGrowerInternalProgram as $k => $GrowerInternalProgramID){
                $sqlInternalProgram="INSERT INTO `ktv_members_business_unit` SET
                    `MemberID` = ?,
                    `BusinessUnitID` = ?,
                    `DateCreated` = NOW(),
                    `CreatedBy` = ?";
                $pInternalProgram = array(
                    $varPost['Koltiva_view_Grower_FormMainGrower-MemberID'],
                    $GrowerInternalProgramID,
                    $_SESSION['userid']
                );
                $this->db->query($sqlInternalProgram,$pInternalProgram);
            }
           
        }
        //insert internal program ======================================================================== (end)

        $arrGrowerExternalProgram = array();
        $arrGrowerExternalProgram = $varPost['Koltiva_view_Grower_FormMainGrower-CmbExternalProgram'];
        if(!empty($arrGrowerExternalProgram)){
            foreach($arrGrowerExternalProgram as $k => $GrowerExternalProgramID){
                $sqlExternalProgram="INSERT INTO `ktv_members_business_unit` SET
                    `MemberID` = ?,
                    `BusinessUnitID` = ?,
                    `DateCreated` = NOW(),
                    `CreatedBy` = ?";
                $pExternalProgram = array(
                    $varPost['Koltiva_view_Grower_FormMainGrower-MemberID'],
                    $GrowerExternalProgramID,
                    $_SESSION['userid']
                );
                $this->db->query($sqlExternalProgram,$pExternalProgram);
            }
           
        }

        //insert external program ======================================================================== (end)

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to update data";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Data updated";
            $results['MemberIDInc'] = $varPost['Koltiva_view_Grower_FormMainGrower-MemberID'];
            $results['PartnerSurvey'] = $PartnerSurvey;
            $results['CountryCode'] = $CountryCode;
        }

        return $results;
    }

    public function updateMemberSME($varPost) {
        $this->db->trans_begin();

        //rapikan variable post (begin)
        $postFarmer     = array();
        $postExtention  = array();
        foreach ($varPost as $k => $v) {
            $k = str_replace("Koltiva_view_GrowerSME_FormMainGrower-","",$k,$count);
            $z = str_replace("Koltiva_view_Grower_FormLabourExtension-","",$k,$count2);

            if ($varPost[$k] == "") {
                $varPost[$k] = null;
            }

            if($count > 0){
                $postFarmer[$k] = $v;
            }

            if($count2 > 0){
                $postExtention[$z] = $v;
            }
        }

        $MemberID = $postFarmer['MemberID'];
        $MemberPhotoOld = $postFarmer['MemberPhotoOld'];
        $KTPPhotoOld = $postFarmer['KTPPhotoOld'];
        $WillingnesSignatureOld = $postFarmer['WillingnesSignatureOld'];
        $WillingnesCommitSignatureOld = $postFarmer['WillingnesCommitSignatureOld'];
        $DateStart = $postFarmer['DateStart'];
        $DateEnd = $postFarmer['DateEnd'];
        $DealerAssign = $postFarmer['DealerAssign'];
        $postFarmer['VillageID'] = $postFarmer['Village'];
        $postFarmer['MemberName'] = $postFarmer['Fullname'];
        $postFarmer['Comment'] = $postFarmer['frComment'];

        unset($postFarmer['DateStart']);
        unset($postFarmer['DateEnd']);
        unset($postFarmer['MemberID']);
        unset($postFarmer['Province']);
        unset($postFarmer['District']);
        unset($postFarmer['Subdistrict']);
        unset($postFarmer['Village']);
        unset($postFarmer['MemberPhotoOld']);
        unset($postFarmer['Fullname']);
        unset($postFarmer['DealerAssign']);
        unset($postFarmer['KTPPhotoOld']);
        unset($postFarmer['PlotTotalHectare']);
        unset($postFarmer['frComment']);
        unset($postFarmer['Enumerator']);
        unset($postFarmer['ModifiedBy']);
        unset($postFarmer['WillingnesSignatureOld']);
        unset($postFarmer['WillingnesCommitSignatureOld']);
        //rapikan variable post (begin)

        //Get apakah GAR/WAGS
        $sql = "SELECT
                    prov.`CountryCode`
                FROM
                    ktv_village vil
                    LEFT JOIN ktv_subdistrict subd ON vil.`SubDistrictID` = subd.`SubDistrictID`
                    LEFT JOIN ktv_district dis ON subd.`DistrictID` = dis.`DistrictID`
                    LEFT JOIN ktv_province prov ON dis.`ProvinceID` = prov.`ProvinceID`
                WHERE
                    vil.`VillageID` = ?
                LIMIT 1";
        $DataCountry = $this->db->query($sql,array($postFarmer['VillageID']))->row_array();
        $CountryCode = $DataCountry['CountryCode'];
        
        //photo
        if ($MemberPhotoOld != "") {
            $tmpGambar = $MemberPhotoOld;
            $tmpGambar1 = substr($tmpGambar, 3);
            $tmpGambar2 = explode("?", $tmpGambar1);
            $sqlPhoto = " `Photo` = '{$tmpGambar2[0]}', ";
        } else {
            $Photo = null;
            $sqlPhoto = "";
        }

        //consent
        if ($postFarmer['LearningContractSignOld'] != "") {
            $tmpGambar = $postFarmer['LearningContractSignOld'];
            $tmpGambar1 = substr($tmpGambar, 3);
            $tmpGambar2 = explode("?", $tmpGambar1);

            $postFarmer['LearningContractSign'] = $tmpGambar2[0];
            $postFarmer['LearningContractStatus'] = '1';
        } else {
            $Consent = null;
            $sqlConsent = "";
        }

        //get Partner Member
        $PartnerID = $this->getPartnerMemberByDistrict($postFarmer['VillageID']);
        if($PartnerID == false){
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "No Partner assign to this farmer district yet";
            return $results;
        }
        $PartnerSurvey = $this->getPartnerSurveyByPartnerID($PartnerID);

        //Farmer Group
        if($postFarmer['inGroup'] == "0"){
            $postFarmer['FarmerGroupID'] = null;
        }

        $this->db->where("MemberID",$MemberID);
        $query = $this->db->update('ktv_members',$postFarmer);

        $sql2 = "SELECT
                    * 
                FROM
                    `ktv_members_relation`
                WHERE MemberID = ?
        ";

        $query2 = $this->db->query($sql2,array($MemberID));
        if($query2->num_rows()>0){
            $sql3 = "UPDATE
                    ktv_members_relation
                SET
                `ObjID` = ?,
                `ObjType` = 'agent',
                `DateUpdated` = NOW(),
                `LastModifiedBy` = ?
                WHERE
                    MemberID = ?
            ";

            $p3 = array(
                $DealerAssign,
                $_SESSION['userid'],
                $MemberID
            );
            $query3 = $this->db->query($sql3, $p3);
        }else{
            $sql3 = "INSERT INTO
                    ktv_members_relation
                SET
                `MemberID` = ?,
                `ObjType` = 'agent',
                `ObjID` = ?,
                `DateCreated` = NOW(),
                `CreatedBy` = ?
            ";

            $p3 = array(
                $MemberID,
                $DealerAssign,
                $_SESSION['userid']
            );
            $query3 = $this->db->query($sql3, $p3);
        }

        //ktv_members_extension
        $sql = "INSERT INTO ktv_members_extension (
                MemberID,
                `frRespondentName`,
                `frRelationToOwner`,
                `frRelationToOwnerText`,
                `frComment`,
                `frChildrenCount`,
                `frChildrenSchool`,
                `frChildrenWorkInFarm`,
                `frChildrenUnderAgeWork`,
                `frChildrenTypeOfWork`,
                `DateCreated`,
                `CreatedBy`
                )
            VALUES(
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                NOW(),
                ?
                )
            ON DUPLICATE KEY UPDATE
                `frRespondentName` = ?,
                `frRelationToOwner` = ?,
                `frRelationToOwnerText` = ?,
                `frComment` = ?,
                `frChildrenCount` = ?,
                `frChildrenSchool` = ?,
                `frChildrenWorkInFarm` = ?,
                `frChildrenUnderAgeWork` = ?,
                `frChildrenTypeOfWork` = ?,
                `DateUpdated` = NOW(),
                `LastModifiedBy` = ?";
        $p = array(
            $MemberID,
            $postExtention['frRespondentName'],
            $postExtention['frRelationToOwner'],
            $postExtention['frRelationToOwnerText'],
            $postExtention['frComment'],
            $postExtention['frChildrenCount'],
            $postExtention['frChildrenSchool'],
            $postExtention['frChildrenWorkInFarm'],
            $postExtention['frChildrenUnderAgeWork'],
            $postExtention['frChildrenTypeOfWork'],
            $_SESSION['userid'],
            $postExtention['frRespondentName'],
            $postExtention['frRelationToOwner'],
            $postExtention['frRelationToOwnerText'],
            $postExtention['frComment'],
            $postExtention['frChildrenCount'],
            $postExtention['frChildrenSchool'],
            $postExtention['frChildrenWorkInFarm'],
            $postExtention['frChildrenUnderAgeWork'],
            $postExtention['frChildrenTypeOfWork'],
            $_SESSION['userid']
        );
        $query = $this->db->query($sql, $p);

        //ktv_tc_supplychain_farmer
        $sql = "SELECT SupplychainFarmerID FROM ktv_tc_supplychain_farmer WHERE FarmerID = ? AND SupplychainID = ?";
        $query = $this->db->query($sql,array($MemberID, $_SESSION['SupplychainID']));
        if($query->num_rows() > 0){            
            $postSupplychain['DateStart'] = $DateStart;
            $postSupplychain['DateEnd'] = $DateEnd;
            $postSupplychain['StatusCode'] = 'active';
            $postSupplychain['DateUpdated'] = date('Y-m-d H:i:s');

            $this->db->where('FarmerID',$MemberID);
            $this->db->where('SupplychainID',$_SESSION['SupplychainID']);
            $this->db->update('ktv_tc_supplychain_farmer',$postSupplychain);
        }else{
            $postSupplychain['FarmerID'] = $MemberID;
            $postSupplychain['SupplychainID'] = $_SESSION['SupplychainID'];
            $postSupplychain['DateStart'] = $DateStart;
            $postSupplychain['DateEnd'] = $DateEnd;
            $postSupplychain['StatusCode'] = 'active';
            $postSupplychain['DateCreated'] = date('Y-m-d H:i:s');
            $postSupplychain['CreatedBy'] = $_SESSION['userid'];

            $this->db->insert('ktv_tc_supplychain_farmer',$postSupplychain);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to update data";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Data updated";
            $results['MemberIDInc'] = $MemberID;
            $results['PartnerSurvey'] = $PartnerSurvey;
            $results['CountryCode'] = $CountryCode;
        }
        return $results;
    }

    public function updateMemberWAGS($varPost) {
        $this->db->trans_begin();

        //Get apakah GAR/WAGS
        $sql = "SELECT
                    prov.`CountryCode`
                FROM
                    ktv_village vil
                    LEFT JOIN ktv_subdistrict subd ON vil.`SubDistrictID` = subd.`SubDistrictID`
                    LEFT JOIN ktv_district dis ON subd.`DistrictID` = dis.`DistrictID`
                    LEFT JOIN ktv_province prov ON dis.`ProvinceID` = prov.`ProvinceID`
                WHERE
                    vil.`VillageID` = ?
                LIMIT 1";
        $DataCountry = $this->db->query($sql,array($varPost['Koltiva_view_GrowerWAGS_FormMainGrower-Village']))->row_array();
        $CountryCode = $DataCountry['CountryCode'];

        //rapikan variable post (begin)
        foreach ($varPost as $k => $v) {
            if ($varPost[$k] == "") {
                $varPost[$k] = null;
            }
        }
        if ($varPost['Koltiva_view_GrowerWAGS_FormMainGrower-InactiveReason'] == "")
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-InactiveReason'] = null;
        //rapikan variable post (end)
        //photo
        if ($varPost['Koltiva_view_GrowerWAGS_FormMainGrower-MemberPhotoOld'] != "") {
            $tmpGambar = $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-MemberPhotoOld'];
            $tmpGambar1 = substr($tmpGambar, 3);
            $tmpGambar2 = explode("?", $tmpGambar1);
            $sqlPhoto = " `Photo` = '{$tmpGambar2[0]}', ";
        } else {
            $Photo = null;
            $sqlPhoto = "";
        }

        //consent
        if ($varPost['Koltiva_view_GrowerWAGS_FormMainGrower-LearningContractSignOld'] != "") {
            $tmpGambar = $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-LearningContractSignOld'];
            $tmpGambar1 = substr($tmpGambar, 3);
            $tmpGambar2 = explode("?", $tmpGambar1);
            $sqlConsent = " `LearningContractSign` = '{$tmpGambar2[0]}', `LearningContractStatus` = '1', ";
        } else {
            $Consent = null;
            $sqlConsent = "";
        }

        //get Partner Member
        $PartnerID = $this->getPartnerMemberByDistrict($varPost['Koltiva_view_GrowerWAGS_FormMainGrower-Village']);
        if($PartnerID == false){
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "No Partner assign to this farmer district yet";
            return $results;
        }
        $PartnerSurvey = $this->getPartnerSurveyByPartnerID($PartnerID);

        //Farmer Group
        if($varPost['Koltiva_view_GrowerWAGS_FormMainGrower-inGroup'] == "0"){
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-FarmerGroupID'] = null;
        }

        // echo "<pre>";
        // print_r($varPost);
        // die;

        $sql = "UPDATE `ktv_members` SET
                `MemberName` = ?,
                `DateCollection` = ?,
                `DateOfBirth` = ?,
                `Gender` = ?,
                PhotoDesc = ?,
                `MaritalStatus` = ?,
                `Education` = ?,
                `VillageID` = ?,
                `Address` = ?,
                `RtRw` = ?,
                `Handphone` = ?,
                `HandphoneType` = ?,
                `AccessToSmartphone` = ?,
                $sqlConsent
                Nin = ?,
                inGroup = ?,
                groupName = ?,
                FarmerGroupID = ?,
                inCoop = ?,
                CoopName = ?,
                inGapoktan = ?,
                GapoktanName = ?,
                ExtID = ?,
                CategoryFarmer = ?,
                TotalProductionArea = ?,
                MembershipStatus = ?,
                FarmerGroupWAGSID = ?,
                HowManyPlot = ?,
                WorkInPlot = ?,
                ManageFarm = ?,
                UseAPD = ?,
                HadAccident = ?,
                WhatAccident = ?,
                HaveBPJS = ?,
                SupplybaseType = ?,
                JoinProgram = ?,
                NotJoinProgramReason = ?,
                NotJoinProgramReasonText = ?,
                JoinComment = ?,
                StatusMember = ?,
                InactiveReason = ?,
                InactiveReasonText = ?,
                StoppedReason = ?,
                StoppedReasonText = ?,
                PartnerID = ?,
                `DateUpdated` = NOW(),
                `LastModifiedBy` = ?
            WHERE
                `MemberID` = ?
            LIMIT 1";
        $p = array(
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-Fullname'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-DateCollection'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-DateOfBirth'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-Gender'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-PhotoDesc'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-MaritalStatus'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-Education'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-Village'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-Address'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-RtRw'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-Handphone'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-HandphoneType'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-AccessToSmartphone'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-Nin'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-inGroup'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-groupName'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-FarmerGroupID'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-inCoop'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-CoopName'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-inGapoktan'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-GapoktanName'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-ExtID'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-CategoryFarmer'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-TotalProductionArea'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-MembershipStatus'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-FarmerGroupWAGSID'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-HowManyPlot'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-WorkInPlot'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-ManageFarm'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-UseAPD'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-HadAccident'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-WhatAccident'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-HaveBPJS'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-SupplybaseType'],            
            // $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-isCertified'],
            // $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-CertificationRSPO'],
            // $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-CertificationISCC'],
            // $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-CertificationISPO'],
            // $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-CertificationMSPO'],
            // $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-ReceiveTraining'],
            // $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-CertificationSourceGovernment'],
            // $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-CertificationSourceNGO'],
            // $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-CertificationSourceMill'],
            // $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-CertificationSourcePrivateOrg'],
            // $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-CertificationSourceOthers'],
            // $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-CertificationTypeFinancial'],
            // $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-CertificationTypeGoodAgriculture'],
            // $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-CertificationTypeHumanRights'],
            // $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-CertificationTypeManagementPesticides'],
            // $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-CertificationTypeFireFighting'],
            // $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-CertificationTypeHCVHCS'],
            // $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-CertificationTypeRSPOIndependent'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-JoinProgram'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-NotJoinProgramReason'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-NotJoinProgramReasonText'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-JoinComment'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-StatusMember'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-InactiveReason'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-InactiveReasonText'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-StoppedReason'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-StoppedReasonText'],
            $PartnerID,
            $_SESSION['userid'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-MemberID']
        );
        $query = $this->db->query($sql, $p);

        $sql2 = "SELECT
                    * 
                FROM
                    `ktv_members_relation`
                WHERE MemberID = ?
        ";

        $query2 = $this->db->query($sql2,array($varPost['Koltiva_view_GrowerWAGS_FormMainGrower-MemberID']));
        if($query2->num_rows()>0){
            $sql3 = "UPDATE
                    ktv_members_relation
                SET
                `ObjID` = ?,
                `ObjType` = 'agent',
                `DateUpdated` = NOW(),
                `LastModifiedBy` = ?
                WHERE
                    MemberID = ?
            ";

            $p3 = array(
                $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-DealerAssign'],
                $_SESSION['userid'],
                $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-MemberID']
            );
            $query3 = $this->db->query($sql3, $p3);
        }else{
            $sql3 = "INSERT INTO
                    ktv_members_relation
                SET
                `MemberID` = ?,
                `ObjType` = 'agent',
                `ObjID` = ?,
                `DateCreated` = NOW(),
                `CreatedBy` = ?
            ";

            $p3 = array(
                $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-MemberID'],
                $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-DealerAssign'],
                $_SESSION['userid']
            );
            $query3 = $this->db->query($sql3, $p3);
        }

        //ktv_members_extension
        $sql = "INSERT INTO ktv_members_extension (
                MemberID,
                `frRespondentName`,
                `frRelationToOwner`,
                `frRelationToOwnerText`,
                `frComment`,
                `frChildrenCount`,
                `frChildrenSchool`,
                `frChildrenWorkInFarm`,
                `frChildrenUnderAgeWork`,
                `frChildrenTypeOfWork`,
                `DateCreated`,
                `CreatedBy`
                )
            VALUES(
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                NOW(),
                ?
                )
            ON DUPLICATE KEY UPDATE
                `frRespondentName` = ?,
                `frRelationToOwner` = ?,
                `frRelationToOwnerText` = ?,
                `frComment` = ?,
                `frChildrenCount` = ?,
                `frChildrenSchool` = ?,
                `frChildrenWorkInFarm` = ?,
                `frChildrenUnderAgeWork` = ?,
                `frChildrenTypeOfWork` = ?,
                `DateUpdated` = NOW(),
                `LastModifiedBy` = ?";
        $p = array(
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-MemberID'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-frRespondentName'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-frRelationToOwner'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-frRelationToOwnerText'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-frComment'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-frChildrenCount'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-frChildrenSchool'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-frChildrenWorkInFarm'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-frChildrenUnderAgeWork'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-frChildrenTypeOfWork'],
            $_SESSION['userid'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-frRespondentName'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-frRelationToOwner'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-frRelationToOwnerText'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-frComment'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-frChildrenCount'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-frChildrenSchool'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-frChildrenWorkInFarm'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-frChildrenUnderAgeWork'],
            $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-frChildrenTypeOfWork'],
            $_SESSION['userid']
        );
        $query = $this->db->query($sql, $p);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to update data";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Data updated";
            $results['MemberIDInc'] = $varPost['Koltiva_view_GrowerWAGS_FormMainGrower-MemberID'];
            $results['PartnerSurvey'] = $PartnerSurvey;
            $results['CountryCode'] = $CountryCode;
        }
        return $results;
    }

    public function updateMemberConsentNotes($gambarFileName, $MemberID) {
        $sql = "UPDATE ktv_members SET
                `LearningContractSign` = '{$gambarFileName}',
                `LearningContractStatus` = '1',
                `DateUpdated` = NOW(),
                `LastModifiedBy` = '{$_SESSION['userid']}'
            WHERE
                MemberID = ?
            LIMIT 1
            ";
        return $this->db->query($sql, array((int) $MemberID));
    }

    public function updateMemberWithdrawalConsentNotes($gambarFileName, $MemberID) {
        $sql = "UPDATE ktv_members SET
                `WithdrawalConsentSign` = '{$gambarFileName}',
                `WithdrawalConsentStatus` = '1',
                `DateUpdated` = NOW(),
                `LastModifiedBy` = '{$_SESSION['userid']}'
            WHERE
                MemberID = ?
            LIMIT 1
            ";
        return $this->db->query($sql, array((int) $MemberID));
    }

    public function UpdateMemberLabourExt($VarPost){
        $MemberID = $VarPost['MemberID'];

        $sql = "UPDATE `ktv_members_extension` a SET
                a.labHaveWorkers = ?,
                a.labHowManyWorker = ?,
                a.labWorkerUseApd = ?,
                a.labWhoBuyApd = ?,
                a.labWorkerHadAccident = ?,
                a.labWhatAccident = ?,
                a.labWorkerHaveBpjs = ?,
                a.labWhoPayBpjs = ?,
                a.labGiveInfoHealthSafety = ?,
                a.labWorkerLivePlantation = ?,
                a.labWorkerSafeHouse = ?,
                a.labWorkerKeepIdentity = ?,
                a.labWorkerAccessibleDocument = ?,
                a.labWorkerRecruitmentFee = ?,
                a.labWorkerWrittenContract = ?,
                a.labWorkerUnderstandRight = ?,
                a.labWorkerDeductionWage = ?,
                a.labWorkerFamilyWage = ?,
                a.labWorkerComplaintSystem = ?,
                a.labWorkerComplaintStored = ?,
                a.labWorkerOweMoney = ?,
                a.labWorkerBasicSupplies = ?,
                a.DateUpdated = NOW(),
                a.`LastModifiedBy` = ?
            WHERE
                a.`MemberID` = ?
            LIMIT 1";
        $p = array(
            $VarPost['Koltiva_view_Grower_FormLabourExtension-labHaveWorkers'],
            $VarPost['Koltiva_view_Grower_FormLabourExtension-labHowManyWorker'],
            $VarPost['Koltiva_view_Grower_FormLabourExtension-labWorkerUseApd'],
            $VarPost['Koltiva_view_Grower_FormLabourExtension-labWhoBuyApd'],
            $VarPost['Koltiva_view_Grower_FormLabourExtension-labWorkerHadAccident'],
            $VarPost['Koltiva_view_Grower_FormLabourExtension-labWhatAccident'],
            $VarPost['Koltiva_view_Grower_FormLabourExtension-labWorkerHaveBpjs'],
            $VarPost['Koltiva_view_Grower_FormLabourExtension-labWhoPayBpjs'],
            $VarPost['Koltiva_view_Grower_FormLabourExtension-labGiveInfoHealthSafety'],
            $VarPost['Koltiva_view_Grower_FormLabourExtension-labWorkerLivePlantation'],
            $VarPost['Koltiva_view_Grower_FormLabourExtension-labWorkerSafeHouse'],            
            $VarPost['Koltiva_view_Grower_FormLabourExtension-labWorkerKeepIdentity'],
            $VarPost['Koltiva_view_Grower_FormLabourExtension-labWorkerAccessibleDocument'],
            $VarPost['Koltiva_view_Grower_FormLabourExtension-labWorkerRecruitmentFee'],
            $VarPost['Koltiva_view_Grower_FormLabourExtension-labWorkerWrittenContract'],
            $VarPost['Koltiva_view_Grower_FormLabourExtension-labWorkerUnderstandRight'],
            $VarPost['Koltiva_view_Grower_FormLabourExtension-labWorkerDeductionWage'],
            $VarPost['Koltiva_view_Grower_FormLabourExtension-labWorkerFamilyWage'],
            $VarPost['Koltiva_view_Grower_FormLabourExtension-labWorkerComplaintSystem'],
            $VarPost['Koltiva_view_Grower_FormLabourExtension-labWorkerComplaintStored'],
            $VarPost['Koltiva_view_Grower_FormLabourExtension-labWorkerOweMoney'],
            $VarPost['Koltiva_view_Grower_FormLabourExtension-labWorkerBasicSupplies'],
            $_SESSION['userid'],
            $MemberID
        );
        $query = $this->db->query($sql,$p);

        if($query == true){
            $result['success'] = true;
            $result['message'] = lang('Data saved');
        }else{
            $result['success'] = false;
            $result['message'] = lang('Failed to save data');
        }
        return $result;
    }

    public function deleteMember($MemberID) {
        $sql = "UPDATE `ktv_members` SET
                StatusCode = 'nullified'
            WHERE
                `MemberID` = ?
            LIMIT 1";
        $p = array(
            $MemberID
        );
        $query = $this->db->query($sql, $p);

        if ($query) {
            $results['success'] = true;
            $results['message'] = "Data deleted";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete data";
        }
        return $results;
    }

    public function getGridFamilyLabour($MemberID) {
        $sql = "SELECT
                a.`FamLabID`,
                a.`MemberID`,
                a.`FamLabName`,
                a.`FamLabRelation`
                , CASE
                    WHEN a.FamLabRelation = '1' THEN '" . lang('Spouse') . "'
                    WHEN a.FamLabRelation = '2' THEN '" . lang('Child') . "'
                    WHEN a.FamLabRelation = '3' THEN '" . lang('Parent') . "'
                    WHEN a.FamLabRelation = '4' THEN '" . lang('Other') . "'
                END AS FamLabRelation
                , YEAR(CURDATE()) - a.`YearOfBirth` AS Age
                , CASE
                    WHEN a.Gender = 'm' THEN '" . lang('Male') . "'
                    WHEN a.Gender = 'f' THEN '" . lang('Female') . "'
                END AS Gender
            FROM
                `ktv_member_family_labour` a
            WHERE
                a.`MemberID` = ?
                AND a.StatusCode = 'active'
            ORDER BY a.`FamLabName` ASC, a.`FamLabName` ASC";
        $query = $this->db->query($sql, array((int) $MemberID));
        $data = $query->result_array();
        if ($data[0]['FamLabID'] == "")
            $data = array();

        $return['data'] = $data;
        return $return;
    }

    public function insertFamLab($paramPost) {
        $this->db->trans_start();

        unset($paramPost['FamLabAge']);
        unset($paramPost['Enumerator']);
        unset($paramPost['ModifiedBy']);

        $uid = $this->getUID();

        //ambil untuk MemberUid nya
        $dataMember = $this->getMemberDataDetail($paramPost['MemberID']);
        $paramPost['MemberUID'] = $uid;
        $paramPost['uid']       = $uid;

        $paramPost['DateCreated'] = date('Y-m-d H:i:s');
        $paramPost['CreatedBy'] = $_SESSION['userid'];
        $this->db->insert('ktv_member_family_labour', $paramPost);

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Data saved";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        }
        return $results;
    }

    public function updateFamLab($paramPost) {
        $this->db->trans_start();

        $paramPost['DateUpdated'] = date('Y-m-d H:i:s');
        $paramPost['LastModifiedBy'] = $_SESSION['userid'];

        $FamLabID = $paramPost['FamLabID'];
        unset($paramPost['FamLabID']);
        unset($paramPost['FamLabAge']);
        unset($paramPost['Enumerator']);
        unset($paramPost['ModifiedBy']);

        //reset semuanya dl
        $sql = "UPDATE `ktv_member_family_labour` SET
                `FamLabName` = null,
                `FamLabRelation` = null,
                `WorkingStatus` = null,
                `ActivityType` = null,
                `TotalWorkingHrsPerDay` = null,
                `TotalWorkingHrsPerMonth` = null,
                `WageAmount` = null,
                WageCurr = null,
                `WagePeriod` = null,
                `YearOfBirth` = null,
                `Gender` = null,
                `InSchool` = null,
                `TypeWorkSeed` = null,
                `TypeWorkSlash` = null,
                `TypeWorkCircle` = null,
                `TypeWorkPruning` = null,
                `TypeWorkPemupukan` = null,
                `TypeWorkPest` = null,
                `TypeWorkHarvest` = null,
                `TypeWorkTransport` = null,
                DayWorkInMonth = null,
                ReasonFamilyWork = null,
                FamLabInterviewDate = null,
                ReasonNotGoingToSchool = null,
                ReasonLackofLabor = null,
                ReasonHelpingParent = null,
                ReasonNotToPay = null,
                ReasonOther = null,
                UseSharpTools = null,
                ApplyingInorganicFert = null,
                SprayPest = null,
                CaryingHeavyItem = null,
                UsePPE = null
            WHERE
                FamLabID = ?
            LIMIT 1";
        $query = $this->db->query($sql, array($FamLabID));

        $this->db->where('FamLabID', $FamLabID);
        $query = $this->db->update('ktv_member_family_labour', $paramPost);

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Data saved";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        }
        return $results;
    }

    public function deleteFamLab($FamLabID) {
        $sql = "UPDATE `ktv_member_family_labour` SET
                StatusCode = 'nullified'
            WHERE
                FamLabID = ?
            LIMIT 1";
        $query = $this->db->query($sql, array($FamLabID));

        if ($query) {
            $results['success'] = true;
            $results['message'] = "Data deleted";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete data";
        }
        return $results;
    }

    public function getMemberFamilyLabourFormData($FamLabID) {
        $sql = "SELECT
                a.`FamLabID`,
                a.`MemberID`,
                a.`FamLabName`,
                a.`FamLabRelation`,
                a.`WorkingStatus`,
                a.`ActivityType`,
                a.`TotalWorkingHrsPerDay`,
                a.`TotalWorkingHrsPerMonth`,
                a.`WageAmount`,
                a.WageCurr,
                a.`YearOfBirth`,
                a.`Gender`,
                a.`InSchool`,
                a.`TypeWorkSeed`,
                a.`TypeWorkSlash`,
                a.`TypeWorkCircle`,
                a.`TypeWorkPruning`,
                a.`TypeWorkPemupukan`,
                a.`TypeWorkPest`,
                a.`TypeWorkHarvest`,
                a.`TypeWorkTransport`,
                a.WagePeriod,
                a.DayWorkInMonth,
                a.ReasonFamilyWork,
                a.ChildSupervision,
                a.ReasonNotGoingToSchool,
                a.ReasonLackofLabor,
                a.ReasonHelpingParent,
                a.ReasonNotToPay,
                a.ReasonOther,
                a.UseSharpTools,
                a.ApplyingInorganicFert,
                a.SprayPest,
                a.CaryingHeavyItem,
                a.FamLabInterviewDate,
                a.UsePPE,
                CONCAT((SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy` LIMIT 1),', ',a.DateCreated) AS Enumerator,
                CONCAT((SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`LastModifiedBy` LIMIT 1),', ',a.DateUpdated) AS ModifiedBy,
                a.DateCreated,
                a.DateUpdated
            FROM
                `ktv_member_family_labour` a
            WHERE
                a.`FamLabID` = ?
            LIMIT 1";
        $query = $this->db->query($sql, array((int) $FamLabID));
        $data = $query->row_array();

        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.Grower.WinFormFamLab-Form-" . $key;
            $dataRow[$keyNew] = $value;
        }

        $return['success'] = true;
        $return['data'] = $dataRow;
        return $return;
    }

    public function getGridPlotStatus($MemberID) {
        $sql = "SELECT
                a.`MemberID`
                , a.`PlotNr`
                , a.`SurveyNr` AS LastSurveyNr
                , CONCAT(a.`SurveyNr`,' - ',b.`SurveyTxt`) AS LastSurvey
                , a.`GardenAreaHa`
                , c.`ActiveStatus`
                , CASE
                    WHEN c.ActiveStatus = '1' THEN 'Active'
                    WHEN c.ActiveStatus = '2' THEN 'Inactive'
                    ELSE 'No Status'
                END AS ActiveStatus
            FROM
                ktv_survey_plot a
                INNER JOIN (
                    SELECT
                        sub_a.`MemberID`
                        , sub_a.`PlotNr`
                        , MAX(sub_a.`SurveyNr`) AS LatestSurveyNr
                    FROM
                        ktv_survey_plot sub_a
                    WHERE
                        sub_a.`StatusCode` = 'active'
                    GROUP BY sub_a.`MemberID`, sub_a.`PlotNr`
                ) AS plot_max_sur ON
                    a.`MemberID` = plot_max_sur.MemberID AND a.`PlotNr` = plot_max_sur.PlotNr AND a.`SurveyNr` = plot_max_sur.LatestSurveyNr
                LEFT JOIN ktv_survey b ON a.`SurveyNr` = b.`SurveyNr`
                LEFT JOIN ktv_member_plot_status c ON a.`MemberID` = c.`MemberID` AND a.`PlotNr` = c.`PlotNr`
            WHERE
                a.`StatusCode` = 'active'
                AND a.`MemberID` = ?
            ORDER BY a.`PlotNr` ASC";
        $query = $this->db->query($sql, array((int) $MemberID));
        $data = $query->result_array();
        if ($data[0]['MemberID'] == "")
            $data = array();

        $return['data'] = $data;
        return $return;
    }

    public function getMemberPlotStatusFormData($MemberID, $PlotNr) {
        $sql = "SELECT
                a.`MemberID`
                , a.`PlotNr`
                , CONCAT(a.`SurveyNr`,' - ',b.`SurveyTxt`) AS LastSurvey
                , a.`GardenAreaHa` AS AreaHa
                , c.`ActiveStatus`
                , c.InactiveStatus
                , c.OtherCommodity
                , c.Remark
            FROM
                ktv_survey_plot a
                INNER JOIN (
                    SELECT
                        sub_a.`MemberID`
                        , sub_a.`PlotNr`
                        , MAX(sub_a.`SurveyNr`) AS LatestSurveyNr
                    FROM
                        ktv_survey_plot sub_a
                    WHERE
                        sub_a.`StatusCode` = 'active'
                    GROUP BY sub_a.`MemberID`, sub_a.`PlotNr`
                ) AS plot_max_sur ON
                    a.`MemberID` = plot_max_sur.MemberID AND a.`PlotNr` = plot_max_sur.PlotNr AND a.`SurveyNr` = plot_max_sur.LatestSurveyNr
                LEFT JOIN ktv_survey b ON a.`SurveyNr` = b.`SurveyNr`
                LEFT JOIN ktv_member_plot_status c ON a.`MemberID` = c.`MemberID` AND a.`PlotNr` = c.`PlotNr`
            WHERE
                a.`StatusCode` = 'active'
                AND a.`MemberID` = ?
                AND a.`PlotNr` = ?";
        $p = array(
            (int) $MemberID,
            (int) $PlotNr
        );
        $query = $this->db->query($sql, $p);
        $data = $query->row_array();

        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.Grower.WinFormPlotStatus-Form-" . $key;
            $dataRow[$keyNew] = $value;
        }

        $return['success'] = true;
        $return['data'] = $dataRow;
        return $return;
    }

    public function updatePlotStatus($paramPost) {
        $this->db->trans_start();

        //cek apakah perlu insert / update
        $sql = "SELECT
                a.`MemberID`
            FROM
                ktv_member_plot_status a
            WHERE
                a.`MemberID` = ?
                AND a.`PlotNr` = ?
            LIMIT 1";
        $p = array(
            $paramPost['MemberID'],
            $paramPost['PlotNr']
        );
        $query = $this->db->query($sql, $p);
        $dataCek = $query->row_array();

        if ($dataCek['MemberID'] == "") {
            //ambil untuk MemberUid nya
            $dataMember = $this->getMemberDataDetail($paramPost['MemberID']);
            $paramPost['MemberUID'] = $dataMember['data']['MemberDisplayID'];

            //insert
            $sql = "INSERT INTO `ktv_member_plot_status` SET
                    `MemberID` = ?,
                    MemberUID = ?,
                    `PlotNr` = ?,
                    `ActiveStatus` = ?,
                    `InactiveStatus` = ?,
                    `OtherCommodity` = ?,
                    `Remark` = ?,
                    `DateCreated` = NOW(),
                    `CreatedBy` = ?";
            $p = array(
                $paramPost['MemberID'],
                $paramPost['MemberUID'],
                $paramPost['PlotNr'],
                $paramPost['ActiveStatus'],
                $paramPost['InactiveStatus'],
                $paramPost['OtherCommodity'],
                $paramPost['Remark'],
                $_SESSION['userid']
            );
            $query = $this->db->query($sql, $p);
        } else {
            //update
            $sql = "UPDATE `ktv_member_plot_status` SET
                    `ActiveStatus` = ?,
                    `InactiveStatus` = ?,
                    `OtherCommodity` = ?,
                    `Remark` = ?,
                    `DateUpdated` = NOW(),
                    `LastModifiedBy` = ?
                WHERE
                    `MemberID` = ?
                    AND `PlotNr` = ?
                LIMIT 1";
            $p = array(
                $paramPost['ActiveStatus'],
                $paramPost['InactiveStatus'],
                $paramPost['OtherCommodity'],
                $paramPost['Remark'],
                $_SESSION['userid'],
                $paramPost['MemberID'],
                $paramPost['PlotNr']
            );
            $query = $this->db->query($sql, $p);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Data saved";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        }
        return $results;
    }

    public function getFarmers($pSearch) {

        $sqlFilter = "";
        $sqlFilter = $this->generateSqlFilter($pSearch);

        //fixed tampilkan petani
        $sqlFilterRole .= " AND sub_b.MRoleID = 1 ";

        $sql = "SELECT
                SQL_CALC_FOUND_ROWS
                a.MemberID AS MemberIDInc
                , a.`MemberDisplayID` AS id
                , a.`MemberName` AS Name
                , c.`Village` AS Desa
                , d.`SubDistrict` AS Kecamatan
                , a.DateUpdated AS LastUpdated
                , e.Province
                , f.District
                , a.`DateOfBirth` AS Birthdate
                , FLOOR(DATEDIFF(CURDATE(), a.DateOfBirth) / 365.25) AS Age
                , DATE_FORMAT(a.`DateCollection`,'%Y-%m-%d') AS DateCollection
                , CONCAT(a.HandphoneCode, a.HandPhone) AS Handphone
                , CASE
                    WHEN a.MaritalStatus = '1' THEN '" . lang('Married') . "'
                    WHEN a.MaritalStatus = '2' THEN '" . lang('Single') . "'
                    WHEN a.MaritalStatus = '3' THEN '" . lang('Janda/Duda') . "'
                END AS MaritalStatus
                , GROUP_CONCAT(rrole.MRoleName SEPARATOR ', ') AS MemberRole
                , CONCAT(
                    (SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy` LIMIT 1),
                    IF(a.`LastModifiedBy` IS NOT NULL OR a.`LastModifiedBy` != '',(SELECT CONCAT(', modified by ',sub_a.UserRealname) FROM sys_user sub_a WHERE sub_a.UserId = a.`LastModifiedBy` LIMIT 1),'')
                ) AS Enumerator
            FROM
                ktv_members a
                INNER JOIN (
                    SELECT
                        sub_a.MemberID
                    FROM
                        ktv_members sub_a
                        LEFT JOIN ktv_member_role sub_b ON sub_a.MemberID = sub_b.MemberID
                    WHERE
                        sub_a.StatusCode = 'active'
                        $sqlFilterRole
                    GROUP BY sub_a.MemberID
                ) AS tmp_member_filter ON a.MemberID = tmp_member_filter.MemberID
                LEFT JOIN ktv_member_role mrole ON a.MemberID = mrole.MemberID
                LEFT JOIN ktv_ref_member_role rrole ON mrole.MRoleID = rrole.MRoleID
                LEFT JOIN ktv_village c ON a.`VillageID` = c.`VillageID`
                LEFT JOIN ktv_subdistrict d ON c.SubDistrictID = d.`SubDistrictID`
                LEFT JOIN ktv_district f ON d.DistrictID = f.DistrictID
                LEFT JOIN ktv_province e ON d.ProvinceID = e.ProvinceID
            WHERE
                a.`StatusCode` = 'active'
                $sqlFilter
            GROUP BY a.MemberID
            ";
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function getGridOtherLand($MemberID) {
        $sql = "SELECT
                a.`MemOtherID`
                , a.`MemberID`
                , CASE
                    WHEN a.`Commodity` = '1' THEN '" . lang('Corn') . "'
                    WHEN a.`Commodity` = '2' THEN '" . lang('Rubber') . "'
                    WHEN a.`Commodity` = '3' THEN '" . lang('Clove') . "'
                    WHEN a.`Commodity` = '4' THEN '" . lang('Rice') . "'
                    WHEN a.`Commodity` = '5' THEN '" . lang('Fruits') . "'
                    WHEN a.`Commodity` = '6' THEN '" . lang('Woods') . "'
                    WHEN a.`Commodity` = '7' THEN '" . lang('Other') . "'
                    WHEN a.`Commodity` = '8' THEN '" . lang('Cocoa') . "'
                END AS Commodity
                , a.`GardenHa`
                , 'null' AS Purpose
                , a.`Remark`
            FROM
                ktv_member_other_land a
            WHERE
                a.`StatusCode` = 'active'
                AND a.`MemberID` = ?
            ORDER BY a.`MemOtherID` ASC";
        $query = $this->db->query($sql, array((int) $MemberID));
        $data = $query->result_array();
        if ($data[0]['MemOtherID'] == "")
            $data = array();

        $return['data'] = $data;
        return $return;
    }

    public function insertOtherLand($paramPost) {
        $sql = "INSERT INTO `ktv_member_other_land` SET
                `MemberID` = ?,
                `Commodity` = ?,
                `GardenHa` = ?,
                `Remark` = ?,
                DateCreated = NOW(),
                CreatedBy = ?,
                uid = random_string(11,null)
            ";
        $p = array(
            $paramPost['MemberID'],
            $paramPost['Commodity'],
            $paramPost['GardenHa'],
            $paramPost['Remark'],
            $_SESSION['userid']
        );
        $query = $this->db->query($sql, $p);

        if ($query) {
            $results['success'] = true;
            $results['message'] = "Data saved";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        }
        return $results;
    }

    public function updateOtherLand($paramPost) {
        //cek apakah ada perubahan Commodity
        $cekCommo = (int) $paramPost['Commodity'];
        if ($cekCommo == 0) {
            $sqlCommo = "";
        } else {
            $sqlCommo = " `Commodity` = '{$paramPost['Commodity']}', ";
        }

        $sql = "UPDATE `ktv_member_other_land` SET
                $sqlCommo
                `GardenHa` = ?,
                `Remark` = ?,
                `DateUpdated` = NOW(),
                `LastModifiedBy` = ?
            WHERE
                `MemOtherID` = ?
            LIMIT 1";
        $p = array(
            $paramPost['GardenHa'],
            $paramPost['Remark'],
            $_SESSION['userid'],
            $paramPost['MemOtherID']
        );
        $query = $this->db->query($sql, $p);

        if ($query) {
            $results['success'] = true;
            $results['message'] = "Data saved";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        }
        return $results;
    }

    public function deleteOtherLand($MemOtherID) {
        $sql = "UPDATE `ktv_member_other_land` SET
                StatusCode = 'nullified'
            WHERE
                MemOtherID = ?
            LIMIT 1";
        $query = $this->db->query($sql, array($MemOtherID));

        if ($query) {
            $results['success'] = true;
            $results['message'] = "Data deleted";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete data";
        }
        return $results;
    }

    public function getMemberUID($MemberID) {
        $sql = "SELECT "
                . "uid "
                . " FROM ktv_members"
                . " WHERE MemberID = ?";
        $query = $this->db->query($sql, array($MemberID));
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result[0]['uid'];
        } else {
            return false;
        }
    }

    public function deleteMemberUID($uid) {
        $sql = "UPDATE `ktv_members` SET
                uid = null
            WHERE
                `uid` = ?
            LIMIT 1";
        $p = array(
            $uid
        );
        $query = $this->db->query($sql, $p);

        if ($query) {
            $results['success'] = true;
            $results['message'] = "Data deleted";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete data";
        }
        return $results;
    }

    public function getGridLabour($MemberID) {
        $sql = "SELECT
                a.LaboID
                , a.MemberID
                , a.LaboName
                , IFNULL(YEAR(".date("Y").") - a.`YearOfBirth`,FamLabAge) AS Age
                , CASE
                    WHEN a.Gender = 'm' THEN '" . lang('Male') . "'
                    WHEN a.Gender = 'f' THEN '" . lang('Female') . "'
                END AS Gender
                , FORMAT(a.WageAmount,0) AS WageAmount
                , CASE
                    WHEN a.WagePeriod = '1' THEN '" . lang('per day') . "'
                    WHEN a.WagePeriod = '2' THEN '" . lang('per week') . "'
                    WHEN a.WagePeriod = '3' THEN '" . lang('per month') . "'
                    WHEN a.WagePeriod = '4' THEN '" . lang('per year') . "'
                END AS WagePeriod
            FROM
                ktv_member_labour a
            WHERE
                a.`MemberID` = ?
                AND a.StatusCode = 'active'
            ORDER BY a.`LaboName` ASC
            ";
        $query = $this->db->query($sql, array((int) $MemberID));
        $data = $query->result_array();
        if ($data[0]['LaboID'] == "")
            $data = array();

        $return['data'] = $data;
        return $return;
    }

    public function insertLabour($paramPost) {
        $this->db->trans_start();

        unset($paramPost['Enumerator']);
        unset($paramPost['ModifiedBy']);

        $uid = $this->getUID();

        //ambil untuk MemberUid nya
        $dataMember = $this->getMemberDataDetail($paramPost['MemberID']);
        $paramPost['MemberUID'] = $uid;
        $paramPost['uid']       = $uid;

        $paramPost['DateCreated'] = date('Y-m-d H:i:s');
        $paramPost['CreatedBy'] = $_SESSION['userid'];
        $this->db->insert('ktv_member_labour', $paramPost);

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Data saved";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        }
        return $results;
    }

    public function updateLabour($paramPost) {
        $this->db->trans_start();

        $paramPost['DateUpdated'] = date('Y-m-d H:i:s');
        $paramPost['LastModifiedBy'] = $_SESSION['userid'];

        $LaboID = $paramPost['LaboID'];
        unset($paramPost['LaboID']);
        unset($paramPost['Enumerator']);
        unset($paramPost['ModifiedBy']);

        //reset semuanya dl
        $sql = "UPDATE `ktv_member_labour` SET
                `LaboName` = null,
                `TotalWorkingHrsPerDay` = null,
                `WageAmount` = null,
                WageCurr = null,
                `WagePeriod` = null,
                `YearOfBirth` = null,
                `Gender` = null,
                `TypeWorkSeed` = null,
                `TypeWorkSlash` = null,
                `TypeWorkCircle` = null,
                `TypeWorkPruning` = null,
                `TypeWorkPemupukan` = null,
                `TypeWorkPest` = null,
                `TypeWorkHarvest` = null,
                `TypeWorkTransport` = null,
                DayWorkInMonth = null
            WHERE
                LaboID = ?
            LIMIT 1";
        $query = $this->db->query($sql, array($LaboID));

        $this->db->where('LaboID', $LaboID);
        $query = $this->db->update('ktv_member_labour', $paramPost);

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Data saved";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        }
        return $results;
    }

    public function deleteLabour($LaboID) {
        $sql = "UPDATE `ktv_member_labour` SET
                StatusCode = 'nullified'
            WHERE
                LaboID = ?
            LIMIT 1";
        $query = $this->db->query($sql, array($LaboID));

        if ($query) {
            $results['success'] = true;
            $results['message'] = "Data deleted";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete data";
        }
        return $results;
    }

    public function getMemberLabourFormData($LaboID) {
        $sql = "SELECT
                a.LaboID,
                a.MemberID,
                a.LaboName,
                a.`YearOfBirth`,
                a.`Gender`,
                a.`TypeWorkSeed`,
                a.`TypeWorkSlash`,
                a.`TypeWorkCircle`,
                a.`TypeWorkPruning`,
                a.`TypeWorkPemupukan`,
                a.`TypeWorkPest`,
                a.`TypeWorkHarvest`,
                a.`TypeWorkTransport`,
                a.WagePeriod,
                a.WageCurr,
                a.`TotalWorkingHrsPerDay`,
                a.DayWorkInMonth,
                a.`WageAmount`,
                CONCAT((SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy` LIMIT 1),', ',a.DateCreated) AS Enumerator,
                CONCAT((SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`LastModifiedBy` LIMIT 1),', ',a.DateUpdated) AS ModifiedBy,
                a.DateCreated,
                a.DateUpdated
            FROM
                ktv_member_labour a
            WHERE
                a.LaboID = ?
            LIMIT 1";
        $query = $this->db->query($sql, array((int) $LaboID));
        $data = $query->row_array();

        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.Grower.WinFormLabour-Form-" . $key;
            $dataRow[$keyNew] = $value;
        }

        $return['success'] = true;
        $return['data'] = $dataRow;
        return $return;
    }

    public function getStaffAgent($MemberID){
        $arrReturn = array();

        $sql="SELECT
                COUNT(a.`StaffID`) AS BANYAK
                , SUM(IF(b.`Gender`='m',1,0)) AS StaffLaki
                , SUM(IF(b.`Gender`='f',1,0)) AS StaffPerempuan
            FROM
                ktv_staffs a
                INNER JOIN ktv_persons b ON a.`PersonID` = b.`PersonID`
            WHERE
                a.`ObjType` = 'agent'
                AND a.`ObjID` = ?
                AND a.`StatusCode` = 'active'
                AND b.`StatusCd` = 'active'";
        $query = $this->db->query($sql, array($MemberID));
        $data = $query->row_array();
        $pembagi_data_BANYAK = intval($data['BANYAK'])== 0 ? 1 : intval($data['BANYAK']);
        $arrReturn['NrOfStaff'] = number_format($data['BANYAK'],0,".",",");
        $arrReturn['StaffLaki'] = ($data['StaffLaki'] / $pembagi_data_BANYAK) * 100;
        $arrReturn['StaffLaki'] = number_format($arrReturn['StaffLaki'],0,".",",");
        $arrReturn['StaffPerempuan'] = 100 - $arrReturn['StaffLaki'];
        if($arrReturn['NrOfStaff'] == 0){
            $arrReturn['StaffLaki'] = 0;
            $arrReturn['StaffPerempuan'] = 0;
        }

        return $arrReturn;
    }

    public function getJumlahKendaraan($MemberID){
        $sql="SELECT
                COUNT(a.`VehID`) AS BANYAK
            FROM
                ktv_member_vehicle a
            WHERE
                a.`StatusCode` = 'active'
                AND a.`MemberID` = ?";
        $query = $this->db->query($sql, array($MemberID));
        $data = $query->row_array();
        return $data['BANYAK'];
    }

    public function getTrainingDataAgent($MemberID){
        $sql="SELECT
                COUNT(tpar.`ParticipantNewStaffID`) AS NrOfStaff
                , tt.`CpgTrainings` AS Topic
                , DATE(t.`TrainingStart`) AS `Start`
                , DATE(t.`TrainingEnd`) AS `End`
                , t.`TrainingDays` AS `Days`
            FROM
                ktv_staffs a
                INNER JOIN ktv_persons b ON a.`PersonID` = b.`PersonID`

                LEFT JOIN ktv_master_trainings_participants tpar ON a.`StaffID` = tpar.`ParticipantNewStaffID`
                LEFT JOIN ktv_master_trainings t ON tpar.`MasterTrainingID` = t.`MasterTrainingID`
                LEFT JOIN ktv_cpg_trainings tt ON t.`CPGtrainingsID` = tt.`CpgTrainingsID`
            WHERE
                a.`ObjType` = 'agent'
                AND a.`ObjID` = ?
                AND a.`StatusCode` = 'active'
                AND b.`StatusCd` = 'active'
                AND tpar.`StatusCode` = 'active'
                AND t.`StatusCode` = 'active'
            GROUP BY t.`MasterTrainingID`
            ORDER BY t.`TrainingEnd` DESC
            LIMIT 8
            ";

        $query = $this->db->query($sql, array((int) $MemberID));
        return $query->result_array();
    }

    public function getTraceabilityDataAgent($MemberID){
        $sql="SELECT
                    COUNT(DISTINCT st.SupplyBatchID) batch_count,
                    COUNT(DISTINCT st.SupplyTransID) trans_count,
                    FORMAT((SUM(st.VolumeNetto)/1000),2) netto
                FROM
                    view_tc_supplychain_org vso
                    LEFT JOIN ktv_tc_supplychain_transaction st ON st.SupplychainID=vso.SupplychainID
                WHERE
                    vso.ObjType='agent' AND vso.ObjID=?";

        $query = $this->db->query($sql, array((int) $MemberID))->result_array();

        $sql="SELECT
                    COUNT(DISTINCT IFNULL(st2.SupplyID, st.SupplyID)) farmer_count
                FROM
                    view_tc_supplychain_org vso
                    LEFT JOIN ktv_tc_supplychain_transaction st ON st.SupplychainID=vso.SupplychainID
                    LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchNumber=st.SupplyID AND st.SupplyType='Batch'
                    LEFT JOIN ktv_tc_supplychain_transaction st2 ON st2.SupplyBatchID=sb2.SupplyBatchID
                WHERE
                    vso.ObjType='agent' AND vso.ObjID=?";
        $farmer = $this->db->query($sql, array((int) $MemberID))->result_array();
        $return = array(
            'batch_count' => $query[0]['batch_count'],
            'trans_count' => $query[0]['trans_count'],
            'farmer_count' => $farmer[0]['farmer_count'],
            'netto' => $query[0]['netto']
        );
        return $return;
    }

    function getFFBSales($MemberID){
        $sql = "SELECT
            /*Quarter 1 Start*/
            COUNT(DISTINCT
                IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-01-01') AND CONCAT(YEAR(NOW()),'-03-31'),
                    batchid_1,
                    NULL
                )
            ) Q1_batch,
            IFNULL(SUM(DISTINCT
                IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-01-01') AND CONCAT(YEAR(NOW()),'-03-31'),
                    ROUND(IFNULL(netto_1,0)/1000,2),
                    NULL
                )
            ),0) Q1_ton,
            COUNT(DISTINCT
                IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-01-01') AND CONCAT(YEAR(NOW()),'-03-31'),
                    IF(objtype_1='agent', supplychainid_1, IF(objtype_2='agent', supplychainid_2, IF(objtype_3='agent', supplychainid_3, NULL))),
                    NULL
                )
            ) Q1_agent,
            COUNT(DISTINCT
                IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-01-01') AND CONCAT(YEAR(NOW()),'-03-31'),
                    MemberID,
                    NULL
                )
            ) Q1_farmer,
            COUNT(DISTINCT
                IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-01-01') AND CONCAT(YEAR(NOW()),'-03-31'),
                    transid_1,
                    NULL
                )
            ) Q1_transaction,
            /*Quarter 1 End*/
            
            /*Quarter 2 Start*/
            COUNT(DISTINCT
                IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-04-01') AND CONCAT(YEAR(NOW()),'-06-30'),
                    batchid_1,
                    NULL
                )
            ) Q2_batch,
            IFNULL(SUM(DISTINCT
                IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-04-01') AND CONCAT(YEAR(NOW()),'-06-30'),
                    ROUND(IFNULL(netto_1,0)/1000,2),
                    NULL
                )
            ),0) Q2_ton,
            COUNT(DISTINCT
                IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-04-01') AND CONCAT(YEAR(NOW()),'-06-30'),
                    IF(objtype_1='agent', supplychainid_1, IF(objtype_2='agent', supplychainid_2, IF(objtype_3='agent', supplychainid_3, NULL))),
                    NULL
                )
            ) Q2_agent,
            COUNT(DISTINCT
                IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-04-01') AND CONCAT(YEAR(NOW()),'-06-30'),
                    MemberID,
                    NULL
                )
            ) Q2_farmer,
            COUNT(DISTINCT
                IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-04-01') AND CONCAT(YEAR(NOW()),'-06-30'),
                    transid_1,
                    NULL
                )
            ) Q2_transaction,
            /*Quarter 2 End*/
            
            /*Quarter 3 Start*/
            COUNT(DISTINCT
                IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-07-01') AND CONCAT(YEAR(NOW()),'-09-30'),
                    batchid_1,
                    NULL
                )
            ) Q3_batch,
            IFNULL(SUM(DISTINCT
                IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-07-01') AND CONCAT(YEAR(NOW()),'-09-30'),
                    ROUND(IFNULL(netto_1,0)/1000,2),
                    NULL
                )
            ),0) Q3_ton,
            COUNT(DISTINCT
                IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-07-01') AND CONCAT(YEAR(NOW()),'-09-30'),
                    IF(objtype_1='agent', supplychainid_1, IF(objtype_2='agent', supplychainid_2, IF(objtype_3='agent', supplychainid_3, NULL))),
                    NULL
                )
            ) Q3_agent,
            COUNT(DISTINCT
                IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-07-01') AND CONCAT(YEAR(NOW()),'-09-30'),
                    MemberID,
                    NULL
                )
            ) Q3_farmer,
            COUNT(DISTINCT
                IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-07-01') AND CONCAT(YEAR(NOW()),'-09-30'),
                    transid_1,
                    NULL
                )
            ) Q3_transaction,
            /*Quarter 3 End*/
            
            /*Quarter 4 Start*/
            COUNT(DISTINCT
                IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-10-01') AND CONCAT(YEAR(NOW()),'-12-31'),
                    batchid_1,
                    NULL
                )
            ) Q4_batch,
            IFNULL(SUM(DISTINCT
                IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-10-01') AND CONCAT(YEAR(NOW()),'-12-31'),
                    ROUND(IFNULL(netto_1,0)/1000,2),
                    NULL
                )
            ),0) Q4_ton,
            COUNT(DISTINCT
                IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-10-01') AND CONCAT(YEAR(NOW()),'-12-31'),
                    IF(objtype_1='agent', supplychainid_1, IF(objtype_2='agent', supplychainid_2, IF(objtype_3='agent', supplychainid_3, NULL))),
                    NULL
                )
            ) Q4_agent,
            COUNT(DISTINCT
                IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-10-01') AND CONCAT(YEAR(NOW()),'-12-31'),
                    MemberID,
                    NULL
                )
            ) Q4_farmer,
            COUNT(DISTINCT
                IF(DATE_FORMAT(date_1, '%Y-%m-%d') BETWEEN CONCAT(YEAR(NOW()),'-10-01') AND CONCAT(YEAR(NOW()),'-12-31'),
                    transid_1,
                    NULL
                )
            ) Q4_transaction
            /*Quarter 4 End*/
        FROM 
        (
            SELECT
            st.SupplyTransID transid_1,
            st.TransNumber transnumber_1,
            st.SupplyType transtype_1,
            st.DateTransaction date_1,
            st.SupplyType supplytype_1,
            st.SupplyBatchType supplybatchtype_1,
            st.SupplyID supplyid_1,
            st.PlantationNr plot_1,
            m.MemberID,
            st.VolumeBruto bruto_1,
            st.VolumeNetto netto_1,
            IFNULL(m.MemberName, IFNULL(vso_1.`Name`, '-')) supplier_1,
            vso.`Name` name_1,
            vso.SupplychainID supplychainid_1,
            vso.ObjType objtype_1,
            vso.ObjID objid_1,
            st.SupplyBatchID batchid_1,
            sb.DeliveryDate deliverydate_1,
            
            st2.SupplyTransID transid_2,
            st2.TransNumber transnumber_2,
            st2.DateTransaction date_2,
            st2.SupplyType supplytype_2,
            st2.SupplyID supplyid_2,
            vso2.`Name` name_2,
            vso2.SupplychainID supplychainid_2,
            vso2.ObjType objtype_2,
            vso2.ObjID objid_2,
            st2.SupplyBatchID batchid_2,
            sb2.DeliveryDate deliverydate_2,
            
            st3.SupplyTransID transid_3,
            st3.TransNumber transnumber_3,
            st3.DateTransaction date_3,
            st3.SupplyType supplytype_3,
            st3.SupplyID supplyid_3,
            vso3.`Name` name_3,
            vso3.SupplychainID supplychainid_3,
            vso3.ObjType objtype_3,
            vso3.ObjID objid_3
        FROM
            ktv_tc_supplychain_transaction st
            LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID=st.SupplyBatchID
            LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID = IFNULL(st.SupplychainID, sb.SupplyOrgID)
            LEFT JOIN ktv_members m ON m.MemberID=st.SupplyID AND st.SupplyType='Farmer'
            LEFT JOIN view_tc_supplychain_org vso_1 ON vso_1.SupplychainID = IF(st.DOID > 0 , st.DOID, IF(st.AgentID > 0, st.AgentID, IF(st.MillID > 0, st.MillID, NULL)))
            
            LEFT JOIN ktv_tc_supplychain_transaction st2 ON st2.SupplyID=sb.SupplyBatchID AND st2.StatusCode='active' AND st2.SupplyType='Batch' AND (st2.SupplyBatchType IS NULL OR st2.SupplyBatchType='Traceable') AND st2.SupplyID > 0 AND st2.SupplyID!=st2.SupplychainID
            LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID=st2.SupplyBatchID
            LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID = IFNULL(st2.SupplychainID, sb2.SupplyOrgID)
            
            LEFT JOIN ktv_tc_supplychain_transaction st3 ON st3.SupplyID=sb.SupplyBatchID AND st3.StatusCode='active' AND st3.SupplyType='Batch' AND (st3.SupplyBatchType IS NULL OR st3.SupplyBatchType='Traceable') AND st3.SupplyID > 0 AND st3.SupplyID!=st3.SupplychainID
            LEFT JOIN ktv_tc_supplychain_batch sb3 ON sb3.SupplyBatchID=st3.SupplyBatchID
            LEFT JOIN view_tc_supplychain_org vso3 ON vso3.SupplychainID = IFNULL(st3.SupplychainID, sb3.SupplyOrgID)
            
            LEFT JOIN ktv_village v ON v.VillageID=vso.VillageID
            LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
            LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
            LEFT JOIN ktv_province p ON p.ProvinceID=d.ProvinceID
            
        WHERE 1=1
            AND st.StatusCode='active'
            AND (st.SupplyType IN ('Farmer', 'Nonfarmer') OR (st.SupplyType='Batch' AND st.SupplyBatchType='Untraceable'))
            AND st.SupplyID > 0
        GROUP BY st.SupplyTransID
        ) dt
        WHERE 
            /*Untuk Agent / SME*/
            ( (objtype_1='agent' AND objid_1='$MemberID') OR (objtype_2='agent' AND objid_2='$MemberID') OR (objtype_3='agent' AND objid_3='$MemberID') )
            /* 27078 = MemberID dari Agent atau SME */";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    function getTraceabilityDetails($MemberID){
        $sql="SELECT
                    IFNULL(st.SupplyBatchID, st.SupplyTransID) BatchID,
                    SUBSTR(IFNULL(sb.SupplyBatchDate, st.DateTransaction),1,10) DateTransaction,
                    SUM(st.VolumeNetto) VolumeNetto,
                    IFNULL(SUM(IF(st.Bjr>0, ROUND(st.VolumeBruto1 / st.Bjr), IF(st.NumberPackage > 0, st.NumberPackage, NULL))), '-') FFB,
                    IFNULL(vso2.`Name`, '-') Delivered
                FROM
                    ktv_supplychain_transaction st
                    LEFT JOIN ktv_supplychain_batch sb ON sb.SupplyBatchID=st.SupplyBatchID
                    LEFT JOIN view_supplychain_org vso ON vso.SupplychainID=st.SupplychainID
                    LEFT JOIN view_supplychain_org vso2 ON vso2.SupplychainID=sb.SupplyDestOrgID
                WHERE vso.OrgID = ? AND YEAR(st.DateTransaction)=YEAR(NOW())
                GROUP BY IFNULL(st.SupplyBatchID, st.SupplyTransID)";
        $query = $this->db->query($sql, array((int) $MemberID));
        //echo "<pre>".$this->db->last_query();die;
        return $query->result_array();
    }

    public function getComboDealer($uid = ''){
        $ObjID = $_SESSION["PartnerID"];
        $DaerahAccess = "";
        $MillID = "";

        if($uid <> ''){
            // $sql_p = "
            //     SELECT
            //         a.StaffID,
            //         a.ObjID,
            //         a.PersonID
            //     FROM
            //         `ktv_staffs` a
            //     LEFT JOIN
            //         ktv_persons p on p.PersonID = a.PersonID
            //     LEFT JOIN
            //         sys_user u on u.UserId = p.UserID
            //     WHERE
            //         u.UserExtId = ?
            // ";
            $sql_daerah_access = "SELECT
                                        a.MillID AS MillID
                                        , mill.PartnerID
                                        , GROUP_CONCAT(d.DistrictID) AS daerah_access
                                    FROM
                                        `ktv_staffs` a
                                    LEFT JOIN
                                        ktv_persons p on p.PersonID = a.PersonID
                                    LEFT JOIN
                                        sys_user u on u.UserId = p.UserID
                                    JOIN
                                        ktv_access_staff kas ON kas.`UserID` = u.`UserID`
                                    JOIN 
                                        ktv_district d ON kas.`DistrictID` = d.`DistrictID`
                                    JOIN 
                                        mw_organisationunit mworg ON d.`District` = mworg.`name`
                                    JOIN
                                        ktv_mill mill on mill.MillID = a.MillID
                                    WHERE
                                        u.UserExtId = ?
                                    GROUP BY
                                        u.UserID";
            $query_daerah_access = $this->db->query($sql_daerah_access, array($uid));
            $PartnerID = "";
            if($query_daerah_access->num_rows()>0){
                $data = $query_daerah_access->row();
                $DaerahAccess = $data->daerah_access;
                $MillID = $data->MillID;
                $PartnerID = $data->PartnerID;
            }
    
            $sql = " SELECT
                    b.MemberID id,
                    b.MemberUID uid,
                    IFNULL( c.agCompanyName, b.MemberName ) label 
                FROM
                    ktv_access_partner_member a
                    LEFT JOIN ktv_members b ON b.MemberID = a.apmMemberID
                    LEFT JOIN ktv_members_extension c ON c.MemberID = b.MemberID
                    LEFT JOIN ktv_member_role e ON e.MemberID = b.MemberID
                    LEFT JOIN ktv_mill d ON d.PartnerID = a.apmPartnerID 
                WHERE
                    a.apmPartnerID = ?
                    AND e.MRoleID <> 1 
                    AND b.StatusCode = 'active'
                                    GROUP BY b.MemberID";
            $query = $this->db->query($sql, [$PartnerID]);
            return $query->result_array();
        }else{
            $PartnerID = "";
            $sql = "SELECT
                        PartnerID
                    FROM
                        ktv_mill
                    WHERE
                        MillID = ?
                    AND
                        StatusCode = 'active'";
            $query = $this->db->query($sql, [$_SESSION["MillID"]]);
            if($query->num_rows()>0){
                $PartnerID = $query->row()->PartnerID;
            }
    
            $sql = " SELECT
                    b.MemberID id,
                    b.MemberUID uid,
                    IFNULL( c.agCompanyName, b.MemberName ) label 
                FROM
                    ktv_access_partner_member a
                    LEFT JOIN ktv_members b ON b.MemberID = a.apmMemberID
                    LEFT JOIN ktv_members_extension c ON c.MemberID = b.MemberID
                    LEFT JOIN ktv_member_role e ON e.MemberID = b.MemberID
                    LEFT JOIN ktv_mill d ON d.PartnerID = a.apmPartnerID 
                WHERE
                    a.apmPartnerID = ?
                    AND e.MRoleID <> 1 
                    AND b.StatusCode = 'active'
                                    GROUP BY b.MemberID";
            $query = $this->db->query($sql, [$PartnerID]);
            return $query->result_array();
        }
    }

    public function getComboCertified(){
        $sql = "
        SELECT
            CertProgID id
            , CertProgName label
        FROM
            `ktv_ref_certification_program`
        WHERE
            StatusCode = 'active'
            AND CertProgID IN (5,6,7,8,9,10);
        ";
        $query = $this->db->query($sql);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function getComboEnumerator()
    {
        $sql = "
SELECT
    u.`UserId` AS id
    , u.`UserRealName` AS label
FROM sys_user u
JOIN ktv_members m ON m.`CreatedBy` = u.`UserId`
WHERE
    m.`StatusCode` = 'active'
GROUP BY label        
        ";
        $query = $this->db->query($sql, array());
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    //============================ Khusus WAGS ======================================//

    public function getMemberBasicDataFormWAGS($MemberID) {
        $this->load->library('awsfileupload');
        $sql = "SELECT
                a.`MemberID` AS \"Koltiva.view.GrowerWAGS.FormMainGrower-MemberID\"
                , a.`MemberDisplayID` AS \"Koltiva.view.GrowerWAGS.FormMainGrower-MemberDisplayID\"
                , a.`DateCollection` AS \"Koltiva.view.GrowerWAGS.FormMainGrower-DateCollection\"
                , a.`MemberName` AS \"Koltiva.view.GrowerWAGS.FormMainGrower-Fullname\"
                , a.`DateOfBirth` AS \"Koltiva.view.GrowerWAGS.FormMainGrower-DateOfBirth\"
                , a.`Gender` AS \"Koltiva.view.GrowerWAGS.FormMainGrower-Gender\"
                , a.`Gender`
                , a.`MaritalStatus` AS \"Koltiva.view.GrowerWAGS.FormMainGrower-MaritalStatus\"
                , a.`Education` AS \"Koltiva.view.GrowerWAGS.FormMainGrower-Education\"
                , SUBSTR(a.`VillageID`,1,2) AS \"Province\"
                , SUBSTR(a.`VillageID`,1,4) AS \"District\"
                , SUBSTR(a.`VillageID`,1,7) AS \"Subdistrict\"
                , a.`VillageID` AS \"Village\"
                , a.`Address` AS \"Koltiva.view.GrowerWAGS.FormMainGrower-Address\"
                , a.`RtRw` AS \"Koltiva.view.GrowerWAGS.FormMainGrower-RtRw\"
                , a.`Handphone` AS \"Koltiva.view.GrowerWAGS.FormMainGrower-Handphone\"
                , a.`HandphoneType` AS \"Koltiva.view.GrowerWAGS.FormMainGrower-HandphoneType\"
                , a.`AccessToSmartphone` AS \"Koltiva.view.GrowerWAGS.FormMainGrower-AccessToSmartphone\"
                #, a.`Photo` AS \"Koltiva.view.GrowerWAGS.FormMainGrower-MemberPhotoOld\"
                , a.Photo AS PhotoSrc
                , a.KTPFile AS KTPSrc
                , a.LearningContractSign AS ConsentSrc
                , a.`PhotoDesc` AS \"Koltiva.view.GrowerWAGS.FormMainGrower-PhotoDesc\"
                , a.`StatusMember` AS \"Koltiva.view.GrowerWAGS.FormMainGrower-RbStatus\"
                , a.`InactiveReason` AS \"Koltiva.view.GrowerWAGS.FormMainGrower-InactiveReason\"
                , GROUP_CONCAT(b.`MRoleID` SEPARATOR ',') AS MemRole
                , a.Nin AS \"Koltiva.view.GrowerWAGS.FormMainGrower-Nin\"
                , a.inGroup AS \"Koltiva.view.GrowerWAGS.FormMainGrower-inGroup\"
                , a.groupName AS \"Koltiva.view.GrowerWAGS.FormMainGrower-groupName\"
                , a.FarmerGroupID AS \"FarmerGroupID\"
                , a.inCoop AS \"Koltiva.view.GrowerWAGS.FormMainGrower-inCoop\"
                , a.CoopName AS \"Koltiva.view.GrowerWAGS.FormMainGrower-CoopName\"
                , a.inGapoktan AS \"Koltiva.view.GrowerWAGS.FormMainGrower-inGapoktan\"
                , a.GapoktanName AS \"Koltiva.view.GrowerWAGS.FormMainGrower-GapoktanName\"
                , a.HowManyPlantation AS \"Koltiva.view.GrowerWAGS.FormMainGrower-HowManyPlantation\"
                , a.BankBeneficiary AS \"Koltiva.view.GrowerWAGS.FormMainGrower-BankBeneficiary\"
                , a.BankID AS \"Koltiva.view.GrowerWAGS.FormMainGrower-BankID\"
                , a.BankBranchName AS \"Koltiva.view.GrowerWAGS.FormMainGrower-BankBranchName\"
                , a.BankAccNumber AS \"Koltiva.view.GrowerWAGS.FormMainGrower-BankAccNumber\"

                , a.ExtID AS \"Koltiva.view.GrowerWAGS.FormMainGrower-ExtID\"
                , a.CategoryFarmer AS \"Koltiva.view.GrowerWAGS.FormMainGrower-CategoryFarmer\"
                , a.TotalProductionArea AS \"Koltiva.view.GrowerWAGS.FormMainGrower-TotalProductionArea\"
                , a.MembershipStatus AS \"Koltiva.view.GrowerWAGS.FormMainGrower-MembershipStatus\"
                , a.FarmerGroupWAGSID AS \"Koltiva.view.GrowerWAGS.FormMainGrower-FarmerGroupWAGSID\"
                , a.HowManyPlot AS \"Koltiva.view.GrowerWAGS.FormMainGrower-HowManyPlot\"
                , a.WorkInPlot AS \"Koltiva.view.GrowerWAGS.FormMainGrower-WorkInPlot\"
                -- , a.ManageFarm AS \"Koltiva.view.GrowerWAGS.FormMainGrower-ManageFarm\"
                , a.UseAPD AS \"Koltiva.view.GrowerWAGS.FormMainGrower-UseAPD\"
                , a.HadAccident AS \"Koltiva.view.GrowerWAGS.FormMainGrower-HadAccident\"
                , a.WhatAccident AS \"Koltiva.view.GrowerWAGS.FormMainGrower-WhatAccident\"
                , a.HaveBPJS AS \"Koltiva.view.GrowerWAGS.FormMainGrower-HaveBPJS\"
                , a.isCertified AS \"Koltiva.view.GrowerWAGS.FormMainGrower-isCertified\"
                , a.SupplybaseType AS \"Koltiva.view.GrowerWAGS.FormMainGrower-SupplybaseType\"
                , igar.TotalHectare AS \"Koltiva.view.GrowerWAGS.FormMainGrower-PlotTotalHectare\"
                , a.PartnerID

                , c.`frRespondentName` AS \"Koltiva.view.GrowerWAGS.FormMainGrower-frRespondentName\"
                , c.`frRelationToOwner` AS \"Koltiva.view.GrowerWAGS.FormMainGrower-frRelationToOwner\"
                , c.`frRelationToOwnerText` AS \"Koltiva.view.GrowerWAGS.FormMainGrower-frRelationToOwnerText\"
                , c.`frComment` AS \"Koltiva.view.GrowerWAGS.FormMainGrower-frComment\"
                , c.`frChildrenCount` AS \"Koltiva.view.GrowerWAGS.FormMainGrower-frChildrenCount\"
                , c.`frChildrenSchool` AS \"Koltiva.view.GrowerWAGS.FormMainGrower-frChildrenSchool\"
                , c.`frChildrenWorkInFarm` AS \"Koltiva.view.GrowerWAGS.FormMainGrower-frChildrenWorkInFarm\"
                , c.`frChildrenUnderAgeWork` AS \"Koltiva.view.GrowerWAGS.FormMainGrower-frChildrenUnderAgeWork\"
                , c.`frChildrenTypeOfWork` AS \"Koltiva.view.GrowerWAGS.FormMainGrower-frChildrenTypeOfWork\"
                , c.`labHaveWorkers`
                , c.`labHowManyWorker`
                , c.`labWorkerUseApd`
                , c.`labWhoBuyApd`
                , c.`labWorkerHadAccident`
                , c.`labWhatAccident`
                , c.`labWorkerHaveBpjs`
                , c.`labWhoPayBpjs`
                , c.`labGiveInfoHealthSafety`
                , c.labWorkerLivePlantation
                , c.labWorkerSafeHouse
                , c.labWorkerKeepIdentity
                , c.labWorkerAccessibleDocument
                , c.labWorkerRecruitmentFee
                , c.labWorkerWrittenContract
                , c.labWorkerUnderstandRight
                , c.labWorkerDeductionWage
                , c.labWorkerFamilyWage
                , c.labWorkerComplaintSystem
                , c.labWorkerComplaintStored
                , c.labWorkerOweMoney
                , c.labWorkerBasicSupplies
                , mr.ObjID AS \"Koltiva.view.GrowerWAGS.FormMainGrower-DealerAssign\"

                /*, CONCAT(
                    (SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy` LIMIT 1),
                    IF(a.`LastModifiedBy` IS NOT NULL OR a.`LastModifiedBy` != '',(SELECT CONCAT(', modified by ',sub_a.UserRealname) FROM sys_user sub_a WHERE sub_a.UserId = a.`LastModifiedBy` LIMIT 1),'')
                ) AS \"Koltiva.view.GrowerWAGS.FormMainGrower-Enumerator\"*/
                , CONCAT((SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy` LIMIT 1),', ',a.DateCreated) AS \"Koltiva.view.GrowerWAGS.FormMainGrower-Enumerator\"
                , CONCAT((SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`LastModifiedBy` LIMIT 1),', ',a.DateUpdated) AS \"Koltiva.view.GrowerWAGS.FormMainGrower-ModifiedBy\"
                , CertificationRSPO AS \"Koltiva.view.GrowerWAGS.FormMainGrower-IsCertified\"
                , CertificationISCC AS \"Koltiva.view.GrowerWAGS.FormMainGrower-CertificationISCC\"
                , CertificationISPO AS \"Koltiva.view.GrowerWAGS.FormMainGrower-CertificationISPO\"
                , CertificationRSPO AS \"Koltiva.view.GrowerWAGS.FormMainGrower-CertificationRSPO\"
                , CertificationMSPO AS \"Koltiva.view.GrowerWAGS.FormMainGrower-CertificationMSPO\"
                , ReceiveTraining AS \"Koltiva.view.GrowerWAGS.FormMainGrower-ReceiveTraining\"
                , CertificationSourceGovernment AS \"Koltiva.view.GrowerWAGS.FormMainGrower-CertificationSourceGovernment\"
                , CertificationSourceNGO AS \"Koltiva.view.GrowerWAGS.FormMainGrower-CertificationSourceNGO\"
                , CertificationSourceMill AS \"Koltiva.view.GrowerWAGS.FormMainGrower-CertificationSourceMill\"
                , CertificationSourcePrivateOrg AS \"Koltiva.view.GrowerWAGS.FormMainGrower-CertificationSourcePrivateOrg\"
                , CertificationSourceOthers AS \"Koltiva.view.GrowerWAGS.FormMainGrower-CertificationSourceOthers\"
                , CertificationTypeFinancial AS \"Koltiva.view.GrowerWAGS.FormMainGrower-CertificationTypeFinancial\"
                , CertificationTypeGoodAgriculture AS \"Koltiva.view.GrowerWAGS.FormMainGrower-CertificationTypeGoodAgriculture\"
                , CertificationTypeHumanRights AS \"Koltiva.view.GrowerWAGS.FormMainGrower-CertificationTypeHumanRights\"
                , CertificationTypeManagementPesticides AS \"Koltiva.view.GrowerWAGS.FormMainGrower-CertificationTypeManagementPesticides\"
                , CertificationTypeFireFighting AS \"Koltiva.view.GrowerWAGS.FormMainGrower-CertificationTypeFireFighting\"
                , CertificationTypeHCVHCS AS \"Koltiva.view.GrowerWAGS.FormMainGrower-CertificationTypeHCVHCS\"
                , CertificationTypeRSPOIndependent AS \"Koltiva.view.GrowerWAGS.FormMainGrower-CertificationTypeRSPOIndependent\"
                , JoinProgram AS \"Koltiva.view.GrowerWAGS.FormMainGrower-JoinProgram\"
                , NotJoinProgramReason AS \"Koltiva.view.GrowerWAGS.FormMainGrower-NotJoinProgramReason\"
                , NotJoinProgramReasonText AS \"Koltiva.view.GrowerWAGS.FormMainGrower-NotJoinProgramReasonText\"
                , JoinComment AS \"Koltiva.view.GrowerWAGS.FormMainGrower-JoinComment\"
                , StatusMember AS \"Koltiva.view.GrowerWAGS.FormMainGrower-StatusMember\"
                , InactiveReason AS \"Koltiva.view.GrowerWAGS.FormMainGrower-InactiveReason\"
                , InactiveReasonText AS \"Koltiva.view.GrowerWAGS.FormMainGrower-InactiveReasonText\"
                , StoppedReason AS \"Koltiva.view.GrowerWAGS.FormMainGrower-StoppedReason\"
                , StoppedReasonText AS \"Koltiva.view.GrowerWAGS.FormMainGrower-StoppedReasonText\"
            FROM
                ktv_members a
                LEFT JOIN ktv_member_role b ON a.`MemberID` = b.`MemberID`
                LEFT JOIN ktv_members_extension c ON a.MemberID = c.MemberID
                LEFT JOIN ktv_members_relation mr on mr.MemberID = a.MemberID
                LEFT JOIN (
                    SELECT
                        suba.`MemberID`
                        , SUM(suba.GardenAreaHa) AS TotalHectare
                    FROM
                        ktv_survey_plot suba
                        JOIN (SELECT
                                    p.MemberID, p.PlotNr, MAX(p.SurveyNr) AS SurveyNr
                                FROM ktv_survey_plot p WHERE p.`StatusCode` = 'active'
                                GROUP BY p.MemberID, p.PlotNr) suba_lat
                                    ON suba.MemberID = suba_lat.MemberID 
                                    AND suba.PlotNr = suba_lat.PlotNr 
                                    AND suba.SurveyNr = suba_lat.SurveyNr
                    WHERE
                        suba.`MemberID` = ?
                ) AS igar ON 1=1
                    AND a.`MemberID` = igar.MemberID
            WHERE
                a.`MemberID` = ?
            GROUP BY a.`MemberID`
            LIMIT 1";
        $query = $this->db->query($sql, array((int) $MemberID, (int) $MemberID));
        $data = $query->row_array();
        if($this->awsfileupload->doesObjectExist($data['PhotoSrc']) == true) {
            $data['PhotoSrcPath'] = $data['PhotoSrc'];
            $data['PhotoSrc'] = $this->config->item('CTCDN')."/".$data['PhotoSrc'];
        }else{
            $data['PhotoSrcPath'] = '/images/member/'.$data["Province"].'/'.$data['PhotoSrc'];
            $data['PhotoSrc'] = base_url().'/images/member/'.$data["Province"].'/'.$data['PhotoSrc'];
        }
        $return['success'] = true;
        $return['data'] = $data;
        return $return;
    }

    //============================ Khusus WAGS End ======================================//

    public function getFamMemberName($get){
        $where = '';
        $key   = $get['query'];

        if ($key != ""){
            $where .= " AND a.FamLabName like ?";
        }

        $start = $get['start'];
        $limit = $get['limit'];

        $limitfilter = ($start != null OR $start != '' AND $limit != null OR $limit != '')? ' LIMIT '. $start.','.$limit  : '';

        $sql = "SELECT SQL_CALC_FOUND_ROWS
            a.FamLabID as id
            ,a.FamLabName as label
            ,a.FamLabInterviewDate as interview_date
            ,a.YearOfBirth as year_birthdate
            ,a.MemberID as member_id
        FROM
            `ktv_member_family_labour` a
        WHERE
            1=1
            AND a.MemberID = ?
            $where
            AND a.StatusCode = 'active'
            ORDER BY a.FamLabName ASC $limitfilter";
        $data = $this->db->query($sql,array($get['MemberID'], "%$key%"))->result_array();
        
        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;
        
        $result["success"] = true;
        $result["data"] = $data;
        return $result;
    }

    public function insertFamLabPostline($paramPost) {
        $this->db->trans_start();

        /* $checkExistingFamLabPostSurveyNr = $this->checkExistingFamLabPostSurveyNr($paramPost["FamLabID"], $paramPost["survey_nr"]);

        if (!empty($checkExistingFamLabPostSurveyNr)) {
            return [
                'success' => false,
                'message' => lang("Data already exists")
            ];
        } */

        $uid = $this->getUID();

        unset($paramPost['YearOfBirth']);
        unset($paramPost['FamLabAge']);
        unset($paramPost['FamLabName']);
        unset($paramPost['FamLabPostID']);
        unset($paramPost['FamLabInterviewDate']);
        unset($paramPost['Enumerator']);
        unset($paramPost['ModifiedBy']);
        unset($paramPost['DateUpdated']);
        unset($paramPost['survey_nr_history']);

        if ($paramPost['conducting_postline'] == '2') {
            $paramPost['survey_nr'] = NULL;
        }

        if ($paramPost['wage_amount'] == "") {
            $paramPost['wage_amount'] = NULL;
        }

        if ($paramPost['total_working_hours_per_day'] == "") {
            $paramPost['total_working_hours_per_day'] = NULL;
        }

        if ($paramPost['total_working_days_per_month'] == "") {
            $paramPost['total_working_days_per_month'] = NULL;
        }

        $paramPost['uid'] = $uid;

        $paramPost['DateCreated'] = date('Y-m-d H:i:s');
        $paramPost['CreatedBy']   = $_SESSION['userid'];
        $this->db->insert('ktv_member_family_labour_postline', $paramPost);

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = lang("Data saved");
        } else {
            $results['success'] = false;
            $results['message'] = lang("Failed to save data");
        }

        return $results;
    }

    public function updateFamLabPostline($paramPost) {
        $this->db->trans_start();

        /* if ($paramPost['survey_nr'] != $paramPost['survey_nr_history']) {
            $checkExistingFamLabPostSurveyNr = $this->checkExistingFamLabPostSurveyNr($paramPost["FamLabID"], $paramPost["survey_nr"]);

            if (!empty($checkExistingFamLabPostSurveyNr)) {
                return [
                    'success' => false,
                    'message' => lang("Data already exists")
                ];
            }
        } */

        $paramPost['DateUpdated']    = date('Y-m-d H:i:s');
        $paramPost['LastModifiedBy'] = $_SESSION['userid'];

        $FamLabPostID = $paramPost['FamLabPostID'];

        unset($paramPost['FamLabPostID']);
        unset($paramPost['FamLabName']);
        unset($paramPost['FamLabInterviewDate']);
        unset($paramPost['YearOfBirth']);
        unset($paramPost['FamLabAge']);
        unset($paramPost['Enumerator']);
        unset($paramPost['DateCreated']);
        unset($paramPost['ModifiedBy']);
        unset($paramPost['survey_nr_history']);

        if ($paramPost['conducting_postline'] == '2') {
            $paramPost['survey_nr'] = NULL;
        }

        if ($paramPost['wage_amount'] == "") {
            $paramPost['wage_amount'] = NULL;
        }

        if ($paramPost['total_working_hours_per_day'] == "") {
            $paramPost['total_working_hours_per_day'] = NULL;
        }

        if ($paramPost['total_working_days_per_month'] == "") {
            $paramPost['total_working_days_per_month'] = NULL;
        }

        $this->db->where('FamLabPostID', $FamLabPostID);
        $query = $this->db->update('ktv_member_family_labour_postline', $paramPost);

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = lang("Data saved");
        } else {
            $results['success'] = false;
            $results['message'] = lang("Failed to save data");
        }

        return $results;
    }

    public function deleteFamLabPostline($FamLabPostID) {
        $sql = "UPDATE `ktv_member_family_labour_postline` SET
                StatusCode = 'nullified'
            WHERE
                FamLabPostID = ?
            LIMIT 1";
        $query = $this->db->query($sql, array($FamLabPostID));

        if ($query) {
            $results['success'] = true;
            $results['message'] = lang("Data deleted");
        } else {
            $results['success'] = false;
            $results['message'] = lang("Failed to delete data");
        }
        return $results;
    }

    public function getMemberFamilyLabourPostlineFormData($FamLabPostID) {
        $sql = "SELECT
                    a.`FamLabPostID`
                    ,a.`FamLabID`
                    ,a.`MemberID`
                    ,a.`FamLabID` as FamLabName
                    ,b.FamLabInterviewDate
                    ,b.YearOfBirth
                    ,a.conducting_postline
                    ,a.survey_nr
                    ,a.survey_nr as survey_nr_history
                    ,a.working_on_the_plantation
                    ,a.child_receive_adult_supervision
                    ,a.`child_activity_sharp_tools`
                    ,a.`child_activity_applying_inorganic`
                    ,a.`child_activity_spraying_pesticides`
                    ,a.`child_activity_carrying_heavy`
                    ,a.`type_work_planting`
                    ,a.`type_work_slashing`
                    ,a.`type_work_circle`
                    ,a.`type_work_pruning`
                    ,a.`type_work_fertilizing`
                    ,a.`type_work_pesticide`
                    ,a.`type_work_harvest`
                    ,a.`type_work_transportation`
                    ,a.`does_the_family_member_use_PPE`
                    ,a.`total_working_hours_per_day`
                    ,a.`total_working_days_per_month`
                    ,a.`wage_amount`
                    ,a.`wage_period`
                    ,a.`reason_not_going_to_school`
                    ,a.`reason_lack_of_labour`
                    ,a.`reason_helping_parents`
                    ,a.`reason_not_pay_them`
                    ,a.`reason_other`
                    ,CONCAT((SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy` LIMIT 1),', ',a.DateCreated) AS Enumerator
                    ,CONCAT((SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`LastModifiedBy` LIMIT 1),', ',a.DateUpdated) AS ModifiedBy
                    ,a.DateCreated
                    ,a.DateUpdated
                FROM
                    `ktv_member_family_labour_postline` a
                LEFT JOIN ktv_member_family_labour b ON a.FamLabID = b.FamLabID
                WHERE
                    a.`FamLabPostID` = ?
                LIMIT 1";
        $query = $this->db->query($sql, array((int) $FamLabPostID));
        $data = $query->row_array();

        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline-Form-" . $key;
            $dataRow[$keyNew] = $value;
        }

        $return['success'] = true;
        $return['data'] = $dataRow;
        return $return;
    }

    public function getGridFamilyLabourPostline($get) {
        $sorting      = json_decode($get['sort']);
        if (isset($sorting[0]->property)) $sortingField = $sorting[0]->property; else $sortingField = null;
        if (isset($sorting[0]->direction)) $sortingDir = $sorting[0]->direction; else $sortingDir = null;
        $start  = (int) $get['start'];
        $limit  = (int) $get['limit'];

        if ($sortingField == "") $sortingField = 'a.FamLabPostID';
        if ($sortingDir == "") $sortingDir = 'DESC';

        $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    a.`FamLabPostID`
                    ,a.`FamLabID`
                    ,a.`MemberID`
                    ,b.`FamLabName`
                    ,b.`FamLabInterviewDate`
                    ,IF(a.`survey_nr` IS NULL, ' - ' , CONCAT(c.`SurveyNr`,' - ',c.`SurveyTxt`)) AS survey_number
                    ,IF(a.`conducting_postline` = '2', 'No', 'Yes') as conducting_postline
                    ,a.`DateCreated`
                    ,a.`DateUpdated`
                FROM
                    `ktv_member_family_labour_postline` a
                LEFT JOIN ktv_member_family_labour b ON a.FamLabID = b.FamLabID
                LEFT JOIN ktv_survey c ON c.SurveyNr = a.survey_nr AND c.SurveyType = 'general'
                WHERE
                    a.`MemberID` = ?
                    AND a.`StatusCode` = 'active'
                ORDER BY $sortingField $sortingDir
                LIMIT ?,?";
        $query = $this->db->query($sql, array((int) $get['MemberID'], $start, $limit));
        $return['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $return['total'] = $query->row()->total;

        return $return;
    }

    private function checkExistingFamLabPostSurveyNr($FamLabID, $valueSurvey) {
        $getData = $this->db->where("FamLabID", $FamLabID)
                            ->where("survey_nr", $valueSurvey)
                            ->where("StatusCode", "active")
                            ->get('ktv_member_family_labour_postline')->result();

        return $getData;
    }

    public function getComboSurveyNrFamilyLabourPostline($from){
        $sql="SELECT
                a.`SurveyNr` AS id
                , CONCAT(a.`SurveyNr`,' - ',a.`SurveyTxt`) AS label
            FROM
                ktv_survey a
            WHERE
                a.`StatusCode` = 'active'
            AND a.`SurveyNr` = 22
            AND a.`SurveyType` = 'certification'
            ORDER BY a.`SurveyNr`";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function getFarmLabourName($get){
        $where = '';
        $key   = $get['query'];

        if ($key != ""){
            $where .= " AND a.LaboName like ?";
        }

        $start = $get['start'];
        $limit = $get['limit'];

        $limitfilter = ($start != null OR $start != '' AND $limit != null OR $limit != '')? ' LIMIT '. $start.','.$limit  : '';

        $sql = "SELECT SQL_CALC_FOUND_ROWS
            a.LaboID as id
            ,a.LaboName as label
            ,a.MemberID as MemberID
        FROM
            `ktv_member_labour` a
        WHERE
            1=1
            AND a.MemberID = ?
            $where
            AND a.StatusCode = 'active'
            ORDER BY a.LaboName ASC $limitfilter";
        $data = $this->db->query($sql,array($get['MemberID'], "%$key%"))->result_array();
        
        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;
        
        $result["success"] = true;
        $result["data"] = $data;
        return $result;
    }

    public function insertLabourPostline($paramPost) {
        $this->db->trans_start();
        $uid = $this->getUID();

        unset($paramPost['LaboName']);
        unset($paramPost['LaboPostID']);
        unset($paramPost['Enumerator']);
        unset($paramPost['ModifiedBy']);
        unset($paramPost['DateUpdated']);

        if ($paramPost['ConductingPostline'] == '2') {
            $paramPost['survey_nr'] = NULL;
        }

        if ($paramPost['WageAmount'] == "") {
            $paramPost['WageAmount'] = NULL;
        }

        if ($paramPost['TotalWorkingHrsPerDay'] == "") {
            $paramPost['TotalWorkingHrsPerDay'] = NULL;
        }

        if ($paramPost['DayWorkInMonth'] == "") {
            $paramPost['DayWorkInMonth'] = NULL;
        }

        $paramPost['uid'] = $uid;

        $paramPost['DateCreated'] = date('Y-m-d H:i:s');
        $paramPost['CreatedBy']   = $_SESSION['userid'];
        $this->db->insert('ktv_member_labour_postline', $paramPost);

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = lang("Data saved");
        } else {
            $results['success'] = false;
            $results['message'] = lang("Failed to save data");
        }

        return $results;
    }

    public function updateLabourPostline($paramPost) {
        $this->db->trans_start();

        $paramPost['DateUpdated']    = date('Y-m-d H:i:s');
        $paramPost['LastModifiedBy'] = $_SESSION['userid'];

        $LaboPostID = $paramPost['LaboPostID'];

        unset($paramPost['LaboName']);
        unset($paramPost['LaboPostID']);
        unset($paramPost['Enumerator']);
        unset($paramPost['DateCreated']);
        unset($paramPost['ModifiedBy']);

        if ($paramPost['ConductingPostline'] == '2') {
            $paramPost['survey_nr'] = NULL;
        }

        if ($paramPost['WageAmount'] == "") {
            $paramPost['WageAmount'] = NULL;
        }

        if ($paramPost['TotalWorkingHrsPerDay'] == "") {
            $paramPost['TotalWorkingHrsPerDay'] = NULL;
        }

        if ($paramPost['DayWorkInMonth'] == "") {
            $paramPost['DayWorkInMonth'] = NULL;
        }

        $this->db->where('LaboPostID', $LaboPostID);
        $query = $this->db->update('ktv_member_labour_postline', $paramPost);

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = lang("Data saved");
        } else {
            $results['success'] = false;
            $results['message'] = lang("Failed to save data");
        }

        return $results;
    }

    public function getMemberLabourPostlineFormData($LaboPostID) {
        $sql = "SELECT
                    a.`LaboPostID`
                    ,a.`LaboID`
                    ,a.`MemberID`
                    ,a.`LaboID` as LaboName
                    ,a.`ConductingPostline`
                    ,a.`survey_nr`
                    ,a.`TypeWorkPlanting`
                    ,a.`TypeWorkSlash`
                    ,a.`TypeWorkCircle`
                    ,a.`TypeWorkPruning`
                    ,a.`TypeWorkFertilizing`
                    ,a.`TypeWorkPest`
                    ,a.`TypeWorkHarvest`
                    ,a.`TypeWorkTransport`
                    ,a.`TotalWorkingHrsPerDay`
                    ,a.`DayWorkInMonth`
                    ,a.`WageAmount`
                    ,a.`WagePeriod`
                    ,CONCAT((SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy` LIMIT 1),', ',a.DateCreated) AS Enumerator
                    ,CONCAT((SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`LastModifiedBy` LIMIT 1),', ',a.DateUpdated) AS ModifiedBy
                    ,a.DateCreated
                    ,a.DateUpdated
                FROM
                    `ktv_member_labour_postline` a
                LEFT JOIN ktv_member_labour b ON a.LaboID = b.LaboID
                WHERE
                    a.`LaboPostID` = ?
                LIMIT 1";
        $query = $this->db->query($sql, array((int) $LaboPostID));
        $data = $query->row_array();

        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline-Form-" . $key;
            $dataRow[$keyNew] = $value;
        }

        $return['success'] = true;
        $return['data'] = $dataRow;
        return $return;
    }

    public function getGridLabourPostline($get) {
        $sorting      = json_decode($get['sort']);
        if (isset($sorting[0]->property)) $sortingField = $sorting[0]->property; else $sortingField = null;
        if (isset($sorting[0]->direction)) $sortingDir = $sorting[0]->direction; else $sortingDir = null;
        $start  = (int) $get['start'];
        $limit  = (int) $get['limit'];

        if ($sortingField == "") $sortingField = 'a.LaboPostID';
        if ($sortingDir == "") $sortingDir = 'DESC';

        $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    a.`LaboPostID`
                    ,a.`LaboID`
                    ,a.`MemberID`
                    ,b.`LaboName`
                    ,IF(a.`survey_nr` IS NULL, ' - ' , CONCAT(c.`SurveyNr`,' - ',c.`SurveyTxt`)) AS survey_number
                    ,IF(a.`ConductingPostline` = '2', 'No', 'Yes') as ConductingPostline
                    ,a.`DateCreated`
                    ,a.`DateUpdated`
                FROM
                    `ktv_member_labour_postline` a
                LEFT JOIN ktv_member_labour b ON a.LaboID = b.LaboID
                LEFT JOIN ktv_survey c ON c.SurveyNr = a.survey_nr AND c.SurveyType = 'general'
                WHERE
                    a.`MemberID` = ?
                    AND a.`StatusCode` = 'active'
                ORDER BY $sortingField $sortingDir
                LIMIT ?,?";
        $query = $this->db->query($sql, array((int) $get['MemberID'], $start, $limit));
        $return['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $return['total'] = $query->row()->total;

        return $return;
    }

    public function deleteLabourPostline($LaboPostID) {
        $sql = "UPDATE `ktv_member_labour_postline` SET
                StatusCode = 'nullified'
            WHERE
                LaboPostID = ?
            LIMIT 1";
        $query = $this->db->query($sql, array($LaboPostID));

        if ($query) {
            $results['success'] = true;
            $results['message'] = lang("Data deleted");
        } else {
            $results['success'] = false;
            $results['message'] = lang("Failed to delete data");
        }
        return $results;
    }
}
?>