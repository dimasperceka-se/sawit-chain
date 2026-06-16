<?php
/**
 * @Author: nikolius
 * @Date:   2017-08-10 11:41:32
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Document_survey extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->file = $_FILES;
        $this->load->model('document_survey/mdocument_survey');
    }

    public function grid_document_survey_panel_get(){
        //set bahasa
        if($_SESSION['language'] == "Indonesia"){
            $this->load->language('general', 'indonesia');
        }else{
            $this->load->language('general', 'english');
        }

        $data = $this->mdocument_survey->getGridDocumentSurvey($this->get('MemberID'));
        $this->response($data, 200);
    }

    public function cetak_proj_background_get(){
        $this->load->helper('date');
        if($_SESSION['language'] == "Indonesia"){
            $this->load->language('general', 'indonesia');
        }else{
            $this->load->language('general', 'english');
        }
        $data = array();
        $MemberID = $this->uri->segment(3);

        //cek Petani milik Petani mana
        $PartnerID = $this->mdocument_survey->CheckFarmerPartnerID($MemberID);

        $this->load->view('document_survey/cetak_proj_bg_template_header');
        switch ($PartnerID) {
            case '7':
                //sinar mas
                $this->load->view('document_survey/cetak_proj_bg_template_sinarmas', $data);
            break;
            case '11':
                //snv
                $this->load->view('document_survey/cetak_proj_bg_template_snv', $data);
            break;
            case '14':
                //Wild Asia
                $this->load->view('document_survey/cetak_proj_bg_template_wildasia', $data);
            break;
            default:
                //unilever
                $this->load->view('document_survey/cetak_proj_bg_template', $data);
            break;
        }
        $this->load->view('document_survey/cetak_proj_bg_template_footer');
    }

    public function cetak_withdrawal_consent_notes_get(){
        $this->load->helper('date');
        $this->load->model('grower/mgrower');
        if($_SESSION['language'] == "Indonesia"){
            $this->load->language('general', 'indonesia');
        }else{
            $this->load->language('general', 'english');
        }
        $data = array();
        $MemberIDs = $this->uri->segment(3);
        $opsiDisplay = $this->uri->segment(4);

        $arrMemberID = explode("::",$MemberIDs);
        //ambil data content
        foreach ($arrMemberID as $key => $MemberID) {
            $MemberData = $this->mgrower->getMemberDataDetail($MemberID);
            $data['member'][$key] = $MemberData['data'];

            $PartnerID = $this->mdocument_survey->CheckFarmerPartnerID($MemberID);
        }

        if($opsiDisplay == "empty"){
            $data['member'][0]['MemberNameTtd'] = '('.lang('Nama').')';
        }

        $this->load->view('document_survey/cetak_withdrawal_consent_notes_template_header');
        switch ($PartnerID) {
            case '7':
                //Sinar Mas
                $this->load->view('document_survey/cetak_withdrawal_consent_notes_template_sinarmas', $data);
            break;
            case '11':
                //SNV
                $this->load->view('document_survey/cetak_withdrawal_consent_notes_template_snv', $data);
            break;
            case '14':
                //Wild Asia
                $this->load->view('document_survey/cetak_withdrawal_consent_notes_template_wildasia', $data);
            break;
            default:
                $this->load->view('document_survey/cetak_withdrawal_consent_notes_template', $data);
            break;
        }
        $this->load->view('document_survey/cetak_withdrawal_consent_notes_template_footer');
    }

    public function view_withdrawal_consent_notes_get(){
        $this->load->model('grower/mgrower');
        $MemberID = (int) $this->uri->segment(3);
        $MemberData = $this->mgrower->getMemberDataDetail($MemberID);

        if($MemberData['data']['WithdrawalConsentSign'] != ""){
            $data['gambarNya'] = "images/withdrawal_consent/".$MemberData['data']['ProvinceID']."/".$MemberData['data']['WithdrawalConsentSign'];
            $this->load->view('document_survey/view_withdrawal_consent_notes_template', $data);
        }else{
            $data['gambarNya'] = "images/no-image-vertikal.jpg";
            $this->load->view('document_survey/view_withdrawal_consent_notes_template', $data);
        }
    }

}
?>