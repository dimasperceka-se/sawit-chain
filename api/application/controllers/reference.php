<?php
/**
 * @Author: nikolius
 * @Date:   2016-03-17 18:18:30
 * @Last Modified by: sonny.fitriawan
 * @Last Modified time: 2017-12-08 15:17:50
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Reference extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('position/mposition');
        $this->load->model('reference/mcommodity_type');
        $this->load->model('reference/mcrop_type');
        $this->load->model('reference/minfrastructure_type');
        $this->load->model('reference/mcertification_program');
        $this->load->model('vehicle/mvehicle');
    }

    public function position_combo_role_get(){
        $result = $this->mposition->position_combo_role();
        $this->response($result, 200);
    }

    //position
    public function positions_get()
    {
        $sorting = json_decode($this->get('sort'));
        $sortingField = $sorting[0]->property;
        $sortingDir = $sorting[0]->direction;

        /*$key = '';
        if($this->get('key') !== NULL){
           $key = $this->get('key');
        }*/

        $position_list = $this->mposition->position_list($this->get('key'), $this->get('start'), $this->get('limit'), $sortingField, $sortingDir);

        $this->response($position_list, 200);
    }

    public function position_get()
    {
        if (!$this->get('id')) {
            $this->response(null, 400);
        }

        $position_id = $this->mposition->position_id($this->get('id'));
        if ($position_id) {
            $this->response($position_id, 200);
        } else {
            $this->response(array('error' => 'Position could not be found'), 404);
        }

    }

    public function position_post()
    {
        if (!$this->post('PositionName')) {
            $this->response(null, 400);
        }

        $proses = $this->mposition->create_position($this->post('PositionCode'),$this->post('PositionName'), $this->post('Category'), $this->post('StatusCode'), $_SESSION['userid']);
        if ($proses) {
            $this->response($proses, 200);
        } else {
            $this->response(array('error' => 'Insert Data Failed'), 404);
        }

    }

    public function position_put()
    {
        if (!$this->put('PositionID')) {
            $this->response(null, 400);
        }

        $proses = $this->mposition->update_position($this->put('PositionID'), $this->put('PositionCode'), $this->put('PositionName'), $this->put('Category'), $this->put('StatusCode'), $_SESSION['userid']);
        if ($proses) {
            $this->response($proses, 200);
        } else {
            $this->response(array('error' => 'PositionID could not be found'), 404);
        }

    }

    public function position_delete()
    {
        if (!$this->delete('PositionID')) {
            $this->response(null, 400);
        }

        $proses = $this->mposition->delete_position($this->delete('PositionID'), $_SESSION['userid']);
        if ($proses) {
            $this->response($proses, 200);
        } else {
            $this->response(array('error' => 'Position could not be delete'), 404);
        }

    }

    /*=========================================================================================================================*/

    public function commodity_types_get()
    {
        $result = $this->mcommodity_type->commodity_type_list($this->get('start'), $this->get('limit'));
        $this->response($result, 200);
    }

    public function commodity_type_post()
    {
        if (!$this->post('CommodityTypeName')) {
            $this->response(null, 400);
        }

        $proses = $this->mcommodity_type->create_commodity_type($this->post('CommodityTypeName'), $this->post('StatusCode'), $_SESSION['userid']);
        if ($proses) {
            $this->response($proses, 200);
        } else {
            $this->response(array('error' => 'Insert Data Failed'), 404);
        }

    }

    public function commodity_type_put()
    {
        if (!$this->put('CommodityTypeID')) {
            $this->response(null, 400);
        }

        $proses = $this->mcommodity_type->update_commodity_type($this->put('CommodityTypeID'), $this->put('CommodityTypeName'), $this->put('StatusCode'), $_SESSION['userid']);
        if ($proses) {
            $this->response($proses, 200);
        } else {
            $this->response(array('error' => 'CommodityTypeID could not be found'), 404);
        }

    }

    public function commodity_type_delete()
    {
        if (!$this->delete('CommodityTypeID')) {
            $this->response(null, 400);
        }

        $proses = $this->mcommodity_type->delete_commodity_type($this->delete('CommodityTypeID'), $_SESSION['userid']);
        if ($proses) {
            $this->response($proses, 200);
        } else {
            $this->response(array('error' => 'Data could not be delete'), 404);
        }

    }

    /*=========================================================================================================================*/

    public function crop_types_get()
    {
        $result = $this->mcrop_type->crop_type_list($this->get('start'), $this->get('limit'));
        $this->response($result, 200);
    }

    public function crop_type_post()
    {
        if (!$this->post('CropTypeName')) {
            $this->response(null, 400);
        }

        $proses = $this->mcrop_type->create_crop_type($this->post('CropTypeName'), $this->post('StatusCode'), $_SESSION['userid']);
        if ($proses) {
            $this->response($proses, 200);
        } else {
            $this->response(array('error' => 'Insert Data Failed'), 404);
        }

    }

    public function crop_type_put()
    {
        if (!$this->put('CropTypeID')) {
            $this->response(null, 400);
        }

        $proses = $this->mcrop_type->update_crop_type($this->put('CropTypeID'), $this->put('CropTypeName'), $this->put('StatusCode'), $_SESSION['userid']);
        if ($proses) {
            $this->response($proses, 200);
        } else {
            $this->response(array('error' => 'CropTypeID could not be found'), 404);
        }

    }

    public function crop_type_delete()
    {
        if (!$this->delete('CropTypeID')) {
            $this->response(null, 400);
        }

        $proses = $this->mcrop_type->delete_crop_type($this->delete('CropTypeID'), $_SESSION['userid']);
        if ($proses) {
            $this->response($proses, 200);
        } else {
            $this->response(array('error' => 'Data could not be delete'), 404);
        }

    }

    /*=========================================================================================================================*/

    public function infrastructure_types_get()
    {
        $result = $this->minfrastructure_type->infrastructure_type_list($this->get('start'), $this->get('limit'));
        $this->response($result, 200);
    }

    public function infrastructure_type_post()
    {
        if (!$this->post('InfrastructureTypeName')) {
            $this->response(null, 400);
        }

        $proses = $this->minfrastructure_type->create_infrastructure_type($this->post('InfrastructureTypeName'), $this->post('StatusCode'), $_SESSION['userid']);
        if ($proses) {
            $this->response($proses, 200);
        } else {
            $this->response(array('error' => 'Insert Data Failed'), 404);
        }

    }

    public function infrastructure_type_put()
    {
        if (!$this->put('InfrastructureTypeID')) {
            $this->response(null, 400);
        }

        $proses = $this->minfrastructure_type->update_infrastructure_type($this->put('InfrastructureTypeID'), $this->put('InfrastructureTypeName'), $this->put('StatusCode'), $_SESSION['userid']);
        if ($proses) {
            $this->response($proses, 200);
        } else {
            $this->response(array('error' => 'InfrastructureTypeID could not be found'), 404);
        }

    }

    public function infrastructure_type_delete()
    {
        if (!$this->delete('InfrastructureTypeID')) {
            $this->response(null, 400);
        }

        $proses = $this->minfrastructure_type->delete_infrastructure_type($this->delete('InfrastructureTypeID'), $_SESSION['userid']);
        if ($proses) {
            $this->response($proses, 200);
        } else {
            $this->response(array('error' => 'Data could not be delete'), 404);
        }

    }

    /*=========================================================================================================================*/

    function certification_programs_get() {
        $certification_programs = $this->mcertification_program->readCertificationPrograms($this->get('key'),$this->get('start'), $this->get('limit'));
        if ($certification_programs)
            $this->response($certification_programs, 200);
        else
            $this->response(array(), 200);
    }

    function certification_program_post(){
        if($this->post('CertProgID')==""){
            $proses = $this->mcertification_program->create_certification_program($this->post('CertProgName'), $this->post('CertProgOfficialName'), $this->post('CertProgAddress'), $this->post('CertProgPhone'), $this->post('CertProgEmail'), $this->post('CertProgWeb'), $this->post('PhotoOld'));
        }else{
            $proses = $this->mcertification_program->update_certification_program($this->post('CertProgName'), $this->post('CertProgOfficialName'), $this->post('CertProgAddress'), $this->post('CertProgPhone'), $this->post('CertProgEmail'), $this->post('CertProgWeb'), $this->post('CertProgID'));
        }
        if ($proses) {
            $this->response($proses, 200);
        } else {
            $this->response(array('error' => 'Insert Data Failed'), 404);
        }

    }

    function certification_program_logo_post(){
        $this->load->library('awsfileupload');
        if($this->post('CertProgID')==""){
            if ($_FILES['Photo']['name'] != '') {
                $dir = FCPATH . 'images/certification_provider';
                if(!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }
                $gambar = 'certprog_' .date('Ymdhis').'.jpg';
                $upload = move_upload($_FILES, 'images/certification_provider/' . $gambar);
                if (isset($upload['upload_data'])) {
                    $result['success'] = true;
                    $result['message'] = lang('File uploaded');
                    $result['file'] = base_url().'images/certification_provider/'.$gambar;
                    $result['filepath']   = 'images/certification_provider/'.$gambar;
                    $this->response($result, 200);
                }else{
                    $result['file'] = "";
                    $result['filepath'] = "";
                    $result['message'] = "Photo not uploaded!";
                    $this->response($result, 200);
                }
            }else{
                $result['file'] = "";
                $result['filepath'] = "";
                $result['message'] = "No Photo.";
                $this->response($result, 200);
            }
        }else{
            if ($_FILES['Photo']['name'] != '') {
                $upload = $this->awsfileupload->upload($this->file['Photo']['tmp_name'],$this->file['Photo']['name'], AWSS3_CERT_PROG_PATH, 'images');
                if ($upload['success'] == true) {
                    if($this->awsfileupload->doesObjectExist($this->post('PhotoOld')) == true) {
                        $this->awsfileupload->delete($this->post('PhotoOld'));
                    }else{
                        delete_file($this->post('PhotoOld'));
                    }
                    $prosesUpdate = $this->mcertification_program->updateLogo($_POST["CertProgID"],$upload['filenamepath']);
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
            }else{
                $result['file'] = "";
                $result['filepath'] = "";
                $result['message'] = "No Photo.";
                $this->response($result, 200);
            }
        }
    }

    function certification_program_get() {
        $certification_program = $this->mcertification_program->readCertificationProgram($this->get('CertProgID'));
        if ($certification_program){
            $this->response($certification_program, 200);
        }else{
            $this->response(array(), 200);
        }
    }

    public function certification_program_delete()
    {
        if (!$this->delete('CertProgID')) {
            $this->response(null, 400);
        }

        $proses = $this->mcertification_program->delete_certification_program($this->delete('CertProgID'));
        if ($proses) {
            $this->response($proses, 200);
        } else {
            $this->response(array('error' => 'Data could not be delete'), 404);
        }

    }

    //vehicle
    public function vehicles_get()
    {
        $sorting = json_decode($this->get('sort'));
        $sortingField = $sorting[0]->property;
        $sortingDir = $sorting[0]->direction;

        $position_list = $this->mvehicle->vehicle_list($this->get('start'), $this->get('limit'),$sortingField,$sortingDir);
        $this->response($position_list, 200);
    }

    public function vehicle_get()
    {
        if (!$this->get('id')) {
            $this->response(null, 400);
        }

        $vehicle_id = $this->mvehicle->vehicle_id($this->get('id'));
        if ($vehicle_id) {
            $this->response($vehicle_id, 200);
        } else {
            $this->response(array('error' => 'Vehicle brand could not be found'), 404);
        }
    }

    public function vehicle_post()
    {
        if (!$this->post('BrandName')) {
            $this->response(null, 400);
        }
        
        $proses = $this->mvehicle->create_vehicle($this->post('BrandName'), $this->post('StatusCode'), $_SESSION['userid']); 
        
        if ($proses) {
            $this->response($proses, 200);
        } else {
            $this->response(array('error' => 'Insert Data Failed'), 404);
        }

    }

    public function vehicle_put()
    {
        if (!$this->put('BrandID')) {
            $this->response('asd', 400);
        }

        $proses = $this->mvehicle->update_vehicle($this->put('BrandID'), $this->put('BrandName'), $this->put('StatusCode'), $_SESSION['userid']);
        if ($proses) {
            $this->response($proses, 200);
        } else {
            $this->response(array('error' => 'BrandID could not be found'), 404);
        }

    }

    public function vehicle_delete()
    {
        if (!$this->delete('BrandID')) {
            $this->response(null, 400);
        }

        $proses = $this->mvehicle->delete_vehicle($this->delete('BrandID'), $_SESSION['userid']);
        if ($proses) {
            $this->response($proses, 200);
        } else {
            $this->response(array('error' => 'Vehicle rand could not be delete'), 404);
        }

    }

}
