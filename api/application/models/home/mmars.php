<?php

class Mmars extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->main_farmer = "
SELECT
    COUNT(CPGid) AS cpg
    ,SUM(farmer) AS farmer
    ,SUM(age_sum)/(SUM(farmer)-SUM(farmer_unage)) AS usia
    ,SUM(female)/(SUM(female)+SUM(male)) AS perempuan
    ,SUM(certified) AS certified
    ,SUM(gnp) AS gnp
    ,SUM(gfp) AS gfp
FROM dash_mars_farmer kcf
WHERE 1 = 1 
    %s
        ";
        $this->main_volume = "
SELECT
    SUM(volume) AS volume
FROM mars_actual_volume
WHERE 1 = 1 
    %s
        ";
        $this->main_quality = "
SELECT
    SUM(quality) AS quality
FROM mars_actual_quality
WHERE 1 = 1 
    %s
        ";
        $this->main_garden = "
SELECT
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
FROM dash_mars_garden kcf
WHERE 1 = 1 
    %s
        ";
    }

    function readData($prov = '', $kab = '', $cert_holder = 'all') {
        if ($prov == '') $where = '';
        elseif ($kab == '') $where = 'and substr(VillageID,1,2)=?';
        else $where = 'and substr(VillageID,1,4)=?';
        if ($kab != '') $prov = $kab;

        $where_cert = '';
        if ($cert_holder != 'all') {
            $where_cert = " AND certification = '{$cert_holder}'";
        }
        
        $query_farmer = $this->db->query(sprintf($this->main_farmer, $where.$where_cert), array($prov));
        $query_garden = $this->db->query(sprintf($this->main_garden, $where.$where_cert), array($prov));
        $query_volume = $this->db->query(sprintf($this->main_volume, $where.$where_cert), array($prov));
        $query_quality = $this->db->query(sprintf($this->main_quality, $where), array($prov));
        $result = $query_farmer->row_array(0);
        $result += $query_garden->row_array(0);
        $result += $query_volume->row_array(0);
        $result += $query_quality->row_array(0);
        return $result;

    }
    function readDataDistrict($user, $district, $priv = '', $partner = '', $prov = '', $cert_holder = 'all') {
        $where = '';
        if ($prov != '') {
            $where .= ' and substr(VillageID,1,2) = ' . $prov;
            $group = 'GROUP BY substr(VillageID,1,4)';
        }
        if ($priv == '') {
            $where .= ' and substr(VillageID,1,4) in (%s)';
            $group = 'GROUP BY SubDistrictID';
        } else {
            $where .= ' and substr(VillageID,1,4)=?';
            $group = 'GROUP BY SubDistrictID';
        }
        $where_cert = '';
        if ($cert_holder != 'all') {
            $where_cert = " AND certification = '{$cert_holder}'";
        }
        $dist = array();
        // if ($user['isProgramStaff'] == 1) {
        //     $dist[] = $user['accessStaff'];
        // } else {
        //     $dist[] = $user['districtPartner'];
        // }
        $dist[] = $user['district_access'];
        // if ($user['isPrivateStaff'] AND $user['FlagAccess']) {
        // if ($_SESSION['FlagAccess']) {
        //     $where .= " AND kcf.`CPGid` IN (SELECT CPGid FROM `ktv_cpg_partner` WHERE `PartnerID` = {$_SESSION['PartnerID']})";
        // }
        $query_farmer = $this->db->query(sprintf(sprintf($this->main_farmer, $where.$where_cert), implode(',', $dist)), array($priv));
        $query_garden = $this->db->query(sprintf(sprintf($this->main_garden, $where.$where_cert), implode(',', $dist)), array($priv));
        $query_volume = $this->db->query(sprintf(sprintf($this->main_volume, $where.$where_cert), implode(',', $dist)), array($priv));
        $query_quality = $this->db->query(sprintf(sprintf($this->main_quality, $where), implode(',', $dist)), array($priv));
        $result = $query_farmer->row_array(0);
        $result += $query_garden->row_array(0);
        $result += $query_volume->row_array(0);
        $result += $query_quality->row_array(0);
        return $result;
    }

}

?>
