<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Dataquality extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('dataquality/mdataquality');
    }

    function dataqualitys_get() {
        $result = $this->mdataquality->getDataQuality($this->get('key'), $this->get('start'), $this->get('limit'));
        if ($result)
            $this->response($result, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }

    function dataqualities_post() {
        foreach ($this->post() as $key => $value) {
            if ($value == "") {
                $varPost[$key] = null;
            } else {
                $varPost[$key] = $value;
            }
        }
        $varPost['userid'] = $_SESSION['userid'];
        //var proses (end)

        if ($this->post('dq_id') == "") {
            //insert
            $proses = $this->mdataquality->insertDataQuality($varPost);
        } else {
            //update
            $proses = $this->mdataquality->updateDataQuality($varPost);
        }

        if ($proses) {
            $this->response($proses, 200);
        } else {
            $this->response('Process Failed', 400);
        }
    }

    function dataqualities_delete() {
        if (!$this->delete('id')) {
            $this->response(NULL, 400);
        }
        $data = $this->mdataquality->deleteDataQuality($this->delete('id'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Village could not be delete'), 404);
        }
    }

    function calculate_get() {
        $result = $this->mdataquality->calculateDataQualityItem($this->get('id'));
        if ($result) {
            $this->response($result, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }

    function calculate_cron_get() {
        $result = true;
        $items = $this->mdataquality->getActiveItems();

        if (count($items > 0)) {
            foreach ($items as $val) {
                $result = $result && $this->mdataquality->calculateDataQualityItem($val['dq_id']);
            }
        }

        if ($result) {
            echo "<pre>";
            print_r('success');
            echo "</pre>";
            exit;
        } else {
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }

    public function prep_run_query_get() {
        //memory limit set
//        $mem_ini = ini_get('memory_limit');
//        ini_set('memory_limit', '1048576M');

        $data = $this->mdataquality->getPrepRunQuery((int) $this->get('dq_id'));

        //memory limit set
//        ini_set('memory_limit', $mem_ini);

        $this->response($data, 200);
    }

    function data_quality_list_get() {
        //memory limit set
//        $mem_ini = ini_get('memory_limit');
//        ini_set('memory_limit', '1048576M');
        $data = $this->mdataquality->getMainListDataQualityQuery($this->get('dq_id'), $this->get('start'), $this->get('limit'), 'limit');
        //memory limit set
//        ini_set('memory_limit', $mem_ini);

        $this->response($data, 200);
    }

    function program_get() {
        $data = $this->mdataquality->readPrograms();
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
    }

    function programsection_get() {
        $dqprogram_id = $this->get('dqprogram_id');
        $data = $this->mdataquality->readProgramSections($dqprogram_id);
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
    }

    public function form_data_quality_get() {
        $data = $this->mdataquality->getFormDataQuality($this->get('dq_id'));
        $this->response($data, 200);
    }

    public function sql_view_export_excel_post() {
        ini_set('memory_limit', '-1');

        //ambil data  (begin)
        $dataList = $this->mdataquality->getMainListDataQualityQuery((int) $this->post('dq_id'), null, null, 'no_limit');

        $data = $this->mdataquality->getPrepRunQuery((int) $this->post('dq_id'));
        $dataKolom = $data['fieldNya'];

        //generate nama file excel
        $sqlViewName = $data['sqlViewName'];
        $sqlViewName = str_replace(' ', '_', $sqlViewName);

        //generate data header
        $dataHeader = array('No.');
        foreach ($dataKolom as $key => $value) {
            $dataHeader[] = $value['name'];
        }

        //generate data list
        $dataListExcel = array();
        foreach ($dataList as $key => $value) {
            array_unshift($value, ($key + 1));
            foreach ($value as $key1 => $value1) {

                //pengecualian untuk tidak diformat ke angka
                switch ($key1) {
                    case 'Nin':
                    case 'Handphone':
                    case 'Latitude':
                    case 'Longitude':
                        #No Convert
                        break;
                    default:
                        //cek tipe datanya
                        if (is_numeric($value1)) {
                            $value1 = (float) $value1;
                        }
                        break;
                }

                $dataListExcel[$key][] = $value1;
            }
        }
        //ambil data  (end)

        $writer = WriterFactory::create(Type::XLSX); // for XLSX files
        //$writer = WriterFactory::create(Type::CSV); // for CSV files
        //$writer = WriterFactory::create(Type::ODS); // for ODS files

        $writer->setTempFolder('files/data_quality_temp/');
        $namaFile = date('YmdHis') . '_' . $sqlViewName . '.xlsx';
        $filePath = 'files/data_quality/' . $namaFile;
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
        $writer->addRowWithStyle($dataHeader, $styleHeader); // add a row at a time
        //style data
        $styleData = (new StyleBuilder())
                ->setBorder($borderDefa)
                ->build();

        //data
        $writer->addRowsWithStyle($dataListExcel, $styleData);

        $writer->close();

        $this->response(array('success' => TRUE, 'filenya' => base_url() . 'files/data_quality/' . $namaFile), 200);
        exit;
    }

    function Provinsis_get() {
        $sesPartner = ($_SESSION['FlagAccess'] > 0) ? $_SESSION['PartnerID'] : 'ALL';
        $data = $this->mdataquality->readProvinsis($sesPartner);
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
    }

    function Kabupatens_get() {
        $sesPartner = ($_SESSION['PartnerID'] > 1) ? $_SESSION['PartnerID'] : 'ALL';
        $data = $this->mdataquality->readKabupatens($this->get('key'), $sesPartner);
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
    }

    function Cpgs_get() {
        $data = $this->mdataquality->readPrintoutCpg($this->get('key'), $this->get('kab'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function checktype_get() {
        $data = $this->mdataquality->readCheckType();
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    public function dataquality_excel_get($prov = '', $kab = '', $cpg = '', $check_type) {
        $start = 0;
        $limit = 100000;
        if ($prov == 'null') {
            $prov = '';
        }
        if ($kab == 'null') {
            $kab = '';
        }
        if ($cpg == 'null') {
            $cpg = '';
        }

        $result = $this->mdataquality->getDataQuality($prov, $kab, $cpg, $check_type, $start, $limit);

        if ($prov)
            $detail['provinsi'] = 'Province : ' . $this->mdataquality->getDetailProvince($prov);
        if ($kab)
            $detail['kabupaten'] = 'District : ' . $this->mdataquality->getDetailDistrict($kab);
        if ($cpg)
            $detail['cpg'] = 'CPG : ' . $this->mdataquality->getDetailCpg($cpg);
        if ($check_type)
            $detail['check_type'] = 'Check Type : ' . $this->mdataquality->getDetailCheckType($check_type);

        $data = $result['data'];

        foreach ($data[0] as $k => $v) {
            $header[] = $k;
        }

        $this->load->library('Excel', null, 'PHPExcel');
        $filename = 'Report Data Quality.xls';
        $this->PHPExcel->filename($filename);

        $sheet['title'] = 'Data Quality';
        $sheet['header'][] = 'Data Quality Report';
        $sheet['header'][] = $detail['provinsi'] . ', ' . $detail['kabupaten'] . ', ' . $detail['cpg'] . ', ' . $detail['check_type'];

        $sheet['cols'] = array(
            array(
                'name' => 'No',
                'data' => 'no',
                'size' => 5,
                'align' => 'center'
            )
        );

        foreach ($header as $key => $val) {
            $array_temp = array(
                'name' => ucfirst($val),
                'data' => $val,
                'size' => 30,
                'align' => 'left',
                    // 'wrap' => true,
            );
            array_push($sheet['cols'], $array_temp);
        }

        $sheet['data'] = $data;

        $path = $this->PHPExcel->create(compact('sheet'), '');
//        header('Location: '.base_url().$filename);
        exit;
    }

}
