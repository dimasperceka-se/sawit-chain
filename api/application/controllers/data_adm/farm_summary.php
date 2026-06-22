<?php
/*
 * @Author: sofyan
 * @Date:   2021-11-08 
*/
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//write excel
require_once 'application/third_party/Spout3/Autoloader/autoload.php';


use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Common\Entity\Style\Border;
use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;

class Farm_summary extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->file = $_FILES;
        $this->load->model('data_adm/mfarm_summary');
    }

    public function grid_main_get(){
        //set bahasa
        if($_SESSION['language'] == "Indonesia"){
            $this->load->language('general', 'indonesia');
        }else{
            $this->load->language('general', 'english');
        }
        
        //sort
        $sorting = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';
        
        //get param
        $pSearch = array(
            'prov' => $this->get('prov'),
            'kab' => $this->get('kab'),
            'kec' => $this->get('kec'),
            'textSearch' => $this->get('textSearch'),
            'CmbPolygonStatus' => $this->get('CmbPolygonStatus'),
        );
        
        $data = $this->mfarm_summary->getGridMainFarmSummary($pSearch,$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        $this->response($data, 200);
    }
    
    // farm_summary_export_excel
    public function farm_summary_export_excel_post() 
    {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);

        //get param
        $pSearch = array(
            'prov' => $this->post('prov'),
            'kab' => $this->post('kab'),
            'kec' => $this->post('kec'),
            'textSearch' => $this->post('textSearch'),
            'CmbPolygonStatus' => $this->post('CmbPolygonStatus'),
        );

        //Get Data Plot
        $dataList  = $this->mfarm_summary->getMainFarmSummaryExcel($pSearch);

        if(count($dataList)){
            //Kolom Header Plot
            $dataHeader = array('No');
            foreach($dataList[0] as $key => $value){
                array_push($dataHeader,lang($key));
            }
            //Kolom Body Plot
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

            $writer = WriterEntityFactory::createXLSXWriter(); // for XLSX files// 
            $namaFile = date('YmdHis') . '_export_excel_plots.xlsx';
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

    public function render_map_post() 
    {
        $ContWidth = (int) $this->post('ContWidth');
        $ContHeight = (int) $this->post('ContHeight');
        $DataView = array();

        $DataView['ContWidth'] = $ContWidth - 17;
        $DataView['ContHeight'] = $ContHeight - 48;

        $this->load->view('data_adm/farm_summary_map', $DataView);
    }

    public function farm_summary_polygon_get() {
        //ini_set('display_errors',true); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $pSearch = array(
            'prov' => $this->get('prov'),
            'kab' => $this->get('kab'),
            'kec' => $this->get('kec'),
            'textSearch' => $this->get('textSearch'),
            'CmbPolygonStatus' => $this->get('CmbPolygonStatus'),
        );

        $data = $this->mfarm_summary->GetFarmSummaryPolygon($pSearch);
        $this->response($data, 200);
    }
    
}
?>