<?php
/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Thu Feb 21 2019
 *  File : dashboard_export_detail_jbcocoa.php
 *******************************************/
defined('BASEPATH') OR exit('No direct script access allowed');


//write excel
require_once 'application/third_party/Spout3/Autoloader/autoload.php';


use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
//use Box\Spout\Common\Entity\Style\CellAlignment;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Common\Entity\Style\Border;
use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;

class Dashboard_export_detail_sawit extends REST_Controller {

    public function __construct() {
        parent::__construct();
        ini_set('memory_limit', '-1');
        $this->load->model('dboard/mpro_kpi');
    }
    
    public function palm_oil_mill_get($ProgID,$ClusterID,$Lockdate) {
        $ProgID = (int) $ProgID;
        $ClusterID = (int) $ClusterID;
        $UrlFilenya = null;

        //Ambil data query nya =============== (Begin)
        //        $ImsIDImp = $this->mjbcocoakpi->GetImsIDList($ProgID);
        //$DateGenerated = $this->mjbcocoakpi->GetCurrentDateGenerated($ProgID);
        $DateGenerated = $Lockdate;
        $DataList = $this->mpro_kpi->palm_oil_mill_export($DateGenerated,$ProgID,$ClusterID);
        if($DataList['count_data'] == 0){
            $DataReturn['success'] = true;
            $DataReturn['count_data'] = 0;
            $this->response($DataReturn, 200);
        }
        //Ambil data query nya =============== (End)

        //Write Excelnya
        $name = 'Palm Oil Mill';
        if ($ProgID == 14){
            $name = 'Palm Oil Mill';
        }
        $UrlFilenya = $this->WriteExcelForExport($name,$DataList['data']);

        $DataReturn['success'] = true;
        $DataReturn['UrlFilenya'] = $UrlFilenya;
        $DataReturn['count_data'] = $DataList['count_data'];
        $this->response($DataReturn, 200);        
    }
    
    public function farmers_registered_get($ProgID,$ClusterID,$Lockdate) {
        $ProgID = (int) $ProgID;
        $ClusterID = (int) $ClusterID;
        $UrlFilenya = null;

        //Ambil data query nya =============== (Begin)
        //        $ImsIDImp = $this->mjbcocoakpi->GetImsIDList($ProgID);
        //$DateGenerated = $this->mjbcocoakpi->GetCurrentDateGenerated($ProgID);
        $DateGenerated = $Lockdate;
        $DataList = $this->mpro_kpi->farmers_registered_export($DateGenerated,$ProgID,$ClusterID);
        if($DataList['count_data'] == 0){
            $DataReturn['success'] = true;
            $DataReturn['count_data'] = 0;
            $this->response($DataReturn, 200);
        }
        //Ambil data query nya =============== (End)

        //Write Excelnya
        $name = 'Farmer Registered';
        if ($ProgID == 14){
            $name = 'Farmer Registered';
        }
        $UrlFilenya = $this->WriteExcelForExport($name,$DataList['data']);

        $DataReturn['success'] = true;
        $DataReturn['UrlFilenya'] = $UrlFilenya;
        $DataReturn['count_data'] = $DataList['count_data'];
        $this->response($DataReturn, 200);        
    }
    
    public function farm_registered_get($ProgID,$ClusterID,$Lockdate) {
        $ProgID = (int) $ProgID;
        $ClusterID = (int) $ClusterID;
        $UrlFilenya = null;

        //Ambil data query nya =============== (Begin)
        //        $ImsIDImp = $this->mjbcocoakpi->GetImsIDList($ProgID);
        //$DateGenerated = $this->mjbcocoakpi->GetCurrentDateGenerated($ProgID);
        $DateGenerated = $Lockdate;
        $DataList = $this->mpro_kpi->farm_registered_export($DateGenerated,$ProgID,$ClusterID);
        if($DataList['count_data'] == 0){
            $DataReturn['success'] = true;
            $DataReturn['count_data'] = 0;
            $this->response($DataReturn, 200);
        }
        //Ambil data query nya =============== (End)

        //Write Excelnya
        $name = 'Farm Registered';
        if ($ProgID == 14){
            $name = 'Farm Registered';
        }
        $UrlFilenya = $this->WriteExcelForExport($name,$DataList['data']);

        $DataReturn['success'] = true;
        $DataReturn['UrlFilenya'] = $UrlFilenya;
        $DataReturn['count_data'] = $DataList['count_data'];
        $this->response($DataReturn, 200);        
    }
    
