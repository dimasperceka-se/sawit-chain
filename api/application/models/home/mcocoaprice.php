<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mcocoaprice extends CI_Model {

    public $sql;

    public function __construct()
    {
        parent::__construct();
        $this->sql['icco_price'] = "
SELECT
    cp.ICCODailyPriceUS AS latest_price,
    z.max_date AS latest_date,
    z.avg_price
FROM cocoa_price cp
JOIN (
    SELECT
        AVG(price) AS avg_price,
        MAX(`date`) AS max_date
    FROM (
    SELECT
        ICCODailyPriceUS AS price,
        cp.system_date AS `date`
    FROM cocoa_price cp
    WHERE
        cp.ICCODailyPriceUS IS NOT NULL
        AND YEAR(system_date) = YEAR(CURRENT_DATE)
    ) r
) z ON z.max_date = cp.system_date
";
        $this->sql['district_price'] = "
SELECT
    SUBSTRING_INDEX(latest,':',-1)*1000/13000 AS latest_price,
    SUBSTRING_INDEX(latest,':',1) AS latest_date,
    avg_price*1000/13000 AS avg_price
FROM (
SELECT
    SUM(sum_price)/SUM(count_price) AS avg_price,
    MAX(latest) AS latest
FROM (
SELECT
    cp.DistrictID,
    z.count_price,
    z.sum_price,
    z.avg_price,
    cp.CocoaPrice,
    z.max_date,
    CONCAT(max_date,':',CocoaPrice) AS latest
FROM ktv_price cp
JOIN (
SELECT
    cp.DistrictID,
    COUNT(cp.CocoaPrice) AS count_price,
    SUM(cp.CocoaPrice) AS sum_price,
    AVG(cp.CocoaPrice) AS avg_price,
    MAX(cp.CocoaPriceDate) AS max_date
FROM ktv_price cp
WHERE
    1 = 1
    AND cp.Type = 'FF'
    AND YEAR(CocoaPriceDate) = YEAR(CURRENT_DATE)
    --where--
GROUP BY cp.DistrictID
) z ON z.DistrictID = cp.DistrictID AND cp.CocoaPriceDate = z.max_date
) r
) r
        ";
        $this->sql['icco_history'] = "
SELECT
    UNIX_TIMESTAMP(cp.system_date) * 1000 AS `timestamp`,
    cp.ICCODailyPriceUS AS price
FROM cocoa_price cp
WHERE
    cp.ICCODailyPriceUS > 0
ORDER BY `timestamp`
        ";
        $this->sql['district_history'] = "
SELECT
    UNIX_TIMESTAMP(cp.CocoaPriceDate) * 1000 AS `timestamp`,
    ROUND(AVG(cp.CocoaPrice)*1000/13000,2) AS price
FROM ktv_price cp
WHERE
    1 = 1
    AND cp.Type = 'FF'
    --where--
GROUP BY `timestamp`
        ";
    }

    function readDataPrice($prov = '', $kab = '')
    {
        $where = '';
        $params = array();
        if (!empty($prov)) {
            $where .= " AND SUBSTR(cp.DistrictID,1,2) = ?";
            $params[] = $prov;
            if (!empty($kab)) {
                $where .= " AND SUBSTR(cp.DistrictID,1,4) = ?";
                $params[] = $kab;
            }
        }
        $query = $this->db->query(str_replace('--where--', $where, $this->sql['district_price']));
        $results['district'] = $query->row_array(0);

        $query = $this->db->query($this->sql['icco_price']);
        $results['icco'] = $query->row_array(0);

        $query = $this->db->query(str_replace('--where--', $where, $this->sql['district_history']));
        $district_history = $query->result_array();
        $results['district_history'] = array();
        if (!empty($district_history)) {
            foreach ($district_history as $key => $value) {
                $results['district_history'][$key][] = intval($value['timestamp']);
                $results['district_history'][$key][] = floatval($value['price']);
            }
        }

        $query = $this->db->query($this->sql['icco_history']);
        $icco_history = $query->result_array();
        $results['icco_history'] = array();
        if (!empty($icco_history)) {
            foreach ($icco_history as $key => $value) {
                $results['icco_history'][$key][] = intval($value['timestamp']);
                $results['icco_history'][$key][] = floatval($value['price']);
            }
        }

        return $results;
    }

    function readDataDistrictPrice($user, $district, $priv = '', $partner = '', $prov = '')
    {
        $where = '';
        $params = array();
        if (!empty($prov)) {
            $where .= " AND SUBSTR(cp.DistrictID,1,2) = ?";
            $params[] = $prov;
            if (!empty($kab)) {
                $where .= " AND SUBSTR(cp.DistrictID,1,4) = ?";
                $params[] = $kab;
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

/* End of file mcocoaprice.php */
/* Location: ./application/models/mcocoaprice.php */