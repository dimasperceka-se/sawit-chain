<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mfinance extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->sql = "SELECT
    %s AS label
    ,SUM(gfp) AS gfp
    ,SUM(male) AS male
    ,SUM(female) AS female
    ,SUM(fin) AS fin
    ,SUM(account) AS account
    ,SUM(saving) AS saving
    ,SUM(loan) AS loan
    ,SUM(saving_money) AS saving_money
    ,SUM(saving_invest) AS saving_invest
    ,SUM(saving_gold) AS saving_gold
    ,SUM(saving_no) AS saving_no
    ,SUM(loan_yes_current) AS loan_yes_current
    ,SUM(loan_no) AS loan_no
    ,SUM(loan_yes_past) AS loan_yes_past
    ,SUM(loan_yes_past_current) AS loan_yes_past_current
    ,SUM(loan_from_family) AS loan_from_family
    ,SUM(loan_from_bank) AS loan_from_bank
    ,SUM(loan_from_trader) AS loan_from_trader
    ,SUM(loan_from_coops) AS loan_from_coops
    ,SUM(loan_for_farm) AS loan_for_farm
    ,SUM(loan_for_other) AS loan_for_other
    ,SUM(loan_for_school) AS loan_for_school
    ,SUM(loan_for_daily) AS loan_for_daily
    ,SUM(loan_for_emergency) AS loan_for_emergency
    ,SUM(account_active) AS account_active
    ,SUM(account_no) AS account_no
    ,SUM(account_inactive) AS account_inactive
    ,SUM(product_saving) AS product_saving
    ,SUM(product_loan) AS product_loan
    ,SUM(product_saving_loan) AS product_saving_loan
    ,SUM(need_loan) AS need_loan
    ,SUM(need_loan_no) AS need_loan_no
    ,SUM(future_count) AS future_count
    ,SUM(future_school) AS future_school
    ,SUM(future_invest_farm) AS future_invest_farm
    ,SUM(future_invest_other) AS future_invest_other
    ,SUM(future_emergency) AS future_emergency
    ,SUM(future_health) AS future_health
    ,SUM(value_10) AS value_10
    ,SUM(value_10_20) AS value_10_20
    ,SUM(value_20_50) AS value_20_50
    ,SUM(value_50_100) AS value_50_100
    ,SUM(value_100_200) AS value_100_200
    ,SUM(value_200) AS value_200
    ,SUM(value_0) AS value_0
FROM dash_finance kcf
%s
WHERE
    1 = 1
    %s
GROUP BY label
        ";
    }

    function readDataFinance($prov = '', $kab = '')
    {
        if ($prov == '') {
            $label = 'kp.Province';
            $LEFT = 'LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                    LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
                    LEFT JOIN ktv_province kp ON kp.`ProvinceID` = kd.`ProvinceID`';
            $where = '';
            $groupby = 'kp.ProvinceID';
        } elseif ($kab == '') {
            $label = 'kp.District';
            $LEFT = 'LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                    LEFT JOIN ktv_district kp ON kp.`DistrictID` = ksd.`DistrictID`';
            $where = 'and kp.ProvinceID=?';
            $groupby = 'kp.DistrictID';
        } else {
            $label = 'kp.SubDistrict';
            $LEFT = 'LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
                    LEFT JOIN ktv_subdistrict kp ON kp.SubDistrictID = kv.SubDistrictID';
            $where = 'and kp.DistrictID=?';
            $groupby = 'kp.SubDistrictID';
        }
        if ($kab != '') $prov = $kab;
        $query = $this->db->query(sprintf($this->sql, $label, $LEFT, $where), array($prov));
        $results = $query->result_array();

        return $results;
    }

    function readDataDistrictFinance($user, $district, $priv = '', $partner = '', $prov = '')
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
        $where .= ' and ksd.DistrictID in (%s)';
        if (empty($prov)) {
            $label = 'kp.Province';
            $LEFT .= 'LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                    LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
                    LEFT JOIN ktv_province kp ON kp.`ProvinceID` = kd.`ProvinceID`';
            $groupby = 'kp.ProvinceID';
        } else {
            $where .= ' and kd.ProvinceID = ' . $prov;
            if ($priv == '') {
                $label = 'kd.District';
                $LEFT .= 'LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
                        LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                        LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
                        LEFT JOIN ktv_province kp ON kp.`ProvinceID` = kd.`ProvinceID`';
                $groupby = 'ksd.DistrictID';
            } else {
                $label = 'ksd.SubDistrict';
                $LEFT .= 'LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
                        LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                        LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
                        LEFT JOIN ktv_province kp ON kp.`ProvinceID` = kd.`ProvinceID`';
                $where .= ' and ksd.DistrictID=?';
                $groupby = 'kv.SubDistrictID';
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

        $query = $this->db->query(sprintf(sprintf($this->sql, $label, $LEFT, $where, $groupby), implode(',', $dist)), array($priv));
        $results = $query->result_array();

        return $results;
    }

}

/* End of file mfinance.php */
/* Location: ./application/models/mfinance.php */