    public function farm_ha_get($ProgID,$ClusterID,$Lockdate) {
        $ProgID = (int) $ProgID;
        $ClusterID = (int) $ClusterID;
        $UrlFilenya = null;

        //Ambil data query nya =============== (Begin)
        //        $ImsIDImp = $this->mjbcocoakpi->GetImsIDList($ProgID);
        //$DateGenerated = $this->mjbcocoakpi->GetCurrentDateGenerated($ProgID);
        $DateGenerated = $Lockdate;
        $DataList = $this->mpro_kpi->farm_ha_export($DateGenerated,$ProgID,$ClusterID);
        if($DataList['count_data'] == 0){
            $DataReturn['success'] = true;
            $DataReturn['count_data'] = 0;
            $this->response($DataReturn, 200);
        }
        //Ambil data query nya =============== (End)

        //Write Excelnya
        $name = 'Farm Ha';
        if ($ProgID == 14){
            $name = 'Farm Ha';
        }
        $UrlFilenya = $this->WriteExcelForExport($name,$DataList['data']);

        $DataReturn['success'] = true;
        $DataReturn['UrlFilenya'] = $UrlFilenya;
        $DataReturn['count_data'] = $DataList['count_data'];
        $this->response($DataReturn, 200);        
    }
    
    public function soc_sel_get($ProgID,$ClusterID,$Lockdate) {
        $ProgID = (int) $ProgID;
        $ClusterID = (int) $ClusterID;
        $UrlFilenya = null;

        //Ambil data query nya =============== (Begin)
        //        $ImsIDImp = $this->mjbcocoakpi->GetImsIDList($ProgID);
        //$DateGenerated = $this->mjbcocoakpi->GetCurrentDateGenerated($ProgID);
        $DateGenerated = $Lockdate;
        $DataList = $this->mpro_kpi->soc_sel_export($DateGenerated,$ProgID,$ClusterID);
        if($DataList['count_data'] == 0){
            $DataReturn['success'] = true;
            $DataReturn['count_data'] = 0;
            $this->response($DataReturn, 200);
        }
        //Ambil data query nya =============== (End)

        //Write Excelnya
        $name = 'Social and Selection';
        if ($ProgID == 14){
            $name = 'Social and Selection';
        }
        $UrlFilenya = $this->WriteExcelForExport($name,$DataList['data']);

        $DataReturn['success'] = true;
        $DataReturn['UrlFilenya'] = $UrlFilenya;
        $DataReturn['count_data'] = $DataList['count_data'];
        $this->response($DataReturn, 200);        
    }
    
    public function farmer_survey_get($ProgID,$ClusterID,$Lockdate) {
        $ProgID = (int) $ProgID;
        $ClusterID = (int) $ClusterID;
        $UrlFilenya = null;

        //Ambil data query nya =============== (Begin)
        //        $ImsIDImp = $this->mjbcocoakpi->GetImsIDList($ProgID);
        //$DateGenerated = $this->mjbcocoakpi->GetCurrentDateGenerated($ProgID);
        $DateGenerated = $Lockdate;
        $DataList = $this->mpro_kpi->farmer_survey_export($DateGenerated,$ProgID,$ClusterID);
        if($DataList['count_data'] == 0){
            $DataReturn['success'] = true;
            $DataReturn['count_data'] = 0;
            $this->response($DataReturn, 200);
        }
        //Ambil data query nya =============== (End)

        //Write Excelnya
        $name = 'Farmer Survey';
        
        $UrlFilenya = $this->WriteExcelForExport($name,$DataList['data']);

        $DataReturn['success'] = true;
        $DataReturn['UrlFilenya'] = $UrlFilenya;
        $DataReturn['count_data'] = $DataList['count_data'];
        $this->response($DataReturn, 200);        
    }
    
    public function farm_survey_get($ProgID,$ClusterID,$Lockdate) {
        $ProgID = (int) $ProgID;
        $ClusterID = (int) $ClusterID;
        $UrlFilenya = null;

        //Ambil data query nya =============== (Begin)
        //        $ImsIDImp = $this->mjbcocoakpi->GetImsIDList($ProgID);
        //$DateGenerated = $this->mjbcocoakpi->GetCurrentDateGenerated($ProgID);
        $DateGenerated = $Lockdate;
        $DataList = $this->mpro_kpi->farm_survey_export($DateGenerated,$ProgID,$ClusterID);
        if($DataList['count_data'] == 0){
            $DataReturn['success'] = true;
            $DataReturn['count_data'] = 0;
            $this->response($DataReturn, 200);
        }
        //Ambil data query nya =============== (End)

        //Write Excelnya
        $name = 'Farm Survey';
        if ($ProgID == 14){
            $name = 'Farm Survey';
        }
        $UrlFilenya = $this->WriteExcelForExport($name,$DataList['data']);

        $DataReturn['success'] = true;
        $DataReturn['UrlFilenya'] = $UrlFilenya;
        $DataReturn['count_data'] = $DataList['count_data'];
        $this->response($DataReturn, 200);        
    }
    
