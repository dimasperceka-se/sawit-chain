<?php

class Mcargill extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->sql = "
SELECT
    SUM(CF) AS CF
    , SUM(CL) AS CL
    , SUM(UTZ) AS UTZ
    , SUM(CF_Production) AS CF_Production
    , SUM(CL_Production) AS CL_Production
    , SUM(UTZ_Production) AS UTZ_Production
    , SUM(CF_GardenHaUnCertified) AS CF_GardenHaUnCertified
    , SUM(CL_GardenHaUnCertified) AS CL_GardenHaUnCertified
    , SUM(UTZ_GardenHaUnCertified) AS UTZ_GardenHaUnCertified
    , SUM(nursery) AS nursery
    , SUM(nursery_area) AS nursery_area
    , SUM(nursery_volume) AS nursery_volume
FROM dash_cargill kcf
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
        
        $query = $this->db->query(sprintf($this->sql, $where.$where_cert), array($prov));
        $result = $query->row_array(0);
        return $result;

    }
    function readDataDistrict($user, $district, $priv = '', $partner = '', $prov = '', $cert_holder = 'all') {
        $where = '';
        if ($prov != '') {
            $where .= ' and substr(VillageID,1,2) = ' . $prov;
        }
        if ($priv == '') {
            $where .= ' and substr(VillageID,1,4) in (%s)';
        } else {
            $where .= ' and substr(VillageID,1,4)=?';
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
