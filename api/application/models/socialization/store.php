<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Store extends CI_Model {
    public $user;
	private $sql_count = "SELECT FOUND_ROWS() AS total";

	public function __construct()
	{
		parent::__construct();
		$this->user = $this->muserprofile->getUserProfile();
	}

    public function getMainListAppForm($sort, $start=0, $limit=10,$key='', $ProvinceID='',$DistrictID='',$SubDistrictID='')
    {
		$filter = '';
        $limit_filter = '';
        $params = array();
		$sort = json_decode($sort); 
		$by = ($sort[0]->direction == '' ? 'ASC' : $sort[0]->direction);
		$order = ($sort[0]->property == '' ? 'EventName' : $sort[0]->property);
		 
		if ( !empty($key)) {
			$filter .= " AND EventName LIKE '%{$key}%' ";
        }
		if ( !empty($ProvinceID)) {
			$filter .= " AND ProvinceID = ".$ProvinceID."  ";
        }
		if ( !empty($DistrictID)) {
			$filter .= " AND DistrictID = ".$DistrictID."  ";
        }
		if ( !empty($SubDistrictID)) {
			$filter .= " AND SubDistrictID = ".$SubDistrictID."  ";
        }
		
		if (!empty($this->user['accessStaff'])) {
            $filter .= " AND DistrictID IN ({$this->user['accessStaff']})";
        }

		if (isset($start) && !empty($limit)) {
            $limit_filter .= " LIMIT ?, ?";
            $params[] = intval($start);
            $params[] = intval($limit);
        }

		$sql = "SELECT * FROM ktv_view_socialization WHERE StatusCode != 'nullified' " ;
		 
		$sql .= "  --filter-- ORDER BY " . $order ." ". $by . " --limit--";

		$sql = str_replace('--filter--', $filter, $sql);
        $sql = str_replace('--limit--', $limit_filter, $sql);
        $query = $this->db->query($sql, $params);
		//echo $this->db->last_query();die;
 

        //////COUNT/////////////////////////////////
		$sqls = "select count(*) as total FROM ktv_view_socialization WHERE StatusCode != 'nullified' " ;
		$sqls .= "  --filter--";
		$sqls = str_replace('--filter--', $filter, $sqls);
		$num_rows = $this->db->query($sqls)->row()->total;


		$result['data'] = $query->result_array();
        $result['total'] = $num_rows;
        return $result;

    }


	public function getMainListParticipantForm($sort, $start=0, $limit=10, $IMSSocID)
    {
		$filter = '';
        $limit_filter = '';
        $params = array();
		$sort = json_decode($sort); 
		$by = ($sort[0]->direction == '' ? 'ASC' : $sort[0]->direction);
		$order = ($sort[0]->property == '' ? 'ParticipantID' : $sort[0]->property);


		if (isset($start) && !empty($limit)) {
            $limit_filter .= " LIMIT ?, ?";
            $params[] = intval($start);
            $params[] = intval($limit);
        }
		$IMSSocID = $IMSSocID =='' ? '0' : $IMSSocID;

		$sql = "SELECT * FROM ktv_view_socialization_participant WHERE StatusCode != 'nullified' AND IMSSocID = $IMSSocID " ;
		$sql .= "  --filter-- ORDER BY " . $order . " " . $by . " --limit--";

		$sql = str_replace('--filter--', $filter, $sql); 
        $sql = str_replace('--limit--', $limit_filter, $sql);
        $query = $this->db->query($sql, $params);
		
		//////COUNT/////////////////////////////////
		$sqls = "select count(*) as total FROM ktv_view_socialization_participant WHERE StatusCode != 'nullified' AND IMSSocID = $IMSSocID " ; 
		$sqls .= "  --filter--";
		$sqls = str_replace('--filter--', $filter, $sqls);
		$num_rows = $this->db->query($sqls)->row()->total;


		$result['data'] = $query->result_array();
        $result['total'] = $num_rows;
        return $result;
    }
	
	

	public function getMainListapplication($sort, $start=0, $limit=10,$IMSID,$IMSSocID, $key='', $ProvinceID='',$DistrictID='',$SubDistrictID='', $CPGid='')
    {
		$filter = ' AND ( StatusCode != "nullified" ';
        $limit_filter = '';
        $params = array();
		
		$sort = json_decode($sort); 
		$by = ($sort[0]->direction == '' ? 'ASC' : $sort[0]->direction);
		$order = ($sort[0]->property == '' ? 'ApplicantID' : $sort[0]->property);
		
		//Ambil tabel sosialisasi
		 
		$options = array('IMSSocID' => $IMSSocID, 'StatusCode != ' => 'nullified' );
     	$getSocialization = $this->db->get_where('ktv_ims_socialization_participants', $options)->result();
		//echo $this->db->last_query();die;
	    $ApplicantID = '';
		if($getSocialization)
		{
			foreach($getSocialization as $Row)
			{
				$ApplicantID .= ",'". $Row->ApplicantID."'";
			}
		}
		 
		
		if ( !empty($IMSID)) {
			$filter .= " AND IMSID =".$IMSID." ";
        }
		if ( !empty($CPGid)) {
			$filter .= " AND CPGid = ".$CPGid."  ";
        }
		
		if ( !empty($key)) {
			$filter .= " AND Fullname LIKE '%{$key}%' ";
			$params[] = 0;
            $params[] = intval($limit);
        }
		
		if ( !empty($ProvinceID)) {
			$filter .= " AND ProvinceID = ".$ProvinceID."  ";
        }
		if ( !empty($DistrictID)) {
			$filter .= " AND DistrictID = ".$DistrictID."  ";
        }
		if ( !empty($SubDistrictID)) {
			$filter .= " AND SubDistrictID = ".$SubDistrictID."  ";
        }
		
		$filter .=' ) ';
		if ( !empty($key)) { 
			$filter .=' OR ApplicantID LIKE "%'.$key.'%" ';  
        }
		
		if (isset($start) && !empty($limit)) {
            $limit_filter .= " LIMIT ?, ?";
            $params[] = intval($start);
            $params[] = intval($limit);
        }

		//Cari / Tampilkan list ID Peserta yang mengikuti event sebelumnya komplete & selection status = 1 /lolos. maka wherenya NOT IN list dibawah ini.
		//dan mestinya tidak akan muncul kembali
		$getSQLApplicantIDAda = "SELECT A.ApplicantID  FROM ktv_view_applicant_farmers A
								LEFT JOIN ktv_ims_socialization_participants B ON A.ApplicantID = B.ApplicantID
								JOIN ktv_ims_socializations C ON B.IMSSocID = C.IMSSocID
								WHERE  
								A.IMSID = ? AND B.StatusCode != 'nullified' 
								and C.SocializationStatus = 1 and B.SelectionStatus = 1 
								ORDER BY A.ApplicantID ASC";
								
		$getApplicantIDAda = $this->db->query($getSQLApplicantIDAda, array( $IMSID ) )->result();
		//echo $this->db->last_query();die; 
		$appID ='';
		if($getApplicantIDAda)
		{   
			foreach($getApplicantIDAda as $row)
			{
				$appID .= ",'" .$row->ApplicantID."'";
			}
		}
		$appIDTrigger =  trim($appID, ',');

		$sql = "SELECT *, (select Village from ktv_village where VillageID = ktv_view_applicant_farmers.VillageID) as VillageNames,(SELECT  CONCAT('[',ax.CPGid,']',ax.GroupName) FROM ktv_cpg ax where ax.CPGid = ktv_view_applicant_farmers.CPGid limit 1) as GroupName FROM ktv_view_applicant_farmers WHERE 1=1  " ;
		//Tampilkan data dimana yg tidak ada di tabel socialisasi, jadi pas add participant gak muncul kedua kali
		 
		if(trim($ApplicantID,',') != ''){
			$sql .= " AND ApplicantID NOT IN (".trim($ApplicantID,',').")";
		}else{
			if($appIDTrigger !='') { $sql .= " AND ApplicantID NOT IN (".$appIDTrigger.") ";	}
		}
		 
		
		 
		//if($appIDTrigger !='') { $sql .= " AND ApplicantID NOT IN (".$appIDTrigger.") ";	}  
		$sql .= "  --filter-- ORDER BY " . $order . " " . $by . " --limit--";

		$sql = str_replace('--filter--', $filter, $sql);
        $sql = str_replace('--limit--', $limit_filter, $sql);
        $query = $this->db->query($sql, $params);
		//echo $this->db->last_query();die;
		
		
		//////COUNT/////////////////////////////////
		
		$sqls = "select count(*) as total FROM ktv_view_applicant_farmers  WHERE 1=1  " ; 
		if(trim($ApplicantID,',') != ''){
			$sqls .= " AND ApplicantID NOT IN (".trim($ApplicantID,',').")";
		}else{
			if($appIDTrigger !='') { $sqls .= " AND ApplicantID NOT IN (".$appIDTrigger.") ";	}
		}
		
		//if($appIDTrigger !='') { $sqls .= " AND ApplicantID NOT IN (".$appIDTrigger.") ";	}
		$sqls .= "  --filter--";
		$sqls = str_replace('--filter--', $filter, $sqls);
		 
		$num_rows = $this->db->query($sqls)->row()->total;
		 

		$result['data'] = $query->result_array();
        $result['total'] = $num_rows;
        return $result;
    }


    public function getmain_existingfarmer_list($sort, $start=0, $limit=10,$key='', $ProvinceID='',$DistrictID='',$SubDistrictID='', $CPGid)
    {
		 
		$filter = ' WHERE ( StatusFarmer = 1 ';
        $limit_filter = '';
        $params = array();
		$sort = json_decode($sort); 
		$by = ($sort[0]->direction == '' ? 'ASC' : $sort[0]->direction);
		$order = ($sort[0]->property == '' ? 'FarmerName' : $sort[0]->property);
		
		 
		if ( !empty($key)) { 
			$filter .=' AND FarmerName LIKE "%'.$key.'%" '; 
			$params[] = 0;
            $params[] = intval($limit);
        }
		 
		if ( !empty($ProvinceID)) {
			$filter .= " AND ProvinceID = ".$ProvinceID."  ";
        }
		if ( !empty($DistrictID)) {
			$filter .= " AND DistrictID = ".$DistrictID."  ";
        }
		if ( !empty($SubDistrictID)) {
			$filter .= " AND SubDistrictID = ".$SubDistrictID."  ";
        }
		
		if ( !empty($CPGid)) {
			$filter .= " AND CPGid = ".$CPGid."  ";
        }
		
		if (!empty($this->user['accessStaff'])) {
            $filter .= " AND DistrictID IN ({$this->user['accessStaff']})";
        }
		
		$filter .=' ) ';
		
		if ( !empty($key)) { 
			$filter .=' OR FarmerID LIKE "%'.$key.'%" ';  
        }
		
		if (isset($start) && !empty($limit)) {
            $limit_filter .= " LIMIT ?, ?";
            $params[] = intval($start);
            $params[] = intval($limit);
        }
		 
		$sql = "SELECT * FROM ktv_cocoa_farmer_view " ;
		$sql .= "  --filter-- ORDER BY " . $order . " " . $by . " --limit--";

		$sql = str_replace('--filter--', $filter, $sql); 
        $sql = str_replace('--limit--', $limit_filter, $sql);
		 
        $query = $this->db->query($sql, $params);
		//echo $this->db->last_query(); die;
		
		
		//////COUNT/////////////////////////////////
		$sqls = "select count(*) as total FROM ktv_cocoa_farmer_view " ; 
		$sqls .= $filter;
		 
		$num_rows = $this->db->query($sqls)->row()->total;
		//echo $this->db->last_query(); 

		$result['data'] = $query->result_array();
        $result['total'] = $num_rows;
        return $result;
    }
	
	
	function insertApp($post){

		$data_s_insert = array('CreatedBy' => $_SESSION['userid'], 'DateCreated' => date("Y-m-d H:i:s"));
		$data_s_update = array('LastModifiedBy' => $_SESSION['userid'], 'DateUpdated' => date("Y-m-d H:i:s"));

		//print_r($post);
		$data = array(
			'BatchID'=> $post['Koltiva_view_socialization_Form-Form-BatchID'],
			'CPGtrainingsID' => $post['Koltiva_view_socialization_Form-Form-CPGtrainingsID'],
			'ProvinceID' => $post['Koltiva_view_socialization_Form-Form-ProvinceID'],
			'DistrictID' => $post['Koltiva_view_socialization_Form-Form-DistrictID'],
			'SubDistrictID' => $post['Koltiva_view_socialization_Form-Form-SubDistrictID'],
			'VillageID' => $post['Koltiva_view_socialization_Form-Form-VillageID'],
			'VillageName' => $post['Koltiva_view_socialization_Form-Form-VillageName'],
			'CertHolderID' => $post['Koltiva_view_socialization_Form-Form-CertHolderID'],
			'CertProgID' => $post['Koltiva_view_socialization_Form-Form-CertProgID'],
 			'IMSID' => $post['Koltiva_view_socialization_Form-Form-IMSID'],
 			'IMSMasterID' => $post['Koltiva_view_socialization_Form-Form-IMSMasterID'],
			'EventName' => $post['Koltiva_view_socialization_Form-Form-EventName'],
			'CPGtrainingsID' => $post['Koltiva_view_socialization_Form-Form-CPGtrainingsID'],
			'EventStart' => $post['Koltiva_view_socialization_Form-Form-EventStart'],
			'EventEnd' => $post['Koltiva_view_socialization_Form-Form-EventEnd'],
			'EventDays' => $post['Koltiva_view_socialization_Form-Form-EventDays'],
			'Location' => $post['Koltiva_view_socialization_Form-Form-Location'],
			'PICStaffID' => $post['Koltiva_view_socialization_Form-Form-PICStaffID'],
			'Remarks' => $post['Koltiva_view_socialization_Form-Form-Remarks'],
			'SocializationStatus' => $post['Koltiva_view_socialization_Form-Form-CertEventStatus'],
			'StatusCode' => 'active'
		);

	//print_r($data);die;
  	$options = array('IMSSocID' => $this->input->post('Koltiva_view_socialization_Form-Form-IMSSocID'));
  	$Q = $this->db->get_where('ktv_ims_socializations', $options,1);

  	if($Q->num_rows() > 0){
		$this->db->trans_start();
  	    $data_ID = array('IMSSocID' => $this->input->post('Koltiva_view_socialization_Form-Form-IMSSocID')); 
		$this->db->update('ktv_ims_socializations',$data, $data_ID);
		//$this->db->update('ktv_ims_socializations',$data_s_update, $data_ID); 
		//echo $this->db->last_query();die;
  		$this->db->trans_complete(); 
		return $this->input->post('Koltiva_view_socialization_Form-Form-IMSSocID');
    }else{
    	$this->db->trans_start();
		$data_bpjkt = array_merge($data, $data_s_insert);
  		$this->db->insert('ktv_ims_socializations', $data_bpjkt);
  		$id = $this->db->insert_id();
		//echo $this->db->last_query();die;
  		$this->db->trans_complete();
		return $id;
    }
  	$Q->free_result();
  	$this->db->close();
	}

	function deleteAppform($id){

		$sql="UPDATE ktv_ims_socializations SET StatusCode = 'nullified',LastModifiedBy='".$_SESSION['userid']."',DateUpdated=NOW() WHERE IMSSocID =? LIMIT 1";
        $query = $this->db->query($sql, array($id));
        //echo $this->db->last_query();die;
		if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }


	function getLastInsertDataSocializ($id){
        $sql = "select * from ktv_ims_socializations where IMSSocID =? ";
		$query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result;
    }

	function loadappdata($id){
        $sql = "select * from ktv_view_socialization where IMSSocID =? ";
		$query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        $return['success'] = true;
        $return['data'] = $result[0];
        return $return;
    }

	function getDataSocialization($id){
        $sql = "select * from ktv_view_socialization where IMSSocID =? ";
		$query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        $return['success'] = true;
        $return['data'] = $result[0];
        return $return;
    }
    
	function CheckParticipantByTransfer($IMSSocID,$ApplicantID)
	{
		$opt = array('ApplicantID' => $ApplicantID);
		$Qs = $this->db->select('MobileUID')->from('ktv_applicant_farmers')->where($opt)->get()->row();
		//echo $this->db->last_query();
		 
		if($Qs){ 
			return $Qs; 
		}
		 
	}
	
	function getcheckedfromparticipant($IMSSocID)
	{
		$opt = array('IMSSocID' => $IMSSocID, 'StatusCode != ' => 'nullified');
		$Qs = $this->db->select('ApplicantID, MobileUID, ParticipantID')->from('ktv_ims_socialization_participants')->where($opt)->get()->result_array(); 
		return $Qs;
	} 
	
	function save_participant($post){

		$data_s_insert = array('CreatedBy' => $_SESSION['userid'], 'DateCreated' => date("Y-m-d H:i:s"));
		$data_s_update = array('LastModifiedBy' => $_SESSION['userid'], 'DateUpdated' => date("Y-m-d H:i:s"));

		$postAppID = trim($post['ParticipantID'],",");
		$exp = explode(',', $postAppID); 
		$result='';
		if(isset($exp))
		{
			for($i=0; $i<count($exp); $i++){
				$data = array(
					'IMSSocID'=> $post['IMSSocID'], 
					'StatusCode' => 'active'
				);
				 //Cek apakah ApplicantID dari mobile atau bukan
				 //syaratnya mobileUID berisi angka 15 digit 
				 $ch= $this->CheckParticipantByTransfer($post['IMSSocID'], $exp[$i]); 				
				 if($ch->MobileUID !=''){ 
				 
				    //cek kembali mobiluid ada di tabel participant atau enggak 
					$arr = array('IMSSocID' => $post['IMSSocID'], 'MobileUID' => $exp[$i]);
					$ds = $this->db->from('ktv_ims_socialization_participants')->where($arr)->get()->num_rows();
					
					//Ini version langsung dari mobile si registrasinya, maka participasinya jalankan perintah ini untuk menyimpan data
					if($ds > 0){
						//update datanya
						$this->db->trans_start();
						//print_r($data);die;
						unset($data['IMSSocID']);
						$data_ID = array('IMSSocID' => $post['IMSSocID'], 'MobileUID' => $exp[$i]); 
						$this->db->update('ktv_ims_socialization_participants',$data, $data_ID);  
						$this->db->update('ktv_ims_socialization_participants',$data_s_update, $data_ID);
						$this->db->trans_complete(); 
						$result = 1; 
					}
					else
					{
						
						$data['ApplicantID'] = $exp[$i]; 
						$options = array('IMSSocID' => $post['IMSSocID'], 'ParticipantID' => $exp[$i]);
						$Q = $this->db->get_where('ktv_ims_socialization_participants', $options,1);
						if($Q->num_rows() > 0){ 
								$this->db->trans_start();
								unset($data['IMSSocID']);
								
								$data_ID = array('IMSSocID' => $post['IMSSocID'], 'ParticipantID' => $exp[$i]); 
								$this->db->update('ktv_ims_socialization_participants',$data, $data_ID);  
								$this->db->update('ktv_ims_socialization_participants',$data_s_update, $data_ID);
								$this->db->trans_complete(); 
								$result = 1; 
						}else{
							$this->db->trans_start(); 
							$data_bpjkt = array_merge($data, $data_s_insert);
							//print_r($data_bpjkt);die;
							$this->db->insert('ktv_ims_socialization_participants', $data_bpjkt);
							$id = $this->db->insert_id();
							$this->db->trans_complete();
							//echo $this->db->last_query();die;
							$result =  $id;
						}
					
						$Q->free_result();
						$this->db->close();
					} 
				 }
				 else 
				 {   			
					
					//Ini version langsung dari web si registrasinya, maka participasinya jalankan perintah ini untuk menyimpan data 
					$data['ParticipantID'] = $exp[$i];  
					$options = array('IMSSocID' => $post['IMSSocID'], 'ParticipantID' => $exp[$i]);
					$Q = $this->db->get_where('ktv_ims_socialization_participants', $options,1);
						if($Q->num_rows() > 0){ 
								$this->db->trans_start();
								unset($data['IMSSocID']);
								$data_ID = array('IMSSocID' => $post['IMSSocID'], 'ParticipantID' => $exp[$i]); 
								$this->db->update('ktv_ims_socialization_participants',$data, $data_ID);  
								$this->db->update('ktv_ims_socialization_participants',$data_s_update, $data_ID);
								$this->db->trans_complete(); 
								$result = 1; 
						}else{
							$this->db->trans_start();
							
							$data_bpjkt = array_merge($data, $data_s_insert);
							//print_r($data_bpjkt);die;
							$this->db->insert('ktv_ims_socialization_participants', $data_bpjkt);
							$id = $this->db->insert_id();
							$this->db->trans_complete();
							//echo $this->db->last_query();die;
							$result =  $id;
						}
					
						$Q->free_result();
						$this->db->close(); 
				}
			}//for
			return $result; 
    	}
			
	}
	
	function saveexistingfarmerbyweb()
	{ 
		$data_s_insert = array('CreatedBy' => $_SESSION['userid'], 'DateCreated' => date("Y-m-d H:i:s"));
		$data_s_update = array('LastModifiedBy' => $_SESSION['userid'], 'DateUpdated' => date("Y-m-d H:i:s"));
		  
		$data = array( 'IMSSocID'=>  $this->input->post('IMSSocID'), 'StatusCode' => 'active','MobileUID' => $this->input->post('ApplicantID') );
		$options = array('IMSSocID' =>  $this->input->post('IMSSocID'), 'MobileUID' => $this->input->post('ParticipantID'));
		$Q = $this->db->get_where('ktv_ims_socialization_participants', $options,1);
		 
			if($Q->num_rows() > 0){ 
					$this->db->trans_start();
					unset($data['IMSSocID']);
					$data_ID = array('IMSSocID' => $this->input->post('IMSSocID'), 'MobileUID' => $this->input->post('ParticipantID') ); 
					$this->db->update('ktv_ims_socialization_participants',$data, $data_ID);
					$this->db->trans_complete(); 
					//echo $this->db->last_query();die;		
					$this->db->trans_start();					
					$this->db->update('ktv_ims_socialization_participants',$data_s_update, $data_ID);  
					$this->db->trans_complete(); 
					$result = 1; 
			}else{
				$this->db->trans_start(); 
				$data_bpjkt = array_merge($data, $data_s_insert); 
				$this->db->insert('ktv_ims_socialization_participants', $data_bpjkt); 
				$id = $this->db->insert_id();
				$this->db->trans_complete(); 
				$result =  $id;
			}
		
			$Q->free_result();
			$this->db->close(); 
			return $result; 
	}

	public function comboharievent($IMSSocID){
        $sql="select CASE WHEN datediff (EventEnd , EventStart) = 0 Then 1
			  ELSE datediff (EventEnd , EventStart) + 1 end as hari
			  from ktv_ims_socializations where IMSSocID = ? ";
        $query = $this->db->query($sql, array($IMSSocID));
		$Row = $query->row();

		$data = array();
		if($Row){
			for($i=1; $i <= $Row->hari; $i++)
			{
				$data[]['hari'] = $i;
			}
		}
		//print_r($data);die;
		//echo $this->db->last_query();die;
		return $data;
    }

	function save_attandance_participant($post){
		$data = array('LastModifiedBy' => $_SESSION['userid'], 'DateUpdated' => date("Y-m-d H:i:s"),'AttendanceStatus' => $post['checked'] );
		if($post['MobileUID']!=''){
			$data_ID = array('IMSSocID' => $post['IMSSocID'], 'MobileUID' => $post['MobileUID'], 'DayNumber' => $post['DayNumber']);
		}else{
			$data_ID = array('IMSSocID' => $post['IMSSocID'], 'ApplicantID' => $post['ApplicantID'], 'DayNumber' => $post['DayNumber']);
		} 
		
		$this->db->trans_start();
		$this->db->update('ktv_ims_socialization_attendance',$data, $data_ID);
		//echo $this->db->last_query();die;
		$this->db->trans_complete();
		$result =  1;

		//Update ParticipateInSocializationStatus = 1 jika checked = true
		if($post['MobileUID']!=''){
			$data_ID = array('IMSSocID' => $post['IMSSocID'], 'MobileUID' => $post['MobileUID']);
		}else{
			$data_ID = array('IMSSocID' => $post['IMSSocID'], 'ApplicantID' => $post['ApplicantID']);
		}
		
		$this->db->where($data_ID);
		$q = $this->db->select('MIN(AttendanceStatus) as status')->from('ktv_ims_socialization_attendance')->get()->row();
		
		$status = '';
		if($q->status == 2)
		{
			$status = 2;
		}else{
          	$status = 1;
		}
		
		if($post['MobileUID']!=''){
			$data_ID = array('IMSSocID' => $post['IMSSocID'], 'MobileUID' => $post['MobileUID']);
		}else{
			$data_ID = array('IMSSocID' => $post['IMSSocID'], 'ApplicantID' => $post['ApplicantID']);
		}
		
		$this->db->where($data_ID);
		$this->db->update('ktv_ims_socialization_participants', array('ParticipateInSocializationStatus' => $status ));

		return $result;


	}

	public function main_attandance_list($sort, $start=0, $limit=10, $IMSSocID, $DayNumber)
    {
		$filter = '';
        $limit_filter = '';
        $params = array();
		$sort = json_decode($sort); 
		$by = ($sort[0]->direction == '' ? 'ASC' : $sort[0]->direction);
		$order = ($sort[0]->property == '' ? 'Fullname' : $sort[0]->property);
		
		
		if (isset($start) && !empty($limit)) {
            $limit_filter .= " LIMIT ?, ?";
            $params[] = intval($start);
            $params[] = intval($limit);
        }
		$IMSSocID = $IMSSocID =='' ? '0' : $IMSSocID;

		$sql = "SELECT * FROM ktv_view_socialization_attandance WHERE StatusCode != 'nullified' AND IMSSocID = $IMSSocID and DayNumber = $DayNumber  " ;
		$sql .= "  --filter-- ORDER BY " . $order . " " . $by . " --limit--";

		$sql = str_replace('--filter--', $filter, $sql);
        $sql = str_replace('--limit--', $limit_filter, $sql);
        $query = $this->db->query($sql, $params);
		//echo $this->db->last_query();die;
		
		$sqls = "select count(*) as total from ktv_view_socialization_attandance WHERE StatusCode != 'nullified' AND IMSSocID = $IMSSocID and DayNumber = $DayNumber " ;
		$sqls .= "  --filter--";
		$sqls = str_replace('--filter--', $filter, $sqls);
		$num_rows = $this->db->query($sqls)->row()->total;
		
        $result['data'] = $query->result_array();
        $result['total'] = $num_rows;
        return $result;
    }

	//INSERT SEMUA DATA PARTICIPANT KE ATTANDANCE BERDASARKAN IMSSocID
	function save_participant_to_attadance($post){
		//hapus data daynumber = 0
		$SQLDELNULL = "DELETE FROM ktv_ims_socialization_attendance WHERE IMSSocID = ? ";
		$this->db->query($SQLDELNULL, array($post['IMSSocID']) );
		
		$SQL = "select IMSSocID,ApplicantID,MobileUID from ktv_view_socialization_participant WHERE IMSSocID = ? ";
		$q = $this->db->query($SQL, array($post['IMSSocID']) );
		//echo $this->db->last_query();die;

		//Masukkan data participant yang tidak terabsen sebelumnya atau AttendanceStatus = 2
		//sekarang inputkan data barunya
		if($q->result()){
			foreach($q->result() as $Row){
				$datas = array(
					'IMSSocID'=> $Row->IMSSocID,
					'ApplicantID' => $Row->ApplicantID,
					'MobileUID' =>  $Row->MobileUID,
					'DayNumber'=> $post['DayNumber'],
					'EventDate'=> $post['EventDate'],
					'StatusCode' => 'active',
					'AttendanceStatus' => 2,
					'CreatedBy' => $_SESSION['userid'],
					'DateCreated' => date("Y-m-d H:i:s")
				);
				
				if($Row->MobileUID !=''){ 
					$options = array('IMSSocID' => $Row->IMSSocID, 'MobileUID' => $Row->MobileUID, 'DayNumber' => $post['DayNumber']);
				}else{ 
					$options = array('IMSSocID' => $Row->IMSSocID, 'ApplicantID' => $Row->ApplicantID, 'DayNumber' => $post['DayNumber']);
				}
				$Q = $this->db->get_where('ktv_ims_socialization_attendance', $options,1);
				//echo $this->db->last_query();die;
				if($Q->num_rows() > 0){
					//Do not Insert again...
				}else{
					$this->db->insert('ktv_ims_socialization_attendance', $datas);
				}
			}
		}
		return 1;

	}

	//Cetak header KOSONG
	function getDataSocializationCetakHeader($id){
        $sql = "select * from ktv_view_socialization where IMSSocID = ? ";
		$query = $this->db->query($sql, array($id));
		//echo $this->db->last_query();die;
        return $query->row_array();
    }
	function getDataParticipantCetak($id){
        $sql = "select *, (select Village from ktv_village where VillageID = ktv_view_socialization_participant.VillageID) as Village from ktv_view_socialization_participant where IMSSocID = ?  and StatusCode ='active'";
		$query = $this->db->query($sql, array($id));
		//echo $this->db->last_query();die;
        return $query->result_array();
    }

	function getDataStaffCetak($id){
        $sql = "select * from ktv_view_socialization_staff where IMSSocID = ? ";
		$query = $this->db->query($sql, array($id));
		//echo $this->db->last_query();die;
        return $query->result_array();
    }

	function getDataSocializationAttRow($id, $days){
        $sql = "select EventDate from ktv_ims_socialization_attendance where IMSSocID = ? and DayNumber = ? ";
		$query = $this->db->query($sql, array($id,$days));
		//echo $this->db->last_query();die;
        return $query->row()->EventDate;
    }

	function getDataAttandanceCetak($id, $days){
        $sql = "select *, (select Village from ktv_village where VillageID = ktv_view_socialization_attandance.VillageID) as Village from ktv_view_socialization_attandance where IMSSocID = ? and DayNumber = ? and StatusCode ='active' group by  ApplicantID, MobileUID, DayNumber";
		$query = $this->db->query($sql, array($id,$days));
		//echo $this->db->last_query();die;
        return $query->result_array();
    }

	function getDataStaffAttandanceCetak($id, $days){
        $sql = "select * from ktv_view_socialization_attandance_staff where IMSSocID = ? and DayNumber = ? ";
		$query = $this->db->query($sql, array($id,$days));
		//echo $this->db->last_query();die;
        return $query->result_array();
    }

	function readPartnerLogo($id) {
        $sql = "select A.* from ktv_ims_logo A, ktv_ims_socializations B where A.IMSID = B.IMSID and B.IMSSocID = ? and A.StatusCode ='active' order by A.PhotoOrder asc ";
        $query = $this->db->query($sql, array($id));
        $result = $query->result();
        return $result;
    }

	function savedataRekomendasi($post){

		$data_s_insert = array('CreatedBy' => $_SESSION['userid'], 'DateCreated' => date("Y-m-d H:i:s"));
		$data_s_update = array('LastModifiedBy' => $_SESSION['userid'], 'DateUpdated' => date("Y-m-d H:i:s"));


		$data = array(
			'RecommendationStatus' => $post['Koltiva_view_socialization_Form-Form-RecommendationStatus'],
			'FieldAgentName' => $post['Koltiva_view_socialization_Form-Form-FieldAgentName'],
			'RecommendationDate' => $post['Koltiva_view_socialization_Form-Form-RecommendationDate'],
			'Comments' => $post['Koltiva_view_socialization_Form-Form-Comments'],
			"SelectionStatus" => $post['Koltiva_view_socialization_Form-Form-RecommendationStatus'],
			'SelectionRemarks' => $post['Koltiva_view_socialization_Form-Form-Comments'],
		);
		
		$data_ID = array('IMSSocID' => $post['Koltiva_view_socialization_Form-Form-HiddenIMSSocIDRekomndasi']);
		$ApplicantID = $post['Koltiva_view_socialization_Form-Form-HiddenApplicantIDRekomndasi'];				 
		if(strlen($ApplicantID) == 15  || strlen($ApplicantID) == 9 ){ //By Mobil Version Transfer AppID 
			$data_ID['MobileUID'] = $post['Koltiva_view_socialization_Form-Form-HiddenApplicantIDRekomndasi'];
		}else{
			$data_ID['ParticipantID'] = $post['Koltiva_view_socialization_Form-Form-HiddenApplicantIDRekomndasi'];
		}  
						 
		$this->db->trans_start();
		$result = $this->db->update('ktv_ims_socialization_participants',$data, $data_ID);
		//echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
		$result = $this->db->update('ktv_ims_socialization_participants',$data_s_update, $data_ID);
		//echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;

		$this->db->trans_complete();
		$this->db->close();
		return $result;

	}
    
	//BERDASARKAN UAT MAKASSAR INI TIDAK DIPAKAI
	/*
	function savedata_selection($post){

		$data_s_insert = array('CreatedBy' => $_SESSION['userid'], 'DateCreated' => date("Y-m-d H:i:s"));

		$data = array(
			'SelectionStatus' => $post['Koltiva_view_socialization_Form-Form-SelectionStatus'],
			'SelectionRemarks' => $post['Koltiva_view_socialization_Form-Form-SelectionRemarks'],
			'LastModifiedBy' => $_SESSION['userid'],
			'DateUpdated' => date("Y-m-d H:i:s")
		);

		$data_ID = array('IMSSocID' => $post['Koltiva_view_socialization_Form-Form-HiddenIMSSocIDSelectionStatus'],
						 'ApplicantID' => $post['Koltiva_view_socialization_Form-Form-HiddenApplicantIDSelectionStatus']);
		$this->db->trans_start();
		$result = $this->db->update('ktv_ims_socialization_participants',$data, $data_ID);
		//echo $this->db->last_query();
		
		
		//generate Farmer data to tables
		$AppFarmerTables = $this->db->from('ktv_applicant_farmers')
							->where('ApplicantID', $post['Koltiva_view_socialization_Form-Form-HiddenApplicantIDSelectionStatus'])
							->get()->row();

		$farmerID = $this->_generateFarmerID($AppFarmerTables->DistrictID);
		
		$data= array( 'farmerID' => $farmerID,
					'ApplicantID' => $post['Koltiva_view_socialization_Form-Form-HiddenApplicantIDSelectionStatus'],
					'CPGid' => $AppFarmerTables->CPGid,
					'FarmerName' => $AppFarmerTables->Fullname,
					'DateCollection' => date('Y-m-d'),
					'Gender' => $AppFarmerTables->Gender == 'f' ? 1 : 2,
					'VillageID' => $AppFarmerTables->VillageID,
					'Address' => $AppFarmerTables->Address,
					'StatusCode' => 'active',
					'CreatedBy' => 1,
					'DateCreated' => date("Y-m-d H:i:s")
					);
		$log = $this->db->insert('ktv_cocoa_farmer', $data);
		//echo $this->db->last_query(); die;
		
		
		$data2 = array('FarmerID' => $farmerID,
					'PartnerID' => $this->_getPatnerID($post['Koltiva_view_socialization_Form-Form-HiddenIMSSocIDSelectionStatus']) ,
					'FarmertypeID' => $AppFarmerTables->FarmertypeID,
					'StatusCode' => 'active',
					'CreatedBy' => 1,
					'DateCreated' => date("Y-m-d H:i:s")
					);
		if($log){
			$result =  $this->db->insert('ktv_cocoa_farmer_type', $data2);
			//echo $this->db->last_query(); die;
		    //Generato To mobile
            $urldhis = $this->config->item('dhis_url');
            $cmd = 'curl --request GET --url "'.$urldhis.'/api/dhis/synccocoatracedhis?program=QxauNvjcpBw&farmer='.$farmerID.'" --header "authorization: Basic YWRtaW46S29sdGl2YUFiMjAxNyE="';
            exec($cmd);

            //Load model from dhis module
            $this->load->model('dhis/mdsync', '_dsync');

            //Get farmer data from view view_program_farmer
            $farmers = $this->_dsync->getDataByDistrict(false, true, 'QxauNvjcpBw', $farmerID);

            //Found? sync the data to dhis
            if ($farmers) {
                $this->_dsync->syncDataPerProgram($farmers,'QxauNvjcpBw',substr($farmerID, 0, 4));
            }
            //End
		}
		 
		$this->db->trans_complete();
		$this->db->close();
		return $result;

	}
	*/
	
	//ini dijalankan oleh system server
	function cronGenerateFarmerbySocialization()
	{
		$SQL ="select B.IMSSocID, A.FarmertypeID, A.DateOfBirth, A.ApplicantID, A.MobileUID, A.CPGid, A.Fullname,A.Gender,A.VillageID, A.Address,A.DistrictID from 
				ktv_applicant_farmers A, 
				ktv_ims_socialization_participants B
				WHERE 
				(
					A.ApplicantID = B.ApplicantID AND B.SelectionStatus = 1
					OR (A.MobileUID = B.MobileUID AND B.SelectionStatus = 1 AND A.`MobileUID` != 0 AND B.`MobileUID` != 0)
				)
				AND A.`ApplicantID` NOT IN (
					SELECT
						DISTINCT a.`ApplicantID`
					FROM
						ktv_cocoa_farmer a
					WHERE
						a.`ApplicantID` IS NOT NULL
				)";
		$AppFarmerTables = $this->db->query($SQL)->result();
		if($AppFarmerTables){
			foreach($AppFarmerTables as $Row)
			{
				$farmerID = $this->_generateFarmerID($Row->DistrictID);	 
				
				$data= array( 'farmerID' => $farmerID,
					'ApplicantID' =>$Row->ApplicantID,
					'CPGid' => $Row->CPGid,
					'FarmerName' => $Row->Fullname,
					'DateCollection' => date('Y-m-d'),
					'Gender' => $Row->Gender == 'm' ? 1 : 2,
					'VillageID' => $Row->VillageID, 
					'Birthdate' => $Row->DateOfBirth,
					'Address' => $Row->Address,
					'StatusCode' => 'active',
					'CreatedBy' => 1,
					'DateCreated' => date("Y-m-d H:i:s")
					);
				
				//1. cek dulu jika farmer existing yg 9 digit sudah ada, jgn dimasukkan lagi  
				if($Row->MobileUID !='' || strlen($Row->MobileUID) != 9 || $Row->ApplicantID !='' ){
					 
					//2.Jika tabel farmer sudah ada ApplicantID yg sesuai, jgn di insert lagi
					$SQLCheckFarmer = "select FarmerID from ktv_cocoa_farmer where ApplicantID = ? ";
					$ChkFarmer = $this->db->query($SQLCheckFarmer, array($Row->ApplicantID));
				    //echo $this->db->last_query();
					 
					$log ='';
					$this->db->trans_start();
					if($ChkFarmer->num_rows() == 0 ){
						$log = $this->db->insert('ktv_cocoa_farmer', $data);
					}
					$this->db->trans_complete();
					//echo $ChkFarmer->num_rows();die;
					 
					
					/////////////////////////Kemudian tahap mendaftarkan di farmer type///////////////////////
					$data2 = array(
						'FarmerID' => $farmerID,
						'PartnerID' => $this->_getPatnerID($Row->IMSSocID) ,
						'FarmertypeID' => $Row->FarmertypeID,
						'StatusCode' => 'active',
						'CreatedBy' => 1,
						'DateCreated' => date("Y-m-d H:i:s")
						);
					if($log !=''){
						
						//Jika tabel farmer sudah ada ApplicantID yg sesuai, jgn di insert lagi 
						if($ChkFarmer->num_rows() == 0 ){
							$this->db->trans_start();
							$result =  $this->db->insert('ktv_cocoa_farmer_type', $data2);

							//Tabel Farmer Access ==================================== (Begin)
							$sql = "INSERT INTO ktv_farmer_access SET
										FarmerID = ?,
										PartnerID = ( SELECT PartnerID FROM ktv_ref_farmer_type WHERE FarmertypeID = ? LIMIT 1 ),
										DateGenerated = NOW(),
										GeneratedBy = ?";
							$p = array(
								$data2['FarmerID'],
								$data2['FarmertypeID'],
								1
							);
							$query = $this->db->query($sql,$p);

							$sql = "INSERT INTO ktv_farmer_access (FarmerID,PartnerID,DateGenerated,GeneratedBy)
									VALUES (?,37,NOW(),?) 
									ON DUPLICATE KEY
										UPDATE DateGenerated=NOW(), GeneratedBy=?";
							$p = array(
								$data2['FarmerID'],1,1
							);
							$query = $this->db->query($sql,$p);
							//Tabel Farmer Access ==================================== (End)

							$this->db->trans_complete();
						}
						
						//echo $this->db->last_query(); die;
						//Generato To mobile
						$urldhis = $this->config->item('dhis_url');
						$cmd = 'curl --request GET --url "'.$urldhis.'/api/dhis/synccocoatracedhis?program=QxauNvjcpBw&farmer='.$farmerID.'" --header "authorization: Basic YWRtaW46S29sdGl2YUFiMjAxNyE="';
						exec($cmd);

						//Load model from dhis module
						$this->load->model('dhis/mdsync', '_dsync');

						//Get farmer data from view view_program_farmer
						$farmers = $this->_dsync->getDataByDistrict(false, true, 'QxauNvjcpBw', $farmerID);
	  
						//Found? sync the data to dhis
						if ($farmers) {
							$this->_dsync->syncDataPerProgram($farmers,'QxauNvjcpBw',substr($farmerID, 0, 4));
						}
						//End
					}
					
				}//existing
			}
		}
		
		
		$this->db->close();
		  
	}

	public function GenFarmerFromSoc($ApplicantIDIn){
		$ArrTmp = explode("::",$ApplicantIDIn);
		$ProsesCountSuccess = array();
		$ProsesCountFailed = array();

		if(isset($ArrTmp[0])){
			for ($i=0; $i < count($ArrTmp); $i++) { 
				$ApplicantID = $ArrTmp[$i];

				$this->db->trans_start();

				//Get Informasi Applicant
				$sql = "SELECT
							b.`IMSSocID`
							, a.`FarmertypeID`
							, a.DateOfBirth
							, a.ApplicantID
							, a.MobileUID
							, a.CPGid
							, a.Fullname
							,a.Gender
							,a.VillageID
							, a.Address
							,a.DistrictID
						FROM
							ktv_applicant_farmers a
							LEFT JOIN ktv_ims_socialization_participants b ON 1=1
								AND b.`ApplicantID` = a.`ApplicantID`
						WHERE
							a.`StatusCode` = 'active'
							AND b.`StatusCode` = 'active'
							AND b.`SelectionStatus` = '1'
							AND a.`ApplicantID` = ?
							AND a.`DistrictID` != ''
							AND a.`DistrictID` IS NOT NULL
							AND a.`DistrictID` != '0'
						";
				$DataApp = $this->db->query($sql,array($ApplicantID))->row_array();
				
				if(isset($DataApp['ApplicantID'])){
					//Cek sudah terdaftar di farmer belum
					$sql = "SELECT
								far.`FarmerID`
							FROM
								ktv_cocoa_farmer far 
							WHERE
								far.`ApplicantID` = ?
							LIMIT 1";
					$DataCek = $this->db->query($sql,array($DataApp['ApplicantID']));
					if($DataCek->num_rows() == 0 ){

						$FarmerID = $this->_generateFarmerID($DataApp['DistrictID']);

						$DataInsertFarmer = array(
							'FarmerID' => $FarmerID,
							'ApplicantID' =>$DataApp['ApplicantID'],
							'CPGid' => $DataApp['CPGid'],
							'FarmerName' => $DataApp['Fullname'],
							'DateCollection' => date('Y-m-d'),
							'Gender' => $DataApp['Gender'] == 'm' ? 1 : 2,
							'VillageID' => $DataApp['VillageID'], 
							'Birthdate' => $DataApp['DateOfBirth'],
							'Address' => $DataApp['Address'],
							'StatusCode' => 'active',
							'CreatedBy' => 1,
							'DateCreated' => date("Y-m-d H:i:s")
						);
						$InsertFarmer = $this->db->insert('ktv_cocoa_farmer', $DataInsertFarmer);

						$DataInsertFarmerType = array(
							'FarmerID' => $FarmerID,
							'PartnerID' => $this->_getPatnerID($DataApp['IMSSocID']) ,
							'FarmertypeID' => $DataApp['FarmertypeID'],
							'StatusCode' => 'active',
							'CreatedBy' => 1,
							'DateCreated' => date("Y-m-d H:i:s")
						);
						$result =  $this->db->insert('ktv_cocoa_farmer_type', $DataInsertFarmerType);

						//Load model from dhis module
						$this->load->model('dhis/mdsync', '_dsync');

						//Get farmer data from view view_program_farmer
						$ProsesDataDistrictDHIS = $this->_dsync->getDataByDistrict(false, true, 'QxauNvjcpBw', $FarmerID, '');
	  
						//Found? sync the data to dhis
						if ($ProsesDataDistrictDHIS != false) {
							$this->_dsync->syncDataPerProgram($ProsesDataDistrictDHIS,'QxauNvjcpBw',substr($FarmerID, 0, 4));
						}

						if ($this->db->trans_status() === false) {
							$this->db->trans_rollback();
							
							$ProsesCountFailed[] = array(
								'ApplicantID' => $ArrTmp[$i],
								'Message' => 'Query Error'
							);
						} else {
							$this->db->trans_commit();
							$ProsesCountSuccess[] = array(
								'ApplicantID' => $ArrTmp[$i],
								'Message' => 'Success'
							);
						}

					}else{
						$this->db->trans_rollback();
						$ProsesCountFailed[] = array(
							'ApplicantID' => $ArrTmp[$i],
							'Message' => 'Applicant sudah terdaftar'
						);
					}
				}else{
					$this->db->trans_rollback();
					$ProsesCountFailed[] = array(
						'ApplicantID' => $ArrTmp[$i],
						'Message' => 'Data applicant tidak ketemu / Data DistrictID tidak ada'
					);
				}
			}
		}

		$return['success'] = $ProsesCountSuccess;
		$return['failed'] = $ProsesCountFailed;
		return $return;
	}
	
	
	function _getPatnerID($IMSSocID){
		$SQL = "SELECT
				part.`PartnerID` 
			FROM
				ktv_ims_socializations a  
				LEFT JOIN ktv_cpg_batch bat ON a.`BatchID` = bat.`CpgBatchID`
				LEFT JOIN ktv_program_partner part ON bat.`PartnerID` = part.`PartnerID`
			WHERE
				a.`IMSSocID` = ?
			LIMIT 1";
			return $this->db->query($SQL, array($IMSSocID) )->row()->PartnerID;	
	}

	 public function _generateFarmerID($district) {
		//cek data province
        $sql = "SELECT
                    prov.`ProvinceID`
                    , dis.`DistrictID`
                FROM
                    ktv_district dis
                    INNER JOIN ktv_province prov ON dis.`ProvinceID` = prov.`ProvinceID`
                WHERE
                    dis.`DistrictID` = ?
                LIMIT 1";
        $DataWil = $this->db->query($sql,array($district))->row_array();
        if(strlen($DataWil['ProvinceID']) == 5) {
            $DistrictID = $DataWil['DistrictID'];
        } else {
            $DistrictID = $district;
        }

        $sql = "SELECT
                    a.`FarmerID`
                    , RIGHT(a.`FarmerID`,5) AS IncFarmer
                FROM
                    ktv_cocoa_farmer a
                WHERE
                    LEFT(a.`FarmerID`,4) = ?
                ORDER BY a.`FarmerID` DESC
                LIMIT 1";
        $p = array(
            $DistrictID
        );
        $data = $this->db->query($sql,$p)->row_array();
        if(isset($data['IncFarmer'])){
            $IncFarmer = (int) $data['IncFarmer'];
            $IncFarmer++;
            $FarmerID = '';

            switch(strlen($IncFarmer)){
                case 1:
                    $FarmerID = $DistrictID.'0000'.$IncFarmer;
                break;
                case 2:
                    $FarmerID = $DistrictID.'000'.$IncFarmer;
                break;
                case 3:
                    $FarmerID = $DistrictID.'00'.$IncFarmer;
                break;
                case 4:
                    $FarmerID = $DistrictID.'0'.$IncFarmer;
                break;
                default:
                    $FarmerID = $DistrictID.$IncFarmer;
                break;
            }
            return $FarmerID;
        }else{
            return $DistrictID.'00001';
        }
    }

	 public function Getmain_staff($start=0, $limit=10, $key='', $IMSSocID)
    {
		$filter = '';
        $limit_filter = '';
        $params = array();


		if ( !empty($key)) {
			$filter .= " AND PersonNm LIKE '%{$key}%' ";
        }

		if (isset($start) && !empty($limit)) {
            $limit_filter .= " LIMIT ?, ?";
            $params[] = intval($start);
            $params[] = intval($limit);
        }

		$sql = "select  StaffID , PersonNm, case when f_staffchecked(StaffID,".$IMSSocID.") = 1 then 'true' else 'false' end as status_checked
				from ktv_view_socialization_list_staff where  StatusCode = 'active' " ;
		$filter .= " ORDER BY StaffID ASC ";
		$sql .= "  --filter-- --limit--";

		$sql = str_replace('--filter--', $filter, $sql);
        $sql = str_replace('--limit--', $limit_filter, $sql);
        $query = $this->db->query($sql, $params);
		//echo $this->db->last_query();die;

		//////COUNT/////////////////////////////////
		$sqls = "select count(*) as total from ktv_view_socialization_list_staff where StatusCode = 'active' " ;
		$sqls .= "  --filter--";
		$sqls = str_replace('--filter--', $filter, $sqls);
		$num_rows = $this->db->query($sqls)->row()->total;


		$result['data'] = $query->result_array();
        $result['total'] = $num_rows;
        return $result;

    }

	 public function Getmain_list_staff($start=0, $limit=10, $IMSSocID)
    {
		$filter = '';
        $limit_filter = '';
        $params = array();

		if (isset($start) && !empty($limit)) {
            $limit_filter .= " LIMIT ?, ?";
            $params[] = intval($start);
            $params[] = intval($limit);
        }

		$sql = "select * from ktv_view_socialization_staff where  StatusCode = 'active' and IMSSocID =".$IMSSocID." " ;

        $sql = str_replace('--limit--', $limit_filter, $sql);
        $query = $this->db->query($sql, $params);
		//echo $this->db->last_query();die;

		//////COUNT/////////////////////////////////
		$sqls = "select count(*) as total from ktv_view_socialization_staff where StatusCode = 'active' and IMSSocID =".$IMSSocID." " ;
		$sqls .= "  --filter--";
		$sqls = str_replace('--filter--', $filter, $sqls);
		$num_rows = $this->db->query($sqls)->row()->total;


		$result['data'] = $query->result_array();
        $result['total'] = $num_rows;
        return $result;

    }


	//INSERT STAFF BERDASARKAN IMSSocID
	function save_staff($post){
		$data_s_insert = array('CreatedBy' => $_SESSION['userid'], 'DateCreated' => date("Y-m-d H:i:s")); 
		$status = '';
		if( $post['checked'] == 2)
		{
			$data_ID = array('IMSSocID' => $post['IMSSocID'], 'StaffID' => $post['StaffID']);
			$this->db->trans_start();
			$this->db->delete('ktv_ims_socialization_staff',$data_ID);
			$this->db->trans_complete();
			return 1;
		}else{
			$this->db->trans_start(); 
			$data_ID = array('IMSSocID' => $post['IMSSocID'], 'StaffID' => $post['StaffID'],'StatusCode' => 'active');
			$data_bpjkt = array_merge($data_ID, $data_s_insert);
			$this->db->insert('ktv_ims_socialization_staff', $data_bpjkt);
			$id = $this->db->insert_id();
			$this->db->trans_complete();
			return $id;
		}

 		$this->db->close();

	}


	function Hapusstaff($id){
		$sql="DELETE FROM ktv_ims_socialization_staff WHERE SocStaffID =? ";
        $query = $this->db->query($sql, array($id));
        //echo $this->db->last_query();die;
		if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

	//INSERT SEMUA DATA STAFF KE ATTANDANCE BERDASARKAN IMSSocID
	function save_staff_to_attadance($post){
		$SQL = "select IMSSocID,StaffID from ktv_ims_socialization_staff WHERE IMSSocID = ? ";
		$q = $this->db->query($SQL, array($post['IMSSocID']) );
		//echo $this->db->last_query();die;

		//Masukkan data participant yang tidak terabsen sebelumnya atau AttendanceStatus = 2
		//sekarang inputkan data barunya
		$data_s_insert = array('CreatedBy' => $_SESSION['userid'], 'DateCreated' => date("Y-m-d H:i:s")); 
		if($post['DayNumber'] != 0 ){
			if($q->result()){
				foreach($q->result() as $Row){
					$datas = array(
						'IMSSocID'=> $Row->IMSSocID,
						'StaffID' => $Row->StaffID,
						'DayNumber'=> $post['DayNumber'],
						'EventDate'=> $post['EventDate'],
						'StatusCode' => 'active',
						'AttendanceStatus' => 2,
						'CreatedBy' => $_SESSION['userid'],
						'DateCreated' => date("Y-m-d H:i:s")
					);

					$options = array('IMSSocID' => $Row->IMSSocID, 'StaffID' => $Row->StaffID, 'DayNumber' => $post['DayNumber']);
					$Q = $this->db->get_where('ktv_ims_socialization_staff_attendance', $options,1);
					//echo $this->db->last_query();die;
					if($Q->num_rows() > 0){
						//Do not Insert again...
					}else{
						$this->db->trans_start();
						$data_bpjkt = array_merge($datas, $data_s_insert);
						$this->db->insert('ktv_ims_socialization_staff_attendance', $data_bpjkt);
						//echo $this->db->last_query();die;
						$this->db->trans_complete();
					}
				}
			}
		}
		return 1;

	}

	public function main_staffattandance_list($start=0, $limit=10, $IMSSocID, $DayNumber)
    {

		$filter = '';
        $limit_filter = '';
        $params = array();
		if (isset($start) && !empty($limit)) {
            $limit_filter .= " LIMIT ?, ?";
            $params[] = intval($start);
            $params[] = intval($limit);
        }
		$IMSSocID = $IMSSocID =='' ? '0' : $IMSSocID;

		$sql = "SELECT * FROM ktv_view_socialization_attandance_staff WHERE StatusCode != 'nullified' AND IMSSocID = $IMSSocID and DayNumber = $DayNumber  " ;
		$sql .= "  --filter-- --limit--";

		$sql = str_replace('--filter--', $filter, $sql);
        $sql = str_replace('--limit--', $limit_filter, $sql);
        $query = $this->db->query($sql, $params);
	    //echo $this->db->last_query();die;

        //////COUNT/////////////////////////////////
		$sqls = "select count(*) as total from ktv_view_socialization_attandance_staff where StatusCode = 'active'  AND IMSSocID = $IMSSocID and DayNumber = $DayNumber " ;
		$sqls .= "  --filter--";
		$sqls = str_replace('--filter--', $filter, $sqls);
		$num_rows = $this->db->query($sqls)->row()->total;
 

		$result['data'] = $query->result_array();
        $result['total'] = $num_rows;
        return $result;
    }

	function save_attandance_staff($post){
		$data = array('LastModifiedBy' => $_SESSION['userid'], 'DateUpdated' => date("Y-m-d H:i:s"),'AttendanceStatus' => $post['checked'] );
		$data_ID = array('IMSSocID' => $post['IMSSocID'], 'StaffID' => $post['StaffID'], 'DayNumber' => $post['DayNumber']);
		$this->db->trans_start();
		$this->db->update('ktv_ims_socialization_staff_attendance',$data, $data_ID);
		//echo $this->db->last_query();die;
		$this->db->trans_complete();
		$result =  1;

		//Update ParticipateInSocializationStatus = 1 jika checked = true
		$data_ID = array('IMSSocID' => $post['IMSSocID'], 'StaffID' => $post['StaffID']);
		$this->db->where($data_ID);
		$q = $this->db->select('MIN(AttendanceStatus) as status')->from('ktv_ims_socialization_staff_attendance')->get()->row();

		return $result;
	}
 
	function getSocializeEvent($District = '')
	{
		//Socialization event yang di download hanya untuk status event Ongoing
		$sql ="SELECT
				A.*,
				(
				SELECT
					Ax.PersonNm 
				FROM
					ktv_persons Ax,
					ktv_staffs Bx 
				WHERE
					Ax.PersonID = Bx.PersonID 
					AND Bx.StatusCode = 'active' 
					AND Bx.StaffID = A.PICStaffID 
					LIMIT 1 
				) AS PicName,
			CASE
					
					WHEN length( B.MobileUID ) = 15 THEN
					B.MobileUID 
					WHEN length( B.MobileUID ) = 9 THEN
					B.MobileUID ELSE B.ApplicantID 
				END AS ApplicantID,
				B.CPGid,
				B.Fullname,
				B.GroupName,
				BB.NrOfFarm,
				BB.HectareOfFarm,
				BB.LastYearHarvest,
				BB.NrOfProductiveTrees,
				B.ParticipantID,
				CONCAT( C.PartnerID, '-', C.BatchName ) AS BatchNameAlias,
				B.mobileUID FarmerID,
				B.MobileUID
			FROM
				ktv_view_socialization A
				JOIN ktv_view_socialization_participant B ON A.IMSSocID = B.IMSSocID 
				AND B.StatusCode = 'active'
				LEFT JOIN ktv_applicant_farmers BB ON BB.ApplicantID = B.ApplicantID
				LEFT JOIN ktv_cpg_batch C ON A.BatchID = C.CpgBatchID 
			WHERE
				A.SocializationStatus = 2 
				AND A.District = ?
				AND A.StatusCode = 'active' 
			ORDER BY
				A.IMSSocID ASC";
				
		$query = $this->db->query($sql, array($District));

		return $query;
	}


	public function checkSocializationData($IMSSocID)
    {
        $query = $this->db->get_where('ktv_ims_socializations', array('IMSSocID' => $IMSSocID, 'StatusCode' => 'active'), 1);
        if ($query->num_rows()>0) {
            return true;
        }
        return false;
    }

	public function checkParticipant($IMSSocID, $ParticipantID)
    {
        //15 : Mobiluid dari non existing farmer
		//9 : Mobileuid dari existing farmer atau dari tabel farmer cocoa_farmer
		// if(strlen($ApplicantID) == 15 || strlen($ApplicantID) == 9){ //By Mobil Version Transfer AppID
		// 	$query = $this->db->get_where('ktv_ims_socialization_participants', array('IMSSocID' => $IMSSocID, 'MobileUID' => $ApplicantID, 'StatusCode' => 'active'), 1);
		// }else{
			// $query = $this->db->get_where('ktv_ims_socialization_participants', array('IMSSocID' => $IMSSocID, 'ParticipantID' => $ApplicantID, 'StatusCode' => 'active'), 1);
			$query = $this->db->get_where('ktv_ims_socialization_participants', array('IMSSocID' => $IMSSocID, 'ParticipantID' => $ParticipantID, 'StatusCode' => 'active'), 1);
		// }
		
		//echo $this->db->last_query();die;
        return $query->num_rows(); 
    }

	public function addParticipant($IMSSocID, $ApplicantID, $RecommendationStatus, $FieldAgentName, $RecommendationDate , $Comments,$LearningContractSign,$LearningContractStatus,$apply_certification, $apply_certificationStatus)
    {
        
		$data = array(
            'IMSSocID'    => $IMSSocID, 
			'RecommendationStatus' => $RecommendationStatus,
			'FieldAgentName' => $FieldAgentName,
			"SelectionStatus" => $RecommendationStatus,
			'SelectionRemarks' => $Comments,
			'RecommendationDate' => $RecommendationDate,
			'Comments' => $Comments,
			'LearningContractSign' => $LearningContractSign,
			'LearningContractStatus' => $LearningContractStatus,
			'apply_certification' => $apply_certification,
			'apply_certificationStatus' => $apply_certificationStatus
        ); 
		
		if(strlen($ApplicantID) == 15 || strlen($ApplicantID) == 9){ //By Mobil Version Transfer AppID
			$data['MobileUID'] = $ApplicantID;
		}else{
			// $data['ParticipantID'] = $ApplicantID;
			$data['ApplicantID'] = $ApplicantID;

		}
		$this->db->trans_start();
			$data_s_insert = array('CreatedBy' => $_SESSION['userid'], 'DateCreated' => date("Y-m-d H:i:s")); 
			$data_bpjkt = array_merge($data, $data_s_insert);
			return $this->db->insert('ktv_ims_socialization_participants', $data); 
		$this->db->trans_complete();
		//echo $this->db->last_query();die;
    }
	
	public function addFielsEventFoto($IMSSocID, $NamesFile='')
    {
        for($i=0; $i<count($NamesFile); $i++){
			$data = array(
				'IMSSocID'    => $IMSSocID,
				'FilesName' => str_replace("[", "", str_replace("]", "", $NamesFile[$i]))
			);
			$this->db->trans_start();	
			
			$this->db->insert('ktv_ims_socialization_files', $data); 
			$this->db->trans_complete();
		} 
		//echo $this->db->last_query();die;
    }

	 public function editParticipant($IMSSocID, $ParticipantID, $RecommendationStatus, $FieldAgentName, $RecommendationDate , $Comments,$LearningContractSign,$LearningContractStatus,$apply_certification, $apply_certificationStatus)
    {
		
		$data = array( 
			'RecommendationStatus' => $RecommendationStatus,
			'FieldAgentName' => $FieldAgentName,
			"SelectionStatus" => $RecommendationStatus,
			'SelectionRemarks' => $Comments,
			'RecommendationDate' => $RecommendationDate,
			'Comments' => $Comments,
			'LearningContractSign' => $LearningContractSign,
			'LearningContractStatus' => $LearningContractStatus,
			'apply_certification' => $apply_certification,
			'apply_certificationStatus' => $apply_certificationStatus,
			'LastModifiedBy' => $_SESSION['userid'],   
			'DateUpdated'  => date("Y-m-d H:i:s")
        );
		
        $condition = array(
            'IMSSocID'      => $IMSSocID 
        );
		
		// if(strlen($ApplicantID) == 15 || strlen($ApplicantID) == 9){ //By Mobil Version Transfer AppID
		// 	$condition['MobileUID'] = $ApplicantID;
		// }else{
			// $condition['ParticipantID'] = $ApplicantID;
			$condition['ParticipantID'] = $ParticipantID;

		// }
		
		$this->db->trans_start();
			return $this->db->update('ktv_ims_socialization_participants', $data, $condition); 
		$this->db->trans_complete();
    }

	public function checkAttendance($IMSSocID, $ParticipantID, $day_number)
    {
		$sql = "SELECT
			a.ApplicantID
			, a.FarmerID
			, a.MobileUID
			, a.ParticipantType
		FROM
			`ktv_ims_socialization_participants` a
		WHERE
			ParticipantID = ?";
		$query = $this->db->query($sql,array($ParticipantID));

		$row = $query->row();

		if($row->ParticipantType == 'Existing Farmer'){ //By Mobil Version Transfer AppID
			$query = $this->db->get_where('ktv_view_socialization_attandance', array('IMSSocID' => $IMSSocID, 'MobileUID' => $row->MobileUID, 'DayNumber' => $day_number), 1); 
		}else{
			$query = $this->db->get_where('ktv_view_socialization_attandance', array('IMSSocID' => $IMSSocID, 'ApplicantID' => $row->ApplicantID, 'DayNumber' => $day_number), 1); 
		}
		
		//echo $this->db->last_query();die;
        return $query->num_rows();
    }

	public function addAttendance($IMSSocID, $ParticipantID, $day_number, $training_date, $AttendanceStatus, $file_attachement, $path)
    {
		$sql = "SELECT
			a.ApplicantID
			, a.FarmerID
			, a.MobileUID
			, a.ParticipantType
		FROM
			`ktv_ims_socialization_participants` a
		WHERE
			ParticipantID = ?";
		$query = $this->db->query($sql,array($ParticipantID));

		$row = $query->row();

        $data = array(
            'IMSSocID' 			    => $IMSSocID, 
            'DayNumber'             => $day_number,
            'EventDate'          	=> $training_date,
			'AttendanceStatus'      => $AttendanceStatus,
            'AttendanceSign'        => $file_attachement == '-' ? '' :  $path.'/'.$file_attachement
        );
		
		$data['MobileUID'] = $row->MobileUID;
		$data['ApplicantID'] = $row->ApplicantID;
		$data['FarmerID'] = $row->FarmerID;
		
		$this->db->trans_start();
        $this->db->insert('ktv_ims_socialization_attendance', $data);
		$this->db->trans_complete();
		
		
		if($row->ParticipantType == 'Existing Farmer'){ //By Mobil Version Transfer AppID
			$data_ID = array('IMSSocID' => $IMSSocID, 'MobileUID' => $row->MobileUID);
		}else{
			$data_ID = array('IMSSocID' => $IMSSocID, 'ApplicantID' => $row->ApplicantID);
		} 
		
		$this->db->where($data_ID);
		$q = $this->db->select('MIN(AttendanceStatus) as status')->from('ktv_ims_socialization_attendance')->get()->row();

		$status = '';
		if($q->status == 2)
		{
			$status = 2; 
		}else{
          	$status = 1;
		}
		$this->db->trans_start();
		if($row->ParticipantType == 'Existing Farmer'){ //By Mobil Version Transfer AppID
			$data_ID = array('IMSSocID' => $IMSSocID, 'MobileUID' => $row->MobileUID);
		}else{
			$data_ID = array('IMSSocID' => $IMSSocID, 'ApplicantID' => $row->ApplicantID);
		} 
		
		$this->db->where($data_ID);
		$this->db->update('ktv_ims_socialization_participants', array('ParticipateInSocializationStatus' => $status, 'LastModifiedBy' => $_SESSION['userid'], 			'DateUpdated'  => date("Y-m-d H:i:s") ));
		$this->db->trans_complete();
		
		// echo $this->db->last_query();die;
    }
 

	public function editAttendance($IMSSocID, $ParticipantID, $day_number, $training_date, $AttendanceStatus, $file_attachement, $path)
    {
		$sql = "SELECT
			a.ApplicantID
			, a.FarmerID
			, a.MobileUID
			, a.ParticipantType
		FROM
			`ktv_ims_socialization_participants` a
		WHERE
			ParticipantID = ?";
		$query = $this->db->query($sql,array($ParticipantID));

		$row = $query->row();   

        $data = array(
            'EventDate'          	=> $training_date, 
			'AttendanceStatus'		=> $AttendanceStatus,
            'AttendanceSign'        => $path.'/'.$file_attachement,
        );

		if($row->ParticipantType == 'Existing Farmer'){ //By Mobil Version Transfer AppID
			$condition = array(
			'IMSSocID'   		    => $IMSSocID,
			'MobileUID'				=> $row->MobileUID,
			'DayNumber'             => $day_number,
			);
		}else{
			$condition = array(
				'IMSSocID'   		    => $IMSSocID,
				'ApplicantID'			=> $row->ApplicantID,
				'DayNumber'             => $day_number,
			);
		}
		
		$this->db->trans_start();
        $this->db->update('ktv_ims_socialization_attendance', $data, $condition);
		$this->db->trans_complete();
		
		if($row->ParticipantType == 'Existing Farmer'){ //By Mobil Version Transfer AppID
			$data_ID = array('IMSSocID' => $IMSSocID, 'MobileUID' => $row->MobileUID);
		}else{
			$data_ID = array('IMSSocID' => $IMSSocID, 'ApplicantID' => $row->ApplicantID);
		} 
		$this->db->where($data_ID);
		$q = $this->db->select('MIN(AttendanceStatus) as status')->from('ktv_ims_socialization_attendance')->get()->row();

		$status = '';
		if($q->status == 2)
		{
			$status = 2;
		}else{
          	$status = 1;
		}
		 
		$this->db->trans_start();
		if($row->ParticipantType == 'Existing Farmer'){ //By Mobil Version Transfer AppID
			$data_ID = array('IMSSocID' => $IMSSocID, 'MobileUID' => $row->MobileUID);
		}else{
			$data_ID = array('IMSSocID' => $IMSSocID, 'ApplicantID' => $row->ApplicantID);
		} 
		$this->db->where($data_ID);
		$this->db->update('ktv_ims_socialization_participants', array('ParticipateInSocializationStatus' => $status,'LastModifiedBy' => $_SESSION['userid'],   
			'DateUpdated'  => date("Y-m-d H:i:s") ));
		$this->db->trans_complete();
		
    }

	public function loadappdataViewonly($IMSSocID){
    	$sql="SELECT
				a.`EventName`
				, topic.`CpgTrainings`
				, CONCAT(bat.`BatchNumber`,' - ',part.`PartnerName`) AS Batch
				, a.`EventStart`
				, a.`EventEnd`
			FROM
				ktv_ims_socializations a
				LEFT JOIN ktv_cpg_trainings topic ON a.`CPGtrainingsID` = topic.`CpgTrainingsID`
				LEFT JOIN ktv_cpg_batch bat ON a.`BatchID` = bat.`CpgBatchID`
				LEFT JOIN ktv_program_partner part ON bat.`PartnerID` = part.`PartnerID`
			WHERE
				a.`IMSSocID` = ?
			LIMIT 1";
    	$query = $this->db->query($sql, array((int) $IMSSocID));

    	$return['success'] = true;
        $return['data'] = $query->row_array();
        return $return;
    }

    public function loadappdataRecommendationViewonly($IMSSocID,$ApplicantID,$ParticipantID){
    	$sql="SELECT
				CASE
					WHEN a.RecommendationStatus = '1' THEN 'Yes'
					WHEN a.RecommendationStatus = '2' THEN 'No'
					ELSE '-'
				END AS RecommendationStatus
				, a.RecommendationDate AS DateOfRecommendation
				, a.Comments AS RecommendationComment
				, a.FieldAgentName
			FROM
				ktv_ims_socialization_participants a
			WHERE
				a.`ParticipantID` = ?
			LIMIT 1";
    	$query = $this->db->query($sql, array((int) $ParticipantID));
  
    	$return['success'] = true;
        $return['data'] = $query->row_array();
        return $return;
    }
	
	
	function appformparticipantDelete($id){ 
        $sql="UPDATE ktv_ims_socialization_participants SET StatusCode = 'nullified',LastModifiedBy='".$_SESSION['userid']."',DateUpdated=NOW() WHERE ParticipantID=? LIMIT 1";
        $query = $this->db->query($sql, array($id));
		//echo $this->db->last_query();die;
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }
	
	function delSelectedparticipantDelete($id,$IMSSocID, $existingfarmer){ 
		 
		if($existingfarmer !='yes'){
			 $sql="UPDATE ktv_ims_socialization_participants SET StatusCode = 'nullified',LastModifiedBy='".$_SESSION['userid']."',DateUpdated=NOW() WHERE ApplicantID=? and IMSSocID = ? LIMIT 1";
		}else{
			$sql="UPDATE ktv_ims_socialization_participants SET StatusCode = 'nullified',LastModifiedBy='".$_SESSION['userid']."',DateUpdated=NOW() WHERE MobileUID=? and IMSSocID = ? LIMIT 1";
		}
       
        $query = $this->db->query($sql, array($id, $IMSSocID));
		
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }
	
	
	
	function ExportExcelData(){
        $sql = "SELECT * FROM ktv_view_socialization WHERE StatusCode != 'nullified'";
		$query = $this->db->query($sql);
        $result = $query->result_array(); 
        return $result;
    }
	
	function ExportHeaderrow($IMSSocID)
	{
		$SQL = "select EventName , DateUpdated from ktv_view_socialization 			
				where IMSSocID = ? ";
		$Q = $this->db->query($SQL, array($IMSSocID))->row(); 
		return $Q;
	}
	
	function ExportExcellogevent($IMSSocID)
	{
		$SQL = "SELECT * FROM ktv_view_socialization_participant WHERE StatusCode != 'nullified' AND IMSSocID = ? " ; 
		$Q = $this->db->query($SQL, array($IMSSocID))->result_array(); 
		return $Q;
	}
	
	function ExportExcelstaff($IMSSocID)
	{
		$SQL = "SELECT * FROM ktv_view_socialization_staff WHERE StatusCode != 'nullified' AND IMSSocID = ? " ; 
		$Q = $this->db->query($SQL, array($IMSSocID))->result_array(); 
		return $Q;
	}
	
	function checkbelumsyncalert($IMSSocID)
	{
		
		$SQL = "select  count(IMSSocID) as jml from 
				ktv_ims_socialization_participants  
				where
				MobileUID NOT IN (select MobileUID from ktv_applicant_farmers where StatusCode != 'nullified') and
				length(MobileUID) = 15 and StatusCode != 'nullified' AND IMSSocID = ?" ; 
		$Q = $this->db->query($SQL, array($IMSSocID))->row_array();
		if($Q){
			return $Q['jml'];
		}else{
			return 0;
		}
		 
	}
	
	
}

/* End of file mregion.php */
/* Location: ./application/models/mregion.php */