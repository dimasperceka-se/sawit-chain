<?php defined('BASEPATH') OR exit('No direct script access allowed');

//write excel
require_once 'application/third_party/Spout3/Autoloader/autoload.php';


use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
//use Box\Spout\Common\Entity\Style\CellAlignment;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Common\Entity\Style\Border;
use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;

class Report_transaction_mill extends REST_Controller {
   
	
    public function __construct() {
        parent::__construct();
        date_default_timezone_set('UTC');
        $this->load->model('traceability_api/m_report_transaction_mill','_model');
    }
	
	public function fetch_get(){
        $sorting = json_decode($this->get('sort'));
        $sortingField = @$sorting[0]->property;
        $sortingDir = @$sorting[0]->direction;
		
		$InputForm = $this->get();  
        $data = $this->_model->get_data( $InputForm, $this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        
        if($data){
            return $this->response(array('success' => true, 
            'message' => 'Data Berhasil Ditampilkan',
            'total' => $data['total'], 
            'data' => $data['data']),  200);
        }		
    }

    public function export_excel_get(){ 
        $sorting = json_decode($this->get('sort'));
        $sortingField = @$sorting[0]->property;
        $sortingDir = @$sorting[0]->direction;
		
		$InputForm = $this->get();  
        $data = $this->_model->get_data( $InputForm, $this->get('start'),$this->get('limit'),$sortingField,$sortingDir);

        $xls= $this->get('xls');
        if ($data['data']) { 
            if ($xls ==  true ) {
                if(count($data['data'])){

                    //Kolom Header Transaction
                    $dataHeader = array('No');
                    foreach($data['data'][0] as $key => $value){
                        array_push($dataHeader,lang($key));
                    }
                    //Kolom Header Transaction
        
                    //Kolom Body Transaction
                    $dataListExcel = array();
                    $no = 1;
                    foreach ($data['data'] as $key => $value) {
                        $data['data'] = array();
                        array_push($data['data'],$no);
                        foreach($value as $keyx => $valuex){
                            array_push($data['data'],$valuex);
                        }
                        $dataListExcel[$key] = $data['data'];
                        $no++;
                    }
                    //Kolom Body Transaction
                    
        
                    $writer = WriterEntityFactory::createXLSXWriter(); // for XLSX files// 
                    $namaFile = date('YmdHis') . '_export_excel_transaction_report.xlsx';
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
                    
                    $writer->getCurrentSheet()->setName('Transaction Data');
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
            
                    header('Content-Type: application/octet-stream');
                    header("Content-Transfer-Encoding: Binary"); 
                    header("Content-disposition: attachment; filename=\"" . basename(base_url() . $filePath) . "\""); 
                    readfile(base_url() . $filePath); 
                    die;
                }
                // $this->load->view('report_transaction_mill', array('data' => $data['data'])); 
            } else {
                $this->response($data, 200);
            }

        } else {
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }

        return $this->response($this->_output,200);
    }

    public function ComboMill_get(){
        $output = $this->_model->comboMill();
        $this->response($output, 200);
    }

    public function ComboAgent_get(){
        $output = $this->_model->comboAgent($this->get());
        $this->response($output, 200);
    }

    private function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
	 
}