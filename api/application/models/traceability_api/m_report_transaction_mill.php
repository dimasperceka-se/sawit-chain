<?php

/**
 * Authentication Model for Mobile
 *
 * @author Yusuf
 */
class m_report_transaction_mill extends CI_Model {
    
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
			$where_default = "AND (IFNULL(st2.SupplychainID, IFNULL(sb2.SupplyOrgID, sb2.SupplyDestOrgID)) IN ($id)
								OR IFNULL(st3.SupplychainID, sb2.SupplyDestOrgID) IN ($id) 
								)";
		}else{
			$where_default = "";
        }
        
        if($_SESSION['is_admin']=='1' || $_SESSION['SupplychainID']!=''){
            $where_default = "";
        }else{
            $sql = "SELECT
                        GROUP_CONCAT(DISTINCT c.SupplychainID) AS id 
                    FROM
                        ktv_mill a
                        LEFT JOIN ktv_access_partner_mill b ON a.MillID = b.apmiMillID
                        LEFT JOIN view_tc_supplychain_org c ON c.ObjType='mill' AND c.ObjID=a.MillID
                    WHERE
                        (b.apmiPartnerID = ? OR a.PartnerID=?)
                        AND a.StatusCode = 'active' 
                        AND a.NDAAgree = 1";
            $access_mill = $this->db->query($sql, array($_SESSION['PartnerID'], $_SESSION['PartnerID']))->row()->id;
            if(@$access_mill!=''){
                $where_default = "AND st.SupplychainID IN ($access_mill)";
            }else{
                $where_default = "";
            }
        }
        
        if($sortingField == "") $sortingField = 'st.DateTransaction';
        if($sortingDir == "") $sortingDir = 'DESC'; 
		
		$Input=array(); 
		foreach ($InputForm as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Traceability_new_report_MainGridTransMill-form-", '', $key);
            if($value == "") $value = null;
			$Input[$keyNew] = $value;
		}
		$date1 = date_create($Input['date_from']);
        $date2 = date_create($Input['date_to']);

        $startDate = date_format($date1, 'Y-m-d H:i:s');
        $endDate   = date_format($date2, 'Y-m-d H:i:s');
    
        $filter='';
		if($Input['Mill'] !='' ){
            // $filter .=' AND st.SupplychainID = '. $Input['Mill'] ;
        }	
		if($Input['BuyingUnit'] !='' ){
            $filter .=' AND (vso1.SupplychainID  = '. $Input['BuyingUnit'].' OR vso2.SupplychainID = '. $Input['BuyingUnit'].')';
        } 
		if($Input['date_from'] !='' && $Input['date_to'] !=''){
            $filter .=' AND st.DateTransaction BETWEEN "'. $startDate .'" AND "'. $endDate.'"'   ;
        }        
        
        if ($InputForm['Spout'] == true) {
            $sqlLimit = "";
        } else {
            $start = $start;
            $limit = $limit;
            $sqlLimit = " LIMIT {$start},{$limit}";
        }		
        
        $sql="SELECT SQL_CALC_FOUND_ROWS
            st.DateTransaction,
            st.SupplyTransID,
            st.TransNumber,
            st.VolumeBruto,
            st.VolumeNetto,
            vso1.Name AS BatchFrom,
            IFNULL(vso2.Name, IF(st.SupplyBatchSourceType='1', st.MillOther, st.DOOther)) BatchTo,
            IF(st.SupplyBatchType='Untraceable', 'Untraceable', 'Traceable') SupplyBatchType,
            IFNULL(sb.SupplyBatchStatus, IFNULL(sb.SupplyBatchStatus, 'Open')) Status,
            sb.SupplyBatchNumber,
            ktsd.DestDriver,
            ktsd.DestPO
        FROM
            ktv_tc_supplychain_transaction st
            LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID = st.SupplyBatchID  
            LEFT JOIN ktv_tc_supplychain_delivery_detail ktsdd ON ktsdd.SupplyBatchID = sb.SupplyBatchID 
            LEFT JOIN ktv_tc_supplychain_delivery ktsd ON ktsd.Deliveryid = ktsdd.Deliveryid
            LEFT JOIN view_tc_supplychain_org vso1 ON vso1.SupplychainID = ktsd.SupplychainID 
            LEFT JOIN view_tc_supplychain_org vso2 ON vso2.SupplychainID = ktsd.SupplyDestMillOrgID 
        WHERE
            ktsd.SupplyDestMillOrgID= '$id'
            AND
            st.DateTransaction IS NOT NULL
            $filter
        GROUP BY st.SupplyTransID
        ORDER BY $sortingField $sortingDir
        $sqlLimit";
        
        $query = $this->db->query($sql); 
        // echo $this->db->last_query();die;
        
        $result['data'] = $query->result_array();
			
        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;
		
        return $result;
	}
	
	public function comboMill(){
		if($_SESSION['is_admin'] == "1"){
            $sqlHakAksesPartner = "";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            //cek ktv_access_staff
            $sqlHakAksesPartner = " AND m.StatusCode = 'active' AND apm.apmiPartnerID = '$_SESSION[PartnerID]' ";
            $sqlHakAksesPartnerMill = " AND m.StatusCode = 'active' AND apm.apmiPartnerID = '$_SESSION[PartnerID]' ";
            if($_SESSION['PartnerID'] == 1){
                $sqlHakAksesPartner = "";
            }
        }elseif($_SESSION['role'] == "Mill"){
            $sqlHakAksesPartner = " AND m.StatusCode = 'active' AND vso.SupplychainID = '$_SESSION[SupplychainID]' ";
        } else {
            //cek ktv_access_staff
            $sqlHakAksesPartner = "";
        }
        // var_dump($sqlHakAksesPartner);exit;

        $where = "";
        if(isset($_GET["province"]) && $_GET["province"] != ''){
            $where .= " AND p.ProvinceID = '$_GET[province]'";
        }
        if(isset($_GET["MillGroupID"]) && $_GET["MillGroupID"] != ''){
            $where .= " AND m.MillGroupID = '$_GET[MillGroupID]'";
        }
        $sql = "SELECT
                    vso.SupplychainID,
                    m.MillName Name,
                    apm.apmiPartnerID PartnerID,
                    vso.ObjID
                FROM
                    ktv_mill m
                    LEFT JOIN ktv_village v ON v.VillageID = m.VillageID
                    LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
                    LEFT JOIN ktv_district d ON d.DistrictID = sd.DistrictID
                    LEFT JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
                    LEFT JOIN ktv_access_partner_mill apm on apm.apmiMillID = m.MillID
                    LEFT JOIN view_tc_supplychain_org vso on vso.ObjID = m.MillID
                    LEFT JOIN ktv_tc_supplychain_batch sb on ( sb.SupplyDestMillOrgID = vso.SupplychainID OR sb.SupplyDestOrgID = vso.SupplychainID) AND sb.StatusCode ='active'
                WHERE
                    1 = 1
                    $sqlHakAksesPartner
                    $where
                AND
                    vso.SupplychainID IS NOT NULL
                GROUP BY m.MillID
                ORDER BY
                    m.MillName ASC";
        $query = $this->db->query($sql, array($_SESSION["PartnerID"]));
		
        $return = array('success' => false);
        if ($query->num_rows()>0) {
            $data = $query->result();
            return array(
			   'data' => $data, 
			   'total' => count($data)
			);
        }
		return $return;
        return false;
        
        //echo "<pre>".print_r($_SESSION, 1);die;
		$PartnerID = array_key_exists('PartnerID',$_SESSION)?$_SESSION['PartnerID']:1;
		$SupplychainID = array_key_exists('SupplychainID',$_SESSION)?$_SESSION['SupplychainID']:"";


        $this->db->select('', false);
        $this->db->from('view_tc_supplychain_org');
        $this->db->where('ObjType', 'mill');
        $this->db->order_by('Name','ASC');

		/*if($PartnerID==7 && $SupplychainID!=""){
			$this->db->where('PartnerID',$PartnerID);
		}
		else if($PartnerID==9){
			$this->db->where('PartnerID',$PartnerID);
		}*/
		if($PartnerID!=1 AND $PartnerID!=7){
			$this->db->where('PartnerID',$PartnerID);
		}
        $data = $this->db->get();
        //echo $this->db->last_query();die;
        if($data->num_rows()){
        }

	}

	function comboAgent($get){
        $sql="SELECT vt.SupplychainID AS id, vt.Name AS label,orel.ChildID
        FROM view_tc_supplychain_org vt
        LEFT JOIN ktv_tc_supplychain_org_rel orel ON vt.SupplychainID=orel.ChildID
        WHERE
        orel.ParentID=?";
		
        $query = $this->db->query($sql,array($get['SupplyChainID']));
		//echo $this->db->last_query();die;
        return array('data' => $query->result_array());
        
	}
	
	 
}

?>
