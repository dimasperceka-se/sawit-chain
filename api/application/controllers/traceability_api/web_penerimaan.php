<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once 'application/third_party/Spout3/Autoloader/autoload.php';

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
//use Box\Spout\Common\Entity\Style\CellAlignment;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Common\Entity\Style\Border;
use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;
class web_penerimaan extends REST_Controller {
    
    public $_output = array('success' => false, 'error' => 'Data is not valid'); //response data
    
    public function __construct() {
        parent::__construct();
        date_default_timezone_set('UTC');
        $this->load->model('traceability_api/m_webpenerimaan','_model');
    }
    public function fetch_get() {

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

        $trans_batch = $this->_model->get_data_trans($SID, $PID, $pSearch, $this->get('start'), $this->get('limit'), $sortingField, $sortingDir);
        if($trans_batch){ 
            return $this->response(array('success' => true, 
                                         'message' => 'Data Berhasil Ditampilkan',
                                         'total' => $trans_batch['total'], 
                                         'data' => $trans_batch['data']),  200);
        }
        return $this->response($this->_output,200);
    }

    public function data_edit_get() {
        $SID = $this->input->get('SID'); 
        $SBID = $this->input->get('SBID');

        $trans_batch = $this->_model->get_data_edit($SID, $SBID);
        if($trans_batch){ 
            return $this->response(array('success' => true, 
                                         'message' => 'Data Berhasil Ditampilkan',
                                         'data' => $trans_batch['data']),  200);
        }
        return $this->response($this->_output,200);
    }
    public function submit_post(){
        ini_set('display_errors',true);
        error_reporting(E_ALL);
        $data = $this->post(NULL);
        if($data){
            $trans_batch = $this->_model->submit($data);
            if($trans_batch){ 
                return $this->response($trans_batch);
            }
        }else{
            return $this->response(array('success' => false, 'error' => 'Data post empty !'),200);
        }
    }
	
	function report_cetak_kuitansi_get($SupplyTransID){ 
		$data['data'] = $this->_model->getTransaksi($SupplyTransID);
		$data['quality'] = $this->_model->getTransaksiQuality($SupplyTransID);
		echo $this->load->view('kwitansi_supplier', $data);
		 
    }
    
    public function fetch_detail_transaction_get(){
        $output = $this->_model->fetch_detail_transaction($this->get());
        //$output = $this->_model->detail_transaction($this->get());
        $this->response($output, 200);
    }

    public function export_detail_transaction_get(){
        $data = $this->_model->export_detail_transaction($this->get());
        $dataList = $data['data'];
        
        if(count($dataList)){

            //Kolom Header Farmer
            // pakai kolom statis
            $dataHeader = array('No','Farmer ID','Name','Type','Gross','Nett Weight');
            // foreach($dataList[0] as $key => $value){
            //     array_push($dataHeader,lang($key));
            // }
            //Kolom Header Farmer

            //Kolom Body Farmer
            $dataDetail = array();
            $exceptionColumn = array('SupplyTransID', 'SupplyBatchID', 'DateTransaction','SupplyID');
            $no = 1;
            foreach ($dataList as $key => $value) {
                $data = array();
                array_push($data,$no);
                foreach($value as $keyx => $valuex){
                    if (!in_array($keyx, $exceptionColumn))
                        array_push($data,$valuex);
                }
                $dataListExcel[$key] = $data;
                $no++;
            }
            //Kolom Body Farmer

            $writer = WriterEntityFactory::createXLSXWriter(); // for XLSX files// 
            $namaFile = date('YmdHis') . '_export_transactions.xlsx';
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
            
            $writer->getCurrentSheet()->setName(lang("Transactions"));
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
    
            $writer->close();
    
            $this->response(array('success' => TRUE, 'filenya' => base_url() . $filePath), 200);
        }
    }

    public function export_detail_batch_get(){
        $data = $this->_model->export_detail_batch($this->get());
        $dataList = $data['data'];
        
        if(count($dataList)){

            //Kolom Header Farmer
            $dataHeader = array('No','Farmer ID','Name','Type','Gross','Nett Weight');
        
            //Kolom Body Farmer
            $dataListExcel = array();
            $exceptionColumn = array('SupplyTransID', 'SupplyBatchID', 'DateTransaction','SupplyID');
            $no = 1;
            foreach ($dataList as $key => $value) {
                $data = array();
                array_push($data,$no);
                foreach($value as $keyx => $valuex){
                    if (!in_array($keyx, $exceptionColumn))
                        array_push($data,$valuex);
                }
                
                $dataListExcel[$key] = $data;
                $no++;
            }
            //Kolom Body Farmer

            $writer = WriterEntityFactory::createXLSXWriter(); // for XLSX files// 
            $namaFile = date('YmdHis') . '_export_transactions.xlsx';
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
            
            $writer->getCurrentSheet()->setName(lang("Transactions"));
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
    
            $writer->close();
    
            $this->response(array('success' => TRUE, 'filenya' => base_url() . $filePath), 200);
        }
    }

