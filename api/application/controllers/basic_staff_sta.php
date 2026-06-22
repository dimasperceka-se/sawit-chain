<?php
/**
 * @Author: nikolius
 * @Date:   2016-09-27 16:57:44
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Basic_staff_sta extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->file = $_FILES;
        $this->load->model('mbasic_staff_sta');
    }

    public function main_list_get(){
        $sorting = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $data = $this->mbasic_staff_sta->getMainListBasicStaff($this->get('ObjType'),$this->get('PersonNm'),$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        if($data) $this->response($data, 200); else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    public function propinsi_get(){
        $data = $this->mbasic_staff_sta->getPropinsi();
        $this->response($data, 200);
    }

    public function kecamatan_get(){
        $data = $this->mbasic_staff_sta->getKecamatan($this->get('DistrictID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }

    public function workarea_get(){
        $data = $this->mbasic_staff_sta->getWorkarea($this->get('prov'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }

    public function desa_get(){
        $data = $this->mbasic_staff_sta->getDesa($this->get('SubDistrictID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }

    public function cpg_get(){
        $data = $this->mbasic_staff_sta->getCpg($this->get('DistrictID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }

    public function farmer_get(){
        $data = $this->mbasic_staff_sta->getFarmer($this->get('CPGid'));
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
                $data = $this->mbasic_staff_sta->getObjIdBank($DistrictID);
            break;
            case 'cooperative':
                $data = $this->mbasic_staff_sta->getObjIdCoop($DistrictID);
            break;
            case 'farmergroup':
                $data = $this->mbasic_staff_sta->getObjIdCpg($DistrictID);
            break;
            case 'extension':
                $data = $this->mbasic_staff_sta->getObjIdExtension();
                for ($i=0; $i < count($data['data']); $i++) {
                    $data['data'][$i]['label'] = lang($data['data'][$i]['label']);
                }
            break;
            case 'private':
                $data = $this->mbasic_staff_sta->getObjIdPrivate();
            break;
            case 'program':
                $data = $this->mbasic_staff_sta->getObjIdProgram();
            break;
            case 'sce':
                $data = $this->mbasic_staff_sta->getObjIdSce($DistrictID);
            break;
            case 'trader':
                $data = $this->mbasic_staff_sta->getObjIdTrader($DistrictID);
            break;
            case 'warehouse':
                $data = $this->mbasic_staff_sta->getObjIdWarehouse($DistrictID);
            break;
            case 'service':
                $data = $this->mbasic_staff_sta->getObjIdService();
            break;
            case 'mill':
                $data = $this->mbasic_staff_sta->getObjIdMill();
            break;
            case 'agent':
                $data = $this->mbasic_staff_sta->getObjIdAgent($DistrictID);
            break;
            case 'refinery':
                $data = $this->mbasic_staff_sta->getObjIdRefinery();
            break;
        }

        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }

    public function position_get(){
        $data = $this->mbasic_staff_sta->getPosition($this->get('ObjType'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }

    public function form_staff_get(){
        $data = $this->mbasic_staff_sta->getFormStaff($this->get('StaffID'));
        $this->response($data, 200);
    }

    public function user_info_group_user_get(){
        $data = $this->mbasic_staff_sta->getUserInfoGroupUser($this->get('userInfoUserId'));
        $this->response($data, 200);
    }

    public function user_info_district_access_get(){
        $data = $this->mbasic_staff_sta->getUserInfoDistrictAccess($this->get('userInfoUserId'));
        $this->response($data, 200);
    }

    public function staff_position_main_list_get(){
        $sorting = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $data = $this->mbasic_staff_sta->getMainListStaffPosition($this->get('StaffID'),$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        $this->response($data, 200);
    }

    public function image_staff_post(){
        if ($this->file['Photo']['name'] != '') {
            $gambar = date('Ymdhis') . '_' . $this->file['Photo']['name'];
            $upload = move_upload($this->file, 'images/staff/' . $gambar);
            if (isset($upload['upload_data'])) {
                unlink('images/staff/' . $this->post('Photo_old'));
                $result['success'] = true;
                $result['file']    = $gambar;
                if($this->post('user_id_from_profile') != ''){
                    $this->mbasic_staff_sta->updateFoto($gambar,$this->post('user_id_from_profile'));
                }
                $this->response($result, 200);
            } else {
            	echo  'false';
            	exit;
            }
        }
    }

    public function staff_post(){
        if($this->post('StaffID') == ""){
            //insert
            $proses = $this->mbasic_staff_sta->insertStaff($this->post());
        }else{
            //update
            $proses = $this->mbasic_staff_sta->updateStaff($this->post());
        }

        if ($proses) {
            $this->response($proses, 200);
        } else {
            $this->response(array('error' => 'Process failed'), 404);
        }
    }

    public function staff_delete(){
        $proses = $this->mbasic_staff_sta->deleteStaff($this->delete('StaffID'));
        if ($proses) {
            $this->response($proses, 200);
        } else {
            $this->response(array('error' => 'Process failed'), 404);
        }
    }

    public function form_user_get(){
        $data = $this->mbasic_staff_sta->getFormUser($this->get('StaffID'));
        $this->response($data, 200);
    }

    public function user_post(){
        if($this->post('UserId') == ""){
            //insert
            $proses = $this->mbasic_staff_sta->insertUser($this->post());
        }else{
            //update
            $proses = $this->mbasic_staff_sta->updateUser($this->post());
        }

        if ($proses) {
            $this->response($proses, 200);
        } else {
            $this->response(array('error' => 'Process failed'), 404);
        }
    }

    public function user_delete(){
        $proses = $this->mbasic_staff_sta->deleteUser($this->delete('UserId'),$this->delete('StaffID'));
        if ($proses) {
            $this->response($proses, 200);
        } else {
            $this->response(array('error' => 'Process failed'), 404);
        }
    }

    public function app_ref_role_cmb_get(){
        $data = $this->mbasic_staff_sta->getAppRefRole();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'No Data!'), 404);
        }
    }

    public function app_ref_group_cmb_get(){
        $data = $this->mbasic_staff_sta->getAppRefGroup();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'No Data!'), 404);
        }
    }

    public function form_user_app_get(){
        $data = $this->mbasic_staff_sta->getFormUserApp($this->get('StaffID'));
        $this->response($data, 200);
    }

    public function list_access_staff_app_get(){
        $data = $this->mbasic_staff_sta->getListAccessStaffApp($this->get('UserId'));
        $this->response($data, 200);
    }

    public function user_app_post(){
        if($this->post('UserExtId') == ""){
            //insert
            $proses = $this->mbasic_staff_sta->insertUserApp($this->post());
        }else{
            //update
            $proses = $this->mbasic_staff_sta->updateUserApp($this->post());
        }

        if ($proses) {
            $this->response($proses, 200);
        } else {
            $this->response(array('error' => 'Process failed'), 404);
        }
    }

    public function access_role_get(){
        $data = $this->mbasic_staff_sta->getAccessRole();
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
                $data = $this->mbasic_staff_sta->getObjIdBankAll();
                $dataSelected = $this->mbasic_staff_sta->getObjIdAllSelected('bank',$UserId);
            break;
            case '2':
                $data = $this->mbasic_staff_sta->getObjIdCoopAll();
                $dataSelected = $this->mbasic_staff_sta->getObjIdAllSelected('cooperative',$UserId);
            break;
            case '3':
                $data = $this->mbasic_staff_sta->getObjIdCpgAll();
                $dataSelected = $this->mbasic_staff_sta->getObjIdAllSelected('farmergroup',$UserId);
            break;
            case '4':
                $data = $this->mbasic_staff_sta->getObjIdExtensionAll();
                for ($i=0; $i < count($data['data']); $i++) {
                    $data['data'][$i]['label'] = lang($data['data'][$i]['label']);
                }
                $dataSelected = $this->mbasic_staff_sta->getObjIdAllSelected('extension',$UserId);
            break;
            case '5':
                $data = $this->mbasic_staff_sta->getObjIdPrivateAll();
                $dataSelected = $this->mbasic_staff_sta->getObjIdAllSelected('private',$UserId);
            break;
            case '6':
                $data = $this->mbasic_staff_sta->getObjIdProgramAll();
                $dataSelected = $this->mbasic_staff_sta->getObjIdAllSelected('program',$UserId);
            break;
            case '7':
                $data = $this->mbasic_staff_sta->getObjIdSceAll();
                $dataSelected = $this->mbasic_staff_sta->getObjIdAllSelected('sce',$UserId);
            break;
            case '8':
                $data = $this->mbasic_staff_sta->getObjIdTraderAll();
                $dataSelected = $this->mbasic_staff_sta->getObjIdAllSelected('trader',$UserId);
            break;
            case '9':
                $data = $this->mbasic_staff_sta->getObjIdWarehouseAll();
                $dataSelected = $this->mbasic_staff_sta->getObjIdAllSelected('warehouse',$UserId);
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
        $data = $this->mbasic_staff_sta->getObjTypeList();
        $this->response($data, 200);
    }

    public function position_reference_get(){
        $data = $this->mbasic_staff_sta->getPositionReference($this->get('ObjType'));
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
            $proses = $this->mbasic_staff_sta->insertStaffPosition($varPost);
        }else{
            $proses = $this->mbasic_staff_sta->updateStaffPosition($varPost);
        }
        if ($proses['success']) {
            $this->response($proses, 200);
        } else {
            $this->response('Process Failed', 400);
        }
    }

    public function staff_position_delete(){
        $proses = $this->mbasic_staff_sta->deleteStaffPosition($this->delete('id'));
        $this->response($proses, 200);
    }

    public function user_project_get(){
        $projects = $this->mbasic_staff_sta->getProject();
        $this->response($projects, 200);
    }

    //===================================================================== Register Staff =================================================================== //

    public function registers_main_grid_get(){
        $sorting = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $pSearch = array(
            "Role" => $this->get('Role'),
            "StringNameUsername" => $this->get('StringNameUsername')
        );

        $data = $this->mbasic_staff_sta->getRegistersMainGridList($pSearch,$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        $this->response($data, 200);
    }

    public function register_staff_fill_form_get(){
        $RegID = (int) $this->get('RegID');

        $data = $this->mbasic_staff_sta->getDataFillFormRegisterStaff($RegID);
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
            $proses = $this->mbasic_staff_sta->insertRegisterStaff($paramPost);
        }else{
            $proses = $this->mbasic_staff_sta->updateRegisterStaff($paramPost);
        }

        $this->response($proses, 200);
    }

    public function register_staff_delete(){
        $RegID = (int) $this->delete('RegID');
        $proses = $this->mbasic_staff_sta->deleteRegisterStaff($RegID);
        $this->response($proses, 200);
    }

    public function register_staff_send_email_registration_post(){
        $RegID = (int) $this->post('RegID');
        $proses = $this->mbasic_staff_sta->sendEmailRegistrationLink($RegID);
        $this->response($proses, 200);
    }

    public function staff_general_form_get(){
        $StaffID = (int) $this->get('StaffID');
        $data = $this->mbasic_staff_sta->getStaffGeneralForm($StaffID);
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
            $proses = $this->mbasic_staff_sta->insertStaffGeneral($paramPost);
        }else{
            //update
            $proses = $this->mbasic_staff_sta->updateStaffGeneral($paramPost);
        }
        $this->response($proses, 200);
    }

    public function staff_general_delete(){
        $StaffID = (int) $this->delete('StaffID');
        $proses = $this->mbasic_staff_sta->deleteStaff($StaffID);
        $this->response($proses, 200);
    }
}
?>