<?php
/**
 * @Author: nikolius
 * @Date:   2017-07-18 17:48:58
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Msme_mem extends CI_Model { 

    public function __construct() {
        parent::__construct();
    }

    public function getGridAgentRelation($MemberID){
        $sql = "SELECT SQL_CALC_FOUND_ROWS
            m.MemberDisplayID
            , IFNULL(me.agCompanyName, m.MemberName) MemberName
            , so.StartDate
            , so.EndDate
        FROM
            view_tc_supplychain_org vso
        LEFT JOIN
            ktv_tc_supplychain_org_rel so on so.ChildID = vso.SupplychainID
        LEFT JOIN
            view_tc_supplychain_org vso2 on vso2.SupplychainID = so.ParentID
        JOIN
            ktv_member_role mr on mr.MemberID = vso2.ObjID
        JOIN
            ktv_members m on m.MemberID = vso2.ObjID
        LEFT JOIN
            ktv_members_extension me on me.MemberID = m.MemberID
        WHERE
            vso.ObjID = ?
        AND
            mr.MRoleID = 13
        AND
            so.StatusCode = 'active' GROUP BY m.MemberID";
        $query = $this->db->query($sql, array($MemberID));
        
        $result["data"] = $query->result_array();
        
        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    public function generateSqlHakAkses(){
        $sqlHakAkses = array();

        $is_admin      = isset($_SESSION['is_admin'])      ? $_SESSION['is_admin']      : '0';
        $role          = isset($_SESSION['role'])          ? $_SESSION['role']          : '';
        $PartnerID     = isset($_SESSION['PartnerID'])     ? $_SESSION['PartnerID']     : '';
        $daerah_access = isset($_SESSION['daerah_access']) ? $_SESSION['daerah_access'] : '0';

        if($is_admin == "1"){
            $sqlHakAkses['join'] = "";
            $sqlHakAkses['where'] = "";
        } elseif ($role == "Private" || $role == "Program"){
            $sqlHakAkses['where'] = " AND kd.DistrictID IN (" . $daerah_access . ")";
            $sqlHakAkses['join'] = " INNER JOIN ktv_access_partner_member acc_pm ON a.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$PartnerID}' ";
        } else {
            $sqlHakAkses['join'] = "";
            $sqlHakAkses['where'] = " AND kd.DistrictID IN (" . $daerah_access . ")";
        }

        return $sqlHakAkses;
    }

    public function getComboSPCode($MillID){
        $sql = "
            SELECT
                a.SPCodeID id
                , a.SuratNr label
            FROM
                ktv_mill_sp_code a
            WHERE
                a.MillID = ?
            GROUP BY a.SPCodeID 
            ORDER BY a.SuratNr ASC
        ";

        $query = $this->db->query($sql,array($MillID));

        return $query->result_array();
    }

    public function getComboMillSME($MemberID = null){
        $sql = "
            SELECT
            op.ObjID id,
            m.MillName label
            FROM
                ktv_members a
                LEFT JOIN ktv_tc_supplychain_org o on a.MemberID = o.ObjID
                LEFT JOIN ktv_tc_supplychain_org_rel orel on orel.ChildID = o.SupplychainID
                LEFT JOIN ktv_tc_supplychain_org op on orel.ParentID = op.SupplychainID
                LEFT JOIN ktv_mill m on m.MillID = op.ObjID
            WHERE
            a.MemberID = ?
            AND o. ObjType = 'agent'
            AND m.StatusCode = 'active'
        ";

        $query = $this->db->query($sql,array($MemberID));

        return $query->result_array();
    }

    public function getSPCode($MemberID){
        $sql = "
        SELECT
            a.SMESPCodeID
            , a.SPCodeID
            , a.MemberID
            , a.DateStart
            , a.DateEnd
            , a.Remarks
            , b.SuratNr
            , m.MillName
        FROM
            `ktv_sme_sp_code` a
        LEFT JOIN
            ktv_mill_sp_code b on b.SPCodeID = a.SPCodeID
        LEFT JOIN
            ktv_mill m on m.MillID = b.MillID
        WHERE
            MemberID = ?
        ";

        $query = $this->db->query($sql,array($MemberID));

        return $query->result_array();
    }

    function updateFotoWarehouse($MemberID,$path){
        $sql = "
            UPDATE
                ktv_trader_warehouses
            SET
                PhotoBusinessLocation = '$path'
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

    function updateFotoCompanyLogo($MemberID,$path){
        $sql = "
            UPDATE
                ktv_members_extension
            SET
                agCompanyLogo = '$path'
            WHERE MemberID = '$MemberID'
        ";

        $query = $this->db->query($sql);
    }

    public function getTraderFarmerGarden($MemberID){
        $sql = "SELECT
        -- 	sub_a.`MemberID` AS MemberID,
        -- 	COUNT( DISTINCT ktsf.`FarmerID` ) AS NrFarmer
            sm.MemberDisplayID SMEID
            , me.agCompanyName SMEName
            , sm.Alias
            , sf.MemberDisplayID FarmerID
            , sf.MemberName FarmerName
            , sp.GardenAreaHa GardenAreaHa
            , p.Province
            , d.District
            , sd.SubDistrict
            , v.Village
            , ST_Y(sp.`LatLong`) Latitude
            , ST_X(sp.`LatLong`) Longitude
            , sp.FirstPlantingYear
            , sp.YearPlantingCurrent
            , sp.PlotNr
            , CASE
                WHEN sp.SoilType = 1 THEN 'Mineral'
                WHEN sp.SoilType = 2 THEN 'Peat'
                WHEN sp.SoilType = 3 THEN 'Sandy'
                ELSE '-'
              END AS 'SoilType'
            , CASE 
                WHEN sp.OwnershipDoc = 1 THEN 'No Document'
                WHEN sp.OwnershipDoc = 2 THEN 'SKT (Surat Keterangan Tanah)'
                WHEN sp.OwnershipDoc = 3 THEN 'SHM (Sertifikat Hak Milik)/Certificate'
                WHEN sp.OwnershipDoc = 4 THEN 'HGU (Hak Guna Usaha)'
                WHEN sp.OwnershipDoc = 5 THEN 'SKGR (Surat Keterangan Ganti Rugi)' 
                ELSE sp.OwnershipDocText
            END AS 'OwnershipDocument'
            , sp.AnnualProduction
            , sp.PlantationProductivity                
        FROM
            ktv_members sub_a
            LEFT JOIN ktv_tc_supplychain_org ktso ON ktso.`ObjID` = sub_a.`MemberID` 
            AND ktso.ObjType = 'agent'
            LEFT JOIN ktv_tc_supplychain_farmer ktsf ON ktsf.`SupplychainID` = ktso.`SupplychainID`
            LEFT JOIN ktv_members sf ON sf.MemberID = ktsf.FarmerID
            LEFT JOIN ktv_member_role kmr ON sub_a.MemberID = kmr.MemberID
            LEFT JOIN ktv_ref_member_role rm ON rm.`MRoleID` = kmr.`MRoleID`
            LEFT JOIN ktv_members sm on sm.MemberID = sub_a.MemberID
            LEFT JOIN ktv_members_extension me on me.MemberID = sm.MemberID
            LEFT JOIN ktv_survey_plot sp on sp.MemberID = sf.MemberID
            LEFT JOIN ktv_village v on v.VillageID = sp.VillageID
            LEFT JOIN ktv_subdistrict sd on sd.SubDistrictID = v.SubDistrictID
            LEFT JOIN ktv_district d on d.DistrictID = sd.DistrictID
            LEFT JOIN ktv_province p on p.ProvinceID = d.ProvinceID
        WHERE
            sub_a.StatusCode = 'active' 
            AND sf.StatusCode = 'active' 
            AND rm.`MRoleType` = 'Agent'
            AND sub_a.MemberID = ?        
            AND sp.MemberID IS NOT NULL
        GROUP BY
            CONCAT(sp.MemberID,sp.PlotNr, sp.SurveyNr)";
        $query = $this->db->query($sql,$MemberID);
        $result['data'] = $query->result_array();
//        $result['query'] = $this->db->last_query();
        // $tmp = $this->db->last_query();
        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    public function getTraderFarmer($MemberID){
        $sql = "SELECT
        -- 	sub_a.`MemberID` AS MemberID,
        -- 	COUNT( DISTINCT ktsf.`FarmerID` ) AS NrFarmer
            sm.MemberDisplayID SMEID
            , me.agCompanyName SMEName
            , sm.Alias
            , sf.MemberDisplayID FarmerID
            , sf.MemberName FarmerName
            , COUNT(DISTINCT CONCAT(sp.MemberID,sp.PlotNr,sp.SurveyNr)) GardenNr
            , SUM(sp.GardenAreaHa) GardenAreaHa
            , sf.DateOfBirth
            , sf.Nin
            , sf.Handphone
            , p.Province
            , d.District
            , sd.SubDistrict
            , v.Village
            , ST_Y(sf.`LatLong`) Latitude
            , ST_X(sf.`LatLong`) Longitude
            , sp.FirstPlantingYear
            , sp.YearPlantingCurrent
            , CASE
                WHEN sp.SoilType = 1 THEN 'Mineral'
                WHEN sp.SoilType = 2 THEN 'Peat'
                WHEN sp.SoilType = 3 THEN 'Sandy'
                ELSE '-'
              END AS 'SoilType'
            , CASE 
                WHEN sp.OwnershipDoc = 1 THEN 'No Document'
                WHEN sp.OwnershipDoc = 2 THEN 'SKT (Surat Keterangan Tanah)'
                WHEN sp.OwnershipDoc = 3 THEN 'SHM (Sertifikat Hak Milik)/Certificate'
                WHEN sp.OwnershipDoc = 4 THEN 'HGU (Hak Guna Usaha)'
                WHEN sp.OwnershipDoc = 5 THEN 'SKGR (Surat Keterangan Ganti Rugi)' 
                ELSE sp.OwnershipDocText
            END AS 'OwnershipDocument'
            , sp.AnnualProduction
            , sp.PlantationProductivity                
        FROM
            ktv_members sub_a
            LEFT JOIN ktv_tc_supplychain_org ktso ON ktso.`ObjID` = sub_a.`MemberID` 
            AND ktso.ObjType = 'agent'
            LEFT JOIN ktv_tc_supplychain_farmer ktsf ON ktsf.`SupplychainID` = ktso.`SupplychainID`
            LEFT JOIN ktv_members sf ON sf.MemberID = ktsf.FarmerID
            LEFT JOIN ktv_village v on v.VillageID = sf.VillageID
            LEFT JOIN ktv_subdistrict sd on sd.SubDistrictID = v.SubDistrictID
            LEFT JOIN ktv_district d on d.DistrictID = sd.DistrictID
            LEFT JOIN ktv_province p on p.ProvinceID = d.ProvinceID
            LEFT JOIN ktv_member_role kmr ON sub_a.MemberID = kmr.MemberID
            LEFT JOIN ktv_ref_member_role rm ON rm.`MRoleID` = kmr.`MRoleID`
            LEFT JOIN ktv_members sm on sm.MemberID = sub_a.MemberID
            LEFT JOIN ktv_members_extension me on me.MemberID = sm.MemberID
            LEFT JOIN ktv_survey_plot sp on sp.MemberID = sf.MemberID
        WHERE
            sub_a.StatusCode = 'active' 
            AND sf.StatusCode = 'active' 
            AND rm.`MRoleType` = 'Agent'
            AND sub_a.MemberID = ?
        GROUP BY
            ktsf.`FarmerID`";
        $query = $this->db->query($sql,$MemberID);
        $result['data'] = $query->result_array();
//        $result['query'] = $this->db->last_query();
        // $tmp = $this->db->last_query();
        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    public function getFarmersBySupplierExcel($pSearch) {
        $pSearch = array_merge([
            'prov' => '', 'kab' => '', 'kec' => '', 'textSearch' => '', 'textSearchDesa' => '',
            'roleSearch' => '', 'source' => '',
            'AdvRowHandphone' => '', 'AdvTextHandphone' => '',
            'AdvRowAge' => '', 'AdvOpAge' => '', 'AdvTextAge' => '',
        ], $pSearch);

        $sqlFilter = "";
        if ($pSearch['prov'] != "")          $sqlFilter .= " AND kp_sme.ProvinceID = " . (int)$pSearch['prov'];
        if ($pSearch['kab'] != "")           $sqlFilter .= " AND kd_sme.DistrictID = " . (int)$pSearch['kab'];
        if ($pSearch['kec'] != "")           $sqlFilter .= " AND ksd_sme.SubDistrictID = " . (int)$pSearch['kec'];
        if ($pSearch['textSearch'] != "")    $sqlFilter .= " AND (a.MemberName LIKE '%{$pSearch['textSearch']}%' OR a.MemberDisplayID LIKE '%{$pSearch['textSearch']}%' OR x.agCompanyName LIKE '%{$pSearch['textSearch']}%')";
        if ($pSearch['textSearchDesa'] != "") $sqlFilter .= " AND kv_sme.Village LIKE '%{$pSearch['textSearchDesa']}%'";
        if ($pSearch['AdvRowHandphone'] == "true") $sqlFilter .= " AND a.HandPhone LIKE '%{$pSearch['AdvTextHandphone']}%'";
        if ($pSearch['AdvRowAge'] == "true" && $pSearch['AdvOpAge'] != "" && $pSearch['AdvTextAge'] != "")
            $sqlFilter .= " AND (a.DateOfBirth IS NOT NULL AND a.DateOfBirth != '0000-00-00') AND TIMESTAMPDIFF(YEAR, a.DateOfBirth, CURDATE()) {$pSearch['AdvOpAge']} {$pSearch['AdvTextAge']}";

        $sqlFilterRole = " AND sub_b.MRoleID IN (5,6,7,8,9,10,11,12,13,14)";
        if ($pSearch['roleSearch'] != "" && !str_contains($pSearch['roleSearch'], 'all'))
            $sqlFilterRole = " AND sub_b.MRoleID IN ({$pSearch['roleSearch']})";

        $sqlHakAkses = $this->generateSqlHakAkses();

        $sql = "SELECT
                a.MemberDisplayID AS SMEID
                , IFNULL(x.agCompanyName, a.MemberName) AS SMECompanyName
                , a.MemberName AS SMEName
                , a.Alias AS SMEAlias
                , sf.MemberDisplayID AS FarmerID
                , sf.MemberName AS FarmerName
                , COUNT(DISTINCT CONCAT(sp.MemberID, sp.PlotNr, sp.SurveyNr)) AS GardenNr
                , SUM(sp.GardenAreaHa) AS GardenAreaHa
                , sf.DateOfBirth
                , sf.Nin
                , sf.HandPhone AS Handphone
                , kp.Province
                , kd.District
                , ksd.SubDistrict
                , kv.Village
                , ST_Y(sf.LatLong) AS Latitude
                , ST_X(sf.LatLong) AS Longitude
            FROM ktv_members a
                INNER JOIN (
                    SELECT sub_a.MemberID
                    FROM ktv_members sub_a
                        LEFT JOIN ktv_member_role sub_b ON sub_a.MemberID = sub_b.MemberID
                    WHERE sub_a.StatusCode = 'active'
                        $sqlFilterRole
                    GROUP BY sub_a.MemberID
                ) AS tmp_member_filter ON a.MemberID = tmp_member_filter.MemberID
                {$sqlHakAkses['join']}
                LEFT JOIN ktv_members_extension x ON a.MemberID = x.MemberID
                LEFT JOIN ktv_village kv_sme ON kv_sme.VillageID = a.VillageID
                LEFT JOIN ktv_subdistrict ksd_sme ON ksd_sme.SubDistrictID = kv_sme.SubDistrictID
                LEFT JOIN ktv_district kd_sme ON kd_sme.DistrictID = ksd_sme.DistrictID
                LEFT JOIN ktv_province kp_sme ON kp_sme.ProvinceID = kd_sme.ProvinceID
                LEFT JOIN ktv_tc_supplychain_org ktso ON ktso.ObjID = a.MemberID AND ktso.ObjType = 'agent'
                LEFT JOIN ktv_tc_supplychain_farmer ktsf ON ktsf.SupplychainID = ktso.SupplychainID
                INNER JOIN ktv_members sf ON sf.MemberID = ktsf.FarmerID AND sf.StatusCode = 'active'
                LEFT JOIN ktv_village kv ON kv.VillageID = sf.VillageID
                LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
                LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
                LEFT JOIN ktv_province kp ON kp.ProvinceID = kd.ProvinceID
                LEFT JOIN ktv_survey_plot sp ON sp.MemberID = sf.MemberID
            WHERE a.StatusCode = 'active'
                $sqlFilter
                {$sqlHakAkses['where']}
            GROUP BY a.MemberID, sf.MemberID
            ORDER BY a.MemberName, sf.MemberName";

        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function getFarmersBySupplierGardenExcel($pSearch) {
        $pSearch = array_merge([
            'prov' => '', 'kab' => '', 'kec' => '', 'textSearch' => '', 'textSearchDesa' => '',
            'roleSearch' => '', 'source' => '',
            'AdvRowHandphone' => '', 'AdvTextHandphone' => '',
            'AdvRowAge' => '', 'AdvOpAge' => '', 'AdvTextAge' => '',
        ], $pSearch);

        $sqlFilter = "";
        if ($pSearch['prov'] != "")          $sqlFilter .= " AND kp_sme.ProvinceID = " . (int)$pSearch['prov'];
        if ($pSearch['kab'] != "")           $sqlFilter .= " AND kd_sme.DistrictID = " . (int)$pSearch['kab'];
        if ($pSearch['kec'] != "")           $sqlFilter .= " AND ksd_sme.SubDistrictID = " . (int)$pSearch['kec'];
        if ($pSearch['textSearch'] != "")    $sqlFilter .= " AND (a.MemberName LIKE '%{$pSearch['textSearch']}%' OR a.MemberDisplayID LIKE '%{$pSearch['textSearch']}%' OR x.agCompanyName LIKE '%{$pSearch['textSearch']}%')";
        if ($pSearch['textSearchDesa'] != "") $sqlFilter .= " AND kv_sme.Village LIKE '%{$pSearch['textSearchDesa']}%'";
        if ($pSearch['AdvRowHandphone'] == "true") $sqlFilter .= " AND a.HandPhone LIKE '%{$pSearch['AdvTextHandphone']}%'";
        if ($pSearch['AdvRowAge'] == "true" && $pSearch['AdvOpAge'] != "" && $pSearch['AdvTextAge'] != "")
            $sqlFilter .= " AND (a.DateOfBirth IS NOT NULL AND a.DateOfBirth != '0000-00-00') AND TIMESTAMPDIFF(YEAR, a.DateOfBirth, CURDATE()) {$pSearch['AdvOpAge']} {$pSearch['AdvTextAge']}";

        $sqlFilterRole = " AND sub_b.MRoleID IN (5,6,7,8,9,10,11,12,13,14)";
        if ($pSearch['roleSearch'] != "" && !str_contains($pSearch['roleSearch'], 'all'))
            $sqlFilterRole = " AND sub_b.MRoleID IN ({$pSearch['roleSearch']})";

        $sqlHakAkses = $this->generateSqlHakAkses();

        $sql = "SELECT
                a.MemberDisplayID AS SMEID
                , IFNULL(x.agCompanyName, a.MemberName) AS SMECompanyName
                , a.MemberName AS SMEName
                , a.Alias AS SMEAlias
                , sf.MemberDisplayID AS FarmerID
                , sf.MemberName AS FarmerName
                , sp.PlotNr
                , sp.GardenAreaHa
                , sp.FirstPlantingYear
                , sp.YearPlantingCurrent
                , CASE
                    WHEN sp.SoilType = 1 THEN 'Mineral'
                    WHEN sp.SoilType = 2 THEN 'Peat'
                    WHEN sp.SoilType = 3 THEN 'Sandy'
                    ELSE '-'
                  END AS SoilType
                , CASE
                    WHEN sp.OwnershipDoc = 1 THEN 'No Document'
                    WHEN sp.OwnershipDoc = 2 THEN 'SKT (Surat Keterangan Tanah)'
                    WHEN sp.OwnershipDoc = 3 THEN 'SHM (Sertifikat Hak Milik)/Certificate'
                    WHEN sp.OwnershipDoc = 4 THEN 'HGU (Hak Guna Usaha)'
                    WHEN sp.OwnershipDoc = 5 THEN 'SKGR (Surat Keterangan Ganti Rugi)'
                    ELSE sp.OwnershipDocText
                  END AS OwnershipDocument
                , sp.AnnualProduction
                , sp.PlantationProductivity
                , kp.Province
                , kd.District
                , ksd.SubDistrict
                , kv.Village
                , ST_Y(sp.LatLong) AS Latitude
                , ST_X(sp.LatLong) AS Longitude
            FROM ktv_members a
                INNER JOIN (
                    SELECT sub_a.MemberID
                    FROM ktv_members sub_a
                        LEFT JOIN ktv_member_role sub_b ON sub_a.MemberID = sub_b.MemberID
                    WHERE sub_a.StatusCode = 'active'
                        $sqlFilterRole
                    GROUP BY sub_a.MemberID
                ) AS tmp_member_filter ON a.MemberID = tmp_member_filter.MemberID
                {$sqlHakAkses['join']}
                LEFT JOIN ktv_members_extension x ON a.MemberID = x.MemberID
                LEFT JOIN ktv_village kv_sme ON kv_sme.VillageID = a.VillageID
                LEFT JOIN ktv_subdistrict ksd_sme ON ksd_sme.SubDistrictID = kv_sme.SubDistrictID
                LEFT JOIN ktv_district kd_sme ON kd_sme.DistrictID = ksd_sme.DistrictID
                LEFT JOIN ktv_province kp_sme ON kp_sme.ProvinceID = kd_sme.ProvinceID
                LEFT JOIN ktv_tc_supplychain_org ktso ON ktso.ObjID = a.MemberID AND ktso.ObjType = 'agent'
                LEFT JOIN ktv_tc_supplychain_farmer ktsf ON ktsf.SupplychainID = ktso.SupplychainID
                INNER JOIN ktv_members sf ON sf.MemberID = ktsf.FarmerID AND sf.StatusCode = 'active'
                INNER JOIN ktv_survey_plot sp ON sp.MemberID = sf.MemberID
                LEFT JOIN ktv_village kv ON kv.VillageID = sp.VillageID
                LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
                LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
                LEFT JOIN ktv_province kp ON kp.ProvinceID = kd.ProvinceID
            WHERE a.StatusCode = 'active'
                $sqlFilter
                {$sqlHakAkses['where']}
            GROUP BY CONCAT(sp.MemberID, sp.PlotNr, sp.SurveyNr)
            ORDER BY a.MemberName, sf.MemberName, sp.PlotNr";

        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function getGridMainTrader($pSearch,$start,$limit,$sortingField,$sortingDir){
        $pSearch = array_merge([
            'prov' => '', 'kab' => '', 'kec' => '', 'textSearch' => '', 'textSearchDesa' => '',
            'roleSearch' => '', 'categorySearch' => '', 'source' => '',
            'AdvRowHandphone' => '', 'AdvTextHandphone' => '',
            'AdvRowAge' => '', 'AdvOpAge' => '', 'AdvTextAge' => '',
            'AdvRowEnumerator' => '', 'AdvTextEnumerator' => '',
            'AdvRowDateCollection' => '', 'AdvDateCollectionBegin' => '', 'AdvDateCollectionEnd' => '',
        ], $pSearch);
        $sqlFilter = "";
        $sqlFilterMill = "";
        $sqlFilterRole = "";

        //BENTUK QUERY FILTER =============================================== (BEGIN)
        if($pSearch['prov'] != ""){
            $sqlFilter .= " AND kp.ProvinceID = ".$pSearch['prov'];
        }

        if($pSearch['kab'] != ""){
            $sqlFilter .= " AND kd.DistrictID = ".$pSearch['kab'];
        }

        if($pSearch['kec'] != ""){
            $sqlFilter .= " AND ksd.SubDistrictID = ".$pSearch['kec'];
        }

        if($pSearch['textSearch'] != ""){
            $sqlFilter .= " AND (a.MemberName like '%{$pSearch['textSearch']}%' OR a.MemberDisplayID like '%{$pSearch['textSearch']}%'  OR x.agCompanyName like '%{$pSearch['textSearch']}%' ) ";
        }

        if($pSearch['textSearchDesa'] != ""){
            $sqlFilter .= " AND kv.Village like '%{$pSearch['textSearchDesa']}%'";
        }
		
        //filter role
        if (str_contains($pSearch['roleSearch'], 'all') OR $pSearch['roleSearch'] == '') {
            
            $sqlFilterRole .= ($pSearch['source'] == 'mill') ? " AND sub_b.MRoleID IN (5,6,7,8,9,10,11,12,14) " : " AND sub_b.MRoleID IN (5,6,7,8,9,10,11,12,13,14)"; //semua role nya Agent
        }else{
            $sqlFilterRole .= " AND sub_b.MRoleID IN ({$pSearch['roleSearch']}) ";
        }

        //advanced filter
        if($pSearch['AdvRowHandphone'] == "true"){
            $sqlFilter .= " AND a.HandPhone LIKE '%{$pSearch['AdvTextHandphone']}%' ";
        }

        if($pSearch['AdvRowAge'] == "true"){
            if($pSearch['AdvOpAge'] != "" && $pSearch['AdvTextAge'] != ""){
                $sqlFilter .= " AND (a.`DateOfBirth` IS NOT NULL AND a.`DateOfBirth` != '0000-00-00')
                                AND TIMESTAMPDIFF(YEAR, a.DateOfBirth, CURDATE()) " . $pSearch['AdvOpAge'] . " " . $pSearch['AdvTextAge'];
            }
        }
        //BENTUK QUERY FILTER =============================================== (END)

        //Bentuk SQL Hak Akses
        $sqlHakAkses = $this->generateSqlHakAkses();

        if($sortingField == "") $sortingField = 'Name';
        if($sortingDir == "") $sortingDir = 'ASC';

        if($limit > 0){
            $limitwhere = " LIMIT ?,?";
            $p = array(
                (int) $start, (int) $limit
            );
        }else{
            $limitwhere = "";
            $p = array();
        }

        $is_admin  = isset($_SESSION['is_admin']) ? $_SESSION['is_admin'] : '0';
        $role      = isset($_SESSION['role'])     ? $_SESSION['role']     : '';
        $PartnerID = isset($_SESSION['PartnerID'])? $_SESSION['PartnerID']: '';
        if($is_admin == "1"){
            $sqlFilterMill = "";
        } elseif ($role == "Private" || $role == "Program"){
            $sqlFilterMill = " AND ktso.PartnerID = '{$PartnerID}'";
        }

        $sql="SELECT
                SQL_CALC_FOUND_ROWS
				x.agCompanyName  
                ,a.MemberID AS MemberIDInc
                , a.`MemberDisplayID` AS id
                , a.`MemberName` AS Name 
                , sub_mill.MillName
                , sub_mill.MillName2
                , a.Latitude
                , a.Longitude
				, a.Alias 
                , kv.`Village` AS Desa
                , ksd.`SubDistrict` AS Kecamatan
                , a.DateUpdated AS LastUpdated
                , (SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy`) AS Enumerator
                , kp.Province
                , kd.District
                , a.`DateOfBirth` AS Birthdate
                , FLOOR(DATEDIFF(CURDATE(), a.DateOfBirth) / 365.25) AS Age
                , DATE_FORMAT(a.`DateCollection`,'%Y-%m-%d') AS DateCollection
                , a.HandPhone AS Handphone
                , CASE
                    WHEN a.MaritalStatus = '1' THEN '".lang('Married')."'
                    WHEN a.MaritalStatus = '2' THEN '".lang('Single')."'
                    WHEN a.MaritalStatus = '3' THEN '".lang('Janda/Duda')."'
                END AS MaritalStatus
                , GROUP_CONCAT(rrole.MRoleName SEPARATOR ', ') AS MemberRole
                , GROUP_CONCAT(DISTINCT rrole.MRoleName SEPARATOR ', ') AS StatusSME
                , GROUP_CONCAT(mtype.SMETypeID SEPARATOR ',') AS MemberTypeID
                , GROUP_CONCAT(DISTINCT a.Latitude, ',', a.Longitude) AS GPS
                , sub_farmer.NrFarmer AS NrFarmer
                , GROUP_CONCAT(sub_mill.MillName SEPARATOR ', ') AS MillName
                -- , IF(GROUP_CONCAT(sub_mill.MillName SEPARATOR ', ') <> '','Vendor','Agent') StatusSME
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
                LEFT JOIN ktv_member_sme_type mtype ON a.MemberID = mtype.MemberID
                LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
                LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
                LEFT JOIN ktv_province kp ON kp.ProvinceID = kd.ProvinceID
				LEFT JOIN ktv_members_extension x ON a.MemberID = x.MemberID

                LEFT JOIN(
                    SELECT
                        sub_a.`MemberID` AS MemberID
                        , COUNT(DISTINCT ktsf.`FarmerID`) AS NrFarmer
                    FROM ktv_members sub_a  
                        LEFT JOIN ktv_tc_supplychain_org ktso ON ktso.`ObjID` = sub_a.`MemberID` AND ktso.ObjType = 'agent'
                        LEFT JOIN ktv_tc_supplychain_farmer ktsf ON ktsf.`SupplychainID` = ktso.`SupplychainID`
                        LEFT JOIN ktv_members sub_farmer ON sub_farmer.MemberID = ktsf.FarmerID 
                        LEFT JOIN ktv_member_role kmr ON sub_a.MemberID = kmr.MemberID 
                        LEFT JOIN ktv_ref_member_role rm ON rm.`MRoleID`=kmr.`MRoleID`
                    WHERE
                        sub_a.StatusCode = 'active'
                        AND sub_farmer.StatusCode = 'active'
                        AND rm.`MRoleType`='Agent'
                    GROUP BY 
                        sub_a.`MemberID`
                ) sub_farmer on sub_farmer.MemberID = a.`MemberID`

                LEFT JOIN(
                    SELECT
                        kmember.`MemberID` AS MemberID
                        , group_CONCAT(distinct km.`MillName`) AS MillName
                        , GROUP_CONCAT(DISTINCT REPLACE(km.MillName, ' ', '')) MillName2
                    FROM ktv_members kmember
                        LEFT JOIN ktv_tc_supplychain_org ktso2 ON ktso2.`ObjID` = kmember.`MemberID` AND ktso2.`ObjType` = 'agent'
                        LEFT JOIN ktv_tc_supplychain_org_rel ktsor ON ktsor.`ChildID` = ktso2.`SupplychainID`
                        LEFT JOIN ktv_tc_supplychain_org ktso ON ktso.`SupplychainID` = ktsor.`ParentID`
                        LEFT JOIN ktv_mill km on km.`MillID` = ktso.`ObjID` AND ktso.`ObjType` = 'mill'
                        LEFT JOIN ktv_member_role kmr ON kmember.MemberID = kmr.MemberID 
                        LEFT JOIN ktv_ref_member_role rm ON rm.`MRoleID`= kmr.`MRoleID`
                    WHERE
                        kmember.`StatusCode` = 'active'
                        AND ktsor.`StatusCode` = 'active'
                        AND rm.`MRoleType` = 'Agent'
                        $sqlFilterMill
                    GROUP BY
                        kmember.`MemberID`
                ) sub_mill ON sub_mill.MemberID = a.`MemberID`

                {$sqlHakAkses['join']}
            WHERE
                a.`StatusCode` = 'active'
                $sqlFilter
                {$sqlHakAkses['where']}
            GROUP BY a.MemberID
            ORDER BY $sortingField $sortingDir
            $limitwhere
            ";
        $query = $this->db->query($sql,$p);
        $result['data'] = $query->result_array();
       $result['query'] = $this->db->last_query();
        // $tmp = $this->db->last_query();
        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        //generate information grid result (begin)
        if($sortingDir == 'ASC'){
            $sortingInfo = 'ascending';
        }
        if($sortingDir == 'DESC'){
            $sortingInfo = 'descending';
        }

        $infoFilter = '';
        foreach ($pSearch as $key => $value) {
            if($value != ""){
                switch ($key) {
                    case 'prov':
                        $infoFilter .= '<li>'.lang('Filter by').' '.lang('Province').'</li>';
                    break;
                    case 'kab':
                        $infoFilter .= '<li>'.lang('Filter by').' '.lang('District').'</li>';
                    break;
                    case 'kec':
                        $infoFilter .= '<li>'.lang('Filter by').' '.lang('Kecamatan').'</li>';
                    break;
                    case 'textSearch':
                        $infoFilter .= '<li>'.lang('Filter by').' '.lang('ID / Name').'</li>';
                    break;
                }
            }

            if($value == "true"){
                switch ($key) {
                    case 'AdvRowHandphone':
                        $infoFilter .= '<li>'.lang('Filter by').' '.lang('HandPhone').'</li>';
                    break;
                    case 'AdvRowAge':
                        $infoFilter .= '<li>'.lang('Filter by').' '.lang('Age').'</li>';
                    break;
                }
            }
        }

        $_SESSION['informationGrid'] = '<div class="gridInformationContainer">
                                <h4>Information</h4>
                                <ul>
                                    <li>'.$query->row()->total.' '.lang('datas, Sorted by').' '.lang($sortingField).' '.$sortingInfo.'</li>
                                    '.$infoFilter.'
                                </ul>
                            </div>';
        //generate information grid result (end)
		// print_r($tmp);die;
        return $result;
    }

    public function getMemberBasicDataForm($MemberID){
        $this->load->library('awsfileupload');
        $sql="SELECT
                a.`MemberID` AS \"Koltiva.view.SME.FormMainTrader-MemberID\"
                , a.`MemberDisplayID` AS \"Koltiva.view.SME.FormMainTrader-MemberDisplayID\" 
                , a.`DateCollection` AS \"Koltiva.view.SME.FormMainTrader-DateCollection\"
                , a.`MemberName` AS \"Koltiva.view.SME.FormMainTrader-Fullname\"
				, a.`Alias` AS \"Koltiva.view.SME.FormMainTrader-agAliasName\"
                , a.`DateOfBirth` AS \"Koltiva.view.SME.FormMainTrader-DateOfBirth\"
                , a.`Gender` AS \"Koltiva.view.SME.FormMainTrader-Gender\"
                , a.`Gender`
                , a.`MaritalStatus` AS \"Koltiva.view.SME.FormMainTrader-MaritalStatus\"
                , a.`Education` AS \"Koltiva.view.SME.FormMainTrader-Education\" 
                , b.`MRoleID` AS \"Koltiva.view.SME.FormMainTrader-smerole\" 
                , SUBSTR(a.`VillageID`,1,2) AS \"Province\"
                , SUBSTR(a.`VillageID`,1,4) AS \"District\"
                , SUBSTR(a.`VillageID`,1,7) AS \"Subdistrict\"
                , a.`VillageID` AS \"Village\"
                , a.`Address` AS \"Koltiva.view.SME.FormMainTrader-Address\"
                , a.`RtRw` AS \"Koltiva.view.SME.FormMainTrader-RtRw\"
                , a.`Handphone` AS \"Koltiva.view.SME.FormMainTrader-Handphone\"
				, a.`HandphoneType` AS \"Koltiva.view.SME.FormMainTrader-HandphoneType\"
                , a.`AccessToSmartPhone` AS \"Koltiva.view.SME.FormMainTrader-AccessToSmartPhone\"
                , a.`Photo` AS \"Koltiva.view.SME.FormMainTrader-MemberPhotoOld\"
                , a.Photo AS PhotoSrc
                , a.`StatusMember` AS \"Koltiva.view.SME.FormMainTrader-RbStatus\"
                , a.`InactiveReason` AS \"Koltiva.view.SME.FormMainTrader-InactiveReason\"
                , GROUP_CONCAT(b.`MRoleID` SEPARATOR ',') AS MemRole
                , a.Nin AS \"Koltiva.view.SME.FormMainTrader-Nin\"
                , a.Email AS \"Koltiva.view.SME.FormMainTrader-Email\"
				, a.Linked AS \"Koltiva.view.SME.FormMainTrader-Linked\"
				, a.Website AS \"Koltiva.view.SME.FormMainTrader-Website\"
				, a.Fax AS \"Koltiva.view.SME.FormMainTrader-Fax\"
				, a.Phone AS \"Koltiva.view.SME.FormMainTrader-Phone\"
                , IFNULL(a.Latitude,ST_Y(a.LatLong)) AS \"Koltiva.view.SME.FormMainTrader-Latitude\"
                , IFNULL(a.Longitude, ST_X(a.LatLong)) AS \"Koltiva.view.SME.FormMainTrader-Longitude\"
                , a.inGroup AS \"Koltiva.view.SME.FormMainTrader-inGroup\"
                , a.groupName AS \"Koltiva.view.SME.FormMainTrader-groupName\"
                , a.inCoop AS \"Koltiva.view.SME.FormMainTrader-inCoop\"
                , a.CoopName AS \"Koltiva.view.SME.FormMainTrader-CoopName\"
                , a.inGapoktan AS \"Koltiva.view.SME.FormMainTrader-inGapoktan\"
                , a.GapoktanName AS \"Koltiva.view.SME.FormMainTrader-GapoktanName\"
                , a.HowManyPlantation AS \"Koltiva.view.SME.FormMainTrader-HowManyPlantation\"
                , a.BankBeneficiary AS \"Koltiva.view.SME.FormMainTrader-BankBeneficiary\"
                , a.BankID AS \"Koltiva.view.SME.FormMainTrader-BankID\"
                , a.BankBranchName AS \"Koltiva.view.SME.FormMainTrader-BankBranchName\"
                , a.BankAccNumber AS \"Koltiva.view.SME.FormMainTrader-BankAccNumber\"
                , c.agLegalStatusCompany AS \"Koltiva.view.SME.FormMainTrader-agLegalStatusCompany\"
                , c.agCompanyName AS \"Koltiva.view.SME.FormMainTrader-agCompanyName\"
                , c.agYearEstablished AS \"Koltiva.view.SME.FormMainTrader-agYearEstablished\"
                , c.agBusinessLocation AS PhotoBusinessLocation
                , c.agCompanyLogo AS PhotoCompanyLogo
                , c.agCompanyLogo AS \"Koltiva.view.SME.FormMainTrader-agCompanyLogoOld\"
                , a.PartnerID
                , s.PartnerSurvey
                , vso.SupplychainID
            FROM
                ktv_members a
                LEFT JOIN ktv_member_role b ON a.`MemberID` = b.`MemberID`
                LEFT JOIN ktv_members_extension c ON a.MemberID = c.MemberID
                LEFT JOIN view_tc_supplychain_org vso on vso.ObjID = a.MemberID
                LEFT JOIN (
                    SELECT apmMemberID MemberID, GROUP_CONCAT(apmPartnerID SEPARATOR ',') PartnerSurvey FROM ktv_access_partner_member WHERE apmMemberID=?
                ) s ON s.MemberID=a.MemberID
            WHERE
                a.`MemberID` = ?
            GROUP BY a.`MemberID`
            LIMIT 1";
        $query = $this->db->query($sql,array((int) $MemberID,(int) $MemberID));
        $data = $query->row_array();        
        if($this->awsfileupload->doesObjectExist($data['PhotoSrc']) == true) {
            $data['MemberPhotoOld'] = $data['PhotoSrc'];
            $data['PhotoSrc'] = $this->config->item('CTCDN')."/".$data['PhotoSrc'];
        }else{
            $data['MemberPhotoOld'] = '/images/trader/'.$data["Province"].'/'.$data['PhotoSrc'];
            $data['PhotoSrc'] = base_url().'/images/trader/'.$data["Province"].'/'.$data['PhotoSrc'];
        }        

        if($this->awsfileupload->doesObjectExist($data['PhotoCompanyLogo']) == true) {
            $data['agCompanyLogoOld'] = $data['PhotoCompanyLogo'];
            $data['agCompanyLogo'] = $this->config->item('CTCDN')."/".$data['PhotoCompanyLogo'];
        }else{
            $data['agCompanyLogoOld'] = '/images/trader/'.$data["Province"].'/'.$data['PhotoCompanyLogo'];
            $data['agCompanyLogo'] = base_url().'/images/trader/'.$data["Province"].'/'.$data['PhotoCompanyLogo'];
        }
        $tmp = $this->db->last_query();

        $sqlrole = "Select a.MRoleID, b.MRoleName from ktv_member_role as a
        LEFT JOIN ktv_ref_member_role as b ON a.MRoleID = b.MRoleID
        where a.MemberID = ?";
        $queryrole = $this->db->query($sqlrole, array((int) $MemberID));
        $datarole = $queryrole->result_array();

        $arrTmp = explode(",",$data['MemRole']);
        $arrSurvey = array();
        foreach ($arrTmp as $key => $value) {
            switch ($value) {
                case '5':
                    $data['Koltiva.view.SME.FormMainTrader-CbRoleTrader'] = "1";
                    $arrSurvey[] = 5;
                break; 
            }
        }
        if(!empty($datarole)){           
            foreach ($datarole as $arrdata => $arrval) {
                $keyNewx = "Koltiva.view.SME.FormMainTrader-CmbSmeRole";
                $data[$keyNewx][] = $arrval["MRoleID"];
            }
        }
        
        $sqlsme = "Select a.SMETypeID, b.SMEType from ktv_member_sme_type as a
            LEFT JOIN ktv_ref_sme_type as b ON a.SMETypeID = b.SMETypeID
            where a.MemberID = ?";
        $querysme = $this->db->query($sqlsme, array((int) $MemberID));
        $datasme = $querysme->result_array();

        if(!empty($datasme)){
            foreach ($datasme as $arrdata => $arrval) {
                $keyNewx = "Koltiva.view.SME.FormMainTrader-CmbSmeType";
                $data[$keyNewx][] = $arrval["SMETypeID"];
            }
        }
        $data['ArrSurID'] = $arrSurvey;

        $sqlvillage = "
            Select a.VillageID, CONCAT(v.Village, ' > ', sd.`SubDistrict`, ' > ', d.District) label 
                from ktv_member_work_area as a
            LEFT JOIN ktv_village v on v.VillageID = a.VillageID
            LEFT JOIN ktv_subdistrict sd on sd.SubDistrictID = v.SubDistrictID
            LEFT JOIN ktv_district d on sd.DistrictID = d.DistrictID
            where a.MemberID = ?";
        $querryvil = $this->db->query($sqlvillage, array((int) $MemberID));
        $datavil = $querryvil->result_array();
        if(!empty($datavil)){           
            foreach ($datavil as $arrdata => $arrval) {
                $keyNewx = "Koltiva.view.SME.FormMainTrader-cmbSMEVillage";
                $data[$keyNewx][] = $arrval["VillageID"];
            }
        }

        $return['success'] = true;
        $return['data'] = $data;
//        $return['debug'] = $tmp;
        return $return;
    }
    
    public function getGridPlotSurveySta($MemberID) {
        $sql="SELECT
                a.`PlotNr`
                , CONCAT(b.SurveyNr,' - ',b.`SurveyTxt`) AS Survey
                , a.`SurveyNr`
                , a.`DateCollection`
                , (SELECT UserRealName FROM sys_user WHERE UserId = a.`CreatedBy`) AS CreatedBy
                , CONCAT(
                    (SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy` LIMIT 1),
                    IF(a.`LastModifiedBy` IS NOT NULL OR a.`LastModifiedBy` != '',(SELECT CONCAT(', modified by ',sub_a.UserRealname) FROM sys_user sub_a WHERE sub_a.UserId = a.`LastModifiedBy` LIMIT 1),'')
                ) AS Enumerator
            FROM
                ktv_survey_plot_sme a
                LEFT JOIN ktv_survey b ON a.`SurveyNr` = b.`SurveyNr`
            WHERE
                a.`MemberID` = ?
                AND a.`StatusCode` = 'active'
            ORDER BY a.`PlotNr`, a.`SurveyNr`";
        $query = $this->db->query($sql,array((int) $MemberID));
        $data = $query->result_array();
        if($data[0]['PlotNr'] == "") $data = array();

        $return['data'] = $data;
        return $return;        
    }

    public function getPlotSurveyFormData($MemberID,$PlotNr,$SurveyNr,$DateCollection){
        $sql="SELECT
                a.`MemberID`,
                b.MemberUID,
                b.`MemberDisplayID`
                , b.`MemberName`
                ,a.`PlotNr`,
                a.`SurveyNr`,
                a.`DateCollection`,
                a.Certification,
                #(SELECT UserRealName FROM sys_user WHERE UserId = a.`CreatedBy`) AS CreatedByLabel,
                /*CONCAT(
                    (SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy` LIMIT 1),
                    IF(a.`LastModifiedBy` IS NOT NULL OR a.`LastModifiedBy` != '',(SELECT CONCAT(', modified by ',sub_a.UserRealname) FROM sys_user sub_a WHERE sub_a.UserId = a.`LastModifiedBy` LIMIT 1),'')
                ) AS CreatedByLabel,*/
                CONCAT((SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`CreatedBy` LIMIT 1),', ',a.DateCreated) AS CreatedByLabel,
                CONCAT((SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = a.`LastModifiedBy` LIMIT 1),', ',a.DateUpdated) AS ModifiedByLabel,
                SUBSTR(a.`VillageID`,1,2) AS ProvinceID,
                d.`DistrictID`,
                c.`SubDistrictID`,
                a.`VillageID`,
                a.`PhotoOfVisit`,
                a.`GardenAreaHa`,
                a.PlantedAreaHa,
                a.GardenAreaPolygon,
                a.`GardenLength`,
                a.`GardenWidth`,
                ST_Y(a.`LatLong`) AS Latitude,
                ST_X(a.`LatLong`) AS Longitude,
                a.North,
                a.East,
                a.South,
                a.West,
                a.`OwnershipDoc`,
                a.DocumentNumber,
                a.OwnershipDocText,
                a.LandOwnershipType,
                a.OwnerOfTheGarden,
                a.OwnerOfPlantationNameText,
                a.OwnerOfPlantationLocationText,
                a.OwnerOfPlantationPhoneText,
                a.`BusinessModel`,
                a.PlantingType,
                a.ManageType,
                a.`PlantationConditionEst`,
                a.`AverageAgeTree`,
                a.`SoilType`,
                a.`TopographyType`,
                a.FirstPlantingYear,
                a.TreeTBM,
                a.TreeTM,
                a.TreeTR,
                a.`TypePlantMateMarihat`,
                a.`TypePlantMateMarihatNr`,
                a.`TypePlantMateDumpy`,
                a.`TypePlantMateDumpyNr`,
                a.`TypePlantMateLonsum`,
                a.`TypePlantMateLonsumNr`,
                a.`TypePlantMateSimalungun`,
                a.`TypePlantMateSimalungunNr`,
                a.`TypePlantMateDanimas`,
                a.`TypePlantMateDanimasNr`,
                a.`TypePlantMateSriwijaya`,
                a.`TypePlantMateSriwijayaNr`,
                a.`TypePlantMateSocfin`,
                a.`TypePlantMateSocfinNr`,
                a.`TypePlantMateOther`,
                a.`TypePlantMateOtherText`,
                a.`TypePlantMateOtherNr`,
                a.`TypePlantMateDoNotKnow`,
                a.TypePlantMateDoNotKnowNr,
                a.VarietyDura,
                a.VarietyTenera,
                a.VarietyPisifera,
                a.PercentagDura,
                a.`HarvestRateDaysHighSeason`,
                a.`HarvestRateDaysLowSeason`,
                a.`AverageProdHighSeason`,
                a.`AverageProdLowSeason`,
                a.NrHighSeasonMonths,
                a.NrLowSeasonMonths,
                a.HighSeasonProduction,
                a.LowSeasonProduction,
                a.AnnualProduction,
                a.`LeanHarvestSeasonJan`,
                a.`LeanHarvestSeasonFeb`,
                a.`LeanHarvestSeasonMar`,
                a.`LeanHarvestSeasonApr`,
                a.`LeanHarvestSeasonMay`,
                a.`LeanHarvestSeasonJun`,
                a.`LeanHarvestSeasonJul`,
                a.`LeanHarvestSeasonAug`,
                a.`LeanHarvestSeasonSep`,
                a.`LeanHarvestSeasonOct`,
                a.`LeanHarvestSeasonNov`,
                a.`LeanHarvestSeasonDec`,
                a.`WhoHarvestFamily`,
                a.`WhoHarvestLabor`,
                a.`UseEFBFertilizer`,
                a.`useParaquat`,
                a.TPHLoc,
                a.SubDistrictIDTPH,
                a.DistrictIDTPH,
                a.ProvinceIDTPH,
                a.Distance,
                a.`Comment`,
                a.OwnerDocIsOwner,
                a.HaveSTDB,
                a.HaveSPPL,
                a.HowObPlantation,
                a.HowObPlantationText,
                a.TypePlantMateOtherText,
                a.PhotoOfVisitDesc,
                a.OwnerCultivateFarm,
                a.FarmEmployHiredLabor,
                a.FarmEmployFamMem,
                a.FarmEmployLaborFamMem,
                a.FarmEmployNoLabor,
                a.HowManyWorkFarm,
                a.UnderAgeWorker,
                a.AveHoursPerDay,
                a.AveDaysPerMonth,
                a.WageNominalPerDayLabor,
                a.WageNominalPerDayLaborPeriod,
                a.WageNominalPerDayFamMember,
                a.WageNominalPerDayFamMemberPeriod,
                a.HowManyDiffBuyerSoldLastYear,
                a.HowManyDiffBuyerSoldLastYearText,
                a.ToWhoSellFFBLastYear,
                a.HowManyDiffMillSoldLastYear,
                a.HowManyDiffMillSoldLastYearText,
                a.ToWhichMillSellFFBLastYear,
                a.ToWhichMillSellFFBLastYearText,
                a.FertilizerDesc,
                a.FertilizerNotes,
                a.PesticideDesc,
                a.PesticideNotes,
                a.`FertNonOrganicData`,
                a.`FertMoneySpentNonOrganic`,
                a.`FertUreaTimesYear`,
                a.`FertUreaDose`,
                a.`FertSSTimesYear`,
                a.`FertSSDose`,
                a.`FertNPKTimesYear`,
                a.`FertNPKDose`,
                a.`FertTSPTimesYear`,
                a.`FertTSPDose`,
                a.`FertCUTimesYear`,
                a.`FertCUDose`,
                a.`FertKCLTimesYear`,
                a.`FertKCLDose`,
                a.`FertNPKMutiTimesYear`,
                a.`FertNPKMutiDose`,
                a.`FertBoratTimesYear`,
                a.`FertBoratDose`,
                a.`FertDolomiteTimesYear`,
                a.`FertDolomiteDose`,
                a.`FertWithNonOrgaTBM`,
                a.`FertWithNonOrgaTM`,
                a.`FertWithNonOrgaTR`,
                a.`FertUseOrganic`,
                a.`FertMoneySpentOrganic`,
                a.`FertPBATimesYear`,
                a.`FertPBADose`,
                a.`FertPBTimesYear`,
                a.`FertPBDose`,
                a.`FertCPBTimesYear`,
                a.`FertCPBDose`,
                a.`FertManureTimesYear`,
                a.`FertManureDose`,
                a.`FertWithOrgaTBM`,
                a.`FertWithOrgaTM`,
                a.`FertWithOrgaTR`,
                a.`PeUsingHerbicide`,
                a.`PeMoneySpentHerbi`,
                a.`PeFreqHerbi`,
                a.`PeDoseHerbi`,
                a.`PeHerbi1`,
                a.`PeHerbi2`,
                a.`PeHerbi3`,
                a.`PeHerbi4`,
                a.`PeHerbi5`,
                a.`PeHerbi6`,
                a.`PeHerbi7`,
                a.`PeHerbi8`,
                a.`PeHerbi9`,
                a.`PeHerbi10`,
                a.`PeHerbi11`,
                a.`PeHerbi12`,
                a.`PeHerbi13`,
                a.`PeHerbi14`,
                a.`PeHerbi15`,
                a.`PeHerbi16`,
                a.`PeHerbi17`,
                a.`PeHerbi18`,
                a.`PeHerbi19`,
                a.`PeHerbi20`,
                a.`PeHerbi21`,
                a.`PeHerbi22`,
                a.`PeHerbi23`,
                a.`PeHerbi24`,
                a.`PeHerbi25`,
                a.`PeHerbi26`,
                a.`PeHerbi27`,
                a.`PeHerbi28`,
                a.`PeHerbi29`,
                a.`PeHerbiOther`,
                a.`PeUsingInsecticide`,
                a.`PeMoneySpentInsec`,
                a.`PeFreqInsec`,
                a.`PeDoseInsec`,
                a.`PeInsec1`,
                a.`PeInsec2`,
                a.`PeInsec3`,
                a.`PeInsec4`,
                a.`PeInsec5`,
                a.`PeInsec6`,
                a.`PeInsec7`,
                a.`PeInsec8`,
                a.`PeInsec9`,
                a.`PeInsec10`,
                a.`PeInsec11`,
                a.`PeInsec12`,
                a.`PeInsec13`,
                a.`PeInsec14`,
                a.`PeInsec15`,
                a.`PeInsec16`,
                a.`PeInsec17`,
                a.`PeInsec18`,
                a.`PeInsec19`,
                a.`PeInsec20`,
                a.`PeInsec21`,
                a.`PeInsec22`,
                a.`PeInsec23`,
                a.`PeInsecOther`,
                a.`PeUsingFungicide`,
                a.`PeMoneySpentFungi`,
                a.`PeFreqFungi`,
                a.`PeDoseFungi`,
                a.`PeFungi1`,
                a.`PeFungi2`,
                a.`PeFungi3`,
                a.`PeFungi4`,
                a.`PeFungi5`,
                a.`PeFungi6`,
                a.`PeFungi7`,
                a.`PeFungi8`,
                a.`PeFungi9`,
                a.`PeFungi10`,
                a.`PeFungi11`,
                a.`PeFungi12`,
                a.`PeFungiOther`,
                a.`PestMainRats`,
                a.`PestMainOly`,
                a.`PestMainSatora`,
                a.`PestMainTira`,
                a.`PestMainRhino`,
                a.`PestMainElep`,
                a.`PestMainOrgUtan`,
                a.`PestMainLandak`,
                a.`PestMainBabi`,
                a.`PestMainOther`,
                a.`PestMainOtherText`,
                a.UseProtectiveGear,
                a.EquipHelm,
                a.EquipBoots,
                a.EquipDodosProtector,
                a.EquipMask,
                a.EquipGloves,
                a.EquipSprayGlasses,
                a.EquipEgrekProtector,
                a.EquipProtectiveClothing,
                a.PestStoreLocation,
                a.PestPackageAfterUse,
                a.`DisMainBlast`,
                a.`DisMainGeno`,
                a.`DisMainSteam`,
                a.`DisMainBud`,
                a.`DisMainSpear`,
                a.`DisMainYellow`,
                a.`DisMainAnt`,
                a.`DisMainCrown`,
                a.`DisMainViscular`,
                a.`DisMainBunch`,
                a.`DisMainOther`,
                a.`DisMainOtherText`,
                a.GarWitnessProveOwnership,
                a.GarNameOfWitness,
                a.GarOwnerRelationship,
                a.YearPlantingCurrent,
                a.WagsCert,
                a.WagsCertStandardRSPO,
                a.WagsCertStandardMSPO,
                a.WagsPlantationStage,
                a.WagsCondEstPlantation,
                a.FarmPhoto,
                a.FarmPhotoDesc,
                a.PresentedBy,
                a.Signature
            FROM
                `ktv_survey_plot_sme` a
                LEFT JOIN ktv_members b ON a.`MemberID` = b.`MemberID`
                LEFT JOIN ktv_village c ON a.`VillageID` = c.`VillageID`
                LEFT JOIN ktv_subdistrict d ON c.`SubDistrictID` = d.`SubDistrictID`
            WHERE
                a.`MemberID` = ?
                AND a.`PlotNr` = ?
                AND a.`SurveyNr` = ?
                AND a.`DateCollection` = ?
            LIMIT 1";
        $p = array(
            (int) $MemberID,
            (int) $PlotNr,
            $SurveyNr,
            $DateCollection
        );
        $query = $this->db->query($sql,$p);
        $data = $query->row_array();

        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-".$key;
            $dataRow[$keyNew] = $value;
        }

        //yg diperlukan untuk proses lebih lanjut
        $dataRow['MemberDisplayID'] = $dataRow['Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-MemberDisplayID'];
        $dataRow['MemberUID'] = $dataRow['Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-MemberUID'];
        $dataRow['ProvinceID'] = $dataRow['Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ProvinceID'];
        $dataRow['DistrictID'] = $dataRow['Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DistrictID'];
        $dataRow['SubDistrictID'] = $dataRow['Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SubDistrictID'];
        $dataRow['VillageID'] = $dataRow['Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-VillageID'];
        $dataRow['PhotoOfVisit'] = $dataRow['Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOfVisit'];
        $dataRow['ProvinceIDTPH'] = $dataRow['Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-ProvinceIDTPH'];
        $dataRow['DistrictIDTPH'] = $dataRow['Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DistrictIDTPH'];
        $dataRow['SubDistrictIDTPH'] = $dataRow['Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SubDistrictIDTPH'];
        $dataRow['FarmPhoto'] = $dataRow['Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FarmPhoto'];
        $dataRow['Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOfFarm'] = $dataRow['Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-FarmPhoto'];
        $dataRow['Signature'] = $dataRow['Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-Signature'];


        if($this->awsfileupload->doesObjectExist($dataRow['PhotoOfVisit']) == true) {
            $dataRow['PhotoOfVisitPath']    = $dataRow['PhotoOfVisit'];
            $dataRow['PhotoOfVisit']        = $this->config->item('CTCDN')."/".$dataRow['PhotoOfVisit'];
        }else{
            if($dataRow['PhotoOfVisitPath'] != ''){
                $dataRow['PhotoOfVisitPath']    = '/images/plot_visit/'.$dataRow['ProvinceID'].'/'.$dataRow['MemberUID'].'/'.$dataRow['PhotoOfVisit'];
                $dataRow['PhotoOfVisit']        = base_url().'images/plot_visit/'.$dataRow['ProvinceID'].'/'.$dataRow['MemberUID'].'/'.$dataRow['PhotoOfVisit'];
            }
        }

        if($this->awsfileupload->doesObjectExist($dataRow['FarmPhoto']) == true) {
            $dataRow['FarmPhotoPath']       = $dataRow['FarmPhoto'];
            $dataRow['FarmPhoto']           = $this->config->item('CTCDN')."/".$dataRow['FarmPhoto'];
        }else{
            if($dataRow['FarmPhotoPath'] != ''){
                $dataRow['FarmPhotoPath']   = '/images/plot_farm_sme/'.$dataRow['ProvinceID'].'/'.$dataRow['MemberUID'].'/'.$dataRow['FarmPhoto'];
                $dataRow['FarmPhoto']       = base_url().'images/plot_farm_sme/'.$dataRow['ProvinceID'].'/'.$dataRow['MemberUID'].'/'.$dataRow['FarmPhoto'];
            }
        }

        $return['success'] = true;
        $return['data'] = $dataRow;
        return $return;
    }

    public function insertPlotSurvey($paramPost,$MemberData){
        $this->db->trans_start();

        //buang var yg tidak perlu (begin)
        $MemberDisplayID = $paramPost['MemberDisplayID'];
        $MemberName = $paramPost['MemberName'];
        $PhotoOfVisit = $paramPost['PhotoOfVisitOld'];
        $FarmPhoto = $paramPost['PhotoOfFarmOld'];
        $SignaturePhoto = $paramPost['SignatureOld'];

        unset($paramPost['MemberDisplayID']);
        unset($paramPost['MemberName']);
        unset($paramPost['CreatedByLabel']);
        unset($paramPost['ModifiedByLabel']);
        unset($paramPost['ProvinceID']);
        unset($paramPost['DistrictID']);
        unset($paramPost['SubDistrictID']);
        unset($paramPost['PhotoOfVisitOld']);
        unset($paramPost['PhotoOfFarmOld']);
        unset($paramPost['SignatureOld']);
        unset($paramPost['opsiDisplay']);
        unset($paramPost['TypePlantMateTotalTreeNr']);
        unset($paramPost['TreeTotalTBMTMTR']);
        unset($paramPost['FertUreaDosePlotYear']);
        unset($paramPost['FertSSDosePlotYear']);
        unset($paramPost['FertNPKDosePlotYear']);
        unset($paramPost['FertTSPDosePlotYear']);
        unset($paramPost['FertCUDosePlotYear']);
        unset($paramPost['FertKCLDosePlotYear']);
        unset($paramPost['FertNPKMutiDosePlotYear']);
        unset($paramPost['FertBoratDosePlotYear']);
        unset($paramPost['FertDolomiteDosePlotYear']);
        unset($paramPost['FertPBADosePlotYear']);
        unset($paramPost['FertPBDosePlotYear']);
        unset($paramPost['FertCPBDosePlotYear']);
        unset($paramPost['FertManureDosePlotYear']);
        unset($paramPost['PeTotalUsageHerbi']);
        unset($paramPost['PeTotalUsageInsec']);
        unset($paramPost['PeTotalUsageFungi']);
        unset($paramPost['GardenAreaPolygon']);
        unset($paramPost['TreeTotalTBMTMTRPerHa']);
        //buang var yg tidak perlu (end)

        //tambahkan var yg diperlukan
        $paramPost['DateCreated'] = date('Y-m-d H:i:s');
        $paramPost['CreatedBy'] = $_SESSION['userid'];
        $paramPost['MemberUid'] = $MemberDisplayID;

        //insert
        $this->db->insert('ktv_survey_plot_sme', $paramPost);

        //Plot Status ==================================================== (Begin)
        $sql = "INSERT INTO `ktv_survey_plot_status` (
            `MemberID`,
            `PlotNr`,
            `ActiveStatus`,
            Remark,
            `DateCreated`,
            `CreatedBy`
        )
        SELECT
            t_gar.MemberID
            , t_gar.PlotNr
            , '1'
            , 'Insert dari script penyesuaian garden status'
            , NOW()
            , '1'
        FROM (
            SELECT
                gar.`MemberID`
                , gar.`PlotNr`
            FROM
                ktv_survey_plot_sme gar
            WHERE
                gar.`MemberID` != '0'
                AND gar.`PlotNr` != '0'
            GROUP BY gar.`MemberID`, gar.`PlotNr`
        ) AS t_gar
        LEFT JOIN (
            SELECT
                gstat.`MemberID`
                , gstat.`PlotNr`
                , gstat.`ActiveStatus`
            FROM
                `ktv_survey_plot_status` gstat
            WHERE
                gstat.`MemberID` != '0'
                AND gstat.`PlotNr` != '0'
        ) AS t_garstat ON 1=1
            AND t_gar.MemberID = t_garstat.MemberID
            AND t_gar.PlotNr = t_garstat.PlotNr
        WHERE
            t_garstat.MemberID IS NULL
            AND t_gar.MemberID = ?
            AND t_gar.PlotNr = ?
        ";
        $query = $this->db->query($sql, array($paramPost['MemberID'],$paramPost['PlotNr']));

        $sql = "UPDATE ktv_survey_plot_status tup
                INNER JOIN (
                    SELECT
                        sgar.`MemberID`
                        , sgar.`PlotNr`
                        , sgar.`SurveyNr`
                        , sgar.`GardenAreaHa`
                        , sgar.`AnnualProduction`
                    FROM
                        `ktv_survey_plot_sme` sgar
                        INNER JOIN (
                            SELECT
                                lat_sgar.`MemberID`
                                , lat_sgar.`PlotNr`
                                , MAX(lat_sgar.`SurveyNr`) AS SurveyNr
                            FROM
                                ktv_survey_plot_sme lat_sgar
                            GROUP BY lat_sgar.`MemberID`, lat_sgar.`PlotNr`
                        ) AS sgar_lat ON
                            sgar.MemberID = sgar_lat.MemberID
                            AND sgar.`PlotNr` = sgar_lat.PlotNr
                            AND sgar.`SurveyNr` = sgar_lat.SurveyNr
                    WHERE 1=1
                        AND sgar.MemberID = ?
                        AND sgar.PlotNr = ?
                    GROUP BY sgar_lat.MemberID , sgar_lat.PlotNr
                ) AS gar_lat ON 1=1
                    AND tup.`MemberID` = gar_lat.MemberID
                    AND tup.`PlotNr` = gar_lat.PlotNr
                SET
                    tup.`GardenAreaHa` = gar_lat.GardenAreaHa
                    , tup.`AnnualProduction` = gar_lat.AnnualProduction";
        $query = $this->db->query($sql, array($paramPost['MemberID'],$paramPost['PlotNr']));
        
        $sql = "UPDATE ktv_survey_plot_status tup
                INNER JOIN (
                    SELECT
                        sgar.`MemberID`
                        , sgar.`PlotNr`
                        , sgar.`SurveyNr`
                        , sgar.`Latitude`
                        , sgar.`Longitude`
                    FROM
                        `ktv_survey_plot_sme` sgar
                        INNER JOIN (
                            SELECT
                                lat_sgar.`MemberID`
                                , lat_sgar.`PlotNr`
                                , MAX(lat_sgar.`SurveyNr`) AS SurveyNr
                            FROM
                                ktv_survey_plot_sme lat_sgar
                            GROUP BY lat_sgar.`MemberID`, lat_sgar.`PlotNr`
                        ) AS sgar_lat ON
                            sgar.MemberID = sgar_lat.MemberID
                            AND sgar.`PlotNr` = sgar_lat.PlotNr
                            AND sgar.`SurveyNr` = sgar_lat.SurveyNr
                    WHERE 1=1
                        AND sgar.`Latitude` IS NOT NULL
                        AND sgar.`Latitude` != ''
                        AND sgar.`Latitude` != '0'
                        AND sgar.`Longitude` IS NOT NULL
                        AND sgar.`Longitude` != ''
                        AND sgar.`Longitude` != '0'
                        AND sgar.MemberID = ?
                        AND sgar.PlotNr = ?
                    GROUP BY sgar_lat.MemberID , sgar_lat.PlotNr
                ) AS gar_lat ON 1=1
                    AND tup.`MemberID` = gar_lat.MemberID
                    AND tup.`PlotNr` = gar_lat.PlotNr
                SET
                    tup.`Latitude` = gar_lat.Latitude
                    , tup.`Longitude` = gar_lat.Longitude";
        $query = $this->db->query($sql, array($paramPost['MemberID'],$paramPost['PlotNr']));
        //Plot Status ==================================================== (End)

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Data saved";

            //apakah ada fotonya, kalau ada dipindahkan dan diupdate
            if($PhotoOfVisit != ""){
                //get ext nya..
                $arrTemp = explode(".", $PhotoOfVisit);
                $extNya = array_values(array_slice($arrTemp, -1))[0];
                $namaFileGambar = date('YmdHis').".".$extNya;

                //foto dipisah perdirectory ProvinceID, per MemberDisplayID, cek apakah folder tempat nyimpan foto sudah ada
                if(!file_exists('images/plot_visit_sme/'.$MemberData['ProvinceID'])){
                    mkdir('images/plot_visit_sme/'.$MemberData['ProvinceID'], 0777, true);
                }
                if(!file_exists('images/plot_visit_sme/'.$MemberData['ProvinceID'].'/'.$MemberData['MemberDisplayID'])){
                    mkdir('images/plot_visit_sme/'.$MemberData['ProvinceID'].'/'.$MemberData['MemberDisplayID'], 0777, true);
                }

                $gambarTujuan = 'images/plot_visit_sme/'.$MemberData['ProvinceID'].'/'.$MemberData['MemberDisplayID'].'/'.$namaFileGambar;
                if(rename('images/plot_visit_sme/'.$PhotoOfVisit,$gambarTujuan)){
                    $sql="UPDATE ktv_survey_plot_sme a SET
                            a.`PhotoOfVisit` = ?
                        WHERE
                            a.`MemberID` = ?
                            AND a.`PlotNr` = ?
                            AND a.`SurveyNr` = ?
                            AND a.DateCollection = ?
                        LIMIT 1";
                    $p = array(
                        $namaFileGambar,
                        $paramPost['MemberID'],
                        $paramPost['PlotNr'],
                        $paramPost['SurveyNr'],
                        $paramPost['DateCollection']
                    );
                    $query = $this->db->query($sql,$p);
                }
            }
            if($FarmPhoto != ""){
                //get ext nya..
                $arrTemp = explode(".", $FarmPhoto);
                $extNya = array_values(array_slice($arrTemp, -1))[0];
                $namaFileGambar = date('YmdHis').".".$extNya;

                //foto dipisah perdirectory ProvinceID, per MemberDisplayID, cek apakah folder tempat nyimpan foto sudah ada
                if(!file_exists('images/plot_farm_sme/'.$MemberData['ProvinceID'])){
                    mkdir('images/plot_farm_sme/'.$MemberData['ProvinceID'], 0777, true);
                }
                if(!file_exists('images/plot_farm_sme/'.$MemberData['ProvinceID'].'/'.$MemberData['MemberDisplayID'])){
                    mkdir('images/plot_farm_sme/'.$MemberData['ProvinceID'].'/'.$MemberData['MemberDisplayID'], 0777, true);
                }

                $gambarTujuan = 'images/plot_farm_sme/'.$MemberData['ProvinceID'].'/'.$MemberData['MemberDisplayID'].'/'.$namaFileGambar;
                if(rename('images/plot_farm_sme/'.$FarmPhoto,$gambarTujuan)){
                    $sql="UPDATE ktv_survey_plot_sme a SET
                            a.`FarmPhoto` = ?
                        WHERE
                            a.`MemberID` = ?
                            AND a.`PlotNr` = ?
                            AND a.`SurveyNr` = ?
                            AND a.DateCollection = ?
                        LIMIT 1";
                    $p = array(
                        $namaFileGambar,
                        $paramPost['MemberID'],
                        $paramPost['PlotNr'],
                        $paramPost['SurveyNr'],
                        $paramPost['DateCollection']
                    );
                    $query = $this->db->query($sql,$p);
                }
            }
            if($SignaturePhoto != ""){
                //get ext nya..
                $arrTemp = explode(".", $SignaturePhoto);
                $extNya = array_values(array_slice($arrTemp, -1))[0];
                $namaFileGambar = date('YmdHis').".".$extNya;

                //foto dipisah perdirectory ProvinceID, per MemberDisplayID, cek apakah folder tempat nyimpan foto sudah ada
                if(!file_exists('images/plot_signature_sme/'.$MemberData['ProvinceID'])){
                    mkdir('images/plot_signature_sme/'.$MemberData['ProvinceID'], 0777, true);
                }
                if(!file_exists('images/plot_signature_sme/'.$MemberData['ProvinceID'].'/'.$MemberData['MemberDisplayID'])){
                    mkdir('images/plot_signature_sme/'.$MemberData['ProvinceID'].'/'.$MemberData['MemberDisplayID'], 0777, true);
                }

                $gambarTujuan = 'images/plot_signature_sme/'.$MemberData['ProvinceID'].'/'.$MemberData['MemberDisplayID'].'/'.$namaFileGambar;
                if(rename('images/plot_signature_sme/'.$SignaturePhoto,$gambarTujuan)){
                    $sql="UPDATE ktv_survey_plot_sme a SET
                            a.`Signature` = ?
                        WHERE
                            a.`MemberID` = ?
                            AND a.`PlotNr` = ?
                            AND a.`SurveyNr` = ?
                            AND a.DateCollection = ?
                        LIMIT 1";
                    $p = array(
                        $namaFileGambar,
                        $paramPost['MemberID'],
                        $paramPost['PlotNr'],
                        $paramPost['SurveyNr'],
                        $paramPost['DateCollection']
                    );
                    $query = $this->db->query($sql,$p);
                }
            }

        } else {
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        }
        return $results;
    }

    private function resetAllFieldPlot($MemberID,$PlotNr,$SurveyNr,$DateCollection){
        $sql="UPDATE `ktv_survey_plot_sme` SET
                `VillageID` = null,
                `PhotoOfVisitDesc` = null,
                `GardenAreaHa` = null,
                `GardenLength` = null,
                `GardenWidth` = null,
                `Latitude` = null,
                `Longitude` = null,
                `OwnershipDoc` = null,
                `OwnershipDocText` = null,
                `OwnerDocIsOwner` = null,
                `HaveSTDB` = null,
                `HaveSPPL` = null,
                `BusinessModel` = null,
                `LandOwnership` = null,
                LandOwnershipType = null,
                OwnerOfTheGarden = null,
                `HowObPlantation` = null,
                `HowObPlantationText` = null,
                `PlantationConditionEst` = null,
                `AverageAgeTree` = null,
                `SoilType` = null,
                `TopographyType` = null,
                `FirstPlantingYear` = null,
                TreeTBM = null,
                TreeTM = null,
                TreeTR = null,
                `TypePlantMateMarihat` = null,
                `TypePlantMateMarihatNr` = null,
                `TypePlantMateDumpy` = null,
                `TypePlantMateDumpyNr` = null,
                `TypePlantMateLonsum` = null,
                `TypePlantMateLonsumNr` = null,
                `TypePlantMateSimalungun` = null,
                `TypePlantMateSimalungunNr` = null,
                `TypePlantMateDanimas` = null,
                `TypePlantMateDanimasNr` = null,
                `TypePlantMateSriwijaya` = null,
                `TypePlantMateSriwijayaNr` = null,
                `TypePlantMateSocfin` = null,
                `TypePlantMateSocfinNr` = null,
                `TypePlantMateOther` = null,
                `TypePlantMateOtherText` = null,
                `TypePlantMateOtherNr` = null,
                `TypePlantMateDoNotKnow` = null,
                TypePlantMateDoNotKnowNr = null,
                `OwnerCultivateFarm` = null,
                `FarmEmployHiredLabor` = null,
                `FarmEmployFamMem` = null,
                `FarmEmployLaborFamMem` = null,
                `FarmEmployNoLabor` = null,
                `HowManyWorkFarm` = null,
                `UnderAgeWorker` = null,
                `AveHoursPerDay` = null,
                `AveDaysPerMonth` = null,
                `WageNominalPerDayLabor` = null,
                `WageNominalPerDayLaborPeriod` = null,
                `WageNominalPerDayFamMember` = null,
                `WageNominalPerDayFamMemberPeriod` = null,
                `HarvestRateDaysHighSeason` = null,
                `HarvestRateDaysLowSeason` = null,
                `AverageProdHighSeason` = null,
                `AverageProdLowSeason` = null,
                NrHighSeasonMonths = null,
                NrLowSeasonMonths = null,
                HighSeasonProduction = null,
                LowSeasonProduction = null,
                AnnualProduction = null,
                PlantationProductivity = null,
                `LeanHarvestSeasonJan` = null,
                `LeanHarvestSeasonFeb` = null,
                `LeanHarvestSeasonMar` = null,
                `LeanHarvestSeasonApr` = null,
                `LeanHarvestSeasonMay` = null,
                `LeanHarvestSeasonJun` = null,
                `LeanHarvestSeasonJul` = null,
                `LeanHarvestSeasonAug` = null,
                `LeanHarvestSeasonSep` = null,
                `LeanHarvestSeasonOct` = null,
                `LeanHarvestSeasonNov` = null,
                `LeanHarvestSeasonDec` = null,
                `WhoHarvestFamily` = null,
                `WhoHarvestLabor` = null,
                `HowManyDiffBuyerSoldLastYear` = null,
                `HowManyDiffBuyerSoldLastYearText` = null,
                `ToWhoSellFFBLastYear` = null,
                `HowManyDiffMillSoldLastYear` = null,
                `HowManyDiffMillSoldLastYearText` = null,
                `ToWhichMillSellFFBLastYear` = null,
                `ToWhichMillSellFFBLastYearText` = null,
                `UseEFBFertilizer` = null,
                `FertilizerDesc` = null,
                `FertilizerNotes` = null,
                `useParaquat` = null,
                `PesticideDesc` = null,
                `PesticideNotes` = null,
                `Comment` = null,
                `FertNonOrganicData` = null,
                `FertMoneySpentNonOrganic` = null,
                `FertUreaTimesYear` = null,
                `FertUreaDose` = null,
                `FertSSTimesYear` = null,
                `FertSSDose` = null,
                `FertNPKTimesYear` = null,
                `FertNPKDose` = null,
                `FertTSPTimesYear` = null,
                `FertTSPDose` = null,
                `FertCUTimesYear` = null,
                `FertCUDose` = null,
                `FertKCLTimesYear` = null,
                `FertKCLDose` = null,
                `FertNPKMutiTimesYear` = null,
                `FertNPKMutiDose` = null,
                `FertBoratTimesYear` = null,
                `FertBoratDose` = null,
                `FertDolomiteTimesYear` = null,
                `FertDolomiteDose` = null,
                `FertWithNonOrgaTBM` = null,
                `FertWithNonOrgaTM` = null,
                `FertWithNonOrgaTR` = null,
                `FertUseOrganic` = null,
                `FertMoneySpentOrganic` = null,
                `FertPBATimesYear` = null,
                `FertPBADose` = null,
                `FertPBTimesYear` = null,
                `FertPBDose` = null,
                `FertCPBTimesYear` = null,
                `FertCPBDose` = null,
                `FertManureTimesYear` = null,
                `FertManureDose` = null,
                `FertWithOrgaTBM` = null,
                `FertWithOrgaTM` = null,
                `FertWithOrgaTR` = null,
                `PeUsingHerbicide` = null,
                `PeMoneySpentHerbi` = null,
                `PeFreqHerbi` = null,
                `PeDoseHerbi` = null,
                `PeHerbi1` = null,
                `PeHerbi2` = null,
                `PeHerbi3` = null,
                `PeHerbi4` = null,
                `PeHerbi5` = null,
                `PeHerbi6` = null,
                `PeHerbi7` = null,
                `PeHerbi8` = null,
                `PeHerbi9` = null,
                `PeHerbi10` = null,
                `PeHerbi11` = null,
                `PeHerbi12` = null,
                `PeHerbi13` = null,
                `PeHerbi14` = null,
                `PeHerbi15` = null,
                `PeHerbi16` = null,
                `PeHerbi17` = null,
                `PeHerbi18` = null,
                `PeHerbi19` = null,
                `PeHerbi20` = null,
                `PeHerbi21` = null,
                `PeHerbi22` = null,
                `PeHerbi23` = null,
                `PeHerbi24` = null,
                `PeHerbi25` = null,
                `PeHerbi26` = null,
                `PeHerbi27` = null,
                `PeHerbi28` = null,
                `PeHerbi29` = null,
                `PeHerbiOther` = null,
                `PeUsingInsecticide` = null,
                `PeMoneySpentInsec` = null,
                `PeFreqInsec` = null,
                `PeDoseInsec` = null,
                `PeInsec1` = null,
                `PeInsec2` = null,
                `PeInsec3` = null,
                `PeInsec4` = null,
                `PeInsec5` = null,
                `PeInsec6` = null,
                `PeInsec7` = null,
                `PeInsec8` = null,
                `PeInsec9` = null,
                `PeInsec10` = null,
                `PeInsec11` = null,
                `PeInsec12` = null,
                `PeInsec13` = null,
                `PeInsec14` = null,
                `PeInsec15` = null,
                `PeInsec16` = null,
                `PeInsec17` = null,
                `PeInsec18` = null,
                `PeInsec19` = null,
                `PeInsec20` = null,
                `PeInsec21` = null,
                `PeInsec22` = null,
                `PeInsec23` = null,
                `PeInsecOther` = null,
                `PeUsingFungicide` = null,
                `PeMoneySpentFungi` = null,
                `PeFreqFungi` = null,
                `PeDoseFungi` = null,
                `PeFungi1` = null,
                `PeFungi2` = null,
                `PeFungi3` = null,
                `PeFungi4` = null,
                `PeFungi5` = null,
                `PeFungi6` = null,
                `PeFungi7` = null,
                `PeFungi8` = null,
                `PeFungi9` = null,
                `PeFungi10` = null,
                `PeFungi11` = null,
                `PeFungi12` = null,
                `PeFungiOther` = null,
                `PestMainRats` = null,
                `PestMainOly` = null,
                `PestMainSatora` = null,
                `PestMainTira` = null,
                `PestMainRhino` = null,
                `PestMainElep` = null,
                `PestMainOrgUtan` = null,
                `PestMainLandak` = null,
                `PestMainBabi` = null,
                `PestMainOther` = null,
                `PestMainOtherText` = null,
                `DisMainBlast` = null,
                `DisMainGeno` = null,
                `DisMainSteam` = null,
                `DisMainBud` = null,
                `DisMainSpear` = null,
                `DisMainYellow` = null,
                `DisMainAnt` = null,
                `DisMainCrown` = null,
                `DisMainViscular` = null,
                `DisMainBunch` = null,
                `DisMainOther` = null,
                `DisMainOtherText` = null,
                UseProtectiveGear = null,
                EquipHelm = null,
                EquipBoots = null,
                EquipDodosProtector = null,
                EquipMask = null,
                EquipGloves = null,
                EquipSprayGlasses = null,
                EquipEgrekProtector = null,
                EquipProtectiveClothing = null,
                PestStoreLocation = null,
                PestPackageAfterUse = null,
                OwnerOfPlantationNameText = null,
                OwnerOfPlantationLocationText = null,
                OwnerOfPlantationPhoneText = null,
                GarWitnessProveOwnership = null,
                GarNameOfWitness = null,
                GarOwnerRelationship = null,
                YearPlantingCurrent = null,
                WagsCert = null,
                WagsCertStandardRSPO = null,
                WagsCertStandardMSPO = null,
                WagsPlantationStage = null,
                WagsCondEstPlantation = null
            WHERE
                `MemberID` = ?
                AND `PlotNr` = ?
                AND `SurveyNr` = ?
                AND `DateCollection` = ?
            LIMIT 1";
        $p = array(
            $MemberID,
            $PlotNr,
            $SurveyNr,
            $DateCollection
        );
        $query = $this->db->query($sql,$p);
    }

    public function updatePlotSurvey($paramPost,$MemberData){
        $this->db->trans_start();

        //buang var yg tidak perlu (begin)
        $MemberDisplayID = $paramPost['MemberDisplayID'];
        $MemberName = $paramPost['MemberName'];
        $MemberID = $paramPost['MemberID'];
        $PlotNr = $paramPost['PlotNr'];
        $SurveyNr = $paramPost['SurveyNr'];
        $DateCollection = $paramPost['DateCollection'];

        unset($paramPost['MemberDisplayID']);
        unset($paramPost['MemberName']);
        unset($paramPost['CreatedByLabel']);
        unset($paramPost['ModifiedByLabel']);
        unset($paramPost['ProvinceID']);
        unset($paramPost['DistrictID']);
        unset($paramPost['SubDistrictID']);
        unset($paramPost['opsiDisplay']);
        unset($paramPost['TypePlantMateTotalTreeNr']);
        unset($paramPost['TreeTotalTBMTMTR']);
        unset($paramPost['MemberID']);
        unset($paramPost['PlotNr']);
        unset($paramPost['SurveyNr']);
        unset($paramPost['DateCollection']);
        unset($paramPost['FertUreaDosePlotYear']);
        unset($paramPost['FertSSDosePlotYear']);
        unset($paramPost['FertNPKDosePlotYear']);
        unset($paramPost['FertTSPDosePlotYear']);
        unset($paramPost['FertCUDosePlotYear']);
        unset($paramPost['FertKCLDosePlotYear']);
        unset($paramPost['FertNPKMutiDosePlotYear']);
        unset($paramPost['FertBoratDosePlotYear']);
        unset($paramPost['FertDolomiteDosePlotYear']);
        unset($paramPost['FertPBADosePlotYear']);
        unset($paramPost['FertPBDosePlotYear']);
        unset($paramPost['FertCPBDosePlotYear']);
        unset($paramPost['FertManureDosePlotYear']);
        unset($paramPost['PeTotalUsageHerbi']);
        unset($paramPost['PeTotalUsageInsec']);
        unset($paramPost['PeTotalUsageFungi']);
        unset($paramPost['GardenAreaPolygon']);
        unset($paramPost['TreeTotalTBMTMTRPerHa']);
        //buang var yg tidak perlu (end)

        //tambahkan var yg diperlukan
        $paramPost['DateUpdated'] = date('Y-m-d H:i:s');
        $paramPost['LastModifiedBy'] = $_SESSION['userid'];

        //photo
        if($paramPost['PhotoOfVisitOld'] != ""){
            $arrTemp = explode("/", $paramPost['PhotoOfVisitOld']);
            $PhotoOfVisit = array_values(array_slice($arrTemp, -1))[0];
            $paramPost['PhotoOfVisit'] = $PhotoOfVisit;
        }else{
            unset($paramPost['PhotoOfVisit']);
        }
        unset($paramPost['PhotoOfVisitOld']);
        
        if($paramPost['PhotoOfFarmOld'] != ""){
            $arrTemp = explode("/", $paramPost['PhotoOfFarmOld']);
            $FarmPhoto = array_values(array_slice($arrTemp, -1))[0];
            $paramPost['FarmPhoto'] = $FarmPhoto;
        }else{
            unset($paramPost['PhotoOfFarmOld']);
        }
        unset($paramPost['PhotoOfFarmOld']);
        
        if($paramPost['SignatureOld'] != ""){
            $arrTemp = explode("/", $paramPost['SignatureOld']);
            $SignaturePhoto = array_values(array_slice($arrTemp, -1))[0];
            $paramPost['Signature'] = $SignaturePhoto;
        }else{
            unset($paramPost['SignatureOld']);
        }
        unset($paramPost['SignatureOld']);

        //reset semuanya dulu
        $this->resetAllFieldPlot($MemberID,$PlotNr,$SurveyNr,$DateCollection);

        $this->db->where('MemberID', $MemberID);
        $this->db->where('PlotNr', $PlotNr);
        $this->db->where('SurveyNr', $SurveyNr);
        $this->db->where('DateCollection', $DateCollection);
        $query = $this->db->update('ktv_survey_plot_sme', $paramPost);

        //Plot Status ==================================================== (End)
        $sql = "UPDATE ktv_survey_plot_status tup
                INNER JOIN (
                    SELECT
                        sgar.`MemberID`
                        , sgar.`PlotNr`
                        , sgar.`SurveyNr`
                        , sgar.`GardenAreaHa`
                        , sgar.`AnnualProduction`
                    FROM
                        `ktv_survey_plot_sme` sgar
                        INNER JOIN (
                            SELECT
                                lat_sgar.`MemberID`
                                , lat_sgar.`PlotNr`
                                , MAX(lat_sgar.`SurveyNr`) AS SurveyNr
                            FROM
                                ktv_survey_plot_sme lat_sgar
                            GROUP BY lat_sgar.`MemberID`, lat_sgar.`PlotNr`
                        ) AS sgar_lat ON
                            sgar.MemberID = sgar_lat.MemberID
                            AND sgar.`PlotNr` = sgar_lat.PlotNr
                            AND sgar.`SurveyNr` = sgar_lat.SurveyNr
                    WHERE 1=1
                        AND sgar.MemberID = ?
                        AND sgar.PlotNr = ?
                    GROUP BY sgar_lat.MemberID , sgar_lat.PlotNr
                ) AS gar_lat ON 1=1
                    AND tup.`MemberID` = gar_lat.MemberID
                    AND tup.`PlotNr` = gar_lat.PlotNr
                SET
                    tup.`GardenAreaHa` = gar_lat.GardenAreaHa
                    , tup.`AnnualProduction` = gar_lat.AnnualProduction";
        $query = $this->db->query($sql, array($MemberID,$PlotNr));
        
        $sql = "UPDATE ktv_survey_plot_status tup
                INNER JOIN (
                    SELECT
                        sgar.`MemberID`
                        , sgar.`PlotNr`
                        , sgar.`SurveyNr`
                        , sgar.`Latitude`
                        , sgar.`Longitude`
                    FROM
                        `ktv_survey_plot_sme` sgar
                        INNER JOIN (
                            SELECT
                                lat_sgar.`MemberID`
                                , lat_sgar.`PlotNr`
                                , MAX(lat_sgar.`SurveyNr`) AS SurveyNr
                            FROM
                                ktv_survey_plot_sme lat_sgar
                            GROUP BY lat_sgar.`MemberID`, lat_sgar.`PlotNr`
                        ) AS sgar_lat ON
                            sgar.MemberID = sgar_lat.MemberID
                            AND sgar.`PlotNr` = sgar_lat.PlotNr
                            AND sgar.`SurveyNr` = sgar_lat.SurveyNr
                    WHERE 1=1
                        AND sgar.`Latitude` IS NOT NULL
                        AND sgar.`Latitude` != ''
                        AND sgar.`Latitude` != '0'
                        AND sgar.`Longitude` IS NOT NULL
                        AND sgar.`Longitude` != ''
                        AND sgar.`Longitude` != '0'
                        AND sgar.MemberID = ?
                        AND sgar.PlotNr = ?
                    GROUP BY sgar_lat.MemberID , sgar_lat.PlotNr
                ) AS gar_lat ON 1=1
                    AND tup.`MemberID` = gar_lat.MemberID
                    AND tup.`PlotNr` = gar_lat.PlotNr
                SET
                    tup.`Latitude` = gar_lat.Latitude
                    , tup.`Longitude` = gar_lat.Longitude";
        $query = $this->db->query($sql, array($MemberID,$PlotNr));
        //Plot Status ==================================================== (End)

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
	
	
	function SetWorkArea($ProvinceID, $DistrictID)
	{
		$this->db->where('ProvinceID', $ProvinceID);
		$this->db->where('DistrictID', $DistrictID);
		return $this->db->from('ktv_ref_work_area')->get()->num_rows();
	}
	function FindDistrictName($DistrictID)
	{
		$this->db->where('DistrictID', $DistrictID); 
		$r = $this->db->from('ktv_district')->get()->row();
		if($r){
			return $r->District;
		}
	}
    
    public function setPartnerSME($paramPost){
        $sql="UPDATE `ktv_members` SET
                PartnerID = ?,
                DateUpdated = NOW(),
                LastModifiedBy = ?
            WHERE
                `MemberID` = ?";
        $p = array(
            $paramPost["PartnerID"],
            $_SESSION['userid'],
            $paramPost["MemberID"],
        );
        $query = $this->db->query($sql,$p);

        if ($query) {
            $results['success'] = true;
            $results['message'] = "Data updated";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update data";
        }
        return $results;
    }

    public function insertMember($varPost){
        $this->load->library('awsfileupload');
        $this->db->trans_begin();
		
		 
        //rapikan variable post (begin)
        foreach ($varPost as $k => $v) {
            if($varPost[$k] == ""){
                $varPost[$k] = null;
            }
        }
        
        //rapikan variable post (end)

        //generate MemberID dan MemberDisplayID
        $this->load->model('grower/mgrower');
        $id = $this->mgrower->genMemberID($varPost['Koltiva_view_SME_FormMainTrader-Village'],'A'); 
        $uid = $this->mgrower->getUID();
		
        $p = array(
            $id['MemberID'],
            $uid,
            $uid,
            $id['MemberDisplayID'],
            $varPost['Koltiva_view_SME_FormMainTrader-Fullname'],
			$varPost['Koltiva_view_SME_FormMainTrader-agAliasName'],
            $varPost['Koltiva_view_SME_FormMainTrader-DateCollection'],
            $varPost['Koltiva_view_SME_FormMainTrader-DateOfBirth'],
            $varPost['Koltiva_view_SME_FormMainTrader-Gender'],
            $varPost['Koltiva_view_SME_FormMainTrader-Village'],
            $varPost['Koltiva_view_SME_FormMainTrader-Address'],
			$varPost['Koltiva_view_SME_FormMainTrader-HandphoneType'],			
            $varPost['Koltiva_view_SME_FormMainTrader-Handphone'],
			$varPost['Koltiva_view_SME_FormMainTrader-Latitude'], 
			$varPost['Koltiva_view_SME_FormMainTrader-Longitude'], 
            $varPost['Koltiva_view_SME_FormMainTrader-Nin'],
            $varPost['Koltiva_view_SME_FormMainTrader-Email'], 			
			$varPost['Koltiva_view_SME_FormMainTrader-Linked'],
			$varPost['Koltiva_view_SME_FormMainTrader-Website'],
			$varPost['Koltiva_view_SME_FormMainTrader-Phone'],
			$varPost['Koltiva_view_SME_FormMainTrader-Fax'],			
            $varPost['Koltiva_view_SME_FormMainTrader-Education'],
            $_SESSION['userid']
        );
		//print_r($p);die;
        $sql="INSERT INTO `ktv_members` SET
                `MemberID` = ?,
                 MemberUID = ?,
                 `uid` = ?,
                `MemberDisplayID` = ?,
                `MemberName` = ?,
				 Alias = ?,
                `DateCollection` = ?,
                `DateOfBirth` = ?,
                 Gender = ?,
                `VillageID` = ?,
                `Address` = ?, 
				 HandphoneType =?,
                `Handphone` = ?,
				 Latitude= ?,
				 Longitude =?, 
                 Nin = ?,
                 Email = ?, 
				 Linked = ?,
				 Website = ?,
				 Phone =?,
				 Fax =?, 
                 Education = ?,				 
                `DateCreated` = NOW(),
                `CreatedBy` = ?,
                `StatusMember` = 'Active' ";
        $query = $this->db->query($sql,$p);
		
		//insert member role ======================================================================== (begin)
        // $arrRole = array(); 
		// $sqlr="INSERT INTO `ktv_member_role` SET
		// 	`MemberID` = ?,
		// 	`MRoleID` = ?,
		// 	`DateCreated` = NOW(),
		// 	`CreatedBy` = ?";
		// $pr = array(
		// 	$id['MemberID'],
		// 	$varPost['Koltiva_view_SME_FormMainTrader-smerole'], 
		// 	$_SESSION['userid']
		// );
        // $queryr = $this->db->query($sqlr,$pr);

        //Koordinat Geometry ============= (Begin)
        if($varPost['Koltiva_view_SME_FormMainTrader-Latitude'] != "" && $varPost['Koltiva_view_SME_FormMainTrader-Longitude'] != "") {

			$LatitudeProses = (float) $varPost['Koltiva_view_SME_FormMainTrader-Latitude'];
            $LongitudeProses = (float) $varPost['Koltiva_view_SME_FormMainTrader-Longitude'];
            
            //Check Latitude
            if( ($LatitudeProses >= -90 && $LatitudeProses <= 90) && ($LongitudeProses >= -180 && $LongitudeProses <= 180) ) {

				//Cek valid tidak koordinatnya
                $sql = "SELECT ST_IsValid(ST_GeomFromText('POINT({$LatitudeProses} {$LongitudeProses})', 4326)) AS HasilCek";
                $DataCekKoordinat = $this->db->query($sql)->row_array();
                
                if ($DataCekKoordinat['HasilCek'] == "1") {
					$PointInsert = "POINT({$LatitudeProses} {$LongitudeProses})";

					$sql = "UPDATE ktv_members a SET
								a.`LatLong` = ST_GEOMFROMTEXT('$PointInsert', 4326)
							WHERE
								a.`MemberID` = ?
							LIMIT 1";
					$p = array(
                        $id['MemberID']
					);
					$query = $this->db->query($sql,$p);
				}

            }
        }
        //Koordinat Geometry ============= (End)
        
        $arrSMERole = array();
        $arrSMERole = $varPost['Koltiva_view_SME_FormMainTrader-CmbSmeRole'];
        if(!empty($arrSMERole)){
            foreach($arrSMERole as $k => $SMERoleID){
                $sqlr="INSERT INTO `ktv_member_role` SET
                 	`MemberID` = ?,
                 	`MRoleID` = ?,
                 	`DateCreated` = NOW(),
                 	`CreatedBy` = ?";
                $prole = array(
                    $id['MemberID'],
                    $SMERoleID,
                    $_SESSION['userid']
                );
                $this->db->query($sqlr,$prole);
            }
           
        }
        //insert member role ======================================================================== (end) 

        //insert member work area ======================================================================== (start) 
        $arrSMEVillage = array();
        $arrSMEVillage = $varPost['Koltiva_view_SME_FormMainTrader-cmbSMEVillage'];
        if(!empty($arrSMEVillage)){
            foreach($arrSMEVillage as $k => $SMEVillageID){
                $sqlr="INSERT INTO `ktv_member_work_area` SET
                    `MemberID` = ?,
                    `VillageID` = ?,
                    `DateCreated` = NOW(),
                    `CreatedBy` = ?";
                $prole = array(
                    $id['MemberID'],
                    $SMEVillageID,
                    $_SESSION['userid']
                );
                $this->db->query($sqlr,$prole);
            }
        
        }
        //insert member work area ======================================================================== (end) 

        //insert member sme type ======================================================================== (begin)        
        $arrSMEType = array();
        $arrSMEType = $varPost['Koltiva_view_SME_FormMainTrader-CmbSmeType'];
        $impSMEType = implode(",",$arrSMEType);
        if(!empty($arrSMEType)){
            foreach($arrSMEType as $k => $SMETypeID){
                $sqlr="INSERT INTO `ktv_member_sme_type` SET
                 	`MemberID` = ?,
                 	`SMETypeID` = ?,
                 	`DateCreated` = NOW(),
                 	`CreatedBy` = ?";
                $ptype = array(
                    $id['MemberID'],
                    $SMETypeID,
                    $_SESSION['userid']
                );
                $this->db->query($sqlr,$ptype);
            }
           
        }
        //insert member sme type ======================================================================== (end)
		
		//echo $this->db->last_query();die;  
		//insert workarea agar tabel ktv_ref_work_area up to date
		$workAreaCount = $this->SetWorkArea($varPost['Koltiva_view_SME_FormMainTrader-Province'], $varPost['Koltiva_view_SME_FormMainTrader-District']); 
		if($workAreaCount == 0 )	
		 {
			 $sqlw="INSERT INTO ktv_ref_work_area SET
                 ProvinceID = ?,
                 DistrictID = ?,
                 WorkAreaName = ?,
                 StatusCode = 'active',
                 DateCreated = NOW(),
                 CreatedBy = ?
            ";
			$pw = array( 
				$varPost['Koltiva_view_SME_FormMainTrader-Province'],
				$varPost['Koltiva_view_SME_FormMainTrader-District'],
				$this->FindDistrictName($varPost['Koltiva_view_SME_FormMainTrader-District']),
				$_SESSION['userid']
			  );
			 $queryWr = $this->db->query($sqlw,$pw); 
		 } 
		 
        //Member extension
        $sqlex="INSERT INTO `ktv_members_extension` SET
                `MemberID` = ?,
                uid = ?,
                 agLegalStatusCompany = ?,
                agCompanyName = ?,
                agYearEstablished = ?,
                `DateCreated` = NOW(),
                `CreatedBy` = ?
            ";
        $pex = array(
            $id['MemberID'],
            $uid,
            $varPost['Koltiva_view_SME_FormMainTrader-agLegalStatusCompany'],
            $varPost['Koltiva_view_SME_FormMainTrader-agCompanyName'],
            $varPost['Koltiva_view_SME_FormMainTrader-agYearEstablished'],
            $_SESSION['userid']
        );
        $queryex = $this->db->query($sqlex,$pex);

        

        //insert hak akses data control (Begin)
        if($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            $sql="INSERT INTO `ktv_access_partner_member` SET
                    `apmPartnerID` = ?,
                    `apmMemberID` = ?,
                    `DateCreated` = NOW(),
                    `CreatedBy` = ?";
            $p = array(
                $_SESSION['PartnerID'],
                $id['MemberID'],
                $_SESSION['userid']
            );
            $query = $this->db->query($sql,$p);

            //cek kalau bukan Partner Koltiva, maka ditambahkan juga ke Partner Koltiva
            if($_SESSION['PartnerID'] != "1"){
                //insertkan ke Koltiva
                $sql="INSERT INTO `ktv_access_partner_member` SET
                        `apmPartnerID` = ?,
                        `apmMemberID` = ?,
                        `DateCreated` = NOW(),
                        `CreatedBy` = ?";
                $p = array(
                    '1',
                    $id['MemberID'],
                    $_SESSION['userid']
                );
                $query = $this->db->query($sql,$p);
            }
			
        }else{
            //insertkan ke Koltiva
            $sql="INSERT INTO `ktv_access_partner_member` SET
                    `apmPartnerID` = ?,
                    `apmMemberID` = ?,
                    `DateCreated` = NOW(),
                    `CreatedBy` = ?";
            $p = array(
                '1',
                $id['MemberID'],
                $_SESSION['userid']
            );
            $query = $this->db->query($sql,$p);
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
            $results['MemberTypeID'] = $impSMEType;

            //apakah ada fotonya, kalau ada dipindahkan dan diupdate
            if($varPost['Koltiva_view_SME_FormMainTrader-MemberPhotoOld'] != ""){
                $file = explode("images/trader/temp/",$varPost['Koltiva_view_SME_FormMainTrader-MemberPhotoOld']);
                // //Insert ada photonya pakai aws
                if(file_exists('images/trader/temp/'.$file[1])) {
                    $this->load->library('awsfileupload');
                    $upload = $this->awsfileupload->upload('images/trader/temp/'.$file[1],$file[1],AWSS3_SME_PATH, 'images');
                    if ($upload['success'] == true) {
                        delete_file("/".$varPost['Koltiva_view_SME_FormMainTrader-MemberPhotoOld']);
                        $Photo = $upload['filenamepath'];
                    }
                }
    
                $sql="UPDATE ktv_members a SET
                        a.`Photo` = ?
                    WHERE
                        a.`MemberID` = ?
                    LIMIT 1";
                $p = array(
                    $Photo,
                    $id['MemberID']
                );
                $query = $this->db->query($sql,$p);
            }

            //apakah ada fotonya, kalau ada dipindahkan dan diupdate
            if($varPost['Koltiva_view_SME_FormMainTrader-agCompanyLogoOld'] != ""){
                $file = explode("images/trader/temp/",$varPost['Koltiva_view_SME_FormMainTrader-agCompanyLogoOld']);
                // //Insert ada photonya pakai aws
                if(file_exists('images/trader/temp/'.$file[1])) {
                    $this->load->library('awsfileupload');
                    $upload = $this->awsfileupload->upload('images/trader/temp/'.$file[1],$file[1],AWSS3_SME_LOGO_PATH, 'images');
                    if ($upload['success'] == true) {
                        delete_file("/".$varPost['Koltiva_view_SME_FormMainTrader-agCompanyLogoOld']);
                        $Photo = $upload['filenamepath'];
                    }
                }
    
                $sql="UPDATE ktv_members_extension a SET
                        a.`agCompanyLogo` = ?
                    WHERE
                        a.`MemberID` = ?
                    LIMIT 1";
                $p = array(
                    $Photo,
                    $id['MemberID']
                );
                $query = $this->db->query($sql,$p);
            }
            
        }

        return $results;
    }

    public function updateMember($varPost){
        $this->db->trans_begin();

        //rapikan variable post (begin)
        foreach ($varPost as $k => $v) {
            if($varPost[$k] == ""){
                $varPost[$k] = null;
            }
        }
        //photo
        if($varPost['Koltiva_view_SME_FormMainTrader-MemberPhotoOld'] != ""){
            $tmpGambar = $varPost['Koltiva_view_SME_FormMainTrader-MemberPhotoOld'];
            $tmpGambar1 = substr($tmpGambar,3);
            $tmpGambar2 = explode("?",$tmpGambar1);
            $sqlPhoto = " `Photo` = '{$tmpGambar2[0]}', ";
        }else{
            $Photo = null;
            $sqlPhoto = "";
        }
 
        $sql="UPDATE `ktv_members` SET
                `MemberName` = ?,
				 Alias = ?,
                `DateCollection` = ?,
                `DateOfBirth` = ?,
                `Gender` = ?,
                `VillageID` = ?,
                `Address` = ?, 
				 Latitude = ?,
				 Longitude = ?,
				 HandphoneType=?,
                `Handphone` = ?,
                 Nin = ?,
                 Email = ?, 
				 Linked=?,
				 Website=?,
				 Phone=?,
				 Fax =?,
                 Education = ?,
                `DateUpdated` = NOW(),
                `LastModifiedBy` = ?
            WHERE
                `MemberID` = ?
            LIMIT 1";
        $p = array(
            $varPost['Koltiva_view_SME_FormMainTrader-Fullname'],
			$varPost['Koltiva_view_SME_FormMainTrader-agAliasName'],
            $varPost['Koltiva_view_SME_FormMainTrader-DateCollection'],
            $varPost['Koltiva_view_SME_FormMainTrader-DateOfBirth'],
            $varPost['Koltiva_view_SME_FormMainTrader-Gender'],
            $varPost['Koltiva_view_SME_FormMainTrader-Village'],
            $varPost['Koltiva_view_SME_FormMainTrader-Address'], 			
			$varPost['Koltiva_view_SME_FormMainTrader-Latitude'], 
			$varPost['Koltiva_view_SME_FormMainTrader-Longitude'], 
			$varPost['Koltiva_view_SME_FormMainTrader-HandphoneType'],	
            $varPost['Koltiva_view_SME_FormMainTrader-Handphone'],
            $varPost['Koltiva_view_SME_FormMainTrader-Nin'],
            $varPost['Koltiva_view_SME_FormMainTrader-Email'], 
			$varPost['Koltiva_view_SME_FormMainTrader-Linked'],
			$varPost['Koltiva_view_SME_FormMainTrader-Website'],
			$varPost['Koltiva_view_SME_FormMainTrader-Phone'],
			$varPost['Koltiva_view_SME_FormMainTrader-Fax'],	
            $varPost['Koltiva_view_SME_FormMainTrader-Education'],
            $_SESSION['userid'],
            $varPost['Koltiva_view_SME_FormMainTrader-MemberID']
        );
        $query = $this->db->query($sql,$p);

        

        //Koordinat Geometry ============= (Begin)
        if($varPost['Koltiva_view_SME_FormMainTrader-Latitude'] != "" && $varPost['Koltiva_view_SME_FormMainTrader-Longitude'] != "") {

			$LatitudeProses = (float) $varPost['Koltiva_view_SME_FormMainTrader-Latitude'];
            $LongitudeProses = (float) $varPost['Koltiva_view_SME_FormMainTrader-Longitude'];
            
            //Check Latitude
            if( ($LatitudeProses >= -90 && $LatitudeProses <= 90) && ($LongitudeProses >= -180 && $LongitudeProses <= 180) ) {

				//Cek valid tidak koordinatnya
                $sql = "SELECT ST_IsValid(ST_GeomFromText('POINT({$LatitudeProses} {$LongitudeProses})', 4326)) AS HasilCek";
                $DataCekKoordinat = $this->db->query($sql)->row_array();
                
                if ($DataCekKoordinat['HasilCek'] == "1") {
					$PointInsert = "POINT({$LatitudeProses} {$LongitudeProses})";

					$sql = "UPDATE ktv_members a SET
								a.`LatLong` = ST_GEOMFROMTEXT('$PointInsert', 4326)
							WHERE
								a.`MemberID` = ?
							LIMIT 1";
					$p = array(
                        $varPost['Koltiva_view_SME_FormMainTrader-MemberID']
					);
					$query = $this->db->query($sql,$p);
				}

            }
        }
        //Koordinat Geometry ============= (End)

        //Member Extension
        $sql="INSERT INTO ktv_members_extension (
                MemberID,
                agLegalStatusCompany,
                agCompanyName, 
                agYearEstablished, 
                `DateCreated`,
                `CreatedBy`
                )
            VALUES(
                ?,
                ?, 
				?,
                ?, 			
                NOW(),
                ?
                )
            ON DUPLICATE KEY UPDATE
                `agLegalStatusCompany` = ?,
                `agCompanyName` = ?, 
                `agYearEstablished` = ?, 
                `DateUpdated` = NOW(),
                `LastModifiedBy` = ?";
        $p = array(
            $varPost['Koltiva_view_SME_FormMainTrader-MemberID'],
            $varPost['Koltiva_view_SME_FormMainTrader-agLegalStatusCompany'],
            $varPost['Koltiva_view_SME_FormMainTrader-agCompanyName'], 
            $varPost['Koltiva_view_SME_FormMainTrader-agYearEstablished'],
            $_SESSION['userid'],
            $varPost['Koltiva_view_SME_FormMainTrader-agLegalStatusCompany'],
            $varPost['Koltiva_view_SME_FormMainTrader-agCompanyName'], 
            $varPost['Koltiva_view_SME_FormMainTrader-agYearEstablished'],
            $_SESSION['userid']
        );
        $query = $this->db->query($sql,$p);
		 
        //delete dl rolenya
        $sql="DELETE FROM ktv_member_role WHERE MemberID = ?";
        $p = array(
            (int) $varPost['Koltiva_view_SME_FormMainTrader-MemberID']
        );
        $query = $this->db->query($sql,$p);
        
        $sql2="DELETE FROM ktv_member_work_area WHERE MemberID = ?";
        $p2 = array(
            (int) $varPost['Koltiva_view_SME_FormMainTrader-MemberID']
        );
        $query2 = $this->db->query($sql2,$p2);

        //insert member role ======================================================================== (begin) 
            // $sql="INSERT INTO `ktv_member_role` SET
            //     `MemberID` = ?,
            //     `MRoleID` = ?,
            //     `DateCreated` = NOW(),
            //     `CreatedBy` = ?";
            // $p = array(
            //     $varPost['Koltiva_view_SME_FormMainTrader-MemberID'],
            //     $varPost['Koltiva_view_SME_FormMainTrader-smerole'], 
            //     $_SESSION['userid']
            // );
            // $query = $this->db->query($sql,$p); 
            $arrSMERole = array();
            $arrSMERole = $varPost['Koltiva_view_SME_FormMainTrader-CmbSmeRole'];
            if(!empty($arrSMERole)){
                foreach($arrSMERole as $k => $SMERoleID){
                    $sqlr="INSERT INTO `ktv_member_role` SET
                        `MemberID` = ?,
                        `MRoleID` = ?,
                        `DateCreated` = NOW(),
                        `CreatedBy` = ?";
                    $prole = array(
                        $varPost['Koltiva_view_SME_FormMainTrader-MemberID'],
                        $SMERoleID,
                        $_SESSION['userid']
                    );
                    $this->db->query($sqlr,$prole);
                }
            
            } 

            $arrSMEVillage = array();
            $arrSMEVillage = $varPost['Koltiva_view_SME_FormMainTrader-cmbSMEVillage'];
            if(!empty($arrSMEVillage)){
                foreach($arrSMEVillage as $k => $SMEVillageID){
                    $sqlr="INSERT INTO `ktv_member_work_area` SET
                        `MemberID` = ?,
                        `VillageID` = ?,
                        `DateCreated` = NOW(),
                        `CreatedBy` = ?";
                    $prole = array(
                        $varPost['Koltiva_view_SME_FormMainTrader-MemberID'],
                        $SMEVillageID,
                        $_SESSION['userid']
                    );
                    $this->db->query($sqlr,$prole);
                }
            
            }
			
        //insert member role ======================================================================== (end)

        //delete dulu sme typenya
        $sql="DELETE FROM ktv_member_sme_type WHERE MemberID = ?";
        $p = array(
            (int) $varPost['Koltiva_view_SME_FormMainTrader-MemberID']
        );
        $query = $this->db->query($sql,$p);

        //insert member sme type ======================================================================== (begin)        
        $arrSMEType = array();
        $arrSMEType = $varPost['Koltiva_view_SME_FormMainTrader-CmbSmeType'];
        $impSMEType = implode(",",$arrSMEType);
        if(!empty($arrSMEType)){
            foreach($arrSMEType as $k => $SMETypeID){
                $sqlr="INSERT INTO `ktv_member_sme_type` SET
                 	`MemberID` = ?,
                 	`SMETypeID` = ?,
                 	`DateCreated` = NOW(),
                 	`CreatedBy` = ?";
                $ptype = array(
                    $varPost['Koltiva_view_SME_FormMainTrader-MemberID'],
                    $SMETypeID,
                    $_SESSION['userid']
                );
                $this->db->query($sqlr,$ptype);
            }
           
        }
        //insert member sme type ======================================================================== (end)

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to update data";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Data updated";
            $results['MemberIDInc'] = $varPost['Koltiva_view_SME_FormMainTrader-MemberID'];
            $results['MemberTypeID'] = $impSMEType;
        }
        return $results;
    }

    public function deleteMember($MemberID){
        $sql="UPDATE `ktv_members` SET
                StatusCode = 'nullified'
            WHERE
                `MemberID` = ?
            LIMIT 1";
        $p = array(
            $MemberID
        );
        $query = $this->db->query($sql,$p);

        if ($query) {
            $results['success'] = true;
            $results['message'] = "Data deleted";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete data";
        }
        return $results;
    }

    public function getGridTraderStaff($MemberID){
        $sql="SELECT
                a.`StaffID`
                , b.`PersonID`
                , b.`PersonNm` AS 'Name'
                , FLOOR(DATEDIFF(CURDATE(), b.`BirthDate`) / 365.25) AS Age
                , IFNULL(rpos.PositionName,'-') AS `Position`
                , b.UserID
            FROM
                ktv_staffs a
                INNER JOIN ktv_persons b ON a.`PersonID` = b.`PersonID`
                LEFT JOIN ktv_staff_positions f ON a.`StaffID` = f.`StaffPosStaffID`
                                    AND (CURDATE() BETWEEN f.`StaffPostStart` AND f.`StaffPostEnd`)
                                    AND f.StatusCode = 'active'
                LEFT JOIN ktv_ref_position_type rpos ON f.StaffPosPositionID = rpos.PositionID
            WHERE
                a.`ObjType` = 'agent'
                AND a.`ObjID` = ?
                AND a.`StatusCode` IN ('active','inactive')
                AND b.`StatusCd` IN ('active','inactive')
            ORDER BY b.`PersonNm` ASC";
        $query = $this->db->query($sql, array((int) $MemberID));
        $data = $query->result_array();
        if(@$data[0]['StaffID'] == "") $data = array();

        $return['data'] = $data;
        return $return;
    }
	
	 public function getGrid_trader_warehouses($MemberID){
        $data = array();
		$sql="SELECT 
            WarehousesNr 
            , MemberID
            , PhotoBusinessLocation
            , Warehousetype
            , ST_Y(`LatLong`) AS Latitude
            , ST_X(`LatLong`) AS Longitude
            , StatusCode
            , DateCreated
            , CreatedBy
        from 
            ktv_trader_warehouses where MemberID = ? and  StatusCode ='active' ";
        $query = $this->db->query($sql, array((int) $MemberID));
        $data = $query->result_array(); 
        // echo "<pre>";print_r($data);die;
        $return['data'] = $data;
        return $return;
    }

    public function getGridTraderVehicle($MemberID){
        $sql="SELECT
                a.`VehID`
                , b.`BrandName`
                , CASE
                    WHEN a.VehName = '1' THEN '".lang('Truck')."'
                    WHEN a.VehName = '2' THEN '".lang('Mini Truck')."'
                    WHEN a.VehName = '3' THEN '".lang('Pick Up')."'
                    WHEN a.VehName = '4' THEN '".lang('Truck Colt Diesel')."'
                    WHEN a.VehName = '5' THEN '".lang('Dump Truck')."'
                    WHEN a.VehName = '6' THEN '".lang('Motorcycle')."'
                    WHEN a.VehName = '7' THEN '".lang('Other')."'
                END AS VehName
                , a.`VehPoliceNr`
                , CASE
                    WHEN a.VehCapacity = '1' THEN '".lang('Less than 1,000 kg')."'
                    WHEN a.VehCapacity = '2' THEN '".lang('1,000 - 3,500 kg')."'
                    WHEN a.VehCapacity = '3' THEN '".lang('3,500 - 8,500 kg')."'
                    WHEN a.VehCapacity = '4' THEN '".lang('Above 8,000 kg')."'
                END AS VehCapacity
                , IFNULL((SELECT
                    sub_b.`PersonNm`
                FROM
                    ktv_staffs sub_a
                    INNER JOIN ktv_persons sub_b ON sub_a.`PersonID` = sub_b.`PersonID`
                WHERE
                    sub_a.`StaffID` = a.`StaffID`
                LIMIT 1),'-') AS Driver
            FROM
                ktv_member_vehicle a
                LEFT JOIN ktv_ref_vehicle_brand b ON a.`BrandID` = b.`BrandID`
            WHERE
                a.`StatusCode` = 'active'
                AND a.MemberID = ?
            ORDER BY a.`VehName` ASC";
        $query = $this->db->query($sql, array((int) $MemberID));
        $data = $query->result_array();
        if($data[0]['VehID'] == "") $data = array();

        $return['data'] = $data;
        return $return;
    }

    public function getCmbBrandVehicle(){
        $sql="SELECT
                a.`BrandID` AS id
                , a.`BrandName` AS label
            FROM
                ktv_ref_vehicle_brand a
            WHERE
                a.`StatusCode` = 'active'
            ORDER BY a.`BrandName` ASC";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function getCmbWorkArea(){
        // if($_SESSION['is_admin'] != "1"){
        //     $sqlDistrikAkses = " AND a.DistrictID IN ({$_SESSION['daerah_access']}) ";
        // }else{
            $sqlDistrikAkses = "";
        // }
            
        $sql_area = "SELECT
                GROUP_CONCAT(b.DistrictID) DistrictID
            FROM
                `ktv_staffs` a
            LEFT JOIN
                ktv_persons p on p.PersonID = a.PersonID
            LEFT JOIN
                ktv_access_staff b on b.UserId = p.UserID
            WHERE
                a.ObjID = ?
            GROUP BY
                a.ObjID";
        $query_area = $this->db->query($sql_area,array($_GET["MemberID"]));
        if($query_area->num_rows()>0){
            $sqlDistrikAkses = " AND a.DistrictID IN ({$query_area->row()->DistrictID})";

            $sql = "SELECT
                v.`VillageID` AS id
                , CONCAT(v.Village, ' > ', sd.`SubDistrict`, ' > ', a.District) AS label
            FROM
                ktv_district a
                LEFT JOIN ktv_subdistrict sd on sd.DistrictID = a.DistrictID
                LEFT JOIN ktv_village v on v.SubDistrictID = sd.SubDistrictID
                LEFT JOIN ktv_province p on p.ProvinceID = a.ProvinceID
            WHERE
                a.`active` = '1'
                $sqlDistrikAkses
            GROUP BY v.VillageID ORDER BY v.`Village` ASC";
            $query = $this->db->query($sql);
    
            // echo "<pre>";
            // print_r($this->db->last_query());
            // print_r($sqlDistrikAkses);
            // print_r($_SESSION);
            // die;
            return $query->result_array();
        }
        return false;
    }

    public function getCmbSmeRole($RoleType,$Partner = null){
        if($_SESSION['is_admin'] == "1"){
            $where = "";
        }else{            
            if($Partner == "GAR"){
                $where = "AND MRoleID IN ('7','11','12','13','14','15')";
            }else{
                $where = "AND MRoleID NOT IN ('11','12','13','14','15')";
            }
        }
        $sql="
            SELECT
                MRoleID AS id,
                MRoleName AS label
            FROM
                ktv_ref_member_role
            WHERE
                StatusCode = 'active'
                AND MRoleType = ?
                $where
            ORDER BY label ASC";
        $query = $this->db->query($sql, [$RoleType]);
        return $query->result_array();
    }

    public function getCmbTraderStaff($MemberID){
        $sql="SELECT
                a.`StaffID` AS id
                , CONCAT(b.`PersonNm`,' - ',IFNULL(rpos.PositionName,'-')) AS label
            FROM
                ktv_staffs a
                INNER JOIN ktv_persons b ON a.`PersonID` = b.`PersonID`
                LEFT JOIN ktv_staff_positions f ON a.`StaffID` = f.`StaffPosStaffID`
                                    AND (CURDATE() BETWEEN f.`StaffPostStart` AND f.`StaffPostEnd`)
                                    AND f.StatusCode = 'active'
                LEFT JOIN ktv_ref_position_type rpos ON f.StaffPosPositionID = rpos.PositionID
            WHERE
                a.`ObjType` = 'agent'
                AND a.`ObjID` = ?
                AND a.`StatusCode` = 'active'
                AND b.`StatusCd` = 'active'
            ORDER BY b.`PersonNm` ASC";
        $query = $this->db->query($sql,array((int) $MemberID));
        return $query->result_array();
    }

    public function getTraderVehicleFormData($VehID){
        $sql="SELECT
                a.`VehID`,
                a.`MemberID`,
                a.`StaffID`,
                a.`BrandID`,
                a.`VehName`,
                a.`VehPoliceNr`,
                a.`VehCapacity`,
                a.Remark,
                a.Ownership
            FROM
                `ktv_member_vehicle` a
            WHERE
                a.`VehID` = ?
            LIMIT 1";
        $query = $this->db->query($sql,array((int) $VehID));
        $data = $query->row_array();

        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.SME.WinFormVehicle-Form-".$key;
            $dataRow[$keyNew] = $value;
        }

        $return['success'] = true;
        $return['data'] = $dataRow;
        return $return;
    }

    public function insertVehicle($varPost){
        $this->db->trans_start();

        $sql="INSERT INTO `ktv_member_vehicle` SET
                `MemberID` = ?,
                `StaffID` = ?,
                `BrandID` = ?,
                `VehName` = ?,
                `VehPoliceNr` = ?,
                `VehCapacity` = ?,
                Remark = ?,
                Ownership = ?,
                `DateCreated` = NOW(),
                `CreatedBy` = ?
            ";
        $p = array(
            $varPost['MemberID'],
            $varPost['StaffID'],
            $varPost['BrandID'],
            $varPost['VehName'],
            $varPost['VehPoliceNr'],
            $varPost['VehCapacity'],
            $varPost['Remark'],
            $varPost['Ownership'],
            $_SESSION['userid']
        );
        $query = $this->db->query($sql,$p);

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

    public function updateVehicle($varPost){
        $this->db->trans_start();

        $sql="UPDATE ktv_member_vehicle SET
                `StaffID` = ?,
                `BrandID` = ?,
                `VehName` = ?,
                `VehPoliceNr` = ?,
                `VehCapacity` = ?,
                Remark = ?,
                Ownership = ?,
                DateUpdated = NOW(),
                LastModifiedBy = ?
            WHERE
                VehID = ?
            LIMIT 1
            ";
        $p = array(
            $varPost['StaffID'],
            $varPost['BrandID'],
            $varPost['VehName'],
            $varPost['VehPoliceNr'],
            $varPost['VehCapacity'],
            $varPost['Remark'],
            $varPost['Ownership'],
            $_SESSION['userid'],
            $varPost['VehID']
        );
        $query = $this->db->query($sql,$p);

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

    public function deleteVehicle($VehID){
        $this->db->trans_start();

        $sql="UPDATE ktv_member_vehicle SET
                StatusCode = 'nullified',
                DateUpdated = NOW(),
                LastModifiedBy = ?
            WHERE
                VehID = ?
            LIMIT 1
            ";
        $p = array(
            $_SESSION['userid'],
            $VehID
        );
        $query = $this->db->query($sql,$p);

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Data deleted";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete data";
        }
        return $results;
    }
	
	public function insertWarehouses($varPost){
        $this->db->trans_start();

        $Photo = null;
        
        if($varPost['Koltiva_view_SME_WinFormWarehouses-agBusinessLocationOld'] != ""){
            $file = explode("images/trader/temp/",$varPost['Koltiva_view_SME_WinFormWarehouses-agBusinessLocationOld']);
            // //Insert ada photonya pakai aws
            if(file_exists('images/trader/temp/'.$file[1])) {
                $this->load->library('awsfileupload');
                $upload = $this->awsfileupload->upload('images/trader/temp/'.$file[1],$file[1],AWSS3_WH_SME_PLOT_PATH, 'images');
                if ($upload['success'] == true) {
                    delete_file("/".$varPost['Koltiva_view_SME_WinFormWarehouses-agBusinessLocationOld']);
                    $Photo = $upload['filenamepath'];
                }
            }
        }

        $sql="INSERT INTO `ktv_trader_warehouses` SET
                `MemberID` = ?,
                `WarehousesNr` = ?,
                 Warehousetype =?,
				`Latitude` = ?,
                `Longitude` = ?,   
                `PhotoBusinessLocation` = ?,
                `DateCreated` = NOW(),
                `CreatedBy` = ?
            ";
        $p = array(
            $varPost['MemberID'],
            $varPost['Koltiva_view_SME_WinFormWarehouses-WarehousesNr'],
			$varPost['Koltiva_view_SME_WinFormWarehouses-Warehousetype'],
            $varPost['Koltiva_view_SME_WinFormWarehouses-Latitude'],
            $varPost['Koltiva_view_SME_WinFormWarehouses-Longitude'], 
            $Photo,
            $_SESSION['userid']
        );


		$sqlCheckIfExist ="SELECT
                a.MemberID
            FROM
                ktv_trader_warehouses a
            WHERE
                a.MemberID = ?
                AND a.WarehousesNr = ?   
            LIMIT 1";
        $ps = array(
            $varPost['MemberID'],
			$varPost['Koltiva_view_SME_WinFormWarehouses-WarehousesNr']
        );
        $query = $this->db->query($sqlCheckIfExist,$ps); 
		$isExist = $query->num_rows();
        if($isExist == true){ 
			$this->db->where('MemberID', $varPost['MemberID']);
			$this->db->where('WarehousesNr', $varPost['Koltiva_view_SME_WinFormWarehouses-WarehousesNr']); 
			  $p = array( 
					'Warehousetype' => $varPost['Koltiva_view_SME_WinFormWarehouses-Warehousetype'],
					'Latitude' => $varPost['Koltiva_view_SME_WinFormWarehouses-Latitude'],
					'Longitude' => $varPost['Koltiva_view_SME_WinFormWarehouses-Longitude'],
					'StatusCode' => 'active'
				);
			$query = $this->db->update('ktv_trader_warehouses', $p);
		}else{
			$query = $this->db->query($sql,$p);
			//echo $this->db->last_query();die;
        }

        //Koordinat Geometry ============= (Begin)
        if($varPost['Koltiva_view_SME_WinFormWarehouses-Latitude'] != "" && $varPost['Koltiva_view_SME_WinFormWarehouses-Longitude'] != "") {

			$LatitudeProses = (float) $varPost['Koltiva_view_SME_WinFormWarehouses-Latitude'];
            $LongitudeProses = (float) $varPost['Koltiva_view_SME_WinFormWarehouses-Longitude'];
            
            //Check Latitude
            if( ($LatitudeProses >= -90 && $LatitudeProses <= 90) && ($LongitudeProses >= -180 && $LongitudeProses <= 180) ) {

				//Cek valid tidak koordinatnya
                $sql = "SELECT ST_IsValid(ST_GeomFromText('POINT({$LatitudeProses} {$LongitudeProses})', 4326)) AS HasilCek";
                $DataCekKoordinat = $this->db->query($sql)->row_array();
                
                if ($DataCekKoordinat['HasilCek'] == "1") {
					$PointInsert = "POINT({$LatitudeProses} {$LongitudeProses})";

					$sql = "UPDATE ktv_trader_warehouses a SET
								a.`LatLong` = ST_GEOMFROMTEXT('$PointInsert', 4326)
							WHERE
								a.`MemberID` = ?
								AND a.`WarehousesNr` = ?
							LIMIT 1";
					$p = array(
                        $varPost['MemberID'],
                        $varPost['Koltiva_view_SME_WinFormWarehouses-WarehousesNr']
					);
					$query = $this->db->query($sql,$p);
				}

            }
        }
        //Koordinat Geometry ============= (End)
		
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
	
	public function updateWarehouses($varPost){
        $this->db->trans_start();
		//echo $valAgBusinessLocation; die;
        $sql="UPDATE ktv_trader_warehouses SET
				Warehousetype = ? ,
                Latitude = ?,
                Longitude = ?,
                DateUpdated = NOW(),
                LastModifiedBy = ?
            WHERE
                WarehousesNr = ? and MemberID = ?
            LIMIT 1
            ";
        $p = array(
			$varPost['Koltiva_view_SME_WinFormWarehouses-Warehousetype'],
            $varPost['Koltiva_view_SME_WinFormWarehouses-Latitude'],
            $varPost['Koltiva_view_SME_WinFormWarehouses-Longitude'],
            $_SESSION['userid'],
            $varPost['Koltiva_view_SME_WinFormWarehouses-WarehousesNr'],
			$varPost['MemberID']
        );
        $query = $this->db->query($sql,$p);

        //Koordinat Geometry ============= (Begin)
        if($varPost['Koltiva_view_SME_WinFormWarehouses-Latitude'] != "" && $varPost['Koltiva_view_SME_WinFormWarehouses-Longitude'] != "") {

			$LatitudeProses = (float) $varPost['Koltiva_view_SME_WinFormWarehouses-Latitude'];
            $LongitudeProses = (float) $varPost['Koltiva_view_SME_WinFormWarehouses-Longitude'];
            
            //Check Latitude
            if( ($LatitudeProses >= -90 && $LatitudeProses <= 90) && ($LongitudeProses >= -180 && $LongitudeProses <= 180) ) {

				//Cek valid tidak koordinatnya
                $sql = "SELECT ST_IsValid(ST_GeomFromText('POINT({$LatitudeProses} {$LongitudeProses})', 4326)) AS HasilCek";
                $DataCekKoordinat = $this->db->query($sql)->row_array();
                
                if ($DataCekKoordinat['HasilCek'] == "1") {
					$PointInsert = "POINT({$LatitudeProses} {$LongitudeProses})";

					$sql = "UPDATE ktv_trader_warehouses a SET
								a.`LatLong` = ST_GEOMFROMTEXT('$PointInsert', 4326)
							WHERE
								a.`MemberID` = ?
								AND a.`WarehousesNr` = ?
							LIMIT 1";
					$p = array(
                        $varPost['MemberID'],
                        $varPost['Koltiva_view_SME_WinFormWarehouses-WarehousesNr']
					);
					$query = $this->db->query($sql,$p);
				}

            }
        }
        //Koordinat Geometry ============= (End)

		//echo $this->db->last_query();die;
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
	
	public function checkIfWarehouseExist($paramPost){
        $sql="SELECT
                a.`MemberID`
            FROM
                ktv_trader_warehouses a
            WHERE
                a.MemberID = ?
                AND a.`WarehousesNr` = ?   
                AND a.`StatusCode` = 'active'
            LIMIT 1";
        $p = array(
            $paramPost['MemberID'],
            $paramPost['Koltiva_view_SME_WinFormWarehouses-WarehousesNr'] 
        );
        $query = $this->db->query($sql,$p);
        $data = $query->row_array();
		
		if($data){
			if($data['MemberID'] != ""){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
    }
    
	
	public function getWarehousesForm($MemberID, $WarehousesNr){
        $this->load->library('awsfileupload');
        $sql="SELECT
                a.MemberID AS \"Koltiva.view.SME.WinFormWarehouses-MemberID\"
                , a.WarehousesNr AS \"Koltiva.view.SME.WinFormWarehouses-WarehousesNr\"
				, a.Warehousetype AS \"Koltiva.view.SME.WinFormWarehouses-Warehousetype\"
                , ST_Y(a.LatLong) AS \"Koltiva.view.SME.WinFormWarehouses-Latitude\"
                , ST_X(a.LatLong) AS \"Koltiva.view.SME.WinFormWarehouses-Longitude\"
                , a.PhotoBusinessLocation 
            FROM
                ktv_trader_warehouses a 
            WHERE
                a.MemberID = ? and a.WarehousesNr = ? 
            LIMIT 1";
        $query = $this->db->query($sql,array((int) $MemberID, $WarehousesNr));
        $data = $query->row_array();
        $data["WarehousesNr"] = $data["Koltiva.view.SME.WinFormWarehouses-WarehousesNr"];
        if($this->awsfileupload->doesObjectExist($data['PhotoBusinessLocation']) == true) {
            $data['PhotoSrcPath'] = $data['PhotoBusinessLocation'];
            $data['PhotoBusinessLocation'] = $this->config->item('CTCDN')."/".$data['PhotoBusinessLocation'];
        }else{
            $data['PhotoSrcPath'] = 'images/trader_business/'.$data["WarehousesNr"].'/'.$data['PhotoBusinessLocation'];
            $data['PhotoBusinessLocation'] = base_url().'images/trader_business/'.$data["WarehousesNr"].'/'.$data['PhotoBusinessLocation'];
        }
        $return['success'] = true;
        $return['data'] = $data;
        return $return;
    }
	
	 public function deleteWarehouses($MemberID, $WarehousesNr){
        $this->db->trans_start();

        $sql="UPDATE ktv_trader_warehouses SET
                StatusCode = 'nullified',
                DateUpdated = NOW(),
                LastModifiedBy = ?
            WHERE
                WarehousesNr = ? and MemberID = ? 
            LIMIT 1
            ";
        $p = array(
            $_SESSION['userid'],
            $WarehousesNr,
			$MemberID
        );
        $query = $this->db->query($sql,$p);

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Data deleted";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete data";
        }
        return $results;
    }
	
	
	public function getMemberDataDetail($MemberID) {
        $sql = "SELECT
                a.`MemberID`
                , a.`MemberDisplayID`
                , a.`DateCollection`
                , a.`MemberName`
                , a.`MemberName` AS MemberNameTtd
                , a.`DateOfBirth`
                , a.`Gender`
                , a.`MaritalStatus`
                , a.`Education`
                , SUBSTR(a.`VillageID`,1,2) AS ProvinceID
                , SUBSTR(a.`VillageID`,1,4) AS DistrictID
                , SUBSTR(a.`VillageID`,1,7) AS SubDistrictID
                , CONCAT(subd.`SubDistrict`,', ',vil.`Village`) AS MemberLocation
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
                , ST_Y(a.LatLong) Longitude
                , ST_X(a.LatLong) Longitude
                , c.agYearEstablished
                , a.`MemberUID`
            FROM
                ktv_members a
                LEFT JOIN ktv_member_role b ON a.`MemberID` = b.`MemberID`
                LEFT JOIN ktv_ref_member_role ref_role ON b.MRoleID = ref_role.MRoleID
                LEFT JOIN ktv_members_extension c ON a.MemberID = c.MemberID
                LEFT JOIN ktv_village vil ON a.`VillageID` = vil.`VillageID`
                LEFT JOIN ktv_subdistrict subd ON vil.`SubDistrictID` = subd.`SubDistrictID`
                LEFT JOIN ktv_district dis ON subd.DistrictID = dis.DistrictID
                LEFT JOIN ktv_province prov ON dis.ProvinceID = prov.ProvinceID
            WHERE
                a.`MemberID` = ?
            GROUP BY a.`MemberID`
            LIMIT 1";
        $query = $this->db->query($sql, array((int) $MemberID));
        $data = $query->row_array();
		//echo '<pre>'.$this->db->last_query();die;
        $return['success'] = true;
        $return['data'] = $data;
        return $return;
    }
	
	 public function getStaffTrader($MemberID){
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

        $arrReturn['NrOfStaff'] = number_format($data['BANYAK'],0,".",",");
        $arrReturn['StaffLaki'] = @($data['StaffLaki'] / $data['BANYAK']) * 100;
        $arrReturn['StaffLaki'] = number_format($arrReturn['StaffLaki'],0,".",",");
        $arrReturn['StaffPerempuan'] = 100 - $arrReturn['StaffLaki'];
        if($arrReturn['NrOfStaff'] == 0){
            $arrReturn['StaffLaki'] = 0;
            $arrReturn['StaffPerempuan'] = 0;
        }

        return $arrReturn;
    }
	
	 
	
	function getTraceabilityDetails($MemberID){
        $sql="SELECT
                    IFNULL(st.SupplyBatchID, st.SupplyTransID) BatchID,
                    SUBSTR(IFNULL(sb.SupplyBatchDate, st.DateTransaction),1,10) DateTransaction,
                    SUM(st.VolumeNetto) VolumeNetto, 
                    IFNULL(vso2.`Name`, '-') Delivered
                FROM
                    ktv_tc_supplychain_transaction st
                    LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID=st.SupplyBatchID
                    LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=st.SupplychainID
                    LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID=sb.SupplyDestOrgID
                WHERE vso.ObjID = ? AND YEAR(st.DateTransaction)=YEAR(NOW())
                GROUP BY IFNULL(st.SupplyBatchID, st.SupplyTransID)";
        $query = $this->db->query($sql, array((int) $MemberID));
        ///echo "<pre>".$this->db->last_query();die;
        return $query->result_array();
    }
	
	function getFFBSales($MemberID){
        $sql = "SELECT
                    FORMAT(COUNT(DISTINCT st.SupplyTransID),0) Trans,
                    FORMAT(COUNT(DISTINCT IFNULL(st.SupplyBatchID, 0)),0) Batch,
                    FORMAT(ROUND(SUM(st.VolumeNetto)/1000),0) Netto,
                    YEAR(st.DateTransaction) Year,
                    CASE WHEN MONTH(st.DateTransaction) <= 3 THEN 1
                        WHEN MONTH(st.DateTransaction) > 3 && MONTH(st.DateTransaction) <= 6  THEN 2
                        WHEN MONTH(st.DateTransaction) > 6 && MONTH(st.DateTransaction) <= 8  THEN 3
                        ELSE 4
                    END AS Bulan
                FROM
                    ktv_tc_supplychain_transaction st
                    LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=st.SupplychainID
                WHERE vso.ObjID= ?  AND st.DateTransaction IS NOT NULL
                GROUP BY Year,Bulan ORDER BY Bulan DESC";
        $query = $this->db->query($sql, array($MemberID));
        $ffb = $query->result_array();
        $sql = "SELECT
                    YEAR(st.DateTransaction) Year,
                    CASE WHEN MONTH(st.DateTransaction) <= 3 THEN 1
                        WHEN MONTH(st.DateTransaction) > 3 && MONTH(st.DateTransaction) <= 6  THEN 2
                        WHEN MONTH(st.DateTransaction) > 6 && MONTH(st.DateTransaction) <= 8  THEN 3
                    ELSE 4
                    END AS Bulan,
                    FORMAT(COUNT(DISTINCT m.MemberID),0) Farmer
                FROM
                    ktv_tc_supplychain_transaction st
                    LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=st.SupplychainID
                    LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchNumber=st.SupplyID AND st.SupplyType='Batch'
                    LEFT JOIN ktv_tc_supplychain_transaction st2 ON st2.SupplyBatchID=sb2.SupplyBatchID
                    LEFT JOIN ktv_supplychain_batch sb3 ON sb3.SupplyBatchNumber=st2.SupplyID AND st2.SupplyType='Batch'
                    LEFT JOIN ktv_tc_supplychain_transaction st3 ON st3.SupplyBatchID=sb3.SupplyBatchID
                    LEFT JOIN ktv_members m ON m.MemberID=IFNULL(st3.SupplyID, IFNULL(st2.SupplyID, st.SupplyID)) OR  m.MemberDisplayID=IFNULL(st3.SupplyID, IFNULL(st2.SupplyID, st.SupplyID))
                WHERE vso.ObjID = ? AND st.DateTransaction IS NOT NULL
                GROUP BY Year, Bulan ORDER BY Bulan DESC";
        $query = $this->db->query($sql, array($MemberID))->result_array();
        for($i=0;$i<count($ffb);$i++){
            $ffb[$i]['Farmer'] = $query[$i]['Farmer'];
        }
        return $ffb;
    }
    
    public function getCmbSmeType(){
        $sqlsme = "SELECT a.SMETypeID as id, a.SMEType as label FROM ktv_ref_sme_type as a
            WHERE a.StatusCode = 'active'
            ORDER BY label ASC";
        $querysme = $this->db->query($sqlsme);
        $datasme = $querysme->result_array();

        //Add lang
        foreach ($datasme as $key => $value) {
            $datasme[$key]['label'] = lang($value['label']);
        }

        return $datasme;
    }
}
?>