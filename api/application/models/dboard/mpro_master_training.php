<?php

/**
 * @Author: nikolius
 * @Date:   2017-09-20 16:54:07
 * @Last Modified by:   nikolius
 * @Last Modified time: 2018-01-04 10:56:34
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mpro_master_training extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    private function truncateTable($namaTabel){
        $sql="DELETE FROM `$namaTabel`";
        $query = $this->db->query($sql);
    }

    public function generateDashProMasterTraining(){
        $this->db->trans_begin();

        //truncate dl tabelnya
        $this->truncateTable('dash_pro_master_training');

        $sql="
            INSERT INTO dash_pro_master_training
            SELECT
                SUBSTR(b.`DistrictID`,1,2) AS ProvinceID
                , b.`DistrictID`
                , YEAR(b.`TrainingStart`) AS YearTrain
                , IFNULL(SUM((
                    SELECT
                        IF(sub1_b.ObjID=1,1,NULL) #Koltiva ObjID = 1
                    FROM
                        ktv_persons sub1_a
                        INNER JOIN ktv_staffs sub1_b ON
                            sub1_a.PersonID = sub1_b.PersonID AND
                            sub1_b.ObjType IN ('private','program')
                    WHERE
                        sub1_a.PersonID = a.`ParticipantPersonID`
                )),0) AS Koltiva_Staff
                , IFNULL(SUM((
                    SELECT
                        IF(sub2_b.StaffID IS NOT NULL,1,NULL)
                    FROM
                        ktv_persons sub2_a
                        INNER JOIN ktv_staffs sub2_b ON
                            sub2_a.PersonID = sub2_b.PersonID AND
                            sub2_b.ObjType = 'mill'
                    WHERE
                        sub2_a.PersonID = a.`ParticipantPersonID`
                )),0) AS Mill_Staff
                , IFNULL(SUM((
                    SELECT
                        IF(sub3_b.StaffID IS NOT NULL,1,NULL)
                    FROM
                        ktv_persons sub3_a
                        INNER JOIN ktv_staffs sub3_b ON
                            sub3_a.PersonID = sub3_b.PersonID AND
                            sub3_b.ObjType IN ('private','program')
                        INNER JOIN ktv_program_partner sub3_c ON
                            sub3_b.ObjID = sub3_c.PartnerID AND
                            sub3_c.NgoMasterTraining = '1'
                    WHERE
                        sub3_a.PersonID = a.`ParticipantPersonID`
                )),0) AS NGO_Staff
                , IFNULL(SUM((
                    SELECT
                        IF(sub3_b.StaffID IS NOT NULL,1,NULL)
                    FROM
                        ktv_persons sub3_a
                        INNER JOIN ktv_staffs sub3_b ON
                            sub3_a.PersonID = sub3_b.PersonID AND
                            sub3_b.ObjType IN ('private')
                        INNER JOIN ktv_program_partner sub3_c ON
                            sub3_b.ObjID = sub3_c.PartnerID AND
                            sub3_c.NgoMasterTraining != '1'
                    WHERE
                        sub3_a.PersonID = a.`ParticipantPersonID`
                )),0) AS Private_Staff
                , NOW() AS DateGenerated
            FROM
                ktv_master_trainings_participants a
                INNER JOIN ktv_master_trainings b ON a.`MasterTrainingID` = b.`MasterTrainingID`

                INNER JOIN (
                    SELECT
                        tblg_lat_tm.MasterTrainingsStaffID
                    FROM
                    (
                    SELECT
                        lat_t.MasterTrainingsStaffID
                        , lat_t.`ParticipantPersonID`
                    FROM
                        ktv_master_trainings_participants lat_t
                        INNER JOIN ktv_master_trainings lat_tm ON 1=1
                            AND lat_t.`MasterTrainingID` = lat_tm.`MasterTrainingID`
                    WHERE
                        lat_t.`StatusCode` = 'active'
                        AND lat_tm.`StatusCode` = 'active'
                        AND lat_t.`ParticipantPersonID` != '0'
                        AND lat_t.`ParticipantPersonID` IS NOT NULL
                    ORDER BY lat_tm.`TrainingEnd` DESC
                    ) AS tblg_lat_tm
                    GROUP BY tblg_lat_tm.ParticipantPersonID
                ) AS latest_train ON 1=1
                    AND a.`MasterTrainingsStaffID` = latest_train.MasterTrainingsStaffID
                #Sesuai request mas rofiq, setiap peserta training hanya dihitung 1x yaitu training terakhirnya
            WHERE
                a.`StatusCode` = 'active'
                AND b.`StatusCode` = 'active'
            GROUP BY b.`DistrictID`, YearTrain
            ORDER BY YearTrain ASC
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

    public function getDisplayMasterTraining($ProvinceID, $DistrictID){
        //get DateGenerated
        $sql="SELECT a.DateGenerated FROM dash_pro_master_training a LIMIT 1";
        $query = $this->db->query($sql);
        $dataDate = $query->row_array();
        $result['DateGenerated'] = $dataDate['DateGenerated'];

        //filter region (begin)
        if($ProvinceID == ""){
            $sqlcLabel = "Province";
            $sqlcJoin = "JOIN ktv_province prov ON prov.`ProvinceID` = SUBSTR(a.`DistrictID`,1,2)";
            $sqlcWhere = "";
        } else {
            if (empty($DistrictID)) {
                $sqlcLabel = "District";
                $sqlcJoin = "JOIN ktv_district dis ON dis.`DistrictID` = a.DistrictID";
                $sqlcWhere = " AND substr(a.DistrictID,1,2) = '$ProvinceID' ";
            } else {
                $sqlcLabel = "SubDistrict";
                $sqlcJoin = "JOIN ktv_subdistrict sdis ON sdis.`DistrictID` = a.DistrictID";
                $sqlcWhere = " AND a.DistrictID = '$DistrictID' ";
            }
            
        }
        //filter region (end)

        //buat SqlHakAksesKontrol (begin)
        if($_SESSION['is_admin'] == "1"){
            $sqlcHakAkses = "";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            //cek ktv_access_staff
            $sqlcHakAkses = " AND a.DistrictID IN (".$_SESSION['daerah_access'].")";
        } else {
            //cek ktv_access_staff
            $sqlcHakAkses = " AND a.DistrictID IN (".$_SESSION['daerah_access'].")";
        }
        //buat SqlHakAksesKontrol (end)

        $sql="SELECT
                $sqlcLabel AS label
                , SUM(a.count_koltiva_staff + a.count_mill_staff + a.count_ngo_staff + a.count_private_staff) AS total_training
                , SUM(a.count_koltiva_staff) AS count_koltiva_staff
                , SUM(a.count_mill_staff) AS count_mill_staff
                , SUM(a.count_ngo_staff) AS count_ngo_staff
                , SUM(a.count_private_staff) AS count_private_staff
            FROM
                dash_pro_master_training a
                $sqlcJoin
            WHERE
                1 = 1
                $sqlcWhere
                $sqlcHakAkses
            GROUP BY label
            ORDER BY label";
        $query = $this->db->query($sql,array());
        $result['barChart'] = $query->result_array();


        //Chart Per Year =============================================== (begin)

        //get range tahun
        $sql="SELECT
                YearTrain
            FROM
                dash_pro_master_training
            GROUP BY YearTrain
            ORDER BY YearTrain";
        $query = $this->db->query($sql);
        $dataRangeTahun = $query->result_array();

        $querySelectYear = "";
        $arrYearCate = array();
        foreach ($dataRangeTahun as $key => $value) {
            $querySelectYear .= "
                                , IFNULL(SUM(IF(`YearTrain`={$value['YearTrain']}, (a.count_koltiva_staff + a.count_mill_staff + a.count_ngo_staff) ,0)),0) AS total_training_{$key}
                            ";
        }

        $sql="SELECT
                $sqlcLabel AS label
                $querySelectYear
            FROM
                dash_pro_master_training a
                $sqlcJoin
            WHERE
                1 = 1
                $sqlcWhere
                $sqlcHakAkses
            GROUP BY label
            ORDER BY label";
        $query = $this->db->query($sql,array());
        $TrainYearData = $query->result_array();

        $result['barChartPerYear']['yearRange'] = $dataRangeTahun;
        $result['barChartPerYear']['TrainYearData'] = $TrainYearData;
        //Chart Per Year =============================================== (end)

        return $result;
    }

}
?>