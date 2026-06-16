<?php
/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Thu May 02 2019
 *  File : tph.php
 *******************************************/
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tph extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->file = $_FILES;
        $this->load->model('tph/mtph');
    }

    public function grid_main_get(){
        //set bahasa
        if($_SESSION['language'] == "Indonesia"){
            $this->load->language('general', 'indonesia');
        }else{
            $this->load->language('general', 'english');
        }

        //sort
        $sorting = json_decode($this->get('sort'));
        $sortingField = $sorting[0]->property;
        $sortingDir = $sorting[0]->direction;

        //get param
        $pSearch = array(
            'ArrFilter' => $this->get('ArrFilter'),
            'CmbFilterProvince' => (int) $this->get('CmbFilterProvince'),
            'CmbFilterDistrict' => (int) $this->get('CmbFilterDistrict'),
            'CmbFilterSubDistrict' => (int) $this->get('CmbFilterSubDistrict'),
            'CmbFilterVillage' => (int) $this->get('CmbFilterVillage'),
            'TextFilterID' => filter_var($this->get('TextFilterID'),FILTER_SANITIZE_STRING),
            'TextFilterName' => filter_var($this->get('TextFilterName'),FILTER_SANITIZE_STRING),
        );

        $data = $this->mtph->GetGridMainTph($pSearch,$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        $this->response($data, 200);
    }

    public function grid_trader_tph_get(){
        //set bahasa
        if($_SESSION['language'] == "Indonesia"){
            $this->load->language('general', 'indonesia');
        }else{
            $this->load->language('general', 'english');
        }

        $data = $this->mtph->GetGridMainTphNew($this->get('MemberID'),$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        $this->response($data, 200);
    }

    public function basic_data_form_get(){
        $CollectpointID = (int) $this->get('CollectpointID');
        $data = $this->mtph->GetTphBasicForm($CollectpointID);
        $this->response($data, 200);
    }

    public function basic_data_form_new_get(){
        $CollectpointID = (int) $this->get('CollectpointID');
        $data = $this->mtph->GetTphBasicFormNew($CollectpointID);
        $this->response($data, 200);
    }

    public function tph_form_post(){
        if($this->post('Koltiva_view_Tph_FormMain-CollectpointID') == ""){
            //insert
            $proses = $this->mtph->InsertTph($this->post());
        }else{
            //update
            $proses = $this->mtph->UpdateTph($this->post());
        }
        $this->response($proses, 200);
    }

    public function tph_form_new_post(){
        if($this->post('Koltiva_view_SME_WinFormCollectingPoint-Form-CollectpointID') == ""){
            //insert
            $proses = $this->mtph->InsertTphNew($this->post());
        }else{
            //update
            $proses = $this->mtph->UpdateTphNew($this->post());
        }
        $this->response($proses, 200);
    }

    public function tph_form_delete(){
        $CollectpointID = (int) $this->delete('CollectpointID');
        $proses = $this->mtph->DeleteTph($CollectpointID);
        $this->response($proses, 200);
    }

    public function collective_member_main_grid_get(){
        $CollectpointID = (int) $this->get('CollectpointID');
        $data = $this->mtph->GetCollectiveMemberMainGrid($CollectpointID);
        $this->response($data, 200);
    }

    public function collective_add_member_post(){
        $CollectpointID = (int) $this->post('CollectpointID');
        $MemberIDSel = filter_var($this->post('MemberIDSel'),FILTER_SANITIZE_STRING);

        $proses = $this->mtph->CollectiveAddMember($CollectpointID,$MemberIDSel);
        if($proses['success'] == true){
            $this->response($proses, 200);
        }else{
            $this->response($proses, 400);
        }
    }

    public function collective_member_delete(){
        $CollectpointID = (int) $this->delete('CollectpointID');
        $MemberID = (int) $this->delete('MemberID');

        $proses = $this->mtph->CollectiveDeleteMember($CollectpointID,$MemberID);
        if($proses['success'] == true){
            $this->response($proses, 200);
        }else{
            $this->response($proses, 400);
        }
    }
}