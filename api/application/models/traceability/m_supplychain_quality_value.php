<?php

/**
 * Authentication Model for Mobile
 *
 * @author Yusuf
 */
class m_supplychain_quality_value extends CI_Model {
    
    function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
    }
    
    public function get_data($QualityID=0, $recordStart = 0, $recordLimit = 12, $sortingField = '', $sortingDir = ''){
        $return = array('data' => array(), 'total' => 0);
        
		$this->db->where('a.QualityID', $QualityID);
		$this->db->select(' a.ValueQualityID, 
                                    a.QualityID, 
                                    a.Value, 
                                    a.is_default,
                                    a.StatusCode, 
                                    a.DateCreated, 
                                    a.CreatedBy, 
                                    a.DateUpdated, 
                                    a.LastModifiedBy,
                                    b.Name,
                                    c.ObjType, 
                                    c.ObjID', false);
        $this->db->from('ktv_tc_supplychain_quality_value a');
        $this->db->join('ktv_tc_supplychain_quality b', 'a.QualityID=b.QualityID', 'left');
        $this->db->join('ktv_tc_supplychain_org c', 'b.SupplychainID=c.SupplychainID', 'left');
        //$this->db->where('a.StatusCode', 'active');

        $sql_total = "SELECT count(*) AS total from ktv_tc_supplychain_quality_value where QualityID = ? ";
        $query_total = $this->db->query($sql_total, array($QualityID));
        $total = $query_total->row_array(0);

        if($total > 0){
            $konten = $this->db->limit($recordLimit, $recordStart);
            $konten = $this->db->get()->result();
			//echo '<pre>';
			//echo $this->db->last_query();die;
            foreach($konten as $dt){
                $dt->Name = $dt->Name;
            }
            return array('data' => $konten, 'total' => $total['total']);
        }
        return $return;
    }
    public function quality(){
        $return = array('data' => array(), 'total' => 0);
        $data = $this->db->select('a.QualityID as id, a.Name as label, b.ObjType, b.ObjID', false)
                         ->from('ktv_tc_supplychain_quality a')
                         ->join('ktv_tc_supplychain_org b', 'a.SupplychainID=b.SupplychainID', 'left')
                         ->where('a.StatusCode', 'active')
                         ->get();

        if($data->num_rows()){
            $data = $data->result(); 
           return array('data' => $data, 'total' => count($data));
        }

        return $return;
    } 
	
    public function submit($data){
        $result = false;
        $insid = 0;
        $error = '';
        $bath_number = '';
		//print_r($data);die;
        try{
            $this->db->trans_begin();
            /* Transaksi */
            $content_tr = array(
                "QualityID"=> $data['QualityID'],
                "Value"=> $data['Value'],
                "is_default" => $data['is_default'],
                "StatusCode" => $data['StatusCode']
            );


            if($data['ValueQualityID']){
                /* Update data Transaction */
                $cek_quality = $this->db->get_where('ktv_tc_supplychain_quality_value', array('QualityID'=>$data['QualityID'], 'is_default' => 1))->num_rows(); 

                $this->db->where('ValueQualityID', $data['ValueQualityID']);
                $content_tr['DateUpdated'] = date('Y-m-d H:i:s');
                $content_tr['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                $this->db->update('ktv_tc_supplychain_quality_value', $content_tr);
                $insid = $data['ValueQualityID'];
            }else{
                $content_tr['DateCreated'] = date('Y-m-d H:i:s');
                $content_tr['DateUpdated'] = date('Y-m-d H:i:s');
                $content_tr['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;

                $cek_quality = $this->db->get_where('ktv_tc_supplychain_quality_value', array('QualityID'=>$data['QualityID'], 'is_default' => 1))->num_rows();

                $cek = cek_data_duplicate(array('table' => 'ktv_tc_supplychain_quality_value', 'where' => array('QualityID'=>$data['QualityID'], 'Value' => $data['Value'])));

                if($cek > 0){
                    return array('success' => false, 'message' => 'Data Duplicate', 'error' => 'Data Duplicate');
                } 

                $this->db->insert('ktv_tc_supplychain_quality_value', $content_tr);
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
            return array('success' => $result, 'ValueQualityID' => $insid);
        }else{
            return array('success' => $result, 'message' => 'Save data failed', 'error' => $error);
        }
    }

    public function delete($id){
        if((int)$id > 0){
            //delete role 1st
            $this->db->where('ValueQualityID',$id);
            $this->db->update('ktv_tc_supplychain_quality_value',array(
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
