<?php
/******************************************
 *  Author : fikrifauzul@gmail.com
 *  Created On : 13-05-2020
 *  File : polygon_over.php
 *******************************************/
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

class Polygon_over extends REST_Controller
{
    public function __construct()
    {
        $this->file = $_FILES;
        parent::__construct();
        $this->load->model('data_adm/mpolygonover');
    }

    public function grid_main_get() {
        $sorting      = json_decode($this->get('sort'));
        if (isset($sorting[0]->property)) $sortingField = $sorting[0]->property; else $sortingField = null;
        if (isset($sorting[0]->direction)) $sortingDir = $sorting[0]->direction; else $sortingDir = null;
        $start        = (int) $this->get('start');
        $limit        = (int) $this->get('limit');

        $data = $this->mpolygonover->GetMainGrid($start, $limit, $sortingField, $sortingDir);
        $this->response($data, 200);
    }

    public function add_polygon_grid_get() {
        $sorting      = json_decode($this->get('sort'));
        if (isset($sorting[0]->property)) $sortingField = $sorting[0]->property; else $sortingField = null;
        if (isset($sorting[0]->direction)) $sortingDir = $sorting[0]->direction; else $sortingDir = null;
        $start        = (int) $this->get('start');
        $limit        = (int) $this->get('limit');

        //get param
        $pSearch = array(
            'TxtSearchLabel' => filter_var($this->get('TxtSearchLabel'),FILTER_SANITIZE_STRING),
            'CmbStatusCheck' => filter_var($this->get('CmbStatusCheck'),FILTER_SANITIZE_STRING),
            'UserId' => (int) $this->get('UserId')
        );

        $data = $this->mpolygonover->GetAddPolygonGrid($pSearch, $start, $limit, $sortingField, $sortingDir);
        $this->response($data, 200);
    }

    public function polygon_compare_grid_get() {
        $sorting      = json_decode($this->get('sort'));
        if (isset($sorting[0]->property)) $sortingField = $sorting[0]->property; else $sortingField = null;
        if (isset($sorting[0]->direction)) $sortingDir = $sorting[0]->direction; else $sortingDir = null;
        $start        = (int) $this->get('start');
        $limit        = (int) $this->get('limit');

        //get param
        $pSearch = array(
            'UserId' => (int) $this->get('UserId')
        );

        $data = $this->mpolygonover->GetPolygonCompareGrid($pSearch, $start, $limit, $sortingField, $sortingDir);
        $this->response($data, 200);
    }

