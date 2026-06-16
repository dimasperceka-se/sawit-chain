<?php

class m_supplychain_product extends CI_Model {
    
    function __construct() {
        parent::__construct();
    }
    
    public function get_data($SupplychainID, $SupplychainProductID,  $recordStart = 0, $recordLimit = 12, $sortingField = '', $sortingDir = ''){
        if($SupplychainID != ''){

            $return = array('data' => array(), 'total' => 0);
            $this->db->select("a.SupplychainID, a.SupplychainProductID, a.ProductID, a.OilType, a.ProductPercentage, a.StartDate, a.EndDate,
                                a.StatusCode, a.CreatedBy, a.DateCreated, 
                                a.LastModifiedBy, a.DateUpdated, 
                                c.ProductName", false);
            $this->db->from('ktv_tc_supplychain_product a');
            $this->db->join('ktv_tc_supplychain_org b', 'b.SupplychainID=a.SupplychainID');
            $this->db->join('ref_tc_processing_product c', 'c.ProductID=a.ProductID');
            $this->db->where('a.StatusCode !=', 'nullified');
            $s = $this->db->where('a.SupplychainID',  $SupplychainID )->get();
            $sql_total = "SELECT count(SupplychainID) AS total from ktv_tc_supplychain_product where ProductID = ? ";
            $query_total = $this->db->query($sql_total, array($SupplychainID) );
            $total = $query_total->row_array(0);

            if($total > 0){
                $konten = $this->db->limit($recordLimit, $recordStart);
                $konten = $s->result(); 
                return array('data' => $konten, 'total' => $total['total']);
            }
        } 
        return $return;
    }
    
    public function get_mill_data($SupplychainID, $SupplychainProductID,  $recordStart = 0, $recordLimit = 12, $sortingField = '', $sortingDir = ''){
        if($SupplychainID != ''){

            $return = array('data' => array(), 'total' => 0);
            $this->db->select("a.SupplychainID, a.SupplychainProductID, a.ProductID, a.ProductPercentage, a.StartDate, a.EndDate,
                                a.StatusCode, a.CreatedBy, a.DateCreated, 
                                a.LastModifiedBy, a.DateUpdated, c.ProductID,
                                c.ProductName", false);
            $this->db->from('ktv_tc_supplychain_product a');
            $this->db->join('ktv_tc_supplychain_org b', 'b.SupplychainID=a.SupplychainID');
            $this->db->join('ref_tc_processing_product c', 'c.ProductID=a.ProductID');
            $this->db->where('a.StatusCode !=', 'nullified');
            $s = $this->db->where('a.SupplychainID',  $SupplychainID )->get();
            $sql_total = "SELECT count(SupplychainID) AS total from ktv_tc_supplychain_product where ProductID = ? ";
            $query_total = $this->db->query($sql_total, array($SupplychainID) );
            $total = $query_total->row_array(0);

            if($total > 0){
                $konten = $this->db->limit($recordLimit, $recordStart);
                $konten = $s->result(); 
                return array('data' => $konten, 'total' => $total['total']);
            }
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
                "ProductID"=> '3',
                "OilType"=> $data['OilType'],
                "ProductPercentage"=> $data['ProductPercentage'],
				"StartDate" => $data['StartDate'],
				"EndDate" => $data['EndDate'],
                "StatusCode" => $data['StatusCode']
            ); 
           
            if($data['SupplychainProductID']){
              
                /* Update data Transaction */ 
                $content_tr['DateUpdated'] = date('Y-m-d H:i:s');
                $content_tr['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
				
				$cek = cek_data_duplicate(array('table' => 'ktv_tc_supplychain_product', 'where' => array( 'ProductID'=> '3', 'SupplychainID' => $data['SupplychainID'])));

                if($cek){ 
					$this->db->where('SupplychainProductID', $data['SupplychainProductID']);
                    $this->db->update('ktv_tc_supplychain_product', $content_tr);
                    
				}else{
					$result = false;
				}
                $insid = $data['SupplychainProductID'];
            }else{
                $content_tr['DateCreated'] = date('Y-m-d H:i:s');
                $content_tr['DateUpdated'] = date('Y-m-d H:i:s');
                $content_tr['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
				$cek = cek_data_duplicate(array('table' => 'ktv_tc_supplychain_product', 'where' => array('ProductID'=>$data['ProductID'], 'SupplychainID' => $data['SupplychainID'])));
               
                if($cek == 0){
                    $this->db->insert('ktv_tc_supplychain_product', $content_tr);
				}else{
					$this->db->where(array('ProductID'=>$data['ProductID'], 'SupplychainID' => $data['SupplychainID']));
					$this->db->update('ktv_tc_supplychain_product', $content_tr);
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
            return array('success' => $result, 'SupplychainID' => $insid);
        }else{
            return array('success' => $result, 'message' => 'Save data failed', 'error' => $error);
        }
    }

    public function submit_mill($data){
        $result = false;
        $insid = 0;
        $error = '';
        $bath_number = '';

        try{
            $this->db->trans_begin();

            $checkProduct = $this->db->join('ref_tc_processing_product b', 'b.ProductID = a.ProductID')
                                     ->where('b.ProductName', $data['ProductName'])
                                     ->or_where('b.ProductID', $data['ProductName'])
                                     ->get('ktv_tc_supplychain_product a')
                                     ->row();

            $content_tr = array(
                "SupplychainID"     => $data['SupplychainID'],
                "ProductID"         => $checkProduct->ProductID,
                "ProductPercentage" => $data['ProductPercentage'],
                "StartDate"         => $data['StartDate'],
                "EndDate"           => $data['EndDate'],
                "StatusCode"        => $data['StatusCode']
            );

            if($data['SupplychainProductID']){
              
                /* Update data Transaction */ 
                $content_tr['DateUpdated'] = date('Y-m-d H:i:s');
                $content_tr['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
				
				$cek = cek_data_duplicate(array('table' => 'ktv_tc_supplychain_product', 'where' => array('SupplychainID' => $data['SupplychainID'])));

                if($cek){ 
					$this->db->where('SupplychainProductID', $data['SupplychainProductID']);
                    $this->db->update('ktv_tc_supplychain_product', $content_tr);
				}else{
					$result = false;
				}
                $insid = $data['SupplychainProductID'];
            }else{
                $content_tr['DateCreated'] = date('Y-m-d H:i:s');
                $content_tr['DateUpdated'] = date('Y-m-d H:i:s');
                $content_tr['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
				$cek = cek_data_duplicate(array('table' => 'ktv_tc_supplychain_product', 'where' => array('SupplychainID' => $data['SupplychainID'])));

                if($cek == 0){
                    $this->db->insert('ktv_tc_supplychain_product', $content_tr);
                }elseif($cek != 0){
                    $this->db->insert('ktv_tc_supplychain_product', $content_tr);
                }else{
					$this->db->where(array('ProductID'=>$data['ProductID'], 'SupplychainID' => $data['SupplychainID']));
					$this->db->update('ktv_tc_supplychain_product', $content_tr);
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
                return array('success' => $result, 'SupplychainID' => $insid);
            }else{
                return array('success' => $result, 'message' => 'Save data failed', 'error' => $error);
            }
    }

    public function delete($id){
        if((int)$id > 0){
            //delete role 1st
            $this->db->where('SupplychainProductID',$id);
            $this->db->update('ktv_tc_supplychain_product',array(
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
        
        $this->db->where('ObjID != ', $ObjID);
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

    public function product(){
        $return = array('success' => false);

        $this->db->select('*', false);
        $this->db->from('ref_tc_processing_product');
        $this->db->where('ProductID', '3');
        $this->db->where('StatusCode', 'active');
        $data = $this->db->get();

        if($data->num_rows()){
            $data = $data->result();
           return array('data' => $data, 'total' => count($data));
        }

        return $return;
    }

    public function mill_product(){
        $return = array('success' => false);

        $this->db->select('*', false);
        $this->db->from('ref_tc_processing_product');
        $this->db->where('StatusCode', 'active');
        $data = $this->db->get();

        if($data->num_rows()){
            $data = $data->result();
           return array('data' => $data, 'total' => count($data));
        }

        return $return;
    }
}

?>
