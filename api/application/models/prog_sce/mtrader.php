<?php
/**
 * @Author: nikolius
 * @Date:   2017-03-20 10:11:20
 */
class Mtrader extends CI_Model
{
    public function getTraderSurList($FarmerID){
        $sql="SELECT
                a.TraderSurID
                , a.SurveyYear
                , a.InterviewDate
                , a.`DateCreated`
                , IFNULL((SELECT UserRealName FROM sys_user WHERE UserId = a.`CreatedBy`),'-') AS CreatedBy
                , IFNULL(a.`DateUpdated`,'-') AS DateUpdated
                , IFNULL((SELECT UserRealName FROM sys_user WHERE UserId = a.`LastModifiedBy`),'-') AS LastModifiedBy
            FROM
                ktv_trader_surveys a
            WHERE
                a.`StatusCode` = 'active'
                AND a.`ObjType` = 'farmer'
                AND a.`ObjID` = ?
            ORDER BY a.`SurveyYear` ASC";
        $query = $this->db->query($sql,array((int) $FarmerID));
        $data = $query->result_array();

        $result['data'] = $data;
        return $result;
    }

    public function getFormTraderSurvey($TraderSurID){
        $sql="SELECT
              a.`TraderSurID` AS tSurTraderSurID,
              a.`SurveyYear` AS tSurSurveyYear,
              a.`InterviewDate` AS tSurInterviewDate,
              a.`Name` AS tSurName,
              a.`CompanyName` AS tSurCompanyName,
              a.`BirthDate` AS tSurBirthDate,
              a.`Address` AS tSurAddress,
              a.`NoKTP` AS tSurNoKTP,
              a.`Gender` AS tSurGender,
              a.`Handphone` AS tSurHandphone,
              a.`Email` AS tSurEmail,
              a.`Latitude` AS tSurLatitude,
              a.`Longitude` AS tSurLongitude,
              a.`LastEducation` AS tSurLastEducation,
              a.`FulltimeTrader` AS tSurFulltimeTrader,
              a.`StatusTrader` AS tSurStatusTrader,
              a.`YearRunningTrader` AS tSurYearRunningTrader,
              a.`NrFulltimeStaffMale` AS tSurNrFulltimeStaffMale,
              a.`NrFulltimeStaffFemale` AS tSurNrFulltimeStaffFemale,
              a.`NrParttimeStaffMale` AS tSurNrParttimeStaffMale,
              a.`NrParttimeStaffFemale` AS tSurNrParttimeStaffFemale,
              a.`ComodityCacaoSalePercentage` AS tSurComodityCacaoSalePercentage,
              a.`ComodityOtherSalePercentage` AS tSurComodityOtherSalePercentage,
              a.`BuyWetBeans` AS tSurBuyWetBeans,
              a.`BuyFermentBeans` AS tSurBuyFermentBeans,
              a.`BuyDryBeans` AS tSurBuyDryBeans,
              a.`NrTransWetBeansHighHarvest` AS tSurNrTransWetBeansHighHarvest,
              a.`NrVolumeWetBeansHighHarvest` AS tSurNrVolumeWetBeansHighHarvest,
              a.`NrTransWetBeansNormalHarvest` AS tSurNrTransWetBeansNormalHarvest,
              a.`NrVolumeWetBeansNormalHarvest` AS tSurNrVolumeWetBeansNormalHarvest,
              a.`NrTransWetBeansLowHarvest` AS tSurNrTransWetBeansLowHarvest,
              a.`NrVolumeWetBeansLowHarvest` AS tSurNrVolumeWetBeansLowHarvest,
              a.`NrTransFermentBeansHighHarvest` AS tSurNrTransFermentBeansHighHarvest,
              a.`NrVolumeFermentBeansHighHarvest` AS tSurNrVolumeFermentBeansHighHarvest,
              a.`NrTransFermentBeansNormalHarvest` AS tSurNrTransFermentBeansNormalHarvest,
              a.`NrVolumeFermentBeansNormalHarvest` AS tSurNrVolumeFermentBeansNormalHarvest,
              a.`NrTransFermentBeansLowHarvest` AS tSurNrTransFermentBeansLowHarvest,
              a.`NrVolumeFermentBeansLowHarvest` AS tSurNrVolumeFermentBeansLowHarvest,
              a.`NrTransDryBeansHighHarvest` AS tSurNrTransDryBeansHighHarvest,
              a.`NrVolumeDryBeansHighHarvest` AS tSurNrVolumeDryBeansHighHarvest,
              a.`NrTransDryBeansNormalHarvest` AS tSurNrTransDryBeansNormalHarvest,
              a.`NrVolumeDryBeansNormalHarvest` AS tSurNrVolumeDryBeansNormalHarvest,
              a.`NrTransDryBeansLowHarvest` AS tSurNrTransDryBeansLowHarvest,
              a.`NrVolumeDryBeansLowHarvest` AS tSurNrVolumeDryBeansLowHarvest,
              a.`NrCacaoFrequentBuyer` AS tSurNrCacaoFrequentBuyer,
              a.`NrCacaoNormalBuyer` AS tSurNrCacaoNormalBuyer,
              a.`CacaoActivitySellBuyDryBeans` AS tSurCacaoActivitySellBuyDryBeans,
              a.`CacaoActivitySellBuyFermentBeans` AS tSurCacaoActivitySellBuyFermentBeans,
              a.`CacaoActivitySellPest` AS tSurCacaoActivitySellPest,
              a.`CacaoActivitySellFertilizer` AS tSurCacaoActivitySellFertilizer,
              a.`CacaoActivityLoanToFarmer` AS tSurCacaoActivityLoanToFarmer,
              a.`UseToolDigitalScale` AS tSurUseToolDigitalScale,
              a.`UseToolManualScale` AS tSurUseToolManualScale,
              a.`UseToolAquaboy` AS tSurUseToolAquaboy,
              a.`UseToolSolarDryer` AS tSurUseToolSolarDryer,
              a.`UseToolFuelDryer` AS tSurUseToolFuelDryer,
              a.`UseToolAyakMachine` AS tSurUseToolAyakMachine,
              a.`UseToolFloorDryer` AS tSurUseToolFloorDryer,
              a.`UseToolWarehouse` AS tSurUseToolWarehouse,
              a.`UseToolFermentBox` AS tSurUseToolFermentBox,
              a.`FundValueFromSelf` AS tSurFundValueFromSelf,
              a.`FundValueFromLoan` AS tSurFundValueFromLoan,
              a.`PriceSource` AS tSurPriceSource,
              a.`AverageMarginCacaoPerKgReceivedMin` AS tSurAverageMarginCacaoPerKgReceivedMin,
              a.`AverageMarginCacaoPerKgReceivedMax` AS tSurAverageMarginCacaoPerKgReceivedMax,
              a.`QualityCheckCacaoBeans` AS tSurQualityCheckCacaoBeans,
              a.`WhenPayClient` AS tSurWhenPayClient,
              a.`PayClientMethod` AS tSurPayClientMethod,
              a.`SellCertifiedCacaoBeans` AS tSurSellCertifiedCacaoBeans,
              a.`KnownCertifiedCacaoBeans` AS tSurKnownCertifiedCacaoBeans,
              a.`KnownNonCertifiedCacaoBeans` AS tSurKnownNonCertifiedCacaoBeans,
              a.`UseSystemTraceCertifiedCacaoBeans` AS tSurUseSystemTraceCertifiedCacaoBeans,
              a.`UseSystemTraceNonCertifiedCacaoBeans` AS tSurUseSystemTraceNonCertifiedCacaoBeans,
              a.`TraceSellingCertifiedCacaoBeans` AS tSurTraceSellingCertifiedCacaoBeans,
              a.`TraceSellingNonCertifiedCacaoBeans` AS tSurTraceSellingNonCertifiedCacaoBeans,
              a.`RecordTransCertifiedCacaoBeans` AS tSurRecordTransCertifiedCacaoBeans,
              a.`RecordTransNonCertifiedCacaoBeans` AS tSurRecordTransNonCertifiedCacaoBeans,
              a.`AnalyzeTransCertifiedCacaoBeans` AS tSurAnalyzeTransCertifiedCacaoBeans,
              a.`AnalyzeTransNonCertifiedCacaoBeans` AS tSurAnalyzeTransNonCertifiedCacaoBeans,
              a.`ShowAnalyzeResult` AS tSurShowAnalyzeResult,
              a.`BusinessModel` AS tSurBusinessModel,
              a.`BusinessModelOther` AS tSurBusinessModelOther,
              a.`SellToBigTrader` AS tSurSellToBigTrader,
              a.`SellToCoop` AS tSurSellToCoop,
              a.`SellToBigCompany` AS tSurSellToBigCompany,
              a.`SellToFactory` AS tSurSellToFactory,
              a.`SellToExport` AS tSurSellToExport,
              a.`SellToLoaner` AS tSurSellToLoaner,
              a.`SellToOther` AS tSurSellToOther,
              a.`SellToOtherText` AS tSurSellToOtherText,
              a.`ChooseBuyerContract` AS tSurChooseBuyerContract,
              a.`ChooseBuyerHighestValue` AS tSurChooseBuyerHighestValue,
              a.`ChooseBuyerDistance` AS tSurChooseBuyerDistance,
              a.`ChooseBuyerFastPayment` AS tSurChooseBuyerFastPayment,
              a.`ChooseBuyerFacility` AS tSurChooseBuyerFacility,
              a.`ChooseBuyerFundingSource` AS tSurChooseBuyerFundingSource,
              a.`BuyerInfoDetail` AS tSurBuyerInfoDetail,
              a.`ProblemBuycacaoFund` AS tSurProblemBuycacaoFund,
              a.`ProblemBuycacaoQuality` AS tSurProblemBuycacaoQuality,
              a.`ProblemBuycacaoTransport` AS tSurProblemBuycacaoTransport,
              a.`ProblemBuycacaoPriceFluc` AS tSurProblemBuycacaoPriceFluc,
              a.`ProblemBuycacaoPriceComp` AS tSurProblemBuycacaoPriceComp,
              a.`IsCacaoFarmer` AS tSurIsCacaoFarmer,
              a.`CacaoLandSize` AS tSurCacaoLandSize,
              a.`AverageProduction` AS tSurAverageProduction,
              a.`IsExCacaoFarmer` AS tSurIsExCacaoFarmer,
              a.`ExCacaoLandSize` AS tSurExCacaoLandSize,
              a.`ExAverageProduction` AS tSurExAverageProduction,
              a.`ProvideFertPest` AS tSurProvideFertPest,
              a.`ProvideLoan` AS tSurProvideLoan,
              a.`LoanCreditCount` AS tSurLoanCreditCount,
              a.`LoanCreditValueTotal` AS tSurLoanCreditValueTotal,
              a.`PayLoanMethod` AS tSurPayLoanMethod,
              a.`PayLoanMethodOther` AS tSurPayLoanMethodOther,
              a.`LoanerHaveTo` AS tSurLoanerHaveTo,
              a.`LossAction` AS tSurLossAction,
              a.`IsBankAgent` AS tSurIsBankAgent,
              a.`HaveOtherBusiness` AS tSurHaveOtherBusiness,
              a.`FarmerMainProblemLowProd` AS tSurFarmerMainProblemLowProd,
              a.`FarmerMainProblemOldTree` AS tSurFarmerMainProblemOldTree,
              a.`FarmerMainProblemNoKnowledge` AS tSurFarmerMainProblemNoKnowledge,
              a.`FarmerMainProblemPest` AS tSurFarmerMainProblemPest,
              a.`FarmerMainProblemPestSolving` AS tSurFarmerMainProblemPestSolving,
              a.`FarmerMainProblemSeasonChanging` AS tSurFarmerMainProblemSeasonChanging,
              a.`FarmerMainProblemDisease` AS tSurFarmerMainProblemDisease,
              a.`FarmerMainProblemLand` AS tSurFarmerMainProblemLand,
              a.`FarmerMainProblemLackSkill` AS tSurFarmerMainProblemLackSkill,
              a.`FarmerMainProblemOtherComodity` AS tSurFarmerMainProblemOtherComodity,
              a.`FarmerMainProblemLowPrice` AS tSurFarmerMainProblemLowPrice,
              a.`MorethanOneBankAcc` AS tSurMorethanOneBankAcc,
              a.`BankTransactionFreq` AS tSurBankTransactionFreq,
              a.`SavingAsideFund` AS tSurSavingAsideFund,
              a.`FundValue` AS tSurFundValue,
              a.`HaveLoanBefore` AS tSurHaveLoanBefore,
              a.`LastLoanValue` AS tSurLastLoanValue,
              a.`LastLoanSettle` AS tSurLastLoanSettle,
              a.`LastLoanCreditValue` AS tSurLastLoanCreditValue,
              a.`LastLoanSourceTrader` AS tSurLastLoanSourceTrader,
              a.`LastLoanSourceFamily` AS tSurLastLoanSourceFamily,
              a.`LastLoanSourceLoaner` AS tSurLastLoanSourceLoaner,
              a.`LastLoanSourceBank` AS tSurLastLoanSourceBank,
              a.`LastLoanSourceCoop` AS tSurLastLoanSourceCoop,
              a.`LastLoanSourceOther` AS tSurLastLoanSourceOther,
              a.`LastLoanSourceOtherText` AS tSurLastLoanSourceOtherText,
              a.`PayingStaffFixedSalary` AS tSurPayingStaffFixedSalary,
              a.`PayingStaffCommision` AS tSurPayingStaffCommision,
              a.`PayingStaffFamilyNoPayment` AS tSurPayingStaffFamilyNoPayment,
              a.`TrustedTrader` AS tSurTrustedTrader,
              a.`NeedLoanAndQualify` AS tSurNeedLoanAndQualify,
              a.`CacaoTraderIsProfitable` AS tSurCacaoTraderIsProfitable,
              a.`WealthyPersonInSociety` AS tSurWealthyPersonInSociety
            FROM
                `ktv_trader_surveys` a
            WHERE
                a.`TraderSurID` = ?
            LIMIT 1";
        $query = $this->db->query($sql,array((int) $TraderSurID));

        $return['success'] = true;
        $return['data'] = $query->row_array();
        return $return;
    }

