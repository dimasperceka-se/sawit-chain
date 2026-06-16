<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
//Supaya bisa diakses dari CrossDomain
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    //Just exit with 200 OK with the above headers for OPTIONS method
    exit(0);
}

//write excel
require_once 'application/third_party/Spout3/Autoloader/autoload.php';
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
//use Box\Spout\Common\Entity\Style\CellAlignment;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Common\Entity\Style\Border;
use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;

class Logsync extends REST_Controller
{

    public function __construct()
    {
        $this->file = $_FILES;
        parent::__construct();
        $this->load->model('system/mlogsync');
    }


    public function log_sync_upload_grid_get()
    {
        $data  = $this->mlogsync->readMenus($this->get(), 'limit');
        $this->response($data, 200);
    }
    
    public function mw2_log_process_grid_get()
    {
        $data  = $this->mlogsync->readMenusMw2LogProcess($this->get(), 'limit');
        $this->response($data, 200);
    }

    public function mw2_event_json_grid_get()
    {
        $data  = $this->mlogsync->readMenusMw2EventJson($this->get(), 'limit');
        $this->response($data, 200);
    }

    public function mw_pull_log_grid_get()
    {
        $data  = $this->mlogsync->readMenusMwPullLog($this->get(), 'limit');
        $this->response($data, 200);
    }

    private function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    public function log_sync_upload_export_excel_post()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $dataList  = $this->mlogsync->readMenus($this->post(), 'no_limit')['data'];
        //generate nama file excel
        $fileName = 'Log_syng_upload';

        //generate data header
        $dataHeader = array('No.',lang('Payload'),lang('Remark'),lang('Sender'),lang('Date Created'),lang('Username'));
        
        //generate data list
        $dataListExcel = array();
        foreach ($dataList as $key => $value) {
            array_unshift($value,($key+1));
            foreach ($value as $key1 => $value1) {
                if($key1 === "LogID"){
                    continue;
                }else{
                    $dataListExcel[$key][] = $value1;
                }
            }
        }
        //ambil data  (end)

        $writer = WriterEntityFactory::createXLSXWriter();
        $filePath = 'files/tmp/'.$fileName.'.xlsx';
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
            ->setFormat('d-mmm-YY')
            ->build();
        
