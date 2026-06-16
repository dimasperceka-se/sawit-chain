<?php
/**
 * @Author: nikolius
 * @Date:   2017-06-01 16:47:31
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Household_survey extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->file = $_FILES;
        $this->load->model('household_survey/mhousehold_survey');
    }

    public function grid_household_survey_summary_get(){
        $data = $this->mhousehold_survey->getGridHouseholdSurveySummary($this->get('MemberID'));
        $this->response($data, 200);
    }

    public function household_survey_form_data_get(){
        $data = $this->mhousehold_survey->getHouseholdSurveyFormData($this->get('MemberID'),$this->get('SurveyNr'),$this->get('DateCollection'));
        $this->response($data, 200);
    }

    public function survey_post(){
        $varPost = $this->post();

        //prep variabel (begin)
        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_HouseholdSurvey_WinFormHouseholdSurvey-Form-", '', $key);
            if($value == "") $value = null;

            switch ($keyNew) {
                case 'AvgDaysConsumeBeef':
                    $value = str_replace(",","",$value);
                break;
            }

            $paramPost[$keyNew] = $value;
        }
        //prep variabel (end)

        if($paramPost['opsiDisplay'] == 'insert'){
            //cek apakah data sudah ada
            $isExist = $this->mhousehold_survey->checkIfSurveyExist($paramPost);
            if($isExist == true){
                $proses['success'] = false;
                $proses['message'] = lang('Survey already exist');
                $this->response($proses, 200);
            }

            $proses = $this->mhousehold_survey->insertHouseholdSurvey($paramPost);
        }elseif($paramPost['opsiDisplay'] == 'update'){
            $proses = $this->mhousehold_survey->updateHouseholdSurvey($paramPost);
        }
        $this->response($proses, 200);

    }

    public function survey_delete(){
        $MemberID = (int) $this->delete('MemberID');
        $SurveyNr = $this->delete('SurveyNr');
        $DateCollection = $this->delete('DateCollection');
        $proses = $this->mhousehold_survey->deleteHouseholdSurvey($MemberID,$SurveyNr,$DateCollection);
        $this->response($proses, 200);
    }

}
?>