    public function insertTraderSurvey($post){
        $this->db->trans_start();

        //tambahkan yg perlu
        $post['ObjID'] = $post['tSurFarmerID'];
        $post['ObjType'] = 'farmer';

        //yg tidak diperlukan untuk insert
        unset($post['tSurTraderName']);
        unset($post['tSurFarmerID']);

        foreach ($post as $k => $v) {
            $k = str_replace("tSur", "", $k);
            $insert[$k] = $v;

            //cek yg perlu default value
            if ($insert[$k] == "")
                $insert[$k] = NULL;
        }

        $insert['StatusCode'] = 'active';
        $insert['DateCreated'] = date('Y-m-d H:i:s');
        $insert['CreatedBy'] = $_SESSION['userid'];
        $this->db->insert('ktv_trader_surveys', $insert);

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Data saved";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        }
        return $results;
    }

    public function updateTraderSurvey($post){
        $this->db->trans_start();

        //tambahkan yg perlu
        $TraderSurID = $post['tSurTraderSurID'];
        $post['ObjID'] = $post['tSurFarmerID'];
        $post['ObjType'] = 'farmer';

        //yg tidak diperlukan untuk update
        unset($post['tSurTraderSurID']);
        unset($post['tSurTraderName']);
        unset($post['tSurFarmerID']);

        foreach ($post as $k => $v) {
            $k = str_replace("tSur", "", $k);
            $update[$k] = $v;

            //cek yg perlu default value
            if ($update[$k] == "")
                $update[$k] = NULL;
        }
        $update['DateUpdated'] = date('Y-m-d H:i:s');
        $update['LastModifiedBy'] = $_SESSION['userid'];

        //reset semuanya dulu..
        $sql="UPDATE `ktv_trader_surveys` SET
                  `Name` = NULL,
                  `CompanyName` = NULL,
                  `BirthDate` = NULL,
                  `Address` = NULL,
                  `NoKTP` = NULL,
                  `Gender` = NULL,
                  `Handphone` = NULL,
                  `Email` = NULL,
                  `Latitude` = NULL,
                  `Longitude` = NULL,
                  `LastEducation` = NULL,
                  `FulltimeTrader` = NULL,
                  `StatusTrader` = NULL,
                  `YearRunningTrader` = NULL,
                  `NrFulltimeStaffMale` = NULL,
                  `NrFulltimeStaffFemale` = NULL,
                  `NrParttimeStaffMale` = NULL,
                  `NrParttimeStaffFemale` = NULL,
                  `ComodityCacaoSalePercentage` = NULL,
                  `ComodityOtherSalePercentage` = NULL,
                  `BuyWetBeans` = NULL,
                  `BuyFermentBeans` = NULL,
                  `BuyDryBeans` = NULL,
                  `NrTransWetBeansHighHarvest` = NULL,
                  `NrVolumeWetBeansHighHarvest` = NULL,
                  `NrTransWetBeansNormalHarvest` = NULL,
                  `NrVolumeWetBeansNormalHarvest` = NULL,
                  `NrTransWetBeansLowHarvest` = NULL,
                  `NrVolumeWetBeansLowHarvest` = NULL,
                  `NrTransFermentBeansHighHarvest` = NULL,
                  `NrVolumeFermentBeansHighHarvest` = NULL,
                  `NrTransFermentBeansNormalHarvest` = NULL,
                  `NrVolumeFermentBeansNormalHarvest` = NULL,
                  `NrTransFermentBeansLowHarvest` = NULL,
                  `NrVolumeFermentBeansLowHarvest` = NULL,
                  `NrTransDryBeansHighHarvest` = NULL,
                  `NrVolumeDryBeansHighHarvest` = NULL,
                  `NrTransDryBeansNormalHarvest` = NULL,
                  `NrVolumeDryBeansNormalHarvest` = NULL,
                  `NrTransDryBeansLowHarvest` = NULL,
                  `NrVolumeDryBeansLowHarvest` = NULL,
                  `NrCacaoFrequentBuyer` = NULL,
                  `NrCacaoNormalBuyer` = NULL,
                  `CacaoActivitySellBuyDryBeans` = NULL,
                  `CacaoActivitySellBuyFermentBeans` = NULL,
                  `CacaoActivitySellPest` = NULL,
                  `CacaoActivitySellFertilizer` = NULL,
                  `CacaoActivityLoanToFarmer` = NULL,
                  `UseToolDigitalScale` = NULL,
                  `UseToolManualScale` = NULL,
                  `UseToolAquaboy` = NULL,
                  `UseToolSolarDryer` = NULL,
                  `UseToolFuelDryer` = NULL,
                  `UseToolAyakMachine` = NULL,
                  `UseToolFloorDryer` = NULL,
                  `UseToolWarehouse` = NULL,
                  `UseToolFermentBox` = NULL,
                  `FundValueFromSelf` = NULL,
                  `FundValueFromLoan` = NULL,
                  `PriceSource` = NULL,
                  `AverageMarginCacaoPerKgReceivedMin` = NULL,
                  `AverageMarginCacaoPerKgReceivedMax` = NULL,
                  `QualityCheckCacaoBeans` = NULL,
                  `WhenPayClient` = NULL,
                  `PayClientMethod` = NULL,
                  `SellCertifiedCacaoBeans` = NULL,
                  `KnownCertifiedCacaoBeans` = NULL,
                  `KnownNonCertifiedCacaoBeans` = NULL,
                  `UseSystemTraceCertifiedCacaoBeans` = NULL,
                  `UseSystemTraceNonCertifiedCacaoBeans` = NULL,
                  `TraceSellingCertifiedCacaoBeans` = NULL,
                  `TraceSellingNonCertifiedCacaoBeans` = NULL,
                  `RecordTransCertifiedCacaoBeans` = NULL,
                  `RecordTransNonCertifiedCacaoBeans` = NULL,
                  `AnalyzeTransCertifiedCacaoBeans` = NULL,
                  `AnalyzeTransNonCertifiedCacaoBeans` = NULL,
                  `ShowAnalyzeResult` = NULL,
                  `BusinessModel` = NULL,
                  `BusinessModelOther` = NULL,
                  `SellToBigTrader` = NULL,
                  `SellToCoop` = NULL,
                  `SellToBigCompany` = NULL,
                  `SellToFactory` = NULL,
                  `SellToExport` = NULL,
                  `SellToLoaner` = NULL,
                  `SellToOther` = NULL,
                  `SellToOtherText` = NULL,
                  `ChooseBuyerContract` = NULL,
                  `ChooseBuyerHighestValue` = NULL,
                  `ChooseBuyerDistance` = NULL,
                  `ChooseBuyerFastPayment` = NULL,
                  `ChooseBuyerFacility` = NULL,
                  `ChooseBuyerFundingSource` = NULL,
                  `BuyerInfoDetail` = NULL,
                  `ProblemBuycacaoFund` = NULL,
                  `ProblemBuycacaoQuality` = NULL,
                  `ProblemBuycacaoTransport` = NULL,
                  `ProblemBuycacaoPriceFluc` = NULL,
                  `ProblemBuycacaoPriceComp` = NULL,
                  `IsCacaoFarmer` = NULL,
                  `CacaoLandSize` = NULL,
                  `AverageProduction` = NULL,
                  `IsExCacaoFarmer` = NULL,
                  `ExCacaoLandSize` = NULL,
                  `ExAverageProduction` = NULL,
                  `ProvideFertPest` = NULL,
                  `ProvideLoan` = NULL,
                  `LoanCreditCount` = NULL,
                  `LoanCreditValueTotal` = NULL,
                  `PayLoanMethod` = NULL,
                  `PayLoanMethodOther` = NULL,
                  `LoanerHaveTo` = NULL,
                  `LossAction` = NULL,
                  `IsBankAgent` = NULL,
                  `HaveOtherBusiness` = NULL,
                  `FarmerMainProblemLowProd` = NULL,
                  `FarmerMainProblemOldTree` = NULL,
                  `FarmerMainProblemNoKnowledge` = NULL,
                  `FarmerMainProblemPest` = NULL,
                  `FarmerMainProblemPestSolving` = NULL,
                  `FarmerMainProblemSeasonChanging` = NULL,
                  `FarmerMainProblemDisease` = NULL,
                  `FarmerMainProblemLand` = NULL,
                  `FarmerMainProblemLackSkill` = NULL,
                  `FarmerMainProblemOtherComodity` = NULL,
                  `FarmerMainProblemLowPrice` = NULL,
                  `MorethanOneBankAcc` = NULL,
                  `BankTransactionFreq` = NULL,
                  `SavingAsideFund` = NULL,
                  `FundValue` = NULL,
                  `HaveLoanBefore` = NULL,
                  `LastLoanValue` = NULL,
                  `LastLoanSettle` = NULL,
                  `LastLoanCreditValue` = NULL,
                  `LastLoanSourceTrader` = NULL,
                  `LastLoanSourceFamily` = NULL,
                  `LastLoanSourceLoaner` = NULL,
                  `LastLoanSourceBank` = NULL,
                  `LastLoanSourceCoop` = NULL,
                  `LastLoanSourceOther` = NULL,
                  `LastLoanSourceOtherText` = NULL,
                  `PayingStaffFixedSalary` = NULL,
                  `PayingStaffCommision` = NULL,
                  `PayingStaffFamilyNoPayment` = NULL,
                  `TrustedTrader` = NULL,
                  `NeedLoanAndQualify` = NULL,
                  `CacaoTraderIsProfitable` = NULL,
                  `WealthyPersonInSociety` = NULL
                WHERE
                    `TraderSurID` = ?
                LIMIT 1";
        $query = $this->db->query($sql, array($TraderSurID));

        $this->db->where('TraderSurID', $TraderSurID);
        $query = $this->db->update('ktv_trader_surveys', $update);

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Data saved";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        }
        return $results;
    }

