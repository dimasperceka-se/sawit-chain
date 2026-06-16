<?php
/**
 * @Author: nikolius
 * @Date:   2017-03-20 10:10:09
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Prog_sce_trader extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('prog_sce/mtrader');
    }

    public function traderSurList_get(){
        $data = $this->mtrader->getTraderSurList($this->get('FarmerID'));
        $this->response($data, 200);
    }

    public function traderSurveyGetForm_get(){
        $data = $this->mtrader->getFormTraderSurvey($this->get('TraderSurID'));
        $this->response($data, 200);
    }

    public function traderSurvey_post(){
        $post = $this->post();

        //replace datanya jika ada koma (begin)
        $post['tSurLatitude'] = str_replace(",", "", $post['tSurLatitude']);
        $post['tSurLongitude'] = str_replace(",", "", $post['tSurLongitude']);
        $post['tSurFundValueFromSelf'] = str_replace(",", "", $post['tSurFundValueFromSelf']);
        $post['tSurFundValueFromLoan'] = str_replace(",", "", $post['tSurFundValueFromLoan']);
        $post['tSurAverageMarginCacaoPerKgReceivedMin'] = str_replace(",", "", $post['tSurAverageMarginCacaoPerKgReceivedMin']);
        $post['tSurAverageMarginCacaoPerKgReceivedMax'] = str_replace(",", "", $post['tSurAverageMarginCacaoPerKgReceivedMax']);
        $post['tSurCacaoLandSize'] = str_replace(",", "", $post['tSurCacaoLandSize']);
        $post['tSurAverageProduction'] = str_replace(",", "", $post['tSurAverageProduction']);
        $post['tSurExCacaoLandSize'] = str_replace(",", "", $post['tSurExCacaoLandSize']);
        $post['tSurExAverageProduction'] = str_replace(",", "", $post['tSurExAverageProduction']);
        $post['tSurLoanCreditCount'] = str_replace(",", "", $post['tSurLoanCreditCount']);
        $post['tSurLoanCreditValueTotal'] = str_replace(",", "", $post['tSurLoanCreditValueTotal']);
        $post['tSurFundValue'] = str_replace(",", "", $post['tSurFundValue']);
        $post['tSurLastLoanValue'] = str_replace(",", "", $post['tSurLastLoanValue']);
        $post['tSurLastLoanCreditValue'] = str_replace(",", "", $post['tSurLastLoanCreditValue']);

        $post['tSurYearRunningTrader'] = str_replace(",", "", $post['tSurYearRunningTrader']);
        $post['tSurNrFulltimeStaffFemale'] = str_replace(",", "", $post['tSurNrFulltimeStaffFemale']);
        $post['tSurNrFulltimeStaffMale'] = str_replace(",", "", $post['tSurNrFulltimeStaffMale']);
        $post['tSurNrParttimeStaffFemale'] = str_replace(",", "", $post['tSurNrParttimeStaffFemale']);
        $post['tSurNrParttimeStaffMale'] = str_replace(",", "", $post['tSurNrParttimeStaffMale']);
        $post['tSurComodityCacaoSalePercentage'] = str_replace(",", "", $post['tSurComodityCacaoSalePercentage']);
        $post['tSurComodityOtherSalePercentage'] = str_replace(",", "", $post['tSurComodityOtherSalePercentage']);

        $post['tSurNrCacaoFrequentBuyer'] = str_replace(",", "", $post['tSurNrCacaoFrequentBuyer']);
        $post['tSurNrCacaoNormalBuyer'] = str_replace(",", "", $post['tSurNrCacaoNormalBuyer']);

        $post['tSurNrTransWetBeansHighHarvest'] = str_replace(",","",$post['tSurNrTransWetBeansHighHarvest']);
        $post['tSurNrVolumeWetBeansHighHarvest'] = str_replace(",","",$post['tSurNrVolumeWetBeansHighHarvest']);
        $post['tSurNrTransWetBeansNormalHarvest'] = str_replace(",","",$post['tSurNrTransWetBeansNormalHarvest']);
        $post['tSurNrVolumeWetBeansNormalHarvest'] = str_replace(",","",$post['tSurNrVolumeWetBeansNormalHarvest']);
        $post['tSurNrTransWetBeansLowHarvest'] = str_replace(",","",$post['tSurNrTransWetBeansLowHarvest']);
        $post['tSurNrVolumeWetBeansLowHarvest'] = str_replace(",","",$post['tSurNrVolumeWetBeansLowHarvest']);
        $post['tSurNrTransFermentBeansHighHarvest'] = str_replace(",","",$post['tSurNrTransFermentBeansHighHarvest']);
        $post['tSurNrVolumeFermentBeansHighHarvest'] = str_replace(",","",$post['tSurNrVolumeFermentBeansHighHarvest']);
        $post['tSurNrTransFermentBeansNormalHarvest'] = str_replace(",","",$post['tSurNrTransFermentBeansNormalHarvest']);
        $post['tSurNrVolumeFermentBeansNormalHarvest'] = str_replace(",","",$post['tSurNrVolumeFermentBeansNormalHarvest']);
        $post['tSurNrTransFermentBeansLowHarvest'] = str_replace(",","",$post['tSurNrTransFermentBeansLowHarvest']);
        $post['tSurNrVolumeFermentBeansLowHarvest'] = str_replace(",","",$post['tSurNrVolumeFermentBeansLowHarvest']);
        $post['tSurNrTransDryBeansHighHarvest'] = str_replace(",","",$post['tSurNrTransDryBeansHighHarvest']);
        $post['tSurNrVolumeDryBeansHighHarvest'] = str_replace(",","",$post['tSurNrVolumeDryBeansHighHarvest']);
        $post['tSurNrTransDryBeansNormalHarvest'] = str_replace(",","",$post['tSurNrTransDryBeansNormalHarvest']);
        $post['tSurNrVolumeDryBeansNormalHarvest'] = str_replace(",","",$post['tSurNrVolumeDryBeansNormalHarvest']);
        $post['tSurNrTransDryBeansLowHarvest'] = str_replace(",","",$post['tSurNrTransDryBeansLowHarvest']);
        $post['tSurNrVolumeDryBeansLowHarvest'] = str_replace(",","",$post['tSurNrVolumeDryBeansLowHarvest']);
        //replace datanya jika ada koma (end)

        if($post['tSurTraderSurID'] == ""){
            $proses = $this->mtrader->insertTraderSurvey($post);
        }else{
            $proses = $this->mtrader->updateTraderSurvey($post);
        }
        $this->response($proses, 200);
    }

    public function traderSurvey_delete(){
        $proses = $this->mtrader->deleteTraderSurvey($this->delete('TraderSurID'));
        $this->response($proses, 200);
    }

}
?>