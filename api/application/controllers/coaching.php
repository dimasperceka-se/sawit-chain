<?php

/* * ****************************************
 *  Author : hasbycs@gmail.com
 *  Created On : 2021-10-06
 *  File : coaching.php
 * ***************************************** */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

//write excel
require_once 'application/third_party/Spout3/Autoloader/autoload.php';


use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
//use Box\Spout\Common\Entity\Style\CellAlignment;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Common\Entity\Style\Border;
use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\ErrorCorrectionLevel;

class Coaching extends REST_Controller {

    public function __construct() {
        $this->file = $_FILES;
        parent::__construct();
        $this->load->model('coaching/mcoaching');
    }

    public function grid_main_get() {
        //sort
        $sorting = json_decode($this->get('sort'));
        if (isset($sorting[0]->property))
            $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        else
            $sortingField = null;
        if (isset($sorting[0]->direction))
            $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';
        else
            $sortingDir = null;
        $start = (int) $this->get('start');
        $limit = (int) $this->get('limit');

        $pSearch = array();
        $pSearch['KeySearch']       = filter_var($this->get('KeySearch'), FILTER_SANITIZE_STRING);
        $pSearch['FarmerGroupID']   = $this->get('FarmerGroupID');
        $pSearch['StartDate']       = $this->get('StartDate');
        $pSearch['EndDate']         = $this->get('EndDate');

        //echo '<pre>'; print_r($pSearch); exit;
        $data = $this->mcoaching->GetGridMain($pSearch, $start, $limit, $sortingField, $sortingDir);
        $this->response($data, 200);
    }

    public function coaching_form_open_get() {
        $CoachingID = (int) $this->get('CoachingID');
        $data = $this->mcoaching->CoachingFormOpen($CoachingID);
        $this->response($data, 200);
    }

    public function coaching_ims_get(){
        $data = $this->mcoaching->CoachingIMSList();
        $this->response($data, 200);
    }

    public function coaching_photo_post() {
        //Cek file images
        $ExtNya = GetFileExt($_FILES['Koltiva_view_Coaching_MainForm-Form-CoachingPhotoInput']['name']);
        if (!in_array($ExtNya, array('png', 'jpg', 'jpeg', 'gif'))) {
            $result['success'] = false;
            $result['message'] = lang('File types not allowed');
            $this->response($result, 400);
        } else {
            if ($this->post('OpsiDisplay') == "insert") {
                if ($this->file['Koltiva_view_Coaching_MainForm-Form-CoachingPhotoInput']['name'] != '') {
                    $gambar = date('Ymdhis') . '_' . $this->file['Koltiva_view_Coaching_MainForm-Form-CoachingPhotoInput']['name'];
                    $fileupload['Koltiva_view_Coaching_MainForm-Form-CoachingPhotoInput'] = $this->file['Koltiva_view_Coaching_MainForm-Form-CoachingPhotoInput'];
                    $upload = move_upload($fileupload, 'files/tmp/' . $gambar);
                    if (isset($upload['upload_data'])) {
                        $result['success'] = true;
                        $result['file'] = $upload['upload_data']['file_name'];
                        $this->response($result, 200);
                    } else {
                        $result['success'] = false;
                        $result['message'] = lang('Photo upload failed');
                        $this->response($result, 400);
                    }
                }
            }

            if ($this->post('OpsiDisplay') == "update") {
                if ($this->file['Koltiva_view_Coaching_MainForm-Form-CoachingPhotoInput']['name'] != '') {
                    //Untuk AWS S3, wajib ada
                    $this->load->library('awsfileupload');
                    $upload = $this->awsfileupload->upload($_FILES['Koltiva_view_Coaching_MainForm-Form-CoachingPhotoInput']['tmp_name'], $_FILES['Koltiva_view_Coaching_MainForm-Form-CoachingPhotoInput']['name'], AWSS3_COACHING_PHOTO_PATH, 'images');
                    if ($upload['success'] == true) {
                        $prosesUpdate = $this->mcoaching->UpdateCoachingPhoto($this->post('CoachingID'), $upload['filenamepath']);
                        $result['success'] = true;
                        $result['message'] = lang('File uploaded');
                        $result['file'] = $upload['fileurl'];
                        $this->response($result, 200);
                    } else {
                        $result['success'] = false;
                        $result['message'] = lang('Upload to aws failed');
                        $this->response($result, 400);
                    }
                }
            }

            $result['success'] = false;
            $result['message'] = lang('Photo upload not allowed');
            $this->response($result, 400);
        }
    }

