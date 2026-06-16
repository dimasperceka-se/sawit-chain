<?php
/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Fri Sep 18 2020
 *  File : transman.php
 *******************************************/
//write excel
require_once 'application/third_party/Spout3/Autoloader/autoload.php';
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Common\Entity\Style\Border;
use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Transman extends REST_Controller
{
    public function __construct()
    {
        $this->file = $_FILES;
        parent::__construct();
        $this->load->model('system/mtransman');
    }

    public function main_grid_get() {
        $data = $this->mtransman->GetTransmanMainGrid();
        $this->response($data, 200);
    }

    public function main_grid_translate_get(){        
        $this->load->model('system/mtranslation');

        $key        = $this->get('key');
        $TransManID = $this->get('TransManID');

        $data = $this->mtransman->GetTransmanMainGridTranslate($key,$TransManID, $this->get('start'), $this->get('limit'));
        if (!empty($data['data'])) {
            foreach ($data['data'] as $key_val => $value) {
                $lang = $this->mtranslation->readListLang();
                foreach ($lang as $value_lang) {
                    $data['data'][$key_val][$value_lang['code']] = $this->mtranslation->readTranslationByKey($value['key'], $value_lang['code']);
                }
            }
        }
        $this->response($data, 200);
    }

    public function main_form_post() {
        $return = array();
        $varPost = $this->post();
        $paramPost = array();

        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_System_Transman_MainForm-Form-", '', $key);
            if ($value == "") {
                $value = null;
            }
            $paramPost[$keyNew] = $value;
        }
        //echo '<pre>'; print_r($paramPost); exit;

        if($paramPost['OpsiDisplay'] == 'insert') {
            $proses = $this->mtransman->InsertMainForm($paramPost);
        } else {
            $proses = $this->mtransman->UpdateMainForm($paramPost);
        }

        if($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function main_form_open_get() {
        $TransManID = (int) $this->get('TransManID');
        $data = $this->mtransman->GetMainFormOpen($TransManID);
        $this->response($data, 200);
    }

    public function main_data_delete() {
        $TransManID = (int) $this->delete('TransManID');

        $proses = $this->mtransman->DeleteMainData($TransManID);
        if($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function source_code_files_grid_get() {
        $TransManID = (int) $this->get('TransManID');
        $data = $this->mtransman->GetSourceCodeFilesGrid($TransManID);
        $this->response($data, 200);
    }

    public function source_code_files_post() {
        $proses = array();
        $ParamPost = $this->post();
        
        if($ParamPost['OptionInput'] == 'Insert') {
            $proses = $this->mtransman->InsertSourceCodeFiles($ParamPost);
        } else {
            $proses = $this->mtransman->UpdateSourceCodeFiles($ParamPost);
        }

        if($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function source_code_files_delete() {
        $TransManID = (int) $this->delete('TransManID');
        $FilePath = filter_var($this->delete('FilePath'), FILTER_SANITIZE_STRING);

        $proses = $this->mtransman->DeleteSourceCodeFiles($TransManID,$FilePath);
        if($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function generate_trans_key_post() {
        $TransManID = (int) $this->post('TransManID');

        $proses = $this->mtransman->GenerateTransKey($TransManID);
        if($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function export_translation_key_post() {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        $TransManID = (int) $this->post('TransManID');

        //prep data ======================================== (BEGIN)
        $DataModule = $this->mtransman->GetTransManData($TransManID);
        $DataList = $this->mtransman->GetTransKey($TransManID);
        $DataLang = $this->mtransman->GetRefLanguage();

        if (!empty($DataList)) {
            foreach ($DataList as $key_val => $value) {
                foreach ($DataLang as $value_lang) {
                    $LangDetail = $this->mtransman->GetTranslationByKeyExport($value['TransKey'], $value_lang['code']);

                    //Check LangDetail
                    //if($LangDetail['id'] == "") $LangDetail['id'] = "No Translation Yet";
                    //if($LangDetail['text'] == "") $LangDetail['text'] = "No Translation Yet";

                    $DataList[$key_val][$value_lang['code'].'_id'] = $LangDetail['id'];
                    $DataList[$key_val][$value_lang['code']] = $LangDetail['text'];
                }
            }
        }
        //echo '<pre>'; print_r($DataList); exit;
        //prep data ======================================== (END)

        $DataHeader = array(
            'Translation Key',
            'Indonesia ID',
            'Indonesia Translation',
            'English ID',
            'English Translation',
            'Malay ID',
            'Malay Translation',
        );

        $ModuleName = $DataModule['ModuleName'];
        $ModuleName = str_replace(' ','-',$ModuleName);
        $ModuleName = preg_replace('/[^A-Za-z0-9\-]/', '', $ModuleName);

        $writer = WriterEntityFactory::createXLSXWriter();
        $filename = date('YmdHis').'_'.$ModuleName.'.xlsx';
        $filePath = 'files/tmp/'.$filename.'.xlsx';
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
        $rowHeader = WriterEntityFactory::createRowFromArray($DataHeader, $styleHeader);
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
            ->setFormat('d-mmm-YY')
            ->build();

        //row data
        for ($i=0; $i < count($DataList); $i++) {
            $cells = array();

            $cells[] = WriterEntityFactory::createCell($DataList[$i]['TransKey'], $styleData);
            $cells[] = WriterEntityFactory::createCell($DataList[$i]['indonesia_id'], $styleData);
            $cells[] = WriterEntityFactory::createCell($DataList[$i]['indonesia'], $styleData);
            $cells[] = WriterEntityFactory::createCell($DataList[$i]['english_id'], $styleData);
            $cells[] = WriterEntityFactory::createCell($DataList[$i]['english'], $styleData);
            $cells[] = WriterEntityFactory::createCell($DataList[$i]['malaysia_id'], $styleData);
            $cells[] = WriterEntityFactory::createCell($DataList[$i]['malaysia'], $styleData);

            $rowData = WriterEntityFactory::createRow($cells);
            $writer->addRow($rowData);
        }

        $writer->close();
        $this->response(array('success' => TRUE, 'filenya' => base_url() . $filePath), 200);
    }

    public function export_compare_translation_mobile_post() {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        ini_set('display_errors',true); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        $TransManID = (int) $this->post('TransManID');

        //prep data ======================================== (BEGIN)
        $DataModule = $this->mtransman->GetTransManData($TransManID);
        $DataLang = $this->mtransman->GetRefLanguage();
        $DataList = $this->mtransman->GetTransKeyWithMobile($TransManID, $DataModule['MobileProgramUid']);
        //echo '<pre>'; print_r($DataList); exit;
        //prep data ======================================== (END)

        $DataHeader = array(
            'Translation Key',
            'Web - Source Code File Path',
            'Web English Translation',
            'Mobile - Description',
            'Mobile - Object',
            'Mobile English Translation'
        );

        $ModuleName = $DataModule['ModuleName'];
        $ModuleName = str_replace(' ','-',$ModuleName);
        $ModuleName = preg_replace('/[^A-Za-z0-9\-]/', '', $ModuleName);

        $writer = WriterEntityFactory::createXLSXWriter();
        $filename = date('YmdHis').'_Compare_'.$ModuleName.'.xlsx';
        $filePath = 'files/tmp/'.$filename.'.xlsx';
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
        $rowHeader = WriterEntityFactory::createRowFromArray($DataHeader, $styleHeader);
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
            ->setFormat('d-mmm-YY')
            ->build();

        //row data
        for ($i=0; $i < count($DataList); $i++) {
            $cells = array();
            
            $cells[] = WriterEntityFactory::createCell($DataList[$i]['TransKey'], $styleData);
            $cells[] = WriterEntityFactory::createCell($DataList[$i]['WebFilePath'], $styleData);
            $cells[] = WriterEntityFactory::createCell($DataList[$i]['WebEnglishLabel'], $styleData);
            $cells[] = WriterEntityFactory::createCell($DataList[$i]['MobileDescription'], $styleData);
            $cells[] = WriterEntityFactory::createCell($DataList[$i]['MobileObject'], $styleData);
            $cells[] = WriterEntityFactory::createCell($DataList[$i]['MobileEnglishLabel'], $styleData);

            $rowData = WriterEntityFactory::createRow($cells);
            $writer->addRow($rowData);
        }

        $writer->close();
        $this->response(array('success' => TRUE, 'filenya' => base_url() . $filePath), 200);
    }

}