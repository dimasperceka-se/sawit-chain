<?php

/**
 * Authentication Model for Mobile
 *
 * @author Yusuf
 */
class m_report_transaction extends CI_Model {
    
    function __construct() {
        parent::__construct();
    }
	
	 
	public function get_data($InputForm, $start,$limit,$sortingField,$sortingDir){
		$s1 = $_SESSION['SupplychainID']=='' ? '/*' : '';
		$s2 = $_SESSION['SupplychainID']=='' ? '*/' : '';
		$sql = "SELECT
					GROUP_CONCAT(DISTINCT kso.SupplychainID) id
				FROM
					ktv_access_partner_mill p
					LEFT JOIN ktv_mill m ON m.MillID=p.apmiMillID
					LEFT JOIN ktv_tc_supplychain_org kso ON kso.ObjID=m.MillID AND kso.ObjType='mill'
				WHERE
					apmiPartnerID = ?  $s1 AND kso.SupplychainID=? $s2";
		$query = $this->db->query($sql, array($_SESSION['PartnerID'], $_SESSION['SupplychainID']));
		if($_SESSION['is_admin']!='1'){
			$id = empty($query->row()->id)? $_SESSION['SupplychainID'] : $query->row()->id;
			$where_default = "AND (IFNULL(st2.SupplychainID, IFNULL(sb2.SupplyOrgID, sb.SupplyDestOrgID)) IN ($id)
								OR IFNULL(st3.SupplychainID, sb2.SupplyDestOrgID) IN ($id) 
								)";
		}else{
			$where_default = "";
		}
        if($sortingField == "") $sortingField = 'st.SupplyTransID';
        if($sortingDir == "") $sortingDir = 'ASC'; 
		
		$Input=array(); 
		foreach ($InputForm as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Traceability_new_report_MainGrid-form-", '', $key);
            if($value == "") $value = null;
			$Input[$keyNew] = $value;
		}
		
		$filter='';
		if($Input['Mill'] !='' ){
            $filter .=' AND pp.PartnerID = '. $Input['Mill'] ;
        }	
		if($Input['BuyingUnit'] !='' ){
            $filter .=' AND (vso2.SupplychainID = '. $Input['BuyingUnit'] .' OR vso.SupplychainID = '. $Input['BuyingUnit'] . ')' ;
        } 
		if($Input['date_from'] !='' && $Input['date_to'] !=''){
            $filter .=' AND DATE_FORMAT(st.DateTransaction,"%Y-%m-%d") BETWEEN "'. $Input['date_from'] .'" AND "'. $Input['date_to'].'"'   ;
        }
		if($Input['TransNumber'] !='' ){
            $filter .=' AND st.TransNumber = "'. $Input['TransNumber'].'"' ;
        } 
		 
		if($Input['BatchNumber'] !='' ){
            $filter .=' AND sb.SupplyBatchNumber = "'. $Input['BatchNumber'].'"' ;
        }
		if($Input['ProvinceID'] !='' ){
            $filter .=' AND substr(d.DistrictID,1,2) = "'. $Input['ProvinceID'].'"' ;
        }
		if($Input['DistrictID'] !='' ){
            $filter .=' AND d.DistrictID  = "'. $Input['DistrictID'].'"' ;
        }
		if($Input['SubDistrictID'] !='' ){
            $filter .=' AND sd.SubDistrictID = "'. $Input['SubDistrictID'].'"' ;
        }
		if($Input['VillageID'] !='' ){
            $filter .=' AND v.VillageID = "'. $Input['VillageID'].'"' ;
        }
		if($Input['FarmerID'] !='' ){
            $filter .=' AND m.MemberDisplayID = "'. $Input['FarmerID'].'"' ;
		}
		if($Input['Certified'] == 'yes'){
			$filter .=' AND (m.isCertified IS NOT NULL OR m.isCertified <> 10)';
		}
		if($Input['Certified'] == 'no'){
			$filter .=' AND (m.isCertified IS NULL OR m.isCertified = 10)';
		}

        if($InputForm['Spout'] == true){ $a ='/*'; $b="*/";}
		
