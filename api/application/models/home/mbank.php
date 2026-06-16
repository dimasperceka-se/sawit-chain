<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mbank extends CI_Model {

    private $bank_farmer;
    private $bank_loan;
    private $bank_distance;

    public function __construct()
    {
        parent::__construct();
        $this->bank_farmer = "
SELECT
    %s AS label
    , SUM(farmer) AS farmer
    , SUM(farmer_loan_pass) AS farmer_loan_pass
    , SUM(farmer_loan_pass_lt10) AS farmer_loan_pass_lt10
    , SUM(farmer_loan_pass_mt10) AS farmer_loan_pass_mt10
FROM dash_bank
%s
WHERE
    1 = 1
    %s
GROUP BY label
        ";
        $this->bank_loan = "
SELECT
    %s AS label,
    SUM(IF(fs.ApprovalStatus = 1, 1,0)) AS approved,
    SUM(IF(fs.ApprovalStatus = 2, 1,0)) AS finished,
    SUM(IF(fs.ApprovalStatus = 1, 1,0)) AS rejected,
    SUM(LoanAmount) AS total_amount
FROM ktv_farmer_view kcf
INNER JOIN ktv_farmer_summary fs ON fs.FarmerID = kcf.FarmerID
%s
WHERE 1 = 1 AND kcf.StatusCode = 'active'
%s
GROUP BY label
        ";
        $this->bank_distance = "
SELECT
    %s AS label
    , SUM(farmer_lt_10km) AS farmer
FROM dash_bank
%s
WHERE
    1 = 1
    %s
GROUP BY label
        ";
    }    

    public function readBank($prov = '', $kab = '', $petani = '', $tahun = '')
    {
        $where = '';
        $LEFT  = '';
        if ($prov == '') {
            $label      = 'Province';
            $LEFT      .= ' JOIN ktv_province on ProvinceID=substr(VillageID,1,2)';
        } elseif ($kab == '') {
            $label      = 'District';
            $LEFT      .= ' JOIN ktv_district on DistrictID=substr(VillageID,1,4)';
            $where     .= ' AND substr(VillageID,1,2)=?';
        } else {
            $label      = 'SubDistrict';
            $where     .= ' AND SubDistrictID=?';
        }

        if ($kab != '') $prov = $kab;

        $query_farmer       = $this->db->query(sprintf($this->bank_farmer, $label, $LEFT, $where), array($prov));
        $query_loan         = $this->db->query(sprintf($this->bank_loan, $label, $LEFT, $where), array($prov));
        $query_distance     = $this->db->query(sprintf($this->bank_distance, $label, $LEFT, $where), array($prov));

        $results['farmer']      = $query_farmer->result_array();
        $results['loan']        = $query_loan->result_array();
        $results['distance']    = $query_distance->result_array();

        return $results;
    }

    public function readDistrictBank($district, $priv = '', $petani = '', $partner = '', $prov = '')
    {
        $where = '';
        if ( ! empty($partner)) {
                $where .= "
AND kcf.`CPGid` IN (
    SELECT
        CPGid
    FROM
        `ktv_cpg_partner`
    WHERE
       `PartnerID` = {$partner}
)
            ";
        }
        if ($prov != '') {
            $where .= ' and substr(VillageID,1,2) = ' . $prov;
        }
        if ($priv == '') {
            $label = 'District';
            $LEFT .= ' LEFT JOIN ktv_district on DistrictID=substr(VillageID,1,4)';
            $where .= ' and substr(VillageID,1,4) in (%s)';
        } else {
            $label = 'SubDistrict';
            $where .= ' and substr(VillageID,1,4)=?';
        }

        // $dis = explode(',', $district);
        // for ($i = 0; $i < sizeof($dis); $i++) {
        //     $di = explode('##', $dis[$i]);
        //     $dist[] = $di[0];
        // }
        $dist = array();
        if ($user['isProgramStaff'] == 1) {
            $dist[] = $user['accessStaff'];
        } else {
            $dist[] = $user['districtPartner'];
        }

        $query_farmer     = $this->db->query(sprintf(sprintf($this->bank_farmer, $label, $LEFT, $where), implode(',', $dist)), array($priv));
        $query_loan       = $this->db->query(sprintf(sprintf($this->bank_loan, $label, $LEFT, $where), implode(',', $dist)), array($priv));
        $query_distance   = $this->db->query(sprintf(sprintf($this->bank_distance, $label, $LEFT, $where), implode(',', $dist)), array($priv));

        $results['farmer']      = $query_farmer->result_array();
        $results['loan']        = $query_loan->result_array();
        $results['distance']    = $query_distance->result_array();

        return $results;
    }

}

/* End of file mbank.php */
/* Location: ./application/models/mbank.php */