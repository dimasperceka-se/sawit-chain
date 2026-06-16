<?php
/******************************************
 *  Author : fikrifauzul@gmail.com   
 *  Created On : 08-01-2020
 *  File : farm_survey_loc.php
 *******************************************/
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Farm_survey_loc extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('data_adm/mfarm_survey_loc');
    }

    public function show_location_post() {
        $return = array();
        $FarmerID = $this->post('FarmerID');

        //get farmer data
        $DataFarmer = $this->mfarm_survey_loc->GetDetailFarmer($FarmerID);
        if($DataFarmer['FarmerID'] == "") {
            $return['success'] = true;
            $return['message'] = lang('Farmer not found');
            $this->response($return, 400);
        }

        //Data koordinates
        $DataKoor = $this->mfarm_survey_loc->GetGridCoor($FarmerID);
        $DataPoly = $this->mfarm_survey_loc->GetGridPolygon('Yes',$FarmerID);

        $return['success'] = true;
        $return['data_farmer'] = $DataFarmer;
        $return['data_koor'] = $DataKoor;
        $return['data_poly'] = $DataPoly;
        $this->response($return, 200);
    }

    public function render_map_post() {
        $ContWidth = (int) $this->post('ContWidth');
        $ContHeight = (int) $this->post('ContHeight');
        $DataView = array();

        $DataView['ContWidth'] = $ContWidth - 17;
        $DataView['ContHeight'] = $ContHeight - 48;
        $this->load->view('data_adm/farm_survey_map', $DataView);
    }

    public function grid_coor_get() {
        $FarmerID = $this->get('FarmerID');

        $data = $this->mfarm_survey_loc->GetGridCoor($FarmerID);

        $return['success'] = true;
        $return['data'] = $data;
        $this->response($return, 200);
    }

    public function grid_polygon_get() {
        $FarmerID = $this->get('FarmerID');

        $data = $this->mfarm_survey_loc->GetGridPolygon('No',$FarmerID);

        $return['success'] = true;
        $return['data'] = $data;
        $this->response($return, 200);
    }

    public function update_coor_form_data_get() {
        $FarmerID = $this->get('FarmerID');
        $GardenNr = (int) $this->get('GardenNr');
        $SurveyNr = (int) $this->get('SurveyNr');

        $data = $this->mfarm_survey_loc->UpdateCoorFormData($FarmerID,$GardenNr,$SurveyNr);
        $this->response($data, 200);
    }

    public function update_coor_post() {
        //Prep Var (Begin)
        $varPost = $this->post();
        $ParamPost = array();
        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_DataAdm_FarmSurveyLoc_WinFormUpdateCoor-Form-", '', $key);
            if ($value == "") {
                $value = null;
            }

            $ParamPost[$keyNew] = $value;
        }
        //Prep Var (End)

        $proses = $this->mfarm_survey_loc->UpdateCoor($ParamPost);
        if($proses['success']) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function update_poly_form_data_get() {
        $FarmerID = $this->get('FarmerID');
        $GardenNr = (int) $this->get('GardenNr');
        $SurveyNr = (int) $this->get('SurveyNr');
        $Revision = (int) $this->get('Revision');

        $data = $this->mfarm_survey_loc->UpdatePolyFormData($FarmerID,$GardenNr,$SurveyNr,$Revision);
        $this->response($data, 200);
    }

    public function update_polygon_status_post() {
        //Prep Var (Begin)
        $varPost = $this->post();
        $ParamPost = array();
        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_DataAdm_FarmSurveyLoc_WinFormUpdateStatusPoly-Form-", '', $key);
            if ($value == "") {
                $value = null;
            }

            $ParamPost[$keyNew] = $value;
        }
        //Prep Var (End)

        $proses = $this->mfarm_survey_loc->UpdatePolyStatus($ParamPost);
        if($proses['success']) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function polygon_delete() {
        $FarmerID = $this->delete('FarmerID');
        $GardenNr = (int) $this->delete('GardenNr');
        $SurveyNr = (int) $this->delete('SurveyNr');
        $Revision = (int) $this->delete('Revision');

        $proses = $this->mfarm_survey_loc->DeletePolygon($FarmerID,$GardenNr,$SurveyNr,$Revision);
        if($proses['success']) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

}