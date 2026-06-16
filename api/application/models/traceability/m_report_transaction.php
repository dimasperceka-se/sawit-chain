<?php

/**
 * Authentication Model for Mobile
 *
 * @author Yusuf
 */
class m_report_transaction extends CI_Model {
    
    function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
    }
	
	 
	public function get_data($InputForm, $start,$limit,$sortingField,$sortingDir){
        if($sortingField == "") $sortingField = 'st.SupplyTransID';
        if($sortingDir == "") $sortingDir = 'ASC'; 
		
		$Input=array(); 
		foreach ($InputForm as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Traceability_report_MainGrid-form-", '', $key);
            if($value == "") $value = null;
			$Input[$keyNew] = $value;
		}
		
		$filter='';
		if($Input['PartnerID'] !='' ){
            $filter .=' AND pp.PartnerID = '. $Input['PartnerID'] ;
        }	
		if($Input['PartnerID'] !='' ){
            $filter .=' AND vso2.SupplychainID = '. $Input['BuyingUnit'] ;
        } 
		if($Input['date_from'] !='' && $Input['date_to'] !=''){
            $filter .=' AND st.DateTransaction BETWEEN "'. $Input['date_from'] .'" AND "'. $Input['date_to'].'"'   ;
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
            $filter .=' AND m.MemberID = "'. $Input['FarmerID'].'"' ;
        }
		 
        if($InputForm['Spout'] == true){ $a ='/*'; $b="*/";}
		
            $sql="SELECT
                SQL_CALC_FOUND_ROWS
                st.SupplyTransID,
				st.SupplyBatchID,
				st.DateTransaction,
				st.TransNumber,
				m.MemberDisplayID FarmerID,
				m.MemberName FarmerName,
				v.Village,
				pp.PartnerID,
				sd.SubDistrict,
				d.District,
				st.PlantationNr, 
				st.VolumeBruto,
				st.PackageNumber,
				st.VolumeCutting,
				st.VolumeNetto,
				CONCAT(vso.ObjType,' - ', vso.`Name`) BuyingUnit,
				d2.District Distric,
				pp.PartnerName,
				IFNULL(sb.SupplyBatchStatus, 'Open') Status,
				sb.SupplyBatchNumber,
				IF(vso2.SupplychainID IS NULL, '', CONCAT(vso2.ObjType,' - ', vso2.`Name`)) Destination,
			    pl.PlotNr,
				pl.Longitude,
				pl.Latitude
			FROM
				ktv_tc_supplychain_transaction st
				LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID = st.SupplyBatchID
				LEFT JOIN ktv_members m ON m.MemberID=st.SupplyID AND st.SupplyType!='Batch'
				LEFT JOIN ktv_village v ON v.VillageID=m.VillageID
				LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
				LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
				LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID=st.SupplychainID
				LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID=sb.SupplyDestOrgID
				LEFT JOIN ktv_program_partner pp ON pp.PartnerID=vso.PartnerID
				LEFT JOIN ktv_village v2 ON v2.VillageID=vso.VillageID
				LEFT JOIN ktv_subdistrict sd2 ON sd2.SubDistrictID=v2.SubDistrictID
				LEFT JOIN ktv_district d2 ON d2.DistrictID=sd2.DistrictID 
				LEFT JOIN ktv_survey_plot pl ON pl.MemberID = m.MemberID
			WHERE
				st.SupplyType!='Batch' $filter
				GROUP BY st.SupplyTransID
				ORDER BY $sortingField $sortingDir
            $a LIMIT ?,? $b";
			
			
            $query = $this->db->query($sql,array((int) $start,(int) $limit)); 
			//echo '<pre>';
			//echo $this->db->last_query();die;
			$result['data'] = $query->result_array();
		
        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }
	 
}

?>
