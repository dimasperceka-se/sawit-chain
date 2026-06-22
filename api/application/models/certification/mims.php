<?php
//ini_set('display_errors',false);
//error_reporting(0);

class Mims extends CI_Model
{
	public function getReportToolsViewList($RepID,$IMSID,$start,$limit,$opsiLimit){
		//get querynya
		$sql="
		  SELECT
			a.RepSqlStatement AS sqlNya
			, a.RepName AS ReportName
		  FROM `ktv_ims_report_tools` as a
		  WHERE
			a.RepID = ?
		LIMIT 1";
		$query = $this->db->query($sql,array($RepID));
		$dataSql = $query->row_array();
		$sqlStatement = $dataSql['sqlNya'];
		//lengkapi querynya untuk penyesuaian jika ada filternya (end)

		$sqlStatementLimit = $sqlStatement." LIMIT ?,?";
		$query = $this->db->query(sprintf($sqlStatementLimit,$IMSID), array( (int) $start, (int) $limit));
		$result['data'] = $query->result_array();

		$queryTotal = $this->db->query(sprintf($sqlStatement,$IMSID));
		$data = $queryTotal->result_array();
		$result['total'] = count($data);

		return $result;
	}

	public function getReportTools($RepID,$IMSID){
	  //get querynya
	  $sql="
		  SELECT
			a.RepSqlStatement AS sqlNya
			, a.RepName AS ReportName
		  FROM `ktv_ims_report_tools` as a
		  WHERE
			a.RepID = ?
		LIMIT 1";
	  $query = $this->db->query($sql,array($RepID));
	  $dataSql = $query->row_array();
	  $sqlStatement = $dataSql['sqlNya'];
	  if($sqlStatement == ""){
		$results['success'] = false;
		$results['response'] = lang('Query not found');
		return $results;
	  }

	  //cek apakah query ada kata kunci INSERT, UPDATE, DELETE, DROP
	  if (
		strpos(strtolower($sqlStatement), 'insert ') !== false ||
		strpos(strtolower($sqlStatement), 'update ') !== false ||
		strpos(strtolower($sqlStatement), 'drop ') !== false ||
		strpos(strtolower($sqlStatement), 'delete ') !== false
	  ) {
		  $results['success'] = false;
	  }

	  //eksekusi query dari sql view
	  $query = $this->db->query(sprintf($sqlStatement,$IMSID));

	  // return $this->db->last_query();;
	  if (!empty($this->db->_error_message())) {
		  $results['success'] = false;
	  }else{

		  //data hasil query
		  $dataQuery = $query->result_array();

		  //susun urutan field (begin)
		  $increKolom = 0;
		  $dataKolom = array();
		  foreach ($dataQuery as $key => $value) {
			  foreach ($value as $key1 => $value1) {
				  $dataKolom[$increKolom]['name'] = $key1;
				  $increKolom++;
			  }
			  break;
		  }
		  //susun urutan field (end)

		  //susun grid kolom (begin)
		  $dataParamGridColumn = array();

		  $dataParamGridColumn[0]['text'] = lang('No');
		  $dataParamGridColumn[0]['xtype'] = 'rownumberer';
		  $dataParamGridColumn[0]['width'] = '4%';

		  $increGridKolom = 1;
		  for ($i=0; $i < count($dataKolom); $i++) {
			  $dataParamGridColumn[$increGridKolom]['text'] = $dataKolom[$i]['name'];
			  $dataParamGridColumn[$increGridKolom]['dataIndex'] = $dataKolom[$i]['name'];
			  $dataParamGridColumn[$increGridKolom]['width'] = '15%';
			  $increGridKolom++;
		  }
		  //susun grid kolom (end)

		  $results['success'] = true;
		  $results['fieldNya'] = $dataKolom;
		  $results['gridColumnNya'] = $dataParamGridColumn;
	  }

	  $results['ReportName'] = $dataSql['ReportName'];
	  return $results;
	}

	public function getCmbReportTools($type = null){
	  $sql = "
		SELECT
		  RepID as id,
		  RepName as label
		FROM `ktv_ims_report_tools`
		WHERE DataType = ?
			AND StatusCode = 'active'
		ORDER by RepName ASC
	  ";
	  $query = $this->db->query($sql,array($type));

	  $data["data"] = $query->result_array();
	  return $data;
	}

	public function readAllIMS($key, $start = 0, $limit = 50)
	{
		$this->load->model('muserprofile');
		$user = $this->muserprofile->getUserProfile();
		// echo '<pre>'; print_r($user); echo '</pre>';
		$where = '';
		if ($user['is_admin'] == 1 or $user['type'] == 'program') {
			// do nothing
		} elseif ($user['type'] == 'private') {
			$where .= " AND kfb.FirstBuyerPartnerID = {$user['PartnerID']}";
		} elseif ($user['type'] == 'cooperative' or $user['type'] == 'trader' or $user['type'] == 'warehouse') {
			// konversi type, kenapa tidak disamamakan antara staff dan supplychain (dan yang lain)
			$type = $user['type'] == 'cooperative' ? 'koperasi' : $user['type'];
			$where .= " AND kch.SupplychainID = (
SELECT
	so.SupplychainID
FROM ktv_certification_holders ch
JOIN view_tc_supplychain_org so ON so.SupplychainID = ch.SupplychainID
JOIN ktv_sup
WHERE
	so.StatusCode = 'active'
	AND so.ObjType = '{$type}'
	AND so.ObjID = {$user['ObjID']}
LIMIT 1
			)";
		} else {
			// no access
			$where = ' 0 = 1';
		}
		$sql = "SELECT SQL_CALC_FOUND_ROWS
				   ims.IMSID, ims.CertEventName, kso.Name HolderName, kcp.CertProgName, kcb.CertBodyName, kcc.ContactName, ims.CertificationStart, ims.CertificationEnd, ims.ExtensionDate, ims.SurveyNr, ims.Year, kpp.PartnerName FirstBuyer, GROUP_CONCAT(d.District) AS District
				FROM ktv_ims ims
				LEFT JOIN ktv_district d ON FIND_IN_SET(d.DistrictID,ims.CertDistrictID)
				LEFT JOIN ktv_certification_holders kch ON kch.CertHolderID=ims.CertHolderID
				LEFT JOIN view_tc_supplychain_org kso ON kso.SupplychainID=kch.SupplychainID
				LEFT JOIN ktv_ref_certification_program kcp ON kcp.CertProgID=kch.CertProgID
				LEFT JOIN ktv_certification_body kcb ON kcb.CertBodyID=ims.CertBodyID
				LEFT JOIN ktv_certification_body_contact kcc ON kcc.CertBodyContactID=ims.CertBodyContactID
				LEFT JOIN ktv_first_buyer kfb ON kfb.FirstBuyerID=ims.FirstBuyerID
				LEFT JOIN ktv_program_partner kpp ON kpp.PartnerID=kfb.FirstBuyerPartnerID
				WHERE 1 = 1 AND ims.StatusCode!='nullified'
				AND (kso.Name LIKE ? OR kcp.CertProgName LIKE ? OR kcb.CertBodyName LIKE ? OR kcc.ContactName LIKE ? OR kpp.PartnerName LIKE ?)
					-- filter --
				GROUP BY ims.IMSID
				ORDER BY kso.Name, kcp.CertProgName, ims.SurveyNr, ims.CertificationStart
				LIMIT ?, ?";
		$sql   = str_replace('-- filter --', $where, $sql);
		$query = $this->db->query($sql, array("%$key%", "%$key%", "%$key%", "%$key%", "%$key%", intval($start), intval($limit)));
		// echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
		$sql_total   = "SELECT FOUND_ROWS() AS total";
		$query_total = $this->db->query($sql_total);
		if ($query->num_rows() > 0) {
			$total = $query_total->row_array(0);
			return array(
				'data'  => $query->result_array(),
				'total' => $total['total'],
			);
		}
		return false;
	}

	public function listHolders()
	{
		$sql   = "SELECT DISTINCT a.SupplychainID id, CONCAT('[',b.ObjType,'] ',b.Name) label FROM ktv_certification_holders a LEFT JOIN view_tc_supplychain_org b ON a.SupplychainID=b.SupplychainID WHERE a.StatusCode!='nullified'";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
	}

	public function listPrograms($SupplychainID)
	{
		$sql   = "SELECT DISTINCT a.CertHolderID id, b.CertProgName label FROM ktv_certification_holders a LEFT JOIN ktv_ref_certification_program b ON a.CertProgID= b.CertProgID WHERE a.StatusCode != 'nullified' AND a.SupplychainID=?";
		$query = $this->db->query($sql, array($SupplychainID));
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
	}

	public function listCertBody()
	{
		$sql   = "SELECT CertBodyID id, CertBodyName label FROM ktv_certification_body WHERE StatusCode!='nullified'";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
	}

	public function listCertBodyContact($CertBodyID)
	{
		$sql   = "SELECT CertBodyContactID id, ContactName label FROM ktv_certification_body_contact WHERE StatusCode!='nullified' AND CertBodyID=?";
		$query = $this->db->query($sql, array($CertBodyID));
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
	}

	public function readCertificationHolderDetail($CertHolderID)
	{
		$sql    = "SELECT a.*, b.CertProgName FROM ktv_certification_holders a LEFT JOIN ktv_ref_certification_program b ON a.CertProgID=b.CertProgID WHERE a.CertHolderID=?";
		$query  = $this->db->query($sql, array($CertHolderID));
		$return = $query->result_array();
		return $return[0];
	}

	public function readCertificationBodyContactDetail($CertBodyContactID)
	{
		$sql    = "SELECT * FROM ktv_certification_body_contact WHERE StatusCode!='nullified' AND CertBodyContactID=?";
		$query  = $this->db->query($sql, array($CertBodyContactID));
		$return = $query->result_array();
		return $return[0];
	}

	public function listFirstBuyer()
	{
		$user = $this->muserprofile->getUserProfile();
		$sql  = "
			SELECT
				a.FirstBuyerID id,
				b.PartnerName label
			FROM ktv_first_buyer a
			LEFT JOIN ktv_program_partner b ON a.FirstBuyerPartnerID=b.PartnerID
			WHERE
				a.StatusCode!='nullified'
	-- where --
";
		$where  = '';
		$params = array();
		if ($user['is_admin'] == 1 or $user['type'] == 'program') {
			// do nothing
		} elseif ($user['type'] == 'private') {
			$where .= " AND a.FirstBuyerPartnerID = {$user['PartnerID']}";
		}
		$sql   = str_replace('-- where --', $where, $sql);
		$query = $this->db->query($sql);
		// echo "<pre>";
		// echo $this->db->last_query();
		// die;
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
	}

	public function readIMSDetail($IMSID)
	{
		$sql    = "SELECT a.*, b.SupplychainID, (SELECT COUNT(*) FROM ktv_certification_afl_farmer WHERE IMSID=a.IMSID) afl FROM ktv_ims a LEFT JOIN ktv_certification_holders b ON a.CertHolderID=b.CertHolderID WHERE IMSID=?";
		$query  = $this->db->query($sql, array($IMSID));
		$return = $query->result_array();
		return $return[0];
	}

	public function createIMS($CertHolderID, $CertBodyID, $CertBodyContactID, $FirstBuyerID, $SurveyNr, $Year, $CertificationStart, $CertificationEnd, $InternalStart, $InternalEnd, $ExternalDate, $ExternalStart, $ExternalEnd, $ExtensionDate, $ValidityStart, $ValidityEnd, $userid, $CertEventName, $CertDistrictID)
	{
		$sql = "
			INSERT INTO ktv_ims(CertHolderID, CertBodyID, CertBodyContactID, FirstBuyerID, SurveyNr, Year, CertificationStart, CertificationEnd, InternalStart, InternalEnd, ExternalDate, ExternalStart, ExternalEnd, ExtensionDate, ValidityStart, ValidityEnd, CreatedBy,DateCreated,StatusCode, CertEventName, CertDistrictID)
			VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,now(),'active',?,?)";
		$query = $this->db->query($sql, array($CertHolderID, $CertBodyID, $CertBodyContactID, $FirstBuyerID, $SurveyNr, $Year, $CertificationStart, $CertificationEnd, $InternalStart, $InternalEnd, $ExternalDate, $ExternalStart, $ExternalEnd, $ExtensionDate, $ValidityStart, $ValidityEnd, $userid, $CertEventName, $CertDistrictID));
		if ($query) {
			$results['success'] = true;
			$results['message'] = "record created.";
			$results['IMSID']   = $this->db->insert_id();
		} else {
			$results['success'] = false;
			$results['message'] = "Failed to create record";
		}
		return $results;
	}

	public function updateIMS($CertHolderID, $CertBodyID, $CertBodyContactID, $FirstBuyerID, $SurveyNr, $Year, $CertificationStart, $CertificationEnd, $InternalStart, $InternalEnd, $ExternalDate, $ExternalStart, $ExternalEnd, $ExtensionDate, $ValidityStart, $ValidityEnd, $userid, $IMSID, $CertEventName, $CertDistrictID)
	{
		$sql   = "UPDATE ktv_ims SET CertHolderID=?, CertBodyID=?, CertBodyContactID=?, FirstBuyerID=?, SurveyNr=?, Year=?, CertificationStart=?, CertificationEnd=?, InternalStart=?, InternalEnd=?, ExternalDate=?, ExternalStart=?, ExternalEnd=?, ExtensionDate=?, ValidityStart=?, ValidityEnd=?, LastModifiedBy=?,DateUpdated=NOW(),CertEventName=?,CertDistrictID=? WHERE IMSID=?";
		$query = $this->db->query($sql, array($CertHolderID, $CertBodyID, $CertBodyContactID, $FirstBuyerID, $SurveyNr, $Year, $CertificationStart, $CertificationEnd, $InternalStart, $InternalEnd, $ExternalDate, $ExternalStart, $ExternalEnd, $ExtensionDate, $ValidityStart, $ValidityEnd, $userid, $CertEventName, $CertDistrictID, $IMSID));
		if ($query) {
			$results['success'] = true;
			$results['message'] = "record updated.";
		} else {
			$results['success'] = false;
			$results['message'] = "Failed to update record.";
		}
		return $results;
	}

	public function deleteIMS($userid, $IMSID)
	{
		$sql = "
			UPDATE ktv_ims SET StatusCode='nullified', LastModifiedBy=?, DateUpdated=NOW() WHERE IMSID=?";
		$query = $this->db->query($sql, array($userid, $IMSID));
		if ($query) {
			$results['success'] = true;
			$results['message'] = "record deleted.";
		} else {
			$results['success'] = false;
			$results['message'] = "Failed to delete record.";
		}
		return $results;
	}

	public function readFarmers($IMSID, $key, $SurveyNr, $notcomplete, $start = 0, $limit = 50, $sortingField, $sortingDir)
	{
		if ($sortingField == "") {
			$sortingField = 'FarmerID';
		}

		if ($sortingDir == "") {
			$sortingDir = 'ASC';
		}

		if ($notcomplete == 'true') {
			$not1 = '';
			$not2 = '';
		} else {
			$not1 = '/*';
			$not2 = '*/';
		}
		$s1  = $SurveyNr != '' ? '' : '/*';
		$s2  = $SurveyNr != '' ? '' : '*/';
		$sql = "SELECT SQL_CALC_FOUND_ROWS
					a.IMSID, a.FarmerID,
					CASE
						WHEN a.StatusAudit = '1' THEN '" . lang('Yes') . "'
						WHEN a.StatusAudit = '2' THEN '" . lang('No') . "'
						ELSE '-'
					END AS StatusAudit,
					a.AuditRemark, b.FarmerName FarmerName, g.SurveyNr,
					CONCAT('[',cpg.CPGid,'] ',cpg.GroupName) FarmerGroup, v.Village,
					COUNT(DISTINCT CONCAT(g.FarmerID,'_',g.GardenNr)) CGarden,
					COUNT(DISTINCT CONCAT(cert.FarmerID,'_',cert.GardenNr)) CCertification,
					COUNT(DISTINCT CONCAT(adl.FarmerID,'_',adl.GardenNr)) CAudit,
					COUNT(DISTINCT ppi.FarmerID) CPPI,
					COUNT(DISTINCT ph.FarmerID) CPostHarvest, sd.SubDistrict
				FROM
					ktv_certification_pre_afl a
					LEFT JOIN ktv_ims i ON i.IMSID=a.IMSID
					LEFT JOIN ktv_members b ON a.FarmerID=b.FarmerID
					LEFT JOIN ktv_cpg cpg ON cpg.CPGid=b.CPGid
					LEFT JOIN ktv_village v ON v.VillageID=b.VillageID
					LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
					LEFT JOIN (
						SELECT b.*
						FROM (SELECT FarmerID, GardenNr, MAX(SurveyNr) SurveyNr FROM ktv_members_garden WHERE GardenNr!=0 GROUP BY FarmerID, GardenNr) a
						LEFT JOIN ktv_members_garden b ON a.FarmerID=b.FarmerID AND a.GardenNr=b.GardenNr AND a.SurveyNr=b.SurveyNr
					) g ON g.FarmerID=a.FarmerID AND (g.SurveyNr=0 OR g.SurveyNr=i.SurveyNr)
					LEFT JOIN (
						SELECT b.*
						FROM (SELECT FarmerID, GardenNr, MAX(SurveyNr) SurveyNr, Certification FROM ktv_certification WHERE GardenNr!=0 AND Certification!=0 GROUP BY FarmerID, GardenNr, Certification) a
						LEFT JOIN ktv_certification b ON a.FarmerID=b.FarmerID AND a.GardenNr=b.GardenNr AND a.SurveyNr=b.SurveyNr AND a.Certification=b.Certification
					) cert ON cert.FarmerID=a.FarmerID AND (cert.SurveyNr=0 OR cert.SurveyNr=i.SurveyNr)
					LEFT JOIN (
						SELECT b.*
						FROM (SELECT FarmerID, GardenNr, SurveyNr, Certification, MAX(ICSDate) ICSDate FROM ktv_certification_audit_log WHERE GardenNr!=0 AND Certification!=0 GROUP BY FarmerID, GardenNr, SurveyNr, Certification) a
						LEFT JOIN ktv_certification_audit_log b ON a.FarmerID=b.FarmerID AND a.GardenNr=b.GardenNr AND a.SurveyNr=b.SurveyNr AND a.Certification=b.Certification AND a.ICSDate=b.ICSDate
					) adl ON cert.FarmerID=adl.FarmerID AND cert.SurveyNr=adl.SurveyNr AND cert.GardenNr=adl.GardenNr AND cert.Certification=adl.Certification
					LEFT JOIN (SELECT FarmerID, MAX(SurveyNr) SurveyNr FROM ktv_ppiscore2012 GROUP BY FarmerID) ppi ON ppi.FarmerID=a.FarmerID AND (ppi.SurveyNr=0 OR ppi.SurveyNr=i.SurveyNr)
					LEFT JOIN (SELECT FarmerID, MAX(SurveyNr) SurveyNr FROM ktv_members_post_harvest GROUP BY FarmerID) ph ON ph.FarmerID=a.FarmerID AND (ph.SurveyNr=0 OR ph.SurveyNr=i.SurveyNr)
				WHERE
					a.StatusCode='active' AND a.IMSID=? AND (b.FarmerID LIKE ? OR b.FarmerName LIKE ? OR cpg.GroupName LIKE ? OR cpg.CPGid LIKE ? OR v.Village LIKE ?) $s1 AND g.SurveyNr=? $s2
					$not1 HAVING (CGarden = 0 OR CCertification=0 OR CAudit=0) OR (CCertification!=CGarden OR CAudit < CCertification) $not2
				GROUP BY a.FarmerID
				ORDER BY $sortingField $sortingDir
				LIMIT ?, ?";
		$query = $this->db->query($sql, array($IMSID, $key, "%$key%", "%$key%", $key, "%$key%", $SurveyNr, intval($start), intval($limit)));
		//echo "<pre>".$this->db->last_query();exit;
		$sql_total   = "SELECT FOUND_ROWS() AS total";
		$query_total = $this->db->query($sql_total);
		if ($query->num_rows() > 0) {
			$total = $query_total->row_array(0);
			return array(
				'data'  => $query->result_array(),
				'total' => $total['total'],
			);
		}
		return false;
	}

	public function readFarmersImsDetail($IMSID, $key, $start = 0, $limit = 50, $sortingField, $sortingDir)
	{
		if ($sortingField == "") {
			$sortingField = 'FarmerID';
		}

		if ($sortingDir == "") {
			$sortingDir = 'ASC';
		}

		$sql = "SELECT SQL_CALC_FOUND_ROWS
					a.IMSID, a.FarmerID,
					CASE
						WHEN a.StatusAudit = '1' THEN '" . lang('Yes') . "'
						WHEN a.StatusAudit = '2' THEN '" . lang('No') . "'
						ELSE '-'
					END AS StatusAudit,
					a.AuditRemark, b.FarmerName FarmerName, g.SurveyNr,
					CONCAT('[',cpg.CPGid,'] ',cpg.GroupName) FarmerGroup, v.Village,
					COUNT(DISTINCT CONCAT(g.FarmerID,'_',g.GardenNr)) CGarden,
					COUNT(DISTINCT CONCAT(cert.FarmerID,'_',cert.GardenNr)) CCertification,
					COUNT(DISTINCT CONCAT(adl.FarmerID,'_',adl.GardenNr)) CAudit,
					COUNT(DISTINCT ppi.FarmerID) CPPI,
					COUNT(DISTINCT ph.FarmerID) CPostHarvest, sd.SubDistrict
				FROM
					ktv_certification_pre_afl a
					LEFT JOIN ktv_ims i ON i.IMSID=a.IMSID
					LEFT JOIN ktv_members b ON a.FarmerID=b.FarmerID
					LEFT JOIN ktv_cpg cpg ON cpg.CPGid=b.CPGid
					LEFT JOIN ktv_village v ON v.VillageID=b.VillageID
					LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
					LEFT JOIN (
						SELECT b.*
						FROM (SELECT FarmerID, GardenNr, MAX(SurveyNr) SurveyNr FROM ktv_members_garden WHERE GardenNr!=0 GROUP BY FarmerID, GardenNr) a
						LEFT JOIN ktv_members_garden b ON a.FarmerID=b.FarmerID AND a.GardenNr=b.GardenNr AND a.SurveyNr=b.SurveyNr
					) g ON g.FarmerID=a.FarmerID AND (g.SurveyNr=0 OR g.SurveyNr=i.SurveyNr)
					LEFT JOIN (
						SELECT b.*
						FROM (SELECT FarmerID, GardenNr, MAX(SurveyNr) SurveyNr, Certification FROM ktv_certification WHERE GardenNr!=0 AND Certification!=0 GROUP BY FarmerID, GardenNr, Certification) a
						LEFT JOIN ktv_certification b ON a.FarmerID=b.FarmerID AND a.GardenNr=b.GardenNr AND a.SurveyNr=b.SurveyNr AND a.Certification=b.Certification
					) cert ON cert.FarmerID=a.FarmerID AND (cert.SurveyNr=0 OR cert.SurveyNr=i.SurveyNr)
					LEFT JOIN (
						SELECT b.*
						FROM (SELECT FarmerID, GardenNr, SurveyNr, Certification, MAX(ICSDate) ICSDate FROM ktv_certification_audit_log WHERE GardenNr!=0 AND Certification!=0 GROUP BY FarmerID, GardenNr, SurveyNr, Certification) a
						LEFT JOIN ktv_certification_audit_log b ON a.FarmerID=b.FarmerID AND a.GardenNr=b.GardenNr AND a.SurveyNr=b.SurveyNr AND a.Certification=b.Certification AND a.ICSDate=b.ICSDate
					) adl ON cert.FarmerID=adl.FarmerID AND cert.SurveyNr=adl.SurveyNr AND cert.GardenNr=adl.GardenNr AND cert.Certification=adl.Certification
					LEFT JOIN (SELECT FarmerID, MAX(SurveyNr) SurveyNr FROM ktv_ppiscore2012 GROUP BY FarmerID) ppi ON ppi.FarmerID=a.FarmerID AND (ppi.SurveyNr=0 OR ppi.SurveyNr=i.SurveyNr)
					LEFT JOIN (SELECT FarmerID, MAX(SurveyNr) SurveyNr FROM ktv_members_post_harvest GROUP BY FarmerID) ph ON ph.FarmerID=a.FarmerID AND (ph.SurveyNr=0 OR ph.SurveyNr=i.SurveyNr)
				WHERE
					a.StatusCode='active' AND a.IMSID=? AND (b.FarmerID LIKE ? OR b.FarmerName LIKE ? OR cpg.GroupName LIKE ? OR cpg.CPGid LIKE ? OR v.Village LIKE ?)
				GROUP BY a.FarmerID
				ORDER BY $sortingField $sortingDir
				LIMIT ?, ?";
		$query = $this->db->query($sql, array($IMSID, $key, "%$key%", "%$key%", $key, "%$key%", intval($start), intval($limit)));
		//echo "<pre>".$this->db->last_query();exit;
		$sql_total   = "SELECT FOUND_ROWS() AS total";
		$query_total = $this->db->query($sql_total);
		if ($query->num_rows() > 0) {
			$total = $query_total->row_array(0);
			return array(
				'data'  => $query->result_array(),
				'total' => $total['total'],
			);
		}
		return false;
	}

	public function readCandidateImsDetail($IMSID, $key, $start = 0, $limit = 50, $sortingField, $sortingDir,$callFrom='js_grid')
	{
		if ($sortingField == "") {
			$sortingField = 'FarmerID';
		}

		if ($sortingDir == "") {
			$sortingDir = 'ASC';
		}

		$sql = "SELECT
				a.CertEventDate
			FROM
				ktv_ims a
			WHERE
				a.`StatusCode` = 'active'
				AND a.`IMSID` = ?
			LIMIT 1";
		$query   = $this->db->query($sql, array($IMSID));
		$dataIMS = $query->row_array();
		$CertEventDate = $dataIMS['CertEventDate'];

		$sql="SELECT
				SQL_CALC_FOUND_ROWS
				a.IMSID,
				b.MemberDisplayID,
				b.MemberID FarmerID,
				IFNULL(cl.ClusterName,'-') AS ClusterName,
				CASE
					WHEN a.StatusAudit = '1'
					THEN 'Yes'
					WHEN a.StatusAudit = '2'
					THEN 'No'
					ELSE '-'
				END AS StatusAudit,
				a.AuditRemark,
				b.MemberName FarmerName,
				CONCAT('[', cpg.FarmerGroupID, '] ', cpg.GroupName) FarmerGroup,
				v.Village,
				sd.SubDistrict,
				(
					SELECT
						COUNT(sub_a.`MemberID`) AS TotalFarm
					FROM
						ktv_survey_plot_status sub_a
					WHERE
						sub_a.`MemberID` = a.FarmerID
						AND sub_a.ActiveStatus = '1'
				) AS TotalFarm,

				(
					SELECT
						IF(COUNT(sub_a.`DateUpdated`)>0,'Yes','No') AS BANYAK
					FROM
						ktv_members sub_a
					WHERE
						sub_a.`StatusCode` = 'active'
						AND DATE(sub_a.`DateUpdated`) >= DATE_FORMAT( '{$CertEventDate}' - INTERVAL 6 MONTH, '%Y/%m/%d' )
						AND sub_a.`MemberID` = a.FarmerID
				) AS FarmerUpdated,

				IF((
					SELECT
						sub_a.`MemberID` FarmerID
					FROM
						ktv_certification_pre_afl_garden sub_aflg
						INNER JOIN ktv_members sub_a ON sub_aflg.`FarmerID` = sub_a.`MemberID`

						LEFT JOIN ktv_survey_plot sub_gar ON 1=1
							AND sub_a.`MemberID` = sub_gar.`MemberID`
							AND sub_aflg.`GardenNr` = sub_gar.`PlotNr`
							AND sub_aflg.`SurveyNr` = sub_gar.`SurveyNr`

						LEFT JOIN ktv_survey_plot_status sub_gstat ON 1=1
							AND sub_a.`MemberID` = sub_gstat.`MemberID`
							AND sub_aflg.`GardenNr` = sub_gstat.`PlotNr`
					WHERE
						sub_a.`StatusCode` = 'active'
						AND sub_aflg.`IMSID` = {$IMSID}
						AND sub_aflg.`FarmerID` = a.FarmerID
						AND (
							(DATE(sub_a.`DateUpdated`) >= DATE_FORMAT( '{$CertEventDate}' - INTERVAL 6 MONTH, '%Y/%m/%d' )) #Farmer
							OR
							(
								DATE(sub_gar.`DateUpdated`) >= DATE_FORMAT( '{$CertEventDate}' - INTERVAL 6 MONTH, '%Y/%m/%d' )
								OR
								DATE(sub_gar.`DateCreated`) >= DATE_FORMAT( '{$CertEventDate}' - INTERVAL 6 MONTH, '%Y/%m/%d' )
							) # Garden Survey
							OR
							(
								(
									DATE(sub_gstat.`DateUpdated`) >= DATE_FORMAT( '{$CertEventDate}' - INTERVAL 6 MONTH, '%Y/%m/%d' )
									AND
									sub_gstat.`LastModifiedBy` != '1' #Exclude Admin
								)
								OR
								(
									DATE(sub_gstat.`DateCreated`) >= DATE_FORMAT( '{$CertEventDate}' - INTERVAL 6 MONTH, '%Y/%m/%d' )
									AND
									sub_gstat.`CreatedBy` != '1' #Exclude Admin
								)
							) # Garden Status
						)
					GROUP BY sub_a.`MemberID`
				) IS NOT NULL,'Yes','No') AS FarmerVisited,

				(
					SELECT
						COUNT(sub_gar.`MemberID`) AS BANYAK
					FROM
						ktv_certification_pre_afl_garden sub_a
						LEFT JOIN ktv_survey_plot sub_gar ON 1=1
							AND sub_a.`FarmerID` = sub_gar.`MemberID`
							AND sub_a.`SurveyNr` = sub_gar.`SurveyNr`
							AND sub_a.`GardenNr` = sub_gar.`PlotNr`
							AND (
								DATE(sub_gar.`DateUpdated`) >= DATE_FORMAT( '{$CertEventDate}' - INTERVAL 6 MONTH, '%Y/%m/%d' )
								OR
								DATE(sub_gar.`DateCreated`) >= DATE_FORMAT( '{$CertEventDate}' - INTERVAL 6 MONTH, '%Y/%m/%d' )
							)
					WHERE
						sub_a.`IMSID` = {$IMSID}
						AND sub_a.`FarmerID` = a.FarmerID

				) AS CGarden,

				(
					SELECT
						COUNT(sub_afl.`FarmerID`) AS BANYAK
					FROM
						ktv_certification_pre_afl_garden sub_afl

						LEFT JOIN ktv_ims sub_ims ON sub_afl.`IMSID` = sub_ims.`IMSID`
						LEFT JOIN ktv_certification_holders sub_hold ON sub_ims.CertHolderID = sub_hold.CertHolderID

						LEFT JOIN ktv_certification sub_cert ON 1=1
							AND sub_afl.`FarmerID` = sub_cert.`FarmerID`
							AND sub_afl.`GardenNr` = sub_cert.`GardenNr`
							AND sub_afl.`SurveyNr` = sub_cert.`SurveyNr`
							AND sub_hold.`CertProgID` = sub_cert.`Certification`
					WHERE
						sub_ims.`StatusCode` = 'active'
						AND sub_ims.`IMSID` = {$IMSID}
						AND sub_afl.`FarmerID` = a.FarmerID
						AND (
							DATE(sub_cert.`DateUpdated`) >= DATE_FORMAT( '{$CertEventDate}' - INTERVAL 6 MONTH, '%Y/%m/%d' )
							OR
							DATE(sub_cert.`DateCreated`) >= DATE_FORMAT( '{$CertEventDate}' - INTERVAL 6 MONTH, '%Y/%m/%d' )
						)
				) AS CCertification,

				(
					SELECT
						COUNT(sub_afl.`FarmerID`) AS BANYAK
					FROM
						ktv_certification_pre_afl_garden sub_afl

						LEFT JOIN ktv_ims sub_ims ON sub_afl.`IMSID` = sub_ims.`IMSID`
						LEFT JOIN ktv_certification_holders sub_hold ON sub_ims.CertHolderID = sub_hold.CertHolderID

						LEFT JOIN (
							SELECT
								au.`FarmerID`
								, au.`SurveyNr`
								, au.`GardenNr`
								, au.Certification
								, au.`ICSDate`
								, au.`DateCreated`
								, au.`DateUpdated`
							FROM
								(SELECT
									FarmerID,
									GardenNr,
									SurveyNr,
									Certification,
									MAX(ICSDate) ICSDate
								FROM
									ktv_certification_audit_log
								WHERE Certification != 0
									AND GardenNr != 0
								GROUP BY FarmerID,
									GardenNr,
									SurveyNr,
									Certification) dt
								INNER JOIN ktv_certification_audit_log au
									ON dt.FarmerID = au.FarmerID
									AND dt.GardenNr = au.GardenNr
									AND dt.SurveyNr = au.SurveyNr
									AND dt.Certification = au.Certification
									AND dt.ICSDate = au.ICSDate
						) AS sub_tbl_au ON 1=1
							AND sub_afl.`FarmerID` = sub_tbl_au.FarmerID
							AND sub_afl.`GardenNr` = sub_tbl_au.GardenNr
							AND sub_afl.`SurveyNr` = sub_tbl_au.SurveyNr
							AND sub_hold.CertProgID = sub_tbl_au.Certification
					WHERE
						sub_ims.`StatusCode` = 'active'
						AND sub_ims.`IMSID` = {$IMSID}
						AND sub_afl.`FarmerID` = a.FarmerID
						AND (
							DATE(sub_tbl_au.`DateUpdated`) >= DATE_FORMAT( '{$CertEventDate}' - INTERVAL 6 MONTH, '%Y/%m/%d' )
							OR
							DATE(sub_tbl_au.`DateCreated`) >= DATE_FORMAT( '{$CertEventDate}' - INTERVAL 6 MONTH, '%Y/%m/%d' )
						)
				) AS CAudit,

				(
					SELECT
						COUNT(sub_ph.`MemberID`) AS BANYAK
					FROM
						(
							SELECT
								gar.`MemberID` FarmerID
								, MAX(gar.`SurveyNr`) AS SurveyNr
							FROM
								ktv_certification_pre_afl_garden a
								LEFT JOIN ktv_survey_plot gar ON 1=1
									AND a.`FarmerID` = gar.`MemberID`
									AND a.`SurveyNr` = gar.`SurveyNr`
									AND a.`GardenNr` = gar.`PlotNr`
							WHERE
								a.`IMSID` = {$IMSID}
								AND gar.`StatusCode` = 'active'
								AND (
									DATE(gar.`DateUpdated`) >= DATE_FORMAT( '{$CertEventDate}' - INTERVAL 6 MONTH, '%Y/%m/%d' )
									OR
									DATE(gar.`DateCreated`) >= DATE_FORMAT( '{$CertEventDate}' - INTERVAL 6 MONTH, '%Y/%m/%d' )
								)
							GROUP BY gar.`MemberID`
						) AS sub_tbl_garden
						LEFT JOIN ktv_survey_plot sub_ph ON 1=1
							AND sub_tbl_garden.FarmerID = sub_ph.`MemberID`
							AND sub_tbl_garden.SurveyNr = sub_ph.`PlotNr`
							AND sub_ph.SurveyNr > 0
					WHERE
						sub_ph.`StatusCode` = 'active'
						AND sub_ph.MemberID = a.FarmerID
						AND (
							DATE(sub_ph.`DateUpdated`) >= DATE_FORMAT( '{$CertEventDate}' - INTERVAL 6 MONTH, '%Y/%m/%d' )
							OR
							DATE(sub_ph.`DateCreated`) >= DATE_FORMAT( '{$CertEventDate}' - INTERVAL 6 MONTH, '%Y/%m/%d' )
						)
				) AS CPostHarvest,

				(
					SELECT
						COUNT(sub_tbl_grup.FarmerID) AS BANYAK
					FROM
					(
						SELECT
							afl.`FarmerID`
						FROM
							ktv_certification_pre_afl afl
							INNER JOIN ktv_members a ON afl.`FarmerID` = a.`MemberID`

							LEFT JOIN ktv_ppiscore2012 ppi ON afl.`FarmerID` = ppi.`FarmerID`
						WHERE
							afl.`IMSID` = {$IMSID}
							AND afl.`StatusCode` = 'active'
							AND a.`StatusCode` = 'active'
							AND ppi.`StatusCode` = 'active'
							AND (
								DATE(ppi.`DateUpdated`) >= DATE_FORMAT( '{$CertEventDate}' - INTERVAL 6 MONTH, '%Y/%m/%d' )
								OR
								DATE(ppi.`DateCreated`) >= DATE_FORMAT( '{$CertEventDate}' - INTERVAL 6 MONTH, '%Y/%m/%d' )
							)
						GROUP BY afl.`FarmerID`
					) AS sub_tbl_grup
					WHERE
						sub_tbl_grup.FarmerID = a.FarmerID
				) AS CPPI,

				(
					SELECT
						COUNT(sub_gpoly.MemberID) AS GardenPolygonNya
					FROM
						ktv_certification_pre_afl_garden sub_a
						LEFT JOIN ktv_survey_plot sub_gar ON 1=1
							AND sub_a.`FarmerID` = sub_gar.`MemberID`
							AND sub_a.`SurveyNr` = sub_gar.`SurveyNr`
							AND sub_a.`GardenNr` = sub_gar.`PlotNr`
							AND (
								DATE(sub_gar.`DateUpdated`) >= DATE_FORMAT( '{$CertEventDate}' - INTERVAL 6 MONTH, '%Y/%m/%d' )
								OR
								DATE(sub_gar.`DateCreated`) >= DATE_FORMAT( '{$CertEventDate}' - INTERVAL 6 MONTH, '%Y/%m/%d' )
							)

						LEFT JOIN (
							SELECT
								a.`MemberID`
								, a.`PlotNr`
							FROM
								ktv_survey_plot_polygon a
							WHERE
								a.`StatusCode` != 'nullified'
								AND a.`MemberID` != '0'
								AND a.`PlotNr` != '0'
							GROUP BY a.`MemberID`, a.`PlotNr`
						) AS sub_gpoly ON 1=1
							AND sub_gar.`MemberID` = sub_gpoly.MemberID
							AND sub_gar.`PlotNr` = sub_gpoly.PlotNr
							AND sub_gar.SurveyNr = 0

					WHERE
						sub_a.`IMSID` = {$IMSID}
						AND sub_a.FarmerID = a.FarmerID
					GROUP BY sub_a.`FarmerID`
				) AS CGardenPolygon

			FROM
				ktv_certification_pre_afl a
				LEFT JOIN ktv_ims i
					ON i.IMSID = a.IMSID
				LEFT JOIN ktv_certification_holders hold
					ON i.CertHolderID = hold.CertHolderID
				LEFT JOIN ktv_members b
					ON a.FarmerID = b.MemberID
				LEFT JOIN ktv_farmer_group cpg
					ON cpg.FarmerGroupID = b.FarmerGroupID
				LEFT JOIN ktv_village v
					ON v.VillageID = b.VillageID
				LEFT JOIN ktv_subdistrict sd
					ON sd.SubDistrictID = v.SubDistrictID

				LEFT JOIN ktv_certification_pre_afl_garden aflg
					ON 1 = 1
					AND a.FarmerID = aflg.FarmerID
					AND a.IMSID = aflg.IMSID

				LEFT JOIN ktv_ims_cluster cl ON a.ClusterID = cl.ClusterID
			WHERE
				a.StatusCode = 'active'
				AND a.IMSID = {$IMSID}
				AND (b.MemberID LIKE ? OR b.MemberName LIKE ? OR cpg.GroupName LIKE ? OR cpg.FarmerGroupID LIKE ? OR v.Village LIKE ?)
			GROUP BY a.FarmerID
			";

		if($callFrom == 'js_grid'){
			$sql_query = $sql."  ORDER BY $sortingField $sortingDir LIMIT ?, ?";
			$query = $this->db->query($sql_query, array($key, "%$key%", "%$key%", $key, "%$key%", intval($start), intval($limit)));
		}else{
			$query = $this->db->query($sql, array($key, "%$key%", "%$key%", $key, "%$key%"));
			return $query->result_array();
		}
		// echo "<pre>".$this->db->last_query();exit;

		$sql_total   = "SELECT FOUND_ROWS() AS total";
		$query_total = $this->db->query($sql_total);
		if ($query->num_rows() > 0) {
			$total = $query_total->row_array(0)['total'];
		}

		
		return array(
			'data'  => $query->result_array(),
			'total' => $total,
		);
		return false;
	}

	public function deleteFarmerCandidate($FarmerID, $IMSID)
	{
		$this->db->trans_start();

		$sql = "UPDATE ktv_certification_pre_afl a SET
				a.`StatusCode` = 'nullified'
			WHERE
				a.`IMSID` = ?
				AND a.`FarmerID` = ?";
		$query = $this->db->query($sql, array($IMSID, $FarmerID));

		$this->db->trans_complete();
		if ($this->db->trans_status() === true) {
			$results['success'] = true;
			$results['message'] = "Candidate deleted";
		} else {
			$results['success'] = false;
			$results['message'] = "Failed to delete candidate";
		}
		return $results;
	}

	public function readFarmerAddList($IMSID, $SurveyNr, $DistrictID, $SubDistrictID, $VillageID, $hectare, $production, $key)
	{
		if ($VillageID == "") {$vl = "#";} else { $vl = "";}
		if ($hectare == "") {$ha = "#";} else { $ha = "";}
		if ($production == "") {$pr = "#";} else { $pr = "";}
		$Village       = explode("::", $VillageID);
		$where_village = "(";
		for ($i = 0; $i < count($Village); $i++) {
			if ($i != 0) {$koma = ",";} else { $koma = "";}
			$where_village .= $koma . $Village[$i];
		}
		$where_village .= ")";
		/*//Query Lama, klo blom punya garden gk muncul
		$sql = "SELECT
		%s
		FROM
		(SELECT FarmerID, GardenNr, MAX(SurveyNr) SurveyNr FROM ktv_members_garden GROUP BY FarmerID, GardenNr) g
		LEFT JOIN ktv_members_garden a ON a.FarmerID=g.FarmerID AND a.GardenNr=g.GardenNr AND a.SurveyNr=g.SurveyNr
		LEFT JOIN ktv_certification b ON a.FarmerID=b.FarmerID AND a.GardenNr=b.GardenNr AND b.IMSID=? AND b.SurveyNr=?
		LEFT JOIN ktv_members c ON a.FarmerID=c.FarmerID
		LEFT JOIN ktv_village d ON c.VillageID=d.VillageID
		LEFT JOIN ktv_subdistrict e ON e.SubDistrictID=d.SubDistrictID
		LEFT JOIN ktv_cpg cpg ON cpg.CPGid=c.CPGid
		WHERE
		c.StatusCode!='nullified' AND e.DistrictID=? AND d.SubDistrictID=?
		$vl AND d.VillageID IN $where_village
		$ha AND a.GardenHaUnCertified >= ?
		$pr AND ((IFNULL(a.PanenTrekMonths,0) * IFNULL(a.PanenTrekPanenMonth,0) * IFNULL(a.PanenTrekKg,0)) + (IFNULL(a.PanenBiasaMonths,0) * IFNULL(a.PanenBiasaPanenMonth,0) * IFNULL(a.PanenBiasaKg,0)) + (IFNULL(a.PanenRayaMonths,0) * IFNULL(a.PanenRayaPanenMonth,0) * IFNULL(a.PanenRayaKg,0))) / a.GardenHaUnCertified  >= ?
		AND b.FarmerID IS NULL AND (c.FarmerID LIKE ? OR c.FarmerName LIKE ?)";
		$query = $this->db->query(sprintf($sql, "DISTINCT a.FarmerID as addFarmerID, c.FarmerName as addFarmerName, a.GardenNr addGardenNr, CONCAT('[',cpg.CPGid,'] ',cpg.GroupName) FarmerGroup, d.Village, a.GardenHaUnCertified ha, ROUND(((IFNULL(a.PanenTrekMonths,0) * IFNULL(a.PanenTrekPanenMonth,0) * IFNULL(a.PanenTrekKg,0)) + (IFNULL(a.PanenBiasaMonths,0) * IFNULL(a.PanenBiasaPanenMonth,0) * IFNULL(a.PanenBiasaKg,0)) + (IFNULL(a.PanenRayaMonths,0) * IFNULL(a.PanenRayaPanenMonth,0) * IFNULL(a.PanenRayaKg,0))) / a.GardenHaUnCertified) AS production"), array($IMSID, $SurveyNr, $DistrictID, $SubDistrictID, intval($hectare), intval($production), $key, "%$key%"));
		$result['data'] = $query->result_array();*/
		//Query dengan Farmer bahkan yg blom punya Garden bisa dimasukkan
		$sql = "SELECT
					%s
				FROM
					(
						SELECT f.FarmerID, f2.GardenNr, f2.SurveyNr
						FROM
							(SELECT DISTINCT FarmerID FROM ktv_members WHERE StatusCode='active') f
							LEFT JOIN (SELECT FarmerID, GardenNr, MAX(SurveyNr) SurveyNr FROM ktv_members_garden GROUP BY FarmerID, GardenNr) f2 ON f.FarmerID=f2.FarmerID
					) g
					LEFT JOIN ktv_members_garden a ON a.FarmerID=g.FarmerID AND a.GardenNr=g.GardenNr AND a.SurveyNr=g.SurveyNr
					#LEFT JOIN ktv_certification b ON a.FarmerID=b.FarmerID AND a.GardenNr=b.GardenNr AND b.IMSID=tandatanya AND b.SurveyNr=tandatanya
					LEFT JOIN (
						SELECT FarmerID, SurveyNr FROM ktv_certification_pre_afl a LEFT JOIN ktv_ims b ON a.IMSID=b.IMSID WHERE a.StatusCode='active' AND b.StatusCode='active' AND b.IMSID=? AND b.SurveyNr=?
					) b ON b.FarmerID=g.FarmerID
					LEFT JOIN ktv_members c ON g.FarmerID=c.FarmerID
					LEFT JOIN ktv_village d ON c.VillageID=d.VillageID
					LEFT JOIN ktv_subdistrict e ON e.SubDistrictID=d.SubDistrictID
					LEFT JOIN ktv_cpg cpg ON cpg.CPGid=c.CPGid
				WHERE
					(c.StatusCode!='nullified' OR c.StatusCode IS NULL) AND e.DistrictID=? AND d.SubDistrictID=?
					$vl AND d.VillageID IN $where_village
					$ha AND a.GardenHaUnCertified >= ?
					$pr AND ((IFNULL(a.PanenTrekMonths,0) * IFNULL(a.PanenTrekPanenMonth,0) * IFNULL(a.PanenTrekKg,0)) + (IFNULL(a.PanenBiasaMonths,0) * IFNULL(a.PanenBiasaPanenMonth,0) * IFNULL(a.PanenBiasaKg,0)) + (IFNULL(a.PanenRayaMonths,0) * IFNULL(a.PanenRayaPanenMonth,0) * IFNULL(a.PanenRayaKg,0))) / a.GardenHaUnCertified  >= ?
					AND b.FarmerID IS NULL AND (c.FarmerID LIKE ? OR c.FarmerName LIKE ?)";
		$query          = $this->db->query(sprintf($sql, "DISTINCT g.FarmerID as addFarmerID, c.FarmerName as addFarmerName, a.GardenNr addGardenNr, CONCAT('[',cpg.CPGid,'] ',cpg.GroupName) FarmerGroup, d.Village, a.GardenHaUnCertified ha, ROUND(((IFNULL(a.PanenTrekMonths,0) * IFNULL(a.PanenTrekPanenMonth,0) * IFNULL(a.PanenTrekKg,0)) + (IFNULL(a.PanenBiasaMonths,0) * IFNULL(a.PanenBiasaPanenMonth,0) * IFNULL(a.PanenBiasaKg,0)) + (IFNULL(a.PanenRayaMonths,0) * IFNULL(a.PanenRayaPanenMonth,0) * IFNULL(a.PanenRayaKg,0))) / a.GardenHaUnCertified) AS production"), array($IMSID, $SurveyNr, $DistrictID, $SubDistrictID, intval($hectare), intval($production), $key, "%$key%"));
		$result['data'] = $query->result_array();
		//echo "<pre>".$this->db->last_query();exit;
		//$query = $this->db->query($sql, array($cpgID,$CpgBatchTrainingID));

		$query           = $this->db->query(sprintf($sql, 'count(*) as total', ''), array($IMSID, $SurveyNr, $DistrictID, $SubDistrictID, intval($hectare), intval($production), $key, "%$key%"));
		$result['total'] = $query->row()->total;
		return $result;
	}

	public function listSurveys()
	{
		$sql   = "SELECT SurveyNr id, CONCAT(SurveyNr,' - ',SurveyTxt) label FROM ktv_survey WHERE StatusCode!='nullified'";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
	}

	public function listProvinces()
	{
		$sql   = "SELECT ProvinceID id, Province label FROM ktv_province WHERE active='1' AND StatusCode!='nullified'";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
	}

	public function listDistricts($ProvinceID)
	{
		$sql   = "SELECT DistrictID id, District label FROM ktv_district WHERE active='1' AND StatusCode!='nullified' AND ProvinceID=?";
		$query = $this->db->query($sql, array($ProvinceID));
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
	}

	public function listSubDistricts($ProvinceID, $DistrictID)
	{
		$sql   = "SELECT SubDistrictID id, SubDistrict label FROM ktv_subdistrict WHERE active='1' AND StatusCode!='nullified' AND DistrictID=?";
		$query = $this->db->query($sql, array($DistrictID));
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
	}

	public function listVillages($SubDistrictID)
	{
		$sql   = "SELECT VillageID id, Village label FROM ktv_village WHERE StatusCode='active' AND SubDistrictID=?";
		$query = $this->db->query($sql, array($SubDistrictID));
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
	}

	public function addFarmers($IMSID, $SurveyNr, $farmers, $userid)
	{
		$sql = " INSERT INTO ktv_certification_pre_afl(IMSID, FarmerID, StatusCode, CreatedBy, DateCreated)
			VALUES (?,?,?,?,now())";
		$this->db->trans_start();
		$farmer = explode(',', $farmers);
		for ($i = 1; $i < count($farmer); $i++) {
			$data  = explode('_', $farmer[$i]);
			$query = $this->db->query($sql, array($IMSID, $data[0], 'active', $userid));
		}
		$this->db->trans_complete();

		if ($this->db->trans_status() === true) {
			$results['success'] = true;
			$results['message'] = "Farmer added.";
		} else {
			$results['success'] = false;
			$results['message'] = "Failed to add  farmer";
		}
		return $results;
	}

	public function deleteFarmer($IMSID, $FarmerID)
	{
		$sql   = "DELETE FROM ktv_certification_pre_afl WHERE IMSID=? AND FarmerID=?";
		$query = $this->db->query($sql, array($IMSID, $FarmerID));
		if ($query) {
			$results['success'] = true;
			$results['message'] = "record deleted.";
		} else {
			$results['success'] = false;
			$results['message'] = "Failed to delete record.";
		}
		return $results;
	}

	public function readSummary($IMSID)
	{
		$sql = "SELECT
					COUNT(DISTINCT a.FarmerID) CFarmer,
					COUNT(DISTINCT CONCAT(g.FarmerID,'_',g.GardenNr)) CGarden,
					COUNT(DISTINCT CONCAT(cert.FarmerID,'_',cert.GardenNr)) CCert,
					COUNT(DISTINCT CONCAT(adl.FarmerID,'_',adl.GardenNr)) CAuditLog,
					COUNT(DISTINCT ppi.FarmerID) CPPI,
					COUNT(DISTINCT ph.FarmerID) CPostHarvest, sd.SubDistrict
				FROM
					ktv_certification_pre_afl a
					LEFT JOIN ktv_ims i ON i.IMSID=a.IMSID
					LEFT JOIN ktv_members b ON a.FarmerID=b.FarmerID
					LEFT JOIN ktv_cpg cpg ON cpg.CPGid=b.CPGid
					LEFT JOIN ktv_village v ON v.VillageID=b.VillageID
					LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
					LEFT JOIN (
						SELECT b.*
						FROM (SELECT FarmerID, GardenNr, MAX(SurveyNr) SurveyNr FROM ktv_members_garden WHERE GardenNr!=0 GROUP BY FarmerID, GardenNr) a
						LEFT JOIN ktv_members_garden b ON a.FarmerID=b.FarmerID AND a.GardenNr=b.GardenNr AND a.SurveyNr=b.SurveyNr
					) g ON g.FarmerID=a.FarmerID AND (g.SurveyNr=0 OR g.SurveyNr=i.SurveyNr)
					LEFT JOIN (
						SELECT b.*
						FROM (SELECT FarmerID, GardenNr, MAX(SurveyNr) SurveyNr, Certification FROM ktv_certification WHERE GardenNr!=0 AND Certification!=0 GROUP BY FarmerID, GardenNr, Certification) a
						LEFT JOIN ktv_certification b ON a.FarmerID=b.FarmerID AND a.GardenNr=b.GardenNr AND a.SurveyNr=b.SurveyNr AND a.Certification=b.Certification
					) cert ON cert.FarmerID=a.FarmerID AND (cert.SurveyNr=0 OR cert.SurveyNr=i.SurveyNr)
					LEFT JOIN (
						SELECT b.*
						FROM (SELECT FarmerID, GardenNr, SurveyNr, Certification, MAX(ICSDate) ICSDate FROM ktv_certification_audit_log WHERE GardenNr!=0 AND Certification!=0 GROUP BY FarmerID, GardenNr, SurveyNr, Certification) a
						LEFT JOIN ktv_certification_audit_log b ON a.FarmerID=b.FarmerID AND a.GardenNr=b.GardenNr AND a.SurveyNr=b.SurveyNr AND a.Certification=b.Certification AND a.ICSDate=b.ICSDate
					) adl ON cert.FarmerID=adl.FarmerID AND cert.SurveyNr=adl.SurveyNr AND cert.GardenNr=adl.GardenNr AND cert.Certification=adl.Certification
					LEFT JOIN (SELECT FarmerID, MAX(SurveyNr) SurveyNr FROM ktv_ppiscore2012 GROUP BY FarmerID) ppi ON ppi.FarmerID=a.FarmerID AND (ppi.SurveyNr=0 OR ppi.SurveyNr=i.SurveyNr)
					LEFT JOIN (SELECT FarmerID, MAX(SurveyNr) SurveyNr FROM ktv_members_post_harvest GROUP BY FarmerID) ph ON ph.FarmerID=a.FarmerID AND (ph.SurveyNr=0 OR ph.SurveyNr=i.SurveyNr)
				WHERE a.StatusCode='active' AND a.IMSID=?";
		$query = $this->db->query($sql, array($IMSID));
		//echo "<pre>".$this->db->last_query();exit;
		$sql_total   = "SELECT FOUND_ROWS() AS total";
		$query_total = $this->db->query($sql_total);
		if ($query->num_rows() > 0) {
			$total = $query_total->row_array(0);
			return array(
				'data'  => $query->result_array(),
				'total' => $total['total'],
			);
		}
		return false;
	}

	public function readFiles($IMSID)
	{
		$sql = "SELECT SQL_CALC_FOUND_ROWS
					a.IMSFileID, a.IMSID, a.FileName, a.FilePath, a.FileDesc
				FROM
					ktv_ims_files a
				WHERE a.StatusCode='active' AND a.IMSID=?";
		$query = $this->db->query($sql, array($IMSID));
		$data  = $query->result_array();

		foreach ($data as $key => $value) {
			$data[$key]['FilePath'] = urlencode($value['FilePath']);
		}

		$sql_total   = "SELECT FOUND_ROWS() AS total";
		$query_total = $this->db->query($sql_total);
		if ($query->num_rows() > 0) {
			$total = $query_total->row_array(0);
			return array(
				'data'  => $query->result_array(),
				'total' => $total['total'],
			);
		}
		return false;
	}

	public function readStaffs($IMSID, $key, $start = 0, $limit = 50, $sortingField, $sortingDir)
	{
		if ($sortingField == "") {
			$sortingField = 'StaffName';
		}

		if ($sortingDir == "") {
			$sortingDir = 'ASC';
		}

		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					a.IMSStaffID, a.IMSID, a.StaffID, c.PersonNm StaffName, b.OfficialEMail StaffEmail,

					IFNULL(
						(SELECT sub_a.`WorkAreaName` FROM ktv_ref_work_area sub_a WHERE sub_a.`WorkAreaID` = a.WorkAreaID LIMIT 1),
						d.WorkAreaName
					) AS StaffWorkArea,

					CASE c.Gender WHEN 'f' THEN 'Female' WHEN 'm' THEN 'Male' END Gender
					, CASE
						WHEN b.ObjType = 'program' THEN 'Program'
						WHEN b.ObjType = 'private' THEN 'Private'
						WHEN b.ObjType = 'extension' THEN 'Extension'
						WHEN b.ObjType = 'sce' THEN 'SCE'
						WHEN b.ObjType = 'Trader' THEN 'Trader'
						WHEN b.ObjType = 'cooperative' THEN 'Cooperative'
						WHEN b.ObjType = 'warehouse' THEN 'Warehouse'
						WHEN b.ObjType = 'bank' THEN 'Bank'
						WHEN b.ObjType = 'farmergroup' THEN 'Farmer Group'
					END AS StaffRoleType
					, a.IMSMasterID
				FROM ktv_ims_staff a
				LEFT JOIN ktv_staffs b ON a.StaffID=b.StaffID
				LEFT JOIN ktv_persons c ON b.PersonID=c.PersonID
				LEFT JOIN ktv_ref_work_area d ON d.WorkAreaID=b.WorkAreaID
				WHERE 1 = 1 AND a.StatusCode!='nullified' AND a.IMSID=? AND (c.PersonNm LIKE ? OR b.OfficialEMail LIKE ? OR d.WorkAreaName LIKE ?)
				ORDER BY $sortingField $sortingDir
				LIMIT ?, ?";
		$query       = $this->db->query($sql, array($IMSID, "%$key%", "%$key%", "%$key%", intval($start), intval($limit)));
		$sql_total   = "SELECT FOUND_ROWS() AS total";
		$query_total = $this->db->query($sql_total);
		if ($query->num_rows() > 0) {
			$total = $query_total->row_array(0);
			return array(
				'data'  => $query->result_array(),
				'total' => $total['total'],
			);
		}
		return false;
	}

	public function readBuyingUnits($IMSID, $key, $start = 0, $limit = 50)
	{
		$sql = "SELECT SQL_CALC_FOUND_ROWS
				   a.IMSID, a.SupplychainID, b.ObjType, b.Name, IFNULL(d.Company,'-') Company, kdis.District, a.Status
				FROM ktv_ims_buying_unit a
				LEFT JOIN view_tc_supplychain_org b ON a.SupplychainID=b.SupplychainID
					LEFT JOIN ktv_village kvil ON kvil.VillageID=b.VillageID
					LEFT JOIN ktv_subdistrict ksub ON ksub.SubDistrictID=kvil.SubDistrictID
					LEFT JOIN ktv_district kdis on kdis.DistrictID=ksub.DistrictID
					LEFT JOIN ktv_province kprov on kprov.ProvinceID=kdis.ProvinceID
				LEFT JOIN ktv_traders d ON d.TraderID=b.OrgID AND b.ObjType = 'Agent'
				WHERE a.IMSID=? AND (b.Name LIKE ?)
				LIMIT ?, ?";
		$query       = $this->db->query($sql, array($IMSID, "%$key%", intval($start), intval($limit)));
		$sql_total   = "SELECT FOUND_ROWS() AS total";
		$query_total = $this->db->query($sql_total);
		if ($query->num_rows() > 0) {
			$total = $query_total->row_array(0);
			return array(
				'data'  => $query->result_array(),
				'total' => $total['total'],
			);
		}
		return false;
	}

	public function readStaffAddList($IMSID, $ProvinceID, $WorkAreaID, $key)
	{
		if ($ProvinceID == "") {$pr = "#";} else { $pr = "";}
		if ($WorkAreaID == "") {$wr = "#";} else { $wr = "";}
		$sql = "SELECT
					%s
				FROM ktv_staffs b
				LEFT JOIN ktv_ims_staff a ON a.StaffID=b.StaffID AND a.IMSID=? AND a.StatusCode!='nullified'
				LEFT JOIN ktv_persons c ON b.PersonID=c.PersonID
				LEFT JOIN ktv_ref_work_area d ON d.WorkAreaID=b.WorkAreaID
				WHERE 1 = 1 AND a.StaffID IS NULL AND (c.PersonNm LIKE ? OR b.OfficialEMail LIKE ? OR d.WorkAreaName LIKE ?)
				$pr AND d.ProvinceID=?
				$wr AND d.WorkAreaID=?";
		$query          = $this->db->query(sprintf($sql, "b.StaffID addStaffID, c.PersonNm addStaffName, b.OfficialEMail addStaffEmail,  d.WorkAreaName addStaffWorkArea, CASE c.Gender WHEN 'f' THEN 'Female' WHEN 'm' THEN 'Male' END addGender"), array($IMSID, "%$key%", "%$key%", "%$key%", $ProvinceID, $WorkAreaID));
		$result['data'] = $query->result_array();
		//echo "<pre>".$this->db->last_query();exit;
		//$query = $this->db->query($sql, array($cpgID,$CpgBatchTrainingID));

		$query           = $this->db->query(sprintf($sql, 'count(*) as total', ''), array($IMSID, "%$key%", "%$key%", "%$key%", $ProvinceID, $WorkAreaID));
		$result['total'] = $query->row()->total;
		return $result;
	}

	public function readStaffMasterAddList($IMSMasterID, $ProvinceID, $WorkAreaID, $key)
	{
		if ($ProvinceID == "") {$pr = "#";} else { $pr = "";}
		if ($WorkAreaID == "") {$wr = "#";} else { $wr = "";}
		$sql = "SELECT
					%s
				FROM ktv_staffs b
				LEFT JOIN ktv_ims_staff a ON a.StaffID=b.StaffID AND a.IMSMasterID=? AND a.StatusCode!='nullified'
				LEFT JOIN ktv_persons c ON b.PersonID=c.PersonID
				LEFT JOIN ktv_ref_work_area d ON d.WorkAreaID=b.WorkAreaID
				WHERE 1 = 1 AND a.StaffID IS NULL AND (c.PersonNm LIKE ? OR b.OfficialEMail LIKE ? OR d.WorkAreaName LIKE ?)
				$pr AND d.ProvinceID=?
				$wr AND d.WorkAreaID=?";
		$query          = $this->db->query(sprintf($sql, "b.StaffID addStaffID, c.PersonNm addStaffName, b.OfficialEMail addStaffEmail,  d.WorkAreaName addStaffWorkArea, CASE c.Gender WHEN 'f' THEN 'Female' WHEN 'm' THEN 'Male' END addGender"), array($IMSMasterID, "%$key%", "%$key%", "%$key%", $ProvinceID, $WorkAreaID));
		$result['data'] = $query->result_array();

		$query           = $this->db->query(sprintf($sql, 'count(*) as total', ''), array($IMSMasterID, "%$key%", "%$key%", "%$key%", $ProvinceID, $WorkAreaID));
		$result['total'] = $query->row()->total;
		return $result;
	}

	public function listStaffProvinces()
	{
		$sql   = "SELECT DISTINCT b.ProvinceID id, b.Province label FROM ktv_ref_work_area a LEFT JOIN ktv_province b ON a.ProvinceID=b.ProvinceID WHERE a.StatusCode='active'";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
	}

	public function listWorkArea($ProvinceID)
	{
		$sql   = "SELECT WorkAreaID id, WorkAreaName label FROM ktv_ref_work_area WHERE StatusCode='active' AND ProvinceID=?";
		$query = $this->db->query($sql, array($ProvinceID));
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
	}

	public function addStaffsImsEvent($IMSMasterID, $IMSID, $staffs)
	{
		$arrStaffID = explode(',', $staffs);
		$this->db->trans_start();

		for ($i = 0; $i < count($arrStaffID); $i++) {
			if ($arrStaffID[$i] == "") {
				continue;
			}

			//cek apakah IMSID nya ada
			$sql = "
			SELECT
				a.IMSID
			FROM
				ktv_ims_staff a
			WHERE
				a.IMSMasterID = ?
				AND a.StaffID = ?
			LIMIT 1
			";
			$p = array(
				$IMSMasterID, $arrStaffID[$i],
			);
			$query   = $this->db->query($sql, $p);
			$dataCek = $query->result_array();

			if ($dataCek[0]['IMSID'] == "") {
				//update
				$sql = "UPDATE ktv_ims_staff SET
					IMSID = ?,
					DateUpdated = NOW(),
					LastModifiedBy = '{$_SESSION['userid']}'
				WHERE
					IMSMasterID = ?
					AND StaffID = ?
				LIMIT 1";
				$p = array(
					$IMSID, $IMSMasterID, $arrStaffID[$i],
				);
				$query = $this->db->query($sql, $p);
			} else {
				//insert
				$sql = "INSERT INTO `ktv_ims_staff` SET
					  `IMSMasterID` = ?,
					  `IMSID` = ?,
					  `StaffID` = ?,
					  `StatusCode` = 'active',
					  `DateCreated` = NOW(),
					  `CreatedBy` = '{$_SESSION['userid']}'";
				$p = array(
					$IMSMasterID, $IMSID, $arrStaffID[$i],
				);
				$query = $this->db->query($sql, $p);
			}
		}

		$this->db->trans_complete();
		if ($this->db->trans_status() === true) {
			$results['success'] = true;
			$results['message'] = "Staff added.";
		} else {
			$results['success'] = false;
			$results['message'] = "Failed to add staff.";
		}
		return $results;
	}

	public function deleteStaffsImsEvent($IMSStaffID, $IMSMasterID, $StaffID, $IMSID)
	{
		$this->db->trans_start();

		//cek record yg mirip tinggal berapa
		$sql = "SELECT
				a.`IMSStaffID`
			FROM
				ktv_ims_staff a
			WHERE
				a.`IMSMasterID` = ?
				AND a.`StaffID` = ?";
		$p = array(
			$IMSMasterID, $StaffID,
		);
		$query   = $this->db->query($sql, $p);
		$dataCek = $query->result_array();

		if (count($dataCek) > 1) {
			//delete
			$sql = "DELETE FROM ktv_ims_staff WHERE IMSStaffID = ? LIMIT 1";
			$p   = array(
				$IMSStaffID,
			);
			$query = $this->db->query($sql, $p);
		} else {
			//update
			$sql = "UPDATE ktv_ims_staff SET
				IMSID = NULL,
				DateUpdated = NOW(),
				LastModifiedBy = '{$_SESSION['userid']}'
			WHERE
				IMSStaffID = ?
			LIMIT 1";
			$p = array(
				$IMSStaffID,
			);
			$query = $this->db->query($sql, $p);
		}

		$this->db->trans_complete();
		if ($this->db->trans_status() === true) {
			$results['success'] = true;
			$results['message'] = "Staff deleted";
		} else {
			$results['success'] = false;
			$results['message'] = "Failed to delete staff";
		}
		return $results;
	}

	public function addStaffsMaster($IMSMasterID, $staffs, $userid)
	{
		$sql = " INSERT INTO ktv_ims_staff(IMSMasterID, StaffID, CreatedBy,DateCreated,StatusCode)
			VALUES (?,?,?,now(),'active')";
		$this->db->trans_start();
		$staff = explode(',', $staffs);
		for ($i = 1; $i < count($staff); $i++) {
			$query = $this->db->query($sql, array($IMSMasterID, $staff[$i], $userid));
		}
		$this->db->trans_complete();
		if ($this->db->trans_status() === true) {
			$results['success'] = true;
			$results['message'] = "Staff added.";
		} else {
			$results['success'] = false;
			$results['message'] = "Failed to add staff.";
		}
		return $results;
	}

	public function deleteStaffMaster($IMSMasterID, $StaffID)
	{
		$sql = "DELETE FROM ktv_ims_staff WHERE IMSMasterID = ? AND StaffID = ?";
		/*
		$sql="UPDATE ktv_ims_staff SET
		StatusCode = 'nullified',
		DateUpdated = NOW(),
		LastModifiedBy = '{$_SESSION['userid']}'
		WHERE
		IMSStaffID = ? LIMIT 1";
		 */
		$query = $this->db->query($sql, array($IMSMasterID, $StaffID));
		if ($query) {
			$result['success'] = true;
			$result['message'] = "Staff Deleted";
		} else {
			$result['success'] = false;
			$result['message'] = "Failed to delete staff";
		}
		return $result;
	}

	public function addStaffs($IMSID, $staffs, $userid)
	{
		$sql = " INSERT INTO ktv_ims_staff(IMSID, StaffID, CreatedBy,DateCreated,StatusCode)
			VALUES (?,?,?,now(),'active')";
		$this->db->trans_start();
		$staff = explode(',', $staffs);
		for ($i = 1; $i < count($staff); $i++) {
			$query = $this->db->query($sql, array($IMSID, $staff[$i], $userid));
			//echo "<pre>".$this->db->last_query();
		}
		$this->db->trans_complete();
		if ($this->db->trans_status() === true) {
			$results['success'] = true;
			$results['message'] = "Staff added.";
		} else {
			$results['success'] = false;
			$results['message'] = "Failed to add staff.";
		}
		return $results;
	}

	public function deleteStaff($IMSStaffID, $userid)
	{
		$sql   = "UPDATE ktv_ims_staff SET StatusCode='nullified', LastModifiedBy=?, DateUpdated=NOW() WHERE IMSStaffID=?";
		$query = $this->db->query($sql, array($userid, $IMSStaffID));
		if ($query) {
			$results['success'] = true;
			$results['message'] = "record deleted.";
		} else {
			$results['success'] = false;
			$results['message'] = "Failed to delete record.";
		}
		return $results;
	}

	public function ImsEventStaffWorkAreaFormOpen($IMSStaffID){
		$sql="SELECT
			d.`PersonNm` AS StaffName
			, b.`ProvinceID`
			, b.`WorkAreaID`
		FROM
			ktv_ims_staff a
			LEFT JOIN ktv_ref_work_area b ON a.`WorkAreaID` = b.`WorkAreaID`
			LEFT JOIN ktv_staffs c ON a.`StaffID` = c.`StaffID`
			LEFT JOIN ktv_persons d ON c.`PersonID` = d.`PersonID`
		WHERE
			a.`IMSStaffID` = ?
		LIMIT 1";
		return $this->db->query($sql,array($IMSStaffID))->row_array();
	}

	public function ImsEventStaffWorkArea($IMSStaffID,$WorkAreaID){
		$sql="UPDATE ktv_ims_staff a SET
				a.WorkAreaID = ?
			WHERE
				a.IMSStaffID = ?
			LIMIT 1";
		$p = array(
			$WorkAreaID,$IMSStaffID
		);
		$query = $this->db->query($sql,$p);

		if($query == true){
			$result['success_val'] = true;
			$result['message'] = lang('Save Success');
		}else{
			$result['success_val'] = false;
			$result['message'] = lang('Save Failed');
		}
		return $result;
	}

	public function readIcsReinspect($IMSID, $key, $start = 0, $limit = 50, $sortingField, $sortingDir){
		if ($sortingField == "") {
			$sortingField = 'FarmerID';
		}

		if ($sortingDir == "") {
			$sortingDir = 'ASC';
		}

		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					a.IMSID,
					a.FarmerID,
					a.`FarmerName`,
					a.`CertGardenNr`,
					CONCAT('[', b.`FarmerGroupID`, '] ', b.`GroupName`) AS FarmerGroup,
					a.`Village`,
					a.CertStatusAudit AS AFLStatus,
					c.StatusRegenerateIcs AS RegenerateICSStatus,
					a.`CertICSDate` AS ICSDate,
					a.`CertNextHarvest`,
					a.`CertHarvest`,
					a.`CertHectare`
				FROM
					ktv_certification_afl_garden a
					LEFT JOIN ktv_farmer_group b ON a.`CpgID` = b.`FarmerGroupID`
					INNER JOIN ktv_ims_ics_reinspection c ON 1=1
						AND a.`IMSID` = c.`IMSID`
						AND a.`FarmerID` = c.`FarmerID`
						AND a.`CertGardenNr` = c.`GardenNr`
				WHERE
					a.`IMSID` = ?
					AND a.`StatusCode` = 'active'
					AND (
						a.FarmerID LIKE ?
						OR a.FarmerName LIKE ?
					)
				ORDER BY $sortingField $sortingDir
				LIMIT ?, ?";
		$query = $this->db->query($sql, array($IMSID, "%$key%", "%$key%", intval($start), intval($limit)));
		//echo "<pre>".$this->db->last_query();exit;

		$sql_total   = "SELECT FOUND_ROWS() AS total";
		$query_total = $this->db->query($sql_total);
		if ($query->num_rows() > 0) {
			$total = $query_total->row_array(0);
			return array(
				'data'  => $query->result_array(),
				'total' => $total['total'],
			);
		}else{
			return array(
				'data'  => array(),
				'total' => 0,
			);
		}
	}

	public function readAFLs($IMSID, $key,$StatusAudit,$StatusVerified, $start = 0, $limit = 50, $sortingField, $sortingDir)
	{
		if ($sortingField == "") {
			$sortingField = 'AFLFarmerID';
		}

		if ($sortingDir == "") {
			$sortingDir = 'ASC';
		}

		//SQL WHERE ========================= (Begin)
		$SqlWhere = "";
		if($StatusAudit != ""){
			if(in_array($StatusAudit,array("-","Comply","Not Comply"))){
				$SqlWhere .= " AND a.CertStatusAudit = '{$StatusAudit}' ";
			}
		}

		if($StatusVerified != ""){
			if(in_array($StatusVerified,array("0","1","2"))){
				$StatusVerified = (int) $StatusVerified;
				if($StatusVerified == 0) $StatusVerified = null;
				$SqlWhere .= " AND a.CertStatusVerified = {$StatusVerified} ";
			}
		}
		//SQL WHERE ========================= (End)

		$sql = "SELECT
					SQL_CALC_FOUND_ROWS a.IMSID,
					a.FarmerID AFLFarmerID,
					a.FarmerID,
					m.MemberDisplayID,
					a.FarmerName,
					a.CertStatusAudit AFLStatus,
					a.CertYear,
					a.CertFirstYear,
					a.CertHarvest,
					a.CertNextHarvest,
					a.CertHectare,
					a.CertFarmNr,
					CONCAT('[', b.`FarmerGroupID`, '] ', b.`GroupName`) AS FarmerGroup,
					a.`Village`,
					a.CertICSDate AS ICSDate,
					a.CertTotalHectare AS TotalHa,
					a.TotalCocoaFarm,
					a.CertStatusVerified,
					ch.`CertProgID`,
					'' AS AuditSummaryStatus
				FROM
					ktv_certification_afl_farmer a
					LEFT JOIN ktv_farmer_group b
						ON a.`CpgID` = b.`FarmerGroupID`
					LEFT JOIN ktv_ims ims ON 1=1
						AND a.`IMSID` = ims.`IMSID`
					LEFT JOIN ktv_certification_holders ch ON 1=1
						AND ims.`CertHolderID` = ch.`CertHolderID`
					LEFT JOIN
						ktv_members m on m.MemberID = a.FarmerID
				WHERE a.IMSID = ?
					AND (
						a.FarmerID LIKE ?
						OR a.FarmerName LIKE ?
					)
					AND a.StatusCode = 'active'
					$SqlWhere
				ORDER BY $sortingField $sortingDir
				LIMIT ?, ?";
		$query = $this->db->query($sql, array($IMSID, "%$key%", "%$key%", intval($start), intval($limit)));
		//echo "<pre>".$this->db->last_query();exit;

		$sql_total   = "SELECT FOUND_ROWS() AS total";
		$query_total = $this->db->query($sql_total);

		//Assign Nilai AuditSummaryStatus ==== (Begin)
		$DataList = $query->result_array();
		if($query->num_rows() > 0){
			for ($i=0; $i < count($DataList); $i++) {
				//1. Cek apakah sudah ada audit log nya
				$sql = "SELECT
							aflg.`FarmerID`
							, aflg.`CertGardenNr`
							, aflg.`CertSurveyNr`
						FROM
							ktv_certification_afl_garden aflg
							INNER JOIN ktv_certification_audit_log au ON 1=1
								AND aflg.`FarmerID` = au.`FarmerID`
								AND aflg.`CertSurveyNr` = au.`SurveyNr`
								AND aflg.`CertGardenNr` = au.`GardenNr`
								AND au.`Certification` = ?
						WHERE
							aflg.`IMSID` = ?
							AND aflg.`FarmerID` = ?";
				$p = array(
					$DataList[$i]['CertProgID'],
					$IMSID,
					$DataList[$i]['FarmerID']
				);
				$DataCek1 = $this->db->query($sql,$p)->result_array();
				if(isset($DataCek1[0]['FarmerID'])){
					//2. Cek apakah sudah Audit Log belum punya  Audit Sum
					$sql = "SELECT
								SUM(IF(dg.`DaconID` IS NOT NULL,1,0)) AS CekAuditSum
							FROM
								ktv_certification_afl_garden aflg
								INNER JOIN ktv_certification_audit_log au ON 1=1
									AND aflg.`FarmerID` = au.`FarmerID`
									AND aflg.`CertSurveyNr` = au.`SurveyNr`
									AND aflg.`CertGardenNr` = au.`GardenNr`
									AND au.`Certification` = ?
								LEFT JOIN ktv_farmer_garden_datacontrol dg ON 1=1
									AND au.`FarmerID` = dg.MemberID
									AND au.`SurveyNr` = dg.`SurveyNr`
									AND au.`GardenNr` = dg.`PlotNr`
									AND au.`Certification` = dg.`Certification`
							WHERE
								aflg.`IMSID` = ?
								AND aflg.`FarmerID` = ?
							GROUP BY au.`FarmerID`";
					$p = array(
						$DataList[$i]['CertProgID'],
						$IMSID,
						$DataList[$i]['FarmerID']
					);
					$DataCek2 = $this->db->query($sql,$p)->row_array();
					$CekAuditSum2 = (int) $DataCek2['CekAuditSum'];
					if($CekAuditSum2 > 0){
						//3. Cek apakah semua gardenya sudah punya Audit Summary
						$sql = "SELECT
									IF(banding.JumlahGarden = banding.SumCekDg,'All','Some') AS HasilBanding
								FROM
								(
								SELECT
									(
										SELECT
											COUNT(aflg.FarmerID) AS JumlahGar
										FROM
											ktv_certification_afl_garden aflg
										WHERE
											aflg.`IMSID` = ?
											AND aflg.`FarmerID` = ?
									) AS JumlahGarden,
									(
										SELECT
											SUM(gfar.CekDg) AS SumCekDg
										FROM
										(
										SELECT
											au.`FarmerID`
											, au.`GardenNr`
											, au.`SurveyNr`
											, au.`Certification`
											, IF(dg.`DaconID` IS NOT NULL,1,0) AS CekDg
										FROM
											ktv_certification_afl_garden aflg
											INNER JOIN ktv_certification_audit_log au ON 1=1
												AND aflg.`FarmerID` = au.`FarmerID`
												AND aflg.`CertSurveyNr` = au.`SurveyNr`
												AND aflg.`CertGardenNr` = au.`GardenNr`
												AND au.`Certification` = ?
											LEFT JOIN ktv_farmer_garden_datacontrol dg ON 1=1
												AND au.`FarmerID` = dg.MemberID
												AND au.`SurveyNr` = dg.`SurveyNr`
												AND au.`GardenNr` = dg.`PlotNr`
												AND au.`Certification` = dg.`Certification`
										WHERE
											aflg.`IMSID` = ?
											AND aflg.`FarmerID` = ?
										GROUP BY au.`FarmerID`, au.`GardenNr`, au.`SurveyNr`, au.`Certification`
										) AS gfar
										GROUP BY gfar.FarmerID
									) AS SumCekDg
								) AS banding";
						$p = array(
							$IMSID,
							$DataList[$i]['FarmerID'],
							$DataList[$i]['CertProgID'],
							$IMSID,
							$DataList[$i]['FarmerID']
						);
						$DataCek3 = $this->db->query($sql,$p)->row_array();
						$DataList[$i]['AuditSummaryStatus'] = $DataCek3['HasilBanding'];
					}else{
						$DataList[$i]['AuditSummaryStatus'] = 'None';
					}
				}else{
					$DataList[$i]['AuditSummaryStatus'] = '-';
				}
			}
		}
		//Assign Nilai AuditSummaryStatus ==== (End)

		if ($query->num_rows() > 0) {
			$total = $query_total->row_array(0);
			return array(
				'data'  => $DataList,
				'total' => $total['total'],
			);
		}else{
			return array(
				'data'  => array(),
				'total' => 0
			);
		}
		return false;
	}

	public function readAFLFinal($IMSID, $key, $start = 0, $limit = 50, $sortingField, $sortingDir)
	{
		if ($sortingField == "") {
			$sortingField = 'AFLFarmerID';
		}

		if ($sortingDir == "") {
			$sortingDir = 'ASC';
		}

		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					a.IMSID,
					a.FarmerID AFLFarmerID,
					a.FarmerID,
					a.FarmerName,
					m.MemberDisplayID,
					a.CertStatusAudit AFLStatus,
					a.CertYear,
					a.CertFirstYear,
					a.CertHarvest,
					a.CertNextHarvest,
					a.CertHectare,
					a.CertFarmNr,
					CONCAT('[', b.`FarmerGroupID`, '] ', b.`GroupName`) AS FarmerGroup,
					a.`Village`,
					a.CertICSDate AS ICSDate,
					a.CertTotalHectare AS TotalHa,
					a.TotalCocoaFarm,
					a.ExternalAuditStatus AS ExternalAudit
				FROM
					ktv_certification_afl_farmer a
				LEFT JOIN 
					ktv_farmer_group b ON a.`CPGid` = b.`FarmerGroupID`
				LEFT JOIN
					ktv_members m on m.MemberID = a.FarmerID
				WHERE
					a.IMSID = ?
					AND (
						a.FarmerID LIKE ?
						OR a.FarmerName LIKE ?
					)
					AND a.StatusCode = 'active'
					AND a.CertStatusAudit = 'Comply'
				ORDER BY AFLFarmerID ASC
				LIMIT ?, ?";
		$query = $this->db->query($sql, array((int) $IMSID, $key, "%$key%", intval($start), intval($limit)));
		//echo "<pre>".$this->db->last_query();exit;

		$sql_total   = "SELECT FOUND_ROWS() AS total";
		$query_total = $this->db->query($sql_total);
		if ($query->num_rows() > 0) {
			$total = $query_total->row_array(0);
			return array(
				'data'  => $query->result_array(),
				'total' => $total['total'],
			);
		}else{
			return array(
				'data'  => array(),
				'total' => 0,
			);
		}
		return false;
	}

	public function readAFLFinalExternalAuditInput($IMSID, $key, $start = 0, $limit = 50, $sortingField, $sortingDir) {
		if ($sortingField == "") {
			$sortingField = 'AFLFarmerID';
		}

		if ($sortingDir == "") {
			$sortingDir = 'ASC';
		}

		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					a.IMSID,
					a.FarmerID AFLFarmerID,
					a.FarmerID,
					a.FarmerName,
					a.CertStatusAudit AFLStatus,
					a.CertYear,
					a.CertFirstYear,
					a.CertHarvest,
					a.CertNextHarvest,
					a.CertHectare,
					a.CertFarmNr,
					CONCAT('[', b.`CPGid`, '] ', b.`GroupName`) AS FarmerGroup,
					a.`Village`,
					a.CertICSDate AS ICSDate,
					a.CertTotalHectare AS TotalHa,
					a.TotalCocoaFarm
				FROM
					ktv_certification_afl_farmer a
					LEFT JOIN ktv_cpg b
						ON a.`CPGid` = b.`CPGid`
				WHERE
					a.IMSID = ?
					AND (
						a.FarmerID LIKE ?
						OR a.FarmerName LIKE ?
					)
					AND a.StatusCode = 'active'
					AND a.CertStatusAudit = 'Comply'
					AND a.ExternalAuditStatus = '1' #New
				ORDER BY AFLFarmerID ASC
				LIMIT ?, ?";
		$query = $this->db->query($sql, array((int) $IMSID, $key, "%$key%", intval($start), intval($limit)));
		//echo "<pre>".$this->db->last_query();exit;

		$sql_total   = "SELECT FOUND_ROWS() AS total";
		$query_total = $this->db->query($sql_total);
		if ($query->num_rows() > 0) {
			$total = $query_total->row_array(0);
			return array(
				'data'  => $query->result_array(),
				'total' => $total['total'],
			);
		}
		return false;
	}

	public function readCoaching($IMSID, $TextSearch = '', $start = 0, $limit = 50, $sortingField, $sortingDir) {
		if ($sortingField == "") {
			$sortingField = 'afl.FarmerID';
		}

		if ($sortingDir == "") {
			$sortingDir = 'ASC';
		}
		$where = "";
		if ($TextSearch != '') {
			$where .= " AND (afl.FarmerName LIKE '%{$TextSearch}%' OR afl.FarmerID LIKE '%{$TextSearch}%')";
		}

		$sql = "
		SELECT
			SQL_CALC_FOUND_ROWS
			afl.FarmerID, afl.FarmerName, gdc.`DaconID`,
			afl.Gender AS Gender,
			CONCAT('[', cpg.`CPGid`, '] ', cpg.`GroupName`) AS FarmerGroup,
			vil.VillageID, afl.Village,
			subd.SubDistrictID, afl.SubDistrict,
			dis.DistrictID, afl.District,
			prov.ProvinceID, afl.Province,
			(
				SELECT COUNT(sh.`DaconItemID`)
				FROM `ktv_farmer_garden_datacontrol_item` sh
				INNER JOIN ktv_ref_survey_datacontrol r_sh ON sh.`RefID` = r_sh.`RefID`
				WHERE
				sh.`DaconID` = gdc.`DaconID` AND r_sh.`StatusControl` = 'High'
			) AS NCMajor,
			(
				SELECT COUNT(sh.`DaconItemID`)
				FROM `ktv_farmer_garden_datacontrol_item` sh
				INNER JOIN ktv_ref_survey_datacontrol r_sh ON sh.`RefID` = r_sh.`RefID`
				WHERE sh.`DaconID` = gdc.`DaconID` AND r_sh.`StatusControl` = 'Medium'
			) AS NCMinor, '' AS NCMinorAct, '' AS NCMajorAct
		FROM ktv_certification_afl_farmer afl
		JOIN ktv_members f ON afl.FarmerID=f.FarmerID
		LEFT JOIN ktv_ims_farmer_coaching m ON afl.FarmerID=m.FarmerID
		LEFT JOIN ktv_certification_afl_garden gar ON f.FarmerID=gar.FarmerID AND m.IMSID=gar.IMSID
		LEFT JOIN ktv_farmer_garden_datacontrol gdc ON gdc.FarmerID=f.FarmerID AND gdc.GardenNr=gar.CertGardenNr AND gdc.SurveyNr=gar.CertSurveyNr
		LEFT JOIN ktv_cpg cpg ON f.`CPGid` = cpg.`CPGid`
	LEFT JOIN ktv_village vil ON f.`VillageID` = vil.VillageID
	LEFT JOIN ktv_subdistrict subd ON vil.`SubDistrictID` = subd.`SubDistrictID`
	LEFT JOIN ktv_district dis ON subd.DistrictID = dis.DistrictID
	LEFT JOIN ktv_province prov ON dis.ProvinceID = prov.ProvinceID
		WHERE afl.IMSID = ? $where
		GROUP BY afl.FarmerID
		ORDER BY $sortingField $sortingDir
		LIMIT ?,?";

		$sql = "SELECT
				SQL_CALC_FOUND_ROWS
				afl.FarmerID,
				afl.FarmerName,
				CASE 
					WHEN f.Gender = 'm' THEN 'Male' 
					WHEN f.Gender = 'f' THEN 'Female' 
					ELSE '-'
				END AS Gender,
				CONCAT('[', cpg.`FarmerGroupID`, '] ', cpg.`GroupName`) AS FarmerGroup,
				vil.VillageID,
				afl.Village,
				subd.SubDistrictID,
				afl.SubDistrict,
				dis.DistrictID,
				afl.District,
				prov.ProvinceID,
				afl.Province
				, '' AS NCMajor
				, '' AS NCMinor
				, '' AS NCMinorAct
				, '' AS NCMajorAct
			FROM
				ktv_certification_afl_farmer afl
				JOIN ktv_members f ON afl.FarmerID = f.MemberID

				LEFT JOIN ktv_farmer_group cpg ON f.`FarmerGroupID` = cpg.`FarmerGroupID`
				LEFT JOIN ktv_village vil ON f.`VillageID` = vil.VillageID
				LEFT JOIN ktv_subdistrict subd ON vil.`SubDistrictID` = subd.`SubDistrictID`
				LEFT JOIN ktv_district dis ON subd.DistrictID = dis.DistrictID
				LEFT JOIN ktv_province prov ON dis.ProvinceID = prov.ProvinceID
			WHERE
				afl.IMSID = ?
				AND afl.StatusCode = 'active'
				$where
			GROUP BY afl.FarmerID
			ORDER BY $sortingField $sortingDir LIMIT ?,?";
		$query = $this->db->query($sql, array($IMSID, (int) $start, (int) $limit));
		//echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;

		$sql_total = "SELECT FOUND_ROWS() AS total";
		$query_total = $this->db->query($sql_total);
		$total = $query_total->row_array(0);

		return array(
			'data' => $query->result_array(),
			'total' => $total['total']
		);
	}

	public function getCountNc($IMSID, $FarmerID, $MajorMinor) {
		$return = array();
		$CountNc = 0;
		$CountNcAct = 0;

		//Get Gardennya
		$sql = "SELECT
					a.`FarmerID`
					, a.`CertSurveyNr` AS SurveyNr
					, a.`CertGardenNr` AS GardenNr
				FROM
					`ktv_certification_afl_garden` a
				WHERE 1=1
					AND a.`StatusCode` = 'active'
					AND a.`IMSID` = ?
					AND a.`FarmerID` = ?";
		$DataGarden = $this->db->query($sql,array($IMSID,$FarmerID))->result_array();
		if(isset($DataGarden[0]['FarmerID'])) {
			$ArrRefID = array();

			for ($i=0; $i < count($DataGarden); $i++) {
				$sql = "SELECT
					(
						SELECT
							GROUP_CONCAT(DISTINCT r_sh.RefID SEPARATOR ',')
						FROM
							`ktv_farmer_garden_datacontrol_item` sh
							INNER JOIN ktv_ref_survey_datacontrol r_sh ON sh.`RefID` = r_sh.`RefID`
						WHERE
							sh.`DaconID` = suba.`DaconID`
							AND r_sh.`StatusControl` = 'High'
					) AS NC_Nya
				FROM
					`ktv_farmer_garden_datacontrol` suba
				WHERE
					suba.`MemberID` = '{$DataGarden[$i]['FarmerID']}'
					AND suba.`PlotNr` = '{$DataGarden[$i]['GardenNr']}'
					AND suba.`SurveyNr` = '{$DataGarden[$i]['SurveyNr']}'
				ORDER BY suba.`DaconID` DESC
				LIMIT 1";
				$DataAuditSum = $this->db->query($sql,array($MajorMinor))->row_array();
				if(isset($DataAuditSum['NC_Nya'])) {
					$ArrRefID[$i]['NC_Nya'] = explode(',',$DataAuditSum['NC_Nya']);
				}
			}

			$ArrRefIDHasil = array();
			for ($i=0; $i < count($ArrRefID); $i++) {
				$ArrRefIDHasil = array_unique(array_merge($ArrRefIDHasil, $ArrRefID[$i]['NC_Nya']));
			}
			$CountNc = count($ArrRefIDHasil);
			$ImpRefIDHasil = implode(',',$ArrRefIDHasil);

			if($ImpRefIDHasil != "") {
				$sql = "SELECT
							DISTINCT c.`RefID`
						FROM
							`ktv_ims_farmer_coaching` a
							INNER JOIN `ktv_ims_farmer_coaching_activity` b ON a.`CoachingID` = b.`CoachingID`
							INNER JOIN `ktv_ims_farmer_coaching_activity_nc` c ON b.`ActivityID` = c.`ActivityID`
						WHERE 1=1
							AND a.`FarmerID` = ?
							AND a.`IMSID` = ?
							AND c.RefID IN ($ImpRefIDHasil)
						";
				$DataAct = $this->db->query($sql,array($FarmerID,$IMSID))->result_array();
				if(isset($DataAct[0]['RefID'])) {
					$CountNcAct = count($DataAct);
				} else {
					$CountNcAct = 0;
				}
			} else {
				$CountNcAct = 0;
			}
		} else {
			$CountNc = 0;
			$CountNcAct = 0;
		}

		$return['CountNc'] = $CountNc;
		$return['CountNcAct'] = $CountNcAct;
		return $return;
	}

	public function getAct($DaconID, $Control) {
		$sql = "SELECT COUNT(RefID) NCAct
		FROM (
			SELECT *
			FROM ktv_ims_farmer_coaching_activity_nc
			WHERE RefID IN
				(
					SELECT sh.`RefID`
					FROM `ktv_farmer_garden_datacontrol_item` sh
					INNER JOIN ktv_ref_survey_datacontrol r_sh ON sh.`RefID` = r_sh.`RefID`
					WHERE sh.`DaconID` = ? AND r_sh.`StatusControl` = ?
				)
			GROUP BY RefID
		) Act";
		$query = $this->db->query($sql, array($DaconID, $Control));
		return $query->row()->NCAct;
	}

	public function readCFLs($IMSID, $key, $start = 0, $limit = 50, $sortingField, $sortingDir)
	{
		if ($sortingField == "") {
			$sortingField = 'AFLFarmerID';
		}

		if ($sortingDir == "") {
			$sortingDir = 'ASC';
		}

		$sql = "SELECT
				SQL_CALC_FOUND_ROWS
				a.IMSID,
				a.FarmerID AFLFarmerID,
				a.FarmerID,
				a.FarmerName,
				a.CertStatusAudit AFLStatus,
				a.CertYear,
				a.CertFirstYear,
				a.CertHarvest,
				a.CertNextHarvest,
				a.CertHectare,
				a.CertFarmNr,
				CONCAT('[', b.`FarmerGroupID`, '] ', b.`GroupName`) AS FarmerGroup,
				a.`Village`,
				a.CertICSDate AS ICSDate,
				a.CertTotalHectare AS TotalHa,
				a.SalesQuota,
				a.TotalCocoaFarm
			FROM
				ktv_certification_certified_farmer a
				LEFT JOIN ktv_farmer_group b
					ON a.`CPGid` = b.`FarmerGroupID`
			WHERE
				a.IMSID = ?
				AND (
					a.FarmerID LIKE ?
					OR a.FarmerName LIKE ?
				)
				AND a.StatusCode = 'active'
			ORDER BY $sortingField $sortingDir
			LIMIT ?, ?
			";
		$query = $this->db->query($sql, array($IMSID, $key, "%$key%", intval($start), intval($limit)));
		//echo "<pre>".$this->db->last_query();exit;
		$sql_total   = "SELECT FOUND_ROWS() AS total";
		$query_total = $this->db->query($sql_total);
		if ($query->num_rows() > 0) {
			$total = $query_total->row_array(0);
			return array(
				'data'  => $query->result_array(),
				'total' => $total['total'],
			);
		}else{
			return array(
				'data'  => array(),
				'total' => 0,
			);
		}
		return false;
	}

	public function GenerateAFL($IMSID, $userid)
	{
		$this->db->trans_start();
		$sql      = "DELETE FROM ktv_certification_afl_farmer WHERE IMSID=?";
		$query    = $this->db->query($sql, array($IMSID));
		$sql      = "DELETE FROM ktv_certification_afl_garden WHERE IMSID=?";
		$query    = $this->db->query($sql, array($IMSID));
		$SurveyNr = $this->db->query("SELECT SurveyNr FROM ktv_ims WHERE IMSID=?", array($IMSID))->row()->SurveyNr;
		$sql      = "INSERT INTO ktv_certification_afl_farmer
				SELECT
					? IMSID,
					cert.FarmerID,
					f.FarmerName,
					f.CPGid,
					c.GroupName,
					CASE f.Gender WHEN '1' THEN 'Male' WHEN '2' THEN 'Female' ELSE '' END Gender,
					f.HandPhone,
					p.Province,
					d.District,
					sd.SubDistrict,
					v.Village,
					IF(poly.FarmerID IS NULL, 'Notavailable','Available') PolygonStatus,
					cert.Year CertYear,
					IFNULL(fy.FirstYearCert, YEAR(NOW())) CertFirstYear,
					IFNULL(ph.BuruhFulltime,0) 'PermanentWorkers',
					adl.ICSDate,
					adl.StatusAudit,

					pohon.SurveyNr CertSurveyNr,
					IF(pohon.CertifiedHarvest!=0,pohon.CertifiedHarvest,pohon.Production) CertHarvest,
					pohon.ProductionNext CertNextHarvest,
					pohon.GardenHaUnCertified CertHectare,
					pohon.FarmNr CertFarmNr,
					pohon.PohonTM CertPohonTM,
					pohon.PohonTBM CertPohonTBM,
					pohon.PohonTR CertPohonTR,
					pohon.CertificationStart CertStart,
					pohon.CertificationEnd CertEnd,
					pohon.DateCollection CertDateCollection,

					pohon1yearago.SurveyNr 1YearAgoSurveyNr,
					IF(pohon1yearago.CertifiedHarvest!=0,pohon1yearago.CertifiedHarvest,pohon1yearago.Production) 1YearAgoHarvest,
					pohon1yearago.GardenHaUnCertified 1YearAgoHectare,
					pohon1yearago.FarmNr 1YearAgoFarmNr,
					pohon1yearago.PohonTM 1YearAgoPohonTM,
					pohon1yearago.PohonTBM 1YearAgoPohonTBM,
					pohon1yearago.PohonTR 1YearAgoPohonTR,
					pohon1yearago.CertificationStart 1YearAgoStart,
					pohon1yearago.CertificationEnd 1YearAgoEnd,
					pohon1yearago.DateCollection 1YearAgoDateCollection,

					pohon2yearago.SurveyNr 2YearAgoSurveyNr,
					IF(pohon2yearago.CertifiedHarvest!=0,pohon2yearago.CertifiedHarvest,pohon2yearago.Production) 2YearAgoHarvest,
					pohon2yearago.GardenHaUnCertified 2YearAgoHectare,
					pohon2yearago.FarmNr 2YearAgoFarmNr,
					pohon2yearago.PohonTM 2YearAgoPohonTM,
					pohon2yearago.PohonTBM 2YearAgoPohonTBM,
					pohon2yearago.PohonTR 2YearAgoPohonTR,
					pohon2yearago.CertificationStart 2YearAgoStart,
					pohon2yearago.CertificationEnd 2YearAgoEnd,
					pohon2yearago.DateCollection 2YearAgoDateCollection,

					pohonbaseline.SurveyNr BaselineSurveyNr,
					IF(
						IF(pohonbaseline.CertifiedHarvest!=0,pohonbaseline.CertifiedHarvest,pohonbaseline.Production) = IF(pohon.CertifiedHarvest!=0,pohon.CertifiedHarvest,pohon.Production)
						&& pohon.DateCollection=pohonbaseline.DateCollection,
						0,
						IF(pohonbaseline.CertifiedHarvest!=0,pohonbaseline.CertifiedHarvest,pohonbaseline.Production)
					) BaselineHarvest,
					pohonbaseline.GardenHaUnCertified BaselineHectare,
					pohonbaseline.FarmNr BaselineFarmNr,
					pohonbaseline.PohonTM BaselinePohonTM,
					pohonbaseline.PohonTBM BaselinePohonTBM,
					pohonbaseline.PohonTR BaselinePohonTR,
					pohonbaseline.CertificationStart BaselineStart,
					pohonbaseline.CertificationEnd BaselineEnd,
					pohonbaseline.DateCollection BaselineDateCollection,

					u.UserRealName 'CreatedBy',
					uu.UserRealName 'LastModifiedBy',

					'active' StatusCode,
					NOW() DateCreated,
					1 CreatedBy,
					NULL DateUpdated,
					NULL LastModifiedBy

				FROM
					ktv_certification cert
					LEFT JOIN ktv_certification_pre_afl pre ON pre.IMSID=? AND pre.FarmerID=cert.FarmerID
					LEFT JOIN ktv_members f ON f.FarmerID=cert.FarmerID
					LEFT JOIN ktv_village v ON v.VillageID=f.VillageID
					LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
					LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
					LEFT JOIN ktv_province p ON p.ProvinceID=d.ProvinceID
					LEFT JOIN ktv_cpg c ON c.CPGid=f.CPGid
					LEFT JOIN (
						SELECT au.FarmerID, au.GardenNr, au.SurveyNr, au.Certification, au.ICSDate, au.StatusAudit, au.CommentAudit, au.DateRevisionAudit, au.RecommendationAudit
						FROM
							(SELECT FarmerID, GardenNr, SurveyNr, Certification, MAX(ICSDate) ICSDate FROM ktv_certification_audit_log WHERE Certification!=0 AND GardenNr!=0 GROUP BY FarmerID, GardenNr, SurveyNr, Certification) dt
							LEFT JOIN ktv_certification_audit_log au ON dt.FarmerID=au.FarmerID AND dt.GardenNr=au.GardenNr AND dt.SurveyNr=au.SurveyNr AND dt.Certification=au.Certification AND dt.ICSDate=au.ICSDate
					) adl ON adl.FarmerID=cert.FarmerID AND adl.GardenNr=cert.GardenNr AND adl.SurveyNr=cert.SurveyNr AND adl.Certification=cert.Certification
					LEFT JOIN ktv_members_garden g ON g.GardenNr=cert.GardenNr AND g.FarmerID=cert.FarmerID AND g.SurveyNr=cert.SurveyNr
					LEFT JOIN (
							SELECT FarmerID, MIN(YEAR(ExternalDate)) FirstYearCert
							FROM ktv_certification
							WHERE GardenNr!=0 AND Certification!=0 AND ExternalDate!='0000-00-00' AND ExternalDate IS NOT NULL
							GROUP BY FarmerID
					) fy ON fy.FarmerID=cert.FarmerID
					LEFT JOIN ktv_members_post_harvest ph ON ph.FarmerID=cert.FarmerID AND ph.SurveyNr=cert.SurveyNr
					LEFT JOIN (
							SELECT DISTINCT FarmerID, GardenNr  FROM ktv_members_garden_area
					) poly ON poly.FarmerID=cert.FarmerID AND poly.GardenNr=cert.GardenNr
					LEFT JOIN (
							SELECT
									a.FarmerID, a.SurveyNr, b.DateCollection, Count(*) FarmNr
									,SUM(IFNULL(b.GardenHaUnCertified,0)) GardenHaUnCertified
									,SUM(IFNULL(b.Production,0)) Production
									,SUM(IFNULL(b.ProductionNext,0)) ProductionNext
									,SUM(IFNULL(((IFNULL(b.PanenBiasaMonths,0) * IFNULL(b.PanenBiasaPanenMonth,0) * IFNULL(b.PanenBiasaKg,0)) + (IFNULL(b.PanenTrekMonths,0) * IFNULL(b.PanenTrekPanenMonth,0) * IFNULL(b.PanenTrekKg,0)) + (IFNULL(b.PanenRayaMonths,0) * IFNULL(b.PanenRayaPanenMonth,0) * IFNULL(b.PanenRayaKg,0))),0)) CertifiedHarvest
									,SUM(IFNULL(b.PohonTM,0)) 'PohonTM'
									,SUM(IFNULL(b.PohonTBM,0)) 'PohonTBM'
									,SUM(IFNULL(b.PohonRehab,0)) 'PohonTR', a.CertificationStart, a.CertificationEnd
							FROM
									ktv_certification a
									LEFT JOIN ktv_members_garden b ON a.FarmerID=b.FarmerID AND a.GardenNr=b.GardenNr AND a.SurveyNr=b.SurveyNr
							WHERE a.GardenNr!=0 AND a.Certification!=0
							GROUP BY FarmerID, SurveyNr
					) pohon ON pohon.FarmerID=cert.FarmerID AND pohon.SurveyNr = cert.SurveyNr
					LEFT JOIN (
							SELECT
											a.FarmerID, a.SurveyNr, b.DateCollection, Count(*) FarmNr
											,SUM(IFNULL(b.GardenHaUnCertified,0)) GardenHaUnCertified
											,SUM(IFNULL(b.Production,0)) Production
											,SUM(IFNULL(((IFNULL(b.PanenBiasaMonths,0) * IFNULL(b.PanenBiasaPanenMonth,0) * IFNULL(b.PanenBiasaKg,0)) + (IFNULL(b.PanenTrekMonths,0) * IFNULL(b.PanenTrekPanenMonth,0) * IFNULL(b.PanenTrekKg,0)) + (IFNULL(b.PanenRayaMonths,0) * IFNULL(b.PanenRayaPanenMonth,0) * IFNULL(b.PanenRayaKg,0))),0)) CertifiedHarvest
											,SUM(IFNULL(b.PohonTM,0)) 'PohonTM'
											,SUM(IFNULL(b.PohonTBM,0)) 'PohonTBM'
											,SUM(IFNULL(b.PohonRehab,0)) 'PohonTR', a.CertificationStart, a.CertificationEnd
							FROM
											ktv_certification a
											LEFT JOIN ktv_members_garden b ON a.FarmerID=b.FarmerID AND a.GardenNr=b.GardenNr AND a.SurveyNr=b.SurveyNr
							WHERE a.GardenNr!=0 AND a.ExternalDate!='0000-00-00' AND a.ExternalDate IS NOT NULL AND a.Certification!=0
							GROUP BY FarmerID, SurveyNr
					) pohon1yearago ON pohon1yearago.FarmerID=cert.FarmerID AND pohon1yearago.SurveyNr < cert.SurveyNr AND pohon1yearago.SurveyNr = (SELECT MAX(SurveyNr) FROM ktv_certification WHERE GardenNr!=0 AND ExternalDate!='0000-00-00' AND ExternalDate IS NOT NULL AND FarmerID=cert.FarmerID AND SurveyNr < cert.SurveyNr)
					LEFT JOIN (
							SELECT
											a.FarmerID, a.SurveyNr, b.DateCollection, Count(*) FarmNr
											,SUM(IFNULL(b.GardenHaUnCertified,0)) GardenHaUnCertified
											,SUM(IFNULL(b.Production,0)) Production
											,SUM(IFNULL(((IFNULL(b.PanenBiasaMonths,0) * IFNULL(b.PanenBiasaPanenMonth,0) * IFNULL(b.PanenBiasaKg,0)) + (IFNULL(b.PanenTrekMonths,0) * IFNULL(b.PanenTrekPanenMonth,0) * IFNULL(b.PanenTrekKg,0)) + (IFNULL(b.PanenRayaMonths,0) * IFNULL(b.PanenRayaPanenMonth,0) * IFNULL(b.PanenRayaKg,0))),0)) CertifiedHarvest
											,SUM(IFNULL(b.PohonTM,0)) 'PohonTM'
											,SUM(IFNULL(b.PohonTBM,0)) 'PohonTBM'
											,SUM(IFNULL(b.PohonRehab,0)) 'PohonTR', a.CertificationStart, a.CertificationEnd
							FROM
											ktv_certification a
											LEFT JOIN ktv_members_garden b ON a.FarmerID=b.FarmerID AND a.GardenNr=b.GardenNr AND a.SurveyNr=b.SurveyNr
							WHERE a.GardenNr!=0 AND a.ExternalDate!='0000-00-00' AND a.ExternalDate IS NOT NULL AND a.Certification!=0
							GROUP BY FarmerID, SurveyNr
					) pohon2yearago ON pohon2yearago.FarmerID=cert.FarmerID AND pohon2yearago.SurveyNr < cert.SurveyNr AND pohon2yearago.SurveyNr = (SELECT MAX(SurveyNr) FROM ktv_certification WHERE GardenNr!=0 AND ExternalDate!='0000-00-00' AND ExternalDate IS NOT NULL AND FarmerID=cert.FarmerID AND SurveyNr < pohon1yearago.SurveyNr)
					LEFT JOIN (
							SELECT
											b.FarmerID, b.SurveyNr, b.DateCollection, Count(*) FarmNr
											,SUM(IFNULL(b.GardenHaUnCertified,0)) GardenHaUnCertified
											,SUM(IFNULL(b.Production,0)) Production
											,SUM(IFNULL(((IFNULL(b.PanenBiasaMonths,0) * IFNULL(b.PanenBiasaPanenMonth,0) * IFNULL(b.PanenBiasaKg,0)) + (IFNULL(b.PanenTrekMonths,0) * IFNULL(b.PanenTrekPanenMonth,0) * IFNULL(b.PanenTrekKg,0)) + (IFNULL(b.PanenRayaMonths,0) * IFNULL(b.PanenRayaPanenMonth,0) * IFNULL(b.PanenRayaKg,0))),0)) CertifiedHarvest
											,SUM(IFNULL(b.PohonTM,0)) 'PohonTM'
											,SUM(IFNULL(b.PohonTBM,0)) 'PohonTBM'
											,SUM(IFNULL(b.PohonRehab,0)) 'PohonTR', a.CertificationStart, a.CertificationEnd
							FROM
											ktv_members_garden b
											LEFT JOIN ktv_certification a ON a.FarmerID=b.FarmerID AND a.GardenNr=b.GardenNr AND a.SurveyNr=b.SurveyNr AND a.ExternalDate!='0000-00-00' AND a.ExternalDate IS NOT NULL AND a.Certification!=0
							WHERE b.GardenNr!=0
							GROUP BY FarmerID, SurveyNr
					) pohonbaseline ON pohonbaseline.FarmerID=cert.FarmerID AND pohonbaseline.SurveyNr < IF(IFNULL(pohon2yearago.SurveyNr,IFNULL(pohon1yearago.SurveyNr,cert.SurveyNr))=0,1,IFNULL(pohon2yearago.SurveyNr,IFNULL(pohon1yearago.SurveyNr,cert.SurveyNr))) AND pohonbaseline.SurveyNr = (SELECT MAX(SurveyNr) FROM ktv_members_garden WHERE GardenNr!=0 AND FarmerID=cert.FarmerID AND SurveyNr < IF(IFNULL(pohon2yearago.SurveyNr,IFNULL(pohon1yearago.SurveyNr,cert.SurveyNr))=0,1,IFNULL(pohon2yearago.SurveyNr,IFNULL(pohon1yearago.SurveyNr,cert.SurveyNr))))
					LEFT JOIN sys_user u ON u.UserId=cert.CreatedBy
					LEFT JOIN sys_user uu ON uu.UserId=cert.LastModifiedBy
				WHERE
					cert.Certification!=0 AND cert.GardenNr!=0 AND (cert.SurveyNr=0 OR cert.SurveyNr=?)
					AND adl.StatusAudit IN (1,3)
					AND pre.FarmerID IS NOT NULL
				GROUP BY cert.FarmerID";
		$query = $this->db->query($sql, array($IMSID, $IMSID, $SurveyNr));
		$total = $this->db->affected_rows();
		//--AFL Farmer Garden--//
		$sql = "INSERT INTO ktv_certification_afl_garden
				SELECT
						? IMSID,
						cert.FarmerID,
						f.FarmerName,
						f.CPGid,
						c.GroupName,
						CASE f.Gender WHEN '1' THEN 'Male' WHEN '2' THEN 'Female' ELSE '' END Gender,
						f.HandPhone,
						p.Province,
						d.District,
						sd.SubDistrict,
						v.Village,
						IF(poly.FarmerID IS NULL, 'Notavailable','Available') PolygonStatus,
						cert.Year CertYear,
						IFNULL(fy.FirstYearCert, YEAR(NOW())) CertFirstYear,
						IFNULL(ph.BuruhFulltime,0) 'PermanentWorkers',
						adl.ICSDate,
						adl.StatusAudit,

						pohon.SurveyNr CertSurveyNr,
						IF(pohon.CertifiedHarvest!=0,pohon.CertifiedHarvest,pohon.Production) CertHarvest,
						pohon.ProductionNext CertNextHarvest,
						pohon.GardenHaUnCertified CertHectare,
						cert.GardenNr CertGardenNr,
						pohon.PohonTM CertPohonTM,
						pohon.PohonTBM CertPohonTBM,
						pohon.PohonTR CertPohonTR,
						pohon.CertificationStart CertStart,
						pohon.CertificationEnd CertEnd,
						pohon.DateCollection CertDateCollection,
						cert.CandidateSelection CertCandidateSelection,
						adl.CommentAudit CertCommentAudit,
						adl.DateRevisionAudit CertDateRevisionAudit,
						adl.RecommendationAudit CertRecommendationAudit,

						pohon1yearago.SurveyNr 1YearAgoSurveyNr,
						IF(pohon1yearago.CertifiedHarvest!=0,pohon1yearago.CertifiedHarvest,pohon1yearago.Production) 1YearAgoHarvest,
						pohon1yearago.GardenHaUnCertified 1YearAgoHectare,
						pohon1yearago.GardenNr 1YearAgoGardenNr,
						pohon1yearago.PohonTM 1YearAgoPohonTM,
						pohon1yearago.PohonTBM 1YearAgoPohonTBM,
						pohon1yearago.PohonTR 1YearAgoPohonTR,
						pohon1yearago.CertificationStart 1YearAgoStart,
						pohon1yearago.CertificationEnd 1YearAgoEnd,
						pohon1yearago.DateCollection 1YearAgoDateCollection,

						pohon2yearago.SurveyNr 2YearAgoSurveyNr,
						IF(pohon2yearago.CertifiedHarvest!=0,pohon2yearago.CertifiedHarvest,pohon2yearago.Production) 2YearAgoHarvest,
						pohon2yearago.GardenHaUnCertified 2YearAgoHectare,
						pohon2yearago.GardenNr 2YearAgoGardenNr,
						pohon2yearago.PohonTM 2YearAgoPohonTM,
						pohon2yearago.PohonTBM 2YearAgoPohonTBM,
						pohon2yearago.PohonTR 2YearAgoPohonTR,
						pohon2yearago.CertificationStart 2YearAgoStart,
						pohon2yearago.CertificationEnd 2YearAgoEnd,
						pohon2yearago.DateCollection 2YearAgoDateCollection,

						pohonbaseline.SurveyNr BaselineSurveyNr,
						IF(
								IF(pohonbaseline.CertifiedHarvest!=0,pohonbaseline.CertifiedHarvest,pohonbaseline.Production) = IF(pohon.CertifiedHarvest!=0,pohon.CertifiedHarvest,pohon.Production)
								&& pohon.DateCollection=pohonbaseline.DateCollection,
								0,
								IF(pohonbaseline.CertifiedHarvest!=0,pohonbaseline.CertifiedHarvest,pohonbaseline.Production)
						) BaselineHarvest,
						pohonbaseline.GardenHaUnCertified BaselineHectare,
						pohonbaseline.GardenNr BaselineGardenNr,
						pohonbaseline.PohonTM BaselinePohonTM,
						pohonbaseline.PohonTBM BaselinePohonTBM,
						pohonbaseline.PohonTR BaselinePohonTR,
						pohonbaseline.CertificationStart BaselineStart,
						pohonbaseline.CertificationEnd BaselineEnd,
						pohonbaseline.DateCollection BaselineDateCollection,

						u.UserRealName 'CreatedBy',
						uu.UserRealName 'LastModifiedBy',

						'active' StatusCode,
						NOW() DateCreated,
						1 CreatedBy,
						NULL DateUpdated,
						NULL LastModifiedBy


				FROM
						ktv_certification cert
						LEFT JOIN ktv_certification_pre_afl pre ON pre.IMSID=? AND pre.FarmerID=cert.FarmerID
						LEFT JOIN ktv_members f ON f.FarmerID=cert.FarmerID
						LEFT JOIN ktv_village v ON v.VillageID=f.VillageID
						LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
						LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
						LEFT JOIN ktv_province p ON p.ProvinceID=d.ProvinceID
						LEFT JOIN ktv_cpg c ON c.CPGid=f.CPGid
						LEFT JOIN (
							SELECT au.FarmerID, au.GardenNr, au.SurveyNr, au.Certification, au.ICSDate, au.StatusAudit, au.CommentAudit, au.DateRevisionAudit, au.RecommendationAudit
							FROM
								(SELECT FarmerID, GardenNr, SurveyNr, Certification, MAX(ICSDate) ICSDate FROM ktv_certification_audit_log WHERE Certification!=0 AND GardenNr!=0 GROUP BY FarmerID, GardenNr, SurveyNr, Certification) dt
								LEFT JOIN ktv_certification_audit_log au ON dt.FarmerID=au.FarmerID AND dt.GardenNr=au.GardenNr AND dt.SurveyNr=au.SurveyNr AND dt.Certification=au.Certification AND dt.ICSDate=au.ICSDate
						) adl ON adl.FarmerID=cert.FarmerID AND adl.GardenNr=cert.GardenNr AND adl.SurveyNr=cert.SurveyNr AND adl.Certification=cert.Certification
						LEFT JOIN ktv_members_garden g ON g.GardenNr=cert.GardenNr AND g.FarmerID=cert.FarmerID AND g.SurveyNr=cert.SurveyNr
						LEFT JOIN (
								SELECT FarmerID, GardenNr, MIN(YEAR(ExternalDate)) FirstYearCert
								FROM ktv_certification
								WHERE GardenNr!=0 AND Certification!=0 AND ExternalDate!='0000-00-00' AND ExternalDate IS NOT NULL
								GROUP BY FarmerID, GardenNr
						) fy ON fy.FarmerID=cert.FarmerID AND fy.GardenNr=cert.GardenNr
						LEFT JOIN ktv_members_post_harvest ph ON ph.FarmerID=cert.FarmerID AND ph.SurveyNr=cert.SurveyNr
						LEFT JOIN (
								SELECT DISTINCT FarmerID, GardenNr  FROM ktv_members_garden_area
						) poly ON poly.FarmerID=cert.FarmerID AND poly.GardenNr=cert.GardenNr
						LEFT JOIN (
								SELECT
										a.FarmerID, a.SurveyNr, b.DateCollection, b.GardenNr
										,SUM(IFNULL(b.GardenHaUnCertified,0)) GardenHaUnCertified
										,SUM(IFNULL(b.Production,0)) Production
										,SUM(IFNULL(b.ProductionNext,0)) ProductionNext
										,SUM(IFNULL(((IFNULL(b.PanenBiasaMonths,0) * IFNULL(b.PanenBiasaPanenMonth,0) * IFNULL(b.PanenBiasaKg,0)) + (IFNULL(b.PanenTrekMonths,0) * IFNULL(b.PanenTrekPanenMonth,0) * IFNULL(b.PanenTrekKg,0)) + (IFNULL(b.PanenRayaMonths,0) * IFNULL(b.PanenRayaPanenMonth,0) * IFNULL(b.PanenRayaKg,0))),0)) CertifiedHarvest
										,SUM(IFNULL(b.PohonTM,0)) 'PohonTM'
										,SUM(IFNULL(b.PohonTBM,0)) 'PohonTBM'
										,SUM(IFNULL(b.PohonRehab,0)) 'PohonTR', a.CertificationStart, a.CertificationEnd
								FROM
										ktv_certification a
										LEFT JOIN ktv_members_garden b ON a.FarmerID=b.FarmerID AND a.GardenNr=b.GardenNr AND a.SurveyNr=b.SurveyNr
								WHERE a.GardenNr!=0 AND a.Certification!=0
								GROUP BY FarmerID, SurveyNr, GardenNr
						) pohon ON pohon.FarmerID=cert.FarmerID AND pohon.SurveyNr = cert.SurveyNr AND pohon.GardenNr=cert.GardenNr
						LEFT JOIN (
								SELECT
												a.FarmerID, a.SurveyNr, b.DateCollection, b.GardenNr
												,SUM(IFNULL(b.GardenHaUnCertified,0)) GardenHaUnCertified
												,SUM(IFNULL(b.Production,0)) Production
												,SUM(IFNULL(((IFNULL(b.PanenBiasaMonths,0) * IFNULL(b.PanenBiasaPanenMonth,0) * IFNULL(b.PanenBiasaKg,0)) + (IFNULL(b.PanenTrekMonths,0) * IFNULL(b.PanenTrekPanenMonth,0) * IFNULL(b.PanenTrekKg,0)) + (IFNULL(b.PanenRayaMonths,0) * IFNULL(b.PanenRayaPanenMonth,0) * IFNULL(b.PanenRayaKg,0))),0)) CertifiedHarvest
												,SUM(IFNULL(b.PohonTM,0)) 'PohonTM'
												,SUM(IFNULL(b.PohonTBM,0)) 'PohonTBM'
												,SUM(IFNULL(b.PohonRehab,0)) 'PohonTR', a.CertificationStart, a.CertificationEnd
								FROM
												ktv_certification a
												LEFT JOIN ktv_members_garden b ON a.FarmerID=b.FarmerID AND a.GardenNr=b.GardenNr AND a.SurveyNr=b.SurveyNr
								WHERE a.GardenNr!=0 AND a.ExternalDate!='0000-00-00' AND a.ExternalDate IS NOT NULL AND a.Certification!=0
								GROUP BY FarmerID, SurveyNr, GardenNr
						) pohon1yearago ON pohon1yearago.FarmerID=cert.FarmerID AND pohon1yearago.GardenNr=cert.GardenNr AND pohon1yearago.SurveyNr < cert.SurveyNr AND pohon1yearago.SurveyNr = (SELECT MAX(SurveyNr) FROM ktv_certification WHERE GardenNr!=0 AND ExternalDate!='0000-00-00' AND ExternalDate IS NOT NULL AND FarmerID=cert.FarmerID AND SurveyNr < cert.SurveyNr)
						LEFT JOIN (
								SELECT
												a.FarmerID, a.SurveyNr, b.DateCollection, b.GardenNr
												,SUM(IFNULL(b.GardenHaUnCertified,0)) GardenHaUnCertified
												,SUM(IFNULL(b.Production,0)) Production
												,SUM(IFNULL(((IFNULL(b.PanenBiasaMonths,0) * IFNULL(b.PanenBiasaPanenMonth,0) * IFNULL(b.PanenBiasaKg,0)) + (IFNULL(b.PanenTrekMonths,0) * IFNULL(b.PanenTrekPanenMonth,0) * IFNULL(b.PanenTrekKg,0)) + (IFNULL(b.PanenRayaMonths,0) * IFNULL(b.PanenRayaPanenMonth,0) * IFNULL(b.PanenRayaKg,0))),0)) CertifiedHarvest
												,SUM(IFNULL(b.PohonTM,0)) 'PohonTM'
												,SUM(IFNULL(b.PohonTBM,0)) 'PohonTBM'
												,SUM(IFNULL(b.PohonRehab,0)) 'PohonTR', a.CertificationStart, a.CertificationEnd
								FROM
												ktv_certification a
												LEFT JOIN ktv_members_garden b ON a.FarmerID=b.FarmerID AND a.GardenNr=b.GardenNr AND a.SurveyNr=b.SurveyNr
								WHERE a.GardenNr!=0 AND a.ExternalDate!='0000-00-00' AND a.ExternalDate IS NOT NULL AND a.Certification!=0
								GROUP BY FarmerID, SurveyNr, GardenNr
						) pohon2yearago ON pohon2yearago.FarmerID=cert.FarmerID AND pohon2yearago.GardenNr=cert.GardenNr AND pohon2yearago.SurveyNr < cert.SurveyNr AND pohon2yearago.SurveyNr = (SELECT MAX(SurveyNr) FROM ktv_certification WHERE GardenNr!=0 AND ExternalDate!='0000-00-00' AND ExternalDate IS NOT NULL AND FarmerID=cert.FarmerID AND SurveyNr < pohon1yearago.SurveyNr)
						LEFT JOIN (
								SELECT
												b.FarmerID, b.SurveyNr, b.DateCollection, b.GardenNr
												,SUM(IFNULL(b.GardenHaUnCertified,0)) GardenHaUnCertified
												,SUM(IFNULL(b.Production,0)) Production
												,SUM(IFNULL(((IFNULL(b.PanenBiasaMonths,0) * IFNULL(b.PanenBiasaPanenMonth,0) * IFNULL(b.PanenBiasaKg,0)) + (IFNULL(b.PanenTrekMonths,0) * IFNULL(b.PanenTrekPanenMonth,0) * IFNULL(b.PanenTrekKg,0)) + (IFNULL(b.PanenRayaMonths,0) * IFNULL(b.PanenRayaPanenMonth,0) * IFNULL(b.PanenRayaKg,0))),0)) CertifiedHarvest
												,SUM(IFNULL(b.PohonTM,0)) 'PohonTM'
												,SUM(IFNULL(b.PohonTBM,0)) 'PohonTBM'
												,SUM(IFNULL(b.PohonRehab,0)) 'PohonTR', a.CertificationStart, a.CertificationEnd
								FROM
												ktv_members_garden b
												LEFT JOIN ktv_certification a ON a.FarmerID=b.FarmerID AND a.GardenNr=b.GardenNr AND a.SurveyNr=b.SurveyNr AND a.ExternalDate!='0000-00-00' AND a.ExternalDate IS NOT NULL AND a.Certification!=0
								WHERE b.GardenNr!=0
								GROUP BY FarmerID, SurveyNr, GardenNr
						) pohonbaseline ON pohonbaseline.FarmerID=cert.FarmerID AND pohonbaseline.GardenNr=cert.GardenNr AND pohonbaseline.SurveyNr < IF(IFNULL(pohon2yearago.SurveyNr,IFNULL(pohon1yearago.SurveyNr,cert.SurveyNr))=0,1,IFNULL(pohon2yearago.SurveyNr,IFNULL(pohon1yearago.SurveyNr,cert.SurveyNr))) AND pohonbaseline.SurveyNr = (SELECT MAX(SurveyNr) FROM ktv_members_garden WHERE GardenNr!=0 AND FarmerID=cert.FarmerID AND SurveyNr < IF(IFNULL(pohon2yearago.SurveyNr,IFNULL(pohon1yearago.SurveyNr,cert.SurveyNr))=0,1,IFNULL(pohon2yearago.SurveyNr,IFNULL(pohon1yearago.SurveyNr,cert.SurveyNr))))
						LEFT JOIN sys_user u ON u.UserId=cert.CreatedBy
						LEFT JOIN sys_user uu ON uu.UserId=cert.LastModifiedBy
				WHERE
						cert.Certification!=0 AND cert.GardenNr!=0  AND (cert.SurveyNr=0 OR cert.SurveyNr=?)
						AND adl.StatusAudit IN (1,3)
						AND pre.FarmerID IS NOT NULL
				GROUP BY cert.FarmerID, cert.GardenNr";
		$query = $this->db->query($sql, array($IMSID, $IMSID, $SurveyNr));
		$this->db->trans_complete();
		if ($this->db->trans_status() === true) {
			$results['success'] = true;
			$results['message'] = "Generate AFL Success ($total Farmers).";
			$results['totals']  = $total;
		} else {
			$results['success'] = false;
			$results['message'] = "Failed to generate afl!";
		}
		return $results;
	}

	public function getAFLs($IMSID)
	{
		$sql = "SELECT
					afl.*,
					IFNULL(1YearAgoHarvest,IF(BaselineSurveyNr='0',0,IFNULL(BaselineHarvest,0))) Harvest,
					(IFNULL(o.garden,0) + afl.CertHectare) other
				FROM ktv_certification_afl_farmer afl
					LEFT JOIN (
						SELECT FarmerID, SUM(GardenHa) garden FROM ktv_members_other_land GROUP BY FarmerID
					) o ON o.FarmerID=afl.FarmerID
					LEFT JOIN (
							SELECT farmer_id, SUM(farmer_netto) KgSales, SUM(IF(farmer_ispaid='1',farmer_netto,0)) KgPaid
							FROM rpt_traceability a
							LEFT JOIN ktv_certification_afl_farmer b ON a.farmer_id=b.FarmerID AND b.IMSID=?
							WHERE farmer_id IS NOT NULL AND IFNULL(1_date,2_date) BETWEEN b.1YearAgoStart AND b.1YearAgoEnd
							GROUP BY farmer_id
					) sales1 ON sales1.farmer_id=afl.FarmerID
					LEFT JOIN (
							SELECT farmer_id, SUM(farmer_netto) KgSales, SUM(IF(farmer_ispaid='1',farmer_netto,0)) KgPaid
							FROM rpt_traceability a
							LEFT JOIN ktv_certification_afl_farmer b ON a.farmer_id=b.FarmerID AND b.IMSID=?
							WHERE farmer_id IS NOT NULL AND IFNULL(1_date,2_date) BETWEEN b.2YearAgoStart AND b.2YearAgoEnd
							GROUP BY farmer_id
					) sales2 ON sales2.farmer_id=afl.FarmerID
				WHERE afl.IMSID=?";
		$query = $this->db->query($sql, array($IMSID, $IMSID, $IMSID));
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
	}

	public function getIMSDetail($IMSID)
	{
		$sql = "SELECT
					c.CertProgName,
					d.`Name`,
					a.SurveyNr,
					YEAR(CertificationStart) Year,
					a.CertEventDate,
					a.CertEventName,
					a.Year AS IMSYear,
					b.CertProgID,
					a.ICSStatus,
					a.ExternalAuditStatus
				FROM
					ktv_ims a
					LEFT JOIN ktv_certification_holders b ON a.CertHolderID=b.CertHolderID
					LEFT JOIN ktv_ref_certification_program c ON b.CertProgID=c.CertProgID
					LEFT JOIN view_tc_supplychain_org d ON d.SupplychainID=b.SupplychainID
				WHERE a.IMSID =?";
		$query  = $this->db->query($sql, array($IMSID));
		$return = $query->result_array();
		return $return[0];
	}

	public function getFADetail($UserID)
	{
		$sql = "SELECT
				s.`UserRealName` AS `Name`
			FROM
				sys_user s
			WHERE
				s.`UserId` = ?
			LIMIT 1";
		$query  = $this->db->query($sql, array($UserID));
		$return = $query->result_array();
		return $return[0];
	}

	public function getGardenCheck($IMSID)
	{
		$sql = "SELECT afl.*, g.Latitude, g.Longitude, afl.CertHarvest/afl.CertHectare Productivity, afl.CertPohonTM/afl.CertHectare TreeProductivity, 'Yes' Certification, cp.CertProgName,
					sp.ObjType OrgType, sp.Name, i.ExternalDate, i.ExtensionDate
				FROM
					ktv_certification_afl_garden afl
					LEFT JOIN ktv_members_garden g ON afl.FarmerID=g.FarmerID AND afl.CertSurveyNr=g.SurveyNr AND afl.CertGardenNr=g.GardenNr
					LEFT JOIN ktv_ims i ON i.IMSID=afl.IMSID
					LEFT JOIn ktv_certification_holders ch ON i.CertHolderID=ch.CertHolderID
					LEFT JOIN ktv_ref_certification_program cp ON cp.CertProgID=ch.CertProgID
					LEFT JOIN view_tc_supplychain_org sp ON sp.SupplychainID=ch.SupplychainID
				WHERE afl.IMSID=?";
		$query = $this->db->query($sql, array($IMSID));
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
	}

	public function ImportFarmer($data, $IMSID)
	{
		$param    = $data[0][0];
		$value    = $data[0];
		$insert   = array();
		$j        = 0;
		$berhasil = 0;
		$gagal    = 0;
		
		for ($i = 1; $i < count($value); $i++) {
			//echo count($param);
			$insert[$i]['IMSID']       = $IMSID;
			$insert[$i]['CreatedBy']   = $_SESSION['userid'];
			$insert[$i]['DateCreated'] = date('Y-m-d H:i:s');
			$insert[$i]['FarmerID'] = $value[$i][0];
		}
		// echo "<pre>".print_r($insert,1);exit;
		$this->db->trans_start();
		foreach ($insert as $data_item) {
			$sql = "SELECT * FROM ktv_certification_pre_afl WHERE FarmerID=? AND IMSID=? AND StatusCode='active'";
			$ada = $this->db->query($sql, array($data_item['FarmerID'], $IMSID));
			if ($ada->num_rows() > 0) {
				$gagal++;
			} else {
				if ($data_item['FarmerID'] != '') {


					$sqlcheck = "SELECT
							MemberID 
						FROM
							ktv_members 
						WHERE
							MemberID = ? 
							OR MemberDisplayID = ?
						ORDER BY 
							MemberID DESC
						LIMIT 1
					";
					$querycheck = $this->db->query($sqlcheck,array($data_item['FarmerID'],$data_item['FarmerID']));
					$rowcheck = $querycheck->row_array();

					$data_item['FarmerID'] = $rowcheck['MemberID'];

					//cek farmer aktif tidak dulu
					// $sql="SELECT far.`StatusFarmer` FROM ktv_members far WHERE far.`FarmerID` = ? LIMIT 1";
					// $query = $this->db->query($sql, array($data_item['FarmerID']));
					// $dataCek = $query->row_array();
					// if($dataCek['StatusFarmer'] == "1"){
					//     $data_item['StatusAudit'] = '1';
					// }else{
					//     $data_item['StatusAudit'] = '2';
					// }

					//Tidak jadi cek petani aktif, pokoknya petani yang di importkan akan dianggap eligible semuanya dulu.
					$data_item['StatusAudit'] = '1';

					$insert_query = $this->db->insert_string('ktv_certification_pre_afl', $data_item);
					$query        = $this->db->query($insert_query);

					if ($query) {
						$berhasil++;
					} else {
						$gagal++;
					}
				}
			}

		}
		$this->db->trans_complete();
		if ($this->db->trans_status() === true) {
			$results['infos'] = 'Success';
		} else {
			$results['infos'] = 'Error';
		}
		$results['berhasil'] = $berhasil;
		$results['gagal']    = $gagal;
		return $results;

	}

	public function readFarmerGardens($FarmerID)
	{
		$sql = "SELECT a.FarmerID, CONCAT('[',a.FarmerID,'] ',b.FarmerName) FarmerName, GardenNr, SurveyNr, GardenHauncertified ha, Production,
				IF(
					(IFNULL(PanenBiasaMonths,0) * IFNULL(PanenBiasaPanenMonth,0) * IFNULL(PanenBiasaKg,0)) + (IFNULL(PanenTrekMonths,0) * IFNULL(PanenTrekPanenMonth,0) * IFNULL(PanenTrekKg,0)) + (IFNULL(PanenRayaMonths,0) * IFNULL(PanenRayaPanenMonth,0) * IFNULL(PanenRayaKg,0)) = 0,
					IFNULL(Production,0),
					(IFNULL(PanenBiasaMonths,0) * IFNULL(PanenBiasaPanenMonth,0) * IFNULL(PanenBiasaKg,0)) + (IFNULL(PanenTrekMonths,0) * IFNULL(PanenTrekPanenMonth,0) * IFNULL(PanenTrekKg,0)) + (IFNULL(PanenRayaMonths,0) * IFNULL(PanenRayaPanenMonth,0) * IFNULL(PanenRayaKg,0))
				) CalculatedProduction
				FROM ktv_members_garden a
					LEFT JOIN ktv_members b ON a.FarmerID=b.FarmerID
				WHERE a.FarmerID=? AND a.StatusCode='active'
				ORDER BY SurveyNr, GardenNr";
		$query          = $this->db->query($sql, array($FarmerID));
		$result['data'] = $query->result_array();
		return $result;
	}

	public function getImsFarmerGarden($FarmerID, $IMSID)
	{
		$sql = "SELECT
				tbl_uni.*
			FROM
			(
			SELECT
				a.MemberID FarmerID,
				b.MemberName FarmerName,
				a.PlotNr GardenNr,
				a.SurveyNr,
				GardenAreaHa ha,
				AnnualProduction,
				AnnualProduction CalculatedProduction
			FROM
				ktv_survey_plot a
				LEFT JOIN ktv_members b
					ON a.MemberID = b.MemberID
			WHERE
				a.MemberID = ?
				AND a.StatusCode = 'active'

			UNION

			SELECT
				aflg.`FarmerID`
				, far.MemberName FarmerName
				, aflg.`GardenNr`
				, aflg.`SurveyNr`
				, IFNULL(gar.GardenAreaHa,'-') AS ha
				, IFNULL(gar.AnnualProduction,'-') AS Production
				, AnnualProduction AS CalculatedProduction
			FROM
				ktv_certification_pre_afl_garden aflg
				LEFT JOIN ktv_members far ON far.MemberID = aflg.`FarmerID`
				LEFT JOIN ktv_survey_plot gar ON 1=1
					AND aflg.`FarmerID` = gar.`MemberID`
					AND aflg.`GardenNr` = gar.`PlotNr`
					AND aflg.`SurveyNr` = gar.`SurveyNr`
			WHERE
				aflg.`IMSID` = ?
				AND aflg.`FarmerID` = ?
			) AS tbl_uni
			GROUP BY tbl_uni.FarmerID, tbl_uni.GardenNr, tbl_uni.SurveyNr";
		$query          = $this->db->query($sql, array($FarmerID, $IMSID, $FarmerID));
		$result['data'] = $query->result_array();
		return $result;
	}

	public function readFarmerCertifications($FarmerID)
	{
		//audit status, 1=passed without requirements, 2=passed with requirements, 3=not passed
		$sql = "SELECT a.FarmerID, CONCAT('[',a.FarmerID,'] ',b.FarmerName) FarmerName, GardenNr, SurveyNr, ICSDate,
				CASE StatusAudit WHEN '1' THEN 'Passed' WHEN '2' THEN 'Not Passed' WHEN '3' THEN 'Passed with Requirements' ELSE '-' END StatusAudit,
				CASE Certification WHEN '1' THEN 'UTZ' WHEN '2' THEN 'Rainforest' WHEN '3' THEN 'Fairtrade' WHEN '4' THEN 'Organic' ELSE Certification END AS Certification,
				IF(FarmerSignature IS NULL OR FarmerSignature='','No', 'Yes') FarmerSignature, Certification CertificationID
				FROM ktv_certification a
					LEFT JOIN ktv_members b ON a.FarmerID=b.FarmerID
				WHERE a.FarmerID=?
				ORDER BY SurveyNr, GardenNr";
		$query          = $this->db->query($sql, array($FarmerID));
		$result['data'] = $query->result_array();
		return $result;
	}

	public function getImsFarmerCertifications($FarmerID, $IMSID)
	{
		$sql = "SELECT
				tbl_uni.*
			FROM
			(
			SELECT
				a.FarmerID,
				b.MemberName FarmerName,
				a.GardenNr,
				a.SurveyNr,
				a.ICSDate,
				CASE
					StatusAudit
					WHEN '1' THEN 'Passed'
					WHEN '2' THEN 'Not Passed'
					WHEN '3' THEN 'Passed with Requirements'
					ELSE '-'
				END StatusAudit,
				CASE
					Certification
					WHEN '1' THEN 'UTZ'
					WHEN '2' THEN 'Rainforest'
					WHEN '3' THEN 'Fairtrade'
					WHEN '4' THEN 'Organic'
					ELSE Certification
				END AS Certification,
				IF(
					a.FarmerSignature IS NULL OR a.FarmerSignature = '','No','Yes'
				) AS FarmerSignature,
				Certification AS CertificationID
			FROM
				ktv_certification a
				LEFT JOIN ktv_members b
					ON a.FarmerID = b.MemberID
			WHERE
				a.FarmerID = ?

			UNION

			SELECT
				far.MemberID FarmerID,
				far.MemberName FarmerName,
				aflg.GardenNr,
				aflg.SurveyNr,
				IFNULL(cert.ICSDate,'-') AS ICSDate,
				CASE
					cert.StatusAudit
					WHEN '1' THEN 'Passed'
					WHEN '2' THEN 'Not Passed'
					WHEN '3' THEN 'Passed with Requirements'
					ELSE '-'
				END AS StatusAudit,
				CASE
					cert.Certification
					WHEN '1' THEN 'UTZ'
					WHEN '2' THEN 'Rainforest'
					WHEN '3' THEN 'Fairtrade'
					WHEN '4' THEN 'Organic'
					ELSE '-'
				END AS Certification,
				IF(
					cert.FarmerSignature IS NULL OR cert.FarmerSignature = '','-','Yes'
				) AS FarmerSignature,
				cert.Certification AS CertificationID
			FROM
				ktv_certification_pre_afl_garden aflg
				LEFT JOIN ktv_members far ON far.MemberID = aflg.`FarmerID`
				LEFT JOIN ktv_certification cert ON 1=1
					AND aflg.`FarmerID` = cert.`FarmerID`
					AND aflg.`GardenNr` = cert.`GardenNr`
					AND aflg.`SurveyNr` = cert.`SurveyNr`
			WHERE
				aflg.`IMSID` = ?
				AND aflg.`FarmerID` = ?
			) AS tbl_uni
			GROUP BY tbl_uni.FarmerID, tbl_uni.GardenNr, tbl_uni.SurveyNr";
		$query          = $this->db->query($sql, array($FarmerID, $IMSID, $FarmerID));
		$result['data'] = $query->result_array();
		return $result;
	}

	public function readFarmerAudits($FarmerID)
	{
		$sql = "SELECT a.FarmerID, CONCAT('[',a.FarmerID,'] ',b.FarmerName) FarmerName, GardenNr, SurveyNr, ICSDate,
				CASE StatusAudit WHEN '1' THEN 'Passed' WHEN '2' THEN 'Not Passed' WHEN '3' THEN 'Passed with Requirements' ELSE '-' END StatusAudit,
				CASE Certification WHEN '1' THEN 'UTZ' WHEN '2' THEN 'Rainforest' WHEN '3' THEN 'Fairtrade' WHEN '4' THEN 'Organic' ELSE Certification END AS Certification, Certification CertificationID
				FROM ktv_certification_audit_log a
					LEFT JOIN ktv_members b ON a.FarmerID=b.FarmerID
				WHERE a.FarmerID=?
				ORDER BY SurveyNr, GardenNr, ICSDate";
		$query          = $this->db->query($sql, array($FarmerID));
		$result['data'] = $query->result_array();
		return $result;
	}

	public function getImsFarmerAudits($FarmerID, $IMSID)
	{
		$sql = "SELECT
					tbl_uni.*
				FROM
				(
				SELECT
					a.FarmerID,
					b.MemberName FarmerName,
					a.GardenNr,
					a.SurveyNr,
					a.ICSDate,
					CASE
						StatusAudit
						WHEN '1'
						THEN 'Passed'
						WHEN '2'
						THEN 'Not Passed'
						WHEN '3'
						THEN 'Passed with Requirements'
						ELSE '-'
					END StatusAudit,
					CASE
						Certification
						WHEN '1'
						THEN 'UTZ'
						WHEN '2'
						THEN 'Rainforest'
						WHEN '3'
						THEN 'Fairtrade'
						WHEN '4'
						THEN 'Organic'
						ELSE Certification
					END AS Certification,
					Certification CertificationID
				FROM
					ktv_certification_audit_log a
					LEFT JOIN ktv_members b
						ON a.FarmerID = b.MemberID
				WHERE
					a.FarmerID = ?

				UNION

				SELECT
					far.MemberID FarmerID,
					far.MemberName FarmerName,
					aflg.`GardenNr`,
					aflg.`SurveyNr`,
					IFNULL(au.ICSDate,'-') AS ICSDate,
					CASE
						au.StatusAudit
						WHEN '1' THEN 'Passed'
						WHEN '2' THEN 'Not Passed'
						WHEN '3' THEN 'Passed with Requirements'
						ELSE '-'
					END StatusAudit,
					CASE
						Certification
						WHEN '1' THEN 'UTZ'
						WHEN '2' THEN 'Rainforest'
						WHEN '3' THEN 'Fairtrade'
						WHEN '4' THEN 'Organic'
						ELSE '-'
					END AS Certification,
					IFNULL(Certification,'-') AS CertificationID
				FROM
					ktv_certification_pre_afl_garden aflg
					LEFT JOIN ktv_members far ON far.MemberID = aflg.`FarmerID`
					LEFT JOIN ktv_certification_audit_log au ON 1=1
						AND aflg.`FarmerID` = au.`FarmerID`
						AND aflg.`GardenNr` = au.`GardenNr`
						AND aflg.`SurveyNr` = au.`SurveyNr`
				WHERE
					aflg.`IMSID` = ?
					AND aflg.`FarmerID` = ?
				) AS tbl_uni
				GROUP BY tbl_uni.FarmerID, tbl_uni.GardenNr, tbl_uni.SurveyNr
			";
		$p = array(
			$FarmerID, $IMSID, $FarmerID,
		);
		$query          = $this->db->query($sql, $p);
		$result['data'] = $query->result_array();
		return $result;
	}

	//--Garden--//
	public function readGardenDetail($FarmerID, $SurveyNr, $GardenNr)
	{
		$sql    = "SELECT a.*, b.FarmerName FROM ktv_members_garden a LEFT JOIN ktv_members b ON a.FarmerID=b.FarmerID WHERE a.FarmerID=? AND a.SurveyNr=? AND a.GardenNr=?";
		$query  = $this->db->query($sql, array($FarmerID, $SurveyNr, $GardenNr));
		$return = $query->result_array();
		return $return[0];
	}

	public function updateFarmer($EditType, $FarmerID, $DefaultSurveyNr, $SurveyNr, $DefaultGardenNr, $GardenNr, $DefaultCertification, $Certification, $userID, $GardenHaUncertified, $PohonTM, $PohonTBM, $PohonRehab, $Production, $PanenTrekMonths, $PanenBiasaMonths, $PanenRayaMonths, $PanenTrekPanenMonths, $PanenBiasaPanenMonths, $PanenRayaPanenMonths, $PanenTrekKg, $PanenBiasaKg, $PanenRayaKg, $DefaultICSDate, $ICSDate, $StatusAudit)
	{
		if ($EditType == 'garden') {
			$sql_check   = "SELECT * FROM ktv_members_garden WHERE SurveyNr=? AND GardenNr=? AND FarmerID=? AND (?!=? OR ?!=?)";
			$query_check = $query = $this->db->query($sql_check, array($SurveyNr, $GardenNr, $FarmerID, $DefaultGardenNr, $GardenNr, $DefaultSurveyNr, $SurveyNr));
			if ($query_check->num_rows() > 0) {
				$results['success'] = "false";
				$results['message'] = "Failed to update record. Duplicate data Farmer Garden.";
			} else {
				$sql = "UPDATE ktv_members_garden SET DateSync=NULL, DateUpdated=NOW(), LastModifiedBy=?, SurveyNr=?, GardenNr=?, GardenHaUncertified=?, PohonTM=?, PohonTBM=?, PohonRehab=?, Production=?,"
					. "PanenTrekMonths=?, PanenBiasaMonths=?, PanenRayaMonths=?, PanenTrekPanenMonth=?, PanenBiasaPanenMonth=?, PanenRayaPanenMonth=?, PanenTrekKg=?, PanenBiasaKg=?, PanenRayaKg=? "
					. "WHERE FarmerID=? AND SurveyNr=? AND GardenNr=?";
				$query = $this->db->query($sql, array($userID, $SurveyNr, $GardenNr, $GardenHaUncertified, $PohonTM, $PohonTBM, $PohonRehab, $Production, $PanenTrekMonths, $PanenBiasaMonths, $PanenRayaMonths, $PanenTrekPanenMonths, $PanenBiasaPanenMonths, $PanenRayaPanenMonths, $PanenTrekKg, $PanenBiasaKg, $PanenRayaKg, $FarmerID, $DefaultSurveyNr, $DefaultGardenNr));
				if ($query) {
					$results['success'] = "true";
					$results['message'] = "record updated.";
				} else {
					$results['success'] = "false";
					$results['message'] = "Failed to update record.";
				}
			}
		} else if ($EditType == 'cert') {
			$sql_check   = "SELECT * FROM ktv_certification WHERE SurveyNr=? AND GardenNr=? AND FarmerID=? AND Certification=? AND (?!=? OR ?!=? OR ?!=?)";
			$query_check = $query = $this->db->query($sql_check, array($SurveyNr, $GardenNr, $FarmerID, $Certification, $DefaultGardenNr, $GardenNr, $DefaultSurveyNr, $SurveyNr, $DefaultCertification, $Certification));
			if ($query_check->num_rows() > 0) {
				$results['success'] = "false";
				$results['message'] = "Failed to update record. Duplicate data Farmer Certification.";
			} else {
				$sql = "UPDATE ktv_certification SET DateSync=NULL, DateUpdated=NOW(), LastModifiedBy=?, SurveyNr=?, GardenNr=?, Certification=?, ICSDate=?, StatusAudit=? "
					. "WHERE FarmerID=? AND SurveyNr=? AND GardenNr=? AND Certification=?";
				$query = $this->db->query($sql, array($userID, $SurveyNr, $GardenNr, $Certification, $ICSDate, $StatusAudit, $FarmerID, $DefaultSurveyNr, $DefaultGardenNr, $DefaultCertification));
				if ($query) {
					$results['success'] = "true";
					$results['message'] = "record updated.";
				} else {
					$results['success'] = "false";
					$results['message'] = "Failed to update record.";
				}
			}
		} else if ($EditType == 'audit') {
			$sql_check   = "SELECT * FROM ktv_certification_audit_log WHERE SurveyNr=? AND GardenNr=? AND FarmerID=? AND Certification=? AND ICSDate=? AND (?!=? OR ?!=? OR ?!=? OR ?!=?)";
			$query_check = $query = $this->db->query($sql_check, array($SurveyNr, $GardenNr, $FarmerID, $Certification, $ICSDate, $DefaultGardenNr, $GardenNr, $DefaultSurveyNr, $SurveyNr, $DefaultCertification, $Certification, $DefaultICSDate, $ICSDate));
			if ($query_check->num_rows() > 0) {
				$results['success'] = "false";
				$results['message'] = "Failed to update record. Duplicate data Farmer Audit Log.";
			} else {
				$sql = "UPDATE ktv_certification_audit_log SET DateSync=NULL, DateUpdated=NOW(), LastModifiedBy=?, SurveyNr=?, GardenNr=?, Certification=?, ICSDate=?, StatusAudit=? "
					. "WHERE FarmerID=? AND SurveyNr=? AND GardenNr=? AND Certification=? AND ICSDate=?";
				$query = $this->db->query($sql, array($userID, $SurveyNr, $GardenNr, $Certification, $ICSDate, $StatusAudit, $FarmerID, $DefaultSurveyNr, $DefaultGardenNr, $DefaultCertification, $DefaultICSDate));
				if ($query) {
					$results['success'] = "true";
					$results['message'] = "record updated.";
				} else {
					$results['success'] = "false";
					$results['message'] = "Failed to update record.";
				}
			}
		}
		$results['tipe'] = $EditType;
		return $results;
	}

	public function duplicateGarden($UserID, $FarmerID, $SurveyNr, $GardenNr, $IMSSurveyNr)
	{
		$sql_check   = "SELECT * FROM ktv_members_garden WHERE SurveyNr=? AND GardenNr=? AND FarmerID=?";
		$query_check = $query = $this->db->query($sql_check, array($IMSSurveyNr, $GardenNr, $FarmerID));
		if ($query_check->num_rows() > 0) {
			$results['success'] = "false";
			$results['message'] = "Failed to duplicate record. Data garden already exist.";
		} else {
			$sql                      = "SELECT * FROM ktv_members_garden WHERE FarmerID=? AND SurveyNr=? AND GardenNr=?";
			$query                    = $this->db->query($sql, array($FarmerID, $SurveyNr, $GardenNr))->result_array();
			$garden                   = $query[0];
			$garden['CreatedBy']      = $UserID;
			$garden['DateCreated']    = date('Y-m-d H:i:s');
			$garden['LastModifiedBy'] = '';
			$garden['DateUpdated']    = '';
			$garden['DateSync']       = null;
			$garden['uid']            = null;
			$garden['SurveyNr']       = $IMSSurveyNr;
			$insert                   = $this->db->insert('ktv_members_garden', $garden);
			if ($insert) {
				$results['success'] = "true";
				$results['message'] = "Duplicate record success.";
			} else {
				$results['success'] = "false";
				$results['message'] = "Failed to duplicate record.";
			}
		}
		return $results;
	}

	public function deleteGarden($UserID, $FarmerID, $SurveyNr, $GardenNr)
	{
		$sql_check   = "SELECT * FROM ktv_members_garden WHERE SurveyNr=? AND GardenNr=? AND FarmerID=?";
		$query_check = $query = $this->db->query($sql_check, array($SurveyNr, $GardenNr, $FarmerID));
		if ($query_check->num_rows() == 0) {
			$results['success'] = "false";
			$results['message'] = "Failed to delete record. Data not found.";
		} else {
			$this->db->trans_start();
			$sql                   = "SELECT * FROM ktv_members_garden WHERE FarmerID=? AND SurveyNr=? AND GardenNr=?";
			$query                 = $this->db->query($sql, array($FarmerID, $SurveyNr, $GardenNr))->result_array();
			$garden                = $query[0];
			$garden['DeleteBy']    = $UserID;
			$garden['DateHistory'] = date('Y-m-d H:i:s');
			unset($garden['uid'], $garden['Lokal'], $garden['c04'], $garden['c04Nr'], $garden['c07'], $garden['c07Nr'], $garden['BB'], $garden['BBNr'], $garden['ShadeTreesReason'], $garden['ObtainSeedsTodayNr'], $garden['FrPengapuran'], $garden['DosePengapuran']);
			unset($garden['Insectisida24'], $garden['BeanGraftedTrees'], $garden['BeanGraftedTreesTahun'], $garden['FrFertiliaKakao'], $garden['DoFertiliaKakao'], $garden['FrNitrabor'], $garden['DoNitrabor'], $garden['ParticipateChildEducation'], $garden['CutWageForDisciplinary'], $garden['DoCutWageForWorker'], $garden['WagePaidByPerformance'], $garden['PayingWorkerWageByPerformance']);
			unset($garden['HandlingFirstAidInGarden'], $garden['FirstAidKitLocation'], $garden['WorkerNotHandlePesticide'], $garden['WorkerAccessSafeDrinkingWater'], $garden['BufferZoneGarden'], $garden['LandOpeningForest'], $garden['LandOpeningForestCertificate'], $garden['IdentifyProtectRareSpecies'], $garden['AreaRevision'], $garden['DoCutWageForWorker'], $garden['WagePaidByPerformance'], $garden['PayingWorkerWageByPerformance']);
			//, $garden['FrPengapuran']
			$insert = $this->db->insert('his_ktv_members_garden', $garden);
			if ($insert) {
				$sql   = "DELETE FROM ktv_members_garden WHERE FarmerID=? AND SurveyNr=? AND GardenNr=?";
				$query = $this->db->query($sql, array($FarmerID, $SurveyNr, $GardenNr));
			} else {
				$results['success'] = "false";
				$results['message'] = "Failed to delete record.";
			}
			$this->db->trans_complete();
			if ($this->db->trans_status() === true) {
				$results['success'] = "true";
				$results['message'] = "Delete record success.";
			} else {
				$results['success'] = false;
				$results['message'] = "Failed to delete record.";
			}
		}
		return $results;
	}
	//--Certification--//
	public function readCertificationDetail($FarmerID, $SurveyNr, $GardenNr, $Certification)
	{
		$sql    = "SELECT a.*, b.MemberName FarmerName FROM ktv_certification a LEFT JOIN ktv_members b ON a.FarmerID=b.MemberID WHERE a.FarmerID=? AND a.SurveyNr=? AND a.GardenNr=? AND a.Certification=?";
		$query  = $this->db->query($sql, array($FarmerID, $SurveyNr, $GardenNr, $Certification));
		$return = $query->result_array();
		return $return[0];
	}
	public function duplicateCertification($UserID, $FarmerID, $SurveyNr, $GardenNr, $IMSSurveyNr, $Certification)
	{
		$sql_check   = "SELECT * FROM ktv_certification WHERE SurveyNr=? AND GardenNr=? AND FarmerID=? AND Certification=?";
		$query_check = $query = $this->db->query($sql_check, array($IMSSurveyNr, $GardenNr, $FarmerID, $Certification));
		if ($query_check->num_rows() > 0) {
			$results['success'] = "false";
			$results['message'] = "Failed to duplicate record. Data certification already exist.";
		} else {
			$sql                      = "SELECT * FROM ktv_certification WHERE FarmerID=? AND SurveyNr=? AND GardenNr=? AND Certification=?";
			$query                    = $this->db->query($sql, array($FarmerID, $SurveyNr, $GardenNr, $Certification))->result_array();
			$garden                   = $query[0];
			$garden['CreatedBy']      = $UserID;
			$garden['DateCreated']    = date('Y-m-d H:i:s');
			$garden['LastModifiedBy'] = '';
			$garden['DateUpdated']    = '';
			$garden['DateSync']       = null;
			$garden['uid']            = null;
			$garden['SurveyNr']       = $IMSSurveyNr;
			$insert                   = $this->db->insert('ktv_certification', $garden);
			if ($insert) {
				$results['success'] = "true";
				$results['message'] = "Duplicate record success.";
			} else {
				$results['success'] = "false";
				$results['message'] = "Failed to duplicate record.";
			}
		}
		return $results;
	}
	public function deleteCertification($UserID, $FarmerID, $SurveyNr, $GardenNr, $Certification)
	{
		$sql_check   = "SELECT * FROM ktv_certification WHERE SurveyNr=? AND GardenNr=? AND FarmerID=? AND Certification=?";
		$query_check = $query = $this->db->query($sql_check, array($SurveyNr, $GardenNr, $FarmerID, $Certification));
		if ($query_check->num_rows() == 0) {
			$results['success'] = "false";
			$results['message'] = "Failed to delete record. Data not found.";
		} else {
			$this->db->trans_start();
			$sql                   = "SELECT * FROM ktv_certification WHERE FarmerID=? AND SurveyNr=? AND GardenNr=? AND Certification=?";
			$query                 = $this->db->query($sql, array($FarmerID, $SurveyNr, $GardenNr, $Certification))->result_array();
			$garden                = $query[0];
			$garden['DeleteBy']    = $UserID;
			$garden['DateHistory'] = date('Y-m-d H:i:s');
			unset($garden['uid'], $garden['IMSID']);
			$insert = $this->db->insert('his_ktv_certification', $garden);
			if ($insert) {
				$sql   = "DELETE FROM ktv_certification WHERE FarmerID=? AND SurveyNr=? AND GardenNr=? AND Certification=?";
				$query = $this->db->query($sql, array($FarmerID, $SurveyNr, $GardenNr, $Certification));
			} else {
				$results['success'] = "false";
				$results['message'] = "Failed to delete record.";
			}
			$this->db->trans_complete();
			if ($this->db->trans_status() === true) {
				$results['success'] = "true";
				$results['message'] = "Delete record success.";
			} else {
				$results['success'] = false;
				$results['message'] = "Failed to delete record.";
			}
		}
		return $results;
	}
	//--Audit--//
	public function readAuditDetail($FarmerID, $SurveyNr, $GardenNr, $Certification, $ICSDate)
	{
		$sql    = "SELECT a.*, b.MemberName FarmerName FROM ktv_certification_audit_log a LEFT JOIN ktv_members b ON a.FarmerID=b.MemberID WHERE a.FarmerID=? AND a.SurveyNr=? AND a.GardenNr=? AND a.Certification=? AND a.ICSDate=?";
		$query  = $this->db->query($sql, array($FarmerID, $SurveyNr, $GardenNr, $Certification, $ICSDate));
		$return = $query->result_array();
		return $return[0];
	}
	public function duplicateAudit($UserID, $FarmerID, $SurveyNr, $GardenNr, $IMSSurveyNr, $Certification, $ICSDate)
	{
		$sql_check   = "SELECT * FROM ktv_certification_audit_log WHERE SurveyNr=? AND GardenNr=? AND FarmerID=? AND Certification=? AND ICSDate=?";
		$query_check = $query = $this->db->query($sql_check, array($IMSSurveyNr, $GardenNr, $FarmerID, $Certification, $ICSDate));
		if ($query_check->num_rows() > 0) {
			$results['success'] = "false";
			$results['message'] = "Failed to duplicate record. Data audit log already exist.";
		} else {
			$sql                      = "SELECT * FROM ktv_certification_audit_log WHERE FarmerID=? AND SurveyNr=? AND GardenNr=? AND Certification=? AND ICSDate=?";
			$query                    = $this->db->query($sql, array($FarmerID, $SurveyNr, $GardenNr, $Certification, $ICSDate))->result_array();
			$garden                   = $query[0];
			$garden['CreatedBy']      = $UserID;
			$garden['DateCreated']    = date('Y-m-d H:i:s');
			$garden['LastModifiedBy'] = '';
			$garden['DateUpdated']    = '';
			$garden['DateSync']       = null;
			$garden['uid']            = null;
			$garden['SurveyNr']       = $IMSSurveyNr;
			$insert                   = $this->db->insert('ktv_certification_audit_log', $garden);
			if ($insert) {
				$results['success'] = "true";
				$results['message'] = "Duplicate record success.";
			} else {
				$results['success'] = "false";
				$results['message'] = "Failed to duplicate record.";
			}
		}
		return $results;
	}
	public function deleteAudit($UserID, $FarmerID, $SurveyNr, $GardenNr, $Certification, $ICSDate)
	{
		$sql_check   = "SELECT * FROM ktv_certification_audit_log WHERE SurveyNr=? AND GardenNr=? AND FarmerID=? AND Certification=? AND ICSDate=?";
		$query_check = $query = $this->db->query($sql_check, array($SurveyNr, $GardenNr, $FarmerID, $Certification, $ICSDate));
		if ($query_check->num_rows() == 0) {
			$results['success'] = "false";
			$results['message'] = "Failed to delete record. Data not found.";
		} else {
			$this->db->trans_start();
			$sql                   = "SELECT * FROM ktv_certification_audit_log WHERE FarmerID=? AND SurveyNr=? AND GardenNr=? AND Certification=? AND ICSDate=?";
			$query                 = $this->db->query($sql, array($FarmerID, $SurveyNr, $GardenNr, $Certification, $ICSDate))->result_array();
			$garden                = $query[0];
			$garden['DeleteBy']    = $UserID;
			$garden['DateHistory'] = date('Y-m-d H:i:s');
			unset($garden['uid'], $garden['IMSID']);
			$insert = $this->db->insert('his_ktv_certification_audit_log', $garden);
			if ($insert) {
				$sql   = "DELETE FROM ktv_certification_audit_log WHERE FarmerID=? AND SurveyNr=? AND GardenNr=? AND Certification=? AND ICSDate=?";
				$query = $this->db->query($sql, array($FarmerID, $SurveyNr, $GardenNr, $Certification, $ICSDate));
			} else {
				$results['success'] = "false";
				$results['message'] = "Failed to delete record.";
			}
			$this->db->trans_complete();
			if ($this->db->trans_status() === true) {
				$results['success'] = "true";
				$results['message'] = "Delete record success.";
			} else {
				$results['success'] = false;
				$results['message'] = "Failed to delete record.";
			}
		}
		return $results;
	}

	public function getIMSInfo($IMSID)
	{
		$sql = "SELECT
					CertHolderOrgName
					, c.`Name`
					, b.CertHolderResponsible
					, refcert.CertProgName AS 'Certification'
					, a.ParamOfflineProgramID
					, a.ParamOfflineOrgUnitID
				FROM
					ktv_ims a
					LEFT JOIN ktv_certification_holders b ON a.CertHOlderID = b.CertHOlderID
					LEFT JOIN view_tc_supplychain_org c ON c.SupplychainID = b.SupplychainID
					LEFT JOIN ktv_ref_certification_program refcert ON b.CertProgID = refcert.CertProgID
				WHERE
					a.IMSID = ?
				LIMIT 1";
		$query = $this->db->query($sql, array($IMSID))->result_array();
		return $query[0];
	}
	public function getFarmerInfo($FarmerID)
	{
		$sql = "SELECT a.MemberID FarmerID, a.MemberName FarmerName, a.Address, b.GroupName, c.BankName , IF(a.BankBranchName=0,'',a.BankBranchName) BankBranch, IF(a.BankBeneficiary=0,'',a.BankBeneficiary) AccountBeneficiary, IF(a.BankAccNumber=0,'',a.BankAccNumber) AccountNumber
				FROM ktv_members a
					LEFT JOIN ktv_farmer_group b ON a.FarmerGroupID=b.FarmerGroupID
					LEFT JOIn ktv_bank c ON c.BankID=a.BankID
				WHERE a.MemberID =?";
		$query = $this->db->query($sql, array($FarmerID))->result_array();
		return $query[0];
	}

	public function getApplicantInfo($ApplicantID)
	{
		$sql = "SELECT
				a.`DisplayID` AS FarmerID
				, a.`Fullname` AS FarmerName
				, a.`Address`
				, a.NIN AS NoKTP
				, b.`GroupName`
				, '' AS BankName
				, '' AS BankBranch
				, '' AS AccountBeneficiary
				, '' AS AccountNumber
			FROM
				ktv_applicant_farmers a
				LEFT JOIN ktv_cpg b ON 1=1
					AND a.`CPGid` = b.`CPGid`
			WHERE
				a.`ApplicantID` = ?
			LIMIT 1";
		return $this->db->query($sql, array($ApplicantID))->row_array();
	}

	public function getIMSFile($IMSID)
	{
		$query = $this->db->get_where('ktv_ims_files', array('IMSID' => $IMSID, 'StatusCode' => 'active'));
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
		return false;
	}
	public function deleteIMSFile($IMSID)
	{
		$detail = $this->getIMSFile($IMSID);
		if ($detail !== false) {
			foreach ($detail as $key => $value) {
				rename_file($value['full_path'], $value['file_path'] . 'deleted/' . $value['file_name']);
				$this->db->update('ktv_ims_files', array('StatusCode' => 'nullified'), array('IMSFileID' => $value['IMSFileID']));
			}
		}
	}
	public function addIMSFile($IMSID, $Remarks, $upload_data)
	{
		if ($detail = $this->getIMSFile($IMSID)) {
			$this->deleteIMSFile($IMSID);
		}
		$data = array(
			'IMSID'     => $IMSID,
			'Remarks'   => $Remarks,
			'file_name' => $upload_data['file_name'],
			'file_type' => $upload_data['file_type'],
			'file_path' => $upload_data['file_path'],
			'full_path' => $upload_data['full_path'],
		);
		return $this->db->insert('ktv_ims_files', $data);
	}

	public function getMainListMasterIms($searchDesc, $start, $limit, $sortingField, $sortingDir)
	{
		if ($sortingField == "") {
			$sortingField = 'DateEstablishedGrid';
		}

		if ($sortingDir == "") {
			$sortingDir = 'DESC';
		}

		//Filter Hak Akses
		if ($_SESSION['role'] == "Private") {
			$sqlJoinHakAkses = " INNER JOIN ktv_ims ims ON a.`IMSMasterID` = ims.`IMSMasterID`
								INNER JOIN ktv_first_buyer ims_fb ON ims.`FirstBuyerID` = ims_fb.`FirstBuyerID` ";
			$sqlWhereHakAkses = " AND ims_fb.`FirstBuyerPartnerID` = '{$_SESSION['PartnerID']}' ";
			$sqlGroupHakAkses = " GROUP BY a.`IMSMasterID` ";
			$sqlJoinHakAkses  = "";
			$sqlWhereHakAkses = "";
			$sqlGroupHakAkses = "";
		} else {
			$sqlJoinHakAkses  = "";
			$sqlWhereHakAkses = "";
			$sqlGroupHakAkses = "";
		}

		$sql = "SELECT
				SQL_CALC_FOUND_ROWS
				a.`IMSMasterID` AS id
				, a.`DateEstablished` AS DateEstablishedGrid
				, a.`Description` AS DescriptionGrid
				, IFNULL(
					CASE
					WHEN b.ObjType = 'farmer_group' THEN
						(
							SELECT
								(SELECT dis.District FROM ktv_district dis WHERE dis.DistrictID = SUBSTR(fgroup.`VillageID`,1,4))
							FROM
								ktv_farmer_group fgroup
							WHERE
								fgroup.`FarmerGroupID` = b.ObjID
							LIMIT 1
						)
					WHEN b.ObjType = 'cooperative' THEN
						(
							SELECT
								(SELECT dis.District FROM ktv_district dis WHERE dis.DistrictID = SUBSTR(coop.`VillageID`,1,4))
							FROM
								ktv_cooperatives coop
							WHERE
								coop.`CoopID` = b.ObjID
							LIMIT 1
						)
				END,'-') AS District
				, CONCAT('[',UPPER(
					CASE
						WHEN b.`ObjType` = 'farmer_group' THEN '" . lang('Farmer Group') . "'
						WHEN b.`ObjType` = 'cooperative' THEN '" . lang('Cooperative') . "'
						ELSE 'Org'
					END
				),'] ',b.`CertHolderOrgName`) AS labelCH
				, d.CertProgName AS TypeOfBean
				, d.`CertProgOfficialName` AS ProgSert
			FROM
				ktv_ims_master a
				INNER JOIN `ktv_certification_holders` b ON a.`CertHolderID` = b.`CertHolderID`
				LEFT JOIN ktv_ref_certification_program d ON b.`CertProgID` = d.`CertProgID`
				$sqlJoinHakAkses
			WHERE
				a.`StatusCode` = 'active'
				AND (a.Description LIKE ? OR b.`CertHolderOrgName` LIKE ? )
				$sqlWhereHakAkses
			$sqlGroupHakAkses
			ORDER BY $sortingField $sortingDir
			LIMIT ?,?
			";
		$p = array(
			'%' . $searchDesc . '%',
			'%' . $searchDesc . '%',
			(int) $start, (int) $limit,
		);
		$query          = $this->db->query($sql, $p);
		$result['data'] = $query->result_array();

		$query           = $this->db->query('SELECT FOUND_ROWS() AS total');
		$result['total'] = $query->row()->total;

		return $result;
	}

	public function getComboCertHolder()
	{
		$sql = "SELECT
				a.CertHolderID id,
				CONCAT('[',
				CASE
                    WHEN a.ObjType = 'farmer_group' THEN 'Farmer Group'
                    WHEN a.ObjType = 'cooperative' THEN 'Cooperative'
                    ELSE '-' 
				END
				,'] ',IFNULL(a.CertHolderOrgName,''), ' - ',IFNULL(c.`CertProgName`,'')) label
			FROM
				ktv_certification_holders a
				LEFT JOIN ktv_ref_certification_program c ON a.`CertProgID` = c.`CertProgID`
			WHERE
				a.StatusCode != 'nullified'
			ORDER BY label";
		$query = $this->db->query($sql);

		$return['data'] = $query->result_array();
		return $return;
	}

	public function GetCmbCertHolderByFirstBuyerID($FirstBuyerID){
		$sql="SELECT
				a.CertHolderID id,
				CONCAT('[',UPPER(
					CASE
						WHEN b.`ObjType` = 'agent' THEN '" . lang('Agent') . "'
						WHEN b.`ObjType` = 'farmer_group' THEN '" . lang('Farmer Group') . "'
						WHEN b.`ObjType` = 'mill' THEN '" . lang('Mill') . "'
						ELSE 'Org'
					END
				),'] ',b.Name, ' - ',c.`CertProgName`) label
			FROM
				ktv_certification_holders a
				LEFT JOIN view_tc_supplychain_org b ON a.SupplychainID=b.SupplychainID
				LEFT JOIN ktv_ref_certification_program c ON a.`CertProgID` = c.`CertProgID`

				LEFT JOIN ktv_ims ims ON a.`CertHolderID` = ims.`CertHolderID`
				LEFT JOIN ktv_first_buyer fb ON ims.`FirstBuyerID` = fb.`FirstBuyerID`
			WHERE
				a.StatusCode = 'active'
				AND ims.`StatusCode` = 'active'
				AND fb.`FirstBuyerPartnerID` = ?
			GROUP BY a.`CertHolderID`
			ORDER BY label";
		return $this->db->query($sql,array($FirstBuyerID))->result_array();
	}

	public function imsMasterFillForm($IMSMasterID)
	{
		$sql = "SELECT
				a.`IMSMasterID` AS imsCertFormImsEvent_IMSMasterID,
				a.`CertHolderID` AS imsCertFormImsEvent_cmbCertHolder,
				a.`DateEstablished` AS imsCertFormImsEvent_DateEstablished,
				a.`Description` AS imsCertFormImsEvent_Description,
				c.`CertProgOfficialName` AS imsCertFormImsEvent_CertHolderProgram
			FROM
				`ktv_ims_master` a
				LEFT JOIN ktv_certification_holders b ON a.`CertHolderID` = b.`CertHolderID`
				LEFT JOIN ktv_ref_certification_program c ON b.`CertProgID` = c.`CertProgID`
			WHERE
				a.`IMSMasterID` = ?
			LIMIT 1";
		$query             = $this->db->query($sql, array($IMSMasterID));
		$return['success'] = true;
		$return['data']    = $query->row_array();
		return $return;
	}

	public function getCertHolderProgByImsMaster($IMSMasterID)
	{
		$sql = "SELECT
				a.`CertHolderID`
				, CONCAT('[',
				CASE
                    WHEN c.ObjType = 'mill' THEN 'Mill'
                    WHEN c.ObjType = 'agent' THEN 'Agent'
                    WHEN c.ObjType = 'refinery' THEN 'Refinery'
                    WHEN c.ObjType = 'bulking' THEN 'Bulking'
                    WHEN c.ObjType = 'kcp' THEN 'KCP'
                    WHEN c.ObjType = 'farmer_group' THEN 'Farmer Group'
                    WHEN c.ObjType = 'cooperative' THEN 'Cooperative'
                    ELSE '-' 
				END
				,'] ',c.Name) AS SupplychainLabel,
				d.`CertProgName` AS CertHolderProgramLabel
				, b.CertProgMemberID
				, b.CertProgMemberDate
				, b.GIPNumber
			FROM
				ktv_ims_master a
				LEFT JOIN ktv_certification_holders b ON a.`CertHolderID` = b.`CertHolderID`
				LEFT JOIN view_tc_supplychain_org c ON b.SupplychainID = c.SupplychainID
				LEFT JOIN ktv_ref_certification_program d ON b.`CertProgID` = d.`CertProgID`
			WHERE
				a.`IMSMasterID` = ?
			LIMIT 1";
		$query = $this->db->query($sql, array($IMSMasterID));
		$data  = $query->row_array();

		return $data;
	}

	public function getGridAnnualCertificate($IMSMasterID, $SearchEventName, $start, $limit, $sortingField, $sortingDir)
	{
		if ($sortingField == "") {
			$sortingField = 'EventYear';
		}

		if ($sortingDir == "") {
			$sortingDir = 'DESC';
		}

		$sql = "
			SELECT
				SQL_CALC_FOUND_ROWS
				a.IMSID
				, a.CertEventName AS EventName
				, a.Year AS EventYear
				, IF(fbp.ProgramName IS NOT NULL, CONCAT(fbp.ProgramName, ' (', fbp.ProgramYear, ')'), a.Year) EventProgram
				, IFNULL(fbp.ProgramYear, a.Year) ProgramYear
				, c.`PartnerName` AS FirstBuyer
				, a.`Location`
				, a.`CertificationStart`
				, a.`CertificationEnd`
				, CONCAT(IFNULL(a.`CertificationStart`,'0000-00-00'),' to ',IFNULL(a.`CertificationEnd`,'0000-00-00')) AS DateOfCertification
				, CONCAT(IFNULL(a.ValidityStart,'0000-00-00'),' to ',IFNULL(a.ValidityEnd,'0000-00-00')) AS DateValid
				, COUNT(cert.`FarmerID`) AS NrOfFarmerCert
				, CAST((SUM(cert.SalesQuota)) / 1000 AS DECIMAL(10,2))  AS Quota
				, IFNULL(a.kmlPath,'NoFile') AS FileKML
				, CASE
					WHEN a.CertEventStatus = 1 THEN 'Ongoing'
					WHEN a.CertEventStatus = 2 THEN 'Completed'
					WHEN a.CertEventStatus = 3 THEN 'Canceled'
					ELSE '-'
				END AS EventStatus
				, a.CertEventStatus AS EventStatusRaw
				, a.CertEventStatus AS Status
				, CAST((SUM(cert.CertNextHarvest)) / 1000 AS DECIMAL(10,2))  AS TotVolApp
				, SUM(cert.CertHectare) AS TotHectare
			FROM
				ktv_ims a
				LEFT JOIN ktv_first_buyer b ON a.`FirstBuyerID` = b.`FirstBuyerID`
				LEFT JOIN ktv_program_partner c ON b.`FirstBuyerPartnerID` = c.`PartnerID`
				LEFT JOIN ktv_certification_certified_farmer cert ON a.IMSID = cert.IMSID AND cert.StatusCode = 'active'
				LEFT JOIN ktv_certification_holders hold ON a.CertHolderID = hold.CertHolderID
				LEFT JOIN ktv_ref_certification_program prog ON hold.CertProgID = prog.CertProgID
				LEFT JOIN ktv_first_buyer_program fbp ON a.ProgID = fbp.ProgID
			WHERE
				a.`StatusCode` = 'active'
				AND a.CertEventName LIKE ?
				AND a.`IMSMasterID` = ?
			GROUP BY a.IMSID
			ORDER BY $sortingField $sortingDir
			LIMIT ?,?
			";
		$p = array(
			'%' . $SearchEventName . '%',
			$IMSMasterID,
			(int) $start, (int) $limit,
		);
		$query          = $this->db->query($sql, $p);
		$result['data'] = $query->result_array();

		//replace string FileKML
		// for ($i = 0; $i < count($result['data']); $i++) {
		//     $result['data'][$i]['FileKML'] = str_replace(" ", "_", $result['data'][$i]['FileKML']);

		//     //cek File ada tidak
		//     if (!file_exists('files/ims_kml/' . $result['data'][$i]['FileKML'])) {
		//         $result['data'][$i]['FileKML'] = 'NoFile';
		//     }
		// }

		$query           = $this->db->query('SELECT FOUND_ROWS() AS total');
		$result['total'] = $query->row()->total;

		return $result;
	}

	public function getImsEventGridStaff($IMSMasterID, $SearchStaffName, $start, $limit, $sortingField, $sortingDir)
	{
		if ($sortingField == "") {
			$sortingField = 'StaffName';
		}

		if ($sortingDir == "") {
			$sortingDir = 'ASC';
		}

		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					a.IMSStaffID
					, a.IMSMasterID
					, a.StaffID
					, c.`PersonNm` AS StaffName
					, CASE
						WHEN b.ObjType = 'program' THEN 'Program'
						WHEN b.ObjType = 'private' THEN 'Private'
						WHEN b.ObjType = 'extension' THEN 'Extension'
						WHEN b.ObjType = 'sce' THEN 'SCE'
						WHEN b.ObjType = 'Trader' THEN 'Trader'
						WHEN b.ObjType = 'cooperative' THEN 'Cooperative'
						WHEN b.ObjType = 'warehouse' THEN 'Warehouse'
						WHEN b.ObjType = 'bank' THEN 'Bank'
						WHEN b.ObjType = 'farmergroup' THEN 'Farmer Group'
					END AS StaffRoleType
					, CASE
						WHEN c.Gender = 'm' THEN '" . lang('Male') . "'
						WHEN c.Gender = 'f' THEN '" . lang('Female') . "'
					END AS Gender
					, b.OfficialEmail AS Email
					, d.WorkAreaName AS WorkAreaLabel
				FROM
					ktv_ims_staff a
					LEFT JOIN ktv_staffs b ON a.`StaffID` = b.`StaffID`
					LEFT JOIN ktv_persons c ON b.`PersonID` = c.`PersonID`
					LEFT JOIN ktv_ref_work_area d ON b.WorkAreaID = d.WorkAreaID
				WHERE
					a.`IMSMasterID` = ?
					AND c.PersonNm LIKE ?
					AND a.`StatusCode` = 'active'
				GROUP BY a.StaffID
				ORDER BY $sortingField $sortingDir
				LIMIT ?,?
				";
		$p = array(
			$IMSMasterID,
			'%' . $SearchStaffName . '%',
			(int) $start, (int) $limit,
		);
		$query          = $this->db->query($sql, $p);
		$result['data'] = $query->result_array();

		$query           = $this->db->query('SELECT FOUND_ROWS() AS total');
		$result['total'] = $query->row()->total;

		return $result;
	}

	public function getImsEventGridStaffInput($IMSMasterID, $IMSID, $start, $limit, $sortingField, $sortingDir)
	{
		if ($sortingField == "") {
			$sortingField = 'StaffName';
		}

		if ($sortingDir == "") {
			$sortingDir = 'ASC';
		}

		$sql = "SELECT
				SQL_CALC_FOUND_ROWS
				a.IMSStaffID
				, a.StaffID
				, c.`PersonNm` AS StaffName
				, CASE
					WHEN b.ObjType = 'program' THEN 'Program'
					WHEN b.ObjType = 'private' THEN 'Private'
					WHEN b.ObjType = 'extension' THEN 'Extension'
					WHEN b.ObjType = 'sce' THEN 'SCE'
					WHEN b.ObjType = 'Trader' THEN 'Trader'
					WHEN b.ObjType = 'cooperative' THEN 'Cooperative'
					WHEN b.ObjType = 'warehouse' THEN 'Warehouse'
					WHEN b.ObjType = 'bank' THEN 'Bank'
					WHEN b.ObjType = 'farmergroup' THEN 'Farmer Group'
				END AS StaffRoleType
				, CASE
					WHEN c.Gender = 'm' THEN 'Male'
					WHEN c.Gender = 'f' THEN 'Female'
				END AS Gender
				, b.OfficialEmail AS Email
				, d.WorkAreaName AS WorkAreaLabel
			FROM
				ktv_ims_staff a
				LEFT JOIN ktv_staffs b ON a.`StaffID` = b.`StaffID`
				LEFT JOIN ktv_persons c ON b.`PersonID` = c.`PersonID`
				LEFT JOIN ktv_ref_work_area d ON b.WorkAreaID = d.WorkAreaID
			WHERE
				a.`IMSMasterID` = ?
				AND ( a.IMSID != ? OR a.IMSID IS NULL)
				AND a.`StatusCode` = 'active'
			ORDER BY $sortingField $sortingDir
			LIMIT ?,?";
		$p = array(
			$IMSMasterID, $IMSID,
			(int) $start, (int) $limit,
		);
		$query          = $this->db->query($sql, $p);
		$result['data'] = $query->result_array();

		$query           = $this->db->query('SELECT FOUND_ROWS() AS total');
		$result['total'] = $query->row()->total;

		return $result;
	}

	public function getImsEventGridSummary($IMSMasterID)
	{
		//get IMSID nya
		$sql = "SELECT
				a.`IMSID`
				, a.CertEventName AS EventName
				, a.`Year` AS EventYear
				, a.CertEventDate AS EventDate
			FROM
				ktv_ims a
			WHERE
				a.`IMSMasterID` = ?
				AND a.`StatusCode` = 'active'
			ORDER BY a.`Year` DESC";
		$query   = $this->db->query($sql, array($IMSMasterID));
		$dataIMS = $query->result_array();

		$arrReturn = array();
		for ($i = 0; $i < count($dataIMS); $i++) {

			//Capaian Farmer
			$sql = "SELECT
					COUNT(a.`DateUpdated`) AS BANYAK
				FROM
					ktv_certification_pre_afl afl
					INNER JOIN ktv_members a ON afl.`FarmerID` = a.`FarmerID`
				WHERE
					a.`StatusCode` = 'active'
					AND afl.`IMSID` = ?
					AND DATE(a.`DateUpdated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
				";
			$query         = $this->db->query($sql, array($dataIMS[$i]['IMSID'], $dataIMS[$i]['EventDate']));
			$dataCapFarmer = $query->row_array();

			//Capaian Garden
			$sql = "SELECT
					COUNT(gar.`FarmerID`) AS BANYAK
				FROM
					ktv_certification_pre_afl_garden a
					LEFT JOIN ktv_members_garden gar ON 1=1
						AND a.`FarmerID` = gar.`FarmerID`
						AND a.`SurveyNr` = gar.`SurveyNr`
						AND a.`GardenNr` = gar.`GardenNr`
				WHERE
					a.`IMSID` = ?
					AND (
						DATE(gar.`DateUpdated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
						OR
						DATE(gar.`DateCreated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
					)
				;";
			$query         = $this->db->query($sql, array($dataIMS[$i]['IMSID'], $dataIMS[$i]['EventDate'], $dataIMS[$i]['EventDate']));
			$dataCapGarden = $query->row_array();

			//Capaian PostHarvest
			$sql = "SELECT
						COUNT(ph.`FarmerID`) AS BANYAK
					FROM
						(
							SELECT
								gar.`FarmerID`
								, MAX(gar.`SurveyNr`) AS SurveyNr
							FROM
								ktv_certification_pre_afl_garden a
								LEFT JOIN ktv_members_garden gar ON 1=1
									AND a.`FarmerID` = gar.`FarmerID`
									AND a.`SurveyNr` = gar.`SurveyNr`
									AND a.`GardenNr` = gar.`GardenNr`
							WHERE
								a.`IMSID` = ?
								AND gar.`StatusCode` = 'active'
								AND (
									DATE(gar.`DateUpdated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
									OR
									DATE(gar.`DateCreated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
								)
							GROUP BY gar.`FarmerID`
						) AS tbl_garden
						LEFT JOIN ktv_members_post_harvest ph ON 1=1
							AND tbl_garden.FarmerID = ph.`FarmerID`
							AND tbl_garden.SurveyNr = ph.`SurveyNr`
					WHERE
						ph.`StatusCode` = 'active'
						AND (
							DATE(ph.`DateUpdated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
							OR
							DATE(ph.`DateCreated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
						)";
			$query     = $this->db->query($sql, array($dataIMS[$i]['IMSID'], $dataIMS[$i]['EventDate'], $dataIMS[$i]['EventDate'], $dataIMS[$i]['EventDate'], $dataIMS[$i]['EventDate']));
			$dataCapPh = $query->row_array();

			//Capaian PPI
			$sql = "SELECT
					COUNT(tbl_grup.FarmerID) AS BANYAK
				FROM
				(
					SELECT
						afl.`FarmerID`
					FROM
						ktv_certification_pre_afl afl
						INNER JOIN ktv_members a ON afl.`FarmerID` = a.`FarmerID`

						LEFT JOIN ktv_ppiscore2012 ppi ON afl.`FarmerID` = ppi.`FarmerID`
					WHERE
						afl.`IMSID` = ?
						AND afl.`StatusCode` = 'active'
						AND a.`StatusCode` = 'active'
						AND ppi.`StatusCode` = 'active'
						AND (
							DATE(ppi.`DateUpdated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
							OR
							DATE(ppi.`DateCreated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
						)
					GROUP BY afl.`FarmerID`
				) AS tbl_grup";
			$query      = $this->db->query($sql, array($dataIMS[$i]['IMSID'], $dataIMS[$i]['EventDate'], $dataIMS[$i]['EventDate']));
			$dataCapPpi = $query->row_array();

			//Capaian Certification
			$sql = "SELECT
					COUNT(afl.`FarmerID`) AS BANYAK
				FROM
					ktv_certification_pre_afl_garden afl
					LEFT JOIN ktv_ims ims ON afl.`IMSID` = ims.`IMSID`
					LEFT JOIN ktv_certification_holders hold ON ims.CertHolderID = hold.CertHolderID

					LEFT JOIN ktv_certification cert ON 1=1
						AND afl.`FarmerID` = cert.`FarmerID`
						AND afl.`GardenNr` = cert.`GardenNr`
						AND afl.`SurveyNr` = cert.`SurveyNr`
						AND hold.`CertProgID` = cert.`Certification`
				WHERE
					ims.`StatusCode` = 'active'
					AND ims.`IMSID` = ?
					AND (
						DATE(cert.`DateUpdated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
						OR
						DATE(cert.`DateCreated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
					)";
			$query                = $this->db->query($sql, array($dataIMS[$i]['IMSID'], $dataIMS[$i]['EventDate'], $dataIMS[$i]['EventDate']));
			$dataCapCertification = $query->row_array();

			//Capaian Audit Log
			$sql = "SELECT
					COUNT(afl.`FarmerID`) AS BANYAK
				FROM
					ktv_certification_pre_afl_garden afl
					LEFT JOIN ktv_ims ims ON afl.`IMSID` = ims.`IMSID`
					LEFT JOIN ktv_certification_holders hold ON ims.CertHolderID = hold.CertHolderID

					LEFT JOIN (
						SELECT
							au.`FarmerID`
							, au.`SurveyNr`
							, au.`GardenNr`
							, au.Certification
							, au.`ICSDate`
							, au.`DateCreated`
							, au.`DateUpdated`
						FROM
							(SELECT
								FarmerID,
								GardenNr,
								SurveyNr,
								Certification,
								MAX(ICSDate) ICSDate
							FROM
								ktv_certification_audit_log
							WHERE Certification != 0
								AND GardenNr != 0
							GROUP BY FarmerID,
								GardenNr,
								SurveyNr,
								Certification) dt
							INNER JOIN ktv_certification_audit_log au
								ON dt.FarmerID = au.FarmerID
								AND dt.GardenNr = au.GardenNr
								AND dt.SurveyNr = au.SurveyNr
								AND dt.Certification = au.Certification
								AND dt.ICSDate = au.ICSDate
					) AS tbl_au ON 1=1
						AND afl.`FarmerID` = tbl_au.FarmerID
						AND afl.`GardenNr` = tbl_au.GardenNr
						AND afl.`SurveyNr` = tbl_au.SurveyNr
						AND hold.CertProgID = tbl_au.Certification
				WHERE
					ims.`StatusCode` = 'active'
					AND ims.`IMSID` = ?
					AND (
						DATE(tbl_au.`DateUpdated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
						OR
						DATE(tbl_au.`DateCreated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
					)";
			$query           = $this->db->query($sql, array($dataIMS[$i]['IMSID'], $dataIMS[$i]['EventDate'], $dataIMS[$i]['EventDate']));
			$dataCapAuditLog = $query->row_array();

			$arrReturn[$i]['EventName'] = $dataIMS[$i]['EventName'];
			$arrReturn[$i]['EventYear'] = $dataIMS[$i]['EventYear'];

			$arrReturn[$i]['CFarmer']      = $dataCapFarmer['BANYAK'];
			$arrReturn[$i]['CPostHarvest'] = $dataCapPh['BANYAK'];
			$arrReturn[$i]['CPPI']         = $dataCapPpi['BANYAK'];
			$arrReturn[$i]['CGarden']      = $dataCapGarden['BANYAK'];
			$arrReturn[$i]['CCert']        = $dataCapCertification['BANYAK'];
			$arrReturn[$i]['CAuditLog']    = $dataCapAuditLog['BANYAK'];
		}

		$return['data'] = $arrReturn;
		return $return;
	}

	public function getImsEventGridFiles($IMSMasterID, $start, $limit, $sortingField, $sortingDir)
	{
		if ($sortingField == "") {
			$sortingField = 'IMSMasterFileID';
		}

		if ($sortingDir == "") {
			$sortingDir = 'DESC';
		}

		$sql = "
			SELECT
				SQL_CALC_FOUND_ROWS
				a.`IMSMasterFileID`
				, a.`FileName`
				, a.FilePath
				, a.FileDesc
			FROM
				ktv_ims_master_files a
			WHERE
				a.`StatusCode` = 'active'
				AND a.`IMSMasterID` = ?
			ORDER BY $sortingField $sortingDir
			LIMIT ?,?
		";
		$p = array(
			$IMSMasterID,
			(int) $start, (int) $limit,
		);
		$query          = $this->db->query($sql, $p);
		$result['data'] = $query->result_array();

		$query           = $this->db->query('SELECT FOUND_ROWS() AS total');
		$result['total'] = $query->row()->total;

		return $result;
	}

	public function imsEventDetailFillForm($IMSID)
	{
		$sql = "SELECT
				a.`IMSID`,
				a.`IMSID` AS CertEventID,
				a.`IMSMasterID`,
				CONCAT('[',(
					CASE
						WHEN b.`ObjType` = 'farmer_group' THEN '" . lang('Farmer Group') . "'
						WHEN b.`ObjType` = 'cooperative' THEN '" . lang('Cooperatives') . "'
						ELSE 'Org'
					END
				),'] ',b.CertHolderOrgName) AS SupplychainLabel,
				d.`CertProgName` AS CertHolderProgramLabel,
				a.`CertHolderID`,
				b.SupplychainID,
				a.`CertBodyID`,
				a.`CertBodyContactID`,
				a.`InternalStart`,
				a.`InternalEnd`,
				a.`CertificationStart`,
				a.`CertificationEnd`,
				a.`ExternalDate`,
				a.`ExternalStart`,
				a.`ExternalEnd`,
				a.`ValidityStart`,
				a.`ValidityEnd`,
				a.`ExtensionDate`,
				a.`SurveyNr`,
				a.`Year`,
				a.`FirstBuyerID`,
				a.`CertEventName`,
				a.`CertEventDate`,
				a.`CertEventStatus`,
				a.`ICSStatus`,
				a.`CertDistrictID`,
				a.`Location`,
				a.CpgBatchID,
				b.CertProgMemberID,
				b.CertProgMemberDate,
				b.GIPNumber,
				d.CertProgLogoExportJPG,
				IFNULL(summa.DateUpdated,'-') AS DateUpdatedSummary,
				a.StatusImsFinalPeriod,
				a.StatusIcsReinspect,
				a.ExternalAuditStatus
			FROM
				ktv_ims a
				LEFT JOIN ktv_certification_holders b ON a.`CertHolderID` = b.`CertHolderID`
				LEFT JOIN ktv_ref_certification_program d ON b.`CertProgID` = d.`CertProgID`
				LEFT JOIN ktv_ims_summary summa ON a.IMSID = summa.IMSID
			WHERE
				a.`IMSID` = ?
			GROUP BY a.`IMSID`
			LIMIT 1";
		$query = $this->db->query($sql, array($IMSID));

		$return['success'] = true;
		$return['data']    = $query->row_array();
		return $return;
	}

	public function certProgramLabelGetByCertHolderID($CertHolderID)
	{
		$sql = "SELECT
					b.`CertProgOfficialName` AS label
				FROM
					ktv_certification_holders a
					LEFT JOIN ktv_ref_certification_program b ON a.`CertProgID` = b.`CertProgID`
				WHERE
					a.`CertHolderID` = ?
				LIMIT 1";
		$query = $this->db->query($sql, array((int) $CertHolderID));
		$data  = $query->row_array();

		$return['success'] = true;
		$return['label']   = $data['label'];
		return $return;
	}

	public function imsEventDetailFarmerGardenFillForm($FarmerID, $GardenNr, $SurveyNr)
	{
		$sql = "SELECT a.*, b.MemberName FarmerName FROM ktv_survey_plot a LEFT JOIN ktv_members b ON a.MemberID=b.MemberID WHERE a.MemberID=? AND a.SurveyNr=? AND a.PlotNr=?";
		$p   = array(
			$FarmerID,
			$SurveyNr,
			$GardenNr,
		);
		$query    = $this->db->query($sql, $p);
		$dataForm = $query->row_array();

		$return['success'] = true;
		$return['data']    = $dataForm;
		return $return;
	}

	public function imsEventFileUploadInput($varPost, $filenameNya)
	{
		$path_parts = pathinfo($filenameNya);
		$basenamefile = $path_parts['basename'];

		if ($varPost['winImsEventFileUploadForm_callForm'] == "ims_event") {
			$sql = "INSERT INTO `ktv_ims_master_files` SET
				  `IMSMasterID` = ?,
				  `FileName` = ?,
				  `FilePath` = ?,
				  `FileDesc` = ?,
				  StatusCode = 'active',
				  `DateCreated` = NOW(),
				  `CreatedBy` = '{$_SESSION['userid']}',
				  `DateUpdated` = NOW(),
				  `LastModifiedBy` = '{$_SESSION['userid']}' ";
			$p = array(
				$varPost['winImsEventFileUploadForm_IDCaller'],
				$basenamefile,
				$filenameNya,
				$varPost['winImsEventFileUploadForm_Description']
			);
			$query = $this->db->query($sql, $p);

			if ($query) {
				$result['success'] = true;
				$result['message'] = 'File Uploaded';
			} else {
				$result['success'] = false;
				$result['message'] = 'Upload Failed';
			}

			return $result;
		}

		if ($varPost['winImsEventFileUploadForm_callForm'] == "ims_event_detail") {
			$sql = "INSERT INTO `ktv_ims_files` SET
				  `IMSID` = ?,
				  `FileName` = ?,
				  `FilePath` = ?,
				  `FileDesc` = ?,
				  `StatusCode` = 'active',
				  `DateCreated` = NOW(),
				  `CreatedBy` = '{$_SESSION['userid']}',
				  `DateUpdated` = NOW(),
				  `LastModifiedBy` = '{$_SESSION['userid']}'";
			$p = array(
				$varPost['winImsEventFileUploadForm_IDCaller'],
				$basenamefile,
				$filenameNya,
				$varPost['winImsEventFileUploadForm_Description']
			);
			$query = $this->db->query($sql, $p);

			if ($query) {
				$result['success'] = true;
				$result['message'] = 'File Uploaded';
			} else {
				$result['success'] = false;
				$result['message'] = 'Upload Failed';
			}

			return $result;
		}
	}

	public function imsEventFileUploadDelete($imsType, $IDCaller)
	{
		$this->load->library('awsfileupload');

		if ($imsType == "ims_event") {
			//get filenamenya
			$sql         = "SELECT a.FilePath FROM ktv_ims_master_files a WHERE a.`IMSMasterFileID` = '{$IDCaller}' LIMIT 1";
			$query       = $this->db->query($sql);
			$data        = $query->row_array();
			$filenameNya = $data['FilePath'];

			//Delete
			$this->awsfileupload->delete($filenameNya);

			$sql   = "DELETE FROM ktv_ims_master_files WHERE IMSMasterFileID = '{$IDCaller}' LIMIT 1";
			$query = $this->db->query($sql);
			if ($this->db->affected_rows() > 0) {
				$result['success'] = true;
				$result['message'] = 'File Deleted';
			} else {
				$result['success'] = false;
				$result['message'] = 'Delete Failed';
			}
		}

		if ($imsType == "ims_event_detail") {
			//get filenamenya
			$sql         = "SELECT a.FilePath FROM ktv_ims_files a WHERE a.`IMSFileID` = '{$IDCaller}' LIMIT 1";
			$query       = $this->db->query($sql);
			$data        = $query->row_array();
			$filenameNya = $data['FilePath'];

			//Delete
			$this->awsfileupload->delete($filenameNya);

			$sql   = "DELETE FROM ktv_ims_files WHERE IMSFileID = '{$IDCaller}' LIMIT 1";
			$query = $this->db->query($sql);
			if ($this->db->affected_rows() > 0) {
				$result['success'] = true;
				$result['message'] = 'File Deleted';
			} else {
				$result['success'] = false;
				$result['message'] = 'Delete Failed';
			}
		}

		return $result;
	}

	public function imsEventMasterInsert($varPost)
	{
		$this->db->trans_begin();

		$sql = "INSERT INTO `ktv_ims_master` SET
			  `CertHolderID` = ?,
			  `DateEstablished` = ?,
			  `Description` = ?,
			  `StatusCode` = 'active',
			  `DateCreated` = NOW(),
			  `CreatedBy` = '{$_SESSION['userid']}'";
		$p = array(
			$varPost['imsCertFormImsEvent_cmbCertHolder'],
			$varPost['imsCertFormImsEvent_DateEstablished'],
			$varPost['imsCertFormImsEvent_Description'],
		);
		$query = $this->db->query($sql, $p);

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			$results['success'] = false;
			$results['message'] = "Failed to save record";
		} else {
			$this->db->trans_commit();
			$results['success'] = true;
			$results['message'] = "Record saved";
		}

		return $results;
	}

	public function imsEventMasterUpdate($varPost)
	{
		$this->db->trans_begin();

		$sql = "UPDATE `ktv_ims_master` SET
				  `CertHolderID` = ?,
				  `DateEstablished` = ?,
				  `Description` = ?,
				  `DateUpdated` = NOW(),
				  `LastModifiedBy` = ?
				WHERE
					`IMSMasterID` = ?
				LIMIT 1";
		$p = array(
			$varPost['imsCertFormImsEvent_cmbCertHolder'],
			$varPost['imsCertFormImsEvent_DateEstablished'],
			$varPost['imsCertFormImsEvent_Description'],
			$_SESSION['userid'],
			$varPost['imsCertFormImsEvent_IMSMasterID'],
		);
		$query = $this->db->query($sql, $p);

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			$results['success'] = false;
			$results['message'] = "Failed to save record";
		} else {
			$this->db->trans_commit();
			$results['success'] = true;
			$results['message'] = "Record saved";
		}

		return $results;
	}

	public function imsEventMasterDelete($IMSMasterID)
	{
		$this->db->trans_begin();

		$sql = "UPDATE `ktv_ims_master` SET
				  StatusCode = 'nullified'
				WHERE
					`IMSMasterID` = ?
				LIMIT 1";
		$p = array(
			$IMSMasterID,
		);
		$query = $this->db->query($sql, $p);

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			$results['success'] = false;
			$results['message'] = "Failed to delete record";
		} else {
			$this->db->trans_commit();
			$results['success'] = true;
			$results['message'] = "Record deleted";
		}

		return $results;
	}

	public function imsEventDetailInsert($varPost)
	{
		$this->db->trans_begin();

		$sql = "INSERT INTO `ktv_ims` SET
			  `IMSMasterID` = ?,
			  `CertHolderID` = ?,
			  `CertBodyID` = ?,
			  `CertBodyContactID` = ?,
			  `InternalStart` = ?,
			  `InternalEnd` = ?,
			  `CertificationStart` = ?,
			  `CertificationEnd` = ?,
			  `ExternalDate` = ?,
			  `ExternalStart` = ?,
			  `ExternalEnd` = ?,
			  `ValidityStart` = ?,
			  `ValidityEnd` = ?,
			  `ExtensionDate` = ?,
			  `SurveyNr` = ?,
			  `Year` = ?,
			  `FirstBuyerID` = ?,
			  `CertEventName` = ?,
			  `CertEventDate` = ?,
			  CertEventStatus = ?,
			  ICSStatus = ?,
			  `Location` = ?,
			  CpgBatchID = ?,
			  ExternalAuditStatus = ?,
			  `StatusCode` = 'active',
			  `DateCreated` = NOW(),
			  `CreatedBy` = ?";
		$p = array(
			$varPost['IMSMasterID'],
			$varPost['CertHolderID'],
			$varPost['CertBodyID'],
			$varPost['CertBodyContactID'],
			$varPost['InternalStart'],
			$varPost['InternalEnd'],
			$varPost['CertificationStart'],
			$varPost['CertificationEnd'],
			$varPost['ExternalDate'],
			$varPost['ExternalStart'],
			$varPost['ExternalEnd'],
			$varPost['ValidityStart'],
			$varPost['ValidityEnd'],
			$varPost['ExtensionDate'],
			$varPost['SurveyNr'],
			$varPost['Year'],
			$varPost['FirstBuyerID'],
			$varPost['CertEventName'],
			$varPost['CertEventDate'],
			'1',
			$varPost['ICSStatus'],
			$varPost['Location'],
			$varPost['CpgBatchID'],
			$varPost['ExternalAuditStatus'],
			$_SESSION['userid'],
		);
		$query = $this->db->query($sql, $p);

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			$results['success'] = false;
			$results['message'] = "Failed to save record";
		} else {
			$this->db->trans_commit();
			$results['success'] = true;
			$results['message'] = "Record saved";
		}

		return $results;
	}

	public function imsEventDetailUpdate($varPost)
	{
		$this->db->trans_begin();

		//Check ICS Status
		$DataIMS = $this->getIMSDetail($varPost['IMSID']);
		if($varPost['ICSStatus'] == "") $varPost['ICSStatus'] = $DataIMS['ICSStatus'];
		if($varPost['ExternalAuditStatus'] == "") $varPost['ExternalAuditStatus'] = $DataIMS['ExternalAuditStatus'];

		$sql = "UPDATE `ktv_ims` SET
			  `IMSMasterID` = ?,
			  `CertHolderID` = ?,
			  `CertBodyID` = ?,
			  `CertBodyContactID` = ?,
			  `InternalStart` = ?,
			  `InternalEnd` = ?,
			  `CertificationStart` = ?,
			  `CertificationEnd` = ?,
			  `ExternalDate` = ?,
			  `ExternalStart` = ?,
			  `ExternalEnd` = ?,
			  `ValidityStart` = ?,
			  `ValidityEnd` = ?,
			  `ExtensionDate` = ?,
			  `SurveyNr` = ?,
			  `Year` = ?,
			  `FirstBuyerID` = ?,
			  `CertEventName` = ?,
			  `CertEventDate` = ?,
			  `CertEventStatus` = ?,
			  `ICSStatus` = ?,
			  `Location` = ?,
			  CpgBatchID = ?,
			  ExternalAuditStatus = ?,
			  `DateUpdated` = NOW(),
			  `LastModifiedBy` = ?
			WHERE
				IMSID = ?
			LIMIT 1";
		$p = array(
			$varPost['IMSMasterID'],
			$varPost['CertHolderID'],
			$varPost['CertBodyID'],
			$varPost['CertBodyContactID'],
			$varPost['InternalStart'],
			$varPost['InternalEnd'],
			$varPost['CertificationStart'],
			$varPost['CertificationEnd'],
			$varPost['ExternalDate'],
			$varPost['ExternalStart'],
			$varPost['ExternalEnd'],
			$varPost['ValidityStart'],
			$varPost['ValidityEnd'],
			$varPost['ExtensionDate'],
			$varPost['SurveyNr'],
			$varPost['Year'],
			$varPost['FirstBuyerID'],
			$varPost['CertEventName'],
			$varPost['CertEventDate'],
			$varPost['CertEventStatus'],
			$varPost['ICSStatus'],
			$varPost['Location'],
			$varPost['CpgBatchID'],
			$varPost['ExternalAuditStatus'],
			$_SESSION['userid'],
			$varPost['IMSID'],
		);
		$query = $this->db->query($sql, $p);

		//Cek apakah External Audit Completed (Begin)
		if ($varPost['ExternalAuditStatus'] == "2") {
			//Update Farmer
			$sql = "UPDATE `ktv_certification_afl_farmer` a SET
					a.ExternalAuditStatus = '2' #Pass
					, a.ExternalAuditRemark = 'Event IMS, External Audit Completed'
				WHERE
					a.`IMSID` = ?
					AND a.`ExternalAuditStatus` = '1'
					AND a.StatusCode = 'active'
					AND a.`CertStatusAudit` = 'Comply'";
			$p = array(
				$varPost['IMSID']
			);
			$query = $this->db->query($sql,$p);

			//Update Garden
			$sql = "UPDATE `ktv_certification_afl_garden` a SET
					a.`ExternalAuditStatus` = '2' #Pass
					, a.ExternalAuditRemark = 'Event IMS, External Audit Completed'
				WHERE
					a.`IMSID` = ?
					AND a.`ExternalAuditStatus` = '1'
					AND a.`StatusCode` = 'active'";
			$p = array(
				$varPost['IMSID']
			);
			$query = $this->db->query($sql,$p);
		}
		//Cek apakah External Audit Completed (End)

		//Cek apakah Status Completed
		if ($varPost['CertEventStatus'] == "2") {
			//get data IMS
			$sql = "SELECT
					`CertificationStart`,
					`CertificationEnd`,
					`ExternalDate`,
					`ExternalStart`,
					`ExternalEnd`,
					`ValidityStart`,
					`ValidityEnd`,
					`ExtensionDate`,
					c.`FarmertypeID`,
					a.SettingCflSalesQuota
				FROM
					`ktv_ims` a
					INNER JOIN ktv_certification_holders b ON a.`CertHolderID` = b.`CertHolderID`
					INNER JOIN ktv_ref_certification_program c ON b.`CertProgID` = c.`CertProgID`
				WHERE
					a.`IMSID` = ?
				LIMIT 1";
			$query   = $this->db->query($sql, array($varPost['IMSID']));
			$dataIMS = $query->row_array();

			//Setting Sales Quota
			if($dataIMS['SettingCflSalesQuota'] == ""){
				$SettingCflSalesQuota = 1;
			}else{
				$SettingCflSalesQuota = (float) $dataIMS['SettingCflSalesQuota'];
			}

			//1. Proses Insert ke CFL ======================================== (Begin)

			//Cek terlebih dahulu sudah ada recordnya belum
			$sql = "SELECT
						COUNT(a.`IMSID`) AS CekNya
					FROM
						ktv_certification_certified_farmer a
					WHERE
						a.`IMSID` = ?";
			$DataCek = $this->db->query($sql, array($varPost['IMSID']))->row_array();
			if ($DataCek['CekNya'] > 0) {
				$this->db->trans_rollback();
				$results['success'] = false;
				$results['message'] = lang("Farmer Certified Data for this IMS Already Exist");

				return $results;
			}

			//Insert CFL Farmer
			$sql = "INSERT INTO `ktv_certification_certified_farmer` (
						`IMSID`,
						`FarmerID`,
						`FarmerName`,
						`CPGid`,
						`GroupName`,
						`Gender`,
						`HandPhone`,
						`Province`,
						`District`,
						`SubDistrict`,
						`Village`,
						`PolygonStatus`,
						`CertYear`,
						`CertFirstYear`,
						`YearOfCertification`,
						`PermanentWorkers`,
						`CertICSDate`,
						`CertStatusAudit`,
						`CertSurveyNr`,
						`CertHarvest`,
						`CertNextHarvest`,
						`SalesQuota`,
						`CertHectare`,
						`CertFarmNr`,
						`CertFarmTotalNr`,
						`CertTotalHectare`,
						`CertPohonTM`,
						`CertPohonTBM`,
						`CertPohonTR`,
						`CertCertificationStart`,
						`CertCertificationEnd`,
						`CertValidityStart`,
						`CertValidityEnd`,
						`CertExtensionStart`,
						`CertExtensionEnd`,
						`CertIssueDate`,
						`CertDateCollection`,
						TotalCocoaFarm,
						`SalesLastYear`,
						`SalesLast2Years`,
						`SalesLast3Years`,
						`IMSCreator`,
						`IMSEditor`,
						`StatusAdditional`,
						`CertAuditRemark`,
						`StatusCode`,
						`DateCreated`,
						`CreatedBy`,
						`DateUpdated`,
						`LastModifiedBy`,

						CertFarmNrLastYear,
						TotalAuditedFarm,
						TotalAuditedFarmLastYear
					)
					SELECT
						`IMSID`,
						`FarmerID`,
						`FarmerName`,
						`CPGid`,
						`GroupName`,
						`Gender`,
						`HandPhone`,
						`Province`,
						`District`,
						`SubDistrict`,
						`Village`,
						`PolygonStatus`,
						`CertYear`,
						`CertFirstYear`,
						`YearOfCertification`,
						`PermanentWorkers`,
						`CertICSDate`,
						`CertStatusAudit`,
						`CertSurveyNr`,
						`CertHarvest`,
						`CertNextHarvest`,
						(CertNextHarvest * {$SettingCflSalesQuota}) AS SalesQuota, #SalesQuota
						`CertHectare`,
						`CertFarmNr`,
						`CertFarmTotalNr`,
						`CertTotalHectare`,
						`CertPohonTM`,
						`CertPohonTBM`,
						`CertPohonTR`,
						'{$dataIMS['CertificationStart']}', #CertCertificationStart
						'{$dataIMS['CertificationEnd']}', #CertCertificationEnd
						'{$dataIMS['ValidityStart']}', #CertValidityStart
						'{$dataIMS['ValidityEnd']}', #CertValidityEnd
						'{$dataIMS['ExtensionDate']}', #CertExtensionStart
						NULL, #CertExtensionEnd
						'{$dataIMS['ExternalDate']}', #CertIssueDate
						`CertDateCollection`,
						TotalCocoaFarm,
						`SalesLastYear`,
						`SalesLast2Years`,
						`SalesLast3Years`,
						`IMSCreator`,
						`IMSEditor`,
						`StatusAdditional`,
						`CertAuditRemark`,
						'active',
						NOW(),
						'1',
						NOW(),
						'1',

						CertFarmNrLastYear,
						TotalAuditedFarm,
						TotalAuditedFarmLastYear
					FROM
						`ktv_certification_afl_farmer` a
					WHERE
						a.`IMSID` = ?
						-- AND a.ExternalAuditStatus = '2' #PASS
						AND a.StatusCode = 'active'
						-- AND a.`CertStatusAudit` = 'Comply'
					";
			$query = $this->db->query($sql, array($varPost['IMSID']));
			$farmer_affected_rows = $this->db->affected_rows();

			//Insert CFL Garden
			$sql = "INSERT INTO `ktv_certification_certified_garden` (
					`IMSID`,
					`FarmerID`,
					`FarmerName`,
					`CPGid`,
					`GroupName`,
					`Gender`,
					`HandPhone`,
					`Province`,
					`District`,
					`SubDistrict`,
					`Village`,
					`PolygonStatus`,
					`CertLatitude`,
					`CertLongitude`,
					`CertYear`,
					`CertFirstYear`,
					`PermanentWorkers`,
					`CertICSDate`,
					`CertStatusAudit`,
					`CertSurveyNr`,
					`CertHarvest`,
					`CertNextHarvest`,
					`CertPercentageIncline`,
					`CertHectare`,
					`CertGardenNr`,
					`CertPohonTM`,
					`CertPohonTBM`,
					`CertPohonTR`,
					`CertPohonTMHectare`,
					`CertTotalPohonHectare`,
					`CertStart`,
					`CertEnd`,
					`CertDateCollection`,
					`CertCandidateSelection`,
					`CertCommentAudit`,
					`CertDateRevisionAudit`,
					`CertRecommendationAudit`,
					`1YearAgoSurveyNr`,
					`1YearAgoHarvest`,
					`1YearAgoHectare`,
					`1YearAgoGardenNr`,
					`1YearAgoPohonTM`,
					`1YearAgoPohonTBM`,
					`1YearAgoPohonTR`,
					`1YearAgoStart`,
					`1YearAgoEnd`,
					`1YearAgoDateCollection`,
					`2YearAgoSurveyNr`,
					`2YearAgoHarvest`,
					`2YearAgoHectare`,
					`2YearAgoGardenNr`,
					`2YearAgoPohonTM`,
					`2YearAgoPohonTBM`,
					`2YearAgoPohonTR`,
					`2YearAgoStart`,
					`2YearAgoEnd`,
					`2YearAgoDateCollection`,
					`BaselineSurveyNr`,
					`BaselineHarvest`,
					`BaselineHectare`,
					`BaselineGardenNr`,
					`BaselinePohonTM`,
					`BaselinePohonTBM`,
					`BaselinePohonTR`,
					`BaselineStart`,
					`BaselineEnd`,
					`BaselineDateCollection`,
					`IMSCreatorBy`,
					`IMSCreator`,
					`IMSEditorBy`,
					`IMSEditor`,
					`ResponsibleBy`,
					`ResponsibleName`,
					`CertAuditNotComplyReason`,
					`CertAuditRemark`,
					`StatusCode`,
					`DateCreated`,
					`CreatedBy`
				)
				SELECT
					`IMSID`,
					`FarmerID`,
					`FarmerName`,
					`CPGid`,
					`GroupName`,
					`Gender`,
					`HandPhone`,
					`Province`,
					`District`,
					`SubDistrict`,
					`Village`,
					`PolygonStatus`,
					`CertLatitude`,
					`CertLongitude`,
					`CertYear`,
					`CertFirstYear`,
					`PermanentWorkers`,
					`CertICSDate`,
					`CertStatusAudit`,
					`CertSurveyNr`,
					`CertHarvest`,
					`CertNextHarvest`,
					`CertPercentageIncline`,
					`CertHectare`,
					`CertGardenNr`,
					`CertPohonTM`,
					`CertPohonTBM`,
					`CertPohonTR`,
					`CertPohonTMHectare`,
					`CertTotalPohonHectare`,
					`CertStart`,
					`CertEnd`,
					`CertDateCollection`,
					`CertCandidateSelection`,
					`CertCommentAudit`,
					`CertDateRevisionAudit`,
					`CertRecommendationAudit`,
					`1YearAgoSurveyNr`,
					`1YearAgoHarvest`,
					`1YearAgoHectare`,
					`1YearAgoGardenNr`,
					`1YearAgoPohonTM`,
					`1YearAgoPohonTBM`,
					`1YearAgoPohonTR`,
					`1YearAgoStart`,
					`1YearAgoEnd`,
					`1YearAgoDateCollection`,
					`2YearAgoSurveyNr`,
					`2YearAgoHarvest`,
					`2YearAgoHectare`,
					`2YearAgoGardenNr`,
					`2YearAgoPohonTM`,
					`2YearAgoPohonTBM`,
					`2YearAgoPohonTR`,
					`2YearAgoStart`,
					`2YearAgoEnd`,
					`2YearAgoDateCollection`,
					`BaselineSurveyNr`,
					`BaselineHarvest`,
					`BaselineHectare`,
					`BaselineGardenNr`,
					`BaselinePohonTM`,
					`BaselinePohonTBM`,
					`BaselinePohonTR`,
					`BaselineStart`,
					`BaselineEnd`,
					`BaselineDateCollection`,
					`IMSCreatorBy`,
					`IMSCreator`,
					`IMSEditorBy`,
					`IMSEditor`,
					`ResponsibleBy`,
					`ResponsibleName`,
					`CertAuditNotComplyReason`,
					`CertAuditRemark`,
					'active',
					NOW(),
					'{$_SESSION['userid']}'
				FROM
					`ktv_certification_afl_garden`
				WHERE
					IMSID = '{$varPost['IMSID']}'
					AND ExternalAuditStatus = '2' #PASS
				";
			$query = $this->db->query($sql);
			$garden_affected_rows = $this->db->affected_rows();

			//cek apakah ada data terinsert
			if ($farmer_affected_rows == 0 || $garden_affected_rows == 0) {
				$this->db->trans_rollback();
				$results['success'] = false;
				$results['message'] = lang("No Farmer/Garden Certified Found");

				return $results;
			}
			//1. Proses Insert ke CFL ======================================== (End)

			//2. Proses Insert/Update ke FarmerType ======================================== (Begin)
			if ($dataIMS['FarmertypeID'] == "") {
				$this->db->trans_rollback();
				$results['success'] = false;
				$results['message'] = lang("Farmer type not set for this Certification Program");

				return $results;
			} else {
				//get list FarmerID nya
				$sql = "SELECT
							a.`FarmerID`
						FROM
							`ktv_certification_afl_farmer` a
						WHERE
							a.`IMSID` = ?
							AND a.StatusCode = 'active'
							AND a.`CertStatusAudit` = 'Comply'";
				$query            = $this->db->query($sql, array($varPost['IMSID']));
				$dataListFarmerID = $query->result_array();

				for ($i = 0; $i < count($dataListFarmerID); $i++) {
					//get Informasi Training
					$sql = "SELECT
									MIN(tbl_training.BatchNumberPertama) AS BatchNumberPertama
									, tbl_training.FirstTraining
									, tbl_training.FirstTrainingID
								FROM
								(
									SELECT
										unia_a.`FarmerID`
										, MIN(unia_c.`BatchNumber`) AS BatchNumberPertama
										, 'CPG' AS FirstTraining
										, unia_b.`CpgBatchTrainingID` AS FirstTrainingID
									FROM
										ktv_cpg_batch_trainings_farmers unia_a
										LEFT JOIN ktv_cpg_batch_trainings unia_b ON unia_a.`CpgBatchTrainingID` = unia_b.`CpgBatchTrainingID`
										LEFT JOIN ktv_cpg_batch unia_c ON unia_b.`CpgBatchID` = unia_c.`CpgBatchID`
									WHERE
										unia_b.`StatusCode` = 'active'
										AND unia_a.`FarmerID` = ?
									GROUP BY unia_a.`FarmerID`

									UNION

									SELECT
										unib_a.`FarmerID`
										, MIN(unib_c.`BatchNumber`) AS BatchNumberPertama
										, 'Kader' AS FirstTraining
										, unib_b.CpgKaderTrainingID AS FirstTrainingID
									FROM
										ktv_kader_trainings_participants unib_a
										LEFT JOIN ktv_kader_trainings unib_b ON unib_a.`CpgKaderTrainingID` = unib_b.`CpgKaderTrainingID`
										LEFT JOIN ktv_cpg_batch unib_c ON unib_b.`CpgBatchID` = unib_c.`CpgBatchID`
									WHERE
										unib_b.`StatusCode` = 'active'
										AND unib_a.`FarmerID` = ?
									GROUP BY unib_a.`FarmerID`
								) AS tbl_training
								GROUP BY tbl_training.FarmerID
								ORDER BY tbl_training.FarmerID
								LIMIT 1";
					$query            = $this->db->query($sql, array($dataListFarmerID[$i]['FarmerID'], $dataListFarmerID[$i]['FarmerID']));
					$dataInfoTraining = $query->row_array();

					//cek sudah ada recordnya belum di tabel "ktv_members_type"
					//tidak bisa pakai on duplicate key karena tidak tau nilai PartnerID
					$sql                 = "SELECT FarmerID FROM `ktv_member_farmer_type` WHERE `FarmerID` = ? LIMIT 1";
					$query               = $this->db->query($sql, array($dataListFarmerID[$i]['FarmerID']));
					$dataExistFarmerType = $query->row_array();

					if ($dataExistFarmerType['FarmerID'] != "") {
						//Update
						$sql = "UPDATE `ktv_member_farmer_type` SET
									FarmertypeID = ?,
									FirstBatchNr = ?,
									FirstTraining = ?,
									FirstTrainingID = ?,
									isCertified = '1',
									Remark = 'Update from IMS Event ID : {$varPost['IMSID']}',
									StatusCode = 'active',
									DateUpdated = NOW(),
									LastModifiedBy = '1'
								WHERE
									FarmerID = ?
								LIMIT 1";
						$p = array(
							$dataIMS['FarmertypeID'],
							$dataInfoTraining['BatchNumberPertama'],
							$dataInfoTraining['FirstTraining'],
							$dataInfoTraining['FirstTrainingID'],
							$dataListFarmerID[$i]['FarmerID'],
						);
						$query = $this->db->query($sql, $p);
					} else {
						//Insert
						$sql = "INSERT INTO ktv_member_farmer_type SET
									FarmerID = ?,
									PartnerID = NULL,
									FarmertypeID = ?,
									FirstBatchNr = ?,
									FirstTraining = ?,
									FirstTrainingID = ?,
									isCertified = '1',
									Remark = 'Update from IMS Event ID : {$varPost['IMSID']}',
									StatusCode = 'active',
									DateCreated = NOW(),
									CreatedBy = '1'";
						$p = array(
							$dataListFarmerID[$i]['FarmerID'],
							$dataIMS['FarmertypeID'],
							$dataInfoTraining['BatchNumberPertama'],
							$dataInfoTraining['FirstTraining'],
							$dataInfoTraining['FirstTrainingID'],
						);
						$query = $this->db->query($sql, $p);
					}
				}
			}
			//2. Proses Insert/Update ke FarmerType ======================================== (End)
		}

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			$results['success'] = false;
			$results['message'] = "Failed to save record";
		} else {
			$this->db->trans_commit();
			$results['success'] = true;
			$results['message'] = "Record saved";

			//Kirim Email Notifikasi
			// if ($varPost['CertEventStatus'] == "2") {
			// 	require_once 'application/third_party/phpmailer-hr/class.phpmailer.php';
			// 	$this->config->load('email');

			// 	//Ambil list penerima
			// 	$sql = "SELECT
			// 				a.SetValue
			// 			FROM
			// 				sys_setting a
			// 			WHERE
			// 				a.SetKey = 'list_email_receiver_ims_completed'
			// 			LIMIT 1";
			// 	$Data = $this->db->query($sql)->row_array();
			// 	$ArrEmailPenerima = explode(",",$Data['SetValue']);

			// 	$ObjMail = new PHPMailer();
			// 	$ObjMail->IsSMTP();
			// 	$ObjMail->SMTPSecure = 'tls';
			// 	$ObjMail->SMTPAuth = true;
			// 	$ObjMail->Host = $this->config->item('email_Host');
			// 	$ObjMail->Port = $this->config->item('email_Port');
			// 	$ObjMail->Username = $this->config->item('email_Username');
			// 	$ObjMail->Password = $this->config->item('email_Password');
			// 	$ObjMail->Priority = 0;
			// 	$ObjMail->SetFrom($this->config->item('email_from'), 'Koltiva Cocoatrace Support');

			// 	$tpl = file_get_contents('files/email/default_notif.html');
			// 	$root_created = "Team IMS Certification";
			// 	$opening = "Berikut Detail IMS yang telah diupdate untuk Event Statusnya menjadi Completed : ";
			// 	$body = '<div class="well"><table width="100%"><tr><th>IMSID</th><td>'.$varPost['IMSID'].'</td></tr><tr><th>Event Name</th><td>'.$varPost['CertEventName'].'</td></tr><tr><th>Location</th><td>'.$varPost['Location'].'</td></tr><tr><th>Cert Date</th><td>'.tanggal_dwibahasa($varPost['CertificationStart']).' s/d '.tanggal_dwibahasa($varPost['CertificationEnd']).'</td></tr><tr><th>Valid Date</th><td>'.tanggal_dwibahasa($varPost['ValidityStart']).' s/d '.tanggal_dwibahasa($varPost['ValidityEnd']).'</td></tr></table></div>';

			// 	$tpl = str_replace('{{root_created}}', $root_created, $tpl);
			// 	$tpl = str_replace('{{opening}}', $opening, $tpl);
			// 	$tpl = str_replace('{{body}}', $body, $tpl);

			// 	$ObjMail->Subject = 'IMS Event Complete Notifications';
			// 	$ObjMail->Body = $tpl;

			// 	$ObjMail->IsHTML(true);
			// 	$ObjMail->AddAddress('nikolius.lau@koltiva.com');
			// 	for ($i=0; $i < count($ArrEmailPenerima); $i++) {
			// 		$ObjMail->AddAddress($ArrEmailPenerima[$i]);
			// 	}

			// 	$result = $ObjMail->Send();
			// 	$ObjMail->ClearAddresses();
			// 	$ObjMail->ClearAllRecipients();
			// 	$ObjMail->IsHTML(false);
			// }
		}

		return $results;
	}

	public function imsEventDetailDelete($IMSID)
	{
		$this->db->trans_begin();

		$sql = "UPDATE `ktv_ims` SET
			  StatusCode = 'nullified',
			  `DateUpdated` = NOW(),
			  `LastModifiedBy` = ?
			WHERE
				IMSID = ?
			LIMIT 1";
		$p = array(
			$_SESSION['userid'],
			$IMSID,
		);
		$query = $this->db->query($sql, $p);

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			$results['success'] = false;
			$results['message'] = "Failed to delete record";
		} else {
			$this->db->trans_commit();
			$results['success'] = true;
			$results['message'] = "Record deleted";
		}

		return $results;
	}

	public function cflNotCertListExportExcel($IMSID){
		$sql = "SELECT
					a.IMSID,
					a.FarmerID AFLFarmerID,
					a.FarmerID,
					far.ExtFarmerID,
					a.FarmerName,
					a.Gender,
					far.Birthdate,
					FLOOR(DATEDIFF(CURDATE(), far.Birthdate) / 365.25) AS Age,
					CONCAT('[', b.`CPGid`, '] ', b.`GroupName`) AS FarmerGroup,
					'' AS ClusterName,
					a.`Village`,
					a.CertStatusAudit AFLStatus,
					a.CertFirstYear,
					a.CertHarvest,
					a.CertNextHarvest,
					a.CertHectare,
					a.CertFarmNr,
					a.CertICSDate AS ICSDate,
					a.CertTotalHectare AS TotalHa,
					a.CertAuditRemark,
					a.CertPohonTM,
					a.CertPohonTBM,
					a.CertPohonTR,
					a.SalesLastYear,
					a.SalesLast2Years,
					a.SalesLast3Years
				FROM
					ktv_certification_not_certified_farmer a
					LEFT JOIN ktv_cpg b
						ON a.`CPGid` = b.`CPGid`
					LEFT JOIN ktv_members far ON 1=1
						AND a.FarmerID = far.FarmerID
				WHERE
					a.IMSID = ?
				ORDER BY a.FarmerID ASC";
		$query = $this->db->query($sql, array($IMSID));
		return $query->result_array();
	}

	public function cflNotCertListGardenExportExcel($IMSID){
		$sql = "SELECT
				a.IMSID,
				a.FarmerID AFLFarmerID,
				a.FarmerID,
				far.ExtFarmerID,
				a.FarmerName,
				a.Gender,                
				far.Birthdate,
				FLOOR(DATEDIFF(CURDATE(), far.Birthdate) / 365.25) AS Age,
				CONCAT('[', b.`CPGid`, '] ', b.`GroupName`) AS FarmerGroup,
				a.`Village`,
				CASE
					WHEN a.CertStatusAudit='1' THEN 'Comply'
					WHEN a.CertStatusAudit='2' THEN 'Not Comply'
					WHEN a.CertStatusAudit='3' THEN 'Comply with Recommendation'
				END AS AFLStatus,
				a.CertGardenNr,
				a.`CertSurveyNr`,
				a.PolygonStatus,
				a.CertLatitude,
				a.CertLongitude,
				a.CertFirstYear,
				a.CertICSDate AS ICSDate,
				a.CertNextHarvest,
				a.CertHarvest,
				a.CertHectare,
				a.CertPohonTM,
				a.CertPohonTBM,
				a.CertPohonTR,
				a.CertCommentAudit AS AuditComment,
				a.CertRecommendationAudit AS AuditRecommendationComment
			FROM
				`ktv_certification_not_certified_garden` certf
				INNER JOIN ktv_certification_afl_garden a ON certf.FarmerID = a.FarmerID
				LEFT JOIN ktv_cpg b
					ON a.`CPGid` = b.`CPGid`
				LEFT JOIN ktv_members far ON 1=1
					AND a.FarmerID = far.FarmerID

				LEFT JOIN ktv_certification_pre_afl pafl ON 1=1
					AND a.IMSID = pafl.IMSID
					AND a.FarmerID = pafl.FarmerID
				LEFT JOIN ktv_ims_cluster cl ON pafl.ClusterID = cl.ClusterID
			WHERE
				a.IMSID = ?
			ORDER BY a.FarmerID ASC";
		$query = $this->db->query($sql, array($IMSID));
		return $query->result_array();
	}

	public function cflListExportExcel($IMSID)
	{
		$sql = "SELECT
					a.IMSID,
					a.FarmerID AFLFarmerID,
					a.FarmerID,
					far.ExtFarmerID,
					a.FarmerName,
					a.Gender,
					far.Birthdate,
					FLOOR(DATEDIFF(CURDATE(), far.Birthdate) / 365.25) AS Age,
					CONCAT('[', b.`CPGid`, '] ', b.`GroupName`) AS FarmerGroup,
					IFNULL(cl.ClusterName,'-') AS ClusterName,
					a.`Village`,
					a.CertStatusAudit AFLStatus,
					a.CertFirstYear,
					a.CertHarvest,
					a.CertNextHarvest,
					a.CertHectare,
					a.CertFarmNr,
					a.CertICSDate AS ICSDate,
					a.CertTotalHectare AS TotalHa,
					a.CertAuditRemark,
					a.CertPohonTM,
					a.CertPohonTBM,
					a.CertPohonTR,
					a.SalesLastYear,
					a.SalesLast2Years,
					a.SalesLast3Years
				FROM
					ktv_certification_certified_farmer a
					LEFT JOIN ktv_cpg b
						ON a.`CPGid` = b.`CPGid`
					LEFT JOIN ktv_members far ON 1=1
						AND a.FarmerID = far.FarmerID

					LEFT JOIN ktv_certification_pre_afl pafl ON 1=1
						AND a.IMSID = pafl.IMSID
						AND a.FarmerID = pafl.FarmerID
					LEFT JOIN ktv_ims_cluster cl ON pafl.ClusterID = cl.ClusterID
				WHERE
					a.IMSID = ?
				ORDER BY a.FarmerID ASC";
		$query = $this->db->query($sql, array($IMSID));
		return $query->result_array();
	}

	public function aflListExportExcel($IMSID, $StatusNya)
	{
		if ($StatusNya == 'Comply') {
			$sqlWhere = "AND a.CertStatusAudit = 'Comply'";
		} elseif ($StatusNya == 'NotComply') {
			$sqlWhere = "AND a.CertStatusAudit = 'Not Comply'";
		} elseif ($StatusNya == 'NoStatus') {
			$sqlWhere = "AND a.CertStatusAudit = '-'";
		}

		$sql = "SELECT
					a.IMSID,
					ch.`CertProgID`,
					a.FarmerID AFLFarmerID,
					a.FarmerID,
					far.ExtFarmerID,
					a.FarmerName,
					a.Gender,
					far.Birthdate,
					FLOOR(DATEDIFF(CURDATE(), far.Birthdate) / 365.25) AS Age,
					CONCAT('[', b.`CPGid`, '] ', b.`GroupName`) AS FarmerGroup,
					IFNULL(cl.ClusterName,'-') AS ClusterName,
					prov.`Province`,
					dis.`District`,
					subd.`SubDistrict`,
					a.`Village`,
					IFNULL(ba.`BankName`,'-') AS BankName,
					IFNULL(far.`AccountNumber`,'-') AS BankAccNumber,
					a.CertStatusAudit AFLStatus,
					CASE
						WHEN a.ExternalAuditStatus='1' THEN 'No Status'
						WHEN a.ExternalAuditStatus='2' THEN 'Pass'
						WHEN a.ExternalAuditStatus='3' THEN 'Not Pass'
					END AS ExternalAuditStatus,
					CASE
						WHEN a.CertStatusVerified='1' THEN 'Verified by CL'
						WHEN a.CertStatusVerified='2' THEN 'Verified by IMS Manager'
						ELSE '-'
					END AS CertStatusVerified,
					a.CertFirstYear,
					a.CertHarvest,
					a.CertNextHarvest,
					a.CertHectare,
					a.CertFarmNr,
					a.CertICSDate AS ICSDate,
					a.CertTotalHectare AS TotalHa,
					a.CertPohonTM,
					a.CertPohonTBM,
					a.CertPohonTR,
					a.SalesLastYear,
					a.SalesLast2Years,
					a.SalesLast3Years,
					a.`IMSCreator`,

					CASE
			WHEN far.StatusFarmer='1' THEN '".lang('Active')."'
						WHEN far.StatusFarmer='2' THEN '".lang('Not Active')."'
					END AS StatusFarmer,
					CASE
						WHEN far.ReasonStatusFarmer='1' THEN '".lang('Died')."'
						WHEN far.ReasonStatusFarmer='2' THEN '".lang('Moved/left the area')."'
						WHEN far.ReasonStatusFarmer='3' THEN '".lang('Stop Farming')."'
					END AS ReasonStatusFarmer,
					CASE
						WHEN pafl.`StatusComply`='1' THEN '".lang('Yes')."'
						WHEN pafl.`StatusComply`='2' THEN '".lang('No')."'
					END AS EligibleForAudit,
					pafl.`AuditRemark`,
					a.`CertAuditRemark`
				FROM
					ktv_certification_afl_farmer a
					LEFT JOIN ktv_cpg b
						ON a.`CPGid` = b.`CPGid`
					LEFT JOIN ktv_members far ON 1=1
						AND a.FarmerID = far.FarmerID
					LEFT JOIN ktv_bank ba ON far.`BankID` = ba.`BankID`
					LEFT JOIN `ktv_certification_pre_afl` pafl ON
						a.`IMSID` = pafl.`IMSID` AND a.`FarmerID` = pafl.`FarmerID` AND pafl.`StatusCode`='active'

					LEFT JOIN ktv_village vil ON far.`VillageID` = vil.`VillageID`
					LEFT JOIN ktv_subdistrict subd ON vil.`SubDistrictID` = subd.`SubDistrictID`
					LEFT JOIN ktv_district dis ON subd.`DistrictID` = dis.`DistrictID`
					LEFT JOIN ktv_province prov ON dis.`ProvinceID` = prov.`ProvinceID`

					LEFT JOIN `ktv_ims_ics_reinspection` re ON 1=1
						AND a.IMSID = re.`IMSID` AND a.`FarmerID` = re.`FarmerID`

					LEFT JOIN ktv_ims_cluster cl ON pafl.ClusterID = cl.ClusterID

					LEFT JOIN ktv_ims ims ON a.IMSID = ims.IMSID
					LEFT JOIN ktv_ims_master imsm ON imsm.IMSMasterID=ims.IMSMasterID
					LEFT JOIN ktv_certification_holders ch ON 1=1
						AND imsm.`CertHolderID` = ch.`CertHolderID`
				WHERE
					a.IMSID = ?
					$sqlWhere
				ORDER BY a.FarmerID ASC";
		$query = $this->db->query($sql, array($IMSID));
		$DataList = $query->result_array();

		if ($StatusNya == 'Comply') {
			if(isset($DataList[0]['IMSID'])){
				for ($i=0; $i < count($DataList); $i++) {
					//1. Cek apakah sudah ada audit log nya
					$sql = "SELECT
								aflg.`FarmerID`
								, aflg.`CertGardenNr`
								, aflg.`CertSurveyNr`
							FROM
								ktv_certification_afl_garden aflg
								INNER JOIN ktv_certification_audit_log au ON 1=1
									AND aflg.`FarmerID` = au.`FarmerID`
									AND aflg.`CertSurveyNr` = au.`SurveyNr`
									AND aflg.`CertGardenNr` = au.`GardenNr`
									AND au.`Certification` = ?
							WHERE
								aflg.`IMSID` = ?
								AND aflg.`FarmerID` = ?";
					$p = array(
						$DataList[$i]['CertProgID'],
						$DataList[$i]['IMSID'],
						$DataList[$i]['FarmerID']
					);
					$DataCek1 = $this->db->query($sql,$p)->result_array();
					if(isset($DataCek1[0]['FarmerID'])){
						//2. Cek apakah sudah Audit Log belum punya  Audit Sum
						$sql = "SELECT
									SUM(IF(dg.`DaconID` IS NOT NULL,1,0)) AS CekAuditSum
								FROM
									ktv_certification_afl_garden aflg
									INNER JOIN ktv_certification_audit_log au ON 1=1
										AND aflg.`FarmerID` = au.`FarmerID`
										AND aflg.`CertSurveyNr` = au.`SurveyNr`
										AND aflg.`CertGardenNr` = au.`GardenNr`
										AND au.`Certification` = ?
									LEFT JOIN ktv_farmer_garden_datacontrol dg ON 1=1
										AND au.`FarmerID` = dg.MemberID
										AND au.`SurveyNr` = dg.`SurveyNr`
										AND au.`GardenNr` = dg.`PlotNr`
										AND au.`Certification` = dg.`Certification`
								WHERE
									aflg.`IMSID` = ?
									AND aflg.`FarmerID` = ?
								GROUP BY au.`FarmerID`";
						$p = array(
							$DataList[$i]['CertProgID'],
							$DataList[$i]['IMSID'],
							$DataList[$i]['FarmerID']
						);
						$DataCek2 = $this->db->query($sql,$p)->row_array();
						$CekAuditSum2 = (int) $DataCek2['CekAuditSum'];
						if($CekAuditSum2 > 0){
							//3. Cek apakah semua gardenya sudah punya Audit Summary
							$sql = "SELECT
										IF(banding.JumlahGarden = banding.SumCekDg,'All','Some') AS HasilBanding
									FROM
									(
									SELECT
										(
											SELECT
												COUNT(aflg.FarmerID) AS JumlahGar
											FROM
												ktv_certification_afl_garden aflg
											WHERE
												aflg.`IMSID` = ?
												AND aflg.`FarmerID` = ?
										) AS JumlahGarden,
										(
											SELECT
												SUM(gfar.CekDg) AS SumCekDg
											FROM
											(
											SELECT
												au.`FarmerID`
												, au.`GardenNr`
												, au.`SurveyNr`
												, au.`Certification`
												, IF(dg.`DaconID` IS NOT NULL,1,0) AS CekDg
											FROM
												ktv_certification_afl_garden aflg
												INNER JOIN ktv_certification_audit_log au ON 1=1
													AND aflg.`FarmerID` = au.`FarmerID`
													AND aflg.`CertSurveyNr` = au.`SurveyNr`
													AND aflg.`CertGardenNr` = au.`GardenNr`
													AND au.`Certification` = ?
												LEFT JOIN ktv_farmer_garden_datacontrol dg ON 1=1
													AND au.`FarmerID` = dg.MemberID
													AND au.`SurveyNr` = dg.`SurveyNr`
													AND au.`GardenNr` = dg.`PlotNr`
													AND au.`Certification` = dg.`Certification`
											WHERE
												aflg.`IMSID` = ?
												AND aflg.`FarmerID` = ?
											GROUP BY au.`FarmerID`, au.`GardenNr`, au.`SurveyNr`, au.`Certification`
											) AS gfar
											GROUP BY gfar.FarmerID
										) AS SumCekDg
									) AS banding";
							$p = array(
								$DataList[$i]['IMSID'],
								$DataList[$i]['FarmerID'],
								$DataList[$i]['CertProgID'],
								$DataList[$i]['IMSID'],
								$DataList[$i]['FarmerID']
							);
							$DataCek3 = $this->db->query($sql,$p)->row_array();
							$DataList[$i]['AuditSummaryStatus'] = $DataCek3['HasilBanding'];
						}else{
							$DataList[$i]['AuditSummaryStatus'] = 'None';
						}
					}else{
						$DataList[$i]['AuditSummaryStatus'] = '-';
					}
				}
			}
		}

		return $DataList;
	}

	public function cflListExportExcelGarden($IMSID)
	{
		$sql = "SELECT
				a.IMSID,
				a.FarmerID AFLFarmerID,
				a.FarmerID,
				far.ExtFarmerID,
				a.FarmerName,
				a.Gender,
				far.Birthdate,
				FLOOR(DATEDIFF(CURDATE(), far.Birthdate) / 365.25) AS Age,
				CONCAT('[', b.`CPGid`, '] ', b.`GroupName`) AS FarmerGroup,
				a.`Village`,
				CASE
					WHEN a.CertStatusAudit='1' THEN 'Comply'
					WHEN a.CertStatusAudit='2' THEN 'Not Comply'
					WHEN a.CertStatusAudit='3' THEN 'Comply with Recommendation'
				END AS AFLStatus,
				a.CertGardenNr,
				a.`CertSurveyNr`,
				a.PolygonStatus,
				a.CertLatitude,
				a.CertLongitude,
				a.CertFirstYear,
				a.CertICSDate AS ICSDate,
				a.CertNextHarvest,
				a.CertHarvest,
				a.CertHectare,
				a.CertAuditRemark,
				a.CertPohonTM,
				a.CertPohonTBM,
				a.CertPohonTR,
				a.CertCommentAudit AS AuditComment,
				a.CertRecommendationAudit AS AuditRecommendationComment
			FROM
				`ktv_certification_certified_farmer` certf
				INNER JOIN ktv_certification_afl_garden a ON certf.FarmerID = a.FarmerID AND a.IMSID=certf.IMSID
				LEFT JOIN ktv_cpg b
					ON a.`CPGid` = b.`CPGid`
				LEFT JOIN ktv_members far ON 1=1
					AND a.FarmerID = far.FarmerID
			WHERE
				a.IMSID = ?
			ORDER BY a.FarmerID ASC";
		$query = $this->db->query($sql, array($IMSID));
		return $query->result_array();
	}

	public function aflListExportExcelGarden($IMSID, $StatusNya)
	{
		if ($StatusNya == 'Comply') {
			$sqlWhere = "AND a.CertStatusAudit IN ('1','3')";
		} elseif ($StatusNya == 'NotComply') {
			$sqlWhere = "AND a.CertStatusAudit = '2'";
		} elseif ($StatusNya == 'NoStatus') {
			$sqlWhere = "AND a.CertStatusAudit = '-'";
		} elseif ($StatusNya == 'In123') {
			$sqlWhere = "AND a.CertStatusAudit IN ('1','2','3')";
		}

		$sql = "SELECT
				a.IMSID,
				a.FarmerID AFLFarmerID,
				a.FarmerID,
				far.ExtFarmerID,
				a.FarmerName,
				a.Gender,
				far.Birthdate,
				FLOOR(DATEDIFF(CURDATE(), far.Birthdate) / 365.25) AS Age,
				CONCAT('[', b.`CPGid`, '] ', b.`GroupName`) AS FarmerGroup,
				prov.`Province`,
				dis.`District`,
				subd.`SubDistrict`,
				a.`Village`,
				CASE
					WHEN a.CertStatusAudit='1' THEN 'Comply'
					WHEN a.CertStatusAudit='2' THEN 'Not Comply'
					WHEN a.CertStatusAudit='3' THEN 'Comply with Recommendation'
					ELSE '-'
				END AS AFLStatus,
				a.CertGardenNr,
				a.`CertSurveyNr`,
				CASE
					WHEN a.ExternalAuditStatus='1' THEN 'No Status'
					WHEN a.ExternalAuditStatus='2' THEN 'Pass'
					WHEN a.ExternalAuditStatus='3' THEN 'Not Pass'
				END AS ExternalAuditStatus,
				IF(re.`FarmerID` IS NULL,'No','Yes') AS Reinspection,
				CASE 
					WHEN gstat.ActiveStatus = '1' THEN '".lang('Active')."'
					WHEN gstat.ActiveStatus = '2' THEN '".lang('Inactive')."'
					ELSE '-'
				END AS GardenStatus,
				CASE
					WHEN gstat.GardenStatus = '1' THEN 'Died'
					WHEN gstat.GardenStatus = '2' THEN 'Moved/left the area'
					WHEN gstat.GardenStatus = '3' THEN 'Switched to other crop'
					WHEN gstat.GardenStatus = '4' THEN 'Sold the land'
					WHEN gstat.GardenStatus = '5' THEN 'Gave the land to family member'
					WHEN gstat.GardenStatus = '6' THEN 'Force Major'
					ELSE '-'
				END AS NotActiveStatus,
				a.PolygonStatus,
				a.CertLatitude,
				a.CertLongitude,
				a.CertFirstYear,
				a.CertICSDate AS ICSDate,
				a.CertHarvest,
				a.CertNextHarvest,
				a.CertHectare,
				a.CertPohonTM,
				a.CertPohonTBM,
				a.CertPohonTR,
				a.CertCommentAudit AS AuditComment,
				a.CertRecommendationAudit AS AuditRecommendationComment,
				a.CertAuditRemark,
				a.`IMSCreator`,
				CASE
					WHEN far.StatusFarmer='1' THEN '".lang('Active')."'
					WHEN far.StatusFarmer='2' THEN '".lang('Not Active')."'
				END AS StatusFarmer,
				CASE
					WHEN far.ReasonStatusFarmer='1' THEN '".lang('Died')."'
					WHEN far.ReasonStatusFarmer='2' THEN '".lang('Moved/left the area')."'
					WHEN far.ReasonStatusFarmer='3' THEN '".lang('Stop Farming')."'
				END AS ReasonStatusFarmer,
				CASE
					WHEN pafl.`StatusComply`='1' THEN '".lang('Yes')."'
					WHEN pafl.`StatusComply`='2' THEN '".lang('No')."'
				END AS EligibleForAudit,
				pafl.`AuditRemark`
			FROM
				ktv_certification_afl_garden a
				LEFT JOIN ktv_cpg b ON a.`CPGid` = b.`CPGid`
				LEFT JOIN ktv_members far ON 1=1 AND a.FarmerID = far.FarmerID
				LEFT JOIN `ktv_certification_pre_afl` pafl ON a.`IMSID` = pafl.`IMSID` AND a.`FarmerID` = pafl.`FarmerID` AND pafl.`StatusCode`='active'

				LEFT JOIN ktv_village vil ON far.`VillageID` = vil.`VillageID`
		LEFT JOIN ktv_subdistrict subd ON vil.`SubDistrictID` = subd.`SubDistrictID`
		LEFT JOIN ktv_district dis ON subd.`DistrictID` = dis.`DistrictID`
		LEFT JOIN ktv_province prov ON dis.`ProvinceID` = prov.`ProvinceID`

				LEFT JOIN `ktv_members_garden_status` gstat ON 1=1
					AND gstat.FarmerID = a.FarmerID AND gstat.GardenNr = a.CertGardenNr

		LEFT JOIN `ktv_ims_ics_reinspection` re ON 1=1 
					AND a.IMSID = re.`IMSID` AND a.`FarmerID` = re.`FarmerID` AND a.CertGardenNr = re.GardenNr
			WHERE
				a.IMSID = ?
				$sqlWhere
			ORDER BY a.FarmerID ASC
			";
		$query = $this->db->query($sql, array($IMSID));
		$result['size'] = $query->num_rows();
		$result['data'] = $query->result_array();
		return $result;
	}

	public function GetDataGardenByAflGarden($IMSID,$OpsiCall){
		$SqlOpsiCall = "";
		switch($OpsiCall){
			case 'All':
				$SqlOpsiCall = " AND sose.ObjType IN ('Applicant','Existing Farmer','Existing Certified Farmer') ";
			break;
			case 'Year1':
				$SqlOpsiCall = " AND sose.ObjType IN ('Applicant','Existing Farmer') ";
			break;
			case 'Year2':
				$SqlOpsiCall = " AND sose.ObjType = 'Existing Certified Farmer' ";
			break;
		}

		$sql = "SELECT
				a.IMSID,
				a.FarmerID AFLFarmerID,
				a.FarmerID,
				far.ExtFarmerID,
				a.FarmerName,
				a.Gender,
				CASE
					WHEN a.CertStatusAudit='1' THEN 'Comply'
					WHEN a.CertStatusAudit='2' THEN 'Not Comply'
					WHEN a.CertStatusAudit='3' THEN 'Comply with Recommendation'
					ELSE '-'
				END AS AFLStatus,
				a.CertYear,
				a.CertFirstYear,
				a.CertHarvest,
				a.CertNextHarvest,
				a.CertHectare,
				a.CertGardenNr,
				a.`CertSurveyNr`,
				CONCAT('[', b.`CPGid`, '] ', b.`GroupName`) AS FarmerGroup,
				a.`Village`,
				a.CertICSDate AS ICSDate,
				a.CertCommentAudit AS AuditComment,
				a.CertRecommendationAudit AS AuditRecommendationComment,
				a.CertAuditRemark,
				a.CertPohonTM,
				a.CertPohonTBM,
				a.CertPohonTR,

				a.PolygonStatus,
				a.CertLatitude,
				a.CertLongitude,

				prov.`Province`,
				dis.`District`,
				subd.`SubDistrict`,
				a.`IMSCreator`,

				CASE
					WHEN gstat.ActiveStatus = '1' THEN '".lang('Active')."'
					WHEN gstat.ActiveStatus = '2' THEN '".lang('Inactive')."'
					ELSE '-'
				END AS GardenStatus,
				CASE
					WHEN gstat.GardenStatus = '1' THEN '".lang('Died')."'
					WHEN gstat.GardenStatus = '2' THEN '".lang('Moved/left the area')."'
					WHEN gstat.GardenStatus = '3' THEN '".lang('Switched to other crop')."'
					WHEN gstat.GardenStatus = '4' THEN '".lang('Sold the land')."'
					WHEN gstat.GardenStatus = '5' THEN '".lang('Gave the land to family member')."'
					WHEN gstat.GardenStatus = '6' THEN '".lang('Force Major')."'
					ELSE '-'
				END AS NotActiveStatus,

				CASE
					WHEN far.StatusFarmer='1' THEN '".lang('Active')."'
					WHEN far.StatusFarmer='2' THEN '".lang('Not Active')."'
				END AS StatusFarmer,
				CASE
					WHEN far.ReasonStatusFarmer='1' THEN '".lang('Died')."'
					WHEN far.ReasonStatusFarmer='2' THEN '".lang('Moved/left the area')."'
					WHEN far.ReasonStatusFarmer='3' THEN '".lang('Stop Farming')."'
				END AS ReasonStatusFarmer,
				CASE
					WHEN pafl.`StatusComply`='1' THEN '".lang('Yes')."'
					WHEN pafl.`StatusComply`='2' THEN '".lang('No')."'
				END AS EligibleForAudit,
				pafl.`AuditRemark`,
				far.Birthdate,
				IF(re.`FarmerID` IS NULL,'".lang('No')."','".lang('Yes')."') AS Reinspection,

				IF(sose.ObjType IS NULL,'-',IF(sose.ObjType='Existing Certified Farmer','Year 2','Year 1')) AS SoseType,

				gar.*,
				cl.ClusterName
			FROM
				ktv_certification_afl_garden a
				LEFT JOIN ktv_cpg b
					ON a.`CPGid` = b.`CPGid`
				LEFT JOIN ktv_members far ON 1=1
					AND a.FarmerID = far.FarmerID

				LEFT JOIN `ktv_certification_pre_afl` pafl ON a.`IMSID` = pafl.`IMSID` AND a.`FarmerID` = pafl.`FarmerID`
				LEFT JOIN `ktv_ims_cluster` cl ON 1=1
					AND pafl.IMSID = cl.IMSID
					AND pafl.ClusterID = cl.ClusterID

				LEFT JOIN ktv_village vil ON far.`VillageID` = vil.`VillageID`
				LEFT JOIN ktv_subdistrict subd ON vil.`SubDistrictID` = subd.`SubDistrictID`
				LEFT JOIN ktv_district dis ON subd.`DistrictID` = dis.`DistrictID`
				LEFT JOIN ktv_province prov ON dis.`ProvinceID` = prov.`ProvinceID`

				LEFT JOIN `ktv_members_garden_status` gstat ON 1=1
					AND gstat.FarmerID = a.FarmerID
					AND gstat.GardenNr = a.CertGardenNr

				LEFT JOIN `ktv_ims_ics_reinspection` re ON 1=1
						AND a.IMSID = re.`IMSID`
						AND a.`FarmerID` = re.`FarmerID`
						AND a.CertGardenNr = re.GardenNr

				LEFT JOIN ktv_members_garden gar ON 1=1
					AND a.FarmerID = gar.FarmerID
					AND a.CertGardenNr = gar.GardenNr
					AND a.CertSurveyNr = gar.SurveyNr

				LEFT JOIN ktv_ims_soc_sel sose ON 1=1
					AND a.IMSID = sose.IMSID
					AND a.FarmerID = sose.DestObjID
			WHERE
				a.IMSID = ?
				AND a.CertStatusAudit IN ('1','2','3')
				$SqlOpsiCall
			ORDER BY a.FarmerID ASC
			";
		$query = $this->db->query($sql, array($IMSID));
		return $query->result_array();
	}

	public function GetDataControlRefGarden(){
		$DataReturn = array();

		$sql = "SELECT
					a.*
				FROM
					`ktv_ref_survey_datacontrol` a
				WHERE
					a.`SurveyType` = 'Garden'";
		$data = $this->db->query($sql)->result_array();

		foreach ($data as $key => $value) {
			$DataReturn[$value['RefID']] = $value;
		}

		return $DataReturn;
	}

	public function getExportExcelFarmerPreAflGarden($IMSID)
	{
		$sql = "SELECT
			  d.FarmerID,
			  d.`FarmerName`,
			  d.`Birthdate`,
			  e.`CPGid`,
			  e.`GroupName`,
			  j.`Year` AS CertificationYear,
			  h.`District`,
			  g.`SubDistrict`,
			  f.`Village`,
			  a.GardenNr,
			  a.SurveyNr,
			  a.`DateCollection`,
			  a.`Latitude`,
			  a.`Longitude`,
			  a.`GardenHaUnCertified` AS FarmHa,
			  a.`Production` AS LastYearHarvestByEstimation,
			  (
				(
				  IFNULL(a.PanenTrekMonths, 0) * IFNULL(a.PanenTrekPanenMonth, 0) * IFNULL(a.PanenTrekKg, 0)
				) + (
				  IFNULL(a.PanenBiasaMonths, 0) * IFNULL(a.PanenBiasaPanenMonth, 0) * IFNULL(a.PanenBiasaKg, 0)
				) + (
				  IFNULL(a.PanenRayaMonths, 0) * IFNULL(a.PanenRayaPanenMonth, 0) * IFNULL(a.PanenRayaKg, 0)
				)
			  ) AS LastYearHarvestByFormula,
			  a.`ProductionNext` AS PresentYearHarvestByEstimation,
			  IFNULL(
				a.`ProductionNext` / a.`GardenHaUnCertified`,
				0
			  ) AS KgHa,
			  IF(
				(
				  a.`ProductionNext` / a.`GardenHaUnCertified`
				) > 750,
				IFNULL(750 * a.`GardenHaUnCertified`, 0),
				0
			  ) AS 'SuggestKgRevisionKgHaOver750',
			  (
				(
				  IFNULL(a.`ProductionNext`, 0) - (
					(
					  IFNULL(a.PanenTrekMonths, 0) * IFNULL(a.PanenTrekPanenMonth, 0) * IFNULL(a.PanenTrekKg, 0)
					) + (
					  IFNULL(a.PanenBiasaMonths, 0) * IFNULL(a.PanenBiasaPanenMonth, 0) * IFNULL(a.PanenBiasaKg, 0)
					) + (
					  IFNULL(a.PanenRayaMonths, 0) * IFNULL(a.PanenRayaPanenMonth, 0) * IFNULL(a.PanenRayaKg, 0)
					)
				  )
				) / (
				  (
					IFNULL(a.PanenTrekMonths, 0) * IFNULL(a.PanenTrekPanenMonth, 0) * IFNULL(a.PanenTrekKg, 0)
				  ) + (
					IFNULL(a.PanenBiasaMonths, 0) * IFNULL(a.PanenBiasaPanenMonth, 0) * IFNULL(a.PanenBiasaKg, 0)
				  ) + (
					IFNULL(a.PanenRayaMonths, 0) * IFNULL(a.PanenRayaPanenMonth, 0) * IFNULL(a.PanenRayaKg, 0)
				  )
				)
			  ) * 100 AS '%KgIncline',
			  IF(
				(
				  (
					(
					  IFNULL(a.`ProductionNext`, 0) - (
						(
						  IFNULL(a.PanenTrekMonths, 0) * IFNULL(a.PanenTrekPanenMonth, 0) * IFNULL(a.PanenTrekKg, 0)
						) + (
						  IFNULL(a.PanenBiasaMonths, 0) * IFNULL(a.PanenBiasaPanenMonth, 0) * IFNULL(a.PanenBiasaKg, 0)
						) + (
						  IFNULL(a.PanenRayaMonths, 0) * IFNULL(a.PanenRayaPanenMonth, 0) * IFNULL(a.PanenRayaKg, 0)
						)
					  )
					) / (
					  (
						IFNULL(a.PanenTrekMonths, 0) * IFNULL(a.PanenTrekPanenMonth, 0) * IFNULL(a.PanenTrekKg, 0)
					  ) + (
						IFNULL(a.PanenBiasaMonths, 0) * IFNULL(a.PanenBiasaPanenMonth, 0) * IFNULL(a.PanenBiasaKg, 0)
					  ) + (
						IFNULL(a.PanenRayaMonths, 0) * IFNULL(a.PanenRayaPanenMonth, 0) * IFNULL(a.PanenRayaKg, 0)
					  )
					)
				  ) * 100
				) > 10,
				FLOOR(
				  ((10 / 100) + 1) * (
					(
					  IFNULL(a.PanenTrekMonths, 0) * IFNULL(a.PanenTrekPanenMonth, 0) * IFNULL(a.PanenTrekKg, 0)
					) + (
					  IFNULL(a.PanenBiasaMonths, 0) * IFNULL(a.PanenBiasaPanenMonth, 0) * IFNULL(a.PanenBiasaKg, 0)
					) + (
					  IFNULL(a.PanenRayaMonths, 0) * IFNULL(a.PanenRayaPanenMonth, 0) * IFNULL(a.PanenRayaKg, 0)
					)
				  )
				),
				0
			  ) AS 'SuggestKgRevision%KgInclineOver10',
			  a.`PohonTM`,
			  a.`PohonTBM`,
			  a.`PohonRehab`,
			  IFNULL(
				(
				  a.`PohonTM` + a.`PohonTBM` + a.`PohonRehab`
				) / a.`GardenHaUnCertified`,
				0
			  ) AS TreeHa,
			  IF(
				(
				  (
					a.`PohonTM` + a.`PohonTBM` + a.`PohonRehab`
				  ) / a.`GardenHaUnCertified`
				) > 2600,
				'Yes',
				'No'
			  ) AS TreeHaOver2600,
			  IF(b.`Certification` = 1, 'UTZ', '') AS Certification,
			  c.ICSDate,
			  IF(
				c.StatusAudit = 1,
				'Comply',
				IF(
				  c.StatusAudit = 3,
				  'Comply with Condition',
				  IF(c.StatusAudit = 2, 'Not Comply', '')
				)
			  ) AS StatusAudit,
			  c.CommentAudit,
			  c.DateRevisionAudit,
			  c.RecommendationAudit
			FROM
			  ktv_members_view d
			  LEFT JOIN ktv_members_garden a
				ON d.`FarmerID` = a.`FarmerID`
			  LEFT JOIN ktv_certification b
				ON b.`FarmerID` = a.`FarmerID`
				AND b.`GardenNr` = a.`GardenNr`
				AND b.`SurveyNr` = a.`SurveyNr`
			  LEFT JOIN
				(SELECT
				  m.FarmerID,
				  m.GardenNr,
				  m.SurveyNr,
				  m.Certification,
				  m.ICSDate,
				  m.StatusAudit,
				  m.CommentAudit,
				  m.`DateRevisionAudit`,
				  m.`RecommendationAudit`
				FROM
				  ktv_certification_audit_log m
				  INNER JOIN
					(SELECT
					  FarmerID,
					  GardenNr,
					  SurveyNr,
					  Certification,
					  MAX(ICSDate) AS LatestICSDate
					FROM
					  ktv_certification_audit_log m
					GROUP BY FarmerID,
					  GardenNr,
					  SurveyNr,
					  Certification) n
					ON n.FarmerID = m.`FarmerID`
					AND n.GardenNr = m.`GardenNr`
					AND n.SurveyNr = m.`SurveyNr`
					AND n.Certification = m.`Certification`
					AND n.LatestICSDate = m.`ICSDate`) c
				ON c.FarmerID = b.`FarmerID`
				AND c.GardenNr = b.`GardenNr`
				AND c.SurveyNr = b.`SurveyNr`
				AND c.Certification = b.`Certification`
			  LEFT JOIN ktv_cpg e
				ON e.`CPGid` = d.`CPGid`
			  LEFT JOIN ktv_village f
				ON f.`VillageID` = d.`VillageID`
			  LEFT JOIN ktv_subdistrict g
				ON g.`SubDistrictID` = f.`SubDistrictID`
			  LEFT JOIN ktv_district h
				ON h.`DistrictID` = g.`DistrictID`
			  JOIN ktv_certification_pre_afl i
				ON i.`FarmerID` = d.`FarmerID`
			  LEFT JOIN ktv_ims j
				ON j.`IMSID` = i.`IMSID`
			  LEFT JOIN `ktv_ims_master` k
				ON k.`IMSMasterID` = j.`IMSMasterID`
			WHERE j.`IMSID` = ?
			ORDER BY d.FarmerID";
		$query = $this->db->query($sql, array($IMSID));
		return $query->result_array();
	}

	public function getExportExcelFarmerPreAflGardenByAflGarden($IMSID)
	{
		//Cek SQL BirthDate / Age
		if($_SESSION['PartnerID'] == '8') {
			$SqlBirthDateAge = " FLOOR(DATEDIFF(CURDATE(), d.DateOfBirth) / 365.25) AS Age, ";
		}else{
			$SqlBirthDateAge = " d.`DateOfBirth`, ";
		}

		$sql = "SELECT
				d.MemberID FarmerID,
				d.ExtID AS OtherFarmerID,
				d.MemberName `FarmerName`,
				$SqlBirthDateAge
				e.`FarmerGroupID`,
				e.`GroupName`,
				j.`Year` AS CertificationYear,
				h.`District`,
				g.`SubDistrict`,
				f.`Village`,
				aflg.GardenNr,
				aflg.SurveyNr,
				a.`DateCollection`,
				a.`Latitude`,
				a.`Longitude`,
				a.`GardenAreaHa` AS FarmHa,
				a.`AnnualProduction` AS LastYearHarvestByEstimation,
				a.`TreeTM`,
				a.`TreeTBM`,
				a.`TreeTR`,
				IFNULL( ( a.`TreeTM` + a.`TreeTBM` + a.`TreeTR` ) / a.`GardenAreaHa`, 0 ) AS TreeHa,
			IF
				( ( ( a.`TreeTM` + a.`TreeTBM` + a.`TreeTR` ) / a.`GardenAreaHa` ) > 2600, 'Yes', 'No' ) AS TreeHaOver2600,
			IF
				( b.`Certification` = 1, 'UTZ', '' ) AS Certification,
				c.ICSDate,
			IF
				(
					c.StatusAudit = 1,
					'Comply',
				IF
					( c.StatusAudit = 3, 'Comply with Condition', IF ( c.StatusAudit = 2, 'Not Comply', '' ) ) 
				) AS StatusAudit,
				c.CommentAudit,
				c.DateRevisionAudit,
				c.RecommendationAudit 
			FROM
				ktv_members d
				JOIN ktv_certification_pre_afl i ON i.`FarmerID` = d.`MemberID`
				LEFT JOIN ktv_ims j ON j.`IMSID` = i.`IMSID`
				LEFT JOIN `ktv_ims_master` k ON k.`IMSMasterID` = j.`IMSMasterID`
				LEFT JOIN ktv_certification_holders hold ON j.CertHolderID = hold.CertHolderID
				LEFT JOIN ktv_certification_pre_afl_garden aflg ON 1 = 1 
				AND i.IMSID = aflg.IMSID 
				AND i.FarmerID = aflg.FarmerID
				LEFT JOIN ktv_survey_plot a ON 1 = 1 
				AND aflg.`FarmerID` = a.`MemberID` 
				AND aflg.GardenNr = a.PlotNr 
				AND aflg.SurveyNr = a.SurveyNr
				LEFT JOIN ktv_certification b ON b.`FarmerID` = a.`MemberID` 
				AND b.`GardenNr` = a.`PlotNr` 
				AND b.`SurveyNr` = a.`SurveyNr` 
				AND b.Certification = hold.CertProgID
				LEFT JOIN (
				SELECT
					m.FarmerID,
					m.GardenNr,
					m.SurveyNr,
					m.Certification,
					m.ICSDate,
					m.StatusAudit,
					m.CommentAudit,
					m.`DateRevisionAudit`,
					m.`RecommendationAudit` 
				FROM
					ktv_certification_audit_log m
					INNER JOIN (
					SELECT
						FarmerID,
						GardenNr,
						SurveyNr,
						Certification,
						MAX( ICSDate ) AS LatestICSDate 
					FROM
						ktv_certification_audit_log m 
					GROUP BY
						FarmerID,
						GardenNr,
						SurveyNr,
						Certification 
					) n ON n.FarmerID = m.`FarmerID` 
					AND n.GardenNr = m.`GardenNr` 
					AND n.SurveyNr = m.`SurveyNr` 
					AND n.Certification = m.`Certification` 
					AND n.LatestICSDate = m.`ICSDate` 
				) c ON c.FarmerID = a.`MemberID` 
				AND c.GardenNr = a.`PlotNr` 
				AND c.SurveyNr = a.`SurveyNr` 
				AND c.Certification = hold.CertProgID
				LEFT JOIN ktv_farmer_group e ON e.`FarmerGroupID` = d.`FarmerGroupID`
				LEFT JOIN ktv_village f ON f.`VillageID` = d.`VillageID`
				LEFT JOIN ktv_subdistrict g ON g.`SubDistrictID` = f.`SubDistrictID`
				LEFT JOIN ktv_district h ON h.`DistrictID` = g.`DistrictID` 
			WHERE
				j.`IMSID` = ? 
			ORDER BY
				d.MemberID";
		$query = $this->db->query($sql, array($IMSID));
		return $query->result_array();
	}

	public function getFarmerAflP1Summary($IMSID)
	{
		if($_SESSION['PartnerID'] == '8')
			$SqlBirthdateAge = " FLOOR(DATEDIFF(CURDATE(), a.Birthdate) / 365.25) AS 'Umur', ";
		else
			$SqlBirthdateAge = " a.BirthDate AS 'Tanggal Lahir', ";

		$sql = "SELECT
			  a.`FarmerID` AS 'ID Petani',
			  a.ExtFarmerID AS 'ID Petani Eksternal',
			  a.`FarmerName` AS 'Nama Petani',
			  a.`DateCollection` AS 'Tgl Interview',
			  a.CPGid AS 'ID Kelompok Tani',
			  b.`GroupName` AS 'Kelompok Tani',
			  c.`Province` AS 'Propinsi',
			  d.`District` AS 'Kabupaten',
			  e.`SubDistrict` AS 'Kecamatan',
			  f.`Village` AS 'Desa',
			  a.Address AS 'Alamat',
			  a.RtRw AS 'RT / RW',
			  IF(a.Gender = 1, 'L', IF(a.Gender = 2, 'P', '')) AS 'Jenis Kelamin',
			  IF(
				a.MaritalStatus = 1,
				'Menikah',
				IF(
				  a.MaritalStatus = 2,
				  'Lajang',
				  IF(a.MaritalStatus = 2, 'Janda/Duda', '')
				)
			  ) AS 'Status Perkawinan',
			  $SqlBirthdateAge
			  a.Handphone AS 'Handphone',
			  IF(
				Education = 1,
				'Tidak pernah sekolah',
				IF(
				  Education = 2,
				  'Tidak tamat SD',
				  IF(
					Education = 3,
					'Tamat SD',
					IF(
					  Education = 4,
					  'Tamat SMP',
					  IF(
						Education = 5,
						'Tamat SMA',
						IF(
						  Education = 6,
						  'Tamat Perguruan Tinggi',
						  ''
						)
					  )
					)
				  )
				)
			  ) AS 'Pendidikan terakhir',
			  IF(
				a.StatusFarmer = 1,
				'Yes',
				IF(a.StatusFarmer = 2, 'No', '')
			  ) AS 'Status aktif',
			  IF(
				ReasonStatusFarmer = 1,
				'Meninggal',
				IF(
				  ReasonStatusFarmer = 2,
				  'Pindah',
				  IF(
					ReasonStatusFarmer = 3,
					'Berhenti bertani',
					''
				  )
				)
			  ) AS 'Alasan jika petani sudah tidak aktif',
			  AccountBeneficiary AS 'Nama Akun',
			  i.`BankName` AS 'Nama Bank',
			  BankBranch AS 'Cabang',
			  AccountNumber AS 'Nomor rekening',
			  Photo AS 'Foto Petani',
			  LearningContractStatus AS 'Kontrak Pelatihan',
			  LearningContractSign AS 'Tanda Tangan Petani',
			  a.`DateCreated`,
			  g.`UserRealName` AS CreatedBy,
			  a.`DateUpdated`,
			  h.`UserRealName` AS LastModifiedBy
			FROM
			  `ktv_members` a
			  LEFT JOIN ktv_cpg b
				ON b.`CPGid` = a.`CPGid`
			  LEFT JOIN ktv_village f
				ON f.`VillageID` = a.`VillageID`
			  LEFT JOIN ktv_subdistrict e
				ON e.`SubDistrictID` = f.SubDistrictID
			  LEFT JOIN ktv_district d
				ON d.`DistrictID` = e.DistrictID
			  LEFT JOIN ktv_province c
				ON c.`ProvinceID` = d.ProvinceID
			  LEFT JOIN sys_user g
				ON g.UserId = a.`CreatedBy`
			  LEFT JOIN sys_user h
				ON h.UserId = a.`LastModifiedBy`
			  LEFT JOIN ktv_bank i
				ON i.`BankID` = a.`BankID`
			  INNER JOIN ktv_certification_afl_farmer afl
				ON a.`FarmerID` = afl.`FarmerID`
			WHERE
				a.StatusCode = 'active'
				AND afl.`IMSID` = ?
				AND afl.CertStatusAudit != '-'
			GROUP BY a.`FarmerID`
			ORDER BY a.FarmerID
			";
		$query = $this->db->query($sql, array($IMSID));
		return $query->result_array();
	}

	private function getDateRange($first, $last, $step = '+1 day', $output_format = 'Y-m-d')
	{
		$dates   = array();
		$current = strtotime($first);
		$last    = strtotime($last);

		while ($current <= $last) {

			$dates[] = date($output_format, $current);
			$current = strtotime($step, $current);
		}

		return $dates;
	}

	public function getFarmerAflP1SummaryFaProgress($IMSID, $UserID)
	{
		$arrReturn = array();

		//target
		$sql = "SELECT
				COUNT(*) AS BANYAK
			FROM
				`ktv_certification_pre_afl_target` tar
			WHERE
				tar.`IMSID` = ?
				AND tar.`PICUserID` = ?";
		$query      = $this->db->query($sql, array($IMSID, $UserID));
		$dataTarget = $query->row_array();

		//realnya sudah dapat berapa
		$sql = "SELECT
				COUNT(far.`FarmerID`) AS 'CAPAI',
				DATE(MIN(far.`DateUpdated`)) AS BeginTgl,
				DATE(MAX(far.`DateUpdated`)) AS EndTgl
			FROM
				`ktv_certification_pre_afl_target` tar
				LEFT JOIN ktv_ims ims ON tar.IMSID = ims.IMSID

				#Farmer
				LEFT JOIN ktv_members far ON 1=1
					AND far.`StatusCode` = 'active'
					AND tar.`FarmerID` = far.`FarmerID`
					AND DATE(far.`DateUpdated`) >= DATE_FORMAT( ims.CertEventDate - INTERVAL 6 MONTH, '%Y/%m/%d' )
			WHERE
				tar.`IMSID` = ?
				AND tar.`PICUserID` = ?
				AND far.`FarmerID` IS NOT NULL";
		$query     = $this->db->query($sql, array($IMSID, $UserID));
		$dataCapai = $query->row_array();

		//get data progress by tanggal
		$sql = "SELECT
				DATE(far.`DateUpdated`) AS TglCollect,
				COUNT(far.`FarmerID`) AS 'Capai'
			FROM
				`ktv_certification_pre_afl_target` tar
				LEFT JOIN ktv_ims ims ON tar.IMSID = ims.IMSID

				#Farmer
				LEFT JOIN ktv_members far ON 1=1
					AND far.`StatusCode` = 'active'
					AND tar.`FarmerID` = far.`FarmerID`
					AND DATE(far.`DateUpdated`) >= DATE_FORMAT( ims.CertEventDate - INTERVAL 6 MONTH, '%Y/%m/%d' )
			WHERE
				tar.`IMSID` = ?
				AND tar.`PICUserID` = ?
				AND far.`FarmerID` IS NOT NULL
			GROUP BY DATE(far.`DateUpdated`)
			ORDER BY far.`DateUpdated` ASC";
		$query             = $this->db->query($sql, array($IMSID, $UserID));
		$dataQueryProgress = $query->result_array();

		$dataProgress = array();
		foreach ($dataQueryProgress as $key => $value) {
			$dataProgress[$value['TglCollect']] = $value['Capai'];
		}

		//progress data by tanggal
		$data      = array();
		$dateRange = $this->getDateRange($dataCapai['BeginTgl'], $dataCapai['EndTgl'], '+1 day', 'Y-m-d');
		foreach ($dateRange as $key => $tglProses) {
			$data[$key]['tanggal'] = $tglProses;

			if ($dataProgress[$tglProses] > 0) {
				$data[$key]['capai'] = $dataProgress[$tglProses];
			} else {
				$data[$key]['capai'] = 0;
			}
		}

		$arrReturn['dataList'] = $data;
		$arrReturn['capai']    = $dataCapai['CAPAI'];
		$arrReturn['target']   = $dataTarget['BANYAK'];
		return $arrReturn;
	}

	public function getFarmerAflP1SummaryFa($IMSID, $UserID)
	{
		$arrReturn = array();

		if($_SESSION['PartnerID'] == '8')
			$SqlBirthdateAge = " FLOOR(DATEDIFF(CURDATE(), far.Birthdate) / 365.25) AS 'Umur', ";
		else
			$SqlBirthdateAge = " far.BirthDate AS 'Tanggal Lahir', ";

		$sql = "SELECT
			  far.`FarmerID` AS 'ID Petani',
			  far.ExtFarmerID AS 'ID Petani Eksternal',
			  far.`FarmerName` AS 'Nama Petani',
			  far.`DateCollection` AS 'Tgl Interview',
			  far.CPGid AS 'ID Kelompok Tani',
			  b.`GroupName` AS 'Kelompok Tani',
			  c.`Province` AS 'Propinsi',
			  d.`District` AS 'Kabupaten',
			  e.`SubDistrict` AS 'Kecamatan',
			  f.`Village` AS 'Desa',
			  far.Address AS 'Alamat',
			  far.RtRw AS 'RT / RW',
			  IF(far.Gender = 1, 'L', IF(far.Gender = 2, 'P', '')) AS 'Jenis Kelamin',
			  IF(
				far.MaritalStatus = 1,
				'Menikah',
				IF(
				  far.MaritalStatus = 2,
				  'Lajang',
				  IF(far.MaritalStatus = 2, 'Janda/Duda', '')
				)
			  ) AS 'Status Perkawinan',
			  $SqlBirthdateAge
			  far.Handphone AS 'Handphone',
			  IF(
				Education = 1,
				'Tidak pernah sekolah',
				IF(
				  Education = 2,
				  'Tidak tamat SD',
				  IF(
					Education = 3,
					'Tamat SD',
					IF(
					  Education = 4,
					  'Tamat SMP',
					  IF(
						Education = 5,
						'Tamat SMA',
						IF(
						  Education = 6,
						  'Tamat Perguruan Tinggi',
						  ''
						)
					  )
					)
				  )
				)
			  ) AS 'Pendidikan terakhir',
			  IF(
				StatusFarmer = 1,
				'Yes',
				IF(StatusFarmer = 2, 'No', '')
			  ) AS 'Status aktif',
			  IF(
				ReasonStatusFarmer = 1,
				'Meninggal',
				IF(
				  ReasonStatusFarmer = 2,
				  'Pindah',
				  IF(
					ReasonStatusFarmer = 3,
					'Berhenti bertani',
					''
				  )
				)
			  ) AS 'Alasan jika petani sudah tidak aktif',
			  AccountBeneficiary AS 'Nama Akun',
			  i.`BankName` AS 'Nama Bank',
			  BankBranch AS 'Cabang',
			  AccountNumber AS 'Nomor rekening',
			  far.Photo AS 'Foto Petani',
			  '' AS 'Status File Foto Petani',
			  COUNT(fam.FamilyID) AS 'Jumlah Keluarga dan Pekerja',
			  LearningContractStatus AS 'Kontrak Pelatihan',
			  LearningContractSign AS 'Tanda Tangan Petani',
			  far.`DateCreated`,
			  g.`UserRealName` AS CreatedBy,
			  far.`DateUpdated`,
			  h.`UserRealName` AS LastModifiedBy
			FROM
				`ktv_certification_pre_afl_target` tar
				LEFT JOIN sys_user us ON tar.`PICUserID` = us.`UserId`
				LEFT JOIN ktv_ims ims ON tar.IMSID = ims.IMSID

				#Farmer
				LEFT JOIN ktv_members far ON 1=1
					AND far.`StatusCode` = 'active'
					AND tar.`FarmerID` = far.`FarmerID`
					AND DATE(far.`DateUpdated`) >= DATE_FORMAT( ims.CertEventDate - INTERVAL 6 MONTH, '%Y/%m/%d' )

				#Family
				LEFT JOIN ktv_family fam ON 1=1
					AND far.`FarmerID` = fam.`FarmerID`
					AND fam.`FamilyStatus` != 'inactive'

				LEFT JOIN ktv_cpg b ON b.`CPGid` = far.`CPGid`
				LEFT JOIN ktv_village f ON f.`VillageID` = far.`VillageID`
				LEFT JOIN ktv_subdistrict e ON e.`SubDistrictID` = f.SubDistrictID
				LEFT JOIN ktv_district d ON d.`DistrictID` = d.DistrictID
				LEFT JOIN ktv_province c ON c.`ProvinceID` = c.ProvinceID
				LEFT JOIN sys_user g ON g.UserId = far.`CreatedBy`
				LEFT JOIN sys_user h ON h.UserId = far.`LastModifiedBy`
				LEFT JOIN ktv_bank i ON i.`BankID` = far.`BankID`
			WHERE
				tar.`IMSID` = ?
				AND tar.`PICUserID` = ?
				AND far.`FarmerID` IS NOT NULL
			GROUP BY far.FarmerID
			ORDER BY far.FarmerID ASC
		";
		$query = $this->db->query($sql, array($IMSID, $UserID));
		$data  = $query->result_array();

		//cek foto petani (begin)
		for ($i = 0; $i < count($data); $i++) {
			if ($data[$i]['Foto Petani'] != "") {
				$fotoCek = 'images/Photo/' . $data[$i]['Foto Petani'];
				if (@file_exists($fotoCek)) {
					$data[$i]['Status File Foto Petani'] = lang('Foto Tersedia');
				} else {
					$data[$i]['Status File Foto Petani'] = lang('Foto Tidak Tersedia');
				}
			} else {
				$data[$i]['Status File Foto Petani'] = lang('Foto Tidak Tersedia');
			}
		}
		//cek foto petani (end)

		//target
		$sql = "SELECT
				COUNT(*) AS BANYAK
			FROM
				`ktv_certification_pre_afl_target` tar
			WHERE
				tar.`IMSID` = ?
				AND tar.`PICUserID` = ?";
		$query   = $this->db->query($sql, array($IMSID, $UserID));
		$dataRow = $query->row_array();

		$arrReturn['dataList'] = $data;
		$arrReturn['capai']    = count($data);
		$arrReturn['target']   = $dataRow['BANYAK'];
		return $arrReturn;
	}

	public function getGardenAflP1Summary($IMSID)
	{
		$sql = "SELECT
			  a.`FarmerID` AS 'ID Petani',
			  b.ExtFarmerID AS 'ID Petani Eksternal',
			  b.`FarmerName` AS 'Nama Petani',
			  c.`GroupName` AS 'Kelompok Tani',
			  d.`Province` AS 'Propinsi',
			  e.`District` AS 'Kabupaten',
			  f.`SubDistrict` AS 'Kecamatan',
			  g.`Village` AS 'Desa',
			  a.`GardenNr` AS 'Nr Kebun',
			  a.`SurveyNr` AS 'Nr Survey',
			  a.`DateCollection` AS 'Tgl Interview',
			  IF(
				RoadCondition = 1,
				'Jalan Aspal',
				IF(
				  RoadCondition = 2,
				  'Jalan Pengerasan',
				  IF(
					RoadCondition = 3,
					'Jalan Tanah',
					IF(
					  RoadCondition = 4,
					  'Tidak ada Jalan',
					  ''
					)
				  )
				)
			  ) AS 'Kondisi jalan ke kebun kakao',
			  IF(
				OwnershipCocoa = 1,
				'Pemilik Penggarap',
				IF(
				  OwnershipCocoa = 2,
				  'Profit Sharing',
				  IF(
					OwnershipCocoa = 3,
					'Petani Penyewa',
					IF(OwnershipCocoa = 4, 'Lain-lain', '')
				  )
				)
			  ) AS 'Status kepemilikan tanah',
			  IF(
				LandOwner = 1,
				'Saya Sendiri',
				IF(
				  LandOwner = 2,
				  'Anggota Keluarga',
				  IF(
					LandOwner = 3,
					'Orang Lain',
					IF(LandOwner = 4, 'Tidak Tahu', '')
				  )
				)
			  ) AS 'Pemilik tanah',
			  IF(
				LandCertificate = 1,
				'Tidak Ada',
				IF(
				  LandCertificate = 2,
				  'Akte Notaris/BPN',
				  IF(
					LandCertificate = 3,
					'KKT (Camat)',
					IF(
					  LandCertificate = 4,
					  'Desa/Lurah',
					  IF(
						LandCertificate = 5,
						'Tidak Tahu',
						''
					  )
					)
				  )
				)
			  ) AS 'Serfitikat kepemilikan tanah',
			  GardenDistance AS 'Jarak rumah ke kebun kakao (m)',
			  a.`Latitude`,
			  a.`Longitude`,
			  a.`Elevation`,
			  GardenHaUnCertified AS 'Ukuran kebun (Ha)',
			  GardenAreaCoordinates AS 'Koordinat area kebun',
			  IF(
				GardenLandUse = 1,
				'Converted Forest',
				IF(
				  GardenLandUse = 2,
				  'Limited Forest',
				  IF(
					GardenLandUse = 3,
					'Production Forest',
					IF(
					  GardenLandUse = 4,
					  'Protected Forest',
					  IF(
						GardenLandUse = 5,
						'Unspecified Area',
						''
					  )
					)
				  )
				)
			  ) AS 'Pengggunaan Lahan',
			  TahunTanamanCocoa AS 'Tahun tanam kakao',
			  PohonTBM AS 'Jumlah tanaman belum menghasilkan (pohon)',
			  PohonTM AS 'Jumlah tanaman menghasilkan (pohon)',
			  PohonRehab AS 'Jumlah tanaman rusak (pohon)',
			  GraftedTrees AS 'Jumlah pohon sambung samping/sambung pucuk tunas air ',
			  GraftedTreesTahun AS 'Tahun tanam pohon sambung samping/sambung pucuk tunas air ',
			  TopGraftedTrees AS 'Jumlah penanaman ulang dari sambung pucuk dan biji',
			  TopGraftedTreesTahun AS 'Tahun tanam pohon pengan penanaman ulang dari sambung pucuk dan biji',
			  ReplantedTrees AS 'Jumlah pohon dengan penanaman ulang dan sisipan',
			  ReplantedTreesTahun AS 'Tahun tanam pohon dengan penanaman ulang dan sisipan',
			  S1Nr AS 'S1',
			  S2Nr AS 'S2',
			  J45Nr AS '45/MCC02',
			  M01Nr AS 'M01',
			  TSH858Nr AS 'TSH 858',
			  ICRRI3Nr AS 'ICCRI3',
			  ICRRI4Nr AS 'ICCRI4',
			  ICRRI5Nr AS 'ICCRI5',
			  RCC70Nr AS 'RCC70',
			  RCC71Nr AS 'RCC71',
			  RCC72Nr AS 'RCC72',
			  RCC73Nr AS 'RCC73',
			  LokalNr AS 'Lokal',
			  RCLNr AS 'RCL',
			  THRNr AS 'THR',
			  APNr AS 'AP',
			  PRNr AS 'PR',
			  ScavinaNr AS 'Scavina',
			  MTNr AS 'MT',
			  M02Nr AS 'M02',
			  M04Nr AS 'M04',
			  M06Nr AS 'M06',
			  MHP03Nr AS 'MHP03',
			  MHP04Nr AS 'MHP04',
			  BB01Nr AS 'BB01',
			  BLBNr AS 'BLB',
			  BRTNr AS 'BRT',
			  CloneLain AS 'Klon Lainnya (sebutkan nama varietas)',
			  CloneLainNr AS 'Jumlah klon lain (pohon)',
			  KelapaNr AS 'Kelapa',
			  PinangNr AS 'Pinang',
			  KaretNr AS 'Karet',
			  CengkehNr AS 'Cengkeh',
			  JambuMenteNr AS 'Jambu Mete',
			  SawitNr AS 'Sawit',
			  ArenNr AS 'Aren',
			  PalaNr AS 'Pala',
			  KemiriNr AS 'Kemiri',
			  KapokNr AS 'Kapuk',
			  MahoniNr AS 'Mahoni',
			  JatiNr AS 'Jati',
			  BitiNr AS 'Biti',
			  UruNr AS 'Uru',
			  JabonNr AS 'Jabon',
			  SengonNr AS 'Sengon',
			  AlpukatNr AS 'Alpukat',
			  PisangNr AS 'Pisang',
			  SukunNr AS 'Sukun',
			  CempedakNr AS 'Cempedak',
			  JerukNr AS 'Jeruk',
			  JambuNr AS 'Fruit Trees-Guava',
			  JackFruitNr AS 'Nangka',
			  LangsatNr AS 'Langsat',
			  ManggaNr AS 'Mangga',
			  ManggisNr AS 'Manggis',
			  PepayaNr AS 'Pepaya',
			  RambutanNr AS 'Rambutan',
			  KedondongNr AS 'Kedondong',
			  DurianNr AS 'Durian',
			  JengkolNr AS 'Jengkol',
			  GamalNr AS 'Gamal',
			  LamtoroNr AS 'Lamtoro',
			  PetaiNr AS 'Petai',
			  ShadeTreesNr AS 'Total pohon pelindung',
			  IF(
				ShadeTreesIncProductivity = 1,
				'Yes',
				IF(
				  ShadeTreesIncProductivity = 2,
				  'No',
				  ''
				)
			  ) AS 'Mengapa anda  menanam pohon  pelindung - Untuk meningkatkan produktivitas tanaman kakao',
			  IF(
				ShadeTreesExtraIncome = 1,
				'Yes',
				IF(ShadeTreesExtraIncome = 2, 'No', '')
			  ) AS 'Mengapa anda  menanam pohon  pelindung - Untuk mendapatkan penghasilan tambahan',
			  IF(
				ShadeTreesProtectSoil = 1,
				'Yes',
				IF(ShadeTreesProtectSoil = 2, 'No', '')
			  ) AS 'Mengapa anda  menanam pohon  pelindung - Untuk melindungi tanah',
			  IF(
				ShadeTreesReducePests = 1,
				'Yes',
				IF(ShadeTreesReducePests = 2, 'No', '')
			  ) AS 'Mengapa anda  menanam pohon  pelindung - Untuk mengurangi serangan hama dan penyakit',
			  IF(
				ShadeTreesReduceHeat = 1,
				'Yes',
				IF(ShadeTreesReduceHeat = 2, 'No', '')
			  ) AS 'Mengapa anda  menanam pohon  pelindung - Untuk mengurangi suhu panas di kebun',
			  IF(
				ShadeTreesIncLandValue = 1,
				'Yes',
				IF(
				  ShadeTreesIncLandValue = 2,
				  'No',
				  ''
				)
			  ) AS 'Mengapa anda  menanam pohon  pelindung - Untuk meningkatkan nilai tanah',
			  IF(
				ShadeTreesAddFirewood = 1,
				'Yes',
				IF(ShadeTreesAddFirewood = 2, 'No', '')
			  ) AS 'Mengapa anda  menanam pohon  pelindung - Untuk menambah sumber kayu bakar',
			  IF(
				ShadeTreesAddFodder = 1,
				'Yes',
				IF(ShadeTreesAddFodder = 2, 'No', '')
			  ) AS 'Mengapa anda  menanam pohon  pelindung - Untuk menambah sumber makanan ternak',
			  IF(
				ShadeTreesDoNotKnow = 1,
				'Yes',
				IF(ShadeTreesDoNotKnow = 2, 'No', '')
			  ) AS 'Mengapa anda  menanam pohon  pelindung - Saya tidak tahu',
			  ShadeTreesOthers AS 'Mengapa anda  menanam pohon  pelindung - Lainnya (Sebutkan)',
			  IF(
				ShadeTreesSpreadEvently = 1,
				'Yes',
				IF(
				  ShadeTreesSpreadEvently = 2,
				  'No',
				  ''
				)
			  ) AS '(C) Apakah pohon penaung tersebar merata di kebun',
			  IF(
				ShadeTreesObtainSeeds = 1,
				'Farmer Group',
				IF(
				  ShadeTreesObtainSeeds = 2,
				  'Cooperatives/IMS',
				  IF(
					ShadeTreesObtainSeeds = 3,
					'Seeds Seller',
					IF(
					  ShadeTreesObtainSeeds = 4,
					  'Make your own',
					  ''
					)
				  )
				)
			  ) AS '(C) Darimana anda mendapatkan bibit pohon penaung',
			  Nuts AS 'Kacang-kacangan',
			  Tubers AS 'Umbi-umbian',
			  Patchouli AS 'Nilam',
			  CoverCropOthers AS 'Tanaman Penutup Lainnya (sebutkan)',
			  IF(
				NoCoverCrop = 1,
				'Yes',
				IF(NoCoverCrop = 2, 'No', '')
			  ) AS 'Tidak Ada Tanaman Penutup Tanah',
			  IF(
				ObtainSeedsToday = 1,
				'Supplier yang direkomendasikan IMS',
				IF(
				  ObtainSeedsToday = 2,
				  'Supplier diluar rekomendasi IMS',
				  IF(
					ObtainSeedsToday = 3,
					'Membuat bibit sendiri',
					''
				  )
				)
			  ) AS '(C) Darimana anda memperoleh bibit saat ini',
			  IF(
				SeedsFreeFromPests = 1,
				'Yes',
				IF(SeedsFreeFromPests = 2, 'No', '')
			  ) AS '(C) Apakah secara kasat mata bibit anda bebas  hama & penyakit',
			  IF(
				SeedsFillRoutineMaintenance = 1,
				'Yes',
				IF(
				  SeedsFillRoutineMaintenance = 2,
				  'No',
				  ''
				)
			  ) AS '(C) Apakah anda mengisi lembar catatan perawatan bibit  secara rutin',
			  IF(
				AfterCertSaveRecordOriginSeeds = 1,
				'Yes',
				IF(
				  AfterCertSaveRecordOriginSeeds = 2,
				  'No',
				  ''
				)
			  ) AS '(C) Setelah bergabung dengan program sertifikasi UTZ/RA,  apakah anda menyimpan catatan, sertifikat atau  keterangan tertulis tentang asal bibit kakao anda',
			  ProductionNext AS 'Perkiraan produksi setahun ke depan (kg)',
			  Production AS 'Perkiraan produksi setahun yang lalu (kg)',
			  PanenTrekMonths AS 'Panen trek/lama musim (jumlah bulan)',
			  IF(
				PanenTrekPanenMonth = 0,
				'Tidak Panen',
				IF(
				  PanenTrekPanenMonth = 4,
				  '1 kali/minggu',
				  IF(
					PanenTrekPanenMonth = 2,
					'1 kali/2 minggu',
					IF(
					  PanenTrekPanenMonth = 1,
					  '1 kali/bulan',
					  ''
					)
				  )
				)
			  ) AS 'Panen trek/interval panen',
			  PanenTrekKg AS 'Panen trek (kg/panen)',
			  PanenBiasaMonths AS 'Panen biasa/lama musin (jumlah bulan)',
			  IF(
				PanenBiasaPanenMonth = 0,
				'Tidak Panen',
				IF(
				  PanenBiasaPanenMonth = 4,
				  '1 kali/minggu',
				  IF(
					PanenBiasaPanenMonth = 2,
					'1 kali/2 minggu',
					IF(
					  PanenBiasaPanenMonth = 1,
					  '1 kali/bulan',
					  ''
					)
				  )
				)
			  ) AS 'Panen biasa/interval panen',
			  PanenBiasaKg AS 'Panen biasa (kg/panen)',
			  PanenRayaMonths AS 'Panen raya/lama musin (jumlah bulan)',
			  IF(
				PanenRayaPanenMonth = 0,
				'Tidak Panen',
				IF(
				  PanenRayaPanenMonth = 4,
				  '1 kali/minggu',
				  IF(
					PanenRayaPanenMonth = 2,
					'1 kali/2 minggu',
					IF(
					  PanenRayaPanenMonth = 1,
					  '1 kali/bulan',
					  ''
					)
				  )
				)
			  ) AS 'Panen raya/interval panen',
			  PanenRayaKg AS 'Panen raya (kg/panen)',
			  SalesLastyear AS 'Penjualan dari hasil setahun yang lalu (kg)',
			  a.`Comment` AS 'Komentar',
			  IF(
				HarvestAwal = 1,
				'Yes',
				IF(HarvestAwal = 2, 'No', '')
			  ) AS 'Cara panen kakao - Buah masak awal',
			  IF(
				HarvestMasak = 1,
				'Yes',
				IF(HarvestMasak = 2, 'No', '')
			  ) AS 'Cara panen kakao - Buah masak',
			  IF(
				HarvestHama = 1,
				'Yes',
				IF(HarvestHama = 2, 'No', '')
			  ) AS 'Cara panen kakao - Buah terserang H/P',
			  IF(
				HowToCleanSkin = 1,
				'Ditumpuk di kebun kakao',
				IF(
				  HowToCleanSkin = 2,
				  'Ditumpuk diluar kebun',
				  IF(
					HowToCleanSkin = 3,
					'Ditumpuk & ditutup dengan plastik',
					IF(
					  HowToCleanSkin = 4,
					  'Diolah menjadi kompos',
					  IF(
						HowToCleanSkin = 5,
						'Dikuburkan',
						IF(
						  HowToCleanSkin = 6,
						  'Dibakar',
						  IF(
							HowToCleanSkin = 7,
							'Ditumpuk jadi pakan ternak',
							IF(
							  HowToCleanSkin = 8,
							  'Dibuang di sungai',
							  ''
							)
						  )
						)
					  )
					)
				  )
				)
			  ) AS 'Sanitasi  Apa yang anda lakukan pada kulit buah setelah pembelahan',
			  IF(
				HowToDealOrganicAnorganicWaste = 1,
				'Limbah disimpan dan dibuang hanya pada area - area yang ditentukan',
				IF(
				  HowToDealOrganicAnorganicWaste = 2,
				  'Limbah tidak berbahaya digunakan kembali atau didaur ulang manakala mungin',
				  IF(
					HowToDealOrganicAnorganicWaste = 1,
					'Limbah organik digunakan sebagai pupuk',
					''
				  )
				)
			  ) AS '(C) Bagaimana anda menangani limbah organik dan anorganik',
			  IF(
				PruningOptStructure = 1,
				'Yes',
				IF(PruningOptStructure = 2, 'No', '')
			  ) AS 'Dilakukan Pemangkasan tanaman kakao untuk membentuk struktur yang optimal',
			  FrequentPruningOptStructure AS 'Frekuensi pemangkasan (kali/tahun)',
			  HeightPruningOptStructure AS 'Tinggi pemangkasan (meter)',
			  IF(
				PruningBudInfected = 1,
				'Yes',
				IF(PruningBudInfected = 2, 'No', '')
			  ) AS 'Dilakukan Pemangkasan tanaman kakao Pemangkasan tunas atau bagian tanaman yang terinfeksi hama penyakit',
			  FrequentPruningBudInfected AS 'Frekuensi pemangkasan (kali/tahun)',
			  HeightPruningBudInfected AS 'Tinggi pemangkasan (meter)',
			  IF(
				PruningNotProductive = 1,
				'Yes',
				IF(PruningNotProductive = 2, 'No', '')
			  ) AS 'Dilakukan Pemangkasan tanaman kakao Pemangkasan berat untuk tanaman yang tidak produktif',
			  FrequentPruningNotProductive AS 'Frekuensi (kali/tahun)',
			  HeightPruningNotProductive AS 'Tinggi pemangkasan (meter)',
			  IF(
				DisinfectedTools = 1,
				'Yes',
				IF(DisinfectedTools = 2, 'No', '')
			  ) AS '(C) Apakah alat-alat yang anda gunakan selalu disterilkan',
			  IF(
				PruningProtectPlants = 1,
				'Yes',
				IF(PruningProtectPlants = 2, 'No', '')
			  ) AS 'Pemangkasan pohon pelindung',
			  FrequentPruningProtect AS 'Frekuensi Pemangkasan Pohon Pelindung',
			  IF(
				PakaiKompos = 1,
				'Yes',
				IF(PakaiKompos = 2, 'No', '')
			  ) AS 'Apakah anda memakai pupuk kompos dan/atau organik',
			  FrequentFertilizationKompos AS 'Kompos Frekuensi (kali/tahun)',
			  DoseFertilizerKompos AS 'Dosis (kg/pohon/kali)',
			  FrKomposKandang AS 'Pupuk Kandang Frekuensi (kali/tahun)',
			  DoseKomposKandang AS 'Dosis (kg/pohon/kali)',
			  FrKomposCair AS 'Pupuk Cair Frekuensi (kali/tahun)',
			  DoseKomposCair AS 'Dosis (liter/pohon/kali)',
			  FrKomposGranula AS 'Pupuk Granula Frekuensi (kali/tahun)',
			  DoseKomposGranula AS 'Dosis (gram/pohon/kali)',
			  IF(
				KomposTBM = 1,
				'Yes',
				IF(KomposTBM = 2, 'No', '')
			  ) AS 'Pohon mana yang diberi pupuk kompos dan/atau organik - Tanaman Belum Menghasilkan',
			  IF(
				KomposTM = 1,
				'Yes',
				IF(KomposTM = 2, 'No', '')
			  ) AS 'Pohon mana yang diberi pupuk kompos dan/atau organik - Tanaman Menghasilkan',
			  IF(
				KomposTR = 1,
				'Yes',
				IF(KomposTR = 2, 'No', '')
			  ) AS 'Pohon mana yang diberi pupuk kompos dan/atau organik - Tanaman Rusak',
			  IF(
				AvailableOrganicFertilizer = 1,
				'Yes',
				IF(
				  AvailableOrganicFertilizer = 2,
				  'No',
				  ''
				)
			  ) AS '(C) Apakah pupuk organik selalu tersedia dan mudah diperoleh',
			  IF(
				RoutineWatchSoilFertility = 1,
				'Yes',
				IF(
				  RoutineWatchSoilFertility = 2,
				  'No',
				  ''
				)
			  ) AS '(C) Apakah anda secara rutin memantau kesuburan tanah secara visual',
			  IF(
				ImprovePlantFixNitrogenInSoil = 1,
				'Yes',
				IF(
				  ImprovePlantFixNitrogenInSoil = 2,
				  'No',
				  ''
				)
			  ) AS 'Apa yang anda lakukan untuk memperbaiki kesuburan tanah - Menanam tanaman yang dapat memperbaiki unsur nitrogen dalam tanah',
			  IF(
				ImproveApplyPracticeAgroforestry = 1,
				'Yes',
				IF(
				  ImproveApplyPracticeAgroforestry = 2,
				  'No',
				  ''
				)
			  ) AS 'Apa yang anda lakukan untuk memperbaiki kesuburan tanah - Menerapkan praktek agroforestry',
			  IF(
				ImproveFertilizingWithOrganic = 1,
				'Yes',
				IF(
				  ImproveFertilizingWithOrganic = 2,
				  'No',
				  ''
				)
			  ) AS 'Apa yang anda lakukan untuk memperbaiki kesuburan tanah - Melakukan pemupukan dengan pupuk alami/organik',
			  IF(
				ImproveFertilizingWithAnorganic = 1,
				'Yes',
				IF(
				  ImproveFertilizingWithAnorganic = 2,
				  'No',
				  ''
				)
			  ) AS 'Apa yang anda lakukan untuk memperbaiki kesuburan tanah - Melakukan pemupukan dengan pupuk buatan/anorganik',
			  IF(
				ImproveMakeBiopori = 1,
				'Yes',
				IF(ImproveMakeBiopori = 2, 'No', '')
			  ) AS 'Apa yang anda lakukan untuk memperbaiki kesuburan tanah - Membuat biopori',
			  IF(
				ImprovePlantingShadeTrees = 1,
				'Yes',
				IF(
				  ImprovePlantingShadeTrees = 2,
				  'No',
				  ''
				)
			  ) AS 'Apa yang anda lakukan untuk memperbaiki kesuburan tanah - Menanam tanaman pelindung ',
			  IF(
				ImproveUseCoverCrop = 1,
				'Yes',
				IF(ImproveUseCoverCrop = 2, 'No', '')
			  ) AS 'Apa yang anda lakukan untuk memperbaiki kesuburan tanah - Menggunakan tanaman penutup tanah (cover crop)',
			  IF(
				ImproveTerracing = 1,
				'Yes',
				IF(ImproveTerracing = 2, 'No', '')
			  ) AS 'Apa yang anda lakukan untuk memperbaiki kesuburan tanah - Membuat terasering',
			  IF(
				ImproveDoNothing = 1,
				'Yes',
				IF(ImproveDoNothing = 2, 'No', '')
			  ) AS 'Apa yang anda lakukan untuk memperbaiki kesuburan tanah - Tidak melakukan apa-apa',
			  IF(
				TidakMemakaiKimia = 1,
				'Yes',
				IF(TidakMemakaiKimia = 2, 'No', '')
			  ) AS 'Apakah anda di kebun ini memakai pupuk non organik/kimia',
			  FrUrea AS 'Urea Frekuensi (kali/tahun)',
			  DoUrea AS 'Urea Dosis (gram/pohon/kali)',
			  FrZa AS 'ZA Frekuensi (kali/tahun)',
			  DoZa AS 'ZA Dosis (gram/pohon/kali)',
			  FrTsp AS 'TSP Frekuensi (kali/tahun)',
			  DoTsp AS 'TSP Dosis (gram/pohon/kali)',
			  FrNpk AS 'NPK Frekuensi (kali/tahun)',
			  DoNpk AS 'NPK Dosis (gram/pohon/kali)',
			  FrKcl AS 'KCL Frekuensi (kali/tahun)',
			  DoKcl AS 'KCL Dosis (gram/pohon/kali)',
			  FrFoliar AS 'Foliar Frekuensi (kali/tahun)',
			  DoFoliar AS 'Foliar Dosis (gram/pohon/kali)',
			  IF(
				PupukTBM = 1,
				'Yes',
				IF(PupukTBM = 2, 'No', '')
			  ) AS 'Pohon mana yang dipupuk tidak organik/kimia - Tanaman Belum Menghasilkan',
			  IF(
				PupukTM = 1,
				'Yes',
				IF(PupukTM = 2, 'No', '')
			  ) AS 'Pohon mana yang dipupuk tidak organik/kimia - Tanaman Menghasilkan',
			  IF(
				PupukTR = 1,
				'Yes',
				IF(PupukTR = 2, 'No', '')
			  ) AS 'Pohon mana yang dipupuk tidak organik/kimia - Tanaman Rusak',
			  IF(
				KimiaDana = 1,
				'Yes',
				IF(KimiaDana = 2, 'No', '')
			  ) AS 'Jika tidak memakai pupuk non organik, kenapa - Tidak ada dana',
			  IF(
				KimiaSupplier = 1,
				'Yes',
				IF(KimiaSupplier = 2, 'No', '')
			  ) AS 'Jika tidak memakai pupuk non organik, kenapa - Tidak menemukan supplier',
			  IF(
				KimiaDilatih = 1,
				'Yes',
				IF(KimiaDilatih = 2, 'No', '')
			  ) AS 'Jika tidak memakai pupuk non organik, kenapa - Belum dilatih',
			  IF(
				KimiaTidakSuka = 1,
				'Yes',
				IF(KimiaTidakSuka = 2, 'No', '')
			  ) AS 'Jika tidak memakai pupuk non organik, kenapa - Tidak suka menggunakan pupuk kimia',
			  IF(
				KimiaTidakTersedia = 1,
				'Yes',
				IF(KimiaTidakTersedia = 2, 'No', '')
			  ) AS 'Jika tidak memakai pupuk non organik, kenapa - Pupuk tidak tersedia',
			  IF(
				KimiaLain = 1,
				'Yes',
				IF(KimiaLain = 2, 'No', '')
			  ) AS 'Jika tidak memakai pupuk non organik, kenapa - Lain-lain',
			  IF(
				HamaBPK = 1,
				'Yes',
				IF(HamaBPK = 2, 'No', '')
			  ) AS 'Hama Utama Kakao - Penggerek Buah Kakao',
			  IF(
				HamaHelopeltis = 1,
				'Yes',
				IF(HamaHelopeltis = 2, 'No', '')
			  ) AS 'Hama Utama Kakao - Helopeltis',
			  IF(
				HamaBatang = 1,
				'Yes',
				IF(HamaBatang = 2, 'No', '')
			  ) AS 'Hama Utama Kakao - Penggerek batang atau ranting',
			  IF(
				PenyakitKanker = 1,
				'Yes',
				IF(PenyakitKanker = 2, 'No', '')
			  ) AS 'Penyakit Utama Kakao - Kanker Batang',
			  IF(
				PenyakitBusuk = 1,
				'Yes',
				IF(PenyakitBusuk = 2, 'No', '')
			  ) AS 'Penyakit Utama Kakao - Busuk Buah',
			  IF(
				PenyakitUpas = 1,
				'Yes',
				IF(PenyakitUpas = 2, 'No', '')
			  ) AS 'Penyakit Utama Kakao - Jamur Upas',
			  IF(
				PenyakitAkar = 1,
				'Yes',
				IF(PenyakitAkar = 2, 'No', '')
			  ) AS 'Penyakit Utama Kakao - Jamur Akar',
			  IF(
				PenyakitVSD = 1,
				'Yes',
				IF(PenyakitVSD = 2, 'No', '')
			  ) AS 'Penyakit Utama Kakao - VSD',
			  IF(
				PenyakitAntraknose = 1,
				'Yes',
				IF(PenyakitAntraknose = 2, 'No', '')
			  ) AS 'Penyakit Utama Kakao - Antraknose',
			  IF(
				RoutineMonitorPestInGarden = 1,
				'Yes',
				IF(
				  RoutineMonitorPestInGarden = 2,
				  'No',
				  ''
				)
			  ) AS '(C) Apakah pemantauan hama dan penyakit rutin anda lakukan di kebun ini',
			  IF(
				Herbisida,
				'Yes',
				IF(Herbisida, 'No', '')
			  ) AS 'Apakah Anda menggunakan herbisida',
			  FrequentHerbisida AS 'Herbisida Frekuensi (kali/tahun)',
			  DoseHerbisida AS 'Herbisida Dosis (ml/kali/kebun)',
			  IF(
				Herbisida14 = 1,
				'Yes',
				IF(Herbisida14 = 2, 'No', '')
			  ) AS 'Bimastar',
			  IF(
				Herbisida12 = 1,
				'Yes',
				IF(Herbisida12 = 2, 'No', '')
			  ) AS 'Bravo-xone',
			  IF(
				Herbisida22 = 1,
				'Yes',
				IF(Herbisida22 = 2, 'No', '')
			  ) AS 'DMA',
			  IF(
				Herbisida24 = 1,
				'Yes',
				IF(Herbisida24 = 2, 'No', '')
			  ) AS 'Konup',
			  IF(
				Herbisida26 = 1,
				'Yes',
				IF(Herbisida26 = 2, 'No', '')
			  ) AS 'Mupxone',
			  IF(
				Herbisida10 = 1,
				'Yes',
				IF(Herbisida10 = 2, 'No', '')
			  ) AS 'Noxone',
			  IF(
				Herbisida20 = 1,
				'Yes',
				IF(Herbisida20 = 2, 'No', '')
			  ) AS 'Prima Up',
			  IF(
				Herbisida16 = 1,
				'Yes',
				IF(Herbisida16 = 2, 'No', '')
			  ) AS 'Primastar',
			  IF(
				Herbisida8 = 1,
				'Yes',
				IF(Herbisida8 = 2, 'No', '')
			  ) AS 'Rambo',
			  IF(
				Herbisida28 = 1,
				'Yes',
				IF(Herbisida28 = 2, 'No', '')
			  ) AS 'Senus',
			  IF(
				Herbisida18 = 1,
				'Yes',
				IF(Herbisida18 = 2, 'No', '')
			  ) AS 'Supretox',
			  IF(
				Herbisida2 = 1,
				'Yes',
				IF(Herbisida2 = 2, 'No', '')
			  ) AS 'Basmilang',
			  IF(
				Herbisida5 = 1,
				'Yes',
				IF(Herbisida5 = 2, 'No', '')
			  ) AS 'Gramo-xone',
			  IF(
				Herbisida25 = 1,
				'Yes',
				IF(Herbisida25 = 2, 'No', '')
			  ) AS 'Herbatop',
			  IF(
				Herbisida19 = 1,
				'Yes',
				IF(Herbisida19 = 2, 'No', '')
			  ) AS 'Kleenup',
			  IF(
				Herbisida9 = 1,
				'Yes',
				IF(Herbisida9 = 2, 'No', '')
			  ) AS 'Para  Special',
			  IF(
				Herbisida11 = 1,
				'Yes',
				IF(Herbisida11 = 2, 'No', '')
			  ) AS 'Paratop',
			  IF(
				Herbisida3 = 1,
				'Yes',
				IF(Herbisida3 = 2, 'No', '')
			  ) AS 'Pilar Up',
			  IF(
				Herbisida27 = 1,
				'Yes',
				IF(Herbisida27 = 2, 'No', '')
			  ) AS 'Pointer',
			  IF(
				Herbisida15 = 1,
				'Yes',
				IF(Herbisida15 = 2, 'No', '')
			  ) AS 'Polado',
			  IF(
				Herbisida23 = 1,
				'Yes',
				IF(Herbisida23 = 2, 'No', '')
			  ) AS 'Polaris',
			  IF(
				Herbisida13 = 1,
				'Yes',
				IF(Herbisida13 = 2, 'No', '')
			  ) AS 'Primaxone',
			  IF(
				Herbisida1 = 1,
				'Yes',
				IF(Herbisida1 = 2, 'No', '')
			  ) AS 'Round Up',
			  IF(
				Herbisida17 = 1,
				'Yes',
				IF(Herbisida17 = 2, 'No', '')
			  ) AS 'Rumat',
			  IF(
				Herbisida7 = 1,
				'Yes',
				IF(Herbisida7 = 2, 'No', '')
			  ) AS 'Sapurata',
			  IF(
				Herbisida4 = 1,
				'Yes',
				IF(Herbisida4 = 2, 'No', '')
			  ) AS 'Sun Up',
			  IF(
				Herbisida6 = 1,
				'Yes',
				IF(Herbisida6 = 2, 'No', '')
			  ) AS 'Supremo',
			  IF(
				Herbisida29 = 1,
				'Yes',
				IF(Herbisida29 = 2, 'No', '')
			  ) AS 'Tamaxon',
			  IF(
				Herbisida21 = 1,
				'Yes',
				IF(Herbisida21 = 2, 'No', '')
			  ) AS 'Tanistar',
			  MerekHerbisida AS 'Herbisida Merk Lainnya',
			  IF(
				Insectisida = 1,
				'Yes',
				IF(Insectisida = 2, 'No', '')
			  ) AS 'Apakah Anda menggunakan insektisida',
			  FrequentInsectisida AS 'Insektisida Frekuensi (kali/tahun)',
			  DoseInsectisida AS 'Insektisida Dosis (ml/kali/kebun)',
			  IF(
				Insectisida1 = 1,
				'Yes',
				IF(Insectisida1 = 2, 'No', '')
			  ) AS 'Alika',
			  IF(
				Insectisida16 = 1,
				'Yes',
				IF(Insectisida16 = 2, 'No', '')
			  ) AS 'Arrivo',
			  IF(
				Insectisida18 = 1,
				'Yes',
				IF(Insectisida18 = 2, 'No', '')
			  ) AS 'Bestox',
			  IF(
				Insectisida21 = 1,
				'Yes',
				IF(Insectisida21 = 2, 'No', '')
			  ) AS 'Buldok',
			  IF(
				Insectisida3 = 1,
				'Yes',
				IF(Insectisida3 = 2, 'No', '')
			  ) AS 'Capture',
			  IF(
				Insectisida4 = 1,
				'Yes',
				IF(Insectisida4 = 2, 'No', '')
			  ) AS 'Bento',
			  IF(
				Insectisida5 = 1,
				'Yes',
				IF(Insectisida5 = 2, 'No', '')
			  ) AS 'Regent',
			  IF(
				Insectisida9 = 1,
				'Yes',
				IF(Insectisida9 = 2, 'No', '')
			  ) AS 'Chlormite',
			  IF(
				Insectisida20 = 1,
				'Yes',
				IF(Insectisida20 = 2, 'No', '')
			  ) AS 'Dangke',
			  IF(
				Insectisida10 = 1,
				'Yes',
				IF(Insectisida10 = 2, 'No', '')
			  ) AS 'Decis',
			  IF(
				Insectisida15 = 1,
				'Yes',
				IF(Insectisida15 = 2, 'No', '')
			  ) AS 'Deicer 505',
			  IF(
				Insectisida6 = 1,
				'Yes',
				IF(Insectisida6 = 2, 'No', '')
			  ) AS 'Drusban',
			  IF(
				Insectisida19 = 1,
				'Yes',
				IF(Insectisida19 = 2, 'No', '')
			  ) AS 'Halona',
			  IF(
				Insectisida12 = 1,
				'Yes',
				IF(Insectisida12 = 2, 'No', '')
			  ) AS 'Klensect',
			  IF(
				Insectisida22 = 1,
				'Yes',
				IF(Insectisida22 = 2, 'No', '')
			  ) AS 'Laser',
			  IF(
				Insectisida2 = 1,
				'Yes',
				IF(Insectisida2 = 2, 'No', '')
			  ) AS 'Matador',
			  IF(
				Insectisida8 = 1,
				'Yes',
				IF(Insectisida8 = 2, 'No', '')
			  ) AS 'Nurelle',
			  IF(
				Insectisida11 = 1,
				'Yes',
				IF(Insectisida11 = 2, 'No', '')
			  ) AS 'Organik',
			  IF(
				Insectisida7 = 1,
				'Yes',
				IF(Insectisida7 = 2, 'No', '')
			  ) AS 'Penalty',
			  IF(
				Insectisida23 = 1,
				'Yes',
				IF(Insectisida23 = 2, 'No', '')
			  ) AS 'Sevin',
			  IF(
				Insectisida17 = 1,
				'Yes',
				IF(Insectisida17 = 2, 'No', '')
			  ) AS 'Sidame-thrin',
			  IF(
				Insectisida14 = 1,
				'Yes',
				IF(Insectisida14 = 2, 'No', '')
			  ) AS 'Unicide',
			  IF(
				Insectisida13 = 1,
				'Yes',
				IF(Insectisida13 = 2, 'No', '')
			  ) AS 'Vigor',
			  MerekInsectisida AS 'Insektisida Merk Lainnya',
			  IF(
				Fungisida = 1,
				'Yes',
				IF(Fungisida = 2, 'No', '')
			  ) AS 'Apakah Anda menggunakan fungisida',
			  FrequentFungisida AS 'Fungisida Frekuensi (kali/tahun)',
			  DoseFungisida AS 'Fungisida Dosis (ml/kali/kebun)',
			  IF(
				Fungisida3 = 1,
				'Yes',
				IF(Fungisida3 = 2, 'No', '')
			  ) AS 'Amistar-top',
			  IF(
				Fungisida6 = 1,
				'Yes',
				IF(Fungisida6 = 2, 'No', '')
			  ) AS 'Antila',
			  IF(
				Fungisida7 = 1,
				'Yes',
				IF(Fungisida7 = 2, 'No', '')
			  ) AS 'Antracol',
			  IF(
				Fungisida13 = 1,
				'Yes',
				IF(Fungisida13 = 2, 'No', '')
			  ) AS 'Benhasil',
			  IF(
				Fungisida10 = 1,
				'Yes',
				IF(Fungisida10 = 2, 'No', '')
			  ) AS 'Cozeb',
			  IF(
				Fungisida2 = 1,
				'Yes',
				IF(Fungisida2 = 2, 'No', '')
			  ) AS 'Dithane',
			  IF(
				Fungisida1 = 1,
				'Yes',
				IF(Fungisida1 = 2, 'No', '')
			  ) AS 'Nordox',
			  IF(
				Fungisida11 = 1,
				'Yes',
				IF(Fungisida11 = 2, 'No', '')
			  ) AS 'Fungicide-Organik',
			  IF(
				Fungisida9 = 1,
				'Yes',
				IF(Fungisida9 = 2, 'No', '')
			  ) AS 'Polydor',
			  IF(
				Fungisida12 = 1,
				'Yes',
				IF(Fungisida12 = 2, 'No', '')
			  ) AS 'Rabbat',
			  IF(
				Fungisida5 = 1,
				'Yes',
				IF(Fungisida5 = 2, 'No', '')
			  ) AS 'Rhidomil',
			  IF(
				Fungisida4 = 1,
				'Yes',
				IF(Fungisida4 = 2, 'No', '')
			  ) AS 'Scorpio',
			  MerekFungisida AS 'Fungisida Merk Lainnya',
			  IF(
				UseChemicalPesticideDosage = 1,
				'Yes',
				IF(
				  UseChemicalPesticideDosage = 2,
				  'No',
				  ''
				)
			  ) AS '(C) Apakah Anda menggunakan pestisida kimia sesuai dengan dosis yang dianjurkan',
			  IF(
				ApplyAltNonChemicalControlPests = 1,
				'Yes',
				IF(
				  ApplyAltNonChemicalControlPests = 2,
				  'No',
				  ''
				)
			  ) AS '(C) Apakah Anda menerapkan cara alternatif non-kimia untuk mengendalikan hama & penyakit',
			  IF(
				UseOrganicControlPests = 1,
				'Yes',
				IF(
				  UseOrganicControlPests = 2,
				  'No',
				  ''
				)
			  ) AS '(C) Apakah Anda menggunakan pestisida alami untuk mengendalikan hama dan penyakit',
			  IF(
				UseChemicalLowestToxicity = 1,
				'Yes',
				IF(
				  UseChemicalLowestToxicity = 2,
				  'No',
				  ''
				)
			  ) AS '(C) Apakah Anda selalu menggunakan pestisida kimia yang memiliki kadar racun terendah',
			  IF(
				UseChemicalLastChoice = 1,
				'Yes',
				IF(UseChemicalLastChoice = 2, 'No', '')
			  ) AS '(C) Apakah pestisida kimia hanya Anda gunakan sebagai pilihan terakhir',
			  IF(
				ApplyRotationStrategy = 1,
				'Yes',
				IF(ApplyRotationStrategy = 2, 'No', '')
			  ) AS '(C) Apakah Anda menerapkan strategi rotasi pada penggunaan pestisida kimia',
			  IF(
				NoticeUseInorganicFertilizer = 1,
				'Yes',
				IF(
				  NoticeUseInorganicFertilizer = 2,
				  'No',
				  ''
				)
			  ) AS '(C) Apakah Anda mencatat penggunaan pestisida dan pupuk anorganik',
			  IF(
				TrainedUseProperly = 1,
				'Yes',
				IF(TrainedUseProperly = 2, 'No', '')
			  ) AS '(C) Apakah Anda sudah dilatih untuk menggunakan pestisida dengan tepat dan aman',
			  IF(
				MixPesticideLiquidFertilizer = 1,
				'Yes',
				IF(
				  MixPesticideLiquidFertilizer = 2,
				  'No',
				  ''
				)
			  ) AS '(C) Apakah ketika Anda menyiapkan dan mencampur pestisida dan pupuk cair sesuai dengan petunjuk dosis dan keamanan pada label',
			  IF(
				ExcessPesticideDisposedSafely = 1,
				'Yes',
				IF(
				  ExcessPesticideDisposedSafely = 2,
				  'No',
				  ''
				)
			  ) AS '(C) Apakah kelebihan campuran pestisida, pupuk cair atau limbah pencucian tangki dibuang dengan aman sesuai standar internal kelompok',
			  IF(
				GiveNoEntrySignAfterSpraying = 1,
				'Yes',
				IF(
				  GiveNoEntrySignAfterSpraying = 2,
				  'No',
				  ''
				)
			  ) AS '(C) Apakah Anda memberi tanda dilarang masuk atau mematuhi waktu masuk kembali ke kebun setelah penyemprotan pestisida',
			  IF(
				AdherePreHarvestInterval = 1,
				'Yes',
				IF(
				  AdherePreHarvestInterval = 2,
				  'No',
				  ''
				)
			  ) AS '(C) Apakah Anda mematuhi jeda waktu pra-panen yang direkomendasikan untuk seluruh pestisida  yang digunakan',
			  IF(
				EquipmentGoodCondition = 1,
				'Yes',
				IF(
				  EquipmentGoodCondition = 2,
				  'No',
				  ''
				)
			  ) AS '(C) Apakah seluruh perlengkapan yang digunakan untuk pemberian pupuk dan pestisida dalam kondisi yang baik dan berfungsi sebagaimana mestinya',
			  IF(
				StoreAccordanceOnLabel = 1,
				'Yes',
				IF(
				  StoreAccordanceOnLabel = 2,
				  'No',
				  ''
				)
			  ) AS '(C) Pestisida dan pupuk anorganik disimpan dengan cara - Sesuai dengan petunjuk pada label',
			  IF(
				StoreOriginalPackaging = 1,
				'Yes',
				IF(
				  StoreOriginalPackaging = 2,
				  'No',
				  ''
				)
			  ) AS '(C) Pestisida dan pupuk anorganik disimpan dengan cara - Dalam wadah atau kemasan asli',
			  IF(
				StoreIndicationSuitablePlants = 1,
				'Yes',
				IF(
				  StoreIndicationSuitablePlants = 2,
				  'No',
				  ''
				)
			  ) AS '(C) Pestisida dan pupuk anorganik disimpan dengan cara - Dengan indikasi jenis tanaman yang sesuai dengan penggunaannya',
			  IF(
				StoreAvoidPossibleSpill = 1,
				'Yes',
				IF(
				  StoreAvoidPossibleSpill = 2,
				  'No',
				  ''
				)
			  ) AS '(C) Pestisida dan pupuk anorganik disimpan dengan cara - Terhindar dari kemungkinan tumpah',
			  IF(
				StoreSecuredPlace = 1,
				'Yes',
				IF(StoreSecuredPlace = 2, 'No', '')
			  ) AS '(C) Pestisida dan pupuk anorganik disimpan dengan cara - Diamankan ditempat yang tidak bisa diakses anak anak',
			  IF(
				StoreFarFromProducts = 1,
				'Yes',
				IF(StoreFarFromProducts = 2, 'No', '')
			  ) AS '(C) Pestisida dan pupuk anorganik disimpan dengan cara - Jauh dari produk yang dipanen, alat-alat, materi kemasan, dan produk-produk makanan',
			  IF(
				HandlingCleanDry = 1,
				'Yes',
				IF(HandlingCleanDry = 2, 'No', '')
			  ) AS '(C) Bagaimana keadaan fasilitas untuk penanganan, pelarutan dan penyimpanan  pestisida dan pupuk anorganik - Bersih dan kering',
			  IF(
				HandlingEnoughVentilationLight = 1,
				'Yes',
				IF(
				  HandlingEnoughVentilationLight = 2,
				  'No',
				  ''
				)
			  ) AS '(C) Bagaimana keadaan fasilitas untuk penanganan, pelarutan dan penyimpanan  pestisida dan pupuk anorganik - Cukup ventilasi dan cahaya',
			  IF(
				HandlingStructurallySafe = 1,
				'Yes',
				IF(
				  HandlingStructurallySafe = 2,
				  'No',
				  ''
				)
			  ) AS '(C) Bagaimana keadaan fasilitas untuk penanganan, pelarutan dan penyimpanan  pestisida dan pupuk anorganik - Secara struktur aman',
			  IF(
				HandlingAntiAbsorptive = 1,
				'Yes',
				IF(
				  HandlingAntiAbsorptive = 2,
				  'No',
				  ''
				)
			  ) AS '(C) Bagaimana keadaan fasilitas untuk penanganan, pelarutan dan penyimpanan  pestisida dan pupuk anorganik - Dilengkapi dengan bahan anti serap',
			  IF(
				HandlingLeakproofedFloor = 1,
				'Yes',
				IF(
				  HandlingLeakproofedFloor = 2,
				  'No',
				  ''
				)
			  ) AS '(C) Bagaimana keadaan fasilitas untuk penanganan, pelarutan dan penyimpanan  pestisida dan pupuk anorganik - Lantai yang kedap suara dan anti rembes',
			  IF(
				HandlingFireproofMaterial = 1,
				'Yes',
				IF(
				  HandlingFireproofMaterial = 2,
				  'No',
				  ''
				)
			  ) AS '(C) Bagaimana keadaan fasilitas untuk penanganan, pelarutan dan penyimpanan  pestisida dan pupuk anorganik - Rak-rak yang bersifat anti-serap dan bermateri tahan api',
			  IF(
				HandlingCollectSpillage = 1,
				'Yes',
				IF(
				  HandlingCollectSpillage = 2,
				  'No',
				  ''
				)
			  ) AS '(C) Bagaimana keadaan fasilitas untuk penanganan, pelarutan dan penyimpanan  pestisida dan pupuk anorganik - Terdapat sebuah sistem untuk menampung tumpahan',
			  IF(
				HandlingClearWarningSign = 1,
				'Yes',
				IF(
				  HandlingClearWarningSign = 2,
				  'No',
				  ''
				)
			  ) AS '(C) Bagaimana keadaan fasilitas untuk penanganan, pelarutan dan penyimpanan  pestisida dan pupuk anorganik - Terdapat tanda peringatan yang jelas dan permanen ada di dekat pintu masuk',
			  IF(
				HandlingFirstAidInfo = 1,
				'Yes',
				IF(HandlingFirstAidInfo = 2, 'No', '')
			  ) AS '(C) Bagaimana keadaan fasilitas untuk penanganan, pelarutan dan penyimpanan  pestisida dan pupuk anorganik - Terdapat peringatan keselamatan yang kelihatan, lambang-lambang  peringatan, gejala keracunan, dan informasi pertolongan pertama untuk setiap  produk yang disimpan',
			  IF(
				HandlingProcedureEmergency = 1,
				'Yes',
				IF(
				  HandlingProcedureEmergency = 2,
				  'No',
				  ''
				)
			  ) AS '(C) Bagaimana keadaan fasilitas untuk penanganan, pelarutan dan penyimpanan  pestisida dan pupuk anorganik - Terdapat tata cara keadaan darurat yang jelas',
			  IF(
				HandlingAreaCleanEye = 1,
				'Yes',
				IF(HandlingAreaCleanEye = 2, 'No', '')
			  ) AS '(C) Bagaimana keadaan fasilitas untuk penanganan, pelarutan dan penyimpanan  pestisida dan pupuk anorganik - Terdapat area untuk membersihkan mata',
			  IF(
				HandlingAccommodateLiquidStored = 1,
				'Yes',
				IF(
				  HandlingAccommodateLiquidStored = 2,
				  'No',
				  ''
				)
			  ) AS '(C) Bagaimana keadaan fasilitas untuk penanganan, pelarutan dan penyimpanan  pestisida dan pupuk anorganik - Fasilitas diberi pembatas dan mampu menampung 110% dari seluruh volume  cair yang disimpan',
			  IF(APD = 1, 'Yes', IF(APD = 2, 'No', '')) AS 'Apakah Anda menggunakan Pakaian Perlindungan Diri (PPD)',
			  IF(
				TempatSimpanPestisida = 1,
				'Didalam rumah',
				IF(
				  TempatSimpanPestisida = 2,
				  'Tempat khusus pestisida',
				  IF(
					TempatSimpanPestisida = 3,
					'Diluar rumah (kawasan rumah)',
					IF(
					  TempatSimpanPestisida = 4,
					  'Diluar kebun',
					  IF(
						TempatSimpanPestisida = 5,
						'Lain-lain',
						''
					  )
					)
				  )
				)
			  ) AS 'Dimana Anda menyimpan pestisida sebelum dan selama pemakaian',
			  IF(
				BuangKemasanPestisida = 1,
				'Di buang sembarangan (di kebun atau sekitar rumah)',
				IF(
				  BuangKemasanPestisida = 2,
				  'Digunakan untuk menyimpan sesuatu',
				  IF(
					BuangKemasanPestisida = 3,
					'Dicuci dengan bersih dan dikubur',
					IF(
					  BuangKemasanPestisida = 4,
					  'Dibakar',
					  IF(
						BuangKemasanPestisida = 5,
						'Daur ulang',
						IF(
						  BuangKemasanPestisida = 6,
						  'Lain-lain',
						  ''
						)
					  )
					)
				  )
				)
			  ) AS 'Apa yang Anda lakukan dengan kemasan pestisida setelah pemakaian',
			  IF(
				UsePesticideInorganicFertilizer = 1,
				'Dalam jarak 5 meter dari badan air musiman maupun permanen yang lebarnya 3 meter atau kurang',
				IF(
				  UsePesticideInorganicFertilizer = 2,
				  'Dalam jarak 10 meter dari badan air musiman ataupun permanen yang lebarnya lebih dari 3 meter',
				  IF(
					UsePesticideInorganicFertilizer = 3,
					'Dalam jarak 15 meter dari mata air',
					IF(
					  UsePesticideInorganicFertilizer = 4,
					  'Tidak sesuai poin A, B dan C',
					  ''
					)
				  )
				)
			  ) AS '(C) Apakah Anda menggunakan pestisida dan pupuk anorganik',
			  a.`DateCreated`,
			  h.`UserRealName` AS CreatedBy,
			  a.`DateUpdated`,
			  i.`UserRealName` AS LastModifiedBy
			FROM
			  `ktv_members_garden` a
			  LEFT JOIN ktv_members b
				ON b.`FarmerID` = a.`FarmerID`
			  LEFT JOIN ktv_cpg c
				ON c.`CPGid` = b.`CPGid`
			  LEFT JOIN ktv_village g
				ON g.`VillageID` = b.`VillageID`
			  LEFT JOIN ktv_subdistrict f
				ON f.`SubDistrictID` = g.SubDistrictID
			  LEFT JOIN ktv_district e
				ON e.`DistrictID` = f.DistrictID
			  LEFT JOIN ktv_province d
				ON d.`ProvinceID` = e.ProvinceID
			  LEFT JOIN sys_user h
				ON h.UserId = a.`CreatedBy`
			  LEFT JOIN sys_user i
				ON i.UserId = a.`LastModifiedBy`
			  INNER JOIN ktv_certification_afl_garden afl
				ON afl.`FarmerID` = a.`FarmerID`
				AND afl.`CertSurveyNr` = a.`SurveyNr`
				AND afl.`CertGardenNr` = a.`GardenNr`
			WHERE
				b.`StatusCode` = 'active'
				AND afl.StatusCode = 'active'
				AND afl.IMSID = '{$IMSID}'
				AND afl.CertStatusAudit != '-'
			ORDER BY afl.`FarmerID`"
		;
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getGardenAflP1SummaryFaProgress($IMSID, $UserID)
	{
		$arrReturn = array();

		//target
		$sql = "SELECT
					COUNT(*) AS BANYAK
				FROM
					`ktv_certification_pre_afl_target` tar
					LEFT JOIN ktv_certification_pre_afl_garden aflg ON 1=1
						AND tar.`IMSID` = aflg.`IMSID`
						AND tar.`FarmerID` = aflg.`FarmerID`
				WHERE
					tar.`IMSID` = ?
					AND tar.`PICUserID` = ?";
		$query      = $this->db->query($sql, array($IMSID, $UserID));
		$dataTarget = $query->row_array();

		//realnya sudah dapat berapa
		$sql = "SELECT
					tbl_sub.CAPAI,
					LEAST(
						DATE(IF(tbl_sub.BeginTgl='0000-00-00',tbl_sub.BeginTglUpdated,tbl_sub.BeginTgl)),
						DATE(IF(tbl_sub.BeginTglUpdated='0000-00-00',tbl_sub.BeginTgl,tbl_sub.BeginTglUpdated))
					) AS BeginTgl,
					GREATEST(DATE(tbl_sub.EndTgl),DATE(tbl_sub.EndTglUpdated)) AS EndTgl
				FROM
				(
				SELECT
					COUNT(aflg.`GardenNr`) AS CAPAI,
					DATE(MIN(gar.`DateCreated`)) AS BeginTgl,
					DATE(MIN(gar.`DateUpdated`)) AS BeginTglUpdated,
					DATE(MAX(gar.`DateCreated`)) AS EndTgl,
					DATE(MAX(gar.`DateUpdated`)) AS EndTglUpdated
				FROM
					`ktv_certification_pre_afl_target` tar
					LEFT JOIN ktv_ims ims ON tar.`IMSID` = ims.`IMSID`

					LEFT JOIN ktv_certification_pre_afl_garden aflg ON 1=1
						AND tar.`IMSID` = aflg.`IMSID`
						AND tar.`FarmerID` = aflg.`FarmerID`

					LEFT JOIN ktv_members_garden gar ON 1=1
						AND aflg.`FarmerID` = gar.`FarmerID`
						AND aflg.`SurveyNr` = gar.`SurveyNr`
						AND aflg.`GardenNr` = gar.`GardenNr`
						AND (
							DATE(gar.`DateUpdated`) >= DATE_FORMAT( ims.`CertEventDate` - INTERVAL 6 MONTH, '%Y/%m/%d' )
							OR
							DATE(gar.`DateCreated`) >= DATE_FORMAT( ims.`CertEventDate` - INTERVAL 6 MONTH, '%Y/%m/%d' )
						)
				WHERE
					tar.`IMSID` = ?
					AND tar.`PICUserID` = ?
					AND gar.GardenNr IS NOT NULL
				ORDER BY tar.`FarmerID` ASC
				) AS tbl_sub";
		$query     = $this->db->query($sql, array($IMSID, $UserID));
		$dataCapai = $query->row_array();

		//cek apakah data tanggal rangenya ada
		if ($dataCapai['BeginTgl'] != "0000-00-00" && $dataCapai['BeginTgl'] != "" && $dataCapai['EndTgl'] != "0000-00-00" && $dataCapai['EndTgl'] != "") {
			//progress data by tanggal
			$data      = array();
			$dateRange = $this->getDateRange($dataCapai['BeginTgl'], $dataCapai['EndTgl'], '+1 day', 'Y-m-d');
			foreach ($dateRange as $key => $tglProses) {
				$data[$key]['tanggal'] = $tglProses;

				//query capainya perhari
				$sql = "SELECT
						COUNT(aflg.`GardenNr`) AS CAPAI
					FROM
						`ktv_certification_pre_afl_target` tar
						LEFT JOIN ktv_ims ims ON tar.`IMSID` = ims.`IMSID`

						LEFT JOIN ktv_certification_pre_afl_garden aflg ON 1=1
							AND tar.`IMSID` = aflg.`IMSID`
							AND tar.`FarmerID` = aflg.`FarmerID`

						LEFT JOIN ktv_members_garden gar ON 1=1
							AND aflg.`FarmerID` = gar.`FarmerID`
							AND aflg.`SurveyNr` = gar.`SurveyNr`
							AND aflg.`GardenNr` = gar.`GardenNr`
							AND (
								DATE(gar.`DateUpdated`) >= DATE_FORMAT( ims.`CertEventDate` - INTERVAL 6 MONTH, '%Y/%m/%d' )
								OR
								DATE(gar.`DateCreated`) >= DATE_FORMAT( ims.`CertEventDate` - INTERVAL 6 MONTH, '%Y/%m/%d' )
							)
					WHERE
						tar.`IMSID` = ?
						AND tar.`PICUserID` = ?
						AND gar.GardenNr IS NOT NULL
						AND (
							DATE(gar.`DateCreated`) = ?
							OR
							DATE(gar.`DateUpdated`) = ?
						)
					ORDER BY tar.`FarmerID` ASC";
				$query       = $this->db->query($sql, array($IMSID, $UserID, $tglProses, $tglProses));
				$dataPerHari = $query->row_array();

				$data[$key]['capai'] = $dataPerHari['CAPAI'];
			}
		} else {
			$data = array();
		}

		$arrReturn['dataList'] = $data;
		$arrReturn['capai']    = $dataCapai['CAPAI'];
		$arrReturn['target']   = $dataTarget['BANYAK'];
		return $arrReturn;
	}

	public function getGardenAflP1SummaryFa($IMSID, $UserID)
	{
		$arrReturn = array();

		$sql = "SELECT
					b.`FarmerID` AS 'ID Petani',
					b.ExtFarmerID AS 'ID Petani Eksternal',
					b.`FarmerName` AS 'Nama Petani',
					c.`GroupName` AS 'Kelompok Tani',
					d.`Province` AS 'Propinsi',
					e.`District` AS 'Kabupaten',
					f.`SubDistrict` AS 'Kecamatan',
					g.`Village` AS 'Desa',
					aflg.`GardenNr` AS 'Nr Kebun',
					aflg.`SurveyNr` AS 'Nr Survey',

					gar.`DateCollection` AS 'Tgl Interview',
					IF(
						RoadCondition = 1,
						'Jalan Aspal',
						IF(
						  RoadCondition = 2,
						  'Jalan Pengerasan',
						  IF(
							RoadCondition = 3,
							'Jalan Tanah',
							IF(
							  RoadCondition = 4,
							  'Tidak ada Jalan',
							  ''
							)
						  )
						)
					  ) AS 'Kondisi jalan ke kebun kakao',
					  IF(
						OwnershipCocoa = 1,
						'Pemilik Penggarap',
						IF(
						  OwnershipCocoa = 2,
						  'Profit Sharing',
						  IF(
							OwnershipCocoa = 3,
							'Petani Penyewa',
							IF(OwnershipCocoa = 4, 'Lain-lain', '')
						  )
						)
					  ) AS 'Status kepemilikan tanah',
					  IF(
						LandOwner = 1,
						'Saya Sendiri',
						IF(
						  LandOwner = 2,
						  'Anggota Keluarga',
						  IF(
							LandOwner = 3,
							'Orang Lain',
							IF(LandOwner = 4, 'Tidak Tahu', '')
						  )
						)
					  ) AS 'Pemilik tanah',
					  IF(
						LandCertificate = 1,
						'Tidak Ada',
						IF(
						  LandCertificate = 2,
						  'Akte Notaris/BPN',
						  IF(
							LandCertificate = 3,
							'KKT (Camat)',
							IF(
							  LandCertificate = 4,
							  'Desa/Lurah',
							  IF(
								LandCertificate = 5,
								'Tidak Tahu',
								''
							  )
							)
						  )
						)
					  ) AS 'Serfitikat kepemilikan tanah',
					  GardenDistance AS 'Jarak rumah ke kebun kakao (m)',
					  gar.`Latitude`,
					  gar.`Longitude`,
					  gar.`Elevation`,
					  GardenHaUnCertified AS 'Ukuran kebun (Ha)',
					  GardenAreaCoordinates AS 'Koordinat area kebun',
					  IF(
						GardenLandUse = 1,
						'Converted Forest',
						IF(
						  GardenLandUse = 2,
						  'Limited Forest',
						  IF(
							GardenLandUse = 3,
							'Production Forest',
							IF(
							  GardenLandUse = 4,
							  'Protected Forest',
							  IF(
								GardenLandUse = 5,
								'Unspecified Area',
								''
							  )
							)
						  )
						)
					  ) AS 'Pengggunaan Lahan',
					  TahunTanamanCocoa AS 'Tahun tanam kakao',
					  PohonTBM AS 'Jumlah tanaman belum menghasilkan (pohon)',
					  PohonTM AS 'Jumlah tanaman menghasilkan (pohon)',
					  PohonRehab AS 'Jumlah tanaman rusak (pohon)',
					  GraftedTrees AS 'Jumlah pohon sambung samping/sambung pucuk tunas air ',
					  GraftedTreesTahun AS 'Tahun tanam pohon sambung samping/sambung pucuk tunas air ',
					  TopGraftedTrees AS 'Jumlah penanaman ulang dari sambung pucuk dan biji',
					  TopGraftedTreesTahun AS 'Tahun tanam pohon pengan penanaman ulang dari sambung pucuk dan biji',
					  ReplantedTrees AS 'Jumlah pohon dengan penanaman ulang dan sisipan',
					  ReplantedTreesTahun AS 'Tahun tanam pohon dengan penanaman ulang dan sisipan',
					  S1Nr AS 'S1',
					  S2Nr AS 'S2',
					  J45Nr AS '45/MCC02',
					  M01Nr AS 'M01',
					  TSH858Nr AS 'TSH 858',
					  ICRRI3Nr AS 'ICCRI3',
					  ICRRI4Nr AS 'ICCRI4',
					  ICRRI5Nr AS 'ICCRI5',
					  RCC70Nr AS 'RCC70',
					  RCC71Nr AS 'RCC71',
					  RCC72Nr AS 'RCC72',
					  RCC73Nr AS 'RCC73',
					  LokalNr AS 'Lokal',
					  RCLNr AS 'RCL',
					  THRNr AS 'THR',
					  APNr AS 'AP',
					  PRNr AS 'PR',
					  ScavinaNr AS 'Scavina',
					  MTNr AS 'MT',
					  M02Nr AS 'M02',
					  M04Nr AS 'M04',
					  M06Nr AS 'M06',
					  MHP03Nr AS 'MHP03',
					  MHP04Nr AS 'MHP04',
					  BB01Nr AS 'BB01',
					  BLBNr AS 'BLB',
					  BRTNr AS 'BRT',
					  CloneLain AS 'Klon Lainnya (sebutkan nama varietas)',
					  CloneLainNr AS 'Jumlah klon lain (pohon)',
					  KelapaNr AS 'Kelapa',
					  PinangNr AS 'Pinang',
					  KaretNr AS 'Karet',
					  CengkehNr AS 'Cengkeh',
					  JambuMenteNr AS 'Jambu Mete',
					  SawitNr AS 'Sawit',
					  ArenNr AS 'Aren',
					  PalaNr AS 'Pala',
					  KemiriNr AS 'Kemiri',
					  KapokNr AS 'Kapuk',
					  MahoniNr AS 'Mahoni',
					  JatiNr AS 'Jati',
					  BitiNr AS 'Biti',
					  UruNr AS 'Uru',
					  JabonNr AS 'Jabon',
					  SengonNr AS 'Sengon',
					  AlpukatNr AS 'Alpukat',
					  PisangNr AS 'Pisang',
					  SukunNr AS 'Sukun',
					  CempedakNr AS 'Cempedak',
					  JerukNr AS 'Jeruk',
					  JambuNr AS 'Fruit Trees-Guava',
					  JackFruitNr AS 'Nangka',
					  LangsatNr AS 'Langsat',
					  ManggaNr AS 'Mangga',
					  ManggisNr AS 'Manggis',
					  PepayaNr AS 'Pepaya',
					  RambutanNr AS 'Rambutan',
					  KedondongNr AS 'Kedondong',
					  DurianNr AS 'Durian',
					  JengkolNr AS 'Jengkol',
					  GamalNr AS 'Gamal',
					  LamtoroNr AS 'Lamtoro',
					  PetaiNr AS 'Petai',
					  ShadeTreesNr AS 'Total pohon pelindung',
					  IF(
						ShadeTreesIncProductivity = 1,
						'Yes',
						IF(
						  ShadeTreesIncProductivity = 2,
						  'No',
						  ''
						)
					  ) AS 'Mengapa anda  menanam pohon  pelindung - Untuk meningkatkan produktivitas tanaman kakao',
					  IF(
						ShadeTreesExtraIncome = 1,
						'Yes',
						IF(ShadeTreesExtraIncome = 2, 'No', '')
					  ) AS 'Mengapa anda  menanam pohon  pelindung - Untuk mendapatkan penghasilan tambahan',
					  IF(
						ShadeTreesProtectSoil = 1,
						'Yes',
						IF(ShadeTreesProtectSoil = 2, 'No', '')
					  ) AS 'Mengapa anda  menanam pohon  pelindung - Untuk melindungi tanah',
					  IF(
						ShadeTreesReducePests = 1,
						'Yes',
						IF(ShadeTreesReducePests = 2, 'No', '')
					  ) AS 'Mengapa anda  menanam pohon  pelindung - Untuk mengurangi serangan hama dan penyakit',
					  IF(
						ShadeTreesReduceHeat = 1,
						'Yes',
						IF(ShadeTreesReduceHeat = 2, 'No', '')
					  ) AS 'Mengapa anda  menanam pohon  pelindung - Untuk mengurangi suhu panas di kebun',
					  IF(
						ShadeTreesIncLandValue = 1,
						'Yes',
						IF(
						  ShadeTreesIncLandValue = 2,
						  'No',
						  ''
						)
					  ) AS 'Mengapa anda  menanam pohon  pelindung - Untuk meningkatkan nilai tanah',
					  IF(
						ShadeTreesAddFirewood = 1,
						'Yes',
						IF(ShadeTreesAddFirewood = 2, 'No', '')
					  ) AS 'Mengapa anda  menanam pohon  pelindung - Untuk menambah sumber kayu bakar',
					  IF(
						ShadeTreesAddFodder = 1,
						'Yes',
						IF(ShadeTreesAddFodder = 2, 'No', '')
					  ) AS 'Mengapa anda  menanam pohon  pelindung - Untuk menambah sumber makanan ternak',
					  IF(
						ShadeTreesDoNotKnow = 1,
						'Yes',
						IF(ShadeTreesDoNotKnow = 2, 'No', '')
					  ) AS 'Mengapa anda  menanam pohon  pelindung - Saya tidak tahu',
					  ShadeTreesOthers AS 'Mengapa anda  menanam pohon  pelindung - Lainnya (Sebutkan)',
					  IF(
						ShadeTreesSpreadEvently = 1,
						'Yes',
						IF(
						  ShadeTreesSpreadEvently = 2,
						  'No',
						  ''
						)
					  ) AS '(C) Apakah pohon penaung tersebar merata di kebun',
					  IF(
						ShadeTreesObtainSeeds = 1,
						'Farmer Group',
						IF(
						  ShadeTreesObtainSeeds = 2,
						  'Cooperatives/IMS',
						  IF(
							ShadeTreesObtainSeeds = 3,
							'Seeds Seller',
							IF(
							  ShadeTreesObtainSeeds = 4,
							  'Make your own',
							  ''
							)
						  )
						)
					  ) AS '(C) Darimana anda mendapatkan bibit pohon penaung',
					  Nuts AS 'Kacang-kacangan',
					  Tubers AS 'Umbi-umbian',
					  Patchouli AS 'Nilam',
					  CoverCropOthers AS 'Tanaman Penutup Lainnya (sebutkan)',
					  IF(
						NoCoverCrop = 1,
						'Yes',
						IF(NoCoverCrop = 2, 'No', '')
					  ) AS 'Tidak Ada Tanaman Penutup Tanah',
					  IF(
						ObtainSeedsToday = 1,
						'Supplier yang direkomendasikan IMS',
						IF(
						  ObtainSeedsToday = 2,
						  'Supplier diluar rekomendasi IMS',
						  IF(
							ObtainSeedsToday = 3,
							'Membuat bibit sendiri',
							''
						  )
						)
					  ) AS '(C) Darimana anda memperoleh bibit saat ini',
					  IF(
						SeedsFreeFromPests = 1,
						'Yes',
						IF(SeedsFreeFromPests = 2, 'No', '')
					  ) AS '(C) Apakah secara kasat mata bibit anda bebas  hama & penyakit',
					  IF(
						SeedsFillRoutineMaintenance = 1,
						'Yes',
						IF(
						  SeedsFillRoutineMaintenance = 2,
						  'No',
						  ''
						)
					  ) AS '(C) Apakah anda mengisi lembar catatan perawatan bibit  secara rutin',
					  IF(
						AfterCertSaveRecordOriginSeeds = 1,
						'Yes',
						IF(
						  AfterCertSaveRecordOriginSeeds = 2,
						  'No',
						  ''
						)
					  ) AS '(C) Setelah bergabung dengan program sertifikasi UTZ/RA,  apakah anda menyimpan catatan, sertifikat atau  keterangan tertulis tentang asal bibit kakao anda',
					  ProductionNext AS 'Perkiraan produksi setahun ke depan (kg)',
					  Production AS 'Perkiraan produksi setahun yang lalu (kg)',
					  PanenTrekMonths AS 'Panen trek/lama musim (jumlah bulan)',
					  IF(
						PanenTrekPanenMonth = 1,
						'Tidak Panen',
						IF(
						  PanenTrekPanenMonth = 2,
						  '1 kali/minggu',
						  IF(
							PanenTrekPanenMonth = 3,
							'1 kali/2 minggu',
							IF(
							  PanenTrekPanenMonth = 4,
							  '1 kali/bulan',
							  ''
							)
						  )
						)
					  ) AS 'Panen trek/interval panen',
					  PanenTrekKg AS 'Panen trek (kg/panen)',
					  PanenBiasaMonths AS 'Panen biasa/lama musin (jumlah bulan)',
					  IF(
						PanenBiasaPanenMonth = 1,
						'Tidak Panen',
						IF(
						  PanenBiasaPanenMonth = 2,
						  '1 kali/minggu',
						  IF(
							PanenBiasaPanenMonth = 3,
							'1 kali/2 minggu',
							IF(
							  PanenBiasaPanenMonth = 4,
							  '1 kali/bulan',
							  ''
							)
						  )
						)
					  ) AS 'Panen biasa/interval panen',
					  PanenBiasaKg AS 'Panen biasa (kg/panen)',
					  PanenRayaMonths AS 'Panen raya/lama musin (jumlah bulan)',
					  IF(
						PanenRayaPanenMonth = 1,
						'Tidak Panen',
						IF(
						  PanenRayaPanenMonth = 2,
						  '1 kali/minggu',
						  IF(
							PanenRayaPanenMonth = 3,
							'1 kali/2 minggu',
							IF(
							  PanenRayaPanenMonth = 4,
							  '1 kali/bulan',
							  ''
							)
						  )
						)
					  ) AS 'Panen raya/interval panen',
					  PanenRayaKg AS 'Panen raya (kg/panen)',
					  SalesLastyear AS 'Penjualan dari hasil setahun yang lalu (kg)',
					  gar.`Comment` AS 'Komentar',
					  IF(
						HarvestAwal = 1,
						'Yes',
						IF(HarvestAwal = 2, 'No', '')
					  ) AS 'Cara panen kakao - Buah masak awal',
					  IF(
						HarvestMasak = 1,
						'Yes',
						IF(HarvestMasak = 2, 'No', '')
					  ) AS 'Cara panen kakao - Buah masak',
					  IF(
						HarvestHama = 1,
						'Yes',
						IF(HarvestHama = 2, 'No', '')
					  ) AS 'Cara panen kakao - Buah terserang H/P',
					  IF(
						HowToCleanSkin = 1,
						'Ditumpuk di kebun kakao',
						IF(
						  HowToCleanSkin = 2,
						  'Ditumpuk diluar kebun',
						  IF(
							HowToCleanSkin = 3,
							'Ditumpuk & ditutup dengan plastik',
							IF(
							  HowToCleanSkin = 4,
							  'Diolah menjadi kompos',
							  IF(
								HowToCleanSkin = 5,
								'Dikuburkan',
								IF(
								  HowToCleanSkin = 6,
								  'Dibakar',
								  IF(
									HowToCleanSkin = 7,
									'Ditumpuk jadi pakan ternak',
									IF(
									  HowToCleanSkin = 8,
									  'Dibuang di sungai',
									  ''
									)
								  )
								)
							  )
							)
						  )
						)
					  ) AS 'Sanitasi Apa yang anda lakukan pada kulit buah setelah pembelahan',
					  IF(
						HowToDealOrganicAnorganicWaste = 1,
						'Limbah disimpan dan dibuang hanya pada area - area yang ditentukan',
						IF(
						  HowToDealOrganicAnorganicWaste = 2,
						  'Limbah tidak berbahaya digunakan kembali atau didaur ulang manakala mungin',
						  IF(
							HowToDealOrganicAnorganicWaste = 1,
							'Limbah organik digunakan sebagai pupuk',
							''
						  )
						)
					  ) AS '(C) Bagaimana anda menangani limbah organik dan anorganik',
					  IF(
						PruningOptStructure = 1,
						'Yes',
						IF(PruningOptStructure = 2, 'No', '')
					  ) AS 'Dilakukan Pemangkasan tanaman kakao untuk membentuk struktur yang optimal',
					  FrequentPruningOptStructure AS 'Frekuensi pemangkasan (kali/tahun)',
					  HeightPruningOptStructure AS 'Tinggi pemangkasan (meter)',
					  IF(
						PruningBudInfected = 1,
						'Yes',
						IF(PruningBudInfected = 2, 'No', '')
					  ) AS 'Dilakukan Pemangkasan tanaman kakao Pemangkasan tunas atau bagian tanaman yang terinfeksi hama penyakit',
					  FrequentPruningBudInfected AS 'Frekuensi pemangkasan (kali/tahun)',
					  HeightPruningBudInfected AS 'Tinggi pemangkasan (meter)',
					  IF(
						PruningNotProductive = 1,
						'Yes',
						IF(PruningNotProductive = 2, 'No', '')
					  ) AS 'Dilakukan Pemangkasan tanaman kakao Pemangkasan berat untuk tanaman yang tidak produktif',
					  FrequentPruningNotProductive AS 'Frekuensi (kali/tahun)',
					  HeightPruningNotProductive AS 'Tinggi pemangkasan (meter)',
					  IF(
						DisinfectedTools = 1,
						'Yes',
						IF(DisinfectedTools = 2, 'No', '')
					  ) AS '(C) Apakah alat-alat yang anda gunakan selalu disterilkan',
					  IF(
						PruningProtectPlants = 1,
						'Yes',
						IF(PruningProtectPlants = 2, 'No', '')
					  ) AS 'Pemangkasan pohon pelindung',
					  FrequentPruningProtect AS 'Frekuensi Pemangkasan Pohon Pelindung',
					  IF(
						PakaiKompos = 1,
						'Yes',
						IF(PakaiKompos = 2, 'No', '')
					  ) AS 'Apakah anda memakai pupuk kompos dan/atau organik',
					  FrequentFertilizationKompos AS 'Kompos Frekuensi (kali/tahun)',
					  DoseFertilizerKompos AS 'Dosis (kg/pohon/kali)',
					  FrKomposKandang AS 'Pupuk Kandang Frekuensi (kali/tahun)',
					  DoseKomposKandang AS 'Dosis (kg/pohon/kali)',
					  FrKomposCair AS 'Pupuk Cair Frekuensi (kali/tahun)',
					  DoseKomposCair AS 'Dosis (liter/pohon/kali)',
					  FrKomposGranula AS 'Pupuk Granula Frekuensi (kali/tahun)',
					  DoseKomposGranula AS 'Dosis (gram/pohon/kali)',
					  IF(
						KomposTBM = 1,
						'Yes',
						IF(KomposTBM = 2, 'No', '')
					  ) AS 'Pohon mana yang diberi pupuk kompos dan/atau organik - Tanaman Belum Menghasilkan',
					  IF(
						KomposTM = 1,
						'Yes',
						IF(KomposTM = 2, 'No', '')
					  ) AS 'Pohon mana yang diberi pupuk kompos dan/atau organik - Tanaman Menghasilkan',
					  IF(
						KomposTR = 1,
						'Yes',
						IF(KomposTR = 2, 'No', '')
					  ) AS 'Pohon mana yang diberi pupuk kompos dan/atau organik - Tanaman Rusak',
					  IF(
						AvailableOrganicFertilizer = 1,
						'Yes',
						IF(
						  AvailableOrganicFertilizer = 2,
						  'No',
						  ''
						)
					  ) AS '(C) Apakah pupuk organik selalu tersedia dan mudah diperoleh',
					  IF(
						RoutineWatchSoilFertility = 1,
						'Yes',
						IF(
						  RoutineWatchSoilFertility = 2,
						  'No',
						  ''
						)
					  ) AS '(C) Apakah anda secara rutin memantau kesuburan tanah secara visual',
					  IF(
						ImprovePlantFixNitrogenInSoil = 1,
						'Yes',
						IF(
						  ImprovePlantFixNitrogenInSoil = 2,
						  'No',
						  ''
						)
					  ) AS 'Apa yang anda lakukan untuk memperbaiki kesuburan tanah - Menanam tanaman yang dapat memperbaiki unsur nitrogen dalam tanah',
					  IF(
						ImproveApplyPracticeAgroforestry = 1,
						'Yes',
						IF(
						  ImproveApplyPracticeAgroforestry = 2,
						  'No',
						  ''
						)
					  ) AS 'Apa yang anda lakukan untuk memperbaiki kesuburan tanah - Menerapkan praktek agroforestry',
					  IF(
						ImproveFertilizingWithOrganic = 1,
						'Yes',
						IF(
						  ImproveFertilizingWithOrganic = 2,
						  'No',
						  ''
						)
					  ) AS 'Apa yang anda lakukan untuk memperbaiki kesuburan tanah - Melakukan pemupukan dengan pupuk alami/organik',
					  IF(
						ImproveFertilizingWithAnorganic = 1,
						'Yes',
						IF(
						  ImproveFertilizingWithAnorganic = 2,
						  'No',
						  ''
						)
					  ) AS 'Apa yang anda lakukan untuk memperbaiki kesuburan tanah - Melakukan pemupukan dengan pupuk buatan/anorganik',
					  IF(
						ImproveMakeBiopori = 1,
						'Yes',
						IF(ImproveMakeBiopori = 2, 'No', '')
					  ) AS 'Apa yang anda lakukan untuk memperbaiki kesuburan tanah - Membuat biopori',
					  IF(
						ImprovePlantingShadeTrees = 1,
						'Yes',
						IF(
						  ImprovePlantingShadeTrees = 2,
						  'No',
						  ''
						)
					  ) AS 'Apa yang anda lakukan untuk memperbaiki kesuburan tanah - Menanam tanaman pelindung ',
					  IF(
						ImproveUseCoverCrop = 1,
						'Yes',
						IF(ImproveUseCoverCrop = 2, 'No', '')
					  ) AS 'Apa yang anda lakukan untuk memperbaiki kesuburan tanah - Menggunakan tanaman penutup tanah (cover crop)',
					  IF(
						ImproveTerracing = 1,
						'Yes',
						IF(ImproveTerracing = 2, 'No', '')
					  ) AS 'Apa yang anda lakukan untuk memperbaiki kesuburan tanah - Membuat terasering',
					  IF(
						ImproveDoNothing = 1,
						'Yes',
						IF(ImproveDoNothing = 2, 'No', '')
					  ) AS 'Apa yang anda lakukan untuk memperbaiki kesuburan tanah - Tidak melakukan apa-apa',
					  IF(
						TidakMemakaiKimia = 1,
						'Yes',
						IF(TidakMemakaiKimia = 2, 'No', '')
					  ) AS 'Apakah anda di kebun ini memakai pupuk non organik/kimia',
					  FrUrea AS 'Urea Frekuensi (kali/tahun)',
					  DoUrea AS 'Urea Dosis (gram/pohon/kali)',
					  FrZa AS 'ZA Frekuensi (kali/tahun)',
					  DoZa AS 'ZA Dosis (gram/pohon/kali)',
					  FrTsp AS 'TSP Frekuensi (kali/tahun)',
					  DoTsp AS 'TSP Dosis (gram/pohon/kali)',
					  FrNpk AS 'NPK Frekuensi (kali/tahun)',
					  DoNpk AS 'NPK Dosis (gram/pohon/kali)',
					  FrKcl AS 'KCL Frekuensi (kali/tahun)',
					  DoKcl AS 'KCL Dosis (gram/pohon/kali)',
					  FrFoliar AS 'Foliar Frekuensi (kali/tahun)',
					  DoFoliar AS 'Foliar Dosis (gram/pohon/kali)',
					  IF(
						PupukTBM = 1,
						'Yes',
						IF(PupukTBM = 2, 'No', '')
					  ) AS 'Pohon mana yang dipupuk tidak organik/kimia - Tanaman Belum Menghasilkan',
					  IF(
						PupukTM = 1,
						'Yes',
						IF(PupukTM = 2, 'No', '')
					  ) AS 'Pohon mana yang dipupuk tidak organik/kimia - Tanaman Menghasilkan',
					  IF(
						PupukTR = 1,
						'Yes',
						IF(PupukTR = 2, 'No', '')
					  ) AS 'Pohon mana yang dipupuk tidak organik/kimia - Tanaman Rusak',
					  IF(
						KimiaDana = 1,
						'Yes',
						IF(KimiaDana = 2, 'No', '')
					  ) AS 'Jika tidak memakai pupuk non organik, kenapa - Tidak ada dana',
					  IF(
						KimiaSupplier = 1,
						'Yes',
						IF(KimiaSupplier = 2, 'No', '')
					  ) AS 'Jika tidak memakai pupuk non organik, kenapa - Tidak menemukan supplier',
					  IF(
						KimiaDilatih = 1,
						'Yes',
						IF(KimiaDilatih = 2, 'No', '')
					  ) AS 'Jika tidak memakai pupuk non organik, kenapa - Belum dilatih',
					  IF(
						KimiaTidakSuka = 1,
						'Yes',
						IF(KimiaTidakSuka = 2, 'No', '')
					  ) AS 'Jika tidak memakai pupuk non organik, kenapa - Tidak suka menggunakan pupuk kimia',
					  IF(
						KimiaTidakTersedia = 1,
						'Yes',
						IF(KimiaTidakTersedia = 2, 'No', '')
					  ) AS 'Jika tidak memakai pupuk non organik, kenapa - Pupuk tidak tersedia',
					  IF(
						KimiaLain = 1,
						'Yes',
						IF(KimiaLain = 2, 'No', '')
					  ) AS 'Jika tidak memakai pupuk non organik, kenapa - Lain-lain',
					  IF(
						HamaBPK = 1,
						'Yes',
						IF(HamaBPK = 2, 'No', '')
					  ) AS 'Hama Utama Kakao - Penggerek Buah Kakao',
					  IF(
						HamaHelopeltis = 1,
						'Yes',
						IF(HamaHelopeltis = 2, 'No', '')
					  ) AS 'Hama Utama Kakao - Helopeltis',
					  IF(
						HamaBatang = 1,
						'Yes',
						IF(HamaBatang = 2, 'No', '')
					  ) AS 'Hama Utama Kakao - Penggerek batang atau ranting',
					  IF(
						PenyakitKanker = 1,
						'Yes',
						IF(PenyakitKanker = 2, 'No', '')
					  ) AS 'Penyakit Utama Kakao - Kanker Batang',
					  IF(
						PenyakitBusuk = 1,
						'Yes',
						IF(PenyakitBusuk = 2, 'No', '')
					  ) AS 'Penyakit Utama Kakao - Busuk Buah',
					  IF(
						PenyakitUpas = 1,
						'Yes',
						IF(PenyakitUpas = 2, 'No', '')
					  ) AS 'Penyakit Utama Kakao - Jamur Upas',
					  IF(
						PenyakitAkar = 1,
						'Yes',
						IF(PenyakitAkar = 2, 'No', '')
					  ) AS 'Penyakit Utama Kakao - Jamur Akar',
					  IF(
						PenyakitVSD = 1,
						'Yes',
						IF(PenyakitVSD = 2, 'No', '')
					  ) AS 'Penyakit Utama Kakao - VSD',
					  IF(
						PenyakitAntraknose = 1,
						'Yes',
						IF(PenyakitAntraknose = 2, 'No', '')
					  ) AS 'Penyakit Utama Kakao - Antraknose',
					  IF(
						RoutineMonitorPestInGarden = 1,
						'Yes',
						IF(
						  RoutineMonitorPestInGarden = 2,
						  'No',
						  ''
						)
					  ) AS '(C) Apakah pemantauan hama dan penyakit rutin anda lakukan di kebun ini',
					  IF(
						Herbisida,
						'Yes',
						IF(Herbisida, 'No', '')
					  ) AS 'Apakah Anda menggunakan herbisida',
					  FrequentHerbisida AS 'Herbisida Frekuensi (kali/tahun)',
					  DoseHerbisida AS 'Herbisida Dosis (ml/kali/kebun)',
					  IF(
						Herbisida14 = 1,
						'Yes',
						IF(Herbisida14 = 2, 'No', '')
					  ) AS 'Bimastar',
					  IF(
						Herbisida12 = 1,
						'Yes',
						IF(Herbisida12 = 2, 'No', '')
					  ) AS 'Bravo-xone',
					  IF(
						Herbisida22 = 1,
						'Yes',
						IF(Herbisida22 = 2, 'No', '')
					  ) AS 'DMA',
					  IF(
						Herbisida24 = 1,
						'Yes',
						IF(Herbisida24 = 2, 'No', '')
					  ) AS 'Konup',
					  IF(
						Herbisida26 = 1,
						'Yes',
						IF(Herbisida26 = 2, 'No', '')
					  ) AS 'Mupxone',
					  IF(
						Herbisida10 = 1,
						'Yes',
						IF(Herbisida10 = 2, 'No', '')
					  ) AS 'Noxone',
					  IF(
						Herbisida20 = 1,
						'Yes',
						IF(Herbisida20 = 2, 'No', '')
					  ) AS 'Prima Up',
					  IF(
						Herbisida16 = 1,
						'Yes',
						IF(Herbisida16 = 2, 'No', '')
					  ) AS 'Primastar',
					  IF(
						Herbisida8 = 1,
						'Yes',
						IF(Herbisida8 = 2, 'No', '')
					  ) AS 'Rambo',
					  IF(
						Herbisida28 = 1,
						'Yes',
						IF(Herbisida28 = 2, 'No', '')
					  ) AS 'Senus',
					  IF(
						Herbisida18 = 1,
						'Yes',
						IF(Herbisida18 = 2, 'No', '')
					  ) AS 'Supretox',
					  IF(
						Herbisida2 = 1,
						'Yes',
						IF(Herbisida2 = 2, 'No', '')
					  ) AS 'Basmilang',
					  IF(
						Herbisida5 = 1,
						'Yes',
						IF(Herbisida5 = 2, 'No', '')
					  ) AS 'Gramo-xone',
					  IF(
						Herbisida25 = 1,
						'Yes',
						IF(Herbisida25 = 2, 'No', '')
					  ) AS 'Herbatop',
					  IF(
						Herbisida19 = 1,
						'Yes',
						IF(Herbisida19 = 2, 'No', '')
					  ) AS 'Kleenup',
					  IF(
						Herbisida9 = 1,
						'Yes',
						IF(Herbisida9 = 2, 'No', '')
					  ) AS 'Para  Special',
					  IF(
						Herbisida11 = 1,
						'Yes',
						IF(Herbisida11 = 2, 'No', '')
					  ) AS 'Paratop',
					  IF(
						Herbisida3 = 1,
						'Yes',
						IF(Herbisida3 = 2, 'No', '')
					  ) AS 'Pilar Up',
					  IF(
						Herbisida27 = 1,
						'Yes',
						IF(Herbisida27 = 2, 'No', '')
					  ) AS 'Pointer',
					  IF(
						Herbisida15 = 1,
						'Yes',
						IF(Herbisida15 = 2, 'No', '')
					  ) AS 'Polado',
					  IF(
						Herbisida23 = 1,
						'Yes',
						IF(Herbisida23 = 2, 'No', '')
					  ) AS 'Polaris',
					  IF(
						Herbisida13 = 1,
						'Yes',
						IF(Herbisida13 = 2, 'No', '')
					  ) AS 'Primaxone',
					  IF(
						Herbisida1 = 1,
						'Yes',
						IF(Herbisida1 = 2, 'No', '')
					  ) AS 'Round Up',
					  IF(
						Herbisida17 = 1,
						'Yes',
						IF(Herbisida17 = 2, 'No', '')
					  ) AS 'Rumat',
					  IF(
						Herbisida7 = 1,
						'Yes',
						IF(Herbisida7 = 2, 'No', '')
					  ) AS 'Sapurata',
					  IF(
						Herbisida4 = 1,
						'Yes',
						IF(Herbisida4 = 2, 'No', '')
					  ) AS 'Sun Up',
					  IF(
						Herbisida6 = 1,
						'Yes',
						IF(Herbisida6 = 2, 'No', '')
					  ) AS 'Supremo',
					  IF(
						Herbisida29 = 1,
						'Yes',
						IF(Herbisida29 = 2, 'No', '')
					  ) AS 'Tamaxon',
					  IF(
						Herbisida21 = 1,
						'Yes',
						IF(Herbisida21 = 2, 'No', '')
					  ) AS 'Tanistar',
					  MerekHerbisida AS 'Herbisida Merk Lainnya',
					  IF(
						Insectisida = 1,
						'Yes',
						IF(Insectisida = 2, 'No', '')
					  ) AS 'Apakah Anda menggunakan insektisida',
					  FrequentInsectisida AS 'Insektisida Frekuensi (kali/tahun)',
					  DoseInsectisida AS 'Insektisida Dosis (ml/kali/kebun)',
					  IF(
						Insectisida1 = 1,
						'Yes',
						IF(Insectisida1 = 2, 'No', '')
					  ) AS 'Alika',
					  IF(
						Insectisida16 = 1,
						'Yes',
						IF(Insectisida16 = 2, 'No', '')
					  ) AS 'Arrivo',
					  IF(
						Insectisida18 = 1,
						'Yes',
						IF(Insectisida18 = 2, 'No', '')
					  ) AS 'Bestox',
					  IF(
						Insectisida21 = 1,
						'Yes',
						IF(Insectisida21 = 2, 'No', '')
					  ) AS 'Buldok',
					  IF(
						Insectisida3 = 1,
						'Yes',
						IF(Insectisida3 = 2, 'No', '')
					  ) AS 'Capture',
					  IF(
						Insectisida4 = 1,
						'Yes',
						IF(Insectisida4 = 2, 'No', '')
					  ) AS 'Bento',
					  IF(
						Insectisida5 = 1,
						'Yes',
						IF(Insectisida5 = 2, 'No', '')
					  ) AS 'Regent',
					  IF(
						Insectisida9 = 1,
						'Yes',
						IF(Insectisida9 = 2, 'No', '')
					  ) AS 'Chlormite',
					  IF(
						Insectisida20 = 1,
						'Yes',
						IF(Insectisida20 = 2, 'No', '')
					  ) AS 'Dangke',
					  IF(
						Insectisida10 = 1,
						'Yes',
						IF(Insectisida10 = 2, 'No', '')
					  ) AS 'Decis',
					  IF(
						Insectisida15 = 1,
						'Yes',
						IF(Insectisida15 = 2, 'No', '')
					  ) AS 'Deicer 505',
					  IF(
						Insectisida6 = 1,
						'Yes',
						IF(Insectisida6 = 2, 'No', '')
					  ) AS 'Drusban',
					  IF(
						Insectisida19 = 1,
						'Yes',
						IF(Insectisida19 = 2, 'No', '')
					  ) AS 'Halona',
					  IF(
						Insectisida12 = 1,
						'Yes',
						IF(Insectisida12 = 2, 'No', '')
					  ) AS 'Klensect',
					  IF(
						Insectisida22 = 1,
						'Yes',
						IF(Insectisida22 = 2, 'No', '')
					  ) AS 'Laser',
					  IF(
						Insectisida2 = 1,
						'Yes',
						IF(Insectisida2 = 2, 'No', '')
					  ) AS 'Matador',
					  IF(
						Insectisida8 = 1,
						'Yes',
						IF(Insectisida8 = 2, 'No', '')
					  ) AS 'Nurelle',
					  IF(
						Insectisida11 = 1,
						'Yes',
						IF(Insectisida11 = 2, 'No', '')
					  ) AS 'Organik',
					  IF(
						Insectisida7 = 1,
						'Yes',
						IF(Insectisida7 = 2, 'No', '')
					  ) AS 'Penalty',
					  IF(
						Insectisida23 = 1,
						'Yes',
						IF(Insectisida23 = 2, 'No', '')
					  ) AS 'Sevin',
					  IF(
						Insectisida17 = 1,
						'Yes',
						IF(Insectisida17 = 2, 'No', '')
					  ) AS 'Sidame-thrin',
					  IF(
						Insectisida14 = 1,
						'Yes',
						IF(Insectisida14 = 2, 'No', '')
					  ) AS 'Unicide',
					  IF(
						Insectisida13 = 1,
						'Yes',
						IF(Insectisida13 = 2, 'No', '')
					  ) AS 'Vigor',
					  MerekInsectisida AS 'Insektisida Merk Lainnya',
					  IF(
						Fungisida = 1,
						'Yes',
						IF(Fungisida = 2, 'No', '')
					  ) AS 'Apakah Anda menggunakan fungisida',
					  FrequentFungisida AS 'Fungisida Frekuensi (kali/tahun)',
					  DoseFungisida AS 'Fungisida Dosis (ml/kali/kebun)',
					  IF(
						Fungisida3 = 1,
						'Yes',
						IF(Fungisida3 = 2, 'No', '')
					  ) AS 'Amistar-top',
					  IF(
						Fungisida6 = 1,
						'Yes',
						IF(Fungisida6 = 2, 'No', '')
					  ) AS 'Antila',
					  IF(
						Fungisida7 = 1,
						'Yes',
						IF(Fungisida7 = 2, 'No', '')
					  ) AS 'Antracol',
					  IF(
						Fungisida13 = 1,
						'Yes',
						IF(Fungisida13 = 2, 'No', '')
					  ) AS 'Benhasil',
					  IF(
						Fungisida10 = 1,
						'Yes',
						IF(Fungisida10 = 2, 'No', '')
					  ) AS 'Cozeb',
					  IF(
						Fungisida2 = 1,
						'Yes',
						IF(Fungisida2 = 2, 'No', '')
					  ) AS 'Dithane',
					  IF(
						Fungisida1 = 1,
						'Yes',
						IF(Fungisida1 = 2, 'No', '')
					  ) AS 'Nordox',
					  IF(
						Fungisida11 = 1,
						'Yes',
						IF(Fungisida11 = 2, 'No', '')
					  ) AS 'Fungicide-Organik',
					  IF(
						Fungisida9 = 1,
						'Yes',
						IF(Fungisida9 = 2, 'No', '')
					  ) AS 'Polydor',
					  IF(
						Fungisida12 = 1,
						'Yes',
						IF(Fungisida12 = 2, 'No', '')
					  ) AS 'Rabbat',
					  IF(
						Fungisida5 = 1,
						'Yes',
						IF(Fungisida5 = 2, 'No', '')
					  ) AS 'Rhidomil',
					  IF(
						Fungisida4 = 1,
						'Yes',
						IF(Fungisida4 = 2, 'No', '')
					  ) AS 'Scorpio',
					  MerekFungisida AS 'Fungisida Merk Lainnya',
					  IF(
						UseChemicalPesticideDosage = 1,
						'Yes',
						IF(
						  UseChemicalPesticideDosage = 2,
						  'No',
						  ''
						)
					  ) AS '(C) Apakah Anda menggunakan pestisida kimia sesuai dengan dosis yang dianjurkan',
					  IF(
						ApplyAltNonChemicalControlPests = 1,
						'Yes',
						IF(
						  ApplyAltNonChemicalControlPests = 2,
						  'No',
						  ''
						)
					  ) AS '(C) Apakah Anda menerapkan cara alternatif non-kimia untuk mengendalikan hama & penyakit',
					  IF(
						UseOrganicControlPests = 1,
						'Yes',
						IF(
						  UseOrganicControlPests = 2,
						  'No',
						  ''
						)
					  ) AS '(C) Apakah Anda menggunakan pestisida alami untuk mengendalikan hama dan penyakit',
					  IF(
						UseChemicalLowestToxicity = 1,
						'Yes',
						IF(
						  UseChemicalLowestToxicity = 2,
						  'No',
						  ''
						)
					  ) AS '(C) Apakah Anda selalu menggunakan pestisida kimia yang memiliki kadar racun terendah',
					  IF(
						UseChemicalLastChoice = 1,
						'Yes',
						IF(UseChemicalLastChoice = 2, 'No', '')
					  ) AS '(C) Apakah pestisida kimia hanya Anda gunakan sebagai pilihan terakhir',
					  IF(
						ApplyRotationStrategy = 1,
						'Yes',
						IF(ApplyRotationStrategy = 2, 'No', '')
					  ) AS '(C) Apakah Anda menerapkan strategi rotasi pada penggunaan pestisida kimia',
					  IF(
						NoticeUseInorganicFertilizer = 1,
						'Yes',
						IF(
						  NoticeUseInorganicFertilizer = 2,
						  'No',
						  ''
						)
					  ) AS '(C) Apakah Anda mencatat penggunaan pestisida dan pupuk anorganik',
					  IF(
						TrainedUseProperly = 1,
						'Yes',
						IF(TrainedUseProperly = 2, 'No', '')
					  ) AS '(C) Apakah Anda sudah dilatih untuk menggunakan pestisida dengan tepat dan aman',
					  IF(
						MixPesticideLiquidFertilizer = 1,
						'Yes',
						IF(
						  MixPesticideLiquidFertilizer = 2,
						  'No',
						  ''
						)
					  ) AS '(C) Apakah ketika Anda menyiapkan dan mencampur pestisida dan pupuk cair sesuai dengan petunjuk dosis dan keamanan pada label',
					  IF(
						ExcessPesticideDisposedSafely = 1,
						'Yes',
						IF(
						  ExcessPesticideDisposedSafely = 2,
						  'No',
						  ''
						)
					  ) AS '(C) Apakah kelebihan campuran pestisida, pupuk cair atau limbah pencucian tangki dibuang dengan aman sesuai standar internal kelompok',
					  IF(
						GiveNoEntrySignAfterSpraying = 1,
						'Yes',
						IF(
						  GiveNoEntrySignAfterSpraying = 2,
						  'No',
						  ''
						)
					  ) AS '(C) Apakah Anda memberi tanda dilarang masuk atau mematuhi waktu masuk kembali ke kebun setelah penyemprotan pestisida',
					  IF(
						AdherePreHarvestInterval = 1,
						'Yes',
						IF(
						  AdherePreHarvestInterval = 2,
						  'No',
						  ''
						)
					  ) AS '(C) Apakah Anda mematuhi jeda waktu pra-panen yang direkomendasikan untuk seluruh pestisida  yang digunakan',
					  IF(
						EquipmentGoodCondition = 1,
						'Yes',
						IF(
						  EquipmentGoodCondition = 2,
						  'No',
						  ''
						)
					  ) AS '(C) Apakah seluruh perlengkapan yang digunakan untuk pemberian pupuk dan pestisida dalam kondisi yang baik dan berfungsi sebagaimana mestinya',
					  IF(
						StoreAccordanceOnLabel = 1,
						'Yes',
						IF(
						  StoreAccordanceOnLabel = 2,
						  'No',
						  ''
						)
					  ) AS '(C) Pestisida dan pupuk anorganik disimpan dengan cara - Sesuai dengan petunjuk pada label',
					  IF(
						StoreOriginalPackaging = 1,
						'Yes',
						IF(
						  StoreOriginalPackaging = 2,
						  'No',
						  ''
						)
					  ) AS '(C) Pestisida dan pupuk anorganik disimpan dengan cara - Dalam wadah atau kemasan asli',
					  IF(
						StoreIndicationSuitablePlants = 1,
						'Yes',
						IF(
						  StoreIndicationSuitablePlants = 2,
						  'No',
						  ''
						)
					  ) AS '(C) Pestisida dan pupuk anorganik disimpan dengan cara - Dengan indikasi jenis tanaman yang sesuai dengan penggunaannya',
					  IF(
						StoreAvoidPossibleSpill = 1,
						'Yes',
						IF(
						  StoreAvoidPossibleSpill = 2,
						  'No',
						  ''
						)
					  ) AS '(C) Pestisida dan pupuk anorganik disimpan dengan cara - Terhindar dari kemungkinan tumpah',
					  IF(
						StoreSecuredPlace = 1,
						'Yes',
						IF(StoreSecuredPlace = 2, 'No', '')
					  ) AS '(C) Pestisida dan pupuk anorganik disimpan dengan cara - Diamankan ditempat yang tidak bisa diakses anak anak',
					  IF(
						StoreFarFromProducts = 1,
						'Yes',
						IF(StoreFarFromProducts = 2, 'No', '')
					  ) AS '(C) Pestisida dan pupuk anorganik disimpan dengan cara - Jauh dari produk yang dipanen, alat-alat, materi kemasan, dan produk-produk makanan',
					  IF(
						HandlingCleanDry = 1,
						'Yes',
						IF(HandlingCleanDry = 2, 'No', '')
					  ) AS '(C) Bagaimana keadaan fasilitas untuk penanganan, pelarutan dan penyimpanan  pestisida dan pupuk anorganik - Bersih dan kering',
					  IF(
						HandlingEnoughVentilationLight = 1,
						'Yes',
						IF(
						  HandlingEnoughVentilationLight = 2,
						  'No',
						  ''
						)
					  ) AS '(C) Bagaimana keadaan fasilitas untuk penanganan, pelarutan dan penyimpanan  pestisida dan pupuk anorganik - Cukup ventilasi dan cahaya',
					  IF(
						HandlingStructurallySafe = 1,
						'Yes',
						IF(
						  HandlingStructurallySafe = 2,
						  'No',
						  ''
						)
					  ) AS '(C) Bagaimana keadaan fasilitas untuk penanganan, pelarutan dan penyimpanan  pestisida dan pupuk anorganik - Secara struktur aman',
					  IF(
						HandlingAntiAbsorptive = 1,
						'Yes',
						IF(
						  HandlingAntiAbsorptive = 2,
						  'No',
						  ''
						)
					  ) AS '(C) Bagaimana keadaan fasilitas untuk penanganan, pelarutan dan penyimpanan  pestisida dan pupuk anorganik - Dilengkapi dengan bahan anti serap',
					  IF(
						HandlingLeakproofedFloor = 1,
						'Yes',
						IF(
						  HandlingLeakproofedFloor = 2,
						  'No',
						  ''
						)
					  ) AS '(C) Bagaimana keadaan fasilitas untuk penanganan, pelarutan dan penyimpanan  pestisida dan pupuk anorganik - Lantai yang kedap suara dan anti rembes',
					  IF(
						HandlingFireproofMaterial = 1,
						'Yes',
						IF(
						  HandlingFireproofMaterial = 2,
						  'No',
						  ''
						)
					  ) AS '(C) Bagaimana keadaan fasilitas untuk penanganan, pelarutan dan penyimpanan  pestisida dan pupuk anorganik - Rak-rak yang bersifat anti-serap dan bermateri tahan api',
					  IF(
						HandlingCollectSpillage = 1,
						'Yes',
						IF(
						  HandlingCollectSpillage = 2,
						  'No',
						  ''
						)
					  ) AS '(C) Bagaimana keadaan fasilitas untuk penanganan, pelarutan dan penyimpanan  pestisida dan pupuk anorganik - Terdapat sebuah sistem untuk menampung tumpahan',
					  IF(
						HandlingClearWarningSign = 1,
						'Yes',
						IF(
						  HandlingClearWarningSign = 2,
						  'No',
						  ''
						)
					  ) AS '(C) Bagaimana keadaan fasilitas untuk penanganan, pelarutan dan penyimpanan  pestisida dan pupuk anorganik - Terdapat tanda peringatan yang jelas dan permanen ada di dekat pintu masuk',
					  IF(
						HandlingFirstAidInfo = 1,
						'Yes',
						IF(HandlingFirstAidInfo = 2, 'No', '')
					  ) AS '(C) Bagaimana keadaan fasilitas untuk penanganan, pelarutan dan penyimpanan  pestisida dan pupuk anorganik - Terdapat peringatan keselamatan yang kelihatan, lambang-lambang  peringatan, gejala keracunan, dan informasi pertolongan pertama untuk setiap  produk yang disimpan',
					  IF(
						HandlingProcedureEmergency = 1,
						'Yes',
						IF(
						  HandlingProcedureEmergency = 2,
						  'No',
						  ''
						)
					  ) AS '(C) Bagaimana keadaan fasilitas untuk penanganan, pelarutan dan penyimpanan  pestisida dan pupuk anorganik - Terdapat tata cara keadaan darurat yang jelas',
					  IF(
						HandlingAreaCleanEye = 1,
						'Yes',
						IF(HandlingAreaCleanEye = 2, 'No', '')
					  ) AS '(C) Bagaimana keadaan fasilitas untuk penanganan, pelarutan dan penyimpanan  pestisida dan pupuk anorganik - Terdapat area untuk membersihkan mata',
					  IF(
						HandlingAccommodateLiquidStored = 1,
						'Yes',
						IF(
						  HandlingAccommodateLiquidStored = 2,
						  'No',
						  ''
						)
					  ) AS '(C) Bagaimana keadaan fasilitas untuk penanganan, pelarutan dan penyimpanan  pestisida dan pupuk anorganik - Fasilitas diberi pembatas dan mampu menampung 110% dari seluruh volume  cair yang disimpan',
					  IF(APD = 1, 'Yes', IF(APD = 2, 'No', '')) AS 'Apakah Anda menggunakan Pakaian Perlindungan Diri (PPD)',
					  IF(
						TempatSimpanPestisida = 1,
						'Didalam rumah',
						IF(
						  TempatSimpanPestisida = 2,
						  'Tempat khusus pestisida',
						  IF(
							TempatSimpanPestisida = 3,
							'Diluar rumah (kawasan rumah)',
							IF(
							  TempatSimpanPestisida = 4,
							  'Diluar kebun',
							  IF(
								TempatSimpanPestisida = 5,
								'Lain-lain',
								''
							  )
							)
						  )
						)
					  ) AS 'Dimana Anda menyimpan pestisida sebelum dan selama pemakaian',
					  IF(
						BuangKemasanPestisida = 1,
						'Di buang sembarangan (di kebun atau sekitar rumah)',
						IF(
						  BuangKemasanPestisida = 2,
						  'Digunakan untuk menyimpan sesuatu',
						  IF(
							BuangKemasanPestisida = 3,
							'Dicuci dengan bersih dan dikubur',
							IF(
							  BuangKemasanPestisida = 4,
							  'Dibakar',
							  IF(
								BuangKemasanPestisida = 5,
								'Daur ulang',
								IF(
								  BuangKemasanPestisida = 6,
								  'Lain-lain',
								  ''
								)
							  )
							)
						  )
						)
					  ) AS 'Apa yang Anda lakukan dengan kemasan pestisida setelah pemakaian',
					  IF(
						UsePesticideInorganicFertilizer = 1,
						'Dalam jarak 5 meter dari badan air musiman maupun permanen yang lebarnya 3 meter atau kurang',
						IF(
						  UsePesticideInorganicFertilizer = 2,
						  'Dalam jarak 10 meter dari badan air musiman ataupun permanen yang lebarnya lebih dari 3 meter',
						  IF(
							UsePesticideInorganicFertilizer = 3,
							'Dalam jarak 15 meter dari mata air',
							IF(
							  UsePesticideInorganicFertilizer = 4,
							  'Tidak sesuai poin A, B dan C',
							  ''
							)
						  )
						)
					  ) AS '(C) Apakah Anda menggunakan pestisida dan pupuk anorganik',
					  gar.`DateCreated`,
					  h.`UserRealName` AS CreatedBy,
					  gar.`DateUpdated`,
					  i.`UserRealName` AS LastModifiedBy
				FROM
					`ktv_certification_pre_afl_target` tar
					LEFT JOIN sys_user us ON tar.`PICUserID` = us.`UserId`
					LEFT JOIN ktv_ims ims ON tar.`IMSID` = ims.`IMSID`

					LEFT JOIN ktv_certification_pre_afl_garden aflg ON 1=1
						AND tar.`IMSID` = aflg.`IMSID`
						AND tar.`FarmerID` = aflg.`FarmerID`
					LEFT JOIN ktv_members_garden gar ON 1=1
						AND aflg.`FarmerID` = gar.`FarmerID`
						AND aflg.`SurveyNr` = gar.`SurveyNr`
						AND aflg.`GardenNr` = gar.`GardenNr`
						AND (
							DATE(gar.`DateUpdated`) >= DATE_FORMAT( ims.`CertEventDate` - INTERVAL 6 MONTH, '%Y/%m/%d' )
							OR
							DATE(gar.`DateCreated`) >= DATE_FORMAT( ims.`CertEventDate` - INTERVAL 6 MONTH, '%Y/%m/%d' )
						)

					LEFT JOIN ktv_members b ON b.`FarmerID` = tar.`FarmerID`
					LEFT JOIN ktv_cpg c ON c.`CPGid` = b.`CPGid`
					LEFT JOIN ktv_village g ON g.`VillageID` = b.`VillageID`
					LEFT JOIN ktv_subdistrict f ON f.`SubDistrictID` = g.SubDistrictID
					LEFT JOIN ktv_district e ON e.`DistrictID` = f.DistrictID
					LEFT JOIN ktv_province d ON d.`ProvinceID` = e.ProvinceID
					LEFT JOIN sys_user h ON h.UserId = gar.`CreatedBy`
					LEFT JOIN sys_user i ON i.UserId = gar.`LastModifiedBy`
				WHERE
					tar.`IMSID` = ?
					AND tar.`PICUserID` = ?
					AND gar.GardenNr IS NOT NULL
				ORDER BY tar.`FarmerID` ASC
				";
		$query = $this->db->query($sql, array($IMSID, $UserID));
		$data  = $query->result_array();

		//target
		$sql = "SELECT
					COUNT(*) AS BANYAK
				FROM
					`ktv_certification_pre_afl_target` tar
					LEFT JOIN ktv_certification_pre_afl_garden aflg ON 1=1
						AND tar.`IMSID` = aflg.`IMSID`
						AND tar.`FarmerID` = aflg.`FarmerID`
				WHERE
					tar.`IMSID` = ?
					AND tar.`PICUserID` = ?";
		$query   = $this->db->query($sql, array($IMSID, $UserID));
		$dataRow = $query->row_array();

		$arrReturn['dataList'] = $data;
		$arrReturn['capai']    = count($data);
		$arrReturn['target']   = $dataRow['BANYAK'];
		return $arrReturn;
	}

	public function getPostHarvestAflP1Summary($IMSID)
	{
		$sql = "SELECT
				afl.`FarmerID` AS 'ID Petani',
				b.ExtFarmerID AS 'ID Petani Eksternal',
				b.`FarmerName` AS 'Nama Petani',
				c.`CPGid` AS 'ID Kelompok',
				c.`GroupName` AS 'Kelompok Tani',
				d.`Province` AS 'Propinsi',
				e.`District` AS 'Kabupaten',
				f.`SubDistrict` AS 'Kecamatan',
				g.`Village` AS 'Desa',

				  post_h.`SurveyNr` AS 'Nr Survey',
				  post_h.`DateCollection` AS 'Tgl Interview',
				  IF(
					Fermentation = 1,
					'Yes',
					IF(Fermentation = 2, 'No', '')
				  ) AS 'Apakah anda melakukan fermentasi biji kakao sebelum menjemur (fermentasi minimal 4 hari)',
				  FermentationDays AS 'Jika ya, berapa hari fermentasi biji dilakukan (hari)',
				  IF(
					NoFermentation = 1,
					'Tidak punya cukup waktu',
					IF(
					  NoFermentation = 2,
					  'Tidak punya alat',
					  IF(
						NoFermentation = 3,
						'Tidak tahu caranya',
						IF(
						  NoFermentation = 4,
						  'Tidak menguntungkan',
						  IF(
							NoFermentation = 5,
							'Malas',
							IF(
							  NoFermentation = 6,
							  'Lain -lain',
							  ''
							)
						  )
						)
					  )
					)
				  ) AS 'Jika tidak, mengapa',
				  IF(
					JemurYesNo = 1,
					'Yes',
					IF(JemurYesNo = 2, 'No', '')
				  ) AS 'Apakah anda menjemur biji kakao sebelum menjual',
				  DryingDays AS 'Jika ya, berapa hari anda mengeringkan biji kakao (hari)',
				  IF(
					SunDryingSemen = 1,
					'Yes',
					IF(SunDryingSemen = 2, 'No', '')
				  ) AS 'Pengeringan pada lantai penjemuran',
				  IF(
					SunDryingAspal = 1,
					'Yes',
					IF(SunDryingAspal = 2, 'No', '')
				  ) AS 'Pengeringan di atas aspal',
				  IF(
					DryingAlat = 1,
					'Yes',
					IF(DryingAlat = 2, 'No', '')
				  ) AS 'Pengeringan dengan alat',
				  IF(
					SunDryingAlas = 1,
					'Yes',
					IF(SunDryingAlas = 2, 'No', '')
				  ) AS 'Pengeringan menggunakan alas (terpal, plastik, anyaman daun kelapa)',
				  IF(
					BeanDryHygienic = 1,
					'Yes',
					IF(BeanDryHygienic = 2, 'No', '')
				  ) AS 'Apakah anda selalu memastikan jika biji kakao anda dikeringkan dengan cara yang higienis dan terhindar dari pencemaran asap, kotoran, benda asing dll yang dapat mempengaruhi mutu',
				  IF(
					TidakJemur = 1,
					'Lebih menguntungkan menjual biji basah',
					IF(
					  TidakJemur = 2,
					  'Lebih mudah dikerjakan',
					  IF(
						TidakJemur = 3,
						'Lebih cepat memperoleh uang',
						IF(
						  TidakJemur = 4,
						  'Sulit menjemur karena musim hujan',
						  IF(
							TidakJemur = 5,
							'Tidak cukup waktu and perlu bantuan tenaga kerja',
							IF(TidakJemur = 6, 'Lain-lain', '')
						  )
						)
					  )
					)
				  ) AS 'Jika tidak, mengapa anda tidak menjemur biji kakao',
				  IF(
					DryMoistureStandard = 1,
					'Yes',
					IF(DryMoistureStandard = 2, 'No', '')
				  ) AS '(C) Apakah biji kakao anda keringkan hingga mencapai kadar kelembaban sesuai standar kelompok',
				  IF(
					ImplementBeanRemainDry = 1,
					'Yes',
					IF(
					  ImplementBeanRemainDry = 2,
					  'No',
					  ''
					)
				  ) AS 'Apakah anda menerapkan langkah-langkah untuk memastikan agar biji kakao tetap kering dan terhindar dari basah selama proses pengangkutan dan penyimpanan',
				  IF(
					Sortasi = 1,
					'Yes',
					IF(Sortasi = 2, 'No', '')
				  ) AS 'Apakah anda memisahkan biji berkualitas bagus dan berkualitas jelek/rendah sebelum menjualnya',
				  IF(
					NoSortasi = 1,
					'Tidak ada perbedaan harga',
					IF(
					  NoSortasi = 2,
					  'Terlalu banyak menghabiskan waktu',
					  IF(
						NoSortasi = 3,
						'Tidak banyak biji berkualitas bagus',
						IF(
						  NoSortasi = 4,
						  'Tidak tahu cara memisahkan biji',
						  ''
						)
					  )
					)
				  ) AS 'Jika tidak, mengapa anda tidak melakukan pemisahan biji',
				  IF(
					CocoaBuyers = 1,
					'Pedagang pengumpul di kampung',
					IF(
					  CocoaBuyers = 2,
					  'Pedagang pengumpul di kecamatan',
					  IF(
						CocoaBuyers = 3,
						'Pedagangan kabupaten/eksportir',
						IF(
						  CocoaBuyers = 4,
						  'Kelompok petani',
						  ''
						)
					  )
					)
				  ) AS 'Biasanya menjual biji kakao kepada',
				  IF(
					AntarSendiri = 1,
					'Yes',
					IF(AntarSendiri = 2, 'No', '')
				  ) AS 'Apakah anda mengantar kakao sendiri',
				  Distance AS 'Jika ya, berapa jarak dari rumah anda (m)',
				  post_h.`DateCreated`,
				  h.`UserRealName` AS CreatedBy,
				  post_h.`DateUpdated`,
				  i.`UserRealName` AS LastModifiedBy
			FROM
				ktv_certification_afl_farmer afl

				LEFT JOIN ktv_members b
					ON b.`FarmerID` = afl.`FarmerID`
				LEFT JOIN ktv_cpg c
					ON c.`CPGid` = b.`CPGid`
				LEFT JOIN ktv_village g
					ON g.`VillageID` = b.`VillageID`
				LEFT JOIN ktv_subdistrict f
					ON f.`SubDistrictID` = g.SubDistrictID
				LEFT JOIN ktv_district e
					ON e.`DistrictID` = f.DistrictID
				LEFT JOIN ktv_province d
					ON d.`ProvinceID` = e.ProvinceID
				INNER JOIN (
					SELECT
						ph.*
					FROM
					(
					SELECT
						a.`IMSID`
						, a.`FarmerID`
						, MAX(a.`CertSurveyNr`) AS SurveyNr
					FROM
						ktv_certification_afl_garden a
					WHERE
						a.`IMSID` = ?
					GROUP BY a.`FarmerID`
					) AS ph_survey
					INNER JOIN ktv_members_post_harvest ph ON 1=1
						AND ph_survey.FarmerID = ph.`FarmerID`
						AND ph_survey.SurveyNr = ph.`SurveyNr`
				) AS post_h ON 1=1
					AND afl.FarmerID = post_h.FarmerID

				LEFT JOIN sys_user h ON h.UserId = post_h.`CreatedBy`
				LEFT JOIN sys_user i ON i.UserId = post_h.`LastModifiedBy`

			WHERE
				afl.`IMSID` = ?
				AND afl.CertStatusAudit != '-'
			ORDER BY afl.`FarmerID` ASC
		";
		$query = $this->db->query($sql, array($IMSID, $IMSID));
		return $query->result_array();
	}

	public function getPostHarvestAflP1SummaryFaProgress($IMSID, $UserID, $CertEventDate)
	{
		$arrReturn = array();

		//target
		$sql = "SELECT
				COUNT(*) AS BANYAK
			FROM
				`ktv_certification_pre_afl_target` tar
			WHERE
				tar.`IMSID` = ?
				AND tar.`PICUserID` = ?";
		$query      = $this->db->query($sql, array($IMSID, $UserID));
		$dataTarget = $query->row_array();

		//realnya sudah dapat berapa
		$sql = "SELECT
					tbl_sub.CAPAI,
					LEAST(
						DATE(IF(tbl_sub.BeginTgl='0000-00-00',tbl_sub.BeginTglUpdated,tbl_sub.BeginTgl)),
						DATE(IF(tbl_sub.BeginTglUpdated='0000-00-00',tbl_sub.BeginTgl,tbl_sub.BeginTglUpdated))
					) AS BeginTgl,
					GREATEST(DATE(tbl_sub.EndTgl),DATE(tbl_sub.EndTglUpdated)) AS EndTgl
				FROM
				(
				SELECT
					COUNT(tar.`FarmerID`) AS CAPAI,
					DATE(MIN(tbl_ph.`DateCreated`)) AS BeginTgl,
					DATE(MIN(tbl_ph.`DateUpdated`)) AS BeginTglUpdated,
					DATE(MAX(tbl_ph.`DateCreated`)) AS EndTgl,
					DATE(MAX(tbl_ph.`DateUpdated`)) AS EndTglUpdated
				FROM
					`ktv_certification_pre_afl_target` tar
					LEFT JOIN sys_user us ON tar.`PICUserID` = us.`UserId`
					LEFT JOIN ktv_ims ims ON tar.`IMSID` = ims.`IMSID`

					INNER JOIN (
						SELECT
							tbl_garden.IMSID
							, ph.*
						FROM
							(
								SELECT
									a.`IMSID`
									, gar.`FarmerID`
									, MAX(gar.`SurveyNr`) AS SurveyNr
								FROM
									ktv_certification_pre_afl_garden a
									LEFT JOIN ktv_members_garden gar ON 1=1
										AND a.`FarmerID` = gar.`FarmerID`
										AND a.`SurveyNr` = gar.`SurveyNr`
										AND a.`GardenNr` = gar.`GardenNr`
								WHERE
									a.`IMSID` = ?
									AND gar.`StatusCode` = 'active'
									AND (
										DATE(gar.`DateUpdated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
										OR
										DATE(gar.`DateCreated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
									)
								GROUP BY gar.`FarmerID`
							) AS tbl_garden
							LEFT JOIN ktv_members_post_harvest ph ON 1=1
								AND tbl_garden.FarmerID = ph.`FarmerID`
								AND tbl_garden.SurveyNr = ph.`SurveyNr`
						WHERE
							ph.`StatusCode` = 'active'
							AND (
								DATE(ph.`DateUpdated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
								OR
								DATE(ph.`DateCreated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
							)
					) AS tbl_ph ON 1=1
						AND tar.`IMSID` = tbl_ph.IMSID
						AND tar.`FarmerID` = tbl_ph.FarmerID

					LEFT JOIN ktv_members b ON b.`FarmerID` = tar.`FarmerID`
					LEFT JOIN ktv_cpg c ON c.`CPGid` = b.`CPGid`
					LEFT JOIN ktv_village g ON g.`VillageID` = b.`VillageID`
					LEFT JOIN ktv_subdistrict f ON f.`SubDistrictID` = g.SubDistrictID
					LEFT JOIN ktv_district e ON e.`DistrictID` = f.DistrictID
					LEFT JOIN ktv_province d ON d.`ProvinceID` = e.ProvinceID
					LEFT JOIN sys_user h ON h.UserId = tbl_ph.`CreatedBy`
					LEFT JOIN sys_user i ON i.UserId = tbl_ph.`LastModifiedBy`
				WHERE
					tar.`IMSID` = ?
					AND tar.`PICUserID` = ?
				ORDER BY tar.`FarmerID` ASC
				) AS tbl_sub";
		$query     = $this->db->query($sql, array($IMSID, $CertEventDate, $CertEventDate, $CertEventDate, $CertEventDate, $IMSID, $UserID));
		$dataCapai = $query->row_array();

		//cek apakah data tanggal rangenya ada
		if ($dataCapai['BeginTgl'] != "0000-00-00" && $dataCapai['BeginTgl'] != "" && $dataCapai['EndTgl'] != "0000-00-00" && $dataCapai['EndTgl'] != "") {
			//progress data by tanggal
			$data      = array();
			$dateRange = $this->getDateRange($dataCapai['BeginTgl'], $dataCapai['EndTgl'], '+1 day', 'Y-m-d');
			foreach ($dateRange as $key => $tglProses) {
				$data[$key]['tanggal'] = $tglProses;

				//query capainya perhari
				$sql = "SELECT
							COUNT(tar.`FarmerID`) AS CAPAI
						FROM
							`ktv_certification_pre_afl_target` tar
							LEFT JOIN sys_user us ON tar.`PICUserID` = us.`UserId`
							LEFT JOIN ktv_ims ims ON tar.`IMSID` = ims.`IMSID`

							INNER JOIN (
								SELECT
									tbl_garden.IMSID
									, ph.*
								FROM
									(
										SELECT
											a.`IMSID`
											, gar.`FarmerID`
											, MAX(gar.`SurveyNr`) AS SurveyNr
										FROM
											ktv_certification_pre_afl_garden a
											LEFT JOIN ktv_members_garden gar ON 1=1
												AND a.`FarmerID` = gar.`FarmerID`
												AND a.`SurveyNr` = gar.`SurveyNr`
												AND a.`GardenNr` = gar.`GardenNr`
										WHERE
											a.`IMSID` = ?
											AND gar.`StatusCode` = 'active'
											AND (
												DATE(gar.`DateUpdated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
												OR
												DATE(gar.`DateCreated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
											)
										GROUP BY gar.`FarmerID`
									) AS tbl_garden
									LEFT JOIN ktv_members_post_harvest ph ON 1=1
										AND tbl_garden.FarmerID = ph.`FarmerID`
										AND tbl_garden.SurveyNr = ph.`SurveyNr`
								WHERE
									ph.`StatusCode` = 'active'
									AND (
										DATE(ph.`DateUpdated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
										OR
										DATE(ph.`DateCreated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
									)
							) AS tbl_ph ON 1=1
								AND tar.`IMSID` = tbl_ph.IMSID
								AND tar.`FarmerID` = tbl_ph.FarmerID

							LEFT JOIN ktv_members b ON b.`FarmerID` = tar.`FarmerID`
							LEFT JOIN ktv_cpg c ON c.`CPGid` = b.`CPGid`
							LEFT JOIN ktv_village g ON g.`VillageID` = b.`VillageID`
							LEFT JOIN ktv_subdistrict f ON f.`SubDistrictID` = g.SubDistrictID
							LEFT JOIN ktv_district e ON e.`DistrictID` = f.DistrictID
							LEFT JOIN ktv_province d ON d.`ProvinceID` = e.ProvinceID
							LEFT JOIN sys_user h ON h.UserId = tbl_ph.`CreatedBy`
							LEFT JOIN sys_user i ON i.UserId = tbl_ph.`LastModifiedBy`
						WHERE
							tar.`IMSID` = ?
							AND tar.`PICUserID` = ?
							AND (
								DATE(tbl_ph.`DateCreated`) = ?
								OR
								DATE(tbl_ph.`DateUpdated`) = ?
							)
						ORDER BY tar.`FarmerID` ASC";
				$query       = $this->db->query($sql, array($IMSID, $CertEventDate, $CertEventDate, $CertEventDate, $CertEventDate, $IMSID, $UserID, $tglProses, $tglProses));
				$dataPerHari = $query->row_array();

				$data[$key]['capai'] = $dataPerHari['CAPAI'];
			}
		} else {
			$data = array();
		}

		$arrReturn['dataList'] = $data;
		$arrReturn['capai']    = $dataCapai['CAPAI'];
		$arrReturn['target']   = $dataTarget['BANYAK'];
		return $arrReturn;
	}

	public function getPostHarvestAflP1SummaryFa($IMSID, $UserID, $EventDate)
	{
		$arrReturn = array();

		$sql = "SELECT
				tar.`FarmerID` AS 'ID Petani',
				b.ExtFarmerID AS 'ID Petani Eksternal',
				b.`FarmerName` AS 'Nama Petani',
				c.`CPGid` AS 'ID Kelompok',
				c.`GroupName` AS 'Kelompok Tani',
				d.`Province` AS 'Propinsi',
				e.`District` AS 'Kabupaten',
				f.`SubDistrict` AS 'Kecamatan',
				g.`Village` AS 'Desa',
				  tbl_ph.`SurveyNr` AS 'Nr Survey',
				  tbl_ph.`DateCollection` AS 'Tgl Interview',
				  IF(
					Fermentation = 1,
					'Yes',
					IF(Fermentation = 2, 'No', '')
				  ) AS 'Apakah anda melakukan fermentasi biji kakao sebelum menjemur (fermentasi minimal 4 hari)',
				  FermentationDays AS 'Jika ya, berapa hari fermentasi biji dilakukan (hari)',
				  IF(
					NoFermentation = 1,
					'Tidak punya cukup waktu',
					IF(
					  NoFermentation = 2,
					  'Tidak punya alat',
					  IF(
						NoFermentation = 3,
						'Tidak tahu caranya',
						IF(
						  NoFermentation = 4,
						  'Tidak menguntungkan',
						  IF(
							NoFermentation = 5,
							'Malas',
							IF(
							  NoFermentation = 6,
							  'Lain -lain',
							  ''
							)
						  )
						)
					  )
					)
				  ) AS 'Jika tidak, mengapa',
				  IF(
					JemurYesNo = 1,
					'Yes',
					IF(JemurYesNo = 2, 'No', '')
				  ) AS 'Apakah anda menjemur biji kakao sebelum menjual',
				  DryingDays AS 'Jika ya, berapa hari anda mengeringkan biji kakao (hari)',
				  IF(
					SunDryingSemen = 1,
					'Yes',
					IF(SunDryingSemen = 2, 'No', '')
				  ) AS 'Pengeringan pada lantai penjemuran',
				  IF(
					SunDryingAspal = 1,
					'Yes',
					IF(SunDryingAspal = 2, 'No', '')
				  ) AS 'Pengeringan di atas aspal',
				  IF(
					DryingAlat = 1,
					'Yes',
					IF(DryingAlat = 2, 'No', '')
				  ) AS 'Pengeringan dengan alat',
				  IF(
					SunDryingAlas = 1,
					'Yes',
					IF(SunDryingAlas = 2, 'No', '')
				  ) AS 'Pengeringan menggunakan alas (terpal, plastik, anyaman daun kelapa)',
				  IF(
					BeanDryHygienic = 1,
					'Yes',
					IF(BeanDryHygienic = 2, 'No', '')
				  ) AS 'Apakah anda selalu memastikan jika biji kakao anda dikeringkan dengan cara yang higienis dan terhindar dari pencemaran asap, kotoran, benda asing dll yang dapat mempengaruhi mutu',
				  IF(
					TidakJemur = 1,
					'Lebih menguntungkan menjual biji basah',
					IF(
					  TidakJemur = 2,
					  'Lebih mudah dikerjakan',
					  IF(
						TidakJemur = 3,
						'Lebih cepat memperoleh uang',
						IF(
						  TidakJemur = 4,
						  'Sulit menjemur karena musim hujan',
						  IF(
							TidakJemur = 5,
							'Tidak cukup waktu and perlu bantuan tenaga kerja',
							IF(TidakJemur = 6, 'Lain-lain', '')
						  )
						)
					  )
					)
				  ) AS 'Jika tidak, mengapa anda tidak menjemur biji kakao',
				  IF(
					DryMoistureStandard = 1,
					'Yes',
					IF(DryMoistureStandard = 2, 'No', '')
				  ) AS '(C) Apakah biji kakao anda keringkan hingga mencapai kadar kelembaban sesuai standar kelompok',
				  IF(
					ImplementBeanRemainDry = 1,
					'Yes',
					IF(
					  ImplementBeanRemainDry = 2,
					  'No',
					  ''
					)
				  ) AS 'Apakah anda menerapkan langkah-langkah untuk memastikan agar biji kakao tetap kering dan terhindar dari basah selama proses pengangkutan dan penyimpanan',
				  IF(
					Sortasi = 1,
					'Yes',
					IF(Sortasi = 2, 'No', '')
				  ) AS 'Apakah anda memisahkan biji berkualitas bagus dan berkualitas jelek/rendah sebelum menjualnya',
				  IF(
					NoSortasi = 1,
					'Tidak ada perbedaan harga',
					IF(
					  NoSortasi = 2,
					  'Terlalu banyak menghabiskan waktu',
					  IF(
						NoSortasi = 3,
						'Tidak banyak biji berkualitas bagus',
						IF(
						  NoSortasi = 4,
						  'Tidak tahu cara memisahkan biji',
						  ''
						)
					  )
					)
				  ) AS 'Jika tidak, mengapa anda tidak melakukan pemisahan biji',
				  IF(
					CocoaBuyers = 1,
					'Pedagang pengumpul di kampung',
					IF(
					  CocoaBuyers = 2,
					  'Pedagang pengumpul di kecamatan',
					  IF(
						CocoaBuyers = 3,
						'Pedagangan kabupaten/eksportir',
						IF(
						  CocoaBuyers = 4,
						  'Kelompok petani',
						  ''
						)
					  )
					)
				  ) AS 'Biasanya menjual biji kakao kepada',
				  IF(
					AntarSendiri = 1,
					'Yes',
					IF(AntarSendiri = 2, 'No', '')
				  ) AS 'Apakah anda mengantar kakao sendiri',
				  Distance AS 'Jika ya, berapa jarak dari rumah anda (m)',
				  tbl_ph.`DateCreated`,
				  h.`UserRealName` AS CreatedBy,
				  tbl_ph.`DateUpdated`,
				  i.`UserRealName` AS LastModifiedBy
			FROM
				`ktv_certification_pre_afl_target` tar
				LEFT JOIN sys_user us ON tar.`PICUserID` = us.`UserId`
				LEFT JOIN ktv_ims ims ON tar.`IMSID` = ims.`IMSID`

				INNER JOIN (
					SELECT
						tbl_garden.IMSID
						, ph.*
					FROM
						(
							SELECT
								a.`IMSID`
								, gar.`FarmerID`
								, MAX(gar.`SurveyNr`) AS SurveyNr
							FROM
								ktv_certification_pre_afl_garden a
								LEFT JOIN ktv_members_garden gar ON 1=1
									AND a.`FarmerID` = gar.`FarmerID`
									AND a.`SurveyNr` = gar.`SurveyNr`
									AND a.`GardenNr` = gar.`GardenNr`
							WHERE
								a.`IMSID` = ?
								AND gar.`StatusCode` = 'active'
								AND (
									DATE(gar.`DateUpdated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
									OR
									DATE(gar.`DateCreated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
								)
							GROUP BY gar.`FarmerID`
						) AS tbl_garden
						LEFT JOIN ktv_members_post_harvest ph ON 1=1
							AND tbl_garden.FarmerID = ph.`FarmerID`
							AND tbl_garden.SurveyNr = ph.`SurveyNr`
					WHERE
						ph.`StatusCode` = 'active'
						AND (
							DATE(ph.`DateUpdated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
							OR
							DATE(ph.`DateCreated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
						)
				) AS tbl_ph ON 1=1
					AND tar.`IMSID` = tbl_ph.IMSID
					AND tar.`FarmerID` = tbl_ph.FarmerID

				LEFT JOIN ktv_members b ON b.`FarmerID` = tar.`FarmerID`
				LEFT JOIN ktv_cpg c ON c.`CPGid` = b.`CPGid`
				LEFT JOIN ktv_village g ON g.`VillageID` = b.`VillageID`
				LEFT JOIN ktv_subdistrict f ON f.`SubDistrictID` = g.SubDistrictID
				LEFT JOIN ktv_district e ON e.`DistrictID` = f.DistrictID
				LEFT JOIN ktv_province d ON d.`ProvinceID` = e.ProvinceID
				LEFT JOIN sys_user h ON h.UserId = tbl_ph.`CreatedBy`
				LEFT JOIN sys_user i ON i.UserId = tbl_ph.`LastModifiedBy`

			WHERE
				tar.`IMSID` = ?
				AND tar.`PICUserID` = ?
			ORDER BY tar.`FarmerID` ASC
			";
		$query = $this->db->query($sql, array($IMSID, $EventDate, $EventDate, $EventDate, $EventDate, $IMSID, $UserID));
		$data  = $query->result_array();

		//target
		$sql = "SELECT
				COUNT(*) AS BANYAK
			FROM
				`ktv_certification_pre_afl_target` tar
			WHERE
				tar.`IMSID` = ?
				AND tar.`PICUserID` = ?";
		$query   = $this->db->query($sql, array($IMSID, $UserID));
		$dataRow = $query->row_array();

		$arrReturn['dataList'] = $data;
		$arrReturn['capai']    = count($data);
		$arrReturn['target']   = $dataRow['BANYAK'];
		return $arrReturn;
	}

	public function getCertificationAflP1Summary($IMSID)
	{
		$sql = "SELECT
			  a.`FarmerID` AS 'ID Petani',
			  b.ExtFarmerID AS 'ID Petani Eksternal',
			  b.`FarmerName` AS 'Nama Petani',
			  c.`CPGid` AS 'ID Kelompok',
			  c.`GroupName` AS 'Kelompok Tani',
			  d.`Province` AS 'Propinsi',
			  e.`District` AS 'Kabupaten',
			  f.`SubDistrict` AS 'Kecamatan',
			  g.`Village` AS 'Desa',
			  a.`GardenNr` AS 'Nr Kebun',
			  a.`SurveyNr` AS 'Nr Survey',
			  IF(
				`Certification` = 1,
				'UTZ',
				IF(
				  `Certification` = 2,
				  'Rainforest',
				  IF(
					`Certification` = 3,
					'Fairtrade',
					IF(`Certification` = 4, 'Organic', '')
				  )
				)
			  ) AS 'Program Sertifikasi',
			  `CertificationHolderJenis` AS 'Jenis Pemegang Sertifikasi',
			  IF(
				`CertificationHolderJenis` = 'Organisasi Petani',
				(SELECT
				  CoopName
				FROM
				  `ktv_cooperatives`
				WHERE CoopID = `CertificationHolder`),
				''
			  ) AS 'Pemegang Sertifikasi',
			  a.`Year` AS 'Tahun Sertifikasi',
			  `CandidateSelection` AS 'Seleksi Kandidat',
			  `ICSDate` AS 'Tgl Audit Internal',
			  `DateRevisionAudit` AS 'Tgl Revisi Audit Internal',
			  `CommentAudit` AS 'Komentar Audit',
			  `RecommendationAudit` AS 'Rekomendasi Audit',
			  IF(
				`StatusAudit` = 1,
				'Passed',
				IF(
				  `StatusAudit` = 2,
				  'Not Passed',
				  IF(
					`StatusAudit` = 3,
					'Passed with Requirement',
					''
				  )
				)
			  ) AS 'Status Audit Internal',
			  a.`ExternalDate` AS 'Tgl Audit Eksternal',
			  a.`CertificationStart` AS 'Tgl Awal Sertifikasi',
			  a.`CertificationEnd` AS 'Tgl Akhir Sertifikasi',
			  a.`CertificationExtension` AS 'Tgl Perpanjangan Sertifikasi',
			  j.`PersonNm` AS 'Nama Auditor',
			  `InspectorSignature` AS 'Ttd Auditor',
			  k.`PersonNm` AS 'Nama Komite Audit',
			  `AuditCommiteeSignature` AS 'Ttd Komite Audit',
			  l.`PersonNm` AS 'Nama IMS Manager',
			  a.`IMSManagerSignature` AS 'Ttd IMS Manager',
			  a.`FarmerSignature` AS 'Ttd Petani',
			  a.`DateCreated`,
			  h.`UserRealName` AS CreatedBy,
			  a.`DateUpdated`,
			  i.`UserRealName` AS LastModifiedBy
			FROM
			  `ktv_certification` a
			  LEFT JOIN ktv_members b
				ON b.`FarmerID` = a.`FarmerID`
			  LEFT JOIN ktv_cpg c
				ON c.`CPGid` = b.`CPGid`
				LEFT JOIN ktv_village g ON g.`VillageID` = b.`VillageID`
				LEFT JOIN ktv_subdistrict f ON f.`SubDistrictID` = g.SubDistrictID
				LEFT JOIN ktv_district e ON e.`DistrictID` = f.DistrictID
				LEFT JOIN ktv_province d ON d.`ProvinceID` = e.ProvinceID
			  LEFT JOIN sys_user h
				ON h.UserId = a.`CreatedBy`
			  LEFT JOIN sys_user i
				ON i.UserId = a.`LastModifiedBy`
			  LEFT JOIN ktv_persons j
				ON j.`PersonID` = a.`InspectorID`
			  LEFT JOIN ktv_persons k
				ON k.`PersonID` = a.`AuditCommiteeID`
			  LEFT JOIN ktv_persons l
				ON l.`PersonID` = a.`IMSManagerID`

			  INNER JOIN `ktv_certification_afl_garden` afl ON 1=1
				AND afl.`FarmerID` = a.`FarmerID`
				AND afl.`CertGardenNr` = a.`GardenNr`
				AND afl.`CertSurveyNr` = a.`SurveyNr`

				INNER JOIN ktv_ims ims ON 1=1
					AND afl.IMSID = ims.IMSID

				INNER JOIN ktv_certification_holders hold ON 1=1
					AND ims.CertHolderID = hold.CertHolderID
					AND a.Certification = hold.CertProgID

			WHERE
				b.`StatusCode` = 'active'
				AND afl.`IMSID` = ?
				AND afl.CertStatusAudit != '-'
			ORDER BY a.`FarmerID` ASC";
		$query = $this->db->query($sql, array($IMSID));
		return $query->result_array();
	}

	public function getCertificationAflP1SummaryFaProgress($IMSID, $UserID, $CertEventDate)
	{
		$arrReturn = array();

		//target
		$sql = "SELECT
					COUNT(*) AS BANYAK
				FROM
					`ktv_certification_pre_afl_target` tar
					LEFT JOIN ktv_certification_pre_afl_garden aflg ON 1=1
						AND tar.`IMSID` = aflg.`IMSID`
						AND tar.`FarmerID` = aflg.`FarmerID`
				WHERE
					tar.`IMSID` = ?
					AND tar.`PICUserID` = ?";
		$query      = $this->db->query($sql, array($IMSID, $UserID));
		$dataTarget = $query->row_array();

		//realnya sudah dapat berapa
		$sql = "SELECT
					tbl_sub.CAPAI,
					LEAST(
						DATE(IF(tbl_sub.BeginTgl='0000-00-00',tbl_sub.BeginTglUpdated,tbl_sub.BeginTgl)),
						DATE(IF(tbl_sub.BeginTglUpdated='0000-00-00',tbl_sub.BeginTgl,tbl_sub.BeginTglUpdated))
					) AS BeginTgl,
					GREATEST(DATE(tbl_sub.EndTgl),DATE(tbl_sub.EndTglUpdated)) AS EndTgl
				FROM
				(
				SELECT
					COUNT(cert.`FarmerID`) AS CAPAI,
					DATE(MIN(cert.`DateCreated`)) AS BeginTgl,
					DATE(MIN(cert.`DateUpdated`)) AS BeginTglUpdated,
					DATE(MAX(cert.`DateCreated`)) AS EndTgl,
					DATE(MAX(cert.`DateUpdated`)) AS EndTglUpdated
				FROM
					`ktv_certification_pre_afl_target` tar
					LEFT JOIN ktv_ims ims ON tar.`IMSID` = ims.`IMSID`
					LEFT JOIN ktv_certification_holders hold ON ims.CertHolderID = hold.CertHolderID

					LEFT JOIN ktv_certification_pre_afl_garden aflg ON 1=1
						AND tar.`IMSID` = aflg.`IMSID`
						AND tar.`FarmerID` = aflg.`FarmerID`
					INNER JOIN ktv_certification cert ON 1=1
						AND aflg.`FarmerID` = cert.`FarmerID`
						AND aflg.`GardenNr` = cert.`GardenNr`
						AND aflg.`SurveyNr` = cert.`SurveyNr`
						AND hold.`CertProgID` = cert.`Certification`
						AND (
							DATE(cert.`DateUpdated`) >= DATE_FORMAT( ims.`CertEventDate` - INTERVAL 6 MONTH, '%Y/%m/%d' )
							OR
							DATE(cert.`DateCreated`) >= DATE_FORMAT( ims.`CertEventDate` - INTERVAL 6 MONTH, '%Y/%m/%d' )
						)

					LEFT JOIN ktv_members b ON b.`FarmerID` = tar.`FarmerID`
				WHERE
					tar.`IMSID` = ?
					AND tar.`PICUserID` = ?
					AND cert.GardenNr IS NOT NULL
					AND b.StatusCode = 'active'
				ORDER BY tar.`FarmerID` ASC
				) AS tbl_sub";
		$query     = $this->db->query($sql, array($IMSID, $UserID));
		$dataCapai = $query->row_array();

		//cek apakah data tanggal rangenya ada
		if ($dataCapai['BeginTgl'] != "0000-00-00" && $dataCapai['BeginTgl'] != "" && $dataCapai['EndTgl'] != "0000-00-00" && $dataCapai['EndTgl'] != "") {
			//progress data by tanggal
			$data      = array();
			$dateRange = $this->getDateRange($dataCapai['BeginTgl'], $dataCapai['EndTgl'], '+1 day', 'Y-m-d');
			foreach ($dateRange as $key => $tglProses) {
				$data[$key]['tanggal'] = $tglProses;

				//query capainya perhari
				$sql = "SELECT
							COUNT(cert.`FarmerID`) AS CAPAI
						FROM
							`ktv_certification_pre_afl_target` tar
							LEFT JOIN ktv_ims ims ON tar.`IMSID` = ims.`IMSID`
							LEFT JOIN ktv_certification_holders hold ON ims.CertHolderID = hold.CertHolderID

							LEFT JOIN ktv_certification_pre_afl_garden aflg ON 1=1
								AND tar.`IMSID` = aflg.`IMSID`
								AND tar.`FarmerID` = aflg.`FarmerID`
							INNER JOIN ktv_certification cert ON 1=1
								AND aflg.`FarmerID` = cert.`FarmerID`
								AND aflg.`GardenNr` = cert.`GardenNr`
								AND aflg.`SurveyNr` = cert.`SurveyNr`
								AND hold.`CertProgID` = cert.`Certification`
								AND (
									DATE(cert.`DateUpdated`) >= DATE_FORMAT( ims.`CertEventDate` - INTERVAL 6 MONTH, '%Y/%m/%d' )
									OR
									DATE(cert.`DateCreated`) >= DATE_FORMAT( ims.`CertEventDate` - INTERVAL 6 MONTH, '%Y/%m/%d' )
								)

							LEFT JOIN ktv_members b ON b.`FarmerID` = tar.`FarmerID`
						WHERE
							tar.`IMSID` = ?
							AND tar.`PICUserID` = ?
							AND cert.GardenNr IS NOT NULL
							AND b.StatusCode = 'active'
							AND (
								DATE(cert.`DateCreated`) = ?
								OR
								DATE(cert.`DateUpdated`) = ?
							)
						ORDER BY tar.`FarmerID` ASC";
				$query       = $this->db->query($sql, array($IMSID, $UserID, $tglProses, $tglProses));
				$dataPerHari = $query->row_array();

				$data[$key]['capai'] = $dataPerHari['CAPAI'];
			}
		} else {
			$data = array();
		}

		$arrReturn['dataList'] = $data;
		$arrReturn['capai']    = $dataCapai['CAPAI'];
		$arrReturn['target']   = $dataTarget['BANYAK'];
		return $arrReturn;
	}

	public function getCertificationAflP1SummaryFa($IMSID, $UserID, $EventDate)
	{
		$arrReturn = array();

		$sql = "SELECT
				tar.`FarmerID` AS 'ID Petani',
				b.ExtFarmerID AS 'ID Petani Eksternal',
				b.`FarmerName` AS 'Nama Petani',
				c.`CPGid` AS 'ID Kelompok',
				c.`GroupName` AS 'Kelompok Tani',
				d.`Province` AS 'Propinsi',
				e.`District` AS 'Kabupaten',
				f.`SubDistrict` AS 'Kecamatan',
				g.`Village` AS 'Desa',
				cert.`GardenNr` AS 'Nr Kebun',
				cert.`SurveyNr` AS 'Nr Survey',
				IF(
				`Certification` = 1,
				'UTZ',
				IF(
				  `Certification` = 2,
				  'Rainforest',
				  IF(
					`Certification` = 3,
					'Fairtrade',
					IF(`Certification` = 4, 'Organic', '')
				  )
				)
				) AS 'Program Sertifikasi',
				`CertificationHolderJenis` AS 'Jenis Pemegang Sertifikasi',
				IF(
				`CertificationHolderJenis` = 'Organisasi Petani',
				(SELECT
				  CoopName
				FROM
				  `ktv_cooperatives`
				WHERE CoopID = `CertificationHolder`),
				''
				) AS 'Pemegang Sertifikasi',
				cert.`Year` AS 'Tahun Sertifikasi',
				`CandidateSelection` AS 'Seleksi Kandidat',
				`ICSDate` AS 'Tgl Audit Internal',
				`DateRevisionAudit` AS 'Tgl Revisi Audit Internal',
				`CommentAudit` AS 'Komentar Audit',
				`RecommendationAudit` AS 'Rekomendasi Audit',
				IF(
				`StatusAudit` = 1,
				'Passed',
				IF(
				  `StatusAudit` = 2,
				  'Not Passed',
				  IF(
					`StatusAudit` = 3,
					'Passed with Requirement',
					''
				  )
				)
				) AS 'Status Audit Internal',
				cert.`ExternalDate` AS 'Tgl Audit Eksternal',
				cert.`CertificationStart` AS 'Tgl Awal Sertifikasi',
				cert.`CertificationEnd` AS 'Tgl Akhir Sertifikasi',
				cert.`CertificationExtension` AS 'Tgl Perpanjangan Sertifikasi',
				j.`PersonNm` AS 'Nama Auditor',
				`InspectorSignature` AS 'Ttd Auditor',
				k.`PersonNm` AS 'Nama Komite Audit',
				`AuditCommiteeSignature` AS 'Ttd Komite Audit',
				l.`PersonNm` AS 'Nama IMS Manager',
				cert.`IMSManagerSignature` AS 'Ttd IMS Manager',
				cert.`FarmerSignature` AS 'Ttd Petani',
				cert.`DateCreated`,
				h.`UserRealName` AS CreatedBy,
				cert.`DateUpdated`,
				i.`UserRealName` AS LastModifiedBy
			FROM
				`ktv_certification_pre_afl_target` tar
				LEFT JOIN sys_user us ON tar.`PICUserID` = us.`UserId`
				LEFT JOIN ktv_ims ims ON tar.`IMSID` = ims.`IMSID`
				LEFT JOIN ktv_certification_holders hold ON ims.CertHolderID = hold.CertHolderID

				LEFT JOIN ktv_certification_pre_afl_garden aflg ON 1=1
					AND tar.`IMSID` = aflg.`IMSID`
					AND tar.`FarmerID` = aflg.`FarmerID`
				INNER JOIN ktv_certification cert ON 1=1
					AND aflg.`FarmerID` = cert.`FarmerID`
					AND aflg.`GardenNr` = cert.`GardenNr`
					AND aflg.`SurveyNr` = cert.`SurveyNr`
					AND hold.`CertProgID` = cert.`Certification`
					AND (
						DATE(cert.`DateUpdated`) >= DATE_FORMAT( ims.`CertEventDate` - INTERVAL 6 MONTH, '%Y/%m/%d' )
						OR
						DATE(cert.`DateCreated`) >= DATE_FORMAT( ims.`CertEventDate` - INTERVAL 6 MONTH, '%Y/%m/%d' )
					)

				LEFT JOIN ktv_members b ON b.`FarmerID` = tar.`FarmerID`
				LEFT JOIN ktv_cpg c ON c.`CPGid` = b.`CPGid`
				LEFT JOIN ktv_village g ON g.`VillageID` = b.`VillageID`
				LEFT JOIN ktv_subdistrict f ON f.`SubDistrictID` = g.SubDistrictID
				LEFT JOIN ktv_district e ON e.`DistrictID` = f.DistrictID
				LEFT JOIN ktv_province d ON d.`ProvinceID` = e.ProvinceID
				LEFT JOIN sys_user h ON h.UserId = cert.`CreatedBy`
				LEFT JOIN sys_user i ON i.UserId = cert.`LastModifiedBy`
				LEFT JOIN ktv_persons j ON j.`PersonID` = cert.`InspectorID`
				LEFT JOIN ktv_persons k ON k.`PersonID` = cert.`AuditCommiteeID`
				LEFT JOIN ktv_persons l ON l.`PersonID` = cert.`IMSManagerID`
			WHERE
				tar.`IMSID` = ?
				AND tar.`PICUserID` = ?
				AND cert.GardenNr IS NOT NULL
				AND b.StatusCode = 'active'
			ORDER BY tar.`FarmerID` ASC
			";
		$query = $this->db->query($sql, array($IMSID, $UserID));
		$data  = $query->result_array();

		//target
		$sql = "SELECT
					COUNT(*) AS BANYAK
				FROM
					`ktv_certification_pre_afl_target` tar
					LEFT JOIN ktv_certification_pre_afl_garden aflg ON 1=1
						AND tar.`IMSID` = aflg.`IMSID`
						AND tar.`FarmerID` = aflg.`FarmerID`
				WHERE
					tar.`IMSID` = ?
					AND tar.`PICUserID` = ?";
		$query   = $this->db->query($sql, array($IMSID, $UserID));
		$dataRow = $query->row_array();

		$arrReturn['dataList'] = $data;
		$arrReturn['capai']    = count($data);
		$arrReturn['target']   = $dataRow['BANYAK'];
		return $arrReturn;
	}

	public function getAuditLogAflP1Summary($IMSID)
	{
		$sql = "SELECT
			  a.`FarmerID` AS 'ID Petani',
			  b.ExtFarmerID AS 'ID Petani Eksternal',
			  b.`FarmerName` AS 'Nama Petani',
			  c.`CPGid` AS 'ID Kelompok',
			  c.`GroupName` AS 'Kelompok Tani',
			  d.`Province` AS 'Propinsi',
			  e.`District` AS 'Kabupaten',
			  f.`SubDistrict` AS 'Kecamatan',
			  g.`Village` AS 'Desa',
			  a.`GardenNr` AS 'Nr Kebun',
			  a.`SurveyNr` AS 'Nr Survey',
			  IF(
				`Certification` = 1,
				'UTZ',
				IF(
				  `Certification` = 2,
				  'Rainforest',
				  IF(
					`Certification` = 3,
					'Fairtrade',
					IF(`Certification` = 4, 'Organic', '')
				  )
				)
			  ) AS 'Program Sertifikasi',
			  `ICSDate` AS 'Tgl Audit Internal',
			  `DateRevisionAudit` AS 'Tgl Revisi Audit Internal',
			  `CommentAudit` AS 'Komentar Audit',
			  `RecommendationAudit` AS 'Rekomendasi Audit',
			  IF(
				`StatusAudit` = 1,
				'Passed',
				IF(
				  `StatusAudit` = 2,
				  'Not Passed',
				  IF(
					`StatusAudit` = 3,
					'Passed with Requirement',
					''
				  )
				)
			  ) AS 'Status Audit Internal',

			  (
				SELECT
					sub_b.PersonNm
				FROM
					ktv_staffs sub_a
					INNER JOIN ktv_persons sub_b ON sub_a.PersonID = sub_b.PersonID
				WHERE
					sub_a.StaffID = a.`InspectorID`
				LIMIT 1
			  ) AS 'Nama Auditor',
			  `InspectorSignature` AS 'Ttd Auditor',

			  (
				SELECT
					sub_b.PersonNm
				FROM
					ktv_staffs sub_a
					INNER JOIN ktv_persons sub_b ON sub_a.PersonID = sub_b.PersonID
				WHERE
					sub_a.StaffID = a.AuditCommiteeID
				LIMIT 1
			  ) AS 'Nama Komite Audit',
			  `AuditCommiteeSignature` AS 'Ttd Komite Audit',

			  (
				SELECT
					sub_b.PersonNm
				FROM
					ktv_staffs sub_a
					INNER JOIN ktv_persons sub_b ON sub_a.PersonID = sub_b.PersonID
				WHERE
					sub_a.StaffID = a.IMSManagerID
				LIMIT 1
			  ) AS 'Nama IMS Manager',
			  a.`IMSManagerSignature` AS 'Ttd IMS Manager',

			  a.`FarmerSignature` AS 'Ttd Petani',
			  IF(
				ParticipateChildEducation = 1,
				'Yes',
				IF(
				  ParticipateChildEducation = 2,
				  'No',
				  ''
				)
			  ) AS '(C) Apakah Anda berpartisipasi dalam usaha untuk memastikan agar semua anak usia sekolah mendapatkan akses pendidikan',
			  IF(
				CutWageForDisciplinary = 1,
				'Yes',
				IF(
				  CutWageForDisciplinary = 2,
				  'No',
				  ''
				)
			  ) AS '(C) Apakah Anda mengalami pemotongan upah kerja untuk tujuan disipliner',
			  IF(
				DoCutWageForWorker = 1,
				'Yes',
				IF(DoCutWageForWorker = 2, 'No', '')
			  ) AS '(C) Apakah Anda melakukan pemotongan upah pekerja anda dengan tujuan disipliner',
			  IF(
				WagePaidByPerformance = 1,
				'Yes',
				IF(WagePaidByPerformance = 2, 'No', '')
			  ) AS '(C) Apakah upah Anda dibayarkan sesuai kinerja atau kesepakatan tanpa adanya diskiriminasi',
			  IF(
				PayingWorkerWageByPerformance = 1,
				'Yes',
				IF(
				  PayingWorkerWageByPerformance = 2,
				  'No',
				  ''
				)
			  ) AS '(C) Apakah Anda membayar upah pekerja anda sesuai kinerja atau kesepakatan tanpa adanya diskiriminasi',
			  IF(
				HandlingFirstAidInGarden = 1,
				'Yes',
				IF(
				  HandlingFirstAidInGarden = 2,
				  'No',
				  ''
				)
			  ) AS '(C) Anda memahami bagaimana penanganan pertolongan pertama pada kecelakaan di kebun',
			  IF(
				FirstAidKitLocation = 1,
				'Yes',
				IF(FirstAidKitLocation = 2, 'No', '')
			  ) AS '(C) Apakah kotak pertolongan pertama (P3K) tersedia di pusat lokasi produk, pengolahan dan pemeliharaan',
			  IF(
				WorkerNotHandlePesticide = 1,
				'Yes',
				IF(
				  WorkerNotHandlePesticide = 2,
				  'No',
				  ''
				)
			  ) AS '(C) Apakah Anda sudah memastikan para pengurus kelompok, anggota kelompok, dan anggota kelompok yang termasuk pekerja, yang berusia di bawah 1 tahun, atau hamil dan sedang menyusui tidak boleh menangani pestisida',
			  IF(
				WorkerAccessSafeDrinkingWater = 1,
				'Yes',
				IF(
				  WorkerAccessSafeDrinkingWater = 2,
				  'No',
				  ''
				)
			  ) AS '(C) Apakah staf kelompok, anggota kelompok, dan anggota kelompok yang merupakan pekerja mempunyai akses terhadap air minum yang aman.',
			  IF(
				BufferZoneGarden = 1,
				'Yes',
				IF(BufferZoneGarden = 2, 'No', '')
			  ) AS '(C) Di kebun ini terdapat sebuah zona penyangga berisi vegetasi asli setidaknya selebar 5 meter  dipelihara di sepanjang batas badan air musiman dan permanen untuk mengurangi erosi, membatasi pencemaran pestisida dan pupuk, dan melindungi habitat satwa liar. Di lahan yang luasnya kurang dari 2 Ha, terdapat zona penyangga  dengan lebar setidaknya 2 meter',
			  IF(
				LandOpeningForest = 1,
				'Yes',
				IF(LandOpeningForest = 2, 'No', '')
			  ) AS '(C) Apakah lahan Anda dibuat dengan membuka hutan pada tahun 2008 atau sesudahnya',
			  IF(
				LandOpeningForestCertificate = 1,
				'Yes',
				IF(
				  LandOpeningForestCertificate = 2,
				  'No',
				  ''
				)
			  ) AS '(C) Jika lahan Anda dibuat dengan membuka hutan, apakah Anda memiliki surat kepemilikan secara resmi dari pemerintah',
			  IF(
				IdentifyProtectRareSpecies = 1,
				'Yes',
				IF(
				  IdentifyProtectRareSpecies = 2,
				  'No',
				  ''
				)
			  ) AS '(C) Apakah Anda melakukan identifikasi dan perlindungan terhadap spesies langka dan terancam punah di sekitar Anda',
			  a.`DateCreated`,
			  h.`UserRealName` AS CreatedBy,
			  a.`DateUpdated`,
			  i.`UserRealName` AS LastModifiedBy
			FROM
			  `ktv_certification_audit_log` a
			  LEFT JOIN ktv_members b
				ON b.`FarmerID` = a.`FarmerID`
			  LEFT JOIN ktv_cpg c
				ON c.`CPGid` = b.`CPGid`
				LEFT JOIN ktv_village g ON g.`VillageID` = b.`VillageID`
				LEFT JOIN ktv_subdistrict f ON f.`SubDistrictID` = g.SubDistrictID
				LEFT JOIN ktv_district e ON e.`DistrictID` = f.DistrictID
				LEFT JOIN ktv_province d ON d.`ProvinceID` = e.ProvinceID
			  LEFT JOIN sys_user h
				ON h.UserId = a.`CreatedBy`
			  LEFT JOIN sys_user i
				ON i.UserId = a.`LastModifiedBy`

			  #LEFT JOIN ktv_persons j
			  #  ON j.`PersonID` = a.`InspectorID`
			  #LEFT JOIN ktv_persons k
			  #  ON k.`PersonID` = a.`AuditCommiteeID`
			  #LEFT JOIN ktv_persons l
			  #  ON l.`PersonID` = a.`IMSManagerID`

			  INNER JOIN `ktv_certification_afl_garden` afl ON 1=1
				AND afl.`FarmerID` = a.`FarmerID`
				AND afl.`CertGardenNr` = a.`GardenNr`
				AND afl.`CertSurveyNr` = a.`SurveyNr`

				INNER JOIN ktv_ims ims ON 1=1
					AND afl.IMSID = ims.IMSID

				INNER JOIN ktv_certification_holders hold ON 1=1
					AND ims.CertHolderID = hold.CertHolderID
					AND a.Certification = hold.CertProgID

			WHERE
				b.`StatusCode` = 'active'
				AND afl.`StatusCode` = 'active'
				AND afl.`IMSID` = ?
				AND afl.CertStatusAudit != '-'
			ORDER BY a.`FarmerID` ASC
			";
		$query = $this->db->query($sql, array($IMSID));
		return $query->result_array();
	}

	public function getAuditLogAflP1SummaryFaProgress($IMSID, $UserID, $CertEventDate)
	{
		$arrReturn = array();

		//target
		$sql = "SELECT
					COUNT(*) AS BANYAK
				FROM
					`ktv_certification_pre_afl_target` tar
					LEFT JOIN ktv_certification_pre_afl_garden aflg ON 1=1
						AND tar.`IMSID` = aflg.`IMSID`
						AND tar.`FarmerID` = aflg.`FarmerID`
				WHERE
					tar.`IMSID` = ?
					AND tar.`PICUserID` = ?";
		$query      = $this->db->query($sql, array($IMSID, $UserID));
		$dataTarget = $query->row_array();

		//realnya sudah dapat berapa
		$sql = "SELECT
					tbl_sub.CAPAI,
					LEAST(
						DATE(IF(tbl_sub.BeginTgl='0000-00-00',tbl_sub.BeginTglUpdated,tbl_sub.BeginTgl)),
						DATE(IF(tbl_sub.BeginTglUpdated='0000-00-00',tbl_sub.BeginTgl,tbl_sub.BeginTglUpdated))
					) AS BeginTgl,
					GREATEST(DATE(tbl_sub.EndTgl),DATE(tbl_sub.EndTglUpdated)) AS EndTgl
				FROM
				(
				SELECT
					COUNT(tbl_au.FarmerID) AS CAPAI,
					DATE(MIN(tbl_au.`DateCreated`)) AS BeginTgl,
					DATE(MIN(tbl_au.`DateUpdated`)) AS BeginTglUpdated,
					DATE(MAX(tbl_au.`DateCreated`)) AS EndTgl,
					DATE(MAX(tbl_au.`DateUpdated`)) AS EndTglUpdated
				FROM
					`ktv_certification_pre_afl_target` tar
					LEFT JOIN ktv_ims ims ON tar.`IMSID` = ims.`IMSID`
					LEFT JOIN ktv_certification_holders hold ON ims.CertHolderID = hold.CertHolderID

					LEFT JOIN ktv_certification_pre_afl_garden aflg ON 1=1
						AND tar.`IMSID` = aflg.`IMSID`
						AND tar.`FarmerID` = aflg.`FarmerID`

					INNER JOIN (
						SELECT
							au.*
						FROM
							(SELECT
								FarmerID,
								GardenNr,
								SurveyNr,
								Certification,
								MAX(ICSDate) ICSDate
							FROM
								ktv_certification_audit_log
							WHERE Certification != 0
								AND GardenNr != 0
							GROUP BY FarmerID,
								GardenNr,
								SurveyNr,
								Certification) dt
							INNER JOIN ktv_certification_audit_log au
								ON dt.FarmerID = au.FarmerID
								AND dt.GardenNr = au.GardenNr
								AND dt.SurveyNr = au.SurveyNr
								AND dt.Certification = au.Certification
								AND dt.ICSDate = au.ICSDate
					) AS tbl_au ON 1=1
						AND aflg.`FarmerID` = tbl_au.FarmerID
						AND aflg.`GardenNr` = tbl_au.GardenNr
						AND aflg.`SurveyNr` = tbl_au.SurveyNr
						AND hold.`CertProgID` = tbl_au.Certification
						AND (
							DATE(tbl_au.`DateUpdated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
							OR
							DATE(tbl_au.`DateCreated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
						)

					LEFT JOIN ktv_members b ON b.`FarmerID` = tbl_au.`FarmerID`
				WHERE
					tar.`IMSID` = ?
					AND tar.`PICUserID` = ?
					AND b.StatusCode = 'active'
				ORDER BY tar.`FarmerID` ASC
				) AS tbl_sub";
		$query     = $this->db->query($sql, array($CertEventDate, $CertEventDate, $IMSID, $UserID));
		$dataCapai = $query->row_array();

		//cek apakah data tanggal rangenya ada
		if ($dataCapai['BeginTgl'] != "0000-00-00" && $dataCapai['BeginTgl'] != "" && $dataCapai['EndTgl'] != "0000-00-00" && $dataCapai['EndTgl'] != "") {
			//progress data by tanggal
			$data      = array();
			$dateRange = $this->getDateRange($dataCapai['BeginTgl'], $dataCapai['EndTgl'], '+1 day', 'Y-m-d');
			foreach ($dateRange as $key => $tglProses) {
				$data[$key]['tanggal'] = $tglProses;

				//query capainya perhari
				$sql = "SELECT
							COUNT(tbl_au.FarmerID) AS CAPAI
						FROM
							`ktv_certification_pre_afl_target` tar
							LEFT JOIN ktv_ims ims ON tar.`IMSID` = ims.`IMSID`
							LEFT JOIN ktv_certification_holders hold ON ims.CertHolderID = hold.CertHolderID

							LEFT JOIN ktv_certification_pre_afl_garden aflg ON 1=1
								AND tar.`IMSID` = aflg.`IMSID`
								AND tar.`FarmerID` = aflg.`FarmerID`

							INNER JOIN (
								SELECT
									au.*
								FROM
									(SELECT
										FarmerID,
										GardenNr,
										SurveyNr,
										Certification,
										MAX(ICSDate) ICSDate
									FROM
										ktv_certification_audit_log
									WHERE Certification != 0
										AND GardenNr != 0
									GROUP BY FarmerID,
										GardenNr,
										SurveyNr,
										Certification) dt
									INNER JOIN ktv_certification_audit_log au
										ON dt.FarmerID = au.FarmerID
										AND dt.GardenNr = au.GardenNr
										AND dt.SurveyNr = au.SurveyNr
										AND dt.Certification = au.Certification
										AND dt.ICSDate = au.ICSDate
							) AS tbl_au ON 1=1
								AND aflg.`FarmerID` = tbl_au.FarmerID
								AND aflg.`GardenNr` = tbl_au.GardenNr
								AND aflg.`SurveyNr` = tbl_au.SurveyNr
								AND hold.`CertProgID` = tbl_au.Certification
								AND (
									DATE(tbl_au.`DateUpdated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
									OR
									DATE(tbl_au.`DateCreated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
								)

							LEFT JOIN ktv_members b ON b.`FarmerID` = tbl_au.`FarmerID`
						WHERE
							tar.`IMSID` = ?
							AND tar.`PICUserID` = ?
							AND b.StatusCode = 'active'
							AND (
								DATE(tbl_au.`DateCreated`) = ?
								OR
								DATE(tbl_au.`DateUpdated`) = ?
							)
						ORDER BY tar.`FarmerID` ASC";
				$query       = $this->db->query($sql, array($CertEventDate, $CertEventDate, $IMSID, $UserID, $tglProses, $tglProses));
				$dataPerHari = $query->row_array();

				$data[$key]['capai'] = $dataPerHari['CAPAI'];
			}
		} else {
			$data = array();
		}

		$arrReturn['dataList'] = $data;
		$arrReturn['capai']    = $dataCapai['CAPAI'];
		$arrReturn['target']   = $dataTarget['BANYAK'];
		return $arrReturn;
	}

	public function getAuditLogAflP1SummaryFa($IMSID, $UserID, $EventDate)
	{
		$arrReturn = array();

		$sql = "SELECT
				tar.`FarmerID` AS 'ID Petani',
				b.ExtFarmerID AS 'ID Petani Eksternal',
				b.`FarmerName` AS 'Nama Petani',
				c.`CPGid` AS 'ID Kelompok',
				c.`GroupName` AS 'Kelompok Tani',
				d.`Province` AS 'Propinsi',
				e.`District` AS 'Kabupaten',
				f.`SubDistrict` AS 'Kecamatan',
				g.`Village` AS 'Desa',
				tbl_au.`GardenNr` AS 'Nr Kebun',
				tbl_au.`SurveyNr` AS 'Nr Survey',
				IF(
				`Certification` = 1,
				'UTZ',
				IF(
				  `Certification` = 2,
				  'Rainforest',
				  IF(
					`Certification` = 3,
					'Fairtrade',
					IF(`Certification` = 4, 'Organic', '')
				  )
				)
				) AS 'Program Sertifikasi',
				`ICSDate` AS 'Tgl Audit Internal',
				`DateRevisionAudit` AS 'Tgl Revisi Audit Internal',
				`CommentAudit` AS 'Komentar Audit',
				`RecommendationAudit` AS 'Rekomendasi Audit',
				IF(
				`StatusAudit` = 1,
				'Passed',
				IF(
				  `StatusAudit` = 2,
				  'Not Passed',
				  IF(
					`StatusAudit` = 3,
					'Passed with Requirement',
					''
				  )
				)
				) AS 'Status Audit Internal',
				j.`PersonNm` AS 'Nama Auditor',
				`InspectorSignature` AS 'Ttd Auditor',
				k.`PersonNm` AS 'Nama Komite Audit',
				`AuditCommiteeSignature` AS 'Ttd Komite Audit',
				l.`PersonNm` AS 'Nama IMS Manager',
				tbl_au.`IMSManagerSignature` AS 'Ttd IMS Manager',
				tbl_au.`FarmerSignature` AS 'Ttd Petani',
				IF(
				ParticipateChildEducation = 1,
				'Yes',
				IF(
				  ParticipateChildEducation = 2,
				  'No',
				  ''
				)
				) AS '(C) Apakah Anda berpartisipasi dalam usaha untuk memastikan agar semua anak usia sekolah mendapatkan akses pendidikan',
				IF(
				CutWageForDisciplinary = 1,
				'Yes',
				IF(
				  CutWageForDisciplinary = 2,
				  'No',
				  ''
				)
				) AS '(C) Apakah Anda mengalami pemotongan upah kerja untuk tujuan disipliner',
				IF(
				DoCutWageForWorker = 1,
				'Yes',
				IF(DoCutWageForWorker = 2, 'No', '')
				) AS '(C) Apakah Anda melakukan pemotongan upah pekerja anda dengan tujuan disipliner',
				IF(
				WagePaidByPerformance = 1,
				'Yes',
				IF(WagePaidByPerformance = 2, 'No', '')
				) AS '(C) Apakah upah Anda dibayarkan sesuai kinerja atau kesepakatan tanpa adanya diskiriminasi',
				IF(
				PayingWorkerWageByPerformance = 1,
				'Yes',
				IF(
				  PayingWorkerWageByPerformance = 2,
				  'No',
				  ''
				)
				) AS '(C) Apakah Anda membayar upah pekerja anda sesuai kinerja atau kesepakatan tanpa adanya diskiriminasi',
				IF(
				HandlingFirstAidInGarden = 1,
				'Yes',
				IF(
				  HandlingFirstAidInGarden = 2,
				  'No',
				  ''
				)
				) AS '(C) Anda memahami bagaimana penanganan pertolongan pertama pada kecelakaan di kebun',
				IF(
				FirstAidKitLocation = 1,
				'Yes',
				IF(FirstAidKitLocation = 2, 'No', '')
				) AS '(C) Apakah kotak pertolongan pertama (P3K) tersedia di pusat lokasi produk, pengolahan dan pemeliharaan',
				IF(
				WorkerNotHandlePesticide = 1,
				'Yes',
				IF(
				  WorkerNotHandlePesticide = 2,
				  'No',
				  ''
				)
				) AS '(C) Apakah Anda sudah memastikan para pengurus kelompok, anggota kelompok, dan anggota kelompok yang termasuk pekerja, yang berusia di bawah 1 tahun, atau hamil dan sedang menyusui tidak boleh menangani pestisida',
				IF(
				WorkerAccessSafeDrinkingWater = 1,
				'Yes',
				IF(
				  WorkerAccessSafeDrinkingWater = 2,
				  'No',
				  ''
				)
				) AS '(C) Apakah staf kelompok, anggota kelompok, dan anggota kelompok yang merupakan pekerja mempunyai akses terhadap air minum yang aman.',
				IF(
				BufferZoneGarden = 1,
				'Yes',
				IF(BufferZoneGarden = 2, 'No', '')
				) AS '(C) Di kebun ini terdapat sebuah zona penyangga berisi vegetasi asli setidaknya selebar 5 meter  dipelihara di sepanjang batas badan air musiman dan permanen untuk mengurangi erosi, membatasi pencemaran pestisida dan pupuk, dan melindungi habitat satwa liar. Di lahan yang luasnya kurang dari 2 Ha, terdapat zona penyangga  dengan lebar setidaknya 2 meter',
				IF(
				LandOpeningForest = 1,
				'Yes',
				IF(LandOpeningForest = 2, 'No', '')
				) AS '(C) Apakah lahan Anda dibuat dengan membuka hutan pada tahun 2008 atau sesudahnya',
				IF(
				LandOpeningForestCertificate = 1,
				'Yes',
				IF(
				  LandOpeningForestCertificate = 2,
				  'No',
				  ''
				)
				) AS '(C) Jika lahan Anda dibuat dengan membuka hutan, apakah Anda memiliki surat kepemilikan secara resmi dari pemerintah',
				IF(
				IdentifyProtectRareSpecies = 1,
				'Yes',
				IF(
				  IdentifyProtectRareSpecies = 2,
				  'No',
				  ''
				)
				) AS '(C) Apakah Anda melakukan identifikasi dan perlindungan terhadap spesies langka dan terancam punah di sekitar Anda',
				tbl_au.`DateCreated`,
				h.`UserRealName` AS CreatedBy,
				tbl_au.`DateUpdated`,
				i.`UserRealName` AS LastModifiedBy
			FROM
				`ktv_certification_pre_afl_target` tar
				LEFT JOIN sys_user us ON tar.`PICUserID` = us.`UserId`
				LEFT JOIN ktv_ims ims ON tar.`IMSID` = ims.`IMSID`
				LEFT JOIN ktv_certification_holders hold ON ims.CertHolderID = hold.CertHolderID

				LEFT JOIN ktv_certification_pre_afl_garden aflg ON 1=1
					AND tar.`IMSID` = aflg.`IMSID`
					AND tar.`FarmerID` = aflg.`FarmerID`

				INNER JOIN (
					SELECT
						au.*
					FROM
						(SELECT
							FarmerID,
							GardenNr,
							SurveyNr,
							Certification,
							MAX(ICSDate) ICSDate
						FROM
							ktv_certification_audit_log
						WHERE Certification != 0
							AND GardenNr != 0
						GROUP BY FarmerID,
							GardenNr,
							SurveyNr,
							Certification) dt
						INNER JOIN ktv_certification_audit_log au
							ON dt.FarmerID = au.FarmerID
							AND dt.GardenNr = au.GardenNr
							AND dt.SurveyNr = au.SurveyNr
							AND dt.Certification = au.Certification
							AND dt.ICSDate = au.ICSDate
				) AS tbl_au ON 1=1
					AND aflg.`FarmerID` = tbl_au.FarmerID
					AND aflg.`GardenNr` = tbl_au.GardenNr
					AND aflg.`SurveyNr` = tbl_au.SurveyNr
					AND hold.`CertProgID` = tbl_au.Certification
					AND (
						DATE(tbl_au.`DateUpdated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
						OR
						DATE(tbl_au.`DateCreated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
					)

				LEFT JOIN ktv_members b ON b.`FarmerID` = tbl_au.`FarmerID`
				LEFT JOIN ktv_cpg c ON c.`CPGid` = b.`CPGid`
				LEFT JOIN ktv_village g ON g.`VillageID` = b.`VillageID`
				LEFT JOIN ktv_subdistrict f ON f.`SubDistrictID` = g.SubDistrictID
				LEFT JOIN ktv_district e ON e.`DistrictID` = f.DistrictID
				LEFT JOIN ktv_province d ON d.`ProvinceID` = e.ProvinceID
				LEFT JOIN sys_user h ON h.UserId = tbl_au.`CreatedBy`
				LEFT JOIN sys_user i ON i.UserId = tbl_au.`LastModifiedBy`
				LEFT JOIN ktv_persons j ON j.`PersonID` = tbl_au.`InspectorID`
				LEFT JOIN ktv_persons k ON k.`PersonID` = tbl_au.`AuditCommiteeID`
				LEFT JOIN ktv_persons l ON l.`PersonID` = tbl_au.`IMSManagerID`

			WHERE
				tar.`IMSID` = ?
				AND tar.`PICUserID` = ?
				AND b.StatusCode = 'active'
			ORDER BY tar.`FarmerID` ASC";
		$query = $this->db->query($sql, array($EventDate, $EventDate, $IMSID, $UserID));
		$data  = $query->result_array();

		//target
		$sql = "SELECT
					COUNT(*) AS BANYAK
				FROM
					`ktv_certification_pre_afl_target` tar
					LEFT JOIN ktv_certification_pre_afl_garden aflg ON 1=1
						AND tar.`IMSID` = aflg.`IMSID`
						AND tar.`FarmerID` = aflg.`FarmerID`
				WHERE
					tar.`IMSID` = ?
					AND tar.`PICUserID` = ?";
		$query   = $this->db->query($sql, array($IMSID, $UserID));
		$dataRow = $query->row_array();

		$arrReturn['dataList'] = $data;
		$arrReturn['capai']    = count($data);
		$arrReturn['target']   = $dataRow['BANYAK'];
		return $arrReturn;
	}

	public function getPpigAflP1SummaryFaProgress($IMSID, $UserID, $CertEventDate)
	{
		$arrReturn = array();

		//target
		$sql = "SELECT
				COUNT(*) AS BANYAK
			FROM
				`ktv_certification_pre_afl_target` tar
			WHERE
				tar.`IMSID` = ?
				AND tar.`PICUserID` = ?";
		$query      = $this->db->query($sql, array($IMSID, $UserID));
		$dataTarget = $query->row_array();

		//realnya sudah dapat berapa
		$sql = "SELECT
				tbl_sub.CAPAI,
				LEAST(
					DATE(IF(tbl_sub.BeginTgl='0000-00-00',tbl_sub.BeginTglUpdated,tbl_sub.BeginTgl)),
					DATE(IF(tbl_sub.BeginTglUpdated='0000-00-00',tbl_sub.BeginTgl,tbl_sub.BeginTglUpdated))
				) AS BeginTgl,
				GREATEST(DATE(tbl_sub.EndTgl),DATE(tbl_sub.EndTglUpdated)) AS EndTgl
			FROM
			(
			SELECT
				COUNT(tbl_grup.FarmerID) AS CAPAI,
				DATE(MIN(tbl_grup.`DateCreated`)) AS BeginTgl,
				DATE(MIN(tbl_grup.`DateUpdated`)) AS BeginTglUpdated,
				DATE(MAX(tbl_grup.`DateCreated`)) AS EndTgl,
				DATE(MAX(tbl_grup.`DateUpdated`)) AS EndTglUpdated
			FROM
			(
				SELECT
					a.FarmerID,
					a.`DateCreated`,
					a.`DateUpdated`
				FROM
					`ktv_certification_pre_afl_target` tar
					LEFT JOIN ktv_ims ims ON tar.`IMSID` = ims.`IMSID`

					LEFT JOIN ktv_certification_pre_afl afl ON 1=1
						AND tar.`IMSID` = afl.`IMSID`
						AND tar.`FarmerID` = afl.`FarmerID`

					LEFT JOIN `ktv_ppiscore2012` a ON 1=1
						AND afl.`FarmerID` = a.`FarmerID`
					LEFT JOIN ktv_members b
						ON b.`FarmerID` = a.`FarmerID`
					LEFT JOIN ktv_cpg_partner p
						ON p.`CPGid` = b.`CPGid`
					LEFT JOIN ktv_cpg c
						ON c.`CPGid` = b.`CPGid`
					LEFT JOIN ktv_village g ON g.`VillageID` = b.`VillageID`
					LEFT JOIN ktv_subdistrict f ON f.`SubDistrictID` = g.SubDistrictID
					LEFT JOIN ktv_district e ON e.`DistrictID` = f.DistrictID
					LEFT JOIN ktv_province d ON d.`ProvinceID` = e.ProvinceID
					LEFT JOIN sys_user h
						ON h.UserId = a.`CreatedBy`
					LEFT JOIN sys_user i
						ON i.UserId = a.`LastModifiedBy`
				WHERE
					b.`StatusCode` = 'active'
					AND afl.`IMSID` = ?
					AND tar.`PICUserID` = '?'
					AND afl.`StatusCode` = 'active'
					AND afl.StatusAudit = '1'
					AND a.`StatusCode` = 'active'
					AND (
						DATE(a.`DateUpdated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
						OR
						DATE(a.`DateCreated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
					)
				GROUP BY a.`FarmerID`
			) AS tbl_grup
			) AS tbl_sub";
		$query     = $this->db->query($sql, array($IMSID, $UserID, $CertEventDate, $CertEventDate));
		$dataCapai = $query->row_array();

		//cek apakah data tanggal rangenya ada
		if ($dataCapai['BeginTgl'] != "0000-00-00" && $dataCapai['BeginTgl'] != "" && $dataCapai['EndTgl'] != "0000-00-00" && $dataCapai['EndTgl'] != "") {
			//progress data by tanggal
			$data      = array();
			$dateRange = $this->getDateRange($dataCapai['BeginTgl'], $dataCapai['EndTgl'], '+1 day', 'Y-m-d');
			foreach ($dateRange as $key => $tglProses) {
				$data[$key]['tanggal'] = $tglProses;

				//query capainya perhari
				$sql = "SELECT
					COUNT(tbl_grup.FarmerID) AS CAPAI
				FROM
				(
					SELECT
						a.FarmerID,
						a.`DateCreated`,
						a.`DateUpdated`
					FROM
						`ktv_certification_pre_afl_target` tar
						LEFT JOIN ktv_ims ims ON tar.`IMSID` = ims.`IMSID`

						LEFT JOIN ktv_certification_pre_afl afl ON 1=1
							AND tar.`IMSID` = afl.`IMSID`
							AND tar.`FarmerID` = afl.`FarmerID`

						LEFT JOIN `ktv_ppiscore2012` a ON 1=1
							AND afl.`FarmerID` = a.`FarmerID`
						LEFT JOIN ktv_members b
							ON b.`FarmerID` = a.`FarmerID`
						LEFT JOIN ktv_cpg_partner p
							ON p.`CPGid` = b.`CPGid`
						LEFT JOIN ktv_cpg c
							ON c.`CPGid` = b.`CPGid`
						LEFT JOIN ktv_village g ON g.`VillageID` = b.`VillageID`
						LEFT JOIN ktv_subdistrict f ON f.`SubDistrictID` = g.SubDistrictID
						LEFT JOIN ktv_district e ON e.`DistrictID` = f.DistrictID
						LEFT JOIN ktv_province d ON d.`ProvinceID` = e.ProvinceID
						LEFT JOIN sys_user h
							ON h.UserId = a.`CreatedBy`
						LEFT JOIN sys_user i
							ON i.UserId = a.`LastModifiedBy`
					WHERE
						b.`StatusCode` = 'active'
						AND afl.`IMSID` = ?
						AND tar.`PICUserID` = ?
						AND afl.`StatusCode` = 'active'
						AND afl.StatusAudit = '1'
						AND a.`StatusCode` = 'active'
						AND (
							DATE(a.`DateUpdated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
							OR
							DATE(a.`DateCreated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
						)
						AND (
							(
								DATE(a.`DateCreated`) = ?
								OR
								DATE(a.`DateUpdated`) = ?
							)
						)
					GROUP BY a.`FarmerID`
				) AS tbl_grup";
				$query       = $this->db->query($sql, array($IMSID, $UserID, $CertEventDate, $CertEventDate, $tglProses, $tglProses));
				$dataPerHari = $query->row_array();

				$data[$key]['capai'] = $dataPerHari['CAPAI'];
			}
		} else {
			$data = array();
		}

		$arrReturn['dataList'] = $data;
		$arrReturn['capai']    = $dataCapai['CAPAI'];
		$arrReturn['target']   = $dataTarget['BANYAK'];
		return $arrReturn;
	}

	public function getPpigAflP1SummaryFa($IMSID, $UserID, $CertEventDate)
	{
		$arrReturn = array();

		$sql = "SELECT
				a.`FarmerID` AS 'ID Petani',
				b.`FarmerName` AS 'Nama Petani',
				c.`CPGid` AS 'ID Kelompok',
				c.`GroupName` AS 'Kelompok Tani',
				e.`District` AS 'Kabupaten',
				f.`SubDistrict` AS 'Kecamatan',
				g.`Village` AS 'Desa',
				a.`SurveyNr` AS 'Nr Survey',
				a.`InterviewDate`,
				CASE
					WHEN a.`Householdmembers`='1' THEN 'Enam atau lebih'
					WHEN a.`Householdmembers`='2' THEN 'Lima'
					WHEN a.`Householdmembers`='3' THEN 'Empat'
					WHEN a.`Householdmembers`='4' THEN 'Tiga'
					WHEN a.`Householdmembers`='5' THEN 'Dua'
					WHEN a.`Householdmembers`='6' THEN 'Satu'
				END AS 'Berapa jumlah anggota rumah tangga',
				CASE
					WHEN a.`Schooling`='1' THEN 'Tidak ada anak usia 6-19 tahun'
					WHEN a.`Schooling`='2' THEN 'Tidak'
					WHEN a.`Schooling`='3' THEN 'Ya'
				END AS 'Apakah semua anggota rumah tangga yang berusia 6 sampai 18 tahun masih bersekolah',
				CASE
					WHEN a.`Education`='1' THEN 'Belum pernah bersekolah'
					WHEN a.`Education`='2' THEN 'SD/SDLB, Madrasah Ibtidaiyah, atau Paket A'
					WHEN a.`Education`='3' THEN 'SMP/SMPLB, Madrasah Tsanawiayh, atau Paket B'
					WHEN a.`Education`='4' THEN 'Tidak ada kepala rumah tangga perempuan/istri'
					WHEN a.`Education`='5' THEN 'SMK'
					WHEN a.`Education`='6' THEN 'SMA/SMALB, Madrasah Aliyah, atau Paket C'
					WHEN a.`Education`='7' THEN 'D1, D2, D3/Sarjana Muda, D4, S1, S2, S3'
				END AS 'Apa tingkat pendidikan terakhir yang diselesaikan oleh kepala rumah tangga perempuan/istri',
				CASE
					WHEN a.`Employment`='1' THEN 'Tidak ada kepala rumah tangga laki-laki/suami'
					WHEN a.`Employment`='2' THEN 'Tidak bekerja atau pekerja keluarga/pekerja tidak dibayar'
					WHEN a.`Employment`='3' THEN 'Pekerja bebas'
					WHEN a.`Employment`='4' THEN 'Berusaha sendiri atau berusaha dibantu buruh tidak tetap/buruh tidak dibayar'
					WHEN a.`Employment`='5' THEN 'Buruh/karyawan/pegawai'
					WHEN a.`Employment`='6' THEN 'Berusaha dibantu buruh tetap/buruh dibayar'
				END AS 'Apa status pekerjaan utama dari kepala rumah tangga laki-laki/suami di minggu terakhir',
				CASE
					WHEN a.`HouseFloor`='1' THEN 'Tanah atau bambu'
					WHEN a.`HouseFloor`='2' THEN 'Bukan tanah/bambu'
				END AS  'Jenis lantai terluas',
				CASE
					WHEN a.`ToiletFacility`='1' THEN 'Tidak ada atau jamban cemplung/cebluk'
					WHEN a.`ToiletFacility`='2' THEN 'Ada kloset, tapi tidak tersambung ke septic tank (plengsengan)'
					WHEN a.`ToiletFacility`='3' THEN 'Leher Angsa'
				END AS 'Jenis kloset/WC yang rumah tangga anda miliki',
				CASE
					WHEN a.`CookingFuel`='1' THEN 'Kayu bakar, arang, briket'
					WHEN a.`CookingFuel`='2' THEN 'Gas/elpiji, minyak tanah, listrik, atau lainnya'
				END AS 'Apa jenis bahan bakar utama rumah tangga',
				CASE
					WHEN a.`GasCylinder`='1' THEN 'Tidak'
					WHEN a.`GasCylinder`='2' THEN 'Ya'
				END AS 'Apakah rumah tangga memiliki tabung gas 12 Kg atau lebih',
				CASE
					WHEN a.`Refrigerator`='1' THEN 'Tidak'
					WHEN a.`Refrigerator`='2' THEN 'Ya'
				END AS 'Apakah rumah tangga memiliki kulkas/lemari es',
				CASE
					WHEN a.`Motorcycle`='1' THEN 'Tidak'
					WHEN a.`Motorcycle`='2' THEN 'Ya'
				END AS 'Apakah rumah tangga memiliki sepeda motor atau perahu motor',
				a.`Score`,
				a.`National`,
				a.`1.25/day`,
				a.`2.5/day`,
				h.`UserRealName` AS CreatedBy,
				a.`DateCreated`,
				i.`UserRealName` AS LastModifiedBy,
				a.`DateUpdated`
			FROM
				`ktv_certification_pre_afl_target` tar
				LEFT JOIN ktv_ims ims ON tar.`IMSID` = ims.`IMSID`

				LEFT JOIN ktv_certification_pre_afl afl ON 1=1
					AND tar.`IMSID` = afl.`IMSID`
					AND tar.`FarmerID` = afl.`FarmerID`

				LEFT JOIN `ktv_ppiscore2012` a ON 1=1
					AND afl.`FarmerID` = a.`FarmerID`
				LEFT JOIN ktv_members b
					ON b.`FarmerID` = a.`FarmerID`
				LEFT JOIN ktv_cpg_partner p
					ON p.`CPGid` = b.`CPGid`
				LEFT JOIN ktv_cpg c
					ON c.`CPGid` = b.`CPGid`
				LEFT JOIN ktv_village g ON g.`VillageID` = b.`VillageID`
				LEFT JOIN ktv_subdistrict f ON f.`SubDistrictID` = g.SubDistrictID
				LEFT JOIN ktv_district e ON e.`DistrictID` = f.DistrictID
				LEFT JOIN ktv_province d ON d.`ProvinceID` = e.ProvinceID
				LEFT JOIN sys_user h
					ON h.UserId = a.`CreatedBy`
				LEFT JOIN sys_user i
					ON i.UserId = a.`LastModifiedBy`
			WHERE
				b.`StatusCode` = 'active'
				AND afl.`IMSID` = ?
				AND tar.`PICUserID` = ?
				AND afl.`StatusCode` = 'active'
				AND afl.StatusAudit = '1'
				AND a.`StatusCode` = 'active'
				AND (
					DATE(a.`DateUpdated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
					OR
					DATE(a.`DateCreated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
				)
			GROUP BY a.`FarmerID`";
		$query = $this->db->query($sql, array($IMSID, $UserID, $CertEventDate, $CertEventDate));
		$data  = $query->result_array();

		//target
		$sql = "SELECT
				COUNT(*) AS BANYAK
			FROM
				`ktv_certification_pre_afl_target` tar
			WHERE
				tar.`IMSID` = ?
				AND tar.`PICUserID` = ?";
		$query   = $this->db->query($sql, array($IMSID, $UserID));
		$dataRow = $query->row_array();

		$arrReturn['dataList'] = $data;
		$arrReturn['capai']    = count($data);
		$arrReturn['target']   = $dataRow['BANYAK'];
		return $arrReturn;
	}

	public function getBuyingUnitAddList($IMSID, $NameSearch, $ProvinceID, $DistrictID, $ObjType)
	{
		$sqlFilter = "";

		if ($ProvinceID != "") {
			$sqlFilter .= " AND kdis.ProvinceID = '$ProvinceID' ";
		}

		if ($DistrictID != "") {
			$sqlFilter .= " AND kdis.DistrictID = '$DistrictID' ";
		}

		$sql = "SELECT
				a.`SupplychainID` AS addBUSupplychainID
				, a.`ObjType` AS addBUOrgType
				, a.`Name` AS addBUName
				, IFNULL(d.Company, '-') AS addBUCompany
				, kdis.`District` AS addBUDistrict
			FROM
				view_tc_supplychain_org a
				LEFT JOIN ktv_village kvil ON kvil.VillageID=a.VillageID
				LEFT JOIN ktv_subdistrict ksub ON ksub.SubDistrictID=kvil.SubDistrictID
				LEFT JOIN ktv_district kdis on kdis.DistrictID=ksub.DistrictID
				LEFT JOIN ktv_ims_buying_unit b ON a.`SupplychainID` = b.`SupplychainID`
				LEFT JOIN ktv_traders d
					ON d.TraderID = a.OrgID
					AND a.ObjType = 'Pedagang'
			WHERE 1=1
				AND a.`SupplychainID` NOT IN (
					SELECT sub_a.SupplychainID FROM ktv_ims_buying_unit sub_a WHERE sub_a.IMSID = ?
				)
				AND a.`Name` LIKE ?
				AND a.`ObjType` = ?
				$sqlFilter
			GROUP BY a.`SupplychainID`
			ORDER BY a.`Name` ASC";
		$p = array(
			$IMSID, '%' . $NameSearch . '%', $ObjType,
		);
		$query = $this->db->query($sql, $p);

		$arrReturn['data'] = $query->result_array();
		return $arrReturn;
	}

	public function inputBuyingUnit($IMSMasterID, $IMSID, $arrTempBunits)
	{
		$this->db->trans_begin();

		for ($i = 0; $i < count($arrTempBunits); $i++) {
			if ($arrTempBunits[$i] != "") {
				$sql = "INSERT INTO `ktv_ims_buying_unit` SET
					`IMSID` = ?,
					`SupplychainID` = ?,
					`StatusCode` = 'active',
					`DateCreated` = NOW(),
					`CreatedBy` = '{$_SESSION['userid']}'";
				$query = $this->db->query($sql, array($IMSID, $arrTempBunits[$i]));
			}
		}

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			$results['success'] = false;
			$results['message'] = "Failed to save data";
		} else {
			$this->db->trans_commit();
			$results['success'] = true;
			$results['message'] = "Data saved";
		}

		return $results;
	}

	public function deleteBuyingUnit($IMSID, $SupplychainID)
	{
		$sql   = "DELETE FROM ktv_ims_buying_unit WHERE IMSID = ? AND SupplychainID = ? LIMIT 1";
		$query = $this->db->query($sql, array($IMSID, $SupplychainID));

		if ($query) {
			$results['success'] = true;
			$results['message'] = "Data deleted";
		} else {
			$results['success'] = false;
			$results['message'] = "Failed to delete data";
		}

		return $results;
	}

	public function getFillFormEligibleFarmer($IMSID, $FarmerID)
	{
		$sql = "SELECT
				a.`StatusAudit`
				, a.`NotEligibleReason`
				, a.StatusComply
				, a.ICSDate
				, a.AuditRemark AS StatusComplyRemark
			FROM
				ktv_certification_pre_afl a
			WHERE
				a.`IMSID` = ?
				AND a.`FarmerID` = ?
			LIMIT 1";
		$query = $this->db->query($sql, array($IMSID, $FarmerID));
		$data  = $query->row_array();

		$return['success'] = true;
		$return['data']    = $data;
		return $return;
	}

	public function updateStatusEligibleFarmer($StatusAudit, $NotEligibleReason, $StatusComply, $StatusComplyRemark, $IMSID, $FarmerID, $ICSDate)
	{
		$sql = "UPDATE ktv_certification_pre_afl SET
				StatusAudit = ?,
				NotEligibleReason = ?,
				StatusComply = ?,
				ICSDate = ?,
				AuditRemark = ?,
				DateUpdated = NOW(),
				LastModifiedBy = ?
			WHERE
				FarmerID = ?
				AND IMSID = ?
			LIMIT 1";
		$p = array(
			$StatusAudit,
			$NotEligibleReason,
			$StatusComply,
			$ICSDate,
			$StatusComplyRemark,
			$_SESSION['userid'],
			$FarmerID,
			$IMSID,
		);
		$query = $this->db->query($sql, $p);

		if ($query) {
			$result['success'] = true;
			$result['message'] = 'Data saved';
		} else {
			$result['success'] = false;
			$result['message'] = 'Failed to save data';
		}

		return $result;
	}

	public function genPreAflGarden($OpsiCall,$IMSID)
	{
		$this->db->trans_begin();

		//SurveyNr IMS terpilih dan Event Date
		$sql = "SELECT
				a.SurveyNr
				, a.CertEventDate
			FROM
				ktv_ims a
			WHERE
				a.`IMSID` = ?
			LIMIT 1";
		$query       = $this->db->query($sql, array($IMSID));
		$data        = $query->row_array();
		$SurveyNrIMS = $data['SurveyNr'];
		$EventDate   = $data['CertEventDate'];

		switch($OpsiCall){
			case 'gen':
				//hapus dulu data sebelumnya
				$sql   = "DELETE FROM ktv_certification_pre_afl_garden WHERE IMSID = ?";
				$query = $this->db->query($sql, array($IMSID));

				//ambil data Farmer yg Eligible
				$sql = "SELECT
						a.`FarmerID`
					FROM
						ktv_certification_pre_afl a
					WHERE
						a.`StatusAudit` = '1'
						AND a.`IMSID` = ?
						AND a.StatusCode = 'active'
					ORDER BY a.`FarmerID`";
				$query          = $this->db->query($sql, array($IMSID));
				$dataListFarmer = $query->result_array();
			break;
			case 'regen':
				//Ambil FarmerID yg Reinspek
				$sql = "SELECT
							GROUP_CONCAT(a.`FarmerID` SEPARATOR ',') AS FarmerIDImp
						FROM
							ktv_ims_ics_reinspection a
						WHERE
							a.`IMSID` = ?";
				$Data = $this->db->query($sql,array($IMSID))->row_array();
				if(!isset($Data['FarmerIDImp'])){
					//Balikan, berhenti proses
					$this->db->trans_rollback();
					$results['success'] = false;
					$results['message'] = "Failed to generate Garden Candidates";
					return $results;
				}else{
					$FarmerIDImp = $Data['FarmerIDImp'];
				}

				//hapus dulu data sebelumnya
				$sql   = "DELETE FROM ktv_certification_pre_afl_garden WHERE IMSID = ? AND FarmerID IN ({$FarmerIDImp})";
				$query = $this->db->query($sql, array($IMSID));

				$sql = "SELECT
						a.`FarmerID`
					FROM
						ktv_certification_pre_afl a
					WHERE
						a.`IMSID` = ?
						AND a.FarmerID IN ({$FarmerIDImp})
					ORDER BY a.`FarmerID`";
				$query          = $this->db->query($sql, array($IMSID));
				$dataListFarmer = $query->result_array();
			break;
		}


		for ($i = 0; $i < count($dataListFarmer); $i++) {
			$dataInsert             = array();
			$dataInsert['FarmerID'] = $dataListFarmer[$i]['FarmerID'];
			$dataInsert['IMSID']    = $IMSID;

			//cek ada berapa Garden dan SurveyNr
			$sql = "SELECT
					a.`PlotNr` GardenNr
					, GROUP_CONCAT(a.`SurveyNr` SEPARATOR ',') AS GroupSurveyNr
				FROM
					ktv_survey_plot a
				WHERE
					a.`MemberID` = ?
				GROUP BY a.`PlotNr`
				ORDER BY a.`PlotNr`, a.`SurveyNr`";
			$query            = $this->db->query($sql, array($dataInsert['FarmerID']));
			$dataGardenSurvey = $query->result_array();

			if ($dataGardenSurvey[0]['GardenNr'] != "") {
				for ($j = 0; $j < count($dataGardenSurvey); $j++) {
					/*$sql = "SELECT
							a.`SurveyNr`
						FROM
							ktv_members_garden a
							INNER JOIN ktv_survey b ON a.`SurveyNr` = b.`SurveyNr`
						WHERE 1=1
							AND ( (b.`SurveyCertification` = 'Yes') OR (b.`SurveyNr` = '0') ) #Hanya ambil Survey certifikasi / Baseline
							AND a.`FarmerID` = ?
							AND a.`GardenNr` = ?
							AND (
								DATE(a.`DateCollection`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
								AND
								DATE(a.`DateCollection`) < DATE_FORMAT( CURDATE(), '%Y/%m/%d' )
							)
						ORDER BY a.`SurveyNr` DESC #Prioritas Survey Sertifikasi
						LIMIT 1";
					$p = array(
						$dataInsert['FarmerID'],
						$dataGardenSurvey[$j]['GardenNr'],
						$EventDate,
					);
					$query                  = $this->db->query($sql, $p);
					$data                   = $query->row_array();
					$SurveyNrBaselineOrCert = $data['SurveyNr'];

					//jika Survey Baseline/Cert ada, maka dipakai, jika tidak pakai SurveyNr IMS
					if ($SurveyNrBaselineOrCert != "") {
						$SurveyNrInsert = $SurveyNrBaselineOrCert;
					} else {
						$SurveyNrInsert = $SurveyNrIMS;
					}*/

					//Tidak pakai hitung2an 6 bulan sebelum lagi, langsung saja tembak dari SurveyNr nya ktv_ims
					$dataInsert['GardenNr'] = $dataGardenSurvey[$j]['GardenNr'];
					$dataInsert['SurveyNr'] = $SurveyNrIMS;

					//sebelum insert, cek gardennya aktif tidak, kalau tidak langsung dilewatkan
					/*$sql="SELECT
					gstat.`ActiveStatus`
					FROM
					ktv_members_garden_status gstat
					WHERE
					gstat.`FarmerID` = ?
					AND gstat.`GardenNr` = ?
					LIMIT 1";
					$query = $this->db->query($sql,array($dataInsert['FarmerID'], $dataInsert['GardenNr']));
					$cekGarden = $query->row_array();
					if($cekGarden['ActiveStatus'] == "2") {
					//lewatkan
					continue;
					}*/
					//dilewatkan dl aja pengecekan ini, karena asumsinya semua petani yg di importkan harus di audit lagi.

					//insert
					$sql = "INSERT INTO `ktv_certification_pre_afl_garden` SET
							`IMSID` = ?,
							`FarmerID` = ?,
							`GardenNr` = ?,
							`SurveyNr` = ?,
							`CreatedBy` = ?,
							`DateCreated` = NOW()
						";
					$p = array(
						$dataInsert['IMSID'],
						$dataInsert['FarmerID'],
						$dataInsert['GardenNr'],
						$dataInsert['SurveyNr'],
						$_SESSION['userid'],
					);
					$query = $this->db->query($sql, $p);
				}
			} else {
				//Jika Petani ini masih belum punya Garden sama sekali
				//Langsung insertkan saja Garden nya GardenNr 1 dan Survey Baseline

				//insert
				$sql = "INSERT INTO `ktv_certification_pre_afl_garden` SET
						`IMSID` = ?,
						`FarmerID` = ?,
						`GardenNr` = ?,
						`SurveyNr` = ?,
						`CreatedBy` = ?,
						`DateCreated` = NOW()
					";
				$p = array(
					$dataInsert['IMSID'],
					$dataInsert['FarmerID'],
					'1',
					$SurveyNrIMS,
					$_SESSION['userid'],
				);
				$query = $this->db->query($sql, $p);
			}
		}

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			$results['success'] = false;
			$results['message'] = "Failed to generate Garden Candidates";
		} else {
			$this->db->trans_commit();
			$results['success'] = true;
			$results['message'] = "Garden Candidates Generated";
		}
		return $results;
	}

    public function GridMappingFAFarmer($IMSID, $UserId, $sffmt, $limit, $sortingField, $sortingDir, $opsiCall)
    {
        if ($UserId == "not_map") {
            if ($sortingField == "") {
                $sortingField = 'Farmer';
            }

            if ($sortingDir == "") {
                $sortingDir = 'ASC';
            }

            $sql = "SELECT
                    '-' AS FieldAgent
                    , CONCAT(far.`MemberDisplayID`,' - ',far.`MemberName`) AS Farmer
                    , CONCAT(cpg.`FarmerGroupID`,' - ',cpg.`GroupName`) AS FarmerGroup
                    , prov.`Province`
                    , dis.`District`
                    , subd.`SubDistrict`
                    , vil.`Village`
                FROM
                    `ktv_fa_farmer_mapping` ffm

                    INNER JOIN ktv_members far ON ffm.`FarmerID` = far.`MemberID`
                    LEFT JOIN ktv_farmer_group cpg ON far.`FarmerGroupID` = cpg.`FarmerGroupID`

                    LEFT JOIN ktv_village vil ON far.`VillageID` = vil.`VillageID`
                    LEFT JOIN ktv_subdistrict subd ON vil.`SubDistrictID` = subd.`SubDistrictID`
                    LEFT JOIN ktv_district dis ON subd.`DistrictID` = dis.`DistrictID`
                    LEFT JOIN ktv_province prov ON dis.`ProvinceID` = prov.`ProvinceID`
                WHERE 1=1
                    AND ffm.`IMSID` = ?
                    AND ffm.`FarmerID` IS NULL
                ORDER BY $sortingField $sortingDir";

            if ($opsiCall == 'no_limit') {
                return $this->db->query($sql, array($IMSID))->result_array();
            } else {
                $sffmt          = (int) $sffmt;
                $limit          = (int) $limit;
                $result['data'] = $this->db->query($sql . " LIMIT $sffmt,$limit", array($IMSID))->result_array();

                $query           = $this->db->query('SELECT FOUND_ROWS() AS total');
                $result['total'] = $query->row()->total;

                return $result;
            }
        } else {
            if ($UserId == "" || $UserId == "null") {
                $UserId = '';
            }

            if ($sortingField == "") {
                $sortingField = 'FieldAgent';
            }

            if ($sortingDir == "") {
                $sortingDir = 'ASC';
            }

            $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    b.`PersonNm` AS FieldAgent
                    , a.UserName
                    , far.MemberID FarmerID
                    , CONCAT(far.MemberDisplayID,' - ',far.MemberName ) AS Farmer
                    , CONCAT(cpg.`FarmerGroupID`,' - ',cpg.`GroupName`) AS FarmerGroup
                    , prov.`Province`
                    , dis.`District`
                    , subd.`SubDistrict`
                    , vil.`Village`
                FROM
                    ktv_fa_farmer_mapping ffm
                    INNER JOIN ktv_members far ON ffm.`FarmerID` = far.`MemberID`
                    LEFT JOIN ktv_farmer_group cpg ON far.`FarmerGroupID` = cpg.`FarmerGroupID`

                    LEFT JOIN ktv_village vil ON far.`VillageID` = vil.`VillageID`
                    LEFT JOIN ktv_subdistrict subd ON vil.`SubDistrictID` = subd.`SubDistrictID`
                    LEFT JOIN ktv_district dis ON subd.`DistrictID` = dis.`DistrictID`
                    LEFT JOIN ktv_province prov ON dis.`ProvinceID` = prov.`ProvinceID`

                    INNER JOIN sys_user a ON ffm.`UserName` = a.`UserName`
                    INNER JOIN ktv_persons b ON a.`UserId` = b.`UserID`
                WHERE 1 = 1
                    AND ffm.`IMSID` = ?
                ORDER BY $sortingField $sortingDir
                ";

            if ($opsiCall == 'no_limit') {
                return $this->db->query($sql, array($IMSID, $UserId, $UserId))->result_array();
            } else {
                $sffmt          = (int) $sffmt;
                $limit          = (int) $limit;
                $result['data'] = $this->db->query($sql . " LIMIT $sffmt,$limit", array($IMSID, $UserId, $UserId))->result_array();

                $query           = $this->db->query('SELECT FOUND_ROWS() AS total');
                $result['total'] = $query->row()->total;

                return $result;
            }
        }
    }

	public function genAflFarmerAndGarden($OpsiCall,$IMSID, $CertEventDate)
	{
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);
		$this->db->trans_begin();

		switch($OpsiCall){
			case 'gen':
				//Generate $FarmerID yg sudah di verified
				$sql = "SELECT
						GROUP_CONCAT(a.`FarmerID` SEPARATOR ',') AS ImpFarmerID
					FROM
						ktv_certification_afl_farmer a
					WHERE
						a.`IMSID` = ?
						AND a.`CertStatusVerified` IN (1,2)";
				$DataFarmerVerified = $this->db->query($sql,array($IMSID))->row_array();
				$ImpFarmerVerifiedFarmerID = $DataFarmerVerified['ImpFarmerID'];

				if($ImpFarmerVerifiedFarmerID != ""){
					$SqlDeleteAfl = " AND FarmerID NOT IN ($ImpFarmerVerifiedFarmerID) ";
					$SqlSelectAflProcess = " AND a.FarmerID NOT IN ($ImpFarmerVerifiedFarmerID) ";
				}else{
					$SqlDeleteAfl = "";
					$SqlSelectAflProcess = "";
				}

				$SqlInsertAflFarmerStatusVerified = "";
			break;
			case 'regen':
				//Ambil FarmerID yg Reinspek
				$sql = "SELECT
							GROUP_CONCAT(a.`FarmerID` SEPARATOR ',') AS FarmerIDImp
						FROM
							ktv_ims_ics_reinspection a
						WHERE
							a.`IMSID` = ?";
				$Data = $this->db->query($sql,array($IMSID))->row_array();
				if(!isset($Data['FarmerIDImp'])){
					//Balikan, berhenti proses
					$results['success'] = false;
					$results['message'] = "Failed";
					return $results;
				}else{
					$FarmerIDImpRegen = $Data['FarmerIDImp'];
				}

				$SqlDeleteAfl = " AND FarmerID IN ($FarmerIDImpRegen) ";
				$SqlSelectAflProcess = " AND a.FarmerID IN ($FarmerIDImpRegen) ";
				$SqlInsertAflFarmerStatusVerified = " CertStatusVerified = '2',
														CertStatusVerifiedChangeBy = '{$_SESSION['userid']}',
														CertStatusVerifiedComment = 'Regenerate ICS on ICS Reinspection', ";

				//Update Status di ICS Reinspect
				$sql = "UPDATE ktv_ims_ics_reinspection a SET
							a.`StatusRegenerateIcs` = '1'
						WHERE
							a.`IMSID` = ?
							AND a.`FarmerID` IN ({$FarmerIDImpRegen})";
				$p = array(
					$IMSID
				);
				$query = $this->db->query($sql,$p);
			break;
		}

		//hapus dulu data sebelumnya
		$sql   = "DELETE FROM ktv_certification_afl_garden WHERE IMSID = ? $SqlDeleteAfl";
		$query = $this->db->query($sql, array($IMSID));

		$sql   = "DELETE FROM ktv_certification_afl_farmer WHERE IMSID = ? $SqlDeleteAfl";
		$query = $this->db->query($sql, array($IMSID));

		//==================== AFL Farmer (BEGIN) ==========================//
		$sql = "SELECT
				ims.Year AS IMSYear
				, a.`FarmerID`
				, cpg.GroupName
				, CASE
					WHEN far.Gender='1' THEN 'Male'
					WHEN far.Gender='2' THEN 'Female'
				END AS Gender
				, IF(COUNT(gpoly.MemberID) > 0,'Available','Notavailable') AS PolygonStatus
				, (
					SELECT
						MAX(subq_a.CertYear)+1 AS CertYear
					FROM
						ktv_certification_afl_farmer subq_a
					WHERE
						subq_a.`FarmerID` = a.FarmerID
						AND subq_a.`StatusCode` = 'active'
				) AS CertYear
				, CASE
					WHEN hold.CertProgID = '1' THEN far.isCertified
					WHEN hold.CertProgID = '2' THEN far.isCertified
					WHEN hold.CertProgID = '3' THEN far.isCertified
					WHEN hold.CertProgID = '4' THEN far.isCertified
				END AS CertFirstYear #kalau tidak dapat, nanti baru query ambil di for nya

				, MAX(audit_log.ICSDate) AS CertICSDate
				, GROUP_CONCAT(audit_log.StatusAudit SEPARATOR ',') AS CertStatusAuditLog
				, GROUP_CONCAT(audit_log.MasukHutanLindung SEPARATOR ',') AS AuditLogMasukHutanLindung

				, GROUP_CONCAT(gstat.ActiveStatus SEPARATOR ',') AS GarStatusGarden
				, SUBSTRING_INDEX(GROUP_CONCAT(gstat.DateUpdated ORDER BY gstat.DateUpdated DESC SEPARATOR ','),',',1) AS GarStatusDateUpdated
				, MAX(b.SurveyNr) AS CertSurveyNr

				, gar_all.TotalGarden + IFNULL(otl.TotalOtl,0) AS 'CertFarmTotalNr'
				, gar_all.TotalHectare + IFNULL(otl.TotalOtlHectare,0) AS 'CertTotalHectare'

				, MAX(gar.DateCollection) AS CertDateCollection
				, (SELECT UserRealName FROM sys_user WHERE UserId = gar.CreatedBy) AS IMSCreator
				, (SELECT UserRealName FROM sys_user WHERE UserId = gar.LastModifiedBy) AS IMSEditor
			FROM
				ktv_certification_pre_afl a
				INNER JOIN ktv_certification_pre_afl_garden b ON 1=1
					AND a.FarmerID = b.FarmerID
					AND a.IMSID = b.IMSID

				LEFT JOIN ktv_ims ims ON a.IMSID = ims.IMSID
				LEFT JOIN ktv_certification_holders hold ON ims.CertHolderID = hold.CertHolderID

				INNER JOIN ktv_members far ON a.`FarmerID` = far.`MemberID`
				LEFT JOIN ktv_farmer_group cpg ON far.FarmerGroupID = cpg.FarmerGroupID
				LEFT JOIN ktv_survey_plot gar ON 1=1
					AND b.FarmerID = gar.MemberID
					AND b.GardenNr = gar.PlotNr
					AND b.SurveyNr = gar.SurveyNr

				LEFT JOIN ktv_survey_plot_status gstat ON 1=1
					AND b.FarmerID = gstat.MemberID
					AND b.GardenNr = gstat.PlotNr

				LEFT JOIN (
					SELECT
						a.`MemberID`
						, a.`PlotNr`
					FROM
						ktv_survey_plot_polygon a
						INNER JOIN ktv_certification_pre_afl subpafl ON a.`MemberID` = subpafl.`FarmerID`
					WHERE
						a.`StatusCheck` = 'verified'
						AND a.`MemberID` != '0'
						AND a.`PlotNr` != '0'
						AND subpafl.`IMSID` = ?
					GROUP BY a.`MemberID`, a.`PlotNr`
				) AS gpoly ON 1=1
					AND b.`FarmerID` = gpoly.MemberID
					AND b.`GardenNr` = gpoly.PlotNr

				LEFT JOIN (
					SELECT
						au.`FarmerID`,
						au.GardenNr,
						au.SurveyNr,
						au.ICSDate,
						au.StatusAudit,
						au.Certification,
						au.MasukHutanLindung
					FROM
						(SELECT
							FarmerID,
							GardenNr,
							SurveyNr,
							Certification,
							MAX(ICSDate) ICSDate
						FROM
							ktv_certification_audit_log
						WHERE Certification != 0
							AND GardenNr != 0
						GROUP BY FarmerID,
							GardenNr,
							SurveyNr,
							Certification) dt
						INNER JOIN ktv_certification_audit_log au
							ON dt.FarmerID = au.FarmerID
							AND dt.GardenNr = au.GardenNr
							AND dt.SurveyNr = au.SurveyNr
							AND dt.Certification = au.Certification
							AND dt.ICSDate = au.ICSDate
					WHERE 1=1
				) AS audit_log ON 1=1
					AND a.FarmerID = audit_log.FarmerID
					AND gar.PlotNr = audit_log.GardenNr
					AND gar.SurveyNr = audit_log.SurveyNr
					AND hold.CertProgID = audit_log.Certification

				LEFT JOIN (
					SELECT
						sub_gar.MemberID
						, COUNT(sub_gar.`PlotNr`) AS TotalGarden
						, SUM(sub_gar.`GardenAreaHa`) AS TotalHectare
					FROM
						ktv_survey_plot sub_gar
						INNER JOIN ktv_certification_pre_afl subpafl ON sub_gar.`MemberID` = subpafl.`FarmerID`
						INNER JOIN (
							SELECT
								lat_sur_g.`MemberID`
								, lat_sur_g.`PlotNr`
								, MAX(lat_sur_g.`SurveyNr`) AS SurveyNr
							FROM
								ktv_survey_plot lat_sur_g
							GROUP BY lat_sur_g.`MemberID`, lat_sur_g.`PlotNr`
						) AS sub_gar_lat ON
							sub_gar.`MemberID` = sub_gar_lat.MemberID
							AND sub_gar.`PlotNr` = sub_gar_lat.PlotNr
							AND sub_gar.`SurveyNr` = sub_gar_lat.SurveyNr
					WHERE
						subpafl.IMSID = ?
					GROUP BY sub_gar.MemberID
				) AS gar_all ON 1=1
					AND a.FarmerID = gar_all.MemberID

				LEFT JOIN (
					SELECT
						sub_otl.`FarmerID`
						, COUNT(sub_otl.`FarmerID`) AS TotalOtl
						, SUM(sub_otl.GardenHa) AS TotalOtlHectare
					FROM
						ktv_farmer_other_land sub_otl
					WHERE
						sub_otl.`StatusCode` = 'active'
					GROUP BY sub_otl.`FarmerID`
				) AS otl ON 1=1
					AND a.FarmerID = otl.FarmerID

			WHERE 1=1
				AND a.`IMSID` = ?
				AND a.StatusAudit = '1' #Pre AFL Farmer Eligible
				AND a.StatusCode = 'active'
				$SqlSelectAflProcess
			GROUP BY a.FarmerID";
		$query             = $this->db->query($sql, array($IMSID,$IMSID,$IMSID));
		$dataListAflFarmer = $query->result_array();

		for ($i = 0; $i < count($dataListAflFarmer); $i++) {
			//get data additional
			$sql = "SELECT
					kprov.Province AS Province
					, kdis.District AS District
					, subd.SubDistrict AS SubDistrict
					, vil.`Village`
					, far.StatusMember AS StatusFarmer
					, far.DateUpdated AS DateUpdatedFarmer
					, far.Comment AS CommentTabelFarmer
					, far.MemberName FarmerName
					, far.FarmerGroupID CPGid
					, far.HandPhone
					, DATE_FORMAT(far.`DateSync`,'%Y-%m-%d') AS FarmerICSDate

					, pafl.StatusComply
					, pafl.ICSDate AS pafl_ICSDate
					, pafl.AuditRemark AS StatusComplyRemark

					, pafl.SalesLastYear
					, pafl.SalesLast2Years
					, pafl.SalesLast3Years

					, COUNT(gstat.MemberID) AS TotalFarm
					, (SELECT UserRealName FROM sys_user WHERE UserId = tar.`PICUserID`) AS IMSCreatorTarget
				FROM
					ktv_members far
					INNER JOIN ktv_village vil ON 1=1
						AND far.`VillageID` = vil.`VillageID`
					INNER JOIN ktv_subdistrict subd ON 1=1
						AND vil.SubDistrictID = subd.SubDistrictID
					LEFT JOIN ktv_district kdis on kdis.DistrictID=subd.DistrictID
					LEFT JOIN ktv_province kprov on kprov.ProvinceID=kdis.ProvinceID
					LEFT JOIN ktv_certification_pre_afl pafl ON 1=1
						AND pafl.IMSID = ?
						AND pafl.FarmerID = far.MemberID

					LEFT JOIN `ktv_certification_pre_afl_target` tar ON 1=1
						AND pafl.`IMSID` = tar.`IMSID`
						AND pafl.`FarmerID` = tar.`FarmerID`

					LEFT JOIN ktv_survey_plot_status gstat ON 1=1
						AND far.`MemberID` = gstat.MemberID
						AND gstat.ActiveStatus = '1'
				WHERE
					far.`MemberID` = ?
				LIMIT 1";
			$query      = $this->db->query($sql, array($IMSID, $dataListAflFarmer[$i]['FarmerID']));
			$dataFarmer = $query->row_array();

			//Isikan yg perlu
			$dataListAflFarmer[$i]['IMSID']                 = $IMSID;
			$dataListAflFarmer[$i]['FarmerName']            = $dataFarmer['FarmerName'];
			$dataListAflFarmer[$i]['CPGid']                 = $dataFarmer['CPGid'];
			$dataListAflFarmer[$i]['Province']              = $dataFarmer['Province'];
			$dataListAflFarmer[$i]['District']              = $dataFarmer['District'];
			$dataListAflFarmer[$i]['SubDistrict']           = $dataFarmer['SubDistrict'];
			$dataListAflFarmer[$i]['Village']               = $dataFarmer['Village'];
			$dataListAflFarmer[$i]['HandPhone']               = $dataFarmer['HandPhone'];
			$dataListAflFarmer[$i]['StatusFarmer']          = $dataFarmer['StatusFarmer'];
			$dataListAflFarmer[$i]['StatusComply']          = $dataFarmer['StatusComply'];
			$dataListAflFarmer[$i]['StatusComplyRemark']    = $dataFarmer['StatusComplyRemark'];
			$dataListAflFarmer[$i]['CertPercentageIncline'] = 0;
			$dataListAflFarmer[$i]['CertYield']             = 0;
			$dataListAflFarmer[$i]['CertNextYield']         = 0;
			$dataListAflFarmer[$i]['CertPohonTMHectare']    = 0;
			$dataListAflFarmer[$i]['CertTotalPohonHectare'] = 0;
			$dataListAflFarmer[$i]['CertStart']             = null;
			$dataListAflFarmer[$i]['CertEnd']               = null;

			$dataListAflFarmer[$i]['SalesLastYear']   = $dataFarmer['SalesLastYear'];
			$dataListAflFarmer[$i]['SalesLast2Years'] = $dataFarmer['SalesLast2Years'];
			$dataListAflFarmer[$i]['SalesLast3Years'] = $dataFarmer['SalesLast3Years'];

			$dataListAflFarmer[$i]['TotalCocoaFarm'] = $dataFarmer['TotalCocoaFarm'];
			$dataListAflFarmer[$i]['IMSCreatorTarget'] = $dataFarmer['IMSCreatorTarget'];

			//Susun CertStatusAudit ======================== (BEGIN)
			$CertStatusAudit          = ''; #'Comply', 'Not Comply', '-'
			$arrCertCommentAudit      = '';
			$CertAuditNotComplyReason = '';

			if ($dataListAflFarmer[$i]['StatusFarmer'] != '2') {

				if ($dataListAflFarmer[$i]['StatusComply'] != '2') {

					//cek Garden Status
					$lolosGardenStatus = false;
					if ($dataListAflFarmer[$i]['GarStatusGarden'] != "") {
						$arrTmpGardenStatus = explode(",", $dataListAflFarmer[$i]['GarStatusGarden']);
						foreach ($arrTmpGardenStatus as $key => $value) {
							if ($value != '2') {
								$lolosGardenStatus = true;
							}
						}
					} else {
						$lolosGardenStatus = true;
					}

					if ($lolosGardenStatus == true) {

						//cek Audit Log
						if ($dataListAflFarmer[$i]['CertStatusAuditLog'] != "") {

							//Cek By CertStatusAuditLog ======================================================== (Begin)
							$arrTmpStatusAudit = explode(",", $dataListAflFarmer[$i]['CertStatusAuditLog']);
							$isComplyAuditLog  = false;

							if ($arrTmpStatusAudit[0] != "") {
								foreach ($arrTmpStatusAudit as $key => $value) {
									if ($value != '2') {
										$isComplyAuditLog = true;
									}
								}
							}
							if ($isComplyAuditLog == true) {
								$CertStatusAudit     = 'Comply';
								$arrCertCommentAudit = 'Comply with Certification Rules';
							} else {
								$CertStatusAudit          = 'Not Comply';
								$arrCertCommentAudit      = 'All Audit Log Not Comply';
								$CertAuditNotComplyReason = 'Audit Log Not Comply';
							}
							//Cek By CertStatusAuditLog ======================================================== (End)

							//Harus dicek lagi by AuditLogMasukHutanLindung ========================== (Begin)
							$arrTmpMasukHutanLindung = explode(",",$dataListAflFarmer[$i]['AuditLogMasukHutanLindung']);
							$isComplyAuditLogMasukHutanLindung = true;

							if($arrTmpMasukHutanLindung[0] != ""){
								foreach ($arrTmpMasukHutanLindung as $key => $value) {
									if ($value == '1') {
										$isComplyAuditLogMasukHutanLindung = false;
									}
								}
							}
							if($isComplyAuditLogMasukHutanLindung == false){
								$CertStatusAudit          = 'Not Comply';
								$arrCertCommentAudit      = 'Ada Kebun Masuk Hutan Lindung';
								$CertAuditNotComplyReason = 'Kebun Masuk Hutan Lindung';
							}
							//Harus dicek lagi by AuditLogMasukHutanLindung ========================== (End)

						} else {
							$CertStatusAudit = '-';
						}

					} else {
						$CertStatusAudit                      = 'Not Comply';
						$arrCertCommentAudit                  = 'All Garden Status Not Active, ' . $dataFarmer['CommentTabelFarmer'];
						$CertAuditNotComplyReason             = 'Garden Status Not Active';
						$dataListAflFarmer[$i]['CertICSDate'] = $dataListAflFarmer[$i]['GarStatusDateUpdated'];
					}

				} else {

					$CertStatusAudit                      = 'Not Comply';
					$arrCertCommentAudit                  = $dataListAflFarmer[$i]['StatusComplyRemark'];
					$CertAuditNotComplyReason             = 'Pre AFL Not Comply';
					$dataListAflFarmer[$i]['CertICSDate'] = $dataFarmer['pafl_ICSDate'];
				}

			} else {
				$CertStatusAudit                      = 'Not Comply';
				$arrCertCommentAudit                  = 'Farmer not active, ' . $dataFarmer['CommentTabelFarmer'];
				$CertAuditNotComplyReason             = 'Farmer Not Active';
				$dataListAflFarmer[$i]['CertICSDate'] = $dataFarmer['DateUpdatedFarmer'];
			}
			$dataListAflFarmer[$i]['CertStatusAudit']          = $CertStatusAudit;
			$dataListAflFarmer[$i]['CertAuditRemark']          = $arrCertCommentAudit;
			$dataListAflFarmer[$i]['CertAuditNotComplyReason'] = $CertAuditNotComplyReason;

			//Susun CertStatusAudit ======================== (END)

			//Susun ulang untuk cek yang tidak comply ============== (Begin)
			switch ($dataListAflFarmer[$i]['CertAuditNotComplyReason']) {
				case 'Farmer Not Active':
					$sql = "SELECT
							a.`FarmerID`
						FROM
							ktv_members a
						WHERE
							a.`FarmerID` = ?
							AND DATE(a.`DateUpdated`) < DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
					";
					$query         = $this->db->query($sql, array($dataListAflFarmer[$i]['FarmerID'], $CertEventDate));
					$dataCekFarmer = $query->row_array();

					if ($dataCekFarmer['FarmerID'] != "") {
						$dataListAflFarmer[$i]['CertStatusAudit']          = '-';
						$dataListAflFarmer[$i]['CertAuditRemark']          = 'Revisit Farmer (Farmer Not Active State)';
						$dataListAflFarmer[$i]['CertAuditNotComplyReason'] = null;
						$dataListAflFarmer[$i]['CertICSDate'] = null;
					}
				break;
				case 'Garden Status Not Active':
					$sql = "SELECT
							a.`FarmerID`
						FROM
							`ktv_members_garden_status` a
						WHERE
							a.`FarmerID` = ?
							AND DATE(a.`DateUpdated`) < DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )";
					$query         = $this->db->query($sql, array($dataListAflFarmer[$i]['FarmerID'], $CertEventDate));
					$dataCekGarden = $query->row_array();

					if ($dataCekGarden['FarmerID'] != "") {
						$dataListAflFarmer[$i]['CertStatusAudit']          = '-';
						$dataListAflFarmer[$i]['CertAuditRemark']          = 'Revisit Farmer (Garden Status Not Active State)';
						$dataListAflFarmer[$i]['CertAuditNotComplyReason'] = null;
						$dataListAflFarmer[$i]['CertICSDate'] = null;
					}
				break;
			}
			//Susun ulang untuk cek yang tidak comply ============== (End)

			//CertFirstYear
			if ($dataListAflFarmer[$i]['CertFirstYear'] == "") {
				//ambil dari afl
				$sql = "SELECT
							b.`Year`
						FROM
							`ktv_certification_certified_farmer` a
							LEFT JOIN ktv_ims b ON a.`IMSID` = b.`IMSID`
						WHERE
							a.`FarmerID` = ?
						ORDER BY b.`IMSID` ASC
						LIMIT 1";
				$query                                  = $this->db->query($sql, array($dataListAflFarmer[$i]['FarmerID']));
				$data                                   = $query->row_array();
				$dataListAflFarmer[$i]['CertFirstYear'] = $data['Year'];

				//Cek kalau tidak ada lagi nilainya, baru ambil di IMS Yearnya
				if ($data['Year'] == "") {
					$dataListAflFarmer[$i]['CertFirstYear'] = $dataListAflFarmer[$i]['IMSYear'];
				}
			}
			$dataListAflFarmer[$i]['YearOfCertification'] = $dataListAflFarmer[$i]['IMSYear'];

			//Nilai2 yang bergantung dari CertStatusAudit ================= (BEGIN)
			switch ($dataListAflFarmer[$i]['CertStatusAudit']) {
				case 'Comply':
					//Ambil data Garden yang Comply dr Audit Log
					$sql = "SELECT
							IFNULL(SUM(IFNULL((
								(30/gar.HarvestRateDaysHighSeason) * gar.NrHighSeasonMonths * gar.AverageProdHighSeason
							) +(
								(30/gar.HarvestRateDaysLowSeason) * gar.NrLowSeasonMonths * gar.AverageProdLowSeason
							),0)),gar.AnnualProduction) AS CertHarvest
							, IFNULL(SUM(gar.`PlantationProductivity`),0) AS CertNextHarvest
							, IFNULL(SUM(gar.GardenAreaHa),0) AS CertHectare
							, IFNULL(COUNT(gar.PlotNr),0) AS CertFarmNr
							, IFNULL(SUM(gar.TreeTM),0) AS CertPohonTM
							, IFNULL(SUM(gar.TreeTBM),0) AS CertPohonTBM
							, IFNULL(SUM(gar.TreeTR),0) AS CertPohonTR
						FROM
							ktv_certification_pre_afl_garden pre_afl

							LEFT JOIN ktv_ims ims ON pre_afl.IMSID = ims.IMSID
							LEFT JOIN ktv_certification_holders hold ON ims.CertHolderID = hold.CertHolderID

							LEFT JOIN (
								SELECT
									au.`FarmerID`,
									au.GardenNr,
									au.SurveyNr,
									au.ICSDate,
									au.StatusAudit,
									au.Certification
								FROM
									(SELECT
										FarmerID,
										GardenNr,
										SurveyNr,
										Certification,
										MAX(ICSDate) ICSDate
									FROM
										ktv_certification_audit_log
									WHERE
										Certification != 0
										AND GardenNr != 0
										AND FarmerID = '{$dataListAflFarmer[$i]['FarmerID']}'
									GROUP BY FarmerID,
										GardenNr,
										SurveyNr,
										Certification) dt
									INNER JOIN ktv_certification_audit_log au
										ON dt.FarmerID = au.FarmerID
										AND dt.GardenNr = au.GardenNr
										AND dt.SurveyNr = au.SurveyNr
										AND dt.Certification = au.Certification
										AND dt.ICSDate = au.ICSDate
										AND au.`StatusAudit` IN (1,3)
										AND au.`FarmerID` = '{$dataListAflFarmer[$i]['FarmerID']}'
								WHERE 1=1
							) AS audit_log ON 1=1
								AND pre_afl.FarmerID = audit_log.FarmerID
								AND pre_afl.GardenNr = audit_log.GardenNr
								AND pre_afl.SurveyNr = audit_log.SurveyNr
								AND hold.CertProgID = audit_log.Certification

							LEFT JOIN ktv_survey_plot gar ON 1=1
								AND audit_log.FarmerID = gar.`MemberID`
								AND audit_log.GardenNr = gar.`PlotNr`
								AND audit_log.SurveyNr = gar.`SurveyNr`
								AND gar.`MemberID` = '{$dataListAflFarmer[$i]['FarmerID']}'
						WHERE
							pre_afl.`IMSID` = ?
							AND pre_afl.`FarmerID` = '{$dataListAflFarmer[$i]['FarmerID']}'";
					$dataGardenComply = $this->db->query($sql, array($IMSID))->row_array();

					$CertHarvest     = $dataGardenComply['CertHarvest'];
					$CertNextHarvest = $dataGardenComply['CertNextHarvest'];
					$CertHectare     = $dataGardenComply['CertHectare'];
					$CertFarmNr      = $dataGardenComply['CertFarmNr'];
					$CertPohonTM     = $dataGardenComply['CertPohonTM'];
					$CertPohonTBM    = $dataGardenComply['CertPohonTBM'];
					$CertPohonTR     = $dataGardenComply['CertPohonTR'];

					$selisihHarvest        = $CertNextHarvest - $CertHarvest;
					$CertPercentageIncline = ($selisihHarvest / $CertHarvest) * 100;
					if (is_nan($CertPercentageIncline) || is_infinite($CertPercentageIncline)) {
						$CertPercentageIncline = 0;
					}

					$CertYield = $CertHarvest / $CertHectare;
					if (is_nan($CertYield) || is_infinite($CertYield)) {
						$CertYield = 0;
					}

					$CertNextYield = $CertNextHarvest / $CertHectare;
					if (is_nan($CertNextYield) || is_infinite($CertNextYield)) {
						$CertNextYield = 0;
					}

					$CertPohonTMHectare = $CertPohonTM / $CertHectare;
					if (is_nan($CertPohonTMHectare) || is_infinite($CertPohonTMHectare)) {
						$CertPohonTMHectare = 0;
					}

					$CertTotalPohonHectare = ($CertPohonTM + $CertPohonTBM + $CertPohonTR) / $CertHectare;
					if (is_nan($CertTotalPohonHectare) || is_infinite($CertTotalPohonHectare)) {
						$CertTotalPohonHectare = 0;
					}

					break;
				case 'Not Comply':
					//Ambil data Garden yang Not Comply dr Audit Log
					$sql = "SELECT
							IFNULL(SUM(IFNULL((
								(30/gar.HarvestRateDaysHighSeason) * gar.NrHighSeasonMonths * gar.AverageProdHighSeason
							) +(
								(30/gar.HarvestRateDaysLowSeason) * gar.NrLowSeasonMonths * gar.AverageProdLowSeason
							),0)),gar.AnnualProduction) AS CertHarvest
							, IFNULL(SUM(gar.`PlantationProductivity`),0) AS CertNextHarvest
							, IFNULL(SUM(gar.GardenAreaHa),0) AS CertHectare
							, IFNULL(COUNT(gar.PlotNr),0) AS CertFarmNr
							, IFNULL(SUM(gar.TreeTM),0) AS CertPohonTM
							, IFNULL(SUM(gar.TreeTBM),0) AS CertPohonTBM
							, IFNULL(SUM(gar.TreeTR),0) AS CertPohonTR
						FROM
							ktv_certification_pre_afl_garden pre_afl

							LEFT JOIN ktv_ims ims ON pre_afl.IMSID = ims.IMSID
							LEFT JOIN ktv_certification_holders hold ON ims.CertHolderID = hold.CertHolderID

							LEFT JOIN (
								SELECT
									au.`FarmerID`,
									au.GardenNr,
									au.SurveyNr,
									au.ICSDate,
									au.StatusAudit,
									au.Certification
								FROM
									(SELECT
										FarmerID,
										GardenNr,
										SurveyNr,
										Certification,
										MAX(ICSDate) ICSDate
									FROM
										ktv_certification_audit_log
									WHERE
										Certification != 0
										AND GardenNr != 0
										AND FarmerID = '{$dataListAflFarmer[$i]['FarmerID']}'
									GROUP BY FarmerID,
										GardenNr,
										SurveyNr,
										Certification) dt
									INNER JOIN ktv_certification_audit_log au
										ON dt.FarmerID = au.FarmerID
										AND dt.GardenNr = au.GardenNr
										AND dt.SurveyNr = au.SurveyNr
										AND dt.Certification = au.Certification
										AND dt.ICSDate = au.ICSDate
										AND au.`StatusAudit` = 2
										AND au.`FarmerID` = '{$dataListAflFarmer[$i]['FarmerID']}'
								WHERE 1=1
							) AS audit_log ON 1=1
								AND pre_afl.FarmerID = audit_log.FarmerID
								AND pre_afl.GardenNr = audit_log.GardenNr
								AND pre_afl.SurveyNr = audit_log.SurveyNr
								AND hold.CertProgID = audit_log.Certification

							LEFT JOIN ktv_survey_plot gar ON 1=1
								AND audit_log.FarmerID = gar.`MemberID`
								AND audit_log.GardenNr = gar.`PlotNr`
								AND audit_log.SurveyNr = gar.`SurveyNr`
								AND gar.`MemberID` = '{$dataListAflFarmer[$i]['FarmerID']}'
						WHERE
							pre_afl.`IMSID` = ?
							AND pre_afl.`FarmerID` = '{$dataListAflFarmer[$i]['FarmerID']}'";
					$dataGardenNotComply = $this->db->query($sql, array($IMSID))->row_array();

					$CertHarvest     = $dataGardenNotComply['CertHarvest'];
					$CertNextHarvest = $dataGardenNotComply['CertNextHarvest'];
					$CertHectare     = $dataGardenNotComply['CertHectare'];
					$CertFarmNr      = $dataGardenNotComply['CertFarmNr'];
					$CertPohonTM     = $dataGardenNotComply['CertPohonTM'];
					$CertPohonTBM    = $dataGardenNotComply['CertPohonTBM'];
					$CertPohonTR     = $dataGardenNotComply['CertPohonTR'];

					$selisihHarvest        = $CertNextHarvest - $CertHarvest;
					$CertPercentageIncline = ($selisihHarvest / $CertHarvest) * 100;
					if (is_nan($CertPercentageIncline) || is_infinite($CertPercentageIncline)) {
						$CertPercentageIncline = 0;
					}

					$CertYield = $CertHarvest / $CertHectare;
					if (is_nan($CertYield) || is_infinite($CertYield)) {
						$CertYield = 0;
					}

					$CertNextYield = $CertNextHarvest / $CertHectare;
					if (is_nan($CertNextYield) || is_infinite($CertNextYield)) {
						$CertNextYield = 0;
					}

					$CertPohonTMHectare = $CertPohonTM / $CertHectare;
					if (is_nan($CertPohonTMHectare) || is_infinite($CertPohonTMHectare)) {
						$CertPohonTMHectare = 0;
					}

					$CertTotalPohonHectare = ($CertPohonTM + $CertPohonTBM + $CertPohonTR) / $CertHectare;
					if (is_nan($CertTotalPohonHectare) || is_infinite($CertTotalPohonHectare)) {
						$CertTotalPohonHectare = 0;
					}

					break;
				case '-':
					$CertHarvest           = 0;
					$CertNextHarvest       = 0;
					$CertHectare           = 0;
					$CertPercentageIncline = 0;
					$CertYield             = 0;
					$CertNextYield         = 0;
					$CertFarmNr            = 0;
					$CertPohonTM           = 0;
					$CertPohonTBM          = 0;
					$CertPohonTR           = 0;
					$CertPohonTMHectare    = 0;
					$CertTotalPohonHectare = 0;
					break;
			}

			$dataListAflFarmer[$i]['CertHarvest']           = $CertHarvest;
			$dataListAflFarmer[$i]['CertNextHarvest']       = $CertNextHarvest;
			$dataListAflFarmer[$i]['CertPercentageIncline'] = $CertPercentageIncline;
			$dataListAflFarmer[$i]['CertHectare']           = $CertHectare;
			$dataListAflFarmer[$i]['CertYield']             = $CertYield;
			$dataListAflFarmer[$i]['CertNextYield']         = $CertNextYield;
			$dataListAflFarmer[$i]['CertFarmNr']            = $CertFarmNr;
			$dataListAflFarmer[$i]['CertPohonTM']           = $CertPohonTM;
			$dataListAflFarmer[$i]['CertPohonTBM']          = $CertPohonTBM;
			$dataListAflFarmer[$i]['CertPohonTR']           = $CertPohonTR;
			$dataListAflFarmer[$i]['CertPohonTMHectare']    = $CertPohonTMHectare;
			$dataListAflFarmer[$i]['CertTotalPohonHectare'] = $CertTotalPohonHectare;
			//Nilai2 yang bergantung dari CertStatusAudit ================= (END)

			//Cek IMSCreator
			if($dataListAflFarmer[$i]['IMSCreatorTarget'] != ""){
				$IMSCreatorFarmer = $dataListAflFarmer[$i]['IMSCreatorTarget'];
			}else{
				$IMSCreatorFarmer = $dataListAflFarmer[$i]['IMSCreator'];
			}

			//echo '<pre>'; print_r($dataListAflFarmer); exit;
			$sql = "INSERT INTO `ktv_certification_afl_farmer` SET
					`IMSID` = ?,
					`FarmerID` = ?,
					`FarmerName` = ?,
					`CPGid` = ?,
					`GroupName` = ?,
					`Gender` = ?,
					`HandPhone` = ?,
					`Province` = ?,
					`District` = ?,
					`SubDistrict` = ?,
					`Village` = ?,
					`PolygonStatus` = ?,
					YearOfCertification = ?,
					`CertYear` = ?,
					`CertFirstYear` = ?,
					`CertICSDate` = ?,
					`CertStatusAudit` = ?,
					$SqlInsertAflFarmerStatusVerified
					`CertSurveyNr` = ?,
					`CertHarvest` = ?,
					`CertNextHarvest` = ?,
					`CertPercentageIncline` = ?,
					`CertHectare` = ?,
					`CertYield` = ?,
					`CertNextYield` = ?,
					`CertFarmNr` = ?,
					`CertFarmTotalNr` = ?,
					`CertTotalHectare` = ?,
					`CertPohonTM` = ?,
					`CertPohonTBM` = ?,
					`CertPohonTR` = ?,
					`CertPohonTMHectare` = ?,
					`CertTotalPohonHectare` = ?,
					`CertDateCollection` = ?,
					TotalCocoaFarm = ?,
					`IMSCreator` = ?,
					`IMSEditor` = ?,
					CertAuditNotComplyReason = ?,
					CertAuditRemark = ?,
					SalesLastYear = ?,
					SalesLast2Years = ?,
					SalesLast3Years = ?,
					StatusCode = 'active',
					`DateCreated` = NOW(),
					`CreatedBy` = ?";
			$p = array(
				$dataListAflFarmer[$i]['IMSID'],
				$dataListAflFarmer[$i]['FarmerID'],
				$dataListAflFarmer[$i]['FarmerName'],
				$dataListAflFarmer[$i]['CPGid'],
				$dataListAflFarmer[$i]['GroupName'],
				$dataListAflFarmer[$i]['Gender'],
				$dataListAflFarmer[$i]['HandPhone'],
				$dataListAflFarmer[$i]['Province'],
				$dataListAflFarmer[$i]['District'],
				$dataListAflFarmer[$i]['SubDistrict'],
				$dataListAflFarmer[$i]['Village'],
				$dataListAflFarmer[$i]['PolygonStatus'],
				$dataListAflFarmer[$i]['YearOfCertification'],
				$dataListAflFarmer[$i]['CertYear'],
				$dataListAflFarmer[$i]['CertFirstYear'],
				$dataListAflFarmer[$i]['CertICSDate'],
				$dataListAflFarmer[$i]['CertStatusAudit'],
				$dataListAflFarmer[$i]['CertSurveyNr'],
				$dataListAflFarmer[$i]['CertHarvest'],
				$dataListAflFarmer[$i]['CertNextHarvest'],
				$dataListAflFarmer[$i]['CertPercentageIncline'],
				$dataListAflFarmer[$i]['CertHectare'],
				$dataListAflFarmer[$i]['CertYield'],
				$dataListAflFarmer[$i]['CertNextYield'],
				$dataListAflFarmer[$i]['CertFarmNr'],
				$dataListAflFarmer[$i]['CertFarmTotalNr'],
				$dataListAflFarmer[$i]['CertTotalHectare'],
				$dataListAflFarmer[$i]['CertPohonTM'],
				$dataListAflFarmer[$i]['CertPohonTBM'],
				$dataListAflFarmer[$i]['CertPohonTR'],
				$dataListAflFarmer[$i]['CertPohonTMHectare'],
				$dataListAflFarmer[$i]['CertTotalPohonHectare'],
				$dataListAflFarmer[$i]['CertDateCollection'],
				$dataListAflFarmer[$i]['TotalCocoaFarm'],
				$IMSCreatorFarmer,
				$dataListAflFarmer[$i]['IMSEditor'],
				$dataListAflFarmer[$i]['CertAuditNotComplyReason'],
				$dataListAflFarmer[$i]['CertAuditRemark'],
				$dataListAflFarmer[$i]['SalesLastYear'],
				$dataListAflFarmer[$i]['SalesLast2Years'],
				$dataListAflFarmer[$i]['SalesLast3Years'],
				$_SESSION['userid'],
			);
			$query = $this->db->query($sql, $p);

			//Update ke pre_afl ---------------------- (Begin)
			if($dataListAflFarmer[$i]['CertStatusAudit'] != "-"){
				$sql = "UPDATE ktv_certification_pre_afl a SET
							a.`ICSDate` = ?
							, a.`DateUpdated` = NOW()
							, a.`LastModifiedBy` = ?
						WHERE
							a.`IMSID` = ?
							AND a.`FarmerID` = ?
						LIMIT 1";
				$p = array(
					$dataListAflFarmer[$i]['CertICSDate'],
					$_SESSION['userid'],
					$dataListAflFarmer[$i]['IMSID'],
					$dataListAflFarmer[$i]['FarmerID']
				);
				$query = $this->db->query($sql,$p);
			}
			//Update ke pre_afl ---------------------- (End)
		}

		//==================== AFL Farmer (END) ==========================//

		//==================== AFL Garden (BEGIN) ==========================//
		$sql = "SELECT
				a.`IMSID`
				, a.`FarmerID`
				, a.StatusComply
				, a.AuditRemark AS StatusComplyRemark
				, far.`MemberName`
				, far.`FarmerGroupID`
				, far.DateUpdated AS DateUpdatedFarmer
				, far.Comment AS CommentTabelFarmer
				, cpg.GroupName
				, CASE
					WHEN far.Gender='1' THEN 'Male'
					WHEN far.Gender='2' THEN 'Female'
				END AS Gender
				, far.HandPhone
				, kprov.Province AS Province
				, kdis.District AS District
				, subd.SubDistrict AS SubDistrict
				, vil.Village AS Village
				, far.StatusMember AS StatusFarmer
				, IF( (SELECT
						gpoly.MemberID
					FROM
						`ktv_survey_plot_polygon` gpoly
					WHERE
						gpoly.MemberID = b.FarmerID
						AND gpoly.PlotNr = b.GardenNr
						AND gpoly.StatusCheck = 'verified'
					LIMIT 1) IS NOT NULL,'Available','Notavailable'
				) AS PolygonStatus

				, (
					SELECT
						CONCAT(gar_sel_sub.Latitude,'@',gar_sel_sub.Longitude)
					FROM
						ktv_survey_plot gar_sel_sub
					WHERE
						gar_sel_sub.MemberID = b.FarmerID
						AND gar_sel_sub.PlotNr = b.GardenNr
						AND gar_sel_sub.Latitude != '0.000000'
						AND gar_sel_sub.Latitude IS NOT NULL
						AND gar_sel_sub.Longitude != '0.000000'
						AND gar_sel_sub.Longitude IS NOT NULL
					ORDER BY gar_sel_sub.SurveyNr DESC
					LIMIT 1
				) AS LatLong

				, (
					SELECT
						COUNT(subq_a.IMSID)+1 AS CertYear
					FROM
						ktv_certification_afl_garden subq_a
					WHERE
						subq_a.`FarmerID` = a.FarmerID
						AND subq_a.CertGardenNr = b.GardenNr
						AND subq_a.`StatusCode` = 'active'
				) AS CertYear
				, ims.Year AS IMSYear
				, audit_log.ICSDate AS CertICSDate
				, IFNULL(audit_log.StatusAudit,'-') AS CertStatusAuditLog

				, IFNULL(gstat.ActiveStatus,'-') AS GarStatusGarden
				, gstat.DateUpdated AS DateUpdatedGardenStatus

				, gar.SurveyNr AS CertSurveyNr
				, IFNULL(gar.AnnualProduction,0) AS CertHarvest
				, IFNULL(gar.PlantationProductivity,0) AS CertNextHarvest
				, '0' AS CertPercentageIncline #Hitung pada for nanti
				, gar.GardenAreaHa AS CertHectare
				, gar.PlotNr AS CertGardenNr
				, IFNULL(gar.TreeTM,0) AS CertPohonTM
				, IFNULL(gar.TreeTBM,0) AS CertPohonTBM
				, IFNULL(gar.TreeTR,0) AS CertPohonTR
				, '0' AS CertPohonTMHectare #Hitung pada for nanti
				, '0' AS CertTotalPohonHectare #Hitung pada for nanti
				, NULL AS CertStart
				, NULL AS CertEnd
				, gar.DateCollection AS CertDateCollection
				, cert.CandidateSelection AS CertCandidateSelection
				, audit_log.CommentAudit AS CertCommentAudit
				, audit_log.DateRevisionAudit AS CertDateRevisionAudit
				, audit_log.RecommendationAudit AS CertRecommendationAudit
				, audit_log.MasukHutanLindung AS MasukHutanLindung
				, (SELECT UserRealName FROM sys_user WHERE UserId = gar.CreatedBy) AS IMSCreator
				, (SELECT UserRealName FROM sys_user WHERE UserId = gar.LastModifiedBy) AS IMSEditor
				, (
					SELECT
						(SELECT UserRealName FROM sys_user WHERE UserId = sub_a.PICUserID)
					FROM
						`ktv_certification_pre_afl_target` sub_a
					WHERE
						sub_a.IMSID = a.IMSID
						AND sub_a.FarmerID = a.FarmerID
					LIMIT 1
				) AS IMSCreatorTarget
			FROM
				ktv_certification_pre_afl a
				INNER JOIN ktv_certification_pre_afl_garden b ON 1=1
					AND a.FarmerID = b.FarmerID
					AND a.IMSID = b.IMSID

				LEFT JOIN ktv_ims ims ON a.IMSID = ims.IMSID
				LEFT JOIN ktv_certification_holders hold ON ims.CertHolderID = hold.CertHolderID

				INNER JOIN ktv_members far ON a.`FarmerID` = far.`MemberID`
				LEFT JOIN ktv_farmer_group cpg ON far.FarmerGroupID = cpg.FarmerGroupID
				LEFT JOIN ktv_survey_plot gar ON 1=1
					AND b.FarmerID = gar.MemberID
					AND b.GardenNr = gar.PlotNr
					AND b.SurveyNr = gar.SurveyNr

				LEFT JOIN ktv_village vil ON 1=1
					AND far.VillageID = vil.VillageID
				LEFT JOIN ktv_subdistrict subd ON 1=1
					AND vil.SubDistrictID = subd.SubDistrictID
				LEFT JOIN ktv_district kdis on kdis.DistrictID=subd.DistrictID
				LEFT JOIN ktv_province kprov on kprov.ProvinceID=kdis.ProvinceID
				LEFT JOIN ktv_survey_plot_status gstat ON 1=1
					AND b.FarmerID = gstat.MemberID
					AND b.GardenNr = gstat.PlotNr

				LEFT JOIN ktv_certification cert ON 1=1
					AND b.FarmerID = cert.FarmerID
					AND b.GardenNr = cert.GardenNr
					AND b.SurveyNr = cert.SurveyNr
					AND hold.CertProgID = cert.Certification

				LEFT JOIN (
					SELECT
						au.`FarmerID`,
						au.GardenNr,
						au.SurveyNr,
						au.Certification,
						au.ICSDate,
						au.StatusAudit,
						au.CommentAudit,
						au.DateRevisionAudit,
						au.RecommendationAudit,
						au.MasukHutanLindung
					FROM
						(SELECT
							FarmerID,
							GardenNr,
							SurveyNr,
							Certification,
							MAX(ICSDate) ICSDate
						FROM
							ktv_certification_audit_log
						WHERE Certification != 0
							AND GardenNr != 0
						GROUP BY FarmerID,
							GardenNr,
							SurveyNr,
							Certification) dt
						LEFT JOIN ktv_certification_audit_log au
							ON dt.FarmerID = au.FarmerID
							AND dt.GardenNr = au.GardenNr
							AND dt.SurveyNr = au.SurveyNr
							AND dt.Certification = au.Certification
							AND dt.ICSDate = au.ICSDate
					WHERE 1=1
						#AND au.`StatusAudit` IN (1,3)
					GROUP BY au.`FarmerID`, au.`GardenNr`, au.`SurveyNr`
				) AS audit_log ON 1=1
					AND b.FarmerID = audit_log.FarmerID
					AND b.GardenNr = audit_log.GardenNr
					AND b.SurveyNr = audit_log.SurveyNr
					AND hold.CertProgID = audit_log.Certification

			WHERE 1=1
				AND a.`IMSID` = ?
				AND a.StatusAudit = '1' #Eligible
				AND a.StatusComply = '1' #Comply dari PreAFL
				AND a.StatusCode = 'active'
				AND gar.PlotNr IS NOT NULL #Ambil yang sudah ada Survey Gardennya saja
				$SqlSelectAflProcess
			ORDER BY a.FarmerID ASC";
		$query             = $this->db->query($sql, array($IMSID));
		$dataListAflGarden = $query->result_array();

		for ($i = 0; $i < count($dataListAflGarden); $i++) {
			//LatLong
			$arrTemp = explode('@', $dataListAflGarden[$i]['LatLong']);
			if ($arrTemp[0] != "" && $arrTemp[0] != "0.000000") {
				$CertLatitude = $arrTemp[0];
			} else {
				$CertLatitude = null;
			}

			if ($arrTemp[1] != "" && $arrTemp[1] != "0.000000") {
				$CertLongitude = $arrTemp[1];
			} else {
				$CertLongitude = null;
			}

			//CertFirstYear
			if ($dataListAflGarden[$i]['CertFirstYear'] == "") {
				//ambil dari afl
				$sql = "SELECT
							b.`Year`
						FROM
							`ktv_certification_certified_farmer` a
							LEFT JOIN ktv_ims b ON a.`IMSID` = b.`IMSID`
						WHERE
							a.`FarmerID` = ?
						ORDER BY b.`IMSID` ASC
						LIMIT 1";
				$query                                  = $this->db->query($sql, array($dataListAflGarden[$i]['FarmerID']));
				$data                                   = $query->row_array();
				$dataListAflGarden[$i]['CertFirstYear'] = $data['Year'];

				if($dataListAflGarden[$i]['CertFirstYear'] == ""){
					$dataListAflGarden[$i]['CertFirstYear'] = $dataListAflGarden[$i]['IMSYear'];
				}
			}

			//CertPercentageIncline
			$selisihHarvest                                 = $dataListAflGarden[$i]['CertNextHarvest'] - $dataListAflGarden[$i]['CertHarvest'];
			$dataListAflGarden[$i]['CertPercentageIncline'] = ($selisihHarvest / $dataListAflGarden[$i]['CertHarvest']) * 100;
			if (is_nan($dataListAflGarden[$i]['CertPercentageIncline']) || is_infinite($dataListAflGarden[$i]['CertPercentageIncline'])) {
				$dataListAflGarden[$i]['CertPercentageIncline'] = 0;
			}

			//CertPohonTMHectare
			$dataListAflGarden[$i]['CertPohonTMHectare'] = $dataListAflGarden[$i]['CertPohonTM'] / $dataListAflGarden[$i]['CertHectare'];
			if (is_nan($dataListAflGarden[$i]['CertPohonTMHectare']) || is_infinite($dataListAflGarden[$i]['CertPohonTMHectare'])) {
				$dataListAflGarden[$i]['CertPohonTMHectare'] = 0;
			}

			//CertTotalPohonHectare
			$dataListAflGarden[$i]['CertTotalPohonHectare'] = ($dataListAflGarden[$i]['CertPohonTM'] + $dataListAflGarden[$i]['CertPohonTBM'] + $dataListAflGarden[$i]['CertPohonTR']) / $dataListAflGarden[$i]['CertHectare'];
			if (is_nan($dataListAflGarden[$i]['CertTotalPohonHectare']) || is_infinite($dataListAflGarden[$i]['CertTotalPohonHectare'])) {
				$dataListAflGarden[$i]['CertTotalPohonHectare'] = 0;
			}

			// Penentuan CertStatusAudit ================== (BEGIN)
			$CertStatusAudit          = ''; #'1', '2', '3', '-'
			$arrCertCommentAudit      = '';
			$CertAuditNotComplyReason = '';

			if ($dataListAflGarden[$i]['StatusFarmer'] != '2') {

				if ($dataListAflGarden[$i]['StatusComply'] != '2') {

					//cek Garden Status
					if ($dataListAflGarden[$i]['GarStatusGarden'] != '2') {

						//Cek Status Audit Log ================================================= (Begin)
						if ($dataListAflGarden[$i]['CertStatusAuditLog'] != '2') {
							$CertStatusAudit = $dataListAflGarden[$i]['CertStatusAuditLog'];
						} else {
							$CertStatusAudit          = '2';
							$arrCertCommentAudit      = 'Audit Log Not Comply';
							$CertAuditNotComplyReason = 'Audit Log Not Comply';
						}
						//Cek Status Audit Log ================================================= (End)

						//Cek Audit Log Hutan Lindung ================================================= (Begin)
						if($dataListAflGarden[$i]['MasukHutanLindung'] == "1"){
							$CertStatusAudit          = '2';
							$arrCertCommentAudit      = 'Kebun Masuk Hutan Lindung';
							$CertAuditNotComplyReason = 'Kebun Masuk Hutan Lindung';
						}
						//Cek Audit Log Hutan Lindung ================================================= (End)

					} else {
						$CertStatusAudit                      = '2';
						$arrCertCommentAudit                  = 'Garden Status not active, ' . $dataListAflGarden[$i]['CommentTabelFarmer'];
						$CertAuditNotComplyReason             = 'Garden Status Not Active';
						$dataListAflGarden[$i]['CertICSDate'] = $dataListAflGarden[$i]['DateUpdatedGardenStatus'];
					}

				} else {

					$CertStatusAudit          = '2';
					$arrCertCommentAudit      = $dataListAflGarden[$i]['StatusComplyRemark'];
					$CertAuditNotComplyReason = 'Pre AFL Not Comply';

				}

			} else {
				$CertStatusAudit                      = '2';
				$arrCertCommentAudit                  = 'Farmer not active, ' . $dataListAflGarden[$i]['CommentTabelFarmer'];
				$CertAuditNotComplyReason             = 'Farmer Not Active';
				$dataListAflGarden[$i]['CertICSDate'] = $dataListAflGarden[$i]['DateUpdatedFarmer'];
			}

			$dataListAflGarden[$i]['CertStatusAudit']          = $CertStatusAudit;
			$dataListAflGarden[$i]['CertAuditRemark']          = $arrCertCommentAudit;
			$dataListAflGarden[$i]['CertAuditNotComplyReason'] = $CertAuditNotComplyReason;
			// Penentuan CertStatusAudit ================== (END)

			//Susun ulang untuk cek yang tidak comply ============== (Begin)
			switch ($dataListAflGarden[$i]['CertAuditNotComplyReason']) {
				case 'Farmer Not Active':
					$sql = "SELECT
							a.`MemberID` FarmerID
						FROM
							ktv_members a
						WHERE
							a.`MemberID` = ?
							AND DATE(a.`DateUpdated`) < DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )";
					$query         = $this->db->query($sql, array($dataListAflGarden[$i]['FarmerID'], $CertEventDate));
					$dataCekFarmer = $query->row_array();

					if ($dataCekFarmer['FarmerID'] != "") {
						$dataListAflGarden[$i]['CertStatusAudit']          = '-';
						$dataListAflGarden[$i]['CertAuditRemark']          = 'Revisit Farmer (Farmer Not Active State)';
						$dataListAflGarden[$i]['CertAuditNotComplyReason'] = null;
					}
					break;
				case 'Garden Status Not Active':
					$sql = "SELECT
							a.`MemberID` FarmerID
						FROM
							`ktv_survey_plot_status` a
						WHERE
							a.`MemberID` = ?
							AND a.PlotNr = ?
							AND DATE(a.`DateUpdated`) < DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )";
					$query         = $this->db->query($sql, array($dataListAflGarden[$i]['FarmerID'], $dataListAflGarden[$i]['CertGardenNr'], $CertEventDate));
					$dataCekGarden = $query->row_array();

					if ($dataCekGarden['FarmerID'] != "") {
						$dataListAflGarden[$i]['CertStatusAudit']          = '-';
						$dataListAflGarden[$i]['CertAuditRemark']          = 'Revisit Farmer (Garden Status Not Active State)';
						$dataListAflGarden[$i]['CertAuditNotComplyReason'] = null;
					}
					break;
			}
			//Susun ulang untuk cek yang tidak comply ============== (End)

			//Cek IMSCreator
			if($dataListAflGarden[$i]['IMSCreatorTarget'] != ""){
				$IMSCreatorGarden = $dataListAflGarden[$i]['IMSCreatorTarget'];
			}else{
				$IMSCreatorGarden = $dataListAflGarden[$i]['IMSCreator'];
			}

			$sql = "INSERT INTO `ktv_certification_afl_garden` SET
					`IMSID` = ?,
					`FarmerID` = ?,
					`FarmerName` = ?,
					`CPGid` = ?,
					`GroupName` = ?,
					`Gender` = ?,
					`HandPhone` = ?,
					`Province` = ?,
					`District` = ?,
					`SubDistrict` = ?,
					`Village` = ?,
					`PolygonStatus` = ?,
					CertLatitude = ?,
					CertLongitude = ?,
					`CertYear` = ?,
					`CertFirstYear` = ?,
					`CertICSDate` = ?,
					`CertStatusAudit` = ?,
					`CertSurveyNr` = ?,
					`CertHarvest` = ?,
					`CertNextHarvest` = ?,
					`CertPercentageIncline` = ?,
					`CertHectare` = ?,
					`CertGardenNr` = ?,
					`CertPohonTM` = ?,
					`CertPohonTBM` = ?,
					`CertPohonTR` = ?,
					`CertPohonTMHectare` = ?,
					`CertTotalPohonHectare` = ?,
					`CertDateCollection` = ?,
					`CertCandidateSelection` = ?,
					`CertCommentAudit` = ?,
					`CertDateRevisionAudit` = ?,
					`CertRecommendationAudit` = ?,
					`IMSCreator` = ?,
					`IMSEditor` = ?,
					CertAuditNotComplyReason = ?,
					CertAuditRemark = ?,
					`DateCreated` = NOW(),
					StatusCode = 'active',
					`CreatedBy` = ?";
			$p = array(
				$dataListAflGarden[$i]['IMSID'],
				$dataListAflGarden[$i]['FarmerID'],
				$dataListAflGarden[$i]['FarmerName'],
				$dataListAflGarden[$i]['CPGid'],
				$dataListAflGarden[$i]['GroupName'],
				$dataListAflGarden[$i]['Gender'],
				$dataListAflGarden[$i]['HandPhone'],
				$dataListAflGarden[$i]['Province'],
				$dataListAflGarden[$i]['District'],
				$dataListAflGarden[$i]['SubDistrict'],
				$dataListAflGarden[$i]['Village'],
				$dataListAflGarden[$i]['PolygonStatus'],
				$CertLatitude,
				$CertLongitude,
				$dataListAflGarden[$i]['CertYear'],
				$dataListAflGarden[$i]['CertFirstYear'],
				$dataListAflGarden[$i]['CertICSDate'],
				$dataListAflGarden[$i]['CertStatusAudit'],
				$dataListAflGarden[$i]['CertSurveyNr'],
				$dataListAflGarden[$i]['CertHarvest'],
				$dataListAflGarden[$i]['CertNextHarvest'],
				$dataListAflGarden[$i]['CertPercentageIncline'],
				$dataListAflGarden[$i]['CertHectare'],
				$dataListAflGarden[$i]['CertGardenNr'],
				$dataListAflGarden[$i]['CertPohonTM'],
				$dataListAflGarden[$i]['CertPohonTBM'],
				$dataListAflGarden[$i]['CertPohonTR'],
				$dataListAflGarden[$i]['CertPohonTMHectare'],
				$dataListAflGarden[$i]['CertTotalPohonHectare'],
				$dataListAflGarden[$i]['CertDateCollection'],
				$dataListAflGarden[$i]['CertCandidateSelection'],
				$dataListAflGarden[$i]['CertCommentAudit'],
				$dataListAflGarden[$i]['CertDateRevisionAudit'],
				$dataListAflGarden[$i]['CertRecommendationAudit'],
				$IMSCreatorGarden,
				$dataListAflGarden[$i]['IMSEditor'],
				$dataListAflGarden[$i]['CertAuditNotComplyReason'],
				$dataListAflGarden[$i]['CertAuditRemark'],
				$_SESSION['userid'],
			);
			$query = $this->db->query($sql, $p);
		}
		//==================== AFL Garden (END)   ==========================//

		//==================== New rule AFL Garden (BEGIN)   ==========================//
		//* Jika ada salah satu afl_garden yg masuk hutan lindung, berarti semua garden dari farmer tsb jadi NotComply
		$sql = "UPDATE ktv_certification_afl_garden a SET
					a.`CertStatusAudit` = '2',
					a.CertAuditRemark = 'Kebun Masuk Hutan Lindung',
					a.CertAuditNotComplyReason = 'Kebun Masuk Hutan Lindung'
				WHERE
					a.`IMSID` = ?
					AND a.`FarmerID` IN (
						SELECT
							wrap.FarmerID
						FROM
						(
							SELECT
								sa.`FarmerID`
							FROM
								`ktv_certification_afl_garden` sa
							WHERE
								sa.`IMSID` = ?
								AND sa.`CertAuditNotComplyReason` = 'Kebun Masuk Hutan Lindung'
							GROUP BY sa.`FarmerID`
						) AS wrap
					)";
		$query = $this->db->query($sql,array($IMSID,$IMSID));
		//==================== New rule AFL Garden (END)   ==========================//

		//======================= Lengkapi Data bersifat Summary pada AFL FARMER & GARDEN (BEGIN)   ==========================//
		$sql="SELECT
				aflf.`FarmerID`
				, COUNT(aflg.`FarmerID`) AS TotalAuditedFarm
			FROM
				`ktv_certification_afl_farmer` aflf
				LEFT JOIN `ktv_certification_afl_garden` aflg ON 1=1
					AND aflf.`IMSID` = aflg.`IMSID`
					AND aflf.`FarmerID` = aflg.`FarmerID`
			WHERE
				aflf.`IMSID` = ?
				AND aflg.CertStatusAudit IN (1,2,3)
			GROUP BY aflf.`FarmerID`
			ORDER BY aflf.`FarmerID`";
		$DataAflSum = $this->db->query($sql,array($IMSID))->result_array();

		for ($i=0; $i < count($DataAflSum); $i++) {
			//Cari IMSID dan nilai2nya untuk Event Sebelumnya (Begin)
			$sql="SELECT
					afl.`FarmerID`
					, ims.`IMSID`
					, afl.TotalAuditedFarm
					, cfl.`CertFarmNr`
				FROM
					`ktv_certification_afl_farmer` afl
					LEFT JOIN ktv_ims ims ON afl.`IMSID` = ims.`IMSID`
					LEFT JOIN `ktv_certification_certified_farmer` cfl ON 1=1
						AND ims.`IMSID` = cfl.`IMSID`
						AND afl.`FarmerID` = cfl.`FarmerID`
				WHERE
					afl.`FarmerID` = ?
					AND ims.`IMSID` < ?
				ORDER BY ims.`IMSID` DESC
				LIMIT 1";
			$DataPrevIMS = $this->db->query($sql,array($DataAflSum[$i]['FarmerID'],$IMSID))->row_array();

			$TotalAuditedFarm = $DataAflSum[$i]['TotalAuditedFarm'];
			if($DataPrevIMS['TotalAuditedFarm'] != "")
				$TotalAuditedFarmLastYear = $DataPrevIMS['TotalAuditedFarm'];
			else
				$TotalAuditedFarmLastYear = null;
			if($DataPrevIMS['CertFarmNr'] != "")
				$CertFarmNrLastYear = $DataPrevIMS['CertFarmNr'];
			else
				$CertFarmNrLastYear = null;
			//Cari IMSID dan nilai2nya untuk Event Sebelumnya (End)

			//Update ke AFL Sekarang
			$sql="UPDATE `ktv_certification_afl_farmer` a SET
					a.TotalAuditedFarm = ?,
					a.`TotalAuditedFarmLastYear` = ?,
					a.`CertFarmNrLastYear` = ?
				WHERE
					a.`IMSID` = ?
					AND a.`FarmerID` = ?
				LIMIT 1";
			$p = array(
				$TotalAuditedFarm,
				$TotalAuditedFarmLastYear,
				$CertFarmNrLastYear,
				$IMSID,
				$DataAflSum[$i]['FarmerID']
			);
			$query = $this->db->query($sql,$p);
		}
		//======================= Lengkapi Data bersifat Summary pada AFL FARMER & GARDEN (END)   ==========================//

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			$results['success'] = false;
			$results['message'] = "Failed to generate AFL";
		} else {
			$this->db->trans_commit();
			$results['success'] = true;
			$results['message'] = "AFL Generated";
		}
		return $results;
	}

	public function GenAuditFarmFarmNrLastYear($IMSID){
		$this->db->trans_begin();

		//======================= Lengkapi Data bersifat Summary pada AFL FARMER & GARDEN (BEGIN)   ==========================//
		$sql="SELECT
				aflf.`FarmerID`
				, COUNT(aflg.`FarmerID`) AS TotalAuditedFarm
			FROM
				`ktv_certification_afl_farmer` aflf
				LEFT JOIN `ktv_certification_afl_garden` aflg ON 1=1
					AND aflf.`IMSID` = aflg.`IMSID`
					AND aflf.`FarmerID` = aflg.`FarmerID`
			WHERE
				aflf.`IMSID` = ?
				AND aflg.CertStatusAudit IN (1,2,3)
			GROUP BY aflf.`FarmerID`
			ORDER BY aflf.`FarmerID`";
		$DataAflSum = $this->db->query($sql,array($IMSID))->result_array();

		for ($i=0; $i < count($DataAflSum); $i++) {
			//Cari IMSID dan nilai2nya untuk Event Sebelumnya (Begin)
			$sql="SELECT
					afl.`FarmerID`
					, ims.`IMSID`
					, afl.TotalAuditedFarm
					, cfl.`CertFarmNr`
				FROM
					`ktv_certification_afl_farmer` afl
					LEFT JOIN ktv_ims ims ON afl.`IMSID` = ims.`IMSID`
					LEFT JOIN `ktv_certification_certified_farmer` cfl ON 1=1
						AND ims.`IMSID` = cfl.`IMSID`
						AND afl.`FarmerID` = cfl.`FarmerID`
				WHERE
					afl.`FarmerID` = ?
					AND ims.`IMSID` < ?
				ORDER BY ims.`IMSID` DESC
				LIMIT 1";
			$DataPrevIMS = $this->db->query($sql,array($DataAflSum[$i]['FarmerID'],$IMSID))->row_array();

			$TotalAuditedFarm = $DataAflSum[$i]['TotalAuditedFarm'];
			if($DataPrevIMS['TotalAuditedFarm'] != "")
				$TotalAuditedFarmLastYear = $DataPrevIMS['TotalAuditedFarm'];
			else
				$TotalAuditedFarmLastYear = null;
			if($DataPrevIMS['CertFarmNr'] != "")
				$CertFarmNrLastYear = $DataPrevIMS['CertFarmNr'];
			else
				$CertFarmNrLastYear = null;
			//Cari IMSID dan nilai2nya untuk Event Sebelumnya (End)

			//Update ke AFL Sekarang
			$sql="UPDATE `ktv_certification_afl_farmer` a SET
					a.TotalAuditedFarm = ?,
					a.`TotalAuditedFarmLastYear` = ?,
					a.`CertFarmNrLastYear` = ?
				WHERE
					a.`IMSID` = ?
					AND a.`FarmerID` = ?
				LIMIT 1";
			$p = array(
				$TotalAuditedFarm,
				$TotalAuditedFarmLastYear,
				$CertFarmNrLastYear,
				$IMSID,
				$DataAflSum[$i]['FarmerID']
			);
			$query = $this->db->query($sql,$p);
		}
		//======================= Lengkapi Data bersifat Summary pada AFL FARMER & GARDEN (END)   ==========================//

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			$results['success'] = false;
			$results['message'] = "Process Failed";
		} else {
			$this->db->trans_commit();
			$results['success'] = true;
			$results['message'] = "Success";
		}
		return $results;
	}

	public function ImsEventDetailSummaryAfl($IMSID)
	{
		$dataReturn = array();

		$sql = "SELECT
				SUM(IF(a.`CertStatusAudit`='Comply',1,0)) AS Comply
				, SUM(IF(a.`CertStatusAudit`='Not Comply',1,0)) AS NotComply
				, SUM(IF(a.`CertStatusAudit`='-',1,0)) AS NoStatusYet
				, a.`DateCreated` AS LastUpdated
			FROM
				`ktv_certification_afl_farmer` a
			WHERE
				a.`IMSID` = ?
				AND a.`StatusCode` = 'active'";
		$query      = $this->db->query($sql, array($IMSID));
		$dataFarmer = $query->row_array();

		$dataReturn[0]['Data']        = lang('Farmer');
		$dataReturn[0]['Comply']      = $dataFarmer['Comply'];
		$dataReturn[0]['NotComply']   = $dataFarmer['NotComply'];
		$dataReturn[0]['NoStatusYet'] = $dataFarmer['NoStatusYet'];
		$dataReturn[0]['LastUpdated'] = $dataFarmer['LastUpdated'];

		$sql = "SELECT
				SUM(IF(a.`CertStatusAudit`='1' OR a.`CertStatusAudit`='3',1,0)) AS Comply
				, SUM(IF(a.`CertStatusAudit`='2',1,0)) AS NotComply
				, SUM(IF(a.`CertStatusAudit`='-',1,0)) AS NoStatusYet
				, a.`DateCreated` AS LastUpdated
			FROM
				`ktv_certification_afl_garden` a
			WHERE
				a.`IMSID` = ?
				AND a.`StatusCode` = 'active'";
		$query      = $this->db->query($sql, array($IMSID));
		$dataGarden = $query->row_array();

		$dataReturn[1]['Data']        = lang('Cocoa Farm');
		$dataReturn[1]['Comply']      = $dataGarden['Comply'];
		$dataReturn[1]['NotComply']   = $dataGarden['NotComply'];
		$dataReturn[1]['NoStatusYet'] = $dataGarden['NoStatusYet'];
		$dataReturn[1]['LastUpdated'] = $dataGarden['LastUpdated'];

		return $dataReturn;
	}

	public function ImsEventDetailSummaryKpi_Target($IMSID, $dataIMS)
	{
		$data = array();

		//get total pre afl farmer
		$sql = "SELECT
				COUNT(a.`PreAFLID`) AS BANYAK
			FROM
				ktv_certification_pre_afl a
			WHERE
				a.`StatusCode` = 'active'
				AND a.StatusAudit = '1'
				AND a.`IMSID` = ?";
		$query         = $this->db->query($sql, array($IMSID));
		$dataAflFarmer = $query->row_array();

		//get total pre afl garden
		$sql = "SELECT
				COUNT(a.`IMSID`) AS BANYAK
			FROM
				ktv_certification_pre_afl_garden a
			WHERE
				a.`IMSID` = ?";
		$query         = $this->db->query($sql, array($IMSID));
		$dataAflGarden = $query->row_array();

		$data['rowLabel']          = lang('Target');
		$data['FarmerVisited']     = $dataAflFarmer['BANYAK'];
		$data['Farmer']            = $dataAflFarmer['BANYAK'];
		$data['PostHarvest']       = $dataAflFarmer['BANYAK'];
		$data['PPI']               = $dataAflFarmer['BANYAK'];
		$data['Garden']            = $dataAflGarden['BANYAK'];
		$data['GardenWithPolygon'] = $dataAflGarden['BANYAK'];
		$data['Certification']     = $dataAflGarden['BANYAK'];
		$data['AuditLog']          = $dataAflGarden['BANYAK'];

		return $data;
	}

	public function ImsEventDetailSummaryKpi_Achieve($IMSID, $dataIMS, $opsiAchieve)
	{
		$data = array();

		if ($opsiAchieve == 'FA') {
			$filterAchieve = "
				AND tar.`PICUserID` IS NOT NULL
				AND tar.`PICUserID` != ''
			";
		} else {
			$filterAchieve = "
				AND (tar.`PICUserID` IS NULL OR tar.`PICUserID` = '')
			";
		}

		//Capaian Farmer Visited (Farmer, Garden Status, dan Survey Garden)
		$sql = "SELECT
				a.`FarmerID`
			FROM
				`ktv_certification_pre_afl_target` tar
				LEFT JOIN ktv_certification_pre_afl afl ON tar.`FarmerID` = afl.`FarmerID`

				LEFT JOIN ktv_certification_pre_afl_garden aflg ON 1=1
					AND afl.`FarmerID` = aflg.`FarmerID`
				INNER JOIN ktv_members a ON afl.`FarmerID` = a.`FarmerID`

				LEFT JOIN ktv_members_garden gar ON 1=1
					AND a.`FarmerID` = gar.`FarmerID`
					AND aflg.`GardenNr` = gar.`GardenNr`
					AND aflg.`SurveyNr` = gar.`SurveyNr`

				LEFT JOIN ktv_members_garden_status gstat ON 1=1
					AND a.`FarmerID` = gstat.`FarmerID`
					AND aflg.`GardenNr` = gstat.`GardenNr`
			WHERE
				a.`StatusCode` = 'active'
				AND afl.StatusAudit = '1'
				AND afl.`IMSID` = ?
				AND (
					(DATE(a.`DateUpdated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )) #Farmer
					OR
					(
						DATE(gar.`DateUpdated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
						OR
						DATE(gar.`DateCreated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
					) # Garden Survey
					OR
					(
						(
							DATE(gstat.`DateUpdated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
							AND
							gstat.`LastModifiedBy` != '1' #Exclude Admin
						)
						OR
						(
							DATE(gstat.`DateCreated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
							AND
							gstat.`CreatedBy` != '1' #Exclude Admin
						)
					) # Garden Status
				)
				$filterAchieve
			GROUP BY a.`FarmerID`";
		$query                  = $this->db->query($sql, array($IMSID, $dataIMS['CertEventDate'], $dataIMS['CertEventDate'], $dataIMS['CertEventDate'], $dataIMS['CertEventDate'], $dataIMS['CertEventDate']));
		$dataCapFarmerVisited   = $query->result_array();
		$dataCapaiFarmerVisited = count($dataCapFarmerVisited);

		//Capaian Farmer
		$sql = "SELECT
				COUNT(a.`DateUpdated`) AS BANYAK
			FROM
				`ktv_certification_pre_afl_target` tar
				LEFT JOIN ktv_certification_pre_afl afl ON tar.`FarmerID` = afl.`FarmerID`

				INNER JOIN ktv_members a ON afl.`FarmerID` = a.`FarmerID`
			WHERE
				a.`StatusCode` = 'active'
				AND afl.StatusAudit = '1'
				AND afl.`IMSID` = ?
				AND tar.`IMSID` = ?
				AND DATE(a.`DateUpdated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
				$filterAchieve
			";
		$query         = $this->db->query($sql, array($IMSID, $IMSID, $dataIMS['CertEventDate']));
		$dataCapFarmer = $query->row_array();

		//Capaian Garden
		$sql = "SELECT
				COUNT(gar.`FarmerID`) AS BANYAK
				, COUNT(gpoly.FarmerID) AS GardenPolygonNya
			FROM

				ktv_certification_pre_afl_garden a
				LEFT JOIN `ktv_certification_pre_afl_target` tar ON 1=1
					AND a.`FarmerID` = tar.`FarmerID`
					AND a.`IMSID` = tar.`IMSID`

				LEFT JOIN ktv_members_garden gar ON 1=1
					AND a.`FarmerID` = gar.`FarmerID`
					AND a.`SurveyNr` = gar.`SurveyNr`
					AND a.`GardenNr` = gar.`GardenNr`
					AND (
						DATE(gar.`DateUpdated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
						OR
						DATE(gar.`DateCreated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
					)

				LEFT JOIN (
					SELECT
						a.`FarmerID`
						, a.`GardenNr`
					FROM
						ktv_members_garden_area a
					WHERE
						a.`Status` != 'nullified'
						AND a.`FarmerID` != '0'
						AND a.`GardenNr` != '0'
					GROUP BY a.`FarmerID`, a.`GardenNr`
				) AS gpoly ON 1=1
					AND a.`FarmerID` = gpoly.FarmerID
					AND a.`GardenNr` = gpoly.GardenNr
			WHERE
				a.`IMSID` = ?
				$filterAchieve
			;";
		$query         = $this->db->query($sql, array($dataIMS['CertEventDate'], $dataIMS['CertEventDate'], $IMSID));
		$dataCapGarden = $query->row_array();

		//Capaian PostHarvest
		$sql = "SELECT
					COUNT(ph.`FarmerID`) AS BANYAK
				FROM
					(
						SELECT
							gar.`FarmerID`
							, MAX(gar.`SurveyNr`) AS SurveyNr
						FROM
							ktv_certification_pre_afl_garden a
							LEFT JOIN `ktv_certification_pre_afl_target` tar ON 1=1
								AND a.`FarmerID` = tar.`FarmerID`
								AND a.`IMSID` = tar.`IMSID`

							LEFT JOIN ktv_members_garden gar ON 1=1
								AND a.`FarmerID` = gar.`FarmerID`
								AND a.`SurveyNr` = gar.`SurveyNr`
								AND a.`GardenNr` = gar.`GardenNr`
						WHERE
							a.`IMSID` = ?
							AND gar.`StatusCode` = 'active'
							AND (
								DATE(gar.`DateUpdated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
								OR
								DATE(gar.`DateCreated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
							)
							$filterAchieve
						GROUP BY gar.`FarmerID`
					) AS tbl_garden
					LEFT JOIN ktv_members_post_harvest ph ON 1=1
						AND tbl_garden.FarmerID = ph.`FarmerID`
						AND tbl_garden.SurveyNr = ph.`SurveyNr`
				WHERE
					ph.`StatusCode` = 'active'
					AND (
						DATE(ph.`DateUpdated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
						OR
						DATE(ph.`DateCreated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
					)";
		$query     = $this->db->query($sql, array($IMSID, $dataIMS['CertEventDate'], $dataIMS['CertEventDate'], $dataIMS['CertEventDate'], $dataIMS['CertEventDate']));
		$dataCapPh = $query->row_array();

		//Capaian PPI
		$sql = "SELECT
				COUNT(tbl_grup.FarmerID) AS BANYAK
			FROM
			(
				SELECT
					afl.`FarmerID`
				FROM
					`ktv_certification_pre_afl_target` tar
					LEFT JOIN ktv_certification_pre_afl afl ON tar.`FarmerID` = afl.`FarmerID`

					INNER JOIN ktv_members a ON afl.`FarmerID` = a.`FarmerID`
					LEFT JOIN ktv_ppiscore2012 ppi ON afl.`FarmerID` = ppi.`FarmerID`
				WHERE
					afl.`IMSID` = ?
					AND afl.`StatusCode` = 'active'
					AND afl.StatusAudit = '1'
					AND a.`StatusCode` = 'active'
					AND ppi.`StatusCode` = 'active'
					AND (
						DATE(ppi.`DateUpdated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
						OR
						DATE(ppi.`DateCreated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
					)
					$filterAchieve
				GROUP BY afl.`FarmerID`
			) AS tbl_grup";
		$query      = $this->db->query($sql, array($IMSID, $dataIMS['CertEventDate'], $dataIMS['CertEventDate']));
		$dataCapPpi = $query->row_array();

		//Capaian Certification
		$sql = "SELECT
				COUNT(afl.`FarmerID`) AS BANYAK
			FROM
				ktv_certification_pre_afl_garden afl
				LEFT JOIN `ktv_certification_pre_afl_target` tar ON 1=1
					AND afl.`FarmerID` = tar.`FarmerID`
					AND afl.`IMSID` = tar.`IMSID`

				LEFT JOIN ktv_ims ims ON afl.`IMSID` = ims.`IMSID`
				LEFT JOIN ktv_certification_holders hold ON ims.CertHolderID = hold.CertHolderID

				LEFT JOIN ktv_certification cert ON 1=1
					AND afl.`FarmerID` = cert.`FarmerID`
					AND afl.`GardenNr` = cert.`GardenNr`
					AND afl.`SurveyNr` = cert.`SurveyNr`
					AND hold.`CertProgID` = cert.`Certification`
			WHERE
				ims.`StatusCode` = 'active'
				AND ims.`IMSID` = ?
				AND (
					DATE(cert.`DateUpdated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
					OR
					DATE(cert.`DateCreated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
				)
				$filterAchieve
				";
		$query                = $this->db->query($sql, array($IMSID, $dataIMS['CertEventDate'], $dataIMS['CertEventDate']));
		$dataCapCertification = $query->row_array();

		//Capaian Audit Log
		$sql = "SELECT
				COUNT(afl.`FarmerID`) AS BANYAK
			FROM
				ktv_certification_pre_afl_garden afl
				LEFT JOIN `ktv_certification_pre_afl_target` tar ON 1=1
					AND afl.`FarmerID` = tar.`FarmerID`
					AND afl.`IMSID` = tar.`IMSID`

				LEFT JOIN ktv_ims ims ON afl.`IMSID` = ims.`IMSID`
				LEFT JOIN ktv_certification_holders hold ON ims.CertHolderID = hold.CertHolderID

				LEFT JOIN (
					SELECT
						au.`FarmerID`
						, au.`SurveyNr`
						, au.`GardenNr`
						, au.Certification
						, au.`ICSDate`
						, au.`DateCreated`
						, au.`DateUpdated`
					FROM
						(SELECT
							FarmerID,
							GardenNr,
							SurveyNr,
							Certification,
							MAX(ICSDate) ICSDate
						FROM
							ktv_certification_audit_log
						WHERE Certification != 0
							AND GardenNr != 0
						GROUP BY FarmerID,
							GardenNr,
							SurveyNr,
							Certification) dt
						INNER JOIN ktv_certification_audit_log au
							ON dt.FarmerID = au.FarmerID
							AND dt.GardenNr = au.GardenNr
							AND dt.SurveyNr = au.SurveyNr
							AND dt.Certification = au.Certification
							AND dt.ICSDate = au.ICSDate
				) AS tbl_au ON 1=1
					AND afl.`FarmerID` = tbl_au.FarmerID
					AND afl.`GardenNr` = tbl_au.GardenNr
					AND afl.`SurveyNr` = tbl_au.SurveyNr
					AND hold.CertProgID = tbl_au.Certification
			WHERE
				ims.`StatusCode` = 'active'
				AND ims.`IMSID` = ?
				AND (
					DATE(tbl_au.`DateUpdated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
					OR
					DATE(tbl_au.`DateCreated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
				)
				$filterAchieve
				";
		$query           = $this->db->query($sql, array($IMSID, $dataIMS['CertEventDate'], $dataIMS['CertEventDate']));
		$dataCapAuditLog = $query->row_array();

		//rowLabel
		if ($opsiAchieve == "FA") {
			$rowLabel = lang("Achievement per FA");
		} else {
			$rowLabel = lang("Achievement (Farmers not Mapped)");
		}

		$data['rowLabel']          = $rowLabel;
		$data['FarmerVisited']     = $dataCapaiFarmerVisited;
		$data['Farmer']            = $dataCapFarmer['BANYAK'];
		$data['PostHarvest']       = $dataCapPh['BANYAK'];
		$data['PPI']               = $dataCapPpi['BANYAK'];
		$data['Garden']            = $dataCapGarden['BANYAK'];
		$data['GardenWithPolygon'] = $dataCapGarden['GardenPolygonNya'];
		$data['Certification']     = $dataCapCertification['BANYAK'];
		$data['AuditLog']          = $dataCapAuditLog['BANYAK'];

		return $data;
	}

	public function ImsEventDetailSummaryKpi($IMSID)
	{
		$dataList = array();
		$data     = array();

		$sql = "SELECT
				a.CertEventDate
			FROM
				ktv_ims a
			WHERE
				a.`StatusCode` = 'active'
				AND a.`IMSID` = ?
			LIMIT 1";
		$query   = $this->db->query($sql, array($IMSID));
		$dataIMS = $query->row_array();

		$data        = $this->ImsEventDetailSummaryKpi_Target($IMSID, $dataIMS);
		$dataList[0] = $data;

		$data        = $this->ImsEventDetailSummaryKpi_Achieve($IMSID, $dataIMS, 'FA');
		$dataList[1] = $data;

		$data        = $this->ImsEventDetailSummaryKpi_Achieve($IMSID, $dataIMS, 'NotMapped');
		$dataList[2] = $data;

		//Overall Achievement
		$dataList[3]['rowLabel']          = lang('Overall Achievement');
		$dataList[3]['FarmerVisited']     = $dataList[1]['FarmerVisited'] + $dataList[2]['FarmerVisited'];
		$dataList[3]['Farmer']            = $dataList[1]['Farmer'] + $dataList[2]['Farmer'];
		$dataList[3]['PostHarvest']       = $dataList[1]['PostHarvest'] + $dataList[2]['PostHarvest'];
		$dataList[3]['PPI']               = $dataList[1]['PPI'] + $dataList[2]['PPI'];
		$dataList[3]['Garden']            = $dataList[1]['Garden'] + $dataList[2]['Garden'];
		$dataList[3]['GardenWithPolygon'] = $dataList[1]['GardenWithPolygon'] + $dataList[2]['GardenWithPolygon'];
		$dataList[3]['Certification']     = $dataList[1]['Certification'] + $dataList[2]['Certification'];
		$dataList[3]['AuditLog']          = $dataList[1]['AuditLog'] + $dataList[2]['AuditLog'];

		$return['data'] = $dataList;
		return $return;
	}

	public function getImsSummaryKpiWeekly($IMSID, $CertEventDate)
	{
		//get tanggal awal
		$sql = "SELECT
				DATE_FORMAT( a.`CertEventDate` - INTERVAL 6 MONTH, '%Y-%m-%d' ) AS tglAwal
			FROM
				ktv_ims a
			WHERE
				a.`IMSID` = ?
			LIMIT 1";
		$query = $this->db->query($sql, array($IMSID));
		$data  = $query->row_array();

		//Hardcode ditampilkan 24 Minggu sejak tanggal awal dimulai pengambilan data (Begin)
		$dataListWeekly = array();
		$tglAwal        = $data['tglAwal'];

		for ($i = 0; $i < 24; $i++) {
			$dataListWeekly[$i]['start'] = $tglAwal . ' 00:00:00';

			//add 7 hari
			$prosesTgl         = strtotime($tglAwal);
			$prosesTgl         = strtotime("+7 day", $prosesTgl);
			$tglAkhir          = date('Y-m-d', $prosesTgl);
			$tglAkhirMinusSatu = strtotime("-1 day", $prosesTgl);
			$tglAkhirMinusSatu = date('Y-m-d', $tglAkhirMinusSatu);

			//dimasukkan di -1 days
			$dataListWeekly[$i]['end'] = $tglAkhirMinusSatu . ' 23:59:59';

			$tglAwal = $tglAkhir;
		}
		//Hardcode ditampilkan 24 Minggu sejak tanggal awal dimulai pengambilan data (End)

		//Query Data (Begin)
		for ($i = 0; $i < count($dataListWeekly); $i++) {
			//Capaian Farmer Visited (Farmer, Garden Status, dan Survey Garden)
			$sql = "SELECT
					COUNT(tbl_grup.FarmerID) AS FarmerID
				FROM
				(
					SELECT
						a.`FarmerID`
						, GREATEST(
							IFNULL(a.`DateUpdated`,'0000-00-00'),
							IFNULL(gar.`DateCreated`,'0000-00-00'),
							IFNULL(gar.`DateUpdated`,'0000-00-00'),
							IFNULL(gstat.`DateCreated`,'0000-00-00'),
							IFNULL(gstat.`DateUpdated`,'0000-00-00')
						) AS MaxTglCollection
					FROM
						ktv_certification_pre_afl afl
						LEFT JOIN ktv_certification_pre_afl_garden aflg ON 1=1
							AND afl.`FarmerID` = aflg.`FarmerID`
						INNER JOIN ktv_members a ON afl.`FarmerID` = a.`FarmerID`

						LEFT JOIN ktv_members_garden gar ON 1=1
							AND a.`FarmerID` = gar.`FarmerID`
							AND aflg.`GardenNr` = gar.`GardenNr`
							AND aflg.`SurveyNr` = gar.`SurveyNr`

						LEFT JOIN ktv_members_garden_status gstat ON 1=1
							AND a.`FarmerID` = gstat.`FarmerID`
							AND aflg.`GardenNr` = gstat.`GardenNr`
					WHERE
						a.`StatusCode` = 'active'
						AND afl.StatusAudit = '1'
						AND afl.`IMSID` = ?
						AND (
							(DATE(a.`DateUpdated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y-%m-%d' )) #Farmer
							OR
							(
								DATE(gar.`DateUpdated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y-%m-%d' )
								OR
								DATE(gar.`DateCreated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y-%m-%d' )
							) # Garden Survey
							OR
							(
								(
									DATE(gstat.`DateUpdated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y-%m-%d' )
									AND
									gstat.`LastModifiedBy` != '1' #Exclude Admin
								)
								OR
								(
									DATE(gstat.`DateCreated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y-%m-%d' )
									AND
									gstat.`CreatedBy` != '1' #Exclude Admin
								)
							) # Garden Status
						)
					GROUP BY a.`FarmerID`
				) AS tbl_grup
				WHERE
					tbl_grup.MaxTglCollection BETWEEN ? AND ?";
			$query = $this->db->query($sql, array(
				$IMSID,
				$CertEventDate, $CertEventDate, $CertEventDate, $CertEventDate, $CertEventDate,
				$dataListWeekly[$i]['start'], $dataListWeekly[$i]['end'],
			)
			);
			$dataCapFarmerVisited                = $query->row_array();
			$dataListWeekly[$i]['FarmerVisited'] = $dataCapFarmerVisited['FarmerID'];

			//Farmer
			$sql = "SELECT
					COUNT(far.FarmerID) AS FarmerID
				FROM
					`ktv_certification_pre_afl` afl
					LEFT JOIN ktv_members far ON 1=1
						AND far.`StatusCode` = 'active'
						AND afl.`FarmerID` = far.`FarmerID`
						AND far.`DateUpdated` BETWEEN ? AND ?
				WHERE
					afl.IMSID = ?";
			$query                        = $this->db->query($sql, array($dataListWeekly[$i]['start'], $dataListWeekly[$i]['end'], $IMSID));
			$dataCapFarmer                = $query->row_array();
			$dataListWeekly[$i]['Farmer'] = $dataCapFarmer['FarmerID'];

			//Cocoa Farm
			$sql = "SELECT
					COUNT(gar.`FarmerID`) AS BANYAK
					, COUNT(gpoly.FarmerID) AS GardenPolygonNya
				FROM
					ktv_certification_pre_afl_garden a
					LEFT JOIN ktv_members_garden gar ON 1=1
						AND a.`FarmerID` = gar.`FarmerID`
						AND a.`SurveyNr` = gar.`SurveyNr`
						AND a.`GardenNr` = gar.`GardenNr`
					LEFT JOIN (
						SELECT
							a.`FarmerID`
							, a.`GardenNr`
						FROM
							ktv_members_garden_area a
						WHERE
							a.`Status` != 'nullified'
							AND a.`FarmerID` != '0'
							AND a.`GardenNr` != '0'
						GROUP BY a.`FarmerID`, a.`GardenNr`
					) AS gpoly ON 1=1
						AND a.`FarmerID` = gpoly.FarmerID
						AND a.`GardenNr` = gpoly.GardenNr
				WHERE
					a.`IMSID` = ?
					AND (
						(gar.`DateUpdated` >= ? AND gar.`DateUpdated` <= ?)
						OR
						(gar.`DateCreated` >= ? AND gar.`DateCreated` <= ?)
					)";
			$query                                   = $this->db->query($sql, array($IMSID, $dataListWeekly[$i]['start'], $dataListWeekly[$i]['end'], $dataListWeekly[$i]['start'], $dataListWeekly[$i]['end']));
			$dataCapGarden                           = $query->row_array();
			$dataListWeekly[$i]['Garden']            = $dataCapGarden['BANYAK'];
			$dataListWeekly[$i]['GardenWithPolygon'] = $dataCapGarden['GardenPolygonNya'];

			//Post Harvest
			$sql = "SELECT
					COUNT(ph.`FarmerID`) AS BANYAK
				FROM
					(
						SELECT
							gar.`FarmerID`
							, MAX(gar.`SurveyNr`) AS SurveyNr
						FROM
							ktv_certification_pre_afl_garden a
							LEFT JOIN ktv_members_garden gar ON 1=1
								AND a.`FarmerID` = gar.`FarmerID`
								AND a.`SurveyNr` = gar.`SurveyNr`
								AND a.`GardenNr` = gar.`GardenNr`
						WHERE
							a.`IMSID` = ?
							AND gar.`StatusCode` = 'active'
							AND (
								DATE(gar.`DateUpdated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
								OR
								DATE(gar.`DateCreated`) >= DATE_FORMAT( ? - INTERVAL 6 MONTH, '%Y/%m/%d' )
							)
						GROUP BY gar.`FarmerID`
					) AS tbl_garden
					LEFT JOIN ktv_members_post_harvest ph ON 1=1
						AND tbl_garden.FarmerID = ph.`FarmerID`
						AND tbl_garden.SurveyNr = ph.`SurveyNr`
				WHERE
					ph.`StatusCode` = 'active'
					AND (
						(ph.`DateUpdated` >= ? AND ph.`DateUpdated` <= ?)
						OR
						(ph.`DateCreated` >= ? AND ph.`DateCreated` <= ?)
					)";
			$query                             = $this->db->query($sql, array($IMSID, $CertEventDate, $CertEventDate, $dataListWeekly[$i]['start'], $dataListWeekly[$i]['end'], $dataListWeekly[$i]['start'], $dataListWeekly[$i]['end']));
			$dataCapPostHarvest                = $query->row_array();
			$dataListWeekly[$i]['PostHarvest'] = $dataCapPostHarvest['BANYAK'];

			//Certification
			$sql = "SELECT
				COUNT(afl.`FarmerID`) AS BANYAK
			FROM
				ktv_certification_pre_afl_garden afl
				LEFT JOIN ktv_ims ims ON afl.`IMSID` = ims.`IMSID`
				LEFT JOIN ktv_certification_holders hold ON ims.CertHolderID = hold.CertHolderID

				LEFT JOIN ktv_certification cert ON 1=1
					AND afl.`FarmerID` = cert.`FarmerID`
					AND afl.`GardenNr` = cert.`GardenNr`
					AND afl.`SurveyNr` = cert.`SurveyNr`
					AND hold.`CertProgID` = cert.`Certification`
			WHERE
				ims.`StatusCode` = 'active'
				AND ims.`IMSID` = ?
				AND (
					(cert.`DateUpdated` >= ? AND cert.`DateUpdated` <= ?)
					OR
					(cert.`DateCreated` >= ? AND cert.`DateCreated` <= ?)
				)";
			$query                               = $this->db->query($sql, array($IMSID, $dataListWeekly[$i]['start'], $dataListWeekly[$i]['end'], $dataListWeekly[$i]['start'], $dataListWeekly[$i]['end']));
			$dataCapCertification                = $query->row_array();
			$dataListWeekly[$i]['Certification'] = $dataCapCertification['BANYAK'];

			//Audit Log
			$sql = "SELECT
				COUNT(afl.`FarmerID`) AS BANYAK
			FROM
				ktv_certification_pre_afl_garden afl
				LEFT JOIN ktv_ims ims ON afl.`IMSID` = ims.`IMSID`
				LEFT JOIN ktv_certification_holders hold ON ims.CertHolderID = hold.CertHolderID

				LEFT JOIN (
					SELECT
						au.`FarmerID`
						, au.`SurveyNr`
						, au.`GardenNr`
						, au.Certification
						, au.`ICSDate`
						, au.`DateCreated`
						, au.`DateUpdated`
					FROM
						(SELECT
							FarmerID,
							GardenNr,
							SurveyNr,
							Certification,
							MAX(ICSDate) ICSDate
						FROM
							ktv_certification_audit_log
						WHERE Certification != 0
							AND GardenNr != 0
						GROUP BY FarmerID,
							GardenNr,
							SurveyNr,
							Certification) dt
						INNER JOIN ktv_certification_audit_log au
							ON dt.FarmerID = au.FarmerID
							AND dt.GardenNr = au.GardenNr
							AND dt.SurveyNr = au.SurveyNr
							AND dt.Certification = au.Certification
							AND dt.ICSDate = au.ICSDate
				) AS tbl_au ON 1=1
					AND afl.`FarmerID` = tbl_au.FarmerID
					AND afl.`GardenNr` = tbl_au.GardenNr
					AND afl.`SurveyNr` = tbl_au.SurveyNr
					AND hold.CertProgID = tbl_au.Certification
			WHERE
				ims.`StatusCode` = 'active'
				AND ims.`IMSID` = ?
				AND (
					(tbl_au.`DateUpdated` >= ? AND tbl_au.`DateUpdated` <= ?)
					OR
					(tbl_au.`DateCreated` >= ? AND tbl_au.`DateCreated` <= ?)
				)";
			$query                          = $this->db->query($sql, array($IMSID, $dataListWeekly[$i]['start'], $dataListWeekly[$i]['end'], $dataListWeekly[$i]['start'], $dataListWeekly[$i]['end']));
			$dataCapAuditLog                = $query->row_array();
			$dataListWeekly[$i]['AuditLog'] = $dataCapAuditLog['BANYAK'];

			//PPI
			$sql = "SELECT
				COUNT(tbl_grup.FarmerID) AS BANYAK
			FROM
			(
				SELECT
					afl.`FarmerID`
				FROM
					ktv_certification_pre_afl afl
					INNER JOIN ktv_members a ON afl.`FarmerID` = a.`FarmerID`

					LEFT JOIN ktv_ppiscore2012 ppi ON afl.`FarmerID` = ppi.`FarmerID`
				WHERE
					afl.`IMSID` = ?
					AND afl.`StatusCode` = 'active'
					AND afl.StatusAudit = '1'
					AND a.`StatusCode` = 'active'
					AND ppi.`StatusCode` = 'active'
					AND (
						(ppi.`DateUpdated` >= ? AND ppi.`DateUpdated` <= ?)
						OR
						(ppi.`DateCreated` >= ? AND ppi.`DateCreated` <= ?)
					)
				GROUP BY afl.`FarmerID`
			) AS tbl_grup";
			$query                     = $this->db->query($sql, array($IMSID, $dataListWeekly[$i]['start'], $dataListWeekly[$i]['end'], $dataListWeekly[$i]['start'], $dataListWeekly[$i]['end']));
			$dataCapPpi                = $query->row_array();
			$dataListWeekly[$i]['PPI'] = $dataCapPpi['BANYAK'];

			//convert tgl
			$dataListWeekly[$i]['tglMulai']   = substr($dataListWeekly[$i]['start'], 0, 10);
			$dataListWeekly[$i]['tglSelesai'] = substr($dataListWeekly[$i]['end'], 0, 10);
		}
		//Query Data (End)

		return $dataListWeekly;
	}

	public function ImsEventDetailSummaryFaTable($IMSID)
	{
		$sql = "SELECT
				a.`UserID` AS UserID,
				a.`FieldAgentName` AS FaLabel,
				a.`FarmerVisited`,
				a.`FarmerTarget`,
				a.`FarmerUpdated` AS Farmer,
				a.`FarmerWithPhoto`,
				a.`FarmerFamilyLabour`,
				a.`CocoaFarm` AS Garden,
				a.`CocoaFarmWithPolygon` AS GardenWithPolygon,
				a.`PostHarvest`,
				a.`Certification`,
				a.`AuditLog`,
				a.`PPI`
			FROM
				ktv_ims_summary a
			WHERE
				a.`IMSID` = ?
			ORDER BY a.`FieldAgentName` ASC";
		$query = $this->db->query($sql, array($IMSID));
		return $query->result_array();
	}

	public function ImsEventDetailSummaryFa($IMSID)
	{
		$this->db->trans_begin();

		//hapus dl data sebelumnya
		$sql   = "DELETE FROM ktv_ims_summary WHERE IMSID = ?";
		$query = $this->db->query($sql, array($IMSID));

		$sql = "SELECT
				a.CertEventDate
				, a.IMSID
			FROM
				ktv_ims a
			WHERE
				a.`StatusCode` = 'active'
				AND a.`IMSID` = ?
			LIMIT 1";
		$query   = $this->db->query($sql, array($IMSID));
		$dataIMS = $query->row_array();

		$sql = "SELECT
				IFNULL(tar.PICUserID,0) AS UserID
				, IFNULL(us.`UserRealName`, '" . lang('UNKNOWN') . "') AS FaLabel
				, SUM(IF(tbl_fam_visit.FarmerID IS NULL,0,1)) AS FarmerVisited
				, IFNULL(COUNT(tar.`FarmerID`),0) AS FarmerTarget
				, IFNULL(COUNT(far.`FarmerID`),0) AS Farmer
				, GROUP_CONCAT(tar.FarmerID SEPARATOR ',') AS FarmerTargetGroupCon
				, IFNULL(SUM(tbl_fam.jumFam),0) AS FarmerFamilyLabour
				, IFNULL(SUM(capfa_garden.GardenNya),0) AS Garden
				, IFNULL(SUM(capfa_garden.GardenPolygonNya),0) AS GardenWithPolygon
				, IFNULL(COUNT(capfa_ph.FarmerID),0) AS PostHarvest
				, IFNULL(COUNT(capfa_ppi.FarmerID),0) AS PPI
				, IFNULL(SUM(capfa_cert.certNya),0) AS Certification
				, IFNULL(SUM(capfa_audit.AuditNya),0) AS AuditLog
			FROM
				`ktv_certification_pre_afl_target` tar
				LEFT JOIN sys_user us ON tar.`PICUserID` = us.`UserId`

				#Farmer Visited
				LEFT JOIN (
					SELECT
						a.`FarmerID`
					FROM
						ktv_certification_pre_afl afl
						LEFT JOIN ktv_certification_pre_afl_garden aflg ON 1=1
							AND afl.`FarmerID` = aflg.`FarmerID`
						INNER JOIN ktv_members a ON afl.`FarmerID` = a.`FarmerID`

						LEFT JOIN ktv_members_garden gar ON 1=1
							AND a.`FarmerID` = gar.`FarmerID`
							AND aflg.`GardenNr` = gar.`GardenNr`
							AND aflg.`SurveyNr` = gar.`SurveyNr`

						LEFT JOIN ktv_members_garden_status gstat ON 1=1
							AND a.`FarmerID` = gstat.`FarmerID`
							AND aflg.`GardenNr` = gstat.`GardenNr`
					WHERE
						a.`StatusCode` = 'active'
						AND afl.StatusAudit = '1'
						AND afl.`IMSID` = {$dataIMS['IMSID']}
						AND (
							(DATE(a.`DateUpdated`) >= DATE_FORMAT( '{$dataIMS['CertEventDate']}' - INTERVAL 6 MONTH, '%Y/%m/%d' )) #Farmer
							OR
							(
								DATE(gar.`DateUpdated`) >= DATE_FORMAT( '{$dataIMS['CertEventDate']}' - INTERVAL 6 MONTH, '%Y/%m/%d' )
								OR
								DATE(gar.`DateCreated`) >= DATE_FORMAT( '{$dataIMS['CertEventDate']}' - INTERVAL 6 MONTH, '%Y/%m/%d' )
							) # Garden Survey
							OR
							(
								(
									DATE(gstat.`DateUpdated`) >= DATE_FORMAT( '{$dataIMS['CertEventDate']}' - INTERVAL 6 MONTH, '%Y/%m/%d' )
									AND
									gstat.`LastModifiedBy` != '1' #Exclude Admin
								)
								OR
								(
									DATE(gstat.`DateCreated`) >= DATE_FORMAT( '{$dataIMS['CertEventDate']}' - INTERVAL 6 MONTH, '%Y/%m/%d' )
									AND
									gstat.`CreatedBy` != '1' #Exclude Admin
								)
							) # Garden Status
						)
					GROUP BY a.`FarmerID`
				) AS tbl_fam_visit ON 1=1
					AND tar.FarmerID = tbl_fam_visit.FarmerID

				#Farmer
				LEFT JOIN ktv_members far ON 1=1
					AND far.`StatusCode` = 'active'
					AND tar.`FarmerID` = far.`FarmerID`
					AND DATE(far.`DateUpdated`) >= DATE_FORMAT( '{$dataIMS['CertEventDate']}' - INTERVAL 6 MONTH, '%Y/%m/%d' )

				#Family
				LEFT JOIN (
					SELECT
						fam.`FarmerID`
						, COUNT(fam.`FamilyID`) AS jumFam
					FROM
						ktv_family fam
					WHERE
						fam.`FamilyStatus` != 'inactive'
					GROUP BY fam.`FarmerID`
				) AS tbl_fam ON 1=1
					AND tar.`FarmerID` = tbl_fam.FarmerID

				#Garden
				LEFT JOIN (
					SELECT
						a.`IMSID`
						, a.`FarmerID`
						, COUNT(gar.`FarmerID`) AS GardenNya
						, COUNT(gpoly.FarmerID) AS GardenPolygonNya
					FROM
						ktv_certification_pre_afl_garden a
						LEFT JOIN ktv_members_garden gar ON 1=1
							AND a.`FarmerID` = gar.`FarmerID`
							AND a.`SurveyNr` = gar.`SurveyNr`
							AND a.`GardenNr` = gar.`GardenNr`
							AND (
								DATE(gar.`DateUpdated`) >= DATE_FORMAT( '{$dataIMS['CertEventDate']}' - INTERVAL 6 MONTH, '%Y/%m/%d' )
								OR
								DATE(gar.`DateCreated`) >= DATE_FORMAT( '{$dataIMS['CertEventDate']}' - INTERVAL 6 MONTH, '%Y/%m/%d' )
							)

						LEFT JOIN (
							SELECT
								a.`FarmerID`
								, a.`GardenNr`
							FROM
								ktv_members_garden_area a
							WHERE
								a.`Status` != 'nullified'
								AND a.`FarmerID` != '0'
								AND a.`GardenNr` != '0'
							GROUP BY a.`FarmerID`, a.`GardenNr`
						) AS gpoly ON 1=1
							AND a.`FarmerID` = gpoly.FarmerID
							AND a.`GardenNr` = gpoly.GardenNr

					WHERE
						a.`IMSID` = '{$dataIMS['IMSID']}'

					GROUP BY a.`FarmerID`
				) AS capfa_garden ON 1=1
					AND tar.`IMSID` = capfa_garden.IMSID
					AND tar.`FarmerID` = capfa_garden.FarmerID

				#PostHarvest
				LEFT JOIN (
					SELECT
						tbl_garden.IMSID
						, ph.`FarmerID`
					FROM
						(
							SELECT
								a.`IMSID`
								, gar.`FarmerID`
								, MAX(gar.`SurveyNr`) AS SurveyNr
							FROM
								ktv_certification_pre_afl_garden a
								LEFT JOIN ktv_members_garden gar ON 1=1
									AND a.`FarmerID` = gar.`FarmerID`
									AND a.`SurveyNr` = gar.`SurveyNr`
									AND a.`GardenNr` = gar.`GardenNr`
							WHERE
								a.`IMSID` = '{$dataIMS['IMSID']}'
								AND gar.`StatusCode` = 'active'
								AND (
									DATE(gar.`DateUpdated`) >= DATE_FORMAT( '{$dataIMS['CertEventDate']}' - INTERVAL 6 MONTH, '%Y/%m/%d' )
									OR
									DATE(gar.`DateCreated`) >= DATE_FORMAT( '{$dataIMS['CertEventDate']}' - INTERVAL 6 MONTH, '%Y/%m/%d' )
								)
							GROUP BY gar.`FarmerID`
						) AS tbl_garden
						LEFT JOIN ktv_members_post_harvest ph ON 1=1
							AND tbl_garden.FarmerID = ph.`FarmerID`
							AND tbl_garden.SurveyNr = ph.`SurveyNr`
					WHERE
						ph.`StatusCode` = 'active'
						AND (
							DATE(ph.`DateUpdated`) >= DATE_FORMAT( '{$dataIMS['CertEventDate']}' - INTERVAL 6 MONTH, '%Y/%m/%d' )
							OR
							DATE(ph.`DateCreated`) >= DATE_FORMAT( '{$dataIMS['CertEventDate']}' - INTERVAL 6 MONTH, '%Y/%m/%d' )
						)
				) AS capfa_ph ON 1=1
					AND tar.`IMSID` = capfa_ph.IMSID
					AND tar.`FarmerID` = capfa_ph.FarmerID

				#PPI
				LEFT JOIN (
					SELECT
						tbl_grup.IMSID
						, tbl_grup.FarmerID
					FROM
					(
						SELECT
							afl.`FarmerID`
							, afl.`IMSID`
						FROM
							ktv_certification_pre_afl afl
							INNER JOIN ktv_members a ON afl.`FarmerID` = a.`FarmerID`

							LEFT JOIN ktv_ppiscore2012 ppi ON afl.`FarmerID` = ppi.`FarmerID`
						WHERE
							afl.`IMSID` = '{$dataIMS['IMSID']}'
							AND afl.`StatusCode` = 'active'
							AND afl.StatusAudit = '1'
							AND a.`StatusCode` = 'active'
							AND ppi.`StatusCode` = 'active'
							AND (
								DATE(ppi.`DateUpdated`) >= DATE_FORMAT( '{$dataIMS['CertEventDate']}' - INTERVAL 6 MONTH, '%Y/%m/%d' )
								OR
								DATE(ppi.`DateCreated`) >= DATE_FORMAT( '{$dataIMS['CertEventDate']}' - INTERVAL 6 MONTH, '%Y/%m/%d' )
							)
						GROUP BY afl.`FarmerID`
					) AS tbl_grup
				) AS capfa_ppi ON 1=1
					AND tar.`IMSID` = capfa_ppi.IMSID
					AND tar.`FarmerID` = capfa_ppi.FarmerID

				#Certification
				LEFT JOIN (
					SELECT
						ims.`IMSID`
						, cert.`FarmerID`
						, COUNT(cert.`FarmerID`) AS certNya
					FROM
						ktv_certification_pre_afl_garden afl
						LEFT JOIN ktv_ims ims ON afl.`IMSID` = ims.`IMSID`
						LEFT JOIN ktv_certification_holders hold ON ims.CertHolderID = hold.CertHolderID

						LEFT JOIN ktv_certification cert ON 1=1
							AND afl.`FarmerID` = cert.`FarmerID`
							AND afl.`GardenNr` = cert.`GardenNr`
							AND afl.`SurveyNr` = cert.`SurveyNr`
							AND hold.`CertProgID` = cert.`Certification`
					WHERE
						ims.`StatusCode` = 'active'
						AND ims.`IMSID` = '{$dataIMS['IMSID']}'
						AND (
							DATE(cert.`DateUpdated`) >= DATE_FORMAT( '{$dataIMS['CertEventDate']}' - INTERVAL 6 MONTH, '%Y/%m/%d' )
							OR
							DATE(cert.`DateCreated`) >= DATE_FORMAT( '{$dataIMS['CertEventDate']}' - INTERVAL 6 MONTH, '%Y/%m/%d' )
						)
					GROUP BY cert.`FarmerID`
				) AS capfa_cert ON 1=1
					AND tar.`IMSID` = capfa_cert.IMSID
					AND tar.`FarmerID` = capfa_cert.FarmerID

				#AuditLog
				LEFT JOIN (
					SELECT
						ims.`IMSID`
						, tbl_au.FarmerID
						, COUNT(tbl_au.FarmerID) AS AuditNya
					FROM
						ktv_certification_pre_afl_garden afl
						LEFT JOIN ktv_ims ims ON afl.`IMSID` = ims.`IMSID`
						LEFT JOIN ktv_certification_holders hold ON ims.CertHolderID = hold.CertHolderID

						LEFT JOIN (
							SELECT
								au.`FarmerID`
								, au.`SurveyNr`
								, au.`GardenNr`
								, au.Certification
								, au.`ICSDate`
								, au.`DateCreated`
								, au.`DateUpdated`
							FROM
								(SELECT
									FarmerID,
									GardenNr,
									SurveyNr,
									Certification,
									MAX(ICSDate) ICSDate
								FROM
									ktv_certification_audit_log
								WHERE Certification != 0
									AND GardenNr != 0
								GROUP BY FarmerID,
									GardenNr,
									SurveyNr,
									Certification) dt
								INNER JOIN ktv_certification_audit_log au
									ON dt.FarmerID = au.FarmerID
									AND dt.GardenNr = au.GardenNr
									AND dt.SurveyNr = au.SurveyNr
									AND dt.Certification = au.Certification
									AND dt.ICSDate = au.ICSDate
						) AS tbl_au ON 1=1
							AND afl.`FarmerID` = tbl_au.FarmerID
							AND afl.`GardenNr` = tbl_au.GardenNr
							AND afl.`SurveyNr` = tbl_au.SurveyNr
							AND hold.CertProgID = tbl_au.Certification
					WHERE
						ims.`StatusCode` = 'active'
						AND ims.`IMSID` = '{$dataIMS['IMSID']}'
						AND (
							DATE(tbl_au.`DateUpdated`) >= DATE_FORMAT( '{$dataIMS['CertEventDate']}' - INTERVAL 6 MONTH, '%Y/%m/%d' )
							OR
							DATE(tbl_au.`DateCreated`) >= DATE_FORMAT( '{$dataIMS['CertEventDate']}' - INTERVAL 6 MONTH, '%Y/%m/%d' )
						)
					GROUP BY tbl_au.FarmerID
				) AS capfa_audit ON 1=1
					AND tar.`IMSID` = capfa_audit.IMSID
					AND tar.`FarmerID` = capfa_audit.FarmerID

			WHERE
				tar.`StatusCode` = 'active'
				AND tar.`IMSID` = '{$dataIMS['IMSID']}'
				#AND tar.`PICUserID` IS NOT NULL
			GROUP BY tar.`PICUserID`
			ORDER BY us.`UserRealName` ASC";
		$query = $this->db->query($sql);
		$data  = $query->result_array();

		//Jika ada NOT SPECIFIED shift kan element itu ke element terakhir
//        if ($data[0]['UserID'] == "0") {
//            $itemTemp = $data[0];
//            unset($data[0]);
//            array_push($data, $itemTemp);
//        }

		//reorder array
		$data = array_values($data);

		//Hitung Farmer dengan Foto (Begin)
		for ($i = 0; $i < count($data); $i++) {

			if ($data[$i]['FarmerTargetGroupCon'] != "") {
				$photoCount = 0;

				$arrFarmerID = explode(',', $data[$i]['FarmerTargetGroupCon']);
				for ($j = 0; $j < count($arrFarmerID); $j++) {
					$sql     = "SELECT Photo FROM ktv_members WHERE FarmerID = '{$arrFarmerID[$j]}' LIMIT 1";
					$query   = $this->db->query($sql);
					$dataCek = $query->row_array();

					if ($dataCek['Photo'] != "") {
						if (@file_exists('images/Photo/' . $dataCek['Photo'])) {
							$photoCount++;
						}
					}
				}

				$data[$i]['FarmerWithPhoto'] = $photoCount;
			} else {
				$data[$i]['FarmerWithPhoto'] = 0;
			}
		}
		//Hitung Farmer dengan Foto (End)

		for ($i = 0; $i < count($data); $i++) {
			$sql = "INSERT INTO `ktv_ims_summary` SET
					`IMSID` = ?,
					`UserID` = ?,
					`FieldAgentName` = ?,
					`FarmerVisited` = ?,
					`FarmerTarget` = ?,
					`FarmerUpdated` = ?,
					`FarmerWithPhoto` = ?,
					`FarmerFamilyLabour` = ?,
					`CocoaFarm` = ?,
					`CocoaFarmWithPolygon` = ?,
					`PostHarvest` = ?,
					`Certification` = ?,
					`AuditLog` = ?,
					`PPI` = ?,
					`DateUpdated` = NOW()";
			$p = array(
				$IMSID,
				$data[$i]['UserID'],
				$data[$i]['FaLabel'],
				$data[$i]['FarmerVisited'],
				$data[$i]['FarmerTarget'],
				$data[$i]['Farmer'],
				$data[$i]['FarmerWithPhoto'],
				$data[$i]['FarmerFamilyLabour'],
				$data[$i]['Garden'],
				$data[$i]['GardenWithPolygon'],
				$data[$i]['PostHarvest'],
				$data[$i]['Certification'],
				$data[$i]['AuditLog'],
				$data[$i]['PPI'],
			);
			$query = $this->db->query($sql, $p);
		}

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			$results['success'] = false;
			$results['message'] = "Failed to update summary";
		} else {
			$this->db->trans_commit();
			$results['success'] = true;
			$results['message'] = date('Y-m-d H:i:s');
		}

		return $results;
	}

	public function getDataPetaniNotComply($IMSID)
	{
		$sql = "SELECT
				far.`FarmerID` AS 'ID Petani',
				far.ExtFarmerID AS 'ID Petani Eksternal',
				far.`FarmerName` AS 'Nama Petani',
				far.`DateCollection` AS 'Tgl Interview',
				far.FarmerGroupID AS 'ID Kelompok Tani',
				b.`GroupName` AS 'Kelompok Tani',
				c.`Province` AS 'Propinsi',
				d.`District` AS 'Kabupaten',
				e.`SubDistrict` AS 'Kecamatan',
				f.`Village` AS 'Desa',
				IF(
					far.StatusFarmer = 1,
					'Yes',
					IF(far.StatusFarmer = 2, 'No', '')
				) AS 'Status aktif',
				IF(
					ReasonStatusFarmer = 1,
					'Meninggal',
					IF(
					  ReasonStatusFarmer = 2,
					  'Pindah',
					  IF(
						ReasonStatusFarmer = 3,
						'Berhenti bertani',
						''
					  )
					)
				) AS 'Alasan jika petani sudah tidak aktif',
				far.`DateCreated`,
				g.`UserRealName` AS CreatedBy,
				far.`DateUpdated`,
				h.`UserRealName` AS LastModifiedBy
			FROM
				ktv_certification_pre_afl afl
				INNER JOIN ktv_members far ON afl.`FarmerID` = far.`FarmerID`

				LEFT JOIN ktv_farmer_group b ON b.`FarmerGroupID` = far.`FarmerGroupID`
				LEFT JOIN ktv_village f ON f.`VillageID` = far.`VillageID`
				LEFT JOIN ktv_subdistrict e ON e.`SubDistrictID` = f.SubDistrictID
				LEFT JOIN ktv_district d ON d.`DistrictID` = e.DistrictID
				LEFT JOIN ktv_province c ON c.`ProvinceID` = d.ProvinceID
				LEFT JOIN sys_user g ON g.UserId = far.`CreatedBy`
				LEFT JOIN sys_user h ON h.UserId = far.`LastModifiedBy`
				LEFT JOIN ktv_bank i ON i.`BankID` = far.`BankID`
			WHERE
				afl.`IMSID` = ?
				AND afl.StatusAudit = '1'
				AND far.`StatusCode` = 'active'
				AND far.`StatusFarmer` = '2'
			ORDER BY afl.`FarmerID` ASC";
		$query = $this->db->query($sql, array($IMSID));
		return $query->result_array();
	}

	public function getDataGardenNotComply($IMSID)
	{
		$sql = "SELECT
				b.`FarmerID` AS 'ID Petani',
				b.ExtFarmerID AS 'ID Petani Eksternal',
				b.`FarmerName` AS 'Nama Petani',
				c.`GroupName` AS 'Kelompok Tani',
				d.`Province` AS 'Propinsi',
				e.`District` AS 'Kabupaten',
				f.`SubDistrict` AS 'Kecamatan',
				g.`Village` AS 'Desa',
				gstat.GardenNr,
				CASE
					WHEN gstat.GardenStatus='1' THEN 'Died (Dropped)'
					WHEN gstat.GardenStatus='2' THEN 'Moved / Left the area'
					WHEN gstat.GardenStatus='3' THEN 'Switched to other crop'
					WHEN gstat.GardenStatus='4' THEN 'Sold the land'
					WHEN gstat.GardenStatus='5' THEN 'Gave the land to family member'
					WHEN gstat.GardenStatus='6' THEN 'Force Majeure'
				END AS NotActiveReason,
				gstat.`DateCreated`,
				h.`UserRealName` AS CreatedBy,
				gstat.`DateUpdated`,
				i.`UserRealName` AS LastModifiedBy
			FROM
				ktv_certification_pre_afl afl
				INNER JOIN ktv_certification_pre_afl_garden aflg ON 1=1
					AND afl.`FarmerID` = aflg.`FarmerID`
					AND afl.IMSID = aflg.IMSID

				LEFT JOIN ktv_members_garden_status gstat ON 1=1
					AND aflg.`FarmerID` = gstat.`FarmerID`
					AND aflg.`GardenNr` = gstat.`GardenNr`

				LEFT JOIN ktv_members b ON b.`FarmerID` = aflg.`FarmerID`
				LEFT JOIN ktv_farmer_group c ON c.`FarmerGroupID` = b.`FarmerGroupID`
				LEFT JOIN ktv_village g ON g.`VillageID` = b.`VillageID`
				LEFT JOIN ktv_subdistrict f ON f.`SubDistrictID` = g.SubDistrictID
				LEFT JOIN ktv_district e ON e.`DistrictID` = f.DistrictID
				LEFT JOIN ktv_province d ON d.`ProvinceID` = e.ProvinceID
				LEFT JOIN sys_user h ON h.UserId = gstat.`CreatedBy`
				LEFT JOIN sys_user i ON i.UserId = gstat.`LastModifiedBy`

			WHERE
				afl.`IMSID` = ?
				AND afl.StatusAudit = '1'
				AND afl.`StatusCode` = 'active'
				AND gstat.`ActiveStatus` = '2'
			GROUP BY aflg.`FarmerID`, aflg.`GardenNr`
			";

		$query = $this->db->query($sql, array($IMSID));
		return $query->result_array();
	}

	public function getDataAuditLogNotComply($IMSID)
	{
		$sql = "SELECT
				afl.`FarmerID` AS 'ID Petani',
				b.ExtFarmerID AS 'ID Petani Eksternal',
				b.`FarmerName` AS 'Nama Petani',
				c.`CPGid` AS 'ID Kelompok',
				c.`GroupName` AS 'Kelompok Tani',
				d.`Province` AS 'Propinsi',
				e.`District` AS 'Kabupaten',
				f.`SubDistrict` AS 'Kecamatan',
				g.`Village` AS 'Desa',
				tbl_au.`GardenNr` AS 'Nr Kebun',
				tbl_au.`SurveyNr` AS 'Nr Survey',
				IF(
				`Certification` = 1,
				'UTZ',
				IF(
				  `Certification` = 2,
				  'Rainforest',
				  IF(
					`Certification` = 3,
					'Fairtrade',
					IF(`Certification` = 4, 'Organic', '')
				  )
				)
				) AS 'Program Sertifikasi',
				tbl_au.`ICSDate` AS 'Tgl Audit Internal',
				`DateRevisionAudit` AS 'Tgl Revisi Audit Internal',
				`CommentAudit` AS 'Komentar Audit',
				`RecommendationAudit` AS 'Rekomendasi Audit',
				IF(
				tbl_au.`StatusAudit` = 1,
				'Passed',
				IF(
				  tbl_au.`StatusAudit` = 2,
				  'Not Passed',
				  IF(
					tbl_au.`StatusAudit` = 3,
					'Passed with Requirement',
					''
				  )
				)
				) AS 'Status Audit Internal',
				j.`PersonNm` AS 'Nama Auditor',
				`InspectorSignature` AS 'Ttd Auditor',
				k.`PersonNm` AS 'Nama Komite Audit',
				`AuditCommiteeSignature` AS 'Ttd Komite Audit',
				l.`PersonNm` AS 'Nama IMS Manager',
				tbl_au.`IMSManagerSignature` AS 'Ttd IMS Manager',
				tbl_au.`FarmerSignature` AS 'Ttd Petani',
				IF(
				ParticipateChildEducation = 1,
				'Yes',
				IF(
				  ParticipateChildEducation = 2,
				  'No',
				  ''
				)
				) AS '(C) Apakah Anda berpartisipasi dalam usaha untuk memastikan agar semua anak usia sekolah mendapatkan akses pendidikan',
				IF(
				CutWageForDisciplinary = 1,
				'Yes',
				IF(
				  CutWageForDisciplinary = 2,
				  'No',
				  ''
				)
				) AS '(C) Apakah Anda mengalami pemotongan upah kerja untuk tujuan disipliner',
				IF(
				DoCutWageForWorker = 1,
				'Yes',
				IF(DoCutWageForWorker = 2, 'No', '')
				) AS '(C) Apakah Anda melakukan pemotongan upah pekerja anda dengan tujuan disipliner',
				IF(
				WagePaidByPerformance = 1,
				'Yes',
				IF(WagePaidByPerformance = 2, 'No', '')
				) AS '(C) Apakah upah Anda dibayarkan sesuai kinerja atau kesepakatan tanpa adanya diskiriminasi',
				IF(
				PayingWorkerWageByPerformance = 1,
				'Yes',
				IF(
				  PayingWorkerWageByPerformance = 2,
				  'No',
				  ''
				)
				) AS '(C) Apakah Anda membayar upah pekerja anda sesuai kinerja atau kesepakatan tanpa adanya diskiriminasi',
				IF(
				HandlingFirstAidInGarden = 1,
				'Yes',
				IF(
				  HandlingFirstAidInGarden = 2,
				  'No',
				  ''
				)
				) AS '(C) Anda memahami bagaimana penanganan pertolongan pertama pada kecelakaan di kebun',
				IF(
				FirstAidKitLocation = 1,
				'Yes',
				IF(FirstAidKitLocation = 2, 'No', '')
				) AS '(C) Apakah kotak pertolongan pertama (P3K) tersedia di pusat lokasi produk, pengolahan dan pemeliharaan',
				IF(
				WorkerNotHandlePesticide = 1,
				'Yes',
				IF(
				  WorkerNotHandlePesticide = 2,
				  'No',
				  ''
				)
				) AS '(C) Apakah Anda sudah memastikan para pengurus kelompok, anggota kelompok, dan anggota kelompok yang termasuk pekerja, yang berusia di bawah 1 tahun, atau hamil dan sedang menyusui tidak boleh menangani pestisida',
				IF(
				WorkerAccessSafeDrinkingWater = 1,
				'Yes',
				IF(
				  WorkerAccessSafeDrinkingWater = 2,
				  'No',
				  ''
				)
				) AS '(C) Apakah staf kelompok, anggota kelompok, dan anggota kelompok yang merupakan pekerja mempunyai akses terhadap air minum yang aman.',
				IF(
				BufferZoneGarden = 1,
				'Yes',
				IF(BufferZoneGarden = 2, 'No', '')
				) AS '(C) Di kebun ini terdapat sebuah zona penyangga berisi vegetasi asli setidaknya selebar 5 meter  dipelihara di sepanjang batas badan air musiman dan permanen untuk mengurangi erosi, membatasi pencemaran pestisida dan pupuk, dan melindungi habitat satwa liar. Di lahan yang luasnya kurang dari 2 Ha, terdapat zona penyangga  dengan lebar setidaknya 2 meter',
				IF(
				LandOpeningForest = 1,
				'Yes',
				IF(LandOpeningForest = 2, 'No', '')
				) AS '(C) Apakah lahan Anda dibuat dengan membuka hutan pada tahun 2008 atau sesudahnya',
				IF(
				LandOpeningForestCertificate = 1,
				'Yes',
				IF(
				  LandOpeningForestCertificate = 2,
				  'No',
				  ''
				)
				) AS '(C) Jika lahan Anda dibuat dengan membuka hutan, apakah Anda memiliki surat kepemilikan secara resmi dari pemerintah',
				IF(
				IdentifyProtectRareSpecies = 1,
				'Yes',
				IF(
				  IdentifyProtectRareSpecies = 2,
				  'No',
				  ''
				)
				) AS '(C) Apakah Anda melakukan identifikasi dan perlindungan terhadap spesies langka dan terancam punah di sekitar Anda',
				tbl_au.`DateCreated`,
				h.`UserRealName` AS CreatedBy,
				tbl_au.`DateUpdated`,
				i.`UserRealName` AS LastModifiedBy
			FROM
				ktv_certification_pre_afl afl
				INNER JOIN ktv_certification_pre_afl_garden aflg ON 1=1
					AND afl.`IMSID` = aflg.`IMSID`
					AND afl.`FarmerID` = aflg.`FarmerID`
				LEFT JOIN ktv_ims ims ON afl.`IMSID` = ims.`IMSID`
				LEFT JOIN ktv_certification_holders hold ON ims.CertHolderID = hold.CertHolderID

				INNER JOIN (
					SELECT
						au.*
					FROM
						(SELECT
							FarmerID,
							GardenNr,
							SurveyNr,
							Certification,
							MAX(ICSDate) ICSDate
						FROM
							ktv_certification_audit_log
						WHERE Certification != 0
							AND GardenNr != 0
						GROUP BY FarmerID,
							GardenNr,
							SurveyNr,
							Certification) dt
						INNER JOIN ktv_certification_audit_log au
							ON dt.FarmerID = au.FarmerID
							AND dt.GardenNr = au.GardenNr
							AND dt.SurveyNr = au.SurveyNr
							AND dt.Certification = au.Certification
							AND dt.ICSDate = au.ICSDate
				) AS tbl_au ON 1=1
					AND aflg.`FarmerID` = tbl_au.FarmerID
					AND aflg.`GardenNr` = tbl_au.GardenNr
					AND aflg.`SurveyNr` = tbl_au.SurveyNr
					AND hold.`CertProgID` = tbl_au.Certification

				LEFT JOIN ktv_members b ON b.`FarmerID` = tbl_au.`FarmerID`
				LEFT JOIN ktv_cpg c ON c.`CPGid` = b.`CPGid`
				LEFT JOIN ktv_village g ON g.`VillageID` = b.`VillageID`
				LEFT JOIN ktv_subdistrict f ON f.`SubDistrictID` = g.SubDistrictID
				LEFT JOIN ktv_district e ON e.`DistrictID` = f.DistrictID
				LEFT JOIN ktv_province d ON d.`ProvinceID` = e.ProvinceID
				LEFT JOIN sys_user h ON h.UserId = tbl_au.`CreatedBy`
				LEFT JOIN sys_user i ON i.UserId = tbl_au.`LastModifiedBy`
				LEFT JOIN ktv_persons j ON j.`PersonID` = tbl_au.`InspectorID`
				LEFT JOIN ktv_persons k ON k.`PersonID` = tbl_au.`AuditCommiteeID`
				LEFT JOIN ktv_persons l ON l.`PersonID` = tbl_au.`IMSManagerID`

			WHERE
				ims.`IMSID` = ?
				AND afl.StatusAudit = '1'
				AND b.StatusCode = 'active'
				AND tbl_au.StatusAudit = '2'
			ORDER BY afl.`FarmerID` ASC";
		$query = $this->db->query($sql, array($IMSID));
		return $query->result_array();
	}

	public function getIMSongoing()
	{
		$sql = "
SELECT
	ims.IMSID AS IMSID
	, prog.CertProgName AS ProgramName
	, kch.CertHolderID AS HolderID
FROM ktv_ims ims
LEFT JOIN ktv_certification_holders kch ON kch.CertHolderID = ims.CertHolderID
LEFT JOIN ktv_ref_certification_program prog ON kch.CertProgID = prog.CertProgID
WHERE 1 = 1
	AND ims.WithPolygon = 1
	AND ims.CertEventStatus IN (1,2)
		";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
		return false;
	}

	public function GetIMSKmlDetail($IMSID){
		$sql = "
SELECT
	ims.IMSID AS IMSID
	, prog.CertProgName AS ProgramName
	, kch.CertHolderID AS HolderID
FROM ktv_ims ims
LEFT JOIN ktv_certification_holders kch ON kch.CertHolderID = ims.CertHolderID
LEFT JOIN ktv_ref_certification_program prog ON kch.CertProgID = prog.CertProgID
WHERE 1 = 1
	AND ims.IMSID = ?
		";
		$query = $this->db->query($sql,array($IMSID));
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
		return false;
	}

	public function getAcqProForm($IMSID)
	{
		$sql = "SELECT
				a.`IMSID`
				, a.`CertEventName`
				, a.`Year`
				, part.`PartnerName` AS FirstBuyer
				, CONCAT('[',(
					CASE
						WHEN b.ObjType = 'farmer_group' THEN 'Farmer Group'
						WHEN b.ObjType = 'cooperative' THEN 'Cooperative'
                    	ELSE '-' 
					END
				),'] ',b.CertHolderOrgName) AS CertificateHolder
				, d.`CertProgName` AS ProgramName
				, cbody.`CertBodyName` AS CertificationBody
				, a.Location
				, a.TrainStatus
				, a.SocStart
				, a.SocEnd
				, a.TrainingStart
				, a.TrainingEnd
				, a.SigningLockSocSelBy
				, a.SigningLockSocSelRemark
				, a.SigningLockSocSelDatetime
				, a.SigningLockGapCocBy
				, a.SigningLockGapCocRemark
				, a.SigningLockGapCocDatetime
				, a.CertEventStatus
			FROM
				ktv_ims a
				LEFT JOIN ktv_first_buyer fb ON a.`FirstBuyerID` = fb.`FirstBuyerID`
				LEFT JOIN ktv_program_partner part ON fb.`FirstBuyerPartnerID` = part.`PartnerID`
				LEFT JOIN ktv_certification_holders b ON a.`CertHolderID` = b.`CertHolderID`
				LEFT JOIN ktv_ref_certification_program d ON b.`CertProgID` = d.`CertProgID`
				LEFT JOIN ktv_certification_body cbody ON a.`CertBodyID` = cbody.`CertBodyID`
			WHERE
				a.`IMSID` = ?
			LIMIT 1";
		$p = array(
			$IMSID,
		);
		$query       = $this->db->query($sql, $p);
		$data        = $query->row_array();
		$TrainStatus = $data['TrainStatus'];

		if (
			$data['SocStart'] != "" &&
			$data['SocStart'] != "0000-00-00" &&
			$data['SocEnd'] != "" &&
			$data['SocEnd'] != "0000-00-00"
		) {
			$data['SocSelPeriodLabel'] = $data['SocStart'] . '   ' . lang('to') . '   ' . $data['SocEnd'];
		} else {
			$data['SocSelPeriodLabel'] = '-';
		}

		if (
			$data['TrainingStart'] != "" &&
			$data['TrainingStart'] != "0000-00-00" &&
			$data['TrainingEnd'] != "" &&
			$data['TrainingEnd'] != "0000-00-00"
		) {
			$data['TrainingPeriodLabel'] = $data['TrainingStart'] . '   ' . lang('to') . '   ' . $data['TrainingEnd'];
		} else {
			$data['TrainingPeriodLabel'] = '-';
		}

		//prep variable
		$dataRow = array();
		foreach ($data as $key => $value) {
			$keyNew           = "Koltiva.view.IMS.WinImsAcqPro-Form-" . $key;
			$dataRow[$keyNew] = $value;
		}
		$dataRow['TrainStatus'] = $TrainStatus;
		$dataRow['SigningLockSocSelBy'] = $data['SigningLockSocSelBy'];
		$dataRow['SigningLockGapCocBy'] = $data['SigningLockGapCocBy'];
		$dataRow['CertEventStatus'] = $data['CertEventStatus'];

		$return['success'] = true;
		$return['data']    = $dataRow;
		return $return;
	}

	public function getGridsCoachingActivity($IMSID, $start = 0, $limit = 50, $sortingField, $sortingDir, $pSearch){
	  if ($sortingField == "") {
		$sortingField = 'MemberName';
	  }

	  if ($sortingDir == "") {
		$sortingDir = 'ASC';
	  }
	  $sqlWhereSimple = "";

	  if($pSearch['textSearch'] != ""){
		$sqlWhereSimple .= " AND (far.MemberName LIKE '%{$pSearch['textSearch']}%' OR ifc.FarmerID LIKE '%{$pSearch['textSearch']}%' OR ifc.UserName LIKE '%{$pSearch['textSearch']}%') ";
	  }

	  $sql = "SELECT
			ca.ActivityID,
			ca.CoachingID,
			ifc.FarmerID,
			far.MemberName FarmerName,
			ca.EventDate,
			ca.TimeStart,
			ca.TimeEnd,
			ca.DateCreated,
			p.PersonNm as CreatedBy,
			ifc.UserName
		FROM 
			`ktv_ims_farmer_coaching_activity` as ca
		LEFT JOIN 
			ktv_ims_farmer_coaching as ifc on ifc.CoachingID = ca.CoachingID
		LEFT JOIN 
			ktv_persons as p ON p.UserId = ca.CreatedBy
		LEFT JOIN 
			ktv_members as far on far.MemberID = ifc.FarmerID
		WHERE 
			ca.IMSID = ?
		AND ca.StatusCode = 'active'
			$sqlWhereSimple
		ORDER BY 
			$sortingField $sortingDir
		LIMIT ?,?
	  ";
	  $query = $this->db->query($sql, array($IMSID, (int) $start, (int) $limit));
	  $sql_total   = "SELECT FOUND_ROWS() AS total";
		$query_total = $this->db->query($sql_total);
		if ($query->num_rows() > 0) {
			$total = $query_total->row_array(0);
			return array(
				'data'  => $query->result_array(),
				'total' => $total['total'],
			);
		}
		return false;
	}
        
    public function getProgIDIMS($IMSID) {
        $sql = "SELECT
                    a.IMSID,
                    a.ProgID
                FROM ktv_ims a
                WHERE
                    1 = 1
                    AND a.IMSID = ?
                LIMIT 1";
        return $this->db->query($sql, array($IMSID))->row_array();
    }

	public function getExportCoachingActivitySql($IMSID, $pSearch, $sortingField, $sortingDir) {
        if ($sortingField == "")
            $sortingField = 'FarmerName';

        if ($sortingDir == "")
            $sortingDir = 'ASC';
        $sqlWhere = "";
        
        if ($pSearch['textSearch'] != "") {
            $sqlWhere .= " AND (far.FarmerName LIKE '%{$pSearch['textSearch']}%' OR far.FarmerID LIKE '%{$pSearch['textSearch']}%' OR ifc.UserName LIKE '%{$pSearch['textSearch']}%') ";
        }

		$sql = "SELECT
			fca.IMSID
			, CONCAT(vso.`Name`,' (',vso.ObjType,')') AS 'Certification Holder'
			, prv.Province
			, dis.District
			, fca.ActivityID
			, sub.SubDistrict
			, vil.VillageID
			, fca.CoachingID
			, far.MemberDisplayID 'Farmer ID'
			, far.MemberName 'Farmer Name'
			, fg.FarmerGroupID 'Farmer Group ID'
			, fg.GroupName 'Farmer Group Name'
			, vil.Village AS 'Adress'
			, IF(fca.CoachingRecipient = 1, 'Registered Farmer', IF(fca.CoachingRecipient = 2, 'Garden Operator', IF(fca.CoachingRecipient = 3, 'Household Member', '-'))) AS 'Training Receiver'
			, IF(fca.CoachingRecipient = 1, far.MemberName, IF(fca.CoachingRecipient = 2, fca.FarmerWorkerName, IF(fca.CoachingRecipient = 3, fca.FarmerWorkerName, IF(fca.CoachingRecipient = 0, fca.FarmerWorkerName, far.MemberName)))) AS 'Farmer Worker Name'
			, fca.EventDate AS 'Visit Date'
			, fca.TimeStart AS 'Start Time'
			, fca.TimeEnd AS 'End time'
			, SEC_TO_TIME(UNIX_TIMESTAMP(fca.TimeEnd) - UNIX_TIMESTAMP(fca.TimeStart)) AS 'Total Hour'
			, IFNULL(fca.Latitude, ST_Y(fca.LatLong)) Latitude
			, IFNULL(fca.Longitude, ST_X(fca.LatLong)) Longitude
			, CASE 
						WHEN fcn.CategoryID = '1' THEN 'Argonomy'
						WHEN fcn.CategoryID = '2' THEN 'Non Argonomy'
						ELSE ''
				END AS Category
			, IFNULL(ct.Topic, fcn.Topic) AS Topic
			, fca.Sample_pH1 'PH 1'
			, fca.Sample_pH2 'PH 2'
			, fca.Sample_pH3 'PH 3'
			, fca.comment AS 'General Comment'
			, IF(DATE(fca.DateCreated) < fca.EventDate, DATE(fca.DateCreated), fca.EventDate) AS DateSyncedFarmerCoaching
			, IFNULL(fc.UserID,fca.CreatedBy) AS CoachID
			, REPLACE(su.UserRealName,'-notactive','') AS CoachName
			, REPLACE(su.UserName,'-notactive','') AS CoachUserName
		FROM
			ktv_ims_farmer_coaching_activity fca
		LEFT JOIN
			ktv_ims_farmer_coaching fc on fc.CoachingID = fca.CoachingID
		LEFT JOIN
			ktv_members far on far.MemberID = fca.FarmerID
		LEFT JOIN 
			`ktv_village` vil ON vil.VillageID = far.VillageID
		LEFT JOIN 
			`ktv_subdistrict` sub ON sub.SubDistrictID = vil.SubDistrictID
		LEFT JOIN 
			`ktv_district` dis ON dis.DistrictID = sub.DistrictID
		LEFT JOIN 
			`ktv_province` prv ON prv.ProvinceID = dis.ProvinceID
		LEFT JOIN
			view_user su on su.UserId = fca.CreatedBy
		LEFT JOIN 
			ktv_ims ims ON ims.IMSID = fca.IMSID
		LEFT JOIN 
			`ktv_certification_holders` ch ON ch.CertHolderID=ims.CertHolderID
		LEFT JOIN 
			`ktv_first_buyer_program` fbp ON fbp.ProgID=ims.ProgID
		LEFT JOIN 
			`ktv_ims_farmer_coaching_activity_nc` fcn ON fcn.ActivityID=fca.ActivityID
		LEFT JOIN
			ktv_coaching_topic ct on ct.TopicID = fcn.Topic
		LEFT JOIN
			ktv_coaching_subtopic st on st.TopicID = fcn.Subtopic
		LEFT JOIN
			ktv_coaching_finding fn on fn.CoachingFindingID = fcn.Finding
		LEFT JOIN
			ktv_coaching_recommendation cr on cr.CoachingRecomID = fcn.Recommendation
		LEFT JOIN
			ktv_farmer_group fg on fg.FarmerGroupID = far.FarmerGroupID
		LEFT JOIN
			view_tc_supplychain_org vso on vso.SupplychainID = ch.SupplychainID
		WHERE
			1=1 
		AND 
			fc.StatusCode='active' 
		AND 
			fca.StatusCode='active'
		AND
			fca.IMSID = ?
			$sqlWhere
		GROUP BY 
			fcn.ActivityNCID, fca.ActivityID, fca.CoachingID
		ORDER BY 
			ProgramName, IMSID, fca.CoachingID, fca.FarmerID, fca.EventDate ASC";
			
        $query = $this->db->query($sql, array($IMSID));
        return $query->result_array();

	}

	function getExportCoachingActivitySessionSql($IMSID, $pSearch, $sortingField, $sortingDir) {
		if ($sortingField == "")
            $sortingField = 'FarmerName';

        if ($sortingDir == "")
            $sortingDir = 'ASC';
        $sqlWhere = "";
        
        if ($pSearch['textSearch'] != "") {
            $sqlWhere .= " AND (far.FarmerName LIKE '%{$pSearch['textSearch']}%' OR far.FarmerID LIKE '%{$pSearch['textSearch']}%' OR ifc.UserName LIKE '%{$pSearch['textSearch']}%') ";
        }
		
		$sql = "SELECT
			fca.IMSID
			, CONCAT(vso.`Name`,' (',vso.ObjType,')') AS 'Certification Holder'
			, prv.Province
			, dis.District
			, fcn.ActivityID
			, fcn.ActivityNCID
			, sub.SubDistrict
			, vil.VillageID
			, fca.CoachingID
			, far.MemberDisplayID 'Farmer ID'
			, far.MemberName 'Farmer Name'
			, fg.FarmerGroupID 'Farmer Group ID'
			, fg.GroupName 'Farmer Group Name'
			, vil.Village AS 'Adress'
			, IF(fca.CoachingRecipient = 1, 'Registered Farmer', IF(fca.CoachingRecipient = 2, 'Garden Operator', IF(fca.CoachingRecipient = 3, 'Household Member', '-'))) AS 'Training Receiver'
			, IF(fca.CoachingRecipient = 1, far.MemberName, IF(fca.CoachingRecipient = 2, fca.FarmerWorkerName, IF(fca.CoachingRecipient = 3, fca.FarmerWorkerName, IF(fca.CoachingRecipient = 0, fca.FarmerWorkerName, far.MemberName)))) AS 'Farmer Worker Name'
			, fca.EventDate AS 'Visit Date'
			, fca.TimeStart AS 'Start Time'
			, fca.TimeEnd AS 'End time'
			, SEC_TO_TIME(UNIX_TIMESTAMP(fca.TimeEnd) - UNIX_TIMESTAMP(fca.TimeStart)) AS 'Total Hour'
			, IFNULL(fca.Latitude, ST_Y(fca.LatLong)) Latitude
			, IFNULL(fca.Longitude, ST_X(fca.LatLong)) Longitude
			, CASE 
						WHEN fcn.CategoryID = '1' THEN 'Argonomy'
						WHEN fcn.CategoryID = '2' THEN 'Non Argonomy'
						ELSE ''
				END AS Category
			, IFNULL(ct.Topic, fcn.Topic) AS Topic
			, fca.Sample_pH1 'PH 1'
			, fca.Sample_pH2 'PH 2'
			, fca.Sample_pH3 'PH 3'
			, IF(fcn.UrgentlyStatus = 1, 'High', IF(fcn.UrgentlyStatus = 2, 'Medium', IF(fcn.UrgentlyStatus = 3, 'Low', '-'))) AS 'Level Status'
			, IF(fn.CoachingFinding = 'Other', CONCAT(fn.CoachingFinding, IF(fcn.FindingOtherText IS NULL, '', CONCAT(' (', fcn.FindingOtherText, ')'))), IFNULL(fn.CoachingFinding, fcn.Finding)) AS Finding
			, fcn.ActivityType 'Activity Type'
			, IF(cr.CoachingRecom = 'Other', CONCAT(cr.CoachingRecom, IF(fcn.RecomOtherText IS NULL, '', CONCAT(' (', fcn.RecomOtherText, ')'))), IFNUll(cr.CoachingRecom, fcn.Recommendation)) AS Recommendation
			, fcn.Target
			, fcn.deadline AS 'DeadLine'
			, IF(fcn.ActNCStatus = 1, 'Completed', IF(fcn.ActNCStatus = 2, 'In Progress', IF(fcn.ActNCStatus = 3, 'Not Started', IF(fcn.ActNCStatus = 4, 'Canceled', '-')))) AS 'Status'
			, fcn.Explanation AS 'Status Notes'
			, fca.comment AS 'General Comment'
			, IF(DATE(fca.DateCreated) < fca.EventDate, DATE(fca.DateCreated), fca.EventDate) AS DateSyncedFarmerCoaching
			, IFNULL(fc.UserID,fca.CreatedBy) AS CoachID
			, REPLACE(su.UserRealName,'-notactive','') AS CoachName
			, REPLACE(su.UserName,'-notactive','') AS CoachUserName
		FROM
			ktv_ims_farmer_coaching_activity fca
		LEFT JOIN
			ktv_ims_farmer_coaching fc on fc.CoachingID = fca.CoachingID
		LEFT JOIN
			ktv_members far on far.MemberID = fca.FarmerID
		LEFT JOIN 
			`ktv_village` vil ON vil.VillageID = far.VillageID
		LEFT JOIN 
			`ktv_subdistrict` sub ON sub.SubDistrictID = vil.SubDistrictID
		LEFT JOIN 
			`ktv_district` dis ON dis.DistrictID = sub.DistrictID
		LEFT JOIN 
			`ktv_province` prv ON prv.ProvinceID = dis.ProvinceID
		LEFT JOIN
			view_user su on su.UserId = fca.CreatedBy
		LEFT JOIN 
			ktv_ims ims ON ims.IMSID = fca.IMSID
		LEFT JOIN 
			`ktv_certification_holders` ch ON ch.CertHolderID=ims.CertHolderID
		LEFT JOIN 
			`ktv_first_buyer_program` fbp ON fbp.ProgID=ims.ProgID
		LEFT JOIN 
			`ktv_ims_farmer_coaching_activity_nc` fcn ON fcn.ActivityID=fca.ActivityID
		LEFT JOIN
			ktv_coaching_topic ct on ct.TopicID = fcn.Topic
		LEFT JOIN
			ktv_coaching_subtopic st on st.TopicID = fcn.Subtopic
		LEFT JOIN
			ktv_coaching_finding fn on fn.CoachingFindingID = fcn.Finding
		LEFT JOIN
			ktv_coaching_recommendation cr on cr.CoachingRecomID = fcn.Recommendation
		LEFT JOIN
			ktv_farmer_group fg on fg.FarmerGroupID = far.FarmerGroupID
		LEFT JOIN
			view_tc_supplychain_org vso on vso.SupplychainID = ch.SupplychainID
		WHERE
			1=1 
		AND 
			fc.StatusCode='active' 
		AND 
			fca.StatusCode='active'
		AND
			fca.IMSID = ?
		#AND ActivityNCID IS NOT NULL
		$sqlWhere
		GROUP BY 
			fca.ActivityID, fca.CoachingID
		ORDER BY 
			ProgramName, IMSID, fca.CoachingID, fca.FarmerID, fca.EventDate ASC";
			
		$query = $this->db->query($sql, array($IMSID));
		return $query->result_array();
	}

	function getGridActivityNC($ActivityID,$start,$limit,$sortingField,$sortingDir){
		if ($sortingField == "") {
			$sortingField = 'Topic';
		}

		if ($sortingDir == "") {
			$sortingDir = 'ASC';
		}

	  	$sql = "SELECT
				anc.ActivityNCID,
				anc.ActivityID,
				anc.CategoryID,
				ct.Topic,
				cs.Subtopic,
				CASE 
					WHEN anc.UrgentlyStatus = 3 THEN 'Low'
					WHEN anc.UrgentlyStatus = 2 THEN 'Medium'
					WHEN anc.UrgentlyStatus = 1 THEN 'High'
					ELSE '-'
				END AS UrgentlyStatus,
				anc.Finding,
				anc.FindingOtherText,
				anc.ActivityType,
				recom.CoachingRecom Recommendation,
				anc.RecomOtherText,
				anc.Target,
				anc.FollowupStatus,
				anc.Deadline,
				anc.ActNCStatus,
				anc.Explanation,
				p.PersonNm AS CreatedBy,
				anc.DateCreated 
			FROM
				ktv_ims_farmer_coaching_activity_nc AS anc
				LEFT JOIN ktv_coaching_topic AS ct ON ct.TopicID = anc.Topic
				LEFT JOIN ktv_coaching_subtopic AS cs on cs.SubtopicID = anc.Subtopic
				LEFT JOIN ktv_coaching_recommendation AS recom on recom.CoachingRecomID = anc.Recommendation
				LEFT JOIN ktv_persons AS p ON p.UserID = anc.CreatedBy
			WHERE
				anc.ActivityID = ? 
				AND anc.StatusCode = 'active'
		ORDER BY $sortingField $sortingDir
		LIMIT ?,?
	  	";
	  	$query = $this->db->query($sql, array($ActivityID, (int) $start, (int) $limit));
	  
		$sql_total   = "SELECT FOUND_ROWS() AS total";
		$query_total = $this->db->query($sql_total);
		if ($query->num_rows() > 0) {
		  	$total = $query_total->row_array(0);
		  	return array(
				'data'  => $query->result_array(),
				'total' => $total['total'],
		  	);
	  	}
	  	return false;
	}

	function getCoachingActivitybyID($ActivityID){
	  $sql = " SELECT
		ca.ActivityID,
		ca.CoachingID,
		fc.FarmerID,
		far.MemberName FarmerName,
		ca.EventDate,
		ca.TimeStart,
		ca.TimeEnd,
		ca.DateCreated,
		ca.FarmerInPlace,
		ca.Reason,
		ca.Comment,
		IFNULL(ST_X(ca.Latlong), ca.Longitude) Longitude,
		IFNULL(ST_Y(ca.Latlong), ca.Latitude) Latitude,
		ca.PhotoActPath,
		ca.FarmerSigActPath,
		p.PersonNm as CreatedBy
		FROM `ktv_ims_farmer_coaching_activity` as ca
		LEFT JOIN ktv_ims_farmer_coaching fc on fc.CoachingID = ca.CoachingID
		LEFT JOIN ktv_persons as p ON p.UserId = ca.CreatedBy
		LEFT JOIN ktv_members as far on far.MemberID = fc.FarmerID
		WHERE ActivityID = ?
	  ";
	  $query = $this->db->query($sql, array($ActivityID));
	  if ($query->num_rows() > 0) {
		  $data = array();
		  foreach($query->result() as $row => $key){
			  if($key->FarmerSigActPath){
				if($this->awsfileupload->doesObjectExist($key->FarmerSigActPath) == true) {
				  $key->FarmerSigActPath = $this->config->item('CTCDN')."/".$key->FarmerSigActPath;
				}else{
				  $key->FarmerSigActPath = base_url().$key->FarmerSigActPath;
				}
			  }
			  if($key->PhotoActPath){
				if($this->awsfileupload->doesObjectExist($key->PhotoActPath) == true) {
				  $key->PhotoActPath = $this->config->item('CTCDN')."/".$key->PhotoActPath;
				}else{
				  $key->PhotoActPath = base_url().$key->PhotoActPath;
				}
			  }
			  $data[] = $key;
		  }
		  return array(
			'data'  => $data,
		  );
	  }
	  return false;
	}

	public function getAcqProGridFarmerIdentification($IMSID, $StringSearch, $start = 0, $limit = 50, $sortingField, $sortingDir)
	{
		if ($sortingField == "") {
			$sortingField = 'ApplicantName';
		}

		if ($sortingDir == "") {
			$sortingDir = 'ASC';
		}

		$sql = "SELECT
				SQL_CALC_FOUND_ROWS
				a.ApplicantID
				, mem.MemberDisplayID DisplayID
				, a.`Fullname` AS ApplicantName
				, CASE
					WHEN a.Gender='m' THEN 'Male'
					WHEN a.Gender='f' THEN 'Female'
				END AS Gender
				, dis.District
				, subd.SubDistrict
				, vil.Village
				, IF(cpg.GroupName IS NOT NULL,CONCAT(cpg.FarmerGroupID,' - ',cpg.GroupName),a.NewGroupName) AS FarmerGroup
				, CASE
					WHEN a.ActiveStatus = 'active' THEN 'Active'
					WHEN a.ActiveStatus = 'inactive' THEN 'Inactive'
				END AS ApplicantStatus
			FROM
				ktv_applicant_farmers a
				LEFT JOIN ktv_village vil ON a.VillageID = vil.VillageID
				LEFT JOIN ktv_subdistrict subd ON subd.SubDistrictID = vil.SubDistrictID
				LEFT JOIN ktv_district dis ON dis.DistrictID = subd.DistrictID
				LEFT JOIN ktv_farmer_group cpg ON a.CPGid = cpg.FarmerGroupID
				LEFT JOIN ktv_members mem on mem.ApplicantID = a.ApplicantID
			WHERE
				a.`StatusCode` = 'active'
				AND a.`IMSID` = ?
				AND a.Fullname LIKE ?
			ORDER BY $sortingField $sortingDir
			LIMIT ?,?";
		$query = $this->db->query($sql, array($IMSID, '%' . $StringSearch . '%', (int) $start, (int) $limit));

		$sql_total   = "SELECT FOUND_ROWS() AS total";
		$query_total = $this->db->query($sql_total);
		if ($query->num_rows() > 0) {
			$total = $query_total->row_array(0);
			return array(
				'data'  => $query->result_array(),
				'total' => $total['total'],
			);
		}
		return false;
	}

	public function getAcqProGridSocialization($IMSID, $StringSearch, $start = 0, $limit = 50, $sortingField, $sortingDir, $opsiCall)
	{
		if ($sortingField == "") {
			$sortingField = 'Name';
		}

		if ($sortingDir == "") {
			$sortingDir = 'ASC';
		}

		$sql = "SELECT
				SQL_CALC_FOUND_ROWS
				CASE
					WHEN ObjType = 'Applicant' THEN (
						SELECT DisplayID FROM ktv_applicant_farmers WHERE ApplicantID = a.ObjID LIMIT 1
					)
					WHEN ObjType = 'Existing Farmer' THEN a.ObjID
				END DisplayID
				, IFNULL(m.MemberDisplayID,'-') AS DestObjID
				, a.Name
				, IFNULL(a.Gender, m.Gender) Gender
				, a.SubDistrict
				, a.Village
				, a.FarmerGroup
				, b.EventName AS SocEventName
				, CONCAT(b.EventStart,' - ',b.EventEnd) AS DateOfSocialization
				, a.DateGenerated
				, a.IMSSocID
			FROM
				ktv_ims_soc_sel a
				LEFT JOIN ktv_ims_socializations b ON a.IMSSocID = b.IMSSocID
				LEFT JOIN ktv_members m on m.MemberID = a.DestObjID
			WHERE 1=1
				AND a.`IMSID` = ?
				AND a.DateApproval IS NULL
				AND a.Name LIKE ?
				AND a.ObjType IN ('Applicant','Existing Farmer')
			ORDER BY $sortingField $sortingDir
			";

		if ($opsiCall == 'php_code') {
			$query = $this->db->query($sql, array($IMSID, '%' . $StringSearch . '%'));
			return $query->result_array();
		}

		$query = $this->db->query($sql . " LIMIT ?,?", array($IMSID, '%' . $StringSearch . '%', (int) $start, (int) $limit));

		$sql_total   = "SELECT FOUND_ROWS() AS total";
		$query_total = $this->db->query($sql_total);
		if ($query->num_rows() > 0) {
			$total = $query_total->row_array(0);
			return array(
				'data'  => $query->result_array(),
				'total' => $total['total'],
			);
		}
		return false;
	}

	public function getAcqProGridSelection($IMSID, $StringSearch, $Participate, $Recommendation, $Selection, $start = 0, $limit = 50, $sortingField, $sortingDir, $opsiCall)
	{
		if ($sortingField == "") {
			$sortingField = 'Name';
		}

		if ($sortingDir == "") {
			$sortingDir = 'ASC';
		}

		$sqlStatus = "";

		//Filter ======================== (Begin)
		if ($Participate == 1) {
			$sqlStatus .= " AND a.ParticipateInSocializationStatus = '1' ";
		}
		if ($Recommendation == 1) {
			$sqlStatus .= " AND a.RecommendationStatus = '1' ";
		}
		if ($Selection == 1) {
			$sqlStatus .= " AND a.SelectionStatus = '1' ";
		}
		//Filter ======================== (End)

		$sql = "SELECT
				SQL_CALC_FOUND_ROWS
				CASE
					WHEN ObjType = 'Applicant' THEN (
						SELECT DisplayID FROM ktv_applicant_farmers WHERE ApplicantID = a.ObjID LIMIT 1
					)
					WHEN ObjType = 'Existing Farmer' THEN a.ObjID
					WHEN ObjType = 'Existing Certified Farmer' THEN a.ObjID
				END DisplayID
				, IFNULL(m.MemberDisplayID,'-') AS DestObjID
				, a.Name
				, IFNULL(a.Gender, m.Gender) Gender
				, a.Province
				, a.District
				, a.SubDistrict
				, a.Village
				, a.FarmerGroup
				, CASE
					WHEN a.RecommendationStatus = '1' THEN 'Yes'
					WHEN a.RecommendationStatus = '2' THEN 'No'
					ELSE '-'
				END AS Recommendation
				, CASE
					WHEN a.ParticipateInSocializationStatus = '1' THEN 'Yes'
					WHEN a.ParticipateInSocializationStatus = '2' THEN 'No'
					ELSE '-'
				END AS ParticipateInSocialization
				, CASE
					WHEN a.SelectionStatus = '1' THEN 'Yes'
					WHEN a.SelectionStatus = '2' THEN 'No'
					ELSE '-'
				END AS SelectionStatus
				, a.DateGenerated
				, a.IMSSocID
				, a.ObjType AS ParticipantType
			FROM
				`ktv_ims_soc_sel` a
			LEFT JOIN ktv_members m on m.MemberID = a.DestObjID
			WHERE
				a.`IMSID` = ?
				AND a.Name LIKE ?
				AND a.DateApproval IS NULL
				AND (
					a.ParticipateInSocializationStatus IS NOT NULL OR
					a.RecommendationStatus IS NOT NULL OR
					a.SelectionStatus IS NOT NULL
				)
				$sqlStatus
			ORDER BY $sortingField $sortingDir
			";

		if ($opsiCall == 'php_code') {
			$query = $this->db->query($sql, array($IMSID, '%' . $StringSearch . '%'));
			return $query->result_array();
		}

		$query = $this->db->query($sql . " LIMIT ?,?", array($IMSID, '%' . $StringSearch . '%', (int) $start, (int) $limit));

		$sql_total   = "SELECT FOUND_ROWS() AS total";
		$query_total = $this->db->query($sql_total);
		if ($query->num_rows() > 0) {
			$total = $query_total->row_array(0);
			return array(
				'data'  => $query->result_array(),
				'total' => $total['total'],
			);
		}else{
			return array(
				'data'  => array(),
				'total' => 0,
			);
		}
		return false;
	}

	public function getAcqProGridSelectionApproved($IMSID, $StringSearch, $start, $limit, $sortingField, $sortingDir, $opsiCall)
	{
		if ($sortingField == "") {
			$sortingField = 'Name';
		}

		if ($sortingDir == "") {
			$sortingDir = 'ASC';
		}

		$sql = "SELECT
				SQL_CALC_FOUND_ROWS
				CASE
					WHEN ObjType = 'Applicant' THEN (
						SELECT DisplayID FROM ktv_applicant_farmers WHERE ApplicantID = a.ObjID LIMIT 1
					)
					WHEN ObjType = 'Existing Farmer' THEN a.ObjID
					WHEN ObjType = 'Existing Certified Farmer' THEN a.ObjID
				END DisplayID
				, IFNULL(m.MemberDisplayID,'-') AS DestObjID
				, a.Name
				, IFNULL(a.Gender, m.Gender) Gender
				, a.Province
				, a.District
				, a.SubDistrict
				, a.Village
				, a.FarmerGroup
				, a.IMSSocID
				, a.ApprovalRemark
				, (SELECT su.UserRealName  FROM sys_user su WHERE su.UserId = a.`ApprovalBy` LIMIT 1) AS ApprovalBy
				, a.DateApproval
				, a.ObjType AS ParticipantType
			FROM
				`ktv_ims_soc_sel` a
			LEFT JOIN ktv_members m on m.MemberID = a.DestObjID
			WHERE
				a.`IMSID` = ?
				AND a.Name LIKE ?
				AND a.DateApproval IS NOT NULL
				#AND a.SelectionStatus = '1'
			ORDER BY $sortingField $sortingDir
			";

		if ($opsiCall == 'php_code') {
			$query = $this->db->query($sql, array($IMSID, '%' . $StringSearch . '%'));
			return $query->result_array();
		}

		$query = $this->db->query($sql . " LIMIT ?,?", array($IMSID, '%' . $StringSearch . '%', (int) $start, (int) $limit));

		$sql_total   = "SELECT FOUND_ROWS() AS total";
		$query_total = $this->db->query($sql_total);
		if ($query->num_rows() > 0) {
			$total = $query_total->row_array(0);
			return array(
				'data'  => $query->result_array(),
				'total' => $total['total'],
			);
		}
		return false;
	}

	public function getComboBatchTraining()
	{
		$sql = "SELECT
				a.`CpgBatchID` AS id
				, CONCAT(a.`BatchNumber`,' - ',b.`PartnerName`) AS label
			FROM
				ktv_cpg_batch a
				INNER JOIN ktv_program_partner b ON a.`PartnerID` = b.`PartnerID`
			WHERE
				a.`StatusCode` = 'active'
			ORDER BY a.`BatchNumber` DESC";
		$query = $this->db->query($sql);

		$result['data']    = $query->result_array();
		$result['success'] = true;
		return $result;
	}

	public function getAcqProGridTraining($IMSID, $StringSearch, $start, $limit, $sortingField, $sortingDir, $opsiCall)
	{
		if ($sortingField == "") {
			$sortingField = 'FarmerID';
		}

		if ($sortingDir == "") {
			$sortingDir = 'ASC';
		}

		//get info Batch dari IMS
		/*
		$sql="SELECT
		a.`CpgBatchID`
		FROM
		ktv_ims a
		WHERE
		a.`IMSID` = ?
		LIMIT 1";
		$query = $this->db->query($sql, array($IMSID));
		$dataIMS = $query->row_array();*/

		$sql = "SELECT
				SQL_CALC_FOUND_ROWS
				a.`FarmerID`
				, far.`MemberName` FarmerName
				, far.`ApplicantID`
				, CASE
					WHEN far.Gender = 'm' THEN '" . lang('Male') . "'
					WHEN far.Gender = 'f' THEN '" . lang('Female') . "'
					ELSE '-'
				END AS Gender
				, prov.Province
				, dis.District
				, subd.SubDistrict
				, vil.Village
				, CONCAT(cpg.FarmerGroupID,' - ',cpg.GroupName) AS FarmerGroup
				, CASE
					WHEN a.EligibleStatus = '1' THEN '" . lang('Yes') . "'
					WHEN a.EligibleStatus = '2' THEN '" . lang('No') . "'
					ELSE '-'
				END AS EligibleStatus
				, a.TrainingReq
				, a.TrainingReqPercentage AS PercentageAttendance
				, a.DateGenerated
			FROM
				ktv_ims_training_gap_coc a
				INNER JOIN ktv_members far ON a.`FarmerID` = far.`MemberID`

				LEFT JOIN ktv_village vil ON far.VillageID = vil.VillageID
				LEFT JOIN ktv_subdistrict subd ON vil.SubDistrictID = subd.SubDistrictID
				LEFT JOIN ktv_district dis ON subd.DistrictID = dis.DistrictID
				LEFT JOIN ktv_province prov ON dis.ProvinceID = prov.ProvinceID

				LEFT JOIN ktv_farmer_group cpg ON far.FarmerGroupID = cpg.FarmerGroupID
			WHERE
				a.`IMSID` = ?
				AND a.DateApproval IS NULL
				AND far.MemberName LIKE ?
			ORDER BY $sortingField $sortingDir";

		if ($opsiCall == 'php_code') {
			$query = $this->db->query($sql, array($IMSID, '%' . $StringSearch . '%'));
			return $query->result_array();
		}

		$query = $this->db->query($sql . " LIMIT ?,?", array($IMSID, '%' . $StringSearch . '%', (int) $start, (int) $limit));

		$sql_total   = "SELECT FOUND_ROWS() AS total";
		$query_total = $this->db->query($sql_total);
		$total       = $query_total->row_array(0);
		return array(
			'data'  => $query->result_array(),
			'total' => $total['total'],
		);
	}

	public function getAcqProGridTrainingApproved($IMSID, $StringSearch, $DateApprovalSearch, $start, $limit, $sortingField, $sortingDir, $opsiCall)
	{
		if ($sortingField == "") {
			$sortingField = 'FarmerID';
		}

		if ($sortingDir == "") {
			$sortingDir = 'ASC';
		}

		if($DateApprovalSearch == ''){
			$DateApprovalSearch = date("Y-m-d");
		}

		$sql = "SELECT
				SQL_CALC_FOUND_ROWS
				a.`FarmerID`
				, far.`MemberName` FarmerName
				, CASE
					WHEN far.Gender = 'm' THEN '" . lang('Male') . "'
					WHEN far.Gender = 'f' THEN '" . lang('Female') . "'
					ELSE '-'
				END AS Gender
				, CONCAT(cpg.FarmerGroupID,' - ',cpg.GroupName) AS FarmerGroup
				, a.TrainingReq
				, a.TrainingReqPercentage AS PercentageAttendance
				, a.ApprovalRemark AS AppRemark
				, (SELECT su.UserRealName  FROM sys_user su WHERE su.UserId = a.`ApprovalBy` LIMIT 1) AS AppBy
				, a.DateApproval
			FROM
				ktv_ims_training_gap_coc a
				INNER JOIN ktv_members far ON a.`FarmerID` = far.`MemberID`

				LEFT JOIN ktv_farmer_group cpg ON far.FarmerGroupID = cpg.FarmerGroupID
			WHERE
				a.`IMSID` = ?
				AND a.DateApproval IS NOT NULL
				AND ( (DATE(a.DateApproval) = ?) OR ('' = ?) )
				AND far.MemberName LIKE ?
			ORDER BY $sortingField $sortingDir
			";

		if ($opsiCall == 'php_code') {
			$query = $this->db->query($sql, array($IMSID, $DateApprovalSearch, $DateApprovalSearch, '%' . $StringSearch . '%'));
			return $query->result_array();
		}

		$query = $this->db->query($sql . " LIMIT ?,?", array($IMSID, $DateApprovalSearch, $DateApprovalSearch, '%' . $StringSearch . '%', (int) $start, (int) $limit));

		$sql_total   = "SELECT FOUND_ROWS() AS total";
		$query_total = $this->db->query($sql_total);
		$total       = $query_total->row_array(0);
		return array(
			'data'  => $query->result_array(),
			'total' => $total['total'],
		);
	}

	public function getAcqProGridCandidatePreICS($IMSID, $StringSearch, $start, $limit, $sortingField, $sortingDir, $opsiCall){
		if ($sortingField == "") {
			$sortingField = 'FarmerID';
		}
		if ($sortingDir == "") {
			$sortingDir = 'ASC';
		}

		$sql="SELECT
				SQL_CALC_FOUND_ROWS
				far.MemberDisplayID FarmerID
				, far.`MemberName` FarmerName
				, CASE
					WHEN far.Gender='m' THEN 'Male'
					WHEN far.Gender='f' THEN 'Female'
				END AS Gender
				, CONCAT(IFNULL(cpg.FarmerGroupID,cpg2.FarmerGroupID),' - ',IFNULL(cpg.GroupName,cpg2.GroupName)) AS FarmerGroup
				, IFNULL(a.TrainingPercentage,'-') AS TrainingPercentage
				, CASE
					WHEN a.StatusComply='1' THEN 'Yes'
					WHEN a.StatusComply='2' THEN 'No'
				END AS StatusComply
				, IFNULL(a.AuditRemark,'-') AS AuditRemark
			FROM
				`ktv_certification_pre_afl` a
				LEFT JOIN ktv_members far ON a.`FarmerID` = far.`MemberID`
				LEFT JOIN ktv_farmer_group cpg ON far.FarmerGroupID = cpg.FarmerGroupID
				LEFT JOIN ktv_applicant_farmers app on app.ApplicantID = a.FarmerID
				LEFT JOIN ktv_farmer_group cpg2 ON app.CPGid = cpg2.FarmerGroupID
			WHERE
				a.`IMSID` = ?
				AND a.`StatusCode` = 'active'
				AND far.MemberName LIKE ?
			ORDER BY $sortingField $sortingDir";

		if ($opsiCall == 'php_code') {
			$query = $this->db->query($sql, array($IMSID, '%' . $StringSearch . '%'));
			return $query->result_array();
		}

		$query = $this->db->query($sql . " LIMIT ?,?", array($IMSID, '%' . $StringSearch . '%', (int) $start, (int) $limit));

		$sql_total   = "SELECT FOUND_ROWS() AS total";
		$query_total = $this->db->query($sql_total);
		$total       = $query_total->row_array(0);
		return array(
			'data'  => $query->result_array(),
			'total' => $total['total'],
		);
	}

	public function getAcqProGridTrainingInfoDetail($IMSID, $FarmerID)
	{
		$arrReturn = array();

		//Get Info Training GAP Terbaru
		$sql = "SELECT
				tra.`CpgBatchTrainingID`
				, DATE_FORMAT(tra.`TrainingStart`,'%Y-%m-%d') AS `Start`
				, DATE_FORMAT(tra.`TrainingEnd`,'%Y-%m-%d') AS `End`
				, trafar.`Percentage` AS AttendancePercentage
				, CONCAT(batch.`BatchNumber`,' - ',part.`PartnerName`) AS BatchNumber
			FROM
				ktv_cpg_batch_trainings tra
				INNER JOIN ktv_cpg_batch_trainings_farmers trafar ON tra.`CpgBatchTrainingID` = trafar.`CpgBatchTrainingID`

				LEFT JOIN ktv_cpg_batch batch ON tra.`CpgBatchID` = batch.`CpgBatchID`
				LEFT JOIN ktv_program_partner part ON batch.`PartnerID` = part.`PartnerID`
			WHERE
				tra.`StatusCode` = 'active'
				AND tra.`CPGtrainingsID` = '1' #GAP
				AND tra.`IMSID` = ?
				AND trafar.`FarmerID` = ?
			ORDER BY tra.`TrainingStart` DESC
			LIMIT 1";
		$query = $this->db->query($sql, array($IMSID, $FarmerID));
		$data  = $query->row_array();
		if ($data['CpgBatchTrainingID'] == "") {
			$data['Topic']                = "-";
			$data['Start']                = "-";
			$data['End']                  = "-";
			$data['AttendancePercentage'] = "-";
			$data['BatchNumber']          = "-";
		}

		$arrReturn[0]['CpgBatchTrainingID']   = $data['CpgBatchTrainingID'];
		$arrReturn[0]['Topic']                = "GAP Basic Good Agriculture Practices";
		$arrReturn[0]['Start']                = $data['Start'];
		$arrReturn[0]['End']                  = $data['End'];
		$arrReturn[0]['AttendancePercentage'] = $data['AttendancePercentage'];
		$arrReturn[0]['BatchNumber']          = $data['BatchNumber'];

		//Get Info Training CoC Terbaru
		$sql = "SELECT
				tra.`CpgBatchTrainingID`
				, 'GBP - COC -  Traceability and Certification (FFS Farmer)' AS Topic
				, DATE_FORMAT(tra.`TrainingStart`,'%Y-%m-%d') AS `Start`
				, DATE_FORMAT(tra.`TrainingEnd`,'%Y-%m-%d') AS `End`
				, trafar.`Percentage` AS AttendancePercentage
				, CONCAT(batch.`BatchNumber`,' - ',part.`PartnerName`) AS BatchNumber
			FROM
				ktv_cpg_batch_trainings tra
				INNER JOIN ktv_cpg_batch_trainings_sub_topics trasub ON tra.`CpgBatchTrainingID` = trasub.`CpgBatchTrainingID`
				INNER JOIN ktv_cpg_batch_trainings_farmers trafar ON tra.`CpgBatchTrainingID` = trafar.`CpgBatchTrainingID`

				LEFT JOIN ktv_cpg_batch batch ON tra.`CpgBatchID` = batch.`CpgBatchID`
				LEFT JOIN ktv_program_partner part ON batch.`PartnerID` = part.`PartnerID`
			WHERE
				tra.`StatusCode` = 'active'
				AND tra.`CPGtrainingsID` = '14'
				AND trasub.`SubCpgTrainingsID` = '53'
				AND tra.`IMSID` = ?
				AND trafar.`FarmerID` = ?
			ORDER BY tra.`TrainingStart` DESC
			LIMIT 1";
		$query = $this->db->query($sql, array($IMSID, $FarmerID));
		$data  = $query->row_array();
		if ($data['CpgBatchTrainingID'] == "") {
			$data['Topic']                = "-";
			$data['Start']                = "-";
			$data['End']                  = "-";
			$data['AttendancePercentage'] = "-";
			$data['BatchNumber']          = "-";
		}

		$arrReturn[1]['CpgBatchTrainingID']   = $data['CpgBatchTrainingID'];
		$arrReturn[1]['Topic']                = 'GBP - COC -  Traceability and Certification (FFS Farmer)';
		$arrReturn[1]['Start']                = $data['Start'];
		$arrReturn[1]['End']                  = $data['End'];
		$arrReturn[1]['AttendancePercentage'] = $data['AttendancePercentage'];
		$arrReturn[1]['BatchNumber']          = $data['BatchNumber'];

		$result['data']    = $arrReturn;
		$result['success'] = true;
		return $result;
	}

	public function CekDuplicateSocSel($ObjID,$ObjType,$IMSID){
		$sql="SELECT
				a.`SocSelID`
			FROM
				ktv_ims_soc_sel a
			WHERE
				a.`ObjID` = ?
				AND a.`ObjType` = ?
				AND a.`IMSID` = ?";
		$DataCek = $this->db->query($sql,array($ObjID,$ObjType,$IMSID))->row_array();
		if(isset($DataCek['SocSelID'])){
			return true;
		}else{
			return false;
		}
	}

	public function acqProcessGenSocSel($IMSID,$RemarkText)
	{
		$this->db->trans_begin();

		//Hapus dl data yang belum terapprove
		$sql   = "DELETE FROM ktv_ims_soc_sel WHERE IMSID = ? AND DateApproval IS NULL AND ObjType IN ('Applicant','Existing Farmer')";
		$query = $this->db->query($sql, array($IMSID));

		// ======================================== Applicant Web (Begin) =============================================//
		$sql = "SELECT
				sp.`ApplicantID`
				, app.`Fullname` AS `Name`
				, CASE
					WHEN app.`Gender`='m' THEN 'Male'
					WHEN app.`Gender`='f' THEN 'Female'
					ELSE 'Male'
				END AS Gender
				, prov.`Province`
				, dis.`District`
				, subd.`SubDistrict`
				, vil.`Village`
				, CONCAT(cpg.`FarmerGroupID`,' - ',cpg.`GroupName`) AS FarmerGroup
				, IFNULL(far.MemberID, sp.ApplicantID) AS DestObjID
			FROM
				ktv_ims_socializations s
				LEFT JOIN ktv_ims_socialization_participants sp ON s.`IMSSocID` = sp.`IMSSocID`

				LEFT JOIN ktv_applicant_farmers app ON sp.`ApplicantID` = app.`ApplicantID`

				LEFT JOIN ktv_village vil ON app.`VillageID` = vil.`VillageID`
				LEFT JOIN ktv_subdistrict subd ON vil.`SubDistrictID` = subd.`SubDistrictID`
				LEFT JOIN ktv_district dis ON subd.`DistrictID` = dis.`DistrictID`
				LEFT JOIN ktv_province prov ON dis.`ProvinceID` = prov.`ProvinceID`

				LEFT JOIN ktv_farmer_group cpg ON app.`CPGid` = cpg.`FarmerGroupID`
				LEFT JOIN ktv_members far ON app.ApplicantID = far.ApplicantID
			WHERE
				s.`StatusCode` = 'active'
				AND sp.`StatusCode` = 'active'
				AND app.StatusCode = 'active'
				AND s.`IMSID` = ?
				AND sp.`ParticipantType` = 'Applicant Web'
			GROUP BY sp.`ApplicantID`";
		$ListApplicantWeb = $this->db->query($sql, array($IMSID))->result_array();

		for ($i = 0; $i < count($ListApplicantWeb); $i++) {
			$DataInsert = array();

			//Get Data Socialization nya
			$sql = "SELECT
					s.`IMSSocID`
					, s.`EventStart`
					, s.`EventEnd`
					, sp.`ParticipateInSocializationStatus`
					, sp.`RecommendationStatus`
					, sp.`SelectionStatus`
				FROM
					ktv_ims_socializations s
					INNER JOIN ktv_ims_socialization_participants sp ON s.`IMSSocID` = sp.`IMSSocID`

					#Get Soc Terbaru
					INNER JOIN (
						SELECT
							SUBSTRING_INDEX(
								GROUP_CONCAT(s_s.`IMSSocID` ORDER BY `s_s`.EventStart DESC SEPARATOR ',')
								, ','
								, 1
							) AS IMSSocID
						FROM
							ktv_ims_socializations s_s
							INNER JOIN ktv_ims_socialization_participants s_sp ON s_s.`IMSSocID` = s_sp.`IMSSocID`
						WHERE
							s_s.`StatusCode` = 'active'
							AND s_sp.`StatusCode` = 'active'
							AND s_s.`IMSID` = ?
							AND s_sp.ApplicantID = ?
					) AS lat_soc ON 1=1
						AND s.`IMSSocID` = lat_soc.`IMSSocID`
				WHERE
					s.`StatusCode` = 'active'
					AND sp.`StatusCode` = 'active'
					AND s.`IMSID` = ?
					AND sp.`ApplicantID` = ?";
			$DataSoc = $this->db->query($sql, array($IMSID, $ListApplicantWeb[$i]['ApplicantID'], $IMSID, $ListApplicantWeb[$i]['ApplicantID']))->row_array();

			if($DataSoc['ParticipateInSocializationStatus'] == "" || $DataSoc['ParticipateInSocializationStatus'] == "0") $DataSoc['ParticipateInSocializationStatus'] = null;
			if($DataSoc['RecommendationStatus'] == "" || $DataSoc['RecommendationStatus'] == "0") $DataSoc['RecommendationStatus'] = null;
			if($DataSoc['SelectionStatus'] == "" || $DataSoc['SelectionStatus'] == "0") $DataSoc['SelectionStatus'] = null;

			$DataInsert['ObjID']                            = $ListApplicantWeb[$i]['ApplicantID'];
			$DataInsert['ObjType']                          = 'Applicant';
			$DataInsert['DestObjID']                        = $ListApplicantWeb[$i]['DestObjID'];
			$DataInsert['IMSID']                            = $IMSID;
			$DataInsert['IMSSocID']                         = $DataSoc['IMSSocID'];
			$DataInsert['Name']                             = $ListApplicantWeb[$i]['Name'];
			$DataInsert['Gender']                           = $ListApplicantWeb[$i]['Gender'];
			$DataInsert['Province']                         = $ListApplicantWeb[$i]['Province'];
			$DataInsert['District']                         = $ListApplicantWeb[$i]['District'];
			$DataInsert['SubDistrict']                      = $ListApplicantWeb[$i]['SubDistrict'];
			$DataInsert['Village']                          = $ListApplicantWeb[$i]['Village'];
			$DataInsert['FarmerGroup']                      = $ListApplicantWeb[$i]['FarmerGroup'];
			$DataInsert['EventStart']                       = $DataSoc['EventStart'];
			$DataInsert['EventEnd']                         = $DataSoc['EventEnd'];
			$DataInsert['ParticipateInSocializationStatus'] = $DataSoc['ParticipateInSocializationStatus'];
			$DataInsert['RecommendationStatus']             = $DataSoc['RecommendationStatus'];
			$DataInsert['SelectionStatus']                  = $DataSoc['SelectionStatus'];
			$DataInsert['DateGenerated']                    = date('Y-m-d H:i:s');
			$DataInsert['GeneratedBy']                      = $_SESSION['userid'];

			//Cek apakah sudah ada datanya (karena ada kemungkinan datanya sudah di approve)
			$CekDuplicateSocSel = $this->CekDuplicateSocSel($DataInsert['ObjID'],$DataInsert['ObjType'],$DataInsert['IMSID']);
			if($CekDuplicateSocSel == false){
				//Insert ke Tabel
				$this->db->insert('ktv_ims_soc_sel', $DataInsert);
			}
		}
		// ======================================== Applicant Web (End) =============================================//

		// ======================================== Applicant Mobile (Begin) =============================================//
		$sql = "SELECT
				app.`ApplicantID`
				, app.`Fullname` AS `Name`
				, CASE
					WHEN app.`Gender`='m' THEN 'Male'
					WHEN app.`Gender`='f' THEN 'Female'
					ELSE 'Male'
				END AS Gender
				, prov.`Province`
				, dis.`District`
				, subd.`SubDistrict`
				, vil.`Village`
				, CONCAT(cpg.`FarmerGroupID`,' - ',cpg.`GroupName`) AS FarmerGroup
				, far.MemberID AS DestObjID
			FROM
				ktv_ims_socializations s
				LEFT JOIN ktv_ims_socialization_participants sp ON s.`IMSSocID` = sp.`IMSSocID`

				LEFT JOIN ktv_applicant_farmers app ON sp.`MobileUID` = app.`MobileUID`

				LEFT JOIN ktv_village vil ON app.`VillageID` = vil.`VillageID`
				LEFT JOIN ktv_subdistrict subd ON vil.`SubDistrictID` = subd.`SubDistrictID`
				LEFT JOIN ktv_district dis ON subd.`DistrictID` = dis.`DistrictID`
				LEFT JOIN ktv_province prov ON dis.`ProvinceID` = prov.`ProvinceID`

				LEFT JOIN ktv_farmer_group cpg ON app.`CPGid` = cpg.`FarmerGroupID`
				LEFT JOIN ktv_members far ON app.ApplicantID = far.ApplicantID
			WHERE
				s.`StatusCode` = 'active'
				AND sp.`StatusCode` = 'active'
				AND app.StatusCode = 'active'
				AND s.`IMSID` = ?
				AND sp.`ParticipantType` = 'Applicant Mobile'
			GROUP BY app.`ApplicantID`";
		$ListApplicantMobile = $this->db->query($sql, array($IMSID))->result_array();

		for ($i = 0; $i < count($ListApplicantMobile); $i++) {
			$DataInsert = array();

			//Get Data Socialization nya
			$sql = "SELECT
						s.`IMSSocID`
						, s.`EventStart`
						, s.`EventEnd`
						, sp.`ParticipateInSocializationStatus`
						, sp.`RecommendationStatus`
						, sp.`SelectionStatus`
					FROM
						ktv_ims_socializations s
						INNER JOIN ktv_ims_socialization_participants sp ON s.`IMSSocID` = sp.`IMSSocID`
						LEFT JOIN ktv_applicant_farmers app ON sp.`MobileUID` = app.`MobileUID`

						#Get Soc Terbaru
						INNER JOIN (
							SELECT
								SUBSTRING_INDEX(
									GROUP_CONCAT(s_s.`IMSSocID` ORDER BY `s_s`.EventStart DESC SEPARATOR ',')
									, ','
									, 1
								) AS IMSSocID
							FROM
								ktv_ims_socializations s_s
								INNER JOIN ktv_ims_socialization_participants s_sp ON s_s.`IMSSocID` = s_sp.`IMSSocID`
								LEFT JOIN ktv_applicant_farmers s_app ON s_sp.`MobileUID` = s_app.`MobileUID`
							WHERE
								s_s.`StatusCode` = 'active'
								AND s_sp.`StatusCode` = 'active'
								AND s_s.`IMSID` = ?
								AND s_app.ApplicantID = ?
						) AS lat_soc ON 1=1
							AND s.`IMSSocID` = lat_soc.`IMSSocID`
					WHERE
						s.`StatusCode` = 'active'
						AND sp.`StatusCode` = 'active'
						AND s.`IMSID` = ?
						AND app.`ApplicantID` = ?";
			$DataSoc = $this->db->query($sql, array($IMSID, $ListApplicantMobile[$i]['ApplicantID'], $IMSID, $ListApplicantMobile[$i]['ApplicantID']))->row_array();

			if($DataSoc['ParticipateInSocializationStatus'] == "" || $DataSoc['ParticipateInSocializationStatus'] == "0") $DataSoc['ParticipateInSocializationStatus'] = null;
			if($DataSoc['RecommendationStatus'] == "" || $DataSoc['RecommendationStatus'] == "0") $DataSoc['RecommendationStatus'] = null;
			if($DataSoc['SelectionStatus'] == "" || $DataSoc['SelectionStatus'] == "0") $DataSoc['SelectionStatus'] = null;

			$DataInsert['ObjID']                            = $ListApplicantMobile[$i]['ApplicantID'];
			$DataInsert['ObjType']                          = 'Applicant';
			$DataInsert['DestObjID']                        = $ListApplicantMobile[$i]['DestObjID'];
			$DataInsert['IMSID']                            = $IMSID;
			$DataInsert['IMSSocID']                         = $DataSoc['IMSSocID'];
			$DataInsert['Name']                             = $ListApplicantMobile[$i]['Name'];
			$DataInsert['Gender']                           = $ListApplicantMobile[$i]['Gender'];
			$DataInsert['Province']                         = $ListApplicantMobile[$i]['Province'];
			$DataInsert['District']                         = $ListApplicantMobile[$i]['District'];
			$DataInsert['SubDistrict']                      = $ListApplicantMobile[$i]['SubDistrict'];
			$DataInsert['Village']                          = $ListApplicantMobile[$i]['Village'];
			$DataInsert['FarmerGroup']                      = $ListApplicantMobile[$i]['FarmerGroup'];
			$DataInsert['EventStart']                       = $DataSoc['EventStart'];
			$DataInsert['EventEnd']                         = $DataSoc['EventEnd'];
			$DataInsert['ParticipateInSocializationStatus'] = $DataSoc['ParticipateInSocializationStatus'];
			$DataInsert['RecommendationStatus']             = $DataSoc['RecommendationStatus'];
			$DataInsert['SelectionStatus']                  = $DataSoc['SelectionStatus'];
			$DataInsert['DateGenerated']                    = date('Y-m-d H:i:s');
			$DataInsert['GeneratedBy']                      = $_SESSION['userid'];

			//Cek apakah sudah ada datanya (karena ada kemungkinan datanya sudah di approve)
			$CekDuplicateSocSel = $this->CekDuplicateSocSel($DataInsert['ObjID'],$DataInsert['ObjType'],$DataInsert['IMSID']);
			if($CekDuplicateSocSel == false){
				//Insert ke Tabel
				$this->db->insert('ktv_ims_soc_sel', $DataInsert);
			}
		}
		// ======================================== Applicant Mobile (End) =============================================//

		// ======================================== Existing Farmer (Begin) =============================================//
		$sql = "SELECT
				far.`MemberID` FarmerID
				, far.`MemberName` AS `Name`
				, CASE
					WHEN far.Gender='1' THEN 'Male'
					WHEN far.Gender='2' THEN 'Female'
				END AS Gender
				, prov.`Province`
				, dis.`District`
				, subd.`SubDistrict`
				, vil.`Village`
				, CONCAT(cpg.`FarmerGroupID`,' - ',cpg.`GroupName`) AS FarmerGroup
			FROM
				ktv_ims_socializations s
				LEFT JOIN ktv_ims_socialization_participants sp ON s.`IMSSocID` = sp.`IMSSocID`

				LEFT JOIN ktv_members far ON sp.`MobileUID` = far.`MemberID`

				LEFT JOIN ktv_village vil ON far.`VillageID` = vil.`VillageID`
				LEFT JOIN ktv_subdistrict subd ON vil.`SubDistrictID` = subd.`SubDistrictID`
				LEFT JOIN ktv_district dis ON subd.`DistrictID` = dis.`DistrictID`
				LEFT JOIN ktv_province prov ON dis.`ProvinceID` = prov.`ProvinceID`

				LEFT JOIN ktv_farmer_group cpg ON far.`FarmerGroupID` = cpg.`FarmerGroupID`
			WHERE
				s.`StatusCode` = 'active'
				AND sp.`StatusCode` = 'active'
				AND far.StatusCode = 'active'
				AND s.`IMSID` = ?
				AND sp.`ParticipantType` = 'Existing Farmer'
			GROUP BY far.`MemberID`";
		$ListExistingFarmer = $this->db->query($sql, array($IMSID))->result_array();

		for ($i = 0; $i < count($ListExistingFarmer); $i++) {
			$DataInsert = array();

			//Get Data Socialization nya
			$sql = "SELECT
					s.`IMSSocID`
					, s.`EventStart`
					, s.`EventEnd`
					, sp.`ParticipateInSocializationStatus`
					, sp.`RecommendationStatus`
					, sp.`SelectionStatus`
				FROM
					ktv_ims_socializations s
					INNER JOIN ktv_ims_socialization_participants sp ON s.`IMSSocID` = sp.`IMSSocID`
					LEFT JOIN ktv_members far ON sp.`MobileUID` = far.`MemberID`

					#Get Soc Terbaru
					INNER JOIN (
						SELECT
							SUBSTRING_INDEX(
								GROUP_CONCAT(s_s.`IMSSocID` ORDER BY `s_s`.EventStart DESC SEPARATOR ',')
								, ','
								, 1
							) AS IMSSocID
						FROM
							ktv_ims_socializations s_s
							INNER JOIN ktv_ims_socialization_participants s_sp ON s_s.`IMSSocID` = s_sp.`IMSSocID`
							LEFT JOIN ktv_members s_far ON s_sp.`MobileUID` = s_far.`MemberID`
						WHERE
							s_s.`StatusCode` = 'active'
							AND s_sp.`StatusCode` = 'active'
							AND s_s.`IMSID` = ?
							AND s_sp.`MobileUID` = ?
					) AS lat_soc ON 1=1
						AND s.`IMSSocID` = lat_soc.`IMSSocID`
				WHERE
					s.`StatusCode` = 'active'
					AND sp.`StatusCode` = 'active'
					AND s.`IMSID` = ?
					AND sp.`MobileUID` = ?";
			$DataSoc = $this->db->query($sql, array($IMSID, $ListExistingFarmer[$i]['FarmerID'], $IMSID, $ListExistingFarmer[$i]['FarmerID']))->row_array();

			if($DataSoc['ParticipateInSocializationStatus'] == "" || $DataSoc['ParticipateInSocializationStatus'] == "0") $DataSoc['ParticipateInSocializationStatus'] = null;
			if($DataSoc['RecommendationStatus'] == "" || $DataSoc['RecommendationStatus'] == "0") $DataSoc['RecommendationStatus'] = null;
			if($DataSoc['SelectionStatus'] == "" || $DataSoc['SelectionStatus'] == "0") $DataSoc['SelectionStatus'] = null;

			$DataInsert['ObjID']                            = $ListExistingFarmer[$i]['FarmerID'];
			$DataInsert['ObjType']                          = 'Existing Farmer';
			$DataInsert['DestObjID']                        = $ListExistingFarmer[$i]['FarmerID'];
			$DataInsert['IMSID']                            = $IMSID;
			$DataInsert['IMSSocID']                         = $DataSoc['IMSSocID'];
			$DataInsert['Name']                             = $ListExistingFarmer[$i]['Name'];
			$DataInsert['Gender']                           = $ListExistingFarmer[$i]['Gender'];
			$DataInsert['Province']                         = $ListExistingFarmer[$i]['Province'];
			$DataInsert['District']                         = $ListExistingFarmer[$i]['District'];
			$DataInsert['SubDistrict']                      = $ListExistingFarmer[$i]['SubDistrict'];
			$DataInsert['Village']                          = $ListExistingFarmer[$i]['Village'];
			$DataInsert['FarmerGroup']                      = $ListExistingFarmer[$i]['FarmerGroup'];
			$DataInsert['EventStart']                       = $DataSoc['EventStart'];
			$DataInsert['EventEnd']                         = $DataSoc['EventEnd'];
			$DataInsert['ParticipateInSocializationStatus'] = $DataSoc['ParticipateInSocializationStatus'];
			$DataInsert['RecommendationStatus']             = $DataSoc['RecommendationStatus'];
			$DataInsert['SelectionStatus']                  = $DataSoc['SelectionStatus'];
			$DataInsert['DateGenerated']                    = date('Y-m-d H:i:s');
			$DataInsert['GeneratedBy']                      = $_SESSION['userid'];

			//Cek apakah sudah ada datanya (karena ada kemungkinan datanya sudah di approve)
			$CekDuplicateSocSel = $this->CekDuplicateSocSel($DataInsert['ObjID'],$DataInsert['ObjType'],$DataInsert['IMSID']);
			if($CekDuplicateSocSel == false){
				//Insert ke Tabel
				$this->db->insert('ktv_ims_soc_sel', $DataInsert);
			}
		}
		// ======================================== Existing Farmer (End) =============================================//

		//Insert Log
		$sql="INSERT INTO `log_ims_acquisition` SET
			IMSID = ?,
			`ActType` = 'Generate Candidate for Socialization Selection',
			`Remark` = ?,
			`LogDate` = NOW(),
			`UserId` = ?";
		$p = array(
			$IMSID,
			$RemarkText,
			$_SESSION['userid']
		);
		$query = $this->db->query($sql,$p);

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			$results['success'] = false;
			$results['message'] = lang("Failed to generate Candidates for Socialization Selection");
		} else {
			$this->db->trans_commit();
			$results['success'] = true;
			$results['message'] = lang("Candidates for Socialization Selection Generated");
		}
		return $results;
	}

	public function acqProcessGenTrainingCandidate($IMSID,$RemarkText)
	{
		$this->db->trans_begin();

		//Ambil Setting Percentage Lolos
		$sql               = "SELECT SetValue FROM sys_setting WHERE SetKey = 'min_percent_training' LIMIT 1";
		$DataSetting       = $this->db->query($sql)->row_array();
		$PercentageSetting = (float) $DataSetting['SetValue'];

		//Hapus data candidate yang belum terapprove
		$sql   = "DELETE FROM ktv_ims_training_gap_coc WHERE IMSID = ? AND DateApproval IS NULL";
		$query = $this->db->query($sql, array($IMSID));

		$sql = "SELECT
				a.`IMSID`
				, a.`CpgBatchID`
			FROM
				ktv_ims a
			WHERE
				a.`IMSID` = ?
			LIMIT 1";
		$DataIMS = $this->db->query($sql, array($IMSID))->row_array();

		//Ambil FarmerID yang sudah Eligible dan belum di approve
		$sql = "SELECT
				GROUP_CONCAT(a.`FarmerID` SEPARATOR \"','\") AS FarmerID
			FROM
				ktv_ims_training_gap_coc a
			WHERE
				a.`IMSID` = ?
				AND EligibleStatus = '1'
				AND DateApproval IS NOT NULL";
		$DataEligible  = $this->db->query($sql, array($IMSID))->row_array();
		$FarmerIDNotIN = $DataEligible['FarmerID'];

		//Ambil FarmerID yang jadi Participant Training GAP & CoC pada Training IMS dan Batch sesuai IMSID
		$sql = "SELECT
				tpar.`FarmerID`
				, far.`ApplicantID`
				, far.`isCertified`
			FROM
				ktv_farmer_trainings t
				LEFT JOIN ktv_farmer_trainings_attendance tpar ON t.`FarmerTrainingID` = tpar.`FarmerTrainingID`

				LEFT JOIN `ktv_farmer_trainings_sub_topics` trasub ON 1=1
					AND t.FarmerTrainingID = trasub.FarmerTrainingID

				LEFT JOIN ktv_members far ON tpar.`FarmerID` = far.`MemberID`

				#Joinkan ke tabel soc_sel, hanya yg lolos seleksi, sudah jadi farmer, dan sudah diapprove yg boleh ikut
				INNER JOIN (
					SELECT
						sub_ss.`DestObjID` AS FarmerID
					FROM
						ktv_ims_soc_sel sub_ss
					WHERE
						sub_ss.`IMSID` = ?
						AND sub_ss.`DestObjID` IS NOT NULL AND sub_ss.`DestObjID` != '' AND sub_ss.`DestObjID` != '0'
						AND sub_ss.`SelectionStatus` = '1'
						AND sub_ss.`DateApproval` IS NOT NULL
				) AS tbl_ss ON tpar.`FarmerID` = tbl_ss.FarmerID
			WHERE
				t.`IMSID` = ?
				AND t.`CpgBatchID` = ?
				AND t.`StatusCode` = 'active'
				AND far.StatusCode = 'active'
				AND (
					( t.`CPGtrainingsID` = 1 ) #GAP
					OR
					( trasub.SubCpgTrainingsID = 53 ) #CoC
				)
				AND tpar.`FarmerID` NOT IN ('{$FarmerIDNotIN}')
			GROUP BY tpar.`FarmerID`";
		$DataPetani = $this->db->query($sql, array($DataIMS['IMSID'], $DataIMS['IMSID'], $DataIMS['CpgBatchID']))->result_array();

		if ($DataPetani[0]['FarmerID'] != "") {
			for ($i = 0; $i < count($DataPetani); $i++) {
				$DataInsert                = array();
				$DataInsert['IMSID']       = $DataIMS['IMSID'];
				$DataInsert['FarmerID']    = $DataPetani[$i]['FarmerID'];
				$DataInsert['ApplicantID'] = $DataPetani[$i]['ApplicantID'];
				//$DataInsert['EligibleStatus'] = '2';
				$DataInsert['DateGenerated'] = date('Y-m-d H:i:s');
				$DataInsert['GeneratedBy']   = $_SESSION['userid'];

				if ($DataPetani[$i]['isCertified'] == "1") {
					#Petani Tersertifikasi
					$DataInsert['TrainingReq'] = 'CoC';

					$sql = "SELECT
							t.`FarmerTrainingID`
							, 100 `Percentage`
							, t.`TrainingStatus`
						FROM
							ktv_farmer_trainings t
							INNER JOIN ktv_farmer_trainings_attendance tpar ON t.`FarmerTrainingID` = tpar.`FarmerTrainingID`
							INNER JOIN `ktv_farmer_trainings_sub_topics` trasub ON 1=1
								AND t.FarmerTrainingID = trasub.FarmerTrainingID
								-- AND trasub.SubCpgTrainingsID = 53 #CoC

							#CARI TRAINING NYA YG TERBARU!
							INNER JOIN (
								SELECT
									SUBSTRING_INDEX(
										GROUP_CONCAT(lat_tra.`FarmerTrainingID` ORDER BY `lat_tra`.TrainingStart DESC SEPARATOR ',')
										, ','
										, 1
									) AS FarmerTrainingID
								FROM
									ktv_farmer_trainings lat_tra

									INNER JOIN `ktv_farmer_trainings_sub_topics` lat_trasub ON 1=1
										AND lat_tra.FarmerTrainingID = lat_trasub.FarmerTrainingID
										-- AND lat_trasub.SubCpgTrainingsID = 53 #CoC

									LEFT JOIN ktv_farmer_trainings_attendance lat_tra_p ON 1=1
										AND lat_tra.`FarmerTrainingID` = lat_tra_p.`FarmerTrainingID`
								WHERE
									lat_tra.`IMSID` = {$DataIMS['IMSID']}
									-- AND lat_tra.`CPGtrainingsID` = 14
									AND lat_tra.`CpgBatchID` = {$DataIMS['CpgBatchID']}
									AND lat_tra_p.FarmerID = '{$DataPetani[$i]['FarmerID']}'
							) AS lat_tra ON 1=1
								AND t.FarmerTrainingID = lat_tra.FarmerTrainingID

						WHERE
							t.`StatusCode` = 'active'
							AND tpar.`FarmerID` = '{$DataPetani[$i]['FarmerID']}'
							AND t.`IMSID` = {$DataIMS['IMSID']}
							AND t.`CpgBatchID` = {$DataIMS['CpgBatchID']}
							-- AND t.`CPGtrainingsID` = 14
							";
					$DataCoC = $this->db->query($sql)->row_array();

					$TrainingReqPercentage = (float) $DataCoC['Percentage'];
					$TrainingRemark        = $DataCoC['FarmerTrainingID'];
					$TrainingStatus = $DataCoC['TrainingStatus'];
					if ($TrainingReqPercentage >= $PercentageSetting) {
						$EligibleStatus = '1';
					} else {
						$EligibleStatus = '2';
					}
				} else {
					#Petani Non Tersertifikasi
					$DataInsert['TrainingReq'] = 'GAP & CoC';

					$sql = "SELECT
						t.`FarmerTrainingID`
						, 100 `Percentage`
						, t.`TrainingStatus`
					FROM
						ktv_farmer_trainings t
						INNER JOIN ktv_farmer_trainings_attendance tpar ON t.`FarmerTrainingID` = tpar.`FarmerTrainingID`

						#CARI TRAINING NYA YG TERBARU!
						INNER JOIN (
							SELECT
								SUBSTRING_INDEX(
									GROUP_CONCAT(lat_tra.`FarmerTrainingID` ORDER BY `lat_tra`.TrainingStart DESC SEPARATOR ',')
									, ','
									, 1
								) AS FarmerTrainingID
								, lat_tra.`CpgBatchID`
							FROM
								ktv_farmer_trainings lat_tra
								LEFT JOIN ktv_farmer_trainings_attendance lat_tra_p ON 1=1
									AND lat_tra.`FarmerTrainingID` = lat_tra_p.`FarmerTrainingID`
							WHERE
								lat_tra.`IMSID` = {$DataIMS['IMSID']}
								AND lat_tra.`CPGtrainingsID` = 1
								AND lat_tra.`CpgBatchID` = {$DataIMS['CpgBatchID']}
								AND lat_tra_p.`FarmerID` = '{$DataPetani[$i]['FarmerID']}'
						) AS lat_tra ON 1=1
							AND t.FarmerTrainingID = lat_tra.FarmerTrainingID

					WHERE
						t.`StatusCode` = 'active'
						AND tpar.`FarmerID` = '{$DataPetani[$i]['FarmerID']}'
						AND t.`IMSID` = {$DataIMS['IMSID']}
						AND t.`CpgBatchID` = {$DataIMS['CpgBatchID']}
						-- AND t.`CPGtrainingsID` = 1 #GAP
					";
					$DataGAP = $this->db->query($sql)->row_array();

					$sql = "SELECT
							t.`FarmerTrainingID`
							, 100 `Percentage`
							, t.`TrainingStatus`
						FROM
							ktv_farmer_trainings t
							INNER JOIN ktv_farmer_trainings_attendance tpar ON t.`FarmerTrainingID` = tpar.`FarmerTrainingID`
							INNER JOIN `ktv_farmer_trainings_sub_topics` trasub ON 1=1
								AND t.FarmerTrainingID = trasub.FarmerTrainingID
								-- AND trasub.SubCpgTrainingsID = 53 #CoC

							#CARI TRAINING NYA YG TERBARU!
							INNER JOIN (
								SELECT
									SUBSTRING_INDEX(
										GROUP_CONCAT(lat_tra.`FarmerTrainingID` ORDER BY `lat_tra`.TrainingStart DESC SEPARATOR ',')
										, ','
										, 1
									) AS FarmerTrainingID
								FROM
								ktv_farmer_trainings lat_tra

									INNER JOIN `ktv_farmer_trainings_sub_topics` lat_trasub ON 1=1
										AND lat_tra.FarmerTrainingID = lat_trasub.FarmerTrainingID
										-- AND lat_trasub.SubCpgTrainingsID = 53 #CoC

									LEFT JOIN ktv_farmer_trainings_attendance lat_tra_p ON 1=1
										AND lat_tra.`FarmerTrainingID` = lat_tra_p.`FarmerTrainingID`
								WHERE
									lat_tra.`IMSID` = {$DataIMS['IMSID']}
									-- AND lat_tra.`CPGtrainingsID` = 14
									AND lat_tra.`CpgBatchID` = {$DataIMS['CpgBatchID']}
									AND lat_tra_p.`FarmerID` = '{$DataPetani[$i]['FarmerID']}'
							) AS lat_tra ON 1=1
								AND t.FarmerTrainingID = lat_tra.FarmerTrainingID

						WHERE
							t.`StatusCode` = 'active'
							AND tpar.`FarmerID` = '{$DataPetani[$i]['FarmerID']}'
							AND t.`IMSID` = {$DataIMS['IMSID']}
							AND t.`CpgBatchID` = {$DataIMS['CpgBatchID']}
							-- AND t.`CPGtrainingsID` = 14
							";
					$DataCoC = $this->db->query($sql)->row_array();

					$TrainingReqPercentage = (float) $DataGAP['Percentage'] + (float) $DataCoC['Percentage'];
					$TrainingReqPercentage = $TrainingReqPercentage / 2;
					$TrainingRemark        = $DataGAP['FarmerTrainingID'] . ',' . $DataCoC['FarmerTrainingID'];
					if ($TrainingReqPercentage >= $PercentageSetting) {
						$EligibleStatus = '1';
					} else {
						$EligibleStatus = '2';
					}

					if($DataGAP['TrainingStatus'] == "1" && $DataCoC['TrainingStatus'] == "1"){
						$TrainingStatus = "1";
					}else{
						$TrainingStatus = "2";
					}
				}

				$DataInsert['TrainingReqPercentage'] = $TrainingReqPercentage;
				$DataInsert['TrainingRemark']        = $TrainingRemark;
				$DataInsert['EligibleStatus']        = $EligibleStatus;
				$DataInsert['TrainingStatus']        = $TrainingStatus;

				//Insert ke Tabel
				$this->db->insert('ktv_ims_training_gap_coc', $DataInsert);
			}
		} else {
			$this->db->trans_rollback();
			$results['success'] = false;
			$results['message'] = lang("No Candidate Generated");
			return $results;
		}

		//log
		$sql="INSERT INTO `log_ims_acquisition` SET
			IMSID = ?,
			`ActType` = 'Generate Candidate for Approval (GAP & CoC)',
			`Remark` = ?,
			`LogDate` = NOW(),
			`UserId` = ?";
		$p = array(
			$IMSID,
			$RemarkText,
			$_SESSION['userid']
		);
		$query = $this->db->query($sql,$p);

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			$results['success'] = false;
			$results['message'] = lang("Failed to generate Candidates for Approval");
		} else {
			$this->db->trans_commit();
			$results['success'] = true;
			$results['message'] = lang("Candidates for Approval Generated");
		}
		return $results;
	}

	public function acqProcessToCandidate($IMSID, $FarmerID)
	{
		//Get Data Training
		$data         = $this->getAcqProGridTrainingInfoDetail($IMSID, $FarmerID);
		$dataTraining = $data['data'];

		//Get Setting
		$sql = "SELECT
				a.`SetValue`
			FROM
				sys_setting a
			WHERE
				a.`SetID` = '4'
			LIMIT 1";
		$query         = $this->db->query($sql);
		$data          = $query->row_array();
		$minPercentage = $data['SetValue'];

		$totalPercentage    = (int) $dataTraining[0]['AttendancePercentage'] + (int) $dataTraining[1]['AttendancePercentage'];
		$TrainingPercentage = $totalPercentage / 2;

		if ($TrainingPercentage >= $minPercentage) {
			//Lolos
			$StatusAudit       = 1;
			$NotEligibleReason = null;
		} else {
			//Tidak Lolos
			$StatusAudit       = 2;
			$NotEligibleReason = 'Not Fulfill Training Attendace Requirement';
		}

		//Cek apakah sudah ada data
		$sql = "SELECT
				a.`PreAFLID`
			FROM
				ktv_certification_pre_afl a
			WHERE
				a.`IMSID` = ? AND a.`FarmerID` = ?
			LIMIT 1
			";
		$query = $this->db->query($sql, array($IMSID, $FarmerID));
		$data  = $query->row_array();

		if ($data['PreAFLID'] == "") {
			//insert
			$sql = "INSERT INTO `ktv_certification_pre_afl` SET
				`FarmerID` = ?,
				`IMSID` = ?,
				`TrainingPercentage` = ?,
				`StatusAudit` = ?,
				`NotEligibleReason` = ?,
				`StatusCode` = 'active',
				`DateCreated` = NOW(),
				`CreatedBy` = 1
				";
			$p = array(
				$FarmerID,
				$IMSID,
				$TrainingPercentage,
				$StatusAudit,
				$NotEligibleReason,
			);
			$query = $this->db->query($sql, $p);
		} else {
			//update
			$sql = "UPDATE `ktv_certification_pre_afl` SET
					`TrainingPercentage` = ?,
					`StatusAudit` = ?,
					`NotEligibleReason` = ?,
					`DateUpdated` = NOW(),
					`LastModifiedBy` = 1
				WHERE
					`FarmerID` = ?
					AND `IMSID` = ?
				LIMIT 1";
			$p = array(
				$TrainingPercentage,
				$StatusAudit,
				$NotEligibleReason,
				$FarmerID,
				$IMSID,
			);
			$query = $this->db->query($sql, $p);
		}

		if ($query) {
			$result['success'] = true;
			$result['message'] = 'Process Saved';
		} else {
			$result['success'] = false;
			$result['message'] = 'Process Failed';
		}
		return $result;
	}

	public function approvalSociaFormOpenInfo($IMSID)
	{
		$arrReturn = array();

		$sql = "SELECT
				a.`SocStatus`
				, a.`SocApprovalRemark`
				, a.`SocApprovalDate`
				, (
					SELECT
						UserRealName
					FROM
						sys_user
					WHERE
						UserId = SocUserApprove
				) AS SocUserApprove
			FROM
				ktv_ims a
			WHERE
				a.`IMSID` = ?
			LIMIT 1";
		$query                  = $this->db->query($sql, array($IMSID));
		$dataIMS                = $query->row_array();
		$arrReturn['SocStatus'] = $dataIMS['SocStatus'];

		if ($dataIMS['SocStatus'] == "2") {
			//get info Nr of participant that passed socialization
			$sql = "SELECT
					COUNT(a.ApplicantID) AS BANYAKNYA
				FROM
					ktv_applicant_farmers a

					LEFT JOIN ktv_view_applicant_latest_socialization latest_soc ON 1=1
						AND a.ApplicantID = latest_soc.ApplicantID
					LEFT JOIN ktv_ims_socialization_participants par ON 1=1
						AND a.ApplicantID = par.ApplicantID
						AND par.IMSSocID = latest_soc.IMSSocID_latest
					LEFT JOIN ktv_ims_socializations soc ON 1=1
						AND latest_soc.IMSSocID_latest = soc.IMSSocID
				WHERE
					a.`StatusCode` = 'active'
					AND a.`IMSID` = ?
					AND par.ParticipateInSocializationStatus = '1'";
			$query = $this->db->query($sql, array($IMSID));
			$data  = $query->row_array();

			$arrReturn['ParPassSoc'] = $data['BANYAKNYA'];

		} else {
			$arrReturn['SocApprovalRemark'] = $dataIMS['SocApprovalRemark'];
			$arrReturn['SocApprovalDate']   = $dataIMS['SocApprovalDate'];
			$arrReturn['SocUserApprove']    = $dataIMS['SocUserApprove'];

			$sql = "SELECT
				COUNT(a.`ApplicantID`) AS BANYAKNYA
			FROM
				ktv_applicant_farmers a
			WHERE
				a.`StatusCode` = 'active'
				AND a.`IMSID` = ?
				AND a.`SocStatus` = '1'";
			$query = $this->db->query($sql, array($IMSID));
			$data  = $query->row_array();

			$arrReturn['ParApprovedSoc'] = $data['BANYAKNYA'];
		}

		return $arrReturn;
	}

	public function approvalSocia($IMSID, $SocApprovalRemark)
	{
		$this->db->trans_begin();

		$sql = "UPDATE `ktv_ims` SET
				SocStatus = '1',
				SocUserApprove = ?,
				SocApprovalDate = NOW(),
				SocApprovalRemark = ?
			WHERE
				IMSID = ?
			LIMIT 1";
		$p = array(
			$_SESSION['userid'],
			$SocApprovalRemark,
			$IMSID,
		);
		$query = $this->db->query($sql, $p);

		$sql = "SELECT
				a.`ApplicantID`
			FROM
				ktv_applicant_farmers a

				LEFT JOIN ktv_view_applicant_latest_socialization latest_soc ON 1=1
					AND a.ApplicantID = latest_soc.ApplicantID
				LEFT JOIN ktv_ims_socialization_participants par ON 1=1
					AND a.ApplicantID = par.ApplicantID
					AND par.IMSSocID = latest_soc.IMSSocID_latest
				LEFT JOIN ktv_ims_socializations soc ON 1=1
					AND latest_soc.IMSSocID_latest = soc.IMSSocID
			WHERE
				a.`StatusCode` = 'active'
				AND a.`IMSID` = ?
				AND par.ParticipateInSocializationStatus = '1'";
		$query         = $this->db->query($sql, array($IMSID));
		$dataApplicant = $query->result_array();

		for ($i = 0; $i < count($dataApplicant); $i++) {
			$sql = "UPDATE ktv_applicant_farmers a SET
					a.SocStatus = '1',
					a.SocUserApprove = ?,
					a.SocApprovalDate = NOW(),
					a.SocApprovalRemark = ?
				WHERE
					a.`ApplicantID` = ?
				LIMIT 1";
			$p = array(
				$_SESSION['userid'],
				$SocApprovalRemark,
				$dataApplicant[$i]['ApplicantID'],
			);
			$query = $this->db->query($sql, $p);
		}

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			$results['success'] = false;
		} else {
			$this->db->trans_commit();
			$results['success'] = true;
		}
		return $results;
	}

	public function approvalSelecFormOpenInfo($IMSID)
	{
		$arrReturn = array();

		//Ambil yang sudah lolos selection
		$sql = "SELECT
			COUNT(a.ObjID) AS BANYAKNYA
		FROM
			ktv_ims_soc_sel a
		WHERE
			a.IMSID = ?
			AND a.SelectionStatus = '1'
			AND a.DateApproval IS NULL
			AND a.DestObjID IS NOT NULL
			AND a.DestObjID != ''
			AND a.DestObjID != '0'
		";
		$Data                    = $this->db->query($sql, array($IMSID))->row_array();
		$arrReturn['ParPassSel'] = $Data['BANYAKNYA'];

		return $arrReturn;
	}

	public function approvalSelec($IMSID, $SelApprovalRemark)
	{
		$this->db->trans_begin();

		//Cari yang lolos
		$sql = "SELECT
			a.SocSelID
		FROM
			ktv_ims_soc_sel a
		WHERE
			a.IMSID = ?
			AND a.SelectionStatus = '1'
			AND a.DateApproval IS NULL
			AND a.DestObjID IS NOT NULL
			AND a.DestObjID != ''
			AND a.DestObjID != '0'
		";
		$DataList = $this->db->query($sql, array($IMSID))->result_array();

		for ($i = 0; $i < count($DataList); $i++) {
			//update balik ke ktv_ims_training_gap_coc
			$sql = "UPDATE ktv_ims_soc_sel a SET
					a.`ApprovalRemark` = ?,
					a.`ApprovalBy` = ?,
					a.`DateApproval` = NOW()
				WHERE
					a.`SocSelID` = ?
				LIMIT 1";
			$p = array(
				$SelApprovalRemark,
				$_SESSION['userid'],
				$DataList[$i]['SocSelID'],
			);
			$query = $this->db->query($sql, $p);
		}

		//log
		$sql="INSERT INTO `log_ims_acquisition` SET
			IMSID = ?,
			`ActType` = 'Approval in Socialization Selection',
			`Remark` = ?,
			`LogDate` = NOW(),
			`UserId` = ?";
		$p = array(
			$IMSID,
			'[Approved Participants Nr: '.count($DataList).'] '.$SelApprovalRemark,
			$_SESSION['userid']
		);
		$query = $this->db->query($sql,$p);

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			$results['success'] = false;
		} else {
			$this->db->trans_commit();
			$results['success'] = true;
		}
		return $results;
	}

	public function approvalTrainFormOpenInfo($IMSID)
	{
		$arrReturn = array();

		//Cari Banyaknya Farmer yg lolos
		$sql = "SELECT
				COUNT(a.`FarmerID`) AS BANYAKNYA
			FROM
				ktv_ims_training_gap_coc a
			WHERE
				a.`IMSID` = ?
				AND a.`EligibleStatus` = '1'
				AND a.`TrainingStatus` = '1' #Training sudah Complete
				AND a.`DateApproval` IS NULL
			ORDER BY a.`FarmerID` ASC";
		$Data                        = $this->db->query($sql, array($IMSID))->row_array();
		$arrReturn['CountTrainPass'] = $Data['BANYAKNYA'];

		/*
		$sql="SELECT
		a.`TrainStatus`
		, a.`TrainApprovalRemark`
		, a.`TrainApprovalDate`
		, (
		SELECT
		UserRealName
		FROM
		sys_user
		WHERE
		UserId = TrainUserApprove
		) AS TrainUserApprove
		FROM
		ktv_ims a
		WHERE
		a.`IMSID` = ?
		LIMIT 1";
		$query = $this->db->query($sql,array($IMSID));
		$dataIMS = $query->row_array();
		$arrReturn['TrainStatus'] = $dataIMS['TrainStatus'];

		if($dataIMS['TrainStatus'] == "2"){
		//get info Nr of participant that passed training
		$sql="SELECT
		COUNT(b.`FarmerID`) AS BANYAKNYA
		FROM
		ktv_ims a
		INNER JOIN `ktv_certification_pre_afl` b ON a.`IMSID` = b.`IMSID`
		WHERE
		a.`IMSID` = ?
		AND b.`StatusCode` = 'active'
		AND b.StatusAudit = '1'
		";
		$query = $this->db->query($sql, array($IMSID));
		$data = $query->row_array();

		$arrReturn['ParPassTrain'] = $data['BANYAKNYA'];
		}else{
		$arrReturn['TrainApprovalRemark'] = $dataIMS['TrainApprovalRemark'];
		$arrReturn['TrainApprovalDate'] = $dataIMS['TrainApprovalDate'];
		$arrReturn['TrainUserApprove'] = $dataIMS['TrainUserApprove'];

		$sql="SELECT
		COUNT(b.`FarmerID`) AS BANYAKNYA
		FROM
		ktv_ims a
		INNER JOIN `ktv_certification_pre_afl` b ON a.`IMSID` = b.`IMSID`
		WHERE
		a.`IMSID` = ?
		AND b.`StatusCode` = 'active'
		AND b.StatusAudit = '1'";
		$query = $this->db->query($sql, array($IMSID));
		$data = $query->row_array();

		$arrReturn['ParApprovedTrain'] = $data['BANYAKNYA'];
		}*/

		return $arrReturn;
	}

	public function SigningLockSocSelFormOpen($IMSID){
		$sql="SELECT
				a.`SigningLockSocSelBy`
				, (SELECT UserRealName FROM sys_user sub WHERE sub.UserId = a.`SigningLockSocSelBy` LIMIT 1) AS SigningLockSocSelByRealname
				, a.`SigningLockSocSelRemark`
				, IFNULL(a.`SigningLockSocSelDatetime`,NOW()) AS SigningLockSocSelDatetime
			FROM
				ktv_ims a
			WHERE
				a.`IMSID` = ?
			LIMIT 1";
		$DataSign = $this->db->query($sql,array($IMSID))->row_array();
		return $DataSign;
	}

	public function SigningLockSocSel($IMSID,$SigningLockSocSelRemark){
		$this->db->trans_begin();
		$datenow = date('Y-m-d H:i:s');

		$SigningRemark = "UserId: {$_SESSION['userid']}\nTimestamp: {$datenow}\nRemark: {$SigningLockSocSelRemark}";

		$sql="UPDATE ktv_ims_soc_sel a SET
				a.`SigningRemark` = ?
			WHERE
				a.`IMSID` = ?
				AND a.DateApproval IS NOT NULL
				AND a.`SelectionStatus` = '1'
				AND a.`DestObjID` IS NOT NULL
				AND a.`DestObjID` != ''
				AND a.`DestObjID` != '0'";
		$p = array(
			$SigningRemark,
			$IMSID
		);
		$query = $this->db->query($sql,$p);

		$sql="UPDATE ktv_ims a SET
				a.`SigningLockSocSelBy` = ?,
				a.`SigningLockSocSelDatetime` = NOW(),
				a.`SigningLockSocSelRemark` = ?
			WHERE
				a.`IMSID` = ?
			LIMIT 1";
		$p = array(
			$_SESSION['userid'],
			$SigningLockSocSelRemark,
			$IMSID
		);
		$query = $this->db->query($sql,$p);

		//Send ke pre_afl (Candidate IMS) ==================== (Begin)
		/*
		$sql="SELECT
			a.`DestObjID` AS FarmerID
			, a.`SelectionStatus`
			, a.SocSelID
		FROM
			ktv_ims_soc_sel a
		WHERE
			a.`IMSID` = ?
			AND a.`SelectionStatus` IN ('1','2')
			AND a.`ObjType` = 'Existing Certified Farmer'
			AND a.`DestObjID` IS NOT NULL
			AND a.`DestObjID` != ''
			AND a.`DestObjID` != '0'
		";
		$DataSocSel = $this->db->query($sql,array($IMSID))->result_array();

		if($DataSocSel[0]['FarmerID'] != ""){
			for ($i=0; $i < count($DataSocSel); $i++) {

				//update soc sel
				$sql="UPDATE ktv_ims_soc_sel a SET
						a.`SigningRemark` = ?
					WHERE
						a.`SocSelID` = ?
					";
				$p = array(
					$SigningRemark,
					$DataSocSel[$i]['SocSelID']
				);
				$query = $this->db->query($sql,$p);

				if($DataSocSel[$i]['SelectionStatus'] == "1"){
					$StatusComply = "1";
					$AuditRemark = null;
				}
				if($DataSocSel[$i]['SelectionStatus'] == "2"){
					$StatusComply = "2";
					$AuditRemark = lang('Not passed Selection');
				}

				//Cek datanya ada atau belum
				$sql="SELECT
						a.`PreAFLID` AS id
					FROM
						ktv_certification_pre_afl a
					WHERE
						a.`FarmerID` = ?
						AND a.`IMSID` = ?
					LIMIT 1";
				$DataCek = $this->db->query($sql,array($DataSocSel[$i]['FarmerID'],$IMSID))->row_array();

				if($DataCek['id'] != ""){
					//Update
					$sql="UPDATE ktv_certification_pre_afl a SET
							`StatusAudit` = 1,
							`NotEligibleReason` = NULL,
							StatusComply = ?,
							AuditRemark = ?,
							DateUpdated = NOW(),
							LastModifiedBy = ?
						WHERE
							a.`FarmerID` = ?
							AND a.`IMSID` = ?
						LIMIT 1";
					$p = array(
						$StatusComply,
						$AuditRemark,
						$_SESSION['userid'],
						$DataSocSel[$i]['FarmerID'],
						$IMSID
					);
					$query = $this->db->query($sql,$p);
				}else{
					//Insert
					$sql="INSERT INTO `ktv_certification_pre_afl` SET
						`FarmerID` = ?,
						`IMSID` = ?,
						`StatusAudit` = 1,
						`NotEligibleReason` = NULL,
						StatusComply = ?,
						AuditRemark = ?,
						`StatusCode` = 'active',
						`DateCreated` = NOW(),
						`CreatedBy` = '{$_SESSION['userid']}'
					";
					$p = array(
						$DataSocSel[$i]['FarmerID'],
						$IMSID,
						$StatusComply,
						$AuditRemark
					);
					$query = $this->db->query($sql,$p);
				}
			}
		}
		*/
		//Send ke pre_afl (Candidate IMS) ==================== (End)

		//log
		$sql="INSERT INTO `log_ims_acquisition` SET
			IMSID = ?,
			`ActType` = 'Signing Lock in Socialization Selection Participants',
			`Remark` = 'Signing Lock in Socialization Selection Participants',
			`LogDate` = NOW(),
			`UserId` = ?";
		$p = array(
			$IMSID,
			$_SESSION['userid']
		);
		$query = $this->db->query($sql,$p);

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			return false;
		} else {
			$this->db->trans_commit();
			return true;
		}
	}

	public function ProcessToCandidateFromSelection($IMSID,$RemarkText){
		$this->db->trans_begin();
		$datenow = date('Y-m-d H:i:s');

		$sql="SELECT
			a.`DestObjID` AS FarmerID
			, a.`SelectionStatus`
			, a.SocSelID
		FROM
			ktv_ims_soc_sel a
		WHERE
			a.`IMSID` = ?
			AND a.`SelectionStatus` IN ('1','2')
			AND a.`DestObjID` IS NOT NULL
			AND a.`DestObjID` != ''
			AND a.`DestObjID` != '0'
		";
		$DataSocSel = $this->db->query($sql,array($IMSID))->result_array();
		$DataProcess = count($DataSocSel);

		if($DataSocSel[0]['FarmerID'] != ""){
			for ($i=0; $i < count($DataSocSel); $i++) {
				$SigningRemark = "UserId: {$_SESSION['userid']}\nTimestamp: {$datenow}\nRemark: {$RemarkText}";
				//update soc sel
				$sql="UPDATE ktv_ims_soc_sel a SET
						a.`SigningRemark` = ?
					WHERE
						a.`SocSelID` = ?
					";
				$p = array(
					$SigningRemark,
					$DataSocSel[$i]['SocSelID']
				);
				$query = $this->db->query($sql,$p);

				if($DataSocSel[$i]['SelectionStatus'] == "1"){
					$StatusComply = "1";
					$AuditRemark = null;
				}
				if($DataSocSel[$i]['SelectionStatus'] == "2"){
					$StatusComply = "2";
					$AuditRemark = lang('Not passed Selection');
				}

				//Cek datanya ada atau belum
				$sql="SELECT
						a.`PreAFLID` AS id
					FROM
						ktv_certification_pre_afl a
					WHERE
						a.`FarmerID` = ?
						AND a.`IMSID` = ?
					LIMIT 1";
				$DataCek = $this->db->query($sql,array($DataSocSel[$i]['FarmerID'],$IMSID))->row_array();

				if($DataCek['id'] != ""){
					//Update
					$sql="UPDATE ktv_certification_pre_afl a SET
							`StatusAudit` = 1,
							`NotEligibleReason` = NULL,
							StatusComply = ?,
							AuditRemark = ?,
							DateUpdated = NOW(),
							LastModifiedBy = ?
						WHERE
							a.`FarmerID` = ?
							AND a.`IMSID` = ?
						LIMIT 1";
					$p = array(
						$StatusComply,
						$AuditRemark,
						$_SESSION['userid'],
						$DataSocSel[$i]['FarmerID'],
						$IMSID
					);
					$query = $this->db->query($sql,$p);
				}else{
					//Insert
					$sql="INSERT INTO `ktv_certification_pre_afl` SET
						`FarmerID` = ?,
						`IMSID` = ?,
						`StatusAudit` = 1,
						`NotEligibleReason` = NULL,
						StatusComply = ?,
						AuditRemark = ?,
						`StatusCode` = 'active',
						`DateCreated` = NOW(),
						`CreatedBy` = '{$_SESSION['userid']}'
					";
					$p = array(
						$DataSocSel[$i]['FarmerID'],
						$IMSID,
						$StatusComply,
						$AuditRemark
					);
					$query = $this->db->query($sql,$p);
				}
			}
		}

		//log
		$RemarkText = "[Process Data: $DataProcess] ".$RemarkText;
		$sql="INSERT INTO `log_ims_acquisition` SET
			IMSID = ?,
			`ActType` = 'Process to Candidate from Selection',
			`Remark` = ?,
			`LogDate` = NOW(),
			`UserId` = ?";
		$p = array(
			$IMSID,
			$RemarkText,
			$_SESSION['userid']
		);
		$query = $this->db->query($sql,$p);

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			$result['success'] = false;
			$result['message'] = lang('Process Failed');
		} else {
			$this->db->trans_commit();
			$result['success'] = true;
			$result['message'] = lang('Process Success').'<br>'.lang('Process Data').': '.$DataProcess;
		}
		return $result;
	}

	public function SigningLockGapCocFormOpen($IMSID){
		$sql="SELECT
				a.`SigningLockGapCocBy`
				, (SELECT UserRealName FROM sys_user sub WHERE sub.UserId = a.`SigningLockGapCocBy` LIMIT 1) AS SigningLockGapCocByRealname
				, a.`SigningLockGapCocRemark`
				, IFNULL(a.`SigningLockGapCocDatetime`,NOW()) AS SigningLockGapCocDatetime
			FROM
				ktv_ims a
			WHERE
				a.`IMSID` = ?
			LIMIT 1";
		$DataSign = $this->db->query($sql,array($IMSID))->row_array();
		return $DataSign;
	}

	public function SigningLockGapCoc($IMSID,$SigningLockGapCocRemark){
		$this->db->trans_begin();
		$datenow = date('Y-m-d H:i:s');
		$SigningRemark = "UserId: {$_SESSION['userid']}\nTimestamp: {$datenow}\nRemark: {$SigningLockGapCocRemark}";

		$sql="UPDATE ktv_ims_training_gap_coc a SET
				a.`SigningRemark` = ?
			WHERE
				a.`IMSID` = ?
			";
		$p = array(
			$SigningRemark,
			$IMSID
		);
		$query = $this->db->query($sql,$p);

		$sql="UPDATE ktv_ims a SET
				a.`SigningLockGapCocBy` = ?,
				a.`SigningLockGapCocDatetime` = NOW(),
				a.`SigningLockGapCocRemark` = ?
			WHERE
				a.`IMSID` = ?
			LIMIT 1";
		$p = array(
			$_SESSION['userid'],
			$SigningLockGapCocRemark,
			$IMSID
		);
		$query = $this->db->query($sql,$p);

		$sql="SELECT
				a.FarmerID
				, a.EligibleStatus
			FROM
				ktv_ims_training_gap_coc a
			WHERE
				a.IMSID = ?";
		$DataTrainingPar = $this->db->query($sql,array($IMSID))->result_array();

		if($DataTrainingPar[0]['FarmerID'] != ""){
			for ($i=0; $i < count($DataTrainingPar); $i++) {
				if($DataTrainingPar[$i]['EligibleStatus'] == "1"){
					$StatusComply = "1";
					$AuditRemark = null;
				}
				if($DataTrainingPar[$i]['EligibleStatus'] == "2"){
					$StatusComply = "2";
					$AuditRemark = lang('Not passed Training IMS');
				}

				//Cek datanya ada atau belum
				$sql="SELECT
						a.`PreAFLID` AS id
					FROM
						ktv_certification_pre_afl a
					WHERE
						a.`FarmerID` = ?
						AND a.`IMSID` = ?
					LIMIT 1";
				$DataCek = $this->db->query($sql,array($DataTrainingPar[$i]['FarmerID'],$IMSID))->row_array();

				if($DataCek['id'] != ""){
					//Update
					$sql="UPDATE ktv_certification_pre_afl a SET
							`StatusAudit` = 1,
							`NotEligibleReason` = NULL,
							StatusComply = ?,
							AuditRemark = ?,
							DateUpdated = NOW(),
							LastModifiedBy = ?
						WHERE
							a.`FarmerID` = ?
							AND a.`IMSID` = ?
						LIMIT 1";
					$p = array(
						$StatusComply,
						$AuditRemark,
						$_SESSION['userid'],
						$DataTrainingPar[$i]['FarmerID'],
						$IMSID
					);
					$query = $this->db->query($sql,$p);
				}else{
					//Insert
					$sql="INSERT INTO `ktv_certification_pre_afl` SET
						`FarmerID` = ?,
						`IMSID` = ?,
						`StatusAudit` = 1,
						`NotEligibleReason` = NULL,
						StatusComply = ?,
						AuditRemark = ?,
						`StatusCode` = 'active',
						`DateCreated` = NOW(),
						`CreatedBy` = '{$_SESSION['userid']}'
					";
					$p = array(
						$DataTrainingPar[$i]['FarmerID'],
						$IMSID,
						$StatusComply,
						$AuditRemark
					);
					$query = $this->db->query($sql,$p);
				}
			}
		}

		//log
		$sql="INSERT INTO `log_ims_acquisition` SET
			IMSID = ?,
			`ActType` = 'Signing Lock in GAP & CoC Participants',
			`Remark` = 'Signing Lock in GAP & CoC Participants',
			`LogDate` = NOW(),
			`UserId` = ?";
		$p = array(
			$IMSID,
			$_SESSION['userid']
		);
		$query = $this->db->query($sql,$p);

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			return false;
		} else {
			$this->db->trans_commit();
			return true;
		}
	}

	public function ProcessToCandidateFromTraining($IMSID,$RemarkText){
		$this->db->trans_begin();

		$sql="SELECT
				a.FarmerID
				, a.EligibleStatus
				, a.TrainingReqPercentage
			FROM
				ktv_ims_training_gap_coc a
			WHERE
				a.IMSID = ?
				AND a.TrainingStatus = '1' #Hanya Training yg sudah completed saja
			";
		$DataTrainingPar = $this->db->query($sql,array($IMSID))->result_array();
		$DataProcess = count($DataTrainingPar);

		if($DataTrainingPar[0]['FarmerID'] != ""){
			for ($i=0; $i < count($DataTrainingPar); $i++) {
				if($DataTrainingPar[$i]['EligibleStatus'] == "1"){
					$StatusComply = "1";
					$AuditRemark = null;
				}
				if($DataTrainingPar[$i]['EligibleStatus'] == "2"){
					$StatusComply = "2";
					$AuditRemark = lang('Not passed Training IMS');
				}

				//Cek datanya ada atau belum
				$sql="SELECT
						a.`PreAFLID` AS id
					FROM
						ktv_certification_pre_afl a
					WHERE
						a.`FarmerID` = ?
						AND a.`IMSID` = ?
					LIMIT 1";
				$DataCek = $this->db->query($sql,array($DataTrainingPar[$i]['FarmerID'],$IMSID))->row_array();

				if($DataCek['id'] != ""){
					//Update
					$sql="UPDATE ktv_certification_pre_afl a SET
							`StatusAudit` = 1,
							`NotEligibleReason` = NULL,
							StatusComply = ?,
							AuditRemark = ?,
							TrainingPercentage = ?,
							DateUpdated = NOW(),
							LastModifiedBy = ?
						WHERE
							a.`FarmerID` = ?
							AND a.`IMSID` = ?
						LIMIT 1";
					$p = array(
						$StatusComply,
						$AuditRemark,
						$DataTrainingPar[$i]['TrainingReqPercentage'],
						$_SESSION['userid'],
						$DataTrainingPar[$i]['FarmerID'],
						$IMSID
					);
					$query = $this->db->query($sql,$p);
				}else{
					//Insert
					$sql="INSERT INTO `ktv_certification_pre_afl` SET
						`FarmerID` = ?,
						`IMSID` = ?,
						`StatusAudit` = 1,
						`NotEligibleReason` = NULL,
						StatusComply = ?,
						AuditRemark = ?,
						TrainingPercentage = ?,
						`StatusCode` = 'active',
						`DateCreated` = NOW(),
						`CreatedBy` = '{$_SESSION['userid']}'
					";
					$p = array(
						$DataTrainingPar[$i]['FarmerID'],
						$IMSID,
						$StatusComply,
						$AuditRemark,
						$DataTrainingPar[$i]['TrainingReqPercentage']
					);
					$query = $this->db->query($sql,$p);
				}
			}
		}

		//log
		$RemarkText = "[Process Data: $DataProcess] ".$RemarkText;
		$sql="INSERT INTO `log_ims_acquisition` SET
			IMSID = ?,
			`ActType` = 'Process to Candidate from Training',
			`Remark` = ?,
			`LogDate` = NOW(),
			`UserId` = ?";
		$p = array(
			$IMSID,
			$RemarkText,
			$_SESSION['userid']
		);
		$query = $this->db->query($sql,$p);

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			$result['success'] = false;
			$result['message'] = lang('Process Failed');
		} else {
			$this->db->trans_commit();
			$result['success'] = true;
			$result['message'] = lang('Process Success').'<br>'.lang('Process Data').': '.$DataProcess;
		}
		return $result;
	}

	public function acqProcessToCandidateBulk($IMSID, $dbtransState = true)
	{
		if ($dbtransState == true) {
			$this->db->trans_begin();
		}

		//Get Setting
		$sql = "SELECT
				a.`SetValue`
			FROM
				sys_setting a
			WHERE
				a.`SetID` = '4'
			LIMIT 1";
		$query         = $this->db->query($sql);
		$data          = $query->row_array();
		$minPercentage = $data['SetValue'];

		//Get List semua FarmerID yang akan diproses
		$dataList = $this->getAcqProGridTraining($IMSID, null, null, null, null, null, 'php_code');
		for ($i = 0; $i < count($dataList); $i++) {
			$TrainingPercentage = (float) $dataList[$i]['PercentageAttendance'];
			if ($TrainingPercentage >= $minPercentage) {
				//Lolos
				$StatusAudit       = 1;
				$NotEligibleReason = null;
			} else {
				//Tidak Lolos
				$StatusAudit       = 2;
				$NotEligibleReason = 'Not Fulfill Training Attendace Requirement';
			}

			//Cek apakah sudah ada data
			$sql = "SELECT
					a.`PreAFLID`
				FROM
					ktv_certification_pre_afl a
				WHERE
					a.`IMSID` = ? AND a.`FarmerID` = ?
				LIMIT 1
				";
			$query = $this->db->query($sql, array($IMSID, $dataList[$i]['FarmerID']));
			$data  = $query->row_array();

			if ($data['PreAFLID'] == "") {
				//insert
				$sql = "INSERT INTO `ktv_certification_pre_afl` SET
					`FarmerID` = ?,
					`IMSID` = ?,
					`TrainingPercentage` = ?,
					`StatusAudit` = ?,
					`NotEligibleReason` = ?,
					`StatusCode` = 'active',
					`DateCreated` = NOW(),
					`CreatedBy` = 1
					";
				$p = array(
					$dataList[$i]['FarmerID'],
					$IMSID,
					$TrainingPercentage,
					$StatusAudit,
					$NotEligibleReason,
				);
				$query = $this->db->query($sql, $p);
			} else {
				//update
				$sql = "UPDATE `ktv_certification_pre_afl` SET
						`TrainingPercentage` = ?,
						`StatusAudit` = ?,
						`NotEligibleReason` = ?,
						`DateUpdated` = NOW(),
						`LastModifiedBy` = 1
					WHERE
						`FarmerID` = ?
						AND `IMSID` = ?
					LIMIT 1";
				$p = array(
					$TrainingPercentage,
					$StatusAudit,
					$NotEligibleReason,
					$dataList[$i]['FarmerID'],
					$IMSID,
				);
				$query = $this->db->query($sql, $p);
			}
		}

		if ($dbtransState == true) {
			if ($this->db->trans_status() === false) {
				$this->db->trans_rollback();

				$results['success'] = false;
				$results['message'] = 'Process Failed';
			} else {
				$this->db->trans_commit();

				$results['success'] = true;
				$results['message'] = 'Process Saved';
			}
			return $results;
		}
	}

	public function approvalTrain($IMSID, $TrainApprovalRemark)
	{
		$this->db->trans_begin();

		//Cari Banyaknya Farmer yg lolos
		$sql = "SELECT
				a.`FarmerID`
				, a.TrainingReqPercentage
			FROM
				ktv_ims_training_gap_coc a
			WHERE
				a.`IMSID` = ?
				AND a.`EligibleStatus` = '1'
				AND a.`TrainingStatus` = '1' #Training sudah Complete
				AND a.`DateApproval` IS NULL
			ORDER BY a.`FarmerID` ASC";
		$DataPetani = $this->db->query($sql, array($IMSID))->result_array();

		for ($i = 0; $i < count($DataPetani); $i++) {
			/*
			Kirim ke Candidate IMS di proses Signing Lock saja, bukan disini
			$sql = "INSERT INTO `ktv_certification_pre_afl` SET
				`FarmerID` = ?,
				`IMSID` = ?,
				`TrainingPercentage` = ?,
				`StatusAudit` = 1,
				`NotEligibleReason` = NULL,
				`StatusCode` = 'active',
				`DateCreated` = NOW(),
				`CreatedBy` = '{$_SESSION['userid']}'
				";
			$p = array(
				$DataPetani[$i]['FarmerID'],
				$IMSID,
				$DataPetani[$i]['TrainingReqPercentage'],
			);
			$query = $this->db->query($sql, $p);
			*/

			//update balik ke ktv_ims_training_gap_coc
			$sql = "UPDATE ktv_ims_training_gap_coc a SET
					a.`ApprovalRemark` = ?,
					a.`ApprovalBy` = ?,
					a.`DateApproval` = NOW()
				WHERE
					a.`IMSID` = ?
					AND a.`FarmerID` = ?
				LIMIT 1";
			$p = array(
				$TrainApprovalRemark,
				$_SESSION['userid'],
				$IMSID,
				$DataPetani[$i]['FarmerID'],
			);
			$query = $this->db->query($sql, $p);
		}

		//log
		$sql="INSERT INTO `log_ims_acquisition` SET
			IMSID = ?,
			`ActType` = 'Approval in GAP & CoC Training',
			`Remark` = ?,
			`LogDate` = NOW(),
			`UserId` = ?";
		$p = array(
			$IMSID,
			'[Approved Participants Nr: '.count($DataPetani).'] '.$TrainApprovalRemark,
			$_SESSION['userid']
		);
		$query = $this->db->query($sql,$p);

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			$results['success'] = false;
		} else {
			$this->db->trans_commit();
			$results['success'] = true;
		}
		return $results;
	}

	public function cekAcqTrainingApproval($IMSID)
	{
		$sql = "SELECT
				a.`TrainStatus`
			FROM
				ktv_ims a
			WHERE
				a.`IMSID` = ?
			LIMIT 1";
		$query = $this->db->query($sql, array($IMSID));
		return $query->row_array();
	}

	public function ExportExcelFarmerIdentificated($IMSID)
	{
		$sql = "SELECT
					a.ApplicantID
					, a.DisplayID
					, a.`Fullname` AS ApplicantName
					, CASE
						WHEN a.Gender='m' THEN 'Male'
						WHEN a.Gender='f' THEN 'Female'
					END AS Gender
					, dis.District
					, subd.SubDistrict
					, vil.Village
					, IF(cpg.GroupName IS NOT NULL,CONCAT(cpg.FarmerGroupID,' - ',cpg.GroupName),a.NewGroupName) AS FarmerGroup
					, CASE
						WHEN a.ActiveStatus = 'active' THEN 'Active'
						WHEN a.ActiveStatus = 'inactive' THEN 'Inactive'
					END AS ApplicantStatus
				FROM
					ktv_applicant_farmers a
					LEFT JOIN ktv_village vil ON a.VillageID = vil.VillageID
					LEFT JOIN ktv_subdistrict subd ON subd.SubDistrictID = vil.SubDistrictID
					LEFT JOIN ktv_district dis ON dis.DistrictID = subd.DistrictID
					LEFT JOIN ktv_farmer_group cpg ON a.CPGid = cpg.FarmerGroupID
				WHERE
					a.`StatusCode` = 'active'
					AND a.`IMSID` = ? ";
		$Q = $this->db->query($sql, array($IMSID))->result_array();
		return $Q;

	}
	public function ExportExcelFarmerSoc($IMSID)
	{
		$sql = "SELECT
				a.ApplicantID
				, a.DisplayID
				, a.`Fullname` AS ApplicantName
				, CASE
					WHEN a.Gender='m' THEN 'Male'
					WHEN a.Gender='f' THEN 'Female'
				END AS Gender
				, dis.District
				, subd.SubDistrict
				, vil.Village
				, IF(cpg.GroupName IS NOT NULL,CONCAT(cpg.FarmerGroupID,' - ',cpg.GroupName),a.NewGroupName) AS FarmerGroup
				, CASE
					WHEN a.ActiveStatus = 'active' THEN 'Active'
					WHEN a.ActiveStatus = 'inactive' THEN 'Inactive'
				END AS ApplicantStatus
				, IMSSocID_latest
				, CASE
					WHEN par.ParticipateInSocializationStatus = '1' THEN 'Yes'
					WHEN par.ParticipateInSocializationStatus = '2' THEN 'No'
					ELSE '-'
				END AS ParticipateInSocialization
				, soc.EventStart AS DateOfSocialization
				, soc.IMSSocID
			FROM
				ktv_applicant_farmers a
				LEFT JOIN ktv_village vil ON a.VillageID = vil.VillageID
				LEFT JOIN ktv_subdistrict subd ON subd.SubDistrictID = vil.SubDistrictID
				LEFT JOIN ktv_district dis ON dis.DistrictID = subd.DistrictID
				LEFT JOIN ktv_farmer_group cpg ON a.CPGid = cpg.FarmerGroupID

				LEFT JOIN ktv_view_applicant_latest_socialization latest_soc ON 1=1
					AND a.ApplicantID = latest_soc.ApplicantID
				LEFT JOIN ktv_ims_socialization_participants par ON 1=1
					AND a.ApplicantID = par.ApplicantID
					AND par.IMSSocID = latest_soc.IMSSocID_latest
				LEFT JOIN ktv_ims_socializations soc ON 1=1
					AND latest_soc.IMSSocID_latest = soc.IMSSocID
			WHERE
				a.`StatusCode` = 'active'
				AND a.`IMSID` = ?
				AND par.ParticipateInSocializationStatus = '1'
			";

		$Q = $this->db->query($sql, array($IMSID))->result_array();
		return $Q;
	}

	public function ExportExcelFarmerSelection($IMSID)
	{
		$sql = "SELECT
				a.ApplicantID
				, a.DisplayID
				, a.`Fullname` AS ApplicantName
				, CASE
					WHEN a.Gender='m' THEN 'Male'
					WHEN a.Gender='f' THEN 'Female'
				END AS Gender
				, dis.District
				, subd.SubDistrict
				, vil.Village
				, IF(cpg.GroupName IS NOT NULL,CONCAT(cpg.CPGid,' - ',cpg.GroupName),a.NewGroupName) AS FarmerGroup
				, CASE
					WHEN a.ActiveStatus = 'active' THEN 'Active'
					WHEN a.ActiveStatus = 'inactive' THEN 'Inactive'
				END AS ApplicantStatus
				, IMSSocID_latest
				, CASE
					WHEN par.ParticipateInSocializationStatus = '1' THEN 'Yes'
					WHEN par.ParticipateInSocializationStatus = '2' THEN 'No'
					ELSE '-'
				END AS ParticipateInSocialization
				, soc.EventStart AS DateOfSocialization
				, soc.IMSSocID
				, CASE
					WHEN par.RecommendationStatus = '1' THEN 'Yes'
					WHEN par.RecommendationStatus = '2' THEN 'No'
					ELSE '-'
				END AS Recommendation
				, CASE
					WHEN par.SelectionStatus = '1' THEN 'Yes'
					WHEN par.SelectionStatus = '2' THEN 'No'
					ELSE '-'
				END AS SelectionStatus
				, par.ParticipantID
			FROM
				ktv_applicant_farmers a
				LEFT JOIN ktv_village vil ON a.VillageID = vil.VillageID
				LEFT JOIN ktv_subdistrict subd ON subd.SubDistrictID = vil.SubDistrictID
				LEFT JOIN ktv_district dis ON dis.DistrictID = subd.DistrictID
				LEFT JOIN ktv_cpg cpg ON a.CPGid = cpg.CPGid

				LEFT JOIN ktv_view_applicant_latest_socialization latest_soc ON 1=1
					AND a.ApplicantID = latest_soc.ApplicantID
				LEFT JOIN ktv_ims_socialization_participants par ON 1=1
					AND a.ApplicantID = par.ApplicantID
					AND par.IMSSocID = latest_soc.IMSSocID_latest
				LEFT JOIN ktv_ims_socializations soc ON 1=1
					AND latest_soc.IMSSocID_latest = soc.IMSSocID
			WHERE
				a.`StatusCode` = 'active'
				AND a.`IMSID` = ?
				AND par.SelectionStatus = '1'
			";
		$Q = $this->db->query($sql, array($IMSID))->result_array();
		//echo $this->db->last_query();die;
		return $Q;
	}

	public function ExportExcelFarmerGapCOC($IMSID)
	{
		//get info Batch dari IMS
		$sqls = "SELECT
				a.`CpgBatchID`
			FROM
				ktv_ims a
			WHERE
				a.`IMSID` = ?
			LIMIT 1";
		$query = $this->db->query($sqls, array($IMSID));
		//echo $this->db->last_query();die;
		$dataIMS = $query->row_array();

		$sql = "SELECT
					FarmerID
					, ApplicantID
					, DisplayID
					, MemberName
					, Gender
					, District
					, SubDistrict
					, Village
					, FarmerGroup
					, FORMAT((SUM(PercentageAttendance) / 2),2) AS PercentageAttendance
					, EligibleStatus
				FROM
					(
					#Tabel Training GAP
					SELECT
						far.`MemberID`
						, app.ApplicantID
						, IFNULL(app.`DisplayID`,'-') AS DisplayID
						, far.`MemberName`
						, CASE
							WHEN far.Gender='1' THEN 'Male'
							WHEN far.Gender='2' THEN 'Female'
						END AS Gender
						, dis.District
						, subd.SubDistrict
						, vil.Village
						, CONCAT(cpg.FarmerGroupID,' - ',cpg.GroupName) AS FarmerGroup
						, IFNULL(trafar.Percentage,0) AS PercentageAttendance
						, CASE
							WHEN pafl.StatusAudit='1' THEN 'Yes'
							WHEN pafl.StatusAudit='2' THEN 'No'
							ELSE '-'
						END AS EligibleStatus
						, tra.`CPGtrainingsID`
					FROM
						ktv_ims a
						LEFT JOIN `ktv_cpg_batch_trainings` tra ON 1=1
							AND a.`CpgBatchID` = tra.`CpgBatchID`
							AND a.IMSID = tra.IMSID
							AND tra.`CPGtrainingsID` = 1 #GAP

						#CARI TRAINING NYA YG TERBARU!
						INNER JOIN (
							SELECT
								SUBSTRING_INDEX(
									GROUP_CONCAT(lat_tra.`CpgBatchTrainingID` ORDER BY `lat_tra`.TrainingStart DESC SEPARATOR ',')
									, ','
									, 1
								) AS CpgBatchTrainingID
								, lat_tra.`CpgBatchID`
							FROM
								ktv_cpg_batch_trainings lat_tra
							WHERE
								lat_tra.`IMSID` = ?
								AND lat_tra.`CPGtrainingsID` = 1
								AND lat_tra.`CpgBatchID` = ?
						) AS lat_tra ON 1=1
							AND tra.CpgBatchTrainingID = lat_tra.CpgBatchTrainingID

						LEFT JOIN ktv_cpg_batch_trainings_farmers trafar ON 1=1
							AND tra.CpgBatchTrainingID = trafar.`CpgBatchTrainingID`
						LEFT JOIN ktv_members far ON trafar.`FarmerID` = far.`MemberID`

						LEFT JOIN ktv_certification_pre_afl pafl ON 1=1
							AND a.`IMSID` = pafl.`IMSID`
							AND trafar.`FarmerID` = pafl.`FarmerID`
						LEFT JOIN `ktv_applicant_farmers` app ON 1=1
							AND far.`ApplicantID` = app.ApplicantID

						LEFT JOIN ktv_village vil ON far.VillageID = vil.VillageID
						LEFT JOIN ktv_subdistrict subd ON subd.SubDistrictID = vil.SubDistrictID
						LEFT JOIN ktv_district dis ON dis.DistrictID = subd.DistrictID
						LEFT JOIN ktv_farmer_group cpg ON far.FarmerGroupID = cpg.FarmerGroupID
					WHERE
						a.`IMSID` = ?
						AND tra.`StatusCode` = 'active'
						AND trafar.`StatusCode` = 'active'
					GROUP BY trafar.`FarmerID`

					UNION

					#Tabel Training CoC
					SELECT
						far.`MemberID`
						, app.ApplicantID
						, IFNULL(app.`DisplayID`,'-') AS DisplayID
						, far.`MemberName`
						, CASE
							WHEN far.Gender='1' THEN 'Male'
							WHEN far.Gender='2' THEN 'Female'
						END AS Gender
						, dis.District
						, subd.SubDistrict
						, vil.Village
						, CONCAT(cpg.FarmerGroupID,' - ',cpg.GroupName) AS FarmerGroup
						, IFNULL(trafar.Percentage,0) AS PercentageAttendance
						, CASE
							WHEN pafl.StatusAudit='1' THEN 'Yes'
							WHEN pafl.StatusAudit='2' THEN 'No'
							ELSE '-'
						END AS EligibleStatus
						, tra.`CPGtrainingsID`
					FROM
						ktv_ims a
						LEFT JOIN `ktv_cpg_batch_trainings` tra ON 1=1
							AND a.`CpgBatchID` = tra.`CpgBatchID`
							AND a.IMSID = tra.IMSID
							AND tra.`CPGtrainingsID` = 14 #GBP
						INNER JOIN `ktv_cpg_batch_trainings_sub_topics` trasub ON 1=1
							AND tra.CpgBatchTrainingID = trasub.CpgBatchTrainingID
							AND trasub.SubCpgTrainingsID = 53 #CoC

						#CARI TRAINING NYA YG TERBARU!
						INNER JOIN (
							SELECT
								SUBSTRING_INDEX(
									GROUP_CONCAT(lat_tra.`CpgBatchTrainingID` ORDER BY `lat_tra`.TrainingStart DESC SEPARATOR ',')
									, ','
									, 1
								) AS CpgBatchTrainingID
							FROM
								ktv_cpg_batch_trainings lat_tra

								INNER JOIN `ktv_cpg_batch_trainings_sub_topics` lat_trasub ON 1=1
									AND lat_tra.CpgBatchTrainingID = lat_trasub.CpgBatchTrainingID
									AND lat_trasub.SubCpgTrainingsID = 53 #CoC
							WHERE
								lat_tra.`IMSID` = ?
								AND lat_tra.`CPGtrainingsID` = 14
								AND lat_tra.`CpgBatchID` = ?
						) AS lat_tra ON 1=1
							AND tra.CpgBatchTrainingID = lat_tra.CpgBatchTrainingID

						LEFT JOIN ktv_cpg_batch_trainings_farmers trafar ON 1=1
							AND tra.CpgBatchTrainingID = trafar.`CpgBatchTrainingID`
						LEFT JOIN ktv_members far ON trafar.`FarmerID` = far.`MemberID`

						LEFT JOIN ktv_certification_pre_afl pafl ON 1=1
							AND a.`IMSID` = pafl.`IMSID`
							AND trafar.`FarmerID` = pafl.`FarmerID`
						LEFT JOIN `ktv_applicant_farmers` app ON 1=1
							AND far.`ApplicantID` = app.ApplicantID

						LEFT JOIN ktv_village vil ON far.VillageID = vil.VillageID
						LEFT JOIN ktv_subdistrict subd ON subd.SubDistrictID = vil.SubDistrictID
						LEFT JOIN ktv_district dis ON dis.DistrictID = subd.DistrictID
						LEFT JOIN ktv_farmer_group cpg ON far.FarmerGroupID = cpg.FarmerGroupID
					WHERE
						a.`IMSID` = ?
						AND tra.`StatusCode` = 'active'
						AND trafar.`StatusCode` = 'active'
					GROUP BY trafar.`FarmerID`
					) AS tbl_grup

				GROUP BY FarmerID
				ORDER BY FarmerID ASC";

		$Q = $this->db->query($sql, array($IMSID, @$dataIMS['CpgBatchID'], $IMSID, $IMSID, @$dataIMS['CpgBatchID'], $IMSID))->result_array();
		//echo $this->db->last_query();die;
		return $Q;
	}

	public function ExportHeaderrow($IMSID)
	{
		$SQL = "select CertEventName from ktv_ims
				where IMSID = ? ";
		$Q = $this->db->query($SQL, array($IMSID))->row();
		return $Q;
	}

	public function cekExistFarmer($FarmerID){
		$sql="SELECT MemberID FarmerID FROM ktv_members WHERE MemberID = ? AND StatusCode = 'active' LIMIT 1";
		$dataCek = $this->db->query($sql, array($FarmerID))->row_array();
		if ($dataCek['FarmerID'] != "") {
			return true;
		} else {
			return false;
		}
	}

	public function cekFarmerIMSAlreadyApproved($IMSID,$FarmerID){
		$sql="SELECT
				a.`DestObjID` AS FarmerID
			FROM
				ktv_ims_soc_sel a
			WHERE
				a.`IMSID` = ?
				AND a.`DestObjID` = ?
				AND a.`DateApproval` IS NOT NULL
				AND a.`DateApproval` != ''
				AND a.`DateApproval` != '0000-00-00'
			LIMIT 1";
		$DataCek = $this->db->query($sql, array($IMSID, $FarmerID))->row_array();
		if ($DataCek['FarmerID'] != "") {
			return true;
		} else {
			return false;
		}
	}

	public function cekExistFarmerIMS($IMSID, $FarmerID)
	{
		$sql = "SELECT
				a.`PreAFLID`
			FROM
				ktv_certification_pre_afl a
			LEFT JOIN
				ktv_members m on m.MemberID = a.FarmerID
			WHERE
				a.`IMSID` = ?
				AND (a.`FarmerID` = ? OR m.MemberDisplayID = ?)
			LIMIT 1";
		$dataCek = $this->db->query($sql, array($IMSID, $FarmerID, $FarmerID))->row_array();
		if ($dataCek['PreAFLID'] != "") {
			return true;
		} else {
			return false;
		}
	}

	public function cekExistUserByUsername($UserName)
	{
		$sql = "SELECT
				a.`UserName`
			FROM
				sys_user a
			WHERE
				a.`UserName` = ?
			LIMIT 1";
		$dataCek = $this->db->query($sql, array($UserName))->row_array();
		if ($dataCek['UserName'] != "") {
			return true;
		} else {
			return false;
		}
	}

	public function importFarmerMappingFA($IMSID, $dataExcel)
	{
		$this->db->trans_begin();

		//Hapus dl
		$sql   = "DELETE FROM ktv_fa_farmer_mapping WHERE IMSID = ?";
		$query = $this->db->query($sql, array($IMSID));

		for ($i = 0; $i < count($dataExcel); $i++) {
			$sqlcheck = "SELECT
					MemberID 
				FROM
					ktv_members 
				WHERE
					MemberID = ? 
					OR MemberDisplayID = ?
				ORDER BY 
					MemberID DESC
				LIMIT 1
			";
			$querycheck = $this->db->query($sqlcheck,array($dataExcel[$i][0],$dataExcel[$i][0]));
			$rowcheck = $querycheck->row_array();

			$sql = "INSERT INTO `ktv_fa_farmer_mapping` SET
				`FarmerID` = ?,
				`IMSID` = ?,
				`Username` = ?,
				`DateGenerated` = NOW()
			";
			$p = array(
				$rowcheck['MemberID'],
				$IMSID,
				$dataExcel[$i][1],
			);
			$query = $this->db->query($sql, $p);
		}

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			$results['success'] = false;
			$results['message'] = lang('Process Import Failed');
		} else {
			$this->db->trans_commit();
			$results['success'] = true;
			$results['message'] = lang('Process Import ' . count($dataExcel) . ' Data Farmer Success');
		}
		return $results;
	}

	public function cekExistUser($UserId)
	{
		$sql = "SELECT
				a.`UserId`
			FROM
				sys_user a
			WHERE
				a.`UserId` = ?
			LIMIT 1";
		$dataCek = $this->db->query($sql, array($UserId))->row_array();
		if ($dataCek['UserId'] != "") {
			return true;
		} else {
			return false;
		}
	}

	public function cekExistStaff($StaffID)
	{
		$sql = "SELECT
				a.`StaffID`
			FROM
				ktv_staffs a
			WHERE
				a.`StatusCode` = 'active'
				AND a.`StaffID` = ?
			LIMIT 1";
		$dataCek = $this->db->query($sql, array($StaffID))->row_array();
		if ($dataCek['StaffID'] != "") {
			return true;
		} else {
			return false;
		}
	}

	public function ImportCertFarmerIMS($IMSID, $DataExcel){
		$this->db->trans_begin();

		if($DataExcel[0][0] != ""){
			for ($i=0; $i < count($DataExcel); $i++) {
				$FarmerID = $DataExcel[$i][0];

				//Cek sudah ada di ims soc sel belum
				$sql="SELECT
						a.SocSelID
					FROM
						ktv_ims_soc_sel a
					WHERE
						a.`IMSID` = ?
						AND a.`DestObjID` = ?
					LIMIT 1";
				$DataCek = $this->db->query($sql,array($IMSID,$FarmerID))->row_array();
				if($DataCek['SocSelID'] != ""){
					//update
					$sql="UPDATE ktv_ims_soc_sel a SET
							ParticipateInSocializationStatus = '{$DataExcel[$i][1]}',
							RecommendationStatus = '{$DataExcel[$i][2]}',
							SelectionStatus = '{$DataExcel[$i][3]}',
							DateGenerated = NOW(),
							GeneratedBy = '{$_SESSION['userid']}'
						WHERE
							a.`SocSelID` = '{$DataCek['SocSelID']}'
							AND a.`DateApproval` IS NULL
						LIMIT 1";
					$query = $this->db->query($sql);
				}else{
					//insert
					$sql="INSERT INTO `ktv_ims_soc_sel` (`ObjID`,`ObjType`,`DestObjID`,`IMSID`,`Name`,`Gender`,`Province`,`District`,`SubDistrict`,`Village`,`FarmerGroup`,`ParticipateInSocializationStatus`,`RecommendationStatus`,`SelectionStatus`,`DateGenerated`,GeneratedBy)
						 SELECT
							'{$FarmerID}' AS ObjID
							, 'Existing Certified Farmer' AS ObjType
							, '{$FarmerID}' AS DestObjID
							, '{$IMSID}' AS IMSID
							, a.`MemberName` AS `Name`
							, CASE
								WHEN a.Gender='1' THEN 'Male'
								WHEN a.Gender='2' THEN 'Female'
							END AS Gender
							, prov.Province
							, dis.District
							, subd.SubDistrict
							, vil.Village
							, CONCAT(cpg.FarmerGroupID,' - ',cpg.GroupName) AS FarmerGroup
							, '{$DataExcel[$i][1]}' AS ParticipateInSocializationStatus
							, '{$DataExcel[$i][2]}' AS RecommendationStatus
							, '{$DataExcel[$i][3]}' AS SelectionStatus
							, NOW() AS DateGenerated
							, '{$_SESSION['userid']}' AS GeneratedBy
						FROM
							ktv_members a
							LEFT JOIN ktv_village vil ON vil.VillageID = a.VillageID
							LEFT JOIN ktv_subdistrict subd ON vil.SubDistrictID = subd.SubDistrictID
							LEFT JOIN ktv_district dis ON subd.DistrictID = dis.DistrictID
							LEFT JOIN ktv_province prov ON dis.ProvinceID = prov.ProvinceID
							LEFT JOIN ktv_farmer_group cpg ON a.FarmerGroupID = cpg.FarmerGroupID
						WHERE
							a.`MemberID` = '{$FarmerID}'";
					$query = $this->db->query($sql);
				}
			}
		}

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			$results['success'] = false;
			$results['message'] = lang('Process Import Failed');
		} else {
			$this->db->trans_commit();
			$results['success'] = true;
		}
		return $results;
	}

	public function importCandidateMappingFA($IMSID, $dataExcel)
	{
		$this->db->trans_begin();

		//Hapus dl
		$sql   = "DELETE FROM ktv_certification_pre_afl_target WHERE IMSID = ?";
		$query = $this->db->query($sql, array($IMSID));

		for ($i = 0; $i < count($dataExcel); $i++) {
			$sql = "INSERT INTO `ktv_certification_pre_afl_target` SET
				`FarmerID` = ?,
				`IMSID` = ?,
				`PICStaffID` = ?,
				`PICUserID` = ?,
				`Username` = ?,
				`FAName` = ?,
				`Remarks` = 'Import from Application',
				`StatusCode` = 'active',
				`DateCreated` = NOW(),
				`CreatedBy` = ?
			";
			$p = array(
				$dataExcel[$i][0],
				$IMSID,
				$dataExcel[$i][2],
				$dataExcel[$i][1],
				$dataExcel[$i][3],
				$dataExcel[$i][4],
				$_SESSION['userid'],
			);
			$query = $this->db->query($sql, $p);
		}

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			$results['success'] = false;
			$results['message'] = lang('Process Import Failed');
		} else {
			$this->db->trans_commit();
			$results['success'] = true;
			$results['message'] = lang('Process Import ' . count($dataExcel) .' Data Farmer Success');
		}
		return $results;
	}

	public function getComboFilterFA($IMSID)
	{
		$sql = "SELECT
				a.`UserId` AS id
				, b.`PersonNm` AS label
			FROM
				sys_user a
				LEFT JOIN ktv_persons b ON a.`UserId` = b.`UserID`
				LEFT JOIN ktv_staffs c ON b.`PersonID` = c.`PersonID`

				INNER JOIN `ktv_certification_pre_afl_target` tar ON a.`UserId` = tar.`PICUserID`
			WHERE
				tar.`StatusCode` = 'active'
				AND tar.`IMSID` = ?
			GROUP BY a.`UserId`
			ORDER BY b.`PersonNm`";
		$result['data'] = $this->db->query($sql, array($IMSID))->result_array();

		//Insert satu nilai di awal
		$InsertArray = array(
			0 => array(
				"id" => "not_map",
				"label" => lang('Not Map Farmers')
			)
		);
		$result['data'] = array_merge($InsertArray,$result['data']);

		return $result;
	}

	public function getComboFilterFAMapping($IMSID)
	{
		$sql = "SELECT
				a.`UserId` AS id
				, b.`PersonNm` AS label
			FROM
				sys_user a
				LEFT JOIN ktv_persons b ON a.`UserId` = b.`UserID`
				LEFT JOIN ktv_staffs c ON b.`PersonID` = c.`PersonID`

				INNER JOIN `ktv_fa_farmer_mapping` tar ON a.`UserName` = tar.`UserName`
			WHERE
				1=1
				AND tar.`IMSID` = ?
			GROUP BY a.`UserId`
			ORDER BY b.`PersonNm`";
		$result['data'] = $this->db->query($sql, array($IMSID))->result_array();

		//Insert satu nilai di awal
		$InsertArray = array(
			0 => array(
				"id" => "not_map",
				"label" => lang('Not Map Farmers')
			)
		);
		$result['data'] = array_merge($InsertArray,$result['data']);

		return $result;
	}

	public function GridMappingFA($IMSID, $UserId, $start, $limit, $sortingField, $sortingDir, $opsiCall)
	{
		if($UserId == "not_map"){
			if ($sortingField == "") {
				$sortingField = 'Farmer';
			}

			if ($sortingDir == "") {
				$sortingDir = 'ASC';
			}

			$sql = "SELECT
					tar.FAName FieldAgent
					, CONCAT( far.`MemberID`, ' - ', far.`MemberName` ) AS Farmer
					, CONCAT( fg.`FarmerGroupID`, ' - ', fg.`GroupName` ) AS FarmerGroup
					, prov.`Province`
					, dis.`District`
					, subd.`SubDistrict`
					, vil.`Village` 
				FROM
					ktv_certification_pre_afl pafl
				LEFT JOIN
					ktv_certification_pre_afl_target tar on tar.FarmerID = pafl.FarmerID AND tar.IMSID = pafl.IMSID
				LEFT JOIN
					ktv_members far on far.MemberID = pafl.FarmerID
				LEFT JOIN
					ktv_farmer_group fg on fg.FarmerGroupID = far.FarmerGroupID
				LEFT JOIN 
					ktv_village vil ON far.`VillageID` = vil.`VillageID`
				LEFT JOIN 
					ktv_subdistrict subd ON vil.`SubDistrictID` = subd.`SubDistrictID`
				LEFT JOIN 
					ktv_district dis ON subd.`DistrictID` = dis.`DistrictID`
				LEFT JOIN 
					ktv_province prov ON dis.`ProvinceID` = prov.`ProvinceID` 
				WHERE
					pafl.IMSID = ?
				AND
					tar.FarmerID IS NULL
				AND
					pafl.StatusCode = 'active'
				ORDER BY $sortingField $sortingDir";

			if ($opsiCall == 'no_limit') {
				return $this->db->query($sql, array($IMSID))->result_array();
			} else {
				$start          = (int) $start;
				$limit          = (int) $limit;
				$result['data'] = $this->db->query($sql . " LIMIT $start,$limit", array($IMSID))->result_array();

				$query           = $this->db->query('SELECT FOUND_ROWS() AS total');
				$result['total'] = $query->row()->total;

				return $result;
			}
		}else{
			if ($UserId == "" || $UserId == "null") {
				$UserId = '';
			}

			if ($sortingField == "") {
				$sortingField = 'PICUserID';
			}

			if ($sortingDir == "") {
				$sortingDir = 'ASC';
			}

			$sql = "SELECT
						tar.FAName FieldAgent
						, CONCAT( far.`MemberID`, ' - ', far.`MemberName` ) AS Farmer
						, CONCAT( fg.`FarmerGroupID`, ' - ', fg.`GroupName` ) AS FarmerGroup
						, prov.`Province`
						, dis.`District`
						, subd.`SubDistrict`
						, vil.`Village` 
					FROM
						ktv_certification_pre_afl pafl
					LEFT JOIN
						ktv_certification_pre_afl_target tar on tar.FarmerID = pafl.FarmerID AND tar.IMSID = pafl.IMSID
					LEFT JOIN
						ktv_members far on far.MemberID = pafl.FarmerID
					LEFT JOIN
						ktv_farmer_group fg on fg.FarmerGroupID = far.FarmerGroupID
					LEFT JOIN 
						ktv_village vil ON far.`VillageID` = vil.`VillageID`
					LEFT JOIN 
						ktv_subdistrict subd ON vil.`SubDistrictID` = subd.`SubDistrictID`
					LEFT JOIN 
						ktv_district dis ON subd.`DistrictID` = dis.`DistrictID`
					LEFT JOIN 
						ktv_province prov ON dis.`ProvinceID` = prov.`ProvinceID` 
					WHERE
						pafl.IMSID = ?
					AND
						pafl.StatusCode = 'active'
					AND
						tar.FarmerID IS NOT NULL
					AND ( (tar.`PICUserID` = ?) OR ('' = ?) )
				ORDER BY $sortingField $sortingDir
				";

			if ($opsiCall == 'no_limit') {
				return $this->db->query($sql, array($IMSID, $UserId, $UserId))->result_array();
			} else {
				$start          = (int) $start;
				$limit          = (int) $limit;
				$result['data'] = $this->db->query($sql . " LIMIT $start,$limit", array($IMSID, $UserId, $UserId))->result_array();

				$query           = $this->db->query('SELECT FOUND_ROWS() AS total');
				$result['total'] = $query->row()->total;

				return $result;
			}
		}
	}

	public function GetComboProvince()
	{
		$sql = "SELECT
				p.`ProvinceID` AS id
				, p.`Province` AS label
			FROM
				ktv_province p
			WHERE
				p.`active` = '1'
				AND p.`StatusCode` = 'active'
			ORDER BY p.`Province`";
		$result['data'] = $this->db->query($sql)->result_array();
		return $result;
	}

	public function GetComboDistrict($ProvinceID)
	{
		$sql = "SELECT
				d.`DistrictID` AS id
				, d.`District` AS label
			FROM
				ktv_province p
				INNER JOIN ktv_district d ON p.`ProvinceID` = d.`ProvinceID`
			WHERE
				p.`active` = '1'
				AND p.`StatusCode` = 'active'
				AND d.`active` = '1'
				AND d.`StatusCode` = 'active'
				AND d.`ProvinceID` = ?
			ORDER BY p.`Province`, d.`District`";
		$result['data'] = $this->db->query($sql, array($ProvinceID))->result_array();
		return $result;
	}

	public function GetGridImsDocumentsMaster($IMSMasterID){
		$data['text'] = '.';
		$data['expanded'] = true;
		$data['children'] = $this->GetTreeImsDocumentsMaster('0',$IMSMasterID);
		return $data;
	}

	public function GetTreeImsDocumentsMaster($ParentID,$IMSMasterID){
		$sql="SELECT
				a.`DocMasID`
				, a.`DocMasName` AS DocumentName
				, a.isCheck
				, a.Remark AS DocumentRemark
				, b.`StatusCheck` AS StatusUpload
				, IFNULL(b.`DateCheck`,'-') AS DateCheck
				, IFNULL(b.`Remark`,'-') AS Remark
				, a.TemplatePath
				, b.DocFile AS DocumentFilePath
				, b.StatusLock AS StatusLockRaw
				, CASE
					WHEN b.StatusLock = '1' THEN 'Yes'
					WHEN b.StatusLock = '2' THEN 'No'
				END AS StatusLock
			FROM
				ktv_ims_document_master a
				LEFT JOIN ktv_ims_document_master_data b ON 1=1
					AND a.`DocMasID` = b.`DocMasID`
					AND b.`IMSMasterID` = $IMSMasterID
			WHERE
				a.StatusCode = 'active'
				AND a.`ParentID` = $ParentID
			ORDER BY a.`OrderDisplay` ASC";
		$query = $this->db->query($sql);
		if ($query->num_rows()>0) {
			$data = $query->result_array();

			//Convert Newline
			for ($i=0; $i < count($data); $i++) {
				$data[$i]['DocumentName'] = lang($data[$i]['DocumentName']);
				$data[$i]['DocumentRemark'] = nl2br(lang($data[$i]['DocumentRemark']));
			}

			foreach ($data as $key => $value) {
				$children = $this->GetTreeImsDocumentsMaster($value['DocMasID'],$IMSMasterID);
				if (!empty($children)) {
					$data[$key]['children'] = $children;
				} else {
					$data[$key]['leaf'] = true;
				}
				$data[$key]['expanded'] = true;
			}
			return $data;
		}
		return false;
	}

	public function GetImsDocumentsMasterFormData($IMSMasterID,$DocMasID){
		$sql="SELECT
				a.`StatusCheck`
				, a.`DateCheck`
				, a.`Remark`
				, a.StatusLock
				, a.DocFile
			FROM
				`ktv_ims_document_master_data` a
			WHERE
				a.`IMSMasterID` = ?
				AND a.`DocMasID` = ?
			LIMIT 1";
		$data = $this->db->query($sql,array($IMSMasterID,$DocMasID))->row_array();

		$sql="SELECT
			a.DocMasName
		FROM
			ktv_ims_document_master a
		WHERE
			a.DocMasID = ?
		LIMIT 1
		";
		$dataDoc = $this->db->query($sql,array($DocMasID))->row_array();
		$data['DocumentName'] = $dataDoc['DocMasName'];

		//prep variable
		$dataRow = array();
		foreach ($data as $key => $value) {
			$keyNew = "Koltiva.view.IMS.WinFormImsMasterDocument-Form-".$key;
			$dataRow[$keyNew] = $value;
		}

		$dataRow['StatusLock'] = $data['StatusLock'];

		$return['success'] = true;
		$return['data']    = $dataRow;
		return $return;
	}

	public function UpdateImsDocumentsMaster($paramPost){
		$this->db->trans_start();

		$sql="INSERT INTO ktv_ims_document_master_data
				(
					IMSMasterID,
					DocMasID,
					StatusCheck,
					DateCheck,
					DocFile,
					Remark,
					StatusLock,
					CreatedBy,
					DateCreated
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
					NOW()
				)
			ON DUPLICATE KEY UPDATE
				StatusCheck = ?,
				DateCheck = ?,
				DocFile = ?,
				Remark = ?,
				StatusLock = ?,
				LastModifiedBy = ?,
				DateUpdated = NOW()";
		$p = array(
			$paramPost['IMSMasterID'],
			$paramPost['DocMasID'],
			$paramPost['StatusCheck'],
			$paramPost['DateCheck'],
			$paramPost['DocFile'],
			$paramPost['Remark'],
			$paramPost['StatusLock'],
			$_SESSION['userid'],
			$paramPost['StatusCheck'],
			$paramPost['DateCheck'],
			$paramPost['DocFile'],
			$paramPost['Remark'],
			$paramPost['StatusLock'],
			$_SESSION['userid']
		);
		$query = $this->db->query($sql,$p);

		$this->db->trans_complete();
		if ($this->db->trans_status()) {
			$results['success'] = true;
			$results['message'] = "Data saved";
		} else {
			$results['success'] = false;
			$results['message'] = "Failed to save data";
		}
		return $results;
	}

	public function GetDocumentMasterInfoTitle($IMSMasterID){
		$sql="SELECT
				COUNT(*) AS BANYAKNYA
			FROM
				ktv_ims_document_master_data a
			WHERE
				a.`IMSMasterID` = ?
				AND a.`StatusCheck` = '1'";
		$DataDoc = $this->db->query($sql,array($IMSMasterID))->row_array();

		$sql="SELECT
				COUNT(*) AS BANYAKNYA
			FROM
				ktv_ims_document_master a
			WHERE
				a.`isCheck` = '1'
				AND a.`StatusCode` = 'active'";
		$DataMaster = $this->db->query($sql)->row_array();

		$return['success'] = true;
		$return['DocUploaded'] = $DataDoc['BANYAKNYA'];
		$return['DocMaster'] = $DataMaster['BANYAKNYA'];
		return $return;
	}

	public function UnlockImsDocument($IMSMasterID,$DocMasID){
		$sql = "UPDATE `ktv_ims_document_master_data` a SET
					a.`StatusLock` = '2',
					a.`DateUpdated` = NOW(),
					a.`LastModifiedBy` = ?
				WHERE
					a.`DocMasID` = ?
					AND a.`IMSMasterID` = ?
				LIMIT 1";
		$p = array(
			$_SESSION['userid'],
			$DocMasID,
			$IMSMasterID
		);
		$query = $this->db->query($sql,$p);

		if($query == true){
			$return['success'] = true;
			$return['message'] = lang('Data Updated');
		}else{
			$return['success'] = false;
			$return['message'] = lang('Data update fail');
		}
		return $return;
	}

	public function GetDocumentEventInfoTitle($IMSID){
		$sql="SELECT
				COUNT(*) AS BANYAKNYA
			FROM
				ktv_ims_document_event_data a
			WHERE
				a.`IMSID` = ?
				AND a.`StatusCheck` = '1'";
		$DataDoc = $this->db->query($sql,array($IMSID))->row_array();

		$sql="SELECT
				COUNT(*) AS BANYAKNYA
			FROM
				ktv_ims_document_event a
			WHERE
				a.`isCheck` = '1'
				AND a.`StatusCode` = 'active'";
		$DataMaster = $this->db->query($sql)->row_array();

		$return['success'] = true;
		$return['DocUploaded'] = $DataDoc['BANYAKNYA'];
		$return['DocMaster'] = $DataMaster['BANYAKNYA'];
		return $return;
	}

	public function GetGridImsDocumentsEvent($IMSID){
		$data['text'] = '.';
		$data['expanded'] = true;
		$data['children'] = $this->GetTreeImsDocumentsEvent('0',$IMSID);
		return $data;
	}

	public function GetTreeImsDocumentsEvent($ParentID,$IMSID){
		$sql="SELECT
				a.`DocEveID`
				, a.`DocEveName` AS DocumentName
				, a.isCheck
				, a.Remark AS DocumentRemark
				, b.`StatusCheck` AS StatusUpload
				, IFNULL(b.`DateCheck`,'-') AS DateCheck
				, IFNULL(b.`Remark`,'-') AS Remark
				, a.TemplatePath
				, b.DocFile AS DocumentFilePath
			FROM
				ktv_ims_document_event a
				LEFT JOIN ktv_ims_document_event_data b ON 1=1
					AND a.`DocEveID` = b.`DocEveID`
					AND b.`IMSID` = $IMSID
			WHERE
				a.StatusCode = 'active'
				AND a.`ParentID` = $ParentID
			ORDER BY a.`OrderDisplay` ASC";
		$query = $this->db->query($sql);
		if ($query->num_rows()>0) {
			$data = $query->result_array();

			//Convert Newline
			for ($i=0; $i < count($data); $i++) {
				$data[$i]['DocumentName'] = lang($data[$i]['DocumentName']);
				$data[$i]['DocumentRemark'] = nl2br(lang($data[$i]['DocumentRemark']));
			}

			foreach ($data as $key => $value) {
				$children = $this->GetTreeImsDocumentsEvent($value['DocEveID'],$IMSID);
				if (!empty($children)) {
					$data[$key]['children'] = $children;
				} else {
					$data[$key]['leaf'] = true;
				}
				$data[$key]['expanded'] = true;
			}
			return $data;
		}
		return false;
	}

	public function GetImsDocumentsEventFormData($IMSID,$DocEveID){
		$sql="SELECT
				a.`StatusCheck`
				, a.`DateCheck`
				, a.`Remark`
				, a.DocFile
			FROM
				`ktv_ims_document_event_data` a
			WHERE
				a.`IMSID` = ?
				AND a.`DocEveID` = ?
			LIMIT 1";
		$data = $this->db->query($sql,array($IMSID,$DocEveID))->row_array();

		$sql="SELECT
			a.DocEveName
		FROM
			ktv_ims_document_event a
		WHERE
			a.DocEveID = ?
		LIMIT 1
		";
		$dataDoc = $this->db->query($sql,array($DocEveID))->row_array();
		$data['DocumentName'] = $dataDoc['DocEveName'];

		//prep variable
		$dataRow = array();
		foreach ($data as $key => $value) {
			$keyNew = "Koltiva.view.IMS.WinFormImsEventDocument-Form-".$key;
			$dataRow[$keyNew] = $value;
		}

		$return['success'] = true;
		$return['data']    = $dataRow;
		return $return;
	}

	public function UpdateImsDocumentsEvent($paramPost){
		$this->db->trans_start();

		$sql="INSERT INTO ktv_ims_document_event_data
				(
					IMSID,
					DocEveID,
					StatusCheck,
					DateCheck,
					DocFile,
					Remark,
					CreatedBy,
					DateCreated
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
					NOW()
				)
			ON DUPLICATE KEY UPDATE
				StatusCheck = ?,
				DateCheck = ?,
				DocFile = ?,
				Remark = ?,
				LastModifiedBy = ?,
				DateUpdated = NOW()";
		$p = array(
			$paramPost['IMSID'],
			$paramPost['DocEveID'],
			$paramPost['StatusCheck'],
			$paramPost['DateCheck'],
			$paramPost['DocFile'],
			$paramPost['Remark'],
			$_SESSION['userid'],
			$paramPost['StatusCheck'],
			$paramPost['DateCheck'],
			$paramPost['DocFile'],
			$paramPost['Remark'],
			$_SESSION['userid']
		);
		$query = $this->db->query($sql,$p);

		$this->db->trans_complete();
		if ($this->db->trans_status()) {
			$results['success'] = true;
			$results['message'] = "Data saved";
		} else {
			$results['success'] = false;
			$results['message'] = "Failed to save data";
		}
		return $results;
	}

	public function GetPreAflCertificationContractFormData($FarmerID,$IMSID){
		$sql="SELECT
				a.`CertContractFile`
				, a.FarmerID AS \"Koltiva.view.IMS.WinFormInputCertContract-Form-FarmerID\"
				, m.MemberDisplayID AS \"Koltiva.view.IMS.WinFormInputCertContract-Form-MemberDisplayID\"
				, a.IMSID AS \"Koltiva.view.IMS.WinFormInputCertContract-Form-IMSID\"
				, a.`CertContractSignDate` AS \"Koltiva.view.IMS.WinFormInputCertContract-Form-CertContractSignDate\"
				, a.`CertContractStatus` AS \"Koltiva.view.IMS.WinFormInputCertContract-Form-CertContractStatus\"
				, (SELECT MemberName FROM ktv_members WHERE MemberID = a.`FarmerID` LIMIT 1) AS \"Koltiva.view.IMS.WinFormInputCertContract-Form-FarmerName\"
			FROM
				`ktv_certification_pre_afl` a
			LEFT JOIN
				ktv_members m on m.MemberID = a.FarmerID
			WHERE
				a.`IMSID` = ?
				AND a.`FarmerID` = ?
			LIMIT 1";
		$query = $this->db->query($sql, array($IMSID,$FarmerID));
		$data = $query->row_array();

		if($this->awsfileupload->doesObjectExist($data['CertContractFile']) == true) {
            $data['CertContractFilePath'] = $data['CertContractFile'];
            $data['CertContractFile'] = $this->config->item('CTCDN')."/".$data['CertContractFile'];
        }else{
            $data['CertContractFilePath'] = '/files/ims_contract/'.$data["CertContractFile"];
            $data['CertContractFile'] = base_url().'/files/ims_contract/'.$data["CertContractFile"];
        }

		$return['success'] = true;
		$return['data'] = $data;
		return $return;
	}

	public function UpdateCertificationContract($IMSID, $FarmerID,$fileName){
		$sql="UPDATE ktv_certification_pre_afl a SET
				a.CertContractFile = ?
			WHERE
				a.`IMSID` = ?
				AND a.`FarmerID` = ?
			LIMIT 1";
		$p = array(
			$fileName,$IMSID,$FarmerID
		);
		return $this->db->query($sql,$p);
	}

	public function UpdateImsCertificationContract($paramPost){
		$sql="UPDATE ktv_certification_pre_afl a SET
				a.CertContractSignDate = ?,
				a.`CertContractStatus` = ?,
				a.`LastModifiedBy` = ?,
				a.`DateUpdated` = NOW()
			WHERE
				a.`FarmerID` = ?
				AND a.`IMSID` = ?
			LIMIT 1";
		$p = array(
			$paramPost['CertContractSignDate'],
			$paramPost['CertContractStatus'],
			$_SESSION['userid'],
			$paramPost['FarmerID'],
			$paramPost['IMSID']
		);
		$query = $this->db->query($sql,$p);

		if($query == true){
			$result['success'] = true;
			$result['message'] = lang('Data Saved');
		}else{
			$result['success'] = false;
			$result['message'] = "Failed to save data";
		}
		return $result;
	}

	public function DataImportCertFarmer($IMSID){
		$sql="SELECT
				a.`DestObjID` AS FarmerID
				, a.`ParticipateInSocializationStatus`
				, a.`RecommendationStatus`
				, a.`SelectionStatus`
			FROM
				`ktv_ims_soc_sel` a
			WHERE
				a.`IMSID` = ?
				AND a.`DateApproval` IS NULL
				AND a.`ObjType` = 'Existing Certified Farmer'
			ORDER BY a.`DestObjID` ASC";
		return $this->db->query($sql,array($IMSID))->result_array();
	}

	public function GetRangeTglProgressFA($IMSID){
		$sql="SELECT
				DISTINCT a.`DateCollection`
			FROM
				ktv_ims_summary_fa_progress a
			WHERE
				a.`IMSID` = ?
				AND a.CollectionType = 'Farmer Updated'
			ORDER BY a.`DateCollection` ASC";
		return $this->db->query($sql,array($IMSID))->result_array();
	}

	public function GetDataListProgressFA($IMSID){
		$ArrReturn = array();

		$sql="SELECT
				a.`PICUserID`
				, b.`UserRealName`
			FROM
				ktv_ims_summary_fa_progress a
				LEFT JOIN sys_user b ON a.`PICUserID` = b.`UserId`
			WHERE
				a.`IMSID` = ?
				AND a.CollectionType = 'Farmer Updated'
			GROUP BY a.`PICUserID`
			ORDER BY b.`UserRealName` ASC";
		$DataFA = $this->db->query($sql,array($IMSID))->result_array();

		for ($i=0; $i < count($DataFA); $i++) {
			//Get Data Count
			$sql="SELECT
					a.`DateCollection`
					, a.`DataCount`
				FROM
					ktv_ims_summary_fa_progress a
				WHERE
					a.`IMSID` = ?
					AND a.`PICUserID` = ?
					AND a.CollectionType = 'Farmer Updated'
				ORDER BY a.`DateCollection` ASC";
			$DataCount = $this->db->query($sql,array($IMSID,$DataFA[$i]['PICUserID']))->result_array();

			$ArrReturn[$i]['PICUserID'] = $DataFA[$i]['PICUserID'];
			$ArrReturn[$i]['FA'] = $DataFA[$i]['UserRealName'];
			$ArrReturn[$i]['DataCount'] = array();

			for ($j=0; $j < count($DataCount); $j++) {
				$ArrReturn[$i]['DataCount'][$DataCount[$j]['DateCollection']] = $DataCount[$j]['DataCount'];
			}
		}

		return $ArrReturn;
	}

	public function GetDataListFarmerSelectionParticipateInSocialization($IMSID){
		$sql="SELECT
				CASE
					WHEN ObjType = 'Applicant' THEN (
						SELECT DisplayID FROM ktv_applicant_farmers WHERE ApplicantID = a.ObjID LIMIT 1
					)
					WHEN ObjType = 'Existing Farmer' THEN a.ObjID
					WHEN ObjType = 'Existing Certified Farmer' THEN a.ObjID
				END DisplayID
				, IFNULL(a.DestObjID,'-') AS DestObjID
				, a.Name
				, a.Gender
				, a.Province
				, a.District
				, a.SubDistrict
				, a.Village
				, a.FarmerGroup
				, CASE
					WHEN a.RecommendationStatus = '1' THEN 'Yes'
					WHEN a.RecommendationStatus = '2' THEN 'No'
					ELSE '-'
				END AS Recommendation
				, CASE
					WHEN a.ParticipateInSocializationStatus = '1' THEN 'Yes'
					WHEN a.ParticipateInSocializationStatus = '2' THEN 'No'
					ELSE '-'
				END AS ParticipateInSocialization
				, CASE
					WHEN a.SelectionStatus = '1' THEN 'Yes'
					WHEN a.SelectionStatus = '2' THEN 'No'
					ELSE '-'
				END AS SelectionStatus
				, a.DateGenerated
				, IF(a.DateApproval IS NOT NULL,'Yes','No') AS HasBeenApproved
				, a.DateApproval
				, a.IMSSocID
				, a.ObjType AS ParticipantType
				, IFNULL(b.EventName,'-') AS EventName
				, IFNULL(b.EventStart,'-') AS EventStart
				, IFNULL(b.EventEnd,'-') AS EventEnd
			FROM
				`ktv_ims_soc_sel` a
				LEFT JOIN `ktv_ims_socializations` b ON a.IMSSocID = b.IMSSocID
			WHERE
				a.`IMSID` = ?
				AND a.ParticipateInSocializationStatus = '1'";
		return $this->db->query($sql,array($IMSID))->result_array();
	}

	public function ExportImsFarmerTargetToDHIS($IMSID,$UserID){
		$IMSID = (int) $IMSID;
		$UserID = (int) $UserID;

		//Connect PostGre PDO ============================= (Begin)
		$ConnPg = GetPostGreConn();
		if($ConnPg == false){
			$return['success'] = false;
			$return['message'] = lang('Cannot connect to PostGre Server');
			return $return;
		}
		//Connect PostGre PDO ============================= (End)

		//Delete dl datanya
		$query_pg = $ConnPg->prepare("DELETE FROM ims_fa_farmer WHERE useridfa=? AND eventstatus=1");
		$query_pg->execute(array($IMSID,$UserID));

		//Ambil List Data
		$sql = "SELECT
					a.`FarmerID`
					, b.`UserName` AS 'username'
				FROM
					`ktv_certification_pre_afl_target` a
					INNER JOIN sys_user b ON a.`PICUserID` = b.`UserId`
				WHERE
					a.`StatusCode` = 'active'
					AND a.`IMSID`= ?
					AND a.`PICUserID` = ?
				ORDER BY a.`FarmerID`";
		$DataList = $this->db->query($sql, array($IMSID,$UserID))->result_array();

		//Susun Values insert
		$ArrInsert = array();
		if(isset($DataList[0]['FarmerID'])){
			$CountInserted = 0;
			$ArrInsertValue = array();

			for ($i=0; $i < count($DataList); $i++) {
				//$ArrInsert[] = "({$IMSID},{$DataList[$i]['FarmerID']},{$UserID})";
				$ArrInsert[] = "(?,?,?,?)";
				$ArrInsertValue[] = $IMSID;
				$ArrInsertValue[] = $DataList[$i]['FarmerID'];
				$ArrInsertValue[] = $UserID;
				$ArrInsertValue[] = $DataList[$i]['username'];
				$ArrInsertValue[] = 1;
			}
			$SqlInsertValue = implode(",",$ArrInsert);
			$query_pg = $ConnPg->prepare("insert into ims_fa_farmer (imsid,farmerid,useridfa,username,eventstatus) values ".$SqlInsertValue);
			$query_pg->execute($ArrInsertValue);
			$CountInserted = $query_pg->rowCount();

			$return['success'] = true;
			if($CountInserted > 0){
				$return['message'] = $CountInserted." ".lang('datas inserted to DHIS');
			}else{
				$return['message'] = lang('no data inserted to DHIS');
			}
		}else{
			$return['success'] = false;
			$return['message'] = lang('No farmer target for this FA');
		}

		return $return;
	}

	public function GetIcsFarmerVerifiedFormData($IMSID,$FarmerID){
		$return = array();

		$sql = "SELECT
					a.`IMSID` AS 'Koltiva.view.IMS.WinFormICSVerifyFarmer-Form-IMSID'
					, far.`FarmerID` AS 'Koltiva.view.IMS.WinFormICSVerifyFarmer-Form-FarmerID'
					, far.`FarmerName` AS 'Koltiva.view.IMS.WinFormICSVerifyFarmer-Form-FarmerName'
					, a.`CertStatusVerified` AS 'Koltiva.view.IMS.WinFormICSVerifyFarmer-Form-CertStatusVerified'
					, a.CertStatusVerified
					, (SELECT su.UserRealName FROM sys_user su WHERE su.UserId = a.`CertStatusVerifiedChangeBy` LIMIT 1) AS 'Koltiva.view.IMS.WinFormICSVerifyFarmer-Form-CertStatusVerifiedChangeBy'
					, a.`CertStatusVerifiedComment` AS 'Koltiva.view.IMS.WinFormICSVerifyFarmer-Form-CertStatusVerifiedComment'
				FROM
					`ktv_certification_afl_farmer` a
					LEFT JOIN ktv_members far ON a.`FarmerID` = far.`FarmerID`
				WHERE
					a.`IMSID` = ?
					AND a.`FarmerID` = ?
				LIMIT 1";
		$data = $this->db->query($sql,array($IMSID,$FarmerID))->row_array();

		//Cek apakah survey mandatory sudah ada (survey garden all, survey postharvest, dan audit log)
		$sql = "SELECT
					b.`FarmerID`
					, b.`GardenNr`
					, b.`SurveyNr`
					, gar.`FarmerID` AS SurGarden
					, ph.`FarmerID` AS SurPh
					, audit_log.FarmerID AS SurAuditLog
				FROM
					ktv_certification_pre_afl a
					INNER JOIN ktv_certification_pre_afl_garden b ON 1=1
						AND a.FarmerID = b.FarmerID
						AND a.IMSID = b.IMSID

					LEFT JOIN ktv_ims ims ON a.IMSID = ims.IMSID
					LEFT JOIN ktv_certification_holders hold ON ims.CertHolderID = hold.CertHolderID

					INNER JOIN ktv_members far ON a.`FarmerID` = far.`FarmerID`

					LEFT JOIN `ktv_members_post_harvest` ph ON 1=1
						AND b.`FarmerID` = ph.`FarmerID`
						AND b.`SurveyNr` = ph.`SurveyNr`

					LEFT JOIN ktv_members_garden gar ON 1=1
						AND b.FarmerID = gar.MemberID
						AND b.GardenNr = gar.PlotNr
						AND b.SurveyNr = gar.SurveyNr
						AND b.FarmerID = ?

					LEFT JOIN (
						SELECT
							au.`FarmerID`,
							au.GardenNr,
							au.SurveyNr,
							au.ICSDate,
							au.StatusAudit,
							au.Certification,
							au.MasukHutanLindung
						FROM
							(SELECT
								FarmerID,
								GardenNr,
								SurveyNr,
								Certification,
								MAX(ICSDate) ICSDate
							FROM
								ktv_certification_audit_log
							WHERE Certification != 0
								AND GardenNr != 0
							GROUP BY FarmerID,
								GardenNr,
								SurveyNr,
								Certification) dt
							INNER JOIN ktv_certification_audit_log au
								ON dt.FarmerID = au.FarmerID
								AND dt.GardenNr = au.GardenNr
								AND dt.SurveyNr = au.SurveyNr
								AND dt.Certification = au.Certification
								AND dt.ICSDate = au.ICSDate
						WHERE 1=1
							AND au.FarmerID = ?
					) AS audit_log ON 1=1
						AND a.FarmerID = audit_log.FarmerID
						AND gar.GardenNr = audit_log.GardenNr
						AND gar.SurveyNr = audit_log.SurveyNr
						AND hold.CertProgID = audit_log.Certification

				WHERE 1=1
					AND a.`IMSID` = ?
					AND a.FarmerID = ?
					AND a.StatusAudit = '1' #Pre AFL Farmer Eligible
					AND a.StatusCode = 'active'
				GROUP BY a.FarmerID";
		$DataCekSur = $this->db->query($sql,array($FarmerID,$FarmerID,$IMSID,$FarmerID))->result_array();

		$data['CekSur'] = true;
		$data['CekSurMessage'] = "";
		$ArrSurTakAda = array();
		$CekSurGarden = true;
		$CekSurPh = true;
		$CekSurAuditLog = true;
		for ($i=0; $i < count($DataCekSur); $i++) {
			if($DataCekSur[$i]['SurGarden'] == ""){
				$data['CekSur'] = false;
				$CekSurGarden = false;
			}

			if($DataCekSur[$i]['SurPh'] == ""){
				$data['CekSur'] = false;
				$CekSurPh = false;
			}

			if($DataCekSur[$i]['SurAuditLog'] == ""){
				$data['CekSur'] = false;
				$CekSurAuditLog = false;
			}
		}
		if($data['CekSur'] == false){
			if($CekSurGarden == false){
				$ArrSurTakAda[] = 'Garden';
			}
			if($CekSurPh == false){
				$ArrSurTakAda[] = 'Post Harvest';
			}
			if($CekSurAuditLog == false){
				$ArrSurTakAda[] = 'Audit Log';
			}
			$ImpSurTakAda = implode(", ",$ArrSurTakAda);

			$data['CekSurMessage'] = lang('Survey data not complete yet').' ('.$ImpSurTakAda.')';
		}

		$return['data'] = $data;
		$return['success'] = true;
		return $return;
	}

	public function UpdateAflStatusFarmerVerified($ParamPost){
		$sql = "UPDATE `ktv_certification_afl_farmer` a SET
					a.`CertStatusVerified` = ?,
					a.`CertStatusVerifiedComment` = ?,
					a.`CertStatusVerifiedChangeBy` = ?
				WHERE
					a.`IMSID` = ?
					AND a.`FarmerID` = ?
				LIMIT 1";
		$p = array(
			$ParamPost['CertStatusVerified'],
			$ParamPost['CertStatusVerifiedComment'],
			$_SESSION['userid'],
			$ParamPost['IMSID'],
			$ParamPost['FarmerID']
		);
		$query = $this->db->query($sql,$p);

		if($query == true){
			$return['success'] = true;
			$return['message'] = lang('Data Saved');
		}else{
			$return['success'] = false;
			$return['message'] = lang('Failed to save data');
		}
		return $return;
	}

	public function GetCflTakeoutFarmerList($IMSID,$SearchStringParam,$SearchCpgParam,$start,$limit,$sortingField,$sortingDir){
		if($sortingField == "") $sortingField = 'FarmerID';
		if($sortingDir == "") $sortingDir = 'ASC';

		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					a.IMSID,
					a.FarmerID AFLFarmerID,
					a.FarmerID,
					a.FarmerName,
					a.CertStatusAudit AFLStatus,
					a.CertYear,
					a.CertFirstYear,
					a.CertHarvest,
					a.CertNextHarvest,
					a.CertHectare,
					a.CertFarmNr,
					CONCAT('[', b.`CPGid`, '] ', b.`GroupName`) AS FarmerGroup,
					a.`Village`,
					a.CertICSDate AS ICSDate,
					a.CertTotalHectare AS TotalHa,
					a.SalesQuota,
					a.TotalCocoaFarm
				FROM
					ktv_certification_certified_farmer a
					LEFT JOIN ktv_cpg b
						ON a.`CPGid` = b.`CPGid`
				WHERE
					a.IMSID = ?
					AND (
						a.FarmerID LIKE ?
						OR a.FarmerName LIKE ?
					)
					AND (
						b.CPGid LIKE ?
						OR b.GroupName LIKE ?
					)
					AND a.StatusCode = 'active'";
		$p = array(
			$IMSID,'%'.$SearchStringParam.'%','%'.$SearchStringParam.'%', '%'.$SearchCpgParam.'%', '%'.$SearchCpgParam.'%',
			(int) $start,(int) $limit
		);
		$DataList = $this->db->query($sql,$p)->result_array();

		$query = $this->db->query('SELECT FOUND_ROWS() AS total');
		$return['total'] = $query->row()->total;

		$return['success'] = true;
		$return['data'] = $DataList;
		return $return;
	}

	public function CflTakeoutFarmerList($IMSID,$FarmerIDSel){
		//echo '<pre>'; print_r($FarmerIDSel); exit;
		$this->db->trans_start();

		for ($i=0; $i < count($FarmerIDSel); $i++) {
			$FarmerID = (int) $FarmerIDSel[$i];

			//CFL Farmer
			$sql = "INSERT INTO `ktv_certification_not_certified_farmer` (
					`IMSID`,
					`FarmerID`,
					`FarmerName`,
					`CPGid`,
					`GroupName`,
					`Gender`,
					`HandPhone`,
					`Province`,
					`District`,
					`SubDistrict`,
					`Village`,
					`PolygonStatus`,
					`CertYear`,
					`CertFirstYear`,
					`YearOfCertification`,
					`PermanentWorkers`,
					`CertICSDate`,
					`CertStatusAudit`,
					`CertSurveyNr`,
					`CertHarvest`,
					`CertNextHarvest`,
					`SalesQuota`,
					`CertHectare`,
					`CertFarmNrLastYear`,
					`CertFarmNr`,
					`CertFarmTotalNr`,
					`CertTotalHectare`,
					`CertPohonTM`,
					`CertPohonTBM`,
					`CertPohonTR`,
					`CertCertificationStart`,
					`CertCertificationEnd`,
					`CertValidityStart`,
					`CertValidityEnd`,
					`CertExtensionStart`,
					`CertExtensionEnd`,
					`CertIssueDate`,
					`CertDateCollection`,
					`TotalCocoaFarm`,
					`TotalAuditedFarm`,
					`TotalAuditedFarmLastYear`,
					`SalesLastYear`,
					`SalesLast2Years`,
					`SalesLast3Years`,
					`IMSCreatorBy`,
					`IMSCreator`,
					`IMSEditorBy`,
					`IMSEditor`,
					`StatusAdditional`,
					`CertAuditRemark`,
					`StatusCode`,
					`DateCreated`,
					`CreatedBy`
				)
				SELECT
					`IMSID`,
					`FarmerID`,
					`FarmerName`,
					`CPGid`,
					`GroupName`,
					`Gender`,
					`HandPhone`,
					`Province`,
					`District`,
					`SubDistrict`,
					`Village`,
					`PolygonStatus`,
					`CertYear`,
					`CertFirstYear`,
					`YearOfCertification`,
					`PermanentWorkers`,
					`CertICSDate`,
					`CertStatusAudit`,
					`CertSurveyNr`,
					`CertHarvest`,
					`CertNextHarvest`,
					`SalesQuota`,
					`CertHectare`,
					`CertFarmNrLastYear`,
					`CertFarmNr`,
					`CertFarmTotalNr`,
					`CertTotalHectare`,
					`CertPohonTM`,
					`CertPohonTBM`,
					`CertPohonTR`,
					`CertCertificationStart`,
					`CertCertificationEnd`,
					`CertValidityStart`,
					`CertValidityEnd`,
					`CertExtensionStart`,
					`CertExtensionEnd`,
					`CertIssueDate`,
					`CertDateCollection`,
					`TotalCocoaFarm`,
					`TotalAuditedFarm`,
					`TotalAuditedFarmLastYear`,
					`SalesLastYear`,
					`SalesLast2Years`,
					`SalesLast3Years`,
					`IMSCreatorBy`,
					`IMSCreator`,
					`IMSEditorBy`,
					`IMSEditor`,
					`StatusAdditional`,
					`CertAuditRemark`,
					'active',
					NOW(),
					'{$_SESSION['userid']}'
				FROM
					`ktv_certification_certified_farmer`
				WHERE
					IMSID = '{$IMSID}'
					AND FarmerID = '{$FarmerID}'";
			$query = $this->db->query($sql);

			//CFL Garden
			$sql = "INSERT INTO `ktv_certification_not_certified_garden` (
					`IMSID`,
					`FarmerID`,
					`FarmerName`,
					`CPGid`,
					`GroupName`,
					`Gender`,
					`HandPhone`,
					`Province`,
					`District`,
					`SubDistrict`,
					`Village`,
					`PolygonStatus`,
					`CertLatitude`,
					`CertLongitude`,
					`CertYear`,
					`CertFirstYear`,
					`PermanentWorkers`,
					`CertICSDate`,
					`CertStatusAudit`,
					`CertSurveyNr`,
					`CertHarvest`,
					`CertNextHarvest`,
					`CertPercentageIncline`,
					`CertHectare`,
					`CertGardenNr`,
					`CertPohonTM`,
					`CertPohonTBM`,
					`CertPohonTR`,
					`CertPohonTMHectare`,
					`CertTotalPohonHectare`,
					`CertStart`,
					`CertEnd`,
					`CertDateCollection`,
					`CertCandidateSelection`,
					`CertCommentAudit`,
					`CertDateRevisionAudit`,
					`CertRecommendationAudit`,
					`1YearAgoSurveyNr`,
					`1YearAgoHarvest`,
					`1YearAgoHectare`,
					`1YearAgoGardenNr`,
					`1YearAgoPohonTM`,
					`1YearAgoPohonTBM`,
					`1YearAgoPohonTR`,
					`1YearAgoStart`,
					`1YearAgoEnd`,
					`1YearAgoDateCollection`,
					`2YearAgoSurveyNr`,
					`2YearAgoHarvest`,
					`2YearAgoHectare`,
					`2YearAgoGardenNr`,
					`2YearAgoPohonTM`,
					`2YearAgoPohonTBM`,
					`2YearAgoPohonTR`,
					`2YearAgoStart`,
					`2YearAgoEnd`,
					`2YearAgoDateCollection`,
					`BaselineSurveyNr`,
					`BaselineHarvest`,
					`BaselineHectare`,
					`BaselineGardenNr`,
					`BaselinePohonTM`,
					`BaselinePohonTBM`,
					`BaselinePohonTR`,
					`BaselineStart`,
					`BaselineEnd`,
					`BaselineDateCollection`,
					`IMSCreatorBy`,
					`IMSCreator`,
					`IMSEditorBy`,
					`IMSEditor`,
					`ResponsibleBy`,
					`ResponsibleName`,
					`CertAuditNotComplyReason`,
					`CertAuditRemark`,
					`StatusCode`,
					`DateCreated`,
					`CreatedBy`
				)
				SELECT
					`IMSID`,
					`FarmerID`,
					`FarmerName`,
					`CPGid`,
					`GroupName`,
					`Gender`,
					`HandPhone`,
					`Province`,
					`District`,
					`SubDistrict`,
					`Village`,
					`PolygonStatus`,
					`CertLatitude`,
					`CertLongitude`,
					`CertYear`,
					`CertFirstYear`,
					`PermanentWorkers`,
					`CertICSDate`,
					`CertStatusAudit`,
					`CertSurveyNr`,
					`CertHarvest`,
					`CertNextHarvest`,
					`CertPercentageIncline`,
					`CertHectare`,
					`CertGardenNr`,
					`CertPohonTM`,
					`CertPohonTBM`,
					`CertPohonTR`,
					`CertPohonTMHectare`,
					`CertTotalPohonHectare`,
					`CertStart`,
					`CertEnd`,
					`CertDateCollection`,
					`CertCandidateSelection`,
					`CertCommentAudit`,
					`CertDateRevisionAudit`,
					`CertRecommendationAudit`,
					`1YearAgoSurveyNr`,
					`1YearAgoHarvest`,
					`1YearAgoHectare`,
					`1YearAgoGardenNr`,
					`1YearAgoPohonTM`,
					`1YearAgoPohonTBM`,
					`1YearAgoPohonTR`,
					`1YearAgoStart`,
					`1YearAgoEnd`,
					`1YearAgoDateCollection`,
					`2YearAgoSurveyNr`,
					`2YearAgoHarvest`,
					`2YearAgoHectare`,
					`2YearAgoGardenNr`,
					`2YearAgoPohonTM`,
					`2YearAgoPohonTBM`,
					`2YearAgoPohonTR`,
					`2YearAgoStart`,
					`2YearAgoEnd`,
					`2YearAgoDateCollection`,
					`BaselineSurveyNr`,
					`BaselineHarvest`,
					`BaselineHectare`,
					`BaselineGardenNr`,
					`BaselinePohonTM`,
					`BaselinePohonTBM`,
					`BaselinePohonTR`,
					`BaselineStart`,
					`BaselineEnd`,
					`BaselineDateCollection`,
					`IMSCreatorBy`,
					`IMSCreator`,
					`IMSEditorBy`,
					`IMSEditor`,
					`ResponsibleBy`,
					`ResponsibleName`,
					`CertAuditNotComplyReason`,
					`CertAuditRemark`,
					'active',
					NOW(),
					'{$_SESSION['userid']}'
				FROM
					`ktv_certification_certified_garden`
				WHERE
					IMSID = '{$IMSID}'
					AND FarmerID = '{$FarmerID}'";
			$query = $this->db->query($sql);

			//Delete Data CFL
			$sql = "DELETE FROM ktv_certification_certified_garden WHERE IMSID='{$IMSID}' AND FarmerID='{$FarmerID}'";
			$query = $this->db->query($sql);

			$sql = "DELETE FROM ktv_certification_certified_farmer WHERE IMSID='{$IMSID}' AND FarmerID='{$FarmerID}'";
			$query = $this->db->query($sql);
		}

		$this->db->trans_complete();
		if ($this->db->trans_status() === true) {
			$results['success'] = true;
			$results['message'] = "Process Success";
		} else {
			$results['success'] = false;
			$results['message'] = "Process Failed";
		}
		return $results;
	}

	public function ImsFinalizationPeriodFormData($IMSID){
		$sql = "SELECT
					a.`StatusImsFinalPeriod` AS 'Koltiva.view.IMS.WinImsFormFinalizationPeriod-Form-StatusImsFinalPeriod'
					, (SELECT su.UserRealName FROM sys_user su WHERE su.UserId = a.`StatusImsFinalPeriodUser` LIMIT 1) AS 'Koltiva.view.IMS.WinImsFormFinalizationPeriod-Form-StatusImsFinalPeriodUser'
					, (SELECT su.UserRealName FROM sys_user su WHERE su.UserId = a.`StatusImsFinalPeriodUser` LIMIT 1) AS StatusImsFinalPeriodUser
					, a.`StatusImsFinalPeriodComment` AS 'Koltiva.view.IMS.WinImsFormFinalizationPeriod-Form-StatusImsFinalPeriodComment'
				FROM
					ktv_ims a
				WHERE
					a.`IMSID` = ?
				LIMIT 1";
		$data = $this->db->query($sql,array($IMSID))->row_array();

		if(!isset($data['StatusImsFinalPeriodUser'])){
			$sql = "SELECT su.UserRealName AS 'Koltiva.view.IMS.WinImsFormFinalizationPeriod-Form-StatusImsFinalPeriodUser' FROM sys_user su WHERE su.UserId = '{$_SESSION['userid']}' LIMIT 1";
			$data = $this->db->query($sql)->row_array();
		}

		$return['success'] = true;
		$return['data'] = $data;
		return $return;
	}

	public function UpdateImsFinalizationPeriod($ParamPost){
		$sql = "UPDATE ktv_ims a SET
					a.`StatusImsFinalPeriod` = ?,
					a.`StatusImsFinalPeriodUser` = ?,
					a.`StatusImsFinalPeriodComment` = ?
				WHERE
					a.`IMSID` = ?
				LIMIT 1";
		$p = array(
			$ParamPost['StatusImsFinalPeriod'],
			$_SESSION['userid'],
			$ParamPost['StatusImsFinalPeriodComment'],
			$ParamPost['IMSID']
		);
		$query = $this->db->query($sql,$p);

		if($query){
			$return['success'] = true;
			$return['message'] = lang('Process Saved');
		}else{
			$return['success'] = false;
			$return['message'] = lang('Process Failed');
		}
		return $return;
	}

	public function ImsIcsReinspectionFormData($IMSID){
		$sql = "SELECT
					a.`StatusIcsReinspect` AS 'Koltiva.view.IMS.WinImsFormIcsReinspectionStatus-Form-StatusIcsReinspect'
					, (SELECT su.UserRealName FROM sys_user su WHERE su.UserId = a.`StatusIcsReinspectUser` LIMIT 1) AS 'Koltiva.view.IMS.WinImsFormIcsReinspectionStatus-Form-StatusIcsReinspectUser'
					, (SELECT su.UserRealName FROM sys_user su WHERE su.UserId = a.`StatusIcsReinspectUser` LIMIT 1) AS StatusIcsReinspectUser
					, a.`StatusIcsReinspectComment` AS 'Koltiva.view.IMS.WinImsFormIcsReinspectionStatus-Form-StatusIcsReinspectComment'
				FROM
					ktv_ims a
				WHERE
					a.`IMSID` = ?
				LIMIT 1";
		$data = $this->db->query($sql,array($IMSID))->row_array();

		if(!isset($data['StatusIcsReinspectUser'])){
			$sql = "SELECT su.UserRealName AS 'Koltiva.view.IMS.WinImsFormIcsReinspectionStatus-Form-StatusIcsReinspectUser' FROM sys_user su WHERE su.UserId = '{$_SESSION['userid']}' LIMIT 1";
			$data = $this->db->query($sql)->row_array();
		}

		$return['success'] = true;
		$return['data'] = $data;
		return $return;
	}

	public function UpdateIcsReinspectionForm($ParamPost){
		$this->db->trans_start();

		$sql = "UPDATE ktv_ims a SET
					a.`StatusIcsReinspect` = ?,
					a.`StatusIcsReinspectUser` = ?,
					a.`StatusIcsReinspectComment` = ?
				WHERE
					a.`IMSID` = ?
				LIMIT 1";
		$p = array(
			$ParamPost['StatusIcsReinspect'],
			$_SESSION['userid'],
			$ParamPost['StatusIcsReinspectComment'],
			$ParamPost['IMSID']
		);
		$query = $this->db->query($sql,$p);

		//Copy data AFL ke history ============================= (Begin)
		if($ParamPost['StatusIcsReinspect'] == "1"){

			//Cek apakah perlu di insert ke history
			$sql = "SELECT
						COUNT(a.`IMSID`) AS Jumlah
					FROM
						`ktv_certification_afl_farmer_reinspection_history` a
					WHERE
						a.`IMSID` = ?";
			$CekAflFarmer = $this->db->query($sql,array($ParamPost['IMSID']))->row_array();

			$sql = "SELECT
						COUNT(a.`IMSID`) AS Jumlah
					FROM
						`ktv_certification_afl_garden_reinspection_history` a
					WHERE
						a.`IMSID` = ?";
			$CekAflGarden = $this->db->query($sql,array($ParamPost['IMSID']))->row_array();

			if($CekAflFarmer['Jumlah'] == "0" && $CekAflGarden['Jumlah'] == "0"){
				//Afl Farmer
				$sql = "SELECT
							a.*
						FROM
							ktv_certification_afl_farmer a
						WHERE
							a.`IMSID` = ?";
				$DataAflFarmer = $this->db->query($sql,array($ParamPost['IMSID']))->result_array();
				foreach ($DataAflFarmer as $key => $value) {
					$this->db->insert('ktv_certification_afl_farmer_reinspection_history', $value);
				}

				//Afl Garden
				$sql = "SELECT
							a.*
						FROM
							ktv_certification_afl_garden a
						WHERE
							a.`IMSID` = ?";
				$DataAflGarden = $this->db->query($sql,array($ParamPost['IMSID']))->result_array();
				foreach ($DataAflGarden as $key => $value) {
					$this->db->insert('ktv_certification_afl_garden_reinspection_history', $value);
				}
			}
		}
		//Copy data AFL ke history ============================= (End)

		$this->db->trans_complete();
		if ($this->db->trans_status() === true) {
			$return['success'] = true;
			$return['message'] = lang('Process Saved');
		} else {
			$return['success'] = false;
			$return['message'] = lang('Process Failed');
		}
		$return['StatusIcsReinspect'] = $ParamPost['StatusIcsReinspect'];
		return $return;
	}

	public function GetIcsReinspectionAddFarmerListGrid($IMSID,$SearchStringParam,$SearchCpgParam,$start,$limit,$sortingField,$sortingDir){
		if($sortingField == "") $sortingField = 'FarmerID';
		if($sortingDir == "") $sortingDir = 'ASC';

		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					a.FarmerID,
					a.`FarmerName`,
					a.`CertGardenNr`,
					CONCAT('[', b.`CPGid`, '] ', b.`GroupName`) AS FarmerGroup,
					a.`Village`,
					a.CertStatusAudit AS AFLStatus,
					c.StatusRegenerateIcs AS RegenerateICSStatus,
					a.`CertICSDate` AS ICSDate,
					a.`CertNextHarvest`,
					a.`CertHarvest`,
					a.`CertHectare`
				FROM
					ktv_certification_afl_garden a
					LEFT JOIN `ktv_certification_afl_farmer` afl ON 1=1
						AND a.`IMSID` = afl.`IMSID`
						AND a.`FarmerID` = afl.`FarmerID`
					LEFT JOIN ktv_cpg b ON a.`CPGid` = b.`CPGid`
					LEFT JOIN ktv_ims_ics_reinspection c ON 1=1
						AND a.`IMSID` = c.`IMSID`
						AND a.`FarmerID` = c.`FarmerID`
						AND a.`CertGardenNr` = c.`GardenNr`
				WHERE
					a.`IMSID` = ?
					AND (
						a.FarmerID LIKE ?
						OR a.FarmerName LIKE ?
					)
					AND (
						b.CPGid LIKE ?
						OR b.GroupName LIKE ?
					)
					AND a.`StatusCode` = 'active'
					AND a.`CertStatusAudit` IN (1,2,3) #yang punya status audit saja
					AND c.`FarmerID` IS NULL
					AND afl.`CertStatusVerified` = '2' #Sudah verified ims manager
				ORDER BY $sortingField $sortingDir
				LIMIT ?, ?";
		$p = array(
			$IMSID,'%'.$SearchStringParam.'%','%'.$SearchStringParam.'%', '%'.$SearchCpgParam.'%', '%'.$SearchCpgParam.'%',
			(int) $start,(int) $limit
		);
		$DataList = $this->db->query($sql,$p)->result_array();

		$query = $this->db->query('SELECT FOUND_ROWS() AS total');
		$return['total'] = $query->row()->total;

		$return['success'] = true;
		$return['data'] = $DataList;
		return $return;
	}

	public function InsertIcsReinspectionFarmer($IMSID,$FarmerGardenSel){
		$this->db->trans_start();

		//Get IMS Certification Program
		$sql = "SELECT
					b.`CertProgID`
				FROM
					ktv_ims a
					INNER JOIN ktv_certification_holders b ON a.`CertHolderID` = b.`CertHolderID`
				WHERE
					a.`IMSID` = ?
				LIMIT 1";
		$DataIMS = $this->db->query($sql,array($IMSID))->row_array();

		for ($i=0; $i < count($FarmerGardenSel); $i++) {
			$ArrTemp = explode("@",$FarmerGardenSel[$i]);
			$FarmerID = $ArrTemp[0];
			$GardenNr = $ArrTemp[1];
			$LogIDSurveyGarden = array();
			$LogIDSurveyPh = array();
			$LogIDSurveyAu = array();

			//Insert
			$sql = "INSERT INTO `ktv_ims_ics_reinspection` SET
					`IMSID` = ?,
					`FarmerID` = ?,
					GardenNr = ?,
					`StatusRegenerateIcs` = NULL,
					`DateGenerated` = NOW(),
					`GeneratedBy` = '{$_SESSION['userid']}'";
			$p = array(
				$IMSID,
				$FarmerID,
				$GardenNr
			);
			$query = $this->db->query($sql,$p);

			//Backup data survey ========================================= (Begin)

			//Ambil data SurveyNr dan GardenNr yg perlu dibackup
			$sql = "SELECT
						a.`CertSurveyNr` AS SurveyNr
						, GROUP_CONCAT(a.`CertGardenNr` SEPARATOR ',') AS GardenNr
					FROM
						ktv_certification_afl_garden a
					WHERE
						a.`IMSID` = ?
						AND a.`FarmerID` = ?
						AND a.CertGardenNr = ?
					GROUP BY a.`CertSurveyNr`
					LIMIT 1";
			$DataNr = $this->db->query($sql,array($IMSID,$FarmerID,$GardenNr))->row_array();

			if(isset($DataNr['SurveyNr']) && isset($DataNr['GardenNr'])){
				//Survey Garden
				$sql = "SELECT
							g.*
						FROM
							ktv_members_garden g
						WHERE
							g.FarmerID = ?
							AND g.SurveyNr = ?
							AND g.GardenNr IN ({$DataNr['GardenNr']})";
				$DataGardenInsert = $this->db->query($sql,array($FarmerID,$DataNr['SurveyNr']))->result_array();
				foreach ($DataGardenInsert as $key => $value) {
					$value['LogID'] = null;
					$this->db->insert('ktv_members_garden_reinspection_history', $value);
					$LogIDSurveyGarden[] = $this->db->insert_id();
				}
				//Delete SurveyNya
				$sql = "DELETE FROM ktv_members_garden WHERE FarmerID = ?
						AND SurveyNr = ?
						AND GardenNr IN ({$DataNr['GardenNr']})";
				$query = $this->db->query($sql,array($FarmerID,$DataNr['SurveyNr']));

				//Survey Post Harvest
				$sql = "SELECT
							a.*
						FROM
							ktv_members_post_harvest a
						WHERE
							a.`FarmerID` = ?
							AND a.`SurveyNr` = ?
						LIMIT 1";
				$DataPhInsert = $this->db->query($sql,array($FarmerID,$DataNr['SurveyNr']))->row_array();
				if(isset($DataPhInsert['FarmerID'])){
					$DataPhInsert['LogID'] = null;
					$this->db->insert('ktv_members_post_harvest_reinspection_history', $DataPhInsert);
					$LogIDSurveyPh[] = $this->db->insert_id();
				}
				//Delete SurveyNya
				$sql = "DELETE FROM ktv_members_post_harvest WHERE `FarmerID` = ?
						AND `SurveyNr` = ?
						LIMIT 1";
				$query = $this->db->query($sql,array($FarmerID,$DataNr['SurveyNr']));


				//Survey Audit Log
				/* Survey Audit Log tidak dibackup berdasarkan diskusi terbaru
				$sql = "SELECT
							a.*
						FROM
							ktv_certification_audit_log a
						WHERE
							a.`FarmerID` = ?
							AND a.`SurveyNr` = ?
							AND a.`GardenNr` IN ({$DataNr['GardenNr']})
							AND a.`Certification` = ?";
				$DataAuInsert = $this->db->query($sql,array($FarmerID,$DataNr['SurveyNr'],$DataIMS['CertProgID']))->result_array();
				foreach ($DataAuInsert as $key => $value) {
					$value['LogID'] = null;
					$this->db->insert('ktv_certification_audit_log_reinspection_history', $value);
					$LogIDSurveyAu[] = $this->db->insert_id();
				}
				//Delete SurveyNya
				$sql = "DELETE FROM ktv_certification_audit_log WHERE
							`FarmerID` = ?
							AND `SurveyNr` = ?
							AND `GardenNr` IN ({$DataNr['GardenNr']})
							AND `Certification` = ?
				";
				$query = $this->db->query($sql,array($FarmerID,$DataNr['SurveyNr'],$DataIMS['CertProgID']));
				*/

				//Update LogId
				$ImpLogIDSurveyGarden = implode(',',$LogIDSurveyGarden);
				$ImpLogIDSurveyPh = implode(',',$LogIDSurveyPh);
				$ImpLogIDSurveyAu = implode(',',$LogIDSurveyAu);
				$sql = "UPDATE ktv_ims_ics_reinspection a SET
							a.`LogIDSurveyGarden` = ?,
							a.`LogIDSurveyPh` = ?,
							a.`LogIDSurveyAu` = ?
						WHERE
							a.`FarmerID` = ?
							AND a.`IMSID` = ?
						LIMIT 1";
				$p = array(
					$ImpLogIDSurveyGarden,
					$ImpLogIDSurveyPh,
					null,
					$FarmerID,
					$IMSID
				);
				$query = $this->db->query($sql,$p);
			}

			//Backup data survey ========================================= (End)
		}

		$this->db->trans_complete();
		if ($this->db->trans_status() === true) {
			$results['success'] = true;
			$results['message'] = "Process Success";
		} else {
			$results['success'] = false;
			$results['message'] = "Process Failed";
		}
		return $results;
	}

	public function CancelIcsReinspectionFarmer($IMSID,$FarmerID,$GardenNr){
		$this->db->trans_start();

		//Ambil Datanya terlebih dahulu
		$sql = "SELECT
					a.LogIDSurveyGarden,
					a.`LogIDSurveyPh`,
					a.`LogIDSurveyAu`
				FROM
					ktv_ims_ics_reinspection a
				WHERE
					a.`IMSID` = ?
					AND a.`FarmerID` = ?
					AND a.GardenNr = ?
				LIMIT 1";
		$DataIcs = $this->db->query($sql,array($IMSID,$FarmerID,$GardenNr))->row_array();

		$sql = "DELETE FROM `ktv_ims_ics_reinspection` WHERE IMSID=? AND FarmerID=? LIMIT 1";
		$query = $this->db->query($sql,array($IMSID,$FarmerID));

		//Kembalikan surveynya dari backup reinspection history ============================== (Begin)

		//Survey Garden
		if($DataIcs['LogIDSurveyGarden'] != ""){
			$sql = "SELECT
						a.*
					FROM
						`ktv_members_garden_reinspection_history` a
					WHERE
						a.`LogID` IN ({$DataIcs['LogIDSurveyGarden']})";
			$DataSurGarden = $this->db->query($sql)->result_array();
			foreach ($DataSurGarden as $key => $value) {
				unset($value['LogID']);
				$this->db->insert('ktv_members_garden', $value);
			}
		}

		//Survey Post Harvest
		if($DataIcs['LogIDSurveyPh'] != ""){
			$sql = "SELECT
						a.*
					FROM
						`ktv_members_post_harvest_reinspection_history` a
					WHERE
						a.`LogID` = {$DataIcs['LogIDSurveyPh']}";
			$DataSurPh = $this->db->query($sql)->row_array();
			if($DataSurPh['FarmerID'] != ""){
				unset($DataSurPh['LogID']);
				$this->db->insert('ktv_members_post_harvest', $DataSurPh);
			}
		}

		//Survey Audit Log
		if($DataIcs['LogIDSurveyAu'] != ""){
			$sql = "SELECT
						a.*
					FROM
						`ktv_certification_audit_log_reinspection_history` a
					WHERE
						a.`LogID` IN ({$DataIcs['LogIDSurveyAu']})";
			$DataSurAu = $this->db->query($sql)->result_array();
			foreach ($DataSurAu as $key => $value) {
				unset($value['LogID']);
				$this->db->insert('ktv_certification_audit_log', $value);
			}
		}

		//Kembalikan surveynya dari backup reinspection history ============================== (End)


		//Delete data di tabel backup reinspection history ============================== (Begin)
		$sql = "DELETE FROM ktv_members_garden_reinspection_history WHERE LogID IN ({$DataIcs['LogIDSurveyGarden']})";
		$query = $this->db->query($sql);

		$sql = "DELETE FROM ktv_members_post_harvest_reinspection_history WHERE LogID = {$DataIcs['LogIDSurveyPh']} LIMIT 1";
		$query = $this->db->query($sql);

		//$sql = "DELETE FROM ktv_certification_audit_log_reinspection_history WHERE LogID IN ({$DataIcs['LogIDSurveyAu']})";
		//$query = $this->db->query($sql);
		//Delete data di tabel backup reinspection history ============================== (End)

		$this->db->trans_complete();
		if ($this->db->trans_status() === true) {
			$results['success'] = true;
			$results['message'] = "Cancel Process Success";
		} else {
			$results['success'] = false;
			$results['message'] = "Cancel Process Failed";
		}
		return $results;
	}

	public function GetAflGardenDataByFarmer($IMSID,$FarmerID){
		$sql = "SELECT
					au.`FarmerID`
					, au.`GardenNr`
					, au.`SurveyNr`
					, au.`Certification`
					, MAX(au.`ICSDate`) AS ICSDate
				FROM
					ktv_certification_afl_garden aflg
					INNER JOIN ktv_certification_audit_log au ON 1=1
						AND aflg.`FarmerID` = au.`FarmerID`
						AND aflg.`CertSurveyNr` = au.`SurveyNr`
						AND aflg.`CertGardenNr` = au.`GardenNr`
						#AND au.`Certification` = 1
				WHERE
					aflg.`IMSID` = ?
					AND aflg.`FarmerID` = ?
				GROUP BY au.`FarmerID`, au.`GardenNr`, au.`SurveyNr`, au.`Certification`";
		$p = array(
			$IMSID,$FarmerID
		);
		return $this->db->query($sql,$p)->result_array();
	}

	public function UpdateIMSKmlPath($IMSID, $filepath) {
		$sql = "UPDATE ktv_ims a SET
					a.`kmlPath` = ?
				WHERE
					a.`IMSID` = ?
				LIMIT 1";
		$p = array(
			$filepath,$IMSID
		);
		$query = $this->db->query($sql,$p);
	}

	public function GetICSFarmerByIMS($IMSID) {
		$sql = "SELECT
					a.`FarmerID`
				FROM
					ktv_certification_afl_farmer a
				WHERE
					a.`IMSID` = ?";
		$p = array(
			$IMSID
		);
		return $this->db->query($sql,$p)->result_array();
	}

	public function ExternalAuditInput($FarmerIDSel,$IMSID) {
		$results = array();
		$this->db->trans_start();

		$RemarkText = "UserID: {$_SESSION['userid']}\nDateGenerated: ".date('YmdHis');

		//Get IMS Info
		$DataIMS = $this->getIMSDetail($IMSID);

		$ArrFarmerID = explode(",",$FarmerIDSel);
		for ($i=0; $i < count($ArrFarmerID); $i++) {
			$FarmerID = (int) $ArrFarmerID[$i];

			//Farmer
			$sql = "UPDATE `ktv_certification_afl_farmer` a SET
						a.`ExternalAuditStatus` = '3' #NotPass
						, a.`ExternalAuditRemark` = ?
					WHERE
						a.`FarmerID` = ?
						AND a.`IMSID` = ?
					LIMIT 1";
			$p = array(
				$RemarkText,$FarmerID,$IMSID
			);
			$query = $this->db->query($sql,$p);

			//Garden
			$sql = "UPDATE `ktv_certification_afl_garden` a SET
						a.`ExternalAuditStatus` = '3' #NotPass
						, a.`ExternalAuditRemark` = ?
					WHERE
						a.`FarmerID` = ?
						AND a.`IMSID` = ?";
			$p = array(
				$RemarkText,$FarmerID,$IMSID
			);
			$query = $this->db->query($sql,$p);

			//Insert ke Audit Log nya ===================== (Begin)
				//Get Data Garden
				$sql = "SELECT
							a.`FarmerID`
							, a.`CertGardenNr` AS GardenNr
							, a.`CertSurveyNr` AS SurveyNr
						FROM
							`ktv_certification_afl_garden` a
						WHERE
							a.`FarmerID` = ?
							AND a.`IMSID` = ?";
				$p = array(
					$FarmerID,$IMSID
				);
				$AflGarden = $this->db->query($sql,$p)->result_array();

				if(isset($AflGarden[0]['FarmerID'])) {
					for ($j=0; $j < count($AflGarden); $j++) {
						//insert audit log
						$sql = "INSERT INTO ktv_certification_audit_log SET
								FarmerID = ?,
								GardenNr = ?,
								SurveyNr = ?,
								Certification = ?,
								ICSDate = CURDATE(),
								StatusAudit = '2',
								CommentAudit = 'External Audit take out',
								FromExternalAudit = '1',
								DateCreated = NOW(),
								CreatedBy = ?";
						$p = array(
							$FarmerID,
							$AflGarden[$j]['GardenNr'],
							$AflGarden[$j]['SurveyNr'],
							$DataIMS['CertProgID'],
							$_SESSION['userid']
						);
						$query = $this->db->query($sql,$p);
					}
				}
			//Insert ke Audit Log nya ===================== (End)
		}

		$this->db->trans_complete();
		if ($this->db->trans_status() === true) {
			$results['success'] = true;
			$results['message'] = lang("External Audit Updated");
		} else {
			$results['success'] = false;
			$results['message'] = lang("Failed to save data");
		}
		return $results;
	}

	public function ExternalAuditReset($FarmerID,$IMSID) {
		$results = array();
		$DataIMS = $this->getIMSDetail($IMSID);
		$this->db->trans_start();

		$sql = "UPDATE `ktv_certification_afl_farmer` a SET
						a.`ExternalAuditStatus` = '1' #New
						, a.`ExternalAuditRemark` = NULL
					WHERE
						a.`FarmerID` = ?
						AND a.`IMSID` = ?
					LIMIT 1";
		$p = array(
			$FarmerID,$IMSID
		);
		$query = $this->db->query($sql,$p);

		$sql = "UPDATE `ktv_certification_afl_garden` a SET
					a.`ExternalAuditStatus` = '1' #New
					, a.`ExternalAuditRemark` = NULL
				WHERE
					a.`FarmerID` = ?
					AND a.`IMSID` = ?";
		$p = array(
			$FarmerID,$IMSID
		);
		$query = $this->db->query($sql,$p);

		//Audit Log ================================= (Begin)
			//Get Data Garden
			$sql = "SELECT
						a.`FarmerID`
						, a.`CertGardenNr` AS GardenNr
						, a.`CertSurveyNr` AS SurveyNr
					FROM
						`ktv_certification_afl_garden` a
					WHERE
						a.`FarmerID` = ?
						AND a.`IMSID` = ?";
			$p = array(
				$FarmerID,$IMSID
			);
			$AflGarden = $this->db->query($sql,$p)->result_array();

			if(isset($AflGarden[0]['FarmerID'])) {
				for ($j=0; $j < count($AflGarden); $j++) {
					$sql = "DELETE FROM `ktv_certification_audit_log`
							WHERE
								FarmerID = ?
								AND GardenNr = ?
								AND SurveyNr = ?
								AND Certification = ?
								AND FromExternalAudit = '1'";
					$p = array(
						$AflGarden[$j]['FarmerID'],
						$AflGarden[$j]['GardenNr'],
						$AflGarden[$j]['SurveyNr'],
						$DataIMS['CertProgID']
					);
					$query = $this->db->query($sql,$p);
				}
			}
		//Audit Log ================================= (End)

		$this->db->trans_complete();
		if ($this->db->trans_status() === true) {
			$results['success'] = true;
			$results['message'] = lang("Reset Success");
		} else {
			$results['success'] = false;
			$results['message'] = lang("Reset Failed");
		}
		return $results;
	}

	public function GetGridFormFarmerCandidate($pSearch, $start, $limit, $sortingField, $sortingDir) {
		$result = array();
		if ($sortingField == "")
			$sortingField = 'MemberName';
		if ($sortingDir == "")
			$sortingDir = 'ASC';

		//========== Hak akses (Begin) =====================
		if ($_SESSION['daerah_access'] != "")
			$SqlHakAkses = "AND d.DistrictID IN ({$_SESSION['daerah_access']})";
		else
			$SqlHakAkses = "AND d.DistrictID IN ('')";
		//========== Hak akses (End) =======================
		//========== Search (Begin) =====================
		$SqlSearch = "";
		if ($pSearch['textSearch'] != "") {
			$SqlSearch = $SqlSearch . " AND ( (a.`MemberDisplayID` LIKE '%{$pSearch['textSearch']}%') OR (a.`MemberName` LIKE '%{$pSearch['textSearch']}%') ) ";
		}
		if ($pSearch['CmbFilterProvince'] != "") {
			$SqlSearch = $SqlSearch . " AND p.ProvinceID = '{$pSearch['CmbFilterProvince']}' ";
		}
		if ($pSearch['CmbFilterDistrict'] != "") {
			$SqlSearch = $SqlSearch . " AND d.DistrictID = '{$pSearch['CmbFilterDistrict']}' ";
		}
		if ($pSearch['CmbFilterSubDistrict'] != "") {
			$SqlSearch = $SqlSearch . " AND sd.SubDistrictID = '{$pSearch['CmbFilterSubDistrict']}' ";
		}
		//========== Search (End) =====================

		$sql = "SELECT
					ims.IMSID
					, fb.FirstBuyerID
					, fb.FirstBuyerPartnerID
				FROM ktv_ims ims
				LEFT JOIN ktv_first_buyer fb ON fb.FirstBuyerID = ims.FirstBuyerID
				WHERE ims.IMSID = '{$pSearch['IMSID']}';";
		$query = $this->db->query($sql);
		$ims = $query->row_array();

		$sql = "SELECT GROUP_CONCAT(a.FarmerID) farmer_exist
				FROM ktv_certification_pre_afl a
				WHERE a.IMSID = '{$pSearch['IMSID']}'
				group by a.IMSID;";
		$query = $this->db->query($sql);
		$farmer = $query->row_array();
		$sql_exist = "";
		if(count($farmer)>0){
			$sql_exist = " AND a.MemberID NOT IN ({$farmer['farmer_exist']})";
		}

		$sql = "SELECT SQL_CALC_FOUND_ROWS
					a.MemberID FarmerID
					, a.MemberName FarmerName
					, a.Gender
					, FLOOR(DATEDIFF(CURDATE(), a.DateOfBirth) / 365.25) AS Age
					, p.Province
					, d.District
					, sd.SubDistrict
					, v.VillageID
				FROM ktv_members a
				INNER JOIN ktv_access_partner_member fa ON fa.apmMemberID = a.MemberID AND fa.apmPartnerID = '{$ims['FirstBuyerPartnerID']}'
				LEFT JOIN ktv_village v ON v.VillageID = a.VillageID
				LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
				LEFT JOIN ktv_district d ON d.DistrictID = sd.DistrictID
				LEFT JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
				WHERE 1=1
					AND a.`StatusCode` = 'active'
					$sql_exist
					$SqlHakAkses
					$SqlSearch
				GROUP BY a.`MemberID`
				ORDER BY `$sortingField` $sortingDir
				LIMIT ?,?";
		$p = array($start, $limit);
		$query = $this->db->query($sql, $p);
		$result['data'] = $query->result_array();
		$result['sql'] = $this->db->last_query();

		$query = $this->db->query('SELECT FOUND_ROWS() AS total');
		$result['total'] = $query->row()->total;

		return $result;
	}

	public function InsertCandidateIms($FarmerID, $IMSID) {
		$this->db->trans_start();
		foreach ($FarmerID as $k => $v) {
			$data = array();
			$data['FarmerID'] = $v;
			$data['IMSID'] = $IMSID;
			$data['StatusAudit'] = 1;
			$data['StatusComply'] = '1';
			$data['StatusAdditional'] = '1';
//            $data['ClusterID'] = 6;
			$data['StatusCode'] = 'active';
			$data['CreatedBy']   = $_SESSION['userid'];
			$data['DateCreated'] = date('Y-m-d H:i:s');

			$this->db->insert('ktv_certification_pre_afl', $data);

		}
		$this->db->trans_complete();
		if ($this->db->trans_status() === true) {
			$results['success'] = true;
			$results['message'] = lang("Candidate Saved");
		} else {
			$results['success'] = false;
			$results['message'] = lang("Failed to save data");
		}
		return $results;
	}
	
	public function getDataReview($RepID) {
		$sql = "SELECT
					a.RepSqlStatement AS sqlNya,
					a.RepName AS ReportName
				FROM `ktv_ims_report_tools` as a
				WHERE
					a.RepID = ?
				LIMIT 1";
		$p = array(
			$RepID
		);
		return $this->db->query($sql, $p)->row_array();
	}

	public function GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$RefID){
		$sql = "INSERT INTO $DataControlItemTabel SET
				`DaconID` = ?,
				`RefID` = ?,
				`DateGenerated` = NOW()";
		$p = array(
			$DaconID,$RefID
		);
		$query = $this->db->query($sql,$p);
	}
	

	public function GenDataControlSurveyGarden($ProsesOpt,$DateGen,$UserID,$FarmerID,$GardenNr,$SurveyNr,$Certification,$ICSDate){
		$DataControlItemTabel = 'ktv_farmer_garden_datacontrol_item';

		//Get User
		$sql = "SELECT
					a.`UserRealName` AS `Name`
				FROM
					sys_user a
				WHERE
					a.`UserId` = ?
				LIMIT 1";
		$DataUser = $this->db->query($sql,array($UserID))->row_array();

		//Susun RemarkText ==================== (Begin)
		if($ProsesOpt == "Insert"){
			$RemarkText = "Data Inserted on: $DateGen by {$DataUser['Name']}";
		}else{
			$RemarkText = "Data Updated on: $DateGen by {$DataUser['Name']}";
		}
		if($ProsesOpt == 'ims'){
			$RemarkText = "Data Regenerated from IMS on: $DateGen by {$DataUser['Name']}";
		}
		//Susun RemarkText ==================== (End)

		//Get Data Garden
		$sql = "SELECT
					a.*
				FROM
					ktv_survey_plot a
				WHERE
					a.`MemberID` = ?
					AND a.`PlotNr` = ?
					AND a.`SurveyNr` = ?
				LIMIT 1";
		$DataGarden = $this->db->query($sql,array($FarmerID,$GardenNr,$SurveyNr))->row_array();

		//Data Control
		$sql = "SELECT
					a.`RefID`
					, a.`ConKey`
				FROM
					ktv_ref_survey_datacontrol a
				WHERE
					a.`SurveyType` = 'Garden'
					AND a.`StatusCode` = 'active'
				ORDER BY a.`RefID`";
		$DataDC = $this->db->query($sql)->result_array();

		if(isset($DataDC[0]['RefID'])){
			//Insert Master
			$sql = "INSERT INTO `ktv_farmer_garden_datacontrol` SET
				`MemberID` = ?,
				`SurveyNr` = ?,
				PlotNr = ?,
				Certification = ?,
				ICSDate = ?,
				`RemarkProcess` = ?,
				`DataGenerated` = NOW(),
				`GeneratedBy` = ?";
			$p = array(
				$FarmerID,
				$SurveyNr,
				$GardenNr,
				$Certification,
				$ICSDate,
				$RemarkText,
				$UserID
			);
			$query = $this->db->query($sql,$p);
			$DaconID = $this->db->insert_id();

			//Variable Kalkulasi yg diperlukan untuk pengecekan =============== (Begin)
			$EstTotalCocoaTrees = (float) $DataGarden['EstTotalCocoaTrees'];
			$PohonTotal = ((float) $DataGarden['TreeTBM']) + ((float) $DataGarden['TreeTM']) + ((float) $DataGarden['TreeTR']);

			// $TotalKlon = ((float) $DataGarden['S1Nr']) +
			// 			((float) $DataGarden['S2Nr']) +
			// 			((float) $DataGarden['S3Nr']) +
			// 			((float) $DataGarden['J45Nr']) +
			// 			((float) $DataGarden['M01Nr']) +
			// 			((float) $DataGarden['TSH858Nr']) +
			// 			((float) $DataGarden['ICRRI3Nr']) +
			// 			((float) $DataGarden['ICRRI4Nr']) +
			// 			((float) $DataGarden['ICRRI5Nr']) +
			// 			((float) $DataGarden['RCC70Nr']) +
			// 			((float) $DataGarden['RCC71Nr']) +
			// 			((float) $DataGarden['RCC72Nr']) +
			// 			((float) $DataGarden['RCC73Nr']) +
			// 			((float) $DataGarden['LokalNr']) +
			// 			((float) $DataGarden['RCLNr']) +
			// 			((float) $DataGarden['THRNr']) +
			// 			((float) $DataGarden['APNr']) +
			// 			((float) $DataGarden['PRNr']) +
			// 			((float) $DataGarden['ScavinaNr']) +
			// 			((float) $DataGarden['MTNr']) +
			// 			((float) $DataGarden['M02Nr']) +
			// 			((float) $DataGarden['M04Nr']) +
			// 			((float) $DataGarden['M06Nr']) +
			// 			((float) $DataGarden['MHP03Nr']) +
			// 			((float) $DataGarden['MHP04Nr']) +
			// 			((float) $DataGarden['BB01Nr']) +
			// 			((float) $DataGarden['BLBNr']) +
			// 			((float) $DataGarden['BRTNr']) +
			// 			((float) $DataGarden['CloneLainGovCertNr']) +
			// 			((float) $DataGarden['CloneLainNr']);
			$Production = (int) $DataGarden['AnnualProduction'];
			$GardenHaUnCertified = (float) $DataGarden['GardenAreaHa'];

			if($GardenHaUnCertified != 0){
				$Productivity = $Production / $GardenHaUnCertified;
			}else{
				$Productivity = 0;
			}

			$TotalBulan = ((int) $DataGarden['NrLowSeasonMonths']) +
						((int) $DataGarden['NrHighSeasonMonths']);

			$TotalPanen = ((int) $DataGarden['LowSeasonProduction']) +
						((int) $DataGarden['HighSeasonProduction']);

			if($GardenHaUnCertified != 0){
				$TotalProductivity = $TotalPanen / $GardenHaUnCertified;
			}else{
				$TotalProductivity = 0;
			}

			$DoseFertilizerCPB = (float) $DataGarden['FertCPBDose'];
			$FrequentFertilizationCPB = (int) $DataGarden['FertCPBTimesYear'];
			$DoseFertilizerCPBYear = $DoseFertilizerCPB * $FrequentFertilizationCPB;

			$TotalPohonFertOrganic = 0;
			if((int) $DataGarden['KomposTBM'] == 1) $TotalPohonFertOrganic = $TotalPohonFertOrganic + ((int) $DataGarden['TreeTBM']);
			if((int) $DataGarden['KomposTM'] == 1) $TotalPohonFertOrganic = $TotalPohonFertOrganic + ((int) $DataGarden['TreeTM']);
			if((int) $DataGarden['KomposTR'] == 1) $TotalPohonFertOrganic = $TotalPohonFertOrganic + ((int) $DataGarden['TreeTR']);

			$FertilizationKomposDosePerFarm = (int) $DataGarden['FertCPBDose'];
			if($GardenHaUnCertified != 0){
				$FertilizationKomposDoseKgHaYear = ($FrequentFertilizationCPB * $FertilizationKomposDosePerFarm) / $GardenHaUnCertified;
			}else{
				$FertilizationKomposDoseKgHaYear = 0;
			}

			$FrKomposKandang = (float) $DataGarden['FertManureTimesYear'];
			$FrKomposKandangDosePerFarm = (int) $DataGarden['FertManureDose'];
			$DoseKomposKandang = (float) $DataGarden['FertManureDose'];
			$DoseKomposKandangYear = $FrKomposKandang * $DoseKomposKandang;
			$KgTotalKomposKandangYear = $DoseKomposKandangYear * $TotalPohonFertOrganic;
			if($GardenHaUnCertified != 0){
				$FrKomposKandangDoseKgHaYear = $FrKomposKandangDosePerFarm / $GardenHaUnCertified;
			}else{
				$FrKomposKandangDoseKgHaYear = 0;
			}

			$FrUrea = (int) $DataGarden['FertUreaTimesYear'];
			$FrUreaDosePerFarm = (float) $DataGarden['FertUreaDose'];
			if($GardenHaUnCertified != 0){
				$FrUreaDoseKgHaYear = $FrUreaDosePerFarm / $GardenHaUnCertified;
			}else{
				$FrUreaDoseKgHaYear = 0;
			}

			$FrDolomiteLime = (int) $DataGarden['FertDolomiteTimesYear'];
			$FrDolomiteLimeDosePerFarm = (float) $DataGarden['FertDolomiteDose'];
			if($GardenHaUnCertified != 0){
				$FrDolomiteLimeDoseKgHaYear = $FrDolomiteLimeDosePerFarm / $GardenHaUnCertified;
			}else{
				$FrDolomiteLimeDoseKgHaYear = 0;
			}

			$FrBorat = (int) $DataGarden['FertBoratTimesYear'];
			$FrBoratDosePerFarm = (float) $DataGarden['FertBoratDose'];
			if($GardenHaUnCertified != 0){
				$FrBoratDoseKgHaYear = $FrBoratDosePerFarm / $GardenHaUnCertified;
			}else{
				$FrBoratDoseKgHaYear = 0;
			}

			$FrZa = (int) $DataGarden['FertSSTimesYear'];
			$FrZaDosePerFarm = (float) $DataGarden['FertSSDose'];
			if($GardenHaUnCertified != 0){
				$FrZaDoseKgHaYear = $FrZaDosePerFarm / $GardenHaUnCertified;
			}else{
				$FrZaDoseKgHaYear = 0;
			}

			$FrTsp = (int) $DataGarden['FertTSPTimesYear'];
			$FrTspDosePerFarm = (float) $DataGarden['FertTSPDose'];
			if($GardenHaUnCertified != 0){
				$FrTspDoseKgHaYear = $FrTspDosePerFarm / $GardenHaUnCertified;
			}else{
				$FrTspDoseKgHaYear = 0;
			}

			$FrNpk = (int) $DataGarden['FertNPKTimesYear'];
			$FrNpkDosePerFarm = (float) $DataGarden['FertNPKDose'];
			if($GardenHaUnCertified != 0){
				$FrNpkDoseKgHaYear = $FrNpkDosePerFarm / $GardenHaUnCertified;
			}else{
				$FrNpkDoseKgHaYear = 0;
			}

			$FrKcl = (int) $DataGarden['FertKCLTimesYear'];
			$DoKcl = (float) $DataGarden['FertKCLDose'];
			$TotalKcl = $FrKcl * $DoKcl;
			//Variable Kalkulasi yg diperlukan untuk pengecekan =============== (End)

			for ($i=0; $i < count($DataDC); $i++) {
				switch($DataDC[$i]['ConKey']){
					case 'GardenHaUnCertified':
						$DataGarden['GardenAreaHa'] = (float) $DataGarden['GardenAreaHa'];
						if($DataGarden['GardenAreaHa'] != "" && $DataGarden['GardenAreaHa'] != 0){
							if($DataGarden['GardenAreaHa'] < 0.1 || $DataGarden['GardenAreaHa'] > 5){
								$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
							}
						}
					break;
					// case 'GardenDistance':
					// 	$DataGarden['GardenDistance'] = (float) $DataGarden['GardenDistance'];
					// 	if($DataGarden['GardenDistance'] != "" && $DataGarden['GardenDistance'] != 0){
					// 		if($DataGarden['GardenDistance'] > 10000){
					// 			$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 		}
					// 	}
					// break;
					case 'EstTotalCocoaTrees':
						$DataGarden['TreeTotalTBMTMTR'] = (float) $DataGarden['TreeTotalTBMTMTR'];
						if($DataGarden['TreeTotalTBMTMTR'] != "" && $DataGarden['TreeTotalTBMTMTR'] != 0){
							if($DataGarden['TreeTotalTBMTMTR'] < 100 || $DataGarden['TreeTotalTBMTMTR'] > 4000){
								$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
							}
						}
					break;
					case 'PohonTBM':
						$DataGarden['TreeTBM'] = (float) $DataGarden['TreeTBM'];
						if($DataGarden['TreeTBM'] != "" && $DataGarden['TreeTBM'] != 0){
							if($DataGarden['TreeTBM'] > 4000){
								$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
							}
						}
					break;
					case 'PohonTM':
						$DataGarden['TreeTM'] = (float) $DataGarden['TreeTM'];
						if($DataGarden['TreeTM'] != "" && $DataGarden['TreeTM'] != 0){
							if($DataGarden['TreeTM'] > 4000){
								$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
							}
						}
					break;
					case 'PohonRehab':
						$DataGarden['TreeTR'] = (float) $DataGarden['TreeTR'];
						if($DataGarden['TreeTR'] != "" && $DataGarden['TreeTR'] != 0){
							if($DataGarden['TreeTR'] > 4000){
								$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
							}
						}
					break;
					case 'PohonTotal':
						if($PohonTotal != $EstTotalCocoaTrees){
							$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
						}
					break;
					// case 'GraftedTrees':
					// 	$DataGarden['GraftedTrees'] = (float) $DataGarden['GraftedTrees'];
					// 	if($DataGarden['GraftedTrees'] > $PohonTotal){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'TopGraftedTrees':
					// 	$DataGarden['TopGraftedTrees'] = (float) $DataGarden['TopGraftedTrees'];
					// 	if($DataGarden['TopGraftedTrees'] > $PohonTotal){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'ReplantedTrees':
					// 	$DataGarden['ReplantedTrees'] = (float) $DataGarden['ReplantedTrees'];
					// 	if($DataGarden['ReplantedTrees'] > $PohonTotal){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'FarmRehabilitation':
					// 	if($DataGarden['FarmRehabilitation'] == "1" || $DataGarden['FarmRehabilitation'] == "4"){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'NonGovCertClone':
					// 	if(
					// 		$DataGarden['Lokal'] == "1" ||
					// 		$DataGarden['RCL'] == "1" ||
					// 		$DataGarden['THR'] == "1" ||
					// 		$DataGarden['AP'] == "1" ||
					// 		$DataGarden['Scavina'] == "1" ||
					// 		$DataGarden['PR'] == "1" ||
					// 		$DataGarden['M02'] == "1" ||
					// 		$DataGarden['MT'] == "1" ||
					// 		$DataGarden['M04'] == "1" ||
					// 		$DataGarden['M06'] == "1" ||
					// 		$DataGarden['MHP03'] == "1" ||
					// 		$DataGarden['MHP04'] == "1" ||
					// 		$DataGarden['BB01'] == "1" ||
					// 		$DataGarden['BLB'] == "1" ||
					// 		$DataGarden['BRT'] == "1" ||
					// 		$DataGarden['CloneLain'] == "1"
					// 	){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'TotalKlon':
					// 	if($TotalKlon != $EstTotalCocoaTrees){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					case 'ShadeTreesNr':
						$DataGarden['ShadeTreesNr'] = (float) $DataGarden['ShadeTreesNr'];
						if($DataGarden['ShadeTreesNr'] <= 0){
							$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
						}
					break;
					case 'ShadeTreesSpreadEvently':
						if($DataGarden['ShadeTreesSpreadEvently'] == "2"){
							$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
						}
					break;
					case 'Production':
						$DataGarden['AnnualProduction'] = (int) $DataGarden['AnnualProduction'];
						if($DataGarden['AnnualProduction'] < 50 || $DataGarden['AnnualProduction'] > 3000){
							$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
						}
					break;
					case 'PerkiraanProduktifitas':
						if($Productivity < 100 || $Productivity > 750){
							$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
						}
					break;
					case 'PanenTrekMonths':
						$DataGarden['NrLowSeasonMonths'] = (int) $DataGarden['NrLowSeasonMonths'];
						if($DataGarden['NrLowSeasonMonths'] > 10){
							$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
						}
					break;
					// case 'PanenBiasaMonths':
					// 	$DataGarden['PanenBiasaMonths'] = (int) $DataGarden['PanenBiasaMonths'];
					// 	if($DataGarden['PanenBiasaMonths'] > 10){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					case 'PanenRayaMonths':
						$DataGarden['NrHighSeasonMonths'] = (int) $DataGarden['NrHighSeasonMonths'];
						if($DataGarden['NrHighSeasonMonths'] > 6){
							$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
						}
					break;
					case 'PanenTrekKg':
						$DataGarden['LowSeasonProduction'] = (int) $DataGarden['LowSeasonProduction'];
						if($DataGarden['LowSeasonProduction'] > 100){
							$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
						}
					break;
					// case 'PanenBiasaKg':
					// 	$DataGarden['PanenBiasaKg'] = (int) $DataGarden['PanenBiasaKg'];
					// 	if($DataGarden['PanenBiasaKg'] > 150){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					case 'PanenRayaKg':
						$DataGarden['HighSeasonProduction'] = (int) $DataGarden['HighSeasonProduction'];
						if($DataGarden['HighSeasonProduction'] > 150){
							$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
						}
					break;
					case 'TotalBulan':
						if($TotalBulan != 12){
							$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
						}
					break;
					case 'TotalPanen':
						if($TotalPanen < 50 || $TotalPanen > 3000){
							$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
						}
					break;
					case 'TotalProductivity':
						if($TotalProductivity < 100 || $TotalProductivity > 750){
							$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
						}
					break;
					// case 'ProductionNext':
					// 	$DataGarden['ProductionNext'] = (int) $DataGarden['ProductionNext'];
					// 	$ProductionChangePercentage = abs(number_format((1 - $TotalPanen / $DataGarden['ProductionNext']) * 100,2));
					// 	if($ProductionChangePercentage > 20){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'WeedingMethod':
					// 	if($DataGarden['WeedingMethod'] == "3"){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'HealthHarvestHuskBurn':
					// 	if($DataGarden['HealthHarvestHuskBurn'] == "1"){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'HealthHarvestHuskDisposeOnFarm':
					// 	if($DataGarden['HealthHarvestHuskDisposeOnFarm'] == "1"){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'HealthHarvestHuskDisposeRiver':
					// 	if($DataGarden['HealthHarvestHuskDisposeRiver'] == "1"){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'PrunedPodInfestLeaveOnFarm':
					// 	if($DataGarden['PrunedPodInfestLeaveOnFarm'] == "1"){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'PrunedPodInfestRoraks':
					// 	if($DataGarden['PrunedPodInfestRoraks'] == "1"){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'PrunedPodInfestProcessCompost':
					// 	if($DataGarden['PrunedPodInfestProcessCompost'] == "1"){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'PrunedPodInfestDisposeRiver':
					// 	if($DataGarden['PrunedPodInfestDisposeRiver'] == "1"){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'PruningOptStructure':
					// 	if($DataGarden['PruningOptStructure'] == "2"){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'PruningBudInfected':
					// 	if($DataGarden['PruningBudInfected'] == "2"){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'RemovalInfestedPods':
					// 	if($DataGarden['RemovalInfestedPods'] == "2"){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'PruningNotProductive':
					// 	if($DataGarden['PruningNotProductive'] == "2"){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'PruningProtectPlants':
					// 	if($DataGarden['PruningProtectPlants'] == "2"){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'DisinfectedTools':
					// 	if($DataGarden['DisinfectedTools'] == "2"){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					case 'PestMain':
						if($DataGarden['PestMain'] != ""){
							$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
						}
					break;
					case 'DisMain':
						if($DataGarden['DisMain'] != ""){
							$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
						}
					break;
					// case 'RoutineMonitorPestInGarden':
					// 	if($DataGarden['RoutineMonitorPestInGarden'] == "2"){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'SoilPh':
					// 	$DataGarden['SoilPh'] = (float) $DataGarden['SoilPh'];
					// 	if($DataGarden['SoilPh'] < 5 || $DataGarden['SoilPh'] > 7.5){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'FarmAverageSlope':
					// 	if($DataGarden['FarmAverageSlope'] == "2"){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'SoilEroMethodNoEroCont':
					// 	if($DataGarden['SoilEroMethodNoEroCont'] == "1"){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'RainSeasonWaterStaying':
					// 	if($DataGarden['RainSeasonWaterStaying'] == "1"){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'NutriDefiN':
					// 	if($DataGarden['NutriDefiN'] == "1"){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'NutriDefiP':
					// 	if($DataGarden['NutriDefiP'] == "1"){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'NutriDefiK':
					// 	if($DataGarden['NutriDefiK'] == "1"){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					case 'FrequentFertilizationKompos':
						$DataGarden['FertCPBTimesYear'] = (int) $DataGarden['FertCPBTimesYear'];
						if($DataGarden['FertCPBTimesYear'] > 12){
							$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
						}
					break;
					case 'FertilizationKomposDosePerFarm':
						if($FertilizationKomposDosePerFarm > 10000){
							$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
						}
					break;
					case 'DoseFertilizerKompos':
						$DataGarden['FertCPBDose'] = (float) $DataGarden['FertCPBDose'];
						if($DataGarden['FertCPBDose'] > 10){
							$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
						}
					break;
					case 'DoseFertilizerKomposYear':
						if($DoseFertilizerKomposYear > 10){
							$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
						}
					break;
					case 'KgTotalFertilizerKomposYear':
						$KgTotalFertilizerKomposYear = $DoseFertilizerKomposYear * $TotalPohonFertOrganic;
						if($KgTotalFertilizerKomposYear > 10000){
							$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
						}
					break;
					case 'FertilizationKomposDoseKgHaYear':
						if($FertilizationKomposDoseKgHaYear > 10000){
							$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
						}
					break;
					case 'FrKomposKandang':
						if($FrKomposKandang > 12){
							$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
						}
					break;
					case 'FrKomposKandangDosePerFarm':
						if($FrKomposKandangDosePerFarm > 10000){
							$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
						}
					break;
					case 'DoseKomposKandang':
						if($DoseKomposKandang > 10){
							$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
						}
					break;
					case 'DoseKomposKandangYear':
						if($DoseKomposKandangYear > 10){
							$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
						}
					break;
					case 'KgTotalKomposKandangYear':
						if($KgTotalKomposKandangYear > 10000){
							$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
						}
					break;
					case 'FrKomposKandangDoseKgHaYear':
						if($FrKomposKandangDoseKgHaYear > 10000){
							$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
						}
					break;
					// case 'FrKomposCair':
					// 	$DataGarden['FrKomposCair'] = (float) $DataGarden['FrKomposCair'];
					// 	if($DataGarden['FrKomposCair'] > 0){
					// 		if($DataGarden['FrKomposCair'] < 6){
					// 			$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 		}
					// 	}
					// break;
					// case 'FrKomposGranula':
					// 	$DataGarden['FrKomposGranula'] = (float) $DataGarden['FrKomposGranula'];
					// 	if($DataGarden['FrKomposGranula'] > 0){
					// 		if($DataGarden['FrKomposGranula'] < 2){
					// 			$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 		}
					// 	}
					// break;
					case 'PakaiKompos':
						if($DataGarden['FertUseOrganic'] == "2"){
							$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
						}
					break;
					case 'TidakMemakaiKimia':
						if($DataGarden['FertNonOrganicData'] == "2"){
							$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
						}
					break;
					case 'FrUrea':
						if($FrUrea > 0){
							if($FrUrea < 2){
								$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
							}
						}
					break;
					case 'FrUreaDoseKgHaYear':
						if($FrUreaDoseKgHaYear > 500){
							$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
						}
					break;
					case 'FrDolomiteLime':
						if($FrDolomiteLime <= 0){
							$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
						}
					break;
					case 'FrDolomiteLimeDoseKgHaYear':
						if($FrDolomiteLimeDoseKgHaYear > 0){
							if($FrDolomiteLimeDoseKgHaYear < 1000){
								$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
							}
						}
					break;
					case 'FrBorat':
						if($FrBorat > 0){
							if($FrBorat < 2){
								$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
							}
						}
					break;
					case 'FrBoratDoseKgHaYear':
						if($FrBoratDoseKgHaYear > 1000){
							$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
						}
					break;
					case 'FrZa':
						if($FrZa > 0){
							if($FrZa < 2){
								$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
							}
						}
					break;
					case 'FrZaDoseKgHaYear':
						if($FrZaDoseKgHaYear > 500){
							$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
						}
					break;
					case 'FrTsp':
						if($FrTsp > 0){
							if($FrTsp < 2){
								$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
							}
						}
					break;
					case 'FrTspDoseKgHaYear':
						if($FrTspDoseKgHaYear > 500){
							$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
						}
					break;
					case 'FrNpk':
						if($FrNpk > 0){
							if($FrNpk < 2){
								$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
							}
						}
					break;
					case 'FrNpkDoseKgHaYear':
						if($FrTspDoseKgHaYear > 1000){
							$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
						}
					break;
					case 'FrKcl':
						if($FrKcl > 0){
							if($FrKcl < 2){
								$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
							}
						}
					break;
					case 'TotalKcl':
						if($TotalKcl > 500){
							$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
						}
					break;
					// case 'FrequentHerbisida':
					// 	$DataGarden['FrequentHerbisida'] = (int) $DataGarden['FrequentHerbisida'];
					// 	if($DataGarden['FrequentHerbisida'] > 12){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Herbisida2':
					// 	$DataGarden['Herbisida2'] = (int) $DataGarden['Herbisida2'];
					// 	if($DataGarden['Herbisida2'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Herbisida14':
					// 	$DataGarden['Herbisida14'] = (int) $DataGarden['Herbisida14'];
					// 	if($DataGarden['Herbisida14'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Herbisida12':
					// 	$DataGarden['Herbisida12'] = (int) $DataGarden['Herbisida12'];
					// 	if($DataGarden['Herbisida12'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Herbisida5':
					// 	$DataGarden['Herbisida5'] = (int) $DataGarden['Herbisida5'];
					// 	if($DataGarden['Herbisida5'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Herbisida25':
					// 	$DataGarden['Herbisida25'] = (int) $DataGarden['Herbisida25'];
					// 	if($DataGarden['Herbisida25'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Herbisida19':
					// 	$DataGarden['Herbisida19'] = (int) $DataGarden['Herbisida19'];
					// 	if($DataGarden['Herbisida19'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Herbisida24':
					// 	$DataGarden['Herbisida24'] = (int) $DataGarden['Herbisida24'];
					// 	if($DataGarden['Herbisida24'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Herbisida10':
					// 	$DataGarden['Herbisida10'] = (int) $DataGarden['Herbisida10'];
					// 	if($DataGarden['Herbisida10'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Herbisida9':
					// 	$DataGarden['Herbisida9'] = (int) $DataGarden['Herbisida9'];
					// 	if($DataGarden['Herbisida9'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Herbisida11':
					// 	$DataGarden['Herbisida11'] = (int) $DataGarden['Herbisida11'];
					// 	if($DataGarden['Herbisida11'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Herbisida3':
					// 	$DataGarden['Herbisida3'] = (int) $DataGarden['Herbisida3'];
					// 	if($DataGarden['Herbisida3'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Herbisida27':
					// 	$DataGarden['Herbisida27'] = (int) $DataGarden['Herbisida27'];
					// 	if($DataGarden['Herbisida27'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Herbisida15':
					// 	$DataGarden['Herbisida15'] = (int) $DataGarden['Herbisida15'];
					// 	if($DataGarden['Herbisida15'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Herbisida23':
					// 	$DataGarden['Herbisida23'] = (int) $DataGarden['Herbisida23'];
					// 	if($DataGarden['Herbisida23'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Herbisida20':
					// 	$DataGarden['Herbisida20'] = (int) $DataGarden['Herbisida20'];
					// 	if($DataGarden['Herbisida20'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Herbisida13':
					// 	$DataGarden['Herbisida13'] = (int) $DataGarden['Herbisida13'];
					// 	if($DataGarden['Herbisida13'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Herbisida8':
					// 	$DataGarden['Herbisida8'] = (int) $DataGarden['Herbisida8'];
					// 	if($DataGarden['Herbisida8'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Herbisida1':
					// 	$DataGarden['Herbisida1'] = (int) $DataGarden['Herbisida1'];
					// 	if($DataGarden['Herbisida1'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Herbisida17':
					// 	$DataGarden['Herbisida17'] = (int) $DataGarden['Herbisida17'];
					// 	if($DataGarden['Herbisida17'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Herbisida7':
					// 	$DataGarden['Herbisida7'] = (int) $DataGarden['Herbisida7'];
					// 	if($DataGarden['Herbisida7'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Herbisida4':
					// 	$DataGarden['Herbisida4'] = (int) $DataGarden['Herbisida4'];
					// 	if($DataGarden['Herbisida4'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Herbisida6':
					// 	$DataGarden['Herbisida6'] = (int) $DataGarden['Herbisida6'];
					// 	if($DataGarden['Herbisida6'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Herbisida18':
					// 	$DataGarden['Herbisida18'] = (int) $DataGarden['Herbisida18'];
					// 	if($DataGarden['Herbisida18'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Herbisida29':
					// 	$DataGarden['Herbisida29'] = (int) $DataGarden['Herbisida29'];
					// 	if($DataGarden['Herbisida29'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Herbisida21':
					// 	$DataGarden['Herbisida21'] = (int) $DataGarden['Herbisida21'];
					// 	if($DataGarden['Herbisida21'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Herbisida30':
					// 	$DataGarden['Herbisida30'] = (int) $DataGarden['Herbisida30'];
					// 	if($DataGarden['Herbisida30'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Herbisida26':
					// 	$DataGarden['Herbisida26'] = (int) $DataGarden['Herbisida26'];
					// 	if($DataGarden['Herbisida26'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'FrequentInsectisida':
					// 	$DataGarden['FrequentInsectisida'] = (int) $DataGarden['FrequentInsectisida'];
					// 	if($DataGarden['FrequentInsectisida'] > 0){
					// 		if($DataGarden['FrequentInsectisida'] >= 1 && $DataGarden['FrequentInsectisida'] <= 12){
					// 			$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 		}
					// 	}
					// break;
					// case 'Insectisida21':
					// 	$DataGarden['Insectisida21'] = (int) $DataGarden['Insectisida21'];
					// 	if($DataGarden['Insectisida21'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Insectisida9':
					// 	$DataGarden['Insectisida9'] = (int) $DataGarden['Insectisida9'];
					// 	if($DataGarden['Insectisida9'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Insectisida20':
					// 	$DataGarden['Insectisida20'] = (int) $DataGarden['Insectisida20'];
					// 	if($DataGarden['Insectisida20'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Insectisida10':
					// 	$DataGarden['Insectisida10'] = (int) $DataGarden['Insectisida10'];
					// 	if($DataGarden['Insectisida10'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Insectisida15':
					// 	$DataGarden['Insectisida15'] = (int) $DataGarden['Insectisida15'];
					// 	if($DataGarden['Insectisida15'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Insectisida6':
					// 	$DataGarden['Insectisida6'] = (int) $DataGarden['Insectisida6'];
					// 	if($DataGarden['Insectisida6'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Insectisida19':
					// 	$DataGarden['Insectisida19'] = (int) $DataGarden['Insectisida19'];
					// 	if($DataGarden['Insectisida19'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Insectisida12':
					// 	$DataGarden['Insectisida12'] = (int) $DataGarden['Insectisida12'];
					// 	if($DataGarden['Insectisida12'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Insectisida22':
					// 	$DataGarden['Insectisida22'] = (int) $DataGarden['Insectisida22'];
					// 	if($DataGarden['Insectisida22'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Insectisida2':
					// 	$DataGarden['Insectisida2'] = (int) $DataGarden['Insectisida2'];
					// 	if($DataGarden['Insectisida2'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Insectisida8':
					// 	$DataGarden['Insectisida8'] = (int) $DataGarden['Insectisida8'];
					// 	if($DataGarden['Insectisida8'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Insectisida7':
					// 	$DataGarden['Insectisida7'] = (int) $DataGarden['Insectisida7'];
					// 	if($DataGarden['Insectisida7'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Insectisida5':
					// 	$DataGarden['Insectisida5'] = (int) $DataGarden['Insectisida5'];
					// 	if($DataGarden['Insectisida5'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Insectisida23':
					// 	$DataGarden['Insectisida23'] = (int) $DataGarden['Insectisida23'];
					// 	if($DataGarden['Insectisida23'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Insectisida1':
					// 	$DataGarden['Insectisida1'] = (int) $DataGarden['Insectisida1'];
					// 	if($DataGarden['Insectisida1'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Fungisida':
					// 	$DataGarden['Fungisida'] = (int) $DataGarden['Fungisida'];
					// 	if($DataGarden['Fungisida'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'FrequentFungisida':
					// 	$DataGarden['FrequentFungisida'] = (int) $DataGarden['FrequentFungisida'];
					// 	if($DataGarden['FrequentFungisida'] > 12){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Fungisida6':
					// 	$DataGarden['Fungisida6'] = (int) $DataGarden['Fungisida6'];
					// 	if($DataGarden['Fungisida6'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Fungisida13':
					// 	$DataGarden['Fungisida13'] = (int) $DataGarden['Fungisida13'];
					// 	if($DataGarden['Fungisida13'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Fungisida10':
					// 	$DataGarden['Fungisida10'] = (int) $DataGarden['Fungisida10'];
					// 	if($DataGarden['Fungisida10'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Fungisida2':
					// 	$DataGarden['Fungisida2'] = (int) $DataGarden['Fungisida2'];
					// 	if($DataGarden['Fungisida2'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'Fungisida9':
					// 	$DataGarden['Fungisida9'] = (int) $DataGarden['Fungisida9'];
					// 	if($DataGarden['Fungisida9'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'PestApplies':
					// 	$DataGarden['PestApplies'] = (int) $DataGarden['PestApplies'];
					// 	if($DataGarden['PestApplies'] == 2){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'ApplyAltNonChemicalControlPests':
					// 	$DataGarden['ApplyAltNonChemicalControlPests'] = (int) $DataGarden['ApplyAltNonChemicalControlPests'];
					// 	if($DataGarden['ApplyAltNonChemicalControlPests'] == 2){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'UseOrganicControlPests':
					// 	$DataGarden['UseOrganicControlPests'] = (int) $DataGarden['UseOrganicControlPests'];
					// 	if($DataGarden['UseOrganicControlPests'] == 2){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'UseChemicalLowestToxicity':
					// 	$DataGarden['UseChemicalLowestToxicity'] = (int) $DataGarden['UseChemicalLowestToxicity'];
					// 	if($DataGarden['UseChemicalLowestToxicity'] == 2){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'UseChemicalLastChoice':
					// 	$DataGarden['UseChemicalLastChoice'] = (int) $DataGarden['UseChemicalLastChoice'];
					// 	if($DataGarden['UseChemicalLastChoice'] == 2){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'ApplyRotationStrategy':
					// 	$DataGarden['ApplyRotationStrategy'] = (int) $DataGarden['ApplyRotationStrategy'];
					// 	if($DataGarden['ApplyRotationStrategy'] == 2){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'NoticeUseInorganicFertilizer':
					// 	$DataGarden['NoticeUseInorganicFertilizer'] = (int) $DataGarden['NoticeUseInorganicFertilizer'];
					// 	if($DataGarden['NoticeUseInorganicFertilizer'] == 2){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'TrainedUseProperly':
					// 	$DataGarden['TrainedUseProperly'] = (int) $DataGarden['TrainedUseProperly'];
					// 	if($DataGarden['TrainedUseProperly'] == 2){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'MixPesticideLiquidFertilizer':
					// 	$DataGarden['MixPesticideLiquidFertilizer'] = (int) $DataGarden['MixPesticideLiquidFertilizer'];
					// 	if($DataGarden['MixPesticideLiquidFertilizer'] == 2){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'ExcessPesticideDisposedSafely':
					// 	$DataGarden['ExcessPesticideDisposedSafely'] = (int) $DataGarden['ExcessPesticideDisposedSafely'];
					// 	if($DataGarden['ExcessPesticideDisposedSafely'] == 2){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'GiveNoEntrySignAfterSpraying':
					// 	$DataGarden['GiveNoEntrySignAfterSpraying'] = (int) $DataGarden['GiveNoEntrySignAfterSpraying'];
					// 	if($DataGarden['GiveNoEntrySignAfterSpraying'] == 2){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'AdherePreHarvestInterval':
					// 	$DataGarden['AdherePreHarvestInterval'] = (int) $DataGarden['AdherePreHarvestInterval'];
					// 	if($DataGarden['AdherePreHarvestInterval'] == 2){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'EquipmentGoodCondition':
					// 	$DataGarden['EquipmentGoodCondition'] = (int) $DataGarden['EquipmentGoodCondition'];
					// 	if($DataGarden['EquipmentGoodCondition'] == 2){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'APD':
					// 	$DataGarden['APD'] = (int) $DataGarden['APD'];
					// 	if($DataGarden['APD'] == 2){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'UsePesticideInorganicFertilizer':
					// 	$DataGarden['UsePesticideInorganicFertilizer'] = (int) $DataGarden['UsePesticideInorganicFertilizer'];
					// 	if(
					// 		$DataGarden['UsePesticideInorganicFertilizer'] == 1 ||
					// 		$DataGarden['UsePesticideInorganicFertilizer'] == 2 ||
					// 		$DataGarden['UsePesticideInorganicFertilizer'] == 3
					// 	){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'TempatSimpanPestisida':
					// 	$DataGarden['TempatSimpanPestisida'] = (int) $DataGarden['TempatSimpanPestisida'];
					// 	if(
					// 		$DataGarden['TempatSimpanPestisida'] == 1 ||
					// 		$DataGarden['TempatSimpanPestisida'] == 3 ||
					// 		$DataGarden['TempatSimpanPestisida'] == 4
					// 	){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'StoreAccordanceOnLabel':
					// 	$DataGarden['StoreAccordanceOnLabel'] = (int) $DataGarden['StoreAccordanceOnLabel'];
					// 	if($DataGarden['StoreAccordanceOnLabel'] != 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'StoreOriginalPackaging':
					// 	$DataGarden['StoreOriginalPackaging'] = (int) $DataGarden['StoreOriginalPackaging'];
					// 	if($DataGarden['StoreOriginalPackaging'] != 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'StoreIndicationSuitablePlants':
					// 	$DataGarden['StoreIndicationSuitablePlants'] = (int) $DataGarden['StoreIndicationSuitablePlants'];
					// 	if($DataGarden['StoreIndicationSuitablePlants'] != 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'StoreAvoidPossibleSpill':
					// 	$DataGarden['StoreAvoidPossibleSpill'] = (int) $DataGarden['StoreAvoidPossibleSpill'];
					// 	if($DataGarden['StoreAvoidPossibleSpill'] != 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'StoreSecuredPlace':
					// 	$DataGarden['StoreSecuredPlace'] = (int) $DataGarden['StoreSecuredPlace'];
					// 	if($DataGarden['StoreSecuredPlace'] != 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'StoreFarFromProducts':
					// 	$DataGarden['StoreFarFromProducts'] = (int) $DataGarden['StoreFarFromProducts'];
					// 	if($DataGarden['StoreFarFromProducts'] != 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'HandlingCleanDry':
					// 	$DataGarden['HandlingCleanDry'] = (int) $DataGarden['HandlingCleanDry'];
					// 	if($DataGarden['HandlingCleanDry'] != 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'HandlingEnoughVentilationLight':
					// 	$DataGarden['HandlingEnoughVentilationLight'] = (int) $DataGarden['HandlingEnoughVentilationLight'];
					// 	if($DataGarden['HandlingEnoughVentilationLight'] != 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'HandlingStructurallySafe':
					// 	$DataGarden['HandlingStructurallySafe'] = (int) $DataGarden['HandlingStructurallySafe'];
					// 	if($DataGarden['HandlingStructurallySafe'] != 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'HandlingAntiAbsorptive':
					// 	$DataGarden['HandlingAntiAbsorptive'] = (int) $DataGarden['HandlingAntiAbsorptive'];
					// 	if($DataGarden['HandlingAntiAbsorptive'] != 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'HandlingClearWarningSign':
					// 	$DataGarden['HandlingClearWarningSign'] = (int) $DataGarden['HandlingClearWarningSign'];
					// 	if($DataGarden['HandlingClearWarningSign'] != 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'HandlingFirstAidInfo':
					// 	$DataGarden['HandlingFirstAidInfo'] = (int) $DataGarden['HandlingFirstAidInfo'];
					// 	if($DataGarden['HandlingFirstAidInfo'] != 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'HandlingProcedureEmergency':
					// 	$DataGarden['HandlingProcedureEmergency'] = (int) $DataGarden['HandlingProcedureEmergency'];
					// 	if($DataGarden['HandlingProcedureEmergency'] != 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'HandlingAreaCleanEye':
					// 	$DataGarden['HandlingAreaCleanEye'] = (int) $DataGarden['HandlingAreaCleanEye'];
					// 	if($DataGarden['HandlingAreaCleanEye'] != 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'BuangKemasanPestisida':
					// 	$DataGarden['BuangKemasanPestisida'] = (int) $DataGarden['BuangKemasanPestisida'];
					// 	if(
					// 		$DataGarden['BuangKemasanPestisida'] == 1 ||
					// 		$DataGarden['BuangKemasanPestisida'] == 2 ||
					// 		$DataGarden['BuangKemasanPestisida'] == 4 ||
					// 		$DataGarden['BuangKemasanPestisida'] == 5 ||
					// 		$DataGarden['BuangKemasanPestisida'] == 6
					// 	){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'ParticipateChildEducation':
					// 	$DataGarden['ParticipateChildEducation'] = (int) $DataGarden['ParticipateChildEducation'];
					// 	if($DataGarden['ParticipateChildEducation'] == 2){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'CutWageForDisciplinary':
					// 	$DataGarden['CutWageForDisciplinary'] = (int) $DataGarden['CutWageForDisciplinary'];
					// 	if($DataGarden['CutWageForDisciplinary'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'DoCutWageForWorker':
					// 	$DataGarden['DoCutWageForWorker'] = (int) $DataGarden['DoCutWageForWorker'];
					// 	if($DataGarden['DoCutWageForWorker'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'WagePaidByPerformance':
					// 	$DataGarden['WagePaidByPerformance'] = (int) $DataGarden['WagePaidByPerformance'];
					// 	if($DataGarden['WagePaidByPerformance'] == 2){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'PayingWorkerWageByPerformance':
					// 	$DataGarden['PayingWorkerWageByPerformance'] = (int) $DataGarden['PayingWorkerWageByPerformance'];
					// 	if($DataGarden['PayingWorkerWageByPerformance'] == 2){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'HandlingFirstAidInGarden':
					// 	$DataGarden['HandlingFirstAidInGarden'] = (int) $DataGarden['HandlingFirstAidInGarden'];
					// 	if($DataGarden['HandlingFirstAidInGarden'] == 2){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'FirstAidKitLocation':
					// 	$DataGarden['FirstAidKitLocation'] = (int) $DataGarden['FirstAidKitLocation'];
					// 	if($DataGarden['FirstAidKitLocation'] == 2){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'WorkerNotHandlePesticide':
					// 	$DataGarden['WorkerNotHandlePesticide'] = (int) $DataGarden['WorkerNotHandlePesticide'];
					// 	if($DataGarden['WorkerNotHandlePesticide'] == 2){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'WorkerAccessSafeDrinkingWater':
					// 	$DataGarden['WorkerAccessSafeDrinkingWater'] = (int) $DataGarden['WorkerAccessSafeDrinkingWater'];
					// 	if($DataGarden['WorkerAccessSafeDrinkingWater'] == 2){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'BufferZoneGarden':
					// 	$DataGarden['BufferZoneGarden'] = (int) $DataGarden['BufferZoneGarden'];
					// 	if($DataGarden['BufferZoneGarden'] == 2){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'LandOpeningForest':
					// 	$DataGarden['LandOpeningForest'] = (int) $DataGarden['LandOpeningForest'];
					// 	if($DataGarden['LandOpeningForest'] == 1){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'LandOpeningForestCertificate':
					// 	$DataGarden['LandOpeningForestCertificate'] = (int) $DataGarden['LandOpeningForestCertificate'];
					// 	if($DataGarden['LandOpeningForestCertificate'] == 2){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'IdentifyProtectRareSpecies':
					// 	$DataGarden['IdentifyProtectRareSpecies'] = (int) $DataGarden['IdentifyProtectRareSpecies'];
					// 	if($DataGarden['IdentifyProtectRareSpecies'] == 2){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
					// case 'SoilFertMonitoring':
					// 	$DataGarden['SoilFertMonitoring'] = (int) $DataGarden['SoilFertMonitoring'];
					// 	if($DataGarden['SoilFertMonitoring'] == 2){
					// 		$this->GenDataControlSurveyInsertItem($DataControlItemTabel,$DaconID,$DataDC[$i]['RefID']);
					// 	}
					// break;
				}
			}
		}
	}

}