    public function polygon_mapping_get($ProgID,$ClusterID,$Lockdate) {
        $ProgID = (int) $ProgID;
        $ClusterID = (int) $ClusterID;
        $UrlFilenya = null;

        //Ambil data query nya =============== (Begin)
        //        $ImsIDImp = $this->mjbcocoakpi->GetImsIDList($ProgID);
        //$DateGenerated = $this->mjbcocoakpi->GetCurrentDateGenerated($ProgID);
        $DateGenerated = $Lockdate;
        $DataList = $this->mpro_kpi->polygon_mapping_export($DateGenerated,$ProgID,$ClusterID);
        if($DataList['count_data'] == 0){
            $DataReturn['success'] = true;
            $DataReturn['count_data'] = 0;
            $this->response($DataReturn, 200);
        }
        //Ambil data query nya =============== (End)

        //Write Excelnya
        $name = 'Polygon Mapping';
        if ($ProgID == 14){
            $name = 'Polygon Mapping';
        }
        $UrlFilenya = $this->WriteExcelForExport($name,$DataList['data']);

        $DataReturn['success'] = true;
        $DataReturn['UrlFilenya'] = $UrlFilenya;
        $DataReturn['count_data'] = $DataList['count_data'];
        $this->response($DataReturn, 200);        
    }
    
    public function individual_farmer_coaching_get($ProgID,$ClusterID,$Lockdate) {
        $ProgID = (int) $ProgID;
        $ClusterID = (int) $ClusterID;
        $UrlFilenya = null;

        //Ambil data query nya =============== (Begin)
        //        $ImsIDImp = $this->mjbcocoakpi->GetImsIDList($ProgID);
        //$DateGenerated = $this->mjbcocoakpi->GetCurrentDateGenerated($ProgID);
        $DateGenerated = $Lockdate;
        $DataList = $this->mpro_kpi->individual_farmer_coaching_export($DateGenerated,$ProgID,$ClusterID);
        if($DataList['count_data'] == 0){
            $DataReturn['success'] = true;
            $DataReturn['count_data'] = 0;
            $this->response($DataReturn, 200);
        }
        //Ambil data query nya =============== (End)

        //Write Excelnya
        $name = 'Farmer Coaching';
        if ($ProgID == 14){
            $name = 'Farmer Coaching';
        }
        $UrlFilenya = $this->WriteExcelForExport($name,$DataList['data']);

        $DataReturn['success'] = true;
        $DataReturn['UrlFilenya'] = $UrlFilenya;
        $DataReturn['count_data'] = $DataList['count_data'];
        $this->response($DataReturn, 200);        
    }
    
    public function individual_farmer_coaching_session_get($ProgID,$ClusterID,$Lockdate) {
        $ProgID = (int) $ProgID;
        $ClusterID = (int) $ClusterID;
        $UrlFilenya = null;

        //Ambil data query nya =============== (Begin)
        //        $ImsIDImp = $this->mjbcocoakpi->GetImsIDList($ProgID);
        //$DateGenerated = $this->mjbcocoakpi->GetCurrentDateGenerated($ProgID);
        $DateGenerated = $Lockdate;
        $DataList = $this->mpro_kpi->individual_farmer_coaching_session_export($DateGenerated,$ProgID,$ClusterID);
        if($DataList['count_data'] == 0){
            $DataReturn['success'] = true;
            $DataReturn['count_data'] = 0;
            $this->response($DataReturn, 200);
        }
        //Ambil data query nya =============== (End)

        //Write Excelnya
        $name = 'Farmer Coaching Session';
        if ($ProgID == 14){
            $name = 'Farmer Coaching Session';
        }
        $UrlFilenya = $this->WriteExcelForExport($name,$DataList['data']);

        $DataReturn['success'] = true;
        $DataReturn['UrlFilenya'] = $UrlFilenya;
        $DataReturn['count_data'] = $DataList['count_data'];
        $this->response($DataReturn, 200);        
    }
    
    public function broadcast_sms_get($ProgID,$ClusterID,$Lockdate) {
        $ProgID = (int) $ProgID;
        $ClusterID = (int) $ClusterID;
        $UrlFilenya = null;

        //Ambil data query nya =============== (Begin)
        //        $ImsIDImp = $this->mjbcocoakpi->GetImsIDList($ProgID);
        //$DateGenerated = $this->mjbcocoakpi->GetCurrentDateGenerated($ProgID);
        $DateGenerated = $Lockdate;
        $DataList = $this->mpro_kpi->broadcast_sms_export($DateGenerated,$ProgID,$ClusterID);
        if($DataList['count_data'] == 0){
            $DataReturn['success'] = true;
            $DataReturn['count_data'] = 0;
            $this->response($DataReturn, 200);
        }
        //Ambil data query nya =============== (End)

        //Write Excelnya
        $name = 'Broadcast SMS';
        if ($ProgID == 14){
            $name = 'Broadcast SMS';
        }
        $UrlFilenya = $this->WriteExcelForExport($name,$DataList['data']);

        $DataReturn['success'] = true;
        $DataReturn['UrlFilenya'] = $UrlFilenya;
        $DataReturn['count_data'] = $DataList['count_data'];
        $this->response($DataReturn, 200);        
    }
    
