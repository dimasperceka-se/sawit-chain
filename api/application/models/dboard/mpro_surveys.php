<?php

/**
 * @Author: nikolius
 * @Date:   2017-09-15 15:43:36
 * @Last Modified by:   nikolius
 * @Last Modified time: 2017-12-14 10:36:00
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mpro_surveys extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    private function truncateTable($namaTabel){
        $sql="DELETE FROM `$namaTabel`";
        $query = $this->db->query($sql);
    }

    public function generateDashProSurveys(){
        $this->db->trans_begin();

        //truncate dl tabelnya
        $this->truncateTable('dash_pro_surveys');
        $this->truncateTable('dash_pro_surveys_year');

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

                $sql="INSERT INTO dash_pro_surveys
                    SELECT
                        tbl_region.ProvinceID
                        , tbl_region.DistrictID
                        , tbl_region.SubDistrictID
                        , tbl_region.VillageID
                        , tbl_region.PartnerID

                        , tbl_join_farmer.total_farmer_baseline AS farmer_baseline
                        , tbl_join_farmer.total_farmer_postline AS farmer_postline

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
                                tblg_farmer.VillageID
                                , tblg_farmer.PartnerID
                                , SUM(
                                    IF(tblg_farmer.total_plot_baseline > 0,1,
                                        IF(tblg_farmer.total_plot_baseline = 0,0,NULL)
                                    )
                                ) AS total_farmer_baseline
                                , SUM(
                                    IF(tblg_farmer.total_plot_postline > 0,1,
                                        IF(tblg_farmer.total_plot_postline = 0,0,NULL)
                                    )
                                ) AS total_farmer_postline
                            FROM
                            (
                            SELECT
                                a.`VillageID`
                                , '{$dataPartner[$j]['PartnerID']}' AS PartnerID
                                , a.`MemberID`
                                , SUM(IF(p.`PlotNr` IS NOT NULL,1,NULL)) AS total_plot_baseline
                                , SUM(IF(p_post.`PlotNr` IS NOT NULL,1,NULL)) AS total_plot_postline
                            FROM
                                ktv_members a
                                INNER JOIN ktv_member_role r ON a.MemberID = r.MemberID AND r.MRoleID = 1 #ROLE PETANI
                                INNER JOIN ktv_access_partner_member acc_pm ON a.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$dataPartner[$j]['PartnerID']}'
                                LEFT JOIN ktv_survey_plot p
                                    ON a.`MemberID` = p.`MemberID` AND p.`SurveyNr` = '0' AND p.`StatusCode` = 'active'
                                LEFT JOIN ktv_survey_plot p_post
                                    ON a.`MemberID` = p_post.`MemberID` AND p_post.`SurveyNr` != '0' AND p_post.`StatusCode` = 'active'
                            WHERE
                                a.`StatusCode` = 'active'
                                AND a.VillageID = '{$dataRegion[$i]['VillageID']}'
                            GROUP BY a.`MemberID`
                            ) AS tblg_farmer
                            GROUP BY tblg_farmer.VillageID
                        ) AS tbl_join_farmer
                            ON tbl_region.PartnerID = tbl_join_farmer.PartnerID AND tbl_region.VillageID = tbl_join_farmer.VillageID
                ";
                $query = $this->db->query($sql);

                $sql="
                    INSERT INTO dash_pro_surveys_year

                    SELECT
                        tbl_region_year.ProvinceID
                        , tbl_region_year.DistrictID
                        , tbl_region_year.SubDistrictID
                        , tbl_region_year.VillageID
                        , tbl_region_year.PartnerID
                        , tbl_region_year.YearCollect
                        , tbl_plot_baseline.total_plot_baseline
                        , tbl_plot_postline.total_plot_postline
                        , tbl_plot_baseline.production_baseline
                        , tbl_plot_postline.production_postline
                        , tbl_plot_baseline.total_ha_baseline
                        , tbl_plot_postline.total_ha_postline
                        , tbl_plot_baseline.production_tree_baseline
                        , tbl_plot_postline.production_tree_postline
                        , tbl_plot_baseline.total_tm_baseline
                        , tbl_plot_postline.total_tm_postline
                        , NOW() AS DateGenerated
                    FROM
                        (
                        SELECT
                            kd.ProvinceID AS ProvinceID
                            , kd.DistrictID AS DistrictID
                            , ksd.SubDistrictID AS SubDistrictID
                            , a.`VillageID`
                            , '{$dataPartner[$j]['PartnerID']}' AS PartnerID
                            , tbl_plot_year.YearCollect
                        FROM
                            ktv_members a
                            JOIN ktv_member_role r ON a.MemberID = r.MemberID AND r.MRoleID = 1 #ROLE PETANI
                            LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                            LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
                            LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`

                            INNER JOIN (
                                SELECT
                                    '{$dataPartner[$j]['PartnerID']}' AS PartnerID
                                    , YEAR(p.`DateCollection`) AS YearCollect
                                FROM
                                    ktv_survey_plot p
                                WHERE
                                    p.`StatusCode` = 'active'
                                GROUP BY YearCollect
                            ) AS tbl_plot_year
                        WHERE
                            a.`StatusCode` = 'active'
                            AND a.`VillageID` IS NOT NULL
                            AND a.VillageID != ''
                            AND a.VillageID != '0'
                        GROUP BY a.`VillageID`, tbl_plot_year.YearCollect
                        ) AS tbl_region_year

                        LEFT JOIN (

                            SELECT
                                m.`VillageID`
                                , '{$dataPartner[$j]['PartnerID']}' AS PartnerID
                                , YEAR(p.`DateCollection`) AS YearCollect
                                , COUNT(p.`PlotNr`) AS total_plot_baseline
                                , SUM(
                                    IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.AnnualProduction, NULL)
                                ) AS production_baseline
                                , SUM(
                                    IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.GardenAreaHa, NULL)
                                ) AS total_ha_baseline
                                , SUM(
                                    IF(p.AnnualProduction > 0 && p.TreeTM > 0, p.AnnualProduction, NULL)
                                ) AS production_tree_baseline
                                , SUM(
                                    IF(p.AnnualProduction > 0 && p.TreeTM > 0, p.TreeTM, NULL)
                                ) AS total_tm_baseline
                            FROM
                                ktv_survey_plot p
                                JOIN ktv_members m ON m.MemberID = p.MemberID
                                JOIN ktv_member_role r ON m.MemberID = r.MemberID AND r.MRoleID = 1 #ROLE PETANI
                                INNER JOIN ktv_access_partner_member acc_pm ON m.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$dataPartner[$j]['PartnerID']}'
                            WHERE
                                p.`StatusCode` = 'active'
                                AND m.StatusCode = 'active'
                                AND p.`SurveyNr` = '0'
                            GROUP BY m.`VillageID`, YearCollect

                        ) AS tbl_plot_baseline ON
                            tbl_region_year.VillageID = tbl_plot_baseline.VillageID AND
                            tbl_region_year.PartnerID = tbl_plot_baseline.PartnerID AND
                            tbl_region_year.YearCollect = tbl_plot_baseline.YearCollect

                        LEFT JOIN (

                            SELECT
                                m.`VillageID`
                                , '{$dataPartner[$j]['PartnerID']}' AS PartnerID
                                , YEAR(a.`DateCollection`) AS YearCollect
                                , COUNT(a.`PlotNr`) AS total_plot_postline
                                , SUM(
                                    IF(a.AnnualProduction > 0 && a.GardenAreaHa > 0, a.AnnualProduction, NULL)
                                ) AS production_postline
                                , SUM(
                                    IF(a.AnnualProduction > 0 && a.GardenAreaHa > 0, a.GardenAreaHa, NULL)
                                ) AS total_ha_postline
                                , SUM(
                                    IF(a.AnnualProduction > 0 && a.TreeTM > 0, a.AnnualProduction, NULL)
                                ) AS production_tree_postline
                                , SUM(
                                    IF(a.AnnualProduction > 0 && a.TreeTM > 0, a.TreeTM, NULL)
                                ) AS total_tm_postline
                            FROM
                                ktv_survey_plot a
                                JOIN (SELECT
                                    p.MemberID, p.PlotNr, MAX(p.SurveyNr) AS SurveyNr
                                FROM ktv_survey_plot p WHERE p.`StatusCode` = 'active'
                                GROUP BY p.MemberID, p.PlotNr) AS gar_latest
                                    ON a.MemberID = gar_latest.MemberID AND a.PlotNr = gar_latest.PlotNr AND a.SurveyNr = gar_latest.SurveyNr
                                JOIN ktv_members m ON m.MemberID = a.MemberID
                                JOIN ktv_member_role r ON m.MemberID = r.MemberID AND r.MRoleID = 1 #ROLE PETANI
                                INNER JOIN ktv_access_partner_member acc_pm ON m.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$dataPartner[$j]['PartnerID']}'
                            WHERE
                                a.`StatusCode` = 'active'
                                AND m.StatusCode = 'active'
                                AND a.`SurveyNr` > '0'
                            GROUP BY m.`VillageID`, YearCollect

                        ) AS tbl_plot_postline ON
                            tbl_region_year.VillageID = tbl_plot_postline.VillageID AND
                            tbl_region_year.PartnerID = tbl_plot_postline.PartnerID AND
                            tbl_region_year.YearCollect = tbl_plot_postline.YearCollect

                    WHERE
                        tbl_region_year.VillageID = '{$dataRegion[$i]['VillageID']}'
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

    public function generateDashProSurveysOptimize(){
        $this->db->trans_begin();

        //truncate dl tabelnya
        $this->truncateTable('dash_pro_surveys');
        $this->truncateTable('dash_pro_surveys_year');

        //insert kedua table
        $sql="INSERT INTO dash_pro_surveys
            SELECT
                     t1.ProvinceID
                   , t1.DistrictID
                   , t1.SubDistrictID
                   , t1.PartnerID
                   , 'Smallholder' AS `Type`
                   , SUM(
                         IF(t1.total_plot_baseline > 0,1,
                             IF(t1.total_plot_baseline = 0,0,NULL)
                         )
                     ) AS farmer_baseline
                   , SUM(
                         IF(t1.total_plot_postline > 0,1,
                             IF(t1.total_plot_postline = 0,0,NULL)
                         )
                     ) AS farmer_postline
                   , NOW() AS DateGenerated
                FROM
                   (SELECT      
                      prov.ProvinceID AS ProvinceID
                      , d.DistrictID AS DistrictID
                      , sd.SubDistrictID AS SubDistrictID
                      , v.VillageID AS VillageID
                      , pp.PartnerID AS PartnerID
                      , SUM(IF(p.`plotnr` IS NOT NULL,1,NULL))      AS total_plot_baseline
                      , SUM(IF(p_post.`plotnr` IS NOT NULL,1,NULL)) AS total_plot_postline
                      FROM       ktv_members m
                      INNER JOIN ktv_member_role r
                         ON         m.MemberID = r.MemberID
                         AND        r.MRoleID = 1 #role petani
                      JOIN ktv_village v 
                         ON v.VillageID = m.VillageID
                      JOIN ktv_subdistrict sd 
                         ON sd.SubDistrictID = v.SubDistrictID
                      JOIN ktv_district d 
                         ON d.DistrictID = sd.DistrictID
                      JOIN ktv_province prov 
                         ON prov.ProvinceID = d.ProvinceID 
                      INNER JOIN ktv_access_partner_member acc_pm
                         ON         m.MemberID = acc_pm.apmMemberID
                      INNER JOIN ktv_program_partner pp
                         ON         pp.PartnerID = acc_pm.apmPartnerID
                      LEFT JOIN  ktv_survey_plot p
                         ON         m.`MemberID` = p.`MemberID`
                         AND        p.`SurveyNr` = '0'
                         AND        p.`StatusCode` = 'active'
                      LEFT JOIN  ktv_survey_plot p_post
                         ON         m.`MemberID` = p_post.`MemberID`
                         AND        p_post.`SurveyNr` != '0'
                         AND        p_post.`StatusCode` = 'active'
                      WHERE      
                         m.`StatusCode` = 'active'
                         AND
                         m.`VillageID` IS NOT NULL
                         AND 
                         m.VillageID != ''
                         AND 
                         m.VillageID != '0'
                         AND
                         pp.`StatusCode` = 'active'
                         AND
                         pp.IsGenDashboard = 'Yes'
                         AND
                         p.`SurveyNr` IS NOT NULL
                      GROUP BY
                         m.`MemberID`
                         , prov.ProvinceID
                         , d.DistrictID
                         , sd.SubDistrictID
                         , pp.PartnerID
                    ) t1
                GROUP BY
                     t1.ProvinceID
                    , t1.DistrictID
                    , t1.SubDistrictID
                    , t1.PartnerID
                ORDER BY 
                    t1.`VillageID`";
        $query = $this->db->query($sql);

        $sql = "INSERT INTO dash_pro_surveys
        SELECT
            t1.ProvinceID
            , t1.DistrictID
            , t1.SubDistrictID
            , t1.PartnerID
            , 'SME' AS `Type`
            , SUM(
                    IF(t1.total_plot_baseline > 0,1,
                        IF(t1.total_plot_baseline = 0,0,NULL)
                    )
                ) AS farmer_baseline
            , SUM(
                    IF(t1.total_plot_postline > 0,1,
                        IF(t1.total_plot_postline = 0,0,NULL)
                    )
                ) AS farmer_postline
            , NOW() AS DateGenerated
        FROM
            (SELECT      
                prov.ProvinceID AS ProvinceID
                , d.DistrictID AS DistrictID
                , sd.SubDistrictID AS SubDistrictID
                , v.VillageID AS VillageID
                , pp.PartnerID AS PartnerID
                , SUM(IF(p.`plotnr` IS NOT NULL,1,NULL))      AS total_plot_baseline
                , SUM(IF(p_post.`plotnr` IS NOT NULL,1,NULL)) AS total_plot_postline
                FROM       ktv_members m
                JOIN ktv_village v 
                    ON v.VillageID = m.VillageID
                JOIN ktv_subdistrict sd 
                    ON sd.SubDistrictID = v.SubDistrictID
                JOIN ktv_district d 
                    ON d.DistrictID = sd.DistrictID
                JOIN ktv_province prov 
                    ON prov.ProvinceID = d.ProvinceID 
                INNER JOIN ktv_access_partner_member acc_pm
                    ON         m.MemberID = acc_pm.apmMemberID
                INNER JOIN ktv_program_partner pp
                    ON         pp.PartnerID = acc_pm.apmPartnerID
                LEFT JOIN  ktv_survey_plot_sme p
                    ON         m.`MemberID` = p.`MemberID`
                    AND        p.`SurveyNr` = '0'
                    AND        p.`StatusCode` = 'active'
                LEFT JOIN  ktv_survey_plot_sme p_post
                    ON         m.`MemberID` = p_post.`MemberID`
                    AND        p_post.`SurveyNr` != '0'
                    AND        p_post.`StatusCode` = 'active'
                WHERE      
                    m.`StatusCode` = 'active'
                    AND m.`VillageID` IS NOT NULL AND m.VillageID != '' AND m.VillageID != '0'
                    AND pp.`StatusCode` = 'active'
                    AND pp.IsGenDashboard = 'Yes'
                    AND p.`SurveyNr` IS NOT NULL
                    AND m.`MemberID` IN (
                    SELECT
                        DISTINCT suba.`MemberID`
                    FROM
                        ktv_survey_plot_sme suba
                    WHERE
                        suba.`MemberID` > 0
                    )
                GROUP BY m.`MemberID`, prov.ProvinceID, d.DistrictID, sd.SubDistrictID, pp.PartnerID
            ) t1
        GROUP BY t1.ProvinceID, t1.DistrictID, t1.SubDistrictID, t1.PartnerID
        ORDER BY t1.`VillageID`";
        $query = $this->db->query($sql);

        $sql="INSERT INTO dash_pro_surveys_year
            SELECT
                tbl_region_year.ProvinceID
                , tbl_region_year.DistrictID
                , tbl_region_year.SubDistrictID
                , tbl_region_year.PartnerID
                , tbl_region_year.YearCollect
                , 'Smallholder' AS `Type`
                , tbl_plot_baseline.total_plot_baseline
                , tbl_plot_postline.total_plot_postline
                , tbl_plot_baseline.production_baseline
                , tbl_plot_postline.production_postline
                , tbl_plot_baseline.total_ha_baseline
                , tbl_plot_postline.total_ha_postline
                , tbl_plot_baseline.production_tree_baseline
                , tbl_plot_postline.production_tree_postline
                , tbl_plot_baseline.total_tm_baseline
                , tbl_plot_postline.total_tm_postline
                , NOW() AS DateGenerated
            FROM
                (
                SELECT
                    prov.ProvinceID AS ProvinceID
                    , d.DistrictID AS DistrictID
                    , sd.SubDistrictID AS SubDistrictID
                    , v.VillageID AS VillageID
                    , pp.PartnerID AS PartnerID
                    , YEAR(p.`DateCollection`) AS YearCollect
                FROM
                    ktv_members a
                    JOIN ktv_member_role r ON a.MemberID = r.MemberID AND r.MRoleID = 1 #ROLE PETANI
                    JOIN ktv_village v ON v.VillageID = a.VillageID
                    JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
                    JOIN ktv_district d ON d.DistrictID = sd.DistrictID
                    JOIN ktv_province prov ON prov.ProvinceID = d.ProvinceID 
                    INNER JOIN ktv_survey_plot p ON p.MemberID = a.MemberID
                    INNER JOIN ktv_access_partner_member acc_pm ON acc_pm.apmMemberID = a.MemberID
                    INNER JOIN ktv_program_partner pp ON pp.PartnerID = acc_pm.apmPartnerID
                WHERE
                    a.`StatusCode` = 'active'
                    AND a.`VillageID` IS NOT NULL
                    AND a.VillageID != ''
                    AND a.VillageID != '0'
                    AND p.`StatusCode` = 'active'
                    AND pp.StatusCode = 'active'
                    AND pp.IsGenDashboard = 'Yes'
                GROUP BY 
                prov.ProvinceID
                , d.DistrictID
                , sd.SubDistrictID
                , pp.PartnerID
                , YearCollect
                ) AS tbl_region_year
            LEFT JOIN (
                SELECT
                    m.`VillageID`
                    , pp.PartnerID AS PartnerID
                    , YEAR(p.`DateCollection`) AS YearCollect
                    , SUM(IF(((p.Latitude IS NOT NULL AND p.Longitude IS NOT NULL) OR (p.Latitude != 0 AND p.Longitude != 0)) AND (p.PlotNr IS NOT NULL AND p.PlotNr != 0), 1, 0)) AS total_plot_baseline
                    , SUM(
                        IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.AnnualProduction, NULL)
                    ) AS production_baseline
                    , SUM(
                        IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.GardenAreaHa, NULL)
                    ) AS total_ha_baseline
                    , SUM(
                        IF(p.AnnualProduction > 0 && p.TreeTM > 0, p.AnnualProduction, NULL)
                    ) AS production_tree_baseline
                    , SUM(
                        IF(p.AnnualProduction > 0 && p.TreeTM > 0, p.TreeTM, NULL)
                    ) AS total_tm_baseline
                FROM
                    ktv_survey_plot p
                    JOIN ktv_members m ON m.MemberID = p.MemberID
                    JOIN ktv_member_role r ON m.MemberID = r.MemberID AND r.MRoleID = 1 #ROLE PETANI
                    JOIN ktv_village v ON v.VillageID = m.VillageID
                    JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
                    JOIN ktv_district d ON d.DistrictID = sd.DistrictID
                    JOIN ktv_province prov ON prov.ProvinceID = d.ProvinceID 
                    INNER JOIN ktv_access_partner_member acc_pm ON m.MemberID = acc_pm.apmMemberID 
                    INNER JOIN ktv_program_partner pp ON pp.PartnerID = acc_pm.apmPartnerID
                WHERE
                    p.`StatusCode` = 'active'
                    AND m.StatusCode = 'active'
                    AND pp.StatusCode = 'active'
                GROUP BY 
                 prov.ProvinceID
                , d.DistrictID
                , sd.SubDistrictID
                , pp.PartnerID 
                , YearCollect
            ) AS tbl_plot_baseline 
                ON tbl_region_year.VillageID = tbl_plot_baseline.VillageID
                AND tbl_region_year.PartnerID = tbl_plot_baseline.PartnerID
                AND tbl_region_year.YearCollect = tbl_plot_baseline.YearCollect
            LEFT JOIN (
                SELECT
                    m.`VillageID`
                    , pp.PartnerID AS PartnerID
                    , YEAR(a.`DateCollection`) AS YearCollect
                    , COUNT(a.`PlotNr`) AS total_plot_postline
                    , SUM(
                        IF(a.AnnualProduction > 0 && a.GardenAreaHa > 0, a.AnnualProduction, NULL)
                    ) AS production_postline
                    , SUM(
                        IF(a.AnnualProduction > 0 && a.GardenAreaHa > 0, a.GardenAreaHa, NULL)
                    ) AS total_ha_postline
                    , SUM(
                        IF(a.AnnualProduction > 0 && a.TreeTM > 0, a.AnnualProduction, NULL)
                    ) AS production_tree_postline
                    , SUM(
                        IF(a.AnnualProduction > 0 && a.TreeTM > 0, a.TreeTM, NULL)
                    ) AS total_tm_postline
                FROM
                    ktv_survey_plot a
                    JOIN (  
                            SELECT
                                p.MemberID, p.PlotNr, MAX(p.SurveyNr) AS SurveyNr
                            FROM ktv_survey_plot p 
                            WHERE p.`StatusCode` = 'active'
                            GROUP BY p.MemberID, p.PlotNr
                    ) AS gar_latest 
                    ON a.MemberID = gar_latest.MemberID 
                    AND a.PlotNr = gar_latest.PlotNr 
                    AND a.SurveyNr = gar_latest.SurveyNr
                    JOIN ktv_members m ON m.MemberID = a.MemberID
                    JOIN ktv_member_role r ON m.MemberID = r.MemberID AND r.MRoleID = 1 #ROLE PETANI
                    JOIN ktv_village v ON v.VillageID = m.VillageID
                    JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
                    JOIN ktv_district d ON d.DistrictID = sd.DistrictID
                    JOIN ktv_province prov ON prov.ProvinceID = d.ProvinceID 
                    INNER JOIN ktv_access_partner_member acc_pm ON m.MemberID = acc_pm.apmMemberID 
                    INNER JOIN ktv_program_partner pp ON pp.PartnerID = acc_pm.apmPartnerID
                WHERE
                    a.`StatusCode` = 'active'
                    AND m.StatusCode = 'active'
                    AND a.`SurveyNr` > '0'
                    AND pp.StatusCode = 'active'
                    AND pp.IsGenDashboard = 'Yes'
                GROUP BY 
                prov.ProvinceID
                , d.DistrictID
                , sd.SubDistrictID
                , pp.PartnerID
                , YearCollect
            ) AS tbl_plot_postline ON
                tbl_region_year.VillageID = tbl_plot_postline.VillageID 
                AND tbl_region_year.PartnerID = tbl_plot_postline.PartnerID 
                AND tbl_region_year.YearCollect = tbl_plot_postline.YearCollect
            ORDER BY tbl_region_year.VillageID, tbl_region_year.PartnerID";
        $query = $this->db->query($sql);

        $sql = "INSERT INTO dash_pro_surveys_year
        SELECT
                tbl_region_year.ProvinceID
                , tbl_region_year.DistrictID
                , tbl_region_year.SubDistrictID
                , tbl_region_year.PartnerID
                , tbl_region_year.YearCollect
                , 'SME' AS `Type`
                , tbl_plot_baseline.total_plot_baseline
                , tbl_plot_postline.total_plot_postline
                , tbl_plot_baseline.production_baseline
                , tbl_plot_postline.production_postline
                , tbl_plot_baseline.total_ha_baseline
                , tbl_plot_postline.total_ha_postline
                , tbl_plot_baseline.production_tree_baseline
                , tbl_plot_postline.production_tree_postline
                , tbl_plot_baseline.total_tm_baseline
                , tbl_plot_postline.total_tm_postline
                , NOW() AS DateGenerated
            FROM
                (
                SELECT
                    prov.ProvinceID AS ProvinceID
                    , d.DistrictID AS DistrictID
                    , sd.SubDistrictID AS SubDistrictID
                    , v.VillageID AS VillageID
                    , pp.PartnerID AS PartnerID
                    , YEAR(p.`DateCollection`) AS YearCollect
                FROM
                    ktv_members a
                    JOIN ktv_village v ON v.VillageID = a.VillageID
                    JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
                    JOIN ktv_district d ON d.DistrictID = sd.DistrictID
                    JOIN ktv_province prov ON prov.ProvinceID = d.ProvinceID 
                    INNER JOIN ktv_survey_plot_sme p ON p.MemberID = a.MemberID
                    INNER JOIN ktv_access_partner_member acc_pm ON acc_pm.apmMemberID = a.MemberID
                    INNER JOIN ktv_program_partner pp ON pp.PartnerID = acc_pm.apmPartnerID
                WHERE
                    a.`StatusCode` = 'active'
                    AND a.`VillageID` IS NOT NULL
                    AND a.VillageID != ''
                    AND a.VillageID != '0'
                    AND p.`StatusCode` = 'active'
                    AND pp.StatusCode = 'active'
                    AND pp.IsGenDashboard = 'Yes'
                GROUP BY prov.ProvinceID, d.DistrictID, sd.SubDistrictID, pp.PartnerID, YearCollect
                ) AS tbl_region_year
            LEFT JOIN (
                SELECT
                    m.`VillageID`
                    , pp.PartnerID AS PartnerID
                    , YEAR(p.`DateCollection`) AS YearCollect
                    , COUNT(p.`PlotNr`) AS total_plot_baseline
                    , SUM(
                        IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.AnnualProduction, NULL)
                    ) AS production_baseline
                    , SUM(
                        IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.GardenAreaHa, NULL)
                    ) AS total_ha_baseline
                    , SUM(
                        IF(p.AnnualProduction > 0 && p.TreeTM > 0, p.AnnualProduction, NULL)
                    ) AS production_tree_baseline
                    , SUM(
                        IF(p.AnnualProduction > 0 && p.TreeTM > 0, p.TreeTM, NULL)
                    ) AS total_tm_baseline
                FROM
                    ktv_survey_plot_sme p
                    JOIN ktv_members m ON m.MemberID = p.MemberID
                    JOIN ktv_village v ON v.VillageID = m.VillageID
                    JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
                    JOIN ktv_district d ON d.DistrictID = sd.DistrictID
                    JOIN ktv_province prov ON prov.ProvinceID = d.ProvinceID 
                    INNER JOIN ktv_access_partner_member acc_pm ON m.MemberID = acc_pm.apmMemberID 
                    INNER JOIN ktv_program_partner pp ON pp.PartnerID = acc_pm.apmPartnerID
                WHERE
                    p.`StatusCode` = 'active'
                    AND m.StatusCode = 'active'
                    AND p.`SurveyNr` = '0'
                    AND pp.StatusCode = 'active'
                    AND pp.IsGenDashboard = 'Yes'
                GROUP BY prov.ProvinceID, d.DistrictID, sd.SubDistrictID, pp.PartnerID , YearCollect
            ) AS tbl_plot_baseline 
                ON tbl_region_year.VillageID = tbl_plot_baseline.VillageID
                AND tbl_region_year.PartnerID = tbl_plot_baseline.PartnerID
                AND tbl_region_year.YearCollect = tbl_plot_baseline.YearCollect
            LEFT JOIN (
                SELECT
                    m.`VillageID`
                    , pp.PartnerID AS PartnerID
                    , YEAR(a.`DateCollection`) AS YearCollect
                    , COUNT(a.`PlotNr`) AS total_plot_postline
                    , SUM(
                        IF(a.AnnualProduction > 0 && a.GardenAreaHa > 0, a.AnnualProduction, NULL)
                    ) AS production_postline
                    , SUM(
                        IF(a.AnnualProduction > 0 && a.GardenAreaHa > 0, a.GardenAreaHa, NULL)
                    ) AS total_ha_postline
                    , SUM(
                        IF(a.AnnualProduction > 0 && a.TreeTM > 0, a.AnnualProduction, NULL)
                    ) AS production_tree_postline
                    , SUM(
                        IF(a.AnnualProduction > 0 && a.TreeTM > 0, a.TreeTM, NULL)
                    ) AS total_tm_postline
                FROM
                    ktv_survey_plot_sme a
                    JOIN (  
                            SELECT
                                p.MemberID, p.PlotNr, MAX(p.SurveyNr) AS SurveyNr
                            FROM ktv_survey_plot_sme p 
                            WHERE p.`StatusCode` = 'active'
                            GROUP BY p.MemberID, p.PlotNr
                    ) AS gar_latest ON a.MemberID = gar_latest.MemberID 
						AND a.PlotNr = gar_latest.PlotNr 
						AND a.SurveyNr = gar_latest.SurveyNr
                    JOIN ktv_members m ON m.MemberID = a.MemberID
                    JOIN ktv_village v ON v.VillageID = m.VillageID
                    JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
                    JOIN ktv_district d ON d.DistrictID = sd.DistrictID
                    JOIN ktv_province prov ON prov.ProvinceID = d.ProvinceID 
                    INNER JOIN ktv_access_partner_member acc_pm ON m.MemberID = acc_pm.apmMemberID 
                    INNER JOIN ktv_program_partner pp ON pp.PartnerID = acc_pm.apmPartnerID
                WHERE
                    a.`StatusCode` = 'active'
                    AND m.StatusCode = 'active'
                    AND a.`SurveyNr` > '0'
                    AND pp.StatusCode = 'active'
                    AND pp.IsGenDashboard = 'Yes'
                GROUP BY prov.ProvinceID, d.DistrictID, sd.SubDistrictID, pp.PartnerID, YearCollect
            ) AS tbl_plot_postline ON
                tbl_region_year.VillageID = tbl_plot_postline.VillageID 
                AND tbl_region_year.PartnerID = tbl_plot_postline.PartnerID 
                AND tbl_region_year.YearCollect = tbl_plot_postline.YearCollect
            ORDER BY tbl_region_year.VillageID, tbl_region_year.PartnerID";
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
    
    public function getDisplaySurveys($fprovince,$fdistrict,$ftype){
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
            $sqlHakAkses = " AND a.DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlHakAkses .= " AND a.PartnerID = '{$_SESSION['PartnerID']}' ";
        } else {
            //cek ktv_access_staff
            $sqlHakAkses = " AND a.DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlHakAkses .= " AND a.PartnerID = '1' #Partner Koltiva ";
        }
        //buat SqlHakAksesKontrol (end)

        $sql="SELECT
                IFNULL(SUM(a.farmer_baseline),'-') AS farmer_baseline
                #, IFNULL(SUM(a.farmer_postline),'-') AS farmer_postline (hard coded tidak ditampilkan dl)
                , '-' AS farmer_postline
                , a.DateGenerated
            FROM
                dash_pro_surveys a
            WHERE
                1 = 1
                $sqldWherePropinsi
                $sqldWhereDistrict
                $sqldWhereFtype
                $sqlHakAkses
            ";
        $query = $this->db->query($sql,array());
        $dataDisplay = $query->row_array();

        $sql="SELECT
                IFNULL(SUM(a.plantation_baseline),'-') AS plantation_baseline
                #, IFNULL(SUM(a.plantation_postline),'-') AS plantation_postline (hard coded tidak ditampilkan dl)
                , '-' AS plantation_postline

                , IFNULL(SUM(a.production_baseline) / SUM(a.total_ha_baseline),'-') AS productivity_baseline
                #, IFNULL(SUM(a.production_postline) / SUM(a.total_ha_postline),'-') AS productivity_postline (hard coded tidak ditampilkan dl)
                , '-' AS productivity_postline

                , IFNULL((SUM(a.production_tree_baseline) / SUM(a.total_tm_baseline))*1000,'-') AS productivity_per_tree_baseline
                #, IFNULL((SUM(a.production_tree_postline) / SUM(a.total_tm_postline))*1000,'-') AS productivity_per_tree_postline (hard coded tidak ditampilkan dl)
                , '-' AS productivity_per_tree_postline
            FROM
                dash_pro_surveys_year a
            WHERE
                1 = 1
                $sqldWherePropinsi
                $sqldWhereDistrict
                $sqldWhereFtype
                $sqlHakAkses
        ";
        $query = $this->db->query($sql,array());
        $dataDisplayPerYear = $query->row_array();

        $result['dataDisplay'] = array_merge($dataDisplay, $dataDisplayPerYear);
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

        //buat SqlHakAksesKontrol (begin)
        if($_SESSION['is_admin'] == "1"){
            $sqlcHakAkses = " AND a.PartnerID = '1' #Partner Koltiva ";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            //cek ktv_access_staff
            $sqlcHakAkses = " AND a.DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlcHakAkses .= " AND a.PartnerID = '{$_SESSION['PartnerID']}' ";
        } else {
            //cek ktv_access_staff
            $sqlcHakAkses = " AND a.DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlcHakAkses .= " AND a.PartnerID = '1' #Partner Koltiva ";
        }
        //buat SqlHakAksesKontrol (end)

        //get range tahun
        $sql="SELECT
                a.YearCollect
            FROM
                dash_pro_surveys_year a
            WHERE 1=1
                $sqldWhereFtype
            GROUP BY a.YearCollect
            ORDER BY a.YearCollect";
        $query = $this->db->query($sql);
        $dataRangeTahun = $query->result_array();

        $querySelectYear = "";
        $arrYearCate = array();
        foreach ($dataRangeTahun as $key => $value) {
            $querySelectYear .= "
                                , IFNULL(SUM(IF(`YearCollect`={$value['YearCollect']},plantation_baseline,0)),0) AS base{$key}
                                #, IFNULL(SUM(IF(`YearCollect`={$value['YearCollect']},plantation_postline,0)),0) AS post{$key}
                                , 0 AS post{$key}
                            ";

            $arrYearCate['name'][] = $value['YearCollect'];
            $arrYearCate['categories'] = array('Baseline', 'Post-Line');
        }

        $sql="SELECT
                $sqlcLabel AS label
                $querySelectYear
            FROM
                dash_pro_surveys_year a
                $sqlcJoin
            WHERE
                1 = 1
                $sqlcWhere
                $sqlcHakAkses
                $sqldWhereFtype
            GROUP BY label
            ORDER BY label";
        $query = $this->db->query($sql,array());
        $GardenYearData = $query->result_array();

        $result['dataChart']['GardenYearData'] = $GardenYearData;
        $result['dataChart']['YearNameCate'] = $arrYearCate;

        //bar chart average productivity dan average tree productivity
        $sql="SELECT
                $sqlcLabel AS label
                , IFNULL(SUM(a.production_baseline) / SUM(a.total_ha_baseline),'-') AS productivity_baseline
                #, IFNULL(SUM(a.production_postline) / SUM(a.total_ha_postline),'-') AS productivity_postline
                , '-' AS productivity_postline

                , IFNULL((SUM(a.production_tree_baseline) / SUM(a.total_tm_baseline))*1000,'-') AS productivity_per_tree_baseline
                #, IFNULL((SUM(a.production_tree_postline) / SUM(a.total_tm_postline))*1000,'-') AS productivity_per_tree_postline
                , '-' AS productivity_per_tree_postline
            FROM
                dash_pro_surveys_year a
                $sqlcJoin
            WHERE
                1 = 1
                $sqlcWhere
                $sqlcHakAkses
                $sqldWhereFtype
            GROUP BY label
            ORDER BY label";
        $query = $this->db->query($sql,array());
        $BarChartProductivity = $query->result_array();
        $result['dataChart']['BarChartProductivity'] = $BarChartProductivity;
        //data display chart ================================================== (end)

        return $result;
    }
}
?>