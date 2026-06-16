<?php

/**
 * Authentication Model for Mobile
 *
 * @author Yusuf
 */
class m_supplychain_package extends CI_Model {
    
    function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
    }
    
    public function get_data($SupplychainID, $recordStart = 0, $recordLimit = 12, $sortingField = '', $sortingDir = ''){
        $return = array('data' => array(), 'total' => 0);
        
		$this->db->where('a.SupplychainID', $SupplychainID);
		$this->db->where('a.StatusCode', 'active');
		$this->db->select(' a.PackageID,
                                    a.SupplychainID,
                                    a.PackageType,
                                    a.PackageWeight,
                                    a.PackageCapacity,
                                    a.DefaultPackage,
                                    a.StatusCode,
                                    a.DateCreated,
                                    a.CreatedBy,
                                    a.DateUpdated,
                                    a.LastModifiedBy,
                                    b.ObjType,
                                    b.ObjID,
                                    c.Name as Obj', false);
        $this->db->from('ktv_tc_supplychain_package a');
        $this->db->join('ktv_tc_supplychain_org b', 'a.SupplychainID=b.SupplychainID', 'left');
        $this->db->join('view_tc_supplychain_org c', 'a.SupplychainID=c.SupplychainID', 'left');
         

        $sql_total = "SELECT count(PackageID) AS total from ktv_tc_supplychain_package where SupplychainID = ? ";
        $query_total = $this->db->query($sql_total, array($SupplychainID));
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
            /* Transaksi */
            $content_tr = array(
                "SupplychainID"=> $data['SupplychainID'],
                "PackageType"=> $data['PackageType'],
                "PackageWeight"=> $data['PackageWeight'],
                "PackageCapacity"=> $data['PackageCapacity'],
                "DefaultPackage"=> $data['DefaultPackage'],
                "StatusCode" => $data['StatusCode']
            );

            $cek = cek_data_duplicate(array('table' => 'ktv_tc_supplychain_package', 'where' => array('PackageID'=> $data['PackageID'], 'SupplychainID'=>$data['SupplychainID'])));
			//echo $this->db->last_query();die;
            if($cek > 0 && $data['PackageID']){
                /* Update data Transaction */
                $this->db->where('PackageID', $data['PackageID']);
                $content_tr['DateUpdated'] = date('Y-m-d H:i:s');
                $content_tr['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                $this->db->update('ktv_tc_supplychain_package', $content_tr);
                $insid = $data['PackageID'];
            }else{
                $content_tr['DateCreated'] = date('Y-m-d H:i:s');
                $content_tr['DateUpdated'] = date('Y-m-d H:i:s');
                $content_tr['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                $this->db->insert('ktv_tc_supplychain_package', $content_tr);
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
            $this->db->where('PackageID',$id);
            $this->db->update('ktv_tc_supplychain_package',array(
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
            $this->db->select('a.GapoktanID as id, a.GapoktanName as label', false);
            $this->db->from('ktv_gapoktan a');
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
