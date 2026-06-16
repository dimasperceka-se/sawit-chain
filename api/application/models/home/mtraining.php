<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mtraining extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->sql = "SELECT
    %s AS label
    ,IFNULL(SUM(gap),0) AS gap
    ,IFNULL(SUM(gnp),0) AS gnp
    ,IFNULL(SUM(gfp),0) AS gfp
    ,IFNULL(SUM(gep),0) AS gep
    ,IFNULL(SUM(gbp),0) AS gbp
    ,IFNULL(SUM(agap),0) AS agap
    ,IFNULL(SUM(gsp),0) AS gsp
    ,IFNULL(SUM(IF(`year`=2010,gap,0)),0) AS gap_2010
    ,IFNULL(SUM(IF(`year`=2011,gap,0)),0) AS gap_2011
    ,IFNULL(SUM(IF(`year`=2012,gap,0)),0) AS gap_2012
    ,IFNULL(SUM(IF(`year`=2013,gap,0)),0) AS gap_2013
    ,IFNULL(SUM(IF(`year`=2014,gap,0)),0) AS gap_2014
    ,IFNULL(SUM(IF(`year`=2015,gap,0)),0) AS gap_2015
    ,IFNULL(SUM(IF(`year`=2016,gap,0)),0) AS gap_2016
    ,IFNULL(SUM(IF(`year`=2017,gap,0)),0) AS gap_2017
    ,IFNULL(SUM(IF(`year`=2010,gnp,0)),0) AS gnp_2010
    ,IFNULL(SUM(IF(`year`=2011,gnp,0)),0) AS gnp_2011
    ,IFNULL(SUM(IF(`year`=2012,gnp,0)),0) AS gnp_2012
    ,IFNULL(SUM(IF(`year`=2013,gnp,0)),0) AS gnp_2013
    ,IFNULL(SUM(IF(`year`=2014,gnp,0)),0) AS gnp_2014
    ,IFNULL(SUM(IF(`year`=2015,gnp,0)),0) AS gnp_2015
    ,IFNULL(SUM(IF(`year`=2016,gnp,0)),0) AS gnp_2016
    ,IFNULL(SUM(IF(`year`=2017,gnp,0)),0) AS gnp_2017
    ,IFNULL(SUM(IF(`year`=2010,gfp,0)),0) AS gfp_2010
    ,IFNULL(SUM(IF(`year`=2011,gfp,0)),0) AS gfp_2011
    ,IFNULL(SUM(IF(`year`=2012,gfp,0)),0) AS gfp_2012
    ,IFNULL(SUM(IF(`year`=2013,gfp,0)),0) AS gfp_2013
    ,IFNULL(SUM(IF(`year`=2014,gfp,0)),0) AS gfp_2014
    ,IFNULL(SUM(IF(`year`=2015,gfp,0)),0) AS gfp_2015
    ,IFNULL(SUM(IF(`year`=2016,gfp,0)),0) AS gfp_2016
    ,IFNULL(SUM(IF(`year`=2017,gfp,0)),0) AS gfp_2017
    ,IFNULL(SUM(IF(`year`=2010,gep,0)),0) AS gep_2010
    ,IFNULL(SUM(IF(`year`=2011,gep,0)),0) AS gep_2011
    ,IFNULL(SUM(IF(`year`=2012,gep,0)),0) AS gep_2012
    ,IFNULL(SUM(IF(`year`=2013,gep,0)),0) AS gep_2013
    ,IFNULL(SUM(IF(`year`=2014,gep,0)),0) AS gep_2014
    ,IFNULL(SUM(IF(`year`=2015,gep,0)),0) AS gep_2015
    ,IFNULL(SUM(IF(`year`=2016,gep,0)),0) AS gep_2016
    ,IFNULL(SUM(IF(`year`=2017,gep,0)),0) AS gep_2017
    ,IFNULL(SUM(IF(`year`=2010,gbp,0)),0) AS gbp_2010
    ,IFNULL(SUM(IF(`year`=2011,gbp,0)),0) AS gbp_2011
    ,IFNULL(SUM(IF(`year`=2012,gbp,0)),0) AS gbp_2012
    ,IFNULL(SUM(IF(`year`=2013,gbp,0)),0) AS gbp_2013
    ,IFNULL(SUM(IF(`year`=2014,gbp,0)),0) AS gbp_2014
    ,IFNULL(SUM(IF(`year`=2015,gbp,0)),0) AS gbp_2015
    ,IFNULL(SUM(IF(`year`=2016,gbp,0)),0) AS gbp_2016
    ,IFNULL(SUM(IF(`year`=2017,gbp,0)),0) AS gbp_2017
    ,IFNULL(SUM(IF(`year`=2010,agap,0)),0) AS agap_2010
    ,IFNULL(SUM(IF(`year`=2011,agap,0)),0) AS agap_2011
    ,IFNULL(SUM(IF(`year`=2012,agap,0)),0) AS agap_2012
    ,IFNULL(SUM(IF(`year`=2013,agap,0)),0) AS agap_2013
    ,IFNULL(SUM(IF(`year`=2014,agap,0)),0) AS agap_2014
    ,IFNULL(SUM(IF(`year`=2015,agap,0)),0) AS agap_2015
    ,IFNULL(SUM(IF(`year`=2016,agap,0)),0) AS agap_2016
    ,IFNULL(SUM(IF(`year`=2017,agap,0)),0) AS agap_2017
    ,IFNULL(SUM(IF(`year`=2010,gsp,0)),0) AS gsp_2010
    ,IFNULL(SUM(IF(`year`=2011,gsp,0)),0) AS gsp_2011
    ,IFNULL(SUM(IF(`year`=2012,gsp,0)),0) AS gsp_2012
    ,IFNULL(SUM(IF(`year`=2013,gsp,0)),0) AS gsp_2013
    ,IFNULL(SUM(IF(`year`=2014,gsp,0)),0) AS gsp_2014
    ,IFNULL(SUM(IF(`year`=2015,gsp,0)),0) AS gsp_2015
    ,IFNULL(SUM(IF(`year`=2016,gsp,0)),0) AS gsp_2016
    ,IFNULL(SUM(IF(`year`=2017,gsp,0)),0) AS gsp_2017
