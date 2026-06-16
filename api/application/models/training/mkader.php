<?php

class Mkader extends CI_Model {
    public $user;

    public function __construct()
    {
        parent::__construct();
        $this->user = $this->muserprofile->getUserProfile();
    }
    function readDatas($key, $prov, $dist, $subdist, $start, $limit) {
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
            $sqlHakAkses = " AND kd.DistrictID IN (".$_SESSION['daerah_access'].")";
        } else {
            //cek ktv_access_staff
            $sqlHakAkses = " AND kd.DistrictID IN (".$_SESSION['daerah_access'].")";
        }
        //buat SqlHakAksesKontrol (end)

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
            a.CpgKaderTrainingID as id,CpgTrainings as training,
            District as tot,count(CpgKaderTrainingsFarmerID) as participant,date(TrainingStart) as start,date(TrainingEnd) as end,
            TrainingDays as days
            from ktv_kader_trainings a
            left join ktv_district kd on a.TrainingDistrict=kd.DistrictID
            left join ktv_cpg_trainings b on a.CPGtrainingsID=b.CpgtrainingsID
            left join ktv_kader_trainings_participants d on a.CpgKaderTrainingID=d.CpgKaderTrainingID AND d.`StatusCode` = 'active'
            WHERE a.StatusCode != 'nullified' AND (CpgTrainings like ? OR a.CpgKaderTrainingID like ?)
                $sqlHakAkses
                $where
            GROUP BY a.CpgKaderTrainingID
            LIMIT ?,?";
        $query = $this->db->query($sql, array("%$key%", $key, (int) $start, (int) $limit));
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $result['data'] = $query->result_array();
        $query = $this->db->query('SELECT FOUND_ROWS() as total');
        $result['total'] = $query->row()->total;
        return $result;
    }

    function readData($id) {
        $sql = "SELECT a.*,DATE(TrainingStart) AS TrainingStart,DATE(TrainingEnd) AS TrainingEnd, GROUP_CONCAT(sub.`SubCpgTrainingsID` SEPARATOR ',') AS subtopics, b.`Province`, c.`District`
            FROM ktv_kader_trainings a
            LEFT JOIN ktv_province b ON a.TrainingProvince=b.ProvinceID
            LEFT JOIN ktv_district c ON a.TrainingDistrict=c.DistrictID
            LEFT JOIN `ktv_kader_trainings_sub_topics` sub ON a.`CpgKaderTrainingID` = sub.`CpgKaderTrainingID`
            WHERE a.CpgKaderTrainingID=?";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

    function createData($training, $batch, $staf, $start, $end, $location, $staffPs, $days, $district, $provinsi, $userid, $CpgTrainingsIDSubTopic, $TrainingDayStatus) {
        $sql = "INSERT INTO ktv_kader_trainings (CPGtrainingsID, CpgBatchID, FacProgramPersonID, TrainingStart, TrainingEnd,
               TrainingDistrict, TrainingProvince,TotLocation,FacPrivatePersonID,TrainingDays, DateCreated,CreatedBy,
               DateUpdated,LastModifiedBy,TrainingDayStatus)
            SELECT ?,?,?,?,?,   b.DistrictID,a.ProvinceID,?,?,?,now(),?,now(),?,? from ktv_province a
            left join ktv_district b on a.ProvinceID=b.ProvinceID and District=? where Province=?";
        $stat = $this->db->query($sql, array($training, $batch, $staf, $start, $end, $location, $staffPs, $days,$userid, $userid, $TrainingDayStatus, $district, $provinsi));

        if ($stat) {
            $CpgKaderTrainingID = $this->db->insert_id();

            //sub topic (begin)
            if($CpgTrainingsIDSubTopic[0] != ""){
                foreach ($CpgTrainingsIDSubTopic as $key => $value) {
                    $sql="INSERT INTO `ktv_kader_trainings_sub_topics` SET
                          `CpgKaderTrainingID` = ?,
                          `SubCpgTrainingsID` = ?,
                          `DateCreated` = NOW(),
                          `CreatedBy` = ?";
                    $p = array(
                        $CpgKaderTrainingID,
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

    function updateData($training, $batch, $staf, $start, $end, $location, $staffPs, $days, $district, $provinsi, $id, $userid, $CpgTrainingsIDSubTopic, $TrainingDayStatus) {
        $sql = "UPDATE ktv_kader_trainings
            SET CPGtrainingsID=?, CpgBatchID=?, FacProgramPersonID=?, TrainingStart=?, TrainingEnd=?, TrainingDistrict=
               (SELECT DistrictID FROM ktv_district WHERE District=?),
               TrainingProvince=(SELECT ProvinceID FROM ktv_province WHERE Province=?),TotLocation=?,FacPrivatePersonID=?,
               TrainingDays=?, DateUpdated=now(), LastModifiedBy=?, TrainingDayStatus = ?
            WHERE CpgKaderTrainingID=?";
        $stat = $this->db->query($sql, array($training, $batch, $staf, $start, $end, $district, $provinsi, $location, $staffPs,$days, $userid, $TrainingDayStatus, $id));

        if ($stat) {
            $sql="DELETE FROM ktv_kader_trainings_sub_topics WHERE CpgKaderTrainingID = ?";
            $query = $this->db->query($sql,array($id));

            if($CpgTrainingsIDSubTopic[0] != ""){
                foreach ($CpgTrainingsIDSubTopic as $key => $value) {
                    $sql="INSERT INTO `ktv_kader_trainings_sub_topics` SET
                          `CpgKaderTrainingID` = ?,
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

            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deleteData($id) {
        //$sql = "DELETE FROM ktv_kader_trainings WHERE CpgKaderTrainingID=?";
        $sql="UPDATE ktv_kader_trainings SET StatusCode = 'nullified',LastModifiedBy='".$_SESSION['userid']."',DateUpdated=NOW() WHERE CpgKaderTrainingID = ? LIMIT 1";
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
            SELECT %s
            FROM ktv_kader_trainings_participants a
            LEFT JOIN ktv_kader_trainings ac on a.CpgKaderTrainingID=ac.CpgKaderTrainingID
            LEFT JOIN ktv_cpg_batch ae on ae.CpgBatchID=ac.CpgBatchID
            LEFT JOIN ktv_members b on b.StatusCode='active' and b.MemberID=a.FarmerID
            LEFT JOIN ktv_family c on a.FamilyID=c.FamilyID
   			LEFT JOIN ktv_village d ON b.VillageID = d.VillageID
   			LEFT JOIN ktv_subdistrict e ON d.SubDistrictID = e.SubDistrictID
            WHERE a.CpgKaderTrainingID=? AND a.StatusCode != 'nullified'
            GROUP BY CpgKaderTrainingsFarmerID
            ORDER BY MemberName
             %s";
        $query = $this->db->query(sprintf($sql, 'CpgKaderTrainingsFarmerID as participant_id,b.MemberID as farmer_id,b.MemberDisplayID as farmer_display_id,
            MemberName as farmer, SubDistrict as Kecamatan, Gender, AnggotaName, AnggotaGender,
            IF(PetaniKakao="1","Ya","Tidak") as participant,PetaniKakao,AnggotaName as if_no,a.FamilyID,
            WritingAwal as wstart,WritingAkhir as wend,BallotAwal as bstart,BallotAkhir as bend,ae.PartnerID', ''), array($id, (int) $start, (int) $limit));
        $result['data'] = $query->result_array();
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
            insert into ktv_kader_trainings_participants (CpgKaderTrainingID, FarmerID, PetaniKakao, FamilyID,
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
            UPDATE ktv_kader_trainings_participants
            SET FarmerID=?, PetaniKakao=?, FamilyID=?, WritingAwal=?, WritingAkhir=?, BallotAwal=?, BallotAkhir=?,
               DateUpdated=now(), LastModifiedBy=?
            WHERE CpgKaderTrainingsFarmerID=?";
        $stat = $this->db->query($sql, array($farmer, $petani, $family, $wstart, $wend, $bstart, $bend, $userid, $id));
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
        //$sql = "DELETE FROM ktv_kader_trainings_participants WHERE CpgKaderTrainingsFarmerID=?";
        $sql="UPDATE ktv_kader_trainings_participants SET StatusCode = 'nullified',LastModifiedBy='".$_SESSION['userid']."',DateUpdated=NOW() WHERE CpgKaderTrainingsFarmerID = ? LIMIT 1";
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

    function readFarmers($prov, $search = '') {
        if ($search != '')
            $add_sql = "AND (MemberDisplayID like '%$search%' OR MemberName like '%$search%')";
        if ($prov) {
            $add_sql .= " AND f.ProvinceID={$prov}";
        }

        //buat SqlHakAksesKontrol (begin)
        if($_SESSION['is_admin'] == "1"){
            $sqlHakAkses = " ";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            //cek ktv_access_staff
            $sqlHakAkses = " AND f.DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlHakAkses .= " AND a.PartnerID = '{$_SESSION['PartnerID']}' ";
        } else {
            //cek ktv_access_staff
            $sqlHakAkses = " AND f.DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlHakAkses .= " AND a.PartnerID = '1' #Partner Koltiva ";
        }
        //buat SqlHakAksesKontrol (end)

        $sql = "
            SELECT MemberID as id,CONCAT(MemberDisplayID,' - ',MemberName) as label
            FROM ktv_members a
            LEFT JOIN ktv_village d on a.VillageID=d.VillageID
            LEFT JOIN ktv_subdistrict e on d.SubDistrictID=e.SubDistrictID
            LEFT JOIN ktv_district f on e.DistrictID=f.DistrictID
            WHERE a.StatusCode='active' $add_sql $sqlHakAkses
            ORDER BY MemberName";
        $query = $this->db->query($sql, array($prov));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $result['data'] = $query->result_array();
        return $result;
    }

    function readFamilys($farmer) {
        $sql = "
            select FamilyID as id,AnggotaName as label
            from ktv_family a
            WHERE FarmerID=?
            ORDER BY AnggotaName";
        $query = $this->db->query($sql, array($farmer));
        $result['data'] = $query->result_array();
        return $result;
    }

    function checkFarmer($training, $farmer) {
        $sql = "
            SELECT FarmerId as id FROM ktv_kader_trainings_participants WHERE CpgKaderTrainingID=? and FarmerID=?";
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
            SELECT %s
            from ktv_kader_trainings_participants a
            left join ktv_members b on b.StatusCode='active' and b.MemberID=a.FarmerID
            left join ktv_family c on a.FamilyID=c.FamilyID
			LEFT JOIN ktv_village d ON b.VillageID = d.VillageID
			LEFT JOIN ktv_subdistrict e ON d.SubDistrictID = e.SubDistrictID
            WHERE CpgKaderTrainingID=? AND a.StatusCode != 'nullified'
            ORDER BY MemberName ASC %s";
        $query = $this->db->query(sprintf($sql, "a.CpgKaderTrainingID,b.MemberDisplayID as pFarmerID,
            a.FamilyID,AnggotaName,AnggotaGender,MemberName as PersonNm,
            Gender, SubDistrict as Kecamatan", ''), array($key));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql, 'count(*) as total', ''), array($key));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function readTraining($id) {
        $sql = "SELECT j.CpgTrainings, a.CpgKaderTrainingID,e.PersonNm AS koordinator,fp.PersonNm AS private_staff, a.TotLocation,
               c.District AS Kabupaten,c.DistrictID,b.Province AS Provinsi, g.PartnerName AS Partner,
                   h.PartnerName AS PrivateStaffPartner, i.BatchNumber, DATE_FORMAT(a.TrainingStart,'%d - %b - %Y') AS TrainingStart,
                   DATE_FORMAT(a.TrainingEnd,'%d - %b - %Y') AS TrainingEnd
                   , a.`TrainingDayStatus`
                   , GROUP_CONCAT(subtr.`CpgTrainings` SEPARATOR ', ') AS subtopics
            FROM
            ktv_kader_trainings a
            LEFT JOIN ktv_province b ON a.TrainingProvince=b.ProvinceID
            LEFT JOIN ktv_district c ON a.TrainingDistrict=c.DistrictID
            LEFT JOIN ktv_persons e ON e.PersonID = a.FacProgramPersonID
            LEFT JOIN ktv_program_staff d ON e.PersonID = d.PersonID
            LEFT JOIN ktv_persons fp ON fp.PersonID = a.FacPrivatePersonID
            LEFT JOIN ktv_private_staff f ON f.PersonID = fp.PersonID
            LEFT JOIN ktv_program_partner g ON g.PartnerID=d.PartnerID
            LEFT JOIN ktv_program_partner h ON h.PartnerID=f.PartnerID
            LEFT JOIN ktv_cpg_batch i ON a.CpgBatchID=i.CpgBatchID
            LEFT JOIN ktv_cpg_trainings j ON a.CPGtrainingsID=j.CpgtrainingsID
            LEFT JOIN ktv_kader_trainings_sub_topics sub ON a.`CpgKaderTrainingID` = sub.`CpgKaderTrainingID`
            LEFT JOIN ktv_cpg_trainings subtr ON sub.`SubCpgTrainingsID` = subtr.`CpgTrainingsID`
            WHERE a.CpgKaderTrainingID=?
            LIMIT 1";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

    function readPartnerLogo($id) {
        $sql = "
            select distinct c.Photo

            from ktv_kader_trainings a,
            ktv_cpg_batch b,
            ktv_program_partner c
            where a.CpgBatchID = b.CpgBatchID
            and b.PartnerID = c.PartnerID
            and a.CpgKaderTrainingID =? ";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

}

?>