    public function farmer_id_card_get($ProgID,$ClusterID,$Lockdate) {
        $ProgID = (int) $ProgID;
        $ClusterID = (int) $ClusterID;
        $UrlFilenya = null;

        //Ambil data query nya =============== (Begin)
        //        $ImsIDImp = $this->mjbcocoakpi->GetImsIDList($ProgID);
        //$DateGenerated = $this->mjbcocoakpi->GetCurrentDateGenerated($ProgID);
        $DateGenerated = $Lockdate;
        $DataList = $this->mpro_kpi->farmer_id_card_export($DateGenerated,$ProgID,$ClusterID);
        if($DataList['count_data'] == 0){
            $DataReturn['success'] = true;
            $DataReturn['count_data'] = 0;
            $this->response($DataReturn, 200);
        }
        //Ambil data query nya =============== (End)

        //Write Excelnya
        $name = 'Farmer ID Card';
        if ($ProgID == 14){
            $name = 'Farmer ID Card';
        }
        $UrlFilenya = $this->WriteExcelForExport($name,$DataList['data']);

        $DataReturn['success'] = true;
        $DataReturn['UrlFilenya'] = $UrlFilenya;
        $DataReturn['count_data'] = $DataList['count_data'];
        $this->response($DataReturn, 200);        
    }
    
    public function farmx_get($ProgID,$ClusterID,$Lockdate) {
        $ProgID = (int) $ProgID;
        $ClusterID = (int) $ClusterID;
        $UrlFilenya = null;

        //Ambil data query nya =============== (Begin)
        //        $ImsIDImp = $this->mjbcocoakpi->GetImsIDList($ProgID);
        //$DateGenerated = $this->mjbcocoakpi->GetCurrentDateGenerated($ProgID);
        $DateGenerated = $Lockdate;
        $DataList = $this->mpro_kpi->farmx_export($DateGenerated,$ProgID,$ClusterID);
        if($DataList['count_data'] == 0){
            $DataReturn['success'] = true;
            $DataReturn['count_data'] = 0;
            $this->response($DataReturn, 200);
        }
        //Ambil data query nya =============== (End)

        //Write Excelnya
        $name = 'FarmX';
        if ($ProgID == 14){
            $name = 'FarmX';
        }
        $UrlFilenya = $this->WriteExcelForExport($name,$DataList['data']);

        $DataReturn['success'] = true;
        $DataReturn['UrlFilenya'] = $UrlFilenya;
        $DataReturn['count_data'] = $DataList['count_data'];
        $this->response($DataReturn, 200);        
    }
    
    public function farmgate_get($ProgID,$ClusterID,$Lockdate) {
        $ProgID = (int) $ProgID;
        $ClusterID = (int) $ClusterID;
        $UrlFilenya = null;

        //Ambil data query nya =============== (Begin)
        //        $ImsIDImp = $this->mjbcocoakpi->GetImsIDList($ProgID);
        //$DateGenerated = $this->mjbcocoakpi->GetCurrentDateGenerated($ProgID);
        $DateGenerated = $Lockdate;
        $DataList = $this->mpro_kpi->farmgate_export($DateGenerated,$ProgID,$ClusterID);
        if($DataList['count_data'] == 0){
            $DataReturn['success'] = true;
            $DataReturn['count_data'] = 0;
            $this->response($DataReturn, 200);
        }
        //Ambil data query nya =============== (End)

        //Write Excelnya
        $name = 'FarmGate';
        if ($ProgID == 14){
            $name = 'FarmGate';
        }
        $UrlFilenya = $this->WriteExcelForExport($name,$DataList['data']);

        $DataReturn['success'] = true;
        $DataReturn['UrlFilenya'] = $UrlFilenya;
        $DataReturn['count_data'] = $DataList['count_data'];
        $this->response($DataReturn, 200);        
    }
    
    public function farmretail_get($ProgID,$ClusterID,$Lockdate) {
        $ProgID = (int) $ProgID;
        $ClusterID = (int) $ClusterID;
        $UrlFilenya = null;

        //Ambil data query nya =============== (Begin)
        //        $ImsIDImp = $this->mjbcocoakpi->GetImsIDList($ProgID);
        //$DateGenerated = $this->mjbcocoakpi->GetCurrentDateGenerated($ProgID);
        $DateGenerated = $Lockdate;
        $DataList = $this->mpro_kpi->farmretail_export($DateGenerated,$ProgID,$ClusterID);
        if($DataList['count_data'] == 0){
            $DataReturn['success'] = true;
            $DataReturn['count_data'] = 0;
            $this->response($DataReturn, 200);
        }
        //Ambil data query nya =============== (End)

        //Write Excelnya
        $name = 'FarmRetail';
        
        $UrlFilenya = $this->WriteExcelForExport($name,$DataList['data']);

        $DataReturn['success'] = true;
        $DataReturn['UrlFilenya'] = $UrlFilenya;
        $DataReturn['count_data'] = $DataList['count_data'];
        $this->response($DataReturn, 200);        
    }
    
