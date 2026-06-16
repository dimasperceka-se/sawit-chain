<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kpi_general extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->file = $_FILES;
        $this->load->model('dboard/mkpi_general');
    }

    public function combo_filter_year_kpi_target_get() {
        $data = $this->mkpi_general->GetComboFilterYearKpiGeneral();
        $this->response($data, 200);
    }

    public function kpi_target_general_main_grid_get() {
        $FilterYear = (int) $this->get('FilterYear');
        $FilterCountry = $this->get('FilterCountry');
        $FilterProvince = (int) $this->get('FilterProvince');
        $FilterPartnerID = (int) $this->get('FilterPartnerID');
        $FilterDistrictID = (int) $this->get('FilterDistrictID');

        $data = $this->mkpi_general->GetKpiTargetGeneralMainGrid($FilterYear,$FilterCountry,$FilterProvince,$FilterPartnerID,$FilterDistrictID);
        $this->response($data, 200);
    }

    public function kpi_target_sawit_terampil_main_grid_get() {
        $FilterYear = (int) $this->get('FilterYear');

        $data = $this->mkpi_general->GetKpiTargetSawitTerampilMainGrid($FilterYear);
        $this->response($data, 200);
    }

    public function kpi_target_post() {
        $return = array();
        $varPost = $this->post();
        $paramPost = array();

        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Dboard_WinFormInputKpiTargetGeneral-Form-", '', $key);
            if ($value == "") {
                $value = null;
            }

            $paramPost[$keyNew] = $value;
        }


        $proses = $this->mkpi_general->InputKpiTarget($paramPost);
        if($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function kpi_target_sawit_terampil_post() {
        $return = array();
        $varPost = $this->post();
        $paramPost = array();

        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Dboard_WinFormInputKpiTargetSawitTerampil-Form-", '', $key);
            if ($value == "") {
                $value = null;
            }

            $paramPost[$keyNew] = $value;
        }


        $proses = $this->mkpi_general->InputKpiTargetSawitTerampil($paramPost);
        if($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function target_sawit_terampil_form_get(){
        $data = $this->mkpi_general->getTargetSawitTerampilForm($this->get('TargetID'));
        $this->response($data, 200);
    }

}