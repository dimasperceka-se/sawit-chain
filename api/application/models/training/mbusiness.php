<?php

class Mbusiness extends CI_Model {
    public $user;

    public function __construct()
    {
        parent::__construct();
        $this->user = $this->muserprofile->getUserProfile();
    }
    function readDatas($key, $prov, $dist, $subdist, $start, $limit, $userid) {
        $userID = $_SESSION['userid'];
        $PartnerID = $_SESSION['PartnerID'];
        $sqll = "
            SELECT PartnerID FROM ktv_program_staff WHERE UserId=?";
        $queryy = $this->db->query($sqll, array($userID));
        $resultt = $queryy->result_array();
        $wh = '';
        if ($resultt[0]['PartnerID'] == '1' OR $userID == '1')
            $wh = ' OR a.CPGtrainingsID is not null';
        if ($PartnerID)
            $wh .= " OR c.`PartnerID` = {$PartnerID}";
        $where = '';
        if (!empty($prov)) {
            $where .= " AND TrainingProvinceID = {$prov}";
        }
        if (!empty($dist)) {
            $where .= " AND a.TrainingDistrictID = {$dist}";
        }
        if (!empty($this->user['district_access'])) {
            $where .= " AND a.TrainingDistrictID IN ({$this->user['district_access']})";
        }
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS
                a.BsnTrainingID as id,CpgTrainings as training,BatchNumber as batch,g.`PartnerName` as partner_name,
                District as tot,(SELECT COUNT(DISTINCT aa.PartStaffID) FROM ktv_business_trainings_participants aa WHERE aa.BsnTrainingID=a.BsnTrainingID AND aa.StatusCode='active') as participant,date(TrainingStart) as start,date(TrainingEnd) as end,
                TrainingDays as days
            from ktv_business_trainings a
            left join ktv_cpg_trainings b on a.CPGtrainingsID=b.CpgTrainingsID
            left join ktv_cpg_batch c on a.CpgBatchID=c.CpgBatchID
            #left join ktv_business_trainings_participants d on a.BsnTrainingID=d.BsnTrainingID
            LEFT JOIN ktv_private_staff e ON e.PartnerID=c.PartnerID
            LEFT JOIN ktv_district f ON f.DistrictID=a.TrainingDistrictID
            LEFT JOIN `ktv_program_partner` g ON c.`PartnerID` = g.`PartnerID`
            WHERE a.StatusCode != 'nullified' AND CpgTrainings like ?
            AND (f.DistrictID IN (
               SELECT DistrictID FROM ktv_district a, ktv_private_staff b
               WHERE a.PartnerID=b.PartnerID AND b.UserID=$userID
               UNION
               SELECT DistrictID FROM ktv_district a, ktv_program_staff b
               WHERE a.PartnerID=b.PartnerID AND b.UserID=$userID )
               $wh)
               $where
            GROUP BY a.BsnTrainingID ORDER BY a.BsnTrainingID LIMIT ?,?";
        /*$query = $this->db->query(sprintf($sql, 'a.BsnTrainingID as id,CpgTrainings as training,BatchNumber as batch,g.`PartnerName` as partner_name,
            District as tot,count(distinct PartStaffID) as participant,date(TrainingStart) as start,date(TrainingEnd) as end,
            TrainingDays as days', 'GROUP BY a.BsnTrainingID ORDER BY a.BsnTrainingID LIMIT ?,?'), array("%$key%", $prov, (int) $start, (int) $limit, $userid));*/
        $query = $this->db->query($sql, array("%$key%", (int) $start, (int) $limit, $userid));
        $result['data'] = $query->result_array();
        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $total = $query->result_array();
        $result['total'] = $total[0]['total'];
        return $result;
    }

