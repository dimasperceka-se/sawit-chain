<?php

/**
 * @Author: nikolius
 * @Date:   2017-11-08 16:11:57
 * @Last Modified by:   nikolius
 * @Last Modified time: 2017-11-10 10:44:25
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

//write excel
require_once 'application/third_party/Spout3/Autoloader/autoload.php';


use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
//use Box\Spout\Common\Entity\Style\CellAlignment;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Common\Entity\Style\Border;
use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;

class Farmer_group extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('farmer_group/mfarmer_group');
    }

    public function grid_main_get(){
        //set bahasa
        if ($_SESSION['language'] == "Indonesia") {
            $this->load->language('general', 'indonesia');
        } else {
            $this->load->language('general', 'english');
        }

        //sort
        $sorting = json_decode($this->get('sort'));
        $sortingField = $sorting[0]->property;
        $sortingDir = $sorting[0]->direction;

        //get param
        $pSearch = array(
            'ProvinceID' => $this->get('prov'),
            'DistrictID' => $this->get('kab'),
            'ArrFilter' => $this->get('ArrFilter'),
            'CmbFilterProvince' => (int) $this->get('CmbFilterProvince'),
            'CmbFilterDistrict' => (int) $this->get('CmbFilterDistrict'),
            'CmbFilterSubDistrict' => (int) $this->get('CmbFilterSubDistrict'),
            'CmbFilterVillage' => (int) $this->get('CmbFilterVillage'),
            'TextFilterID' => filter_var($this->get('TextFilterID'),FILTER_SANITIZE_STRING),
            'TextFilterName' => filter_var($this->get('TextFilterName'),FILTER_SANITIZE_STRING),
        );

        $data = $this->mfarmer_group->getGridMainFarmerGroup($pSearch, $this->get('start'), $this->get('limit'), $sortingField, $sortingDir);
        $this->response($data, 200);
    }

    public function farmer_group_form_post(){
        if($this->post('Koltiva_view_FarmerGroup_FormMainFarmerGroup-FormBasicData-FarmerGroupID') == ""){
            //insert
            $proses = $this->mfarmer_group->insertFarmerGroup($this->post());
        }else{
            //update
            $proses = $this->mfarmer_group->updateFarmerGroup($this->post());
        }
        $this->response($proses, 200);
    }

    public function farmer_group_basic_data_form_get(){
        $FarmerGroupID = (int) $this->get('FarmerGroupID');
        $data = $this->mfarmer_group->getFarmerGroupBasicDataForm($FarmerGroupID);
        $this->response($data, 200);
    }

    public function farmer_group_form_delete(){
        $FarmerGroupID = (int) $this->delete('FarmerGroupID');
        $result = $this->mfarmer_group->deleteFarmerGroup($FarmerGroupID);
        $this->response($result, 200);
    }

    public function farmer_group_member_panel_grid_get(){
        $FarmerGroupID = (int) $this->get('FarmerGroupID');

        //sort
        $sorting = json_decode($this->get('sort'));
        $sortingField = $sorting[0]->property;
        $sortingDir = $sorting[0]->direction;

        $result = $this->mfarmer_group->getFarmerGroupMemberPanelGrid($FarmerGroupID,$this->get('start'), $this->get('limit'), $sortingField, $sortingDir);
        $this->response($result, 200);
    }

    public function export_farmers_get(){
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);

        $FarmerGroupID = (int) $this->get('FarmerGroupID');

        $dataList        = $this->mfarmer_group->getFarmerGroupMemberExcel($FarmerGroupID);

        $dataListKebun   = $this->mfarmer_group->getFarmerGroupMemberPlotExcel($FarmerGroupID);

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

            //Kolom Header Survey Plot
            $dataHeaderGarden = array('No');
            foreach($dataListKebun[0] as $key2 => $value2){
                array_push($dataHeaderGarden,lang($key2));
            }
            //Kolom Header Survey Plot
            
            //Kolom Body Survey Plot
            $dataListExcelKebun = array();
            $no = 1;
            foreach ($dataListKebun as $key => $value) {
                $data = array();
                array_push($data,$no);
                foreach($value as $keyx => $valuex){
                    array_push($data,$valuex);
                }
                $dataListExcelKebun[$key] = $data;
                $no++;
            }

            $writer = WriterEntityFactory::createXLSXWriter(); // for XLSX files// 
            $namaFile = date('YmdHis') . '_export_excel_farmers.xlsx';
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
            
            $writer->getCurrentSheet()->setName('Farmer Data');
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
                            $dataRow = 25569 + (strtotime($dataRows[$j]) / 86400);
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

            $rowHeaderKebun = WriterEntityFactory::createRowFromArray($dataHeaderGarden, $styleHeader);
            $writer->addNewSheetAndMakeItCurrent()->setName('Farmer Plot');
            $writer->addRow($rowHeaderKebun);

            for ($i=0; $i < count($dataListExcelKebun); $i++) {
                $dataRowsKebun = $dataListExcelKebun[$i];
                $cells2 = array();
    
                for ($j=0; $j < count($dataRowsKebun); $j++) {
                    $styleRow = null;
                    $dataRowKebun = null;
    
                    //cek apakah numeric
                    if(is_numeric($dataRowsKebun[$j])){
                        $styleRow = $styleFormatAngka;
                        $dataRowKebun = (float) $dataRowsKebun[$j];
                    } else {
                        //cek apakah tanggal
                        if($this->validateDate($dataRowsKebun[$j]) == true) {
                            $styleRow = $styleFormatTanggal;
                            $dataRowKebun = 25569 + (strtotime($dataRowsKebun[$j]) / 86400);
                        } else {
                            $styleRow = $styleData;
                            $dataRowKebun = $dataRowsKebun[$j];
                        }
                    }
    
                    $cells2[$j] = WriterEntityFactory::createCell($dataRowKebun, $styleRow);
                }
                /*$cells = [
                    WriterEntityFactory::createCell($dataRows[0], $styleData),
                    WriterEntityFactory::createCell((float) $dataRows[1], $styleFormatAngka),
                    WriterEntityFactory::createCell($dataRows[2], $styleData),
                    WriterEntityFactory::createCell(25569 + (time() / 86400), $styleFormatTanggal),
                    WriterEntityFactory::createCell($dataRows[4], $styleFormatTanggal)
                ];*/
    
                $rowDataKebun = WriterEntityFactory::createRow($cells2);
                $writer->addRow($rowDataKebun);
            }
    
            $writer->close();
    
            $this->response(array('success' => TRUE, 'filenya' => base_url() . $filePath), 200);
            exit;
        }else{
            $this->response(array('success' => FALSE, 'filenya' => ''));
            exit;
        }
    }

    public function farmer_group_member_input_grid_get(){
        $FarmerGroupID = (int) $this->get('FarmerGroupID');
        $textSearch = $this->get('textSearch');
        $ProvinceID = $this->get('ProvinceID');
        $DistrictID = $this->get('DistrictID');
        $SubdistrictID = $this->get('SubdistrictID');
        $VillageID = $this->get('VillageID');
        $Enumerator = $this->get('Enumerator');

        //sort
        $sorting = json_decode($this->get('sort'));
        $sortingField = $sorting[0]->property;
        $sortingDir = $sorting[0]->direction;

        $result = $this->mfarmer_group->getFarmerGroupMemberInputGrid($FarmerGroupID,$textSearch,$ProvinceID,$DistrictID,$SubdistrictID,$VillageID,$this->get('start'), $this->get('limit'), $sortingField, $sortingDir,$Enumerator);
        $this->response($result, 200);
    }

    public function enumerator_input_grid_get(){
        $result = $this->mfarmer_group->getUserList();
        $this->response($result, 200);
    }

    public function farmer_group_member_input_post(){
        $FarmerGroupID = (int) $this->post('FarmerGroupID');
        $arrMemberID = json_decode($this->post('MemberID'));

        $result = $this->mfarmer_group->inputFarmerGroupMember($arrMemberID,$FarmerGroupID);
        $this->response($result, 200);
    }

    public function farmer_group_member_delete(){
        $FarmerGroupID = (int) $this->delete('FarmerGroupID');
        $MemberID = (int) $this->delete('MemberID');

        $result = $this->mfarmer_group->deleteFarmerGroupMember($MemberID,$FarmerGroupID);
        $this->response($result, 200);
    }

    public function export_farmer_group_get() {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        //set bahasa
        if ($_SESSION['language'] == "Indonesia") {
            $this->load->language('general', 'indonesia');
        } else {
            $this->load->language('general', 'english');
        }

        //get param
        $pSearch = array(
            'ProvinceID' => $this->get('prov'),
            'DistrictID' => $this->get('kab'),
        );

        //Get Data Farmer Group
        $dataList       = $this->mfarmer_group->getGridMainFarmerGroupExcel($pSearch);
        // print_r($dataList);
        // die;
        
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
            $namaFile = date('YmdHis') . '_export_excel_farmer_group.xlsx';
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
            exit;
        }
    }

    private function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
}