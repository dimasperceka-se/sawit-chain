<?php

class Mgroups extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->sql_cpg = "
SELECT 
    %s AS label
    , COUNT(CPGid) AS cpg
    , SUM(IF(established_year = 2010,1,0)) AS est_2010
    , SUM(IF(established_year = 2011,1,0)) AS est_2011
    , SUM(IF(established_year = 2012,1,0)) AS est_2012
    , SUM(IF(established_year = 2013,1,0)) AS est_2013
    , SUM(IF(established_year = 2014,1,0)) AS est_2014
    , SUM(IF(established_year = 2015,1,0)) AS est_2015
    , SUM(IF(established_year = 2016,1,0)) AS est_2016
    , SUM(IF(established_year = 2017,1,0)) AS est_2017
    , IFNULL(SUM(ada_pengurus),0) AS ada_pengurus
    , IFNULL(SUM(tidak_ada_pengurus),0) AS tidak_ada_pengurus
    , IFNULL(SUM(ketua_m),0) AS ketua_m
    , IFNULL(SUM(ketua_f),0) AS ketua_f
    , IFNULL(SUM(sekretaris_m),0) AS sekretaris_m
    , IFNULL(SUM(sekretaris_f),0) AS sekretaris_f
    , IFNULL(SUM(bendahara_m),0) AS bendahara_m
    , IFNULL(SUM(bendahara_f),0) AS bendahara_f
FROM (
SELECT 
    cpg.CPGid,
    cpg.VillageID,
    v.SubDistrictID,
    established_year,
    IF(`AdaPengurus` = 1,1,NULL) AS ada_pengurus,
    IF(`AdaPengurus` != 1,1,NULL) AS tidak_ada_pengurus,
    IF(`Ketua`,1,NULL) AS ketua,
    IF(`Ketua` AND fa.`Gender` = 1,1,NULL) AS ketua_m,
    IF(`Ketua` AND fa.`Gender` = 2,1,NULL) AS ketua_f,
    IF(`Sekretaris`,1,NULL) AS sekretaris,
    IF(`Sekretaris` AND fb.`Gender` = 1,1,NULL) AS sekretaris_m,
    IF(`Sekretaris` AND fb.`Gender` = 2,1,NULL) AS sekretaris_f,
    IF(`Bendahara`,1,NULL) AS bendahara,
    IF(`Bendahara` AND fc.`Gender` = 1,1,NULL) AS bendahara_m,
    IF(`Bendahara` AND fc.`Gender` = 2,1,NULL) AS bendahara_f
FROM ktv_cpg cpg
JOIN (
    SELECT
    CPGid,
    YEAR(MIN(TrainingStart)) AS established_year
    FROM ktv_cpg_batch_trainings 
    WHERE 
        TrainingStart > 0
    GROUP BY CPGid
) cbt ON cbt.CPGid = cpg.CPGid 
LEFT JOIN ktv_village v ON v.VillageID = cpg.VillageID
LEFT JOIN ktv_farmer fa ON fa.`FarmerID` = Ketua
LEFT JOIN ktv_farmer fb ON fb.`FarmerID` = Sekretaris
LEFT JOIN ktv_farmer fc ON fc.`FarmerID` = Bendahara
GROUP BY cpg.CPGid
) kcf
%s
WHERE
    1 = 1
    %s
GROUP BY label        
        ";
        $this->sql_coop = "
SELECT    
    %s AS label
    , COUNT(CoopID) AS coop
    , IFNULL(SUM(have_management),0) AS ada_pengurus
    , IFNULL(SUM(dont_have_management),0) AS tidak_ada_pengurus
    , IFNULL(SUM(ketua_m),0) AS ketua_m
    , IFNULL(SUM(wakil_ketua_m),0) AS wakil_ketua_m
    , IFNULL(SUM(sekretaris_m),0) AS sekretaris_m
    , IFNULL(SUM(wakil_sekretaris_m),0) AS wakil_sekretaris_m
    , IFNULL(SUM(bendahara_m),0) AS bendahara_m
    , IFNULL(SUM(wakil_bendahara_m),0) AS wakil_bendahara_m
    , IFNULL(SUM(ketua_f),0) AS ketua_f
    , IFNULL(SUM(wakil_ketua_f),0) AS wakil_ketua_f
    , IFNULL(SUM(sekretaris_f),0) AS sekretaris_f
    , IFNULL(SUM(wakil_sekretaris_f),0) AS wakil_sekretaris_f
    , IFNULL(SUM(bendahara_f),0) AS bendahara_f
    , IFNULL(SUM(wakil_bendahara_f),0) AS wakil_bendahara_f
