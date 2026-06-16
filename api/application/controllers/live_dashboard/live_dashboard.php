<?php

defined('BASEPATH') OR exit('No direct script access allowed');

//library Write Excel
require_once 'application/third_party/Spout/Autoloader/autoload.php';
use Box\Spout\Common\Type;
use Box\Spout\Writer\Style\Border;
use Box\Spout\Writer\Style\BorderBuilder;
use Box\Spout\Writer\Style\Color;
use Box\Spout\Writer\Style\StyleBuilder;
use Box\Spout\Writer\WriterFactory;
class Live_dashboard extends REST_Controller {

    public function __construct() {

        parent::__construct();
        $this->load->model('dashboard/mlive_dash');
    }

    private function WriteExcelForExport($OpsiCall,$DataList){
        //generate data header
        $DataHeader = array('No.');
        foreach ($DataList as $key => $value) {
            foreach ($value as $k1 => $v1) {
                $DataHeader[] = $k1;
            }
            break;
        }
        
        //generate data list
        $DataListExcel = array();
        foreach ($DataList as $key => $value) {
            array_unshift($value,($key+1));
            foreach ($value as $key1 => $value1) {

                //pengecualian untuk tidak diformat ke angka
                switch ($key1) {
                    default:
                        //cek tipe datanya
                        if(is_numeric($value1)){
                            $value1 = (float) $value1;
                        }
                    break;
                }

                $DataListExcel[$key][] = $value1;
            }
        }

        $writer = WriterFactory::create(Type::XLSX); // for XLSX files

        $writer->setTempFolder('files/sql_view_temp/');
        $namaFile = date('YmdHis').'_'.$OpsiCall.'.xlsx';
        $filePath = 'files/sql_view/'.$namaFile;
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
        $writer->addRowWithStyle($DataHeader,$styleHeader); // add a row at a time

        //style data
        $styleData = (new StyleBuilder())
            ->setBorder($borderDefa)
            ->build();

        //data
        $writer->addRowsWithStyle($DataListExcel, $styleData);
        $writer->close();
        return $filePath;
    }

    function wave3_get() {
        $METABASE_SITE_URL = "https://analytics.koltiva.com";
        $METABASE_SECRET_KEY = "c16bfe1664ffb9cff812e859044147f7d3064d02cfdd59b424874791fb9befa0";

        
        # php >= 5.6
        $signer = new \Lcobucci\JWT\Signer\Hmac\Sha256();
        $token = (new \Lcobucci\JWT\Builder())
                ->withClaim('resource', [
                    'dashboard' => 5,
                ])
                ->withClaim('params', (object) [])
                ->getToken($signer, new Lcobucci\JWT\Signer\Key($METABASE_SECRET_KEY));

        $url_iframe = $METABASE_SITE_URL . "/embed/dashboard/" . $token . "#bordered=false";

        $result['url'] = $url_iframe;
        $this->response($result, 200);
    }

    function wave4_get() {
        $METABASE_SITE_URL = "https://analytics.koltiva.com";
        $METABASE_SECRET_KEY = "c16bfe1664ffb9cff812e859044147f7d3064d02cfdd59b424874791fb9befa0";

        
        # php >= 5.6
        $signer = new \Lcobucci\JWT\Signer\Hmac\Sha256();
        $token = (new \Lcobucci\JWT\Builder())
                ->withClaim('resource', [
                    'dashboard' => 10,
                ])
                ->withClaim('params', (object) [])
                ->getToken($signer, new Lcobucci\JWT\Signer\Key($METABASE_SECRET_KEY));

        $url_iframe = $METABASE_SITE_URL . "/embed/dashboard/" . $token .  "#bordered=false&titled=true";

        $result['url'] = $url_iframe;
        $this->response($result, 200);
    }
    
    public function cargill_farmer_selection_get($ProgID) {
        $ProgID = (int) $ProgID;

        $DataList = $this->mlive_dash->CargillFarmerSelection($ProgID);
        if ($DataList['count_data'] == 0) {
            $DataReturn['success'] = true;
            $DataReturn['count_data'] = 0;
            $this->response($DataReturn, 200);
        }
        //Ambil data query nya =============== (End)
        //Write Excelnya
        //echo '<pre>'; print_r($DataList); exit;
        $file_name = 'farmer_selection';
        $UrlFilenya = $this->WriteExcelForExport($file_name, $DataList['data']);

        $DataReturn['success'] = true;
        $DataReturn['UrlFilenya'] = $UrlFilenya;
        $this->response($DataReturn, 200);
    }
    
    public function cargill_farmer_inspection_get($ProgID) {
        $ProgID = (int) $ProgID;

        $DataList = $this->mlive_dash->CargillFarmerInspection($ProgID);
        if ($DataList['count_data'] == 0) {
            $DataReturn['success'] = true;
            $DataReturn['count_data'] = 0;
            $this->response($DataReturn, 200);
        }
        //Ambil data query nya =============== (End)
        //Write Excelnya
        //echo '<pre>'; print_r($DataList); exit;
        $file_name = 'farmer_inspection';
        $UrlFilenya = $this->WriteExcelForExport($file_name, $DataList['data']);

        $DataReturn['success'] = true;
        $DataReturn['UrlFilenya'] = $UrlFilenya;
        $this->response($DataReturn, 200);
    }
    
