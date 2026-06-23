<?php

/*******************************************
 * Author : aji.alhabsyi@koltiva.com
 * Created On : Tue June 28 2022
 * File : vehicle_type.php
********************************************/
defined('BASEPATH') or exit('No direct script access allowed');

class Vehicle_type extends REST_Controller
{
    public function __construct()
    {
        $this->file = $_FILES;
        parent::__construct();
        $this->load->model('traceability_setting/Mvehicle_type', '_model');
    }

    public function grid_main_get()
    {
        // sort
        $sorting = json_decode($this->get('sort'));
        if (isset($sorting[0]->property)) $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : ''; else $sortingField = null;
        if (isset($sorting[0]->direction)) $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : ''; else $sortingDir = null;
        $start = (int) $this->get('start');
        $limit = (int) $this->get('limit');

        $pSearch = array(
            'textSearch' => filter_var($this->get('textSearch'), FILTER_SANITIZE_STRING),
        );

        $data = $this->_model->GetGridMain($pSearch, $start, $limit, $sortingField, $sortingDir);

        $this->response($data, 200);
    }

    public function vehicle_type_data_get()
    {
        $GHGVehicleTypeID = (int) $this->get('GHGVehicleTypeID');
        $data = $this->_model->GetVehicleTypeData($GHGVehicleTypeID);
        $this->response($data, 200);
    }

    public function vehicle_type_data_post()
    {
        $return = array();
        $varPost = $this->post();
        $paramPost = array();

        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_TraceabilitySetting_VehicleType_MainForm-Form-", '', $key);
            if ($value == "") {
                $value = null;
            }
            $paramPost[$keyNew] = $value;
        }

        if($paramPost['OpsiDisplay'] == 'insert') {
            $proses = $this->_model->InsertVehicleType($paramPost);
        } else {
            $proses = $this->_model->UpdateVehicleType($paramPost);
            $paramPost['GHGVehicleTypeID'] = $proses['GHGVehicleTypeID'];
        }

        if ($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function vehicle_type_data_delete()
    {
        $GHGVehicleTypeID = (int) $this->delete("GHGVehicleTypeID");

        $result = $this->_model->delete_vehicle_type($GHGVehicleTypeID);
        if ($result['success']) {
            $this->response($result, 200);
        } else {
            $this->response($result, 400);
        }
    }
}