<?php

/**
 * Authentication Model for Mobile
 *
 * @author Yusuf Sutana 
 */
class m_webpengiriman extends CI_Model {
    
    function __construct() {
        parent::__construct();
        $this->load->helper('common_helper');
        date_default_timezone_set('UTC');
    }
    public function get_data_pengiriman($SID){
        $page = $this->input->get('page');
        $start = $this->input->get('start');
        $limit = $this->input->get('limit');
        $PID = $this->input->get('PID');

        /* pencarian */ 
        $SupplyStatus = $this->input->get('SupplyStatus');
        $SupplyKey = $this->input->get('SupplyKey');
 
        if($SupplyStatus){
            $this->db->where('a.SupplyBatchStatus', $SupplyStatus);
        }

        if($SupplyKey){
            $this->db->like('b.`Name`', $SupplyKey);
        }

        $data = array('data' => array(), 'total' => 0);
        $this->db->select(' a.`SupplyBatchID`,
                            a.`SupplyBatchDate`,
                            a.`SupplyOrgID`,
                            a.`SupplyDestOrgID`,
                            b.`Name` as SupplyDestOrgName,
                            a.`SupplyBatchNumber`,
                            a.`SupplyBatchStatus`,
                            a.`DeliveryDate`,
                            a.`DestPO`,
                            a.`DestWeight`,
                            a.`DestNumberPackage`,
                            a.`DestDriver`,
                            a.`DestTransportID`,
                            a.`DestTransportNumber`,
							a.DestContainerNumber,
                            a.`Notes`,
                            a.`ChangeLog`,
                            a.`ChangeBy`,
                            a.`StatusCode`,
                            a.`DateCreated`,
                            a.`CreatedBy`,
                            a.`DateUpdated`,
                            a.`LastModifiedBy`');
        $this->db->from('ktv_tc_supplychain_batch a');
        $this->db->join('view_tc_supplychain_org b', 'a.SupplyDestOrgID=b.SupplychainID', 'left');
        $this->db->where('a.StatusCode', 'active');
        $this->db->where('a.SupplyOrgID', $SID);
		if($this->input->get('SBID')){
			$this->db->where('a.SupplyBatchID', $this->input->get('SBID'));
		}
        $this->db->order_by('a.SupplyBatchID', 'DESC');
        $this->db->limit($limit, $start);

		
        $Q = $this->db->get();

        if($Q->num_rows()){
            $result = $Q->result();
            $data['data'] = $result;
            $data['total'] = $Q->num_rows();
            return $data;
        }

        return $data;
    }
    public function get_data_transaction(){
        $page = $this->input->get('page');
        $start = $this->input->get('start');
        $limit = $this->input->get('limit');
        $status = $this->input->get('status');
		
		/* pencarian */ 
        $SupplyStatus = $this->input->get('SupplyStatus');
        $SupplyKey = $this->input->get('SupplyKey');
  
        if($SupplyKey){
            $this->db->like('IFNULL(e.Name,b.MemberName)', $SupplyKey);
        }
		
        $data = array('data' => array(), 'total' => 0); 
        $this->db->select('
                            a.`SupplyTransID`,
                            a.`SupplychainID`,
                            a.`SupplyBatchID`,
                            a.`TransNumber`,
                            a.`InvoiceNumber`,
                            a.`DateTransaction`,
                            a.`SupplyType`,
                            IFNULL(e.ObjID, a.`SupplyID`) SupplyID,
                            a.`PlantationNr`,
                            a.`VolumeBruto`,
                            a.`VolumeNetto`,
                            a.`VolumeCutting`,
                            a.`PackageID`,
                            a.`PackageNumber`,
                            a.`PackageWeight`,
                            a.`DetailTypeID`,
                            a.`TransStatusID`,
                            a.ContractPrice,
                            a.NetPrice,
                            a.DiscountPrice,
                            a.TotalPayment,
                            a.`Notes`,
                            a.`ChangeLog`,
                            a.`ChangeBy`,
                            a.`DateCreated`,
                            a.`CreatedBy`,
                            a.`DateUpdated`,
                            a.`LastModifiedBy`,
                            x.Status,
                            x.SupplyUserID,
                            IFNULL(e.Name,b.MemberName) as SupplierName,
                            c.PackageType
                        ', false);
        $this->db->from('ktv_tc_supplychain_transaction_pengiriman x');
        $this->db->join('ktv_tc_supplychain_transaction a', 'x.SupplyTransID=a.SupplyTransID', 'left');
        $this->db->join('ktv_members b', 'a.SupplyID=b.MemberID', 'left');
        $this->db->join('ktv_trace_package c', 'a.PackageID=c.PackageID', 'left');
		$this->db->join('ktv_tc_supplychain_batch d', "d.SupplyBatchID=a.SupplyID AND a.SupplyType='Batch'", 'left');
		$this->db->join('view_tc_supplychain_org e', 'e.SupplychainID=d.SupplyOrgID', 'left');
        $this->db->where('a.StatusCode', 'active');
        //$this->db->where('x.Status', $status);
        $this->db->order_by('a.TransNumber', 'DESC');
        $this->db->limit($limit, $start);
	 
		if($this->input->get('SBID')!=''){
			$this->db->where('x.SupplyBatchID', $this->input->get('SBID'));
		} 
        $Q = $this->db->get();
		//echo $this->db->last_query();die;
        if($Q->num_rows()){
            $result = $Q->result();
            $data['data'] = $result;
            $data['total'] = $Q->num_rows();
            return $data;
        }

        return $data;
    }
    public function get_data_transaction_window(){
        $page = $this->input->get('page');
        $start = $this->input->get('start');
        $limit = $this->input->get('limit');

        $PID = $this->input->get('PID');
        $SID = $this->input->get('SID');
        $STID = $this->input->get('STID');
        $SBID = $this->input->get('SBID');

        $data = array('data' => array(), 'total' => 0);

        $where = array('Status' => 2, 'SupplyUserID' => array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1); 
	 
        $getTemp = array();
        $x = $this->db->select('SupplyTransID')
                ->from('ktv_tc_supplychain_transaction_pengiriman')
                ->where($where)
                ->get();
	 
        if($x->num_rows()){        
            foreach($x->result_array() as $k => $v){
                $getTemp[] = $v['SupplyTransID'];
            } 
            $this->db->where_not_in('a.SupplyTransID', $getTemp);
        }
		  
        $this->db->select('
                            a.`SupplyTransID`,
                            a.`SupplychainID`,
                            a.`SupplyBatchID`,
                            a.`TransNumber`,
                            a.`InvoiceNumber`,
                            a.`DateTransaction`,
                            a.`SupplyType`,
                            IFNULL(e.ObjID, a.`SupplyID`) SupplyID,
                            a.`PlantationNr`,
                            a.`VolumeBruto`,
                            a.`VolumeNetto`,
                            a.`VolumeCutting`,
                            a.`PackageID`,
                            a.`PackageNumber`,
                            a.`PackageWeight`,
                            a.`DetailTypeID`,
                            a.`TransStatusID`,
                            a.`FarmingTypeID`,
                            a.ContractPrice,
                            a.NetPrice,
                            a.DiscountPrice,
                            a.TotalPayment,
                            a.`Notes`,
                            a.`ChangeLog`,
                            a.`ChangeBy`,
                            a.`DateCreated`,
                            a.`CreatedBy`,
                            a.`DateUpdated`,
                            a.`LastModifiedBy`,
                            IFNULL(e.Name,b.MemberName) as SupplierName,
                            c.PackageType
                        ', false);
        $this->db->from('ktv_tc_supplychain_transaction a');
        $this->db->join('ktv_members b', 'a.SupplyID=b.MemberID', 'left');
        $this->db->join('ktv_trace_package c', 'a.PackageID=c.PackageID', 'left');
		$this->db->join('ktv_tc_supplychain_batch d', "d.SupplyBatchID=a.SupplyID AND a.SupplyType='Batch'", 'left');
		$this->db->join('view_tc_supplychain_org e', 'e.SupplychainID=d.SupplyOrgID', 'left');
        $this->db->where('a.StatusCode', 'active');
        $this->db->where('a.SupplychainID', $SID);

        if(@$SBID == 0){
            $this->db->or_where('a.SupplyBatchID IS NULL');
        }
        if($STID){
            $this->db->where('SupplyTransID', $STID);
        }

        $this->db->order_by('a.TransNumber', 'DESC');
        $this->db->limit($limit, $start);

        $Q = $this->db->get();
		//echo $this->db->last_query();die;

        if($Q->num_rows()){
            $result = $Q->result();
            foreach($result as $val){
                $val = $this->check_isNull($val);
                $val->FarmingTypeName = $this->getFarming($val->FarmingTypeID);
            }
            $data['data'] = $result;
            $data['total'] = $Q->num_rows();
            return $data;
        }

        return $data;
    }
    public function delete_transaction($STID, $SBID){
        $result = false;
        $error = 'error';
		 
        try{
            $this->db->trans_begin();
 
			//echo $this->db->last_query();die;
			// Rollback SupllyBatchID ke NULL
			$this->db->where('SupplyTransID', $STID);
			$this->db->update('ktv_tc_supplychain_transaction', array('SupplyBatchID' => NULL));
			
			$this->db->where('SupplyTransID', $STID);
			$this->db->where('SupplyBatchID', $SBID);
            $this->db->where('Status', 2);
            $this->db->where('SupplyUserID', array_key_exists('userid', $_SESSION) ? $_SESSION['userid']:1);
            $this->db->delete('ktv_tc_supplychain_transaction_pengiriman');
			
            if ($this->db->trans_status() == false) {
                $this->db->trans_rollback();
                $error = $this->db->_error_messages();
            } else {
                $this->db->trans_commit();
                $result = true;
            }
        } catch (Exception $exc) {
            $this->db->trans_rollback();
            $result = false;
        }

        $this->db->trans_complete();

        if($result) {
            return array('success' => $result);
        }else{
            return array('success' => $result, 'error' => $error);
        }
    }
    private function check_isNull($v){
        foreach($v as $key => $value){
            $v->{$key} = is_null($v->{$key}) ? "" : $v->{$key};
        }
        return $v;
    }
    public function submit_transaction($param){
        $result = false;
        $error = 'error';

        try{
            $this->db->trans_begin();
            $content = array(
                'SupplyTransID' => $param['STID'],
				'SupplyBatchID' => $param['SBID'],
                'SupplyUserID' => array_key_exists('userid', $_SESSION) ? $_SESSION['userid']:1,
                'Status' => 2
            );
            $this->db->insert('ktv_tc_supplychain_transaction_pengiriman', $content);
			//echo $this->db->last_query();die;
            if($param['SBID']){
                $this->db->where('SupplyTransID', $param['STID']);
                $this->db->update('ktv_tc_supplychain_transaction', array('SupplyBatchID' => $param['SBID']));  
            }
			
            if ($this->db->trans_status() == false) {
                $this->db->trans_rollback();
                //$error = $this->db->_error_messages();
            } else {
                $this->db->trans_commit();
                $result = true;
            }
        } catch (Exception $exc) {
            $this->db->trans_rollback();
            $result = false;
        }

        $this->db->trans_complete();

        if($result) {
            return array('success' => $result);
        }else{
            return array('success' => $result, 'error' => $error);
        }
    }
    public function submit($params){
        $result = false;
        $insid = 0;
        $error = '';
        $bath_number = '';

        $data=array();
        foreach ($params as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Traceability_Transaction_FormPengiriman-Form-", '', $key);
            if($value == "") $value = null;
            $data[$keyNew] = $value;
        }

        try{
            $this->db->trans_begin(); 
            $content = array(
                "SupplyBatchDate" => date('Y-m-d H:i:s'),
                "SupplyOrgID" => $this->input->request_headers()['Sid'],
                "SupplyDestOrgID" => $data['SupplyDestOrgID'],
                "SupplyBatchStatus" => 'Open',
                "DeliveryDate" => $data['DeliveryDate'],
                "DestPO" => $data['DestPO'],
                "DestWeight" => $this->replacestr($data['DestWeight']),
                "DestNumberPackage" => $this->replacestr($data['DestNumberPackage']),
                "DestDriver" => $data['DestDriver'],
                "DestTransportID" => $data['DestTransportID'],
                "DestTransportNumber" => $data['DestTransportNumber'],
				"DestContainerNumber" => $data['DestContainerNumber'],
                "StatusCode" => 'active'
            );
			 
            if(@$this->input->request_headers()['Sbid']){  
                $content['DateUpdated'] = date('Y-m-d H:i:s');
                $content['LastModifiedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                $this->db->where('SupplyBatchID', $this->input->request_headers()['Sbid']);
                $this->db->update('ktv_tc_supplychain_batch', $content);
				//echo $this->db->last_query();die;
                $insid = $this->input->request_headers()['Sbid'];
			    $bath_number = $data['SupplyBatchNumber'];
            }else{
                $content["SupplyBatchNumber"] =generateTransTraceabilityNumber($this->input->request_headers()['Sid']);
                $content['DateCreated'] = date('Y-m-d H:i:s');
                $content['DateUpdated'] = date('Y-m-d H:i:s');
                $content['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                $this->db->insert('ktv_tc_supplychain_batch', $content);
				//echo $this->db->last_query();die;
                $insid = $this->db->insert_id();
				$bath_number = $content['SupplyBatchNumber'];
            }
			/*
			// Update status pengiriman temporary
			$getTemp = array();
			$x = $this->db->select('SupplyTransID,SupplyBatchID')
					->from('ktv_tc_supplychain_transaction')
					->where('SupplyBatchID', $insid)
					->get();

			if($x->num_rows()){        
				foreach($x->result_array() as $k => $v){
					$this->db->where('SupplyBatchID', $v['SupplyBatchID']);
					$this->db->where('SupplyTransID', $v['SupplyTransID']);
					$this->db->where('SupplyUserID', array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1);
					$this->db->update('ktv_tc_supplychain_transaction_pengiriman', array('Status' => 1));
				}
			}
			*/
            /***********************************************/
			
            if ($this->db->trans_status() == false) {
                $this->db->trans_rollback();
                $error = $this->db->_error_messages();
            } else {
                $this->db->trans_commit();
                $result = true;
            }
        } catch (Exception $exc) {
            $this->db->trans_rollback();
            $result = false;
        }

        $this->db->trans_complete();

        if($result) {
            return array('success' => $result, 'SBID' => $insid, 'SupplyBatchNumber' => $bath_number);
        }else{
            return array('success' => $result, 'message' => 'Save data failed', 'error' => $error);
        }
    }
	
	function replacestr($form)
	{
		return str_replace(',','',$form);
	}
	
    public function get_relation($SID){
        $return = array('data' => array(), 'total' => 0);

        $Q = $this->db->select('b.*')
          ->from('ktv_tc_supplychain_org_rel a ')
          ->join('view_tc_supplychain_org b', 'b.SupplychainID=a.ParentID', 'left')
          ->where('ChildID', $SID)
          ->where('a.StatusCode', 'active')
          ->get();

        if($Q->num_rows()){
            $result = $Q->result();
            foreach($result as $key => $val){
                $val = $this->check_isNull($val);
            }
            $return['data'] = $result;
            $return['total'] = $Q->num_rows();
            return $return;
        }
        return $return;
    }
    private function getFarming($id){
        $Q = $this->db->get_where('ref_tc_farming_type', array('FarmingTypeID' => $id));

        if($Q->num_rows()){
            return $Q->row()->FarmingTypeName;
        }else{
            return '';
        }
    }
    public function close_batch(){
        $result = false;
        try{
            $this->db->trans_begin();

            $this->db->where('SupplyBatchID', $this->input->post('SBID'));
            $this->db->update('ktv_tc_supplychain_batch', array('SupplyBatchStatus' => 'Closed'));
			
			/*
			// Update status pengiriman temporary
			$getTemp = array();
			$x = $this->db->select('SupplyBatchID')
					->from('ktv_tc_supplychain_transaction')
					->where('SupplyBatchID', $this->input->post('SBID'))
					->get();

			if($x->num_rows()){        
				foreach($x->result_array() as $k => $v){
					$this->db->where('SupplyBatchID', $v['SupplyBatchID']);
					$this->db->where('SupplyUserID', array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1);
					$this->db->update('ktv_tc_supplychain_transaction_pengiriman', array('Status' => 1));
				}
			}
			*/
            /***********************************************/
			
            if ($this->db->trans_status() == false) {
                $this->db->trans_rollback();
                $error = $this->db->_error_messages();
            } else {
                $this->db->trans_commit();
                $result = true;
            }
        } catch (Exception $exc) {
            $this->db->trans_rollback();
            $result = false;
        }

        $this->db->trans_complete();

        if($result) {
            return array('success' => $result);
        }else{
            return array('success' => $result);
        }
    }
    public function sent_batch(){
        $result = false;
        try{
            $this->db->trans_begin();

            $this->db->where('SupplyBatchID', $this->input->post('SBID'));
            $this->db->update('ktv_tc_supplychain_batch', array('SupplyBatchStatus' => 'Sent'));

            if ($this->db->trans_status() == false) {
                $this->db->trans_rollback();
                $error = $this->db->_error_messages();
            } else {
                $this->db->trans_commit();
                $result = true;
            }
        } catch (Exception $exc) {
            $this->db->trans_rollback();
            $result = false;
        }

        $this->db->trans_complete();

        if($result) {
            return array('success' => $result);
        }else{
            return array('success' => $result);
        }
    }
	
	function  getTransaksi($SupplyBatchID,$SID)
	{
		$SQL ="SELECT  
				a.SupplyBatchDate, 
				( SELECT  Name from view_tc_supplychain_org where  SupplychainID = ? limit 1) as PengirimName,
				( 
					
					SELECT District 
					from ktv_district A, view_tc_supplychain_org B
					WHERE SUBSTR(B.VillageID,1,4) = A.DistrictID
					AND B.SupplychainID = ?
				)
				as DistrictPengirim,				
				b.Name as SupplyDestOrgName,
				b.Address  SupplyDestOrgAddress,
				a.SupplyBatchNumber, 
				a.SupplyBatchStatus, 
				a.DeliveryDate, 
				a.DestPO, 
				a.DestWeight, 
				a.DestNumberPackage, 
				a.DestDriver as Driver,  
				a.DestTransportNumber as PlatNomor, 
				a.DestContainerNumber,
				a.Notes, 
				a.ChangeLog, 
				a.ChangeBy, 
				a.StatusCode, 
				a.DateCreated, 
				a.CreatedBy,
				 a.DateUpdated, 
				 a.LastModifiedBy
				FROM (ktv_tc_supplychain_batch a)
				LEFT JOIN view_tc_supplychain_org b ON a.SupplyDestOrgID=b.SupplychainID
				WHERE a.StatusCode =  'active'
				AND a.SupplyBatchID = ? ";
				 
		$t = $this->db->query($SQL, array($SID,$SID, $SupplyBatchID));
		//echo $this->db->last_query();die;
		return $t->row_array();
	}
}

?>
