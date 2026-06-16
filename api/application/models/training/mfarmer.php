<?php

class Mfarmer extends CI_Model {
    public $user;

    public function __construct()
    {
        parent::__construct();
        $this->user = $this->muserprofile->getUserProfile();
    }

    public function getFacilitatorSCPP()
    {
        $sql = "SELECT
            p.`PersonID` AS id,
            CONCAT(p.`PersonNm`,' (PR/',part.PartnerName,') ',IFNULL(rpos.PositionName,'-')) AS label
        FROM
            ktv_staffs st
            INNER JOIN ktv_persons p ON st.`PersonID` = p.`PersonID`
            LEFT JOIN ktv_program_partner part ON st.`ObjID` = part.`PartnerID`
            
            LEFT JOIN ktv_staff_positions f ON st.`StaffID` = f.`StaffPosStaffID`
                AND (CURDATE() BETWEEN f.`StaffPostStart` AND f.`StaffPostEnd`)
                AND f.StatusCode = 'active'
            LEFT JOIN ktv_ref_position_type rpos ON f.StaffPosPositionID = rpos.PositionID
        WHERE 1=1
            AND st.`StatusCode` != 'nullified'
            AND p.`StatusCd` != 'nullified'
            AND st.`ObjType` = 'program'
            AND p.`PersonID` NOT IN (1)
        ORDER BY label";
        $query = $this->db->query($sql);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function getFacilitatorMitra()
    {
        $sql = "
    SELECT
        p.`PersonID` AS id
        , CONCAT(p.`PersonNm`,' (',
            IF(st.`ObjType`='private',
            (SELECT subp.PartnerName FROM ktv_program_partner subp WHERE subp.PartnerID = st.`ObjID` LIMIT 1)
            ,'Penyuluh')
            ,') ',IFNULL(rpos.PositionName,'-')) AS label
    FROM
        ktv_staffs st
        INNER JOIN ktv_persons p ON st.`PersonID` = p.`PersonID`
        
        LEFT JOIN ktv_staff_positions f ON st.`StaffID` = f.`StaffPosStaffID`
            AND (CURDATE() BETWEEN f.`StaffPostStart` AND f.`StaffPostEnd`)
            AND f.StatusCode = 'active'
        LEFT JOIN ktv_ref_position_type rpos ON f.StaffPosPositionID = rpos.PositionID
    WHERE 1=1
        AND st.`StatusCode` != 'nullified'
        AND p.`StatusCd` != 'nullified'
        AND st.`ObjType` IN ('private','extension')
    ORDER BY label
        ";
        $query = $this->db->query($sql);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    function readDatasOld($key, $prov, $dist, $subdist, $start, $limit) {
        $userID = $_SESSION['userid'];
        $PartnerID = $_SESSION['PartnerID'];
        $sqll = "
            SELECT PartnerID FROM ktv_program_staff WHERE UserId=?";
        $queryy = $this->db->query($sqll, array($userID));
        $resultt = $queryy->result_array();
        $wh = '';
        if ($resultt[0]['PartnerID'] == '1' OR $userID == '1')
            $wh = ' OR a.CPGtrainingsID is not null';
        // if ($PartnerID)
        //     $wh .= " OR c.`PartnerID` = {$PartnerID}";
        $where = '';
        if (!empty($prov)) {
            $where .= " AND TrainingProvince = {$prov}";
        }
        if (!empty($dist)) {
            $where .= " AND TrainingDistrict = {$dist}";
        }
        if (!empty($this->user['district_access'])) {
            $where .= " AND TrainingDistrict IN ({$this->user['district_access']})";
        }
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS
            a.FarmerTrainingID as id,CpgTrainings as training,
            District as tot,count(FarmerTrainingsFarmerID) as participant,date(TrainingStart) as start,date(TrainingEnd) as end,
            TrainingDays as days
            from ktv_farmer_trainings a
            left join ktv_district kd on a.TrainingDistrict=kd.DistrictID
            left join ktv_cpg_trainings b on a.CPGtrainingsID=b.CpgtrainingsID
            left join ktv_farmer_trainings_participants d on a.FarmerTrainingID=d.FarmerTrainingID AND d.`StatusCode` = 'active'
            WHERE a.StatusCode != 'nullified' AND (CpgTrainings like ? OR a.FarmerTrainingID like ?)
               AND (kd.DistrictID IN ({$_SESSION['daerah_access']})
               $wh )
               $where
            GROUP BY a.FarmerTrainingID
            LIMIT ?,?";
        $query = $this->db->query($sql, array("%$key%", $key, (int) $start, (int) $limit));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $result['data']     = array();
        if ($query->num_rows() > 0) {
            $result['data'] = $query->result_array();
        }
            $query = $this->db->query('SELECT FOUND_ROWS() as total');
            $result['total'] = $query->row()->total;
        return $result;
    }

    function readDatas($IMSID, $start, $limit) {
        $userID = $_SESSION['userid'];
        $PartnerID = $_SESSION['PartnerID'];
        $GroupID = $_SESSION['groupid'];
        $daerah_access = $_SESSION['daerah_access'];
        
        $sqll = "
            SELECT PartnerID FROM ktv_program_staff WHERE UserId=?";
        $queryy = $this->db->query($sqll, array($userID));
        $resultt = $queryy->result_array();
        $wh = '';
        if($queryy->num_rows()>0){
            if ($resultt[0]['PartnerID'] == '1' OR $GroupID == '1')
                $wh = ' OR a.CPGtrainingsID is not null';
            if ($PartnerID)
                $wh .= " OR c.`PartnerID` = {$PartnerID}";
        }
        $where = '';
        if (!empty($this->user['district_access'])) {
            $where .= " AND TrainingDistrict IN ({$this->user['district_access']})";
        }
        if (!empty($IMSID)) {
            $where .= " AND a.IMSID = '$IMSID'";
        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS
            a.FarmerTrainingID as id
            , CpgTrainings as training
            , BatchNumber as batch
            , e.`PartnerName` AS partner_name
            , District as tot
            , count(FarmerTrainingsFarmerID) as participant
            , date(TrainingStart) as start
            , date(TrainingEnd) as end
            , TrainingDays as days
            , CASE 
                WHEN TrainingStatus = 1 THEN 'Complete'
                WHEN TrainingStatus = 2 THEN 'On Going'
                ELSE '-'
            END TrainingStatus
            FROM 
                ktv_farmer_trainings a
            LEFT JOIN 
                ktv_district kd on a.TrainingDistrict=kd.DistrictID
            LEFT JOIN 
                ktv_cpg_trainings b on a.CPGtrainingsID=b.CpgtrainingsID
            LEFT JOIN 
                ktv_cpg_batch c on a.CpgBatchID=c.CpgBatchID
            LEFT JOIN 
                ktv_farmer_trainings_participants d on a.FarmerTrainingID=d.FarmerTrainingID AND d.`StatusCode` = 'active'
            LEFT JOIN 
                `ktv_program_partner` e ON c.`PartnerID`=e.`PartnerID`
            WHERE 
                a.StatusCode != 'nullified' 
            AND (CpgTrainings like ? OR a.FarmerTrainingID like ?)
            AND (kd.DistrictID IN ($daerah_access))
               $where
            GROUP BY a.FarmerTrainingID
            LIMIT ?,?";
        $query = $this->db->query($sql, array("%$key%", $key, (int) $start, (int) $limit));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $result['data']     = array();
        if ($query->num_rows() > 0) {
            $result['data'] = $query->result_array();
        }
        $result['sql'] = $this->db->last_query();
            $query = $this->db->query('SELECT FOUND_ROWS() as total');
            $result['total'] = $query->row()->total;
        return $result;
    }

    function readData($id) {
        $sql = "SELECT 
            a.*
            , a.FacPrivatePersonID fasilitator_mitra
            , a.FacProgramPersonID fasilitator_scpp
            , t.CpgTrainings AS label
            , DATE(TrainingStart) AS TrainingStart
            , DATE(TrainingEnd) AS TrainingEnd
            , GROUP_CONCAT(sub.`SubCpgTrainingsID` SEPARATOR ',') AS subtopics
            , b.`ProvinceID` Province
            , c.`DistrictID` District
            FROM ktv_farmer_trainings a
            LEFT JOIN ktv_cpg_trainings t ON t.CpgTrainingsID = a.CPGtrainingsID
            LEFT JOIN ktv_province b ON a.TrainingProvince=b.ProvinceID
            LEFT JOIN ktv_district c ON a.TrainingDistrict=c.DistrictID
            LEFT JOIN `ktv_farmer_trainings_sub_topics` sub ON a.`FarmerTrainingID` = sub.`FarmerTrainingID`
            WHERE a.FarmerTrainingID=?";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

    function createData($post) {
        $sql = "INSERT INTO ktv_farmer_trainings SET
                    IMSID = ?,
                    CPGtrainingsID = ?,
                    CpgBatchID = ?,
                    FacProgramPersonID = ?,
                    TrainingStart = ?,
                    TrainingEnd = ?,
                    TrainingDistrict = ?,
                    TrainingProvince = ?,
                    TotLocation = ?,
                    FacPrivatePersonID = ?,
                    TrainingDays = ?,
                    TrainingDayStatus = ?,
                    DateCreated = NOW(),
                    CreatedBy = ?,
                    DateUpdated = NOW(),
                    LastModifiedBy = ?,
                    TrainingStatus = ?";
        $p = array(
            $post["IMSID"],
            $post["training"],
            $post["cpg"],
            $post["fasilitator_scpp"],
            $post["TrainingStart"],
            $post["TrainingEnd"],
            $post["Kabupaten"],
            $post["Provinsi"],
            $post["location"],
            $post["fasilitator_mitra"],
            $post["days"],
            $post["TrainingDayStatus"],
            $_SESSION['userid'],
            $_SESSION['userid'],
            $post["TrainingStatus"],
        );
        $stat = $this->db->query($sql, $p);

        if($stat){
            $FarmerTrainingID = $this->db->insert_id();

            //sub topic (begin)
            foreach ($post["CpgTrainingsIDSubTopic"] as $key => $value) {
                $sql2 = "INSERT INTO `ktv_farmer_trainings_sub_topics` SET
                      `FarmerTrainingID` = ?,
                      `SubCpgTrainingsID` = ?,
                      `DateCreated` = NOW(),
                      `CreatedBy` = ?";
                $p2 = array(
                    $FarmerTrainingID,
                    $value,
                    $_SESSION['userid']
                );
                $query = $this->db->query($sql2,$p2);
            }
            //sub topic (end)

            $results['success'] = true;
            $results['message'] = lang("Data Saved");
            $results['FarmerTrainingID'] = $FarmerTrainingID;
        } else {
            $results['success'] = false;
            $results['message'] = lang("Failed to save data");
        }
        return $results;
    }

    function updateData($post) {
        $sql = "UPDATE ktv_farmer_trainings SET
                    CPGtrainingsID = ?,
                    CpgBatchID = ?,
                    FacProgramPersonID = ?,
                    TrainingStart = ?,
                    TrainingEnd = ?,
                    TrainingDistrict = ?,
                    TrainingProvince = ?,
                    TotLocation = ?,
                    FacPrivatePersonID = ?,
                    TrainingDays = ?,
                    TrainingDayStatus = ?,
                    DateUpdated = NOW(),
                    LastModifiedBy = ?,
                    TrainingStatus = ? WHERE FarmerTrainingID = ?";
        $p = array(
            $post["training"],
            $post["cpg"],
            $post["fasilitator_scpp"],
            $post["TrainingStart"],
            $post["TrainingEnd"],
            $post["Kabupaten"],
            $post["Provinsi"],
            $post["location"],
            $post["fasilitator_mitra"],
            $post["days"],
            $post["TrainingDayStatus"],
            $_SESSION['userid'],
            $post["TrainingStatus"],
            $post["id"]
        );
        $stat = $this->db->query($sql, $p);
        // echo "<pre>";print_r($this->db->last_query());die;

        if ($stat) {
            $sql="DELETE FROM ktv_farmer_trainings_sub_topics WHERE FarmerTrainingID = ?";
            $query = $this->db->query($sql,array($post["id"]));

            foreach ($post["CpgTrainingsIDSubTopic"] as $key => $value) {
                $sql="INSERT INTO `ktv_farmer_trainings_sub_topics` SET
                      `FarmerTrainingID` = ?,
                      `SubCpgTrainingsID` = ?,
                      `DateCreated` = NOW(),
                      `CreatedBy` = ?";
                $p = array(
                    $post["id"],
                    $value,
                    $userid
                );
                $query = $this->db->query($sql,$p);
            }

            $results['success'] = true;
            $results['message'] = "Data Updated";
            $results['FarmerTrainingID'] = $post["id"];
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update data";
        }
        return $results;
    }

    function deleteData($id) {
        //$sql = "DELETE FROM ktv_farmer_trainings WHERE FarmerTrainingID=?";
        $sql="UPDATE ktv_farmer_trainings SET StatusCode = 'nullified',LastModifiedBy='".$_SESSION['userid']."',DateUpdated=NOW() WHERE FarmerTrainingID = ? LIMIT 1";
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
        $sql = "SELECT %s
            from 
                ktv_farmer_trainings_participants a
            left join ktv_farmer_trainings ac on a.FarmerTrainingID=ac.FarmerTrainingID
            left join ktv_cpg_batch ae on ae.CpgBatchID=ac.CpgBatchID
            left join ktv_members b on b.StatusCode='active' and b.MemberID=a.FarmerID
            left join ktv_member_family_labour c on a.FamilyID=c.FamLabID
   			LEFT JOIN ktv_village d ON b.VillageID = d.VillageID
   			LEFT JOIN ktv_subdistrict e ON d.SubDistrictID = e.SubDistrictID               
            LEFT JOIN (
                SELECT
                    COUNT(*) kehadiran
                    , FarmerTrainingID
                    , FarmerID
                FROM
                    `ktv_farmer_trainings_attendance`
                GROUP BY
                    FarmerTrainingID, FarmerID
            ) as att on att.FarmerTrainingID = ac.FarmerTrainingID AND att.FarmerID = a.FarmerID
            WHERE a.FarmerTrainingID=? AND a.StatusCode != 'nullified'
            GROUP BY FarmerTrainingsFarmerID
            ORDER BY MemberName /*,b.VillageID */
             %s";
        $query = $this->db->query(sprintf($sql, 'FarmerTrainingsFarmerID as participant_id, a.FarmerTrainingID,b.MemberDisplayID as farmer_id,b.MemberID,
            MemberName as farmer, SubDistrict as Kecamatan, b.Gender, FamLabName, c.Gender,
            IF(Subtitute="1","Ya","Tidak") as participant,Subtitute,FamLabName as if_no,a.FamilyID,
            WritingAwal as wstart,WritingAkhir as wend,BallotAwal as bstart,BallotAkhir as bend,ae.PartnerID, 
            CONCAT(FORMAT((att.kehadiran/ac.TrainingDays) * 100,0),"%") Percentage', ''), array($id, (int) $start, (int) $limit));
        $result['data'] = $query->result_array();
        $result['query'] = $this->db->last_query();
        $query = $this->db->query(sprintf($sql, 'count(*) as total', ''), array($id));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function readLabelProvinsi($id) {
        $sql = "
            select Province as id
            from ktv_province
            WHERE ProvinceID=?";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

    function createParticipant($training, $farmer, $petani, $family, $wstart, $wend, $bstart, $bend, $userid) {
        if ($family == '')
            $family = NULL;
        if ($petani == '')$petani = 1;
        $sql = "
            insert into ktv_farmer_trainings_participants (FarmerTrainingID, FarmerID, Subtitute, FamilyID,
               WritingAwal, WritingAkhir, BallotAwal, BallotAkhir, DateCreated,CreatedBy,DateUpdated,LastModifiedBy)
            VALUES (?,?,?,?,   ?,?,?,?,now(),?,now(),?)";
        $stat = $this->db->query($sql, array($training, $farmer, $petani, $family, $wstart, $wend, $bstart, $bend, $userid, $userid));
        if ($stat) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateParticipant($training, $farmer, $petani, $family, $wstart, $wend, $bstart, $bend, $id, $userid) {
        if ($family == '')
            $family = null;
        $sql = "
            UPDATE ktv_farmer_trainings_participants
            SET Subtitute=?, FamilyID=?, WritingAwal=?, WritingAkhir=?, BallotAwal=?, BallotAkhir=?,
               DateUpdated=now(), LastModifiedBy=?
            WHERE FarmerTrainingsFarmerID=?";
        $stat = $this->db->query($sql, array($petani, $family, $wstart, $wend, $bstart, $bend, $userid, $id));
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
        //$sql = "DELETE FROM ktv_farmer_trainings_participants WHERE FarmerTrainingsFarmerID=?";
        $sql="UPDATE ktv_farmer_trainings_participants SET StatusCode = 'nullified',LastModifiedBy='".$_SESSION['userid']."',DateUpdated=NOW() WHERE FarmerTrainingsFarmerID = ? LIMIT 1";
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

    function readFarmers($prov, $search = '', $kab = '') {
        if ($search != '')
            $add_sql = "AND (FarmerID like '%$search%' OR FarmerName like '%$search%' OR OldFarmerID like '%$search%')";
        $sql = "
            SELECT FarmerID as id,concat(FarmerID,' - ',FarmerName,IF(OldFarmerID is null,'',concat(' (',OldFarmerID,')'))) as label
            from ktv_farmer a
            left join ktv_village d on a.VillageID=d.VillageID
            left join ktv_subdistrict e on d.SubDistrictID=e.SubDistrictID
            left join ktv_district f on e.DistrictID=f.DistrictID
            WHERE a.StatusCode='active' 
            -- AND substr(a.VillageID,1,2)=? 
            AND (District = ? OR '' = ?)
            $add_sql
            ORDER BY FarmerName";
        $query = $this->db->query($sql, array($prov, $kab, $kab));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readFamilys($farmer) {
        $sql = "
            SELECT FamLabID as id,FamLabName as label
            from ktv_member_family_labour a
            WHERE MemberID=?
            ORDER BY label";
        $query = $this->db->query($sql, array($farmer));
        $result['data'] = $query->result_array();
        return $result;
    }

    function checkFarmer($training, $farmer) {
        $sql = "
            SELECT FarmerId as id FROM ktv_farmer_trainings_participants WHERE FarmerTrainingID=? and FarmerID=?";
        $query = $this->db->query($sql, array($training, $farmer));
        $result = $query->result_array();
        if ($result[0]['id'] != '')
            $res['data'] = FALSE;
        else
            $res['data'] = TRUE;
        return $res;
    }

    function readParticipantsTraining($key) {
        $sql = "SELECT %s
            FROM 
                ktv_farmer_trainings_participants a
            LEFT JOIN 
                ktv_members b on b.StatusCode='active' and b.MemberID=a.FarmerID
            LEFT JOIN 
                ktv_family c on a.FamilyID=c.FamilyID
			LEFT JOIN 
                ktv_village d ON b.VillageID = d.VillageID
			LEFT JOIN 
                ktv_subdistrict e ON d.SubDistrictID = e.SubDistrictID
            WHERE 
                FarmerTrainingID = ? AND a.StatusCode = 'active'
            ORDER BY MemberName ASC %s";
        $query = $this->db->query(sprintf($sql, "a.FarmerTrainingID,b.MemberDisplayID as pFarmerID,
            a.FamilyID,AnggotaName,AnggotaGender,MemberName as PersonNm,
            Gender, SubDistrict as Kecamatan", ''), array($key));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql, 'count(*) as total', ''), array($key));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function readTraining($id) {
        $sql = "SELECT
            j.CpgTrainings,
            a.FarmerTrainingID,
            e.PersonNm AS koordinator,
            fp.PersonNm AS private_staff,
            a.TotLocation,
            c.District AS Kabupaten,
            b.Province AS Provinsi,
            g.PartnerName AS Partner,
            h.PartnerName AS PrivateStaffPartner,
            i.BatchNumber,
            DATE_FORMAT( a.TrainingStart, '%d - %b - %Y' ) AS TrainingStart,
            DATE_FORMAT( a.TrainingEnd, '%d - %b - %Y' ) AS TrainingEnd,
            a.`TrainingDayStatus`,
            GROUP_CONCAT( subtr.`CpgTrainings` SEPARATOR ', ' ) AS subtopics,
            b.ProvinceID,
            c.DistrictID 
        FROM
            ktv_farmer_trainings a
            LEFT JOIN ktv_province b ON a.TrainingProvince = b.ProvinceID
            LEFT JOIN ktv_district c ON a.TrainingDistrict = c.DistrictID
            LEFT JOIN ktv_persons e ON e.PersonID = a.FacProgramPersonID
            LEFT JOIN ktv_program_staff d ON e.PersonID = d.PersonID
            LEFT JOIN ktv_persons fp ON fp.PersonID = a.FacPrivatePersonID
            LEFT JOIN ktv_private_staff f ON f.PersonID = fp.PersonID
            LEFT JOIN ktv_program_partner g ON g.PartnerID = d.PartnerID
            LEFT JOIN ktv_program_partner h ON h.PartnerID = f.PartnerID
            LEFT JOIN ktv_cpg_batch i ON a.CpgBatchID = i.CpgBatchID
            LEFT JOIN ktv_cpg_trainings j ON a.CPGtrainingsID = j.CpgtrainingsID
            LEFT JOIN ktv_farmer_trainings_sub_topics sub ON a.`FarmerTrainingID` = sub.`FarmerTrainingID`
            LEFT JOIN ktv_cpg_trainings subtr ON sub.`SubCpgTrainingsID` = subtr.`CpgTrainingsID` 
        WHERE
            a.FarmerTrainingID = ? 
            LIMIT 1";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        if($result[0]['subtopics'] != "") $result[0]['subtopics'] = '('.$result[0]['subtopics'].')';
        return $result[0];
    }

    function readPartnerLogo($id) {
        $sql = "
            SELECT DISTINCT c.Photo
            from ktv_farmer_trainings a,
            ktv_cpg_batch b,
            ktv_program_partner c
            where a.CpgBatchID = b.CpgBatchID
            and b.PartnerID = c.PartnerID
            and a.FarmerTrainingID =? ";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

    public function getFacilitator()
    {
        //buat SqlHakAksesKontrol (begin)
        if($_SESSION['is_admin'] == "1"){
            $sqlHakAkses = " ";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            //cek ktv_access_staff
            $sqlHakAkses .= " AND s.ObjID IN (1,".$_SESSION['PartnerID'].") ";
        } else {
            //cek ktv_access_staff
            $sqlHakAkses .= " AND s.ObjID IN (1)  ";
        }
        //buat SqlHakAksesKontrol (end)

        $sql = "SELECT
                p.PersonID AS id
                , CONCAT(p.PersonNm,IFNULL(CONCAT(' [',rpos.PositionName,']'),'-')) AS label
            FROM ktv_staffs s
            JOIN ktv_persons p ON p.PersonID = s.PersonID
            LEFT JOIN ktv_staff_positions f ON s.`StaffID` = f.`StaffPosStaffID`
                AND (CURDATE() BETWEEN f.`StaffPostStart` AND f.`StaffPostEnd`)
                AND f.StatusCode = 'active'
            LEFT JOIN ktv_ref_position_type rpos ON f.StaffPosPositionID = rpos.PositionID
            WHERE
                s.StatusCode = 'active'
                AND (s.`ObjType` = 'private' OR s.`ObjType` = 'program')
                $sqlHakAkses
            ORDER BY label
        ";
        $query = $this->db->query($sql);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function getParticipantDetail($FarmerTrainingsFarmerID) {
        $sql = "SELECT
    btf.`FarmerID`,
    f.MemberName `FarmerName`,
    cpg.`GroupName`,
    bt.TrainingDays,
    DATE(bt.TrainingStart) AS TrainingStart,
    DATE(bt.TrainingEnd) AS TrainingEnd,
    bt.TrainingDayStatus
FROM `ktv_farmer_trainings_participants` AS btf
JOIN ktv_members f ON f.`MemberID` = btf.`FarmerID`
LEFT JOIN ktv_cpg cpg ON cpg.`CPGid` = f.`FarmerGroupID`
JOIN `ktv_farmer_trainings` bt ON bt.FarmerTrainingID = btf.`FarmerTrainingID`
WHERE 1 = 1
    AND btf.`FarmerTrainingsFarmerID` = ?
        ";
        $query = $this->db->query($sql, array($FarmerTrainingsFarmerID));
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
    }

    public function getFarmerAttendance($FarmerTrainingID, $FarmerID, $DayNumber = '') {
        if ($DayNumber != '') {
            $sql = "
                SELECT
                    a.`DayNumber`, a.`SignAttendance1`, IF(a.TrainingDate = '0000-00-00',null,a.TrainingDate) AS TrainingDate,
                    IF(a.`Attendance1` = 0 OR a.`Attendance1` IS NULL, '',1)`Attendance1`,
                    IF(a.`Attendance2` = 0 OR a.`Attendance2` IS NULL, '',1)`Attendance2`,
                    b.LearningContractSign
                FROM
                    `ktv_farmer_trainings_attendance` a
                    LEFT JOIN ktv_farmer b ON a.FarmerID=b.FarmerID
                WHERE
                    a.`FarmerTrainingID` = ?
                AND a.`FarmerID` = ? AND a.`DayNumber` = ?";
            $query = $this->db->query($sql, array($FarmerTrainingID, $FarmerID, $DayNumber));
        } else {
            $sql = "
                SELECT
                    a.`DayNumber`, a.`SignAttendance1`, IF(a.TrainingDate = '0000-00-00',null,a.TrainingDate) AS TrainingDate,
                    IF(a.`Attendance1` = 0 OR a.`Attendance1` IS NULL, '',1)`Attendance1`,
                    IF(a.`Attendance2` = 0 OR a.`Attendance2` IS NULL, '',1)`Attendance2`,
                    b.LearningContractSign
                FROM
                    `ktv_farmer_trainings_attendance` a
                    LEFT JOIN ktv_farmer b ON a.FarmerID=b.FarmerID
                WHERE
                    a.`FarmerTrainingID` = ?
                AND a.`FarmerID` = ?";
            $query = $this->db->query($sql, array($FarmerTrainingID, $FarmerID));
        }
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            $this->generateFarmerAttendance($FarmerTrainingID, $FarmerID);
            return $this->getFarmerAttendance($FarmerTrainingID, $FarmerID);
        }
    }

    public function getFarmerAttendanceDay($FarmerTrainingID, $DayNumber) {
        $this->load->library('awsfileupload');

        $sql = "SELECT
                    btf.FarmerID,
                    f.MemberDisplayID AS pFarmerID,
                    btf.FamilyID,
                    f.MemberName AS FarmerName,
                    f.MemberName AS PersonNm,
                    fm.FamLabName AS AnggotaName,
                    bta.FamilyName,
                    bta.Substitute,
                    IF(bta.Attendance1 = 0,'',bta.Attendance1) AS Attendance1,
                    IF(bta.Attendance2 = 0,'',bta.Attendance2) AS Attendance2,
                    IF(bta.SignAttendance1 = NULL,'',bta.SignAttendance1) AS SignAttendance1,
                    IF(bta.SignAttendance2 = NULL,'',bta.SignAttendance2) AS SignAttendance2,
                    f.Gender,
                    SubDistrict as Kecamatan
                FROM 
                    ktv_farmer_trainings_participants btf
                LEFT JOIN 
                    ktv_members f ON f.MemberID = btf.FarmerID
                LEFT JOIN 
                    ktv_farmer_trainings_attendance bta ON bta.FarmerTrainingID = btf.`FarmerTrainingID` AND bta.FarmerID = btf.FarmerID AND DayNumber = ?
                LEFT JOIN 
                    ktv_member_family_labour fm ON fm.FamLabID = bta.FamilyID AND bta.FamilyName = fm.FamLabName
                LEFT JOIN 
                    ktv_village v ON v.VillageID = f.VillageID
                LEFT JOIN 
                    ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
                WHERE
                    btf.FarmerTrainingID = ?
                AND
                    btf.StatusCode = 'active'
                GROUP BY btf.FarmerID
        ";
        $query = $this->db->query($sql, array($DayNumber, $FarmerTrainingID));
        
        if ($query->num_rows() > 0) {
            $return = array();

            foreach($query->result_array()  as $key => $rowdata){
                $return[$key] = $rowdata;
                foreach($rowdata as $keys => $value){
                    if($keys == "SignAttendance1"){
                        if($this->awsfileupload->doesObjectExist($value) == true) {
                            $return[$key][$keys] = $this->config->item('CTCDN')."/".$value;
                        }else{
                            $return[$key][$keys] = base_url()."/".$value;
                        }
                    }
                }
            }

            return $return;
        }
        return false;
    }

    public function generateFarmerAttendance($FarmerTrainingID, $FarmerID) {
        $query = $this->db->get_where('ktv_farmer_trainings', array('FarmerTrainingID' => $FarmerTrainingID));
        $detail = $query->row_array(0);

        $attendance = array();
        for ($i = 1; $i <= $detail['TrainingDays']; $i++) {
            $attendance[] = array(
                'FarmerTrainingID' => $FarmerTrainingID,
                'FarmerID' => $FarmerID,
                'DayNumber' => $i,
                'Attendance1' => 0,
                'Attendance2' => 0,
            );
        }
        return $this->db->insert_batch('ktv_farmer_trainings_attendance', $attendance);
    }

    public function updateFarmerAttendance($FarmerTrainingID, $FarmerID, $DayNumber, $Attendance1, $Attendance2, $TrainingDate, $FamilyID = 0, $FamilyName=null) {

        //cek apakah insert / update
        $sql="SELECT
                COUNT(*) AS BANYAK
            FROM
                ktv_farmer_trainings_attendance
            WHERE
                FarmerTrainingID = ?
                AND FarmerID = ?
                AND DayNumber = ?";
        $query = $this->db->query($sql,array($FarmerTrainingID,$FarmerID,$DayNumber));
        $data = $query->row_array();

        if($data['BANYAK'] > 0){
            //update
            return $this->db->update('ktv_farmer_trainings_attendance', array(
                    'Attendance1' => $Attendance1,
                    'Attendance2' => $Attendance2,
                    'TrainingDate' => $TrainingDate,
                    'FamilyID' => $FamilyID,
                    'FamilyName' => $FamilyName
                        ), array(
                    'FarmerTrainingID' => $FarmerTrainingID,
                    'FarmerID' => $FarmerID,
                    'DayNumber' => $DayNumber
                        )
            );
        }else{
            //insert
            $sql="INSERT INTO ktv_farmer_trainings_attendance SET
                    FarmerTrainingID = ?,
                    FarmerID = ?,
                    DayNumber = ?,
                    Attendance1 = ?,
                    Attendance2 = ?,
                    TrainingDate = ?,
                    FamilyID = ?,
                    FamilyName = ?
                ";
            $p = array(
                $FarmerTrainingID,
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

    public function getFamilyID($FarmerID, $AnggotaName) {
        $sql = "SELECT
                FamLabID AS FamilyID
            FROM
                ktv_member_family_labour
            WHERE
                MemberID = ?
            AND FamLabName like ?
        ";
        $query = $this->db->query($sql, array($FarmerID, "%$AnggotaName%"));
        if ($query->num_rows() > 0) {
            $result = $query->row_array(0);
            return $result['FamilyID'];
        }
        return false;
    }

    public function readProvinsis($user, $key) 
    {
        $sql = "SELECT
            p.ProvinceID AS id,
            p.Province AS label
        FROM ktv_province p 
        JOIN ktv_district d ON d.ProvinceID = p.ProvinceID
        WHERE 1 = 1 AND d.DistrictID IN ({$user['accessStaff']})
            --where--
        GROUP BY p.ProvinceID
        ORDER BY p.Province";
        $where = '';
        // if ($user['type'] == 'program' || $user['type'] == 'private') {
        //     if (!empty($user['accessStaff'])) {
        //         $where = " AND d.DistrictID IN({$user['accessStaff']})";
        //     } elseif (!empty($user['accessDistrict'])) {
        //         $where = " AND d.DistrictID IN({$user['accessDistrict']})";
        //     }
        // }
        $sql = str_replace('--where--', $where, $sql);
        $query = $this->db->query($sql);

        $result['data'] = $query->result_array();
        return $result;
    }
    // just a copy from mfarmer (farmer module)
    // dengan penambahan active filter
    public function readKabupatens($user, $ProvinceID = '') 
    {
        $sql = "SELECT
            d.DistrictID AS id,
            d.District AS label
        FROM ktv_district d
        JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
        WHERE 1 = 1 AND d.DistrictID IN ({$user['accessStaff']})
        --where--
        ORDER BY label";
        $where = '';
        if (!empty($ProvinceID)) {
            $where = " AND (d.ProvinceID = '{$ProvinceID}' OR p.Province = '{$ProvinceID}')";
        }
        // if ($user['isPrivateStaff'] || $user['isProgramStaff']) {
        //     if (!empty($user['accessStaff'])) {
        //         $where = " AND d.DistrictID IN({$user['accessStaff']}";
        //     } elseif (!empty($user['districtPartner'])) {
        //         $where = " AND d.DistrictID IN({$user['districtPartner']}";
        //     }
        // }
        $sql = str_replace('--where--', $where, $sql);
        $query = $this->db->query($sql);

        $result['sql']  = $this->db->last_query();
        $result['data'] = $query->result_array();
        return $result;
    }

    function readParticipantsAdd($FarmerTrainingID, $prov, $kab, $cpg, $key, $start, $limit) {
        //buat SqlHakAksesKontrol (begin)
        if($_SESSION['is_admin'] == "1"){
            $sqlHakAkses = " ";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            //cek ktv_access_staff
            $sqlHakAkses = " AND d.DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlHakAkses .= " AND apm.apmPartnerID = '{$_SESSION['PartnerID']}' ";
        } else {
            //cek ktv_access_staff
            $sqlHakAkses = " AND d.DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlHakAkses .= " AND apm.apmPartnerID = '1' #Partner Koltiva ";
        }
        //buat SqlHakAksesKontrol (end)

        $where = '';
        $params = array();
        $params[] = $FarmerTrainingID;
        if (!empty($prov)) {
            $where .= " AND p.ProvinceID = ?";
            $params[] = $prov;
        }
        if (!empty($kab)) {
            $where .= " AND d.DistrictID = ?";
            $params[] = $kab;
        }
        if (!empty($cpg)) {
            $where .= " AND c.CPGid = ?";
            $params[] = $cpg;
        }
        if (!empty($key)) {
            $where .= " AND (MemberName like ? OR MemberDisplayID=?)";
            $params[] = "%$key%"; $params[] = $key;
        }
        $params[] = intval($start);
        $params[] = intval($limit);
        $sql = "SELECT 
                    SQL_CALC_FOUND_ROWS
                    f.MemberID AS addFarmerID
                    , MemberDisplayID AS addFarmerDisplayID
                    , MemberName AS addFarmerName
                    , p.Province
                    , d.District
                    , sd.SubDistrict
                    , v.Village
                FROM 
                    ktv_members f
                LEFT JOIN 
                    ktv_village v ON v.VillageID = f.VillageID
                LEFT JOIN 
                    ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
                LEFT JOIN 
                    ktv_district d ON d.DistrictID = sd.DistrictID
                LEFT JOIN 
                    ktv_province p ON p.ProvinceID = d.ProvinceID
	            LEFT JOIN 
                    ktv_access_partner_member apm on apm.apmMemberID = f.MemberID
                INNER JOIN
                    ktv_member_role mr on mr.MemberID = f.MemberID AND MRoleID = 1 -- Role Petani
                WHERE
                    f.StatusCode = 'active' AND
                    f.MemberID NOT IN (
                        SELECT
                            FarmerID
                        FROM ktv_farmer_trainings_participants
                        WHERE
                            FarmerTrainingID = ? AND StatusCode='active'
                    )
                    --where--
                    $sqlHakAkses
                GROUP BY f.MemberID
                ORDER BY addFarmerName ASC
                LIMIT ?, ?";
        $sql = str_replace('--where--', $where, $sql);
        $query = $this->db->query($sql, $params);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $result['data'] = $query->result_array();
        //$query = $this->db->query($sql, array($cpgID,$CpgBatchTrainingID));

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;
        return $result;
    }

    function createParticipants($FarmerTrainingID, $participants, $userid) {

        $record = array();
        foreach ($participants as $participant) {
            $record[] = array(
                'FarmerTrainingID'      => $FarmerTrainingID,
                'FarmerID'              => $participant,
                'Subtitute'           => 1,
                'FamilyID'              => 0,
                'WritingAwal'           => 0,
                'WritingAkhir'          => 0,
                'BallotAwal'            => 0,
                'BallotAkhir'           => 0,
                'DateCreated'           => date("Y-m-d H:i:s"),
                'DateUpdated'           => date("Y-m-d H:i:s"),
                'CreatedBy'             => $userid,
                'LastModifiedBy'        => $userid,
            );
        }

        if (!$this->db->insert_batch('ktv_farmer_trainings_participants', $record)) {
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

    public function readCPGs($DistrictID)
    {
        $sql = "
SELECT 
    CPGid AS id
    , CONCAT(CPGid,' - ',GroupName) AS label
FROM
    ktv_cpg c
JOIN ktv_village v ON v.VillageID = c.VillageID
JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
WHERE
    c.Status = 'active'
    AND sd.DistrictID = ?
ORDER BY label
        ";
        $query = $this->db->query($sql, array($DistrictID));
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function getFarmerTrainings($District = '')
    {
        $sql = "SELECT
                t.training_id,
                t.DistrictID,
                t.topic,
                t.sub_topic,
                t.province,
                t.district,
                t.tot_location,
                t.start_date,
                t.end_date,
                t.TrainingDayStatus,
                t.facilitator1,
                t.facilitator2,
                t.farmer_group,
                t.batch,
                t.status,
                t.CertProgName,
                t.CertHolderOrgName,
                tp.FarmerID AS farmerid,
                tp.FarmerName AS farmername,
                t.country,
                t.durasi
            FROM
            (
                SELECT
                    ft.FarmerTrainingID training_id
                    , ft.TrainingDistrict DistrictID
                    , ct.CpgTrainings topic
                    , GROUP_CONCAT(ct2.CpgTrainings SEPARATOR ' | ') AS sub_topic
                    , p.Province province
                    , d.District district
                    , co.CountryName country
                    , ft.TotLocation tot_location
                    , ft.TrainingStart start_date
                    , ft.TrainingEnd end_date
                    , IF( TIMESTAMPDIFF(DAY,DATE(ft.TrainingStart),DATE(ft.TrainingEnd)) != '', TIMESTAMPDIFF(DAY,DATE(ft.TrainingStart),DATE(ft.TrainingEnd)) + 1, '') durasi
                    , ft.TrainingDayStatus
                    , s1.label AS facilitator1
                    , s2.label AS facilitator2
                    , '' farmer_group
                    , cb.BatchNumber batch
                    , ft.TrainingDays status
                    , cp.CertProgName
                    , vso.`Name` CertHolderOrgName
                FROM
                    ktv_farmer_trainings ft
                LEFT JOIN
                    ktv_farmer_trainings_sub_topics st on st.FarmerTrainingID = ft.FarmerTrainingID
                LEFT JOIN
                    ktv_cpg_trainings ct2 on ct2.CpgTrainingsID = st.SubCpgTrainingsID
                LEFT JOIN
                    ktv_cpg_trainings ct on ct.CpgTrainingsID = ft.CPGtrainingsID
                LEFT JOIN
                    ktv_district d on d.DistrictID = ft.TrainingDistrict
                LEFT JOIN
                    ktv_province p on p.ProvinceID = d.ProvinceID
                LEFT JOIN
                    ktv_country co on co.ISO2 = p.CountryCode
                LEFT JOIN
                    ktv_cpg_batch cb on cb.CpgBatchID = ft.CpgBatchID
                LEFT JOIN
                    ktv_ims ims on ims.IMSID = ft.IMSID
                LEFT JOIN
                    ktv_ims_master im on im.IMSMasterID = ims.IMSMasterID
                LEFT JOIN
                    ktv_certification_holders ch on ch.CertHolderID = im.CertHolderID
                LEFT JOIN
                    ktv_ref_certification_program cp on cp.CertProgID = ch.CertProgID
                LEFT JOIN
                    view_tc_supplychain_org vso on vso.SupplychainID = ch.SupplychainID
                LEFT JOIN (
                    SELECT
                        st.PersonID AS id,
                        CONCAT(
                            ps.`PersonNm`,
                        CASE
                                
                                WHEN st.`ObjType` = 'program' THEN
                                CONCAT( ' (PR/', part.PartnerName, ')' ) 
                                WHEN st.`ObjType` = 'private' THEN
                                CONCAT( ' (PS/', part.PartnerName, ')' ) 
                            END,
                            ' [',
                            IFNULL( rpos.PositionName, '-' ),
                            ']' 
                        ) AS label 
                    FROM
                        ktv_staffs st
                        INNER JOIN ktv_persons ps ON st.`PersonID` = ps.`PersonID`
                        LEFT JOIN ktv_program_partner part ON st.`ObjID` = part.`PartnerID`
                        LEFT JOIN ktv_staff_positions f ON st.`StaffID` = f.`StaffPosStaffID` 
                        AND ( CURDATE() BETWEEN f.`StaffPostStart` AND f.`StaffPostEnd` ) 
                        AND f.StatusCode = 'active'
                        LEFT JOIN ktv_ref_position_type rpos ON f.StaffPosPositionID = rpos.PositionID 
                    WHERE
                        st.`StatusCode` = 'active' 
                        AND ps.`StatusCd` = 'active' 
                        AND st.`ObjType` IN ( 'program', 'private' ) 
                        AND st.`StaffID` NOT IN ( '1', '2804' ) 
                    ORDER BY
                        label 
                ) s1 ON s1.id = ft.FacProgramPersonID
                LEFT JOIN ( 
                    SELECT
                        p.`PersonID` AS id
                        , CONCAT(p.`PersonNm`,' (',
                            IF(st.`ObjType`='private',
                            (SELECT subp.PartnerName FROM ktv_program_partner subp WHERE subp.PartnerID = st.`ObjID` LIMIT 1)
                        ,'Penyuluh')
                        ,') ',IFNULL(rpos.PositionName,'-')) AS label
                        FROM
                            ktv_staffs st
                        INNER JOIN ktv_persons p ON st.`PersonID` = p.`PersonID`        
                        LEFT JOIN ktv_staff_positions f ON st.`StaffID` = f.`StaffPosStaffID`
                            AND (CURDATE() BETWEEN f.`StaffPostStart` AND f.`StaffPostEnd`)
                            AND f.StatusCode = 'active'
                        LEFT JOIN ktv_ref_position_type rpos ON f.StaffPosPositionID = rpos.PositionID
                        WHERE 1=1
                            AND st.`StatusCode` != 'nullified'
                            AND p.`StatusCd` != 'nullified'
                            AND st.`ObjType` IN ('private','extension')
                        ORDER BY label 
                ) s2 ON s2.id = ft.FacPrivatePersonID
                WHERE
                    1=1
                AND
                    ft.TrainingStatus = 2
                AND 
                    ft.StatusCode = 'active'
                AND 
                    d.District = ?
                GROUP BY
                    ft.FarmerTrainingID
                ORDER BY ft.TrainingStart DESC
            ) t
            LEFT JOIN (
                SELECT
                    tp.FarmerTrainingID,
                    m.MemberID FarmerID,
                    m.MemberName FarmerName 
                FROM
                    ktv_farmer_trainings_participants tp
                    JOIN ktv_members m ON m.MemberID = tp.FarmerID 
                WHERE
                    tp.StatusCode = 'active' 
            ) tp ON tp.FarmerTrainingID = t.training_id 
            WHERE
                1 = 1";
        $query = $this->db->query($sql, array($District));
        return $query;
        // if ($query->num_rows()>0) {
        //     return $query->result_array();
        // }
        // return false;
    }

    public function checkFarmerTraining($training_id)
    {
        $query = $this->db->get_where('ktv_farmer_trainings', array('FarmerTrainingID' => $training_id, 'StatusCode' => 'active'), 1);
        if ($query->num_rows()>0) {
            return true;
        }
        return false;
    }

    public function checkParticipant($training_id, $farmer_id)
    {
        $query = $this->db->get_where('ktv_farmer_trainings_participants', array('FarmerTrainingID' => $training_id, 'FarmerID' => $farmer_id, 'StatusCode' => 'active'), 1);
        if ($query->num_rows()>0) {
            return true;
        }
        return false;
    }

    public function addParticipant($training_id, $farmer_id, $w_start, $w_end, $b_start, $b_end)
    {
        $data = array(
            'FarmerTrainingID'      => $training_id,
            'FarmerID'              => $farmer_id,
            'WritingAwal'           => $w_start,
            'WritingAkhir'          => $w_end,
            'BallotAwal'            => $b_start,
            'BallotAkhir'           => $b_end,
            'StatusCode'           => 'active',
        );
        return $this->db->insert('ktv_farmer_trainings_participants', $data);
    }

    public function editParticipant($training_id, $farmer_id, $w_start, $w_end, $b_start, $b_end)
    {
        $data = array(
            'WritingAwal'           => $w_start,
            'WritingAkhir'          => $w_end,
            'BallotAwal'            => $b_start,
            'BallotAkhir'           => $b_end,
        );
        $condition = array(
            'FarmerTrainingID'      => $training_id,
            'FarmerID'              => $farmer_id,
        );
        return $this->db->update('ktv_farmer_trainings_participants', $data, $condition);;
    }

    public function checkAttendance($training_id, $day_number)
    {
        $query = $this->db->get_where('ktv_farmer_trainings_daily_attendance', array('FarmerTrainingID' => $training_id, 'DayNumber' => $day_number), 1);
        if ($query->num_rows()>0) {
            return $query->row_array(0);
        }
        return false;
    }

    public function InsertAttachmentFiles($TrainID,$TrainType,$filepath){
        $sql = "INSERT INTO `ktv_training_attachment_files` SET
                `TrainID` = ?,
                `TrainType` = ?,
                `Filename` = ?,
                `StatusCode` = 'inactive'";
        $query = $this->db->query($sql,array($TrainID,$TrainType,$filepath));
        $TrainAttID = $this->db->insert_id();

        $return['success'] = true;
        $return['TrainAttID'] = $TrainAttID;
        return $return;
    }

    public function UpdateAttachmentFile($TrainAttID, $NamafileNya){
        $sql = "UPDATE ktv_training_attachment_files a SET
                    a.`Filename` = ?,
                    a.`DateUpdated` = NOW(),
                    a.`LastModifiedBy` = ?
                WHERE
                    a.`TrainAttID` = ?
                LIMIT 1
                ";
        $p = array(
            $NamafileNya,
            $_SESSION['userid'],
            $TrainAttID
        );
        return $this->db->query($sql,$p);
    }

    public function addAttendance($training_id, $day_number, $training_date, $file_attachement)
    {
        if (!empty($file_attachement)) {
            // $file = $this->createFileAttachment($file_attachement, "{$training_id} {$day_number}.jpg");
            $file = array();
            foreach ($file_attachment as $key => $val) {
                $file[] = $this->moveFileAttachment($val, null);
            }
            $file = implode(';', $file);
            $data = array(
                'FarmerTrainingID'      => $training_id,
                'DayNumber'             => $day_number,
                'TrainingDate'          => $training_date,
                'File'                  => $file,
            );
            return $this->db->insert('ktv_farmer_trainings_daily_attendance', $data);
        }else{
            return true;
        }
    }

    public function editAttendance($training_id, $day_number, $training_date, $file_attachment)
    {
        $training = $this->checkAttendance($training_id, $day_number);
        if (!empty($training['File'])) {
            // delete_file($training['File']);
            $tmp = explode(';', $training['File']);
            foreach ($tmp as $file) {
                delete_file($file);
            }
        }
        if (!empty($file_attachment)) {
            // $file = $this->createFileAttachment($file_attachment, "{$training_id} {$training_date}.jpg");
            $file = array();
            foreach ($file_attachment as $key => $val) {
                $file[] = $this->moveFileAttachment($val, null);
            }
            $file = implode(';', $file);
            $data = array(
                'TrainingDate'          => $training_date,
                'File'                  => $file,
            );
            $condition = array(
                'FarmerTrainingID'      => $training_id,
                'DayNumber'             => $day_number,
            );
            return $this->db->update('ktv_farmer_trainings_daily_attendance', $data, $condition);;
        }else{
            return true;
        }
    }   

    public function checkFarmerAttendance($training_id, $farmer_id, $day_number)
    {
        $query = $this->db->get_where('ktv_farmer_trainings_attendance', array('FarmerTrainingID' => $training_id, 'FarmerID' => $farmer_id, 'DayNumber' => $day_number), 1);
        if ($query->num_rows()>0) {
            return true;
        }
        return false;
    }

    public function addFarmerAttendance($training_id, $farmer_id, $family_id, $sub_name, $day_number, $training_date, $first, $second, $file_attachment = null)
    {
        if (!empty($file_attachment)) {
            $files = array();
            foreach ($file_attachment as $key => $val) {
                $files[$key] = $this->uploadSignAttendance($val, null);
            }
        }

        $data = array(
            'FarmerTrainingID'    => $training_id,
            'FarmerID'              => $farmer_id,
            'DayNumber'             => $day_number,
            'TrainingDate'          => $training_date,
            'Attendance1'           => $first,
            'SignAttendance1'       => @$files[1],
            'Attendance2'           => $second,
            'SignAttendance2'       => @$files[2],
        );
        if ($family_id !== false OR $sub_name !== false) {
            $data['Substitute']     = 1;
            $data['FamilyID']       = $family_id;
            $data['FamilyName']     = $sub_name;
        }
        return $this->db->insert('ktv_farmer_trainings_attendance', $data);
    }

    public function editFarmerAttendance($training_id, $farmer_id, $family_id, $sub_name, $day_number, $training_date, $first, $second, $file_attachment = null)
    {
        $this->load->library('awsfileupload');
        
        $attendance = $this->checkFarmerAttendance($training_id, $farmer_id, $day_number);
        if (!empty($attendance['SignAttendance1'])) {
            if($this->awsfileupload->doesObjectExist($attendance['SignAttendance1']) == true) {
                $this->awsfileupload->delete($attendance['SignAttendance1']);
            }
        }
        if (!empty($attendance['SignAttendance2'])) {
            if($this->awsfileupload->doesObjectExist($attendance['SignAttendance2']) == true) {
                $this->awsfileupload->delete($attendance['SignAttendance2']);
            }
        }
        if (!empty($file_attachment)) {
            $files = array();
            foreach ($file_attachment as $key => $val) {
                $files[$key] = $this->uploadSignAttendance($val, null);
            }
        }
        $data = array(
            'TrainingDate'          => $training_date,
            'Attendance1'           => $first,
            'SignAttendance1'       => @$files[1],
            'Attendance2'           => $second,
            'SignAttendance2'       => @$files[2],
        );
        if ($family_id !== false || $sub_name !== false) {
            $data['Substitute']     = 1;
            $data['FamilyID']       = $family_id;
            $data['FamilyName']     = $sub_name;
        }
        $condition = array(
            'FarmerTrainingID'      => $training_id,
            'FarmerID'              => $farmer_id,
            'DayNumber'             => $day_number,
        );
        return $this->db->update('ktv_farmer_trainings_attendance', $data, $condition);;
    }

    public function uploadSignAttendance($file, $filename)
    {
        $this->load->library('awsfileupload');

        $tmp        = explode("/", $file);
        $filename   = end($tmp);
        
        $upload = $this->awsfileupload->upload($file, $filename, AWSS3_TRAINING_FARMER_TTD_PATH, 'images');
            

        if ($upload['success'] == true) {
            unlink($file);
            return $upload['filenamepath'];
        }
    }
    
    public function getFarmerTrain($FarmerTrainingID) {
        $query = $this->db->get_where('ktv_farmer_trainings', array('FarmerTrainingID' => $FarmerTrainingID), 1);
        return $query->row_array();
    }
    
    public function getFarmerTrainingByUsername($username) {
        $sql = "SET SESSION group_concat_max_len = 1000000";
        $this->db->query($sql);
        $sql_get_access = "SELECT u.UserId, u.UserName, g.GroupId, g.GroupName, g.GroupDescription, GROUP_CONCAT(kas.DistrictID) district_access
                            FROM sys_user u
                            LEFT JOIN sys_user_group ug ON u.UserId = ug.UserGroupUserId
                            LEFT JOIN sys_group g ON ug.UserGroupGroupId = g.GroupId
                            LEFT JOIN ktv_access_staff kas ON kas.UserId = u.UserId
                            WHERE u.UserName = '{$username}'
                            GROUP BY u.UserId
                            LIMIT 1;";
        $access = $this->db->query($sql_get_access)->row_array();

        if($access['district_access'] == 0){
            return array();
        }

        $sql = "SELECT
                ft.FarmerTrainingID AS training_id,
                ct.CpgTrainings topic,
                GROUP_CONCAT( st.CpgTrainings ) sub_topic,
                p.ProvinceID,
                d.DistrictID,
                p.Province,
                d.District,
                ft.TotLocation,
                DATE( ft.TrainingStart ) start_date,
                DATE( ft.TrainingEnd ) end_date,
                CASE
                    WHEN ft.TrainingDayStatus = 'half' THEN
                        'Half day' 
                    WHEN ft.TrainingDayStatus = 'full' THEN
                        'Full day' ELSE '-' 
                    END STATUS,
                IFNULL( s1.label, '-' ) AS facilitator1,
                IFNULL( s2.label, '-' ) AS facilitator2,
                cb.BatchNumber,
                ft.TrainingDays duration 
            FROM
                ktv_farmer_trainings ft
                LEFT JOIN ktv_cpg_trainings ct ON ct.CpgTrainingsID = ft.CPGtrainingsID
                LEFT JOIN ktv_cpg_batch cb ON cb.CpgBatchID = ft.CpgBatchID
                LEFT JOIN ktv_district d ON d.DistrictID = ft.TrainingDistrict
                LEFT JOIN ktv_province p ON p.ProvinceID = ft.TrainingProvince
                LEFT JOIN ( SELECT fts.FarmerTrainingID, ct.CpgTrainings FROM ktv_farmer_trainings_sub_topics fts LEFT JOIN ktv_cpg_trainings ct ON fts.SubCpgTrainingsID = ct.CpgTrainingsID ) st ON st.FarmerTrainingID = ft.FarmerTrainingID
                LEFT JOIN (
                SELECT
                    ks1.PersonID AS id,
                    CONCAT( kp1.`PersonNm`, ' - ', part1.PartnerName ) AS label 
                FROM
                    ktv_staffs ks1
                    INNER JOIN ktv_persons kp1 ON kp1.PersonID = ks1.PersonID
                    LEFT JOIN ktv_program_partner part1 ON ks1.`ObjID` = part1.`PartnerID` 
                WHERE
                    ks1.`StatusCode` = 'active' 
                    AND kp1.StatusCd = 'active' 
                ORDER BY
                    label 
                ) s1 ON s1.id = ft.FacProgramPersonID
                LEFT JOIN (
                SELECT
                    ks2.PersonID AS id,
                    CONCAT( kp2.`PersonNm`, ' - ', part2.PartnerName ) AS label 
                FROM
                    ktv_staffs ks2
                    INNER JOIN ktv_persons kp2 ON kp2.PersonID = ks2.PersonID
                    LEFT JOIN ktv_program_partner part2 ON ks2.`ObjID` = part2.`PartnerID` 
                WHERE
                    ks2.`StatusCode` = 'active' 
                    AND kp2.StatusCd = 'active' 
                ORDER BY
                    label 
                ) s2 ON s2.id = ft.FacPrivatePersonID 
            WHERE
                1 = 1 
                AND ft.TrainingStatus = 2 -- Ongoing
                AND d.DistrictID IN ({$access['district_access']})                
            GROUP BY
                ft.FarmerTrainingID 
            ORDER BY
                ft.TrainingStart DESC";

        $query  = $this->db->query($sql);
        $data   = $query->result_array();
        if($query->num_rows()>0){            
            foreach($data as $row => $list){
                $sql = "SELECT
                        tp.FarmerTrainingID
                        , tp.FarmerTrainingsFarmerID AS ParticipantD
                        , tp.FarmerID
                        , s.MemberDisplayID
                        , s.MemberName
                        , s.Gender
                        , IFNULL(fg.GroupName, s.groupName) FarmerGroup
                    FROM ktv_farmer_trainings_participants tp
                    JOIN ktv_members s ON s.MemberID = tp.FarmerID
                    LEFT JOIN ktv_farmer_group fg on fg.FarmerGroupID = s.FarmerGroupID
                    WHERE 1=1
                        AND s.StatusCode = 'active'
                    AND tp.FarmerTrainingID = ?";
                
                $query2 = $this->db->query($sql, array($list["training_id"]));
                $datapart = $query2->result_array();
                if($query2->num_rows()>0){
                    foreach($query2->result_array() as $rows => $datas){
                        $sqlFam = "SELECT 
                                fl.FamLabID
                                , fl.FamLabName 
                                , fl.FamLabRelation
                                , fl.Gender
                                , CASE 
                                    WHEN fl.FamLabRelation = 1 THEN 'Spouse'
                                    WHEN fl.FamLabRelation = 2 THEN 'Child'
                                    WHEN fl.FamLabRelation = 3 THEN 'Other'
                                    ELSE '-'
                                END FamLabRelationText
                            FROM 
                                ktv_member_family_labour fl 
                            WHERE fl.MemberID = ? AND fl.StatusCode = 'active'";
                        $queryFam = $this->db->query($sqlFam, array($datas['FarmerID']));
        
                        $datapart[$rows]['Family'] = $queryFam->result_array();
        
                        $sqlLab = "SELECT
                                ml.LaboID FamLabID
                                , ml.LaboName FamLabName
                                , 'Labour' FamLabRelation
                                , ml.Gender
                            FROM
                                ktv_member_labour ml
                            WHERE
                                ml.StatusCode = 'active' 
                            AND ml.MemberID = ?";
                        $queryLab = $this->db->query($sqlLab, array($datas['FarmerID']));
        
                        $datapart[$rows]['Labour'] = $queryLab->result_array();
                    }
                }
                $data[$row]["participant"] = $datapart;
            }
        }

        return $data;
    }
    
    public function AddLogUploadFarmerTraining($UploadFile) {
        $FileZip = $UploadFile['filenamepath'];

        $sql = "INSERT INTO `log_upload_farmer_training` SET
                        `filezip` = ?,
                        `statusProses` = 'Belum',
                        `statusFile` = 'backup',
                        `remark` = 'Backup file upload',
                        `waktuProses` = NOW()";
        $p = array($FileZip);
        $query = $this->db->query($sql, $p);
        $logId = $this->db->insert_id();
        return $logId;
    }

    public function getUser($username) {
        $data = array();

        $sql = "SELECT
                    a.UserId
                    , a.UserRealName
                    , a.UserName
                    , a.UserEmail
                FROM sys_user a
                WHERE a.UserName = ?
                LIMIT 1
                ";
        $p = array(
            $username
        );
        $data = $this->db->query($sql, $p)->row_array();

        return $data;
    }

    public function deleteAttachmentFile($training_id){
        $this->load->library('awsfileupload');
        
        $sql    = "SELECT * FROM ktv_training_attachment_files WHERE TrainID = ? AND TrainType = 'farmer'";
        $query  = $this->db->query($sql, array($training_id));
        if($query->num_rows()>0){
            foreach($query->result_array() as $row){
                if($this->awsfileupload->doesObjectExist($row['Filename']) == true) {
                    $this->awsfileupload->delete($row['Filename']);
                }
            }

            $this->db->where("TrainID", $training_id);
            $this->db->where("TrainType", "farmer");
            $this->db->delete("ktv_training_attachment_files");
        }
    }

    public function MobileUpdateFileAttachment($filename, $filepath, $FarmerID, $TrainFarmerID, $UserID){
        $this->load->library('awsfileupload');

        $tmp = explode(".", $filename);
        $ext = $tmp[1];
        
        if($ext == "pdf"){
            $upload = $this->awsfileupload->upload($filepath, $filename, AWSS3_TRAINING_FILE_PATH, 'documents');
        }else{
            $upload = $this->awsfileupload->upload($filepath, $filename, AWSS3_TRAINING_IMAGE_PATH, 'images');
        }

        if ($upload['success'] == true) {
            $data["TrainID"]            = $TrainFarmerID;
            $data["TrainType"]          = "farmer";
            $data["Filename"]           = $upload['filenamepath'];
            $data["FilenameBackup"]     = $filename;
            $data["Remark"]             = 'Upload From Mobile';
            $data["StatusCode"]         = "active";
            $data["CreatedBy"]          = $UserID;
            $data["DateCreated"]        = date("Y-m-d H:i:s");

            $this->db->insert("ktv_training_attachment_files", $data);
        }
    }

    public function MobileUpdateAttendances($TrainFarmerID, $DayTrain, $TrainDateProcess, $ParAttendances, $UserID, $DirProcess) {
        $this->load->library('awsfileupload');
        $result = array();
        //echo '<pre>'; print_r(array($TrainFarmerID,$TrainDateProcess,$ParAttendances)); exit;

        //Cek file ttd kehadiran ======================= (Begin)
        $files_upload = array_slice(scandir($DirProcess), 2);
        $ArrFilesTtd = array();
        foreach ($files_upload as $file) {
            $ArrTmpFile = explode("_",$file);
            if($ArrTmpFile[2] == "attendance" AND $ArrTmpFile[4] != '') {
                $KeyFile = "attendance_".$ArrTmpFile[3]."_".$ArrTmpFile[4]."_".$ArrTmpFile[1]."_session1";
                $ArrFilesTtd[$KeyFile] = $file;
            }
        }
        //Cek file ttd kehadiran ======================= (End) 

        $this->db->trans_begin();
        
        foreach ($ParAttendances as $key => $paratt) {
            $FarmerID = $paratt['farmer_id']; // Yg dikirim mobile bukan participant_id/FarmerTrainingsFarmerID tapi ini FarmerID
            //Jika pakai subs
            if($paratt['substitute_name'] != "") {
                $sql = "UPDATE `ktv_farmer_trainings_attendance` a SET
                            a.`Substitute` = ?,
                            a.FamilyName = ?,
                            a.DateUpdated = NOW(),
                            a.LastModifiedBy = ?
                        WHERE
                            a.FarmerTrainingID = ?
                            AND a.FarmerID = ?
                        LIMIT 1";
                $p = array(
                    1,
                    $paratt['substitute_name'],
                    $UserID,
                    $TrainFarmerID,
                    $FarmerID
                );
                $query = $this->db->query($sql,$p);
            }

            //update attendances
            $sql = "UPDATE ktv_farmer_trainings_attendance a SET
                        a.`Attendance1` = ?,
                        a.DateUpdated = NOW(),
                        a.LastModifiedBy = ?
                    WHERE
                        a.FarmerTrainingID = ?
                        AND a.`FarmerID` = ?
                        AND a.`DayNumber` = ?
                    LIMIT 1";
            $p = array(
                $paratt['first_session'],
                $UserID,
                $TrainFarmerID,
                $FarmerID,
                $DayTrain
            );
            $query = $this->db->query($sql,$p);

            //Cek Ttd nya ============================== (Begin)
            $KeyTtdCheck = "attendance_".$FarmerID."_".$TrainFarmerID."_".$DayTrain."_session1";
            if($ArrFilesTtd[$KeyTtdCheck] != "") {
                if(file_exists($DirProcess."/".$ArrFilesTtd[$KeyTtdCheck])) {
                    //echo "$DirProcess/$ArrFilesTtd[$KeyTtdCheck]<br>";

                    //namafile
                    $filepathinfo = pathinfo($DirProcess."/".$ArrFilesTtd[$KeyTtdCheck]);
                    $namafile = $filepathinfo['basename'];

                    $UploadTtd = $this->awsfileupload->upload($DirProcess."/".$ArrFilesTtd[$KeyTtdCheck], $namafile, AWSS3_TRAINING_FARMER_TTD_PATH, 'images');
                    if ($UploadTtd['success'] == true) {
                        $sql = "SELECT * 
                                FROM ktv_farmer_trainings_attendance 
                                WHERE FarmerTrainingID = ?
                                    AND `FarmerID` = ?
                                    AND `DayNumber` = ? 
                                LIMIT 1";
                        $p = array($TrainFarmerID, $FarmerID, $DayTrain);
                        $DataCek = $this->db->query($sql,$p)->row_array();
                        
                        if (isset($DataCek['SignAttendance1']) && $DataCek['SignAttendance1'] != "") {
                            $this->load->library('awsfileupload');
                            $this->awsfileupload->delete($DataCek['SignAttendance1']);
                        }

                        $FileTtd = $UploadTtd['filenamepath'];
                        $sql = "UPDATE ktv_farmer_trainings_attendance a SET
                                    a.`SignAttendance1` = ?,
                                    a.DateUpdated = NOW(),
                                    a.LastModifiedBy = ?
                                WHERE
                                    a.FarmerTrainingID = ?
                                    AND a.`FarmerID` = ?
                                    AND a.`DayNumber` = ?
                                LIMIT 1";
                        $p = array(
                            $FileTtd,
                            $UserID,
                            $TrainFarmerID,
                            $FarmerID,
                            $DayTrain
                        );
                        $query = $this->db->query($sql,$p);
                    }
                }
            }
            //Cek Ttd nya ============================== (End)

        }

//        TrainingCalculateAttendancePercentage($TrainFarmerID,'farmer');

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
    
    public function UpdateLogUpload($logId) {
        $sql = "UPDATE `log_upload_farmer_training` a SET
                    a.statusProses = 'Sudah',
                    a.`remark` = 'Data sudah terproses'
                WHERE
                    a.logId = ?
                LIMIT 1";
        $p = array(
            $logId
        );
        $query = $this->db->query($sql, $p);
    }

}

?>
