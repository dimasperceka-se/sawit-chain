<?php

/**
 * @Author: nikolius
 * @Date:   2017-11-02 16:18:59
 * @Last Modified by:   nikolius
 * @Last Modified time: 2017-11-03 11:15:59
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Finance_survey extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('finance_survey/mfinance_survey');
    }

    public function grid_finance_survey_summary_get(){
        $data = $this->mfinance_survey->getGridFinanceSurveySummary($this->get('MemberID'));
        $this->response($data, 200);
    }

    public function finance_survey_form_data_get(){
        $data = $this->mfinance_survey->getFinanceSurveyFormData($this->get('MemberID'),$this->get('SurveyNr'));
        $this->response($data, 200);
    }

    public function survey_post(){
        //set bahasa
        if ($_SESSION['language'] == "Indonesia") {
            $this->load->language('general', 'indonesia');
        } else {
            $this->load->language('general', 'english');
        }

        $varPost = $this->post();

        //prep variabel (begin)
        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_FinanceSurvey_WinFormFinanceSurvey-Form-", '', $key);
            if($value == "") $value = null;

            switch ($keyNew) {
                case 'bNameOfBPR':
                    if($value == lang('Name of BPR')){
                        $value = null;
                    }
                break;
                case 'bNameOfCoop':
                    if($value == lang('Name of Cooperative')){
                        $value = null;
                    }
                break;
                case 'bNameOfBank':
                    if($value == lang('Name of Bank')){
                        $value = null;
                    }
                break;
                case 'aValueOfLivestock':
                case 'aMonthlyIncomeOtherCrop':
                case 'aValueRemitPerYear':
                case 'aRevenueToHousehold':
                case 'aIncomeOtherPlot':
                case 'aTransportCost':
                case 'bValueOfDebt':
                case 'bTenorYear':
                case 'bTimeToMature':
                case 'bHowMuchInterestRate':
                case 'bLevelCurrentSavings':
                    $value = str_replace(",","",$value);
                break;
            }

            $paramPost[$keyNew] = $value;
        }
        //prep variabel (end)
        //echo '<pre>'; print_r($paramPost); exit;

        if($paramPost['opsiDisplay'] == 'insert'){
            //cek apakah data sudah ada
            $isExist = $this->mfinance_survey->checkIfSurveyExist($paramPost);
            if($isExist == true){
                $proses['success'] = false;
                $proses['message'] = lang('Survey already exist');
                $this->response($proses, 200);
            }

            $proses = $this->mfinance_survey->insertFinanceSurvey($paramPost);
        }elseif($paramPost['opsiDisplay'] == 'update'){
            $proses = $this->mfinance_survey->updateFinanceSurvey($paramPost);
        }
        $this->response($proses, 200);
    }

    public function survey_delete(){
        $MemberID = (int) $this->delete('MemberID');
        $SurveyNr = (int) $this->delete('SurveyNr');
        $proses = $this->mfinance_survey->deleteFinanceSurvey($MemberID,$SurveyNr);
        $this->response($proses, 200);
    }

}
?>