FROM dash_training kcf
%s
WHERE 1 = 1
%s
GROUP BY label
HAVING label IS NOT NULL
        ";
        $this->sql_master = "SELECT
    %s AS label
    ,IFNULL(SUM(gap),0) AS gap
    ,IFNULL(SUM(gap_program),0) AS gap_program
    ,IFNULL(SUM(gap_private),0) AS gap_private
    ,IFNULL(SUM(gap_extension),0) AS gap_extension
    ,IFNULL(SUM(gnp),0) AS gnp
    ,IFNULL(SUM(gnp_program),0) AS gnp_program
    ,IFNULL(SUM(gnp_private),0) AS gnp_private
    ,IFNULL(SUM(gnp_extension),0) AS gnp_extension
    ,IFNULL(SUM(gfp),0) AS gfp
    ,IFNULL(SUM(gfp_program),0) AS gfp_program
    ,IFNULL(SUM(gfp_private),0) AS gfp_private
    ,IFNULL(SUM(gfp_extension),0) AS gfp_extension
    ,IFNULL(SUM(gep),0) AS gep
    ,IFNULL(SUM(gep_program),0) AS gep_program
    ,IFNULL(SUM(gep_private),0) AS gep_private
    ,IFNULL(SUM(gep_extension),0) AS gep_extension
    ,IFNULL(SUM(gbp),0) AS gbp
    ,IFNULL(SUM(gbp_program),0) AS gbp_program
    ,IFNULL(SUM(gbp_private),0) AS gbp_private
    ,IFNULL(SUM(gbp_extension),0) AS gbp_extension
    ,IFNULL(SUM(agap),0) AS agap
    ,IFNULL(SUM(agap_program),0) AS agap_program
    ,IFNULL(SUM(agap_private),0) AS agap_private
    ,IFNULL(SUM(agap_extension),0) AS agap_extension
    ,IFNULL(SUM(gsp),0) AS gsp
    ,IFNULL(SUM(gsp_program),0) AS gsp_program
    ,IFNULL(SUM(gsp_private),0) AS gsp_private
    ,IFNULL(SUM(gsp_extension),0) AS gsp_extension
    ,IFNULL(SUM(cst),0) AS cst
    ,IFNULL(SUM(IF(`year`=2010,gap,0)),0) AS gap_2010
    ,IFNULL(SUM(IF(`year`=2011,gap,0)),0) AS gap_2011
    ,IFNULL(SUM(IF(`year`=2012,gap,0)),0) AS gap_2012
    ,IFNULL(SUM(IF(`year`=2013,gap,0)),0) AS gap_2013
    ,IFNULL(SUM(IF(`year`=2014,gap,0)),0) AS gap_2014
    ,IFNULL(SUM(IF(`year`=2015,gap,0)),0) AS gap_2015
    ,IFNULL(SUM(IF(`year`=2016,gap,0)),0) AS gap_2016
    ,IFNULL(SUM(IF(`year`=2017,gap,0)),0) AS gap_2017
    ,IFNULL(SUM(IF(`year`=2010,gnp,0)),0) AS gnp_2010
    ,IFNULL(SUM(IF(`year`=2011,gnp,0)),0) AS gnp_2011
    ,IFNULL(SUM(IF(`year`=2012,gnp,0)),0) AS gnp_2012
    ,IFNULL(SUM(IF(`year`=2013,gnp,0)),0) AS gnp_2013
    ,IFNULL(SUM(IF(`year`=2014,gnp,0)),0) AS gnp_2014
    ,IFNULL(SUM(IF(`year`=2015,gnp,0)),0) AS gnp_2015
    ,IFNULL(SUM(IF(`year`=2016,gnp,0)),0) AS gnp_2016
    ,IFNULL(SUM(IF(`year`=2017,gnp,0)),0) AS gnp_2017
    ,IFNULL(SUM(IF(`year`=2010,gfp,0)),0) AS gfp_2010
    ,IFNULL(SUM(IF(`year`=2011,gfp,0)),0) AS gfp_2011
    ,IFNULL(SUM(IF(`year`=2012,gfp,0)),0) AS gfp_2012
    ,IFNULL(SUM(IF(`year`=2013,gfp,0)),0) AS gfp_2013
    ,IFNULL(SUM(IF(`year`=2014,gfp,0)),0) AS gfp_2014
    ,IFNULL(SUM(IF(`year`=2015,gfp,0)),0) AS gfp_2015
    ,IFNULL(SUM(IF(`year`=2016,gfp,0)),0) AS gfp_2016
    ,IFNULL(SUM(IF(`year`=2017,gfp,0)),0) AS gfp_2017
    ,IFNULL(SUM(IF(`year`=2010,gep,0)),0) AS gep_2010
    ,IFNULL(SUM(IF(`year`=2011,gep,0)),0) AS gep_2011
    ,IFNULL(SUM(IF(`year`=2012,gep,0)),0) AS gep_2012
    ,IFNULL(SUM(IF(`year`=2013,gep,0)),0) AS gep_2013
    ,IFNULL(SUM(IF(`year`=2014,gep,0)),0) AS gep_2014
    ,IFNULL(SUM(IF(`year`=2015,gep,0)),0) AS gep_2015
    ,IFNULL(SUM(IF(`year`=2016,gep,0)),0) AS gep_2016
    ,IFNULL(SUM(IF(`year`=2017,gep,0)),0) AS gep_2017
    ,IFNULL(SUM(IF(`year`=2010,gbp,0)),0) AS gbp_2010
    ,IFNULL(SUM(IF(`year`=2011,gbp,0)),0) AS gbp_2011
    ,IFNULL(SUM(IF(`year`=2012,gbp,0)),0) AS gbp_2012
    ,IFNULL(SUM(IF(`year`=2013,gbp,0)),0) AS gbp_2013
    ,IFNULL(SUM(IF(`year`=2014,gbp,0)),0) AS gbp_2014
    ,IFNULL(SUM(IF(`year`=2015,gbp,0)),0) AS gbp_2015
    ,IFNULL(SUM(IF(`year`=2016,gbp,0)),0) AS gbp_2016
    ,IFNULL(SUM(IF(`year`=2017,gbp,0)),0) AS gbp_2017
    ,IFNULL(SUM(IF(`year`=2010,agap,0)),0) AS agap_2010
    ,IFNULL(SUM(IF(`year`=2011,agap,0)),0) AS agap_2011
    ,IFNULL(SUM(IF(`year`=2012,agap,0)),0) AS agap_2012
    ,IFNULL(SUM(IF(`year`=2013,agap,0)),0) AS agap_2013
    ,IFNULL(SUM(IF(`year`=2014,agap,0)),0) AS agap_2014
    ,IFNULL(SUM(IF(`year`=2015,agap,0)),0) AS agap_2015
    ,IFNULL(SUM(IF(`year`=2016,agap,0)),0) AS agap_2016
    ,IFNULL(SUM(IF(`year`=2017,agap,0)),0) AS agap_2017
    ,IFNULL(SUM(IF(`year`=2010,gsp,0)),0) AS gsp_2010
    ,IFNULL(SUM(IF(`year`=2011,gsp,0)),0) AS gsp_2011
    ,IFNULL(SUM(IF(`year`=2012,gsp,0)),0) AS gsp_2012
    ,IFNULL(SUM(IF(`year`=2013,gsp,0)),0) AS gsp_2013
    ,IFNULL(SUM(IF(`year`=2014,gsp,0)),0) AS gsp_2014
    ,IFNULL(SUM(IF(`year`=2015,gsp,0)),0) AS gsp_2015
    ,IFNULL(SUM(IF(`year`=2016,gsp,0)),0) AS gsp_2016
    ,IFNULL(SUM(IF(`year`=2017,gsp,0)),0) AS gsp_2017
    ,IFNULL(SUM(IF(`year`=2010,cst,0)),0) AS cst_2010
    ,IFNULL(SUM(IF(`year`=2011,cst,0)),0) AS cst_2011
    ,IFNULL(SUM(IF(`year`=2012,cst,0)),0) AS cst_2012
    ,IFNULL(SUM(IF(`year`=2013,cst,0)),0) AS cst_2013
    ,IFNULL(SUM(IF(`year`=2014,cst,0)),0) AS cst_2014
    ,IFNULL(SUM(IF(`year`=2015,cst,0)),0) AS cst_2015
    ,IFNULL(SUM(IF(`year`=2016,cst,0)),0) AS cst_2016
    ,IFNULL(SUM(IF(`year`=2017,cst,0)),0) AS cst_2017
