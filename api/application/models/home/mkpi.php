<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mkpi extends CI_Model {

    public $sql;

    public function __construct()
    {
        parent::__construct();
        $this->sql['kpi'] = "
SELECT
    %s AS label
    , IFNULL(SUM(k.farmer),0) AS farmer
    , IFNULL(SUM(k.farmer_male),0) AS farmer_male
    , IFNULL(SUM(k.farmer_female),0) AS farmer_female
    , IFNULL(SUM(k.farmer_certified),0) AS farmer_certified
    , IFNULL(SUM(kt.farmer_certified),0) AS farmer_certified_target
    , IFNULL(SUM(k.farmer_certified_male),0) AS farmer_certified_male
    , IFNULL(SUM(kt.farmer_certified_male),0) AS farmer_certified_male_target
    , IFNULL(SUM(k.farmer_certified_female),0) AS farmer_certified_female
    , IFNULL(SUM(kt.farmer_certified_female),0) AS farmer_certified_female_target
    , IFNULL(SUM(k.gap_basic),0) AS gap_basic
    , IFNULL(SUM(kt.gap_basic),0) AS gap_basic_target
    , IFNULL(SUM(k.gap_basic_male),0) AS gap_basic_male
    , IFNULL(SUM(kt.gap_basic_male),0) AS gap_basic_male_target
    , IFNULL(SUM(k.gap_basic_female),0) AS gap_basic_female
    , IFNULL(SUM(kt.gap_basic_female),0) AS gap_basic_female_target
    , IFNULL(SUM(k.gap_adv),0) AS gap_adv
    , IFNULL(SUM(kt.gap_adv),0) AS gap_adv_target
    , IFNULL(SUM(k.gap_adv_male),0) AS gap_adv_male
    , IFNULL(SUM(kt.gap_adv_male),0) AS gap_adv_male_target
    , IFNULL(SUM(k.gap_adv_female),0) AS gap_adv_female
    , IFNULL(SUM(kt.gap_adv_female),0) AS gap_adv_female_target
    , IFNULL(SUM(k.gnp),0) AS gnp
    , IFNULL(SUM(kt.gnp),0) AS gnp_target
    , IFNULL(SUM(k.gnp_male),0) AS gnp_male
    , IFNULL(SUM(kt.gnp_male),0) AS gnp_male_target
    , IFNULL(SUM(k.gnp_female),0) AS gnp_female
    , IFNULL(SUM(kt.gnp_female),0) AS gnp_female_target
    , IFNULL(SUM(k.gfp),0) AS gfp
    , IFNULL(SUM(kt.gfp),0) AS gfp_target
    , IFNULL(SUM(k.gfp_male),0) AS gfp_male
    , IFNULL(SUM(kt.gfp_male),0) AS gfp_male_target
    , IFNULL(SUM(k.gfp_female),0) AS gfp_female
    , IFNULL(SUM(kt.gfp_female),0) AS gfp_female_target
    , IFNULL(SUM(k.gep),0) AS gep
    , IFNULL(SUM(kt.gep),0) AS gep_target
    , IFNULL(SUM(k.gep_male),0) AS gep_male
    , IFNULL(SUM(kt.gep_male),0) AS gep_male_target
    , IFNULL(SUM(k.gep_female),0) AS gep_female
    , IFNULL(SUM(kt.gep_female),0) AS gep_female_target
    , IFNULL(SUM(k.gsp),0) AS gsp
    , IFNULL(SUM(kt.gsp),0) AS gsp_target
    , IFNULL(SUM(k.gsp_male),0) AS gsp_male
    , IFNULL(SUM(kt.gsp_male),0) AS gsp_male_target
    , IFNULL(SUM(k.gsp_female),0) AS gsp_female
    , IFNULL(SUM(kt.gsp_female),0) AS gsp_female_target
    , IFNULL(SUM(k.gbp),0) AS gbp
    , IFNULL(SUM(kt.gbp),0) AS gbp_target
    , IFNULL(SUM(k.gbp_male),0) AS gbp_male
    , IFNULL(SUM(kt.gbp_male),0) AS gbp_male_target
    , IFNULL(SUM(k.gbp_female),0) AS gbp_female
    , IFNULL(SUM(kt.gbp_female),0) AS gbp_female_target
    , IFNULL(SUM(k.cpg),0) AS cpg
    , IFNULL(SUM(kt.cpg),0) AS cpg_target
FROM ktv_kpi_target kt
JOIN ktv_district d ON d.DistrictID = kt.DistrictID
JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
LEFT JOIN (
SELECT
    d.DistrictID
    , IFNULL(SUM(k.gap_basic),0) AS gap_basic
    , IFNULL(SUM(k.gap_basic_male),0) AS gap_basic_male
    , IFNULL(SUM(k.gap_basic_female),0) AS gap_basic_female
    , IFNULL(SUM(k.gap_adv),0) AS gap_adv
    , IFNULL(SUM(k.gap_adv_male),0) AS gap_adv_male
    , IFNULL(SUM(k.gap_adv_female),0) AS gap_adv_female
    , IFNULL(SUM(k.gnp),0) AS gnp
    , IFNULL(SUM(k.gnp_male),0) AS gnp_male
    , IFNULL(SUM(k.gnp_female),0) AS gnp_female
    , IFNULL(SUM(k.gfp),0) AS gfp
    , IFNULL(SUM(k.gfp_male),0) AS gfp_male
    , IFNULL(SUM(k.gfp_female),0) AS gfp_female
    , IFNULL(SUM(k.gep),0) AS gep
    , IFNULL(SUM(k.gep_male),0) AS gep_male
    , IFNULL(SUM(k.gep_female),0) AS gep_female
    , IFNULL(SUM(k.gsp),0) AS gsp
    , IFNULL(SUM(k.gsp_male),0) AS gsp_male
    , IFNULL(SUM(k.gsp_female),0) AS gsp_female
    , IFNULL(SUM(k.gbp),0) AS gbp
    , IFNULL(SUM(k.gbp_male),0) AS gbp_male
    , IFNULL(SUM(k.gbp_female),0) AS gbp_female
    , IFNULL(SUM(k.cpg),0) AS cpg
    , IFNULL(SUM(k.farmer),0) AS farmer
    , IFNULL(SUM(k.farmer_male),0) AS farmer_male
    , IFNULL(SUM(k.farmer_female),0) AS farmer_female
    , IFNULL(SUM(k.farmer_certified),0) AS farmer_certified
    , IFNULL(SUM(k.farmer_certified_male),0) AS farmer_certified_male
    , IFNULL(SUM(k.farmer_certified_female),0) AS farmer_certified_female
FROM dash_kpi k
JOIN ktv_village v ON k.VillageID = v.VillageID
JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
JOIN ktv_district d ON d.DistrictID = sd.DistrictID
WHERE 1 = 1
    --where_cpg--
GROUP BY DistrictID
) k ON kt.DistrictID = k.DistrictID
%s
WHERE 1 = 1 
    %s
GROUP BY label
HAVING label IS NOT NULL
        ";
        $this->sql['nursery_area'] = "
SELECT
    %s AS label
    , IFNULL(SUM(k.nursery_area),0) AS nursery_area
    , IFNULL(SUM(kt.nursery_area),0) AS nursery_area_target
    , IFNULL(SUM(n.nutrition_area),0) AS nutrition_area
    , IFNULL(SUM(kt.nutrition_area),0) AS nutrition_area_target
FROM ktv_kpi_target kt
JOIN ktv_district d ON d.DistrictID = kt.DistrictID
JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
LEFT JOIN (
    SELECT
        d.DistrictID
        , IFNULL(SUM(n.area),0) AS nursery_area
    FROM (
        SELECT
            CASE n.ObjType
               WHEN 'cpg' THEN cp.VillageID
               WHEN 'farmer' THEN f.VillageID
               WHEN 'trader' THEN t.VillageID
               WHEN 'koperasi' THEN co.VillageID
            END AS VillageID
            , SUM(n.Panjang*n.Lebar) AS `area`
        FROM ktv_nursery n
        LEFT JOIN ktv_cpg cp ON cp.CPGid = n.ObjID AND n.ObjType = 'cpg'
        LEFT JOIN ktv_farmer f ON f.FarmerID = n.ObjID AND n.ObjType = 'farmer'
        LEFT JOIN ktv_traders t ON t.TraderID = n.ObjID AND n.ObjType = 'trader'
        LEFT JOIN ktv_cooperatives co ON co.CoopID = n.ObjID AND n.ObjType = 'koperasi'
        GROUP BY VillageID
    ) n
    JOIN ktv_village v ON n.VillageID = v.VillageID
    JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
    JOIN ktv_district d ON d.DistrictID = sd.DistrictID
    GROUP BY d.DistrictID
) k ON kt.DistrictID = k.DistrictID
LEFT JOIN (
    SELECT
        d.DistrictID
        , SUM(IF(KebunPanjang * KebunLebar <= 100,KebunPanjang * KebunLebar,IF(KebunPanjang * KebunLebar IS NULL,0,100))) +
        SUM(IF(ComKebunPanjang * ComKebunLebar <= 10000,ComKebunPanjang * ComKebunLebar,IF(ComKebunPanjang * ComKebunLebar IS NULL,0,10000))) AS nutrition_area
    FROM ktv_nutrition n
    JOIN (
        SELECT
            n.FarmerID, MAX(n.SurveyNr) AS SurveyNr
        FROM ktv_nutrition n
        GROUP BY FarmerID
    ) z ON z.FarmerID = n.FarmerID AND z.SurveyNr = n.SurveyNr
    JOIN ktv_farmer_view kcf ON n.FarmerID = kcf.FarmerID AND kcf.StatusCode = 'active'
    JOIN ktv_village v ON kcf.VillageID = v.VillageID
    JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
    JOIN ktv_district d ON d.DistrictID = sd.DistrictID
    WHERE
        n.FarmerID
        AND kcf.VillageID > 0
    GROUP BY d.DistrictID
) n ON kt.DistrictID = n.DistrictID
%s
WHERE 1 = 1
    %s
GROUP BY label
HAVING label IS NOT NULL
";
        $this->sql['training_master'] = "SELECT
    %s AS label
    , IFNULL(SUM(t.all),0) AS `all`
    , IFNULL(SUM(kt.master),0) AS all_target
    , IFNULL(SUM(t.cst),0) AS cst
    , IFNULL(SUM(kt.cst),0) AS cst_target
    , IFNULL(SUM(t.cst_male),0) AS cst_male
    , IFNULL(SUM(kt.cst_male),0) AS cst_male_target
    , IFNULL(SUM(t.cst_female),0) AS cst_female
    , IFNULL(SUM(kt.cst_female),0) AS cst_female_target
FROM ktv_kpi_target kt
JOIN ktv_district d ON d.DistrictID = kt.DistrictID
JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
LEFT JOIN (
SELECT
    VillageID
    , SUM(m.all) AS `all`
    , SUM(m.cst) AS cst
    , SUM(m.cst_male) AS cst_male
    , SUM(m.cst_female) AS cst_female
FROM dash_training_master m
GROUP BY m.VillageID
) t ON kt.DistrictID = t.VillageID
%s
WHERE 1 = 1
%s
GROUP BY label
HAVING label IS NOT NULL";
    }

    function readData($prov = '', $kab = '')
    {
        $where = "";
        $LEFT = '';
        if ($prov == '') {
            $label = 'Province';
            // $LEFT = 'JOIN ktv_province kp ON kp.ProvinceID = substr(VillageID,1,2)';
            // $where .= 'AND VillageID';
        } elseif ($kab == '') {
            $label = 'd.District';
            // $LEFT = 'JOIN ktv_district kp ON kp.DistrictID = substr(VillageID,1,4)';
            $where .= 'AND d.ProvinceID=?';
        } else {
            $label = 'd.District';
            // $LEFT = 'JOIN ktv_district kp ON kp.DistrictID = substr(VillageID,1,4)';
            $where .= 'AND d.DistrictID=?';
        }
        if ($kab != '') $prov = $kab;

        $this->sql['kpi'] = str_replace('--where_cpg--', '', $this->sql['kpi']);
        $query              = $this->db->query(sprintf($this->sql['kpi'], $label, $LEFT, $where), array($prov));
        $results['data']    = $query->result_array();
        $query                      = $this->db->query(sprintf($this->sql['nursery_area'], $label, $LEFT, $where), array($prov));
        $results['area']    = $query->result_array();
        $query                      = $this->db->query(sprintf($this->sql['training_master'], $label, $LEFT, $where), array($prov));
        $results['training_master']    = $query->result_array();
        
        return $results;
    }

    function readDistrictData($user, $district, $priv = '', $partner = '', $prov = '')
    {
        $where = "";
        $where_cpg = "";
        $LEFT = '';
        // $where .= ' AND d.DistrictID IN (%s)';
        if (empty($prov)) {
            $label = 'Province';
        } else {
            $where .= ' and d.ProvinceID = ' . $prov;
            if ($priv == '') {
                $label = 'd.District';
            } else {
                $label = 'd.District';
                $where .= ' AND d.DistrictID=?';
            }
        }

        $dist = array();
        // $dist[] = $user['district_access'];
        // if ($_SESSION['FlagAccess']) {
        //     $where_cpg .= " AND `CPGid` IN (SELECT CPGid FROM `ktv_cpg_partner` WHERE `PartnerID` = {$_SESSION['PartnerID']})";
        // }
        if ($user['accessStaff']) {
            $where .= " AND d.DistrictID IN ({$user['accessStaff']})";
        }
        $this->sql['kpi'] = str_replace('--where_cpg--', $where_cpg, $this->sql['kpi']);
        $query              = $this->db->query(sprintf(sprintf($this->sql['kpi'], $label, $LEFT, $where), implode(',', $dist)), array($priv));
        $results['data']    = $query->result_array();
        $query                      = $this->db->query(sprintf(sprintf($this->sql['nursery_area'], $label, $LEFT, $where), implode(',', $dist)), array($priv));
        $results['area']    = $query->result_array();
        $query                      = $this->db->query(sprintf(sprintf($this->sql['training_master'], $label, $LEFT, $where), implode(',', $dist)), array($priv));
        $results['training_master']    = $query->result_array();

        return $results;
    }

}

/* End of file mcocoaprice.php */
/* Location: ./application/models/mcocoaprice.php */