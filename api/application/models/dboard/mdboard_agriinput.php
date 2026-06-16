<?php
/**
 * @Author: nikolius
 * @Date:   2018-01-08 15:07:36
 * @Last Modified by:   nikolius
 * @Last Modified time: 2018-01-10 14:18:05
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mdboard_agriinput extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    public function generateDashAgriInput(){
    	$this->db->trans_begin();

        //truncate dl tabelnya
        $sql="DELETE FROM dash_det_agri_input";
        $query = $this->db->query($sql);

        $sql="
            SELECT
                kd.ProvinceID AS ProvinceID
                , kd.DistrictID AS DistrictID
                , ksd.SubDistrictID AS SubDistrictID
                , a.`VillageID`
            FROM
                ktv_members a
                JOIN ktv_member_role r ON a.MemberID = r.MemberID
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
            	INSERT INTO dash_det_agri_input
            	SELECT
                    tbl_region.ProvinceID
                    , tbl_region.DistrictID
                    , tbl_region.SubDistrictID
                    , tbl_region.VillageID
                    , tbl_region.PartnerID
                    , calc_garden.TotalPlot

                    , calc_garden.CompostKg
                    , calc_garden.CompostKgPerHaPembagi
                    , calc_garden.FertKg
                    , calc_garden.FertKgPerHaPembagi

					, calc_farmer_garden.FarmerUsePesYes
					, calc_farmer_garden.FarmerUsePesNo
					, calc_farmer_garden.FarmerUseOrgPestYes
					, calc_farmer_garden.FarmerUseOrgPestNo
					, calc_farmer_garden.FarmerUseChemicalFertYes
					, calc_farmer_garden.FarmerUseChemicalFertNo
					, calc_farmer_garden.FarmerUseOrgFertYes
					, calc_farmer_garden.FarmerUseOrgFertNo
					, calc_farmer_garden.FarmerUseProtectGearYes
					, calc_farmer_garden.FarmerUseProtectGearNo
					, calc_farmer_garden.FarmerHandPestBotSafeYes
					, calc_farmer_garden.FarmerHandPestBotSafeNo
					, calc_farmer_garden.FarmerStorePestSafeYes
					, calc_farmer_garden.FarmerStorePestSafeNo

                    , calc_garden.CompAppPBA
                    , calc_garden.CompAppPB
                    , calc_garden.CompAppFromPB
                    , calc_garden.CompAppManure

                    , calc_garden.FertAppTreeTBM
                    , calc_garden.FertAppTreeTM
                    , calc_garden.FertAppTreeTR

                    , calc_garden.FertAppUrea
                    , calc_garden.FertAppSS
                    , calc_garden.FertAppNPK
                    , calc_garden.FertAppTSP
                    , calc_garden.FertAppCU
                    , calc_garden.FertAppKCL
                    , calc_garden.FertAppNPKMuti
                    , calc_garden.FertAppBorat
                    , calc_garden.FertAppDolomite

                    , calc_garden.DisBlast
                    , calc_garden.DisGeno
                    , calc_garden.DisSteam
                    , calc_garden.DisBud
                    , calc_garden.DisSpear
                    , calc_garden.DisYellow
                    , calc_garden.DisAnt
                    , calc_garden.DisCrown
                    , calc_garden.DisViscular
                    , calc_garden.DisBunch

                    , calc_garden.PestRats
                    , calc_garden.PestOly
                    , calc_garden.PestSatora
                    , calc_garden.PestTira
                    , calc_garden.PestRhino
                    , calc_garden.PestElep
                    , calc_garden.PestOrgUtan
                    , calc_garden.PestLandak
                    , calc_garden.PestBabi

                    , calc_garden.HerbiYes
                    , calc_garden.HerbiNo
                    , calc_garden.InsecYes
                    , calc_garden.InsecNo
                    , calc_garden.FungiYes
                    , calc_garden.FungiNo

                    , calc_garden.PestUsageInHh
                    , calc_garden.PestUsageSpecPlace
                    , calc_garden.PestUsageOutHouse
                    , calc_garden.PestUsageOutFarm
                    , calc_garden.PestUsageOther

                    , calc_garden.PestPackRandom
                    , calc_garden.PestPackSomeElse
                    , calc_garden.PestPackBurry
                    , calc_garden.PestPackBurn
                    , calc_garden.PestPackRecycle
                    , calc_garden.PestPackOther

                    , calc_garden.HerbiParaquat
                    , calc_garden.HerbiGlyphosate
                    , calc_garden.HerbiAllowed

                    , calc_garden.InsecBanned
                    , calc_garden.InsecWatchlist
                    , calc_garden.InsecAllowed

                    , calc_garden.FungiBanned
                    , calc_garden.FungiWatchlist
                    , calc_garden.FungiAllowed

                    , NOW()
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
							, acc_pm.apmPartnerID AS PartnerID
                            , COUNT(p.PlotNr) AS TotalPlot

							, IFNULL(SUM(IFNULL((p.FertPBATimesYear * p.FertPBADose),0) +
								IFNULL((p.FertPBTimesYear * p.FertPBDose),0) +
								IFNULL((p.FertCPBTimesYear * p.FertCPBDose),0) +
								IFNULL((p.FertManureTimesYear * p.FertManureDose),0)),0) AS CompostKg
							, SUM(IF(
								IFNULL((p.FertPBATimesYear * p.FertPBADose),0) +
								IFNULL((p.FertPBTimesYear * p.FertPBDose),0) +
								IFNULL((p.FertCPBTimesYear * p.FertCPBDose),0) +
								IFNULL((p.FertManureTimesYear * p.FertManureDose),0) > 0, p.`GardenAreaHa`, 0
							)) AS CompostKgPerHaPembagi

							, IFNULL(SUM(
								IFNULL((p.FertUreaTimesYear * p.`FertUreaDose`),0) +
								IFNULL((p.FertSSTimesYear * p.FertSSDose),0) +
								IFNULL((p.FertNPKTimesYear * p.`FertNPKDose`),0) +
								IFNULL((p.FertTSPTimesYear * p.`FertTSPDose`),0) +
								IFNULL((p.FertCUTimesYear * p.`FertCUDose`),0) +
								IFNULL((p.FertKCLTimesYear * p.`FertKCLDose`),0) +
								IFNULL((p.FertNPKMutiTimesYear * p.`FertNPKMutiDose`),0) +
								IFNULL((p.FertBoratTimesYear * p.`FertBoratDose`),0) +
								IFNULL((p.FertDolomiteTimesYear * p.`FertDolomiteDose`),0)
							),0) AS FertKg
							, SUM(IF(
								IFNULL((p.FertUreaTimesYear * p.`FertUreaDose`),0) +
								IFNULL((p.FertSSTimesYear * p.FertSSDose),0) +
								IFNULL((p.FertNPKTimesYear * p.`FertNPKDose`),0) +
								IFNULL((p.FertTSPTimesYear * p.`FertTSPDose`),0) +
								IFNULL((p.FertCUTimesYear * p.`FertCUDose`),0) +
								IFNULL((p.FertKCLTimesYear * p.`FertKCLDose`),0) +
								IFNULL((p.FertNPKMutiTimesYear * p.`FertNPKMutiDose`),0) +
								IFNULL((p.FertBoratTimesYear * p.`FertBoratDose`),0) +
								IFNULL((p.FertDolomiteTimesYear * p.`FertDolomiteDose`),0) > 0, p.`GardenAreaHa`, 0
							)) AS FertKgPerHaPembagi

                            , SUM(IF(p.FertPBATimesYear > 0 AND p.FertPBADose > 0,1,0)) AS CompAppPBA
                            , SUM(IF(p.FertPBTimesYear > 0 AND p.FertPBDose > 0,1,0)) AS CompAppPB
                            , SUM(IF(p.FertCPBTimesYear > 0 AND p.FertCPBDose > 0,1,0)) AS CompAppFromPB
                            , SUM(IF(p.FertManureTimesYear > 0 AND p.FertManureDose > 0,1,0)) AS CompAppManure

                            , SUM(IF(p.FertWithNonOrgaTBM = '1',1,0)) AS FertAppTreeTBM
                            , SUM(IF(p.FertWithNonOrgaTM = '1',1,0)) AS FertAppTreeTM
                            , SUM(IF(p.FertWithNonOrgaTR = '1',1,0)) AS FertAppTreeTR

                            , SUM(IF(p.FertUreaTimesYear > 0 AND p.FertUreaDose > 0,1,0)) AS FertAppUrea
                            , SUM(IF(p.FertSSTimesYear > 0 AND p.FertSSDose > 0,1,0)) AS FertAppSS
                            , SUM(IF(p.FertNPKTimesYear > 0 AND p.FertNPKDose > 0,1,0)) AS FertAppNPK
                            , SUM(IF(p.FertTSPTimesYear > 0 AND p.FertTSPDose > 0,1,0)) AS FertAppTSP
                            , SUM(IF(p.FertCUTimesYear > 0 AND p.FertCUDose > 0,1,0)) AS FertAppCU
                            , SUM(IF(p.FertKCLTimesYear > 0 AND p.FertKCLDose > 0,1,0)) AS FertAppKCL
                            , SUM(IF(p.FertNPKMutiTimesYear > 0 AND p.FertNPKMutiDose > 0,1,0)) AS FertAppNPKMuti
                            , SUM(IF(p.FertBoratTimesYear > 0 AND p.FertBoratDose > 0,1,0)) AS FertAppBorat
                            , SUM(IF(p.FertDolomiteTimesYear > 0 AND p.FertDolomiteDose > 0,1,0)) AS FertAppDolomite

                            , SUM(IF(p.DisMainBlast = '1',1,0)) AS DisBlast
                            , SUM(IF(p.DisMainGeno = '1',1,0)) AS DisGeno
                            , SUM(IF(p.DisMainSteam = '1',1,0)) AS DisSteam
                            , SUM(IF(p.DisMainBud = '1',1,0)) AS DisBud
                            , SUM(IF(p.DisMainSpear = '1',1,0)) AS DisSpear
                            , SUM(IF(p.DisMainYellow = '1',1,0)) AS DisYellow
                            , SUM(IF(p.DisMainAnt = '1',1,0)) AS DisAnt
                            , SUM(IF(p.DisMainCrown = '1',1,0)) AS DisCrown
                            , SUM(IF(p.DisMainViscular = '1',1,0)) AS DisViscular
                            , SUM(IF(p.DisMainBunch = '1',1,0)) AS DisBunch

                            , SUM(IF(p.PestMainRats = '1',1,0)) AS PestRats
                            , SUM(IF(p.PestMainOly = '1',1,0)) AS PestOly
                            , SUM(IF(p.PestMainSatora = '1',1,0)) AS PestSatora
                            , SUM(IF(p.PestMainTira = '1',1,0)) AS PestTira
                            , SUM(IF(p.PestMainRhino = '1',1,0)) AS PestRhino
                            , SUM(IF(p.PestMainElep = '1',1,0)) AS PestElep
                            , SUM(IF(p.PestMainOrgUtan = '1',1,0)) AS PestOrgUtan
                            , SUM(IF(p.PestMainLandak = '1',1,0)) AS PestLandak
                            , SUM(IF(p.PestMainBabi = '1',1,0)) AS PestBabi

                            , SUM(IF(p.PeUsingHerbicide = '1',1,0)) AS HerbiYes
                            , SUM(IF(p.PeUsingHerbicide = '2',1,0)) AS HerbiNo
                            , SUM(IF(p.PeUsingInsecticide = '1',1,0)) AS InsecYes
                            , SUM(IF(p.PeUsingInsecticide = '2',1,0)) AS InsecNo
                            , SUM(IF(p.PeUsingFungicide = '1',1,0)) AS FungiYes
                            , SUM(IF(p.PeUsingFungicide = '2',1,0)) AS FungiNo

                            , SUM(IF(p.PestStoreLocation = '1',1,0)) AS PestUsageInHh
                            , SUM(IF(p.PestStoreLocation = '2',1,0)) AS PestUsageSpecPlace
                            , SUM(IF(p.PestStoreLocation = '3',1,0)) AS PestUsageOutHouse
                            , SUM(IF(p.PestStoreLocation = '4',1,0)) AS PestUsageOutFarm
                            , SUM(IF(p.PestStoreLocation = '5',1,0)) AS PestUsageOther

                            , SUM(IF(p.PestPackageAfterUse = '1',1,0)) AS PestPackRandom
                            , SUM(IF(p.PestPackageAfterUse = '2',1,0)) AS PestPackSomeElse
                            , SUM(IF(p.PestPackageAfterUse = '3',1,0)) AS PestPackBurry
                            , SUM(IF(p.PestPackageAfterUse = '4',1,0)) AS PestPackBurn
                            , SUM(IF(p.PestPackageAfterUse = '5',1,0)) AS PestPackRecycle
                            , SUM(IF(p.PestPackageAfterUse = '6',1,0)) AS PestPackOther

                            , SUM(IF(p.PeUsingHerbicide = '1',
                                IF(
                                    p.PeHerbi5 = '1' OR
                                    p.PeHerbi9 = '1' OR
                                    p.PeHerbi10 = '1' OR
                                    p.PeHerbi11 = '1' OR
                                    p.PeHerbi12 = '1' OR
                                    p.PeHerbi13 = '1' OR
                                    p.PeHerbi18 = '1' OR
                                    p.PeHerbi25 = '1' OR
                                    p.PeHerbi26 = '1' OR
                                    p.PeHerbi27 = '1' OR
                                    p.PeHerbi28 = '1' OR
                                    p.PeHerbi29 = '1' , 1, 0
                                )
                            ,0)) AS HerbiParaquat
                            , SUM(IF(p.PeUsingHerbicide = '1',
                                IF(
                                    p.PeHerbi1 = '1' OR
                                    p.PeHerbi2 = '1' OR
                                    p.PeHerbi3 = '1' OR
                                    p.PeHerbi4 = '1' OR
                                    p.PeHerbi6 = '1' OR
                                    p.PeHerbi7 = '1' OR
                                    p.PeHerbi8 = '1' OR
                                    p.PeHerbi14 = '1' OR
                                    p.PeHerbi15 = '1' OR
                                    p.PeHerbi16 = '1' OR
                                    p.PeHerbi17 = '1' OR
                                    p.PeHerbi19 = '1' OR
                                    p.PeHerbi20 = '1' OR
                                    p.PeHerbi21 = '1' OR
                                    p.PeHerbi23 = '1' OR
                                    p.PeHerbi24 = '1' , 1, 0
                                )
                            ,0)) AS HerbiGlyphosate
                            , SUM(IF(p.PeUsingHerbicide = '1',
                                IF(
                                    p.PeHerbi14 = '1' OR
                                    p.PeHerbi15 = '1' OR
                                    p.PeHerbi16 = '1' OR
                                    p.PeHerbi21 = '1' OR
                                    p.PeHerbi22 = '1' , 1, 0
                                )
                            ,0)) AS HerbiAllowed

                            , SUM(IF(p.PeUsingInsecticide = '1',
                                IF(
                                    p.PeInsec11 = '1' OR
                                    p.PeHerbi19 = '1' OR
                                    p.PeHerbi20 = '1' OR
                                    p.PeHerbi21 = '1' OR
                                    p.PeHerbi22 = '1' , 1, 0
                                )
                            ,0)) AS InsecBanned
                            , SUM(IF(p.PeUsingInsecticide = '1',
                                IF(
                                    p.PeInsec1 = '1' OR
                                    p.PeHerbi2 = '1' OR
                                    p.PeHerbi5 = '1' OR
                                    p.PeHerbi6 = '1' OR
                                    p.PeHerbi7 = '1' OR
                                    p.PeHerbi8 = '1' OR
                                    p.PeHerbi9 = '1' OR
                                    p.PeHerbi10 = '1' OR
                                    p.PeHerbi14 = '1' OR
                                    p.PeHerbi18 = '1' , 1, 0
                                )
                            ,0)) AS InsecWatchlist
                            , SUM(IF(p.PeUsingInsecticide = '1',
                                IF(
                                    p.PeInsec3 = '1' OR
                                    p.PeHerbi4 = '1' OR
                                    p.PeHerbi12 = '1' OR
                                    p.PeHerbi13 = '1' OR
                                    p.PeHerbi15 = '1' OR
                                    p.PeHerbi16 = '1' OR
                                    p.PeHerbi17 = '1' OR
                                    p.PeHerbi23 = '1' , 1, 0
                                )
                            ,0)) AS InsecAllowed

                            , SUM(IF(p.PeUsingFungicide = '1',
                                IF(
                                    p.PeFungi11 = '1' , 1, 0
                                )
                            ,0)) AS FungiBanned
                            , SUM(IF(p.PeUsingFungicide = '1',
                                IF(
                                    p.PeFungi2 = '1' OR
                                    p.PeFungi5 = '1' OR
                                    p.PeFungi6 = '1' OR
                                    p.PeFungi9 = '1' , 1, 0
                                )
                            ,0)) AS FungiWatchlist
                            , SUM(IF(p.PeUsingFungicide = '1',
                                IF(
                                    p.PeFungi1 = '1' OR
                                    p.PeFungi3 = '1' OR
                                    p.PeFungi4 = '1' OR
                                    p.PeFungi7 = '1' OR
                                    p.PeFungi10 = '1' OR
                                    p.PeFungi12 = '1' , 1, 0
                                )
                            ,0)) AS FungiAllowed

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
							AND m.`VillageID` = '{$dataRegion[$i]['VillageID']}'
						GROUP BY m.`VillageID`

                    ) AS calc_garden ON 1=1
                    	AND tbl_region.PartnerID = calc_garden.PartnerID
                    	AND tbl_region.VillageID = calc_garden.VillageID


                    LEFT JOIN (

                    	SELECT
							grup_farmer.VillageID
							, grup_farmer.PartnerID
							, SUM(IF(grup_farmer.FarmerUsePesYes > 0,1,0)) AS FarmerUsePesYes
							, SUM(IF(grup_farmer.FarmerUsePesNo > 0,1,0)) AS FarmerUsePesNo
							, SUM(IF(grup_farmer.FarmerUseOrgPestYes > 0,1,0)) AS FarmerUseOrgPestYes
							, SUM(IF(grup_farmer.FarmerUseOrgPestNo > 0,1,0)) AS FarmerUseOrgPestNo
							, SUM(IF(grup_farmer.FarmerUseChemicalFertYes > 0,1,0)) AS FarmerUseChemicalFertYes
							, SUM(IF(grup_farmer.FarmerUseChemicalFertNo > 0,1,0)) AS FarmerUseChemicalFertNo
							, SUM(IF(grup_farmer.FarmerUseOrgFertYes > 0,1,0)) AS FarmerUseOrgFertYes
							, SUM(IF(grup_farmer.FarmerUseOrgFertNo > 0,1,0)) AS FarmerUseOrgFertNo
							, SUM(IF(grup_farmer.FarmerUseProtectGearYes > 0,1,0)) AS FarmerUseProtectGearYes
							, SUM(IF(grup_farmer.FarmerUseProtectGearNo > 0,1,0)) AS FarmerUseProtectGearNo
							, SUM(IF(grup_farmer.FarmerHandPestBotSafeYes > 0,1,0)) AS FarmerHandPestBotSafeYes
							, SUM(IF(grup_farmer.FarmerHandPestBotSafeNo > 0,1,0)) AS FarmerHandPestBotSafeNo
							, SUM(IF(grup_farmer.FarmerStorePestSafeYes > 0,1,0)) AS FarmerStorePestSafeYes
							, SUM(IF(grup_farmer.FarmerStorePestSafeNo > 0,1,0)) AS FarmerStorePestSafeNo
						FROM
						(
							SELECT
								m.`MemberID`
								, m.`VillageID`
								, acc_pm.apmPartnerID AS PartnerID

								, SUM(IF(
									p.`PeUsingFungicide` = 1 OR
									p.`PeUsingInsecticide` = 1 OR
									p.`PeUsingFungicide` = 1 , 1, 0
								)) AS FarmerUsePesYes

								, SUM(IF(
									p.`PeUsingFungicide` = 2 AND
									p.`PeUsingInsecticide` = 2 AND
									p.`PeUsingFungicide` = 2 , 1, 0
								)) AS FarmerUsePesNo

								, SUM(IF(
									(p.`PeUsingInsecticide` = 1 AND	p.`PeInsec23` = 1) OR
									(p.`PeUsingFungicide` = 1 AND p.`PeFungi12` = 1) , 1, 0
								)) AS FarmerUseOrgPestYes

								, SUM(IF(
									(p.`PeUsingHerbicide` = 1) OR
									(p.`PeUsingInsecticide` = 1 AND	p.`PeInsec23` IS NULL) OR
									(p.`PeUsingFungicide` = 1 AND p.`PeFungi12` IS NULL) , 1, 0
								)) AS FarmerUseOrgPestNo

								, SUM(IF(p.FertNonOrganicData = 1,1,0)) AS FarmerUseChemicalFertYes
								, SUM(IF(p.FertNonOrganicData = 2,1,0)) AS FarmerUseChemicalFertNo

								, SUM(IF(p.FertUseOrganic = 1,1,0)) AS FarmerUseOrgFertYes
								, SUM(IF(p.FertUseOrganic = 2,1,0)) AS FarmerUseOrgFertNo

								, SUM(IF(p.UseProtectiveGear = 1,1,0)) AS FarmerUseProtectGearYes
								, SUM(IF(p.UseProtectiveGear = 2,1,0)) AS FarmerUseProtectGearNo

								, SUM(IF(p.PestPackageAfterUse = 3 OR p.PestPackageAfterUse = 4 OR p.PestPackageAfterUse = 5,1,0)) AS FarmerHandPestBotSafeYes
								, SUM(IF(p.PestPackageAfterUse = 1 OR p.PestPackageAfterUse = 2,1,0)) AS FarmerHandPestBotSafeNo

								, SUM(IF(p.PestStoreLocation = 2 OR p.PestStoreLocation = 3 OR p.PestStoreLocation = 4,1,0)) AS FarmerStorePestSafeYes
								, SUM(IF(p.PestStoreLocation = 1,1,0)) AS FarmerStorePestSafeNo
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
								AND m.VillageID = '{$dataRegion[$i]['VillageID']}'
							GROUP BY m.MemberID
						) AS grup_farmer
						GROUP BY grup_farmer.VillageID

                    ) AS calc_farmer_garden ON 1=1
                    	AND tbl_region.PartnerID = calc_farmer_garden.PartnerID
                    	AND tbl_region.VillageID = calc_farmer_garden.VillageID
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

    public function getDisplayAgriInput($ProvinceID,$DistrictID){
    	//data display langsung ================================================== (begin)

    	if($ProvinceID != ""){
            $sqldWherePropinsi = " AND kd.ProvinceID = '$ProvinceID' ";
        }else{
            $sqldWherePropinsi = "";
        }

        if($DistrictID != ""){
            $sqldWhereDistrict = " AND kd.DistrictID = '$DistrictID' ";
        }else{
            $sqldWhereDistrict = "";
        }

        //buat SqlHakAksesKontrol (begin)
        if($_SESSION['is_admin'] == "1"){
            $sqlHakAkses = " AND a.PartnerID = '1' #Partner Koltiva ";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            //cek ktv_access_staff
            $sqlHakAkses = " AND kd.DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlHakAkses .= " AND a.PartnerID = '{$_SESSION['PartnerID']}' ";
        } else {
            //cek ktv_access_staff
            $sqlHakAkses = " AND kd.DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlHakAkses .= " AND a.PartnerID = '1' #Partner Koltiva ";
        }
        //buat SqlHakAksesKontrol (end)

        $sql="SELECT
                SUM(TotalPlot) AS TotalPlot
        		, SUM(IFNULL(CompostKg,0)) / SUM(IFNULL(CompostKgPerHaPembagi,0)) AS CompostKgPerHa
        		, SUM(IFNULL(FertKg,0)) / SUM(IFNULL(FertKgPerHaPembagi,0)) AS FertKg
        		, SUM(FarmerUsePesYes) / SUM(FarmerUsePesYes + FarmerUsePesNo) * 100 AS FarmerUsePes
        		, SUM(FarmerUseOrgPestYes) / SUM(FarmerUseOrgPestYes + FarmerUseOrgPestNo) * 100 AS FarmerUseOrgPest
        		, SUM(FarmerUseChemicalFertYes) / SUM(FarmerUseChemicalFertYes + FarmerUseChemicalFertNo) * 100 AS FarmerUseChemicalFert
        		, SUM(FarmerUseOrgFertYes) / SUM(FarmerUseOrgFertYes + FarmerUseOrgFertNo) * 100 AS FarmerUseOrgFert
        		, SUM(FarmerUseProtectGearYes) / SUM(FarmerUseProtectGearYes + FarmerUseProtectGearNo) * 100 AS FarmerUseProtectGear
        		, SUM(FarmerHandPestBotSafeYes) / SUM(FarmerHandPestBotSafeYes + FarmerHandPestBotSafeNo) * 100 AS FarmerHandPestBotSafe
        		, SUM(FarmerStorePestSafeYes) / SUM(FarmerStorePestSafeYes + FarmerStorePestSafeNo) * 100 AS FarmerStorePestSafe

                , IFNULL(SUM(FertAppUrea),0) AS FertAppUrea
                , IFNULL(SUM(FertAppSS),0) AS FertAppSS
                , IFNULL(SUM(FertAppNPK),0) AS FertAppNPK
                , IFNULL(SUM(FertAppTSP),0) AS FertAppTSP
                , IFNULL(SUM(FertAppCU),0) AS FertAppCU
                , IFNULL(SUM(FertAppKCL),0) AS FertAppKCL
                , IFNULL(SUM(FertAppNPKMuti),0) AS FertAppNPKMuti
                , IFNULL(SUM(FertAppBorat),0) AS FertAppBorat
                , IFNULL(SUM(FertAppDolomite),0) AS FertAppDolomite

                , IFNULL(SUM(DisBlast),0) AS DisBlast
                , IFNULL(SUM(DisGeno),0) AS DisGeno
                , IFNULL(SUM(DisSteam),0) AS DisSteam
                , IFNULL(SUM(DisBud),0) AS DisBud
                , IFNULL(SUM(DisSpear),0) AS DisSpear
                , IFNULL(SUM(DisYellow),0) AS DisYellow
                , IFNULL(SUM(DisAnt),0) AS DisAnt
                , IFNULL(SUM(DisCrown),0) AS DisCrown
                , IFNULL(SUM(DisViscular),0) AS DisViscular
                , IFNULL(SUM(DisBunch),0) AS DisBunch

                , IFNULL(SUM(PestRats),0) AS PestRats
                , IFNULL(SUM(PestOly),0) AS PestOly
                , IFNULL(SUM(PestSatora),0) AS PestSatora
                , IFNULL(SUM(PestTira),0) AS PestTira
                , IFNULL(SUM(PestRhino),0) AS PestRhino
                , IFNULL(SUM(PestElep),0) AS PestElep
                , IFNULL(SUM(PestOrgUtan),0) AS PestOrgUtan
                , IFNULL(SUM(PestLandak),0) AS PestLandak
                , IFNULL(SUM(PestBabi),0) AS PestBabi

                , IFNULL(SUM(HerbiYes),0) AS HerbiYes
                , IFNULL(SUM(HerbiNo),0) AS HerbiNo
                , IFNULL(SUM(InsecYes),0) AS InsecYes
                , IFNULL(SUM(InsecNo),0) AS InsecNo
                , IFNULL(SUM(FungiYes),0) AS FungiYes
                , IFNULL(SUM(FungiNo),0) AS FungiNo

                , SUM(IFNULL(HerbiParaquat,0)) / SUM(IFNULL(HerbiYes,0)) * 100 AS HerbiParaquat
                , SUM(IFNULL(HerbiGlyphosate,0)) / SUM(IFNULL(HerbiYes,0)) * 100 AS HerbiGlyphosate
                , SUM(IFNULL(HerbiAllowed,0)) / SUM(IFNULL(HerbiYes,0)) * 100 AS HerbiAllowed

                , SUM(IFNULL(InsecBanned,0)) / SUM(IFNULL(InsecYes,0)) * 100 AS InsecBanned
                , SUM(IFNULL(InsecWatchlist,0)) / SUM(IFNULL(InsecYes,0)) * 100 AS InsecWatchlist
                , SUM(IFNULL(InsecAllowed,0)) / SUM(IFNULL(InsecYes,0)) * 100 AS InsecAllowed

                , SUM(IFNULL(FungiBanned,0)) / SUM(IFNULL(FungiYes,0)) * 100 AS FungiBanned
                , SUM(IFNULL(FungiWatchlist,0)) / SUM(IFNULL(FungiYes,0)) * 100 AS FungiWatchlist
                , SUM(IFNULL(FungiAllowed,0)) / SUM(IFNULL(FungiYes,0)) * 100 AS FungiAllowed

        		, DateGenerated
            FROM
                dash_det_agri_input a
                LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
                LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
            WHERE
                1 = 1
                $sqldWherePropinsi
                $sqldWhereDistrict
                $sqlHakAkses
            ";
        $query = $this->db->query($sql,array());
        $result['dataDisplay'] = $query->row_array();

        //data display langsung ================================================== (end)


        //data group by wilayah (untuk chart) ============================================= (begin)

        if($ProvinceID == ""){
            $sqlcLabel = "Province";
            $sqlcJoin = " LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                        LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
                        LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
                        LEFT JOIN ktv_province kp ON kp.ProvinceID = kd.ProvinceID";
            $sqlcWhere = "";
        } elseif($DistrictID == "") {
            $sqlcLabel = "District";
            $sqlcJoin = " LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                        LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
                        LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID";
            $sqlcWhere = "AND kd.ProvinceID = '$ProvinceID'";
        } else {
            $sqlcLabel = "SubDistrict";
            $sqlcJoin = " LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
                        LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID";
            $sqlcWhere = "AND ksd.DistrictID = '$DistrictID'";
        }

        //buat SqlHakAksesKontrol (begin)
        if($_SESSION['is_admin'] == "1"){
            $sqlcHakAkses = " AND a.PartnerID = '1' #Partner Koltiva ";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            //cek ktv_access_staff
            $sqlcHakAkses = " AND ksd.DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlcHakAkses .= " AND a.PartnerID = '{$_SESSION['PartnerID']}' ";
        } else {
            //cek ktv_access_staff
            $sqlcHakAkses = " AND ksd.DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlcHakAkses .= " AND a.PartnerID = '1' #Partner Koltiva ";
        }
        //buat SqlHakAksesKontrol (end)

        $sql="
        	SELECT
        		$sqlcLabel AS label
        		, SUM(IFNULL(CompostKg,0)) / SUM(IFNULL(CompostKgPerHaPembagi,0)) AS CompostKgPerHa
        		, SUM(IFNULL(FertKg,0)) / SUM(IFNULL(FertKgPerHaPembagi,0)) AS FertKg

                , IFNULL(SUM(CompAppPBA),0) AS CompAppPBA
                , IFNULL(SUM(CompAppPB),0) AS CompAppPB
                , IFNULL(SUM(CompAppFromPB),0) AS CompAppFromPB
                , IFNULL(SUM(CompAppManure),0) AS CompAppManure

                , IFNULL(SUM(FertAppTreeTBM),0) AS FertAppTreeTBM
                , IFNULL(SUM(FertAppTreeTM),0) AS FertAppTreeTM
                , IFNULL(SUM(FertAppTreeTR),0) AS FertAppTreeTR

                , IFNULL(SUM(FertAppUrea),0) AS FertAppUrea
                , IFNULL(SUM(FertAppSS),0) AS FertAppSS
                , IFNULL(SUM(FertAppNPK),0) AS FertAppNPK
                , IFNULL(SUM(FertAppTSP),0) AS FertAppTSP
                , IFNULL(SUM(FertAppCU),0) AS FertAppCU
                , IFNULL(SUM(FertAppKCL),0) AS FertAppKCL
                , IFNULL(SUM(FertAppNPKMuti),0) AS FertAppNPKMuti
                , IFNULL(SUM(FertAppBorat),0) AS FertAppBorat
                , IFNULL(SUM(FertAppDolomite),0) AS FertAppDolomite

                , IFNULL(SUM(DisBlast),0) AS DisBlast
                , IFNULL(SUM(DisGeno),0) AS DisGeno
                , IFNULL(SUM(DisSteam),0) AS DisSteam
                , IFNULL(SUM(DisBud),0) AS DisBud
                , IFNULL(SUM(DisSpear),0) AS DisSpear
                , IFNULL(SUM(DisYellow),0) AS DisYellow
                , IFNULL(SUM(DisAnt),0) AS DisAnt
                , IFNULL(SUM(DisCrown),0) AS DisCrown
                , IFNULL(SUM(DisViscular),0) AS DisViscular
                , IFNULL(SUM(DisBunch),0) AS DisBunch

                , IFNULL(SUM(PestRats),0) AS PestRats
                , IFNULL(SUM(PestOly),0) AS PestOly
                , IFNULL(SUM(PestSatora),0) AS PestSatora
                , IFNULL(SUM(PestTira),0) AS PestTira
                , IFNULL(SUM(PestRhino),0) AS PestRhino
                , IFNULL(SUM(PestElep),0) AS PestElep
                , IFNULL(SUM(PestOrgUtan),0) AS PestOrgUtan
                , IFNULL(SUM(PestLandak),0) AS PestLandak
                , IFNULL(SUM(PestBabi),0) AS PestBabi

                , IFNULL(SUM(PestUsageInHh),0) AS PestUsageInHh
                , IFNULL(SUM(PestUsageSpecPlace),0) AS PestUsageSpecPlace
                , IFNULL(SUM(PestUsageOutHouse),0) AS PestUsageOutHouse
                , IFNULL(SUM(PestUsageOutFarm),0) AS PestUsageOutFarm
                , IFNULL(SUM(PestUsageOther),0) AS PestUsageOther

                , IFNULL(SUM(PestPackRandom),0) AS PestPackRandom
                , IFNULL(SUM(PestPackSomeElse),0) AS PestPackSomeElse
                , IFNULL(SUM(PestPackBurry),0) AS PestPackBurry
                , IFNULL(SUM(PestPackBurn),0) AS PestPackBurn
                , IFNULL(SUM(PestPackRecycle),0) AS PestPackRecycle
                , IFNULL(SUM(PestPackOther),0) AS PestPackOther

                , IFNULL(SUM(HerbiYes),0) AS HerbiYes
                , IFNULL(SUM(HerbiNo),0) AS HerbiNo
                , IFNULL(SUM(InsecYes),0) AS InsecYes
                , IFNULL(SUM(InsecNo),0) AS InsecNo
                , IFNULL(SUM(FungiYes),0) AS FungiYes
                , IFNULL(SUM(FungiNo),0) AS FungiNo

                , IFNULL(SUM(FarmerUseProtectGearYes),0) AS FarmerUseProtectGearYes
                , IFNULL(SUM(FarmerUseProtectGearNo),0) AS FarmerUseProtectGearNo
        	FROM
                dash_det_agri_input a
                $sqlcJoin
            WHERE
                1 = 1
                $sqlcWhere
                $sqlcHakAkses
            GROUP BY label
            ORDER BY label
        ";
        $query = $this->db->query($sql,array());
        $result['dataChart'] = $query->result_array();
        //data group by wilayah (untuk chart) ============================================= (end)

        return $result;
    }

    public function generateDashAgriInputOptimize(){
        $this->db->trans_begin();

        $sql = "DELETE FROM dash_det_agri_input";
        $query = $this->db->query($sql);

        $sql = "INSERT INTO dash_det_agri_input
                SELECT calc_garden.ProvinceID,
                       calc_garden.DistrictID,
                       calc_garden.SubDistrictID,
                       calc_garden.PartnerID,
                       calc_garden.TotalPlot,
                       calc_garden.CompostKg,
                       calc_garden.CompostKgPerHaPembagi,
                       calc_garden.FertKg,
                       calc_garden.FertKgPerHaPembagi,
                       calc_farmer_garden.FarmerUsePesYes,
                       calc_farmer_garden.FarmerUsePesNo,
                       calc_farmer_garden.FarmerUseOrgPestYes,
                       calc_farmer_garden.FarmerUseOrgPestNo,
                       calc_farmer_garden.FarmerUseChemicalFertYes,
                       calc_farmer_garden.FarmerUseChemicalFertNo,
                       calc_farmer_garden.FarmerUseOrgFertYes,
                       calc_farmer_garden.FarmerUseOrgFertNo,
                       calc_farmer_garden.FarmerUseProtectGearYes,
                       calc_farmer_garden.FarmerUseProtectGearNo,
                       calc_farmer_garden.FarmerHandPestBotSafeYes,
                       calc_farmer_garden.FarmerHandPestBotSafeNo,
                       calc_farmer_garden.FarmerStorePestSafeYes,
                       calc_farmer_garden.FarmerStorePestSafeNo,
                       calc_garden.CompAppPBA,
                       calc_garden.CompAppPB,
                       calc_garden.CompAppFromPB,
                       calc_garden.CompAppManure,
                       calc_garden.FertAppTreeTBM,
                       calc_garden.FertAppTreeTM,
                       calc_garden.FertAppTreeTR,
                       calc_garden.FertAppUrea,
                       calc_garden.FertAppSS,
                       calc_garden.FertAppNPK,
                       calc_garden.FertAppTSP,
                       calc_garden.FertAppCU,
                       calc_garden.FertAppKCL,
                       calc_garden.FertAppNPKMuti,
                       calc_garden.FertAppBorat,
                       calc_garden.FertAppDolomite,
                       calc_garden.DisBlast,
                       calc_garden.DisGeno,
                       calc_garden.DisSteam,
                       calc_garden.DisBud,
                       calc_garden.DisSpear,
                       calc_garden.DisYellow,
                       calc_garden.DisAnt,
                       calc_garden.DisCrown,
                       calc_garden.DisViscular,
                       calc_garden.DisBunch,
                       calc_garden.PestRats,
                       calc_garden.PestOly,
                       calc_garden.PestSatora,
                       calc_garden.PestTira,
                       calc_garden.PestRhino,
                       calc_garden.PestElep,
                       calc_garden.PestOrgUtan,
                       calc_garden.PestLandak,
                       calc_garden.PestBabi,
                       calc_garden.HerbiYes,
                       calc_garden.HerbiNo,
                       calc_garden.InsecYes,
                       calc_garden.InsecNo,
                       calc_garden.FungiYes,
                       calc_garden.FungiNo,
                       calc_garden.PestUsageInHh,
                       calc_garden.PestUsageSpecPlace,
                       calc_garden.PestUsageOutHouse,
                       calc_garden.PestUsageOutFarm,
                       calc_garden.PestUsageOther,
                       calc_garden.PestPackRandom,
                       calc_garden.PestPackSomeElse,
                       calc_garden.PestPackBurry,
                       calc_garden.PestPackBurn,
                       calc_garden.PestPackRecycle,
                       calc_garden.PestPackOther,
                       calc_garden.HerbiParaquat,
                       calc_garden.HerbiGlyphosate,
                       calc_garden.HerbiAllowed,
                       calc_garden.InsecBanned,
                       calc_garden.InsecWatchlist,
                       calc_garden.InsecAllowed,
                       calc_garden.FungiBanned,
                       calc_garden.FungiWatchlist,
                       calc_garden.FungiAllowed,
                       NOW()
                FROM
                  (SELECT ks.SubDistrictID AS SubDistrictID,
                          kd.DistrictID AS DistrictID,
                          kp.ProvinceID AS ProvinceID, 
                          kapm.apmPartnerID AS PartnerID,
                          COUNT(p.PlotNr) AS TotalPlot,
                          IFNULL(SUM(IFNULL((p.FertPBATimesYear * p.FertPBADose),0) + IFNULL((p.FertPBTimesYear * p.FertPBDose),0) + IFNULL((p.FertCPBTimesYear * p.FertCPBDose),0) + IFNULL((p.FertManureTimesYear * p.FertManureDose),0)), 0) AS CompostKg,
                          SUM(IF(IFNULL((p.FertPBATimesYear * p.FertPBADose),0) + IFNULL((p.FertPBTimesYear * p.FertPBDose),0) + IFNULL((p.FertCPBTimesYear * p.FertCPBDose),0) + IFNULL((p.FertManureTimesYear * p.FertManureDose),0) > 0, p.`GardenAreaHa`, 0)) AS CompostKgPerHaPembagi,
                          IFNULL(SUM(IFNULL((p.FertUreaTimesYear * p.`FertUreaDose`),0) + IFNULL((p.FertSSTimesYear * p.FertSSDose),0) + IFNULL((p.FertNPKTimesYear * p.`FertNPKDose`),0) + IFNULL((p.FertTSPTimesYear * p.`FertTSPDose`),0) + IFNULL((p.FertCUTimesYear * p.`FertCUDose`),0) + IFNULL((p.FertKCLTimesYear * p.`FertKCLDose`),0) + IFNULL((p.FertNPKMutiTimesYear * p.`FertNPKMutiDose`),0) + IFNULL((p.FertBoratTimesYear * p.`FertBoratDose`),0) + IFNULL((p.FertDolomiteTimesYear * p.`FertDolomiteDose`),0)), 0) AS FertKg,
                          SUM(IF(IFNULL((p.FertUreaTimesYear * p.`FertUreaDose`),0) + IFNULL((p.FertSSTimesYear * p.FertSSDose),0) + IFNULL((p.FertNPKTimesYear * p.`FertNPKDose`),0) + IFNULL((p.FertTSPTimesYear * p.`FertTSPDose`),0) + IFNULL((p.FertCUTimesYear * p.`FertCUDose`),0) + IFNULL((p.FertKCLTimesYear * p.`FertKCLDose`),0) + IFNULL((p.FertNPKMutiTimesYear * p.`FertNPKMutiDose`),0) + IFNULL((p.FertBoratTimesYear * p.`FertBoratDose`),0) + IFNULL((p.FertDolomiteTimesYear * p.`FertDolomiteDose`),0) > 0, p.`GardenAreaHa`, 0)) AS FertKgPerHaPembagi,
                          SUM(IF(p.FertPBATimesYear > 0
                                 AND p.FertPBADose > 0, 1, 0)) AS CompAppPBA,
                          SUM(IF(p.FertPBTimesYear > 0
                                 AND p.FertPBDose > 0, 1, 0)) AS CompAppPB,
                          SUM(IF(p.FertCPBTimesYear > 0
                                 AND p.FertCPBDose > 0, 1, 0)) AS CompAppFromPB,
                          SUM(IF(p.FertManureTimesYear > 0
                                 AND p.FertManureDose > 0, 1, 0)) AS CompAppManure,
                          SUM(IF(p.FertWithNonOrgaTBM = '1', 1, 0)) AS FertAppTreeTBM,
                          SUM(IF(p.FertWithNonOrgaTM = '1', 1, 0)) AS FertAppTreeTM,
                          SUM(IF(p.FertWithNonOrgaTR = '1', 1, 0)) AS FertAppTreeTR,
                          SUM(IF(p.FertUreaTimesYear > 0
                                 AND p.FertUreaDose > 0, 1, 0)) AS FertAppUrea,
                          SUM(IF(p.FertSSTimesYear > 0
                                 AND p.FertSSDose > 0, 1, 0)) AS FertAppSS,
                          SUM(IF(p.FertNPKTimesYear > 0
                                 AND p.FertNPKDose > 0, 1, 0)) AS FertAppNPK,
                          SUM(IF(p.FertTSPTimesYear > 0
                                 AND p.FertTSPDose > 0, 1, 0)) AS FertAppTSP,
                          SUM(IF(p.FertCUTimesYear > 0
                                 AND p.FertCUDose > 0, 1, 0)) AS FertAppCU,
                          SUM(IF(p.FertKCLTimesYear > 0
                                 AND p.FertKCLDose > 0, 1, 0)) AS FertAppKCL,
                          SUM(IF(p.FertNPKMutiTimesYear > 0
                                 AND p.FertNPKMutiDose > 0, 1, 0)) AS FertAppNPKMuti,
                          SUM(IF(p.FertBoratTimesYear > 0
                                 AND p.FertBoratDose > 0, 1, 0)) AS FertAppBorat,
                          SUM(IF(p.FertDolomiteTimesYear > 0
                                 AND p.FertDolomiteDose > 0, 1, 0)) AS FertAppDolomite,
                          SUM(IF(p.DisMainBlast = '1', 1, 0)) AS DisBlast,
                          SUM(IF(p.DisMainGeno = '1', 1, 0)) AS DisGeno,
                          SUM(IF(p.DisMainSteam = '1', 1, 0)) AS DisSteam,
                          SUM(IF(p.DisMainBud = '1', 1, 0)) AS DisBud,
                          SUM(IF(p.DisMainSpear = '1', 1, 0)) AS DisSpear,
                          SUM(IF(p.DisMainYellow = '1', 1, 0)) AS DisYellow,
                          SUM(IF(p.DisMainAnt = '1', 1, 0)) AS DisAnt,
                          SUM(IF(p.DisMainCrown = '1', 1, 0)) AS DisCrown,
                          SUM(IF(p.DisMainViscular = '1', 1, 0)) AS DisViscular,
                          SUM(IF(p.DisMainBunch = '1', 1, 0)) AS DisBunch,
                          SUM(IF(p.PestMainRats = '1', 1, 0)) AS PestRats,
                          SUM(IF(p.PestMainOly = '1', 1, 0)) AS PestOly,
                          SUM(IF(p.PestMainSatora = '1', 1, 0)) AS PestSatora,
                          SUM(IF(p.PestMainTira = '1', 1, 0)) AS PestTira,
                          SUM(IF(p.PestMainRhino = '1', 1, 0)) AS PestRhino,
                          SUM(IF(p.PestMainElep = '1', 1, 0)) AS PestElep,
                          SUM(IF(p.PestMainOrgUtan = '1', 1, 0)) AS PestOrgUtan,
                          SUM(IF(p.PestMainLandak = '1', 1, 0)) AS PestLandak,
                          SUM(IF(p.PestMainBabi = '1', 1, 0)) AS PestBabi,
                          SUM(IF(p.PeUsingHerbicide = '1', 1, 0)) AS HerbiYes,
                          SUM(IF(p.PeUsingHerbicide = '2', 1, 0)) AS HerbiNo,
                          SUM(IF(p.PeUsingInsecticide = '1', 1, 0)) AS InsecYes,
                          SUM(IF(p.PeUsingInsecticide = '2', 1, 0)) AS InsecNo,
                          SUM(IF(p.PeUsingFungicide = '1', 1, 0)) AS FungiYes,
                          SUM(IF(p.PeUsingFungicide = '2', 1, 0)) AS FungiNo,
                          SUM(IF(p.PestStoreLocation = '1', 1, 0)) AS PestUsageInHh,
                          SUM(IF(p.PestStoreLocation = '2', 1, 0)) AS PestUsageSpecPlace,
                          SUM(IF(p.PestStoreLocation = '3', 1, 0)) AS PestUsageOutHouse,
                          SUM(IF(p.PestStoreLocation = '4', 1, 0)) AS PestUsageOutFarm,
                          SUM(IF(p.PestStoreLocation = '5', 1, 0)) AS PestUsageOther,
                          SUM(IF(p.PestPackageAfterUse = '1', 1, 0)) AS PestPackRandom,
                          SUM(IF(p.PestPackageAfterUse = '2', 1, 0)) AS PestPackSomeElse,
                          SUM(IF(p.PestPackageAfterUse = '3', 1, 0)) AS PestPackBurry,
                          SUM(IF(p.PestPackageAfterUse = '4', 1, 0)) AS PestPackBurn,
                          SUM(IF(p.PestPackageAfterUse = '5', 1, 0)) AS PestPackRecycle,
                          SUM(IF(p.PestPackageAfterUse = '6', 1, 0)) AS PestPackOther,
                          SUM(IF(p.PeUsingHerbicide = '1', IF(p.PeHerbi5 = '1'
                                                              OR p.PeHerbi9 = '1'
                                                              OR p.PeHerbi10 = '1'
                                                              OR p.PeHerbi11 = '1'
                                                              OR p.PeHerbi12 = '1'
                                                              OR p.PeHerbi13 = '1'
                                                              OR p.PeHerbi18 = '1'
                                                              OR p.PeHerbi25 = '1'
                                                              OR p.PeHerbi26 = '1'
                                                              OR p.PeHerbi27 = '1'
                                                              OR p.PeHerbi28 = '1'
                                                              OR p.PeHerbi29 = '1', 1, 0), 0)) AS HerbiParaquat,
                          SUM(IF(p.PeUsingHerbicide = '1', IF(p.PeHerbi1 = '1'
                                                              OR p.PeHerbi2 = '1'
                                                              OR p.PeHerbi3 = '1'
                                                              OR p.PeHerbi4 = '1'
                                                              OR p.PeHerbi6 = '1'
                                                              OR p.PeHerbi7 = '1'
                                                              OR p.PeHerbi8 = '1'
                                                              OR p.PeHerbi14 = '1'
                                                              OR p.PeHerbi15 = '1'
                                                              OR p.PeHerbi16 = '1'
                                                              OR p.PeHerbi17 = '1'
                                                              OR p.PeHerbi19 = '1'
                                                              OR p.PeHerbi20 = '1'
                                                              OR p.PeHerbi21 = '1'
                                                              OR p.PeHerbi23 = '1'
                                                              OR p.PeHerbi24 = '1', 1, 0), 0)) AS HerbiGlyphosate,
                          SUM(IF(p.PeUsingHerbicide = '1', IF(p.PeHerbi14 = '1'
                                                              OR p.PeHerbi15 = '1'
                                                              OR p.PeHerbi16 = '1'
                                                              OR p.PeHerbi21 = '1'
                                                              OR p.PeHerbi22 = '1', 1, 0), 0)) AS HerbiAllowed,
                          SUM(IF(p.PeUsingInsecticide = '1', IF(p.PeInsec11 = '1'
                                                                OR p.PeHerbi19 = '1'
                                                                OR p.PeHerbi20 = '1'
                                                                OR p.PeHerbi21 = '1'
                                                                OR p.PeHerbi22 = '1', 1, 0), 0)) AS InsecBanned,
                          SUM(IF(p.PeUsingInsecticide = '1', IF(p.PeInsec1 = '1'
                                                                OR p.PeHerbi2 = '1'
                                                                OR p.PeHerbi5 = '1'
                                                                OR p.PeHerbi6 = '1'
                                                                OR p.PeHerbi7 = '1'
                                                                OR p.PeHerbi8 = '1'
                                                                OR p.PeHerbi9 = '1'
                                                                OR p.PeHerbi10 = '1'
                                                                OR p.PeHerbi14 = '1'
                                                                OR p.PeHerbi18 = '1', 1, 0), 0)) AS InsecWatchlist,
                          SUM(IF(p.PeUsingInsecticide = '1', IF(p.PeInsec3 = '1'
                                                                OR p.PeHerbi4 = '1'
                                                                OR p.PeHerbi12 = '1'
                                                                OR p.PeHerbi13 = '1'
                                                                OR p.PeHerbi15 = '1'
                                                                OR p.PeHerbi16 = '1'
                                                                OR p.PeHerbi17 = '1'
                                                                OR p.PeHerbi23 = '1', 1, 0), 0)) AS InsecAllowed,
                          SUM(IF(p.PeUsingFungicide = '1', IF(p.PeFungi11 = '1', 1, 0), 0)) AS FungiBanned,
                          SUM(IF(p.PeUsingFungicide = '1', IF(p.PeFungi2 = '1'
                                                              OR p.PeFungi5 = '1'
                                                              OR p.PeFungi6 = '1'
                                                              OR p.PeFungi9 = '1', 1, 0), 0)) AS FungiWatchlist,
                          SUM(IF(p.PeUsingFungicide = '1', IF(p.PeFungi1 = '1'
                                                              OR p.PeFungi3 = '1'
                                                              OR p.PeFungi4 = '1'
                                                              OR p.PeFungi7 = '1'
                                                              OR p.PeFungi10 = '1'
                                                              OR p.PeFungi12 = '1', 1, 0), 0)) AS FungiAllowed
                      FROM
                         ktv_survey_plot p

                         JOIN
                           (SELECT p.MemberID,
                                   p.PlotNr,
                                   MAX(p.SurveyNr) AS SurveyNr
                            FROM ktv_survey_plot p
                            WHERE p.`StatusCode` = 'active'
                            GROUP BY p.MemberID,
                                     p.PlotNr
                            ) z ON p.MemberID = z.MemberID
                               AND p.PlotNr = z.PlotNr
                               AND p.SurveyNr = z.SurveyNr

                         JOIN ktv_members km ON km.MemberID = p.MemberID
                         JOIN ktv_member_role kr ON km.MemberID = kr.MemberID
                         JOIN ktv_access_partner_member kapm ON kapm.`apmMemberID` = kr.`MemberID`
                            AND kr.MRoleID = 1 #ROLE PETANI
                         JOIN ktv_program_partner kpp ON kpp.`PartnerID` = kapm.`apmPartnerID`
                         
                         JOIN ktv_village kv ON kv.`VillageID` = km.`VillageID`
                         JOIN ktv_subdistrict ks ON ks.`SubDistrictID` = kv.`SubDistrictID`
                         JOIN ktv_district kd ON kd.`DistrictID` = ks.`DistrictID`
                         JOIN ktv_province kp ON kp.`ProvinceID` = kd.`ProvinceID`
                      WHERE
                         km.`StatusCode` = 'active'
                         AND km.`VillageID` IS NOT NULL
                         AND km.VillageID != ''
                         AND km.VillageID != '0'
                         AND kpp.`StatusCode` = 'active'
                         AND kpp.IsGenDashboard = 'Yes'
                         AND p.`SurveyNr` IS NOT NULL
                      GROUP BY
                         kp.ProvinceID,
                         kd.DistrictID,
                         ks.SubDistrictID,
                         kpp.PartnerID
                   ) AS calc_garden

                   JOIN

                  (SELECT grup_farmer.ProvinceID,
                      grup_farmer.DistrictID,
                      grup_farmer.SubDistrictID,
                      grup_farmer.PartnerID,
                      SUM(IF(grup_farmer.FarmerUsePesYes > 0, 1, 0)) AS FarmerUsePesYes,
                      SUM(IF(grup_farmer.FarmerUsePesNo > 0, 1, 0)) AS FarmerUsePesNo,
                      SUM(IF(grup_farmer.FarmerUseOrgPestYes > 0, 1, 0)) AS FarmerUseOrgPestYes,
                      SUM(IF(grup_farmer.FarmerUseOrgPestNo > 0, 1, 0)) AS FarmerUseOrgPestNo,
                      SUM(IF(grup_farmer.FarmerUseChemicalFertYes > 0, 1, 0)) AS FarmerUseChemicalFertYes,
                      SUM(IF(grup_farmer.FarmerUseChemicalFertNo > 0, 1, 0)) AS FarmerUseChemicalFertNo,
                      SUM(IF(grup_farmer.FarmerUseOrgFertYes > 0, 1, 0)) AS FarmerUseOrgFertYes,
                      SUM(IF(grup_farmer.FarmerUseOrgFertNo > 0, 1, 0)) AS FarmerUseOrgFertNo,
                      SUM(IF(grup_farmer.FarmerUseProtectGearYes > 0, 1, 0)) AS FarmerUseProtectGearYes,
                      SUM(IF(grup_farmer.FarmerUseProtectGearNo > 0, 1, 0)) AS FarmerUseProtectGearNo,
                      SUM(IF(grup_farmer.FarmerHandPestBotSafeYes > 0, 1, 0)) AS FarmerHandPestBotSafeYes,
                      SUM(IF(grup_farmer.FarmerHandPestBotSafeNo > 0, 1, 0)) AS FarmerHandPestBotSafeNo,
                      SUM(IF(grup_farmer.FarmerStorePestSafeYes > 0, 1, 0)) AS FarmerStorePestSafeYes,
                      SUM(IF(grup_farmer.FarmerStorePestSafeNo > 0, 1, 0)) AS FarmerStorePestSafeNo
                   FROM
                     (SELECT km.`MemberID` AS MemberID,
                             kp.ProvinceID AS ProvinceID,
                             kd.DistrictID AS DistrictID,
                             ks.SubDistrictID AS SubDistrictID,
                             kapm.apmPartnerID AS PartnerID,
                             SUM(IF(p.`PeUsingFungicide` = 1
                                    OR p.`PeUsingInsecticide` = 1
                                    OR p.`PeUsingFungicide` = 1, 1, 0)) AS FarmerUsePesYes,
                             SUM(IF(p.`PeUsingFungicide` = 2
                                    AND p.`PeUsingInsecticide` = 2
                                    AND p.`PeUsingFungicide` = 2, 1, 0)) AS FarmerUsePesNo,
                             SUM(IF((p.`PeUsingInsecticide` = 1
                                     AND p.`PeInsec23` = 1)
                                    OR (p.`PeUsingFungicide` = 1
                                        AND p.`PeFungi12` = 1) , 1, 0)) AS FarmerUseOrgPestYes,
                             SUM(IF((p.`PeUsingHerbicide` = 1)
                                    OR (p.`PeUsingInsecticide` = 1
                                        AND p.`PeInsec23` IS NULL)
                                    OR (p.`PeUsingFungicide` = 1
                                        AND p.`PeFungi12` IS NULL) , 1, 0)) AS FarmerUseOrgPestNo,
                             SUM(IF(p.FertNonOrganicData = 1, 1, 0)) AS FarmerUseChemicalFertYes,
                             SUM(IF(p.FertNonOrganicData = 2, 1, 0)) AS FarmerUseChemicalFertNo,
                             SUM(IF(p.FertUseOrganic = 1, 1, 0)) AS FarmerUseOrgFertYes,
                             SUM(IF(p.FertUseOrganic = 2, 1, 0)) AS FarmerUseOrgFertNo,
                             SUM(IF(p.UseProtectiveGear = 1, 1, 0)) AS FarmerUseProtectGearYes,
                             SUM(IF(p.UseProtectiveGear = 2, 1, 0)) AS FarmerUseProtectGearNo,
                             SUM(IF(p.PestPackageAfterUse = 3
                                    OR p.PestPackageAfterUse = 4
                                    OR p.PestPackageAfterUse = 5, 1, 0)) AS FarmerHandPestBotSafeYes,
                             SUM(IF(p.PestPackageAfterUse = 1
                                    OR p.PestPackageAfterUse = 2, 1, 0)) AS FarmerHandPestBotSafeNo,
                             SUM(IF(p.PestStoreLocation = 2
                                    OR p.PestStoreLocation = 3
                                    OR p.PestStoreLocation = 4, 1, 0)) AS FarmerStorePestSafeYes,
                             SUM(IF(p.PestStoreLocation = 1, 1, 0)) AS FarmerStorePestSafeNo
                        FROM
                             ktv_survey_plot p

                             JOIN
                               (SELECT p.MemberID,
                                       p.PlotNr,
                                       MAX(p.SurveyNr) AS SurveyNr
                                FROM ktv_survey_plot p
                                WHERE p.`StatusCode` = 'active'
                                GROUP BY p.MemberID,
                                         p.PlotNr
                                ) z ON p.MemberID = z.MemberID
                                AND p.PlotNr = z.PlotNr
                                AND p.SurveyNr = z.SurveyNr

                             
                             JOIN ktv_members km ON km.MemberID = p.MemberID
                             JOIN ktv_member_role kr ON km.MemberID = kr.MemberID
                             JOIN ktv_access_partner_member kapm ON kapm.`apmMemberID` = kr.`MemberID`
                                AND kr.MRoleID = 1 #ROLE PETANI
                             JOIN ktv_program_partner kpp ON kpp.`PartnerID` = kapm.`apmPartnerID`
                             
                             JOIN ktv_village kv ON kv.`VillageID` = km.`VillageID`
                             JOIN ktv_subdistrict ks ON ks.`SubDistrictID` = kv.`SubDistrictID`
                             JOIN ktv_district kd ON kd.`DistrictID` = ks.`DistrictID`
                             JOIN ktv_province kp ON kp.`ProvinceID` = kd.`ProvinceID`

                        WHERE
                            km.`StatusCode` = 'active'
                            AND km.`VillageID` IS NOT NULL
                            AND km.VillageID != ''
                            AND km.VillageID != '0'
                            AND kpp.`StatusCode` = 'active'
                            AND kpp.IsGenDashboard = 'Yes'
                            AND p.`SurveyNr` IS NOT NULL
                        GROUP BY 
                            km.MemberID,
                            kp.ProvinceID,
                            kd.DistrictID,
                            ks.SubDistrictID,
                            kpp.PartnerID
                    ) grup_farmer
                   GROUP BY 
                      grup_farmer.ProvinceID,
                      grup_farmer.DistrictID,
                      grup_farmer.SubDistrictID,
                      grup_farmer.PartnerID
                   ) AS calc_farmer_garden ON calc_garden.PartnerID = calc_farmer_garden.PartnerID 
                        AND calc_farmer_garden.SubDistrictID = calc_garden.SubDistrictID";
        $query = $this->db->query($sql);

        if ($this->db->trans_status() === FALSE) {
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

    public function getDisplayAgriInputOptimize($ProvinceID,$DistrictID){
        //data display langsung ================================================== (begin)

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

        $sql="SELECT
                SUM(TotalPlot) AS TotalPlot
                , SUM(IFNULL(CompostKg,0)) / SUM(IFNULL(CompostKgPerHaPembagi,0)) AS CompostKgPerHa
                , SUM(IFNULL(FertKg,0)) / SUM(IFNULL(FertKgPerHaPembagi,0)) AS FertKg
                , SUM(FarmerUsePesYes) / SUM(FarmerUsePesYes + FarmerUsePesNo) * 100 AS FarmerUsePes
                , SUM(FarmerUseOrgPestYes) / SUM(FarmerUseOrgPestYes + FarmerUseOrgPestNo) * 100 AS FarmerUseOrgPest
                , SUM(FarmerUseChemicalFertYes) / SUM(FarmerUseChemicalFertYes + FarmerUseChemicalFertNo) * 100 AS FarmerUseChemicalFert
                , SUM(FarmerUseOrgFertYes) / SUM(FarmerUseOrgFertYes + FarmerUseOrgFertNo) * 100 AS FarmerUseOrgFert
                , SUM(FarmerUseProtectGearYes) / SUM(FarmerUseProtectGearYes + FarmerUseProtectGearNo) * 100 AS FarmerUseProtectGear
                , SUM(FarmerHandPestBotSafeYes) / SUM(FarmerHandPestBotSafeYes + FarmerHandPestBotSafeNo) * 100 AS FarmerHandPestBotSafe
                , SUM(FarmerStorePestSafeYes) / SUM(FarmerStorePestSafeYes + FarmerStorePestSafeNo) * 100 AS FarmerStorePestSafe

                , IFNULL(SUM(FertAppUrea),0) AS FertAppUrea
                , IFNULL(SUM(FertAppSS),0) AS FertAppSS
                , IFNULL(SUM(FertAppNPK),0) AS FertAppNPK
                , IFNULL(SUM(FertAppTSP),0) AS FertAppTSP
                , IFNULL(SUM(FertAppCU),0) AS FertAppCU
                , IFNULL(SUM(FertAppKCL),0) AS FertAppKCL
                , IFNULL(SUM(FertAppNPKMuti),0) AS FertAppNPKMuti
                , IFNULL(SUM(FertAppBorat),0) AS FertAppBorat
                , IFNULL(SUM(FertAppDolomite),0) AS FertAppDolomite

                , IFNULL(SUM(DisBlast),0) AS DisBlast
                , IFNULL(SUM(DisGeno),0) AS DisGeno
                , IFNULL(SUM(DisSteam),0) AS DisSteam
                , IFNULL(SUM(DisBud),0) AS DisBud
                , IFNULL(SUM(DisSpear),0) AS DisSpear
                , IFNULL(SUM(DisYellow),0) AS DisYellow
                , IFNULL(SUM(DisAnt),0) AS DisAnt
                , IFNULL(SUM(DisCrown),0) AS DisCrown
                , IFNULL(SUM(DisViscular),0) AS DisViscular
                , IFNULL(SUM(DisBunch),0) AS DisBunch

                , IFNULL(SUM(PestRats),0) AS PestRats
                , IFNULL(SUM(PestOly),0) AS PestOly
                , IFNULL(SUM(PestSatora),0) AS PestSatora
                , IFNULL(SUM(PestTira),0) AS PestTira
                , IFNULL(SUM(PestRhino),0) AS PestRhino
                , IFNULL(SUM(PestElep),0) AS PestElep
                , IFNULL(SUM(PestOrgUtan),0) AS PestOrgUtan
                , IFNULL(SUM(PestLandak),0) AS PestLandak
                , IFNULL(SUM(PestBabi),0) AS PestBabi

                , IFNULL(SUM(HerbiYes),0) AS HerbiYes
                , IFNULL(SUM(HerbiNo),0) AS HerbiNo
                , IFNULL(SUM(InsecYes),0) AS InsecYes
                , IFNULL(SUM(InsecNo),0) AS InsecNo
                , IFNULL(SUM(FungiYes),0) AS FungiYes
                , IFNULL(SUM(FungiNo),0) AS FungiNo

                , SUM(IFNULL(HerbiParaquat,0)) / SUM(IFNULL(HerbiYes,0)) * 100 AS HerbiParaquat
                , SUM(IFNULL(HerbiGlyphosate,0)) / SUM(IFNULL(HerbiYes,0)) * 100 AS HerbiGlyphosate
                , SUM(IFNULL(HerbiAllowed,0)) / SUM(IFNULL(HerbiYes,0)) * 100 AS HerbiAllowed

                , SUM(IFNULL(InsecBanned,0)) / SUM(IFNULL(InsecYes,0)) * 100 AS InsecBanned
                , SUM(IFNULL(InsecWatchlist,0)) / SUM(IFNULL(InsecYes,0)) * 100 AS InsecWatchlist
                , SUM(IFNULL(InsecAllowed,0)) / SUM(IFNULL(InsecYes,0)) * 100 AS InsecAllowed

                , SUM(IFNULL(FungiBanned,0)) / SUM(IFNULL(FungiYes,0)) * 100 AS FungiBanned
                , SUM(IFNULL(FungiWatchlist,0)) / SUM(IFNULL(FungiYes,0)) * 100 AS FungiWatchlist
                , SUM(IFNULL(FungiAllowed,0)) / SUM(IFNULL(FungiYes,0)) * 100 AS FungiAllowed

                , DateGenerated
            FROM
                dash_det_agri_input a
            WHERE
                1 = 1
                $sqldWherePropinsi
                $sqldWhereDistrict
                $sqlHakAkses
            ";
        $query = $this->db->query($sql,array());
        $result['dataDisplay'] = $query->row_array();

        //data display langsung ================================================== (end)


        //data group by wilayah (untuk chart) ============================================= (begin)

        if($ProvinceID == ""){
            $sqlcLabel = "Province";
            $sqlcJoin = "JOIN ktv_province prov ON prov.`ProvinceID` = a.ProvinceID";
            $sqlcWhere = "";
        } elseif($DistrictID == "") {
            $sqlcLabel = "District";
            $sqlcJoin = "JOIN ktv_district dis ON dis.`DistrictID` = a.DistrictID";
            $sqlcWhere = "AND a.ProvinceID = '$ProvinceID'";
        } else {
            $sqlcLabel = "SubDistrict";
            $sqlcJoin = "JOIN ktv_subdistrict subdis ON subdis.`SubDistrictID` = a.SubDistrictID";
            $sqlcWhere = "AND a.DistrictID = '$DistrictID'";
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

        $sql="
            SELECT
                $sqlcLabel AS label
                , SUM(IFNULL(CompostKg,0)) / SUM(IFNULL(CompostKgPerHaPembagi,0)) AS CompostKgPerHa
                , SUM(IFNULL(FertKg,0)) / SUM(IFNULL(FertKgPerHaPembagi,0)) AS FertKg

                , IFNULL(SUM(CompAppPBA),0) AS CompAppPBA
                , IFNULL(SUM(CompAppPB),0) AS CompAppPB
                , IFNULL(SUM(CompAppFromPB),0) AS CompAppFromPB
                , IFNULL(SUM(CompAppManure),0) AS CompAppManure

                , IFNULL(SUM(FertAppTreeTBM),0) AS FertAppTreeTBM
                , IFNULL(SUM(FertAppTreeTM),0) AS FertAppTreeTM
                , IFNULL(SUM(FertAppTreeTR),0) AS FertAppTreeTR

                , IFNULL(SUM(FertAppUrea),0) AS FertAppUrea
                , IFNULL(SUM(FertAppSS),0) AS FertAppSS
                , IFNULL(SUM(FertAppNPK),0) AS FertAppNPK
                , IFNULL(SUM(FertAppTSP),0) AS FertAppTSP
                , IFNULL(SUM(FertAppCU),0) AS FertAppCU
                , IFNULL(SUM(FertAppKCL),0) AS FertAppKCL
                , IFNULL(SUM(FertAppNPKMuti),0) AS FertAppNPKMuti
                , IFNULL(SUM(FertAppBorat),0) AS FertAppBorat
                , IFNULL(SUM(FertAppDolomite),0) AS FertAppDolomite

                , IFNULL(SUM(DisBlast),0) AS DisBlast
                , IFNULL(SUM(DisGeno),0) AS DisGeno
                , IFNULL(SUM(DisSteam),0) AS DisSteam
                , IFNULL(SUM(DisBud),0) AS DisBud
                , IFNULL(SUM(DisSpear),0) AS DisSpear
                , IFNULL(SUM(DisYellow),0) AS DisYellow
                , IFNULL(SUM(DisAnt),0) AS DisAnt
                , IFNULL(SUM(DisCrown),0) AS DisCrown
                , IFNULL(SUM(DisViscular),0) AS DisViscular
                , IFNULL(SUM(DisBunch),0) AS DisBunch

                , IFNULL(SUM(PestRats),0) AS PestRats
                , IFNULL(SUM(PestOly),0) AS PestOly
                , IFNULL(SUM(PestSatora),0) AS PestSatora
                , IFNULL(SUM(PestTira),0) AS PestTira
                , IFNULL(SUM(PestRhino),0) AS PestRhino
                , IFNULL(SUM(PestElep),0) AS PestElep
                , IFNULL(SUM(PestOrgUtan),0) AS PestOrgUtan
                , IFNULL(SUM(PestLandak),0) AS PestLandak
                , IFNULL(SUM(PestBabi),0) AS PestBabi

                , IFNULL(SUM(PestUsageInHh),0) AS PestUsageInHh
                , IFNULL(SUM(PestUsageSpecPlace),0) AS PestUsageSpecPlace
                , IFNULL(SUM(PestUsageOutHouse),0) AS PestUsageOutHouse
                , IFNULL(SUM(PestUsageOutFarm),0) AS PestUsageOutFarm
                , IFNULL(SUM(PestUsageOther),0) AS PestUsageOther

                , IFNULL(SUM(PestPackRandom),0) AS PestPackRandom
                , IFNULL(SUM(PestPackSomeElse),0) AS PestPackSomeElse
                , IFNULL(SUM(PestPackBurry),0) AS PestPackBurry
                , IFNULL(SUM(PestPackBurn),0) AS PestPackBurn
                , IFNULL(SUM(PestPackRecycle),0) AS PestPackRecycle
                , IFNULL(SUM(PestPackOther),0) AS PestPackOther

                , IFNULL(SUM(HerbiYes),0) AS HerbiYes
                , IFNULL(SUM(HerbiNo),0) AS HerbiNo
                , IFNULL(SUM(InsecYes),0) AS InsecYes
                , IFNULL(SUM(InsecNo),0) AS InsecNo
                , IFNULL(SUM(FungiYes),0) AS FungiYes
                , IFNULL(SUM(FungiNo),0) AS FungiNo

                , IFNULL(SUM(FarmerUseProtectGearYes),0) AS FarmerUseProtectGearYes
                , IFNULL(SUM(FarmerUseProtectGearNo),0) AS FarmerUseProtectGearNo
            FROM
                dash_det_agri_input a
                $sqlcJoin
            WHERE
                1 = 1
                $sqlcWhere
                $sqlcHakAkses
            GROUP BY label
            ORDER BY label
        ";
        $query = $this->db->query($sql,array());
        $result['dataChart'] = $query->result_array();
        //data group by wilayah (untuk chart) ============================================= (end)

        return $result;
    }

}
?>