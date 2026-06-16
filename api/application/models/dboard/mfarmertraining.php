<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mfarmertraining extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    private function truncateTable($namaTabel){
        $sql="DELETE FROM `$namaTabel`";
        $query = $this->db->query($sql);
    }

    public function generateDash(){
        $this->db->trans_begin();

        //truncate dl tabelnya
        $this->db->truncate('dash_farmer_training');

        //isi datanya
        $sql="
INSERT INTO dash_farmer_training
SELECT
    sd.SubDistrictID
    , sd.SubDistrict
    , d.DistrictID
    , d.District
    , p.ProvinceID
    , p.Province
    , `year`
    , COUNT(DISTINCT IF(TrainingID=1,t.FarmerID,NULL)) gap
    , COUNT(DISTINCT IF(TrainingID=1 AND m.Gender = 'm',t.FarmerID,NULL)) gap_male
    , COUNT(DISTINCT IF(TrainingID=1 AND m.Gender = 'f',t.FarmerID,NULL)) gap_female
    , SUM(planned) AS planned
    , SUM(IF(m.Gender = 'm', planned, 0)) AS planned_male
    , SUM(IF(m.Gender = 'f', planned, 0)) AS planned_female
    , SUM(attended) AS attended
    , SUM(IF(m.Gender = 'm', attended, 0)) AS attended_male
    , SUM(IF(m.Gender = 'f', attended, 0)) AS attended_female
    , SUM(IF(m.Gender = 'm',planned_1,0)) AS planned_1_male
    , SUM(IF(m.Gender = 'f',planned_1,0)) AS planned_1_female
    , SUM(IF(m.Gender = 'm',attended_1,0)) AS attended_1_male
    , SUM(IF(m.Gender = 'f',attended_1,0)) AS attended_1_female
    , SUM(IF(m.Gender = 'm',planned_2,0)) AS planned_2_male
    , SUM(IF(m.Gender = 'f',planned_2,0)) AS planned_2_female
    , SUM(IF(m.Gender = 'm',attended_2,0)) AS attended_2_male
    , SUM(IF(m.Gender = 'f',attended_2,0)) AS attended_2_female
    , SUM(IF(m.Gender = 'm',planned_3,0)) AS planned_3_male
    , SUM(IF(m.Gender = 'f',planned_3,0)) AS planned_3_female
    , SUM(IF(m.Gender = 'm',attended_3,0)) AS attended_3_male
    , SUM(IF(m.Gender = 'f',attended_3,0)) AS attended_3_female
    , SUM(IF(m.Gender = 'm',planned_4,0)) AS planned_4_male
    , SUM(IF(m.Gender = 'f',planned_4,0)) AS planned_4_female
    , SUM(IF(m.Gender = 'm',attended_4,0)) AS attended_4_male
    , SUM(IF(m.Gender = 'f',attended_4,0)) AS attended_4_female
    , SUM(IF(m.Gender = 'm',planned_5,0)) AS planned_5_male
    , SUM(IF(m.Gender = 'f',planned_5,0)) AS planned_5_female
    , SUM(IF(m.Gender = 'm',attended_5,0)) AS attended_5_male
    , SUM(IF(m.Gender = 'f',attended_5,0)) AS attended_5_female
    , SUM(IF(m.Gender = 'm',planned_6,0)) AS planned_6_male
    , SUM(IF(m.Gender = 'f',planned_6,0)) AS planned_6_female
    , SUM(IF(m.Gender = 'm',attended_6,0)) AS attended_6_male
    , SUM(IF(m.Gender = 'f',attended_6,0)) AS attended_6_female
    , SUM(IF(m.Gender = 'm',planned_7,0)) AS planned_7_male
    , SUM(IF(m.Gender = 'f',planned_7,0)) AS planned_7_female
    , SUM(IF(m.Gender = 'm',attended_7,0)) AS attended_7_male
    , SUM(IF(m.Gender = 'f',attended_7,0)) AS attended_7_female
    , SUM(IF(m.Gender = 'm',planned_8,0)) AS planned_8_male
    , SUM(IF(m.Gender = 'f',planned_8,0)) AS planned_8_female
    , SUM(IF(m.Gender = 'm',attended_8,0)) AS attended_8_male
    , SUM(IF(m.Gender = 'f',attended_8,0)) AS attended_8_female
    , SUM(mt_70) AS mt_70
    , NOW() AS DateUpdated
FROM (
SELECT *
FROM (
    SELECT
       ftp.FarmerID
       , ft.CPGtrainingsID TrainingID
       , YEAR(ft.`TrainingStart`) AS `year`
       , 8 AS planned
       , attended
       , 1 AS planned_1
       , attended_1
       , 1 AS planned_2
       , attended_2
       , 1 AS planned_3
       , attended_3
       , 1 AS planned_4
       , attended_4
       , 1 AS planned_5
       , attended_5
       , 1 AS planned_6
       , attended_6
       , 1 AS planned_7
       , attended_7
       , 1 AS planned_8
       , attended_8
       , IF(attended/8 >= 0.7, 1, 0) AS mt_70
    FROM
       `ktv_farmer_trainings_participants` ftp
    JOIN `ktv_farmer_trainings` ft ON ft.FarmerTrainingID = ftp.FarmerTrainingID
    LEFT JOIN (
        SELECT
            ta.FarmerID
            , ta.FarmerTrainingID
            , SUM(IF(ta.Attendance1 = 1, 1, 0)) AS attended
            , SUM(IF(ta.DayNumber = 1 AND ta.Attendance1 = 1, 1, 0)) AS attended_1
            , SUM(IF(ta.DayNumber = 2 AND ta.Attendance1 = 1, 1, 0)) AS attended_2
            , SUM(IF(ta.DayNumber = 3 AND ta.Attendance1 = 1, 1, 0)) AS attended_3
            , SUM(IF(ta.DayNumber = 4 AND ta.Attendance1 = 1, 1, 0)) AS attended_4
            , SUM(IF(ta.DayNumber = 5 AND ta.Attendance1 = 1, 1, 0)) AS attended_5
            , SUM(IF(ta.DayNumber = 6 AND ta.Attendance1 = 1, 1, 0)) AS attended_6
            , SUM(IF(ta.DayNumber = 7 AND ta.Attendance1 = 1, 1, 0)) AS attended_7
            , SUM(IF(ta.DayNumber = 8 AND ta.Attendance1 = 1, 1, 0)) AS attended_8
        FROM ktv_farmer_trainings_attendance ta
        WHERE 
            1 = 1 AND ta.FarmerID > 0
        GROUP BY ta.FarmerID, FarmerTrainingID
    ) att ON att.FarmerID = ftp.FarmerID AND att.FarmerTrainingID = ft.FarmerTrainingID
    WHERE ft.TrainingStart > 0 AND ft.CPGtrainingsID = 1 AND ft.`StatusCode` = 'active'
    ORDER BY TrainingID,FarmerID
) m WHERE FarmerID GROUP BY FarmerID, TrainingID
) t
JOIN ktv_members m ON m.MemberID = t.FarmerID
LEFT JOIN ktv_village v ON v.VillageID = m.VillageID
LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
LEFT JOIN ktv_district d ON d.DistrictID = sd.DistrictID
LEFT JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
WHERE
   m.StatusCode = 'active'
GROUP BY sd.SubDistrictID,`year`
        ";
        $query = $this->db->query($sql);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Success";
        }
        return $results;
    }

    public function getDisplay($ProvinceID,$DistrictID)
    {
        //buat SqlHakAksesKontrol (begin)
        if($_SESSION['is_admin'] == "1"){
            $sqlHakAkses = "";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            //cek ktv_access_staff
            $sqlHakAkses = " AND d.DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlHakAkses .= "";
        } else {
            //cek ktv_access_staff
            $sqlHakAkses = " AND d.DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlHakAkses .= "";
        }
        //buat SqlHakAksesKontrol (end)

        $result     = array();
        $result['detail'] = array();
        $where      = '';
        $params     = array();
        $sql = "
SELECT
    %s AS label
    , IFNULL(SUM(gap),0) AS gap
    , IFNULL(SUM(gap_male),0) AS gap_male
    , IFNULL(SUM(gap_female),0) AS gap_female
    , IFNULL(SUM(IF(`year`=2010,gap,0)),0) AS gap_2010
    , IFNULL(SUM(IF(`year`=2011,gap,0)),0) AS gap_2011
    , IFNULL(SUM(IF(`year`=2012,gap,0)),0) AS gap_2012
    , IFNULL(SUM(IF(`year`=2013,gap,0)),0) AS gap_2013
    , IFNULL(SUM(IF(`year`=2014,gap,0)),0) AS gap_2014
    , IFNULL(SUM(IF(`year`=2015,gap,0)),0) AS gap_2015
    , IFNULL(SUM(IF(`year`=2016,gap,0)),0) AS gap_2016
    , IFNULL(SUM(IF(`year`=2017,gap,0)),0) AS gap_2017
    , IFNULL(SUM(IF(`year`=2018,gap,0)),0) AS gap_2018
    , SUM(planned) AS planned
    , SUM(planned_male) AS planned_male
    , SUM(planned_female) AS planned_female
    , SUM(attended) AS attended
    , SUM(attended_male) AS attended_male
    , SUM(attended_female) AS attended_female
    , SUM(planned_1_male) AS planned_1_male
    , SUM(planned_1_female) AS planned_1_female
    , SUM(attended_1_male) AS attended_1_male
    , SUM(attended_1_female) AS attended_1_female
    , SUM(planned_2_male) AS planned_2_male
    , SUM(planned_2_female) AS planned_2_female
    , SUM(attended_2_male) AS attended_2_male
    , SUM(attended_2_female) AS attended_2_female
    , SUM(planned_3_male) AS planned_3_male
    , SUM(planned_3_female) AS planned_3_female
    , SUM(attended_3_male) AS attended_3_male
    , SUM(attended_3_female) AS attended_3_female
    , SUM(planned_4_male) AS planned_4_male
    , SUM(planned_4_female) AS planned_4_female
    , SUM(attended_4_male) AS attended_4_male
    , SUM(attended_4_female) AS attended_4_female
    , SUM(planned_5_male) AS planned_5_male
    , SUM(planned_5_female) AS planned_5_female
    , SUM(attended_5_male) AS attended_5_male
    , SUM(attended_5_female) AS attended_5_female
    , SUM(planned_6_male) AS planned_6_male
    , SUM(planned_6_female) AS planned_6_female
    , SUM(attended_6_male) AS attended_6_male
    , SUM(attended_6_female) AS attended_6_female
    , SUM(planned_7_male) AS planned_7_male
    , SUM(planned_7_female) AS planned_7_female
    , SUM(attended_7_male) AS attended_7_male
    , SUM(attended_7_female) AS attended_7_female
    , SUM(planned_8_male) AS planned_8_male
    , SUM(planned_8_female) AS planned_8_female
    , SUM(attended_8_male) AS attended_8_male
    , SUM(attended_8_female) AS attended_8_female
    , SUM(mt_70) AS mt_70
FROM dash_farmer_training d
WHERE 1 = 1
    %s
    $sqlHakAkses
GROUP BY label
        ";
        $label_data     = "''";
        $label_detail     = "Province";
        if (!empty($ProvinceID)) {
            $where .= " AND d.ProvinceID = ?";
            $params[] = intval($ProvinceID);
            $label_data     = 'Province';
            $label_detail     = 'District';
        }
        if (!empty($DistrictID)) {
            $where .= " AND d.DistrictID = ?";
            $params[] = intval($DistrictID);
            $label_data     = 'District';
            $label_detail     = 'SubDistrict';
        }
        
        $query = $this->db->query(sprintf($sql, $label_data, $where), $params);
        if ($query->num_rows()>0) {
            $result = $query->row_array(0);
            $query = $this->db->query(sprintf($sql, $label_detail, $where), $params);
            if ($query->num_rows()>0) {
                $result['detail'] = $query->result_array();
            }
        }

        return $result;
    }

}
?>