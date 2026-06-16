<?php

/**
 * Authentication Model for Mobile
 *
 * @author Yusuf
 */
class m_supplychain_areafarmer extends CI_Model {
    
    function __construct() {
        parent::__construct();
    }

    public function postFormAccessFarmer($post){
        $DateStart  = $post["DateStart"];
        $DateEnd    = $post["DateEnd"];
        $SupplychainFarmerID = $post['SupplychainFarmerID'];

        $data = array(
            "DateStart" => date("Y-m-d", strtotime($DateStart)),
            "DateEnd" => date("Y-m-d", strtotime($DateEnd))
        );

        $this->db->where("SupplychainFarmerID",$SupplychainFarmerID);
        $query = $this->db->update("ktv_tc_supplychain_farmer",$data);

        return $query;
    }

    public function getFormAccessFarmer($SupplychainFarmerID){
        $sql = "SELECT
                a.DateStart
                , a.DateEnd
            FROM
                ktv_tc_supplychain_farmer a
            WHERE
                a.SupplychainFarmerID = ?
        ";

        $query = $this->db->query($sql,array($SupplychainFarmerID));
        $data = array();
        if($query->num_rows()>0){
            $data = array(
                "DateStart" => $query->row()->DateStart,
                "DateEnd" => $query->row()->DateEnd
            );
        }

        return array("success"=>true,"data"=>$data);
    }
	
	public function generate_farmer_access_all(){
        $sqlSupplychainID = "
            SELECT
                a.MemberID
                , tcorg.SupplychainID
            FROM
                `ktv_member_work_area` a                
                LEFT JOIN view_tc_supplychain_org tcorg on tcorg.ObjID = a.MemberID
            WHERE
                a.VillageID IS NOT NULL
                AND a.VillageID != 0
                AND tcorg.SupplychainID IS NOT NULL
            GROUP BY a.MemberID
        ";

        $querryvil = $this->db->query($sqlSupplychainID);
        
        if($querryvil->num_rows()>0){
            try{
                $this->db->trans_begin();
                /* Transaksi */         
                foreach ($querryvil->result() as $key) {
                    $this->generate_farmer_access($key->SupplychainID);
                }
                if ($this->db->trans_status() == false) {
                    $this->db->trans_rollback();
                    $error = $this->db->_error_messages();
                    return array("success"=>false,"message"=>lang("Failed to Generate"),"code"=>$error);
                } else {
                    $this->db->trans_commit();
                    return array("success"=>true,"message"=>lang("Berhasil Generate Data"),"code"=>"200");
                }
            } catch (Exception $exc) {
                $this->db->trans_rollback();
                return array("success"=>false,"message"=>lang("Failed to Generate"),"code"=>"400");
            }
        }else{
            return array("success"=>true,"message"=>lang("Belum ada Setting Work Area"),"code"=>"400");
        }
    }

    public function generate_farmer_access($SupplychainID){
        $sqlvillage = "
            Select a.VillageID, CONCAT(v.Village, ' > ', sd.`SubDistrict`, ' > ', d.District) label 
                from ktv_member_work_area as a
            LEFT JOIN ktv_village v on v.VillageID = a.VillageID
            LEFT JOIN ktv_subdistrict sd on sd.SubDistrictID = v.SubDistrictID
            LEFT JOIN ktv_district d on sd.DistrictID = d.DistrictID
            LEFT JOIN view_tc_supplychain_org tcorg on tcorg.ObjID = a.MemberID
            where tcorg.SupplychainID = ?";
        $querryvil = $this->db->query($sqlvillage, array((int) $SupplychainID));
        $datavil = $querryvil->result();
        $VillageID = "";
        if(!empty($datavil)){           
            foreach ($datavil as $key) {
                $VillageID .= $key->VillageID .",";
            }
            $VillageID = rtrim($VillageID,","); 
        }else{
            return array("success"=>true,"message"=>lang("Belum ada Work Area"),"code"=>"400");
        }
		
		//Buka dulu data di farmer ktv_tc_supplychain_farmer biar ketahuan farmer mana yang tidak ada di tabel itu.
		$dstart = date("Y-m-d");
		$dend = date("Y-m-d",strtotime(date("Y-m-d", mktime()) . " + 365 day"));
		$SQLfarmer = "SELECT FarmerID FROM ktv_tc_supplychain_farmer where SupplychainID =? ";
		$s = $this->db->query($SQLfarmer, array($SupplychainID) )->result();
		// echo $this->db->last_query();die;
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
            INNER JOIN ktv_member_role mr on mr.MemberID = a.MemberID
            INNER JOIN ktv_ref_member_role rmr on mr.MRoleID = rmr.MroleID AND rmr.MRoleType = 'Farmer'
			LEFT JOIN ktv_village c ON a.VillageID = c.VillageID
			LEFT JOIN ktv_subdistrict d ON SUBSTR(a.VillageID,1,7) = d.SubDistrictID
			LEFT JOIN ktv_province e ON SUBSTR(a.VillageID,1,2) = e.ProvinceID
			LEFT JOIN ktv_district f ON SUBSTR(a.VillageID,1,4) = f.DistrictID 
		WHERE
            a.StatusCode = 'active'
            AND a.SupplybaseType = 'farmer'
			$a and a.MemberID NOT IN ($MemberID) $b
			AND c.VillageID IN ($VillageID)
			GROUP BY a.MemberID";
        $query = $this->db->query($sql);
        
        if($query->num_rows()>0){
            $content_tr = array();
            foreach($query->result() as $row){
                $content_tr[] = array(
                    "FarmerID" => $row->MemberID,
                    "SupplychainID" => $SupplychainID,
                    "DateStart" => date("Y-m-d", strtotime($dstart)),
                    "DateEnd" => date("Y-m-d", strtotime($dend)), 
                    "StatusCode" => 'active',
                    "DateCreated"   =>date('Y-m-d H:i:s'),
                    "DateUpdated"   =>date('Y-m-d H:i:s'),
                    "CreatedBy"   => array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1,
                );
            }

            try{
                $this->db->trans_begin();
                /* Transaksi */
                 
                $this->db->insert_batch('ktv_tc_supplychain_farmer', $content_tr); 
                // echo '<pre>';
                // echo $this->db->last_query();
                // die;
                $insid = $this->db->insert_id();
                        
                if ($this->db->trans_status() == false) {
                    $this->db->trans_rollback();
                    $error = $this->db->_error_messages();
                    return array("success"=>false,"message"=>lang("Failed to Generate"),"code"=>$error);
                } else {
                    $this->db->trans_commit();
                    return array("success"=>true,"message"=>lang("Berhasil Generate Data"),"code"=>"200");
                }
            } catch (Exception $exc) {
                $this->db->trans_rollback();
                return array("success"=>false,"message"=>lang("Failed to Generate"),"code"=>"400");
            }
        }else{
            return array("success"=>true,"message"=>lang("No Farmer To Generate"),"code"=>"400");
        }
    } 
	
