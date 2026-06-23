<?php
/**
 * @Author: nikolius
 * @Date:   2017-02-09 16:11:52
 */
defined('BASEPATH') or exit('No direct script access allowed');
/*
ini_set('display_errors',true);
error_reporting(E_ALL);
*/
//write excel
require_once 'application/third_party/Spout/Autoloader/autoload.php';
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;
use Box\Spout\Writer\Style\StyleBuilder;
use Box\Spout\Writer\Style\Color;
use Box\Spout\Writer\Style\Border;
use Box\Spout\Writer\Style\BorderBuilder;

class Report_sql_view extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('report/msql_view','msql_view');
    }

    public function main_list_get(){
        $sorting = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $data = $this->msql_view->getMainListSqlView($this->get('SqlvName_Search'),$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        $this->response($data, 200);
    }

    public function form_sql_view_get(){
        $data = $this->msql_view->getFormSqlView($this->get('SqlvID'));
        $this->response($data, 200);
    }

    public function sql_view_post(){
        /*
        $proses['success'] = false;
        $proses['message'] = "Oh no";
        $this->response($proses, 200);
        $this->response('Process Failed', 400);
        */

        //var proses (begin)
        foreach ($this->post() as $key => $value) {
            if($value == ""){
                $varPost[$key] = null;
            }else{
                $varPost[$key] = $value;
            }
        }
        $varPost['userid'] = $_SESSION['userid'];
        //var proses (end)

        if($this->post('SqlvID') == ""){
            //insert
            $proses = $this->msql_view->insertSqlView($varPost);
        }else{
            //update
            $proses = $this->msql_view->updateSqlView($varPost);
        }

        if ($proses) {
            $this->response($proses, 200);
        } else {
            $this->response('Process Failed', 400);
        }
    }

    public function sql_view_delete(){
        $proses = $this->msql_view->deleteSqlView($this->delete('SqlvID'));
        if ($proses) {
            $this->response($proses, 200);
        } else {
            $this->response(array('error' => 'Process failed'), 404);
        }
    }

    public function prep_run_query_get(){
        //memory limit set
        ini_set('memory_limit', '-1');

        $data = $this->msql_view->getPrepRunQuery((int) $this->get('SqlvID'));

        //memory limit set
        //ini_set('memory_limit', $mem_ini);

        $this->response($data, 200);
    }

    public function sql_view_main_list_get(){
        //memory limit set
        ini_set('memory_limit', '-1');

        $data = $this->msql_view->getMainListSqlViewQuery($this->get('SqlvID'),$this->get('start'),$this->get('limit'),'limit');

        $this->response($data, 200);
    }

    public function sql_view_export_excel_node_post() {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);

        $SqlvID = (int) $this->post('SqlvID');

        //curl
        $url = $this->config->item('base_url_util').'sqlview/excel/'.$SqlvID.'/'.$_SESSION['userid'];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $arrResult = json_decode($result, true);
        if( ($arrResult['success'] == true) && file_exists('files/tmp/'.$arrResult['filename']) ) {
            $this->response(array('success' => TRUE, 'filenya' => base_url() . 'files/tmp/'.$arrResult['filename']), 200);
        } else {
            $this->response(array('success' => FALSE, 'message' => lang('Export Excel Failed')), 400);
        }
    }

    public function sql_view_export_excel_xml_post(){
     
        $this->load->helper('date');
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);

         $userid_apicin = $this->post('userid_apicin');
         if(!isset($userid_apicin) || $userid_apicin == ""){
             $userid_apicin = $_SESSION['userid'];
         }

        //ambil data
        $dataList = $this->msql_view->getMainListSqlViewQuery((int) $this->post('SqlvID'),null,null,'no_limit',$userid_apicin);
      
       

        $data = $this->msql_view->getPrepRunQuery((int) $this->post('SqlvID'));
       
        
        //generate nama file excel
        $sqlViewName = $data['sqlViewName'];
        $sqlViewName = str_replace(' ','-',$sqlViewName);
        $sqlViewName = preg_replace('/[^A-Za-z0-9\-]/', '', $sqlViewName);
        $NamaFile = date('YmdHis').'_'.$sqlViewName;
        
        

        //Proses Excel
        $DataView['dataList'] = $dataList;
        $DataView['data'] = $data;
        $DataView['filename'] = $NamaFile;        

        $tmp = $this->load->view('report_data/sql_view_excel_xml', $DataView, TRUE);
        
        $filenamepath = 'files/sql_view_temp/'.$NamaFile.".xls";
        $filenamepath = filter_var($filenamepath,FILTER_SANITIZE_STRING);
        file_put_contents($filenamepath,$tmp);

        $this->response(array('success' => TRUE, 'filenya' => base_url() . $filenamepath), 200);
    }
    

    public function sql_view_export_excel_post(){
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);

        //ambil data  (begin)
        $dataList = $this->msql_view->getMainListSqlViewQuery((int) $this->post('SqlvID'),null,null,'no_limit');

        $data = $this->msql_view->getPrepRunQuery((int) $this->post('SqlvID'));
        $dataKolom = $data['fieldNya'];

        //generate nama file excel
        $sqlViewName = $data['sqlViewName'];
        $sqlViewName = str_replace(' ','_',$sqlViewName);

        //Kolom Header Farmer
        $dataHeader = array('No');
        foreach($dataKolom as $key){
            array_push($dataHeader,lang($key["name"]));
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

        $writer = WriterFactory::create(Type::XLSX); // for XLSX files
        //$writer = WriterFactory::create(Type::CSV); // for CSV files
        //$writer = WriterFactory::create(Type::ODS); // for ODS files

        $writer->setTempFolder('files/tmp/');
        $namaFile = $sqlViewName.'.xlsx';
        $filePath = 'files/tmp/' . $namaFile;
        $defaultStyle = (new StyleBuilder())
                ->setFontName('Arial')
                ->setFontSize(10)
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
                ->setFontBold()
                ->setBorder($borderDefa)
                ->setBackgroundColor(Color::LIGHT_BLUE)
                ->build();

        //row header
        $writer->addRowWithStyle($dataHeader, $styleHeader); // add a row at a time
        //style data
        $styleData = (new StyleBuilder())
                ->setBorder($borderDefa)
                ->build();

        //Sheet Farmer Data
        $writer->getCurrentSheet()->setName('Sql View Data');
        $writer->addRowsWithStyle($dataListExcel, $styleData);


        $writer->close();
        $this->response(array('success' => true, 'filenya' => base_url() . $filePath), 200);

        //generate nama file excel
        // $sqlViewName = $data['sqlViewName'];
        // $sqlViewName = str_replace(' ','_',$sqlViewName);

        // //generate data header
        // $dataHeader = array('No.');
        // foreach ($dataKolom as $key => $value) {
        //     $dataHeader[] = $value['name'];
        // }

        // //generate data list
        // $dataListExcel = array();
        // foreach ($dataList as $key => $value) {
        //     array_unshift($value,($key+1));
        //     foreach ($value as $key1 => $value1) {

        //         //pengecualian untuk tidak diformat ke angka
        //         switch ($key1) {
        //             case 'Nin':
        //             case 'Handphone':
        //             case 'Latitude':
        //             case 'Longitude':
        //                 #No Convert
        //             break;
        //             default:
        //                 //cek tipe datanya
        //                 if(is_numeric($value1)){
        //                     $value1 = (float) $value1;
        //                 }
        //             break;
        //         }

        //         $dataListExcel[$key][] = $value1;
        //     }
        // }
        // //ambil data  (end)

        // $writer = WriterFactory::create(Type::XLSX); // for XLSX files
        // //$writer = WriterFactory::create(Type::CSV); // for CSV files
        // //$writer = WriterFactory::create(Type::ODS); // for ODS files

        // $writer->setTempFolder('files/sql_view_temp/');
        // $namaFile = date('YmdHis').'_'.$sqlViewName.'.xlsx';
        // $filePath = 'files/sql_view/'.$namaFile;
        // $defaultStyle = (new StyleBuilder())
        //     ->setFontName('Arial')
        //     ->setFontSize(9)
        //     ->setShouldWrapText(false)
        //     ->build();
        // $writer->setDefaultRowStyle($defaultStyle)
        //     ->openToFile($filePath);

        // $borderDefa = (new BorderBuilder())
        //     ->setBorderBottom(Color::BLACK, Border::WIDTH_MEDIUM, Border::STYLE_SOLID)
        //     ->setBorderTop(Color::BLACK, Border::WIDTH_MEDIUM, Border::STYLE_SOLID)
        //     ->setBorderRight(Color::BLACK, Border::WIDTH_MEDIUM, Border::STYLE_SOLID)
        //     ->setBorderLeft(Color::BLACK, Border::WIDTH_MEDIUM, Border::STYLE_SOLID)
        //     ->build();

        // //style
        // $styleHeader = (new StyleBuilder())
        //     ->setFontBold()
        //     ->setBorder($borderDefa)
        //     ->setBackgroundColor(Color::LIGHT_BLUE)
        //     ->build();

        // //row header
        // $writer->addRowWithStyle($dataHeader,$styleHeader); // add a row at a time

        // //style data
        // $styleData = (new StyleBuilder())
        //     ->setBorder($borderDefa)
        //     ->build();

        // //data
        // $writer->addRowsWithStyle($dataListExcel, $styleData);

        // $writer->close();

        // //ini_set('memory_limit', $mem_ini);
        // $this->response(array('success' => TRUE, 'filenya' => base_url() . 'files/sql_view/'.$namaFile), 200);
        // exit;
    }

    public function sql_view_export_excel_old_post(){
        //ambil data  (begin)
        $dataList = $this->msql_view->getMainListSqlViewQuery((int) $this->post('SqlvID'),null,null,'no_limit');

        $data = $this->msql_view->getPrepRunQuery((int) $this->post('SqlvID'));
        $dataKolom = $data['fieldNya'];

        //ambil data  (end)

        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';

        $mem_ini = ini_get('memory_limit');
        ini_set('memory_limit', '1048576M');

        //=============== MULAI TULIS EXCEL (BEGIN) ===================================================================//
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("PT Koltiva")
                ->setLastModifiedBy("PT Koltiva")
                ->setTitle("List Data Export SQL View")
                ->setSubject("List Data Export SQL View")
                ->setDescription("List Data Export SQL View")
                ->setKeywords("List Data Export SQL View")
                ->setCategory("List Data Export SQL View");

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('List');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        //set style
        $styleFont = array(
            'font' => array(
                'name' => 'Arial',
                'size' => '9',
            ),
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
            ),
        );

        $styleFontBold = array(
            'font' => array(
                'name' => 'Arial',
                'size' => '9',
                'bold' => true,
            ),
        );

        $styleFontBoldTitle = array(
            'font' => array(
                'name' => 'Arial',
                'size' => '9',
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
        );

        $styleFontBoldHeader = array(
            'font' => array(
                'name' => 'Arial',
                'size' => '9',
                'bold' => true,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '8DB4E3'),
            ),
        );
        $styleFontBoldBgRedCenter = array(
            'font' => array(
                'name' => 'Arial',
                'size' => '9',
                'bold' => true,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'C0504D'),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $styleBorderFull = array(
            'borders' => array(
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );

        //set width column
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(5);

        //tulis judul
        $objPHPExcel->getActiveSheet()->setCellValue('B2', 'Data SQL View');
        $objPHPExcel->getActiveSheet()->getStyle('B2')->applyFromArray($styleFontBoldTitle);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B2:G2');

        //tabel header (begin)
        $objPHPExcel->getActiveSheet()->setCellValue('B4', 'No');
        $columnstart = 2;
        for ($i=0; $i < count($dataKolom); $i++) {
            $objPHPExcel->getActiveSheet()->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart).'4', $dataKolom[$i]['name']);
            $columnstart++;
        }
        $columnstart--;
        $columnstartLast = $columnstart;
        $objPHPExcel->getActiveSheet()->getStyle('B4:'.PHPExcel_Cell::stringFromColumnIndex($columnstartLast).'4')->applyFromArray($styleFontBoldHeader);
        $objPHPExcel->getActiveSheet()->getStyle('B4:'.PHPExcel_Cell::stringFromColumnIndex($columnstartLast).'4')->applyFromArray($styleBorderFull,false);
        //tabel header (end)

        $rowStart = 5;
        $incre = 0;
        foreach ($dataList as $val) {
            $val['no'] = $incre+1;
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$rowStart, $val['no']);

            $columnstart = 2;
            for ($i=0; $i < count($dataKolom); $i++) {
                $objPHPExcel->getActiveSheet()->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart).$rowStart, $val[$dataKolom[$i]['name']]);
                $columnstart++;
            }

            $objPHPExcel->getActiveSheet()->getStyle('B'.$rowStart.':'.PHPExcel_Cell::stringFromColumnIndex($columnstartLast).$rowStart)->applyFromArray($styleFont);
            $objPHPExcel->getActiveSheet()->getStyle('B'.$rowStart.':'.PHPExcel_Cell::stringFromColumnIndex($columnstartLast).$rowStart)->applyFromArray($styleBorderFull,false);

            $rowStart++;
            $incre++;
        }

        //=============== MULAI TULIS EXCEL (END) =====================================================================//

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $namaFile = date('YmdHis').'_export_excel_sql_view.xlsx';
        $objWriter->save('files/sql_view/'.$namaFile);
        ini_set('memory_limit', $mem_ini);
        $this->response(array('success' => TRUE, 'filenya' => base_url() . 'files/sql_view/'.$namaFile), 200);
        exit;
    }

    public function sql_view_export_csv_post(){
        //$mem_ini = ini_get('memory_limit');
        ini_set('memory_limit', '-1');

        //ambil data  (begin)
        $dataList = $this->msql_view->getMainListSqlViewQuery((int) $this->post('SqlvID'),null,null,'no_limit');

        $data = $this->msql_view->getPrepRunQuery((int) $this->post('SqlvID'));
        $dataKolom = $data['fieldNya'];

        //generate nama file excel
        $sqlViewName = $data['sqlViewName'];
        $sqlViewName = str_replace(' ','_',$sqlViewName);

        //generate data header
        $dataHeader = array('No.');
        foreach ($dataKolom as $key => $value) {
            $dataHeader[] = $value['name'];
        }

        //generate data list
        $dataListExcel = array();
        foreach ($dataList as $key => $value) {
            array_unshift($value,($key+1));
            foreach ($value as $key1 => $value1) {
                $dataListExcel[$key][] = $value1;
            }
        }
        //ambil data  (end)

        $writer = WriterFactory::create(Type::CSV); // for CSV files

        $namaFile = date('YmdHis').'_'.$sqlViewName.'.csv';
        $filePath = 'files/sql_view/'.$namaFile;
        $defaultStyle = (new StyleBuilder())
            ->setFontName('Arial')
            ->setFontSize(9)
            ->setShouldWrapText(false)
            ->build();
        $writer->setDefaultRowStyle($defaultStyle)
            ->openToFile($filePath);

        //row header
        $writer->addRow($dataHeader); // add a row at a time

        //data
        $writer->addRows($dataListExcel);

        $writer->close();

        //ini_set('memory_limit', $mem_ini);
        $this->response(array('success' => TRUE, 'filenya' => base_url() . 'files/sql_view/'.$namaFile), 200);
        exit;
    }

    public function sql_view_export_csv_old_post(){
        //ambil data  (begin)
        $dataList = $this->msql_view->getMainListSqlViewQuery((int) $this->post('SqlvID'),null,null,'no_limit');

        $data = $this->msql_view->getPrepRunQuery((int) $this->post('SqlvID'));
        $dataKolom = $data['fieldNya'];

        //ambil data  (end)

        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';

        $mem_ini = ini_get('memory_limit');
        ini_set('memory_limit', '1048576M');

        //=============== MULAI TULIS EXCEL (BEGIN) ===================================================================//
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("PT Koltiva")
                ->setLastModifiedBy("PT Koltiva")
                ->setTitle("List Data Export SQL View")
                ->setSubject("List Data Export SQL View")
                ->setDescription("List Data Export SQL View")
                ->setKeywords("List Data Export SQL View")
                ->setCategory("List Data Export SQL View");

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('List');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        //set style
        $styleFont = array(
            'font' => array(
                'name' => 'Arial',
                'size' => '9',
            ),
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
            ),
        );

        $styleFontBold = array(
            'font' => array(
                'name' => 'Arial',
                'size' => '9',
                'bold' => true,
            ),
        );

        $styleFontBoldTitle = array(
            'font' => array(
                'name' => 'Arial',
                'size' => '9',
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
        );

        $styleFontBoldHeader = array(
            'font' => array(
                'name' => 'Arial',
                'size' => '9',
                'bold' => true,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '8DB4E3'),
            ),
        );
        $styleFontBoldBgRedCenter = array(
            'font' => array(
                'name' => 'Arial',
                'size' => '9',
                'bold' => true,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'C0504D'),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $styleBorderFull = array(
            'borders' => array(
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );

        //tabel header (begin)
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'No');
        $columnstart = 0;
        for ($i=0; $i < count($dataKolom); $i++) {
            $objPHPExcel->getActiveSheet()->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart).'1', $dataKolom[$i]['name']);
            $columnstart++;
        }
        $columnstart--;
        $columnstartLast = $columnstart;
        $objPHPExcel->getActiveSheet()->getStyle('A1:'.PHPExcel_Cell::stringFromColumnIndex($columnstartLast).'1')->applyFromArray($styleFontBoldHeader);
        $objPHPExcel->getActiveSheet()->getStyle('A1:'.PHPExcel_Cell::stringFromColumnIndex($columnstartLast).'1')->applyFromArray($styleBorderFull,false);
        //tabel header (end)

        $rowStart = 2;
        $incre = 0;
        foreach ($dataList as $val) {
            $val['no'] = $incre+1;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$rowStart, $val['no']);

            $columnstart = 0;
            for ($i=0; $i < count($dataKolom); $i++) {
                $objPHPExcel->getActiveSheet()->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart).$rowStart, $val[$dataKolom[$i]['name']]);
                $columnstart++;
            }

            $objPHPExcel->getActiveSheet()->getStyle('A'.$rowStart.':'.PHPExcel_Cell::stringFromColumnIndex($columnstartLast).$rowStart)->applyFromArray($styleFont);
            $objPHPExcel->getActiveSheet()->getStyle('A'.$rowStart.':'.PHPExcel_Cell::stringFromColumnIndex($columnstartLast).$rowStart)->applyFromArray($styleBorderFull,false);

            $rowStart++;
            $incre++;
        }

        //=============== MULAI TULIS EXCEL (END) =====================================================================//

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
        $namaFile = date('YmdHis').'_export_excel_sql_view.csv';
        $objWriter->save('files/sql_view/'.$namaFile);
        ini_set('memory_limit', $mem_ini);
        $this->response(array('success' => TRUE, 'filenya' => base_url() . 'files/sql_view/'.$namaFile), 200);
        exit;
    }

    public function share_user_list_get(){
        $data = $this->msql_view->getShareUserList($this->get('SqlvID'));
        $this->response($data, 200);
    }

    public function share_user_list_filter_get(){
        $sorting = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $data = $this->msql_view->getShareUserFilterList($this->get('filter_name'),$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        $this->response($data, 200);
    }

    public function insert_share_user_post(){
        //var proses (begin)
        foreach ($this->post() as $key => $value) {
            if($value == ""){
                $varPost[$key] = null;
            }else{
                $varPost[$key] = $value;
            }

            switch ($key) {
                case 'UserIDs':
                    $varPost[$key] = json_decode($value);
                break;
            }
        }
        $varPost['userid'] = $_SESSION['userid'];
        //var proses (end)

        $proses = $this->msql_view->insertShareUser($varPost);
        $this->response($proses, 200);
    }

    public function share_user_delete(){
        $proses = $this->msql_view->deleteShareUser($this->delete('id'));
        if ($proses['success']) {
            $this->response($proses, 200);
        } else {
            $this->response('Process Failed', 400);
        }
    }

    public function grid_add_filter_get(){
        $data = $this->msql_view->getGridAddFilter($this->get('SqlvID'));
        $this->response($data, 200);
    }

    public function cmb_add_filter_by_get(){
        $data = $this->msql_view->getCmbAddFilterBy($this->get('SqlvID'));
        $this->response($data, 200);
    }

    public function add_filter_item_post(){
        $varPost = $this->post();

        //prep variabel (begin)
        foreach ($varPost as $key => $value) {
            if($value == "") $value = null;

            $paramPost[$key] = $value;
        }
        //prep variabel (end)

        if($paramPost['opsiPost'] == "insert"){
            //insert
            $proses = $this->msql_view->insertAddFilterItem($paramPost);
        }else{
            //update
            $proses = $this->msql_view->updateAddFilterItem($paramPost);
        }
        $this->response($proses, 200);
    }

    public function add_filter_item_delete(){
        $FilterID = (int) $this->delete('FilterID');
        $proses = $this->msql_view->deleteAddFilterItem($FilterID);
        $this->response($proses, 200);
    }

}
?>