    public function coaching_signature_post() {
        //Cek file images
        $ExtNya = GetFileExt($_FILES['Koltiva_view_Coaching_MainForm-Form-CoachingRecipientSignatureInput']['name']);
        if (!in_array($ExtNya, array('png', 'jpg', 'jpeg', 'gif'))) {
            $result['success'] = false;
            $result['message'] = lang('File types not allowed');
            $this->response($result, 400);
        } else {
            if ($this->post('OpsiDisplay') == "insert") {
                if ($this->file['Koltiva_view_Coaching_MainForm-Form-CoachingRecipientSignatureInput']['name'] != '') {
                    $gambar = date('Ymdhis') . '_' . $this->file['Koltiva_view_Coaching_MainForm-Form-CoachingRecipientSignatureInput']['name'];
                    $fileupload['Koltiva_view_Coaching_MainForm-Form-CoachingRecipientSignatureInput'] = $this->file['Koltiva_view_Coaching_MainForm-Form-CoachingRecipientSignatureInput'];
                    $upload = move_upload($fileupload, 'files/tmp/' . $gambar);
                    if (isset($upload['upload_data'])) {
                        $result['success'] = true;
                        $result['file'] = $upload['upload_data']['file_name'];
                        $this->response($result, 200);
                    } else {
                        $result['success'] = false;
                        $result['message'] = lang('Photo upload failed');
                        $this->response($result, 400);
                    }
                }
            }

            if ($this->post('OpsiDisplay') == "update") {
                if ($this->file['Koltiva_view_Coaching_MainForm-Form-CoachingRecipientSignatureInput']['name'] != '') {
                    //Untuk AWS S3, wajib ada
                    $this->load->library('awsfileupload');
                    $upload = $this->awsfileupload->upload($_FILES['Koltiva_view_Coaching_MainForm-Form-CoachingRecipientSignatureInput']['tmp_name'], $_FILES['Koltiva_view_Coaching_MainForm-Form-CoachingRecipientSignatureInput']['name'], AWSS3_COACHING_SIGNATURE_PATH, 'images');
                    if ($upload['success'] == true) {
                        $prosesUpdate = $this->mcoaching->UpdateCoachingRecipientSignature($this->post('CoachingID'), $upload['filenamepath']);
                        $result['success'] = true;
                        $result['message'] = lang('File uploaded');
                        $result['file'] = $upload['fileurl'];
                        $this->response($result, 200);
                    } else {
                        $result['success'] = false;
                        $result['message'] = lang('Upload to aws failed');
                        $this->response($result, 400);
                    }
                }
            }

            $result['success'] = false;
            $result['message'] = lang('Photo upload not allowed');
            $this->response($result, 400);
        }
    }

    public function coaching_data_post() {
        $varPost = $this->post();
        $ParamPost = array();

        //prep variabel (begin)
        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Coaching_MainForm-Form-", '', $key);
            if ($value == "") {
                $value = null;
            }
            $ParamPost[$keyNew] = $value;
        }
        //prep variabel (end)
        //echo '<pre>'; print_r($ParamPost); exit;
        if ($ParamPost['Latitude'] != '' && $ParamPost['Longitude'] != ''){
            $this->load->model('mcommon');
            $CekGPS = $this->mcommon->CekKoordinat($ParamPost['Latitude'], $ParamPost['Longitude']);
            if ($CekGPS == false) {
                $return['success'] = false;
                $return['message'] = lang('Invalid GPS coordinates!');
                $this->response($return, 200);
            }
        }

