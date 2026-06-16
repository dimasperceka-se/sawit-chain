<?php

/**
 * Authentication Model for Mobile
 *
 * @author Yusuf
 */
class m_supplychain_quality extends CI_Model {
    
    function __construct() {
        parent::__construct();
    }


    public function get_data($SupplychainID, $recordStart = 0, $recordLimit = 12, $sortingField = '', $sortingDir = ''){
        $return = array('data' => array(), 'total' => 0);
        $this->db->where('a.SupplychainID', $SupplychainID);
		$this->db->select(' a.QualityID, 
                            a.SupplychainID, 
                            a.Name, 
                            a.Formula, 
                            a.Order, 
							a.StartDate,
							a.EndDate,
                            a.Type, 
                            a.MinValue, 
                            a.MaxValue, 
                            a.StandardValue, 
                            a.IsPrintVisible,
                            a.StatusCode,
                            a.DateCreated,
                            a.CreatedBy,
                            a.DateUpdated,
                            a.LastModifiedBy,
                            b.ObjType,
                            b.ObjID,
                            b.Name as Obj', false);
        $this->db->from('ktv_tc_supplychain_quality a');
        $this->db->join('view_tc_supplychain_org b', 'a.SupplychainID=b.SupplychainID', 'left'); 
        $this->db->where('a.StatusCode', 'active');

        $sql_total = "SELECT count(QualityID) AS total from ktv_tc_supplychain_quality where  SupplychainID = ? ";
        $query_total = $this->db->query($sql_total, array( $SupplychainID) );
        $total = $query_total->row_array(0);
			
        if($total > 0){
            $konten = $this->db->limit($recordLimit, $recordStart);
            $konten = $this->db->get()->result(); 
			//echo '<pre>';
			//echo $this->db->last_query();die;
            return array('data' => $konten, 'total' => $total['total']);
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
    public function submit($data){
        $result = false;
        $insid = 0;
        $error = '';
        $bath_number = '';

        try{
            $this->db->trans_begin();
            /* Transaksi */
            $content_tr = array(
                "SupplychainID"=> $data['SupplychainID'],
                "Name"=> $data['Name'],
                "Formula"=> $data['Formula'],
                "Order"=> $data['Order'],
				"StartDate" => $data['StartDate'],
				"EndDate" => $data['EndDate'],
                "Type" => $data['Type'],
                "MinValue"=> $data['MinValue'],
                "MaxValue"=> $data['MaxValue'],
                "StandardValue"=> $data['StandardValue'],
                "IsPrintVisible"=> $data['IsPrintVisible'],
                "StatusCode" => 'active'
            );

            $cek = cek_data_duplicate(array('table' => 'ktv_tc_supplychain_quality', 'where' => array('SupplychainID'=>$data['SupplychainID'], 'Name' => $data['Name'])));

            if($data['QualityID'] !=''){
                /* Update data Transaction */
                $this->db->where('QualityID', $data['QualityID']);
                $content_tr['DateUpdated'] = date('Y-m-d H:i:s');
                $content_tr['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                $this->db->update('ktv_tc_supplychain_quality', $content_tr);
                $insid = $data['QualityID'];
            }else{
                $content_tr['DateCreated'] = date('Y-m-d H:i:s');
                $content_tr['DateUpdated'] = date('Y-m-d H:i:s');
                $content_tr['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                $this->db->insert('ktv_tc_supplychain_quality', $content_tr);
				//echo '<pre>';
				//echo $this->db->last_query();die;
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
            return array('success' => $result, 'PackageID' => $insid);
        }else{
            return array('success' => $result, 'message' => 'Save data failed', 'error' => $error);
        }
    }

    public function delete($id){
        if((int)$id > 0){
            //delete role 1st
            $this->db->where('QualityID',$id);
            $this->db->update('ktv_tc_supplychain_quality',array(
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
}

?>
