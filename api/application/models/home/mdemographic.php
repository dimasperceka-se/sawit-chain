<?php

class Mdemographic extends CI_Model {

    function __construct() {
      parent::__construct();
      $this->demographic_satu = "
SELECT SUM(Farmer) farmer
    , SUM(Female)/(SUM(Female)+SUM(Male))*100 female_persen
    , SUM(Age)/(SUM(Farmer)-SUM(Unage)) age_avg
    , SUM(PassPrimarySchool)/(SUM(PassPrimarySchool)+SUM(NotPassPrimarySchool))*100  cps_persen
    , COUNT(DISTINCT ProvinceID) province
    , COUNT(DISTINCT DistrictID) district
    , COUNT(DISTINCT SubDistrictID) subdistrict
    , COUNT(DISTINCT VillageID) village
    , SUM(Below125)/SUM(PpiFarmer) sdl
    , SUM(Below25)/SUM(PpiFarmer) dl
    , SUM(Young)/(SUM(Farmer)-SUM(Unage))*100 young
    , SUM(Household)/SUM(Household_count) household
    , SUM(Age1524) Age1524
    , SUM(Age2534) Age2534
    , SUM(Age3544) Age3544
    , SUM(Age4554) Age4554
    , SUM(Age55) Age55
    , SUM(NotSchool) NotSchool
    , SUM(PrimarySchoolIncomplete) PrimarySchoolIncomplete
    , SUM(PrimarySchoolcompleted) PrimarySchoolcompleted
    , SUM(JuniorHighSchool) JuniorHighSchool
    , SUM(SeniorHighSchool) SeniorHighSchool
    , SUM(TertiarySchool) TertiarySchool
    , SUM(Family1) hh1
    , SUM(Family2) hh2
    , SUM(Family3) hh3
    , SUM(Family4) hh4
    , SUM(Family5) hh5
    , SUM(Family6) hh6
    , SUM(Nasional) Nasional
    , SUM(Married) Married
    , SUM(Single) Single
    , SUM(Widow) Widow
FROM dash_demographic a
WHERE 1=1 %s";
     $this->demographic_dua = "
         SELECT %s label,   SUM(Farmer) Farmer, SUM(Female) female,SUM(Male) male,SUM(Age)/(SUM(Farmer)) age,
         	SUM(Nasional)/SUM(NasionalCount) Nasional,SUM(Below125)/SUM(NasionalCount) sdl,SUM(Below25)/SUM(NasionalCount) dl,
         	SUM(Household)/SUM(Household_count) hh,SUM(Firewood) Firewood,SUM(GasOther) GasOther,
         	SUM(RefrigeratorYes) RefrigeratorYes,SUM(RefrigeratorNo) RefrigeratorNo,
         	SUM(MotorcycleYes) MotorcycleYes,SUM(MotorcycleNo) MotorcycleNo,
            sum(Family1) hh1,SUM(Family2) hh2,SUM(Family3) hh3,SUM(Family4) hh4,SUM(Family5) hh5,SUM(Family6) hh6
         FROM dash_demographic a
         %s
         WHERE Farmer>0 %s
         GROUP BY %s
         ORDER BY label";
    }

    function readDataDemographic($prov = '', $kab = '', $petani = '', $tahun = '') {
        if ($petani == '1') {
            $tahun = ! empty($tahun) ? $tahun : date('Y');
            $where .= " AND Certified='$tahun'";
        } else $where .= " AND Certified is null";
        if ($prov == '') {
            $label = 'Province';
            $LEFT .= ' LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
                    LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
                    LEFT JOIN ktv_province z ON z.ProvinceID = kd.ProvinceID';
            $groupby = 'z.ProvinceID';
        } elseif ($kab == '') {
            $label = 'District';
            $LEFT .= ' LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
                    LEFT JOIN ktv_district z ON z.DistrictID = ksd.DistrictID';
            $where .= ' and z.ProvinceID=?';
            $groupby = 'z.DistrictID';
        } else {
            $label = 'SubDistrict';
            $LEFT .= ' LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                    LEFT JOIN ktv_subdistrict z ON z.SubDistrictID = kv.SubDistrictID';
            $where .= ' and z.DistrictID=?';
            $groupby = 'label';
        }

        if ($kab != '') $prov = $kab;
        $query_satu              = $this->db->query(sprintf($this->demographic_satu, $where), array($prov));
        $results['data']         = $query_satu->result_array();
        $query_kedua             = $this->db->query(sprintf($this->demographic_dua, $label,$LEFT, $where,$groupby), array($prov));
        $results['kedua']        = $query_kedua->result_array();

        return $results;
    }
    function readDataDistrictDemographic($user, $district, $priv = '', $petani = '', $partner = '', $prov = '') {
        if ($petani == '1') {
            $tahun = ! empty($tahun) ? $tahun : date('Y');
            $where .= " AND Certified='$tahun'";
        } else $where .= " AND Certified is null"; 
        $where .= ' and kd.DistrictID in (%s)';
        if (empty($prov)) {
            $label = 'Province';
            $LEFT .= ' LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
                    LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
                    LEFT JOIN ktv_province kp ON kp.ProvinceID = kd.ProvinceID';
            $groupby = 'kd.ProvinceID';
        } else {
            $where .= ' and kd.ProvinceID = ' . $prov;
            if ($priv == '') {
                $label = 'District';
                $LEFT .= ' LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
                    LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
                    LEFT JOIN ktv_province kp ON kp.ProvinceID = kd.ProvinceID';
                $groupby = 'kd.DistrictID';
            } else {
                $label = 'SubDistrict';
                $LEFT .= ' LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
                    LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
                    LEFT JOIN ktv_province kp ON kp.ProvinceID = kd.ProvinceID';
                $where .= ' and kd.DistrictID=?';
                $groupby = 'ksd.SubDistrictID';
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
        if ($_SESSION['FlagAccess']) {
            $where .= " AND `CPGid` IN (SELECT CPGid FROM `ktv_cpg_partner` WHERE `PartnerID` = {$_SESSION['PartnerID']})";
        }
        $query_satu              = $this->db->query(sprintf(sprintf($this->demographic_satu,$where),
            implode(',', $dist)), array($priv));
        $results['data']         = $query_satu->result_array();
        $query_kedua             = $this->db->query(sprintf(sprintf($this->demographic_dua, $label,$LEFT, $where,$groupby), 
            implode(',', $dist)), array($priv));
        $results['kedua']        = $query_kedua->result_array();

        return $results;
    }

}

?>
