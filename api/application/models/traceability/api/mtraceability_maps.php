<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mtraceability_maps extends CI_Model {
	private $sql;
	public function __construct(){
		parent::__construct();
	}

	public function getComboWarehouse()
	{
		$PartnerID = $_SESSION['PartnerID'];

		//harcode dulu untuk testing 
		// $PartnerID = '7';

		$sql = "SELECT 
					vso.SupplychainID AS id, 
					vso.`Name` AS label 
				FROM 
					view_tc_supplychain_org vso 
				WHERE 
					vso.PartnerID = '$PartnerID'
				AND 
					vso.ObjType = 'mill'
				ORDER BY label";
		$query = $this->db->query($sql);
		// echo "<pre>"; print_r($this->db->last_query()); echo "</pre>";exit;

		if ($query->num_rows()>0) {
		    $data =  $query->result_array();
		}else{
			$data = array();
		}
		return $data;
	}

	public function getComboTier2($get)
	{

		@$PartnerID = $this->db->query("SELECT PartnerID FROM view_tc_supplychain_org WHERE SupplychainID =?", array($get['WarehouseID']))->row()->PartnerID; 

		$sql = "SELECT 
				vso.SupplychainID AS id, 
				vso.`Name` AS label 
			FROM 
				view_tc_supplychain_org vso 
			WHERE 
				vso.PartnerID = '$PartnerID'
			AND 
				vso.ObjType = 'agent'
			AND 
				MRoleID = '9'
			ORDER BY label";

		$query = $this->db->query($sql);
		// echo "<pre>"; print_r($this->db->last_query()); echo "</pre>";exit;
		if ($query->num_rows()>0) {
		    return $data =  $query->result_array();
		}else{
			$data[0]['id'] = '';
			$data[0]['label'] = '';
		}
		return $data;
	}

	public function getComboTier1($get)
	{
		@$PartnerID = $this->db->query("SELECT PartnerID FROM view_tc_supplychain_org WHERE SupplychainID =?", array($get['WarehouseID']))->row()->PartnerID; 

		$sql = "SELECT 
				vso.SupplychainID AS id, 
				vso.`Name` AS label 
			FROM 
				view_tc_supplychain_org vso 
			WHERE 
				vso.PartnerID = '$PartnerID'
			AND 
				vso.ObjType = 'agent'
			ORDER BY label";

		$query = $this->db->query($sql);
		// echo "<pre>"; print_r($this->db->last_query()); echo "</pre>";exit;
		if ($query->num_rows()>0) {
		    return $data =  $query->result_array();
		}else{
			$data[0]['id'] = '';
			$data[0]['label'] = '';
		}
		return $data;
	}

	public function getMarkerRelation($get)
	{
		$date1 = @$get['StartDate'];
		$date2 = @$get['EndDate'];
		if($date1!='' && $date2!=''){
			$date1 = ''; $date2 = '';
		}else{
			$date1 = '/*'; $date2 = '*/';
		}

		if(@$get['WarehouseID']!=''){
			$wh1 = ''; $wh2 ='';
		}else{
			$wh1 = '/*'; $wh2 ='*/';
		}
		if(@$get['Tier1']!=''){
			$tier1a = ''; $tier1b ='';
		}else{
			$tier1a = '/*'; $tier1b ='*/';
		}
		if(@$get['Tier2']!=''){
			$tier2a = ''; $tier2b ='';
		}else{
			$tier2a = '/*'; $tier2b ='*/';
		}

		if(@$get['showFarmer']!=''){
			$select = "dt.latitude AS lat_0,
						dt.longitude AS long_0,
						'Farmer With Transactions' AS type_0,
						rpt.FarmerID,
						rpt.FarmerName,";
			$left_join = "
				LEFT JOIN (
					SELECT
						g.FarmerID,
						g.Latitude AS latitude, 
						g.Longitude AS longitude
						/*ST_Y(g.LatLong) AS latitude, 
						ST_X(g.LatLong) AS longitude*/
					FROM
						`ktv_cocoa_farmer_garden_status` g
					WHERE
						g.LatLong IS NOT NULL 
					GROUP BY g.FarmerID
				) dt ON dt.FarmerID=rpt.FarmerID";
				$where = "AND dt.latitude!=0 AND dt.longitude!=0";
		}else{
			$select = "";
			$left_join = "";
			$where = "";
		}

		if(@$get['to_id']!=''){
			$to_id1 = ''; $to_id2 ='';
		}else{
			$to_id1 = '/*'; $to_id2 ='*/';
		}
		$to_id = intval(@$get['to_id']);

		$sql = "SELECT DISTINCT
					$select
					loc.lat_1,
					loc.long_1,
					IF(orgtype_2='Gudang', 'Tier 1 Supplier', 'Tier 2 Supplier') AS type_1,
					IF(orgtype_2='Gudang', 'Tier 1 Supplier', IF(rpt.supplyorgid_1=? || rpt.supplyorgid_1=ch.SupplychainID, 'Tier 1 Supplier', 'Tier 2 Supplier')) AS type_1,
					ROUND((
						 3959 *
						 acos(cos(radians(37)) * 
						 cos(radians(IF(st.TransLatitude!=0, st.TransLatitude, rpt.lat_1))) * 
						 cos(radians(IF(st.TransLongitude!=0, st.TransLongitude, rpt.long_1)) - 
						 radians(-122)) + 
						 sin(radians(37)) * 
						 sin(radians(IF(st.TransLatitude!=0, st.TransLatitude, rpt.lat_1) )))
					), 1) AS distance,
					rpt.supplyorgid_1,
					rpt.name_1,
					
					rpt.lat_2, rpt.long_2, 
					IF(orgtype_2='Gudang', 'Warehouse', IF(orgtype_2!='', 'Tier 1 Supplier', NULL)) AS type_2, 
					rpt.supplyorgid_2, rpt.name_2,
					
					rpt.lat_3, rpt.long_3, 
					IF(orgtype_3='Gudang', 'Warehouse', IF(orgtype_3!='', 'Tier 1 Supplier', NULL)) AS type_3, 
					rpt.supplyorgid_3, rpt.name_3,
					
					rpt.lat_4, rpt.long_4, 
					IF(orgtype_4='Gudang', 'Warehouse', IF(orgtype_4!='', 'Tier 1 Supplier', NULL)) AS type_4,
					rpt.supplyorgid_4, rpt.name_4
					
				FROM
					`rpt_tc_trans_detail` rpt 
					LEFT JOIN ktv_supplychain_transaction st ON st.SupplyTransID=rpt.transid_1
					LEFT JOIN rpt_tc_trans_location_view loc ON loc.supplyorgid_1=rpt.supplyorgid_1 AND loc.distance=ROUND((
						 3959 *
						 acos(cos(radians(37)) * 
						 cos(radians(IF(st.TransLatitude!=0, st.TransLatitude, rpt.lat_1))) * 
						 cos(radians(IF(st.TransLongitude!=0, st.TransLongitude, rpt.long_1)) - 
						 radians(-122)) + 
						 sin(radians(37)) * 
						 sin(radians(IF(st.TransLatitude!=0, st.TransLatitude, rpt.lat_1) )))
					), 1)
					LEFT JOIN ktv_certification_holders ch ON ch.StatusCode='active' AND ch.SupplychainID=rpt.supplyorgid_1
					$left_join
				WHERE
					rpt.partnerid_1 = 8 
					$to_id1 AND ( (rpt.supplyorgid_1=$to_id) OR (rpt.supplyorgid_2=$to_id) OR (rpt.supplyorgid_3=$to_id) OR (rpt.supplyorgid_4=$to_id) ) $to_id2
					AND rpt.supplyorgid_1 NOT IN (870)
					$date1 AND rpt.date_1 BETWEEN ? AND ? $date2
					$wh1 AND IF(rpt.orgtype_5='Gudang', rpt.supplyorgid_5, IF(rpt.orgtype_4='Gudang', rpt.supplyorgid_4, IF(rpt.orgtype_3='Gudang', rpt.supplyorgid_3, IF(rpt.orgtype_2='Gudang', rpt.supplyorgid_2, NULL))))=? $wh2
					$tier1a AND (IF(rpt.orgtype_5='Gudang', rpt.supplyorgid_4, IF(rpt.orgtype_4='Gudang', rpt.supplyorgid_3, IF(rpt.orgtype_3='Gudang', rpt.supplyorgid_2, IF(rpt.orgtype_2='Gudang', rpt.supplyorgid_1, NULL))))=? OR (rpt.supplyorgid_1=? AND rpt.supplyorgid_2 IS NULL) ) $tier1b
					/*AND (rpt.supplyorgid_1=0 OR rpt.supplyorgid_2=0 OR rpt.supplyorgid_3=0 OR rpt.supplyorgid_4=0) */
					$tier2a AND rpt.supplyorgid_1=? $tier2b
					AND IF(st.TransLatitude!=0, st.TransLatitude, rpt.lat_1)!=0
					AND IF(st.TransLongitude!=0, st.TransLongitude, rpt.long_1)!=0
					AND rpt.supplyorgid_1 > 0
					$where
					AND (
						rpt.orgid_1 LIKE ? OR rpt.orgid_2 LIKE ? OR rpt.orgid_3 LIKE ? OR rpt.orgid_4 LIKE ?
						OR rpt.name_1 LIKE ? OR rpt.name_2 LIKE ? OR rpt.name_3 LIKE ? OR rpt.name_4 LIKE ?
						OR rpt.FarmerID LIKE ? OR rpt.FarmerName LIKE ?
					)";

		$query = $this->db->query($sql, array(@$get['Tier1'],
			@$get['StartDate'].' 00:00:00', @$get['EndDate'].' 23:59:59', @$get['WarehouseID'], 
			@$get['Tier1'], @$get['Tier1'],
			@$get['Tier2'],
			'%'.$get['key'].'%', '%'.$get['key'].'%', '%'.$get['key'].'%', '%'.$get['key'].'%', '%'.$get['key'].'%',
			'%'.$get['key'].'%', '%'.$get['key'].'%', '%'.$get['key'].'%', '%'.$get['key'].'%', '%'.$get['key'].'%'
		));
		//echo "<pre>".$this->db->last_query();die;
		if ($query->num_rows() > 0) {
			$data['data'] = $query->result_array();

			$sql = "SELECT
						COUNT(DISTINCT rpt.FarmerID) FarmerWithTransactions,
						COUNT(DISTINCT IF(orgtype_2='Gudang', NULL, rpt.supplyorgid_1)) AS Tier2Supplier,
						COUNT(DISTINCT IF(orgtype_2='Gudang', rpt.supplyorgid_1, IF(rpt.supplyorgid_2!='' && rpt.name_2!='', supplyorgid_2, NULL))) AS Tier1Supplier,
						COUNT(DISTINCT IF(rpt.orgtype_5='Gudang', rpt.supplyorgid_5, IF(rpt.orgtype_4='Gudang', rpt.supplyorgid_4, IF(rpt.orgtype_3='Gudang', rpt.supplyorgid_3, IF(rpt.orgtype_2='Gudang', rpt.supplyorgid_2, NULL))))) Warehouse
						
					FROM
						`rpt_tc_trans_detail` rpt 
						LEFT JOIN ktv_supplychain_transaction st ON st.SupplyTransID=rpt.transid_1
						LEFT JOIN rpt_tc_trans_location_view loc ON loc.supplyorgid_1=rpt.supplyorgid_1 AND loc.distance=ROUND((
							 3959 *
							 acos(cos(radians(37)) * 
							 cos(radians(IF(st.TransLatitude!=0, st.TransLatitude, rpt.lat_1))) * 
							 cos(radians(IF(st.TransLongitude!=0, st.TransLongitude, rpt.long_1)) - 
							 radians(-122)) + 
							 sin(radians(37)) * 
							 sin(radians(IF(st.TransLatitude!=0, st.TransLatitude, rpt.lat_1) )))
						), 1)
					WHERE
						rpt.partnerid_1 = 8 
						AND rpt.supplyorgid_1 NOT IN (870)
						$date1 AND rpt.date_1 BETWEEN ? AND ? $date2
						$wh1 AND IF(rpt.orgtype_5='Gudang', rpt.supplyorgid_5, IF(rpt.orgtype_4='Gudang', rpt.supplyorgid_4, IF(rpt.orgtype_3='Gudang', rpt.supplyorgid_3, IF(rpt.orgtype_2='Gudang', rpt.supplyorgid_2, NULL))))=? $wh2
						$tier1a AND (IF(rpt.orgtype_5='Gudang', rpt.supplyorgid_4, IF(rpt.orgtype_4='Gudang', rpt.supplyorgid_3, IF(rpt.orgtype_3='Gudang', rpt.supplyorgid_2, IF(rpt.orgtype_2='Gudang', rpt.supplyorgid_1, NULL))))=? OR (rpt.supplyorgid_1=? AND rpt.supplyorgid_2 IS NULL) ) $tier1b
						/*AND (rpt.supplyorgid_1=0 OR rpt.supplyorgid_2=0 OR rpt.supplyorgid_3=0 OR rpt.supplyorgid_4=0) */
						$tier2a AND rpt.supplyorgid_1=? $tier2b
						AND IF(st.TransLatitude!=0, st.TransLatitude, rpt.lat_1)!=0
						AND IF(st.TransLongitude!=0, st.TransLongitude, rpt.long_1)!=0
						AND rpt.supplyorgid_1 > 0
						AND (
							rpt.orgid_1 LIKE ? OR rpt.orgid_2 LIKE ? OR rpt.orgid_3 LIKE ? OR rpt.orgid_4 LIKE ?
							OR rpt.name_1 LIKE ? OR rpt.name_2 LIKE ? OR rpt.name_3 LIKE ? OR rpt.name_4 LIKE ?
							OR rpt.FarmerID LIKE ? OR rpt.FarmerName LIKE ?
						)";

			$query2 = $this->db->query($sql, array(
				@$get['StartDate'].' 00:00:00', @$get['EndDate'].' 23:59:59', @$get['WarehouseID'], 
				@$get['Tier1'], @$get['Tier1'],
				@$get['Tier2'],
				'%'.$get['key'].'%', '%'.$get['key'].'%', '%'.$get['key'].'%', '%'.$get['key'].'%', '%'.$get['key'].'%',
				'%'.$get['key'].'%', '%'.$get['key'].'%', '%'.$get['key'].'%', '%'.$get['key'].'%', '%'.$get['key'].'%'
			));

			$data['total'] = $query2->row_array();
			//echo "<pre>".$this->db->last_query();die;

			return $data;
		}
		return array(
			'data' => array()
		);
	}

	public function getMarkerRelationPalm($get)
	{
		$date1 = @$get['StartDate'];
		$date2 = @$get['EndDate'];
		if($date1!='' && $date2!=''){
			$date1 = ''; $date2 = '';
		}else{
			$date1 = '/*'; $date2 = '*/';
		}

		if(@$get['WarehouseID']!=''){
			$wh1 = ''; $wh2 ='';
		}else{
			$wh1 = '/*'; $wh2 ='*/';
		}
		if(@$get['Tier1']!=''){
			$tier1a = ''; $tier1b ='';
		}else{
			$tier1a = '/*'; $tier1b ='*/';
		}
		if(@$get['Tier2']!=''){
			$tier2a = ''; $tier2b ='';
		}else{
			$tier2a = '/*'; $tier2b ='*/';
		}
	
		@$WarehouseID = $this->db->query("SELECT SupplychainID FROM view_tc_supplychain_org WHERE SupplychainID =?", array($get['WarehouseID']))->row()->SupplychainID; 
		// if(@$get['showFarmer']!=''){

			$start = $get['StartDate'];
			$end   = $get['EndDate'];
			$key   = $get['key'];
			
			$sql = "SELECT 
						vso.SupplychainID AS wh_supplychainid, 
						'Mill' AS `type_1`,
						vso.`Name` AS wh_name,
						vso.Latitude AS `lat_1`,
						vso.Longitude AS `long_1`,
						
						IF(vso2.SupplychainID=vso3.SupplychainID OR vso3.SupplychainID IS NULL, '', vso2.SupplychainID) `2_supplychainid`, 
						IF(vso2.SupplychainID=vso3.SupplychainID OR vso3.SupplychainID IS NULL, 'DO', 'DO') `2_orgtype`,
						IF(vso2.SupplychainID=vso3.SupplychainID OR vso3.SupplychainID IS NULL, '', vso2.`Name`) `2_name`,
						IF(vso2.SupplychainID=vso3.SupplychainID OR vso3.SupplychainID IS NULL, vso.Latitude, vso2.Latitude) `2_latitude`,
						IF(vso2.SupplychainID=vso3.SupplychainID OR vso3.SupplychainID IS NULL, vso.Longitude, vso2.Longitude) `2_longitude`,

						IF(vso2.SupplychainID=vso3.SupplychainID OR vso3.SupplychainID IS NULL, vso2.SupplychainID, vso3.SupplychainID) `1_supplychainid`, 
						IF(vso2.SupplychainID=vso3.SupplychainID OR vso3.SupplychainID IS NULL, 'Agent/Dealer', 'Agent/Dealer') `1_orgtype`,
						IF(vso2.SupplychainID=vso3.SupplychainID OR vso3.SupplychainID IS NULL, vso2.`Name`, vso3.`Name`) `1_name`,
						IF(vso2.SupplychainID=vso3.SupplychainID OR vso3.SupplychainID IS NULL, vso2.Latitude, vso3.Latitude) `1_latitude`,
						IF(vso2.SupplychainID=vso3.SupplychainID OR vso3.SupplychainID IS NULL, vso2.Longitude, vso3.Longitude) `1_longitude`,

						m.MemberID AS '3_supplychainid', 
						'Farmer With Transactions' AS '3_orgtype',
						m.`MemberName` AS '3_name',
						st.Latitude AS `3_latitude`,
						st.Longitude AS `3_longitude`

					FROM
						ktv_tc_supplychain_transaction st
						LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID = st.SupplyBatchID AND st.SupplyType = 'Farmer'
						LEFT JOIN ktv_members m ON m.MemberID = st.SupplyID
						LEFT JOIN ktv_tc_supplychain_delivery_detail ktsdd ON ktsdd.SupplyBatchID = sb.SupplyBatchID
						LEFT JOIN ktv_tc_supplychain_delivery ktsd ON ktsd.DeliveryID = ktsdd.DeliveryID
						LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID = ktsd.SupplyDestMillOrgID
						LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID = ktsd.SupplyDestDoOrgID 
						LEFT JOIN view_tc_supplychain_org vso3 ON vso3.SupplychainID = ktsd.SupplychainID 
					WHERE
							vso.SupplychainID = '$WarehouseID'
						AND 
							ktsd.DeliveryStatusID = '4'
						AND 
							st.latitude IS NOT NULL
						AND 
							DATE_FORMAT(st.DateTransaction,'%Y-%m-%d') BETWEEN '$start' AND '$end'
						AND (
							vso2.name LIKE '%$key%' OR vso3.name LIKE '%$key%' OR vso.name LIKE '%$key%' OR m.membername LIKE '%$key%'
						)
						GROUP BY ktsdd.DeliveryID ";
						
			$query = $this->db->query($sql);
			
			if ($query->num_rows() > 0) {
				
				$data['data'] = $query->result_array();
				
				$start = $get['StartDate'];
				$end   = $get['EndDate'];

				$sql = "SELECT
				COUNT(DISTINCT dt.SupplyDestMillOrgID ) AS Warehouse,
				COUNT(DISTINCT dt.SupplyChainID ) AS Tier1Supplier,
				COUNT(DISTINCT dt.SupplyDestDoOrgID ) AS Tier2Supplier,
				COUNT(DISTINCT dt.SupplyTransID) AS FarmerWithTransactions
				FROM
				(   SELECT
					ktsd.SupplyDestMillOrgID,
					ktsd.SupplyChainID,  
					ktsd.SupplyDestDoOrgID, 
					st.SupplyTransID
					FROM
					ktv_tc_supplychain_transaction st
					LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID=st.SupplyBatchID AND st.SupplyType = 'Farmer'
					LEFT JOIN ktv_members m ON m.MemberID = st.SupplyID
					LEFT JOIN ktv_tc_supplychain_delivery_detail ktsdd ON ktsdd.SupplyBatchID = sb.SupplyBatchID
					LEFT JOIN ktv_tc_supplychain_delivery ktsd ON ktsd.DeliveryID = ktsdd.DeliveryID
					LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID = ktsd.SupplyDestMillOrgID
					LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID = ktsd.SupplyDestDoOrgID 
					LEFT JOIN view_tc_supplychain_org vso3 ON vso3.SupplychainID = ktsd.SupplychainID 
				WHERE
					vso.SupplychainID ='$WarehouseID'
				AND	
					ktsd.DeliveryStatusID = '4'
				AND 
					st.latitude IS NOT NULL
				AND 
					DATE_FORMAT(st.DateTransaction,'%Y-%m-%d') BETWEEN '$start' AND '$end'
				GROUP BY ktsdd.DeliveryID
			   ) dt";

				$query2 = $this->db->query($sql);

				$data['total'] = $query2->row_array();
				
				return $data;
		}
	}

	public function getMarkerRelationFarmer($get)
	{
		//echo "<pre>".print_r($get, 1);die;
		$date1 = @$get['StartDate'];
		$date2 = @$get['EndDate'];
		if($date1!='' && $date2!=''){
			$date1 = ''; $date2 = '';
		}else{
			$date1 = '/*'; $date2 = '*/';
		}

		if(@$get['WarehouseID']!=''){
			$wh1 = ''; $wh2 ='';
		}else{
			$wh1 = '/*'; $wh2 ='*/';
		}
		if(@$get['Tier1']!=''){
			$tier1a = ''; $tier1b ='';
		}else{
			$tier1a = '/*'; $tier1b ='*/';
		}
		if(@$get['Tier2']!=''){
			$tier2a = ''; $tier2b ='';
		}else{
			$tier2a = '/*'; $tier2b ='*/';
		}
		if(@$get['to_id']!=''){
			$to1 = ''; $to2 ='';
		}else{
			$to1 = '/*'; $to2 ='*/';
		}

		$sql = "SELECT
					loc.lat_1,
					loc.long_1,
					IF(orgtype_2='Gudang', 'Tier 1 Supplier', 'Tier 2 Supplier') AS type_1,
					ROUND((
						 3959 *
						 acos(cos(radians(37)) * 
						 cos(radians(IF(st.TransLatitude!=0, st.TransLatitude, rpt.lat_1))) * 
						 cos(radians(IF(st.TransLongitude!=0, st.TransLongitude, rpt.long_1)) - 
						 radians(-122)) + 
						 sin(radians(37)) * 
						 sin(radians(IF(st.TransLatitude!=0, st.TransLatitude, rpt.lat_1) )))
					), 1) AS distance,
					rpt.supplyorgid_1,
					rpt.name_1,
					
					dt.latitude AS lat_0,
					dt.longitude AS long_0,
					'Farmer With Transactions' AS type_0,
					rpt.FarmerID,
					rpt.FarmerName
					
				FROM
					`rpt_tc_trans_detail` rpt 
					LEFT JOIN ktv_supplychain_transaction st ON st.SupplyTransID=rpt.transid_1
					LEFT JOIN rpt_tc_trans_location_view loc ON loc.supplyorgid_1=rpt.supplyorgid_1 AND loc.distance=ROUND((
						 3959 *
						 acos(cos(radians(37)) * 
						 cos(radians(IF(st.TransLatitude!=0, st.TransLatitude, rpt.lat_1))) * 
						 cos(radians(IF(st.TransLongitude!=0, st.TransLongitude, rpt.long_1)) - 
						 radians(-122)) + 
						 sin(radians(37)) * 
						 sin(radians(IF(st.TransLatitude!=0, st.TransLatitude, rpt.lat_1) )))
					), 1)
					LEFT JOIN (
						SELECT
							g.FarmerID,
							g.Latitude AS latitude, 
							g.Longitude AS longitude
							/*ST_Y(g.LatLong) AS latitude, 
							ST_X(g.LatLong) AS longitude*/
						FROM
							`ktv_cocoa_farmer_garden_status` g
						WHERE
							g.LatLong IS NOT NULL 
						GROUP BY g.FarmerID
					) dt ON dt.FarmerID=rpt.FarmerID
				WHERE
					rpt.partnerid_1 = 8 
					AND rpt.supplyorgid_1 NOT IN (870)
					$date1 AND rpt.date_1 BETWEEN ? AND ? $date2
					$wh1 AND IF(rpt.orgtype_5='Gudang', rpt.supplyorgid_5, IF(rpt.orgtype_4='Gudang', rpt.supplyorgid_4, IF(rpt.orgtype_3='Gudang', rpt.supplyorgid_3, IF(rpt.orgtype_2='Gudang', rpt.supplyorgid_2, NULL))))=? $wh2
					$tier1a AND (IF(rpt.orgtype_5='Gudang', rpt.supplyorgid_4, IF(rpt.orgtype_4='Gudang', rpt.supplyorgid_3, IF(rpt.orgtype_3='Gudang', rpt.supplyorgid_2, IF(rpt.orgtype_2='Gudang', rpt.supplyorgid_1, NULL))))=? OR (rpt.supplyorgid_1=? AND rpt.supplyorgid_2 IS NULL) ) $tier1b
					/*AND (rpt.supplyorgid_1=0 OR rpt.supplyorgid_2=0 OR rpt.supplyorgid_3=0 OR rpt.supplyorgid_4=0) */
					$tier2a AND rpt.supplyorgid_1=? $tier2b
					$to1 
						AND rpt.supplyorgid_1=? 
						AND loc.lat_1 = ?
						AND loc.long_1 = ?
					$to2
					AND IF(st.TransLatitude!=0, st.TransLatitude, rpt.lat_1)!=0
					AND IF(st.TransLongitude!=0, st.TransLongitude, rpt.long_1)!=0
					AND dt.latitude!=0
					AND dt.latitude!=0
					AND rpt.supplyorgid_1 > 0
					AND (
						rpt.orgid_1 LIKE ? OR rpt.orgid_2 LIKE ? OR rpt.orgid_3 LIKE ? OR rpt.orgid_4 LIKE ?
						OR rpt.name_1 LIKE ? OR rpt.name_2 LIKE ? OR rpt.name_3 LIKE ? OR rpt.name_4 LIKE ?
						OR rpt.FarmerID LIKE ? OR rpt.FarmerName LIKE ?
					)
				GROUP BY rpt.FarmerID";

		$query = $this->db->query($sql, array(
			@$get['StartDate'].' 00:00:00', @$get['EndDate'].' 23:59:59', @$get['WarehouseID'], 
			@$get['Tier1'], @$get['Tier1'],
			@$get['Tier2'], 
			@$get['to_id'], @$get['lat'], @$get['long'],
			'%'.$get['key'].'%', '%'.$get['key'].'%', '%'.$get['key'].'%', '%'.$get['key'].'%', '%'.$get['key'].'%',
			'%'.$get['key'].'%', '%'.$get['key'].'%', '%'.$get['key'].'%', '%'.$get['key'].'%', '%'.$get['key'].'%'
		));
		//echo "<pre>".$this->db->last_query();die;
		if ($query->num_rows() > 0) {
			$data = $query->result_array();
			return $data;
		}
		return array();
	}

	public function getMarkerRelationFarmerNotSales($get)
	{
		$sql = "SELECT GROUP_CONCAT(DistrictID) AS DistrictID FROM `ktv_supplychain_area` WHERE SupplychainID=?";
		$query = $this->db->query($sql, array(@$get['Tier1']));
		if($query->num_rows() > 0){
			$DistrictID  = $query->row()->DistrictID;
		}else{
			$DistrictID = 0;
		}
		//echo "<pre>".print_r($get, 1);die;
		$date1 = @$get['StartDate'];
		$date2 = @$get['EndDate'];
		if($date1!='' && $date2!=''){
			$date1 = ''; $date2 = '';
		}else{
			$date1 = '/*'; $date2 = '*/';
		}

		$sql = "SELECT rpt.FarmerID
				FROM
					`rpt_tc_trans_detail` rpt
				WHERE
					rpt.partnerid_1 = 8 
					AND rpt.supplyorgid_1 NOT IN (870)
					$date1 AND rpt.date_1 BETWEEN ? AND ? $date2
					
				GROUP BY rpt.FarmerID";

		$query = $this->db->query($sql, array(@$get['StartDate'].' 00:00:00', @$get['EndDate'].' 23:59:59'
		));
		//echo "<pre>".$this->db->last_query();die;
		if ($query->num_rows() > 0) {
			$data = $query->result_array();
			$FarmerID = '';
			$x = 0;
			foreach ($data as $key => $value) {
				if($x==0){
					$koma = '';
				}else{
					$koma = ',';
				}
				$FarmerID .= $koma.$value['FarmerID'];
			}
		}else{
			$FarmerID = '0';
		}

		$sql = "SELECT
					g.FarmerID AS SupplierID,
					g.FarmerID AS LocationID,
					g.Latitude AS Latitude, 
					g.Longitude AS Longitude,
					'Farmer Not Selling' AS Tipe,
					'' AS LatitudeParent,
					'' AS LongitudeParent
					/*ST_Y(g.LatLong) AS latitude, 
					ST_X(g.LatLong) AS longitude*/
				FROM
					`ktv_cocoa_farmer_garden_status` g
					LEFT JOIN ktv_cocoa_farmer f ON f.FarmerID=g.FarmerID
					LEFT JOIN ktv_cocoa_farmer_type ft ON ft.FarmerID=f.FarmerID
					LEFT JOIN ktv_ref_farmer_type rft ON rft.FarmertypeID=ft.FarmertypeID
					LEFT JOIN ktv_village v ON v.VillageID=f.VillageID
					LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
					LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
				WHERE
					g.LatLong IS NOT NULL 
					AND f.StatusCode = 'active'
					AND rft.PartnerID = 8
					AND g.FarmerID NOT IN ($FarmerID)
					AND d.DistrictID IN ($DistrictID)
					AND (f.FarmerID LIKE ? OR f.FarmerName LIKE ?)
				GROUP BY g.FarmerID
				/*LIMIT 100*/";
		$query = $this->db->query($sql, array('%'.$get['key'].'%', '%'.$get['key'].'%'));
		if ($query->num_rows() > 0) {
			$farmer = $query->result_array();
			return $farmer;
		}

		return array();
	}

	public function getDetailActorPalm($get){
		// if($get['type']=='Farmer With Transactions' || $get['type']=='Farmer Not Selling'){
		if($get['type']=='DO'){

			$idx = explode("_", $get['id']);

			$SupplychainID = $idx[0];

			$sql = "SELECT 
                    dt.id, 
                    dt.name, 
                    COUNT(DISTINCT dt.SupplyTransID) transaction_count,
                    COUNT(DISTINCT dt.SupplyBatchID) batch_count,
                    IFNULL(SUM(dt.bruto), 0) bruto,
					IFNULL(SUM(dt.netto), 0) netto,
					dt.Village,
                    dt.SubDistrict,
                    dt.District,
                    dt.Province
                FROM
                (
                    SELECT
                        IFNULL(km.MemberDisplayID, vso.ObjID) AS id,
						vso.Name AS name,
                        st.SupplyTransID,
                        st.SupplyBatchID,
                        st.VolumeBruto bruto,
                        st.VolumeNetto netto,
						v.Village,
                        sd.SubDistrict,
                        d.District,
                        p.Province
                    FROM
                        view_tc_supplychain_org org 				
                        LEFT JOIN ktv_tc_supplychain_transaction st ON st.SupplychainID=org.SupplychainID
                        LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID=st.SupplyBatchID
                        LEFT JOIN ktv_tc_supplychain_delivery_detail ktsdd ON ktsdd.SupplyBatchID = sb.SupplyBatchID
                        LEFT JOIN ktv_tc_supplychain_delivery ktsd ON ktsd.DeliveryID = ktsdd.DeliveryID
                        LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=ktsd.SupplyDestDoOrgID

						LEFT JOIN ktv_supplychain_org kso ON kso.SupplychainID=org.SupplychainID 
                        LEFT JOIN ktv_village v ON v.VillageID=org.VillageID
                        LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
                        LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
                        LEFT JOIN ktv_province p ON p.ProvinceID=d.ProvinceID

						LEFT JOIN ktv_members_extension kme ON kme.MemberID = org.ObjID
                        LEFT JOIN ktv_members km ON km.MemberID = vso.ObjID
                        WHERE
                        km.StatusCode = 'active'
                        AND vso.SupplychainID = ? 
						
                    GROUP BY st.SupplyTransID
                ) dt";
        // $query = $this->db->query($sql, array($id, "$start 00:00:00", "$end 23:59:59"));
        $query = $this->db->query($sql, array($SupplychainID));
		// echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
		
		if ($query->num_rows()>0) {
			$data = $query->row_array(0);
				$return = array(
					'id' => $data['id'],
					'type' => $get['type'],
					'name' => $data['name'],
					'transaction' => $data['transaction_count'],
					'location' => $data['Village'].', '.$data['SubDistrict'].', '.$data['District'].', '.$data['Province']
				);
			return $return;
			}
		} elseif($get['type']=='Agent/Dealer'){

			$idx = explode("_", $get['id']);

			$SupplychainID = $idx[0];

			$sql = "SELECT 
                    dt.id, 
                    dt.name, 
                    COUNT(DISTINCT dt.SupplyTransID) transaction_count,
                    COUNT(DISTINCT dt.SupplyBatchID) batch_count,
                    IFNULL(SUM(dt.bruto), 0) bruto,
					IFNULL(SUM(dt.netto), 0) netto,
					dt.Village,
                    dt.SubDistrict,
                    dt.District,
                    dt.Province
                FROM
                (
                    SELECT
                        IFNULL(km.MemberDisplayID, org.ObjID) id,
                        IFNULL(kme.agCompanyName, org.Name) AS name,
                        st.SupplyTransID,
                        st.SupplyBatchID,
                        st.VolumeBruto bruto,
                        st.VolumeNetto netto,
						v.Village,
                        sd.SubDistrict,
                        d.District,
                        p.Province
                    FROM
                        view_tc_supplychain_org org 				
                        LEFT JOIN ktv_tc_supplychain_transaction st ON st.SupplychainID=org.SupplychainID
                        LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID=st.SupplyBatchID
                        LEFT JOIN ktv_tc_supplychain_delivery_detail ktsdd ON ktsdd.SupplyBatchID = sb.SupplyBatchID
                        LEFT JOIN ktv_tc_supplychain_delivery ktsd ON ktsd.DeliveryID = ktsdd.DeliveryID
                        LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=ktsd.SupplyDestMillOrgID

						LEFT JOIN ktv_supplychain_org kso ON kso.SupplychainID=org.SupplychainID 
                        LEFT JOIN ktv_village v ON v.VillageID=org.VillageID
                        LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
                        LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
                        LEFT JOIN ktv_province p ON p.ProvinceID=d.ProvinceID

						LEFT JOIN ktv_members_extension kme ON kme.MemberID = org.ObjID
                        LEFT JOIN ktv_members km ON km.MemberID = kme.MemberID
                        WHERE
                        km.StatusCode = 'active'
                        AND org.SupplychainID = ? 
						
                    GROUP BY st.SupplyTransID
                ) dt";
			// $query = $this->db->query($sql, array($id, "$start 00:00:00", "$end 23:59:59"));
			$query = $this->db->query($sql, array($SupplychainID));
		
			if ($query->num_rows()>0) {
				$data = $query->row_array(0);
					$return = array(
						'id' => $data['id'],
						'type' => $get['type'],
						'name' => $data['name'],
						'transaction' => $data['transaction_count'],
						'location' => $data['Village'].', '.$data['SubDistrict'].', '.$data['District'].', '.$data['Province']
					);
				return $return;
			}

		} elseif($get['type']=='Farmer With Transactions'){
			$idx = explode("_", $get['id']);

			$SupplychainID = $idx[0];

			$sql = "SELECT
						km.MemberDisplayID as id,
						km.MemberName as name,
						COUNT(DISTINCT st.SupplyTransID) transaction_count,
                    	COUNT(DISTINCT st.SupplyBatchID) batch_count,
						SUM(st.VolumeBruto) AS bruto,
						SUM(st.VolumeNetto) AS netto,
						v.Village,
						kv.SubDistrict,
						d.District,
						p.Province
					FROM
						ktv_members km
					LEFT JOIN ktv_tc_supplychain_transaction st ON st.SupplyID = km.MemberID
					LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID = st.SupplyBatchID
					LEFT JOIN ktv_tc_supplychain_delivery sd ON sd.SupplychainID = sb.SupplyOrgID
					LEFT JOIN ktv_tc_supplychain_delivery_detail ktsd ON ktsd.DeliveryID = sd.DeliveryID
						
					LEFT JOIN ktv_village v ON v.VillageID = km.VillageID
					LEFT JOIN ktv_subdistrict kv ON kv.SubDistrictID=v.SubDistrictID
					LEFT JOIN ktv_district d ON d.DistrictID= kv.DistrictID
					LEFT JOIN ktv_province p ON p.ProvinceID= d.ProvinceID

					WHERE
						km.StatusCode = 'active'
					AND 
						km.MemberID = ?";
			// $query = $this->db->query($sql, array($id, "$start 00:00:00", "$end 23:59:59"));
			$query = $this->db->query($sql, array($SupplychainID));
		
			if ($query->num_rows()>0) {
				$data = $query->row_array(0);
					$return = array(
						'id' => $data['id'],
						'type' => $get['type'],
						'name' => $data['name'],
						'transaction' => $data['transaction_count'],
						'location' => $data['Village'].', '.$data['SubDistrict'].', '.$data['District'].', '.$data['Province']
					);
				return $return;
			}
		} else{

			$sql = "SELECT 
					dt.id, 
					dt.name, 
					COUNT( dt.SupplyTransID) transaction_count,
					COUNT( dt.SupplyBatchID) batch_count,
					IFNULL(SUM(dt.bruto), 0) bruto,
					IFNULL(SUM(dt.netto), 0) netto,
					dt.Village,
					dt.SubDistrict,
					dt.District,
					dt.Province
				FROM
				(
				SELECT
					IFNULL(km.MemberDisplayID, org.ObjID) id,
					IFNULL(kme.agCompanyName, org.Name) AS name,
					st.SupplyTransID,
					st.SupplyBatchID,
					st.VolumeBruto bruto,
					st.VolumeNetto netto,
					v.Village,
					sd.SubDistrict,
					d.District,
					p.Province
				FROM
				ktv_tc_supplychain_transaction st
				LEFT JOIN ktv_tc_supplychain_batch ktsb ON ktsb.SupplyBatchID = st.SupplyBatchID
				LEFT JOIN ktv_tc_supplychain_delivery ktsd ON ktsd.SupplychainID = st.SupplychainID
				LEFT JOIN ktv_tc_supplychain_delivery_detail ktsdd ON ktsdd.DeliveryID = ktsd.DeliveryID
				LEFT JOIN view_tc_supplychain_org org ON org.SupplychainID=ktsd.SupplyDestMillOrgID

				LEFT JOIN ktv_supplychain_org kso ON kso.SupplychainID=org.SupplychainID 
				LEFT JOIN ktv_village v ON v.VillageID=org.VillageID
				LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
				LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
				LEFT JOIN ktv_province p ON p.ProvinceID=d.ProvinceID

				LEFT JOIN ktv_members_extension kme ON kme.MemberID = org.ObjID
                LEFT JOIN ktv_members km ON km.MemberID = kme.MemberID
				WHERE
				org.SupplychainID = ?
				AND ktsd.DeliveryStatusID = '4'
				AND ktsd.StatusCode = 'active'
				GROUP BY 		
				ktsdd.DeliveryID 
		) dt";

		$query = $this->db->query($sql, array($get['WarehouseID']));
		// echo "<pre>".$this->db->last_query();die;
		
		if ($query->num_rows()>0) {
			$data = $query->row_array(0);
				$return = array(
					'id' => $data['id'],
					'type' => $get['type'],
					'name' => $data['name'],
					'transaction' => $data['transaction_count'],
					'location' => $data['Village'].', '.$data['SubDistrict'].', '.$data['District'].', '.$data['Province']
				);
			}

			return $return;
		}
	}

	public function getDetailTransaction($get){
		if($get['type']=='Farmer With Transactions' || $get['type']=='Farmer Not Selling'){
			$idx = explode("_", $get['id']);
			$get['id'] = @$idx[1];
			$FarmerID = $idx[0];
			$get['lat'] = @$get['LatitudeParent'];
			$get['long'] = @$get['LongitudeParent'];
			$to1 = ''; $to2 ='';
			$select = "CONCAT(rpt.orgid_1,' - ',rpt.name_1) AS 'Destination'
					/*IF(rpt.supplyorgid_1=?, CONCAT(rpt.FarmerID, ' - ', rpt.FarmerName), IF(rpt.supplyorgid_2=?, CONCAT(rpt.orgid_1,' - ',rpt.name_1), IF(rpt.supplyorgid_3=?, CONCAT(rpt.orgid_2,' - ',rpt.name_2), CONCAT(rpt.orgid_3,' - ',rpt.name_3)))) AS 'From'*/";
		}else{
			$to1 = '/*'; $to2 ='*/';
			$select = "IF(rpt.supplyorgid_1=?, CONCAT(rpt.FarmerID, ' - ', rpt.FarmerName), IF(rpt.supplyorgid_2=?, CONCAT(rpt.orgid_1,' - ',rpt.name_1), IF(rpt.supplyorgid_3=?, CONCAT(rpt.orgid_2,' - ',rpt.name_2), CONCAT(rpt.orgid_3,' - ',rpt.name_3)))) AS 'From'";
		}
		
		$date1 = @$get['StartDate'];
		$date2 = @$get['EndDate'];
		if($date1!='' && $date2!=''){
			$date1 = ''; $date2 = '';
		}else{
			$date1 = '/*'; $date2 = '*/';
		}

		if(@$get['WarehouseID']!=''){
			$wh1 = ''; $wh2 ='';
		}else{
			$wh1 = '/*'; $wh2 ='*/';
		}
		if(@$get['Tier1']!=''){
			$tier1a = ''; $tier1b ='';
		}else{
			$tier1a = '/*'; $tier1b ='*/';
		}
		if(@$get['Tier2']!=''){
			$tier2a = ''; $tier2b ='';
		}else{
			$tier2a = '/*'; $tier2b ='*/';
		}

		$sql = "SELECT
					IFNULL(st.InvoiceNumber, IFNULL(st.SupplyID, sbf.SupplyBatchNumber)) AS 'Transaction Number',
					SUBSTR(IFNULL(st.DateTransaction, sbf.DeliveryDate), 1,10) AS `date`,
					IFNULL(st.FAQVolumeBruto, sbf.VolumeBruto) AS bruto,
					IFNULL(st.FAQVolumeNetto, sbf.DestWeight) AS netto,
					IF(st.SupplyTransID IS NOT NULL, IF(sb.SupplyBatchID IS NULL, 'Open Transaction', sb.SupplyDestStatus), 'Pending') AS `Status`,
					$select
				FROM
					`rpt_tc_trans_detail` rpt 
					LEFT JOIN ktv_supplychain_transaction st ON st.SupplyTransID=
						IF(rpt.supplyorgid_1=?, rpt.transid_1, IF(rpt.supplyorgid_2=?, rpt.transid_2, IF(rpt.supplyorgid_3=?, rpt.transid_3, rpt.transid_4)))
					LEFT JOIN ktv_supplychain_batch sb ON  sb.SupplyBatchID=st.SupplyBatchID
					LEFT JOIN ktv_supplychain_batch sbf ON st.SupplyTransID IS NULL AND sbf.SupplyBatchID=IF(rpt.supplyorgid_1=?, NULL, IF(rpt.supplyorgid_2=?, rpt.batchid_1, IF(rpt.supplyorgid_3=?, rpt.batchid_2, rpt.batchid_3)))
					LEFT JOIN rpt_tc_trans_location_view loc ON loc.supplyorgid_1=rpt.supplyorgid_1 AND loc.distance=ROUND((
						 3959 *
						 acos(cos(radians(37)) * 
						 cos(radians(IF(st.TransLatitude!=0, st.TransLatitude, rpt.lat_1))) * 
						 cos(radians(IF(st.TransLongitude!=0, st.TransLongitude, rpt.long_1)) - 
						 radians(-122)) + 
						 sin(radians(37)) * 
						 sin(radians(IF(st.TransLatitude!=0, st.TransLatitude, rpt.lat_1) )))
					), 1)
				WHERE
					rpt.partnerid_1 = 8 
					AND rpt.supplyorgid_1 NOT IN (870)
					AND (rpt.supplyorgid_1=? OR rpt.supplyorgid_2=? OR rpt.supplyorgid_3=? OR rpt.supplyorgid_4=?)
					AND IF(rpt.supplyorgid_1=?, loc.lat_1, IF(rpt.supplyorgid_2=?, rpt.lat_2, IF(rpt.supplyorgid_3=?, rpt.lat_3, rpt.lat_4)))=?
					AND IF(rpt.supplyorgid_1=?, loc.long_1, IF(rpt.supplyorgid_2=?, rpt.long_2, IF(rpt.supplyorgid_3=?, rpt.long_3, rpt.long_4)))=?
					$date1 AND rpt.date_1 BETWEEN ? AND ? $date2
					$wh1 AND IF(rpt.orgtype_5='Gudang', rpt.supplyorgid_5, IF(rpt.orgtype_4='Gudang', rpt.supplyorgid_4, IF(rpt.orgtype_3='Gudang', rpt.supplyorgid_3, IF(rpt.orgtype_2='Gudang', rpt.supplyorgid_2, NULL))))=? $wh2
					$tier1a AND (IF(rpt.orgtype_5='Gudang', rpt.supplyorgid_4, IF(rpt.orgtype_4='Gudang', rpt.supplyorgid_3, IF(rpt.orgtype_3='Gudang', rpt.supplyorgid_2, IF(rpt.orgtype_2='Gudang', rpt.supplyorgid_1, NULL))))=? OR (rpt.supplyorgid_1=? AND rpt.supplyorgid_2 IS NULL) ) $tier1b
					/*AND (rpt.supplyorgid_1=0 OR rpt.supplyorgid_2=0 OR rpt.supplyorgid_3=0 OR rpt.supplyorgid_4=0) */
					$tier2a AND rpt.supplyorgid_1=? $tier2b
					AND IF(st.TransLatitude!=0, st.TransLatitude, rpt.lat_1)!=0
					AND IF(st.TransLongitude!=0, st.TransLongitude, rpt.long_1)!=0
					AND rpt.supplyorgid_1 > 0
					$to1 
						AND rpt.supplyorgid_1=? 
						AND loc.lat_1 = ?
						AND loc.long_1 = ?
						AND rpt.FarmerID = ?
					$to2
					AND (
						rpt.orgid_1 LIKE ? OR rpt.orgid_2 LIKE ? OR rpt.orgid_3 LIKE ? OR rpt.orgid_4 LIKE ?
						OR rpt.name_1 LIKE ? OR rpt.name_2 LIKE ? OR rpt.name_3 LIKE ? OR rpt.name_4 LIKE ?
						OR rpt.FarmerID LIKE ? OR rpt.FarmerName LIKE ?
					)
					
				GROUP BY IFNULL(st.SupplyTransID, sbf.SupplyBatchID)";

		$query = $this->db->query($sql, array(
			@$get['id'], @$get['id'], @$get['id'],
			@$get['id'], @$get['id'], @$get['id'],
			@$get['id'], @$get['id'], @$get['id'], @$get['id'], @$get['id'], @$get['id'], @$get['id'],
			@$get['id'], @$get['id'], @$get['id'], @$get['lat'],
			@$get['id'], @$get['id'], @$get['id'], @$get['long'],
			@$get['StartDate'].' 00:00:00', @$get['EndDate'].' 23:59:59', @$get['WarehouseID'], 
			@$get['Tier1'], @$get['Tier1'],
			@$get['Tier2'],
			@$get['id'], @$get['lat'], @$get['long'], @$FarmerID,
			'%'.$get['key'].'%', '%'.$get['key'].'%', '%'.$get['key'].'%', '%'.$get['key'].'%', '%'.$get['key'].'%',
			'%'.$get['key'].'%', '%'.$get['key'].'%', '%'.$get['key'].'%', '%'.$get['key'].'%', '%'.$get['key'].'%'
		));
		//echo "<pre>".$this->db->last_query();die;
		if ($query->num_rows() > 0) {
			$data = $query->result_array();
			return $data;
		}
		return array();
	}

	public function getDetailTransactionPalm($get){
		// if($get['type']=='Farmer With Transactions' || $get['type']=='Farmer Not Selling'){
		// 	$idx = explode("_", $get['id']);
		// 	$get['id'] = @$idx[1];
		// 	$FarmerID = $idx[0];
		// 	$get['lat'] = @$get['LatitudeParent'];
		// 	$get['long'] = @$get['LongitudeParent'];
		// 	$to1 = ''; $to2 ='';
		// 	$select = "CONCAT(rpt.orgid_1,' - ',rpt.name_1) AS 'Destination'
		// 			/*IF(rpt.supplyorgid_1=?, CONCAT(rpt.FarmerID, ' - ', rpt.FarmerName), IF(rpt.supplyorgid_2=?, CONCAT(rpt.orgid_1,' - ',rpt.name_1), IF(rpt.supplyorgid_3=?, CONCAT(rpt.orgid_2,' - ',rpt.name_2), CONCAT(rpt.orgid_3,' - ',rpt.name_3)))) AS 'From'*/";
		// }else{
		// 	$to1 = '/*'; $to2 ='*/';
		// 	$select = "IF(rpt.supplyorgid_1=?, CONCAT(rpt.FarmerID, ' - ', rpt.FarmerName), IF(rpt.supplyorgid_2=?, CONCAT(rpt.orgid_1,' - ',rpt.name_1), IF(rpt.supplyorgid_3=?, CONCAT(rpt.orgid_2,' - ',rpt.name_2), CONCAT(rpt.orgid_3,' - ',rpt.name_3)))) AS 'From'";
		// }
		
		// $date1 = @$get['StartDate'];
		// $date2 = @$get['EndDate'];
		// if($date1!='' && $date2!=''){
		// 	$date1 = ''; $date2 = '';
		// }else{
		// 	$date1 = '/*'; $date2 = '*/';
		// }

		// if(@$get['WarehouseID']!=''){
		// 	$wh1 = ''; $wh2 ='';
		// }else{
		// 	$wh1 = '/*'; $wh2 ='*/';
		// }
		// if(@$get['Tier1']!=''){
		// 	$tier1a = ''; $tier1b ='';
		// }else{
		// 	$tier1a = '/*'; $tier1b ='*/';
		// }
		// if(@$get['Tier2']!=''){
		// 	$tier2a = ''; $tier2b ='';
		// }else{
		// 	$tier2a = '/*'; $tier2b ='*/';
		// }

		if($get['type']=='DO'){

			$idx = explode("_", $get['id']);

			$SupplychainID = $idx[0];

			$sql = "SELECT
					DATE_FORMAT(st.DateTransaction, '%Y-%m-%d') AS 'date',
					st.TransNumber AS 'Transaction Number',
					st.VolumeBruto AS bruto,
					st.VolumeNetto AS netto,
					sb.SupplyBatchStatus AS Status
				FROM
					view_tc_supplychain_org org 				
				LEFT JOIN ktv_tc_supplychain_transaction st ON st.SupplychainID=org.SupplychainID
				LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID=st.SupplyBatchID
				LEFT JOIN ktv_tc_supplychain_delivery_detail ktsdd ON ktsdd.SupplyBatchID = sb.SupplyBatchID
				LEFT JOIN ktv_tc_supplychain_delivery ktsd ON ktsd.DeliveryID = ktsdd.DeliveryID
				LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=ktsd.SupplyDestDoOrgID

				LEFT JOIN ktv_supplychain_org kso ON kso.SupplychainID=org.SupplychainID 
				LEFT JOIN ktv_village v ON v.VillageID=org.VillageID
				LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
				LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
				LEFT JOIN ktv_province p ON p.ProvinceID=d.ProvinceID

				LEFT JOIN ktv_members_extension kme ON kme.MemberID = org.ObjID
				LEFT JOIN ktv_members km ON km.MemberID = kme.MemberID
				WHERE
					km.StatusCode = 'active'
				AND 
					vso.SupplychainID = ? 
				AND 
					DATE_FORMAT(st.DateTransaction,'%Y-%m-%d') BETWEEN ? AND ? 
				GROUP BY 
					st.SupplyTransID 
				ORDER BY 
					st.DateTransaction DESC";
				$query = $this->db->query($sql, array($SupplychainID,$get['StartDate'],$get['EndDate']));
				// echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
				if ($query->num_rows() > 0) {
					$data = $query->result_array();
					return $data;
				}

			return array();

		} elseif($get['type']=='Agent/Dealer'){
			$idx = explode("_", $get['id']);

			$SupplychainID = $idx[0];

			$sql = "SELECT
					DATE_FORMAT(st.DateTransaction, '%Y-%m-%d') AS 'date',
					st.TransNumber AS 'Transaction Number',
					st.VolumeBruto AS bruto,
					st.VolumeNetto AS netto,
					sb.SupplyBatchStatus AS Status
				FROM
					view_tc_supplychain_org org 				
				LEFT JOIN ktv_tc_supplychain_transaction st ON st.SupplychainID=org.SupplychainID
				LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID=st.SupplyBatchID
				LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID=sb.SupplyDestOrgID
				LEFT JOIN ktv_tc_supplychain_delivery_detail ktsdd ON ktsdd.SupplyBatchID = sb.SupplyBatchID
				LEFT JOIN ktv_tc_supplychain_delivery ktsd ON ktsd.DeliveryID = ktsdd.DeliveryID
				LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=ktsd.SupplyDestMillOrgID

				LEFT JOIN ktv_supplychain_org kso ON kso.SupplychainID=org.SupplychainID 
				LEFT JOIN ktv_village v ON v.VillageID=org.VillageID
				LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
				LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
				LEFT JOIN ktv_province p ON p.ProvinceID=d.ProvinceID

				LEFT JOIN ktv_members_extension kme ON kme.MemberID = org.ObjID
				LEFT JOIN ktv_members km ON km.MemberID = kme.MemberID
				WHERE
					km.StatusCode = 'active'
				AND 
					org.SupplychainID = ? 
				AND 
					DATE_FORMAT(st.DateTransaction,'%Y-%m-%d') BETWEEN ? AND ? 
				GROUP BY 
					st.SupplyTransID 
				ORDER BY 
					st.DateTransaction DESC";
				$query = $this->db->query($sql, array($SupplychainID,$get['StartDate'],$get['EndDate']));
				// echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
				if ($query->num_rows() > 0) {
					$data = $query->result_array();
					return $data;
				}

			return array();

		} elseif($get['type'] == 'Farmer With Transactions'){

			$idx = explode("_", $get['id']);

			$SupplychainID = $idx[0];

			$sql = "SELECT
					DATE_FORMAT(st.DateTransaction, '%Y-%m-%d') AS 'date',
					st.TransNumber AS 'Transaction Number',
					st.VolumeBruto AS bruto,
					st.VolumeNetto AS netto,
					sb.SupplyBatchStatus AS Status
				FROM
					ktv_tc_supplychain_transaction st
                LEFT JOIN 
                	ktv_members km ON km.MemberID = st.SupplyID
				LEFT JOIN
                	ktv_tc_supplychain_batch sb ON sb.SupplyBatchID = st.SupplyBatchID
				WHERE
					km.StatusCode = 'active'
				AND 
					km.MemberID = ? 
				AND 
					DATE_FORMAT(st.DateTransaction,'%Y-%m-%d') BETWEEN ? AND ? 
				GROUP BY 
					st.SupplyTransID 
				ORDER BY 
					st.DateTransaction DESC";
				$query = $this->db->query($sql, array($SupplychainID,$get['StartDate'],$get['EndDate']));
				// echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
				if ($query->num_rows() > 0) {
					$data = $query->result_array();
					return $data;
				}

			return array();

		} else {
			$sql = "SELECT
					DATE_FORMAT(st.DateTransaction, '%Y-%m-%d') AS 'date',
					st.TransNumber AS 'Transaction Number',
					'Received' batch_status,
					st.TransNumber batch_number,
					IFNULL(vso2.`Name`, IF(st.SupplyBatchSourceType='1', IFNULL(st.MillOther,'-'), IFNULL(st.DOOther,'-'))) batch_from,
					st.VolumeBruto bruto,
					st.VolumeNetto netto
				FROM
					ktv_tc_supplychain_transaction st
					LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID=st.SupplyBatchID 
					LEFT JOIN ktv_tc_supplychain_transaction st2 ON st2.SupplyBatchID=sb2.SupplyBatchID
					LEFT JOIN ktv_tc_supplychain_batch sb3 ON sb3.SupplyBatchID=st2.SupplyID 
					LEFT JOIN ktv_tc_supplychain_transaction st3 ON st3.SupplyBatchID=sb3.SupplyBatchID

					LEFT JOIN ktv_tc_supplychain_delivery_detail ktsdd ON ktsdd.SupplyBatchID = sb2.SupplyBatchID
					LEFT JOIN ktv_tc_supplychain_delivery ktsd ON ktsd.DeliveryID = ktsdd.DeliveryID
					LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=ktsd.SupplyDestMillOrgID

					LEFT JOIN ktv_supplychain_org kso ON kso.SupplychainID=vso.SupplychainID 
					LEFT JOIN ktv_village v ON v.VillageID=vso.VillageID
					LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
					LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
					LEFT JOIN ktv_province p ON p.ProvinceID=d.ProvinceID

					LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID=IF(st.SupplyBatchType='Untraceable', IF(st.SupplyBatchSourceType='1', st.MIllID, st.DOID), sb2.SupplyOrgID)
					LEFT JOIN view_tc_supplychain_org vso3 ON vso3.SupplychainID=IF(st2.SupplyBatchType='Untraceable', IF(st2.SupplyBatchSourceType='1', st2.MIllID, st2.DOID), sb3.SupplyOrgID)
				WHERE
					ktsd.SupplyDestMillOrgID = ?
					AND ktsd.DeliveryStatusID = '4'
					AND ktsd.StatusCode = 'active'
					AND vso3.SupplychainID IS NULL
					AND DATE_FORMAT(st.DateTransaction,'%Y-%m-%d') BETWEEN ? AND ? GROUP BY ktsdd.DeliveryID ORDER BY st.DateTransaction DESC";
					$query = $this->db->query($sql, array($get['WarehouseID'],$get['StartDate'],$get['EndDate']));
			// echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
			if ($query->num_rows() > 0) {
				$data = $query->result_array();
				return $data;
			}

			return array();
		}
	}

	//============================================================================================================//

	public function getDistrict($ProvinceID)
	{
		$where    = '';
		$params   = [];
		$params[] = $ProvinceID;
		// if ($_SESSION['is_admin'] == '0') {
			$where = " AND ac.UserId = ?";
			$params[] = $_SESSION['userid'];
		// }
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
		// echo "<pre>"; print_r($this->db->last_query()); echo "</pre>";exit;
		if ($query->num_rows()>0) {
		    return $query->result_array();
		}
		return false;
	}

	public function getCertificateHolders($get){
		if(intval($get['ProvinceID']) > 0){
			$where1 = "AND d.ProvinceID='".intval($get['ProvinceID'])."'";
		}else{
			return array();
		}
		if(intval($get['DistrictID']) > 0){
			$where2 = "AND d.DistrictID='".intval($get['DistrictID'])."'";
		}else{
			$where2 = "";
		}
		$sql = "SELECT
					ch.SupplychainID AS id,
					CONCAT(ch.CertHolderOrgName, ' (', vso.Name,')') AS label
				FROM
					ktv_certification_holders ch
					LEFT JOIN view_supplychain_org vso ON vso.SupplychainID=ch.SupplychainID
					LEFT JOIN ktv_village v ON v.VillageID=vso.VillageID
					LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
					LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
				WHERE
					ch.StatusCode = 'active' AND vso.PartnerID=8
					$where1 $where2
				ORDER BY label";
		$query = $this->db->query($sql);
		
		if ($query->num_rows()>0) {
		    return $query->result_array();
		}
		return array();
	}

	public function getBuyingStations($get){
		if(intval($get['ProvinceID']) > 0){
			$where1 = "AND d.ProvinceID='".intval($get['ProvinceID'])."'";
		}else{
			return array();
		}
		if(intval($get['DistrictID']) > 0){
			$where2 = "AND d.DistrictID='".intval($get['DistrictID'])."'";
		}else{
			$where2 = "";
		}
		if(intval($get['CertHolderID']) > 0){
			$where3 = "AND ch.SupplychainID='".intval($get['CertHolderID'])."'";
		}else{
			$sql = "SELECT GROUP_CONCAT( DISTINCT ch.SupplychainID) AS id
					FROM
						ktv_certification_holders ch
						LEFT JOIN view_supplychain_org vso ON vso.SupplychainID=ch.SupplychainID
						LEFT JOIN ktv_village v ON v.VillageID=vso.VillageID
						LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
						LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
					WHERE
						ch.StatusCode = 'active' AND vso.PartnerID=8
						$where1 $where2";
			$CertHolderID = $this->db->query($sql)->row()->id;
			if($CertHolderID==''){
				$CertHolderID = 0;
			}
			$where3 = "AND ch.SupplychainID IN ($CertHolderID)";
		}
		$sql = "SELECT DISTINCT 
					vso2.SupplychainID AS id,
					vso2.Name AS label
				FROM
					ktv_certification_holders ch
					LEFT JOIN view_supplychain_org vso ON vso.SupplychainID=ch.SupplychainID
					LEFT JOIN ktv_village v ON v.VillageID=vso.VillageID
					LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
					LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
					LEFT JOIN ktv_supplychain_org_rel rel ON rel.ParentOrgId=ch.SupplychainID
					LEFT JOIN view_supplychain_org vso2 ON vso2.SupplychainID=rel.ChildOrgId
					LEFT JOIN ktv_certification_holders ch2 ON ch2.SupplychainID=vso2.SupplychainID
					LEFT JOIN ktv_supplychain_org kso2 ON kso2.SupplychainID=rel.ChildOrgId
				WHERE
					ch.StatusCode = 'active' AND vso.PartnerID=8
					AND ch2.SupplychainID IS NULL
					AND vso2.SupplychainID IS NOT NULL 
					AND kso2.OrgType!='koperasi'
					$where1 $where2 $where3
				ORDER BY label";
		$query = $this->db->query($sql);
		//echo "<pre>".$this->db->last_query();die;
		if ($query->num_rows()>0) {
		    return $query->result_array();
		}
		return array();
	}

	public function getFarmerOrg($ProvinceID, $DistrictID, $key = '')
	{
		$where  = '';
		$params = [];
		$sql = "
SELECT
	kc.`CoopID`
	, kc.`CoopID` AS ID
	, CoopName AS Name
	, kc.Latitude
	, kc.Longitude
	, Village
	, SubDistrict
	, IFNULL(FarmerName,StaffName) as StaffName
FROM ktv_cooperatives kc
-- LEFT JOIN ktv_supplychain_org so ON so.OrgID = kc.CoopID AND so.OrgType = 'koperasi'
-- LEFT JOIN ktv_supplychain_org_partner sop ON sop.SupplychainID = so.SupplychainID
JOIN ktv_village v ON kc.VillageID=v.VillageID
JOIN ktv_subdistrict sd ON v.SubDistrictID=sd.SubDistrictID
JOIN ktv_district d ON d.DistrictID = sd.DistrictID
JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
LEFT JOIN ktv_cooperative_staff kcs ON kc.CoopID=kcs.CoopID and Position='ketua'
LEFT JOIN ktv_cocoa_farmer kcf ON kcs.FarmerID=kcf.FarmerID
LEFT JOIN ktv_access_staff st ON st.DistrictID = d.DistrictID
INNER JOIN ktv_cooperative_access kca ON kc.`CoopID` = kca.`CoopID` AND kca.`PartnerID` = {$_SESSION['PartnerID']}
WHERE 1 = 1
	AND kc.StatusCode != 'nullified'
	AND ABS(kc.Latitude) > 0 AND ABS(kc.Longitude) > 0
   -- where --
GROUP BY kc.CoopID
        ";

		$where .= ' AND st.UserId = ?';
		$params[] = intval($_SESSION['userid']);

		if (!empty($ProvinceID)) {
			$where .= " AND p.ProvinceID = ?";
			$params[] = intval($ProvinceID);
		}
		if (!empty($DistrictID)) {
			$where .= " AND d.DistrictID = ?";
			$params[] = intval($DistrictID);
		}
		if (!empty($key)) {
			$where .= " AND (CoopName LIKE '%{$key}%')";
		}
		$sql = str_replace("-- where --", $where, $sql);
		$query = $this->db->query($sql, $params);
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
		return false;
	}

	public function getTrader($ProvinceID, $DistrictID, $SupplychainID, $key = '', $date='', $show_all='')
	{
		if($show_all=='1'){
			$h1 = '/*'; $h2 = '*/';
		}else{
			$h1 = ''; $h2 = '';
		}
		if($DistrictID==''){
			$w1 = '/*'; $w2 = '*/';
		}else{
			$w1 = ''; $w2 = '';
		}
		if($key==''){
			$w3 = '/*'; $w4 = '*/';
		}else{
			$w3 = ''; $w4 = '';
		}
		$sql = "SELECT
					kso.SupplychainID AS ID,
					kso.OrgID AS OrgID,
					vso.`Name`,
					v.Village,
					sd.SubDistrict,
					d.District,
					p.Province,
					IFNULL(kso.GPSUpdated, '-') GPSUpdated,
					IFNULL(kso.OrgLatitude, vso.Latitude) AS Latitude,
					IFNULL(kso.OrgLongitude, vso.Longitude) AS Longitude,
					
					(SELECT CONCAT(DateCreated, '|', Latitude, '|', Longitude) AS Location FROM sys_log_farmgate_location aa WHERE aa.SupplychainID=kso.SupplychainID AND DateCreated LIKE ? ORDER BY LocationID DESC LIMIT 1) AS DateLocation
					
				FROM
					ktv_supplychain_org kso
					LEFT JOIN view_supplychain_org vso ON vso.SupplychainID = kso.SupplychainID
					LEFT JOIN ktv_village v ON v.VillageID=vso.VillageID
					LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
					LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
					LEFT JOIN ktv_province p ON p.ProvinceID=d.ProvinceID
				WHERE
					vso.PartnerID = 8 
					AND kso.OrgType = 'trader'
					AND p.ProvinceID=?
					$w1 AND d.DistrictID=? $w2
					$w3 AND (vso.OrgID LIKE ? OR vso.Name LIKE ? ) $w4
				GROUP BY kso.SupplychainID
				HAVING Latitude IS NOT NULL AND Longitude IS NOT NULL $h1 AND DateLocation IS NOT NULL $h2";
		$query = $this->db->query($sql, array("%$date%", $ProvinceID, $DistrictID, "$key", "%$key%"));
		if ($query->num_rows() > 0) {
			$data = $query->result_array();
			$i = 0;
			foreach($data as $k=>$v){
				foreach($v as $k2=>$v2){
					if($k2=='DateLocation'){
						$x1 = explode('|', $v2);
						if(@$x1[0]!=''){
							$data[$k]['GPSUpdated'] = @$x1[0];
						}
						if(@$x1[1]!=''){
							$data[$k]['Latitude'] = @$x1[1];
						}
						if(@$x1[2]!=''){
							$data[$k]['Longitude'] = @$x1[2];
						}
					}
				}
			}
			//echo "<pre>".$this->db->last_query();die;
			return $data;
		}
		//echo "<pre>".$this->db->last_query();die;
		return false;
	}

	public function getSCE($ProvinceID, $DistrictID, $SupplychainID, $key = '', $date='', $show_all='')
	{
		if($show_all=='1'){
			$h1 = '/*'; $h2 = '*/';
		}else{
			$h1 = ''; $h2 = '';
		}
		if($DistrictID==''){
			$w1 = '/*'; $w2 = '*/';
		}else{
			$w1 = ''; $w2 = '';
		}
		if($key==''){
			$w3 = '/*'; $w4 = '*/';
		}else{
			$w3 = ''; $w4 = '';
		}
		$sql = "SELECT
					kso.SupplychainID AS ID,
					f.FarmerID AS OrgID,
					f.FarmerName AS `Name`,
					f.Photo,
					v.Village,
					sd.SubDistrict,
					d.District,
					p.Province,
					IFNULL(kso.GPSUpdated, '-') GPSUpdated,
					IFNULL(kso.OrgLatitude, vso.Latitude) AS Latitude,
					IFNULL(kso.OrgLongitude, vso.Longitude) AS Longitude,
					
					(SELECT CONCAT(DateCreated, '|', Latitude, '|', Longitude) AS Location FROM sys_log_farmgate_location aa WHERE aa.SupplychainID=kso.SupplychainID AND DateCreated LIKE ? ORDER BY LocationID DESC LIMIT 1) AS DateLocation
					
				FROM
					ktv_supplychain_org kso
					LEFT JOIN view_supplychain_org vso ON vso.SupplychainID = kso.SupplychainID
					LEFT JOIN ktv_village v ON v.VillageID=vso.VillageID
					LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
					LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
					LEFT JOIN ktv_province p ON p.ProvinceID=d.ProvinceID
					LEFT JOIN sce_farmer sf ON sf.SceID=kso.OrgID AND kso.OrgType='sce'
					LEFT JOIN ktv_cocoa_farmer f ON f.FarmerID=sf.FarmerID
				WHERE
					vso.PartnerID = 8 
					AND kso.OrgType = 'sce'
					AND p.ProvinceID=?
					$w1 AND d.DistrictID=? $w2
					$w3 AND (vso.OrgID LIKE ? OR vso.Name LIKE ? ) $w4
				GROUP BY kso.SupplychainID
				HAVING Latitude IS NOT NULL AND Longitude IS NOT NULL $h1 AND DateLocation IS NOT NULL $h2";
		$query = $this->db->query($sql, array("%$date%", $ProvinceID, $DistrictID, "$key", "%$key%"));
		if ($query->num_rows() > 0) {
			$data = $query->result_array();
			$i = 0;
			foreach($data as $k=>$v){
				foreach($v as $k2=>$v2){
					if($k2=='DateLocation'){
						$x1 = explode('|', $v2);
						if(@$x1[0]!=''){
							$data[$k]['GPSUpdated'] = @$x1[0];
						}
						if(@$x1[1]!=''){
							$data[$k]['Latitude'] = @$x1[1];
						}
						if(@$x1[2]!=''){
							$data[$k]['Longitude'] = @$x1[2];
						}
					}
				}
			}
			//echo "<pre>".$this->db->last_query();die;
			return $data;
		}
		//echo "<pre>".$this->db->last_query();die;
		return false;
	}

	public function getWarehouse($ProvinceID, $DistrictID, $key = '')
	{
		$where  = '';
		$params = [];
		$sql = "
SELECT
	kc.`WarehouseID` AS ID
	, WarehouseName AS Name
	, kc.Latitude
	, kc.Longitude
	, Village
	, SubDistrict
	, StaffName
FROM ktv_warehouse kc
LEFT JOIN ktv_supplychain_org so ON so.OrgID = kc.WarehouseID AND so.OrgType = 'warehouse'
LEFT JOIN ktv_supplychain_org_partner sop ON sop.SupplychainID = so.SupplychainID
JOIN ktv_village v ON kc.VillageID=v.VillageID
JOIN ktv_subdistrict sd ON v.SubDistrictID=sd.SubDistrictID
JOIN ktv_district d ON d.DistrictID = sd.DistrictID
JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
LEFT JOIN ktv_warehouse_staff kcs ON kc.WarehouseID=kcs.WarehouseID and Position='pemilik'
LEFT JOIN ktv_access_staff st ON st.DistrictID = d.DistrictID
INNER JOIN ktv_warehouse_access kwa ON kc.`WarehouseID` = kwa.`WarehouseID` AND kwa.`PartnerID` = {$_SESSION['PartnerID']}
WHERE 1 = 1
	AND kc.StatusCode != 'nullified'
	AND ABS(kc.Latitude) > 0 AND ABS(kc.Longitude) > 0
   -- where --
group by kc.WarehouseID
		";

		$where .= ' AND st.UserId = ?';
		$params[] = intval($_SESSION['userid']);

		$where .= ' AND sop.PartnerID = ?';
		$params[] = intval($_SESSION['PartnerID']);

		if (!empty($ProvinceID)) {
			$where .= " AND p.ProvinceID = ?";
			$params[] = intval($ProvinceID);
		}
		if (!empty($DistrictID)) {
			$where .= " AND d.DistrictID = ?";
			$params[] = intval($DistrictID);
		}
		if (!empty($key)) {
			$where .= " AND (WarehouseName LIKE '%{$key}%')";
		}
		$sql = str_replace("-- where --", $where, $sql);
		$query = $this->db->query($sql, $params);
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
		return false;
	}

	

	public function getTransactionDetails($get)
	{
		$date1 = $get['StartDate'].' 00:00:00';
		$date2 = $get['EndDate'].' 23:59:59';
		if(@$get['WarehouseID']!=''){
			$wh1 = ''; $wh2 ='';
		}else{
			$wh1 = '/*'; $wh2 ='*/';
		}
		if(@$get['Tier1']!=''){
			$tier1a = ''; $tier1b ='';
		}else{
			$tier1a = '/*'; $tier1b ='*/';
		}
		if(@$get['Tier2']!=''){
			$tier2a = ''; $tier2b ='';
		}else{
			$tier2a = '/*'; $tier2b ='*/';
		}

		$sql = "SELECT DISTINCT
					st.InvoiceNumber AS 'Trans Number',
					vso.`Name` AS 'Buying Unit',
					SUBSTR(st.DateTransaction, 1, 10) AS `Date`,
					st.FAQVolumeBruto AS Gross,
					st.FAQVolumeNetto AS Nett,
					IF(st.SupplyType='Batch',
						CONCAT(vso.OrgType, ' - ', vso.OrgID, ' - ', vso.`Name`),
						CONCAT(IF(f.FarmerID IS NULL, 'New Farmer', 'Farmer'), ' - ', IFNULL(f.FarmerID,IFNULL(af.DisplayID, '')), ' - ', IFNULL(f.FarmerName,IFNULL(af.Fullname, '')))
					) AS 'From',
					IFNULL(sb.SupplyDestStatus, 'Open Transaction') AS 'Status'
				FROM
					`rpt_tc_trans_detail` rpt
					LEFT JOIN ktv_supplychain_transaction st ON st.SupplyTransID=rpt.transid_1
					LEFT JOIN ktv_supplychain_batch sb ON sb.SupplyBatchID=st.SupplyBatchID
					LEFT JOIN view_supplychain_org vso ON vso.SupplychainID=st.SupplychainID
					LEFT JOIN ktv_village v ON v.VillageID=vso.VillageID
					LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
					LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
					
					LEFT JOIN ktv_cocoa_farmer f ON f.FarmerID=st.SupplyID AND st.SupplyType!='Batch'
					LEFT JOIN ktv_applicant_farmers af ON af.ApplicantID=st.SupplyID AND st.SupplyType!='Batch' AND f.FarmerID IS NULL
					
					LEFT JOIN ktv_supplychain_batch sb2 ON sb2.SupplyBatchNumber=st.SupplyID AND st.SupplyType='Batch'
					LEFT JOIN view_supplychain_org vso2 ON vso2.SupplychainID=sb2.SupplyOrgID
					LEFT JOIN ktv_village v2 ON v2.VillageID=vso2.VillageID
					LEFT JOIN ktv_subdistrict sd2 ON sd2.SubDistrictID=v2.SubDistrictID
					LEFT JOIN ktv_district d2 ON d2.DistrictID=sd2.DistrictID
				WHERE
					rpt.partnerid_1 = 8 
					AND rpt.date_1 BETWEEN ? AND ?
					$wh1 AND IF(rpt.orgtype_5='Gudang', supplyorgid_5, IF(rpt.orgtype_4='Gudang', supplyorgid_4, IF(rpt.orgtype_3='Gudang', supplyorgid_3, IF(rpt.orgtype_2='Gudang', supplyorgid_2, NULL))))=? $wh2
					$tier1a AND IF(rpt.orgtype_5='Gudang', supplyorgid_4, IF(rpt.orgtype_4='Gudang', supplyorgid_3, IF(rpt.orgtype_3='Gudang', supplyorgid_2, IF(rpt.orgtype_2='Gudang', supplyorgid_1, NULL))))=? $tier1b
					$tier2a AND supplyorgid_1=? $tier2b
					AND supplyorgid_1=?
					AND rpt.lat_1!=0
					AND rpt.long_1!=0
					AND rpt.supplyorgid_1 > 0
					AND (
						rpt.orgid_1 LIKE ? OR rpt.orgid_2 LIKE ? OR rpt.orgid_3 LIKE ? OR rpt.orgid_4 LIKE ?
						OR rpt.name_1 LIKE ? OR rpt.name_2 LIKE ? OR rpt.name_3 LIKE ? OR rpt.name_4 LIKE ?
						OR rpt.FarmerID LIKE ? OR rpt.FarmerName LIKE ?
					)";

		$query = $this->db->query($sql, array(
			@$get['StartDate'], @$get['EndDate'], @$get['WarehouseID'], @$get['Tier1'], @$get['Tier2'], @$get['ID'],

			'%'.$get['key'].'%', '%'.$get['key'].'%', '%'.$get['key'].'%', '%'.$get['key'].'%', '%'.$get['key'].'%',
			'%'.$get['key'].'%', '%'.$get['key'].'%', '%'.$get['key'].'%', '%'.$get['key'].'%', '%'.$get['key'].'%'
		));
		if ($query->num_rows() > 0) {
			$return['id'] = $get['ID'];
			$return['type'] = 'trader';
			$return['uniqueChild'] = '3';
			$return['data'] = $query->result_array();
			return $return;
		}
		return false;
	}

}