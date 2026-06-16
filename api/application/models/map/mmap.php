<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mmap extends CI_Model {

	private $sql;

	public function __construct()
	{
		parent::__construct();
		
		$this->sqlHakAkses = "";
        $this->sqlHakAksesMill = "";
        if ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
			$this->sqlHakAkses = " INNER JOIN ktv_access_partner_member acc_pm ON f.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$_SESSION['PartnerID']}' ";
			$this->sqlHakAksesAgent = " INNER JOIN ktv_access_partner_member acc_pm ON m.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$_SESSION['PartnerID']}' ";
            $this->sqlHakAksesMill = " INNER JOIN ktv_access_partner_mill acc_pmi ON m.MillID = acc_pmi.apmiMillID AND acc_pmi.apmiPartnerID = '{$_SESSION['PartnerID']}' ";
		}
		
		$this->sql['kml'] = "SELECT 
				ID
				, `Name`
				, CONCAT(`Name`,' - ',p.Province) NameBoundary
				, FileName
				, Color
				-- , FilePath
			FROM
				ktv_kml kml
			LEFT JOIN ktv_province p ON p.`ProvinceID` = kml.`ProvinceID`
			LEFT JOIN ktv_district d ON d.`DistrictID` = kml.`DistrictID`
			WHERE
				kml.StatusCode != 'nullified'
				-- where --
			ORDER BY `Name`
		";

		$this->sql['farmer'] = "SELECT
				f.MemberID AS ID
				, f.MemberID
				, f.MemberName AS Name
				, f.MemberDisplayID
				, f.Address
				, p.Province
				, p.ProvinceID
				, d.District
				, d.DistrictID
				, sd.SubDistrict
				, v.Village
				, IF(f.Photo!='',f.Photo,'no-user.jpg') AS Photo
				, g.PlotNr AS GardenNr
				, g.SurveyNr
				, g.GardenAreaHa AS AreaHa
				, ps.AnnualProduction AS Production
				, IFNULL(ST_Latitude(g.LatLong), g.Latitude) Latitude
				, IFNULL(ST_Longitude(g.LatLong), g.Longitude) Longitude
				, IFNULL(g.FarmAge,0) AS FarmAge
			FROM (
				SELECT
					f.*
				FROM ktv_members f
				JOIN ktv_member_role mr ON mr.`MemberID` = f.`MemberID` -- AND mr.`MRoleID` = 1
				-- where_hakakses --
				WHERE
					f.StatusCode = 'active'
					-- where --
			) f
			JOIN (
				SELECT
					g.*, g.AverageAgeTree AS FarmAge
				FROM ktv_survey_plot g
				JOIN (SELECT g.MemberID, g.PlotNr, MAX(g.SurveyNr) AS SurveyNr FROM ktv_survey_plot g GROUP BY g.MemberID, g.PlotNr) z ON g.MemberID = z.MemberID AND g.PlotNr = z.PlotNr AND g.SurveyNr = z.SurveyNr
				WHERE 1 = 1
					AND g.StatusCode = 'active' 
					AND (ABS(g.`Latitude`) > 0 AND ABS(g.`Longitude`) > 0 or ST_Latitude(g.LatLong) IS NOT NULL AND ST_Longitude(g.LatLong) IS NOT NULL)
			) g ON f.MemberID = g.MemberID
			LEFT JOIN ktv_survey_plot_status ps ON ps.MemberID = g.MemberID AND ps.PlotNr = g.PlotNr
			JOIN ktv_village v ON v.VillageID = g.VillageID
			JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
			JOIN ktv_district d ON d.DistrictID = sd.DistrictID
			JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
			WHERE 1 = 1
			-- where_garden --
			-- group_by_member --
		";

		$this->sql['farm_area_table'] = "SELECT
				f.MemberID AS ID
				, f.MemberID
				, f.MemberName AS Name
				, f.MemberDisplayID
				, f.Address
				, p.Province
				, p.ProvinceID
				, d.District
				, d.DistrictID
				, sd.SubDistrict
				, v.Village
				, IF(f.Photo!='',f.Photo,'no-user.jpg') AS Photo
				, g.PlotNr AS GardenNr
				, g.SurveyNr
				, g.GardenAreaHa AS AreaHa
				, ps.AnnualProduction AS Production
				, IFNULL(g.Latitude, ST_Latitude(g.LatLong)) Latitude
				, IFNULL(g.Longitude, ST_Longitude(g.LatLong)) Longitude
				, IFNULL(g.FarmAge,0) AS FarmAge
			FROM (
				SELECT
					f.*
				FROM ktv_members f
				JOIN ktv_member_role mr ON mr.`MemberID` = f.`MemberID` -- AND mr.`MRoleID` = 1
				-- where_hakakses --
				WHERE
					f.StatusCode = 'active'
					-- where --
			) f
			JOIN (
				SELECT
					g.*, g.AverageAgeTree AS FarmAge
				FROM ktv_survey_plot g
				JOIN (SELECT g.MemberID, g.PlotNr, MAX(g.SurveyNr) AS SurveyNr FROM ktv_survey_plot g GROUP BY g.MemberID, g.PlotNr) z ON g.MemberID = z.MemberID AND g.PlotNr = z.PlotNr AND g.SurveyNr = z.SurveyNr
				WHERE 1 = 1
					AND g.StatusCode = 'active' 
					AND (ABS(g.`Latitude`) > 0 AND ABS(g.`Longitude`) > 0 or ST_Latitude(g.LatLong) IS NOT NULL AND ST_Longitude(g.LatLong) IS NOT NULL)
			) g ON f.MemberID = g.MemberID
			LEFT JOIN ktv_survey_plot_status ps ON ps.MemberID = g.MemberID AND ps.PlotNr = g.PlotNr
			JOIN ktv_village v ON v.VillageID = g.VillageID
			JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
			JOIN ktv_district d ON d.DistrictID = sd.DistrictID
			JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
			WHERE 1 = 1
			-- where_garden --
			-- group_by_member --
		";

		$this->sql['farmer_bluenumber'] = "SELECT
				f.MemberID AS ID
				, f.MemberName AS Name
				, f.MemberDisplayID
				, f.Address
				, p.Province
				, p.ProvinceID
				, d.District
				, sd.SubDistrict
				, v.Village
				, IF(f.Photo!='',f.Photo,'no-user.jpg') AS Photo
				, g.PlotNr AS GardenNr
				, g.SurveyNr
				, g.GardenAreaHa AS AreaHa
				, ps.AnnualProduction AS Production
				, g.Latitude
				, g.Longitude
				, IFNULL(g.FarmAge,0) AS FarmAge
			FROM (
				SELECT
					f.*
					, p.Province
					, p.ProvinceID
					, d.District
					, sd.SubDistrict
					, v.Village
				FROM ktv_members f
				JOIN ktv_member_role mr ON mr.`MemberID` = f.`MemberID` -- AND mr.`MRoleID` = 1
				-- where_hakakses --
				JOIN (
					SELECT
						a.`MemberID`
					FROM
						ktv_survey_sdg a
					WHERE
						a.`StatusCode` = 'active'
						AND a.StatusVerified = 'Yes'
					GROUP BY a.`MemberID`
				) AS sdg ON f.MemberID = sdg.MemberID
				WHERE
					f.StatusCode = 'active'
					-- where --
			) f
			JOIN (
				SELECT
					g.*, g.AverageAgeTree AS FarmAge
				FROM ktv_survey_plot g
				JOIN (SELECT g.MemberID, g.PlotNr, MAX(g.SurveyNr) AS SurveyNr FROM ktv_survey_plot g GROUP BY g.MemberID, g.PlotNr) z ON g.MemberID = z.MemberID AND g.PlotNr = z.PlotNr AND g.SurveyNr = z.SurveyNr
				WHERE 1 = 1
					AND g.StatusCode = 'active'
					AND (ABS(g.`Latitude`) > 0 AND ABS(g.`Longitude`) > 0 or ST_Latitude(g.LatLong) IS NOT NULL AND ST_Longitude(g.LatLong) IS NOT NULL)
			) g ON f.MemberID = g.MemberID
			LEFT JOIN ktv_survey_plot_status ps ON ps.MemberID = g.MemberID AND ps.PlotNr = g.PlotNr
			JOIN ktv_village v ON v.VillageID = g.VillageID
			JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
			JOIN ktv_district d ON d.DistrictID = sd.DistrictID
			JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
			WHERE 1 = 1
				-- where_garden --
			-- group_by_member --
		";
	}

	public function getCovidRisk($ProvinceID,$DistrictID){
		$where  = '';
		$params = [];
		$where .= "AND CategoryID = 8";
		// if (!empty($ProvinceID)) {
		// 	$where .= " AND kml.ProvinceID = ?";
		// 	$params[] = intval($ProvinceID);
		// }
		// if (!empty($DistrictID)) {
		// 	$where .= " AND kml.DistrictID IN ($DistrictID)";
		// 	// $params[] = intval($DistrictID);
		// } else {
		// 	// $where .= " AND kml.DistrictID = 0";
		// }
		$sql = str_replace("-- where --", $where, $this->sql['kml']);
		$query = $this->db->query($sql, $params);

		// echo "<pre>";
		// echo $this->db->last_query();
		// die;
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
		return false;
	}

	public function getRestrictedArea($ProvinceID, $DistrictID)
	{
		$where  = '';
		$params = [];
		$where .= "AND CategoryID = 3";
		if (!empty($ProvinceID)) {
			$where .= " AND kml.ProvinceID = ?";
			$params[] = intval($ProvinceID);
		}
		if (!empty($DistrictID)) {
			$where .= " AND kml.DistrictID IN ($DistrictID)";
			// $params[] = intval($DistrictID);
		} else {
			$where .= " AND kml.DistrictID = 0";
		}
		$sql = str_replace("-- where --", $where, $this->sql['kml']);
		$query = $this->db->query($sql, $params);
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
		return false;
	}

	public function getSafeArea($ProvinceID, $DistrictID)
	{
		$where  = '';
		$params = [];
		$where .= "AND CategoryID = 4";
		if (!empty($ProvinceID)) {
			$where .= " AND kml.ProvinceID = ?";
			$params[] = intval($ProvinceID);
		}
		if (!empty($DistrictID)) {
			$where .= " AND kml.DistrictID IN ($DistrictID)";
			// $params[] = intval($DistrictID);
		} else {
			$where .= " AND kml.DistrictID = 0";
		}
		$sql = str_replace("-- where --", $where, $this->sql['kml']);
		$query = $this->db->query($sql, $params);
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
		return false;
	}

	public function getBufferZone($ProvinceID, $DistrictID)
	{
		$where  = '';
		$params = [];
		$where .= "AND CategoryID = 7";
		if (!empty($ProvinceID)) {
			$where .= " AND kml.ProvinceID = ?";
			$params[] = intval($ProvinceID);
		}
		if (!empty($DistrictID)) {
			$where .= " AND kml.DistrictID IN ($DistrictID)";
			// $params[] = intval($DistrictID);
		} else {
			$where .= " AND kml.DistrictID = 0";
		}
		$sql = str_replace("-- where --", $where, $this->sql['kml']);
		$query = $this->db->query($sql, $params);
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
		return false;
	}

	public function getAdministrativeArea($ProvinceID = null, $DistrictID = null)
	{
		$where  = '';
		$params = [];
		$where .= "AND CategoryID = 2";
		if (!empty($ProvinceID)) {
			$where .= " AND kml.ProvinceID = ?";
			$params[] = intval($ProvinceID);
		}
		if (!empty($DistrictID)) {
			$where .= " AND kml.DistrictID IN ($DistrictID)";
			// $params[] = intval($DistrictID);
		} else {
			$where .= " AND kml.DistrictID = 0";
		}
		$sql = str_replace("-- where --", $where, $this->sql['kml']);
		$query = $this->db->query($sql, $params);
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
		return false;
	}

	public function getLandCover($ProvinceID, $DistrictID)
	{
		$where  = '';
		$params = [];
		$where .= "AND CategoryID = 5";
		if (!empty($ProvinceID)) {
			$where .= " AND kml.ProvinceID = ?";
			$params[] = intval($ProvinceID);
		}
		if (!empty($DistrictID)) {
			$where .= " AND kml.DistrictID IN ($DistrictID)";
			// $params[] = intval($DistrictID);
		} else {
			$where .= " AND kml.DistrictID = 0";
		}
		$sql = str_replace("-- where --", $where, $this->sql['kml']);
		$query = $this->db->query($sql, $params);
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
		return false;
	}

	public function getAnimalHabitat($ProvinceID, $DistrictID)
	{
		$where  = '';
		$params = [];
		$where .= "AND CategoryID = 6";
		if (!empty($ProvinceID)) {
			$where .= " AND kml.ProvinceID = ?";
			$params[] = intval($ProvinceID);
		}
		if (!empty($DistrictID)) {
			$where .= " AND kml.DistrictID IN ($DistrictID)";
			// $params[] = intval($DistrictID);
		} else {
			$where .= " AND kml.DistrictID = 0";
		}
		$sql = str_replace("-- where --", $where, $this->sql['kml']);
		$query = $this->db->query($sql, $params);
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
		return false;
	}

	public function getFarmersSME($MemberID = ''){
		$sql = "SELECT
				f.MemberID AS ID
				, f.MemberName AS Name
				, f.MemberDisplayID
				, f.Address
				, p.Province
				, p.ProvinceID
				, d.District
				, d.DistrictID
				, sd.SubDistrict
				, v.Village
				, IF(f.Photo!='',f.Photo,'no-user.jpg') AS Photo
				, g.PlotNr AS GardenNr
				, g.SurveyNr
				, g.GardenAreaHa AS AreaHa
				, ps.AnnualProduction AS Production
				, g.Latitude
				, g.Longitude
				, IFNULL(g.FarmAge,0) AS FarmAge
			FROM (
				SELECT
					f.*
				FROM ktv_members f
				JOIN ktv_member_role mr ON mr.`MemberID` = f.`MemberID` -- AND mr.`MRoleID` = 1
				-- where_hakakses --
				WHERE
					f.StatusCode = 'active'
					-- where --
			) f
			JOIN (
				SELECT
					g.*, g.AverageAgeTree AS FarmAge
				FROM ktv_survey_plot g
				JOIN (SELECT g.MemberID, g.PlotNr, MAX(g.SurveyNr) AS SurveyNr FROM ktv_survey_plot g GROUP BY g.MemberID, g.PlotNr) z ON g.MemberID = z.MemberID AND g.PlotNr = z.PlotNr AND g.SurveyNr = z.SurveyNr
				WHERE 1 = 1
					AND g.StatusCode = 'active'
					AND (ABS(g.`Latitude`) > 0 AND ABS(g.`Longitude`) > 0 or ST_Latitude(g.LatLong) IS NOT NULL AND ST_Longitude(g.LatLong) IS NOT NULL)
			) g ON f.MemberID = g.MemberID
			LEFT JOIN ktv_survey_plot_status ps ON ps.MemberID = g.MemberID AND ps.PlotNr = g.PlotNr
			JOIN ktv_village v ON v.VillageID = g.VillageID
			JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
			JOIN ktv_district d ON d.DistrictID = sd.DistrictID
			JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
			INNER JOIN ktv_members_relation mr on mr.MemberID = f.MemberID
			WHERE 1 = 1
			AND mr.ObjID = ?
				-- where_garden --
			-- group_by_member --
			";

		$query = $this->db->query($sql,array($MemberID));
		
		$DataReturn = $query->result_array();
		$DataReturn[0]['petani_realcount'] = $query->num_rows();
		
		return $DataReturn;
	}

	public function getFarmers($ProvinceID, $DistrictID, $key = '', $FarmAge = '', $PartnerID = '')
	{
        $this->load->library('awsfileupload');
		$where  = '';
		$where_garden = '';
		$params = [];
		if (!empty($ProvinceID)) {
			$where_garden .= " AND p.ProvinceID = ?";
			$params[] = intval($ProvinceID);
		}
		if (!empty($DistrictID)) {
			$where_garden .= " AND d.DistrictID = ?";
			$params[] = intval($DistrictID);
		}
		if (!empty($key)) {
			// $where_garden .= " AND (MemberName LIKE '%{$key}%' OR f.MemberDisplayID LIKE '%{$key}%')";
			$where_garden .= " AND f.MemberDisplayID = '{$key}'";
		}
		
		if (isset($FarmAge) && $FarmAge != '') {
			switch ($FarmAge) {
				case '0':
					$where_garden = " AND FarmAge BETWEEN 0 AND 4";
					break;
				case '4':
					$where_garden = " AND FarmAge BETWEEN 5 AND 8";
					break;
				case '8':
					$where_garden = " AND FarmAge BETWEEN 9 AND 18";
					break;
				case '18':
					$where_garden = " AND FarmAge > 18";
					break;
			}
		}

		//Pengecekan Hak Akses
		$where_hakakses = "";
		if (!empty($PartnerID)) {
			$where_hakakses = " JOIN `ktv_access_partner_member` apm ON apm.apmMemberID = f.`MemberID` AND apm.apmPartnerID IN ({$PartnerID}) ";
		} elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program") {
			if($_SESSION["PartnerAsParent"] == "Yes"){
				($PartnerID != '')?$PartnerID = $PartnerID: $PartnerID = $_SESSION['PartnerChild'];
			
				$where_hakakses = " JOIN `ktv_access_partner_member` apm ON apm.apmMemberID = f.`MemberID` AND apm.apmPartnerID IN ({$PartnerID})";
				$where = " AND d.DistrictID IN (".$_SESSION['daerah_access'].")";	
			}else{
				$where_hakakses = " JOIN `ktv_access_partner_member` apm ON apm.apmMemberID = f.`MemberID` AND apm.apmPartnerID = '{$_SESSION['PartnerID']}' ";
				$where = " AND d.DistrictID IN (".$_SESSION['daerah_access'].")";
			}
		}

		$sql = str_replace("-- where --", $where, $this->sql['farmer']);
		$sql = str_replace("-- where_garden --", $where_garden, $sql);
		$sql = str_replace("-- where_hakakses --", $where_hakakses, $sql);
		$query = $this->db->query($sql, $params);
		// echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;

		if ($query->num_rows() > 0) {
			$DataReturn = $query->result_array();

			//Cari petani_realcount
			$sql_count = str_replace("-- where --", $where, $this->sql['farmer']);
			$sql_count = str_replace("-- where_garden --", $where_garden, $sql_count);
			$sql_count = str_replace("-- where_hakakses --", $where_hakakses, $sql_count);
			$sql_count = str_replace("-- group_by_member --", " GROUP BY f.MemberID ", $sql_count);
			$query = $this->db->query($sql_count, $params);

			foreach($DataReturn as $key => $val){

				if($this->awsfileupload->doesObjectExist($val['Photo']) == true) {
					$DataReturn[$key]['Photo'] = $this->config->item('CTCDN')."/".$val['Photo'];
				}else{
					$DataReturn[$key]['Photo'] = base_url().'/images/member/'.$val['Province'].'/'.$val['Photo'];
				}
	
			}
			//echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
			$DataReturn[0]['petani_realcount'] = $query->num_rows();

			return $DataReturn;
		}
		return false;
	}

	public function getFarmersBlueNumber($ProvinceID, $DistrictID, $key = '', $FarmAge = '') {
		$where  = '';
		$params = [];
		if (!empty($ProvinceID)) {
			$where .= " AND p.ProvinceID = ?";
			$params[] = intval($ProvinceID);
		}
		if (!empty($DistrictID)) {
			$where .= " AND d.DistrictID = ?";
			$params[] = intval($DistrictID);
		}
		$where_garden = '';
		if (!empty($key)) {
			// $where_garden .= " AND (MemberName LIKE '%{$key}%' OR f.MemberDisplayID LIKE '%{$key}%')";
			$where_garden .= " AND f.MemberDisplayID = '{$key}'";
		}
		
		if (isset($FarmAge) && $FarmAge != '') {
			switch ($FarmAge) {
				case '0':
					$where_garden = " AND FarmAge BETWEEN 0 AND 4";
					break;
				case '4':
					$where_garden = " AND FarmAge BETWEEN 5 AND 8";
					break;
				case '8':
					$where_garden = " AND FarmAge BETWEEN 9 AND 18";
					break;
				case '18':
					$where_garden = " AND FarmAge > 18";
					break;
			}
		}

		$sql = str_replace("-- where --", $where, $this->sql['farmer_bluenumber']);
		$sql = str_replace("-- where_garden --", $where_garden, $sql);
		$query = $this->db->query($sql, $params);
		// echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
		if ($query->num_rows() > 0) {
			$DataReturn = $query->result_array();

			//Cari petani_realcount
			$sql_count = str_replace("-- where --", $where, $this->sql['farmer_bluenumber']);
			$sql_count = str_replace("-- where_garden --", $where_garden, $sql_count);
			$sql_count = str_replace("-- group_by_member --", " GROUP BY f.MemberID ", $sql_count);
			$query = $this->db->query($sql_count, $params);
			$DataReturn[0]['petani_realcount'] = $query->num_rows();

			return $DataReturn;
		}
		return false;
	}

	public function getFarmersGroup($ProvinceID, $DistrictID, $key = '', $FarmAge = '', $PartnerID){
		$where  = '';
		$where_garden = '';
		$params = [];
		if (!empty($ProvinceID)) {
			$where_garden .= " AND p.ProvinceID = ?";
			$params[] = intval($ProvinceID);
		}
		if (!empty($DistrictID)) {
			$where_garden .= " AND d.DistrictID = ?";
			$params[] = intval($DistrictID);
		}
		if (!empty($key)) {
			// $where_garden .= " AND (MemberName LIKE '%{$key}%' OR f.MemberDisplayID LIKE '%{$key}%')";
			$where_garden .= " AND f.MemberDisplayID = '{$key}'";
		}
		
		if (isset($FarmAge) && $FarmAge != '') {
			switch ($FarmAge) {
				case '0':
					$where_garden = " AND FarmAge BETWEEN 0 AND 4";
					break;
				case '4':
					$where_garden = " AND FarmAge BETWEEN 5 AND 8";
					break;
				case '8':
					$where_garden = " AND FarmAge BETWEEN 9 AND 18";
					break;
				case '18':
					$where_garden = " AND FarmAge > 18";
					break;
			}
		}

		//Pengecekan Hak Akses
		$where_hakakses = "";
		if (!empty($PartnerID)) {
			$where_hakakses = " JOIN `ktv_access_partner_member` apm ON apm.apmMemberID = f.`MemberID` AND apm.apmPartnerID IN ({$PartnerID}) ";
		} elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program") {
			if($_SESSION["PartnerAsParent"] == "Yes"){
				($PartnerID != '')?$PartnerID = $PartnerID: $PartnerID = $_SESSION['PartnerChild'];
			
				$where_hakakses = " JOIN `ktv_access_partner_member` apm ON apm.apmMemberID = f.`MemberID` AND apm.apmPartnerID IN ({$PartnerID})";
				$where = " AND d.DistrictID IN (".$_SESSION['daerah_access'].")";	
			}else{
				$where_hakakses = " JOIN `ktv_access_partner_member` apm ON apm.apmMemberID = f.`MemberID` AND apm.apmPartnerID = '{$_SESSION['PartnerID']}' ";
				$where = " AND d.DistrictID IN (".$_SESSION['daerah_access'].")";
			}
		}

		$sql_count = str_replace("-- where --", $where, $this->sql['farmer']);
		$sql_count = str_replace("-- where_garden --", $where_garden, $sql_count);
		$sql_count = str_replace("-- where_hakakses --", $where_hakakses, $sql_count);
		$sql_count = str_replace("-- group_by_member --", " GROUP BY f.MemberID ", $sql_count);
		$query = $this->db->query($sql_count, $params);		
		// echo "<pre>"; print_r($this->db->last_query()); echo "</pre>";exit;
		if ($query->num_rows() > 0) {
			$DataReturn = $query->result_array();
			return $DataReturn;
		}
		return false;
	}

	public function getFarmersCertified($ProvinceID, $DistrictID, $key = '')
	{
		$where  = ' AND isCertified = 1';
		$params = [];
		if (!empty($ProvinceID)) {
			$where .= " AND p.ProvinceID = ?";
			$params[] = intval($ProvinceID);
		}
		if (!empty($DistrictID)) {
			$where .= " AND d.DistrictID = ?";
			$params[] = intval($DistrictID);
		}
		if (!empty($key)) {
			// $where .= " AND (MemberName LIKE '%{$key}%' OR f.MemberDisplayID LIKE '%{$key}%')";
			$where .= " AND f.MemberDisplayID = '{$key}'";
		}
		$where_garden = '';
		if (!empty($key)) {
			// $where_garden .= " AND (MemberName LIKE '%{$key}%' OR f.MemberDisplayID LIKE '%{$key}%')";
			$where_garden .= " AND f.MemberDisplayID = '{$key}'";
		}
		if (isset($FarmAge) && $FarmAge != '') {
			switch ($FarmAge) {
				case '0':
					$where_garden = " AND FarmAge BETWEEN 0 AND 4";
					break;
				case '4':
					$where_garden = " AND FarmAge BETWEEN 5 AND 8";
					break;
				case '8':
					$where_garden = " AND FarmAge BETWEEN 9 AND 18";
					break;
				case '18':
					$where_garden = " AND FarmAge > 18";
					break;
			}
		}
		$sql = str_replace("-- where --", $where, $this->sql['farmer']);
		$sql = str_replace("-- where_garden --", $where_garden, $sql);
		$query = $this->db->query($sql, $params);
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
		return false;
	}

	public function getSMESTA($jml,$ProvinceID, $DistrictID, $key = '', $PartnerID){
		$where  = '';
		$params = [];
		if($jml == "500"){
			$where .= " AND sr.jml_petani > 500";
		}
		if($jml == "200"){
			$where .= " AND sr.jml_petani <= 500 AND sr.jml_petani >= 200";
		}
		if($jml == "100"){
			$where .= " AND sr.jml_petani < 200";
		}
		$sql = "SELECT
				m.MemberID
				, m.MemberDisplayID
				, m.MemberName
				, m.Address
				, m.VillageID
				, v.Village
				, sd.SubDistrict
				, d.District
				, d.DistrictID
				, p.Province
				, p.ProvinceID
				, m.Latitude
				, m.Longitude
				, mr.RoleName
				, IF(m.Photo!='',m.Photo,'no-user.jpg') AS Photo
			FROM ktv_members m
			JOIN (SELECT r.MemberID, mr.MRoleName AS RoleName FROM ktv_member_role r JOIN ktv_ref_member_role mr ON mr.MRoleID = r.MRoleID WHERE r.MRoleID IN (5,6,7,8,9,10)) mr ON mr.MemberID = m.MemberID
			-- where_hakakses --
			LEFT JOIN ktv_village v ON v.VillageID = m.VillageID
			LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
			LEFT JOIN ktv_district d ON d.DistrictID = sd.DistrictID
			LEFT JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
			LEFT JOIN (
				SELECT
						COUNT(MemberID) jml_petani
						, ObjID
				FROM
					ktv_members_relation
				GROUP BY
					ObjID
				ORDER BY ObjID
			) sr on sr.ObjID = m.MemberID
			WHERE
				m.StatusCode = 'active'
				AND m.Latitude IS NOT NULL AND m.Longitude IS NOT NULL
				AND (ABS(m.Latitude) BETWEEN 0 AND 90) AND (ABS(m.Longitude) BETWEEN 0 AND 180)
				-- where --
			GROUP BY m.MemberID
		";

		if (!empty($ProvinceID)) {
			$where .= " AND p.ProvinceID = ?";
			$params[] = intval($ProvinceID);
		}
		if (!empty($DistrictID)) {
			$where .= " AND d.DistrictID = ?";
			$params[] = intval($DistrictID);
		}
		if (!empty($key)) {
			// $where .= " AND (MemberName LIKE '%{$key}%' OR MemberDisplayID LIKE '%{$key}%')";
			$where .= " AND MemberDisplayID = '{$key}'";
		}

		//Hak Akses
		$where_hakakses = "";
		if (!empty($PartnerID)) {
			$where_hakakses = " JOIN `ktv_access_partner_member` apm ON apm.apmMemberID = m.`MemberID` AND apm.apmPartnerID IN ({$PartnerID}) ";
		} elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program") {
			$where_hakakses = " JOIN `ktv_access_partner_member` apm ON apm.apmMemberID = m.`MemberID` AND apm.apmPartnerID = '{$_SESSION['PartnerID']}' ";
		}

		$sql = str_replace("-- where --", $where, $sql);
		$sql = str_replace("-- where_hakakses --", $where_hakakses, $sql);
		$query = $this->db->query($sql, $params);
		// echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
		return array();
	}

	public function getSME($ProvinceID, $DistrictID, $key = '', $PartnerID)
	{
		$where  = '';
		$params = [];
		$sql = "SELECT
    m.MemberID
    , m.MemberDisplayID
    , IFNULL(me.agCompanyName, m.MemberName) MemberName
    , m.Address
    , m.VillageID
    , v.Village
    , sd.SubDistrict
    , d.District
    , d.DistrictID
    , p.Province
    , p.ProvinceID
    , m.Latitude
    , m.Longitude
    , mr.RoleName
    , IF(m.Photo!='',m.Photo,'no-user.jpg') AS Photo
FROM ktv_members m
JOIN (SELECT r.MemberID, mr.MRoleName AS RoleName FROM ktv_member_role r JOIN ktv_ref_member_role mr ON mr.MRoleID = r.MRoleID WHERE r.MRoleID IN (5,6,7,8,9,10,11,12,13,14)) mr ON mr.MemberID = m.MemberID
-- where_hakakses --
LEFT JOIN ktv_village v ON v.VillageID = m.VillageID
LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
LEFT JOIN ktv_district d ON d.DistrictID = sd.DistrictID
LEFT JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
LEFT JOIN ktv_members_extension me on me.MemberID = m.MemberID
WHERE
    m.StatusCode = 'active'
	AND (ABS(m.`Latitude`) > 0 AND ABS(m.`Longitude`) > 0 or ST_Latitude(m.LatLong) IS NOT NULL AND ST_Longitude(m.LatLong) IS NOT NULL)
    -- where --
GROUP BY m.MemberID
		";

		if (!empty($ProvinceID)) {
			$where .= " AND p.ProvinceID = ?";
			$params[] = intval($ProvinceID);
		}
		if (!empty($DistrictID)) {
			$where .= " AND d.DistrictID = ?";
			$params[] = intval($DistrictID);
		}
		if (!empty($key)) {
			// $where .= " AND (MemberName LIKE '%{$key}%' OR MemberDisplayID LIKE '%{$key}%')";
			$where .= " AND MemberDisplayID = '{$key}'";
		}

		//Hak Akses
		$where_hakakses = "";
		if (!empty($PartnerID)) {
			$where_hakakses = " JOIN `ktv_access_partner_member` apm ON apm.apmMemberID = m.`MemberID` AND apm.apmPartnerID IN ({$PartnerID}) ";
		} elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program") {
			$where_hakakses = " JOIN `ktv_access_partner_member` apm ON apm.apmMemberID = m.`MemberID` AND apm.apmPartnerID = '{$_SESSION['PartnerID']}' ";
		}

		$sql = str_replace("-- where --", $where, $sql);
		$sql = str_replace("-- where_hakakses --", $where_hakakses, $sql);
		$query = $this->db->query($sql, $params);
		// echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
		return false;
	}

	public function getSMEPlantation($ProvinceID, $DistrictID, $key = '', $PartnerID)
	{
		$where  = '';
		$params = [];
		$sql = "SELECT
    m.MemberID
    , m.MemberDisplayID
    , IFNULL(me.agCompanyName, m.MemberName) MemberName
    , m.Address
    , m.VillageID
    , v.Village
    , sd.SubDistrict
    , d.District
    , p.Province
	, sp.PlotNr AS GardenNr
    , sp.Latitude
    , sp.Longitude
	, sp.GardenAreaHa AS AreaHa
	, sp.AnnualProduction AS Production
    , mr.RoleName
    , IF(m.Photo!='',m.Photo,'no-user.jpg') AS Photo
    , pol.SurveyNr
    , pol.Revision
FROM ktv_survey_plot_status_sme sp
JOIN ktv_members m ON sp.MemberID = m.MemberID
JOIN (SELECT r.MemberID, GROUP_CONCAT(mr.MRoleName SEPARATOR ', ') AS RoleName FROM ktv_member_role r JOIN ktv_ref_member_role mr ON mr.MRoleID = r.MRoleID WHERE r.MRoleID IN (5,6,7,8,9,10,11,12,13,14) GROUP BY r.MemberID) mr ON mr.MemberID = m.MemberID
-- where_hakakses --
LEFT JOIN (
	SELECT
		MemberID, PlotNr, MAX(SurveyNr) AS SurveyNr, MAX(Revision) AS Revision
	FROM `ktv_survey_plot_polygon_sme` GROUP BY MemberID, PlotNr
) pol ON pol.MemberID = sp.MemberID AND pol.PlotNr = sp.PlotNr
LEFT JOIN ktv_village v ON v.VillageID = m.VillageID
LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
LEFT JOIN ktv_district d ON d.DistrictID = sd.DistrictID
LEFT JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
LEFT JOIN ktv_members_extension me on me.MemberID = m.MemberID
WHERE
    m.StatusCode = 'active' AND sp.ActiveStatus = 1
	AND (ABS(sp.`Latitude`) > 0 AND ABS(sp.`Longitude`) > 0 or ST_Latitude(sp.LatLong) IS NOT NULL AND ST_Longitude(sp.LatLong) IS NOT NULL)
    -- where --
GROUP BY sp.MemberID, sp.PlotNr
		";

		if (!empty($ProvinceID)) {
			$where .= " AND p.ProvinceID = ?";
			$params[] = intval($ProvinceID);
		}
		if (!empty($DistrictID)) {
			$where .= " AND d.DistrictID = ?";
			$params[] = intval($DistrictID);
		}
		if (!empty($key)) {
			// $where .= " AND (MemberName LIKE '%{$key}%' OR MemberDisplayID LIKE '%{$key}%')";
			$where .= " AND MemberDisplayID = '{$key}'";
		}

		//Hak Akses
		$where_hakakses = "";
		if (!empty($PartnerID)) {
			$where_hakakses = " JOIN `ktv_access_partner_member` apm ON apm.apmMemberID = m.`MemberID` AND apm.apmPartnerID IN ({$PartnerID}) ";
		} elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program") {
			$where_hakakses = " JOIN `ktv_access_partner_member` apm ON apm.apmMemberID = m.`MemberID` AND apm.apmPartnerID = '{$_SESSION['PartnerID']}' ";
		}

		$sql = str_replace("-- where --", $where, $sql);
		$sql = str_replace("-- where_hakakses --", $where_hakakses, $sql);
		$query = $this->db->query($sql, $params);
		// echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
		return false;
	}

	public function getSMEPolygon($ProvinceID, $DistrictID, $key = '', $PartnerID)
	{
		$where  = '';
		$params = [];
		$sql = "SELECT
				m.MemberID
				, m.MemberDisplayID
				, IFNULL(me.agCompanyName, m.MemberName) MemberName
				, m.Address
				, m.VillageID
				, v.Village
				, sd.SubDistrict
				, d.District
				, p.Province
				, sp.PlotNr AS GardenNr
				, sp.GardenAreaHa AS AreaHa
				, sp.GardenAreaPolygon AS PolygonHa
				, sp.AnnualProduction AS Production
				, mr.RoleName
				, IF(m.Photo!='',m.Photo,'no-user.jpg') AS Photo
				, pol.SurveyNr
				, pol.Revision
				, pol.CenterLatitude Latitude
				, pol.CenterLongitude Longitude
			FROM ktv_survey_plot_status_sme sp
			JOIN ktv_members m ON sp.MemberID = m.MemberID
			JOIN (SELECT r.MemberID, GROUP_CONCAT(mr.MRoleName SEPARATOR ', ') AS RoleName FROM ktv_member_role r JOIN ktv_ref_member_role mr ON mr.MRoleID = r.MRoleID WHERE r.MRoleID IN (5,6,7,8,9,10,11,12,13,14) GROUP BY r.MemberID) mr ON mr.MemberID = m.MemberID
			-- where_hakakses --
			INNER JOIN (
				SELECT
					MemberID, PlotNr, MAX(SurveyNr) AS SurveyNr, MAX(Revision) AS Revision
					, CenterLatitude
					, CenterLongitude
				FROM `ktv_survey_plot_polygon_sme` GROUP BY MemberID, PlotNr
			) pol ON pol.MemberID = sp.MemberID AND pol.PlotNr = sp.PlotNr
			LEFT JOIN ktv_village v ON v.VillageID = m.VillageID
			LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
			LEFT JOIN ktv_district d ON d.DistrictID = sd.DistrictID
			LEFT JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
			LEFT JOIN ktv_members_extension me on me.MemberID = m.MemberID
			WHERE
				m.StatusCode = 'active' AND sp.ActiveStatus = 1			
				AND (ABS(sp.`Latitude`) > 0 AND ABS(sp.`Longitude`) > 0 or ST_Latitude(sp.LatLong) IS NOT NULL AND ST_Longitude(sp.LatLong) IS NOT NULL)
				-- where --
			GROUP BY sp.MemberID, sp.PlotNr
		";

		if (!empty($ProvinceID)) {
			$where .= " AND p.ProvinceID = ?";
			$params[] = intval($ProvinceID);
		}
		if (!empty($DistrictID)) {
			$where .= " AND d.DistrictID = ?";
			$params[] = intval($DistrictID);
		}
		if (!empty($key)) {
			// $where .= " AND (MemberName LIKE '%{$key}%' OR MemberDisplayID LIKE '%{$key}%')";
			$where .= " AND MemberDisplayID = '{$key}'";
		}

		//Hak Akses
		$where_hakakses = "";
		if (!empty($PartnerID)) {
			$where_hakakses = " JOIN `ktv_access_partner_member` apm ON apm.apmMemberID = m.`MemberID` AND apm.apmPartnerID IN ({$PartnerID}) ";
		} elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program") {
			$where_hakakses = " JOIN `ktv_access_partner_member` apm ON apm.apmMemberID = m.`MemberID` AND apm.apmPartnerID = '{$_SESSION['PartnerID']}' ";
		}

		$sql = str_replace("-- where --", $where, $sql);
		$sql = str_replace("-- where_hakakses --", $where_hakakses, $sql);
		$query = $this->db->query($sql, $params);
		// echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
		return false;
	}

	public function getSMEPlantationPolygon($MemberID, $PlotNr, $SurveyNr, $Revision){
		$sql = "SELECT
					a.`latitude`
					, a.`longitude`
				FROM
					ktv_survey_plot_polygon_sme a
				WHERE
					a.`MemberID` = ?
					AND a.`PlotNr` = ?
					AND a.`SurveyNr` = ?
					AND a.`Revision` = ?
				ORDER BY a.`OrderNr` ASC";
		$query = $this->db->query($sql, array($MemberID, $PlotNr, $SurveyNr, $Revision));
        if ($query->num_rows() > 0) {
			$return = array();
            $result = $query->result_array();
            foreach ($result as $key => $value) {
                $return[$key][0] = floatval($value['latitude']);
                $return[$key][1] = floatval($value['longitude']);
            }
            return $return;
        }
        return array();
	}

	public function getProcessing($ProvinceID, $DistrictID, $key = '', $PartnerID)
	{
		if (!empty($PartnerID)) {
			$sqlHakAksesMill = " INNER JOIN ktv_access_partner_mill acc_pmi ON m.MillID = acc_pmi.apmiMillID AND acc_pmi.apmiPartnerID IN ({$PartnerID}) ";
		} else {
			$sqlHakAksesMill = $this->sqlHakAksesMill;
		}
		$where  = '';
		$params = [];
		$sql = "
SELECT
    m.MillID ID
    , m.MillDisplayID AS DisplayID
    , m.MillName AS Name
    , m.Address
    , v.Village
    , sd.SubDistrict
    , d.District
    , d.DistrictID
    , p.Province
    , p.ProvinceID
    , m.Status
    , m.Latitude
    , m.Longitude
FROM ktv_mill m
LEFT JOIN ktv_village v ON v.VillageID = m.VillageID
LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
LEFT JOIN ktv_district d ON d.DistrictID = sd.DistrictID
LEFT JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
".$sqlHakAksesMill."
WHERE m.StatusCode = 'active'
    AND m.Latitude IS NOT NULL AND m.Longitude IS NOT NULL
	AND (ABS(m.`Latitude`) > 0 AND ABS(m.`Longitude`) > 0 or ST_Latitude(m.LatLong) IS NOT NULL AND ST_Longitude(m.LatLong) IS NOT NULL)
   -- where --
GROUP BY m.MillID
		";
		if (!empty($ProvinceID)) {
			$where .= " AND p.ProvinceID = ?";
			$params[] = intval($ProvinceID);
		}
		if (!empty($DistrictID)) {
			$where .= " AND d.DistrictID = ?";
			$params[] = intval($DistrictID);
		}
		if (!empty($key)) {
			// $where .= " AND (MillDisplayID LIKE '%{$key}%' OR MillName LIKE '%{$key}%')";
			$where .= " AND MillDisplayID = '{$key}'";
		}
		$sql = str_replace("-- where --", $where, $sql);
		$query = $this->db->query($sql, $params);
		//echo "<pre>"; print_r($this->db->last_query()); echo "</pre>";exit;
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
		return false;
	}

	public function getMillPlantation($ProvinceID, $DistrictID, $key = '', $PartnerID)
	{
		if (!empty($PartnerID)) {
			$sqlHakAksesMill = " INNER JOIN ktv_access_partner_mill acc_pmi ON m.MillID = acc_pmi.apmiMillID AND acc_pmi.apmiPartnerID IN ({$PartnerID}) ";
		} else {
			$sqlHakAksesMill = $this->sqlHakAksesMill;
		}
		$where  = '';
		$params = [];
		$sql = "
SELECT
    m.MillDisplayID AS ID
    , m.MillName AS Name
    , m.Address
    , v.Village
    , sd.SubDistrict
    , m.Status
	, sp.PlotNr AS GardenNr
    , sp.Latitude
    , sp.Longitude
	, sp.GardenAreaHa AS AreaHa
	, sp.AnnualProduction AS Production
    , pol.SurveyNr
    , pol.Revision
FROM ktv_survey_plot_status_mill sp
JOIN ktv_mill m ON sp.MillID = m.MillID
LEFT JOIN ktv_village v ON v.VillageID = m.VillageID
LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
LEFT JOIN ktv_district d ON d.DistrictID = sd.DistrictID
LEFT JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
LEFT JOIN (
	SELECT
		MillID, PlotNr, MAX(SurveyNr) AS SurveyNr, MAX(Revision) AS Revision
	FROM `ktv_survey_plot_polygon_mill` GROUP BY MillID, PlotNr
) pol ON pol.MillID = sp.MillID AND pol.PlotNr = sp.PlotNr
".$sqlHakAksesMill."
WHERE m.StatusCode = 'active' AND sp.ActiveStatus = 1
    AND m.Latitude IS NOT NULL AND m.Longitude IS NOT NULL
    AND (ABS(sp.Latitude) BETWEEN 0 AND 90) AND (ABS(sp.Longitude) BETWEEN 0 AND 180)
   -- where --
GROUP BY m.MillID
		";
		if (!empty($ProvinceID)) {
			$where .= " AND p.ProvinceID = ?";
			$params[] = intval($ProvinceID);
		}
		if (!empty($DistrictID)) {
			$where .= " AND d.DistrictID = ?";
			$params[] = intval($DistrictID);
		}
		if (!empty($key)) {
			$where .= " AND MillDisplayID = '{$key}'";
		}
		$sql = str_replace("-- where --", $where, $sql);
		$query = $this->db->query($sql, $params);
		// echo "<pre>"; print_r($this->db->last_query()); echo "</pre>";exit;
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
		return false;
	}

	public function getMillPlantationPolygon($MillID, $PlotNr, $SurveyNr, $Revision){
		$sql = "SELECT
					a.`latitude`
					, a.`longitude`
				FROM
					ktv_survey_plot_polygon_mill a
				WHERE
					a.`MillID` = ?
					AND a.`PlotNr` = ?
					AND a.`SurveyNr` = ?
					AND a.`Revision` = ?
				ORDER BY a.`OrderNr` ASC";
		$query = $this->db->query($sql, array($MillID, $PlotNr, $SurveyNr, $Revision));
        if ($query->num_rows() > 0) {
			$return = array();
            $result = $query->result_array();
            foreach ($result as $key => $value) {
                $return[$key][0] = floatval($value['latitude']);
                $return[$key][1] = floatval($value['longitude']);
            }
            return $return;
        }
        return array();
	}

	public function getFarmerPolygon($MemberID, $PlotNr, $SurveyNr, $Revision)
	{
        $sql = "
SELECT
  ga.Latitude,
  ga.Longitude
FROM `ktv_survey_plot_polygon` ga
WHERE
  ga.MemberID = ?
  AND ga.PlotNr = ?
  AND ga.SurveyNr = ?
  AND ga.Revision = ?
ORDER BY ga.OrderNr
      ";
        $query = $this->db->query($sql, array($MemberID, $PlotNr, $SurveyNr, $Revision));
        if ($query->num_rows()>0) {
            $return = array();
            $result = $query->result_array();
            foreach ($result as $key => $value) {
                $return[$key][0] = floatval($value['Latitude']);
                $return[$key][1] = floatval($value['Longitude']);
            }
            return $return;
        }
	}

	public function GetInfoGardenPolygon($MemberID){
		$sql = "SELECT
				b.`MemberID`
				, b.`MemberID` AS ID
				, b.`PlotNr`
				, b.SurveyNr
				, ST_ASGEOJSON(ar.Polygon) Polygon
				, b.`GardenAreaHa`
				, IFNULL(b.`GardenAreaPolygon`, b.`GardenAreaHa`) GardenAreaPolygon
				, YEAR(NOW())-b.FirstPlantingYear AS FarmAge
			FROM
				ktv_survey_plot b
			LEFT JOIN ktv_survey_plot_polygon_geo ar ON 1=1
				AND b.`MemberID` = ar.`MemberID`
				AND b.`PlotNr` = ar.`PlotNr`
				AND ar.StatusCheck IN ('new','verified')
			WHERE
				b.`MemberID` = ?
				AND ar.`MemberID` IS NOT NULL
			GROUP BY b.`PlotNr`
		";
		$data = $this->db->query($sql,array($MemberID))->result_array();
		$DataReturn = $data;

		//Tentukan ambil revisi yang mana
        foreach ($data as $key => $value) {
            $polygon = json_decode($value["Polygon"])->coordinates;
			$DataReturn[$key]["Latitude"] = $polygon[0][0][1];
			$DataReturn[$key]["Longitude"] = $polygon[0][0][0];
			$polygonnew = array();
			foreach($polygon[0] as $key => $value){
				$polygonnew[$key][0] = $value[1];
				$polygonnew[$key][1] = $value[0];
			}
			
			$DataReturn[$key]["polygon"] = $polygonnew;
        }

		return $DataReturn;
	}

	public function getFarmerPolygonNew($MemberID, $PlotNr, $SurveyNr, $Revision){
		$sql = "SELECT
					ST_ASGEOJSON(a.Polygon) Polygon
				FROM
					ktv_survey_plot_polygon_geo a
				WHERE
					a.`StatusCheck` in ('verified','new')
					AND a.`MemberID` = ?
					AND a.`PlotNr` = ?
					AND a.`SurveyNr` = ?
				ORDER BY a.`PlotNr` ASC";
		$query = $this->db->query($sql, array($MemberID, $PlotNr, $SurveyNr, $Revision));
		
        if ($query->num_rows() > 0) {
			$return = array();
            $result = $query->row_array();
			$polygon = json_decode($result["Polygon"])->coordinates[0];
            foreach ($polygon as $key => $value) {
                $return[$key][0] = floatval($value[1]);
                $return[$key][1] = floatval($value[0]);
            }
            return $return;
        }
        return array();
	}

	public function getBank($ProvinceID, $DistrictID, $key = '')
	{
		$where  = '';
		$params = [];
		$sql = "
SELECT
    bb.`BranchID` AS ID
	, CONCAT(b.`BankName`, ' ', bb.`BranchName`) AS `Name`
	, IFNULL(v.Village,'') as Village
	, IFNULL(sd.SubDistrict,'') as SubDistrict
    , bb.`BranchAddress` AS Address
    , bb.`BranchLatitude` AS Latitude
    , bb.`BranchLongitude` AS Longitude
FROM ktv_bank_branch bb
JOIN ktv_bank b ON b.`BankID` = bb.`BranchBankID`
LEFT JOIN ktv_village v ON v.VillageID = bb.`BranchVillageID`
LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = bb.BranchSubDistrictID 
LEFT JOIN ktv_district d ON d.DistrictID = bb.BranchDistrictID
LEFT JOIN ktv_province p ON p.ProvinceID = bb.BranchProvinceID
WHERE bb.StatusCode = 'active'
    AND ABS(bb.BranchLatitude) > 0 AND ABS(bb.BranchLongitude) > 0
   -- where --
GROUP BY bb.BranchID
		";
		if (!empty($ProvinceID)) {
			$where .= " AND p.ProvinceID = ?";
			$params[] = intval($ProvinceID);
		}
		if (!empty($DistrictID)) {
			$where .= " AND d.DistrictID = ?";
			$params[] = intval($DistrictID);
		}
		if (!empty($key)) {
			$where .= " AND (BranchName LIKE '%{$key}%' OR BankName LIKE '%{$key}%')";
		}
		$sql = str_replace("-- where --", $where, $sql);
		$query = $this->db->query($sql, $params);
		
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
		return false;
		
	}

	public function updatePolygonRevision()
	{
		return $this->db->query("
UPDATE ktv_survey_plot p
JOIN (
SELECT
	p.`MemberID`, p.`PlotNr`, p.`SurveyNr`, MAX(p.`Revision`) AS Revision
FROM ktv_survey_plot_polygon p
WHERE
	p.`StatusCheck` = 'verified'
	AND p.`StatusCode` = 'active'
GROUP BY p.`MemberID`, p.`PlotNr`, p.`SurveyNr`
) z ON p.`MemberID` = z.`MemberID` AND  p.`PlotNr` = z.`PlotNr` AND p.`SurveyNr` = z.`SurveyNr`
SET
	p.`PolygonRevision` = z.Revision");
	}

	public function getProvince()
	{
		$where  = '';
		$params = [];
		if ($_SESSION['is_admin'] == '0') {
			$where = " AND ac.UserId = ?";
			$params[] = $_SESSION['userid'];
		}
		
		$sql = "
		SELECT
			p.`ProvinceID` AS id
			, p.`Province` AS label
		FROM ktv_province p
		JOIN ktv_district d ON d.`ProvinceID` = p.`ProvinceID`
		LEFT JOIN ktv_access_staff ac ON ac.DistrictID = d.DistrictID
		WHERE
			p.`StatusCode` = 'active'
			{$where}
		GROUP BY id
		ORDER BY label		
		";
		$query = $this->db->query($sql, $params);
		if ($query->num_rows()>0) {
		    return $query->result_array();
		}
		return false;
	}

	public function getDistrict($ProvinceID)
	{
		$where    = '';
		$params   = [];
		$params[] = $ProvinceID;
		if ($_SESSION['is_admin'] == '0') {
			$where = " AND ac.UserId = ?";
			$params[] = $_SESSION['userid'];
		}
		$sql = "
		SELECT
			d.`DistrictID` AS id
			, d.`District` AS label
		FROM ktv_district d
		LEFT JOIN ktv_access_staff ac ON ac.DistrictID = d.DistrictID
		WHERE
			d.`StatusCode` = 'active'
			AND d.ProvinceID = ?
			{$where}
		GROUP BY id
		ORDER BY label		
		";
		$query = $this->db->query($sql, $params);
		if ($query->num_rows()>0) {
		    return $query->result_array();
		}
		return false;
	}

	public function getProvinceFull()
	{
		$where  = '';
		$params = [];
		
		$sql = "
		SELECT
			p.`ProvinceID` AS id
			, p.`Province` AS label
		FROM ktv_province p
		JOIN ktv_district d ON d.`ProvinceID` = p.`ProvinceID`
		WHERE
			p.`StatusCode` = 'active'
			{$where}
		GROUP BY id
		ORDER BY label		
		";
		$query = $this->db->query($sql, $params);
		if ($query->num_rows()>0) {
		    return $query->result_array();
		}
		return false;
	}

	public function getDistrictFull($PartnerIDs)
	{
		$sql = "
		SELECT
			d.`DistrictID` AS id
			, CONCAT(p.Province,' - ',d.`District`) AS label
		FROM ktv_district d
		LEFT JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
		LEFT JOIN ktv_district_partner dp ON dp.`DistrictID` = d.`DistrictID`
		WHERE
			d.`StatusCode` = 'active'
			AND dp.`PartnerID` IN ({$PartnerIDs})
		GROUP BY id
		ORDER BY label		
		";
		$query = $this->db->query($sql, $params);
		if ($query->num_rows()>0) {
		    return $query->result_array();
		}
		return false;
	}

	public function getFireHotspot($timeline = '', $date = '', $confidence = '', $satellite = '')
	{
		$sql = "
			SELECT
			    `latitude` AS Latitude
			    , `longitude` AS Longitude
			    , `bright_ti4`-273.15 AS Temperature
			    , DATE(`acq_date`) AS AcqDate
			    , `acq_time` AS AcqTime
			    , `satellite` AS Satelite
			    , CASE
			        WHEN `confidence` = 'low' THEN 'Low'
			        WHEN `confidence` = 'nominal' THEN 'Medium'
			        WHEN `confidence` = 'high' THEN 'High'
			        ELSE ''
			    END AS Confidence
			    , `daynight`
			    , CASE
			        WHEN satellite = 'N' THEN 'S-NPP (VIIRS)'
			        WHEN satellite = '1' THEN 'NOAA (VIIRS)'
			        WHEN satellite = 'Aqua' THEN 'Aqua (MODIS)'
			        WHEN satellite = 'Terra' THEN 'Terra (MODIS)'
			        ELSE ''
			    END AS SatelliteName
			FROM
			    `firms_viis`
			WHERE 1 = 1
				-- where --
		";
		$where = '';
		$params = [];
		if (!empty($timeline)) {
	        $today = date('Y-m-d');
	        $end = date("Y-m-d", strtotime($today." - 24 hours"));
			switch ($timeline) {
				case 'latest':
					$query = $this->db->query("SELECT DATE(MAX(acq_date)) AS dt FROM firms_viis");
					$where .= " AND DATE(acq_date) = ?";
					$params[] = $query->row()->dt;
					break;
				case '24h':
					$start = date("Y-m-d", strtotime($end." - 24 hours"));
					$where .= " AND DATE(acq_date) BETWEEN '{$start}' AND '{$end}'";
					break;
				case '48h':
					$start = date("Y-m-d", strtotime($end." - 48 hours"));
					$where .= " AND DATE(acq_date) BETWEEN '{$start}' AND '{$end}'";
					break;
				case '72h':
					$start = date("Y-m-d", strtotime($end." - 72 hours"));
					$where .= " AND DATE(acq_date) BETWEEN '{$start}' AND '{$end}'";
					break;
			}
		} else if ($date) {
			$where .= " AND DATE(acq_date) = ?";
			$params[] = $date;
		}
		if (!empty($confidence)) {
			$where .= " AND confidence = ?";
			$params[] = $confidence;
		}
		if (!empty($satellite)) {
			$where .= " AND satellite = ?";
			$params[] = $satellite;
		}
		$sql = str_replace('-- where --', $where, $sql);
		$query = $this->db->query($sql, $params);
		// echo "<pre>"; print_r($this->db->last_query()); echo "</pre>";exit;
		if ($query->num_rows()>0) {
		    return $query->result_array();
		}
		return false;
	}


// NEW UI --------------------------
	
	public function GetFarmLocation($ProvinceID, $DistrictID, $key = '', $FarmAge = '', $PartnerID = '') {
		$this->load->library('awsfileupload');

		$DataReturn = [];
		$DataReturn["Data"] = [];
		$DataReturn["Info"] = [];

		$where  = '';
		$where_garden = '';
		$params = [];

		if (!empty($ProvinceID)) {
			$where_garden .= " AND p.ProvinceID = ?";
			$params[] = intval($ProvinceID);
		}

		if (!empty($DistrictID)) {
			$where_garden .= " AND d.DistrictID = ?";
			$params[] = intval($DistrictID);
		}

		if (!empty($key)) {
			$where_garden .= " AND f.MemberDisplayID like '%{$key}%'";
		}
		
		if (isset($FarmAge) && $FarmAge != '') {
			switch ($FarmAge) {
				case '0':
					$where_garden = " AND FarmAge BETWEEN 0 AND 4";
					break;
				case '4':
					$where_garden = " AND FarmAge BETWEEN 5 AND 8";
					break;
				case '8':
					$where_garden = " AND FarmAge BETWEEN 9 AND 18";
					break;
				case '18':
					$where_garden = " AND FarmAge > 18";
					break;
			}
		}

		$where_garden .= " AND ABS(ST_Latitude(g.`LatLong`)) > 0";
		$where_garden .= " AND ABS(ST_Longitude(g.`LatLong`)) > 0";
	
						
		// var_dump($where_garden);die();
		//Pengecekan Hak Akses
		$where_hakakses = "";
		if (!empty($PartnerID)) {
			$where_hakakses = " JOIN `ktv_access_partner_member` apm ON apm.apmMemberID = f.`MemberID` AND apm.apmPartnerID IN ({$PartnerID}) ";
		} elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program") {
			if($_SESSION["PartnerAsParent"] == "Yes"){
				($PartnerID != '')?$PartnerID = $PartnerID: $PartnerID = $_SESSION['PartnerChild'];
			
				$where_hakakses = " JOIN `ktv_access_partner_member` apm ON apm.apmMemberID = f.`MemberID` AND apm.apmPartnerID IN ({$PartnerID})";
				$where = " AND d.DistrictID IN (".$_SESSION['daerah_access'].")";	
			}else{
				$where_hakakses = " JOIN `ktv_access_partner_member` apm ON apm.apmMemberID = f.`MemberID` AND apm.apmPartnerID = '{$_SESSION['PartnerID']}' ";
				$where = " AND d.DistrictID IN (".$_SESSION['daerah_access'].")";
			}
		}
		$sql = str_replace("-- where --", $where, $this->sql['farmer']);
		$sql = str_replace("-- where_garden --", $where_garden, $sql);
		$sql = str_replace("-- where_hakakses --", $where_hakakses, $sql);
		$query = $this->db->query($sql, $params);
		// echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;

		if ($query->num_rows() > 0) {
			$DataReturn["Data"] = $query->result_array();
			$DataReturn["Info"]["total_farm"] = $query->num_rows();
			return $DataReturn;
		}
		return false;
	}


	public function GetKmlLayerList($params) {
		extract($params);   //params : $CountryID, $ProvinceID,  $DistrictID

		$DataReturn = [];

		$sql = "SELECT distinct
					kk.CategoryID
					, kk.Name
					, kk.Color 
				FROM 
					ktv_kml kk 
				WHERE 1=1
					AND kk.StatusCode = 'active'
					AND ( (kk.`ProvinceID`  = {$ProvinceID}) OR (0={$ProvinceID}) )
					AND ( (kk.`DistrictID`  = {$DistrictID}) OR (0={$DistrictID}) )
				ORDER BY
					kk.Name
		";
		$DataReturn = $this->db->query($sql)->result_array();

		return $DataReturn;
	}


	public function GetShowKml($params) {
		extract($params);   //params : $CountryID, $ProvinceID, $DistrictID, $Name

		$DataReturn = [];

		$sql = "SELECT 
					kk.ID
					, kk.FileName
				FROM 
					ktv_kml kk 
				WHERE 1=1
					AND kk.StatusCode = 'active'
					AND ( (kk.`ProvinceID`  = {$ProvinceID}) OR (0={$ProvinceID}) )
					AND ( (kk.`DistrictID`  = {$DistrictID}) OR (0={$DistrictID}) )
					AND kk.`Name`= '{$Name}'
		";
		$DataReturn = $this->db->query($sql)->result_array();

		return $DataReturn;
	}


	public function GetInfoGardenPolygonNEWUI($MemberID){
		$sql = "SELECT
			MemberID
			, PlotNr
			, Max(Revision) as Revision
			FROM
			ktv_survey_plot_polygon_geo
			WHERE
				MemberID = ?
			GROUP BY MemberID, PlotNr
		";
		$data = $this->db->query($sql,array($MemberID))->result_array();
		$DataReturn = $data;

		if (!empty($DataReturn)) {
			// var_dump($DataReturn);die();
			$DataGarden = [];
			
			foreach ($DataReturn as $key => $value) {
				$sql = "SELECT
						b.`MemberID`
						, b.`MemberID` AS ID
						, b.`PlotNr`
						, ar.SurveyNr
						, ST_ASGEOJSON(ar.Polygon) Polygon
						, ar.StatusCheck
						, ar.Revision
						, b.`GardenAreaHa`
						/*, IFNULL(b.`GardenAreaPolygon`, b.`GardenAreaHa`) GardenAreaPolygon*/
						, ar.AreaHa GardenAreaPolygon
						, YEAR(NOW())-b.FirstPlantingYear AS FarmAge
						, p.Province
						, p.ProvinceID
						, d.District
						, d.DistrictID
						, sd.SubDistrict
						, v.Village
						, b.GardenAreaHa AS AreaHa
						, ps.AnnualProduction AS Production
						, ar.PartnerName
					FROM
						ktv_survey_plot b
					LEFT JOIN ktv_survey_plot_polygon_geo ar ON 1=1
						AND b.`MemberID` = ar.`MemberID`
						AND b.`PlotNr` = ar.`PlotNr`
						AND ar.StatusCheck IN ('new','verified','overlap','retake','partnerverified')
						AND ar.Revision = ?
					LEFT JOIN ktv_survey_plot_status ps ON ps.MemberID = b.MemberID AND ps.PlotNr = b.PlotNr
					JOIN ktv_village v ON v.VillageID = b.VillageID
					JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
					JOIN ktv_district d ON d.DistrictID = sd.DistrictID
					JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
					WHERE
						b.`MemberID` = ?
						AND b.`PlotNr` = ?
						AND ar.`MemberID` IS NOT NULL
					GROUP BY ar.`MemberID`, b.`PlotNr`
				";
				$data = $this->db->query($sql,array($value['Revision'], $MemberID, $value['PlotNr']))->result_array();
				$LatestRev = $data;

				//Tentukan ambil revisi yang mana
				foreach ($data as $key => $value) {
					$polygon = json_decode($value["Polygon"])->coordinates;
					$DataReturn[$key]["Latitude"] = $polygon[0][0][1];
					$DataReturn[$key]["Longitude"] = $polygon[0][0][0];
					$DataReturn[$key]["polygon"] = $polygon[0];
				}
				// array_push($DataGarden,$LatestRev);
				$DataGarden = array_merge($DataGarden,$LatestRev);
			}

			return $DataGarden;
		}
		

		return $DataReturn;
	}


	public function GetLandManagement($params) {
		extract($params);   //params : $CountryID, $ProvinceID, $DistrictID, $PartnerID

		$DataReturn = [];

		$sql = "SELECT 
					g.Remark
					, ST_AsGeoJSON(g.Polygon) as PolygonGeo
					, g.AreaHa
					, p.Province
					, d.District
				FROM 
					gis_ext_land_management g
				JOIN ktv_province p ON p.ProvinceID = g.ProvinceID
				JOIN ktv_district d ON d.DistrictID = g.DistrictID
				WHERE 1=1
				AND ( (g.`PartnerID`  = {$PartnerID}) OR (1={$PartnerID}) )
				AND ( (g.`ProvinceID`  = {$ProvinceID}) OR (0={$ProvinceID}) )
				AND ( (g.`DistrictID`  = {$DistrictID}) OR (0={$DistrictID}) )
		";
		$DataReturn = $this->db->query($sql)->result_array();

		return $DataReturn;
	}


	public function importKMLtmp($file) {
		$this->load->helper('file');
		$kml = read_file($file);
		if (!empty($kml)) {
			$this->db->query("DELETE FROM ktv_upload_farm_client_tmp WHERE CreatedBy = {$_SESSION['userid']}");

			$places_xml = simplexml_load_string($kml);
			$errors = array();
			$success = array();
			if ($places_xml) {
				foreach ($places_xml->Document->Folder->Placemark as $key => $value) {

					$MemberDisplayID_key = null;
					$MemberName_key = null;
					$PlotNr_key = null;
					$SurveyNr_key = null;
					
					$MemberID 		= "";
					$MemberName		= "";
					$PlotNr			= 0;
					$SurveyNr		= 0;
					$Polygon		= 'null';
					$Lat			= 'null';
					$Lng			= 'null';  
					$CenterLatLong  = 'null';  
					$Valid			= 1;
					$AreaHa			= 'null';
					$Remark			= "-";
					$DateCreated	= date("Y-m-d H:i:s");
					$CreatedBy		= $_SESSION["userid"];
					$Revision 		= 1;
					$PartnerName = $this->getPartnerName($_SESSION['PartnerID']);


					// Cek Coordinat & convert to polygon string
						$coordinates = trim(strval($value->Polygon->outerBoundaryIs->LinearRing->coordinates));

						$coor_arr   = explode(' ', $coordinates);
						if ($coor_arr[count($coor_arr)-1] != $coor_arr[0]){
							$coor_arr[] = $coor_arr[0];
						} 
						for ($i = 0; $i < count($coor_arr); $i++) {
							$coor_arr[$i] = str_replace(","," ",$coor_arr[$i]);
						}

						$PolygonStr = 'POLYGON ((' . implode(", ",$coor_arr). '))' ;
						
						$Polygon 		= "ST_GeomFromText('{$PolygonStr}', 4326, 'axis-order=long-lat')";
						$Lat 			= "ST_Y(ST_Centroid(ST_GeomFromText('{$PolygonStr}')))";
						$Lng 			= "ST_X(ST_Centroid(ST_GeomFromText('{$PolygonStr}')))";
						$AreaHa			= "ST_Area(ST_GeomFromText('{$PolygonStr}', 4326, 'axis-order=long-lat'))/10000";

					
					// GET Data FarmerID, FarmNr, SurveyNr From KML
						for ($i = 0; $i < count($value->ExtendedData->SchemaData->SimpleData); $i++) {
							$v = reset($value->ExtendedData->SchemaData->SimpleData[$i]);
							if (strtoupper($v['name']) == 'ID') {
								$MemberDisplayID_key = $i;
							}
							if (strtoupper($v['name']) == 'NAME') {
								$MemberName_key = $i;
							}
							if (strtoupper($v['name']) == 'FARM_NR') {
								$PlotNr_key = $i;
							}
							if (strtoupper($v['name']) == 'SURVEY_NR') {
								$SurveyNr_key = $i;
							}

							$MemberDisplayID    = strval($value->ExtendedData->SchemaData->SimpleData[$MemberDisplayID_key]);
							$MemberName    		= strval($value->ExtendedData->SchemaData->SimpleData[$MemberName_key]);
							$PlotNr    			= intval($value->ExtendedData->SchemaData->SimpleData[$PlotNr_key]);
							$SurveyNr    		= intval($value->ExtendedData->SchemaData->SimpleData[$SurveyNr_key]);
						}

						$MemberID           = $this->getMemberID($MemberDisplayID);
					
					// Cek Valid Data 
						if($MemberID){
							$cekPlotNr = $this->cekPlotNr($MemberID, $PlotNr);
							if($cekPlotNr){
								$cekSurveyNr = $this->cekSurveyNr($MemberID, $PlotNr, $SurveyNr );
								if ($cekSurveyNr["LastRevision"] !="") {
									$Revision 	= intval($cekSurveyNr["LastRevision"]) + 1;
								} else {
									if($SurveyNr != 20 ) $Valid = 0;
									$Remark		= "SurveyNr Not Exist";
								}
							}else{
								$Valid		= 0;
								$Remark		= "FarmNr Not Exist";
							}
						} else {
							$Valid		= 0;
							$Remark		= "Farmer Not Exist";
						}

					// Insert Into Database
						$sql = "INSERT INTO ktv_upload_farm_client_tmp
									(	
										`MemberDisplayID`,
										`MemberName`,
										`PlotNr`,
										`SurveyNr`,
										`Revision`,
										`PartnerName`,
										`Polygon`,
										`Lat`,
										`Lng`,
										`AreaHa`,
										`Valid`,
										`Remark`,
										`DateCreated`,
										`CreatedBy`
									)
								VALUES
									(
										'{$MemberDisplayID}',
										'{$MemberName}',
										{$PlotNr},
										{$SurveyNr},
										{$Revision}, 
										'{$PartnerName}', 
										{$Polygon},
										{$Lat},
										{$Lng},
										{$AreaHa},
										{$Valid},
										'{$Remark}',
										'{$DateCreated}',
										{$CreatedBy}
									)";
						$this->db->query($sql);


						$sql = "SELECT a.Lat, a.Lng FROM ktv_upload_farm_client_tmp a
						WHERE 1=1
							AND a.MemberDisplayID 	= '{$MemberDisplayID}'
							AND a.PlotNr 			= {$PlotNr}
							AND a.SurveyNr 			= {$SurveyNr}
							AND a.CreatedBy 		= {$CreatedBy}
						";
						
						$tmp  = $this->db->query($sql)->result_array();
						
						$f_Lat = floatval($tmp[0]["Lat"]);
						$f_Lng = floatval($tmp[0]["Lng"]);

						$sql = "UPDATE ktv_upload_farm_client_tmp a
								SET a.CenterLatLong = ST_GeomFromText('POINT({$f_Lng} {$f_Lat})', 4326, 'axis-order=long-lat')
								WHERE 1=1
									AND a.MemberDisplayID 	= '{$MemberDisplayID}'
									AND a.PlotNr 			= {$PlotNr}
									AND a.SurveyNr 			= {$SurveyNr}
									AND a.CreatedBy 		= {$CreatedBy}
								";
						$this->db->query($sql);
						// var_dump($tmp[0]["Lat"]);die();

						
				}
			}
		}
	}

	public function getFarmPolygonClient(){
		$sql = "SELECT 
					a.`MemberDisplayID`
					, a.`MemberName`
					, a.`PlotNr`
					, a.`SurveyNr`
					, a.`Lat`
					, a.`Lng`
					, ST_ASGEOJSON(a.Polygon) as `Polygon`
					, a.`AreaHa`
					, a.`Valid`
					, a.`Remark`
				FROM ktv_upload_farm_client_tmp a
				WHERE CreatedBy = ?
				";
		$query = $this->db->query($sql, array($_SESSION['userid']));

		if ($query->num_rows() > 0) {
			$return['data'] = $query->result_array();
			$query = $this->db->query("SELECT FOUND_ROWS() AS total");
			$return['total'] = $query->row_array(0)['total'];
			return $return;
		}
		return true;
	}

	public function getMemberID($MemberDisplayID)
	{
		$query = $this->db->select('MemberID')->get_where('ktv_members', array('MemberDisplayID' => $MemberDisplayID), 1);
		if ($query->num_rows() > 0) {
			return $query->row_array(0)["MemberID"];
		}
		return false;
	}

	public function cekPlotNr($MemberID, $PlotNr){
		$sql = "SELECT ksp.MemberID, ksp.PlotNr
					FROM ktv_survey_plot as ksp
					WHERE ksp.MemberID = {$MemberID}
						AND ksp.PlotNr = {$PlotNr}
				";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0) {
			return $query->row_array(0);
		}

		return false;
	}

	public function cekSurveyNr($MemberID, $PlotNr, $SurveyNr)
	{
		$sql = "SELECT MAX(ksppg.Revision) as `LastRevision`
				FROM ktv_survey_plot_polygon_geo as ksppg
				WHERE ksppg.MemberID = {$MemberID}
					AND ksppg.PlotNr = {$PlotNr}
					AND ksppg.SurveyNr = {$SurveyNr}
			";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			return $query->row_array(0);
		}
		return false;
	}


	public function farmPolygonClientClearData(){
		return $this->db->query("DELETE FROM ktv_upload_farm_client_tmp WHERE CreatedBy=?",  array($_SESSION['userid']));
	}

	public function cekFarmPolygonClient()
	{
		$sql = "SELECT a.*
				FROM ktv_upload_farm_client_tmp a
					JOIN ktv_members m ON m.MemberDisplayID = a.MemberDisplayID
					JOIN ktv_survey_plot_status p ON m.MemberID = p.MemberID AND a.PlotNr = p.PlotNr
				WHERE a.CreatedBy = ?";

		$query = $this->db->query($sql, array($_SESSION['userid']));
		
		if ($query->num_rows() > 0) {
			return true;
		}

		return false;
	}

	public function updateFarmPolygonClient()
	{


		$this->db->trans_start(FALSE);
			
		// insert data to ktv_survey_plot_polygon_geo
			
			$sql = "INSERT INTO `ktv_survey_plot_polygon_geo` (
				`MemberID`
				, `PlotNr`
				, `SurveyNr`
				, `Revision`
				, `Polygon`
				, `CenterLatLong`
				, `AreaHa`
				, `PartnerName`
				, `StatusCheck`
				, `DateCreated`
				, `CreatedBy`
			)
			SELECT
				m.`MemberID`
				, a.`PlotNr`
				, a.`SurveyNr`
				, a.`Revision`
				, a.`Polygon`
				, a.`CenterLatLong`
				, a.`AreaHa`
				, a.`PartnerName`
				, 'partnerverified'
				, a.`DateCreated`
				, a.`CreatedBy`
			FROM
				ktv_upload_farm_client_tmp a
				JOIN ktv_members m ON m.MemberDisplayID = a.MemberDisplayID 
			WHERE 1=1
				AND a.`CreatedBy` = {$_SESSION['userid']}
				AND a.Valid = 1";

			$query = $this->db->query($sql);

		// Update data to ktv_survey_plot_status
			$sql = "UPDATE ktv_survey_plot_status ksps
					JOIN ktv_members km ON km.MemberID = ksps.MemberID 
					INNER JOIN ktv_upload_farm_client_tmp kufc on 1=1
						AND kufc.MemberDisplayID = km.MemberDisplayID
						AND kufc.PlotNr = ksps.PlotNr
					SET ksps.GardenAreaPolygon = kufc.AreaHa
						, ksps.Latitude = kufc.Lat
						, ksps.Longitude = kufc.Lng
						, ksps.LatLong = kufc.CenterLatLong
						, ksps.DateUpdated = kufc.DateCreated
						, ksps.LastModifiedBy = kufc.CreatedBy
					WHERE 1=1
						AND kufc.`CreatedBy` = {$_SESSION['userid']}
						AND kufc.Valid = 1";
			$query = $this->db->query($sql);

			
			$sql = "UPDATE ktv_survey_plot ksps
					JOIN ktv_members km ON km.MemberID = ksps.MemberID 
					INNER JOIN ktv_upload_farm_client_tmp kufc on 1=1
						AND kufc.MemberDisplayID = km.MemberDisplayID
						AND kufc.PlotNr = ksps.PlotNr
					SET ksps.GardenAreaPolygon = kufc.AreaHa
						, ksps.Latitude = kufc.Lat
						, ksps.Longitude = kufc.Lng
						, ksps.LatLong = kufc.CenterLatLong
						, ksps.DateUpdated = kufc.DateCreated
						, ksps.LastModifiedBy = kufc.CreatedBy
					WHERE 1=1
						AND kufc.`CreatedBy` = {$_SESSION['userid']}
						AND kufc.Valid = 1";
			$query = $this->db->query($sql);
		
		// Delete Temporary
			
			$this->db->delete('ktv_upload_farm_client_tmp', ['CreatedBy' => $_SESSION['userid']]);

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	public function getPartnerName($PartnerID){
		$query = $this->db->select('PartnerName')->get_where('ktv_program_partner', array('PartnerID' => $PartnerID), 1);
		if ($query->num_rows() > 0) {
			return $query->row_array(0)["PartnerName"];
		}
		return false;
	}

	public function getInfoLanduseSummary($params){
		extract($params); 
		$sql = "SELECT
					IFNULL(grlk.landuse, 'Outside Area') AS Landuse,
					SUM(ggls.AreaIntersect) AS AreaHa
				FROM
					gis_garden_landuse_summary ggls
				LEFT JOIN ktv_survey_plot_polygon_geo ksppg ON 
					ggls.MemberID = ksppg.MemberID
					AND ggls.PlotNr = ksppg.PlotNr
					AND ggls.SurveyNr = ksppg.SurveyNr
					AND ggls.Revision = ksppg.Revision
				LEFT JOIN ktv_members km 			ON ggls.MemberID = km.MemberID
				LEFT JOIN ktv_village kv 			ON km.VillageID = kv.VillageID
				LEFT JOIN ktv_subdistrict ks 		ON kv.SubDistrictID = ks.SubDistrictID
				LEFT JOIN ktv_district kd 			ON ks.DistrictID = kd.DistrictID
				LEFT JOIN ktv_province kp 			ON  kd.ProvinceID = kp.ProvinceID
				LEFT JOIN gis_ref_landuse_klhk grlk ON ggls.Landuse = grlk.function_code 
				WHERE 1=1 
					AND kp.ProvinceID = {$ProvinceID}
					AND kd.DistrictID = {$DistrictID}
				GROUP BY ggls.Landuse";
		$query = $this->db->query($sql)->result_array();	

		return $query;
	}

	/**
	 * Farm polygon / point  export by excel
	 * line 2075 - 2228
	 */

	 public function importExceltmp($file) {
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);

		$this->load->library('Excel');
        $data = $this->excel->import($file);
        
        $key = array();
        $key[0] = 'FarmerID';
        $key[1] = 'FarmerName';
        $key[2] = 'FarmNr';
		$key[3] = 'SurveyNr';                

        if (!empty($data) && $data[0] === $key) {            
            unset($data[0]);
            $this->db->delete('ktv_farm_polygon_download_tmp', array('UserID' => $_SESSION['userid']));
			
            if (!empty($data)) {
                $data_insert = array();
                foreach ($data as $value) {
					if(!empty($value)){
						$tmp = array();
						$tmp['UserID'] = $_SESSION['userid'];
						$tmp['MemberDisplayID'] = $value[0];
						$tmp['MemberName'] = $value[1];
						$tmp['PlotNr'] = $value[2];
						$tmp['SurveyNr'] = $value[3];						
						$Members = $this->getMembers($tmp['MemberDisplayID']);
						
						// member id exist or not exist						  
						if ($Members !== false) {
							$tmp['MemberID'] = $Members['MemberID'];
						} else {              
							$tmp['Remark'] = 'Member ID Not Exist';          
							$data_insert[] = $tmp;							
						}
						
						$Remark = $this->getRemark($Members['MemberID'], $tmp['PlotNr'], $tmp['SurveyNr']);
						if ($Remark !== false) {
							$tmp['Remark'] = '-'; //Remark Exist
						} else {                        
							$tmp['Remark'] = "Not Exist";
							$data_insert[] = $tmp;							
						}
						// penambahan                   
						$tmp['AreaHa'] = $Remark['AreaHa'];
						$tmp['StatusCheck'] = $Remark['StatusCheck'];
						$tmp['SurveyNr'] = $tmp['SurveyNr'];
						$tmp['Polygon'] = $Remark['Polygon'];
						$tmp['PartnerName'] = $Members['PartnerName'];
						$tmp['CenterLatitude'] = $Remark['CenterLat'];
						$tmp['CenterLongitude'] = $Remark['CenterLon'];
						$tmp['StatusMember'] = $Members['StatusMember'];
						$tmp['Province'] = $Members['Province'];
						$tmp['District'] = $Members['District'];
						$tmp['SubDistrict'] = $Members['SubDistrict'];
						$tmp['Village'] = $Members['Village'];

						$this->db->insert('ktv_farm_polygon_download_tmp', $tmp);
					}
                }				
            }
        }
	}

	/**
	 * get SurveyNr, StatusCheck (polygon status), AreaHa
	 * function getRemark ($MemberID, $PlotNr)
	 * 
	 * MemberID, PlotNr, SurveyNr, Polygon, Revision, StatusCheck, AreaHa,   
	 */

	 public function getRemark($MemberID, $PlotNr, $SurveyNr) {
		$sql = "
		SELECT
		AreaHa,	StatusCheck, SurveyNr, Polygon, Revision,
		ST_X(ST_Centroid(ST_GeomFromText(ST_AsText(Polygon)))) as CenterLat,
		ST_Y(ST_Centroid(ST_GeomFromText(ST_AsText(Polygon)))) as CenterLon
		FROM ktv_survey_plot_polygon_geo
		WHERE MemberID = {$MemberID} AND PlotNr = {$PlotNr} AND SurveyNr = {$SurveyNr}
		LIMIT 1
		";
        
		$query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return false;
    }	
	
	/**
	 * get StatusMember (member status), MemberID, Province, District, SubDistrict, Village
	 */

	public function getMembers($MemberDisplayID) {
		$sql = "
		SELECT
		f.StatusMember, f.MemberID , p.Province , d.District , sd.SubDistrict , v.Village, kpp.PartnerName
		from ktv_members f 
		LEFT JOIN ktv_village v ON v.VillageID = f.VillageID
		LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
		LEFT JOIN ktv_district d ON d.DistrictID = sd.DistrictID
		LEFT JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
		LEFT JOIN ktv_program_partner kpp ON kpp.PartnerID = f.PartnerID
		WHERE f.MemberDisplayID = '{$MemberDisplayID}' LIMIT 1
		";
        
		$query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return false;
    }
	
	public function farmPolygonClientExcelClearData(){
		return $this->db->query("DELETE FROM ktv_farm_polygon_download_tmp WHERE UserID=?",  array($_SESSION['userid']));
	}

	public function getFarmPolygonClientExcel(){		
		$sql = "SELECT 
					a.`MemberDisplayID`
					, a.`MemberName`
					, a.`PlotNr`										
					, a.`Remark`
					, a.`AreaHa`
					, a.`StatusCheck`
					, a.`SurveyNr`
					, a.`CenterLatitude`
					, a.`CenterLongitude`
					, ST_ASGEOJSON(a.Polygon) as `Polygon`
					, a.`PartnerName`
					, a.`StatusMember`
					, a.`Province`
					, a.`District`
					, a.`SubDistrict`
					, a.`Village`
				FROM ktv_farm_polygon_download_tmp a
				WHERE UserID = ?
				";
		$query = $this->db->query($sql, array($_SESSION['userid']));

		if ($query->num_rows() > 0) {
			$return['data'] = $query->result_array();
			$query = $this->db->query("SELECT FOUND_ROWS() AS total");
			$return['total'] = $query->row_array(0)['total'];
			return $return;
		}
		return true;
	}
}
/* End of file mmaps.php */
/* Location: ./application/models/mmaps.php */
// -- JOIN ktv_survey_plot_status p ON m.MemberID = p.MemberID AND a.FarmNr = p.PlotNr
