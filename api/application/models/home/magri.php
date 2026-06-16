<?php

class Magri extends CI_Model {

    function __construct() {
      parent::__construct();
        $this->agri_all = "
            SELECT 
            	SUM(Kompos)/SUM(Hectare) kh,SUM(Fertilizer)/SUM(Hectare) fh,SUM(PestisidaYes)/SUM(PestisidaYes+PestisidaNo)*100 pes,
            	SUM(PestisidaOrganic)/SUM(PestisidaOrganic+PestisidaNonOrganic)*100 pp,
            	SUM(ChemicalYes)/SUM(ChemicalYes+ChemicalNo)*100 che,SUM(OrganicYes)/SUM(OrganicYes+OrganicNo)*100 org,
            	SUM(ProtectiveYes)/SUM(ProtectiveYes+ProtectiveNo)*100 py,
            	SUM(BuangKubur)/SUM(BuangKebun+BuangGunakan+BuangKubur+BuangBakar+BuangLain)*100 op,
            	SUM(PestisidaKhusus)/SUM(PestisidaRumah+PestisidaKhusus+PestisidaLuar+PestisidaKebun+PestisidaLain)*100 kp,
            	
            	SUM(KomposKandang)KomposKandang,SUM(KomposCair)KomposCair,SUM(KomposGranula)KomposGranula,
            	SUM(ApplicationUrea)ApplicationUrea,SUM(ApplicationTSP)ApplicationTSP,SUM(ApplicationNPK)ApplicationNPK,
            	SUM(ApplicationKCl)ApplicationKCl,SUM(ApplicationZA)ApplicationZA,
            	SUM(TBMApplication) AS TBMApplication,SUM(TMApplication) AS TMApplication, SUM(TRApplication) AS TRApplication,
            	SUM(PenyakitKanker) AS PenyakitKanker,SUM(PenyakitBusuk) AS PenyakitBusuk,
            	SUM(PenyakitUpas) AS PenyakitUpas,SUM(PenyakitAkar) AS PenyakitAkar,
            	SUM(PenyakitVSD) AS PenyakitVSD,SUM(PenyakitAntraknose) AS PenyakitAntraknose,
            	SUM(HamaBPK) AS HamaBPK,SUM(HamaHelopeltis) AS HamaHelopeltis,SUM(HamaBatang) AS HamaBatang,
            	SUM(InsectisidaYes) AS InsectisidaYes,SUM(InsectisidaNo) AS InsectisidaNo,
            	SUM(FungisidaYes) AS FungisidaYes,SUM(FungisidaNo) AS FungisidaNo,
            	sum(BuangKebun)BuangKebun,SUM(BuangGunakan)BuangGunakan,SUM(BuangKubur)BuangKubur,SUM(BuangBakar)BuangBakar,SUM(BuangLain)BuangLain,
            	SUM(PestisidaRumah)PestisidaRumah,SUM(PestisidaKhusus)PestisidaKhusus,SUM(PestisidaLuar)PestisidaLuar,
            	SUM(PestisidaKebun)PestisidaKebun,SUM(PestisidaLain)PestisidaLain,
            	SUM(HerbisidaYes) AS HerbisidaYes,SUM(HerbisidaNo) AS HerbisidaNo,
            	SUM(herbicide_paraquat)/sum(HerbisidaYes)*100 herbicide_paraquat,
               SUM(herbicide_glyphosate)/sum(HerbisidaYes)*100 herbicide_glyphosate,
               SUM(herbicide_24d)/sum(HerbisidaYes)*100 herbicide_24d,
            	SUM(InsectisidaYes) AS InsectisidaYes,SUM(InsectisidaNo) AS InsectisidaNo,
            	SUM(insecticide_banned)/sum(InsectisidaYes)*100 insecticide_banned,
               SUM(insecticide_watchlist)/sum(InsectisidaYes)*100 insecticide_watchlist,
               SUM(insecticide_allowed)/sum(InsectisidaYes)*100 insecticide_allowed,
            	SUM(FungisidaYes) AS FungisidaYes,SUM(FungisidaNo) AS FungisidaNo,
            	SUM(fungicide_banned)/sum(FungisidaYes)*100 fungicide_banned,
               SUM(fungicide_watchlist)/sum(FungisidaYes)*100 fungicide_watchlist,
               SUM(fungicide_allowed)/sum(FungisidaYes)*100 fungicide_allowed,
            	SUM(NOGAP_NOFung)NOGAP_NOFung,SUM(NOGAP_Fung)NOGAP_Fung,SUM(GAP_NOFung)GAP_NOFung,SUM(GAP_Fung)GAP_Fung,
            	SUM(ProtectiveYes)ProtectiveYes,SUM(ProtectiveNo)ProtectiveNo
            FROM dash_agri kcf
            WHERE 1=1 %s";
         $this->agri_group = "
            SELECT
            	%s label,
            	SUM(Kompos)/SUM(Hectare) kompos,
            	SUM(Fertilizer)/SUM(Hectare) Fertilizer,
            	SUM(ProtectiveYes)ProtectiveYes,SUM(ProtectiveNo)ProtectiveNo
            FROM dash_agri kcf
            %s
            WHERE 1=1 %s
            GROUP BY %s
            ORDER BY label";
    }

