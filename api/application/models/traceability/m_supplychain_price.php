<?php

/**
 * Authentication Model for Mobile
 *
 * @author Yusuf
 */
class m_supplychain_price extends CI_Model {
    
    function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
    }
    
    public function get_data($recordStart = 0, $recordLimit = 12, $sortingField = '', $sortingDir = ''){
        $return = array('data' => array(), 'total' => 0);
        $this->db->select(' a.*,
                                    b.ObjType,
                                    b.ObjID', false);
        $this->db->from('ktv_tc_supplychain_price a');
        $this->db->join('ktv_tc_supplychain_org b', 'a.SupplychainID=b.SupplychainID', 'left');
        //$this->db ->where('a.StatusCode', 'active');

        $sql_total = "SELECT count(*) AS total from ktv_tc_supplychain_price";
        $query_total = $this->db->query($sql_total);
        $total = $query_total->row_array(0);

        if($total > 0){
            $konten = $this->db->limit($recordLimit, $recordStart);
            $konten = $this->db->get()->result();

            foreach($konten as $dt){
                $dt->Obj = $this->getSupplychain($dt->ObjType, $dt->ObjID);
            }
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
                "DateStart"=> $data['DateStart'],
                "DateEnd"=> $data['DateEnd'],
                "Price"=> $data['Price'],
                "StatusCode" => $data['StatusCode']
            );

            $cek = cek_data_duplicate(array('table' => 'ktv_tc_supplychain_price', 'where' => array('SupplychainID'=>$data['SupplychainID'], 'Price' => $data['Price'])));

            if($cek > 0 && $data['PriceID']){
                /* Update data Transaction */
                $this->db->where('PriceID', $data['PriceID']);
                $content_tr['DateUpdated'] = date('Y-m-d H:i:s');
                $content_tr['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                $this->db->update('ktv_tc_supplychain_price', $content_tr);
                $insid = $data['PriceID'];
            }else{
                $content_tr['DateCreated'] = date('Y-m-d H:i:s');
                $content_tr['DateUpdated'] = date('Y-m-d H:i:s');
                $content_tr['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                $this->db->insert('ktv_tc_supplychain_price', $content_tr);
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
            return array('success' => $result, 'PriceID' => $insid);
        }else{
            return array('success' => $result, 'message' => 'Save data failed', 'error' => $error);
        }
    }

    public function delete($id){
        if((int)$id > 0){
            //delete role 1st
            $this->db->where('PriceID',$id);
            $this->db->update('ktv_tc_supplychain_price',array(
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
}

?>
