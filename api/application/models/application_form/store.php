<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Store extends CI_Model {
     public $user;
	private $sql_count = "SELECT FOUND_ROWS() AS total";

	public function __construct()
	{
		parent::__construct();
		$this->user = $this->muserprofile->getUserProfile();
	}

	public function getApplicantMemberInputGrid($textSearch,$start,$limit,$sortingField, $sortingDir,$Enumerator = null){
        $filter = '';
        $limit_filter = '';
        $params = array();

		if ($sortingField == "")
            $sortingField = 'ApplicantID';
        if ($sortingDir == "")
            $sortingDir = 'DESC';
		
		if ( !empty($textSearch)) {
			$filter .= " AND Fullname LIKE '%{$textSearch}%' "; 
        }
		if ( !empty($Enumerator)) {
			$filter .= " AND f.CreatedBy = ".$Enumerator."  "; 
        }
		
		if (!empty($this->user['accessStaff'])) {
            $filter .= " AND d.DistrictID IN ({$this->user['accessStaff']})";
        }
		
		if (isset($start) && !empty($limit)) {
            $limit_filter .= " LIMIT ?, ?";
            $params[] = intval($start);
            $params[] = intval($limit);
        }
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS 
				f.ApplicantID,
				f.Fullname,
				f.ProvinceID,
				f.DistrictID,
				f.SubDistrictID,
				f.SubDistrictName,
				f.VillageID,
				v.Village,
				sd.SubDistrict, 
				d.District, 
				p.Province,
                s.UserRealName Enumerator
			FROM 
				ktv_applicant_farmers f
			LEFT JOIN 
				ktv_subdistrict sd ON sd.SubDistrictID=f.SubDistrictID
			LEFT JOIN 
				ktv_district d ON d.DistrictID=f.DistrictID
			Left JOIN 
				ktv_province p ON p.ProvinceID=f.ProvinceID
			LEFT JOIN
				ktv_village v on v.VillageID = f.VillageID
			LEFT JOIN
				ktv_members m on m.ApplicantID = f.ApplicantID
			LEFT JOIN 
				sys_user s ON s.UserId = f.CreatedBy
			WHERE f.StatusCode = 'active' AND m.MemberID is null" ; 
		$sql .= "  --filter-- ORDER BY " . $sortingField . " " . $sortingDir . " --limit--";
		 
		$sql = str_replace('--filter--', $filter, $sql);
        $sql = str_replace('--limit--', $limit_filter, $sql);
		$query = $this->db->query($sql, $params);
		 
		//////COUNT/////////////////////////////////
		$sqls = "SELECT FOUND_ROWS() AS total" ; 
		$num_rows = $this->db->query($sqls)->row()->total;

     		
		$result['data'] = $query->result_array();
        $result['total'] = $num_rows; 
        return $result;
    }

	public function inputApplicantMember($arrApplicantID){
        $this->db->trans_begin();
		if(is_array($arrApplicantID)){
			foreach($arrApplicantID as $num => $ApplicantID){
				$sql = "SELECT
					kaf.*
				FROM
					ktv_applicant_farmers kaf
				WHERE
					kaf.ApplicantID = ?
				LIMIT 1
				";
		
				$query = $this->db->query($sql,array($ApplicantID));

				if($query->num_rows()>0){
					$row = $query->row_array();

					$id = $this->genMemberID($row["DistrictID"], "F");

					$PartnerID = $this->getPartnerMemberByDistrict($row["DistrictID"]);
					$PartnerSurvey = $this->getPartnerSurveyByPartnerID($PartnerID);
					
					$data["MemberID"] 			= $id["MemberID"];
					$data["MemberDisplayID"] 	= $id["MemberDisplayID"];
					$data["MemberUid"] 			= $id["MemberUid"];
					$data["uid"]				= $id["MemberUid"]; //req tim BE, supaya bisa sync download
					$data["MemberName"] 		= $row["Fullname"];
					$data["DateCollection"] 	= date("Y-m-d");
					$data["DateOfBirth"] 		= ($row["DateOfBirth"] == '')?'1990-01-01':$row["DateOfBirth"];
					$data["Gender"] 			= ($row["Gender"] == '')?'m':$row["Gender"];
					$data["MaritalStatus"] 		= $row["MaritalStatus"];
					$data["Education"] 			= $row["Education"];
					$data["VillageID"] 			= $row["VillageID"];
					$data["Address"] 			= $row["Address"];
					$data["HandphoneType"] 		= $row["HandphoneType"];
					$data["Handphone"] 			= $row["PhoneNumber"];
					$data["Nin"] 				= $row["NIN"];
					$data["FarmerGroupID"] 		= $row["CPGid"];
					$data["SupplybaseType"] 	= 'farmer';
					$data["StatusCode"] 		= 'active';
					$data["DateCreated"] 		= date("Y-m-d H:i:s");
					$data["CreatedBy"] 			= $_SESSION["userid"];
					$data["PartnerID"] 			= $PartnerID;
					$data["ApplicantID"]		= $row["ApplicantID"];

					$query = $this->db->insert("ktv_members",$data);

					//insert member role (begin)
					$arrRole = array();
					//if($varPost['Koltiva_view_Grower_FormMainGrower-CbRolePlanter'] == "1") $arrRole[] = 1;
					$arrRole[] = 1;
			
					foreach ($arrRole as $key => $value) {
						$sql = "INSERT INTO `ktv_member_role` SET
							`MemberID` = ?,
							`MRoleID` = ?,
							`DateCreated` = NOW(),
							`CreatedBy` = ?";
						$p = array(
							$id['MemberID'],
							$value,
							$_SESSION['userid']
						);
						$query = $this->db->query($sql, $p);
					}
					//insert member role (end)

					//insert hak akses data control (Begin)
					if ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program") {
						$sql = "INSERT INTO `ktv_access_partner_member` SET
								`apmPartnerID` = ?,
								`apmMemberID` = ?,
								`DateCreated` = NOW(),
								`CreatedBy` = ?";
						$p = array(
							$_SESSION['PartnerID'],
							$id['MemberID'],
							$_SESSION['userid']
						);
						$query = $this->db->query($sql, $p);
			
						//cek kalau bukan Partner Koltiva, maka ditambahkan juga ke Partner Koltiva
						if ($_SESSION['PartnerID'] != "1") {
							//insertkan ke Koltiva
							$sql = "INSERT INTO `ktv_access_partner_member` SET
									`apmPartnerID` = ?,
									`apmMemberID` = ?,
									`DateCreated` = NOW(),
									`CreatedBy` = ?";
							$p = array(
								'1',
								$id['MemberID'],
								$_SESSION['userid']
							);
							$query = $this->db->query($sql, $p);
						}
					} else {
						//insertkan ke Koltiva
						$sql = "INSERT INTO `ktv_access_partner_member` SET
								`apmPartnerID` = ?,
								`apmMemberID` = ?,
								`DateCreated` = NOW(),
								`CreatedBy` = ?";
						$p = array(
							'1',
							$id['MemberID'],
							$_SESSION['userid']
						);
						$query = $this->db->query($sql, $p);
					}
					//insert hak akses data control (End)
				}
			}
		}

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();

			return false;
		} else {
			$this->db->trans_commit();
			return true;
		}
	}

	public function getPartnerSurveyByPartnerID($PartnerID){
        $sql="SELECT
                GROUP_CONCAT(a.`SurveyName` SEPARATOR ',') AS PartnerSurvey
            FROM
                ktv_program_partner_survey a
            WHERE
                a.`PartnerID` = ?";
        $query = $this->db->query($sql,array($PartnerID));
        $data = $query->row_array();
        return $data['PartnerSurvey'];
    }
	
	public function getPartnerMemberByDistrict($DistrictID){

        $sql="SELECT
                a.`PartnerID`
            FROM
                ktv_district_partner_member a
            WHERE
                a.`DistrictID` = ?
            LIMIT 1";
        $query = $this->db->query($sql,array($DistrictID));
        $data = $query->row_array();
        if(isset($data['PartnerID'])){
            return $data['PartnerID'];
        }else{
            return false;
        }
    }
	
	public function genMemberID($DistrictID, $prefixId = 'F') {
        //MemberID
        $sql = "SELECT
                a.MemberID
            FROM
                ktv_members a
            ORDER BY a.`MemberID` DESC
            LIMIT 1";
        $query = $this->db->query($sql);
        $data = $query->row_array();
        if ($data['MemberID'] != "") {
            $return['MemberID'] = $data['MemberID'] + 1;
        } else {
            $return['MemberID'] = 1;
        }

        //MemberDisplayID
        $MemberID = $return['MemberID'];
        $IncMemberID = "";
        $awalan = $prefixId . $DistrictID;

        //Gen Increment
        switch (strlen($MemberID)) {
            case '1':
                $IncMemberID = "00000000" . $MemberID;
                break;
            case '2':
                $IncMemberID = "0000000" . $MemberID;
                break;
            case '3':
                $IncMemberID = "000000" . $MemberID;
                break;
            case '4':
                $IncMemberID = "00000" . $MemberID;
                break;
            case '5':
                $IncMemberID = "0000" . $MemberID;
                break;
            case '6':
                $IncMemberID = "000" . $MemberID;
                break;
            case '7':
                $IncMemberID = "00" . $MemberID;
                break;
            case '8':
                $IncMemberID = "0" . $MemberID;
                break;
            default:
                $IncMemberID = $MemberID;
                break;
        }
        $return['MemberDisplayID'] = $awalan.$IncMemberID;

        //MemberUID
        $return['MemberUid'] = 'W-'.$IncMemberID;

        return $return;
	}

	public function updatefilephotoapplicant($ApplicantID, $filepath){
		$data['ApplicantPhoto'] = $filepath;

		$this->db->where("ApplicantID",$ApplicantID);
		$this->db->update("ktv_applicant_farmers",$data);

		return true;
	}

	public function updatefilephoto($ApplicantID, $filepath){
		$data['Photo'] = $filepath;

		$this->db->where("ApplicantID",$ApplicantID);
		$this->db->update("ktv_applicant_farmers",$data);

		return true;
	}

	public function updatefilecontractphoto($ApplicantID, $filepath){
		$data['CertificationContractSign'] = $filepath;

		$this->db->where("ApplicantID",$ApplicantID);
		$this->db->update("ktv_applicant_farmers",$data);

		return true;
	}

    public function getMainListAppForm($sort, $start=0, $limit=10,$key='', $ProvinceID='',$DistrictID='',$SubDistrictID='')
    { 
		
		$filter = '';
        $limit_filter = '';
        $params = array();
		$sort = json_decode($sort); 
		$by = ($sort[0]->direction == '' ? 'DESC' : $sort[0]->direction);
		$order = ($sort[0]->property == '' ? 'ApplicantID' : $sort[0]->property);
		
		if ( !empty($key)) {
			$filter .= " AND Fullname LIKE '%{$key}%' "; 
        }
		if ( !empty($ProvinceID)) {
			$filter .= " AND p.ProvinceID = ".$ProvinceID."  "; 
        }
		if ( !empty($DistrictID)) {
			$filter .= " AND d.DistrictID = ".$DistrictID."  "; 
        }
		if ( !empty($SubDistrictID)) {
			$filter .= " AND sd.SubDistrictID = ".$SubDistrictID."  "; 
        }
		
		if (!empty($this->user['accessStaff'])) {
            $filter .= " AND d.DistrictID IN ({$this->user['accessStaff']})";
        }
		
		if (isset($start) && !empty($limit)) {
            $limit_filter .= " LIMIT ?, ?";
            $params[] = intval($start);
            $params[] = intval($limit);
        }
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS 
				f.ApplicantID,
				f.MobileUID,
				f.DisplayID,
				f.Fullname,
				f.NIN,
				f.DateCollection,
				f.DateRecommendation,
				f.DateOfBirth,
				f.Age,
				f.Gender,
				f.MaritalStatus,
				f.Education,
				f.ProvinceID,
				f.DistrictID,
				f.SubDistrictID,
				f.SubDistrictName,
				f.VillageID,
				f.VillageName,
				f.Address,
				f.HandphoneType,
				f.PhoneNumber,
				f.Email,
				f.RecommendationStatus,
				f.Comment,
				f.GroupMemberStatus,
				f.CPGid,
				f.NewGroupName,
				f.CertHolderID,
				f.CertProgID,
				f.IMSMasterID,
				f.IMSID,
				f.Photo,
				f.PhotoDesc,
				f.CertificationContractStatus,
				f.CertificationContractSign,
				f.FarmertypeID,
				f.PatnerID,
				f.NrOfFarm,
				f.HectareOfFarm,
				f.LastYearHarvest,
				f.NrOfProductiveTrees,
				IFNULL(f.Latitude, ST_Y(f.LatLong)) Latitude,
				IFNULL(f.Longitude, ST_X(f.LatLong)) Longitude,
				IF(m.MemberID is null, 'No', 'Yes') MemberStatus,
				f.ActiveStatus,
				f.InactiveReason,
				f.InactiveRemarks,
				f.SocStatus,
				f.SocUserApprove,
				f.SocApprovalDate,
				f.SocApprovalRemark,
				f.SelStatus,
				f.SelUserApprove,
				f.SelApprovalDate,
				f.SelApprovalRemark,
				f.TrainStatus,
				f.TrainUserApprove,
				f.TrainApprovalDate,
				f.TrainApprovalRemark,
				f.HubID,
				f.SupplychainID,
				f.StatusCode,
				f.DateCreated,
				f.CreatedBy,
				f.DateUpdated,
				f.LastModifiedBy,
				f.DateSync,
				f.uid,
				f.PartnerID,
					CASE
						WHEN f.Gender='m' THEN '" . lang('Male') . "'
						WHEN f.Gender='f' THEN '" . lang('Female') . "'
						ELSE '-'
					END AS GenderDesc,
					(
						SELECT CONCAT('[',cp.FarmerGroupID,']',cp.GroupName)
						FROM ktv_farmer_group cp
						WHERE cp.FarmerGroupID = f.CPGid
						LIMIT 1
					) AS GroupName,
					sd.SubDistrict, d.District, p.Province
				FROM ktv_applicant_farmers f
				LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=f.SubDistrictID
				LEFT JOIN ktv_district d ON d.DistrictID=f.DistrictID
				Left JOIN ktv_province p ON p.ProvinceID=f.ProvinceID
				LEFT JOIN ktv_members m on m.ApplicantID = f.ApplicantID
				WHERE f.StatusCode = 'active'" ; 
		$sql .= "  --filter-- ORDER BY " . $order . " " . $by . " --limit--";
		 
		$sql = str_replace('--filter--', $filter, $sql);
        $sql = str_replace('--limit--', $limit_filter, $sql);
		$query = $this->db->query($sql, $params);
		 
		//////COUNT/////////////////////////////////
		$sqls = "SELECT FOUND_ROWS() AS total" ; 
		$num_rows = $this->db->query($sqls)->row()->total;

     		
		$result['data'] = $query->result_array();
        $result['total'] = $num_rows; 
        return $result;
    }
	
	function insertApp($post){
		 
		$data_s_insert = array('CreatedBy' => $_SESSION['userid'], 'DateCreated' => date("Y-m-d H:i:s"));
		$data_s_update = array('LastModifiedBy' => $_SESSION['userid'], 'DateUpdated' => date("Y-m-d H:i:s"));
		
		
		$data = array( 
			'DisplayID'=> $post['Koltiva_view_application_form_WinRegisterAppForm-Form-DisplayID'],
			'Fullname' => $post['Koltiva_view_application_form_WinRegisterAppForm-Form-Fullname'],
			'NIN' => $post['Koltiva_view_application_form_WinRegisterAppForm-Form-NIN'],
			'DateCollection' => $post['Koltiva_view_application_form_WinRegisterAppForm-Form-DateCollection'],
			'ProvinceID' => $post['Koltiva_view_application_form_WinRegisterAppForm-Form-ProvinceID'],
			'DistrictID' => $post['Koltiva_view_application_form_WinRegisterAppForm-Form-DistrictID'],
			'SubDistrictID' => $post['Koltiva_view_application_form_WinRegisterAppForm-Form-SubDistrictID'],
			'VillageID' => $post['Koltiva_view_application_form_WinRegisterAppForm-Form-VillageID'],
			'VillageName' => $post['Koltiva_view_application_form_WinRegisterAppForm-Form-VillageName'],
			'Address' => $post['Koltiva_view_application_form_WinRegisterAppForm-Form-Address'],
			'CPGid' => $post['Koltiva_view_application_form_WinRegisterAppForm-Form-CPGid'],
			'NewGroupName'  => $post['Koltiva_view_application_form_WinRegisterAppForm-Form-GroupName'], 
			'DateOfBirth'  => $post['Koltiva_view_application_form_WinRegisterAppForm-Form-DateOfBirth'],
			'Age' => $post['Koltiva_view_application_form_WinRegisterAppForm-Form-Age'],
			'Gender' => $post['Koltiva_view_application_form_WinRegisterAppForm-Form-Gender'], 
			'CertHolderID' => $post['Koltiva_view_application_form_WinRegisterAppForm-Form-CertHolderID'],
			'CertProgID' => $post['Koltiva_view_application_form_WinRegisterAppForm-Form-CertProgID'],
 			'IMSID' => $post['Koltiva_view_application_form_WinRegisterAppForm-Form-IMSID'],
 			'IMSMasterID' => $post['Koltiva_view_application_form_WinRegisterAppForm-Form-IMSMasterID'], 
			'FarmertypeID' => $post['Koltiva_view_application_form_WinRegisterAppForm-Form-FarmertypeID'], 
			'PartnerID' => $post['Koltiva_view_application_form_WinRegisterAppForm-Form-PatnerID'], 					
			'NrOfFarm' => $post['Koltiva_view_application_form_WinRegisterAppForm-Form-NrOfFarm'],
			'HectareOfFarm' => $post['Koltiva_view_application_form_WinRegisterAppForm-Form-HectareOfFarm'],
			'LastYearHarvest' => $post['Koltiva_view_application_form_WinRegisterAppForm-Form-LastYearHarvest'],
			'NrOfProductiveTrees' => $post['Koltiva_view_application_form_WinRegisterAppForm-Form-NrOfProductiveTrees'],
			'Latitude' => $post['Koltiva_view_application_form_WinRegisterAppForm-Form-Latitude'],
			'Longitude' => $post['Koltiva_view_application_form_WinRegisterAppForm-Form-Longitude'],
			'ActiveStatus' => $post['Koltiva_view_application_form_WinRegisterAppForm-Form-ActiveStatus'],
			'InactiveReason' => $post['Koltiva_view_application_form_WinRegisterAppForm-Form-InactiveReason'],
			'InactiveRemarks' => $post['Koltiva_view_application_form_WinRegisterAppForm-Form-InactiveRemarks'],
			'StatusCode' => 'active'
		);
   
		$options = array('ApplicantID' => $this->input->post('Koltiva_view_application_form_WinRegisterAppForm-Form-ApplicantID'));
		$Q = $this->db->get_where('ktv_applicant_farmers', $options,1);
		//echo $this->db->last_query();die;
		if($Q->num_rows() > 0){
			$data_ID = array('ApplicantID' => $this->input->post('Koltiva_view_application_form_WinRegisterAppForm-Form-ApplicantID'));
			$this->db->trans_start();
			$this->db->update('ktv_applicant_farmers',$data, $data_ID);	 
			$this->db->update('ktv_applicant_farmers',$data_s_update, $data_ID);
			
			$this->db->trans_complete();
			return $data_ID;
		}else{
			$this->db->trans_start();
			$data_bpjkt = array_merge($data, $data_s_insert);
			$this->db->insert('ktv_applicant_farmers', $data_bpjkt);
			$id = $this->db->insert_id();

			if ($post['Koltiva_view_application_form_WinRegisterAppForm-Form-PhotoOld'] != "") {
                $Photo = "";
                $file = explode("/images/upload/",$post['Koltiva_view_application_form_WinRegisterAppForm-Form-PhotoOld']);
                //Insert ada photonya pakai aws
                if(file_exists('images/upload/'.$file[1])) {
                    $this->load->library('awsfileupload');
                    $upload = $this->awsfileupload->upload('images/upload/'.$file[1],$file[1],AWSS3_APPLICANT_SIGNATURE_PATH, 'images');
                    if ($upload['success'] == true) {
                        delete_file($post['Koltiva_view_application_form_WinRegisterAppForm-Form-PhotoOld']);
                        $Photo = $upload['filenamepath'];
                    }
                }

				$this->updatefilephoto($id,$Photo);
            }

			if ($post['Koltiva_view_application_form_WinRegisterAppForm-Form-CertificationContractSignOld'] != "") {
                $Contract = "";
                $file = explode("/images/upload/",$post['Koltiva_view_application_form_WinRegisterAppForm-Form-CertificationContractSignOld']);
                //Insert ada photonya pakai aws
                if(file_exists('images/upload/'.$file[1])) {
                    $this->load->library('awsfileupload');
                    $upload = $this->awsfileupload->upload('images/upload/'.$file[1],$file[1],AWSS3_APPLICANT_CONTRACT_PATH, 'images');
                    if ($upload['success'] == true) {
                        delete_file($post['Koltiva_view_application_form_WinRegisterAppForm-Form-CertificationContractSignOld']);
                        $Contract = $upload['filenamepath'];
                    }
                }

				$this->updatefilecontractphoto($id,$Contract);
            }

			if ($post['Koltiva_view_application_form_WinRegisterAppForm-Form-ApplicantPhotoOld'] != "") {
                $Contract = "";
                $file = explode("/images/upload/",$post['Koltiva_view_application_form_WinRegisterAppForm-Form-ApplicantPhotoOld']);
                //Insert ada photonya pakai aws
                if(file_exists('images/upload/'.$file[1])) {
                    $this->load->library('awsfileupload');
                    $upload = $this->awsfileupload->upload('images/upload/'.$file[1],$file[1],AWSS3_APPLICANT_PHOTO_PATH, 'images');
                    if ($upload['success'] == true) {
                        delete_file($post['Koltiva_view_application_form_WinRegisterAppForm-Form-ApplicantPhotoOld']);
                        $Contract = $upload['filenamepath'];
                    }
                }

				$this->updatefilecontractphoto($id,$Contract);
            }

			$this->db->trans_complete();
			
			return $id;
		}
		$Q->free_result();
		$this->db->close();
	}
	
	function deleteAppform($id){ 
        $sql="UPDATE ktv_applicant_farmers SET StatusCode = 'nullified',LastModifiedBy='".$_SESSION['userid']."',DateUpdated=NOW() WHERE ApplicantID=? LIMIT 1";
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }
	
	function loadappdata($id){
		$this->load->library('awsfileupload');
        $sql = "SELECT 
			f.ApplicantID,
			f.MobileUID,
			f.DisplayID,
			f.Fullname,
			f.NIN,
			f.DateCollection,
			f.DateRecommendation,
			f.DateOfBirth,
			f.Age,
			f.Gender,
			f.MaritalStatus,
			f.Education,
			f.ProvinceID,
			f.DistrictID,
			f.SubDistrictID,
			f.SubDistrictName,
			f.VillageID,
			f.VillageName,
			f.Address,
			f.HandphoneType,
			f.PhoneNumber,
			f.Email,
			f.RecommendationStatus,
			f.Comment,
			f.GroupMemberStatus,
			f.CPGid,
			f.NewGroupName,
			f.CertHolderID,
			f.CertProgID,
			f.IMSMasterID,
			f.IMSID,
			f.Photo,
			f.PhotoDesc,
			f.ApplicantPhoto,
			f.CertificationContractStatus,
			f.CertificationContractSign,
			f.FarmertypeID,
			f.PatnerID,
			f.NrOfFarm,
			f.HectareOfFarm,
			f.LastYearHarvest,
			f.NrOfProductiveTrees,
			IFNULL(f.Latitude, ST_Y(f.LatLong)) Latitude,
			IFNULL(f.Longitude, ST_X(f.LatLong)) Longitude,
			f.ActiveStatus,
			f.InactiveReason,
			f.InactiveRemarks,
			f.SocStatus,
			f.SocUserApprove,
			f.SocApprovalDate,
			f.SocApprovalRemark,
			f.SelStatus,
			f.SelUserApprove,
			f.SelApprovalDate,
			f.SelApprovalRemark,
			f.TrainStatus,
			f.TrainUserApprove,
			f.TrainApprovalDate,
			f.TrainApprovalRemark,
			f.HubID,
			f.SupplychainID,
			f.StatusCode,
			f.DateCreated,
			f.CreatedBy,
			f.DateUpdated,
			f.LastModifiedBy,
			f.DateSync,
			f.uid,
			f.PartnerID 
		FROM 
			ktv_applicant_farmers f 
		WHERE 
			f.ApplicantID =? ";

		$query = $this->db->query($sql, array($id));
        $result = $query->row_array();

		// foreach($result as $key => $value){
		if($this->awsfileupload->doesObjectExist($result['Photo']) == true) {
			$result['PhotoSrcPath'] = $result['Photo'];
			$result['PhotoSrc'] = $this->config->item('CTCDN')."/".$result['Photo'];
		}else{
			$result['PhotoSrcPath'] = $result['Photo'];
			$result['PhotoSrc'] = $result['Photo'];
		}

		if($this->awsfileupload->doesObjectExist($result['CertificationContractSign']) == true) {
			$result['CertificationContractSignSrcPath'] = $result['CertificationContractSign'];
			$result['CertificationContractSignSrc'] = $this->config->item('CTCDN')."/".$result['CertificationContractSign'];
		}else{
			$result['CertificationContractSignSrcPath'] = $result['CertificationContractSign'];
			$result['CertificationContractSignSrc'] = $result['CertificationContractSign'];
		}

		if($this->awsfileupload->doesObjectExist($result['ApplicantPhoto']) == true) {
			$result['ApplicantPhotoSrcPath'] = $result['ApplicantPhoto'];
			$result['ApplicantPhotoSrc'] = $this->config->item('CTCDN')."/".$result['ApplicantPhoto'];
		}else{
			$result['ApplicantPhotoSrcPath'] = $result['ApplicantPhoto'];
			$result['ApplicantPhotoSrc'] = $result['ApplicantPhoto'];
		}
		// }

        $return['success'] = true;
        $return['data'] = $result;
        return $return;
    }
	
	public function getMainListParticipantForm($start=0, $limit=10, $ApplicantID)
    {		
		$filter = '';
        $limit_filter = '';
        $params = array();
		 
		
		 
		if (isset($start) && !empty($limit)) {
            $limit_filter .= " LIMIT ?, ?";
            $params[] = intval($start);
            $params[] = intval($limit);
        }
		$ApplicantID = $ApplicantID =='' ? '0' : $ApplicantID;
		
		$sql = "SELECT * FROM ktv_view_socialization_participant WHERE StatusCode != 'nullified' AND ApplicantID = $ApplicantID " ; 
		$sql .= "  --filter-- --limit--";
		 
		$sql = str_replace('--filter--', $filter, $sql);
        $sql = str_replace('--limit--', $limit_filter, $sql);
        $query = $this->db->query($sql, $params);
		
		//////COUNT/////////////////////////////////
		$sqls = "select count(*) as total FROM ktv_view_socialization_participant WHERE StatusCode != 'nullified' AND ApplicantID = $ApplicantID " ;
		$sqls .= "  --filter--";  
		$sqls = str_replace('--filter--', $filter, $sqls); 
		$num_rows = $this->db->query($sqls)->row()->total;

     		
		$result['data'] = $query->result_array();
        $result['total'] = $num_rows; 
        return $result;
    }
	 
   
    function ExportExcelData(){
        $sql = "SELECT A.*, (SELECT  CONCAT('[',ax.CPGid,']',ax.GroupName) FROM ktv_cpg ax where ax.CPGid = A.CPGid limit 1) as GroupName
				FROM ktv_applicant_farmers A WHERE A.StatusCode != 'nullified' ";
		$query = $this->db->query($sql);
        $result = $query->result_array(); 
        return $result;
    }
	
	function ExportExcelDataTdkLolos( $CertProgID = 0 , $CertHolderID = 0 , $IMSID = 0)
	{
	 
		$sql = "SELECT A.ApplicantID, A.Fullname, A.CPGid, A.VillageID,A.Gender, A.MaritalStatus, A.DateOfBirth,A.DateCollection,A.DateUpdated, A.Education,A.PhoneNumber,
				A.CertProgID, A.CertHolderID, A.IMSID, A.IMSMasterID
				FROM ktv_view_applicant_farmers A
				LEFT JOIN ktv_ims_socialization_participants B ON A.ApplicantID = B.ApplicantID
				JOIN ktv_ims_socializations C ON B.IMSSocID = C.IMSSocID
				WHERE  
			    A.CertProgID = ? and A.CertHolderID = ? and A.IMSID = ? AND B.StatusCode != 'nullified' 
				and B.SelectionStatus = 2 
				ORDER BY A.ApplicantID ASC ";
		 	
		$query = $this->db->query($sql, array($CertProgID, $CertHolderID, $IMSID)); 
        $result = $query->result_array();   
        return $result;
	}
	
	function ExportHeaderrow($ApplicantID)
	{
		$SQL = "select A.*, B.CertEventName from ktv_applicant_farmers A, ktv_ims B			
				where A.IMSID = B.IMSID and ApplicantID =? ";
		$Q = $this->db->query($SQL, array($ApplicantID))->row(); 
		return $Q;
	}
	
	function ExportExcellogevent($ApplicantID)
	{
		$SQL = "SELECT * FROM ktv_view_socialization_participant WHERE StatusCode != 'nullified' AND ApplicantID = ? " ; 
		$Q = $this->db->query($SQL, array($ApplicantID))->result_array(); 
		return $Q;
	}
	
 
	
	
}

/* End of file mregion.php */
/* Location: ./application/models/mregion.php */