        if ($ParamPost['OpsiDisplay'] == 'insert') {
            $proses = $this->mcoaching->InsertCoaching($ParamPost);
        } elseif ($ParamPost['OpsiDisplay'] == 'update') {
            $proses = $this->mcoaching->UpdateCoaching($ParamPost);
        }

        $this->response($proses, 200);
    }
    
    public function coaching_data_delete() {
        $CoachingID = (int) $this->delete('CoachingID');
        $proses = $this->mcoaching->DeleteCoaching($CoachingID);
        $this->response($proses, 200);
    }

    public function cmb_coaching_topic_get() {
        $data = $this->mcoaching->GetComboCoachingTopic($this->get("CategoryID"));
        $this->response($data, 200);
    }

    public function cmb_coaching_subtopic_get() {
        $data = $this->mcoaching->GetComboCoachingSubTopic($this->get("TopicID"));
        $this->response($data, 200);
    }

    public function cmb_coaching_finding_get() {
        $data = $this->mcoaching->GetComboCoachingFinding($this->get("SubtopicID"), $this->get("UrgentlyStatus"));
        $this->response($data, 200);
    }

    public function cmb_coaching_recomm_get() {
        $data = $this->mcoaching->GetComboCoachingRecomm($this->get("SubtopicID"));
        $this->response($data, 200);
    }

    public function coaching_task_grid_get() {
        //sort
        $sorting = json_decode($this->get('sort'));
        if (isset($sorting[0]->property))
            $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        else
            $sortingField = null;
        if (isset($sorting[0]->direction))
            $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';
        else
            $sortingDir = null;
        $start = (int) $this->get('start');
        $limit = (int) $this->get('limit');

        $pSearch = array(
            'CoachingID' => (int) $this->get('CoachingID')
        );

        $data = $this->mcoaching->GetCoachingTaskGrid($pSearch, $start, $limit, $sortingField, $sortingDir);
        $this->response($data, 200);
    }

    public function coaching_task_data_form_get() {
        $ActivityNCID = (int) $this->get('ActivityNCID');
        $data = $this->mcoaching->CoachingTaskFormOpen($ActivityNCID);
        $this->response($data, 200);
    }

    public function coaching_task_data_post() {
        $return = array();
        $varPost = $this->post();
        $paramPost = array();

        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Coaching_WinFormCoachingTask-Form-", '', $key);
            if ($value == "") {
                $value = null;
            }
            $paramPost[$keyNew] = $value;
        }

        // echo '<pre>'; print_r($paramPost) ; echo "</pre>"; exit;

        if ($paramPost['OpsiDisplay'] == 'insert') {
            //Cek validasi task
            $CekTask = $this->mcoaching->CekDuplikatTask($paramPost['ActivityID'], $paramPost['Topic']);
            if ($CekTask == true) {
                $return['success'] = false;
                $return['message'] = lang('Task already registered');
                $this->response($return, 400);
            }
            $proses = $this->mcoaching->InsertTask($paramPost);
        } else {
            $proses = $this->mcoaching->UpdateTask($paramPost);
        }

        if ($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }
    
    public function coaching_task_delete() {
        $ActivityNCID = (int) $this->delete('ActivityNCID');
        $proses = $this->mcoaching->DeleteTask($ActivityNCID);

        if ($proses['success'] == true)
            $this->response($proses, 200);
        else
            $this->response($proses, 400);
    }

    public function cmb_category_get() {
        $data = $this->mcoaching->GetComboCategory();
        $this->response($data, 200);
    }

    public function cmb_coaching_sub_topic_get() {
        $data = $this->mcoaching->GetComboCoachingSubTopic($this->get('CoachingTopicID'));
        $this->response($data, 200);
    }

    public function export_coaching_get(){

        $pSearch = array();
        $pSearch['KeySearch']       = filter_var($this->get('KeySearch'), FILTER_SANITIZE_STRING);
        $pSearch['FarmerGroupID']   = $this->get('FarmerGroupID');
        $pSearch['StartDate']       = $this->get('StartDate');
        $pSearch['EndDate']         = $this->get('EndDate');

        //echo '<pre>'; print_r($pSearch); exit;
        $dataList = $this->mcoaching->GetGridMainExport($pSearch, $start, $limit, $sortingField, $sortingDir);

        if(count($dataList)){

            //Kolom Header Farmer
            $dataHeader = array('No');
            foreach($dataList[0] as $key => $value){
                array_push($dataHeader,lang($key));
            }
            //Kolom Header Farmer

            //Kolom Body Farmer
            $dataListExcel = array();
            $no = 1;
            foreach ($dataList as $key => $value) {
                $data = array();
                array_push($data,$no);
                foreach($value as $keyx => $valuex){
                    array_push($data,$valuex);
                }
                $dataListExcel[$key] = $data;
                $no++;
            }
            //Kolom Body Farmer

            $writer = WriterEntityFactory::createXLSXWriter(); // for XLSX files// 
            $namaFile = date('YmdHis') . '_export_excel_coaching.xlsx';
            $filePath = 'files/tmp/' . $namaFile;
            $writer->openToFile($filePath);

            $defaultStyle = (new StyleBuilder())
                ->setFontName('Arial')
                ->setFontSize(11)
                ->setShouldWrapText(false)
                ->build();
            $writer->setDefaultRowStyle($defaultStyle)
                ->openToFile($filePath);

            $borderDefa = (new BorderBuilder())
                ->setBorderBottom(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
                ->setBorderTop(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
                ->setBorderRight(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
                ->setBorderLeft(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
                ->build();

            //style
            $styleHeader = (new StyleBuilder())
                ->setFontColor(Color::WHITE)
                ->setBorder($borderDefa)
                ->setBackgroundColor(Color::GREEN)
                ->build();

            //row header
            $rowHeader = WriterEntityFactory::createRowFromArray($dataHeader, $styleHeader);
            $writer->addRow($rowHeader);

            $styleData = (new StyleBuilder())
                ->setBorder($borderDefa)
                ->build();

            $styleFormatAngka = (new StyleBuilder())
                ->setBorder($borderDefa)
                ->setFormat('0')
                ->build();

            $styleFormatTanggal = (new StyleBuilder())
                ->setBorder($borderDefa)
                ->setFormat('YYYY-mm-dd')
                ->build();

            for ($i=0; $i < count($dataListExcel); $i++) {
                $dataRows = $dataListExcel[$i];
                $cells = array();
    
                for ($j=0; $j < count($dataRows); $j++) {
                    $styleRow = null;
                    $dataRow = null;
    
                    //cek apakah numeric
                    if(is_numeric($dataRows[$j])){
                        $styleRow = $styleFormatAngka;
                        $dataRow = $dataRows[$j];
                    } else {
                        //cek apakah tanggal
                        if($this->validateDate($dataRows[$j]) == true) {
                            $styleRow = $styleFormatTanggal;
                            $dataRow = $dataRows[$j];
                        } else {
                            $styleRow = $styleData;
                            $dataRow = $dataRows[$j];
                        }
                    }
    
                    $cells[$j] = WriterEntityFactory::createCell($dataRow, $styleRow);
                }
                /*$cells = [
                    WriterEntityFactory::createCell($dataRows[0], $styleData),
                    WriterEntityFactory::createCell((float) $dataRows[1], $styleFormatAngka),
                    WriterEntityFactory::createCell($dataRows[2], $styleData),
                    WriterEntityFactory::createCell(25569 + (time() / 86400), $styleFormatTanggal),
                    WriterEntityFactory::createCell($dataRows[4], $styleFormatTanggal)
                ];*/
    
                $rowData = WriterEntityFactory::createRow($cells);
                $writer->addRow($rowData);
            }

            $writer->close();
    
            $this->response(array('success' => TRUE, 'filenya' => base_url() . $filePath), 200);
            exit;
        }else{
            $this->response(array('success' => FALSE, 'filenya' => ''));
        }
    }

    private function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
}
