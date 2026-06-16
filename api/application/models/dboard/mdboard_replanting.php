<?php
/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Wed Aug 07 2019
 *  File : mdboard_replanting.php
 *******************************************/
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mdboard_replanting extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    private function truncateTable($namaTabel){
        $sql="DELETE FROM `$namaTabel`";
        $query = $this->db->query($sql);
    }

    public function generateDashReplanting() {
        $this->db->trans_begin();

        //truncate dl tabelnya
        $this->truncateTable('dash_det_replant_finance');

        // $sql = "SELECT
        //             dis.`ProvinceID`
        //             , dis.`DistrictID`
        //             , subd.`SubDistrictID`
        //         FROM
        //             ktv_members a
        //             JOIN ktv_member_role r ON a.MemberID = r.MemberID AND r.MRoleID = 1 #ROLE PETANI
        //             LEFT JOIN ktv_village vil ON a.`VillageID` = vil.`VillageID`
        //             LEFT JOIN ktv_subdistrict subd ON vil.`SubDistrictID` = subd.`SubDistrictID`
        //             LEFT JOIN ktv_district dis ON subd.`DistrictID` = dis.`DistrictID`
        //         WHERE
        //             a.`StatusCode` = 'active'
        //             AND a.`VillageID` IS NOT NULL
        //             AND a.VillageID != ''
        //             AND a.VillageID != '0'
        //         GROUP BY subd.`SubDistrictID`";
        // $query = $this->db->query($sql);
        // $dataRegion = $query->result_array();

        // //ambil data Partner yg ada
        // $sql="SELECT
        //         a.`PartnerID`
        //         , a.`PartnerName`
        //     FROM
        //         ktv_program_partner a
        //     WHERE
        //         a.`StatusCode` = 'active'
        //         AND a.IsGenDashboard = 'Yes'
        //     ORDER BY a.PartnerID ASC
        // ";

        // $query = $this->db->query($sql);
        // $dataPartner = $query->result_array();

        // for ($i=0; $i < count($dataRegion); $i++) {
        //     for ($j=0; $j < count($dataPartner); $j++) {
                $sql = "INSERT INTO dash_det_replant_finance
                        SELECT
                                dis.ProvinceID
                                , dis.DistrictID
                                , subd.SubDistrictID
                                , acc_pm.apmPartnerID
                                , (COUNT(DISTINCT m.`MemberID`)) AS FarmerHasPlantation
                                , IFNULL(COUNT(p.PlotNr),0) AS TotalPlantation
                                , IFNULL(SUM(p.GardenAreaHa),0) AS TotalPlantationHa
                                , IFNULL(SUM(IF(p.`AverageAgeTree` > 19,p.`GardenAreaHa`,0)),0) AS ReplantedHa
                                , IFNULL(SUM(IF(p.`AverageAgeTree` > 19,p.`GardenAreaHa`,0)),0) * ".REPLANTED_FINANCE_HA_FUNDING." AS ReplantedHaFunding
                                , IFNULL(SUM(IF(p.`AverageAgeTree` > 0 AND p.`AverageAgeTree` < 1,1,0)),0) AS Age_0
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 1 AND p.`AverageAgeTree` < 2,1,0)),0) AS Age_1
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 2 AND p.`AverageAgeTree` < 3,1,0)),0) AS Age_2
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 3 AND p.`AverageAgeTree` < 4,1,0)),0) AS Age_3
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 4 AND p.`AverageAgeTree` < 5,1,0)),0) AS Age_4
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 5 AND p.`AverageAgeTree` < 6,1,0)),0) AS Age_5
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 6 AND p.`AverageAgeTree` < 7,1,0)),0) AS Age_6
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 7 AND p.`AverageAgeTree` < 8,1,0)),0) AS Age_7
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 8 AND p.`AverageAgeTree` < 9,1,0)),0) AS Age_8
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 9 AND p.`AverageAgeTree` < 10,1,0)),0) AS Age_9
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 10 AND p.`AverageAgeTree` < 11,1,0)),0) AS Age_10
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 11 AND p.`AverageAgeTree` < 12,1,0)),0) AS Age_11
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 12 AND p.`AverageAgeTree` < 13,1,0)),0) AS Age_12
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 13 AND p.`AverageAgeTree` < 14,1,0)),0) AS Age_13
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 14 AND p.`AverageAgeTree` < 15,1,0)),0) AS Age_14
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 15 AND p.`AverageAgeTree` < 16,1,0)),0) AS Age_15
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 16 AND p.`AverageAgeTree` < 17,1,0)),0) AS Age_16
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 17 AND p.`AverageAgeTree` < 18,1,0)),0) AS Age_17
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 18 AND p.`AverageAgeTree` < 19,1,0)),0) AS Age_18
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 19 AND p.`AverageAgeTree` < 20,1,0)),0) AS Age_19
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 20 AND p.`AverageAgeTree` < 21,1,0)),0) AS Age_20
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 21 AND p.`AverageAgeTree` < 22,1,0)),0) AS Age_21
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 22 AND p.`AverageAgeTree` < 23,1,0)),0) AS Age_22
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 23 AND p.`AverageAgeTree` < 24,1,0)),0) AS Age_23
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 24 AND p.`AverageAgeTree` < 25,1,0)),0) AS Age_24
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 25 AND p.`AverageAgeTree` < 26,1,0)),0) AS Age_25
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 26 AND p.`AverageAgeTree` < 27,1,0)),0) AS Age_26
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 27 AND p.`AverageAgeTree` < 28,1,0)),0) AS Age_27
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 28 AND p.`AverageAgeTree` < 29,1,0)),0) AS Age_28
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 29 AND p.`AverageAgeTree` < 30,1,0)),0) AS Age_29
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 30 AND p.`AverageAgeTree` < 31,1,0)),0) AS Age_30
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 31 AND p.`AverageAgeTree` < 32,1,0)),0) AS Age_31
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 32 AND p.`AverageAgeTree` < 33,1,0)),0) AS Age_32
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 33 AND p.`AverageAgeTree` < 34,1,0)),0) AS Age_33
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 34 AND p.`AverageAgeTree` < 35,1,0)),0) AS Age_34
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 35 AND p.`AverageAgeTree` < 36,1,0)),0) AS Age_35
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 36 AND p.`AverageAgeTree` < 37,1,0)),0) AS Age_36
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 37 AND p.`AverageAgeTree` < 38,1,0)),0) AS Age_37
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 38 AND p.`AverageAgeTree` < 39,1,0)),0) AS Age_38
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 39 AND p.`AverageAgeTree` < 40,1,0)),0) AS Age_39
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 40 AND p.`AverageAgeTree` < 41,1,0)),0) AS Age_40
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 41 AND p.`AverageAgeTree` < 42,1,0)),0) AS Age_41
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 42 AND p.`AverageAgeTree` < 43,1,0)),0) AS Age_42
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 43 AND p.`AverageAgeTree` < 44,1,0)),0) AS Age_43
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 44 AND p.`AverageAgeTree` < 45,1,0)),0) AS Age_44
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 45 AND p.`AverageAgeTree` < 46,1,0)),0) AS Age_45
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 46 AND p.`AverageAgeTree` < 47,1,0)),0) AS Age_46
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 47 AND p.`AverageAgeTree` < 48,1,0)),0) AS Age_47
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 48 AND p.`AverageAgeTree` < 49,1,0)),0) AS Age_48
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 49 AND p.`AverageAgeTree` < 50,1,0)),0) AS Age_49
                                , IFNULL(SUM(IF(p.`AverageAgeTree` >= 50,1,0)),0) AS Age_50_more
                                , NOW() AS DateGenerated
                        FROM
                                ktv_survey_plot p
                                JOIN (SELECT
                                                p.MemberID, p.PlotNr, MAX(p.SurveyNr) AS SurveyNr
                                        FROM ktv_survey_plot p WHERE p.`StatusCode` = 'active'
                                        GROUP BY p.MemberID, p.PlotNr) z
                                                ON p.MemberID = z.MemberID AND p.PlotNr = z.PlotNr AND p.SurveyNr = z.SurveyNr
                                JOIN ktv_members m ON m.MemberID = p.MemberID
                                JOIN ktv_member_role r ON m.MemberID = r.MemberID AND r.MRoleID = 1 #ROLE PETANI
                                INNER JOIN ktv_access_partner_member acc_pm ON m.MemberID = acc_pm.apmMemberID
                                
                                INNER JOIN ktv_village vil ON m.`VillageID` = vil.`VillageID`
                                INNER JOIN ktv_subdistrict subd on subd.SubDistrictID = vil.SubDistrictID
                                INNER JOIN ktv_district dis on dis.DistrictID = subd.DistrictID
                        WHERE
                                1=1
                                AND m.`StatusCode` = 'active'
                                AND p.`StatusCode` = 'active'
                        GROUP BY vil.`SubDistrictID`, acc_pm.apmPartnerID";
                $query = $this->db->query($sql);
        //     }
        // }

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

    public function getDisplayDashReplanting($ProvinceID,$DistrictID) {
        //data display langsung ================================================== (Begin)

        if($ProvinceID != ""){
            $sqldWherePropinsi = " AND a.ProvinceID = '$ProvinceID' ";
        }else{
            $sqldWherePropinsi = "";
        }

        if($DistrictID != ""){
            $sqldWhereDistrict = " AND a.DistrictID = '$DistrictID' ";
        }else{
            $sqldWhereDistrict = "";
        }

        //buat SqlHakAksesKontrol (begin)
        if($_SESSION['is_admin'] == "1"){
            $sqlHakAkses = " AND a.PartnerID = '1' #Partner Koltiva ";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            //cek ktv_access_staff
            $sqlHakAkses = " AND a.DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlHakAkses .= " AND a.PartnerID = '{$_SESSION['PartnerID']}' ";
        } else {
            //cek ktv_access_staff
            $sqlHakAkses = " AND a.DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlHakAkses .= " AND a.PartnerID = '1' #Partner Koltiva ";
        }
        //buat SqlHakAksesKontrol (end)

        $sql = "SELECT
                    SUM(a.FarmerHasPlantation) AS FarmerHasPlantation 
                    , SUM(a.TotalPlantation) AS TotalPlantation 
                    , SUM(a.TotalPlantationHa) AS TotalPlantationHa 
                    , SUM(a.ReplantedHa) AS ReplantedHa 
                    , SUM(a.ReplantedHaFunding) AS ReplantedHaFunding 
                    , SUM(a.Age_0) AS age_0
                    , SUM(a.Age_1) AS age_1
                    , SUM(a.Age_2) AS age_2
                    , SUM(a.Age_3) AS age_3
                    , SUM(a.Age_4) AS age_4
                    , SUM(a.Age_5) AS age_5
                    , SUM(a.Age_6) AS age_6
                    , SUM(a.Age_7) AS age_7
                    , SUM(a.Age_8) AS age_8
                    , SUM(a.Age_9) AS age_9
                    , SUM(a.Age_10) AS age_10
                    , SUM(a.Age_11) AS age_11
                    , SUM(a.Age_12) AS age_12
                    , SUM(a.Age_13) AS age_13
                    , SUM(a.Age_14) AS age_14
                    , SUM(a.Age_15) AS age_15
                    , SUM(a.Age_16) AS age_16
                    , SUM(a.Age_17) AS age_17
                    , SUM(a.Age_18) AS age_18
                    , SUM(a.Age_19) AS age_19
                    , SUM(a.Age_20) AS age_20
                    , SUM(a.Age_21) AS age_21
                    , SUM(a.Age_22) AS age_22
                    , SUM(a.Age_23) AS age_23
                    , SUM(a.Age_24) AS age_24
                    , SUM(a.Age_25) AS age_25
                    , SUM(a.Age_26) AS age_26
                    , SUM(a.Age_27) AS age_27
                    , SUM(a.Age_28) AS age_28
                    , SUM(a.Age_29) AS age_29
                    , SUM(a.Age_30) AS age_30
                    , SUM(a.Age_31) AS age_31
                    , SUM(a.Age_32) AS age_32
                    , SUM(a.Age_33) AS age_33
                    , SUM(a.Age_34) AS age_34
                    , SUM(a.Age_35) AS age_35
                    , SUM(a.Age_36) AS age_36
                    , SUM(a.Age_37) AS age_37
                    , SUM(a.Age_38) AS age_38
                    , SUM(a.Age_39) AS age_39
                    , SUM(a.Age_40) AS age_40
                    , SUM(a.Age_41) AS age_41
                    , SUM(a.Age_42) AS age_42
                    , SUM(a.Age_43) AS age_43
                    , SUM(a.Age_44) AS age_44
                    , SUM(a.Age_45) AS age_45
                    , SUM(a.Age_46) AS age_46
                    , SUM(a.Age_47) AS age_47
                    , SUM(a.Age_48) AS age_48
                    , SUM(a.Age_49) AS age_49
                    , SUM(a.Age_50_more) AS age_50_more
                    , SUM(a.Age_30 + a.Age_31 + a.Age_32 + a.Age_33 + a.Age_34 + a.Age_35 + a.Age_36 + a.Age_37 + a.Age_38 + a.Age_39 + a.Age_40
                          + Age_41 + Age_42 + Age_43 + Age_44 + Age_45 + Age_46 + Age_47 + Age_48 + Age_49 + Age_50_more) AS age_30_more
                    , a.DateGenerated
                FROM
                    dash_det_replant_finance a
                WHERE
                    1=1
                    $sqldWherePropinsi
                    $sqldWhereDistrict
                    $sqlHakAkses
                ";
        $query = $this->db->query($sql,array());
        $result['dataDisplay'] = $query->row_array();
        //data display langsung ================================================== (End)

        return $result;
    }
}