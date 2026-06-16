<?php
class Mreport_sawit_terampil extends CI_Model
{
	
	function __construct()
	{
		parent::__construct();
	}

     public function getWaveJB() {
          $sql = "SELECT 
               a.ProgID AS id
               , concat(a.ProgramName,' (',a.ProgramYear,')') AS `name`
          FROM
               ktv_first_buyer_program a 
          WHERE 
               a.StatusCode = 'active' 
          -- AND a.CertProgID = 1
          -- AND a.FirstBuyerID = 2
          ";
          $query = $this->db->query($sql);
          if ($query->num_rows() > 0) {
              return $query->result_array();
          }
          return false;
     }

	
	public function ReadComboMonthYear($pfilter)
	{
		// if ($pfilter['showProcessDate'] == 'reported') {
		// 	$sqlfilter = " where a.ReportStatus = 1";
		// } else {
			$sqlfilter = "";
		// }
		$sql = "SELECT a.id, a.ReportName, CONCAT(MONTHNAME(a.DateProcess), ' ',  DAY(a.DateProcess), ', ', YEAR(a.DateProcess)) as monthnmyears, 
        CONCAT(MONTH(a.DateProcess), '-', YEAR(a.DateProcess)) as monthyears, DATE(a.DateProcess) as DateProcess, a.ReportStatus FROM `ktv_certification_progress_st_process` as a
        $sqlfilter
        order by DateProcess DESC";
		$query = $this->db->query($sql);
		$result['data'] = $query->result_array();
		$result['bugLog'] = $pfilter['showProcessDate'];
		// $result['sqlLog'] = $this->db->last_query();
		return $result;
	}

     public function getCmbStoreProcedureSawitTerampil($ProgID) {
          $sql = "SELECT
                      a.id,
                      a.StoreProcedureName AS label
                  FROM ktv_certification_progress_st_process_program_calc a
                  WHERE 1=1
                      AND a.ProgID = {$ProgID}
                      AND a.StatusCode = 'active'
                  ORDER BY a.OrderNo ASC;";
          $query = $this->db->query($sql);
          return $query->result_array();
     }

     public function GridMainCalculateSawit($ProgID, $sortingField = "", $sortingDir = "") {
          if ($sortingField == "")
              $sortingField = 'OrderNo';
          if ($sortingDir == "")
              $sortingDir = 'ASC';
  
          //========== Search (Begin) =====================
          $sqlSearch = "";
          if ($ProgID != 0) {
              $sqlSearch .= " AND a.ProgID = {$ProgID}";
          }
          //========== Search (End) =====================
  
          $sql = "SELECT SQL_CALC_FOUND_ROWS   
                      a.id
                      , a.ProgID
                      , a.StoreProcedureName
                      , a.DateGenerated
                      , a.OrderNo
                  FROM
                      `ktv_certification_progress_st_process_program_calc` a
                  WHERE 1=1
                      AND a.`StatusCode` = 'active'
                      AND DATE(a.DateGenerated) = DATE(NOW())
                      $sqlSearch
                  ORDER BY `$sortingField` $sortingDir";
          $query = $this->db->query($sql);
          $result['data'] = $query->result_array();
  //        $result['query'] = $this->db->last_query();
  
          $query = $this->db->query('SELECT FOUND_ROWS() AS total');
          $result['total'] = $query->row()->total;     
          return $result;
     }

     public function CalculateSawitDinamis($ProgID, $DateProcess, $StoreProcedureName) {
          $results = array();
          $this->db->trans_begin();
          // param order terakhir
          $results['max_order'] = false;
          
          $sql = "Call {$StoreProcedureName}({$ProgID}, '{$DateProcess}')";
          $call = $this->db->query($sql);
          
          $sql = "SELECT OrderNo FROM ktv_certification_progress_st_process_program_calc WHERE ProgID = ? AND StoreProcedureName = ?";
          $cal_order = $this->db->query($sql, array($ProgID, $StoreProcedureName))->row_array();
          
          
          $sql = "SELECT MAX(OrderNo) max_order FROM ktv_certification_progress_st_process_program_calc WHERE ProgID = ?";
          $max_order = $this->db->query($sql, array($ProgID))->row_array();
          
          if ($call) {
              $sql = "UPDATE ktv_certification_progress_st_process_program_calc SET DateGenerated = NOW() WHERE ProgID = ? AND StoreProcedureName = ?";
              $prog_calc = $this->db->query($sql, array($ProgID, $StoreProcedureName));
          }
          
          // Calculasi terakhir untuk summary
          if ($cal_order['OrderNo'] == $max_order['max_order']) {
              $sql = "SELECT id FROM ktv_certification_progress_st_process WHERE DATE(DateProcess) = ? LIMIT 1";
              $process = $this->db->query($sql, array($DateProcess))->row_array();
              
              if (empty($process)) {
                  $sql = "INSERT INTO `ktv_certification_progress_st_process` SET
                      `DateProcess` = ?,
                      `ReportStatus` = 0,
                      `CreatedBy` = ?,
                      `DateCreated` = NOW()
                      ";
                  $cargill_process = $this->db->query($sql, array($DateProcess, $_SESSION['userid']));
                  $LockID = $this->db->insert_id();
              } else {
                  $LockID = $process['id'];
              }
              
              $sql = "INSERT INTO `ktv_certification_progress_st_process_program` SET
                  `ProgID` = ?,
                  `LockID` = ?,
                  `StatusCode` = 'active',
                  `CreatedBy` = ?,
                  `DateCreated` = NOW()
                  ";
              $prog_calc = $this->db->query($sql, array($ProgID, $LockID, $_SESSION['userid']));
              
              $sql = "UPDATE ktv_certification_progress_st_process_program_calc SET DateGenerated = NULL WHERE ProgID = ?";
              $prog_calc = $this->db->query($sql, array($ProgID));
              $results['max_order'] = true;
          }
          
          if ($this->db->trans_status() === false) {
              $this->db->trans_rollback();
              $results['success'] = false;
              $results['message'] = lang("Failed to calculate data");
          } else {
              $this->db->trans_commit();
              $results['success'] = true;
              $results['message'] = lang("Data Calculated!");
          }
          return $results;
     }
	
	// public function ReadComboYear()
    // {
    //     $sql = "select DISTINCT(a.`Year`) as year 
    //         from ktv_certification_progress_jbcocoa_ims_district_history as a
    //         order by year";
    //     $query = $this->db->query($sql);
    //     $result['data'] = $query->result_array();
    //     return $result;
    // }
	
	// public function ReadComboMonth()
    // {
    //     $arrmonth = array();
    //     for($i=1; $i <= 12; $i++){
    //         $arrmonth[] =  ["month"   => $i];
    //     }
    //     $result['data'] = $arrmonth; 
    //     return $result;
    // }
	
	public function GetSawitTerampilMainGrid($pSearch, $start, $limit)
	{
		$sql = "SELECT SQL_CALC_FOUND_ROWS
               d.ProgramName as 'Batch',
               b.`Year` as 'Year',
               pc.`ClusterName`,
               b.`Province`,
               IFNULL(a.KsMill,0) + IFNULL(a.StMill,0) 'AchievedPalmoilMill',
               IFNULL(a.FarmerReg,0) 'AchievedFarmerReg',
               IFNULL(a.FarmReg,0) 'AchievedFarmReg',
               IFNULL(a.Ha,0) 'AchievedHa',
               IFNULL(a.SocSel,0) 'AchievedSocSel',
               IFNULL(a.FarmerSurveyBP,0) 'AchievedFarmerSurveyBP',
               IFNULL(a.FarmSurvey,0) 'AchievedFarmSurvey',
               IFNULL(a.Polygon,0) 'AchievedPolygon',
               IFNULL(a.FarmerCoach,0) 'AchievedFarmerCoach',
               IFNULL(a.CoachingSess,0) 'AchievedCoachingSess',
               IFNULL(a.Sms,0) 'AchievedSms',
               IFNULL(a.IdCard,0) 'AchievedIdCard',
               IFNULL(a.FarmX,0) 'AchievedFarmX',
               IFNULL(a.FarmG,0) 'AchievedFarmG',
               IFNULL(a.FarmR,0) 'AchievedFarmR',
               IFNULL(a.FarmC,0) 'AchievedFarmC',
               IFNULL(b.KsMill,0) + IFNULL(b.StMill,0) 'TargetedPalmoilMill',
               IFNULL(b.FarmerReg,0) 'TargetedFarmerReg',
               IFNULL(b.FarmReg,0) 'TargetedFarmReg',
               IFNULL(b.Ha,0) 'TargetedHa',
               IFNULL(b.SocSel,0) 'TargetedSocSel',
               IFNULL(b.FarmerSurveyBP,0) 'TargetedFarmerSurveyBP',
               IFNULL(b.FarmSurvey,0) 'TargetedFarmSurvey',
               IFNULL(b.Polygon,0) 'TargetedPolygon',
               IFNULL(b.FarmerCoach,0) 'TargetedFarmerCoach',
               IFNULL(b.CoachingSess,0) 'TargetedCoachingSess',
               IFNULL(b.Sms,0) 'TargetedSms',
               IFNULL(b.IdCard,0) 'TargetedIdCard',
               IFNULL(b.FarmX,0) 'TargetedFarmX',
               IFNULL(b.FarmG,0) 'TargetedFarmG',
               IFNULL(b.FarmR,0) 'TargetedFarmR',
               IFNULL(b.FarmC,0) 'TargetedFarmC',
               b.`DateUpdated`,
               a.`DateUpdated` as DateUpdatedHis,
               c.`UserRealName` AS LastModifiedBy
          FROM 
               `ktv_certification_progress_st_ims_district_history` a
          LEFT JOIN 
               `ktv_kpi_st_certification_target_ims_district` b ON b.`ClusterID`=a.`ClusterID` AND b.`ProgID`=a.`ProgID` AND b.`Year`=a.`Year`
          LEFT JOIN 
               sys_user c ON c.`UserId`=a.`LastModifiedBy`
          LEFT JOIN 
               ktv_first_buyer_program as d ON d.ProgID = a.ProgID
          LEFT JOIN
               ktv_first_buyer_program_cluster pc on pc.ClusterID = a.ClusterID
          WHERE 1 = 1 
          --filter--
          ORDER BY b.TargetID
          Limit ?,?";
		$filter = '';
        // if(isset($pSearch['filterYears']) && $pSearch['filterYears'] != ""){
        // if(isset($pSearch['filterYears']) && $pSearch['filterYears'] != ""){
        //     $filter .= " AND YEAR(a.DateUpdated) = {$pSearch['filterYears']} ";
        // }
        // if(isset($pSearch['filterMonths']) && $pSearch['filterMonths'] != ""){
        //     $filter .= " AND MONTH(a.DateUpdated) = {$pSearch['filterMonths']} ";
        // }
		if (isset($pSearch['filterMonthYears']) && $pSearch['filterMonthYears'] != "") {
            // $arrdate = explode("-",$pSearch['filterMonthYears']);
            // $filter .= " AND MONTH(a.DateUpdated) = '".$arrdate[0]."' AND YEAR(a.DateUpdated) = '".$arrdate[1]."'";
			$filter .= " AND DATE(a.DateUpdated) = DATE('" . $pSearch['filterMonthYears'] . "')";
		}
		$sql = str_replace('--filter--', $filter, $sql);
		$query = $this->db->query($sql, array((int)$start, (int)$limit));
		$tmp_qry = $this->db->last_query();
        
		//ini untuk ambil jumlah query sebelum dilimit
		$sql_total = "SELECT FOUND_ROWS() AS total";
		$query_total = $this->db->query($sql_total);
		if ($query->num_rows() > 0) {  //ini ambil dari variable $query diatas
			$total = $query_total->row_array(0);
			return array(
				'data' => $query->result_array(),
				'total' => $total['total'],
				'debugsql' => $tmp_qry
			);
		}
	}
	
	public function GetClassification()
	{
		$arrClassification = [
			// ["classification" => "CT 1.1", "classificationValue" => "SEL"], //Farmers Selected from Socializations
			// ["classification" => "CT 1.2", "classificationValue" => "ICS1Hectare"],
			// ["classification" => "CT 1.3", "classificationValue" => "HaveSmartphone"],
			// ["classification" => "CT 2.1.1 / CT 2.1.2", "classificationValue" => "TC"],
			// ["classification" => "CT 2.1.4", "classificationValue" => "PrinterPaper"],
			// ["classification" => "CT2.2.1-2.2.3 2.2.1/2.2.2/2.2.3", "classificationValue" => "AFL"],
			// ["classification" => "CT 3.1", "classificationValue" => "ICS0"],
			// ["classification" => "CT 3.2", "classificationValue" => "ICS1"],
			// ["classification" => "CT 3.3", "classificationValue" => "ICS2"],
			// ["classification" => "Certified Certificate Holder", "classificationValue" => "CertifiedCH"],
			// ["classification" => "Certified Buying Unit", "classificationValue" => "CertifiedTrader"],
			// ["classification" => "Master Training", "classificationValue" => "MasterTraining"],
			// ["classification" => "IMS Training", "classificationValue" => "IMSTraining"],
			// ["classification" => "IMS Support Year One", "classificationValue" => "IMSSupport1"],
			// ["classification" => "IMS Support Year Two", "classificationValue" => "IMSSupport2"],
			// ["classification" => "Socialization", "classificationValue" => "SOC"],
			// ["classification" => "Farmers Selected for Training", "classificationValue" => "SELYes"],
			// ["classification" => "Farmers Selected for Training Year Two", "classificationValue" => "SELY2Yes"],
			// ["classification" => "GAP", "classificationValue" => "GAP"],
			// ["classification" => "GAP Refresh Year One", "classificationValue" => "GAPR"],
			// ["classification" => "CoC", "classificationValue" => "COC"],
			// ["classification" => "CoC Refresh Year One", "classificationValue" => "COCR1"],
			// ["classification" => "CoC Refresh Year Two", "classificationValue" => "COCR2"],
			// ["classification" => "Achieved Farmers Selected for ICS", "classificationValue" => "FinalTraining"],
			// ["classification" => "ICS 0 Active Join", "classificationValue" => "ICS0ActiveJoin"],
			// ["classification" => "ICS 0 Active Not Join", "classificationValue" => "ICS0ActiveNotJoin"],
			// ["classification" => "ICS 0 Inactive ", "classificationValue" => "ICS0NotActive"],
			// ["classification" => "Farmer Certified", "classificationValue" => "CFL"],
			// ["classification" => "TC Year One", "classificationValue" => "TC1"],
			// ["classification" => "TC Year Two", "classificationValue" => "TC2"],
			// ["classification" => "ICS Equipment", "classificationValue" => "ICSEquipment"],
			// ["classification" => "Progress Updated Garden Status", "classificationValue" => "ProgressUpdatedFarmer"],
			// ["classification" => "Progress Garden", "classificationValue" => "ProgressGarden"],
			// ["classification" => "Progress Polygon", "classificationValue" => "ProgressPolygon"],
			// ["classification" => "Progress Post Harvest", "classificationValue" => "ProgressPostHarvest"],
			// ["classification" => "Progress PPI", "classificationValue" => "ProgressPPI"],
			// ["classification" => "Progress Certification", "classificationValue" => "ProgressCertification"],
			// ["classification" => "Progress Audit Log", "classificationValue" => "ProgressAuditLog"],
			// ["classification" => "Gross Sales", "classificationValue" => "GrossSales"],
			// ["classification" => "Net Sales", "classificationValue" => "NetSales"]
			// ["classification" => "SELY2", "classificationValue" => "SELY2"],
          ];

		$result['data'] = $arrClassification;
		return $result;
	}
	
	public function getCertificationProgress($fDate)
	{
		$sql = "SELECT
        b.`IMSID`,
        b.`Year` as 'Year',
        b.`CertificateHolder`,
        b.`Province`,
        b.`District`,
        b.`Location` AS WorkArea,
        b.`CertificationHolders` AS TargetedCertificateHolder,
        IFNULL(a.`CertificationHolders`,0) AS AchievedCertificateHolder,
        b.`Traders` AS TargetedBuyingUnit,  
        IFNULL(a.`Traders`,0) AS AchievedBuyingUnit,
        b.`MasterTrainings` AS TargetedMasterTrainings,
        IFNULL(a.`MasterTotal`,0) AS AchievedMasterTrainings,
        b.`IMSTrainings` AS TargetedIMSTrainings,
        IFNULL(a.`IMStrainings`,0) AS AchievedIMSTrainings,
        b.`IMSSupport` AS TargetedIMSSupport,
        TRUNCATE(IFNULL(a.`IMSsupport`,0)/1000,2) AS AchievedIMSSupport,
        b.`SOL` AS TargetedSOC,
       IFNULL( a.`SOL`,0) AS AchievedSOC,
        (b.`SEL`+b.`SELY2`) AS TargetedFarmersSelectedForTraining,
        (IFNULL(a.`SELYes`,0)+IFNULL(a.`SELY2Yes`,0)) AS AchievedFarmersSelectedForTraining,
        b.`GAP` AS TargetedGAPYear1,
        IFNULL(a.`GAP`,0) AS AchievedGAPYear1,
        b.`GAPR` AS TargetedGAPYear1Refresh,
        IFNULL(a.`GAPR`,0) AS AchievedGAPYear1Refresh,
        b.`COC` AS TargetedCOC,
        IFNULL(a.`COC`,0) AS AchievedCOC,
        b.`COCR1` AS TargetedCOCYear1Refresh,
        IFNULL(a.`COCR1`,0) AS AchievedCOCYear1Refresh,
        b.`COCR2` AS TargetedCOCYear2Refresh,
        IFNULL(a.`COCR2`,0) AS AchievedCOCYear2Refresh,
        b.`TrainingFinal` AS TargetedFarmersSelectedForICS,
        IFNULL(a.`TrainingFinal`,0) AS AchievedFarmersSelectedForICS,
        b.`ICS0` AS TargetedICS0,
        IFNULL(a.`ICS0ActiveJoin`,0) AS AchievedICS0ActiveJoin, /* a.ICS0 AS AchievedICSFarmer  */
        IFNULL(a.`ICS0ActiveNotJoin`,0) AS AchievedICS0ActiveNotJoin, /* hapus  */
        IFNULL(a.`ICS0Inactive`,0) AS AchievedICS0Inactive, /* hapus  */
        b.`ICS1` AS TargetedICS1,
        IFNULL(a.`ICS1`,0) AS AchievedICS1,
        b.`ICS2` AS TargetedICS2,
        IFNULL(a.`ICS2`,0) AS AchievedICS2,
        b.`AFL1` AS TargetedAFL,
        IFNULL(a.`AFL1`,0) AS AchievedAFL,
        b.`FarmerCertifiedFinal` AS TargetedFarmerCertified,
        IFNULL(a.`FarmerCertifiedFinal`,0) AS AchievedFarmerCertified,
        b.`TC` AS TargetedTC,
        IFNULL(ROUND((a.`TC`/1000),2),0) AS AchievedTC,
        b.`ICSEquipment` AS TargetedICSEquipment,
        IFNULL(a.`ICSEquipment`,0) AS AchievedICSEquipment,
        IFNULL(a.`ProgressUpdatedFarmer`,0) AS ProgressUpdatedFarmer,
        IFNULL(a.`ProgressUpdatedGardenStatus`,0) AS ProgressUpdatedGardenStatus,  
        IFNULL(a.`ProgressGarden`,0) AS ProgressGarden,
        IFNULL(a.`ProgressPolygon`,0) AS ProgressPolygon,
        IFNULL(a.`ProgressPostHarvest`,0) AS ProgressPostHarvest,
        IFNULL(a.`ProgressPPI`,0) AS ProgressPPI,
        IFNULL(a.`ProgressCertification`,0) AS ProgressCertification,
        IFNULL(a.`ProgressAuditLog`,0) AS ProgressAuditLog,
        TRUNCATE(IFNULL(a.`CertifiedBruto`,0)/1000,2) AS AchievedGrossSales,
        TRUNCATE(IFNULL(a.`CertifiedNetto`,0)/1000,2) AS AchievedNetSales,
        b.`DateUpdated`,
        a.`DateUpdated` as DateUpdatedHis,
        c.`UserRealName` AS LastModifiedBy
      FROM `ktv_certification_progress_jbcocoa_ims_district_history` a
      LEFT JOIN `ktv_kpi_jbcocoa_certification_target_ims_district` b ON b.`IMSID`=a.`IMSID` AND b.`DistrictID`=a.`DistrictID` AND b.`Year`=a.`Year`
      LEFT JOIN sys_user c ON c.`UserId`=a.`LastModifiedBy`
      WHERE 1 = 1
      --filter--
      ORDER BY b.TargetID";
		$filter = '';
		if (isset($fDate) && $fDate != "") {
            // $arrdate = explode("-",$fDate);
            // $filter .= " AND MONTH(a.DateUpdated) = '".$arrdate[0]."' AND YEAR(a.DateUpdated) = '".$arrdate[1]."'";
			$filter .= " AND DATE(a.DateUpdated) = DATE('" . $fDate . "')";
		}
		$sql = str_replace('--filter--', $filter, $sql);
		$query = $this->db->query($sql);
		$result['data'] = $query->result_array();

		$sqlfDatenm = "SELECT CONCAT(MONTHNAME('" . $fDate . "'), '_', YEAR('" . $fDate . "')) as monthnmyears";
		$queryFDatenm = $this->db->query($sqlfDatenm);
		$monthnmyears = "";
		if ($queryFDatenm->num_rows() > 0) {
			$arrFDatenm = $queryFDatenm->row_array(0);
			$monthnmyears = $arrFDatenm['monthnmyears'];
		}
		$result['fDateNm'] = $monthnmyears;

		return $result;
	}
	
	public function getCertificationDetailProgress($fDate, $fClass)
	{
		$matrixTable = array(
			"AFL" => "ktv_jbcocoa_summary_approved_farmer_list",
               "CertifiedTrader" => "ktv_jbcocoa_summary_certified_buying_unit",
               "CertifiedCH" => "ktv_jbcocoa_summary_certified_certificate_holder",
               "CFL" => "ktv_jbcocoa_summary_certified_farmer_list",
               "COC" => "ktv_jbcocoa_summary_coc",
               "COCR1" => "ktv_jbcocoa_summary_coc_refresh_one",
               "COCR2" => "ktv_jbcocoa_summary_coc_refresh_two",
               "FinalTraining" => "ktv_jbcocoa_summary_final_training",
               "GAP" => "ktv_jbcocoa_summary_gap_first_year",
               "GAPR" => "ktv_jbcocoa_summary_gap_refresh",
               "ICSEquipment" => "ktv_jbcocoa_summary_ics_equipment",
               "ICS1" => "ktv_jbcocoa_summary_ics_one",
               "ICS2" => "ktv_jbcocoa_summary_ics_two",
               "ICS0" => "ktv_jbcocoa_summary_ics_zero",
               "ICS0ActiveJoin" => "ktv_jbcocoa_summary_ics_zero_active_join",
               "ICS0ActiveNotJoin" => "ktv_jbcocoa_summary_ics_zero_active_not_join",
               "ICS0NotActive" => "ktv_jbcocoa_summary_ics_zero_notactive",
               "IMSSupport1" => "ktv_jbcocoa_summary_ims_support",
               "IMSTraining" => "ktv_jbcocoa_summary_ims_training",
               "MasterTraining" => "ktv_jbcocoa_summary_master_training",
               "MasterTrainingDistrict" => "ktv_jbcocoa_summary_master_training_per_district",
               "ProgressAuditLog" => "ktv_jbcocoa_summary_progress_auditlog",
               "ProgressCertification" => "ktv_jbcocoa_summary_progress_certification",
               "ProgressUpdatedFarmer" => "ktv_jbcocoa_summary_progress_farmer_updated",
               "ProgressGarden" => "ktv_jbcocoa_summary_progress_garden",
               "ProgressUpdatedGardenStatus" => "ktv_jbcocoa_summary_progress_garden_status_updated",
               "GrossSales" => "ktv_jbcocoa_summary_progress_gross_sales",
               "NetSales" => "ktv_jbcocoa_summary_progress_net_sales",
               "ProgressPolygon" => "ktv_jbcocoa_summary_progress_polygon",
               "ProgressPostHarvest" => "ktv_jbcocoa_summary_progress_post_harvest",
               "ProgressPPI" => "ktv_jbcocoa_summary_progress_ppi",
               "SEL" => "ktv_jbcocoa_summary_selection",
               "SELY2" => "ktv_jbcocoa_summary_selection_second_year",
               "SELY2Yes" => "ktv_jbcocoa_summary_selection_second_year_yes",
               "SELYes" => "ktv_jbcocoa_summary_selection_yes",
               "SOC" => "ktv_jbcocoa_summary_socialization",
               "TC1" => "ktv_jbcocoa_summary_tc_year_two"
		);

		if (isset($matrixTable[$fClass])) {
			$filter = "";
			if ($fClass == 'TC1') {
				$filter .= " AND a.ProgID = '1' ";
			}
			if ($fClass == 'TC2') {
				$filter .= " AND a.ProgID = '2' ";
			}
			if ($fClass == 'IMSSupport1') {
				$filter .= " AND a.ProgID = '1' ";
			}
			if ($fClass == 'IMSSupport2') {
				$filter .= " AND a.ProgID = '2' ";
			}
			if ($fClass == 'FinalTraining') {
				$filter .= " AND a.FinalTrainingStatus = 'Passed' ";
			}
			$sqlfDatenm = "SELECT CONCAT(MONTHNAME('" . $fDate . "'), '_', YEAR('" . $fDate . "')) as monthnmyears";
			$queryFDatenm = $this->db->query($sqlfDatenm);
			$monthnmyears = "";
			if ($queryFDatenm->num_rows() > 0) {
				$arrFDatenm = $queryFDatenm->row_array(0);
				$monthnmyears = $arrFDatenm['monthnmyears'];
			}
			$sql = "SELECT * FROM " . $matrixTable[$fClass] . " as a 
            where a.DateGenerated = '" . $fDate . "'
            --filter--
            order by a.IMSID";
			$sql = str_replace('--filter--', $filter, $sql);
			$query = $this->db->query($sql);
			$result['data'] = $query->result_array();
			$result['fDateNm'] = $monthnmyears;
            // $result['debuglog'] = $monthnmyears;

			return $result;
		}
		return;
	}
	
	public function updateProcessDate($processId, $ReportStatus, $ReportName, $userid)
	{
		if ($ReportStatus == "true") {
			$ReportStatusNum = 1;
		} else if ($ReportStatus == "false") {
			$ReportStatusNum = 0;
		}
		$sql = "UPDATE ktv_certification_progress_st_process SET ReportStatus=? , ReportName=?
        WHERE id=?";

		$query = $this->db->query($sql, array($ReportStatusNum, $ReportName, $processId));
		if ($query) {
			$results['success'] = true;
			$results['message'] = "record updated.";
		} else {
			$results['success'] = false;
			$results['message'] = "Failed to update record";
		}
		return $results;
	}
		
	public function doKPICalculation1($args)
	{

          $resultcalc = array();
          $arrStep = array();
//          array_push($resultcalc, $this->insert_detail_AFL());
//          $arrStep[] = " AFL done";
		array_push($resultcalc, $this->insert_detail_FarmerBasic());
          $arrStep[] = " Farmerbasic Wave III & IV done";
//		array_push($resultcalc, $this->insert_detail_CertifiedCH());
//          $arrStep[] = " CertifiedCH done";
//		array_push($resultcalc, $this->insert_detail_SEL()); //untuk sementara
//          $arrStep[] = " SEL done";
//		array_push($resultcalc, $this->insert_detail_CFL());
//          $arrStep[] = " CFL done";
		array_push($resultcalc, $this->insert_detail_CFL_Wave3());
          $arrStep[] = " CFL Wave III & IV done";
//		array_push($resultcalc, $this->insert_detail_CertifiedTrader());
//          $arrStep[] = " Buying Unit done";
//		array_push($resultcalc, $this->insert_detail_COC());
//          $arrStep[] = " COC done";
		array_push($resultcalc, $this->insert_detail_COC_Wave3());
          $arrStep[] = " COC Wave III & IV done";
//		array_push($resultcalc, $this->insert_detail_COCR1());
//          $arrStep[] = " COCR1 done";

		if ($resultcalc) {
			$results['success'] = true;
			$results['message'] = $resultcalc;
			$results['step'] = array("Details : Step 1",$arrStep);
		} else {
			$results['success'] = false;
			$results['message'] = "Failed to update record";
			$results['step'] = "";
		}
		return $results;
	}
	
	public function doKPICalculation2($args)
	{

          $resultcalc = array();
          $arrStep = array();
		array_push($resultcalc, $this->insert_detail_FarmXUser());
          $arrStep[] = " FarmXtension User done";
		array_push($resultcalc, $this->insert_detail_FarmGUser());
          $arrStep[] = " FarmGate User done";
//		array_push($resultcalc, $this->insert_detail_COCR2());
//          $arrStep[] = " COCR2 done";
//		array_push($resultcalc, $this->insert_detail_FinalTraining());
//          $arrStep[] = " Final Training done";
//		array_push($resultcalc, $this->insert_detail_GAP());
//          $arrStep[] = " GAP done";
//          array_push($resultcalc, $this->insert_detail_GAPR());
//          $arrStep[] = " GAPR done";
//          array_push($resultcalc, $this->insert_detail_IMSSupport());
//          $arrStep[] = " IMS Support done";
		// array_push($resultcalc, $this->insert_detail_IMSTraining()); lewat dulu

		if ($resultcalc) {
			$results['success'] = true;
			$results['message'] = $resultcalc;
			$results['step'] = array("Details : Step 2",$arrStep);
		} else {
			$results['success'] = false;
			$results['message'] = "Failed to update record";
			$results['step'] = "";
		}
		return $results;
	}
	
	public function doKPICalculation3($args)
	{

          $resultcalc = array();
          $arrStep = array();
//          array_push($resultcalc, $this->insert_detail_ICSEquipment());
//          $arrStep[] = " ICS Equipment done";
//          array_push($resultcalc, $this->insert_detail_ICS1());
//          $arrStep[] = " ICS1 done";
//          array_push($resultcalc, $this->insert_detail_ICS1Hectare());
//          $arrStep[] = " ICS1 Hectare done";
//          array_push($resultcalc, $this->insert_detail_ICS1Hectare_Wave3());
//          $arrStep[] = " ICS1 Hectare done Wave III & I ";
          array_push($resultcalc, $this->insert_detail_ICS1Hectare_Wave4());
          $arrStep[] = " ICS1 Hectare Wave IV done";
//		array_push($resultcalc, $this->insert_detail_ICS2());
//          $arrStep[] = " ICS2 done";
		array_push($resultcalc, $this->insert_detail_ICS2_Wave3());
          $arrStep[] = " ICS2 Wave III & IV done";
//		array_push($resultcalc, $this->insert_detail_ICS0());
//          $arrStep[] = " ICS0 done";
		array_push($resultcalc, $this->insert_detail_IMSAFL1_Wave3());
          $arrStep[] = " AFL1 Wave III & IV done";
//          array_push($resultcalc, $this->insert_detail_ICS0ActiveJoin());
//          $arrStep[] = " ICS0 ActiveJoin done";
//          array_push($resultcalc, $this->insert_detail_ICS0ActiveNotJoin());
//          $arrStep[] = " ICS0 ActiveNotJoin done";

		if ($resultcalc) {
			$results['success'] = true;
			$results['message'] = $resultcalc;
			$results['step'] = array("Details : Step 3",$arrStep);
		} else {
			$results['success'] = false;
			$results['message'] = "Failed to update record";
			$results['step'] = "";
		}
		return $results;
	}
	
	public function doKPICalculation4($args)
	{

          $resultcalc = array();
          $arrStep = array();
          $hasil['calc'] = "Data updated.";
          array_push($resultcalc, $hasil);
                $arrStep[] = " Data Updated done";
//          array_push($resultcalc, $this->insert_detail_ICS0NotActive());
//          $arrStep[] = " ICS0 Not Active done";
//          array_push($resultcalc, $this->insert_detail_progress_ppi());
//          $arrStep[] = " Progress PPI done";
//		array_push($resultcalc, $this->insert_detail_progress_certification());
//          $arrStep[] = " Progress Certification done";
//		array_push($resultcalc, $this->insert_detail_progress_auditlog());
//          $arrStep[] = " Progress Audit Log done";
          // array_push($resultcalc, $this->insert_detail_MasterTraining()); // ini pakai setelah Ims Training  Jalan
          // $arrStep[] = " Master Training done";
//          array_push($resultcalc, $this->insert_detail_SELY2()); // sementara
//          $arrStep[] = " SELY2 done";
//          array_push($resultcalc, $this->insert_detail_progress_garden());
//          $arrStep[] = " Progress Garden done";
//          array_push($resultcalc, $this->insert_detail_progress_polygon());
//          $arrStep[] = " Progress Polygon done";
//          array_push($resultcalc, $this->insert_detail_progress_post_harvest());
//          $arrStep[] = " Progress Post Harvest done";

		if ($resultcalc) {
			$results['success'] = true;
			$results['message'] = $resultcalc;
			$results['step'] = array("Details : Step 4",$arrStep);
		} else {
			$results['success'] = false;
			$results['message'] = "Failed to update record";
			$results['step'] = "";
		}
		return $results;
	}
	
	public function doKPICalculation5($args)
	{

		$resultcalc = array();
		$arrStep = array();
//          array_push($resultcalc, $this->insert_detail_SELY2Yes()); //untuk sementara
//          $arrStep[] = " SELY2Yes done";
//		array_push($resultcalc, $this->insert_detail_SELYes()); // untuk sementara
//          $arrStep[] = " SELYes done";
//		array_push($resultcalc, $this->insert_detail_SOC()); // untuk sementara
//          $arrStep[] = " SOC done";
		array_push($resultcalc, $this->insert_detail_SOC_Wave3()); // untuk sementara
          $arrStep[] = " SOC Wave III & IV done";
//          array_push($resultcalc, $this->insert_detail_TC1()); // untuk sementara
//          $arrStep[] = " TC1 done";
//          array_push($resultcalc, $this->insert_detail_TC2());
//          $arrStep[] = " TC2 done";
          
		if ($resultcalc) {
			$results['success'] = true;
			$results['message'] = $resultcalc;
			$results['step'] = array("Details : Step 5",$arrStep);
		} else {
			$results['success'] = false;
			$results['message'] = "Failed to update record";
			$results['step'] = "";
		}
		return $results;
	}
	
	public function doKPICalculation6($args)
	{

		$resultcalc = array();
		$arrStep = array();
          $hasil['calc'] = "Data updated.";
          array_push($resultcalc, $hasil);
                $arrStep[] = " Data Updated done";
//          array_push($resultcalc, $this->insert_detail_progress_farmer_updated());
//          $arrStep[] = " Progress Farmer Updated done";
//          array_push($resultcalc, $this->insert_detail_progress_garden_status_updated());
//          $arrStep[] = " Progress Garden Status done";
//		array_push($resultcalc, $this->insert_detail_progress_gross_sales());
//          $arrStep[] = " Progress Gross Sales done";
//          array_push($resultcalc, $this->insert_detail_progress_net_sales());
//          $arrStep[] = " Progress Net Sales done";
		if ($resultcalc) {
			$results['success'] = true;
			$results['message'] = $resultcalc;
			$results['step'] = array("Details : Step 6",$arrStep);
		} else {
			$results['success'] = false;
			$results['message'] = "Failed to update record";
			$results['step'] = "";
		}
		return $results;
	}
	
	public function doKPICalculation7($args)
	{

          $resultcalc = array();
          $arrStep = array();
//          array_push($resultcalc, $this->calcAFL1FromDetail());
//          $arrStep[] = " AFL done";
          array_push($resultcalc, $this->calcFarmerBasicFromDetail());
          $arrStep[] = " FarmerBasic Wave III & IV done";
//          array_push($resultcalc, $this->calcCertifiedCHFromDetail());
//          $arrStep[] = " CertifiedCH done";
//          array_push($resultcalc, $this->calcRegisteredCHFromDetail());
//          $arrStep[] = " RegCertifiedCH done";
//          array_push($resultcalc, $this->calcSELFromDetail());
//          $arrStep[] = " SEL done";
//		array_push($resultcalc, $this->calcCFLFromDetail());
//          $arrStep[] = " CFL done";
		array_push($resultcalc, $this->calcCFLFromDetailWave3());
          $arrStep[] = " CFL Wave III & IV done";
//		array_push($resultcalc, $this->calcCertifiedTraderFromDetail());
//          $arrStep[] = " Buying Unit done";
//          array_push($resultcalc, $this->calcRegisteredTraderFromDetail());
//          $arrStep[] = " Buying Unit done";
//		array_push($resultcalc, $this->calcCOCFromDetail());
//          $arrStep[] = " COC done";
//		array_push($resultcalc, $this->calcCOCR1FromDetail());
//          $arrStep[] = " COCR1 done";
//		array_push($resultcalc, $this->calcCOCR2FromDetail());
//          $arrStep[] = " COCR2 done";
		array_push($resultcalc, $this->calcCOCFromDetailWave3());
          $arrStep[] = " COC Wave III & IV done";
//		array_push($resultcalc, $this->calcFinalTrainingFromDetail());
//          $arrStep[] = " Final Training done";
//		array_push($resultcalc, $this->calcGAPFromDetail());
//          $arrStep[] = " GAP done";
		if ($resultcalc) {
			$results['success'] = true;
			$results['message'] = $resultcalc;
               $results['step'] = array("Summary : Step 1",$arrStep);          
		} else {
			$results['success'] = false;
			$results['message'] = "Failed to update record";
			$results['step'] = "";
		}
		return $results;
	}
	
	public function doKPICalculation8($args)
	{

          $resultcalc = array();
          $arrStep = array();
//          array_push($resultcalc, $this->calcGAPRFromDetail());
//          $arrStep[] = " GAPR done";
		array_push($resultcalc, $this->calcIMSSupportFromDetail());
          $arrStep[] = " IMS Support done";
//		array_push($resultcalc, $this->calcIMSTrainingsFromDetail());
//          $arrStep[] = " IMS Training done";
//		array_push($resultcalc, $this->calcICSEquipmentFromDetail());
//          $arrStep[] = " ICS Equipment done";
//		array_push($resultcalc, $this->calcICS1FromDetail());
//          $arrStep[] = " ICS1 done";
//        array_push($resultcalc, $this->calcICS1HectareFromDetail());
//          $arrStep[] = " ICS2 Hectare done";
        array_push($resultcalc, $this->calcICS1HectareFromDetailWave3());
          $arrStep[] = " ICS1 Hectare Wave III done";
        array_push($resultcalc, $this->calcICS1HectareFromDetailWave4());
          $arrStep[] = " ICS1 Hectare Wave IV done";
//		array_push($resultcalc, $this->calcICS2FromDetail());
//          $arrStep[] = " ICS2 done";
		array_push($resultcalc, $this->calcICS2FromDetailWave3());
          $arrStep[] = " ICS2 Wave III & IV done";
//		array_push($resultcalc, $this->calcICS0FromDetail());
//          $arrStep[] = " ICS0 done";
		array_push($resultcalc, $this->calcIMSAFL1FromDetailWave3());
          $arrStep[] = " IMS AFL1 Wave III & IV done";
//		array_push($resultcalc, $this->calcICS0ActiveJoinFromDetail());
//          $arrStep[] = " ICS0 ActiveJoin done";
//          array_push($resultcalc, $this->calcICS0ActiveNotJoinFromDetail());
//          $arrStep[] = " ICS0 ActiveNotJoin done";
//          array_push($resultcalc, $this->calcICS0InactiveFromDetail());
//          $arrStep[] = " ICS0 Inactive done";
//          array_push($resultcalc, $this->calcMasterTrainingFromDetail());
//          $arrStep[] = " Master Training done";
//          array_push($resultcalc, $this->calcSELY2FromDetail());
//          $arrStep[] = " SELY2 done";
//          array_push($resultcalc, $this->calcSELY2YesFromDetail());
//          $arrStep[] = " SELY2Yes done";
//          array_push($resultcalc, $this->calcSELYesFromDetail());
//          $arrStep[] = " SELYes done";
//          array_push($resultcalc, $this->calcSOCFromDetail());
//          $arrStep[] = " SOC done";
          array_push($resultcalc, $this->calcSOCFromDetailWave3());
          $arrStep[] = " SOC Wave III & IV done";
//          array_push($resultcalc, $this->calcTC1FromDetail()); 
//          $arrStep[] = " TC1 done";

		if ($resultcalc) {
			$results['success'] = true;
			$results['message'] = $resultcalc;
			$results['step'] = array("Summary : Step 2",$arrStep);
		} else {
			$results['success'] = false;
			$results['message'] = "Failed to update record";
			$results['step'] = "";
		}
		return $results;
	}
	
	public function doKPICalculation9($args)
	{

		$resultcalc = array();
          $arrStep = array();	
          array_push($resultcalc, $this->calcFarmXUserFromDetail());
          $arrStep[] = " FarmXUser done";
          array_push($resultcalc, $this->calcFarmGUserFromDetail());
          $arrStep[] = " FarmGUser done";
//          array_push($resultcalc, $this->calcTC2FromDetail());
//          $arrStep[] = " TC2 done";
//		array_push($resultcalc, $this->calcProgressGardenFromDetail());
//          $arrStep[] = " Progress Garden done";
//		array_push($resultcalc, $this->calcProgressPolygonFromDetail());
//          $arrStep[] = " Progress Polygon done";
//		array_push($resultcalc, $this->calcProgressPostHarvestFromDetail());
//          $arrStep[] = " Progress Post Harvest done";
//		array_push($resultcalc, $this->calcProgressPPIFromDetail());
//          $arrStep[] = " Progress PPI done";
//		array_push($resultcalc, $this->calcProgressCertificationFromDetail());
//          $arrStep[] = " Progress Certification done";
//          array_push($resultcalc, $this->calcProgressAuditLogFromDetail());
//          $arrStep[] = " Progress Audit Log done";
//          array_push($resultcalc, $this->calcProgressUpdatedFarmerFromDetail());
//          $arrStep[] = " Progress Updated Farmer done";
//          array_push($resultcalc, $this->calcProgressUpdatedGardenStatusFromDetail());
//          $arrStep[] = " Progress Garden Status done";
          
		if ($resultcalc) {
			$results['success'] = true;
			$results['message'] = $resultcalc;
			$results['step'] = array("Summary : Step 3",$arrStep);
		} else {
			$results['success'] = false;
			$results['message'] = "Failed to update record";
			$results['step'] = "";
		}
		return $results;
	}
	
	public function doKPICalculation10($args)
	{

		$resultcalc = array();
		$arrStep = array();	
          array_push($resultcalc, $this->moveToHistory());
          $arrStep[] = " Move To History done";
		array_push($resultcalc, $this->insert_KPIDateProcess());
          $arrStep[] = " Insert KPI Date Process done";

		if ($resultcalc) {
			$results['success'] = true;
			$results['message'] = $resultcalc;
			$results['step'] = array("Summary : Step 4",$arrStep);
		} else {
			$results['success'] = false;
			$results['message'] = "Failed to update record";
			$results['step'] = "";
		}
		return $results;
	}
		
	function moveToHistory()
	{
//            Process di skip karena tabel sudah tidak dipakai
//		$sql = "UPDATE `ktv_kpi_jbcocoa_certification_target_ims_district_report`
//            SET DateUpdated = NOW(), LastModifiedBy = 1";
//		$query = $this->db->query($sql);

		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` 
            SET DateUpdated = NOW(), LastModifiedBy = 1";
		$query = $this->db->query($sql);

		$sql = "DELETE FROM ktv_certification_progress_jbcocoa_ims_district_history 
            WHERE DATE(HisDateCreated) = DATE(NOW())";
		$query = $this->db->query($sql);

		$sql = "INSERT INTO `ktv_certification_progress_jbcocoa_ims_district_history` (
            `ProgressID`,
            `ProgID`,
            `CertHolderID`,
            `CertificateHolder`,
            `IMSID`,
            `ClusterID`,
            `IMSList`,
            `DistrictID`,
            `DistrictList`,
            `District`,
            `Location`,
            `Year`,
            `PartnerID`,
            `SurveyNr`,
            `RegCertificationHolders`,
            `CertificationHolders`,
            `RegTraders`,
            `Traders`,
            `FarmerCertified`,
            `FarmerCertifiedY2`,
            `FarmerCertifiedFinal`,
            `SalesQuota`,
            `CertifiedHa`,
            `MasterTotal`,
            `IMStrainings`,
            `IMSsupport`,
            `SOL`,
            `SOLY2`,
            `SEL`,
            `SELYes`,
            `SELY2`,
            `SELY2Yes`,
            `GAP`,
            `GAPR`,
            `COC`,
            `COCR1`,
            `COCR2`,
            `TrainingFinal`,
            `ICS0`,
            `ICS0Active`,
            `ICS0ActiveJoin`,
            `ICS0ActiveNotJoin`,
            `ICS0Inactive`,
            `ICS1`,
            `ICS1Total`,
            `ICS1Lolos`,
            `ICS1TidakLolos`,
            `ICS1Hectare`,
            `AFL1`,
            `ICS2`,
            `ICS3`,
            `ICS4`,
            `CertifiedBruto`,
            `CertifiedNetto`,
            `NonCertifiedBruto`,
            `NonCertifiedNetto`,
            `TC`,
            `ICSEquipment`,
            `FarmerReg`,
            `FarmCloud`,
            `AnnualServiceFarmGate`,
            `TrainSupportFarmGate`,
            `BroadcastSMSGateway`,
            `ManagementSystem`,
            `FarmerBasic`,
            `ICSSurvey`,
            `ICSNC`,
            `ProgressGarden`,
            `ProgressPolygon`,
            `ProgressPostHarvest`,
            `ProgressPPI`,
            `ProgressCertification`,
            `ProgressAuditLog`,
            `ProgressUpdatedFarmer`,
            `ProgressUpdatedGardenStatus`,
            `HaveSmartphone`,
            `PrinterPaper`,
            `FarmXUsers`, 
            `FarmGUsers`,
            `DateUpdated`,
            `LastModifiedBy`,
            `HisDateCreated`,
            `HisCreatedBy`
       ) 
       SELECT 
            `ProgressID`,
            `ProgID`,
            `CertHolderID`,
            `CertificateHolder`,
            `IMSID`,
            `ClusterID`,
            `IMSList`,
            `DistrictID`,
            `DistrictList`,
            `District`,
            `Location`,
            `Year`,
            `PartnerID`,
            `SurveyNr`,
            `RegCertificationHolders`,
            `CertificationHolders`,
            `RegTraders`,
            `Traders`,
            `FarmerCertified`,
            `FarmerCertifiedY2`,
            `FarmerCertifiedFinal`,
            `SalesQuota`,
            `CertifiedHa`,
            `MasterTotal`,
            `IMStrainings`,
            `IMSsupport`,
            `SOL`,
            `SOLY2`,
            `SEL`,
            `SELYes`,
            `SELY2`,
            `SELY2Yes`,
            `GAP`,
            `GAPR`,
            `COC`,
            `COCR1`,
            `COCR2`,
            `TrainingFinal`,
            `ICS0`,
            `ICS0Active`,
            `ICS0ActiveJoin`,
            `ICS0ActiveNotJoin`,
            `ICS0Inactive`,
            `ICS1`,
            `ICS1Total`,
            `ICS1Lolos`,
            `ICS1TidakLolos`,
            `ICS1Hectare`,
            `AFL1`,
            `ICS2`,
            `ICS3`,
            `ICS4`,
            `CertifiedBruto`,
            `CertifiedNetto`,
            `NonCertifiedBruto`,
            `NonCertifiedNetto`,
            `TC`,
            `ICSEquipment`,
            `FarmerReg`,
            `FarmCloud`,
            `AnnualServiceFarmGate`,
            `TrainSupportFarmGate`,
            `BroadcastSMSGateway`,
            `ManagementSystem`,
            `FarmerBasic`,
            `ICSSurvey`,
            `ICSNC`,
            `ProgressGarden`,
            `ProgressPolygon`,
            `ProgressPostHarvest`,
            `ProgressPPI`,
            `ProgressCertification`,
            `ProgressAuditLog`,
            `ProgressUpdatedFarmer`,
            `ProgressUpdatedGardenStatus`,
            `HaveSmartphone`,
            `PrinterPaper`,
            `FarmXUsers`, 
            `FarmGUsers`,
            `DateUpdated`,
            `LastModifiedBy`,
            NOW(),
            1
        FROM
            `ktv_certification_progress_jbcocoa_ims_district_report` ;";
		$query = $this->db->query($sql);

		if ($query) {
			$results['moveToHistory'] = "Insert To History success.";
		} else {
			$results['moveToHistory'] .= "||Insert To History Failed";
		}
		return $results;
	}
	
	function insert_detail_AFL()
	{
		$sql = "DELETE FROM ktv_jbcocoa_summary_approved_farmer_list WHERE DateGenerated = DATE(NOW()) ;";
		$query = $this->db->query($sql);
		$sql = "INSERT INTO ktv_jbcocoa_summary_approved_farmer_list (
                    DateGenerated,
                    CertHolderID,
                    DistrictID,
                    IMSID,
                    ClusterID,
                    ProgID,
                    Program,
                    CertificateHolders,
                    Responsible,
                    FarmerID,
                    FarmerName,
                    Gender,
                    HandPhone,
                    Province,
                    District,
                    SubDistrict,
                    Village,
                    GroupID,
                    FarmerGroup,
                    StatusAudit,
                    AuditRemarks,
                    FirstYearOfCertification,
                    YearOfCertification,
                    CertificationYear,
                    ICSDate,
                    PresentYearHarvest,
                    PreviousYearHarvest,
                    HaCertifiedCropArea,
                    NrOfCertifiedPlots,
                    HaTotalFarmArea,
                    LastYearDelivery,
                    Last2ndYearDelivery,
                    Last3rdYearDelivery
               ) 
               SELECT
               DATE( NOW( ) ),
               d.CertHolderID,
               k.DistrictID,
               b.`IMSID`,
               m.ClusterID,
               b.`ProgID`,
               CONCAT( l.`ProgramName`, ' (', l.`ProgramYear`, ')' ) AS Program,
          IF
               ( h.OrgType = 'trader', i.`Company`, IF ( h.OrgType = 'koperasi', j.CoopName, '-' ) ) AS CertificateHolders,
          IF
               ( h.OrgType = 'trader', i.TraderName, IF ( h.OrgType = 'koperasi', j.CoopName, '-' ) ) AS Responsible,
               g.`FarmerID`,
               g.`FarmerName`,
               a.`Gender`,
               a.`HandPhone`,
               a.`Province`,
               a.`District`,
               a.`SubDistrict`,
               a.`Village`,
               a.`CPGid` AS GroupID,
               a.`GroupName` AS FarmerGroup,
               a.`CertStatusAudit` AS StatusAudit,
               a.`CertAuditRemark` AS AuditRemarks,
               a.`CertFirstYear` AS FirstYearOfCertification,
               a.`YearOfCertification` AS YearOfCertification,
               a.`CertYear` AS CertificationYear,
               a.`CertICSDate` AS ICSDate,
               a.`CertNextHarvest` AS PresentYearHarvest,
               a.`CertHarvest` AS PreviousYearHarvest,
               a.`CertHectare` AS HaCertifiedCropArea,
               a.`CertFarmNr` AS NrOfCertifiedPlots,
               a.`CertTotalHectare` AS HaTotalFarmArea,
               a.`SalesLastYear` AS LastYearDelivery,
               a.`SalesLast2Years` AS Last2ndYearDelivery,
               a.`SalesLast3Years` AS Last3rdYearDelivery 
          FROM
               `ktv_cocoa_certification_afl_farmer` a
               LEFT JOIN `ktv_ims` b ON b.`IMSID` = a.`IMSID`
               LEFT JOIN `ktv_ims_master` c ON c.`IMSMasterID` = b.`IMSMasterID`
               LEFT JOIN `ktv_certification_holders` d ON d.`CertHolderID` = c.`CertHolderID`
               LEFT JOIN `ktv_first_buyer` e ON e.`FirstBuyerID` = b.`FirstBuyerID`
               LEFT JOIN `ktv_program_partner` f ON f.`PartnerID` = e.`FirstBuyerPartnerID`
               LEFT JOIN `ktv_cocoa_farmer` g ON g.`FarmerID` = a.`FarmerID`
               LEFT JOIN `ktv_supplychain_org` h ON h.`SupplychainID` = d.`SupplychainID`
               LEFT JOIN `ktv_traders` i ON i.`TraderID` = h.`OrgID` 
               AND h.`OrgType` = 'trader'
               LEFT JOIN `ktv_cooperatives` j ON j.`CoopID` = h.`OrgID` 
               AND h.`OrgType` = 'koperasi'
               LEFT JOIN ktv_village vil ON vil.VillageID = g.VillageID
               LEFT JOIN ktv_subdistrict subd ON subd.SubDistrictID = vil.SubDistrictID
               LEFT JOIN `ktv_district` k ON k.`DistrictID` = subd.DistrictID 
               LEFT JOIN `ktv_first_buyer_program` l ON l.`ProgID` = b.`ProgID`
               LEFT JOIN ktv_cocoa_certification_pre_afl m ON m.IMSID = a.IMSID 
               AND m.FarmerID = a.FarmerID 
          WHERE
               b.`IMSID` IN ( 207 ) 
               AND b.`StatusCode` = 'active' 
               AND c.`StatusCode` = 'active' 
               AND a.CertStatusAudit = 'Comply' 
          ORDER BY
               d.`CertHolderID`,
               a.`District`,
               a.`CPGid`";
		$query = $this->db->query($sql); //AFL
		if ($query) {
			$results['detailAFL'] = "detailAFL updated.";
		} else {
			$results['detailAFL'] .= "||detailAFL Failed to update";
		}
		return $results;
	}
        
        function insert_detail_FarmerBasic() {
            $sql = "DELETE FROM ktv_jbcocoa_summary_farmerbasic WHERE DateGenerated=DATE(NOW()) AND IMSID IN (252,278)";
            $query = $this->db->query($sql);            
            
            $sql = "INSERT INTO ktv_jbcocoa_summary_farmerbasic (
    DateGenerated,
    CertHolderID,
    DistrictID,
    IMSID,
    ProgID,
    Program,
    CertificateHolders,
    Responsible,
    FarmerID,
    FarmerName,
    Gender,
    HandPhone,
    Province,
    District,
    SubDistrict,
    Village,
    GroupID,
    FarmerGroup,
    StatusAudit,
    AuditRemarks,
    FirstYearOfCertification,
    YearOfCertification,
    CertificationYear,
    ICSDate,
    PresentYearHarvest,
    PreviousYearHarvest,
    HaCertifiedCropArea,
    NrOfCertifiedPlots,
    HaTotalFarmArea,
    LastYearDelivery,
    Last2ndYearDelivery,
    Last3rdYearDelivery,
    ClusterID,
    StatusLevel
) SELECT
        DATE(NOW()),
        d.CertHolderID,
        k.DistrictID,
        b.`IMSID`,
        b.`ProgID`,
        CONCAT( l.`ProgramName`, ' (', l.`ProgramYear`, ')' ) AS Program,
        IF
                ( h.OrgType = 'trader', i.`Company`, IF ( h.OrgType = 'koperasi', j.CoopName, '-' ) ) AS CertificateHolders,
        IF
                ( h.OrgType = 'trader', i.TraderName, IF ( h.OrgType = 'koperasi', j.CoopName, '-' ) ) AS Responsible,
	g.`FarmerID`,
	g.`FarmerName`,
	a.`Gender`,
	a.`HandPhone`,
	a.`Province`,
	a.`District`,
	a.`SubDistrict`,
	a.`Village`,
	a.`CPGid` AS GroupID,
	a.`GroupName` AS FarmerGroup,
	a.`CertStatusAudit` AS StatusAudit,
	a.`CertAuditRemark` AS AuditRemarks,
	a.`CertFirstYear` AS FirstYearOfCertification,
	a.`YearOfCertification` AS YearOfCertification,
	a.`CertYear` AS CertificationYear,
	a.`CertICSDate` AS ICSDate,
	a.`CertNextHarvest` AS PresentYearHarvest,
	a.`CertHarvest` AS PreviousYearHarvest,
	a.`CertHectare` AS HaCertifiedCropArea,
	a.`CertFarmNr` AS NrOfCertifiedPlots,
	a.`CertTotalHectare` AS HaTotalFarmArea,
	a.`SalesLastYear` AS LastYearDelivery,
	a.`SalesLast2Years` AS Last2ndYearDelivery,
	a.`SalesLast3Years` AS Last3rdYearDelivery,
        m.ClusterID,
        IF(g.StatusFarmer=1 AND IFNULL(g.StatusJoin=1,1),'active_join',IF(g.StatusFarmer=1 AND g.StatusJoin=2,'active_notjoin',IF(g.StatusFarmer=2,'not_active','-'))) AS `StatusLevel`
FROM
	`ktv_cocoa_certification_afl_farmer` a
	LEFT JOIN `ktv_ims` b ON b.`IMSID` = a.`IMSID`
	LEFT JOIN `ktv_ims_master` c ON c.`IMSMasterID` = b.`IMSMasterID`
	LEFT JOIN `ktv_certification_holders` d ON d.`CertHolderID` = c.`CertHolderID`
	LEFT JOIN `ktv_first_buyer` e ON e.`FirstBuyerID` = b.`FirstBuyerID`
	LEFT JOIN `ktv_program_partner` f ON f.`PartnerID` = e.`FirstBuyerPartnerID`
	LEFT JOIN `ktv_cocoa_farmer` g ON g.`FarmerID` = a.`FarmerID`
	LEFT JOIN `ktv_supplychain_org` h ON h.`SupplychainID` = d.`SupplychainID`
	LEFT JOIN `ktv_traders` i ON i.`TraderID` = h.`OrgID` 
	AND h.`OrgType` = 'trader'
	LEFT JOIN `ktv_cooperatives` j ON j.`CoopID` = h.`OrgID` 
	AND h.`OrgType` = 'koperasi'
        LEFT JOIN ktv_village vil ON g.`VillageID` = vil.VillageID
        LEFT JOIN ktv_subdistrict subd ON subd.SubDistrictID = vil.SubDistrictID
	LEFT JOIN `ktv_district` k ON k.`DistrictID` = subd.DistrictID
	LEFT JOIN `ktv_first_buyer_program` l ON l.`ProgID` = b.`ProgID`
	LEFT JOIN ktv_cocoa_certification_pre_afl m ON m.IMSID = a.IMSID 
	AND m.FarmerID = a.FarmerID 
WHERE
	b.`IMSID` IN (252,278) 
	AND b.`StatusCode` = 'active' 
	AND c.`StatusCode` = 'active' 
	AND a.CertStatusAudit IN ( 'Comply', 'Not Comply' ) 
ORDER BY
	d.`CertHolderID`,
	a.`District`,
	a.`CPGid`;";
            $query = $this->db->query($sql); // Farmer Basic Wave III
        if ($query) {
            $results['detailAFL'] = "FarmerBasic updated.";
        } else {
            $results['detailAFL'] .= "||FarmerBasic Failed to update";
        }
        return $results;
    }

    function insert_detail_CertifiedTrader()
	{
		$sql = " DELETE FROM ktv_jbcocoa_summary_certified_buying_unit WHERE DateGenerated = DATE(NOW()) ;";
		$query = $this->db->query($sql);
		$sql = "INSERT INTO ktv_jbcocoa_summary_certified_buying_unit (
                DateGenerated,
                CertHolderID,
                DistrictID,
                IMSID,
                ClusterID,
                ProgID,
                District,
                Program,
                CertificateHolders,
                Responsible,
                SupplychainID,
                WorkArea,
                BuyingUnitID,
                BuyingUnit,
                Location,
                `Status`,
                DateUpdated,
                LastModifiedBy
           ) 
           SELECT 
                DATE(NOW()),
                n.CertHolderID,
                IF(
                     b.OrgType = 'trader',
                     h.DistrictID,
                     IF(
                          b.OrgType = 'koperasi',
                          i.DistrictID,
                          IF(
                               b.OrgType = 'warehouse',
                               j.DistrictID,
                               IF(b.OrgType = 'sce', k.DistrictID, '-')
                          )
                     )
                ) AS DistrictID,
                l.`IMSID`,
                a.ClusterID,
                o.ProgID,
                IF(
                     b.OrgType = 'trader',
                     h.District,
                     IF(
                          b.OrgType = 'koperasi',
                          i.District,
                          IF(
                               b.OrgType = 'warehouse',
                               j.District,
                               IF(b.OrgType = 'sce', k.District, '-')
                          )
                     )
                ) AS District,
                CONCAT(
                     o.`ProgramName`,
                     ' (',
                     o.`ProgramYear`,
                     ')'
                ) AS Program,
                n.`CertHolderOrgName` AS CertificateHolders,
                n.`CertHolderResponsible` AS Responsible,
                a.`SupplychainID`,
                l.`Location` AS WorkArea,
                IF(
                     b.OrgType = 'trader',
                     c.`TraderID`,
                     IF(
                          b.OrgType = 'koperasi',
                          d.`CoopID`,
                          IF(
                               b.OrgType = 'warehouse',
                               e.`WarehouseID`,
                               IF(
                                    b.OrgType = 'sce',
                                    g.`FarmerID`,
                                    '-'
                               )
                          )
                     )
                ) AS BuyingUnitID,
                IF(
                     b.OrgType = 'trader',
                     IFNULL(c.TraderName, c.Company),
                     IF(
                          b.OrgType = 'koperasi',
                          d.CoopName,
                          IF(
                               b.OrgType = 'warehouse',
                               e.WarehouseName,
                               IF(
                                    b.OrgType = 'sce',
                                    g.FarmerName,
                                    '-'
                               )
                          )
                     )
                ) AS BuyingUnit,
                IF(
                     b.OrgType = 'trader',
                     h.District,
                     IF(
                          b.OrgType = 'koperasi',
                          i.District,
                          IF(
                               b.OrgType = 'warehouse',
                               j.District,
                               IF(b.OrgType = 'sce', k.District, '-')
                          )
                     )
                ) AS Location,
                a.Status,
                IFNULL(
                     a.`DateUpdated`,
                     a.`DateCreated`
                ) AS DateUpdated,
                IFNULL(q.UserRealName, p.UserRealName) AS LastModifiedBy 
           FROM
                `ktv_ims_buying_unit` a 
                LEFT JOIN `ktv_ims` l 
                     ON l.`IMSID` = a.`IMSID` 
                LEFT JOIN `ktv_ims_master` m 
                     ON m.`IMSMasterID` = l.`IMSMasterID` 
                LEFT JOIN `ktv_certification_holders` n 
                     ON n.`CertHolderID` = m.`CertHolderID` 
                LEFT JOIN `ktv_first_buyer_program` o 
                     ON o.`ProgID` = l.`ProgID` 
                LEFT JOIN `ktv_supplychain_org` b 
                     ON a.`SupplychainID` = b.`SupplychainID` 
                LEFT JOIN `ktv_traders` c 
                     ON c.`TraderID` = b.`OrgID` 
                     AND b.`OrgType` = 'trader' 
                LEFT JOIN `ktv_cooperatives` d 
                     ON d.`CoopID` = b.`OrgID` 
                     AND b.`OrgType` = 'koperasi' 
                LEFT JOIN `ktv_warehouse` e 
                     ON e.WarehouseID = b.OrgID 
                     AND b.OrgType = 'warehouse' 
                LEFT JOIN `sce_farmer` f 
                     ON f.`SceID` = b.`OrgID` 
                     AND b.`OrgType` = 'sce' 
                LEFT JOIN `ktv_cocoa_farmer` g 
                     ON g.`FarmerID` = f.`FarmerID` 
                LEFT JOIN `ktv_district` h 
                     ON h.`DistrictID` = c.ReportDistrictID 
                LEFT JOIN `ktv_district` i 
                     ON i.`DistrictID` = SUBSTR(d.`VillageID`, 1, 4) 
                LEFT JOIN `ktv_district` j 
                     ON j.`DistrictID` = SUBSTR(e.`VillageID`, 1, 4) 
                LEFT JOIN `ktv_district` k 
                     ON k.`DistrictID` = SUBSTR(g.`VillageID`, 1, 4) 
                LEFT JOIN `sys_user` p 
                     ON p.`UserId` = a.`CreatedBy` 
                LEFT JOIN `sys_user` q 
                     ON q.`UserId` = a.`LastModifiedBy` 
           WHERE l.`IMSID` IN (207) 
                AND b.`SupplychainID` <> n.`SupplychainID` 
                AND a.`StatusCode` = 'active' 
                AND l.`StatusCode` = 'active' 
           GROUP BY a.`SupplychainID` 
           ORDER BY WorkArea";
		$query = $this->db->query($sql); //CertifiedTrader
		if ($query) {
			$results['detailCertifiedTrader'] = "CertifiedTrader updated.";
		} else {
			$results['detailCertifiedTrader'] .= "||CertifiedTrader Failed to update";
		}
		return $results;
	}
	
	function insert_detail_CertifiedCH()
	{
		$sql = "DELETE FROM ktv_jbcocoa_summary_certified_certificate_holder WHERE DateGenerated = DATE(NOW()) ;";
		$query = $this->db->query($sql);
		$sql = "INSERT INTO ktv_jbcocoa_summary_certified_certificate_holder (
                DateGenerated,
                CertHolderID,
                DistrictID,
                IMSID,
                ClusterID,
                ProgID,
                Program,
                CertificateHolders,
                Responsible,
                WorkArea,
                CertificationProgram,                
                `Status`,
                DateUpdated,
                LastModifiedBy
           ) 
           SELECT
               DATE( NOW( ) ),
               a.CertHolderID,
               c.ReportDistrictID AS DistrictID,
               c.`IMSID`,
               u.ClusterID,
               t.ProgID,
               CONCAT( t.`ProgramName`, ' (', t.`ProgramYear`, ')' ) AS Program,
               a.`CertHolderOrgName` AS CertificateHolders,
               a.`CertHolderResponsible` AS Responsible,
               c.`Location` AS WorkArea,
               f.`CertProgName` AS CertificationProgram,
          IF
               ( c.CertificationStart = '0000-00-00' OR c.CertificationStart IS NULL, 'Registered', 'Certified' ) AS `Status`,
               IFNULL( a.`DateUpdated`, a.`DateCreated` ) AS DateUpdated,
               IFNULL( s.UserRealName, r.UserRealName ) AS LastModifiedBy 
          FROM
               `ktv_certification_holders` a
               LEFT JOIN `ktv_ims_master` b ON b.`CertHolderID` = a.`CertHolderID`
               LEFT JOIN `ktv_ims` c ON c.`IMSMasterID` = b.`IMSMasterID`
               LEFT JOIN `ktv_first_buyer` e ON e.`FirstBuyerID` = c.`FirstBuyerID`
               LEFT JOIN `ktv_ref_certification_program` f ON f.`CertProgID` = a.`CertProgID`
               LEFT JOIN `ktv_district` n ON n.`DistrictID` = c.ReportDistrictID
               LEFT JOIN `sys_user` r ON r.`UserId` = a.`CreatedBy`
               LEFT JOIN `sys_user` s ON s.`UserId` = a.`LastModifiedBy`
               LEFT JOIN `ktv_first_buyer_program` t ON t.`ProgID` = c.`ProgID` 
			LEFT JOIN `ktv_ims_cluster` u ON u.IMSID = c.IMSID
          WHERE
               c.`IMSID` IN (207 ) 
               AND b.`StatusCode` = 'active' 
               AND c.`StatusCode` = 'active' 
          GROUP BY
               c.IMSID 
          ORDER BY
               c.IMSID";
		$query = $this->db->query($sql); //CertifiedCH
		if ($query) {
			$results['detailCertifiedCH'] = "CertifiedCH updated.";
		} else {
			$results['detailCertifiedCH'] .= "||CertifiedCH Failed to update";
		}
		return $results;
	}
	
	function insert_detail_CFL()
	{
		$sql = "DELETE FROM ktv_jbcocoa_summary_certified_farmer_list WHERE DateGenerated = DATE(NOW()) AND IMSID=207;";
		$query = $this->db->query($sql);
		$sql = "INSERT INTO ktv_jbcocoa_summary_certified_farmer_list (
                DateGenerated,
                CertHolderID,
                DistrictID,
                IMSID,
                ClusterID,
                Program,
                CertificateHolders,
                Responsible,
                FarmerID,
                FarmerName,
                Gender,
                Handhpone,
                Province,
                District,
                SubDistrict,
                Village,
                GroupID,
                FarmerGroup,
                StatusAudit,
                AuditRemarks,
                FirstYearCertification,
                YearOfCertification,
                CertificationYear,
                ICSDate,
                PresentYearHarvest,
                SalesQuota,
                PreviousYearHarvest,
                HaCertifiedCropArea,
                NrOfCertifiedPlots,
                HaTotalFarmArea,
                LastYearDelivery,
                Last2ndYearDelivery,
                Last3rdYearDelivery
           ) 
           SELECT 
                DATE(NOW()),
                d.CertHolderID,
                k.DistrictID,
                b.`IMSID`,
			 m.ClusterID,
                CONCAT(
                     l.`ProgramName`,
                     ' (',
                     l.`ProgramYear`,
                     ')'
                ) AS Program,
                IF(
                     h.OrgType = 'trader',
                     i.`Company`,
                     IF(
                          h.OrgType = 'koperasi',
                          j.CoopName,
                          '-'
                     )
                ) AS CertificateHolders,
                IF(
                     h.OrgType = 'trader',
                     i.TraderName,
                     IF(
                          h.OrgType = 'koperasi',
                          j.CoopName,
                          '-'
                     )
                ) AS Responsible,
                g.`FarmerID`,
                g.`FarmerName`,
                a.`Gender`,
                a.`HandPhone`,
                a.`Province`,
                a.`District`,
                a.`SubDistrict`,
                a.`Village`,
                a.`CPGid` AS GroupID,
                a.`GroupName` AS FarmerGroup,
                a.`CertStatusAudit` AS StatusAudit,
                a.`CertAuditRemark` AS AuditRemarks,
                a.`CertFirstYear` AS FirstYearOfCertification,
                a.`YearOfCertification` AS YearOfCertification,
                a.`CertYear` AS CertificationYear,
                a.`CertICSDate` AS ICSDate,
                a.`CertNextHarvest` AS PresentYearHarvest,
                a.`SalesQuota` AS SalesQuota,
                a.`CertHarvest` AS PreviousYearHarvest,
                a.`CertHectare` AS HaCertifiedCropArea,
                a.`CertFarmNr` AS NrOfCertifiedPlots,
                a.`CertTotalHectare` AS HaTotalFarmArea,
                a.`SalesLastYear` AS LastYearDelivery,
                a.`SalesLast2Years` AS Last2ndYearDelivery,
                a.`SalesLast3Years` AS Last3rdYearDelivery 
           FROM
                `ktv_cocoa_certification_certified_farmer` a 
                LEFT JOIN `ktv_ims` b 
                     ON b.`IMSID` = a.`IMSID` 
                LEFT JOIN `ktv_ims_master` c 
                     ON c.`IMSMasterID` = b.`IMSMasterID` 
                LEFT JOIN `ktv_certification_holders` d 
                     ON d.`CertHolderID` = c.`CertHolderID` 
                LEFT JOIN `ktv_first_buyer` e 
                     ON e.`FirstBuyerID` = b.`FirstBuyerID` 
                LEFT JOIN `ktv_program_partner` f 
                     ON f.`PartnerID` = e.`FirstBuyerPartnerID` 
                LEFT JOIN `ktv_cocoa_farmer` g 
                     ON g.`FarmerID` = a.`FarmerID` 
                LEFT JOIN `ktv_supplychain_org` h 
                     ON h.`SupplychainID` = d.`SupplychainID` 
                LEFT JOIN `ktv_traders` i 
                     ON i.`TraderID` = h.`OrgID` 
                     AND h.`OrgType` = 'trader' 
                LEFT JOIN `ktv_cooperatives` j 
                     ON j.`CoopID` = h.`OrgID` 
                     AND h.`OrgType` = 'koperasi' 
                LEFT JOIN ktv_village vil ON vil.VillageID = g.VillageID
                LEFT JOIN ktv_subdistrict subd ON subd.SubDistrictID = vil.SubDistrictID
                LEFT JOIN `ktv_district` k ON k.`DistrictID` = subd.DistrictID 
                LEFT JOIN `ktv_first_buyer_program` l 
                     ON l.`ProgID` = b.`ProgID` 
                LEFT JOIN ktv_cocoa_certification_pre_afl m ON m.IMSID = a.IMSID 
                     AND m.FarmerID = a.FarmerID 
           WHERE b.`IMSID` IN (207) 
                AND b.`StatusCode` = 'active' 
                AND c.`StatusCode` = 'active' 
                AND a.CertStatusAudit = 'Comply' 
           ORDER BY d.`CertHolderID`,
                a.`District`,
                a.`CPGid`";
		$query = $this->db->query($sql); //detailCFL
		if ($query) {
			$results['detailCFL'] = "detailCFL updated.";
		} else {
			$results['detailCFL'] .= "||detailCFL Failed to update";
		}
		return $results;
	}
        
        function insert_detail_CFL_Wave3() {
            $sql = "DELETE FROM ktv_jbcocoa_summary_certified_farmer_list WHERE DateGenerated = DATE(NOW()) AND IMSID IN (252,278);";
            $query = $this->db->query($sql);
            
            $sql = "INSERT INTO ktv_jbcocoa_summary_certified_farmer_list (
DateGenerated,
CertHolderID,
DistrictID,
IMSID,
ProgID,
Program,
CertificateHolders,
Responsible,
FarmerID,
FarmerName,
Gender,
Handhpone,
Province,
District,
SubDistrict,
Village,
GroupID,
FarmerGroup,
StatusAudit,
AuditRemarks,
FirstYearCertification,
YearOfCertification,
CertificationYear,
ICSDate,
PresentYearHarvest,
SalesQuota,
PreviousYearHarvest,
HaCertifiedCropArea,
NrOfCertifiedPlots,
HaTotalFarmArea,
LastYearDelivery,
Last2ndYearDelivery,
Last3rdYearDelivery,
ClusterID 
) SELECT
DATE( NOW( ) ),
d.CertHolderID,
k.DistrictID,
b.`IMSID`,
b.`ProgID`,
CONCAT( l.`ProgramName`, ' (', l.`ProgramYear`, ')' ) AS Program,
IF
	( h.OrgType = 'trader', i.`Company`, IF ( h.OrgType = 'koperasi', j.CoopName, '-' ) ) AS CertificateHolders,
IF
	( h.OrgType = 'trader', i.TraderName, IF ( h.OrgType = 'koperasi', j.CoopName, '-' ) ) AS Responsible,
	g.`FarmerID`,
	g.`FarmerName`,
	a.`Gender`,
	a.`HandPhone`,
	a.`Province`,
	a.`District`,
	a.`SubDistrict`,
	a.`Village`,
	a.`CPGid` AS GroupID,
	a.`GroupName` AS FarmerGroup,
	a.`CertStatusAudit` AS StatusAudit,
	a.`CertAuditRemark` AS AuditRemarks,
	a.`CertFirstYear` AS FirstYearOfCertification,
	a.`YearOfCertification` AS YearOfCertification,
	a.`CertYear` AS CertificationYear,
	a.`CertICSDate` AS ICSDate,
	a.`CertNextHarvest` AS PresentYearHarvest,
	a.`SalesQuota` AS SalesQuota,
	a.`CertHarvest` AS PreviousYearHarvest,
	a.`CertHectare` AS HaCertifiedCropArea,
	a.`CertFarmNr` AS NrOfCertifiedPlots,
	a.`CertTotalHectare` AS HaTotalFarmArea,
	a.`SalesLastYear` AS LastYearDelivery,
	a.`SalesLast2Years` AS Last2ndYearDelivery,
	a.`SalesLast3Years` AS Last3rdYearDelivery,
	m.ClusterID 
FROM
	`ktv_cocoa_certification_certified_farmer` a
	LEFT JOIN `ktv_ims` b ON b.`IMSID` = a.`IMSID`
	LEFT JOIN `ktv_ims_master` c ON c.`IMSMasterID` = b.`IMSMasterID`
	LEFT JOIN `ktv_certification_holders` d ON d.`CertHolderID` = c.`CertHolderID`
	LEFT JOIN `ktv_first_buyer` e ON e.`FirstBuyerID` = b.`FirstBuyerID`
	LEFT JOIN `ktv_program_partner` f ON f.`PartnerID` = e.`FirstBuyerPartnerID`
	LEFT JOIN `ktv_cocoa_farmer` g ON g.`FarmerID` = a.`FarmerID`
	LEFT JOIN `ktv_supplychain_org` h ON h.`SupplychainID` = d.`SupplychainID`
	LEFT JOIN `ktv_traders` i ON i.`TraderID` = h.`OrgID` 
	AND h.`OrgType` = 'trader'
	LEFT JOIN `ktv_cooperatives` j ON j.`CoopID` = h.`OrgID` 
	AND h.`OrgType` = 'koperasi'
        LEFT JOIN ktv_village vil ON vil.VillageID = g.VillageID
        LEFT JOIN ktv_subdistrict subd ON subd.SubDistrictID = vil.SubDistrictID
        LEFT JOIN `ktv_district` k ON k.`DistrictID` = subd.DistrictID 
	LEFT JOIN `ktv_first_buyer_program` l ON l.`ProgID` = b.`ProgID`
	LEFT JOIN ktv_cocoa_certification_pre_afl m ON m.IMSID = a.IMSID 
	AND m.FarmerID = a.FarmerID 
WHERE
	b.IMSID IN (252,278)
	AND b.`StatusCode` = 'active' 
	AND c.`StatusCode` = 'active' 
	AND a.CertStatusAudit = 'Comply' 
ORDER BY
	d.`CertHolderID`,
	a.`District`,
	a.`CPGid`;";
            $query = $this->db->query($sql);
            
	if ($query) {
            $results['detailCFLWave3'] = "detailCOCWave3 updated.";
        } else {
            $results['detailCFLWave3'] .= "||detailCOCWave3 Failed to update";
        }
        return $results;
    }

    function insert_detail_COC()
	{
		$sql = "DELETE FROM ktv_jbcocoa_summary_coc WHERE DateGenerated = DATE(NOW()) AND IMSID = 207;";
		$query = $this->db->query($sql);
		$sql = "INSERT INTO ktv_jbcocoa_summary_coc (
                DateGenerated,
                CertHolderID,
                DistrictID,
                IMSID,
                ClusterID,
                ProgID,
                Program,
                CertificateHolders,
                Responsible,
                ParticipantID,
                ParticipantName,
                GroupID,
                FarmerGroup,
                Province,
                District,
                SubDistrict,
                Village,
                EventName,
                EventDate,
                TrainingType,
                NrOfTrainingDays,
                PercentageOfAttendance,
                TrainingStatus
           ) 
           SELECT 
                DATE(NOW()),
                g.CertHolderID,
                o.DistrictID,
                e.`IMSID`,
                v.ClusterID,
                s.ProgID,
                CONCAT(
                     s.`ProgramName`,
                     ' (',
                     s.`ProgramYear`,
                     ')'
                ) AS Program,
                IF(
                     j.OrgType = 'trader',
                     k.`Company`,
                     IF(
                          j.OrgType = 'koperasi',
                          l.CoopName,
                          '-'
                     )
                ) AS CertificateHolders,
                IF(
                     j.OrgType = 'trader',
                     k.TraderName,
                     IF(
                          j.OrgType = 'koperasi',
                          l.CoopName,
                          '-'
                     )
                ) AS Responsible,
                d.`FarmerID` AS ParticipantID,
                d.`FarmerName` AS ParticipantName,
                b.`CPGid` AS GroupID,
                q.`GroupName` AS FarmerGroup,
                p.`Province`,
                o.`District`,
                n.`SubDistrict`,
                m.`Village`,
                r.`CpgTrainings` AS EventName,
                DATE(b.`TrainingStart`) AS EventDate,
                IF(
                     b.`ActivityType` = 'full',
                     '1st Year',
                     IF(
                          b.`ActivityType` = 'refresh',
                          '2nd Year',
                          ''
                     )
                ) AS TrainingType,
                b.`TrainingDays` AS NrOfTrainingDays,
                IFNULL(a.`Percentage`, '-') AS PercentageOfAttendance,
                IF(
                     a.`StatusTraining` = 1,
                     'Passed',
                     IF(
                          a.`StatusTraining` = 2,
                          'Not Passed',
                          'No Status'
                     )
                ) AS TrainingStatus 
           FROM
                `ktv_cpg_batch_trainings_farmers` a 
                LEFT JOIN `ktv_cpg_batch_trainings` b 
                     ON b.`CpgBatchTrainingID` = a.`CpgBatchTrainingID` 
                LEFT JOIN `ktv_cpg_batch` c 
                     ON c.`CpgBatchID` = b.`CpgBatchID` 
                LEFT JOIN `ktv_cocoa_farmer` d 
                     ON d.`FarmerID` = a.`FarmerID` 
                     AND d.`StatusCode` = 'active' 
                LEFT JOIN `ktv_ims` e 
                     ON e.`IMSID` = b.`IMSID` 
                LEFT JOIN `ktv_ims_master` f 
                     ON f.`IMSMasterID` = e.`IMSMasterID` 
                LEFT JOIN `ktv_certification_holders` g 
                     ON g.`CertHolderID` = f.`CertHolderID` 
                LEFT JOIN `ktv_first_buyer` h 
                     ON h.`FirstBuyerID` = e.`FirstBuyerID` 
                LEFT JOIN `ktv_program_partner` i 
                     ON i.`PartnerID` = h.`FirstBuyerPartnerID` 
                LEFT JOIN `ktv_supplychain_org` j 
                     ON j.`SupplychainID` = g.`SupplychainID` 
                LEFT JOIN `ktv_traders` k 
                     ON k.`TraderID` = j.`OrgID` 
                     AND j.`OrgType` = 'trader' 
                LEFT JOIN `ktv_cooperatives` l 
                     ON l.`CoopID` = j.`OrgID` 
                     AND j.`OrgType` = 'koperasi' 
                LEFT JOIN `ktv_village` m 
                     ON m.`VillageID` = d.`VillageID` 
                LEFT JOIN `ktv_subdistrict` n 
                     ON n.`SubDistrictID` = m.`SubDistrictID` 
                LEFT JOIN `ktv_district` o 
                     ON o.`DistrictID` = n.`DistrictID` 
                LEFT JOIN `ktv_province` p 
                     ON p.`ProvinceID` = o.`ProvinceID` 
                LEFT JOIN `ktv_cpg` q 
                     ON q.`CPGid` = d.`CPGid` 
                LEFT JOIN `ktv_cpg_trainings` r 
                     ON r.`CpgTrainingsID` = b.`CPGtrainingsID` 
                LEFT JOIN `ktv_first_buyer_program` s 
                     ON s.`ProgID` = e.`ProgID` 
                LEFT JOIN `ktv_cpg_batch_trainings_sub_topics` t 
                     ON t.`CpgBatchTrainingID` = b.`CpgBatchTrainingID` 
               LEFT JOIN ktv_ims_training_gap_coc u
                     ON u.IMSID = e.IMSID and u.FarmerID = d.FarmerID
			LEFT JOIN ktv_ims_soc_sel as v ON v.DestObjID = u.FarmerID and v.IMSID = u.IMSID
           WHERE e.`IMSID` IN (207 ) 
                AND a.`StatusCode` = 'active' 
                AND b.`StatusCode` = 'active' 
                AND d.`StatusCode` = 'active' 
                AND b.`CPGtrainingsID` = 14 
                AND t.`SubCpgTrainingsID` = 53 
                AND v.ObjType = 'Applicant'               
           GROUP BY d.FarmerID ;";
		$query = $this->db->query($sql); //detailCOC
		if ($query) {
			$results['detailCOC'] = "detailCOC updated.";
		} else {
			$results['detailCOC'] .= "||detailCOC Failed to update";
		}
		return $results;
	}
        
    function insert_detail_COC_Wave3() {
        $sql = "DELETE FROM ktv_jbcocoa_summary_coc WHERE DateGenerated = DATE(NOW()) AND IMSID IN (252,278);";
        $query = $this->db->query($sql);
        
        $sql = "INSERT INTO ktv_jbcocoa_summary_coc (
DateGenerated,
CertHolderID,
DistrictID,
IMSID,
ProgID,
Program,
CertificateHolders,
Responsible,
ParticipantID,
ParticipantName,
GroupID,
FarmerGroup,
Province,
District,
SubDistrict,
Village,
EventName,
EventDate,
TrainingType,
NrOfTrainingDays,
PercentageOfAttendance,
TrainingStatus,
ClusterID 
) SELECT
DATE(NOW()),
g.CertHolderID,
o.DistrictID,
e.`IMSID`,
s.ProgID,
CONCAT( s.`ProgramName`, ' (', s.`ProgramYear`, ')' ) AS Program,
IF
	( j.OrgType = 'trader', k.`Company`, IF ( j.OrgType = 'koperasi', l.CoopName, '-' ) ) AS CertificateHolders,
IF
	( j.OrgType = 'trader', k.TraderName, IF ( j.OrgType = 'koperasi', l.CoopName, '-' ) ) AS Responsible,
	d.`FarmerID` AS ParticipantID,
	d.`FarmerName` AS ParticipantName,
	b.`CPGid` AS GroupID,
	q.`GroupName` AS FarmerGroup,
	p.`Province`,
	o.`District`,
	n.`SubDistrict`,
	m.`Village`,
	r.`CpgTrainings` AS EventName,
	DATE( b.`TrainingStart` ) AS EventDate,
IF
	( b.`ActivityType` = 'full', '1st Year', IF ( b.`ActivityType` = 'refresh', '2nd Year', '' ) ) AS TrainingType,
	b.`TrainingDays` AS NrOfTrainingDays,
	IFNULL( a.`Percentage`, '-' ) AS PercentageOfAttendance,
IF
	( a.`StatusTraining` = 1, 'Passed', IF ( a.`StatusTraining` = 2, 'Not Passed', 'No Status' ) ) AS TrainingStatus,
	v.ClusterID 
FROM
	`ktv_cpg_batch_trainings_farmers` a
	LEFT JOIN `ktv_cpg_batch_trainings` b ON b.`CpgBatchTrainingID` = a.`CpgBatchTrainingID`
	LEFT JOIN `ktv_cpg_batch` c ON c.`CpgBatchID` = b.`CpgBatchID`
	LEFT JOIN `ktv_cocoa_farmer` d ON d.`FarmerID` = a.`FarmerID` 
	AND d.`StatusCode` = 'active'
	LEFT JOIN `ktv_ims` e ON e.`IMSID` = b.`IMSID`
	LEFT JOIN `ktv_ims_master` f ON f.`IMSMasterID` = e.`IMSMasterID`
	LEFT JOIN `ktv_certification_holders` g ON g.`CertHolderID` = f.`CertHolderID`
	LEFT JOIN `ktv_first_buyer` h ON h.`FirstBuyerID` = e.`FirstBuyerID`
	LEFT JOIN `ktv_program_partner` i ON i.`PartnerID` = h.`FirstBuyerPartnerID`
	LEFT JOIN `ktv_supplychain_org` j ON j.`SupplychainID` = g.`SupplychainID`
	LEFT JOIN `ktv_traders` k ON k.`TraderID` = j.`OrgID` 
	AND j.`OrgType` = 'trader'
	LEFT JOIN `ktv_cooperatives` l ON l.`CoopID` = j.`OrgID` 
	AND j.`OrgType` = 'koperasi'
	LEFT JOIN `ktv_village` m ON m.`VillageID` = d.`VillageID`
	LEFT JOIN `ktv_subdistrict` n ON n.`SubDistrictID` = m.`SubDistrictID`
	LEFT JOIN `ktv_district` o ON o.`DistrictID` = n.`DistrictID`
	LEFT JOIN `ktv_province` p ON p.`ProvinceID` = o.`ProvinceID`
	LEFT JOIN `ktv_cpg` q ON q.`CPGid` = d.`CPGid`
	LEFT JOIN `ktv_cpg_trainings` r ON r.`CpgTrainingsID` = b.`CPGtrainingsID`
	LEFT JOIN `ktv_first_buyer_program` s ON s.`ProgID` = e.`ProgID`
	LEFT JOIN `ktv_cpg_batch_trainings_sub_topics` t ON t.`CpgBatchTrainingID` = b.`CpgBatchTrainingID`
	LEFT JOIN ktv_ims_training_gap_coc u ON u.IMSID = e.IMSID 
	AND u.FarmerID = d.FarmerID
	LEFT JOIN ktv_ims_soc_sel AS v ON v.DestObjID = a.FarmerID AND v.IMSID = b.IMSID 
WHERE
	e.IMSID IN (252,278)
	AND a.`StatusCode` = 'active' 
	AND b.`StatusCode` = 'active' 
	AND d.`StatusCode` = 'active' 
	AND b.`CPGtrainingsID` = 14 
	AND t.`SubCpgTrainingsID` = 53 
	#AND v.ObjType = 'Applicant' 
GROUP BY
	d.FarmerID;";
        $query = $this->db->query($sql);
        
	if ($query) {
            $results['detailCOCWave3'] = "detailCOCWave3 updated.";
        } else {
            $results['detailCOCWave3'] .= "||detailCOCWave3 Failed to update";
        }
        return $results;
    }

    function insert_detail_COCR1()
	{
		$sql = "DELETE FROM ktv_jbcocoa_summary_coc_refresh_one WHERE DateGenerated = DATE(NOW())";
		$query = $this->db->query($sql);
		$sql = "INSERT INTO ktv_jbcocoa_summary_coc_refresh_one (
                DateGenerated,
                CertHolderID,
                DistrictID,
                IMSID,
                ClusterID,
                ProgID,
                Program,
                CertificateHolders,
                Responsible,
                ParticipantID,
                ParticipantName,
                GroupID,
                FarmerGroup,
                Province,
                District,
                SubDistrict,
                Village,
                EventName,
                EventDate,
                TrainingType,
                NrOfTrainingDays,
                PercentageOfAttendance,
                TrainingStatus
           ) 
           SELECT 
                DATE(NOW()),
                g.CertHolderID,
                o.DistrictID,
                e.`IMSID`,
                v.ClusterID,
                s.ProgID,
                CONCAT(
                     s.`ProgramName`,
                     ' (',
                     s.`ProgramYear`,
                     ')'
                ) AS Program,
                IF(
                     j.OrgType = 'trader',
                     k.`Company`,
                     IF(
                          j.OrgType = 'koperasi',
                          l.CoopName,
                          '-'
                     )
                ) AS CertificateHolders,
                IF(
                     j.OrgType = 'trader',
                     k.TraderName,
                     IF(
                          j.OrgType = 'koperasi',
                          l.CoopName,
                          '-'
                     )
                ) AS Responsible,
                d.`FarmerID` AS ParticipantID,
                d.`FarmerName` AS ParticipantName,
                b.`CPGid` AS GroupID,
                q.`GroupName` AS FarmerGroup,
                p.`Province`,
                o.`District`,
                n.`SubDistrict`,
                m.`Village`,
                r.`CpgTrainings` AS EventName,
                DATE(b.`TrainingStart`) AS EventDate,
                IF(
                     b.`ActivityType` = 'full',
                     '1st Year',
                     IF(
                          b.`ActivityType` = 'refresh',
                          '2nd Year',
                          ''
                     )
                ) AS TrainingType,
                b.`TrainingDays` AS NrOfTrainingDays,
                IFNULL(a.`Percentage`, '-') AS PercentageOfAttendance,
                IF(
                     a.`StatusTraining` = 1,
                     'Passed',
                     IF(
                          a.`StatusTraining` = 2,
                          'Not Passed',
                          'No Status'
                     )
                ) AS TrainingStatus 
           FROM
                `ktv_cpg_batch_trainings_farmers` a 
                LEFT JOIN `ktv_cpg_batch_trainings` b 
                     ON b.`CpgBatchTrainingID` = a.`CpgBatchTrainingID` 
                LEFT JOIN `ktv_cpg_batch` c 
                     ON c.`CpgBatchID` = b.`CpgBatchID` 
                LEFT JOIN `ktv_cocoa_farmer` d 
                     ON d.`FarmerID` = a.`FarmerID` 
                     AND d.`StatusCode` = 'active' 
                LEFT JOIN `ktv_ims` e 
                     ON e.`IMSID` = b.`IMSID` 
                LEFT JOIN `ktv_ims_master` f 
                     ON f.`IMSMasterID` = e.`IMSMasterID` 
                LEFT JOIN `ktv_certification_holders` g 
                     ON g.`CertHolderID` = f.`CertHolderID` 
                LEFT JOIN `ktv_first_buyer` h 
                     ON h.`FirstBuyerID` = e.`FirstBuyerID` 
                LEFT JOIN `ktv_program_partner` i 
                     ON i.`PartnerID` = h.`FirstBuyerPartnerID` 
                LEFT JOIN `ktv_supplychain_org` j 
                     ON j.`SupplychainID` = g.`SupplychainID` 
                LEFT JOIN `ktv_traders` k 
                     ON k.`TraderID` = j.`OrgID` 
                     AND j.`OrgType` = 'trader' 
                LEFT JOIN `ktv_cooperatives` l 
                     ON l.`CoopID` = j.`OrgID` 
                     AND j.`OrgType` = 'koperasi' 
                LEFT JOIN `ktv_village` m 
                     ON m.`VillageID` = d.`VillageID` 
                LEFT JOIN `ktv_subdistrict` n 
                     ON n.`SubDistrictID` = m.`SubDistrictID` 
                LEFT JOIN `ktv_district` o 
                     ON o.`DistrictID` = n.`DistrictID` 
                LEFT JOIN `ktv_province` p 
                     ON p.`ProvinceID` = o.`ProvinceID` 
                LEFT JOIN `ktv_cpg` q 
                     ON q.`CPGid` = d.`CPGid` 
                LEFT JOIN `ktv_cpg_trainings` r 
                     ON r.`CpgTrainingsID` = b.`CPGtrainingsID` 
                LEFT JOIN `ktv_first_buyer_program` s 
                     ON s.`ProgID` = e.`ProgID` 
                LEFT JOIN `ktv_cpg_batch_trainings_sub_topics` t 
                     ON t.`CpgBatchTrainingID` = b.`CpgBatchTrainingID` 
               LEFT JOIN ktv_ims_training_gap_coc u
                     ON u.IMSID = e.IMSID and u.FarmerID = d.FarmerID
								 LEFT JOIN ktv_ims_soc_sel as v ON v.DestObjID = u.FarmerID and v.IMSID = u.IMSID
           WHERE e.`IMSID` IN (207 ) 
                AND a.`StatusCode` = 'active' 
                AND b.`StatusCode` = 'active' 
                AND d.`StatusCode` = 'active' 
                AND b.`CPGtrainingsID` = 14 
                AND t.`SubCpgTrainingsID` = 53 
                AND v.ObjType = 'Existing Farmer'
           GROUP BY d.FarmerID ;";
		$query = $this->db->query($sql); //detailCOCR1
		if ($query) {
			$results['detailCOCR1'] = "detailCOCR1 updated.";
		} else {
			$results['detailCOCR1'] .= "||detailCOCR1 Failed to update";
		}
		return $results;
     }
     
     function insert_detail_COCR2()
	{
		$sql = "DELETE FROM ktv_jbcocoa_summary_coc_refresh_two WHERE DateGenerated = DATE(NOW())";
		$query = $this->db->query($sql);
		$sql = "INSERT INTO ktv_jbcocoa_summary_coc_refresh_two (
                DateGenerated,
                CertHolderID,
                DistrictID,
                IMSID,
                ClusterID,
                ProgID,
                Program,
                CertificateHolders,
                Responsible,
                ParticipantID,
                ParticipantName,
                GroupID,
                FarmerGroup,
                Province,
                District,
                SubDistrict,
                Village,
                EventName,
                EventDate,
                TrainingType,
                NrOfTrainingDays,
                PercentageOfAttendance,
                TrainingStatus
           ) 
           SELECT 
                DATE(NOW()),
                g.CertHolderID,
                o.DistrictID,
                e.`IMSID`,
                v.ClusterID,
                s.ProgID,
                CONCAT(
                     s.`ProgramName`,
                     ' (',
                     s.`ProgramYear`,
                     ')'
                ) AS Program,
                IF(
                     j.OrgType = 'trader',
                     k.`Company`,
                     IF(
                          j.OrgType = 'koperasi',
                          l.CoopName,
                          '-'
                     )
                ) AS CertificateHolders,
                IF(
                     j.OrgType = 'trader',
                     k.TraderName,
                     IF(
                          j.OrgType = 'koperasi',
                          l.CoopName,
                          '-'
                     )
                ) AS Responsible,
                d.`FarmerID` AS ParticipantID,
                d.`FarmerName` AS ParticipantName,
                b.`CPGid` AS GroupID,
                q.`GroupName` AS FarmerGroup,
                p.`Province`,
                o.`District`,
                n.`SubDistrict`,
                m.`Village`,
                r.`CpgTrainings` AS EventName,
                DATE(b.`TrainingStart`) AS EventDate,
                IF(
                     b.`ActivityType` = 'full',
                     '1st Year',
                     IF(
                          b.`ActivityType` = 'refresh',
                          '2nd Year',
                          ''
                     )
                ) AS TrainingType,
                b.`TrainingDays` AS NrOfTrainingDays,
                IFNULL(a.`Percentage`, '-') AS PercentageOfAttendance,
                IF(
                     a.`StatusTraining` = 1,
                     'Passed',
                     IF(
                          a.`StatusTraining` = 2,
                          'Not Passed',
                          'No Status'
                     )
                ) AS TrainingStatus 
           FROM
                `ktv_cpg_batch_trainings_farmers` a 
                LEFT JOIN `ktv_cpg_batch_trainings` b 
                     ON b.`CpgBatchTrainingID` = a.`CpgBatchTrainingID` 
                LEFT JOIN `ktv_cpg_batch` c 
                     ON c.`CpgBatchID` = b.`CpgBatchID` 
                LEFT JOIN `ktv_cocoa_farmer` d 
                     ON d.`FarmerID` = a.`FarmerID` 
                     AND d.`StatusCode` = 'active' 
                LEFT JOIN `ktv_ims` e 
                     ON e.`IMSID` = b.`IMSID` 
                LEFT JOIN `ktv_ims_master` f 
                     ON f.`IMSMasterID` = e.`IMSMasterID` 
                LEFT JOIN `ktv_certification_holders` g 
                     ON g.`CertHolderID` = f.`CertHolderID` 
                LEFT JOIN `ktv_first_buyer` h 
                     ON h.`FirstBuyerID` = e.`FirstBuyerID` 
                LEFT JOIN `ktv_program_partner` i 
                     ON i.`PartnerID` = h.`FirstBuyerPartnerID` 
                LEFT JOIN `ktv_supplychain_org` j 
                     ON j.`SupplychainID` = g.`SupplychainID` 
                LEFT JOIN `ktv_traders` k 
                     ON k.`TraderID` = j.`OrgID` 
                     AND j.`OrgType` = 'trader' 
                LEFT JOIN `ktv_cooperatives` l 
                     ON l.`CoopID` = j.`OrgID` 
                     AND j.`OrgType` = 'koperasi' 
                LEFT JOIN `ktv_village` m 
                     ON m.`VillageID` = d.`VillageID` 
                LEFT JOIN `ktv_subdistrict` n 
                     ON n.`SubDistrictID` = m.`SubDistrictID` 
                LEFT JOIN `ktv_district` o 
                     ON o.`DistrictID` = n.`DistrictID` 
                LEFT JOIN `ktv_province` p 
                     ON p.`ProvinceID` = o.`ProvinceID` 
                LEFT JOIN `ktv_cpg` q 
                     ON q.`CPGid` = d.`CPGid` 
                LEFT JOIN `ktv_cpg_trainings` r 
                     ON r.`CpgTrainingsID` = b.`CPGtrainingsID` 
                LEFT JOIN `ktv_first_buyer_program` s 
                     ON s.`ProgID` = e.`ProgID` 
                LEFT JOIN `ktv_cpg_batch_trainings_sub_topics` t 
                     ON t.`CpgBatchTrainingID` = b.`CpgBatchTrainingID` 
               LEFT JOIN ktv_ims_training_gap_coc u
                     ON u.IMSID = e.IMSID and u.FarmerID = d.FarmerID
			LEFT JOIN ktv_ims_soc_sel as v ON v.DestObjID = u.FarmerID and v.IMSID = u.IMSID
           WHERE e.`IMSID` IN (207 ) 
                AND a.`StatusCode` = 'active' 
                AND b.`StatusCode` = 'active' 
                AND d.`StatusCode` = 'active' 
                AND b.`CPGtrainingsID` = 14 
                AND t.`SubCpgTrainingsID` = 53 
                AND v.ObjType = 'Existing Certified Farmer'
           GROUP BY d.FarmerID ;";
		$query = $this->db->query($sql); //detailCOCR2
		if ($query) {
			$results['detailCOCR2'] = "detailCOCR2 updated.";
		} else {
			$results['detailCOCR2'] .= "||detailCOCR2 Failed to update";
		}
		return $results;
	}
	
	function insert_detail_FinalTraining()
	{
		$sql = "DELETE FROM ktv_jbcocoa_summary_final_training WHERE DateGenerated = DATE(NOW())";
		$query = $this->db->query($sql);
		$sql = "INSERT INTO ktv_jbcocoa_summary_final_training (
                DateGenerated,
                CertHolderID,
                DistrictID,
                IMSID,
                ClusterID,
                ProgID,
                Program,
                CertificateHolders,
                Responsible,
                ParticipantID,
                ParticipantName,
                GroupID,
                FarmerGroup,
                Province,
                District,
                SubDistrict,
                Village,
                FinalPercentageOfAttendance,
                FinalTrainingStatus
           ) 
           SELECT 
                DATE(NOW()),
                g.CertHolderID,
                o.DistrictID,
                e.`IMSID`,
			 v.ClusterID,
                s.ProgID,
                CONCAT(
                     s.`ProgramName`,
                     ' (',
                     s.`ProgramYear`,
                     ')'
                ) AS Program,
                IF(
                     j.OrgType = 'trader',
                     k.`Company`,
                     IF(
                          j.OrgType = 'koperasi',
                          l.CoopName,
                          '-'
                     )
                ) AS CertificateHolders,
                IF(
                     j.OrgType = 'trader',
                     k.TraderName,
                     IF(
                          j.OrgType = 'koperasi',
                          l.CoopName,
                          '-'
                     )
                ) AS Responsible,
                d.`FarmerID` AS ParticipantID,
                d.`FarmerName` AS ParticipantName,
                b.`CPGid` AS GroupID,
                q.`GroupName` AS FarmerGroup,
                p.`Province`,
                o.`District`,
                n.`SubDistrict`,
                m.`Village`,
--                 r.`CpgTrainings` AS EventName,
--                 DATE(b.`TrainingStart`) AS EventDate,
--                 IF(
--                      b.`ActivityType` = 'full',
--                      '1st Year',
--                      IF(
--                           b.`ActivityType` = 'refresh',
--                           '2nd Year',
--                           ''
--                      )
--                 ) AS TrainingType,
--                 b.`TrainingDays` AS NrOfTrainingDays,
                IFNULL(a.`Percentage`, '-') AS PercentageOfAttendance,
                IF(
                     a.`StatusTraining` = 1,
                     'Passed',
                     IF(
                          a.`StatusTraining` = 2,
                          'Not Passed',
                          'No Status'
                     )
                ) AS TrainingStatus 
           FROM
                `ktv_cpg_batch_trainings_farmers` a 
                LEFT JOIN `ktv_cpg_batch_trainings` b 
                     ON b.`CpgBatchTrainingID` = a.`CpgBatchTrainingID` 
							 JOIN (
								 SELECT CpgBatchTrainingID, FarmerID, Attendance1 as StatusAttend
								 FROM ktv_cpg_batch_trainings_attendance
								 WHERE Attendance1 = 1
								 GROUP BY CpgBatchTrainingID, FarmerID 
							 ) as bb ON bb.CpgBatchTrainingID = a.CpgBatchTrainingID and bb.FarmerID = a.FarmerID
                LEFT JOIN `ktv_cpg_batch` c 
                     ON c.`CpgBatchID` = b.`CpgBatchID` 
                LEFT JOIN `ktv_cocoa_farmer` d 
                     ON d.`FarmerID` = a.`FarmerID` 
                     AND d.`StatusCode` = 'active' 
                LEFT JOIN `ktv_ims` e 
                     ON e.`IMSID` = b.`IMSID` 
                LEFT JOIN `ktv_ims_master` f 
                     ON f.`IMSMasterID` = e.`IMSMasterID` 
                LEFT JOIN `ktv_certification_holders` g 
                     ON g.`CertHolderID` = f.`CertHolderID` 
                LEFT JOIN `ktv_first_buyer` h 
                     ON h.`FirstBuyerID` = e.`FirstBuyerID` 
                LEFT JOIN `ktv_program_partner` i 
                     ON i.`PartnerID` = h.`FirstBuyerPartnerID` 
                LEFT JOIN `ktv_supplychain_org` j 
                     ON j.`SupplychainID` = g.`SupplychainID` 
                LEFT JOIN `ktv_traders` k 
                     ON k.`TraderID` = j.`OrgID` 
                     AND j.`OrgType` = 'trader' 
                LEFT JOIN `ktv_cooperatives` l 
                     ON l.`CoopID` = j.`OrgID` 
                     AND j.`OrgType` = 'koperasi' 
                LEFT JOIN `ktv_village` m 
                     ON m.`VillageID` = d.`VillageID` 
                LEFT JOIN `ktv_subdistrict` n 
                     ON n.`SubDistrictID` = m.`SubDistrictID` 
                LEFT JOIN `ktv_district` o 
                     ON o.`DistrictID` = n.`DistrictID` 
                LEFT JOIN `ktv_province` p 
                     ON p.`ProvinceID` = o.`ProvinceID` 
                LEFT JOIN `ktv_cpg` q 
                     ON q.`CPGid` = d.`CPGid` 
                LEFT JOIN `ktv_cpg_trainings` r 
                     ON r.`CpgTrainingsID` = b.`CPGtrainingsID` 
                LEFT JOIN `ktv_first_buyer_program` s 
                     ON s.`ProgID` = e.`ProgID` 
                LEFT JOIN ktv_ims_training_gap_coc t
                     ON t.IMSID = e.IMSID and t.FarmerID = a.FarmerID
               LEFT JOIN ktv_ims_soc_sel as v ON v.DestObjID = t.FarmerID and v.IMSID = t.IMSID
           WHERE e.`IMSID` IN (207) 
                AND a.`StatusCode` = 'active' 
                AND b.`StatusCode` = 'active' 
                AND d.`StatusCode` = 'active' 
                AND b.`CPGtrainingsID` IN (1 , 14)
                AND v.ObjType IN ('Applicant','Existing Farmer','Existing Certified Farmer')
           GROUP BY d.`FarmerID`";
		$query = $this->db->query($sql); //detailFinalTraining
		if ($query) {
			$results['detailFinalTraining'] = "detailFinalTraining updated.";
		} else {
			$results['detailFinalTraining'] .= "||detailFinalTraining Failed to update";
		}
		return $results;
	}
	
	function insert_detail_GAP()
	{
		$sql = "DELETE FROM ktv_jbcocoa_summary_gap_first_year WHERE DateGenerated = DATE(NOW())";
		$query = $this->db->query($sql);
		$sql = "INSERT INTO ktv_jbcocoa_summary_gap_first_year (
                DateGenerated,
                CertHolderID,
                DistrictID,
                IMSID,
                ClusterID,
                ProgID,
                Program,
                CertificateHolders,
                Responsible,
                ParticipantID,
                ParticipantName,
                GroupID,
                FarmerGroup,
                Province,
                District,
                SubDistrict,
                Village,
                EventName,
                EventDate,
                TrainingType,
                NrOfTrainingDays,
                PercentageOfAttendance,
                TrainingStatus
           ) 
           SELECT 
                DATE(NOW()),
                g.CertHolderID,
                o.DistrictID,
                e.`IMSID`,
			 v.ClusterID,
                s.ProgID,
                CONCAT(
                     s.`ProgramName`,
                     ' (',
                     s.`ProgramYear`,
                     ')'
                ) AS Program,
                IF(
                     j.OrgType = 'trader',
                     k.`Company`,
                     IF(
                          j.OrgType = 'koperasi',
                          l.CoopName,
                          '-'
                     )
                ) AS CertificateHolders,
                IF(
                     j.OrgType = 'trader',
                     k.TraderName,
                     IF(
                          j.OrgType = 'koperasi',
                          l.CoopName,
                          '-'
                     )
                ) AS Responsible,
                d.`FarmerID` AS ParticipantID,
                d.`FarmerName` AS ParticipantName,
                b.`CPGid` AS GroupID,
                q.`GroupName` AS FarmerGroup,
                p.`Province`,
                o.`District`,
                n.`SubDistrict`,
                m.`Village`,
                r.`CpgTrainings` AS EventName,
                DATE(b.`TrainingStart`) AS EventDate,
                IF(
                     b.`ActivityType` = 'full',
                     '1st Year',
                     IF(
                          b.`ActivityType` = 'refresh',
                          '2nd Year',
                          ''
                     )
                ) AS TrainingType,
                b.`TrainingDays` AS NrOfTrainingDays,
                IFNULL(a.`Percentage`, '-') AS PercentageOfAttendance,
                IF(
                     a.`StatusTraining` = 1,
                     'Passed',
                     IF(
                          a.`StatusTraining` = 2,
                          'Not Passed',
                          'No Status'
                     )
                ) AS TrainingStatus 
           FROM
                `ktv_cpg_batch_trainings_farmers` a 
                LEFT JOIN `ktv_cpg_batch_trainings` b 
                     ON b.`CpgBatchTrainingID` = a.`CpgBatchTrainingID` 
                LEFT JOIN `ktv_cpg_batch` c 
                     ON c.`CpgBatchID` = b.`CpgBatchID` 
                LEFT JOIN `ktv_cocoa_farmer` d 
                     ON d.`FarmerID` = a.`FarmerID` 
                     AND d.`StatusCode` = 'active' 
                LEFT JOIN `ktv_ims` e 
                     ON e.`IMSID` = b.`IMSID` 
                LEFT JOIN `ktv_ims_master` f 
                     ON f.`IMSMasterID` = e.`IMSMasterID` 
                LEFT JOIN `ktv_certification_holders` g 
                     ON g.`CertHolderID` = f.`CertHolderID` 
                LEFT JOIN `ktv_first_buyer` h 
                     ON h.`FirstBuyerID` = e.`FirstBuyerID` 
                LEFT JOIN `ktv_program_partner` i 
                     ON i.`PartnerID` = h.`FirstBuyerPartnerID` 
                LEFT JOIN `ktv_supplychain_org` j 
                     ON j.`SupplychainID` = g.`SupplychainID` 
                LEFT JOIN `ktv_traders` k 
                     ON k.`TraderID` = j.`OrgID` 
                     AND j.`OrgType` = 'trader' 
                LEFT JOIN `ktv_cooperatives` l 
                     ON l.`CoopID` = j.`OrgID` 
                     AND j.`OrgType` = 'koperasi' 
                LEFT JOIN `ktv_village` m 
                     ON m.`VillageID` = d.`VillageID` 
                LEFT JOIN `ktv_subdistrict` n 
                     ON n.`SubDistrictID` = m.`SubDistrictID` 
                LEFT JOIN `ktv_district` o 
                     ON o.`DistrictID` = n.`DistrictID` 
                LEFT JOIN `ktv_province` p 
                     ON p.`ProvinceID` = o.`ProvinceID` 
                LEFT JOIN `ktv_cpg` q 
                     ON q.`CPGid` = d.`CPGid` 
                LEFT JOIN `ktv_cpg_trainings` r 
                     ON r.`CpgTrainingsID` = b.`CPGtrainingsID` 
                LEFT JOIN `ktv_first_buyer_program` s 
                     ON s.`ProgID` = e.`ProgID` 
                LEFT JOIN ktv_ims_training_gap_coc t
                     ON t.IMSID = e.IMSID and t.FarmerID = a.FarmerID
               LEFT JOIN ktv_ims_soc_sel as v ON v.DestObjID = t.FarmerID and v.IMSID = t.IMSID
           WHERE e.`IMSID` IN (207) 
                AND a.`StatusCode` = 'active' 
                AND b.`StatusCode` = 'active' 
                AND d.`StatusCode` = 'active' 
                AND b.`CPGtrainingsID` = 1 
                AND v.ObjType IN ('Applicant','Existing Farmer')
           GROUP BY d.`FarmerID`";
		$query = $this->db->query($sql); //detailGAP
		if ($query) {
			$results['detailGAP'] = "detailGAP updated.";
		} else {
			$results['detailGAP'] .= "||detailGAP Failed to update";
		}
		return $results;
	}
	
	function insert_detail_GAPR()
	{
		$sql = "DELETE FROM ktv_jbcocoa_summary_gap_refresh WHERE DateGenerated = DATE(NOW())";
		$query = $this->db->query($sql);
		$sql = "INSERT INTO ktv_jbcocoa_summary_gap_refresh (
                DateGenerated,
                CertHolderID,
                DistrictID,
                IMSID,
                ClusterID,
                ProgID,
                Program,
                CertificateHolders,
                Responsible,
                ParticipantID,
                ParticipantName,
                GroupID,
                FarmerGroup,
                Province,
                District,
                SubDistrict,
                Village,
                EventName,
                EventDate,
                TrainingType,
                NrOfTrainingDays,
                PercentageOfAttendance,
                TrainingStatus
           ) 
           SELECT 
                DATE(NOW()),
                g.CertHolderID,
                o.DistrictID,
                e.`IMSID`,
			 v.ClusterID,
                s.ProgID,
                CONCAT(
                     s.`ProgramName`,
                     ' (',
                     s.`ProgramYear`,
                     ')'
                ) AS Program,
                IF(
                     j.OrgType = 'trader',
                     k.`Company`,
                     IF(
                          j.OrgType = 'koperasi',
                          l.CoopName,
                          '-'
                     )
                ) AS CertificateHolders,
                IF(
                     j.OrgType = 'trader',
                     k.TraderName,
                     IF(
                          j.OrgType = 'koperasi',
                          l.CoopName,
                          '-'
                     )
                ) AS Responsible,
                d.`FarmerID` AS ParticipantID,
                d.`FarmerName` AS ParticipantName,
                b.`CPGid` AS GroupID,
                q.`GroupName` AS FarmerGroup,
                p.`Province`,
                o.`District`,
                n.`SubDistrict`,
                m.`Village`,
                r.`CpgTrainings` AS EventName,
                DATE(b.`TrainingStart`) AS EventDate,
                IF(
                     b.`ActivityType` = 'full',
                     '1st Year',
                     IF(
                          b.`ActivityType` = 'refresh',
                          '2nd Year',
                          ''
                     )
                ) AS TrainingType,
                b.`TrainingDays` AS NrOfTrainingDays,
                IFNULL(a.`Percentage`, '-') AS PercentageOfAttendance,
                IF(
                     a.`StatusTraining` = 1,
                     'Passed',
                     IF(
                          a.`StatusTraining` = 2,
                          'Not Passed',
                          'No Status'
                     )
                ) AS TrainingStatus 
           FROM
                `ktv_cpg_batch_trainings_farmers` a 
                LEFT JOIN `ktv_cpg_batch_trainings` b 
                     ON b.`CpgBatchTrainingID` = a.`CpgBatchTrainingID` 
                LEFT JOIN `ktv_cpg_batch` c 
                     ON c.`CpgBatchID` = b.`CpgBatchID` 
                LEFT JOIN `ktv_cocoa_farmer` d 
                     ON d.`FarmerID` = a.`FarmerID` 
                     AND d.`StatusCode` = 'active' 
                LEFT JOIN `ktv_ims` e 
                     ON e.`IMSID` = b.`IMSID` 
                LEFT JOIN `ktv_ims_master` f 
                     ON f.`IMSMasterID` = e.`IMSMasterID` 
                LEFT JOIN `ktv_certification_holders` g 
                     ON g.`CertHolderID` = f.`CertHolderID` 
                LEFT JOIN `ktv_first_buyer` h 
                     ON h.`FirstBuyerID` = e.`FirstBuyerID` 
                LEFT JOIN `ktv_program_partner` i 
                     ON i.`PartnerID` = h.`FirstBuyerPartnerID` 
                LEFT JOIN `ktv_supplychain_org` j 
                     ON j.`SupplychainID` = g.`SupplychainID` 
                LEFT JOIN `ktv_traders` k 
                     ON k.`TraderID` = j.`OrgID` 
                     AND j.`OrgType` = 'trader' 
                LEFT JOIN `ktv_cooperatives` l 
                     ON l.`CoopID` = j.`OrgID` 
                     AND j.`OrgType` = 'koperasi' 
                LEFT JOIN `ktv_village` m 
                     ON m.`VillageID` = d.`VillageID` 
                LEFT JOIN `ktv_subdistrict` n 
                     ON n.`SubDistrictID` = m.`SubDistrictID` 
                LEFT JOIN `ktv_district` o 
                     ON o.`DistrictID` = n.`DistrictID` 
                LEFT JOIN `ktv_province` p 
                     ON p.`ProvinceID` = o.`ProvinceID` 
                LEFT JOIN `ktv_cpg` q 
                     ON q.`CPGid` = d.`CPGid` 
                LEFT JOIN `ktv_cpg_trainings` r 
                     ON r.`CpgTrainingsID` = b.`CPGtrainingsID` 
                LEFT JOIN `ktv_first_buyer_program` s 
                     ON s.`ProgID` = e.`ProgID` 
                LEFT JOIN ktv_ims_training_gap_coc t
                     ON t.IMSID = e.IMSID and t.FarmerID = a.FarmerID
			 LEFT JOIN ktv_ims_soc_sel as v ON v.DestObjID = t.FarmerID and v.IMSID = t.IMSID
           WHERE e.`IMSID` IN (207) 
                AND a.`StatusCode` = 'active' 
                AND b.`StatusCode` = 'active' 
                AND d.`StatusCode` = 'active' 
                AND b.`CPGtrainingsID` = 1 
                AND v.ObjType IN ('Existing Certified Farmer')
           GROUP BY d.`FarmerID`";
		$query = $this->db->query($sql); //detailGAPR
		if ($query) {
			$results['detailGAPR'] = "detailGAPR updated.";
		} else {
			$results['detailGAPR'] .= "||detailGAPR Failed to update";
		}
		return $results;
	}
	
	function insert_detail_IMSSupport()
	{
		$sql = "DELETE FROM ktv_jbcocoa_summary_ims_support WHERE DateGenerated = DATE(NOW())";
		$query = $this->db->query($sql);
		$sql = "INSERT INTO ktv_jbcocoa_summary_ims_support (
                DateGenerated,
                CertHolderID,
                DistrictID,
                IMSID,
                ClusterID,
                ProgID,
                Program,
                CertificateHolders,
                Responsible,
                TransactionID,
                FarmerID,
                FarmerName,
                Province,
                District,
                SubDistrict,
                Village,
                GroupID,
                FarmerGroup,
                DateOfTransaction,
                SupplyBatchID,
                PONumber,
                BatchNumber,
                BUID,
                BuyingUnit,
                SentID,
                SentName,
                CHID,
                CertificateHolder,
                NetKgBS,
                NetKgCH
           ) 
           SELECT 
               DATE(NOW()) DateGenerated,
               i.CertHolderID,
               dt.DistrictID,
               a.IMSID,
			afl.ClusterID,
               kfbp.ProgID,
               CONCAT(kfbp.ProgramName, ' (', kfbp.ProgramYear,')') ProgramName,
               kch.CertHolderOrgName,
               kch.CertHolderResponsible,
               dt.FarmerTransID,
               dt.FarmerID,
               dt.FarmerName,
               dt.Province,
               dt.District,
               dt.SubDistrict,
               dt.Village,
               dt.CPGId,
               dt.GroupName,
               dt.FarmerDate,
               IFNULL(dt.batchid_1, dt.batchid_2) batchid,
               IFNULL(dt.destpo_1, dt.destpo_2) destpo,
               IFNULL(dt.batchnumber_1, dt.batchnumber_2) batchnumber,
               IFNULL(dt.supplyorgid_1, dt.supplyorgid_2) supplyorgid,
               IFNULL(dt.name_1, dt.name_2) name,
               IF(dt.supplyorgid_1 IS NULL, dt.supplyorgid_3, dt.supplyorgid_2) supplydestorgid,
               IF(dt.supplyorgid_1 IS NULL, dt.name_3, dt.name_2) destname,
               kch.SupplychainID,
               dt.name_2,
               dt.netto_1,
               dt.netto_farmer_2
          FROM
               ktv_ims i 
               LEFT JOIN ktv_certification_holders kch ON kch.CertHolderID=i.CertHolderID
               LEFT JOIN ktv_first_buyer kfb ON kfb.FirstBuyerID=i.FirstBuyerID
               LEFT JOIN ktv_cocoa_certification_certified_farmer a ON a.IMSID=i.IMSID
               LEFT JOIN ktv_cpg b ON a.`CPGid` = b.`CPGid`
               LEFT JOIN (
                    SELECT
                         FarmerID, 
                         FarmerName,
                         rpt.CPGId,
                         GroupName,
                         Village,
                         SubDistrict,
                         DistrictID,
                         District,
                         Province,
                         SUBSTR(date_1,1,10) FarmerDate,
                         transid_1 FarmerTransID,
                         transcert_1 SupplyType,
                         IFNULL(imsid_1, imsid_2) IMSID,
                         
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                         batchid_1) batchid_1,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                         transid_1) transid_1,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                         date_1) date_1,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                         supplyorgid_1) supplyorgid_1,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                         faktur_1) faktur_1,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                         bruto_1) bruto_1,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                         netto_1) netto_1,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                         orgtype_1) orgtype_1,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                         name_1) name_1,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                         batchnumber_1) batchnumber_1,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                         destpo_1) destpo_1,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                         status_1) status_1,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                         deliverydate_1) deliverydate_1,
                         
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,batchid_1,
                         IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, batchid_2)) batchid_2,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,transid_1,
                         IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, transid_2)) transid_2,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,date_1,
                         IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, date_2)) date_2,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,supplyorgid_1,
                         IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, supplyorgid_2)) supplyorgid_2,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,faktur_1,
                         IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, faktur_2)) faktur_2,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,bruto_1,
                         IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, bruto_2)) bruto_2,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,netto_1,
                         IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, netto_farmer_2)) netto_farmer_2,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,bruto_1,
                         IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, bruto_farmer_2)) bruto_farmer_2,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,netto_1,
                         IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, netto_2)) netto_2,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,orgtype_1,
                         IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, orgtype_2)) orgtype_2,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,name_1,
                         IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, name_2)) name_2,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,batchnumber_1,
                         IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, batchnumber_2)) batchnumber_2,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,destpo_1,
                         IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, destpo_2)) destpo_2,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,status_1,
                         IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, status_2)) status_2,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,deliverydate_1,
                         IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, deliverydate_2)) deliverydate_2,

                         IF(orgtype_2='Gudang', supplyorgid_2, supplyorgid_3) supplyorgid_3,
                         IF(orgtype_2='Gudang', IFNULL(date_2, deliverydate_1), IFNULL(date_3, deliverydate_2)) date_3,
                         IF(orgtype_2='Gudang', transid_2, transid_3) transid_3,
                         IF(orgtype_2='Gudang', batchid_2, batchid_3) batchid_3,
                         IF(orgtype_2='Gudang', orgid_2, orgid_3) orgid_3,
                         IF(orgtype_2='Gudang', name_2, name_3) name_3,
                         IF(orgtype_2='Gudang', faktur_2, faktur_3) faktur_3,
                         IF(orgtype_2='Gudang', bruto_2, bruto_3) bruto_3,
                         IF(orgtype_2='Gudang', netto_2, netto_3) netto_3,
                         IF(orgtype_2='Gudang', 
                                   netto_1,
                                   netto_farmer_2
                         ) bruto_farmer_3,
                         IF(orgtype_2='Gudang', 
                                   netto_1,
                                   netto_farmer_2
                         ) netto_farmer_3,
                         IF(orgtype_2='Gudang', destpo_2, destpo_3) destpo_3,
                         IF(orgtype_2='Gudang', status_2, status_3) status_3
               FROM
                         rpt_tc_trans_detail rpt
                         LEFT JOIN view_supplychain_org dest ON dest.SupplychainID=supplydestorgid_1
                         LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchID=IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,batchid_1,IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, batchid_2))
                         LEFT JOIN ktv_supplychain_batch sb1 ON sb1.SupplyBatchID=IF(transcert_1='utz' && (orgtype_2='Gudang' || dest.OrgType='Gudang'),NULL,batchid_1)
               WHERE partnerid_1=22 AND rpt.transcert_1='utz'
               ) dt ON dt.FarmerID=a.FarmerID AND dt.FarmerDate BETWEEN i.ValidityStart AND i.ValidityEnd
               LEFT JOIN ktv_supplychain_transaction wht ON wht.SupplyTransID=dt.transid_3
               LEFT JOIN ktv_first_buyer_program kfbp ON kfbp.ProgID=i.ProgID
               LEFT JOIN ktv_cocoa_certification_pre_afl afl ON afl.IMSID = a.IMSID AND afl.FarmerID = a.FarmerID 
               LEFT JOIN ktv_ims as ims ON ims.IMSID = afl.IMSID
          WHERE
               kfb.FirstBuyerPartnerID=22 AND i.StatusCode='active' AND dt.FarmerID IS NOT NULL AND dt.status_2 IN ('Sent', 'Delivered')
               AND ims.ProgID in (6,7)";
		$query = $this->db->query($sql); //detailIMSSupport
		if ($query) {
			$results['detailIMSSupport'] = "detailIMSSupport updated.";
		} else {
			$results['detailIMSSupport'] .= "||detailIMSSupport Failed to update";
		}
		return $results;
	}
	
	function insert_detail_IMSTraining()
	{
		$sql = "DELETE FROM ktv_jbcocoa_summary_ims_training WHERE DateGenerated = DATE(NOW())";
		$query = $this->db->query($sql);
		$sql = "INSERT INTO ktv_jbcocoa_summary_ims_training (
                DateGenerated,
                CertHolderID,
                DistrictID,
                IMSID,
                ClusterID,
                Program,
                CertificateHolders,
                Responsible,
                ParticipantID,
                ParticipantName,
                `Position`,
                GroupID,
                FarmerGroup,
                Province,
                District,
                SubDistrict,
                Village,
                EventName,
                EventDate,
                NrOfTrainingDays
           ) 
           SELECT 
                DATE(NOW()),
                i.CertHolderID,
                IF(
                     a.PartFarmerID IS NULL,
                     t.`DistrictID`,
                     q.`DistrictID`
                ) AS DistrictID,
                g.`IMSID`,
                CONCAT(
                     y.`ProgramName`,
                     ' (',
                     y.`ProgramYear`,
                     ')'
                ) AS Program,
                IF(
                     l.OrgType = 'trader',
                     m.`Company`,
                     IF(
                          l.OrgType = 'koperasi',
                          n.CoopName,
                          '-'
                     )
                ) AS CertificateHolders,
                IF(
                     l.OrgType = 'trader',
                     m.TraderName,
                     IF(
                          l.OrgType = 'koperasi',
                          n.CoopName,
                          '-'
                     )
                ) AS Responsible,
                IF(
                     a.PartFarmerID IS NULL,
                     e.`StaffID`,
                     d.`FarmerID`
                ) AS ParticipantID,
                IF(
                     a.PartFarmerID IS NULL,
                     f.`PersonNm`,
                     d.`FarmerName`
                ) AS ParticipantName,
                IF(
                     a.PartFarmerID IS NULL,
                     aa.`PositionName`,
                     'Farmer'
                ) AS `Position`,
                d.`CPGid` AS GroupID,
                v.`GroupName` AS FarmerGroup,
                IF(
                     a.PartFarmerID IS NULL,
                     u.`Province`,
                     r.`Province`
                ) AS Province,
                IF(
                     a.PartFarmerID IS NULL,
                     t.`District`,
                     q.`District`
                ) AS District,
                IF(
                     a.PartFarmerID IS NULL,
                     NULL,
                     p.`SubDistrict`
                ) AS SubDistrict,
                IF(
                     a.PartFarmerID IS NULL,
                     NULL,
                     o.`Village`
                ) AS Village,
                w.`CpgTrainings` AS EventName,
                DATE(b.`TrainingStart`) AS EventDate,
                b.`TrainingDays` AS NrOfTrainingDays 
           FROM
                `ktv_business_trainings_participants` a 
                LEFT JOIN `ktv_business_trainings` b 
                     ON b.`BsnTrainingID` = a.`BsnTrainingID` 
                LEFT JOIN `ktv_cpg_batch` c 
                     ON c.`CpgBatchID` = b.`CpgBatchID` 
                LEFT JOIN `ktv_cocoa_farmer` d 
                     ON d.`FarmerID` = a.`PartFarmerID` 
                     AND d.`StatusCode` = 'active' 
                LEFT JOIN `ktv_staffs` e 
                     ON e.`StaffID` = a.`PartStaffID` 
                LEFT JOIN `ktv_persons` f 
                     ON f.`PersonID` = e.`PersonID` 
                LEFT JOIN `ktv_ims` g 
                     ON g.`IMSID` = b.`IMSID` 
                LEFT JOIN `ktv_ims_master` h 
                     ON h.`IMSMasterID` = g.`IMSMasterID` 
                LEFT JOIN `ktv_certification_holders` i 
                     ON i.`CertHolderID` = g.`CertHolderID` 
                LEFT JOIN `ktv_first_buyer` j 
                     ON j.`FirstBuyerID` = g.`FirstBuyerID` 
                LEFT JOIN `ktv_program_partner` k 
                     ON k.`PartnerID` = j.`FirstBuyerPartnerID` 
                LEFT JOIN `ktv_supplychain_org` l 
                     ON l.`SupplychainID` = i.`SupplychainID` 
                LEFT JOIN `ktv_traders` m 
                     ON m.`TraderID` = l.`OrgID` 
                     AND l.`OrgType` = 'trader' 
                LEFT JOIN `ktv_cooperatives` n 
                     ON n.`CoopID` = l.`OrgID` 
                     AND l.`OrgType` = 'koperasi' 
                LEFT JOIN `ktv_village` o 
                     ON o.`VillageID` = d.`VillageID` 
                LEFT JOIN `ktv_subdistrict` p 
                     ON p.`SubDistrictID` = o.`SubDistrictID` 
                LEFT JOIN `ktv_district` q 
                     ON q.`DistrictID` = p.`DistrictID` 
                LEFT JOIN `ktv_province` r 
                     ON r.`ProvinceID` = q.`ProvinceID` 
                LEFT JOIN `ktv_ref_work_area` s 
                     ON s.`WorkAreaID` = e.`WorkAreaID` 
                LEFT JOIN `ktv_district` t 
                     ON t.`DistrictID` = s.`DistrictID` 
                LEFT JOIN `ktv_province` u 
                     ON u.`ProvinceID` = t.`ProvinceID` 
                LEFT JOIN `ktv_cpg` v 
                     ON v.`CPGid` = d.`CPGid` 
                LEFT JOIN `ktv_cpg_trainings` w 
                     ON w.`CpgTrainingsID` = b.`CPGtrainingsID` 
                LEFT JOIN `ktv_business_trainings_sub_topics` `x` 
                     ON x.`BsnTrainingID` = b.`BsnTrainingID` 
                LEFT JOIN `ktv_first_buyer_program` `y` 
                     ON y.`ProgID` = g.`ProgID` 
                LEFT JOIN `ktv_staff_positions` z 
                     ON z.`StaffPosStaffID` = e.`StaffID` 
                LEFT JOIN `ktv_ref_position_type` aa 
                     ON aa.`PositionID` = z.`StaffPosPositionID` 
           WHERE k.`PartnerID` = 8 
                AND g.`IMSID` IN (
                     182,
                     130,
                     171,
                     162,
                     153,
                     189,
                     173,
                     178,
                     154,
                     164,
                     165,
                     168,
                     170,
                     145,
                     152,
                     166,
                     161,
                     167,
                     143,
                     144,
                     148,
                     149,
                     179
                ) 
                AND c.`BatchNumber` = '158' 
                AND a.`StatusCode` = 'active' 
                AND b.`StatusCode` = 'active' 
                AND g.`StatusCode` = 'active' 
                AND w.`CpgTrainingsID` = 14 
                AND x.`SubCpgTrainingsID` IN (6, 55) 
           GROUP BY IFNULL(d.`FarmerID`, e.`StaffID`);";
		$query = $this->db->query($sql); //detailIMSTraining
		if ($query) {
			$results['detailIMSTraining'] = "detailIMSTraining updated.";
		} else {
			$results['detailIMSTraining'] .= "||detailIMSTraining Failed to update";
		}
		return $results;
	}
	
	function insert_detail_ICSEquipment()
	{
		$sql = "DELETE FROM ktv_jbcocoa_summary_ics_equipment WHERE DateGenerated = DATE(NOW())";
		$query = $this->db->query($sql);
		$sql = "INSERT INTO ktv_jbcocoa_summary_ics_equipment (
                DateGenerated,
                CertHolderID,
                DistrictID,
                IMSID,
                ClusterID,
                Program,
                CertificateHolders,
                Responsible,
                FarmerID,
                FarmerName,
                Gender,
                HandPhone,
                Province,
                District,
                SubDistrict,
                Village,
                GroupID,
                FarmerGroup,
                StatusAudit,
                AuditRemarks,
                FirstYearOfCertification,
                YearOfCertification,
                CertificationYear,
                ICSDate,
                PresentYearHarvest,
                SalesQuota,
                PreviousYearHarvest,
                HaCertifiedCropArea,
                NrOfCertifiedPlots,
                HaTotalFarmArea,
                LastYearDelivery,
                Last2ndYearDelivery,
                Last3rdYearDelivery
           ) 
           SELECT 
                DATE(NOW()),
                d.CertHolderID,
                k.DistrictID,
                b.`IMSID`,
			 m.ClusterID,
                CONCAT(
                     l.`ProgramName`,
                     ' (',
                     l.`ProgramYear`,
                     ')'
                ) AS Program,
                IF(
                     h.OrgType = 'trader',
                     i.`Company`,
                     IF(
                          h.OrgType = 'koperasi',
                          j.CoopName,
                          '-'
                     )
                ) AS CertificateHolders,
                IF(
                     h.OrgType = 'trader',
                     i.TraderName,
                     IF(
                          h.OrgType = 'koperasi',
                          j.CoopName,
                          '-'
                     )
                ) AS Responsible,
                g.`FarmerID`,
                g.`FarmerName`,
                a.`Gender`,
                a.`HandPhone`,
                a.`Province`,
                a.`District`,
                a.`SubDistrict`,
                a.`Village`,
                a.`CPGid` AS GroupID,
                a.`GroupName` AS FarmerGroup,
                a.`CertStatusAudit` AS StatusAudit,
                a.`CertAuditRemark` AS AuditRemarks,
                a.`CertFirstYear` AS FirstYearOfCertification,
                a.`YearOfCertification` AS YearOfCertification,
                a.`CertYear` AS CertificationYear,
                a.`CertICSDate` AS ICSDate,
                a.`CertNextHarvest` AS PresentYearHarvest,
                a.`SalesQuota` AS SalesQuota,
                a.`CertHarvest` AS PreviousYearHarvest,
                a.`CertHectare` AS HaCertifiedCropArea,
                a.`CertFarmNr` AS NrOfCertifiedPlots,
                a.`CertTotalHectare` AS HaTotalFarmArea,
                a.`SalesLastYear` AS LastYearDelivery,
                a.`SalesLast2Years` AS Last2ndYearDelivery,
                a.`SalesLast3Years` AS Last3rdYearDelivery 
           FROM
                `ktv_cocoa_certification_certified_farmer` a 
                LEFT JOIN `ktv_ims` b 
                     ON b.`IMSID` = a.`IMSID` 
                LEFT JOIN `ktv_ims_master` c 
                     ON c.`IMSMasterID` = b.`IMSMasterID` 
                LEFT JOIN `ktv_certification_holders` d 
                     ON d.`CertHolderID` = c.`CertHolderID` 
                LEFT JOIN `ktv_first_buyer` e 
                     ON e.`FirstBuyerID` = b.`FirstBuyerID` 
                LEFT JOIN `ktv_program_partner` f 
                     ON f.`PartnerID` = e.`FirstBuyerPartnerID` 
                LEFT JOIN `ktv_cocoa_farmer` g 
                     ON g.`FarmerID` = a.`FarmerID` 
                LEFT JOIN `ktv_supplychain_org` h 
                     ON h.`SupplychainID` = d.`SupplychainID` 
                LEFT JOIN `ktv_traders` i 
                     ON i.`TraderID` = h.`OrgID` 
                     AND h.`OrgType` = 'trader' 
                LEFT JOIN `ktv_cooperatives` j 
                     ON j.`CoopID` = h.`OrgID` 
                     AND h.`OrgType` = 'koperasi' 
                LEFT JOIN ktv_village vil ON vil.VillageID = g.VillageID
                LEFT JOIN ktv_subdistrict subd ON subd.SubDistrictID = vil.SubDistrictID
                LEFT JOIN `ktv_district` k ON k.`DistrictID` = subd.DistrictID 
                LEFT JOIN `ktv_first_buyer_program` l 
                     ON l.`ProgID` = b.`ProgID` 
                LEFT JOIN ktv_cocoa_certification_pre_afl m ON m.IMSID = a.IMSID AND m.FarmerID = a.FarmerID 
           WHERE b.`IMSID` IN (207) 
                AND b.`StatusCode` = 'active' 
                AND c.`StatusCode` = 'active' 
                AND a.CertStatusAudit = 'Comply' 
           ORDER BY d.`CertHolderID`,
                a.`District`,
                a.`CPGid`";
		$query = $this->db->query($sql); //detailICSEquipment
		if ($query) {
			$results['detailICSEquipment'] = "detailICSEquipment updated.";
		} else {
			$results['detailICSEquipment'] .= "||detailICSEquipment Failed to update";
		}
		return $results;
	}
	
	function insert_detail_ICS1()
	{
		$sql = "DELETE FROM ktv_jbcocoa_summary_ics_one WHERE DateGenerated = DATE(NOW())";
		$query = $this->db->query($sql);
		$sql = "INSERT INTO ktv_jbcocoa_summary_ics_one (
                DateGenerated,
                CertHolderID,
                DistrictID,
                IMSID,
                ClusterID,
                Program,
                CertificateHolders,
                Responsible,
                FarmerID,
                FarmerName,
                Gender,
                HandPhone,
                Province,
                District,
                SubDistrict,
                Village,
                GroupID,
                FarmerGroup,
                StatusFinalAudit,
                FinalAuditRemarks,
                FirstYearOfCertification,
                YearOfCertification,
                CertificationYear,
                ICSDate,
                GardenNr,
                SurveyNr,
                Longitude,
                Latitude,
                StatusGardenAudit,
                GardenAuditRemarks,
                GardenAuditComment,
                PresentYearHarvest,
                PreviousYearHarvest,
                HaCertifiedCropArea,
                PolygonStatus,
                PIC,
                DateUpdated
           ) 
           SELECT 
                DATE(NOW()),
                d.CertHolderID,
                k.`DistrictID`,
                b.`IMSID`,
                q.ClusterID,
                CONCAT(
                     l.`ProgramName`,
                     ' (',
                     l.`ProgramYear`,
                     ')'
                ) AS Program,
                IF(
                     h.OrgType = 'trader',
                     i.`Company`,
                     IF(
                          h.OrgType = 'koperasi',
                          j.CoopName,
                          '-'
                     )
                ) AS CertificateHolders,
                IF(
                     h.OrgType = 'trader',
                     i.TraderName,
                     IF(
                          h.OrgType = 'koperasi',
                          j.CoopName,
                          '-'
                     )
                ) AS Responsible,
                m.`FarmerID`,
                m.`FarmerName`,
                m.`Gender`,
                m.`HandPhone`,
                m.`Province`,
                m.`District`,
                m.`SubDistrict`,
                m.`Village`,
                m.`CPGid` AS GroupID,
                m.`GroupName` AS FarmerGroup,
                m.`CertStatusAudit` AS StatusFinalAudit,
                m.`CertAuditRemark` AS FinalAuditRemarks,
                m.`CertFirstYear` AS FirstYearOfCertification,
                m.`YearOfCertification` AS YearOfCertification,
                m.`CertYear` AS CertificationYear,
                a.`CertICSDate` AS ICSDate,
                a.`CertGardenNr` AS GardenNr,
                a.`CertSurveyNr` AS SurveyNr,
                IF(
                     n.`Longitude` IS NULL 
                     OR n.`Longitude` = '0.000000',
                     o.Longitude,
                     n.Longitude
                ) AS Longitude,
                IF(
                     n.`Latitude` IS NULL 
                     OR n.`Latitude` = '0.000000',
                     o.Latitude,
                     n.Latitude
                ) AS Latitude,
                IF(
                     a.`CertStatusAudit` = 1,
                     'Comply',
                     IF(
                          a.`CertStatusAudit` = 2,
                          'Not Comply',
                          IF(
                               a.`CertStatusAudit` = 3,
                               'Comply with Recommendation',
                               'No Status'
                          )
                     )
                ) AS StatusGardenAudit,
                a.`CertAuditRemark` AS GardenAuditRemarks,
                a.`CertCommentAudit` AS GardenAuditComment,
                a.`CertNextHarvest` AS PresentYearHarvest,
                a.`CertHarvest` AS PreviousYearHarvest,
                a.`CertHectare` AS HaCertifiedCropArea,
                IF(
                     p.`FarmerID` IS NULL,
                     'Not Available',
                     'Available'
                ) AS PolygonStatus,
                IFNULL(a.`IMSEditor`, a.`IMSCreator`) AS PIC,
                n.DateUpdated 
           FROM
                `ktv_cocoa_certification_afl_garden` a 
                LEFT JOIN `ktv_cocoa_certification_afl_farmer` m 
                     ON m.`IMSID` = a.`IMSID` 
                     AND m.`FarmerID` = a.`FarmerID` 
                LEFT JOIN `ktv_cocoa_farmer_garden` n 
                     ON n.`FarmerID` = a.`FarmerID` 
                     AND n.`GardenNr` = a.`CertGardenNr` 
                     AND a.`CertSurveyNr` = n.`SurveyNr` 
                JOIN 
                     (SELECT 
                          FarmerID,
                          GardenNr,
                          MAX(Longitude) AS Longitude,
                          MAX(Latitude) AS Latitude 
                     FROM
                          ktv_cocoa_farmer_garden 
                     GROUP BY FarmerID,
                          GardenNr) o 
                     ON o.`FarmerID` = n.`FarmerID` 
                     AND o.`GardenNr` = n.`GardenNr` 
                LEFT JOIN 
                     (SELECT 
                          FarmerID,
                          GardenNr 
                     FROM
                          ktv_cocoa_farmer_garden_area 
                     GROUP BY FarmerID,
                          GardenNr) p 
                     ON p.`FarmerID` = n.`FarmerID` 
                     AND p.`GardenNr` = n.`GardenNr` 
                LEFT JOIN `ktv_ims` b 
                     ON b.`IMSID` = a.`IMSID` 
                LEFT JOIN `ktv_ims_master` c 
                     ON c.`IMSMasterID` = b.`IMSMasterID` 
                LEFT JOIN `ktv_certification_holders` d 
                     ON d.`CertHolderID` = c.`CertHolderID` 
                LEFT JOIN `ktv_first_buyer` e 
                     ON e.`FirstBuyerID` = b.`FirstBuyerID` 
                LEFT JOIN `ktv_program_partner` f 
                     ON f.`PartnerID` = e.`FirstBuyerPartnerID` 
                LEFT JOIN `ktv_cocoa_farmer` g 
                     ON g.`FarmerID` = a.`FarmerID` 
                LEFT JOIN `ktv_supplychain_org` h 
                     ON h.`SupplychainID` = d.`SupplychainID` 
                LEFT JOIN `ktv_traders` i 
                     ON i.`TraderID` = h.`OrgID` 
                     AND h.`OrgType` = 'trader' 
                LEFT JOIN `ktv_cooperatives` j 
                     ON j.`CoopID` = h.`OrgID` 
                     AND h.`OrgType` = 'koperasi' 
                LEFT JOIN ktv_village vil ON vil.VillageID = g.VillageID
                LEFT JOIN ktv_subdistrict subd ON subd.SubDistrictID = vil.SubDistrictID
                LEFT JOIN `ktv_district` k ON k.`DistrictID` = subd.DistrictID 
                LEFT JOIN `ktv_first_buyer_program` l 
                     ON l.`ProgID` = b.`ProgID` 
               LEFT JOIN ktv_cocoa_certification_pre_afl q ON q.IMSID = a.IMSID AND q.FarmerID = a.FarmerID 
           WHERE b.`IMSID` IN ( 207 ) 
                AND n.`SurveyNr` = 16
                AND b.`StatusCode` = 'active' 
                AND c.`StatusCode` = 'active' 
                AND a.CertStatusAudit IN ('1', '2', '3') 
           ORDER BY d.`CertHolderID`,
                a.`District`,
                a.`CPGid`";
		$query = $this->db->query($sql); //detailICS1
		if ($query) {
			$results['detailICS1'] = "detailICS1 updated.";
		} else {
			$results['detailICS1'] .= "||detailICS1 Failed to update";
		}
		return $results;
	}

    function insert_detail_ICS1Hectare()
    {
        $sql = "DELETE FROM ktv_jbcocoa_summary_ics_one_hectare WHERE ProgID=7 AND DateGenerated = DATE(NOW())";
        $query = $this->db->query($sql);
        $sql = "INSERT INTO ktv_jbcocoa_summary_ics_one_hectare (
                    `DateGenerated`,
                    `CertHolderID`,
                    `DistrictID`,
                    `IMSID`,
                    `ProgID`,
                    `Program`,
                    `CertificateHolders`,
                    `Responsible`,
                    `FarmerID`,
                    `FarmerName`,
                    `Gender`,
                    `Birthdate`,
                    `CPGid`,
                    `GroupName`,
                    `VillageID`,
                    `Village`,
                    `SubDistrict`,
                    `District`,
                    `Province`,
                    `CertGardenNr`,
                    `CertHectare`,
                    `ClusterID`,
                    `ObjType`
                )
                SELECT
                    DATE(NOW()) AS DateGenerated,
                    j.CertHolderID AS 'CertHolderID',
                    SUBSTR(a.FarmerID,1,4) AS DistrictID,
                    b.IMSID,
                    k.ProgID,
                    CONCAT(k.ProgramName,' (',k.ProgramYear,')') AS Program,
                    j.CertHolderOrgName AS CertificateHolders,
                    j.CertHolderResponsible AS Responsible,
                    b.DestObjID AS FarmerID,
                    b.Name AS FarmerName,
                    b.Gender,
                    c.Birthdate,
                    c.CPGid,
                    d.GroupName,
                    c.VillageID,
                    b.Village,
                    b.SubDistrict,
                    b.District,
                    b.Province,
                    a.GardenNr AS CertGardenNr,
                    IF(IFNULL(e.GardenHaUnCertified,0) < IFNULL(f.GardenHaUnCertified,0), f.GardenHaUnCertified, e.GardenHaUnCertified) AS CertHectare,
                    g.ClusterID,
                    IF(b.ObjType IN('Applicant','Existing Farmer'),'Y1',IF(b.ObjType IN('Existing Certified Farmer'),'Y2','-')) AS ObjType
                FROM
                    ktv_cocoa_farmer_garden a
                    JOIN ktv_ims_soc_sel b ON b.DestObjID = a.FarmerID 
                    LEFT JOIN ktv_cocoa_farmer c ON c.FarmerID=a.FarmerID
                    LEFT JOIN ktv_cpg d ON d.CPGid=c.CPGid
                    LEFT JOIN (
                    SELECT m.FarmerID,m.GardenNr,m.SurveyNr, m.GardenHaUnCertified,m.DateCollection FROM ktv_cocoa_farmer_garden m
                    JOIN (SELECT FarmerID,MAX(SurveyNr) AS mxsur,GardenNr FROM ktv_cocoa_farmer_garden WHERE SurveyNr!=16 GROUP BY FarmerID,GardenNr) n ON n.FarmerID=m.FarmerID AND n.GardenNr=m.GardenNr AND n.mxsur=m.SurveyNr) e ON e.FarmerID=a.FarmerID AND e.GardenNr=a.GardenNr
                    LEFT JOIN (
                    SELECT m.FarmerID,m.GardenNr,m.SurveyNr, m.GardenHaUnCertified,m.DateCollection FROM ktv_cocoa_farmer_garden m
                    JOIN (SELECT FarmerID,MAX(SurveyNr) AS mxsur,GardenNr FROM ktv_cocoa_farmer_garden GROUP BY FarmerID,GardenNr) n ON n.FarmerID=m.FarmerID AND n.GardenNr=m.GardenNr AND n.mxsur=m.SurveyNr) f ON f.FarmerID=a.FarmerID AND f.GardenNr=a.GardenNr
                    JOIN ktv_ims_cluster AS g ON g.ClusterID=b.ClusterID
                    JOIN ktv_ims h ON h.IMSID=b.IMSID
                    LEFT JOIN ktv_ims_master i ON i.IMSMasterID=h.IMSMasterID
                    LEFT JOIN ktv_certification_holders j ON j.CertHolderID=i.CertHolderID
                    LEFT JOIN ktv_first_buyer_program k ON k.ProgID=h.ProgID
                WHERE
                    b.IMSID = 207 
                    AND SelectionStatus =1
                    GROUP BY a.FarmerID,a.GardenNr";
        $query = $this->db->query($sql); //detailICS1
        if ($query) {
            $results['detailICS1'] = "detailICS1Hectare updated.";
        } else {
            $results['detailICS1'] .= "||detailICS1Hectare Failed to update";
        }
        return $results;
    }
    
    function insert_detail_ICS1Hectare_Wave3() {
        $sql = "DELETE FROM ktv_jbcocoa_summary_ics_one_hectare WHERE ProgID IN (12) AND DateGenerated = DATE(NOW());";
        $query = $this->db->query($sql);
        
        $sql = "INSERT INTO ktv_jbcocoa_summary_ics_one_hectare (
`DateGenerated`,
`CertHolderID`,
`DistrictID`,
`IMSID`,
`ProgID`,
`Program`,
`CertificateHolders`,
`Responsible`,
`FarmerID`,
`FarmerName`,
`Gender`,
`Birthdate`,
`CPGid`,
`GroupName`,
`VillageID`,
`Village`,
`SubDistrict`,
`District`,
`Province`,
`CertGardenNr`,
`CertHectare`,
`ClusterID`,
`ObjType`
) 
SELECT
DATE( NOW( ) ) AS DateGenerated,
j.CertHolderID AS 'CertHolderID',
SUBSTR( a.FarmerID, 1, 4 ) AS DistrictID,
b.IMSID,
k.ProgID,
CONCAT( k.ProgramName, ' (', k.ProgramYear, ')' ) AS Program,
j.CertHolderOrgName AS CertificateHolders,
j.CertHolderResponsible AS Responsible,
b.DestObjID AS FarmerID,
b.NAME AS FarmerName,
b.Gender,
c.Birthdate,
c.CPGid,
d.GroupName,
c.VillageID,
b.Village,
b.SubDistrict,
b.District,
b.Province,
a.GardenNr AS CertGardenNr,
IF
	( IFNULL( e.GardenHaUnCertified, 0 ) < IFNULL( f.GardenHaUnCertified, 0 ), f.GardenHaUnCertified, e.GardenHaUnCertified ) AS CertHectare,
	g.ClusterID,
IF
	(
	b.ObjType IN ( 'Applicant', 'Existing Farmer' ),
	'Y1',
IF
	( b.ObjType IN ( 'Existing Certified Farmer' ), 'Y2', '-' ) 
	) AS ObjType 
FROM
	ktv_cocoa_farmer_garden a
	JOIN ktv_ims_soc_sel b ON b.DestObjID = a.FarmerID
	JOIN ktv_cocoa_certification_afl_farmer afl ON afl.FarmerID=b.DestObjID AND afl.IMSID=b.IMSID
	LEFT JOIN ktv_cocoa_farmer c ON c.FarmerID = a.FarmerID
	LEFT JOIN ktv_cpg d ON d.CPGid = c.CPGid
	LEFT JOIN (
SELECT
	m.FarmerID,
	m.GardenNr,
	m.SurveyNr,
	m.GardenHaUnCertified,
	m.DateCollection 
FROM
	ktv_cocoa_farmer_garden m
	JOIN ( SELECT FarmerID, MAX( SurveyNr ) AS mxsur, GardenNr FROM ktv_cocoa_farmer_garden WHERE SurveyNr != 16 GROUP BY FarmerID, GardenNr ) n ON n.FarmerID = m.FarmerID 
	AND n.GardenNr = m.GardenNr 
	AND n.mxsur = m.SurveyNr 
	) e ON e.FarmerID = a.FarmerID 
	AND e.GardenNr = a.GardenNr
	LEFT JOIN (
SELECT
	m.FarmerID,
	m.GardenNr,
	m.SurveyNr,
	m.GardenHaUnCertified,
	m.DateCollection 
FROM
	ktv_cocoa_farmer_garden m
	JOIN ( SELECT FarmerID, MAX( SurveyNr ) AS mxsur, GardenNr FROM ktv_cocoa_farmer_garden GROUP BY FarmerID, GardenNr ) n ON n.FarmerID = m.FarmerID 
	AND n.GardenNr = m.GardenNr 
	AND n.mxsur = m.SurveyNr 
	) f ON f.FarmerID = a.FarmerID 
	AND f.GardenNr = a.GardenNr
	JOIN ktv_ims_cluster AS g ON g.ClusterID = b.ClusterID
	JOIN ktv_ims h ON h.IMSID = b.IMSID
	LEFT JOIN ktv_ims_master i ON i.IMSMasterID = h.IMSMasterID
	LEFT JOIN ktv_certification_holders j ON j.CertHolderID = i.CertHolderID
	LEFT JOIN ktv_first_buyer_program k ON k.ProgID = h.ProgID 
WHERE
	b.IMSID = 252
	AND SelectionStatus = 1 
	AND afl.CertStatusAudit IN('Comply','Not Comply')
GROUP BY
	a.FarmerID,
	a.GardenNr;";
        $query = $this->db->query($sql); // ICS Survey Wave III
        if ($query) {
            $results['ICS1HectareWave3'] = "ICS1HectareWave3 updated.";
        } else {
            $results['ICS1HectareWave3'] .= "||ICS1HectareWave3 Failed to update";
        }
        return $results;
    }
    
    function insert_detail_ICS1Hectare_Wave4() {
        $sql = "DELETE FROM ktv_jbcocoa_summary_ics_one_hectare WHERE ProgID=14 AND DateGenerated = DATE(NOW());";
        $query = $this->db->query($sql);
        
        $sql = "INSERT INTO ktv_jbcocoa_summary_ics_one_hectare (
`DateGenerated`,
`CertHolderID`,
`DistrictID`,
`IMSID`,
`ProgID`,
`Program`,
`CertificateHolders`,
`Responsible`,
`FarmerID`,
`FarmerName`,
`Gender`,
`Birthdate`,
`CPGid`,
`GroupName`,
`VillageID`,
`Village`,
`SubDistrict`,
`District`,
`Province`,
`CertGardenNr`,
`CertHectare`,
`ClusterID`,
`ObjType`,
`CertStatusAudit`
)
SELECT
DATE( NOW( ) ) AS DateGenerated,
j.CertHolderID AS 'CertHolderID',
SUBSTR( a.FarmerID, 1, 4 ) AS DistrictID,
b.IMSID,
k.ProgID,
CONCAT( k.ProgramName, ' (', k.ProgramYear, ')' ) AS Program,
j.CertHolderOrgName AS CertificateHolders,
j.CertHolderResponsible AS Responsible,
b.DestObjID AS FarmerID,
b.NAME AS FarmerName,
b.Gender,
c.Birthdate,
c.CPGid,
d.GroupName,
c.VillageID,
b.Village,
b.SubDistrict,
b.District,
b.Province,
a.GardenNr AS CertGardenNr,
aflg.CertHectare AS CertHectare,
g.ClusterID,
/*IF
	(
	b.ObjType IN ( 'Applicant', 'Existing Farmer' ),
	'Y1',
IF
	( b.ObjType IN ( 'Existing Certified Farmer' ), 'Y2', '-' ) 
	) AS ObjType,*/
	b.ObjType,
	IF(aflg.CertStatusAudit IN(1,3),'Comply',IF(aflg.CertStatusAudit=2,'Not Comply','-')) AS CertStatusAudit
	
FROM
	ktv_cocoa_farmer_garden a
	JOIN ktv_ims_soc_sel b ON b.DestObjID = a.FarmerID
	JOIN ktv_cocoa_certification_afl_farmer afl ON afl.FarmerID = b.DestObjID AND afl.CertStatusAudit IN('Comply','Not Comply')
	INNER JOIN ktv_cocoa_certification_afl_garden aflg ON aflg.FarmerID=a.FarmerID AND aflg.CertGardenNr=a.GardenNr AND aflg.CertSurveyNr=a.SurveyNr
	AND afl.IMSID = b.IMSID
	LEFT JOIN ktv_cocoa_farmer c ON c.FarmerID = a.FarmerID
	LEFT JOIN ktv_cpg d ON d.CPGid = c.CPGid
	JOIN ktv_ims_cluster AS g ON g.ClusterID = b.ClusterID
	JOIN ktv_ims h ON h.IMSID = b.IMSID
	LEFT JOIN ktv_ims_master i ON i.IMSMasterID = h.IMSMasterID
	LEFT JOIN ktv_certification_holders j ON j.CertHolderID = i.CertHolderID
	LEFT JOIN ktv_first_buyer_program k ON k.ProgID = h.ProgID 
WHERE
	b.IMSID = 278 
	AND SelectionStatus = 1 
	AND aflg.CertStatusAudit IN ( 1, 2, 3 )
GROUP BY
	a.FarmerID,
	a.GardenNr;";
        $query = $this->db->query($sql); // ICS Survey Wave III
        if ($query) {
            $results['ICS1HectareWave4'] = "ICS1HectareWave4 updated.";
        } else {
            $results['ICS1HectareWave4'] .= "||ICS1HectareWave4 Failed to update";
        }
        return $results;        
    }

    function insert_detail_ICS2()
	{
		$sql = "DELETE FROM ktv_jbcocoa_summary_ics_two WHERE DateGenerated = DATE(NOW()) AND IMSID=207;";
		$query = $this->db->query($sql);
		$sql = "INSERT INTO ktv_jbcocoa_summary_ics_two (
                DateGenerated,
                CertHolderID,
                DistrictID,
                IMSID,
                ClusterID,
                Program,
                CertificateHolders,
                Responsible,
                FarmerID,
                FarmerName,
                Gender,
                HandPhone,
                Province,
                District,
                SubDistrict,
                Village,
                GroupID,
                FarmerGroup,
                StatusFinalAudit,
                FinalAuditRemarks,
                FirstYearOfCertification,
                YearOfCertification,
                CertificationYear,
                ICSDate,
                GardenNr,
                SurveyNr,
                Longitude,
                Latitude,
                StatusGardenAudit,
                GardenAuditRemarks,
                GardenAuditComment,
                PresentYearHarvest,
                PreviousYearHarvest,
                HaCertifiedCropArea,
                PolygonStatus,
                VerificationStatus,
                PIC,
                DateUpdated
           ) 
           SELECT 
                DATE(NOW()),
                d.CertHolderID,
                k.DistrictID,
                b.`IMSID`,
                q.ClusterID,
                CONCAT(
                     l.`ProgramName`,
                     ' (',
                     l.`ProgramYear`,
                     ')'
                ) AS Program,
                IF(
                     h.OrgType = 'trader',
                     i.`Company`,
                     IF(
                          h.OrgType = 'koperasi',
                          j.CoopName,
                          '-'
                     )
                ) AS CertificateHolders,
                IF(
                     h.OrgType = 'trader',
                     i.TraderName,
                     IF(
                          h.OrgType = 'koperasi',
                          j.CoopName,
                          '-'
                     )
                ) AS Responsible,
                m.`FarmerID`,
                m.`FarmerName`,
                m.`Gender`,
                m.`HandPhone`,
                m.`Province`,
                m.`District`,
                m.`SubDistrict`,
                m.`Village`,
                m.`CPGid` AS GroupID,
                m.`GroupName` AS FarmerGroup,
                m.`CertStatusAudit` AS StatusFinalAudit,
                m.`CertAuditRemark` AS FinalAuditRemarks,
                m.`CertFirstYear` AS FirstYearOfCertification,
                m.`YearOfCertification` AS YearOfCertification,
                m.`CertYear` AS CertificationYear,
                a.`CertICSDate` AS ICSDate,
                a.`CertGardenNr` AS GardenNr,
                a.`CertSurveyNr` AS SurveyNr,
                IF(
                     n.`Longitude` IS NULL 
                     OR n.`Longitude` = '0.000000',
                     o.Longitude,
                     n.Longitude
                ) AS Longitude,
                IF(
                     n.`Latitude` IS NULL 
                     OR n.`Latitude` = '0.000000',
                     o.Latitude,
                     n.Latitude
                ) AS Latitude,
                IF(
                     a.`CertStatusAudit` = 1,
                     'Comply',
                     IF(
                          a.`CertStatusAudit` = 2,
                          'Not Comply',
                          IF(
                               a.`CertStatusAudit` = 3,
                               'Comply with Recommendation',
                               'No Status'
                          )
                     )
                ) AS StatusGardenAudit,
                a.`CertAuditRemark` AS GardenAuditRemarks,
                a.`CertCommentAudit` AS GardenAuditComment,
                a.`CertNextHarvest` AS PresentYearHarvest,
                a.`CertHarvest` AS PreviousYearHarvest,
                a.`CertHectare` AS HaCertifiedCropArea,
                IF(
                     p.`FarmerID` IS NULL,
                     'Not Available',
                     'Available'
                ) AS PolygonStatus,
                p.Status AS VerificationStatus,
                IFNULL(a.`IMSEditor`, a.`IMSCreator`) AS PIC,
                n.DateUpdated 
           FROM
                `ktv_cocoa_certification_afl_garden` a 
                LEFT JOIN `ktv_cocoa_certification_afl_farmer` m 
                     ON m.`IMSID` = a.`IMSID` 
                     AND m.`FarmerID` = a.`FarmerID` 
                JOIN `ktv_cocoa_farmer_garden` n 
                     ON n.`FarmerID` = a.`FarmerID` 
                     AND n.`GardenNr` = a.`CertGardenNr` 
                     AND a.`CertSurveyNr` = n.`SurveyNr` 
                Left JOIN 
                     (SELECT 
                          FarmerID,
                          GardenNr,
                          MAX(Longitude) AS Longitude,
                          MAX(Latitude) AS Latitude 
                     FROM
                          ktv_cocoa_farmer_garden 
                     GROUP BY FarmerID,
                          GardenNr) o 
                     ON o.`FarmerID` = n.`FarmerID` 
                     AND o.`GardenNr` = n.`GardenNr` 
                JOIN 
                     (SELECT 
                          FarmerID,
                          GardenNr,
                          STATUS 
                     FROM
                          ktv_cocoa_farmer_garden_area 
                     GROUP BY FarmerID,
                          GardenNr) p 
                     ON p.`FarmerID` = n.`FarmerID` 
                     AND p.`GardenNr` = n.`GardenNr` 
                LEFT JOIN `ktv_ims` b 
                     ON b.`IMSID` = a.`IMSID` 
                LEFT JOIN `ktv_ims_master` c 
                     ON c.`IMSMasterID` = b.`IMSMasterID` 
                LEFT JOIN `ktv_certification_holders` d 
                     ON d.`CertHolderID` = c.`CertHolderID` 
                LEFT JOIN `ktv_first_buyer` e 
                     ON e.`FirstBuyerID` = b.`FirstBuyerID` 
                LEFT JOIN `ktv_program_partner` f 
                     ON f.`PartnerID` = e.`FirstBuyerPartnerID` 
                LEFT JOIN `ktv_cocoa_farmer` g 
                     ON g.`FarmerID` = a.`FarmerID` 
                LEFT JOIN `ktv_supplychain_org` h 
                     ON h.`SupplychainID` = d.`SupplychainID` 
                LEFT JOIN `ktv_traders` i 
                     ON i.`TraderID` = h.`OrgID` 
                     AND h.`OrgType` = 'trader' 
                LEFT JOIN `ktv_cooperatives` j 
                     ON j.`CoopID` = h.`OrgID` 
                     AND h.`OrgType` = 'koperasi' 
                LEFT JOIN ktv_village vil ON vil.VillageID = g.VillageID
                LEFT JOIN ktv_subdistrict subd ON subd.SubDistrictID = vil.SubDistrictID
                LEFT JOIN `ktv_district` k ON k.`DistrictID` = subd.DistrictID 
                LEFT JOIN `ktv_first_buyer_program` l 
                     ON l.`ProgID` = b.`ProgID` 
                LEFT JOIN ktv_cocoa_certification_pre_afl q ON q.IMSID = a.IMSID AND q.FarmerID = a.FarmerID 
           WHERE b.`IMSID` IN (207) 
                AND n.`SurveyNr` = 16 
                AND b.`StatusCode` = 'active' 
                AND c.`StatusCode` = 'active' 
                AND a.CertStatusAudit IN ('1', '2', '3') 
                AND p.`FarmerID` IS NOT NULL 
           GROUP BY n.`FarmerID`,
                n.`GardenNr`,
                n.`SurveyNr` 
           ORDER BY d.`CertHolderID`,
                a.`District`,
                a.`CPGid`";
		$query = $this->db->query($sql); //detailICS2
		if ($query) {
			$results['detailICS2'] = "detailICS2 updated.";
		} else {
			$results['detailICS2'] .= "||detailICS2 Failed to update";
		}
		return $results;
	}
        
    function insert_detail_ICS2_Wave3(){
        $sql = "DELETE FROM ktv_jbcocoa_summary_ics_two WHERE DateGenerated = DATE(NOW()) AND IMSID IN (252,278);";
        $query = $this->db->query($sql);
        
        $sql = "INSERT INTO ktv_jbcocoa_summary_ics_two (
DateGenerated,
CertHolderID,
DistrictID,
IMSID,
Program,
CertificateHolders,
Responsible,
FarmerID,
FarmerName,
Gender,
HandPhone,
Province,
District,
SubDistrict,
Village,
GroupID,
FarmerGroup,
StatusFinalAudit,
FinalAuditRemarks,
FirstYearOfCertification,
YearOfCertification,
CertificationYear,
ICSDate,
GardenNr,
SurveyNr,
Longitude,
Latitude,
StatusGardenAudit,
GardenAuditRemarks,
GardenAuditComment,
PresentYearHarvest,
PreviousYearHarvest,
HaCertifiedCropArea,
PolygonStatus,
VerificationStatus,
PIC,
DateUpdated,
ClusterID
)
SELECT
	DATE(NOW()),
	d.CertHolderID,
	k.DistrictID,
	b.`IMSID`,
	CONCAT( l.`ProgramName`, ' (', l.`ProgramYear`, ')' ) AS Program,
IF
	( h.OrgType = 'trader', i.`Company`, IF ( h.OrgType = 'koperasi', j.CoopName, '-' ) ) AS CertificateHolders,
IF
	( h.OrgType = 'trader', i.TraderName, IF ( h.OrgType = 'koperasi', j.CoopName, '-' ) ) AS Responsible,
	m.`FarmerID`,
	m.`FarmerName`,
	m.`Gender`,
	m.`HandPhone`,
	m.`Province`,
	m.`District`,
	m.`SubDistrict`,
	m.`Village`,
	m.`CPGid` AS GroupID,
	m.`GroupName` AS FarmerGroup,
	m.`CertStatusAudit` AS StatusFinalAudit,
	m.`CertAuditRemark` AS FinalAuditRemarks,
	m.`CertFirstYear` AS FirstYearOfCertification,
	m.`YearOfCertification` AS YearOfCertification,
	m.`CertYear` AS CertificationYear,
	a.`CertICSDate` AS ICSDate,
	a.`CertGardenNr` AS GardenNr,
	a.`CertSurveyNr` AS SurveyNr,
IF
	( n.`Longitude` IS NULL OR n.`Longitude` = '0.000000', o.Longitude, n.Longitude ) AS Longitude,
IF
	( n.`Latitude` IS NULL OR n.`Latitude` = '0.000000', o.Latitude, n.Latitude ) AS Latitude,
IF
	(
	a.`CertStatusAudit` = 1,
	'Comply',
IF
	( a.`CertStatusAudit` = 2, 'Not Comply', IF ( a.`CertStatusAudit` = 3, 'Comply with Recommendation', 'No Status' ) ) 
	) AS StatusGardenAudit,
	a.`CertAuditRemark` AS GardenAuditRemarks,
	a.`CertCommentAudit` AS GardenAuditComment,
	a.`CertNextHarvest` AS PresentYearHarvest,
	a.`CertHarvest` AS PreviousYearHarvest,
	a.`CertHectare` AS HaCertifiedCropArea,
IF
	( p.`FarmerID` IS NULL, 'Not Available', 'Available' ) AS PolygonStatus,
	p.STATUS AS VerificationStatus,
	IFNULL( a.`IMSEditor`, a.`IMSCreator` ) AS PIC,
	n.DateUpdated,
	q.ClusterID
FROM
	`ktv_cocoa_certification_afl_garden` a
	LEFT JOIN `ktv_cocoa_certification_afl_farmer` m ON m.`IMSID` = a.`IMSID` 
	AND m.`FarmerID` = a.`FarmerID`
	JOIN `ktv_cocoa_farmer_garden` n ON n.`FarmerID` = a.`FarmerID` 
	AND n.`GardenNr` = a.`CertGardenNr` 
	AND a.`CertSurveyNr` = n.`SurveyNr`
	LEFT JOIN ( SELECT FarmerID, GardenNr, MAX( Longitude ) AS Longitude, MAX( Latitude ) AS Latitude FROM ktv_cocoa_farmer_garden GROUP BY FarmerID, GardenNr ) o ON o.`FarmerID` = n.`FarmerID` 
	AND o.`GardenNr` = n.`GardenNr`
	JOIN ( SELECT m.FarmerID, m.GardenNr, m.Status, COUNT(m.FarmerID) AS jml
                FROM
                (
                    SELECT a.FarmerID, a.`GardenNr`, a.Status FROM ktv_cocoa_farmer_garden_area a
                    JOIN
                    (SELECT a.FarmerID, a.`GardenNr` FROM ktv_cocoa_farmer_garden_area a
                    LEFT JOIN ktv_cocoa_certification_afl_garden b ON b.`FarmerID`=a.`FarmerID` AND b.`CertGardenNr`=a.`GardenNr`
                    WHERE
                    b.IMSID IN (252,278) AND DATE(a.`DateCreated`)>='2019-11-01'
                    GROUP BY a.`FarmerID`, a.`GardenNr`) b ON b.`FarmerID`=a.`FarmerID` AND b.`GardenNr`=a.`GardenNr`
                    GROUP BY a.FarmerID, a.`GardenNr`, DATE(a.`DateCreated`)
                ) m
                GROUP BY m.`FarmerID`, m.`GardenNr`
                HAVING jml=1 
	) p ON p.`FarmerID` = n.`FarmerID` 
	AND p.`GardenNr` = n.`GardenNr`
	LEFT JOIN `ktv_ims` b ON b.`IMSID` = a.`IMSID`
	LEFT JOIN `ktv_ims_master` c ON c.`IMSMasterID` = b.`IMSMasterID`
	LEFT JOIN `ktv_certification_holders` d ON d.`CertHolderID` = c.`CertHolderID`
	LEFT JOIN `ktv_first_buyer` e ON e.`FirstBuyerID` = b.`FirstBuyerID`
	LEFT JOIN `ktv_program_partner` f ON f.`PartnerID` = e.`FirstBuyerPartnerID`
	LEFT JOIN `ktv_cocoa_farmer` g ON g.`FarmerID` = a.`FarmerID`
	LEFT JOIN `ktv_supplychain_org` h ON h.`SupplychainID` = d.`SupplychainID`
	LEFT JOIN `ktv_traders` i ON i.`TraderID` = h.`OrgID` 
	AND h.`OrgType` = 'trader'
	LEFT JOIN `ktv_cooperatives` j ON j.`CoopID` = h.`OrgID` 
	AND h.`OrgType` = 'koperasi'
        LEFT JOIN ktv_village vil ON vil.VillageID = g.VillageID
        LEFT JOIN ktv_subdistrict subd ON subd.SubDistrictID = vil.SubDistrictID
        LEFT JOIN `ktv_district` k ON k.`DistrictID` = subd.DistrictID 
	LEFT JOIN `ktv_first_buyer_program` l ON l.`ProgID` = b.`ProgID`
	LEFT JOIN ktv_cocoa_certification_pre_afl q ON q.IMSID = a.IMSID 
	AND q.FarmerID = a.FarmerID 
WHERE
	b.IMSID IN (252,278)
	AND n.`SurveyNr` = 18 
	AND b.`StatusCode` = 'active' 
	AND c.`StatusCode` = 'active' 
	AND a.CertStatusAudit IN ( '1', '2', '3' ) 
	AND p.`FarmerID` IS NOT NULL 
GROUP BY
	n.`FarmerID`,
	n.`GardenNr`,
	n.`SurveyNr` 
ORDER BY
	d.`CertHolderID`,
	a.`District`,
	a.`CPGid`;";
        $query = $this->db->query($sql); // Polygon Wave III
        
        if ($query) {
            $results['detailICS2Wave3'] = "detailICS2Wave3 updated.";
        } else {
            $results['detailICS2Wave3'] .= "||detailICS2Wave3 Failed to update";
        }
    }
	
	function insert_detail_ICS0()
	{
		$sql = "DELETE FROM ktv_jbcocoa_summary_ics_zero WHERE DateGenerated = DATE(NOW()) AND IMSID=207";
		$query = $this->db->query($sql);
		$sql = "INSERT INTO ktv_jbcocoa_summary_ics_zero (
                DateGenerated,
                CertHolderID,
                DistrictID,
                IMSID,
                ClusterID,
                Program,
                CertificateHolders,
                Responsible,
                FarmerID,
                StatusFarmer,
                FarmerName,
                Gender,
                HandPhone,
                Province,
                District,
                SubDistrict,
                Village,
                GroupID,
                FarmerGroup,
                StatusAudit,
                AuditRemarks,
                FirstYearOfCertification,
                YearOfCertification,
                CertificationYear,
                ICSDate,
                PresentYearHarvest,
                PreviousYearHarvest,
                HaCertifiedCropArea,
                NrOfCertifiedPlots,
                HaTotalFarmArea,
                LastYearDelivery,
                Last2ndYearDelivery,
                Last3rdYearDelivery
           ) 
           SELECT 
                DATE(NOW()),
                d.CertHolderID,
                k.DistrictID,
                b.`IMSID`,
                q.ClusterID,
                CONCAT(
                     l.`ProgramName`,
                     ' (',
                     l.`ProgramYear`,
                     ')'
                ) AS Program,
                IF(
                     h.OrgType = 'trader',
                     i.`Company`,
                     IF(
                          h.OrgType = 'koperasi',
                          j.CoopName,
                          '-'
                     )
                ) AS CertificateHolders,
                IF(
                     h.OrgType = 'trader',
                     i.TraderName,
                     IF(
                          h.OrgType = 'koperasi',
                          j.CoopName,
                          '-'
                     )
                ) AS Responsible,
                g.`FarmerID`,
                g.`StatusFarmer`,
                g.`FarmerName`,
                a.`Gender`,
                a.`HandPhone`,
                a.`Province`,
                a.`District`,
                a.`SubDistrict`,
                a.`Village`,
                a.`CPGid` AS GroupID,
                a.`GroupName` AS FarmerGroup,
                a.`CertStatusAudit` AS StatusAudit,
                a.`CertAuditRemark` AS AuditRemarks,
                a.`CertFirstYear` AS FirstYearOfCertification,
                a.`YearOfCertification` AS YearOfCertification,
                a.`CertYear` AS CertificationYear,
                a.`CertICSDate` AS ICSDate,
                a.`CertNextHarvest` AS PresentYearHarvest,
                a.`CertHarvest` AS PreviousYearHarvest,
                a.`CertHectare` AS HaCertifiedCropArea,
                a.`CertFarmNr` AS NrOfCertifiedPlots,
                a.`CertTotalHectare` AS HaTotalFarmArea,
                a.`SalesLastYear` AS LastYearDelivery,
                a.`SalesLast2Years` AS Last2ndYearDelivery,
                a.`SalesLast3Years` AS Last3rdYearDelivery 
           FROM
                `ktv_cocoa_certification_afl_farmer` a 
                LEFT JOIN `ktv_ims` b 
                     ON b.`IMSID` = a.`IMSID` 
                LEFT JOIN `ktv_ims_master` c 
                     ON c.`IMSMasterID` = b.`IMSMasterID` 
                LEFT JOIN `ktv_certification_holders` d 
                     ON d.`CertHolderID` = c.`CertHolderID` 
                LEFT JOIN `ktv_first_buyer` e 
                     ON e.`FirstBuyerID` = b.`FirstBuyerID` 
                LEFT JOIN `ktv_program_partner` f 
                     ON f.`PartnerID` = e.`FirstBuyerPartnerID` 
                LEFT JOIN `ktv_cocoa_farmer` g 
                     ON g.`FarmerID` = a.`FarmerID` 
                LEFT JOIN `ktv_supplychain_org` h 
                     ON h.`SupplychainID` = d.`SupplychainID` 
                LEFT JOIN `ktv_traders` i 
                     ON i.`TraderID` = h.`OrgID` 
                     AND h.`OrgType` = 'trader' 
                LEFT JOIN `ktv_cooperatives` j 
                     ON j.`CoopID` = h.`OrgID` 
                     AND h.`OrgType` = 'koperasi' 
                LEFT JOIN ktv_village vil ON vil.VillageID = g.VillageID
                LEFT JOIN ktv_subdistrict subd ON subd.SubDistrictID = vil.SubDistrictID
                LEFT JOIN `ktv_district` k ON k.`DistrictID` = subd.DistrictID 
                LEFT JOIN `ktv_first_buyer_program` l 
                     ON l.`ProgID` = b.`ProgID` 
                LEFT JOIN ktv_cocoa_certification_pre_afl q ON q.IMSID = a.IMSID AND q.FarmerID = a.FarmerID 
           WHERE b.`IMSID` IN (207) 
                AND b.`StatusCode` = 'active' 
                AND c.`StatusCode` = 'active' 
                AND a.CertStatusAudit IN ('Comply', 'Not Comply') 
                AND g.`StatusCode` = 'active' 
           GROUP BY g.FarmerID 
           ORDER BY d.`CertHolderID`,
                a.`District`,
                a.`CPGid`";
		$query = $this->db->query($sql); //detailICS0
		if ($query) {
			$results['detailICS0'] = "detailICS0 updated.";
		} else {
			$results['detailICS0'] .= "||detailICS0 Failed to update";
		}
		return $results;
	}
        
    function insert_detail_IMSAFL1_Wave3() {
        $sql = "DELETE FROM ktv_jbcocoa_summary_approved_farmer_list WHERE DateGenerated = DATE(NOW()) AND IMSID IN (252,278);";
        $query = $this->db->query($sql);
        
        $sql = "INSERT INTO ktv_jbcocoa_summary_approved_farmer_list (
DateGenerated,
CertHolderID,
DistrictID,
IMSID,
ProgID,
Program,
CertificateHolders,
Responsible,
FarmerID,
FarmerName,
Gender,
HandPhone,
Province,
District,
SubDistrict,
Village,
GroupID,
FarmerGroup,
StatusAudit,
AuditRemarks,
FirstYearOfCertification,
YearOfCertification,
CertificationYear,
ICSDate,
PresentYearHarvest,
PreviousYearHarvest,
HaCertifiedCropArea,
NrOfCertifiedPlots,
HaTotalFarmArea,
LastYearDelivery,
Last2ndYearDelivery,
Last3rdYearDelivery,
ClusterID
) 
SELECT
DATE(NOW()),
d.CertHolderID,
k.DistrictID,
b.`IMSID`,
b.`ProgID`,
CONCAT( l.`ProgramName`, ' (', l.`ProgramYear`, ')' ) AS Program,
IF
	( h.OrgType = 'trader', i.`Company`, IF ( h.OrgType = 'koperasi', j.CoopName, '-' ) ) AS CertificateHolders,
IF
	( h.OrgType = 'trader', i.TraderName, IF ( h.OrgType = 'koperasi', j.CoopName, '-' ) ) AS Responsible,
	g.`FarmerID`,
	g.`FarmerName`,
	a.`Gender`,
	a.`HandPhone`,
	a.`Province`,
	a.`District`,
	a.`SubDistrict`,
	a.`Village`,
	a.`CPGid` AS GroupID,
	a.`GroupName` AS FarmerGroup,
	a.`CertStatusAudit` AS StatusAudit,
	a.`CertAuditRemark` AS AuditRemarks,
	a.`CertFirstYear` AS FirstYearOfCertification,
	a.`YearOfCertification` AS YearOfCertification,
	a.`CertYear` AS CertificationYear,
	a.`CertICSDate` AS ICSDate,
	a.`CertNextHarvest` AS PresentYearHarvest,
	a.`CertHarvest` AS PreviousYearHarvest,
	a.`CertHectare` AS HaCertifiedCropArea,
	a.`CertFarmNr` AS NrOfCertifiedPlots,
	a.`CertTotalHectare` AS HaTotalFarmArea,
	a.`SalesLastYear` AS LastYearDelivery,
	a.`SalesLast2Years` AS Last2ndYearDelivery,
	a.`SalesLast3Years` AS Last3rdYearDelivery,
        m.ClusterID
FROM
	`ktv_cocoa_certification_afl_farmer` a
	LEFT JOIN `ktv_ims` b ON b.`IMSID` = a.`IMSID`
	LEFT JOIN `ktv_ims_master` c ON c.`IMSMasterID` = b.`IMSMasterID`
	LEFT JOIN `ktv_certification_holders` d ON d.`CertHolderID` = c.`CertHolderID`
	LEFT JOIN `ktv_first_buyer` e ON e.`FirstBuyerID` = b.`FirstBuyerID`
	LEFT JOIN `ktv_program_partner` f ON f.`PartnerID` = e.`FirstBuyerPartnerID`
	LEFT JOIN `ktv_cocoa_farmer` g ON g.`FarmerID` = a.`FarmerID`
	LEFT JOIN `ktv_supplychain_org` h ON h.`SupplychainID` = d.`SupplychainID`
	LEFT JOIN `ktv_traders` i ON i.`TraderID` = h.`OrgID` 
	AND h.`OrgType` = 'trader'
	LEFT JOIN `ktv_cooperatives` j ON j.`CoopID` = h.`OrgID` 
	AND h.`OrgType` = 'koperasi'
        LEFT JOIN ktv_village vil ON vil.`VillageID` = g.VillageID
        LEFT JOIN ktv_subdistrict subd ON subd.SubDistrictID = vil.SubDistrictID
	LEFT JOIN `ktv_district` k ON k.`DistrictID` = subd.DistrictID
	LEFT JOIN `ktv_first_buyer_program` l ON l.`ProgID` = b.`ProgID`
	LEFT JOIN ktv_cocoa_certification_pre_afl m ON m.IMSID = a.IMSID 
	AND m.FarmerID = a.FarmerID 
WHERE
	b.IMSID IN (252,278)
	AND b.`StatusCode` = 'active' 
	AND c.`StatusCode` = 'active' 
	AND a.CertStatusAudit = 'Comply' 
ORDER BY
	d.`CertHolderID`,
	a.`District`,
	a.`CPGid`;";
        $query = $this->db->query($sql); //detailCT3
        
        if ($query) {
            $results['detailCT3'] = "CT 3 Internal Management System and IMS updated.";
        } else {
            $results['detailCT3'] .= "||CT 3 Internal Management System and IMS Failed to update";
        }
        return $results;
    }

    function insert_detail_ICS0ActiveJoin()
	{
		$sql = "DELETE FROM ktv_jbcocoa_summary_ics_zero_active_join WHERE DateGenerated = DATE(NOW())";
		$query = $this->db->query($sql);
		$sql = "INSERT INTO ktv_jbcocoa_summary_ics_zero_active_join (
                DateGenerated,
                CertHolderID,
                DistrictID,
                IMSID,
                ClusterID,
                Program,
                CertificateHolders,
                Responsible,
                FarmerID,
                StatusFarmer,
                FarmerName,
                Gender,
                HandPhone,
                Province,
                District,
                SubDistrict,
                Village,
                GroupID,
                FarmerGroup,
                StatusAudit,
                AuditRemarks,
                FirstYearOfCertification,
                YearOfCertification,
                CertificationYear,
                ICSDate,
                PresentYearHarvest,
                PreviousYearHarvest,
                HaCertifiedCropArea,
                NrOfCertifiedPlots,
                HaTotalFarmArea,
                LastYearDelivery,
                Last2ndYearDelivery,
                Last3rdYearDelivery
           ) 
           SELECT 
                DATE(NOW()),
                d.CertHolderID,
                k.DistrictID,
                b.`IMSID`,
								m.ClusterID,
                CONCAT(
                     l.`ProgramName`,
                     ' (',
                     l.`ProgramYear`,
                     ')'
                ) AS Program,
                IF(
                     h.OrgType = 'trader',
                     i.`Company`,
                     IF(
                          h.OrgType = 'koperasi',
                          j.CoopName,
                          '-'
                     )
                ) AS CertificateHolders,
                IF(
                     h.OrgType = 'trader',
                     i.TraderName,
                     IF(
                          h.OrgType = 'koperasi',
                          j.CoopName,
                          '-'
                     )
                ) AS Responsible,
                g.`FarmerID`,
                g.`StatusFarmer`,
                g.`FarmerName`,
                a.`Gender`,
                a.`HandPhone`,
                a.`Province`,
                a.`District`,
                a.`SubDistrict`,
                a.`Village`,
                a.`CPGid` AS GroupID,
                a.`GroupName` AS FarmerGroup,
                a.`CertStatusAudit` AS StatusAudit,
                a.`CertAuditRemark` AS AuditRemarks,
                a.`CertFirstYear` AS FirstYearOfCertification,
                a.`YearOfCertification` AS YearOfCertification,
                a.`CertYear` AS CertificationYear,
                a.`CertICSDate` AS ICSDate,
                a.`CertNextHarvest` AS PresentYearHarvest,
                a.`CertHarvest` AS PreviousYearHarvest,
                a.`CertHectare` AS HaCertifiedCropArea,
                a.`CertFarmNr` AS NrOfCertifiedPlots,
                a.`CertTotalHectare` AS HaTotalFarmArea,
                a.`SalesLastYear` AS LastYearDelivery,
                a.`SalesLast2Years` AS Last2ndYearDelivery,
                a.`SalesLast3Years` AS Last3rdYearDelivery 
           FROM
                `ktv_cocoa_certification_afl_farmer` a 
                LEFT JOIN `ktv_ims` b 
                     ON b.`IMSID` = a.`IMSID` 
                LEFT JOIN `ktv_ims_master` c 
                     ON c.`IMSMasterID` = b.`IMSMasterID` 
                LEFT JOIN `ktv_certification_holders` d 
                     ON d.`CertHolderID` = c.`CertHolderID` 
                LEFT JOIN `ktv_first_buyer` e 
                     ON e.`FirstBuyerID` = b.`FirstBuyerID` 
                LEFT JOIN `ktv_program_partner` f 
                     ON f.`PartnerID` = e.`FirstBuyerPartnerID` 
                LEFT JOIN `ktv_cocoa_farmer` g 
                     ON g.`FarmerID` = a.`FarmerID` 
                LEFT JOIN `ktv_supplychain_org` h 
                     ON h.`SupplychainID` = d.`SupplychainID` 
                LEFT JOIN `ktv_traders` i 
                     ON i.`TraderID` = h.`OrgID` 
                     AND h.`OrgType` = 'trader' 
                LEFT JOIN `ktv_cooperatives` j 
                     ON j.`CoopID` = h.`OrgID` 
                     AND h.`OrgType` = 'koperasi' 
                LEFT JOIN ktv_village vil ON vil.VillageID = g.VillageID
                LEFT JOIN ktv_subdistrict subd ON subd.SubDistrictID = vil.SubDistrictID
                LEFT JOIN `ktv_district` k ON k.`DistrictID` = subd.DistrictID 
                LEFT JOIN `ktv_first_buyer_program` l 
                     ON l.`ProgID` = b.`ProgID` 
                JOIN `ktv_cocoa_certification_pre_afl` m 
                     ON m.`FarmerID` = a.`FarmerID` 
                     AND m.`IMSID` = a.`IMSID` 
           WHERE b.`IMSID` IN (207) 
                AND b.`StatusCode` = 'active' 
                AND c.`StatusCode` = 'active' 
                AND a.CertStatusAudit IN ('Comply', 'Not Comply') 
                AND g.`StatusFarmer` = '1' 
                AND g.`StatusCode` = 'active' 
                AND m.`StatusCode` = 'active' 
                AND m.`StatusComply` = 1 
           GROUP BY g.FarmerID 
           ORDER BY d.`CertHolderID`,
                a.`District`,
                a.`CPGid`";
		$query = $this->db->query($sql); //detailICS0ActiveJoin
		if ($query) {
			$results['detailICS0ActiveJoin'] = "detailICS0ActiveJoin updated.";
		} else {
			$results['detailICS0ActiveJoin'] .= "||detailICS0ActiveJoin Failed to update";
		}
		return $results;
	}
	
	function insert_detail_ICS0ActiveNotJoin()
	{
		$sql = "DELETE FROM ktv_jbcocoa_summary_ics_zero_active_not_join WHERE DateGenerated = DATE(NOW())";
		$query = $this->db->query($sql);
		$sql = "INSERT INTO ktv_jbcocoa_summary_ics_zero_active_not_join (
                DateGenerated,
                CertHolderID,
                DistrictID,
                IMSID,
                ClusterID,
                ProgID,
                Program,
                CertificateHolders,
                Responsible,
                FarmerID,
                StatusFarmer,
                FarmerName,
                Gender,
                HandPhone,
                Province,
                District,
                SubDistrict,
                Village,
                GroupID,
                FarmerGroup,
                StatusAudit,
                AuditRemarks,
                FirstYearOfCertification,
                YearOfCertification,
                CertificationYear,
                ICSDate,
                PresentYearHarvest,
                PreviousYearHarvest,
                HaCertifiedCropArea,
                NrOfCertifiedPlots,
                HaTotalFarmArea,
                LastYearDelivery,
                Last2ndYearDelivery,
                Last3rdYearDelivery
           ) 
           SELECT 
                DATE(NOW()),
                d.CertHolderID,
                k.DistrictID,
                b.`IMSID`,
                m.ClusterID,
                l.ProgID,
                CONCAT(
                     l.`ProgramName`,
                     ' (',
                     l.`ProgramYear`,
                     ')'
                ) AS Program,
                IF(
                     h.OrgType = 'trader',
                     i.`Company`,
                     IF(
                          h.OrgType = 'koperasi',
                          j.CoopName,
                          '-'
                     )
                ) AS CertificateHolders,
                IF(
                     h.OrgType = 'trader',
                     i.TraderName,
                     IF(
                          h.OrgType = 'koperasi',
                          j.CoopName,
                          '-'
                     )
                ) AS Responsible,
                g.`FarmerID`,
                g.`StatusFarmer`,
                g.`FarmerName`,
                a.`Gender`,
                a.`HandPhone`,
                a.`Province`,
                a.`District`,
                a.`SubDistrict`,
                a.`Village`,
                a.`CPGid` AS GroupID,
                a.`GroupName` AS FarmerGroup,
                a.`CertStatusAudit` AS StatusAudit,
                a.`CertAuditRemark` AS AuditRemarks,
                a.`CertFirstYear` AS FirstYearOfCertification,
                a.`YearOfCertification` AS YearOfCertification,
                a.`CertYear` AS CertificationYear,
                a.`CertICSDate` AS ICSDate,
                a.`CertNextHarvest` AS PresentYearHarvest,
                a.`CertHarvest` AS PreviousYearHarvest,
                a.`CertHectare` AS HaCertifiedCropArea,
                a.`CertFarmNr` AS NrOfCertifiedPlots,
                a.`CertTotalHectare` AS HaTotalFarmArea,
                a.`SalesLastYear` AS LastYearDelivery,
                a.`SalesLast2Years` AS Last2ndYearDelivery,
                a.`SalesLast3Years` AS Last3rdYearDelivery 
           FROM
                `ktv_cocoa_certification_afl_farmer` a 
                LEFT JOIN `ktv_ims` b 
                     ON b.`IMSID` = a.`IMSID` 
                LEFT JOIN `ktv_ims_master` c 
                     ON c.`IMSMasterID` = b.`IMSMasterID` 
                LEFT JOIN `ktv_certification_holders` d 
                     ON d.`CertHolderID` = c.`CertHolderID` 
                LEFT JOIN `ktv_first_buyer` e 
                     ON e.`FirstBuyerID` = b.`FirstBuyerID` 
                LEFT JOIN `ktv_program_partner` f 
                     ON f.`PartnerID` = e.`FirstBuyerPartnerID` 
                LEFT JOIN `ktv_cocoa_farmer` g 
                     ON g.`FarmerID` = a.`FarmerID` 
                LEFT JOIN `ktv_supplychain_org` h 
                     ON h.`SupplychainID` = d.`SupplychainID` 
                LEFT JOIN `ktv_traders` i 
                     ON i.`TraderID` = h.`OrgID` 
                     AND h.`OrgType` = 'trader' 
                LEFT JOIN `ktv_cooperatives` j 
                     ON j.`CoopID` = h.`OrgID` 
                     AND h.`OrgType` = 'koperasi' 
                LEFT JOIN ktv_village vil ON vil.VillageID = g.VillageID
                LEFT JOIN ktv_subdistrict subd ON subd.SubDistrictID = vil.SubDistrictID
                LEFT JOIN `ktv_district` k ON k.`DistrictID` = subd.DistrictID 
                LEFT JOIN `ktv_first_buyer_program` l 
                     ON l.`ProgID` = b.`ProgID` 
                JOIN `ktv_cocoa_certification_pre_afl` m 
                     ON m.`FarmerID` = a.`FarmerID` 
                     AND m.`IMSID` = a.`IMSID` 
           WHERE b.`IMSID` IN (207) 
                AND b.`StatusCode` = 'active' 
                AND c.`StatusCode` = 'active' 
                AND a.CertStatusAudit IN ('Comply', 'Not Comply') 
                AND g.`StatusFarmer` = '1' 
                AND g.`StatusCode` = 'active' 
                AND m.`StatusCode` = 'active' 
                AND m.`StatusComply` = 2 
           GROUP BY g.FarmerID 
           ORDER BY d.`CertHolderID`,
                a.`District`,
                a.`CPGid`";
		$query = $this->db->query($sql); //detailICS0ActiveNotJoin
		if ($query) {
			$results['detailICS0ActiveNotJoin'] = "detailICS0ActiveNotJoin updated.";
		} else {
			$results['detailICS0ActiveNotJoin'] .= "||detailICS0ActiveNotJoin Failed to update";
		}
		return $results;
	}
	
	function insert_detail_ICS0NotActive()
	{
		$sql = "DELETE FROM ktv_jbcocoa_summary_ics_zero_notactive WHERE DateGenerated = DATE(NOW())";
		$query = $this->db->query($sql);
		$sql = "INSERT INTO ktv_jbcocoa_summary_ics_zero_notactive (
                DateGenerated,
                CertHolderID,
                DistrictID,
                IMSID,
                ClusterID,
                Program,
                CertificateHolders,
                Responsible,
                FarmerID,
                FarmerName,
                Gender,
                HandPhone,
                Province,
                District,
                SubDistrict,
                Village,
                GroupID,
                FarmerGroup,
                StatusAudit,
                AuditRemarks,
                FirstYearOfCertification,
                YearOfCertification,
                CertificationYear,
                ICSDate,
                PresentYearHarvest,
                PreviousYearHarvest,
                HaCertifiedCropArea,
                NrOfCertifiedPlots,
                HaTotalFarmArea,
                LastYearDelivery,
                Last2ndYearDelivery,
                Last3rdYearDelivery
           ) 
           SELECT 
                DATE(NOW()),
                d.CertHolderID,
                k.DistrictID,
                b.`IMSID`,
                q.ClusterID,
                CONCAT(
                     l.`ProgramName`,
                     ' (',
                     l.`ProgramYear`,
                     ')'
                ) AS Program,
                IF(
                     h.OrgType = 'trader',
                     i.`Company`,
                     IF(
                          h.OrgType = 'koperasi',
                          j.CoopName,
                          '-'
                     )
                ) AS CertificateHolders,
                IF(
                     h.OrgType = 'trader',
                     i.TraderName,
                     IF(
                          h.OrgType = 'koperasi',
                          j.CoopName,
                          '-'
                     )
                ) AS Responsible,
                g.`FarmerID`,
                g.`FarmerName`,
                a.`Gender`,
                a.`HandPhone`,
                a.`Province`,
                a.`District`,
                a.`SubDistrict`,
                a.`Village`,
                a.`CPGid` AS GroupID,
                a.`GroupName` AS FarmerGroup,
                a.`CertStatusAudit` AS StatusAudit,
                a.`CertAuditRemark` AS AuditRemarks,
                a.`CertFirstYear` AS FirstYearOfCertification,
                a.`YearOfCertification` AS YearOfCertification,
                a.`CertYear` AS CertificationYear,
                a.`CertICSDate` AS ICSDate,
                a.`CertNextHarvest` AS PresentYearHarvest,
                a.`CertHarvest` AS PreviousYearHarvest,
                a.`CertHectare` AS HaCertifiedCropArea,
                a.`CertFarmNr` AS NrOfCertifiedPlots,
                a.`CertTotalHectare` AS HaTotalFarmArea,
                a.`SalesLastYear` AS LastYearDelivery,
                a.`SalesLast2Years` AS Last2ndYearDelivery,
                a.`SalesLast3Years` AS Last3rdYearDelivery 
           FROM
                `ktv_cocoa_certification_afl_farmer` a 
                LEFT JOIN `ktv_ims` b 
                     ON b.`IMSID` = a.`IMSID` 
                LEFT JOIN `ktv_ims_master` c 
                     ON c.`IMSMasterID` = b.`IMSMasterID` 
                LEFT JOIN `ktv_certification_holders` d 
                     ON d.`CertHolderID` = c.`CertHolderID` 
                LEFT JOIN `ktv_first_buyer` e 
                     ON e.`FirstBuyerID` = b.`FirstBuyerID` 
                LEFT JOIN `ktv_program_partner` f 
                     ON f.`PartnerID` = e.`FirstBuyerPartnerID` 
                LEFT JOIN `ktv_cocoa_farmer` g 
                     ON g.`FarmerID` = a.`FarmerID` 
                LEFT JOIN `ktv_supplychain_org` h 
                     ON h.`SupplychainID` = d.`SupplychainID` 
                LEFT JOIN `ktv_traders` i 
                     ON i.`TraderID` = h.`OrgID` 
                     AND h.`OrgType` = 'trader' 
                LEFT JOIN `ktv_cooperatives` j 
                     ON j.`CoopID` = h.`OrgID` 
                     AND h.`OrgType` = 'koperasi' 
                LEFT JOIN ktv_village vil ON vil.VillageID = g.VillageID
                LEFT JOIN ktv_subdistrict subd ON subd.SubDistrictID = vil.SubDistrictID
                LEFT JOIN `ktv_district` k ON k.`DistrictID` = subd.DistrictID 
                LEFT JOIN `ktv_first_buyer_program` l 
                     ON l.`ProgID` = b.`ProgID` 
                LEFT JOIN ktv_cocoa_certification_pre_afl q ON q.IMSID = a.IMSID AND q.FarmerID = a.FarmerID 
           WHERE b.`IMSID` IN (207) 
                AND b.`StatusCode` = 'active' 
                AND c.`StatusCode` = 'active' 
                AND a.CertStatusAudit IN ('Comply', 'Not Comply') 
                AND g.`StatusFarmer` = '2' 
                AND g.`StatusCode` = 'active' 
           GROUP BY g.FarmerID 
           ORDER BY d.`CertHolderID`,
                a.`District`,
                a.`CPGid`";
		$query = $this->db->query($sql); //detailICS0NotActive
		if ($query) {
			$results['detailICS0NotActive'] = "detailICS0NotActive updated.";
		} else {
			$results['detailICS0NotActive'] .= "||detailICS0NotActive Failed to update";
		}
		return $results;
	}
	
	function insert_detail_MasterTraining()
	{
		$sql = "DELETE FROM ktv_jbcocoa_summary_master_training WHERE DateGenerated = DATE(NOW())";
		$query = $this->db->query($sql);
		$sql = "INSERT INTO ktv_jbcocoa_summary_master_training (
            DateGenerated,
            CertHolderID,
            DistrictID,
            IMSID,
            ClusterID,
            Program,
            CertificateHolders,
            Responsible,
            ParticipantID,
            ParticipantName,
            `Position`,
            Province,
            District,
            EventName,
            EventDate,
            NrOfTrainingDays
        ) 
        SELECT 
            DATE(NOW()),
            h.CertHolderID,
            o.DistrictID,
            f.`IMSID`,
            b.ClusterID,
            CONCAT(
                s.`ProgramName`,
                ' (',
                s.`ProgramYear`,
                ')'
            ) AS Program,
            IF(
                k.OrgType = 'trader',
                l.`Company`,
                IF(
                    k.OrgType = 'koperasi',
                    m.CoopName,
                    '-'
                )
            ) AS CertificateHolders,
            IF(
                k.OrgType = 'trader',
                l.TraderName,
                IF(
                    k.OrgType = 'koperasi',
                    m.CoopName,
                    '-'
                )
            ) AS Responsible,
            e.`PersonID` AS ParticipantID,
            e.`PersonNm` AS ParticipantName,
            u.`PositionName` AS `Position`,
            p.`Province` AS Province,
            o.`District` AS District,
            q.`CpgTrainings` AS EventName,
            DATE(b.`TrainingStart`) AS EventDate,
            b.`TrainingDays` AS NrOfTrainingDays 
        FROM
            `ktv_master_trainings_participants` a 
            LEFT JOIN `ktv_master_trainings` b 
                ON b.`MasterTrainingID` = a.`MasterTrainingID` 
            LEFT JOIN `ktv_cpg_batch` c 
                ON c.`CpgBatchID` = b.`CpgBatchID` 
            LEFT JOIN `ktv_persons` e 
                ON e.`PersonID` = a.`ParticipantPersonID` 
                LEFT JOIN ktv_staffs d ON d.PersonID=e.PersonID
            LEFT JOIN `ktv_ims` f 
                ON f.`IMSID` = b.`IMSID` 
            LEFT JOIN `ktv_ims_master` g 
                ON g.`IMSMasterID` = f.`IMSMasterID` 
            LEFT JOIN `ktv_certification_holders` h 
                ON h.`CertHolderID` = f.`CertHolderID` 
            LEFT JOIN `ktv_first_buyer` i 
                ON i.`FirstBuyerID` = f.`FirstBuyerID` 
            LEFT JOIN `ktv_program_partner` j 
                ON j.`PartnerID` = i.`FirstBuyerPartnerID` 
            LEFT JOIN `ktv_supplychain_org` k 
                ON k.`SupplychainID` = h.`SupplychainID` 
            LEFT JOIN `ktv_traders` l 
                ON l.`TraderID` = k.`OrgID` 
                AND k.`OrgType` = 'trader' 
            LEFT JOIN `ktv_cooperatives` m 
                ON m.`CoopID` = k.`OrgID` 
                AND k.`OrgType` = 'koperasi' 
            LEFT JOIN `ktv_ref_work_area` n 
                ON n.`WorkAreaID` = d.`WorkAreaID` 
            LEFT JOIN `ktv_district` o 
                ON o.`DistrictID` = n.`DistrictID` 
            LEFT JOIN `ktv_province` p 
                ON p.`ProvinceID` = o.`ProvinceID` 
            LEFT JOIN `ktv_cpg_trainings` q 
                ON q.`CpgTrainingsID` = b.`CPGtrainingsID` 
            LEFT JOIN `ktv_master_trainings_sub_topics` r 
                ON r.`MasterTrainingID` = b.`MasterTrainingID` 
            LEFT JOIN `ktv_first_buyer_program` s 
                ON s.`ProgID` = f.`ProgID` 
            LEFT JOIN `ktv_staff_positions` t 
                ON t.`StaffPosStaffID` = d.`StaffID` 
            LEFT JOIN `ktv_ref_position_type` u 
                ON u.`PositionID` = t.`StaffPosPositionID` 
        WHERE f.`IMSID` IN (207 ) 
            AND c.`BatchNumber` = '166' 
            AND a.`StatusCode` = 'active' 
            AND b.`StatusCode` = 'active' 
            AND f.`StatusCode` = 'active' 
            AND q.`CpgTrainingsID` IN (1, 14) 
        GROUP BY d.`StaffID` ";
		$query = $this->db->query($sql); //detailMasterTraining
		if ($query) {
			$results['detailMasterTraining'] = "detailMasterTraining updated.";
		} else {
			$results['detailMasterTraining'] .= "||detailMasterTraining Failed to update";
		}
		return $results;
	}
	
	function insert_detail_SEL()
	{
		$sql = "DELETE FROM ktv_jbcocoa_summary_selection WHERE DateGenerated = DATE(NOW())";
		$query = $this->db->query($sql);
		$sql = "INSERT INTO ktv_jbcocoa_summary_selection (
                DateGenerated,
                CertHolderID,
                DistrictID,
                IMSID,
                ClusterID,
                ProgID,
                Program,
                CertificateHolders,
                Responsible,
                ParticipantID,
                ParticipantName,
                Gender,
                FarmerGroup,
                ParticipantType,
                Province,
                District,
                SubDistrict,
                Village,
                EventName,
                EventDate,
                SelectionStatus,
                DateLocked,
                PIC
           ) 
           SELECT 
                DATE(NOW()),
                g.CertHolderID,
                o.DistrictID,
                e.`IMSID`,
                a.ClusterID,
                z.ProgID,
                CONCAT(
                     z.`ProgramName`,
                     ' (',
                     z.`ProgramYear`,
                     ')'
                ) AS Program,
                IF(
                     j.OrgType = 'trader',
                     k.`Company`,
                     IF(
                          j.OrgType = 'koperasi',
                          l.CoopName,
                          '-'
                     )
                ) AS CertificateHolders,
                IF(
                     j.OrgType = 'trader',
                     k.TraderName,
                     IF(
                          j.OrgType = 'koperasi',
                          l.CoopName,
                          '-'
                     )
                ) AS Responsible,
                a.DestObjID AS ParticipantID,
                d.FarmerName AS ParticipantName,
								IF(
                     d.`Gender` = 1,
									   'Male',
                     IF(d.`Gender` = 2, 'Female', '')
								)  AS Gender,
                
                     q.`GroupName`
								AS FarmerGroup,
                IF(
                     a.ObjType = 'Applicant',
                     'New Applicant',
                     'Existing Farmer'
                ) AS ParticipantType,
                
                     p.`Province`
                AS Province,
                
                     o.`District`
                AS District,
                
                     n.`SubDistrict`
                 AS SubDistrict,
                
                     m.`Village`
                 AS Village,
                b.`EventName`,
                b.`EventStart` AS EventDate,
                IF(
                     a.`SelectionStatus` = 1,
                     'Recommended',
                     IF(
                          a.`SelectionStatus` = 2,
                          'Not Recommended',
                          'No Status'
                     )
                ) AS SelectionStatus,
                a.`DateGenerated` AS DateLocked,
                y.`PersonNm` AS PIC 
           FROM
                `ktv_ims_soc_sel` a 
                LEFT JOIN `ktv_ims_socializations` b 
                     ON b.`IMSSocID` = a.`IMSSocID` 
                LEFT JOIN `ktv_cocoa_farmer` d 
                     ON d.`FarmerID` = a.`DestObjID` 
                LEFT JOIN `ktv_ims` e 
                     ON e.`IMSID` = a.`IMSID` 
                LEFT JOIN `ktv_ims_master` f 
                     ON f.`IMSMasterID` = e.`IMSMasterID` 
                LEFT JOIN `ktv_certification_holders` g 
                     ON g.`CertHolderID` = f.`CertHolderID` 
                LEFT JOIN `ktv_first_buyer` h 
                     ON h.`FirstBuyerID` = e.`FirstBuyerID` 
                LEFT JOIN `ktv_program_partner` i 
                     ON i.`PartnerID` = h.`FirstBuyerPartnerID` 
                LEFT JOIN `ktv_supplychain_org` j 
                     ON j.`SupplychainID` = g.`SupplychainID` 
                LEFT JOIN `ktv_traders` k 
                     ON k.`TraderID` = j.`OrgID` 
                     AND j.`OrgType` = 'trader' 
                LEFT JOIN `ktv_cooperatives` l 
                     ON l.`CoopID` = j.`OrgID` 
                     AND j.`OrgType` = 'koperasi' 
                LEFT JOIN `ktv_village` m 
                     ON m.`VillageID` = d.`VillageID` 
                LEFT JOIN `ktv_subdistrict` n 
                     ON n.`SubDistrictID` = m.`SubDistrictID` 
                LEFT JOIN `ktv_district` o 
                     ON o.`DistrictID` = n.`DistrictID` 
                LEFT JOIN `ktv_province` p 
                     ON p.`ProvinceID` = o.`ProvinceID` 
                LEFT JOIN `ktv_cpg` q 
                     ON q.`CPGid` = d.`CPGid` 
                LEFT JOIN `ktv_staffs` `x` 
                     ON x.`StaffID` = b.`PICStaffID` 
                LEFT JOIN `ktv_persons` `y` 
                     ON y.`PersonID` = x.`PersonID` 
                LEFT JOIN `ktv_first_buyer_program` z 
                     ON z.`ProgID` = e.`ProgID` 
           WHERE e.`IMSID` IN (207) 
                AND a.ParticipateInSocializationStatus = 1 
                AND a.`ObjType` IN ('Applicant', 'Existing Farmer') 
           GROUP BY a.ObjID;";
		$query = $this->db->query($sql); //detailSEL
		if ($query) {
			$results['detailSEL'] = "detailSEL updated.";
		} else {
			$results['detailSEL'] .= "||detailSEL Failed to update";
		}
		return $results;
	}
	
	function insert_detail_SELY2()
	{
		$sql = "DELETE FROM ktv_jbcocoa_summary_selection_second_year WHERE DateGenerated = DATE(NOW())";
		$query = $this->db->query($sql);
		$sql = "INSERT INTO ktv_jbcocoa_summary_selection_second_year (
                DateGenerated,
                CertHolderID,
                DistrictID,
                IMSID,
                ClusterID,
                ProgID,
                Program,
                CertificateHolders,
                Responsible,
                ParticipantID,
                ParticipantName,
                Gender,
                FarmerGroup,
                ParticipantType,
                Province,
                District,
                SubDistrict,
                Village,
                EventName,
                EventDate,
                SelectionStatus,
                DateLocked,
                PIC
           ) 
           SELECT 
                DATE(NOW()),
                g.CertHolderID,
                o.DistrictID,
                e.`IMSID`,
                a.ClusterID,
                z.ProgID,
                CONCAT(
                     z.`ProgramName`,
                     ' (',
                     z.`ProgramYear`,
                     ')'
                ) AS Program,
                IF(
                     j.OrgType = 'trader',
                     k.`Company`,
                     IF(
                          j.OrgType = 'koperasi',
                          l.CoopName,
                          '-'
                     )
                ) AS CertificateHolders,
                IF(
                     j.OrgType = 'trader',
                     k.TraderName,
                     IF(
                          j.OrgType = 'koperasi',
                          l.CoopName,
                          '-'
                     )
                ) AS Responsible,
                a.DestObjID AS ParticipantID,
                d.FarmerName AS ParticipantName,
								IF(
                     d.`Gender` = 1,
									   'Male',
                     IF(d.`Gender` = 2, 'Female', '')
								)  AS Gender,
                
                     q.`GroupName`
								AS FarmerGroup,
                IF(
                     a.ObjType = 'Applicant',
                     'New Applicant',
                     'Existing Farmer'
                ) AS ParticipantType,
                
                     p.`Province`
                AS Province,
                
                     o.`District`
                AS District,
                
                     n.`SubDistrict`
                 AS SubDistrict,
                
                     m.`Village`
                 AS Village,
                b.`EventName`,
                b.`EventStart` AS EventDate,
                IF(
                     a.`SelectionStatus` = 1,
                     'Recommended',
                     IF(
                          a.`SelectionStatus` = 2,
                          'Not Recommended',
                          'No Status'
                     )
                ) AS SelectionStatus,
                a.`DateGenerated` AS DateLocked,
                y.`PersonNm` AS PIC 
           FROM
                `ktv_ims_soc_sel` a 
                LEFT JOIN `ktv_ims_socializations` b 
                     ON b.`IMSSocID` = a.`IMSSocID` 
                LEFT JOIN `ktv_cocoa_farmer` d 
                     ON d.`FarmerID` = a.`DestObjID` 
                LEFT JOIN `ktv_ims` e 
                     ON e.`IMSID` = a.`IMSID` 
                LEFT JOIN `ktv_ims_master` f 
                     ON f.`IMSMasterID` = e.`IMSMasterID` 
                LEFT JOIN `ktv_certification_holders` g 
                     ON g.`CertHolderID` = f.`CertHolderID` 
                LEFT JOIN `ktv_first_buyer` h 
                     ON h.`FirstBuyerID` = e.`FirstBuyerID` 
                LEFT JOIN `ktv_program_partner` i 
                     ON i.`PartnerID` = h.`FirstBuyerPartnerID` 
                LEFT JOIN `ktv_supplychain_org` j 
                     ON j.`SupplychainID` = g.`SupplychainID` 
                LEFT JOIN `ktv_traders` k 
                     ON k.`TraderID` = j.`OrgID` 
                     AND j.`OrgType` = 'trader' 
                LEFT JOIN `ktv_cooperatives` l 
                     ON l.`CoopID` = j.`OrgID` 
                     AND j.`OrgType` = 'koperasi' 
                LEFT JOIN `ktv_village` m 
                     ON m.`VillageID` = d.`VillageID` 
                LEFT JOIN `ktv_subdistrict` n 
                     ON n.`SubDistrictID` = m.`SubDistrictID` 
                LEFT JOIN `ktv_district` o 
                     ON o.`DistrictID` = n.`DistrictID` 
                LEFT JOIN `ktv_province` p 
                     ON p.`ProvinceID` = o.`ProvinceID` 
                LEFT JOIN `ktv_cpg` q 
                     ON q.`CPGid` = d.`CPGid` 
                LEFT JOIN `ktv_staffs` `x` 
                     ON x.`StaffID` = b.`PICStaffID` 
                LEFT JOIN `ktv_persons` `y` 
                     ON y.`PersonID` = x.`PersonID` 
                LEFT JOIN `ktv_first_buyer_program` z 
                     ON z.`ProgID` = e.`ProgID` 
           WHERE e.`IMSID` IN (207) 
                AND a.ParticipateInSocializationStatus = 1 
                AND a.`ObjType` = 'Existing Certified Farmer' 
           GROUP BY a.ObjID;";
		$query = $this->db->query($sql); //detailSELY2
		if ($query) {
			$results['detailSELY2'] = "detailSELY2 updated.";
		} else {
			$results['detailSELY2'] .= "||detailSELY2 Failed to update";
		}
		return $results;
	}
	
	function insert_detail_SELY2Yes()
	{
		$sql = "DELETE FROM ktv_jbcocoa_summary_selection_second_year_yes WHERE DateGenerated = DATE(NOW())";
		$query = $this->db->query($sql);
		$sql = "INSERT INTO ktv_jbcocoa_summary_selection_second_year_yes (
                DateGenerated,
                CertHolderID,
                DistrictID,
                IMSID,
                ClusterID,
                ProgID,
                Program,
                CertificateHolders,
                Responsible,
                ParticipantID,
                ParticipantName,
                Gender,
                FarmerGroup,
                ParticipantType,
                Province,
                District,
                SubDistrict,
                Village,
                EventName,
                EventDate,
                SelectionStatus,
                DateLocked,
                PIC
           ) 
           SELECT 
                DATE(NOW()),
                g.CertHolderID,
                o.DistrictID,
                e.`IMSID`,
                a.ClusterID,
                z.ProgID,
                CONCAT(
                     z.`ProgramName`,
                     ' (',
                     z.`ProgramYear`,
                     ')'
                ) AS Program,
                IF(
                     j.OrgType = 'trader',
                     k.`Company`,
                     IF(
                          j.OrgType = 'koperasi',
                          l.CoopName,
                          '-'
                     )
                ) AS CertificateHolders,
                IF(
                     j.OrgType = 'trader',
                     k.TraderName,
                     IF(
                          j.OrgType = 'koperasi',
                          l.CoopName,
                          '-'
                     )
                ) AS Responsible,
                a.DestObjID AS ParticipantID,
                d.FarmerName AS ParticipantName,
								IF(
                     d.`Gender` = 1,
									   'Male',
                     IF(d.`Gender` = 2, 'Female', '')
								)  AS Gender,
                
                     q.`GroupName`
								AS FarmerGroup,
                IF(
                     a.ObjType = 'Applicant',
                     'New Applicant',
                     'Existing Farmer'
                ) AS ParticipantType,
                
                     p.`Province`
                AS Province,
                
                     o.`District`
                AS District,
                
                     n.`SubDistrict`
                 AS SubDistrict,
                
                     m.`Village`
                 AS Village,
                b.`EventName`,
                b.`EventStart` AS EventDate,
                IF(
                     a.`SelectionStatus` = 1,
                     'Recommended',
                     IF(
                          a.`SelectionStatus` = 2,
                          'Not Recommended',
                          'No Status'
                     )
                ) AS SelectionStatus,
                a.`DateGenerated` AS DateLocked,
                y.`PersonNm` AS PIC 
           FROM
                `ktv_ims_soc_sel` a 
                LEFT JOIN `ktv_ims_socializations` b 
                     ON b.`IMSSocID` = a.`IMSSocID` 
                LEFT JOIN `ktv_cocoa_farmer` d 
                     ON d.`FarmerID` = a.`DestObjID` 
                LEFT JOIN `ktv_ims` e 
                     ON e.`IMSID` = a.`IMSID` 
                LEFT JOIN `ktv_ims_master` f 
                     ON f.`IMSMasterID` = e.`IMSMasterID` 
                LEFT JOIN `ktv_certification_holders` g 
                     ON g.`CertHolderID` = f.`CertHolderID` 
                LEFT JOIN `ktv_first_buyer` h 
                     ON h.`FirstBuyerID` = e.`FirstBuyerID` 
                LEFT JOIN `ktv_program_partner` i 
                     ON i.`PartnerID` = h.`FirstBuyerPartnerID` 
                LEFT JOIN `ktv_supplychain_org` j 
                     ON j.`SupplychainID` = g.`SupplychainID` 
                LEFT JOIN `ktv_traders` k 
                     ON k.`TraderID` = j.`OrgID` 
                     AND j.`OrgType` = 'trader' 
                LEFT JOIN `ktv_cooperatives` l 
                     ON l.`CoopID` = j.`OrgID` 
                     AND j.`OrgType` = 'koperasi' 
                LEFT JOIN `ktv_village` m 
                     ON m.`VillageID` = d.`VillageID` 
                LEFT JOIN `ktv_subdistrict` n 
                     ON n.`SubDistrictID` = m.`SubDistrictID` 
                LEFT JOIN `ktv_district` o 
                     ON o.`DistrictID` = n.`DistrictID` 
                LEFT JOIN `ktv_province` p 
                     ON p.`ProvinceID` = o.`ProvinceID` 
                LEFT JOIN `ktv_cpg` q 
                     ON q.`CPGid` = d.`CPGid` 
                LEFT JOIN `ktv_staffs` `x` 
                     ON x.`StaffID` = b.`PICStaffID` 
                LEFT JOIN `ktv_persons` `y` 
                     ON y.`PersonID` = x.`PersonID` 
                LEFT JOIN `ktv_first_buyer_program` z 
                     ON z.`ProgID` = e.`ProgID` 
           WHERE e.`IMSID` IN (207) 
                AND a.SelectionStatus = 1 
                AND a.`ObjType` = 'Existing Certified Farmer' 
           GROUP BY a.ObjID;";
		$query = $this->db->query($sql); //detailSELY2Yes
		if ($query) {
			$results['detailSELY2Yes'] = "detailSELY2Yes updated.";
		} else {
			$results['detailSELY2Yes'] .= "||detailSELY2Yes Failed to update";
		}
		return $results;
	}
	
	function insert_detail_SELYes()
	{
		$sql = "DELETE FROM ktv_jbcocoa_summary_selection_yes WHERE DateGenerated = DATE(NOW())";
		$query = $this->db->query($sql);
		$sql = "INSERT INTO ktv_jbcocoa_summary_selection_yes (
                DateGenerated,
                CertHolderID,
                DistrictID,
                IMSID,
                ClusterID,
                ProgID,
                Program,
                CertificateHolders,
                Responsible,
                ParticipantID,
                ParticipantName,
                Gender,
                FarmerGroup,
                ParticipantType,
                Province,
                District,
                SubDistrict,
                Village,
                EventName,
                EventDate,
                SelectionStatus,
                DateLocked,
                PIC
           ) 
           SELECT 
                DATE(NOW()),
                g.CertHolderID,
                o.DistrictID,
                e.`IMSID`,
                a.ClusterID,
                z.ProgID,
                CONCAT(
                     z.`ProgramName`,
                     ' (',
                     z.`ProgramYear`,
                     ')'
                ) AS Program,
                IF(
                     j.OrgType = 'trader',
                     k.`Company`,
                     IF(
                          j.OrgType = 'koperasi',
                          l.CoopName,
                          '-'
                     )
                ) AS CertificateHolders,
                IF(
                     j.OrgType = 'trader',
                     k.TraderName,
                     IF(
                          j.OrgType = 'koperasi',
                          l.CoopName,
                          '-'
                     )
                ) AS Responsible,
                a.DestObjID AS ParticipantID,
                d.FarmerName AS ParticipantName,
								IF(
                     d.`Gender` = 1,
									   'Male',
                     IF(d.`Gender` = 2, 'Female', '')
								)  AS Gender,
                
                     q.`GroupName`
								AS FarmerGroup,
                IF(
                     a.ObjType = 'Applicant',
                     'New Applicant',
                     'Existing Farmer'
                ) AS ParticipantType,
                
                     p.`Province`
                AS Province,
                
                     o.`District`
                AS District,
                
                     n.`SubDistrict`
                 AS SubDistrict,
                
                     m.`Village`
                 AS Village,
                b.`EventName`,
                b.`EventStart` AS EventDate,
                IF(
                     a.`SelectionStatus` = 1,
                     'Recommended',
                     IF(
                          a.`SelectionStatus` = 2,
                          'Not Recommended',
                          'No Status'
                     )
                ) AS SelectionStatus,
                a.`DateGenerated` AS DateLocked,
                y.`PersonNm` AS PIC 
           FROM
                `ktv_ims_soc_sel` a 
                LEFT JOIN `ktv_ims_socializations` b 
                     ON b.`IMSSocID` = a.`IMSSocID` 
                LEFT JOIN `ktv_cocoa_farmer` d 
                     ON d.`FarmerID` = a.`DestObjID` 
                LEFT JOIN `ktv_ims` e 
                     ON e.`IMSID` = a.`IMSID` 
                LEFT JOIN `ktv_ims_master` f 
                     ON f.`IMSMasterID` = e.`IMSMasterID` 
                LEFT JOIN `ktv_certification_holders` g 
                     ON g.`CertHolderID` = f.`CertHolderID` 
                LEFT JOIN `ktv_first_buyer` h 
                     ON h.`FirstBuyerID` = e.`FirstBuyerID` 
                LEFT JOIN `ktv_program_partner` i 
                     ON i.`PartnerID` = h.`FirstBuyerPartnerID` 
                LEFT JOIN `ktv_supplychain_org` j 
                     ON j.`SupplychainID` = g.`SupplychainID` 
                LEFT JOIN `ktv_traders` k 
                     ON k.`TraderID` = j.`OrgID` 
                     AND j.`OrgType` = 'trader' 
                LEFT JOIN `ktv_cooperatives` l 
                     ON l.`CoopID` = j.`OrgID` 
                     AND j.`OrgType` = 'koperasi' 
                LEFT JOIN `ktv_village` m 
                     ON m.`VillageID` = d.`VillageID` 
                LEFT JOIN `ktv_subdistrict` n 
                     ON n.`SubDistrictID` = m.`SubDistrictID` 
                LEFT JOIN `ktv_district` o 
                     ON o.`DistrictID` = n.`DistrictID` 
                LEFT JOIN `ktv_province` p 
                     ON p.`ProvinceID` = o.`ProvinceID` 
                LEFT JOIN `ktv_cpg` q 
                     ON q.`CPGid` = d.`CPGid` 
                LEFT JOIN `ktv_staffs` `x` 
                     ON x.`StaffID` = b.`PICStaffID` 
                LEFT JOIN `ktv_persons` `y` 
                     ON y.`PersonID` = x.`PersonID` 
                LEFT JOIN `ktv_first_buyer_program` z 
                     ON z.`ProgID` = e.`ProgID`  
           WHERE e.`IMSID` IN (207) 
                AND a.SelectionStatus = 1 
                AND a.`ObjType` IN ('Applicant', 'Existing Farmer') 
           GROUP BY a.ObjID;";
		$query = $this->db->query($sql); //detailSELYes
		if ($query) {
			$results['detailSELYes'] = "detailSELYes updated.";
		} else {
			$results['detailSELYes'] .= "||detailSELYes Failed to update";
		}
		return $results;
	}
	
	function insert_detail_SOC()
	{
		$sql = "DELETE FROM ktv_jbcocoa_summary_socialization WHERE DateGenerated = DATE(NOW()) AND ProgID=7";
		$query = $this->db->query($sql);
		$sql = "INSERT INTO ktv_jbcocoa_summary_socialization (
                DateGenerated,
                CertHolderID,
                DistrictID,
                IMSID,
                ClusterID,
                ProgID,
                Program,
                CertificateHolders,
                Responsible,
                ParticipantID,
                ParticipantName,
                Gender,
                FarmerGroup,
                ParticipantType,
                Province,
                District,
                SubDistrict,
                Village,
                EventName,
                EventDate,
                ParticipateInSocializationStatus,
                DateLocked,
                PIC
           ) 
           SELECT 
                DATE(NOW()),
                g.CertHolderID,
                IF(
                     a.ObjType = 'Applicant',
                     t.`DistrictID`,
                     o.`DistrictID`
                ) AS DistrictID,
                e.`IMSID`,
                a.ClusterID,
                z.ProgID,
                CONCAT(
                     z.`ProgramName`,
                     ' (',
                     z.`ProgramYear`,
                     ')'
                ) AS Program,
                IF(
                     j.OrgType = 'trader',
                     k.`Company`,
                     IF(
                          j.OrgType = 'koperasi',
                          l.CoopName,
                          '-'
                     )
                ) AS CertificateHolders,
                IF(
                     j.OrgType = 'trader',
                     k.TraderName,
                     IF(
                          j.OrgType = 'koperasi',
                          l.CoopName,
                          '-'
                     )
                ) AS Responsible,
                IF(
                     a.ObjType = 'Applicant',
                     c.`ApplicantID`,
                     d.`FarmerID`
                ) AS ParticipantID,
                IF(
                     a.ObjType = 'Applicant',
                     c.`Fullname`,
                     d.`FarmerName`
                ) AS ParticipantName,
                IF(
                     a.ObjType = 'Applicant',
                     IF(
                          c.`Gender` = 1,
                          'Male',
                          IF(c.`Gender` = 2, 'Female', '')
                     ),
                     IF(
                          d.`Gender` = 1,
                          'Male',
                          IF(d.`Gender` = 2, 'Female', '')
                     )
                ) AS Gender,
                IF(
                     a.ObjType = 'Applicant',
                     v.`GroupName`,
                     q.`GroupName`
                ) AS FarmerGroup,
                IF(
                     a.ObjType = 'Applicant',
                     'New Applicant',
                     'Existing Farmer'
                ) AS ParticipantType,
                IF(
                     a.ObjType = 'Applicant',
                     u.`Province`,
                     p.`Province`
                ) AS Province,
                IF(
                     a.ObjType = 'Applicant',
                     t.`District`,
                     o.`District`
                ) AS District,
                IF(
                     a.ObjType = 'Applicant',
                     s.`SubDistrict`,
                     n.`SubDistrict`
                ) AS SubDistrict,
                IF(
                     a.ObjType = 'Applicant',
                     r.`Village`,
                     m.`Village`
                ) AS Village,
                b.`EventName`,
                b.`EventStart` AS EventDate,
                IF(
                     a.`ParticipateInSocializationStatus` = 1,
                     'Present',
                     IF(
                          a.`ParticipateInSocializationStatus` = 2,
                          'Not Present',
                          'No Status'
                     )
                ) AS ParticipateInSocializationStatus,
                a.`DateGenerated` AS DateLocked,
                y.`PersonNm` AS PIC 
           FROM
                `ktv_ims_soc_sel` a 
                LEFT JOIN `ktv_ims_socializations` b 
                     ON b.`IMSSocID` = a.`IMSSocID` 
                LEFT JOIN `ktv_applicant_farmers` c 
                     ON c.`ApplicantID` = a.`ObjID` 
                     AND a.`ObjType` = 'Applicant' 
                     AND c.`StatusCode` = 'active' 
                LEFT JOIN `ktv_cocoa_farmer` d 
                     ON d.`FarmerID` = a.`ObjID` 
                     AND a.`ObjType` = 'Existing Farmer' 
                     AND d.`StatusCode` = 'active' 
                LEFT JOIN `ktv_ims` e 
                     ON e.`IMSID` = b.`IMSID` 
                LEFT JOIN `ktv_ims_master` f 
                     ON f.`IMSMasterID` = e.`IMSMasterID` 
                LEFT JOIN `ktv_certification_holders` g 
                     ON g.`CertHolderID` = f.`CertHolderID` 
                LEFT JOIN `ktv_first_buyer` h 
                     ON h.`FirstBuyerID` = e.`FirstBuyerID` 
                LEFT JOIN `ktv_program_partner` i 
                     ON i.`PartnerID` = h.`FirstBuyerPartnerID` 
                LEFT JOIN `ktv_supplychain_org` j 
                     ON j.`SupplychainID` = g.`SupplychainID` 
                LEFT JOIN `ktv_traders` k 
                     ON k.`TraderID` = j.`OrgID` 
                     AND j.`OrgType` = 'trader' 
                LEFT JOIN `ktv_cooperatives` l 
                     ON l.`CoopID` = j.`OrgID` 
                     AND j.`OrgType` = 'koperasi' 
                LEFT JOIN `ktv_village` m 
                     ON m.`VillageID` = d.`VillageID` 
                LEFT JOIN `ktv_subdistrict` n 
                     ON n.`SubDistrictID` = m.`SubDistrictID` 
                LEFT JOIN `ktv_district` o 
                     ON o.`DistrictID` = n.`DistrictID` 
                LEFT JOIN `ktv_province` p 
                     ON p.`ProvinceID` = o.`ProvinceID` 
                LEFT JOIN `ktv_cpg` q 
                     ON q.`CPGid` = d.`CPGid` 
                LEFT JOIN `ktv_village` r 
                     ON r.`VillageID` = c.`VillageID` 
                LEFT JOIN `ktv_subdistrict` s 
                     ON s.`SubDistrictID` = r.`SubDistrictID` 
                LEFT JOIN `ktv_district` t 
                     ON t.`DistrictID` = s.`DistrictID` 
                LEFT JOIN `ktv_province` u 
                     ON u.`ProvinceID` = t.`ProvinceID` 
                LEFT JOIN `ktv_cpg` v 
                     ON v.`CPGid` = c.`CPGid` 
                LEFT JOIN `ktv_staffs` `x` 
                     ON x.`StaffID` = b.`PICStaffID` 
                LEFT JOIN `ktv_persons` `y` 
                     ON y.`PersonID` = x.`PersonID` 
                LEFT JOIN `ktv_first_buyer_program` z 
                     ON z.`ProgID` = e.`ProgID` 
           WHERE e.`IMSID` IN (207) 
                AND a.`ParticipateInSocializationStatus` = 1 
                AND a.`ObjType` IN ('Applicant', 'Existing Farmer') 
                AND b.`StatusCode` = 'active' 
           GROUP BY ParticipantID";
		$query = $this->db->query($sql); //detailSOC
		if ($query) {
			$results['detailSOC'] = "detailSOC updated.";
		} else {
			$results['detailSOC'] .= "||detailSOC Failed to update";
		}
		return $results;
	}
        
        function insert_detail_SOC_Wave3() {
        $sql = "DELETE FROM ktv_jbcocoa_summary_socialization WHERE DateGenerated = DATE(NOW()) AND ProgID IN (12,14)";
        $query = $this->db->query($sql);
        
        $sql = "INSERT INTO ktv_jbcocoa_summary_socialization (
DateGenerated,
CertHolderID,
DistrictID,
IMSID,
ProgID,
Program,
CertificateHolders,
Responsible,
ParticipantID,
ParticipantName,
Gender,
FarmerGroup,
ParticipantType,
Province,
District,
SubDistrict,
Village,
EventName,
EventDate,
ParticipateInSocializationStatus,
DateLocked,
PIC,
ClusterID
) 
SELECT
    now() AS DateGenerated,
    ch.CertHolderID,
    dis.DistrictID,
    sol.IMSID,
    bp.ProgID,
    bp.ProgramName,
    ch.CertHolderOrgName,
    ch.CertHolderResponsible,
    sol.DestObjID,
    sol.`Name`,
    sol.Gender,
    sol.FarmerGroup,
    sol.ObjType,
    sol.Province,
    sol.District,
    sol.SubDistrict,
    sol.Village,
    NULL AS EventName,
    sol.EventStart,
    ParticipateInSocializationStatus,
    DateGenerated AS DateLocked,
    NULL AS PIC,
    sol.ClusterID
FROM
    ktv_ims_soc_sel AS sol
LEFT JOIN ktv_district dis ON dis.District=sol.District
LEFT JOIN ktv_ims ims ON ims.IMSID=sol.IMSID
LEFT JOIN ktv_ims_master imm ON imm.IMSMasterID=ims.IMSMasterID
LEFT JOIN ktv_certification_holders ch ON ch.CertHolderID=imm.CertHolderID
LEFT JOIN ktv_first_buyer_program bp ON bp.ProgID=ims.ProgID
WHERE
    sol.IMSID IN (252,278) AND
    sol.SelectionStatus = 1;";
        $query = $this->db->query($sql); //detailSOC
        if ($query) {
            $results['detailSOC'] = "detailSOC Wave III updated.";
        } else {
            $results['detailSOC'] .= "||detailSOC Wave III  Failed to update";
        }
        return $results;
    }
	
	function insert_detail_TC1()
	{
		$sql = "DELETE FROM ktv_jbcocoa_summary_tc_year_two WHERE DateGenerated = DATE(NOW()) AND ProgID = 6";
		$query = $this->db->query($sql);
		$sql = "INSERT INTO `ktv_jbcocoa_summary_tc_year_two`
          (`DateGenerated`,
               `CertHolderID`,
               `DistrictID`,
               `IMSID`,
               ClusterID,
               `ProgID`,
               `ValidityStart`,
               `CertHolderOrgName`,
               `Location`,
               `ValidityEnd`,
               `ProgramYear`,
               `ProgramName`,
               `FarmerID`,
               `FarmerName`,
               `CPGId`,
               `GroupName`,
               `Village`,
               `SubDistrict`,
               `District`,
               `transid_farmer`,
               `FarmerDate`,
               `SupplyType`,
               `batchid_2`,
               `transid_2`,
               `date_2`,
               `supplyorgid_2`,
               `faktur_2`,
               `bruto_2`,
               `netto_farmer_2`,
               `bruto_farmer_2`,
               `netto_2`,
               `orgtype_2`,
               `name_2`,
               `batchnumber_2`,
               `destpo_2`,
               `status_2`,
               `deliverydate_2`,
               `BatchDateCH`
          )
          SELECT
               DATE(NOW()),
               i.CertHolderID,
               dt.DistrictID,
               a.IMSID,
               q.ClusterID,
               kfbp.ProgID,
               i.ValidityStart,
               kch.CertHolderOrgName,
               i.Location,
               i.ValidityEnd,
               kfbp.ProgramYear,
               kfbp.ProgramName,
               dt.FarmerID,
               dt.FarmerName,
               dt.CPGId,
               dt.GroupName,
               dt.Village,
               dt.SubDistrict,
               dt.District,
               dt.FarmerTransID,
               dt.FarmerDate,
               dt.SupplyType,
               dt.batchid_2,
               dt.transid_2,
               dt.date_2,
               dt.supplyorgid_2,
               dt.faktur_2,
               dt.bruto_2,
               dt.netto_farmer_2,
               dt.bruto_farmer_2,
               dt.netto_2,
               dt.orgtype_2,
               dt.name_2,
               dt.batchnumber_2,
               dt.destpo_2,
               dt.status_2,
               dt.deliverydate_2,
               dt.BatchDateCH
          FROM
               ktv_ims i 
          LEFT JOIN ktv_certification_holders kch ON kch.CertHolderID=i.CertHolderID
          LEFT JOIN ktv_first_buyer kfb ON kfb.FirstBuyerID=i.FirstBuyerID
          LEFT JOIN ktv_cocoa_certification_certified_farmer a ON a.IMSID=i.IMSID
          LEFT JOIN ktv_cpg b ON a.`CPGid` = b.`CPGid`
          LEFT JOIN (
               SELECT
                    FarmerID, 
                    FarmerName,
                    rpt.CPGId,
                    GroupName,
                    Village,
                    SubDistrict,
                    DistrictID,
                    District,
                    Province,
                    SUBSTR(date_1,1,10) FarmerDate,
                    transid_1 FarmerTransID,
                    transcert_1 SupplyType,
                    IFNULL(imsid_1, imsid_2) IMSID,
                    
                    IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                    batchid_1) batchid_1,
                    IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                    transid_1) transid_1,
                    IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                    date_1) date_1,
                    IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                    supplyorgid_1) supplyorgid_1,
                    IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                    faktur_1) faktur_1,
                    IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                    bruto_1) bruto_1,
                    IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                    netto_1) netto_1,
                    IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                    orgtype_1) orgtype_1,
                    IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                    name_1) name_1,
                    IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                    batchnumber_1) batchnumber_1,
                    IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                    destpo_1) destpo_1,
                    IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                    status_1) status_1,
                    IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                    deliverydate_1) deliverydate_1,
                    
                    IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,batchid_1,
                    IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, batchid_2)) batchid_2,
                    IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,transid_1,
                    IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, transid_2)) transid_2,
                    IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,date_1,
                    IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, date_2)) date_2,
                    IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,supplyorgid_1,
                    IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, supplyorgid_2)) supplyorgid_2,
                    IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,faktur_1,
                    IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, faktur_2)) faktur_2,
                    IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,bruto_1,
                    IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, bruto_2)) bruto_2,
                    IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,netto_1,
                    IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, netto_farmer_2)) netto_farmer_2,
                    IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,bruto_1,
                    IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, bruto_farmer_2)) bruto_farmer_2,
                    IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,netto_1,
                    IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, netto_2)) netto_2,
                    IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,orgtype_1,
                    IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, orgtype_2)) orgtype_2,
                    IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,name_1,
                    IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, name_2)) name_2,
                    IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,batchnumber_1,
                    IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, batchnumber_2)) batchnumber_2,
                    IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,destpo_1,
                    IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, destpo_2)) destpo_2,
                    IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,status_1,
                    IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, status_2)) status_2,
                    IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,deliverydate_1,
                    IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, deliverydate_2)) deliverydate_2,

                    IF(orgtype_2='Gudang', supplyorgid_2, supplyorgid_3) supplyorgid_3,
                    IF(orgtype_2='Gudang', IFNULL(date_2, deliverydate_1), IFNULL(date_3, deliverydate_2)) date_3,
                    IF(orgtype_2='Gudang', transid_2, transid_3) transid_3,
                    IF(orgtype_2='Gudang', batchid_2, batchid_3) batchid_3,
                    IF(orgtype_2='Gudang', orgid_2, orgid_3) orgid_3,
                    IF(orgtype_2='Gudang', name_2, name_3) name_3,
                    IF(orgtype_2='Gudang', faktur_2, faktur_3) faktur_3,
                    IF(orgtype_2='Gudang', bruto_2, bruto_3) bruto_3,
                    IF(orgtype_2='Gudang', netto_2, netto_3) netto_3,
                    IF(orgtype_2='Gudang', 
                              netto_1,
                              netto_farmer_2
                    ) bruto_farmer_3,
                    IF(orgtype_2='Gudang', 
                              netto_1,
                              netto_farmer_2
                    ) netto_farmer_3,
                    IF(orgtype_2='Gudang', destpo_2, destpo_3) destpo_3,
                    IF(orgtype_2='Gudang', status_2, status_3) status_3,
                    IFNULL(sb2.SupplyBatchDate, sb1.SupplyBatchDate) BatchDateCH
          FROM
                    rpt_tc_trans_detail rpt
                    LEFT JOIN view_supplychain_org dest ON dest.SupplychainID=supplydestorgid_1
                    LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchID=IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,batchid_1,IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, batchid_2))
                    LEFT JOIN ktv_supplychain_batch sb1 ON sb1.SupplyBatchID=IF(transcert_1='utz' && (orgtype_2='Gudang' || dest.OrgType='Gudang'),NULL,batchid_1)
          WHERE partnerid_1=22 AND rpt.transcert_1='utz'
          ) dt ON dt.FarmerID=a.FarmerID AND dt.FarmerDate BETWEEN i.ValidityStart AND i.ValidityEnd
          LEFT JOIN ktv_supplychain_transaction wht ON wht.SupplyTransID=dt.transid_3
          LEFT JOIN ktv_first_buyer_program kfbp ON kfbp.ProgID=i.ProgID
		LEFT JOIN ktv_cocoa_certification_pre_afl q ON q.IMSID = a.IMSID AND q.FarmerID = dt.FarmerID 
          WHERE
          kfb.FirstBuyerPartnerID=22 AND i.StatusCode='active' AND i.`CertEventStatus`=2 AND dt.FarmerID IS NOT NULL AND dt.status_2 IN ('Sent', 'Delivered')
          AND i.ProgID = 6";
		$query = $this->db->query($sql); //detailTC1
		if ($query) {
			$results['detailTC1'] = "detailTC1 updated.";
		} else {
			$results['detailTC1'] .= "||detailTC1 Failed to update";
		}
		return $results;
	}
	
	function insert_detail_TC2()
	{
		$sql = "DELETE FROM ktv_jbcocoa_summary_tc_year_two WHERE DateGenerated = DATE(NOW()) AND ProgID = 7";
		$query = $this->db->query($sql);
		$sql = "INSERT INTO `ktv_jbcocoa_summary_tc_year_two`
               (`DateGenerated`,
                    `CertHolderID`,
                    `DistrictID`,
                    `IMSID`,
                    ClusterID,
                    `ProgID`,
                    `ValidityStart`,
                    `CertHolderOrgName`,
                    `Location`,
                    `ValidityEnd`,
                    `ProgramYear`,
                    `ProgramName`,
                    `FarmerID`,
                    `FarmerName`,
                    `CPGId`,
                    `GroupName`,
                    `Village`,
                    `SubDistrict`,
                    `District`,
                    `transid_farmer`,
                    `FarmerDate`,
                    `SupplyType`,
                    `batchid_2`,
                    `transid_2`,
                    `date_2`,
                    `supplyorgid_2`,
                    `faktur_2`,
                    `bruto_2`,
                    `netto_farmer_2`,
                    `bruto_farmer_2`,
                    `netto_2`,
                    `orgtype_2`,
                    `name_2`,
                    `batchnumber_2`,
                    `destpo_2`,
                    `status_2`,
                    `deliverydate_2`,
                    `BatchDateCH`
               )
               SELECT
                    DATE(NOW()),
                    i.CertHolderID,
                    dt.DistrictID,
                    a.IMSID,
                    q.ClusterID,
                    kfbp.ProgID,
                    i.ValidityStart,
                    kch.CertHolderOrgName,
                    i.Location,
                    i.ValidityEnd,
                    kfbp.ProgramYear,
                    kfbp.ProgramName,
                    dt.FarmerID,
                    dt.FarmerName,
                    dt.CPGId,
                    dt.GroupName,
                    dt.Village,
                    dt.SubDistrict,
                    dt.District,
                    dt.FarmerTransID,
                    dt.FarmerDate,
                    dt.SupplyType,
                    dt.batchid_2,
                    dt.transid_2,
                    dt.date_2,
                    dt.supplyorgid_2,
                    dt.faktur_2,
                    dt.bruto_2,
                    dt.netto_farmer_2,
                    dt.bruto_farmer_2,
                    dt.netto_2,
                    dt.orgtype_2,
                    dt.name_2,
                    dt.batchnumber_2,
                    dt.destpo_2,
                    dt.status_2,
                    dt.deliverydate_2,
                    dt.BatchDateCH
               FROM
                    ktv_ims i 
               LEFT JOIN ktv_certification_holders kch ON kch.CertHolderID=i.CertHolderID
               LEFT JOIN ktv_first_buyer kfb ON kfb.FirstBuyerID=i.FirstBuyerID
               LEFT JOIN ktv_cocoa_certification_certified_farmer a ON a.IMSID=i.IMSID
               LEFT JOIN ktv_cpg b ON a.`CPGid` = b.`CPGid`
               LEFT JOIN (
                    SELECT
                         FarmerID, 
                         FarmerName,
                         rpt.CPGId,
                         GroupName,
                         Village,
                         SubDistrict,
                         DistrictID,
                         District,
                         Province,
                         SUBSTR(date_1,1,10) FarmerDate,
                         transid_1 FarmerTransID,
                         transcert_1 SupplyType,
                         IFNULL(imsid_1, imsid_2) IMSID,
                         
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                         batchid_1) batchid_1,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                         transid_1) transid_1,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                         date_1) date_1,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                         supplyorgid_1) supplyorgid_1,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                         faktur_1) faktur_1,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                         bruto_1) bruto_1,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                         netto_1) netto_1,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                         orgtype_1) orgtype_1,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                         name_1) name_1,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                         batchnumber_1) batchnumber_1,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                         destpo_1) destpo_1,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                         status_1) status_1,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,NULL,
                         deliverydate_1) deliverydate_1,
                         
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,batchid_1,
                         IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, batchid_2)) batchid_2,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,transid_1,
                         IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, transid_2)) transid_2,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,date_1,
                         IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, date_2)) date_2,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,supplyorgid_1,
                         IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, supplyorgid_2)) supplyorgid_2,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,faktur_1,
                         IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, faktur_2)) faktur_2,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,bruto_1,
                         IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, bruto_2)) bruto_2,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,netto_1,
                         IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, netto_farmer_2)) netto_farmer_2,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,bruto_1,
                         IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, bruto_farmer_2)) bruto_farmer_2,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,netto_1,
                         IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, netto_2)) netto_2,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,orgtype_1,
                         IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, orgtype_2)) orgtype_2,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,name_1,
                         IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, name_2)) name_2,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,batchnumber_1,
                         IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, batchnumber_2)) batchnumber_2,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,destpo_1,
                         IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, destpo_2)) destpo_2,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,status_1,
                         IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, status_2)) status_2,
                         IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,deliverydate_1,
                         IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, deliverydate_2)) deliverydate_2,

                         IF(orgtype_2='Gudang', supplyorgid_2, supplyorgid_3) supplyorgid_3,
                         IF(orgtype_2='Gudang', IFNULL(date_2, deliverydate_1), IFNULL(date_3, deliverydate_2)) date_3,
                         IF(orgtype_2='Gudang', transid_2, transid_3) transid_3,
                         IF(orgtype_2='Gudang', batchid_2, batchid_3) batchid_3,
                         IF(orgtype_2='Gudang', orgid_2, orgid_3) orgid_3,
                         IF(orgtype_2='Gudang', name_2, name_3) name_3,
                         IF(orgtype_2='Gudang', faktur_2, faktur_3) faktur_3,
                         IF(orgtype_2='Gudang', bruto_2, bruto_3) bruto_3,
                         IF(orgtype_2='Gudang', netto_2, netto_3) netto_3,
                         /*IF(orgtype_2='Gudang', bruto_farmer_2, bruto_farmer_3) bruto_farmer_3,
                         IF(orgtype_2='Gudang', netto_farmer_2, netto_farmer_3) netto_farmer_3,*/
                         IF(orgtype_2='Gudang', 
                                   netto_1,
                                   netto_farmer_2
                         ) bruto_farmer_3,
                         IF(orgtype_2='Gudang', 
                                   netto_1,
                                   netto_farmer_2
                         ) netto_farmer_3,
                         IF(orgtype_2='Gudang', destpo_2, destpo_3) destpo_3,
                         IF(orgtype_2='Gudang', status_2, status_3) status_3,
                         IFNULL(sb2.SupplyBatchDate, sb1.SupplyBatchDate) BatchDateCH
               FROM
                         rpt_tc_trans_detail rpt
                         LEFT JOIN view_supplychain_org dest ON dest.SupplychainID=supplydestorgid_1
                         LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchID=IF(transcert_1='utz' && (orgtype_2='Gudang' ||  dest.OrgType='Gudang' ) && ch_1 IS NOT NULL,batchid_1,IF(transcert_1='cocoalife' || (orgtype_2='Gudang' &&  ch_1 IS NULL && ch_2 IS NULL), NULL, batchid_2))
                         LEFT JOIN ktv_supplychain_batch sb1 ON sb1.SupplyBatchID=IF(transcert_1='utz' && (orgtype_2='Gudang' || dest.OrgType='Gudang'),NULL,batchid_1)
               WHERE partnerid_1=22 AND rpt.transcert_1='utz'
               ) dt ON dt.FarmerID=a.FarmerID AND dt.FarmerDate BETWEEN i.ValidityStart AND i.ValidityEnd
               LEFT JOIN ktv_supplychain_transaction wht ON wht.SupplyTransID=dt.transid_3
               LEFT JOIN ktv_first_buyer_program kfbp ON kfbp.ProgID=i.ProgID
               LEFT JOIN ktv_cocoa_certification_pre_afl q ON q.IMSID = a.IMSID AND q.FarmerID = a.FarmerID 
               WHERE
               kfb.FirstBuyerPartnerID=22 AND i.StatusCode='active' AND i.`CertEventStatus`=2 AND dt.FarmerID IS NOT NULL AND dt.status_2 IN ('Sent', 'Delivered')
               AND i.ProgID = 7";
		$query = $this->db->query($sql); //detailTC2
		if ($query) {
			$results['detailTC2'] = "detailTC2 updated.";
		} else {
			$results['detailTC2'] .= "||detailTC2 Failed to update";
		}
		return $results;
     }
     
     function insert_detail_progress_garden()
	{
		$sql = "DELETE FROM ktv_jbcocoa_summary_progress_garden WHERE DateGenerated = DATE(NOW())";
		$query = $this->db->query($sql);
		$sql = "INSERT INTO ktv_jbcocoa_summary_progress_garden (
               DateGenerated, 
               ProgID, 
               CertHolderID, 
               DistrictID, 
               Program, 
               CertificateHolders, 
               Responsible, 
               IMSID, 
               ClusterID,
               FarmerID, 
               FarmerName, 
               GroupID, 
               FarmerGroup, 
               Province, 
               District, 
               SubDistrict, 
               Village, 
               GardenNr, 
               SurveyNr, 
               DateCollection, 
               Longitude, 
               Latitude, 
               HaGarden, 
               DateVisited
           ) 
           SELECT DATE(NOW()),
               g.ProgID, e.CertHolderID, j.DistrictID,
               CONCAT(g.`ProgramName`, ' (', g.`ProgramYear`, ')') AS Program,
               IF(m.OrgType='trader',n.`Company`,IF(m.OrgType='koperasi',o.CoopName,'-')) AS CertificateHolders,
               IF(m.OrgType='trader',n.TraderName,IF(m.OrgType='koperasi',o.CoopName,'-')) AS Responsible,
               c.`IMSID`,
               a.ClusterID,
               b.`FarmerID`,
               b.`FarmerName`,
               b.`CPGid` AS GroupID,
               l.`GroupName` AS FarmerGroup,
               k.`Province`,
               j.`District`,
               i.`SubDistrict`,
               h.`Village`,
               q.`GardenNr`,
               q.SurveyNr,
               IF(YEAR(q.`DateCollection`)=2018,q.DateCollection,q.DateUpdated) AS DateCollection,
               IF(q.Longitude IS NULL OR q.`Longitude`='0.000000',r.LongitudeExist,q.`Longitude`) AS Longitude,
               IF(q.Latitude IS NULL OR q.`Latitude`='0.000000',r.LatitudeExist,q.`Latitude`) AS Latitude,
               IFNULL(q.GardenHaUncertified,'0.00') AS HaGarden,
               IFNULL(q.`DateUpdated`, '0000-00-00') AS DateVisited 
          FROM
               ktv_cocoa_certification_pre_afl a
               INNER JOIN `ktv_cocoa_farmer` b ON b.`FarmerID` = a.`FarmerID` 
               JOIN ktv_cocoa_farmer_garden q ON q.`FarmerID`=b.`FarmerID`
               LEFT JOIN (SELECT FarmerID, GardenNr, MAX(Longitude) LongitudeExist, MAX(Latitude) LatitudeExist FROM ktv_cocoa_farmer_garden GROUP BY
               FarmerID, GardenNr) r ON r.FarmerID=q.`FarmerID` AND r.GardenNr=q.`GardenNr` 
               LEFT JOIN `ktv_ims` c ON c.`IMSID`=a.`IMSID`
               LEFT JOIN `ktv_ims_master` d ON d.`IMSMasterID`=c.`IMSMasterID`
               LEFT JOIN `ktv_certification_holders` e ON e.`CertHolderID`=d.`CertHolderID`
               LEFT JOIN `ktv_first_buyer` f ON f.`FirstBuyerID`=c.`FirstBuyerID`
               LEFT JOIN `ktv_first_buyer_program` g ON g.`ProgID`=c.`ProgID`
               LEFT JOIN `ktv_village` h ON h.`VillageID`=b.`VillageID`
               LEFT JOIN `ktv_subdistrict` i ON i.`SubDistrictID`=h.`SubDistrictID`
               LEFT JOIN `ktv_district` j ON j.`DistrictID`=i.`DistrictID`
               LEFT JOIN `ktv_province` k ON k.`ProvinceID`=j.`ProvinceID` 
               LEFT JOIN `ktv_cpg` l ON l.`CPGid`=b.`CPGid`
               LEFT JOIN `ktv_supplychain_org` m ON m.`SupplychainID`=e.`SupplychainID`
               LEFT JOIN `ktv_traders` n ON n.`TraderID`=m.`OrgID` AND m.`OrgType`='trader'
               LEFT JOIN `ktv_cooperatives` o ON o.`CoopID`=m.`OrgID` AND m.`OrgType`='koperasi'
          WHERE a.`StatusCode` = 'active' AND b.`StatusCode` = 'active' 
               AND a.StatusAudit = '1' AND q.SurveyNr=14
               AND c.`IMSID` IN (207)
               AND (DATE(q.`DateUpdated`) >= DATE_FORMAT(c.`CertEventDate` - INTERVAL 6 MONTH, '%Y-%m-%d'))
          GROUP BY q.`FarmerID`, q.`GardenNr`, q.`SurveyNr`
           ";
		$query = $this->db->query($sql); //detailProgressGarden
		if ($query) {
			$results['detailProgressGarden'] = "ProgressGarden updated.";
		} else {
			$results['detailProgressGarden'] .= "||ProgressGarden Failed to update";
          }
          return $results;
     }

     function insert_detail_progress_polygon()
	{
		$sql = "DELETE FROM ktv_jbcocoa_summary_progress_polygon WHERE DateGenerated = DATE(NOW())";
		$query = $this->db->query($sql);
		$sql = "INSERT INTO ktv_jbcocoa_summary_progress_polygon (
               DateGenerated, 
               ProgID, 
               CertHolderID, 
               DistrictID, 
               Program, 
               CertificateHolders, 
               Responsible, 
               IMSID, 
               ClusterID,
               FarmerID, 
               FarmerName, 
               GroupID, 
               FarmerGroup, 
               Province, 
               District, 
               SubDistrict, 
               Village, 
               GardenNr, 
               SurveyNr, 
               DateCollection, 
               Longitude, 
               Latitude, 
               PolygonAvailability, 
               HaSurvey, 
               HaPolygon, 
               DateVisited
           ) 
           SELECT 
               DATE( NOW( ) ),g.ProgID, e.CertHolderID, j.DistrictID,
               CONCAT(g.`ProgramName`, ' (', g.`ProgramYear`, ')') AS Program,
               IF(m.OrgType='trader',n.`Company`,IF(m.OrgType='koperasi',o.CoopName,'-')) AS CertificateHolders,
               IF(m.OrgType='trader',n.TraderName,IF(m.OrgType='koperasi',o.CoopName,'-')) AS Responsible,
               c.`IMSID`,
               a.ClusterID,
               b.`FarmerID`,
               b.`FarmerName`,
               b.`CPGid` AS GroupID,
               l.`GroupName` AS FarmerGroup,
               k.`Province`,
               j.`District`,
               i.`SubDistrict`,
               h.`Village`,
               q.`GardenNr`,
               q.SurveyNr,
               IF(YEAR(q.`DateCollection`)=2018,q.DateCollection,q.DateUpdated) AS DateCollection,
               IF(q.Longitude IS NULL OR q.`Longitude`='0.000000',r.LongitudeExist,q.`Longitude`) AS Longitude,
               IF(q.Latitude IS NULL OR q.`Latitude`='0.000000',r.LatitudeExist,q.`Latitude`) AS Latitude,
               IF(s.FarmerID IS NOT NULL, 'Exist','Not Exist') AS PolygonAvailability,
               IFNULL(q.GardenHaUncertified,'0.00') AS HaSurvey,
               IFNULL(q.GardenHaPolygon,'0.00') AS HaPolygon,
               IFNULL(q.`DateUpdated`, '0000-00-00') AS DateVisited 
          FROM
               ktv_cocoa_certification_pre_afl a
               INNER JOIN `ktv_cocoa_farmer` b ON b.`FarmerID` = a.`FarmerID` 
               JOIN ktv_cocoa_farmer_garden q ON q.`FarmerID`=b.`FarmerID`
               JOIN (SELECT FarmerID, GardenNr, MAX(Longitude) LongitudeExist, MAX(Latitude) LatitudeExist FROM ktv_cocoa_farmer_garden GROUP BY
               FarmerID, GardenNr) r ON r.FarmerID=q.`FarmerID` AND r.GardenNr=q.`GardenNr` 
               JOIN (SELECT FarmerID, GardenNr, MAX(SurveyNr) AS LatestSurvey, MAX(Revision) AS LatestRevision FROM ktv_cocoa_farmer_garden_area 
               WHERE `Status`='verified' GROUP BY FarmerID, GardenNr) s ON s.`FarmerID`=q.`FarmerID` AND s.`GardenNr`=q.`GardenNr`
               LEFT JOIN `ktv_ims` c ON c.`IMSID`=a.`IMSID`
               LEFT JOIN `ktv_ims_master` d ON d.`IMSMasterID`=c.`IMSMasterID`
               LEFT JOIN `ktv_certification_holders` e ON e.`CertHolderID`=d.`CertHolderID`
               LEFT JOIN `ktv_first_buyer` f ON f.`FirstBuyerID`=c.`FirstBuyerID`
               LEFT JOIN `ktv_first_buyer_program` g ON g.`ProgID`=c.`ProgID`
               LEFT JOIN `ktv_village` h ON h.`VillageID`=b.`VillageID`
               LEFT JOIN `ktv_subdistrict` i ON i.`SubDistrictID`=h.`SubDistrictID`
               LEFT JOIN `ktv_district` j ON j.`DistrictID`=i.`DistrictID`
               LEFT JOIN `ktv_province` k ON k.`ProvinceID`=j.`ProvinceID` 
               LEFT JOIN `ktv_cpg` l ON l.`CPGid`=b.`CPGid`
               LEFT JOIN `ktv_supplychain_org` m ON m.`SupplychainID`=e.`SupplychainID`
               LEFT JOIN `ktv_traders` n ON n.`TraderID`=m.`OrgID` AND m.`OrgType`='trader'
               LEFT JOIN `ktv_cooperatives` o ON o.`CoopID`=m.`OrgID` AND m.`OrgType`='koperasi'
          WHERE a.`StatusCode` = 'active' AND b.`StatusCode` = 'active' 
               AND a.StatusAudit = '1' AND q.SurveyNr=14
               AND c.`IMSID` IN (207)
               AND (DATE(q.`DateUpdated`) >= DATE_FORMAT(c.`CertEventDate` - INTERVAL 6 MONTH, '%Y-%m-%d'))
          GROUP BY q.`FarmerID`, q.`GardenNr`, q.`SurveyNr`
           ";
		$query = $this->db->query($sql); //detailProgressPolygon
		if ($query) {
			$results['detailProgressPolygon'] = "ProgressPolygon updated.";
		} else {
			$results['detailProgressPolygon'] .= "||ProgressPolygon Failed to update";
          }
          return $results;
     }

     function insert_detail_progress_post_harvest()
	{
		$sql = "DELETE FROM ktv_jbcocoa_summary_progress_post_harvest WHERE DateGenerated = DATE(NOW())";
		$query = $this->db->query($sql);
		$sql = "INSERT INTO ktv_jbcocoa_summary_progress_post_harvest (
               DateGenerated, 
               ProgID, 
               CertHolderID, 
               DistrictID, 
               Program, 
               CertificateHolders, 
               Responsible, 
               IMSID, 
               ClusterID,
               FarmerID, 
               FarmerName,
               Gender, 
               HandPhone, 
               DateOfBirth,
               GroupID, 
               FarmerGroup, 
               Province, 
               District, 
               SubDistrict, 
               Village, 
               SurveyNr, 
               DateCollection, 
               DateVisited
           ) 
           SELECT 
               DATE( NOW( ) ),g.ProgID, e.CertHolderID, j.DistrictID,
               CONCAT(g.`ProgramName`, ' (', g.`ProgramYear`, ')') AS Program,
               IF(m.OrgType='trader',n.`Company`,IF(m.OrgType='koperasi',o.CoopName,'-')) AS CertificateHolders,
               IF(m.OrgType='trader',n.TraderName,IF(m.OrgType='koperasi',o.CoopName,'-')) AS Responsible,
               c.`IMSID`,
               a.ClusterID,
               b.`FarmerID`,
               b.`FarmerName`,
               IF(b.Gender=1,'Male',IF(b.Gender=2,'Female','')) AS Gender,
               b.`HandPhone`,
               b.`Birthdate` AS DateOfBirth,
               b.`CPGid` AS GroupID,
               l.`GroupName` AS FarmerGroup,
               k.`Province`,
               j.`District`,
               i.`SubDistrict`,
               h.`Village`,
               q.`SurveyNr`,
               IF(YEAR(q.`DateCollection`)=2018,q.DateCollection,q.DateUpdated) AS DateCollection,
               IFNULL(q.`DateUpdated`, '0000-00-00') AS DateVisited 
          FROM
               ktv_cocoa_certification_pre_afl a
               INNER JOIN `ktv_cocoa_farmer` b ON b.`FarmerID` = a.`FarmerID` 
               JOIN ktv_cocoa_farmer_post_harvest q ON q.`FarmerID`=b.`FarmerID`
               LEFT JOIN `ktv_ims` c ON c.`IMSID`=a.`IMSID`
               LEFT JOIN `ktv_ims_master` d ON d.`IMSMasterID`=c.`IMSMasterID`
               LEFT JOIN `ktv_certification_holders` e ON e.`CertHolderID`=d.`CertHolderID`
               LEFT JOIN `ktv_first_buyer` f ON f.`FirstBuyerID`=c.`FirstBuyerID`
               LEFT JOIN `ktv_first_buyer_program` g ON g.`ProgID`=c.`ProgID`
               LEFT JOIN `ktv_village` h ON h.`VillageID`=b.`VillageID`
               LEFT JOIN `ktv_subdistrict` i ON i.`SubDistrictID`=h.`SubDistrictID`
               LEFT JOIN `ktv_district` j ON j.`DistrictID`=i.`DistrictID`
               LEFT JOIN `ktv_province` k ON k.`ProvinceID`=j.`ProvinceID` 
               LEFT JOIN `ktv_cpg` l ON l.`CPGid`=b.`CPGid`
               LEFT JOIN `ktv_supplychain_org` m ON m.`SupplychainID`=e.`SupplychainID`
               LEFT JOIN `ktv_traders` n ON n.`TraderID`=m.`OrgID` AND m.`OrgType`='trader'
               LEFT JOIN `ktv_cooperatives` o ON o.`CoopID`=m.`OrgID` AND m.`OrgType`='koperasi'
          WHERE a.`StatusCode` = 'active' AND b.`StatusCode` = 'active' 
               AND a.StatusAudit = '1' AND q.SurveyNr=14
               AND c.`IMSID` IN (207)
               AND (DATE(q.`DateUpdated`) >= DATE_FORMAT(c.`CertEventDate` - INTERVAL 6 MONTH, '%Y-%m-%d'))
          GROUP BY a.`FarmerID`
           ";
		$query = $this->db->query($sql); //detailProgressPostHarvest
		if ($query) {
			$results['detailProgressPostHarvest'] = "ProgressPostHarvest updated.";
		} else {
			$results['detailProgressPostHarvest'] .= "||ProgressPostHarvest Failed to update";
          }
          return $results;
     }

     function insert_detail_progress_ppi()
	{
		$sql = "DELETE FROM ktv_jbcocoa_summary_progress_ppi WHERE DateGenerated = DATE(NOW())";
		$query = $this->db->query($sql);
		$sql = "INSERT INTO ktv_jbcocoa_summary_progress_ppi (
               DateGenerated, 
               ProgID, 
               CertHolderID, 
               DistrictID, 
               Program, 
               CertificateHolders, 
               Responsible, 
               IMSID, 
               ClusterID,
               FarmerID, 
               FarmerName, 
               Gender, 
               HandPhone, 
               DateOfBirth, 
               GroupID, 
               FarmerGroup, 
               Province, 
               District, 
               SubDistrict, 
               Village, 
               SurveyNr, 
               DateCollection, 
               DateVisited
           ) 
           SELECT 
               DATE( NOW( ) ),g.ProgID, e.CertHolderID, j.DistrictID,
               CONCAT(g.`ProgramName`, ' (', g.`ProgramYear`, ')') AS Program,
               IF(m.OrgType='trader',n.`Company`,IF(m.OrgType='koperasi',o.CoopName,'-')) AS CertificateHolders,
               IF(m.OrgType='trader',n.TraderName,IF(m.OrgType='koperasi',o.CoopName,'-')) AS Responsible,
               c.`IMSID`,
               a.ClusterID,
               b.`FarmerID`,
               b.`FarmerName`,
               IF(b.Gender=1,'Male',IF(b.Gender=2,'Female','')) AS Gender,
               b.`HandPhone`,
               b.`Birthdate` AS DateOfBirth,
               b.`CPGid` AS GroupID,
               l.`GroupName` AS FarmerGroup,
               k.`Province`,
               j.`District`,
               i.`SubDistrict`,
               h.`Village`,
               q.`SurveyNr`,
               IF(YEAR(q.`InterviewDate`)=2018,q.InterviewDate,q.DateUpdated) AS DateCollection,
               IFNULL(q.`DateUpdated`, '0000-00-00') AS DateVisited 
          FROM
               ktv_cocoa_certification_pre_afl a
               INNER JOIN `ktv_cocoa_farmer` b ON b.`FarmerID` = a.`FarmerID` 
               JOIN ktv_ppiscore2012 q ON q.`FarmerID`=b.`FarmerID`
               LEFT JOIN `ktv_ims` c ON c.`IMSID`=a.`IMSID`
               LEFT JOIN `ktv_ims_master` d ON d.`IMSMasterID`=c.`IMSMasterID`
               LEFT JOIN `ktv_certification_holders` e ON e.`CertHolderID`=d.`CertHolderID`
               LEFT JOIN `ktv_first_buyer` f ON f.`FirstBuyerID`=c.`FirstBuyerID`
               LEFT JOIN `ktv_first_buyer_program` g ON g.`ProgID`=c.`ProgID`
               LEFT JOIN `ktv_village` h ON h.`VillageID`=b.`VillageID`
               LEFT JOIN `ktv_subdistrict` i ON i.`SubDistrictID`=h.`SubDistrictID`
               LEFT JOIN `ktv_district` j ON j.`DistrictID`=i.`DistrictID`
               LEFT JOIN `ktv_province` k ON k.`ProvinceID`=j.`ProvinceID` 
               LEFT JOIN `ktv_cpg` l ON l.`CPGid`=b.`CPGid`
               LEFT JOIN `ktv_supplychain_org` m ON m.`SupplychainID`=e.`SupplychainID`
               LEFT JOIN `ktv_traders` n ON n.`TraderID`=m.`OrgID` AND m.`OrgType`='trader'
               LEFT JOIN `ktv_cooperatives` o ON o.`CoopID`=m.`OrgID` AND m.`OrgType`='koperasi'
          WHERE a.`StatusCode` = 'active' AND b.`StatusCode` = 'active' 
               AND a.StatusAudit = '1' AND q.SurveyNr=14
               AND c.`IMSID` IN (207)
               AND (DATE(q.`DateUpdated`) >= DATE_FORMAT(c.`CertEventDate` - INTERVAL 6 MONTH, '%Y-%m-%d'))
          GROUP BY a.`FarmerID`
           ";
		$query = $this->db->query($sql); //detailProgressPPI
		if ($query) {
			$results['detailProgressPPI'] = "ProgressPPI updated.";
		} else {
			$results['detailProgressPPI'] .= "||ProgressPPI Failed to update";
          }
          return $results;
          // if ($results) {
		// 	$tmpresults['success'] = true;
		// 	$tmpresults['message'] = $results;
		// 	$tmpresults['step'] = "test";
		// } else {
		// 	$tmpresults['success'] = false;
		// 	$tmpresults['message'] = "Failed to update record";
		// 	$tmpresults['step'] = "";
		// }
		// return $tmpresults;
     }
     
     function insert_detail_progress_certification()
	{
		$sql = "DELETE FROM ktv_jbcocoa_summary_progress_certification WHERE DateGenerated = DATE(NOW())";
		$query = $this->db->query($sql);
		$sql = "INSERT INTO ktv_jbcocoa_summary_progress_certification (
               DateGenerated, 
               ProgID, 
               CertHolderID, 
               DistrictID, 
               Program, 
               CertificateHolders, 
               Responsible, 
               IMSID, 
               ClusterID,
               FarmerID, 
               FarmerName, 
               GroupID, 
               FarmerGroup, 
               Province, 
               District, 
               SubDistrict, 
               Village, 
               GardenNr, 
               SurveyNr, 
               CertificationProgram, 
               CertificationYear, 
               SelectionDate, 
               DateVisited
           ) 
           SELECT 
               DATE( NOW( ) ), 
               g.ProgID, e.CertHolderID, j.DistrictID,
               CONCAT(g.`ProgramName`, ' (', g.`ProgramYear`, ')') AS Program,
               IF(m.OrgType='trader',n.`Company`,IF(m.OrgType='koperasi',o.CoopName,'-')) AS CertificateHolders,
               IF(m.OrgType='trader',n.TraderName,IF(m.OrgType='koperasi',o.CoopName,'-')) AS Responsible,
               c.`IMSID`,
               a.ClusterID,
               b.`FarmerID`,
               b.`FarmerName`,
               b.`CPGid` AS GroupID,
               l.`GroupName` AS FarmerGroup,
               k.`Province`,
               j.`District`,
               i.`SubDistrict`,
               h.`Village`,
               r.`GardenNr`,
               r.SurveyNr,
               IF(r.`Certification`=1,'UTZ','') AS CertificationProgram,
               r.`Year` AS CertificationYear,
               r.`CandidateSelection` AS SelectionDate,
               IFNULL(r.`DateUpdated`, '0000-00-00') AS DateVisited 
          FROM
               ktv_cocoa_certification_pre_afl a
               INNER JOIN `ktv_cocoa_farmer` b ON b.`FarmerID` = a.`FarmerID` 
               JOIN ktv_cocoa_farmer_garden q ON q.`FarmerID`=b.`FarmerID`
               JOIN ktv_cocoa_certification r ON r.`FarmerID`=q.`FarmerID` AND r.`GardenNr`=q.`GardenNr` AND r.`SurveyNr`=q.`SurveyNr` AND r.`Certification`=1
               LEFT JOIN `ktv_ims` c ON c.`IMSID`=a.`IMSID`
               LEFT JOIN `ktv_ims_master` d ON d.`IMSMasterID`=c.`IMSMasterID`
               LEFT JOIN `ktv_certification_holders` e ON e.`CertHolderID`=d.`CertHolderID`
               LEFT JOIN `ktv_first_buyer` f ON f.`FirstBuyerID`=c.`FirstBuyerID`
               LEFT JOIN `ktv_first_buyer_program` g ON g.`ProgID`=c.`ProgID`
               LEFT JOIN `ktv_village` h ON h.`VillageID`=b.`VillageID`
               LEFT JOIN `ktv_subdistrict` i ON i.`SubDistrictID`=h.`SubDistrictID`
               LEFT JOIN `ktv_district` j ON j.`DistrictID`=i.`DistrictID`
               LEFT JOIN `ktv_province` k ON k.`ProvinceID`=j.`ProvinceID` 
               LEFT JOIN `ktv_cpg` l ON l.`CPGid`=b.`CPGid`
               LEFT JOIN `ktv_supplychain_org` m ON m.`SupplychainID`=e.`SupplychainID`
               LEFT JOIN `ktv_traders` n ON n.`TraderID`=m.`OrgID` AND m.`OrgType`='trader'
               LEFT JOIN `ktv_cooperatives` o ON o.`CoopID`=m.`OrgID` AND m.`OrgType`='koperasi'
          WHERE a.`StatusCode` = 'active' AND b.`StatusCode` = 'active' 
               AND a.StatusAudit = '1' AND r.SurveyNr=14
               AND c.`IMSID` IN (207)
               AND (DATE(r.`DateUpdated`) >= DATE_FORMAT(c.`CertEventDate` - INTERVAL 6 MONTH, '%Y-%m-%d'))
          GROUP BY r.`FarmerID`, r.`GardenNr`, r.`SurveyNr`, r.`Certification`
           ";
		$query = $this->db->query($sql); //detailProgressCertification
		if ($query) {
			$results['detailProgressCertification'] = "ProgressCertification updated.";
		} else {
			$results['detailProgressCertification'] .= "||ProgressCertification Failed to update";
          }
          return $results;
     }

     function insert_detail_progress_auditlog()
	{
		$sql = "DELETE FROM ktv_jbcocoa_summary_progress_auditlog WHERE DateGenerated = DATE(NOW())";
		$query = $this->db->query($sql);
		$sql = "INSERT INTO ktv_jbcocoa_summary_progress_auditlog (
               DateGenerated, 
               ProgID, 
               CertHolderID, 
               DistrictID, 
               Program, 
               CertificateHolders, 
               Responsible, 
               IMSID, 
               ClusterID,
               FarmerID, 
               FarmerName, 
               GroupID, 
               FarmerGroup, 
               Province, 
               District, 
               SubDistrict, 
               Village, 
               GardenNr, 
               SurveyNr, 
               CertificationProgram, 
               ICSDate, 
               StatusAudit, 
               CommentAudit, 
               DateRevisionAudit, 
               RecommendationAudit, 
               DateVisited
           ) 
           SELECT 
               DATE( NOW( ) ), 
               g.ProgID, e.CertHolderID, j.DistrictID,
               CONCAT(g.`ProgramName`, ' (', g.`ProgramYear`, ')') AS Program,
               IF(m.OrgType='trader',n.`Company`,IF(m.OrgType='koperasi',o.CoopName,'-')) AS CertificateHolders,
               IF(m.OrgType='trader',n.TraderName,IF(m.OrgType='koperasi',o.CoopName,'-')) AS Responsible,
               c.`IMSID`,
               a.ClusterID,
               b.`FarmerID`,
               b.`FarmerName`,
               b.`CPGid` AS GroupID,
               l.`GroupName` AS FarmerGroup,
               k.`Province`,
               j.`District`,
               i.`SubDistrict`,
               h.`Village`,
               r.`GardenNr`,
               r.SurveyNr,
               IF(r.`Certification`=1,'UTZ','') AS CertificationProgram,
               r.`ICSDate`,
               IF(r.`StatusAudit`=1,'Comply',IF(r.`StatusAudit`=2,'Not Comply',IF(r.`StatusAudit`=3,'Comply with Recommendation',''))) AS StatusAudit,
               r.`CommentAudit`,
               r.`DateRevisionAudit`,
               r.`RecommendationAudit`,
               IFNULL(r.`DateUpdated`, '0000-00-00') AS DateVisited 
          FROM
               ktv_cocoa_certification_pre_afl a
               INNER JOIN `ktv_cocoa_farmer` b ON b.`FarmerID` = a.`FarmerID` 
               JOIN ktv_cocoa_farmer_garden q ON q.`FarmerID`=b.`FarmerID`
               JOIN ktv_cocoa_certification_audit_log r ON r.`FarmerID`=q.`FarmerID` AND r.`GardenNr`=q.`GardenNr` AND r.`SurveyNr`=q.`SurveyNr` AND r.`Certification`=1
               LEFT JOIN `ktv_ims` c ON c.`IMSID`=a.`IMSID`
               LEFT JOIN `ktv_ims_master` d ON d.`IMSMasterID`=c.`IMSMasterID`
               LEFT JOIN `ktv_certification_holders` e ON e.`CertHolderID`=d.`CertHolderID`
               LEFT JOIN `ktv_first_buyer` f ON f.`FirstBuyerID`=c.`FirstBuyerID`
               LEFT JOIN `ktv_first_buyer_program` g ON g.`ProgID`=c.`ProgID`
               LEFT JOIN `ktv_village` h ON h.`VillageID`=b.`VillageID`
               LEFT JOIN `ktv_subdistrict` i ON i.`SubDistrictID`=h.`SubDistrictID`
               LEFT JOIN `ktv_district` j ON j.`DistrictID`=i.`DistrictID`
               LEFT JOIN `ktv_province` k ON k.`ProvinceID`=j.`ProvinceID` 
               LEFT JOIN `ktv_cpg` l ON l.`CPGid`=b.`CPGid`
               LEFT JOIN `ktv_supplychain_org` m ON m.`SupplychainID`=e.`SupplychainID`
               LEFT JOIN `ktv_traders` n ON n.`TraderID`=m.`OrgID` AND m.`OrgType`='trader'
               LEFT JOIN `ktv_cooperatives` o ON o.`CoopID`=m.`OrgID` AND m.`OrgType`='koperasi'
          WHERE a.`StatusCode` = 'active' AND b.`StatusCode` = 'active' 
               AND a.StatusAudit = '1' AND q.SurveyNr=14
               AND c.`IMSID` IN (207)
               AND (DATE(r.`DateUpdated`) >= DATE_FORMAT(c.`CertEventDate` - INTERVAL 6 MONTH, '%Y-%m-%d'))
          GROUP BY r.`FarmerID`, r.`GardenNr`, r.`SurveyNr`, r.`Certification`, r.`ICSDate`
           ";
		$query = $this->db->query($sql); //detailProgressAuditlog
		if ($query) {
			$results['detailProgressAuditlog'] = "ProgressAuditlog updated.";
		} else {
			$results['detailProgressAuditlog'] .= "||ProgressAuditlog Failed to update";
          }
          return $results;
     }
     
     function insert_detail_progress_farmer_updated()
	{
		$sql = "DELETE FROM ktv_jbcocoa_summary_progress_farmer_updated WHERE DateGenerated = DATE(NOW())";
		$query = $this->db->query($sql);
		$sql = "INSERT INTO ktv_jbcocoa_summary_progress_farmer_updated (
               DateGenerated, 
               ProgID, 
               CertHolderID, 
               DistrictID, 
               Program, 
               CertificateHolders, 
               Responsible, 
               IMSID, 
               ClusterID,
               FarmerID, 
               FarmerName, 
               Gender, 
               HandPhone, 
               DateOfBirth, 
               GroupID, 
               FarmerGroup, 
               Province, 
               District, 
               SubDistrict, 
               Village, 
               MaritalStatus, 
               FarmerStatus, 
               ReasonForInactive, 
               Remarks, 
               DateVisited
           ) 
           SELECT 
               DATE( NOW( ) ), 
               g.ProgID, e.CertHolderID, j.DistrictID,
               CONCAT(g.`ProgramName`, ' (', g.`ProgramYear`, ')') AS Program,
               IF(m.OrgType='trader',n.`Company`,IF(m.OrgType='koperasi',o.CoopName,'-')) AS CertificateHolders,
               IF(m.OrgType='trader',n.TraderName,IF(m.OrgType='koperasi',o.CoopName,'-')) AS Responsible,
               c.`IMSID`,
							 a.ClusterID,
               b.`FarmerID`,
               b.`FarmerName`,
               IF(b.Gender=1,'Male',IF(b.Gender=2,'Female','')) AS Gender,
               b.`HandPhone`,
               b.`Birthdate` AS DateOfBirth,
               b.`CPGid` AS GroupID,
               l.`GroupName` AS FarmerGroup,
               k.`Province`,
               j.`District`,
               i.`SubDistrict`,
               h.`Village`,
               IF(b.`MaritalStatus`=1,'Married',IF(b.`MaritalStatus`=2,'Single',IF(b.`MaritalStatus`=3,'Widower',''))) AS MaritalStatus,
               IF(b.StatusFarmer=1,'Active',IF(b.StatusFarmer=2,'Not Active','No Status')) AS FarmerStatus,
               IF(b.ReasonStatusFarmer=1,'Died',IF(b.ReasonStatusFarmer=2,'Moved/Left the Area',IF(b.ReasonStatusFarmer=3,'Stop Farming',''))) AS ReasonForInactive,
               b.`Comment` AS Remarks,
               IFNULL(b.`DateUpdated`, '0000-00-00') AS DateVisited 
          FROM
               ktv_cocoa_certification_pre_afl a
               INNER JOIN `ktv_cocoa_farmer` b ON b.`FarmerID` = a.`FarmerID` 
               LEFT JOIN `ktv_ims` c ON c.`IMSID`=a.`IMSID`
               LEFT JOIN `ktv_ims_master` d ON d.`IMSMasterID`=c.`IMSMasterID`
               LEFT JOIN `ktv_certification_holders` e ON e.`CertHolderID`=d.`CertHolderID`
               LEFT JOIN `ktv_first_buyer` f ON f.`FirstBuyerID`=c.`FirstBuyerID`
               LEFT JOIN `ktv_first_buyer_program` g ON g.`ProgID`=c.`ProgID`
               LEFT JOIN `ktv_village` h ON h.`VillageID`=b.`VillageID`
               LEFT JOIN `ktv_subdistrict` i ON i.`SubDistrictID`=h.`SubDistrictID`
               LEFT JOIN `ktv_district` j ON j.`DistrictID`=i.`DistrictID`
               LEFT JOIN `ktv_province` k ON k.`ProvinceID`=j.`ProvinceID` 
               LEFT JOIN `ktv_cpg` l ON l.`CPGid`=b.`CPGid`
               LEFT JOIN `ktv_supplychain_org` m ON m.`SupplychainID`=e.`SupplychainID`
               LEFT JOIN `ktv_traders` n ON n.`TraderID`=m.`OrgID` AND m.`OrgType`='trader'
               LEFT JOIN `ktv_cooperatives` o ON o.`CoopID`=m.`OrgID` AND m.`OrgType`='koperasi'
          WHERE a.`StatusCode` = 'active' AND b.`StatusCode` = 'active' 
               AND a.StatusAudit = '1' 
               AND c.`IMSID` IN (207)
               AND (DATE(b.`DateUpdated`) >= DATE_FORMAT(c.`CertEventDate` - INTERVAL 6 MONTH, '%Y-%m-%d'))
          GROUP BY a.`FarmerID`
           ";
		$query = $this->db->query($sql); //detailProgressFarmerUpdated
		if ($query) {
			$results['detailProgressFarmerUpdated'] = "ProgressFarmerUpdated updated.";
		} else {
			$results['detailProgressFarmerUpdated'] .= "||ProgressFarmerUpdated Failed to update";
          }
          return $results;
          
     }

     function insert_detail_progress_garden_status_updated()
	{
		$sql = "DELETE FROM ktv_jbcocoa_summary_progress_garden_status_updated WHERE DateGenerated = DATE(NOW())";
		$query = $this->db->query($sql);
		$sql = "INSERT INTO ktv_jbcocoa_summary_progress_garden_status_updated (
               DateGenerated, 
               ProgID, 
               CertHolderID, 
               DistrictID, 
               Program, 
               CertificateHolders, 
               Responsible, 
               IMSID, 
               ClusterID,
               FarmerID, 
               FarmerName, 
               GroupID, 
               FarmerGroup, 
               Province, 
               District, 
               SubDistrict, 
               Village, 
               GardenNr, 
               HaCocoaGarden, 
               GardenStatus, 
               ReasonForInactive, 
               CommodityForSwitchedToOtherCrop, 
               HaOtherCrop, 
               Remarks, 
               DateVisited
           ) 
           SELECT 
               DATE( NOW( ) ), 
               g.ProgID, e.CertHolderID, j.DistrictID,
               CONCAT(g.`ProgramName`, ' (', g.`ProgramYear`, ')') AS Program,
               IF(m.OrgType='trader',n.`Company`,IF(m.OrgType='koperasi',o.CoopName,'-')) AS CertificateHolders,
               IF(m.OrgType='trader',n.TraderName,IF(m.OrgType='koperasi',o.CoopName,'-')) AS Responsible,
               c.`IMSID`,
               a.ClusterID,
               b.`FarmerID`,
               b.`FarmerName`,
               b.`CPGid` AS GroupID,
               l.`GroupName` AS FarmerGroup,
               k.`Province`,
               j.`District`,
               i.`SubDistrict`,
               h.`Village`,
               p.`GardenNr`,
               IFNULL(q.GardenHaUncertified,'0.00') AS HaCocoaGarden,
               IF(p.ActiveStatus=1,'Active',IF(p.ActiveStatus=2,'Not Active','No Status')) AS GardenStatus,
               IF(p.GardenStatus=2,'Moved/Left the area',IF(p.GardenStatus=3,'Switched to other crop',IF(p.GardenStatus=4,'Sold the land',
               IF(p.GardenStatus=5,'Gave the land to family member',IF(p.GardenStatus=6,'Force majeure',''))))) AS ReasonForInactive,
               IF(p.GardenStatus=1,'Jagung',IF(p.GardenStatus=2,'Sawit',IF(p.GardenStatus=3,'Karet',IF(p.GardenStatus=4,'Cengkeh',IF(p.GardenStatus=5,'Padi',
               IF(p.GardenStatus=6,'Empty',IF(p.GardenStatus=7,'Others',IF(p.GardenStatus=8,'Buah-buahan',IF(p.GardenStatus=9,'Kayu-kayuan',''))))))))) AS CommodityForSwitchedToOtherCrop,
               IFNULL(p.`CommodityHa`,'0.00') AS HaOtherCrop,
               p.`Remarks`,
               IFNULL(p.`DateUpdated`, '0000-00-00') AS DateVisited 
          FROM
               ktv_cocoa_certification_pre_afl a
               INNER JOIN `ktv_cocoa_farmer` b ON b.`FarmerID` = a.`FarmerID` 
               LEFT JOIN ktv_cocoa_farmer_garden_status p ON p.`FarmerID`=b.`FarmerID`
               LEFT JOIN (SELECT FarmerID, GardenNr, MAX(SurveyNr) AS Latest, GardenHaUncertified FROM ktv_cocoa_farmer_garden GROUP BY FarmerID, GardenNr) q
               ON q.FarmerID=p.`FarmerID` AND q.GardenNr=p.`GardenNr`
               LEFT JOIN `ktv_ims` c ON c.`IMSID`=a.`IMSID`
               LEFT JOIN `ktv_ims_master` d ON d.`IMSMasterID`=c.`IMSMasterID`
               LEFT JOIN `ktv_certification_holders` e ON e.`CertHolderID`=d.`CertHolderID`
               LEFT JOIN `ktv_first_buyer` f ON f.`FirstBuyerID`=c.`FirstBuyerID`
               LEFT JOIN `ktv_first_buyer_program` g ON g.`ProgID`=c.`ProgID`
               LEFT JOIN `ktv_village` h ON h.`VillageID`=b.`VillageID`
               LEFT JOIN `ktv_subdistrict` i ON i.`SubDistrictID`=h.`SubDistrictID`
               LEFT JOIN `ktv_district` j ON j.`DistrictID`=i.`DistrictID`
               LEFT JOIN `ktv_province` k ON k.`ProvinceID`=j.`ProvinceID` 
               LEFT JOIN `ktv_cpg` l ON l.`CPGid`=b.`CPGid`
               LEFT JOIN `ktv_supplychain_org` m ON m.`SupplychainID`=e.`SupplychainID`
               LEFT JOIN `ktv_traders` n ON n.`TraderID`=m.`OrgID` AND m.`OrgType`='trader'
               LEFT JOIN `ktv_cooperatives` o ON o.`CoopID`=m.`OrgID` AND m.`OrgType`='koperasi'
          WHERE a.`StatusCode` = 'active' AND b.`StatusCode` = 'active' 
               AND a.StatusAudit = '1' 
               AND c.`IMSID` IN (207)
               AND (DATE(p.`DateUpdated`) >= DATE_FORMAT(c.`CertEventDate` - INTERVAL 6 MONTH, '%Y-%m-%d'))
          GROUP BY p.`FarmerID`, p.`GardenNr`
           ";
		$query = $this->db->query($sql); //detailProgressGardenStatusUpdated
		if ($query) {
			$results['detailProgressGardenStatusUpdated'] = "ProgressGardenStatusUpdated updated.";
		} else {
			$results['detailProgressGardenStatusUpdated'] .= "||ProgressGardenStatusUpdated Failed to update";
          }
          return $results;
     }

     function insert_detail_progress_gross_sales()
	{
		$sql = "DELETE FROM ktv_jbcocoa_summary_progress_gross_sales WHERE DateGenerated = DATE(NOW())";
		$query = $this->db->query($sql);
		$sql = "INSERT INTO ktv_jbcocoa_summary_progress_gross_sales (
               DateGenerated, 
               CertHolderID, 
               DistrictID, 
               IMSID, 
               ClusterID,
               ProgID, 
               ValidityStart, 
               CertHolderOrgName, 
               Location, 
               ValidityEnd, 
               ProgramYear, 
               ProgramName, 
               FarmerID, 
               FarmerName, 
               CPGId, 
               GroupName, 
               Village, 
               SubDistrict, 
               District, 
               FarmerDate, 
               bruto_farmer_2
           ) 
           SELECT
               DATE( NOW( ) ), 
               CertHolderID,
               DistrictID,
               IMSID,
               ClusterID,
               ProgID,
               ValidityStart,
               CertHolderOrgName,
               Location,
               ValidityEnd,
               ProgramYear,
               ProgramName,
               FarmerID,
               FarmerName,
               CPGId,
               GroupName,
               Village,
               SubDistrict,
               District,
               FarmerDate,
               bruto_farmer_2
          FROM
               (
          SELECT
               i.ProgID,
               i.CertHolderID,
               i.ValidityStart,
               ch.CertHolderOrgName,
               i.IMSID,
               q.ClusterID,
               i.CertDistrictID,
               i.Location,
               i.ValidityEnd,
               fbp.ProgramYear,
               fbp.ProgramName,
               dt.* 
          FROM
               ktv_ims i
               LEFT JOIN ktv_certification_holders ch ON ch.CertHolderID = i.CertHolderID
               LEFT JOIN ktv_cocoa_certification_certified_farmer ccf ON ccf.IMSID = i.IMSID
               LEFT JOIN ktv_first_buyer fb ON i.FirstBuyerID = fb.FirstBuyerID
               LEFT JOIN ktv_first_buyer_program fbp ON fb.FirstBuyerID = fbp.FirstBuyerID 
               AND i.ProgID = fbp.ProgID
               LEFT JOIN (
          SELECT
               FarmerID,
               FarmerName,
               rpt.CPGId,
               GroupName,
               Village,
               SubDistrict,
               District,
               rpt.DistrictID,
               transid_1 transid_farmer,
               date_1 FarmerDate,
               transcert_1 SupplyType,
               IFNULL( imsid_1, imsid_2 ) imsid_1,
          IF
               ( transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ), NULL, batchid_1 ) batchid_1,
          IF
               ( transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ), NULL, transid_1 ) transid_1,
          IF
               ( transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ), NULL, date_1 ) date_1,
          IF
               ( transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ), NULL, supplyorgid_1 ) supplyorgid_1,
          IF
               ( transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ), NULL, faktur_1 ) faktur_1,
          IF
               ( transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ), NULL, bruto_1 ) bruto_1,
          IF
               ( transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ), NULL, netto_1 ) netto_1,
          IF
               ( transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ), NULL, orgtype_1 ) orgtype_1,
          IF
               ( transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ), NULL, name_1 ) name_1,
          IF
               ( transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ), NULL, batchnumber_1 ) batchnumber_1,
          IF
               ( transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ), NULL, destpo_1 ) destpo_1,
          IF
               ( transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ), NULL, status_1 ) status_1,
          IF
               ( transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ), NULL, deliverydate_1 ) deliverydate_1,
          IF
               (
               transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ),
               batchid_1,
          IF
               ( transcert_1 = 'cocoalife', NULL, batchid_2 ) 
               ) batchid_2,
          IF
               (
               transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ),
               transid_1,
          IF
               ( transcert_1 = 'cocoalife', NULL, transid_2 ) 
               ) transid_2,
          IF
               (
               transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ),
               date_1,
          IF
               ( transcert_1 = 'cocoalife', NULL, date_2 ) 
               ) date_2,
          IF
               (
               transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ),
               supplyorgid_1,
          IF
               ( transcert_1 = 'cocoalife', NULL, supplyorgid_2 ) 
               ) supplyorgid_2,
          IF
               (
               transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ),
               faktur_1,
          IF
               ( transcert_1 = 'cocoalife', NULL, faktur_2 ) 
               ) faktur_2,
          IF
               (
               transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ),
               bruto_1,
          IF
               ( transcert_1 = 'cocoalife', NULL, bruto_2 ) 
               ) bruto_2,
          IF
               (
               transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ),
               netto_1,
          IF
               ( transcert_1 = 'cocoalife', NULL, netto_farmer_2 ) 
               ) netto_farmer_2,
          IF
               (
               transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ),
               bruto_1,
          IF
               ( transcert_1 = 'cocoalife', NULL, bruto_farmer_2 ) 
               ) bruto_farmer_2,
          IF
               (
               transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ),
               netto_1,
          IF
               ( transcert_1 = 'cocoalife', NULL, netto_2 ) 
               ) netto_2,
          IF
               (
               transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ),
               orgtype_1,
          IF
               ( transcert_1 = 'cocoalife', NULL, orgtype_2 ) 
               ) orgtype_2,
          IF
               (
               transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ),
               name_1,
          IF
               ( transcert_1 = 'cocoalife', NULL, name_2 ) 
               ) name_2,
          IF
               (
               transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ),
               batchnumber_1,
          IF
               ( transcert_1 = 'cocoalife', NULL, batchnumber_2 ) 
               ) batchnumber_2,
          IF
               (
               transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ),
               destpo_1,
          IF
               ( transcert_1 = 'cocoalife', NULL, destpo_2 ) 
               ) destpo_2,
          IF
               (
               transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ),
               status_1,
          IF
               ( transcert_1 = 'cocoalife', NULL, status_2 ) 
               ) status_2,
          IF
               (
               transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ),
               deliverydate_1,
          IF
               ( transcert_1 = 'cocoalife', NULL, deliverydate_2 ) 
               ) deliverydate_2,
          IF
               ( orgtype_2 = 'Gudang', supplyorgid_2, supplyorgid_3 ) supplyorgid_3,
          IF
               ( orgtype_2 = 'Gudang', date_2, date_3 ) date_3,
          IF
               ( orgtype_2 = 'Gudang', transid_2, transid_3 ) transid_3,
          IF
               ( orgtype_2 = 'Gudang', batchid_2, batchid_3 ) batchid_3,
          IF
               ( orgtype_2 = 'Gudang', orgid_2, orgid_3 ) orgid_3,
          IF
               ( orgtype_2 = 'Gudang', name_2, name_3 ) name_3,
          IF
               ( orgtype_2 = 'Gudang', faktur_2, faktur_3 ) faktur_3,
          IF
               ( orgtype_2 = 'Gudang', bruto_2, bruto_3 ) bruto_3,
          IF
               ( orgtype_2 = 'Gudang', netto_2, netto_3 ) netto_3,
          IF
               ( orgtype_2 = 'Gudang', netto_1, netto_farmer_2 ) bruto_farmer_3,
          IF
               ( orgtype_2 = 'Gudang', netto_1, netto_farmer_2 ) netto_farmer_3,
          IF
               ( orgtype_2 = 'Gudang', destpo_2, destpo_3 ) destpo_3,
          IF
               ( orgtype_2 = 'Gudang', status_2, status_3 ) status_3,
               IFNULL( sb2.SupplyBatchDate, sb1.SupplyBatchDate ) BatchDateCH 
          FROM
               rpt_tc_trans_detail rpt
               LEFT JOIN view_supplychain_org dest ON dest.SupplychainID = supplydestorgid_1
               LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchID =
          IF
               (
               transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ),
               batchid_1,
          IF
               ( transcert_1 = 'cocoalife', NULL, batchid_2 ) 
               )
               LEFT JOIN ktv_supplychain_batch sb1 ON sb1.SupplyBatchID =
          IF
               ( transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' ), NULL, batchid_1 ) 
          WHERE
               partnerid_1 = 22 
               AND supplytype_1 = 'Farmer' 
               AND (
          IF
               ( orgtype_2 = 'Gudang', date_2, date_3 ) IS NOT NULL 
               OR ( sb2.DeliveryDate IS NULL AND supplytype_1 = 'utz' ) 
               OR ( sb2.DeliveryDate = '0000-00-00' AND supplytype_1 = 'utz' ) 
               ) 
               AND transcert_1 = 'utz' 
               ) dt ON dt.FarmerID = ccf.FarmerID 
               AND dt.FarmerDate BETWEEN i.ValidityStart 
               AND i.ValidityEnd 
               LEFT JOIN ktv_cocoa_certification_pre_afl q ON q.IMSID = i.IMSID AND q.FarmerID = dt.FarmerID 
          WHERE
               i.IMSID IN ( 207 ) 
               AND i.ProgID = 7 
               AND dt.FarmerID IS NOT NULL 
               AND dt.imsid_1 = i.IMSID 
               ) a
           ";
		$query = $this->db->query($sql); //detailProgressGrossSales
		if ($query) {
			$results['detailProgressGardenGrossSales'] = "ProgressGardenGrossSales updated.";
		} else {
			$results['detailProgressGardenGrossSales'] .= "||ProgressGardenGrossSales Failed to update";
          }
          return $results;
     }

     function insert_detail_progress_net_sales()
	{
		$sql = "DELETE FROM ktv_jbcocoa_summary_progress_net_sales WHERE DateGenerated = DATE(NOW())";
		$query = $this->db->query($sql);
		$sql = "INSERT INTO ktv_jbcocoa_summary_progress_net_sales (
               DateGenerated, 
               CertHolderID, 
               DistrictID, 
               IMSID, 
               ClusterID,
               ProgID, 
               ValidityStart, 
               CertHolderOrgName, 
               Location, 
               ValidityEnd, 
               ProgramYear, 
               ProgramName, 
               FarmerID, 
               FarmerName, 
               CPGId, 
               GroupName, 
               Village, 
               SubDistrict, 
               District, 
               FarmerDate, 
               netto_farmer_2
           ) 
           SELECT
               DATE( NOW( ) ), 
               CertHolderID,
               DistrictID,
               IMSID,
               ClusterID,
               ProgID,
               ValidityStart,
               CertHolderOrgName,
               Location,
               ValidityEnd,
               ProgramYear,
               ProgramName,
               FarmerID,
               FarmerName,
               CPGId,
               GroupName,
               Village,
               SubDistrict,
               District,
               FarmerDate,
               netto_farmer_2
          FROM
               (
          SELECT
               i.ProgID,
               i.CertHolderID,
               i.ValidityStart,
               ch.CertHolderOrgName,
               i.IMSID,
							 q.ClusterID,
               i.CertDistrictID,
               i.Location,
               i.ValidityEnd,
               fbp.ProgramYear,
               fbp.ProgramName,
               dt.* 
          FROM
               ktv_ims i
               LEFT JOIN ktv_certification_holders ch ON ch.CertHolderID = i.CertHolderID
               LEFT JOIN ktv_cocoa_certification_certified_farmer ccf ON ccf.IMSID = i.IMSID
               LEFT JOIN ktv_first_buyer fb ON i.FirstBuyerID = fb.FirstBuyerID
               LEFT JOIN ktv_first_buyer_program fbp ON fb.FirstBuyerID = fbp.FirstBuyerID 
               AND i.ProgID = fbp.ProgID
               LEFT JOIN (
          SELECT
               FarmerID,
               FarmerName,
               rpt.CPGId,
               GroupName,
               Village,
               SubDistrict,
               District,
               rpt.DistrictID,
               transid_1 transid_farmer,
               date_1 FarmerDate,
               transcert_1 SupplyType,
               IFNULL( imsid_1, imsid_2 ) imsid_1,
          IF
               ( transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ), NULL, batchid_1 ) batchid_1,
          IF
               ( transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ), NULL, transid_1 ) transid_1,
          IF
               ( transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ), NULL, date_1 ) date_1,
          IF
               ( transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ), NULL, supplyorgid_1 ) supplyorgid_1,
          IF
               ( transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ), NULL, faktur_1 ) faktur_1,
          IF
               ( transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ), NULL, bruto_1 ) bruto_1,
          IF
               ( transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ), NULL, netto_1 ) netto_1,
          IF
               ( transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ), NULL, orgtype_1 ) orgtype_1,
          IF
               ( transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ), NULL, name_1 ) name_1,
          IF
               ( transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ), NULL, batchnumber_1 ) batchnumber_1,
          IF
               ( transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ), NULL, destpo_1 ) destpo_1,
          IF
               ( transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ), NULL, status_1 ) status_1,
          IF
               ( transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ), NULL, deliverydate_1 ) deliverydate_1,
          IF
               (
               transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ),
               batchid_1,
          IF
               ( transcert_1 = 'cocoalife', NULL, batchid_2 ) 
               ) batchid_2,
          IF
               (
               transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ),
               transid_1,
          IF
               ( transcert_1 = 'cocoalife', NULL, transid_2 ) 
               ) transid_2,
          IF
               (
               transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ),
               date_1,
          IF
               ( transcert_1 = 'cocoalife', NULL, date_2 ) 
               ) date_2,
          IF
               (
               transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ),
               supplyorgid_1,
          IF
               ( transcert_1 = 'cocoalife', NULL, supplyorgid_2 ) 
               ) supplyorgid_2,
          IF
               (
               transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ),
               faktur_1,
          IF
               ( transcert_1 = 'cocoalife', NULL, faktur_2 ) 
               ) faktur_2,
          IF
               (
               transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ),
               bruto_1,
          IF
               ( transcert_1 = 'cocoalife', NULL, bruto_2 ) 
               ) bruto_2,
          IF
               (
               transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ),
               netto_1,
          IF
               ( transcert_1 = 'cocoalife', NULL, netto_farmer_2 ) 
               ) netto_farmer_2,
          IF
               (
               transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ),
               bruto_1,
          IF
               ( transcert_1 = 'cocoalife', NULL, bruto_farmer_2 ) 
               ) bruto_farmer_2,
          IF
               (
               transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ),
               netto_1,
          IF
               ( transcert_1 = 'cocoalife', NULL, netto_2 ) 
               ) netto_2,
          IF
               (
               transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ),
               orgtype_1,
          IF
               ( transcert_1 = 'cocoalife', NULL, orgtype_2 ) 
               ) orgtype_2,
          IF
               (
               transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ),
               name_1,
          IF
               ( transcert_1 = 'cocoalife', NULL, name_2 ) 
               ) name_2,
          IF
               (
               transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ),
               batchnumber_1,
          IF
               ( transcert_1 = 'cocoalife', NULL, batchnumber_2 ) 
               ) batchnumber_2,
          IF
               (
               transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ),
               destpo_1,
          IF
               ( transcert_1 = 'cocoalife', NULL, destpo_2 ) 
               ) destpo_2,
          IF
               (
               transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ),
               status_1,
          IF
               ( transcert_1 = 'cocoalife', NULL, status_2 ) 
               ) status_2,
          IF
               (
               transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ),
               deliverydate_1,
          IF
               ( transcert_1 = 'cocoalife', NULL, deliverydate_2 ) 
               ) deliverydate_2,
          IF
               ( orgtype_2 = 'Gudang', supplyorgid_2, supplyorgid_3 ) supplyorgid_3,
          IF
               ( orgtype_2 = 'Gudang', date_2, date_3 ) date_3,
          IF
               ( orgtype_2 = 'Gudang', transid_2, transid_3 ) transid_3,
          IF
               ( orgtype_2 = 'Gudang', batchid_2, batchid_3 ) batchid_3,
          IF
               ( orgtype_2 = 'Gudang', orgid_2, orgid_3 ) orgid_3,
          IF
               ( orgtype_2 = 'Gudang', name_2, name_3 ) name_3,
          IF
               ( orgtype_2 = 'Gudang', faktur_2, faktur_3 ) faktur_3,
          IF
               ( orgtype_2 = 'Gudang', bruto_2, bruto_3 ) bruto_3,
          IF
               ( orgtype_2 = 'Gudang', netto_2, netto_3 ) netto_3,
          IF
               ( orgtype_2 = 'Gudang', netto_1, netto_farmer_2 ) bruto_farmer_3,
          IF
               ( orgtype_2 = 'Gudang', netto_1, netto_farmer_2 ) netto_farmer_3,
          IF
               ( orgtype_2 = 'Gudang', destpo_2, destpo_3 ) destpo_3,
          IF
               ( orgtype_2 = 'Gudang', status_2, status_3 ) status_3,
               IFNULL( sb2.SupplyBatchDate, sb1.SupplyBatchDate ) BatchDateCH 
          FROM
               rpt_tc_trans_detail rpt
               LEFT JOIN view_supplychain_org dest ON dest.SupplychainID = supplydestorgid_1
               LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchID =
          IF
               (
               transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' || ch_1 IS NOT NULL ),
               batchid_1,
          IF
               ( transcert_1 = 'cocoalife', NULL, batchid_2 ) 
               )
               LEFT JOIN ktv_supplychain_batch sb1 ON sb1.SupplyBatchID =
          IF
               ( transcert_1 = 'utz' && ( orgtype_2 = 'Gudang' || dest.OrgType = 'Gudang' ), NULL, batchid_1 ) 
          WHERE
               partnerid_1 = 22 
               AND supplytype_1 = 'Farmer' 
               AND (
          IF
               ( orgtype_2 = 'Gudang', date_2, date_3 ) IS NOT NULL 
               OR ( sb2.DeliveryDate IS NULL AND supplytype_1 = 'utz' ) 
               OR ( sb2.DeliveryDate = '0000-00-00' AND supplytype_1 = 'utz' ) 
               ) 
               AND transcert_1 = 'utz' 
               ) dt ON dt.FarmerID = ccf.FarmerID 
               AND dt.FarmerDate BETWEEN i.ValidityStart 
               AND i.ValidityEnd 
               LEFT JOIN ktv_cocoa_certification_pre_afl q ON q.IMSID = i.IMSID AND q.FarmerID = dt.FarmerID 
          WHERE
               i.IMSID IN ( 207 ) 
               AND i.ProgID = 7 
               AND dt.FarmerID IS NOT NULL 
               AND dt.imsid_1 = i.IMSID 
               ) a
           ";
		$query = $this->db->query($sql); //detailProgressNetSales
		if ($query) {
			$results['detailProgressNetSales'] = "ProgressGardenNetSales updated.";
		} else {
			$results['detailProgressNetSales'] .= "||ProgressGardenNetSales Failed to update";
          }
          return $results;
     }
     
     function insert_detail_FarmXUser() {
        $sql = "DELETE FROM ktv_jbcocoa_summary_farmxuser WHERE DateGenerated=DATE(now()) AND ProgID=14;";
        $query = $this->db->query($sql);
        
        $sql = "INSERT INTO `cocoatrace`.`ktv_jbcocoa_summary_farmxuser`(`DateGenerated`, `ProgID`, `Program`, `IMSID`, `CertHolderID`, `CertificateHolders`, `UserId`, `Enumerator`, `ClusterID`, `Cluster`)

SELECT
	DATE(now()) AS DateGenerated,
	fbp.ProgID,
	fbp.ProgramName AS Program,
	ims.IMSID,
	ims.CertHolderID,
	CONCAT(ch.CertHolderOrgName,' (',ch.CertHolderResponsible,')') AS CertificateHolders,
	IFNULL( fg.CreatedBy, fg.LastModifiedBy ) AS UserId,
	IFNULL( su.UserRealName, su1.UserRealName ) AS Enumerator,
	a.ClusterID,
	ic.ClusterName AS Cluster 
FROM
	ktv_cocoa_certification_pre_afl a
	LEFT JOIN ktv_ims_cluster ic ON ic.ClusterID=a.ClusterID
	LEFT JOIN ktv_cocoa_certification_pre_afl_garden b ON b.IMSID = a.IMSID 
	AND b.FarmerID = a.FarmerID
	LEFT JOIN ktv_ims ims ON ims.IMSID = a.IMSID
	LEFT JOIN ktv_certification_holders ch ON ch.CertHolderID=ims.CertHolderID
	LEFT JOIN ktv_first_buyer_program fbp ON fbp.ProgID=ims.ProgID
	INNER JOIN ktv_cocoa_farmer_garden fg ON fg.FarmerID = b.FarmerID 
	AND fg.GardenNr = b.GardenNr 
	AND fg.SurveyNr = b.SurveyNr
	LEFT JOIN sys_user su ON su.UserId = fg.CreatedBy
	LEFT JOIN sys_user su1 ON su1.UserId = fg.LastModifiedBy 
WHERE
	a.IMSID = 278 
GROUP BY
	ic.ClusterID,
	fg.CreatedBy;";
        $query = $this->db->query($sql); //detailSOC
        if ($query) {
            $results['detailSOC'] = "detailFarmXUser Wave IV updated.";
        } else {
            $results['detailSOC'] .= "||detailFarmXUser Wave IV  Failed to update";
        }
        return $results;
     }
     
     function insert_detail_FarmGUser() {
        $sql = "DELETE FROM ktv_jbcocoa_summary_farmguser WHERE DateGenerated=DATE(now()) AND ProgID=14;";
        $query = $this->db->query($sql);
        
        $sql = "INSERT INTO `cocoatrace`.`ktv_jbcocoa_summary_farmguser`(`id`, `DateGenerated`, `ProgID`, `Program`, `IMSID`, `CertHolderID`, `CertificateHolders`, `SupplychainID`, `OrgType`, `OrgID`, `Name`, `VillageID`, `Village`, `SubDistrict`, `DistrictID`, `District`, `Latitude`, `Longitude`, `ClusterID`, `JmlAccess`)
SELECT
	DATE(now()) AS DateGenerated,
	fbp.ProgID,
	fbp.ProgramName AS Program,
	ims.IMSID,
	ims.CertHolderID,
	CONCAT(ch.CertHolderOrgName,' (',ch.CertHolderResponsible,')') AS CertificateHolders, 
	c.SupplychainID,
	c.OrgType,
	c.OrgID,
	c.`Name`,
	c.VillageID,
	vil.Village,
	sub.SubDistrict,
	dis.DistrictID,
	IFNULL(ic.ClusterName,dis.District) AS District,
	c.Latitude,
	c.Longitude,
	a.ClusterID,
	COUNT(b.SupplychainID) AS JmlAccess
FROM
	ktv_ims_buying_unit a
	LEFT JOIN ktv_ims_cluster ic ON ic.ClusterID=a.ClusterID
	LEFT JOIN ktv_ims ims ON ims.IMSID=a.IMSID
	LEFT JOIN ktv_certification_holders ch ON ch.CertHolderID=ims.CertHolderID
	LEFT JOIN ktv_first_buyer_program fbp ON fbp.ProgID=ims.ProgID
	INNER JOIN `sys_user_login_mobile` b ON b.SupplychainID = a.SupplychainID
	LEFT JOIN ktv_supplychain_org_view c ON c.SupplychainID = a.SupplychainID 
	LEFT JOIN ktv_village vil ON vil.VillageID=c.VillageID
	LEFT JOIN ktv_subdistrict sub ON sub.SubDistrictID=vil.SubDistrictID
	LEFT JOIN ktv_district dis ON dis.DistrictID=sub.DistrictID
	LEFT JOIN ktv_province prv ON prv.ProvinceID=dis.ProvinceID
WHERE
	a.IMSID = 278 
GROUP BY
	a.IMSID,
	a.SupplychainID;";
        $query = $this->db->query($sql); //detailSOC
        if ($query) {
            $results['detailSOC'] = "detailFarmGUser Wave IV updated.";
        } else {
            $results['detailSOC'] .= "||detailFarmGUser Wave IV  Failed to update";
        }
        return $results;
     }

	function insert_KPIDateProcess()
	{
		$userid = $_SESSION['userid'];
		$sql = "DELETE FROM ktv_certification_progress_jbcocoa_process WHERE DATE(DateProcess) = DATE(NOW()) AND ReportStatus = 0";
		$query = $this->db->query($sql);
		$sql = "INSERT INTO ktv_certification_progress_jbcocoa_process (
                DateProcess,
                COMMENT,
                ReportStatus,
                DateCreated,
                CreatedBy
                ) 
           VALUES
                (DATE(NOW()), NULL, 0, NOW(), 1)";
		$query = $this->db->query($sql, array($userid)); //detailKPIDateProcess
		if ($query) {
			$results['detailKPIDateProcess'] = "detailKPIDateProcess updated.";
		} else {
			$results['detailKPIDateProcess'] .= "||detailKPIDateProcess Failed to update";
		}
		return $results;
	}
	
	function calcCertifiedTraderFromDetail()
	{
          //Reset Traders Fields
		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `Traders` = NULL WHERE ProgID = 7";
          // $sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `Traders` = NULL";
		$this->db->query($sql);
          // ===============//

		$sql = "UPDATE 
               ktv_certification_progress_jbcocoa_ims_district_report a 
               JOIN 
                    (SELECT
                         n.ProgID,
                         m.CertHolderID,
                         m.CertificateHolders,
                         m.IMSID,
                         m.ClusterID,
                         Count(m.CertHolderID) AS total
                    FROM
                         ktv_jbcocoa_summary_certified_buying_unit AS m
                    LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID
                    WHERE
                         DATE(m.DateGenerated) = DATE(NOW()) and m.Status = 'Certified'
                    GROUP BY
                         n.ProgID,
                         m.CertHolderID,
                         m.IMSID,
                         m.ClusterID) b 
                    ON a.ProgID = b.ProgID 
                    AND a.CertHolderID = b.CertHolderID 
                    AND a.IMSID = b.IMSID 
                    AND a.ClusterID = b.ClusterID SET a.Traders = b.total 
          WHERE a.ProgID = 7 ;";
		$query = $this->db->query($sql); //Traders
		if ($query) {
			$results['calcCertifiedTraderFromDetail'] = "Traders field updated.";
		} else {
			$results['calcCertifiedTraderFromDetail'] .= "||Traders Failed to update";
		}
		return $results;
     }
     
     function calcRegisteredTraderFromDetail()
	{
          //Reset RegTraders Fields
		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `RegTraders` = NULL WHERE ProgID = 7";
          // $sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `RegTraders` = NULL";
		$this->db->query($sql);
          // ===============//

		$sql = "UPDATE 
               ktv_certification_progress_jbcocoa_ims_district_report a 
               JOIN 
                    (SELECT
                         n.ProgID,
                         m.CertHolderID,
                         m.CertificateHolders,
                         m.IMSID,
                         m.ClusterID,
                         Count(m.CertHolderID) AS total
                    FROM
                         ktv_jbcocoa_summary_certified_buying_unit AS m
                    LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID
                    WHERE
                         DATE(m.DateGenerated) = DATE(NOW()) and m.Status = 'Registered'
                    GROUP BY
                         n.ProgID,
                         m.CertHolderID,
                         m.IMSID,
                         m.ClusterID) b 
                    ON a.ProgID = b.ProgID 
                    AND a.CertHolderID = b.CertHolderID 
                    AND a.IMSID = b.IMSID 
                    AND a.ClusterID = b.ClusterID SET a.RegTraders = b.total 
          WHERE a.ProgID = 7 ;";
		$query = $this->db->query($sql); //RegTraders
		if ($query) {
			$results['calcRegisteredTraderFromDetail'] = "RegTraders field updated.";
		} else {
			$results['calcRegisteredTraderFromDetail'] .= "||RegTraders Failed to update";
		}
		return $results;
	}
	
	function calcCertifiedCHFromDetail()
	{
          //Reset CertificationHolders Fields
		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `CertificationHolders` = NULL WHERE ProgID = 7";
		$this->db->query($sql);
          // ===============//
          
          //Khusus query ini pakai m.DistrictID karena m.District tidak ada di table ktv_jbcocoa_summary_certified_certificate_holder
		$sql = "UPDATE 
               ktv_certification_progress_jbcocoa_ims_district_report a 
               JOIN 
                    (SELECT
                         n.ProgID,
                         m.CertHolderID,
                         m.CertificateHolders,
                         m.IMSID,
                         m.ClusterID, 
                         Count(m.CertHolderID) AS total
                    FROM
                    ktv_jbcocoa_summary_certified_certificate_holder AS m
                    LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID
                    WHERE
                         DATE(m.DateGenerated) = DATE(NOW()) and m.Status = 'Certified'
                    GROUP BY
                         n.ProgID,
                         m.CertHolderID,
                         m.IMSID,
                         m.ClusterID
                    ) b 
                    ON a.ProgID = b.ProgID 
                    AND a.CertHolderID = b.CertHolderID 
                    AND a.IMSID = b.IMSID 
                    AND a.ClusterID = b.ClusterID SET a.CertificationHolders = b.total
              WHERE a.ProgID = 7";
		$query = $this->db->query($sql); //CertificationHolders
		if ($query) {
			$results['calcCertifiedCHFromDetail'] = "CertificationHolders field updated.";
		} else {
			$results['calcCertifiedCHFromDetail'] .= "||CertificationHolders Failed to update";
		}
		return $results;
	}
	function calcRegisteredCHFromDetail()
	{
          //Reset RegCertificationHolders Fields
		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `RegCertificationHolders` = NULL WHERE ProgID = 7";
		$this->db->query($sql);
          // ===============//
          
          //Khusus query ini pakai m.DistrictID karena m.District tidak ada di table ktv_jbcocoa_summary_certified_certificate_holder
		$sql = "UPDATE 
               ktv_certification_progress_jbcocoa_ims_district_report a 
               JOIN 
                    (SELECT
                         n.ProgID,
                         m.CertHolderID,
                         m.CertificateHolders,
                         m.IMSID,
                         m.ClusterID, 
                         Count(m.CertHolderID) AS total
                    FROM
                    ktv_jbcocoa_summary_certified_certificate_holder AS m
                    LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID
                    WHERE
                         DATE(m.DateGenerated) = DATE(NOW()) and m.Status = 'Registered'
                    GROUP BY
                         n.ProgID,
                         m.CertHolderID,
                         m.IMSID,
                         m.ClusterID
                    ) b 
                    ON a.ProgID = b.ProgID 
                    AND a.CertHolderID = b.CertHolderID 
                    AND a.IMSID = b.IMSID 
                    AND a.ClusterID = b.ClusterID SET a.RegCertificationHolders = b.total
              WHERE a.ProgID = 7";
		$query = $this->db->query($sql); //CertificationHolders
		if ($query) {
			$results['calcRegCertifiedCHFromDetail'] = "RegCertificationHolders field updated.";
		} else {
			$results['calcRegCertifiedCHFromDetail'] .= "||RegCertificationHolders Failed to update";
		}
		return $results;
	}
	
	function calcCFLFromDetail()
	{
          //Reset FarmerCertifiedFinal Fields
		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `FarmerCertifiedFinal` = NULL WHERE ProgID = 7";
		$this->db->query($sql);
          // ===============//
		$sql = "UPDATE 
            ktv_certification_progress_jbcocoa_ims_district_report a 
            JOIN 
                (SELECT
                    n.ProgID,
                    m.CertHolderID,
                    m.CertificateHolders,
                    m.IMSID,
                    m.ClusterID,
                    Count(m.FarmerID) AS total
                FROM
                ktv_jbcocoa_summary_certified_farmer_list AS m
                LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID
                WHERE
                    DATE(m.DateGenerated) = DATE(NOW())
                GROUP BY
                    n.ProgID,
                    m.CertHolderID,
                    m.IMSID,
                    m.ClusterID
                ) b 
                ON a.ProgID = b.ProgID 
                AND a.CertHolderID = b.CertHolderID 
                AND a.IMSID = b.IMSID 
                AND a.ClusterID = b.ClusterID SET a.FarmerCertifiedFinal = b.total 
        WHERE a.ProgID = 7 ;";
		$query = $this->db->query($sql); //FarmerCertifiedFinal
		if ($query) {
			$results['calcCFLFromDetail'] = "FarmerCertifiedFinal field updated.";
		} else {
			$results['calcCFLFromDetail'] .= "||FarmerCertifiedFinal field Failed to update";
		}
		return $results;
	}
        
        function calcCFLFromDetailWave3(){
            $sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `FarmerCertifiedFinal` = NULL WHERE ProgID IN (12,14);";
            $query = $this->db->query($sql);
            
            $sql = "UPDATE ktv_certification_progress_jbcocoa_ims_district_report a
JOIN (
    SELECT
	n.ProgID,
	m.CertHolderID,
	m.CertificateHolders,
	m.IMSID,
	m.ClusterID,
	Count( m.FarmerID ) AS total 
    FROM
	ktv_jbcocoa_summary_certified_farmer_list AS m
	LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID 
    WHERE
	DATE( m.DateGenerated ) = DATE( NOW( ) ) 
    GROUP BY
	n.ProgID,
	m.CertHolderID,
	m.IMSID,
	m.ClusterID 
) b ON a.ProgID = b.ProgID 
	AND a.CertHolderID = b.CertHolderID 
	AND a.IMSID = b.IMSID 
	AND a.ClusterID = b.ClusterID 
	SET a.FarmerCertifiedFinal = b.total 
WHERE
	a.ProgID IN (12,14);";
            $query = $this->db->query($sql);
        if ($query) {
            $results['calcCFLFromDetailWave3'] = "FarmerCertifiedFinalWave3 field updated.";
        } else {
            $results['calcCFLFromDetailWave3'] .= "||FarmerCertifiedFinalWave3 field Failed to update";
        }
        return $results;
    }
	
	function calcCertifiedHaSalesQuotaFromDetail()
	{
          //Reset CertifiedHaSalesQuota Fields
		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `CertifiedHa` = NULL, SalesQuota = NULL WHERE ProgID = 7";
		$this->db->query($sql);
          // ===============//
		$sql = "UPDATE 
               ktv_certification_progress_jbcocoa_ims_district_report a 
               JOIN 
                    (SELECT
                        b.ProgID,
                        b.CertHolderID,
                        a.IMSID,
                        ic.ClusterName District,
                        SUM( a.CertHectare ) AS CertHa,
                        Sum( a.SalesQuota ) AS SalesQuota,
                        bb.ClusterID
                    FROM
                        ktv_cocoa_certification_certified_farmer AS a
                        JOIN ktv_cocoa_certification_pre_afl bb ON bb.FarmerID=a.FarmerID AND bb.IMSID=a.IMSID
                        JOIN ktv_ims_cluster ic ON ic.ClusterID=bb.ClusterID
                        LEFT JOIN ktv_ims AS b ON b.IMSID = a.IMSID 
                    WHERE
                        b.ProgID = 7 
                        AND ( b.CertificationStart IS NOT NULL AND b.CertificationStart <> '0000-00-00' ) 
                    GROUP BY
                        b.ProgID,
                        b.CertHolderID,
                        a.IMSID,
                        bb.ClusterID) b 
                    ON a.ProgID = b.ProgID 
                    AND a.CertHolderID = b.CertHolderID 
                    AND a.IMSID = b.IMSID 
                    AND a.ClusterID = b.ClusterID SET a.CertifiedHa = b.CertHa, a.SalesQuota = b.SalesQuota 
               WHERE a.ProgID = 7";
		$query = $this->db->query($sql); //CertifiedHa, SalesQuota
		if ($query) {
			$results['calcCertifiedHaSalesQuotaFromDetail'] = "CertifiedHaSalesQuota field updated.";
		} else {
			$results['calcCertifiedHaSalesQuotaFromDetail'] .= "||CertifiedHaSalesQuota field Failed to update";
		}
		return $results;
	}
	
	function calcAFL1FromDetail()
	{
          //Reset AFL1 Fields
		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `AFL1` = NULL WHERE ProgID = 7";
		$this->db->query($sql);
          // ===============//
		$sql = "UPDATE 
            ktv_certification_progress_jbcocoa_ims_district_report a 
            JOIN 
                (SELECT
                    n.ProgID,
                    m.CertHolderID,
                    m.CertificateHolders,
                    m.IMSID,
                    m.ClusterID,
                    Count(m.FarmerID) AS total
                FROM
                    ktv_jbcocoa_summary_approved_farmer_list AS m
                LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID
                WHERE
                    DATE(m.DateGenerated) = DATE(NOW())
                GROUP BY
                    n.ProgID,
                    m.CertHolderID,
                    m.IMSID,
                    m.ClusterID
                ) b 
                ON a.ProgID = b.ProgID 
                AND a.CertHolderID = b.CertHolderID 
                AND a.IMSID = b.IMSID 
                AND a.ClusterID = b.ClusterID SET a.AFL1 = b.total 
        WHERE a.ProgID = 7 ;";
		$query = $this->db->query($sql); //AFL1
		if ($query) {
			$results['calcAFL1FromDetail'] = "AFL1 field updated.";
		} else {
			$results['calcAFL1FromDetail'] .= "||AFL1 field Failed to update";
		}
		return $results;
	}
        
        function calcFarmerBasicFromDetail() {
            $sql = "UPDATE ktv_certification_progress_jbcocoa_ims_district_report SET FarmerBasic=NULL WHERE ProgID IN (12,14);";
            $update_null = $this->db->query($sql);
            
            if($update_null){            
                $sql = "UPDATE
ktv_certification_progress_jbcocoa_ims_district_report a
JOIN(
SELECT
        n.ProgID,
        m.CertHolderID,
	m.IMSID,
	m.ClusterID,
	COUNT(FarmerID) jml,
	m.DateGenerated
FROM
	ktv_jbcocoa_summary_farmerbasic as m
	LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID 
WHERE
	m.ProgID IN (12,14)
	AND DateGenerated = DATE(NOW())
GROUP BY
	n.ProgID,
	m.CertHolderID,
	m.IMSID,
	m.ClusterID 
) b ON a.ProgID = b.ProgID
    AND a.CertHolderID = b.CertHolderID
    AND a.IMSID = b.IMSID
    AND a.ClusterID = b.ClusterID 
    SET a.FarmerBasic=b.jml
WHERE
	a.ProgID IN (12,14);";
            $query = $this->db->query($sql);
		if ($query) {
			$results['calcFarmerBasicFromDetail'] = "FarmerBasic field updated.";
		} else {
			$results['calcFarmerBasicFromDetail'] .= "||FarmerBasic field Failed to update";
		}
            } else {
                $results['calcFarmerBasicFromDetail'] .= "||FarmerBasic field Failed to update";
            }
            return $results;
        }

    function calcCOCFromDetail()
	{
         //Reset COC Fields
		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `COC` = NULL WHERE ProgID = 7";
		$this->db->query($sql);
         // ===============//
		$sql = "UPDATE 
            ktv_certification_progress_jbcocoa_ims_district_report a 
            JOIN 
                (SELECT
                    n.ProgID,
                    m.CertHolderID,
                    m.CertificateHolders,
                    m.IMSID,
                    m.ClusterID,
                    Count(m.ParticipantID) AS total
                FROM
                    ktv_jbcocoa_summary_coc AS m
                LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID
                WHERE
                    DATE(m.DateGenerated) = DATE(NOW())
                GROUP BY
                    n.ProgID,
                    m.CertHolderID,
                    m.IMSID,
                    m.ClusterID
                ) b 
                ON a.ProgID = b.ProgID 
                AND a.CertHolderID = b.CertHolderID 
                AND a.IMSID = b.IMSID 
                AND a.ClusterID = b.ClusterID SET a.COC = b.total 
        WHERE a.ProgID = 7 ;";
		$query = $this->db->query($sql); //COC
		if ($query) {
			$results['calcCOCFromDetail'] = "COC field updated.";
		} else {
			$results['calcCOCFromDetail'] .= "||COC field Failed to update";
		}
		return $results;
	}
        
        function calcCOCFromDetailWave3() {
        $sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `COC` = NULL WHERE ProgID IN (12,14);";
        $query = $this->db->query($sql);

        $sql = "UPDATE 
ktv_certification_progress_jbcocoa_ims_district_report a
JOIN (
SELECT
	n.ProgID,
	m.CertHolderID,
	m.CertificateHolders,
	m.IMSID,
	m.ClusterID,
	Count( m.ParticipantID ) AS total 
FROM
	ktv_jbcocoa_summary_coc AS m
	LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID 
WHERE
	DATE( m.DateGenerated ) = DATE(NOW()) 
GROUP BY
	n.ProgID,
	m.CertHolderID,
	m.IMSID,
	m.ClusterID 
) b ON a.ProgID = b.ProgID 
	AND a.CertHolderID = b.CertHolderID 
	AND a.IMSID = b.IMSID 
	AND a.ClusterID = b.ClusterID 
	SET a.COC = b.total 
WHERE
	a.ProgID IN (12,14);";
        $query = $this->db->query($sql);
        if ($query) {
            $results['calcCOCFromDetailWave3'] = "calcCOCFromDetailWave3 field updated.";
        } else {
            $results['calcCOCFromDetailWave3'] .= "||calcCOCFromDetailWave3 field Failed to update";
        }
        return $results;
    }

    function calcCOCR1FromDetail()
	{
         //Reset COCR1 Fields
		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `COCR1` = NULL WHERE ProgID = 7";
		$this->db->query($sql);
         // ===============//
		$sql = "UPDATE 
            ktv_certification_progress_jbcocoa_ims_district_report a 
            JOIN 
                (SELECT
                    n.ProgID,
                    m.CertHolderID,
                    m.CertificateHolders,
                    m.IMSID,
                    m.ClusterID,
                    Count(m.ParticipantID) AS total
                FROM
                    ktv_jbcocoa_summary_coc_refresh_one AS m
                LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID
                WHERE
                    DATE(m.DateGenerated) = DATE(NOW())
                GROUP BY
                    n.ProgID,
                    m.CertHolderID,
                    m.IMSID,
                    m.ClusterID
                ) b 
                ON a.ProgID = b.ProgID 
                AND a.CertHolderID = b.CertHolderID 
                AND a.IMSID = b.IMSID 
                AND a.ClusterID = b.ClusterID SET a.COCR1 = b.total 
        WHERE a.ProgID = 7 ;";
		$query = $this->db->query($sql); //COCR1
		if ($query) {
			$results['calcCOCR1FromDetail'] = "COCR1 field updated.";
		} else {
			$results['calcCOCR1FromDetail'] .= "||COCR1 field Failed to update";
		}
		return $results;
	}
	
	function calcCOCR2FromDetail()
	{
         //Reset COCR2 Fields
		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `COCR2` = NULL WHERE ProgID = 7";
		$this->db->query($sql);
         // ===============//
		$sql = "UPDATE 
            ktv_certification_progress_jbcocoa_ims_district_report a 
            JOIN 
                (SELECT
                    n.ProgID,
                    m.CertHolderID,
                    m.CertificateHolders,
                    m.IMSID,
                    m.ClusterID,
                    Count(m.ParticipantID) AS total
                FROM
                    ktv_jbcocoa_summary_coc_refresh_two AS m
                LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID
                WHERE
                    DATE(m.DateGenerated) = DATE(NOW())
                GROUP BY
                    n.ProgID,
                    m.CertHolderID,
                    m.IMSID,
                    m.ClusterID
                ) b 
                ON a.ProgID = b.ProgID 
                AND a.CertHolderID = b.CertHolderID 
                AND a.IMSID = b.IMSID 
                AND a.ClusterID = b.ClusterID SET a.COCR2 = b.total 
        WHERE a.ProgID = 7 ;";
		$query = $this->db->query($sql); //COCR2
		if ($query) {
			$results['calcCOCR2FromDetail'] = "COCR2 field updated.";
		} else {
			$results['calcCOCR2FromDetail'] .= "||COCR2 field Failed to update";
		}
		return $results;
	}
	
	function calcFinalTrainingFromDetail()
	{
          //Reset TrainingFinal Fields
		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `TrainingFinal` = NULL WHERE ProgID = 7";
		$this->db->query($sql);
          // ===============//
		$sql = "UPDATE 
            ktv_certification_progress_jbcocoa_ims_district_report a 
            JOIN 
                (SELECT
                    n.ProgID,
                    m.CertHolderID,
                    m.CertificateHolders,
                    m.IMSID,
                    m.ClusterID,
                    Count(m.ParticipantID) AS total
                FROM
                    ktv_jbcocoa_summary_final_training AS m
                LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID
                WHERE
                    DATE(m.DateGenerated) = DATE(NOW()) and m.FinalTrainingStatus = 'passed'
                GROUP BY
                    n.ProgID,
                    m.CertHolderID,
                    m.IMSID,
                    m.ClusterID
                ) b 
                ON a.ProgID = b.ProgID 
                AND a.CertHolderID = b.CertHolderID 
                AND a.IMSID = b.IMSID 
                AND a.ClusterID = b.ClusterID SET a.TrainingFinal = b.total 
        WHERE a.ProgID = 7 ;";
		$query = $this->db->query($sql); //TrainingFinal
		if ($query) {
			$results['calcFinalTrainingFromDetail'] = "TrainingFinal field updated.";
		} else {
			$results['calcFinalTrainingFromDetail'] .= "||TrainingFinal field Failed to update";
		}
		return $results;
	}
	
	function calcGAPFromDetail()
	{
         //Reset GAP Fields
		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `GAP` = NULL WHERE ProgID = 7";
		$this->db->query($sql);
         // ===============//
		$sql = "UPDATE 
            ktv_certification_progress_jbcocoa_ims_district_report a 
            JOIN 
                (SELECT
                    n.ProgID,
                    m.CertHolderID,
                    m.CertificateHolders,
                    m.IMSID,
                    m.ClusterID,
                    Count(m.ParticipantID) AS total
                FROM
                    ktv_jbcocoa_summary_gap_first_year AS m
                LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID
                WHERE
                    DATE(m.DateGenerated) = DATE(NOW())
                GROUP BY
                    n.ProgID,
                    m.CertHolderID,
                    m.IMSID,
                    m.ClusterID
                ) b 
                ON a.ProgID = b.ProgID 
                AND a.CertHolderID = b.CertHolderID 
                AND a.IMSID = b.IMSID 
                AND a.ClusterID = b.ClusterID SET a.GAP = b.total 
        WHERE a.ProgID = 7 ;";
		$query = $this->db->query($sql); //GAP
		if ($query) {
			$results['calcGAPFromDetail'] = "GAP field updated.";
		} else {
			$results['calcGAPFromDetail'] .= "||GAP field Failed to update";
		}
		return $results;
	}
	
	function calcGAPRFromDetail()
	{
         //Reset GAPR Fields
		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `GAPR` = NULL WHERE ProgID = 7";
		$this->db->query($sql);
         // ===============//
		$sql = "UPDATE 
            ktv_certification_progress_jbcocoa_ims_district_report a 
            JOIN 
                (SELECT
                    n.ProgID,
                    m.CertHolderID,
                    m.CertificateHolders,
                    m.IMSID,
                    m.ClusterID,
                    Count(m.ParticipantID) AS total
                FROM
                    ktv_jbcocoa_summary_gap_refresh AS m
                LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID
                WHERE
                    DATE(m.DateGenerated) = DATE(NOW())
                GROUP BY
                    n.ProgID,
                    m.CertHolderID,
                    m.IMSID,
                    m.ClusterID
                ) b 
                ON a.ProgID = b.ProgID 
                AND a.CertHolderID = b.CertHolderID 
                AND a.IMSID = b.IMSID 
                AND a.ClusterID = b.ClusterID SET a.GAPR = b.total 
        WHERE a.ProgID = 7 ;";
		$query = $this->db->query($sql); //GAPR
		if ($query) {
			$results['calcGAPRFromDetail'] = "GAPR field updated.";
		} else {
			$results['calcGAPRFromDetail'] .= "||GAPR field Failed to update";
		}
		return $results;
	}
	
	function calcIMSSupportFromDetail()
	{
         //Reset IMSSupport Fields
		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `IMSSupport` = NULL";
		$this->db->query($sql);
         // ===============//
		$sql = "UPDATE 
            ktv_certification_progress_jbcocoa_ims_district_report a 
            JOIN 
                (SELECT
                    n.ProgID,
                    m.CertHolderID,
                    m.CertificateHolders,
                    m.IMSID,
                    m.ClusterID,
                    SUM(m.NetKgCH) AS total
                FROM
                    ktv_jbcocoa_summary_ims_support AS m
                LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID
                WHERE
                    DATE(m.DateGenerated) = DATE(NOW())
                GROUP BY
                    n.ProgID,
                    m.CertHolderID,
                    m.IMSID,
                    m.ClusterID
                ) b 
                ON a.ProgID = b.ProgID 
                AND a.CertHolderID = b.CertHolderID 
                AND a.IMSID = b.IMSID 
                AND a.ClusterID = b.ClusterID SET a.IMSSupport = b.total 
          ";
		$query = $this->db->query($sql); //IMSSupport
		if ($query) {
			$results['calcIMSSupportFromDetail'] = "IMSSupport field updated.";
		} else {
			$results['calcIMSSupportFromDetail'] .= "||IMSSupport field Failed to update";
		}
		return $results;
	}
	
	function calcIMSTrainingsFromDetail()
	{
         //Reset IMStrainings Fields
		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `IMStrainings` = NULL WHERE ProgID = 7";
		$this->db->query($sql);
         // ===============//
		$sql = "UPDATE 
            ktv_certification_progress_jbcocoa_ims_district_report a 
            JOIN 
                (SELECT
                    n.ProgID,
                    m.CertHolderID,
                    m.CertificateHolders,
                    m.IMSID,
                    m.ClusterID,
                    Count(m.ParticipantID) AS total
                FROM
                    ktv_jbcocoa_summary_ims_training AS m
                LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID
                WHERE
                    DATE(m.DateGenerated) = DATE(NOW())
                GROUP BY
                    n.ProgID,
                    m.CertHolderID,
                    m.IMSID,
                    m.ClusterID
                ) b 
                ON a.ProgID = b.ProgID 
                AND a.CertHolderID = b.CertHolderID 
                AND a.IMSID = b.IMSID 
                AND a.ClusterID = b.ClusterID SET a.IMStrainings = b.total 
        WHERE a.ProgID = 7 ;";
		$query = $this->db->query($sql); //IMStrainings
		if ($query) {
			$results['calcIMStrainingsFromDetail'] = "IMStrainings field updated.";
		} else {
			$results['calcIMStrainingsFromDetail'] .= "||IMStrainings field Failed to update";
		}
		return $results;
	}
	
	function calcICSEquipmentFromDetail()
	{
         //Reset ICSEquipment Fields
		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `ICSEquipment` = NULL WHERE ProgID = 7";
		$this->db->query($sql);
         // ===============//
		$sql = "UPDATE 
            ktv_certification_progress_jbcocoa_ims_district_report a 
            JOIN 
                (SELECT
                    n.ProgID,
                    m.CertHolderID,
                    m.CertificateHolders,
                    m.IMSID,
                    m.ClusterID,
                    Count(m.FarmerID) AS total
                FROM
                    ktv_jbcocoa_summary_ics_equipment AS m
                LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID
                WHERE
                    DATE(m.DateGenerated) = DATE(NOW())
                GROUP BY
                    n.ProgID,
                    m.CertHolderID,
                    m.IMSID,
                    m.ClusterID
                ) b 
                ON a.ProgID = b.ProgID 
                AND a.CertHolderID = b.CertHolderID 
                AND a.IMSID = b.IMSID 
                AND a.ClusterID = b.ClusterID SET a.ICSEquipment = b.total 
        WHERE a.ProgID = 7 ;";
		$query = $this->db->query($sql); //ICSEquipment
		if ($query) {
			$results['calcICSEquipmentFromDetail'] = "ICSEquipment field updated.";
		} else {
			$results['calcICSEquipmentFromDetail'] .= "||ICSEquipment field Failed to update";
		}
		return $results;
	}
	
	function calcICS1FromDetail()
	{
         //Reset ICS1 Fields
		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `ICS1` = NULL WHERE ProgID = 7";
		$this->db->query($sql);
         // ===============//
		$sql = "UPDATE 
            ktv_certification_progress_jbcocoa_ims_district_report a 
            JOIN 
                (SELECT
                    n.ProgID,
                    m.CertHolderID,
                    m.CertificateHolders,
                    m.IMSID,
                    m.ClusterID,
                    Count(m.FarmerID) AS total,
                    sum(m.HaCertifiedCropArea) as HaCertifiedCropArea
                FROM
                    ktv_jbcocoa_summary_ics_one AS m
                LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID
                WHERE
                    DATE(m.DateGenerated) = DATE(NOW())
                GROUP BY
                    n.ProgID,
                    m.CertHolderID,
                    m.IMSID,
                    m.ClusterID
                ) b 
                ON a.ProgID = b.ProgID 
                AND a.CertHolderID = b.CertHolderID 
                AND a.IMSID = b.IMSID 
                AND a.ClusterID = b.ClusterID SET a.ICS1 = b.total
        WHERE a.ProgID = 7 ;";
		$query = $this->db->query($sql); //ICS1
		if ($query) {
			$results['calcICS1FromDetail'] = "ICS1 field updated.";
		} else {
			$results['calcICS1FromDetail'] .= "||ICS1 field Failed to update";
		}
		return $results;
	}

    function calcICS1HectareFromDetail()
    {
         //Reset ICS1 Fields
        $sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `ICS1Hectare` = NULL WHERE ProgID = 7";
        $this->db->query($sql);
         // ===============//
        $sql = "UPDATE 
            ktv_certification_progress_jbcocoa_ims_district_report a 
            JOIN(
                    SELECT
                        n.ProgID,
                        m.CertHolderID,
                        m.CertificateHolders,
                        m.IMSID,
                        m.ClusterID,
                        Count( m.FarmerID ) AS total,
                        sum( m.CertHectare ) AS HaCertifiedCropArea 
                    FROM
                        ktv_jbcocoa_summary_ics_one_hectare AS m
                        LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID 
                    WHERE
                        DATE( m.DateGenerated ) = DATE(NOW())
                    GROUP BY
                        n.ProgID,
                        m.CertHolderID,
                        m.IMSID,
                        m.ClusterID 
                ) b 
                ON a.ProgID = b.ProgID 
                AND a.CertHolderID = b.CertHolderID 
                AND a.IMSID = b.IMSID 
                AND a.ClusterID = b.ClusterID SET a.ICS1Hectare = b.HaCertifiedCropArea 
        WHERE a.ProgID = 7 ;";
        $query = $this->db->query($sql); //ICS1
        if ($query) {
            $results['calcICS1FromDetail'] = "ICS1 field updated.";
        } else {
            $results['calcICS1FromDetail'] .= "||ICS1 field Failed to update";
        }
        return $results;
    }
    
    function calcICS1HectareFromDetailWave3() {
        $sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `ICS1Hectare` = NULL WHERE ProgID = 12;";
        $query = $this->db->query($sql);

        $sql = "UPDATE 
ktv_certification_progress_jbcocoa_ims_district_report a
JOIN (
SELECT
	n.ProgID,
	m.CertHolderID,
	m.CertificateHolders,
	m.IMSID,
	m.ClusterID,
	Count( m.FarmerID ) AS total,
	sum( m.CertHectare ) AS HaCertifiedCropArea,
	m.DateGenerated
FROM
	ktv_jbcocoa_summary_ics_one_hectare AS m
	LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID 
WHERE
	DATE( m.DateGenerated ) = DATE( NOW() ) 
GROUP BY
	n.ProgID,
	m.CertHolderID,
	m.IMSID,
	m.ClusterID 
) b ON a.ProgID = b.ProgID 
	AND a.CertHolderID = b.CertHolderID 
	AND a.IMSID = b.IMSID 
	AND a.ClusterID = b.ClusterID 
	SET a.ICS1Hectare = b.HaCertifiedCropArea 
WHERE
	a.ProgID = 12;";
        $query = $this->db->query($sql); // ICS Survey Wave III
        if ($query) {
            $results['calcICS1HectareFromDetailWave3'] = "calcICS1HectareFromDetailWave3 field updated.";
        } else {
            $results['calcICS1HectareFromDetailWave3'] .= "||calcICS1HectareFromDetailWave3 field Failed to update";
        }
        return $results;
    }
    
    function calcICS1HectareFromDetailWave4() {
        $sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `ICS1Hectare` = NULL WHERE ProgID = 14;";
        $query = $this->db->query($sql);

        $sql = "UPDATE 
ktv_certification_progress_jbcocoa_ims_district_report a
JOIN (
SELECT
	n.ProgID,
	m.CertHolderID,
	m.CertificateHolders,
	m.IMSID,
	m.ClusterID,
	Count( m.FarmerID ) AS total, # cuplik ini di Wave4
	sum( m.CertHectare ) AS HaCertifiedCropArea,
	m.DateGenerated
FROM
	ktv_jbcocoa_summary_ics_one_hectare AS m
	LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID 
WHERE
	DATE( m.DateGenerated ) = DATE( NOW() ) 
GROUP BY
	n.ProgID,
	m.CertHolderID,
	m.IMSID,
	m.ClusterID 
) b ON a.ProgID = b.ProgID 
	AND a.CertHolderID = b.CertHolderID 
	AND a.IMSID = b.IMSID 
	AND a.ClusterID = b.ClusterID 
	SET a.ICS1Hectare = b.total 
WHERE
	a.ProgID = 14;";
        $query = $this->db->query($sql); // ICS Survey Wave III
        if ($query) {
            $results['calcICS1HectareFromDetailWave4'] = "calcICS1HectareFromDetailWave4 field updated.";
        } else {
            $results['calcICS1HectareFromDetailWave4'] .= "||calcICS1HectareFromDetailWave4 field Failed to update";
        }
        return $results;
    }

    function calcICS2FromDetail()
	{
         //Reset ICS2 Fields
		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `ICS2` = NULL WHERE ProgID = 7";
		$this->db->query($sql);
         // ===============//
		$sql = "UPDATE 
            ktv_certification_progress_jbcocoa_ims_district_report a 
            JOIN 
                (SELECT
                    n.ProgID,
                    m.CertHolderID,
                    m.CertificateHolders,
                    m.IMSID,
                    m.ClusterID,
                    Count(m.FarmerID) AS total
                FROM
                    ktv_jbcocoa_summary_ics_two AS m
                LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID
                WHERE
                    DATE(m.DateGenerated) = DATE(NOW())
                GROUP BY
                    n.ProgID,
                    m.CertHolderID,
                    m.IMSID,
                    m.ClusterID
                ) b 
                ON a.ProgID = b.ProgID 
                AND a.CertHolderID = b.CertHolderID 
                AND a.IMSID = b.IMSID 
                AND a.ClusterID = b.ClusterID SET a.ICS2 = b.total 
        WHERE a.ProgID = 7 ;";
		$query = $this->db->query($sql); //ICS2
		if ($query) {
			$results['calcICS2FromDetail'] = "ICS2 field updated.";
		} else {
			$results['calcICS2FromDetail'] .= "||ICS2 field Failed to update";
		}
		return $results;
	}
        
    function calcICS2FromDetailWave3() {
        $sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `ICS2` = NULL WHERE ProgID IN (12,14);";
        $query = $this->db->query($sql);

        $sql = "UPDATE ktv_certification_progress_jbcocoa_ims_district_report a
JOIN (
SELECT
	n.ProgID,
	m.CertHolderID,
	m.CertificateHolders,
	m.IMSID,
	m.ClusterID,
	Count( m.FarmerID ) AS total 
FROM
	ktv_jbcocoa_summary_ics_two AS m
	LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID 
WHERE
	DATE( m.DateGenerated ) = DATE(NOW()) 
GROUP BY
	n.ProgID,
	m.CertHolderID,
	m.IMSID,
	m.ClusterID 
) b ON a.ProgID = b.ProgID 
	AND a.CertHolderID = b.CertHolderID 
	AND a.IMSID = b.IMSID 
	AND a.ClusterID = b.ClusterID 
	SET a.ICS2 = b.total 
WHERE
	a.ProgID IN (12,14);";
        $query = $this->db->query($sql);
        if ($query) {
            $results['calcICS2FromDetailWave3'] = "calcICS2FromDetailWave3 field updated.";
        } else {
            $results['calcICS2FromDetailWave3'] .= "||calcICS2FromDetailWave3 field Failed to update";
        }
        return $results;
    }

    function calcICS0FromDetail()
	{
         //Reset ICS0 Fields
		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `ICS0` = NULL WHERE ProgID = 7";
		$this->db->query($sql);
         // ===============//
		$sql = "UPDATE 
            ktv_certification_progress_jbcocoa_ims_district_report a 
            JOIN 
                (SELECT
                    n.ProgID,
                    m.CertHolderID,
                    m.CertificateHolders,
                    m.IMSID,
                    m.ClusterID,
                    Count(m.FarmerID) AS total
                FROM
                    ktv_jbcocoa_summary_ics_zero AS m
                LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID
                WHERE
                    DATE(m.DateGenerated) = DATE(NOW())
                GROUP BY
                    n.ProgID,
                    m.CertHolderID,
                    m.IMSID,
                    m.ClusterID
                ) b 
                ON a.ProgID = b.ProgID 
                AND a.CertHolderID = b.CertHolderID 
                AND a.IMSID = b.IMSID 
                AND a.ClusterID = b.ClusterID SET a.ICS0 = b.total 
        WHERE a.ProgID = 7 ;";
		$query = $this->db->query($sql); //ICS0
		if ($query) {
			$results['calcICS0FromDetail'] = "ICS0 field updated.";
		} else {
			$results['calcICS0FromDetail'] .= "||ICS0 field Failed to update";
		}
		return $results;
	}
        
        function calcIMSAFL1FromDetailWave3() {
        $sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `AFL1` = NULL WHERE ProgID IN (12,14);";
        $query = $this->db->query($sql);

        $sql = "UPDATE 
ktv_certification_progress_jbcocoa_ims_district_report a
JOIN (
    SELECT
            n.ProgID,
            m.CertHolderID,
            m.CertificateHolders,
            m.IMSID,
            m.ClusterID,
            Count( m.FarmerID ) AS total 
    FROM
            ktv_jbcocoa_summary_approved_farmer_list AS m
            LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID 
    WHERE
            DATE( m.DateGenerated ) = DATE(NOW()) 
    GROUP BY
            n.ProgID,
            m.CertHolderID,
            m.IMSID,
            m.ClusterID 
) b ON a.ProgID = b.ProgID 
    AND a.CertHolderID = b.CertHolderID 
    AND a.IMSID = b.IMSID 
    AND a.ClusterID = b.ClusterID 
    SET a.AFL1 = b.total 
WHERE
	a.ProgID IN (12,14);";
        $query = $this->db->query($sql);
        if ($query) {
            $results['calcICS0FromDetailWave3'] = "calcICS0FromDetailWave3 field updated.";
        } else {
            $results['calcICS0FromDetailWave3'] .= "||calcICS0FromDetailWave3 field Failed to update";
        }
        return $results;
    }

    function calcICS0ActiveJoinFromDetail()
	{
         //Reset ICS0ActiveJoin Fields
		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `ICS0ActiveJoin` = NULL WHERE ProgID = 7";
		$this->db->query($sql);
         // ===============//
		$sql = "UPDATE 
            ktv_certification_progress_jbcocoa_ims_district_report a 
            JOIN 
                (SELECT
                    n.ProgID,
                    m.CertHolderID,
                    m.CertificateHolders,
                    m.IMSID,
                    m.ClusterID,
                    Count(m.FarmerID) AS total
                FROM
                    ktv_jbcocoa_summary_ics_zero_active_join AS m
                LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID
                WHERE
                    DATE(m.DateGenerated) = DATE(NOW())
                GROUP BY
                    n.ProgID,
                    m.CertHolderID,
                    m.IMSID,
                    m.ClusterID
                ) b 
                ON a.ProgID = b.ProgID 
                AND a.CertHolderID = b.CertHolderID 
                AND a.IMSID = b.IMSID 
                AND a.ClusterID = b.ClusterID SET a.ICS0ActiveJoin = b.total 
        WHERE a.ProgID = 7 ;";
		$query = $this->db->query($sql); //ICS0ActiveJoin
		if ($query) {
			$results['calcICS0ActiveJoinFromDetail'] = "ICS0ActiveJoin field updated.";
		} else {
			$results['calcICS0ActiveJoinFromDetail'] .= "||ICS0ActiveJoin field Failed to update";
		}
		return $results;
	}
	
	function calcICS0ActiveNotJoinFromDetail()
	{
         //Reset ICS0ActiveNotJoin Fields
		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `ICS0ActiveNotJoin` = NULL WHERE ProgID = 7";
		$this->db->query($sql);
         // ===============//
		$sql = "UPDATE 
            ktv_certification_progress_jbcocoa_ims_district_report a 
            JOIN 
                (SELECT
                    n.ProgID,
                    m.CertHolderID,
                    m.CertificateHolders,
                    m.IMSID,
                    m.ClusterID,
                    Count(m.FarmerID) AS total
                FROM
                    ktv_jbcocoa_summary_ics_zero_active_not_join AS m
                LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID
                WHERE
                    DATE(m.DateGenerated) = DATE(NOW())
                GROUP BY
                    n.ProgID,
                    m.CertHolderID,
                    m.IMSID,
                    m.ClusterID
                ) b 
                ON a.ProgID = b.ProgID 
                AND a.CertHolderID = b.CertHolderID 
                AND a.IMSID = b.IMSID 
                AND a.ClusterID = b.ClusterID SET a.ICS0ActiveNotJoin = b.total 
        WHERE a.ProgID = 7 ;";
		$query = $this->db->query($sql); //ICS0ActiveNotJoin
		if ($query) {
			$results['calcICS0ActiveNotJoinFromDetail'] = "ICS0ActiveNotJoin field updated.";
		} else {
			$results['calcICS0ActiveNotJoinFromDetail'] .= "||ICS0ActiveNotJoin field Failed to update";
		}
		return $results;
	}
	
	function calcICS0InactiveFromDetail()
	{
         //Reset ICS0Inactive Fields
		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `ICS0Inactive` = NULL WHERE ProgID = 7";
		$this->db->query($sql);
         // ===============//
		$sql = "UPDATE 
            ktv_certification_progress_jbcocoa_ims_district_report a 
            JOIN 
                (SELECT
                    n.ProgID,
                    m.CertHolderID,
                    m.CertificateHolders,
                    m.IMSID,
                    m.ClusterID,
                    Count(m.FarmerID) AS total
                FROM
                    ktv_jbcocoa_summary_ics_zero_notactive AS m
                LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID
                WHERE
                    DATE(m.DateGenerated) = DATE(NOW())
                GROUP BY
                    n.ProgID,
                    m.CertHolderID,
                    m.IMSID,
                    m.ClusterID
                ) b 
                ON a.ProgID = b.ProgID 
                AND a.CertHolderID = b.CertHolderID 
                AND a.IMSID = b.IMSID 
                AND a.ClusterID = b.ClusterID SET a.ICS0Inactive = b.total 
        WHERE a.ProgID = 7 ;";
		$query = $this->db->query($sql); //ICS0Inactive
		if ($query) {
			$results['calcICS0InactiveFromDetail'] = "ICS0Inactive field updated.";
		} else {
			$results['calcICS0InactiveFromDetail'] .= "||ICS0Inactive field Failed to update";
		}
		return $results;
	}
	
	function calcMasterTrainingFromDetail()
	{
         //Reset MasterTotal Fields
		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `MasterTotal` = NULL WHERE ProgID = 7";
		$this->db->query($sql);
         // ===============//
		$sql = "UPDATE 
            ktv_certification_progress_jbcocoa_ims_district_report a 
            JOIN 
                (SELECT
                    n.ProgID,
                    m.CertHolderID,
                    m.CertificateHolders,
                    m.IMSID,
                    m.ClusterID,
                    Count(m.ParticipantID) AS total
                FROM
                    ktv_jbcocoa_summary_master_training AS m
                LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID
                WHERE
                    DATE(m.DateGenerated) = DATE(NOW())
                GROUP BY
                    n.ProgID,
                    m.CertHolderID,
                    m.IMSID,
                    m.ClusterID
                ) b 
                ON a.ProgID = b.ProgID 
                AND a.CertHolderID = b.CertHolderID 
                AND a.IMSID = b.IMSID 
                AND a.ClusterID = b.ClusterID SET a.MasterTotal = b.total 
        WHERE a.ProgID = 7 ;";
		$query = $this->db->query($sql); //MasterTraining
		if ($query) {
			$results['calcMasterTrainingFromDetail'] = "MasterTraining field updated.";
		} else {
			$results['calcMasterTrainingFromDetail'] .= "||MasterTraining field Failed to update";
		}
		return $results;
	}
	
	function calcSELFromDetail()
	{
         //Reset SEL Fields
		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `SEL` = NULL WHERE ProgID = 7";
		$this->db->query($sql);
         // ===============//
		$sql = "UPDATE 
            ktv_certification_progress_jbcocoa_ims_district_report a 
            JOIN 
                (SELECT
                    n.ProgID,
                    m.CertHolderID,
                    m.CertificateHolders,
                    m.IMSID,
                    m.ClusterID,
                    Count(m.ParticipantID) AS total
                FROM
                    ktv_jbcocoa_summary_selection AS m
                LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID
                WHERE
                    DATE(m.DateGenerated) = DATE(NOW())
                GROUP BY
                    n.ProgID,
                    m.CertHolderID,
                    m.IMSID,
                    m.ClusterID
                ) b 
                ON a.ProgID = b.ProgID 
                AND a.CertHolderID = b.CertHolderID 
                AND a.IMSID = b.IMSID 
                AND a.ClusterID = b.ClusterID SET a.SEL = b.total 
        WHERE a.ProgID = 7 ;";
		$query = $this->db->query($sql); //SEL
		if ($query) {
			$results['calcSELFromDetail'] = "SEL field updated.";
		} else {
			$results['calcSELFromDetail'] .= "||SEL field Failed to update";
		}
		return $results;
	}
	
	function calcSELY2FromDetail()
	{
         //Reset SELY2 Fields
		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `SELY2` = NULL WHERE ProgID = 7";
		$this->db->query($sql);
         // ===============//
		$sql = "UPDATE 
            ktv_certification_progress_jbcocoa_ims_district_report a 
            JOIN 
                (SELECT
                    n.ProgID,
                    m.CertHolderID,
                    m.CertificateHolders,
                    m.IMSID,
                    m.ClusterID,
                    Count(m.ParticipantID) AS total
                FROM
                    ktv_jbcocoa_summary_selection_second_year AS m
                LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID
                WHERE
                    DATE(m.DateGenerated) = DATE(NOW())
                GROUP BY
                    n.ProgID,
                    m.CertHolderID,
                    m.IMSID,
                    m.ClusterID
                ) b 
                ON a.ProgID = b.ProgID 
                AND a.CertHolderID = b.CertHolderID 
                AND a.IMSID = b.IMSID 
                AND a.ClusterID = b.ClusterID SET a.SELY2 = b.total 
        WHERE a.ProgID = 7 ;";
		$query = $this->db->query($sql); //SELY2
		if ($query) {
			$results['calcSELY2FromDetail'] = "SELY2 field updated.";
		} else {
			$results['calcSELY2FromDetail'] .= "||SELY2 field Failed to update";
		}
		return $results;
	}
	
	function calcSELY2YesFromDetail()
	{
         //Reset SELY2YES Fields
		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `SELY2YES` = NULL WHERE ProgID = 7";
		$this->db->query($sql);
         // ===============//
		$sql = "UPDATE 
            ktv_certification_progress_jbcocoa_ims_district_report a 
            JOIN 
                (SELECT
                    n.ProgID,
                    m.CertHolderID,
                    m.CertificateHolders,
                    m.IMSID,
                    m.ClusterID,
                    Count(m.ParticipantID) AS total
                FROM
                    ktv_jbcocoa_summary_selection_second_year_yes AS m
                LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID
                WHERE
                    DATE(m.DateGenerated) = DATE(NOW())
                GROUP BY
                    n.ProgID,
                    m.CertHolderID,
                    m.IMSID,
                    m.ClusterID
                ) b 
                ON a.ProgID = b.ProgID 
                AND a.CertHolderID = b.CertHolderID 
                AND a.IMSID = b.IMSID 
                AND a.ClusterID = b.ClusterID SET a.SELY2YES = b.total 
        WHERE a.ProgID = 7 ;";
		$query = $this->db->query($sql); //SELY2YES
		if ($query) {
			$results['calcSELY2YESFromDetail'] = "SELY2YES field updated.";
		} else {
			$results['calcSELY2YESFromDetail'] .= "||SELY2YES field Failed to update";
		}
		return $results;
	}
	
	function calcSELYesFromDetail()
	{
         //Reset SELYES Fields
		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `SELYES` = NULL WHERE ProgID = 7";
		$this->db->query($sql);
         // ===============//
		$sql = "UPDATE 
            ktv_certification_progress_jbcocoa_ims_district_report a 
            JOIN 
                (SELECT
                    n.ProgID,
                    m.CertHolderID,
                    m.CertificateHolders,
                    m.IMSID,
                    m.ClusterID,
                    Count(m.ParticipantID) AS total
                FROM
                    ktv_jbcocoa_summary_selection_yes AS m
                LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID
                WHERE
                    DATE(m.DateGenerated) = DATE(NOW())
                GROUP BY
                    n.ProgID,
                    m.CertHolderID,
                    m.IMSID,
                    m.ClusterID
                ) b 
                ON a.ProgID = b.ProgID 
                AND a.CertHolderID = b.CertHolderID 
                AND a.IMSID = b.IMSID 
                AND a.ClusterID = b.ClusterID SET a.SELYES = b.total 
        WHERE a.ProgID = 7 ;";
		$query = $this->db->query($sql); //SELYES
		if ($query) {
			$results['calcSELYESFromDetail'] = "SELYES field updated.";
		} else {
			$results['calcSELYESFromDetail'] .= "||SELYES field Failed to update";
		}
		return $results;
	}
	
	function calcSOCFromDetail()
	{
         //Reset SOL Fields
		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `SOL` = NULL WHERE ProgID = 7";
		$this->db->query($sql);
         // ===============//
		$sql = "UPDATE 
            ktv_certification_progress_jbcocoa_ims_district_report a 
            JOIN 
                (SELECT
                    n.ProgID,
                    m.CertHolderID,
                    m.CertificateHolders,
                    m.IMSID,
                    m.ClusterID,
                    Count(m.ParticipantID) AS total
                FROM
                    ktv_jbcocoa_summary_socialization AS m
                LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID
                WHERE
                    DATE(m.DateGenerated) = DATE(NOW())
                GROUP BY
                    n.ProgID,
                    m.CertHolderID,
                    m.IMSID,
                    m.ClusterID
                ) b 
                ON a.ProgID = b.ProgID 
                AND a.CertHolderID = b.CertHolderID 
                AND a.IMSID = b.IMSID 
                AND a.ClusterID = b.ClusterID SET a.SOL = b.total 
        WHERE a.ProgID = 7 ;";
		$query = $this->db->query($sql); //SOL
		if ($query) {
			$results['calcSOCFromDetail'] = "SOL field updated.";
		} else {
			$results['calcSOCFromDetail'] .= "||SOL field Failed to update";
		}
		return $results;
	}
        
        function calcSOCFromDetailWave3() {
        $sql = "UPDATE
   ktv_certification_progress_jbcocoa_ims_district_report SET FarmerReg = NULL WHERE ProgID IN (12,14);";
        $query = $this->db->query($sql);

        $sql = "UPDATE
   ktv_certification_progress_jbcocoa_ims_district_report a 
   JOIN
      (
         SELECT
            IMSID,
            ClusterID,
            COUNT(ParticipantID) jml,
            DateGenerated 
         FROM
            ktv_jbcocoa_summary_socialization 
         WHERE
            ProgID IN (12,14)
            AND DateGenerated = DATE(NOW()) 
         GROUP BY
            IMSID,
            ClusterID
      ) b 
      ON b.IMSID = a.IMSID 
      AND b.ClusterID = a.ClusterID 
SET
   a.FarmerReg = b.jml;";
        $query = $this->db->query($sql);
        if ($query) {
            $results['calcSOCFromDetailWave3'] = "calcSOCFromDetailWave3 field updated.";
        } else {
            $results['calcSOCFromDetailWave3'] .= "||calcSOCFromDetailWave3 field Failed to update";
        }
        return $results;
    }

    function calcTC1FromDetail()
	{
         //Reset TC Fields
		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `TC` = NULL WHERE ProgID = 6";
		$this->db->query($sql);
         // ===============//
		$sql = "UPDATE 
            ktv_certification_progress_jbcocoa_ims_district_report a 
            JOIN 
                (SELECT
                    n.ProgID,
                    m.CertHolderID,
                    m.CertHolderOrgName,
                    m.IMSID,
                    m.ClusterID,
                    Sum(m.netto_farmer_2) AS total,
                    Sum(m.bruto_farmer_2) AS total2
                FROM
                    ktv_jbcocoa_summary_tc_year_two AS m
                LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID
                WHERE
                    DATE(m.DateGenerated) = DATE(NOW()) AND m.ProgID = '6'
                GROUP BY
                    n.ProgID,
                    m.CertHolderID,
                    m.IMSID,
                    m.ClusterID
                ) b 
                ON a.ProgID = b.ProgID 
                AND a.CertHolderID = b.CertHolderID 
                AND a.IMSID = b.IMSID 
                AND a.ClusterID = b.ClusterID SET a.TC = b.total, a.CertifiedNetto = b.total,  a.CertifiedBruto = b.total2 
        WHERE a.ProgID = 6 ;";
		$query = $this->db->query($sql); //TC1
		if ($query) {
			$results['calcTC1FromDetail'] = "TC1 field updated.";
		} else {
			$results['calcTC1FromDetail'] .= "||TC1 field Failed to update";
		}
		return $results;
	}
	
	function calcTC2FromDetail()
	{
         //Reset TC Fields
		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `TC` = NULL WHERE ProgID = 7";
		$this->db->query($sql);
         // ===============//
		$sql = "UPDATE 
            ktv_certification_progress_jbcocoa_ims_district_report a 
            JOIN 
                (SELECT
                    n.ProgID,
                    m.CertHolderID,
                    m.CertHolderOrgName,
                    m.IMSID,
                    m.ClusterID,
                    Sum(m.netto_farmer_2) AS total,
                    Sum(m.bruto_farmer_2) AS total2
                FROM
                    ktv_jbcocoa_summary_tc_year_two AS m
                LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID
                WHERE
                    DATE(m.DateGenerated) = DATE(NOW()) AND m.ProgID = '7'
                GROUP BY
                    n.ProgID,
                    m.CertHolderID,
                    m.IMSID,
                    m.ClusterID
                ) b 
                ON a.ProgID = b.ProgID 
                AND a.CertHolderID = b.CertHolderID 
                AND a.IMSID = b.IMSID 
                AND a.ClusterID = b.ClusterID SET a.TC = b.total, a.CertifiedNetto = b.total,  a.CertifiedBruto = b.total2
        WHERE a.ProgID = 7 ;";
		$query = $this->db->query($sql); //TC2
		if ($query) {
			$results['calcTC2FromDetail'] = "TC2 field updated.";
		} else {
			$results['calcTC2FromDetail'] .= "||TC2 field Failed to update";
		}
		return $results;
     }
     
     
    function calcFarmXUserFromDetail() {
        $sql = "UPDATE ktv_certification_progress_jbcocoa_ims_district_report SET FarmXUsers=NULL WHERE ProgID=14;";
        $query = $this->db->query($sql);

        $sql = "UPDATE ktv_certification_progress_jbcocoa_ims_district_report a
JOIN (
SELECT
	n.ProgID,
	m.CertHolderID,
	m.CertificateHolders,
	m.IMSID,
	m.ClusterID,
	Count( m.id ) AS total,
	m.DateGenerated
FROM
	ktv_jbcocoa_summary_farmxuser AS m
	LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID 
WHERE
	DATE( m.DateGenerated ) = DATE(now()) 
GROUP BY
	n.ProgID,
	m.CertHolderID,
	m.IMSID,
	m.ClusterID 
	) b ON a.ProgID = b.ProgID 
	AND a.CertHolderID = b.CertHolderID 
	AND a.IMSID = b.IMSID 
	AND a.ClusterID = b.ClusterID 
	SET a.FarmXUsers = b.total 
WHERE
	a.ProgID = 14;";
        $query = $this->db->query($sql);
        if ($query) {
            $results['calcFarmXUserFromDetail'] = "calcFarmXUserFromDetail field updated.";
        } else {
            $results['calcFarmXUserFromDetail'] .= "||calcFarmXUserFromDetail field Failed to update";
        }
        return $results;
    }
     
     
    function calcFarmGUserFromDetail() {
        $sql = "UPDATE ktv_certification_progress_jbcocoa_ims_district_report SET FarmGUsers=NULL WHERE ProgID=14;";
        $query = $this->db->query($sql);

        $sql = "UPDATE ktv_certification_progress_jbcocoa_ims_district_report a
JOIN (
SELECT
	n.ProgID,
	m.CertHolderID,
	m.CertificateHolders,
	m.IMSID,
	m.ClusterID,
	Count( m.id ) AS total,
	m.DateGenerated
FROM
	ktv_jbcocoa_summary_farmguser AS m
	LEFT JOIN ktv_ims AS n ON n.IMSID = m.IMSID 
WHERE
	DATE( m.DateGenerated ) = DATE(now()) 
GROUP BY
	n.ProgID,
	m.CertHolderID,
	m.IMSID,
	m.ClusterID 
	) b ON a.ProgID = b.ProgID 
	AND a.CertHolderID = b.CertHolderID 
	AND a.IMSID = b.IMSID 
	AND a.ClusterID = b.ClusterID 
	SET a.FarmGUsers = b.total 
WHERE
	a.ProgID = 14;";
        $query = $this->db->query($sql);
        if ($query) {
            $results['calcFarmGUserFromDetail'] = "calcFarmGUserFromDetail field updated.";
        } else {
            $results['calcFarmGUserFromDetail'] .= "||calcFarmGUserFromDetail field Failed to update";
        }
        return $results;
    }
    
     function calcProgressGardenFromDetail()
	{
         //Reset ProgressGarden Fields
		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `ProgressGarden` = NULL WHERE ProgID = 7";
		$this->db->query($sql);
         // ===============//
		$sql = "UPDATE 
            ktv_certification_progress_jbcocoa_ims_district_report a 
            JOIN 
                (SELECT
                    m.ProgID,
                    m.CertHolderID,
                    m.CertificateHolders,
                    m.IMSID,
                    m.ClusterID,
                    Count(m.FarmerID) AS total
               FROM
                    ktv_jbcocoa_summary_progress_garden AS m
               WHERE
                    DATE(m.DateGenerated) = DATE(NOW())
               GROUP BY
                    m.ProgID,
                    m.CertHolderID,
                    m.IMSID,
                    m.ClusterID
                ) b 
                ON a.ProgID = b.ProgID 
                AND a.CertHolderID = b.CertHolderID 
                AND a.IMSID = b.IMSID 
                AND a.ClusterID = b.ClusterID SET a.ProgressGarden = b.total 
        WHERE a.ProgID = 7 ;";
		$query = $this->db->query($sql); //ProgressGarden
		if ($query) {
			$results['calcProgressGardenFromDetail'] = "ProgressGarden field updated.";
		} else {
			$results['calcProgressGardenFromDetail'] .= "||ProgressGarden field Failed to update";
		}
		return $results;
     }
     function calcProgressPolygonFromDetail()
	{
         //Reset ProgressPolygon Fields
		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `ProgressPolygon` = NULL WHERE ProgID = 7";
		$this->db->query($sql);
         // ===============//
		$sql = "UPDATE 
            ktv_certification_progress_jbcocoa_ims_district_report a 
            JOIN 
                (SELECT
                    m.ProgID,
                    m.CertHolderID,
                    m.CertificateHolders,
                    m.IMSID,
                    m.ClusterID,
                    Count(m.FarmerID) AS total
               FROM
                    ktv_jbcocoa_summary_progress_polygon AS m
               WHERE
                    DATE(m.DateGenerated) = DATE(NOW())
               GROUP BY
                    m.ProgID,
                    m.CertHolderID,
                    m.IMSID,
                    m.ClusterID
                ) b 
                ON a.ProgID = b.ProgID 
                AND a.CertHolderID = b.CertHolderID 
                AND a.IMSID = b.IMSID 
                AND a.ClusterID = b.ClusterID SET a.ProgressPolygon = b.total 
        WHERE a.ProgID = 7 ;";
		$query = $this->db->query($sql); //ProgressPolygon
		if ($query) {
			$results['calcProgressPolygonFromDetail'] = "ProgressPolygon field updated.";
		} else {
			$results['calcProgressPolygonFromDetail'] .= "||ProgressPolygon field Failed to update";
		}
		return $results;
     }
     function calcProgressPostHarvestFromDetail()
	{
         //Reset ProgressPostHarvest Fields
		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `ProgressPostHarvest` = NULL WHERE ProgID = 7";
		$this->db->query($sql);
         // ===============//
		$sql = "UPDATE 
            ktv_certification_progress_jbcocoa_ims_district_report a 
            JOIN 
                (SELECT
                    m.ProgID,
                    m.CertHolderID,
                    m.CertificateHolders,
                    m.IMSID,
                    m.ClusterID,
                    Count(m.FarmerID) AS total
               FROM
                    ktv_jbcocoa_summary_progress_post_harvest AS m
               WHERE
                    DATE(m.DateGenerated) = DATE(NOW())
               GROUP BY
                    m.ProgID,
                    m.CertHolderID,
                    m.IMSID,
                    m.ClusterID
                ) b 
                ON a.ProgID = b.ProgID 
                AND a.CertHolderID = b.CertHolderID 
                AND a.IMSID = b.IMSID 
                AND a.ClusterID = b.ClusterID SET a.ProgressPostHarvest = b.total 
        WHERE a.ProgID = 7 ;";
		$query = $this->db->query($sql); //ProgressPostHarvest
		if ($query) {
			$results['calcProgressPostHarvestFromDetail'] = "ProgressPostHarvest field updated.";
		} else {
			$results['calcProgressPostHarvestFromDetail'] .= "||ProgressPostHarvest field Failed to update";
		}
		return $results;
     }
     function calcProgressPPIFromDetail()
	{
         //Reset ProgressPPI Fields
		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `ProgressPPI` = NULL WHERE ProgID = 7";
		$this->db->query($sql);
         // ===============//
		$sql = "UPDATE 
            ktv_certification_progress_jbcocoa_ims_district_report a 
            JOIN 
                (SELECT
                    m.ProgID,
                    m.CertHolderID,
                    m.CertificateHolders,
                    m.IMSID,
                    m.ClusterID,
                    Count(m.FarmerID) AS total
               FROM
                    ktv_jbcocoa_summary_progress_ppi AS m
               WHERE
                    DATE(m.DateGenerated) = DATE(NOW())
               GROUP BY
                    m.ProgID,
                    m.CertHolderID,
                    m.IMSID,
                    m.ClusterID
                ) b 
                ON a.ProgID = b.ProgID 
                AND a.CertHolderID = b.CertHolderID 
                AND a.IMSID = b.IMSID 
                AND a.ClusterID = b.ClusterID SET a.ProgressPPI = b.total 
        WHERE a.ProgID = 7 ;";
		$query = $this->db->query($sql); //ProgressPPI
		if ($query) {
			$results['calcProgressPPIFromDetail'] = "ProgressPPI field updated.";
		} else {
			$results['calcProgressPPIFromDetail'] .= "||ProgressPPI field Failed to update";
		}
		return $results;
     }
     function calcProgressCertificationFromDetail()
	{
         //Reset ProgressCertification Fields
		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `ProgressCertification` = NULL WHERE ProgID = 7";
		$this->db->query($sql);
         // ===============//
		$sql = "UPDATE 
            ktv_certification_progress_jbcocoa_ims_district_report a 
            JOIN 
                (SELECT
                    m.ProgID,
                    m.CertHolderID,
                    m.CertificateHolders,
                    m.IMSID,
                    m.ClusterID,
                    Count(m.FarmerID) AS total
               FROM
                    ktv_jbcocoa_summary_progress_certification AS m
               WHERE
                    DATE(m.DateGenerated) = DATE(NOW())
               GROUP BY
                    m.ProgID,
                    m.CertHolderID,
                    m.IMSID,
                    m.ClusterID
                ) b 
                ON a.ProgID = b.ProgID 
                AND a.CertHolderID = b.CertHolderID 
                AND a.IMSID = b.IMSID 
                AND a.ClusterID = b.ClusterID SET a.ProgressCertification = b.total 
        WHERE a.ProgID = 7 ;";
		$query = $this->db->query($sql); //ProgressCertification
		if ($query) {
			$results['calcProgressCertificationFromDetail'] = "ProgressCertification field updated.";
		} else {
			$results['calcProgressCertificationFromDetail'] .= "||ProgressCertification field Failed to update";
		}
		return $results;
     }
     function calcProgressAuditLogFromDetail()
	{
         //Reset ProgressAuditLog Fields
		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `ProgressAuditLog` = NULL WHERE ProgID = 7";
		$this->db->query($sql);
         // ===============//
		$sql = "UPDATE 
            ktv_certification_progress_jbcocoa_ims_district_report a 
            JOIN 
                (SELECT
                    m.ProgID,
                    m.CertHolderID,
                    m.CertificateHolders,
                    m.IMSID,
                    m.ClusterID,
                    Count(m.FarmerID) AS total
               FROM
                    ktv_jbcocoa_summary_progress_auditlog AS m
               WHERE
                    DATE(m.DateGenerated) = DATE(NOW())
               GROUP BY
                    m.ProgID,
                    m.CertHolderID,
                    m.IMSID,
                    m.ClusterID
                ) b 
                ON a.ProgID = b.ProgID 
                AND a.CertHolderID = b.CertHolderID 
                AND a.IMSID = b.IMSID 
                AND a.ClusterID = b.ClusterID SET a.ProgressAuditLog = b.total 
        WHERE a.ProgID = 7 ;";
		$query = $this->db->query($sql); //ProgressAuditLog
		if ($query) {
			$results['calcProgressAuditLogFromDetail'] = "ProgressAuditLog field updated.";
		} else {
			$results['calcProgressAuditLogFromDetail'] .= "||ProgressAuditLog field Failed to update";
		}
		return $results;
     }
     function calcProgressUpdatedFarmerFromDetail()
	{
         //Reset ProgressUpdatedFarmer Fields
		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `ProgressUpdatedFarmer` = NULL WHERE ProgID = 7";
		$this->db->query($sql);
         // ===============//
		$sql = "UPDATE 
            ktv_certification_progress_jbcocoa_ims_district_report a 
            JOIN 
                (SELECT
                    m.ProgID,
                    m.CertHolderID,
                    m.CertificateHolders,
                    m.IMSID,
                    m.ClusterID,
                    Count(m.FarmerID) AS total
               FROM
                    ktv_jbcocoa_summary_progress_farmer_updated AS m
               WHERE
                    DATE(m.DateGenerated) = DATE(NOW())
               GROUP BY
                    m.ProgID,
                    m.CertHolderID,
                    m.IMSID,
                    m.ClusterID
                ) b 
                ON a.ProgID = b.ProgID 
                AND a.CertHolderID = b.CertHolderID 
                AND a.IMSID = b.IMSID 
                AND a.ClusterID = b.ClusterID SET a.ProgressUpdatedFarmer = b.total 
        WHERE a.ProgID = 7 ;";
		$query = $this->db->query($sql); //ProgressUpdatedFarmer
		if ($query) {
			$results['calcProgressUpdatedFarmerFromDetail'] = "ProgressUpdatedFarmer field updated.";
		} else {
			$results['calcProgressUpdatedFarmerFromDetail'] .= "||ProgressUpdatedFarmer field Failed to update";
		}
		return $results;
     }
     function calcProgressUpdatedGardenStatusFromDetail()
	{
         //Reset ProgressUpdatedGardenStatus Fields
		$sql = "UPDATE `ktv_certification_progress_jbcocoa_ims_district_report` SET `ProgressUpdatedGardenStatus` = NULL WHERE ProgID = 7";
		$this->db->query($sql);
         // ===============//
		$sql = "UPDATE 
            ktv_certification_progress_jbcocoa_ims_district_report a 
            JOIN 
                (SELECT
                    m.ProgID,
                    m.CertHolderID,
                    m.CertificateHolders,
                    m.IMSID,
                    m.ClusterID,
                    Count(m.FarmerID) AS total
               FROM
                    ktv_jbcocoa_summary_progress_garden_status_updated AS m
               WHERE
                    DATE(m.DateGenerated) = DATE(NOW())
               GROUP BY
                    m.ProgID,
                    m.CertHolderID,
                    m.IMSID,
                    m.ClusterID
                ) b 
                ON a.ProgID = b.ProgID 
                AND a.CertHolderID = b.CertHolderID 
                AND a.IMSID = b.IMSID 
                AND a.ClusterID = b.ClusterID SET a.ProgressUpdatedGardenStatus = b.total 
        WHERE a.ProgID = 7 ;";
		$query = $this->db->query($sql); //ProgressUpdatedGardenStatus
		if ($query) {
			$results['calcProgressUpdatedGardenStatusFromDetail'] = "ProgressUpdatedGardenStatus field updated.";
		} else {
			$results['calcProgressUpdatedGardenStatusFromDetail'] .= "||ProgressUpdatedGardenStatus field Failed to update";
		}
		return $results;
     }
}
?>