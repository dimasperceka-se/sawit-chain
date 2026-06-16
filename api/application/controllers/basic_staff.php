<?php
/**
 * @Author: nikolius
 * @Date:   2016-09-27 16:57:44
 */
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('display_errors',false); error_reporting(0);

class Basic_staff extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->file = $_FILES;
        $this->load->model('mbasic_staff');
    }

    function list_mill_get(){
        $this->load->model('mill/mmill');
        $mill = $this->mmill->getListAllMill();

        if($mill) $this->response($mill, 200);
        else $this->response(array('error' => 'Couldn\'t find any mill!'), 404);
    }

    function list_refinery_get(){
        $this->load->model('refinery/mrefinery');
        $refinery = $this->mrefinery->getListAllRefinery();

        if($refinery) $this->response($refinery, 200);
        else $this->response(array('error' => 'Couldn\'t find any mill!'), 404);
    }

    function list_sme_get(){
        $this->load->model('mill/mmill');
        $mill = $this->mmill->getListAllSME($this->get("MillID"));

        if($mill) $this->response($mill, 200);
        else $this->response(array('error' => 'Couldn\'t find any mill!'), 404);
    }

    function list_sme_staff_get(){
        $this->load->model('mill/mmill');
        $mill = $this->mmill->getListAllSMEStaff($this->get("MillID"));

        if($mill) $this->response($mill, 200);
        else $this->response(array('error' => 'Couldn\'t find any mill!'), 404);
    }

    public function main_list_get(){
        $sorting = json_decode($this->get('sort'));
        $sortingField = $sorting[0]->property;
        $sortingDir = $sorting[0]->direction;

        $data = $this->mbasic_staff->getMainListBasicStaff($this->get('ObjType'),$this->get('PersonNm'),$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        if($data) $this->response($data, 200); else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    public function propinsi_get(){
        $data = $this->mbasic_staff->getPropinsi();
        $this->response($data, 200);
    }

    public function kecamatan_get(){
        $data = $this->mbasic_staff->getKecamatan($this->get('DistrictID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }

    public function workarea_get(){
        $data = $this->mbasic_staff->getWorkarea($this->get('prov'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }

    public function desa_get(){
        $data = $this->mbasic_staff->getDesa($this->get('SubDistrictID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }

    public function cpg_get(){
        $data = $this->mbasic_staff->getCpg($this->get('DistrictID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }

    public function farmer_get(){
        $data = $this->mbasic_staff->getFarmer($this->get('CPGid'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }

    public function objectid_get(){
        $this->load->language('general', $_SESSION['language']);
        $DistrictID = $this->get('DistrictID');

        switch ($this->get('ObjType')) {
            case 'bank':
                $data = $this->mbasic_staff->getObjIdBank($DistrictID);
            break;
            case 'cooperative':
                $data = $this->mbasic_staff->getObjIdCoop($DistrictID);
            break;
            case 'farmergroup':
                $data = $this->mbasic_staff->getObjIdCpg($DistrictID);
            break;
            case 'extension':
                $data = $this->mbasic_staff->getObjIdExtension();
                for ($i=0; $i < count($data['data']); $i++) {
                    $data['data'][$i]['label'] = lang($data['data'][$i]['label']);
                }
            break;
            case 'private':
                $data = $this->mbasic_staff->getObjIdPrivate();
            break;
            case 'program':
                $data = $this->mbasic_staff->getObjIdProgram();
            break;
            case 'sce':
                $data = $this->mbasic_staff->getObjIdSce($DistrictID);
            break;
            case 'trader':
                $data = $this->mbasic_staff->getObjIdTrader($DistrictID);
            break;
            case 'warehouse':
                $data = $this->mbasic_staff->getObjIdWarehouse($DistrictID);
            break;
            case 'service':
                $data = $this->mbasic_staff->getObjIdService();
            break;
            case 'mill':
                $data = $this->mbasic_staff->getObjIdMill();
            break;
            case 'agent':
                $data = $this->mbasic_staff->getObjIdAgent($DistrictID);
            break;
            case 'refinery':
                $data = $this->mbasic_staff->getObjIdRefinery();
            break;
        }

        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }

    public function position_get(){
        $data = $this->mbasic_staff->getPosition($this->get('ObjType'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }

    public function form_staff_get(){
        $data = $this->mbasic_staff->getFormStaff($this->get('StaffID'));
        $this->response($data, 200);
    }

    public function user_info_group_user_get(){
        $data = $this->mbasic_staff->getUserInfoGroupUser($this->get('userInfoUserId'));
        $this->response($data, 200);
    }

    public function user_info_district_access_get(){
        $data = $this->mbasic_staff->getUserInfoDistrictAccess($this->get('userInfoUserId'));
        $this->response($data, 200);
    }

    public function staff_position_main_list_get(){
        $sorting = json_decode($this->get('sort'));
        $sortingField = $sorting[0]->property;
        $sortingDir = $sorting[0]->direction;

        $data = $this->mbasic_staff->getMainListStaffPosition($this->get('StaffID'),$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        $this->response($data, 200);
    }

    public function image_staff_post(){
        //Cek file images
        $ExtNya = GetFileExt($_FILES['Photo']['name']);
        if (!in_array($ExtNya, array('png', 'jpg', 'jpeg', 'gif'))) {
            $result['success'] = false;
            $result['message'] = lang('File types not allowed');
            $this->response($result, 400);
        } else {
            //Jika update, baru langsung ke aws
            if ($this->file['Photo']['name'] != '') {
                //Untuk AWS S3, wajib ada
                $this->load->library('awsfileupload');
                $upload = $this->awsfileupload->upload($_FILES['Photo']['tmp_name'],$_FILES['Photo']['name'], AWSS3_STAFF_PHOTO, 'images');
                if ($upload['success'] == true) {
                    $prosesUpdate = $this->mbasic_staff->updateFotoProfile($upload['filenamepath']);
                    $result['success'] = true;
                    $result['message'] = lang('File uploaded');
                    $result['fileurl'] = $upload['fileurl'];
                    $this->response($result, 200);
                } else {
                    $result['success'] = false;
                    $result['message'] = lang('Upload to aws failed');
                    $this->response($result, 400);
                }
            }
        }

        $result['success'] = false;
        $result['message'] = lang('No files');
        $this->response($result, 400);
    }

    public function staff_post(){
        if($this->post('StaffID') == ""){
            //insert
            $proses = $this->mbasic_staff->insertStaff($this->post());
        }else{
            //update
            $proses = $this->mbasic_staff->updateStaff($this->post());
        }

        if ($proses) {
            $this->response($proses, 200);
        } else {
            $this->response(array('error' => 'Process failed'), 404);
        }
    }

    public function staff_delete(){
        $proses = $this->mbasic_staff->deleteStaff($this->delete('StaffID'));
        if ($proses) {
            $this->response($proses, 200);
        } else {
            $this->response(array('error' => 'Process failed'), 404);
        }
    }

    public function form_user_get(){
        $data = $this->mbasic_staff->getFormUser($this->get('StaffID'));
        $this->response($data, 200);
    }

    public function user_post(){
        if($this->post('UserId') == ""){
            //insert
            $proses = $this->mbasic_staff->insertUser($this->post());
        }else{
            //update
            $proses = $this->mbasic_staff->updateUser($this->post());
        }

        if ($proses) {
            $this->response($proses, 200);
        } else {
            $this->response(array('error' => 'Process failed'), 404);
        }
    }

    public function user_delete(){
        $proses = $this->mbasic_staff->deleteUser($this->delete('UserId'),$this->delete('StaffID'));
        if ($proses) {
            $this->response($proses, 200);
        } else {
            $this->response(array('error' => 'Process failed'), 404);
        }
    }

    public function app_ref_role_cmb_get(){
        $data = $this->mbasic_staff->getAppRefRole();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'No Data!'), 404);
        }
    }

    public function app_ref_group_cmb_get(){
        $data = $this->mbasic_staff->getAppRefGroup();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'No Data!'), 404);
        }
    }

    public function form_user_app_get(){
        $data = $this->mbasic_staff->getFormUserApp($this->get('StaffID'));
        $this->response($data, 200);
    }

    public function list_access_staff_app_get(){
        $data = $this->mbasic_staff->getListAccessStaffApp($this->get('UserId'));
        $this->response($data, 200);
    }

    public function user_app_post(){
        if($this->post('UserExtId') == ""){
            //insert
            $proses = $this->mbasic_staff->insertUserApp($this->post());
        }else{
            //update
            $proses = $this->mbasic_staff->updateUserApp($this->post());
        }

        if ($proses) {
            $this->response($proses, 200);
        } else {
            $this->response(array('error' => 'Process failed'), 404);
        }
    }

    public function access_role_get(){
        $data = $this->mbasic_staff->getAccessRole();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any district!'), 404);
        }
    }

    public function access_object_list_post(){
        $RoleId = $this->post('RoleId');
        $UserId = $this->post('UserId');
        $this->load->language('general', $_SESSION['language']);

        switch ($RoleId) {
            case '1':
                //bank
                $data = $this->mbasic_staff->getObjIdBankAll();
                $dataSelected = $this->mbasic_staff->getObjIdAllSelected('bank',$UserId);
            break;
            case '2':
                $data = $this->mbasic_staff->getObjIdCoopAll();
                $dataSelected = $this->mbasic_staff->getObjIdAllSelected('cooperative',$UserId);
            break;
            case '3':
                $data = $this->mbasic_staff->getObjIdCpgAll();
                $dataSelected = $this->mbasic_staff->getObjIdAllSelected('farmergroup',$UserId);
            break;
            case '4':
                $data = $this->mbasic_staff->getObjIdExtensionAll();
                for ($i=0; $i < count($data['data']); $i++) {
                    $data['data'][$i]['label'] = lang($data['data'][$i]['label']);
                }
                $dataSelected = $this->mbasic_staff->getObjIdAllSelected('extension',$UserId);
            break;
            case '5':
                $data = $this->mbasic_staff->getObjIdPrivateAll();
                $dataSelected = $this->mbasic_staff->getObjIdAllSelected('private',$UserId);
            break;
            case '6':
                $data = $this->mbasic_staff->getObjIdProgramAll();
                $dataSelected = $this->mbasic_staff->getObjIdAllSelected('program',$UserId);
            break;
            case '7':
                $data = $this->mbasic_staff->getObjIdSceAll();
                $dataSelected = $this->mbasic_staff->getObjIdAllSelected('sce',$UserId);
            break;
            case '8':
                $data = $this->mbasic_staff->getObjIdTraderAll();
                $dataSelected = $this->mbasic_staff->getObjIdAllSelected('trader',$UserId);
            break;
            case '9':
                $data = $this->mbasic_staff->getObjIdWarehouseAll();
                $dataSelected = $this->mbasic_staff->getObjIdAllSelected('warehouse',$UserId);
            break;
        }

        if(count($data['data']) > 0){
            $data['success'] = true;
        }else{
            $data['success'] = false;
        }
        $data['dataSelected'] = $dataSelected;
        $this->response($data, 200);
    }

    public function objtype_list_get(){
        $data = $this->mbasic_staff->getObjTypeList();
        $this->response($data, 200);
    }

    public function position_reference_get(){
        $data = $this->mbasic_staff->getPositionReference($this->get('ObjType'));
        $this->response($data, 200);
    }

    public function staff_position_post(){
        //var proses (begin)
        foreach ($this->post() as $key => $value) {
            if($value == ""){
                $varPost[$key] = null;
            }else{
                $varPost[$key] = $value;
            }
        }
        $varPost['userid'] = $_SESSION['userid'];
        //var proses (end)

        $arrTmp = explode("T",$varPost['StaffPostStart']);
        $varPost['StaffPostStartDate'] = $arrTmp[0];

        $arrTmp = explode("T",$varPost['StaffPostEnd']);
        $varPost['StaffPostEndDate'] = $arrTmp[0];

        if($varPost['StaffPosID'] == ""){
            $proses = $this->mbasic_staff->insertStaffPosition($varPost);
        }else{
            $proses = $this->mbasic_staff->updateStaffPosition($varPost);
        }
        if ($proses['success']) {
            $this->response($proses, 200);
        } else {
            $this->response('Process Failed', 400);
        }
    }

    public function staff_position_delete(){
        $proses = $this->mbasic_staff->deleteStaffPosition($this->delete('id'));
        $this->response($proses, 200);
    }

    public function user_project_get(){
        $projects = $this->mbasic_staff->getProject();
        $this->response($projects, 200);
    }

    //===================================================================== Register Staff =================================================================== //

    public function registers_main_grid_get(){
        $sorting = json_decode($this->get('sort'));
        $sortingField = $sorting[0]->property;
        $sortingDir = $sorting[0]->direction;

        $pSearch = array(
            "Role" => $this->get('Role'),
            "StringNameUsername" => $this->get('StringNameUsername')
        );

        $data = $this->mbasic_staff->getRegistersMainGridList($pSearch,$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        $this->response($data, 200);
    }

    public function register_staff_fill_form_get(){
        $RegID = (int) $this->get('RegID');

        $data = $this->mbasic_staff->getDataFillFormRegisterStaff($RegID);
        $this->response($data, 200);
    }

    public function register_staff_post(){
        $varPost = $this->post();

        //prep variabel (begin)
        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Staff_RegisterStaff_WinRegisterStaffForm-Form-", '', $key);
            if($value == "") $value = null;

            $paramPost[$keyNew] = $value;
        }
        //prep variabel (end)

        if($paramPost['RegID'] == ''){
            $proses = $this->mbasic_staff->insertRegisterStaff($paramPost);
        }else{
            $proses = $this->mbasic_staff->updateRegisterStaff($paramPost);
        }

        $this->response($proses, 200);
    }

    public function register_staff_delete(){
        $RegID = (int) $this->delete('RegID');
        $proses = $this->mbasic_staff->deleteRegisterStaff($RegID);
        $this->response($proses, 200);
    }

    public function register_staff_send_email_registration_post(){
        $RegID = (int) $this->post('RegID');
        $proses = $this->mbasic_staff->sendEmailRegistrationLink($RegID);
        $this->response($proses, 200);
    }

    public function staff_general_form_get(){
        $StaffID = (int) $this->get('StaffID');
        $data = $this->mbasic_staff->getStaffGeneralForm($StaffID);
        $this->response($data, 200);
    }

    public function staff_general_post(){
        $varPost = $this->post();

        //prep variabel (begin)
        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Staff_WinFormStaffGeneral-Form-", '', $key);
            if ($value == "")
                $value = null;

            switch ($keyNew) {
                case 'WageAmount':
                    $value = str_replace(",", "", $value);
                break;
            }

            $paramPost[$keyNew] = $value;
        }
        //prep variabel (end)

        if($paramPost['StaffID'] == ""){
            //insert
            $proses = $this->mbasic_staff->insertStaffGeneral($paramPost);
        }else{
            //update
            $proses = $this->mbasic_staff->updateStaffGeneral($paramPost);
        }
        $this->response($proses, 200);
    }

    public function staff_general_delete(){
        $StaffID = (int) $this->delete('StaffID');
        $proses = $this->mbasic_staff->deleteStaff($StaffID);
        $this->response($proses, 200);
    }

    public function phone_code_get(){
        $data = $this->mbasic_staff->getPhoneCode();
        $this->response($data, 200);
    }
}
?>