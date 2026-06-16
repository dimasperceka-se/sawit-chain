<?php

/**
 * @Author: nikolius
 * @Date:   2017-10-11 16:47:09
 * @Last Modified by:   nikolius
 * @Last Modified time: 2017-10-11 18:22:04
 */
defined('BASEPATH') OR exit('No direct script access allowed');
/*
ini_set('display_errors',true);
error_reporting(E_ALL);
*/

class Adc_mill extends REST_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('data_adm/madc_mill');
    }

    public function grid_set_by_mill_get(){
        //sort
        $sorting = json_decode($this->get('sort'));
        $sortingField = $sorting[0]->property;
        $sortingDir = $sorting[0]->direction;

        //get param
        $pSearch = array(
            'MillID' => $this->get('MillID'),
            'MillName' => $this->get('MillName'),
            'ProvinceID' => $this->get('ProvinceID'),
            'DistrictID' => $this->get('DistrictID'),
            'SubDistrictID' => $this->get('SubDistrictID'),
            'VillageID' => $this->get('VillageID')
        );

        $data = $this->madc_mill->getGridSetByMill($pSearch, $this->get('start'), $this->get('limit'), $sortingField, $sortingDir);
        $this->response($data, 200);
    }

    public function grid_set_data_control_get(){
        //sort
        $sorting = json_decode($this->get('sort'));
        $sortingField = $sorting[0]->property;
        $sortingDir = $sorting[0]->direction;

        //get param
        $MillIDSelected = json_decode($this->get('MillIDSelected'));

        $data = $this->madc_mill->getGridSetDataControl($MillIDSelected, $this->get('start'), $this->get('limit'), $sortingField, $sortingDir);
        $this->response($data, 200);
    }

    public function data_control_post(){
        $MillIDSelected = json_decode($this->post('MillIDSelected'));
        $PartnerAccess = $this->post('PartnerAccess');
        $arrPartnerAccess = explode("::",$PartnerAccess);

        $proses = $this->madc_mill->updateDataControl($MillIDSelected,$arrPartnerAccess);
        $this->response($proses, 200);
    }

    public function grid_set_data_control_by_region_get(){
        //sort
        $sorting = json_decode($this->get('sort'));
        $sortingField = $sorting[0]->property;
        $sortingDir = $sorting[0]->direction;

        //get param
        $pSearch = array(
            'ProvinceID' => $this->get('ProvinceID'),
            'DistrictID' => $this->get('DistrictID'),
            'SubDistrictID' => $this->get('SubDistrictID'),
            'VillageID' => $this->get('VillageID')
        );

        $data = $this->madc_mill->getGridSetDataControlByRegion($pSearch, $this->get('start'), $this->get('limit'), $sortingField, $sortingDir);
        $this->response($data, 200);
    }

    public function data_control_by_region_post(){
        $ProvinceID = $this->post('ProvinceID');
        $DistrictID = $this->post('DistrictID');
        $SubDistrictID = $this->post('SubDistrictID');
        $VillageID = $this->post('VillageID');

        $PartnerAccess = $this->post('PartnerAccess');
        $arrPartnerAccess = explode("::",$PartnerAccess);

        $proses = $this->madc_mill->updateDataControlByRegion($ProvinceID,$DistrictID,$SubDistrictID,$VillageID,$arrPartnerAccess);
        $this->response($proses, 200);
    }

    public function grid_mill_not_assign_yet_get(){
        //sort
        $sorting = json_decode($this->get('sort'));
        $sortingField = $sorting[0]->property;
        $sortingDir = $sorting[0]->direction;

        $data = $this->madc_mill->getGridMillNotAssignYet($this->get('start'), $this->get('limit'), $sortingField, $sortingDir);
        $this->response($data, 200);
    }

}
?>