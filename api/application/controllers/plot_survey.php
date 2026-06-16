<?php
/**
 * @Author: nikolius
 * @Date:   2017-05-31 11:47:12
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
class Plot_survey extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->file = $_FILES;
        $this->load->model('plot_survey/mplot_survey');
    }

    public function grid_plot_survey_summary_get(){
        $data = $this->mplot_survey->getGridPlotSurveySummary($this->get('MemberID'),$this->get('from'));
        $this->response($data, 200);
    }

    public function combo_collection_get(){
        $data = $this->mplot_survey->getComboCollection($this->get('DistrictID'));
        $this->response($data, 200);
    }

    public function grid_plot_certification_get(){
        $data = $this->mplot_survey->getGridPlotCertification($this->get('MemberID'));
        $this->response($data, 200);
    }

    public function combo_survey_nr_get(){
        $data = $this->mplot_survey->getComboSurveyNr($this->get('from'));
        $this->response($data, 200);
    }

    public function photo_visit_sme_post(){
        $this->load->library('awsfileupload');
        if($this->post('opsiDisplay') == "insert"){
            if ($this->file['Koltiva_view_PlotSurvey_WinFormSmePlotSurvey-Form-PhotoOfVisitInput']['name'] != '') {
                $gambar = 'temp/'.$this->file['Koltiva_view_PlotSurvey_WinFormSmePlotSurvey-Form-PhotoOfVisitInput']['name'];
                $fileupload['Koltiva_view_PlotSurvey_WinFormSmePlotSurvey-Form-PhotoOfVisitInput'] = $this->file['Koltiva_view_PlotSurvey_WinFormSmePlotSurvey-Form-PhotoOfVisitInput'];
                
                $path = 'images/plot_visit_sme/';
                $upload = move_upload($fileupload, $path . $gambar);
                if (isset($upload['upload_data'])) {
                    $result['success'] = true;
                    $result['file']     = base_url().$path.$gambar;
                    $result['filepath'] = $path . $gambar;
                    $this->response($result, 200);
                } else {
                    echo  'false'; exit;
                }
            }
        }

        if($this->post('opsiDisplay') == "update"){
            $varPost = $this->post();

            if ($this->file['Koltiva_view_PlotSurvey_WinFormSmePlotSurvey-Form-PhotoOfVisitInput']['name'] != '') {
                $ProvinceID = $MemberData['ProvinceID'];
                $MemberDisplayID = $varPost['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-MemberDisplayID'];
                
                $upload = $this->awsfileupload->upload($this->file['Koltiva_view_PlotSurvey_WinFormSmePlotSurvey-Form-PhotoOfVisitInput']['tmp_name'],$this->file['Koltiva_view_PlotSurvey_WinFormSmePlotSurvey-Form-PhotoOfVisitInput']['name'], AWSS3_SME_PLOT_PATH, 'images');
               
                
                if ($upload['success'] == true) {
                    if($this->awsfileupload->doesObjectExist($this->post('Koltiva_view_PlotSurvey_WinFormSmePlotSurvey-Form-PhotoOfVisitOld')) == true) {
                        $this->awsfileupload->delete($this->post('Koltiva_view_PlotSurvey_WinFormSmePlotSurvey-Form-PhotoOfVisitOld'));
                    }else{
                        delete_file($this->post('Koltiva_view_PlotSurvey_WinFormSmePlotSurvey-Form-PhotoOfVisitOld'));
                    }
                    $prosesUpdate = $this->mplot_survey->updatePlotImage('SME',$_POST["MemberID"],$upload['filenamepath']);
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
    }

    public function photo_visit_post(){
        $this->load->library('awsfileupload');
        if($this->post('opsiDisplay') == "insert"){
            if ($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoOfVisitInput']['name'] != '') {
                $gambar = 'temp/'.$this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoOfVisitInput']['name'];
                $fileupload['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoOfVisitInput'] = $this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoOfVisitInput'];
                if ($this->post('User') == 'Farmer') {
                    $path = 'images/plot_visit/';
                } elseif ($this->post('User') == 'SME') {
                    $path = 'images/plot_visit_sme/';
                }else{
                    $path = 'images/plot_visit/';
                }
                $upload = move_upload($fileupload, $path . $gambar);
                if (isset($upload['upload_data'])) {
                    $result['success'] = true;
                    $result['file']     = base_url().$path.$gambar;
                    $result['filepath'] = $path . $gambar;
                    $this->response($result, 200);
                } else {
                    echo  'false'; exit;
                }
            }
        }

        if($this->post('opsiDisplay') == "update"){
            $varPost = $this->post();

            //get member data
            $this->load->model('grower/mgrower');
            $getData = $this->mgrower->getMemberDataDetail($varPost['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-MemberID']);
            $MemberData = $getData['data'];

            if ($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoOfVisitInput']['name'] != '') {
                $ProvinceID = $MemberData['ProvinceID'];
                $MemberDisplayID = $varPost['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-MemberDisplayID'];
                if ($this->post('User') == 'Farmer') {
                    $upload = $this->awsfileupload->upload($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoOfVisitInput']['tmp_name'],$this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoOfVisitInput']['name'], AWSS3_FARMER_PLOT_PATH, 'images');
                } elseif ($this->post('User') == 'SME') {
                    $upload = $this->awsfileupload->upload($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoOfVisitInput']['tmp_name'],$this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoOfVisitInput']['name'], AWSS3_SME_PLOT_PATH, 'images');
                }else{
                    $upload = $this->awsfileupload->upload($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoOfVisitInput']['tmp_name'],$this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoOfVisitInput']['name'], AWSS3_FARMER_PLOT_PATH, 'images');
                }
                
                if ($upload['success'] == true) {
                    if($this->awsfileupload->doesObjectExist($this->post('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOfVisitOld')) == true) {
                        $this->awsfileupload->delete($this->post('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOfVisitOld'));
                    }else{
                        delete_file($this->post('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOfVisitOld'));
                    }
                    $prosesUpdate = $this->mplot_survey->updatePlotImage($this->post('User'),$_POST["MemberID"],$upload['filenamepath']);
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
    }

    public function photo_fire_post(){
        $this->load->library('awsfileupload');
        if($this->post('opsiDisplay') == "insert"){
            if ($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoofFireInput']['name'] != '') {
                $gambar = 'temp/'.$this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoofFireInput']['name'];
                $fileupload['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoofFireInput'] = $this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoofFireInput'];
                if ($this->post('User') == 'Farmer') {
                    $path = 'images/plot_fire/';
                } elseif ($this->post('User') == 'SME') {
                    $path = 'images/plot_fire_sme/';
                }else{
                    $path = 'images/plot_fire/';
                }
                $upload = move_upload($fileupload, $path . $gambar);
                if (isset($upload['upload_data'])) {
                    $result['success'] = true;
                    $result['file']     = base_url().$path.$gambar;
                    $result['filepath'] = $path . $gambar;
                    $this->response($result, 200);
                } else {
                    echo  'false'; exit;
                }
            }
        }

        if($this->post('opsiDisplay') == "update"){
            $varPost = $this->post();

            //get member data
            $this->load->model('grower/mgrower');
            $getData = $this->mgrower->getMemberDataDetail($varPost['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-MemberID']);
            $MemberData = $getData['data'];

            if ($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoofFireInput']['name'] != '') {
                $ProvinceID = $MemberData['ProvinceID'];
                $MemberDisplayID = $varPost['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-MemberDisplayID'];
                if ($this->post('User') == 'Farmer') {
                    $upload = $this->awsfileupload->upload($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoofFireInput']['tmp_name'],$this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoofFireInput']['name'], AWSS3_FARMER_PLOT_PATH, 'images');
                } elseif ($this->post('User') == 'SME') {
                    $upload = $this->awsfileupload->upload($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoofFireInput']['tmp_name'],$this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoofFireInput']['name'], AWSS3_SME_PLOT_PATH, 'images');
                }else{
                    $upload = $this->awsfileupload->upload($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoofFireInput']['tmp_name'],$this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoofFireInput']['name'], AWSS3_FARMER_PLOT_PATH, 'images');
                }
                
                if ($upload['success'] == true) {
                    if($this->awsfileupload->doesObjectExist($this->post('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoofFireOld')) == true) {
                        $this->awsfileupload->delete($this->post('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoofFireOld'));
                    }else{
                        delete_file($this->post('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoofFireOld'));
                    }
                    $prosesUpdate = $this->mplot_survey->updatePlotImageFire($this->post('User'),$_POST["MemberID"],$upload['filenamepath']);
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
    }

    public function photo_soil_erotion_post(){
        $this->load->library('awsfileupload');
        if($this->post('opsiDisplay') == "insert"){
            if ($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoSoilErotionInput']['name'] != '') {
                $gambar = 'temp/'.$this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoSoilErotionInput']['name'];
                $fileupload['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoSoilErotionInput'] = $this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoSoilErotionInput'];
                if ($this->post('User') == 'Farmer') {
                    $path = 'images/plot_soil_erotion/';
                } elseif ($this->post('User') == 'SME') {
                    $path = 'images/plot_soil_erotion_sme/';
                }else{
                    $path = 'images/plot_soil_erotion/';
                }
                $upload = move_upload($fileupload, $path . $gambar);
                if (isset($upload['upload_data'])) {
                    $result['success'] = true;
                    $result['file']     = base_url().$path.$gambar;
                    $result['filepath'] = $path . $gambar;
                    $this->response($result, 200);
                } else {
                    echo  'false'; exit;
                }
            }
        }

        if($this->post('opsiDisplay') == "update"){
            $varPost = $this->post();

            //get member data
            $this->load->model('grower/mgrower');
            $getData = $this->mgrower->getMemberDataDetail($varPost['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-MemberID']);
            $MemberData = $getData['data'];

            if ($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoSoilErotionInput']['name'] != '') {
                $ProvinceID = $MemberData['ProvinceID'];
                $MemberDisplayID = $varPost['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-MemberDisplayID'];
                if ($this->post('User') == 'Farmer') {
                    $upload = $this->awsfileupload->upload($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoSoilErotionInput']['tmp_name'],$this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoSoilErotionInput']['name'], AWSS3_FARMER_PLOT_PATH, 'images');
                } elseif ($this->post('User') == 'SME') {
                    $upload = $this->awsfileupload->upload($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoSoilErotionInput']['tmp_name'],$this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoSoilErotionInput']['name'], AWSS3_SME_PLOT_PATH, 'images');
                }else{
                    $upload = $this->awsfileupload->upload($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoSoilErotionInput']['tmp_name'],$this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoSoilErotionInput']['name'], AWSS3_FARMER_PLOT_PATH, 'images');
                }
                
                if ($upload['success'] == true) {
                    if($this->awsfileupload->doesObjectExist($this->post('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoSoilErotionOld')) == true) {
                        $this->awsfileupload->delete($this->post('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoSoilErotionOld'));
                    }else{
                        delete_file($this->post('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoSoilErotionOld'));
                    }
                    $prosesUpdate = $this->mplot_survey->updatePlotImageSoilErotion($this->post('User'),$_POST["MemberID"],$upload['filenamepath']);
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
    }    

    public function photo_soil_accumulation_post(){
        $this->load->library('awsfileupload');
        if($this->post('opsiDisplay') == "insert"){
            if ($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoSoilAccumulationInput']['name'] != '') {
                $gambar = 'temp/'.$this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoSoilAccumulationInput']['name'];
                $fileupload['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoSoilAccumulationInput'] = $this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoSoilAccumulationInput'];
                if ($this->post('User') == 'Farmer') {
                    $path = 'images/plot_soil_acc/';
                } elseif ($this->post('User') == 'SME') {
                    $path = 'images/plot_soil_acc_sme/';
                }else{
                    $path = 'images/plot_soil_acc/';
                }
                $upload = move_upload($fileupload, $path . $gambar);
                if (isset($upload['upload_data'])) {
                    $result['success'] = true;
                    $result['file']     = base_url().$path.$gambar;
                    $result['filepath'] = $path . $gambar;
                    $this->response($result, 200);
                } else {
                    echo  'false'; exit;
                }
            }
        }

        if($this->post('opsiDisplay') == "update"){
            $varPost = $this->post();

            //get member data
            $this->load->model('grower/mgrower');
            $getData = $this->mgrower->getMemberDataDetail($varPost['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-MemberID']);
            $MemberData = $getData['data'];

            if ($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoSoilAccumulationInput']['name'] != '') {
                $ProvinceID = $MemberData['ProvinceID'];
                $MemberDisplayID = $varPost['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-MemberDisplayID'];
                if ($this->post('User') == 'Farmer') {
                    $upload = $this->awsfileupload->upload($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoSoilAccumulationInput']['tmp_name'],$this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoSoilAccumulationInput']['name'], AWSS3_FARMER_PLOT_PATH, 'images');
                } elseif ($this->post('User') == 'SME') {
                    $upload = $this->awsfileupload->upload($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoSoilAccumulationInput']['tmp_name'],$this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoSoilAccumulationInput']['name'], AWSS3_SME_PLOT_PATH, 'images');
                }else{
                    $upload = $this->awsfileupload->upload($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoSoilAccumulationInput']['tmp_name'],$this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoSoilAccumulationInput']['name'], AWSS3_FARMER_PLOT_PATH, 'images');
                }
                
                if ($upload['success'] == true) {
                    if($this->awsfileupload->doesObjectExist($this->post('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoSoilAccumulationOld')) == true) {
                        $this->awsfileupload->delete($this->post('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoSoilAccumulationOld'));
                    }else{
                        delete_file($this->post('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoSoilAccumulationOld'));
                    }
                    $prosesUpdate = $this->mplot_survey->updatePlotImageSoilAccumulation($this->post('User'),$_POST["MemberID"],$upload['filenamepath']);
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
    }    

    public function photo_document_ownership_post(){
        $this->load->library('awsfileupload');
        if($this->post('opsiDisplay') == "insert"){
            if ($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoOwnershipDocInput']['name'] != '') {
                $gambar = 'temp/'.$this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoOwnershipDocInput']['name'];
                $fileupload['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoOwnershipDocInput'] = $this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoOwnershipDocInput'];
                if ($this->post('User') == 'Farmer') {
                    $path = 'images/plot_docs/';
                } elseif ($this->post('User') == 'SME') {
                    $path = 'images/plot_docs_sme/';
                }else{
                    $path = 'images/plot_docs/';
                }
                $upload = move_upload($fileupload, $path . $gambar);
                if (isset($upload['upload_data'])) {
                    $result['success'] = true;
                    $result['file']     = base_url().$path.$gambar;
                    $result['filepath'] = $path . $gambar;
                    $this->response($result, 200);
                } else {
                    echo  'false'; exit;
                }
            }
        }

        if($this->post('opsiDisplay') == "update"){
            $varPost = $this->post();

            //get member data
            $this->load->model('grower/mgrower');
            $getData = $this->mgrower->getMemberDataDetail($varPost['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-MemberID']);
            $MemberData = $getData['data'];

            if ($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoOwnershipDocInput']['name'] != '') {
                $ProvinceID = $MemberData['ProvinceID'];
                $MemberDisplayID = $varPost['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-MemberDisplayID'];
                if ($this->post('User') == 'Farmer') {
                    $upload = $this->awsfileupload->upload($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoOwnershipDocInput']['tmp_name'],$this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoOwnershipDocInput']['name'], AWSS3_FARMER_PLOT_PATH, 'images');
                } elseif ($this->post('User') == 'SME') {
                    $upload = $this->awsfileupload->upload($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoOwnershipDocInput']['tmp_name'],$this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoOwnershipDocInput']['name'], AWSS3_SME_PLOT_PATH, 'images');
                }else{
                    $upload = $this->awsfileupload->upload($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoOwnershipDocInput']['tmp_name'],$this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoOwnershipDocInput']['name'], AWSS3_FARMER_PLOT_PATH, 'images');
                }
                
                if ($upload['success'] == true) {
                    if($this->awsfileupload->doesObjectExist($this->post('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOwnershipDocOld')) == true) {
                        $this->awsfileupload->delete($this->post('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOwnershipDocOld'));
                    }else{
                        delete_file($this->post('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOwnershipDocOld'));
                    }
                    $prosesUpdate = $this->mplot_survey->updatePlotImageOwnerDoc($this->post('User'),$_POST["MemberID"],$upload['filenamepath']);
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
    }

    public function photo_farm_post(){
        $this->load->library('awsfileupload');
        if($this->post('opsiDisplay') == "insert"){
            if ($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoOfFarmInput']['name'] != '') {
                $gambar = 'temp/'.$this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoOfFarmInput']['name'];
                $fileupload['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoOfFarmInput'] = $this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoOfFarmInput'];
                if ($this->post('User') == 'Farmer') {
                    $path = 'images/plot_farm/';
                } elseif ($this->post('User') == 'SME') {
                    $path = 'images/plot_farm_sme/';
                }else{
                    $path = 'images/plot_farm/';
                }
                $upload = move_upload($fileupload, $path . $gambar);
                if (isset($upload['upload_data'])) {
                    $result['success'] = true;
                    $result['file']     = base_url().$path.$gambar;
                    $result['filepath'] = $path.$gambar;
                    $this->response($result, 200);
                } else {
                    echo 'false'; exit;
                }
            }
        }

        if($this->post('opsiDisplay') == "update"){
            $varPost = $this->post();

            //get member data
            $this->load->model('grower/mgrower');
            $getData = $this->mgrower->getMemberDataDetail($varPost['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-MemberID']);
            $MemberData = $getData['data'];

            if ($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoOfFarmInput']['name'] != '') {
                if ($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoOfFarmInput']['name'] != '') {                    
                    if ($this->post('User') == 'Farmer') {
                        $upload = $this->awsfileupload->upload($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoOfFarmInput']['tmp_name'],$this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoOfFarmInput']['name'], AWSS3_FARMER_PLOT_PATH, 'images');
                    } elseif ($this->post('User') == 'SME') {
                        $upload = $this->awsfileupload->upload($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoOfFarmInput']['tmp_name'],$this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoOfVisitInput']['name'], AWSS3_SME_PLOT_PATH, 'images');
                    }else{
                        $upload = $this->awsfileupload->upload($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoOfFarmInput']['tmp_name'],$this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-PhotoOfFarmInput']['name'], AWSS3_FARMER_PLOT_PATH, 'images');
                    }
                    
                    if ($upload['success'] == true) {
                        if($this->awsfileupload->doesObjectExist($this->post('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOfFarmOld')) == true) {
                            $this->awsfileupload->delete($this->post('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOfFarmOld'));
                        }else{
                            delete_file($this->post('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PhotoOfFarmOld'));
                        }
                        $prosesUpdate = $this->mplot_survey->updateFarmImage($this->post('User'),$_POST["MemberID"],$upload['filenamepath']);
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
        }
    }

    public function delivery_by_post(){
        $this->load->library('awsfileupload');
        if($this->post('opsiDisplay') == "insert"){
            if ($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-DeliveryByInput']['name'] != '') {
                $gambar = 'temp/'.$this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-DeliveryByInput']['name'];
                $fileupload['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-DeliveryByInput'] = $this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-DeliveryByInput'];
                if ($this->post('User') == 'Farmer') {
                    $path = 'images/delivery_by/';
                } elseif ($this->post('User') == 'SME') {
                    $path = 'images/delivery_by_sme/';
                }else{
                    $path = 'images/delivery_by/';
                }
                $upload = move_upload($fileupload, $path . $gambar);
                if (isset($upload['upload_data'])) {
                    $result['success'] = true;
                    $result['file']     = base_url().$path.$gambar;
                    $result['filepath'] = $path.$gambar;
                    $this->response($result, 200);
                } else {
                    echo 'false'; exit;
                }
            }
        }

        if($this->post('opsiDisplay') == "update"){
            $varPost = $this->post();

            //get member data
            $this->load->model('grower/mgrower');
            $getData = $this->mgrower->getMemberDataDetail($varPost['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-MemberID']);
            $MemberData = $getData['data'];

            if ($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-DeliveryByInput']['name'] != '') {
                if ($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-DeliveryByInput']['name'] != '') {                    
                    if ($this->post('User') == 'Farmer') {
                        $upload = $this->awsfileupload->upload($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-DeliveryByInput']['tmp_name'],$this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-DeliveryByInput']['name'], AWSS3_FARMER_PLOT_PATH, 'images');
                    } elseif ($this->post('User') == 'SME') {
                        $upload = $this->awsfileupload->upload($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-DeliveryByInput']['tmp_name'],$this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-DeliveryByInput']['name'], AWSS3_SME_PLOT_PATH, 'images');
                    }else{
                        $upload = $this->awsfileupload->upload($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-DeliveryByInput']['tmp_name'],$this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-DeliveryByInput']['name'], AWSS3_FARMER_PLOT_PATH, 'images');
                    }
                    
                    if ($upload['success'] == true) {
                        if($this->awsfileupload->doesObjectExist($this->post('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DeliveryByOld')) == true) {
                            $this->awsfileupload->delete($this->post('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DeliveryByOld'));
                        }else{
                            delete_file($this->post('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-DeliveryByOld'));
                        }
                        $prosesUpdate = $this->mplot_survey->updateDeliveryImage($this->post('User'),$_POST["MemberID"],$upload['filenamepath']);
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
        }
    }

    public function document_written_post(){
        $this->load->library('awsfileupload');
        if($this->post('opsiDisplay') == "insert"){
            if ($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurveyCertification-Form-DocumentWrittenInput']['name'] != '') {
                $gambar = $this->file['Koltiva_view_PlotSurvey_WinFormPlotSurveyCertification-Form-DocumentWrittenInput']['name'];
                $fileupload['Koltiva_view_PlotSurvey_WinFormPlotSurveyCertification-Form-DocumentWrittenInput'] = $this->file['Koltiva_view_PlotSurvey_WinFormPlotSurveyCertification-Form-DocumentWrittenInput'];
                if ($this->post('User') == 'Farmer') {
                    $path = 'images/delivery_by/';
                } elseif ($this->post('User') == 'SME') {
                    $path = 'images/delivery_by_sme/';
                }else{
                    $path = 'images/delivery_by/';
                }

                //cek folder propinsi itu sudah ada belum
                if (!file_exists('images/delivery_by')) {
                    mkdir('images/delivery_by', 0777, true);
                }

                $upload = move_upload($fileupload, $path . $gambar);
                if (isset($upload['upload_data'])) {
                    $result['success'] = true;
                    $result['file']     = base_url().$path.$gambar;
                    $result['filepath'] = $path.$gambar;
                    $this->response($result, 200);
                } else {
                    $result['success'] = false;
                    $result['message'] = $upload["error"];
                    $this->response($result, 200);
                }
            }
        }

        if($this->post('opsiDisplay') == "update"){
            $varPost = $this->post();

            //get member data
            $this->load->model('grower/mgrower');
            $getData = $this->mgrower->getMemberDataDetail($varPost['Koltiva_view_PlotSurvey_WinFormPlotSurveyCertification-Form-MemberID']);
            $MemberData = $getData['data'];

            if ($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurveyCertification-Form-DocumentWrittenInput']['name'] != '') {
                if ($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurveyCertification-Form-DocumentWrittenInput']['name'] != '') {                    
                    if ($this->post('User') == 'Farmer') {
                        $upload = $this->awsfileupload->upload($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurveyCertification-Form-DocumentWrittenInput']['tmp_name'],$this->file['Koltiva_view_PlotSurvey_WinFormPlotSurveyCertification-Form-DocumentWrittenInput']['name'], AWSS3_FARMER_SURVEY_CERT_PATH, 'images');
                    } elseif ($this->post('User') == 'SME') {
                        $upload = $this->awsfileupload->upload($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurveyCertification-Form-DocumentWrittenInput']['tmp_name'],$this->file['Koltiva_view_PlotSurvey_WinFormPlotSurveyCertification-Form-DocumentWrittenInput']['name'], AWSS3_SME_PLOT_PATH, 'images');
                    }else{
                        $upload = $this->awsfileupload->upload($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurveyCertification-Form-DocumentWrittenInput']['tmp_name'],$this->file['Koltiva_view_PlotSurvey_WinFormPlotSurveyCertification-Form-DocumentWrittenInput']['name'], AWSS3_FARMER_SURVEY_CERT_PATH, 'images');
                    }
                    
                    if ($upload['success'] == true) {
                        if($this->awsfileupload->doesObjectExist($this->post('Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-DocumentWrittenOld')) == true) {
                            $this->awsfileupload->delete($this->post('Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-DocumentWrittenOld'));
                        }else{
                            delete_file($this->post('Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-DocumentWrittenOld'));
                        }
                        $prosesUpdate = $this->mplot_survey->updateDocumentWritten($this->post('User'),$_POST["SurveyID"],$upload['filenamepath']);
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
        }
    }

    public function farm_photo_post(){
        $this->load->library('awsfileupload');
        if($this->post('opsiDisplay') == "insert"){
            if ($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurveyCertification-Form-FarmPhotoInput']['name'] != '') {
                $gambar = $this->file['Koltiva_view_PlotSurvey_WinFormPlotSurveyCertification-Form-FarmPhotoInput']['name'];
                $fileupload['Koltiva_view_PlotSurvey_WinFormPlotSurveyCertification-Form-FarmPhotoInput'] = $this->file['Koltiva_view_PlotSurvey_WinFormPlotSurveyCertification-Form-FarmPhotoInput'];
                
                $path = 'images/member/';
                
                $upload = move_upload($fileupload, $path . $gambar);
                if (isset($upload['upload_data'])) {
                    $result['success'] = true;
                    $result['file']     = base_url().$path.$gambar;
                    $result['filepath'] = $path.$gambar;
                    $this->response($result, 200);
                } else {
                    $result['success'] = false;
                    $result['message'] = $upload["error"];
                    $this->response($result, 200);
                }
            }
        }

        if($this->post('opsiDisplay') == "update"){
            $varPost = $this->post();

            //get member data
            $this->load->model('grower/mgrower');
            $getData = $this->mgrower->getMemberDataDetail($varPost['Koltiva_view_PlotSurvey_WinFormPlotSurveyCertification-Form-MemberID']);
            $MemberData = $getData['data'];

            if ($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurveyCertification-Form-FarmPhotoInput']['name'] != '') {
                if ($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurveyCertification-Form-FarmPhotoInput']['name'] != '') {  
                    
                    $upload = $this->awsfileupload->upload($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurveyCertification-Form-FarmPhotoInput']['tmp_name'],$this->file['Koltiva_view_PlotSurvey_WinFormPlotSurveyCertification-Form-FarmPhotoInput']['name'], AWSS3_FARMER_PLOT_PATH, 'images');
                    
                    
                    if ($upload['success'] == true) {
                        if($this->awsfileupload->doesObjectExist($this->post('Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-FarmPhotoOld')) == true) {
                            $this->awsfileupload->delete($this->post('Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-FarmPhotoOld'));
                        }else{
                            delete_file($this->post('Koltiva.view.PlotSurvey.WinFormPlotSurveyCertification-Form-FarmPhotoOld'));
                        }
                        $prosesUpdate = $this->mplot_survey->updateFarmPhoto($this->post('User'),$_POST["SurveyID"],$upload['filenamepath']);
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
        }
    }
    
    public function farm_photo_mill_post() {
        $this->load->library('awsfileupload');
        if($this->post('opsiDisplay') == "insert"){
            if ($this->file['Koltiva_view_PlotSurvey_WinFormPlotStatus-Form-FarmPhotoInput']['name'] != '') {
                $gambar = 'temp/'.$this->file['Koltiva_view_PlotSurvey_WinFormPlotStatus-Form-FarmPhotoInput']['name'];
                $fileupload['Koltiva_view_PlotSurvey_WinFormPlotStatus-Form-FarmPhotoInput'] = $this->file['Koltiva_view_PlotSurvey_WinFormPlotStatus-Form-FarmPhotoInput'];
                $path = 'images/farm_photo_mill/';
                if (!is_dir($path)) {
                    mkdir($path);
                }
                if (!is_dir($path . 'temp/')) {
                    mkdir($path . 'temp/');
                }

                $upload = move_upload($fileupload, $path . $gambar);
                if (isset($upload['upload_data'])) {
                    $result['success'] = true;
                    $result['file']    = base_url().'images/farm_photo_mill/'.$gambar;
                    $result['filepath']    = 'images/farm_photo_mill/'.$gambar;
                    $this->response($result, 200);
                } else {
                    echo 'false'; exit;
                }
            }
        }
//        $this->response($this->file, 200);
//        print_r($this->file['Koltiva_view_PlotSurvey_WinFormPlotStatus-Form-FarmPhotoInput']['name']);
        if($this->post('opsiDisplay') == "update"){
            if ($this->file['Koltiva_view_PlotSurvey_WinFormPlotStatus-Form-FarmPhotoInput']['name'] != '') {

                $upload = $this->awsfileupload->upload($this->file['Koltiva_view_PlotSurvey_WinFormPlotStatus-Form-FarmPhotoInput']['tmp_name'],$this->file['Koltiva_view_PlotSurvey_WinFormPlotStatus-Form-FarmPhotoInput']['name'], AWSS3_MILL_PLOT_PATH, 'images');
                if ($upload['success'] == true) {
                    if($this->awsfileupload->doesObjectExist($this->post('Koltiva_view_PlotSurvey_WinFormPlotStatus-Form-FarmPhotoOld')) == true) {
                        $this->awsfileupload->delete($this->post('Koltiva_view_PlotSurvey_WinFormPlotStatus-Form-FarmPhotoOld'));
                    }else{
                        delete_file($this->post('Koltiva_view_PlotSurvey_WinFormPlotStatus-Form-FarmPhotoOld'));
                    }
                    $prosesUpdate = $this->mplot_survey->UpdateImageMill($_POST["MemberID"],$_POST["PlotNr"],$upload['filenamepath']);
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
    }

    public function photo_signature_post(){
        if($this->post('opsiDisplay') == "insert"){
            if ($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-SignatureInput']['name'] != '') {
                $gambar = 'temp/'.$this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-SignatureInput']['name'];
                $fileupload['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-SignatureInput'] = $this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-SignatureInput'];
                if ($this->post('User') == 'Farmer') {
                    $path = 'images/plot_signature/';
                } elseif ($this->post('User') == 'SME') {
                    $path = 'images/plot_signature_sme/';
                }
                $upload = move_upload($fileupload, $path . $gambar);
                if (isset($upload['upload_data'])) {
                    $result['success'] = true;
                    $result['file']    = $gambar;
                    $this->response($result, 200);
                } else {
                    echo  'false'; exit;
                }
            }
        }

        if($this->post('opsiDisplay') == "update"){
            $varPost = $this->post();

            //get member data
            $this->load->model('grower/mgrower');
            $getData = $this->mgrower->getMemberDataDetail($varPost['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-MemberID']);
            $MemberData = $getData['data'];

            if ($this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-SignatureInput']['name'] != '') {
                $ProvinceID = $MemberData['ProvinceID'];
                $MemberDisplayID = $varPost['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-MemberDisplayID'];
                if ($this->post('User') == 'Farmer') {
                    $path = 'images/plot_signature/';
                } elseif ($this->post('User') == 'SME') {
                    $path = 'images/plot_signature_sme/';
                }

                //get ext nya..
                $arrTemp = explode(".", $this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-SignatureInput']['name']);
                $extNya = array_values(array_slice($arrTemp, -1))[0];
                $namaFileGambar = date('YmdHis').".".$extNya;

                //foto dipisah perdirectory ProvinceID, per MemberDisplayID, cek apakah folder tempat nyimpan foto sudah ada
                if(!file_exists($path.$ProvinceID)){
                    mkdir($path.$ProvinceID, 0777, true);
                }
                if(!file_exists($path.$ProvinceID.'/'.$MemberDisplayID)){
                    mkdir($path.$ProvinceID.'/'.$MemberDisplayID, 0777, true);
                }

                $pathGambarTujuan = $ProvinceID.'/'.$MemberDisplayID.'/'.$namaFileGambar;

                $fileupload['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-SignatureInput'] = $this->file['Koltiva_view_PlotSurvey_WinFormPlotSurvey-Form-SignatureInput'];
                $upload = move_upload($fileupload, $path. $pathGambarTujuan);
                if (isset($upload['upload_data'])) {
                    $result['success'] = true;
                    $result['file']    = $pathGambarTujuan;
                    $this->response($result, 200);
                } else {
                    echo  'false'; exit;
                }
            }
        }
    }

    public function grid_ics_log_status_lock_get()
    {
        $MemberID = (int) $this->get('MemberID');
        $PlotNr = (int) $this->get('PlotNr');
        $SurveyNr = (int) $this->get('SurveyNr');
        $data = $this->mplot_survey->GetICSLogLockStatus($MemberID, $PlotNr, $SurveyNr);
        $this->response($data, 200);
    }

    public function grid_ics_log_get()
    {
        $MemberID = (int) $this->get('MemberID');
        $PlotNr = (int) $this->get('PlotNr');
        $SurveyNr = (int) $this->get('SurveyNr');
        $Certification = (int) $this->get('Certification');
        $data          = $this->mplot_survey->GetICSLogMainGrid($MemberID, $PlotNr, $SurveyNr, $Certification);
        $this->response($data, 200);
    }

    public function ics_log_post()
    {
        $varPost   = $this->post();
        $paramPost = array();

        //prep variabel (begin)
        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_PlotSurvey_WinFormICSLog-Form-", '', $key);
            if ($value == "") {
                $value = null;
            }

            $paramPost[$keyNew] = $value;
        }
        //prep variabel (end)
        //echo '<pre>'; print_r($paramPost); exit;

        if ($paramPost['OpsiDisplay'] == 'insert') {
            $proses = $this->mplot_survey->InsertIcsLog($paramPost);
        } elseif ($paramPost['OpsiDisplay'] == 'update') {
            $proses = $this->mplot_survey->UpdateIcsLog($paramPost);
        }
        $this->response($proses, 200);
    }

    public function garden_grid_summary_get(){
        $MemberID = (int) $this->get('MemberID');
        $SurveyNr = (int) $this->get('SurveyNr');
        $PlotNr = (int) $this->get('PlotNr');
        $Certification = (int) $this->get('Certification');
        $ICSDate = $this->get('ICSDate');

        $data = $this->mplot_survey->GetGardenGridSummary($MemberID, $SurveyNr, $PlotNr, $Certification, $ICSDate);
        $this->response($data, 200);
    }

    public function garden_grid_summary_issue_get()
    {
        $DaconID = (int) $this->get('DaconID');
        $data = $this->mplot_survey->GetGardenGridSummaryIssue($DaconID);
        $this->response($data, 200);
    }

    public function survey_certification_open_get(){
        $data = $this->mplot_survey->getSurveyCertFormData($this->get('SurveyID'));
        $this->response($data, 200);
    }

    public function survey_certification_open_polygon_get(){
        $data = $this->mplot_survey->getSurveyCertFormDataPolygon($this->get('SurveyID'));
        $this->response($data, 200);
    }

    public function plot_survey_form_data_get(){
        $data = $this->mplot_survey->getPlotSurveyFormData($this->get('MemberID'),$this->get('PlotNr'),$this->get('SurveyNr'),$this->get('DateCollection'));
        $this->response($data, 200);
    }

    public function grid_main_herbicide_get(){
        $data = $this->mplot_survey->getMainHerbicide($this->get('MemberID'),$this->get('PlotNr'),$this->get('SurveyNr'));
        $this->response($data, 200);
    }

    public function cmb_list_herbicide_get(){
        $data = $this->mplot_survey->CmbListHerbicide();
        $this->response($data, 200);
    }

    public function submit_herbicide_post(){
        $post = $this->post();
        if($post['HerbicideID'] == ''){
            $post = $this->mplot_survey->InsertHerbicide($post);
        }else{
            $post = $this->mplot_survey->UpdateHerbicide($post);
        }
        $this->response($post, 200);
    }

    public function data_herbicide_put($HerbicideID){

        $data["StatusCode"] = "nullified";
        $data["DateUpdated"]    = date("Y-m-d H:i:s");
        $data["LastModifiedBy"] = $_SESSION['userid'];

        $this->db->where("HerbicideID",$HerbicideID);
        $query = $this->db->update("ktv_survey_plot_herbicide",$data);
        if($query){
            $this->response(array("success"=>true), 200);
        }else{
            $this->response(array("success"=>false), 200);
        }
    }

    public function data_insecticide_put($InsecticideID){
        $post = $this->put();

        $data["StatusCode"] = "nullified";
        $data["DateUpdated"]    = date("Y-m-d H:i:s");
        $data["LastModifiedBy"] = $_SESSION['userid'];

        $this->db->where("InsecticideID",$InsecticideID);
        $query = $this->db->update("ktv_survey_plot_insecticide",$data);
        if($query){
            $this->response(array("success"=>true), 200);
        }else{
            $this->response(array("success"=>false), 200);
        }
    }

    public function grid_main_insecticide_get(){
        $data = $this->mplot_survey->getMainInsecticide($this->get('MemberID'),$this->get('PlotNr'),$this->get('SurveyNr'));
        $this->response($data, 200);
    }

    public function cmb_list_insecticide_get(){
        $data = $this->mplot_survey->CmbListInsecticide();
        $this->response($data, 200);
    }

    public function submit_insecticide_post(){
        $post = $this->post();
        if($post['InsecticideID'] == ''){
            $post = $this->mplot_survey->InsertInsecticide($post);
        }else{
            $post = $this->mplot_survey->UpdateInsecticide($post);
        }
        $this->response($post, 200);
    }

    public function grid_main_fungicide_get(){
        $data = $this->mplot_survey->getMainFungicide($this->get('MemberID'),$this->get('PlotNr'),$this->get('SurveyNr'));
        $this->response($data, 200);
    }

    public function cmb_list_fungicide_get(){
        $data = $this->mplot_survey->CmbListFungicide();
        $this->response($data, 200);
    }

    public function submit_fungicide_post(){
        $post = $this->post();
        if($post['FungicideID'] == ''){
            $post = $this->mplot_survey->InsertFungicide($post);
        }else{
            $post = $this->mplot_survey->UpdateFungicide($post);
        }
        $this->response($post, 200);
    }

    public function data_fungicide_put($FungicideID){

        $data["StatusCode"] = "nullified";
        $data["DateUpdated"]    = date("Y-m-d H:i:s");
        $data["LastModifiedBy"] = $_SESSION['userid'];

        $this->db->where("FungicideID",$FungicideID);
        $query = $this->db->update("ktv_survey_plot_fungicide",$data);
        if($query){
            $this->response(array("success"=>true), 200);
        }else{
            $this->response(array("success"=>false), 200);
        }
    }

    public function survey_certification_delete(){
        $MemberID = (int) $this->delete('MemberID');
        $SurveyID = (int) $this->delete('SurveyID');
        $DateCollection = $this->delete('DateCollection');
        $proses = $this->mplot_survey->delete_survey($MemberID,$SurveyID,$DateCollection);
        $this->response($proses, 200);
    }

    public function survey_certification_post(){
        
        $varPost = $this->post();
        $paramPost = array();

        //prep variabel (begin)
        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_PlotSurvey_WinFormPlotSurveyCertification-Form-", '', $key);
            if($value == "") $value = null;
            $paramPost[$keyNew] = $value;
        }

        if($paramPost["SurveyID"] == ""){
            $proses = $this->mplot_survey->insert_survey($paramPost);
        }else{
            $proses = $this->mplot_survey->update_survey($paramPost);
        }
        $this->response($proses, 200);
    }

    public function survey_post(){
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
        // echo "<pre>";
        // print_r($paramPost);
        // die;
        //prep variabel (end)

        //get member data
        $this->load->model('grower/mgrower');
        $getData = $this->mgrower->getMemberDataDetail($paramPost['MemberID']);
        $MemberData = $getData['data'];

        if($paramPost['opsiDisplay'] == 'insert'){

            //cek apakah data sudah ada
            $isExist = $this->mplot_survey->checkIfSurveyExist($paramPost);
            if($isExist == true){
                $proses['success'] = false;
                $proses['message'] = lang('Survey already exist');
                $this->response($proses, 200);
            }

            $proses = $this->mplot_survey->insertPlotSurvey($paramPost,$MemberData);

            // push ke dhis untuk survei baru
            $this->push_dhis($paramPost, true);
        }elseif($paramPost['opsiDisplay'] == 'update'){
            $proses = $this->mplot_survey->updatePlotSurvey($paramPost,$MemberData);

            // push ke dhis untuk survei yang sudah ada
            $this->push_dhis($paramPost);
        }
        $this->response($proses, 200);
    }

    public function ics_log_form_data_get()
    {
        $FarmerID      = (int) $this->get('FarmerID');
        $GardenNr      = (int) $this->get('GardenNr');
        $SurveyNr      = (int) $this->get('SurveyNr');
        $Certification = (int) $this->get('Certification');
        $ICSDate       = $this->get('ICSDate');

        $data = $this->mplot_survey->GetICSLogFormData($FarmerID, $GardenNr, $SurveyNr, $Certification, $ICSDate);
        $this->response($data, 200);
    }

    public function survey_delete(){
        $MemberID = (int) $this->delete('MemberID');
        $PlotNr = (int) $this->delete('PlotNr');
        $SurveyNr = $this->delete('SurveyNr');
        $DateCollection = $this->delete('DateCollection');
        $proses = $this->mplot_survey->deletePlotSurvey($MemberID,$PlotNr,$SurveyNr,$DateCollection);
        $this->response($proses, 200);
    }

    public function grid_plot_polygon_panel_get(){
        $data = $this->mplot_survey->getGridPlotPolygonPanel($this->get('MemberID'),$this->get('CallFrom'));
        $this->response($data, 200);
    }

    public function plot_polygon_get(){
        //$this->response(false, 400);
        $data = array();

        //get polygon area
        $data['area'] = $this->mplot_survey->getPlotPolygonMap($this->get('MemberID'),$this->get('PlotNr'),$this->get('SurveyNr'),$this->get('DateCollection'),$this->get('CallFrom'));
        if($data['area'] == false) return $this->response(false, 400);

        //$data['centerLatLong'] = $this->mplot_survey->getPlotPolygonCenterCoor($this->get('MemberID'),$this->get('PlotNr'),$this->get('SurveyNr'),$this->get('DateCollection'));
        $data['centerLatLong'] = $this->mplot_survey->getPlotPolygonCenterCoorOnlyFirst($this->get('MemberID'),$this->get('PlotNr'),$this->get('SurveyNr'),$this->get('DateCollection'),$this->get('CallFrom'));

        if($data['centerLatLong']['latitude'] == ''){

            $jsondata = json_decode($data['area']);
            for ($j = 0; $j < count($jsondata); $j++) {
                if ($jsondata[$j][0] != "" && $jsondata[$j][1] != "") {
                    $data['centerLatLong'][0] = $jsondata[$j][1];
                    $data['centerLatLong'][1] = $jsondata[$j][0];
                }
            }
        }
        
        $this->load->view('plot_survey/plot_polygon_map', $data);
    }

    public function grid_plot_status_get(){
        $MemberID = (int) $this->get('MemberID');
        $data = $this->mplot_survey->GetGridPlotStatus($MemberID,$this->get('CallFrom'));
        $this->response($data, 200);
    }

    public function plantation_status_form_data_get(){
        $MemberID = (int) $this->get('MemberID');
        $PlotNr = (int) $this->get('PlotNr');
        $CallFrom = $this->get('CallFrom');

        $data = $this->mplot_survey->GetPlantationStatusFormData($MemberID,$PlotNr,$CallFrom);
        $this->response($data, 200);
    }
    
    public function plantation_status_member_get() {
        $MillID = (int) $this->get('MemberID');

        $data = $this->mplot_survey->GetMillDetail($MillID);
        $this->response($data, 200);
    }

    public function plantation_status_post(){
        $varPost = $this->post();

        //prep variabel (begin)
        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_PlotSurvey_WinFormPlotStatus-Form-", '', $key);
            if($value == "") $value = null;

            $paramPost[$keyNew] = $value;
        }
        //prep variabel (end)
        if ($this->post('OpsiDisplay') == 'insert') {
            $proses = $this->mplot_survey->InsertPlantationStatus($paramPost, $this->post('CallFrom'));
        } else {
            $proses = $this->mplot_survey->UpdatePlantationStatus($paramPost, $this->post('CallFrom'));
        }
        $proses['query'] = $this->db->last_query();
        if($proses['success'] == true){
            $this->response($proses, 200);
        }else{
            $this->response($proses, 400);
        }
    }

    public function grid_sme_plot_survey_summary_get() {
        $MemberID = (int) $this->get('MemberID');
        $data = $this->mplot_survey->getGridSmePlotSurveySummary($MemberID);
        $this->response($data, 200);
    }

    public function plot_sme_survey_form_data_get() {
        $data = $this->mplot_survey->getPlotSmeSurveyFormData($this->get('MemberID'),$this->get('PlotNr'),$this->get('SurveyNr'),$this->get('DateCollection'));
        $this->response($data, 200);
    }

    public function sme_survey_post() {
        $varPost = $this->post();

        //prep variabel (begin)
        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_PlotSurvey_WinFormSmePlotSurvey-Form-", '', $key);
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
            $isExist = $this->mplot_survey->checkIfSurveyExistSme($paramPost);
            if($isExist == true){
                $proses['success'] = false;
                $proses['message'] = lang('Survey already exist');
                $this->response($proses, 200);
            }

            $proses = $this->mplot_survey->insertSmePlotSurvey($paramPost,$MemberData);
        }elseif($paramPost['opsiDisplay'] == 'update'){
            $proses = $this->mplot_survey->updateSmePlotSurvey($paramPost,$MemberData);
        }
        $this->response($proses, 200);
    }

    public function survey_sme_delete() {
        $MemberID = (int) $this->delete('MemberID');
        $PlotNr = (int) $this->delete('PlotNr');
        $SurveyNr = $this->delete('SurveyNr');
        $DateCollection = $this->delete('DateCollection');
        $proses = $this->mplot_survey->deletePlotSmeSurvey($MemberID,$PlotNr,$SurveyNr,$DateCollection);
        $this->response($proses, 200);
    }

    /**
     * untuk push garden ke dhis
     * @return [type] [description]
     */
    private function push_dhis($data, $onlyNew = false){
        $this->load->model('mmiddleware');
        $uid = 'nQxNqbkCil1'; // push by program
        $programs = $this->mmiddleware->getAllProgramWithView($uid);
        if (count($programs) > 0) {
            foreach ($programs as $progkeys => $program) {
                $datas = $this->mmiddleware->getDataBy(
                                                $onlyNew, 
                                                $program['uid'], 
                                                array(
                                                    'MemberID' =>$data['MemberID'], 
                                                    'PlotNr'   =>$data['PlotNr'], 
                                                    'SurveyNr' =>$data['SurveyNr']
                                                )
                                            );
                $this->mmiddleware->syncDataPerProgram($datas, $program['uid']);
            }
        }
        
    }

    public function export_plot_status_get(){
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        //Get Data Farmer
        $dataList       = $this->mplot_survey->getExportPlotStatusMill($this->get('MemberID'),$this->get('CallFrom'));

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

            $writer = WriterFactory::create(Type::XLSX); // for XLSX files
            //$writer = WriterFactory::create(Type::CSV); // for CSV files
            //$writer = WriterFactory::create(Type::ODS); // for ODS files

            $writer->setTempFolder('files/tmp/');
            $namaFile = date('YmdHis') . '_export_excel_plot_status_mill.xlsx';
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

            //Sheet Farmer Data
            $writer->getCurrentSheet()->setName('Plot Status Mill');
            $writer->addRowsWithStyle($dataListExcel, $styleData);

            $writer->close();
            $this->response(array('success' => true, 'filenya' => base_url() . $filePath), 200);
        }

        $this->response($dataList,200);
    }
}