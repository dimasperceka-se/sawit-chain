<?php
/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Thu Aug 08 2019
 *  File : mpro_bluenumber_sdg.php
 *******************************************/
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mpro_bluenumber_sdg extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    public function getDisplay($ProvinceID, $DistrictID) {
        $result = array();

        //data display langsung ================================================== (begin)
        if($ProvinceID != ""){
            $sqldWherePropinsi = " AND p.ProvinceID = '$ProvinceID' ";
        }else{
            $sqldWherePropinsi = "";
        }

        if($DistrictID != ""){
            $sqldWhereDistrict = " AND d.DistrictID = '$DistrictID' ";
        }else{
            $sqldWhereDistrict = "";
        }

        //buat SqlHakAksesKontrol (begin)
        /*if($_SESSION['is_admin'] == "1"){
            $sqlHakAkses = " AND pp.PartnerID = '1' #Partner Koltiva ";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            //cek ktv_access_staff
            $sqlHakAkses = " AND d.DistrictID IN (".$_SESSION['daerah_access'].")";
            $sqlHakAkses .= " AND pp.PartnerID = '{$_SESSION['PartnerID']}' ";
        } else {
            //cek ktv_access_staff
            $sqlHakAkses = " AND SUBSTR(a.VillageID,1,4) IN (".$_SESSION['daerah_access'].")";
            $sqlHakAkses .= " AND pp.PartnerID = '1' #Partner Koltiva ";
        }*/

        //Tidak usah pakai hak akses
        $sqlHakAkses = " AND pp.PartnerID = '1' #Partner Koltiva ";

        //buat SqlHakAksesKontrol (end)

        $sql = "SELECT
                    SUM(mgrup.TotalFarmers) AS TotalFarmers
                    , SUM(mgrup.TotalFarmers) AS ConsentLetterSigned
                    , SUM(mgrup.GardenTotal) AS GardenTotal
                    , SUM(mgrup.GardenTotalHa) AS GardenTotalHa
                    , SUM(mgrup.Family) AS Family
                    , SUM(mgrup.Workers) AS Workers
                    , SUM(mgrup.SdgScore) / COUNT(mgrup.DistrictID) AS SdgScore
                FROM
                (
                SELECT
                    p.ProvinceID
                    , d.DistrictID
                    , sd.SubDistrictID
                    , pp.PartnerID
                    , COUNT(m.`MemberID`) AS TotalFarmers
                    , IFNULL(SUM(IF(m.`LearningContractStatus`='1',1,0)),0) AS ConsentLetterSigned
                    , IFNULL(SUM(gar.GardenTotal),0) AS GardenTotal
                    , IFNULL(SUM(gar.GardenTotalHa),0) AS GardenTotalHa
                    , IFNULL(SUM(f.family),0) AS Family
                    , IFNULL(SUM(lab.lab_workers),0) AS Workers
                    , IFNULL(SUM(sdg.SdgScore),0) / COUNT(m.`MemberID`) AS SdgScore
                FROM
                    ktv_members m
                    JOIN ktv_member_role r ON m.MemberID = r.MemberID AND r.MRoleID = 1 #ROLE PETANI
                    LEFT JOIN ktv_village v ON v.VillageID = m.VillageID
                    LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
                    LEFT JOIN ktv_district d ON d.DistrictID = sd.DistrictID
                    LEFT JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
                    JOIN ktv_access_partner_member acc_pm ON m.MemberID = acc_pm.apmMemberID
                    JOIN ktv_program_partner pp ON pp.PartnerID = acc_pm.apmPartnerID AND pp.`IsGenDashboard` = 'Yes'
                    JOIN (
                        SELECT
                            sdg.`MemberID`
                            , ( IFNULL(AlternativeJobs,0) +
                            IFNULL(DailyNeedsAreMet,0) +
                            IFNULL(HouseholdMalnourished,0) +
                            IFNULL(HouseholdDied,0) +
                            IFNULL(BirthFamilyAssisted,0) +
                            IFNULL(ChildrenForceDropOut,0) +
                            IFNULL(FamilyVocationalTraining,0) +
                            IFNULL(SafeWomanToLive,0) +
                            IFNULL(ReceiveAssistanceFromGov,0) +
                            IFNULL(AccessCleanDrinkingWater,0) +
                            IFNULL(AccessToRunningWater,0) +
                            IFNULL(HaveElectricityAtHome,0) +
                            IFNULL(EnergySourceForCooking,0) +
                            IFNULL(UseBankServices,0) +
                            IFNULL(HaveInsurance,0) +
                            IFNULL(HaveInternetAccess,0) +
                            IFNULL(HowLongTravelToBuyEquipment,0) +
                            IFNULL(HasIncomeIncreased,0) +
                            IFNULL(ReplantingHigherYield,0) +
                            IFNULL(WeatherAffectLivelihoods,0) +
                            IFNULL(ReceiveAdviceForDisaster,0) +
                            IFNULL(ThrowGarbageIntoRiver,0) +
                            IFNULL(PlantTreesOnYourLand,0) +
                            IFNULL(BelieveLawEnforcement,0) +
                            IFNULL(KnowSustainableHelpEnvironment,0) +
                            IFNULL(PracticeUseFertilizer,0) +
                            IFNULL(HowDoYouSellFFB,0) +
                            IFNULL(FreqSellFFBAgentToMill,0) +
                            IFNULL(RegularFFBPostHarvest,0) +
                            IFNULL(HouseholdFreeAbuse,0) +
                            IFNULL(HomeProtectFromWeather,0) +
                            IFNULL(DoJusticeSystemFair,0) +
                            IFNULL(HowLodgeFormalComplaint,0) +
                            IFNULL(HaveCropInsurance,0) +
                            IFNULL(HouseholdCompleteImmunization,0) ) / 35 AS SdgScore
                        FROM
                            ktv_survey_sdg sdg
                            LEFT JOIN (
                                SELECT
                                    lsdg.MemberID
                                    , MAX(lsdg.SurveyNr) AS SurveyNr
                                FROM
                                    ktv_survey_sdg lsdg
                                WHERE
                                    lsdg.StatusCode = 'active'
                                    AND lsdg.StatusVerified = 'Yes'
                                GROUP BY lsdg.MemberID
                            ) AS lsdg ON 1=1
                                AND sdg.MemberID = lsdg.MemberID
                        WHERE
                            sdg.StatusCode = 'active'
                            AND sdg.StatusVerified = 'Yes'
                        GROUP BY sdg.`MemberID`
                    ) AS sdg ON m.`MemberID` = sdg.MemberID
                    LEFT JOIN (
                        SELECT
                            a.`MemberID`
                            , IFNULL(COUNT(a.PlotNr),0) AS GardenTotal
                            , IFNULL(SUM(a.GardenAreaHa),0) AS GardenTotalHa
                        FROM
                            ktv_survey_plot a
                            JOIN (SELECT
                                p.MemberID, p.PlotNr, MAX(p.SurveyNr) AS SurveyNr
                            FROM ktv_survey_plot p WHERE p.`StatusCode` = 'active'
                            GROUP BY p.MemberID, p.PlotNr) AS gar_latest
                                ON a.MemberID = gar_latest.MemberID AND a.PlotNr = gar_latest.PlotNr AND a.SurveyNr = gar_latest.SurveyNr
                            JOIN ktv_members m ON m.MemberID = a.MemberID
                        WHERE
                            a.`StatusCode` = 'active'
                            AND m.`StatusCode` = 'active'
                        GROUP BY a.`MemberID`
                    ) AS gar ON m.`MemberID` = gar.MemberID
                    LEFT JOIN (
                        SELECT
                            f.MemberID
                            , COUNT(f.FamLabID) AS family
                        FROM
                            ktv_member_family_labour f
                        WHERE
                            f.StatusCode = 'active'
                        GROUP BY f.MemberID
                    ) AS f ON m.MemberID = f.MemberID
                    LEFT JOIN (
                        SELECT
                            lab.`MemberID`
                            , COUNT(lab.`LaboID`) AS lab_workers
                        FROM
                            ktv_member_labour lab
                        WHERE
                            lab.`StatusCode` = 'active'
                        GROUP BY lab.`MemberID`
                    ) AS lab ON m.`MemberID` = lab.MemberID
                WHERE
                    m.`StatusCode` = 'active'
                    $sqldWherePropinsi
                    $sqldWhereDistrict
                    $sqlHakAkses
                GROUP BY
                    p.ProvinceID
                    , d.DistrictID
                    , sd.SubDistrictID
                    , pp.PartnerID
                ) AS mgrup";
        $query = $this->db->query($sql,array());
        $result['dataDisplay'] = $query->row_array();
        //data display langsung ================================================== (begin)

        return $result;
    }
}