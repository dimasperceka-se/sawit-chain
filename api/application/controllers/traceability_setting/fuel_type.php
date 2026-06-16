<?php

/**************************************
 * Author : aji.alhabsyi@koltiva.com
 * Created On : Thu June 23 2022
 * File : fuel_type.php
 ************************************** */
defined('BASEPATH') or exit('No direct script access allowed');

class Fuel_type extends REST_Controller
{
    public function __construct()
    {
        $this->file = $_FILES;
        parent::__construct();
        $this->load->model('traceability_setting/Mfuel_type', '_model');
    }

    public function grid_main_get()
    {
        // sort
        $sorting = json_decode($this->get('sort'));
        if (isset($sorting[0]->property)) $sortingField = $sorting[0]->property; else $sortingField = null;
        if (isset($sorting[0]->direction)) $sortingDir = $sorting[0]->direction; else $sortingDir = null;
        $start = (int) $this->get('start');
        $limit = (int) $this->get('limit');

        $pSearch = array(
            'textSearch' => filter_var($this->get('textSearch'), FILTER_SANITIZE_STRING),
        );

        $data = $this->_model->GetGridMain($pSearch, $start, $limit, $sortingField, $sortingDir);

        $this->response($data, 200);
    }

    public function fuel_type_data_get()
    {
        $GHGFuelTypeID = (int) $this->get('GHGFuelTypeID');
        $data = $this->_model->GetFuelTypeData($GHGFuelTypeID);
        $this->response($data, 200);
    }

    public function fuel_type_data_post()
    {
        $return = array();
        $varPost = $this->post();
        $paramPost = array();

        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_TraceabilitySetting_FuelType_MainForm-Form-", '', $key);
            if ($value == "") {
                $value = null;
            }
            $paramPost[$keyNew] = $value;
        }

        if($paramPost['OpsiDisplay'] == 'insert') {
            $proses = $this->_model->InsertFuelType($paramPost);
            // $paramPost['GHGFuelTypeID'] = $proses['GHGFuelTypeID'];
        } else {
            $proses = $this->_model->UpdateFuelType($paramPost);
            $paramPost['GHGFuelTypeID'] = $proses['GHGFuelTypeID'];
        }

        if($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function fuel_type_data_delete()
    {
        $GHGFuelTypeID = (int) $this->delete('GHGFuelTypeID');

        $result = $this->_model->delete_fuel_type($GHGFuelTypeID);
        if($result['success']) {
            $this->response($result, 200);
        } else {
            $this->response($result, 400);
        }
    }
}