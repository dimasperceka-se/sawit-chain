<?php
/**
 * @Author: nikolius
 * @Date:   2017-09-19 14:20:07
 * @Last Modified by:   nikolius
 * @Last Modified time: 2018-02-09 14:44:51
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mpro_survey_ppi extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    private function truncateTable($namaTabel){
        $sql="DELETE FROM `$namaTabel`";
        $query = $this->db->query($sql);
    }

    private function generateScorePpi(){
        $this->db->trans_begin();

        $sql="UPDATE ktv_survey_household tbl_upd
            INNER JOIN (
                SELECT
                    tbl_survey_score.MemberID
                    , tbl_survey_score.SurveyNr
                    , tbl_survey_score.DateCollection
                    , tbl_survey_score.TotalScore
                    , ppi_calc.`National`
                    , ppi_calc.`$1.25/day`
                    , ppi_calc.`$2.5/day`
                FROM
                (
                SELECT
                    sur_hh.MemberID
                    , sur_hh.SurveyNr
                    , sur_hh.DateCollection
                    , CASE sur_hh.HhMember
                        WHEN 1 THEN 0
                        WHEN 2 THEN 6
                        WHEN 3 THEN 12
                        WHEN 4 THEN 18
                        WHEN 5 THEN 24
                        WHEN 6 THEN 35
                    END + CASE sur_hh.HhInSchoolEarlyAge
                        WHEN 1 THEN 0
                        WHEN 2 THEN 0
                        WHEN 3 THEN 2
                    END + CASE sur_hh.FemaleEduLevel
                        WHEN 1 THEN 0
                        WHEN 2 THEN 3
                        WHEN 3 THEN 4
                        WHEN 4 THEN 4
                        WHEN 5 THEN 5
                        WHEN 6 THEN 7
                        WHEN 7 THEN 18
                    END + CASE sur_hh.MaleMainOccu
                        WHEN 1 THEN 0
                        WHEN 2 THEN 0
                        WHEN 3 THEN 1
                        WHEN 4 THEN 3
                        WHEN 5 THEN 4
                        WHEN 6 THEN 6
                    END + CASE sur_hh.TypeOfToilet
                        WHEN 1 THEN 0
                        WHEN 2 THEN 2
                        WHEN 3 THEN 4
                    END + CASE sur_hh.TypeOfFloor
                        WHEN 1 THEN 0
                        WHEN 2 THEN 5
                    END + CASE sur_hh.PrimaryFuel
                        WHEN 1 THEN 0
                        WHEN 2 THEN 5
                    END + CASE sur_hh.OwnRefri
                        WHEN 1 THEN 9
                        WHEN 2 THEN 0
                    END + CASE sur_hh.Own12KgGas
                        WHEN 1 THEN 7
                        WHEN 2 THEN 0
                    END + CASE sur_hh.OwnMotor
                        WHEN 1 THEN 9
                        WHEN 2 THEN 0
                    END AS TotalScore
                FROM
                    ktv_survey_household sur_hh
                WHERE
                    sur_hh.StatusCode = 'active' AND
                    (sur_hh.DateGeneratedPPI IS NULL) OR (sur_hh.DateUpdated > sur_hh.DateGeneratedPPI)
                ) AS tbl_survey_score
                LEFT JOIN ktv_ppi_calculation ppi_calc ON ppi_calc.`Type`='PPI 2012' AND (tbl_survey_score.TotalScore BETWEEN ppi_calc.ScoreMin AND ppi_calc.ScoreMax)
            ) AS tbl_survey_calc ON
                tbl_upd.`MemberID` = tbl_survey_calc.MemberID AND
                tbl_upd.`SurveyNr` = tbl_survey_calc.SurveyNr AND
                tbl_upd.`DateCollection` = tbl_survey_calc.DateCollection
            SET
                tbl_upd.`Score` = tbl_survey_calc.TotalScore,
                tbl_upd.`National` = tbl_survey_calc.National,
                tbl_upd.`1.25/day` = tbl_survey_calc.`$1.25/day`,
                tbl_upd.`2.5/day` = tbl_survey_calc.`$2.5/day`,
                tbl_upd.`DateGeneratedPPI` = NOW()
            WHERE
                tbl_survey_calc.TotalScore IS NOT NULL";
        $query = $this->db->query($sql);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
    }

    public function generateDashProSurveyPpi_old(){
        $this->generateScorePpi();

        $this->db->trans_begin();

        //truncate dl tabelnya
        // $this->truncateTable('dash_pro_survey_ppi');

        $this->db->query("TRUNCATE TABLE dash_pro_survey_ppi;");

        //ambil data village nya yg ada
        $sql="SELECT
                kd.ProvinceID AS ProvinceID
                , kd.DistrictID AS DistrictID
                , ksd.SubDistrictID AS SubDistrictID
                , a.`VillageID`
            FROM
                ktv_members a
                JOIN ktv_member_role r ON a.MemberID = r.MemberID AND r.MRoleID = 1 #ROLE PETANI
                LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
            WHERE
                a.`StatusCode` = 'active'
                AND a.`VillageID` IS NOT NULL
                AND a.VillageID != ''
                AND a.VillageID != '0'
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

        for ($i=0; $i < count($dataRegion); $i++) {
            for ($j=0; $j < count($dataPartner); $j++) {

                $sql="
                    INSERT INTO dash_pro_survey_ppi
                    SELECT
                        tbl_region.ProvinceID
                        , tbl_region.DistrictID
                        , tbl_region.SubDistrictID
                        , tbl_region.VillageID
                        , tbl_region.PartnerID

                        , tbl_join_hh_baseline.count_baseline
                        , tbl_join_hh_baseline.national_sum_baseline
                        , tbl_join_hh_baseline.national_count_baseline
                        , tbl_join_hh_baseline.125_baseline
                        , tbl_join_hh_baseline.25_baseline

                        , tbl_join_hh_postline.count_postline
                        , tbl_join_hh_postline.national_sum_postline
                        , tbl_join_hh_postline.national_count_postline
                        , tbl_join_hh_postline.125_postline
                        , tbl_join_hh_postline.25_postline

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
                                , SUM(IF(a.`SurveyNr` IS NOT NULL,1,NULL)) AS count_baseline
                                , SUM(IF(a.`National` > 0, a.`National`,NULL)) AS national_sum_baseline
                                , SUM(IF(a.`National` > 0, 1,NULL)) AS national_count_baseline
                                , SUM(IF(a.`1.25/day` > 0, a.`1.25/day`,NULL)) AS 125_baseline
                                , SUM(IF(a.`2.5/day` > 0, a.`2.5/day`,NULL)) AS 25_baseline
                            FROM
                                ktv_survey_household a
                                JOIN ktv_members m ON m.MemberID = a.MemberID
                                INNER JOIN ktv_access_partner_member acc_pm ON m.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$dataPartner[$j]['PartnerID']}'
                            WHERE
                                a.`StatusCode` = 'active'
                                AND m.`StatusCode` = 'active'
                                AND a.`SurveyNr` = '0'
                                AND m.VillageID = '{$dataRegion[$i]['VillageID']}'
                            GROUP BY m.`VillageID`
                        ) AS tbl_join_hh_baseline ON
                            tbl_region.PartnerID = tbl_join_hh_baseline.PartnerID AND tbl_region.VillageID = tbl_join_hh_baseline.VillageID

                        LEFT JOIN (
                            SELECT
                                m.`VillageID`
                                , '{$dataPartner[$j]['PartnerID']}' AS PartnerID
                                , SUM(IF(a.`SurveyNr` IS NOT NULL,1,NULL)) AS count_postline
                                , SUM(IF(a.`National` > 0, a.`National`,NULL)) AS national_sum_postline
                                , SUM(IF(a.`National` > 0, 1,NULL)) AS national_count_postline
                                , SUM(IF(a.`1.25/day` > 0, a.`1.25/day`,NULL)) AS 125_postline
                                , SUM(IF(a.`2.5/day` > 0, a.`2.5/day`,NULL)) AS 25_postline
                            FROM
                                ktv_survey_household a
                                INNER JOIN (
                                    SELECT
                                        sub_a.`MemberID`, MAX(sub_a.`SurveyNr`) AS SurveyNr
                                    FROM
                                        ktv_survey_household sub_a
                                    WHERE
                                        sub_a.`StatusCode` = 'active'
                                    GROUP BY sub_a.`MemberID`
                                ) AS hh_latest ON
                                    a.`MemberID` = hh_latest.MemberID AND
                                    a.`SurveyNr` = hh_latest.SurveyNr
                                JOIN ktv_members m ON m.MemberID = a.MemberID
                                INNER JOIN ktv_access_partner_member acc_pm ON m.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$dataPartner[$j]['PartnerID']}'
                            WHERE
                                a.`StatusCode` = 'active'
                                AND m.`StatusCode` = 'active'
                                AND a.`SurveyNr` > '0'
                                AND m.VillageID = '{$dataRegion[$i]['VillageID']}'
                            GROUP BY m.`VillageID`
                        ) AS tbl_join_hh_postline ON
                            tbl_region.PartnerID = tbl_join_hh_postline.PartnerID AND tbl_region.VillageID = tbl_join_hh_postline.VillageID
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

    public function generateDashProSurveyPpi(){
        $this->generateScorePpi();

        $this->db->trans_begin();

        $this->db->query("DELETE FROM dash_pro_survey_ppi");

        /* $sql="SELECT
                GROUP_CONCAT(a.`PartnerID` SEPARATOR ',') as PartnerID
            FROM
                ktv_program_partner a
            WHERE
                a.`StatusCode` = 'active'
                AND a.IsGenDashboard = 'Yes'
            ORDER BY a.PartnerID ASC
            ";
        $query = $this->db->query($sql);
        $dataPartner = $query->result_array()[0]['PartnerID']; */

        $sql="SELECT
                a.PartnerID
            FROM
                ktv_program_partner a
            WHERE
                a.`StatusCode` = 'active'
                AND a.IsGenDashboard = 'Yes'
            ORDER BY a.PartnerID ASC
            ";
        $query = $this->db->query($sql);
        $dataPartnerGet = $query->result_array();

        foreach ($dataPartnerGet as $key => $value) {
            $dataPartner = $value["PartnerID"];
            
            $sql = "INSERT INTO dash_pro_survey_ppi
                    SELECT
                      kd.ProvinceID AS ProvinceID,
                      kd.DistrictID AS DistrictID,
                      ksd.SubDistrictID AS SubDistrictID,
                      a.VillageID,
                      acc_pm.apmPartnerID as PartnerID,
                      anu.count_baseline,
                      anu.national_sum_baseline,
                      anu.national_count_baseline,
                      anu.125_baseline,
                      anu.25_baseline,
                      anu2.count_postline,
                      anu2.national_sum_postline,
                      anu2.national_count_postline,
                      anu2.125_postline,
                      anu2.25_postline,
                      NOW() AS DateGenerated
                    FROM
                      ktv_members a
                      INNER JOIN ktv_member_role r
                        ON a.MemberID = r.MemberID
                        AND r.MRoleID = 1
                      LEFT JOIN ktv_village kv
                        ON kv.VillageID = a.VillageID
                      LEFT JOIN ktv_subdistrict ksd
                        ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                      LEFT JOIN ktv_district kd
                        ON kd.`DistrictID` = ksd.`DistrictID`
                      INNER JOIN ktv_access_partner_member acc_pm ON a.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID IN ({$dataPartner})
                      LEFT JOIN
                        (SELECT
                          m.`VillageID`,
                          acc_pm.apmPartnerID as PartnerID,
                          SUM(IF(a.`SurveyNr` IS NOT NULL, 1, NULL)) AS count_baseline,
                          SUM(
                            IF(
                              a.`National` > 0,
                              a.`National`,
                              NULL
                            )
                          ) AS national_sum_baseline,
                          SUM(IF(a.`National` > 0, 1, NULL)) AS national_count_baseline,
                          SUM(
                            IF(
                              a.`1.25/day` > 0,
                              a.`1.25/day`,
                              NULL
                            )
                          ) AS 125_baseline,
                          SUM(
                            IF(a.`2.5/day` > 0, a.`2.5/day`, NULL)
                          ) AS 25_baseline
                        FROM
                          ktv_survey_household a
                          JOIN ktv_members m
                            ON m.MemberID = a.MemberID
                          INNER JOIN ktv_access_partner_member acc_pm ON m.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID IN ({$dataPartner})
                        WHERE a.`StatusCode` = 'active'
                          AND m.`StatusCode` = 'active'
                          AND a.`SurveyNr` = '0'
                        GROUP BY m.`VillageID`) anu
                        ON anu.VillageID = a.VillageID
                      LEFT JOIN
                        (SELECT
                          m.`VillageID`,
                          acc_pm.apmPartnerID as PartnerID,
                          SUM(IF(a.`SurveyNr` IS NOT NULL, 1, NULL)) AS count_postline,
                          SUM(
                            IF(
                              a.`National` > 0,
                              a.`National`,
                              NULL
                            )
                          ) AS national_sum_postline,
                          SUM(IF(a.`National` > 0, 1, NULL)) AS national_count_postline,
                          SUM(
                            IF(
                              a.`1.25/day` > 0,
                              a.`1.25/day`,
                              NULL
                            )
                          ) AS 125_postline,
                          SUM(
                            IF(a.`2.5/day` > 0, a.`2.5/day`, NULL)
                          ) AS 25_postline
                        FROM
                          ktv_survey_household a
                          INNER JOIN
                            (SELECT
                              sub_a.`MemberID`,
                              MAX(sub_a.`SurveyNr`) AS SurveyNr
                            FROM
                              ktv_survey_household sub_a
                            WHERE sub_a.`StatusCode` = 'active'
                            GROUP BY sub_a.`MemberID`) AS hh_latest
                            ON a.`MemberID` = hh_latest.MemberID
                            AND a.`SurveyNr` = hh_latest.SurveyNr
                          JOIN ktv_members m
                            ON m.MemberID = a.MemberID
                          INNER JOIN ktv_access_partner_member acc_pm ON m.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID IN ({$dataPartner})
                        WHERE a.`StatusCode` = 'active'
                          AND m.`StatusCode` = 'active'
                          AND a.`SurveyNr` > '0'
                        GROUP BY m.`VillageID`) anu2
                        ON anu2.VillageID = a.VillageID
                    WHERE a.`StatusCode` = 'active'
                      AND a.`VillageID` IS NOT NULL
                      AND a.VillageID != ''
                      AND a.VillageID != '0'
                    GROUP BY a.`VillageID`";

            $query = $this->db->query($sql);
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

    public function getDisplaySurveyPpi($ProvinceID, $DistrictID){
        if($ProvinceID == ""){
            $sqlcLabel = "Province";
            $sqlcJoin = " LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                        LEFT JOIN ktv_subdistrict subdis ON subdis.SubDistrictID = kv.SubDistrictID
                        LEFT JOIN ktv_district dis ON dis.DistrictID = subdis.DistrictID
                        LEFT JOIN ktv_province prov ON prov.ProvinceID = dis.ProvinceID";
            $sqlcWhere = "";
        } elseif($DistrictID == "") {
            $sqlcLabel = "District";
            $sqlcJoin = " LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                        LEFT JOIN ktv_subdistrict subdis ON subdis.SubDistrictID = kv.SubDistrictID
                        LEFT JOIN ktv_district dis ON dis.DistrictID = subdis.DistrictID";
            $sqlcWhere = "AND dis.ProvinceID = '$ProvinceID'";
        } else {
            $sqlcLabel = "SubDistrict";
            $sqlcJoin = " LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                        LEFT JOIN ktv_subdistrict subdis ON subdis.SubDistrictID = kv.SubDistrictID";
            $sqlcWhere = "AND subdis.DistrictID = '$DistrictID'";
        }

        //buat SqlHakAksesKontrol (begin)
        if($_SESSION['is_admin'] == "1"){
            $sqlcHakAkses = " AND a.PartnerID = '1' #Partner Koltiva ";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            //cek ktv_access_staff
            $sqlcHakAkses = " AND subdis.DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlcHakAkses .= " AND a.PartnerID = '{$_SESSION['PartnerID']}' ";
        } else {
            //cek ktv_access_staff
            $sqlcHakAkses = " AND subdis.DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlcHakAkses .= " AND a.PartnerID = '1' #Partner Koltiva ";
        }
        //buat SqlHakAksesKontrol (end)

        $sql="SELECT
                $sqlcLabel AS label
                ,IFNULL(SUM(`count_baseline`),0) AS `count_baseline`
                ,IFNULL(SUM(`count_baseline`),0) AS `count_baseline`
                ,IFNULL(SUM(`national_sum_baseline`),0) AS `National_sum_baseline`
                ,IFNULL(SUM(`national_count_baseline`),0) AS `National_count_baseline`
                ,IFNULL(SUM(`125_baseline`),0) AS `1.25_baseline`
                ,IFNULL(SUM(`25_baseline`),0) AS `2.5_baseline`
                ,IFNULL(SUM(`count_postline`),0) AS `count_postline`
                ,IFNULL(SUM(`national_sum_postline`),0) AS `National_sum_postline`
                ,IFNULL(SUM(`national_count_postline`),0) AS `National_count_postline`
                ,IFNULL(SUM(`125_postline`),0) AS `1.25_postline`
                ,IFNULL(SUM(`25_postline`),0) AS `2.5_postline`
                ,a.DateGenerated
            FROM
                dash_pro_survey_ppi a
                $sqlcJoin
            WHERE
                1 = 1
                $sqlcWhere
                $sqlcHakAkses
            GROUP BY label
            ORDER BY label";
        $query = $this->db->query($sql,array());
        $result['ppi'] = $query->result_array();

        foreach ($result['ppi'] as $key => $value) {
            if (empty($value['label'])) {
                unset($result['ppi'][$key]);
            }    
        }

        $result['ppi'] = array_values($result['ppi']);

        $result['dateGen'] = $result['ppi'][0]['DateGenerated'];

        return $result;
    }

}
?>