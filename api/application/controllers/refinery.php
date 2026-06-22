<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Refinery extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->file = $_FILES;
        $this->load->model('refinery/mrefinery');
        $this->load->library('awsfileupload');
    }

    public function get_refinery_transaction_get(){
        $data = $this->mrefinery->getSupTransaction($this->get('PID'),$this->get('start'),$this->get('end'));
        $this->response($data, 200);
    }

    public function get_refinery_dashboard_get(){
        list($supplier,$total_ttp) = $this->mrefinery->get_category_traceability($this->get('PID'));
        $data["farmer"]     = $this->mrefinery->get_farmer_refinery($this->get('PID'));
        $data["supplier"]   = $supplier;
        $data["supplier_detail"]   = $this->mrefinery->get_supplier_detail($this->get('PID'));
        $this->response($data, 200);
    }

    public function get_refinery_profile_get(){
        list($supplier,$total_ttp) = $this->mrefinery->get_category_traceability($this->get('PID'));
        $data["supplier"]   = $supplier;
        $data["total_ttp"]  = $total_ttp;
        $data["mapped"]     = $this->mrefinery->get_mapped_farmer($this->get('PID'));
        $data["pemasok"]    = $this->mrefinery->get_pemasok($this->get('PID'));
        $data["basic"]      = $this->mrefinery->get_basic_data_refinery($this->get('PID'));
        $this->response($data, 200);
    }

    public function refinery_summary_get(){
        $PartnerID     = $this->get("PartnerID");
        $Year       = $this->get("Year");
        $Period     = $this->get("Period");
        $RefineryTCDID  = $this->get("RefineryTCDID");

        $dataRefinery       = $this->mrefinery->getRefineryBasicDataFormNew($PartnerID);
        $dataTracable   = $this->mrefinery->getTracablePrint($PartnerID,$Year,$RefineryTCDID,$Period);

        $data["EstateInti"] = $this->mrefinery->getGridTracebilityDeclaration(
            $this->get('PartnerID')
            , $this->get('Year')
            , $this->get('RefineryTCDID')
            , $this->get('Period')
            , 2
        );

        $data["Plasma"] = $this->mrefinery->getGridTracebilityDeclaration(
            $this->get('PartnerID')
            , $this->get('Year')
            , $this->get('RefineryTCDID')
            , $this->get('Period')
            , 1
        );

        $data["Other"] = $this->mrefinery->getGridTracebilityDeclaration(
            $this->get('PartnerID')
            , $this->get('Year')
            , $this->get('RefineryTCDID')
            , $this->get('Period')
            , 4
        );

        $data["External"] = $this->mrefinery->getGridTracebilityDeclaration(
            $this->get('PartnerID')
            , $this->get('Year')
            , $this->get('RefineryTCDID')
            , $this->get('Period')
            , 3
        );

        if($dataRefinery["success"]){
            foreach($dataRefinery["data"] as $key => $value){
                $keyNew = str_replace("Koltiva.view.Refinery.FormMainRefinery-FormBasicData-","",$key);
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
        
        if($RefineryTCDID == ""){
            $this->load->view("refinery/summary_report",$data);
        }else{
            $this->load->view("refinery/summary_report_manual",$data);
        }
    }

    public function get_grid_report_locked_get(){
        $data = $this->mrefinery->getGridReportLocked($this->get("RefineryID"),$this->get("Year"),$this->get('start'),$this->get('limit'));

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

        $data = $this->mrefinery->getGridMainRefinery($pSearch,$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        $this->response($data, 200);
    }

    public function get_supplier_list_get(){
        $RefineryTCDID  = (int) $this->get('RefineryTCDID');
        $data       = $this->mrefinery->getSupplierList($RefineryTCDID); 
        return $this->response($data, 200);
    }

    public function submit_tc_declaration_post(){
        $submit = $this->mrefinery->submit_tc_declaration($_POST);
        $this->response($submit,200);
    }

    public function submit_tc_declaration_new_post(){
        $submit = $this->mrefinery->submit_tc_declaration_new($_POST);
        $this->response($submit,200);
    }

    public function form_tc_declaration_new_get(){
        $RefineryTCID  = $_GET["RefineryTCID"];

        $data       = $this->mrefinery->form_tc_declaration_new($RefineryTCID);
        $this->response($data,200);
    }

    public function form_tc_declaration_get(){
        $RefineryID     = $_GET["RefineryID"];
        $RefineryTCDID  = $_GET["RefineryTCDID"];

        $data       = $this->mrefinery->form_tc_declaration($RefineryID,$RefineryTCDID);
        $this->response($data,200);
    }

    public function form_tc_delete(){
        $RefineryTCID = $this->delete("RefineryTCID");
        $this->db->where("RefineryTCID",$RefineryTCID);
        $this->db->delete("ktv_refinery_tc");

        $this->response(true,200);
    }

    public function form_tc_declaration_delete(){
        $RefineryTCDID  = $this->delete("RefineryTCDID");

        $data = array(
            "StatusCode" => 'nullified'
        );
        $this->db->where("RefineryTCDID",$RefineryTCDID);
        $this->db->update("ktv_refinery_tc_declaration",$data);
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
            'RefineryID'     => $this->get('RefineryID')
        );

        $data = $this->mrefinery->getGridMainRefineryTCDeclarationManual($pSearch,$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        $this->response($data, 200);
    }

    public function setPartnerRefinery_post(){
        $varPost = $this->post();

        foreach ($varPost as $key => $value) {
            if ($value == "") {
                $value = null;
            }
            $key = str_replace("Koltiva_view_Refinery_FormMainRefinery-FormBasicData-","",$key);          
            $paramPost[$key] = $value;
        }

        $data = $this->mrefinery->setPartnerRefinery($paramPost);
        $this->response($data, 200);
    }

    public function refinery_as_partner_put(){
        $varPost = $this->put();

        foreach ($varPost as $key => $value) {
            if ($value == "") {
                $value = null;
            }         
            $paramPost[$key] = $value;
        }
        $data = $this->mrefinery->refinery_as_partner($paramPost);
        $this->response($data, 200);
    }

    public function refinery_tracebilityDeclaration_get(){
        $data = $this->mrefinery->getRefinerytracebilityDeclaration(
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

        if($Period == "full"){
            $month = "-12-31";
        }else{
            $month = "-06-30";
        }

        $startDate  = $Year."-01-01 00:00:00";
        $endDate    = $Year.$month." 23:59:59";

        $sql = "
            UPDATE
                ktv_refinery_tc tc
            LEFT JOIN
                ktv_refinery m on m.RefineryID = tc.RefineryID
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
        $data = $this->mrefinery->getGridTracebilityDeclaration(
            $this->get('PartnerID')
            , $this->get('Year')
            , $this->get('RefineryTCDID')
            , $this->get('Period')
            , 2
        );
        $this->response($data, 200);
    }

    public function grid_plasma_get(){
        $data = $this->mrefinery->getGridTracebilityDeclaration(
            $this->get('PartnerID')
            , $this->get('Year')
            , $this->get('RefineryTCDID')
            , $this->get('Period')
            , 1
        );
        $this->response($data, 200);
    }

    public function grid_external_get(){
        $data = $this->mrefinery->getGridTracebilityDeclaration(
            $this->get('PartnerID')
            , $this->get('Year')
            , $this->get('RefineryTCDID')
            , $this->get('Period')
            , 3
        );
        $this->response($data, 200);
    }

    public function grid_other_get(){
        $data = $this->mrefinery->getGridTracebilityDeclaration(
            $this->get('PartnerID')
            , $this->get('Year')
            , $this->get('RefineryTCDID')
            , $this->get('Period')
            , 4
        );
        $this->response($data, 200);
    }

    public function refinery_basic_data_form_profile_get(){
        $data = $this->mrefinery->getRefineryBasicDataFormProfile($this->get('PartnerID'));
        $this->response($data, 200);
    }

    public function refinery_basic_data_form_get(){
        $data = $this->mrefinery->getRefineryBasicDataForm($this->get('RefineryID'));
        $this->response($data, 200);
    }
    public function staff_assignment_get() {
        $data = $this->mrefinery->getStaffFAAssignment($this->get('RefineryID'));
        $this->response($data, 200);
    }
    public function list_staff_assignment_get() {
        $data = $this->mrefinery->getListStaffFAAssignment($this->get('RefineryID'));
        $this->response($data, 200);
    }
    public function setFaAssignment_post() {
        $data = $this->mrefinery->setStaffFAAssignment($this->post('RefineryID'), $this->post('itemselector-fa_assignment'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response($data, 404);
    }

    public function image_refinery_post(){
        if($this->post('opsiDisplay') == "insert"){
            //ketika insert
            if ($this->file['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-MemberPhotoInput']['name'] != '') {
                $gambar = 'temp/'.$this->file['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-MemberPhotoInput']['name'];
                $fileupload['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-MemberPhotoInput'] = $this->file['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-MemberPhotoInput'];
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
            $upload = $this->awsfileupload->upload($this->file['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-MemberPhotoInput']['tmp_name'],$this->file['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-MemberPhotoInput']['name'], AWSS3_REFINERY_LOGO_PATH, 'images');
            if ($upload['success'] == true) {
                if($this->awsfileupload->doesObjectExist($this->post('Koltiva_view_Refinery_FormMainRefinery-FormBasicData-PhotoOld')) == true) {
                    $this->awsfileupload->delete($this->post('Koltiva_view_Refinery_FormMainRefinery-FormBasicData-PhotoOld'));
                }else{
                    delete_file($this->post('Koltiva_view_Refinery_FormMainRefinery-FormBasicData-PhotoOld'));
                }
                $prosesUpdate = $this->mrefinery->UpdateImageRefinery($_POST["RefineryID"],$upload['filenamepath'],'Logo');
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

    public function image_refinery_location_post(){
        if($this->post('opsiDisplay') == "insert"){
            //ketika insert
            if ($this->file['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-LocationPhotoInput']['name'] != '') {
                $gambar = 'temp/'.$this->file['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-LocationPhotoInput']['name'];
                $fileupload['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-LocationPhotoInput'] = $this->file['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-LocationPhotoInput'];
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
            $upload = $this->awsfileupload->upload($this->file['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-LocationPhotoInput']['tmp_name'],$this->file['Koltiva_view_Refinery_FormMainRefinery-FormBasicData-LocationPhotoInput']['name'], AWSS3_REFINERY_LOCATION_PATH, 'images');
            if ($upload['success'] == true) {
                if($this->awsfileupload->doesObjectExist($this->post('Koltiva_view_Refinery_FormMainRefinery-FormBasicData-LocationPhotoOld')) == true) {
                    $this->awsfileupload->delete($this->post('Koltiva_view_Refinery_FormMainRefinery-FormBasicData-LocationPhotoOld'));
                }else{
                    delete_file($this->post('Koltiva_view_Refinery_FormMainRefinery-FormBasicData-LocationPhotoOld'));
                }
                $prosesUpdate = $this->mrefinery->UpdateImageRefinery($_POST["RefineryID"],$upload['filenamepath'],'Location');
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

        $result = $this->mrefinery->getSourceName($PartnerID,$SourceType,$this->get("KategoriKebun"));

        $this->response($result,200);
    }

    public function traceabiliy_generate_get(){
        $RefineryID = $this->get("RefineryID");

        $result = $this->mrefinery->generateDeclaration($RefineryID);

        $this->response($result,200);
    }

    public function refinery_form_post(){
        if($this->post('Koltiva_view_Refinery_FormMainRefinery-FormBasicData-RefineryID') == ""){
            //insert
            $proses = $this->mrefinery->insertRefinery($this->post());
        }else{
            //update
            $proses = $this->mrefinery->updateRefinery($this->post());
        }
        $this->response($proses, 200);
    }

    public function refinery_profile_form_post(){
        
        if($this->post('Koltiva_view_Refinery_FormMainRefineryProfile-FormBasicData-RefineryID') != ""){
            
            $proses = $this->mrefinery->updateRefineryProfile($this->post());
        }
        $this->response($proses, 200);
    }

    public function refinery_form_delete(){
        $RefineryID = (int) $this->delete('RefineryID');
        $proses = $this->mrefinery->deleteRefinery($RefineryID);
        $this->response($proses, 200);
    }

    public function grid_refinery_staff_get(){
        $data = $this->mrefinery->getGridRefineryStaff($this->get('RefineryID'));
        $this->response($data, 200);
    }

    public function cetak_refinery_profiles_get(){
        //set bahasa
        if ($_SESSION['language'] == "Indonesia") {
            $this->load->language('general', 'indonesia');
        } else {
            $this->load->language('general', 'english');
        }

        //get param
        $RefineryID = $this->get('RefineryID');
        $RefineryIDs = explode('::', $RefineryID);

        $dataHeader['titleNya'] = "Refinery Profile";
        $this->load->view('profiles/cetak_refinery_profiles_header', $dataHeader);

        if (strpos($RefineryID, '::')) {
            $countData = count($RefineryIDs);
            $increData = 1;
            foreach ($RefineryIDs as $key => $RefineryID) {
                $this->cetak_refinery_profiles($RefineryID, $countData, $increData);
                $increData++;
            }
        } else {
            $this->cetak_refinery_profiles($RefineryID);
        }

        $this->load->view('profiles/cetak_refinery_profiles_footer');
    }

    private function cetak_refinery_profiles($RefineryID, $countData = 1, $increData = 0){
        $data = array(); 

        //get data
        $data['refinery'] = $this->mrefinery->getRefineryDetailPrint($RefineryID);
        $data['NrOfStaff'] = $this->mrefinery->getRefineryNrOfStaff($RefineryID);

        //get data training
        $data['training'] = $this->mrefinery->getTrainingDataRefinery($RefineryID);
        $data['tc'] = $this->mrefinery->getTraceabilityDataRefinery($RefineryID);

        //logo
        $this->load->model('project_process/mproject_process');
        $DistrictID = substr($data['refinery']['VillageID'], 0,4);
        $data['logos'] = $this->mproject_process->getPrintLogoHeader($DistrictID);
        $data['ffb'] = $this->mrefinery->getFFBSales($RefineryID);
        $data['traceability_details'] = $this->mrefinery->getTraceabilityDetails($RefineryID);
        //echo '<pre>'; print_r($data); exit;
        $this->load->view('profiles/cetak_refinery_profiles', $data);
    }

    public function combo_refinery_group_get(){
        $data = $this->mrefinery->getRefineryGroups();
        $this->response($data, 200);
    }

    public function submit_sp_code_delete(){
        $SPCodeID   = $this->delete('SPCodeID');

        $this->db->where("SPCodeID",$SPCodeID);
        $result = $this->db->delete("ktv_refinery_sp_code");

        $this->response($result,200);
    }

    public function submit_sp_code_post(){

        $data = array(
            "RefineryID" => $_POST["RefineryID"],
            "SuratNr" => $_POST["SuratNr"],
            "Note" => $_POST["Note"],
        );

        if(isset($_POST["SPCodeID"]) AND $_POST["SPCodeID"] != ""){
            $data["UpdatedBy"] = $_SESSION["userid"];
            $data["DateUpdated"] = date("Y-m-d H:i:s");
            $this->db->where("SPCodeID",$_POST["SPCodeID"]);
            $post = $this->db->update("ktv_refinery_sp_code",$data);
        }else{
            $data["DateCreated"] = date("Y-m-d H:i:s");
            $data["CreatedBy"] = $_SESSION["userid"];
            $post = $this->db->insert("ktv_refinery_sp_code",$data);
        }

        $this->response($post,200);
    }

    public function grid_sp_code_get(){
        $data = $this->mrefinery->getSPCode($this->get('RefineryID'));
        $this->response($data, 200);
    }

    public function sp_code_form_get(){
        $SPCodeID   = $this->get('SPCodeID');
        $RefineryID = $this->get('RefineryID');

        $sql = "
            SELECT
                a.SPCodeID
                , a.RefineryID
                , a.SuratNr
                , a.Note
            FROM 
                `ktv_refinery_sp_code` a
            WHERE
                a.RefineryID = ?
            AND a.SPCodeID = ?
        ";

        $query = $this->db->query($sql,array($RefineryID,$SPCodeID));
        $data = array();
        if($query->num_rows()>0){
            $row = $query->row();
            $data["Koltiva.view.Refinery.WinFormSPCode-Form-SPCodeID"] = $row->SPCodeID;
            $data["Koltiva.view.Refinery.WinFormSPCode-Form-RefineryID"] = $row->RefineryID;
            $data["Koltiva.view.Refinery.WinFormSPCode-Form-SuratNr"] = $row->SuratNr;
            $data["Koltiva.view.Refinery.WinFormSPCode-Form-Note"] = $row->Note;
        }
        $result['success'] = true;
        $result['data'] = $data;

        $this->response($result,200);
    }
}
?>