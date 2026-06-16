<?php
/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Tue Jan 15 2019
 *  File : mext_staff.php
 *******************************************/

class Mext_staff extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function GetGridMainExtStaff($pSearch, $start, $limit, $sortingField, $sortingDir, $opsiCall){
        //Sorting
    	if ($sortingField == "") $sortingField = 'PersonNm';
        if ($sortingDir == "") $sortingDir = 'ASC';

        //Filter ================== (Begin)
        $sqlWhereCountry = ""; $sqlWhereProv = ""; $sqlWhereDistrict = ""; $sqlHavingFilter = ""; $sqlWhereAccessStaff ="";
        $sqlJoinSimple = ""; $sqlWhereSimple = ""; $sqlJoinAdv = ""; $sqlWhereAdv = ""; $sqlFieldAdv = "";

        if($pSearch['country'] != ""){
        	$sqlWhereCountry = " AND ct.CountryID = '{$pSearch['country']}' ";
        }
        if($pSearch['prov'] != ""){
        	$sqlWhereProv = " AND prov.ProvinceID = '{$pSearch['prov']}' ";
        }

        //Hak Akses Province
        if(isset($_SESSION['GroupAccess'])){
        	if($_SESSION['GroupAccess'] != ""){
	        	$sqlWhereAccessStaff .= " AND prov.ProvinceID IN ({$_SESSION['GroupAccess']}) ";
	        }
        }

        // sementara opsi call di nonaktifkan karna belum dipakai
        // switch ($pSearch['opsiCall']) {
        // 	case 'simple':
        		if($pSearch['textSearch'] != ""){
        			$sqlWhereSimple .= "
        				AND (
        					p.`PersonNm` LIKE '%{$pSearch['textSearch']}%'
        				) ";
        		}
        // 	break;
        // }

        // switch($pSearch['callFrom']){
        //     case 'agro':
        //         $sqlCallFrom = " AND p.`PosID` = '1' ";
        //     break;
        //     case 'tech':
        //         $sqlCallFrom = " AND p.`PosID` = '2' ";
        //     break;
        //     default:
        //         $sqlCallFrom = "";
        //     break;
        // }
        //Filter ===== Agronomis selain admin
        /*$is_admin = $_SESSION['is_admin'];
        if(!$is_admin && (int)$_SESSION['groupid'] != 1){
            $getRole = $this->db->get_where('sys_group', ['GroupId'=>$_SESSION['groupid'] ]);
            if($getRole->num_rows()){
                $dataRole = $getRole->row();
                if((int)$dataRole->RoleId == 6){

                    // cek user affiliasi
                    $cekAffiliateExist = $this->db->get_where('sys_user_affiliate', array('UserId' => $_SESSION['userid']));

                    if($cekAffiliateExist->num_rows()){
                        $sqlWhereUser .= " AND p.CreatedBy IN ('".$_SESSION['userid']."', '".$cekAffiliateExist->row()->UserIdAff."') ";
                    }else{
                        $sqlWhereUser = " AND p.CreatedBy = ".$_SESSION['userid'];
                    }

                }
            }
            
        }
        var_dump($sqlWhereUser, $_SESSION); die;*/
        //Filter ================== (End)

        $sql = "SELECT
                SQL_CALC_FOUND_ROWS
                p.UserID,
                p.`PersonID`
                , s.`StaffID`
                , p.`PersonNm`
                , pos.`PositionName` AS StaffPositionLabel
                , p.`Gender`
                , pv.Province
                , ds.District
                , subp.PersonNm AS ReferenceStaff
                , sro.RoleName AS UserRole
                , su.UserName
                , IF(su.`UserName` IS NOT NULL,'1','2') AS UserCreated
                , CONCAT((SELECT sub_a.UserRealname FROM sys_user sub_a WHERE sub_a.UserId = p.`CreatedBy` LIMIT 1),', ',p.`DateCreated`) AS ModifiedBy
            FROM
                ktv_persons p
                INNER JOIN ktv_staffs s ON p.`PersonID` = s.`PersonID`
                LEFT JOIN ktv_staff_positions sp on sp.StaffPosStaffID = s.StaffID
                LEFT JOIN ktv_ref_position_type pos ON sp.`StaffPosPositionID` = pos.`PositionID`
                LEFT JOIN ktv_ref_work_area wa ON p.`WorkAreaID` = wa.`WorkAreaID`
                LEFT JOIN ktv_province pv ON wa.ProvinceID = pv.ProvinceID
                LEFT JOIN ktv_district ds ON wa.DistrictID = ds.DistrictID
                LEFT JOIN ktv_persons subp ON subp.`UserID` = p.CreatedBy                
                LEFT JOIN sys_user su ON p.`UserID` = su.`UserId`
                LEFT JOIN sys_role sro on s.ObjType = sro.RoleCode
            WHERE
                p.`StatusCd` = 'active'
                AND s.`StatusCode` <> 'nullified'
                AND s.`ObjType` IN ('program')
                AND (su.UserIsAdmin = 0 OR su.UserIsAdmin IS NULL)
                AND pos.`PositionID` IN ('1','8','148')
                $sqlWhereCountry
				$sqlWhereProv
                $sqlWhereAccessStaff
                $sqlWhereSimple
            GROUP BY p.PersonID
            ORDER BY $sortingField $sortingDir
        ";
        //echo $sql; die;
        
