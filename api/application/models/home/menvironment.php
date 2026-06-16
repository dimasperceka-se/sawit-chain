<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Menvironment extends CI_Model {
    private $sql;

    public function __construct()
    {
        parent::__construct();
        $this->sql['environment'] = "
SELECT 
    %s AS label
    , SUM(TotalTrees) AS TotalTrees
    , SUM(Hectare) AS Hectare
    , SUM(Production) AS Production
    , SUM(Productivity) AS Productivity
    , SUM(Productivity_Trees) AS Productivity_Trees
    , SUM(Kg_Kompos_Tree) AS Kg_Kompos_Tree
    , SUM(TBM_Kompos) AS TBM_Kompos
    , SUM(TM_Kompos) AS TM_Kompos
    , SUM(TR_Kompos) AS TR_Kompos
    , SUM(Trees_Kompos) AS Trees_Kompos
    , SUM(Kg_Kompos) AS Kg_Kompos
    , SUM(CO2_Kompos) AS CO2_Kompos
    , SUM(TBM_Fertilized) AS TBM_Fertilized
    , SUM(TM_Fertilized) AS TM_Fertilized
    , SUM(TR_Fertilized) AS TR_Fertilized
    , SUM(Trees_Fertilized) AS Trees_Fertilized
    , SUM(G_Urea_Tree) AS G_Urea_Tree
    , SUM(G_NPK_Tree) AS G_NPK_Tree
    , SUM(Kg_Urea) AS Kg_Urea
    , SUM(Kg_NPK) AS Kg_NPK
    , SUM(Kg_Fertilizer) AS Kg_Fertilizer
    , SUM(Kg_Fertilizer_Tree) AS Kg_Fertilizer_Tree
    , SUM(CO2_Urea) AS CO2_Urea
    , SUM(CO2_NPK) AS CO2_NPK
    , SUM(CO2_ZA) AS CO2_ZA
    , SUM(CO2_Total) AS CO2_Total
    , SUM(CO2_Hectare) AS CO2_Hectare
    , SUM(tCO2e_tCocoa) AS tCO2e_tCocoa
    , SUM(C_Stock_Trees) AS C_Stock_Trees
    , SUM(C_Stock) AS C_Stock
FROM dash_environment
%s
WHERE 1 = 1
%s
GROUP BY label
HAVING label IS NOT NULL
        ";
        $this->sql['garden'] = "
SELECT 
    %s AS label
    , SUM(TotalHa) AS TotalHa
    , SUM(CacaoTrees) AS CacaoTrees
    , SUM(Nr_Coconut) AS Nr_Coconut
    , SUM(Nr_Areca_Palm) AS Nr_Areca_Palm
    , SUM(Nr_Rubber) AS Nr_Rubber
    , SUM(Nr_Clove) AS Nr_Clove
    , SUM(Nr_Oil_Palm) AS Nr_Oil_Palm
    , SUM(Nr_Sugar_Palm) AS Nr_Sugar_Palm
    , SUM(Nr_Nutmeg) AS Nr_Nutmeg
    , SUM(Nr_Hazelnut) AS Nr_Hazelnut
    , SUM(Nr_Mahagony) AS Nr_Mahagony
    , SUM(Nr_Teak) AS Nr_Teak
    , SUM(Nr_Vitex) AS Nr_Vitex
    , SUM(Nr_Elmerilla) AS Nr_Elmerilla
    , SUM(Nr_Anthocephalus) AS Nr_Anthocephalus
    , SUM(Nr_Jackfruit) AS Nr_Jackfruit
    , SUM(Nr_Banana) AS Nr_Banana
    , SUM(Nr_Rambutan) AS Nr_Rambutan
    , SUM(Nr_Mango) AS Nr_Mango
    , SUM(Nr_Langsat) AS Nr_Langsat
    , SUM(Nr_Durian) AS Nr_Durian
    , SUM(Nr_Avocado) AS Nr_Avocado
    , SUM(Nr_Breadfruit) AS Nr_Breadfruit
    , SUM(Nr_Papaya) AS Nr_Papaya
    , SUM(Nr_Mangosteen) AS Nr_Mangosteen
    , SUM(Nr_Citrus) AS Nr_Citrus
    , SUM(Nr_Gliricidia) AS Nr_Gliricidia
    , SUM(Nr_Leucaena) AS Nr_Leucaena
    , SUM(Nr_Parkia) AS Nr_Parkia
    , SUM(Nr_Archidendron) AS Nr_Archidendron
    , SUM(Nr_Other) AS Nr_Other
    , SUM(Total_Nr_Diversification) AS Total_Nr_Diversification
    , SUM(Check_Total_Nr) AS Check_Total_Nr
    , SUM(Tanaman_Produksi_Selain_Kakao) AS Tanaman_Produksi_Selain_Kakao
    , SUM(Kayu_Keras) AS Kayu_Keras
    , SUM(Buah_buahan) AS Buah_buahan
    , SUM(Leguminosa) AS Leguminosa
    , SUM(Lainnya) AS Lainnya
FROM dash_environment_garden
%s
WHERE 1 = 1
%s
GROUP BY label
HAVING label IS NOT NULL
        ";
        $this->sql['base'] = "
SELECT 
    %s AS label
    , IFNULL(COUNT(Farmers),0) AS farmers
    , IFNULL(COUNT(CO2_Total_Baseline),0) AS Farmers_Baseline
    , IFNULL(COUNT(CO2_Total_Postline),0) AS Farmers_Postline
    , IFNULL(SUM(TotalTrees_Baseline),0) AS TotalTrees_Baseline
    , IFNULL(SUM(TotalTrees_Postline),0) AS TotalTrees_Postline
    , IFNULL(SUM(Hectare_Baseline),0) AS Hectare_Baseline
    , IFNULL(SUM(Hectare_Postline),0) AS Hectare_Postline
    , IFNULL(SUM(Production_Baseline),0) AS Production_Baseline
    , IFNULL(SUM(Production_Postline),0) AS Production_Postline
    , IFNULL(SUM(Productivity_Baseline),0) AS Productivity_Baseline
    , IFNULL(SUM(Productivity_Postline),0) AS Productivity_Postline
    , IFNULL(SUM(Productivity_Trees_Baseline),0) AS Productivity_Trees_Baseline
    , IFNULL(SUM(Productivity_Trees_Postline),0) AS Productivity_Trees_Postline
    , IFNULL(SUM(CO2_Kompos_Baseline),0) AS CO2_Kompos_Baseline
    , IFNULL(SUM(CO2_Kompos_Postline),0) AS CO2_Kompos_Postline
    , IFNULL(SUM(CO2_Urea_Baseline),0) AS CO2_Urea_Baseline
    , IFNULL(SUM(CO2_Urea_Postline),0) AS CO2_Urea_Postline
    , IFNULL(SUM(CO2_NPK_Baseline),0) AS CO2_NPK_Baseline
    , IFNULL(SUM(CO2_NPK_Postline),0) AS CO2_NPK_Postline
    , IFNULL(SUM(CO2_ZA_Baseline),0) AS CO2_ZA_Baseline
    , IFNULL(SUM(CO2_ZA_Postline),0) AS CO2_ZA_Postline
    , IFNULL(SUM(CO2_Total_Baseline),0) AS CO2_Total_Baseline
    , IFNULL(SUM(CO2_Total_Postline),0) AS CO2_Total_Postline
    , IFNULL(SUM(CO2_Total_Baseline/Hectare_Baseline),0) AS CO2_Hectare_Baseline
    , IFNULL(SUM(CO2_Total_Postline/Hectare_Postline),0) AS CO2_Hectare_Postline
    , IFNULL(SUM(CO2_Total_Baseline/Production_Baseline),0) AS CO2_Kg_Baseline
    , IFNULL(SUM(CO2_Total_Postline/Production_Postline),0) AS CO2_Kg_Postline
FROM
    dash_environment_base 
%s
WHERE 1 = 1
%s
GROUP BY label
        ";
    }

    function readDataEnvironment($prov = '', $kab = '')
    {
        if ($prov == '') {
            $label = 'Province';
            // $LEFT = 'LEFT JOIN ktv_province kp ON kp.ProvinceID = substr(VillageID,1,2)';
            $where = '';
        } elseif ($kab == '') {
            $label = 'District';
            // $LEFT = 'LEFT JOIN ktv_district kp ON kp.DistrictID = substr(VillageID,1,4)';
            // $where = 'and substr(VillageID,1,2)=?';
            $where = 'AND DistrictID=?';
        } else {
            $label = 'SubDistrict';
            // $where = 'and substr(VillageID,1,4)=?';
            $where = 'AND SubDistrictID=?';
        }
        if ($kab != '') $prov = $kab;
        $query                      = $this->db->query(sprintf($this->sql['environment'], $label, $LEFT, $where), array($prov));
        $results['environment']     = $query->result_array();
        $query                      = $this->db->query(sprintf($this->sql['garden'], $label, $LEFT, $where), array($prov));
        $results['garden']          = $query->result_array();
        $query                      = $this->db->query(sprintf($this->sql['base'], $label, $LEFT, $where), array($prov));
        $results['base']            = $query->result_array();

        return $results;
    }

    function readDataDistrictEnvironment($user, $district, $priv = '', $partner = '', $prov = '')
    {
        $where = '';
        $LEFT = '';
        $where .= ' AND DistrictID in (%s)';
        if (empty($prov)) {
            $label = 'Province';
            // $LEFT .= ' LEFT JOIN ktv_province on ProvinceID=substr(VillageID,1,2)';
            // $groupby = 'substr(VillageID,1,2)';
        } else {
            $where .= ' AND ProvinceID = ' . $prov;
            if ($priv == '') {
                $label = 'District';
                // $LEFT .= ' LEFT JOIN ktv_district on DistrictID=substr(VillageID,1,4)';
                // $groupby = 'substr(VillageID,1,4)';
            } else {
                $label = 'SubDistrict';
                // $LEFT .= ' LEFT JOIN ktv_subdistrict on SubDistrictID=SubDistrictID';
                // $where .= ' and substr(VillageID,1,4)=?';
                $where .= ' AND DistrictID=?';
                // $groupby = 'SubDistrictID';
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

        $query = $this->db->query(sprintf(sprintf($this->sql['environment'], $label, $LEFT, $where, $groupby), implode(',', $dist)), array($priv));
        $results['environment'] = $query->result_array();
        $query = $this->db->query(sprintf(sprintf($this->sql['garden'], $label, $LEFT, $where, $groupby), implode(',', $dist)), array($priv));
        $results['garden'] = $query->result_array();
        $query = $this->db->query(sprintf(sprintf($this->sql['base'], $label, $LEFT, $where, $groupby), implode(',', $dist)), array($priv));
        $results['base'] = $query->result_array();

        return $results;
    }

}

/* End of file mfinance.php */
/* Location: ./application/models/mfinance.php */