    public function deleteTraderSurvey($TraderSurID){
        $this->db->trans_begin();

        $sql="INSERT INTO `his_ktv_trader_surveys` (
                  `DateHistory`,
                  `DeleteBy`,
                  `TraderSurID`,
                  `ObjType`,
                  `ObjID`,
                  `SurveyYear`,
                  `InterviewDate`,
                  `Name`,
                  `CompanyName`,
                  `BirthDate`,
                  `Address`,
                  `NoKTP`,
                  `Gender`,
                  `Handphone`,
                  `Email`,
                  `Latitude`,
                  `Longitude`,
                  `LastEducation`,
                  `FulltimeTrader`,
                  `StatusTrader`,
                  `YearRunningTrader`,
                  `NrFulltimeStaffMale`,
                  `NrFulltimeStaffFemale`,
                  `NrParttimeStaffMale`,
                  `NrParttimeStaffFemale`,
                  `ComodityCacaoSalePercentage`,
                  `ComodityOtherSalePercentage`,
                  `BuyWetBeans`,
                  `BuyFermentBeans`,
                  `BuyDryBeans`,
                  `NrTransWetBeansHighHarvest`,
                  `NrVolumeWetBeansHighHarvest`,
                  `NrTransWetBeansNormalHarvest`,
                  `NrVolumeWetBeansNormalHarvest`,
                  `NrTransWetBeansLowHarvest`,
                  `NrVolumeWetBeansLowHarvest`,
                  `NrTransFermentBeansHighHarvest`,
                  `NrVolumeFermentBeansHighHarvest`,
                  `NrTransFermentBeansNormalHarvest`,
                  `NrVolumeFermentBeansNormalHarvest`,
                  `NrTransFermentBeansLowHarvest`,
                  `NrVolumeFermentBeansLowHarvest`,
                  `NrTransDryBeansHighHarvest`,
                  `NrVolumeDryBeansHighHarvest`,
                  `NrTransDryBeansNormalHarvest`,
                  `NrVolumeDryBeansNormalHarvest`,
                  `NrTransDryBeansLowHarvest`,
                  `NrVolumeDryBeansLowHarvest`,
                  `NrCacaoFrequentBuyer`,
                  `NrCacaoNormalBuyer`,
                  `CacaoActivitySellBuyDryBeans`,
                  `CacaoActivitySellBuyFermentBeans`,
                  `CacaoActivitySellPest`,
                  `CacaoActivitySellFertilizer`,
                  `CacaoActivityLoanToFarmer`,
                  `UseToolDigitalScale`,
                  `UseToolManualScale`,
                  `UseToolAquaboy`,
                  `UseToolSolarDryer`,
                  `UseToolFuelDryer`,
                  `UseToolAyakMachine`,
                  `UseToolFloorDryer`,
                  `UseToolWarehouse`,
                  `UseToolFermentBox`,
                  `FundValueFromSelf`,
                  `FundValueFromLoan`,
                  `PriceSource`,
                  `AverageMarginCacaoPerKgReceivedMin`,
                  `AverageMarginCacaoPerKgReceivedMax`,
                  `QualityCheckCacaoBeans`,
                  `WhenPayClient`,
                  `PayClientMethod`,
                  `SellCertifiedCacaoBeans`,
                  `KnownCertifiedCacaoBeans`,
                  `KnownNonCertifiedCacaoBeans`,
                  `UseSystemTraceCertifiedCacaoBeans`,
                  `UseSystemTraceNonCertifiedCacaoBeans`,
                  `TraceSellingCertifiedCacaoBeans`,
                  `TraceSellingNonCertifiedCacaoBeans`,
                  `RecordTransCertifiedCacaoBeans`,
                  `RecordTransNonCertifiedCacaoBeans`,
                  `AnalyzeTransCertifiedCacaoBeans`,
                  `AnalyzeTransNonCertifiedCacaoBeans`,
                  `ShowAnalyzeResult`,
                  `BusinessModel`,
                  `BusinessModelOther`,
                  `SellToBigTrader`,
                  `SellToCoop`,
                  `SellToBigCompany`,
                  `SellToFactory`,
                  `SellToExport`,
                  `SellToLoaner`,
                  `SellToOther`,
                  `SellToOtherText`,
                  `ChooseBuyerContract`,
                  `ChooseBuyerHighestValue`,
                  `ChooseBuyerDistance`,
                  `ChooseBuyerFastPayment`,
                  `ChooseBuyerFacility`,
                  `ChooseBuyerFundingSource`,
                  `BuyerInfoDetail`,
                  `ProblemBuycacaoFund`,
                  `ProblemBuycacaoQuality`,
                  `ProblemBuycacaoTransport`,
                  `ProblemBuycacaoPriceFluc`,
                  `ProblemBuycacaoPriceComp`,
                  `IsCacaoFarmer`,
                  `CacaoLandSize`,
                  `AverageProduction`,
                  `IsExCacaoFarmer`,
                  `ExCacaoLandSize`,
                  `ExAverageProduction`,
                  `ProvideFertPest`,
                  `ProvideLoan`,
                  `LoanCreditCount`,
                  `LoanCreditValueTotal`,
                  `PayLoanMethod`,
                  `PayLoanMethodOther`,
                  `LoanerHaveTo`,
                  `LossAction`,
                  `IsBankAgent`,
                  `HaveOtherBusiness`,
                  `FarmerMainProblemLowProd`,
                  `FarmerMainProblemOldTree`,
                  `FarmerMainProblemNoKnowledge`,
                  `FarmerMainProblemPest`,
                  `FarmerMainProblemPestSolving`,
                  `FarmerMainProblemSeasonChanging`,
                  `FarmerMainProblemDisease`,
                  `FarmerMainProblemLand`,
                  `FarmerMainProblemLackSkill`,
                  `FarmerMainProblemOtherComodity`,
                  `FarmerMainProblemLowPrice`,
                  `MorethanOneBankAcc`,
                  `BankTransactionFreq`,
                  `SavingAsideFund`,
                  `FundValue`,
                  `HaveLoanBefore`,
                  `LastLoanValue`,
                  `LastLoanSettle`,
                  `LastLoanCreditValue`,
                  `LastLoanSourceTrader`,
                  `LastLoanSourceFamily`,
                  `LastLoanSourceLoaner`,
                  `LastLoanSourceBank`,
                  `LastLoanSourceCoop`,
                  `LastLoanSourceOther`,
                  `LastLoanSourceOtherText`,
                  `PayingStaffFixedSalary`,
                  `PayingStaffCommision`,
                  `PayingStaffFamilyNoPayment`,
                  `TrustedTrader`,
                  `NeedLoanAndQualify`,
                  `CacaoTraderIsProfitable`,
                  `WealthyPersonInSociety`,
                  `StatusCode`,
                  `DateSync`,
                  `DateSynced`,
                  `DateCreated`,
                  `CreatedBy`,
                  `DateUpdated`,
                  `LastModifiedBy`,
                  `uid`
                )
                SELECT
                    NOW(), ?, a.*
                FROM
                    ktv_trader_surveys a
                WHERE
                    a.TraderSurID = ?
                LIMIT 1
                ";
        $this->db->query($sql, array($_SESSION['userid'], $TraderSurID));

        $sql = "DELETE FROM ktv_trader_surveys WHERE TraderSurID = ? LIMIT 1";
        $this->db->query($sql, array($TraderSurID));

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to delete data";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Data deleted";
        }
        return $results;
    }

}

?>