        if($opsiCall == 'non_grid'){
    		
            //Non Grid Call
    		return $this->db->query($sql)->result_array();
    	}else{
    		//For Grid
    		$p = array(
	    		(int) $start,(int) $limit
	    	);
    		$result['data'] = $this->db->query($sql." LIMIT ?,?",$p)->result_array();
	        $result['sql'] = $this->db->last_query();

    		$query = $this->db->query('SELECT FOUND_ROWS() AS total');
	        $result['total'] = $query->row()->total;

	        //generate information grid result (begin)
	        if ($sortingDir == 'ASC') {
	            $sortingInfo = 'ascending';
	        }
	        if ($sortingDir == 'DESC') {
	            $sortingInfo = 'descending';
	        }

	        foreach ($pSearch as $key => $value) {
	            if ($value != "") {
	                switch ($key) {
	                	case 'country':
	                		$infoFilter .= '<li>'.lang('Filter by').' ' . lang('Country') . '</li>';
	                	break;
	                    case 'prov':
	                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('Province') . '</li>';
	                        break;
	                    case 'textSearch':
	                        $infoFilter .= '<li>'.lang('Filter by').' ' . lang('Name') . '</li>';
	                        break;
	                    default:
	                    	$infoFilter = '';
	                    break;
	                }
	            }
	        }

	        $_SESSION['informationGrid'] = '
            <div class="Sfr_BoxInfoDataGrid_Title"><strong>' . number_format($query->row()->total, 0, ".", ",") . '</strong> ' . lang('Data') . '</div>
            <ul class="Sft_UlListInfoDataGrid">
                <li class="Sft_ListInfoDataGrid">
                    <img class="Sft_ListIconInfoDataGrid" src="' . base_url() . '/assets/images/sort.png" width="20" />&nbsp;&nbsp;Sorted by ' . lang($sortingField) . ' ' . $sortingInfo . '
                </li>
            </ul>';
	        //generate information grid result (end)

            return $result;
        }
    }

    public function GetExtStaffFormAdditionalOpen($PersonID,$StaffID){        
        $DataForm = array();

        $sql = "SELECT
                p.`AccountBeneficiary`
                , p.`BankID`
                , p.`AccountNumber`
                , p.`RegID`
                , rg.`RegType`
            FROM
                ktv_persons p
                INNER JOIN reg_region rg ON p.RegID = rg.RegID
                LEFT JOIN reg_country ct ON SUBSTR(p.`RegID`,1,2) = ct.`CountryID`
                LEFT JOIN reg_province pv ON SUBSTR(p.`RegID`,1,4) = pv.ProvinceID
                LEFT JOIN reg_district ds ON SUBSTR(p.`RegID`,1,8) = ds.DistrictID
                LEFT JOIN reg_subdistrict sd ON SUBSTR(p.`RegID`,1,12) = sd.SubDistrictID
                LEFT JOIN reg_village vl ON p.`RegID` = vl.VillageID
            WHERE
                p.`PersonID` = ?
            LIMIT 1";
        $query = $this->db->query($sql, array((int) $PersonID));
        $data = $query->row_array();

        //prep variable
        $DataForm = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.Ext_staff.PanelFormAdditional-Form-".$key;
            $DataForm[$keyNew] = $value;
        }

        //Buat dipakai langsung di JS
        $DataForm['BankID'] = $data['BankID'];
        $DataForm['RegID']  = $data['RegID'];

        $return['success'] = true;
        $return['data'] = $DataForm;
        return $return;
    }

    public function GetExtStaffFormOpen($PersonID,$StaffID){
        $DataForm = array();

        $sql = "SELECT
                p.`PersonID`
                , s.`StaffID`
                , s.`StaffRegisteredNumber`
                , p.`PersonNm`
                , p.`BirthDate` Birthdate
                , p.`Gender`
                , p.`Education`
                , s.ObjID `PartnerID`
                , dis.`DistrictID`
                , pv.`ProvinceID`
                , p.`Address`
                , p.Photo as Photo
                , p.`Photo` AS StaffPhotoPath
                , p.`OfficialCellPhoneCode` AS HandphoneCode
                , p.`OfficialCellPhone` AS Handphone
                , p.`Email`
                , pv.Province
                , dis.District
                , su.UserInCognito
                , s.StatusCode
                , s.`ObjType` AS StaffRole
                , s.`ObjID` AS StaffObjID
                , sp.StaffPosPositionID
            FROM
                ktv_persons p
                INNER JOIN ktv_staffs s ON p.`PersonID` = s.`PersonID`
                LEFT JOIN ktv_ref_work_area wa ON p.`WorkAreaID` = wa.`WorkAreaID`
                LEFT JOIN ktv_district dis ON wa.`DistrictID` = dis.`DistrictID`
                LEFT JOIN ktv_province pv ON wa.ProvinceID = pv.ProvinceID
                LEFT JOIN ktv_country ct ON pv.`CountryCode` = ct.`ISO2`
                LEFT JOIN sys_user su ON su.UserId = p.UserId
                LEFT JOIN ktv_staff_positions sp on sp.StaffPosStaffID = s.StaffID
            WHERE
                p.`PersonID` = ?
            LIMIT 1";
        $query = $this->db->query($sql, array((int) $PersonID));
        $data = $query->row_array();

        //prep variable
        $DataForm = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.Ext_staff.MainForm-FormBasicData-".$key;
            $DataForm[$keyNew] = $value;
        }

        //Buat dipakai langsung di JS
        $DataForm['Photo'] = $data['Photo'];
        $DataForm['Gender'] = $data['Gender'];
        $DataForm['CountryName'] = $data['CountryName'];
        $DataForm['ProvinceName'] = $data['ProvinceName'];
        $DataForm['DistrictName'] = $data['DistrictName'];
        $DataForm['SubDistrictName'] = $data['SubDistrictName'];
        $DataForm['VillageName'] = $data['VillageName'];
        $DataForm['RegType'] = $data['RegType'];
        $DataForm['UserInCognito'] = $data['UserInCognito'];
        $DataForm['StaffRole'] = $data['StaffRole'];
        $DataForm['StaffRoleEntity'] = $data['StaffObjID'];

        //Check gambarnya ada tidak
        $this->load->library('awsfileupload');

        if($DataForm['Photo'] != "") {
            //Cek ada tidak filenya di AWS
            if($this->awsfileupload->doesObjectExist($DataForm['Photo']) == false) {
                $DataForm['Photo'] = null;
            }
        } else {
            $DataForm['Photo'] = null;
        }

        $return['success'] = true;
        $return['data'] = $DataForm;
        return $return;
    }

    private function GenerateExtStaffID(){
        $sql = "SELECT
                    a.`PersonID`+1 AS PersonID
                FROM
                    ktv_persons a
                ORDER BY a.`PersonID` DESC
                LIMIT 1";
        $data = $this->db->query($sql)->row_array();
        return "EXT-".$data['PersonID'];
    }

    private function GenerateFarmerAssignID(){
        return "EXT-".date('ymdHis').mt_rand(0,9);
    }

    public function InsertExtStaff($paramPost){
        $this->db->trans_begin();

        // Generate ID
        $ExtID = $this->GenerateExtStaffID();

        $PosID = 2;

        // switch($paramPost['CallFrom']){
        //     case 'agro':
        //         $PosID = 1;
        //     break;
        //     case 'tech':
        //         $PosID = 2;
        //     break;
        // }
        $postPerson['PersonNm']     = $paramPost['PersonNm'];
        $postPerson['BirthDate']    = $paramPost['Birthdate'];
        $postPerson['Gender']       = $paramPost['Gender'];
        $postPerson['RegID']        = $paramPost['RegID'];
        $postPerson['CountryID']    = substr($paramPost['RegID'], 0,2);
        $postPerson['Address']      = $paramPost['Address'];
        $postPerson['Email']        = $paramPost['Email'];
        $postPerson['EmpNr']        = $ExtID;
        $postPerson['OfficialEmail']            = $paramPost['Email'];
        $postPerson['HandphoneType']            = $paramPost['HandphoneType'];
        $postPerson['OfficialCellPhoneCode']    = $paramPost['HandphoneCode'];
        $postPerson['OfficialCellPhone']        = $paramPost['Handphone'];
        $postPerson['Education']    = $paramPost['Education'];
        $postPerson['StatusCd']     = 'active';
        $postPerson['PosID']        = $PosID;
        $postPerson['Company']      = $paramPost['Company'];
        $postPerson['DateCreated']  = date('Y-m-d H:i:s');
        $postPerson['CreatedBy']    = $_SESSION['userid'];

        $query = $this->db->insert("ktv_persons",$postPerson);
        $PersonID = $this->db->insert_id();

        //Cari StaffID Reference
        $sql = "SELECT
                    s.`StaffID`
                FROM
                    sys_user su
                    INNER JOIN ktv_persons p ON su.`UserId` = p.`UserID`
                    INNER JOIN ktv_staffs s ON p.`PersonID` = s.`PersonID`
                WHERE
                    su.`UserId` = ?
                LIMIT 1";
        $DataRef = $this->db->query($sql,array($_SESSION['userid']))->row_array();

        $postStaff['PersonID']  = $PersonID;
        $postStaff['ObjType']   = $paramPost['StaffRole'];
        $postStaff['ObjID']   = $paramPost['StaffRoleEntity'];
        $postStaff['PartnerID'] = $_SESSION['PartnerID'];
        $postStaff['IsGeneralStaff']        = 'Yes';
        $postStaff['StaffRegisteredNumber'] = $ExtID;
        $postStaff['HandphoneType']         = $paramPost['HandphoneType'];
        $postStaff['OfficialPhoneCode']     = $paramPost['HandphoneCode'];
        $postStaff['OfficialPhone']         = $paramPost['Handphone'];
        $postStaff['WorkPhone']             = $paramPost['HandphoneCode'].$paramPost['Handphone'];
        $postStaff['OfficialEmail']         = $paramPost['Email'];
        $postStaff['StaffReferenceID']      = $DataRef['StaffID'];
        $postStaff['StatusCode']   = $paramPost['StatusCode'];
        $postStaff['DateCreated']  = date('Y-m-d H:i:s');
        $postStaff['CreatedBy']    = $_SESSION['userid'];

        $query = $this->db->insert('ktv_staffs',$postStaff);
        $StaffID = $this->db->insert_id();

        //Cek Foto
        if(isset($paramPost['PhotoOld'])){
            //Proses foto
            if ($paramPost['PhotoOld'] != "" && file_exists("images/staff/temp/".$paramPost['PhotoOld'])) {

                $path = AWSS3_STAFF_PHOTO_PATH;
                $pathPhoto = "images/staff/temp/".$paramPost['PhotoOld'];
                $splitPath = explode('/', $paramPost['PhotoOld']);
                $fileName = end($splitPath);

                //upload ke aws s3
                $this->load->library('awsfileupload');
                $upload = $this->awsfileupload->upload($pathPhoto, $fileName, $path, 'images');

                if ($upload['success'] == true) {
                    $sql = "UPDATE ktv_persons a SET
                            a.`Photo` = ?
                        WHERE
                            a.`PersonID` = ?
                        LIMIT 1";
                    $p = array(
                        $upload['filenamepath'],
                        $PersonID
                    );
                    $query = $this->db->query($sql,$p);
                }

                //hapus foto temporary
                delete_file($pathPhoto);
            }
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = lang("Failed to save data");
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = lang("Data saved");
            $results['PersonID'] = $PersonID;
            $results['StaffID'] = $StaffID;
        }
        return $results;
    }

    public function UpdateExtStaffAdditional($paramPost){
        $this->db->trans_begin();

        $postPerson['AccountBeneficiary']     = $paramPost['AccountBeneficiary'];
        $postPerson['BankID']    = $paramPost['BankID'];
        $postPerson['AccountNumber']       = $paramPost['AccountNumber'];
        $postPerson['DateUpdated']  = date('Y-m-d H:i:s');
        $postPerson['LastModifiedBy']    = $_SESSION['userid'];

        $this->db->where('PersonID',$paramPost['PersonID']);
        $query = $this->db->update("ktv_persons",$postPerson);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = lang("Failed to save data");
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = lang("Data saved");
            $results['PersonID'] = $paramPost['PersonID'];
            $results['StaffID'] = $paramPost['StaffID'];
        }
        return $results;
    }

    public function UpdateExtStaff($paramPost){
        $this->db->trans_begin();

        // switch($paramPost['CallFrom']){
        //     case 'agro':
        //         $PosID = 1;
        //     break;
        //     case 'tech':
                $PosID = 2;
        //     break;
        // }
        
        $postPerson['PersonNm']     = $paramPost['PersonNm'];
        $postPerson['BirthDate']    = $paramPost['Birthdate'];
        $postPerson['Gender']       = $paramPost['Gender'];
        $postPerson['RegID']        = $paramPost['RegID'];
        $postPerson['CountryID']    = substr($paramPost['RegID'], 0,2);
        $postPerson['Address']      = $paramPost['Address'];
        $postPerson['Email']        = $paramPost['Email'];
        $postPerson['OfficialEmail']            = $paramPost['Email'];
        $postPerson['HandphoneType']            = $paramPost['HandphoneType'];
        $postPerson['OfficialCellPhoneCode']    = $paramPost['HandphoneCode'];
        $postPerson['OfficialCellPhone']        = $paramPost['Handphone'];
        $postPerson['Education']    = $paramPost['Education'];
        $postPerson['Company']      = $paramPost['Company'];
        $postPerson['DateUpdated']  = date('Y-m-d H:i:s');
        $postPerson['LastModifiedBy']    = $_SESSION['userid'];

        $this->db->where('PersonID',$paramPost['PersonID']);
        $query = $this->db->update("ktv_persons",$postPerson);


        $postStaff['HandphoneType']         = $paramPost['HandphoneType'];
        $postStaff['ObjID']   = $paramPost['StaffRoleEntity'];
        $postStaff['OfficialPhoneCode']     = $paramPost['HandphoneCode'];
        $postStaff['OfficialPhone']         = $paramPost['Handphone'];
        $postStaff['WorkPhone']             = $paramPost['HandphoneCode'].$paramPost['Handphone'];
        $postStaff['OfficialEmail']         = $paramPost['Email'];
        $postStaff['StatusCode']            = $paramPost['StatusCode'];
        $postStaff['DateUpdated']           = date('Y-m-d H:i:s');
        $postStaff['LastModifiedBy']        = $_SESSION['userid'];

        $this->db->where('StaffID',$paramPost['StaffID']);
        $query = $this->db->update('ktv_staffs',$postStaff);

        //Cek apakah sudah ada user
        $sql = "SELECT
                    su.`UserId`
                FROM
                    sys_user su
                    INNER JOIN ktv_persons p ON su.`UserId` = p.`UserID`
                WHERE
                    p.`PersonID` = ?
                LIMIT 1";
        $DataCek = $this->db->query($sql,array($paramPost['PersonID']))->row_array();
        if(isset($DataCek['UserId'])){
            $sql = "UPDATE sys_user a SET
                        a.`UserRealName` = ?,
                        a.`UserEmail` = ?
                    WHERE
                        a.`UserId` = ?
                    LIMIT 1
                    ";
            $p = array(
                $paramPost['PersonNm'],
                $paramPost['Email'],
                $DataCek['UserId']
            );
            $query = $this->db->query($sql,$p);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = lang("Failed to save data");
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = lang("Data saved");
            $results['PersonID'] = $paramPost['PersonID'];
            $results['StaffID'] = $paramPost['StaffID'];
        }
        return $results;
    }

    public function UpdatePhotoExtStaff($gambar,$PersonID){
        //Cek terlebih dahulu, apakah ada foto lama, kalau ada dihapus dl
        $sql = "SELECT
                    a.`Photo` AS PhotoPath
                FROM
                    ktv_persons a
                WHERE
                    a.`PersonID` = ?
                LIMIT 1";
        $DataCek = $this->db->query($sql, array($PersonID))->row_array();
        if (isset($DataCek['PhotoPath']) && $DataCek['PhotoPath'] != "") {
            $this->load->library('awsfileupload');
            $this->awsfileupload->delete($DataCek['PhotoPath']);
        }

        $sql = "UPDATE ktv_persons a SET
                    a.`Photo` = ?
                WHERE
                    a.`PersonID` = ?
                LIMIT 1";
        $query = $this->db->query($sql,array($gambar,$PersonID));
    }

    public function DeleteExtStaff($PersonID,$StaffID){
        $this->db->trans_begin();

        //cek apakah sudah ada user
        $sql = "SELECT
                    su.`UserId`
                FROM
                    sys_user su
                    INNER JOIN ktv_persons p ON su.`UserId` = p.`UserID`
                WHERE
                    p.`PersonID` = ?
                LIMIT 1";
        $DataCek = $this->db->query($sql,array($paramPost['PersonID']))->row_array();
        if(isset($DataCek['UserId'])){
            $results['success'] = false;
            $results['message'] = lang("Failed to delete staff, User already created");
            return $results;
        }else{
            $sql = "DELETE FROM ktv_staffs WHERE StaffID = ? LIMIT 1";
            $query = $this->db->query($sql,array($StaffID));

            $sql = "DELETE FROM ktv_persons WHERE PersonID = ? LIMIT 1";
            $query = $this->db->query($sql,array($PersonID));
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = lang("Failed to delete staff");
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = lang("Staff deleted");
        }
        return $results;
    }

    public function InsertExtStaffUserAcc($ParamPost){
        //echo '<pre>'; print_r($ParamPost); exit;
        //Cek username sudah ada belum
        $sql="SELECT
                UserId
            FROM
                sys_user
            WHERE
                UserName = ?
            LIMIT 1";
        $query = $this->db->query($sql,array($ParamPost['Username']));
        $dataCekUsername = $query->row_array();

        if(isset($dataCekUsername['UserId'])){
            $results['success'] = false;
            $results['message'] = lang("Username already existed");

            return $results;
        }

        $UpdateDhis = true;

        //data ktv_person
        $sql="SELECT
                b.PersonID,
                b.PersonNm,
                b.Email
            FROM
                ktv_staffs a
                INNER JOIN ktv_persons b ON a.`PersonID` = b.`PersonID`
            WHERE
                a.`StaffID` = ?
            LIMIT 1";
        $query = $this->db->query($sql,array($ParamPost['StaffID']));
        $dataStaff = $query->row_array();

        //Insert User DHIS ============================================ (Begin)
            $tmpName   = explode(" ", $dataStaff['PersonNm']);
            $firstName = $tmpName[0];
            unset($tmpName[0]);
            $lastName = implode(" ", $tmpName);
            if(strlen($lastName) < 2){
                switch (strlen($lastName)) {
                    case 0:
                        $lastName = "..";
                    break;
                    case 1:
                        $lastName = $lastName.".";
                    break;
                }
            }

            //org unit (begin)
            $sql="SELECT
                    DISTINCT ct.`CountryID`
                    , ct.CountryUID AS uid
                FROM
                    sys_group a
                    LEFT JOIN sys_group_access b ON a.`GroupId` = b.`GroupID`
                    LEFT JOIN reg_province prov ON b.`ProvinceID` = prov.`ProvinceID`
                    LEFT JOIN reg_country ct ON prov.`CountryID` = ct.`CountryID`
                WHERE
                    a.`GroupId` = ?
                ORDER BY ct.`CountryName` ASC";
            $query = $this->db->query($sql,array($ParamPost['UserGroupIsDefault']));
            $dataOrgUnit = $query->result_array();
            $tmpJson = array();
            foreach ($dataOrgUnit as $key => $value) {
                if($value['uid'] != ""){
                    $tmpJson[]['id'] = $value['uid'];
                }
            }
            $jsonOrgUnit = json_encode($tmpJson);

            //User Group DHIS ============================= (Begin)
            if($ParamPost['AppGroupUid'] != ""){
                $AppGroupUidRaw = $ParamPost['AppGroupUid'];

                $TmpAppGroupUid = explode(',',$ParamPost['AppGroupUid']);
                $TmpJsonAppGroupUid = array();            
                foreach ($TmpAppGroupUid as $key => $value) {
                    $TmpJsonAppGroupUid[]['id'] = $value;
                }
                $JsonAppGroupUid = json_encode($TmpJsonAppGroupUid);
            }else{
                $JsonAppGroupUid = null;
                $AppGroupUidRaw = null;
            }
            //User Group DHIS ============================= (End)

            $bodyJson = '{
                "firstName": "'.$firstName.'",
                "surname": "'.$lastName.'",
                "email": "'.$dataStaff['Email'].'",
                "userCredentials": {
                    "username": "'.$ParamPost['Username'].'",
                    "password": "'.$ParamPost['UserPassword'].'",
                "userRoles": [ {
                    "id": "'.$ParamPost['AppRoleUid'].'"
                } ]
                },
                "organisationUnits": '.$jsonOrgUnit.',
                "userGroups": '.$JsonAppGroupUid.'
            }';

            $url = $this->config->item('dhis_url').'api/users';
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
              'Content-Type: application/json',
              'Authorization: Basic '.$this->config->item('dhis_basic_auth')
            ));
            curl_setopt($ch, CURLOPT_POSTFIELDS, ($bodyJson));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
            $result = curl_exec($ch);

            $curlresult = json_decode($result,true);
    
            if($curlresult['lastImported'] != "") {
                $UidDhisUser = $curlresult['lastImported'];
            }else{
                $UpdateDhis = false;
            }
        //Insert User DHIS ============================================ (End)

        if($UpdateDhis == true){
            //Start Trans
            $this->db->trans_begin();
        
            if($ParamPost['StatusCode'] == 'active'){
                $UserActive = 'Yes';
                $StatusCode = 'active';
            }else{
                $UserActive = 'No';
                $StatusCode = 'inactive';
            }

            switch ($ParamPost['UserLanguage']) {
                case '1':
                    $UserLanguage = 'English';
                break;
                case '2':
                    $UserLanguage = 'Chinese';
                break;
                case '3':
                    $UserLanguage = 'French';
                break;
                default:
                    $UserLanguage = 'English';
                break;
            }

            // insert user
            $UserAddUserId      = $_SESSION['userid'];
            $UserAddTime        = date('Y-m-d H:i:s');
            $UserUpdateUserId   = $_SESSION['userid'];
            $UserUpdateTime     = date('Y-m-d H:i:s');

            $p = array(
                'UserName' => $ParamPost['Username'],
                'UserRealName' => $dataStaff['PersonNm'],
                'UserPassword' => md5($ParamPost['UserPassword']),
                'UserEmail' => $dataStaff['Email'],
                'UserActive' => $UserActive,
                'StatusCode' => $StatusCode,
                'UserLanguage' => $UserLanguage,
                'UserIsAdmin' => '0',
                'UserTorStatus' => '1',

                'UserExtId' => $UidDhisUser,
                'UserExtPassword' => md5($ParamPost['UserPassword']),
                'UserExtGroupId' => $AppGroupUidRaw,
                'UserExtRoleId' => $ParamPost['AppRoleUid'],

                'UserAddUserId' => $UserAddUserId,
                'UserAddTime' => $UserAddTime,
                'UserUpdateUserId' => $UserUpdateUserId,
                'UserUpdateTime' => $UserUpdateTime
            );
            $query = $this->db->insert('sys_user', $p);
            $UserId = $this->db->insert_id();

            // insert user groups
            $GroupIds = explode(',',$ParamPost['GroupIds']);
            foreach ($GroupIds as $key => $GroupId) {
                $isDefault = $GroupId == $ParamPost['UserGroupIsDefault'] ? '1' : '0';
                $p = array(
                    'UserGroupUserId' => $UserId,
                    'UserGroupGroupId' => $GroupId,
                    'UserGroupIsDefault' => $isDefault
                );
                $query = $this->db->insert('sys_user_group', $p);
            }

            //update user id di ktv_persons
            $sql="UPDATE ktv_persons SET
                        UserID = ?
                    WHERE
                        PersonID = ?
                    LIMIT 1";
            $query = $this->db->query($sql,array($UserId,$dataStaff['PersonID']));
        

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                $results['success'] = false;
                $results['message'] = lang("Failed to save data");
            } else {
                $this->db->trans_commit();
                $results['success'] = true;
                $results['message'] = lang("Data saved");
            }
            return $results;
        }else{
            $results['success'] = false;
            $results['message'] = lang("Create Account on DHIS Failed");
            return $results;
        }
    }

    public function UpdateExtStaffUserAcc($ParamPost){
        //update password
        $isGantiPass = false;
        if($ParamPost['UserPassword'] != ""){
            $isGantiPass = true;
        }
        $UpdateDhis = true;
        $UserId = $ParamPost['UserId'];

        //data ktv_person
        $sql="SELECT
                b.PersonID,
                b.PersonNm,
                b.Email,
                c.UserExtId
            FROM
                ktv_staffs a
                INNER JOIN ktv_persons b ON a.`PersonID` = b.`PersonID`
                INNER JOIN sys_user c ON b.UserID = c.UserId
            WHERE
                a.`StaffID` = ?
            LIMIT 1";
        $query = $this->db->query($sql,array($ParamPost['StaffID']));
        $dataStaff = $query->row_array();

        //update user ke dhis (begin)
        $tmpName   = explode(" ", $dataStaff['PersonNm']);
        $firstName = $tmpName[0];
        unset($tmpName[0]);
        $lastName = implode(" ", $tmpName);
        if(strlen($lastName) < 2){
            switch (strlen($lastName)) {
                case 0:
                    $lastName = "..";
                break;
                case 1:
                    $lastName = $lastName.".";
                break;
            }
        }

        if($isGantiPass == true){
            $jsonPassword = '"password": "'.$ParamPost['UserPassword'].'",';
        }else{
            $jsonPassword = '';
        }

        //org unit (begin)
        $sql="SELECT
                DISTINCT ct.`CountryID`
                , ct.CountryUID AS uid
            FROM
                sys_group a
                LEFT JOIN sys_group_access b ON a.`GroupId` = b.`GroupID`
                LEFT JOIN reg_province prov ON b.`ProvinceID` = prov.`ProvinceID`
                LEFT JOIN reg_country ct ON prov.`CountryID` = ct.`CountryID`
            WHERE
                a.`GroupId` = ?
            ORDER BY ct.`CountryName` ASC";
        $query = $this->db->query($sql,array($ParamPost['UserGroupIsDefault']));
        $dataOrgUnit = $query->result_array();
        $tmpJson = array();
        foreach ($dataOrgUnit as $key => $value) {
            if($value['uid'] != ""){
                $tmpJson[]['id'] = $value['uid'];
            }
        }
        $jsonOrgUnit = json_encode($tmpJson);

        //User Group DHIS ============================= (Begin)
        if($ParamPost['AppGroupUid'] != ""){
            $AppGroupUidRaw = $ParamPost['AppGroupUid'];

            $TmpAppGroupUid = explode(',',$ParamPost['AppGroupUid']);
            $TmpJsonAppGroupUid = array();
            foreach ($TmpAppGroupUid as $key => $value) {
                $TmpJsonAppGroupUid[]['id'] = $value;
            }
            $JsonAppGroupUid = json_encode($TmpJsonAppGroupUid);
        }else{
            $JsonAppGroupUid = null;
            $AppGroupUidRaw = null;
        }
        //User Group DHIS ============================= (End)

        $bodyJson = '{
            "firstName": "'.$firstName.'",
            "surname": "'.$lastName.'",
            "userCredentials": {
                "username": "'.$ParamPost['Username'].'",
                '.$jsonPassword.'
            "userRoles": [ {
                "id": "'.$ParamPost['AppRoleUid'].'"
            } ]
            },
            "organisationUnits": '.$jsonOrgUnit.',
            "userGroups": '.$JsonAppGroupUid.'
        }';

        $url = $this->config->item('dhis_url').'api/users/'.$dataStaff['UserExtId'];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'Content-Type: application/json',
          'Authorization: Basic '.$this->config->item('dhis_basic_auth')
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, ($bodyJson));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        $curlresult = json_decode($result,true);

        if($curlresult['status'] == "SUCCESS") {
            $UpdateDhis = true;
        }else{
            $results['success'] = false;
            $results['message'] = lang("Update Account on DHIS Failed");
            return $results;
        }

        if($UpdateDhis == true){
            $this->db->trans_begin();

            if($ParamPost['StatusCode'] == 'active'){
                $UserActive = 'Yes';
                $StatusCode = 'active';
            }else{
                $UserActive = 'No';
                $StatusCode = 'inactive';
            }
    
            switch ($ParamPost['UserLanguage']) {
                case '1':
                    $UserLanguage = 'English';
                break;
                case '2':
                    $UserLanguage = 'Chinese';
                break;
                case '3':
                    $UserLanguage = 'French';
                break;
                default:
                    $UserLanguage = 'English';
                break;
            }
    
            $UserUpdateUserId   = $_SESSION['userid'];
            $UserUpdateTime     = date('Y-m-d H:i:s');

            //update sys_user
            $p = array(
                'UserName' => $ParamPost['Username'],
                'UserRealName' => $dataStaff['PersonNm'],
                'UserActive' => $UserActive,
                'StatusCode' => $StatusCode,
                'UserExtGroupId' => $AppGroupUidRaw,
                'UserExtRoleId' => $ParamPost['AppRoleUid'],
                'UserLanguage' => $UserLanguage,
                'UserUpdateUserId' => $UserUpdateUserId,
                'UserUpdateTime' => $UserUpdateTime
            );
            $query = $this->db->update('sys_user', $p, compact('UserId'));

            //update password
            if($isGantiPass == true){
                $p = array(
                    'UserPassword' => md5($ParamPost['UserPassword']),
                    'UserExtPassword' => md5($ParamPost['UserPassword'])
                );
                $query = $this->db->update('sys_user', $p, compact('UserId'));
            }

            //delete group lalu insert
            $query = $this->db->delete('sys_user_group', array('UserGroupUserId' => $UserId));
            $GroupIds = explode(',',$ParamPost['GroupIds']);
            foreach ($GroupIds as $key => $GroupId) {
                $isDefault = $GroupId == $ParamPost['UserGroupIsDefault'] ? '1' : '0';
                $p = array(
                    'UserGroupUserId' => $UserId,
                    'UserGroupGroupId' => $GroupId,
                    'UserGroupIsDefault' => $isDefault
                );
                $query = $this->db->insert('sys_user_group', $p);
            }

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                $results['success'] = false;
                $results['message'] = lang("Failed to Update Data");
            } else {
                $this->db->trans_commit();
                $results['success'] = true;
                $results['message'] = lang("Data Updated");
            }
            return $results;
        }else{
            $results['success'] = false;
            $results['message'] = lang("Update Account on DHIS Failed");
            return $results;
        }
    }

    public function GetUserAccLoginGrid($PersonID,$StaffID){
        $sql = "SELECT
                    a.`SessionIP`
                    , a.`Timestamp`
                FROM
                    sys_log_access a
                    LEFT JOIN ktv_persons b ON a.`UserID` = b.`UserID`
                WHERE
                    b.`PersonID` = ?
                    AND a.`type` = 'Login'
                    AND a.`AttempProcess` = 'Success'
                ORDER BY a.`Timestamp` DESC
                LIMIT 10";
        $Data = $this->db->query($sql,array($PersonID))->result_array();

        $return['data'] = $Data;
        $return['success'] = true;
        return $return;
    }

    public function GetFarmerListGrid($StaffAssignmentID, $StaffID, $textSearch, $start = null, $limit = null, $opsiLimit = 'limit', $sortingField = '', $sortingDir = ''){
        if ($sortingField == "") $sortingField = 'StaffAssignmentMemberID';
        if ($sortingDir == "") $sortingDir = 'DESC';

        $filtersearch = ($textSearch != "") ? " AND ( s.MemberName like '%$textSearch%' OR s.MemberDisplayID like '%$textSearch%')" : "";

        $sql = "SELECT SQL_CALC_FOUND_ROWS
                sas.StaffAssignmentMemberID
                , sas.MemberID
                , s.MemberDisplayID
                , s.MemberName
                , CASE 
                        WHEN s.Gender = 'm' THEN 'Male'
                        WHEN s.Gender = 'f' THEN 'Female'
                        WHEN s.Gender = 'o' THEN 'Others'
                        ELSE '-'
                    END Gender
                , pv.Province Province
                , ds.District District
            FROM
                ktv_staffs_assignment_member sas
            LEFT JOIN
                ktv_staffs_assignment sa on sa.StaffAssignmentID = sas.StaffAssignmentID
            LEFT JOIN
                ktv_members s on s.MemberID = sas.MemberID
            LEFT JOIN
                ktv_village vil on vil.VillageID = s.VillageID
            LEFT JOIN
                ktv_subdistrict subd on subd.SubDistrictID = vil.SubDistrictID
            LEFT JOIN
                ktv_district ds on ds.DistrictID = subd.DistrictID
            LEFT JOIN
                ktv_province pv on ds.ProvinceID = pv.ProvinceID
            WHERE
                sas.StaffAssignmentID = ?
            $filtersearch
            AND
                sa.StaffID = ?";
            
            if ($opsiLimit == 'limit'){
                $sql = $sql . " ORDER BY `$sortingField` $sortingDir
                                LIMIT ?,?";
                $p = array($StaffAssignmentID, $StaffID, $start, $limit);
            } else{
                $sql = $sql;
                $p = array($StaffAssignmentID, $StaffID);
            }
    
            $Data = $this->db->query($sql,$p)->result_array();
    

            $query = $this->db->query('SELECT FOUND_ROWS() AS total');
            $return['total'] = $query->row()->total;
            $return['data'] = $Data;
            $return['success'] = true;
            return $return;
    }

    public function GetFarmerAssignmentGrid($StaffID, $start = null, $limit = null, $opsiLimit = 'limit', $sortingField = '', $sortingDir = ''){
        if ($sortingField == "") $sortingField = 'StatusCode';
        if ($sortingDir == "") $sortingDir = 'ASC';

        $sql = "SELECT SQL_CALC_FOUND_ROWS
                a.StaffAssignmentID
                , a.StaffAssignmentExtID
                , a.StartDate
                , a.EndDate
                , a.StatusCode
                , COUNT(b.MemberID) FarmerNr
            FROM
                ktv_staffs_assignment a
            LEFT JOIN
                ktv_staffs_assignment_member b on b.StaffAssignmentID = a.StaffAssignmentID
            WHERE
                a.StaffID = ?
            GROUP BY
                a.StaffAssignmentID";

        if ($opsiLimit == 'limit'){
            $sql = $sql . " ORDER BY `$sortingField` $sortingDir
                            LIMIT ?,?";
            $p = array($StaffID, $start, $limit);
        } elseif($opsiLimit == 'no_limit'){
            $sql = $sql;
            $p = array($StaffID);
        }

        $Data = $this->db->query($sql,$p)->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $return['total'] = $query->row()->total;

        $return['data'] = $Data;
        $return['success'] = true;
        return $return;
    }

    public function cekPerson($personID){
        return $this->db->get_where('ktv_persons', ['PersonID' => $personID]);
    }

    public function InsertFarmerAssign($paramPost){
        $datapost['StaffAssignmentExtID']        = $this->GenerateFarmerAssignID();
        $datapost['StaffID']        = $paramPost['StaffID'];
        $datapost['StartDate']      = $paramPost['StartDate'];
        $datapost['EndDate']        = $paramPost['EndDate'];
        $datapost['Description']    = $paramPost['Description'];
        $datapost['StatusCode']     = $paramPost['StatusCode'];
        $datapost['DateCreated']    = date('Y-m-d H:i:s');
        $datapost['CreatedBy']      = $_SESSION['userid'];

        $query = $this->db->insert('ktv_staffs_assignment',$datapost);

        if($query){
            $return['success'] = true;
            $return['success'] = lang('Data Saved');
        }else{
            $return['success'] = false;
            $return['success'] = lang('Failed Save Data');
        }
        
        return $return;
    }

    public function UpdateFarmerAssign($paramPost){
        $datapost['StartDate']      = $paramPost['StartDate'];
        $datapost['EndDate']        = $paramPost['EndDate'];
        $datapost['Description']    = $paramPost['Description'];
        $datapost['StatusCode']     = $paramPost['StatusCode'];
        $datapost['DateUpdated']    = date('Y-m-d H:i:s');
        $datapost['LastModifiedBy'] = $_SESSION['userid'];

        // if(date("Y-m-d", strtotime($paramPost["EndDate"])) > date("Y-m-d")){
        //     $sql = "SELECT
        //         sas.StaffAssignmentID
        //         , sas.MemberID
        //     FROM
        //         ktv_staffs_assignment_member sas
        //     WHERE
        //         sas.StaffAssignmentID = ?";
        //     $query = $this->db->query($sql, array($paramPost['StaffAssignmentID']));
        //     if($query->num_rows()>0){
        //         foreach($query->result_array() as $row){
        //             $sql2 = "SELECT
        //                 sas.StaffAssignmentID
        //                 , sas.MemberID
        //                 , CONCAT(s.MemberDisplayID, ' - ', s.MemberName) MemberName
        //             FROM
        //                 ktv_staffs_assignment_member sas
        //             LEFT JOIN
        //                 ktv_members s on s.MemberID = sas.MemberID
        //             LEFT JOIN
        //                 ktv_staffs_assignment sa on sa.StaffAssignmentID = sas.StaffAssignmentID
        //             WHERE
        //                 sas.StaffAssignmentID <> ?
        //             AND
        //                 sas.MemberID = ?
        //             AND
        //                 DATE(sa.EndDate) > DATE(NOW())
        //             ";
        //             $query2 = $this->db->query($sql2, array($paramPost['StaffAssignmentID'], $row['MemberID']));

        //             if($query2->num_rows()>0){
        //                 $data = $query2->row_array();
        //                 $return['success'] = false;
        //                 $return['message'] = $data["MemberName"].' '.lang('Already Assign to Antoher Agronomist');

        //                 return $return;
        //             }
        //         }
        //     }
        // }

        $this->db->where('StaffAssignmentID',$paramPost['StaffAssignmentID']);
        $query = $this->db->update('ktv_staffs_assignment',$datapost);

        if($query){
            $return['success'] = true;
            $return['message'] = lang('Data Saved');
        }else{
            $return['success'] = false;
            $return['message'] = lang('Failed Save Data');
        }
        
        return $return;
    }

    public function GetFarmerAssignForm($StaffAssignmentID){
        $sql = "SELECT
            a.StaffAssignmentID
            , a.StaffAssignmentExtID
            , a.StaffID
            , a.StartDate
            , a.EndDate
            , a.Description
            , a.StatusCode
        FROM
            ktv_staffs_assignment a
        WHERE
            a.StaffAssignmentID = ?";

        $query = $this->db->query($sql,array($StaffAssignmentID));
        $data  = $query->row_array();

        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.Ext_staff.WinFormFarmerAssignment-Form-" . $key;
            $dataRow[$keyNew] = $value;
        }

        $dataRow['StatusCode'] = $data["StatusCode"];
        
        $result['success']  = true;
        $result['data']     = $dataRow;

        return $result;
    }

    public function getMemberID($MemberDisplayID){
        $sql    = "SELECT MemberID FROM ktv_members WHERE MemberDisplayID = ?";
        $query  = $this->db->query($sql, array($MemberDisplayID))->row_array();

        return $query['MemberID'];
    }

    public function getMemberAdd($StaffAssignmentID, $StaffID, $textSearch, $ProvinceID, $DistrictID, $SubdistrictID, $VillageID, $start, $limit, $sortingField, $sortingDir){
        $this->load->model('grower/mgrower');
        if ($sortingField == "")
            $sortingField = 'a.MemberDisplayID';
        if ($sortingDir == "")
            $sortingDir = 'ASC';
        
        //========== Hak akses (Begin) =====================
        $sqlHakAkses = $this->mgrower->generateSqlHakAkses();

        $sql        = "SELECT StaffPosPositionID FROM ktv_staff_positions sp WHERE sp.StaffPosStaffID = ? LIMIT 1";
        $posstaff   = $this->db->query($sql, array($StaffID))->row_array();
        //========== Hak akses (End) =======================

        $sqlwhere = "";

        if($ProvinceID != ""){
            $sqlHakAkses["where"] = " AND pv.ProvinceID = '$ProvinceID'";
        }

        if($DistrictID != ""){
            $sqlHakAkses["where"] = " AND f.DistrictID = '$DistrictID'";
        }

        if($SubdistrictID != ""){
            $sqlHakAkses["where"] = " AND subd.SubDistrictID = '$SubdistrictID'";
        }

        if($VillageID != ""){
            $sqlHakAkses["where"] = " AND vil.VillageID = '$VillageID'";
        }

        $sql = "SELECT SQL_CALC_FOUND_ROWS
                a.MemberID
                , a.MemberDisplayID
                , a.MemberName FarmerName
                , f.District
                , subd.SubDistrict
                , vil.Village
            FROM
                ktv_members a
            LEFT JOIN 
            (
                SELECT
	                ksam.MemberID
                FROM
	                ktv_staffs_assignment_member ksam
                LEFT JOIN
	                ktv_staffs_assignment ksa on ksa.StaffAssignmentID = ksam.StaffAssignmentID
                LEFT JOIN
                    ktv_staff_positions sp on sp.StaffPosStaffID = ksa.StaffID
                WHERE
	                ksa.StatusCode = 'active'
                AND
                    sp.StaffPosPositionID = '{$posstaff['StaffPosPositionID']}'
                GROUP BY ksam.MemberID
            ) ksa ON ksa.MemberID = a.MemberID
            INNER JOIN
                ktv_member_role ep on ep.MemberID = a.MemberID AND ep.MRoleID = 1
            LEFT JOIN
                ktv_village vil on vil.VillageID = a.VillageID
            LEFT JOIN
                ktv_subdistrict subd on subd.SubDistrictID = vil.SubDistrictID
            LEFT JOIN
                ktv_district f on f.DistrictID = subd.DistrictID
            LEFT JOIN
                ktv_province pv on f.ProvinceID = pv.ProvinceID
            $sqlHakAkses[join]
            WHERE
                a.StatusCode = 'active'
            AND 
                ksa.MemberID IS NULL
            AND 
                (a.MemberName LIKE ? OR a.MemberDisplayID LIKE ?)
            $sqlHakAkses[where] 
            GROUP BY a.MemberID
            ORDER BY $sortingField $sortingDir
            LIMIT ?,?";

        $query = $this->db->query($sql, array(
            '%'.$textSearch.'%', 
            '%'.$textSearch.'%',
            (int) $start, (int) $limit)
        );
        $result['data'] = $query->result_array();
        // echo '<pre>'; print_r($this->db->last_query()); exit;

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }
    
    public function insertMember($MemberID, $StaffAssignmentID, $StaffID){
        $this->db->trans_begin();

        foreach ($MemberID as $key => $value) {
            # code...
            $this->db->insert('ktv_staffs_assignment_member', array(
                'MemberID' =>  $value,
                'StaffAssignmentID' => $StaffAssignmentID,
                'CreatedBy' => date('Y-m-d H:i:s'),
                'DateCreated' => $_SESSION['userid']
            ));
        }

        if ($this->db->trans_complete() === false) {
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        } else {
            $results['success'] = true;
            $results['message'] = "Data saved";
        }

        return $results;
    }

    public function insertMemberAssign($SupplierID, $StaffAssignmentID, $StaffID){
        $this->db->trans_begin();

        $sqlpos        = "SELECT StaffPosPositionID FROM ktv_staff_positions sp WHERE sp.StaffPosStaffID = ? LIMIT 1";
        $posstaff   = $this->db->query($sqlpos, array($StaffID))->row_array();

        $sukses = 0;
        $exist  = 0;
        $agro  = 0;
        $tmpagro = 0;
        foreach ($SupplierID as $key => $value) {
            # code...
            $sql = "SELECT
                    ksam.MemberID
                    , ksa.StaffID
                FROM
                    ktv_staffs_assignment_member ksam
                LEFT JOIN
                    ktv_staffs_assignment ksa on ksa.StaffAssignmentID = ksam.StaffAssignmentID
                LEFT JOIN
                    ktv_staff_positions sp on sp.StaffPosStaffID = ksa.StaffID
                WHERE
                    ksa.StatusCode = 'active'
                AND sp.StaffPosPositionID = '{$posstaff['StaffPosPositionID']}'
                AND ksam.MemberID = ?";

            $query  = $this->db->query($sql, array($value));
            if($query->num_rows()>0){
                foreach($query->result_array() as $key => $row){
                    if($tmpagro <> $row["StaffID"]){
                        $agro ++;
                    }
                    $tmpagro = $row["StaffID"];
                }
                $exist++;
            }else{
                $this->db->insert('ktv_staffs_assignment_member', array(
                    'MemberID' =>  $value,
                    'StaffAssignmentID' => $StaffAssignmentID,
                    'CreatedBy' => $_SESSION['userid'],
                    'DateCreated' => date('Y-m-d H:i:s')
                ));
                $sukses++;
            }
        }

        if ($this->db->trans_complete() === false) {
            $results['success'] = false;
            $results['message'] = lang("Failed to save data");
        } else {
            $results['success'] = true;
            $results['message'] = lang("Data Imported");
            $results['Insert']  = $sukses;
            $results['Exist']   = $exist;
            $results['Agro']    = $agro;
        }

        return $results;
    }
}