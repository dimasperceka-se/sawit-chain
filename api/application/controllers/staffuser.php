<?php
/******************************************
 *  Author : n1colius.lau@gmail.com
 *  Created On : Mon Dec 02 2019
 *  File : staffuser.php
 *******************************************/
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

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

class Staffuser extends REST_Controller
{
    public function __construct()
    {
        $this->file = $_FILES;
        parent::__construct();
        $this->load->model('staff/mstaffuser');
        ini_set('display_errors',false); error_reporting(0); //Untuk didemo ntah kenapa selalu muncul notice2.
    }

    public function farmer_assignment_grid_get(){
        $StaffID = (int) $this->get('StaffID');
        $data = $this->mstaffuser->GetFarmerAssignmentGrid($StaffID, (int)$this->get('start'), (int)$this->get('limit'), 'limit');
        $this->response($data, 200);
    }

    public function farmer_assign_data_form_get(){
        $StaffAssignmentID = $this->get('StaffAssignmentID');
        $data = $this->mstaffuser->GetFarmerAssignForm($StaffAssignmentID);

        $this->response($data, 200);
    }

    public function farmer_assign_data_post(){
        //Prep Var (Begin)
        $varPost = $this->post();

        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Staffuser_WinFormFarmerAssignment-Form-", '', $key);
            if ($value == "") {
                $value = null;
            }

            $paramPost[$keyNew] = $value;
        }

