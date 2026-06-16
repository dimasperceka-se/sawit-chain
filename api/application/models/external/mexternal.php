<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mexternal extends CI_Model {

    public function sme_garden(){
        
        $sql = "
        SELECT
            a.MemberID AS 'System ID',
            a.MemberDisplayID AS 'Farmer ID',
            a.MemberName AS 'Farmer Name',
            pp.PartnerName 'Nama Mill',
            b.PlotNr AS 'Plantation Nr',
            CONCAT( g.SurveyNr, ' - ', g.SurveyTxt ) AS 'Survey Nr',
            b.DateCollection AS 'Date Collection',
            f.Province AS 'Province',
            e.District AS 'District',
            d.SubDistrict AS 'Subdistrict',
            c.Village AS 'Village',
            b.GardenAreaHa AS 'Area of Plantation(Ha)',
            b.GardenAreaPolygon AS 'Area of Garden Polygon (Ha)',
            b.Latitude AS 'Latitude',
            b.Longitude AS 'Longitude',
        CASE
                
                WHEN b.LandOwnershipType = 1 THEN
                'Owned' 
                WHEN b.LandOwnershipType = 2 THEN
                'Profit Sharing' 
                WHEN b.LandOwnershipType = 3 THEN
                'Rented' ELSE 'Others' 
            END AS 'Land Ownership',
        CASE
                
                WHEN b.OwnerOfTheGarden = 1 THEN
                'Registered Farmer' 
                WHEN b.OwnerOfTheGarden = 2 THEN
                'Family Members' 
                WHEN b.OwnerOfTheGarden = 3 THEN
                'Other People' ELSE 'Do Not Know ' 
            END AS 'Owner of the Garden',
            b.OwnerOfPlantationNameText AS 'Owner of this Plantation - Name',
            b.OwnerOfPlantationLocationText AS 'Owner of this Plantation - Location',
        CASE
                
                WHEN b.OwnershipDoc = 1 THEN
                'No Document' 
                WHEN b.OwnershipDoc = 2 THEN
                'SKT (Surat Keterangan Tanah)' 
                WHEN b.OwnershipDoc = 3 THEN
                'SHM (Sertifikat Hak Milik)/Certificate' 
                WHEN b.OwnershipDoc = 4 THEN
                'HGU (Hak Guna Usaha)' 
                WHEN b.OwnershipDoc = 5 THEN
                'SKGR (Surat Keterangan Ganti Rugi)' ELSE b.OwnershipDocText 
            END AS 'Ownership Document',
        IF
            ( b.OwnerDocIsOwner = 1, 'Yes', IF ( b.OwnerDocIsOwner = 2, 'No', 'Do Not Know' ) ) AS 'Is the ownership document in the name of the current owner',
        IF
            ( b.HaveSTDB = 1, 'Yes', IF ( b.HaveSTDB = 2, 'No', 'Do Not Know' ) ) AS 'Does the farm have a STD-B (operational / business letter)',
        IF
            ( b.HaveSPPL = 1, 'Yes', IF ( b.HaveSPPL = 2, 'No', 'Do Not Know' ) ) AS 'Does the farm have a SPPL (Environmental Management Letter)',
        IF
            ( b.BusinessModel = 1, 'Independence', IF ( b.BusinessModel = 2, 'Independent - Ex Plasma', 'Plasma(has existing contract with plantation)' ) ) AS 'Business Model',
        CASE
                
                WHEN b.HowObPlantation = 1 THEN
                'Inheritance' 
                WHEN b.HowObPlantation = 2 THEN
                'Purchased' 
                WHEN b.HowObPlantation = 3 THEN
                'Convert Existing Plantation' 
                WHEN b.HowObPlantation = 4 THEN
                'Received From Government (Transmigrate)' ELSE b.HowObPlantationText 
            END AS 'How did you obtain the plantation',
        CASE
                
                WHEN b.PlantationConditionEst = 2 THEN
                'Secondary Veg/Fallow' 
                WHEN b.PlantationConditionEst = 2 THEN
                'Food Crops' 
                WHEN b.PlantationConditionEst = 3 THEN
                'Mangrove' 
                WHEN b.PlantationConditionEst = 4 THEN
                'Other Plantation (rubber, coffee, etc)' 
                WHEN b.PlantationConditionEst = 5 THEN
                'Oil Palm Plantation' 
                WHEN b.PlantationConditionEst = 6 THEN
                'Forest' ELSE 'I dont know' 
            END AS 'Condition when establishing oil palm plantation',
            b.AverageAgeTree AS 'Average age of trees on plantation (years)',
        IF
            (
                b.SoilType = 1,
                'Mineral',
            IF
                ( b.SoilType = 2, 'Peat', IF ( b.SoilType = 3, 'Sandy', '-' ) ) 
            ) AS 'Soil Type',
        IF
            (
                b.TopographyType = 1,
                'Flat',
            IF
                ( b.TopographyType = 2, 'Hilly', IF ( b.TopographyType = 3, 'Mountainous', '-' ) ) 
            ) AS 'Type of Topography Plantation',
            b.FirstPlantingYear AS 'Year of first planting palm trees',
            b.TreeTBM AS 'TBM - Plants yet to produce',
            b.TreeTM AS 'TM - Producing plants',
            b.TreeTR AS 'TR - Old/diseased',
            b.TreeTBM + b.TreeTM + b.TreeTR AS 'Total Number of Trees',
        IF
            ( b.TypePlantMateMarihat = 1, 'Yes', 'No' ) AS 'Type of Planting Material - Marihat',
            b.TypePlantMateMarihatNr AS 'Type of Planting Material - Marihat Number Of Trees',
        IF
            ( b.TypePlantMateDumpy = 1, 'Yes', 'No' ) AS 'Type of Planting Material - Dumpy',
            b.TypePlantMateDumpyNr AS 'Type of Planting Material - Dumpy Number Of Trees',
        IF
            ( b.TypePlantMateLonsum = 1, 'Yes', 'No' ) AS 'Type of Planting Material - Lonsum',
            b.TypePlantMateLonsumNr AS 'Type of Planting Material - Lonsum Number Of Trees',
        IF
            ( b.TypePlantMateSimalungun = 1, 'Yes', 'No' ) AS 'Type of Planting Material - Simalungun',
            b.TypePlantMateSimalungunNr AS 'Type of Planting Material - Simalungun Number Of Trees',
        IF
            ( b.TypePlantMateDanimas = 1, 'Yes', 'No' ) AS 'Type of Planting Material - Dami Mas',
            b.TypePlantMateDanimasNr AS 'Type of Planting Material - Dami Mas Number Of Trees',
        IF
            ( b.TypePlantMateSriwijaya = 1, 'Yes', 'No' ) AS 'Type of Planting Material - Sriwijaya',
            b.TypePlantMateSriwijayaNr AS 'Type of Planting Material - Sriwijaya Number Of Trees',
        IF
            ( b.TypePlantMateSocfin = 1, 'Yes', 'No' ) AS 'Type of Planting Material - Socfin',
            b.TypePlantMateSocfinNr AS 'Type of Planting Material - Socfin Number Of Trees',
        IF
            ( b.TypePlantMateOther = 1, b.TypePlantMateOtherText, '-' ) AS 'Type of Planting Material - Other Method',
            b.TypePlantMateOtherNr AS 'Type of Planting Material - Other Method Number Of Trees',
        IF
            ( b.TypePlantMateDoNotKnow = 1, 'Yes', 'No' ) AS 'Type of Planting Material - Do Not Know',
            b.TypePlantMateDoNotKnowNr AS 'Type of Planting Material - Do Not Know Number Of Trees',
            b.TreeTBM + b.TreeTM + b.TreeTR AS 'Total Number of Oil Palm Trees',
            b.HarvestRateDaysHighSeason AS 'Harvest rate (once every how many days) in high season',
            b.HarvestRateDaysLowSeason AS 'Harvest rate (once every how many days) in low season',
            b.AverageProdHighSeason AS 'Average production per harvest (ton) in high season',
            b.AverageProdLowSeason AS 'Average production per harvest (ton) in low season',
            12 - (
                IFNULL( CAST( b.LeanHarvestSeasonJan AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonFeb AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonMar AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonApr AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonMay AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonJun AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonJul AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonAug AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonSep AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonOct AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonNov AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonDec AS UNSIGNED ), 0 ) 
            ) AS 'Number of Months in High Season',
            (
                IFNULL( CAST( b.LeanHarvestSeasonJan AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonFeb AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonMar AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonApr AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonMay AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonJun AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonJul AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonAug AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonSep AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonOct AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonNov AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonDec AS UNSIGNED ), 0 ) 
            ) AS 'Number of Months in Low Season',
            ( 30 / b.HarvestRateDaysHighSeason ) * b.AverageProdHighSeason * (
                12 - (
                    IFNULL( CAST( b.LeanHarvestSeasonJan AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonFeb AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonMar AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonApr AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonMay AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonJun AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonJul AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonAug AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonSep AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonOct AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonNov AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonDec AS UNSIGNED ), 0 ) 
                ) 
            ) AS 'High Season Production (ton)',
            ( 30 / b.HarvestRateDaysLowSeason ) * b.AverageProdLowSeason * (
                (
                    IFNULL( CAST( b.LeanHarvestSeasonJan AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonFeb AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonMar AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonApr AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonMay AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonJun AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonJul AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonAug AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonSep AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonOct AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonNov AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonDec AS UNSIGNED ), 0 ) 
                ) 
            ) AS 'Low Season Production (ton)',
            (
                ( 30 / b.HarvestRateDaysHighSeason ) * b.AverageProdHighSeason * (
                    12 - (
                        IFNULL( CAST( b.LeanHarvestSeasonJan AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonFeb AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonMar AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonApr AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonMay AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonJun AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonJul AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonAug AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonSep AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonOct AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonNov AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonDec AS UNSIGNED ), 0 ) 
                    ) 
                ) 
                ) + (
                ( 30 / b.HarvestRateDaysLowSeason ) * b.AverageProdLowSeason * (
                    (
                        IFNULL( CAST( b.LeanHarvestSeasonJan AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonFeb AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonMar AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonApr AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonMay AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonJun AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonJul AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonAug AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonSep AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonOct AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonNov AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonDec AS UNSIGNED ), 0 ) 
                    ) 
                ) 
            ) AS 'Annual Production (ton)',
            (
                (
                    ( 30 / b.HarvestRateDaysHighSeason ) * b.AverageProdHighSeason * (
                        12 - (
                            IFNULL( CAST( b.LeanHarvestSeasonJan AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonFeb AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonMar AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonApr AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonMay AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonJun AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonJul AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonAug AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonSep AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonOct AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonNov AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonDec AS UNSIGNED ), 0 ) 
                        ) 
                    ) 
                    ) + (
                    ( 30 / b.HarvestRateDaysLowSeason ) * b.AverageProdLowSeason * (
                        (
                            IFNULL( CAST( b.LeanHarvestSeasonJan AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonFeb AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonMar AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonApr AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonMay AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonJun AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonJul AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonAug AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonSep AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonOct AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonNov AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonDec AS UNSIGNED ), 0 ) 
                        ) 
                    ) 
                ) 
            ) / b.GardenAreaHa AS 'Plantation Productivity (ton/ha)',
        IF
            ( b.LeanHarvestSeasonJan = 1, 'Yes', 'No' ) AS 'When is the lean harvest season for oil palm in your area - January',
        IF
            ( b.LeanHarvestSeasonFeb = 1, 'Yes', 'No' ) AS 'When is the lean harvest season for oil palm in your area - February',
        IF
            ( b.LeanHarvestSeasonMar = 1, 'Yes', 'No' ) AS 'When is the lean harvest season for oil palm in your area - March',
        IF
            ( b.LeanHarvestSeasonApr = 1, 'Yes', 'No' ) AS 'When is the lean harvest season for oil palm in your area - April',
        IF
            ( b.LeanHarvestSeasonMay = 1, 'Yes', 'No' ) AS 'When is the lean harvest season for oil palm in your area - May',
        IF
            ( b.LeanHarvestSeasonJun = 1, 'Yes', 'No' ) AS 'When is the lean harvest season for oil palm in your area - June',
        IF
            ( b.LeanHarvestSeasonJul = 1, 'Yes', 'No' ) AS 'When is the lean harvest season for oil palm in your area - July',
        IF
            ( b.LeanHarvestSeasonAug = 1, 'Yes', 'No' ) AS 'When is the lean harvest season for oil palm in your area - August',
        IF
            ( b.LeanHarvestSeasonSep = 1, 'Yes', 'No' ) AS 'When is the lean harvest season for oil palm in your area - September',
        IF
            ( b.LeanHarvestSeasonOct = 1, 'Yes', 'No' ) AS 'When is the lean harvest season for oil palm in your area - October',
        IF
            ( b.LeanHarvestSeasonNov = 1, 'Yes', 'No' ) AS 'When is the lean harvest season for oil palm in your area - November',
        IF
            ( b.LeanHarvestSeasonDec = 1, 'Yes', 'No' ) AS 'When is the lean harvest season for oil palm in your area - December',
        IF
            ( b.WhoHarvestFamily = 1, 'Yes', 'No' ) AS 'Who does the harvesting - Respondent and/or Family member',
        IF
            ( b.WhoHarvestLabor = 1, 'Yes', 'No' ) AS 'Who does the harvesting - Use of Hired Labor',
        CASE
                
                WHEN b.HowManyDiffBuyerSoldLastYear = 1 THEN
                '1' 
                WHEN b.HowManyDiffBuyerSoldLastYear = 1 THEN
                '2' 
                WHEN b.HowManyDiffBuyerSoldLastYear = 1 THEN
                '3' 
                WHEN b.HowManyDiffBuyerSoldLastYear = 1 THEN
                '4' ELSE 'More than 4' 
            END AS 'To how many different buyers have you sold your FFB from this plantation to within the past year',
        CASE
                
                WHEN b.HowManyDiffMillSoldLastYear = 1 THEN
                '1' 
                WHEN b.HowManyDiffMillSoldLastYear = 1 THEN
                '2' 
                WHEN b.HowManyDiffMillSoldLastYear = 1 THEN
                '3' 
                WHEN b.HowManyDiffMillSoldLastYear = 1 THEN
                '4' ELSE 'More than 4' 
            END AS 'How many different palm oil mills have you sold your FFB to within the past year',
            b.COMMENT AS 'Any comments about plantation',
        IF
            ( b.FertNonOrganicData = 1, 'Yes', IF ( b.FertNonOrganicData = 2, 'No', '-' ) ) AS 'Non Organic / Chemical Fertilizing Data',
            b.FertMoneySpentNonOrganic AS 'How much money did you spend in the past 24 months on Chemical Fertilizer',
            b.FertUreaTimesYear AS 'Urea - Times/Year',
            b.FertUreaDose AS 'Urea - Dose/Plot/Times per Year',
            b.FertUreaTimesYear * b.FertUreaDose AS 'Urea - Dose/Plot/Year',
            b.FertSSTimesYear AS 'SS - Times/Year',
            b.FertSSDose AS 'SS - Dose/Plot/Times per Year',
            b.FertSSTimesYear * b.FertSSDose AS 'SS - Dose/Plot/Year',
            b.FertNPKTimesYear AS 'NPK - Times/Year',
            b.FertNPKDose AS 'NPK - Dose/Plot/Times per Year',
            b.FertNPKTimesYear * b.FertNPKDose AS 'NPK - Dose/Plot/Year',
            b.FertTSPTimesYear AS 'TSP - Times/Year',
            b.FertTSPDose AS 'TSP - Dose/Plot/Times per Year',
            b.FertTSPTimesYear * b.FertTSPDose AS 'TSP - Dose/Plot/Year',
            b.FertCUTimesYear AS 'CU - Times/Year',
            b.FertCUDose AS 'CU - Dose/Plot/Times per Year',
            b.FertCUTimesYear * b.FertCUDose AS 'CU - Dose/Plot/Year',
            b.FertKCLTimesYear AS 'KCL - Times/Year',
            b.FertKCLDose AS 'KCL - Dose/Plot/Times per Year',
            b.FertKCLTimesYear * b.FertKCLDose AS 'KCL - Dose/Plot/Year',
            b.FertNPKMutiTimesYear AS 'NPK Mutiara - Times/Year',
            b.FertNPKMutiDose AS 'NPK Mutiara - Dose/Plot/Times per Year',
            b.FertNPKMutiTimesYear * b.FertNPKMutiDose AS 'NPK Mutiara - Dose/Plot/Year',
            b.FertBoratTimesYear AS 'Borat - Times/Year',
            b.FertBoratDose AS 'Borat - Dose/Plot/Times per Year',
            b.FertBoratTimesYear * b.FertBoratDose AS 'Borat - Dose/Plot/Year',
            b.FertDolomiteTimesYear AS 'Dolomite - Times/Year',
            b.FertDolomiteDose AS 'Dolomite - Dose/Plot/Times per Year',
            b.FertDolomiteTimesYear * b.FertDolomiteDose AS 'Dolomite - Dose/Plot/Year',
        IF
            ( b.FertWithNonOrgaTBM = 1, 'Yes', 'No' ) AS 'Which trees are fertilized with Non Organic/Chemical - singk TBM',
        IF
            ( b.FertWithNonOrgaTM = 1, 'Yes', 'No' ) AS 'Which trees are fertilized with Non Organic/Chemical - singk TM',
        IF
            ( b.FertWithNonOrgaTR = 1, 'Yes', 'No' ) AS 'Which trees are fertilized with Non Organic/Chemical - singk TR',
        IF
            ( b.FertUseOrganic = 1, 'Yes', IF ( b.FertUseOrganic = 2, 'No', '-' ) ) AS 'Do you use compost and/or organic fertilizer',
            b.FertMoneySpentOrganic AS 'How much money did you spend in the past 24 months on Compost and Organic Fertilizers',
            b.FertPBATimesYear AS 'Palm Bunch Ash - Times/Year',
            b.FertPBADose AS 'Palm Bunch Ash - Dose/Plot/Times per Year',
            b.FertPBATimesYear * b.FertPBADose AS 'Palm Bunch Ash - Dose/Plot/Year',
            b.FertPBTimesYear AS 'Palm Bunch - Times/Year',
            b.FertPBDose AS 'Palm Bunch - Dose/Plot/Times per Year',
            b.FertPBTimesYear * b.FertPBDose AS 'Palm Bunch - Dose/Plot/Year',
            b.FertCPBTimesYear AS 'Compost from Palm Bunch - Times/Year',
            b.FertCPBDose AS 'Compost from Palm Bunch - Dose/Plot/Times per Year',
            b.FertCPBTimesYear * b.FertCPBDose AS 'Compost from Palm Bunch - Dose/Plot/Year',
            b.FertManureTimesYear AS 'Manure - Times/Year',
            b.FertManureDose AS 'Manure - Dose/Plot/Times per Year',
            b.FertManureTimesYear * b.FertManureDose AS 'Manure - Dose/Plot/Year',
        IF
            ( b.FertWithOrgaTBM = 1, 'Yes', 'No' ) AS 'Which trees are fertilized using compost and/or Organic - TBM',
        IF
            ( b.FertWithOrgaTM = 1, 'Yes', 'No' ) AS 'Which trees are fertilized using compost and/or Organic - TM',
        IF
            ( b.FertWithOrgaTR = 1, 'Yes', 'No' ) AS 'Which trees are fertilized using compost and/or Organic - TR',
        IF
            ( b.PeUsingHerbicide = 1, 'Yes', 'No' ) AS 'Herbicide - Using Herbicide',
            b.PeMoneySpentHerbi AS 'Herbicide - How much money did you spend in the past 24 months',
            b.PeFreqHerbi AS 'Herbicide - Pesticides Frequency (Times/Year)',
        IF
            ( b.PeHerbi1 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Round Up',
        IF
            ( b.PeHerbi2 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Basmilang',
        IF
            ( b.PeHerbi3 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Pilar Up',
        IF
            ( b.PeHerbi4 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Sun Up',
        IF
            ( b.PeHerbi5 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Gramaxone',
        IF
            ( b.PeHerbi6 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Supremo',
        IF
            ( b.PeHerbi7 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Sapurata',
        IF
            ( b.PeHerbi8 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Rambo',
        IF
            ( b.PeHerbi9 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Para Special',
        IF
            ( b.PeHerbi10 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Noxone',
        IF
            ( b.PeHerbi11 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Paratop',
        IF
            ( b.PeHerbi12 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Bravoxone',
        IF
            ( b.PeHerbi13 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Primaxone',
        IF
            ( b.PeHerbi14 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Bimastar',
        IF
            ( b.PeHerbi15 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Polado',
        IF
            ( b.PeHerbi16 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Primastar',
        IF
            ( b.PeHerbi17 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Rumat',
        IF
            ( b.PeHerbi18 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Supretox',
        IF
            ( b.PeHerbi19 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Kleenup',
        IF
            ( b.PeHerbi20 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Prima Up',
        IF
            ( b.PeHerbi21 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Tanistar',
        IF
            ( b.PeHerbi22 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - DMA',
        IF
            ( b.PeHerbi23 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Polaris',
        IF
            ( b.PeHerbi24 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Konup',
        IF
            ( b.PeHerbi25 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Herbatop',
        IF
            ( b.PeHerbi26 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Mupxone',
        IF
            ( b.PeHerbi27 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Pointer',
        IF
            ( b.PeHerbi28 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Senus',
        IF
            ( b.PeHerbi29 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Tamaxon',
            IFNULL( b.PeHerbiOther, 'No' ) AS 'Herbicide - Brand - Other Brand',
        IF
            ( b.PeUsingInsecticide = 1, 'Yes', 'No' ) AS 'Insecticide - Using Insecticide',
            b.PeMoneySpentInsec AS 'Insecticide - How much money did you spend in the past 24 months',
            b.PeFreqInsec AS 'Insecticide - Pesticides Frequency (Times/Year)',
        IF
            ( b.PeInsec1 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Alika',
        IF
            ( b.PeInsec2 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Matador',
        IF
            ( b.PeInsec3 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Capture',
        IF
            ( b.PeInsec4 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Bento',
        IF
            ( b.PeInsec5 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Regent',
        IF
            ( b.PeInsec6 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Drusban',
        IF
            ( b.PeInsec7 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Penalty',
        IF
            ( b.PeInsec8 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Nurelle',
        IF
            ( b.PeInsec9 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Chlormite',
        IF
            ( b.PeInsec10 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Decis',
        IF
            ( b.PeInsec11 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Klensect',
        IF
            ( b.PeInsec12 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Vigor',
        IF
            ( b.PeInsec13 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Unicide',
        IF
            ( b.PeInsec14 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Deicer 505',
        IF
            ( b.PeInsec15 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Arrivo',
        IF
            ( b.PeInsec16 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Sidamethrin',
        IF
            ( b.PeInsec17 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Bestox',
        IF
            ( b.PeInsec18 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Halona',
        IF
            ( b.PeInsec19 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Dangke',
        IF
            ( b.PeInsec20 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Buldok',
        IF
            ( b.PeInsec21 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Laser',
        IF
            ( b.PeInsec22 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Sevin',
        IF
            ( b.PeInsec23 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Organic',
            IFNULL( b.PeInsecOther, 'No' ) AS 'Insecticide - Brand - Other Brand',
        IF
            ( b.PeUsingFungicide = 1, 'Yes', 'No' ) AS 'Fungicide - Using Fungicide',
            b.PeMoneySpentFungi AS 'Fungicide - How much money did you spend in the past 24 months',
            b.PeFreqFungi AS 'Fungicide - Pesticides Frequency (Times/Year)',
        IF
            ( b.PeFungi1 = 1, 'Yes', 'No' ) AS 'Fungicide - Brand - Klensect',
        IF
            ( b.PeFungi2 = 1, 'Yes', 'No' ) AS 'Fungicide - Brand - Vigor',
        IF
            ( b.PeFungi3 = 1, 'Yes', 'No' ) AS 'Fungicide - Brand - Unicide',
        IF
            ( b.PeFungi4 = 1, 'Yes', 'No' ) AS 'Fungicide - Brand - Deicer 505',
        IF
            ( b.PeFungi5 = 1, 'Yes', 'No' ) AS 'Fungicide - Brand - Arrivo',
        IF
            ( b.PeFungi6 = 1, 'Yes', 'No' ) AS 'Fungicide - Brand - Sidamethrin',
        IF
            ( b.PeFungi7 = 1, 'Yes', 'No' ) AS 'Fungicide - Brand - Bestox',
        IF
            ( b.PeFungi8 = 1, 'Yes', 'No' ) AS 'Fungicide - Brand - Halona',
        IF
            ( b.PeFungi9 = 1, 'Yes', 'No' ) AS 'Fungicide - Brand - Dangke',
        IF
            ( b.PeFungi10 = 1, 'Yes', 'No' ) AS 'Fungicide - Brand - Buldok',
        IF
            ( b.PeFungi11 = 1, 'Yes', 'No' ) AS 'Fungicide - Brand - Laser',
        IF
            ( b.PeFungi12 = 1, 'Yes', 'No' ) AS 'Fungicide - Brand - Organic',
            IFNULL( b.PeFungiOther, 'No' ) AS 'Fungicide - Brand - Other Brand',
            b.UseProtectiveGear AS 'Do you use protective gears',
            b.EquipHelm AS 'Personal protective equipments used during harvest or garden maintenance - Helm',
            b.EquipBoots AS 'Personal protective equipments used during harvest or garden maintenance - Boots',
            b.EquipDodosProtector AS 'Personal protective equipments used during harvest or garden maintenance - Dodos Protector',
            b.EquipMask AS 'Personal protective equipments used during harvest or garden maintenance - Mask',
            b.EquipGloves AS 'Personal protective equipments used during harvest or garden maintenance - Gloves',
            b.EquipSprayGlasses AS 'Personal protective equipments used during harvest or garden maintenance - Spray Glasses',
            b.EquipEgrekProtector AS 'Personal protective equipments used during harvest or garden maintenance - Egrek Protector',
            b.EquipProtectiveClothing AS 'Personal protective equipments used during harvest or garden maintenance - Protective Clothing',
        CASE
                
                WHEN b.PestStoreLocation = 1 THEN
                'In the house' 
                WHEN b.PestStoreLocation = 2 THEN
                'Pesticide specific place' 
                WHEN b.PestStoreLocation = 3 THEN
                'Outside of the house (house area)' 
                WHEN b.PestStoreLocation = 4 THEN
                'Outside of the cocoa farm' 
                WHEN b.PestStoreLocation = 5 THEN
                'Others' ELSE '-' 
            END AS 'Where you stored pesticides before and after using it',
        CASE
                
                WHEN b.PestPackageAfterUse = 1 THEN
                'Random disposal (Garden or around the house)' 
                WHEN b.PestPackageAfterUse = 2 THEN
                'Use for something else' 
                WHEN b.PestPackageAfterUse = 3 THEN
                'Thoroughly and then burry it' 
                WHEN b.PestPackageAfterUse = 4 THEN
                'Burn' 
                WHEN b.PestPackageAfterUse = 5 THEN
                'Recycle' 
                WHEN b.PestPackageAfterUse = 6 THEN
                'Others' ELSE '-' 
            END AS 'What you do to pesticide package after using it',
        IF
            ( b.PestMainRats = 1, 'Yes', 'No' ) AS 'Main Pest on Plantation - Rats',
        IF
            ( b.PestMainOly = 1, 'Yes', 'No' ) AS 'Main Pest on Plantation - Olygonichus',
        IF
            ( b.PestMainSatora = 1, 'Yes', 'No' ) AS 'Main Pest on Plantation - Satora Nitens',
        IF
            ( b.PestMainTira = 1, 'Yes', 'No' ) AS 'Main Pest on Plantation - Tirathaba Mundella',
        IF
            ( b.PestMainRhino = 1, 'Yes', 'No' ) AS 'Main Pest on Plantation - Rhinoceros Beetle',
        IF
            ( b.PestMainElep = 1, 'Yes', 'No' ) AS 'Main Pest on Plantation - Elephant',
        IF
            ( b.PestMainOrgUtan = 1, 'Yes', 'No' ) AS 'Main Pest on Plantation - Orang Utan',
        IF
            ( b.PestMainLandak = 1, 'Yes', 'No' ) AS 'Main Pest on Plantation - Hedgehog',
        IF
            ( b.PestMainBabi = 1, 'Yes', 'No' ) AS 'Main Pest on Plantation - Boar',
        IF
            ( b.PestMainOther = 1, b.PestMainOtherText, 'No' ) AS 'Main Pest on Plantation - Others',
        IF
            ( b.DisMainBlast = 1, 'Yes', 'No' ) AS 'Main Disease on Plantation - Blast Disease',
        IF
            ( b.DisMainGeno = 1, 'Yes', 'No' ) AS 'Main Disease on Plantation - Basal Steam Root / Genoderma',
        IF
            ( b.DisMainSteam = 1, 'Yes', 'No' ) AS 'Main Disease on Plantation - Upper Steam Rot',
        IF
            ( b.DisMainBud = 1, 'Yes', 'No' ) AS 'Main Disease on Plantation - Bud Rot',
        IF
            ( b.DisMainSpear = 1, 'Yes', 'No' ) AS 'Main Disease on Plantation - Spear Rot',
        IF
            ( b.DisMainYellow = 1, 'Yes', 'No' ) AS 'Main Disease on Plantation - Patch Yellow',
        IF
            ( b.DisMainAnt = 1, 'Yes', 'No' ) AS 'Main Disease on Plantation - Anthracnose',
        IF
            ( b.DisMainCrown = 1, 'Yes', 'No' ) AS 'Main Disease on Plantation - Crown disease',
        IF
            ( b.DisMainViscular = 1, 'Yes', 'No' ) AS 'Main Disease on Plantation - Viscular Wilt',
        IF
            ( b.DisMainBunch = 1, 'Yes', 'No' ) AS 'Main Disease on Plantation - Bunch Rot',
        IF
            ( b.DisMainOther = 1, b.DisMainOtherText, 'No' ) AS 'Main Disease on Plantation - Others' 
        FROM
            ktv_members a
            LEFT JOIN ktv_survey_plot_sme b ON a.MemberID = b.MemberID
            LEFT JOIN ktv_village c ON b.VillageID = c.VillageID
            LEFT JOIN ktv_subdistrict d ON c.SubDistrictID = d.SubDistrictID
            LEFT JOIN ktv_district e ON d.DistrictID = e.DistrictID
            LEFT JOIN ktv_province f ON e.ProvinceID = f.ProvinceID
            LEFT JOIN ktv_survey g ON b.SurveyNr = g.SurveyNr
            LEFT JOIN ktv_access_partner_member apm ON apm.apmMemberID = a.MemberID
            LEFT JOIN ktv_member_role kmr ON kmr.MemberID = a.MemberID
            LEFT JOIN ktv_ref_member_role krm ON krm.MRoleID = kmr.MRoleID
            LEFT JOIN ktv_program_partner pp ON pp.PartnerID = a.PartnerID 
        WHERE
            b.StatusCode = 'active' 
            AND a.StatusCode = 'active' 
            AND apm.apmPartnerID IN ( 78, 179 ) 
            AND krm.MRoleType = 'Agent'
        ";                

        $query = $this->db->query($sql);

        $data["data"]   = $query->result_array();
        $data["total"]  = $query->num_rows();

        return $data;
    }

    public function sme(){
        $sql = "
        SELECT
            a.DateCollection AS 'Tanggal Koleksi',
            pp.`PartnerFullName` 'Nama Mill',
            a.MemberID AS 'System ID',
            a.MemberUID AS 'External ID',
            a.MemberDisplayID AS 'MemberID',
            a.MemberName AS 'Nama Petani',
            IFNULL( a.Nin, '-' ) AS 'Nomor Identitas(KTP)',
            a.DateOfBirth AS 'Tanggal Lahir',
            sp.Latitude,
            sp.Longitude,
        IF
            ( a.Gender = 'm', 'Male', IF ( a.Gender = 'f', 'Female', '-' ) ) AS 'Jenis Kelamin',
        IF
            (
                a.HandphoneType = 1,
                'Smartphone',
            IF
                ( a.HandphoneType = 2, 'Feature Phone', IF ( a.HandphoneType = 3, 'No Handphone', '-' ) ) 
            ) AS 'Tipe Handphone',
        IF
            (
                a.CategoryFarmer = 1,
                'Owned',
            IF
                (
                    a.CategoryFarmer = 2,
                    'Profit Sharing',
                IF
                    ( a.CategoryFarmer = 3, 'Rented', IF ( a.CategoryFarmer = 4, 'Others', '-' ) ) 
                ) 
            ) AS 'Farmer Category',
        IF
            (
                a.MembershipStatus = 1,
                'Potensial',
            IF
                (
                    a.MembershipStatus = 2,
                    'Provisional',
                IF
                    (
                        a.MembershipStatus = 3,
                        'Member',
                    IF
                        (
                            a.MembershipStatus = 4,
                            'Member - Certified',
                        IF
                            (
                                a.MembershipStatus = 5,
                                'Member - Suspended',
                            IF
                                ( a.MembershipStatus = 6, 'Resigned', IF ( a.MembershipStatus = 7, 'Withdrawn', '-' ) ) 
                            ) 
                        ) 
                    ) 
                ) 
            ) AS 'Membership Status',
        IF
            (
                a.MaritalStatus = 1,
                'Menikah',
            IF
                ( a.MaritalStatus = 2, 'Lajang', IF ( a.MaritalStatus = 3, 'Janda/Duda', '-' ) ) 
            ) AS 'Status Pernikahan',
            a.Address AS 'Alamat',
            b.Village AS 'Desa',
            c.SubDistrict AS 'Kecamatan',
            d.District AS 'Kabupaten',
            e.Province AS 'Provinsi',
            f.NrPlot AS 'Jumlah Kebun',
            f.GardenHa AS 'Total Hektar',
            g.UserRealName AS 'Enumerator',
            h.UserRealName AS 'LastModifiedBy' 
        FROM
            ktv_members a
            LEFT JOIN ktv_survey_plot_sme as sp on sp.MemberID = a.MemberID
            LEFT JOIN ktv_village b ON b.VillageID = a.VillageID
            LEFT JOIN ktv_subdistrict c ON c.SubDistrictID = b.SubDistrictID
            LEFT JOIN ktv_district d ON d.DistrictID = c.DistrictID
            LEFT JOIN ktv_province e ON e.ProvinceID = d.ProvinceID
            LEFT JOIN ( SELECT MemberID, COUNT( PlotNr ) NrPlot, SUM( GardenAreaHa ) GardenHa FROM ktv_survey_plot GROUP BY MemberID ) f ON f.MemberID = a.MemberID
            LEFT JOIN sys_user g ON g.UserID = a.CreatedBy
            LEFT JOIN sys_user h ON h.UserID = a.LastModifiedBy
            LEFT JOIN ktv_member_role kmr ON kmr.MemberID = a.MemberID
            LEFT JOIN ktv_ref_member_role krm ON krm.MRoleID = kmr.MRoleID
            LEFT JOIN `ktv_program_partner` pp ON pp.`PartnerID` = a.`PartnerID` 
        WHERE
            a.StatusCode = 'active' 
            AND a.`PartnerID` IN ( 78, 179 ) 
            AND krm.MRoleType = 'Agent' 
        ORDER BY
            a.DateCollection DESC
        ";

        $query = $this->db->query($sql);

        $data["data"]   = $query->result_array();
        $data["total"]  = $query->num_rows();

        return $data;
    }

	public function basic_farmer()
	{
        $sql = "
        SELECT
            a.DateCollection AS 'Tanggal Koleksi',
            pp.`PartnerFullName` 'Nama Mill',
            a.MemberID AS 'System ID',
            a.MemberUID AS 'External ID',
            a.MemberDisplayID AS 'MemberID',
            a.MemberName AS 'Nama Petani',
            sp.Latitude,
            sp.Longitude,
            IFNULL( a.Nin, '-' ) AS 'Nomor Identitas(KTP)',
            a.DateOfBirth AS 'Tanggal Lahir',
        IF
            ( a.Gender = 'm', 'Male', IF ( a.Gender = 'f', 'Female', '-' ) ) AS 'Jenis Kelamin',
        IF
            (
                a.HandphoneType = 1,
                'Smartphone',
            IF
                ( a.HandphoneType = 2, 'Feature Phone', IF ( a.HandphoneType = 3, 'No Handphone', '-' ) ) 
            ) AS 'Tipe Handphone',
        IF
            (
                a.CategoryFarmer = 1,
                'Owned',
            IF
                (
                    a.CategoryFarmer = 2,
                    'Profit Sharing',
                IF
                    ( a.CategoryFarmer = 3, 'Rented', IF ( a.CategoryFarmer = 4, 'Others', '-' ) ) 
                ) 
            ) AS 'Farmer Category',
        IF
            (
                a.MembershipStatus = 1,
                'Potensial',
            IF
                (
                    a.MembershipStatus = 2,
                    'Provisional',
                IF
                    (
                        a.MembershipStatus = 3,
                        'Member',
                    IF
                        (
                            a.MembershipStatus = 4,
                            'Member - Certified',
                        IF
                            (
                                a.MembershipStatus = 5,
                                'Member - Suspended',
                            IF
                                ( a.MembershipStatus = 6, 'Resigned', IF ( a.MembershipStatus = 7, 'Withdrawn', '-' ) ) 
                            ) 
                        ) 
                    ) 
                ) 
            ) AS 'Membership Status',
        IF
            (
                a.MaritalStatus = 1,
                'Menikah',
            IF
                ( a.MaritalStatus = 2, 'Lajang', IF ( a.MaritalStatus = 3, 'Janda/Duda', '-' ) ) 
            ) AS 'Status Pernikahan',
            a.Address AS 'Alamat',
            b.Village AS 'Desa',
            c.SubDistrict AS 'Kecamatan',
            d.District AS 'Kabupaten',
            e.Province AS 'Provinsi',
            f.NrPlot AS 'Jumlah Kebun',
            f.GardenHa AS 'Total Hektar',
            g.UserRealName AS 'Enumerator',
            h.UserRealName AS 'LastModifiedBy' 
        FROM
            ktv_members a
            LEFT JOIN ktv_survey_plot as sp on sp.MemberID = a.MemberID
            LEFT JOIN ktv_village b ON b.VillageID = a.VillageID
            LEFT JOIN ktv_subdistrict c ON c.SubDistrictID = b.SubDistrictID
            LEFT JOIN ktv_district d ON d.DistrictID = c.DistrictID
            LEFT JOIN ktv_province e ON e.ProvinceID = d.ProvinceID
            LEFT JOIN ( SELECT MemberID, COUNT( PlotNr ) NrPlot, SUM( GardenAreaHa ) GardenHa FROM ktv_survey_plot GROUP BY MemberID ) f ON f.MemberID = a.MemberID
            LEFT JOIN sys_user g ON g.UserID = a.CreatedBy
            LEFT JOIN sys_user h ON h.UserID = a.LastModifiedBy
            LEFT JOIN ktv_access_partner_member apm ON apm.apmMemberID = a.MemberID
            LEFT JOIN `ktv_program_partner` pp ON pp.`PartnerID` = apm.`apmPartnerID` 
        WHERE
            a.StatusCode = 'active' 
            AND apm.apmPartnerID IN ( 78, 179 ) 
        ORDER BY
            a.DateCollection DESC
        ";

        $query = $this->db->query($sql);

        $data["data"]   = $query->result_array();
        $data["total"]  = $query->num_rows();

        return $data;
    }

    public function labour(){
        $sql = "
        SELECT
                e.District,
                d.SubDistrict AS 'Sub District',
                c.Village,
                a.MemberID AS 'System ID',
                a.MemberDisplayID AS 'Member ID',
                a.MemberName AS 'Member Name',
                b.LaboName AS 'Labour Name',
                b.TypeWorkSeed AS 'Working Seed',
                b.TypeWorkSlash AS 'Working Slash',
                b.TypeWorkCircle AS 'Working Circle',
                b.TypeWorkPruning AS 'Working Pruning',
                b.TypeWorkPemupukan AS 'Working Pemupukan',
                b.TypeWorkPest AS 'Woking Pest',
                b.TypeWorkHarvest AS 'Working Harvest',
                b.TypeWorkTransport AS 'Working Transport',
                b.TotalWorkingHrsPerDay AS 'Total Working Hours Per Day',
                b.WageAmount,
        IF
                (
                        b.WagePeriod = 1,
                        '1 - Per Day',
                IF
                        ( b.WagePeriod = 2, '2 - Per Month', IF ( b.WagePeriod = 3, '3 - Per Year', '-' ) ) 
                ) AS 'Wage Period',
                b.YearOfBirth AS 'Year of Birth',
                b.Gender,
                b.DateCreated AS 'Date Created',
                f.UserRealName AS 'CreatedBy',
                b.DateUpdated AS 'Date Updated',
                g.UserRealName AS 'LastModifiedBy',
                b.DateSync AS 'Date Sync' 
            FROM
                ktv_members a
                JOIN ktv_member_labour b ON a.MemberID = b.MemberID
                LEFT JOIN ktv_village c ON c.VillageID = a.VillageID
                LEFT JOIN ktv_subdistrict d ON d.SubDistrictID = c.SubDistrictID
                LEFT JOIN ktv_district e ON e.DistrictID = d.DistrictID
                LEFT JOIN sys_user f ON f.UserId = b.CreatedBy
                LEFT JOIN sys_user g ON g.UserId = b.LastModifiedBy
                LEFT JOIN ktv_access_partner_member apm on apm.apmMemberID = a.MemberID
        WHERE
                a.StatusCode = 'active'
                AND apm.apmPartnerID IN (78,179)
        ORDER BY
                e.District,
                d.SubDistrict,
                c.Village,
                a.MemberID
        ";        

        $query = $this->db->query($sql);

        $data["data"]   = $query->result_array();
        $data["total"]  = $query->num_rows();

        return $data;
    }

    public function household(){
        $sql = "
        SELECT
            b.MemberID as 'System ID',
            b.MemberDisplayID AS 'Farmer ID',
            b.MemberName AS 'Farmer Name',
            CONCAT( c.SurveyNr, ' - ', c.SurveyTxt ) AS 'Survey Nr',
            a.DateCollection AS 'Date Collection',
            CONCAT( g.UserRealName, ', ', a.DateCreated ) AS 'Enumerator',
            CONCAT( h.UserRealName, ', ', a.DateCreated ) AS 'Modified by',
        CASE
                
                WHEN a.HhMember = 1 THEN
                'Six or more' 
                WHEN a.HhMember = 2 THEN
                'Five' 
                WHEN a.HhMember = 3 THEN
                'Four' 
                WHEN a.HhMember = 4 THEN
                'Three' 
                WHEN a.HhMember = 5 THEN
                'Two' 
                WHEN a.HhMember = 6 THEN
                'One' ELSE '-' 
            END AS 'Total Household Members',
        CASE
                
                WHEN a.HhInSchoolEarlyAge = 1 THEN
                'There are no children aged 6-18 years old' 
                WHEN a.HhInSchoolEarlyAge = 2 THEN
                'No' 
                WHEN a.HhInSchoolEarlyAge = 3 THEN
                'Yes' ELSE '-' 
            END AS 'Do the members of the household aged from 6 to 18 years old attend school',
        CASE
                
                WHEN a.FemaleEduLevel = 1 THEN
                'None' 
                WHEN a.FemaleEduLevel = 2 THEN
                'Grade school (incl disabled, Islamic, or non-formal)' 
                WHEN a.FemaleEduLevel = 3 THEN
                'Junior-high school (incl disabled, Islamic, or non-formal)' 
                WHEN a.FemaleEduLevel = 4 THEN
                'No female head/spouse' 
                WHEN a.FemaleEduLevel = 5 THEN
                'Vocational school (high-school level)' 
                WHEN a.FemaleEduLevel = 6 THEN
                'High school (incl disabled, Islamic, or non-formal)' 
                WHEN a.FemaleEduLevel = 7 THEN
                'Diploma (one-year or higher)' ELSE '-' 
            END AS 'What is the highest education level achieved by the female head of the household',
        CASE
                
                WHEN a.TypeOfFloor = 1 THEN
                'Earth or bamboo' 
                WHEN a.TypeOfFloor = 2 THEN
                'Others' ELSE '' 
            END AS 'What is the main floor type (covering most of the space) used in the house',
        CASE
                
                WHEN a.TypeOfToilet = 1 THEN
                'None, or latrine' 
                WHEN a.TypeOfToilet = 2 THEN
                'Non-flush to a septic tank' 
                WHEN a.TypeOfToilet = 3 THEN
                'Flush' ELSE '-' 
            END AS 'What type of toilet do you have',
        CASE
                
                WHEN a.PrimaryFuel = 1 THEN
                'Firewood, charcoal, or coal' 
                WHEN a.PrimaryFuel = 2 THEN
                'Gas/LPG, kerosene, electricity or others' ELSE '-' 
            END AS 'What is the primary source of fuel in the household',
        CASE
                
                WHEN a.Own12KgGas = 1 THEN
                'Yes' 
                WHEN a.Own12KgGas = 2 THEN
                'No' ELSE '-' 
            END AS 'Does the household own one or more 12Kg gas cylinder(s)',
        CASE
                
                WHEN a.OwnRefri = 1 THEN
                'Yes' 
                WHEN a.OwnRefri = 2 THEN
                'No' ELSE '-' 
            END AS 'Does the household own a refrigerator',
        CASE
                
                WHEN a.OwnMotor = 1 THEN
                'Yes' 
                WHEN a.OwnMotor = 2 THEN
                'No' ELSE '-' 
            END AS 'Does the household own a motor cycle or motor boat',
        CASE
                
                WHEN a.HaveBankAccount = 1 THEN
                'Yes' 
                WHEN a.HaveBankAccount = 2 THEN
                'No' ELSE '-' 
            END AS 'Do this household have a bank account',
        CASE
                
                WHEN a.UseMobileBanking = 1 THEN
                'Yes' 
                WHEN a.UseMobileBanking = 2 THEN
                'No' ELSE '-' 
            END AS 'Do you use mobile banking/cellphone-based banking',
        CASE
                
                WHEN a.OwnPrivateCar = 1 THEN
                'Yes' 
                WHEN a.OwnPrivateCar = 2 THEN
                'No' ELSE '-' 
            END AS 'Does the household own a private car',
        CASE
                
                WHEN a.OwnGriddedElectricity = 1 THEN
                'Yes' 
                WHEN a.OwnGriddedElectricity = 2 THEN
                'No' ELSE '-' 
            END AS 'Does the household have an electricity grid',
        CASE
                
                WHEN a.OwnComputer = 1 THEN
                'Yes' 
                WHEN a.OwnComputer = 2 THEN
                'No' ELSE '-' 
            END AS 'Does the household own a computer',
        CASE
                
                WHEN a.OwnAC = 1 THEN
                'Yes' 
                WHEN a.OwnAC = 2 THEN
                'No' ELSE '-' 
            END AS 'Does the household own an AC',
            a.ExpectationOfImprovement AS 'Do you have any expectations of the companies to provide support for your planation, If yes, what are your expectations',
        CASE
                
                WHEN a.WorkPalmCoverEconomy = 1 THEN
                'Yes' 
                WHEN a.WorkPalmCoverEconomy = 2 THEN
                'No' ELSE '-' 
            END AS 'Does working in an oil palm plantation cover the economic needs of your family',
        IF
            ( NeedsCoverFoods = 1, 'Yes', '-' ) AS 'What needs are covered - Foods',
        IF
            ( NeedsCoverHousing = 1, 'Yes', '-' ) AS 'What needs are covered - Housing',
        IF
            ( NeedsCoverClothing = 1, 'Yes', '-' ) AS 'What needs are covered - Clothing',
        IF
            ( NeedsCoverEducation = 1, 'Yes', '-' ) AS 'What needs are covered - Education',
        IF
            ( NeedsCoverHouseEquip = 1, 'Yes', '-' ) AS 'What needs are covered - Household equipment',
        IF
            ( NeedsCoverRecre = 1, 'Yes', '-' ) AS 'What needs are covered - Recreation / holidays',
        IF
            ( NeedsCoverOther = 1, 'Yes', '-' ) AS 'What needs are covered - Other',
        CASE
                
                WHEN a.ThinkAnotherJobPlant = 1 THEN
                'Yes' 
                WHEN a.ThinkAnotherJobPlant = 2 THEN
                'No' ELSE '-' 
            END AS 'Do you think of finding another job/planting different crop',
        CASE
                
                WHEN a.HaveLoan = 1 THEN
                'Yes' 
                WHEN a.HaveLoan = 2 THEN
                'No' ELSE '-' 
            END AS 'Do you have a loan',
        CASE
                
                WHEN a.WhereLoanFrom = 1 THEN
                'Bank' 
                WHEN a.WhereLoanFrom = 2 THEN
                'Unofficial loan agent' 
                WHEN a.WhereLoanFrom = 3 THEN
                'Family/friend' 
                WHEN a.WhereLoanFrom = 4 THEN
                'Other' ELSE '-' 
            END AS 'Where did you take out the loan',
        CASE
                
                WHEN a.LoanForPalm = 1 THEN
                'Yes' 
                WHEN a.LoanForPalm = 2 THEN
                'No' ELSE '-' 
            END AS 'Is the loan used for oil palm cultivation' 
        FROM
        ktv_survey_household a
        LEFT JOIN ktv_members b ON a.MemberID = b.MemberID
        LEFT JOIN ktv_survey c ON a.SurveyNr = c.SurveyNr
        LEFT JOIN sys_user g ON a.CreatedBy = g.UserId
        LEFT JOIN sys_user h ON a.LastModifiedBy = h.UserId
        LEFT JOIN ktv_access_partner_member apm on apm.apmMemberID = a.MemberID
        WHERE
            a.StatusCode = 'active' 
            AND b.StatusCode = 'active'
            AND apm.apmPartnerID IN (78,179)
        ";        

        $query = $this->db->query($sql);

        $data["data"]   = $query->result_array();
        $data["total"]  = $query->num_rows();

        return $data;
    }

    public function garden_join(){
        $sql = "
        SELECT
            a.MemberID AS 'System ID',
            a.MemberDisplayID AS 'Farmer ID',
            a.MemberName AS 'Farmer Name',
            pp.PartnerName 'Nama Mill',
            b.PlotNr AS 'Plantation Nr',
            CONCAT( g.SurveyNr, ' - ', g.SurveyTxt ) AS 'Survey Nr',
            b.DateCollection AS 'Date Collection',
            f.Province AS 'Province',
            e.District AS 'District',
            d.SubDistrict AS 'Subdistrict',
            c.Village AS 'Village',
            b.GardenAreaHa AS 'Area of Plantation(Ha)',
            b.GardenAreaPolygon AS 'Area of Garden Polygon (Ha)',
            b.Latitude AS 'Latitude',
            b.Longitude AS 'Longitude',
        CASE
                
                WHEN b.LandOwnershipType = 1 THEN
                'Owned' 
                WHEN b.LandOwnershipType = 2 THEN
                'Profit Sharing' 
                WHEN b.LandOwnershipType = 3 THEN
                'Rented' ELSE 'Others' 
            END AS 'Land Ownership',
        CASE
                
                WHEN b.OwnerOfTheGarden = 1 THEN
                'Registered Farmer' 
                WHEN b.OwnerOfTheGarden = 2 THEN
                'Family Members' 
                WHEN b.OwnerOfTheGarden = 3 THEN
                'Other People' ELSE 'Do Not Know ' 
            END AS 'Owner of the Garden',
            b.OwnerOfPlantationNameText AS 'Owner of this Plantation - Name',
            b.OwnerOfPlantationLocationText AS 'Owner of this Plantation - Location',
        CASE
                
                WHEN b.OwnershipDoc = 1 THEN
                'No Document' 
                WHEN b.OwnershipDoc = 2 THEN
                'SKT (Surat Keterangan Tanah)' 
                WHEN b.OwnershipDoc = 3 THEN
                'SHM (Sertifikat Hak Milik)/Certificate' 
                WHEN b.OwnershipDoc = 4 THEN
                'HGU (Hak Guna Usaha)' 
                WHEN b.OwnershipDoc = 5 THEN
                'SKGR (Surat Keterangan Ganti Rugi)' ELSE b.OwnershipDocText 
            END AS 'Ownership Document',
        IF
            ( b.OwnerDocIsOwner = 1, 'Yes', IF ( b.OwnerDocIsOwner = 2, 'No', 'Do Not Know' ) ) AS 'Is the ownership document in the name of the current owner',
        IF
            ( b.HaveSTDB = 1, 'Yes', IF ( b.HaveSTDB = 2, 'No', 'Do Not Know' ) ) AS 'Does the farm have a STD-B (operational / business letter)',
        IF
            ( b.HaveSPPL = 1, 'Yes', IF ( b.HaveSPPL = 2, 'No', 'Do Not Know' ) ) AS 'Does the farm have a SPPL (Environmental Management Letter)',
        IF
            ( b.BusinessModel = 1, 'Independence', IF ( b.BusinessModel = 2, 'Independent - Ex Plasma', 'Plasma(has existing contract with plantation)' ) ) AS 'Business Model',
        CASE
                
                WHEN b.HowObPlantation = 1 THEN
                'Inheritance' 
                WHEN b.HowObPlantation = 2 THEN
                'Purchased' 
                WHEN b.HowObPlantation = 3 THEN
                'Convert Existing Plantation' 
                WHEN b.HowObPlantation = 4 THEN
                'Received From Government (Transmigrate)' ELSE b.HowObPlantationText 
            END AS 'How did you obtain the plantation',
        CASE
                
                WHEN b.PlantationConditionEst = 2 THEN
                'Secondary Veg/Fallow' 
                WHEN b.PlantationConditionEst = 2 THEN
                'Food Crops' 
                WHEN b.PlantationConditionEst = 3 THEN
                'Mangrove' 
                WHEN b.PlantationConditionEst = 4 THEN
                'Other Plantation (rubber, coffee, etc)' 
                WHEN b.PlantationConditionEst = 5 THEN
                'Oil Palm Plantation' 
                WHEN b.PlantationConditionEst = 6 THEN
                'Forest' ELSE 'I dont know' 
            END AS 'Condition when establishing oil palm plantation',
            b.AverageAgeTree AS 'Average age of trees on plantation (years)',
        IF
            (
                b.SoilType = 1,
                'Mineral',
            IF
                ( b.SoilType = 2, 'Peat', IF ( b.SoilType = 3, 'Sandy', '-' ) ) 
            ) AS 'Soil Type',
        IF
            (
                b.TopographyType = 1,
                'Flat',
            IF
                ( b.TopographyType = 2, 'Hilly', IF ( b.TopographyType = 3, 'Mountainous', '-' ) ) 
            ) AS 'Type of Topography Plantation',
            b.FirstPlantingYear AS 'Year of first planting palm trees',
            b.TreeTBM AS 'TBM - Plants yet to produce',
            b.TreeTM AS 'TM - Producing plants',
            b.TreeTR AS 'TR - Old/diseased',
            b.TreeTBM + b.TreeTM + b.TreeTR AS 'Total Number of Trees',
        IF
            ( b.TypePlantMateMarihat = 1, 'Yes', 'No' ) AS 'Type of Planting Material - Marihat',
            b.TypePlantMateMarihatNr AS 'Type of Planting Material - Marihat Number Of Trees',
        IF
            ( b.TypePlantMateDumpy = 1, 'Yes', 'No' ) AS 'Type of Planting Material - Dumpy',
            b.TypePlantMateDumpyNr AS 'Type of Planting Material - Dumpy Number Of Trees',
        IF
            ( b.TypePlantMateLonsum = 1, 'Yes', 'No' ) AS 'Type of Planting Material - Lonsum',
            b.TypePlantMateLonsumNr AS 'Type of Planting Material - Lonsum Number Of Trees',
        IF
            ( b.TypePlantMateSimalungun = 1, 'Yes', 'No' ) AS 'Type of Planting Material - Simalungun',
            b.TypePlantMateSimalungunNr AS 'Type of Planting Material - Simalungun Number Of Trees',
        IF
            ( b.TypePlantMateDanimas = 1, 'Yes', 'No' ) AS 'Type of Planting Material - Dami Mas',
            b.TypePlantMateDanimasNr AS 'Type of Planting Material - Dami Mas Number Of Trees',
        IF
            ( b.TypePlantMateSriwijaya = 1, 'Yes', 'No' ) AS 'Type of Planting Material - Sriwijaya',
            b.TypePlantMateSriwijayaNr AS 'Type of Planting Material - Sriwijaya Number Of Trees',
        IF
            ( b.TypePlantMateSocfin = 1, 'Yes', 'No' ) AS 'Type of Planting Material - Socfin',
            b.TypePlantMateSocfinNr AS 'Type of Planting Material - Socfin Number Of Trees',
        IF
            ( b.TypePlantMateOther = 1, b.TypePlantMateOtherText, '-' ) AS 'Type of Planting Material - Other Method',
            b.TypePlantMateOtherNr AS 'Type of Planting Material - Other Method Number Of Trees',
        IF
            ( b.TypePlantMateDoNotKnow = 1, 'Yes', 'No' ) AS 'Type of Planting Material - Do Not Know',
            b.TypePlantMateDoNotKnowNr AS 'Type of Planting Material - Do Not Know Number Of Trees',
            b.TreeTBM + b.TreeTM + b.TreeTR AS 'Total Number of Oil Palm Trees',
            b.HarvestRateDaysHighSeason AS 'Harvest rate (once every how many days) in high season',
            b.HarvestRateDaysLowSeason AS 'Harvest rate (once every how many days) in low season',
            b.AverageProdHighSeason AS 'Average production per harvest (ton) in high season',
            b.AverageProdLowSeason AS 'Average production per harvest (ton) in low season',
            12 - (
                IFNULL( CAST( b.LeanHarvestSeasonJan AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonFeb AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonMar AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonApr AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonMay AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonJun AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonJul AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonAug AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonSep AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonOct AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonNov AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonDec AS UNSIGNED ), 0 ) 
            ) AS 'Number of Months in High Season',
            (
                IFNULL( CAST( b.LeanHarvestSeasonJan AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonFeb AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonMar AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonApr AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonMay AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonJun AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonJul AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonAug AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonSep AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonOct AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonNov AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonDec AS UNSIGNED ), 0 ) 
            ) AS 'Number of Months in Low Season',
            ( 30 / b.HarvestRateDaysHighSeason ) * b.AverageProdHighSeason * (
                12 - (
                    IFNULL( CAST( b.LeanHarvestSeasonJan AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonFeb AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonMar AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonApr AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonMay AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonJun AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonJul AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonAug AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonSep AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonOct AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonNov AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonDec AS UNSIGNED ), 0 ) 
                ) 
            ) AS 'High Season Production (ton)',
            ( 30 / b.HarvestRateDaysLowSeason ) * b.AverageProdLowSeason * (
                (
                    IFNULL( CAST( b.LeanHarvestSeasonJan AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonFeb AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonMar AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonApr AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonMay AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonJun AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonJul AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonAug AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonSep AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonOct AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonNov AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonDec AS UNSIGNED ), 0 ) 
                ) 
            ) AS 'Low Season Production (ton)',
            (
                ( 30 / b.HarvestRateDaysHighSeason ) * b.AverageProdHighSeason * (
                    12 - (
                        IFNULL( CAST( b.LeanHarvestSeasonJan AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonFeb AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonMar AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonApr AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonMay AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonJun AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonJul AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonAug AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonSep AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonOct AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonNov AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonDec AS UNSIGNED ), 0 ) 
                    ) 
                ) 
                ) + (
                ( 30 / b.HarvestRateDaysLowSeason ) * b.AverageProdLowSeason * (
                    (
                        IFNULL( CAST( b.LeanHarvestSeasonJan AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonFeb AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonMar AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonApr AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonMay AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonJun AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonJul AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonAug AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonSep AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonOct AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonNov AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonDec AS UNSIGNED ), 0 ) 
                    ) 
                ) 
            ) AS 'Annual Production (ton)',
            (
                (
                    ( 30 / b.HarvestRateDaysHighSeason ) * b.AverageProdHighSeason * (
                        12 - (
                            IFNULL( CAST( b.LeanHarvestSeasonJan AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonFeb AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonMar AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonApr AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonMay AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonJun AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonJul AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonAug AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonSep AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonOct AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonNov AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonDec AS UNSIGNED ), 0 ) 
                        ) 
                    ) 
                    ) + (
                    ( 30 / b.HarvestRateDaysLowSeason ) * b.AverageProdLowSeason * (
                        (
                            IFNULL( CAST( b.LeanHarvestSeasonJan AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonFeb AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonMar AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonApr AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonMay AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonJun AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonJul AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonAug AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonSep AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonOct AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonNov AS UNSIGNED ), 0 ) + IFNULL( CAST( b.LeanHarvestSeasonDec AS UNSIGNED ), 0 ) 
                        ) 
                    ) 
                ) 
            ) / b.GardenAreaHa AS 'Plantation Productivity (ton/ha)',
        IF
            ( b.LeanHarvestSeasonJan = 1, 'Yes', 'No' ) AS 'When is the lean harvest season for oil palm in your area - January',
        IF
            ( b.LeanHarvestSeasonFeb = 1, 'Yes', 'No' ) AS 'When is the lean harvest season for oil palm in your area - February',
        IF
            ( b.LeanHarvestSeasonMar = 1, 'Yes', 'No' ) AS 'When is the lean harvest season for oil palm in your area - March',
        IF
            ( b.LeanHarvestSeasonApr = 1, 'Yes', 'No' ) AS 'When is the lean harvest season for oil palm in your area - April',
        IF
            ( b.LeanHarvestSeasonMay = 1, 'Yes', 'No' ) AS 'When is the lean harvest season for oil palm in your area - May',
        IF
            ( b.LeanHarvestSeasonJun = 1, 'Yes', 'No' ) AS 'When is the lean harvest season for oil palm in your area - June',
        IF
            ( b.LeanHarvestSeasonJul = 1, 'Yes', 'No' ) AS 'When is the lean harvest season for oil palm in your area - July',
        IF
            ( b.LeanHarvestSeasonAug = 1, 'Yes', 'No' ) AS 'When is the lean harvest season for oil palm in your area - August',
        IF
            ( b.LeanHarvestSeasonSep = 1, 'Yes', 'No' ) AS 'When is the lean harvest season for oil palm in your area - September',
        IF
            ( b.LeanHarvestSeasonOct = 1, 'Yes', 'No' ) AS 'When is the lean harvest season for oil palm in your area - October',
        IF
            ( b.LeanHarvestSeasonNov = 1, 'Yes', 'No' ) AS 'When is the lean harvest season for oil palm in your area - November',
        IF
            ( b.LeanHarvestSeasonDec = 1, 'Yes', 'No' ) AS 'When is the lean harvest season for oil palm in your area - December',
        IF
            ( b.WhoHarvestFamily = 1, 'Yes', 'No' ) AS 'Who does the harvesting - Respondent and/or Family member',
        IF
            ( b.WhoHarvestLabor = 1, 'Yes', 'No' ) AS 'Who does the harvesting - Use of Hired Labor',
        CASE
                
                WHEN b.HowManyDiffBuyerSoldLastYear = 1 THEN
                '1' 
                WHEN b.HowManyDiffBuyerSoldLastYear = 1 THEN
                '2' 
                WHEN b.HowManyDiffBuyerSoldLastYear = 1 THEN
                '3' 
                WHEN b.HowManyDiffBuyerSoldLastYear = 1 THEN
                '4' ELSE 'More than 4' 
            END AS 'To how many different buyers have you sold your FFB from this plantation to within the past year',
        CASE
                
                WHEN b.HowManyDiffMillSoldLastYear = 1 THEN
                '1' 
                WHEN b.HowManyDiffMillSoldLastYear = 1 THEN
                '2' 
                WHEN b.HowManyDiffMillSoldLastYear = 1 THEN
                '3' 
                WHEN b.HowManyDiffMillSoldLastYear = 1 THEN
                '4' ELSE 'More than 4' 
            END AS 'How many different palm oil mills have you sold your FFB to within the past year',
            b.COMMENT AS 'Any comments about plantation',
        IF
            ( b.FertNonOrganicData = 1, 'Yes', IF ( b.FertNonOrganicData = 2, 'No', '-' ) ) AS 'Non Organic / Chemical Fertilizing Data',
            b.FertMoneySpentNonOrganic AS 'How much money did you spend in the past 24 months on Chemical Fertilizer',
            b.FertUreaTimesYear AS 'Urea - Times/Year',
            b.FertUreaDose AS 'Urea - Dose/Plot/Times per Year',
            b.FertUreaTimesYear * b.FertUreaDose AS 'Urea - Dose/Plot/Year',
            b.FertSSTimesYear AS 'SS - Times/Year',
            b.FertSSDose AS 'SS - Dose/Plot/Times per Year',
            b.FertSSTimesYear * b.FertSSDose AS 'SS - Dose/Plot/Year',
            b.FertNPKTimesYear AS 'NPK - Times/Year',
            b.FertNPKDose AS 'NPK - Dose/Plot/Times per Year',
            b.FertNPKTimesYear * b.FertNPKDose AS 'NPK - Dose/Plot/Year',
            b.FertTSPTimesYear AS 'TSP - Times/Year',
            b.FertTSPDose AS 'TSP - Dose/Plot/Times per Year',
            b.FertTSPTimesYear * b.FertTSPDose AS 'TSP - Dose/Plot/Year',
            b.FertCUTimesYear AS 'CU - Times/Year',
            b.FertCUDose AS 'CU - Dose/Plot/Times per Year',
            b.FertCUTimesYear * b.FertCUDose AS 'CU - Dose/Plot/Year',
            b.FertKCLTimesYear AS 'KCL - Times/Year',
            b.FertKCLDose AS 'KCL - Dose/Plot/Times per Year',
            b.FertKCLTimesYear * b.FertKCLDose AS 'KCL - Dose/Plot/Year',
            b.FertNPKMutiTimesYear AS 'NPK Mutiara - Times/Year',
            b.FertNPKMutiDose AS 'NPK Mutiara - Dose/Plot/Times per Year',
            b.FertNPKMutiTimesYear * b.FertNPKMutiDose AS 'NPK Mutiara - Dose/Plot/Year',
            b.FertBoratTimesYear AS 'Borat - Times/Year',
            b.FertBoratDose AS 'Borat - Dose/Plot/Times per Year',
            b.FertBoratTimesYear * b.FertBoratDose AS 'Borat - Dose/Plot/Year',
            b.FertDolomiteTimesYear AS 'Dolomite - Times/Year',
            b.FertDolomiteDose AS 'Dolomite - Dose/Plot/Times per Year',
            b.FertDolomiteTimesYear * b.FertDolomiteDose AS 'Dolomite - Dose/Plot/Year',
        IF
            ( b.FertWithNonOrgaTBM = 1, 'Yes', 'No' ) AS 'Which trees are fertilized with Non Organic/Chemical - singk TBM',
        IF
            ( b.FertWithNonOrgaTM = 1, 'Yes', 'No' ) AS 'Which trees are fertilized with Non Organic/Chemical - singk TM',
        IF
            ( b.FertWithNonOrgaTR = 1, 'Yes', 'No' ) AS 'Which trees are fertilized with Non Organic/Chemical - singk TR',
        IF
            ( b.FertUseOrganic = 1, 'Yes', IF ( b.FertUseOrganic = 2, 'No', '-' ) ) AS 'Do you use compost and/or organic fertilizer',
            b.FertMoneySpentOrganic AS 'How much money did you spend in the past 24 months on Compost and Organic Fertilizers',
            b.FertPBATimesYear AS 'Palm Bunch Ash - Times/Year',
            b.FertPBADose AS 'Palm Bunch Ash - Dose/Plot/Times per Year',
            b.FertPBATimesYear * b.FertPBADose AS 'Palm Bunch Ash - Dose/Plot/Year',
            b.FertPBTimesYear AS 'Palm Bunch - Times/Year',
            b.FertPBDose AS 'Palm Bunch - Dose/Plot/Times per Year',
            b.FertPBTimesYear * b.FertPBDose AS 'Palm Bunch - Dose/Plot/Year',
            b.FertCPBTimesYear AS 'Compost from Palm Bunch - Times/Year',
            b.FertCPBDose AS 'Compost from Palm Bunch - Dose/Plot/Times per Year',
            b.FertCPBTimesYear * b.FertCPBDose AS 'Compost from Palm Bunch - Dose/Plot/Year',
            b.FertManureTimesYear AS 'Manure - Times/Year',
            b.FertManureDose AS 'Manure - Dose/Plot/Times per Year',
            b.FertManureTimesYear * b.FertManureDose AS 'Manure - Dose/Plot/Year',
        IF
            ( b.FertWithOrgaTBM = 1, 'Yes', 'No' ) AS 'Which trees are fertilized using compost and/or Organic - TBM',
        IF
            ( b.FertWithOrgaTM = 1, 'Yes', 'No' ) AS 'Which trees are fertilized using compost and/or Organic - TM',
        IF
            ( b.FertWithOrgaTR = 1, 'Yes', 'No' ) AS 'Which trees are fertilized using compost and/or Organic - TR',
        IF
            ( b.PeUsingHerbicide = 1, 'Yes', 'No' ) AS 'Herbicide - Using Herbicide',
            b.PeMoneySpentHerbi AS 'Herbicide - How much money did you spend in the past 24 months',
            b.PeFreqHerbi AS 'Herbicide - Pesticides Frequency (Times/Year)',
        IF
            ( b.PeHerbi1 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Round Up',
        IF
            ( b.PeHerbi2 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Basmilang',
        IF
            ( b.PeHerbi3 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Pilar Up',
        IF
            ( b.PeHerbi4 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Sun Up',
        IF
            ( b.PeHerbi5 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Gramaxone',
        IF
            ( b.PeHerbi6 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Supremo',
        IF
            ( b.PeHerbi7 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Sapurata',
        IF
            ( b.PeHerbi8 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Rambo',
        IF
            ( b.PeHerbi9 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Para Special',
        IF
            ( b.PeHerbi10 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Noxone',
        IF
            ( b.PeHerbi11 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Paratop',
        IF
            ( b.PeHerbi12 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Bravoxone',
        IF
            ( b.PeHerbi13 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Primaxone',
        IF
            ( b.PeHerbi14 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Bimastar',
        IF
            ( b.PeHerbi15 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Polado',
        IF
            ( b.PeHerbi16 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Primastar',
        IF
            ( b.PeHerbi17 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Rumat',
        IF
            ( b.PeHerbi18 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Supretox',
        IF
            ( b.PeHerbi19 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Kleenup',
        IF
            ( b.PeHerbi20 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Prima Up',
        IF
            ( b.PeHerbi21 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Tanistar',
        IF
            ( b.PeHerbi22 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - DMA',
        IF
            ( b.PeHerbi23 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Polaris',
        IF
            ( b.PeHerbi24 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Konup',
        IF
            ( b.PeHerbi25 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Herbatop',
        IF
            ( b.PeHerbi26 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Mupxone',
        IF
            ( b.PeHerbi27 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Pointer',
        IF
            ( b.PeHerbi28 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Senus',
        IF
            ( b.PeHerbi29 = 1, 'Yes', 'No' ) AS 'Herbicide - Brand - Tamaxon',
            IFNULL( b.PeHerbiOther, 'No' ) AS 'Herbicide - Brand - Other Brand',
        IF
            ( b.PeUsingInsecticide = 1, 'Yes', 'No' ) AS 'Insecticide - Using Insecticide',
            b.PeMoneySpentInsec AS 'Insecticide - How much money did you spend in the past 24 months',
            b.PeFreqInsec AS 'Insecticide - Pesticides Frequency (Times/Year)',
        IF
            ( b.PeInsec1 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Alika',
        IF
            ( b.PeInsec2 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Matador',
        IF
            ( b.PeInsec3 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Capture',
        IF
            ( b.PeInsec4 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Bento',
        IF
            ( b.PeInsec5 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Regent',
        IF
            ( b.PeInsec6 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Drusban',
        IF
            ( b.PeInsec7 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Penalty',
        IF
            ( b.PeInsec8 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Nurelle',
        IF
            ( b.PeInsec9 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Chlormite',
        IF
            ( b.PeInsec10 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Decis',
        IF
            ( b.PeInsec11 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Klensect',
        IF
            ( b.PeInsec12 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Vigor',
        IF
            ( b.PeInsec13 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Unicide',
        IF
            ( b.PeInsec14 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Deicer 505',
        IF
            ( b.PeInsec15 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Arrivo',
        IF
            ( b.PeInsec16 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Sidamethrin',
        IF
            ( b.PeInsec17 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Bestox',
        IF
            ( b.PeInsec18 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Halona',
        IF
            ( b.PeInsec19 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Dangke',
        IF
            ( b.PeInsec20 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Buldok',
        IF
            ( b.PeInsec21 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Laser',
        IF
            ( b.PeInsec22 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Sevin',
        IF
            ( b.PeInsec23 = 1, 'Yes', 'No' ) AS 'Insecticide - Brand - Organic',
            IFNULL( b.PeInsecOther, 'No' ) AS 'Insecticide - Brand - Other Brand',
        IF
            ( b.PeUsingFungicide = 1, 'Yes', 'No' ) AS 'Fungicide - Using Fungicide',
            b.PeMoneySpentFungi AS 'Fungicide - How much money did you spend in the past 24 months',
            b.PeFreqFungi AS 'Fungicide - Pesticides Frequency (Times/Year)',
        IF
            ( b.PeFungi1 = 1, 'Yes', 'No' ) AS 'Fungicide - Brand - Klensect',
        IF
            ( b.PeFungi2 = 1, 'Yes', 'No' ) AS 'Fungicide - Brand - Vigor',
        IF
            ( b.PeFungi3 = 1, 'Yes', 'No' ) AS 'Fungicide - Brand - Unicide',
        IF
            ( b.PeFungi4 = 1, 'Yes', 'No' ) AS 'Fungicide - Brand - Deicer 505',
        IF
            ( b.PeFungi5 = 1, 'Yes', 'No' ) AS 'Fungicide - Brand - Arrivo',
        IF
            ( b.PeFungi6 = 1, 'Yes', 'No' ) AS 'Fungicide - Brand - Sidamethrin',
        IF
            ( b.PeFungi7 = 1, 'Yes', 'No' ) AS 'Fungicide - Brand - Bestox',
        IF
            ( b.PeFungi8 = 1, 'Yes', 'No' ) AS 'Fungicide - Brand - Halona',
        IF
            ( b.PeFungi9 = 1, 'Yes', 'No' ) AS 'Fungicide - Brand - Dangke',
        IF
            ( b.PeFungi10 = 1, 'Yes', 'No' ) AS 'Fungicide - Brand - Buldok',
        IF
            ( b.PeFungi11 = 1, 'Yes', 'No' ) AS 'Fungicide - Brand - Laser',
        IF
            ( b.PeFungi12 = 1, 'Yes', 'No' ) AS 'Fungicide - Brand - Organic',
            IFNULL( b.PeFungiOther, 'No' ) AS 'Fungicide - Brand - Other Brand',
            b.UseProtectiveGear AS 'Do you use protective gears',
            b.EquipHelm AS 'Personal protective equipments used during harvest or garden maintenance - Helm',
            b.EquipBoots AS 'Personal protective equipments used during harvest or garden maintenance - Boots',
            b.EquipDodosProtector AS 'Personal protective equipments used during harvest or garden maintenance - Dodos Protector',
            b.EquipMask AS 'Personal protective equipments used during harvest or garden maintenance - Mask',
            b.EquipGloves AS 'Personal protective equipments used during harvest or garden maintenance - Gloves',
            b.EquipSprayGlasses AS 'Personal protective equipments used during harvest or garden maintenance - Spray Glasses',
            b.EquipEgrekProtector AS 'Personal protective equipments used during harvest or garden maintenance - Egrek Protector',
            b.EquipProtectiveClothing AS 'Personal protective equipments used during harvest or garden maintenance - Protective Clothing',
        CASE
                
                WHEN b.PestStoreLocation = 1 THEN
                'In the house' 
                WHEN b.PestStoreLocation = 2 THEN
                'Pesticide specific place' 
                WHEN b.PestStoreLocation = 3 THEN
                'Outside of the house (house area)' 
                WHEN b.PestStoreLocation = 4 THEN
                'Outside of the cocoa farm' 
                WHEN b.PestStoreLocation = 5 THEN
                'Others' ELSE '-' 
            END AS 'Where you stored pesticides before and after using it',
        CASE
                
                WHEN b.PestPackageAfterUse = 1 THEN
                'Random disposal (Garden or around the house)' 
                WHEN b.PestPackageAfterUse = 2 THEN
                'Use for something else' 
                WHEN b.PestPackageAfterUse = 3 THEN
                'Thoroughly and then burry it' 
                WHEN b.PestPackageAfterUse = 4 THEN
                'Burn' 
                WHEN b.PestPackageAfterUse = 5 THEN
                'Recycle' 
                WHEN b.PestPackageAfterUse = 6 THEN
                'Others' ELSE '-' 
            END AS 'What you do to pesticide package after using it',
        IF
            ( b.PestMainRats = 1, 'Yes', 'No' ) AS 'Main Pest on Plantation - Rats',
        IF
            ( b.PestMainOly = 1, 'Yes', 'No' ) AS 'Main Pest on Plantation - Olygonichus',
        IF
            ( b.PestMainSatora = 1, 'Yes', 'No' ) AS 'Main Pest on Plantation - Satora Nitens',
        IF
            ( b.PestMainTira = 1, 'Yes', 'No' ) AS 'Main Pest on Plantation - Tirathaba Mundella',
        IF
            ( b.PestMainRhino = 1, 'Yes', 'No' ) AS 'Main Pest on Plantation - Rhinoceros Beetle',
        IF
            ( b.PestMainElep = 1, 'Yes', 'No' ) AS 'Main Pest on Plantation - Elephant',
        IF
            ( b.PestMainOrgUtan = 1, 'Yes', 'No' ) AS 'Main Pest on Plantation - Orang Utan',
        IF
            ( b.PestMainLandak = 1, 'Yes', 'No' ) AS 'Main Pest on Plantation - Hedgehog',
        IF
            ( b.PestMainBabi = 1, 'Yes', 'No' ) AS 'Main Pest on Plantation - Boar',
        IF
            ( b.PestMainOther = 1, b.PestMainOtherText, 'No' ) AS 'Main Pest on Plantation - Others',
        IF
            ( b.DisMainBlast = 1, 'Yes', 'No' ) AS 'Main Disease on Plantation - Blast Disease',
        IF
            ( b.DisMainGeno = 1, 'Yes', 'No' ) AS 'Main Disease on Plantation - Basal Steam Root / Genoderma',
        IF
            ( b.DisMainSteam = 1, 'Yes', 'No' ) AS 'Main Disease on Plantation - Upper Steam Rot',
        IF
            ( b.DisMainBud = 1, 'Yes', 'No' ) AS 'Main Disease on Plantation - Bud Rot',
        IF
            ( b.DisMainSpear = 1, 'Yes', 'No' ) AS 'Main Disease on Plantation - Spear Rot',
        IF
            ( b.DisMainYellow = 1, 'Yes', 'No' ) AS 'Main Disease on Plantation - Patch Yellow',
        IF
            ( b.DisMainAnt = 1, 'Yes', 'No' ) AS 'Main Disease on Plantation - Anthracnose',
        IF
            ( b.DisMainCrown = 1, 'Yes', 'No' ) AS 'Main Disease on Plantation - Crown disease',
        IF
            ( b.DisMainViscular = 1, 'Yes', 'No' ) AS 'Main Disease on Plantation - Viscular Wilt',
        IF
            ( b.DisMainBunch = 1, 'Yes', 'No' ) AS 'Main Disease on Plantation - Bunch Rot',
        IF
            ( b.DisMainOther = 1, b.DisMainOtherText, 'No' ) AS 'Main Disease on Plantation - Others' 
        FROM
            ktv_members a
            LEFT JOIN ktv_survey_plot b ON a.MemberID = b.MemberID
            LEFT JOIN ktv_village c ON b.VillageID = c.VillageID
            LEFT JOIN ktv_subdistrict d ON c.SubDistrictID = d.SubDistrictID
            LEFT JOIN ktv_district e ON d.DistrictID = e.DistrictID
            LEFT JOIN ktv_province f ON e.ProvinceID = f.ProvinceID
            LEFT JOIN ktv_survey g ON b.SurveyNr = g.SurveyNr
            LEFT JOIN ktv_access_partner_member apm ON apm.apmMemberID = a.MemberID
            LEFT JOIN ktv_program_partner pp ON pp.PartnerID = apm.apmPartnerID 
        WHERE
            b.StatusCode = 'active' 
            AND a.StatusCode = 'active' 
            AND apm.apmPartnerID IN ( 78, 179 )
        ";                

        $query = $this->db->query($sql);

        $data["data"]   = $query->result_array();
        $data["total"]  = $query->num_rows();

        return $data;
    }
}