FROM (
SELECT
    kc.CoopID,
    kc.VillageID,
    v.SubDistrictID,
    IF(kcs.`StaffID`,1,0) AS have_management,
    IF(kcs.`StaffID` IS NULL,1,0) AS dont_have_management,
    SUM(IF(kcs.`Position` = 'Ketua' AND (kcf.Gender = 1 OR kcs.`StaffGender` = 1), 1, 0)) ketua_m,
    SUM(IF(kcs.`Position` = 'Wakil Ketua' AND (kcf.Gender = 1 OR kcs.`StaffGender` = 1), 1, 0)) wakil_ketua_m,
    SUM(IF(kcs.`Position` = 'Sekretaris' AND (kcf.Gender = 1 OR kcs.`StaffGender` = 1), 1, 0)) sekretaris_m,
    SUM(IF(kcs.`Position` = 'Wakil Sekretaris' AND (kcf.Gender = 1 OR kcs.`StaffGender` = 1), 1, 0)) wakil_sekretaris_m,
    SUM(IF(kcs.`Position` = 'Bendahara' AND (kcf.Gender = 1 OR kcs.`StaffGender` = 1), 1, 0)) bendahara_m,
    SUM(IF(kcs.`Position` = 'Wakil Bendahara' AND (kcf.Gender = 1 OR kcs.`StaffGender` = 1), 1, 0)) wakil_bendahara_m,    
    SUM(IF(kcs.`Position` = 'Ketua' AND (kcf.Gender = 2 OR kcs.`StaffGender` = 2), 1, 0)) ketua_f,
    SUM(IF(kcs.`Position` = 'Wakil Ketua' AND (kcf.Gender = 2 OR kcs.`StaffGender` = 2), 1, 0)) wakil_ketua_f,
    SUM(IF(kcs.`Position` = 'Sekretaris' AND (kcf.Gender = 2 OR kcs.`StaffGender` = 2), 1, 0)) sekretaris_f,
    SUM(IF(kcs.`Position` = 'Wakil Sekretaris' AND (kcf.Gender = 2 OR kcs.`StaffGender` = 2), 1, 0)) wakil_sekretaris_f,
    SUM(IF(kcs.`Position` = 'Bendahara' AND (kcf.Gender = 2 OR kcs.`StaffGender` = 2), 1, 0)) bendahara_f,
    SUM(IF(kcs.`Position` = 'Wakil Bendahara' AND (kcf.Gender = 2 OR kcs.`StaffGender` = 2), 1, 0)) wakil_bendahara_f
FROM ktv_cooperatives kc
LEFT JOIN ktv_village v ON v.VillageID = kc.VillageID
LEFT JOIN ktv_cooperative_staff kcs ON kc.CoopID = kcs.CoopID
LEFT JOIN ktv_farmer kcf ON kcf.FarmerID = kcs.FarmerID AND kcf.StatusCode = 'active'
GROUP BY kc.CoopID
) kcf
%s
WHERE 1 = 1
    %s
GROUP BY label";
        $this->sql_trader = "
SELECT
    %s AS label,
    COUNT(kcf.TraderID) AS trader,
    SUM(male) AS male,
    SUM(female) AS female
FROM (
SELECT
    kcf.TraderID,
    kcf.VillageID,
    v.SubDistrictID,
    IF(`Sex`=1,1,0) AS male,
    IF(`Sex`=2,1,0) AS female
FROM ktv_traders kcf
LEFT JOIN ktv_village v ON v.VillageID = kcf.VillageID
) kcf
%s
WHERE 1 = 1
    %s
GROUP BY label";
        $this->sql_nursery = "SELECT
    %s AS label,
    COUNT(NurseryID) AS nursery,
    SUM(IF(ObjType = 'farmer',1,0)) AS nursery_farmer,
    SUM(IF(ObjType = 'cpg',1,0)) AS nursery_cpg,
    SUM(IF(ObjType = 'coop',1,0)) AS nursery_coop,
    SUM(IF(ObjType = 'trader',1,0)) AS nursery_trader,
    SUM(Kapasitas) AS Kapasitas
FROM (
    SELECT
        kn.*,
        v.SubDistrictID
    FROM (
    SELECT
        kn.NurseryID,
        kn.ObjType,
        kc.VillageID,
        kn.Kapasitas
    FROM ktv_nursery kn
    JOIN ktv_cpg kc ON kc.CPGid = kn.ObjID AND kn.ObjType = 'cpg'
    UNION ALL
    SELECT
        kn.NurseryID,
        kn.ObjType,
        kcf.VillageID,
        kn.Kapasitas
    FROM ktv_nursery kn
    JOIN ktv_farmer kcf ON kcf.FarmerID = kn.ObjID AND kn.ObjType = 'farmer'
    UNION ALL
    SELECT
        kn.NurseryID,
        kn.ObjType,
        kc.VillageID,
        kn.Kapasitas
    FROM ktv_nursery kn
    JOIN ktv_cooperatives kc ON kc.CoopID = kn.ObjID AND kn.ObjType = 'koperasi'
    UNION ALL
    SELECT
        kn.NurseryID,
        kn.ObjType,
        kt.VillageID,
        kn.Kapasitas
    FROM ktv_nursery kn
    JOIN ktv_traders kt ON kt.TraderID = kn.ObjID AND kn.ObjType = 'trader'
    ) kn
    LEFT JOIN ktv_village v ON v.VillageID = kn.VillageID
) kcf
%s
WHERE 1 = 1
    %s
