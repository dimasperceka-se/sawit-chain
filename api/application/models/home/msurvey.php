<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Msurvey extends CI_Model {

    private $survey_garden = "";
    private $survey_nutrition = "";
    private $survey_ppi = "";

    public function __construct()
    {
        parent::__construct();
        $this->survey_garden = "SELECT
    %s AS label
    ,SUM(kcf.farmer_baseline) AS farmer_baseline
    ,SUM(kcf.farmer_postline) AS farmer_postline
    ,SUM(garden_baseline) AS garden_baseline
    ,SUM(garden_postline) AS garden_postline
    ,SUM(production_baseline) AS production_baseline
    ,SUM(production_postline) AS production_postline
    ,SUM(ha_baseline) AS ha_baseline
    ,SUM(ha_postline) AS ha_postline
    ,SUM(tree_baseline) AS tree_baseline
    ,SUM(tree_postline) AS tree_postline
    ,SUM(IF(`year`=2010,garden_baseline,0)) AS garden_baseline_2010
    ,SUM(IF(`year`=2010,garden_postline,0)) AS garden_postline_2010
    ,SUM(IF(`year`=2011,garden_baseline,0)) AS garden_baseline_2011
    ,SUM(IF(`year`=2011,garden_postline,0)) AS garden_postline_2011
    ,SUM(IF(`year`=2012,garden_baseline,0)) AS garden_baseline_2012
    ,SUM(IF(`year`=2012,garden_postline,0)) AS garden_postline_2012
    ,SUM(IF(`year`=2013,garden_baseline,0)) AS garden_baseline_2013
    ,SUM(IF(`year`=2013,garden_postline,0)) AS garden_postline_2013
    ,SUM(IF(`year`=2014,garden_baseline,0)) AS garden_baseline_2014
    ,SUM(IF(`year`=2014,garden_postline,0)) AS garden_postline_2014
    ,SUM(IF(`year`=2015,garden_baseline,0)) AS garden_baseline_2015
    ,SUM(IF(`year`=2015,garden_postline,0)) AS garden_postline_2015
    ,SUM(IF(`year`=2016,garden_baseline,0)) AS garden_baseline_2016
    ,SUM(IF(`year`=2016,garden_postline,0)) AS garden_postline_2016
    ,SUM(IF(`year`=2017,garden_baseline,0)) AS garden_baseline_2017
    ,SUM(IF(`year`=2017,garden_postline,0)) AS garden_postline_2017
FROM dash_survey_garden kcf
%s
WHERE 1 = 1
%s
GROUP BY label
        ";
        $this->survey_nutrition = "SELECT
    %s AS label
    ,SUM(nutrition_baseline) AS nutrition_baseline
    ,SUM(nutrition_postline) AS nutrition_postline
    ,SUM(score_sum_baseline) AS score_sum_baseline
    ,SUM(score_count_baseline) AS score_count_baseline
    ,SUM(score_male_sum_baseline) AS score_male_sum_baseline
    ,SUM(score_male_count_baseline) AS score_male_count_baseline
    ,SUM(score_female_sum_baseline) AS score_female_sum_baseline
    ,SUM(score_female_count_baseline) AS score_female_count_baseline
    ,SUM(score_sum_postline) AS score_sum_postline
    ,SUM(score_count_postline) AS score_count_postline
    ,SUM(score_male_sum_postline) AS score_male_sum_postline
    ,SUM(score_male_count_postline) AS score_male_count_postline
    ,SUM(score_female_sum_postline) AS score_female_sum_postline
    ,SUM(score_female_count_postline) AS score_female_count_postline
    ,SUM(score_sum_latest) AS score_sum_latest
    ,SUM(score_count_latest) AS score_count_latest
    ,SUM(score_male_sum_latest) AS score_male_sum_latest
    ,SUM(score_male_count_latest) AS score_male_count_latest
    ,SUM(score_female_sum_latest) AS score_female_sum_latest
    ,SUM(score_female_count_latest) AS score_female_count_latest
    ,SUM(luas_sum_baseline) AS luas_sum_baseline
    ,SUM(luas_count_baseline) AS luas_count_baseline
    ,SUM(luas_sum_postline) AS luas_sum_postline
    ,SUM(luas_count_postline) AS luas_count_postline
    ,SUM(luas_sum_latest) AS luas_sum_latest
    ,SUM(luas_count_latest) AS luas_count_latest
    ,SUM(IF(`year`=2010,nutrition_baseline,0)) AS nutrition_baseline_2010
    ,SUM(IF(`year`=2010,nutrition_postline,0)) AS nutrition_postline_2010
    ,SUM(IF(`year`=2011,nutrition_baseline,0)) AS nutrition_baseline_2011
    ,SUM(IF(`year`=2011,nutrition_postline,0)) AS nutrition_postline_2011
    ,SUM(IF(`year`=2012,nutrition_baseline,0)) AS nutrition_baseline_2012
    ,SUM(IF(`year`=2012,nutrition_postline,0)) AS nutrition_postline_2012
    ,SUM(IF(`year`=2013,nutrition_baseline,0)) AS nutrition_baseline_2013
    ,SUM(IF(`year`=2013,nutrition_postline,0)) AS nutrition_postline_2013
    ,SUM(IF(`year`=2014,nutrition_baseline,0)) AS nutrition_baseline_2014
    ,SUM(IF(`year`=2014,nutrition_postline,0)) AS nutrition_postline_2014
    ,SUM(IF(`year`=2015,nutrition_baseline,0)) AS nutrition_baseline_2015
    ,SUM(IF(`year`=2015,nutrition_postline,0)) AS nutrition_postline_2015
    ,SUM(IF(`year`=2016,nutrition_baseline,0)) AS nutrition_baseline_2016
    ,SUM(IF(`year`=2016,nutrition_postline,0)) AS nutrition_postline_2016
    ,SUM(IF(`year`=2017,nutrition_baseline,0)) AS nutrition_baseline_2017
    ,SUM(IF(`year`=2017,nutrition_postline,0)) AS nutrition_postline_2017
FROM dash_survey_nutrition kcf
%s
WHERE 1 = 1
%s
GROUP BY label";
        $this->survey_ppi = "SELECT
    %s AS label
    ,SUM(`count_baseline`) AS `count_baseline`
    ,SUM(`count_baseline`) AS `count_baseline`
    ,SUM(`National_sum_baseline`) AS `National_sum_baseline`
    ,SUM(`National_count_baseline`) AS `National_count_baseline`
    ,SUM(`1.25_baseline`) AS `1.25_baseline`
    ,SUM(`2.5_baseline`) AS `2.5_baseline`
    ,SUM(`count_postline`) AS `count_postline`
    ,SUM(`National_sum_postline`) AS `National_sum_postline`
    ,SUM(`National_count_postline`) AS `National_count_postline`
    ,SUM(`1.25_postline`) AS `1.25_postline`
    ,SUM(`2.5_postline`) AS `2.5_postline`
FROM dash_survey_ppi kcf
%s
WHERE 1 = 1
%s
GROUP BY label";
        $this->survey_finance = "SELECT 
    %s AS label
    , IFNULL(SUM(gfp_baseline),0) AS gfp_baseline
    , IFNULL(SUM(gfp_postline),0) AS gfp_postline
    , IFNULL(SUM(bank_account_baseline),0) AS bank_account_baseline
    , IFNULL(SUM(bank_account_postline),0) AS bank_account_postline
    , IFNULL(SUM(saving_baseline),0) AS saving_baseline
    , IFNULL(SUM(saving_postline),0) AS saving_postline
FROM
    dash_survey_finance kcf
%s
WHERE 1 = 1
%s
GROUP BY label";
    }

    

    function readDataSurvey($prov = '', $kab = '')
    {
        if ($prov == '') {
            $label = 'Province';
            $LEFT = 'LEFT JOIN ktv_village kv ON kv.villageID = kcf.VillageID
                    LEFT JOIN ktv_subdistrict ksubdis ON kv.SubDistrictID = ksubdis.SubDistrictID
                    LEFT JOIN ktv_district kdis ON ksubdis.DistrictID = kdis.DistrictID
                    LEFT JOIN ktv_province kp ON kdis.ProvinceID = kp.ProvinceID';
            $where = 'AND kcf.VillageID';
            $groupby = 'kp.ProvinceID';
        } elseif ($kab == '') {
            $label = 'District';
            $LEFT = 'LEFT JOIN ktv_village kv ON kv.villageID = kcf.VillageID
                    LEFT JOIN ktv_subdistrict ksubdis ON kv.SubDistrictID = ksubdis.SubDistrictID
                    LEFT JOIN ktv_district kp ON ksubdis.DistrictID = kp.DistrictID';
            $where = 'AND kcf.VillageID and kp.ProvinceID=?';
            $groupby = 'kp.DistrictID';
        } else {
            $label = 'SubDistrict';
            $LEFT = 'LEFT JOIN ktv_subdistrict kp ON kp.SubDistrictID = kcf.SubDistrictID';
            $where = 'AND kcf.VillageID and kcf.DistrictID=?';
            $groupby = 'kp.SubDistrictID';
        }
        if ($kab != '') $prov = $kab;
        $query_garden           = $this->db->query(sprintf($this->survey_garden, $label, $LEFT, $where, $groupby), array($prov));
        $query_nutrition        = $this->db->query(sprintf($this->survey_nutrition, $label, $LEFT, $where, $groupby), array($prov));
        $query_ppi              = $this->db->query(sprintf($this->survey_ppi, $label, $LEFT, $where), array($prov));
        $query_finance          = $this->db->query(sprintf($this->survey_finance, $label, $LEFT, $where), array($prov));

        $results['garden']      = $query_garden->result_array();
        $results['nutrition']   = $query_nutrition->result_array();
        $results['ppi']         = $query_ppi->result_array();
        $results['finance']     = $query_finance->result_array();
        
        return $results;
    }

    function readDataDistrictSurvey($user, $district, $priv = '', $partner = '', $prov = '')
    {
        $where = '';
        $LEFT = '';
        if ($petani == '1') {
            $LEFT .= 'LEFT JOIN (SELECT FarmerID farid,ExternalDate from ktv_certification WHERE ExternalDate > \'0000-00-00\' group by FarmerID) ce on ce.farid=kf.FarmerID';
            $where .= " and farid is not null";
        } elseif ($petani == '2') {
            $LEFT .= 'LEFT JOIN (SELECT FarmerID farid,ExternalDate from ktv_certification WHERE ExternalDate > \'0000-00-00\' group by FarmerID) ce on ce.farid=kf.FarmerID';
            $where .= " and farid is null";
        }
        $where .= ' and substr(VillageID,1,4) in (%s)';
        if (empty($prov)) {
            $label = 'Province';
            $LEFT .= ' LEFT JOIN ktv_province on ProvinceID=substr(VillageID,1,2)';
            $groupby = 'substr(VillageID,1,2)';
        } else {
            $where .= ' and substr(VillageID,1,2) = ' . $prov;
            if ($priv == '') {
                $label = 'District';
                $LEFT .= ' LEFT JOIN ktv_district on DistrictID=substr(VillageID,1,4)';
                $groupby = 'substr(VillageID,1,4)';
            } else {
                $label = 'SubDistrict';
                // $LEFT .= ' LEFT JOIN ktv_subdistrict on SubDistrictID=SubDistrictID';
                $where .= ' and substr(VillageID,1,4)=?';
                $groupby = 'SubDistrictID';
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

        $query_garden           = $this->db->query(sprintf(sprintf($this->survey_garden, $label, $LEFT, $where, $groupby), implode(',', $dist)), array($priv));
        $query_nutrition        = $this->db->query(sprintf(sprintf($this->survey_nutrition, $label, $LEFT, $where, $groupby), implode(',', $dist)), array($priv));
        $query_ppi              = $this->db->query(sprintf(sprintf($this->survey_ppi, $label, $LEFT, $where, $groupby), implode(',', $dist)), array($priv));
        $query_finance          = $this->db->query(sprintf(sprintf($this->survey_finance, $label, $LEFT, $where, $groupby), implode(',', $dist)), array($priv));
        

        $results['garden']      = $query_garden->result_array();
        $results['nutrition']   = $query_nutrition->result_array();
        $results['ppi']         = $query_ppi->result_array();
        $results['finance']     = $query_finance->result_array();
        return $results;
    }

}

/* End of file msurvey.php */
/* Location: ./application/models/msurvey.php */