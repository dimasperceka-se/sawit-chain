<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mnutrition extends CI_Model {

    public $variable;

    public function __construct()
    {
        parent::__construct();
        $this->nutrition_farmer = "SELECT
    %s AS label
    ,SUM(kcf.farmer) AS farmer
    ,SUM(kcf.male) AS male
    ,SUM(kcf.female) AS female
    ,SUM(kcf.sum_farmer_age) AS sum_farmer_age
FROM dash_gnp kcf
%s
WHERE 1 = 1
%s
GROUP BY label
        ";
        $this->nutrition_data = "SELECT
    %s AS label,
    SUM(kcf.farmer) AS farmer,
    SUM(kcf.farmer_distinct) AS farmer_distinct,
    SUM(kcf.IDDS) AS IDDS,
    SUM(kcf.score_total) AS score_total,
    SUM(kcf.score_male) AS score_male,
    SUM(kcf.male_idds) AS male_idds,
    SUM(kcf.score_female) AS score_female,
    SUM(kcf.female_idds) AS female_idds,
    SUM(kcf.GardenYes) AS GardenYes,
    SUM(kcf.avgGardenSizeMod) AS avgGardenSizeMod,
    SUM(kcf.sumGardenSizeMod) AS sumGardenSizeMod,
    SUM(kcf.farmerMod) AS farmerMod,
    SUM(kcf.GardenSizeMod_Farmer) AS GardenSizeMod_Farmer,
    IFNULL(SUM(kcf.Spinach),0) AS Spinach,
    IFNULL(SUM(kcf.Chilli),0) AS Chilli,
    IFNULL(SUM(kcf.LongBean),0) AS LongBean,
    IFNULL(SUM(kcf.WaterCress),0) AS WaterCress,
    IFNULL(SUM(kcf.Mustard),0) AS Mustard,
    IFNULL(SUM(kcf.Eggplant),0) AS Eggplant,
    IFNULL(SUM(kcf.Tomato),0) AS Tomato,
    IFNULL(SUM(kcf.Goat),0) AS Goat,
    IFNULL(SUM(kcf.Cow),0) AS Cow,
    IFNULL(SUM(kcf.Duck),0) AS Duck,
    IFNULL(SUM(kcf.Chicken),0) AS Chicken,
    IFNULL(SUM(kcf.Fish),0) AS Fish,
    IFNULL(SUM(kcf.Sheep),0) AS Sheep,
    IFNULL(SUM(kcf.Buffalo),0) AS Buffalo,
    IFNULL(SUM(kcf.Pig),0) AS Pig,
    SUM(kcf.established_garden) AS Established_Garden,
    SUM(kcf.fish_pond) AS Fish_Pond,
    SUM(kcf.count_farmer_fish_pond_area) AS count_farmer_fish_pond_area,
    SUM(kcf.sum_fish_pond_area) AS sum_fish_pond_area
FROM dash_nutrition kcf
%s
WHERE 1 = 1
%s
GROUP BY label
        ";
    }

    function readDataNutrition($prov = '', $kab = '')
    {
        $where  = '';
        $LEFT   = '';
        if ($prov == '') {
            $label = 'Province';
            $LEFT .= 'LEFT JOIN ktv_village kv ON kv.villageID = kcf.VillageID
                    LEFT JOIN ktv_subdistrict ksubdis ON kv.SubDistrictID = ksubdis.SubDistrictID
                    LEFT JOIN ktv_district kdis ON ksubdis.DistrictID = kdis.DistrictID 
                    LEFT JOIN ktv_province kp on kp.ProvinceID=kdis.ProvinceID';
        } elseif ($kab == '') {
            $label = 'District';
            $LEFT .= ' LEFT JOIN ktv_village kv ON kv.villageID = kcf.VillageID
                    LEFT JOIN ktv_subdistrict ksubdis ON kv.SubDistrictID = ksubdis.SubDistrictID
                    LEFT JOIN ktv_district kp ON ksubdis.DistrictID = kp.DistrictID';
            $where .= ' and kdis.ProvinceID=?';
        } else {
            $label = 'SubDistrict';
            $LEFT .= ' LEFT JOIN ktv_village kv ON kv.villageID = kcf.VillageID
                    LEFT JOIN ktv_subdistrict kp ON kv.SubDistrictID = kp.SubDistrictID';
            $where = ' and kp.DistrictID=?';
        }
        if ($kab != '') $prov = $kab;
        $query_farmer   = $this->db->query(sprintf($this->nutrition_farmer, $label, $LEFT, $where), array($prov));
        $query_data     = $this->db->query(sprintf($this->nutrition_data, $label, $LEFT, $where), array($prov));

        $results['farmer']  = $query_farmer->result_array();
        $results['data']    = $query_data->result_array();
        return $results;
    }

    function readDataDistrictNutrition($user, $district, $priv = '', $partner = '', $prov = '')
    {
        $where = '';
        $LEFT = '';
        $where .= ' AND ksubdis.DistrictID in (%s)';
        if (empty($prov)) {
            $label = 'Province';
            $LEFT .= ' LEFT JOIN ktv_village kv ON kv.villageID = kcf.VillageID
                    LEFT JOIN ktv_subdistrict ksubdis ON kv.SubDistrictID = ksubdis.SubDistrictID
                    LEFT JOIN ktv_district kdis ON ksubdis.DistrictID = kdis.DistrictID 
                    LEFT JOIN ktv_province kp on kp.ProvinceID=kdis.ProvinceID';
            $groupby = 'kp.ProvinceID';
        } else {
            $where .= ' AND kdis.ProvinceID = ' . $prov;
            if ($priv == '') {
                $label = 'District';
                $LEFT .= ' LEFT JOIN ktv_village kv ON kv.villageID = kcf.VillageID
                        LEFT JOIN ktv_subdistrict ksubdis ON kv.SubDistrictID = ksubdis.SubDistrictID
                        LEFT JOIN ktv_district kdis ON ksubdis.DistrictID = kdis.DistrictID';
                $groupby = 'kdis.DistrictID';
            } else {
                $label = 'SubDistrict';
                $LEFT .= ' LEFT JOIN ktv_village kv ON kv.villageID = kcf.VillageID
                        LEFT JOIN ktv_subdistrict ksubdis on ksubdis.SubDistrictID=kv.SubDistrictID';
                $where .= ' AND kp.DistrictID=?';
                $groupby = 'ksubdis.SubDistrictID';
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
            $where .= " AND kcf.`CPGid` IN (SELECT CPGid FROM `ktv_cpg_partner` WHERE `PartnerID` = {$_SESSION['PartnerID']})";
        }

        $query_farmer       = $this->db->query(sprintf(sprintf($this->nutrition_farmer, $label, $LEFT, $where), implode(',', $dist)), array($priv));
        $query_data         = $this->db->query(sprintf(sprintf($this->nutrition_data, $label, $LEFT, $where), implode(',', $dist)), array($priv));

        $results['farmer']  = $query_farmer->result_array();
        $results['data']    = $query_data->result_array();

        return $results;
    }

}

/* End of file mnutrition.php */
/* Location: ./application/models/mnutrition.php */