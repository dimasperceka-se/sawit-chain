<?php

/**
 * Authentication Model for Mobile
 *
 * @author Yusuf
 */
class m_supplychain_org extends CI_Model {
    
    function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
    }
	
	public function getObjTypeList(){
        //cek is admin
        $sql="SELECT
                b.RoleCode AS id,
                b.RoleName AS label
            FROM
                sys_role b
            WHERE
                b.StatusCode = 'active'
                AND b.RoleCode NOT IN ('private', 'program','service','agent')
            ORDER BY b.RoleId ASC";
         $query = $this->db->query($sql, array());
        $result['data'] = $query->result_array();
        $result['data'] = $query->result_array();
        return $result;
    }
	
	public function get_data($ObjType,$name, $start,$limit,$sortingField,$sortingDir){
        if($sortingField == "") $sortingField = 'SupplychainID';
        if($sortingDir == "") $sortingDir = 'ASC'; 
	 
        if($ObjType == ''){ $a ='/*'; $b="*/";}
		
            $sql="SELECT
                SQL_CALC_FOUND_ROWS
                kvso.SupplychainID,
				kvso.ObjID,
				kvso.ObjType,
				kvso.Name, 
				(
					select count(RelID) from ktv_tc_supplychain_org_rel 
				   where kvso.SupplychainID = ParentID  AND  StatusCode ='active'
				 ) as rel,
				 (
					select count(QualityID) from ktv_tc_supplychain_quality 
					where kvso.SupplychainID = SupplychainID  AND  StatusCode ='active'
				 ) as quality,
				 (
					select count(kp.QualityID) from 
					ktv_tc_supplychain_quality kp
					JOIN ktv_tc_supplychain_quality_value kv ON kv.QualityID = kp.QualityID
					where kp.SupplychainID = kvso.SupplychainID AND kp.StatusCode ='active'
				 ) as quality_value,
				 (
					select count(PackageID) from ktv_tc_supplychain_package 
					where kvso.SupplychainID = SupplychainID AND  StatusCode ='active'
				 ) as package
            FROM
            view_tc_supplychain_org kvso
			WHERE 1 = 1
			$a AND kvso.ObjType = ?  $b
			$a AND kvso.Name LIKE ?  $b
            ORDER BY $sortingField $sortingDir
            LIMIT ?,?";
            $query = $this->db->query($sql,array($ObjType,'%'.$name.'%',(int) $start,(int) $limit)); 
			//echo '<pre>';
			//echo $this->db->last_query();die;
			$result['data'] = $query->result_array();
		
        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }
	
	public function partner(){
        $return = array('success' => false);

        $this->db->select('*', false);
        $this->db->from('ktv_program_partner');
        $this->db->where('StatusCode', 'active');
        $data = $this->db->get();

        if($data->num_rows()){
            $data = $data->result();
           return array('data' => $data, 'total' => count($data));
        }

        return $return;
    }
	
	public function submit($params){
        $result = false;
        $insid = 0;
        $error = ''; 
		
		$data=array();
		foreach ($params as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Traceability_Reference_Supplychain_org-dataForm-", '', $key);
            if($value == "") $value = null;
			$data[$keyNew] = $value;
		}

        try{
            $this->db->trans_begin();
            //print_r($data);die; 
            $content = array(
                "ObjType" => $data['ObjType'],
                "ObjID" => $data['ObjID'],
                "PartnerID" => $data['PartnerID'],
                "IsFarmer" => $data['IsFarmer'],
                "IsBatch" => $data['IsBatch'],
                "IsSent" => $data['IsSent'],
				"AccessBy" => $data['AccessBy'],
                "StatusCode" => 'active'
            ); 
            if($data['SupplychainID'] !='' ){ 
                /* Update data Transaction */
                $this->db->where('SupplychainID', @$data['SupplychainID']);
                $content['DateUpdated'] = date('Y-m-d H:i:s');
                $content['LastModifiedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                $this->db->update('ktv_tc_supplychain_org', $content);
                $insid = $data['SupplychainID'] ;  
				//echo $this->db->last_query();die;
            }else{ 
                $content_tr['DateCreated'] = date('Y-m-d H:i:s');
                $content_tr['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                
				$cek = cek_data_duplicate(array('table' => 'ktv_tc_supplychain_org', 'where' => array('ObjID'=>$data['ObjID'], 'PartnerID' => $data['PartnerID'], 'ObjType' => $data['ObjType'])));
				//echo $this->db->last_query();die;
				if($cek == 0){
					$this->db->insert('ktv_tc_supplychain_org', $content);
					$insid = $this->db->insert_id();
				}else{
					return array('success' => false , 'message' => 'Save data failed, Data is Already');
				}
            }
 
            
            if (($this->db->trans_status() == false)) {
                $this->db->trans_rollback(); 
                $result = false;
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
            return array('success' => $result, 'SupplyTransID' => $insid);
        }else{
            return array('success' => $result, 'message' => 'Save data failed');
        }
    }
	
	public function sid($ObjID, $PartnerID){
        $return = array('success' => false); 
		$this->db->where('ObjID != ', $ObjID);
		$this->db->where('PartnerID', $PartnerID);
        $this->db->select('SupplychainID, PartnerID, ObjID, ObjType, Name', false);
        $this->db->from('view_tc_supplychain_org'); 
        $data = $this->db->get();
		//echo $this->db->last_query();die;
        if($data->num_rows()){
            $data = $data->result(); 
           return array('data' => $data, 'total' => count($data));
        } 
        return $return;
    }
	
	 
	function fetch_supplyorg($SupplyChainID)
	{
		$this->db->where('SupplychainID', $SupplyChainID);
		$s = $this->db->select('ObjType,ObjID,PartnerID,IsFarmer,IsBatch,IsSent,AccessBy')->from('ktv_tc_supplychain_org')->get();
		//echo $this->db->last_query();die;  
		return array('data' => $s->result_array());
	}
	
	
	public function getObjIdCooperative($SupplyChainID , $DistrictID){
        $sql="SELECT
					c.CoopID id,
					c.CoopName label
				FROM
					ktv_cooperatives c
					LEFT JOIN ktv_tc_supplychain_org org ON org.ObjType='cooperative' AND org.ObjID=c.CoopID
				WHERE
					c.StatusCode='active' 
					AND (org.SupplychainID IS NULL OR org.SupplychainID=?)
				GROUP BY c.CoopID";
        $query = $this->db->query($sql, array($SupplyChainID));
		//echo $this->db->last_query();die;
        $return['data'] = $query->result_array();
        return $return;
    }
	
	public function getObjIdTrader($SupplyChainID, $PartnerID, $DistrictID){
        $sql="SELECT
					m.MemberID id,
					CONCAT(m.MemberDisplayID, ' - ',m.MemberName) label
				FROM
					ktv_members m
					LEFT JOIN ktv_member_role mr ON mr.MemberID=m.MemberID
					LEFT JOIN	ktv_access_partner_member mp ON mp.apmMemberID=m.MemberID
					LEFT JOIN ktv_tc_supplychain_org org ON org.ObjType='trader' AND org.ObjID=m.MemberID
				WHERE
					mr.MRoleID= 5
					AND m.StatusCode='active' AND (org.SupplychainID IS NULL OR org.SupplychainID=?)
					AND mp.apmPartnerID=?
				GROUP BY m.MemberID";
            $query = $this->db->query($sql, array(@$SupplyChainID, $PartnerID));
		//echo '<pre>';
		//echo $this->db->last_query();die;
        $return['data'] = $query->result_array();
        return $return;
    } 
	
	public function getObjIdMill($SupplychainID, $PartnerID){
		 
		if($SupplychainID =='')
		{
			$a ='/*'; $b="*/";
		}
        $sql="SELECT
					m.MillID id,
					m.MillName label
				FROM
					ktv_mill m
					LEFT JOIN	ktv_access_partner_mill mp ON mp.apmiMillID=m.MillID
					LEFT JOIN ktv_tc_supplychain_org org ON org.ObjType='processing' AND org.ObjID=m.MillID
						
				WHERE
					m.StatusCode='active' AND (org.SupplychainID IS NULL OR org.SupplychainID=?)
					AND mp.apmiPartnerID=?
				GROUP BY m.MillID";

        $query = $this->db->query($sql, array($SupplychainID, $PartnerID));
		//echo $this->db->last_query();die;
        $return['data'] = $query->result_array();
        return $return;
    }
 
	
	public function delete($id){
        if((int)$id > 0){
            //delete role 1st
            $this->db->where('SupplychainID',$id);
            $this->db->update('ktv_tc_supplychain_org',array(
                'StatusCode'      => 'nullified',
                'DateUpdated'     => date('Y-m-d H:i:s')
            ));

            $affected = $this->db->affected_rows();
            $err      = $this->db->_error_number();

            if($affected) {
                return array('success' => true, 'message' => $affected);
            }

            if($err) {
                return array('success' => false, 'message' => $this->db->_error_messages());
            }
        }
    }
	
	/*
    public function get_data($recordStart = 0, $recordLimit = 12, $sortingField = '', $sortingDir = ''){
        $return = array('data' => array(), 'total' => 0);
        $this->db->select('a.SupplychainID, a.ObjType, a.ObjID, c.Name as Obj, a.PartnerID, b.PartnerName, a.IsFarmer, a.IsBatch, a.IsSent, a.StatusCode, a.DateUpdated, a.DateCreated, a.CreatedBy, a.LastModifiedBy', false);
        $this->db->from('ktv_tc_supplychain_org a');
        $this->db->join('ktv_program_partner b', 'a.PartnerID=b.PartnerID', 'left');
        $this->db->join('view_tc_supplychain_org c', 'a.SupplychainID=c.SupplychainID', 'left');
        //$this->db->where('a.StatusCode', 'active');
        

        $sql_total = "SELECT count(SupplychainID) AS total from ktv_tc_supplychain_org";
        $query_total = $this->db->query($sql_total);
        $total = $query_total->row_array(0);

        if($total > 0){
            $konten = $this->db->limit($recordLimit, $recordStart);
            $konten = $this->db->get()->result();
             
            return array('data' => $konten, 'total' => $total['total']);
        }

        return $return;
    }
	
	
    public function submit($data){
        $result = false;
        $insid = 0;
        $error = '';
        $bath_number = '';

        try{
            $this->db->trans_begin();
            
            $content_tr = array(
                "ObjType" => $data['ObjType'],
                "ObjID" => $data['ObjID'],
                "PartnerID" => $data['PartnerID'],
                "IsFarmer" => $data['IsFarmer'],
                "IsBatch" => $data['IsBatch'],
                "IsSent" => $data['IsSent'],
                "StatusCode" => 'active'
            );

            $cek = cek_data_duplicate(array('table' => 'ktv_tc_supplychain_org', 'where' => array('ObjID'=>$data['ObjID'], 'ObjType' => $data['ObjType'])));

            if($cek > 0 && $data['SupplychainID']){
                 
                $this->db->where('SupplychainID', $data['SupplychainID']);
                $content_tr['DateUpdated'] = date('Y-m-d H:i:s');
                $content_tr['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                $this->db->update('ktv_tc_supplychain_org', $content_tr);
                $insid = $data['SupplychainID'];
            }else{
                $content_tr['DateCreated'] = date('Y-m-d H:i:s');
                $content_tr['DateUpdated'] = date('Y-m-d H:i:s');
                $content_tr['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                $this->db->insert('ktv_tc_supplychain_org', $content_tr);
                $insid = $this->db->insert_id();
            }

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
            return array('success' => $result, 'SupplychainID' => $insid);
        }else{
            return array('success' => $result, 'message' => 'Save data failed', 'error' => $error);
        }
    }

    public function delete($id){
        if((int)$id > 0){
            //delete role 1st
            $this->db->where('SupplychainID',$id);
            $this->db->update('ktv_tc_supplychain_org',array(
                'StatusCode'      => 'nullified',
                'DateUpdated'     => date('Y-m-d H:i:s')
            ));

            $affected = $this->db->affected_rows();
            $err      = $this->db->_error_number();

            if($affected) {
                return array('success' => true, 'message' => $affected);
            }

            if($err) {
                return array('success' => false, 'message' => $this->db->_error_messages());
            }
        }
    }

    public function obj($id){
        if($id == 1){
            $this->db->select('a.MemberID as id, a.MemberName as label', false);
            $this->db->from('ktv_members a');
            $this->db->join('ktv_member_role b', 'a.MemberID=b.MemberID');
            $this->db->where('b.MRoleID', 8); // Hardcode Trader
            $this->db->where('a.StatusCode', 'active');
        }else if($id == 2){
            $this->db->select('a.MillID as id, a.MillName as label', false);
            $this->db->from('ktv_mill a');
            $this->db->where('a.StatusCode', 'active'); 
        }else if($id == 3){
            $this->db->select('a.CoopID as id, a.CoopName as label', false);
            $this->db->from('ktv_cooperatives a');
            $this->db->where('a.StatusCode', 'active'); 
        }
        $data = $this->db->get();

        if($data->num_rows()){
            $return = array('data' => $data->result(), 'total' => $data->num_rows());
        }

        return $return;
    }
    public function sid(){
        $return = array('success' => false);

        $this->db->select('*', false);
        $this->db->from('view_tc_supplychain_org');
        //$this->db->where('StatusCode', 'active');
        $this->db->where('ObjID !=',0);
        $data = $this->db->get();
        if($data->num_rows()){
            $data = $data->result();
            foreach($data as $dt){
                $dt->Obj = $this->getSupplychain($dt->ObjType, $dt->ObjID);
            }
           return array('data' => $data, 'total' => count($data));
        }

        return $return;
    }
    public function sid_child($id){
        $return = array('success' => false);
        $parent = $this->db->get_where('ktv_tc_supplychain_org', ['SupplychainID' => $id])->row();
        
        $this->db->select('*', false);
        $this->db->from('view_tc_supplychain_org');
        //$this->db->where('StatusCode', 'active');
        $this->db->where('PartnerID', @$parent->PartnerID);
        $this->db->where('ObjID !=',0);

        $data = $this->db->get();
        if($data->num_rows()){
            $data = $data->result();
            foreach($data as $dt){
                $dt->Obj = $this->getSupplychain($dt->ObjType, $dt->ObjID);
            }
           return array('data' => $data, 'total' => count($data));
        }

        return $return;
    }
    
    private function getSupplychain($type, $objid){
        if($type == 'trader'){
            $this->db->select('a.MemberID as id, a.MemberName as label', false);
            $this->db->from('ktv_members a');
            $this->db->join('ktv_member_role b', 'a.MemberID=b.MemberID');
            $this->db->where('b.MRoleID', 8); // Hardcode Trader
            $this->db->where('a.StatusCode', 'active');
            $this->db->where('a.MemberID', $objid);
        }else if($type == 'processing'){
            $this->db->select('a.MillID as id, a.MillName as label', false);
            $this->db->from('ktv_mill a');
            $this->db->where('a.StatusCode', 'active');
            $this->db->where('a.MillID', $objid); 
        }else if($type == 'cooperative'){
            $this->db->select('a.CoopID as id, a.CoopName as label', false);
            $this->db->from('ktv_cooperatives a');
            $this->db->where('a.StatusCode', 'active'); 
            $this->db->where('a.CoopID', $objid); 
        }else{
            return '';
        }
        $obj = $this->db->get()->row();
        $Object = $obj->label;
        return $Object;
    }
	
	*/
}

?>
