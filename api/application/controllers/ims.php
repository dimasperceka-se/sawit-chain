<?php

defined('BASEPATH') or exit('No direct script access allowed');

//write excel
require_once 'application/third_party/Spout3/Autoloader/autoload.php';
//require_once 'application/third_party/Spout/Autoloader/autoload.php';
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\WriterMultiSheetsAbstract;
use Box\Spout\Common\Type;
use Box\Spout\Reader\Common\Creator\ReaderFactory;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Box\Spout\Common\Entity\Style\Border;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Common\Entity\WriterFactory;
use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;

ini_set('display_errors',false);
error_reporting(0);

class Ims extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->file = $_FILES;
        $this->load->model('certification/mims');
        $this->load->library("awsfileupload");
    }

    private function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
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

        $writer = WriterEntityFactory::createXLSXWriter(); // for XLSX files

        $namaFile = date('YmdHis').'_'.$OpsiCall;
        $filePath = 'files/sql_view_temp/'.$namaFile.'.xlsx';
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
        $writer->addRow($rowHeader);
        
        //style data
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
        for ($i=0; $i < count($DataListExcel); $i++) {
            $dataRows = $DataListExcel[$i];
            $cells = array();

            for ($j=0; $j < sizeof($dataRows); $j++) {
                $styleRow = null;
                $dataRow = null;

                //cek apakah numeric
                if(is_numeric($dataRows[$j])){
                    $styleRow = $styleFormatAngka;
                    $dataRow = (float) $dataRows[$j];
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

            $rowData = WriterEntityFactory::createRow($cells);
            $writer->addRow($rowData);
        }
        
        $writer->close();
        return $filePath;
    }

    public function data_review_excel_post() {
        ini_set('display_errors', true);
        error_reporting(E_ALL);
        ini_set('memory_limit', '-1');
        
        $RepID = (int) $this->post("RepID");
        $IMSID = (int) $this->post("IMSID");
        
        //data yg diperlukan (begin)
        $dataReview = $this->mims->getDataReview($RepID);
        
        if ($dataReview) {
            $queryList = $this->db->query(sprintf($dataReview['sqlNya'], $IMSID));
            $dataList = $queryList->result_array();            
            if(sizeof($dataList) > 0){
                $writer = WriterEntityFactory::createXLSXWriter(); // for XLSX files

                $writer->setTempFolder('files/sql_view_temp/');
                $ReportName = str_replace(' ','-', $dataReview['ReportName']);
                $ReportName = preg_replace('/[^A-Za-z0-9\-]/', '', $ReportName);
                $namaFile = date('YmdHis') . '_' . $ReportName;
                $filePath = 'files/sql_view/'.$namaFile.'.xlsx';
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

                //style data
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

                $empty[] = WriterEntityFactory::createCell('');
                $EmptyRows = WriterEntityFactory::createRow($empty, null);
                
                $HeaderFarComply = array('No.');
                foreach ($dataList[0] as $key => $v) {
                    $HeaderFarComply[] = $key;
                }
                $rowHeader = WriterEntityFactory::createRowFromArray($HeaderFarComply, $styleHeader);
                $writer->addRow($rowHeader);
                
                /*foreach ($dataList as $k => $value) {
                    $rowIsi = array();

                    foreach ($value as $key => $v){
                        $rowIsi[] = $v;
                    }

                    array_unshift($rowIsi, ($k + 1));
                    $rowData = WriterEntityFactory::createRowFromArray($rowIsi, $styleData);
                    $writer->addRow($rowData);
                }*/
                
                //echo '<pre>'; print_r($dataList); exit;
                for ($i=0; $i < count($dataList); $i++) {
                    $dataRows = $dataList[$i];
                    $cells = array();
                    $cells[] = WriterEntityFactory::createCell((int) ($i+1), $styleFormatAngka);
        
                    foreach ($dataRows as $key => $v){
                        $styleRow = null;
                        $dataRow = null;
                        
                        //Chek pengecualian untuk cek format
                        if(!in_array($key, array('KTP'))){
                            if(is_numeric($v)){
                                $styleRow = $styleFormatAngka;
                                $dataRow = (float) $v;
                            } else {
                                //cek apakah tanggal
                                if($this->validateDate($v) == true) {
                                    $styleRow = $styleFormatTanggal;
                                } else {
                                    $styleRow = $styleData;
                                }
                                $dataRow = $v;
                            }
                        } else {
                            $styleRow = $styleData;
                            $dataRow = $v;
                        }
        
                        $cells[] = WriterEntityFactory::createCell($dataRow, $styleRow);
                    }
        
                    $rowData = WriterEntityFactory::createRow($cells);
                    $writer->addRow($rowData);
                }

                $writer->close();

                $DataReturn['success'] = true;
                $DataReturn['count_data'] = sizeof($dataList);
                $DataReturn['filenya'] = base_url() . $filePath;
                $this->response($DataReturn, 200);
            } else {
                $DataReturn['success'] = true;
                $DataReturn['count_data'] = 0;
                $this->response($DataReturn, 200);            
            }            
        } else {
            $DataReturn['success'] = true;
            $DataReturn['count_data'] = 0;
            $this->response($DataReturn, 200);            
        }
    }

    public function data_review_excel_node_post(){
        $RepID  = $this->post("RepID");
        $IMSID  = $this->post("IMSID");

        //curl
        $url = $this->config->item('base_url_ct_util').'certification/ims/dataReview/excel/'.$IMSID.'/'.$RepID;
        $url = filter_var($url,FILTER_SANITIZE_URL);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $arrResult = json_decode($result, true);
        if( ($arrResult['success'] == true) && file_exists('files/export/'.$arrResult['filename']) ) {
            $this->response(array('success' => TRUE, 'filenya' => base_url() . 'files/export/'.$arrResult['filename']), 200);
        } else {
            $this->response(array('success' => FALSE, 'message' => lang('Export Excel Failed')), 400);
        }
    }

    public function view_main_list_get(){
        $RepID  = $this->get("RepID");
        $IMSID  = $this->get("IMSID");

        ini_set('memory_limit', '-1');

        $data = $this->mims->getReportToolsViewList((int) $RepID, $IMSID,$this->get('start'),$this->get('limit'),'limit');
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array(), 200);
        }
    }

    public function getReportTools_get(){
        $RepID  = $this->get("RepID");
        $IMSID  = $this->get("IMSID");

        ini_set('memory_limit', '-1');

        $data = $this->mims->getReportTools((int) $RepID, $IMSID);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array(), 200);
        }
    }

    public function cmb_report_tools_get(){
        $data = $this->mims->getCmbReportTools($this->get("type"));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array(), 200);
        }
    }

    public function data_get()
    {
        $data = $this->mims->readAllIMS($this->get('key'), $this->get('start'), $this->get('limit'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array(), 200);
        }

    }

    public function holders_get()
    {
        $data = $this->mims->listHolders();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any roles!'), 404);
        }
    }

    public function programs_get()
    {
        $data = $this->mims->listPrograms($this->get('SupplychainID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any roles!'), 404);
        }
    }

    public function cert_body_get()
    {
        $data = $this->mims->listCertBody();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any roles!'), 404);
        }
    }

    public function cert_body_contact_get()
    {
        $data = $this->mims->listCertBodyContact($this->get('CertBodyID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any roles!'), 404);
        }
    }

    public function set_holder_get()
    {
        $data = $this->mims->readCertificationHolderDetail($this->get('CertHolderID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(null, 200);
        }
    }

    public function set_body_get()
    {
        $data = $this->mims->readCertificationBodyContactDetail($this->get('CertBodyContactID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array(), 200);
        }

    }

    public function first_buyer_get()
    {
        $this->load->model('muserprofile');
        $data = $this->mims->listFirstBuyer($this->muserprofile->getUserProfile());
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any first buyer!'), 404);
        }
    }

    public function data_post()
    {
        // echo '<pre>'; print_r($this->post(null)); echo '</pre>'; exit;
        $CertDistrictID = '';
        if (!empty($this->post('CertDistrictID'))) {
            $CertDistrictID = implode(',', $this->post('CertDistrictID'));
        }
        $data = $this->mims->createIMS($this->post('CertHolderID'), $this->post('CertBodyID'), $this->post('CertBodyContactID'), $this->post('FirstBuyerID'), $this->post('SurveyNr'), $this->post('Year'), $this->post('CertificationStart'), $this->post('CertificationEnd'), $this->post('InternalStart'), $this->post('InternalEnd'), $this->post('ExternalDate'), $this->post('ExternalStart'), $this->post('ExternalEnd'), $this->post('ExtensionDate'), $this->post('ValidityStart'), $this->post('ValidityEnd'), $_SESSION['userid'], $this->post('CertEventName'), $CertDistrictID);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Data could not be found'), 404);
        }

    }

    public function detail_get()
    {
        $data = $this->mims->readIMSDetail($this->get('IMSID'));
        if ($data) {
            if (!empty($data['CertDistrictID'])) {
                $data['CertProvinceID'] = substr($data['CertDistrictID'], 0, 2);
                $data['CertDistrictID'] = explode(',', $data['CertDistrictID']);
            }
            $this->response($data, 200);
        } else {
            $this->response(array(), 200);
        }

    }

    public function data_put()
    {
        if ($this->put('IMSID') == '') {
            $this->response(null, 400);
        }

        $CertDistrictID = '';
        if (!empty($this->put('CertDistrictID'))) {
            $CertDistrictID = implode(',', $this->put('CertDistrictID'));
        }
        $data = $this->mims->updateIMS($this->put('CertHolderID'), $this->put('CertBodyID'), $this->put('CertBodyContactID'), $this->put('FirstBuyerID'), $this->put('SurveyNr'), $this->put('Year'), $this->put('CertificationStart'), $this->put('CertificationEnd'), $this->put('InternalStart'), $this->put('InternalEnd'), $this->put('ExternalDate'), $this->put('ExternalStart'), $this->put('ExternalEnd'), $this->put('ExtensionDate'), $this->put('ValidityStart'), $this->put('ValidityEnd'), $_SESSION['userid'], $this->put('IMSID'), $this->put('CertEventName'), $CertDistrictID);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Data could not be found'), 404);
        }

    }

    public function data_delete()
    {
        if (!$this->delete('IMSID')) {
            $this->response(null, 400);
        }

        $data = $this->mims->deleteIMS($_SESSION['userid'], $this->delete('IMSID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Data could not be found'), 404);
        }

    }

    public function farmers_get()
    {
        $sorting      = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $data = $this->mims->readFarmers($this->get('IMSID'), $this->get('key'), $this->get('SurveyNr'), $this->get('notcomplete'), $this->get('start'), $this->get('limit'), $sortingField, $sortingDir);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array(), 200);
        }

    }

    public function ims_detail_farmers_get()
    {
        $sorting      = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $data = $this->mims->readFarmersImsDetail($this->get('IMSID'), $this->get('key'), $this->get('start'), $this->get('limit'), $sortingField, $sortingDir);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array(), 200);
        }

    }

    public function ims_detail_candidate_get()
    {
        $sorting      = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $data = $this->mims->readCandidateImsDetail($this->get('IMSID'), $this->get('key'), $this->get('start'), $this->get('limit'), $sortingField, $sortingDir,'js_grid');
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array(), 200);
        }

    }

    public function ims_event_detail_summary_kpi_get()
    {
        $IMSID = (int) $this->get('IMSID');
        $data  = $this->mims->ImsEventDetailSummaryKpi($IMSID);
        $this->response($data, 200);
    }

    public function ims_event_detail_summary_fa_get()
    {
        $IMSID = (int) $this->get('IMSID');
        //$data = $this->mims->ImsEventDetailSummaryFa($IMSID);
        $data = $this->mims->ImsEventDetailSummaryFaTable($IMSID);
        $this->response($data, 200);
    }

    public function reupdate_fa_summary_post()
    {
        $IMSID = (int) $this->post('IMSID');

        $proses = $this->mims->ImsEventDetailSummaryFa($IMSID);
        if ($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function ims_event_detail_summary_afl_get()
    {
        $IMSID = (int) $this->get('IMSID');
        $data  = $this->mims->ImsEventDetailSummaryAfl($IMSID);
        $this->response($data, 200);
    }

    public function ims_farmer_candidate_delete()
    {
        $FarmerID = (int) $this->delete('FarmerID');
        $IMSID    = (int) $this->delete('IMSID');

        $proses = $this->mims->deleteFarmerCandidate($FarmerID, $IMSID);
        $this->response($proses, 200);
    }

    public function farmer_add_list_get()
    {
        $data = $this->mims->readFarmerAddList($this->get('IMSID'), $this->get('SurveyNr'), $this->get('DistrictID'), $this->get('SubDistrictID'), $this->get('VillageID'), $this->get('hectare'), $this->get('production'), $this->get('key'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find participants training!'), 404);
        }

    }

    public function surveys_get()
    {
        $data = $this->mims->listSurveys();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any roles!'), 404);
        }
    }

    public function province_get()
    {
        $data = $this->mims->listProvinces();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any roles!'), 404);
        }
    }

    public function district_get()
    {
        $data = $this->mims->listDistricts($this->get('ProvinceID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any roles!'), 404);
        }
    }

    public function subdistrict_get()
    {
        $data = $this->mims->listSubDistricts($this->get('ProvinceID'), $this->get('DistrictID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any roles!'), 404);
        }
    }

    public function village_get()
    {
        $data = $this->mims->listVillages($this->get('SubDistrictID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any roles!'), 404);
        }
    }

    public function farmer_add_post()
    {
        $data = $this->mims->addFarmers($this->post('IMSID'), $this->post('SurveyNr'), $this->post('farmers'), $_SESSION['userid']);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Data could not be found'), 404);
        }

    }

    public function farmer_add_delete()
    {
        if (!$this->delete('IMSID')) {
            $this->response(null, 400);
        }

        $data = $this->mims->deleteFarmer($this->delete('IMSID'), $this->delete('FarmerID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Data could not be found'), 404);
        }

    }

    public function summary_get()
    {
        $data = $this->mims->readSummary($this->get('IMSID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array(), 200);
        }

    }

    public function files_get()
    {
        $data = $this->mims->readFiles($this->get('IMSID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array(), 200);
        }

    }

    public function staffs_get()
    {
        $sorting      = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $data = $this->mims->readStaffs($this->get('IMSID'), $this->get('key'), $this->get('start'), $this->get('limit'), $sortingField, $sortingDir);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array(), 200);
        }

    }

    public function buying_units_get()
    {
        $data = $this->mims->readBuyingUnits($this->get('IMSID'), $this->get('key'), $this->get('start'), $this->get('limit'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array(), 200);
        }

    }

    public function staff_add_list_get()
    {
        $data = $this->mims->readStaffAddList($this->get('IMSID'), $this->get('ProvinceID'), $this->get('WorkAreaID'), $this->get('key'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find participants training!'), 404);
        }

    }

    public function staff_master_add_list_get()
    {
        $data = $this->mims->readStaffMasterAddList($this->get('IMSMasterID'), $this->get('ProvinceID'), $this->get('WorkAreaID'), $this->get('key'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find participants training!'), 404);
        }

    }

    public function staff_province_get()
    {
        $data = $this->mims->listStaffProvinces();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any roles!'), 404);
        }
    }

    public function work_area_get()
    {
        $data = $this->mims->listWorkArea($this->get('ProvinceID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any roles!'), 404);
        }
    }

    public function staff_add_post()
    {
        $data = $this->mims->addStaffs($this->post('IMSID'), $this->post('staffs'), $_SESSION['userid']);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Data could not be found'), 404);
        }

    }

    public function staff_add_delete()
    {
        if (!$this->delete('IMSStaffID')) {
            $this->response(null, 400);
        }

        $data = $this->mims->deleteStaff($this->delete('IMSStaffID'), $_SESSION['userid']);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Data could not be found'), 404);
        }

    }

    public function staff_ims_event_add_post()
    {
        $data = $this->mims->addStaffsImsEvent($this->post('IMSMasterID'), $this->post('IMSID'), $this->post('staffs'));
        $this->response($data, 200);
    }

    public function staff_ims_event_delete()
    {
        $IMSStaffID  = (int) $this->delete('IMSStaffID');
        $IMSMasterID = (int) $this->delete('IMSMasterID');
        $StaffID     = (int) $this->delete('StaffID');
        $IMSID       = (int) $this->delete('IMSID');
        $data        = $this->mims->deleteStaffsImsEvent($IMSStaffID, $IMSMasterID, $StaffID, $IMSID);
        $this->response($data, 200);
    }

    public function staff_master_add_post()
    {
        $data = $this->mims->addStaffsMaster($this->post('IMSMasterID'), $this->post('staffs'), $_SESSION['userid']);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Data could not be found'), 404);
        }

    }

    public function ims_master_staff_delete()
    {
        $StaffID     = (int) $this->delete('StaffID');
        $IMSMasterID = (int) $this->delete('IMSMasterID');
        $proses      = $this->mims->deleteStaffMaster($IMSMasterID, $StaffID);
        $this->response($proses, 200);
    }

    public function afls_get()
    {
        $sorting      = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $data = $this->mims->readAFLs($this->get('IMSID'), $this->get('key'),$this->get('StatusAudit'),$this->get('StatusVerified'), $this->get('start'), $this->get('limit'), $sortingField, $sortingDir);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array(), 200);
        }

    }

    public function ics_reinspect_get(){
        $sorting      = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $data = $this->mims->readIcsReinspect($this->get('IMSID'), $this->get('key'),$this->get('start'), $this->get('limit'), $sortingField, $sortingDir);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array(), 200);
        }
    }

    public function afl_final_get()
    {
        $sorting      = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';
        $StatusIcsReinspect = $this->get('StatusIcsReinspect');

        if ($this->get('ICSStatus') == "2") {
            $this->response(array(), 200);
        }

        if($StatusIcsReinspect == "1"){
            $this->response(array(), 200);
        }

        $data = $this->mims->readAFLFinal($this->get('IMSID'), $this->get('key'), $this->get('start'), $this->get('limit'), $sortingField, $sortingDir);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response($data, 200);
        }

    }

    public function import_farmer_mapping_fa_post()
    {
        if ($this->file['FileImport']['name'] != "") {
            //Cek extensi
            $arrTemp = explode(".", $this->file['FileImport']['name']);
            $extNya  = array_values(array_slice($arrTemp, -1))[0];
            $extNya  = strtolower($extNya);
            $IMSID   = $this->post('IMSID');

            if ($extNya == 'xlsx') {
                //Read Filenya
                $reader = ReaderEntityFactory::createXLSXReader();
                $reader->setShouldFormatDates(true);
                $reader->open($this->file['FileImport']['tmp_name']);

                $dataExcel = array();
                $dataField = array();
                $increInt  = 0;
                foreach ($reader->getSheetIterator() as $sheet) {
                    foreach ($sheet->getRowIterator() as $row) {
                        if ($increInt == 0) {
                            $dataField = $row;
                        } else {
                            $cells = $row->toArray();
                            $dataExcel[$increInt - 1] = $cells;
                        }
                        $increInt++;
                    }
                }
                $reader->close();

                /*
                [0] => FarmerID
                [1] => UserID
                [2] => StaffID
                 */
                $isAllFarmerRegistered      = true;
                $isAllFarmerRegisteredData  = array();
                $isAllUserIDRegistered      = true;
                $isAllUserIDRegisteredData  = array();
                $isDuplicate                = true;
                $DataDuplicate              = array();
                $isFarmerMoreThanOneFa      = true;
                $isFarmerMoreThanOneFaData  = array();

                $usernames = [];
                for ($i = 0; $i < count($dataExcel); $i++) {
                    //Cek FarmerID
                    $cekProcessFarmer = $this->mims->cekExistFarmerIMS($IMSID, $dataExcel[$i][0]);
                    if ($cekProcessFarmer == false) {
                        $isAllFarmerRegistered       = false;
                        $isAllFarmerRegisteredData[] = $dataExcel[$i][0];
                    }

                    //Cek UserID
                    if (!in_array($dataExcel[$i][1], $usernames)) {
                        $usernames[] = $dataExcel[$i][1];
                        $cekProcessUser = $this->mims->cekExistUserByUsername($dataExcel[$i][1]);
                        if ($dataExcel[$i][1] != '0'){
                            if ($cekProcessUser == false) {
                                $isAllUserIDRegistered       = false;
                                $isAllUserIDRegisteredData[] = $dataExcel[$i][1];
                            }
                        }
                    }

                    $DataDuplicate[]             = $dataExcel[$i][0] . '_' . $dataExcel[$i][1];
                    $isFarmerMoreThanOneFaData[] = $dataExcel[$i][0];
                }

                //Cek Duplikat
                if (count(array_unique($DataDuplicate)) < count($DataDuplicate)) {
                    $isDuplicate = false;
                }

                //Cek isFarmerMoreThanOneFa
                if (count(array_unique($isFarmerMoreThanOneFaData)) < count($isFarmerMoreThanOneFaData)) {
                    $isFarmerMoreThanOneFa = false;
                }

                if (
                    $isAllFarmerRegistered == true &&
                    $isAllUserIDRegistered == true &&
                    $isDuplicate == true &&
                    $isFarmerMoreThanOneFa == true
                ) {

                    //PROSES
                    $proses = $this->mims->importFarmerMappingFA($IMSID, $dataExcel);
                    $this->response($proses, 200);

                } else {
                    //Tidak lolos validasi
                    $psnErrorArr = array();

                    if ($isAllFarmerRegistered == false) {
                        $FarmerNotRegistered = implode(", ", $isAllFarmerRegisteredData);
                        $psnErrorArr[]       = "Farmer " . $FarmerNotRegistered . " are not register to this IMS";
                    }

                    if ($isAllUserIDRegistered == false) {
                        $UserNotRegistered = implode(", ", $isAllUserIDRegisteredData);
                        $psnErrorArr[]     = "User " . $UserNotRegistered . " are not registered";
                    }

                    if ($isDuplicate == false) {
                        $psnErrorArr[] = "There are duplicate entry";
                    } else {
                        if ($isFarmerMoreThanOneFa == false) {
                            $psnErrorArr[] = "There are Farmer that register to more than one FA";
                        }
                    }

                    $results['success'] = false;
                    $psnError           = implode('<br><br>', $psnErrorArr);
                    $results['message'] = $psnError;
                    $this->response($results, 400);
                }

            } else {
                $results['success'] = false;
                $results['message'] = 'Invalid File!';
                $this->response($results, 400);
            }
        }
    }

    public function ims_import_farmer_get($IMSID){
        $IMSID = (int) $IMSID;
        ini_set('memory_limit', '-1');
        
        require_once 'application/third_party/PHPExcel18/PHPExcel.php';
        require_once 'application/third_party/PHPExcel18/PHPExcel/IOFactory.php';

        //=============== MULAI TULIS EXCEL (BEGIN) ===================================================================//

        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("PT Koltiva")
            ->setLastModifiedBy("PT Koltiva")
            ->setTitle("Templte for Candidate Mapping for FA - IMS")
            ->setSubject("Templte for Candidate Mapping for FA - IMS")
            ->setDescription("Templte for Candidate Mapping for FA - IMS")
            ->setKeywords("Templte for Candidate Mapping for FA - IMS")
            ->setCategory("Templte for Candidate Mapping for FA - IMS");

        //set style  (begin)
        $styleFont = array(
            'font'      => array(
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

        $styleFontBoldMainTitle = array(
            'font'      => array(
                'name' => 'Arial',
                'size' => '11',
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
        );

        $styleFontBoldTitle = array(
            'font'      => array(
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
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '8DB4E3'),
            ),
        );
        $styleFontBoldBgRedCenter = array(
            'font'      => array(
                'name' => 'Arial',
                'size' => '9',
                'bold' => true,
            ),
            'fill'      => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'C0504D'),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $styleBorderFull = array(
            'borders' => array(
                'left'   => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'right'  => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'top'    => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );
        //set style  (end)

        //create sheet
        $objWorkSheetPetani = $objPHPExcel->createSheet(0);
        $objWorkSheetPetani->setTitle('Data');

        //set width column
        $objWorkSheetPetani->getColumnDimension('A')->setWidth(14);
        $objWorkSheetPetani->getColumnDimension('B')->setWidth(14);
        $objWorkSheetPetani->getColumnDimension('C')->setWidth(14);
        $objWorkSheetPetani->getColumnDimension('D')->setWidth(14);
        $objWorkSheetPetani->getColumnDimension('E')->setWidth(14);

        //tabel header
        $objWorkSheetPetani->setCellValue('A1', 'FarmerID/MemberDisplayID');
        $objWorkSheetPetani->getStyle('A1')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetPetani->getStyle('A1')->applyFromArray($styleBorderFull, false);

        $objWorkSheetPetani->setCellValue('A2', '');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . date('YmdHis') . '_farmer_mapping_fa_ims_' . $IMSID . '.xlsx');
        header('Cache-Control: max-age=0');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function ims_farmer_mapping_get($IMSID)
    {
        $IMSID = (int) $IMSID;
        ini_set('memory_limit', '-1');

        //data yg diperlukan (begin)
        $dataMapping = $this->mims->GridMappingFAFarmer($IMSID, null, null, null, null, null, 'no_limit');
        //data yg diperlukan (end)

        require_once 'application/third_party/PHPExcel18/PHPExcel.php';
        require_once 'application/third_party/PHPExcel18/PHPExcel/IOFactory.php';

        //=============== MULAI TULIS EXCEL (BEGIN) ===================================================================//

        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("PT Koltiva")
            ->setLastModifiedBy("PT Koltiva")
            ->setTitle("Templte for Candidate Mapping for FA - IMS")
            ->setSubject("Templte for Candidate Mapping for FA - IMS")
            ->setDescription("Templte for Candidate Mapping for FA - IMS")
            ->setKeywords("Templte for Candidate Mapping for FA - IMS")
            ->setCategory("Templte for Candidate Mapping for FA - IMS");

        //set style  (begin)
        $styleFont = array(
            'font'      => array(
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

        $styleFontBoldMainTitle = array(
            'font'      => array(
                'name' => 'Arial',
                'size' => '11',
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
        );

        $styleFontBoldTitle = array(
            'font'      => array(
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
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '8DB4E3'),
            ),
        );
        $styleFontBoldBgRedCenter = array(
            'font'      => array(
                'name' => 'Arial',
                'size' => '9',
                'bold' => true,
            ),
            'fill'      => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'C0504D'),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $styleBorderFull = array(
            'borders' => array(
                'left'   => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'right'  => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'top'    => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );
        //set style  (end)

        //create sheet
        $objWorkSheetPetani = $objPHPExcel->createSheet(0);
        $objWorkSheetPetani->setTitle('Data');

        //set width column
        $objWorkSheetPetani->getColumnDimension('A')->setWidth(14);
        $objWorkSheetPetani->getColumnDimension('B')->setWidth(14);
        $objWorkSheetPetani->getColumnDimension('C')->setWidth(14);
        $objWorkSheetPetani->getColumnDimension('D')->setWidth(14);
        $objWorkSheetPetani->getColumnDimension('E')->setWidth(14);

        //tabel header
        $objWorkSheetPetani->setCellValue('A1', 'FarmerID');
        $objWorkSheetPetani->setCellValue('B1', lang('UserName'));
        $objWorkSheetPetani->getStyle('A1:B1')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetPetani->getStyle('A1:B1')->applyFromArray($styleBorderFull, false);

        $rowStart = 2;
        $incre    = 0;
        foreach ($dataMapping as $val) {
            $val['no'] = $incre + 1;

            $objWorkSheetPetani->setCellValue('A' . $rowStart, $val['FarmerID']);
            $objWorkSheetPetani->setCellValue('B' . $rowStart, $val['UserName']);

            $objWorkSheetPetani->getStyle('A' . $rowStart . ':' . 'B' . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetPetani->getStyle('A' . $rowStart . ':' . 'B' . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . date('YmdHis') . '_farmer_mapping_fa_ims_' . $IMSID . '.xlsx');
        header('Cache-Control: max-age=0');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function grid_mapping_fa_farmer_get()
    {
        $IMSID  = (int) $this->get('IMSID');
        $UserId = $this->get('UserId');

        $sorting      = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $data = $this->mims->GridMappingFAFarmer($IMSID, $UserId, $this->get('start'), $this->get('limit'), $sortingField, $sortingDir, 'limit');
        $this->response($data, 200);
    }

    public function grid_ims_coaching_get() {
        $sorting      = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';
        $IMSID = (int) $this->get('IMSID');

        $data = $this->mims->readCoaching($IMSID, $this->get('textSearch'), $this->get('start'), $this->get('limit'), $sortingField, $sortingDir);
        //echo '<pre>'; print_r($data); exit;

        foreach ($data['data'] as $k => $v) {
            $DataMajor = $this->mims->getCountNc($IMSID, $v['FarmerID'],'High');
            $data['data'][$k]['NCMajor'] = $DataMajor['CountNc'];
            $data['data'][$k]['NCMajorAct'] = $DataMajor['CountNcAct'];

            $DataMinor = $this->mims->getCountNc($IMSID, $v['FarmerID'],'Medium');
            $data['data'][$k]['NCMinor'] = $DataMinor['CountNc'];
            $data['data'][$k]['NCMinorAct'] = $DataMinor['CountNcAct'];
        }

        $this->response($data, 200);
    }

    public function cfls_get()
    {
        $sorting      = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $data = $this->mims->readCFLs($this->get('IMSID'), $this->get('key'), $this->get('start'), $this->get('limit'), $sortingField, $sortingDir);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array(), 200);
        }

    }

    public function generate_afl_post()
    {
        $data = $this->mims->GenerateAFL($this->post('IMSID'), $_SESSION['userid']);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Data could not be found'), 404);
        }

    }

    public function print_afl_get($IMSID)
    {
        set_time_limit(0);
        ini_set('memory_limit', '2500M');
        $ims     = $this->mims->getIMSDetail($IMSID);
        $details = $this->mims->getAFLs($IMSID);
        //echo "<pre>".print_r($details,1);exit;
        $this->load->library('Excel', null, 'PHPExcel');
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';
        $object = new PHPExcel();

        // Set properties
        $object->getProperties()->setCreator("Koltiva Cocoatrace")
            ->setLastModifiedBy("Koltiva Cocoatrace")
            ->setCategory("Koltiva Cocoatrace");
        // Add some data

        $style_center = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $style_border = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );
        if ($ims['Year'] != '0') {
            $year = " - " . $ims['Year'];
        } else {
            $year = "";
        }
        $title   = "Data AFL - " . $ims['CertProgName'] . " - " . $ims['Name'] . " - SurveyNr " . $ims['SurveyNr'] . $year;
        $counter = 6;
        $object->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('B')->setWidth(50);
        $object->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('E')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('F')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('G')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('H')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('I')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('J')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('K')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('L')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('M')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('N')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('O')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('P')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('Q')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('R')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('S')->setWidth(25);
        $object->getActiveSheet()->mergeCells('A1:S1');
        $object->getActiveSheet()->mergeCells('A2:S2');
        $object->getActiveSheet()->mergeCells('A5:S5');
        $object->getActiveSheet()->getStyle("A1:S4")->applyFromArray($style_center);
        $object->getActiveSheet()->getStyle("A4:S4")->applyFromArray($style_border);
        $object->getActiveSheet()->getStyle("A5")->applyFromArray($style_border);
        $object->getActiveSheet()->getStyle("A1:S4")->getFont()->setBold(true);
        $object->getActiveSheet()->getStyle("A5")->getFont()->setBold(true);
        $object->setActiveSheetIndex(0)->setCellValue('A1', $title);
        $object->setActiveSheetIndex(0)->setCellValue('A4', 'Farmer ID');
        $object->setActiveSheetIndex(0)->setCellValue('B4', 'Farmer Name');
        $object->setActiveSheetIndex(0)->setCellValue('C4', 'Gender');
        $object->setActiveSheetIndex(0)->setCellValue('D4', 'Phone Number');
        $object->setActiveSheetIndex(0)->setCellValue('E4', 'Farm Operator Name');
        $object->setActiveSheetIndex(0)->setCellValue('F4', 'Location');
        $object->setActiveSheetIndex(0)->setCellValue('G4', 'Governmental Farm ID');
        $object->setActiveSheetIndex(0)->setCellValue('H4', 'Certified Status');
        $object->setActiveSheetIndex(0)->setCellValue('I4', '1st Year of Certification');
        $object->setActiveSheetIndex(0)->setCellValue('J4', 'No of permanent workers on the crop');
        $object->setActiveSheetIndex(0)->setCellValue('K4', 'Internal Inspection date');
        $object->setActiveSheetIndex(0)->setCellValue('L4', 'Other certification schemes');
        $object->setActiveSheetIndex(0)->setCellValue('M4', 'Estimated harvest present year (Kg)');
        $object->setActiveSheetIndex(0)->setCellValue('N4', "Previous year's harvest (Kg)");
        $object->setActiveSheetIndex(0)->setCellValue('O4', "Total certified crop area (ha)");
        $object->setActiveSheetIndex(0)->setCellValue('P4', "No of plots");
        $object->setActiveSheetIndex(0)->setCellValue('Q4', "Total farm area (ha)");
        $object->setActiveSheetIndex(0)->setCellValue('R4', "Previous year delivery (Kg)");
        $object->setActiveSheetIndex(0)->setCellValue('S4', "2 year ago delivery (Kg)");
        foreach ($details as $key => $val) {
            //$object->getActiveSheet()->getStyle("B$counter:E$counter")->getNumberFormat()->setFormatCode('#,##0.00');
            $object->getActiveSheet()->getStyle("A$counter:S$counter")->applyFromArray($style_border);
            $object->getActiveSheet()->setCellValue('A' . $counter, $val['FarmerID']);
            $object->getActiveSheet()->setCellValue('B' . $counter, $val['FarmerName']);
            $object->getActiveSheet()->setCellValue('C' . $counter, $val['Gender']);
            $object->getActiveSheet()->setCellValue('D' . $counter, $val['HandPhone']);
            $object->getActiveSheet()->setCellValue('E' . $counter, "");
            $object->getActiveSheet()->setCellValue('F' . $counter, $val['Village']);
            $object->getActiveSheet()->setCellValue('G' . $counter, "");
            $object->getActiveSheet()->setCellValue('H' . $counter, "");
            $object->getActiveSheet()->setCellValue('I' . $counter, $val['CertFirstYear']);
            $object->getActiveSheet()->setCellValue('J' . $counter, $val['PermanentWorkers']);
            $object->getActiveSheet()->setCellValue('K' . $counter, $val['ICSDate']);
            $object->getActiveSheet()->setCellValue('L' . $counter, "");
            $object->getActiveSheet()->setCellValue('M' . $counter, $val['CertHarvest']);
            $object->getActiveSheet()->setCellValue('N' . $counter, $val['Harvest']);
            $object->getActiveSheet()->setCellValue('O' . $counter, $val['CertHectare']);
            $object->getActiveSheet()->setCellValue('P' . $counter, $val['CertFarmNr']);
            $object->getActiveSheet()->setCellValue('Q' . $counter, $val['other']);
            $object->getActiveSheet()->setCellValue('R' . $counter, $val['1sales']);
            $object->getActiveSheet()->setCellValue('S' . $counter, $val['2sales']);

            /*$object->getActiveSheet()->setCellValue('B'.$counter, $details['data'][$key]['po']);
            $object->getActiveSheet()->setCellValue('C'.$counter, $details['data'][$key]['batchnumber']);
            $object->getActiveSheet()->setCellValue('D'.$counter, number_format($details['data'][$key]['bruto'],2,'.',''));
            $object->getActiveSheet()->setCellValue('E'.$counter, number_format($details['data'][$key]['netto'],2,'.',''));
             */
            $counter++;
        }
        $konter = $counter;
        $konter++;

        /*$object->getActiveSheet()->setCellValue('A'.$konter, "Total");
        $object->getActiveSheet()->getStyle("B$konter:E$konter")->getNumberFormat()->setFormatCode('#,##0.00');
        $object->getActiveSheet()->setCellValue('D'.$konter, "=SUM(D5:D$counter)");
        $object->getActiveSheet()->setCellValue('E'.$konter, "=SUM(E5:E$counter)");
         */
        $object->setActiveSheetIndex(0);
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $title . '.xlsx');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function print_garden_check_get($IMSID)
    {
        set_time_limit(0);
        ini_set('memory_limit', '2500M');
        $ims     = $this->mims->getIMSDetail($IMSID);
        $details = $this->mims->getGardenCheck($IMSID);
        $this->load->library('Excel', null, 'PHPExcel');
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';
        $object = new PHPExcel();

        // Set properties
        $object->getProperties()->setCreator("Koltiva Cocoatrace")
            ->setLastModifiedBy("Koltiva Cocoatrace")
            ->setCategory("Koltiva Cocoatrace");
        // Add some data

        $style_center = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $style_border = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );
        if ($ims['Year'] != '0') {
            $year = " - " . $ims['Year'];
        } else {
            $year = "";
        }
        $title   = "Data Farmer Garden - " . $ims['CertProgName'] . " - " . $ims['Name'] . " - SurveyNr " . $ims['SurveyNr'] . $year;
        $counter = 6;
        $object->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('B')->setWidth(50);
        $object->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('E')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('F')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('G')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('H')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('I')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('J')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('K')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('L')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('M')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('N')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('O')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('P')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('Q')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('R')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('S')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('T')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('U')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('V')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('W')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('X')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('Y')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('Z')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('AA')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('AB')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('AC')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('AD')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('AE')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('AF')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('AG')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('AH')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('AI')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('AJ')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('AK')->setWidth(25);
        $object->getActiveSheet()->mergeCells('A1:AK1');
        $object->getActiveSheet()->mergeCells('A2:AK2');
        $object->getActiveSheet()->mergeCells('A5:AK5');
        $object->getActiveSheet()->getStyle("A1:AK4")->applyFromArray($style_center);
        $object->getActiveSheet()->getStyle("A4:AK4")->applyFromArray($style_border);
        $object->getActiveSheet()->getStyle("A5")->applyFromArray($style_border);
        $object->getActiveSheet()->getStyle("A1:AK4")->getFont()->setBold(true);
        $object->getActiveSheet()->getStyle("A5")->getFont()->setBold(true);
        $object->setActiveSheetIndex(0)->setCellValue('A1', $title);
        $object->setActiveSheetIndex(0)->setCellValue('A4', 'Farmer ID');
        $object->setActiveSheetIndex(0)->setCellValue('B4', 'Farmer Name');
        $object->setActiveSheetIndex(0)->setCellValue('C4', 'Group ID');
        $object->setActiveSheetIndex(0)->setCellValue('D4', 'GroupName');
        $object->setActiveSheetIndex(0)->setCellValue('E4', 'Gender');
        $object->setActiveSheetIndex(0)->setCellValue('F4', 'Province');
        $object->setActiveSheetIndex(0)->setCellValue('G4', 'District');
        $object->setActiveSheetIndex(0)->setCellValue('H4', 'Sub District');
        $object->setActiveSheetIndex(0)->setCellValue('I4', 'Village');
        $object->setActiveSheetIndex(0)->setCellValue('J4', 'Garden Number');
        $object->setActiveSheetIndex(0)->setCellValue('K4', 'Survey Number');
        $object->setActiveSheetIndex(0)->setCellValue('L4', 'Latitude');
        $object->setActiveSheetIndex(0)->setCellValue('M4', 'Longitude');
        $object->setActiveSheetIndex(0)->setCellValue('N4', "Polygon Status");
        $object->setActiveSheetIndex(0)->setCellValue('O4', "Production");
        $object->setActiveSheetIndex(0)->setCellValue('P4', "Production Next");
        $object->setActiveSheetIndex(0)->setCellValue('Q4', "Garden Area (ha)");
        $object->setActiveSheetIndex(0)->setCellValue('R4', "Pohon TM");
        $object->setActiveSheetIndex(0)->setCellValue('S4', "Pohon TBM");
        $object->setActiveSheetIndex(0)->setCellValue('T4', "Pohon Rehab");
        $object->setActiveSheetIndex(0)->setCellValue('U4', "Productivity");
        $object->setActiveSheetIndex(0)->setCellValue('V4', "Tree Productivity");
        $object->setActiveSheetIndex(0)->setCellValue('W4', "Certification");
        $object->setActiveSheetIndex(0)->setCellValue('X4', "Certification Type");
        $object->setActiveSheetIndex(0)->setCellValue('Y4', "Year");
        $object->setActiveSheetIndex(0)->setCellValue('Z4', "Certification Holder Type");
        $object->setActiveSheetIndex(0)->setCellValue('AA4', "Certification Holder");
        $object->setActiveSheetIndex(0)->setCellValue('AB4', "Candidate Selection");
        $object->setActiveSheetIndex(0)->setCellValue('AC4', "ICSDate");
        $object->setActiveSheetIndex(0)->setCellValue('AD4', "Comment Audit");
        $object->setActiveSheetIndex(0)->setCellValue('AE4', "Date Revision Audit");
        $object->setActiveSheetIndex(0)->setCellValue('AF4', "Recommendation Audit");
        $object->setActiveSheetIndex(0)->setCellValue('AG4', "Internal Audit Status");
        $object->setActiveSheetIndex(0)->setCellValue('AH4', "External Date");
        $object->setActiveSheetIndex(0)->setCellValue('AI4', "Certification Start");
        $object->setActiveSheetIndex(0)->setCellValue('AJ4', "Certification End");
        $object->setActiveSheetIndex(0)->setCellValue('AK4', "Certification Extension");
        foreach ($details as $key => $val) {
            //$object->getActiveSheet()->getStyle("B$counter:E$counter")->getNumberFormat()->setFormatCode('#,##0.00');
            $object->getActiveSheet()->getStyle("A$counter:AK$counter")->applyFromArray($style_border);
            $object->getActiveSheet()->setCellValue('A' . $counter, $val['FarmerID']);
            $object->getActiveSheet()->setCellValue('B' . $counter, $val['FarmerName']);
            $object->getActiveSheet()->setCellValue('C' . $counter, $val['CPGid']);
            $object->getActiveSheet()->setCellValue('D' . $counter, $val['GroupName']);
            $object->getActiveSheet()->setCellValue('E' . $counter, $val['Gender']);
            $object->getActiveSheet()->setCellValue('F' . $counter, $val['Province']);
            $object->getActiveSheet()->setCellValue('G' . $counter, $val['District']);
            $object->getActiveSheet()->setCellValue('H' . $counter, $val['SubDistrict']);
            $object->getActiveSheet()->setCellValue('I' . $counter, $val['Village']);
            $object->getActiveSheet()->setCellValue('J' . $counter, $val['CertGardenNr']);
            $object->getActiveSheet()->setCellValue('K' . $counter, $val['CertSurveyNr']);
            $object->getActiveSheet()->setCellValue('L' . $counter, $val['Latitude']);
            $object->getActiveSheet()->setCellValue('M' . $counter, $val['Longitude']);
            $object->getActiveSheet()->setCellValue('N' . $counter, $val['PolygonStatus']);
            $object->getActiveSheet()->setCellValue('O' . $counter, $val['CertHarvest']);
            $object->getActiveSheet()->setCellValue('P' . $counter, $val['ProductionNext']);
            $object->getActiveSheet()->setCellValue('Q' . $counter, $val['CertHectare']);
            $object->getActiveSheet()->setCellValue('R' . $counter, $val['CertPohonTM']);
            $object->getActiveSheet()->setCellValue('S' . $counter, $val['CertPohonTBM']);
            $object->getActiveSheet()->setCellValue('T' . $counter, $val['CertPohonTR']);
            $object->getActiveSheet()->setCellValue('U' . $counter, $val['Productivity']);
            $object->getActiveSheet()->setCellValue('V' . $counter, $val['TreeProductivity']);
            $object->getActiveSheet()->setCellValue('W' . $counter, $val['Certification']);
            $object->getActiveSheet()->setCellValue('X' . $counter, $val['CertProgName']);
            $object->getActiveSheet()->setCellValue('Y' . $counter, $val['CertYear']);
            $object->getActiveSheet()->setCellValue('Z' . $counter, $val['OrgType']);
            $object->getActiveSheet()->setCellValue('AA' . $counter, $val['Name']);
            $object->getActiveSheet()->setCellValue('AB' . $counter, $val['CertCandidateSelection']);
            $object->getActiveSheet()->setCellValue('AC' . $counter, $val['CertICSDate']);
            $object->getActiveSheet()->setCellValue('AD' . $counter, $val['CertCommentAudit']);
            $object->getActiveSheet()->setCellValue('AE' . $counter, $val['CertDateRevisionAudit']);
            $object->getActiveSheet()->setCellValue('AF' . $counter, $val['CertRecommendationAudit']);
            $object->getActiveSheet()->setCellValue('AG' . $counter, $val['StatusAudit']);
            $object->getActiveSheet()->setCellValue('AH' . $counter, $val['ExternalDate']);
            $object->getActiveSheet()->setCellValue('AI' . $counter, $val['CertStart']);
            $object->getActiveSheet()->setCellValue('AJ' . $counter, $val['CertEnd']);
            $object->getActiveSheet()->setCellValue('AK' . $counter, $val['ExtensionDate']);

            /*$object->getActiveSheet()->setCellValue('B'.$counter, $details['data'][$key]['po']);
            $object->getActiveSheet()->setCellValue('C'.$counter, $details['data'][$key]['batchnumber']);
            $object->getActiveSheet()->setCellValue('D'.$counter, number_format($details['data'][$key]['bruto'],2,'.',''));
            $object->getActiveSheet()->setCellValue('E'.$counter, number_format($details['data'][$key]['netto'],2,'.',''));
             */
            $counter++;
        }
        $konter = $counter;
        $konter++;

        /*$object->getActiveSheet()->setCellValue('A'.$konter, "Total");
        $object->getActiveSheet()->getStyle("B$konter:E$konter")->getNumberFormat()->setFormatCode('#,##0.00');
        $object->getActiveSheet()->setCellValue('D'.$konter, "=SUM(D5:D$counter)");
        $object->getActiveSheet()->setCellValue('E'.$konter, "=SUM(E5:E$counter)");
         */
        $object->setActiveSheetIndex(0);
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $title . '.xlsx');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function import($inputFileName, $cell_exist_only = true)
    {
        /*
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';
        $data = array();
        $inputFileType  = PHPExcel_IOFactory::identify($inputFileName);
        $objReader      = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel    = $objReader->load($inputFileName);
        $a = 0;
        foreach($objPHPExcel->getWorksheetIterator() as $worksheet){
        $data[$a];
        foreach ($worksheet->getRowIterator() as $row) {
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells($cell_exist_only);
        foreach ($cellIterator as $cell) {
        if (!is_null($cell)) {
        $row_data[] = $cell->getFormattedValue();
        // $row_data[] = $cell->getCalculatedValue();
        }
        }
        if (!empty($row_data)) {
        $data[$a][] = $row_data;
        unset($row_data);
        }
        }
        $a++;
        }
         */

        //Ganti pakai library Spout
//        $reader = ReaderFactory::create(Type::XLSX); // for XLSX files
        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->setShouldFormatDates(true);
        $reader->open($inputFileName);

        $increInt = 0;
        $data     = array();
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
//                $cells = $row->getCells();
                $cells = $row->toArray();
                $data[0][$increInt] = $cells;
                $increInt++;
            }
        }

        return $data;
    }

    public function import_farmer_post()
    {
        //set memory
        ini_set('memory_limit', '-1');

        $name = $this->file['file']['name'];
        if (substr($name, strlen($name) - 4, 4) == 'xlsx') {
            $eData               = $this->import($_FILES['file']['tmp_name'], false);
            $import              = $this->mims->ImportFarmer($eData, $this->post('IMSID'));
            $results['infos']    = lang("Success");
            $results['status']   = "true";
            $results['message']  = lang("Import success");
            $results['berhasil'] = $import['berhasil'];
            $results['gagal']    = $import['gagal'];
        } else {
            $results['infos']   = lang("Error");
            $results['status']  = "false";
            $results['message'] = lang("Invalid file type");
        }
        $this->response($results, 200);
    }

    public function farmer_garden_get()
    {
        $data = $this->mims->readFarmerGardens($this->get('FarmerID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array(), 200);
        }

    }

    public function ims_farmer_garden_get()
    {
        $FarmerID = (int) $this->get('FarmerID');
        $IMSID    = (int) $this->get('IMSID');

        $data = $this->mims->getImsFarmerGarden($FarmerID, $IMSID);
        $this->response($data, 200);
    }

    public function farmer_cert_get()
    {
        $data = $this->mims->readFarmerCertifications($this->get('FarmerID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array(), 200);
        }

    }

    public function ims_farmer_cert_get()
    {
        $FarmerID = (int) $this->get('FarmerID');
        $IMSID    = (int) $this->get('IMSID');

        $data = $this->mims->getImsFarmerCertifications($FarmerID, $IMSID);
        $this->response($data, 200);
    }

    public function farmer_audit_get()
    {
        $data = $this->mims->readFarmerAudits($this->get('FarmerID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array(), 200);
        }

    }

    public function ims_farmer_audit_get()
    {
        $FarmerID = (int) $this->get('FarmerID');
        $IMSID    = (int) $this->get('IMSID');

        $data = $this->mims->getImsFarmerAudits($FarmerID, $IMSID);
        $this->response($data, 200);
    }

    //--Garden--//
    public function garden_detail_get()
    {
        $data = $this->mims->readGardenDetail($this->get('FarmerID'), $this->get('SurveyNr'), $this->get('GardenNr'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array(), 200);
        }

    }

    public function farmer_edit_put()
    {
        $data = $this->mims->updateFarmer($this->put('EditType'), $this->put('FarmerID'), $this->put('DefaultSurveyNr'), $this->put('SurveyNr'), $this->put('DefaultGardenNr'), $this->put('GardenNr'), $this->put('DefaultCertification'), $this->put('Certification'), $_SESSION['userid'], $this->put('GardenHaUncertified'), $this->put('PohonTM'), $this->put('PohonTBM'), $this->put('PohonRehab'), $this->put('EstimatedProduction'), $this->put('PanenTrekMonths'), $this->put('PanenBiasaMonths'), $this->put('PanenRayaMonths'), $this->put('PanenTrekPanenMonth'), $this->put('PanenBiasaPanenMonth'), $this->put('PanenRayaPanenMonth'), $this->put('PanenTrekKg'), $this->put('PanenBiasaKg'), $this->put('PanenRayaKg'), $this->put('DefaultICSDate'), $this->put('ICSDate'), $this->put('StatusAudit'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Data could not be found'), 404);
        }

    }

    public function duplicate_garden_put()
    {
        if (!$this->put('FarmerID')) {
            $this->response(null, 400);
        }

        $data = $this->mims->duplicateGarden($_SESSION['userid'], $this->put('FarmerID'), $this->put('SurveyNr'), $this->put('GardenNr'), $this->put('IMSSurveyNr'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Data could not be found'), 404);
        }

    }

    public function garden_delete()
    {
        if (!$this->delete('FarmerID')) {
            $this->response(null, 400);
        }

        $data = $this->mims->deleteGarden($_SESSION['userid'], $this->delete('FarmerID'), $this->delete('SurveyNr'), $this->delete('GardenNr'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Data could not be found'), 404);
        }

    }
    //--Certification--//
    public function certification_detail_get()
    {
        $data = $this->mims->readCertificationDetail($this->get('FarmerID'), $this->get('SurveyNr'), $this->get('GardenNr'), $this->get('Certification'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array(), 200);
        }

    }
    public function duplicate_certification_put()
    {
        if (!$this->put('FarmerID')) {
            $this->response(null, 400);
        }

        $data = $this->mims->duplicateCertification($_SESSION['userid'], $this->put('FarmerID'), $this->put('SurveyNr'), $this->put('GardenNr'), $this->put('IMSSurveyNr'), $this->put('Certification'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Data could not be found'), 404);
        }

    }
    public function certification_delete()
    {
        if (!$this->delete('FarmerID')) {
            $this->response(null, 400);
        }

        $data = $this->mims->deleteCertification($_SESSION['userid'], $this->delete('FarmerID'), $this->delete('SurveyNr'), $this->delete('GardenNr'), $this->delete('Certification'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Data could not be found'), 404);
        }

    }
    //--Audit--//
    public function audit_detail_get()
    {
        $data = $this->mims->readAuditDetail($this->get('FarmerID'), $this->get('SurveyNr'), $this->get('GardenNr'), $this->get('Certification'), $this->get('ICSDate'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array(), 200);
        }

    }
    public function duplicate_audit_put()
    {
        if (!$this->put('FarmerID')) {
            $this->response(null, 400);
        }

        $data = $this->mims->duplicateAudit($_SESSION['userid'], $this->put('FarmerID'), $this->put('SurveyNr'), $this->put('GardenNr'), $this->put('IMSSurveyNr'), $this->put('Certification'), $this->put('ICSDate'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Data could not be found'), 404);
        }

    }
    public function audit_delete()
    {
        if (!$this->delete('FarmerID')) {
            $this->response(null, 400);
        }

        $data = $this->mims->deleteAudit($_SESSION['userid'], $this->delete('FarmerID'), $this->delete('SurveyNr'), $this->delete('GardenNr'), $this->delete('Certification'), $this->delete('ICSDate'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Data could not be found'), 404);
        }

    }

    public function print_contract_get($IMSID = '', $FarmerID = '')
    {
        $data['ims']    = $this->mims->getIMSInfo($IMSID);
        $ArrFarmerID = explode("::", $FarmerID);

        for ($i=0; $i < count($ArrFarmerID); $i++) {
            $data['farmer'] = $this->mims->getFarmerInfo($ArrFarmerID[$i]);
            $this->load->view('print_certification_contract_farmer', $data);
        }
    }

    public function print_contract_applicant_get($IMSID = '', $ApplicantID = '')
    {
        $data['ims']    = $this->mims->getIMSInfo($IMSID);
        $data['farmer'] = $this->mims->getApplicantInfo($ApplicantID);
        $this->load->view('print_certification_contract_farmer', $data);
    }

    public function ims_file_post()
    {
        $IMSID                   = $this->post('IMSID');
        $Remarks                 = $this->post('Remarks');
        $config['upload_path']   = './files/upload/ims';
        $config['allowed_types'] = 'gif|jpg|png|pdf';
        $config['max_size']      = '1024';
        // $config['max_width']        = '1024';
        // $config['max_height']       = '768';

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('File')) {
            $data = array('error' => $this->upload->display_errors());
            $this->response(array('success' => false, 'msg' => $this->upload->display_errors()), 200);
        } else {
            $data = $this->upload->data();
            $this->mims->addIMSFile($IMSID, $Remarks, $data);
            $this->response(array('success' => true, 'msg' => ''), 200);
        }
    }

    public function main_list_master_get()
    {
        $sorting      = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $data = $this->mims->getMainListMasterIms($this->get('searchDesc'), $this->get('start'), $this->get('limit'), $sortingField, $sortingDir);
        $this->response($data, 200);
    }

    public function combo_certificate_holder_get()
    {
        $data = $this->mims->getComboCertHolder();
        $this->response($data, 200);
    }

    public function cmb_cert_holder_by_first_buyer_get(){
        $FirstBuyerID = (int) $this->get('FirstBuyerID');
        $data = $this->mims->GetCmbCertHolderByFirstBuyerID($FirstBuyerID);
        $this->response($data, 200);
    }

    public function ims_master_fill_form_get()
    {
        $IMSMasterID = (int) $this->get('IMSMasterID');
        $data        = $this->mims->imsMasterFillForm($IMSMasterID);
        $this->response($data, 200);
    }

    public function cert_holder_prog_by_ims_master_get()
    {
        $IMSMasterID = (int) $this->get('IMSMasterID');
        $data        = $this->mims->getCertHolderProgByImsMaster($IMSMasterID);
        $this->response($data, 200);
    }

    public function grid_annual_certificate_get()
    {
        $sorting      = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $data = $this->mims->getGridAnnualCertificate($this->get('IMSMasterID'), $this->get('SearchEventName'), $this->get('start'), $this->get('limit'), $sortingField, $sortingDir);
        $this->response($data, 200);
    }

    public function ims_event_grid_staff_get()
    {
        $sorting      = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $data = $this->mims->getImsEventGridStaff($this->get('IMSMasterID'), $this->get('SearchStaffName'), $this->get('start'), $this->get('limit'), $sortingField, $sortingDir);
        $this->response($data, 200);
    }

    public function ims_event_grid_staff_input_get()
    {
        $sorting      = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $IMSMasterID = (int) $this->get('IMSMasterID');
        $IMSID       = (int) $this->get('IMSID');

        $data = $this->mims->getImsEventGridStaffInput($IMSMasterID, $IMSID, $this->get('start'), $this->get('limit'), $sortingField, $sortingDir);
        $this->response($data, 200);
    }

    public function ims_event_grid_summary_get()
    {
        $IMSMasterID = (int) $this->get('IMSMasterID');
        $data        = $this->mims->getImsEventGridSummary($IMSMasterID);
        $this->response($data, 200);
    }

    public function ims_event_grid_files_get()
    {
        $IMSMasterID = (int) $this->get('IMSMasterID');

        $sorting      = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $data = $this->mims->getImsEventGridFiles($IMSMasterID, $this->get('start'), $this->get('limit'), $sortingField, $sortingDir);
        $this->response($data, 200);
    }

    public function ims_event_detail_fill_form_get()
    {
        $IMSID = (int) $this->get('IMSID');
        $data  = $this->mims->imsEventDetailFillForm($IMSID);
        $this->response($data, 200);
    }

    public function certification_program_label_get()
    {
        $CertHolderID = (int) $this->get('CertHolderID');
        $data         = $this->mims->certProgramLabelGetByCertHolderID($CertHolderID);
        $this->response($data, 200);
    }

    public function ims_event_detail_farmer_garden_fill_form_get()
    {
        $FarmerID = (int) $this->get('FarmerID');
        $GardenNr = (int) $this->get('GardenNr');
        $SurveyNr = (int) $this->get('SurveyNr');
        $data     = $this->mims->imsEventDetailFarmerGardenFillForm($FarmerID, $GardenNr, $SurveyNr);
        $this->response($data, 200);
    }

    public function ims_event_file_upload_post()
    {
        //upload filenya dan simpan ke database
        if ($this->file['winImsEventFileUploadForm_File']['name'] != '') {
            $this->load->library('awsfileupload');

            //set folder upload
            if ($this->post('winImsEventFileUploadForm_callForm') == "ims_event") {
                $folderUpload = AWSS3_IMS_MASTER_FILES_PATH;
            } else if ($this->post('winImsEventFileUploadForm_callForm') == "ims_event_detail") {
                $folderUpload = AWSS3_IMS_EVENT_FILES_PATH;
            } else {
                $result['success'] = false;
                $this->response($result, 200);
            }

            $upload = $this->awsfileupload->upload($_FILES['winImsEventFileUploadForm_File']['tmp_name'],$_FILES['winImsEventFileUploadForm_File']['name'], $folderUpload, 'documents', true);
            if ($upload['success'] == true) {
                $prosesUpdate = $this->mims->imsEventFileUploadInput($this->post(), $upload['filenamepath']);
                $result['success'] = true;
                $result['message'] = lang('File uploaded');
                $result['fileurl'] = $upload['fileurl'];
                $this->response($result, 200);
            } else {
                $result['success'] = false;
                $result['message'] = lang('Upload to aws failed');
                $this->response($result, 400);
            }
        } else {
            $result['success'] = false;
            $this->response($result, 200);
        }
    }

    public function ims_event_file_upload_delete()
    {
        $imsType  = $this->delete('imsType');
        $IDCaller = (int) $this->delete('IDCaller');

        $result = $this->mims->imsEventFileUploadDelete($imsType, $IDCaller);
        $this->response($result, 200);
    }

    public function ims_event_post()
    {
        $varPost = $this->post();

        if ($varPost['imsCertFormImsEvent_IMSMasterID'] == "") {
            $result = $this->mims->imsEventMasterInsert($varPost);
        } else {
            $result = $this->mims->imsEventMasterUpdate($varPost);
        }
        $this->response($result, 200);
    }

    public function ims_event_delete()
    {
        $IMSMasterID = (int) $this->delete('IMSMasterID');
        $result      = $this->mims->imsEventMasterDelete($IMSMasterID);
        $this->response($result, 200);
    }

    public function ims_event_detail_post()
    {
        $varPost = $this->post();

        if ($varPost['IMSID'] == "") {
            $result = $this->mims->imsEventDetailInsert($varPost);
        } else {
            $result = $this->mims->imsEventDetailUpdate($varPost);
        }
        $this->response($result, 200);
    }

    public function ims_event_detail_delete()
    {
        $IMSID  = (int) $this->delete('IMSID');
        $result = $this->mims->imsEventDetailDelete($IMSID);
        $this->response($result, 200);
    }

    public function buying_unit_add_list_get()
    {
        $IMSID      = (int) $this->get('IMSID');
        $NameSearch = $this->get('key');
        $ProvinceID = (int) $this->get('ProvinceID');
        $DistrictID = (int) $this->get('DistrictID');
        $ObjType    = $this->get('ObjType');

        $data = $this->mims->getBuyingUnitAddList($IMSID, $NameSearch, $ProvinceID, $DistrictID, $ObjType);
        $this->response($data, 200);
    }

    public function buying_unit_add_post()
    {
        $IMSMasterID   = (int) $this->post('IMSMasterID');
        $IMSID         = (int) $this->post('IMSID');
        $bunits        = $this->post('bunits');
        $arrTempBunits = explode(",", $bunits);

        $proses = $this->mims->inputBuyingUnit($IMSMasterID, $IMSID, $arrTempBunits);
        $this->response($proses, 200);
    }

    public function buying_unit_input_delete()
    {
        $SupplychainID = (int) $this->delete('SupplychainID');
        $IMSID         = (int) $this->delete('IMSID');

        $proses = $this->mims->deleteBuyingUnit($IMSID, $SupplychainID);
        $this->response($proses, 200);
    }

    public function fill_form_eligible_farmer_get()
    {
        $FarmerID = (int) $this->get('FarmerID');
        $IMSID    = (int) $this->get('IMSID');

        $data = $this->mims->getFillFormEligibleFarmer($IMSID, $FarmerID);
        $this->response($data, 200);
    }

    public function form_eligible_farmer_post()
    {
        $StatusAudit        = (int) $this->post('StatusAudit');
        $NotEligibleReason  = $this->post('NotEligibleReason');
        $StatusComply       = $this->post('StatusComply');
        $StatusComplyRemark = $this->post('StatusComplyRemark');
        $IMSID              = (int) $this->post('IMSID');
        $FarmerID           = (int) $this->post('FarmerID');
        $ICSDate            = $this->post('ICSDate');

        $data = $this->mims->updateStatusEligibleFarmer($StatusAudit, $NotEligibleReason, $StatusComply, $StatusComplyRemark, $IMSID, $FarmerID, $ICSDate);
        $this->response($data, 200);
    }

    public function gen_pre_afl_garden_post()
    {
        $IMSID  = (int) $this->post('IMSID');
        $proses = $this->mims->genPreAflGarden('gen',$IMSID);
        $this->response($proses, 200);
    }

    public function gen_afl_farmer_and_garden_post()
    {
        $IMSID = (int) $this->post('IMSID');

        //get CertEventDate
        $dataIms       = $this->mims->imsEventDetailFillForm($IMSID);
        $CertEventDate = $dataIms['data']['CertEventDate'];

        $proses = $this->mims->genAflFarmerAndGarden('gen',$IMSID, $CertEventDate);
        $this->response($proses, 200);
    }

    public function regen_afl_farmer_and_garden_post(){
        $IMSID = (int) $this->post('IMSID');
        $dataIms       = $this->mims->imsEventDetailFillForm($IMSID);
        $CertEventDate = $dataIms['data']['CertEventDate'];

        $ProsesGenPreAfl = $this->mims->genPreAflGarden('regen',$IMSID);
        $ProsesGenAfl = $this->mims->genAflFarmerAndGarden('regen',$IMSID, $CertEventDate);

        if($ProsesGenPreAfl['success'] == true && $ProsesGenAfl['success'] == true){
            $proses['success'] = true;
            $proses['message'] = lang('Regenerate ICS Success');
            $RpCode = 200;
        }else{
            $RpCode = 400;
            $proses['success'] = false;

            if($ProsesGenPreAfl['success'] == false){
                $proses['message'] = lang('Regenerate ICS Pre AFL Garden Failed');
            }

            if($ProsesGenAfl['success'] == false){
                $proses['message'] = lang('Regenerate ICS AFL Failed');
            }
        }
        $this->response($proses, $RpCode);
    }

    public function ims_event_detail_farmer_pre_afl_grid_get($IMSID){
        ini_set('memory_limit', '-1');

        $IMSID = (int) $IMSID;

        //data yg diperlukan (begin)
        $dataList = $this->mims->readCandidateImsDetail($IMSID, '', null, null, null, null,'export_excel');

        // if (sizeof($dataList) <= 0) {
        //     $DataReturn['success'] = true;
        //     $DataReturn['count_data'] = 0;
        //     $this->response($DataReturn, 200);
        // }
        

        //Ambil data query nya =============== (End)
        //Write Excelnya
        $UrlFilenya = $this->WriteExcelForExport('candidate_ims_pre_afl_grid', $dataList);

        $DataReturn['success'] = true;
        $DataReturn['UrlFilenya'] = $UrlFilenya;
        $this->response($DataReturn, 200);
    }

    public function ims_event_detail_farmer_pre_afl_garden_get($IMSID)
    {
        //ini_set('display_errors',true);
        //error_reporting(E_ALL);
        ini_set('memory_limit', '-1');

        $IMSID = (int) $IMSID;

        //data yg diperlukan (begin)
        //$dataList = $this->mims->getExportExcelFarmerPreAflGarden($IMSID);
        $dataList = $this->mims->getExportExcelFarmerPreAflGardenByAflGarden($IMSID);

        if (sizeof($dataList) <= 0) {
            $DataReturn['success'] = true;
            $DataReturn['count_data'] = 0;
            $this->response($DataReturn, 200);
        }
        //Ambil data query nya =============== (End)
        //Write Excelnya
        // echo '<pre>'; print_r($dataList); exit;
        $UrlFilenya = $this->WriteExcelForExport('ims_pre_ics_garden', $dataList);

        $DataReturn['success'] = true;
        $DataReturn['UrlFilenya'] = $UrlFilenya;
        $this->response($DataReturn, 200);
    }

    public function ims_event_detail_afl_p1_summary_post()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $IMSID = (int) $this->post('IMSID');

        //data yg diperlukan (begin)
        $dataPetani        = $this->mims->getFarmerAflP1Summary($IMSID);
        $dataGarden        = $this->mims->getGardenAflP1Summary($IMSID);
        $dataPostHarvest   = $this->mims->getPostHarvestAflP1Summary($IMSID);
        $dataCertification = $this->mims->getCertificationAflP1Summary($IMSID);
        $dataAuditLog      = $this->mims->getAuditLogAflP1Summary($IMSID);
        //data yg diperlukan (end)


        $writer = WriterEntityFactory::createXLSXWriter(); // for XLSX files
        //$writer = WriterFactory::create(Type::CSV); // for CSV files
        //$writer = WriterFactory::create(Type::ODS); // for ODS files

        $writer->setTempFolder('files/sql_view_temp/');
        $namaFile = date('YmdHis') . '_ICS_P1_Summary';
        $filePath = 'files/sql_view/'.$namaFile.'.xlsx';
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

        //style data
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
        
        //DATA Petani ========================= (Begin)
        //data kolom
        $dataHeaderPetani = array('No.');
        foreach ($dataPetani as $key => $value) {
            if ($key == 0) {
                foreach ($value as $key2 => $value2) {
                    $dataHeaderPetani[] = $key2;
                }
            } else {
                break;
            }
        }

        $petaniSheet = $writer->getCurrentSheet()->setName(lang('Petani'));
        $rowHeader = WriterEntityFactory::createRowFromArray($dataHeaderPetani, $styleHeader);
        $writer->addRow($rowHeader);

        for ($i=0; $i < count($dataPetani); $i++) {
            $dataRows = $dataPetani[$i];
            $cells = array();
            $cells[] = WriterEntityFactory::createCell((int) ($i+1), $styleFormatAngka);

            foreach ($dataRows as $key => $v){
                $styleRow = null;
                $dataRow = null;
                
                if(is_numeric($v)){
                    $styleRow = $styleFormatAngka;
                    $dataRow = (float) $v;
                } else {
                    //cek apakah tanggal
                    if($this->validateDate($v) == true) {
                        $styleRow = $styleFormatTanggal;
                    } else {
                        $styleRow = $styleData;
                    }
                    $dataRow = $v;
                }

                $cells[] = WriterEntityFactory::createCell($dataRow, $styleRow);
            }

            $rowData = WriterEntityFactory::createRow($cells);
            $writer->addRow($rowData);
        }

        /*foreach ($dataPetani as $key => $value) {
            array_unshift($value, ($key + 1));
            $rowData = WriterEntityFactory::createRowFromArray($value, $styleData);
            $writer->addRow($rowData);
        }*/

//        $this->response($dataPetani, 200);
        //DATA Petani ========================= (End)

        
        //DATA Garden ========================= (Begin)
        //data kolom
        $dataHeaderSession = array('No.');
        foreach ($dataGarden as $key => $value) {
            if ($key == 0) {
                foreach ($value as $key2 => $value2) {
                    $dataHeaderSession[] = $key2;
                }
            } else {
                break;
            }
        }

        $gardenSheet = $writer->addNewSheetAndMakeItCurrent()->setName(lang('Garden'));
        $rowHeader = WriterEntityFactory::createRowFromArray($dataHeaderSession, $styleHeader);
        $writer->addRow($rowHeader);

        for ($i=0; $i < count($dataGarden); $i++) {
            $dataRows = $dataGarden[$i];
            $cells = array();
            $cells[] = WriterEntityFactory::createCell((int) ($i+1), $styleFormatAngka);

            foreach ($dataRows as $key => $v){
                $styleRow = null;
                $dataRow = null;
                
                if(is_numeric($v)){
                    $styleRow = $styleFormatAngka;
                    $dataRow = (float) $v;
                } else {
                    //cek apakah tanggal
                    if($this->validateDate($v) == true) {
                        $styleRow = $styleFormatTanggal;
                    } else {
                        $styleRow = $styleData;
                    }
                    $dataRow = $v;
                }

                $cells[] = WriterEntityFactory::createCell($dataRow, $styleRow);
            }

            $rowData = WriterEntityFactory::createRow($cells);
            $writer->addRow($rowData);
        }

        /*foreach ($dataGarden as $key => $value) {
            array_unshift($value, ($key + 1));
            $rowData = WriterEntityFactory::createRowFromArray($value, $styleData);
            $writer->addRow($rowData);
        }*/
        //DATA Garden ========================= (End)

        //DATA Post Harvest ========================= (Begin)
        //data kolom
        $dataHeaderPostHarvest = array('No.');
        foreach ($dataPostHarvest as $key => $value) {
            if ($key == 0) {
                foreach ($value as $key2 => $value2) {
                    $dataHeaderPostHarvest[] = $key2;
                }
            } else {
                break;
            }
        }

        $gardenSheet = $writer->addNewSheetAndMakeItCurrent()->setName(lang('Post Harvest'));
        $rowHeader = WriterEntityFactory::createRowFromArray($dataHeaderPostHarvest, $styleHeader);
        $writer->addRow($rowHeader);

        for ($i=0; $i < count($dataPostHarvest); $i++) {
            $dataRows = $dataPostHarvest[$i];
            $cells = array();
            $cells[] = WriterEntityFactory::createCell((int) ($i+1), $styleFormatAngka);

            foreach ($dataRows as $key => $v){
                $styleRow = null;
                $dataRow = null;
                
                if(is_numeric($v)){
                    $styleRow = $styleFormatAngka;
                    $dataRow = (float) $v;
                } else {
                    //cek apakah tanggal
                    if($this->validateDate($v) == true) {
                        $styleRow = $styleFormatTanggal;
                    } else {
                        $styleRow = $styleData;
                    }
                    $dataRow = $v;
                }

                $cells[] = WriterEntityFactory::createCell($dataRow, $styleRow);
            }

            $rowData = WriterEntityFactory::createRow($cells);
            $writer->addRow($rowData);
        }

        /*foreach ($dataPostHarvest as $key => $value) {
            array_unshift($value, ($key + 1));
            $rowData = WriterEntityFactory::createRowFromArray($value, $styleData);
            $writer->addRow($rowData);
        }*/
        //DATA Post Harvest ========================= (End)

        //DATA Certification ========================= (Begin)
        //data kolom
        $dataHeaderCertification = array('No.');
        foreach ($dataCertification as $key => $value) {
            if ($key == 0) {
                foreach ($value as $key2 => $value2) {
                    $dataHeaderCertification[] = $key2;
                }
            } else {
                break;
            }
        }

        $gardenSheet = $writer->addNewSheetAndMakeItCurrent()->setName(lang('Certification'));
        $rowHeader = WriterEntityFactory::createRowFromArray($dataHeaderCertification, $styleHeader);
        $writer->addRow($rowHeader);

        for ($i=0; $i < count($dataCertification); $i++) {
            $dataRows = $dataCertification[$i];
            $cells = array();
            $cells[] = WriterEntityFactory::createCell((int) ($i+1), $styleFormatAngka);

            foreach ($dataRows as $key => $v){
                $styleRow = null;
                $dataRow = null;
                
                if(is_numeric($v)){
                    $styleRow = $styleFormatAngka;
                    $dataRow = (float) $v;
                } else {
                    //cek apakah tanggal
                    if($this->validateDate($v) == true) {
                        $styleRow = $styleFormatTanggal;
                    } else {
                        $styleRow = $styleData;
                    }
                    $dataRow = $v;
                }

                $cells[] = WriterEntityFactory::createCell($dataRow, $styleRow);
            }

            $rowData = WriterEntityFactory::createRow($cells);
            $writer->addRow($rowData);
        }

        /*foreach ($dataCertification as $key => $value) {
            array_unshift($value, ($key + 1));
            $rowData = WriterEntityFactory::createRowFromArray($value, $styleData);
            $writer->addRow($rowData);
        }*/
        //DATA Certification ========================= (End)

        //DATA Audit Log ========================= (Begin)
        //data kolom
        $dataHeaderAuditLog = array('No.');
        foreach ($dataAuditLog as $key => $value) {
            if ($key == 0) {
                foreach ($value as $key2 => $value2) {
                    $dataHeaderAuditLog[] = $key2;
                }
            } else {
                break;
            }
        }

        $gardenSheet = $writer->addNewSheetAndMakeItCurrent()->setName(lang('Audit Log'));
        $rowHeader = WriterEntityFactory::createRowFromArray($dataHeaderAuditLog, $styleHeader);
        $writer->addRow($rowHeader);

        for ($i=0; $i < count($dataAuditLog); $i++) {
            $dataRows = $dataAuditLog[$i];
            $cells = array();
            $cells[] = WriterEntityFactory::createCell((int) ($i+1), $styleFormatAngka);

            foreach ($dataRows as $key => $v){
                $styleRow = null;
                $dataRow = null;
                
                if(is_numeric($v)){
                    $styleRow = $styleFormatAngka;
                    $dataRow = (float) $v;
                } else {
                    //cek apakah tanggal
                    if($this->validateDate($v) == true) {
                        $styleRow = $styleFormatTanggal;
                    } else {
                        $styleRow = $styleData;
                    }
                    $dataRow = $v;
                }

                $cells[] = WriterEntityFactory::createCell($dataRow, $styleRow);
            }

            $rowData = WriterEntityFactory::createRow($cells);
            $writer->addRow($rowData);
        }

        /*foreach ($dataAuditLog as $key => $value) {
            array_unshift($value, ($key + 1));
            $rowData = WriterEntityFactory::createRowFromArray($value, $styleData);
            $writer->addRow($rowData);
        }*/
        //DATA Audit Log ========================= (End)


        $writer->setCurrentSheet($petaniSheet);
        $writer->close();
        $this->response(array('success' => true, 'filenya' => base_url() . $filePath), 200);
        exit;
    }

    public function old_ims_event_detail_afl_p1_summary_get($IMSID)
    {
        // ini_set('display_errors',true);
        // error_reporting(E_ALL);
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $IMSID = (int) $IMSID;

        //data yg diperlukan (begin)
        $dataPetani        = $this->mims->getFarmerAflP1Summary($IMSID);
        $dataGarden        = $this->mims->getGardenAflP1Summary($IMSID);
        $dataPostHarvest   = $this->mims->getPostHarvestAflP1Summary($IMSID);
        $dataCertification = $this->mims->getCertificationAflP1Summary($IMSID);
        $dataAuditLog      = $this->mims->getAuditLogAflP1Summary($IMSID);
        //data yg diperlukan (end)

        require_once 'application/third_party/PHPExcel18/PHPExcel.php';
        require_once 'application/third_party/PHPExcel18/PHPExcel/IOFactory.php';

        //=============== MULAI TULIS EXCEL (BEGIN) ===================================================================//
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("PT Koltiva")
            ->setLastModifiedBy("PT Koltiva")
            ->setTitle("FA - Progress AFL Data")
            ->setSubject("FA - Progress AFL Data")
            ->setDescription("FA - Progress AFL Data")
            ->setKeywords("FA - Progress AFL Data")
            ->setCategory("FA - Progress AFL Data");

        //set style  (begin)
        $styleFont = array(
            'font'      => array(
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

        $styleFontBoldMainTitle = array(
            'font'      => array(
                'name' => 'Arial',
                'size' => '11',
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
        );

        $styleFontBoldTitle = array(
            'font'      => array(
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
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '8DB4E3'),
            ),
        );
        $styleFontBoldBgRedCenter = array(
            'font'      => array(
                'name' => 'Arial',
                'size' => '9',
                'bold' => true,
            ),
            'fill'      => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'C0504D'),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $styleBorderFull = array(
            'borders' => array(
                'left'   => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'right'  => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'top'    => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );
        //set style  (end)

        //SHEET PETANI ================================================== (BEGIN)

        //data kolom
        $dataKolom = array();
        $increTemp = 0;
        foreach ($dataPetani as $key => $value) {
            if ($key == 0) {
                foreach ($value as $key2 => $value2) {
                    $dataKolom[$increTemp]['name'] = $key2;
                    $increTemp++;
                }
            } else {
                break;
            }
        }

        //create sheet
        $objWorkSheetPetani = $objPHPExcel->createSheet(0);
        $objWorkSheetPetani->setTitle('Farmer');

        //tabel header (begin)
        $objWorkSheetPetani->setCellValue('B2', 'No');
        $columnstart = 2;
        for ($i = 0; $i < count($dataKolom); $i++) {
            $objWorkSheetPetani->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart) . '2', $dataKolom[$i]['name']);
            $columnstart++;
        }
        $columnstart--;
        $columnstartLast = $columnstart;
        $objWorkSheetPetani->getStyle('B2:' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . '2')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetPetani->getStyle('B2:' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . '2')->applyFromArray($styleBorderFull, false);
        //tabel header (end)

        $rowStart = 3;
        $incre    = 0;
        foreach ($dataPetani as $val) {

            $val['no'] = $incre + 1;
            $objWorkSheetPetani->setCellValue('B' . $rowStart, $val['no']);

            $columnstart = 2;
            for ($i = 0; $i < count($dataKolom); $i++) {
                $objWorkSheetPetani->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart) . $rowStart, $val[$dataKolom[$i]['name']]);
                $columnstart++;
            }

            $objWorkSheetPetani->getStyle('B' . $rowStart . ':' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetPetani->getStyle('B' . $rowStart . ':' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }
        //SHEET PETANI ================================================== (END)

        //SHEET GARDEN ================================================== (BEGIN)
        //data kolom
        $dataKolom = array();
        $increTemp = 0;
        foreach ($dataGarden as $key => $value) {
            if ($key == 0) {
                foreach ($value as $key2 => $value2) {
                    $dataKolom[$increTemp]['name'] = $key2;
                    $increTemp++;
                }
            } else {
                break;
            }
        }

        //create sheet
        $objWorkSheetGarden = $objPHPExcel->createSheet(1);
        $objWorkSheetGarden->setTitle('Garden');

        //tabel header (begin)
        $objWorkSheetGarden->setCellValue('B2', 'No');
        $columnstart = 2;
        for ($i = 0; $i < count($dataKolom); $i++) {
            $objWorkSheetGarden->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart) . '2', $dataKolom[$i]['name']);
            $columnstart++;
        }
        $columnstart--;
        $columnstartLast = $columnstart;
        $objWorkSheetGarden->getStyle('B2:' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . '2')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetGarden->getStyle('B2:' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . '2')->applyFromArray($styleBorderFull, false);
        //tabel header (end)

        $rowStart = 3;
        $incre    = 0;
        foreach ($dataGarden as $val) {

            $val['no'] = $incre + 1;
            $objWorkSheetGarden->setCellValue('B' . $rowStart, $val['no']);

            $columnstart = 2;
            for ($i = 0; $i < count($dataKolom); $i++) {
                $objWorkSheetGarden->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart) . $rowStart, $val[$dataKolom[$i]['name']]);
                $columnstart++;
            }

            $objWorkSheetGarden->getStyle('B' . $rowStart . ':' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetGarden->getStyle('B' . $rowStart . ':' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }

        //SHEET GARDEN ================================================== (END)

        //SHEET POST HARVEST ================================================== (BEGIN)

        //data kolom
        $dataKolom = array();
        $increTemp = 0;
        foreach ($dataPostHarvest as $key => $value) {
            if ($key == 0) {
                foreach ($value as $key2 => $value2) {
                    $dataKolom[$increTemp]['name'] = $key2;
                    $increTemp++;
                }
            } else {
                break;
            }
        }

        //create sheet
        $objWorkSheetPostHarvest = $objPHPExcel->createSheet(2);
        $objWorkSheetPostHarvest->setTitle('Post Harvest');

        //tabel header (begin)
        $objWorkSheetPostHarvest->setCellValue('B2', 'No');
        $columnstart = 2;
        for ($i = 0; $i < count($dataKolom); $i++) {
            $objWorkSheetPostHarvest->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart) . '2', $dataKolom[$i]['name']);
            $columnstart++;
        }
        $columnstart--;
        $columnstartLast = $columnstart;
        $objWorkSheetPostHarvest->getStyle('B2:' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . '2')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetPostHarvest->getStyle('B2:' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . '2')->applyFromArray($styleBorderFull, false);
        //tabel header (end)

        $rowStart = 3;
        $incre    = 0;
        foreach ($dataPostHarvest as $val) {

            $val['no'] = $incre + 1;
            $objWorkSheetPostHarvest->setCellValue('B' . $rowStart, $val['no']);

            $columnstart = 2;
            for ($i = 0; $i < count($dataKolom); $i++) {
                $objWorkSheetPostHarvest->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart) . $rowStart, $val[$dataKolom[$i]['name']]);
                $columnstart++;
            }

            $objWorkSheetPostHarvest->getStyle('B' . $rowStart . ':' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetPostHarvest->getStyle('B' . $rowStart . ':' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }

        //SHEET POST HARVEST ================================================== (END)

        //SHEET CERTIFICATION ================================================== (BEGIN)

        //data kolom
        $dataKolom = array();
        $increTemp = 0;
        foreach ($dataCertification as $key => $value) {
            if ($key == 0) {
                foreach ($value as $key2 => $value2) {
                    $dataKolom[$increTemp]['name'] = $key2;
                    $increTemp++;
                }
            } else {
                break;
            }
        }

        //create sheet
        $objWorkSheetCertification = $objPHPExcel->createSheet(3);
        $objWorkSheetCertification->setTitle('Certification');

        //tabel header (begin)
        $objWorkSheetCertification->setCellValue('B2', 'No');
        $columnstart = 2;
        for ($i = 0; $i < count($dataKolom); $i++) {
            $objWorkSheetCertification->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart) . '2', $dataKolom[$i]['name']);
            $columnstart++;
        }
        $columnstart--;
        $columnstartLast = $columnstart;
        $objWorkSheetCertification->getStyle('B2:' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . '2')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetCertification->getStyle('B2:' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . '2')->applyFromArray($styleBorderFull, false);
        //tabel header (end)

        $rowStart = 3;
        $incre    = 0;
        foreach ($dataCertification as $val) {

            $val['no'] = $incre + 1;
            $objWorkSheetCertification->setCellValue('B' . $rowStart, $val['no']);

            $columnstart = 2;
            for ($i = 0; $i < count($dataKolom); $i++) {
                $objWorkSheetCertification->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart) . $rowStart, $val[$dataKolom[$i]['name']]);
                $columnstart++;
            }

            $objWorkSheetCertification->getStyle('B' . $rowStart . ':' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetCertification->getStyle('B' . $rowStart . ':' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }

        //SHEET CERTIFICATION ================================================== (END)

        //SHEET AUDIT LOG ================================================== (BEGIN)

        //data kolom
        $dataKolom = array();
        $increTemp = 0;
        foreach ($dataAuditLog as $key => $value) {
            if ($key == 0) {
                foreach ($value as $key2 => $value2) {
                    $dataKolom[$increTemp]['name'] = $key2;
                    $increTemp++;
                }
            } else {
                break;
            }
        }

        //create sheet
        $objWorkSheetAuditLog = $objPHPExcel->createSheet(4);
        $objWorkSheetAuditLog->setTitle('Audit Log');

        //tabel header (begin)
        $objWorkSheetAuditLog->setCellValue('B2', 'No');
        $columnstart = 2;
        for ($i = 0; $i < count($dataKolom); $i++) {
            $objWorkSheetAuditLog->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart) . '2', $dataKolom[$i]['name']);
            $columnstart++;
        }
        $columnstart--;
        $columnstartLast = $columnstart;
        $objWorkSheetAuditLog->getStyle('B2:' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . '2')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetAuditLog->getStyle('B2:' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . '2')->applyFromArray($styleBorderFull, false);
        //tabel header (end)

        $rowStart = 3;
        $incre    = 0;
        foreach ($dataAuditLog as $val) {

            $val['no'] = $incre + 1;
            $objWorkSheetAuditLog->setCellValue('B' . $rowStart, $val['no']);

            $columnstart = 2;
            for ($i = 0; $i < count($dataKolom); $i++) {
                $objWorkSheetAuditLog->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart) . $rowStart, $val[$dataKolom[$i]['name']]);
                $columnstart++;
            }

            $objWorkSheetAuditLog->getStyle('B' . $rowStart . ':' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetAuditLog->getStyle('B' . $rowStart . ':' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }

        //SHEET AUDIT LOG ================================================== (END)

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . date('YmdHis') . '_ICS_P1_Summary.xlsx');
        header('Cache-Control: max-age=0');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function ims_event_detail_cfl_farmer_export_get($IMSID){        
        ini_set('display_errors',true);
        error_reporting(E_ALL);
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $IMSID = (int) $IMSID;
        $PartnerID = $_SESSION['PartnerID'];

        //data yg diperlukan (begin)
        $dataIms       = $this->mims->imsEventDetailFillForm($IMSID);
        $dataCfl       = $this->mims->cflListExportExcel($IMSID);
        $dataCflGarden = $this->mims->cflListExportExcelGarden($IMSID);

        $dataCflNotCert = $this->mims->cflNotCertListExportExcel($IMSID);
        $dataCflNotCertGarden = $this->mims->cflNotCertListGardenExportExcel($IMSID);
        //data yg diperlukan (end)        
        
        $writer = WriterEntityFactory::createXLSXWriter(); // for XLSX files
        //$writer = WriterFactory::create(Type::CSV); // for CSV files
        //$writer = WriterFactory::create(Type::ODS); // for ODS files

        $writer->setTempFolder('files/sql_view_temp/');
        $namaFile = date('YmdHis') . '_Farmer_CFL';
        $filePath = 'files/sql_view/'.$namaFile.'.xlsx';
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

        //style data
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
        
        $empty[] = WriterEntityFactory::createCell('');
        $EmptyRows = WriterEntityFactory::createRow($empty, null);
        
        //DATA Farmer CFL ========================= (Begin)
        $FarmerCFL = $writer->getCurrentSheet()->setName(lang('Farmer'));
        $cells1[0] = WriterEntityFactory::createCell(lang('Farmer CFL'));
        $rowData = WriterEntityFactory::createRow($cells1, null);
        $writer->addRow($rowData);
        $cells2[0] = WriterEntityFactory::createCell(lang('Holder Name'));
        $cells2[1] = WriterEntityFactory::createCell($dataIms['data']['SupplychainLabel']);
        $rowData = WriterEntityFactory::createRow($cells2, null);
        $writer->addRow($rowData);
        $cells3[0] = WriterEntityFactory::createCell(lang('Certification Year'));
        $cells3[1] = WriterEntityFactory::createCell($dataIms['data']['Year'] . '/' . ((int)$dataIms['data']['Year']+1));
        $rowData = WriterEntityFactory::createRow($cells3, null);
        $writer->addRow($rowData);
        $cells4[0] = WriterEntityFactory::createCell(lang('Event name'));
        $cells4[1] = WriterEntityFactory::createCell($dataIms['data']['CertEventName']);
        $rowData = WriterEntityFactory::createRow($cells4, null);
        $writer->addRow($rowData);
        $writer->addRow($EmptyRows);
        $writer->addRow($EmptyRows);
        if ($dataCfl){
            $HeaderFarComply = array('No.');
            foreach ($dataCfl[0] as $key => $v) {
                if ($key == 'IMSID' || $key == 'AFLFarmerID' || $key == 'CertProgID' 
                        || $key === "Province" || $key === "District" || $key === "SubDistrict"
                        || $key === "BankName" || $key === "BankAccNumber" || $key === "CertStatusVerified"
                        || $key === "IMSCreator" || $key === "StatusFarmer" || $key === "ReasonStatusFarmer"
                        || $key === "EligibleForAudit" || $key === "AuditRemark" || $key === "CertAuditRemark"
                        || $key === "AuditSummaryStatus") {
                    continue;
                } else {
                    if ($PartnerID == '8') {
                        if ($key === "Birthdate") {
                            continue;
                        }
                    } else {
                        if ($key === "Age") {
                            continue;
                        }
                    }
                }
                $HeaderFarComply[] = $key;
            }
            $rowHeader = WriterEntityFactory::createRowFromArray($HeaderFarComply, $styleHeader);
            $writer->addRow($rowHeader);

            for ($i=0; $i < count($dataCfl); $i++) {
                $dataRows = $dataCfl[$i];
                $cells = array();
                $cells[] = WriterEntityFactory::createCell((int) ($i+1), $styleFormatAngka);
    
                foreach ($dataRows as $key => $v){
                    $styleRow = null;
                    $dataRow = null;
    
                    if ($key == 'IMSID' || $key == 'AFLFarmerID' || $key == 'CertProgID'
                            || $key === "Province" || $key === "District" || $key === "SubDistrict"
                            || $key === "BankName" || $key === "BankAccNumber" || $key === "CertStatusVerified"
                            || $key === "IMSCreator" || $key === "StatusFarmer" || $key === "ReasonStatusFarmer"
                            || $key === "EligibleForAudit" || $key === "AuditRemark" || $key === "CertAuditRemark"
                            || $key === "AuditSummaryStatus") {
                            continue;
                    } else {
                        if ($PartnerID == '8') {
                            if ($key === "Birthdate") {
                            continue;
                            }
                        } else {
                            if ($key === "Age") {
                            continue;
                            }
                        }
                    }
    
                    //cek apakah numeric
                    if(is_numeric($v)){
                        $styleRow = $styleFormatAngka;
                        $dataRow = (float) $v;
                    } else {
                        //cek apakah tanggal
                        if($this->validateDate($v) == true) {
                            $styleRow = $styleFormatTanggal;
                        } else {
                            $styleRow = $styleData;
                        }
                        $dataRow = $v;
                    }
    
                    $cells[] = WriterEntityFactory::createCell($dataRow, $styleRow);
                }
    
                $rowData = WriterEntityFactory::createRow($cells);
                $writer->addRow($rowData);
            }

            /*foreach ($dataCfl as $k => $value) {
                $rowIsi = array();
                foreach ($value as $key => $v){
                    if ($key == 'IMSID' || $key == 'AFLFarmerID' || $key == 'CertProgID'
                            || $key === "Province" || $key === "District" || $key === "SubDistrict"
                            || $key === "BankName" || $key === "BankAccNumber" || $key === "CertStatusVerified"
                            || $key === "IMSCreator" || $key === "StatusFarmer" || $key === "ReasonStatusFarmer"
                            || $key === "EligibleForAudit" || $key === "AuditRemark" || $key === "CertAuditRemark"
                            || $key === "AuditSummaryStatus") {
                            continue;
                    } else {
                        if ($PartnerID == '8') {
                            if ($key === "Birthdate") {
                            continue;
                            }
                        } else {
                            if ($key === "Age") {
                            continue;
                            }
                        }
                    }
                    $rowIsi[] = $v;
                }
                array_unshift($rowIsi, ($k+1));
                $rowData = WriterEntityFactory::createRowFromArray($rowIsi, $styleData);
                $writer->addRow($rowData);
            }*/

        } else {
            $HeaderFarComply = array('No.', 'FarmerID', 'ExtFarmerID', 'FarmerName', 'Gender', 'Birthdate', 'FarmerGroup', 'ClusterName', 'Village', 'AFLStatus'
                , 'ExternalAuditStatus', 'CertFirstYear', 'CertHarvest', 'CertNextHarvest', 'CertHectare', 'CertFarmNr', 'ICSDate', 'TotalHa', 'CertPohonTM'
                , 'CertPohonTBM', 'CertPohonTR', 'SalesLastYear', 'SalesLast2Years', 'SalesLast3Years');
            $rowHeader = WriterEntityFactory::createRowFromArray($HeaderFarComply, $styleHeader);
            $writer->addRow($rowHeader);
            $cellNoData[0] = WriterEntityFactory::createCell(lang(' - No Data Found - '));
            $rowData = WriterEntityFactory::createRow($cellNoData, $styleHeader);
            $writer->addRow($rowData);
        }
//        $this->response($dataPetani, 200);
        //DATA Farmer CFL ========================= (End)
        
        //DATA Farmer CFL Not Certified ========================= (Begin)
        $FarmerNotCertified = $writer->addNewSheetAndMakeItCurrent()->setName(lang('Farmer (Not Certified)'));
        $cells1[0] = WriterEntityFactory::createCell(lang('Farmer (Not Certified)'));
        $rowData = WriterEntityFactory::createRow($cells1, null);
        $writer->addRow($rowData);
        $cells2[0] = WriterEntityFactory::createCell(lang('Holder Name'));
        $cells2[1] = WriterEntityFactory::createCell($dataIms['data']['SupplychainLabel']);
        $rowData = WriterEntityFactory::createRow($cells2, null);
        $writer->addRow($rowData);
        $cells3[0] = WriterEntityFactory::createCell(lang('Certification Year'));
        $cells3[1] = WriterEntityFactory::createCell($dataIms['data']['Year'] . '/' . ((int)$dataIms['data']['Year']+1));
        $rowData = WriterEntityFactory::createRow($cells3, null);
        $writer->addRow($rowData);
        $cells4[0] = WriterEntityFactory::createCell(lang('Event name'));
        $cells4[1] = WriterEntityFactory::createCell($dataIms['data']['CertEventName']);
        $rowData = WriterEntityFactory::createRow($cells4, null);
        $writer->addRow($rowData);
        $writer->addRow($EmptyRows);
        $writer->addRow($EmptyRows);
        if ($dataCflNotCert){
            $HeaderFarComply = array('No.');
            foreach ($dataCflNotCert[0] as $key => $v) {
                if ($key == 'IMSID' || $key == 'AFLFarmerID' || $key == 'CertProgID' 
                        || $key === "Province" || $key === "District" || $key === "SubDistrict"
                        || $key === "BankName" || $key === "BankAccNumber" || $key === "CertStatusVerified"
                        || $key === "IMSCreator" || $key === "StatusFarmer" || $key === "ReasonStatusFarmer"
                        || $key === "EligibleForAudit" || $key === "AuditRemark" || $key === "CertAuditRemark"
                        || $key === "AuditSummaryStatus") {
                    continue;
                } else {
                    if ($PartnerID == '8') {
                        if ($key === "Birthdate") {
                            continue;
                        }
                    } else {
                        if ($key === "Age") {
                            continue;
                        }
                    }
                }
                $HeaderFarComply[] = $key;
            }
            $rowHeader = WriterEntityFactory::createRowFromArray($HeaderFarComply, $styleHeader);
            $writer->addRow($rowHeader);

            for ($i=0; $i < count($dataCflNotCert); $i++) {
                $dataRows = $dataCflNotCert[$i];
                $cells = array();
                $cells[] = WriterEntityFactory::createCell((int) ($i+1), $styleFormatAngka);
    
                foreach ($dataRows as $key => $v){
                    $styleRow = null;
                    $dataRow = null;
    
                    if ($key == 'IMSID' || $key == 'AFLFarmerID' || $key == 'CertProgID'
                            || $key === "Province" || $key === "District" || $key === "SubDistrict"
                            || $key === "BankName" || $key === "BankAccNumber" || $key === "CertStatusVerified"
                            || $key === "IMSCreator" || $key === "StatusFarmer" || $key === "ReasonStatusFarmer"
                            || $key === "EligibleForAudit" || $key === "AuditRemark" || $key === "CertAuditRemark"
                            || $key === "AuditSummaryStatus") {
                            continue;
                    } else {
                        if ($PartnerID == '8') {
                            if ($key === "Birthdate") {
                            continue;
                            }
                        } else {
                            if ($key === "Age") {
                            continue;
                            }
                        }
                    }
    
                    //cek apakah numeric
                    if(is_numeric($v)){
                        $styleRow = $styleFormatAngka;
                        $dataRow = (float) $v;
                    } else {
                        //cek apakah tanggal
                        if($this->validateDate($v) == true) {
                            $styleRow = $styleFormatTanggal;
                        } else {
                            $styleRow = $styleData;
                        }
                        $dataRow = $v;
                    }
    
                    $cells[] = WriterEntityFactory::createCell($dataRow, $styleRow);
                }
    
                $rowData = WriterEntityFactory::createRow($cells);
                $writer->addRow($rowData);
            }

            /*foreach ($dataCflNotCert as $key => $value) {
                $rowIsi = array();
                foreach ($value as $key => $v){
                    if ($key == 'IMSID' || $key == 'AFLFarmerID' || $key == 'CertProgID'
                            || $key === "Province" || $key === "District" || $key === "SubDistrict"
                            || $key === "BankName" || $key === "BankAccNumber" || $key === "CertStatusVerified"
                            || $key === "IMSCreator" || $key === "StatusFarmer" || $key === "ReasonStatusFarmer"
                            || $key === "EligibleForAudit" || $key === "AuditRemark" || $key === "CertAuditRemark"
                            || $key === "AuditSummaryStatus") {
                            continue;
                    } else {
                        if ($PartnerID == '8') {
                            if ($key === "Birthdate") {
                            continue;
                            }
                        } else {
                            if ($key === "Age") {
                            continue;
                            }
                        }
                    }
                    $rowIsi[] = $v;
                }
                array_unshift($rowIsi, ($k+1));
                $rowData = WriterEntityFactory::createRowFromArray($rowIsi, $styleData);
                $writer->addRow($rowData);
            }*/
        } else {
            $HeaderFarComply = array('No.', 'FarmerID', 'ExtFarmerID', 'FarmerName', 'Gender', 'Birthdate', 'FarmerGroup', 'ClusterName', 'Village', 'AFLStatus'
                , 'ExternalAuditStatus', 'CertFirstYear', 'CertHarvest', 'CertNextHarvest', 'CertHectare', 'CertFarmNr', 'ICSDate', 'TotalHa', 'CertPohonTM'
                , 'CertPohonTBM', 'CertPohonTR', 'SalesLastYear', 'SalesLast2Years', 'SalesLast3Years');
            $rowHeader = WriterEntityFactory::createRowFromArray($HeaderFarComply, $styleHeader);
            $writer->addRow($rowHeader);
            $cellNoData[0] = WriterEntityFactory::createCell(lang(' - No Data Found - '));
            $rowData = WriterEntityFactory::createRow($cellNoData, $styleHeader);
            $writer->addRow($rowData);            
        }
        //DATA Farmer CFL Not Certified ========================= (End)
        
        //DATA Farmer Garden CFL ========================= (Begin)
        $Garden = $writer->addNewSheetAndMakeItCurrent()->setName(lang('Garden'));
        $cells1[0] = WriterEntityFactory::createCell(lang('Farmer Garden CFL'));
        $rowData = WriterEntityFactory::createRow($cells1, null);
        $writer->addRow($rowData);
        $cells2[0] = WriterEntityFactory::createCell(lang('Holder Name'));
        $cells2[1] = WriterEntityFactory::createCell($dataIms['data']['SupplychainLabel']);
        $rowData = WriterEntityFactory::createRow($cells2, null);
        $writer->addRow($rowData);
        $cells3[0] = WriterEntityFactory::createCell(lang('Certification Year'));
        $cells3[1] = WriterEntityFactory::createCell($dataIms['data']['Year'] . '/' . ((int)$dataIms['data']['Year']+1));
        $rowData = WriterEntityFactory::createRow($cells3, null);
        $writer->addRow($rowData);
        $cells4[0] = WriterEntityFactory::createCell(lang('Event name'));
        $cells4[1] = WriterEntityFactory::createCell($dataIms['data']['CertEventName']);
        $rowData = WriterEntityFactory::createRow($cells4, null);
        $writer->addRow($rowData);
        $writer->addRow($EmptyRows);
        $writer->addRow($EmptyRows);
        if($dataCflGarden){
            $HeaderFarComply = array('No.');
            foreach ($dataCflGarden[0] as $key => $v) {
                if ($key == 'IMSID' || $key == 'AFLFarmerID' || $key == 'CertProgID' 
                        || $key === "Province" || $key === "District" || $key === "SubDistrict"
                        || $key === "BankName" || $key === "BankAccNumber" || $key === "CertStatusVerified"
                        || $key === "IMSCreator" || $key === "StatusFarmer" || $key === "ReasonStatusFarmer"
                        || $key === "EligibleForAudit" || $key === "AuditRemark" || $key === "CertAuditRemark"
                        || $key === "AuditSummaryStatus") {
                    continue;
                } else {
                    if ($PartnerID == '8') {
                        if ($key === "Birthdate") {
                            continue;
                        }
                    } else {
                        if ($key === "Age") {
                            continue;
                        }
                    }
                }
                $HeaderFarComply[] = $key;
            }
            $rowHeader = WriterEntityFactory::createRowFromArray($HeaderFarComply, $styleHeader);
            $writer->addRow($rowHeader);

            for ($i=0; $i < count($dataCflGarden); $i++) {
                $dataRows = $dataCflGarden[$i];
                $cells = array();
                $cells[] = WriterEntityFactory::createCell((int) ($i+1), $styleFormatAngka);
    
                foreach ($dataRows as $key => $v){
                    $styleRow = null;
                    $dataRow = null;
    
                    if ($key == 'IMSID' || $key == 'AFLFarmerID' || $key == 'CertProgID'
                            || $key === "Province" || $key === "District" || $key === "SubDistrict"
                            || $key === "BankName" || $key === "BankAccNumber" || $key === "CertStatusVerified"
                            || $key === "IMSCreator" || $key === "StatusFarmer" || $key === "ReasonStatusFarmer"
                            || $key === "EligibleForAudit" || $key === "AuditRemark" || $key === "CertAuditRemark"
                            || $key === "AuditSummaryStatus") {
                            continue;
                    } else {
                        if ($PartnerID == '8') {
                            if ($key === "Birthdate") {
                            continue;
                            }
                        } else {
                            if ($key === "Age") {
                            continue;
                            }
                        }
                    }
    
                    //cek apakah numeric
                    if(is_numeric($v)){
                        $styleRow = $styleFormatAngka;
                        $dataRow = (float) $v;
                    } else {
                        //cek apakah tanggal
                        if($this->validateDate($v) == true) {
                            $styleRow = $styleFormatTanggal;
                        } else {
                            $styleRow = $styleData;
                        }
                        $dataRow = $v;
                    }
    
                    $cells[] = WriterEntityFactory::createCell($dataRow, $styleRow);
                }
    
                $rowData = WriterEntityFactory::createRow($cells);
                $writer->addRow($rowData);
            }

            /*foreach ($dataCflGarden as $key => $value) {
                $rowIsi = array();
                foreach ($value as $key => $v){
                    if ($key == 'IMSID' || $key == 'AFLFarmerID' || $key == 'CertProgID'
                            || $key === "Province" || $key === "District" || $key === "SubDistrict"
                            || $key === "BankName" || $key === "BankAccNumber" || $key === "CertStatusVerified"
                            || $key === "IMSCreator" || $key === "StatusFarmer" || $key === "ReasonStatusFarmer"
                            || $key === "EligibleForAudit" || $key === "AuditRemark" || $key === "CertAuditRemark"
                            || $key === "AuditSummaryStatus") {
                            continue;
                    } else {
                        if ($PartnerID == '8') {
                            if ($key === "Birthdate") {
                            continue;
                            }
                        } else {
                            if ($key === "Age") {
                            continue;
                            }
                        }
                    }
                    $rowIsi[] = $v;
                }
                array_unshift($rowIsi, ($k+1));
                $rowData = WriterEntityFactory::createRowFromArray($rowIsi, $styleData);
                $writer->addRow($rowData);
            }*/
        } else {
            $HeaderFarComply = array('No.', 'FarmerID', 'ExtFarmerID', 'FarmerName', 'Gender', 'Birthdate', 'FarmerGroup', 'Village', 'AFLStatus'
                , 'CertGardenNr', 'CertSurveyNr', 'ExternalAuditStatus', 'Reinspection', 'GardenStatus', 'NotActiveStatus', 'PolygonStatus', 'CertLatitude', 'CertLongitude'
                , 'CertFirstYear', 'ICSDate', 'CertHarvest', 'CertNextHarvest', 'CertHectare', 'CertPohonTM', 'CertPohonTBM', 'CertPohonTR', 'AuditComment', 'AuditRecommendationComment');
            $rowHeader = WriterEntityFactory::createRowFromArray($HeaderFarComply, $styleHeader);
            $writer->addRow($rowHeader);
            $cellNoData[0] = WriterEntityFactory::createCell(lang(' - No Data Found - '));
            $rowData = WriterEntityFactory::createRow($cellNoData, $styleHeader);
            $writer->addRow($rowData);            
        }
        //DATA Farmer Garden CFL ========================= (End)
        
        //DATA Farmer Garden CFL ========================= (Begin)
        $GardenNotCertified = $writer->addNewSheetAndMakeItCurrent()->setName(lang('Garden (Not Certified)'));
        $cells1[0] = WriterEntityFactory::createCell(lang('Farmer Garden (Not Certified)'));
        $rowData = WriterEntityFactory::createRow($cells1, null);
        $writer->addRow($rowData);
        $cells2[0] = WriterEntityFactory::createCell(lang('Holder Name'));
        $cells2[1] = WriterEntityFactory::createCell($dataIms['data']['SupplychainLabel']);
        $rowData = WriterEntityFactory::createRow($cells2, null);
        $writer->addRow($rowData);
        $cells3[0] = WriterEntityFactory::createCell(lang('Certification Year'));
        $cells3[1] = WriterEntityFactory::createCell($dataIms['data']['Year'] . '/' . ((int)$dataIms['data']['Year']+1));
        $rowData = WriterEntityFactory::createRow($cells3, null);
        $writer->addRow($rowData);
        $cells4[0] = WriterEntityFactory::createCell(lang('Event name'));
        $cells4[1] = WriterEntityFactory::createCell($dataIms['data']['CertEventName']);
        $rowData = WriterEntityFactory::createRow($cells4, null);
        $writer->addRow($rowData);
        $writer->addRow($EmptyRows);
        $writer->addRow($EmptyRows);
        if ($dataCflNotCertGarden){
            $HeaderFarComply = array('No.');
            foreach ($dataCflNotCertGarden[0] as $key => $v) {
                if ($key == 'IMSID' || $key == 'AFLFarmerID' || $key == 'CertProgID' 
                        || $key === "Province" || $key === "District" || $key === "SubDistrict"
                        || $key === "BankName" || $key === "BankAccNumber" || $key === "CertStatusVerified"
                        || $key === "IMSCreator" || $key === "StatusFarmer" || $key === "ReasonStatusFarmer"
                        || $key === "EligibleForAudit" || $key === "AuditRemark" || $key === "CertAuditRemark"
                        || $key === "AuditSummaryStatus") {
                    continue;
                } else {
                    if ($PartnerID == '8') {
                        if ($key === "Birthdate") {
                            continue;
                        }
                    } else {
                        if ($key === "Age") {
                            continue;
                        }
                    }
                }
                $HeaderFarComply[] = $key;
            }
            $rowHeader = WriterEntityFactory::createRowFromArray($HeaderFarComply, $styleHeader);
            $writer->addRow($rowHeader);

            for ($i=0; $i < count($dataCflNotCertGarden); $i++) {
                $dataRows = $dataCflNotCertGarden[$i];
                $cells = array();
                $cells[] = WriterEntityFactory::createCell((int) ($i+1), $styleFormatAngka);
    
                foreach ($dataRows as $key => $v){
                    $styleRow = null;
                    $dataRow = null;
    
                    if ($key == 'IMSID' || $key == 'AFLFarmerID' || $key == 'CertProgID'
                            || $key === "Province" || $key === "District" || $key === "SubDistrict"
                            || $key === "BankName" || $key === "BankAccNumber" || $key === "CertStatusVerified"
                            || $key === "IMSCreator" || $key === "StatusFarmer" || $key === "ReasonStatusFarmer"
                            || $key === "EligibleForAudit" || $key === "AuditRemark" || $key === "CertAuditRemark"
                            || $key === "AuditSummaryStatus") {
                            continue;
                    } else {
                        if ($PartnerID == '8') {
                            if ($key === "Birthdate") {
                            continue;
                            }
                        } else {
                            if ($key === "Age") {
                            continue;
                            }
                        }
                    }
    
                    //cek apakah numeric
                    if(is_numeric($v)){
                        $styleRow = $styleFormatAngka;
                        $dataRow = (float) $v;
                    } else {
                        //cek apakah tanggal
                        if($this->validateDate($v) == true) {
                            $styleRow = $styleFormatTanggal;
                        } else {
                            $styleRow = $styleData;
                        }
                        $dataRow = $v;
                    }
    
                    $cells[] = WriterEntityFactory::createCell($dataRow, $styleRow);
                }
    
                $rowData = WriterEntityFactory::createRow($cells);
                $writer->addRow($rowData);
            }

            /*foreach ($dataCflNotCertGarden as $key => $value) {
                $rowIsi = array();
                foreach ($value as $key => $v){
                    if ($key == 'IMSID' || $key == 'AFLFarmerID' || $key == 'CertProgID'
                            || $key === "Province" || $key === "District" || $key === "SubDistrict"
                            || $key === "BankName" || $key === "BankAccNumber" || $key === "CertStatusVerified"
                            || $key === "IMSCreator" || $key === "StatusFarmer" || $key === "ReasonStatusFarmer"
                            || $key === "EligibleForAudit" || $key === "AuditRemark" || $key === "CertAuditRemark"
                            || $key === "AuditSummaryStatus") {
                            continue;
                    } else {
                        if ($PartnerID == '8') {
                            if ($key === "Birthdate") {
                            continue;
                            }
                        } else {
                            if ($key === "Age") {
                            continue;
                            }
                        }
                    }
                    $rowIsi[] = $v;
                }
                array_unshift($rowIsi, ($k+1));
                $rowData = WriterEntityFactory::createRowFromArray($rowIsi, $styleData);
                $writer->addRow($rowData);
            }*/
        } else{
            $HeaderFarComply = array('No.', 'FarmerID', 'ExtFarmerID', 'FarmerName', 'Gender', 'Birthdate', 'FarmerGroup', 'Village', 'AFLStatus'
                , 'CertGardenNr', 'CertSurveyNr', 'ExternalAuditStatus', 'Reinspection', 'GardenStatus', 'NotActiveStatus', 'PolygonStatus', 'CertLatitude', 'CertLongitude'
                , 'CertFirstYear', 'ICSDate', 'CertHarvest', 'CertNextHarvest', 'CertHectare', 'CertPohonTM', 'CertPohonTBM', 'CertPohonTR', 'AuditComment', 'AuditRecommendationComment');
            $rowHeader = WriterEntityFactory::createRowFromArray($HeaderFarComply, $styleHeader);
            $writer->addRow($rowHeader);
            $cellNoData[0] = WriterEntityFactory::createCell(lang(' - No Data Found - '));
            $rowData = WriterEntityFactory::createRow($cellNoData, $styleHeader);
            $writer->addRow($rowData);            
        }
        //DATA Farmer Garden CFL ========================= (End)
        
        $writer->setCurrentSheet($FarmerCFL);
        $writer->close();
        $this->response(array('success' => true, 'filenya' => base_url() . $filePath), 200);
    }
    
    public function ims_event_detail_cfl_farmer_export_old_get($IMSID)
    {
        //ini_set('display_errors',true);
        //error_reporting(E_ALL);
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $IMSID = (int) $IMSID;

        //data yg diperlukan (begin)
        $dataIms       = $this->mims->imsEventDetailFillForm($IMSID);
        $dataAfl       = $this->mims->cflListExportExcel($IMSID);
        $dataAflGarden = $this->mims->cflListExportExcelGarden($IMSID);

        $dataAflNotCert = $this->mims->cflNotCertListExportExcel($IMSID);
        $dataAflNotCertGarden = $this->mims->cflNotCertListGardenExportExcel($IMSID);
        //data yg diperlukan (end)

        require_once 'application/third_party/PHPExcel18/PHPExcel.php';
        require_once 'application/third_party/PHPExcel18/PHPExcel/IOFactory.php';

        //=============== MULAI TULIS EXCEL (BEGIN) ===================================================================//
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("PT Koltiva")
            ->setLastModifiedBy("PT Koltiva")
            ->setTitle("Farmer AFL")
            ->setSubject("Farmer AFL")
            ->setDescription("Farmer AFL")
            ->setKeywords("Farmer AFL")
            ->setCategory("Farmer AFL");

        //set style  (begin)
        $styleFont = array(
            'font'      => array(
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

        $styleFontBoldMainTitle = array(
            'font'      => array(
                'name' => 'Arial',
                'size' => '11',
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
        );

        $styleFontBoldTitle = array(
            'font'      => array(
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
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '8DB4E3'),
            ),
        );
        $styleFontBoldBgRedCenter = array(
            'font'      => array(
                'name' => 'Arial',
                'size' => '9',
                'bold' => true,
            ),
            'fill'      => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'C0504D'),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $styleBorderFull = array(
            'borders' => array(
                'left'   => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'right'  => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'top'    => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );
        //set style  (end)

        //create sheet
        $objWorkSheetPetani = $objPHPExcel->createSheet(0);
        $objWorkSheetPetani->setTitle('Farmer');

        //logo sertifikasi (begin)
        $progCertImage = imagecreatefromjpeg('images/certification_provider/' . $dataIms['data']['CertProgLogoExportJPG']);
        $objDrawing    = new PHPExcel_Worksheet_MemoryDrawing();
        $objDrawing->setName('Certification Program Logo');
        $objDrawing->setDescription('Certification Program Logo');
        $objDrawing->setImageResource($progCertImage);
        $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
        $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
        $objDrawing->setHeight(85);
        $objDrawing->setCoordinates('A1');
        $objDrawing->setWorksheet($objWorkSheetPetani);
        //logo sertifikasi (end)

        //set width column
        $objWorkSheetPetani->getColumnDimension('B')->setWidth(11);
        $objWorkSheetPetani->getColumnDimension('C')->setWidth(11);
        $objWorkSheetPetani->getColumnDimension('D')->setWidth(28);
        $objWorkSheetPetani->getColumnDimension('E')->setWidth(28);
        $objWorkSheetPetani->getColumnDimension('F')->setWidth(22);
        $objWorkSheetPetani->getColumnDimension('G')->setWidth(8);
        $objWorkSheetPetani->getColumnDimension('H')->setWidth(18);
        $objWorkSheetPetani->getColumnDimension('I')->setWidth(17);
        $objWorkSheetPetani->getColumnDimension('J')->setWidth(25);
        $objWorkSheetPetani->getColumnDimension('K')->setWidth(21);
        $objWorkSheetPetani->getColumnDimension('L')->setWidth(20);
        $objWorkSheetPetani->getColumnDimension('M')->setWidth(15);
        $objWorkSheetPetani->getColumnDimension('N')->setWidth(17);

        //tulis judul & informasi detail lainnya
        $objWorkSheetPetani->setCellValue('C2', lang('Farmer CFL'));
        $objWorkSheetPetani->getStyle('C2')->applyFromArray($styleFontBoldMainTitle);
        $objWorkSheetPetani->mergeCells('C2:E2');

        $objWorkSheetPetani->setCellValue('C3', lang('Holder Name') . ' : ');
        $objWorkSheetPetani->setCellValue('D3', $dataIms['data']['SupplychainLabel']);
        $objWorkSheetPetani->getStyle('C3')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetPetani->getStyle('D3')->applyFromArray($styleFontBoldTitle);

        $objWorkSheetPetani->setCellValue('C4', lang('Certification Year') . ' : ');
        $objWorkSheetPetani->setCellValue('D4', $dataIms['data']['Year'] . ' / ' . ($dataIms['data']['Year'] + 1));
        $objWorkSheetPetani->getStyle('C4')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetPetani->getStyle('D4')->applyFromArray($styleFontBoldTitle);

        $objWorkSheetPetani->setCellValue('C5', lang('Event Name') . ' : ');
        $objWorkSheetPetani->setCellValue('D5', $dataIms['data']['CertEventName']);
        $objWorkSheetPetani->getStyle('C5')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetPetani->getStyle('D5')->applyFromArray($styleFontBoldTitle);

        //Data Tabel ============= (Begin)

        //tabel header
        $objWorkSheetPetani->setCellValue('A8', 'No');
        $objWorkSheetPetani->setCellValue('B8', lang('Farmer ID'));
        $objWorkSheetPetani->setCellValue('C8', lang('Other Farmer ID'));
        $objWorkSheetPetani->setCellValue('D8', lang('Farmer Name'));
        $objWorkSheetPetani->setCellValue('E8', lang('Gender'));

        if($_SESSION['PartnerID'] == '8')
            $objWorkSheetPetani->setCellValue('F8', lang('Age'));
        else
            $objWorkSheetPetani->setCellValue('F8', lang('Birthdate'));


        $objWorkSheetPetani->setCellValue('G8', lang('CPG'));
        $objWorkSheetPetani->setCellValue('H8', lang('Cluster'));
        $objWorkSheetPetani->setCellValue('I8', lang('Location'));
        $objWorkSheetPetani->setCellValue('J8', lang('Status'));
        $objWorkSheetPetani->setCellValue('K8', lang('First Year of Certification'));
        $objWorkSheetPetani->setCellValue('L8', lang('Internal Inspection Date'));
        $objWorkSheetPetani->setCellValue('M8', lang('Estimated Harvest Present Year (Kg)'));
        $objWorkSheetPetani->setCellValue('N8', lang('Previous Year\'s Harvest (Kg)'));
        $objWorkSheetPetani->setCellValue('O8', lang('Total Certified Crop Area (Ha)'));
        $objWorkSheetPetani->setCellValue('P8', lang('No of Plots'));
        $objWorkSheetPetani->setCellValue('Q8', lang('Total Farm Area (Ha)'));

        $objWorkSheetPetani->setCellValue('R8', lang('Total Productive Trees'));
        $objWorkSheetPetani->setCellValue('S8', lang('Total Not Productive Trees'));
        $objWorkSheetPetani->setCellValue('T8', lang('Total Damage Trees'));

        $objWorkSheetPetani->setCellValue('U8', lang('Last year of delivery'));
        $objWorkSheetPetani->setCellValue('V8', lang('Last 2 years of delivery'));
        $objWorkSheetPetani->setCellValue('W8', lang('Last 3 years of delivery'));

        $objWorkSheetPetani->getStyle('A8:W8')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetPetani->getStyle('A8:W8')->applyFromArray($styleBorderFull, false);

        //tabel data
        $rowStart = 9;
        $incre    = 0;
        foreach ($dataAfl as $val) {
            $val['no'] = $incre + 1;

            $objWorkSheetPetani->setCellValue('A' . $rowStart, $val['no']);
            $objWorkSheetPetani->setCellValue('B' . $rowStart, $val['FarmerID']);
            $objWorkSheetPetani->setCellValue('C' . $rowStart, $val['ExtFarmerID']);
            $objWorkSheetPetani->setCellValue('D' . $rowStart, $val['FarmerName']);
            $objWorkSheetPetani->setCellValue('E' . $rowStart, $val['Gender']);

            if($_SESSION['PartnerID'] == '8')
                $objWorkSheetPetani->setCellValue('F' . $rowStart, $val['Age']);
            else
                $objWorkSheetPetani->setCellValue('F' . $rowStart, $val['Birthdate']);

            $objWorkSheetPetani->setCellValue('G' . $rowStart, $val['FarmerGroup']);
            $objWorkSheetPetani->setCellValue('H' . $rowStart, $val['ClusterName']);
            $objWorkSheetPetani->setCellValue('I' . $rowStart, $val['Village']);
            $objWorkSheetPetani->setCellValue('J' . $rowStart, $val['AFLStatus']);
            $objWorkSheetPetani->setCellValue('K' . $rowStart, $val['CertFirstYear']);
            $objWorkSheetPetani->setCellValue('L' . $rowStart, $val['ICSDate']);
            $objWorkSheetPetani->setCellValue('M' . $rowStart, $val['CertNextHarvest']);
            $objWorkSheetPetani->setCellValue('N' . $rowStart, $val['CertHarvest']);
            $objWorkSheetPetani->setCellValue('O' . $rowStart, $val['CertHectare']);
            $objWorkSheetPetani->setCellValue('P' . $rowStart, $val['CertFarmNr']);
            $objWorkSheetPetani->setCellValue('Q' . $rowStart, $val['TotalHa']);

            $objWorkSheetPetani->setCellValue('R' . $rowStart, $val['CertPohonTM']);
            $objWorkSheetPetani->setCellValue('S' . $rowStart, $val['CertPohonTBM']);
            $objWorkSheetPetani->setCellValue('T' . $rowStart, $val['CertPohonTR']);

            $objWorkSheetPetani->setCellValue('U' . $rowStart, $val['SalesLastYear']);
            $objWorkSheetPetani->setCellValue('V' . $rowStart, $val['SalesLast2Years']);
            $objWorkSheetPetani->setCellValue('W' . $rowStart, $val['SalesLast3Years']);

            $objWorkSheetPetani->getStyle('A' . $rowStart . ':' . 'W' . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetPetani->getStyle('A' . $rowStart . ':' . 'W' . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }
        //Data Tabel ============= (End)

        //create sheet
        $objWorkSheetGarden = $objPHPExcel->createSheet(1);
        $objWorkSheetGarden->setTitle('Garden');

        //logo sertifikasi (begin)
        $progCertImage = imagecreatefromjpeg('images/certification_provider/' . $dataIms['data']['CertProgLogoExportJPG']);
        $objDrawing    = new PHPExcel_Worksheet_MemoryDrawing();
        $objDrawing->setName('Certification Program Logo');
        $objDrawing->setDescription('Certification Program Logo');
        $objDrawing->setImageResource($progCertImage);
        $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
        $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
        $objDrawing->setHeight(85);
        $objDrawing->setCoordinates('A1');
        $objDrawing->setWorksheet($objWorkSheetGarden);
        //logo sertifikasi (end)

        //set width column
        $objWorkSheetGarden->getColumnDimension('B')->setWidth(11);
        $objWorkSheetGarden->getColumnDimension('C')->setWidth(11);
        $objWorkSheetGarden->getColumnDimension('D')->setWidth(28);
        $objWorkSheetGarden->getColumnDimension('E')->setWidth(28);
        $objWorkSheetGarden->getColumnDimension('F')->setWidth(22);
        $objWorkSheetGarden->getColumnDimension('G')->setWidth(8);
        $objWorkSheetGarden->getColumnDimension('H')->setWidth(18);
        $objWorkSheetGarden->getColumnDimension('I')->setWidth(17);
        $objWorkSheetGarden->getColumnDimension('J')->setWidth(25);
        $objWorkSheetGarden->getColumnDimension('K')->setWidth(21);
        $objWorkSheetGarden->getColumnDimension('L')->setWidth(20);
        $objWorkSheetGarden->getColumnDimension('M')->setWidth(15);
        $objWorkSheetGarden->getColumnDimension('N')->setWidth(17);

        //tulis judul & informasi detail lainnya
        $objWorkSheetGarden->setCellValue('C2', lang('Farmer Garden AFL'));
        $objWorkSheetGarden->getStyle('C2')->applyFromArray($styleFontBoldMainTitle);
        $objWorkSheetGarden->mergeCells('C2:E2');

        $objWorkSheetGarden->setCellValue('C3', lang('Holder Name') . ' : ');
        $objWorkSheetGarden->setCellValue('D3', $dataIms['data']['SupplychainLabel']);
        $objWorkSheetGarden->getStyle('C3')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetGarden->getStyle('D3')->applyFromArray($styleFontBoldTitle);

        $objWorkSheetGarden->setCellValue('C4', lang('Certification Year') . ' : ');
        $objWorkSheetGarden->setCellValue('D4', $dataIms['data']['Year'] . ' / ' . ($dataIms['data']['Year'] + 1));
        $objWorkSheetGarden->getStyle('C4')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetGarden->getStyle('D4')->applyFromArray($styleFontBoldTitle);

        $objWorkSheetGarden->setCellValue('C5', lang('Event Name') . ' : ');
        $objWorkSheetGarden->setCellValue('D5', $dataIms['data']['CertEventName']);
        $objWorkSheetGarden->getStyle('C5')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetGarden->getStyle('D5')->applyFromArray($styleFontBoldTitle);

        //Data Tabel ============= (Begin)

        //tabel header
        $objWorkSheetGarden->setCellValue('A8', 'No');
        $objWorkSheetGarden->setCellValue('B8', lang('Farmer ID'));
        $objWorkSheetGarden->setCellValue('C8', lang('Other Farmer ID'));
        $objWorkSheetGarden->setCellValue('D8', lang('Farmer Name'));
        $objWorkSheetGarden->setCellValue('E8', lang('Gender'));

        if($_SESSION['PartnerID'] == '8')
            $objWorkSheetGarden->setCellValue('F8', lang('Age'));
        else
            $objWorkSheetGarden->setCellValue('F8', lang('Birthdate'));

        $objWorkSheetGarden->setCellValue('G8', lang('CPG'));
        $objWorkSheetGarden->setCellValue('H8', lang('Location'));
        $objWorkSheetGarden->setCellValue('I8', lang('Status'));
        $objWorkSheetGarden->setCellValue('J8', lang('GardenNr'));
        $objWorkSheetGarden->setCellValue('K8', lang('SurveyNr'));

        $objWorkSheetGarden->setCellValue('L8', lang('Polygon Status'));
        $objWorkSheetGarden->setCellValue('M8', lang('Latitude'));
        $objWorkSheetGarden->setCellValue('N8', lang('Longitude'));

        $objWorkSheetGarden->setCellValue('O8', lang('First Year of Certification'));
        $objWorkSheetGarden->setCellValue('P8', lang('Internal Inspection Date'));
        $objWorkSheetGarden->setCellValue('Q8', lang('Estimated Harvest Present Year (Kg)'));
        $objWorkSheetGarden->setCellValue('R8', lang('Previous Year\'s Harvest (Kg)'));
        $objWorkSheetGarden->setCellValue('S8', lang('Total Certified Crop Area (Ha)'));

        $objWorkSheetGarden->setCellValue('T8', lang('Total Productive Trees'));
        $objWorkSheetGarden->setCellValue('U8', lang('Total Not Productive Trees'));
        $objWorkSheetGarden->setCellValue('V8', lang('Total Damage Trees'));

        $objWorkSheetGarden->setCellValue('W8', lang('Audit Comment'));
        $objWorkSheetGarden->setCellValue('X8', lang('Audit Recommendation Comment'));
        $objWorkSheetGarden->getStyle('A8:X8')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetGarden->getStyle('A8:X8')->applyFromArray($styleBorderFull, false);

        //tabel data
        $rowStart = 9;
        $incre    = 0;
        foreach ($dataAflGarden as $val) {
            $val['no'] = $incre + 1;

            $objWorkSheetGarden->setCellValue('A' . $rowStart, $val['no']);
            $objWorkSheetGarden->setCellValue('B' . $rowStart, $val['FarmerID']);
            $objWorkSheetGarden->setCellValue('C' . $rowStart, $val['ExtFarmerID']);
            $objWorkSheetGarden->setCellValue('D' . $rowStart, $val['FarmerName']);
            $objWorkSheetGarden->setCellValue('E' . $rowStart, $val['Gender']);

            if($_SESSION['PartnerID'] == '8')
                $objWorkSheetGarden->setCellValue('F' . $rowStart, $val['Age']);
            else
                $objWorkSheetGarden->setCellValue('F' . $rowStart, $val['Birthdate']);

            $objWorkSheetGarden->setCellValue('G' . $rowStart, $val['FarmerGroup']);
            $objWorkSheetGarden->setCellValue('H' . $rowStart, $val['Village']);
            $objWorkSheetGarden->setCellValue('I' . $rowStart, $val['AFLStatus']);

            $objWorkSheetGarden->setCellValue('J' . $rowStart, $val['CertGardenNr']);
            $objWorkSheetGarden->setCellValue('K' . $rowStart, $val['CertSurveyNr']);

            $objWorkSheetGarden->setCellValue('L' . $rowStart, $val['PolygonStatus']);
            $objWorkSheetGarden->setCellValue('M' . $rowStart, $val['CertLatitude']);
            $objWorkSheetGarden->setCellValue('N' . $rowStart, $val['CertLongitude']);

            $objWorkSheetGarden->setCellValue('O' . $rowStart, $val['CertFirstYear']);
            $objWorkSheetGarden->setCellValue('P' . $rowStart, $val['ICSDate']);
            $objWorkSheetGarden->setCellValue('Q' . $rowStart, $val['CertNextHarvest']);
            $objWorkSheetGarden->setCellValue('R' . $rowStart, $val['CertHarvest']);
            $objWorkSheetGarden->setCellValue('S' . $rowStart, $val['CertHectare']);

            $objWorkSheetGarden->setCellValue('T' . $rowStart, $val['CertPohonTM']);
            $objWorkSheetGarden->setCellValue('U' . $rowStart, $val['CertPohonTBM']);
            $objWorkSheetGarden->setCellValue('V' . $rowStart, $val['CertPohonTR']);

            $objWorkSheetGarden->setCellValue('W' . $rowStart, $val['AuditComment']);
            $objWorkSheetGarden->setCellValue('X' . $rowStart, $val['AuditRecommendationComment']);

            $objWorkSheetGarden->getStyle('A' . $rowStart . ':' . 'X' . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetGarden->getStyle('A' . $rowStart . ':' . 'X' . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }
        //Data Tabel ============= (End)


        //create sheet
        $objWorkSheetPetaniNonCert = $objPHPExcel->createSheet(2);
        $objWorkSheetPetaniNonCert->setTitle('Farmer (Not Certified)');

        //set width column
        $objWorkSheetPetaniNonCert->getColumnDimension('B')->setWidth(11);
        $objWorkSheetPetaniNonCert->getColumnDimension('C')->setWidth(11);
        $objWorkSheetPetaniNonCert->getColumnDimension('D')->setWidth(28);
        $objWorkSheetPetaniNonCert->getColumnDimension('E')->setWidth(28);
        $objWorkSheetPetaniNonCert->getColumnDimension('F')->setWidth(22);
        $objWorkSheetPetaniNonCert->getColumnDimension('G')->setWidth(8);
        $objWorkSheetPetaniNonCert->getColumnDimension('H')->setWidth(18);
        $objWorkSheetPetaniNonCert->getColumnDimension('I')->setWidth(17);
        $objWorkSheetPetaniNonCert->getColumnDimension('J')->setWidth(25);
        $objWorkSheetPetaniNonCert->getColumnDimension('K')->setWidth(21);
        $objWorkSheetPetaniNonCert->getColumnDimension('L')->setWidth(20);
        $objWorkSheetPetaniNonCert->getColumnDimension('M')->setWidth(15);
        $objWorkSheetPetaniNonCert->getColumnDimension('N')->setWidth(17);

        //tulis judul & informasi detail lainnya
        $objWorkSheetPetaniNonCert->setCellValue('C2', lang('Farmer (Not Certified)'));
        $objWorkSheetPetaniNonCert->getStyle('C2')->applyFromArray($styleFontBoldMainTitle);
        $objWorkSheetPetaniNonCert->mergeCells('C2:E2');

        $objWorkSheetPetaniNonCert->setCellValue('C3', lang('Holder Name') . ' : ');
        $objWorkSheetPetaniNonCert->setCellValue('D3', $dataIms['data']['SupplychainLabel']);
        $objWorkSheetPetaniNonCert->getStyle('C3')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetPetaniNonCert->getStyle('D3')->applyFromArray($styleFontBoldTitle);

        $objWorkSheetPetaniNonCert->setCellValue('C4', lang('Certification Year') . ' : ');
        $objWorkSheetPetaniNonCert->setCellValue('D4', $dataIms['data']['Year'] . ' / ' . ($dataIms['data']['Year'] + 1));
        $objWorkSheetPetaniNonCert->getStyle('C4')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetPetaniNonCert->getStyle('D4')->applyFromArray($styleFontBoldTitle);

        $objWorkSheetPetaniNonCert->setCellValue('C5', lang('Event Name') . ' : ');
        $objWorkSheetPetaniNonCert->setCellValue('D5', $dataIms['data']['CertEventName']);
        $objWorkSheetPetaniNonCert->getStyle('C5')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetPetaniNonCert->getStyle('D5')->applyFromArray($styleFontBoldTitle);

        //Data Tabel ============= (Begin)

        //tabel header
        $objWorkSheetPetaniNonCert->setCellValue('A8', 'No');
        $objWorkSheetPetaniNonCert->setCellValue('B8', lang('Farmer ID'));
        $objWorkSheetPetaniNonCert->setCellValue('C8', lang('Other Farmer ID'));
        $objWorkSheetPetaniNonCert->setCellValue('D8', lang('Farmer Name'));
        $objWorkSheetPetaniNonCert->setCellValue('E8', lang('Gender'));

        if($_SESSION['PartnerID'] == '8')
            $objWorkSheetPetaniNonCert->setCellValue('F8', lang('Age'));
        else
            $objWorkSheetPetaniNonCert->setCellValue('F8', lang('Birthdate'));


        $objWorkSheetPetaniNonCert->setCellValue('G8', lang('CPG'));
        $objWorkSheetPetaniNonCert->setCellValue('H8', lang('Cluster'));
        $objWorkSheetPetaniNonCert->setCellValue('I8', lang('Location'));
        $objWorkSheetPetaniNonCert->setCellValue('J8', lang('Status'));
        $objWorkSheetPetaniNonCert->setCellValue('K8', lang('First Year of Certification'));
        $objWorkSheetPetaniNonCert->setCellValue('L8', lang('Internal Inspection Date'));
        $objWorkSheetPetaniNonCert->setCellValue('M8', lang('Estimated Harvest Present Year (Kg)'));
        $objWorkSheetPetaniNonCert->setCellValue('N8', lang('Previous Year\'s Harvest (Kg)'));
        $objWorkSheetPetaniNonCert->setCellValue('O8', lang('Total Certified Crop Area (Ha)'));
        $objWorkSheetPetaniNonCert->setCellValue('P8', lang('No of Plots'));
        $objWorkSheetPetaniNonCert->setCellValue('Q8', lang('Total Farm Area (Ha)'));

        $objWorkSheetPetaniNonCert->setCellValue('R8', lang('Total Productive Trees'));
        $objWorkSheetPetaniNonCert->setCellValue('S8', lang('Total Not Productive Trees'));
        $objWorkSheetPetaniNonCert->setCellValue('T8', lang('Total Damage Trees'));

        $objWorkSheetPetaniNonCert->setCellValue('U8', lang('Last year of delivery'));
        $objWorkSheetPetaniNonCert->setCellValue('V8', lang('Last 2 years of delivery'));
        $objWorkSheetPetaniNonCert->setCellValue('W8', lang('Last 3 years of delivery'));

        $objWorkSheetPetaniNonCert->getStyle('A8:W8')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetPetaniNonCert->getStyle('A8:W8')->applyFromArray($styleBorderFull, false);

        //tabel data
        $rowStart = 9;
        $incre    = 0;
        foreach ($dataAflNotCert as $val) {
            $val['no'] = $incre + 1;

            $objWorkSheetPetaniNonCert->setCellValue('A' . $rowStart, $val['no']);
            $objWorkSheetPetaniNonCert->setCellValue('B' . $rowStart, $val['FarmerID']);
            $objWorkSheetPetaniNonCert->setCellValue('C' . $rowStart, $val['ExtFarmerID']);
            $objWorkSheetPetaniNonCert->setCellValue('D' . $rowStart, $val['FarmerName']);
            $objWorkSheetPetaniNonCert->setCellValue('E' . $rowStart, $val['Gender']);

            if($_SESSION['PartnerID'] == '8')
                $objWorkSheetPetaniNonCert->setCellValue('F' . $rowStart, $val['Age']);
            else
                $objWorkSheetPetaniNonCert->setCellValue('F' . $rowStart, $val['Birthdate']);

            $objWorkSheetPetaniNonCert->setCellValue('G' . $rowStart, $val['FarmerGroup']);
            $objWorkSheetPetaniNonCert->setCellValue('H' . $rowStart, $val['ClusterName']);
            $objWorkSheetPetaniNonCert->setCellValue('I' . $rowStart, $val['Village']);
            $objWorkSheetPetaniNonCert->setCellValue('J' . $rowStart, $val['AFLStatus']);
            $objWorkSheetPetaniNonCert->setCellValue('K' . $rowStart, $val['CertFirstYear']);
            $objWorkSheetPetaniNonCert->setCellValue('L' . $rowStart, $val['ICSDate']);
            $objWorkSheetPetaniNonCert->setCellValue('M' . $rowStart, $val['CertNextHarvest']);
            $objWorkSheetPetaniNonCert->setCellValue('N' . $rowStart, $val['CertHarvest']);
            $objWorkSheetPetaniNonCert->setCellValue('O' . $rowStart, $val['CertHectare']);
            $objWorkSheetPetaniNonCert->setCellValue('P' . $rowStart, $val['CertFarmNr']);
            $objWorkSheetPetaniNonCert->setCellValue('Q' . $rowStart, $val['TotalHa']);

            $objWorkSheetPetaniNonCert->setCellValue('R' . $rowStart, $val['CertPohonTM']);
            $objWorkSheetPetaniNonCert->setCellValue('S' . $rowStart, $val['CertPohonTBM']);
            $objWorkSheetPetaniNonCert->setCellValue('T' . $rowStart, $val['CertPohonTR']);

            $objWorkSheetPetaniNonCert->setCellValue('U' . $rowStart, $val['SalesLastYear']);
            $objWorkSheetPetaniNonCert->setCellValue('V' . $rowStart, $val['SalesLast2Years']);
            $objWorkSheetPetaniNonCert->setCellValue('W' . $rowStart, $val['SalesLast3Years']);

            $objWorkSheetPetaniNonCert->getStyle('A' . $rowStart . ':' . 'W' . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetPetaniNonCert->getStyle('A' . $rowStart . ':' . 'W' . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }
        //Data Tabel ============= (End)



        //create sheet
        $objWorkSheetGardenNotCert = $objPHPExcel->createSheet(3);
        $objWorkSheetGardenNotCert->setTitle('Garden (Not Certified)');

        //set width column
        $objWorkSheetGardenNotCert->getColumnDimension('B')->setWidth(11);
        $objWorkSheetGardenNotCert->getColumnDimension('C')->setWidth(11);
        $objWorkSheetGardenNotCert->getColumnDimension('D')->setWidth(28);
        $objWorkSheetGardenNotCert->getColumnDimension('E')->setWidth(28);
        $objWorkSheetGardenNotCert->getColumnDimension('F')->setWidth(22);
        $objWorkSheetGardenNotCert->getColumnDimension('G')->setWidth(8);
        $objWorkSheetGardenNotCert->getColumnDimension('H')->setWidth(18);
        $objWorkSheetGardenNotCert->getColumnDimension('I')->setWidth(17);
        $objWorkSheetGardenNotCert->getColumnDimension('J')->setWidth(25);
        $objWorkSheetGardenNotCert->getColumnDimension('K')->setWidth(21);
        $objWorkSheetGardenNotCert->getColumnDimension('L')->setWidth(20);
        $objWorkSheetGardenNotCert->getColumnDimension('M')->setWidth(15);
        $objWorkSheetGardenNotCert->getColumnDimension('N')->setWidth(17);

        //tulis judul & informasi detail lainnya
        $objWorkSheetGardenNotCert->setCellValue('C2', lang('Farmer Garden (Not Certified)'));
        $objWorkSheetGardenNotCert->getStyle('C2')->applyFromArray($styleFontBoldMainTitle);
        $objWorkSheetGardenNotCert->mergeCells('C2:E2');

        $objWorkSheetGardenNotCert->setCellValue('C3', lang('Holder Name') . ' : ');
        $objWorkSheetGardenNotCert->setCellValue('D3', $dataIms['data']['SupplychainLabel']);
        $objWorkSheetGardenNotCert->getStyle('C3')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetGardenNotCert->getStyle('D3')->applyFromArray($styleFontBoldTitle);

        $objWorkSheetGardenNotCert->setCellValue('C4', lang('Certification Year') . ' : ');
        $objWorkSheetGardenNotCert->setCellValue('D4', $dataIms['data']['Year'] . ' / ' . ($dataIms['data']['Year'] + 1));
        $objWorkSheetGardenNotCert->getStyle('C4')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetGardenNotCert->getStyle('D4')->applyFromArray($styleFontBoldTitle);

        $objWorkSheetGardenNotCert->setCellValue('C5', lang('Event Name') . ' : ');
        $objWorkSheetGardenNotCert->setCellValue('D5', $dataIms['data']['CertEventName']);
        $objWorkSheetGardenNotCert->getStyle('C5')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetGardenNotCert->getStyle('D5')->applyFromArray($styleFontBoldTitle);

        //Data Tabel ============= (Begin)

        //tabel header
        $objWorkSheetGardenNotCert->setCellValue('A8', 'No');
        $objWorkSheetGardenNotCert->setCellValue('B8', lang('Farmer ID'));
        $objWorkSheetGardenNotCert->setCellValue('C8', lang('Other Farmer ID'));
        $objWorkSheetGardenNotCert->setCellValue('D8', lang('Farmer Name'));
        $objWorkSheetGardenNotCert->setCellValue('E8', lang('Gender'));

        if($_SESSION['PartnerID'] == '8')
            $objWorkSheetGardenNotCert->setCellValue('F8', lang('Age'));
        else
            $objWorkSheetGardenNotCert->setCellValue('F8', lang('Birthdate'));


        $objWorkSheetGardenNotCert->setCellValue('G8', lang('CPG'));
        $objWorkSheetGardenNotCert->setCellValue('H8', lang('Location'));
        $objWorkSheetGardenNotCert->setCellValue('I8', lang('Status'));
        $objWorkSheetGardenNotCert->setCellValue('J8', lang('GardenNr'));
        $objWorkSheetGardenNotCert->setCellValue('K8', lang('SurveyNr'));

        $objWorkSheetGardenNotCert->setCellValue('L8', lang('Polygon Status'));
        $objWorkSheetGardenNotCert->setCellValue('M8', lang('Latitude'));
        $objWorkSheetGardenNotCert->setCellValue('N8', lang('Longitude'));

        $objWorkSheetGardenNotCert->setCellValue('O8', lang('First Year of Certification'));
        $objWorkSheetGardenNotCert->setCellValue('P8', lang('Internal Inspection Date'));
        $objWorkSheetGardenNotCert->setCellValue('Q8', lang('Estimated Harvest Present Year (Kg)'));
        $objWorkSheetGardenNotCert->setCellValue('R8', lang('Previous Year\'s Harvest (Kg)'));
        $objWorkSheetGardenNotCert->setCellValue('S8', lang('Total Certified Crop Area (Ha)'));

        $objWorkSheetGardenNotCert->setCellValue('T8', lang('Total Productive Trees'));
        $objWorkSheetGardenNotCert->setCellValue('U8', lang('Total Not Productive Trees'));
        $objWorkSheetGardenNotCert->setCellValue('V8', lang('Total Damage Trees'));

        $objWorkSheetGardenNotCert->setCellValue('W8', lang('Audit Comment'));
        $objWorkSheetGardenNotCert->setCellValue('X8', lang('Audit Recommendation Comment'));
        $objWorkSheetGardenNotCert->getStyle('A8:X8')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetGardenNotCert->getStyle('A8:X8')->applyFromArray($styleBorderFull, false);

        //tabel data
        $rowStart = 9;
        $incre    = 0;
        foreach ($dataAflNotCertGarden as $val) {
            $val['no'] = $incre + 1;

            $objWorkSheetGardenNotCert->setCellValue('A' . $rowStart, $val['no']);
            $objWorkSheetGardenNotCert->setCellValue('B' . $rowStart, $val['FarmerID']);
            $objWorkSheetGardenNotCert->setCellValue('C' . $rowStart, $val['ExtFarmerID']);
            $objWorkSheetGardenNotCert->setCellValue('D' . $rowStart, $val['FarmerName']);
            $objWorkSheetGardenNotCert->setCellValue('E' . $rowStart, $val['Gender']);

            if($_SESSION['PartnerID'] == '8')
                $objWorkSheetGardenNotCert->setCellValue('F' . $rowStart, $val['Age']);
            else
                $objWorkSheetGardenNotCert->setCellValue('F' . $rowStart, $val['Birthdate']);


            $objWorkSheetGardenNotCert->setCellValue('G' . $rowStart, $val['FarmerGroup']);
            $objWorkSheetGardenNotCert->setCellValue('H' . $rowStart, $val['Village']);
            $objWorkSheetGardenNotCert->setCellValue('I' . $rowStart, $val['AFLStatus']);

            $objWorkSheetGardenNotCert->setCellValue('J' . $rowStart, $val['CertGardenNr']);
            $objWorkSheetGardenNotCert->setCellValue('K' . $rowStart, $val['CertSurveyNr']);

            $objWorkSheetGardenNotCert->setCellValue('L' . $rowStart, $val['PolygonStatus']);
            $objWorkSheetGardenNotCert->setCellValue('M' . $rowStart, $val['CertLatitude']);
            $objWorkSheetGardenNotCert->setCellValue('N' . $rowStart, $val['CertLongitude']);

            $objWorkSheetGardenNotCert->setCellValue('O' . $rowStart, $val['CertFirstYear']);
            $objWorkSheetGardenNotCert->setCellValue('P' . $rowStart, $val['ICSDate']);
            $objWorkSheetGardenNotCert->setCellValue('Q' . $rowStart, $val['CertNextHarvest']);
            $objWorkSheetGardenNotCert->setCellValue('R' . $rowStart, $val['CertHarvest']);
            $objWorkSheetGardenNotCert->setCellValue('S' . $rowStart, $val['CertHectare']);

            $objWorkSheetGardenNotCert->setCellValue('T' . $rowStart, $val['CertPohonTM']);
            $objWorkSheetGardenNotCert->setCellValue('U' . $rowStart, $val['CertPohonTBM']);
            $objWorkSheetGardenNotCert->setCellValue('V' . $rowStart, $val['CertPohonTR']);

            $objWorkSheetGardenNotCert->setCellValue('W' . $rowStart, $val['AuditComment']);
            $objWorkSheetGardenNotCert->setCellValue('X' . $rowStart, $val['AuditRecommendationComment']);

            $objWorkSheetGardenNotCert->getStyle('A' . $rowStart . ':' . 'X' . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetGardenNotCert->getStyle('A' . $rowStart . ':' . 'X' . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }
        //Data Tabel ============= (End)


        //=============== MULAI TULIS EXCEL (END) =====================================================================//

        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . date('YmdHis') . '_FarmerCFL.xlsx');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function ims_event_detail_afl_final_farmer_export_get($IMSID, $ICSStatus) {
        ini_set('display_errors', true);
        error_reporting(E_ALL);
        ini_set('memory_limit', '-1');

        $IMSID = (int) $IMSID;
        $ICSStatus = (int) $ICSStatus;
        $PartnerID = $_SESSION['PartnerID'];
        
        //data yg diperlukan (begin)
        $dataIms = $this->mims->imsEventDetailFillForm($IMSID);
        
        if ($ICSStatus == 2) {
            $dataAfl       = array();
            $dataAflGarden = array();
        } else {
            $dataAfl       = $this->mims->aflListExportExcel($IMSID, 'Comply');
            $dataAflGarden = $this->mims->aflListExportExcelGarden($IMSID, 'Comply');
        }
        //data yg diperlukan (end)
        
        $writer = WriterEntityFactory::createXLSXWriter(); // for XLSX files
        //$writer = WriterFactory::create(Type::CSV); // for CSV files
        //$writer = WriterFactory::create(Type::ODS); // for ODS files

        $writer->setTempFolder('files/sql_view_temp/');
        $namaFile = date('YmdHis') . '_Farmer_AFL';
        $filePath = 'files/sql_view/'.$namaFile.'.xlsx';
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

        //style data
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
        
        $empty[] = WriterEntityFactory::createCell('');
        $EmptyRows = WriterEntityFactory::createRow($empty, null);
        //DATA Farmer AFL Comply ========================= (Begin)
        
        $FarmerComply = $writer->getCurrentSheet()->setName(lang('Farmer'));
        $cells1[0] = WriterEntityFactory::createCell(lang('Farmer AFL'));
        $rowData = WriterEntityFactory::createRow($cells1, null);
        $writer->addRow($rowData);
        $cells2[0] = WriterEntityFactory::createCell(lang('Holder Name'));
        $cells2[1] = WriterEntityFactory::createCell($dataIms['data']['SupplychainLabel']);
        $rowData = WriterEntityFactory::createRow($cells2, null);
        $writer->addRow($rowData);
        $cells3[0] = WriterEntityFactory::createCell(lang('Certification Year'));
        $cells3[1] = WriterEntityFactory::createCell($dataIms['data']['Year'] . '/' . ((int)$dataIms['data']['Year']+1));
        $rowData = WriterEntityFactory::createRow($cells3, null);
        $writer->addRow($rowData);
        $cells4[0] = WriterEntityFactory::createCell(lang('Event name'));
        $cells4[1] = WriterEntityFactory::createCell($dataIms['data']['CertEventName']);
        $rowData = WriterEntityFactory::createRow($cells4, null);
        $writer->addRow($rowData);
        $writer->addRow($EmptyRows);
        $writer->addRow($EmptyRows);

        if ($dataAfl){
            $HeaderFarComply = array('No.');
            foreach ($dataAfl[0] as $key => $v) {
                if ($key == 'IMSID' || $key == 'AFLFarmerID' || $key == 'CertProgID' 
                        || $key === "Province" || $key === "District" || $key === "SubDistrict"
                        || $key === "BankName" || $key === "BankAccNumber" || $key === "CertStatusVerified"
                        || $key === "IMSCreator" || $key === "StatusFarmer" || $key === "ReasonStatusFarmer"
                        || $key === "EligibleForAudit" || $key === "AuditRemark" || $key === "CertAuditRemark"
                        || $key === "AuditSummaryStatus") {
                    continue;
                } else {
                    if ($PartnerID == '8') {
                        if ($key === "Birthdate") {
                            continue;
                        }
                    } else {
                        if ($key === "Age") {
                            continue;
                        }
                    }
                }
                $HeaderFarComply[] = $key;
            }
            $rowHeader = WriterEntityFactory::createRowFromArray($HeaderFarComply, $styleHeader);
            $writer->addRow($rowHeader);

            /*foreach ($dataAfl as $k => $value) {
                $rowIsi = array();
                foreach ($value as $key => $v){
                    if ($key == 'IMSID' || $key == 'AFLFarmerID' || $key == 'CertProgID'
                            || $key === "Province" || $key === "District" || $key === "SubDistrict"
                            || $key === "BankName" || $key === "BankAccNumber" || $key === "CertStatusVerified"
                            || $key === "IMSCreator" || $key === "StatusFarmer" || $key === "ReasonStatusFarmer"
                            || $key === "EligibleForAudit" || $key === "AuditRemark" || $key === "CertAuditRemark"
                            || $key === "AuditSummaryStatus") {
                            continue;
                    } else {
                        if ($PartnerID == '8') {
                            if ($key === "Birthdate") {
                            continue;
                            }
                        } else {
                            if ($key === "Age") {
                            continue;
                            }
                        }
                    }
                    $rowIsi[] = $v;
                }
                array_unshift($rowIsi, ($k+1));
                $rowData = WriterEntityFactory::createRowFromArray($rowIsi, $styleData);
                $writer->addRow($rowData);
            }*/

            for ($i=0; $i < count($dataAfl); $i++) {
                $dataRows = $dataAfl[$i];
                $cells = array();
                $cells[] = WriterEntityFactory::createCell((int) ($i+1), $styleFormatAngka);
    
                foreach ($dataRows as $key => $v){
                    $styleRow = null;
                    $dataRow = null;
    
                    if ($key == 'IMSID' || $key == 'AFLFarmerID' || $key == 'CertProgID'
                            || $key === "Province" || $key === "District" || $key === "SubDistrict"
                            || $key === "BankName" || $key === "BankAccNumber" || $key === "CertStatusVerified"
                            || $key === "IMSCreator" || $key === "StatusFarmer" || $key === "ReasonStatusFarmer"
                            || $key === "EligibleForAudit" || $key === "AuditRemark" || $key === "CertAuditRemark"
                            || $key === "AuditSummaryStatus") {
                            continue;
                    } else {
                        if ($PartnerID == '8') {
                            if ($key === "Birthdate") {
                            continue;
                            }
                        } else {
                            if ($key === "Age") {
                            continue;
                            }
                        }
                    }
    
                    //cek apakah numeric
                    if(is_numeric($v)){
                        $styleRow = $styleFormatAngka;
                        $dataRow = (float) $v;
                    } else {
                        //cek apakah tanggal
                        if($this->validateDate($v) == true) {
                            $styleRow = $styleFormatTanggal;
                        } else {
                            $styleRow = $styleData;
                        }
                        $dataRow = $v;
                    }
    
                    $cells[] = WriterEntityFactory::createCell($dataRow, $styleRow);
                }
    
                $rowData = WriterEntityFactory::createRow($cells);
                $writer->addRow($rowData);
            }

        } else {
            $HeaderFarComply = array('No.', 'FarmerID', 'ExtFarmerID', 'FarmerName', 'Gender', 'Birthdate', 'FarmerGroup', 'ClusterName', 'Village', 'AFLStatus'
                , 'ExternalAuditStatus', 'CertFirstYear', 'CertHarvest', 'CertNextHarvest', 'CertHectare', 'CertFarmNr', 'ICSDate', 'TotalHa', 'CertPohonTM'
                , 'CertPohonTBM', 'CertPohonTR', 'SalesLastYear', 'SalesLast2Years', 'SalesLast3Years');
            $rowHeader = WriterEntityFactory::createRowFromArray($HeaderFarComply, $styleHeader);
            $writer->addRow($rowHeader);
            $cellNoData[0] = WriterEntityFactory::createCell(lang(' - No Data Found - '));
            $rowData = WriterEntityFactory::createRow($cellNoData, $styleHeader);
            $writer->addRow($rowData);
        }
//        $this->response($dataPetani, 200);
        //DATA Farmer AFL Comply ========================= (End)
        
        //DATA Farmer Garden AFL ========================= (Begin)        
        $Garden = $writer->addNewSheetAndMakeItCurrent()->setName(lang('Garden'));
        $cells1[0] = WriterEntityFactory::createCell(lang('Farmer Garden AFL'));
        $rowData = WriterEntityFactory::createRow($cells1, null);
        $writer->addRow($rowData);
        $cells2[0] = WriterEntityFactory::createCell(lang('Holder Name'));
        $cells2[1] = WriterEntityFactory::createCell($dataIms['data']['SupplychainLabel']);
        $rowData = WriterEntityFactory::createRow($cells2, null);
        $writer->addRow($rowData);
        $cells3[0] = WriterEntityFactory::createCell(lang('Certification Year'));
        $cells3[1] = WriterEntityFactory::createCell($dataIms['data']['Year'] . '/' . ((int)$dataIms['data']['Year']+1));
        $rowData = WriterEntityFactory::createRow($cells3, null);
        $writer->addRow($rowData);
        $cells4[0] = WriterEntityFactory::createCell(lang('Event name'));
        $cells4[1] = WriterEntityFactory::createCell($dataIms['data']['CertEventName']);
        $rowData = WriterEntityFactory::createRow($cells4, null);
        $writer->addRow($rowData);
        $writer->addRow($EmptyRows);
        $writer->addRow($EmptyRows);
        if ($dataAflGarden['size'] > 0){
            $HeaderGarden = array('No.');
            foreach ($dataAflGarden['data'][0] as $key => $v) {
                if ($key == 'IMSID' || $key == 'AFLFarmerID' || $key == 'CertProgID'
                    || $key === "Province" || $key === "District" || $key === "SubDistrict"
                    || $key === "BankName" || $key === "BankAccNumber" || $key === "CertStatusVerified"
                    || $key === "IMSCreator" || $key === "StatusFarmer" || $key === "ReasonStatusFarmer"
                    || $key === "EligibleForAudit" || $key === "AuditRemark" || $key === "CertAuditRemark"
                    || $key === "AuditSummaryStatus") {
                    continue;
                } else {
                    if ($PartnerID == '8') {
                        if ($key === "Birthdate") {
                            continue;
                        }
                    } else {
                        if ($key === "Age") {
                            continue;
                        }
                    }
                }
                $HeaderGarden[] = $key;
            }
            $rowHeader = WriterEntityFactory::createRowFromArray($HeaderGarden, $styleHeader);
            $writer->addRow($rowHeader);

            /*foreach ($dataAflGarden['data'] as $k => $value) {
                $rowIsi = array();
                foreach ($value as $key => $v){
                    if ($key == 'IMSID' || $key == 'AFLFarmerID' || $key == 'CertProgID'
                            || $key === "Province" || $key === "District" || $key === "SubDistrict"
                            || $key === "BankName" || $key === "BankAccNumber" || $key === "CertStatusVerified"
                            || $key === "IMSCreator" || $key === "StatusFarmer" || $key === "ReasonStatusFarmer"
                            || $key === "EligibleForAudit" || $key === "AuditRemark" || $key === "CertAuditRemark"
                            || $key === "AuditSummaryStatus") {
                            continue;
                    } else {
                        if ($PartnerID == '8') {
                            if ($key === "Birthdate") {
                            continue;
                            }
                        } else {
                            if ($key === "Age") {
                            continue;
                            }
                        }
                    }
                    $rowIsi[] = $v;
                }
                array_unshift($rowIsi, ($k+1));
                $rowData = WriterEntityFactory::createRowFromArray($rowIsi, $styleData);
                $writer->addRow($rowData);
            }*/

            for ($i=0; $i < count($dataAflGarden['data']); $i++) {
                $dataRows = $dataAflGarden['data'][$i];
                $cells = array();
                $cells[] = WriterEntityFactory::createCell((int) ($i+1), $styleFormatAngka);
    
                foreach ($dataRows as $key => $v){
                    $styleRow = null;
                    $dataRow = null;
    
                    if ($key == 'IMSID' || $key == 'AFLFarmerID' || $key == 'CertProgID'
                            || $key === "Province" || $key === "District" || $key === "SubDistrict"
                            || $key === "BankName" || $key === "BankAccNumber" || $key === "CertStatusVerified"
                            || $key === "IMSCreator" || $key === "StatusFarmer" || $key === "ReasonStatusFarmer"
                            || $key === "EligibleForAudit" || $key === "AuditRemark" || $key === "CertAuditRemark"
                            || $key === "AuditSummaryStatus") {
                            continue;
                    } else {
                        if ($PartnerID == '8') {
                            if ($key === "Birthdate") {
                            continue;
                            }
                        } else {
                            if ($key === "Age") {
                            continue;
                            }
                        }
                    }
    
                    //cek apakah numeric
                    if(is_numeric($v)){
                        $styleRow = $styleFormatAngka;
                        $dataRow = (float) $v;
                    } else {
                        //cek apakah tanggal
                        if($this->validateDate($v) == true) {
                            $styleRow = $styleFormatTanggal;
                        } else {
                            $styleRow = $styleData;
                        }
                        $dataRow = $v;
                    }
    
                    $cells[] = WriterEntityFactory::createCell($dataRow, $styleRow);
                }
    
                $rowData = WriterEntityFactory::createRow($cells);
                $writer->addRow($rowData);
            }

        } else {
            $HeaderFarComply = array('No.', 'FarmerID', 'ExtFarmerID', 'FarmerName', 'Gender', 'Birthdate', 'FarmerGroup', 'Village', 'AFLStatus'
                , 'CertGardenNr', 'CertSurveyNr', 'ExternalAuditStatus', 'Reinspection', 'GardenStatus', 'NotActiveStatus', 'PolygonStatus', 'CertLatitude', 'CertLongitude'
                , 'CertFirstYear', 'ICSDate', 'CertHarvest', 'CertNextHarvest', 'CertHectare', 'CertPohonTM', 'CertPohonTBM', 'CertPohonTR', 'AuditComment', 'AuditRecommendationComment');
            $rowHeader = WriterEntityFactory::createRowFromArray($HeaderFarComply, $styleHeader);
            $writer->addRow($rowHeader);
            $cellNoData[0] = WriterEntityFactory::createCell(lang(' - No Data Found - '));
            $rowData = WriterEntityFactory::createRow($cellNoData, $styleHeader);
            $writer->addRow($rowData);
        }
        //DATA Farmer Garden AFL ========================= (End)
        
        $writer->setCurrentSheet($FarmerComply);
        $writer->close();
        $this->response(array('success' => true, 'filenya' => base_url() . $filePath), 200);
    }

    public function ims_event_detail_afl_final_farmer_export_old_get($IMSID, $ICSStatus)
    {
        // ini_set('display_errors',true);
        // error_reporting(E_ALL);
        ini_set('memory_limit', '-1');

        $IMSID     = (int) $IMSID;
        $ICSStatus = (int) $ICSStatus;

        //data yg diperlukan (begin)
        $dataIms = $this->mims->imsEventDetailFillForm($IMSID);

        if ($ICSStatus == 2) {
            $dataAfl       = array();
            $dataAflGarden = array();
        } else {
            $dataAfl       = $this->mims->aflListExportExcel($IMSID, 'Comply');
            $dataAflGarden = $this->mims->aflListExportExcelGarden($IMSID, 'Comply')['data'];
        }
        //data yg diperlukan (end)

        require_once 'application/third_party/PHPExcel18/PHPExcel.php';
        require_once 'application/third_party/PHPExcel18/PHPExcel/IOFactory.php';

        //=============== MULAI TULIS EXCEL (BEGIN) ===================================================================//
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("PT Koltiva")
            ->setLastModifiedBy("PT Koltiva")
            ->setTitle("Farmer AFL")
            ->setSubject("Farmer AFL")
            ->setDescription("Farmer AFL")
            ->setKeywords("Farmer AFL")
            ->setCategory("Farmer AFL");

        //set style  (begin)
        $styleFont = array(
            'font'      => array(
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

        $styleFontBoldMainTitle = array(
            'font'      => array(
                'name' => 'Arial',
                'size' => '11',
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
        );

        $styleFontBoldTitle = array(
            'font'      => array(
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
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '8DB4E3'),
            ),
        );
        $styleFontBoldBgRedCenter = array(
            'font'      => array(
                'name' => 'Arial',
                'size' => '9',
                'bold' => true,
            ),
            'fill'      => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'C0504D'),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $styleBorderFull = array(
            'borders' => array(
                'left'   => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'right'  => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'top'    => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );
        //set style  (end)

        //create sheet
        $objWorkSheetPetani = $objPHPExcel->createSheet(0);
        $objWorkSheetPetani->setTitle('Farmer');

        //logo sertifikasi (begin)
        $progCertImage = imagecreatefromjpeg('images/certification_provider/' . $dataIms['data']['CertProgLogoExportJPG']);
        $objDrawing    = new PHPExcel_Worksheet_MemoryDrawing();
        $objDrawing->setName('Certification Program Logo');
        $objDrawing->setDescription('Certification Program Logo');
        $objDrawing->setImageResource($progCertImage);
        $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
        $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
        $objDrawing->setHeight(85);
        $objDrawing->setCoordinates('A1');
        $objDrawing->setWorksheet($objWorkSheetPetani);
        //logo sertifikasi (end)

        //set width column
        $objWorkSheetPetani->getColumnDimension('B')->setWidth(11);
        $objWorkSheetPetani->getColumnDimension('C')->setWidth(11);
        $objWorkSheetPetani->getColumnDimension('D')->setWidth(28);
        $objWorkSheetPetani->getColumnDimension('E')->setWidth(28);
        $objWorkSheetPetani->getColumnDimension('F')->setWidth(22);
        $objWorkSheetPetani->getColumnDimension('G')->setWidth(8);
        $objWorkSheetPetani->getColumnDimension('H')->setWidth(18);
        $objWorkSheetPetani->getColumnDimension('I')->setWidth(17);
        $objWorkSheetPetani->getColumnDimension('J')->setWidth(25);
        $objWorkSheetPetani->getColumnDimension('K')->setWidth(21);
        $objWorkSheetPetani->getColumnDimension('L')->setWidth(20);
        $objWorkSheetPetani->getColumnDimension('M')->setWidth(15);
        $objWorkSheetPetani->getColumnDimension('N')->setWidth(17);

        //tulis judul & informasi detail lainnya
        $objWorkSheetPetani->setCellValue('C2', lang('Farmer AFL'));
        $objWorkSheetPetani->getStyle('C2')->applyFromArray($styleFontBoldMainTitle);
        $objWorkSheetPetani->mergeCells('C2:E2');

        $objWorkSheetPetani->setCellValue('C3', lang('Holder Name') . ' : ');
        $objWorkSheetPetani->setCellValue('D3', $dataIms['data']['SupplychainLabel']);
        $objWorkSheetPetani->getStyle('C3')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetPetani->getStyle('D3')->applyFromArray($styleFontBoldTitle);

        $objWorkSheetPetani->setCellValue('C4', lang('Certification Year') . ' : ');
        $objWorkSheetPetani->setCellValue('D4', $dataIms['data']['Year'] . ' / ' . ($dataIms['data']['Year'] + 1));
        $objWorkSheetPetani->getStyle('C4')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetPetani->getStyle('D4')->applyFromArray($styleFontBoldTitle);

        $objWorkSheetPetani->setCellValue('C5', lang('Event Name') . ' : ');
        $objWorkSheetPetani->setCellValue('D5', $dataIms['data']['CertEventName']);
        $objWorkSheetPetani->getStyle('C5')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetPetani->getStyle('D5')->applyFromArray($styleFontBoldTitle);

        //Data Tabel ============= (Begin)

        //tabel header
        $objWorkSheetPetani->setCellValue('A8', 'No');
        $objWorkSheetPetani->setCellValue('B8', lang('Farmer ID'));
        $objWorkSheetPetani->setCellValue('C8', lang('Other Farmer ID'));
        $objWorkSheetPetani->setCellValue('D8', lang('Farmer Name'));
        $objWorkSheetPetani->setCellValue('E8', lang('Gender'));

        if($_SESSION['PartnerID'] == '8')
            $objWorkSheetPetani->setCellValue('F8', lang('Age'));
        else
            $objWorkSheetPetani->setCellValue('F8', lang('Birthdate'));

        $objWorkSheetPetani->setCellValue('G8', lang('CPG'));
        $objWorkSheetPetani->setCellValue('H8', lang('Cluster'));
        $objWorkSheetPetani->setCellValue('I8', lang('Location'));
        $objWorkSheetPetani->setCellValue('J8', lang('Status'));
        $objWorkSheetPetani->setCellValue('K8', lang('First Year of Certification'));
        $objWorkSheetPetani->setCellValue('L8', lang('Internal Inspection Date'));
        $objWorkSheetPetani->setCellValue('M8', lang('Estimated Harvest Present Year (Kg)'));
        $objWorkSheetPetani->setCellValue('N8', lang('Previous Year\'s Harvest (Kg)'));
        $objWorkSheetPetani->setCellValue('O8', lang('Total Certified Crop Area (Ha)'));
        $objWorkSheetPetani->setCellValue('P8', lang('No of Plots'));
        $objWorkSheetPetani->setCellValue('Q8', lang('Total Farm Area (Ha)'));
        $objWorkSheetPetani->setCellValue('R8', lang('Total Productive Trees'));
        $objWorkSheetPetani->setCellValue('S8', lang('Total Not Productive Trees'));
        $objWorkSheetPetani->setCellValue('T8', lang('Total Damage Trees'));

        $objWorkSheetPetani->setCellValue('U8', lang('Last year of delivery'));
        $objWorkSheetPetani->setCellValue('V8', lang('Last 2 years of delivery'));
        $objWorkSheetPetani->setCellValue('W8', lang('Last 3 years of delivery'));

        $objWorkSheetPetani->getStyle('A8:W8')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetPetani->getStyle('A8:W8')->applyFromArray($styleBorderFull, false);

        //tabel data
        $rowStart = 9;
        $incre    = 0;
        foreach ($dataAfl as $val) {
            $val['no'] = $incre + 1;

            $objWorkSheetPetani->setCellValue('A' . $rowStart, $val['no']);
            $objWorkSheetPetani->setCellValue('B' . $rowStart, $val['FarmerID']);
            $objWorkSheetPetani->setCellValue('C' . $rowStart, $val['ExtFarmerID']);
            $objWorkSheetPetani->setCellValue('D' . $rowStart, $val['FarmerName']);
            $objWorkSheetPetani->setCellValue('E' . $rowStart, $val['Gender']);

            if($_SESSION['PartnerID'] == '8')
                $objWorkSheetPetani->setCellValue('F' . $rowStart, $val['Age']);
            else
                $objWorkSheetPetani->setCellValue('F' . $rowStart, $val['Birthdate']);

            $objWorkSheetPetani->setCellValue('G' . $rowStart, $val['FarmerGroup']);
            $objWorkSheetPetani->setCellValue('H' . $rowStart, $val['ClusterName']);
            $objWorkSheetPetani->setCellValue('I' . $rowStart, $val['Village']);
            $objWorkSheetPetani->setCellValue('J' . $rowStart, $val['AFLStatus']);
            $objWorkSheetPetani->setCellValue('K' . $rowStart, $val['CertFirstYear']);
            $objWorkSheetPetani->setCellValue('L' . $rowStart, $val['ICSDate']);
            $objWorkSheetPetani->setCellValue('M' . $rowStart, $val['CertNextHarvest']);
            $objWorkSheetPetani->setCellValue('N' . $rowStart, $val['CertHarvest']);
            $objWorkSheetPetani->setCellValue('O' . $rowStart, $val['CertHectare']);
            $objWorkSheetPetani->setCellValue('P' . $rowStart, $val['CertFarmNr']);
            $objWorkSheetPetani->setCellValue('Q' . $rowStart, $val['TotalHa']);
            $objWorkSheetPetani->setCellValue('R' . $rowStart, $val['CertPohonTM']);
            $objWorkSheetPetani->setCellValue('S' . $rowStart, $val['CertPohonTBM']);
            $objWorkSheetPetani->setCellValue('T' . $rowStart, $val['CertPohonTR']);

            $objWorkSheetPetani->setCellValue('U' . $rowStart, $val['SalesLastYear']);
            $objWorkSheetPetani->setCellValue('V' . $rowStart, $val['SalesLast2Years']);
            $objWorkSheetPetani->setCellValue('W' . $rowStart, $val['SalesLast3Years']);

            $objWorkSheetPetani->getStyle('A' . $rowStart . ':' . 'W' . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetPetani->getStyle('A' . $rowStart . ':' . 'W' . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }
        //Data Tabel ============= (End)

        //create sheet
        $objWorkSheetGarden = $objPHPExcel->createSheet(1);
        $objWorkSheetGarden->setTitle('Garden');

        //logo sertifikasi (begin)
        $progCertImage = imagecreatefromjpeg('images/certification_provider/' . $dataIms['data']['CertProgLogoExportJPG']);
        $objDrawing    = new PHPExcel_Worksheet_MemoryDrawing();
        $objDrawing->setName('Certification Program Logo');
        $objDrawing->setDescription('Certification Program Logo');
        $objDrawing->setImageResource($progCertImage);
        $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
        $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
        $objDrawing->setHeight(85);
        $objDrawing->setCoordinates('A1');
        $objDrawing->setWorksheet($objWorkSheetGarden);
        //logo sertifikasi (end)

        //set width column
        $objWorkSheetGarden->getColumnDimension('B')->setWidth(11);
        $objWorkSheetGarden->getColumnDimension('C')->setWidth(11);
        $objWorkSheetGarden->getColumnDimension('D')->setWidth(28);
        $objWorkSheetGarden->getColumnDimension('E')->setWidth(28);
        $objWorkSheetGarden->getColumnDimension('F')->setWidth(22);
        $objWorkSheetGarden->getColumnDimension('G')->setWidth(8);
        $objWorkSheetGarden->getColumnDimension('H')->setWidth(18);
        $objWorkSheetGarden->getColumnDimension('I')->setWidth(17);
        $objWorkSheetGarden->getColumnDimension('J')->setWidth(25);
        $objWorkSheetGarden->getColumnDimension('K')->setWidth(21);
        $objWorkSheetGarden->getColumnDimension('L')->setWidth(20);
        $objWorkSheetGarden->getColumnDimension('M')->setWidth(15);
        $objWorkSheetGarden->getColumnDimension('N')->setWidth(17);

        //tulis judul & informasi detail lainnya
        $objWorkSheetGarden->setCellValue('C2', lang('Farmer Garden AFL'));
        $objWorkSheetGarden->getStyle('C2')->applyFromArray($styleFontBoldMainTitle);
        $objWorkSheetGarden->mergeCells('C2:E2');

        $objWorkSheetGarden->setCellValue('C3', lang('Holder Name') . ' : ');
        $objWorkSheetGarden->setCellValue('D3', $dataIms['data']['SupplychainLabel']);
        $objWorkSheetGarden->getStyle('C3')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetGarden->getStyle('D3')->applyFromArray($styleFontBoldTitle);

        $objWorkSheetGarden->setCellValue('C4', lang('Certification Year') . ' : ');
        $objWorkSheetGarden->setCellValue('D4', $dataIms['data']['Year'] . ' / ' . ($dataIms['data']['Year'] + 1));
        $objWorkSheetGarden->getStyle('C4')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetGarden->getStyle('D4')->applyFromArray($styleFontBoldTitle);

        $objWorkSheetGarden->setCellValue('C5', lang('Event Name') . ' : ');
        $objWorkSheetGarden->setCellValue('D5', $dataIms['data']['CertEventName']);
        $objWorkSheetGarden->getStyle('C5')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetGarden->getStyle('D5')->applyFromArray($styleFontBoldTitle);

        //Data Tabel ============= (Begin)

        //tabel header
        $objWorkSheetGarden->setCellValue('A8', 'No');
        $objWorkSheetGarden->setCellValue('B8', lang('Farmer ID'));
        $objWorkSheetGarden->setCellValue('C8', lang('Other Farmer ID'));
        $objWorkSheetGarden->setCellValue('D8', lang('Farmer Name'));
        $objWorkSheetGarden->setCellValue('E8', lang('Gender'));

        if($_SESSION['PartnerID'] == '8')
            $objWorkSheetGarden->setCellValue('F8', lang('Age'));
        else
            $objWorkSheetGarden->setCellValue('F8', lang('Birthdate'));

        $objWorkSheetGarden->setCellValue('G8', lang('CPG'));
        $objWorkSheetGarden->setCellValue('H8', lang('Location'));
        $objWorkSheetGarden->setCellValue('I8', lang('Status'));
        $objWorkSheetGarden->setCellValue('J8', lang('GardenNr'));
        $objWorkSheetGarden->setCellValue('K8', lang('SurveyNr'));
        $objWorkSheetGarden->setCellValue('L8', lang('Reinspection Status'));

        $objWorkSheetGarden->setCellValue('M8', lang('Polygon Status'));
        $objWorkSheetGarden->setCellValue('N8', lang('Latitude'));
        $objWorkSheetGarden->setCellValue('O8', lang('Longitude'));

        $objWorkSheetGarden->setCellValue('P8', lang('First Year of Certification'));
        $objWorkSheetGarden->setCellValue('Q8', lang('Internal Inspection Date'));
        $objWorkSheetGarden->setCellValue('R8', lang('Estimated Harvest Present Year (Kg)'));
        $objWorkSheetGarden->setCellValue('S8', lang('Previous Year\'s Harvest (Kg)'));
        $objWorkSheetGarden->setCellValue('T8', lang('Total Certified Crop Area (Ha)'));

        $objWorkSheetGarden->setCellValue('U8', lang('Total Productive Trees'));
        $objWorkSheetGarden->setCellValue('V8', lang('Total Not Productive Trees'));
        $objWorkSheetGarden->setCellValue('W8', lang('Total Damage Trees'));

        $objWorkSheetGarden->setCellValue('X8', lang('Audit Comment'));
        $objWorkSheetGarden->setCellValue('Y8', lang('Audit Recommendation Comment'));
        $objWorkSheetGarden->getStyle('A8:Y8')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetGarden->getStyle('A8:Y8')->applyFromArray($styleBorderFull, false);

        //tabel data
        $rowStart = 9;
        $incre    = 0;
        foreach ($dataAflGarden as $val) {
            $val['no'] = $incre + 1;

            $objWorkSheetGarden->setCellValue('A' . $rowStart, $val['no']);
            $objWorkSheetGarden->setCellValue('B' . $rowStart, $val['FarmerID']);
            $objWorkSheetGarden->setCellValue('C' . $rowStart, $val['ExtFarmerID']);
            $objWorkSheetGarden->setCellValue('D' . $rowStart, $val['FarmerName']);
            $objWorkSheetGarden->setCellValue('E' . $rowStart, $val['Gender']);

            if($_SESSION['PartnerID'] == '8')
                $objWorkSheetGarden->setCellValue('F' . $rowStart, $val['Age']);
            else
                $objWorkSheetGarden->setCellValue('F' . $rowStart, $val['Birthdate']);


            $objWorkSheetGarden->setCellValue('G' . $rowStart, $val['FarmerGroup']);
            $objWorkSheetGarden->setCellValue('H' . $rowStart, $val['Village']);
            $objWorkSheetGarden->setCellValue('I' . $rowStart, $val['AFLStatus']);

            $objWorkSheetGarden->setCellValue('J' . $rowStart, $val['CertGardenNr']);
            $objWorkSheetGarden->setCellValue('K' . $rowStart, $val['CertSurveyNr']);
            $objWorkSheetGarden->setCellValue('L' . $rowStart, $val['Reinspection']);

            $objWorkSheetGarden->setCellValue('M' . $rowStart, $val['PolygonStatus']);
            $objWorkSheetGarden->setCellValue('N' . $rowStart, $val['CertLatitude']);
            $objWorkSheetGarden->setCellValue('O' . $rowStart, $val['CertLongitude']);

            $objWorkSheetGarden->setCellValue('P' . $rowStart, $val['CertFirstYear']);
            $objWorkSheetGarden->setCellValue('Q' . $rowStart, $val['ICSDate']);
            $objWorkSheetGarden->setCellValue('R' . $rowStart, $val['CertNextHarvest']);
            $objWorkSheetGarden->setCellValue('S' . $rowStart, $val['CertHarvest']);
            $objWorkSheetGarden->setCellValue('T' . $rowStart, $val['CertHectare']);

            $objWorkSheetGarden->setCellValue('U' . $rowStart, $val['CertPohonTM']);
            $objWorkSheetGarden->setCellValue('V' . $rowStart, $val['CertPohonTBM']);
            $objWorkSheetGarden->setCellValue('W' . $rowStart, $val['CertPohonTR']);

            $objWorkSheetGarden->setCellValue('X' . $rowStart, $val['AuditComment']);
            $objWorkSheetGarden->setCellValue('Y' . $rowStart, $val['AuditRecommendationComment']);

            $objWorkSheetGarden->getStyle('A' . $rowStart . ':' . 'Y' . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetGarden->getStyle('A' . $rowStart . ':' . 'Y' . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }
        //Data Tabel ============= (End)

        //=============== MULAI TULIS EXCEL (END) =====================================================================//

        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . date('YmdHis') . '_FarmerAFL.xlsx');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function ims_event_detail_afl_farmer_export_node_get($IMSID){
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        $IMSID = (int) $IMSID;

        //curl
        $PartnerID = $_SESSION['PartnerID'];
        $url = $this->config->item('base_url_ct_util').'certification/ims/ics/excel/'.$IMSID.'/'.$PartnerID;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $arrResult = json_decode($result, true);
        if( ($arrResult['success'] == true) && file_exists('files/export/'.$arrResult['filename']) ) {
            $this->response(array('success' => TRUE, 'filenya' => base_url() . 'files/export/'.$arrResult['filename']), 200);
        } else {
            $this->response(array('success' => FALSE, 'message' => lang('Export Excel Failed')), 400);
        }
    }

    public function ims_event_detail_cfl_farmer_export_node_get($IMSID)
    {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        $IMSID = (int) $IMSID;

        //curl
        $PartnerID = $_SESSION['PartnerID'];
        $url = $this->config->item('base_url_ct_util').'certification/ims/cfl/excel/'.$IMSID.'/'.$PartnerID;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $arrResult = json_decode($result, true);
        if( ($arrResult['success'] == true) && file_exists('files/export/'.$arrResult['filename']) ) {
            $this->response(array('success' => TRUE, 'filenya' => base_url() . 'files/export/'.$arrResult['filename']), 200);
        } else {
            $this->response(array('success' => FALSE, 'message' => lang('Export Excel Failed')), 400);
        }
    }

    public function ims_event_detail_afl_final_farmer_export_node_get($IMSID, $ICSStatus){
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        $IMSID = (int) $IMSID;

        //curl
        $PartnerID = $_SESSION['PartnerID'];
        $url = $this->config->item('base_url_ct_util').'certification/ims/afl/excel/'.$IMSID.'/'.$PartnerID.'/'.$ICSStatus;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $arrResult = json_decode($result, true);
        if( ($arrResult['success'] == true) && file_exists('files/export/'.$arrResult['filename']) ) {
            $this->response(array('success' => TRUE, 'filenya' => base_url() . 'files/export/'.$arrResult['filename']), 200);
        } else {
            $this->response(array('success' => FALSE, 'message' => lang('Export Excel Failed')), 400);
        }
    }

    public function ims_event_detail_afl_farmer_export_get($IMSID) {        
         ini_set('display_errors',true);
         error_reporting(E_ALL);
        ini_set('memory_limit', '-1');

        $IMSID = (int) $IMSID;
        $PartnerID = $_SESSION['PartnerID'];

        //data yg diperlukan (begin)
        $dataIms          = $this->mims->imsEventDetailFillForm($IMSID);
        $dataAfl          = $this->mims->aflListExportExcel($IMSID, 'Comply');
        $dataAflNotComply = $this->mims->aflListExportExcel($IMSID, 'NotComply');
        $dataAflNoStatus  = $this->mims->aflListExportExcel($IMSID, 'NoStatus');

//        $this->response(array($this->db->last_query()), 200); 
        $dataAflGarden          = $this->mims->aflListExportExcelGarden($IMSID, 'Comply');
        $dataAflGardenNotComply = $this->mims->aflListExportExcelGarden($IMSID, 'NotComply');
        $dataAflGardenNoStatus  = $this->mims->aflListExportExcelGarden($IMSID, 'NoStatus');
    
        
        $writer = WriterEntityFactory::createXLSXWriter(); // for XLSX files

        $writer->setTempFolder('files/sql_view_temp/');
        $namaFile = date('YmdHis') . '_Farmer_ICS';
        $filePath = 'files/sql_view/'.$namaFile.'.xlsx';
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

        //style data
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
        
        $empty[] = WriterEntityFactory::createCell('');
        $EmptyRows = WriterEntityFactory::createRow($empty, null);
        
        //DATA Farmer Comply ========================= (Begin)
        
        $FarmerComply = $writer->getCurrentSheet()->setName(lang('Farmer (Comply)'));
        $cells1[0] = WriterEntityFactory::createCell(lang('Farmer AFL'));
        $rowData = WriterEntityFactory::createRow($cells1, null);
        $writer->addRow($rowData);
        $cells2[0] = WriterEntityFactory::createCell(lang('Holder Name'));
        $cells2[1] = WriterEntityFactory::createCell($dataIms['data']['SupplychainLabel']);
        $rowData = WriterEntityFactory::createRow($cells2, null);
        $writer->addRow($rowData);
        $cells3[0] = WriterEntityFactory::createCell(lang('Certification Year'));
        $cells3[1] = WriterEntityFactory::createCell($dataIms['data']['Year'] . '/' . ((int)$dataIms['data']['Year']+1));
        $rowData = WriterEntityFactory::createRow($cells3, null);
        $writer->addRow($rowData);
        $cells4[0] = WriterEntityFactory::createCell(lang('Event name'));
        $cells4[1] = WriterEntityFactory::createCell($dataIms['data']['CertEventName']);
        $rowData = WriterEntityFactory::createRow($cells4, null);
        $writer->addRow($rowData);
        $writer->addRow($EmptyRows);
        $writer->addRow($EmptyRows);
        $HeaderFarComply = array('No.');
        foreach ($dataAfl[0] as $key => $v) {
            if ($key == 'IMSID' || $key == 'AFLFarmerID' || $key == 'CertProgID') {
                continue;
            } else {
                if ($PartnerID == '8') {
                    if ($key === "Birthdate") {
                        continue;
                    }
                } else {
                    if ($key === "Age") {
                        continue;
                    }
                }
            }
            $HeaderFarComply[] = $key;
        }
        $rowHeader = WriterEntityFactory::createRowFromArray($HeaderFarComply, $styleHeader);
        $writer->addRow($rowHeader);
        
        for ($i=0; $i < count($dataAfl); $i++) {
            $dataRows = $dataAfl[$i];
            $cells = array();
            $cells[] = WriterEntityFactory::createCell((int) ($i+1), $styleFormatAngka);

            foreach ($dataRows as $key => $v){
                $styleRow = null;
                $dataRow = null;

                if ($key == 'IMSID' || $key == 'AFLFarmerID' || $key == 'CertProgID') {
                    continue;
                } else {
                    if ($PartnerID == '8') {
                        if ($key === "Birthdate") {
                            continue;
                        }
                    } else {
                        if ($key === "Age") {
                            continue;
                        }
                    }
                }

                //cek apakah numeric
                if(is_numeric($v)){
                    $styleRow = $styleFormatAngka;
                    $dataRow = (float) $v;
                } else {
                    //cek apakah tanggal
                    if($this->validateDate($v) == true) {
                        $styleRow = $styleFormatTanggal;
                    } else {
                        $styleRow = $styleData;
                    }
                    $dataRow = $v;
                }

                $cells[] = WriterEntityFactory::createCell($dataRow, $styleRow);
            }

            $rowData = WriterEntityFactory::createRow($cells);
            $writer->addRow($rowData);
        }

//        $this->response($dataPetani, 200);
        //DATA Farmer Comply ========================= (End)
        
        //DATA Farmer Not Comply ========================= (Begin)
        $HeaderFarNotComply = array('No.');
        foreach ($dataAflNotComply[0] as $key => $v) {
            if ($key == 'IMSID' || $key == 'AFLFarmerID' || $key == 'CertProgID') {
                continue;
            } else {
                if ($PartnerID == '8') {
                    if ($key === "Birthdate") {
                        continue;
                    }
                } else {
                    if ($key === "Age") {
                        continue;
                    }
                }
            }
            $HeaderFarNotComply[] = $key;
        }
        $FarmerNotComply = $writer->addNewSheetAndMakeItCurrent()->setName(lang('Farmer (Not Comply)'));
        $rowHeader = WriterEntityFactory::createRowFromArray($HeaderFarNotComply, $styleHeader);
        $writer->addRow($rowHeader);

        /*foreach ($dataAflNotComply as $k => $value) {
            $rowIsi = array();
            foreach ($value as $key => $v){
                if ($key == 'IMSID' || $key == 'AFLFarmerID' || $key == 'CertProgID') {
                    continue;
                } else {
                    if ($PartnerID == '8') {
                        if ($key === "Birthdate") {
                            continue;
                        }
                    } else {
                        if ($key === "Age") {
                            continue;
                        }
                    }
                }
                $rowIsi[] = $v;
            }
            array_unshift($rowIsi, ($k+1));
            $rowData = WriterEntityFactory::createRowFromArray($rowIsi, $styleData);
            $writer->addRow($rowData);
        }*/

        for ($i=0; $i < count($dataAflNotComply); $i++) {
            $dataRows = $dataAflNotComply[$i];
            $cells = array();
            $cells[] = WriterEntityFactory::createCell((int) ($i+1), $styleFormatAngka);

            foreach ($dataRows as $key => $v){
                $styleRow = null;
                $dataRow = null;

                if ($key == 'IMSID' || $key == 'AFLFarmerID' || $key == 'CertProgID') {
                    continue;
                } else {
                    if ($PartnerID == '8') {
                        if ($key === "Birthdate") {
                            continue;
                        }
                    } else {
                        if ($key === "Age") {
                            continue;
                        }
                    }
                }

                //cek apakah numeric
                if(is_numeric($v)){
                    $styleRow = $styleFormatAngka;
                    $dataRow = (float) $v;
                } else {
                    //cek apakah tanggal
                    if($this->validateDate($v) == true) {
                        $styleRow = $styleFormatTanggal;
                    } else {
                        $styleRow = $styleData;
                    }
                    $dataRow = $v;
                }

                $cells[] = WriterEntityFactory::createCell($dataRow, $styleRow);
            }

            $rowData = WriterEntityFactory::createRow($cells);
            $writer->addRow($rowData);
        }
        //DATA Farmer Not Comply ========================= (End)
        
        //DATA Farmer Not Status ========================= (Begin)
        $HeaderFarNotStatus = array('No.');
        foreach ($dataAfl[0] as $key => $v) {
            if ($key == 'IMSID' || $key == 'AFLFarmerID' || $key == 'CertProgID') {
                continue;
            } else {
                if ($PartnerID == '8') {
                    if ($key === "Birthdate") {
                        continue;
                    }
                } else {
                    if ($key === "Age") {
                        continue;
                    }
                }
            }
            $HeaderFarNotStatus[] = $key;
        }
        $FarmerNotStatus = $writer->addNewSheetAndMakeItCurrent()->setName(lang('Farmer (No Status)'));
        $rowHeader = WriterEntityFactory::createRowFromArray($HeaderFarNotStatus, $styleHeader);
        $writer->addRow($rowHeader);

        /*if ($dataAflNoStatus){
            foreach ($dataAflNoStatus as $k => $value) {
                $rowIsi = array();
                foreach ($value as $key => $v){
                    if ($key == 'IMSID' || $key == 'AFLFarmerID' || $key == 'CertProgID') {
                        continue;
                    } else {
                        if ($PartnerID == '8') {
                            if ($key === "Birthdate") {
                                continue;
                            }
                        } else {
                            if ($key === "Age") {
                                continue;
                            }
                        }
                    }
                    $rowIsi[] = $v;
                }
                array_unshift($rowIsi, ($k+1));
                $rowData = WriterEntityFactory::createRowFromArray($rowIsi, $styleData);
                $writer->addRow($rowData);
            }
        } else {
            $cellNoData[0] = WriterEntityFactory::createCell(lang(' - No Data Found - '));
            $rowData = WriterEntityFactory::createRow($cellNoData, $styleHeader);
            $writer->addRow($rowData);
        }*/

        for ($i=0; $i < count($dataAflNoStatus); $i++) {
            $dataRows = $dataAflNoStatus[$i];
            $cells = array();
            $cells[] = WriterEntityFactory::createCell((int) ($i+1), $styleFormatAngka);

            foreach ($dataRows as $key => $v){
                $styleRow = null;
                $dataRow = null;

                if ($key == 'IMSID' || $key == 'AFLFarmerID' || $key == 'CertProgID') {
                    continue;
                } else {
                    if ($PartnerID == '8') {
                        if ($key === "Birthdate") {
                            continue;
                        }
                    } else {
                        if ($key === "Age") {
                            continue;
                        }
                    }
                }

                //cek apakah numeric
                if(is_numeric($v)){
                    $styleRow = $styleFormatAngka;
                    $dataRow = (float) $v;
                } else {
                    //cek apakah tanggal
                    if($this->validateDate($v) == true) {
                        $styleRow = $styleFormatTanggal;
                    } else {
                        $styleRow = $styleData;
                    }
                    $dataRow = $v;
                }

                $cells[] = WriterEntityFactory::createCell($dataRow, $styleRow);
            }

            $rowData = WriterEntityFactory::createRow($cells);
            $writer->addRow($rowData);
        }
        //DATA Farmer Not Status ========================= (End)
        
        //DATA Farmer Garden ========================= (Begin) $dataAflGarden        
        $Garden = $writer->addNewSheetAndMakeItCurrent()->setName(lang('Garden (Comply)'));
        $cells1[0] = WriterEntityFactory::createCell(lang('Farmer Garden AFL'));
        $rowData = WriterEntityFactory::createRow($cells1, null);
        $writer->addRow($rowData);
        $cells2[0] = WriterEntityFactory::createCell(lang('Holder Name'));
        $cells2[1] = WriterEntityFactory::createCell($dataIms['data']['SupplychainLabel']);
        $rowData = WriterEntityFactory::createRow($cells2, null);
        $writer->addRow($rowData);
        $cells3[0] = WriterEntityFactory::createCell(lang('Certification Year'));
        $cells3[1] = WriterEntityFactory::createCell($dataIms['data']['Year'] . '/' . ((int)$dataIms['data']['Year']+1));
        $rowData = WriterEntityFactory::createRow($cells3, null);
        $writer->addRow($rowData);
        $cells4[0] = WriterEntityFactory::createCell(lang('Event name'));
        $cells4[1] = WriterEntityFactory::createCell($dataIms['data']['CertEventName']);
        $rowData = WriterEntityFactory::createRow($cells4, null);
        $writer->addRow($rowData);
        $writer->addRow($EmptyRows);
        $writer->addRow($EmptyRows);

        if ($dataAflGarden){
            $HeaderGarden = array('No.');
            foreach ($dataAflGarden['data'][0] as $key => $v) {
                if ($key == 'IMSID' || $key == 'AFLFarmerID' || $key == 'CertProgID') {
                    continue;
                } else {
                    if ($PartnerID == '8') {
                        if ($key === "Birthdate") {
                            continue;
                        }
                    } else {
                        if ($key === "Age") {
                            continue;
                        }
                    }
                }
                $HeaderGarden[] = $key;
            }
            $rowHeader = WriterEntityFactory::createRowFromArray($HeaderGarden, $styleHeader);
            $writer->addRow($rowHeader);

            /*foreach ($dataAflGarden['data'] as $k => $value) {
                $rowIsi = array();
                foreach ($value as $key => $v){
                    if ($key == 'IMSID' || $key == 'AFLFarmerID' || $key == 'CertProgID') {
                        continue;
                    } else {
                        if ($PartnerID == '8') {
                            if ($key === "Birthdate") {
                                continue;
                            }
                        } else {
                            if ($key === "Age") {
                                continue;
                            }
                        }
                    }
                    $rowIsi[] = $v;
                }
                array_unshift($rowIsi, ($k+1));
                $rowData = WriterEntityFactory::createRowFromArray($rowIsi, $styleData);
                $writer->addRow($rowData);
            }*/

            for ($i=0; $i < count($dataAflGarden['data']); $i++) {
                $dataRows = $dataAflGarden['data'][$i];
                $cells = array();
                $cells[] = WriterEntityFactory::createCell((int) ($i+1), $styleFormatAngka);
    
                foreach ($dataRows as $key => $v){
                    $styleRow = null;
                    $dataRow = null;
    
                    if ($key == 'IMSID' || $key == 'AFLFarmerID' || $key == 'CertProgID') {
                        continue;
                    } else {
                        if ($PartnerID == '8') {
                            if ($key === "Birthdate") {
                                continue;
                            }
                        } else {
                            if ($key === "Age") {
                                continue;
                            }
                        }
                    }
    
                    //cek apakah numeric
                    if(is_numeric($v)){
                        $styleRow = $styleFormatAngka;
                        $dataRow = (float) $v;
                    } else {
                        //cek apakah tanggal                        
                        if($this->validateDate($v) == true) {
                            $styleRow = $styleFormatTanggal;
                        } else {
                            $styleRow = $styleData;
                        }
                        $dataRow = $v;
                    }
    
                    $cells[] = WriterEntityFactory::createCell($dataRow, $styleRow);
                }
    
                $rowData = WriterEntityFactory::createRow($cells);
                $writer->addRow($rowData);
            }
        } else {
            $HeaderGarden = array('No.', 'FarmerID', 'ExtFarmerID', 'FarmerName', 'Gender', 'Birthdate', 'FarmerGroup', 'Village', 'AFLStatus'
                , 'CertGardenNr', 'CertSurveyNr', 'ExternalAuditStatus', 'Reinspection', 'GardenStatus', 'NotActiveStatus', 'PolygonStatus', 'CertLatitude', 'CertLongitude'
                , 'CertFirstYear', 'ICSDate', 'CertHarvest', 'CertNextHarvest', 'CertHectare', 'CertPohonTM', 'CertPohonTBM', 'CertPohonTR', 'AuditComment', 'AuditRecommendationComment');
            $rowHeader = WriterEntityFactory::createRowFromArray($HeaderGarden, $styleHeader);
            $writer->addRow($rowHeader);
            $cellNoData[0] = WriterEntityFactory::createCell(lang(' - No Data Found - '));
            $rowData = WriterEntityFactory::createRow($cellNoData, $styleHeader);
            $writer->addRow($rowData);
        }
        //DATA Farmer Garden ========================= (End)
        
        //DATA Farmer Garden Not Comply ========================= (Begin) $dataAflGardenNotComply 
        $GardenNotComply = $writer->addNewSheetAndMakeItCurrent()->setName(lang('Garden (Not Comply)'));
        if ($dataAflGardenNotComply['data']){
            $HeaderGardenNotComply = array('No.');
            foreach ($dataAflGardenNotComply['data'][0] as $key => $v) {
                if ($key == 'IMSID' || $key == 'AFLFarmerID' || $key == 'CertProgID') {
                    continue;
                } else {
                    if ($PartnerID == '8') {
                        if ($key === "Birthdate") {
                            continue;
                        }
                    } else {
                        if ($key === "Age") {
                            continue;
                        }
                    }
                }
                $HeaderGardenNotComply[] = $key;
            }
            $rowHeader = WriterEntityFactory::createRowFromArray($HeaderGardenNotComply, $styleHeader);
            $writer->addRow($rowHeader);

            /*foreach ($dataAflGardenNotComply['data'] as $k => $value) {
                $rowIsi = array();
                foreach ($value as $key => $v){
                    if ($key == 'IMSID' || $key == 'AFLFarmerID' || $key == 'CertProgID') {
                        continue;
                    } else {
                        if ($PartnerID == '8') {
                            if ($key === "Birthdate") {
                                continue;
                            }
                        } else {
                            if ($key === "Age") {
                                continue;
                            }
                        }
                    }
                    $rowIsi[] = $v;
                }
                array_unshift($rowIsi, ($k+1));
                $rowData = WriterEntityFactory::createRowFromArray($rowIsi, $styleData);
                $writer->addRow($rowData);
            }*/

            for ($i=0; $i < count($dataAflGardenNotComply['data']); $i++) {
                $dataRows = $dataAflGardenNotComply['data'][$i];
                $cells = array();
                $cells[] = WriterEntityFactory::createCell((int) ($i+1), $styleFormatAngka);
    
                foreach ($dataRows as $key => $v){
                    $styleRow = null;
                    $dataRow = null;
    
                    if ($key == 'IMSID' || $key == 'AFLFarmerID' || $key == 'CertProgID') {
                        continue;
                    } else {
                        if ($PartnerID == '8') {
                            if ($key === "Birthdate") {
                                continue;
                            }
                        } else {
                            if ($key === "Age") {
                                continue;
                            }
                        }
                    }
    
                    //cek apakah numeric
                    if(is_numeric($v)){
                        $styleRow = $styleFormatAngka;
                        $dataRow = (float) $v;
                    } else {
                        //cek apakah tanggal
                        if($this->validateDate($v) == true) {
                            $styleRow = $styleFormatTanggal;
                        } else {
                            $styleRow = $styleData;
                        }
                        $dataRow = $v;
                    }
    
                    $cells[] = WriterEntityFactory::createCell($dataRow, $styleRow);
                }
    
                $rowData = WriterEntityFactory::createRow($cells);
                $writer->addRow($rowData);
            }
        } else {
            $HeaderGardenNotComply = array('No.', 'FarmerID', 'ExtFarmerID', 'FarmerName', 'Gender', 'Birthdate', 'FarmerGroup', 'Village', 'AFLStatus'
                , 'CertGardenNr', 'CertSurveyNr', 'ExternalAuditStatus', 'Reinspection', 'GardenStatus', 'NotActiveStatus', 'PolygonStatus', 'CertLatitude', 'CertLongitude'
                , 'CertFirstYear', 'ICSDate', 'CertHarvest', 'CertNextHarvest', 'CertHectare', 'CertPohonTM', 'CertPohonTBM', 'CertPohonTR', 'AuditComment', 'AuditRecommendationComment');
            $rowHeader = WriterEntityFactory::createRowFromArray($HeaderGardenNotComply, $styleHeader);
            $writer->addRow($rowHeader);
            $cellNoData[0] = WriterEntityFactory::createCell(lang(' - No Data Found - '));
            $rowData = WriterEntityFactory::createRow($cellNoData, $styleHeader);
            $writer->addRow($rowData);
        }
        //DATA Farmer Garden Not Comply ========================= (End)
        
        //DATA Farmer Garden Not Status ========================= (Begin) $dataAflGardenNoStatus 
        $GardenNotStatus = $writer->addNewSheetAndMakeItCurrent()->setName(lang('Garden (No Status)'));
        if ($dataAflGardenNoStatus['data']){
            $HeaderGardenNotStatus = array('No.');
            foreach ($dataAflGardenNoStatus['data'][0] as $key => $v) {
                if ($key == 'IMSID' || $key == 'AFLFarmerID' || $key == 'CertProgID') {
                    continue;
                } else {
                    if ($PartnerID == '8') {
                        if ($key === "Birthdate") {
                            continue;
                        }
                    } else {
                        if ($key === "Age") {
                            continue;
                        }
                    }
                }
                $HeaderGardenNotStatus[] = $key;
            }
            $rowHeader = WriterEntityFactory::createRowFromArray($HeaderGardenNotStatus, $styleHeader);
            $writer->addRow($rowHeader);

            /*foreach ($dataAflGardenNoStatus['data'] as $k => $value) {
                $rowIsi = array();
                foreach ($value as $key => $v){
                    if ($key == 'IMSID' || $key == 'AFLFarmerID' || $key == 'CertProgID') {
                        continue;
                    } else {
                        if ($PartnerID == '8') {
                            if ($key === "Birthdate") {
                                continue;
                            }
                        } else {
                            if ($key === "Age") {
                                continue;
                            }
                        }
                    }
                    $rowIsi[] = $v;
                }
                array_unshift($rowIsi, ($k+1));
                $rowData = WriterEntityFactory::createRowFromArray($rowIsi, $styleData);
                $writer->addRow($rowData);
            }*/
            
            for ($i=0; $i < count($dataAflGardenNoStatus['data']); $i++) {
                $dataRows = $dataAflGardenNoStatus['data'][$i];
                $cells = array();
                $cells[] = WriterEntityFactory::createCell((int) ($i+1), $styleFormatAngka);
    
                foreach ($dataRows as $key => $v){
                    $styleRow = null;
                    $dataRow = null;
    
                    if ($key == 'IMSID' || $key == 'AFLFarmerID' || $key == 'CertProgID') {
                        continue;
                    } else {
                        if ($PartnerID == '8') {
                            if ($key === "Birthdate") {
                                continue;
                            }
                        } else {
                            if ($key === "Age") {
                                continue;
                            }
                        }
                    }
    
                    //cek apakah numeric
                    if(is_numeric($v)){
                        $styleRow = $styleFormatAngka;
                        $dataRow = (float) $v;
                    } else {
                        //cek apakah tanggal
                        if($this->validateDate($v) == true) {
                            $styleRow = $styleFormatTanggal;
                        } else {
                            $styleRow = $styleData;
                        }
                        $dataRow = $v;
                    }
    
                    $cells[] = WriterEntityFactory::createCell($dataRow, $styleRow);
                }
    
                $rowData = WriterEntityFactory::createRow($cells);
                $writer->addRow($rowData);
            }
        } else {
            $HeaderGardenNotStatus = array('No.', 'FarmerID', 'ExtFarmerID', 'FarmerName', 'Gender', 'Birthdate', 'FarmerGroup', 'Village', 'AFLStatus'
                , 'CertGardenNr', 'CertSurveyNr', 'ExternalAuditStatus', 'Reinspection', 'GardenStatus', 'NotActiveStatus', 'PolygonStatus', 'CertLatitude', 'CertLongitude'
                , 'CertFirstYear', 'ICSDate', 'CertHarvest', 'CertNextHarvest', 'CertHectare', 'CertPohonTM', 'CertPohonTBM', 'CertPohonTR', 'AuditComment', 'AuditRecommendationComment');
            $rowHeader = WriterEntityFactory::createRowFromArray($HeaderGardenNotStatus, $styleHeader);
            $writer->addRow($rowHeader);
            $cellNoData[0] = WriterEntityFactory::createCell(lang(' - No Data Found - '));
            $rowData = WriterEntityFactory::createRow($cellNoData, $styleHeader);
            $writer->addRow($rowData);
        }
        //DATA Farmer Garden Not Status ========================= (End)
        
        
        $writer->setCurrentSheet($FarmerComply);
        $writer->close();
        $this->response(array('success' => true, 'filenya' => base_url() . $filePath), 200);
//        exit;        
    }
    
    public function ims_event_detail_afl_farmer_export_old_get($IMSID)
    {
        // ini_set('display_errors',true);
        // error_reporting(E_ALL);
        ini_set('memory_limit', '-1');

        $IMSID = (int) $IMSID;

        //data yg diperlukan (begin)
        $dataIms          = $this->mims->imsEventDetailFillForm($IMSID);
        $dataAfl          = $this->mims->aflListExportExcel($IMSID, 'Comply');
        $dataAflNotComply = $this->mims->aflListExportExcel($IMSID, 'NotComply');
        $dataAflNoStatus  = $this->mims->aflListExportExcel($IMSID, 'NoStatus');

        $dataAflGarden          = $this->mims->aflListExportExcelGarden($IMSID, 'Comply');
        $dataAflGardenNotComply = $this->mims->aflListExportExcelGarden($IMSID, 'NotComply');
        $dataAflGardenNoStatus  = $this->mims->aflListExportExcelGarden($IMSID, 'NoStatus');
        //data yg diperlukan (end)

        require_once 'application/third_party/PHPExcel18/PHPExcel.php';
        require_once 'application/third_party/PHPExcel18/PHPExcel/IOFactory.php';

        //=============== MULAI TULIS EXCEL (BEGIN) ===================================================================//
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("PT Koltiva")
            ->setLastModifiedBy("PT Koltiva")
            ->setTitle("Farmer AFL")
            ->setSubject("Farmer AFL")
            ->setDescription("Farmer AFL")
            ->setKeywords("Farmer AFL")
            ->setCategory("Farmer AFL");

        //set style  (begin)
        $styleFont = array(
            'font'      => array(
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

        $styleFontBoldMainTitle = array(
            'font'      => array(
                'name' => 'Arial',
                'size' => '11',
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
        );

        $styleFontBoldTitle = array(
            'font'      => array(
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
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '8DB4E3'),
            ),
        );
        $styleFontBoldBgRedCenter = array(
            'font'      => array(
                'name' => 'Arial',
                'size' => '9',
                'bold' => true,
            ),
            'fill'      => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'C0504D'),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $styleBorderFull = array(
            'borders' => array(
                'left'   => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'right'  => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'top'    => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );
        //set style  (end)

        //create sheet
        $objWorkSheetPetani = $objPHPExcel->createSheet(0);
        $objWorkSheetPetani->setTitle('Farmer');

        //logo sertifikasi (begin)
        $progCertImage = imagecreatefromjpeg('images/certification_provider/' . $dataIms['data']['CertProgLogoExportJPG']);
        $objDrawing    = new PHPExcel_Worksheet_MemoryDrawing();
        $objDrawing->setName('Certification Program Logo');
        $objDrawing->setDescription('Certification Program Logo');
        $objDrawing->setImageResource($progCertImage);
        $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
        $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
        $objDrawing->setHeight(85);
        $objDrawing->setCoordinates('A1');
        $objDrawing->setWorksheet($objWorkSheetPetani);
        //logo sertifikasi (end)

        //set width column
        $objWorkSheetPetani->getColumnDimension('B')->setWidth(11);
        $objWorkSheetPetani->getColumnDimension('C')->setWidth(11);
        $objWorkSheetPetani->getColumnDimension('D')->setWidth(28);
        $objWorkSheetPetani->getColumnDimension('E')->setWidth(28);
        $objWorkSheetPetani->getColumnDimension('F')->setWidth(22);
        $objWorkSheetPetani->getColumnDimension('G')->setWidth(8);
        $objWorkSheetPetani->getColumnDimension('H')->setWidth(18);
        $objWorkSheetPetani->getColumnDimension('I')->setWidth(17);
        $objWorkSheetPetani->getColumnDimension('J')->setWidth(25);
        $objWorkSheetPetani->getColumnDimension('K')->setWidth(21);
        $objWorkSheetPetani->getColumnDimension('L')->setWidth(20);
        $objWorkSheetPetani->getColumnDimension('M')->setWidth(15);
        $objWorkSheetPetani->getColumnDimension('N')->setWidth(17);

        //tulis judul & informasi detail lainnya
        $objWorkSheetPetani->setCellValue('C2', lang('Farmer AFL'));
        $objWorkSheetPetani->getStyle('C2')->applyFromArray($styleFontBoldMainTitle);
        $objWorkSheetPetani->mergeCells('C2:E2');

        $objWorkSheetPetani->setCellValue('C3', lang('Holder Name') . ' : ');
        $objWorkSheetPetani->setCellValue('D3', $dataIms['data']['SupplychainLabel']);
        $objWorkSheetPetani->getStyle('C3')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetPetani->getStyle('D3')->applyFromArray($styleFontBoldTitle);

        $objWorkSheetPetani->setCellValue('C4', lang('Certification Year') . ' : ');
        $objWorkSheetPetani->setCellValue('D4', $dataIms['data']['Year'] . ' / ' . ($dataIms['data']['Year'] + 1));
        $objWorkSheetPetani->getStyle('C4')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetPetani->getStyle('D4')->applyFromArray($styleFontBoldTitle);

        $objWorkSheetPetani->setCellValue('C5', lang('Event Name') . ' : ');
        $objWorkSheetPetani->setCellValue('D5', $dataIms['data']['CertEventName']);
        $objWorkSheetPetani->getStyle('C5')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetPetani->getStyle('D5')->applyFromArray($styleFontBoldTitle);

        //Data Tabel ============= (Begin)

        //tabel header
        $objWorkSheetPetani->setCellValue('A8', 'No');
        $objWorkSheetPetani->setCellValue('B8', lang('Farmer ID'));
        $objWorkSheetPetani->setCellValue('C8', lang('Other Farmer ID'));
        $objWorkSheetPetani->setCellValue('D8', lang('Farmer Name'));
        $objWorkSheetPetani->setCellValue('E8', lang('Gender'));

        if($_SESSION['PartnerID'] == '8')
            $objWorkSheetPetani->setCellValue('F8', lang('Age'));
        else
            $objWorkSheetPetani->setCellValue('F8', lang('Birthdate'));

        $objWorkSheetPetani->setCellValue('G8', lang('CPG'));
        $objWorkSheetPetani->setCellValue('H8', lang('Cluster'));

        $objWorkSheetPetani->setCellValue('I8', lang('Province'));
        $objWorkSheetPetani->setCellValue('J8', lang('District'));
        $objWorkSheetPetani->setCellValue('K8', lang('Sub District'));
        $objWorkSheetPetani->setCellValue('L8', lang('Location'));

        $objWorkSheetPetani->setCellValue('M8', lang('Bank'));
        $objWorkSheetPetani->setCellValue('N8', lang('Bank Acc Number'));

        $objWorkSheetPetani->setCellValue('O8', lang('Audit Summary'));
        $objWorkSheetPetani->setCellValue('P8', lang('Status'));
        $objWorkSheetPetani->setCellValue('Q8', lang('Verified'));
        $objWorkSheetPetani->setCellValue('R8', lang('First Year of Certification'));
        $objWorkSheetPetani->setCellValue('S8', lang('Internal Inspection Date'));
        $objWorkSheetPetani->setCellValue('T8', lang('Estimated Harvest Present Year (Kg)'));
        $objWorkSheetPetani->setCellValue('U8', lang('Previous Year\'s Harvest (Kg)'));
        $objWorkSheetPetani->setCellValue('V8', lang('Total Certified Crop Area (Ha)'));
        $objWorkSheetPetani->setCellValue('W8', lang('No of Plots'));
        $objWorkSheetPetani->setCellValue('X8', lang('Total Farm Area (Ha)'));
        $objWorkSheetPetani->setCellValue('Y8', lang('Total Productive Trees'));
        $objWorkSheetPetani->setCellValue('Z8', lang('Total Not Productive Trees'));
        $objWorkSheetPetani->setCellValue('AA8', lang('Total Damage Trees'));

        $objWorkSheetPetani->setCellValue('AB8', lang('Last year of delivery'));
        $objWorkSheetPetani->setCellValue('AC8', lang('Last 2 years of delivery'));
        $objWorkSheetPetani->setCellValue('AD8', lang('Last 3 years of delivery'));
        $objWorkSheetPetani->setCellValue('AE8', lang('IMS Creator'));

        $objWorkSheetPetani->setCellValue('AF8', lang('Status Farmer'));
        $objWorkSheetPetani->setCellValue('AG8', lang('Reason Status Farmer'));
        $objWorkSheetPetani->setCellValue('AH8', lang('Status Eligible Audit'));
        $objWorkSheetPetani->setCellValue('AI8', lang('Remarks Status Eligible Audit'));
        $objWorkSheetPetani->setCellValue('AJ8', lang('Certification Audit Remark'));

        $objWorkSheetPetani->getStyle('A8:AJ8')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetPetani->getStyle('A8:AJ8')->applyFromArray($styleBorderFull, false);

        //tabel data
        $rowStart = 9;
        $incre    = 0;
        foreach ($dataAfl as $val) {
            $val['no'] = $incre + 1;

            $objWorkSheetPetani->setCellValue('A' . $rowStart, $val['no']);
            $objWorkSheetPetani->setCellValue('B' . $rowStart, $val['FarmerID']);
            $objWorkSheetPetani->setCellValue('C' . $rowStart, $val['ExtFarmerID']);
            $objWorkSheetPetani->setCellValue('D' . $rowStart, $val['FarmerName']);
            $objWorkSheetPetani->setCellValue('E' . $rowStart, $val['Gender']);

            if($_SESSION['PartnerID'] == '8')
                $objWorkSheetPetani->setCellValue('F' . $rowStart, $val['Age']);
            else
                $objWorkSheetPetani->setCellValue('F' . $rowStart, $val['Birthdate']);

            $objWorkSheetPetani->setCellValue('G' . $rowStart, $val['FarmerGroup']);
            $objWorkSheetPetani->setCellValue('H' . $rowStart, $val['ClusterName']);

            $objWorkSheetPetani->setCellValue('I' . $rowStart, $val['Province']);
            $objWorkSheetPetani->setCellValue('J' . $rowStart, $val['District']);
            $objWorkSheetPetani->setCellValue('K' . $rowStart, $val['SubDistrict']);
            $objWorkSheetPetani->setCellValue('L' . $rowStart, $val['Village']);

            $objWorkSheetPetani->setCellValue('M' . $rowStart, $val['BankName']);
            $objWorkSheetPetani->setCellValue('N' . $rowStart, $val['BankAccNumber']);

            $objWorkSheetPetani->setCellValue('O' . $rowStart, $val['AuditSummaryStatus']);
            $objWorkSheetPetani->setCellValue('P' . $rowStart, $val['AFLStatus']);
            $objWorkSheetPetani->setCellValue('Q' . $rowStart, $val['CertStatusVerified']);
            $objWorkSheetPetani->setCellValue('R' . $rowStart, $val['CertFirstYear']);
            $objWorkSheetPetani->setCellValue('S' . $rowStart, $val['ICSDate']);
            $objWorkSheetPetani->setCellValue('T' . $rowStart, $val['CertNextHarvest']);
            $objWorkSheetPetani->setCellValue('U' . $rowStart, $val['CertHarvest']);

            $objWorkSheetPetani->setCellValue('V' . $rowStart, $val['CertHectare']);
            $objWorkSheetPetani->setCellValue('W' . $rowStart, $val['CertFarmNr']);
            $objWorkSheetPetani->setCellValue('X' . $rowStart, $val['TotalHa']);
            $objWorkSheetPetani->setCellValue('Y' . $rowStart, $val['CertPohonTM']);
            $objWorkSheetPetani->setCellValue('Z' . $rowStart, $val['CertPohonTBM']);
            $objWorkSheetPetani->setCellValue('AA' . $rowStart, $val['CertPohonTR']);

            $objWorkSheetPetani->setCellValue('AB' . $rowStart, $val['SalesLastYear']);
            $objWorkSheetPetani->setCellValue('AC' . $rowStart, $val['SalesLast2Years']);
            $objWorkSheetPetani->setCellValue('AD' . $rowStart, $val['SalesLast3Years']);
            $objWorkSheetPetani->setCellValue('AE' . $rowStart, $val['IMSCreator']);

            $objWorkSheetPetani->setCellValue('AF' . $rowStart, $val['StatusFarmer']);
            $objWorkSheetPetani->setCellValue('AG' . $rowStart, $val['ReasonStatusFarmer']);
            $objWorkSheetPetani->setCellValue('AH' . $rowStart, $val['EligibleForAudit']);
            $objWorkSheetPetani->setCellValue('AI' . $rowStart, $val['AuditRemark']);
            $objWorkSheetPetani->setCellValue('AJ' . $rowStart, $val['CertAuditRemark']);

            $objWorkSheetPetani->getStyle('A' . $rowStart . ':' . 'AJ' . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetPetani->getStyle('A' . $rowStart . ':' . 'AJ' . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }
        //Data Tabel ============= (End)

        //create sheet
        $objWorkSheetGarden = $objPHPExcel->createSheet(1);
        $objWorkSheetGarden->setTitle('Garden');

        //logo sertifikasi (begin)
        $progCertImage = imagecreatefromjpeg('images/certification_provider/' . $dataIms['data']['CertProgLogoExportJPG']);
        $objDrawing    = new PHPExcel_Worksheet_MemoryDrawing();
        $objDrawing->setName('Certification Program Logo');
        $objDrawing->setDescription('Certification Program Logo');
        $objDrawing->setImageResource($progCertImage);
        $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
        $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
        $objDrawing->setHeight(85);
        $objDrawing->setCoordinates('A1');
        $objDrawing->setWorksheet($objWorkSheetGarden);
        //logo sertifikasi (end)

        //set width column
        $objWorkSheetGarden->getColumnDimension('B')->setWidth(11);
        $objWorkSheetGarden->getColumnDimension('C')->setWidth(11);
        $objWorkSheetGarden->getColumnDimension('D')->setWidth(28);
        $objWorkSheetGarden->getColumnDimension('E')->setWidth(28);
        $objWorkSheetGarden->getColumnDimension('F')->setWidth(22);
        $objWorkSheetGarden->getColumnDimension('G')->setWidth(8);
        $objWorkSheetGarden->getColumnDimension('H')->setWidth(18);
        $objWorkSheetGarden->getColumnDimension('I')->setWidth(17);
        $objWorkSheetGarden->getColumnDimension('J')->setWidth(25);
        $objWorkSheetGarden->getColumnDimension('K')->setWidth(21);
        $objWorkSheetGarden->getColumnDimension('L')->setWidth(20);
        $objWorkSheetGarden->getColumnDimension('M')->setWidth(15);
        $objWorkSheetGarden->getColumnDimension('N')->setWidth(17);

        //tulis judul & informasi detail lainnya
        $objWorkSheetGarden->setCellValue('C2', lang('Farmer Garden AFL'));
        $objWorkSheetGarden->getStyle('C2')->applyFromArray($styleFontBoldMainTitle);
        $objWorkSheetGarden->mergeCells('C2:E2');

        $objWorkSheetGarden->setCellValue('C3', lang('Holder Name') . ' : ');
        $objWorkSheetGarden->setCellValue('D3', $dataIms['data']['SupplychainLabel']);
        $objWorkSheetGarden->getStyle('C3')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetGarden->getStyle('D3')->applyFromArray($styleFontBoldTitle);

        $objWorkSheetGarden->setCellValue('C4', lang('Certification Year') . ' : ');
        $objWorkSheetGarden->setCellValue('D4', $dataIms['data']['Year'] . ' / ' . ($dataIms['data']['Year'] + 1));
        $objWorkSheetGarden->getStyle('C4')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetGarden->getStyle('D4')->applyFromArray($styleFontBoldTitle);

        $objWorkSheetGarden->setCellValue('C5', lang('Event Name') . ' : ');
        $objWorkSheetGarden->setCellValue('D5', $dataIms['data']['CertEventName']);
        $objWorkSheetGarden->getStyle('C5')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetGarden->getStyle('D5')->applyFromArray($styleFontBoldTitle);

        //Data Tabel ============= (Begin)

        //tabel header
        $objWorkSheetGarden->setCellValue('A8', 'No');
        $objWorkSheetGarden->setCellValue('B8', lang('Farmer ID'));
        $objWorkSheetGarden->setCellValue('C8', lang('Other Farmer ID'));
        $objWorkSheetGarden->setCellValue('D8', lang('Farmer Name'));
        $objWorkSheetGarden->setCellValue('E8', lang('Gender'));

        if($_SESSION['PartnerID'] == '8')
            $objWorkSheetGarden->setCellValue('F8', lang('Age'));
        else
            $objWorkSheetGarden->setCellValue('F8', lang('Birthdate'));

        $objWorkSheetGarden->setCellValue('G8', lang('CPG'));

        $objWorkSheetGarden->setCellValue('H8', lang('Province'));
        $objWorkSheetGarden->setCellValue('I8', lang('District'));
        $objWorkSheetGarden->setCellValue('J8', lang('Sub District'));
        $objWorkSheetGarden->setCellValue('K8', lang('Location'));

        $objWorkSheetGarden->setCellValue('L8', lang('Status'));
        $objWorkSheetGarden->setCellValue('M8', lang('GardenNr'));
        $objWorkSheetGarden->setCellValue('N8', lang('SurveyNr'));

        $objWorkSheetGarden->setCellValue('O8', lang('Garden Status'));
        $objWorkSheetGarden->setCellValue('P8', lang('Not Active Reason'));

        $objWorkSheetGarden->setCellValue('Q8', lang('Polygon Status'));
        $objWorkSheetGarden->setCellValue('R8', lang('Latitude'));
        $objWorkSheetGarden->setCellValue('S8', lang('Longitude'));

        $objWorkSheetGarden->setCellValue('T8', lang('First Year of Certification'));
        $objWorkSheetGarden->setCellValue('U8', lang('Internal Inspection Date'));
        $objWorkSheetGarden->setCellValue('V8', lang('Estimated Harvest Present Year (Kg)'));
        $objWorkSheetGarden->setCellValue('W8', lang('Previous Year\'s Harvest (Kg)'));
        $objWorkSheetGarden->setCellValue('X8', lang('Total Certified Crop Area (Ha)'));

        $objWorkSheetGarden->setCellValue('Y8', lang('Total Productive Trees'));
        $objWorkSheetGarden->setCellValue('Z8', lang('Total Not Productive Trees'));
        $objWorkSheetGarden->setCellValue('AA8', lang('Total Damage Trees'));

        $objWorkSheetGarden->setCellValue('AB8', lang('Audit Comment'));
        $objWorkSheetGarden->setCellValue('AC8', lang('Audit Recommendation Comment'));
        $objWorkSheetGarden->setCellValue('AD8', lang('IMS Creator'));

        $objWorkSheetGarden->setCellValue('AE8', lang('Status Farmer'));
        $objWorkSheetGarden->setCellValue('AF8', lang('Reason Status Farmer'));
        $objWorkSheetGarden->setCellValue('AG8', lang('Status Eligible Audit'));
        $objWorkSheetGarden->setCellValue('AH8', lang('Remarks Status Eligible Audit'));

        $objWorkSheetGarden->getStyle('A8:AH8')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetGarden->getStyle('A8:AH8')->applyFromArray($styleBorderFull, false);

        //tabel data
        $rowStart = 9;
        $incre    = 0;
        foreach ($dataAflGarden as $val) {
            $val['no'] = $incre + 1;

            $objWorkSheetGarden->setCellValue('A' . $rowStart, $val['no']);
            $objWorkSheetGarden->setCellValue('B' . $rowStart, $val['FarmerID']);
            $objWorkSheetGarden->setCellValue('C' . $rowStart, $val['ExtFarmerID']);
            $objWorkSheetGarden->setCellValue('D' . $rowStart, $val['FarmerName']);
            $objWorkSheetGarden->setCellValue('E' . $rowStart, $val['Gender']);

            if($_SESSION['PartnerID'] == '8')
                $objWorkSheetGarden->setCellValue('F' . $rowStart, $val['Age']);
            else
                $objWorkSheetGarden->setCellValue('F' . $rowStart, $val['Birthdate']);

            $objWorkSheetGarden->setCellValue('G' . $rowStart, $val['FarmerGroup']);

            $objWorkSheetGarden->setCellValue('H' . $rowStart, $val['Province']);
            $objWorkSheetGarden->setCellValue('I' . $rowStart, $val['District']);
            $objWorkSheetGarden->setCellValue('J' . $rowStart, $val['SubDistrict']);
            $objWorkSheetGarden->setCellValue('K' . $rowStart, $val['Village']);

            $objWorkSheetGarden->setCellValue('L' . $rowStart, $val['AFLStatus']);

            $objWorkSheetGarden->setCellValue('M' . $rowStart, $val['CertGardenNr']);
            $objWorkSheetGarden->setCellValue('N' . $rowStart, $val['CertSurveyNr']);

            $objWorkSheetGarden->setCellValue('O' . $rowStart, $val['GardenStatus']);
            $objWorkSheetGarden->setCellValue('P' . $rowStart, $val['NotActiveStatus']);

            $objWorkSheetGarden->setCellValue('Q' . $rowStart, $val['PolygonStatus']);
            $objWorkSheetGarden->setCellValue('R' . $rowStart, $val['CertLatitude']);
            $objWorkSheetGarden->setCellValue('S' . $rowStart, $val['CertLongitude']);

            $objWorkSheetGarden->setCellValue('T' . $rowStart, $val['CertFirstYear']);
            $objWorkSheetGarden->setCellValue('U' . $rowStart, $val['ICSDate']);
            $objWorkSheetGarden->setCellValue('V' . $rowStart, $val['CertNextHarvest']);
            $objWorkSheetGarden->setCellValue('W' . $rowStart, $val['CertHarvest']);
            $objWorkSheetGarden->setCellValue('X' . $rowStart, $val['CertHectare']);

            $objWorkSheetGarden->setCellValue('Y' . $rowStart, $val['CertPohonTM']);
            $objWorkSheetGarden->setCellValue('Z' . $rowStart, $val['CertPohonTBM']);
            $objWorkSheetGarden->setCellValue('AA' . $rowStart, $val['CertPohonTR']);

            $objWorkSheetGarden->setCellValue('AB' . $rowStart, $val['AuditComment']);
            $objWorkSheetGarden->setCellValue('AC' . $rowStart, $val['AuditRecommendationComment']);
            $objWorkSheetGarden->setCellValue('AD' . $rowStart, $val['IMSCreator']);

            $objWorkSheetGarden->setCellValue('AE' . $rowStart, $val['StatusFarmer']);
            $objWorkSheetGarden->setCellValue('AF' . $rowStart, $val['ReasonStatusFarmer']);
            $objWorkSheetGarden->setCellValue('AG' . $rowStart, $val['EligibleForAudit']);
            $objWorkSheetGarden->setCellValue('AH' . $rowStart, $val['AuditRemark']);

            $objWorkSheetGarden->getStyle('A' . $rowStart . ':' . 'AH' . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetGarden->getStyle('A' . $rowStart . ':' . 'AH' . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }
        //Data Tabel ============= (End)

        //=============== DATA NOT COMPLY =====================================================================//

        //create sheet
        $objWorkSheetPetaniNotComply = $objPHPExcel->createSheet(2);
        $objWorkSheetPetaniNotComply->setTitle('Farmer (Not Comply)');

        //set width column
        $objWorkSheetPetaniNotComply->getColumnDimension('B')->setWidth(11);
        $objWorkSheetPetaniNotComply->getColumnDimension('C')->setWidth(11);
        $objWorkSheetPetaniNotComply->getColumnDimension('D')->setWidth(28);
        $objWorkSheetPetaniNotComply->getColumnDimension('E')->setWidth(28);
        $objWorkSheetPetaniNotComply->getColumnDimension('F')->setWidth(22);
        $objWorkSheetPetaniNotComply->getColumnDimension('G')->setWidth(8);
        $objWorkSheetPetaniNotComply->getColumnDimension('H')->setWidth(18);
        $objWorkSheetPetaniNotComply->getColumnDimension('I')->setWidth(17);
        $objWorkSheetPetaniNotComply->getColumnDimension('J')->setWidth(25);
        $objWorkSheetPetaniNotComply->getColumnDimension('K')->setWidth(21);
        $objWorkSheetPetaniNotComply->getColumnDimension('L')->setWidth(20);
        $objWorkSheetPetaniNotComply->getColumnDimension('M')->setWidth(15);
        $objWorkSheetPetaniNotComply->getColumnDimension('N')->setWidth(17);

        //Data Tabel ============= (Begin)

        //tabel header
        $objWorkSheetPetaniNotComply->setCellValue('A2', 'No');
        $objWorkSheetPetaniNotComply->setCellValue('B2', lang('Farmer ID'));
        $objWorkSheetPetaniNotComply->setCellValue('C2', lang('Other Farmer ID'));
        $objWorkSheetPetaniNotComply->setCellValue('D2', lang('Farmer Name'));
        $objWorkSheetPetaniNotComply->setCellValue('E2', lang('Gender'));

        if($_SESSION['PartnerID'] == '8')
            $objWorkSheetPetaniNotComply->setCellValue('F2', lang('Age'));
        else
            $objWorkSheetPetaniNotComply->setCellValue('F2', lang('Birthdate'));

        $objWorkSheetPetaniNotComply->setCellValue('G2', lang('CPG'));
        $objWorkSheetPetaniNotComply->setCellValue('H2', lang('Cluster'));

        $objWorkSheetPetaniNotComply->setCellValue('I2', lang('Province'));
        $objWorkSheetPetaniNotComply->setCellValue('J2', lang('District'));
        $objWorkSheetPetaniNotComply->setCellValue('K2', lang('Sub District'));
        $objWorkSheetPetaniNotComply->setCellValue('L2', lang('Location'));

        $objWorkSheetPetaniNotComply->setCellValue('M2', lang('Bank'));
        $objWorkSheetPetaniNotComply->setCellValue('N2', lang('Bank Acc Number'));

        $objWorkSheetPetaniNotComply->setCellValue('O2', lang('Status'));
        $objWorkSheetPetaniNotComply->setCellValue('P2', lang('Verified'));
        $objWorkSheetPetaniNotComply->setCellValue('Q2', lang('First Year of Certification'));
        $objWorkSheetPetaniNotComply->setCellValue('R2', lang('Internal Inspection Date'));
        $objWorkSheetPetaniNotComply->setCellValue('S2', lang('Estimated Harvest Present Year (Kg)'));
        $objWorkSheetPetaniNotComply->setCellValue('T2', lang('Previous Year\'s Harvest (Kg)'));
        $objWorkSheetPetaniNotComply->setCellValue('U2', lang('Total Certified Crop Area (Ha)'));
        $objWorkSheetPetaniNotComply->setCellValue('V2', lang('No of Plots'));
        $objWorkSheetPetaniNotComply->setCellValue('W2', lang('Total Farm Area (Ha)'));
        $objWorkSheetPetaniNotComply->setCellValue('X2', lang('Total Productive Trees'));
        $objWorkSheetPetaniNotComply->setCellValue('Y2', lang('Total Not Productive Trees'));
        $objWorkSheetPetaniNotComply->setCellValue('Z2', lang('Total Damage Trees'));

        $objWorkSheetPetaniNotComply->setCellValue('AA2', lang('Last year of delivery'));
        $objWorkSheetPetaniNotComply->setCellValue('AB2', lang('Last 2 years of delivery'));
        $objWorkSheetPetaniNotComply->setCellValue('AC2', lang('Last 3 years of delivery'));
        $objWorkSheetPetaniNotComply->setCellValue('AD2', lang('IMS Creator'));

        $objWorkSheetPetaniNotComply->setCellValue('AE2', lang('Status Farmer'));
        $objWorkSheetPetaniNotComply->setCellValue('AF2', lang('Reason Status Farmer'));
        $objWorkSheetPetaniNotComply->setCellValue('AG2', lang('Status Eligible Audit'));
        $objWorkSheetPetaniNotComply->setCellValue('AH2', lang('Remarks Status Eligible Audit'));

        $objWorkSheetPetaniNotComply->setCellValue('AI2', lang('Certification Audit Remark'));
        $objWorkSheetPetaniNotComply->setCellValue('AJ2', lang('Certification Not Comply Reason'));

        $objWorkSheetPetaniNotComply->getStyle('A2:AJ2')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetPetaniNotComply->getStyle('A2:AJ2')->applyFromArray($styleBorderFull, false);

        //tabel data
        $rowStart = 3;
        $incre    = 0;
        foreach ($dataAflNotComply as $val) {
            $val['no'] = $incre + 1;

            $objWorkSheetPetaniNotComply->setCellValue('A' . $rowStart, $val['no']);
            $objWorkSheetPetaniNotComply->setCellValue('B' . $rowStart, $val['FarmerID']);
            $objWorkSheetPetaniNotComply->setCellValue('C' . $rowStart, $val['ExtFarmerID']);
            $objWorkSheetPetaniNotComply->setCellValue('D' . $rowStart, $val['FarmerName']);
            $objWorkSheetPetaniNotComply->setCellValue('E' . $rowStart, $val['Gender']);

            if($_SESSION['PartnerID'] == '8')
                $objWorkSheetPetaniNotComply->setCellValue('F' . $rowStart, $val['Age']);
            else
                $objWorkSheetPetaniNotComply->setCellValue('F' . $rowStart, $val['Birthdate']);

            $objWorkSheetPetaniNotComply->setCellValue('G' . $rowStart, $val['FarmerGroup']);
            $objWorkSheetPetaniNotComply->setCellValue('H' . $rowStart, $val['ClusterName']);

            $objWorkSheetPetaniNotComply->setCellValue('I' . $rowStart, $val['Province']);
            $objWorkSheetPetaniNotComply->setCellValue('J' . $rowStart, $val['District']);
            $objWorkSheetPetaniNotComply->setCellValue('K' . $rowStart, $val['SubDistrict']);
            $objWorkSheetPetaniNotComply->setCellValue('L' . $rowStart, $val['Village']);

            $objWorkSheetPetaniNotComply->setCellValue('M' . $rowStart, $val['BankName']);
            $objWorkSheetPetaniNotComply->setCellValue('N' . $rowStart, $val['BankAccNumber']);

            $objWorkSheetPetaniNotComply->setCellValue('O' . $rowStart, $val['AFLStatus']);
            $objWorkSheetPetaniNotComply->setCellValue('P' . $rowStart, $val['CertStatusVerified']);
            $objWorkSheetPetaniNotComply->setCellValue('Q' . $rowStart, $val['CertFirstYear']);
            $objWorkSheetPetaniNotComply->setCellValue('R' . $rowStart, $val['ICSDate']);
            $objWorkSheetPetaniNotComply->setCellValue('S' . $rowStart, $val['CertNextHarvest']);
            $objWorkSheetPetaniNotComply->setCellValue('T' . $rowStart, $val['CertHarvest']);
            $objWorkSheetPetaniNotComply->setCellValue('U' . $rowStart, $val['CertHectare']);
            $objWorkSheetPetaniNotComply->setCellValue('V' . $rowStart, $val['CertFarmNr']);
            $objWorkSheetPetaniNotComply->setCellValue('W' . $rowStart, $val['TotalHa']);
            $objWorkSheetPetaniNotComply->setCellValue('X' . $rowStart, $val['CertPohonTM']);
            $objWorkSheetPetaniNotComply->setCellValue('Y' . $rowStart, $val['CertPohonTBM']);
            $objWorkSheetPetaniNotComply->setCellValue('Z' . $rowStart, $val['CertPohonTR']);

            $objWorkSheetPetaniNotComply->setCellValue('AA' . $rowStart, $val['SalesLastYear']);
            $objWorkSheetPetaniNotComply->setCellValue('AB' . $rowStart, $val['SalesLast2Years']);
            $objWorkSheetPetaniNotComply->setCellValue('AC' . $rowStart, $val['SalesLast3Years']);
            $objWorkSheetPetaniNotComply->setCellValue('AD' . $rowStart, $val['IMSCreator']);

            $objWorkSheetPetaniNotComply->setCellValue('AE' . $rowStart, $val['StatusFarmer']);
            $objWorkSheetPetaniNotComply->setCellValue('AF' . $rowStart, $val['ReasonStatusFarmer']);
            $objWorkSheetPetaniNotComply->setCellValue('AG' . $rowStart, $val['EligibleForAudit']);
            $objWorkSheetPetaniNotComply->setCellValue('AH' . $rowStart, $val['AuditRemark']);

            $objWorkSheetPetaniNotComply->setCellValue('AI' . $rowStart, $val['CertAuditRemark']);
            $objWorkSheetPetaniNotComply->setCellValue('AJ' . $rowStart, $val['CertAuditNotComplyReason']);

            $objWorkSheetPetaniNotComply->getStyle('A' . $rowStart . ':' . 'AJ' . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetPetaniNotComply->getStyle('A' . $rowStart . ':' . 'AJ' . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }
        //Data Tabel ============= (End)

        //create sheet
        $objWorkSheetGardenNotComply = $objPHPExcel->createSheet(3);
        $objWorkSheetGardenNotComply->setTitle('Garden (Not Comply)');

        //set width column
        $objWorkSheetGardenNotComply->getColumnDimension('B')->setWidth(11);
        $objWorkSheetGardenNotComply->getColumnDimension('C')->setWidth(11);
        $objWorkSheetGardenNotComply->getColumnDimension('D')->setWidth(28);
        $objWorkSheetGardenNotComply->getColumnDimension('E')->setWidth(28);
        $objWorkSheetGardenNotComply->getColumnDimension('F')->setWidth(22);
        $objWorkSheetGardenNotComply->getColumnDimension('G')->setWidth(8);
        $objWorkSheetGardenNotComply->getColumnDimension('H')->setWidth(18);
        $objWorkSheetGardenNotComply->getColumnDimension('I')->setWidth(17);
        $objWorkSheetGardenNotComply->getColumnDimension('J')->setWidth(25);
        $objWorkSheetGardenNotComply->getColumnDimension('K')->setWidth(21);
        $objWorkSheetGardenNotComply->getColumnDimension('L')->setWidth(20);
        $objWorkSheetGardenNotComply->getColumnDimension('M')->setWidth(15);
        $objWorkSheetGardenNotComply->getColumnDimension('N')->setWidth(17);

        //Data Tabel ============= (Begin)

        //tabel header
        $objWorkSheetGardenNotComply->setCellValue('A2', 'No');
        $objWorkSheetGardenNotComply->setCellValue('B2', lang('Farmer ID'));
        $objWorkSheetGardenNotComply->setCellValue('C2', lang('Other Farmer ID'));
        $objWorkSheetGardenNotComply->setCellValue('D2', lang('Farmer Name'));
        $objWorkSheetGardenNotComply->setCellValue('E2', lang('Gender'));

        if($_SESSION['PartnerID'] == '8')
            $objWorkSheetGardenNotComply->setCellValue('F2', lang('Age'));
        else
            $objWorkSheetGardenNotComply->setCellValue('F2', lang('Birthdate'));


        $objWorkSheetGardenNotComply->setCellValue('G2', lang('CPG'));

        $objWorkSheetGardenNotComply->setCellValue('H2', lang('Province'));
        $objWorkSheetGardenNotComply->setCellValue('I2', lang('District'));
        $objWorkSheetGardenNotComply->setCellValue('J2', lang('Sub District'));

        $objWorkSheetGardenNotComply->setCellValue('K2', lang('Location'));
        $objWorkSheetGardenNotComply->setCellValue('L2', lang('Status'));
        $objWorkSheetGardenNotComply->setCellValue('M2', lang('GardenNr'));
        $objWorkSheetGardenNotComply->setCellValue('N2', lang('SurveyNr'));

        $objWorkSheetGardenNotComply->setCellValue('O2', lang('Garden Status'));
        $objWorkSheetGardenNotComply->setCellValue('P2', lang('Not Active Reason'));

        $objWorkSheetGardenNotComply->setCellValue('Q2', lang('Polygon Status'));
        $objWorkSheetGardenNotComply->setCellValue('R2', lang('Latitude'));
        $objWorkSheetGardenNotComply->setCellValue('S2', lang('Longitude'));

        $objWorkSheetGardenNotComply->setCellValue('T2', lang('First Year of Certification'));
        $objWorkSheetGardenNotComply->setCellValue('U2', lang('Internal Inspection Date'));
        $objWorkSheetGardenNotComply->setCellValue('V2', lang('Estimated Harvest Present Year (Kg)'));
        $objWorkSheetGardenNotComply->setCellValue('W2', lang('Previous Year\'s Harvest (Kg)'));
        $objWorkSheetGardenNotComply->setCellValue('X2', lang('Total Certified Crop Area (Ha)'));

        $objWorkSheetGardenNotComply->setCellValue('Y2', lang('Total Productive Trees'));
        $objWorkSheetGardenNotComply->setCellValue('Z2', lang('Total Not Productive Trees'));
        $objWorkSheetGardenNotComply->setCellValue('AA2', lang('Total Damage Trees'));

        $objWorkSheetGardenNotComply->setCellValue('AB2', lang('Audit Comment'));
        $objWorkSheetGardenNotComply->setCellValue('AC2', lang('Audit Recommendation Comment'));
        $objWorkSheetGardenNotComply->setCellValue('AD2', lang('Audit Remark'));
        $objWorkSheetGardenNotComply->setCellValue('AE2', lang('IMS Creator'));

        $objWorkSheetGardenNotComply->setCellValue('AF2', lang('Status Farmer'));
        $objWorkSheetGardenNotComply->setCellValue('AG2', lang('Reason Status Farmer'));
        $objWorkSheetGardenNotComply->setCellValue('AH2', lang('Status Eligible Audit'));
        $objWorkSheetGardenNotComply->setCellValue('AI2', lang('Remarks Status Eligible Audit'));

        $objWorkSheetGardenNotComply->getStyle('A2:AI2')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetGardenNotComply->getStyle('A2:AI2')->applyFromArray($styleBorderFull, false);

        //tabel data
        $rowStart = 3;
        $incre    = 0;
        foreach ($dataAflGardenNotComply as $val) {
            $val['no'] = $incre + 1;

            $objWorkSheetGardenNotComply->setCellValue('A' . $rowStart, $val['no']);
            $objWorkSheetGardenNotComply->setCellValue('B' . $rowStart, $val['FarmerID']);
            $objWorkSheetGardenNotComply->setCellValue('C' . $rowStart, $val['ExtFarmerID']);
            $objWorkSheetGardenNotComply->setCellValue('D' . $rowStart, $val['FarmerName']);
            $objWorkSheetGardenNotComply->setCellValue('E' . $rowStart, $val['Gender']);

            if($_SESSION['PartnerID'] == '8')
                $objWorkSheetGardenNotComply->setCellValue('F' . $rowStart, $val['Age']);
            else
                $objWorkSheetGardenNotComply->setCellValue('F' . $rowStart, $val['Birthdate']);

            $objWorkSheetGardenNotComply->setCellValue('G' . $rowStart, $val['FarmerGroup']);

            $objWorkSheetGardenNotComply->setCellValue('H' . $rowStart, $val['Province']);
            $objWorkSheetGardenNotComply->setCellValue('I' . $rowStart, $val['District']);
            $objWorkSheetGardenNotComply->setCellValue('J' . $rowStart, $val['SubDistrict']);

            $objWorkSheetGardenNotComply->setCellValue('K' . $rowStart, $val['Village']);
            $objWorkSheetGardenNotComply->setCellValue('L' . $rowStart, $val['AFLStatus']);

            $objWorkSheetGardenNotComply->setCellValue('M' . $rowStart, $val['CertGardenNr']);
            $objWorkSheetGardenNotComply->setCellValue('N' . $rowStart, $val['CertSurveyNr']);

            $objWorkSheetGardenNotComply->setCellValue('O' . $rowStart, $val['GardenStatus']);
            $objWorkSheetGardenNotComply->setCellValue('P' . $rowStart, $val['NotActiveStatus']);

            $objWorkSheetGardenNotComply->setCellValue('Q' . $rowStart, $val['PolygonStatus']);
            $objWorkSheetGardenNotComply->setCellValue('R' . $rowStart, $val['CertLatitude']);
            $objWorkSheetGardenNotComply->setCellValue('S' . $rowStart, $val['CertLongitude']);

            $objWorkSheetGardenNotComply->setCellValue('T' . $rowStart, $val['CertFirstYear']);
            $objWorkSheetGardenNotComply->setCellValue('U' . $rowStart, $val['ICSDate']);
            $objWorkSheetGardenNotComply->setCellValue('V' . $rowStart, $val['CertNextHarvest']);
            $objWorkSheetGardenNotComply->setCellValue('W' . $rowStart, $val['CertHarvest']);
            $objWorkSheetGardenNotComply->setCellValue('X' . $rowStart, $val['CertHectare']);

            $objWorkSheetGardenNotComply->setCellValue('Y' . $rowStart, $val['CertPohonTM']);
            $objWorkSheetGardenNotComply->setCellValue('Z' . $rowStart, $val['CertPohonTBM']);
            $objWorkSheetGardenNotComply->setCellValue('AA' . $rowStart, $val['CertPohonTR']);

            $objWorkSheetGardenNotComply->setCellValue('AB' . $rowStart, $val['AuditComment']);
            $objWorkSheetGardenNotComply->setCellValue('AC' . $rowStart, $val['AuditRecommendationComment']);
            $objWorkSheetGardenNotComply->setCellValue('AD' . $rowStart, $val['CertAuditRemark']);
            $objWorkSheetGardenNotComply->setCellValue('AE' . $rowStart, $val['IMSCreator']);

            $objWorkSheetGardenNotComply->setCellValue('AF' . $rowStart, $val['StatusFarmer']);
            $objWorkSheetGardenNotComply->setCellValue('AG' . $rowStart, $val['ReasonStatusFarmer']);
            $objWorkSheetGardenNotComply->setCellValue('AH' . $rowStart, $val['EligibleForAudit']);
            $objWorkSheetGardenNotComply->setCellValue('AI' . $rowStart, $val['AuditRemark']);

            $objWorkSheetGardenNotComply->getStyle('A' . $rowStart . ':' . 'AI' . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetGardenNotComply->getStyle('A' . $rowStart . ':' . 'AI' . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }
        //Data Tabel ============= (End)

        //=============== DATA NO STATUS =====================================================================//
        //create sheet
        $objWorkSheetPetaniNoStatus = $objPHPExcel->createSheet(4);
        $objWorkSheetPetaniNoStatus->setTitle('Farmer (No Status)');

        //set width column
        $objWorkSheetPetaniNoStatus->getColumnDimension('B')->setWidth(11);
        $objWorkSheetPetaniNoStatus->getColumnDimension('C')->setWidth(11);
        $objWorkSheetPetaniNoStatus->getColumnDimension('D')->setWidth(28);
        $objWorkSheetPetaniNoStatus->getColumnDimension('E')->setWidth(28);
        $objWorkSheetPetaniNoStatus->getColumnDimension('F')->setWidth(22);
        $objWorkSheetPetaniNoStatus->getColumnDimension('G')->setWidth(8);
        $objWorkSheetPetaniNoStatus->getColumnDimension('H')->setWidth(18);
        $objWorkSheetPetaniNoStatus->getColumnDimension('I')->setWidth(17);
        $objWorkSheetPetaniNoStatus->getColumnDimension('J')->setWidth(25);
        $objWorkSheetPetaniNoStatus->getColumnDimension('K')->setWidth(21);
        $objWorkSheetPetaniNoStatus->getColumnDimension('L')->setWidth(20);
        $objWorkSheetPetaniNoStatus->getColumnDimension('M')->setWidth(15);
        $objWorkSheetPetaniNoStatus->getColumnDimension('N')->setWidth(17);

        //tabel header
        $objWorkSheetPetaniNoStatus->setCellValue('A2', 'No');
        $objWorkSheetPetaniNoStatus->setCellValue('B2', lang('Farmer ID'));
        $objWorkSheetPetaniNoStatus->setCellValue('C2', lang('Other Farmer ID'));
        $objWorkSheetPetaniNoStatus->setCellValue('D2', lang('Farmer Name'));
        $objWorkSheetPetaniNoStatus->setCellValue('E2', lang('Gender'));

        if($_SESSION['PartnerID'] == '8')
            $objWorkSheetPetaniNoStatus->setCellValue('F2', lang('Age'));
        else
            $objWorkSheetPetaniNoStatus->setCellValue('F2', lang('Birthdate'));

        $objWorkSheetPetaniNoStatus->setCellValue('G2', lang('CPG'));
        $objWorkSheetPetaniNoStatus->setCellValue('H2', lang('Cluster'));

        $objWorkSheetPetaniNoStatus->setCellValue('I2', lang('Province'));
        $objWorkSheetPetaniNoStatus->setCellValue('J2', lang('District'));
        $objWorkSheetPetaniNoStatus->setCellValue('K2', lang('Sub District'));
        $objWorkSheetPetaniNoStatus->setCellValue('L2', lang('Location'));

        $objWorkSheetPetaniNoStatus->setCellValue('M2', lang('Bank'));
        $objWorkSheetPetaniNoStatus->setCellValue('N2', lang('Bank Acc Number'));

        $objWorkSheetPetaniNoStatus->setCellValue('O2', lang('Status'));
        $objWorkSheetPetaniNoStatus->setCellValue('P2', lang('Verified'));
        $objWorkSheetPetaniNoStatus->setCellValue('Q2', lang('First Year of Certification'));
        $objWorkSheetPetaniNoStatus->setCellValue('R2', lang('Internal Inspection Date'));
        $objWorkSheetPetaniNoStatus->setCellValue('S2', lang('Estimated Harvest Present Year (Kg)'));
        $objWorkSheetPetaniNoStatus->setCellValue('T2', lang('Previous Year\'s Harvest (Kg)'));
        $objWorkSheetPetaniNoStatus->setCellValue('U2', lang('Total Certified Crop Area (Ha)'));
        $objWorkSheetPetaniNoStatus->setCellValue('V2', lang('No of Plots'));
        $objWorkSheetPetaniNoStatus->setCellValue('W2', lang('Total Farm Area (Ha)'));
        $objWorkSheetPetaniNoStatus->setCellValue('X2', lang('Total Productive Trees'));
        $objWorkSheetPetaniNoStatus->setCellValue('Y2', lang('Total Not Productive Trees'));
        $objWorkSheetPetaniNoStatus->setCellValue('Z2', lang('Total Damage Trees'));

        $objWorkSheetPetaniNoStatus->setCellValue('AA2', lang('Last year of delivery'));
        $objWorkSheetPetaniNoStatus->setCellValue('AB2', lang('Last 2 years of delivery'));
        $objWorkSheetPetaniNoStatus->setCellValue('AC2', lang('Last 3 years of delivery'));
        $objWorkSheetPetaniNoStatus->setCellValue('AD2', lang('IMS Creator'));

        $objWorkSheetPetaniNoStatus->setCellValue('AE2', lang('Status Farmer'));
        $objWorkSheetPetaniNoStatus->setCellValue('AF2', lang('Reason Status Farmer'));
        $objWorkSheetPetaniNoStatus->setCellValue('AG2', lang('Status Eligible Audit'));
        $objWorkSheetPetaniNoStatus->setCellValue('AH2', lang('Remarks Status Eligible Audit'));

        $objWorkSheetPetaniNoStatus->setCellValue('AI2', lang('Certification Audit Remark'));

        $objWorkSheetPetaniNoStatus->getStyle('A2:AI2')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetPetaniNoStatus->getStyle('A2:AI2')->applyFromArray($styleBorderFull, false);

        //tabel data
        $rowStart = 3;
        $incre    = 0;
        foreach ($dataAflNoStatus as $val) {
            $val['no'] = $incre + 1;

            $objWorkSheetPetaniNoStatus->setCellValue('A' . $rowStart, $val['no']);
            $objWorkSheetPetaniNoStatus->setCellValue('B' . $rowStart, $val['FarmerID']);
            $objWorkSheetPetaniNoStatus->setCellValue('C' . $rowStart, $val['ExtFarmerID']);
            $objWorkSheetPetaniNoStatus->setCellValue('D' . $rowStart, $val['FarmerName']);
            $objWorkSheetPetaniNoStatus->setCellValue('E' . $rowStart, $val['Gender']);

            if($_SESSION['PartnerID'] == '8')
                $objWorkSheetPetaniNoStatus->setCellValue('F' . $rowStart, $val['Age']);
            else
                $objWorkSheetPetaniNoStatus->setCellValue('F' . $rowStart, $val['Birthdate']);

            $objWorkSheetPetaniNoStatus->setCellValue('G' . $rowStart, $val['FarmerGroup']);
            $objWorkSheetPetaniNoStatus->setCellValue('H' . $rowStart, $val['ClusterName']);

            $objWorkSheetPetaniNoStatus->setCellValue('I' . $rowStart, $val['Province']);
            $objWorkSheetPetaniNoStatus->setCellValue('J' . $rowStart, $val['District']);
            $objWorkSheetPetaniNoStatus->setCellValue('K' . $rowStart, $val['SubDistrict']);
            $objWorkSheetPetaniNoStatus->setCellValue('L' . $rowStart, $val['Village']);

            $objWorkSheetPetaniNoStatus->setCellValue('M' . $rowStart, $val['BankName']);
            $objWorkSheetPetaniNoStatus->setCellValue('N' . $rowStart, $val['BankAccNumber']);

            $objWorkSheetPetaniNoStatus->setCellValue('O' . $rowStart, $val['AFLStatus']);
            $objWorkSheetPetaniNoStatus->setCellValue('P' . $rowStart, $val['CertStatusVerified']);
            $objWorkSheetPetaniNoStatus->setCellValue('Q' . $rowStart, $val['CertFirstYear']);
            $objWorkSheetPetaniNoStatus->setCellValue('R' . $rowStart, $val['ICSDate']);
            $objWorkSheetPetaniNoStatus->setCellValue('S' . $rowStart, $val['CertNextHarvest']);
            $objWorkSheetPetaniNoStatus->setCellValue('T' . $rowStart, $val['CertHarvest']);
            $objWorkSheetPetaniNoStatus->setCellValue('U' . $rowStart, $val['CertHectare']);
            $objWorkSheetPetaniNoStatus->setCellValue('V' . $rowStart, $val['CertFarmNr']);
            $objWorkSheetPetaniNoStatus->setCellValue('W' . $rowStart, $val['TotalHa']);
            $objWorkSheetPetaniNoStatus->setCellValue('X' . $rowStart, $val['CertPohonTM']);
            $objWorkSheetPetaniNoStatus->setCellValue('Y' . $rowStart, $val['CertPohonTBM']);
            $objWorkSheetPetaniNoStatus->setCellValue('Z' . $rowStart, $val['CertPohonTR']);

            $objWorkSheetPetaniNoStatus->setCellValue('AA' . $rowStart, $val['SalesLastYear']);
            $objWorkSheetPetaniNoStatus->setCellValue('AB' . $rowStart, $val['SalesLast2Years']);
            $objWorkSheetPetaniNoStatus->setCellValue('AC' . $rowStart, $val['SalesLast3Years']);
            $objWorkSheetPetaniNoStatus->setCellValue('AD' . $rowStart, $val['IMSCreator']);

            $objWorkSheetPetaniNoStatus->setCellValue('AE' . $rowStart, $val['StatusFarmer']);
            $objWorkSheetPetaniNoStatus->setCellValue('AF' . $rowStart, $val['ReasonStatusFarmer']);
            $objWorkSheetPetaniNoStatus->setCellValue('AG' . $rowStart, $val['EligibleForAudit']);
            $objWorkSheetPetaniNoStatus->setCellValue('AH' . $rowStart, $val['AuditRemark']);

            $objWorkSheetPetaniNoStatus->setCellValue('AI' . $rowStart, $val['CertAuditRemark']);

            $objWorkSheetPetaniNoStatus->getStyle('A' . $rowStart . ':' . 'AI' . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetPetaniNoStatus->getStyle('A' . $rowStart . ':' . 'AI' . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }

        //create sheet
        $objWorkSheetGardenNoStatus = $objPHPExcel->createSheet(5);
        $objWorkSheetGardenNoStatus->setTitle('Garden (No Status)');

        //set width column
        $objWorkSheetGardenNoStatus->getColumnDimension('B')->setWidth(11);
        $objWorkSheetGardenNoStatus->getColumnDimension('C')->setWidth(11);
        $objWorkSheetGardenNoStatus->getColumnDimension('D')->setWidth(28);
        $objWorkSheetGardenNoStatus->getColumnDimension('E')->setWidth(28);
        $objWorkSheetGardenNoStatus->getColumnDimension('F')->setWidth(22);
        $objWorkSheetGardenNoStatus->getColumnDimension('G')->setWidth(8);
        $objWorkSheetGardenNoStatus->getColumnDimension('H')->setWidth(18);
        $objWorkSheetGardenNoStatus->getColumnDimension('I')->setWidth(17);
        $objWorkSheetGardenNoStatus->getColumnDimension('J')->setWidth(25);
        $objWorkSheetGardenNoStatus->getColumnDimension('K')->setWidth(21);
        $objWorkSheetGardenNoStatus->getColumnDimension('L')->setWidth(20);
        $objWorkSheetGardenNoStatus->getColumnDimension('M')->setWidth(15);
        $objWorkSheetGardenNoStatus->getColumnDimension('N')->setWidth(17);

        //Data Tabel ============= (Begin)

        //tabel header
        $objWorkSheetGardenNoStatus->setCellValue('A2', 'No');
        $objWorkSheetGardenNoStatus->setCellValue('B2', lang('Farmer ID'));
        $objWorkSheetGardenNoStatus->setCellValue('C2', lang('Other Farmer ID'));
        $objWorkSheetGardenNoStatus->setCellValue('D2', lang('Farmer Name'));
        $objWorkSheetGardenNoStatus->setCellValue('E2', lang('Gender'));

        if($_SESSION['PartnerID'] == '8')
            $objWorkSheetGardenNoStatus->setCellValue('F2', lang('Age'));
        else
            $objWorkSheetGardenNoStatus->setCellValue('F2', lang('Birthdate'));

        $objWorkSheetGardenNoStatus->setCellValue('G2', lang('CPG'));

        $objWorkSheetGardenNoStatus->setCellValue('H2', lang('Province'));
        $objWorkSheetGardenNoStatus->setCellValue('I2', lang('District'));
        $objWorkSheetGardenNoStatus->setCellValue('J2', lang('Sub District'));

        $objWorkSheetGardenNoStatus->setCellValue('K2', lang('Location'));
        $objWorkSheetGardenNoStatus->setCellValue('L2', lang('Status'));
        $objWorkSheetGardenNoStatus->setCellValue('M2', lang('GardenNr'));
        $objWorkSheetGardenNoStatus->setCellValue('N2', lang('SurveyNr'));

        $objWorkSheetGardenNoStatus->setCellValue('O2', lang('Garden Status'));
        $objWorkSheetGardenNoStatus->setCellValue('P2', lang('Not Active Reason'));

        $objWorkSheetGardenNoStatus->setCellValue('Q2', lang('Polygon Status'));
        $objWorkSheetGardenNoStatus->setCellValue('R2', lang('Latitude'));
        $objWorkSheetGardenNoStatus->setCellValue('S2', lang('Longitude'));

        $objWorkSheetGardenNoStatus->setCellValue('T2', lang('First Year of Certification'));
        $objWorkSheetGardenNoStatus->setCellValue('U2', lang('Internal Inspection Date'));
        $objWorkSheetGardenNoStatus->setCellValue('V2', lang('Estimated Harvest Present Year (Kg)'));
        $objWorkSheetGardenNoStatus->setCellValue('W2', lang('Previous Year\'s Harvest (Kg)'));
        $objWorkSheetGardenNoStatus->setCellValue('X2', lang('Total Certified Crop Area (Ha)'));

        $objWorkSheetGardenNoStatus->setCellValue('Y2', lang('Total Productive Trees'));
        $objWorkSheetGardenNoStatus->setCellValue('Z2', lang('Total Not Productive Trees'));
        $objWorkSheetGardenNoStatus->setCellValue('AA2', lang('Total Damage Trees'));

        $objWorkSheetGardenNoStatus->setCellValue('AB2', lang('Audit Comment'));
        $objWorkSheetGardenNoStatus->setCellValue('AC2', lang('Audit Recommendation Comment'));
        $objWorkSheetGardenNoStatus->setCellValue('AD2', lang('Audit Remark'));
        $objWorkSheetGardenNoStatus->setCellValue('AE2', lang('IMS Creator'));

        $objWorkSheetGardenNoStatus->setCellValue('AF2', lang('Status Farmer'));
        $objWorkSheetGardenNoStatus->setCellValue('AG2', lang('Reason Status Farmer'));
        $objWorkSheetGardenNoStatus->setCellValue('AH2', lang('Status Eligible Audit'));
        $objWorkSheetGardenNoStatus->setCellValue('AI2', lang('Remarks Status Eligible Audit'));

        $objWorkSheetGardenNoStatus->getStyle('A2:AI2')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetGardenNoStatus->getStyle('A2:AI2')->applyFromArray($styleBorderFull, false);

        //tabel data
        $rowStart = 3;
        $incre    = 0;
        foreach ($dataAflGardenNoStatus as $val) {
            $val['no'] = $incre + 1;

            $objWorkSheetGardenNoStatus->setCellValue('A' . $rowStart, $val['no']);
            $objWorkSheetGardenNoStatus->setCellValue('B' . $rowStart, $val['FarmerID']);
            $objWorkSheetGardenNoStatus->setCellValue('C' . $rowStart, $val['ExtFarmerID']);
            $objWorkSheetGardenNoStatus->setCellValue('D' . $rowStart, $val['FarmerName']);
            $objWorkSheetGardenNoStatus->setCellValue('E' . $rowStart, $val['Gender']);

            if($_SESSION['PartnerID'] == '8')
                $objWorkSheetGardenNoStatus->setCellValue('F' . $rowStart, $val['Age']);
            else
                $objWorkSheetGardenNoStatus->setCellValue('F' . $rowStart, $val['Birthdate']);

            $objWorkSheetGardenNoStatus->setCellValue('G' . $rowStart, $val['FarmerGroup']);

            $objWorkSheetGardenNoStatus->setCellValue('H' . $rowStart, $val['Province']);
            $objWorkSheetGardenNoStatus->setCellValue('I' . $rowStart, $val['District']);
            $objWorkSheetGardenNoStatus->setCellValue('J' . $rowStart, $val['SubDistrict']);

            $objWorkSheetGardenNoStatus->setCellValue('K' . $rowStart, $val['Village']);
            $objWorkSheetGardenNoStatus->setCellValue('L' . $rowStart, $val['AFLStatus']);

            $objWorkSheetGardenNoStatus->setCellValue('M' . $rowStart, $val['CertGardenNr']);
            $objWorkSheetGardenNoStatus->setCellValue('N' . $rowStart, $val['CertSurveyNr']);

            $objWorkSheetGardenNoStatus->setCellValue('O' . $rowStart, $val['GardenStatus']);
            $objWorkSheetGardenNoStatus->setCellValue('P' . $rowStart, $val['NotActiveStatus']);

            $objWorkSheetGardenNoStatus->setCellValue('Q' . $rowStart, $val['PolygonStatus']);
            $objWorkSheetGardenNoStatus->setCellValue('R' . $rowStart, $val['CertLatitude']);
            $objWorkSheetGardenNoStatus->setCellValue('S' . $rowStart, $val['CertLongitude']);

            $objWorkSheetGardenNoStatus->setCellValue('T' . $rowStart, $val['CertFirstYear']);
            $objWorkSheetGardenNoStatus->setCellValue('U' . $rowStart, $val['ICSDate']);
            $objWorkSheetGardenNoStatus->setCellValue('V' . $rowStart, $val['CertNextHarvest']);
            $objWorkSheetGardenNoStatus->setCellValue('W' . $rowStart, $val['CertHarvest']);
            $objWorkSheetGardenNoStatus->setCellValue('X' . $rowStart, $val['CertHectare']);

            $objWorkSheetGardenNoStatus->setCellValue('Y' . $rowStart, $val['CertPohonTM']);
            $objWorkSheetGardenNoStatus->setCellValue('Z' . $rowStart, $val['CertPohonTBM']);
            $objWorkSheetGardenNoStatus->setCellValue('AA' . $rowStart, $val['CertPohonTR']);

            $objWorkSheetGardenNoStatus->setCellValue('AB' . $rowStart, $val['AuditComment']);
            $objWorkSheetGardenNoStatus->setCellValue('AC' . $rowStart, $val['AuditRecommendationComment']);
            $objWorkSheetGardenNoStatus->setCellValue('AD' . $rowStart, $val['CertAuditRemark']);
            $objWorkSheetGardenNoStatus->setCellValue('AE' . $rowStart, $val['IMSCreator']);

            $objWorkSheetGardenNoStatus->setCellValue('AF' . $rowStart, $val['StatusFarmer']);
            $objWorkSheetGardenNoStatus->setCellValue('AG' . $rowStart, $val['ReasonStatusFarmer']);
            $objWorkSheetGardenNoStatus->setCellValue('AH' . $rowStart, $val['EligibleForAudit']);
            $objWorkSheetGardenNoStatus->setCellValue('AI' . $rowStart, $val['AuditRemark']);

            $objWorkSheetGardenNoStatus->getStyle('A' . $rowStart . ':' . 'AI' . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetGardenNoStatus->getStyle('A' . $rowStart . ':' . 'AI' . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }

        //=============== MULAI TULIS EXCEL (END) =====================================================================//

        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . date('YmdHis') . '_FarmerICS.xlsx');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function ims_summary_kpi_weekly_get($IMSID)
    {
        // ini_set('display_errors',true);
        // error_reporting(E_ALL);
        ini_set('memory_limit', '-1');

        $IMSID = (int) $IMSID;

        //data yg diperlukan (begin)
        $dataIms    = $this->mims->getIMSDetail($IMSID);
        $dataWeekly = $this->mims->getImsSummaryKpiWeekly($IMSID, $dataIms['CertEventDate']);
        //data yg diperlukan (end)

        require_once 'application/third_party/PHPExcel18/PHPExcel.php';
        require_once 'application/third_party/PHPExcel18/PHPExcel/IOFactory.php';

        //=============== MULAI TULIS EXCEL (BEGIN) ===================================================================//

        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("PT Koltiva")
            ->setLastModifiedBy("PT Koltiva")
            ->setTitle("AFL P1 Summary")
            ->setSubject("AFL P1 Summary")
            ->setDescription("AFL P1 Summary")
            ->setKeywords("AFL P1 Summary")
            ->setCategory("AFL P1 Summary");

        //set style  (begin)
        $styleFont = array(
            'font'      => array(
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

        $styleFontBoldMainTitle = array(
            'font'      => array(
                'name' => 'Arial',
                'size' => '11',
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
        );

        $styleFontBoldTitle = array(
            'font'      => array(
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
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '8DB4E3'),
            ),
        );
        $styleFontBoldBgRedCenter = array(
            'font'      => array(
                'name' => 'Arial',
                'size' => '9',
                'bold' => true,
            ),
            'fill'      => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'C0504D'),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $styleBgPink = array(
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'FDA182'),
            ),
        );
        $styleBgYellow = array(
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'FDE492'),
            ),
        );

        $styleBorderFull = array(
            'borders' => array(
                'left'   => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'right'  => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'top'    => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );
        //set style  (end)

        // ===================================== KPI Progress (Begin) =========================================//
        $objWorkSheetProgress = $objPHPExcel->createSheet(0);
        $objWorkSheetProgress->setTitle('KPI Progress');

        $objWorkSheetProgress->getColumnDimension('B')->setWidth(8);
        $objWorkSheetProgress->getColumnDimension('C')->setWidth(23);
        $objWorkSheetProgress->getColumnDimension('D')->setWidth(18);
        $objWorkSheetProgress->getColumnDimension('E')->setWidth(18);
        $objWorkSheetProgress->getColumnDimension('F')->setWidth(18);
        $objWorkSheetProgress->getColumnDimension('G')->setWidth(22);
        $objWorkSheetProgress->getColumnDimension('H')->setWidth(18);
        $objWorkSheetProgress->getColumnDimension('I')->setWidth(18);
        $objWorkSheetProgress->getColumnDimension('J')->setWidth(18);
        $objWorkSheetProgress->getColumnDimension('K')->setWidth(8);

        $objWorkSheetProgress->setCellValue('B2', 'KPI Data Collection Weekly Progress');
        $objWorkSheetProgress->getStyle('B2')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetProgress->mergeCells('B2:D2');

        $objWorkSheetProgress->setCellValue('B3', '[' . $dataIms['CertProgName'] . '] ' . $dataIms['CertEventName']);
        $objWorkSheetProgress->getStyle('B3')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetProgress->mergeCells('B3:F3');

        //tabel header
        $objWorkSheetProgress->setCellValue('B5', lang('Week'));
        $objWorkSheetProgress->setCellValue('C5', lang('Date Range'));
        $objWorkSheetProgress->setCellValue('D5', lang('Farmer Visited'));
        $objWorkSheetProgress->setCellValue('E5', lang('Farmer'));
        $objWorkSheetProgress->setCellValue('F5', lang('Cocoa Farm'));
        $objWorkSheetProgress->setCellValue('G5', lang('Cocoa Farm with Polygon'));
        $objWorkSheetProgress->setCellValue('H5', lang('Post Harvest'));
        $objWorkSheetProgress->setCellValue('I5', lang('Certification'));
        $objWorkSheetProgress->setCellValue('J5', lang('Audit Log'));
        $objWorkSheetProgress->setCellValue('K5', lang('PPI'));
        $objWorkSheetProgress->getStyle('B5:K5')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetProgress->getStyle('B5:K5')->applyFromArray($styleBorderFull, false);

        //tabel data
        $rowStart = 6;
        $incre    = 0;
        foreach ($dataWeekly as $val) {
            $val['no'] = $incre + 1;

            /*$objWorkSheetSummary->setCellValue('E'.$rowStart, $val['tanggal']);
            $objWorkSheetSummary->setCellValue('F'.$rowStart, $val['capai']);
            if($val['capai'] > 0){
            $objWorkSheetSummary->getStyle('F'.$rowStart)->applyFromArray($styleBgYellow);
            }else{
            $objWorkSheetSummary->getStyle('F'.$rowStart)->applyFromArray($styleBgPink);
            }*/

            $objWorkSheetProgress->setCellValue('B' . $rowStart, $val['no']);
            $objWorkSheetProgress->setCellValue('C' . $rowStart, $val['tglMulai'] . ' ' . lang('to') . ' ' . $val['tglSelesai']);

            $objWorkSheetProgress->setCellValue('D' . $rowStart, $val['FarmerVisited']);
            if ($val['FarmerVisited'] > 0) {
                $objWorkSheetProgress->getStyle('D' . $rowStart)->applyFromArray($styleBgYellow);
            } else {
                $objWorkSheetProgress->getStyle('D' . $rowStart)->applyFromArray($styleBgPink);
            }

            $objWorkSheetProgress->setCellValue('E' . $rowStart, $val['Farmer']);
            if ($val['Farmer'] > 0) {
                $objWorkSheetProgress->getStyle('E' . $rowStart)->applyFromArray($styleBgYellow);
            } else {
                $objWorkSheetProgress->getStyle('E' . $rowStart)->applyFromArray($styleBgPink);
            }

            $objWorkSheetProgress->setCellValue('F' . $rowStart, $val['Garden']);
            if ($val['Garden'] > 0) {
                $objWorkSheetProgress->getStyle('F' . $rowStart)->applyFromArray($styleBgYellow);
            } else {
                $objWorkSheetProgress->getStyle('F' . $rowStart)->applyFromArray($styleBgPink);
            }

            $objWorkSheetProgress->setCellValue('G' . $rowStart, $val['GardenWithPolygon']);
            if ($val['GardenWithPolygon'] > 0) {
                $objWorkSheetProgress->getStyle('G' . $rowStart)->applyFromArray($styleBgYellow);
            } else {
                $objWorkSheetProgress->getStyle('G' . $rowStart)->applyFromArray($styleBgPink);
            }

            $objWorkSheetProgress->setCellValue('H' . $rowStart, $val['PostHarvest']);
            if ($val['PostHarvest'] > 0) {
                $objWorkSheetProgress->getStyle('H' . $rowStart)->applyFromArray($styleBgYellow);
            } else {
                $objWorkSheetProgress->getStyle('H' . $rowStart)->applyFromArray($styleBgPink);
            }

            $objWorkSheetProgress->setCellValue('I' . $rowStart, $val['Certification']);
            if ($val['Certification'] > 0) {
                $objWorkSheetProgress->getStyle('I' . $rowStart)->applyFromArray($styleBgYellow);
            } else {
                $objWorkSheetProgress->getStyle('I' . $rowStart)->applyFromArray($styleBgPink);
            }

            $objWorkSheetProgress->setCellValue('J' . $rowStart, $val['AuditLog']);
            if ($val['AuditLog'] > 0) {
                $objWorkSheetProgress->getStyle('J' . $rowStart)->applyFromArray($styleBgYellow);
            } else {
                $objWorkSheetProgress->getStyle('J' . $rowStart)->applyFromArray($styleBgPink);
            }

            $objWorkSheetProgress->setCellValue('K' . $rowStart, $val['PPI']);
            if ($val['PPI'] > 0) {
                $objWorkSheetProgress->getStyle('K' . $rowStart)->applyFromArray($styleBgYellow);
            } else {
                $objWorkSheetProgress->getStyle('K' . $rowStart)->applyFromArray($styleBgPink);
            }

            $objWorkSheetProgress->getStyle('B' . $rowStart . ':' . 'K' . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetProgress->getStyle('B' . $rowStart . ':' . 'K' . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }

        // ===================================== KPI Progress (End) =========================================//

        //=============== MULAI TULIS EXCEL (END) ===================================================================//

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . date('YmdHis') . '_Weekly_IMS_KPI_Progress.xlsx');
        header('Cache-Control: max-age=0');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function ims_event_detail_summary_fa_progress_per_fa_get($IMSID){
        ini_set('memory_limit', '-1');

        $IMSID  = (int) $IMSID;

        //Data yg diperlukan (Begin)
        $DataIMS = $this->mims->getIMSDetail($IMSID);
        $DataRangeTgl = $this->mims->GetRangeTglProgressFA($IMSID);
        $DataList = $this->mims->GetDataListProgressFA($IMSID);
        //Data yg diperlukan (End)

        require_once 'application/third_party/PHPExcel18/PHPExcel.php';
        require_once 'application/third_party/PHPExcel18/PHPExcel/IOFactory.php';

        //=============== MULAI TULIS EXCEL (BEGIN) ===================================================================//

        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("PT Koltiva")
            ->setLastModifiedBy("PT Koltiva")
            ->setTitle("Progress FA Data Collection by Date")
            ->setSubject("Progress FA Data Collection by Date")
            ->setDescription("Progress FA Data Collection by Date")
            ->setKeywords("Progress FA Data Collection by Date")
            ->setCategory("Progress FA Data Collection by Date");

        //set style  (begin)
        $styleFont = array(
            'font'      => array(
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

        $styleFontBoldMainTitle = array(
            'font'      => array(
                'name' => 'Arial',
                'size' => '11',
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
        );

        $styleFontBoldTitle = array(
            'font'      => array(
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
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '8DB4E3'),
            ),
        );
        $styleFontBoldBgRedCenter = array(
            'font'      => array(
                'name' => 'Arial',
                'size' => '9',
                'bold' => true,
            ),
            'fill'      => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'C0504D'),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $styleBgPink = array(
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'FDA182'),
            ),
        );
        $styleBgYellow = array(
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'FDE492'),
            ),
        );

        $styleBorderFull = array(
            'borders' => array(
                'left'   => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'right'  => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'top'    => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );
        //set style  (end)

        $objWorkSheetSummary = $objPHPExcel->createSheet(0);
        $objWorkSheetSummary->setTitle('Farmer Updated');

        $objWorkSheetSummary->getColumnDimension('B')->setWidth(32);

        $objWorkSheetSummary->setCellValue('B2', '[' . $DataIMS['CertProgName'] . '] ' . $DataIMS['CertEventName']);
        $objWorkSheetSummary->getStyle('B2')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetSummary->mergeCells('B2:F2');

        $AbjadKolomBegin = 'B';
        $AbjadKolomBeginInt = PHPExcel_Cell::columnIndexFromString($AbjadKolomBegin);
        $AbjadKolomProsesInt = $AbjadKolomBeginInt;

        //Tabel Header =============== (Begin)
        $objWorkSheetSummary->setCellValue('B4', lang('Field Agent'));
        for ($i=0; $i < count($DataRangeTgl); $i++) {
            $objWorkSheetSummary->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($AbjadKolomProsesInt))->setWidth(15);
            $objWorkSheetSummary->setCellValue(PHPExcel_Cell::stringFromColumnIndex($AbjadKolomProsesInt).'4', $DataRangeTgl[$i]['DateCollection']);
            $AbjadKolomProsesInt++;
        }
        $AbjadKolomProsesInt--;
        $AbjadKolomEndInt = $AbjadKolomProsesInt;
        $objWorkSheetSummary->getStyle('B4:'.PHPExcel_Cell::stringFromColumnIndex($AbjadKolomEndInt).'4')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetSummary->getStyle('B4:'.PHPExcel_Cell::stringFromColumnIndex($AbjadKolomEndInt).'4')->applyFromArray($styleBorderFull, false);
        //Tabel Header =============== (End)

        $RowProcess = 5;
        for ($i=0; $i < count($DataList); $i++) {
            $objWorkSheetSummary->setCellValue('B'.$RowProcess, $DataList[$i]['FA']);

            $AbjadKolomProsesInt = $AbjadKolomBeginInt;
            foreach ($DataList[$i]['DataCount'] as $DateColTgl => $DataCount) {
                $objWorkSheetSummary->setCellValue(PHPExcel_Cell::stringFromColumnIndex($AbjadKolomProsesInt).$RowProcess, $DataCount);

                //Add Style
                if($DataCount > 0){
                    $objWorkSheetSummary->getStyle(PHPExcel_Cell::stringFromColumnIndex($AbjadKolomProsesInt).$RowProcess)->applyFromArray($styleBgYellow);
                }else{
                    $objWorkSheetSummary->getStyle(PHPExcel_Cell::stringFromColumnIndex($AbjadKolomProsesInt).$RowProcess)->applyFromArray($styleBgPink);
                }

                $AbjadKolomProsesInt++;
            }

            $objWorkSheetSummary->getStyle('B' . $RowProcess . ':' . PHPExcel_Cell::stringFromColumnIndex($AbjadKolomEndInt) . $RowProcess)->applyFromArray($styleFont);
            $objWorkSheetSummary->getStyle('B' . $RowProcess . ':' . PHPExcel_Cell::stringFromColumnIndex($AbjadKolomEndInt) . $RowProcess)->applyFromArray($styleBorderFull, false);
            $RowProcess++;
        }

        //=============== MULAI TULIS EXCEL (END) ===================================================================//

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . date('YmdHis') . '_' . $IMSID . '_Progress_FA_ByDate.xlsx');
        header('Cache-Control: max-age=0');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function ims_event_detail_summary_fa_progress_get($IMSID, $UserID)
    {
        // ini_set('display_errors',true);
        // error_reporting(E_ALL);
        ini_set('memory_limit', '-1');

        $IMSID  = (int) $IMSID;
        $UserID = (int) $UserID;

        //data yg diperlukan (begin)
        $dataIms = $this->mims->getIMSDetail($IMSID);
        $dataFa  = $this->mims->getFADetail($UserID);

        $dataPetani        = $this->mims->getFarmerAflP1SummaryFaProgress($IMSID, $UserID);
        $dataGarden        = $this->mims->getGardenAflP1SummaryFaProgress($IMSID, $UserID);
        $dataPostHarvest   = $this->mims->getPostHarvestAflP1SummaryFaProgress($IMSID, $UserID, $dataIms['CertEventDate']);
        $dataCertification = $this->mims->getCertificationAflP1SummaryFaProgress($IMSID, $UserID, $dataIms['CertEventDate']);
        $dataAuditLog      = $this->mims->getAuditLogAflP1SummaryFaProgress($IMSID, $UserID, $dataIms['CertEventDate']);
        $dataPpi           = $this->mims->getPpigAflP1SummaryFaProgress($IMSID, $UserID, $dataIms['CertEventDate']);
        //data yg diperlukan (end)

        require_once 'application/third_party/PHPExcel18/PHPExcel.php';
        require_once 'application/third_party/PHPExcel18/PHPExcel/IOFactory.php';

        //=============== MULAI TULIS EXCEL (BEGIN) ===================================================================//

        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("PT Koltiva")
            ->setLastModifiedBy("PT Koltiva")
            ->setTitle("AFL P1 Summary")
            ->setSubject("AFL P1 Summary")
            ->setDescription("AFL P1 Summary")
            ->setKeywords("AFL P1 Summary")
            ->setCategory("AFL P1 Summary");

        //set style  (begin)
        $styleFont = array(
            'font'      => array(
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

        $styleFontBoldMainTitle = array(
            'font'      => array(
                'name' => 'Arial',
                'size' => '11',
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
        );

        $styleFontBoldTitle = array(
            'font'      => array(
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
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '8DB4E3'),
            ),
        );
        $styleFontBoldBgRedCenter = array(
            'font'      => array(
                'name' => 'Arial',
                'size' => '9',
                'bold' => true,
            ),
            'fill'      => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'C0504D'),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $styleBgPink = array(
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'FDA182'),
            ),
        );
        $styleBgYellow = array(
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'FDE492'),
            ),
        );

        $styleBorderFull = array(
            'borders' => array(
                'left'   => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'right'  => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'top'    => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );
        //set style  (end)

        // ===================================== FARMER (Begin) =========================================//
        $objWorkSheetSummary = $objPHPExcel->createSheet(0);
        $objWorkSheetSummary->setTitle('Farmer');

        $objWorkSheetSummary->getColumnDimension('B')->setWidth(18);
        $objWorkSheetSummary->getColumnDimension('C')->setWidth(18);
        $objWorkSheetSummary->getColumnDimension('D')->setWidth(8);
        $objWorkSheetSummary->getColumnDimension('E')->setWidth(18);
        $objWorkSheetSummary->getColumnDimension('F')->setWidth(18);

        $objWorkSheetSummary->setCellValue('B2', lang('Field Agent') . ' : ');
        $objWorkSheetSummary->setCellValue('C2', $dataFa['Name']);
        $objWorkSheetSummary->getStyle('B2')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetSummary->getStyle('C2')->applyFromArray($styleFontBoldTitle);

        $objWorkSheetSummary->setCellValue('B3', '[' . $dataIms['CertProgName'] . '] ' . $dataIms['CertEventName']);
        $objWorkSheetSummary->getStyle('B3')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetSummary->mergeCells('B3:F3');

        //tabel header
        $objWorkSheetSummary->setCellValue('B5', lang('Farmer Data'));
        $objWorkSheetSummary->mergeCells('B5:C5');
        $objWorkSheetSummary->getStyle('B5:C5')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetSummary->getStyle('B5:C5')->applyFromArray($styleBorderFull, false);
        //tabel data
        $objWorkSheetSummary->setCellValue('B6', lang('Target') . ' : ');
        $objWorkSheetSummary->setCellValue('C6', $dataPetani['target']);
        $objWorkSheetSummary->setCellValue('B7', lang('Progress') . ' : ');
        $objWorkSheetSummary->setCellValue('C7', $dataPetani['capai']);
        $objWorkSheetSummary->getStyle('B6:C7')->applyFromArray($styleFont);
        $objWorkSheetSummary->getStyle('B6:C7')->applyFromArray($styleBorderFull, false);

        //tabel header
        $objWorkSheetSummary->setCellValue('E5', lang('Date Collection Date'));
        $objWorkSheetSummary->setCellValue('F5', lang('Count Data'));
        $objWorkSheetSummary->getStyle('E5:F5')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetSummary->getStyle('E5:F5')->applyFromArray($styleBorderFull, false);

        //tabel data
        $rowStart = 6;
        $incre    = 0;
        foreach ($dataPetani['dataList'] as $val) {
            $val['no'] = $incre + 1;

            $objWorkSheetSummary->setCellValue('E' . $rowStart, $val['tanggal']);
            $objWorkSheetSummary->setCellValue('F' . $rowStart, $val['capai']);
            if ($val['capai'] > 0) {
                $objWorkSheetSummary->getStyle('F' . $rowStart)->applyFromArray($styleBgYellow);
            } else {
                $objWorkSheetSummary->getStyle('F' . $rowStart)->applyFromArray($styleBgPink);
            }

            $objWorkSheetSummary->getStyle('E' . $rowStart . ':' . 'F' . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetSummary->getStyle('E' . $rowStart . ':' . 'F' . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }
        // ===================================== FARMER (End) =========================================//

        // ===================================== GARDEN (Begin) =========================================//
        $objWorkSheetSummaryGarden = $objPHPExcel->createSheet(1);
        $objWorkSheetSummaryGarden->setTitle('Garden');

        $objWorkSheetSummaryGarden->getColumnDimension('B')->setWidth(18);
        $objWorkSheetSummaryGarden->getColumnDimension('C')->setWidth(18);
        $objWorkSheetSummaryGarden->getColumnDimension('D')->setWidth(8);
        $objWorkSheetSummaryGarden->getColumnDimension('E')->setWidth(18);
        $objWorkSheetSummaryGarden->getColumnDimension('F')->setWidth(18);

        $objWorkSheetSummaryGarden->setCellValue('B2', lang('Field Agent') . ' : ');
        $objWorkSheetSummaryGarden->setCellValue('C2', $dataFa['Name']);
        $objWorkSheetSummaryGarden->getStyle('B2')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetSummaryGarden->getStyle('C2')->applyFromArray($styleFontBoldTitle);

        $objWorkSheetSummaryGarden->setCellValue('B3', '[' . $dataIms['CertProgName'] . '] ' . $dataIms['CertEventName']);
        $objWorkSheetSummaryGarden->getStyle('B3')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetSummaryGarden->mergeCells('B3:F3');

        //tabel header
        $objWorkSheetSummaryGarden->setCellValue('B5', lang('Garden Data'));
        $objWorkSheetSummaryGarden->mergeCells('B5:C5');
        $objWorkSheetSummaryGarden->getStyle('B5:C5')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetSummaryGarden->getStyle('B5:C5')->applyFromArray($styleBorderFull, false);
        //tabel data
        $objWorkSheetSummaryGarden->setCellValue('B6', lang('Target') . ' : ');
        $objWorkSheetSummaryGarden->setCellValue('C6', $dataGarden['target']);
        $objWorkSheetSummaryGarden->setCellValue('B7', lang('Progress') . ' : ');
        $objWorkSheetSummaryGarden->setCellValue('C7', $dataGarden['capai']);
        $objWorkSheetSummaryGarden->getStyle('B6:C7')->applyFromArray($styleFont);
        $objWorkSheetSummaryGarden->getStyle('B6:C7')->applyFromArray($styleBorderFull, false);

        //tabel header
        $objWorkSheetSummaryGarden->setCellValue('E5', lang('Date Collection Date'));
        $objWorkSheetSummaryGarden->setCellValue('F5', lang('Count Data'));
        $objWorkSheetSummaryGarden->getStyle('E5:F5')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetSummaryGarden->getStyle('E5:F5')->applyFromArray($styleBorderFull, false);

        //tabel data
        $rowStart = 6;
        $incre    = 0;
        foreach ($dataGarden['dataList'] as $val) {
            $val['no'] = $incre + 1;

            $objWorkSheetSummaryGarden->setCellValue('E' . $rowStart, $val['tanggal']);
            $objWorkSheetSummaryGarden->setCellValue('F' . $rowStart, $val['capai']);
            if ($val['capai'] > 0) {
                $objWorkSheetSummaryGarden->getStyle('F' . $rowStart)->applyFromArray($styleBgYellow);
            } else {
                $objWorkSheetSummaryGarden->getStyle('F' . $rowStart)->applyFromArray($styleBgPink);
            }

            $objWorkSheetSummaryGarden->getStyle('E' . $rowStart . ':' . 'F' . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetSummaryGarden->getStyle('E' . $rowStart . ':' . 'F' . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }
        // ===================================== GARDEN (End) =========================================//

        // ===================================== POST HARVEST (Begin) =========================================//
        $objWorkSheetSummaryPostHarvest = $objPHPExcel->createSheet(2);
        $objWorkSheetSummaryPostHarvest->setTitle('Post Harvest');

        $objWorkSheetSummaryPostHarvest->getColumnDimension('B')->setWidth(18);
        $objWorkSheetSummaryPostHarvest->getColumnDimension('C')->setWidth(18);
        $objWorkSheetSummaryPostHarvest->getColumnDimension('D')->setWidth(8);
        $objWorkSheetSummaryPostHarvest->getColumnDimension('E')->setWidth(18);
        $objWorkSheetSummaryPostHarvest->getColumnDimension('F')->setWidth(18);

        $objWorkSheetSummaryPostHarvest->setCellValue('B2', lang('Field Agent') . ' : ');
        $objWorkSheetSummaryPostHarvest->setCellValue('C2', $dataFa['Name']);
        $objWorkSheetSummaryPostHarvest->getStyle('B2')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetSummaryPostHarvest->getStyle('C2')->applyFromArray($styleFontBoldTitle);

        $objWorkSheetSummaryPostHarvest->setCellValue('B3', '[' . $dataIms['CertProgName'] . '] ' . $dataIms['CertEventName']);
        $objWorkSheetSummaryPostHarvest->getStyle('B3')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetSummaryPostHarvest->mergeCells('B3:F3');

        //tabel header
        $objWorkSheetSummaryPostHarvest->setCellValue('B5', lang('Post Harvest Data'));
        $objWorkSheetSummaryPostHarvest->mergeCells('B5:C5');
        $objWorkSheetSummaryPostHarvest->getStyle('B5:C5')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetSummaryPostHarvest->getStyle('B5:C5')->applyFromArray($styleBorderFull, false);
        //tabel data
        $objWorkSheetSummaryPostHarvest->setCellValue('B6', lang('Target') . ' : ');
        $objWorkSheetSummaryPostHarvest->setCellValue('C6', $dataPostHarvest['target']);
        $objWorkSheetSummaryPostHarvest->setCellValue('B7', lang('Progress') . ' : ');
        $objWorkSheetSummaryPostHarvest->setCellValue('C7', $dataPostHarvest['capai']);
        $objWorkSheetSummaryPostHarvest->getStyle('B6:C7')->applyFromArray($styleFont);
        $objWorkSheetSummaryPostHarvest->getStyle('B6:C7')->applyFromArray($styleBorderFull, false);

        //tabel header
        $objWorkSheetSummaryPostHarvest->setCellValue('E5', lang('Date Collection Date'));
        $objWorkSheetSummaryPostHarvest->setCellValue('F5', lang('Count Data'));
        $objWorkSheetSummaryPostHarvest->getStyle('E5:F5')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetSummaryPostHarvest->getStyle('E5:F5')->applyFromArray($styleBorderFull, false);

        //tabel data
        $rowStart = 6;
        $incre    = 0;
        foreach ($dataPostHarvest['dataList'] as $val) {
            $val['no'] = $incre + 1;

            $objWorkSheetSummaryPostHarvest->setCellValue('E' . $rowStart, $val['tanggal']);
            $objWorkSheetSummaryPostHarvest->setCellValue('F' . $rowStart, $val['capai']);
            if ($val['capai'] > 0) {
                $objWorkSheetSummaryPostHarvest->getStyle('F' . $rowStart)->applyFromArray($styleBgYellow);
            } else {
                $objWorkSheetSummaryPostHarvest->getStyle('F' . $rowStart)->applyFromArray($styleBgPink);
            }

            $objWorkSheetSummaryPostHarvest->getStyle('E' . $rowStart . ':' . 'F' . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetSummaryPostHarvest->getStyle('E' . $rowStart . ':' . 'F' . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }
        // ===================================== POST HARVEST (End) =========================================//

        // ===================================== CERTIFICATION (Begin) =========================================//
        $objWorkSheetSummaryCertification = $objPHPExcel->createSheet(3);
        $objWorkSheetSummaryCertification->setTitle('Certification');

        $objWorkSheetSummaryCertification->getColumnDimension('B')->setWidth(18);
        $objWorkSheetSummaryCertification->getColumnDimension('C')->setWidth(18);
        $objWorkSheetSummaryCertification->getColumnDimension('D')->setWidth(8);
        $objWorkSheetSummaryCertification->getColumnDimension('E')->setWidth(18);
        $objWorkSheetSummaryCertification->getColumnDimension('F')->setWidth(18);

        $objWorkSheetSummaryCertification->setCellValue('B2', lang('Field Agent') . ' : ');
        $objWorkSheetSummaryCertification->setCellValue('C2', $dataFa['Name']);
        $objWorkSheetSummaryCertification->getStyle('B2')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetSummaryCertification->getStyle('C2')->applyFromArray($styleFontBoldTitle);

        $objWorkSheetSummaryCertification->setCellValue('B3', '[' . $dataIms['CertProgName'] . '] ' . $dataIms['CertEventName']);
        $objWorkSheetSummaryCertification->getStyle('B3')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetSummaryCertification->mergeCells('B3:F3');

        //tabel header
        $objWorkSheetSummaryCertification->setCellValue('B5', lang('Certification Data'));
        $objWorkSheetSummaryCertification->mergeCells('B5:C5');
        $objWorkSheetSummaryCertification->getStyle('B5:C5')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetSummaryCertification->getStyle('B5:C5')->applyFromArray($styleBorderFull, false);
        //tabel data
        $objWorkSheetSummaryCertification->setCellValue('B6', lang('Target') . ' : ');
        $objWorkSheetSummaryCertification->setCellValue('C6', $dataCertification['target']);
        $objWorkSheetSummaryCertification->setCellValue('B7', lang('Progress') . ' : ');
        $objWorkSheetSummaryCertification->setCellValue('C7', $dataCertification['capai']);
        $objWorkSheetSummaryCertification->getStyle('B6:C7')->applyFromArray($styleFont);
        $objWorkSheetSummaryCertification->getStyle('B6:C7')->applyFromArray($styleBorderFull, false);

        //tabel header
        $objWorkSheetSummaryCertification->setCellValue('E5', lang('Date Collection Date'));
        $objWorkSheetSummaryCertification->setCellValue('F5', lang('Count Data'));
        $objWorkSheetSummaryCertification->getStyle('E5:F5')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetSummaryCertification->getStyle('E5:F5')->applyFromArray($styleBorderFull, false);

        //tabel data
        $rowStart = 6;
        $incre    = 0;
        foreach ($dataCertification['dataList'] as $val) {
            $val['no'] = $incre + 1;

            $objWorkSheetSummaryCertification->setCellValue('E' . $rowStart, $val['tanggal']);
            $objWorkSheetSummaryCertification->setCellValue('F' . $rowStart, $val['capai']);
            if ($val['capai'] > 0) {
                $objWorkSheetSummaryCertification->getStyle('F' . $rowStart)->applyFromArray($styleBgYellow);
            } else {
                $objWorkSheetSummaryCertification->getStyle('F' . $rowStart)->applyFromArray($styleBgPink);
            }

            $objWorkSheetSummaryCertification->getStyle('E' . $rowStart . ':' . 'F' . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetSummaryCertification->getStyle('E' . $rowStart . ':' . 'F' . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }
        // ===================================== CERTIFICATION (End) =========================================//

        // ===================================== AUDIT LOG (Begin) =========================================//
        $objWorkSheetSummaryAuditLog = $objPHPExcel->createSheet(4);
        $objWorkSheetSummaryAuditLog->setTitle('Audit Log');

        $objWorkSheetSummaryAuditLog->getColumnDimension('B')->setWidth(18);
        $objWorkSheetSummaryAuditLog->getColumnDimension('C')->setWidth(18);
        $objWorkSheetSummaryAuditLog->getColumnDimension('D')->setWidth(8);
        $objWorkSheetSummaryAuditLog->getColumnDimension('E')->setWidth(18);
        $objWorkSheetSummaryAuditLog->getColumnDimension('F')->setWidth(18);

        $objWorkSheetSummaryAuditLog->setCellValue('B2', lang('Field Agent') . ' : ');
        $objWorkSheetSummaryAuditLog->setCellValue('C2', $dataFa['Name']);
        $objWorkSheetSummaryAuditLog->getStyle('B2')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetSummaryAuditLog->getStyle('C2')->applyFromArray($styleFontBoldTitle);

        $objWorkSheetSummaryAuditLog->setCellValue('B3', '[' . $dataIms['CertProgName'] . '] ' . $dataIms['CertEventName']);
        $objWorkSheetSummaryAuditLog->getStyle('B3')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetSummaryAuditLog->mergeCells('B3:F3');

        //tabel header
        $objWorkSheetSummaryAuditLog->setCellValue('B5', lang('Audit Log Data'));
        $objWorkSheetSummaryAuditLog->mergeCells('B5:C5');
        $objWorkSheetSummaryAuditLog->getStyle('B5:C5')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetSummaryAuditLog->getStyle('B5:C5')->applyFromArray($styleBorderFull, false);
        //tabel data
        $objWorkSheetSummaryAuditLog->setCellValue('B6', lang('Target') . ' : ');
        $objWorkSheetSummaryAuditLog->setCellValue('C6', $dataAuditLog['target']);
        $objWorkSheetSummaryAuditLog->setCellValue('B7', lang('Progress') . ' : ');
        $objWorkSheetSummaryAuditLog->setCellValue('C7', $dataAuditLog['capai']);
        $objWorkSheetSummaryAuditLog->getStyle('B6:C7')->applyFromArray($styleFont);
        $objWorkSheetSummaryAuditLog->getStyle('B6:C7')->applyFromArray($styleBorderFull, false);

        //tabel header
        $objWorkSheetSummaryAuditLog->setCellValue('E5', lang('Date Collection Date'));
        $objWorkSheetSummaryAuditLog->setCellValue('F5', lang('Count Data'));
        $objWorkSheetSummaryAuditLog->getStyle('E5:F5')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetSummaryAuditLog->getStyle('E5:F5')->applyFromArray($styleBorderFull, false);

        //tabel data
        $rowStart = 6;
        $incre    = 0;
        foreach ($dataAuditLog['dataList'] as $val) {
            $val['no'] = $incre + 1;

            $objWorkSheetSummaryAuditLog->setCellValue('E' . $rowStart, $val['tanggal']);
            $objWorkSheetSummaryAuditLog->setCellValue('F' . $rowStart, $val['capai']);
            if ($val['capai'] > 0) {
                $objWorkSheetSummaryAuditLog->getStyle('F' . $rowStart)->applyFromArray($styleBgYellow);
            } else {
                $objWorkSheetSummaryAuditLog->getStyle('F' . $rowStart)->applyFromArray($styleBgPink);
            }

            $objWorkSheetSummaryAuditLog->getStyle('E' . $rowStart . ':' . 'F' . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetSummaryAuditLog->getStyle('E' . $rowStart . ':' . 'F' . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }
        // ===================================== AUDIT LOG (End) =========================================//

        // ===================================== PPI (Begin) =========================================//
        $objWorkSheetSummaryPpi = $objPHPExcel->createSheet(5);
        $objWorkSheetSummaryPpi->setTitle('PPI');

        $objWorkSheetSummaryPpi->getColumnDimension('B')->setWidth(18);
        $objWorkSheetSummaryPpi->getColumnDimension('C')->setWidth(18);
        $objWorkSheetSummaryPpi->getColumnDimension('D')->setWidth(8);
        $objWorkSheetSummaryPpi->getColumnDimension('E')->setWidth(18);
        $objWorkSheetSummaryPpi->getColumnDimension('F')->setWidth(18);

        $objWorkSheetSummaryPpi->setCellValue('B2', lang('Field Agent') . ' : ');
        $objWorkSheetSummaryPpi->setCellValue('C2', $dataFa['Name']);
        $objWorkSheetSummaryPpi->getStyle('B2')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetSummaryPpi->getStyle('C2')->applyFromArray($styleFontBoldTitle);

        $objWorkSheetSummaryPpi->setCellValue('B3', '[' . $dataIms['CertProgName'] . '] ' . $dataIms['CertEventName']);
        $objWorkSheetSummaryPpi->getStyle('B3')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetSummaryPpi->mergeCells('B3:F3');

        //tabel header
        $objWorkSheetSummaryPpi->setCellValue('B5', lang('PPI Data'));
        $objWorkSheetSummaryPpi->mergeCells('B5:C5');
        $objWorkSheetSummaryPpi->getStyle('B5:C5')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetSummaryPpi->getStyle('B5:C5')->applyFromArray($styleBorderFull, false);
        //tabel data
        $objWorkSheetSummaryPpi->setCellValue('B6', lang('Target') . ' : ');
        $objWorkSheetSummaryPpi->setCellValue('C6', $dataPpi['target']);
        $objWorkSheetSummaryPpi->setCellValue('B7', lang('Progress') . ' : ');
        $objWorkSheetSummaryPpi->setCellValue('C7', $dataPpi['capai']);
        $objWorkSheetSummaryPpi->getStyle('B6:C7')->applyFromArray($styleFont);
        $objWorkSheetSummaryPpi->getStyle('B6:C7')->applyFromArray($styleBorderFull, false);

        //tabel header
        $objWorkSheetSummaryPpi->setCellValue('E5', lang('Date Collection Date'));
        $objWorkSheetSummaryPpi->setCellValue('F5', lang('Count Data'));
        $objWorkSheetSummaryPpi->getStyle('E5:F5')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetSummaryPpi->getStyle('E5:F5')->applyFromArray($styleBorderFull, false);

        //tabel data
        $rowStart = 6;
        $incre    = 0;
        foreach ($dataPpi['dataList'] as $val) {
            $val['no'] = $incre + 1;

            $objWorkSheetSummaryPpi->setCellValue('E' . $rowStart, $val['tanggal']);
            $objWorkSheetSummaryPpi->setCellValue('F' . $rowStart, $val['capai']);
            if ($val['capai'] > 0) {
                $objWorkSheetSummaryPpi->getStyle('F' . $rowStart)->applyFromArray($styleBgYellow);
            } else {
                $objWorkSheetSummaryPpi->getStyle('F' . $rowStart)->applyFromArray($styleBgPink);
            }

            $objWorkSheetSummaryPpi->getStyle('E' . $rowStart . ':' . 'F' . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetSummaryPpi->getStyle('E' . $rowStart . ':' . 'F' . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }
        // ===================================== PPI (End) =========================================//

        //=============== MULAI TULIS EXCEL (END) ===================================================================//

        //format nama untuk filename
        $dataFa['Name'] = str_replace(' ', '', $dataFa['Name']);
        $dataFa['Name'] = preg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $dataFa['Name']);
        $dataFa['Name'] = preg_replace("([\.]{2,})", '', $dataFa['Name']);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . date('YmdHis') . '_' . $dataFa['Name'] . '_PreICS_Progress.xlsx');
        header('Cache-Control: max-age=0');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function ims_event_detail_summary_fa_detail_get($IMSID, $UserID)
    {
        // ini_set('display_errors',true);
        // error_reporting(E_ALL);
        ini_set('memory_limit', '-1');

        $IMSID  = (int) $IMSID;
        $UserID = (int) $UserID;

        //data yg diperlukan (begin)
        $dataIms = $this->mims->getIMSDetail($IMSID);
        $dataFa  = $this->mims->getFADetail($UserID);

        $dataPetani        = $this->mims->getFarmerAflP1SummaryFa($IMSID, $UserID);
        $dataGarden        = $this->mims->getGardenAflP1SummaryFa($IMSID, $UserID);
        $dataPostHarvest   = $this->mims->getPostHarvestAflP1SummaryFa($IMSID, $UserID, $dataIms['CertEventDate']);
        $dataCertification = $this->mims->getCertificationAflP1SummaryFa($IMSID, $UserID, $dataIms['CertEventDate']);
        $dataAuditLog      = $this->mims->getAuditLogAflP1SummaryFa($IMSID, $UserID, $dataIms['CertEventDate']);
        $dataPpi           = $this->mims->getPpigAflP1SummaryFa($IMSID, $UserID, $dataIms['CertEventDate']);
        //data yg diperlukan (end)

        require_once 'application/third_party/PHPExcel18/PHPExcel.php';
        require_once 'application/third_party/PHPExcel18/PHPExcel/IOFactory.php';

        //=============== MULAI TULIS EXCEL (BEGIN) ===================================================================//

        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("PT Koltiva")
            ->setLastModifiedBy("PT Koltiva")
            ->setTitle("AFL P1 Summary")
            ->setSubject("AFL P1 Summary")
            ->setDescription("AFL P1 Summary")
            ->setKeywords("AFL P1 Summary")
            ->setCategory("AFL P1 Summary");

        //set style  (begin)
        $styleFont = array(
            'font'      => array(
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

        $styleFontBoldMainTitle = array(
            'font'      => array(
                'name' => 'Arial',
                'size' => '11',
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
        );

        $styleFontBoldTitle = array(
            'font'      => array(
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
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '8DB4E3'),
            ),
        );
        $styleFontBoldBgRedCenter = array(
            'font'      => array(
                'name' => 'Arial',
                'size' => '9',
                'bold' => true,
            ),
            'fill'      => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'C0504D'),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $styleBorderFull = array(
            'borders' => array(
                'left'   => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'right'  => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'top'    => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );
        //set style  (end)

        //====================== SHEET PETANI (Begin) ==============================//

        //create sheet
        $objWorkSheetPetani = $objPHPExcel->createSheet(0);
        $objWorkSheetPetani->setTitle('Farmer');

        //set width column
        $objWorkSheetPetani->getColumnDimension('B')->setWidth(11);
        $objWorkSheetPetani->getColumnDimension('C')->setWidth(15);
        $objWorkSheetPetani->getColumnDimension('D')->setWidth(30);

        $objWorkSheetPetani->setCellValue('B2', lang('Field Agent') . ' : ');
        $objWorkSheetPetani->setCellValue('C2', $dataFa['Name']);
        $objWorkSheetPetani->getStyle('B2')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetPetani->getStyle('C2')->applyFromArray($styleFontBoldTitle);

        $objWorkSheetPetani->setCellValue('B3', '[' . $dataIms['CertProgName'] . '] ' . $dataIms['CertEventName']);
        $objWorkSheetPetani->getStyle('B3')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetPetani->mergeCells('B3:E3');

        $objWorkSheetPetani->setCellValue('B4', lang('Target Petani') . ' : ' . $dataPetani['target']);
        $objWorkSheetPetani->getStyle('B4')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetPetani->mergeCells('B4:E4');

        $objWorkSheetPetani->setCellValue('B5', lang('Capaian Petani') . ' : ' . $dataPetani['capai']);
        $objWorkSheetPetani->getStyle('B5')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetPetani->mergeCells('B5:E5');

        //data kolom
        $dataKolom = array();
        $increTemp = 0;
        foreach ($dataPetani['dataList'] as $key => $value) {
            if ($key == 0) {
                foreach ($value as $key2 => $value2) {
                    $dataKolom[$increTemp]['name'] = $key2;
                    $increTemp++;
                }
            } else {
                break;
            }
        }

        //tabel header (begin)
        $objWorkSheetPetani->setCellValue('A7', 'No');
        $columnstart = 1;
        for ($i = 0; $i < count($dataKolom); $i++) {
            $objWorkSheetPetani->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart) . '7', $dataKolom[$i]['name']);
            $columnstart++;
        }
        $columnstart--;
        $columnstartLast = $columnstart;
        $objWorkSheetPetani->getStyle('A7:' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . '7')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetPetani->getStyle('A7:' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . '7')->applyFromArray($styleBorderFull, false);
        //tabel header (end)

        $rowStart = 8;
        $incre    = 0;
        foreach ($dataPetani['dataList'] as $val) {
            $val['no'] = $incre + 1;
            $objWorkSheetPetani->setCellValue('A' . $rowStart, $val['no']);

            $columnstart = 1;
            for ($i = 0; $i < count($dataKolom); $i++) {
                $objWorkSheetPetani->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart) . $rowStart, $val[$dataKolom[$i]['name']]);
                $columnstart++;
            }

            $objWorkSheetPetani->getStyle('A' . $rowStart . ':' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetPetani->getStyle('A' . $rowStart . ':' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }

        //====================== SHEET PETANI (End)   ==============================//

        //====================== SHEET GARDEN (Begin)  ==============================//

        //create sheet
        $objWorkSheetGarden = $objPHPExcel->createSheet(1);
        $objWorkSheetGarden->setTitle('Garden');

        //set width column
        $objWorkSheetGarden->getColumnDimension('B')->setWidth(11);
        $objWorkSheetGarden->getColumnDimension('C')->setWidth(15);
        $objWorkSheetGarden->getColumnDimension('D')->setWidth(30);

        $objWorkSheetGarden->setCellValue('B2', lang('Field Agent') . ' : ');
        $objWorkSheetGarden->setCellValue('C2', $dataFa['Name']);
        $objWorkSheetGarden->getStyle('B2')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetGarden->getStyle('C2')->applyFromArray($styleFontBoldTitle);

        $objWorkSheetGarden->setCellValue('B3', '[' . $dataIms['CertProgName'] . '] ' . $dataIms['CertEventName']);
        $objWorkSheetGarden->getStyle('B3')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetGarden->mergeCells('B3:E3');

        $objWorkSheetGarden->setCellValue('B4', lang('Target Garden') . ' : ' . $dataGarden['target']);
        $objWorkSheetGarden->getStyle('B4')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetGarden->mergeCells('B4:E4');

        $objWorkSheetGarden->setCellValue('B5', lang('Capaian Garden') . ' : ' . $dataGarden['capai']);
        $objWorkSheetGarden->getStyle('B5')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetGarden->mergeCells('B5:E5');

        //data kolom
        $dataKolom = array();
        $increTemp = 0;
        foreach ($dataGarden['dataList'] as $key => $value) {
            if ($key == 0) {
                foreach ($value as $key2 => $value2) {
                    $dataKolom[$increTemp]['name'] = $key2;
                    $increTemp++;
                }
            } else {
                break;
            }
        }

        //tabel header (begin)
        $objWorkSheetGarden->setCellValue('A7', 'No');
        $columnstart = 1;
        for ($i = 0; $i < count($dataKolom); $i++) {
            $objWorkSheetGarden->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart) . '7', $dataKolom[$i]['name']);
            $columnstart++;
        }
        $columnstart--;
        $columnstartLast = $columnstart;
        $objWorkSheetGarden->getStyle('A7:' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . '7')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetGarden->getStyle('A7:' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . '7')->applyFromArray($styleBorderFull, false);
        //tabel header (end)

        $rowStart = 8;
        $incre    = 0;
        foreach ($dataGarden['dataList'] as $val) {
            $val['no'] = $incre + 1;
            $objWorkSheetGarden->setCellValue('A' . $rowStart, $val['no']);

            $columnstart = 1;
            for ($i = 0; $i < count($dataKolom); $i++) {
                $objWorkSheetGarden->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart) . $rowStart, $val[$dataKolom[$i]['name']]);
                $columnstart++;
            }

            $objWorkSheetGarden->getStyle('A' . $rowStart . ':' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetGarden->getStyle('A' . $rowStart . ':' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }

        //====================== SHEET GARDEN (End)   ==============================//

        //==================== SHEET POST HARVEST (Begin)   ==============================//

        //create sheet
        $objWorkSheetPostHarvest = $objPHPExcel->createSheet(2);
        $objWorkSheetPostHarvest->setTitle('Post Harvest');

        //set width column
        $objWorkSheetPostHarvest->getColumnDimension('B')->setWidth(11);
        $objWorkSheetPostHarvest->getColumnDimension('C')->setWidth(15);
        $objWorkSheetPostHarvest->getColumnDimension('D')->setWidth(30);

        $objWorkSheetPostHarvest->setCellValue('B2', lang('Field Agent') . ' : ');
        $objWorkSheetPostHarvest->setCellValue('C2', $dataFa['Name']);
        $objWorkSheetPostHarvest->getStyle('B2')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetPostHarvest->getStyle('C2')->applyFromArray($styleFontBoldTitle);

        $objWorkSheetPostHarvest->setCellValue('B3', '[' . $dataIms['CertProgName'] . '] ' . $dataIms['CertEventName']);
        $objWorkSheetPostHarvest->getStyle('B3')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetPostHarvest->mergeCells('B3:E3');

        $objWorkSheetPostHarvest->setCellValue('B4', lang('Target Post Harvest') . ' : ' . $dataPostHarvest['target']);
        $objWorkSheetPostHarvest->getStyle('B4')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetPostHarvest->mergeCells('B4:E4');

        $objWorkSheetPostHarvest->setCellValue('B5', lang('Capaian Post Harvest') . ' : ' . $dataPostHarvest['capai']);
        $objWorkSheetPostHarvest->getStyle('B5')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetPostHarvest->mergeCells('B5:E5');

        //data kolom
        $dataKolom = array();
        $increTemp = 0;
        foreach ($dataPostHarvest['dataList'] as $key => $value) {
            if ($key == 0) {
                foreach ($value as $key2 => $value2) {
                    $dataKolom[$increTemp]['name'] = $key2;
                    $increTemp++;
                }
            } else {
                break;
            }
        }

        //tabel header (begin)
        $objWorkSheetPostHarvest->setCellValue('A7', 'No');
        $columnstart = 1;
        for ($i = 0; $i < count($dataKolom); $i++) {
            $objWorkSheetPostHarvest->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart) . '7', $dataKolom[$i]['name']);
            $columnstart++;
        }
        $columnstart--;
        $columnstartLast = $columnstart;
        $objWorkSheetPostHarvest->getStyle('A7:' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . '7')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetPostHarvest->getStyle('A7:' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . '7')->applyFromArray($styleBorderFull, false);
        //tabel header (end)

        $rowStart = 8;
        $incre    = 0;
        foreach ($dataPostHarvest['dataList'] as $val) {
            $val['no'] = $incre + 1;
            $objWorkSheetPostHarvest->setCellValue('A' . $rowStart, $val['no']);

            $columnstart = 1;
            for ($i = 0; $i < count($dataKolom); $i++) {
                $objWorkSheetPostHarvest->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart) . $rowStart, $val[$dataKolom[$i]['name']]);
                $columnstart++;
            }

            $objWorkSheetPostHarvest->getStyle('A' . $rowStart . ':' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetPostHarvest->getStyle('A' . $rowStart . ':' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }

        //====================== SHEET POST HARVEST (End)   ==============================//

        //====================== SHEET CERTIFICATION (Begin)   ==============================//

        //create sheet
        $objWorkSheetCertification = $objPHPExcel->createSheet(3);
        $objWorkSheetCertification->setTitle('Certification');

        //set width column
        $objWorkSheetCertification->getColumnDimension('B')->setWidth(11);
        $objWorkSheetCertification->getColumnDimension('C')->setWidth(15);
        $objWorkSheetCertification->getColumnDimension('D')->setWidth(30);

        $objWorkSheetCertification->setCellValue('B2', lang('Field Agent') . ' : ');
        $objWorkSheetCertification->setCellValue('C2', $dataFa['Name']);
        $objWorkSheetCertification->getStyle('B2')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetCertification->getStyle('C2')->applyFromArray($styleFontBoldTitle);

        $objWorkSheetCertification->setCellValue('B3', '[' . $dataIms['CertProgName'] . '] ' . $dataIms['CertEventName']);
        $objWorkSheetCertification->getStyle('B3')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetCertification->mergeCells('B3:E3');

        $objWorkSheetCertification->setCellValue('B4', lang('Target Certification') . ' : ' . $dataCertification['target']);
        $objWorkSheetCertification->getStyle('B4')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetCertification->mergeCells('B4:E4');

        $objWorkSheetCertification->setCellValue('B5', lang('Capaian Certification') . ' : ' . $dataCertification['capai']);
        $objWorkSheetCertification->getStyle('B5')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetCertification->mergeCells('B5:E5');

        //data kolom
        $dataKolom = array();
        $increTemp = 0;
        foreach ($dataCertification['dataList'] as $key => $value) {
            if ($key == 0) {
                foreach ($value as $key2 => $value2) {
                    $dataKolom[$increTemp]['name'] = $key2;
                    $increTemp++;
                }
            } else {
                break;
            }
        }

        //tabel header (begin)
        $objWorkSheetCertification->setCellValue('A7', 'No');
        $columnstart = 1;
        for ($i = 0; $i < count($dataKolom); $i++) {
            $objWorkSheetCertification->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart) . '7', $dataKolom[$i]['name']);
            $columnstart++;
        }
        $columnstart--;
        $columnstartLast = $columnstart;
        $objWorkSheetCertification->getStyle('A7:' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . '7')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetCertification->getStyle('A7:' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . '7')->applyFromArray($styleBorderFull, false);
        //tabel header (end)

        $rowStart = 8;
        $incre    = 0;
        foreach ($dataCertification['dataList'] as $val) {
            $val['no'] = $incre + 1;
            $objWorkSheetCertification->setCellValue('A' . $rowStart, $val['no']);

            $columnstart = 1;
            for ($i = 0; $i < count($dataKolom); $i++) {
                $objWorkSheetCertification->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart) . $rowStart, $val[$dataKolom[$i]['name']]);
                $columnstart++;
            }

            $objWorkSheetCertification->getStyle('A' . $rowStart . ':' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetCertification->getStyle('A' . $rowStart . ':' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }
        //====================== SHEET CERTIFICATION (End)   ==============================//

        //====================== SHEET AUDIT LOG (Begin)   ==============================//

        //create sheet
        $objWorkSheetAuditLog = $objPHPExcel->createSheet(4);
        $objWorkSheetAuditLog->setTitle('Audit Log');

        //set width column
        $objWorkSheetAuditLog->getColumnDimension('B')->setWidth(11);
        $objWorkSheetAuditLog->getColumnDimension('C')->setWidth(15);
        $objWorkSheetAuditLog->getColumnDimension('D')->setWidth(30);

        $objWorkSheetAuditLog->setCellValue('B2', lang('Field Agent') . ' : ');
        $objWorkSheetAuditLog->setCellValue('C2', $dataFa['Name']);
        $objWorkSheetAuditLog->getStyle('B2')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetAuditLog->getStyle('C2')->applyFromArray($styleFontBoldTitle);

        $objWorkSheetAuditLog->setCellValue('B3', '[' . $dataIms['CertProgName'] . '] ' . $dataIms['CertEventName']);
        $objWorkSheetAuditLog->getStyle('B3')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetAuditLog->mergeCells('B3:E3');

        $objWorkSheetAuditLog->setCellValue('B4', lang('Target Audit Log') . ' : ' . $dataAuditLog['target']);
        $objWorkSheetAuditLog->getStyle('B4')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetAuditLog->mergeCells('B4:E4');

        $objWorkSheetAuditLog->setCellValue('B5', lang('Capaian Audit Log') . ' : ' . $dataAuditLog['capai']);
        $objWorkSheetAuditLog->getStyle('B5')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetAuditLog->mergeCells('B5:E5');

        //data kolom
        $dataKolom = array();
        $increTemp = 0;
        foreach ($dataAuditLog['dataList'] as $key => $value) {
            if ($key == 0) {
                foreach ($value as $key2 => $value2) {
                    $dataKolom[$increTemp]['name'] = $key2;
                    $increTemp++;
                }
            } else {
                break;
            }
        }

        //tabel header (begin)
        $objWorkSheetAuditLog->setCellValue('A7', 'No');
        $columnstart = 1;
        for ($i = 0; $i < count($dataKolom); $i++) {
            $objWorkSheetAuditLog->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart) . '7', $dataKolom[$i]['name']);
            $columnstart++;
        }
        $columnstart--;
        $columnstartLast = $columnstart;
        $objWorkSheetAuditLog->getStyle('A7:' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . '7')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetAuditLog->getStyle('A7:' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . '7')->applyFromArray($styleBorderFull, false);
        //tabel header (end)

        $rowStart = 8;
        $incre    = 0;
        foreach ($dataAuditLog['dataList'] as $val) {
            $val['no'] = $incre + 1;
            $objWorkSheetAuditLog->setCellValue('A' . $rowStart, $val['no']);

            $columnstart = 1;
            for ($i = 0; $i < count($dataKolom); $i++) {
                $objWorkSheetAuditLog->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart) . $rowStart, $val[$dataKolom[$i]['name']]);
                $columnstart++;
            }

            $objWorkSheetAuditLog->getStyle('A' . $rowStart . ':' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetAuditLog->getStyle('A' . $rowStart . ':' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }

        //====================== SHEET AUDIT LOG (End)   ==============================//

        //====================== SHEET PPI (Begin)   ==============================//

        //create sheet
        $objWorkSheetPpi = $objPHPExcel->createSheet(5);
        $objWorkSheetPpi->setTitle('PPI');

        //set width column
        $objWorkSheetPpi->getColumnDimension('B')->setWidth(11);
        $objWorkSheetPpi->getColumnDimension('C')->setWidth(15);
        $objWorkSheetPpi->getColumnDimension('D')->setWidth(30);

        $objWorkSheetPpi->setCellValue('B2', lang('Field Agent') . ' : ');
        $objWorkSheetPpi->setCellValue('C2', $dataFa['Name']);
        $objWorkSheetPpi->getStyle('B2')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetPpi->getStyle('C2')->applyFromArray($styleFontBoldTitle);

        $objWorkSheetPpi->setCellValue('B3', '[' . $dataIms['CertProgName'] . '] ' . $dataIms['CertEventName']);
        $objWorkSheetPpi->getStyle('B3')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetPpi->mergeCells('B3:E3');

        $objWorkSheetPpi->setCellValue('B4', lang('Target PPI') . ' : ' . $dataPpi['target']);
        $objWorkSheetPpi->getStyle('B4')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetPpi->mergeCells('B4:E4');

        $objWorkSheetPpi->setCellValue('B5', lang('Capaian PPI') . ' : ' . $dataPpi['capai']);
        $objWorkSheetPpi->getStyle('B5')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetPpi->mergeCells('B5:E5');

        //data kolom
        $dataKolom = array();
        $increTemp = 0;
        foreach ($dataPpi['dataList'] as $key => $value) {
            if ($key == 0) {
                foreach ($value as $key2 => $value2) {
                    $dataKolom[$increTemp]['name'] = $key2;
                    $increTemp++;
                }
            } else {
                break;
            }
        }

        //tabel header (begin)
        $objWorkSheetPpi->setCellValue('A7', 'No');
        $columnstart = 1;
        for ($i = 0; $i < count($dataKolom); $i++) {
            $objWorkSheetPpi->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart) . '7', $dataKolom[$i]['name']);
            $columnstart++;
        }
        $columnstart--;
        $columnstartLast = $columnstart;
        $objWorkSheetPpi->getStyle('A7:' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . '7')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetPpi->getStyle('A7:' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . '7')->applyFromArray($styleBorderFull, false);
        //tabel header (end)

        $rowStart = 8;
        $incre    = 0;
        foreach ($dataPpi['dataList'] as $val) {
            $val['no'] = $incre + 1;
            $objWorkSheetPpi->setCellValue('A' . $rowStart, $val['no']);

            $columnstart = 1;
            for ($i = 0; $i < count($dataKolom); $i++) {
                $objWorkSheetPpi->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart) . $rowStart, $val[$dataKolom[$i]['name']]);
                $columnstart++;
            }

            $objWorkSheetPpi->getStyle('A' . $rowStart . ':' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetPpi->getStyle('A' . $rowStart . ':' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }

        //====================== SHEET PPI (End)   ==============================//

        //=============== MULAI TULIS EXCEL (END) ===================================================================//

        //format nama untuk filename
        $dataFa['Name'] = str_replace(' ', '', $dataFa['Name']);
        $dataFa['Name'] = preg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $dataFa['Name']);
        $dataFa['Name'] = preg_replace("([\.]{2,})", '', $dataFa['Name']);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . date('YmdHis') . '_' . $dataFa['Name'] . '_PreICS_Detail.xlsx');
        header('Cache-Control: max-age=0');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;

    }

    public function ims_event_detail_not_comply_get($IMSID)
    {
        // ini_set('display_errors',true);
        // error_reporting(E_ALL);
        ini_set('memory_limit', '-1');

        $IMSID = (int) $IMSID;

        //data yg diperlukan (begin)
        $dataIms               = $this->mims->getIMSDetail($IMSID);
        $dataPetaniNotComply   = $this->mims->getDataPetaniNotComply($IMSID);
        $dataGardenNotComply   = $this->mims->getDataGardenNotComply($IMSID);
        $dataAuditLogNotComply = $this->mims->getDataAuditLogNotComply($IMSID);
        //data yg diperlukan (end)

        require_once 'application/third_party/PHPExcel18/PHPExcel.php';
        require_once 'application/third_party/PHPExcel18/PHPExcel/IOFactory.php';

        //=============== MULAI TULIS EXCEL (BEGIN) ===================================================================//

        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("PT Koltiva")
            ->setLastModifiedBy("PT Koltiva")
            ->setTitle("Pre AFL - Not Comply Data")
            ->setSubject("Pre AFL - Not Comply Data")
            ->setDescription("Pre AFL - Not Comply Data")
            ->setKeywords("Pre AFL - Not Comply Data")
            ->setCategory("Pre AFL - Not Comply Data");

        //set style  (begin)
        $styleFont = array(
            'font'      => array(
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

        $styleFontBoldMainTitle = array(
            'font'      => array(
                'name' => 'Arial',
                'size' => '11',
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
        );

        $styleFontBoldTitle = array(
            'font'      => array(
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
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '8DB4E3'),
            ),
        );
        $styleFontBoldBgRedCenter = array(
            'font'      => array(
                'name' => 'Arial',
                'size' => '9',
                'bold' => true,
            ),
            'fill'      => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'C0504D'),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $styleBorderFull = array(
            'borders' => array(
                'left'   => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'right'  => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'top'    => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );
        //set style  (end)

        //====================== SHEET PETANI (Begin) ==============================//

        //create sheet
        $objWorkSheetPetani = $objPHPExcel->createSheet(0);
        $objWorkSheetPetani->setTitle('Farmer');

        //set width column
        $objWorkSheetPetani->getColumnDimension('B')->setWidth(11);
        $objWorkSheetPetani->getColumnDimension('C')->setWidth(15);
        $objWorkSheetPetani->getColumnDimension('D')->setWidth(30);

        $objWorkSheetPetani->setCellValue('B3', '[' . $dataIms['CertProgName'] . '] ' . $dataIms['CertEventName']);
        $objWorkSheetPetani->getStyle('B3')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetPetani->mergeCells('B3:E3');

        $objWorkSheetPetani->setCellValue('B5', lang('Farmer Not Active'));
        $objWorkSheetPetani->getStyle('B5')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetPetani->mergeCells('B5:E5');

        //data kolom
        $dataKolom = array();
        $increTemp = 0;
        foreach ($dataPetaniNotComply as $key => $value) {
            if ($key == 0) {
                foreach ($value as $key2 => $value2) {
                    $dataKolom[$increTemp]['name'] = $key2;
                    $increTemp++;
                }
            } else {
                break;
            }
        }

        //tabel header (begin)
        $objWorkSheetPetani->setCellValue('A6', 'No');
        $columnstart = 1;
        for ($i = 0; $i < count($dataKolom); $i++) {
            $objWorkSheetPetani->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart) . '6', $dataKolom[$i]['name']);
            $columnstart++;
        }
        $columnstart--;
        $columnstartLast = $columnstart;
        $objWorkSheetPetani->getStyle('A6:' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . '6')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetPetani->getStyle('A6:' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . '6')->applyFromArray($styleBorderFull, false);
        //tabel header (end)

        $rowStart = 7;
        $incre    = 0;
        foreach ($dataPetaniNotComply as $val) {
            $val['no'] = $incre + 1;
            $objWorkSheetPetani->setCellValue('A' . $rowStart, $val['no']);

            $columnstart = 1;
            for ($i = 0; $i < count($dataKolom); $i++) {
                $objWorkSheetPetani->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart) . $rowStart, $val[$dataKolom[$i]['name']]);
                $columnstart++;
            }

            $objWorkSheetPetani->getStyle('A' . $rowStart . ':' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetPetani->getStyle('A' . $rowStart . ':' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }

        //====================== SHEET PETANI (End) ==============================//

        //====================== SHEET GARDEN (Begin) ==============================//

        //create sheet
        $objWorkSheetGarden = $objPHPExcel->createSheet(1);
        $objWorkSheetGarden->setTitle('Garden');

        //set width column
        $objWorkSheetGarden->getColumnDimension('B')->setWidth(11);
        $objWorkSheetGarden->getColumnDimension('C')->setWidth(15);
        $objWorkSheetGarden->getColumnDimension('D')->setWidth(30);

        $objWorkSheetGarden->setCellValue('B3', '[' . $dataIms['CertProgName'] . '] ' . $dataIms['CertEventName']);
        $objWorkSheetGarden->getStyle('B3')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetGarden->mergeCells('B3:E3');

        $objWorkSheetGarden->setCellValue('B5', lang('Garden Not Active'));
        $objWorkSheetGarden->getStyle('B5')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetGarden->mergeCells('B5:E5');

        //data kolom
        $dataKolom = array();
        $increTemp = 0;
        foreach ($dataGardenNotComply as $key => $value) {
            if ($key == 0) {
                foreach ($value as $key2 => $value2) {
                    $dataKolom[$increTemp]['name'] = $key2;
                    $increTemp++;
                }
            } else {
                break;
            }
        }

        //tabel header (begin)
        $objWorkSheetGarden->setCellValue('A6', 'No');
        $columnstart = 1;
        for ($i = 0; $i < count($dataKolom); $i++) {
            $objWorkSheetGarden->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart) . '6', $dataKolom[$i]['name']);
            $columnstart++;
        }
        $columnstart--;
        $columnstartLast = $columnstart;
        $objWorkSheetGarden->getStyle('A6:' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . '6')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetGarden->getStyle('A6:' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . '6')->applyFromArray($styleBorderFull, false);
        //tabel header (end)

        $rowStart = 7;
        $incre    = 0;
        foreach ($dataGardenNotComply as $val) {
            $val['no'] = $incre + 1;
            $objWorkSheetGarden->setCellValue('A' . $rowStart, $val['no']);

            $columnstart = 1;
            for ($i = 0; $i < count($dataKolom); $i++) {
                $objWorkSheetGarden->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart) . $rowStart, $val[$dataKolom[$i]['name']]);
                $columnstart++;
            }

            $objWorkSheetGarden->getStyle('A' . $rowStart . ':' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetGarden->getStyle('A' . $rowStart . ':' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }

        //====================== SHEET GARDEN (End) ==============================//

        //====================== SHEET AUDIT LOG (Begin) ==============================//

        //create sheet
        $objWorkSheetGarden = $objPHPExcel->createSheet(2);
        $objWorkSheetGarden->setTitle('Audit Log');

        //set width column
        $objWorkSheetGarden->getColumnDimension('B')->setWidth(11);
        $objWorkSheetGarden->getColumnDimension('C')->setWidth(15);
        $objWorkSheetGarden->getColumnDimension('D')->setWidth(30);

        $objWorkSheetGarden->setCellValue('B3', '[' . $dataIms['CertProgName'] . '] ' . $dataIms['CertEventName']);
        $objWorkSheetGarden->getStyle('B3')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetGarden->mergeCells('B3:E3');

        $objWorkSheetGarden->setCellValue('B5', lang('Certification Audit Log Not Comply'));
        $objWorkSheetGarden->getStyle('B5')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetGarden->mergeCells('B5:E5');

        //data kolom
        $dataKolom = array();
        $increTemp = 0;
        foreach ($dataAuditLogNotComply as $key => $value) {
            if ($key == 0) {
                foreach ($value as $key2 => $value2) {
                    $dataKolom[$increTemp]['name'] = $key2;
                    $increTemp++;
                }
            } else {
                break;
            }
        }

        //tabel header (begin)
        $objWorkSheetGarden->setCellValue('A6', 'No');
        $columnstart = 1;
        for ($i = 0; $i < count($dataKolom); $i++) {
            $objWorkSheetGarden->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart) . '6', $dataKolom[$i]['name']);
            $columnstart++;
        }
        $columnstart--;
        $columnstartLast = $columnstart;
        $objWorkSheetGarden->getStyle('A6:' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . '6')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetGarden->getStyle('A6:' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . '6')->applyFromArray($styleBorderFull, false);
        //tabel header (end)

        $rowStart = 7;
        $incre    = 0;
        foreach ($dataAuditLogNotComply as $val) {
            $val['no'] = $incre + 1;
            $objWorkSheetGarden->setCellValue('A' . $rowStart, $val['no']);

            $columnstart = 1;
            for ($i = 0; $i < count($dataKolom); $i++) {
                $objWorkSheetGarden->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnstart) . $rowStart, $val[$dataKolom[$i]['name']]);
                $columnstart++;
            }

            $objWorkSheetGarden->getStyle('A' . $rowStart . ':' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetGarden->getStyle('A' . $rowStart . ':' . PHPExcel_Cell::stringFromColumnIndex($columnstartLast) . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }

        //====================== SHEET AUDIT LOG (End) ==============================//

        //=============== MULAI TULIS EXCEL (END) ===================================================================//

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . date('YmdHis') . '_PreICS_NotComplyList.xlsx');
        header('Cache-Control: max-age=0');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function ims_summary_fa_data_col_export_get($IMSID)
    {
        $IMSID = (int) $IMSID;
        ini_set('memory_limit', '-1');

        //data yg diperlukan (begin)
        $dataIms  = $this->mims->getIMSDetail($IMSID);
        $dataList = $this->mims->ImsEventDetailSummaryFaTable($IMSID);
        //data yg diperlukan (end)

        require_once 'application/third_party/PHPExcel18/PHPExcel.php';
        require_once 'application/third_party/PHPExcel18/PHPExcel/IOFactory.php';

        //=============== MULAI TULIS EXCEL (BEGIN) ===================================================================//

        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("PT Koltiva")
            ->setLastModifiedBy("PT Koltiva")
            ->setTitle("FA Data Collection Progress - IMS")
            ->setSubject("FA Data Collection Progress - IMS")
            ->setDescription("FA Data Collection Progress - IMS")
            ->setKeywords("FA Data Collection Progress - IMS")
            ->setCategory("FA Data Collection Progress - IMS");

        //set style  (begin)
        $styleFont = array(
            'font'      => array(
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

        $styleFontBoldMainTitle = array(
            'font'      => array(
                'name' => 'Arial',
                'size' => '11',
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
        );

        $styleFontBoldTitle = array(
            'font'      => array(
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
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '8DB4E3'),
            ),
        );
        $styleFontBoldBgRedCenter = array(
            'font'      => array(
                'name' => 'Arial',
                'size' => '9',
                'bold' => true,
            ),
            'fill'      => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'C0504D'),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $styleBorderFull = array(
            'borders' => array(
                'left'   => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'right'  => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'top'    => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );
        //set style  (end)

        //====================== SHEET PETANI (Begin) ==============================//

        //create sheet
        $objWorkSheetPetani = $objPHPExcel->createSheet(0);
        $objWorkSheetPetani->setTitle('Summary');

        //set width column
        $objWorkSheetPetani->getColumnDimension('B')->setWidth(18);
        $objWorkSheetPetani->getColumnDimension('C')->setWidth(13);
        $objWorkSheetPetani->getColumnDimension('D')->setWidth(15);
        $objWorkSheetPetani->getColumnDimension('E')->setWidth(19);
        $objWorkSheetPetani->getColumnDimension('F')->setWidth(23);
        $objWorkSheetPetani->getColumnDimension('G')->setWidth(13);
        $objWorkSheetPetani->getColumnDimension('H')->setWidth(23);
        $objWorkSheetPetani->getColumnDimension('I')->setWidth(13);
        $objWorkSheetPetani->getColumnDimension('J')->setWidth(13);
        $objWorkSheetPetani->getColumnDimension('K')->setWidth(13);
        $objWorkSheetPetani->getColumnDimension('L')->setWidth(13);
        $objWorkSheetPetani->getColumnDimension('M')->setWidth(13);

        $objWorkSheetPetani->setCellValue('B3', '[' . $dataIms['CertProgName'] . '] ' . $dataIms['CertEventName']);
        $objWorkSheetPetani->getStyle('B3')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetPetani->mergeCells('B3:E3');

        $objWorkSheetPetani->setCellValue('B4', lang('Field Agent Data Collection Summary'));
        $objWorkSheetPetani->getStyle('B4')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetPetani->mergeCells('B4:E4');

        //tabel header
        $objWorkSheetPetani->setCellValue('A6', 'No');
        $objWorkSheetPetani->setCellValue('B6', lang('Field Agent'));
        $objWorkSheetPetani->setCellValue('C6', lang('Farmer Target'));
        $objWorkSheetPetani->setCellValue('D6', lang('Farmer Visited'));
        $objWorkSheetPetani->setCellValue('E6', lang('Farmer Updated'));
        $objWorkSheetPetani->setCellValue('F6', lang('Farmer with Photo'));
        $objWorkSheetPetani->setCellValue('G6', lang('Farmer\'s Family & Labour'));
        $objWorkSheetPetani->setCellValue('H6', lang('Garden'));
        $objWorkSheetPetani->setCellValue('I6', lang('Cocoa Farm with Polygon'));
        $objWorkSheetPetani->setCellValue('J6', lang('Post Harvest'));
        $objWorkSheetPetani->setCellValue('K6', lang('Certification'));
        $objWorkSheetPetani->setCellValue('L6', lang('Audit Log'));
        $objWorkSheetPetani->setCellValue('M6', lang('PPI'));
        $objWorkSheetPetani->getStyle('A6:M6')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetPetani->getStyle('A6:M6')->applyFromArray($styleBorderFull, false);

        $rowStart = 7;
        $incre    = 0;
        foreach ($dataList as $val) {
            $val['no'] = $incre + 1;

            $objWorkSheetPetani->setCellValue('A' . $rowStart, $val['no']);
            $objWorkSheetPetani->setCellValue('B' . $rowStart, $val['FaLabel']);
            $objWorkSheetPetani->setCellValue('C' . $rowStart, $val['FarmerTarget']);
            $objWorkSheetPetani->setCellValue('D' . $rowStart, $val['FarmerVisited']);
            $objWorkSheetPetani->setCellValue('E' . $rowStart, $val['Farmer']);
            $objWorkSheetPetani->setCellValue('F' . $rowStart, $val['FarmerWithPhoto']);
            $objWorkSheetPetani->setCellValue('G' . $rowStart, $val['FarmerFamilyLabour']);
            $objWorkSheetPetani->setCellValue('H' . $rowStart, $val['Garden']);
            $objWorkSheetPetani->setCellValue('I' . $rowStart, $val['GardenWithPolygon']);
            $objWorkSheetPetani->setCellValue('J' . $rowStart, $val['PostHarvest']);
            $objWorkSheetPetani->setCellValue('K' . $rowStart, $val['Certification']);
            $objWorkSheetPetani->setCellValue('L' . $rowStart, $val['AuditLog']);
            $objWorkSheetPetani->setCellValue('M' . $rowStart, $val['PPI']);

            $objWorkSheetPetani->getStyle('A' . $rowStart . ':' . 'L' . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetPetani->getStyle('A' . $rowStart . ':' . 'L' . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }

        //====================== SHEET PETANI (End) ==============================//

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . date('YmdHis') . '_FA_DataCollection_Summary.xlsx');
        header('Cache-Control: max-age=0');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    /*=========================== Acquisition Process ====================================*/

    public function acq_pro_get_form_get()
    {
        $IMSID = (int) $this->get('IMSID');
        $data  = $this->mims->getAcqProForm($IMSID);
        $this->response($data, 200);
    }

    public function grid_coaching_activity_get(){
        $IMSID        = (int) $this->get('IMSID');

        //get param
        $pSearch = array(
            'textSearch'              => $this->get('textSearch'),
        );

        $sorting      = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';
        $data = $this->mims->getGridsCoachingActivity($IMSID, $this->get('start'), $this->get('limit'), $sortingField, $sortingDir, $pSearch);
        $this->response($data, 200);
    }

    public function getCoachingActivitybyID_get(){
        $ActivityID        = $this->get('ActivityID');
        $data = $this->mims->getCoachingActivitybyID($ActivityID);
        $this->response($data, 200);
    }

    public function getGridActivityNC_get(){
        $ActivityID        = $this->get('ActivityID');

        $sorting      = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';
        $data = $this->mims->getGridActivityNC($ActivityID, $this->get('start'), $this->get('limit'), $sortingField, $sortingDir);
        $this->response($data, 200);
    }

    public function acq_pro_grid_farmer_identification_get()
    {
        $IMSID        = (int) $this->get('IMSID');
        $StringSearch = $this->get('StringSearch');

        $sorting      = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $data = $this->mims->getAcqProGridFarmerIdentification($IMSID, $StringSearch, $this->get('start'), $this->get('limit'), $sortingField, $sortingDir);
        $this->response($data, 200);
    }

    public function acq_pro_grid_socialization_get()
    {
        $IMSID        = (int) $this->get('IMSID');
        $StringSearch = $this->get('StringSearch');

        $sorting      = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $data = $this->mims->getAcqProGridSocialization($IMSID, $StringSearch, $this->get('start'), $this->get('limit'), $sortingField, $sortingDir, 'js_grid');
        $this->response($data, 200);
    }

    public function acq_pro_grid_selection_get()
    {
        $IMSID          = (int) $this->get('IMSID');
        $StringSearch   = $this->get('StringSearch');
        $Participate    = (int) $this->get('Participate');
        $Recommendation = (int) $this->get('Recommendation');
        $Selection      = (int) $this->get('Selection');

        $sorting      = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $data = $this->mims->getAcqProGridSelection($IMSID, $StringSearch, $Participate, $Recommendation, $Selection, $this->get('start'), $this->get('limit'), $sortingField, $sortingDir, 'js_grid');
        $this->response($data, 200);
    }

    public function acq_pro_grid_selection_approved_get()
    {
        $IMSID        = (int) $this->get('IMSID');
        $StringSearch = $this->get('StringSearch');

        $sorting      = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $data = $this->mims->getAcqProGridSelectionApproved($IMSID, $StringSearch, $this->get('start'), $this->get('limit'), $sortingField, $sortingDir, 'js_grid');
        $this->response($data, 200);
    }

    public function acq_pro_grid_training_get()
    {
        $IMSID        = (int) $this->get('IMSID');
        $StringSearch = $this->get('StringSearch');

        $sorting      = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $data = $this->mims->getAcqProGridTraining($IMSID, $StringSearch, $this->get('start'), $this->get('limit'), $sortingField, $sortingDir, 'js_grid');
        $this->response($data, 200);
    }

    public function acq_pro_grid_training_approved_get()
    {
        $IMSID              = (int) $this->get('IMSID');
        $StringSearch       = $this->get('StringSearch');
        $DateApprovalSearch = $this->get('DateApprovalSearch');

        $sorting      = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $data = $this->mims->getAcqProGridTrainingApproved($IMSID, $StringSearch, $DateApprovalSearch, $this->get('start'), $this->get('limit'), $sortingField, $sortingDir, 'js_grid');
        $this->response($data, 200);
    }

    public function acq_pro_grid_candidate_preics_get(){
        $IMSID              = (int) $this->get('IMSID');
        $StringSearch       = $this->get('StringSearch');

        $sorting      = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $data = $this->mims->getAcqProGridCandidatePreICS($IMSID, $StringSearch, $this->get('start'), $this->get('limit'), $sortingField, $sortingDir, 'js_grid');
        $this->response($data, 200);
    }

    public function acq_pro_grid_training_info_detail_get()
    {
        $IMSID    = (int) $this->get('IMSID');
        $FarmerID = (int) $this->get('FarmerID');

        $data = $this->mims->getAcqProGridTrainingInfoDetail($IMSID, $FarmerID);
        $this->response($data, 200);
    }

    public function acq_process_generate_soc_sel_post()
    {
        $IMSID  = (int) $this->post('IMSID');
        $RemarkText  = $this->post('RemarkText');
        $proses = $this->mims->acqProcessGenSocSel($IMSID,$RemarkText);
        $this->response($proses, 200);
    }

    public function acq_process_generate_training_candidate_post()
    {
        $IMSID  = (int) $this->post('IMSID');
        $RemarkText  = $this->post('RemarkText');

        $proses = $this->mims->acqProcessGenTrainingCandidate($IMSID,$RemarkText);
        $this->response($proses, 200);
    }

    public function acq_process_to_candidate_post()
    {
        $IMSID    = (int) $this->post('IMSID');
        $FarmerID = (int) $this->post('FarmerID');

        $proses = $this->mims->acqProcessToCandidate($IMSID, $FarmerID);
        if ($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function acq_process_to_candidate_bulk_post()
    {
        $IMSID = (int) $this->post('IMSID');

        $proses = $this->mims->acqProcessToCandidateBulk($IMSID);
        if ($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function cmb_batch_training_get()
    {
        $data = $this->mims->getComboBatchTraining();
        $this->response($data, 200);
    }

    public function ims_event_staff_work_area_form_open_post(){
        $IMSStaffID = (int) $this->post('IMSStaffID');
        $data = $this->mims->ImsEventStaffWorkAreaFormOpen($IMSStaffID);
        $this->response($data, 200);
    }

    public function ims_event_staff_work_area_post(){
        $IMSStaffID = (int) $this->post('IMSStaffID');
        $WorkAreaID = (int) $this->post('WorkAreaID');

        $proses = $this->mims->ImsEventStaffWorkArea($IMSStaffID,$WorkAreaID);
        $this->response($proses, 200);
    }

    public function approval_socia_form_open_post()
    {
        $IMSID = (int) $this->post('IMSID');

        $data = $this->mims->approvalSociaFormOpenInfo($IMSID);
        $this->response($data, 200);
    }

    public function approval_socia_post()
    {
        $IMSID             = (int) $this->post('IMSID');
        $SocStatus         = (int) $this->post('SocStatus');
        $SocApprovalRemark = $this->post('SocApprovalRemark');

        if ($SocStatus == "1") {
            $proses = $this->mims->approvalSocia($IMSID, $SocApprovalRemark);
            if ($proses['success'] == true) {
                $return['success_val'] = true;
            } else {
                $return['success_val'] = false;
                $return['message']     = lang('Approval Process Failed');
            }
        } else {
            $return['success_val'] = false;
            $return['message']     = lang('To approve, status need to be locked!');
        }

        $this->response($return, 200);
    }

    public function approval_selec_form_open_post()
    {
        $IMSID = (int) $this->post('IMSID');

        $data = $this->mims->approvalSelecFormOpenInfo($IMSID);
        $this->response($data, 200);
    }

    public function approval_selec_post()
    {
        $IMSID             = (int) $this->post('IMSID');
        $ParPassSel        = (int) $this->post('ParPassSel');
        $SelApprovalRemark = $this->post('SelApprovalRemark');

        if ($ParPassSel > 0) {
            $proses = $this->mims->approvalSelec($IMSID, $SelApprovalRemark);
            if ($proses['success'] == true) {
                $return['success_val'] = true;
            } else {
                $return['success_val'] = false;
                $return['message']     = lang('Approval Process Failed');
            }
        } else {
            $return['success_val'] = false;
            $return['message']     = lang('There are no participant that passed selection');
        }

        $this->response($return, 200);
    }

    public function approval_train_form_open_post()
    {
        $IMSID = (int) $this->post('IMSID');

        $data = $this->mims->approvalTrainFormOpenInfo($IMSID);
        $this->response($data, 200);
    }

    public function approval_train_post()
    {
        $IMSID               = (int) $this->post('IMSID');
        $ParPassTrain        = (int) $this->post('ParPassTrain');
        $TrainApprovalRemark = $this->post('TrainApprovalRemark');

        if ($ParPassTrain > 0) {
            $proses = $this->mims->approvalTrain($IMSID, $TrainApprovalRemark);
            if ($proses['success'] == true) {
                $return['success_val'] = true;
            } else {
                $return['success_val'] = false;
                $return['message']     = lang('Approval Process Failed');
            }
        } else {
            $return['success_val'] = false;
            $return['message']     = lang('There are no participant that passed training');
        }

        $this->response($return, 200);
    }

    public function cek_acq_training_approval_get()
    {
        $IMSID = (int) $this->get('IMSID');

        $data = $this->mims->cekAcqTrainingApproval($IMSID);
        $this->response($data, 200);
    }

    public function signing_lock_soc_sel_form_open_post(){
        $IMSID = (int) $this->post('IMSID');
        $data = $this->mims->SigningLockSocSelFormOpen($IMSID);
        $this->response($data, 200);
    }

    public function signing_lock_soc_sel_post(){
        $IMSID = (int) $this->post('IMSID');
        $SigningLockSocSelRemark = $this->post('SigningLockSocSelRemark');

        $proses = $this->mims->SigningLockSocSel($IMSID,$SigningLockSocSelRemark);
        if($proses == true){
            $return['success_val'] = true;
        }else{
            $return['success_val'] = false;
            $return['message']     = lang('Signing Lock Process Failed');
        }
        $this->response($return, 200);
    }

    public function acq_process_to_candidate_from_selection_post(){
        $IMSID = (int) $this->post('IMSID');
        $RemarkText = $this->post('RemarkText');

        $proses = $this->mims->ProcessToCandidateFromSelection($IMSID,$RemarkText);
        $this->response($proses, 200);
    }

    public function signing_lock_gap_coc_form_open_post(){
        $IMSID = (int) $this->post('IMSID');
        $data = $this->mims->SigningLockGapCocFormOpen($IMSID);
        $this->response($data, 200);
    }

    public function signing_lock_gap_coc_post(){
        $IMSID = (int) $this->post('IMSID');
        $SigningLockGapCocRemark = $this->post('SigningLockGapCocRemark');

        $proses = $this->mims->SigningLockGapCoc($IMSID,$SigningLockGapCocRemark);
        if($proses == true){
            $return['success_val'] = true;
        }else{
            $return['success_val'] = false;
            $return['message']     = lang('Signing Lock Process Failed');
        }
        $this->response($return, 200);
    }

    public function acq_process_to_candidate_from_training_post(){
        $IMSID = (int) $this->post('IMSID');
        $RemarkText = $this->post('RemarkText');

        $proses = $this->mims->ProcessToCandidateFromTraining($IMSID,$RemarkText);
        $this->response($proses, 200);
    }

    /*====================================Export Excel Farmer Identification========================================*/
    public function exportFarmeridentification_get($IMSID)
    {
        //ini_set('display_errors',true); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        $row     = $this->mims->ExportHeaderrow($IMSID);
        $details = $this->mims->ExportExcelFarmerIdentificated($IMSID);

        set_time_limit(0);
        ini_set('memory_limit', '2500M');
        //echo "<pre>".print_r($details,1);exit;
        //$this->load->library('Excel', null, 'PHPExcel');
        //require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
        //require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';
        require_once 'application/third_party/PHPExcel18/PHPExcel.php';
        require_once 'application/third_party/PHPExcel18/PHPExcel/IOFactory.php';
        $object = new PHPExcel();

        // Set properties
        $object->getProperties()->setCreator("Koltiva Cocoatrace")
            ->setLastModifiedBy("Koltiva Cocoatrace")
            ->setCategory("Koltiva Cocoatrace");
        // Add some data

        $style_center = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $style_border = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );

        $title = "Farmer Identification-Event Name  - " . $row->CertEventName;

        $object->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $object->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $object->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $object->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $object->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $object->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $object->getActiveSheet()->getColumnDimension('G')->setWidth(10);
        $object->getActiveSheet()->mergeCells('A1:H1');
        $object->getActiveSheet()->getStyle("A1:H4")->applyFromArray($style_center);
        $object->getActiveSheet()->getStyle("A4:H4")->applyFromArray($style_border);
        $object->getActiveSheet()->getStyle("A1:H4")->getFont()->setBold(true);
        $object->setActiveSheetIndex(0)->setCellValue('A1', $title); //Judul
        $object->setActiveSheetIndex(0)->setCellValue('A4', 'Applicant ID');
        $object->setActiveSheetIndex(0)->setCellValue('B4', 'Applicant Name');
        $object->setActiveSheetIndex(0)->setCellValue('C4', 'Gender');
        $object->setActiveSheetIndex(0)->setCellValue('D4', 'District');
        $object->setActiveSheetIndex(0)->setCellValue('E4', 'SubDistrict');
        $object->setActiveSheetIndex(0)->setCellValue('F4', 'Village');
        $object->setActiveSheetIndex(0)->setCellValue('G4', 'Farmer Group');
        $object->setActiveSheetIndex(0)->setCellValue('H4', 'Status');
        $i       = 0;
        $counter = 5; //MULAI ROWS SETELAH JUDUL HEADER
        if ($details) {
            foreach ($details as $key => $val) {
                $object->getActiveSheet()->getStyle("A$counter:H$counter")->applyFromArray($style_border);
                $object->getActiveSheet()->setCellValue('A' . $counter, $val['ApplicantID']);
                $object->getActiveSheet()->setCellValue('B' . $counter, $val['ApplicantName']);
                $object->getActiveSheet()->setCellValue('C' . $counter, $val['Gender']);
                $object->getActiveSheet()->setCellValue('D' . $counter, $val['District']);
                $object->getActiveSheet()->setCellValue('E' . $counter, $val['SubDistrict']);
                $object->getActiveSheet()->setCellValue('F' . $counter, $val['Village']);
                $object->getActiveSheet()->setCellValue('G' . $counter, $val['FarmerGroup']);
                $object->getActiveSheet()->setCellValue('H' . $counter, $val['ApplicantStatus']);
                $i++;
                $counter++;
            }
        }
        $konter = $counter;
        $konter++;

        $object->setActiveSheetIndex(0);
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $title . '.xlsx');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    /*====================================Export Excel Farmer Socialization========================================*/
    public function exportFarmersocialization_get($IMSID, $CertEventName = null, $StringSearch = null)
    {
        ini_set('memory_limit', '-1');
        $CertEventName = urldecode($CertEventName);
        $StringSearch  = urldecode($StringSearch);

        $DataList = $this->mims->getAcqProGridSocialization($IMSID, $StringSearch, null, null, null, null, 'php_code');

        $this->load->library('Excel', null, 'PHPExcel');
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';
        $object = new PHPExcel();

        // Set properties
        $object->getProperties()->setCreator("Koltiva Cocoatrace")
            ->setLastModifiedBy("Koltiva Cocoatrace")
            ->setCategory("Koltiva Cocoatrace");
        // Add some data

        $style_center = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $style_border = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );

        $title = "Socialization - Event Name  - " . $CertEventName;

        $object->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $object->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $object->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $object->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $object->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $object->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $object->getActiveSheet()->getColumnDimension('H')->setWidth(10);
        $object->getActiveSheet()->getColumnDimension('I')->setWidth(10);
        $object->getActiveSheet()->getColumnDimension('J')->setWidth(10);
        $object->getActiveSheet()->mergeCells('A1:J1');
        $object->getActiveSheet()->getStyle("A1:J4")->applyFromArray($style_center);
        $object->getActiveSheet()->getStyle("A4:J4")->applyFromArray($style_border);
        $object->getActiveSheet()->getStyle("A1:J4")->getFont()->setBold(true);

        $object->setActiveSheetIndex(0)->setCellValue('A1', $title); //Judul
        $object->setActiveSheetIndex(0)->setCellValue('A4', 'No');
        $object->setActiveSheetIndex(0)->setCellValue('B4', lang('ID'));
        $object->setActiveSheetIndex(0)->setCellValue('C4', lang('Farmer ID'));
        $object->setActiveSheetIndex(0)->setCellValue('D4', lang('Name'));
        $object->setActiveSheetIndex(0)->setCellValue('E4', lang('Gender'));
        $object->setActiveSheetIndex(0)->setCellValue('F4', lang('Sub District'));
        $object->setActiveSheetIndex(0)->setCellValue('G4', lang('Village'));
        $object->setActiveSheetIndex(0)->setCellValue('H4', lang('Farmer Group'));
        $object->setActiveSheetIndex(0)->setCellValue('I4', lang('Event Name'));
        $object->setActiveSheetIndex(0)->setCellValue('J4', lang('Date of Socialization'));
        $object->setActiveSheetIndex(0)->setCellValue('K4', lang('Date Generated'));

        $i       = 1;
        $counter = 5; //MULAI ROWS SETELAH JUDUL HEADER
        if ($DataList) {
            foreach ($DataList as $key => $val) {

                $object->getActiveSheet()->getStyle("A$counter:J$counter")->applyFromArray($style_border);

                $object->getActiveSheet()->setCellValue('A' . $counter, $i);
                $object->getActiveSheet()->setCellValue('B' . $counter, $val['DisplayID']);
                $object->getActiveSheet()->setCellValue('C' . $counter, $val['DestObjID']);
                $object->getActiveSheet()->setCellValue('D' . $counter, $val['Name']);
                $object->getActiveSheet()->setCellValue('E' . $counter, $val['Gender']);
                $object->getActiveSheet()->setCellValue('F' . $counter, $val['SubDistrict']);
                $object->getActiveSheet()->setCellValue('G' . $counter, $val['Village']);
                $object->getActiveSheet()->setCellValue('H' . $counter, $val['FarmerGroup']);
                $object->getActiveSheet()->setCellValue('I' . $counter, $val['SocEventName']);
                $object->getActiveSheet()->setCellValue('J' . $counter, $val['DateOfSocialization']);
                $object->getActiveSheet()->setCellValue('K' . $counter, $val['DateGenerated']);

                $i++;
                $counter++;
            }
        }
        $konter = $counter;
        $konter++;

        $object->setActiveSheetIndex(0);
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $title . '.xlsx');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function exportFarmerGapCoc_get($IMSID, $CertEventName = null, $StringSearch = null)
    {
        ini_set('memory_limit', '-1');
        $CertEventName = urldecode($CertEventName);
        $StringSearch  = urldecode($StringSearch);

        $DataList = $this->mims->getAcqProGridTraining($IMSID, $StringSearch, null, null, null, null, 'php_code');

        $this->load->library('Excel', null, 'PHPExcel');
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';
        $object = new PHPExcel();

        // Set properties
        $object->getProperties()->setCreator("Koltiva Cocoatrace")
            ->setLastModifiedBy("Koltiva Cocoatrace")
            ->setCategory("Koltiva Cocoatrace");
        // Add some data

        $style_center = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $style_border = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );

        $title = "Socialization Training - Event Name  - " . $CertEventName;

        $object->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $object->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $object->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $object->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $object->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $object->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $object->getActiveSheet()->getColumnDimension('H')->setWidth(10);
        $object->getActiveSheet()->getColumnDimension('I')->setWidth(10);
        $object->getActiveSheet()->getColumnDimension('J')->setWidth(10);
        $object->getActiveSheet()->mergeCells('A1:M1');
        $object->getActiveSheet()->getStyle("A1:M4")->applyFromArray($style_center);
        $object->getActiveSheet()->getStyle("A4:M4")->applyFromArray($style_border);
        $object->getActiveSheet()->getStyle("A1:M4")->getFont()->setBold(true);

        $object->setActiveSheetIndex(0)->setCellValue('A1', $title); //Judul
        $object->setActiveSheetIndex(0)->setCellValue('A4', 'No');
        $object->setActiveSheetIndex(0)->setCellValue('B4', lang('Farmer ID'));
        $object->setActiveSheetIndex(0)->setCellValue('C4', lang('Farmer Name'));
        $object->setActiveSheetIndex(0)->setCellValue('D4', lang('Gender'));
        $object->setActiveSheetIndex(0)->setCellValue('E4', lang('Province'));
        $object->setActiveSheetIndex(0)->setCellValue('F4', lang('District'));
        $object->setActiveSheetIndex(0)->setCellValue('G4', lang('Sub District'));
        $object->setActiveSheetIndex(0)->setCellValue('H4', lang('Village'));
        $object->setActiveSheetIndex(0)->setCellValue('I4', lang('Farmer Group'));
        $object->setActiveSheetIndex(0)->setCellValue('J4', lang('Training Requirement'));
        $object->setActiveSheetIndex(0)->setCellValue('K4', lang('Percentage Attandance'));
        $object->setActiveSheetIndex(0)->setCellValue('L4', lang('Eligible Status'));
        $object->setActiveSheetIndex(0)->setCellValue('M4', lang('Date Generated'));

        $i       = 1;
        $counter = 5; //MULAI ROWS SETELAH JUDUL HEADER
        if ($DataList) {
            foreach ($DataList as $key => $val) {

                $object->getActiveSheet()->getStyle("A$counter:J$counter")->applyFromArray($style_border);

                $object->getActiveSheet()->setCellValue('A' . $counter, $i);
                $object->getActiveSheet()->setCellValue('B' . $counter, $val['FarmerID']);
                $object->getActiveSheet()->setCellValue('C' . $counter, $val['FarmerName']);
                $object->getActiveSheet()->setCellValue('D' . $counter, $val['Gender']);
                $object->getActiveSheet()->setCellValue('E' . $counter, $val['Province']);
                $object->getActiveSheet()->setCellValue('F' . $counter, $val['District']);
                $object->getActiveSheet()->setCellValue('G' . $counter, $val['SubDistrict']);
                $object->getActiveSheet()->setCellValue('H' . $counter, $val['Village']);
                $object->getActiveSheet()->setCellValue('I' . $counter, $val['FarmerGroup']);
                $object->getActiveSheet()->setCellValue('J' . $counter, $val['TrainingReq']);
                $object->getActiveSheet()->setCellValue('K' . $counter, $val['PercentageAttendance']);
                $object->getActiveSheet()->setCellValue('L' . $counter, $val['EligibleStatus']);
                $object->getActiveSheet()->setCellValue('M' . $counter, $val['DateGenerated']);

                $i++;
                $counter++;
            }
        }
        $konter = $counter;
        $konter++;

        $object->setActiveSheetIndex(0);
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $title . '.xlsx');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function exportFarmerGapCocApproved_get($IMSID, $CertEventName = null, $StringSearch = null, $DateApprovalSearch = null)
    {
        ini_set('memory_limit', '-1');
        $CertEventName      = urldecode($CertEventName);
        $StringSearch       = urldecode($StringSearch);
        $DateApprovalSearch = urldecode($DateApprovalSearch);

        if ($StringSearch == "kosong") {
            $StringSearch = null;
        }

        if ($DateApprovalSearch == "kosong") {
            $DateApprovalSearch = null;
        }

        $DataList = $this->mims->getAcqProGridTrainingApproved($IMSID, $StringSearch, $DateApprovalSearch, null, null, null, null, 'php_code');

        $this->load->library('Excel', null, 'PHPExcel');
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';
        $object = new PHPExcel();

        // Set properties
        $object->getProperties()->setCreator("Koltiva Cocoatrace")
            ->setLastModifiedBy("Koltiva Cocoatrace")
            ->setCategory("Koltiva Cocoatrace");
        // Add some data

        $style_center = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $style_border = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );

        $title = "Socialization Training Approved - Event Name  - " . $CertEventName;

        $object->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $object->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $object->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $object->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $object->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $object->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $object->getActiveSheet()->getColumnDimension('H')->setWidth(10);
        $object->getActiveSheet()->getColumnDimension('I')->setWidth(10);
        $object->getActiveSheet()->getColumnDimension('J')->setWidth(10);
        $object->getActiveSheet()->mergeCells('A1:J1');
        $object->getActiveSheet()->getStyle("A1:J4")->applyFromArray($style_center);
        $object->getActiveSheet()->getStyle("A4:J4")->applyFromArray($style_border);
        $object->getActiveSheet()->getStyle("A1:J4")->getFont()->setBold(true);

        $object->setActiveSheetIndex(0)->setCellValue('A1', $title); //Judul
        $object->setActiveSheetIndex(0)->setCellValue('A4', 'No');
        $object->setActiveSheetIndex(0)->setCellValue('B4', lang('Farmer ID'));
        $object->setActiveSheetIndex(0)->setCellValue('C4', lang('Farmer Name'));
        $object->setActiveSheetIndex(0)->setCellValue('D4', lang('Gender'));
        $object->setActiveSheetIndex(0)->setCellValue('E4', lang('Farmer Group'));
        $object->setActiveSheetIndex(0)->setCellValue('F4', lang('Training Requirement'));
        $object->setActiveSheetIndex(0)->setCellValue('G4', lang('Percentage Attandance'));
        $object->setActiveSheetIndex(0)->setCellValue('H4', lang('Approval Remark'));
        $object->setActiveSheetIndex(0)->setCellValue('I4', lang('Approval By'));
        $object->setActiveSheetIndex(0)->setCellValue('J4', lang('Approval Date'));

        $i       = 1;
        $counter = 5; //MULAI ROWS SETELAH JUDUL HEADER
        if ($DataList) {
            foreach ($DataList as $key => $val) {

                $object->getActiveSheet()->getStyle("A$counter:J$counter")->applyFromArray($style_border);

                $object->getActiveSheet()->setCellValue('A' . $counter, $i);
                $object->getActiveSheet()->setCellValue('B' . $counter, $val['FarmerID']);
                $object->getActiveSheet()->setCellValue('C' . $counter, $val['FarmerName']);
                $object->getActiveSheet()->setCellValue('D' . $counter, $val['Gender']);
                $object->getActiveSheet()->setCellValue('E' . $counter, $val['FarmerGroup']);
                $object->getActiveSheet()->setCellValue('F' . $counter, $val['TrainingReq']);
                $object->getActiveSheet()->setCellValue('G' . $counter, $val['PercentageAttendance']);
                $object->getActiveSheet()->setCellValue('H' . $counter, $val['AppRemark']);
                $object->getActiveSheet()->setCellValue('I' . $counter, $val['AppBy']);
                $object->getActiveSheet()->setCellValue('J' . $counter, $val['DateApproval']);

                $i++;
                $counter++;
            }
        }
        $konter = $counter;
        $konter++;

        $object->setActiveSheetIndex(0);
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $title . '.xlsx');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function exportFarmerCandidatePreICS_get($IMSID,$StringSearch = null){
        ini_set('memory_limit', '-1');
        $StringSearch       = urldecode($StringSearch);
        if ($StringSearch == "kosong") {
            $StringSearch = null;
        }

        $DataList = $this->mims->getAcqProGridCandidatePreICS($IMSID, $StringSearch, null, null, null, null, 'php_code');
        $DataIMS = $this->mims->getIMSDetail($IMSID);

        $this->load->library('Excel', null, 'PHPExcel');
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';
        $object = new PHPExcel();

        // Set properties
        $object->getProperties()->setCreator("Koltiva Cocoatrace")
            ->setLastModifiedBy("Koltiva Cocoatrace")
            ->setCategory("Koltiva Cocoatrace");
        // Add some data

        $style_center = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $style_border = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );

        $title = "Socialization Training Approved - Event Name  - " . $DataIMS['CertEventName'];

        $object->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $object->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $object->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $object->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $object->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $object->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $object->getActiveSheet()->getColumnDimension('H')->setWidth(10);
        $object->getActiveSheet()->getColumnDimension('I')->setWidth(10);
        $object->getActiveSheet()->getColumnDimension('J')->setWidth(10);
        $object->getActiveSheet()->mergeCells('A1:H1');
        $object->getActiveSheet()->getStyle("A1:H4")->applyFromArray($style_center);
        $object->getActiveSheet()->getStyle("A4:H4")->applyFromArray($style_border);
        $object->getActiveSheet()->getStyle("A1:H4")->getFont()->setBold(true);

        $object->setActiveSheetIndex(0)->setCellValue('A1', $title); //Judul
        $object->setActiveSheetIndex(0)->setCellValue('A4', 'No');
        $object->setActiveSheetIndex(0)->setCellValue('B4', lang('Farmer ID'));
        $object->setActiveSheetIndex(0)->setCellValue('C4', lang('Farmer Name'));
        $object->setActiveSheetIndex(0)->setCellValue('D4', lang('Gender'));
        $object->setActiveSheetIndex(0)->setCellValue('E4', lang('Farmer Group'));
        $object->setActiveSheetIndex(0)->setCellValue('F4', lang('Training Percentage'));
        $object->setActiveSheetIndex(0)->setCellValue('G4', lang('Comply for Audit'));
        $object->setActiveSheetIndex(0)->setCellValue('H4', lang('Remark'));

        $i       = 1;
        $counter = 5; //MULAI ROWS SETELAH JUDUL HEADER
        if ($DataList) {
            foreach ($DataList as $key => $val) {

                $object->getActiveSheet()->getStyle("A$counter:H$counter")->applyFromArray($style_border);

                $object->getActiveSheet()->setCellValue('A' . $counter, $i);
                $object->getActiveSheet()->setCellValue('B' . $counter, $val['FarmerID']);
                $object->getActiveSheet()->setCellValue('C' . $counter, $val['FarmerName']);
                $object->getActiveSheet()->setCellValue('D' . $counter, $val['Gender']);
                $object->getActiveSheet()->setCellValue('E' . $counter, $val['FarmerGroup']);
                $object->getActiveSheet()->setCellValue('F' . $counter, $val['TrainingPercentage']);
                $object->getActiveSheet()->setCellValue('G' . $counter, $val['StatusComply']);
                $object->getActiveSheet()->setCellValue('H' . $counter, $val['AuditRemark']);

                $i++;
                $counter++;
            }
        }
        $konter = $counter;
        $konter++;

        $object->setActiveSheetIndex(0);
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Candidate_PreICS_' . $IMSID . '.xlsx');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function exportFarmerSelectionParticipateInSocialization_get($IMSID){
        ini_set('display_errors',true);
        error_reporting(E_ALL);
        ini_set('memory_limit', '-1');

        $IMSID  = (int) $IMSID;

        //Data yg diperlukan (Begin)
        $DataIMS = $this->mims->getIMSDetail($IMSID);
        $DataList = $this->mims->GetDataListFarmerSelectionParticipateInSocialization($IMSID);
        //Data yg diperlukan (End)

        require_once 'application/third_party/PHPExcel18/PHPExcel.php';
        require_once 'application/third_party/PHPExcel18/PHPExcel/IOFactory.php';

        //=============== MULAI TULIS EXCEL (BEGIN) ===================================================================//
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("PT Koltiva")
            ->setLastModifiedBy("PT Koltiva")
            ->setTitle("Participate in Socialization Data")
            ->setSubject("Participate in Socialization Data")
            ->setDescription("Participate in Socialization Data")
            ->setKeywords("Participate in Socialization Data")
            ->setCategory("Participate in Socialization Data");

        //set style  (begin)
        $styleFont = array(
            'font'      => array(
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

        $styleFontBoldMainTitle = array(
            'font'      => array(
                'name' => 'Arial',
                'size' => '11',
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
        );

        $styleFontBoldTitle = array(
            'font'      => array(
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
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '8DB4E3'),
            ),
        );
        $styleFontBoldBgRedCenter = array(
            'font'      => array(
                'name' => 'Arial',
                'size' => '9',
                'bold' => true,
            ),
            'fill'      => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'C0504D'),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $styleBgPink = array(
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'FDA182'),
            ),
        );
        $styleBgYellow = array(
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'FDE492'),
            ),
        );

        $styleBorderFull = array(
            'borders' => array(
                'left'   => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'right'  => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'top'    => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );
        //set style  (end)

        $objWorkSheetSummary = $objPHPExcel->createSheet(0);
        $objWorkSheetSummary->setTitle('Data');

        $objWorkSheetSummary->setCellValue('B2', '[' . $DataIMS['CertProgName'] . '] ' . $DataIMS['CertEventName']);
        $objWorkSheetSummary->getStyle('B2')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetSummary->mergeCells('B2:F2');

        $objWorkSheetSummary->setCellValue('B3', 'Participants in Socialization');
        $objWorkSheetSummary->getStyle('B3')->applyFromArray($styleFontBoldTitle);
        $objWorkSheetSummary->mergeCells('B3:F3');

        //Tabel Header
        $objWorkSheetSummary->setCellValue('A5', lang('No'));
        $objWorkSheetSummary->setCellValue('B5', lang('ID'));
        $objWorkSheetSummary->setCellValue('C5', lang('Farmer ID'));
        $objWorkSheetSummary->setCellValue('D5', lang('Name'));
        $objWorkSheetSummary->setCellValue('E5', lang('Type'));
        $objWorkSheetSummary->setCellValue('F5', lang('Gender'));
        $objWorkSheetSummary->setCellValue('G5', lang('Sub District'));
        $objWorkSheetSummary->setCellValue('H5', lang('Village'));
        $objWorkSheetSummary->setCellValue('I5', lang('Farmer Group'));

        $objWorkSheetSummary->setCellValue('I5', lang('Soc Event Name'));
        $objWorkSheetSummary->setCellValue('J5', lang('Soc Event Start'));
        $objWorkSheetSummary->setCellValue('K5', lang('Soc Event End'));

        $objWorkSheetSummary->setCellValue('L5', lang('Participate in Socialization'));
        $objWorkSheetSummary->setCellValue('M5', lang('Recommendation Status'));
        $objWorkSheetSummary->setCellValue('N5', lang('Selection Status'));
        $objWorkSheetSummary->setCellValue('O5', lang('Has Been Approved'));
        $objWorkSheetSummary->getStyle('A5:O5')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetSummary->getStyle('A5:O5')->applyFromArray($styleBorderFull, false);

        //Tabel Data
        $RowProcess = 6;
        for ($i=0; $i < count($DataList); $i++) {
            $incre = $i+1;

            $objWorkSheetSummary->setCellValue('A'.$RowProcess, $incre);
            $objWorkSheetSummary->setCellValue('B'.$RowProcess, $DataList[$i]['DisplayID']);
            $objWorkSheetSummary->setCellValue('C'.$RowProcess, $DataList[$i]['DestObjID']);
            $objWorkSheetSummary->setCellValue('D'.$RowProcess, $DataList[$i]['Name']);
            $objWorkSheetSummary->setCellValue('E'.$RowProcess, $DataList[$i]['ParticipantType']);
            $objWorkSheetSummary->setCellValue('F'.$RowProcess, $DataList[$i]['Gender']);
            $objWorkSheetSummary->setCellValue('G'.$RowProcess, $DataList[$i]['SubDistrict']);
            $objWorkSheetSummary->setCellValue('H'.$RowProcess, $DataList[$i]['Village']);
            $objWorkSheetSummary->setCellValue('I'.$RowProcess, $DataList[$i]['FarmerGroup']);

            $objWorkSheetSummary->setCellValue('I'.$RowProcess, $DataList[$i]['EventName']);
            $objWorkSheetSummary->setCellValue('J'.$RowProcess, $DataList[$i]['EventStart']);
            $objWorkSheetSummary->setCellValue('K'.$RowProcess, $DataList[$i]['EventEnd']);

            $objWorkSheetSummary->setCellValue('L'.$RowProcess, $DataList[$i]['ParticipateInSocialization']);
            $objWorkSheetSummary->setCellValue('M'.$RowProcess, $DataList[$i]['Recommendation']);
            $objWorkSheetSummary->setCellValue('N'.$RowProcess, $DataList[$i]['SelectionStatus']);
            $objWorkSheetSummary->setCellValue('O'.$RowProcess, $DataList[$i]['HasBeenApproved']);

            $objWorkSheetSummary->getStyle('A' . $RowProcess . ':O' . $RowProcess)->applyFromArray($styleFont);
            $objWorkSheetSummary->getStyle('A' . $RowProcess . ':O' . $RowProcess)->applyFromArray($styleBorderFull, false);
            $RowProcess++;
        }
        //=============== MULAI TULIS EXCEL (END) ===================================================================//

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . date('YmdHis') . '_' . $IMSID . '_ParticipateInSocialization.xlsx');
        header('Cache-Control: max-age=0');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    /*====================================Export Excel Farmer Selection========================================*/
    public function exportFarmerSelection_get($IMSID, $CertEventName = null, $Participate = null, $Recommendation = null, $Selection = null, $StringSearch = null)
    {
        ini_set('memory_limit', '-1');
        $CertEventName  = urldecode($CertEventName);
        $StringSearch   = urldecode($StringSearch);
        $Participate    = (int) $Participate;
        $Recommendation = (int) $Recommendation;
        $Selection      = (int) $Selection;

        $DataList = $this->mims->getAcqProGridSelection($IMSID, $StringSearch, $Participate, $Recommendation, $Selection, null, null, null, null, 'php_code');

        $this->load->library('Excel', null, 'PHPExcel');
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';
        $object = new PHPExcel();

        // Set properties
        $object->getProperties()->setCreator("Koltiva Cocoatrace")
            ->setLastModifiedBy("Koltiva Cocoatrace")
            ->setCategory("Koltiva Cocoatrace");
        // Add some data

        $style_center = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $style_border = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );

        $title = "Socialization Selection - Event Name  - " . $CertEventName;

        $object->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $object->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $object->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $object->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $object->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $object->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $object->getActiveSheet()->getColumnDimension('H')->setWidth(10);
        $object->getActiveSheet()->getColumnDimension('I')->setWidth(10);
        $object->getActiveSheet()->getColumnDimension('J')->setWidth(10);
        $object->getActiveSheet()->mergeCells('A1:N1');
        $object->getActiveSheet()->getStyle("A1:N4")->applyFromArray($style_center);
        $object->getActiveSheet()->getStyle("A4:N4")->applyFromArray($style_border);
        $object->getActiveSheet()->getStyle("A1:N4")->getFont()->setBold(true);

        $object->setActiveSheetIndex(0)->setCellValue('A1', $title); //Judul
        $object->setActiveSheetIndex(0)->setCellValue('A4', 'No');
        $object->setActiveSheetIndex(0)->setCellValue('B4', lang('ID'));
        $object->setActiveSheetIndex(0)->setCellValue('C4', lang('Farmer ID'));
        $object->setActiveSheetIndex(0)->setCellValue('D4', lang('Name'));
        $object->setActiveSheetIndex(0)->setCellValue('E4', lang('Gender'));
        $object->setActiveSheetIndex(0)->setCellValue('F4', lang('Province'));
        $object->setActiveSheetIndex(0)->setCellValue('G4', lang('District'));
        $object->setActiveSheetIndex(0)->setCellValue('H4', lang('Sub District'));
        $object->setActiveSheetIndex(0)->setCellValue('I4', lang('Village'));
        $object->setActiveSheetIndex(0)->setCellValue('J4', lang('Farmer Group'));
        $object->setActiveSheetIndex(0)->setCellValue('K4', lang('Participate in Socialization'));
        $object->setActiveSheetIndex(0)->setCellValue('L4', lang('Recommendation'));
        $object->setActiveSheetIndex(0)->setCellValue('M4', lang('Selection Status'));
        $object->setActiveSheetIndex(0)->setCellValue('N4', lang('Type'));
        $object->setActiveSheetIndex(0)->setCellValue('O4', lang('Date Generated'));

        $i       = 1;
        $counter = 5; //MULAI ROWS SETELAH JUDUL HEADER
        if ($DataList) {
            foreach ($DataList as $key => $val) {

                $object->getActiveSheet()->getStyle("A$counter:J$counter")->applyFromArray($style_border);

                $object->getActiveSheet()->setCellValue('A' . $counter, $i);
                $object->getActiveSheet()->setCellValue('B' . $counter, $val['DisplayID']);
                $object->getActiveSheet()->setCellValue('C' . $counter, $val['DestObjID']);
                $object->getActiveSheet()->setCellValue('D' . $counter, $val['Name']);
                $object->getActiveSheet()->setCellValue('E' . $counter, $val['Gender']);
                $object->getActiveSheet()->setCellValue('F' . $counter, $val['Province']);
                $object->getActiveSheet()->setCellValue('G' . $counter, $val['District']);
                $object->getActiveSheet()->setCellValue('H' . $counter, $val['SubDistrict']);
                $object->getActiveSheet()->setCellValue('I' . $counter, $val['Village']);
                $object->getActiveSheet()->setCellValue('J' . $counter, $val['FarmerGroup']);
                $object->getActiveSheet()->setCellValue('K' . $counter, $val['ParticipateInSocialization']);
                $object->getActiveSheet()->setCellValue('L' . $counter, $val['Recommendation']);
                $object->getActiveSheet()->setCellValue('M' . $counter, $val['SelectionStatus']);
                $object->getActiveSheet()->setCellValue('N' . $counter, $val['ParticipantType']);
                $object->getActiveSheet()->setCellValue('O' . $counter, $val['DateGenerated']);

                $i++;
                $counter++;
            }
        }
        $konter = $counter;
        $konter++;

        $object->setActiveSheetIndex(0);
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $title . '.xlsx');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function exportFarmerSelectionApproved_get($IMSID, $CertEventName = null, $StringSearch = null)
    {
        ini_set('memory_limit', '-1');
        $CertEventName = urldecode($CertEventName);
        $StringSearch  = urldecode($StringSearch);

        $DataList = $this->mims->getAcqProGridSelectionApproved($IMSID, $StringSearch, null, null, null, null, 'php_code');

        $this->load->library('Excel', null, 'PHPExcel');
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';
        $object = new PHPExcel();

        // Set properties
        $object->getProperties()->setCreator("Koltiva Cocoatrace")
            ->setLastModifiedBy("Koltiva Cocoatrace")
            ->setCategory("Koltiva Cocoatrace");
        // Add some data

        $style_center = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $style_border = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );

        $title = "Socialization Selection Approved - Event Name  - " . $CertEventName;

        $object->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $object->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $object->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $object->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $object->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $object->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $object->getActiveSheet()->getColumnDimension('H')->setWidth(10);
        $object->getActiveSheet()->getColumnDimension('I')->setWidth(10);
        $object->getActiveSheet()->getColumnDimension('J')->setWidth(10);
        $object->getActiveSheet()->mergeCells('A1:M1');
        $object->getActiveSheet()->getStyle("A1:M4")->applyFromArray($style_center);
        $object->getActiveSheet()->getStyle("A4:M4")->applyFromArray($style_border);
        $object->getActiveSheet()->getStyle("A1:M4")->getFont()->setBold(true);

        $object->setActiveSheetIndex(0)->setCellValue('A1', $title); //Judul
        $object->setActiveSheetIndex(0)->setCellValue('A4', 'No');
        $object->setActiveSheetIndex(0)->setCellValue('B4', lang('ID'));
        $object->setActiveSheetIndex(0)->setCellValue('C4', lang('Farmer ID'));
        $object->setActiveSheetIndex(0)->setCellValue('D4', lang('Name'));
        $object->setActiveSheetIndex(0)->setCellValue('E4', lang('Gender'));
        $object->setActiveSheetIndex(0)->setCellValue('F4', lang('Province'));
        $object->setActiveSheetIndex(0)->setCellValue('G4', lang('District'));
        $object->setActiveSheetIndex(0)->setCellValue('H4', lang('Sub District'));
        $object->setActiveSheetIndex(0)->setCellValue('I4', lang('Village'));
        $object->setActiveSheetIndex(0)->setCellValue('J4', lang('Farmer Group'));
        $object->setActiveSheetIndex(0)->setCellValue('K4', lang('Type'));
        $object->setActiveSheetIndex(0)->setCellValue('L4', lang('Approval Remark'));
        $object->setActiveSheetIndex(0)->setCellValue('M4', lang('Approval By'));
        $object->setActiveSheetIndex(0)->setCellValue('N4', lang('Date Approval'));

        $i       = 1;
        $counter = 5; //MULAI ROWS SETELAH JUDUL HEADER
        if ($DataList) {
            foreach ($DataList as $key => $val) {

                $object->getActiveSheet()->getStyle("A$counter:J$counter")->applyFromArray($style_border);

                $object->getActiveSheet()->setCellValue('A' . $counter, $i);
                $object->getActiveSheet()->setCellValue('B' . $counter, $val['DisplayID']);
                $object->getActiveSheet()->setCellValue('C' . $counter, $val['DestObjID']);
                $object->getActiveSheet()->setCellValue('D' . $counter, $val['Name']);
                $object->getActiveSheet()->setCellValue('E' . $counter, $val['Gender']);
                $object->getActiveSheet()->setCellValue('F' . $counter, $val['Province']);
                $object->getActiveSheet()->setCellValue('G' . $counter, $val['District']);
                $object->getActiveSheet()->setCellValue('H' . $counter, $val['SubDistrict']);
                $object->getActiveSheet()->setCellValue('I' . $counter, $val['Village']);
                $object->getActiveSheet()->setCellValue('J' . $counter, $val['FarmerGroup']);
                $object->getActiveSheet()->setCellValue('K' . $counter, $val['ParticipantType']);
                $object->getActiveSheet()->setCellValue('L' . $counter, $val['ApprovalRemark']);
                $object->getActiveSheet()->setCellValue('M' . $counter, $val['ApprovalBy']);
                $object->getActiveSheet()->setCellValue('N' . $counter, $val['DateApproval']);

                $i++;
                $counter++;
            }
        }
        $konter = $counter;
        $konter++;

        $object->setActiveSheetIndex(0);
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $title . '.xlsx');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    /*====================================Export Excel Farmer Selection========================================*/
    public function exportFarmergap_coc_get($IMSID)
    {
        $row     = $this->mims->ExportHeaderrow($IMSID);
        $details = $this->mims->ExportExcelFarmerGapCOC($IMSID);

        set_time_limit(0);
        ini_set('memory_limit', '2500M');
        //echo "<pre>".print_r($details,1);exit;
        $this->load->library('Excel', null, 'PHPExcel');
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';
        $object = new PHPExcel();

        // Set properties
        $object->getProperties()->setCreator("Koltiva Cocoatrace")
            ->setLastModifiedBy("Koltiva Cocoatrace")
            ->setCategory("Koltiva Cocoatrace");
        // Add some data

        $style_center = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $style_border = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );

        $title = "GAPCOCTraining-Event Name  - " . $row->CertEventName;

        $object->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $object->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $object->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $object->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $object->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $object->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $object->getActiveSheet()->getColumnDimension('H')->setWidth(10);
        $object->getActiveSheet()->getColumnDimension('I')->setWidth(10);
        $object->getActiveSheet()->getColumnDimension('J')->setWidth(10);
        $object->getActiveSheet()->mergeCells('A1:J1');
        $object->getActiveSheet()->getStyle("A1:J4")->applyFromArray($style_center);
        $object->getActiveSheet()->getStyle("A4:J4")->applyFromArray($style_border);
        $object->getActiveSheet()->getStyle("A1:J4")->getFont()->setBold(true);
        $object->setActiveSheetIndex(0)->setCellValue('A1', $title); //Judul
        $object->setActiveSheetIndex(0)->setCellValue('A4', 'Applicant ID');
        $object->setActiveSheetIndex(0)->setCellValue('B4', 'Farmer ID');
        $object->setActiveSheetIndex(0)->setCellValue('C4', 'Farmer Name');
        $object->setActiveSheetIndex(0)->setCellValue('D4', 'Gender');
        $object->setActiveSheetIndex(0)->setCellValue('E4', 'District');
        $object->setActiveSheetIndex(0)->setCellValue('F4', 'SubDistrict');
        $object->setActiveSheetIndex(0)->setCellValue('G4', 'Village');
        $object->setActiveSheetIndex(0)->setCellValue('H4', 'Farmer Group');
        $object->setActiveSheetIndex(0)->setCellValue('I4', 'Percentage Attandance');
        $object->setActiveSheetIndex(0)->setCellValue('J4', 'Eligable Status');
        $i       = 0;
        $counter = 5; //MULAI ROWS SETELAH JUDUL HEADER
        if ($details) {
            foreach ($details as $key => $val) {
                $object->getActiveSheet()->getStyle("A$counter:J$counter")->applyFromArray($style_border);
                $object->getActiveSheet()->setCellValue('A' . $counter, $val['ApplicantID']);
                $object->getActiveSheet()->setCellValue('B' . $counter, $val['FarmerID']);
                $object->getActiveSheet()->setCellValue('C' . $counter, $val['ApplicantName']);
                $object->getActiveSheet()->setCellValue('D' . $counter, $val['Gender']);
                $object->getActiveSheet()->setCellValue('E' . $counter, $val['District']);
                $object->getActiveSheet()->setCellValue('F' . $counter, $val['SubDistrict']);
                $object->getActiveSheet()->setCellValue('G' . $counter, $val['Village']);
                $object->getActiveSheet()->setCellValue('H' . $counter, $val['FarmerGroup']);
                $object->getActiveSheet()->setCellValue('I' . $counter, $val['PercentageAttendance']);
                $object->getActiveSheet()->setCellValue('J' . $counter, $val['EligibleStatus']);
                $i++;
                $counter++;
            }
        }
        $konter = $counter;
        $konter++;

        $object->setActiveSheetIndex(0);
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $title . '.xlsx');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function import_cert_farmer_selection_post(){
        $IMSID   = (int) $this->post('IMSID');
        if ($this->file['Koltiva_view_IMS_WinImsAcqProImportCertFarmer-Form-FileImport']['name'] != "") {
            //Cek extensi
            $arrTemp = explode(".", $this->file['Koltiva_view_IMS_WinImsAcqProImportCertFarmer-Form-FileImport']['name']);
            $extNya  = array_values(array_slice($arrTemp, -1))[0];
            $extNya  = strtolower($extNya);

            // echo "<pre>";
            // print_r($cekProcessFarmer);
            // die;

            if ($extNya == 'xlsx') {
                //Read Filenya
//                $reader = ReaderFactory::create(Type::XLSX); // for XLSX files
                $reader = ReaderEntityFactory::createXLSXReader();
                $reader->setShouldFormatDates(true);
                $reader->open($this->file['Koltiva_view_IMS_WinImsAcqProImportCertFarmer-Form-FileImport']['tmp_name']);

                $DataExcel = array();
                $DataField = array();
                $increInt  = 0;
                foreach ($reader->getSheetIterator() as $sheet) {
                    foreach ($sheet->getRowIterator() as $row) {
                        if ($increInt == 0) {
                            $DataField = $row;
                        } else {
//                            $cells = $row->getCells();
                            $cells = $row->toArray();
                            $DataExcel[$increInt - 1] = $cells;
                        }
                        $increInt++;
                    }
                }
                $reader->close();

                /*
                [0] => FarmerID
                [1] => Participate
                [2] => Recommendation
                [3] => Selection
                 */

                $IsAllFarmerRegistered      = true;
                $IsAllFarmerRegisteredData  = array();
                $IsAlreadyApproved = true;
                $IsAlreadyApprovedData = array();

                for ($i = 0; $i < count($DataExcel); $i++) {
                    //Cek FarmerID
                    $cekProcessFarmer = $this->mims->cekExistFarmer($DataExcel[$i][0]);
                    if ($cekProcessFarmer == false) {
                        $IsAllFarmerRegistered       = false;
                        $IsAllFarmerRegisteredData[] = $DataExcel[$i][0];
                    }

                    //Cek Already Approved
                    $cekAlreadyApproved = $this->mims->cekFarmerIMSAlreadyApproved($IMSID,$DataExcel[$i][0]);
                    if ($cekAlreadyApproved == true) {
                        $IsAlreadyApproved       = false;
                        $IsAlreadyApprovedData[] = $DataExcel[$i][0];
                    }
                }

                if (
                    $IsAllFarmerRegistered == true &&
                    $IsAlreadyApproved == true
                ) {
                    //PROSES
                    $proses = $this->mims->ImportCertFarmerIMS($IMSID, $DataExcel);
                    $this->response($proses, 200);
                }else{
                    //Tidak lolos validasi
                    $psnErrorArr = array();

                    if ($IsAllFarmerRegistered == false) {
                        $FarmerNotRegistered = implode(", ", $IsAllFarmerRegisteredData);
                        $psnErrorArr[]       = "Farmer " . $FarmerNotRegistered . " are not register in the system";
                    }

                    if ($IsAlreadyApproved == false) {
                        $FarmerAlreadyApproved = implode(", ", $IsAlreadyApprovedData);
                        $psnErrorArr[]     = "Farmer " . $FarmerAlreadyApproved . " are already been approved in this IMSID";
                    }

                    $results['success'] = false;
                    $psnError           = implode('<br><br>', $psnErrorArr);
                    $results['message'] = $psnError;
                    $this->response($results, 400);
                }

            }else{
                $results['success'] = false;
                $results['message'] = lang('Invalid File!');
                $this->response($results, 400);
            }
        }
    }

    public function import_candidate_mapping_fa_post()
    {
        if ($this->file['Koltiva_view_IMS_WinCandidateMapFA-Form-FileImport']['name'] != "") {
            //Cek extensi
            $arrTemp = explode(".", $this->file['Koltiva_view_IMS_WinCandidateMapFA-Form-FileImport']['name']);
            $extNya  = array_values(array_slice($arrTemp, -1))[0];
            $extNya  = strtolower($extNya);
            $IMSID   = $this->post('IMSID');

            if ($extNya == 'xlsx') {
                //Read Filenya
//                $reader = ReaderFactory::create(Type::XLSX); // for XLSX files
                $reader = ReaderEntityFactory::createXLSXReader();
                $reader->setShouldFormatDates(true);
                $reader->open($this->file['Koltiva_view_IMS_WinCandidateMapFA-Form-FileImport']['tmp_name']);

                $dataExcel = array();
                $dataField = array();
                $increInt  = 0;
                foreach ($reader->getSheetIterator() as $sheet) {
                    foreach ($sheet->getRowIterator() as $row) {
                        if ($increInt == 0) {
                            $dataField = $row;
                        } else {
                            $cells = $row->toArray();
//                            $cells = $row->getCells();
                            $dataExcel[$increInt - 1] = $cells;
                        }
                        $increInt++;
                    }
                }
                $reader->close();

                /*
                [0] => FarmerID
                [1] => UserID
                [2] => StaffID
                 */
                $isAllFarmerRegistered      = true;
                $isAllFarmerRegisteredData  = array();
                $isAllUserIDRegistered      = true;
                $isAllUserIDRegisteredData  = array();
                $isAllStaffIDRegistered     = true;
                $isAllStaffIDRegisteredData = array();
                $isDuplicate                = true;
                $DataDuplicate              = array();
                $isFarmerMoreThanOneFa      = true;
                $isFarmerMoreThanOneFaData  = array();

                for ($i = 0; $i < count($dataExcel); $i++) {
                    //Cek FarmerID
                    $cekProcessFarmer = $this->mims->cekExistFarmerIMS($IMSID, $dataExcel[$i][0]);
                    if ($cekProcessFarmer == false) {
                        $isAllFarmerRegistered       = false;
                        $isAllFarmerRegisteredData[] = $dataExcel[$i][0];
                    }

                    //Cek UserID
                    $cekProcessUser = $this->mims->cekExistUser($dataExcel[$i][1]);
                    if ($dataExcel[$i][1] != '0'){
                        if ($cekProcessUser == false) {
                            $isAllUserIDRegistered       = false;
                            $isAllUserIDRegisteredData[] = $dataExcel[$i][1];
                        }
                    }

                    //Cek StaffID
                    $cekProcessStaff = $this->mims->cekExistStaff($dataExcel[$i][2]);
                    if ($dataExcel[$i][2] != '0'){
                        if ($cekProcessStaff == false) {
                            $isAllStaffIDRegistered       = false;
                            $isAllStaffIDRegisteredData[] = $dataExcel[$i][2];
                        }
                    }

                    $DataDuplicate[]             = $dataExcel[$i][0] . '_' . $dataExcel[$i][1];
                    $isFarmerMoreThanOneFaData[] = $dataExcel[$i][0];
                }

                //Cek Duplikat
                if (count(array_unique($DataDuplicate)) < count($DataDuplicate)) {
                    $isDuplicate = false;
                }

                //Cek isFarmerMoreThanOneFa
                if (count(array_unique($isFarmerMoreThanOneFaData)) < count($isFarmerMoreThanOneFaData)) {
                    $isFarmerMoreThanOneFa = false;
                }

                if (
                    $isAllFarmerRegistered == true &&
                    $isAllUserIDRegistered == true &&
                    $isAllStaffIDRegistered == true &&
                    $isDuplicate == true &&
                    $isFarmerMoreThanOneFa == true
                ) {

                    //PROSES
                    $proses = $this->mims->importCandidateMappingFA($IMSID, $dataExcel);
                    $this->response($proses, 200);

                } else {
                    //Tidak lolos validasi
                    $psnErrorArr = array();

                    if ($isAllFarmerRegistered == false) {
                        $FarmerNotRegistered = implode(", ", $isAllFarmerRegisteredData);
                        $psnErrorArr[]       = "Farmer " . $FarmerNotRegistered . " are not register to this IMS";
                    }

                    if ($isAllUserIDRegistered == false) {
                        $UserNotRegistered = implode(", ", $isAllUserIDRegisteredData);
                        $psnErrorArr[]     = "User " . $UserNotRegistered . " are not registered";
                    }

                    if ($isAllStaffIDRegistered == false) {
                        $StaffNotRegistered = implode(", ", $isAllStaffIDRegisteredData);
                        $psnErrorArr[]      = "Staff " . $StaffNotRegistered . " are not registered";
                    }

                    if ($isDuplicate == false) {
                        $psnErrorArr[] = "There are duplicate entry";
                    } else {
                        if ($isFarmerMoreThanOneFa == false) {
                            $psnErrorArr[] = "There are Farmer that register to more than one FA";
                        }
                    }

                    $results['success'] = false;
                    $psnError           = implode('<br><br>', $psnErrorArr);
                    $results['message'] = $psnError;
                    $this->response($results, 400);
                }

            } else {
                $results['success'] = false;
                $results['message'] = 'Invalid File!';
                $this->response($results, 400);
            }
        }
    }

    public function cmb_filter_fa_get()
    {
        $IMSID = (int) $this->get('IMSID');
        $data  = $this->mims->getComboFilterFA($IMSID);
        $this->response($data, 200);
    }

    public function cmb_filter_fa_mapping_get(){
        $IMSID = (int) $this->get('IMSID');
        $data  = $this->mims->getComboFilterFAMapping($IMSID);
        $this->response($data, 200);
    }

    public function grid_mapping_fa_get()
    {
        $IMSID  = (int) $this->get('IMSID');
        $UserId = $this->get('UserId');

        $sorting      = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $data = $this->mims->GridMappingFA($IMSID, $UserId, $this->get('start'), $this->get('limit'), $sortingField, $sortingDir, 'limit');
        $this->response($data, 200);
    }

    public function ims_import_cert_farmer_selection_get($IMSID){
        $IMSID = (int) $IMSID;
        ini_set('memory_limit', '-1');

        //data yg diperlukan (begin)
        $DataImport = $this->mims->DataImportCertFarmer($IMSID);
        //data yg diperlukan (end)

        require_once 'application/third_party/PHPExcel18/PHPExcel.php';
        require_once 'application/third_party/PHPExcel18/PHPExcel/IOFactory.php';

        //=============== MULAI TULIS EXCEL (BEGIN) ==============================================//
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("PT Koltiva")
            ->setLastModifiedBy("PT Koltiva")
            ->setTitle("Template for Import Existing Certified Farmer - IMS Acquisition Process")
            ->setSubject("Template for Import Existing Certified Farmer - IMS Acquisition Process")
            ->setDescription("Template for Import Existing Certified Farmer - IMS Acquisition Process")
            ->setKeywords("Template for Import Existing Certified Farmer - IMS Acquisition Process")
            ->setCategory("Template for Import Existing Certified Farmer - IMS Acquisition Process");

        //set style  (begin)
        $styleFont = array(
            'font'      => array(
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

        $styleFontBoldMainTitle = array(
            'font'      => array(
                'name' => 'Arial',
                'size' => '11',
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
        );

        $styleFontBoldTitle = array(
            'font'      => array(
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
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '8DB4E3'),
            ),
        );
        $styleFontBoldBgRedCenter = array(
            'font'      => array(
                'name' => 'Arial',
                'size' => '9',
                'bold' => true,
            ),
            'fill'      => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'C0504D'),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $styleBorderFull = array(
            'borders' => array(
                'left'   => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'right'  => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'top'    => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );
        //set style  (end)

        //create sheet
        $objWorkSheetPetani = $objPHPExcel->createSheet(0);
        $objWorkSheetPetani->setTitle('Data');

        //set width column
        $objWorkSheetPetani->getColumnDimension('A')->setWidth(14);
        $objWorkSheetPetani->getColumnDimension('B')->setWidth(14);
        $objWorkSheetPetani->getColumnDimension('C')->setWidth(14);
        $objWorkSheetPetani->getColumnDimension('D')->setWidth(14);

        //tabel header
        $objWorkSheetPetani->setCellValue('A1', 'FarmerID');
        $objWorkSheetPetani->setCellValue('B1', lang('Participate in Socialization'));
        $objWorkSheetPetani->setCellValue('C1', lang('Recommendation'));
        $objWorkSheetPetani->setCellValue('D1', lang('Selection Status'));
        $objWorkSheetPetani->getStyle('A1:D1')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetPetani->getStyle('A1:D1')->applyFromArray($styleBorderFull, false);

        $rowStart = 2;
        $incre    = 0;
        foreach ($DataImport as $val) {
            $val['no'] = $incre + 1;

            $objWorkSheetPetani->setCellValue('A' . $rowStart, $val['FarmerID']);
            $objWorkSheetPetani->setCellValue('B' . $rowStart, $val['ParticipateInSocializationStatus']);
            $objWorkSheetPetani->setCellValue('C' . $rowStart, $val['RecommendationStatus']);
            $objWorkSheetPetani->setCellValue('D' . $rowStart, $val['SelectionStatus']);

            $objWorkSheetPetani->getStyle('A' . $rowStart . ':' . 'D' . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetPetani->getStyle('A' . $rowStart . ':' . 'D' . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . date('YmdHis') . '_import_cert_farmer_' . $IMSID . '.xlsx');
        header('Cache-Control: max-age=0');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
        //=============== MULAI TULIS EXCEL (END) ==============================================//
    }

    public function ims_event_detail_mapping_fa_grid_get($IMSID,$FilterFA){
        ini_set('memory_limit', '-1');

        //data yg diperlukan (begin)
        $DataList = $this->mims->GridMappingFA($IMSID, $FilterFA, null, null, null, null, 'no_limit');
        //echo '<pre>'; print_r($DataList); exit;
        //data yg diperlukan (end)

        require_once 'application/third_party/PHPExcel18/PHPExcel.php';
        require_once 'application/third_party/PHPExcel18/PHPExcel/IOFactory.php';

        //=============== MULAI TULIS EXCEL (BEGIN) ===================================================================//

        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("PT Koltiva")
            ->setLastModifiedBy("PT Koltiva")
            ->setTitle("Mapping for FA - IMS")
            ->setSubject("Mapping for FA - IMS")
            ->setDescription("Mapping for FA - IMS")
            ->setKeywords("Mapping for FA - IMS")
            ->setCategory("Mapping for FA - IMS");

        //set style  (begin)
        $styleFont = array(
            'font'      => array(
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

        $styleFontBoldMainTitle = array(
            'font'      => array(
                'name' => 'Arial',
                'size' => '11',
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
        );

        $styleFontBoldTitle = array(
            'font'      => array(
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
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '8DB4E3'),
            ),
        );
        $styleFontBoldBgRedCenter = array(
            'font'      => array(
                'name' => 'Arial',
                'size' => '9',
                'bold' => true,
            ),
            'fill'      => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'C0504D'),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $styleBorderFull = array(
            'borders' => array(
                'left'   => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'right'  => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'top'    => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );
        //set style  (end)

        //create sheet
        $objWorkSheetPetani = $objPHPExcel->createSheet(0);
        $objWorkSheetPetani->setTitle('Data');

        //set width column
        //$objWorkSheetPetani->getColumnDimension('A')->setWidth(14);
        //$objWorkSheetPetani->getColumnDimension('B')->setWidth(14);
        //$objWorkSheetPetani->getColumnDimension('C')->setWidth(14);

        //tabel header
        $objWorkSheetPetani->setCellValue('A1', 'Field Agent');
        $objWorkSheetPetani->setCellValue('B1', lang('Farmer'));
        $objWorkSheetPetani->setCellValue('C1', lang('Farmer Group'));
        $objWorkSheetPetani->setCellValue('D1', lang('Province'));
        $objWorkSheetPetani->setCellValue('E1', lang('District'));
        $objWorkSheetPetani->setCellValue('F1', lang('Sub District'));
        $objWorkSheetPetani->setCellValue('G1', lang('Village'));
        $objWorkSheetPetani->getStyle('A1:G1')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetPetani->getStyle('A1:G1')->applyFromArray($styleBorderFull, false);

        $rowStart = 2;
        $incre    = 0;
        foreach ($DataList as $val) {
            $val['no'] = $incre + 1;

            $objWorkSheetPetani->setCellValue('A' . $rowStart, $val['FieldAgent']);
            $objWorkSheetPetani->setCellValue('B' . $rowStart, $val['Farmer']);
            $objWorkSheetPetani->setCellValue('C' . $rowStart, $val['FarmerGroup']);
            $objWorkSheetPetani->setCellValue('D' . $rowStart, $val['Province']);
            $objWorkSheetPetani->setCellValue('E' . $rowStart, $val['District']);
            $objWorkSheetPetani->setCellValue('F' . $rowStart, $val['SubDistrict']);
            $objWorkSheetPetani->setCellValue('G' . $rowStart, $val['Village']);

            $objWorkSheetPetani->getStyle('A' . $rowStart . ':' . 'G' . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetPetani->getStyle('A' . $rowStart . ':' . 'G' . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . date('YmdHis') . '_mapping_fa_ims_' . $IMSID . '.xlsx');
        header('Cache-Control: max-age=0');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function ims_candidate_mapping_get($IMSID)
    {
        $IMSID = (int) $IMSID;
        ini_set('memory_limit', '-1');

        //data yg diperlukan (begin)
        $dataMapping = $this->mims->GridMappingFA($IMSID, null, null, null, null, null, 'no_limit');
        //data yg diperlukan (end)

        require_once 'application/third_party/PHPExcel18/PHPExcel.php';
        require_once 'application/third_party/PHPExcel18/PHPExcel/IOFactory.php';

        //=============== MULAI TULIS EXCEL (BEGIN) ===================================================================//

        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("PT Koltiva")
            ->setLastModifiedBy("PT Koltiva")
            ->setTitle("Templte for Candidate Mapping for FA - IMS")
            ->setSubject("Templte for Candidate Mapping for FA - IMS")
            ->setDescription("Templte for Candidate Mapping for FA - IMS")
            ->setKeywords("Templte for Candidate Mapping for FA - IMS")
            ->setCategory("Templte for Candidate Mapping for FA - IMS");

        //set style  (begin)
        $styleFont = array(
            'font'      => array(
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

        $styleFontBoldMainTitle = array(
            'font'      => array(
                'name' => 'Arial',
                'size' => '11',
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
        );

        $styleFontBoldTitle = array(
            'font'      => array(
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
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '8DB4E3'),
            ),
        );
        $styleFontBoldBgRedCenter = array(
            'font'      => array(
                'name' => 'Arial',
                'size' => '9',
                'bold' => true,
            ),
            'fill'      => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'C0504D'),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $styleBorderFull = array(
            'borders' => array(
                'left'   => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'right'  => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'top'    => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );
        //set style  (end)

        //create sheet
        $objWorkSheetPetani = $objPHPExcel->createSheet(0);
        $objWorkSheetPetani->setTitle('Data');

        //set width column
        $objWorkSheetPetani->getColumnDimension('A')->setWidth(14);
        $objWorkSheetPetani->getColumnDimension('B')->setWidth(14);
        $objWorkSheetPetani->getColumnDimension('C')->setWidth(14);
        $objWorkSheetPetani->getColumnDimension('D')->setWidth(14);
        $objWorkSheetPetani->getColumnDimension('E')->setWidth(14);

        //tabel header
        $objWorkSheetPetani->setCellValue('A1', 'FarmerID');
        $objWorkSheetPetani->setCellValue('B1', lang('UserID'));
        $objWorkSheetPetani->setCellValue('C1', lang('StaffID'));
        $objWorkSheetPetani->setCellValue('D1', lang('UserName'));
        $objWorkSheetPetani->setCellValue('E1', lang('FAName'));
        $objWorkSheetPetani->getStyle('A1:E1')->applyFromArray($styleFontBoldHeader);
        $objWorkSheetPetani->getStyle('A1:E1')->applyFromArray($styleBorderFull, false);

        $rowStart = 2;
        $incre    = 0;
        foreach ($dataMapping as $val) {
            $val['no'] = $incre + 1;

            $objWorkSheetPetani->setCellValue('A' . $rowStart, $val['FarmerID']);
            $objWorkSheetPetani->setCellValue('B' . $rowStart, $val['PICUserID']);
            $objWorkSheetPetani->setCellValue('C' . $rowStart, $val['PICStaffID']);
            $objWorkSheetPetani->setCellValue('D' . $rowStart, $val['UserName']);
            $objWorkSheetPetani->setCellValue('E' . $rowStart, $val['FieldAgent']);

            $objWorkSheetPetani->getStyle('A' . $rowStart . ':' . 'E' . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetPetani->getStyle('A' . $rowStart . ':' . 'E' . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . date('YmdHis') . '_candidate_mapping_fa_ims_' . $IMSID . '.xlsx');
        header('Cache-Control: max-age=0');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function combo_province_get()
    {
        $data = $this->mims->GetComboProvince();
        $this->response($data, 200);
    }

    public function combo_district_get()
    {
        $ProvinceID = (int) $this->get('ProvinceID');
        $data       = $this->mims->GetComboDistrict($ProvinceID);
        $this->response($data, 200);
    }

    public function grid_ims_documents_master_get()
    {
        $IMSMasterID = (int) $this->get('IMSMasterID');

        $data = $this->mims->GetGridImsDocumentsMaster($IMSMasterID);
        $this->response($data, 200);
    }

    public function ims_documents_master_form_data_get()
    {
        $IMSMasterID = (int) $this->get('IMSMasterID');
        $DocMasID = (int) $this->get('DocMasID');

        $data = $this->mims->GetImsDocumentsMasterFormData($IMSMasterID,$DocMasID);
        $this->response($data, 200);
    }

    public function ims_documents_master_post(){
        $varPost   = $this->post();
        $paramPost = array();

        //prep variabel (begin)
        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_IMS_WinFormImsMasterDocument-Form-", '', $key);
            if ($value == "") {
                $value = null;
            }

            $paramPost[$keyNew] = $value;
        }
        //prep variabel (end)
        //echo '<pre>'; print_r($paramPost); exit;

        $proses = $this->mims->UpdateImsDocumentsMaster($paramPost);
        $this->response($proses, 200);
    }

    public function ims_documents_master_information_title_post()
    {
        $IMSMasterID = (int) $this->post('IMSMasterID');

        $data = $this->mims->GetDocumentMasterInfoTitle($IMSMasterID);
        $this->response($data, 200);
    }

    public function ims_documents_master_unlock_document_post(){
        $IMSMasterID = (int) $this->post('IMSMasterID');
        $DocMasID = (int) $this->post('DocMasID');

        $proses = $this->mims->UnlockImsDocument($IMSMasterID,$DocMasID);
        $this->response($proses, 200);
    }

    public function ims_documents_event_information_title_post(){
        $IMSID = (int) $this->post('IMSID');

        $data = $this->mims->GetDocumentEventInfoTitle($IMSID);
        $this->response($data, 200);
    }

    public function grid_ims_documents_event_get()
    {
        $IMSID = (int) $this->get('IMSID');

        $data = $this->mims->GetGridImsDocumentsEvent($IMSID);
        $this->response($data, 200);
    }

    public function ims_documents_event_form_data_get()
    {
        $IMSID = (int) $this->get('IMSID');
        $DocEveID = (int) $this->get('DocEveID');

        $data = $this->mims->GetImsDocumentsEventFormData($IMSID,$DocEveID);
        $this->response($data, 200);
    }

    public function ims_documents_event_post()
    {
        $varPost   = $this->post();
        $paramPost = array();

        //prep variabel (begin)
        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_IMS_WinFormImsEventDocument-Form-", '', $key);
            if ($value == "") {
                $value = null;
            }

            $paramPost[$keyNew] = $value;
        }
        //prep variabel (end)
        //echo '<pre>'; print_r($paramPost); exit;

        $proses = $this->mims->UpdateImsDocumentsEvent($paramPost);
        $this->response($proses, 200);
    }

    public function pre_afl_input_certification_contract_get(){
        $IMSID = (int) $this->get('IMSID');
        $FarmerID = (int) $this->get('FarmerID');

        $data = $this->mims->GetPreAflCertificationContractFormData($FarmerID,$IMSID);
        $this->response($data, 200);
    }

    public function certification_contract_post(){
        $this->load->library('awsfileupload');
        if ($this->file['Koltiva_view_IMS_WinFormInputCertContract-Form-CertContractFile']['name'] != '') {

            $upload = $this->awsfileupload->upload($this->file['Koltiva_view_IMS_WinFormInputCertContract-Form-CertContractFile']['tmp_name'],$this->file['Koltiva_view_IMS_WinFormInputCertContract-Form-CertContractFile']['name'], AWSS3_CERT_CONTRACT_PATH, 'documents');

            if ($upload['success'] == true) {
                if($this->awsfileupload->doesObjectExist($this->post('Koltiva_view_IMS_WinFormInputCertContract-Form-CertContractFileUrlOld')) == true) {
                    $this->awsfileupload->delete($this->post('Koltiva_view_IMS_WinFormInputCertContract-Form-CertContractFileUrlOld'));
                }else{
                    delete_file($this->post('Koltiva_view_IMS_WinFormInputCertContract-Form-CertContractFileUrlOld'));
                }

                //Update ke tabel farmer
                $proses = $this->mims->UpdateCertificationContract($this->post('IMSID'), $this->post('FarmerID'),$upload['filenamepath']);
                $result['success'] = true;
                $result['message'] = lang('File uploaded');
                $result['file'] = $upload['fileurl'];
                $result['filepath']   = $upload['filenamepath'];
                $this->response($result, 200);
            } else {
                $result['success'] = false;
                $result['message'] = lang('Upload to aws failed');
                $this->response($result, 400);
            }
        }
    }

    public function certification_contract_form_post(){
        $varPost   = $this->post();
        $paramPost = array();

        //prep variabel (begin)
        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_IMS_WinFormInputCertContract-Form-", '', $key);
            if ($value == "") {
                $value = null;
            }

            $paramPost[$keyNew] = $value;
        }
        //prep variabel (end)
        //echo '<pre>'; print_r($paramPost); exit;

        $proses = $this->mims->UpdateImsCertificationContract($paramPost);
        $this->response($proses, 200);
    }

    public function ims_farmer_target_to_dhis_post(){
        $IMSID = (int) $this->post('IMSID');
        $UserID = (int) $this->post('UserID');

        $proses = $this->mims->ExportImsFarmerTargetToDHIS($IMSID,$UserID);
        $this->response($proses, 200);
    }

    public function ims_farmer_target_offline_data_post(){
        ini_set('memory_limit', '-1');
        $IMSID = (int) $this->post('IMSID');
        $UserID = (int) $this->post('UserID');
        $StaffName = $this->post('StaffName');
        $DataIMS = $this->mims->getIMSInfo($IMSID);

        //Strip Staff Name
        $StaffName = str_replace(' ','-',$StaffName);
        $StaffName = preg_replace('/[^A-Za-z0-9\-]/', '', $StaffName);

        if($DataIMS['ParamOfflineProgramID'] != "" && $DataIMS['ParamOfflineOrgUnitID'] != ""){
            //Hard Coded Parameter
            $DhisID = 'adKe6Ri9ClI'; //offline_data_per_ims

            //Nama File
            $NamaFile = "Offline-Data-IMSID-$IMSID-$StaffName-".date('YmdHis');

            $url = $this->config->item('dhis_url').'api/sqlViews/'.$DhisID.'/data.csv?var=imsid:'.$IMSID.'&var=userid:'.$UserID.'&filter=A.organisationunitid:in:['.$DataIMS['ParamOfflineOrgUnitID'].']&filter=P.programid:in:['.$DataIMS['ParamOfflineProgramID'].']';
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Basic YWRtaW46S29sdGl2YTIwMTMh'
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);

            $NamaFileZip = $NamaFile.'.zip';
            $FilePathZip = 'files/export/'.$NamaFileZip;

            $zip = new ZipArchive();
            $zip->open($FilePathZip, ZipArchive::CREATE | ZipArchive::OVERWRITE);

            $NamaFileCsv = $NamaFile.'.csv';
            $filenamepath = 'files/export/'.$NamaFileCsv;
            $filenamepath = filter_var($filenamepath,FILTER_SANITIZE_STRING);
            file_put_contents($filenamepath,$result);

            $zip->addFile($filenamepath,$NamaFileCsv);
            $zip->close();

            $proses['success'] = true;
            $proses['message'] = lang('Offline Data Generated');
            $proses['FilePathZip'] = $FilePathZip;

            //============================================================================
        }else{
            $proses['success'] = false;
            $proses['message'] = lang('Parameter for Offline Data (ProgramID & OrgUnitID) is empty');
        }

        $this->response($proses, 200);
    }

    public function ics_farmer_verified_form_data_get(){
        $IMSID = (int) $this->get('IMSID');
        $FarmerID = (int) $this->get('FarmerID');
        $data = $this->mims->GetIcsFarmerVerifiedFormData($IMSID,$FarmerID);
        $this->response($data, 200);
    }

    public function ics_farmer_verified_form_post(){
        $varPost   = $this->post();
        $ParamPost = array();

        //prep variabel (begin)
        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_IMS_WinFormICSVerifyFarmer-Form-", '', $key);
            if ($value == "") {
                $value = null;
            }

            $ParamPost[$keyNew] = $value;
        }
        //prep variabel (end)
        //echo '<pre>'; print_r($ParamPost); exit;


        $proses = $this->mims->UpdateAflStatusFarmerVerified($ParamPost);
        if($proses['success'] == true){
            $this->response($proses, 200);
        }else{
            $this->response($proses, 400);
        }
    }

    public function ims_cfl_takeout_farmer_list_get(){
        $IMSID = (int) $this->get('IMSID');
        $SearchStringParam = $this->get('SearchStringParam');
        $SearchCpgParam = $this->get('SearchCpgParam');

        $sorting = json_decode($this->get('sort'));
        if(isset($sorting[0]->property)) $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : ''; else $sortingField = null;
        if(isset($sorting[0]->direction)) $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : ''; else $sortingDir = null;

        $data = $this->mims->GetCflTakeoutFarmerList($IMSID,$SearchStringParam,$SearchCpgParam,$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        $this->response($data, 200);
    }

    public function ims_cfl_takeout_farmer_post(){
        $IMSID = (int) $this->post('IMSID');
        $FarmerIDSel = json_decode($this->post('FarmerIDSel'));

        $proses = $this->mims->CflTakeoutFarmerList($IMSID,$FarmerIDSel);
        if($proses['success'] == true){
            $this->response($proses, 200);
        }else{
            $this->response($proses, 400);
        }
    }

    public function ims_finalization_period_form_data_get(){
        $IMSID = (int) $this->get('IMSID');
        $data = $this->mims->ImsFinalizationPeriodFormData($IMSID);
        $this->response($data, 200);
    }

    public  function ims_finalization_period_post(){
        $varPost   = $this->post();
        $ParamPost = array();

        //prep variabel (begin)
        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_IMS_WinImsFormFinalizationPeriod-Form-", '', $key);
            if ($value == "") {
                $value = null;
            }

            $ParamPost[$keyNew] = $value;
        }
        //prep variabel (end)
        //echo '<pre>'; print_r($ParamPost); exit;

        $proses = $this->mims->UpdateImsFinalizationPeriod($ParamPost);
        if($proses['success'] == true){
            $this->response($proses, 200);
        }else{
            $this->response($proses, 400);
        }
    }

    public function ims_ics_reinspection_form_data_get(){
        $IMSID = (int) $this->get('IMSID');
        $data = $this->mims->ImsIcsReinspectionFormData($IMSID);
        $this->response($data, 200);
    }

    public function ims_ics_reinspection_form_post(){
        $varPost   = $this->post();
        $ParamPost = array();

        //prep variabel (begin)
        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_IMS_WinImsFormIcsReinspectionStatus-Form-", '', $key);
            if ($value == "") {
                $value = null;
            }

            $ParamPost[$keyNew] = $value;
        }
        //prep variabel (end)
        //echo '<pre>'; print_r($ParamPost); exit;

        $proses = $this->mims->UpdateIcsReinspectionForm($ParamPost);
        if($proses['success'] == true){
            $this->response($proses, 200);
        }else{
            $this->response($proses, 400);
        }
    }

    public function ims_ics_reinspection_add_farmer_list_get(){
        $IMSID = (int) $this->get('IMSID');
        $SearchStringParam = $this->get('SearchStringParam');
        $SearchCpgParam = $this->get('SearchCpgParam');

        $sorting = json_decode($this->get('sort'));
        if(isset($sorting[0]->property)) $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : ''; else $sortingField = null;
        if(isset($sorting[0]->direction)) $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : ''; else $sortingDir = null;

        $data = $this->mims->GetIcsReinspectionAddFarmerListGrid($IMSID,$SearchStringParam,$SearchCpgParam,$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        $this->response($data, 200);
    }

    public function ims_ics_reinspection_add_farmer_post(){
        $IMSID = (int) $this->post('IMSID');
        $FarmerGardenSel = json_decode($this->post('FarmerGardenSel'));

        $proses = $this->mims->InsertIcsReinspectionFarmer($IMSID,$FarmerGardenSel);
        if($proses['success'] == true){
            $this->response($proses, 200);
        }else{
            $this->response($proses, 400);
        }
    }

    public function ics_reinspect_farmer_delete(){
        $IMSID = (int) $this->delete('IMSID');
        $FarmerID = (int) $this->delete('FarmerID');
        $GardenNr = (int) $this->delete('GardenNr');

        $proses = $this->mims->CancelIcsReinspectionFarmer($IMSID,$FarmerID,$GardenNr);
        if($proses['success'] == true){
            $this->response($proses, 200);
        }else{
            $this->response($proses, 400);
        }
    }

    public function ims_expot_audit_summary_garden_post(){
        $IMSID = (int) $this->post('IMSID');
        $this->load->helper('date');
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        $IMSID = (int) $IMSID;

        //Set Fix bahasa indonesia
        $this->load->language('general', 'indonesia');

        //Get Data ======================= (Begin)
        $DataListGarden = $this->mims->GetDataGardenByAflGarden($IMSID,'All');
        $DataRefControlGarden = $this->mims->GetDataControlRefGarden();
        //echo '<pre>'; print_r($DataRefControlGarden); exit;
        //echo '<pre>'; print_r($DataListGarden[0]); exit;
        $filename = $IMSID."_AuditSumGarden_".date('Ymd');
        //Get Data ======================= (End)

        $DataView['filename'] = $filename;
        $DataView['DataListGarden'] = $DataListGarden;
        $DataView['DataRefControlGarden'] = $DataRefControlGarden;
        $tmp = $this->load->view('report_data/ims_export_audit_summary_garden', $DataView, TRUE);

        $filenamepath = 'files/export/'.$filename.".xml";
        $filenamepath = filter_var($filenamepath,FILTER_SANITIZE_STRING);
        file_put_contents($filenamepath,$tmp);
        //ngezip (begin)
            // Initialize archive object
            $zip = new ZipArchive();
            $zip->open('files/export/'.$filename.'.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

            // Add current file to archive
            $zip->addFile('files/export/'.$filename.".xml",$filename.".xml");
            $zip->close();
        //ngezip (end)

        $proses['success'] = true;
        $proses['filedl'] = '/api/files/export/'.$filename.'.zip';
        $this->response($proses, 200);
    }

    public function ims_expot_audit_summary_garden_year1_post(){
        $IMSID = (int) $this->post('IMSID');
        $this->load->helper('date');
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        $IMSID = (int) $IMSID;

        //Set Fix bahasa indonesia
        $this->load->language('general', 'indonesia');

        //Get Data ======================= (Begin)
        $DataListGarden = $this->mims->GetDataGardenByAflGarden($IMSID,'Year1');
        $DataRefControlGarden = $this->mims->GetDataControlRefGarden();
        //echo '<pre>'; print_r($DataRefControlGarden); exit;
        //echo '<pre>'; print_r($DataListGarden[0]); exit;
        $filename = $IMSID."_AuditSumGardenYear1_".date('Ymd');
        //Get Data ======================= (End)

        $DataView['filename'] = $filename;
        $DataView['DataListGarden'] = $DataListGarden;
        $DataView['DataRefControlGarden'] = $DataRefControlGarden;
        $tmp = $this->load->view('report_data/ims_export_audit_summary_garden_year1', $DataView, TRUE);

        $filenamepath = 'files/export/'.$filename.".xml";
        $filenamepath = filter_var($filenamepath,FILTER_SANITIZE_STRING);
        file_put_contents($filenamepath,$tmp);
        //ngezip (begin)
            // Initialize archive object
            $zip = new ZipArchive();
            $zip->open('files/export/'.$filename.'.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

            // Add current file to archive
            $zip->addFile('files/export/'.$filename.".xml",$filename.".xml");
            $zip->close();
        //ngezip (end)

        $proses['success'] = true;
        $proses['filedl'] = '/api/files/export/'.$filename.'.zip';
        $this->response($proses, 200);

        // $this->zip->add_data($filename. ".xls", $tmp);
        // $this->zip->archive('files/sql_view_temp/'.$filename.'.zip');
        // $this->zip->download($filename.'.zip');
    }

    public function ims_expot_audit_summary_garden_year2_post(){
        $IMSID = (int) $this->post('IMSID');
        $this->load->helper('date');
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        $IMSID = (int) $IMSID;

        //Set Fix bahasa indonesia
        $this->load->language('general', 'indonesia');

        //Get Data ======================= (Begin)
        $DataListGarden = $this->mims->GetDataGardenByAflGarden($IMSID,'Year2');
        $DataRefControlGarden = $this->mims->GetDataControlRefGarden();
        //echo '<pre>'; print_r($DataRefControlGarden); exit;
        //echo '<pre>'; print_r($DataListGarden[0]); exit;
        $filename = $IMSID."_AuditSumGardenYear2_".date('Ymd');
        //Get Data ======================= (End)

        $DataView['filename'] = $filename;
        $DataView['DataListGarden'] = $DataListGarden;
        $DataView['DataRefControlGarden'] = $DataRefControlGarden;
        $tmp = $this->load->view('report_data/ims_export_audit_summary_garden_year2', $DataView, TRUE);

        $filenamepath = 'files/export/'.$filename.".xml";
        $filenamepath = filter_var($filenamepath,FILTER_SANITIZE_STRING);
        file_put_contents($filenamepath,$tmp);
        //ngezip (begin)
            // Initialize archive object
            $zip = new ZipArchive();
            $zip->open('files/export/'.$filename.'.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

            // Add current file to archive
            $zip->addFile('files/export/'.$filename.".xml",$filename.".xml");
            $zip->close();
        //ngezip (end)

        $proses['success'] = true;
        $proses['filedl'] = '/api/files/export/'.$filename.'.zip';
        $this->response($proses, 200);

        // $this->zip->add_data($filename. ".xls", $tmp);
        // $this->zip->archive('files/sql_view_temp/'.$filename.'.zip');
        // $this->zip->download($filename.'.zip');
    }

    public function ims_regenerate_audit_summary_post(){
        $IMSID = (int) $this->post('IMSID');
        $FarmerID = (int) $this->post('FarmerID');
        $ProsesAll = true;
        $ProsesReturn = array();
        $MsgFailed = lang('Generate Audit Summary Fail');

        $DataAuditLog = $this->mims->GetAflGardenDataByFarmer($IMSID,$FarmerID);
        
        if(isset($DataAuditLog[0]['FarmerID'])){
            for ($i=0; $i < count($DataAuditLog); $i++) {
                $this->mims->GenDataControlSurveyGarden('ims',date('Y-m-d H:i:s'),$_SESSION['userid'],$DataAuditLog[$i]['FarmerID'],$DataAuditLog[$i]['GardenNr'],$DataAuditLog[$i]['SurveyNr'],$DataAuditLog[$i]['Certification'],$DataAuditLog[$i]['ICSDate']);
            }
        }else{
            $ProsesAll = false;
            $MsgFailed = lang('Audit Log not found');
        }

        if($ProsesAll == true){
            $ProsesReturn['success'] = true;
            $ProsesReturn['message'] = lang('Audit Summary Generated');
            $this->response($ProsesReturn, 200);
        }else{
            $ProsesReturn['success'] = false;
            $ProsesReturn['message'] = $MsgFailed;
            $this->response($ProsesReturn, 400);
        }
    }

    public function ims_regenerate_audit_summary_by_event_get() {
        $this->load->model('farmer/mfarmers');
        $IMSID = (int) $this->get('IMSID');

        //Get list farmer from AFL
        $ListFarmerID = $this->mims->GetICSFarmerByIMS($IMSID);
        if(isset($ListFarmerID[0]['FarmerID'])) {
            for ($i=0; $i < count($ListFarmerID); $i++) {
                $DataAuditLog = $this->mims->GetAflGardenDataByFarmer($IMSID,$ListFarmerID[$i]['FarmerID']);
                if(isset($DataAuditLog[0]['FarmerID'])){
                    for ($j=0; $j < count($DataAuditLog); $j++) {
                        $this->mfarmers->GenDataControlSurveyGarden('ims',date('Y-m-d H:i:s'),$_SESSION['userid'],$DataAuditLog[$j]['FarmerID'],$DataAuditLog[$j]['GardenNr'],$DataAuditLog[$j]['SurveyNr'],$DataAuditLog[$j]['Certification'],$DataAuditLog[$j]['ICSDate']);
                    }
                }
            }

            $ProsesReturn['success'] = true;
            $ProsesReturn['message'] = 'Process Finished';
            $this->response($ProsesReturn, 200);
        } else {
            $ProsesReturn['success'] = false;
            $ProsesReturn['message'] = 'No Farmer for this IMSID';
            $this->response($ProsesReturn, 400);
        }
    }

    public function external_audit_input_main_grid_get() {
        $sorting      = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $IMSID = (int) $this->get('IMSID');
        $TextSearch = filter_var($this->get('TextSearch'),FILTER_SANITIZE_STRING);

        $data = $this->mims->readAFLFinalExternalAuditInput($IMSID, $TextSearch, $this->get('start'), $this->get('limit'), $sortingField, $sortingDir);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array(), 200);
        }
    }

    public function external_audit_input_post() {
        $FarmerIDSel = $this->post('FarmerIDSel');
        $IMSID = (int) $this->post('IMSID');

        $proses = $this->mims->ExternalAuditInput($FarmerIDSel,$IMSID);
        if($proses['success'] == true)
            $this->response($proses, 200);
        else
            $this->response($proses, 400);
    }

    public function external_audit_reset_post() {
        $FarmerID = (int) $this->post('FarmerID');
        $IMSID = (int) $this->post('IMSID');
        $proses = $this->mims->ExternalAuditReset($FarmerID,$IMSID);
        if($proses['success'] == true)
            $this->response($proses, 200);
        else
            $this->response($proses, 400);
    }

    public function grid_form_farmer_candidate_get() {
        $sorting = json_decode($this->get('sort'));
        if (isset($sorting[0]->property))
            $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        else
            $sortingField = null;
        if (isset($sorting[0]->direction))
            $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';
        else
            $sortingDir = null;
        $start = (int) $this->get('start');
        $limit = (int) $this->get('limit');
        //get param
        $pSearch = array(
            'textSearch' => $this->get('TxtSearchLabel'),
            'IMSID' => (int) $this->get('IMSID'),
            'CmbFilterProvince' => (int) $this->get('CmbFilterProvince'),
            'CmbFilterDistrict' => (int) $this->get('CmbFilterDistrict'),
            'CmbFilterSubDistrict' => (int) $this->get('CmbFilterSubDistrict')
        );
//        $data['param'] = $pSearch;
//        $this->response($data, 200);

        $data = $this->mims->GetGridFormFarmerCandidate($pSearch, $start, $limit, $sortingField, $sortingDir);
        $this->response($data, 200);
    }

    public function ims_detail_candidate_post() {
        $FarmerID = json_decode($this->post('Candidate'));
        $IMSID = (int) $this->post('IMSID');
        $proses = $this->mims->InsertCandidateIms($FarmerID, $IMSID);
        if($proses['success'] == true){
            $this->response($proses, 200);
        }else{
            $this->response($proses, 400);
        }

    }
    
    public function grid_coaching_activity_sql_export_post() {
        ini_set('memory_limit', '-1');
        
        //sort
        $sorting      = json_decode($this->post('sort'));
        if (isset($sorting[0]->property)) $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        else $sortingField = null;
        if (isset($sorting[0]->direction)) $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';
        else $sortingDir = null;
        
        $IMSID = (int) $this->post('IMSID');
        //get param
        $pSearch = array(
            'textSearch' => $this->post('textSearch'),
        );

        $sorting      = json_decode($this->post('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $dataList           = $this->mims->getExportCoachingActivitySql($IMSID, $pSearch, $sortingField, $sortingDir);
        $dataListSession    = $this->mims->getExportCoachingActivitySessionSql($IMSID, $pSearch, $sortingField, $sortingDir);
        
        //generate nama file excel
        $SqlvName = "Export Sql Coaching Activity IMSID=$IMSID ";
        $sqlViewName = str_replace(' ','-',$SqlvName);

        //Strip character spesial
        $sqlViewName = preg_replace('/[^A-Za-z0-9\-]/', '', $sqlViewName);

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
            $namaFile = $sqlViewName.".xlsx";
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
            
            $CoachingActivity = $writer->getCurrentSheet()->setName(lang('Coaching Activity'));

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

            if(count($dataListSession) > 0){
                //Kolom Header Coaching Session
                $dataHeaderSession = array('No');
                foreach($dataListSession[0] as $key2 => $value2){
                    array_push($dataHeaderSession,lang($key2));
                }
                //Kolom Header Coaching Session
                
                //Kolom Body Coaching Session
                $dataListExcelKebun = array();
                $no = 1;
                foreach ($dataListSession as $key => $value) {
                    $data = array();
                    array_push($data,$no);
                    foreach($value as $keyx => $valuex){
                        array_push($data,$valuex);
                    }
                    $dataListExcelKebun[$key] = $data;
                    $no++;
                }
                // echo '<pre>'; print_r($dataListExcel); echo '</pre>'; exit;
                // return;
                //Kolom Body Coaching Session                

                $rowHeaderKebun = WriterEntityFactory::createRowFromArray($dataHeaderSession, $styleHeader);
                $writer->addNewSheetAndMakeItCurrent()->setName('Coaching Session');
                $writer->addRow($rowHeaderKebun);

                for ($i=0; $i < count($dataListExcelKebun); $i++) {
                    $dataRowsKebun = $dataListExcelKebun[$i];
                    $cells2 = array();
        
                    for ($j=0; $j < count($dataRowsKebun); $j++) {
                        $styleRow = null;
                        $dataRowKebun = null;
        
                        //cek apakah numeric
                        if(is_numeric($dataRowsKebun[$j])){
                            $styleRow = $styleFormatAngka;
                            $dataRowKebun = (float) $dataRowsKebun[$j];
                        } else {
                            //cek apakah tanggal
                            if($this->validateDate($dataRowsKebun[$j]) == true) {
                                $styleRow = $styleFormatTanggal;
                                $dataRowKebun = 25569 + (strtotime($dataRowsKebun[$j]) / 86400);
                            } else {
                                $styleRow = $styleData;
                                $dataRowKebun = $dataRowsKebun[$j];
                            }
                        }
        
                        $cells2[$j] = WriterEntityFactory::createCell($dataRowKebun, $styleRow);
                    }
                    /*$cells = [
                        WriterEntityFactory::createCell($dataRows[0], $styleData),
                        WriterEntityFactory::createCell((float) $dataRows[1], $styleFormatAngka),
                        WriterEntityFactory::createCell($dataRows[2], $styleData),
                        WriterEntityFactory::createCell(25569 + (time() / 86400), $styleFormatTanggal),
                        WriterEntityFactory::createCell($dataRows[4], $styleFormatTanggal)
                    ];*/
        
                    $rowDataKebun = WriterEntityFactory::createRow($cells2);
                    $writer->addRow($rowDataKebun);
                }
            }
            $writer->setCurrentSheet($CoachingActivity);
            $writer->close();
    
            $this->response(array('success' => TRUE, 'filenya' => base_url() . $filePath), 200);
            exit;
        }else{
            $this->response(array('success' => FALSE, 'filenya' => ''));
            exit;
        }
    }

}
