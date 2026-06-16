<?php
/**
 * @Author: nikolius
 * @Date:   2017-07-24 10:28:06
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Trader_survey extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->file = $_FILES;
        $this->load->model('trader_survey/mtrader_survey');
    }

    public function grid_trader_survey_summary_get(){
        $data = $this->mtrader_survey->getGridTraderSurveySummary($this->get('MemberID'));
        $this->response($data, 200);
    }

    public function trader_survey_form_data_get(){
        $data = $this->mtrader_survey->getTraderSurveyFormData($this->get('MemberID'),$this->get('BusinessNr'),$this->get('SurveyNr'),$this->get('DateCollection'));
        $this->response($data, 200);
    }

    public function survey_post(){
        $varPost = $this->post();

        //prep variabel (begin)
        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_TraderSurvey_WinFormTraderSurvey-Form-", '', $key);
            if($value == "") $value = null;

            switch ($keyNew) {
                case 'YearRunning':
                    $value = str_replace(",","",$value);
                break;
            }

            $paramPost[$keyNew] = $value;
        }
        //prep variabel (end)

        if($paramPost['opsiDisplay'] == 'insert'){

            //cek apakah data sudah ada
            $isExist = $this->mtrader_survey->checkIfSurveyExist($paramPost);
            if($isExist == true){
                $proses['success'] = false;
                $proses['message'] = lang('Survey already exist');
                $this->response($proses, 200);
            }

            $proses = $this->mtrader_survey->insertTraderSurvey($paramPost);
        }elseif($paramPost['opsiDisplay'] == 'update'){
            $proses = $this->mtrader_survey->updateTraderSurvey($paramPost);
        }
        $this->response($proses, 200);
    }

    public function survey_delete(){
        $MemberID = (int) $this->delete('MemberID');
        $BusinessNr = (int) $this->delete('BusinessNr');
        $SurveyNr = $this->delete('SurveyNr');
        $DateCollection = $this->delete('DateCollection');
        $proses = $this->mtrader_survey->deleteTraderSurvey($MemberID,$BusinessNr,$SurveyNr,$DateCollection);
        $this->response($proses, 200);
    }

}
?>