        if ($paramPost['OpsiDisplay'] == "insert") {
            $Proses = $this->mstaffuser->InsertFarmerAssign($paramPost);
        }else{
            $Proses = $this->mstaffuser->UpdateFarmerAssign($paramPost);
        }
        $this->response($Proses, 200);
    }

    public function farmer_list_get(){
        $StaffID = (int) $this->get('StaffID');
        $StaffAssignmentID = (int) $this->get('StaffAssignmentID');
        

        //sort
        $sorting = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';
        
        $data = $this->mstaffuser->GetFarmerListGrid($StaffAssignmentID, $StaffID, (int)$this->get('start'), (int)$this->get('limit'), 'limit', $sortingField, $sortingDir);
        $this->response($data, 200);
    }

    public function members_get(){
        $StaffAssignmentID  = (int) $this->get('StaffAssignmentID');
        $StaffID            = (int) $this->get('StaffID');
        $textSearch         = $this->get('textSearch');
        $ProvinceID         = $this->get('ProvinceID');
        $DistrictID         = $this->get('DistrictID');
        $SubdistrictID      = $this->get('SubdistrictID');
        $VillageID          = $this->get('VillageID');

        //sort
        $sorting = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $result = $this->mstaffuser->getMemberAdd($StaffAssignmentID, $StaffID, $textSearch, $ProvinceID, $DistrictID, $SubdistrictID, $VillageID, $this->get('start'), $this->get('limit'), $sortingField, $sortingDir);
        $this->response($result, 200);
    }

    public function member_post(){
        $StaffAssignmentID = (int) $this->post('StaffAssignmentID');
        $StaffID = (int) $this->post('StaffID');
        $MemberID = json_decode($this->post('MemberID'));

        $result = $this->mstaffuser->insertMember($MemberID, $StaffAssignmentID, $StaffID);
        $this->response($result, 200);
    }

    public function import_farmer_assign_post(){

        if ($this->file['Koltiva_view_Staffuser_WinFormImportFarmer-Form-FileImport']['name'] != "") {
            //Cek extensi
            $arrTemp = explode(".", $this->file['Koltiva_view_Staffuser_WinFormImportFarmer-Form-FileImport']['name']);
            $extNya  = array_values(array_slice($arrTemp, -1))[0];
            $extNya  = strtolower($extNya);
            $StaffAssignmentID   = $this->post('StaffAssignmentID');
            $StaffID   = $this->post('StaffID');

            if ($extNya == 'xlsx') {
                $reader = ReaderEntityFactory::createXLSXReader();
                $reader->setShouldFormatDates(true);
                $reader->open($this->file['Koltiva_view_Staffuser_WinFormImportFarmer-Form-FileImport']['tmp_name']);

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
                            $MemberID = $this->mstaffuser->getMemberID($cells[0]);
                            $dataExcel[$increInt - 1] = $MemberID;
                        }
                        $increInt++;
                    }
                }
                $reader->close();

                $results = $this->mstaffuser->insertMember($dataExcel, $StaffAssignmentID, $StaffID);
            }else{
                $results['success'] = false;
                $results['message'] = lang("File Type Not Supported");
            }
        }else{
            $results['success'] = false;
            $results['message'] = lang("File Not Found");
        }

        $this->response($results, 200);
    }

    public function farmer_assign_data_get($StaffAssignmentID, $StaffID)
    {
        ini_set('memory_limit', '-1');

        //data yg diperlukan (begin)
        $dataMapping = $this->mstaffuser->GetFarmerListGrid($StaffAssignmentID, $StaffID, null, null, 'no_limit');
        //data yg diperlukan (end)

        require_once 'application/third_party/PHPExcel18/PHPExcel.php';
        require_once 'application/third_party/PHPExcel18/PHPExcel/IOFactory.php';

        //=============== MULAI TULIS EXCEL (BEGIN) ===================================================================//

        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("PT Koltiva")
            ->setLastModifiedBy("PT Koltiva")
            ->setTitle("Template Farmer Assignment Farmer")
            ->setSubject("Template Farmer Assignment Farmer")
            ->setDescription("Template Farmer Assignment Farmer")
            ->setKeywords("Template Farmer Assignment Farmer")
            ->setCategory("Template Farmer Assignment Farmer");

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

        //tabel header
        $objWorkSheetPetani->setCellValue('A1', 'Member Display ID');

        $objWorkSheetPetani->getStyle('A1')->applyFromArray($styleFont);
        $objWorkSheetPetani->getStyle('A1')->applyFromArray($styleBorderFull, false);

        $rowStart = 2;
        $incre    = 0;

        foreach ($dataMapping['data'] as $val) {
            $val['no'] = $incre + 1;

            $objWorkSheetPetani->setCellValue('A' . $rowStart, $val['MemberDisplayID']);

            $objWorkSheetPetani->getStyle('A' . $rowStart)->applyFromArray($styleFont);
            $objWorkSheetPetani->getStyle('A' . $rowStart)->applyFromArray($styleBorderFull, false);

            $rowStart++;
            $incre++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . date('YmdHis') . '_farmer_assignment_' . $StaffAssignmentID . '_' . $StaffID . '.xlsx');
        header('Cache-Control: max-age=0');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function supplier_delete(){
        $StaffAssignmentMemberID = $this->delete("StaffAssignmentMemberID");

        $this->db->where("StaffAssignmentMemberID", $StaffAssignmentMemberID);
        $query = $this->db->delete("ktv_staffs_assignment_member");
        if($query){
            $data["success"] = true;
            $data["message"] = "Data Deleted";
        }else{
            $data["success"] = false;
            $data["message"] = "Failed to Delete Data";
        }
        $this->response($data, 200);
    }

    public function combo_access_staff_get() {
        $data = $this->mstaffuser->GetComboAccessStaff();
        $this->response($data, 200);
    }

    public function cmb_dhis_role_get() {
        $data = $this->mstaffuser->GetComboDhisRole();
        $this->response($data, 200);
    }

    public function cmb_dhis_group_get() {
        $data = $this->mstaffuser->GetComboDhisGroup();
        $this->response($data, 200);
    }

    public function grid_main_get() {
        //sort
        $sorting      = json_decode($this->get('sort'));
        if (isset($sorting[0]->property)) $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        else $sortingField = null;
        if (isset($sorting[0]->direction)) $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';
        else $sortingDir = null;
        $start        = (int) $this->get('start');
        $limit        = (int) $this->get('limit');

        $pSearch = array();
        $pSearch['KeySearch'] = filter_var($this->get('KeySearch'), FILTER_SANITIZE_STRING);
        $pSearch['CmbSearchRole'] = filter_var($this->get('CmbSearchRole'), FILTER_SANITIZE_STRING);

        //echo '<pre>'; print_r($pSearch); exit;
        $data = $this->mstaffuser->GetGridMain($pSearch,$start,$limit,$sortingField,$sortingDir);
        $this->response($data, 200);
    }

    public function staff_photo_post() {
        $this->load->library('awsfileupload');
        //Cek file images
        $ExtNya = GetFileExt($_FILES['Koltiva_view_Staffuser_MainForm-StaffForm-PhotoInput']['name']);
        if (!in_array($ExtNya, array('png', 'jpg', 'jpeg', 'gif'))) {
            $result['success'] = false;
            $result['message'] = lang('File types not allowed');
            $this->response($result, 400);
        } else {

            if ($this->post('OpsiDisplay') == "insert") {
                if ($this->file['Koltiva_view_Staffuser_MainForm-StaffForm-PhotoInput']['name'] != '') {

                    $gambar = 'files/tmp/' . date('YmdHis').'-'.$this->file['Koltiva_view_Staffuser_MainForm-StaffForm-PhotoInput']['name'];
                    $fileupload['Koltiva_view_Staffuser_MainForm-StaffForm-PhotoInput'] = $this->file['Koltiva_view_Staffuser_MainForm-StaffForm-PhotoInput'];

                    $upload = move_upload($fileupload, $gambar);
                    if (isset($upload['upload_data'])) {
                        $result['success']    = true;
                        $result['FileImage'] = base_url().$gambar . '?' . rand(1, 100);
                        $result['PhotoInput'] = $gambar;
                        $this->response($result, 200);
                    } else {
                        $result['success'] = false;
                        $result['message'] = lang('Photo upload failed');
                        $this->response($result, 400);
                    }
                }
            }

            if ($this->post('OpsiDisplay') == "update") {
                if ($this->file['Koltiva_view_Staffuser_MainForm-StaffForm-PhotoInput']['name'] != '') {

                    $upload = $this->awsfileupload->upload($this->file['Koltiva_view_Staffuser_MainForm-StaffForm-PhotoInput']['tmp_name'],$this->file['Koltiva_view_Staffuser_MainForm-StaffForm-PhotoInput']['name'], AWSS3_STAFF_PHOTO, 'images');
                    if ($upload['success'] == true) {
                        if($this->awsfileupload->doesObjectExist($this->post('Koltiva_view_Staffuser_MainForm-StaffForm-PhotoInputData')) == true) {
                            $this->awsfileupload->delete($this->post('Koltiva_view_Staffuser_MainForm-StaffForm-PhotoInputData'));
                        }else{
                            delete_file($this->post('Koltiva_view_Staffuser_MainForm-StaffForm-PhotoInputData'));
                        }
                        $this->mstaffuser->UpdateStaffPhoto($upload['filenamepath'],$this->post('PersonID'));
                        $result['success'] = true;
                        $result['message'] = lang('File uploaded');
                        $result['FileImage'] = $upload['fileurl'];
                        $result['PhotoInput']   = $upload['filenamepath'];
                        $this->response($result, 200);
                    } else {
                        $result['success'] = false;
                        $result['message'] = lang('Upload to aws failed');
                        $this->response($result, 400);
                    }

                    //Untuk AWS S3, wajib ada
                    /*$this->load->library('awsfileupload');
                    $upload = $this->awsfileupload->upload($_FILES['Koltiva_view_Staffuser_MainForm-StaffForm-PhotoInput']['tmp_name'],$_FILES['Koltiva_view_Staffuser_MainForm-StaffForm-PhotoInput']['name'], AWSS3_STAFF_PHOTO_PATH, 'images');
                    if ($upload['success'] == true) {
                        $this->mstaffuser->UpdateStaffPhoto($upload['filenamepath'],$this->post('PersonID'));
                        $result['success']    = true;
                        $result['FileImage'] = $upload['fileurl'];
                        $result['PhotoInput'] = $upload['filenamepath'];
                        $this->response($result, 200);
                    } else {
                        $result['success'] = false;
                        $result['message'] = lang('Upload to aws failed');
                        $this->response($result, 400);
                    }*/
                }
            }

            $result['success'] = false;
            $result['message'] = lang('Photo upload not allowed');
            $this->response($result, 400);
        }
    }

    public function staff_form_post() {
        $this->load->model('staff/mstaffuser_cognito');
        $return = array();
        $varPost = $this->post();
        $paramPost = array();

        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Staffuser_MainForm-StaffForm-", '', $key);
            if ($value == "") {
                $value = null;
            }
            $paramPost[$keyNew] = $value;
        }
        //echo '<pre>'; print_r($paramPost); exit;

        $CekEmail = $this->mstaffuser->CekDuplikatEmail($paramPost['OfficialEmail'],$paramPost['OpsiDisplay'],$paramPost['PersonID']);
        if($CekEmail == true) {
            $return['success'] = false;
            $return['message'] = lang('Email address already registered');
            $this->response($return, 400);
        }

        $paramPost['OfficialCellPhoneWithCode'] = $paramPost['OfficialCellPhoneCode'].$paramPost['OfficialCellPhone'];
        $CekNoHp = $this->mstaffuser->CekDuplikatNoHp($paramPost['OfficialCellPhoneWithCode'],$paramPost['OpsiDisplay'],$paramPost['PersonID']);
        if($CekNoHp == true) {
            $return['success'] = false;
            $return['message'] = lang('Phone number already registered');
            $this->response($return, 400);
        }

        //Cek alamat email apakah valid
        if (!filter_var($paramPost['OfficialEmail'], FILTER_VALIDATE_EMAIL)) {
            $proses['success'] = false;
            $proses['message'] = lang('Invalid email format');
            $this->response($proses, 400);
        }

        if($paramPost['OpsiDisplay'] == 'insert') {
            $proses = $this->mstaffuser->insertStaff($paramPost);
            $paramPost['PersonID'] = $proses['PersonID'];
        } else {
            $proses = $this->mstaffuser_cognito->updateStaff($paramPost);
        }

        if($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function staff_data_delete() {
        $PersonID = (int) $this->delete('PersonID');

        //Cek apakah ada user account or active atau tidak
        $DataStaff = $this->mstaffuser->GetStaffDataForm($PersonID);
        if(isset($DataStaff['data']['UserID'])) {
            $result['success'] = false;
            $result['message'] = lang('Staff already has user account, cannot be deleted');
            $this->response($result, 400);
        }
        if($DataStaff['data']['StatusCode'] != "inactive") {
            $result['success'] = false;
            $result['message'] = lang('Staff must be set to inactive before delete');
            $this->response($result, 400);
        }

        $result = $this->mstaffuser->deleteStaff($PersonID);
        if($result['success']) {
            $this->response($result, 200);
        } else {
            $this->response($result, 400);
        }
    }

    public function staff_data_form_open_get() {
        $PersonID = (int) $this->get('PersonID');
        $data = $this->mstaffuser->GetStaffDataForm($PersonID);
        $this->response($data, 200);
    }

    public function account_form_open_get() {
        $PersonID = (int) $this->get('PersonID');
        $return = array();
        $DataStaff = $this->mstaffuser->GetStaffDataForm($PersonID);
        $DataReturn = array();

        $StateStaff = "";
        /*
        NOUSER = Belum ada di sys_user
        NOTLINKED = sudah ada di sys_user, tapi belum linked ke cognito
        LINKEDUNCONFIRMED = sudah ada di sys_user, sudah linked ke cognito tapi masih FORCE_CHANGED_PASS/UNCONFIRMED
        LINKEDCONFIRMED = sudah ada di sys_user, sudah linked ke cognito dan sudah CONFIRMED
        */

        if($DataStaff['data']['UserID'] == "") {
            //NOUSER
            $StateStaff = "NOUSER";
            $DataReturn['StateStaff'] = $StateStaff;

            $return['success'] = true;
            $return['data'] = $DataReturn;
            $this->response($return, 200);
        } else {
            //NOTLINKED | LINKEDUNCONFIRMED | LINKEDCONFIRMED
            $DataUser = $this->mstaffuser->GetDataUserAcc($DataStaff['data']['UserID']);
            if($DataUser['UserInCognito'] == "No") {
                $StateStaff = "NOTLINKED";
                $DataReturn['StateStaff'] = $StateStaff;
                $DataReturn['UserId'] = $DataStaff['data']['UserID'];

                $return['success'] = true;
                $return['data'] = $DataReturn;
                $this->response($return, 200);
            }

            if($DataUser['UserInCognito'] == "Yes") {
                $DataForm = $this->mstaffuser->GetUserAccountDataForm($PersonID);
                //echo '<pre>'; print_r($DataForm); exit;

                $return['success'] = true;
                $return['data'] = $DataForm;
                $this->response($return, 200);
            }
        }
    }

    public function user_dhis_form_open_get() {
        $PersonID = (int) $this->get('PersonID');
        $data = $this->mstaffuser->GetuserDhisFormOpen($PersonID);
        $this->response($data, 200);
    }

    public function check_aws_linked_post() {
        $this->load->model('staff/mstaffuser_cognito');
        $PersonID = (int) $this->post('PersonID');
        $UserId = (int) $this->post('UserId');

        $DataStaff = $this->mstaffuser->GetStaffDataForm($PersonID);
        $Email = $DataStaff['data']['Email'];

        //Cek alamat email apakah valid
        if (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
            $proses['success'] = false;
            $proses['message'] = lang('Invalid email format');
            $this->response($proses, 400);
        }

        //Cek apakah alamat email staff ini ada duplikat dengan staff lain
        $CekEmail = $this->mstaffuser->CekDuplikatEmail($Email,'update',$PersonID);
        if($CekEmail == true) {
            $return['success'] = false;
            $return['message'] = lang('Email address already registered on other staff, you must update the email address');
            $this->response($return, 400);
        }

        $CekNoHp = $this->mstaffuser->CekDuplikatNoHp($DataStaff['data']['PhonenumberWithCode'],'update',$PersonID);
        if($CekNoHp == true) {
            $return['success'] = false;
            $return['message'] = lang('Phone number already registered on other staff, you must update the phone number');
            $this->response($return, 400);
        }

        $proses = $this->mstaffuser_cognito->CheckLinkedCognitoOnly($PersonID,$Email);
        $this->response($proses, 200);
    }

    public function create_user_existing_form_open_get() {
        $PersonID = (int) $this->get('PersonID');
        $this->load->model('staff/mstaffuser_cognito');
        $DataStaff = $this->mstaffuser->GetStaffDataForm($PersonID);

        $data = $this->mstaffuser_cognito->GetCreateUserExistingForm($PersonID,$DataStaff);
        $this->response($data, 200);
    }

    public function user_account_create_post() {
        ini_set('display_errors',true); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        $this->load->model('staff/mstaffuser_cognito');
        $varPost = $this->post();
        $paramPost = array();
        $proses = array();

        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Staffuser_WinFormCreatedUser-Form-", '', $key);
            if ($value == "") {
                $value = null;
            }
            $paramPost[$keyNew] = $value;
        }
        //echo '<pre>'; print_r($paramPost); exit;
        $DataStaff = $this->mstaffuser->GetStaffDataForm($paramPost['PersonID']);

        //Cek validasi password (Begin)
        if($paramPost['Linked'] != "Yes") {
            $cekValidasiPassword = cekValidasiPassword($paramPost['UserPassword']);
            if($cekValidasiPassword['success'] == false) {
                $proses['success'] = false;
                $proses['message'] = $cekValidasiPassword['message'];
                $this->response($proses, 400);
            }
        }
        //Cek validasi password (End)

        //Cek usernamenya sudah ada atau belum
        $CekUsername = $this->mstaffuser->CekDuplikatUsername($paramPost['Username'],'insert',null);
        if($CekUsername == true) {
            $return['success'] = false;
            $return['message'] = lang('Username already registered to other staff');
            $this->response($return, 400);
        }

        //Cek username apakah alamat email
        if (filter_var($paramPost['Username'], FILTER_VALIDATE_EMAIL)) {
            $proses['success'] = false;
            $proses['message'] = lang('Username cannot be email format');
            $this->response($proses, 400);
        }

        if($paramPost['Linked'] != "Yes") {
            //insert ke aws cog dl, baru insert2 ke tabel mysql lainnya
            $proses = $this->mstaffuser_cognito->insertUserAcc($paramPost,$DataStaff['data']);
        } else {
            //Cek lagi disini, Phone number yg dibalikkan dari AWS apakah duplikat or tidak
            $CekNoHp = $this->mstaffuser->CekDuplikatNoHp($paramPost['Phonenumber'],'update',$paramPost['PersonID']);
            if($CekNoHp == true) {
                $return['success'] = false;
                $return['message'] = lang('Cellphone number from identity server already registered').' ('.$paramPost['Phonenumber'].')';
                $this->response($return, 400);
            }

            //langsung di insert kan aja data usernya
            $proses = $this->mstaffuser_cognito->insertUserAccLinked($paramPost,$DataStaff['data']);
        }
        if($proses['success']) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function linked_user_form_open_get() {
        $PersonID = (int) $this->get('PersonID');
        $UserId = (int) $this->get('UserId');

        $data = $this->mstaffuser->GetLinkedUserForm($PersonID);
        $this->response($data, 200);
    }

    public function linked_user_cognito_form_post() {
        $this->load->model('staff/mstaffuser_cognito');
        $return = array();
        $post = $this->post();
        $paramPost = array();

        foreach ($post as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Staffuser_WinFormLinkedUser-FormCreate-", '', $key);
            if ($value == "") {
                $value = null;
            }
            $paramPost[$keyNew] = $value;
        }
        //echo '<pre>'; print_r($paramPost); exit;


        //cek duplikat email
        $CekEmail = $this->mstaffuser->CekDuplikatEmail($paramPost['Email'],'update',$paramPost['PersonID']);
        if($CekEmail == true) {
            $return['success'] = false;
            $return['message'] = lang('Email address already registered to other staff');
            $this->response($return, 400);
        }

        $CekNoHp = $this->mstaffuser->CekDuplikatNoHp($paramPost['Phonenumber'],'update',$paramPost['PersonID']);
        if($CekNoHp == true) {
            $return['success'] = false;
            $return['message'] = lang('Phone number already registered to other staff');
            $this->response($return, 400);
        }

        //Cek alamat email apakah valid
        if (!filter_var($paramPost['Email'], FILTER_VALIDATE_EMAIL)) {
            $proses['success'] = false;
            $proses['message'] = lang('Invalid email format');
            $this->response($proses, 400);
        }

        //Cek username apakah alamat email
        if (filter_var($paramPost['Username'], FILTER_VALIDATE_EMAIL)) {
            $proses['success'] = false;
            $proses['message'] = lang('Username cannot be email format');
            $this->response($proses, 400);
        }

        //cek duplikat username
        $CekUsername = $this->mstaffuser->CekDuplikatUsername($paramPost['Username'],'update',$paramPost['UserId']);
        if($CekUsername == true) {
            $return['success'] = false;
            $return['message'] = lang('Username already registered to other staff');
            $this->response($return, 400);
        }

        //cek password
        $cekValidasiPassword = cekValidasiPassword($paramPost['UserPassword']);
        if($cekValidasiPassword['success'] == false) {
            $return['success'] = false;
            $return['message'] = $cekValidasiPassword['message'];
            $this->response($return, 400);
        }

        $proses = $this->mstaffuser_cognito->UpdateUserAcc($paramPost);
        if($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function linked_user_existing_form_open_get() {
        $PersonID = (int) $this->get('PersonID');
        $UserId = (int) $this->get('UserId');
        $this->load->model('staff/mstaffuser_cognito');
        $DataStaff = $this->mstaffuser->GetStaffDataForm($PersonID);

        $data = $this->mstaffuser_cognito->GetLinkedUserExistingForm($PersonID,$UserId,$DataStaff);
        $this->response($data, 200);
    }

    public function linked_user_existing_cognito_form_post() {
        $post = $this->post();
        $paramPost = array();
        $this->load->model('staff/mstaffuser_cognito');
        //ini_set('display_errors',true); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

        foreach ($post as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Staffuser_WinFormLinkedUserExisting-Form-", '', $key);
            if ($value == "") {
                $value = null;
            }
            $paramPost[$keyNew] = $value;
        }
        //echo '<pre>'; print_r($paramPost); exit;

        //DataStaff
        $DataStaff = $this->mstaffuser->GetStaffDataForm($paramPost['PersonID']);

        //cek duplikat email
        $CekEmail = $this->mstaffuser->CekDuplikatEmail($paramPost['Email'],'update',$paramPost['PersonID']);
        if($CekEmail == true) {
            $return['success'] = false;
            $return['message'] = lang('Email address already registered to other staff');
            $this->response($return, 400);
        }

        $CekNoHp = $this->mstaffuser->CekDuplikatNoHp($paramPost['Phonenumber'],'update',$paramPost['PersonID']);
        if($CekNoHp == true) {
            $return['success'] = false;
            $return['message'] = lang('Phone number already registered to other staff');
            $this->response($return, 400);
        }

        //cek duplikat username
        $CekUsername = $this->mstaffuser->CekDuplikatUsername($paramPost['Username'],'update',$paramPost['UserId']);
        if($CekUsername == true) {
            $return['success'] = false;
            $return['message'] = lang('Username already registered to other staff');
            $this->response($return, 400);
        }

        $proses = $this->mstaffuser_cognito->UpdateUserAccExisting($paramPost,$DataStaff['data']);
        if($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function resend_confirmation_email_post() {
        $this->load->model('staff/mstaffuser_cognito');
        $post = $this->post();
        $PersonID = $post['PersonID'];
        $UserPassword = $post['UserPassword'];
        $DataStaff = $this->mstaffuser->GetStaffDataForm($PersonID);

        //Cek validasi password (Begin)
        $cekValidasiPassword = cekValidasiPassword($UserPassword);
        if($cekValidasiPassword['success'] == false) {
            $proses['success'] = false;
            $proses['message'] = $cekValidasiPassword['message'];
            $this->response($proses, 400);
        }
        //Cek validasi password (End)

        $proses = $this->mstaffuser_cognito->ResendConfirmationEmail($DataStaff['data'],$UserPassword);
        if($proses['success']) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function user_account_post() {
        $this->load->model('staff/mstaffuser_cognito');
        $varPost = $this->post();
        $paramPost = array();
        $proses = array();

        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Staffuser_PanelUserMgt-Form-", '', $key);
            if ($value == "") {
                $value = null;
            }
            $paramPost[$keyNew] = $value;
        }
        //echo '<pre>'; print_r($paramPost); exit;

        $DataStaff = $this->mstaffuser->GetStaffDataForm($paramPost['PersonID']);
        $proses = $this->mstaffuser_cognito->updateUserAccount($paramPost,$DataStaff['data']);
        if($proses['success']) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function user_dhis_form_post() {
        $varPost = $this->post();
        $paramPost = array();
        $proses = array();

        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Staffuser_PanelUserDhis-Form-", '', $key);
            if ($value == "") {
                $value = null;
            }
            $paramPost[$keyNew] = $value;
        }
        //echo '<pre>'; print_r($paramPost); exit;

        $proses = $this->mstaffuser->UpdateUserDhisForm($paramPost);
        if($proses['success']) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function user_account_change_passwd_post() {
        $this->load->model('staff/mstaffuser_cognito');
        $varPost = $this->post();
        $paramPost = array();

        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Staffuser_WinFormChangePassword-Form-", '', $key);
            if ($value == "") {
                $value = null;
            }
            $paramPost[$keyNew] = $value;
        }
        //echo '<pre>'; print_r($paramPost); exit;

        //Cek validasi password (Begin)
        $cekValidasiPassword = cekValidasiPassword($paramPost['UserPassword']);
        if($cekValidasiPassword['success'] == false) {
            $proses['success'] = false;
            $proses['message'] = $cekValidasiPassword['message'];
            $this->response($proses, 400);
        }
        //Cek validasi password (End)

        $dataUser = $this->mstaffuser->GetUserAccountDataForm($paramPost['PersonID']);
        $proses = $this->mstaffuser_cognito->changePasswordUserAccount($paramPost,$dataUser);
        if($proses['success']) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function changePasswordUserAccount($paramPost,$dataUser) {
        $result = array();

        $response = $this->clientCog->adminSetUserPassword($dataUser['Username'],$paramPost['UserPassword']);
        if(isset($response['@metadata']['statusCode'])) {
            if($response['@metadata']['statusCode'] == '200') {
                $result['success'] = true;
                $result['message'] = lang("Password changed");
            } else {
                $result['success'] = false;
                $result['message'] = lang("Failed to change password");
            }
        } else {
            $result['success'] = false;
            $result['message'] = lang("Failed to change password").", ".decodeMsgAws($response);
        }

        return $result;
    }

    public function grid_log_user_login_get() {
        $PersonID = (int) $this->get('PersonID');

        $data = $this->mstaffuser->GetGridLogUserLogin($PersonID);
        $this->response($data, 200);
    }

}