    public function farmcloud_get($ProgID,$ClusterID,$Lockdate) {
        $ProgID = (int) $ProgID;
        $ClusterID = (int) $ClusterID;
        $UrlFilenya = null;

        //Ambil data query nya =============== (Begin)
        //        $ImsIDImp = $this->mjbcocoakpi->GetImsIDList($ProgID);
        //$DateGenerated = $this->mjbcocoakpi->GetCurrentDateGenerated($ProgID);
        $DateGenerated = $Lockdate;
        $DataList = $this->mpro_kpi->farmcloud_export($DateGenerated,$ProgID,$ClusterID);
        if($DataList['count_data'] == 0){
            $DataReturn['success'] = true;
            $DataReturn['count_data'] = 0;
            $this->response($DataReturn, 200);
        }
        //Ambil data query nya =============== (End)

        //Write Excelnya
        $name = 'FarmCloud';
        
        $UrlFilenya = $this->WriteExcelForExport($name,$DataList['data']);

        $DataReturn['success'] = true;
        $DataReturn['UrlFilenya'] = $UrlFilenya;
        $DataReturn['count_data'] = $DataList['count_data'];
        $this->response($DataReturn, 200);        
    }

    private function WriteExcelForExport($OpsiCall,$DataList){
        //generate data header
        $DataHeader = array('No');
        foreach($DataList[0] as $key => $value){
            array_push($DataHeader,lang($key));
        }

        $DataListExcel = array();
        $no = 1;
        foreach ($DataList as $key => $value) {
            $data = array();
            array_push($data,$no);
            foreach($value as $keyx => $valuex){
                array_push($data,$valuex);
            }
            $DataListExcel[$key] = $data;
            $no++;
        }
        
        $writer = WriterEntityFactory::createXLSXWriter(); // for XLSX files// 
        $namaFile = date('YmdHis') . '_export_excel_'.$OpsiCall.'.xlsx';
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
        $rowHeader = WriterEntityFactory::createRowFromArray($DataHeader, $styleHeader);
        
        $writer->getCurrentSheet()->setName($OpsiCall);
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

        for ($i=0; $i < count($DataListExcel); $i++) {
            $dataRows = $DataListExcel[$i];
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
        $writer->close();
        return $filePath;
    }

    private function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    public function export_summary_get($ProgID,$ClusterID,$Lockdate){
        $ProgID = (int) $ProgID;
        $ClusterID = (int) $ClusterID;
        $DateGenerated = $Lockdate;
        
        $UrlFilenya = null;

        $DataList = $this->mpro_kpi->ExportSummary($DateGenerated,$ProgID);
        if($DataList['achieved']->num_rows() == 0){
            $DataReturn['success'] = true;
            $DataReturn['count_data'] = 0;
            $this->response($DataReturn, 200);
        }

        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';
        
        $objPHPExcel = new PHPExcel();

        // Add some data
        $objPHPExcel->setActiveSheetIndex(0);

        $style_center = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );
        $style_border = array(
              'borders' => array(
                  'allborders' => array(
                      'style' => PHPExcel_Style_Border::BORDER_THIN
                  )
              )
        );
        
        $header = array(
            "AchievedAchievedPalmOilMill" => lang('Palm Oil Mills'),
            "AchievedFarmerReg" => lang('Farmers Registered'),
            "AchievedFarmReg" => lang('Farm Registered'),
            "AchievedHa" => lang('Ha Registered'),
            "AchievedSocSel" => lang('Socialization and Selection'),
            "AchievedFarmerSurveyBP" => lang('Farmer Survey'),
            "AchievedFarmSurvey" => lang('Farm Survey'),
            "AchievedPolygon" => lang('Polygon Mapping'),
            "AchievedFarmerCoach" => lang('Individual Farmer Coaching'),
            "AchievedCoachingSess" => lang('Individual Farmer Coaching Season'),
            "AchievedSms" => lang('Broadcast SMS'),
            "AchievedIdCard" => lang('Farmer ID Card'),
            "AchievedFarmX" => lang('FarmXtension Users'),
            "AchievedFarmG" => lang('FarmGate Users'),
            "AchievedFarmR" => lang('FarmRetail Users'),
            "AchievedFarmC" => lang('FarmCloud Users'),
        );

        $column = "A";
        $objPHPExcel->getActiveSheet()->setCellValue($column.'1', 'Cluster');
        $objPHPExcel->getActiveSheet()->mergeCells($column."1:".$column."2");
        $objPHPExcel->getActiveSheet()->getStyle($column.'1')->applyFromArray($style_center);
        $objPHPExcel->getActiveSheet()->getStyle($column."1:".$column."2")->applyFromArray($style_border);
        $objPHPExcel->getActiveSheet()->getStyle($column.'1')->getFont()->setBold(true);
        $column = "B";
        foreach ($header as $key => $value) {
            $objPHPExcel->getActiveSheet()->getStyle($column.'1')->applyFromArray($style_center);
            $objPHPExcel->getActiveSheet()->getStyle($column."1:".$column."2")->applyFromArray($style_border);
            $objPHPExcel->getActiveSheet()->getStyle($column.'1')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle($column."1:".$column."2")->applyFromArray($style_center);
            
            $objPHPExcel->getActiveSheet()->SetCellValue($column.'1', $value);
            $objPHPExcel->getActiveSheet()->SetCellValue($column.'2', 'Target');
            $objPHPExcel->getActiveSheet()->mergeCells($column."1:".++$column."1");
            $objPHPExcel->getActiveSheet()->SetCellValue($column.'2', 'Achieved');
            
            $objPHPExcel->getActiveSheet()->getStyle($column."1:".$column."2")->applyFromArray($style_center);
            $objPHPExcel->getActiveSheet()->getStyle($column."1:".$column."2")->applyFromArray($style_border);
            $objPHPExcel->getActiveSheet()->getStyle($column.'1')->getFont()->setBold(true);

            $column++;
        }
        
        $counter = 3;
        $tmpclass = "";
        $startRow = -1;
        $previousKey = '';
        $points_array = array();

        $TotalTargetedPalmoilMill = 0;
        $TotalTargetedFarmerReg = 0;
        $TotalTargetedFarmReg = 0;
        $TotalTargetedHa = 0;
        $TotalTargetedSocSel = 0;
        $TotalTargetedFarmerSurveyBP = 0;
        $TotalTargetedFarmSurvey = 0;
        $TotalTargetedPolygon = 0;
        $TotalTargetedFarmerCoach = 0;
        $TotalTargetedCoachingSess = 0;
        $TotalTargetedSms = 0;
        $TotalTargetedIdCard = 0;
        $TotalTargetedFarmX = 0;
        $TotalTargetedFarmG = 0;
        $TotalTargetedFarmR = 0;
        $TotalTargetedFarmC = 0;

        $TotalAchievedPalmOilMill = 0;
        $TotalAchievedFarmerReg = 0;
        $TotalAchievedFarmReg = 0;
        $TotalAchievedHa = 0;
        $TotalAchievedSocSel = 0;
        $TotalAchievedFarmerSurveyBP = 0;
        $TotalAchievedFarmSurvey = 0;
        $TotalAchievedPolygon = 0;
        $TotalAchievedFarmerCoach = 0;
        $TotalAchievedCoachingSess = 0;
        $TotalAchievedSms = 0;
        $TotalAchievedIdCard = 0;
        $TotalAchievedFarmX = 0;
        $TotalAchievedFarmG = 0;
        $TotalAchievedFarmR = 0;
        $TotalAchievedFarmC = 0;

        $DataAchieved = $DataList['achieved']->result_array();
        $DataTargeted = $DataList['targeted']->result_array();
        foreach ($DataAchieved as $key => $value) {
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$counter, $value["Cluster"]);
            $objPHPExcel->getActiveSheet()->getStyle('A'.$counter)->getFont()->setBold(true);            
            
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $counter, $DataTargeted[$key]['TargetedPalmOilMill']);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $counter, $value['AchievedPalmOilMill']);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $counter, $DataTargeted[$key]['TargetedFarmerReg']);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $counter, $value['AchievedFarmerReg']);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $counter, $DataTargeted[$key]['TargetedFarmReg']);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $counter, $value['AchievedFarmReg']);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $counter, $DataTargeted[$key]['TargetedHa']);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $counter, $value['AchievedHa']);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $counter, $DataTargeted[$key]['TargetedSocSel']);
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $counter, $value['AchievedSocSel']);
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $counter, $DataTargeted[$key]['TargetedFarmerSurveyBP']);
            $objPHPExcel->getActiveSheet()->setCellValue('M' . $counter, $value['AchievedFarmerSurveyBP']);
            $objPHPExcel->getActiveSheet()->setCellValue('N' . $counter, $DataTargeted[$key]['TargetedFarmSurvey']);
            $objPHPExcel->getActiveSheet()->setCellValue('O' . $counter, $value['AchievedFarmSurvey']);
            $objPHPExcel->getActiveSheet()->setCellValue('P' . $counter, $DataTargeted[$key]['TargetedPolygon']);
            $objPHPExcel->getActiveSheet()->setCellValue('Q' . $counter, $value['AchievedPolygon']);
            $objPHPExcel->getActiveSheet()->setCellValue('R' . $counter, $DataTargeted[$key]['TargetedFarmerCoach']);
            $objPHPExcel->getActiveSheet()->setCellValue('S' . $counter, $value['AchievedFarmerCoach']);
            $objPHPExcel->getActiveSheet()->setCellValue('T' . $counter, $DataTargeted[$key]['TargetedCoachingSess']);
            $objPHPExcel->getActiveSheet()->setCellValue('U' . $counter, $value['AchievedCoachingSess']);
            $objPHPExcel->getActiveSheet()->setCellValue('V' . $counter, $DataTargeted[$key]['TargetedSms']);
            $objPHPExcel->getActiveSheet()->setCellValue('W' . $counter, $value['AchievedSms']);
            $objPHPExcel->getActiveSheet()->setCellValue('X' . $counter, $DataTargeted[$key]['TargetedIdCard']);
            $objPHPExcel->getActiveSheet()->setCellValue('Y' . $counter, $value['AchievedIdCard']);
            $objPHPExcel->getActiveSheet()->setCellValue('Z' . $counter, $DataTargeted[$key]['TargetedFarmX']);
            $objPHPExcel->getActiveSheet()->setCellValue('AA' . $counter, $value['AchievedFarmX']);
            $objPHPExcel->getActiveSheet()->setCellValue('AB' . $counter, $DataTargeted[$key]['TargetedFarmG']);
            $objPHPExcel->getActiveSheet()->setCellValue('AC' . $counter, $value['AchievedFarmG']);
            $objPHPExcel->getActiveSheet()->setCellValue('AD' . $counter, $DataTargeted[$key]['TargetedFarmR']);
            $objPHPExcel->getActiveSheet()->setCellValue('AE' . $counter, $value['AchievedFarmR']);
            $objPHPExcel->getActiveSheet()->setCellValue('AF' . $counter, $DataTargeted[$key]['TargetedFarmC']);
            $objPHPExcel->getActiveSheet()->setCellValue('AG' . $counter, $value['AchievedFarmC']);

            // Hitung total
            $TotalAchievedPalmOilMill   = $TotalAchievedPalmOilMill + $value['AchievedPalmOilMill'];
            $TotalAchievedFarmerReg     = $TotalAchievedFarmerReg + $value['AchievedFarmerReg'];
            $TotalAchievedFarmReg       = $TotalAchievedFarmReg + $value['AchievedFarmReg'];
            $TotalAchievedHa            = $TotalAchievedHa + $value['AchievedHa'];
            $TotalAchievedSocSel        = $TotalAchievedSocSel + $value['AchievedSocSel'];
            $TotalAchievedFarmerSurveyBP = $TotalAchievedFarmerSurveyBP + $value['AchievedFarmerSurveyBP'];
            $TotalAchievedFarmSurvey    = $TotalAchievedFarmSurvey + $value['AchievedFarmSurvey'];
            $TotalAchievedPolygon       =  $TotalAchievedPolygon + $value['AchievedPolygon'];
            $TotalAchievedFarmerCoach   =  $TotalAchievedFarmerCoach + $value['AchievedFarmerCoach'];
            $TotalAchievedCoachingSess  =  $TotalAchievedCoachingSess + $value['AchievedCoachingSess'];
            $TotalAchievedSms           =  $TotalAchievedSms + $value['AchievedSms'];
            $TotalAchievedIdCard        =  $TotalAchievedIdCard + $value['AchievedIdCard'];
            $TotalAchievedFarmX         =  $TotalAchievedFarmX + $value['AchievedFarmX'];
            $TotalAchievedFarmG         =  $TotalAchievedFarmG + $value['AchievedFarmG'];
            $TotalAchievedFarmR         =  $TotalAchievedFarmR + $value['AchievedFarmR'];
            $TotalAchievedFarmC         =  $TotalAchievedFarmC + $value['AchievedFarmC'];
            
            $TotalTargetedPalmOilMill   = $TotalTargetedPalmOilMill + $DataTargeted[$key]['TargetedPalmOilMill'];
            $TotalTargetedFarmerReg     = $TotalTargetedFarmerReg + $DataTargeted[$key]['TargetedFarmerReg'];
            $TotalTargetedFarmReg       = $TotalTargetedFarmReg + $DataTargeted[$key]['TargetedFarmReg'];
            $TotalTargetedHa            = $TotalTargetedHa + $DataTargeted[$key]['TargetedHa'];
            $TotalTargetedSocSel        = $TotalTargetedSocSel + $DataTargeted[$key]['TargetedSocSel'];
            $TotalTargetedFarmerSurveyBP = $TotalTargetedFarmerSurveyBP + $DataTargeted[$key]['TargetedFarmerSurveyBP'];
            $TotalTargetedFarmSurvey    = $TotalTargetedFarmSurvey + $DataTargeted[$key]['TargetedFarmSurvey'];
            $TotalTargetedPolygon       =  $TotalTargetedPolygon + $DataTargeted[$key]['TargetedPolygon'];
            $TotalTargetedFarmerCoach   =  $TotalTargetedFarmerCoach + $DataTargeted[$key]['TargetedFarmerCoach'];
            $TotalTargetedCoachingSess  =  $TotalTargetedCoachingSess + $DataTargeted[$key]['TargetedCoachingSess'];
            $TotalTargetedSms           =  $TotalTargetedSms + $DataTargeted[$key]['TargetedSms'];
            $TotalTargetedIdCard        =  $TotalTargetedIdCard + $DataTargeted[$key]['TargetedIdCard'];
            $TotalTargetedFarmX         =  $TotalTargetedFarmX + $DataTargeted[$key]['TargetedFarmX'];
            $TotalTargetedFarmG         =  $TotalTargetedFarmG + $DataTargeted[$key]['TargetedFarmG'];
            $TotalTargetedFarmR         =  $TotalTargetedFarmR + $DataTargeted[$key]['TargetedFarmR'];
            $TotalTargetedFarmC         =  $TotalTargetedFarmC + $DataTargeted[$key]['TargetedFarmC'];

            $counter++;
        }

        $column = "A";
