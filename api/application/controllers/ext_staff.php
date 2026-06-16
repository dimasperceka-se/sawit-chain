<?php
/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Tue Jan 15 2019
 *  File : ext_staff.php
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

//ini_set('display_errors',true); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
class Ext_staff extends REST_Controller
{

    public function __construct()
    {
        $this->file = $_FILES;
        parent::__construct();
        $this->load->model('ext_staff/mext_staff');
    }

    public function grid_main_ext_staff_get()
    {
        //sort
        $sorting      = json_decode($this->get('sort'));
        if(isset($sorting)){
        	$sortingField = $sorting[0]->property;
        	$sortingDir   = $sorting[0]->direction;
        }else{
        	$sortingField = null;
        	$sortingDir   = null;
        }
        $start        = (int) $this->get('start');
        $limit        = (int) $this->get('limit');

        //get param
        $pSearch = array(
            'country'                    => $this->get('country'),
            'prov'                    => $this->get('prov'),
            'opsiCall'                => $this->get('opsiCall'),
            'textSearch'              => $this->get('KeySearch'),
            'callFrom'              => 'agro'
            // 'callFrom'              => $this->get('callForm')
        );

        $data = $this->mext_staff->GetGridMainExtStaff($pSearch, $start, $limit, $sortingField, $sortingDir, 'grid');
        $this->response($data, 200);
    }

    public function photo_staff_post(){
        $return['success'] = false;
        $return['message'] = lang('Upload Failed');

        switch($this->post('OpsiDisplay')){
            case 'insert':
                if($this->file['Koltiva_view_Ext_staff_MainForm-FormBasicData-PhotoInput']['name'] != '') {
                    //Ambil Ext
                    $arrTemp    = explode(".", $this->file['Koltiva_view_Ext_staff_MainForm-FormBasicData-PhotoInput']['name']);
                    $tempExtNya = strtolower(array_values(array_slice($arrTemp, -1))[0]);
                    $arrTempExt = explode("?", $tempExtNya);
                    $extNya     = $arrTempExt[0];

                    $gambar = "staff_".date('YmdHis').".".$extNya;
                    $fileupload['Koltiva_view_Ext_staff_MainForm-FormBasicData-PhotoInput'] = $this->file['Koltiva_view_Ext_staff_MainForm-FormBasicData-PhotoInput'];
                    $upload = move_upload($fileupload, 'images/staff/temp/' . $gambar);
                    if (isset($upload['upload_data'])) {
                        $return['success'] = true;
                        $return['file'] = $gambar;
                        $return['filepath'] = 'images/staff/temp/'.$gambar;
                        $return['message'] = lang('Success');
                        $this->response($return, 200);
                    } else {
                        $this->response($return, 400);
                    }
                }
            break;
            case 'update':
                if($this->file['Koltiva_view_Ext_staff_MainForm-FormBasicData-PhotoInput']['name'] != '') {
                    $this->load->library('awsfileupload');
                    $PersonID = $this->post('Koltiva_view_Ext_staff_MainForm-FormBasicData-PersonID');
                    
                    $path = AWSS3_STAFF_PHOTO_PATH ;
                    $upload = $this->awsfileupload->upload($_FILES['Koltiva_view_Ext_staff_MainForm-FormBasicData-PhotoInput']['tmp_name'], $_FILES['Koltiva_view_Ext_staff_MainForm-FormBasicData-PhotoInput']['name'], $path, 'images');
                    if ($upload['success'] == true) {
                        $this->mext_staff->UpdatePhotoExtStaff($upload['filenamepath'], $PersonID);
                        $return['success'] = true;
                        $return['file'] = $upload['fileurl'] . '?' . rand(1, 100);
                        $return['filepath'] = $upload['fileurl'];
                        $return['message'] = lang('Success');
                        $this->response($return, 200);
                    }
                }
            break;
            default:
                $this->response($return, 400);
            break;
        }
    }

    public function ext_staff_form_post(){
        //Prep Var (Begin)
        $varPost = $this->post();

        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Ext_staff_MainForm-FormBasicData-", '', $key);
            if ($value == "") {
                $value = null;
            }

            $paramPost[$keyNew] = $value;
        }
        //Prep Var (End)

