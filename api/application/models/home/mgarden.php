<?php

class Mgarden extends CI_Model {

    function __construct() {
      parent::__construct();
     $this->garden_all = "
         SELECT 
            SUM(Garden) garden,SUM(FarmArea) area,SUM(Production)/1000 produksi,
               SUM(`Medium`+Large)/SUM(Marginal+Micro+Small+`Medium`+Large)*100 kebun1,
         	SUM(FarmArea)/SUM(Garden) rerata,SUM(CocoaTree)tanaman_cacao, SUM(OtherTree)tanaman_lain,
               SUM(RehabTree) tanaman_rusak,
         	SUM(CocoaTree)/SUM(FarmArea) rerata_hektar,SUM(Production)/SUM(FarmArea) produktifitas,
               SUM(Production)/SUM(PohonTM) produktifitas_pohon,SUM(AgeTree)/SUM(CountAgeTree) rerata_umur,
         	
         	-- SUM(Yield500)Yield500,SUM(Yield1000)Yield1000,SUM(Yield2000)Yield2000,SUM(YieldAbove2000)YieldAbove2000,
            SUM(`<=350Farmer`) '<=350Farmer',
            SUM(`>350And<=500Farmer`) '>350And<=500Farmer',
            SUM(`>500And<=750Farmer`) '>500And<=750Farmer',
            SUM(`>750And<=1000Farmer`) '>750And<=1000Farmer',
            SUM(`>1000And<=1500Farmer`) '>1000And<=1500Farmer',
            -- SUM(`>1500And<=2000Farmer`) '>1500And<=2000Farmer',
            SUM(`>1500Farmer`) '>1500Farmer',
         	SUM(Marginal)Marginal,SUM(Micro)Micro,SUM(Small)Small,SUM(`Medium`)`Medium`,SUM(Large)Large,
            SUM(Unprofessional)Unprofessional,SUM(Progressing)Progressing,SUM(Professional)Professional,
         	SUM(PohonTBM)PohonTBM,SUM(PohonTM)PohonTM,SUM(RehabTree)RehabTree,SUM(PohonLain)PohonLain,
         	SUM(`Owner`)`Owner`,SUM(CropShare)CropShare,SUM(Rent)Rent,SUM(Other)Other,
         	SUM(NoLandCertificate)NoLandCertificate,SUM(NotarialDeepBpn)NotarialDeepBpn,SUM(SkktCamat)SkktCamat,
               SUM(VillageLurah)VillageLurah,
         	SUM(FarmerHimHerself)FarmerHimHerself,SUM(FamilyMember)FamilyMember,SUM(OtherPerson)OtherPerson,
               SUM(DoNotKnow)DoNotKnow	
         FROM dash_farm kcf
         %s
         WHERE 1=1 %s";
     $this->garden_group = "
         SELECT %s label,
            SUM(Garden) kebun,SUM(FarmArea) luas_kebun,SUM(Production) produksi,SUM(Production)/SUM(FarmArea) produktifitas,
         	SUM(Production)/SUM(PohonTM) produktifitas_menghasilkan,SUM(FarmArea)/SUM(FarmerHaveArea) rerata_ukuran,
         	SUM(CocoaTree)/SUM(FarmArea) rerata_pohon,SUM(AgeTree)/SUM(CountAgeTree) rerata_umur
         FROM dash_farm kcf
         %s
         WHERE Garden>0 %s
         GROUP BY %s
         HAVING label IS NOT NULL
         ORDER BY label";
    }

    function readDataGarden($prov = '', $kab = '', $petani = '', $tahun = '', $survey = ''){
        $where = '';
        $LEFT = '';
        if ($petani == '1') {
            $tahun = ! empty($tahun) ? $tahun : date('Y');
            $where .= " AND Certified='$tahun'";
        } else $where .= " AND Certified is null";
        switch ($survey) {
          case '0':
            $where .= " AND Survey = 'baseline'";
            break;
          case '1':
            $where .= " AND Survey = 'postline'";
            break;
          case '2':
            $where .= " AND Survey = 'latest'";
            break;
        }
        if ($prov == '') {
            $label = 'kp.Province';
            $LEFT .= ' LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                    LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
                    LEFT JOIN ktv_province kp ON kp.`ProvinceID` = kd.`ProvinceID`';
            $groupby = 'kp.ProvinceID';
        } elseif ($kab == '') {
            $label = 'kd.District';
            $LEFT .= ' LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                    LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`';
            $where .= " and kd.ProvinceID=?";
            $groupby = 'kd.DistrictID';
        } else {
            $label = 'ksd.SubDistrict';
            $LEFT .= ' LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`';
            $where .= " and ksd.DistrictID=?";
            $groupby = 'ksd.SubDistrictID';
        }
        if ($kab != '') $prov = $kab;
        $query             = $this->db->query(sprintf($this->garden_all,$LEFT,$where), array($prov));
        $results['all']    = $query->result_array();
        $query_group       = $this->db->query(sprintf($this->garden_group,$label,$LEFT,$where,$groupby), array($prov));
        $results['group']  = $query_group->result_array();
        return $results;
    }

    function readDataDistrictGarden($user, $district, $priv = '', $petani = '', $partner = '', $prov = '', $tahun = '', $survey = '')
    {
        $where = '';
        $LEFT = '';
        if ($petani == '1') {
            $tahun = ! empty($tahun) ? $tahun : date('Y');
            $where .= " AND Certified='$tahun'";
        } else $where .= " AND Certified is null";
        switch ($survey) {
          case '0':
            $where .= " AND Survey = 'baseline'";
            break;
          case '1':
            $where .= " AND Survey = 'postline'";
            break;
          case '2':
            $where .= " AND Survey = 'latest'";
            break;
        }
        $where .= ' and ksd.DistrictID in (%s)';
        if (empty($prov)) {
            $label = 'kp.Province';
            $LEFT .= ' LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                    LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
                    LEFT JOIN ktv_province kp ON kp.`ProvinceID` = kd.`ProvinceID`';
            $groupby = 'kd.ProvinceID';
        } else {
            $where .= ' and kd.ProvinceID = ' . $prov;
            if ($priv == '') {
                $label = 'kd.District';
                $LEFT .= ' LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                    LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
                    LEFT JOIN ktv_province kp ON kp.`ProvinceID` = kd.`ProvinceID`';
                $groupby = 'ksd.DistrictID';
            } else {
                $label = 'ksd.SubDistrict';
                $LEFT .= ' LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                    LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
                    LEFT JOIN ktv_province kp ON kp.`ProvinceID` = kd.`ProvinceID`';
                $where .= ' and ksd.DistrictID=?';
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
        $query             = $this->db->query(sprintf(sprintf($this->garden_all,$LEFT,$where),implode(',', $dist)), array($priv));
        $results['all']    = $query->result_array();
        $query_group       = $this->db->query(sprintf(sprintf($this->garden_group,$label,$LEFT,$where,$groupby),implode(',', $dist)), array($priv));
        $results['group']  = $query_group->result_array();
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