//        $objPHPExcel->getActiveSheet()->mergeCells("A".$counter.":B".$counter);
        $objPHPExcel->getActiveSheet()->setCellValue($column.$counter, lang('Total'));
        $objPHPExcel->getActiveSheet()->getStyle($column.$counter)->applyFromArray($style_center);
        $objPHPExcel->getActiveSheet()->getStyle($column.$counter)->getFont()->setBold(true);

//        $objPHPExcel->getActiveSheet()->setCellValue('C'.$counter, $totTargetedFarmerCertified);
//        $objPHPExcel->getActiveSheet()->setCellValue('B'.$counter, $totAchievedFarmerCertified);
    
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $counter, $TotalTargetedPalmOilMill);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $counter, $TotalAchievedPalmOilMill);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $counter, $TotalTargetedFarmerReg);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $counter, $TotalAchievedFarmerReg);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $counter, $TotalTargetedFarmReg);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $counter, $TotalAchievedFarmReg);
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $counter, $TotalTargetedHa);
        $objPHPExcel->getActiveSheet()->setCellValue('I' . $counter, $TotalAchievedHa);
        $objPHPExcel->getActiveSheet()->setCellValue('J' . $counter, $TotalTargetedSocSel);
        $objPHPExcel->getActiveSheet()->setCellValue('K' . $counter, $TotalAchievedSocSel);
        $objPHPExcel->getActiveSheet()->setCellValue('L' . $counter, $TotalTargetedFarmerSurveyBP);
        $objPHPExcel->getActiveSheet()->setCellValue('M' . $counter, $TotalAchievedFarmerSurveyBP);
        $objPHPExcel->getActiveSheet()->setCellValue('N' . $counter, $TotalTargetedFarmSurvey);
        $objPHPExcel->getActiveSheet()->setCellValue('O' . $counter, $TotalAchievedFarmSurvey);
        $objPHPExcel->getActiveSheet()->setCellValue('P' . $counter, $TotalTargetedPolygon);
        $objPHPExcel->getActiveSheet()->setCellValue('Q' . $counter, $TotalAchievedPolygon);
        $objPHPExcel->getActiveSheet()->setCellValue('R' . $counter, $TotalTargetedFarmerCoach);
        $objPHPExcel->getActiveSheet()->setCellValue('S' . $counter, $TotalAchievedFarmerCoach);
        $objPHPExcel->getActiveSheet()->setCellValue('T' . $counter, $TotalTargetedCoachingSess);
        $objPHPExcel->getActiveSheet()->setCellValue('U' . $counter, $TotalAchievedCoachingSess);
        $objPHPExcel->getActiveSheet()->setCellValue('V' . $counter, $TotalTargetedSms);
        $objPHPExcel->getActiveSheet()->setCellValue('W' . $counter, $TotalAchievedSms);
        $objPHPExcel->getActiveSheet()->setCellValue('X' . $counter, $TotalTargetedIdCard);
        $objPHPExcel->getActiveSheet()->setCellValue('Y' . $counter, $TotalAchievedIdCard);
        $objPHPExcel->getActiveSheet()->setCellValue('Z' . $counter, $TotalTargetedFarmX);
        $objPHPExcel->getActiveSheet()->setCellValue('AA' . $counter, $TotalAchievedFarmX);
        $objPHPExcel->getActiveSheet()->setCellValue('AB' . $counter, $TotalTargetedFarmG);
        $objPHPExcel->getActiveSheet()->setCellValue('AC' . $counter, $TotalAchievedFarmG);
        $objPHPExcel->getActiveSheet()->setCellValue('AD' . $counter, $TotalTargetedFarmR);
        $objPHPExcel->getActiveSheet()->setCellValue('AE' . $counter, $TotalAchievedFarmR);
        $objPHPExcel->getActiveSheet()->setCellValue('AF' . $counter, $TotalTargetedFarmC);
        $objPHPExcel->getActiveSheet()->setCellValue('AG' . $counter, $TotalAchievedFarmC);

        $objPHPExcel->getActiveSheet()->getStyle("A" . $counter . ":AM" . $counter)->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A1:" . "AM" . $counter)->applyFromArray($style_border);

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Report');

        $filename = $DateGenerated."_summary_kpi_".time().".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('files/tmp/'.$filename);

        $DataReturn['success'] = true;
        $DataReturn['UrlFilenya'] = 'files/tmp/'.$filename;
        $this->response($DataReturn, 200);
    }
}