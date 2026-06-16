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
 
class Transaction extends REST_Controller
{ 
    public function __construct(){
        parent::__construct();
        $this->load->model('processing/mprocessing', '_model');
    }

    public function fetch_get(){
        $sorting = json_decode($this->get('sort'));
        $sortingField = @$sorting[0]->property;
        $sortingDir = @$sorting[0]->direction;
		
		//get param
        $pSearch = array(
            'country' => $this->get('country'),
			'prov' => $this->get('prov'),
            'kab' => $this->get('dist'), 
            'textSearch' => $this->get('key') 
        ); 
		
        $data = $this->_model->fetch_data($pSearch, $this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        if($data) $this->response($data, 200); else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }
 
	public function fetchpick_get(){
        $sorting = json_decode($this->get('sort'));
        $sortingField = @$sorting[0]->property;
        $sortingDir = @$sorting[0]->direction;
		 
        $data = $this->_model->fetchpick($this->get('ProcessingID'), $this->get('start'),$this->get('limit'),$sortingField,$sortingDir, $this->get('ProductID'));
        if($data) $this->response($data, 200); else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    public function fetch_product_get(){
        $sorting = json_decode($this->get('sort'));
        $sortingField = @$sorting[0]->property;
        $sortingDir = @$sorting[0]->direction;
         
        $data = $this->_model->fetchProduct($this->get('ProcessingID'), $this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        if($data) $this->response($data, 200); else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    public function proses_batch_post(){
        $ProcessingID = $_POST["ProcessingID"];

        $output = array('success' => false, "message" => lang("Please Pick Process Batch"));
        $model = $this->_model->proses_batch($ProcessingID);
        if($model) {
            $this->response($model, 200);
        }
        $this->response($output, 400);
    }

    public function sent_batch_post(){
        $ProcessingID = $_POST["ProcessingID"];

        $output = array('success' => false, "message" => lang("Please add vehicle"));
        $model = $this->_model->sent_batch($ProcessingID);
        if($model) {
            $this->response($model, 200);
        }
        $this->response($output, 400);
    }

    public function fetchvehicle_get(){
        $sorting = json_decode($this->get('sort'));
        $sortingField = @$sorting[0]->property;
        $sortingDir = @$sorting[0]->direction;
		 
        $data = $this->_model->fetchvehicle($this->get('ProcessingID'), $this->get('start'),$this->get('limit'),$sortingField,$sortingDir, $this->get('ProductID'), $this->get('ProcessingProductID'));
        if($data) $this->response($data, 200); else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    public function fetchvehiclebyID_get(){
        $data = $this->_model->fetchvehiclebyID($this->get('ProcessingID'), $this->get('ProcessingProductID'));
        $this->response($data, 200);
    }

    public function list_vehicle_type_get(){
        $output = array('success' => true);
        $model = $this->_model->list_vehicle_type();
        if($model) {
            $this->response($model, 200);
        }
        $this->response($output, 200);
    }

    public function list_destination_get(){
        $output = array('success' => true);
        $model = $this->_model->list_destination();
        if($model) {
            $this->response($model, 200);
        }
        $this->response($output, 200);
    }	
	 
	public function submit_post(){
        ini_set('display_errors',true);
        error_reporting(E_ALL);
        $data = $this->post(NULL);
        if($data){
            $transaction = $this->_model->submit($data);
            //echo "<pre>".print_r($transaction, 1);die;
            if($transaction){  
                return $this->response($transaction);
            }
        }else{
            return $this->response(array('success' => false, 'error' => 'Data post empty !'),200);
        }
    }

    public function vehicle_list_post(){
        $data = $this->post(NULL);
        foreach ($data as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Traceability_new_Processing_win_FormWinProduct-Form-", '', $key);
            if($value == "") $value = null;

            $paramPost[$keyNew] = $value;
        }
        if($paramPost){
            $vehicle = $this->_model->submitVehicle($paramPost);
            if($vehicle){  
                return $this->response($vehicle);
            }
        }else{
            return $this->response(array('success' => false, 'error' => 'Data post empty !'),200);
        }
    }
    
    public function delete_DELETE($id=0){
        $output = array('success' => true);
        $model = $this->_model->delete($id);
        if($model) {
            $this->response($model, 200);
        }
        $this->response($output, 200);
    }
	
	public function delprocess_GET(){
        $output = array('success' => true);
        $model = $this->_model->delprocess($this->input->get('ProcessingProcessID'));
        if($model) {
            $this->response($model, 200);
        }
        $this->response($output, 200);
    }
    
	public function basicdata_GET() {
        $output = array('success' => false);
        $id = $this->input->get('ProcessingID');
        if((int)$id > 0) {
            $output = $this->_model->basicdata($id);
        }
        //echo "<pre>".print_r($output, 1);die;
        $this->response($output, 200);
    } 
	 
	
	public function cmbProcess_GET(){
        $data = $this->_model->cmbProcess($this->get('query'));
        $this->response($data, 200);
    }

    public function RefProcessing_GET(){
        $data = $this->_model->Ref_Processing($this->get('query'),$this->get('ProductID'));
        
        $this->response($data, 200);
    }

    public function information_grid_refinery_get(){
        
        $data = array("PKO"=>$_SESSION["PKO_REMAINING"],"CPO"=>$_SESSION["CPO_REMAINING"],"ProductPercentageCpo"=>$_SESSION["ProductPercentageCpo"], "ProductPercentagePk"=>$_SESSION["ProductPercentagePk"], "HaveOer"=>$_SESSION["HaveOer"], "flagCpo"=>$_SESSION["flagCpo"], "flagPk"=>$_SESSION["flagPk"],"TotalCapacity"=> $_SESSION["TotalCapacity"]);
        
        $this->response($data, 200);
    }

    public function information_grid_have_oer_get(){
        $data = array(
                 "checkDataExist" => $_SESSION["checkDataExist"]
              );
        
        $this->response($data, 200);
    }
	
	public function ProductType_get(){
        $data = $this->_model->ProductType($this->get('ProductID'));
        $this->response($data, 200);
    }

	public function submit_process_post(){
        ini_set('display_errors',true);
        error_reporting(E_ALL);
        $data = $this->post(NULL);
        if($data){
            $transaction = $this->_model->submit_process($data);
            if($transaction){  
                return $this->response($transaction);
            }
        }else{
            return $this->response(array('success' => false, 'error' => 'Data post empty !'),200);
        }
    }
	
	public function save_pick_post(){
        ini_set('display_errors',true);
        error_reporting(E_ALL);
        $data = $this->post(NULL);
        if($data){
            $transaction = $this->_model->save_pick($data);
            if($transaction){ 
                return $this->response($transaction);
            }
        }else{
            return $this->response(array('success' => false, 'error' => 'Data post empty !'),200);
        }
    }
	
	public function del_pick_GET(){
        $output = array('success' => true); 
		$DespatchDetailID = $this->input->get('DespatchDetailID'); 
        $model = $this->_model->del_pick($DespatchDetailID);
        if($model) {
            $this->response($model, 200);
        }
        $this->response($output, 200);
    }

    public function del_vehicle_get(){
        $output = array('success' => true); 
		$ProcessingProductID = $this->input->get('ProcessingProductID'); 
        $model = $this->_model->del_vehicle($ProcessingProductID);
        if($model) {
            $this->response($model, 200);
        }
        $this->response($output, 200);
    }

    public function export_dispatch_get(){        
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);

        
        //Get Data Farmer
        $dataList           = $this->_model->getDispatchExcel();
        $dataListDispatch   = $this->_model->getDispatchDetailExcel();
        $dataListVehicle    = $this->_model->getDispatchVehicleExcel();

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
            $namaFile = date('YmdHis') . '_dispatch_excel.xlsx';
            $filePath = 'files/tmp/' . $namaFile;
            $writer->openToFile($filePath);
            $writer->getCurrentSheet()->setName('Dispatch');

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

            //Kolom Header Detail
            $dataHeaderDetail = array('No');
            foreach($dataListDispatch[0] as $key => $value){
                array_push($dataHeaderDetail,lang($key));
            }
            //Kolom Header Detail

            //Kolom Body Detail
            $dataListExcelDetail = array();
            $no = 1;
            foreach ($dataListDispatch as $key => $value) {
                $data = array();
                array_push($data,$no);
                foreach($value as $keyx => $valuex){
                    array_push($data,$valuex);
                }
                $dataListExcelDetail[$key] = $data;
                $no++;
            }
            //Kolom Body Detail

            $rowHeaderDetail = WriterEntityFactory::createRowFromArray($dataHeaderDetail, $styleHeader);
            $writer->addNewSheetAndMakeItCurrent()->setName('Dispatch Detail');
            $writer->addRow($rowHeaderDetail);

            for ($i=0; $i < count($dataListExcelDetail); $i++) {
                $dataRowsDetail = $dataListExcelDetail[$i];
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
                /*$cells = [
                    WriterEntityFactory::createCell($dataRows[0], $styleData),
                    WriterEntityFactory::createCell((float) $dataRows[1], $styleFormatAngka),
                    WriterEntityFactory::createCell($dataRows[2], $styleData),
                    WriterEntityFactory::createCell(25569 + (time() / 86400), $styleFormatTanggal),
                    WriterEntityFactory::createCell($dataRows[4], $styleFormatTanggal)
                ];*/
    
                $rowDataDetail = WriterEntityFactory::createRow($cells2);
                $writer->addRow($rowDataDetail);
            }            

            //Kolom Header Vehicle
            $dataHeaderVehicle = array('No');
            foreach($dataListVehicle[0] as $key => $value){
                array_push($dataHeaderVehicle,lang($key));
            }
            //Kolom Header Vehicle

            //Kolom Body Vehicle
            $dataListExcelVehicle = array();
            $no = 1;
            foreach ($dataListVehicle as $key => $value) {
                $data = array();
                array_push($data,$no);
                foreach($value as $keyx => $valuex){
                    array_push($data,$valuex);
                }
                $dataListExcelVehicle[$key] = $data;
                $no++;
            }
            //Kolom Body Vehicle

            $rowHeaderVehicle = WriterEntityFactory::createRowFromArray($dataHeaderVehicle, $styleHeader);
            $writer->addNewSheetAndMakeItCurrent()->setName('Dispatch Vehicle');
            $writer->addRow($rowHeaderVehicle);

            for ($i=0; $i < count($dataListExcelVehicle); $i++) {
                $dataRowsVehicle = $dataListExcelVehicle[$i];
                $cells3 = array();
    
                for ($j=0; $j < count($dataRowsVehicle); $j++) {
                    $styleRow = null;
                    $dataRowVehicle = null;
    
                    //cek apakah numeric
                    if(is_numeric($dataRowsVehicle[$j])){
                        $styleRow = $styleFormatAngka;
                        $dataRowVehicle = (float) $dataRowsVehicle[$j];
                    } else {
                        //cek apakah tanggal
                        if($this->validateDate($dataRowsVehicle[$j]) == true) {
                            $styleRow = $styleFormatTanggal;
                            $dataRowVehicle = 25569 + (strtotime($dataRowsVehicle[$j]) / 86400);
                        } else {
                            $styleRow = $styleData;
                            $dataRowVehicle = $dataRowsVehicle[$j];
                        }
                    }
    
                    $cells3[$j] = WriterEntityFactory::createCell($dataRowVehicle, $styleRow);
                }
                /*$cells = [
                    WriterEntityFactory::createCell($dataRows[0], $styleData),
                    WriterEntityFactory::createCell((float) $dataRows[1], $styleFormatAngka),
                    WriterEntityFactory::createCell($dataRows[2], $styleData),
                    WriterEntityFactory::createCell(25569 + (time() / 86400), $styleFormatTanggal),
                    WriterEntityFactory::createCell($dataRows[4], $styleFormatTanggal)
                ];*/
    
                $rowDataVehicle = WriterEntityFactory::createRow($cells3);
                $writer->addRow($rowDataVehicle);
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

    public function getBatchHaveNotOer_get() {
        $data = $this->_model->getBatchHaveNotOer($this->get('ProductID'));

        $this->response($data, 200);
    }

    public function OwnerStatus_get(){
        $data = $this->_model->OwnerStatus($this->get('query'));
        $this->response($data, 200);
    }

    public function list_transit_get(){
        $output = array('success' => true);
        $model = $this->_model->list_transit($this->get('ProductID'));

        if($model) {
            $this->response($model, 200);
        }

        $this->response($output, 200);
    }

    public function list_product_get(){
        $output = array('success' => true);
        $model = $this->_model->list_product();

        if($model) {
            $this->response($model, 200);
        }

        $this->response($output, 200);
    }
}