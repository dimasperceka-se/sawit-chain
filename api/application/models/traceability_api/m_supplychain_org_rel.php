<?php

/**
 * Authentication Model for Mobile
 *
 * @author Yusuf
 */
class m_supplychain_org_rel extends CI_Model {
    
    function __construct() {
        parent::__construct();
    }
    
    public function get_data($SupplychainID, $recordStart = 0, $recordLimit = 12, $sortingField = '', $sortingDir = ''){
        $return = array('data' => array(), 'total' => 0);
        $this->db->select("a.RelID, a.ParentID, a.ChildID, a.StartDate, a.EndDate,
                            IF(DATE(NOW()) > a.EndDate, 'Expired','Active') Status,
							a.StatusCode, a.CreatedBy, a.DateCreated, 
							a.LastModifiedBy, a.DateUpdated, 
                            b.ObjType as ObjTypeParent,
                            IF(b.ObjType='Agent','sme',b.ObjType) ObjType,
                            b.Name as Parent,
                            b.PartnerID,
                            p.PartnerName as Partner", false);
        $this->db->from('ktv_tc_supplychain_org_rel a');
        $this->db->join('view_tc_supplychain_org b', 'b.SupplychainID=a.ParentID');
        $this->db->join('ktv_program_partner p', 'p.PartnerID=b.PartnerID'); 
		$this->db->where('a.StatusCode',  'active' );
        $s = $this->db->where('a.ChildID',  $SupplychainID )->get();
		// echo '<pre>';
		// echo $this->db->last_query();die;	
		
        $sql_total = "SELECT count(RelID) AS total from ktv_tc_supplychain_org_rel where ChildID = ? and StatusCode = 'Active'";
        $query_total = $this->db->query($sql_total, array($SupplychainID) );
        $total = $query_total->row_array(0);

        if($total > 0){
            $konten = $this->db->limit($recordLimit, $recordStart);
            $konten = $s->result(); 
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
            /* Transaksi */
            $content_tr = array(
                "ParentID"=> $data['ParentID'],
                "ParentID"=> $data['Parent'],
                "ChildID"=> $data['ChildID'],
				"StartDate" => $data['StartDate'],
				"EndDate" => $data['EndDate'],
                "StatusCode" => 'active'
            ); 

            if((int)($content_tr['ParentID'])){
                $this->db->where('RelID', $data['RelID']);
                $this->db->update('ktv_tc_supplychain_org_rel', $content_tr);
            }
            // var_dump($_POST);
            $insid = $data['RelID'];
            
            if($data['RelID'] != '' ){
                /* Update data Transaction */ 
                $content_tr['DateUpdated'] = date('Y-m-d H:i:s');
                $content_tr['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
				
				$cek = cek_data_duplicate(array('table' => 'ktv_tc_supplychain_org_rel', 'where' => array('ParentID'=>$data['ParentID'], 'ChildID' => $data['ChildID'])));

                if($cek == 0){ 
					$this->db->where('RelID', $data['RelID']);
					$this->db->update('ktv_tc_supplychain_org_rel', $content_tr);
                }elseif($cek != 0) {
                    $this->db->where('RelID', $data['RelID']);
                    $this->db->update('ktv_tc_supplychain_org_rel', array('ParentID' => $data['ParentID'], 'StartDate' => $data['StartDate'], 'EndDate' => $data['EndDate']));
                }else{
					$result = false;
                }
                
                $insid = $data['RelID'];
            }else{
                $content_tr['DateCreated'] = date('Y-m-d H:i:s');
                $content_tr['DateUpdated'] = date('Y-m-d H:i:s');
                $content_tr['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
				$cek = cek_data_duplicate(array('table' => 'ktv_tc_supplychain_org_rel', 'where' => array('ParentID'=>$data['Parent'], 'ChildID' => $data['ChildID'])));
                
                if($cek == 0){
					$this->db->insert('ktv_tc_supplychain_org_rel', $content_tr);
				}elseif($cek != 0){
                    $this->db->insert('ktv_tc_supplychain_org_rel', $content_tr);
                }else{
					//karena sudah ada maka Status NullFied maka jadi active
					$this->db->where(array('ParentID'=>$data['Parent'], 'ChildID' => $data['ChildID']));
					$this->db->update('ktv_tc_supplychain_org_rel', array('StatusCode' => 'active'));
				}
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
            return array('success' => $result, 'RelID' => $insid);
        }else{
            return array('success' => $result, 'message' => 'Save data failed', 'error' => $error);
        }
    }

    public function delete($id){
        if((int)$id > 0){
            //delete role 1st
            $this->db->where('RelID',$id);
            $this->db->update('ktv_tc_supplychain_org_rel',array(
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

    public function sid($ObjID, $PartnerID,$ObjType){
        $return = array('success' => false); 
        //if($ObjType=)
        //$where = 
        // $this->db->where('ObjID != ', $ObjID);
        $this->db->where('PartnerID', $PartnerID);
        $this->db->where('ObjType', $ObjType);
        $this->db->select('SupplychainID, PartnerID, ObjID, ObjType, Name', false);
        $this->db->from('view_tc_supplychain_org'); 
        $data = $this->db->get();
        
        if($data->num_rows()){
            $data = $data->result(); 
           return array('data' => $data, 'total' => count($data));
        } 
        return $return;
    }
}

?>