    public function export_batch_get($SID=0){
    
        $UserID = $_SESSION['userid'];
    
        $SID = $this->_model->get_data_user($UserID);
        
        $data = $this->_model->export_batch($SID);
        
        $dataList = $data['data'];
        
        if(count($dataList)){
           
            //Kolom Header Farmer
            $dataHeader = array('No','Batch Number','Name','Weight','Status','Date Created');
        
            //Kolom Body Farmer
            $dataListExcel = array();
            $exceptionColumn = array('SupplyTransID', 'SupplyBatchID', 'DateTransaction','SupplyID');
            $no = 1;
            foreach ($dataList as $key => $value) {
                $data = array();
                array_push($data,$no);
                foreach($value as $keyx => $valuex){
                    if (!in_array($keyx, $exceptionColumn))
                        array_push($data,$valuex);
                }
                
                $dataListExcel[$key] = $data;
                $no++;
            }
            //Kolom Body Farmer

            $writer = WriterEntityFactory::createXLSXWriter(); // for XLSX files// 
            $namaFile = date('YmdHis') . '_export_batch.xlsx';
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
            
            $writer->getCurrentSheet()->setName(lang("Batching"));
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
    
            $writer->close();
    
            $this->response(array('success' => TRUE, 'filenya' => base_url() . $filePath), 200);
        }
    }

    private function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    public function export_reception_get($param_string=0){
    
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);

        $DateStart = $this->input->get('TextFilterStartShipmentDate');
        $DateEnd = $this->input->get('TextFilterEndShipmentDate');

        $dataTrans = $this->_model->export_grid_reception($DateStart, $DateEnd);
    
        $dataTransaction = $dataTrans['data'];
        
        $data = $this->_model->export_grid_reception_detail($DateStart,$DateEnd);

        $dataList = $data['data'];

        if(count($dataTransaction)){

            // pakai kolom statis
            $dataHeader = array('No','Tanggal','No mobil','Tonase Bruto (kg)','Tonase Netto (kg)','Nama Supplier DO');

            $dataTransaksi = array();
            $exceptionColumn = array('SupplyTransID', 'SupplyBatchID', 'DateTransaction','SupplyID');
            $no = 1;
            foreach ($dataTransaction as $key => $value) {
                $data = array();
                array_push($data,$no);
                foreach($value as $keyx => $valuex){
                    if (!in_array($keyx, $exceptionColumn))
                        array_push($data,$valuex);
                }
                $dataTransaksi[$key] = $data;
                $no++;
            }

            //Kolom 
            $writer = WriterEntityFactory::createXLSXWriter(); // for XLSX files// 
            $namaFile = date('YmdHis') . '_export_transactions.xlsx';
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
            
            $writer->getCurrentSheet()->setName(lang("Transaksi"));
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

            for ($i=0; $i < count($dataTransaksi); $i++) {
                $dataRows = $dataTransaksi[$i];
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
    
                $rowData = WriterEntityFactory::createRow($cells);
                $writer->addRow($rowData);
            }

            //sheet 2
            $dataHeaderDetail = array('No');
            foreach($dataList[0] as $key => $value){
                array_push($dataHeaderDetail,lang($key));
            }

            $dataListDetail = array();
            $no = 1;
            foreach ($dataList as $key => $value) {
                $data = array();
                array_push($data,$no);
                foreach($value as $keyx => $valuex){
                    array_push($data,$valuex);
                }
                $dataListDetail[$key] = $data;
                $no++;
            }

            $rowHeaderDetail = WriterEntityFactory::createRowFromArray($dataHeaderDetail, $styleHeader);
            $writer->addNewSheetAndMakeItCurrent()->setName('Detail');
            $writer->addRow($rowHeaderDetail);

            for ($i=0; $i < count($dataListDetail); $i++) {
                $dataRowsDetail = $dataListDetail[$i];
                $cells2 = array();
    
                for ($j=0; $j < count($dataRowsDetail); $j++) {
                    $styleRow = null;
                    $dataRowDetail = null;
    
                    //cek apakah numeric
                    if(is_numeric($dataRowsDetail[$j])){
                        $styleRow = $styleFormatAngka;
                        $dataRowDetail = (float) $dataRowsDetail[$j];
                    } else {
                        //cek apakah tanggal
                        if($this->validateDate($dataRowsDetail[$j]) == true) {
                            $styleRow = $styleFormatTanggal;
                            $dataRowDetail = 25569 + (strtotime($dataRowsDetail[$j]) / 86400);
                        } else {
                            $styleRow = $styleData;
                            $dataRowDetail = $dataRowsDetail[$j];
                        }
                    }
    
                    $cells2[$j] = WriterEntityFactory::createCell($dataRowDetail, $styleRow);
                }
    
                $rowDataDetail = WriterEntityFactory::createRow($cells2);
                $writer->addRow($rowDataDetail);
            }  

            $writer->close();
    
            $this->response(array('success' => TRUE, 'filenya' => base_url() . $filePath), 200);
        } else{
            $this->response(array('success' => FALSE, 'filenya' => ''));
            exit;
        }
    
    }
	
}