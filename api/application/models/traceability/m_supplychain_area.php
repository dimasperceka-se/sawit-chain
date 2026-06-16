<?php

/**
 * Authentication Model for Mobile
 *
 * @author Yusuf
 */
class m_supplychain_area extends CI_Model {
    
    function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
    }
	
	 
	
	public function get_data($SupplychainID, $start,$limit,$sortingField,$sortingDir){
        if($sortingField == "") $sortingField = 'ksa.SupplychainID';
        if($sortingDir == "") $sortingDir = 'ASC';  
		
            $sql="SELECT
                SQL_CALC_FOUND_ROWS
                ksa.*, 
				ds.DistrictID,
				ps.ProvinceID,
				ds.District,
				ps.Province
				from 
				ktv_tc_supplychain_area ksa
				LEFT JOIN ktv_district ds ON  ds.DistrictID = ksa.DistrictID
				LEFT JOIN ktv_province ps ON ps.ProvinceID = SUBSTR(ds.DistrictID,1,2)
				WHERE 1 = 1 and ksa.SupplychainID = ?
				ORDER BY $sortingField $sortingDir
				LIMIT ?,?";
				$query = $this->db->query($sql,array($SupplychainID, (int) $start,(int) $limit)); 
				//echo '<pre>';
				//echo $this->db->last_query();die;
				$result['data'] = $query->result_array();
		
        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }
	 
	
	public function submit($data){
        $result = false;
        $insid = 0;
        $error = ''; 
		//print_r($data);die;
        try{
            $this->db->trans_begin();
            /* Transaksi */
           $content_tr = array(
                "DistrictID" => $data['DistrictID'],
                "SupplychainID" => $data['SupplychainID'],
                "DateStart" => date("Y-m-d", strtotime($data['StartDate'])),
                "DateEnd" => date("Y-m-d", strtotime($data['EndDate'])), 
                "StatusCode" => 'active'
            );  

            if($data['SupplychainAreaID'] !=''){
                /* Update data Transaction */
                $this->db->where('SupplychainAreaID', $data['SupplychainAreaID']);
                $content_tr['DateUpdated'] = date('Y-m-d H:i:s');
                $content_tr['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                $this->db->update('ktv_tc_supplychain_area', $content_tr);
                $insid = $data['SupplychainAreaID'];
            }else{
                $content_tr['DateCreated'] = date('Y-m-d H:i:s');
                $content_tr['DateUpdated'] = date('Y-m-d H:i:s');
                $content_tr['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
				
				$cek = cek_data_duplicate(array('table' => 'ktv_tc_supplychain_area', 'where' => array('SupplychainID'=>$data['SupplychainID'], 'DistrictID' => $data['DistrictID'])));
			 
				if($cek== 0 ){
					$this->db->insert('ktv_tc_supplychain_area', $content_tr); 
					//echo '<pre>';
					//echo $this->db->last_query();
					//die;
					$insid = $this->db->insert_id();
				} 
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
            return array('success' => $result, 'SupplychainAreaID' => $insid);
        }else{
            return array('success' => $result, 'message' => 'Save data failed', 'error' => $error);
        }
		
	}
	
	 public function delete($id){
        //echo $id;die;
		if((int)$id > 0){
            //delete role 1st
            $this->db->where('SupplychainAreaID',$id);
            $this->db->delete('ktv_tc_supplychain_area');

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
