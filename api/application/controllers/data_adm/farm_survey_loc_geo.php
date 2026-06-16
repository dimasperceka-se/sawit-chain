<?php
/******************************************
 *  Author : sofyan.salim@koltiva.com 
 *  Created On : 08-11-2021
 *  File : farm_survey_loc.php
 *******************************************/
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Farm_survey_loc_geo extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('data_adm/mfarm_survey_loc_geo');
    }


    public function render_map_post() {
        $ContWidth = (int) $this->post('ContWidth');
        $ContHeight = (int) $this->post('ContHeight');
        $DataView = array();

        $DataView['ContWidth'] = $ContWidth - 17;
        $DataView['ContHeight'] = $ContHeight - 48;
        $this->load->view('data_adm/farm_survey_map_geo', $DataView);
    }

    public function show_location_post() {
        $return = array();
        $FarmerID = $this->post('FarmerID');

        //get farmer data
        $DataFarmer = $this->mfarm_survey_loc_geo->GetDetailFarmer($FarmerID);

        if($DataFarmer['FarmerID'] == "") {
            $return['success'] = true;
            $return['message'] = lang('Farmer not found');
            $this->response($return, 400);
        }

        //Data koordinates
        $DataKoor = $this->mfarm_survey_loc_geo->GetGridCoor($FarmerID);
        $DataPoly = $this->mfarm_survey_loc_geo->GetGridPolygonM8('Yes',$FarmerID);


        $return['success'] = true;
        $return['data_farmer'] = $DataFarmer;
        $return['data_koor'] = $DataKoor;
        $return['data_poly'] = $DataPoly;
        // echo "<pre>"; print_r($return); echo "</pre>"; exit;

        $this->response($return, 200);
    }

    public function grid_coor_get() {
        $FarmerID = $this->get('FarmerID');

        $data = $this->mfarm_survey_loc_geo->GetGridCoor($FarmerID);

        $return['success'] = true;
        $return['data'] = $data;
        $this->response($return, 200);
    }

    public function grid_polygon_get() {
        $FarmerID = $this->get('FarmerID');

        $data = $this->mfarm_survey_loc_geo->GetGridPolygonM8('No',$FarmerID);

        $return['success'] = true;
        $return['data'] = $data;
        $this->response($return, 200);
    }

}