    public function polygon_compare_export_excel_get() {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        //ambil data  (begin)
        $dataList = $this->mpolygonover->GetPolygonCompareGridExport();

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
            $namaFile = date('YmdHis') . '_export_excel_polygon_overlap.xlsx';
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

    public function generate_landuse_get(){
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        $sql = "SELECT
            a.OGR_FID, 
            ST_AsGeoJson(SHAPE) geo,
            a.ObjectID, 
            a.RegionalDecree, 
            a.`Function`, 
            a.FunctionCode, 
            a.FunctionDescription, 
            a.Abbreviation, 
            a.`Year`, 
            a.ProvinceID
        FROM
            ktv_landuse_idn AS a    
        ";
        $DataListProses = $this->db->query($sql)->result_array();
        // $return = array();
        // $return = json_decode($DataListProses[0]["geo"]);
        // $data = $return->coordinates[0];

        // $ArrSusunanPolygon = array();
        // for ($j = 0; $j < count($data); $j++) {
        //     $ArrSusunanPolygon[] = "{$data[$j][1]} {$data[$j][0]}";
        // }
        // $SusunanPolygon = implode(",", $ArrSusunanPolygon);
        $nomor = 0;
        $SusunanPolygon = array();
        for ($i=0; $i < count($DataListProses); $i++) {            
            $return = json_decode($DataListProses[$i]["geo"]);
            $data = $return->coordinates[0];

            $ArrSusunanPolygon = array();
            for ($j = 0; $j < count($data); $j++) {
                $ArrSusunanPolygon[] = "{$data[$j][1]} {$data[$j][0]}";
            }
            $SusunanPolygon = implode(",", $ArrSusunanPolygon);

            if ($SusunanPolygon != "") {
                $TextPolgyon = " ST_GeomFromText('POLYGON(( $SusunanPolygon ))') ";
                $TextPolgyonWithSrid = " ST_GEOMFROMTEXT('POLYGON(( $SusunanPolygon ))', 4326) ";

                //Cek valid by MySQL
                $sql = " SELECT ST_IsValid($TextPolgyon) AS HasilCek ";
                $DataCekPolygon = $this->db->query($sql)->row_array();

                if ($DataCekPolygon['HasilCek'] == "1") {
                    //Koordinat CenterLatLong

                    $sql = "INSERT INTO `ktv_landuse_idn_new` SET
                            `OGR_FID` = ?,
                            `ObjectID` = ?,
                            `RegionalDecree` = ?,
                            `SHAPE` = $TextPolgyonWithSrid ,
                            `Function` = ?,
                            `FunctionCode` = ?,
                            `FunctionDescription` = ?,
                            `Abbreviation` = ?,
                            `Year` = ?,
                            `ProvinceID` = ?
                    ";
                    $p = array(
                        $DataListProses[$i]['OGR_FID'],
                        $DataListProses[$i]['ObjectID'],
                        $DataListProses[$i]['RegionalDecree'],
                        $DataListProses[$i]['Function'],
                        $DataListProses[$i]['FunctionCode'],
                        $DataListProses[$i]['FunctionDescription'],
                        $DataListProses[$i]['Abbreviation'],
                        $DataListProses[$i]['Year'],
                        $DataListProses[$i]['ProvinceID']
                    );
                    $query = $this->db->query($sql, $p);
                    if($query){
                        $nomor += 1;
                    }
                }
            }
        }
        echo '<pre>'; print_r($nomor); exit;
    }

    public function generate_compare_get(){
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);

        //Proses Compare ========================= (Begin)
        $sql = "SELECT
                spg.MemberID,
                spg.PlotNr,
                spg.SurveyNr,
                ST_ASTEXT ( spg.Polygon ) AS Polytext,
                spg.Revision,
                spg.CreatedBy,
                spg.DateCreated,
                e.ProvinceID,
                gtemp.id
            FROM
                ktv_survey_plot_polygon_geo spg
                INNER JOIN ktv_survey_plot sp ON spg.MemberID = sp.MemberID 
                AND spg.PlotNr = sp.PlotNr 
                AND spg.SurveyNr = sp.SurveyNr
                LEFT JOIN ktv_survey_plot_polygon_geo_temp gtemp ON gtemp.MemberID = spg.MemberID 
                AND gtemp.PlotNr = spg.PlotNr 
                AND gtemp.SurveyNr = spg.SurveyNr
                LEFT JOIN ktv_village c ON sp.`VillageID` = c.`VillageID`
                LEFT JOIN ktv_subdistrict d ON c.SubDistrictID = d.`SubDistrictID`
                LEFT JOIN ktv_district f ON d.DistrictID = f.DistrictID
                LEFT JOIN ktv_province e ON f.ProvinceID = e.ProvinceID 
            WHERE
                gtemp.id IS NULL";

        $DataListProses = $this->db->query($sql)->result_array();
        // echo '<pre>'; print_r($DataListProses); exit;

        $nomor = 0;

        for ($i=0; $i < count($DataListProses); $i++) {
            $sql = "SELECT
                        OGR_FID
                    FROM
                        `ktv_landuse_idn_new` a
                    WHERE
                        ProvinceID = ?
                    AND 
                        ST_Intersects(a.`SHAPE`, ST_POLYFROMTEXT(?, 4326) )
                    ";
            $p = array(
                $DataListProses[$i]['ProvinceID'],
                $DataListProses[$i]['Polytext']
            );
            $DataCompare = $this->db->query($sql,$p)->result_array();

            if($DataCompare[0]['OGR_FID'] != "") {
                for ($j=0; $j < count($DataCompare); $j++) {
                    if($DataCompare[$j]['OGR_FID'] != "") {
                        $sql = "INSERT INTO `ktv_survey_plot_polygon_geo_temp` SET
                                `MemberID` = ?,
                                `PlotNr` = ?,
                                `SurveyNr` = ?,
                                `Revision` = ?,
                                `OGR_FID` = ?,
                                `DateCreated` = NOW(),
                                `CreatedBy` = ?
                                ";
                        $p = array(
                            $DataListProses[$i]['MemberID'],
                            $DataListProses[$i]['PlotNr'],
                            $DataListProses[$i]['SurveyNr'],
                            $DataListProses[$i]['Revision'],
                            $DataCompare[$j]['OGR_FID'],
                            $_SESSION['userid']
                        );
                        $query = $this->db->query($sql,$p);
        
                        $nomor++;
                    }
                }
            }
        }

        echo "<pre>";
        print_r($nomor);
        die;
    }
}