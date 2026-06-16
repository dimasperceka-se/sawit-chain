<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class mcertification extends CI_Model
{

    public $sql;

    public function __construct()
    {
        parent::__construct();
        
        $this->sql['certification'] = "
SELECT
    %s label,
   COUNT(DISTINCT IF(kcc.Certification=1,kcf.FarmerID,NULL)) UTZ_farmer,
   COUNT(DISTINCT IF(kcc.Certification=2,kcf.FarmerID,NULL)) Rainforest_farmer,
   COUNT(DISTINCT IF(kcc.Certification=3,kcf.FarmerID,NULL)) Fair_farmer,
   COUNT(DISTINCT IF(kcc.Certification=4,kcf.FarmerID,NULL)) Organic_farmer,
   COUNT(IF(kcc.Certification=1,kcc.FarmerID,NULL)) UTZ_garden,
   COUNT(IF(kcc.Certification=2,kcc.FarmerID,NULL)) Rainforest_garden,
   COUNT(IF(kcc.Certification=3,kcc.FarmerID,NULL)) Fair_garden,
   COUNT(IF(kcc.Certification=4,kcc.FarmerID,NULL)) Organic_garden,
   COUNT(DISTINCT kcf.FarmerID) farmer,
   COUNT(kcc.FarmerID) garden,
   COUNT(DISTINCT IF(kcf.Gender=1,kcf.FarmerID,NULL)) male,
   COUNT(DISTINCT IF(kcf.Gender=2,kcf.FarmerID,NULL)) female,
   SUM(IF(kcc.CertificationHolderJenis='Pedagang',1,0)) trader,
   SUM(IF(kcc.CertificationHolderJenis='Organisasi Petani',1,0)) koperasi,
   SUM(IF(kcc.CertificationHolderJenis='Gudang',1,0)) warehouse
FROM (
    SELECT FarmerID, GardenNr, SurveyNr, Certification, CertificationHolderJenis FROM ktv_certification WHERE ExternalDate>'0000-00-00' AND !('%s' > CertificationEnd OR '%s' < CertificationStart) GROUP BY FarmerID, GardenNr
) kcc
JOIN ktv_farmer_view kcf ON kcf.FarmerID = kcc.FarmerID AND kcf.StatusCode = 'active' AND kcf.isTrained = 1
-- JOIN ktv_farmer_garden kcfg ON kcfg.FarmerID = kcc.FarmerID AND kcfg.GardenNr = kcc.GardenNr AND kcfg.SurveyNr = kcc.SurveyNr
%s
WHERE
    1 = 1
    %s
GROUP BY label";
        $this->sql['certification2'] = "
SELECT
    %s label
    #,sum(IF(kcc.Certification=1,1,0)) UTZ
    #,sum(IF(kcc.Certification=2,1,0)) Rainforest
    #,sum(IF(kcc.Certification=3,1,0)) Fair
    #,sum(IF(kcc.Certification=4,1,0)) Organic
    #,count(DISTINCT kcf.FarmerID) farmer
    ,count(kcfg.FarmerID) garden
    #,sum(IF(kcf.Gender=1,1,0)) male
    #,sum(IF(kcf.Gender=2,1,0)) female
    ,sum(ha) ha
    ,sum(production) as production
    ,sum(tree) AS tree
    #,sum(IF(kcc.CertificationHolderJenis='Pedagang',1,0)) trader
    #,sum(IF(kcc.CertificationHolderJenis='Organisasi Petani',1,0)) koperasi
    #,sum(IF(kcc.CertificationHolderJenis='Gudang',1,0)) warehouse
FROM (
    SELECT FarmerID, GardenNr, SurveyNr, Certification, CertificationHolderJenis FROM ktv_certification WHERE ExternalDate>'0000-00-00' AND !('%s' > CertificationEnd OR '%s' < CertificationStart)
) kcc
JOIN ktv_farmer_view kcf ON kcf.FarmerID = kcc.FarmerID AND kcf.StatusCode = 'active' AND kcf.isTrained = 1
JOIN (SELECT 
    kcfg.FarmerID
    ,kcfg.GardenNr
    ,kcfg.SurveyNr
    ,sum(IFNULL(GardenHaUnCertified,0)) ha
    ,sum(IF(Production > 0, Production, ProductionCalc)) as production
    -- ,sum((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+(IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+(IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) as production
    ,sum(PohonTM) AS tree
FROM ktv_farmer_garden_view kcfg
JOIN (SELECT FarmerID, GardenNr, MAX(SurveyNr) AS SurveyNr FROM ktv_farmer_garden GROUP BY FarmerID, GardenNr) z ON kcfg.FarmerID = z.FarmerID AND kcfg.GardenNr = z.GardenNr AND kcfg.SurveyNr = z.SurveyNr
WHERE 1 = 1
    AND kcfg.GardenHaUnCertified > 0
GROUP BY kcfg.FarmerID,kcfg.GardenNr,kcfg.SurveyNr
) kcfg ON kcfg.FarmerID = kcc.FarmerID AND kcfg.GardenNr = kcc.GardenNr -- AND kcfg.SurveyNr = kcc.SurveyNr
%s
WHERE
    1 = 1
    %s
GROUP BY label";
    }

    public function readDataCertification($prov = '', $kab = '', $startdate = '', $enddate = '')
    {
        if ($startdate == '') {
            $startdate = date('Y-m-d');
        }
        if ($enddate == '') {
            $enddate = date('Y-m-d');
        }
        $where = '';
        if ($prov == '') {
            $label = 'Province';
            $JOIN = ' LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
                    LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
                    LEFT JOIN ktv_province kp ON kp.ProvinceID = kd.ProvinceID';
        } elseif ($kab == '') {
            $label = 'District';
            $JOIN = ' LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
                    LEFT JOIN ktv_district kp ON kp.DistrictID = ksd.DistrictID';
            $where .= ' and kp.ProvinceID=?';
        } else {
            $label = 'SubDistrict';
            $JOIN = ' LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
                    LEFT JOIN ktv_subdistrict kp on kp.SubDistrictID=kv.SubDistrictID';
            $where .= ' and kp.DistrictID=?';
        }
        if ($kab != '') {
            $prov = $kab;
        }
        $query         = $this->db->query(sprintf($this->sql['certification'], $label, $startdate, $enddate, $JOIN, $where), array($prov));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        //$results['data'] = $query->result_array();
        //return $results;
        $i = 0;
        foreach ($query->result() as $row) {
            $results['data'][$i]['label']               = $row->label;
            $results['data'][$i]['UTZ_farmer']          = $row->UTZ_farmer;
            $results['data'][$i]['Rainforest_farmer']   = $row->Rainforest_farmer;
            $results['data'][$i]['Fair_farmer']         = $row->Fair_farmer;
            $results['data'][$i]['Organic_farmer']      = $row->Organic_farmer;
            $results['data'][$i]['UTZ_garden']          = $row->UTZ_garden;
            $results['data'][$i]['Rainforest_garden']   = $row->Rainforest_garden;
            $results['data'][$i]['Fair_garden']         = $row->Fair_garden;
            $results['data'][$i]['Organic_garden']      = $row->Organic_garden;
            $results['data'][$i]['farmer']              = $row->farmer;
            $results['data'][$i]['garden']              = $row->garden;
            $results['data'][$i]['male']                = $row->male;
            $results['data'][$i]['female']              = $row->female;
            $results['data'][$i]['trader']              = $row->trader;
            $results['data'][$i]['koperasi']            = $row->koperasi;
            $results['data'][$i]['warehouse']           = $row->warehouse;
            $i++;
        }
        $query2         = $this->db->query(sprintf($this->sql['certification2'], $label, $startdate, $enddate, $JOIN, $where), array($prov));
        $j = 0;
        foreach ($query2->result() as $row2) {
            // $results['data'][$j]['garden']   = $row2->garden;
            $results['data'][$j]['ha']          = $row2->ha;
            $results['data'][$j]['production']  = $row2->production;
            $results['data'][$j]['tree']        = $row2->tree;
            $j++;
        }
        //echo "<pre>".print_r($results['data'],1);
        return $results;
    }

    public function readDataDistrictCertification($user, $district, $priv = '', $partner = '', $prov = '', $startdate = '', $enddate = '')
    {
        if ($startdate == '') {
            $startdate = date('Y-01-01');
        }
        if ($enddate == '') {
            $enddate = date('Y-m-d');
        }
        $where = '';
        $JOIN = '';

        $where .= ' and kd.DistrictID in (%s)';
        if (empty($prov)) {
            $label = 'Province';
            $JOIN .= ' LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
                    LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
                    LEFT JOIN ktv_province kp ON kp.ProvinceID = kd.ProvinceID';
            $groupby = 'kd.ProvinceID';
        } else {
            $where .= ' and kd.ProvinceID = ' . $prov;
            if ($priv == '') {
                $label = 'District';
                $JOIN .= ' LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
                    LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID';
                $groupby = 'kd.DistrictID';
            } else {
                $label = 'SubDistrict';
                $LEFT .= ' LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
                        LEFT JOIN ktv_subdistrict ksd on ksd.SubDistrictID=kv.SubDistrictID
                        LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID';
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
            $where .= " AND kcf.`CPGid` IN (SELECT CPGid FROM `ktv_cpg_partner` WHERE `PartnerID` = {$_SESSION['PartnerID']})";
        }

        $query = $this->db->query(sprintf(sprintf($this->sql['certification'], $label, $startdate, $enddate, $JOIN, $where), implode(',', $dist)), array($priv));
        //$results['data'] = $query->result_array();
        //return $results;
        $i = 0;
        foreach ($query->result() as $row) {
            $results['data'][$i]['label']               = $row->label;
            $results['data'][$i]['UTZ_farmer']          = $row->UTZ_farmer;
            $results['data'][$i]['Rainforest_farmer']   = $row->Rainforest_farmer;
            $results['data'][$i]['Fair_farmer']         = $row->Fair_farmer;
            $results['data'][$i]['Organic_farmer']      = $row->Organic_farmer;
            $results['data'][$i]['UTZ_garden']          = $row->UTZ_garden;
            $results['data'][$i]['Rainforest_garden']   = $row->Rainforest_garden;
            $results['data'][$i]['Fair_garden']         = $row->Fair_garden;
            $results['data'][$i]['Organic_garden']      = $row->Organic_garden;
            $results['data'][$i]['farmer']              = $row->farmer;
            $results['data'][$i]['male']                = $row->male;
            $results['data'][$i]['female']              = $row->female;
            $results['data'][$i]['trader']              = $row->trader;
            $results['data'][$i]['koperasi']            = $row->koperasi;
            $results['data'][$i]['warehouse']           = $row->warehouse;
            $i++;
        }
        $query2 = $this->db->query(sprintf(sprintf($this->sql['certification2'], $label, $startdate, $enddate, $JOIN, $where), implode(',', $dist)), array($priv));
        $j = 0;
        foreach ($query2->result() as $row2) {
            $results['data'][$j]['garden']      = $row2->garden;
            $results['data'][$j]['ha']          = $row2->ha;
            $results['data'][$j]['production']  = $row2->production;
            $results['data'][$j]['tree']        = $row2->tree;
            $j++;
        }
        return $results;
    }
}

/* End of file mcertification.php */
/* Location: ./application/models/mcertification.php */