	public function get_data($SupplychainID, $textSearch, $start,$limit,$sortingField,$sortingDir){
            if($sortingField == "") $sortingField = 'xc.SupplychainID';
            if($sortingDir == "") $sortingDir = 'ASC';  

            if($textSearch != ""){
                $sqlFilter = "";
                $sqlFilter = $this->sqlFilter($textSearch);
            }
            
            $sql="SELECT
                SQL_CALC_FOUND_ROWS 
                xc.SupplychainFarmerID 
                , xc.DateStart
                , xc.DateEnd
                , IF(DATE(NOW())>xc.DateEnd, 'Expired','Active') Status
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
                LEFT JOIN ktv_ref_certification_program krcp ON krcp.CertProgID = a.isCertified
                LEFT JOIN ktv_member_role mrole ON a.MemberID = mrole.MemberID
                LEFT JOIN ktv_program_partner_survey partsur ON a.PartnerID = partsur.PartnerID
                LEFT JOIN ktv_access_partner_member AS ab ON  a.MemberID = ab.apmMemberID
                LEFT JOIN ktv_ref_member_role rrole ON mrole.MRoleID = rrole.MRoleID
                LEFT JOIN ktv_village c ON a.VillageID = c.VillageID
                LEFT JOIN ktv_subdistrict d ON SUBSTR(a.VillageID,1,7) = d.SubDistrictID
                LEFT JOIN ktv_province e ON SUBSTR(a.VillageID,1,2) = e.ProvinceID
                LEFT JOIN ktv_district f ON SUBSTR(a.VillageID,1,4) = f.DistrictID 
                LEFT JOIN ktv_gapoktan kg ON kg.GapoktanID = a.GapoktanID 
            WHERE
                a.StatusCode = 'active' and xc.SupplychainID = ? and mrole.MRoleID = '1'
                $sqlFilter
				GROUP BY a.MemberID
				ORDER BY $sortingField $sortingDir
				LIMIT ?,?";
				$query = $this->db->query($sql,array($SupplychainID, (int) $start,(int) $limit)); 
				// echo '<pre>';
				// echo $this->db->last_query();die;
				$result['data'] = $query->result_array();
		
        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    private function sqlFilter($textSearch) {
        $sqlFilter = "";

        $sqlFilter .= " AND (a.MemberName like '%{$textSearch}%' OR a.MemberDisplayID like '%{$textSearch}%' ) ";

        return $sqlFilter;
    }
	
	public function get_AllFarmerdata($SupplychainID,$pSearch, $start,$limit,$sortingField,$sortingDir,$role = ''){
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

        if ($pSearch['textSearch'] != "") {
            $sqlFilter .= " AND (a.MemberName like '%{$pSearch['textSearch']}%' OR a.MemberDisplayID like '%{$pSearch['textSearch']}%' ) ";
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
        
        if($role == "mill"){
            $where = " AND a.SupplybaseType IN ('farmer','direct')";
        }else{
            $where = " AND a.SupplybaseType = 'farmer'";
        }
		
		$MemberID = rtrim($MemberID,","); 
		//print_r($MemberID);die; 
		$sql ="SELECT
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
            INNER JOIN ktv_member_role mr on mr.MemberID = a.MemberID
            INNER JOIN ktv_ref_member_role rmr on mr.MRoleID = rmr.MroleID AND rmr.MRoleType = 'Farmer'
			LEFT JOIN ktv_village c ON a.VillageID = c.VillageID
			LEFT JOIN ktv_subdistrict d ON SUBSTR(a.VillageID,1,7) = d.SubDistrictID
			LEFT JOIN ktv_province e ON SUBSTR(a.VillageID,1,2) = e.ProvinceID
			LEFT JOIN ktv_district f ON SUBSTR(a.VillageID,1,4) = f.DistrictID 
		WHERE
            mr.MRoleID = '1'
            and
			a.StatusCode = 'active' 
            $where
			$a and a.MemberID NOT IN ($MemberID) $b
			$sqlFilter
			GROUP BY a.MemberID
			ORDER BY $sortingField $sortingDir
			LIMIT ?,? ";
		$query = $this->db->query($sql,array( (int) $start,(int) $limit)); 
		// echo '<pre>';
		// echo $this->db->last_query();die;
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