        if ($paramPost['OpsiDisplay'] == "insert") {
            $Proses = $this->mext_staff->InsertExtStaff($paramPost);
            // $newData = false;
            // $push = pushWeb($uid, $Proses['PersonID'], $newData);
        }else{
            $Proses = $this->mext_staff->UpdateExtStaff($paramPost);
            $newData = true; //
        }

        /* PUSH */
        //require_once(APPPATH.'controllers/scheduler.php'); 
        //$push =  new Scheduler();
        $uid = $paramPost['CallFrom'] == 'agro' ? 'vFurzwwsv0Z' : 'fzdUYzegdjP';
        //$table_relation = $paramPost['CallFrom'] == 'agro' ? 'view_program_agronomist' : 'view_program_technician';

        //$sentPush = $push->push_dhis_get($uid, $Proses['PersonID'], $newData);


        if($Proses['success'] == true){
            $this->response($Proses, 200);
        }else{
            $this->response($Proses, 400);
        }
    }

    public function staff_additional_post(){
        $varPost = $this->post();

        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Ext_staff_PanelFormAdditional-Form-", '', $key);
            if ($value == "") {
                $value = null;
            }

            $paramPost[$keyNew] = $value;
        }

        $Proses = $this->mext_staff->UpdateExtStaffAdditional($paramPost);

        if($Proses['success'] == true){
            $this->response($Proses, 200);
        }else{
            $this->response($Proses, 400);
        }
    }

    public function ext_staff_form_delete(){
        $PersonID = (int) $this->delete('PersonID');
        $StaffID = (int) $this->delete('StaffID');
        $proses = $this->mext_staff->DeleteExtStaff($PersonID,$StaffID);
        if($proses['success'] == true){
            $this->response($proses, 200);
        }else{
            $this->response($proses, 400);
        }
    }

    public function ext_staff_form_open_get(){
        $PersonID = (int) $this->get('PersonID');
        $StaffID = (int) $this->get('StaffID');
        $data = $this->mext_staff->GetExtStaffFormOpen($PersonID,$StaffID);
        $this->response($data, 200);
    }

    public function ext_staff_form_additional_open_get(){
        $PersonID = (int) $this->get('PersonID');
        $StaffID = (int) $this->get('StaffID');
        $data = $this->mext_staff->GetExtStaffFormAdditionalOpen($PersonID,$StaffID);
        $this->response($data, 200);
    }

    public function user_account_post(){
        //Prep Var (Begin)
        $varPost = $this->post();


        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_ExtStaff_WinFormUserManagement-Form-", '', $key);
            if ($value == "") {
                $value = null;
            }

            $paramPost[$keyNew] = $value;
        }

        //Prep Var (End)
        //Cek password match
        if($paramPost['UserPassword'] != $paramPost['UserPasswordRe']){
            $Proses['success'] = false;
            $Proses['message'] = lang('Password Confirmation does not match');
            $this->response($Proses, 400);
        }

        if ($paramPost['UserId'] == "") {

            //Cek Password ada isinya tidak
            if($paramPost['UserPassword'] == ""){
                $Proses['success'] = false;
                $Proses['message'] = lang('Password is required');
                $this->response($Proses, 400);
            }

            $Proses = $this->mext_staff->InsertExtStaffUserAcc($paramPost);
        }else{
            $Proses = $this->mext_staff->UpdateExtStaffUserAcc($paramPost);
        }

        // Push ke DHIS 
        $person = $this->mext_staff->cekPerson($paramPost['PersonID']); 
        if($person->num_rows()){
            $data = $person->row();
            $uid = (int)$data->PosID == 1 ? 'vFurzwwsv0Z' : 'fzdUYzegdjP';
            $push = pushWeb($uid, $paramPost['PersonID'], true);
        }
        //===========================================================================//

        if($Proses['success'] == true){
            $this->response($Proses, 200);
        }else{
            $this->response($Proses, 400);
        }
    }

    public function farmer_assign_data_form_get(){
        $StaffAssignmentID = $this->get('StaffAssignmentID');
        $data = $this->mext_staff->GetFarmerAssignForm($StaffAssignmentID);

        $this->response($data, 200);
    }

    public function farmer_assign_data_post(){
        //Prep Var (Begin)
        $varPost = $this->post();


        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Ext_staff_WinFormFarmerAssignment-Form-", '', $key);
            if ($value == "") {
                $value = null;
            }

            $paramPost[$keyNew] = $value;
        }

        if ($paramPost['OpsiDisplay'] == "insert") {
            $Proses = $this->mext_staff->InsertFarmerAssign($paramPost);
        }else{
            $Proses = $this->mext_staff->UpdateFarmerAssign($paramPost);
        }
        $this->response($Proses, 200);
    }

    public function grid_user_acc_login_info_get(){
        $StaffID = (int) $this->get('StaffID');
        $PersonID = (int) $this->get('PersonID');
        $data = $this->mext_staff->GetUserAccLoginGrid($PersonID,$StaffID);
        $this->response($data, 200);
    }

    public function farmer_assignment_grid_get(){
        $StaffID = (int) $this->get('StaffID');
        $data = $this->mext_staff->GetFarmerAssignmentGrid($StaffID, (int)$this->get('start'), (int)$this->get('limit'), 'limit');
        $this->response($data, 200);
    }

    public function farmer_list_get(){
        $StaffID = (int) $this->get('StaffID');
        $StaffAssignmentID = (int) $this->get('StaffAssignmentID');
        $textSearch = $this->get('textSearch');
        

        //sort
        $sorting = json_decode($this->get('sort'));
        $sortingField = $sorting[0]->property;
        $sortingDir = $sorting[0]->direction;
        
        $data = $this->mext_staff->GetFarmerListGrid($StaffAssignmentID, $StaffID, $textSearch, (int)$this->get('start'), (int)$this->get('limit'), 'limit', $sortingField, $sortingDir);
        $this->response($data, 200);
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

    public function suppliers_get(){
        $StaffAssignmentID  = (int) $this->get('StaffAssignmentID');
        $StaffID            = (int) $this->get('StaffID');
        $textSearch         = $this->get('textSearch');
        $ProvinceID         = $this->get('ProvinceID');
        $DistrictID         = $this->get('DistrictID');
        $SubdistrictID      = $this->get('SubdistrictID');
        $VillageID          = $this->get('VillageID');

        //sort
        $sorting = json_decode($this->get('sort'));
        $sortingField = $sorting[0]->property;
        $sortingDir = $sorting[0]->direction;

        $result = $this->mext_staff->getMemberAdd($StaffAssignmentID, $StaffID, $textSearch, $ProvinceID, $DistrictID, $SubdistrictID, $VillageID, $this->get('start'), $this->get('limit'), $sortingField, $sortingDir);
        $this->response($result, 200);
    }

    public function member_post(){
        $StaffAssignmentID = (int) $this->post('StaffAssignmentID');
        $StaffID = (int) $this->post('StaffID');
        $MemberID = json_decode($this->post('MemberID'));

        $result = $this->mext_staff->insertMember($MemberID, $StaffAssignmentID, $StaffID);
        $this->response($result, 200);
    }

    public function import_farmer_assign_post(){

        if ($this->file['Koltiva_view_Ext_staff_WinFormImportFarmer-Form-FileImport']['name'] != "") {
            //Cek extensi
            $arrTemp = explode(".", $this->file['Koltiva_view_Ext_staff_WinFormImportFarmer-Form-FileImport']['name']);
            $extNya  = array_values(array_slice($arrTemp, -1))[0];
            $extNya  = strtolower($extNya);
            $StaffAssignmentID   = $this->post('StaffAssignmentID');
            $StaffID   = $this->post('StaffID');

            if ($extNya == 'xlsx') {
                $reader = ReaderEntityFactory::createXLSXReader();
                $reader->setShouldFormatDates(true);
                $reader->open($this->file['Koltiva_view_Ext_staff_WinFormImportFarmer-Form-FileImport']['tmp_name']);

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
                            $MemberID = $this->mext_staff->getMemberID($cells[0]);
                            $dataExcel[$increInt - 1] = $MemberID;
                        }
                        $increInt++;
                    }
                }
                $reader->close();

                $results = $this->mext_staff->insertMemberAssign($dataExcel, $StaffAssignmentID, $StaffID);
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
        $dataMapping = $this->mext_staff->GetFarmerListGrid($StaffAssignmentID, '', $StaffID, null, null, 'no_limit');
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
}