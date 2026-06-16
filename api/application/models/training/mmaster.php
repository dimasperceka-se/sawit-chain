<?php

class Mmaster extends CI_Model {
    public $user;

    public function __construct()
    {
        parent::__construct();
        $this->user = $this->muserprofile->getUserProfile();
    }
    function readDatas($pSearch, $start, $limit, $userid) {
        $userID = $_SESSION['userid'];
        $PartnerID = $_SESSION['PartnerID'];
        $sqll = "
            SELECT PartnerID FROM ktv_program_staff WHERE UserId=?";
        $queryy = $this->db->query($sqll, array($userID));
        $resultt = $queryy->result_array();

        //buat SqlHakAksesKontrol (begin)
        if($_SESSION['is_admin'] == "1"){
            $sqlHakAkses = " ";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            //cek ktv_access_staff
            $sqlHakAkses = " AND f.DistrictID IN (".$_SESSION['daerah_access'].")";
        } else {
            //cek ktv_access_staff
            $sqlHakAkses = " AND f.DistrictID IN (".$_SESSION['daerah_access'].")";
        }
        //buat SqlHakAksesKontrol (end)

        //========== Search (Begin) =====================
        $where = "";
        if($pSearch['ArrFilter'] != "") {
            $ArrTmp = explode(',',$pSearch['ArrFilter']);
            for ($i=0; $i < count($ArrTmp); $i++) { 
                switch($ArrTmp[$i]){
                    case 'id':
                        $where = $where." AND a.`MasterTrainingID` LIKE '%{$pSearch['TextFilterID']}%' ";
                    break;
                    case 'name':
                        $where = $where." AND `CpgTrainings` LIKE '%{$pSearch['TextFilterName']}%' ";
                    break;
                    case 'region':
                        $where = $where."   AND ( (f.ProvinceID = {$pSearch['CmbFilterProvince']}) OR (0={$pSearch['CmbFilterProvince']}) )
                                                    AND ( (a.DistrictID = {$pSearch['CmbFilterDistrict']}) OR (0={$pSearch['CmbFilterDistrict']}) ) ";
                    break;
                }
            }
        }
        //========== Search (End) =====================
        if (!empty($this->user['district_access'])) {
            $where .= " AND a.DistrictID IN ({$this->user['district_access']})";
        }
        $sql = "SELECT 
                    SQL_CALC_FOUND_ROWS
                    a.MasterTrainingID AS id,CpgTrainings AS training,
                    District AS tot,(SELECT COUNT(DISTINCT aa.MasterTrainingsStaffID) FROM ktv_master_trainings_participants aa WHERE aa.MasterTrainingID=a.MasterTrainingID AND aa.StatusCode='active') AS participant,DATE(TrainingStart) AS START,DATE(TrainingEnd) AS END,
                    TrainingDays AS days
                FROM ktv_master_trainings a
                LEFT JOIN ktv_cpg_trainings b ON a.CPGtrainingsID=b.CpgTrainingsID
                LEFT JOIN ktv_district f ON f.DistrictID=a.DistrictID
                WHERE 
                    a.StatusCode != 'nullified' 
                    $where
                    $sqlHakAkses
                GROUP BY a.MasterTrainingID ORDER BY a.MasterTrainingID
                LIMIT ?,?";

        /*$query = $this->db->query(sprintf($sql, 'a.MasterTrainingID as id,CpgTrainings as training,BatchNumber as batch,g.`PartnerName` as partner_name,
            District as tot,count(distinct MasterTrainingsStaffID) as participant,date(TrainingStart) as start,date(TrainingEnd) as end,
            TrainingDays as days', 'GROUP BY a.MasterTrainingID ORDER BY a.MasterTrainingID LIMIT ?,?'), array("%$key%", $prov, (int) $start, (int) $limit, $userid));*/
        $query = $this->db->query($sql, array((int) $start, (int) $limit, $userid));
        $result['data'] = $query->result_array();
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $total = $query->result_array();
        $result['total'] = $total[0]['total'];
        return $result;
    }

