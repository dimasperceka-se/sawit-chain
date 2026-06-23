<?php

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

class Refinery extends REST_Controller
{ 
    public function __construct(){
        parent::__construct();
        $this->load->model('dispatch/mrefinery', '_model');
    }

    public function fetch_get(){
        // echo "Asd";exit;
        //sort
        $sorting      = json_decode($this->get('sort'));
        if (isset($sorting[0]->property)) $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        else $sortingField = null;
        if (isset($sorting[0]->direction)) $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';
        else $sortingDir = null;


        $SID = $this->input->get('SID'); 
        $PID = $this->input->get('PID');

        //get param
        $pSearch = array(
            'textSearch' => $this->get('textSearch'),
            'statusSearch' => $this->get('statusSearch')
        );
        
        $dispatch = $this->_model->get_data_dispatch($SID, $PID, $pSearch, $this->get('start'), $this->get('limit'), $sortingField, $sortingDir);
        if($dispatch){ 
            return $this->response(array('success' => true, 
                                         'message' => 'Data Berhasil Ditampilkan',
                                         'sql' => $dispatch['sql'], 
                                         'total' => $dispatch['total'], 
                                         'data' => $dispatch['data']),  200);
        }
        return $this->response($this->_output,200);
    }

    public function grid_reception_detail_get() 
    {   
        $ShippingDate   = $this->get("ShippingDate");   
        $UserID       = $_SESSION['userid'];

        $SID = $this->_model->getSupplyChainID($UserID);

        if($SID){
            $data = $this->_model->getViewReceptionDetail($ShippingDate,$SID);
        }

        if($data){ 
            return $this->response(array('success' => true, 
                                         'message' => 'Data Berhasil Ditampilkan',
                                         'total' => $data['total'], 
                                         'data' => $data['data']),  200);
        }
        return $this->response($this->_output,200);
        
        $this->response($data, 200);
    }

    public function export_reception_get(){
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);

        // echo "Asd";exit;
        //Get Data Farmer
        // $dataReception      = $this->_model->getReceptionList($this->get());
        $dataReception      = $this->_model->getReceptionList();

        if(count($dataReception)){

            //Kolom Header Farmer
            $dataHeader = array('No');
            foreach($dataReception[0] as $key => $value){
                array_push($dataHeader,lang($key));
            }
            //Kolom Header Farmer

            //Kolom Body Farmer
            $dataListExcel = array();
            $no = 1;
            foreach ($dataReception as $key => $value) {
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
            $namaFile = date('YmdHis') . '_reception_excel.xlsx';
            $filePath = 'files/tmp/' . $namaFile;
            $writer->openToFile($filePath);
            $writer->getCurrentSheet()->setName('Reception');

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

    public function export_reception_detail_get($DespatchID=0){
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);

        $UserID       = $_SESSION['userid'];

        $SID = $this->_model->getSupplyChainID($UserID);
        if($SID){
            $dataReception      = $this->_model->getReceptionListDetail($DespatchID,$SID);
        }
       
        if(count($dataReception)){

            //Kolom Header Farmer
            $dataHeader = array('No');
            foreach($dataReception[0] as $key => $value){
                array_push($dataHeader,lang($key));
            }
            //Kolom Header Farmer

            //Kolom Body Farmer
            $dataListExcel = array();
            $no = 1;
            foreach ($dataReception as $key => $value) {
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
            $namaFile = date('YmdHis') . '_reception_excel_detail.xlsx';
            $filePath = 'files/tmp/' . $namaFile;
            $writer->openToFile($filePath);
            $writer->getCurrentSheet()->setName('Reception');

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

    public function getPernerimaan_get(){
        $ShippingDate = $this->get("ShippingDate");
        // $DespatchID = $this->get("DespatchID");
        $SID        = $this->get("SID");
        
        $trans_batch = $this->_model->get_data_edit($ShippingDate, $SID);
        if($trans_batch){ 
            return $this->response(array('success' => true, 
                                         'message' => 'Data Berhasil Ditampilkan',
                                         'data' => $trans_batch['data']),  200);
        }
        return $this->response($this->_output,200);
    }

    public function grid_dispatch_list_get(){
        $ShippingDate = $this->get("ShippingDate");

        $output = $this->_model->grid_dispatch_list($ShippingDate);
        //$output = $this->_model->detail_transaction($this->get());
        $this->response($output, 200);
    }

    public function grid_product_list_get(){
        $ShippingDate = $this->get("ShippingDate");
        $output = $this->_model->grid_product_list($ShippingDate);
        //$output = $this->_model->detail_transaction($this->get());
        $this->response($output, 200);
    }

    public function close_dispatch_post(){
        $output = $this->_model->close_dispatch($this->post());
        return $this->response($output,200);
    }
}