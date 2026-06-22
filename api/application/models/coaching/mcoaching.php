<?php

/* * ****************************************
 *  Author : hasbycs@gmail.com
 *  Created On : 2021-10-06
 *  File : mcoaching.php
 * ***************************************** */

class Mcoaching extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function CoachingIMSList(){
        $where = "";

        if ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program") {
            $where .= " AND b.FirstBuyerPartnerID = '$_SESSION[PartnerID]'";
        }
        
        $sql = "SELECT
            a.IMSID id
            , CONCAT(a.IMSID, ' - ', '[',UPPER(
                CASE
                    WHEN kch.`ObjType` = 'farmer_group' THEN 'Farmer Group'
                    WHEN kch.`ObjType` = 'cooperative' THEN 'Cooperative'
                    ELSE 'Org'
                END
            ),'] ',kch.`CertHolderOrgName`, ' - ', a.CertEventName) AS label
        FROM
            ktv_ims a
        LEFT JOIN
            ktv_first_buyer b on b.FirstBuyerID = a.FirstBuyerID
        LEFT JOIN 
            ktv_certification_holders kch ON kch.CertHolderID = a.CertHolderID
        LEFT JOIN 
            view_tc_supplychain_org kso ON kso.SupplychainID = kch.SupplychainID
        WHERE
            1=1
        $where
        AND
            a.StatusCode = 'active'";
        
        $query = $this->db->query($sql, $p);

        $result['success'] = true;
        $result['data'] = $query->result_array();

        return $result;
    }

    public function GetGridMain($pSearch, $start, $limit, $sortingField, $sortingDir) {
        if ($sortingField == "")
            $sortingField = 'CoachingDate';
        if ($sortingDir == "")
            $sortingDir = 'DESC';

        //========== View akses (Begin) =======================
        if ($_SESSION['is_admin'] == "1") {
            $sqlHakAkses['join'] = "";
            $sqlHakAkses['where'] = "";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program") {

            $sqlHakAkses['where'] = " AND SUBSTR(s.VillageID,1,4) IN (" . $_SESSION['daerah_access'] . ")";
            $sqlHakAkses['join'] = " INNER JOIN ktv_access_partner_member acc_pm ON s.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$_SESSION['PartnerID']}' ";

        } else {
            //cek ktv_access_staff
            $sqlHakAkses['where'] = " AND SUBSTR(s.VillageID,1,4) IN (" . $_SESSION['daerah_access'] . ")";
            $sqlHakAkses['join'] = "";
        }
        //========== View akses (End)   =======================

        ($pSearch['StartDate'] == "") ? $pSearch["StartDate"] = date("Y-m-d") : $pSearch["StartDate"] = date("Y-m-d", strtotime($pSearch["StartDate"]));
        ($pSearch['EndDate'] == "") ? $pSearch["EndDate"] = date("Y-m-d") : $pSearch["EndDate"] = date("Y-m-d", strtotime($pSearch["EndDate"]));
        ($pSearch['FarmerGroupID'] == "") ? $sqlHakAkses['where'] = '' : $sqlHakAkses['where'] = " AND s.FarmerGroupID = $pSearch[FarmerGroupID]";

        $sql = "SELECT SQL_CALC_FOUND_ROWS
                    fc.CoachingID
                    , fc.FarmerID SupplierID
                    , fc.UserID
                    , p.PersonNm
                    , s.MemberDisplayID
                    , IFNULL(fca.FarmerWorkerName, s.MemberName) CoachingRecipientName
                    , IF(fca.CoachingRecipient = 1, 'Registered Farmer',
                            IF(fca.CoachingRecipient = 2, 'Farmer Worker',
                                    IF(fca.CoachingRecipient = 3, 'Household Member', '-')
                                    )
                            ) AS CoachingRecipient
                    , fca.EventDate CoachingDate
                    , s.MemberName FarmerName
                    , CONCAT(fg.FarmerGroupID, ' - ', fg.GroupName) GroupName
                    , RANK() OVER(PARTITION BY fc.FarmerID ORDER BY fca.EventDate ASC) sesi
                FROM
                    ktv_ims_farmer_coaching fc
                LEFT JOIN
                    ktv_ims_farmer_coaching_activity fca on fca.CoachingID = fc.CoachingID
                INNER JOIN 
                    ktv_members s ON fc.FarmerID = s.`MemberID`
                LEFT JOIN
                    ktv_farmer_group fg on fg.FarmerGroupID = s.FarmerGroupID
                LEFT JOIN 
                    ktv_district ds ON SUBSTR(s.VillageID,1,4) = ds.DistrictID
                    $sqlHakAkses[join]
                INNER JOIN 
                    sys_user u ON u.UserId = fc.UserID
                INNER JOIN
                    ktv_persons p on p.UserID = u.UserID
                WHERE 1=1
                    $sqlHakAkses[where]
                    AND fc.StatusCode = 'active'
                AND 
                    (s.`MemberName` LIKE ? OR s.MemberDisplayID LIKE ? OR fca.FarmerWorkerName LIKE ?)
                AND fca.EventDate >= ? AND fca.EventDate <= ?
                GROUP BY
                    fc.CoachingID
                ORDER BY `$sortingField` $sortingDir
                LIMIT ?,?
                ";
        $p = array(
            '%' . $pSearch['KeySearch'] . '%', '%' . $pSearch['KeySearch'] . '%', '%' . $pSearch['KeySearch'] . '%', $pSearch['StartDate'], $pSearch['EndDate'], $start, $limit
        );
        $query = $this->db->query($sql, $p);

        $result['data'] = $query->result_array();
        // echo "<pre>";print_r($this->db->last_query());die;

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        if ($sortingDir == 'ASC') {
            $sortingInfo = lang('ascending');
        }
        if ($sortingDir == 'DESC') {
            $sortingInfo = lang('descending');
        }

        $_SESSION['informationGrid'] = '
            <div class="Sfr_BoxInfoDataGrid_Title"><strong>' . number_format($query->row()->total, 0, ".", ",") . '</strong> ' . lang('Data') . '</div>
            <ul class="Sft_UlListInfoDataGrid">
                <li class="Sft_ListInfoDataGrid">
                    <img class="Sft_ListIconInfoDataGrid" src="' . base_url() . '/assets/images/icons/sort.png" width="20" />&nbsp;&nbsp;' . lang('Sorted by') . ' ' . lang($sortingField) . ' ' . $sortingInfo . '
                </li>
            </ul>';

        return $result;
    }

    public function GetGridMainExport($pSearch, $start, $limit, $sortingField, $sortingDir){
        if ($sortingField == "")
            $sortingField = 'CoachingDate';
        if ($sortingDir == "")
            $sortingDir = 'DESC';

        //========== View akses (Begin) =======================
        if ($_SESSION['is_admin'] == "1") {
            $sqlHakAkses['join'] = "";
            $sqlHakAkses['where'] = "";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program") {

            $sqlHakAkses['where'] = " AND SUBSTR(s.VillageID,1,4) IN (" . $_SESSION['daerah_access'] . ")";
            $sqlHakAkses['join'] = " INNER JOIN ktv_access_partner_member acc_pm ON s.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$_SESSION['PartnerID']}' ";

        } else {
            //cek ktv_access_staff
            $sqlHakAkses['where'] = " AND SUBSTR(s.VillageID,1,4) IN (" . $_SESSION['daerah_access'] . ")";
            $sqlHakAkses['join'] = "";
        }
        //========== View akses (End)   =======================

        ($pSearch['StartDate'] == "") ? $pSearch["StartDate"] = date("Y-m-d") : $pSearch["StartDate"] = date("Y-m-d", strtotime($pSearch["StartDate"]));
        ($pSearch['EndDate'] == "") ? $pSearch["EndDate"] = date("Y-m-d") : $pSearch["EndDate"] = date("Y-m-d", strtotime($pSearch["EndDate"]));
        ($pSearch['FarmerGroupID'] == "") ? $sqlHakAkses['where'] = '' : $sqlHakAkses['where'] = " AND s.FarmerGroupID = $pSearch[FarmerGroupID]";

        $sql = "SELECT SQL_CALC_FOUND_ROWS
                    fc.CoachingID
                    , fc.FarmerID SupplierID
                    , fc.UserID
                    , p.PersonNm
                    , s.MemberDisplayID
                    , IFNULL(fca.FarmerWorkerName, s.MemberName) CoachingRecipientName
                    , IF(fca.CoachingRecipient = 1, 'Registered Farmer',
                            IF(fca.CoachingRecipient = 2, 'Farmer Worker',
                                    IF(fca.CoachingRecipient = 3, 'Household Member', '-')
                                    )
                            ) AS CoachingRecipient
                    , fca.EventDate CoachingDate
                    , s.MemberName FarmerName
                    , CONCAT(fg.FarmerGroupID, ' - ', fg.GroupName) GroupName
                    , RANK() OVER(PARTITION BY fc.FarmerID ORDER BY fca.EventDate ASC) NumberOfCoachingSessions
                FROM
                    ktv_ims_farmer_coaching fc
                LEFT JOIN
                    ktv_ims_farmer_coaching_activity fca on fca.CoachingID = fc.CoachingID
                INNER JOIN 
                    ktv_members s ON fc.FarmerID = s.`MemberID`
                LEFT JOIN
                    ktv_farmer_group fg on fg.FarmerGroupID = s.FarmerGroupID
                LEFT JOIN 
                    ktv_district ds ON SUBSTR(s.VillageID,1,4) = ds.DistrictID
                    $sqlHakAkses[join]
                INNER JOIN 
                    sys_user u ON u.UserId = fc.UserID
                INNER JOIN
                    ktv_persons p on p.UserID = u.UserID
                WHERE 1=1
                    $sqlHakAkses[where]
                    AND fc.StatusCode = 'active'
                AND 
                    (s.`MemberName` LIKE ? OR s.MemberDisplayID LIKE ? OR fca.FarmerWorkerName LIKE ?)
                AND fca.EventDate >= ? AND fca.EventDate <= ?
                GROUP BY
                    fc.CoachingID
                ORDER BY `$sortingField` $sortingDir
                ";
        $p = array(
            '%' . $pSearch['KeySearch'] . '%', '%' . $pSearch['KeySearch'] . '%', '%' . $pSearch['KeySearch'] . '%', $pSearch['StartDate'], $pSearch['EndDate']
        );
        $query = $this->db->query($sql, $p);

        $result = $query->result_array();
        // echo "<pre>";print_r($this->db->last_query());die;

        return $result;
    }

    public function CoachingFormOpen($CoachingID) {
        $return = array();

        $sql = "SELECT
                a.CoachingID
                , b.IMSID
                , IF(b.isCertified IS NULL, IF(b.IMSID IS NOT NULL, 1, 2), b.isCertified) isCertified
                , a.ActivityID
                , b.FarmerID SupplierID
                , b.UserID
                , b.Username
                , a.CoachingRecipient
                , a.FarmerWorkerName
                , a.EventDate CoachingDate
                , a.TimeStart
                , a.TimeEnd
                , ST_Y(a.LatLong) AS Latitude
                , ST_X(a.LatLong) AS Longitude
                , a.Sample_pH1 PhSample1
                , a.Sample_pH2 PhSample2
                , a.Sample_pH3 PhSample3
                , a.PhotoActPath CoachingPhoto
                , a.FarmerSigActPath CoachingRecipientSignature
                , a.`Comment` Remark
                , (SELECT
                        CONCAT(ss.MemberDisplayID,' - ',ss.MemberName)
                    FROM 
                        ktv_members ss
                    WHERE
                        ss.MemberID = b.FarmerID
                    LIMIT 1
                ) AS FarmerSupplierIDAuto
                , (SELECT
                            CONCAT(sp.PersonNm,' (',spa.PartnerName,')')
                    FROM
                        ktv_persons sp
                        INNER JOIN ktv_staffs ss ON sp.PersonID = ss.PersonID
                        INNER JOIN ktv_program_partner spa ON ss.ObjID = spa.PartnerID
                    WHERE
                        sp.UserId = b.UserID
                    LIMIT 1
                ) AS PersonIDAuto
                , sp.PersonID
        FROM
            `ktv_ims_farmer_coaching` b
        INNER JOIN
            ktv_ims_farmer_coaching_activity a on a.CoachingID = b.CoachingID
        LEFT JOIN 
            ktv_persons sp ON b.UserID = sp.UserID
        WHERE 1=1 
            AND a.CoachingID = ?
        LIMIT 1";
        $p = array(
            $CoachingID
        );
        $data = $this->db->query($sql, $p)->row_array();

        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.Coaching.MainForm-Form-" . $key;
            $dataRow[$keyNew] = $value;
        }
        $dataRow['CoachingPhoto'] = $data['CoachingPhoto'];
        $dataRow['CoachingRecipientSignature'] = $data['CoachingRecipientSignature'];

        //Check gambarnya ada tidak
        $this->load->library('awsfileupload');

        if ($dataRow['CoachingPhoto'] != "") {
            //Cek ada tidak filenya di AWS
            if($dataRow['CoachingPhoto']){
                if($this->awsfileupload->doesObjectExist($dataRow['CoachingPhoto']) == true) {
                    $dataRow['CoachingPhoto'] = $this->config->item('CTCDN')."/".$dataRow['CoachingPhoto'];
                }else{
                    $dataRow['CoachingPhoto'] = base_url().$dataRow['CoachingPhoto'];
                }
            }
        } else {
            $dataRow['CoachingPhoto'] = null;
        }

        if ($dataRow['CoachingRecipientSignature'] != "") {
            //Cek ada tidak filenya di AWS            
            if($dataRow['CoachingRecipientSignature']){
                if($this->awsfileupload->doesObjectExist($dataRow['CoachingRecipientSignature']) == true) {
                    $dataRow['CoachingRecipientSignature'] = $this->config->item('CTCDN')."/".$dataRow['CoachingRecipientSignature'];
                }else{
                    $dataRow['CoachingRecipientSignature'] = base_url().$dataRow['CoachingRecipientSignature'];
                }
            }
        } else {
            $dataRow['CoachingRecipientSignature'] = null;
        }

        $return['success'] = true;
        $return['data'] = $dataRow;
        return $return;
    }

    public function GetCoachingTaskGrid($pSearch, $start, $limit, $sortingField, $sortingDir) {
        $result = array();
        if ($sortingField == "")
            $sortingField = 'Deadline';
        if ($sortingDir == "")
            $sortingDir = 'DESC';

        $sql = "SELECT SQL_CALC_FOUND_ROWS
                a.ActivityNCID
                , a.Topic CoachingTopicID
                , a.Subtopic SubtopicID
                , b.Topic CoachingTopic
                , c.Subtopic Subtopic
                , CASE
                    WHEN a.UrgentlyStatus = '1' THEN 'Low'
                    WHEN a.UrgentlyStatus = '2' THEN 'Medium'
                    WHEN a.UrgentlyStatus = '3' THEN 'High'
                    ELSE '-'
                END AS UrgentlyStatus
                , cf.CoachingFinding Finding
                , CASE
                    WHEN a.ActivityType = '1' THEN 'Suggestion'
                    WHEN a.ActivityType = '2' THEN 'Practice'
                    WHEN a.ActivityType = '3' THEN 'Training'
                    ELSE '-'
                END AS ActivityType
                , cr.CoachingRecom Recommendation
                , a.Target
                , a.Deadline
                , CASE
                    WHEN a.ActNCStatus = '1' THEN 'Canceled'
                    WHEN a.ActNCStatus = '2' THEN 'Not Started'
                    WHEN a.ActNCStatus = '3' THEN 'In Progress'
                    WHEN a.ActNCStatus = '4' THEN 'Completed'
                    ELSE '-'
                END AS Status
                , a.Explanation Remark
        FROM 
            ktv_ims_farmer_coaching_activity_nc a
        LEFT JOIN
            ktv_ims_farmer_coaching_activity fca on fca.ActivityID = a.ActivityID
        LEFT JOIN 
            ktv_coaching_topic b ON b.TopicID = a.Topic
        LEFT JOIN
            ktv_coaching_subtopic c on c.SubtopicID = a.Subtopic
        LEFT JOIN
            ktv_coaching_finding cf on cf.CoachingFindingID = a.Finding
        LEFT JOIN
            ktv_coaching_recommendation cr on cr.CoachingRecomID = a.Recommendation
        WHERE 1=1
            AND fca.`CoachingID` = {$pSearch['CoachingID']}
        ORDER BY `$sortingField` $sortingDir
        LIMIT ?,?";
        $p = array($start, $limit);
        $result['data'] = $this->db->query($sql, $p)->result_array();

        $query = $this->db->query("SELECT FOUND_ROWS() AS total");
        $result['total'] = $query->row(0)->total;

        return $result;
    }

    public function UpdateCoachingPhoto($CoachingID, $gambarPath) {
        //Cek terlebih dahulu, apakah ada foto lama, kalau ada dihapus dl
        $sql = "SELECT
                    b.`PhotoActPath` AS PhotoPath
                FROM
                    ktv_ims_farmer_coaching_activity b
                WHERE
                    b.`CoachingID` = ?
                LIMIT 1";
        $DataCek = $this->db->query($sql, array($CoachingID,))->row_array();
        if (isset($DataCek['PhotoPath']) && $DataCek['PhotoPath'] != "") {
            $this->load->library('awsfileupload');
            $this->awsfileupload->delete($DataCek['PhotoPath']);
        }

        $sql = "UPDATE ktv_ims_farmer_coaching_activity SET
				PhotoActPath = ?
			WHERE
				CoachingID = ?
			LIMIT 1";
        $p = array(
            $gambarPath, $CoachingID
        );
        return $this->db->query($sql, $p);
    }
    
    public function UpdateCoachingRecipientSignature($CoachingID, $gambarPath) {
        //Cek terlebih dahulu, apakah ada foto lama, kalau ada dihapus dl
        $sql = "SELECT
                    b.`FarmerSigActPath` AS PhotoPath
                FROM
                    ktv_ims_farmer_coaching_activity b
                WHERE
                    b.`CoachingID` = ?
                LIMIT 1";
        $DataCek = $this->db->query($sql, array($CoachingID,))->row_array();
        if (isset($DataCek['PhotoPath']) && $DataCek['PhotoPath'] != "") {
            $this->load->library('awsfileupload');
            $this->awsfileupload->delete($DataCek['PhotoPath']);
        }

        $sql = "UPDATE ktv_ims_farmer_coaching_activity SET
				FarmerSigActPath = ?
			WHERE
				CoachingID = ?
			LIMIT 1";
        $p = array(
            $gambarPath, $CoachingID
        );
        return $this->db->query($sql, $p);
    }

    public function InsertCoaching($paramPost) {
        $this->db->trans_start();

        $Photo = $paramPost['CoachingPhotoOld'];
        $Signature = $paramPost['CoachingRecipientSignatureOld'];
        $PersonID = $paramPost['PersonID'];
        $Latitude = $paramPost['Latitude'];
        $Longitude = $paramPost['Longitude'];

        // Get UserID
        $sql = "SELECT a.UserID, u.UserName, s.StaffID
                FROM ktv_persons a
                LEFT JOIN sys_user u ON a.UserID = u.UserId
                LEFT JOIN ktv_staffs s on s.PersonID = a.PersonID
                WHERE a.PersonID = ?
                LIMIT 1";
        $p = array($PersonID);
        $User = $this->db->query($sql, $p)->row_array();
        
        $dataPostCoaching["FarmerID"]           = $paramPost["SupplierID"];
        $dataPostCoaching["IMSID"]              = $paramPost["IMSID"];
        $dataPostCoaching["isCertified"]        = $paramPost["isCertified"];
        $dataPostCoaching['UserID']             = $User['UserID'];
        $dataPostCoaching['StaffID']            = $User['StaffID'];
        $dataPostCoaching['Username']           = $User['UserName'];
        $dataPostCoaching['DateCreated']        = date('Y-m-d H:i:s');
        $dataPostCoaching['CreatedBy']          = $_SESSION['userid'];
        $dataPostCoaching['StatusCode']         = 'active';

        //insert
        $query = $this->db->insert('ktv_ims_farmer_coaching', $dataPostCoaching);
        $CoachingID = $this->db->insert_id();

        if($CoachingID != ''){
            $uid = uidGenerateCode();

            $dataPost['ActivityID']         = $uid;
            $dataPost['CoachingID']         = $CoachingID;
            $dataPost['FarmerID']           = $paramPost["SupplierID"];
            $dataPost['CoachingRecipient']  = $paramPost['CoachingRecipient'];
            $dataPost['FarmerWorkerName']   = $paramPost['FarmerWorkerName'];
            $dataPost['EventDate']          = $paramPost['CoachingDate'];
            $dataPost['TimeStart']          = $paramPost['TimeStart'];
            $dataPost['TimeEnd']            = $paramPost['TimeEnd'];
            $dataPost['Comment']            = $paramPost['Remark'];
            $dataPost['Sample_pH1']         = $paramPost['PhSample1'];
            $dataPost['Sample_pH2']         = $paramPost['PhSample2'];
            $dataPost['Sample_pH3']         = $paramPost['PhSample3'];
            $dataPost['DateCreated']        = date('Y-m-d H:i:s');
            $dataPost['CreatedBy']          = $_SESSION['userid'];
            $dataPost['StatusCode']         = 'active';
            $query = $this->db->insert('ktv_ims_farmer_coaching_activity', $dataPost);
        }

        //Koordinat Geometry ============= (Begin)
        if ($Latitude != "" && $Longitude != "") {
            $PointInsert = "POINT({$Latitude} {$Longitude})";

            $sql = "UPDATE ktv_ims_farmer_coaching_activity a SET
                        a.`LatLong` = ST_GEOMFROMTEXT('$PointInsert', 4326)
                    WHERE
                        a.`CoachingID` = ?
                    LIMIT 1";
            $p = array($CoachingID);
            $query = $this->db->query($sql, $p);
        }
        //Koordinat Geometry ============= (End)

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = lang("Data saved");
            $results['CoachingID'] = $CoachingID;
            //Proses farmer signature
            if ($Photo != "" && file_exists('files/tmp/' . $Photo)) {
                $pathCoachingPhoto = 'files/tmp/' . $Photo;

                //upload ke aws s3
                $this->load->library('awsfileupload');
                $upload = $this->awsfileupload->upload($pathCoachingPhoto, $Photo, AWSS3_COACHING_PHOTO_PATH, 'images');

                if ($upload['success'] == true) {
                    $sql = "UPDATE `ktv_ims_farmer_coaching_activity` SET `PhotoActPath` = ? WHERE CoachingID = ? LIMIT 1";
                    $query = $this->db->query($sql, array($upload['filenamepath'], $CoachingID));
                }

                //hapus foto temporary
                delete_file($pathCoachingPhoto);
            }
            $results['CoachingPhotoOld'] = $Photo;
            $results['UploadCoachingPhotoOld'] = $upload;
            //Proses evidence photo
            if ($Signature != "" && file_exists('files/tmp/' . $Signature)) {
                $pathSignature = 'files/tmp/' . $Signature;

                //upload ke aws s3
                $this->load->library('awsfileupload');
                $upload = $this->awsfileupload->upload($pathSignature, $Signature, AWSS3_COACHING_SIGNATURE_PATH, 'images');

                if ($upload['success'] == true) {
                    $sql = "UPDATE `ktv_ims_farmer_coaching_activity` SET `FarmerSigActPath` = ? WHERE CoachingID = ? LIMIT 1";
                    $query = $this->db->query($sql, array($upload['filenamepath'], $CoachingID));
                }

                //hapus foto temporary
                delete_file($pathSignature);
            }
            $results['CoachingRecipientSignatureOld'] = $Signature;
            $results['UploadCoachingRecipientSignatureOld'] = $upload;
        } else {
            $results['success'] = false;
            $results['message'] = lang("Failed to save data");
        }
        return $results;
    }

    public function UpdateCoaching($paramPost) {
        $this->db->trans_start();

        //buang var yg tidak perlu (begin)
        $CoachingID = $paramPost['CoachingID'];

        $dataPostCoaching["IMSID"]              = $paramPost["IMSID"];
        $dataPostCoaching["isCertified"]        = $paramPost["isCertified"];

        //update
        $this->db->where('CoachingID', $CoachingID);
        $query = $this->db->update('ktv_ims_farmer_coaching', $dataPostCoaching);
        
        $dataPost['CoachingRecipient']  = $paramPost['CoachingRecipient'];
        $dataPost['FarmerWorkerName']   = $paramPost['FarmerWorkerName'];
        $dataPost['EventDate']          = $paramPost['CoachingDate'];
        $dataPost['TimeStart']          = $paramPost['TimeStart'];
        $dataPost['TimeEnd']            = $paramPost['TimeEnd'];
        $dataPost['Comment']            = $paramPost['Remark'];
        $dataPost['Sample_pH1']         = $paramPost['PhSample1'];
        $dataPost['Sample_pH2']         = $paramPost['PhSample2'];
        $dataPost['Sample_pH3']         = $paramPost['PhSample3'];
        $dataPost['DateUpdated']        = date('Y-m-d H:i:s');
        $dataPost['LastModifiedBy']     = $_SESSION['userid'];

        $this->db->where('CoachingID', $CoachingID);
        $query = $this->db->update('ktv_ims_farmer_coaching_activity', $dataPost);

        //Koordinat Geometry ============= (Begin)
        if ($paramPost["Latitude"] != "" && $paramPost["Longitude"] != "") {
            $PointInsert = "POINT({$paramPost["Latitude"]} {$paramPost["Longitude"]})";

            $sql = "UPDATE ktv_ims_farmer_coaching_activity a SET
                        a.`LatLong` = ST_GEOMFROMTEXT('$PointInsert', 4326)
                    WHERE
                        a.`CoachingID` = ?
                    LIMIT 1";
            $p = array($CoachingID);
            $query = $this->db->query($sql, $p);
        }
        //Koordinat Geometry ============= (End)

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = lang("Data saved");
            $results['CoachingID'] = $CoachingID;
        } else {
            $results['success'] = false;
            $results['message'] = lang("Failed to save data");
        }
        return $results;
    }
    
    public function DeleteCoaching($CoachingID) {
        $sql = "UPDATE ktv_ims_farmer_coaching a SET
                    a.`StatusCode` = 'nullified',
                    a.`LastModifiedBy` = ?,
                    a.`DateUpdated` = NOW()
                WHERE
                    a.`CoachingID` = ?
                LIMIT 1";
        $query = $this->db->query($sql, array($_SESSION['userid'], $CoachingID));

        if ($query) {
            $return['success'] = true;
            $return['message'] = lang('Data Deleted');
        } else {
            $return['success'] = false;
            $return['message'] = lang('Failed to delete data');
        }
        return $return;
    }

    public function GetComboCoachingTopic($CategoryID = null) {
        $sql = "SELECT
                    a.`TopicID` AS id
                    , a.`Topic` AS label
                FROM
                    `ktv_coaching_topic` a
                WHERE
                    a.`StatusCode` = 'active'
                AND
                    a.Category = ?
                ORDER BY a.`Topic` ASC";

        $data = $this->db->query($sql, array($CategoryID))->result_array();

        $return['data'] = $data;
        $return['success'] = true;
        return $return;
    }

    public function GetComboCoachingSubTopic($CoachingTopicID) {
        $sql = "SELECT
                    a.`SubtopicID` AS id
                    , a.`Subtopic` AS label
                FROM
                    `ktv_coaching_subtopic` a
                WHERE
                    a.`StatusCode` = 'active'
                    AND a.`TopicID` = ? 
                    OR a.`SubtopicID` = 0
                ORDER BY a.`SubtopicID` DESC";
        
        $p = array(
            (int) $CoachingTopicID
        );

        $data = $this->db->query($sql, $p)->result_array();

        $return['data'] = $data;
        $return['success'] = true;
        return $return;
    }

    public function GetComboCoachingFinding($SubtopicID, $UrgentlyStatus) {
        $sql = "SELECT
                    a.`CoachingFindingID` AS id
                    , a.`CoachingFinding` AS label
                FROM
                    `ktv_coaching_finding` a
                WHERE
                    a.`StatusCode` = 'active'
                    AND a.`SubtopicID` = ? 
                    AND a.`UrgentlyStatus` = ?
                ORDER BY a.`SubtopicID` DESC";
        
        $p = array(
            (int) $SubtopicID,
            (int) $UrgentlyStatus
        );

        $data = $this->db->query($sql, $p)->result_array();

        $return['data'] = $data;
        $return['success'] = true;
        return $return;
    }

    public function GetComboCoachingRecomm($SubtopicID) {
        $sql = "SELECT
                    a.`CoachingRecomID` AS id
                    , a.`CoachingRecom` AS label
                FROM
                    `ktv_coaching_recommendation` a
                WHERE
                    a.`StatusCode` = 'active'
                    AND a.`SubtopicID` = ?
                ORDER BY a.`SubtopicID` DESC";
        
        $p = array(
            (int) $SubtopicID
        );

        $data = $this->db->query($sql, $p)->result_array();

        $return['data'] = $data;
        $return['success'] = true;
        return $return;
    }

    public function CoachingTaskFormOpen($ActivityNCID) {
        $return = array();

        $sql = "SELECT
                    a.ActivityNCID
                    , a.ActivityID
                    , a.Topic
                    , a.Subtopic
                    , a.CategoryID
                    , a.UrgentlyStatus
                    , a.Finding
                    , a.FindingOtherText
                    , a.ActivityType
                    , a.Recommendation
                    , a.RecomOtherText
                    , a.Target
                    , a.FollowupStatus
                    , a.Deadline
                    , a.ActNCStatus
                    , a.Explanation
                FROM
                    `ktv_ims_farmer_coaching_activity_nc` a
                WHERE 1=1 
                    AND a.ActivityNCID = ?
                LIMIT 1";
        $p = array(
            $ActivityNCID
        );
        $data = $this->db->query($sql, $p)->row_array();

        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.Coaching.WinFormCoachingTask-Form-" . $key;
            $dataRow[$keyNew] = $value;
        }

        $return['success'] = true;
        $return['data'] = $dataRow;
        return $return;
    }

    public function CekDuplikatTask($ActivityID, $Topic) {
        $ActivityID = (int) $ActivityID;
        $Topic = (int) $Topic;

        $sql = "SELECT
                    a.`ActivityID`
                    , a.`Topic`
                FROM
                    ktv_ims_farmer_coaching_activity_nc a
                WHERE
                    a.`ActivityID` = ?
                    AND a.Topic = ?
		";
        $data = $this->db->query($sql, array($CoachingID, $Topic))->row_array();
        if (isset($data['Topic'])) {
            return true;
        } else {
            return false;
        }
    }

    public function InsertTask($paramPost) {
        $results = array();

        //Add Parameter yang diperlukan
        $uid = uidGenerateCode();
        $paramPost["uid"]           = $uid;
        $paramPost["CreatedBy"]     = $_SESSION["userid"];
        $paramPost["DateCreated"]   = date("Y-m-d H:i:s");
        $paramPost["StatusCode"]    = "active";
        //Add Parameter yang diperlukan

        //Buang parameter yang tidak perlu
        unset($paramPost["OpsiDisplay"]);
        //Buang parameter yang tidak perlu

        $this->db->trans_begin();

        $query = $this->db->insert("ktv_ims_farmer_coaching_activity_nc", $paramPost);

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
    }

    public function UpdateTask($paramPost) {

        //Add Parameter yang diperlukan
        $paramPost["LastModifiedBy"]    = $_SESSION["userid"];
        $paramPost["DateUpdated"]       = date("Y-m-d H:i:s");
        $ActivityNCID = $paramPost["ActivityNCID"];
        //Add Parameter yang diperlukan

        //Buang parameter yang tidak perlu
        unset($paramPost["OpsiDisplay"]);
        unset($paramPost["ActivityNCID"]);
        unset($paramPost["ActivityID"]);        
        //Buang parameter yang tidak perlu

        $results = array();
        $this->db->trans_begin();

        $this->db->where("ActivityNCID",$ActivityNCID);
        $query = $this->db->update("ktv_ims_farmer_coaching_activity_nc", $paramPost);

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
    }
    
    public function DeleteTask($ActivityNCID) {
        $this->db->trans_start();

        $sql = "SELECT * FROM ktv_ims_farmer_coaching_activity_nc
    		WHERE
                    `ActivityNCID` = ?
                LIMIT 1";
        $DataTask = $this->db->query($sql, array($ActivityNCID))->row_array();

        //Tambah Param
        $DataTask['DateHistory'] = date('Y-m-d');
        $DataTask['DeletedBy'] = $_SESSION['userid'];

        unset($DataTask["CreatedBy"]);
        unset($DataTask["CreatedBy"]);

        //insert
        $this->db->insert('his_ims_farmer_coaching_activity_nc', $DataTask);

        $sql = "DELETE FROM ktv_ims_farmer_coaching_activity_nc WHERE
                    `ActivityNCID` = ?
		LIMIT 1";
        $query = $this->db->query($sql, array($ActivityNCID));

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Data deleted";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete data";
        }
        return $results;
    }

    public function GetComboCategory() {
        $data[0]["id"]      = "1";
        $data[0]["label"]   = lang("Agronomy");
        $data[1]["id"]      = "2";
        $data[1]["label"]   = lang("Non Agronomy");

        $return['data'] = $data;
        $return['success'] = true;
        return $return;
    }

}
