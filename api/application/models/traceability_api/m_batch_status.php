<?php

/**
 * Authentication Model for Mobile
 *
 * @author Yusuf
 */
class m_batch_status extends CI_Model {
    
    function __construct() {
        parent::__construct();
    }
    
    public function get_data($recordStart = 0, $recordLimit = 12, $sortingField = '', $sortingDir = ''){
        $return = array('data' => array(), 'total' => 0);

        $this->db->select('SupplyBatchStatusID, SupplyBatchStatusName, StatusCode, DateUpdated');
        $this->db->from('ref_tc_batch_status');
        //$this->db->where('StatusCode', 'active');

        $sql_total = "SELECT count(SupplyBatchStatusID) AS total from ref_tc_batch_status";
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
            /* Transaksi */
            $content_tr = array(
                "SupplyBatchStatusID"=> $data['SupplyBatchStatusID'],
                "SupplyBatchStatusName"=> $data['SupplyBatchStatusName'],
                "StatusCode" => $data['StatusCode']
            );

            $cek = cek_data_duplicate(array('table' => 'ref_tc_batch_status', 'where' => array('SupplyBatchStatusName'=>$data['SupplyBatchStatusName'])));

            if($cek > 0 && $data['SupplyBatchStatusID']){
                /* Update data Transaction */
                $this->db->where('SupplyBatchStatusID', $data['SupplyBatchStatusID']);
                $content_tr['DateUpdated'] = date('Y-m-d H:i:s');
                $content_tr['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                $this->db->update('ref_tc_batch_status', $content_tr);
                $insid = $data['SupplyBatchStatusID'];
            }else{
                $content_tr['DateCreated'] = date('Y-m-d H:i:s');
                $content_tr['DateUpdated'] = date('Y-m-d H:i:s');
                $content_tr['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                $this->db->insert('ref_tc_batch_status', $content_tr);
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
            return array('success' => $result, 'SupplyBatchStatusID' => $insid);
        }else{
            return array('success' => $result, 'message' => 'Save data failed', 'error' => $error);
        }
    }

    public function delete($id){
        if((int)$id > 0){
            //delete role 1st
            $this->db->where('SupplyBatchStatusID',$id);
            $this->db->update('ref_tc_batch_status',array(
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
