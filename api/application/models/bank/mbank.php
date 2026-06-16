<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mbank extends CI_Model {

	private $sql_count = "SELECT FOUND_ROWS() AS total";

	public function __construct()
	{
		parent::__construct();
	}

    public function getBanks($textSearch,$start,$limit, $sortingField, $sortingDir)
    {
        if ($sortingDir == "") {
            $sortingDir = 'DESC';
		}

		if ($sortingField == "") {
            $sorting = 'ORDER BY a.BankID '.$sortingDir;
        }else{
			$sorting = 'ORDER BY '.$sortingField.' '.$sortingDir;
		}
        $sql = "SELECT 
			SQL_CALC_FOUND_ROWS a.BankID, a.BankCode, a.BankName, a.BankDesc
		FROM 
			`ktv_bank` as a
		WHERE
			a.StatusCode = 'active'
		--filter--
			".$sorting."
		Limit 
			?,? ";

		$filter = '';
        if (!empty($textSearch)) {
            $filter .= " AND a.BankCode like '%{$textSearch}%' OR a.BankName like '%{$textSearch}%' "; // data element name
		}

		$sql = str_replace('--filter--',$filter,$sql);

        $query = $this->db->query($sql,array((int)$start,(int)$limit));
		
		//ini untuk ambil jumlah query sebelum dilimit
		$sql_total = "SELECT FOUND_ROWS() AS total";
        $query_total = $this->db->query($sql_total);

        $passing = [
        	'data'  => [],
        	'total' => 0
        ];

        if ($query->num_rows() > 0) {  //ini ambil dari variable $query diatas
            $total = $query_total->row_array(0);
            
            $passing = [
	        	'data'  => $query->result_array(),
	        	'total' => $total['total']
	        ];
        }

        return $passing;
    }

    public function insertNewBank($paramPost = null){
		$this->db->trans_begin();
		$datapost = array(
			"BankID"      => (int) $paramPost['BankID'],
			"BankCode"    => $paramPost["BankCode"],
			"BankName"    => $paramPost["BankName"],
			"BankDesc"    => $paramPost["BankDesc"],
			"DateCreated" => date("Y-m-d H:i:s"),
			"CreatedBy"   => $_SESSION["userid"],
			"StatusCode"  => "active"
		);

		$query	= $this->db->insert("ktv_bank",$datapost);
		$BankID = $this->db->insert_id();

		if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to add data";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
			$results['message'] = "Bank added";
			$results['BankID']  = $BankID;			
		}

		return $results;
	}
	
	public function updateNewBank($paramPost = null){
    	$BankID = (int) $paramPost['BankID'];
		$this->db->trans_begin();
		$datapost = array(
			"BankID"          => (int) $paramPost['BankID'],
			"BankCode"        => $paramPost["BankCode"],
			"BankName"        => $paramPost["BankName"],
			"BankDesc"        => $paramPost["BankDesc"],
			"DateUpdated"     => date("Y-m-d H:i:s"),
			"LastModifiedBy"  => $_SESSION["userid"],
			"StatusCode"      => "active"
		);

		$this->db->where("BankID", $BankID);
		$query	= $this->db->update("ktv_bank", $datapost);

		if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to update data";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
			$results['message'] = "Data updated";
			$results['BankID'] = $BankID;			
		}

		return $results;
	}

    function DeleteNewBank($BankID){
		$this->db->trans_begin();
		$datapost = array(
			"StatusCode"  => "nullified",
			"DateUpdated" => date("Y-m-d H:i:s"),
			"LastModifiedBy" => $_SESSION["userid"]
		);

		$this->db->where("BankID", $BankID);
		$query	= $this->db->update("ktv_bank", $datapost);

		if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to delete data";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
			$results['message'] = "Bank deleted";		
		}

		return $results;
	}

	public function getBank($id)
	{
		$sql = "SELECT
    `BankID` AS 'id',
    `BankName` AS 'name',
    `BankDesc` AS 'desc',
    `DateCreated`,
    `CreatedBy`,
    `DateUpdated`,
    `LastModifiedBy`
FROM
    `ktv_bank`
WHERE
    BankID = ?
		";
		$query = $this->db->query($sql, array(intval($id)));
		// $query = $this->db->get('ktv_bank', $limit, $start);
		return $query->row_array(0);
	}

	public function createBank($name, $desc)
	{
		$sql = "
INSERT INTO `ktv_bank` (
    `BankName`,
    `BankDesc`,
    `DateCreated`,
    `CreatedBy`
)
VALUES
    (
        ?,
        ?,
        NOW(),
        ?
    )";
		return $this->db->query($sql, array($name, $desc, $_SESSION['userid']));
	}

	public function updateBank($name, $desc, $id)
	{
		$sql = "
UPDATE `ktv_bank` SET
    `BankName` = ?,
    `BankDesc` = ?,
    `DateUpdated` = NOW(),
    `LastModifiedBy` = ?
WHERE
	`BankID` = ?
";
		return $this->db->query($sql, array($name, $desc, $_SESSION['userid'], $id));
	}

	public function deleteBank($id)
	{
      $sql="UPDATE ktv_bank SET StatusCode = 'nullified', LastModifiedBy='".$_SESSION['userid']."', DateUpdated = NOW() WHERE BankID = ? LIMIT 1";
      return $this->db->query($sql,array($id));
		//return $this->db->delete('ktv_bank', array('BankID' => $id));
	}

    public function getBranches($start=0, $limit=10, $ProvinceID=null,$DistrictID=null,$SubDistrictID=null,$key=null)
    {
        $filter = '';
        $limit_filter = '';
        $params = array();
        if (!empty($ProvinceID)) {
            $filter .= " AND p.ProvinceID = ?";
            $params[] = $ProvinceID;
        }
        if (!empty($DistrictID)) {
            $filter .= " AND d.DistrictID = ?";
            $params[] = $DistrictID;
        }
        if (!empty($SubDistrictID)) {
            $filter .= " AND sd.SubDistrictID = ?";
            $params[] = $SubDistrictID;
        }
        if (!empty($key)) {
            $filter .= " AND bb.BranchName LIKE '%{$key}%'";
            // $params[] = $SubDistrictID;
        }
        if (isset($start) && !empty($limit)) {
            $limit_filter .= " LIMIT ?, ?";
            $params[] = intval($start);
            $params[] = intval($limit);
        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS
    `BranchID` AS 'id',
    `BranchBankID` AS 'bankid',
    b.BankName AS bank,
    `BranchName` AS 'name',
    `BranchAddress` AS 'address',
    `BranchProvinceID` AS 'provinceid',
    p.`Province`,
    `BranchDistrictID` AS 'districtid',
    d.`District`,
    `BranchSubDistrictID` AS 'subdistrictid',
    sd.`SubDistrict`,
    `BranchVillageID` AS 'villageid',
    v.`Village`,
    IFNULL(`BranchPhone`,'') AS 'phone',
    `BranchLatitude` AS 'latitude',
    `BranchLongitude` AS 'longitude',
    `BranchDesc` AS 'desc'
    -- `DateCreated`,
    -- `CreatedBy`,
    -- `DateUpdated`,
    -- `LastModifiedBy`
FROM
    `ktv_bank_branch`  bb
JOIN ktv_bank b ON b.BankID = bb.BranchBankID
LEFT JOIN ktv_province p ON p.`ProvinceID` = bb.`BranchProvinceID`
LEFT JOIN ktv_district d ON d.`DistrictID` = bb.`BranchDistrictID`
LEFT JOIN ktv_subdistrict sd ON sd.`SubDistrictID` = bb.`BranchSubDistrictID`
LEFT JOIN ktv_village v ON v.`VillageID` = bb.`BranchVillageID`
WHERE
    1 = 1 AND
    bb.StatusCode != 'nullified'
    --filter--
--limit--
        ";
        $sql = str_replace('--filter--', $filter, $sql);
        $sql = str_replace('--limit--', $limit_filter, $sql);
        $query = $this->db->query($sql, $params);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $count = $this->db->query($this->sql_count)->row_array(0);
        // $query = $this->db->get('ktv_bank', $limit, $start);
        return array(
            'data'      => $query->result_array(),
            'count'     => $count['total']
        );
    }

	public function getBranch($id)
	{
		$sql = "SELECT
    `BranchID` AS 'id',
    `BranchBankID` AS 'bankid',
    b.BankName AS bank,
    `BranchName` AS 'name',
    `BranchAddress` AS 'address',
    `BranchProvinceID` AS 'provinceid',
    p.`Province`,
    `BranchDistrictID` AS 'districtid',
    d.`District`,
    `BranchSubDistrictID` AS 'subdistrictid',
    IFNULL(sd.`SubDistrict`,'') AS SubDistrict,
    `BranchVillageID` AS 'villageid',
    v.`Village`,
    IFNULL(`BranchPhone`,'') AS 'phone',
    `BranchLatitude` AS 'latitude',
    `BranchLongitude` AS 'longitude',
    `BranchDesc` AS 'desc'
    -- `DateCreated`,
    -- `CreatedBy`,
    -- `DateUpdated`,
    -- `LastModifiedBy`
FROM
    `ktv_bank_branch`  bb
JOIN ktv_bank b ON b.BankID = bb.BranchBankID
LEFT JOIN ktv_province p ON p.`ProvinceID` = bb.`BranchProvinceID`
LEFT JOIN ktv_district d ON d.`DistrictID` = bb.`BranchDistrictID`
LEFT JOIN ktv_subdistrict sd ON sd.`SubDistrictID` = bb.`BranchSubDistrictID`
LEFT JOIN ktv_village v ON v.`VillageID` = bb.`BranchVillageID`
WHERE
    BranchID = ?
		";
		$query = $this->db->query($sql, array(intval($id)));
        return $query->row_array(0);
	}

	public function createBranch($BranchBankID,$BranchName,$BranchAddress,$BranchProvinceID,$BranchDistrictID,$BranchSubDistrictID,$BranchVillageID,$BranchPhone,$BranchLatitude,$BranchLongitude,$BranchDesc)
	{
		$sql = "
INSERT INTO `ktv_bank_branch`
SET
    `BranchBankID`          = ?,
    `BranchName`            = ?,
    `BranchAddress`         = ?,
    `BranchProvinceID`      = ?,
    `BranchDistrictID`      = ?,
    `BranchSubDistrictID`   = ?,
    `BranchVillageID`       = ?,
    `BranchPhone`           = ?,
    `BranchLatitude`        = ?,
    `BranchLongitude`       = ?,
    `BranchDesc`            = ?,
    `DateCreated`           = NOW(),
    `CreatedBy`             = ?
		";
		return $this->db->query($sql, array($BranchBankID,$BranchName,$BranchAddress,$BranchProvinceID,$BranchDistrictID,$BranchSubDistrictID,$BranchVillageID,$BranchPhone,$BranchLatitude,$BranchLongitude,$BranchDesc, $_SESSION['userid']));
	}

	public function updateBranch($BranchBankID,$BranchName,$BranchAddress,$BranchProvinceID,$BranchDistrictID,$BranchSubDistrictID,$BranchVillageID,$BranchPhone,$BranchLatitude,$BranchLongitude,$BranchDesc, $id)
	{
		$sql = "
UPDATE `ktv_bank_branch`
SET
    `BranchBankID`          = ?,
    `BranchName`            = ?,
    `BranchAddress`         = ?,
    `BranchProvinceID`      = ?,
    `BranchDistrictID`      = ?,
    `BranchSubDistrictID`   = ?,
    `BranchVillageID`       = ?,
    `BranchPhone`           = ?,
    `BranchLatitude`        = ?,
    `BranchLongitude`       = ?,
    `BranchDesc`            = ?,
    `DateUpdated`           = NOW(),
    `LastModifiedBy`        = ?
WHERE
    `BranchID`              = ?
";
		return $this->db->query($sql, array($BranchBankID,$BranchName,$BranchAddress,$BranchProvinceID,$BranchDistrictID,$BranchSubDistrictID,$BranchVillageID,$BranchPhone,$BranchLatitude,$BranchLongitude,$BranchDesc, $_SESSION['userid'], $id));
	}

	public function deleteBranch($id)
	{
      $sql="UPDATE ktv_bank_branch SET StatusCode='nullified', LastModifiedBy='".$_SESSION['userid']."', DateUpdated = NOW() WHERE BranchID = ? LIMIT 1";
      return $this->db->query($sql,array($id));
		//return $this->db->delete('ktv_bank_branch', array('BranchID' => $id));
	}

    public function getBangList($param = array())
    {
        extract($param);
        $this->db->select('b.BankID AS id, BankName AS name');
        $this->db->join('ktv_bank_branch bb', 'bb.BranchBankID = b.BankID', 'LEFT');

        if (!empty($DistrictID)) {
            $this->db->where('bb.BranchDistrictID', $DistrictID, FALSE);
        }
        if (!empty($SubDistrictID)) {
            $this->db->where('bb.BranchSubDistrictID', $SubDistrictID, FALSE);
        }
        $this->db->group_by('BankID');
        $this->db->order_by('BankName', 'asc');
        return $this->db->get('ktv_bank b')->result_array();
    }

    public function getBranchList($bank, $SubDistrictID = null)
    {
        $this->db->select('BranchID AS id, CONCAT(BranchName," - ",SubDistrict) AS name', FALSE);
        $this->db->join('ktv_subdistrict', 'SubDistrictID = BranchSubDistrictID', 'left');
        $this->db->where('BranchBankID', $bank);
        if (!empty($SubDistrictID)) {
            $this->db->where('BranchSubDistrictID', $SubDistrictID, FALSE);
        }
        $this->db->order_by('BranchName', 'asc');
        return $this->db->get('ktv_bank_branch')->result_array();
    }

    public function getProvince()
    {
        $this->db->select('ProvinceID AS id, Province AS name');
        $this->db->where('active', '1');
        return $this->db->get('ktv_province')->result_array();
    }

    public function getDistrict($provinceid)
    {
        $this->db->select('DistrictID AS id, District AS name');
        $this->db->where('ProvinceID', $provinceid);
        return $this->db->get('ktv_district')->result_array();
    }

    public function getSubDistrict($districtid)
    {
        $this->db->select('SubDistrictID AS id, SubDistrict AS name');
        $this->db->where('DistrictID', $districtid);
        return $this->db->get('ktv_subdistrict')->result_array();
    }

    public function getVillage($subdistrictid)
    {
        $this->db->select('VillageID AS id, Village AS name');
        $this->db->where('SubDistrictID', $subdistrictid);
        return $this->db->get('ktv_village')->result_array();
    }

    public function getBranchStafs($BranchID)
    {
      $sql="SELECT * FROM ktv_bank_branch_staff WHERE BranchID = ? AND StatusCode != 'nullified'";
      $query = $this->db->query($sql,array($BranchID));
      return $query->result_array();
      //return $this->db->get_where('ktv_bank_branch_staff', compact('BranchID'))->result_array();
    }

    public function getBranchStaf($StaffID)
    {
        $sql = "SELECT
    StaffID,
    BranchID,
    StaffName,
    Phone,
    Email,
    StaffBirth,
    StaffGender,
    Photo,
    IdentityNumber,
    v.VillageID,
    Address,
    sd.SubDistrictID,
    d.DistrictID,
    p.ProvinceID,
    u.UserId,
    u.UserName,
    g.GroupId
FROM
    ktv_bank_branch_staff s
LEFT JOIN ktv_village v ON v.VillageID = s.VillageID
LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
LEFT JOIN ktv_district d ON d.DistrictID = sd.DistrictID
LEFT JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
LEFT JOIN sys_user u ON u.UserId = s.UserId
LEFT JOIN sys_user_group ug ON ug.UserGroupUserId = u.UserId
LEFT JOIN sys_group g ON g.GroupId = ug.UserGroupGroupId
WHERE
    s.StaffID = ?
        ";
        $query = $this->db->query($sql, array($StaffID));
        if ($query->num_rows()>0) {
            return $query->row_array(0);
        }
    }

    public function updateBranchStaf($BranchID,$StaffName,$Phone,$Email,$StaffBirth,$StaffGender,$IdentityNumber,$VillageID,$Address,$StaffID)
    {
        $sql = "UPDATE `ktv_bank_branch_staff` SET
    `BranchID` = ?,
    `StaffName` = ?,
    `Phone` = ?,
    `Email` = ?,
    `StaffBirth` = ?,
    `StaffGender` = ?,
    `IdentityNumber` = ?,
    `VillageID` = ?,
    `Address` = ?,
    `DateUpdated` = NOW(),
    `LastModifiedBy` = ?
WHERE
    `StaffID` = ?
";
        return $this->db->query($sql, array($BranchID,$StaffName,$Phone,$Email,$StaffBirth,$StaffGender,$IdentityNumber,$VillageID,$Address,$_SESSION['userid'],$StaffID));
    }

    public function addBranchStaf($BranchID,$StaffName,$Phone,$Email,$StaffBirth,$StaffGender,$IdentityNumber,$VillageID,$Address)
    {
        $sql = "INSERT INTO `ktv_bank_branch_staff` (
    `BranchID`,
    `StaffName`,
    `Phone`,
    `Email`,
    `StaffBirth`,
    `StaffGender`,
    `IdentityNumber`,
    `VillageID`,
    `Address`,
    `DateCreated`,
    `CreatedBy`
)
VALUES
    (
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        NOW(),
        ?
    )";
        $this->db->query($sql, array($BranchID,$StaffName,$Phone,$Email,$StaffBirth,$StaffGender,$IdentityNumber,$VillageID,$Address,$_SESSION['userid']));
        return $this->db->insert_id();
    }

    public function updateStaffPhoto($Photo, $StaffID)
    {
        $sql = "UPDATE ktv_bank_branch_staff
SET
    Photo = ?
WHERE
    StaffID = ?
        ";
        return $this->db->query($sql, array($Photo, $StaffID));
    }

    public function updateStaffUser($UserId, $StaffID)
    {
        $sql = "UPDATE ktv_bank_branch_staff
SET
    UserId = ?
WHERE
    StaffID = ?
        ";
        return $this->db->query($sql, array($UserId, $StaffID));
    }

    public function deleteBranchStaff($StaffID)
    {
      $sql="UPDATE ktv_bank_branch_staff SET StatusCode='nullified', LastModifiedBy='".$_SESSION['userid']."', DateUpdated = NOW() WHERE StaffID = ? LIMIT 1";
      return $this->db->query($sql,array($StaffID));
      //return $this->db->delete('ktv_bank_branch_staff', array('StaffID' => $StaffID));
    }

    public function getGroup()
    {
        $sql = "SELECT
    `GroupId` AS `id`,
    `GroupName` AS `name`
FROM
    `sys_group`
 ORDER BY GroupName
        ";
        return $this->db->query($sql)->result_array();
    }

    public function getFarmerList($filter = array())
    {
        extract($filter);
        $limit_filter   = '';
        $where          = '';
        $params         = array();
        $sql = "SELECT SQL_CALC_FOUND_ROWS
    f.`FarmerID` AS id,
    f.`FarmerID`,
    f.`FarmerName`,
    f.`Address`,
    c.`GroupName`,
    v.`Village`,
    sd.`SubDistrict`,
    d.`District`,
    p.`Province`,
    CASE fs.ApprovalStatus
       WHEN 1 THEN 'Approved'
       WHEN 2 THEN 'Finalized'
       WHEN 3 THEN 'Rejected'
       ELSE 'Unprocessed'
    END AS ApprovalStatus
FROM `ktv_farmer` f
-- only farmer with over 500kg production
INNER JOIN (
    SELECT
        g.FarmerID,
        (IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
        (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
        (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0)) AS Production,
        GardenHaUnCertified
    FROM ktv_farmer_garden g
    JOIN (SELECT FarmerID, GardenNr, MAX(SurveyNr) AS SurveyNr FROM ktv_farmer_garden g GROUP BY FarmerID, GardenNr) z ON g.FarmerID = z.FarmerID AND g.GardenNr = z.GardenNr AND g.SurveyNr = z.SurveyNr
) g ON g.FarmerID = f.FarmerID
INNER JOIN (
    SELECT
        f.FarmerID, f.LoanYesNo, f.NeedLoan
    FROM ktv_farmer_financial f
    JOIN (SELECT    f.FarmerID, MAX(f.SurveyNr) AS SurveyNr FROM ktv_farmer_financial f GROUP BY f.FarmerID) z ON z.FarmerID = f.FarmerID
    WHERE 1 = 1
        -- f.LoanYesNo = 2 AND f.NeedLoan = 1
) fin ON fin.FarmerID = f.FarmerID
LEFT JOIN ktv_farmer_summary fs ON fs.FarmerID = f.FarmerID
LEFT JOIN ktv_cpg c ON c.`CPGid` = f.`CPGid`
LEFT JOIN ktv_village v ON v.`VillageID` = f.`VillageID`
LEFT JOIN ktv_subdistrict sd ON sd.`SubDistrictID` = v.`SubDistrictID`
LEFT JOIN ktv_district d ON d.`DistrictID` = sd.`DistrictID`
LEFT JOIN ktv_province p ON p.`ProvinceID` = d.`ProvinceID`
WHERE 1 = 1
    --where--
ORDER BY f.`FarmerName`
--limit--
        ";
        if (!empty($province)) {
            $where      .= ' AND p.ProvinceID = ?';
            $params[]   = $province;
        }
        if (!empty($district)) {
            $where      .= ' AND d.DistrictID = ?';
            $params[]   = $district;
        }
        if (!empty($subdistrict)) {
            $where      .= ' AND sd.SubDistrictID = ?';
            $params[]   = $subdistrict;
        }
        if (!empty($cpg)) {
            $where      .= ' AND c.CPGid = ?';
            $params[]   = $cpg;
        }
        if (empty($status)) {
            $where .= " AND (fs.ApprovalStatus = '0' OR fs.ApprovalStatus IS NULL)";
        } else {
            $where .= " AND fs.ApprovalStatus = ?";
            $params[] = $status;
        }
        if ($NeedLoan == 'yes') {
            $where      .= ' AND fin.NeedLoan = 1';
        } elseif ($NeedLoan == 'no') {
            $where      .= ' AND fin.NeedLoan = 2';
        }
        if ($LoanYesNo == 'yes') {
            $where      .= ' AND fin.LoanYesNo != 2';
        } elseif ($LoanYesNo == 'no') {
            $where      .= ' AND fin.LoanYesNo = 2';
        }
        // if ($Production == 'yes') {
        //     $where      .= ' AND g.Production > 500';
        // }
        $Production = intval($Production);
        if ($Production) {
            $where      .= " AND g.Production >= {$Production}";
        }
        // $Professionalism = explode('|',$professionalism);
        $prof = array();
        if (strpos($Professionalism, 'unprofessional') !== false) {
            $prof[]      = 'g.Production/g.GardenHaUnCertified < 500';
        }
        if (strpos($Professionalism, 'progressing') !== false) {
            $prof[]      = 'g.Production/g.GardenHaUnCertified BETWEEN 500 AND 1000';
        }
        if (strpos($Professionalism, 'professional') !== false) {
            $prof[]      = 'g.Production/g.GardenHaUnCertified > 1000';
        }
        $prof = implode(' OR ', $prof);
        if (!empty($prof)) {
            $where .= ' AND ('.$prof.')';
        }
        if (isset($start) && isset($limit)) {
            $limit_filter = 'LIMIT ?,?';
            $params[] = intval($start);
            $params[] = intval($limit);
        }

        $sql = str_replace('--where--', $where, $sql);
        $sql = str_replace('--limit--', $limit_filter, $sql);
        $query = $this->db->query($sql, $params);
        // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }

    public function countFarmerList()
    {
        return $this->db->query("SELECT FOUND_ROWS() AS total")->row(0)->total;
    }

    public function saveFarmerSummaryLog($FarmerID, $BankID, $BankBrancID, $Desc)
    {
        $sql = "INSERT INTO ktv_farmer_summary_log (
    FarmerID,
    BankID,
    BankBrancID,
    `Desc`
)
VALUES
    (
        ?,
        ?,
        ?,
        ?
    )";
        return $this->db->query($sql, array($FarmerID, $BankID, $BankBrancID, $Desc));
    }

    public function getGeospatialBank($ProvinceID,$DistrictID,$SubDistrictID,$BranchID)
    {
        $filter = '';
        $params = array();
        if (!empty($ProvinceID)) {
            $filter .= " AND SUBSTR(bb.BranchVillageID,1,2) = ?";
            $params[] = $ProvinceID;
        }
        if (!empty($DistrictID)) {
            $filter .= " AND SUBSTR(bb.BranchVillageID,1,4) = ?";
            $params[] = $DistrictID;
        }
        if (!empty($SubDistrictID)) {
            $filter .= " AND SUBSTR(bb.BranchVillageID,1,7) = ?";
            $params[] = $SubDistrictID;
        }
        if (!empty($BranchID)) {
            $filter .= " AND BranchID = ?";
            $params[] = $BranchID;
        }
        $sql = "SELECT
    'bank' AS type,
    BranchID AS id,
    BranchName AS name,
    BranchLatitude AS lat,
    BranchLongitude AS lng
FROM
    ktv_bank_branch bb
WHERE
    1 = 1
    AND BranchLatitude
    AND BranchLongitude
    --filter--

        ";
        $sql = str_replace('--filter--', $filter, $sql);
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }

    public function getGeospatialFarmerFitted($lat, $lng, $radius)
    {
        return $this->getGeospatialFarmer(array(
            'lat' => $lat,
            'lng' => $lng,
            'radius' => $radius,
            'status' => 0,
            'NeedLoan' => 'yes',
            'Production' => 500
            )
        );
    }

    public function getGeospatialFarmerApproved($lat, $lng, $radius)
    {
        return $this->getGeospatialFarmer(array(
            'lat' => $lat,
            'lng' => $lng,
            'radius' => $radius,
            'status' => 1,
            )
        );
    }

    public function getGeospatialFarmerRejected($lat, $lng, $radius)
    {
        return $this->getGeospatialFarmer(array(
            'lat' => $lat,
            'lng' => $lng,
            'radius' => $radius,
            'status' => 3,
            )
        );
    }

    public function getGeospatialFarmer($filter)
    {
        extract($filter);
        $sql  = "SELECT
    g.FarmerID,
    g.GardenNr,
    g.SurveyNr,
    g.Latitude AS lat,
    g.Longitude AS lng,
    ( 6371 * ACOS( COS( RADIANS(?) ) * COS( RADIANS( g.Latitude) )
    * COS( RADIANS(g.Longitude) - RADIANS(?)) + SIN(RADIANS(?))
    * SIN( RADIANS(g.Latitude)))) AS distance
FROM ktv_farmer f 
INNER JOIN (
    SELECT
        g.FarmerID,
        g.GardenNr,
        g.SurveyNr,
        g.Latitude,
        g.Longitude,
        (IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
        (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
        (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0)) AS Production,
        GardenHaUnCertified
    FROM ktv_farmer_garden g
    JOIN (SELECT FarmerID, GardenNr, MAX(SurveyNr) AS SurveyNr FROM ktv_farmer_garden g GROUP BY FarmerID, GardenNr) z ON g.FarmerID = z.FarmerID AND g.GardenNr = z.GardenNr AND g.SurveyNr = z.SurveyNr
) g ON g.FarmerID = f.FarmerID
INNER JOIN (
    SELECT
        f.FarmerID, f.LoanYesNo, f.NeedLoan
    FROM ktv_farmer_financial f
    JOIN (SELECT    f.FarmerID, MAX(f.SurveyNr) AS SurveyNr FROM ktv_farmer_financial f GROUP BY f.FarmerID) z ON z.FarmerID = f.FarmerID
    WHERE 1 = 1
) fin ON fin.FarmerID = f.FarmerID
LEFT JOIN ktv_farmer_summary fs ON fs.FarmerID = f.FarmerID
WHERE 1 = 1 AND f.StatusCode = 'active'
    --where--
HAVING distance < ?
        ";
        $where          = '';
        $params         = array();
        $params[] = $lat;
        $params[] = $lng;
        $params[] = $lat;

        if (!empty($province)) {
            $where      .= ' AND p.ProvinceID = ?';
            $params[]   = $province;
        }
        if (!empty($district)) {
            $where      .= ' AND d.DistrictID = ?';
            $params[]   = $district;
        }
        if (!empty($subdistrict)) {
            $where      .= ' AND sd.SubDistrictID = ?';
            $params[]   = $subdistrict;
        }
        if (!empty($cpg)) {
            $where      .= ' AND c.CPGid = ?';
            $params[]   = $cpg;
        }
        if (empty($status)) {
            $where .= " AND (fs.ApprovalStatus = '0' OR fs.ApprovalStatus IS NULL)";
        } else {
            $where .= " AND fs.ApprovalStatus = ?";
            $params[] = $status;
        }
        if ($NeedLoan == 'yes') {
            $where      .= ' AND fin.NeedLoan = 1';
        } elseif ($NeedLoan == 'no') {
            $where      .= ' AND fin.NeedLoan = 2';
        }
        if ($LoanYesNo == 'yes') {
            $where      .= ' AND fin.LoanYesNo != 2';
        } elseif ($LoanYesNo == 'no') {
            $where      .= ' AND fin.LoanYesNo = 2';
        }
        // if ($Production == 'yes') {
        //     $where      .= ' AND g.Production > 500';
        // }
        $Production = intval($Production);
        if ($Production) {
            $where      .= " AND g.Production >= {$Production}";
        }
        // $Professionalism = explode('|',$professionalism);
        $prof = array();
        if (strpos($Professionalism, 'unprofessional') !== false) {
            $prof[]      = 'g.Production/g.GardenHaUnCertified < 500';
        }
        if (strpos($Professionalism, 'progressing') !== false) {
            $prof[]      = 'g.Production/g.GardenHaUnCertified BETWEEN 500 AND 1000';
        }
        if (strpos($Professionalism, 'professional') !== false) {
            $prof[]      = 'g.Production/g.GardenHaUnCertified > 1000';
        }
        $prof = implode(' OR ', $prof);
        if (!empty($prof)) {
            $where .= ' AND ('.$prof.')';
        }
        $sql = str_replace('--where--', $where, $sql);
        $params[] = $radius;
        // echo '<pre>'; print_r($sql); echo '</pre>'; exit;
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }

    public function getGeospatialFarmerCertified($lat, $lng, $radius)
    {
        $sql  = "SELECT
    g.FarmerID,
    g.GardenNr,
    g.SurveyNr,
    g.Latitude AS lat,
    g.Longitude AS lng,
    ( 6371 * ACOS( COS( RADIANS(?) ) * COS( RADIANS( g.Latitude) )
    * COS( RADIANS(g.Longitude) - RADIANS(?)) + SIN(RADIANS(?))
    * SIN( RADIANS(g.Latitude)))) AS distance
FROM ktv_farmer_garden g
JOIN ktv_farmer f ON f.FarmerID = g.FarmerID AND f.StatusCode = 'active'
JOIN (
SELECT
    c.FarmerID,c.GardenNr,MAX(c.SurveyNr) AS SurveyNr
FROM ktv_certification c
GROUP BY c.FarmerID,c.GardenNr
) z ON g.FarmerID = z.FarmerID AND g.GardenNr = z.GardenNr AND g.SurveyNr = z.SurveyNr
HAVING distance < ?
        ";
        $query = $this->db->query($sql, array($lat, $lng, $lat, $radius));
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }

    public function getGeospatialNursery($lat, $lng, $radius)
    {
        $sql  = "SELECT
    kcn.NurseryID AS id
    ,kcn.Latitude AS lat
    ,kcn.Longitude AS lng
    ,( 6371 * ACOS( COS( RADIANS(?) ) * COS( RADIANS( kcn.Latitude) )
    * COS( RADIANS(kcn.Longitude) - RADIANS(?)) + SIN(RADIANS(?))
    * SIN( RADIANS(kcn.Latitude)))) AS distance
FROM ktv_nursery kcn
HAVING distance < ?
        ";
        $query = $this->db->query($sql, array($lat, $lng, $lat, $radius));
        // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }

    public function getGeospatialDemoplot($lat, $lng, $radius)
    {
        $sql  = "SELECT
    kc.CPGid AS id
    ,kcfg.Latitude AS lat
    ,kcfg.Longitude AS lng
    ,( 6371 * ACOS( COS( RADIANS(?) ) * COS( RADIANS( kcfg.Latitude) )
    * COS( RADIANS(kcfg.Longitude) - RADIANS(?)) + SIN(RADIANS(?))
    * SIN( RADIANS(kcfg.Latitude)))) AS distance
FROM ktv_cpg_batch_trainings kcbt
LEFT JOIN ktv_cpg kc ON kcbt.CPGid=kc.CPGid
LEFT JOIN ktv_farmer kcf ON kcf.FarmerID=kcbt.DemoplotOwnerID
LEFT JOIN (
      SELECT a.FarmerID,max(SurveyNr) as survey from `ktv_farmer_garden` a where GardenNr=1 group by FarmerID
   ) kcfgt ON kcfgt.FarmerID = kcf.FarmerID
LEFT JOIN ktv_farmer_garden kcfg ON kcfgt.FarmerID=kcfg.FarmerID and kcfg.GardenNr=1 and kcfg.SurveyNr=kcfgt.survey
HAVING distance < ?
        ";
        $query = $this->db->query($sql, array($lat, $lng, $lat, $radius));
        // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }

    public function getGeospatialFarmerOrganization($lat, $lng, $radius)
    {
        $sql  = "SELECT
    kc.`CoopID` AS id
    ,kc.Latitude AS lat
    ,kc.Longitude AS lng
    ,( 6371 * ACOS( COS( RADIANS(?) ) * COS( RADIANS(kc.Latitude) )
    * COS( RADIANS(kc.Longitude) - RADIANS(?)) + SIN(RADIANS(?))
    * SIN( RADIANS(kc.Latitude)))) AS distance
FROM ktv_cooperatives kc
HAVING distance < ?
        ";
        $query = $this->db->query($sql, array($lat, $lng, $lat, $radius));
        // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }

    public function getGeospatialWarehouse($lat, $lng, $radius)
    {
        $sql  = "SELECT
    kc.`WarehouseID` AS id
    ,kc.Latitude AS lat
    ,kc.Longitude AS lng
    ,( 6371 * ACOS( COS( RADIANS(?) ) * COS( RADIANS(kc.Latitude) )
    * COS( RADIANS(kc.Longitude) - RADIANS(?)) + SIN(RADIANS(?))
    * SIN( RADIANS(kc.Latitude)))) AS distance
FROM ktv_warehouse kc
HAVING distance < ?
        ";
        $query = $this->db->query($sql, array($lat, $lng, $lat, $radius));
        // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }

    public function getGeospatialTrader($lat, $lng, $radius)
    {
        $sql  = "SELECT
    kc.`TraderID` AS id
    ,kc.Latitude AS lat
    ,kc.Longitude AS lng
    ,( 6371 * ACOS( COS( RADIANS(?) ) * COS( RADIANS(kc.Latitude) )
    * COS( RADIANS(kc.Longitude) - RADIANS(?)) + SIN(RADIANS(?))
    * SIN( RADIANS(kc.Latitude)))) AS distance
FROM ktv_traders kc
HAVING distance < ?
        ";
        $query = $this->db->query($sql, array($lat, $lng, $lat, $radius));
        // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }

    public function getFarmer($FarmerID,$GardenNr,$SurveyNr)
    {
        $sql = "SELECT
        kcf.FarmerID,
        kcfg.GardenNr,
        kcfg.`SurveyNr`,
        kcf.FarmerName,
        kcf.Address,
        kv.Village,
        ks.SubDistrict,
        kcfg.GardenHaUnCertified,
        (kcfg.PohonTM+kcfg.PohonTBM+kcfg.PohonRehab) AS Pohon,
        (IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+(IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
        (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0)) AS Produksi,
        ((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
        (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
        (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0)))/
        IFNULL(GardenHaUnCertified,0) as Produktivitas
        ,IF(kcf.Photo!='',kcf.Photo,'no-user.jpg') as Photo
FROM `ktv_farmer_garden` kcfg
INNER JOIN `ktv_farmer` kcf ON kcfg.FarmerID = kcf.FarmerID
LEFT JOIN ktv_village kv ON kv.VillageID=kcf.VillageID
LEFT JOIN ktv_subdistrict ks ON kv.SubDistrictID = ks.SubDistrictID
LEFT JOIN ktv_district kd ON ks.DistrictID = kd.DistrictID
WHERE
    1 = 1
    AND kcf.FarmerID = ?
    AND kcfg.GardenNr = ?
    AND kcfg.SurveyNr = ?
        ";
        $query = $this->db->query($sql, array($FarmerID,$GardenNr,$SurveyNr));
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
    }

    public function getNursery($id)
    {
        $sql = "SELECT
    kc.CPGid
    ,kc.GroupName,kcn.Established,kcn.Panjang,kcn.Lebar
    ,kcn.Latitude,kcn.Longitude
    ,kcf.FarmerName,kcn.Kapasitas
FROM ktv_nursery kcn
LEFT JOIN ktv_cpg kc ON kcn.ObjID=kc.CPGid and ObjType='cpg'
LEFT JOIN ktv_farmer kcf ON kcf.FarmerID=kcn.Responsible
WHERE kcn.NurseryID = ?
        ";
        $query = $this->db->query($sql, array($id));
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
    }

    public function getDemoplot($id)
    {
        $sql = "SELECT
    kc.CPGid
    ,kc.GroupName
    ,kcfg.GardenNr
    ,kcfg.Latitude,kcfg.Longitude
    ,kcf.FarmerName,kcf.FarmerID,kcfg.GardenHaUnCertified,
    (kcfg.PohonTM+kcfg.PohonTBM+kcfg.PohonRehab) Pohon,
    (IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+(IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
    (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0)) AS totalProduksi,
    ((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
    (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
    (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0)))/
    IFNULL(GardenHaUnCertified,0) as Produktivitas
FROM ktv_cpg_batch_trainings kcbt
LEFT JOIN ktv_cpg kc ON kcbt.CPGid=kc.CPGid
LEFT JOIN ktv_farmer kcf ON kcf.FarmerID=kcbt.DemoplotOwnerID
LEFT JOIN (
      SELECT a.FarmerID,max(SurveyNr) as survey from `ktv_farmer_garden` a where GardenNr=1 group by FarmerID
   ) kcfgt ON kcfgt.FarmerID = kcf.FarmerID
LEFT JOIN ktv_farmer_garden kcfg ON kcfgt.FarmerID=kcfg.FarmerID and kcfg.GardenNr=1 and kcfg.SurveyNr=kcfgt.survey
WHERE kc.CPGid = ?
        ";
        $query = $this->db->query($sql, array($id));
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
    }

    public function getFarmerOrganization($id)
    {
        $sql = "SELECT
    kc.CoopID AS id
    ,CoopName
    ,kc.Latitude,kc.Longitude
    ,Village,SubDistrict,IFNULL(FarmerName,StaffName) as StaffName
FROM ktv_cooperatives kc
LEFT JOIN ktv_village kv ON kc.VillageID=kv.VillageID
LEFT JOIN ktv_subdistrict ks ON kv.SubDistrictID=ks.SubDistrictID
LEFT JOIN ktv_cooperative_staff kcs ON kc.CoopID=kcs.CoopID and Position='ketua'
LEFT JOIN ktv_farmer kcf ON kcs.FarmerID=kcf.FarmerID
WHERE kc.CoopID = ?
        ";
        $query = $this->db->query($sql, array($id));
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
    }

    public function getWarehouse($id)
    {
        $sql = "SELECT
    kc.`WarehouseID`
    ,WarehouseName CoopName
    ,kc.Latitude Latitude
    ,kc.Longitude Longitude
    ,Village,SubDistrict,StaffName
FROM ktv_warehouse kc
LEFT JOIN ktv_village kv ON kc.VillageID=kv.VillageID
LEFT JOIN ktv_subdistrict ks ON kv.SubDistrictID=ks.SubDistrictID
LEFT JOIN ktv_warehouse_staff kcs ON kc.WarehouseID=kcs.WarehouseID and Position='pemilik'
WHERE kc.WarehouseID = ?
        ";
        $query = $this->db->query($sql, array($id));
        // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
    }

    public function getTrader($id)
    {
        $sql = "SELECT
    kc.`TraderID`
    ,TraderName CoopName
    ,kc.Latitude Latitude
    ,kc.Longitude Longitude
    ,Village,SubDistrict,StaffName
FROM ktv_traders kc
LEFT JOIN ktv_village kv ON kc.VillageID=kv.VillageID
LEFT JOIN ktv_subdistrict ks ON kv.SubDistrictID=ks.SubDistrictID
LEFT JOIN ktv_trader_staff kcs ON kc.TraderID=kcs.TraderID and Position='pemilik'
WHERE kc.TraderID = ?
        ";
        $query = $this->db->query($sql, array($id));
        // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
    }

    public function processApproval($FarmerID, $ApprovalStatus, $StatusNotes, $LoanAmount)
    {
        $sql = "INSERT INTO ktv_farmer_summary (
    FarmerID
    , ApprovalStatus
    , StatusNotes
    , LoanAmount
    , DateCreated
    , CreatedBy
    , DateUpdated
    , UpdatedBy
)
VALUES
    (
        ?
        , ?
        , ?
        , ?
        , NOW()
        , ?
        , NOW()
        , ?
    )
ON DUPLICATE KEY UPDATE
    ApprovalStatus = VALUES(ApprovalStatus),
    StatusNotes = VALUES(StatusNotes),
    LoanAmount = VALUES(LoanAmount),
    DateUpdated = NOW(),
    UpdatedBy = VALUES(CreatedBy)
        ";
        $this->db->trans_start(FALSE);
        foreach ($FarmerID as $key => $value) {
            $result = $this->db->query($sql, array($value, $ApprovalStatus, $StatusNotes, $LoanAmount, $_SESSION['userid'], $_SESSION['userid']));
            // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
        }
        $this->db->trans_complete();
        // $this->db->trans_rollback();
        // exit;
        return $result;
    }

    public function processFinalization($FarmerID, $LoanAmount)
    {
        return $this->db->update('ktv_farmer_summary', array('LoanAmount' => $LoanAmount, 'ApprovalStatus' => '2'), array('FarmerID' => $FarmerID));
    }

    public function getDetail($FarmerID)
    {
        $sql = "SELECT
    fs.ApprovalStatus,
    fs.StatusNotes,
    fs.LoanAmount,
    u.UserRealName AS UpdatedBy,
    fs.DateUpdated
FROM
    ktv_farmer_summary fs
LEFT JOIN sys_user u ON u.UserId = fs.UpdatedBy
WHERE
    fs.FarmerID = ?
        ";
        $query = $this->db->query($sql, array($FarmerID));
        if ($query->num_rows()>0) {
            return $query->row_array(0);
        }
        return false;
    }

    public function getNewBankBasicDataForm($BankID){
    	$sql="SELECT 
				a.BankID as 'Koltiva.view.Basic.NewBank.MainForm-FormBasicData-BankID',
				a.BankCode as 'Koltiva.view.Basic.NewBank.MainForm-FormBasicData-BankCode',
				a.BankName as 'Koltiva.view.Basic.NewBank.MainForm-FormBasicData-BankName',
				a.BankDesc as 'Koltiva.view.Basic.NewBank.MainForm-FormBasicData-BankDesc'
			FROM 
				`ktv_bank` as a 
			WHERE 
				a.BankID = ?";
		$query = $this->db->query($sql, array((int) $BankID));
        $data = $query->row_array();

        $return['success'] = true;
        $return['data'] = $data;

        return $return;
    }

}

/* End of file mregion.php */
/* Location: ./application/models/mregion.php */