    function readData($id) {
        $sql = "
            SELECT *,DATE(TrainingStart) AS TrainingStart,DATE(TrainingEnd) AS TrainingEnd,
            GROUP_CONCAT(SubCpgTrainingsID SEPARATOR ',') AS subtopics
            FROM ktv_master_trainings a
            LEFT JOIN ktv_province b ON a.TrainingProvince=b.ProvinceID
            LEFT JOIN ktv_master_trainings_sub_topics c ON a.MasterTrainingID = c.MasterTrainingID
            WHERE a.MasterTrainingID=?";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

    function createData($training, $staf, $start, $end, $days, $batch, $tot, $staf_mitra, $provinsi, $district, $service_provider_id, $service_provider_staff, $userid,$CpgTrainingsIDSubTopic,$TrainingDayStatus,$TrainingPurpose) {
        $sql = "
            insert into ktv_master_trainings (CPGtrainingsID, FacProgramPersonID, FacProgramStaffID, TrainingStart, TrainingEnd,
	           TrainingDays, CpgBatchID, TrainingProvince,DistrictID, TotLocation,FacPrivatePersonID, FacPartnerStaffID, ServiceProvID, ServiceProvStaffName, TrainingDayStatus,DateCreated,CreatedBy,DateUpdated,LastModifiedBy,TrainingPurpose)
            SELECT ?,?,?,?,?,?,?,ProvinceID,?,?,?,?,?,?,?,now(),?,now(),?,? FROM ktv_province WHERE Province=?";
        $stat = $this->db->query($sql, array($training, $staf, $staf, $start, $end, $days, $batch, $district, $tot, $staf_mitra, $staf_mitra, $service_provider_id, $service_provider_staff, $TrainingDayStatus, $userid,$userid, $TrainingPurpose, $provinsi));
        if ($stat) {
            $MasterTrainingID = $this->db->insert_id();

            //sub topic (begin)
            if($CpgTrainingsIDSubTopic[0] != ""){
                foreach ($CpgTrainingsIDSubTopic as $key => $value) {
                    $sql="INSERT INTO `ktv_master_trainings_sub_topics` SET
                          `MasterTrainingID` = ?,
                          `SubCpgTrainingsID` = ?,
                          `DateCreated` = NOW(),
                          `CreatedBy` = ?";
                    $p = array(
                        $MasterTrainingID,
                        $value,
                        $userid
                    );
                    $query = $this->db->query($sql,$p);
                }
            }
            //sub topic (end)

            $results['success'] = true;
            $results['TrainMasterID'] = $MasterTrainingID;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateData($training, $staf, $start, $end, $days, $batch, $provinsi, $district, $tot, $mitra, $service_provider_id, $service_provider_staff, $id, $userid,$CpgTrainingsIDSubTopic,$TrainingDayStatus,$TrainingPurpose) {

        $sql = "UPDATE ktv_master_trainings
            SET CPGtrainingsID=?, FacProgramPersonID=?, FacProgramStaffID=?, TrainingStart=?, TrainingEnd=?, TrainingDays=?, CpgBatchID=?,
               TrainingProvince=(SELECT ProvinceID FROM ktv_province WHERE Province=?), DistrictID=?,TotLocation=?,
               FacPrivatePersonID=?,FacPartnerStaffID=?,ServiceProvID=?, ServiceProvStaffName=?,DateUpdated=now(), LastModifiedBy=?,TrainingDayStatus=?,TrainingPurpose=?
            WHERE MasterTrainingID=?";
        $stat = $this->db->query($sql, array($training, $staf, $staf, $start, $end, $days, $batch, $provinsi, $district, $tot, $mitra,$mitra, $service_provider_id, $service_provider_staff, $userid, $TrainingDayStatus,$TrainingPurpose, $id));

        if ($stat) {
            //sub topics (begin)
            $sql="DELETE FROM ktv_master_trainings_sub_topics WHERE MasterTrainingID = ?";
            $query = $this->db->query($sql,array($id));

            if($CpgTrainingsIDSubTopic[0] != ""){
                foreach ($CpgTrainingsIDSubTopic as $key => $value) {
                    $sql="INSERT INTO `ktv_master_trainings_sub_topics` SET
                          `MasterTrainingID` = ?,
                          `SubCpgTrainingsID` = ?,
                          `DateCreated` = NOW(),
                          `CreatedBy` = ?";
                    $p = array(
                        $id,
                        $value,
                        $userid
                    );
                    $query = $this->db->query($sql,$p);
                }
            }
            //sub topics (end)

            $results['success'] = true;
            $results['TrainMasterID'] = $id;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deleteData($id) {
        /*$sql = "DELETE FROM ktv_master_trainings WHERE MasterTrainingID=?";
        $sql_participant = "DELETE FROM ktv_master_trainings_participants WHERE MasterTrainingID=?";
        $stat = $this->db->query($sql_participant, array($id));
        $stat = $this->db->query($sql, array($id));*/
        $sql = "UPDATE ktv_master_trainings SET StatusCode = 'nullified',LastModifiedBy='".$_SESSION['userid']."',DateUpdated=NOW() WHERE MasterTrainingID = ? LIMIT 1";
        $stat = $this->db->query($sql, array($id));
        if ($stat) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    function readParticipants($id, $start, $limit) {
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS
                MasterTrainingsStaffID as participant_id,
                a.ParticipantPersonID as id_staff,
                b.PersonNm as staf,a.WritingAwal as wstart,
                a.WritingAkhir as wend,a.BallotAwal as bstart,a.BallotAkhir as bend
            FROM ktv_master_trainings_participants a
            JOIN ktv_persons b on a.ParticipantPersonID=b.PersonID
            WHERE MasterTrainingID=? AND a.StatusCode != 'nullified'
            ORDER BY b.PersonNm";
        $query = $this->db->query(sprintf($sql, '', 'LIMIT ?,?'), array($id, (int) $start, (int) $limit));
        $result['data'] = $query->result_array();
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;
        return $result;
    }

    function createParticipant($training, $staf, $wstart, $wend, $bstart, $bend, $userid) {
        $sql = "
            insert into ktv_master_trainings_participants (MasterTrainingID, ParticipantPersonID,ParticipantNewStaffID,
               WritingAwal, WritingAkhir, BallotAwal, BallotAkhir, DateCreated,CreatedBy,DateUpdated,LastModifiedBy)
            VALUES (?,?,?,?,?,?,?,now(),?,now(),?)";
        $stat = $this->db->query($sql, array($training, $staf, $staf, $wstart, $wend, $bstart, $bend, $userid, $userid));
        if ($stat) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateParticipant($training, $staf, $wstart, $wend, $bstart, $bend, $id, $userid) {
        $sql = "
            UPDATE ktv_master_trainings_participants
            SET ParticipantPersonID=?,ParticipantNewStaffID=?, WritingAwal=?, WritingAkhir=?, BallotAwal=?, BallotAkhir=?, DateUpdated=now(),
               LastModifiedBy=?
            WHERE MasterTrainingsStaffID=?";
        $stat = $this->db->query($sql, array($staf, $staf, $wstart, $wend, $bstart, $bend, $userid, $id));
        if ($stat) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deleteParticipant($id) {
        //$sql = "DELETE FROM ktv_master_trainings_participants WHERE MasterTrainingsStaffID=?";
        $sql="UPDATE ktv_master_trainings_participants SET StatusCode = 'nullified',LastModifiedBy='".$_SESSION['userid']."',DateUpdated=NOW() WHERE MasterTrainingsStaffID = ? LIMIT 1";
        $stat = $this->db->query($sql, array($id));
        if ($stat) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    function readStaffs($key = '') {
        $sql="SELECT
    id,
    label
FROM
(
SELECT
    b.`PersonID` AS id,
    CONCAT(
        '[',srol.`RoleShortCode`,'] ',
        b.`PersonNm`,
        ' (',
        IFNULL(rpos.`PositionName`,'-'),', ',
        IFNULL(CASE a.`ObjType`
            WHEN 'bank' THEN
                (
                    SELECT
                        CONCAT(sub_b.`BankName`,' - ',sub_a.`BranchName`)
                    FROM
                        ktv_bank_branch sub_a
                        LEFT JOIN ktv_bank sub_b ON sub_a.`BranchBankID` = sub_b.`BankID`
                    WHERE
                        sub_a.BranchID = a.`ObjID`
                    LIMIT 1
                )
            WHEN 'cooperative' THEN
                (
                    SELECT
                        CONCAT(sub_a.`Status`,' - ',sub_a.`CoopName`)
                    FROM
                        ktv_cooperatives sub_a
                    WHERE
                        sub_a.CoopID = a.`ObjID`
                    LIMIT 1
                )
            WHEN 'farmergroup' THEN
                (
                    SELECT
                        CONCAT(sub_a.`CPGid`,' - ',sub_a.`GroupName`)
                    FROM
                        ktv_cpg sub_a
                    WHERE
                        sub_a.CPGid = a.`ObjID`
                    LIMIT 1
                )
            WHEN 'extension' THEN
                (
                    SELECT
                        InstiName
                    FROM
                        ktv_ref_institution
                    WHERE
                        InstiId = a.`ObjID`
                    LIMIT 1
                )
            WHEN 'private' THEN
                (
                    SELECT
                        PartnerName
                    FROM
                        ktv_program_partner
                    WHERE
                        PartnerID = a.`ObjID`
                    LIMIT 1
                )
            WHEN 'program' THEN
                (
                    SELECT
                        PartnerName
                    FROM
                        ktv_program_partner
                    WHERE
                        PartnerID = a.`ObjID`
                    LIMIT 1
                )
            WHEN 'sce' THEN
                (
                    SELECT
                        CONCAT(sub_b.`FarmerID`,' - ',sub_b.`FarmerName`)
                    FROM
                        sce_farmer sub_a
                        INNER JOIN ktv_farmer sub_b ON sub_a.`FarmerID` = sub_b.`FarmerID`
                    WHERE
                        sub_a.SceID = a.`ObjID`
                    LIMIT 1
                )
            WHEN 'trader' THEN
                (
                    SELECT
                        sub_a.`TraderName`
                    FROM
                        ktv_traders sub_a
                    WHERE
                        sub_a.TraderID = a.`ObjID`
                    LIMIT 1
                )
            WHEN 'warehouse' THEN
                (
                    SELECT
                        sub_a.`WarehouseName`
                    FROM
                        ktv_warehouse sub_a
                    WHERE
                        sub_a.WarehouseID = a.`ObjID`
                    LIMIT 1
                )
            WHEN 'service' THEN
                (
                    SELECT
                        sub_a.ServiceProvName
                    FROM
                        ktv_service_provider sub_a
                    WHERE
                        sub_a.ServiceProvID = a.`ObjID`
                    LIMIT 1
                )
            WHEN 'mill' THEN
                (
                    SELECT
                        sub_a.MillName
                    FROM
                        ktv_mill sub_a
                    WHERE
                        sub_a.MillID = a.`ObjID`
                    LIMIT 1
                )
            WHEN 'agent' THEN
                (
                    SELECT
                        sub_a.MemberName
                    FROM
                        ktv_members sub_a
                    WHERE
                        sub_a.MemberID = a.`ObjID`
                    LIMIT 1
                )
        END,'-'),
        ')'
    ) AS label
FROM
    ktv_staffs a
    INNER JOIN ktv_persons b ON a.`PersonID` = b.`PersonID`
    INNER JOIN sys_role srol ON a.`ObjType` = srol.`RoleCode`

    LEFT JOIN ktv_staff_positions c ON a.`StaffID` = c.`StaffPosStaffID`
        AND (CURDATE() BETWEEN c.`StaffPostStart` AND c.`StaffPostEnd`)
        AND c.StatusCode = 'active'
    LEFT JOIN ktv_ref_position_type rpos ON c.StaffPosPositionID = rpos.PositionID
WHERE
    a.`StatusCode` = 'active'
    AND srol.RoleMasterTrainingParticipant = '1'
) AS tbl_sel
WHERE
    label LIKE ?
    -- where_login_wags --
ORDER BY label";

        //Cek login wags ================== (Begin)
        $sql_where_login_wags = "";
        if($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program") {
            if($_SESSION['PartnerID'] == "14") {
                
                $sql_cek_wags = "SELECT
                            GROUP_CONCAT(DISTINCT tg.PersonID SEPARATOR ',') AS id_person
                        FROM
                        (
                        SELECT
                            p.`PersonID`
                        FROM
                            ktv_persons p
                            LEFT JOIN ktv_staffs s ON p.`PersonID` = s.`PersonID` AND s.`ObjType` = 'agent'
                            LEFT JOIN ktv_members m ON s.`ObjID` = m.`MemberID`
                        WHERE
                            m.`PartnerID` = 14
                            
                        UNION
                        
                        SELECT
                            p.`PersonID`
                        FROM
                            ktv_persons p
                            LEFT JOIN ktv_staffs s ON p.`PersonID` = s.`PersonID` AND s.`ObjType` = 'mill'
                            LEFT JOIN ktv_mill m ON s.`ObjID` = m.`MillID`
                        WHERE
                            m.`PartnerID` = 14
                        
                        UNION

                        SELECT
                            p.`PersonID`
                        FROM
                            ktv_persons p
                            LEFT JOIN ktv_staffs s ON p.`PersonID` = s.`PersonID` AND s.`ObjType` = 'private'
                        WHERE
                            s.`ObjID` = 14
                        ) AS tg";
                $CekDataWags = $this->db->query($sql_cek_wags)->row_array();
                if(isset($CekDataWags['id_person'])){
                    $sql_where_login_wags = " AND id IN ({$CekDataWags['id_person']}) ";
                }
            }
        }
        //Cek login wags ================== (End)

        $sql = str_replace("-- where_login_wags --",$sql_where_login_wags,$sql);
        $query = $this->db->query($sql, array("%$key%"));
        $result['data'] = $query->result_array();
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        return $result;
    }

    /*function readStaffs($key = '') {
        $sql = "
            SELECT * FROM (
               SELECT a.PersonID as id, concat(StaffID,' - ',a.PersonNm,' (PS)') as label,StaffID as StaffId,null as ExtensionId
               FROM ktv_persons a
               LEFT JOIN ktv_program_staff b on a.PersonID=b.PersonID
               UNION
               SELECT ExtensionID as id, concat(ExtensionID,' - ',StaffName,' (PU)') as label, null as StaffId, ExtensionId as ExtensionId
               FROM ktv_extension_staff
               UNION
               SELECT PrivateStaffID as id, concat(PrivateStaffID,' - ',StaffName,' (PR)') as label, null as StaffId, null as ExtensionId
               FROM ktv_private_staff
            ) a
            WHERE label like ?
            ORDER BY label";
        $query = $this->db->query($sql, array("%$key%"));
        $result['data'] = $query->result_array();
        return $result;
    }*/

    function check($training, $farmer) {
        $sql = "
            SELECT ParticipantStaffID as id FROM ktv_master_trainings_participants WHERE MasterTrainingID=? and ParticipantPersonID=? AND StatusCode!='nullified'";
        $query = $this->db->query($sql, array($training, $farmer));
        $result = $query->result_array();
        if ($result[0]['id'] != '')
            $res['data'] = FALSE;
        else
            $res['data'] = TRUE;
        return $res;
    }

    function readParticipantsTraining($key) {
        $sql = "
            SELECT
                SQL_CALC_FOUND_ROWS
                b.PersonNm AS PersonNm,
                b.Gender AS Gender,
                st_c.`PositionName` AS jabatan
                , s_work.`WorkAreaName` AS Kabupaten
            FROM
                ktv_master_trainings_participants a
                LEFT JOIN ktv_persons b ON a.ParticipantPersonID=b.PersonID
                LEFT JOIN ktv_staffs st_a ON b.`PersonID` = st_a.`PersonID`
                LEFT JOIN ktv_staff_positions st_b ON
                    st_a.`StaffID` = st_b.`StaffPosStaffID`
                    AND CURDATE() BETWEEN st_b.`StaffPostStart` AND st_b.`StaffPostEnd`
                    AND st_b.`StatusCode` = 'active'
                LEFT JOIN ktv_ref_position_type st_c ON st_b.`StaffPosPositionID` = st_c.`PositionID`
                LEFT JOIN ktv_program_staff c ON b.PersonID=c.PersonID
                LEFT JOIN ktv_extension_staff d ON a.ParticipantStaffID=d.ExtensionID
                LEFT JOIN ktv_private_staff e ON a.ParticipantStaffID=e.PrivateStaffID
                LEFT JOIN ktv_program_partner e_partner ON e.`PartnerID` = e_partner.`PartnerID`
                LEFT JOIN ktv_bank_branch_staff bs ON b.`PersonID` = bs.`PersonID`
                LEFT JOIN ktv_ref_position_type ref_pos ON bs.`PositionID` = ref_pos.`PositionID`
                LEFT JOIN ktv_cooperative_staff cs ON b.`PersonID` = cs.`PersonID` AND cs.`StatusCode`='active'
                LEFT JOIN ktv_cooperatives cs_coop ON cs.`CoopID` = cs_coop.`CoopID`
                LEFT JOIN ktv_ref_work_area s_work ON st_a.`WorkAreaID` = s_work.`WorkAreaID`
			WHERE MasterTrainingID=? and IFNULL(e.StaffName, IFNULL(d.StaffName, b.PersonNm)) is not null and a.`StatusCode`!='nullified'
            ORDER BY PersonNm";
        $query = $this->db->query($sql, array($key));
        $result['data'] = $query->result_array();
        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;
        return $result;
    }

    function readTraining($id) {
        $sql = "
            SELECT i.CpgTrainings, a.MasterTrainingID,d.PersonNm as koordinator,fp.PersonNm as private_staff, a.TotLocation,
               b.Province as Provinsi,a.DistrictID, f.PartnerName as Partner,
				g.PartnerName as PrivateStaffPartner, h.BatchNumber, DATE_FORMAT(a.TrainingStart,'%d - %b - %Y') TrainingStart,
				DATE_FORMAT(a.TrainingEnd,'%d - %b - %Y') TrainingEnd
                ,ServiceProvName
                ,ServiceProvStaffName
                ,GROUP_CONCAT(subtop.`CpgTrainings` SEPARATOR ', ') AS Subtopics
                ,a.TrainingDayStatus
            from ktv_master_trainings a
            left join ktv_province b ON a.TrainingProvince=b.ProvinceID
            LEFT JOIN ktv_persons d ON d.PersonID=a.FacProgramPersonID
            LEFT JOIN ktv_program_staff c ON d.PersonID = c.PersonID
            LEFT JOIN ktv_persons fp ON fp.PersonID=a.FacPrivatePersonID
            left join ktv_private_staff e ON e.PersonID = fp.PersonID
            LEFT JOIN ktv_program_partner f ON f.PartnerID=c.PartnerId
			LEFT JOIN ktv_program_partner g ON g.PartnerID=e.PartnerID
			left join ktv_cpg_batch h on a.CpgBatchID=h.CpgBatchID
			left join ktv_cpg_trainings i on a.CPGtrainingsID=i.CpgtrainingsID
            LEFT JOIN ktv_service_provider sp ON sp.ServiceProvID = a.ServiceProvID
            LEFT JOIN ktv_master_trainings_sub_topics sub ON a.`MasterTrainingID` = sub.`MasterTrainingID`
            LEFT JOIN ktv_cpg_trainings subtop ON sub.`SubCpgTrainingsID` = subtop.`CpgTrainingsID`
            WHERE a.MasterTrainingID=?";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

    function readPartnerLogo($id) {
        $sql = "
            select distinct c.Photo
            from ktv_master_trainings a,
            ktv_cpg_batch b,
            ktv_program_partner c
            where a.CpgBatchID = b.CpgBatchID
            and b.PartnerID = c.PartnerID
            and a.MasterTrainingID =? ";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

    public function getServiceProvider()
    {
        //Cek kalau login Wild Asia
        $SqlAccess = "";
        if($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            if($_SESSION['PartnerID'] == "14") { //WildAsia
                $SqlAccess = " AND p.PartnerID IN (1,14) ";
            }
        }

        $sql = "
SELECT
    p.PartnerID AS id
    , p.PartnerName AS label
FROM ktv_program_partner p
WHERE
    p.StatusCode = 'active'
    $SqlAccess
ORDER BY label
        ";
        $query = $this->db->query($sql, array($var));
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }
}

?>
