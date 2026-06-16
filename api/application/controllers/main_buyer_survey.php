<?php
/**
 * @Author: nikolius
 * @Date:   2017-06-01 13:20:48
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main_buyer_survey extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->file = $_FILES;
        $this->load->model('main_buyer_survey/mmain_buyer_survey');
    }

    public function grid_main_buyer_survey_summary_get(){
        $data = $this->mmain_buyer_survey->getGridMainBuyerSurveySummary($this->get('MemberID'));
        $this->response($data, 200);
    }

    public function last_receipt_post(){
        if($this->post('opsiDisplay') == "insert"){
            if ($this->file['Koltiva_view_MainBuyerSurvey_WinFormMainBuyerSurvey-Form-ReceiptPhotoLastSoldFFBInput']['name'] != '') {
                $gambar = 'temp/'.$this->file['Koltiva_view_MainBuyerSurvey_WinFormMainBuyerSurvey-Form-ReceiptPhotoLastSoldFFBInput']['name'];
                $upload = move_upload($this->file, 'images/main_buyer_last_receipt/' . $gambar);
                if (isset($upload['upload_data'])) {
                    $result['success'] = true;
                    $result['file']    = $gambar;
                    $this->response($result, 200);
                } else {
                    echo  'false'; exit;
                }
            }
        }

        if($this->post('opsiDisplay') == "update"){
            $varPost = $this->post();

            //get member data
            $this->load->model('grower/mgrower');
            $getData = $this->mgrower->getMemberDataDetail($varPost['Koltiva_view_MainBuyerSurvey_WinFormMainBuyerSurvey-Form-MemberID']);
            $MemberData = $getData['data'];

            if ($this->file['Koltiva_view_MainBuyerSurvey_WinFormMainBuyerSurvey-Form-ReceiptPhotoLastSoldFFBInput']['name'] != '') {
                $ProvinceID = $MemberData['ProvinceID'];
                $MemberDisplayID = $varPost['Koltiva_view_MainBuyerSurvey_WinFormMainBuyerSurvey-Form-MemberDisplayID'];

                //get ext nya..
                $arrTemp = explode(".", $this->file['Koltiva_view_MainBuyerSurvey_WinFormMainBuyerSurvey-Form-ReceiptPhotoLastSoldFFBInput']['name']);
                $extNya = array_values(array_slice($arrTemp, -1))[0];
                $namaFileGambar = date('YmdHis').".".$extNya;

                //foto dipisah perdirectory ProvinceID, per MemberDisplayID, cek apakah folder tempat nyimpan foto sudah ada
                if(!file_exists('images/main_buyer_last_receipt/'.$ProvinceID)){
                    mkdir('images/main_buyer_last_receipt/'.$ProvinceID, 0777, true);
                }
                if(!file_exists('images/main_buyer_last_receipt/'.$ProvinceID.'/'.$MemberData['MemberUID'])){
                    mkdir('images/main_buyer_last_receipt/'.$ProvinceID.'/'.$MemberData['MemberUID'], 0777, true);
                }

                $pathGambarTujuan = $ProvinceID.'/'.$MemberData['MemberUID'].'/'.$namaFileGambar;

                $upload = move_upload($this->file, 'images/main_buyer_last_receipt/'. $pathGambarTujuan);
                if (isset($upload['upload_data'])) {
                    $result['success'] = true;
                    $result['file']    = $pathGambarTujuan;
                    $this->response($result, 200);
                } else {
                    echo  'false'; exit;
                }
            }
        }

    }

    public function main_buyer_survey_form_data_get(){
        $data = $this->mmain_buyer_survey->getMainBuyerSurveyFormData($this->get('MemberID'),$this->get('SurveyNr'),$this->get('DateCollection'),$this->get('PlotNr'));
        $this->response($data, 200);
    }

    public function survey_post(){
        $varPost = $this->post();

        //prep variabel (begin)
        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_MainBuyerSurvey_WinFormMainBuyerSurvey-Form-", '', $key);
            if($value == "") $value = null;

            switch ($keyNew) {
                case 'DistanceToBuyer':
                case 'FFBPriceLastSold':
                case 'TransportationCost':
                case 'OtherRelatedCost':
                case 'PenaltyDeduction':
                case 'HarvestingCost':
                    $value = str_replace(",","",$value);
                break;
            }

            $paramPost[$keyNew] = $value;
        }
        //prep variabel (end)

        //get member data
        $this->load->model('grower/mgrower');
        $getData = $this->mgrower->getMemberDataDetail($paramPost['MemberID']);
        $MemberData = $getData['data'];

        if($paramPost['opsiDisplay'] == 'insert'){

            //cek apakah data sudah ada
            $isExist = $this->mmain_buyer_survey->checkIfSurveyExist($paramPost);
            if($isExist == true){
                $proses['success'] = false;
                $proses['message'] = lang('Survey already exist');
                $this->response($proses, 200);
            }

            $proses = $this->mmain_buyer_survey->insertMainBuyerSurvey($paramPost,$MemberData);
        }elseif($paramPost['opsiDisplay'] == 'update'){
            $proses = $this->mmain_buyer_survey->updateMainBuyerSurvey($paramPost,$MemberData);
        }
        $this->response($proses, 200);
    }

    public function survey_delete(){
        $MemberID = (int) $this->delete('MemberID');
        $SurveyNr = $this->delete('SurveyNr');
        $PlotNr = $this->delete('PlotNr');
        $DateCollection = $this->delete('DateCollection');
        $proses = $this->mmain_buyer_survey->deleteMainBuyerSurvey($MemberID,$SurveyNr,$PlotNr,$DateCollection);
        $this->response($proses, 200);
    }

}
?>