GROUP BY label";
    }

    function readDataGroups($prov = '', $kab = ''){
        $LEFT = '';
        $where = '';
        if ($prov == '') {
            $label = 'kp.Province';
            $LEFT .= ' LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                    LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
                    LEFT JOIN ktv_province kp ON kp.`ProvinceID` = kd.`ProvinceID`';
        } elseif ($kab == '') {
            $label = 'kd.District';
            $LEFT .= ' LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                    LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`';
            $where .= ' and kd.ProvinceID=?';
        } else {
            $label = 'ksd.SubDistrict';
            $LEFT .= ' LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`';
            $where = ' and ksd.DistrictID=?';
        }
        if ($kab != '') $prov = $kab;
        $query_cpg              = $this->db->query(sprintf($this->sql_cpg, $label, $LEFT, $where), array($prov));
        $results['cpg']         = $query_cpg->result_array();
        $query_coop             = $this->db->query(sprintf($this->sql_coop, $label, $LEFT, $where), array($prov));
        $results['coop']        = $query_coop->result_array();
        $query_trader           = $this->db->query(sprintf($this->sql_trader, $label, $LEFT, $where), array($prov));
        $results['trader']      = $query_trader->result_array();
        $query_nursery          = $this->db->query(sprintf($this->sql_nursery, $label, $LEFT, $where), array($prov));
        $results['nursery']     = $query_nursery->result_array();

        return $results;
    }

    function readDataDistrictGroups($user, $district, $priv = '', $partner = '', $prov = ''){
        $cpgs = '';
        $LEFT = '';
        $where .= ' and ksd.DistrictID in (%s)';
        if (empty($prov)) {
            $label = 'kp.Province';
            $LEFT .= ' LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                    LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
                    LEFT JOIN ktv_province kp ON kp.`ProvinceID` = kd.`ProvinceID`';
        } else {
            $where .= ' and kd.ProvinceID = ' . $prov;
            if ($priv == '') {
                $label = 'kd.District';
                $LEFT .= ' LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
                        LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                        LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
                        LEFT JOIN ktv_province kp ON kp.`ProvinceID` = kd.`ProvinceID`';
            } else {
                $label = 'ksd.SubDistrict';
                $LEFT .= ' LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
                        LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                        LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
                        LEFT JOIN ktv_province kp ON kp.`ProvinceID` = kd.`ProvinceID`';
                $where .= ' and ksd.DistrictID=?';
            }
        }
        
        $dist = array();
        // if ($user['isProgramStaff'] == 1) {
        //     $dist[] = $user['accessStaff'];
        // } else {
        //     $dist[] = $user['districtPartner'];
        // }
        $dist[] = $user['district_access'];
        // if ($user['isPrivateStaff'] AND $user['FlagAccess']) {
        //     $where_cpg = " AND kcf.`CPGid` IN (SELECT CPGid FROM `ktv_cpg_partner` WHERE `PartnerID` = {$user['privatePartner']})";
        // }

        $query_cpg              = $this->db->query(sprintf(sprintf($this->sql_cpg, $label, $LEFT, $where), implode(',', $dist)), array($priv));
        $results['cpg']         = $query_cpg->result_array();
        $query_coop             = $this->db->query(sprintf(sprintf($this->sql_coop, $label, $LEFT, $where), implode(',', $dist)), array($priv));
        $results['coop']        = $query_coop->result_array();
        $query_trader           = $this->db->query(sprintf(sprintf($this->sql_trader, $label, $LEFT, $where), implode(',', $dist)), array($priv));
        $results['trader']      = $query_trader->result_array();
        $query_nursery          = $this->db->query(sprintf(sprintf($this->sql_nursery, $label, $LEFT, $where), implode(',', $dist)), array($priv));
        $results['nursery']     = $query_nursery->result_array();

        return $results;
    }
    private function get_cpgs($partner_id) {
        $sql = "
            SELECT
                GROUP_CONCAT(`CPGid`) AS cpgs
            FROM
                `ktv_cpg_partner`
            WHERE
               `PartnerID` = ?";
        $query = $this->db->query($sql, array($partner_id));
        if ($query->num_rows() > 0) {
            return $query->row(0)->cpgs;
        }
    }

}

?>
