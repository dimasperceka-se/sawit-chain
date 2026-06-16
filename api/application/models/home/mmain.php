<?php

class Mmain extends CI_Model {

    function __construct() {
      parent::__construct();
      $this->dash_main = "
         SELECT COUNT(CPGid) cpg,SUM(CocoaFarmers) farmer,SUM(SumAge)/(SUM(CocoaFarmers)-SUM(SumUnage)) usia,
            SUM(FemaleFarmers)/(SUM(FemaleFarmers)+SUM(MaleFarmers)) perempuan,
         	SUM(SumFarm) kebun,SUM(CocoaFarmArea)/SUM(CountFarmerArea) rerata_ha,SUM(CocoaTree) pohon,SUM(CocoaFarmArea) luas,
         	SUM(Production) produksi,SUM(Production)/SUM(CocoaFarmArea) produktifity,SUM(Production)/SUM(TMCocoaTree) produktivitas_pohon,
         	SUM(CertifiedCocoa) petani_sertifikasi,SUM(CertifiedFarmArea) luas_sertifikasi,SUM(CertifiedProduction) produksi_sertifikasi,
         	SUM(GNPParticipants) gnp,SUM(GFPParticipants) gfp,
         	sum(small) small,sum(`medium`) `medium`,sum(`large`) `large`,
	         sum(unprofessional) unprofessional,sum(`progressing`) `progressing`,sum(`professional`) `professional`
         FROM dash_main
         WHERE 1=1 %s";
        $this->main_farmer = "SELECT
    COUNT(CPGid) AS cpg
    ,SUM(farmer) AS farmer
    ,SUM(age_sum)/(SUM(farmer)-SUM(farmer_unage)) AS usia
    ,SUM(female)/(SUM(female)+SUM(male)) AS perempuan
    ,SUM(certified) AS certified
    ,SUM(gnp) AS gnp
    ,SUM(gfp) AS gfp
FROM dash_main_farmer kcf
LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
left join ktv_province kp on kp.ProvinceID = kd.ProvinceID
WHERE 1 = 1 
    %s
        ";
        $this->main_garden = "SELECT
    SUM(garden) AS garden
    ,SUM(GardenHaUncertified)/SUM(garden) AS rerata
    ,SUM(cocoa_tree) AS pohon
    ,SUM(GardenHaUncertified) AS luas
    ,SUM(production) AS produksi
    ,SUM(production)/SUM(GardenHaUncertified) AS produktifitas
    ,SUM(production)/SUM(tm_cocoa_tree) AS produktifitas_pohon
    ,SUM(GardenHaUncertified_certified) AS luas_sertifikasi
    ,SUM(production_certified) AS produksi_sertifikasi
    ,SUM(small) small
    ,SUM(`medium`) `medium`
    ,SUM(`large`) `large`
    ,SUM(unprofessional) unprofessional
    ,SUM(`progressing`) `progressing`,SUM(`professional`) `professional`
FROM dash_main_garden kcf
LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
left join ktv_province kp on kp.ProvinceID = kd.ProvinceID
WHERE 1 = 1 
    %s
        ";
    }

    function readData($prov = '', $kab = '') {
        if ($prov == '') $where = '';
        elseif ($kab == '') $where = 'and kp.ProvinceID=?';
        else $where = 'and kd.DistrictID=?';
        if ($kab != '') $prov = $kab;
        
        $query_farmer = $this->db->query(sprintf($this->main_farmer, $where), array($prov));
        $query_garden = $this->db->query(sprintf($this->main_garden, $where), array($prov));
        $result = $query_farmer->row_array(0);
        $result += $query_garden->row_array(0);
        return $result;

    }
    function readDataDistrict($user, $district, $priv = '', $partner = '', $prov = '') {
        $where = '';
        if ($prov != '') {
            $where .= ' and kp.ProvinceID = ' . $prov;
            $group = 'GROUP BY kd.DistrictID';
        }
        if ($priv == '') {
            $where .= ' and kd.DistrictID in (%s)';
            $group = 'GROUP BY ksd.SubDistrictID';
        } else {
            $where .= ' and kd.DistrictID=?';
            $group = 'GROUP BY ksd.SubDistrictID';
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
        $query_farmer = $this->db->query(sprintf(sprintf($this->main_farmer, $where), implode(',', $dist)), array($priv));
        $query_garden = $this->db->query(sprintf(sprintf($this->main_garden, $where), implode(',', $dist)), array($priv));
        $result = $query_farmer->row_array(0);
        $result += $query_garden->row_array(0);
        return $result;
    }

}

?>
