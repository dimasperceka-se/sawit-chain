<?php

/**
 * @Author: nikolius
 * @Date:   2017-10-10 12:10:42
 * @Last Modified by:   nikolius
 * @Last Modified time: 2017-10-11 15:34:05
 */
defined('BASEPATH') OR exit('No direct script access allowed');
/*
ini_set('display_errors',true);
error_reporting(E_ALL);
*/

class Adc_member extends REST_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('data_adm/madc_member');
    }

    public function grid_set_by_member_get(){
        //sort
        $sorting = json_decode($this->get('sort'));
        $sortingField = $sorting[0]->property;
        $sortingDir = $sorting[0]->direction;

        //get param
        $pSearch = array(
            'MemberID' => $this->get('MemberID'),
            'MemberName' => $this->get('MemberName'),
            'ProvinceID' => $this->get('ProvinceID'),
            'DistrictID' => $this->get('DistrictID'),
            'SubDistrictID' => $this->get('SubDistrictID'),
            'VillageID' => $this->get('VillageID'),
            'MemberType' => $this->get('MemberType')
        );

        $data = $this->madc_member->getGridSetByMember($pSearch, $this->get('start'), $this->get('limit'), $sortingField, $sortingDir);
        $this->response($data, 200);
    }

    public function grid_set_data_control_get(){
        //sort
        $sorting = json_decode($this->get('sort'));
        $sortingField = $sorting[0]->property;
        $sortingDir = $sorting[0]->direction;

        //get param
        $MemberIDSelected = json_decode($this->get('MemberIDSelected'));

        $data = $this->madc_member->getGridSetDataControl($MemberIDSelected, $this->get('start'), $this->get('limit'), $sortingField, $sortingDir);
        $this->response($data, 200);
    }

    public function data_control_post(){
        $MemberIDSelected = json_decode($this->post('MemberIDSelected'));
        $PartnerAccess = $this->post('PartnerAccess');
        $arrPartnerAccess = explode("::",$PartnerAccess);

        $proses = $this->madc_member->updateDataControl($MemberIDSelected,$arrPartnerAccess);
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
            'VillageID' => $this->get('VillageID'),
            'MemberType' => $this->get('MemberType')
        );

        $data = $this->madc_member->getGridSetDataControlByRegion($pSearch, $this->get('start'), $this->get('limit'), $sortingField, $sortingDir);
        $this->response($data, 200);
    }

    public function data_control_by_region_post(){
        $ProvinceID = $this->post('ProvinceID');
        $DistrictID = $this->post('DistrictID');
        $SubDistrictID = $this->post('SubDistrictID');
        $VillageID = $this->post('VillageID');
        $MemberType = $this->post('MemberType');

        $PartnerAccess = $this->post('PartnerAccess');
        $arrPartnerAccess = explode("::",$PartnerAccess);

        $proses = $this->madc_member->updateDataControlByRegion($ProvinceID,$DistrictID,$SubDistrictID,$VillageID,$MemberType,$arrPartnerAccess);
        $this->response($proses, 200);
    }

    public function grid_member_not_assign_yet_get(){
        //sort
        $sorting = json_decode($this->get('sort'));
        $sortingField = $sorting[0]->property;
        $sortingDir = $sorting[0]->direction;

        $data = $this->madc_member->getGridMemberNotAssignYet($this->get('start'), $this->get('limit'), $sortingField, $sortingDir);
        $this->response($data, 200);
    }

}

?>