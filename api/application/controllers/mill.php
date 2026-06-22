<?php
/**
 * @Author: nikolius
 * @Date:   2017-08-03 15:32:37
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//write excel
require_once 'application/third_party/Spout3/Autoloader/autoload.php';


use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
//use Box\Spout\Common\Entity\Style\CellAlignment;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Common\Entity\Style\Border;
use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\ErrorCorrectionLevel;

class Mill extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->file = $_FILES;
        $this->load->model('mill/mmill');
        $this->load->library('awsfileupload');
    }

    public function grid_sp_code_get(){
        $data = $this->mmill->getSPCode($this->get('MillID'));
        $this->response($data, 200);
    }

    public function get_mill_transaction_get(){
        $data = $this->mmill->getSupTransaction($this->get('PID'),$this->get('start'),$this->get('end'));
        $this->response($data, 200);
    }

    public function get_mill_dashboard_get(){
        list($supplier,$total_ttp) = $this->mmill->get_category_traceability($this->get('PID'), $this->get('year'));
        $data["farmer"]     = $this->mmill->get_farmer_mill($this->get('PID'));
        
        $data["supplier"]   = $supplier;
        
        $data["supplier_detail"]   = $this->mmill->get_supplier_detail($this->get('PID'));
        $this->response($data, 200);
    }

    public function get_mill_profile_get(){
        list($supplier,$total_ttp,$approvedby) = $this->mmill->get_category_traceability($this->get('PID'),$this->get('year'));
        $data["supplier"]   = $supplier;
        $data["total_ttp"]  = $total_ttp;
        $data["approvedby"]  = $approvedby;
        $data["mapped"]     = $this->mmill->get_mapped_farmer($this->get('PID'));
        $data["pemasok"]    = $this->mmill->get_pemasok($this->get('PID'));
        $data["basic"]      = $this->mmill->get_basic_data_mill($this->get('PID'));
        $this->response($data, 200);
    }

    public function sp_code_form_get(){
        $SPCodeID   = $this->get('SPCodeID');
        $MillID     = $this->get('MillID');

        $sql = "
            SELECT
                a.SPCodeID
                , a.MillID
                , a.SuratNr
                , a.Note
            FROM 
                `ktv_mill_sp_code` a
            WHERE
                a.MillID = ?
            AND a.SPCodeID = ?
        ";

        $query = $this->db->query($sql,array($MillID,$SPCodeID));
        $data = array();
        if($query->num_rows()>0){
            $row = $query->row();
            $data["Koltiva.view.Mill.WinFormSPCode-Form-SPCodeID"] = $row->SPCodeID;
            $data["Koltiva.view.Mill.WinFormSPCode-Form-MillID"] = $row->MillID;
            $data["Koltiva.view.Mill.WinFormSPCode-Form-SuratNr"] = $row->SuratNr;
            $data["Koltiva.view.Mill.WinFormSPCode-Form-Note"] = $row->Note;
        }
        $result['success'] = true;
        $result['data'] = $data;

        $this->response($result,200);
    }

    public function submit_sp_code_delete(){
        $SPCodeID   = $this->delete('SPCodeID');

        $this->db->where("SPCodeID",$SPCodeID);
        $result = $this->db->delete("ktv_mill_sp_code");

        $this->response($result,200);
    }

    public function submit_sp_code_post(){
        $data = array(
            "MillID" => $_POST["MillID"],
            "SuratNr" => $_POST["SuratNr"],
            "Note" => $_POST["Note"],
        );

        if(isset($_POST["SPCodeID"]) AND $_POST["SPCodeID"] != ""){
            $data["UpdatedBy"] = $_SESSION["userid"];
            $data["DateUpdated"] = date("Y-m-d H:i:s");
            $this->db->where("SPCodeID",$_POST["SPCodeID"]);
            $post = $this->db->update("ktv_mill_sp_code",$data);
        }else{
            $data["DateCreated"] = date("Y-m-d H:i:s");
            $data["CreatedBy"] = $_SESSION["userid"];
            $post = $this->db->insert("ktv_mill_sp_code",$data);
        }

        $this->response($post,200);
    }

    public function mill_summary_get(){
        $PartnerID     = $this->get("PartnerID");
        $Year       = $this->get("Year");
        $Period     = $this->get("Period");
        $MillTCDID  = $this->get("MillTCDID");

        $dataMill       = $this->mmill->getMillBasicDataFormNew($PartnerID);
        $dataTracable   = $this->mmill->getTracablePrint($PartnerID,$Year,$MillTCDID,$Period);

        $data["EstateInti"] = $this->mmill->getGridTracebilityDeclaration(
            $this->get('PartnerID')
            , $this->get('Year')
            , $this->get('MillTCDID')
            , $this->get('Period')
            , 2
        );

        $data["Plasma"] = $this->mmill->getGridTracebilityDeclaration(
            $this->get('PartnerID')
            , $this->get('Year')
            , $this->get('MillTCDID')
            , $this->get('Period')
            , 1
        );

        $data["Other"] = $this->mmill->getGridTracebilityDeclaration(
            $this->get('PartnerID')
            , $this->get('Year')
            , $this->get('MillTCDID')
            , $this->get('Period')
            , 4
        );

        $data["External"] = $this->mmill->getGridTracebilityDeclaration(
            $this->get('PartnerID')
            , $this->get('Year')
            , $this->get('MillTCDID')
            , $this->get('Period')
            , 3
        );

        if($dataMill["success"]){
            foreach($dataMill["data"] as $key => $value){
                $keyNew = str_replace("Koltiva.view.Mill.FormMainMill-FormBasicData-","",$key);
                if($value == "") $value = null;

                $data[$keyNew] = $value;
            }
        }

        if($Period == "half"){
            $data["ReportPeriodStart"] = date("d-M-Y",strtotime($Year."-01-01"));
            $data["ReportPeriodEnd"] = date("d-M-Y",strtotime($Year."-06-30"));
        }

        if($Period == "full"){
            $data["ReportPeriodStart"] = date("d-M-Y",strtotime($Year."-01-01"));
            $data["ReportPeriodEnd"] = date("d-M-Y",strtotime($Year."-12-31"));
        }

        $data["Year"]           = $Year;
        $data["Period"]         = $Period;
        $data["dataTracable"]   = $dataTracable;

        // $this->response($dataTracable,200);
        
        if($MillTCDID == ""){
            $this->load->view("mill/summary_report",$data);
        }else{
            $this->load->view("mill/summary_report_manual",$data);
        }
    }

    public function get_grid_report_locked_get(){
        $data = $this->mmill->getGridReportLocked($this->get("MillID"),$this->get("Year"),$this->get('start'),$this->get('limit'));

        $this->response($data,200);
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
            'rowStatusPerusahaan' => $this->get('rowStatusPerusahaan'),
            'cmbStatusPerusahaan' => $this->get('cmbStatusPerusahaan'),
            'rowTahunTerbentuk' => $this->get('rowTahunTerbentuk'),
            'cmbOpTahunTerbentuk' => $this->get('cmbOpTahunTerbentuk'),
            'textTahunTerbentuk' => $this->get('textTahunTerbentuk'),
            'rowPhone' => $this->get('rowPhone'),
            'textPhone' => $this->get('textPhone'),
            'rowHavePhoto' => $this->get('rowHavePhoto'),
            'cmbHavePhoto' => $this->get('cmbHavePhoto'),
            'rowTotalPermanentEmployee' => $this->get('rowTotalPermanentEmployee'),
            'cmbOpTotalPermanentEmployee' => $this->get('cmbOpTotalPermanentEmployee'),
            'textTotalPermanentEmployee' => $this->get('textTotalPermanentEmployee')
        );

        $data = $this->mmill->getGridMainMill($pSearch,$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        $this->response($data, 200);
    }

    public function get_supplier_list_get(){
        $MillTCDID  = (int) $this->get('MillTCDID');
        $data       = $this->mmill->getSupplierList($MillTCDID); 
        return $this->response($data, 200);
    }

    public function submit_tc_declaration_post(){
        $submit = $this->mmill->submit_tc_declaration($_POST);
        $this->response($submit,200);
    }

    public function submit_tc_declaration_new_post(){
        $submit = $this->mmill->submit_tc_declaration_new($_POST);
        if($submit["success"]){
            $response = array("success"=>true,"message"=>$submit["message"]);
            $this->response($response,200);
        }else{
            $response = array("success"=>false,"message"=>$submit["message"]);
            $this->response($response,400);
        }
    }

    public function form_tc_declaration_new_get(){
        $MillTCID  = $_GET["MillTCID"];

        $data       = $this->mmill->form_tc_declaration_new($MillTCID);
        $this->response($data,200);
    }

    public function form_tc_declaration_get(){
        $MillID     = $_GET["MillID"];
        $MillTCDID  = $_GET["MillTCDID"];

        $data       = $this->mmill->form_tc_declaration($MillID,$MillTCDID);
        $this->response($data,200);
    }

    public function form_tc_delete(){
        $MillTCID = $this->delete("MillTCID");
        $this->db->where("MillTCID",$MillTCID);
        $this->db->delete("ktv_mill_tc");

        $this->response(true,200);
    }

    public function form_tc_declaration_delete(){
        $MillTCDID  = $this->delete("MillTCDID");

        $data = array(
            "StatusCode" => 'nullified'
        );
        $this->db->where("MillTCDID",$MillTCDID);
        $this->db->update("ktv_mill_tc_declaration",$data);
        $this->response(true,200);
    }

    public function grid_main_tc_declaration_manual_get(){
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
            'textSearch' => $this->get('textSearch'),
            'MillID'     => $this->get('MillID')
        );

        $data = $this->mmill->getGridMainMillTCDeclarationManual($pSearch,$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        $this->response($data, 200);
    }

    public function setPartnerMill_post(){
        $varPost = $this->post();

        foreach ($varPost as $key => $value) {
            if ($value == "") {
                $value = null;
            }
            $key = str_replace("Koltiva_view_Mill_FormMainMill-FormBasicData-","",$key);          
            $paramPost[$key] = $value;
        }

        $data = $this->mmill->setPartnerMill($paramPost);
        $this->response($data, 200);
    }

    public function mill_as_partner_put(){
        $varPost = $this->put();

        foreach ($varPost as $key => $value) {
            if ($value == "") {
                $value = null;
            }         
            $paramPost[$key] = $value;
        }
        $data = $this->mmill->mill_as_partner($paramPost);
        $this->response($data, 200);
    }

    public function mill_tracebilityDeclaration_get(){
        $data = $this->mmill->getMilltracebilityDeclaration(
            $this->get('PartnerID')
            , $this->get('Year')
            , $this->get('Period')
        );
        $this->response($data, 200);
    }

    public function approving_post(){
        $PartnerID = $_POST["PartnerID"];
        $Year   = $_POST["Year"];
        $Period = $_POST["Period"];
        
        if($Period == "half2"){
            $startDate  = $Year."-07-01 00:00:00";
            $month = "-12-31";
            $endDate    = $Year.$month." 23:59:59";
        }else{
            $startDate  = $Year."-01-01 00:00:00";
            $month = "-06-30";
            $endDate    = $Year.$month." 23:59:59";
        }
        
        $sql = "
            UPDATE
                ktv_mill_tc tc
            LEFT JOIN
                ktv_mill m on m.MillID = tc.MillID
            SET tc.Approved = '1' ,tc.ApprovedBy = ?,tc.ApprovedDate = NOW()
            WHERE
                tc.DeliveryDate >= ?
            AND
                tc.DeliveryDate <= ?
            AND
                m.PartnerID = ?
        ";
        
        $query = $this->db->query($sql,array($_SESSION["userid"],$startDate,$endDate,$PartnerID));

        $this->response(true,200);
    }

    public function grid_company_owned_estate_get(){
        $data = $this->mmill->getGridTracebilityDeclaration(
            $this->get('PartnerID')
            , $this->get('Year')
            , $this->get('MillTCDID')
            , $this->get('Period')
            , 2
        );
        $this->response($data, 200);
    }

    public function grid_plasma_get(){
        $data = $this->mmill->getGridTracebilityDeclaration(
            $this->get('PartnerID')
            , $this->get('Year')
            , $this->get('MillTCDID')
            , $this->get('Period')
            , 1
        );
        $this->response($data, 200);
    }

    public function grid_external_get(){
        $data = $this->mmill->getGridTracebilityDeclaration(
            $this->get('PartnerID')
            , $this->get('Year')
            , $this->get('MillTCDID')
            , $this->get('Period')
            , 3
        );
        $this->response($data, 200);
    }

    public function grid_other_get(){
        $data = $this->mmill->getGridTracebilityDeclaration(
            $this->get('PartnerID')
            , $this->get('Year')
            , $this->get('MillTCDID')
            , $this->get('Period')
            , 4
        );
        $this->response($data, 200);
    }

    public function mill_basic_data_form_profile_get(){
        $data = $this->mmill->getMillBasicDataFormProfile($this->get('PartnerID'));
        $this->response($data, 200);
    }

    public function mill_basic_data_form_get(){
        $data = $this->mmill->getMillBasicDataForm($this->get('MillID'));
        $this->response($data, 200);
    }
    public function staff_assignment_get() {
        $data = $this->mmill->getStaffFAAssignment($this->get('MillID'));
        $this->response($data, 200);
    }
    public function list_staff_assignment_get() {
        $data = $this->mmill->getListStaffFAAssignment($this->get('MillID'));
        $this->response($data, 200);
    }
    public function setFaAssignment_post() {
        $data = $this->mmill->setStaffFAAssignment($this->post('MillID'), $this->post('itemselector-fa_assignment'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response($data, 404);
    }

    public function image_mill_post(){
        if($this->post('opsiDisplay') == "insert"){
            //ketika insert

            if ($this->file['Koltiva_view_Mill_FormMainMill-FormBasicData-MemberPhotoInput']['name'] != '') {
                $gambar = 'temp/'.$this->file['Koltiva_view_Mill_FormMainMill-FormBasicData-MemberPhotoInput']['name'];
                $fileupload['Koltiva_view_Mill_FormMainMill-FormBasicData-MemberPhotoInput'] = $this->file['Koltiva_view_Mill_FormMainMill-FormBasicData-MemberPhotoInput'];

                //cek folder sudah ada belum
                if (!file_exists('images/mill/temp')) {
                    mkdir('images/mill/temp', 0777, true);
                }

                $upload = move_upload($fileupload, 'images/mill/' . $gambar);
                if (isset($upload['upload_data'])) {
                    $result['success'] = true;
                    $result['file']         = base_url().'images/mill/'.$gambar;
                    $result['filepath']     = 'images/mill/'.$gambar;
                    $this->response($result, 200);
                } else {
                    echo  'false';
                    exit;
                }
            }
        }

        if($this->post('opsiDisplay') == "update"){
            //ketika update

            $upload = $this->awsfileupload->upload($this->file['Koltiva_view_Mill_FormMainMill-FormBasicData-MemberPhotoInput']['tmp_name'],$this->file['Koltiva_view_Mill_FormMainMill-FormBasicData-MemberPhotoInput']['name'], AWSS3_MILL_LOGO_PATH, 'images');
            if ($upload['success'] == true) {
                if($this->awsfileupload->doesObjectExist($this->post('Koltiva_view_Mill_FormMainMill-FormBasicData-PhotoOld')) == true) {
                    $this->awsfileupload->delete($this->post('Koltiva_view_Mill_FormMainMill-FormBasicData-PhotoOld'));
                }else{
                    delete_file($this->post('Koltiva_view_Mill_FormMainMill-FormBasicData-PhotoOld'));
                }
                $prosesUpdate = $this->mmill->UpdateImageMill($_POST["MillID"],$upload['filenamepath'],'Logo');
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

    public function image_mill_location_post(){
        if($this->post('opsiDisplay') == "insert"){
            //ketika insert

            if ($this->file['Koltiva_view_Mill_FormMainMill-FormBasicData-LocationPhotoInput']['name'] != '') {
                $gambar = 'temp/'.$this->file['Koltiva_view_Mill_FormMainMill-FormBasicData-LocationPhotoInput']['name'];
                $fileupload['Koltiva_view_Mill_FormMainMill-FormBasicData-LocationPhotoInput'] = $this->file['Koltiva_view_Mill_FormMainMill-FormBasicData-LocationPhotoInput'];

                //cek folder sudah ada belum
                if (!file_exists('images/mill_location/temp')) {
                    mkdir('images/mill_location/temp', 0777, true);
                }

                $upload = move_upload($fileupload, 'images/mill_location/' . $gambar);
                if (isset($upload['upload_data'])) {
                    $result['success'] = true;
                    $result['file']    = base_url().'images/mill_location/'.$gambar;
                    $result['filepath']    = 'images/mill_location/'.$gambar;
                    $this->response($result, 200);
                } else {
                    echo  'false';
                    exit;
                }
            }
        }

        if($this->post('opsiDisplay') == "update"){
            //ketika update

            $upload = $this->awsfileupload->upload($this->file['Koltiva_view_Mill_FormMainMill-FormBasicData-LocationPhotoInput']['tmp_name'],$this->file['Koltiva_view_Mill_FormMainMill-FormBasicData-LocationPhotoInput']['name'], AWSS3_MILL_LOCATION_PATH, 'images');
            if ($upload['success'] == true) {
                if($this->awsfileupload->doesObjectExist($this->post('Koltiva_view_Mill_FormMainMill-FormBasicData-LocationPhotoOld')) == true) {
                    $this->awsfileupload->delete($this->post('Koltiva_view_Mill_FormMainMill-FormBasicData-LocationPhotoOld'));
                }else{
                    delete_file($this->post('Koltiva_view_Mill_FormMainMill-FormBasicData-LocationPhotoOld'));
                }
                $prosesUpdate = $this->mmill->UpdateImageMill($_POST["MillID"],$upload['filenamepath'],'Location');
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

    public function get_source_name_get(){
        $PartnerID = $this->get("PartnerID");
        $SourceType = $this->get("SourceType");

        $result = $this->mmill->getSourceName($PartnerID,$SourceType,$this->get("KategoriKebun"));

        $this->response($result,200);
    }

    public function traceabiliy_generate_get(){
        $MillID = $this->get("MillID");

        $result = $this->mmill->generateDeclaration($MillID);

        $this->response($result,200);
    }

    public function mill_form_post(){
        if($this->post('Koltiva_view_Mill_FormMainMill-FormBasicData-MillID') == ""){
            //insert
            $proses = $this->mmill->insertMill($this->post());
        }else{
            //update
            $proses = $this->mmill->updateMill($this->post());
        }
        $this->response($proses, 200);
    }

    public function mill_profile_form_post(){
        if($this->post('Koltiva_view_Mill_FormMainMillProfile-FormBasicData-MillID') != ""){
            $proses = $this->mmill->updateMillProfile($this->post());
        }
        $this->response($proses, 200);
    }

    public function mill_form_delete(){
        $MillID = (int) $this->delete('MillID');
        $proses = $this->mmill->deleteMill($MillID);
        $this->response($proses, 200);
    }

    public function grid_mill_staff_get(){
        $data = $this->mmill->getGridMillStaff($this->get('MillID'));
        $this->response($data, 200);
    }

    public function cetak_mill_profiles_get(){
        //set bahasa
        if ($_SESSION['language'] == "Indonesia") {
            $this->load->language('general', 'indonesia');
        } else {
            $this->load->language('general', 'english');
        }

        //get param
        $MillID = $this->get('MillID');
        $MillIDs = explode('::', $MillID);

        $dataHeader['titleNya'] = "Mill Profile";
        $this->load->view('profiles/cetak_mill_profiles_header', $dataHeader);

        if (strpos($MillID, '::')) {
            $countData = count($MillIDs);
            $increData = 1;
            foreach ($MillIDs as $key => $MillID) {
                $this->cetak_mill_profiles($MillID, $countData, $increData);
                $increData++;
            }
        } else {
            $this->cetak_mill_profiles($MillID);
        }

        $this->load->view('profiles/cetak_mill_profiles_footer');
    }

    private function cetak_mill_profiles($MillID, $countData = 1, $increData = 0){
        $data = array(); 

        //get data
        $data['mill'] = $this->mmill->getMillDetailPrint($MillID);
        $data['NrOfStaff'] = $this->mmill->getMillNrOfStaff($MillID);

        //get data training
        $data['training'] = $this->mmill->getTrainingDataMill($MillID);
        $data['tc'] = $this->mmill->getTraceabilityDataMill($MillID);

        //logo
        $this->load->model('project_process/mproject_process');
        $DistrictID = substr($data['mill']['VillageID'], 0,4);
        $data['logos'] = $this->mproject_process->getPrintLogoHeaderMillNew($MillID, $DistrictID);
        $data['ffb'] = $this->mmill->getFFBSales($MillID);
        $data['traceability_details'] = $this->mmill->getTraceabilityDetails($MillID);

        $data['qrcode_pic']     = $this->QrcodeGenerator($data['mill']['MillDisplayID']);
        //echo '<pre>'; print_r($data); exit;
        $this->load->view('profiles/cetak_mill_profiles', $data);
    }

    public function QrcodeGenerator($FarmerID) {
        $file_code = 'files/tmp/' . $FarmerID . '.png';
        $qrCode = new QrCode($FarmerID);
        $qrCode->setSize(175);
        $qrCode->setEncoding('UTF-8');
        $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevel(ErrorCorrectionLevel::HIGH));
        $qrCode->setLogoPath('images/koltiva_logo_qrcode.jpg');
        $qrCode->setLogoSize(48);
        $qrCode->setRoundBlockSize(true);
        $qrCode->setValidateResult(false);
        $qrCode->setWriterOptions(['exclude_xml_declaration' => true]);
        $qrCode->writeFile($file_code);

        $path = '';
        if (file_exists('files/tmp/' . $FarmerID . '.png')) {
            $path = 'files/tmp/' . $FarmerID . '.png';
        }

        return $path;
    }

    public function combo_mill_group_get(){
        $data = $this->mmill->getMillGroups();
        $this->response($data, 200);
    }

    public function export_mill_get(){
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        error_reporting(E_ALL);
        ini_set('display_errors', '1');

        //set bahasa
        if ($_SESSION['language'] == "Indonesia") {
            $this->load->language('general', 'indonesia');
        } else {
            $this->load->language('general', 'english');
        }

        //get param
        $pSearch["prov"]    = $this->get('prov');
        $pSearch["kab"]     = $this->get('kab');

        //Get Data Farmer Group
        $dataList       = $this->mmill->getGridMainMillExcel($pSearch);
        
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
            $namaFile = date('YmdHis') . '_export_excel_mill.xlsx';
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

    public function export_supplier_get(){
        $MillID = $this->get("MillID");
        
        
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);     

        //Get Data Farmer Group
        $dataList       = $this->mmill->getGridMainMillSupplierExcel($MillID);
        
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
            $namaFile = date('YmdHis') . '_export_excel_mill.xlsx';
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
}
?>