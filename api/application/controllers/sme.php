<?php
/**
 * @Author: nikolius
 * @Date:   2017-07-18 17:47:30
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//write excel
require_once 'application/third_party/Spout/Autoloader/autoload.php';

use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;
use Box\Spout\Writer\Style\StyleBuilder;
use Box\Spout\Writer\Style\Color;
use Box\Spout\Writer\Style\Border;
use Box\Spout\Writer\Style\BorderBuilder;

class Sme extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->file = $_FILES;
        $this->load->model('sme/msme_mem');
    }

    public function export_trader_farmers_get(){
        $MemberID = $_GET["MemberID"];

        $dataList       = $this->msme_mem->getTraderFarmer($MemberID);
        $dataListGarden = $this->msme_mem->getTraderFarmerGarden($MemberID);
        $mem_ini = ini_get('memory_limit');
        ini_set('memory_limit', '1048576M');

        //kolom yg tampil ========================================= (begin)
        $dataHeader = array('No.');
        $dataHeader[] = lang('SME ID');
        $dataHeader[] = lang('SME Name');
        $dataHeader[] = lang('Alias');
        $dataHeader[] = lang('Farmer ID');
        $dataHeader[] = lang('Farmer Name');
        $dataHeader[] = lang('Garden Total');
        $dataHeader[] = lang('Birth Date');
        $dataHeader[] = lang('NIK');
        $dataHeader[] = lang('Handphone');
        $dataHeader[] = lang('Province');
        $dataHeader[] = lang('District');
        $dataHeader[] = lang('Sub District');
        $dataHeader[] = lang('Village');
        $dataHeader[] = lang('Latitude');
        $dataHeader[] = lang('Longitude');
        //kolom yg tampil ========================================= (end)

        //kolom yg tampil ========================================= (begin)
        $dataHeaderGarden = array('No.');
        $dataHeaderGarden[] = lang('SME ID');
        $dataHeaderGarden[] = lang('SME Name');
        $dataHeaderGarden[] = lang('Alias');
        $dataHeaderGarden[] = lang('Farmer ID');
        $dataHeaderGarden[] = lang('Farmer Name');
        $dataHeaderGarden[] = lang('Garden Nr');
        $dataHeaderGarden[] = lang('Garden Area (Ha)');
        $dataHeaderGarden[] = lang('First Planting Year');
        $dataHeaderGarden[] = lang('Year Planting Current');
        $dataHeaderGarden[] = lang('Soil Type');
        $dataHeaderGarden[] = lang('Ownership Document');
        $dataHeaderGarden[] = lang('Annual Production');
        $dataHeaderGarden[] = lang('Plantation Productivity');
        $dataHeaderGarden[] = lang('Province');
        $dataHeaderGarden[] = lang('District');
        $dataHeaderGarden[] = lang('Sub District');
        $dataHeaderGarden[] = lang('Village');
        $dataHeaderGarden[] = lang('Latitude');
        $dataHeaderGarden[] = lang('Longitude');
        //kolom yg tampil ========================================= (end)

        $dataListExcel = array();

        if ($dataList["total"] > 0) {

            //generate datalist excel ========================================== (begin)
            foreach ($dataList["data"] as $key => $value) {
                $dataListExcel[$key][] = $key+1; //increment No.

                $dataListExcel[$key][] = $value['SMEID'];
                $dataListExcel[$key][] = $value['SMEName'];
                $dataListExcel[$key][] = $value['Alias'];
                $dataListExcel[$key][] = $value['FarmerID'];
                $dataListExcel[$key][] = $value['FarmerName'];
                $dataListExcel[$key][] = $value['GardenNr'];
                $dataListExcel[$key][] = $value['DateOfBirth'];
                $dataListExcel[$key][] = $value['Nin'];
                $dataListExcel[$key][] = $value['Handphone'];
                $dataListExcel[$key][] = $value['Province'];
                $dataListExcel[$key][] = $value['District'];
                $dataListExcel[$key][] = $value['SubDistrict'];
                $dataListExcel[$key][] = $value['Village'];
                $dataListExcel[$key][] = $value['Latitude'];
                $dataListExcel[$key][] = $value['Longitude'];

                $increData++;
            }
            //generate datalist excel ========================================== (end)
            //echo '<pre>'; print_r($dataListExcel); echo '</pre>'; exit;
        }

        $dataListExcelGarden = array();

        if ($dataListGarden["total"] > 0) {

            //generate datalist excel ========================================== (begin)
            foreach ($dataListGarden["data"] as $key2 => $value2) {
                $dataListExcelGarden[$key2][] = $key2+1; //increment No.

                $dataListExcelGarden[$key2][] = $value2['SMEID'];
                $dataListExcelGarden[$key2][] = $value2['SMEName'];
                $dataListExcelGarden[$key2][] = $value2['Alias'];
                $dataListExcelGarden[$key2][] = $value2['FarmerID'];
                $dataListExcelGarden[$key2][] = $value2['FarmerName'];
                $dataListExcelGarden[$key2][] = $value2['PlotNr'];
                $dataListExcelGarden[$key2][] = $value2['GardenAreaHa'];
                $dataListExcelGarden[$key2][] = $value2['FirstPlantingYear'];
                $dataListExcelGarden[$key2][] = $value2['YearPlantingCurrent'];
                $dataListExcelGarden[$key2][] = $value2['SoilType'];
                $dataListExcelGarden[$key2][] = $value2['OwnershipDocument'];
                $dataListExcelGarden[$key2][] = $value2['AnnualProduction'];
                $dataListExcelGarden[$key2][] = $value2['PlantationProductivity'];
                $dataListExcelGarden[$key2][] = $value2['Province'];
                $dataListExcelGarden[$key2][] = $value2['District'];
                $dataListExcelGarden[$key2][] = $value2['SubDistrict'];
                $dataListExcelGarden[$key2][] = $value2['Village'];
                $dataListExcelGarden[$key2][] = $value2['Latitude'];
                $dataListExcelGarden[$key2][] = $value2['Longitude'];

                $increData2++;
            }
            //generate datalist excel ========================================== (end)
            //echo '<pre>'; print_r($dataListExcel); echo '</pre>'; exit;
        }

        $writer = WriterFactory::create(Type::XLSX); // for XLSX files
        //$writer = WriterFactory::create(Type::CSV); // for CSV files
        //$writer = WriterFactory::create(Type::ODS); // for ODS files

        $writer->setTempFolder('files/tmp/');
        $namaFile = date('YmdHis') . '_export_excel_trader_farmers.xlsx';
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

        //Farmer Sheet Begin
        $writer->addRowWithStyle($dataHeader, $styleHeader); // add a row at a time
        //style data
        $styleData = (new StyleBuilder())
                ->setBorder($borderDefa)
                ->build();

        //data
        $writer->addRowsWithStyle($dataListExcel, $styleData);
        $writer->getCurrentSheet()->setName(lang('Farmer'));
        //Farmer Sheet End

        // Garden Sheet Begin
        $writer->addNewSheetAndMakeItCurrent()->setName(lang('Garden'));
        // header Sheet kedua
        $writer->addRowWithStyle($dataHeaderGarden, $styleHeader);
        // data Sheet kedua
        $writer->addRowsWithStyle($dataListExcelGarden,$styleData);
        // Garden Sheet End

        $writer->close();
        ini_set('memory_limit', $mem_ini);
        $this->load->helper('download');
        force_download($filePath, null);
    }

    public function export_traders_mill_get(){
        //set bahasa
        if($_SESSION['language'] == "Indonesia"){
            $this->load->language('general', 'indonesia');
        }else{
            $this->load->language('general', 'english');
        }

        //get param
        // $pSearch = array(
        //     'prov' => $this->get('prov'),
        //     'kab' => $this->get('kab'),
        //     'kec' => $this->get('kec'),
        //     'textSearch' => $this->get('textSearch'),
        //     'textSearchDesa' => $this->get('textSearchDesa')
        // );
        $pSearch = array(
            'prov' => $this->get('prov'),
            'kab' => $this->get('kab'),
            'kec' => $this->get('kec'),
            'textSearch' => $this->get('textSearch'),
            'textSearchDesa' => $this->get('textSearchDesa'),
            'roleSearch' => $this->get('roleSearch'),
            'AdvRowHandphone' => $this->get('AdvRowHandphone'),
            'AdvTextHandphone' => $this->get('AdvTextHandphone'),
            'AdvRowAge' => $this->get('AdvRowAge'),
            'AdvOpAge' => $this->get('AdvOpAge'),
            'AdvTextAge' => $this->get('AdvTextAge')
        );

        $dataList = $this->msme_mem->getGridMainTrader($pSearch,null,null,null,null);
        
        if ($dataList["total"] > 0) {
            $mem_ini = ini_get('memory_limit');
            ini_set('memory_limit', '1048576M');

            //kolom yg tampil ========================================= (begin)
            $dataHeader = array('No.');
            $dataHeader[] = lang('SME ID');
            $dataHeader[] = lang('SME Name');
            $dataHeader[] = lang('Nr Of Farmer');
            $dataHeader[] = lang('SME Status');
            $dataHeader[] = lang('Latitude');
            $dataHeader[] = lang('Longitude');
            $dataHeader[] = lang('Trader Name');
            $dataHeader[] = lang('Birth Date');
            $dataHeader[] = lang('Age');
            $dataHeader[] = lang('Handphone');
            $dataHeader[] = lang('Province');
            $dataHeader[] = lang('District');
            $dataHeader[] = lang('Sub District');
            $dataHeader[] = lang('Village');
            $dataHeader[] = lang('Enumerator');
            //kolom yg tampil ========================================= (end)

            //generate datalist excel ========================================== (begin)
            $dataListExcel = array();
            foreach ($dataList["data"] as $key => $value) {
                $dataListExcel[$key][] = $key+1; //increment No.

                $dataListExcel[$key][] = $value['id'];
                $dataListExcel[$key][] = $value['agCompanyName'];
                $dataListExcel[$key][] = $value['NrFarmer'];
                $dataListExcel[$key][] = $value['StatusSME'];
                $dataListExcel[$key][] = $value['Latitude'];
                $dataListExcel[$key][] = $value['Longitude'];
                $dataListExcel[$key][] = $value['agCompanyName'];
                $dataListExcel[$key][] = $value['Birthdate'];
                $dataListExcel[$key][] = $value['Age'];
                $dataListExcel[$key][] = $value['Handphone'];
                $dataListExcel[$key][] = $value['Province'];
                $dataListExcel[$key][] = $value['District'];
                $dataListExcel[$key][] = $value['Kecamatan'];
                $dataListExcel[$key][] = $value['Desa'];
                $dataListExcel[$key][] = $value['Enumerator'];

                $increData++;
            }
            //generate datalist excel ========================================== (end)
            //echo '<pre>'; print_r($dataListExcel); echo '</pre>'; exit;

            $writer = WriterFactory::create(Type::XLSX); // for XLSX files
            //$writer = WriterFactory::create(Type::CSV); // for CSV files
            //$writer = WriterFactory::create(Type::ODS); // for ODS files

            $writer->setTempFolder('files/tmp/');
            $namaFile = date('YmdHis') . '_export_excel_trader.xlsx';
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

            //data
            $writer->addRowsWithStyle($dataListExcel, $styleData);

            $writer->close();
            ini_set('memory_limit', $mem_ini);
            $this->load->helper('download');
            force_download($filePath, null);
        }
    }

    public function export_traders_get(){
        //set bahasa
        if($_SESSION['language'] == "Indonesia"){
            $this->load->language('general', 'indonesia');
        }else{
            $this->load->language('general', 'english');
        }

        //get param
        // $pSearch = array(
        //     'prov' => $this->get('prov'),
        //     'kab' => $this->get('kab'),
        //     'kec' => $this->get('kec'),
        //     'textSearch' => $this->get('textSearch'),
        //     'textSearchDesa' => $this->get('textSearchDesa')
        // );
        $pSearch = array(
            'prov' => $this->get('prov'),
            'kab' => $this->get('kab'),
            'kec' => $this->get('kec'),
            'textSearch' => $this->get('textSearch'),
            'textSearchDesa' => $this->get('textSearchDesa'),
            'roleSearch' => $this->get('roleSearch'),
            'AdvRowHandphone' => $this->get('AdvRowHandphone'),
            'AdvTextHandphone' => $this->get('AdvTextHandphone'),
            'AdvRowAge' => $this->get('AdvRowAge'),
            'AdvOpAge' => $this->get('AdvOpAge'),
            'AdvTextAge' => $this->get('AdvTextAge')
        );

        $dataList = $this->msme_mem->getGridMainTrader($pSearch,null,null,null,null);
        
        if ($dataList["total"] > 0) {
            $mem_ini = ini_get('memory_limit');
            ini_set('memory_limit', '1048576M');

            //kolom yg tampil ========================================= (begin)
            $dataHeader = array('No.');
            $dataHeader[] = lang('Trader ID');
            $dataHeader[] = lang('Company Name');
            $dataHeader[] = lang('Mill Name');
            $dataHeader[] = lang('Latitude');
            $dataHeader[] = lang('Longitude');
            $dataHeader[] = lang('Trader Name');
            $dataHeader[] = lang('Birth Date');
            $dataHeader[] = lang('Age');
            $dataHeader[] = lang('Handphone');
            $dataHeader[] = lang('Province');
            $dataHeader[] = lang('District');
            $dataHeader[] = lang('Sub District');
            $dataHeader[] = lang('Village');
            $dataHeader[] = lang('Enumerator');
            //kolom yg tampil ========================================= (end)

            //generate datalist excel ========================================== (begin)
            $dataListExcel = array();
            foreach ($dataList["data"] as $key => $value) {
                $dataListExcel[$key][] = $key+1; //increment No.

                $dataListExcel[$key][] = $value['id'];
                $dataListExcel[$key][] = $value['agCompanyName'];
                $dataListExcel[$key][] = $value['MillName'];
                $dataListExcel[$key][] = $value['Latitude'];
                $dataListExcel[$key][] = $value['Longitude'];
                $dataListExcel[$key][] = $value['Name'];
                $dataListExcel[$key][] = $value['Birthdate'];
                $dataListExcel[$key][] = $value['Age'];
                $dataListExcel[$key][] = $value['Handphone'];
                $dataListExcel[$key][] = $value['Province'];
                $dataListExcel[$key][] = $value['District'];
                $dataListExcel[$key][] = $value['Kecamatan'];
                $dataListExcel[$key][] = $value['Desa'];
                $dataListExcel[$key][] = $value['Enumerator'];

                $increData++;
            }
            //generate datalist excel ========================================== (end)
            //echo '<pre>'; print_r($dataListExcel); echo '</pre>'; exit;

            $writer = WriterFactory::create(Type::XLSX); // for XLSX files
            //$writer = WriterFactory::create(Type::CSV); // for CSV files
            //$writer = WriterFactory::create(Type::ODS); // for ODS files

            $writer->setTempFolder('files/tmp/');
            $namaFile = date('YmdHis') . '_export_excel_trader.xlsx';
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

            //data
            $writer->addRowsWithStyle($dataListExcel, $styleData);

            $writer->close();
            ini_set('memory_limit', $mem_ini);
            $this->load->helper('download');
            force_download($filePath, null);
        }
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
        $sortingField = @$sorting[0]->property;
        $sortingDir = @$sorting[0]->direction;

        //get param
        $pSearch = array(
            'prov' => $this->get('prov'),
            'kab' => $this->get('kab'),
            'kec' => $this->get('kec'),
            'source' => $this->get('source'),
            'textSearch' => $this->get('textSearch'),
            'textSearchDesa' => $this->get('textSearchDesa'),
            'roleSearch' => $this->get('roleSearch'),
            'AdvRowHandphone' => $this->get('AdvRowHandphone'),
            'AdvTextHandphone' => $this->get('AdvTextHandphone'),
            'AdvRowAge' => $this->get('AdvRowAge'),
            'AdvOpAge' => $this->get('AdvOpAge'),
            'AdvTextAge' => $this->get('AdvTextAge')
        );

        $data = $this->msme_mem->getGridMainTrader($pSearch,$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        $this->response($data, 200);
    }

    public function grid_agent_relation_get(){
        $MemberID = $_GET["MemberID"];

        $data = $this->msme_mem->getGridAgentRelation($MemberID);
        $this->response($data, 200);
    }

    public function submit_sp_code_post(){
        $data = array(
            "MemberID"  => $_POST["MemberID"],
            "SPCodeID"  => $_POST["SPCodeID"],
            "DateStart" => $_POST["DateStart"],
            "DateEnd"   => $_POST["DateEnd"],
            "Remarks"   => $_POST["Remarks"]
        );

        if(isset($_POST["SMESPCodeID"]) AND $_POST["SMESPCodeID"] != ""){
            $data["UpdatedBy"] = $_SESSION["userid"];
            $data["DateUpdated"] = date("Y-m-d H:i:s");
            $this->db->where("SMESPCodeID",$_POST["SMESPCodeID"]);
            $post = $this->db->update("ktv_sme_sp_code",$data);
        }else{
            $data["DateCreated"] = date("Y-m-d H:i:s");
            $data["CreatedBy"] = $_SESSION["userid"];
            $post = $this->db->insert("ktv_sme_sp_code",$data);
        }

        $this->response($post,200);
    }

    public function submit_sp_code_delete(){
        $SMESPCodeID   = $this->delete('SMESPCodeID');

        $this->db->where("SMESPCodeID",$SMESPCodeID);
        $result = $this->db->delete("ktv_sme_sp_code");

        $this->response($result,200);
    }

    public function sp_code_form_get(){
        $SMESPCodeID    = $this->get('SMESPCodeID');
        $MemberID       = $this->get('MemberID');

        $sql = "
            SELECT
                a.SMESPCodeID
                , a.SPCodeID
                , a.MemberID
                , a.DateStart
                , a.DateEnd
                , a.Remarks
                , b.SuratNr
                , m.MillName
                , b.MillID
            FROM
                `ktv_sme_sp_code` a
            LEFT JOIN
                ktv_mill_sp_code b on b.SPCodeID = a.SPCodeID
            LEFT JOIN
                ktv_mill m on m.MillID = b.MillID
            WHERE
                MemberID = ?
                AND a.SMESPCodeID = ?
        ";

        $query = $this->db->query($sql,array($MemberID,$SMESPCodeID));
        $data = array();
        if($query->num_rows()>0){
            $row = $query->row();
            $data["Koltiva.view.SME.WinFormSPCode-Form-SMESPCodeID"] = $row->SMESPCodeID;
            $data["Koltiva.view.SME.WinFormSPCode-Form-SPCodeID"] = $row->SPCodeID;
            $data["SPCodeID"] = $row->SPCodeID;
            $data["Koltiva.view.SME.WinFormSPCode-Form-MillID"] = $row->MillID;
            $data["MillID"] = $row->MillID;
            $data["Koltiva.view.SME.WinFormSPCode-Form-MemberID"] = $row->MemberID;
            $data["Koltiva.view.SME.WinFormSPCode-Form-DateStart"] = date("Y-m-d",strtotime($row->DateStart));
            $data["Koltiva.view.SME.WinFormSPCode-Form-DateEnd"] = date("Y-m-d",strtotime($row->DateEnd));
            $data["Koltiva.view.SME.WinFormSPCode-Form-Remarks"] = $row->Remarks;
        }
        $result['success'] = true;
        $result['data'] = $data;

        $this->response($result,200);
    }

    public function cmb_mill_sme_get(){
        $data = $this->msme_mem->getComboMillSME($this->get('MemberID'));
        $this->response($data, 200);
    }

    public function cmb_sp_code_get(){
        $data = $this->msme_mem->getComboSPCode($this->get('MillID'));
        $this->response($data, 200);
    }

    public function grid_sp_code_get(){
        $MemberID = $this->get("MemberID");
        $data = $this->msme_mem->getSPCode($this->get('MemberID'));
        $this->response($data, 200);
    }

    public function member_basic_data_form_get(){
        $data = $this->msme_mem->getMemberBasicDataForm($this->get('MemberID'));
        $this->response($data, 200);
    }
	
	public function grid_trader_warehouses_get(){
        $data = $this->msme_mem->getGrid_trader_warehouses($this->get('MemberID'));
        $this->response($data, 200);
    }
	
	public function warehouses_form_get(){
        $data = $this->msme_mem->getWarehousesForm($this->get('MemberID'), $this->get('WarehousesNr'));
        $this->response($data, 200);
    }

    public function setPartnerSME_post(){
        $varPost = $this->post();

        foreach ($varPost as $key => $value) {
            if ($value == "") {
                $value = null;
            }
            $key = str_replace("Koltiva_view_SME_FormMainTrader-","",$key);          
            $paramPost[$key] = $value;
        }

        $data = $this->msme_mem->setPartnerSME($paramPost);
        $this->response($data, 200);
    }

    public function image_member_post(){
        $this->load->library('awsfileupload');
        if($this->post('opsiDisplay') == "insert"){
            //ketika insert

            if ($this->file['Koltiva_view_SME_FormMainTrader-MemberPhotoInput']['name'] != '') {
                $gambar = date('Ymdhis') . '_' . $this->file['Koltiva_view_SME_FormMainTrader-MemberPhotoInput']['name'];
                $fileupload['Koltiva_view_SME_FormMainTrader-MemberPhotoInput'] = $this->file['Koltiva_view_SME_FormMainTrader-MemberPhotoInput'];

                //cek folder sudah ada belum
                if (!file_exists('images/trader/temp')) {
                    mkdir('images/trader/temp', 0777, true);
                }

                $upload = move_upload($fileupload, 'images/trader/temp/' . $gambar);
                if (isset($upload['upload_data'])) {
                    $result['success'] = true;
                    $result['file']         = base_url().'images/trader/temp/' . $gambar;
                    $result['filepath']     = 'images/trader/temp/' .$gambar;
                    $this->response($result, 200);
                } else {
                    $result['success'] = false;
                    $result['message'] = lang('Upload failed');
                    $this->response($result, 400);
                }
            }
        }

        if($this->post('opsiDisplay') == "update"){
            //ketika update
            $upload = $this->awsfileupload->upload($this->file['Koltiva_view_SME_FormMainTrader-MemberPhotoInput']['tmp_name'],$this->file['Koltiva_view_SME_FormMainTrader-MemberPhotoInput']['name'], AWSS3_SME_PATH, 'images');
            if ($upload['success'] == true) {
                if($this->awsfileupload->doesObjectExist($this->post('Koltiva_view_SME_FormMainTrader-MemberPhotoOld')) == true) {
                    $this->awsfileupload->delete($this->post('Koltiva_view_SME_FormMainTrader-MemberPhotoOld'));
                }else{
                    delete_file($this->post('Koltiva_view_SME_FormMainTrader-MemberPhotoOld'));
                }
                $prosesUpdate = $this->msme_mem->updateFotoProfile($_POST["MemberID"],$upload['filenamepath']);
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

    public function image_member_business_logo_post(){
        $this->load->library('awsfileupload');
        if($this->post('opsiDisplay') == "insert"){
            //ketika insert
            if ($this->file['Koltiva_view_SME_FormMainTrader-agCompanyLogoInput']['name'] != '') {
                $gambar = date('Ymdhis') . '_' . $this->file['Koltiva_view_SME_FormMainTrader-agCompanyLogoInput']['name'];
                $fileupload['Koltiva_view_SME_FormMainTrader-agCompanyLogoInput'] = $this->file['Koltiva_view_SME_FormMainTrader-agCompanyLogoInput'];

                //cek folder sudah ada belum
                if (!file_exists('images/trader/temp')) {
                    mkdir('images/trader/temp', 0777, true);
                }

                $upload = move_upload($fileupload, 'images/trader/temp/' . $gambar);
                if (isset($upload['upload_data'])) {
                    $result['success'] = true;
                    $result['file']         = base_url().'images/trader/temp/' . $gambar;
                    $result['filepath']     = 'images/trader/temp/' .$gambar;
                    $this->response($result, 200);
                } else {
                    $result['success'] = false;
                    $result['message'] = lang('Upload failed');
                    $this->response($result, 400);
                }
            }
        }

        if($this->post('opsiDisplay') == "update"){
            //ketika update
            $upload = $this->awsfileupload->upload($this->file['Koltiva_view_SME_FormMainTrader-agCompanyLogoInput']['tmp_name'],$this->file['Koltiva_view_SME_FormMainTrader-agCompanyLogoInput']['name'], AWSS3_SME_LOGO_PATH, 'images');
            if ($upload['success'] == true) {
                if($this->awsfileupload->doesObjectExist($this->post('Koltiva_view_SME_FormMainTrader-agCompanyLogoOld')) == true) {
                    $this->awsfileupload->delete($this->post('Koltiva_view_SME_FormMainTrader-agCompanyLogoOld'));
                }else{
                    delete_file($this->post('Koltiva_view_SME_FormMainTrader-agCompanyLogoOld'));
                }
                $prosesUpdate = $this->msme_mem->updateFotoCompanyLogo($_POST["MemberID"],$upload['filenamepath']);
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

    public function image_member_business_photo_post(){
        $this->load->library('awsfileupload');
        //print_r($this->post());die;
		if($this->post('opsiDisplay') == "insert"){ 
            //ketika insert 
            if ($this->file['Koltiva_view_SME_WinFormWarehouses-agBusinessLocationInput']['name'] != '') {
                $gambar = date('Ymdhis') . '_' . $this->file['Koltiva_view_SME_WinFormWarehouses-agBusinessLocationInput']['name'];
                $fileupload['Koltiva_view_SME_WinFormWarehouses-agBusinessLocationInput'] = $this->file['Koltiva_view_SME_WinFormWarehouses-agBusinessLocationInput'];
                
                //cek folder sudah ada belum
                if (!file_exists('images/trader/temp')) {
                    mkdir('images/trader/temp', 0777, true);
                }

                $upload = move_upload($fileupload, 'images/trader/temp/' . $gambar);
                if (isset($upload['upload_data'])) {
                    $result['success'] = true;
                    $result['file']         = base_url().'images/trader/temp/' . $gambar;
                    $result['filepath']     = 'images/trader/temp/' .$gambar;
                    $this->response($result, 200);
                } else {
                    $result['success'] = false;
                    $result['message'] = lang('Upload failed');
                    $this->response($result, 400);
                }
            }
        }

        if($this->post('opsiDisplay') == "update"){
            //ketika update
            $upload = $this->awsfileupload->upload($this->file['Koltiva_view_SME_WinFormWarehouses-agBusinessLocationInput']['tmp_name'],$this->file['Koltiva_view_SME_WinFormWarehouses-agBusinessLocationInput']['name'], AWSS3_WH_SME_PLOT_PATH, 'images');
            if ($upload['success'] == true) {
                if($this->awsfileupload->doesObjectExist($this->post('Koltiva_view_SME_WinFormWarehouses-agBusinessLocationOld')) == true) {
                    $this->awsfileupload->delete($this->post('Koltiva_view_SME_WinFormWarehouses-agBusinessLocationOld'));
                }else{
                    @unlink($this->post('Koltiva_view_SME_WinFormWarehouses-agBusinessLocationOld'));
                }
                $prosesUpdate = $this->msme_mem->updateFotoWarehouse($_POST["MemberID"],$upload['filenamepath']);
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

    public function member_post(){
        if($this->post('Koltiva_view_SME_FormMainTrader-MemberID') == ""){
            //insert
            $proses = $this->msme_mem->insertMember($this->post());
        }else{
            //update
            $proses = $this->msme_mem->updateMember($this->post());
        }
        $this->response($proses, 200);
    }

    public function member_delete(){
        $MemberID = (int) $this->delete('MemberID');
        $proses = $this->msme_mem->deleteMember($MemberID);
        $this->response($proses, 200);
    }	
	
	public function warehouses_post(){
        //print_r($this->post());die;
		if($this->post('opsiDisplay') == "insert"){ 
			 //cek apakah data sudah ada
            $isExist = $this->msme_mem->checkIfWarehouseExist($this->post());
            if($isExist == true){
                $proses['success'] = false;
                $proses['message'] = lang('Warehouses already exist');
                $this->response($proses, 200);
            } 
			//insert
            $proses = $this->msme_mem->insertWarehouses($this->post());
        }else{
            //update
            $proses = $this->msme_mem->updateWarehouses($this->post());
        }
        $this->response($proses, 200);
    }
	
	public function warehouses_delete(){
        $MemberID = (int) $this->delete('MemberID');
		$WarehousesNr = (int) $this->delete('WarehousesNr'); 
        $proses = $this->msme_mem->deleteWarehouses($MemberID, $WarehousesNr);
        $this->response($proses, 200);
    }
	
    public function grid_trader_staff_get(){
        $data = $this->msme_mem->getGridTraderStaff($this->get('MemberID'));
        $this->response($data, 200);
    }

    public function grid_trader_vehicle_get(){
        //set bahasa
        if($_SESSION['language'] == "Indonesia"){
            $this->load->language('general', 'indonesia');
        }else{
            $this->load->language('general', 'english');
        }

        $data = $this->msme_mem->getGridTraderVehicle($this->get('MemberID'));
        $this->response($data, 200);
    }

    public function cmb_brand_vehicle_get(){
        $data = $this->msme_mem->getCmbBrandVehicle();
        $this->response($data, 200);
    }

    public function cmb_work_area_get(){
        $data = $this->msme_mem->getCmbWorkArea();
        $this->response($data, 200);
    }

    public function cmb_sme_role_get(){
        $data = $this->msme_mem->getCmbSmeRole($this->get('RoleType'),$this->get('Partner'));
        $this->response($data, 200);
    }

    public function cmb_sme_type_get(){
        $data = $this->msme_mem->getCmbSmeType();
        $this->response($data, 200);
    }

    public function cmb_staff_trader_get(){
        $data = $this->msme_mem->getCmbTraderStaff($this->get('MemberID'));
        $this->response($data, 200);
    }

    public function trader_vehicle_form_get(){
        $data = $this->msme_mem->getTraderVehicleFormData($this->get('VehID'));
        $this->response($data, 200);
    }

    public function trader_vehicle_post(){
        $varPost = $this->post();

        //prep variabel (begin)
        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_SME_WinFormVehicle-Form-", '', $key);
            if ($value == "")
                $value = null;
            /*
            switch ($keyNew) {
                case 'VehCapacity':
                    $value = str_replace(",", "", $value);
                break;
            }
            */
            $paramPost[$keyNew] = $value;
        }
        //prep variabel (end)

        if($paramPost['VehID'] == ""){
            //insert
            $proses = $this->msme_mem->insertVehicle($paramPost);
        }else{
            //update
            $proses = $this->msme_mem->updateVehicle($paramPost);
        }
        $this->response($proses, 200);
    }

    public function trader_vehicle_delete(){
        $VehID = (int) $this->delete('VehID');
        $proses = $this->msme_mem->deleteVehicle($VehID);
        $this->response($proses, 200);
    }
    
    public function grid_trader_survey_sta_get() {
        $data = $this->msme_mem->getGridPlotSurveySta($this->get('MemberID'));
        $this->response($data, 200);        
    }
    
    public function trader_survey_sta_post() {
        $varPost = $this->post();

        //prep variabel (begin)
        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-", '', $key);
            if($value == "") $value = null;

            switch ($keyNew) {
                case 'GardenAreaHa':
                case 'GardenAreaPolygon':
                case 'GardenLength':
                case 'GardenWidth':
                case 'AverageAgeTree':
                case 'HarvestRateDaysHighSeason':
                case 'HarvestRateDaysLowSeason':
                case 'AverageProdHighSeason':
                case 'AverageProdLowSeason':
                case 'HowManyWorkFarm':
                case 'AveHoursPerDay':
                case 'AveDaysPerMonth':
                case 'WageNominalPerDayLabor':
                case 'WageNominalPerDayFamMember':
                case 'TypePlantMateMarihatNr':
                case 'TypePlantMateDumpyNr':
                case 'TypePlantMateLonsumNr':
                case 'TypePlantMateSimalungunNr':
                case 'TypePlantMateDanimasNr':
                case 'TypePlantMateSriwijayaNr':
                case 'TypePlantMateSocfinNr':
                case 'TypePlantMateOtherNr':
                case 'TypePlantMateDoNotKnowNr':
                case 'NrHighSeasonMonths':
                case 'NrLowSeasonMonths':
                case 'HighSeasonProduction':
                case 'LowSeasonProduction':
                case 'AnnualProduction':
                case 'PlantationProductivity':
                case 'FertMoneySpentNonOrganic':
                case 'FertUreaTimesYear':
                case 'FertUreaDose':
                case 'FertSSTimesYear':
                case 'FertSSDose':
                case 'FertNPKTimesYear':
                case 'FertNPKDose':
                case 'FertTSPTimesYear':
                case 'FertTSPDose':
                case 'FertCUTimesYear':
                case 'FertCUDose':
                case 'FertKCLTimesYear':
                case 'FertKCLDose':
                case 'FertNPKMutiTimesYear':
                case 'FertNPKMutiDose':
                case 'FertBoratTimesYear':
                case 'FertBoratDose':
                case 'FertDolomiteTimesYear':
                case 'FertDolomiteDose':
                case 'FertMoneySpentOrganic':
                case 'FertPBATimesYear':
                case 'FertPBADose':
                case 'FertPBTimesYear':
                case 'FertPBDose':
                case 'FertCPBTimesYear':
                case 'FertCPBDose':
                case 'FertManureTimesYear':
                case 'FertManureDose':
                case 'PeMoneySpentHerbi':
                case 'PeFreqHerbi':
                case 'PeDoseHerbi':
                case 'PeMoneySpentHerbi':
                case 'PeFreqHerbi':
                case 'PeDoseHerbi':
                case 'PeMoneySpentInsec':
                case 'PeFreqInsec':
                case 'PeDoseInsec':
                case 'PeMoneySpentFungi':
                case 'PeFreqFungi':
                case 'PeDoseFungi':
                case 'TreeTBM':
                case 'TreeTM':
                case 'TreeTR':
                    $value = str_replace(",","",$value);
                break;
            }

            $paramPost[$keyNew] = $value;
        }
        //prep variabel (end)

        //get member data
        $this->load->model('grower/mgrower');
        $getData = $this->mgrower->getMemberDataDetail($paramPost['MemberID']);
        $MemberData = $getData['data'];

        if($paramPost['opsiDisplay'] == 'insert'){

            //cek apakah data sudah ada
            $isExist = $this->msme_mem->checkIfSurveyExist($paramPost);
            if($isExist == true){
                $proses['success'] = false;
                $proses['message'] = lang('Survey already exist');
                $this->response($proses, 200);
            }

            $proses = $this->msme_mem->insertPlotSurvey($paramPost,$MemberData);
        }elseif($paramPost['opsiDisplay'] == 'update'){
            $proses = $this->msme_mem->updatePlotSurvey($paramPost,$MemberData);
        }
        $this->response($proses, 200);
        
    }
    
    public function trader_survey_sta_form_data_get() {
        $data = $this->msme_mem->getPlotSurveyFormData($this->get('MemberID'),$this->get('PlotNr'),$this->get('SurveyNr'),$this->get('DateCollection'));
        $this->response($data, 200);        
    }
	
	 public function cetak_trader_profiles_get(){
        //set bahasa
        if ($_SESSION['language'] == "Indonesia") {
            $this->load->language('general', 'indonesia');
        } else {
            $this->load->language('general', 'english');
        }

        //get param
        $MemberID = $this->get('MemberID');
        $MemberIDs = explode('::', $MemberID);
 
        $dataHeader['titleNya'] = "Trader Profile";
        $this->load->view('cetaktrader/cetak_trader_profiles_header', $dataHeader);

        if (strpos($MemberID, '::')) {
            $countData = count($MemberIDs);
            $increData = 1;
            foreach ($MemberIDs as $key => $MemberID) {
                $this->cetak_trader_profiles($MemberID, $countData, $increData);
                $increData++;
            }
        } else {
            $this->cetak_trader_profiles($MemberID);
        }

        $this->load->view('cetaktrader/cetak_trader_profiles_footer');
    }
	
	public function cetak_trader_profiles($MemberID, $countData = 1, $increData = 0){
        $data = array();
        
        //get data trader
        $dataMember = $this->msme_mem->getMemberDataDetail($MemberID);
        $data['trader'] = $dataMember['data'];
        
		
        //get jumlah staff
        $data['staff'] = $this->msme_mem->getStaffTrader($MemberID);

        //get kendaraan
        $data['NrOfVehicle'] = array();

        //get data training
        $data['training'] = array();;
         
        //logo
        //$this->load->model('project_process/mproject_process');
        $DistrictID = substr($data['trader']['VillageID'], 0,4);
        $data['logos'] = array(); //$this->mproject_process->getPrintLogoHeader($DistrictID);
        
        $data['ffb'] = $this->msme_mem->getFFBSales($MemberID);
        $data['traceability_details'] = $this->msme_mem->getTraceabilityDetails($MemberID);

        //echo '<pre>'; print_r($data); exit;
        $this->load->view('cetaktrader/cetak_trader_profiles', $data);
    }
	
	





}
?>