            $sql="SELECT
                SQL_CALC_FOUND_ROWS
                st.SupplyTransID,
				st.SupplyBatchID,
				st.DateTransaction,
				st.TransNumber,
				IFNULL(m.MemberDisplayID, vsoc.ObjID) FarmerID,
				IFNULL(m.MemberName, vsoc.Name) FarmerName,
				v.Village,
				pp.PartnerID,
				sd.SubDistrict,
				d.District,
				pro.Province,
				st.PlantationNr, 
				st.VolumeBruto,
				st.PackageNumber,
				st.VolumeCutting,
				st.VolumeNetto,
				st.NetPrice,
				st.TotalPayment,
				vso.`Name` as Agent,
				CONCAT(vso.ObjType,' - ', vso.`Name`) BuyingUnit,
				d2.District Distric,
				pp.PartnerName,
				IFNULL(sb.SupplyBatchStatus, 'Open') Status,
				sb.SupplyBatchNumber,
				IF(vso2.SupplychainID IS NULL, '', CONCAT(vso2.ObjType,' - ', vso2.`Name`)) Destination,
			    pl.PlotNr,
				pl.Longitude,
				pl.Latitude,
				pl.GardenAreaHa,
				pl.GardenAreaPolygon,
				IF(m.isCertified IS NOT NULL, IF(m.isCertified <> 10,'Yes','No'),'No') isCertified
			FROM
				ktv_tc_supplychain_transaction st
				LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID = st.SupplyBatchID
				LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=st.SupplychainID
				LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID=sb.SupplyDestOrgID
				LEFT JOIN ktv_program_partner pp ON pp.PartnerID=vso.PartnerID
				LEFT JOIN ktv_village v2 ON v2.VillageID=vso.VillageID
				LEFT JOIN ktv_subdistrict sd2 ON sd2.SubDistrictID=v2.SubDistrictID
				LEFT JOIN ktv_district d2 ON d2.DistrictID=sd2.DistrictID
				LEFT JOIN ktv_tc_supplychain_transaction st2 ON st2.SupplyID=sb.SupplyBatchID AND st2.SupplyType='Batch'
				LEFT JOIN ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID=st2.SupplyBatchID
				LEFT JOIN ktv_tc_supplychain_transaction st3 ON st3.SupplyID=sb2.SupplyBatchID AND st3.SupplyType='Batch'
				LEFT JOIN ktv_members m ON m.MemberID = IF(st3.SupplyType='Farmer', st3.SupplyID, IF(st2.SupplyType='Farmer', st2.SupplyID, IF(st.SupplyType='Farmer', st.SupplyID, NULL)))
				LEFT JOIN view_tc_supplychain_org vsoc ON vsoc.SupplychainID=IF(st3.SupplyType='Nonfarmer', st3.SupplyID, IF(st2.SupplyType='Nonfarmer', st2.SupplyID, IF(st.SupplyType='Nonfarmer', st.SupplyID, NULL)))
				LEFT JOIN ktv_village v ON v.VillageID =IFNULL(m.VillageID, vsoc.VillageID)
				LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
				LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
				LEFT JOIN ktv_province pro ON pro.ProvinceID=d.ProvinceID
				LEFT JOIN ktv_survey_plot pl ON pl.MemberID = m.MemberID
			WHERE
				st.SupplyType!='Batch' 
				AND IF(st3.SupplyTransID IS NOT NULL, st3.SupplyID, IF(st2.SupplyTransID IS NOT NULL, st2.SupplyID, st.SupplyID))!=0
				$filter
				$where_default
				GROUP BY st.SupplyTransID
				ORDER BY $sortingField $sortingDir
            $a LIMIT ?,? $b";
			
			
			$query = $this->db->query($sql,array((int) $start,(int) $limit)); 
			// echo '<pre>'.$this->db->last_query();die;
			//echo '<pre>';
			//echo $this->db->last_query();die;
			$result['data'] = $query->result_array();
		
        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
		$result['total'] = $query->row()->total;
		
        return $result;
	}
	
	public function comboMill(){
		//echo "<pre>".print_r($_SESSION, 1);die;
		$return = array('success' => false);
		$PartnerID = array_key_exists('PartnerID',$_SESSION)?$_SESSION['PartnerID']:1;
		$SupplychainID = array_key_exists('SupplychainID',$_SESSION)?$_SESSION['SupplychainID']:"";


        $this->db->select('SupplychainID,PartnerID,ObjID,Name', false);
        $this->db->from('view_tc_supplychain_org');
		$this->db->where('ObjType', 'mill');
		$this->db->order_by('Name','ASC');

		/*if($PartnerID==7 && $SupplychainID!=""){
			$this->db->where('PartnerID',$PartnerID);
		}
		else if($PartnerID==9){
			$this->db->where('PartnerID',$PartnerID);
		}*/
		if($PartnerID!=1){
			$this->db->where('PartnerID',$PartnerID);
		}
        $data = $this->db->get();

        if($data->num_rows()){
            $data = $data->result();
           return array(
			   'data' => $data, 
			   'total' => count($data)
			);
        }

		return $return;
	}

	function comboAgent($get){
        $sql="SELECT
				vso.SupplychainID id,
				vso.`Name` label,
				rel.ChildID
			FROM
				ktv_tc_supplychain_org_rel rel
				LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=rel.ChildID
			WHERE
				rel.ParentID = ? AND rel.StatusCode='active'
				AND vso.SupplychainID IS NOT NULL
			ORDER BY vso.`Name`;";
		$query = $this->db->query($sql,array($get['SupplyChainID']));
        return array('data' => $query->result_array());
        
	}
	
	 
}

?>
