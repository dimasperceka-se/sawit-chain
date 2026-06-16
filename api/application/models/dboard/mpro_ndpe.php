<?php

/**
 * @Author: nikolius
 * @Date:   2017-09-11 10:27:17
 * @Last Modified by:   nikolius
 * @Last Modified time: 2018-01-11 12:21:06
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mpro_ndpe extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    private function truncateTable($namaTabel){
        $sql="DELETE FROM `$namaTabel`";
        $query = $this->db->query($sql);
    }

    public function generateDashProNdpe(){
        $this->db->trans_begin();

        //truncate dl tabelnya
        $this->truncateTable('dash_pro_ndpe');

        //ambil data village nya yg ada
        $sql="SELECT
                kp.ProvinceID AS ProvinceID
                , kd.DistrictID AS DistrictID
                , ksd.SubDistrictID AS SubDistrictID
                , a.`VillageID`
            FROM
                ktv_members a
                LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
                LEFT JOIN ktv_province kp ON kp.`ProvinceID` = kd.`ProvinceID`
            WHERE
                a.`StatusCode` = 'active'
                AND a.`VillageID` IS NOT NULL
                AND a.VillageID != ''
                AND a.VillageID != '0'
                AND kp.`ProvinceID` IS NOT NULL
            GROUP BY a.`VillageID`";
        $query = $this->db->query($sql);
        $dataRegion = $query->result_array();

        //ambil data Partner yg ada
        $sql="SELECT
                a.`PartnerID`
                , a.`PartnerName`
            FROM
                ktv_program_partner a
            WHERE
                a.`StatusCode` = 'active'
                AND a.IsGenDashboard = 'Yes'
            ORDER BY a.PartnerID ASC
            ";
        $query = $this->db->query($sql);
        $dataPartner = $query->result_array();

        //set biar tidak error, gara2 hitungan jadi minus
        $this->db->query("SET sql_mode='NO_UNSIGNED_SUBTRACTION'");

        for ($i=0; $i < count($dataRegion); $i++) {
            for ($j=0; $j < count($dataPartner); $j++) {

                $sql="INSERT INTO dash_pro_ndpe
                    SELECT
                        tbl_region.ProvinceID
                        , tbl_region.DistrictID
                        , tbl_region.SubDistrictID
                        , tbl_region.VillageID
                        , tbl_region.PartnerID
                        , 'Smallholder' AS `Type`
                        , IFNULL(tbl_join_garden.land_convert_forest_2010_yes,0) AS land_convert_forest_2010_yes
                        , IFNULL(tbl_join_garden.garden_total,0) - IFNULL(tbl_join_garden.land_convert_forest_2010_yes,0) AS land_convert_forest_2010_no

                        , IFNULL(tbl_join_garden.land_convert_peat_2010_yes,0) AS land_convert_peat_2010_yes
                        , IFNULL(tbl_join_garden.garden_total,0) - IFNULL(tbl_join_garden.land_convert_peat_2010_yes,0) AS land_convert_peat_2010_no

                        , IFNULL(tbl_join_farmer_fam.labor_right_abuses_yes,0) AS labor_right_abuses_yes
                        , IFNULL(tbl_join_farmer_fam.total_farmer,0) - IFNULL(tbl_join_farmer_fam.labor_right_abuses_yes,0) AS labor_right_abuses_no

                        , IFNULL(tbl_join_fam_labor_abuses.labor_right_fam_child_no_school,0) AS labor_right_fam_child_no_school
                        , IFNULL(tbl_join_fam_labor_abuses.labor_right_fam_child_work,0) AS labor_right_fam_child_work
                        , IFNULL(tbl_join_lab_labor_abuses.labor_right_lab_child_work,0) AS labor_right_lab_child_work
                        , IFNULL(tbl_join_fam_labor_abuses.labor_right_fam_overtime,0) AS labor_right_fam_overtime
                        , IFNULL(tbl_join_lab_labor_abuses.labor_right_lab_overtime,0) AS labor_right_lab_overtime
                        , IFNULL(tbl_join_fam_labor_abuses.labor_right_fam_underpaid,0) AS labor_right_fam_underpaid
                        , IFNULL(tbl_join_lab_labor_abuses.labor_right_lab_underpaid,0) AS labor_right_lab_underpaid

                        , IFNULL(tbl_join_garden_member.sustain_smart_agri_farmer_yes,0) AS sustain_smart_agri_farmer_yes
                        , IFNULL(tbl_join_farmer_fam.total_farmer,0) - IFNULL(tbl_join_garden_member.sustain_smart_agri_farmer_yes,0) AS sustain_smart_agri_farmer_no

                        , IFNULL(tbl_join_farmer_fam.farmer_willing_survey_yes,0) AS farmer_willing_survey_yes
                        , IFNULL(tbl_join_farmer_fam.total_farmer,0) - IFNULL(tbl_join_farmer_fam.farmer_willing_survey_yes,0) AS farmer_willing_survey_no

                        , NOW() AS DateGenerated
                    FROM
                        (
                            SELECT
                                '{$dataRegion[$i]['ProvinceID']}' AS ProvinceID
                                , '{$dataRegion[$i]['DistrictID']}' AS DistrictID
                                , '{$dataRegion[$i]['SubDistrictID']}' AS SubDistrictID
                                , '{$dataRegion[$i]['VillageID']}' AS VillageID
                                , '{$dataPartner[$j]['PartnerID']}' AS PartnerID
                        ) AS tbl_region

                        LEFT JOIN (
                            SELECT
                                m.`VillageID`
                                , '{$dataPartner[$j]['PartnerID']}' AS PartnerID
                                , IFNULL(COUNT(a.PlotNr),0) AS garden_total
                                , (
                                    SUM(IF(a.FirstPlantingYear >= '2010',
                                        IF(a.PlantationConditionEst = '6',1,0)
                                    ,0))
                                ) AS land_convert_forest_2010_yes
                                , (
                                    SUM(IF(a.FirstPlantingYear >= '2010',
                                        IF(a.`SoilType` = '2',1,0)
                                    ,0))
                                ) AS land_convert_peat_2010_yes
                            FROM
                                ktv_survey_plot a
                                JOIN (SELECT
                                    p.MemberID, p.PlotNr, MAX(p.SurveyNr) AS SurveyNr
                                FROM ktv_survey_plot p WHERE p.`StatusCode` = 'active'
                                GROUP BY p.MemberID, p.PlotNr) AS gar_latest
                                    ON a.MemberID = gar_latest.MemberID AND a.PlotNr = gar_latest.PlotNr AND a.SurveyNr = gar_latest.SurveyNr
                                JOIN ktv_members m ON m.MemberID = a.MemberID
                                INNER JOIN ktv_access_partner_member acc_pm ON m.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$dataPartner[$j]['PartnerID']}'
                            WHERE
                                a.`StatusCode` = 'active'
                                AND m.`StatusCode` = 'active'
                                AND m.`VillageID` = '{$dataRegion[$i]['VillageID']}'
                            GROUP BY m.`VillageID`
                        ) AS tbl_join_garden
                            ON tbl_region.PartnerID = tbl_join_garden.PartnerID AND tbl_region.VillageID = tbl_join_garden.VillageID

                        LEFT JOIN (
                            SELECT
                                tbl_grup_member.VillageID
                                , '{$dataPartner[$j]['PartnerID']}' AS PartnerID
                                , SUM(IF( (tbl_grup_member.labor_right_abuses + tbl_grup_member.labor_right_abuses_from_labour) > 0,1,0)) AS labor_right_abuses_yes
                                , COUNT(tbl_grup_member.MemberID) AS total_farmer
                                , SUM(IF(tbl_grup_member.farmer_willing_survey_yes > 0,1,0)) AS farmer_willing_survey_yes
                            FROM
                            (
                                SELECT
                                    a.`MemberID`
                                    , a.`MemberName`
                                    , a.`VillageID`

                                    , IFNULL(SUM(
                                        CASE
                                            #Children not in school
                                            WHEN YEAR(CURDATE()) - fam.`YearOfBirth` BETWEEN 7 AND 15 AND
                                                fam.InSchool = 'No'
                                            THEN 1

                                            #Children work in farm
                                            WHEN YEAR(CURDATE()) - fam.`YearOfBirth` BETWEEN 5 AND 16 AND
                                                fam.`WorkingStatus` = 'Yes'
                                            THEN 1

                                            #Overtime Work
                                            WHEN fam.`TotalWorkingHrsPerDay` > 8
                                            THEN 1

                                            #Underpaid Work
                                            WHEN
                                                IF(fam.WagePeriod IS NOT NULL && fam.WagePeriod != '5',
                                                    IF(fam.WageAmount IS NOT NULL,
                                                        IF( (fam.`WageAmount` * GetWagePeriodDay(fam.`WagePeriod`)) < wg.Amount ,TRUE,FALSE)
                                                    ,FALSE)
                                                ,FALSE)
                                            THEN 1

                                            ELSE 0
                                        END
                                    ),0) AS labor_right_abuses

                                    , IFNULL(SUM(
                                        CASE
                                            #Children work in farm
                                            WHEN (YEAR(CURDATE()) - fam_lab.`YearOfBirth`) BETWEEN 5 AND 16 #Age less than 15
                                            THEN 1

                                            #Overtime Work
                                            WHEN fam_lab.`TotalWorkingHrsPerDay` > 8 #Working hours more than 8 hours
                                            THEN 1

                                            #Underpaid Work
                                            WHEN
                                                IF(fam_lab.WagePeriod IS NOT NULL && fam_lab.WagePeriod != '5',
                                                    IF(fam_lab.WageAmount IS NOT NULL,
                                                        IF( (fam_lab.`WageAmount` * GetWagePeriodDay(fam_lab.`WagePeriod`)) < wg.Amount ,TRUE,FALSE)
                                                    ,FALSE)
                                                ,FALSE)
                                            THEN 1

                                            ELSE 0
                                        END
                                    ),0) AS labor_right_abuses_from_labour

                                    , SUM(IF(a.`LearningContractStatus`='1',1,0)) AS farmer_willing_survey_yes
                                FROM
                                    ktv_members a
                                    LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                                    LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                                    LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
                                    LEFT JOIN ktv_province kp ON kp.`ProvinceID` = kd.`ProvinceID`
                                    JOIN ktv_member_role r ON a.MemberID = r.MemberID AND r.MRoleID = 1 #ROLE PETANI
                                    LEFT JOIN ktv_member_family_labour fam ON a.`MemberID` = fam.`MemberID` AND fam.`StatusCode` = 'active'
                                    LEFT JOIN ktv_member_labour fam_lab ON a.`MemberID` = fam_lab.`MemberID` AND fam_lab.`StatusCode` = 'active'
                                    LEFT JOIN ktv_ref_province_wage wg ON wg.ProvinceID = kp.ProvinceID AND wg.Year = YEAR(CURDATE())
                                    INNER JOIN ktv_access_partner_member acc_pm ON a.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$dataPartner[$j]['PartnerID']}'
                                WHERE
                                    a.`StatusCode` = 'active'
                                    AND a.VillageID = '{$dataRegion[$i]['VillageID']}'
                                GROUP BY a.`MemberID`
                                ) AS tbl_grup_member
                            GROUP BY tbl_grup_member.VillageID
                        ) AS tbl_join_farmer_fam
                            ON tbl_region.PartnerID = tbl_join_farmer_fam.PartnerID AND tbl_region.VillageID = tbl_join_farmer_fam.VillageID

                        LEFT JOIN (

                            SELECT
                                a.`VillageID`
                                , acc_pm.apmPartnerID AS PartnerID
                                , IFNULL(SUM(IF( YEAR(CURDATE()) - fam.`YearOfBirth` BETWEEN 7 AND 15 AND fam.InSchool = 'No',1,0)),0) AS labor_right_fam_child_no_school
                                , IFNULL(SUM(IF(YEAR(CURDATE()) - fam.`YearOfBirth` BETWEEN 5 AND 16 AND fam.`WorkingStatus` = 'Yes',1,0)),0) AS labor_right_fam_child_work
                                , IFNULL(SUM(IF(fam.`TotalWorkingHrsPerDay` > 8,1,0)),0) AS labor_right_fam_overtime
                                , IFNULL(SUM(IF(IF(fam.WagePeriod IS NOT NULL && fam.WagePeriod != '5',
                                        IF(fam.WageAmount IS NOT NULL,
                                            IF( (fam.`WageAmount` * GetWagePeriodDay(fam.`WagePeriod`)) < wg.Amount ,TRUE,FALSE)
                                        ,FALSE)
                                    ,FALSE),1,0)),0) AS labor_right_fam_underpaid
                            FROM
                                ktv_members a
                                LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                                LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                                LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
                                LEFT JOIN ktv_province kp ON kp.`ProvinceID` = kd.`ProvinceID`
                                JOIN ktv_member_role r ON a.MemberID = r.MemberID AND r.MRoleID = 1 #ROLE PETANI
                                INNER JOIN ktv_access_partner_member acc_pm ON a.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$dataPartner[$j]['PartnerID']}'
                                LEFT JOIN ktv_member_family_labour fam ON a.`MemberID` = fam.`MemberID` AND fam.`StatusCode` = 'active'
                                LEFT JOIN ktv_member_labour fam_lab ON a.`MemberID` = fam_lab.`MemberID` AND fam_lab.`StatusCode` = 'active'
                                LEFT JOIN ktv_ref_province_wage wg ON wg.ProvinceID = kp.ProvinceID AND wg.Year = YEAR(CURDATE())
                            WHERE
                                a.`StatusCode` = 'active'
                                AND a.VillageID = '{$dataRegion[$i]['VillageID']}'
                            GROUP BY a.`VillageID`
                            ORDER BY a.`VillageID`

                        ) AS tbl_join_fam_labor_abuses ON 1=1
                            AND tbl_region.PartnerID = tbl_join_fam_labor_abuses.PartnerID
                            AND tbl_region.VillageID = tbl_join_fam_labor_abuses.VillageID


                        LEFT JOIN (

                            SELECT
                                a.`VillageID`
                                , acc_pm.apmPartnerID AS PartnerID
                                , IFNULL(SUM(IF((YEAR(CURDATE()) - fam_lab.`YearOfBirth`) BETWEEN 5 AND 16,1,0)),0) AS labor_right_lab_child_work
                                , IFNULL(SUM(IF(fam_lab.`TotalWorkingHrsPerDay` > 8,1,0)),0) AS labor_right_lab_overtime
                                , IFNULL(SUM(IF(IF(fam_lab.WagePeriod IS NOT NULL && fam_lab.WagePeriod != '5',
                                        IF(fam_lab.WageAmount IS NOT NULL,
                                            IF( (fam_lab.`WageAmount` * GetWagePeriodDay(fam_lab.`WagePeriod`)) < wg.Amount ,TRUE,FALSE)
                                        ,FALSE)
                                    ,FALSE),1,0)),0) AS labor_right_lab_underpaid
                            FROM
                                ktv_members a
                                LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                                LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                                LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
                                LEFT JOIN ktv_province kp ON kp.`ProvinceID` = kd.`ProvinceID`
                                JOIN ktv_member_role r ON a.MemberID = r.MemberID AND r.MRoleID = 1 #ROLE PETANI
                                INNER JOIN ktv_access_partner_member acc_pm ON a.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$dataPartner[$j]['PartnerID']}'
                                LEFT JOIN ktv_ref_province_wage wg ON wg.ProvinceID = kp.ProvinceID AND wg.Year = YEAR(CURDATE())
                                LEFT JOIN ktv_member_labour fam_lab ON a.`MemberID` = fam_lab.`MemberID` AND fam_lab.`StatusCode` = 'active'
                            WHERE
                                a.`StatusCode` = 'active'
                                AND a.VillageID = '{$dataRegion[$i]['VillageID']}'
                            GROUP BY a.`VillageID`
                            ORDER BY a.`VillageID`

                        ) AS tbl_join_lab_labor_abuses ON 1=1
                            AND tbl_region.PartnerID = tbl_join_lab_labor_abuses.PartnerID
                            AND tbl_region.VillageID = tbl_join_lab_labor_abuses.VillageID


                        LEFT JOIN (

                            SELECT
                                tbl_grup_garden_member.VillageID
                                , tbl_grup_garden_member.PartnerID
                                , SUM(IF(tbl_grup_garden_member.sustain_smart_agri_farmer_yes > 0,1,0)) AS sustain_smart_agri_farmer_yes
                            FROM
                            (
                            SELECT
                                m.`VillageID`
                                , '{$dataPartner[$j]['PartnerID']}' AS PartnerID
                                , m.`MemberID`
                                , SUM(
                                    IF(
                                        (
                                            a.PeHerbi5 = '1' ||
                                            a.PeHerbi12 = '1' ||
                                            a.`PeHerbi10` = '1' ||
                                            a.`PeHerbi9` = '1' ||
                                            a.`PeHerbi11` = '1' ||
                                            a.PeHerbi13 = '1'
                                        )
                                        && a.PlantationProductivity > 10
                                        && (
                                            a.FirstPlantingYear >= '2010'
                                            &&
                                            a.PlantationConditionEst = '6'
                                        )
                                        && (
                                            a.`SoilType` = '2'
                                            &&
                                            a.FirstPlantingYear >= '2010'
                                        )
                                    ,1,0)
                                ) AS sustain_smart_agri_farmer_yes

                                , IFNULL(SUM(
                                    CASE
                                        WHEN YEAR(CURDATE()) - fam.`YearOfBirth` BETWEEN 7 AND 15 AND
                                            fam.InSchool = 'No'
                                        THEN 1

                                        WHEN YEAR(CURDATE()) - fam.`YearOfBirth` BETWEEN 5 AND 16 AND
                                            fam.`WorkingStatus` = 'Yes'
                                        THEN 1

                                        WHEN fam.`TotalWorkingHrsPerDay` > 8
                                        THEN 1

                                        WHEN
                                            IF(fam.WagePeriod IS NOT NULL && fam.WagePeriod != '5',
                                                IF(fam.WageAmount IS NOT NULL,
                                                    IF( (fam.`WageAmount` * GetWagePeriodDay(fam.`WagePeriod`)) < wg.Amount ,TRUE,FALSE)
                                                ,FALSE)
                                            ,FALSE)
                                        THEN 1

                                        ELSE 0
                                    END
                                ),0) AS labor_right_abuses

                                , IFNULL(SUM(
                                    CASE
                                        WHEN (YEAR(CURDATE()) - fam_lab.`YearOfBirth`) BETWEEN 5 AND 16 #Age less than 15
                                        THEN 1

                                        WHEN fam_lab.`TotalWorkingHrsPerDay` > 8 #Working hours more than 8 hours
                                        THEN 1

                                        WHEN
                                            IF(fam_lab.WagePeriod IS NOT NULL && fam_lab.WagePeriod != '5',
                                                IF(fam_lab.WageAmount IS NOT NULL,
                                                    IF( (fam_lab.`WageAmount` * GetWagePeriodDay(fam_lab.`WagePeriod`)) < wg.Amount ,TRUE,FALSE)
                                                ,FALSE)
                                            ,FALSE)
                                        THEN 1

                                        ELSE 0
                                    END
                                ),0) AS labor_right_abuses_from_labour

                            FROM
                                ktv_survey_plot a
                                LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                                LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                                LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
                                LEFT JOIN ktv_province kp ON kp.`ProvinceID` = kd.`ProvinceID`
                                JOIN (SELECT
                                    p.MemberID, p.PlotNr, MAX(p.SurveyNr) AS SurveyNr
                                FROM ktv_survey_plot p WHERE p.`StatusCode` = 'active'
                                GROUP BY p.MemberID, p.PlotNr) AS gar_latest
                                    ON a.MemberID = gar_latest.MemberID AND a.PlotNr = gar_latest.PlotNr AND a.SurveyNr = gar_latest.SurveyNr
                                JOIN ktv_members m ON m.MemberID = a.MemberID
                                INNER JOIN ktv_access_partner_member acc_pm ON m.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$dataPartner[$j]['PartnerID']}'
                                LEFT JOIN ktv_member_family_labour fam ON a.`MemberID` = fam.`MemberID` AND fam.`StatusCode` = 'active'
                                LEFT JOIN ktv_member_labour fam_lab ON a.`MemberID` = fam_lab.`MemberID` AND fam_lab.`StatusCode` = 'active'
                                LEFT JOIN ktv_ref_province_wage wg ON wg.ProvinceID = kp.ProvinceID AND wg.Year = YEAR(CURDATE())
                            WHERE
                                a.`StatusCode` = 'active'
                                AND m.`StatusCode` = 'active'
                                AND m.`VillageID` = '{$dataRegion[$i]['VillageID']}'
                            GROUP BY m.`MemberID`
                            HAVING (labor_right_abuses+labor_right_abuses_from_labour) = 0
                            ) AS tbl_grup_garden_member
                            GROUP BY tbl_grup_garden_member.VillageID

                        ) AS tbl_join_garden_member
                            ON tbl_region.PartnerID = tbl_join_garden_member.PartnerID AND tbl_region.VillageID = tbl_join_garden_member.VillageID
                ";
                $query = $this->db->query($sql);
                
                $sql = "INSERT INTO dash_pro_ndpe
                SELECT
                    tbl_region.ProvinceID
                    , tbl_region.DistrictID
                    , tbl_region.SubDistrictID
                    , tbl_region.VillageID
                    , tbl_region.PartnerID
                    , 'SME' AS `Type`
                    , IFNULL(tbl_join_garden.land_convert_forest_2010_yes,0) AS land_convert_forest_2010_yes
                    , IFNULL(tbl_join_garden.garden_total,0) - IFNULL(tbl_join_garden.land_convert_forest_2010_yes,0) AS land_convert_forest_2010_no

                    , IFNULL(tbl_join_garden.land_convert_peat_2010_yes,0) AS land_convert_peat_2010_yes
                    , IFNULL(tbl_join_garden.garden_total,0) - IFNULL(tbl_join_garden.land_convert_peat_2010_yes,0) AS land_convert_peat_2010_no

                    , 0 AS labor_right_abuses_yes
                    , 0 AS labor_right_abuses_no

                    , 0 AS labor_right_fam_child_no_school
                    , 0 AS labor_right_fam_child_work
                    , 0 AS labor_right_lab_child_work
                    , 0 AS labor_right_fam_overtime
                    , 0 AS labor_right_lab_overtime
                    , 0 AS labor_right_fam_underpaid
                    , 0 AS labor_right_lab_underpaid

                    , 0 AS sustain_smart_agri_farmer_yes
                    , 0 AS sustain_smart_agri_farmer_no

                    , 0 AS farmer_willing_survey_yes
                    , 0 AS farmer_willing_survey_no

                    , NOW() AS DateGenerated
                FROM
                    (
                        SELECT
                            '{$dataRegion[$i]['ProvinceID']}' AS ProvinceID
                            , '{$dataRegion[$i]['DistrictID']}' AS DistrictID
                            , '{$dataRegion[$i]['SubDistrictID']}' AS SubDistrictID
                            , '{$dataRegion[$i]['VillageID']}' AS VillageID
                            , '{$dataPartner[$j]['PartnerID']}' AS PartnerID
                    ) AS tbl_region

                    LEFT JOIN (
                        SELECT
                            m.`VillageID`
                            , '{$dataPartner[$j]['PartnerID']}' AS PartnerID
                            , IFNULL(COUNT(a.PlotNr),0) AS garden_total
                            , (
                                SUM(IF(a.FirstPlantingYear >= '2010',
                                    IF(a.PlantationConditionEst = '6',1,0)
                                ,0))
                            ) AS land_convert_forest_2010_yes
                            , (
                                SUM(IF(a.FirstPlantingYear >= '2010',
                                    IF(a.`SoilType` = '2',1,0)
                                ,0))
                            ) AS land_convert_peat_2010_yes
                        FROM
                            ktv_survey_plot_sme a
                            JOIN (SELECT
                                p.MemberID, p.PlotNr, MAX(p.SurveyNr) AS SurveyNr
                            FROM ktv_survey_plot_sme p WHERE p.`StatusCode` = 'active'
                            GROUP BY p.MemberID, p.PlotNr) AS gar_latest
                                ON a.MemberID = gar_latest.MemberID AND a.PlotNr = gar_latest.PlotNr AND a.SurveyNr = gar_latest.SurveyNr
                            JOIN ktv_members m ON m.MemberID = a.MemberID
                            INNER JOIN ktv_access_partner_member acc_pm ON m.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$dataPartner[$j]['PartnerID']}'
                        WHERE
                            a.`StatusCode` = 'active'
                            AND m.`StatusCode` = 'active'
                            AND m.`VillageID` = '{$dataRegion[$i]['VillageID']}'
                        GROUP BY m.`VillageID`
                    ) AS tbl_join_garden
                        ON tbl_region.PartnerID = tbl_join_garden.PartnerID AND tbl_region.VillageID = tbl_join_garden.VillageID
                    ";
                $query = $this->db->query($sql);
            }
        }

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

    public function getDisplayNdpe($fprovince,$fdistrict,$ftype){
        //data display langsung ================================================== (begin)
        if($fprovince != "all_province"){
            $sqldWherePropinsi = " AND a.ProvinceID = '$fprovince' ";
        }else{
            $sqldWherePropinsi = "";
        }

        if($fdistrict != "all_district"){
            $sqldWhereDistrict = " AND a.DistrictID = '$fdistrict' ";
        }else{
            $sqldWhereDistrict = "";
        }

        if($ftype != "All") {
            $sqldWhereFtype = " AND a.Type = '$ftype' ";
        } else {
            $sqldWhereFtype = "";
        }

        //buat SqlHakAksesKontrol (begin)
        if($_SESSION['is_admin'] == "1"){
            $sqlHakAkses = " AND a.PartnerID = '1' #Partner Koltiva ";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            //cek ktv_access_staff
            $sqlHakAkses = " AND dis.DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlHakAkses .= " AND a.PartnerID = '{$_SESSION['PartnerID']}' ";
        } else {
            //cek ktv_access_staff
            $sqlHakAkses = " AND dis.DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlHakAkses .= " AND a.PartnerID = '1' #Partner Koltiva ";
        }
        //buat SqlHakAksesKontrol (end)

        $sql="SELECT
                SUM(a.land_convert_forest_2010_yes) AS land_convert_forest_2010_yes
                , SUM(a.land_convert_forest_2010_no) AS land_convert_forest_2010_no
                , SUM(a.land_convert_peat_2010_yes) AS land_convert_peat_2010_yes
                , SUM(a.land_convert_peat_2010_no) AS land_convert_peat_2010_no
                , SUM(a.labor_right_abuses_yes) AS labor_right_abuses_yes
                , SUM(a.labor_right_abuses_no) AS labor_right_abuses_no
                , SUM(a.sustain_smart_agri_farmer_yes) AS sustain_smart_agri_farmer_yes
                , SUM(a.sustain_smart_agri_farmer_no) AS sustain_smart_agri_farmer_no
                , SUM(a.farmer_willing_survey_yes) AS farmer_willing_survey_yes
                , SUM(a.farmer_willing_survey_no) AS farmer_willing_survey_no

                , IFNULL(SUM(a.labor_right_fam_child_no_school),0) AS labor_right_fam_child_no_school
                , IFNULL(SUM(a.labor_right_fam_child_work),0) AS labor_right_fam_child_work
                , IFNULL(SUM(a.labor_right_lab_child_work),0) AS labor_right_lab_child_work
                , IFNULL(SUM(a.labor_right_fam_overtime),0) AS labor_right_fam_overtime
                , IFNULL(SUM(a.labor_right_lab_overtime),0) AS labor_right_lab_overtime
                , IFNULL(SUM(a.labor_right_fam_underpaid),0) AS labor_right_fam_underpaid
                , IFNULL(SUM(a.labor_right_lab_underpaid),0) AS labor_right_lab_underpaid

                , a.DateGenerated
            FROM
                dash_pro_ndpe a
                LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                LEFT JOIN ktv_subdistrict subdis ON subdis.`SubDistrictID` = kv.`SubDistrictID`
                LEFT JOIN ktv_district dis ON dis.`DistrictID` = subdis.`DistrictID`
                LEFT JOIN ktv_province prov ON prov.`ProvinceID` = dis.`ProvinceID`
            WHERE
                1 = 1
                $sqldWherePropinsi
                $sqldWhereDistrict
                $sqldWhereFtype
                $sqlHakAkses
            ";
        $query = $this->db->query($sql,array());
        $result['dataDisplay'] = $query->row_array();
        //data display langsung ================================================== (end)

        //data display chart ================================================== (begin)

        //Cek Group by apa
        if($fprovince == 'all_province') {
            $sqlcLabel = "Province";
            $sqlcJoin = "JOIN ktv_province prov ON prov.`ProvinceID` = a.ProvinceID";
            $sqlcWhere = "";
        } else {
            if($fdistrict == 'all_district') {
                $sqlcLabel = "District";
                $sqlcJoin = "JOIN ktv_district dis ON dis.`DistrictID` = a.DistrictID";
                $sqlcWhere = "AND a.ProvinceID = '$fprovince'";
            } else {
                $sqlcLabel = "SubDistrict";
                $sqlcJoin = "JOIN ktv_subdistrict subdis ON subdis.`SubDistrictID` = a.SubDistrictID";
                $sqlcWhere = "AND a.DistrictID = '$fdistrict'";
            }
        }

        /*if($ProvinceID == ""){
            $sqlcLabel = "prov.Province";
            $sqlcJoin = "LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                        LEFT JOIN ktv_subdistrict subdis ON subdis.`SubDistrictID` = kv.`SubDistrictID`
                        LEFT JOIN ktv_district dis ON dis.`DistrictID` = subdis.`DistrictID`
                        LEFT JOIN ktv_province prov ON prov.`ProvinceID` = dis.`ProvinceID`";
            $sqlcWhere = "";
        } elseif($DistrictID == "") {
            $sqlcLabel = "dis.District";
            $sqlcJoin = "LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                        LEFT JOIN ktv_subdistrict subdis ON subdis.`SubDistrictID` = kv.`SubDistrictID`
                        LEFT JOIN ktv_district dis ON dis.`DistrictID` = subdis.`DistrictID`
                        LEFT JOIN ktv_province prov ON prov.`ProvinceID` = dis.`ProvinceID`";
            $sqlcWhere = "AND dis.ProvinceID = '$ProvinceID'";
        } else {
            $sqlcLabel = "subdis.SubDistrict";
            $sqlcJoin = "LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                        LEFT JOIN ktv_subdistrict subdis ON subdis.`SubDistrictID` = kv.`SubDistrictID`
                        LEFT JOIN ktv_district dis ON dis.`DistrictID` = subdis.`DistrictID`
                        LEFT JOIN ktv_province prov ON prov.`ProvinceID` = dis.`ProvinceID`";
            $sqlcWhere = "AND subdis.DistrictID = '$DistrictID'";
        }*/

        //buat SqlHakAksesKontrol (begin)
        if($_SESSION['is_admin'] == "1"){
            $sqlcHakAkses = " AND a.PartnerID = '1' #Partner Koltiva ";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            //cek ktv_access_staff
            $sqlcHakAkses = " AND dis.DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlcHakAkses .= " AND a.PartnerID = '{$_SESSION['PartnerID']}' ";
        } else {
            //cek ktv_access_staff
            $sqlcHakAkses = " AND dis.DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlcHakAkses .= " AND a.PartnerID = '1' #Partner Koltiva ";
        }
        //buat SqlHakAksesKontrol (end)

        $sql="SELECT
                $sqlcLabel AS label
                , SUM(a.land_convert_forest_2010_yes) AS land_convert_forest_2010_yes
                , SUM(a.land_convert_forest_2010_no) AS land_convert_forest_2010_no
                , SUM(a.land_convert_peat_2010_yes) AS land_convert_peat_2010_yes
                , SUM(a.land_convert_peat_2010_no) AS land_convert_peat_2010_no
                , SUM(a.labor_right_abuses_yes) AS labor_right_abuses_yes
                , SUM(a.labor_right_abuses_no) AS labor_right_abuses_no
                , SUM(a.sustain_smart_agri_farmer_yes) AS sustain_smart_agri_farmer_yes
                , SUM(a.sustain_smart_agri_farmer_no) AS sustain_smart_agri_farmer_no
                , SUM(a.farmer_willing_survey_yes) AS farmer_willing_survey_yes
                , SUM(a.farmer_willing_survey_no) AS farmer_willing_survey_no
            FROM
                dash_pro_ndpe a
                LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                LEFT JOIN ktv_subdistrict subdis ON subdis.`SubDistrictID` = kv.`SubDistrictID`
                LEFT JOIN ktv_district dis ON dis.`DistrictID` = subdis.`DistrictID`
                LEFT JOIN ktv_province prov ON prov.`ProvinceID` = dis.`ProvinceID`
            WHERE
                1 = 1
                $sqlcWhere
                $sqldWhereFtype
                $sqlcHakAkses
            GROUP BY label
            ORDER BY label";
        $query = $this->db->query($sql,array());
        $result['dataChart'] = $query->result_array();

        //data display chart ================================================== (end)

        return $result;
    }

}