<?php

/**
 * Authentication Model for Mobile
 *
 * @author Yusuf
 */
class m_supplychain_areafarmer extends CI_Model {
    
    function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
    }
	
	 
	
	public function get_data($SupplychainID, $start,$limit,$sortingField,$sortingDir){
        if($sortingField == "") $sortingField = 'xc.SupplychainID';
        if($sortingDir == "") $sortingDir = 'ASC';  
		
            $sql="
			SELECT
                SQL_CALC_FOUND_ROWS 
                  xc.SupplychainFarmerID 
                , xc.DateStart
                , xc.DateEnd
                , xc.SupplychainID
					 , a.MemberID AS MemberID
                , a.MemberDisplayID 
                , a.MemberName 
                , c.Village AS Desa
                , d.SubDistrict AS Kecamatan 
                , e.Province
                , f.District 
            FROM
            	 ktv_tc_supplychain_farmer xc
                LEFT JOIN ktv_members a ON a.MemberID = xc.FarmerID
                LEFT JOIN ktv_member_role mrole ON a.MemberID = mrole.MemberID
                LEFT JOIN ktv_program_partner_survey partsur ON a.PartnerID = partsur.PartnerID
                LEFT JOIN ktv_ref_member_role rrole ON mrole.MRoleID = rrole.MRoleID
                LEFT JOIN ktv_village c ON a.VillageID = c.VillageID
                LEFT JOIN ktv_subdistrict d ON SUBSTR(a.VillageID,1,7) = d.SubDistrictID
                LEFT JOIN ktv_province e ON SUBSTR(a.VillageID,1,2) = e.ProvinceID
                LEFT JOIN ktv_district f ON SUBSTR(a.VillageID,1,4) = f.DistrictID 
            WHERE
                a.StatusCode = 'active' and xc.SupplychainID = ?
				GROUP BY a.MemberID
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
	
	public function get_AllFarmerdata($SupplychainID,$pSearch, $start,$limit,$sortingField,$sortingDir){
        if($sortingField == "") $sortingField = 'a.MemberID';
        if($sortingDir == "") $sortingDir = 'ASC';  
		//BENTUK QUERY FILTER =============================================== (BEGIN)
        $sqlFilter = "";
		if ($pSearch['prov'] != "") {
            $sqlFilter .= " AND SUBSTR(a.VillageID,1,2) = " . $pSearch['prov'];
        }

        if ($pSearch['kab'] != "") {
            $sqlFilter .= " AND SUBSTR(a.VillageID,1,4) = " . $pSearch['kab'];
        }

        if ($pSearch['kec'] != "") {
            $sqlFilter .= " AND SUBSTR(a.VillageID,1,7) = " . $pSearch['kec'];
        }
		
		if ($pSearch['desa'] != "") {
            $sqlFilter .= " AND a.VillageID = " . $pSearch['desa'];
        }
		
		//Buka dulu data di farmer ktv_tc_supplychain_farmer biar ketahuan farmer mana yang tidak ada di tabel itu.
		$dstart = date("Y-m-d", strtotime($pSearch['DateStart']));
		$dend = date("Y-m-d", strtotime($pSearch['DateEnd']));
		$SQLfarmer = "SELECT FarmerID FROM ktv_tc_supplychain_farmer where SupplychainID =? and DateStart >= ? AND DateEnd <= ? ";
		$s = $this->db->query($SQLfarmer, array($SupplychainID, $dstart, $dend ) )->result();
		//echo $this->db->last_query();die;
	    $MemberID ='';
		if($s)
		{
			$a=''; $b='';
			foreach($s as $key)
			{
				$MemberID .= $key->FarmerID .",";
			}
		}else{
			$a='/*'; $b='*/';
		}
		
		$MemberID = rtrim($MemberID,","); 
		//print_r($MemberID);die; 
		$sql ="
		SELECT
			SQL_CALC_FOUND_ROWS 
			a.MemberID AS MemberID
			, a.MemberDisplayID 
			, a.MemberName 
			, c.Village AS Desa
			, d.SubDistrict AS Kecamatan 
			, e.Province
			, f.District 
		FROM 
			ktv_members a 
			LEFT JOIN ktv_village c ON a.VillageID = c.VillageID
			LEFT JOIN ktv_subdistrict d ON SUBSTR(a.VillageID,1,7) = d.SubDistrictID
			LEFT JOIN ktv_province e ON SUBSTR(a.VillageID,1,2) = e.ProvinceID
			LEFT JOIN ktv_district f ON SUBSTR(a.VillageID,1,4) = f.DistrictID 
		WHERE
			a.StatusCode = 'active' 
			$a and a.MemberID NOT IN ($MemberID) $b
			$sqlFilter
			GROUP BY a.MemberID
			ORDER BY $sortingField $sortingDir
			LIMIT ?,? ";
		$query = $this->db->query($sql,array( (int) $start,(int) $limit)); 
		//echo '<pre>';
		//echo $this->db->last_query();die;
		$result['data'] = $query->result_array();
		
        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }
	 
	
	public function submitchecked($data){
        $result = false;
        $insid = 0;
        $error = ''; 
		//print_r($data);die;
        try{
            $this->db->trans_begin();
            /* Transaksi */
           $content_tr = array(
                "FarmerID" => $data['FarmerID'],
                "SupplychainID" => $data['SupplychainID'],
                "DateStart" => date("Y-m-d", strtotime($data['DateStart'])),
                "DateEnd" => date("Y-m-d", strtotime($data['DateEnd'])), 
                "StatusCode" => 'active'
            );  

            $content_tr['DateCreated'] = date('Y-m-d H:i:s');
			$content_tr['DateUpdated'] = date('Y-m-d H:i:s');
			$content_tr['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1; 
			 
			$this->db->insert('ktv_tc_supplychain_farmer', $content_tr); 
			//echo '<pre>';
			//echo $this->db->last_query();
			//die;
			$insid = $this->db->insert_id();
					
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
            return array('success' => $result, 'SupplychainFarmerID' => $insid);
        }else{
            return array('success' => $result, 'message' => 'Save data failed', 'error' => $error);
        }
		
	}
	
	 public function delete($id){
        
		if((int)$id > 0){
            //delete role 1st
            $this->db->where('SupplychainFarmerID',$id);
            $this->db->delete('ktv_tc_supplychain_farmer');
			//echo $this->db->last_query();die;
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