        //row data
        for ($i=0; $i < count($dataListExcel); $i++) {
            $dataRows = $dataListExcel[$i];
            $cells = array();

            for ($j=0; $j < count($dataRows); $j++) {
                $styleRow = null;
                $dataRow = null;

                //cek apakah numeric
                if(is_numeric($dataRows[$j])){
                    $num_length = strlen((string)$dataRows[$j]);
                    if ($num_length >= 12){
                        $styleRow = $styleData;
                        $dataRow = (string)$dataRows[$j];
                    } else {
                        $styleRow = $styleFormatAngka;
                        $dataRow = (float) $dataRows[$j];
                    }
                } else {
                    //cek apakah tanggal
                    if($this->validateDate($dataRows[$j]) == true) {
                        $styleRow = $styleFormatTanggal;
                        $dataRow = $dataRows[$j];
                    } else {
                        $styleRow = $styleData;
                        $dataRow = lang($dataRows[$j]);
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
    }

    public function mw2_log_process_export_excel_post()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $dataList  = $this->mlogsync->readMenusMw2LogProcess($this->post(), 'no_limit')['data'];
        //generate nama file excel
        $fileName = 'mw2_log_process';

        //generate data header
        $dataHeader = array('No.',lang('Log'),lang('Proc Name'),lang('Date Created'));
        
        //generate data list
        $dataListExcel = array();
        foreach ($dataList as $key => $value) {
            array_unshift($value,($key+1));
            foreach ($value as $key1 => $value1) {
                if($key1 === "id"){
                    continue;
                }else{
                    $dataListExcel[$key][] = $value1;
                }
            }
        }
        //ambil data  (end)

        $writer = WriterEntityFactory::createXLSXWriter();
        $filePath = 'files/tmp/'.$fileName.'.xlsx';
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
            ->setFormat('d-mmm-YY')
            ->build();
        
        //row data
        for ($i=0; $i < count($dataListExcel); $i++) {
            $dataRows = $dataListExcel[$i];
            $cells = array();

            for ($j=0; $j < count($dataRows); $j++) {
                $styleRow = null;
                $dataRow = null;

                //cek apakah numeric
                if(is_numeric($dataRows[$j])){
                    $num_length = strlen((string)$dataRows[$j]);
                    if ($num_length >= 12){
                        $styleRow = $styleData;
                        $dataRow = (string)$dataRows[$j];
                    } else {
                        $styleRow = $styleFormatAngka;
                        $dataRow = (float) $dataRows[$j];
                    }
                } else {
                    //cek apakah tanggal
                    if($this->validateDate($dataRows[$j]) == true) {
                        $styleRow = $styleFormatTanggal;
                        $dataRow = $dataRows[$j];
                    } else {
                        $styleRow = $styleData;
                        $dataRow = lang($dataRows[$j]);
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
    }

    public function mw2_event_json_export_excel_post(Type $var = null)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $dataList  = $this->mlogsync->readMenusMw2EventJson($this->post(), 'no_limit')['data'];
        //generate nama file excel
        $fileName = 'mw2_event_json';

        //generate data header
        $dataHeader = array('No.',lang('JSON'),lang('Event UID'),lang('Program UID'),lang('Date Created'));
        
        //generate data list
        $dataListExcel = array();
        foreach ($dataList as $key => $value) {
            array_unshift($value,($key+1));
            foreach ($value as $key1 => $value1) {
                if($key1 === "id"){
                    continue;
                }else{
                    $dataListExcel[$key][] = $value1;
                }
            }
        }
        //ambil data  (end)

        $writer = WriterEntityFactory::createXLSXWriter();
        $filePath = 'files/tmp/'.$fileName.'.xlsx';
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
            ->setFormat('d-mmm-YY')
            ->build();
        
        //row data
        for ($i=0; $i < count($dataListExcel); $i++) {
            $dataRows = $dataListExcel[$i];
            $cells = array();

            for ($j=0; $j < count($dataRows); $j++) {
                $styleRow = null;
                $dataRow = null;

                //cek apakah numeric
                if(is_numeric($dataRows[$j])){
                    $num_length = strlen((string)$dataRows[$j]);
                    if ($num_length >= 12){
                        $styleRow = $styleData;
                        $dataRow = (string)$dataRows[$j];
                    } else {
                        $styleRow = $styleFormatAngka;
                        $dataRow = (float) $dataRows[$j];
                    }
                } else {
                    //cek apakah tanggal
                    if($this->validateDate($dataRows[$j]) == true) {
                        $styleRow = $styleFormatTanggal;
                        $dataRow = $dataRows[$j];
                    } else {
                        $styleRow = $styleData;
                        $dataRow = lang($dataRows[$j]);
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
    }
    
    public function mw_pull_log_export_excel_post(Type $var = null)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $dataList  = $this->mlogsync->readMenusMwPullLog($this->post(), 'limit')['data'];
        //generate nama file excel
        $fileName = 'mw_pull_log2019';

        //generate data header
        $dataHeader = array('No.',lang('Query'),lang('Error Msg'),lang('Table Reff'),lang('Event UID'), lang('Date Exec'));
        
        //generate data list
        $dataListExcel = array();
        foreach ($dataList as $key => $value) {
            array_unshift($value,($key+1));
            foreach ($value as $key1 => $value1) {
                if($key1 === "mw_log_id"){
                    continue;
                }else{
                    $dataListExcel[$key][] = $value1;
                }
            }
        }
        //ambil data  (end)

        $writer = WriterEntityFactory::createXLSXWriter();
        $filePath = 'files/tmp/'.$fileName.'.xlsx';
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
            ->setFormat('d-mmm-YY')
            ->build();
        
        //row data
        for ($i=0; $i < count($dataListExcel); $i++) {
            $dataRows = $dataListExcel[$i];
            $cells = array();

            for ($j=0; $j < count($dataRows); $j++) {
                $styleRow = null;
                $dataRow = null;

                //cek apakah numeric
                if(is_numeric($dataRows[$j])){
                    $num_length = strlen((string)$dataRows[$j]);
                    if ($num_length >= 12){
                        $styleRow = $styleData;
                        $dataRow = (string)$dataRows[$j];
                    } else {
                        $styleRow = $styleFormatAngka;
                        $dataRow = (float) $dataRows[$j];
                    }
                } else {
                    //cek apakah tanggal
                    if($this->validateDate($dataRows[$j]) == true) {
                        $styleRow = $styleFormatTanggal;
                        $dataRow = $dataRows[$j];
                    } else {
                        $styleRow = $styleData;
                        $dataRow = lang($dataRows[$j]);
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
    }
    
}