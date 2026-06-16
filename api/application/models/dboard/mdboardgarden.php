<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mdboardgarden extends CI_Model {

    public $sql;

    public function __construct()
    {
        parent::__construct();
    }

    private function truncateTable($namaTabel){
        $sql="DELETE FROM `$namaTabel`";
        $query = $this->db->query($sql);
    }

    public function generateDashGarden(){
        $this->db->trans_begin();

        //truncate dl tabelnya
        $this->truncateTable('dash_det_garden');

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

                $sql="SELECT
                    '{$dataRegion[$i]['ProvinceID']}' AS ProvinceID
                    , '{$dataRegion[$i]['DistrictID']}' AS DistrictID
                    , '{$dataRegion[$i]['SubDistrictID']}' AS SubDistrictID
                    , '{$dataRegion[$i]['VillageID']}' AS `VillageID`
                    , '{$dataPartner[$j]['PartnerID']}' AS PartnerID
                    , IFNULL(COUNT(p.PlotNr),0) AS garden_total
                    , IFNULL(SUM(p.GardenAreaHa),0) AS garden_ha
                    , (COUNT(DISTINCT m.`MemberID`)) AS total_farmer

                    , '0' AS total_farmer_land_owner #Tidak jadi pakai nilai ini
                    , '0' AS total_farmer_land_owner_divider #Tidak jadi pakai nilai ini

                    , IFNULL(SUM(p.PlantationProductivity),0) AS total_productivity
                    , IFNULL(SUM(p.AnnualProduction),0) AS total_production

                    , IFNULL(SUM(
                        IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.GardenAreaHa, 0)
                    ),0) AS calcprod_total_garden_ha
                    , IFNULL(SUM(
                        IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.AnnualProduction, 0)
                    ),0) AS calcprod_total_production

                    , SUM(
                        IF(p.AnnualProduction > 0 && p.TreeTM > 0, p.AnnualProduction, NULL)
                    ) AS calcyieldtree_total_production

                    , SUM(
                        IF(p.AnnualProduction > 0 && p.TreeTM > 0, p.TreeTM, NULL)
                    ) AS calcyieldtree_total_tm

                    , SUM(IF(
                          IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.AnnualProduction, 0)
                          /
                          IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.GardenAreaHa, 0)
                          < 6
                      ,1,0)) AS productivity_below_6
                    , SUM(IF(
                          IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.AnnualProduction, 0)
                          /
                          IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.GardenAreaHa, 0)
                          BETWEEN 6 AND 15
                      ,1,0)) AS productivity_between_6_15
                    , SUM(IF(
                          IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.AnnualProduction, 0)
                          /
                          IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.GardenAreaHa, 0)
                          BETWEEN 16 AND 25
                      ,1,0)) AS productivity_between_16_25
                    , SUM(IF(
                          IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.AnnualProduction, 0)
                          /
                          IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.GardenAreaHa, 0)
                          BETWEEN 26 AND 35
                      ,1,0)) AS productivity_between_26_35
                    , SUM(IF(
                          IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.AnnualProduction, 0)
                          /
                          IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.GardenAreaHa, 0)
                          > 35
                      ,1,0)) AS productivity_above_35


                    , IFNULL(SUM(p.`TreeTBM`) + SUM(p.TreeTM) + SUM(p.TreeTR),0) AS total_tree
                    , IFNULL(SUM(IF(p.TreeTBM > 0 || p.TreeTM > 0 || p.TreeTR > 0, p.GardenAreaHa, 0)),0) AS calctreehectare_gardenha

                    , IFNULL(SUM(YEAR(CURDATE()) - p.FirstPlantingYear),0) AS total_year_planting
                    , IFNULL(SUM(IF(p.FirstPlantingYear IS NOT NULL,1,0)),0) AS calcaveyearplanting_gardentotal

                    , IFNULL(SUM(p.TreeTBM),0) AS total_tree_tbm
                    , IFNULL(SUM(p.TreeTM),0) AS total_tree_tm
                    , IFNULL(SUM(p.TreeTR),0) AS total_tree_tr

                    , IFNULL(SUM(IF(p.`GardenAreaHa` < 2,1,0)),0) AS plantation_less_2ha
                    , IFNULL(SUM(IF(p.`GardenAreaHa` >= 2 && p.`GardenAreaHa` <= 5,1,0)),0) AS plantation_2ha_5ha
                    , IFNULL(SUM(IF(p.`GardenAreaHa` > 5,1,0)),0) AS plantation_more_5ha

                    , IFNULL(SUM(IF(p.`GardenAreaHa` < 1,1,0)),0) AS plantation_det_below_1
                    , IFNULL(SUM(IF(p.`GardenAreaHa` >= 1 && p.`GardenAreaHa` < 2,1,0)),0) AS plantation_det_between_1_2
                    , IFNULL(SUM(IF(p.`GardenAreaHa` >= 2 && p.`GardenAreaHa` < 3.5,1,0)),0) AS plantation_det_between_2_3half
                    , IFNULL(SUM(IF(p.`GardenAreaHa` >= 3.5 && p.`GardenAreaHa` < 5,1,0)),0) AS plantation_det_between_3_5half
                    , IFNULL(SUM(IF(p.`GardenAreaHa` >= 5,1,0)),0) AS plantation_det_above_5

                    , IFNULL(SUM(IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0,
                            IF( (p.AnnualProduction / p.GardenAreaHa) < 15, 1, 0 )
                        ,0)),0) AS plantation_unprofessional
                    , IFNULL(SUM(IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0,
                            IF( (p.AnnualProduction / p.GardenAreaHa) >= 15 && (p.AnnualProduction / p.GardenAreaHa) <= 25, 1, 0 )
                        ,0)),0) AS plantation_progressing
                    , IFNULL(SUM(IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0,
                            IF( (p.AnnualProduction / p.GardenAreaHa) > 25, 1, 0 )
                        ,0)),0) AS plantation_professional

                    , SUM(YEAR(CURDATE()) - p.FirstPlantingYear) AS total_plantation_age
                    , SUM(IF(p.`FirstPlantingYear` IS NOT NULL || p.`FirstPlantingYear` != '' || p.`FirstPlantingYear` != '0',1,0)) AS total_plantation_age_pembagi

                    , SUM(IF(p.LandOwnershipType = '1',1,0)) AS plantation_land_ownership_owned
                    , SUM(IF(p.LandOwnershipType = '3',1,0)) AS plantation_land_ownership_rented
                    , SUM(IF(p.LandOwnershipType = '2',1,0)) AS plantation_land_ownership_psharing
                    , SUM(IF(p.LandOwnershipType = '4',1,0)) AS plantation_land_ownership_other

                    , SUM(IF(p.OwnershipDoc = '1',1,0)) AS plantation_land_document_nodoc
                    , SUM(IF(p.OwnershipDoc = '2',1,0)) AS plantation_land_document_skt
                    , SUM(IF(p.OwnershipDoc = '3',1,0)) AS plantation_land_document_shm
                    , SUM(IF(p.OwnershipDoc = '4',1,0)) AS plantation_land_document_hgu
                    , SUM(IF(p.OwnershipDoc = '5',1,0)) AS plantation_land_document_skgr
                    , SUM(IF(p.OwnershipDoc = '6',1,0)) AS plantation_land_document_other

                    , SUM(IF(p.OwnerOfTheGarden = '1',1,0)) AS plantation_owner_regisfarmer
                    , SUM(IF(p.OwnerOfTheGarden = '2',1,0)) AS plantation_owner_fammember
                    , SUM(IF(p.OwnerOfTheGarden = '3',1,0)) AS plantation_owner_otherpeople
                    , SUM(IF(p.OwnerOfTheGarden = '4',1,0)) AS plantation_owner_donotknow

                    , IFNULL(SUM(IF(p.`AverageAgeTree` != 0.00 AND p.`AverageAgeTree` IS NOT NULL,
                        IF(p.`AverageAgeTree` <= 4,1,0)
                      ,0)),0) AS tree_age_1_3
                    , IFNULL(SUM(IF(p.`AverageAgeTree` > 4 && p.`AverageAgeTree` <= 6,1,0)),0) AS tree_age_4_6
                    , IFNULL(SUM(IF(p.`AverageAgeTree` > 6 && p.`AverageAgeTree` <= 19,1,0)),0) AS tree_age_7_18
                    , IFNULL(SUM(IF(p.`AverageAgeTree` > 19,1,0)),0) AS tree_age_19

                    , IFNULL(SUM(IF(HowObPlantation = 1, 1, 0)), 0) AS obtain_plantation_inheritance
                    , IFNULL(SUM(IF(HowObPlantation = 2, 1, 0)), 0) AS obtain_plantation_purchased
                    , IFNULL(SUM(IF(HowObPlantation = 3, 1, 0)), 0) AS obtain_plantation_convert
                    , IFNULL(SUM(IF(HowObPlantation = 4, 1, 0)), 0) AS obtain_plantation_government
                    , IFNULL(SUM(IF(HowObPlantation = 5, 1, 0)), 0) AS obtain_plantation_others
                    , IFNULL(SUM(IF(TopographyType = 1, 1, 0)), 0) AS topography_flat
                    , IFNULL(SUM(IF(TopographyType = 2, 1, 0)), 0) AS topography_hilly
                    , IFNULL(SUM(IF(TopographyType = 3, 1, 0)), 0) AS topography_mountainous
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
                    INNER JOIN ktv_access_partner_member acc_pm ON m.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$dataPartner[$j]['PartnerID']}'
                WHERE
                    m.`StatusCode` = 'active'
                    AND p.`StatusCode` = 'active'
                    AND m.`VillageID` = '{$dataRegion[$i]['VillageID']}'";
                $query = $this->db->query($sql);
                $dataDash = $query->row_array();

                $sql="INSERT INTO `dash_det_garden` (
                        `ProvinceID`,
                        `DistrictID`,
                        `SubDistrictID`,
                        `VillageID`,
                        `PartnerID`,
                        `garden_total`,
                        `garden_ha`,
                        `total_farmer`,
                        `total_farmer_land_owner`,
                        `total_farmer_land_owner_divider`,
                        `total_productivity`,
                        `total_production`,
                        `calcprod_total_garden_ha`,
                        `calcprod_total_production`,
                        `calcyieldtree_total_production`,
                        `calcyieldtree_total_tm`,
                        `productivity_below_6`,
                        `productivity_between_6_15`,
                        `productivity_between_16_25`,
                        `productivity_between_26_35`,
                        `productivity_above_35`,
                        `total_tree`,
                        `calctreehectare_gardenha`,
                        `total_year_planting`,
                        `calcaveyearplanting_gardentotal`,
                        `total_tree_tbm`,
                        `total_tree_tm`,
                        `total_tree_tr`,
                        `plantation_less_2ha`,
                        `plantation_2ha_5ha`,
                        `plantation_more_5ha`,
                        `plantation_det_below_1`,
                        `plantation_det_between_1_2`,
                        `plantation_det_between_2_3half`,
                        `plantation_det_between_3half_5`,
                        `plantation_det_above_5`,
                        `plantation_unprofessional`,
                        `plantation_progressing`,
                        `plantation_professional`,
                        `total_plantation_age`,
                        `total_plantation_age_pembagi`,
                        `plantation_land_ownership_owned`,
                        `plantation_land_ownership_rented`,
                        `plantation_land_ownership_psharing`,
                        `plantation_land_ownership_other`,
                        `plantation_land_document_nodoc`,
                        `plantation_land_document_skt`,
                        `plantation_land_document_shm`,
                        `plantation_land_document_hgu`,
                        `plantation_land_document_skgr`,
                        `plantation_land_document_other`,
                        `plantation_owner_regisfarmer`,
                        `plantation_owner_fammember`,
                        `plantation_owner_otherpeople`,
                        `plantation_owner_donotknow`,
                        `tree_age_1_3`,
                        `tree_age_4_6`,
                        `tree_age_7_18`,
                        `tree_age_19`,
                        `obtain_plantation_inheritance`,
                        `obtain_plantation_purchased`,
                        `obtain_plantation_convert`,
                        `obtain_plantation_government`,
                        `obtain_plantation_others`,
                        `topography_flat`,
                        `topography_hilly`,
                        `topography_mountainous`,
                        `DateGenerated`
                    )
                    VALUES
                        (
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            NOW()
                        )
                    ON DUPLICATE KEY UPDATE
                        `garden_total` = VALUES(`garden_total`),
                        `garden_ha` = VALUES(`garden_ha`),
                        `total_farmer` = VALUES(`total_farmer`),
                        `total_farmer_land_owner` = VALUES(`total_farmer_land_owner`),
                        `total_farmer_land_owner_divider` = VALUES(`total_farmer_land_owner_divider`),
                        `total_productivity` = VALUES(`total_productivity`),
                        `total_production` = VALUES(`total_production`),
                        `calcprod_total_garden_ha` = VALUES(`calcprod_total_garden_ha`),
                        `calcprod_total_production` = VALUES(`calcprod_total_production`),
                        `calcyieldtree_total_production` = VALUES(`calcyieldtree_total_production`),
                        `calcyieldtree_total_tm` = VALUES(`calcyieldtree_total_tm`),
                        `productivity_below_6` = VALUES(`productivity_below_6`),
                        `productivity_between_6_15` = VALUES(`productivity_between_6_15`),
                        `productivity_between_16_25` = VALUES(`productivity_between_16_25`),
                        `productivity_between_26_35` = VALUES(`productivity_between_26_35`),
                        `productivity_above_35` = VALUES(`productivity_above_35`),
                        `total_tree` = VALUES(`total_tree`),
                        `calctreehectare_gardenha` = VALUES(`calctreehectare_gardenha`),
                        `total_year_planting` = VALUES(`total_year_planting`),
                        `calcaveyearplanting_gardentotal` = VALUES(`calcaveyearplanting_gardentotal`),
                        `total_tree_tbm` = VALUES(`total_tree_tbm`),
                        `total_tree_tm` = VALUES(`total_tree_tm`),
                        `total_tree_tr` = VALUES(`total_tree_tr`),
                        `plantation_less_2ha` = VALUES(`plantation_less_2ha`),
                        `plantation_2ha_5ha` = VALUES(`plantation_2ha_5ha`),
                        `plantation_more_5ha` = VALUES(`plantation_more_5ha`),
                        `plantation_det_below_1` = VALUES(`plantation_det_below_1`),
                        `plantation_det_between_1_2` = VALUES(`plantation_det_between_1_2`),
                        `plantation_det_between_2_3half` = VALUES(`plantation_det_between_2_3half`),
                        `plantation_det_between_3half_5` = VALUES(`plantation_det_between_3half_5`),
                        `plantation_det_above_5` = VALUES(`plantation_det_above_5`),
                        `plantation_unprofessional` = VALUES(`plantation_unprofessional`),
                        `plantation_progressing` = VALUES(`plantation_progressing`),
                        `plantation_professional` = VALUES(`plantation_professional`),
                        `total_plantation_age` = VALUES(`total_plantation_age`),
                        `total_plantation_age_pembagi` = VALUES(`total_plantation_age_pembagi`),
                        `plantation_land_ownership_owned` = VALUES(`plantation_land_ownership_owned`),
                        `plantation_land_ownership_rented` = VALUES(`plantation_land_ownership_rented`),
                        `plantation_land_ownership_psharing` = VALUES(`plantation_land_ownership_psharing`),
                        `plantation_land_ownership_other` = VALUES(`plantation_land_ownership_other`),
                        `plantation_land_document_nodoc` = VALUES(`plantation_land_document_nodoc`),
                        `plantation_land_document_skt` = VALUES(`plantation_land_document_skt`),
                        `plantation_land_document_shm` = VALUES(`plantation_land_document_shm`),
                        `plantation_land_document_hgu` = VALUES(`plantation_land_document_hgu`),
                        `plantation_land_document_skgr` = VALUES(`plantation_land_document_skgr`),
                        `plantation_land_document_other` = VALUES(`plantation_land_document_other`),
                        `plantation_owner_regisfarmer` = VALUES(`plantation_owner_regisfarmer`),
                        `plantation_owner_fammember` = VALUES(`plantation_owner_fammember`),
                        `plantation_owner_otherpeople` = VALUES(`plantation_owner_otherpeople`),
                        `plantation_owner_donotknow` = VALUES(`plantation_owner_donotknow`),
                        `tree_age_1_3` = VALUES(`tree_age_1_3`),
                        `tree_age_4_6` = VALUES(`tree_age_4_6`),
                        `tree_age_7_18` = VALUES(`tree_age_7_18`),
                        `tree_age_19` = VALUES(`tree_age_19`),
                        `obtain_plantation_inheritance` = VALUES(`obtain_plantation_inheritance`),
                        `obtain_plantation_purchased` = VALUES(`obtain_plantation_purchased`),
                        `obtain_plantation_convert` = VALUES(`obtain_plantation_convert`),
                        `obtain_plantation_government` = VALUES(`obtain_plantation_government`),
                        `obtain_plantation_others` = VALUES(`obtain_plantation_others`),
                        `topography_flat` = VALUES(`topography_flat`),
                        `topography_hilly` = VALUES(`topography_hilly`),
                        `topography_mountainous` = VALUES(`topography_mountainous`),
                        `DateGenerated` = NOW()
                    ";
                $p = array(
                    //insert
                    $dataDash['ProvinceID'],
                    $dataDash['DistrictID'],
                    $dataDash['SubDistrictID'],
                    $dataDash['VillageID'],
                    $dataDash['PartnerID'],
                    $dataDash['garden_total'],
                    $dataDash['garden_ha'],
                    $dataDash['total_farmer'],
                    $dataDash['total_farmer_land_owner'],
                    $dataDash['total_farmer_land_owner_divider'],
                    $dataDash['total_productivity'],
                    $dataDash['total_production'],
                    $dataDash['calcprod_total_garden_ha'],
                    $dataDash['calcprod_total_production'],
                    $dataDash['calcyieldtree_total_production'],
                    $dataDash['calcyieldtree_total_tm'],
                    $dataDash['productivity_below_6'],
                    $dataDash['productivity_between_6_15'],
                    $dataDash['productivity_between_16_25'],
                    $dataDash['productivity_between_26_35'],
                    $dataDash['productivity_above_35'],
                    $dataDash['total_tree'],
                    $dataDash['calctreehectare_gardenha'],
                    $dataDash['total_year_planting'],
                    $dataDash['calcaveyearplanting_gardentotal'],
                    $dataDash['total_tree_tbm'],
                    $dataDash['total_tree_tm'],
                    $dataDash['total_tree_tr'],
                    $dataDash['plantation_less_2ha'],
                    $dataDash['plantation_2ha_5ha'],
                    $dataDash['plantation_more_5ha'],
                    $dataDash['plantation_det_below_1'],
                    $dataDash['plantation_det_between_1_2'],
                    $dataDash['plantation_det_between_2_3half'],
                    $dataDash['plantation_det_between_3_5half'],
                    $dataDash['plantation_det_above_5'],
                    $dataDash['plantation_unprofessional'],
                    $dataDash['plantation_progressing'],
                    $dataDash['plantation_professional'],
                    $dataDash['total_plantation_age'],
                    $dataDash['total_plantation_age_pembagi'],
                    $dataDash['plantation_land_ownership_owned'],
                    $dataDash['plantation_land_ownership_rented'],
                    $dataDash['plantation_land_ownership_psharing'],
                    $dataDash['plantation_land_ownership_other'],
                    $dataDash['plantation_land_document_nodoc'],
                    $dataDash['plantation_land_document_skt'],
                    $dataDash['plantation_land_document_shm'],
                    $dataDash['plantation_land_document_hgu'],
                    $dataDash['plantation_land_document_skgr'],
                    $dataDash['plantation_land_document_other'],
                    $dataDash['plantation_owner_regisfarmer'],
                    $dataDash['plantation_owner_fammember'],
                    $dataDash['plantation_owner_otherpeople'],
                    $dataDash['plantation_owner_donotknow'],
                    $dataDash['tree_age_1_3'],
                    $dataDash['tree_age_4_6'],
                    $dataDash['tree_age_7_18'],
                    $dataDash['tree_age_19'],
                    $dataDash['obtain_plantation_inheritance'],
                    $dataDash['obtain_plantation_purchased'],
                    $dataDash['obtain_plantation_convert'],
                    $dataDash['obtain_plantation_government'],
                    $dataDash['obtain_plantation_others'],
                    $dataDash['topography_flat'],
                    $dataDash['topography_hilly'],
                    $dataDash['topography_mountainous'],
                    //update
                );
                $query = $this->db->query($sql,$p);
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

    public function generateDashGardenOptimize(){
        $this->db->trans_begin();

        //truncate dl tabelnya
        $this->truncateTable('dash_det_garden');

        //insert data farmer
        $sql="INSERT INTO dash_det_garden
            SELECT
                    prov.ProvinceID AS ProvinceID
                    , d.DistrictID AS DistrictID
                    , sd.SubDistrictID AS SubDistrictID
                    , pp.PartnerID AS PartnerID
                    , 'Smallholder' AS `Type`
                    , IFNULL(COUNT(p.PlotNr),0) AS garden_total
                    , IFNULL(SUM(p.GardenAreaHa),0) AS garden_ha
                    , (COUNT(DISTINCT m.`MemberID`)) AS total_farmer

                    , '0' AS total_farmer_land_owner #Tidak jadi pakai nilai ini
                    , '0' AS total_farmer_land_owner_divider #Tidak jadi pakai nilai ini

                    , IFNULL(SUM(p.PlantationProductivity),0) AS total_productivity
                    , IFNULL(SUM(p.AnnualProduction),0) AS total_production

                    , IFNULL(SUM(
                        IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.GardenAreaHa, 0)
                    ),0) AS calcprod_total_garden_ha
                    , IFNULL(SUM(
                        IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.AnnualProduction, 0)
                    ),0) AS calcprod_total_production

                    , SUM(
                        IF(p.AnnualProduction > 0 && p.TreeTM > 0, p.AnnualProduction, NULL)
                    ) AS calcyieldtree_total_production

                    , SUM(
                        IF(p.AnnualProduction > 0 && p.TreeTM > 0, p.TreeTM, NULL)
                    ) AS calcyieldtree_total_tm

                    , SUM(IF(
                          IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.AnnualProduction, 0)
                          /
                          NULLIF(IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.GardenAreaHa, 0), 0)
                          < 6
                      ,1,0)) AS productivity_below_6
                    , SUM(IF(
                          IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.AnnualProduction, 0)
                          /
                          NULLIF(IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.GardenAreaHa, 0), 0)
                          BETWEEN 6 AND 15
                      ,1,0)) AS productivity_between_6_15
                    , SUM(IF(
                          IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.AnnualProduction, 0)
                          /
                          NULLIF(IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.GardenAreaHa, 0), 0)
                          BETWEEN 16 AND 25
                      ,1,0)) AS productivity_between_16_25
                    , SUM(IF(
                          IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.AnnualProduction, 0)
                          /
                          NULLIF(IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.GardenAreaHa, 0), 0)
                          BETWEEN 26 AND 35
                      ,1,0)) AS productivity_between_26_35
                    , SUM(IF(
                          IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.AnnualProduction, 0)
                          /
                          NULLIF(IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.GardenAreaHa, 0), 0)
                          > 35
                      ,1,0)) AS productivity_above_35


                    , IFNULL(COALESCE(SUM(p.`TreeTBM`), 0) + COALESCE(SUM(p.TreeTM), 0) + COALESCE(SUM(p.TreeTR), 0),0) AS total_tree
                    , IFNULL(SUM(IF(p.TreeTBM > 0 || p.TreeTM > 0 || p.TreeTR > 0, p.GardenAreaHa, 0)),0) AS calctreehectare_gardenha

                    , IFNULL(SUM(YEAR(CURDATE()) - p.FirstPlantingYear),0) AS total_year_planting
                    , IFNULL(SUM(IF(p.FirstPlantingYear IS NOT NULL,1,0)),0) AS calcaveyearplanting_gardentotal

                    , IFNULL(SUM(p.TreeTBM),0) AS total_tree_tbm
                    , IFNULL(SUM(p.TreeTM),0) AS total_tree_tm
                    , IFNULL(SUM(p.TreeTR),0) AS total_tree_tr

                    , IFNULL(SUM(IF(p.`GardenAreaHa` < 2,1,0)),0) AS plantation_less_2ha
                    , IFNULL(SUM(IF(p.`GardenAreaHa` >= 2 && p.`GardenAreaHa` <= 5,1,0)),0) AS plantation_2ha_5ha
                    , IFNULL(SUM(IF(p.`GardenAreaHa` > 5,1,0)),0) AS plantation_more_5ha

                    , IFNULL(SUM(IF(p.`GardenAreaHa` < 1,1,0)),0) AS plantation_det_below_1
                    , IFNULL(SUM(IF(p.`GardenAreaHa` >= 1 && p.`GardenAreaHa` < 2,1,0)),0) AS plantation_det_between_1_2
                    , IFNULL(SUM(IF(p.`GardenAreaHa` >= 2 && p.`GardenAreaHa` < 3.5,1,0)),0) AS plantation_det_between_2_3half
                    , IFNULL(SUM(IF(p.`GardenAreaHa` >= 3.5 && p.`GardenAreaHa` < 5,1,0)),0) AS plantation_det_between_3_5half
                    , IFNULL(SUM(IF(p.`GardenAreaHa` >= 5,1,0)),0) AS plantation_det_above_5

                    , IFNULL(SUM(IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0,
                            IF( (p.AnnualProduction / NULLIF(p.GardenAreaHa, 0)) < 15, 1, 0 )
                        ,0)),0) AS plantation_unprofessional
                    , IFNULL(SUM(IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0,
                            IF( (p.AnnualProduction / NULLIF(p.GardenAreaHa, 0)) >= 15 && (p.AnnualProduction / NULLIF(p.GardenAreaHa, 0)) <= 25, 1, 0 )
                        ,0)),0) AS plantation_progressing
                    , IFNULL(SUM(IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0,
                            IF( (p.AnnualProduction / NULLIF(p.GardenAreaHa, 0)) > 25, 1, 0 )
                        ,0)),0) AS plantation_professional

                    , SUM(YEAR(CURDATE()) - p.FirstPlantingYear) AS total_plantation_age
                    , SUM(IF(p.`FirstPlantingYear` IS NOT NULL || p.`FirstPlantingYear` != '' || p.`FirstPlantingYear` != '0',1,0)) AS total_plantation_age_pembagi

                    , SUM(IF(p.LandOwnershipType = '1',1,0)) AS plantation_land_ownership_owned
                    , SUM(IF(p.LandOwnershipType = '3',1,0)) AS plantation_land_ownership_rented
                    , SUM(IF(p.LandOwnershipType = '2',1,0)) AS plantation_land_ownership_psharing
                    , SUM(IF(p.LandOwnershipType = '4',1,0)) AS plantation_land_ownership_other

                    , SUM(IF(p.OwnershipDoc = '1',1,0)) AS plantation_land_document_nodoc
                    , SUM(IF(p.OwnershipDoc = '2',1,0)) AS plantation_land_document_skt
                    , SUM(IF(p.OwnershipDoc = '3',1,0)) AS plantation_land_document_shm
                    , SUM(IF(p.OwnershipDoc = '4',1,0)) AS plantation_land_document_hgu
                    , SUM(IF(p.OwnershipDoc = '5',1,0)) AS plantation_land_document_skgr
                    , SUM(IF(p.OwnershipDoc = '6',1,0)) AS plantation_land_document_other

                    , SUM(IF(p.OwnerOfTheGarden = '1',1,0)) AS plantation_owner_regisfarmer
                    , SUM(IF(p.OwnerOfTheGarden = '2',1,0)) AS plantation_owner_fammember
                    , SUM(IF(p.OwnerOfTheGarden = '3',1,0)) AS plantation_owner_otherpeople
                    , SUM(IF(p.OwnerOfTheGarden = '4',1,0)) AS plantation_owner_donotknow

                    , IFNULL(SUM(IF(p.`AverageAgeTree` != 0.00 AND p.`AverageAgeTree` IS NOT NULL,
                        IF(p.`AverageAgeTree` <= 4,1,0)
                      ,0)),0) AS tree_age_1_3
                    , IFNULL(SUM(IF(p.`AverageAgeTree` > 4 && p.`AverageAgeTree` <= 6,1,0)),0) AS tree_age_4_6
                    , IFNULL(SUM(IF(p.`AverageAgeTree` > 6 && p.`AverageAgeTree` <= 19,1,0)),0) AS tree_age_7_18
                    , IFNULL(SUM(IF(p.`AverageAgeTree` > 19,1,0)),0) AS tree_age_19

                    , IFNULL(SUM(IF(HowObPlantation = 1, 1, 0)), 0) AS obtain_plantation_inheritance
                    , IFNULL(SUM(IF(HowObPlantation = 2, 1, 0)), 0) AS obtain_plantation_purchased
                    , IFNULL(SUM(IF(HowObPlantation = 3, 1, 0)), 0) AS obtain_plantation_convert
                    , IFNULL(SUM(IF(HowObPlantation = 4, 1, 0)), 0) AS obtain_plantation_government
                    , IFNULL(SUM(IF(HowObPlantation = 5, 1, 0)), 0) AS obtain_plantation_others
                    , IFNULL(SUM(IF(TopographyType = 1, 1, 0)), 0) AS topography_flat
                    , IFNULL(SUM(IF(TopographyType = 2, 1, 0)), 0) AS topography_hilly
                    , IFNULL(SUM(IF(TopographyType = 3, 1, 0)), 0) AS topography_mountainous
                    , NOW() AS DateGenerated
                FROM
                    ktv_survey_plot p
                    JOIN (SELECT
                            p.MemberID, p.PlotNr, MAX(p.SurveyNr) AS SurveyNr
                        FROM ktv_survey_plot p 
                        WHERE p.`StatusCode` = 'active'
                        GROUP BY p.MemberID, p.PlotNr) z
                            ON p.MemberID = z.MemberID AND p.PlotNr = z.PlotNr AND p.SurveyNr = z.SurveyNr
                    JOIN ktv_members m ON m.MemberID = p.MemberID
                    JOIN ktv_member_role r ON m.MemberID = r.MemberID AND r.MRoleID = 1 #ROLE PETANI
                    JOIN ktv_village v ON v.VillageID = m.VillageID
                    JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
                    JOIN ktv_district d ON d.DistrictID = sd.DistrictID
                    JOIN ktv_province prov ON prov.ProvinceID = d.ProvinceID
                    INNER JOIN ktv_access_partner_member acc_pm ON m.MemberID = acc_pm.apmMemberID 
                    INNER JOIN ktv_program_partner pp ON pp.PartnerID = acc_pm.apmPartnerID
                WHERE
                    m.`StatusCode` = 'active'
                    AND p.`StatusCode` = 'active'
                    AND m.`VillageID` IS NOT NULL
                    AND m.VillageID != ''
                    AND m.VillageID != '0'
                    AND pp.`StatusCode` = 'active'
                    AND pp.IsGenDashboard = 'Yes'
                GROUP BY
                     prov.ProvinceID
                   , d.DistrictID
                   , sd.SubDistrictID
                   , pp.PartnerID";
        $query = $this->db->query($sql);

        //insert data sme
        $sql="INSERT INTO dash_det_garden
            SELECT
                    prov.ProvinceID AS ProvinceID
                    , d.DistrictID AS DistrictID
                    , sd.SubDistrictID AS SubDistrictID
                    , pp.PartnerID AS PartnerID
                    , 'SME' AS `Type`
                    , IFNULL(COUNT(p.PlotNr),0) AS garden_total
                    , IFNULL(SUM(p.GardenAreaHa),0) AS garden_ha
                    , (COUNT(DISTINCT m.`MemberID`)) AS total_farmer

                    , '0' AS total_farmer_land_owner #Tidak jadi pakai nilai ini
                    , '0' AS total_farmer_land_owner_divider #Tidak jadi pakai nilai ini

                    , IFNULL(SUM(p.PlantationProductivity),0) AS total_productivity
                    , IFNULL(SUM(p.AnnualProduction),0) AS total_production

                    , IFNULL(SUM(
                        IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.GardenAreaHa, 0)
                    ),0) AS calcprod_total_garden_ha
                    , IFNULL(SUM(
                        IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.AnnualProduction, 0)
                    ),0) AS calcprod_total_production

                    , SUM(
                        IF(p.AnnualProduction > 0 && p.TreeTM > 0, p.AnnualProduction, NULL)
                    ) AS calcyieldtree_total_production

                    , SUM(
                        IF(p.AnnualProduction > 0 && p.TreeTM > 0, p.TreeTM, NULL)
                    ) AS calcyieldtree_total_tm

                    , SUM(IF(
                          IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.AnnualProduction, 0)
                          /
                          NULLIF(IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.GardenAreaHa, 0), 0)
                          < 6
                      ,1,0)) AS productivity_below_6
                    , SUM(IF(
                          IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.AnnualProduction, 0)
                          /
                          NULLIF(IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.GardenAreaHa, 0), 0)
                          BETWEEN 6 AND 15
                      ,1,0)) AS productivity_between_6_15
                    , SUM(IF(
                          IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.AnnualProduction, 0)
                          /
                          NULLIF(IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.GardenAreaHa, 0), 0)
                          BETWEEN 16 AND 25
                      ,1,0)) AS productivity_between_16_25
                    , SUM(IF(
                          IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.AnnualProduction, 0)
                          /
                          NULLIF(IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.GardenAreaHa, 0), 0)
                          BETWEEN 26 AND 35
                      ,1,0)) AS productivity_between_26_35
                    , SUM(IF(
                          IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.AnnualProduction, 0)
                          /
                          NULLIF(IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0, p.GardenAreaHa, 0), 0)
                          > 35
                      ,1,0)) AS productivity_above_35


                    , IFNULL(COALESCE(SUM(p.`TreeTBM`), 0) + COALESCE(SUM(p.TreeTM), 0) + COALESCE(SUM(p.TreeTR), 0),0) AS total_tree
                    , IFNULL(SUM(IF(p.TreeTBM > 0 || p.TreeTM > 0 || p.TreeTR > 0, p.GardenAreaHa, 0)),0) AS calctreehectare_gardenha

                    , IFNULL(SUM(YEAR(CURDATE()) - p.FirstPlantingYear),0) AS total_year_planting
                    , IFNULL(SUM(IF(p.FirstPlantingYear IS NOT NULL,1,0)),0) AS calcaveyearplanting_gardentotal

                    , IFNULL(SUM(p.TreeTBM),0) AS total_tree_tbm
                    , IFNULL(SUM(p.TreeTM),0) AS total_tree_tm
                    , IFNULL(SUM(p.TreeTR),0) AS total_tree_tr

                    , IFNULL(SUM(IF(p.`GardenAreaHa` < 2,1,0)),0) AS plantation_less_2ha
                    , IFNULL(SUM(IF(p.`GardenAreaHa` >= 2 && p.`GardenAreaHa` <= 5,1,0)),0) AS plantation_2ha_5ha
                    , IFNULL(SUM(IF(p.`GardenAreaHa` > 5,1,0)),0) AS plantation_more_5ha

                    , IFNULL(SUM(IF(p.`GardenAreaHa` < 1,1,0)),0) AS plantation_det_below_1
                    , IFNULL(SUM(IF(p.`GardenAreaHa` >= 1 && p.`GardenAreaHa` < 2,1,0)),0) AS plantation_det_between_1_2
                    , IFNULL(SUM(IF(p.`GardenAreaHa` >= 2 && p.`GardenAreaHa` < 3.5,1,0)),0) AS plantation_det_between_2_3half
                    , IFNULL(SUM(IF(p.`GardenAreaHa` >= 3.5 && p.`GardenAreaHa` < 5,1,0)),0) AS plantation_det_between_3_5half
                    , IFNULL(SUM(IF(p.`GardenAreaHa` >= 5,1,0)),0) AS plantation_det_above_5

                    , IFNULL(SUM(IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0,
                            IF( (p.AnnualProduction / NULLIF(p.GardenAreaHa, 0)) < 15, 1, 0 )
                        ,0)),0) AS plantation_unprofessional
                    , IFNULL(SUM(IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0,
                            IF( (p.AnnualProduction / NULLIF(p.GardenAreaHa, 0)) >= 15 && (p.AnnualProduction / NULLIF(p.GardenAreaHa, 0)) <= 25, 1, 0 )
                        ,0)),0) AS plantation_progressing
                    , IFNULL(SUM(IF(p.AnnualProduction > 0 && p.GardenAreaHa > 0,
                            IF( (p.AnnualProduction / NULLIF(p.GardenAreaHa, 0)) > 25, 1, 0 )
                        ,0)),0) AS plantation_professional

                    , SUM(YEAR(CURDATE()) - p.FirstPlantingYear) AS total_plantation_age
                    , SUM(IF(p.`FirstPlantingYear` IS NOT NULL || p.`FirstPlantingYear` != '' || p.`FirstPlantingYear` != '0',1,0)) AS total_plantation_age_pembagi

                    , SUM(IF(p.LandOwnershipType = '1',1,0)) AS plantation_land_ownership_owned
                    , SUM(IF(p.LandOwnershipType = '3',1,0)) AS plantation_land_ownership_rented
                    , SUM(IF(p.LandOwnershipType = '2',1,0)) AS plantation_land_ownership_psharing
                    , SUM(IF(p.LandOwnershipType = '4',1,0)) AS plantation_land_ownership_other

                    , SUM(IF(p.OwnershipDoc = '1',1,0)) AS plantation_land_document_nodoc
                    , SUM(IF(p.OwnershipDoc = '2',1,0)) AS plantation_land_document_skt
                    , SUM(IF(p.OwnershipDoc = '3',1,0)) AS plantation_land_document_shm
                    , SUM(IF(p.OwnershipDoc = '4',1,0)) AS plantation_land_document_hgu
                    , SUM(IF(p.OwnershipDoc = '5',1,0)) AS plantation_land_document_skgr
                    , SUM(IF(p.OwnershipDoc = '6',1,0)) AS plantation_land_document_other

                    , SUM(IF(p.OwnerOfTheGarden = '1',1,0)) AS plantation_owner_regisfarmer
                    , SUM(IF(p.OwnerOfTheGarden = '2',1,0)) AS plantation_owner_fammember
                    , SUM(IF(p.OwnerOfTheGarden = '3',1,0)) AS plantation_owner_otherpeople
                    , SUM(IF(p.OwnerOfTheGarden = '4',1,0)) AS plantation_owner_donotknow

                    , IFNULL(SUM(IF(p.`AverageAgeTree` != 0.00 AND p.`AverageAgeTree` IS NOT NULL,
                        IF(p.`AverageAgeTree` <= 4,1,0)
                      ,0)),0) AS tree_age_1_3
                    , IFNULL(SUM(IF(p.`AverageAgeTree` > 4 && p.`AverageAgeTree` <= 6,1,0)),0) AS tree_age_4_6
                    , IFNULL(SUM(IF(p.`AverageAgeTree` > 6 && p.`AverageAgeTree` <= 19,1,0)),0) AS tree_age_7_18
                    , IFNULL(SUM(IF(p.`AverageAgeTree` > 19,1,0)),0) AS tree_age_19

                    , IFNULL(SUM(IF(HowObPlantation = 1, 1, 0)), 0) AS obtain_plantation_inheritance
                    , IFNULL(SUM(IF(HowObPlantation = 2, 1, 0)), 0) AS obtain_plantation_purchased
                    , IFNULL(SUM(IF(HowObPlantation = 3, 1, 0)), 0) AS obtain_plantation_convert
                    , IFNULL(SUM(IF(HowObPlantation = 4, 1, 0)), 0) AS obtain_plantation_government
                    , IFNULL(SUM(IF(HowObPlantation = 5, 1, 0)), 0) AS obtain_plantation_others
                    , IFNULL(SUM(IF(TopographyType = 1, 1, 0)), 0) AS topography_flat
                    , IFNULL(SUM(IF(TopographyType = 2, 1, 0)), 0) AS topography_hilly
                    , IFNULL(SUM(IF(TopographyType = 3, 1, 0)), 0) AS topography_mountainous
                    , NOW() AS DateGenerated
                FROM
                    ktv_survey_plot_sme p
                    JOIN (SELECT
                            p.MemberID, p.PlotNr, MAX(p.SurveyNr) AS SurveyNr
                        FROM ktv_survey_plot_sme p 
                        WHERE p.`StatusCode` = 'active'
                        GROUP BY p.MemberID, p.PlotNr) z
                            ON p.MemberID = z.MemberID AND p.PlotNr = z.PlotNr AND p.SurveyNr = z.SurveyNr
                    JOIN ktv_members m ON m.MemberID = p.MemberID
                    JOIN ktv_village v ON v.VillageID = m.VillageID
                    JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
                    JOIN ktv_district d ON d.DistrictID = sd.DistrictID
                    JOIN ktv_province prov ON prov.ProvinceID = d.ProvinceID
                    INNER JOIN ktv_access_partner_member acc_pm ON m.MemberID = acc_pm.apmMemberID 
                    INNER JOIN ktv_program_partner pp ON pp.PartnerID = acc_pm.apmPartnerID
                WHERE
                    m.`StatusCode` = 'active'
                    AND p.`StatusCode` = 'active'
                    AND m.`VillageID` IS NOT NULL
                    AND m.VillageID != ''
                    AND m.VillageID != '0'
                    AND pp.`StatusCode` = 'active'
                    AND pp.IsGenDashboard = 'Yes'
                GROUP BY prov.ProvinceID, d.DistrictID, sd.SubDistrictID, pp.PartnerID";
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

    public function getDisplayGarden($fprovince,$fdistrict,$ftype){
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
            $sqlHakAkses = " AND DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlHakAkses .= " AND a.PartnerID = '{$_SESSION['PartnerID']}' ";
        } else {
            //cek ktv_access_staff
            $sqlHakAkses = " AND DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlHakAkses .= " AND a.PartnerID = '1' #Partner Koltiva ";
        }
        //buat SqlHakAksesKontrol (end)

        $sql="SELECT
                SUM(a.garden_total) AS garden_total
                , SUM(a.garden_ha) AS garden_ha
                , SUM(a.total_farmer) AS total_farmer
                , IFNULL(SUM(a.plantation_land_document_nodoc),0) AS plantation_land_document_nodoc
                , IFNULL(SUM(a.plantation_land_document_skt),0) AS plantation_land_document_skt
                , IFNULL(SUM(a.plantation_land_document_shm),0) AS plantation_land_document_shm
                , IFNULL(SUM(a.plantation_land_document_hgu),0) AS plantation_land_document_hgu
                , IFNULL(SUM(a.plantation_land_document_skgr),0) AS plantation_land_document_skgr
                , IFNULL(SUM(a.plantation_land_document_other),0) AS plantation_land_document_other
                , SUM(a.garden_ha) / SUM(a.garden_total) AS ave_garden_ha
                , SUM(a.garden_total) / SUM(a.total_farmer) AS ave_count_plantation_by_farmer
                , SUM(a.calcprod_total_production) AS calcprod_total_production
                , SUM(a.calcprod_total_production) / SUM(a.calcprod_total_garden_ha) AS ave_plantation_productivity
                , SUM(a.total_production) AS total_production
                , SUM(a.total_productivity) AS total_productivity
                , SUM(a.total_tree) AS total_tree
                , SUM(a.total_tree) / SUM(a.calctreehectare_gardenha) AS tree_per_hectare
                , SUM(a.total_year_planting) / SUM(a.calcaveyearplanting_gardentotal) AS ave_year_planting
                , (SUM(a.calcyieldtree_total_production) / SUM(a.calcyieldtree_total_tm)) * 1000 AS ave_tree_productivity
                , IFNULL(SUM(a.obtain_plantation_inheritance),0) AS obtain_plantation_inheritance
                , IFNULL(SUM(a.obtain_plantation_purchased),0) AS obtain_plantation_purchased
                , IFNULL(SUM(a.obtain_plantation_convert),0) AS obtain_plantation_convert
                , IFNULL(SUM(a.obtain_plantation_government),0) AS obtain_plantation_government
                , IFNULL(SUM(a.obtain_plantation_others),0) AS obtain_plantation_others
                , IFNULL(SUM(a.topography_flat),0) AS topography_flat
                , IFNULL(SUM(a.topography_hilly),0) AS topography_hilly
                , IFNULL(SUM(a.topography_mountainous),0) AS topography_mountainous
                , DateGenerated
            FROM
                dash_det_garden a
            WHERE
                1 = 1
                $sqldWherePropinsi
                $sqldWhereDistrict
                $sqldWhereFtype
                $sqlHakAkses
            ";
        $query = $this->db->query($sql,array());
        $result['dataDisplay'] = $query->row_array();
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        //data display langsung ================================================== (end)

        //data group by wilayah (untuk chart) ============================================= (begin)

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

        $sql="SELECT
                $sqlcLabel AS label
                , IFNULL(SUM(a.garden_total),0) AS garden_total
                , IFNULL(SUM(a.garden_ha),0) AS garden_ha
                , IFNULL(SUM(a.garden_ha) / SUM(a.garden_total),0) AS ave_garden_ha
                , IFNULL(SUM(a.garden_total) / SUM(a.total_farmer),0) AS ave_count_plantation_by_farmer
                , IFNULL(SUM(a.calcprod_total_production) / SUM(a.calcprod_total_garden_ha),0) AS ave_plantation_productivity
                , IFNULL((SUM(a.calcyieldtree_total_production) / SUM(a.calcyieldtree_total_tm)) * 1000,0) AS ave_tree_productivity
                , SUM(IFNULL(a.productivity_below_6,0)) AS productivity_below_6
                , SUM(IFNULL(a.productivity_between_6_15,0)) AS productivity_between_6_15
                , SUM(IFNULL(a.productivity_between_16_25,0)) AS productivity_between_16_25
                , SUM(IFNULL(a.productivity_between_26_35,0)) AS productivity_between_26_35
                , SUM(IFNULL(a.productivity_above_35,0)) AS productivity_above_35
                , IFNULL(SUM(a.total_production),0) AS total_production
                , IFNULL(SUM(a.total_productivity),0) AS total_productivity
                , IFNULL(SUM(a.total_tree_tbm),0) AS total_tree_tbm
                , IFNULL(SUM(a.total_tree_tm),0) AS total_tree_tm
                , IFNULL(SUM(a.total_tree_tr),0) AS total_tree_tr
                , IFNULL(SUM(a.total_tree) / SUM(a.calctreehectare_gardenha),0) AS tree_per_hectare
                , IFNULL(SUM(a.plantation_less_2ha),0) AS plantation_less_2ha
                , IFNULL(SUM(a.plantation_2ha_5ha),0) AS plantation_2ha_5ha
                , IFNULL(SUM(a.plantation_more_5ha),0) AS plantation_more_5ha
                , IFNULL(SUM(a.plantation_det_below_1),0) AS plantation_det_below_1
                , IFNULL(SUM(a.plantation_det_between_1_2),0) AS plantation_det_between_1_2
                , IFNULL(SUM(a.plantation_det_between_2_3half),0) AS plantation_det_between_2_3half
                , IFNULL(SUM(a.plantation_det_between_3half_5),0) AS plantation_det_between_3half_5
                , IFNULL(SUM(a.plantation_det_above_5),0) AS plantation_det_above_5
                , IFNULL(SUM(a.plantation_unprofessional),0) AS plantation_unprofessional
                , IFNULL(SUM(a.plantation_progressing),0) AS plantation_progressing
                , IFNULL(SUM(a.plantation_professional),0) AS plantation_professional
                , IFNULL(SUM(a.total_plantation_age) / SUM(a.total_plantation_age_pembagi),0) AS ave_plantation_age
                , IFNULL(SUM(a.plantation_land_ownership_owned),0) AS plantation_land_ownership_owned
                , IFNULL(SUM(a.plantation_land_ownership_rented),0) AS plantation_land_ownership_rented
                , IFNULL(SUM(a.plantation_land_ownership_psharing),0) AS plantation_land_ownership_psharing
                , IFNULL(SUM(a.plantation_land_ownership_other),0) AS plantation_land_ownership_other
                , IFNULL(SUM(a.plantation_land_document_nodoc),0) AS plantation_land_document_nodoc
                , IFNULL(SUM(a.plantation_land_document_skt),0) AS plantation_land_document_skt
                , IFNULL(SUM(a.plantation_land_document_shm),0) AS plantation_land_document_shm
                , IFNULL(SUM(a.plantation_land_document_hgu),0) AS plantation_land_document_hgu
                , IFNULL(SUM(a.plantation_land_document_skgr),0) AS plantation_land_document_skgr
                , IFNULL(SUM(a.plantation_land_document_other),0) AS plantation_land_document_other
                , IFNULL(SUM(a.plantation_owner_regisfarmer),0) AS plantation_owner_regisfarmer
                , IFNULL(SUM(a.plantation_owner_fammember),0) AS plantation_owner_fammember
                , IFNULL(SUM(a.plantation_owner_otherpeople),0) AS plantation_owner_otherpeople
                , IFNULL(SUM(a.plantation_owner_donotknow),0) AS plantation_owner_donotknow
                , IFNULL(SUM(a.tree_age_1_3),0) AS tree_age_1_3
                , IFNULL(SUM(a.tree_age_4_6),0) AS tree_age_4_6
                , IFNULL(SUM(a.tree_age_7_18),0) AS tree_age_7_18
                , IFNULL(SUM(a.tree_age_19),0) AS tree_age_19
                , IFNULL(SUM(a.obtain_plantation_inheritance),0) AS obtain_plantation_inheritance
                , IFNULL(SUM(a.obtain_plantation_purchased),0) AS obtain_plantation_purchased
                , IFNULL(SUM(a.obtain_plantation_convert),0) AS obtain_plantation_convert
                , IFNULL(SUM(a.obtain_plantation_government),0) AS obtain_plantation_government
                , IFNULL(SUM(a.obtain_plantation_others),0) AS obtain_plantation_others
                , IFNULL(SUM(a.topography_flat),0) AS topography_flat
                , IFNULL(SUM(a.topography_hilly),0) AS topography_hilly
                , IFNULL(SUM(a.topography_mountainous),0) AS topography_mountainous
            FROM
                dash_det_garden a
                $sqlcJoin
            WHERE
                1 = 1
                $sqlcWhere
                $sqlcHakAkses
                $sqldWhereFtype
            GROUP BY label
            ORDER BY label";
        $query = $this->db->query($sql,array());
        $result['dataChart'] = $query->result_array();
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        //data group by wilayah (untuk chart) ============================================= (end)

        //ambil supply based mapped nya
        $sql="SELECT
                a.`SetValue`
            FROM
                sys_setting a
            WHERE
                a.`SetKey` = 'sup_base_unilever'
            LIMIT 1";
        $query = $this->db->query($sql);
        $dataSuppBased = $query->row_array();
        $suppBasedValue = $dataSuppBased['SetValue'];
        $result['dataAdditional']['suppBasedValue'] = $suppBasedValue;

        return $result;
    }

}

/* End of file mdboardgarden.php */
/* Location: ./application/models/mdboardgarden.php */