    function readDataAgriinput($prov = '', $kab = '') {
        if ($prov == '') {
            $label = 'Province';
            $LEFT = 'JOIN ktv_province kp ON kp.ProvinceID = substr(kcf.VillageID,1,2)';
            $where = '';
            $groupby = 'substr(kcf.VillageID,1,2)';
        } elseif ($kab == '') {
            $label = 'District';
            $LEFT = 'JOIN ktv_district kp ON kp.DistrictID = substr(kcf.VillageID,1,4)';
            $where = 'and substr(kcf.VillageID,1,2)=?';
            $groupby = 'substr(kcf.VillageID,1,4)';
        } else {
            $label = 'SubDistrict';
            $LEFT = 'JOIN ktv_subdistrict kp ON kp.SubDistrictID = kcf.SubDistrictID';
            $where = 'and substr(kcf.VillageID,1,4)=?';
            $groupby = 'kcf.SubDistrictID';
        }
        if ($kab != '') $prov = $kab;
        $query = $this->db->query(sprintf($this->agri_all,$where), array($prov));
        $results['all'] = $query->result_array();
        $query = $this->db->query(sprintf($this->agri_group,$label, $LEFT, $where, $groupby), array($prov));
        $results['group'] = $query->result_array();
        return $results;
    }

    function readDataDistrictAgriinput($user, $district, $priv = '', $partner = '', $prov = '') {
        $where = '';
        $LEFT = '';
        $where .= ' and substr(kcf.VillageID,1,4) in (%s)';
        if (empty($prov)) {
            $label = 'Province';
            $LEFT .= ' LEFT JOIN ktv_province kp on kp.ProvinceID=substr(kcf.VillageID,1,2)';
            $groupby = 'substr(kcf.VillageID,1,2)';
        } else {
            $where .= ' and substr(kcf.VillageID,1,2) = ' . $prov;
            if ($priv == '') {
                $label = 'District';
                $LEFT .= ' LEFT JOIN ktv_district kd on kd.DistrictID=substr(kcf.VillageID,1,4)';
                $groupby = 'substr(kcf.VillageID,1,4)';
            } else {
                $label = 'SubDistrict';
                $LEFT .= ' LEFT JOIN ktv_subdistrict ks on ks.SubDistrictID=kcf.SubDistrictID';
                $where .= ' and substr(kcf.VillageID,1,4)=?';
                $groupby = 'kcf.SubDistrictID';
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
        $query_satu              = $this->db->query(sprintf(sprintf($this->agri_all,$where),implode(',', $dist)), array($priv));
        $results['all']         = $query_satu->result_array();
        $query_kedua             = $this->db->query(sprintf(sprintf($this->agri_group, $label,$LEFT, $where,$groupby), 
            implode(',', $dist)), array($priv));
        $results['group']        = $query_kedua->result_array();
        return $results;
    }

}

?>