    function readData($id) {
        $sql = "
            SELECT a.*,t.CpgTrainings AS label,DATE(TrainingStart) AS TrainingStart,DATE(TrainingEnd) AS TrainingEnd,
            GROUP_CONCAT(SubCpgTrainingsID SEPARATOR ',') AS subtopics
            FROM ktv_business_trainings a
            LEFT JOIN ktv_cpg_trainings t ON t.CpgTrainingsID = a.CPGtrainingsID
            LEFT JOIN ktv_province b ON a.TrainingProvinceID=b.ProvinceID
            LEFT JOIN ktv_business_trainings_sub_topics c ON a.BsnTrainingID = c.BsnTrainingID
            WHERE a.BsnTrainingID=?";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

    function createData($training, $staf, $start, $end, $days, $batch, $tot, $staf_mitra, $provinsi, $district, $service_provider_id, $service_provider_staff, $userid,$CpgTrainingsIDSubTopic,$TrainingDayStatus) {
        $sql = "
            INSERT INTO ktv_business_trainings (CPGtrainingsID, FacProgramStaffID, TrainingStart, TrainingEnd,
	           TrainingDays, CpgBatchID, TrainingProvinceID,TrainingDistrictID, TotLocation,FacPartnerStaffID, ServiceProvID, ServiceProvStaffName, TrainingDayStatus,DateCreated,CreatedBy,DateUpdated,LastModifiedBy)
            SELECT ?,?,?,?,?,?,ProvinceID,?,?,?,?,?,?,now(),?,now(),? FROM ktv_province WHERE Province=?";
        $stat = $this->db->query($sql, array($training, $staf, $start, $end, $days, $batch, $district, $tot, $staf_mitra, $service_provider_id, $service_provider_staff, $TrainingDayStatus, $userid,$userid, $provinsi));
        if ($stat) {
            $BsnTrainingID = $this->db->insert_id();

            //sub topic (begin)
            if($CpgTrainingsIDSubTopic[0] != ""){
                foreach ($CpgTrainingsIDSubTopic as $key => $value) {
                    $sql="INSERT INTO `ktv_business_trainings_sub_topics` SET
                          `BsnTrainingID` = ?,
                          `SubCpgTrainingsID` = ?,
                          `DateCreated` = NOW(),
                          `CreatedBy` = ?";
                    $p = array(
                        $BsnTrainingID,
                        $value,
                        $userid
                    );
                    $query = $this->db->query($sql,$p);
                }
            }
            //sub topic (end)

            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateData($training, $staf, $start, $end, $days, $batch, $provinsi, $district, $tot, $mitra, $service_provider_id, $service_provider_staff, $id, $userid,$CpgTrainingsIDSubTopic,$TrainingDayStatus) {

        $sql = "UPDATE ktv_business_trainings
            SET CPGtrainingsID=?, FacProgramStaffID=?, TrainingStart=?, TrainingEnd=?, TrainingDays=?, CpgBatchID=?,
               TrainingProvinceID=(SELECT ProvinceID FROM ktv_province WHERE Province=?), TrainingDistrictID=?,TotLocation=?,
               FacPartnerStaffID=?,ServiceProvID=?, ServiceProvStaffName=?,DateUpdated=now(), LastModifiedBy=?,TrainingDayStatus=?
            WHERE BsnTrainingID=?";
        $stat = $this->db->query($sql, array($training, $staf, $start, $end, $days, $batch, $provinsi, $district, $tot, $mitra, $service_provider_id, $service_provider_staff, $userid, $TrainingDayStatus, $id));

        if ($stat) {
            //sub topics (begin)
            $sql="DELETE FROM ktv_business_trainings_sub_topics WHERE BsnTrainingID = ?";
            $query = $this->db->query($sql,array($id));

            if($CpgTrainingsIDSubTopic[0] != ""){
                foreach ($CpgTrainingsIDSubTopic as $key => $value) {
                    $sql="INSERT INTO `ktv_business_trainings_sub_topics` SET
                          `BsnTrainingID` = ?,
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
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deleteData($id) {
        /*$sql = "DELETE FROM ktv_business_trainings WHERE BsnTrainingID=?";
        $sql_participant = "DELETE FROM ktv_business_trainings_participants WHERE BsnTrainingID=?";
        $stat = $this->db->query($sql_participant, array($id));
        $stat = $this->db->query($sql, array($id));*/
        $sql = "UPDATE ktv_business_trainings SET StatusCode = 'nullified',LastModifiedBy='".$_SESSION['userid']."',DateUpdated=NOW() WHERE BsnTrainingID = ? LIMIT 1";
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
                BsnTrainingPartID as participant_id,
                IF(PartType = 'farmer', PartFarmerID, PartStaffID) as id_staff,
                IF(PartType = 'farmer', CONCAT('[',PartFarmerID,'] ',kcf.FarmerName), b.PersonNm) as name,
                a.WritingAwal as wstart,
                a.WritingAkhir as wend,a.BallotAwal as bstart,a.BallotAkhir as bend
            FROM ktv_business_trainings_participants a
            LEFT JOIN ktv_staffs s ON s.StaffID = a.PartStaffID
            LEFT JOIN ktv_persons b on s.PersonID=b.PersonID
            LEFT JOIN ktv_farmer kcf ON kcf.FarmerID = a.PartFarmerID
            WHERE BsnTrainingID=? AND a.StatusCode != 'nullified'
            ORDER BY PartType, name";
        $query = $this->db->query(sprintf($sql, '', 'LIMIT ?,?'), array($id, (int) $start, (int) $limit));
        $result['data'] = $query->result_array();
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;
        return $result;
    }

    public function readParticipantsAddFarmer($TrainingID, $ProvinceID, $DistrictID, $SubDistrictID, $CPGid, $key, $start, $limit)
    {
        $sql = "
SELECT SQL_CALC_FOUND_ROWS
    kcf.FarmerID AS id
    , kcf.FarmerName AS `name`
    , Province
    , District
    , SubDistrict
    , Village
    , GroupName
FROM ktv_farmer kcf
JOIN ktv_village v ON v.VillageID = kcf.VillageID
JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
JOIN ktv_district d ON d.DistrictID = sd.DistrictID
JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
JOIN ktv_cpg c ON c.CPGid = kcf.CPGid
WHERE
    kcf.StatusCode = 'active'
    AND kcf.FarmerID NOT IN (
        SELECT
            p.PartFarmerID
        FROM ktv_business_trainings_participants p
        WHERE
            PartFarmerID IS NOT NULL AND p.BsnTrainingID = ?
    )
    --where--
ORDER BY `name`
LIMIT ?, ?
        ";
        $where = '';
        $params = array();
        $params[] = $TrainingID;
        if (!empty($ProvinceID)) {
            $where .= " AND p.ProvinceID = ?";
            $params[] = $ProvinceID;
        }
        if (!empty($DistrictID)) {
            $where .= " AND d.DistrictID = ?";
            $params[] = $DistrictID;
        }
        if (!empty($SubDistrictID)) {
            $where .= " AND sd.SubDistrictID = ?";
            $params[] = $SubDistrictID;
        }
        if (!empty($CPGid)) {
            $where .= " AND c.CPGid = ?";
            $params[] = $CPGid;
        }
        if (!empty($key)) {
            $where .= " AND (kcf.FarmerName LIKE ? OR kcf.FarmerID LIKE ?)";
            $params[] = "%$key%";
            $params[] = "%$key%";
        }

        $params[] = intval($start);
        $params[] = intval($limit);
        $sql = str_replace('--where--', $where, $sql);
        $query = $this->db->query($sql, $params);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $result['data'] = $query->result_array();
        //$query = $this->db->query($sql, array($cpgID,$CpgBatchTrainingID));

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;
        return $result;
    }

    public function readParticipantsAddStaff($TrainingID, $role, $key, $start, $limit)
    {
        $sql = "
SELECT SQL_CALC_FOUND_ROWS
    s.StaffID AS id
    , p.PersonNm AS `name`
    , s.ObjType AS `type`
FROM ktv_staffs s
JOIN ktv_persons p ON p.PersonID = s.PersonID
WHERE
    p.PersonNm IS NOT NULL AND p.PersonNm != ''
    AND StaffID NOT IN (
        SELECT
            tp.PartStaffID
        FROM ktv_business_trainings_participants tp
        WHERE
            tp.BsnTrainingID = ?
    )
    --where--
ORDER BY `name`
LIMIT ?, ?
        ";
        $where = '';
        $params = array();
        $params[] = $TrainingID;
        if (!empty($role)) {
            $where .= " AND s.ObjType = ?";
            $params[] = $role;
        }
        if (!empty($key)) {
            $where .= " AND (s.PersonNm LIKE ? OR s.StaffID LIKE ?)";
            $params[] = "%$key%";
            $params[] = "%$key%";
        }

        $sql = str_replace('--where--', $where, $sql);
        $params[] = intval($start);
        $params[] = intval($limit);
        $query = $this->db->query($sql, $params);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $result['data'] = $query->result_array();
        //$query = $this->db->query($sql, array($cpgID,$CpgBatchTrainingID));

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;
        return $result;
    }

    function createParticipants($BsnTrainingID, $type, $participants, $userid) {

        $record = array();
        $this->db->trans_start(FALSE);
        foreach ($participants as $participant) {
            $data = array(
                'BsnTrainingID'         => $BsnTrainingID,
                'PartType'              => $type,
                'WritingAwal'           => 0,
                'WritingAkhir'          => 0,
                'BallotAwal'            => 0,
                'BallotAkhir'           => 0,
                'DateCreated'           => date("Y-m-d H:i:s"),
                'DateUpdated'           => date("Y-m-d H:i:s"),
                'CreatedBy'             => $userid,
                'LastModifiedBy'        => $userid,
            );
            if ($type == 'farmer') {
                $data['PartFarmerID']          = $participant;
            } elseif ($type == 'staff') {
                $data['PartStaffID']           = $participant;
            }
            // $record[] = $data;
            $this->db->insert('ktv_business_trainings_participants', $data);
        }
        $this->db->trans_complete();

        if (!$this->db->trans_status()) {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        } else {
            $results['success'] = true;
            $results['message'] = "record created.";
        }
        //$results['success'] = true;
        //$results['message'] = "record created.";
        return $results;
    }

    function createParticipant($training, $staf, $wstart, $wend, $bstart, $bend, $userid) {
        $sql = "
            insert into ktv_business_trainings_participants (BsnTrainingID, ParticipantPersonID,ParticipantNewStaffID,
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

    function updateParticipant($wstart, $wend, $bstart, $bend, $id, $userid) {
        $sql = "
            UPDATE ktv_business_trainings_participants
            SET WritingAwal=?, WritingAkhir=?, BallotAwal=?, BallotAkhir=?, DateUpdated=now(),
               LastModifiedBy=?
            WHERE BsnTrainingPartID=?";
        $stat = $this->db->query($sql, array($wstart, $wend, $bstart, $bend, $userid, $id));
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
        //$sql = "DELETE FROM ktv_business_trainings_participants WHERE PartStaffID=?";
        $sql="UPDATE ktv_business_trainings_participants SET StatusCode = 'nullified',LastModifiedBy='".$_SESSION['userid']."',DateUpdated=NOW() WHERE PartStaffID = ? LIMIT 1";
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
        $sql = "SELECT * FROM (

SELECT
    p.PersonID AS id,
    CONCAT(
    CASE
       WHEN ps.PersonID THEN '[PR] - '
       WHEN pvs.PersonID THEN '[PS] - '
       WHEN es.PersonID THEN '[PU] - '
       WHEN cs.`PersonID` THEN '[CS] - '
       WHEN ps_bank.`PersonID` THEN '[BS] - '
    END,
    p.PersonNm,
    IFNULL(
    CASE
       WHEN ps.PersonID THEN
            CASE ps.`Position`
                WHEN '1' THEN ' (Field Fasilitator)'
                WHEN '2' THEN ' (District Coordinator)'
                WHEN '3' THEN ' (Program Ofiicer)'
                WHEN '4' THEN ' (Area Manager)'
                WHEN '5' THEN ' (GIS Officer)'
                WHEN '6' THEN ' (Monitoring and Evaluation)'
            END
       WHEN pvs.PersonID THEN CONCAT(' (',pvs_partner.`PartnerName`,')')
       WHEN es.PersonID THEN
            CASE es.`StaffPosition`
                WHEN '1' THEN ' (Penyuluh)'
                WHEN '2' THEN ' (Petugas Teknis)'
                WHEN '3' THEN ' (Petugas Administratif)'
                WHEN '4' THEN ' (Kepala Balai/unit/Dinas)'
            END
       WHEN cs.`PersonID` THEN CONCAT(' (',cs.`Position`,', ',cs_coop.`CoopName`,')')
       WHEN ps_bank.`PersonID` THEN CONCAT(' (',ref_pos.`PositionName`,', ',ps_bank_branch.`BranchName`,')')
    END,'')
    ) AS label
FROM
    ktv_persons p
    LEFT JOIN ktv_program_staff ps ON ps.PersonID = p.PersonID AND ps.`StatusCd`='active'

    LEFT JOIN ktv_private_staff pvs ON pvs.PersonID = p.PersonID AND pvs.`StatusCode`='active'
    LEFT JOIN ktv_program_partner pvs_partner ON pvs.`PartnerID` = pvs_partner.`PartnerID`

    LEFT JOIN ktv_extension_staff es ON es.PersonID = p.PersonID AND es.`StatusCd`='active'

    LEFT JOIN ktv_cooperative_staff cs ON p.`PersonID` = cs.`PersonID` AND cs.`StatusCode`='active'
    LEFT JOIN ktv_cooperatives cs_coop ON cs.`CoopID` = cs_coop.`CoopID`

    LEFT JOIN ktv_bank_branch_staff ps_bank ON p.`PersonID` = ps_bank.`PersonID` AND ps_bank.`StatusCode` = 'active'
    LEFT JOIN ktv_ref_position_type ref_pos ON ps_bank.`PositionID` = ref_pos.`PositionID`
    LEFT JOIN ktv_bank_branch ps_bank_branch ON ps_bank.`BranchID` = ps_bank_branch.`BranchID`

WHERE
    ps.StaffID OR pvs.PrivateStaffID OR es.ExtensionID OR cs.`StaffID` OR ps_bank.`StaffID` AND
    (p.PersonNm IS NOT NULL OR p.PersonNm != '')
ORDER BY label

) a
WHERE
    label LIKE ?
ORDER BY label";

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
ORDER BY label";

        $query = $this->db->query($sql, array("%$key%"));
        $result['data'] = $query->result_array();
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
            SELECT ParticipantStaffID as id FROM ktv_business_trainings_participants WHERE BsnTrainingID=? and ParticipantPersonID=? AND StatusCode!='nullified'";
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
            SELECT SQL_CALC_FOUND_ROWS
            b.PersonNm AS PersonNm,
            IF(i.WorkAreaName IS NOT NULL,i.WorkAreaName,IF(f.District IS NOT NULL, f.District, IF(h.District IS NOT NULL, h.District, g.District))) AS Kabupaten,
            b.Gender as Gender,
            a.ParticipantPersonID as pStaffID,
            IFNULL(
                CASE
                   WHEN c.PersonID THEN
                        CASE c.`Position`
                            WHEN '1' THEN 'Field Fasilitator'
                            WHEN '2' THEN 'District Coordinator'
                            WHEN '3' THEN 'Program Ofiicer'
                            WHEN '4' THEN 'Area Manager'
                            WHEN '5' THEN 'GIS Officer'
                            WHEN '6' THEN 'Monitoring and Evaluation'
                        END
                    WHEN e.PersonID THEN e_partner.`PartnerName`
                    WHEN d.PersonID THEN
                        CASE d.`StaffPosition`
                            WHEN '1' THEN 'Penyuluh'
                            WHEN '2' THEN 'Petugas Teknis'
                            WHEN '3' THEN 'Petugas Administratif'
                            WHEN '4' THEN 'Kepala Balai/unit/Dinas'
                        END
                    WHEN cs.`PersonID` THEN CONCAT(' (',cs.`Position`,', ',cs_coop.`CoopName`,')')
                    WHEN bs.`PersonID` THEN ref_pos.`PositionName`
                END
            ,'-') AS jabatan
            FROM
                ktv_business_trainings_participants a
                LEFT JOIN ktv_staffs s ON s.StaffID = a.PartStaffID
                LEFT JOIN ktv_persons b on s.PersonID=b.PersonID
                LEFT JOIN ktv_farmer kcf ON kcf.FarmerID = a.PartFarmerID
                LEFT JOIN ktv_program_staff c ON b.PersonID=c.PersonID
                LEFT JOIN ktv_extension_staff d ON a.ParticipantStaffID=d.ExtensionID
                LEFT JOIN ktv_private_staff e ON a.ParticipantStaffID=e.PrivateStaffID
                LEFT JOIN ktv_program_partner e_partner ON e.`PartnerID` = e_partner.`PartnerID`
                LEFT JOIN ktv_bank_branch_staff bs ON b.`PersonID` = bs.`PersonID`
                LEFT JOIN ktv_ref_position_type ref_pos ON bs.`PositionID` = ref_pos.`PositionID`
                LEFT JOIN ktv_cooperative_staff cs ON b.`PersonID` = cs.`PersonID` AND cs.`StatusCode`='active'
                LEFT JOIN ktv_cooperatives cs_coop ON cs.`CoopID` = cs_coop.`CoopID`
                LEFT JOIN ktv_district f ON c.WorkArea = f.DistrictID
                LEFT JOIN ktv_district h ON e.Location = h.DistrictID
                LEFT JOIN ktv_district g ON SUBSTR(d.VillageID,1,4) = g.DistrictID
                LEFT JOIN ktv_district bs_dis ON SUBSTR(bs.`VillageID`,1,4) = bs_dis.DistrictID
                LEFT JOIN ktv_ref_work_area i ON i.WorkAreaID=b.`WorkAreaID`
			WHERE BsnTrainingID=? and IFNULL(e.StaffName, IFNULL(d.StaffName, b.PersonNm)) is not null and a.`StatusCode`!='nullified'
            ORDER BY PersonNm";
        $query = $this->db->query($sql, array($key));
        $result['data'] = $query->result_array();
        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;
        return $result;
    }

    function readTraining($id) {
        $sql = "
            SELECT i.CpgTrainings
                , a.BsnTrainingID
                , d.PersonNm AS koordinator
                , fp.PersonNm AS private_staff
                , a.TotLocation
                , b.Province AS Provinsi
                , kd.District
                , a.TrainingDistrictID
                , f.PartnerName AS Partner
                , g.PartnerName AS PrivateStaffPartner
                , h.BatchNumber
                , DATE_FORMAT(a.TrainingStart,'%d - %b - %Y') TrainingStart
                , DATE_FORMAT(a.TrainingEnd,'%d - %b - %Y') TrainingEnd
                , ServiceProvName
                , ServiceProvStaffName
                , GROUP_CONCAT(subtop.`CpgTrainings` SEPARATOR ', ') AS Subtopics
                , a.TrainingDayStatus
            from ktv_business_trainings a
            LEFT JOIN ktv_staffs s1 ON s1.StaffID = a.FacProgramStaffID
            LEFT JOIN ktv_persons d ON d.PersonID = s1.PersonID
            LEFT JOIN ktv_province b ON a.TrainingProvinceID=b.ProvinceID
            LEFT JOIN ktv_district kd ON a.TrainingDistrictID=kd.DistrictID
            LEFT JOIN ktv_program_staff c ON d.PersonID = c.PersonID
            LEFT JOIN ktv_staffs s2 ON s2.StaffID = a.FacPartnerStaffID
            LEFT JOIN ktv_persons fp ON fp.PersonID = s2.PersonID
            LEFT JOIN ktv_private_staff e ON e.PersonID = fp.PersonID
            LEFT JOIN ktv_program_partner f ON f.PartnerID=c.PartnerId
            LEFT JOIN ktv_program_partner g ON g.PartnerID=e.PartnerID
            LEFT JOIN ktv_cpg_batch h ON a.CpgBatchID=h.CpgBatchID
            LEFT JOIN ktv_cpg_trainings i ON a.CPGtrainingsID=i.CpgtrainingsID
            LEFT JOIN ktv_service_provider sp ON sp.ServiceProvID = a.ServiceProvID
            LEFT JOIN ktv_business_trainings_sub_topics sub ON a.`BsnTrainingID` = sub.`BsnTrainingID`
            LEFT JOIN ktv_cpg_trainings subtop ON sub.`SubCpgTrainingsID` = subtop.`CpgTrainingsID`
            WHERE a.BsnTrainingID=?";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

    function readPartnerLogo($id) {
        $sql = "
            select distinct c.Photo
            from ktv_business_trainings a,
            ktv_cpg_batch b,
            ktv_program_partner c
            where a.CpgBatchID = b.CpgBatchID
            and b.PartnerID = c.PartnerID
            and a.BsnTrainingID =? ";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

    public function getFacilitatorSCPP()
    {
//         $sql = "
// SELECT
//     s.StaffID AS id,
//     CONCAT(
//     p.PersonNm,
//     CASE ps.Position
//         WHEN 1 THEN ' (Field Fasilitator)'
//         WHEN 2 THEN ' (District Coordinator)'
//         WHEN 3 THEN ' (Program Ofiicer)'
//         WHEN 4 THEN ' (Area Manager)'
//         WHEN 5 THEN ' (GIS Officer)'
//         WHEN 6 THEN ' (Monitoring and Evaluation)'
//         ELSE ''
//     END
//     ) AS `label`
// FROM ktv_program_staff ps
// JOIN ktv_persons p ON p.PersonID = ps.PersonID
// ORDER BY label
//         ";

        $sql="SELECT
    a.StaffID AS id,
    CONCAT(b.`PersonNm`,' (',IFNULL(rpos.`PositionName`,'-'),')') AS label
FROM
    ktv_staffs a
    INNER JOIN ktv_persons b ON a.`PersonID` = b.`PersonID`
    LEFT JOIN ktv_staff_positions c ON a.`StaffID` = c.`StaffPosStaffID`
        AND (CURDATE() BETWEEN c.`StaffPostStart` AND c.`StaffPostEnd`)
        AND c.StatusCode = 'active'
    LEFT JOIN ktv_ref_position_type rpos ON c.StaffPosPositionID = rpos.PositionID
WHERE
    a.`ObjType` = 'program'
    AND b.`PersonID` != '3966' #admin
    AND a.`StatusCode` = 'active'
ORDER BY label ASC";

        $query = $this->db->query($sql);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function getFacilitatorMitra()
    {
//         $sql = "
// SELECT
// *
// FROM (
// SELECT
//     p.PersonID AS id,
//     CONCAT(p.PersonNm,' (',pp.PartnerName,')') AS label
// FROM ktv_private_staff ps
// JOIN ktv_persons p ON p.PersonID = ps.PersonID
// JOIN ktv_program_partner pp ON ps.PartnerID = pp.PartnerID
// UNION ALL
// SELECT
//     p.PersonID AS id,
//     CONCAT(
//     p.PersonNm,
//     CASE es.StaffPosition
//         WHEN 1 THEN ' (Penyuluh)'
//         WHEN 2 THEN ' (Petugas Teknis)'
//         WHEN 3 THEN ' (Petugas Administratif)'
//         WHEN 4 THEN ' (Kepala Balai/unit/Dinas)'
//         ELSE ''
//     END
//     ) AS `label`
// FROM ktv_extension_staff es
// JOIN ktv_persons p ON p.PersonID = es.PersonID
// ) r
// ORDER BY label
//         ";

        $sql="SELECT
    a.StaffID AS id,
    CONCAT(b.`PersonNm`, ' (',IFNULL(rpos.`PositionName`,'-'),', ',
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
    END,'-')
    ,')') AS label
FROM
    ktv_staffs a
    INNER JOIN ktv_persons b ON a.`PersonID` = b.`PersonID`
    LEFT JOIN ktv_staff_positions c ON a.`StaffID` = c.`StaffPosStaffID`
        AND (CURDATE() BETWEEN c.`StaffPostStart` AND c.`StaffPostEnd`)
        AND c.StatusCode = 'active'
    LEFT JOIN ktv_ref_position_type rpos ON c.StaffPosPositionID = rpos.PositionID
    INNER JOIN sys_role srol ON a.`ObjType` = srol.`RoleCode`
WHERE
    a.`StatusCode` = 'active'
    AND srol.RoleMasterTrainingFacilitator = '1'
ORDER BY a.`ObjType`,label ASC";

        $query = $this->db->query($sql);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function readProvinsis()
    {
        $sql = '
SELECT
    ProvinceID AS id
    , Province AS label
FROM
    ktv_province
WHERE
    active = 1
ORDER BY label
        ';
        $query = $this->db->query($sql);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function readKabupatens($ProvinceID)
    {
        $sql = '
SELECT
    DistrictID AS id
    , District AS label
FROM
    ktv_district
WHERE
    active = 1
    AND ProvinceID = ?
ORDER BY label
        ';
        $query = $this->db->query($sql, array($ProvinceID));
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function readKecamatans($DistrictID)
    {
        $sql = '
SELECT
    SubDistrictID AS id
    , SubDistrict AS label
FROM
    ktv_subdistrict
WHERE
    active = 1
    AND DistrictID = ?
ORDER BY label
        ';
        $query = $this->db->query($sql, array($DistrictID));
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function readCPGs($SubDistrictID)
    {
        $sql = "
SELECT
    CPGid AS id
    , GroupName AS label
FROM
    ktv_cpg c
JOIN ktv_village v ON v.VillageID = c.VillageID
WHERE
    c.`Status` = 'active'
    AND v.SubDistrictID = ?
ORDER BY label
        ";
        $query = $this->db->query($sql, array($SubDistrictID));
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function getFarmerAttendanceDay($TrainingID, $DayNumber) {
        $sql = "SELECT
    a.BsnTrainingPartID AS id,
    a.PartType,
    a.PartStaffID,
    a.PartFarmerID,
    IF(PartType = 'farmer', CONCAT('[',PartFarmerID,'] ',kcf.FarmerName), b.PersonNm) as name,
    IF(bta.Attendance1 = 0,'',bta.Attendance1) AS Attendance1,
    IF(bta.Attendance2 = 0,'',bta.Attendance2) AS Attendance2
FROM ktv_business_trainings_participants a
LEFT JOIN ktv_staffs s ON s.StaffID = a.PartStaffID
LEFT JOIN ktv_persons b on s.PersonID=b.PersonID
LEFT JOIN ktv_farmer kcf ON kcf.FarmerID = a.PartFarmerID
LEFT JOIN ktv_business_trainings_attendance bta ON bta.BsnTrainingID = a.`BsnTrainingID` AND ((PartType = 'staff' AND bta.AttStaffID = a.PartStaffID) OR (PartType = 'farmer' AND bta.AttFarmerID = a.PartFarmerID)) AND DayNumber = ?
WHERE
    a.BsnTrainingID = ?
GROUP BY a.BsnTrainingPartID
ORDER BY PartType, name
        ";
        $query = $this->db->query($sql, array($DayNumber, $TrainingID));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function updateFarmerAttendance($TrainingID, $type, $StaffID, $FarmerID, $DayNumber, $Attendance1, $Attendance2, $TrainingDate) {

        //cek apakah insert / update
        $sql="SELECT
                COUNT(*) AS BANYAK
            FROM
                ktv_business_trainings_attendance
            WHERE
                BsnTrainingID = ?
                AND AttStaffID = ?
                AND AttFarmerID = ?
                AND DayNumber = ?";
        $query = $this->db->query($sql,array($TrainingID,$StaffID,$FarmerID,$DayNumber));
        $data = $query->row_array();

        if($data['BANYAK'] > 0){
            //update
            return $this->db->update('ktv_business_trainings_attendance',
                array(
                    'Attendance1'       => $Attendance1,
                    'Attendance2'       => $Attendance2,
                    'TrainingDate'      => $TrainingDate,
                        ),
                array(
                    'BsnTrainingID'     => $TrainingID,
                    'AttStaffID'        => $StaffID,
                    'AttFarmerID'       => $FarmerID,
                    'DayNumber'         => $DayNumber
                        )
            );
        }else{
            //insert
            $sql="INSERT INTO ktv_business_trainings_attendance SET
                    BsnTrainingID   = ?,
                    AttType         = ?,
                    AttStaffID      = ?,
                    AttFarmerID     = ?,
                    DayNumber       = ?,
                    Attendance1     = ?,
                    Attendance2     = ?,
                    TrainingDate    = ?
                ";
            $p = array(
                $TrainingID,
                $type,
                $StaffID,
                $FarmerID,
                $DayNumber,
                $Attendance1,
                $Attendance2,
                $TrainingDate,
                $FamilyID,
                $FamilyName
            );
            return $this->db->query($sql,$p);
        }
    }
}

?>