    public function cargill_polygon_comply_get($ProgID) {
        $ProgID = (int) $ProgID;

        $DataList = $this->mlive_dash->CargillPolygonComply($ProgID);
        if ($DataList['count_data'] == 0) {
            $DataReturn['success'] = true;
            $DataReturn['count_data'] = 0;
            $this->response($DataReturn, 200);
        }
        //Ambil data query nya =============== (End)
        //Write Excelnya
        //echo '<pre>'; print_r($DataList); exit;
        $file_name = 'polygon_comply';
        $UrlFilenya = $this->WriteExcelForExport($file_name, $DataList['data']);

        $DataReturn['success'] = true;
        $DataReturn['UrlFilenya'] = $UrlFilenya;
        $this->response($DataReturn, 200);
    }
    
    public function cargill_polygon_selected_get($ProgID) {
        $ProgID = (int) $ProgID;

        $DataList = $this->mlive_dash->CargillPolygonSelected($ProgID);
        if ($DataList['count_data'] == 0) {
            $DataReturn['success'] = true;
            $DataReturn['count_data'] = 0;
            $this->response($DataReturn, 200);
        }
        //Ambil data query nya =============== (End)
        //Write Excelnya
        //echo '<pre>'; print_r($DataList); exit;
        $file_name = 'polygon_selected';
        $UrlFilenya = $this->WriteExcelForExport($file_name, $DataList['data']);

        $DataReturn['success'] = true;
        $DataReturn['UrlFilenya'] = $UrlFilenya;
        $this->response($DataReturn, 200);
    }
    
    public function cargill_coc_attandence_get($ProgID) {
        $ProgID = (int) $ProgID;

        $DataList = $this->mlive_dash->CargillCOCAttandence($ProgID);
        if ($DataList['count_data'] == 0) {
            $DataReturn['success'] = true;
            $DataReturn['count_data'] = 0;
            $this->response($DataReturn, 200);
        }
        //Ambil data query nya =============== (End)
        //Write Excelnya
        //echo '<pre>'; print_r($DataList); exit;
        $file_name = 'coc_attandence';
        $UrlFilenya = $this->WriteExcelForExport($file_name, $DataList['data']);

        $DataReturn['success'] = true;
        $DataReturn['UrlFilenya'] = $UrlFilenya;
        $this->response($DataReturn, 200);
    }
    
    public function cargill_coaching_session_get($ProgID) {
        $ProgID = (int) $ProgID;

        $DataList = $this->mlive_dash->CargillCoachingSession($ProgID);
        if ($DataList['count_data'] == 0) {
            $DataReturn['success'] = true;
            $DataReturn['count_data'] = 0;
            $this->response($DataReturn, 200);
        }
        //Ambil data query nya =============== (End)
        //Write Excelnya
        //echo '<pre>'; print_r($DataList); exit;
        $file_name = 'coaching_session';
        $UrlFilenya = $this->WriteExcelForExport($file_name, $DataList['data']);

        $DataReturn['success'] = true;
        $DataReturn['UrlFilenya'] = $UrlFilenya;
        $this->response($DataReturn, 200);
    }
    
    function app_log_activity_get() {
        $METABASE_SITE_URL = "https://analytics.koltiva.com";
        $METABASE_SECRET_KEY = "c16bfe1664ffb9cff812e859044147f7d3064d02cfdd59b424874791fb9befa0";

        
        # php >= 5.6
        $signer = new \Lcobucci\JWT\Signer\Hmac\Sha256();
        $token = (new \Lcobucci\JWT\Builder())
                ->withClaim('resource', [
                    'dashboard' => 22,
                ])
                ->withClaim('params', (object) [])
                ->getToken($signer, new Lcobucci\JWT\Signer\Key($METABASE_SECRET_KEY));

        $url_iframe = $METABASE_SITE_URL . "/embed/dashboard/" . $token . "#bordered=false";
        
        $result['url'] = $url_iframe;
        $this->response($result, 200);
    }
    
    function jb_cocoa_get($DashboardID) {
        $DashboardID = (int) $DashboardID;
        $METABASE_SITE_URL = "https://analytics.koltiva.com";
        $METABASE_SECRET_KEY = "c16bfe1664ffb9cff812e859044147f7d3064d02cfdd59b424874791fb9befa0";

        
        # php >= 5.6
        $signer = new \Lcobucci\JWT\Signer\Hmac\Sha256();
        $token = (new \Lcobucci\JWT\Builder())
                ->withClaim('resource', [
                    'dashboard' => $DashboardID,
                ])
                ->withClaim('params', (object) [])
                ->getToken($signer, new Lcobucci\JWT\Signer\Key($METABASE_SECRET_KEY));

        $url_iframe = $METABASE_SITE_URL . "/embed/dashboard/" . $token . "#bordered=false";

        $result['url'] = $url_iframe;
        $this->response($result, 200);        
    }
    
    function mars_get($DashboardID) {
        $DashboardID = (int) $DashboardID;
        $METABASE_SITE_URL = "https://analytics.koltiva.com";
        $METABASE_SECRET_KEY = "c16bfe1664ffb9cff812e859044147f7d3064d02cfdd59b424874791fb9befa0";

        
        # php >= 5.6
        $signer = new \Lcobucci\JWT\Signer\Hmac\Sha256();
        $token = (new \Lcobucci\JWT\Builder())
                ->withClaim('resource', [
                    'dashboard' => $DashboardID,
                ])
                ->withClaim('params', (object) [])
                ->getToken($signer, new Lcobucci\JWT\Signer\Key($METABASE_SECRET_KEY));

        $url_iframe = $METABASE_SITE_URL . "/embed/dashboard/" . $token . "#bordered=false";

        $result['url'] = $url_iframe;
        $this->response($result, 200);        
    }
}