FROM dash_training_master kcf
%s
WHERE 1 = 1
%s
GROUP BY label
HAVING label IS NOT NULL
        ";
    }    

    function readDataTraining($prov = '', $kab = '', $training = 'all')
    {
        $where = " AND `type` = '{$training}'";
        if ($prov == '') {
            $label = 'kp.Province';
            $LEFT = 'LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                    LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
                    LEFT JOIN ktv_province kp ON kp.`ProvinceID` = kd.`ProvinceID`';
            $where .= 'AND kcf.VillageID IS NOT NULL';
            $groupby = 'kp.ProvinceID';
        } elseif ($kab == '') {
            $label = 'kd.District';
            $LEFT = 'LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                    LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`';
            $where .= 'AND kd.ProvinceID=?';
            $groupby = 'kd.DistrictID';
        } else {
            $label = 'ksd.SubDistrict';
            $LEFT = 'LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`';
            $where .= 'AND ksd.DistrictID=?';
            $groupby = 'ksd.SubDistrictID';
        }
        if (!empty($staff_type)) {
            $where .= " AND type = '{$staff_type}'";
        }
        if ($kab != '') $prov = $kab;
        $query_training       = $this->db->query(sprintf($this->sql, $label, $LEFT, $where, $groupby), array($prov));
        $results['data']      = $query_training->result_array();
        
        return $results;
    }

    function readDataDistrictTraining($user, $district, $priv = '', $partner = '', $prov = '', $training = 'all')
    {
        $where = " AND `type` = '{$training}'";
        $LEFT = '';
        $where .= ' and ksd.DistrictID in (%s)';
        if (empty($prov)) {
            $label = 'kp.Province';
            $LEFT .= ' LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                    LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
                    LEFT JOIN ktv_province kp ON kp.`ProvinceID` = kd.`ProvinceID`';
            $groupby = 'kp.ProvinceID';
        } else {
            $where .= ' and kd.ProvinceID = ' . $prov;
            if ($priv == '') {
                $label = 'kd.District';
                $LEFT .= ' LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                    LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
                    LEFT JOIN ktv_province kp ON kp.`ProvinceID` = kd.`ProvinceID`';
                $groupby = 'kd.DistrictID';
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

        $query       = $this->db->query(sprintf(sprintf($this->sql, $label, $LEFT, $where, $groupby), implode(',', $dist)), array($priv));
        
        $results['data']      = $query->result_array();
        return $results;
    }

    function readDataTrainingMaster($prov = '', $kab = '', $staff_type = '')
    {
        $where = "";
        if ($prov == '') {
            $label = 'kp.Province';
            $LEFT = ' LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                    LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
                    LEFT JOIN ktv_province kp ON kp.`ProvinceID` = kd.`ProvinceID`';
            $where .= 'AND kv.VillageID';
            $groupby = 'kp.ProvinceID';
        } elseif ($kab == '') {
            $label = 'kd.District';
            $LEFT = ' LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                    LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
                    LEFT JOIN ktv_province kp ON kp.`ProvinceID` = kd.`ProvinceID`';
            $where .= 'AND kv.VillageID and kp.ProvinceID=?';
            $groupby = 'kd.DistrictID';
        } else {
            $label = 'kd.District';
            $LEFT = ' LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                    LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
                    LEFT JOIN ktv_province kp ON kp.`ProvinceID` = kd.`ProvinceID`';
            $where .= 'AND kv.VillageID and kd.DistrictID=?';
            $groupby = 'kd.DistrictID';
        }
        if (!empty($staff_type) && $staff_type != 'false') {
            $where .= " AND type = '{$staff_type}'";
        }
        if ($kab != '') $prov = $kab;
        $query_training       = $this->db->query(sprintf($this->sql_master, $label, $LEFT, $where, $groupby), array($prov));
        $results['data']      = $query_training->result_array();
        
        return $results;
    }

    function readDataDistrictTrainingMaster($user, $district, $priv = '', $partner = '', $prov = '', $staff_type = '')
    {
        $where = "";
        $LEFT = '';
        $where .= ' and ksd.DistrictID in (%s)';
        if (empty($prov)) {
            $label = 'kp.Province';
            $LEFT .= ' LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                    LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
                    LEFT JOIN ktv_province kp ON kp.`ProvinceID` = kd.`ProvinceID`';
            $groupby = 'kp.ProvinceID';
        } else {
            $where .= ' and kd.ProvinceID = ' . $prov;
            if ($priv == '') {
                $label = 'kd.District';
                $LEFT .= ' LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                    LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
                    LEFT JOIN ktv_province kp ON kp.`ProvinceID` = kd.`ProvinceID`';
                $groupby = 'kd.DistrictID';
            } else {
                $label = 'kd.District';
                $LEFT .= ' LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                    LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
                    LEFT JOIN ktv_province kp ON kp.`ProvinceID` = kd.`ProvinceID`';
                $where .= ' and kd.DistrictID=?';
                $groupby = 'kd.DistrictID';
            }
        }
        if (!empty($staff_type) && $staff_type != 'false') {
            $where .= " AND type = '{$staff_type}'";
        }

        $dist = array();
        // if ($user['isProgramStaff'] == 1) {
        //     $dist[] = $user['accessStaff'];
        // } else {
        //     $dist[] = $user['districtPartner'];
        // }
        $dist[] = $user['district_access'];
        // if ($user['isPrivateStaff'] AND $user['FlagAccess']) {
        //     $where .= " AND kcf.`CPGid` IN (SELECT CPGid FROM `ktv_cpg_partner` WHERE `PartnerID` = {$_SESSION['PartnerID']})";        }

        $query       = $this->db->query(sprintf(sprintf($this->sql_master, $label, $LEFT, $where, $groupby), implode(',', $dist)), array($priv));
        
        $results['data']      = $query->result_array();
        return $results;
    }

}

/* End of file msurvey.php */
/* Location: ./application/models/msurvey.php */