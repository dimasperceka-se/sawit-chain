<?php

/**
 * @Author: nikolius
 * @Date:   2017-05-16 15:52:19
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

//write excel
require_once 'application/third_party/Spout3/Autoloader/autoload.php';


use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
//use Box\Spout\Common\Entity\Style\CellAlignment;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Common\Entity\Style\Border;
use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\ErrorCorrectionLevel;

class Grower extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->file = $_FILES;
        $this->load->model('grower/mgrower');
        $this->load->model('dhis/mdsync');
    }

    public function combo_enum_get() {
        //get param
        $pSearch = array(
            'prov' => $this->get('prov'),
            'kab' => $this->get('kab'),
            'kec' => $this->get('kec')
        );
        $data = $this->mgrower->getComboEnum($pSearch);
        $this->response($data, 200);
    }

    public function combo_certified_get(){
        $data = $this->mgrower->getComboCertified();
        $this->response($data, 200);
    }

    public function combo_dealer_Assign_mobile_get(){
        $data = $this->mgrower->getComboDealer($this->get("uid"));

        $result = array(
            "success" => true,
            "data" => $data,
            "total" => count($data)
        );
        $this->response($result, 200);
    }

    public function combo_dealer_Assign_get(){
        $data = $this->mgrower->getComboDealer();
        $this->response($data, 200);
    }

    public function combo_propinsi_get() {
        $data = $this->mgrower->getComboPropinsi($this->get('FarmerGroupID'));
        $this->response($data, 200);
    }

    public function combo_district_get() {
        $data = $this->mgrower->getComboDistrict($this->get('ProvinceID'));
        $this->response($data, 200);
    }

    public function combo_subdistrict_get() {
        $data = $this->mgrower->getComboSubdistrict($this->get('DistrictID'));
        $this->response($data, 200);
    }

    public function combo_village_get() {
        $data = $this->mgrower->getComboVillage($this->get('SubdistrictID'));
        $this->response($data, 200);
    }

    public function combo_role_member_get() {
        $data = $this->mgrower->getComboRoleMember();
        $this->response($data, 200);
    }

    public function combo_bank_get() {
        $data = $this->mgrower->getComboBank();
        $this->response($data, 200);
    }

    public function grid_main_get() {
        //set bahasa
        if ($_SESSION['language'] == "Indonesia") {
            $this->load->language('general', 'indonesia');
        } else {
            $this->load->language('general', 'english');
        }

        //sort
        $sorting = json_decode($this->get('sort'));
        $sortingField = $sorting[0]->property;
        $sortingDir = $sorting[0]->direction;

        //get param
        $pSearch = array(
            'prov' => $this->get('prov'),
            'kab' => $this->get('kab'),
            'kec' => $this->get('kec'),
            'textSearch' => $this->get('textSearch'),
            'textSearchDesa' => $this->get('textSearchDesa'),
            'roleSearch' => $this->get('roleSearch'),
            'categorySearch' => $this->get('categorySearch'),
            'AdvRowEnumerator' => $this->get('AdvRowEnumerator'),
            'AdvTextEnumerator' => $this->get('AdvTextEnumerator'),
            'AdvRowHandphone' => $this->get('AdvRowHandphone'),
            'AdvTextHandphone' => $this->get('AdvTextHandphone'),
            'AdvRowAge' => $this->get('AdvRowAge'),
            'AdvOpAge' => $this->get('AdvOpAge'),
            'AdvTextAge' => $this->get('AdvTextAge'),
            'AdvRowMaritalStatus' => $this->get('AdvRowMaritalStatus'),
            'AdvMaritalStatus' => $this->get('AdvMaritalStatus'),
            'AdvRowDateCollection' => $this->get('AdvRowDateCollection'),
            'AdvDateCollectionBegin' => $this->get('AdvDateCollectionBegin'),
            'AdvDateCollectionEnd'   => $this->get('AdvDateCollectionEnd'),
            'AdvRowDateCreated' => $this->get('AdvRowDateCreated'),
            'AdvDateCreatedBegin' => $this->get('AdvDateCreatedBegin'),
            'AdvDateCreatedEnd'   => $this->get('AdvDateCreatedEnd'),
            'AdvRowDateSynced' => $this->get('AdvRowDateSynced'),
            'AdvDateSyncedBegin' => $this->get('AdvDateSyncedBegin'),
            'AdvDateSyncedEnd'   => $this->get('AdvDateSyncedEnd'),
            'AdvRowLastUpdatedDate' => $this->get('AdvRowLastUpdatedDate'),
            'AdvLastUpdatedDateBegin' => $this->get('AdvLastUpdatedDateBegin'),
            'AdvLastUpdatedDateEnd'   => $this->get('AdvLastUpdatedDateEnd'),
            'AdvRowEnumerator'       => $this->get('AdvRowEnumerator'),
            'AdvEnumerator'          => $this->get('AdvEnumerator')
        );

        $data = $this->mgrower->getGridMainGrower($pSearch, $this->get('start'), $this->get('limit'), $sortingField, $sortingDir);
        $this->response($data, 200);
    }
    
    public function getPartnerParent_get() {
        $this->load->model('dboard/mpro_supplychain_kpi');
        $PartnerIDStr = $this->mpro_supplychain_kpi->DataGetPartnerIDAllHirarki($_SESSION['PartnerID']);
        $data = $PartnerIDStr;        
        $this->response($data, 200);
    }

    public function grid_mill_main_get() {
        ini_set('max_execution_time', '0');
        //set bahasa
        if ($_SESSION['language'] == "Indonesia") {
            $this->load->language('general', 'indonesia');
        } else {
            $this->load->language('general', 'english');
        }

        //sort
        $sorting = json_decode($this->get('sort'));
        $sortingField = $sorting[0]->property;
        $sortingDir = $sorting[0]->direction;

        //get param
        $this->load->model('dboard/mpro_supplychain_kpi');
        $PartnerIDImp = $this->get('pPartnerSearch');
        $MillFirstLoad = (int) $this->get('pPartnerFirstLoad');
        
        if($MillFirstLoad == 1) {
            $PartnerIDStr = $this->mpro_supplychain_kpi->DataGetPartnerIDAllHirarki($_SESSION['PartnerID']);
        } else {
            //Saring variablenya
            $PartnerIDImp = explode(',',$PartnerIDImp);
            $PartnerIDArr = array();
            for ($i=0; $i < count($PartnerIDImp); $i++) { 
                $PartnerIDArr[] = (int) $PartnerIDImp[$i];
            }
            $PartnerIDStr = implode(',',$PartnerIDArr);
        }
        // Untuk fix masalah merge (20-01-2020)
        $pSearch = array(
            'prov' => $this->get('prov'),
            'kab' => $this->get('kab'),
            'kec' => $this->get('kec'),
            'textSearch' => $this->get('textSearch'),
            'roleSearch' => $this->get('roleSearch'),
            'textSearchDesa' => $this->get('textSearchDesa'),
            'pPartnerSearch' => $PartnerIDStr,
            'pPartnerFirstLoad' => $this->get('pPartnerFirstLoad'),
            'categorySearch' => $this->get('categorySearch'),
            'AdvRowEnumerator' => $this->get('AdvRowEnumerator'),
            'AdvTextEnumerator' => $this->get('AdvTextEnumerator'),
            'AdvRowHandphone' => $this->get('AdvRowHandphone'),
            'AdvTextHandphone' => $this->get('AdvTextHandphone'),
            'AdvRowAge' => $this->get('AdvRowAge'),
            'AdvOpAge' => $this->get('AdvOpAge'),
            'AdvTextAge' => $this->get('AdvTextAge'),
            'AdvRowMaritalStatus' => $this->get('AdvRowMaritalStatus'),
            'AdvMaritalStatus' => $this->get('AdvMaritalStatus'),
            'AdvRowDateCollection' => $this->get('AdvRowDateCollection'),
            'AdvDateCollectionBegin' => $this->get('AdvDateCollectionBegin'),
            'AdvDateCollectionEnd' => $this->get('AdvDateCollectionEnd'),
            'AdvRowEnumerator' => $this->get('AdvRowEnumerator'),
            'AdvEnumerator' => $this->get('AdvEnumerator')
        );

        $data = $this->mgrower->getGridMillMainGrower($pSearch, $this->get('start'), $this->get('limit'), $sortingField, $sortingDir);
        
        $data['PartnerIDStr'] = $PartnerIDStr;
        $this->response($data, 200);
    }

    public function image_member_willingnes_commit_post(){
        $this->load->library('awsfileupload');
        $upload = $this->awsfileupload->upload($this->file['Koltiva_view_Grower_FormMainGrower-WillingnesCommitSignatureInput']['tmp_name'],$this->file['Koltiva_view_Grower_FormMainGrower-WillingnesCommitSignatureInput']['name'], AWSS3_FARMER_SIGNATURE_PATH, 'images');
        if ($upload['success'] == true) {
            if($this->awsfileupload->doesObjectExist($this->post('Koltiva_view_Grower_FormMainGrower-WillingnesCommitSignatureOld')) == true) {
                $this->awsfileupload->delete($this->post('Koltiva_view_Grower_FormMainGrower-WillingnesCommitSignatureOld'));
            }else{
                delete_file($this->post('Koltiva_view_Grower_FormMainGrower-WillingnesCommitSignatureOld'));
            }
            $prosesUpdate = $this->mgrower->updateWillingnessCommit($_POST["MemberID"],$upload['filenamepath']);
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

    public function image_member_willingnes_post(){
        $this->load->library('awsfileupload');
        $upload = $this->awsfileupload->upload($this->file['Koltiva_view_Grower_FormMainGrower-WillingnesSignatureInput']['tmp_name'],$this->file['Koltiva_view_Grower_FormMainGrower-WillingnesSignatureInput']['name'], AWSS3_FARMER_SIGNATURE_PATH, 'images');
        if ($upload['success'] == true) {
            if($this->awsfileupload->doesObjectExist($this->post('Koltiva_view_Grower_FormMainGrower-WillingnesSignatureOld')) == true) {
                $this->awsfileupload->delete($this->post('Koltiva_view_Grower_FormMainGrower-WillingnesSignatureOld'));
            }else{
                delete_file($this->post('Koltiva_view_Grower_FormMainGrower-WillingnesSignatureOld'));
            }
            $prosesUpdate = $this->mgrower->updateWillingness($_POST["MemberID"],$upload['filenamepath']);
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

    public function image_member_post() {
        $this->load->library('awsfileupload');
        if ($this->post('opsiDisplay') == "insert") {
            //ketika insert

            if ($this->file['Koltiva_view_Grower_FormMainGrower-MemberPhotoInput']['name'] != '') {
                $gambar = 'temp/' . $this->file['Koltiva_view_Grower_FormMainGrower-MemberPhotoInput']['name'];
                $fileupload['Koltiva_view_Grower_FormMainGrower-MemberPhotoInput'] = $this->file['Koltiva_view_Grower_FormMainGrower-MemberPhotoInput'];

                //cek folder propinsi itu sudah ada belum
                if (!file_exists('images/member/temp')) {
                    mkdir('images/member/temp', 0777, true);
                }

                $upload = move_upload($fileupload, 'images/member/' . $gambar);
                if (isset($upload['upload_data'])) {
                    $result['success'] = true;
                    $result['file'] = base_url().'/images/member/' . $gambar;
                    $result['filepath']   = '/images/member/' . $gambar;
                    $this->response($result, 200);
                } else {
                    $result['success'] = false;
                    $result['message'] = $upload['error'];
                    $this->response($result, 400);
                }
            }
        }

        if ($this->post('opsiDisplay') == "update") {
            //ketika update

            $upload = $this->awsfileupload->upload($this->file['Koltiva_view_Grower_FormMainGrower-MemberPhotoInput']['tmp_name'],$this->file['Koltiva_view_Grower_FormMainGrower-MemberPhotoInput']['name'], AWSS3_FARMER_PATH, 'images');
            if ($upload['success'] == true) {
                if($this->awsfileupload->doesObjectExist($this->post('Koltiva_view_Grower_FormMainGrower-MemberPhotoOld')) == true) {
                    $this->awsfileupload->delete($this->post('Koltiva_view_Grower_FormMainGrower-MemberPhotoOld'));
                }else{
                    delete_file($this->post('Koltiva_view_Grower_FormMainGrower-MemberPhotoOld'));
                }
                $prosesUpdate = $this->mgrower->updateFotoProfile($_POST["MemberID"],$upload['filenamepath']);
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

    public function image_KTP_post() {
        $this->load->library('awsfileupload');
        if ($this->post('opsiDisplay') == "insert") {
            //ketika insert

            if ($this->file['Koltiva_view_Grower_FormMainGrower-KTPPhotoInput']['name'] != '') {
                $gambar = 'temp/ktp/' . $this->file['Koltiva_view_Grower_FormMainGrower-KTPPhotoInput']['name'];
                $fileupload['Koltiva_view_Grower_FormMainGrower-KTPPhotoInput'] = $this->file['Koltiva_view_Grower_FormMainGrower-KTPPhotoInput'];

                //cek folder propinsi itu sudah ada belum
                if (!file_exists('images/member/temp/ktp')) {
                    mkdir('images/member/temp/ktp', 0777, true);
                }

                $upload = move_upload($fileupload, 'images/member/' . $gambar);
                if (isset($upload['upload_data'])) {
                    $result['success'] = true;
                    $result['file'] = base_url().'/images/member/' . $gambar;
                    $result['filepath']   = '/images/member/' . $gambar;
                    $this->response($result, 200);
                } else {
                    $result['success'] = false;
                    $result['message'] = $upload['error'];
                    $this->response($result, 400);
                }
            }
        }

        if ($this->post('opsiDisplay') == "update") {
            //ketika update

            $upload = $this->awsfileupload->upload($this->file['Koltiva_view_Grower_FormMainGrower-KTPPhotoInput']['tmp_name'],$this->file['Koltiva_view_Grower_FormMainGrower-KTPPhotoInput']['name'], AWSS3_FARMER_KTP_PATH, 'images');
            if ($upload['success'] == true) {
                if($this->awsfileupload->doesObjectExist($this->post('Koltiva_view_Grower_FormMainGrower-KTPPhotoOld')) == true) {
                    $this->awsfileupload->delete($this->post('Koltiva_view_Grower_FormMainGrower-KTPPhotoOld'));
                }else{
                    delete_file($this->post('Koltiva_view_Grower_FormMainGrower-KTPPhotoOld'));
                }
                $prosesUpdate = $this->mgrower->updateFotoKTP($_POST["MemberID"],$upload['filenamepath']);
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

    public function consent_member_post() {
        if ($this->post('opsiDisplay') == "insert") {
            //ketika insert

            if ($this->file['Koltiva_view_Grower_FormMainGrower-LearningContractSignInput']['name'] != '') {
                $gambar = 'temp/' . $this->file['Koltiva_view_Grower_FormMainGrower-LearningContractSignInput']['name'];
                $fileupload['Koltiva_view_Grower_FormMainGrower-LearningContractSignInput'] = $this->file['Koltiva_view_Grower_FormMainGrower-LearningContractSignInput'];

                $upload = move_upload($fileupload, 'images/consent/' . $gambar);
                if (isset($upload['upload_data'])) {
                    $result['success'] = true;
                    $result['file'] = $gambar;
                    $this->response($result, 200);
                } else {
                    echo 'false';
                    exit;
                }
            }
        }

        if ($this->post('opsiDisplay') == "update") {
            //ketika update

            if ($this->file['Koltiva_view_Grower_FormMainGrower-LearningContractSignInput']['name'] != '') {
                $ProvinceID = $this->post('Koltiva_view_Grower_FormMainGrower-Province');

                //get ext nya..
                $arrTemp = explode(".", $this->file['Koltiva_view_Grower_FormMainGrower-LearningContractSignInput']['name']);
                $extNya = array_values(array_slice($arrTemp, -1))[0];

                $gambar = $ProvinceID . '/' . $this->post('Koltiva_view_Grower_FormMainGrower-MemberDisplayID') . '.' . $extNya;

                //cek folder propinsi itu sudah ada belum
                if (!file_exists('images/consent/' . $ProvinceID)) {
                    mkdir('images/consent/' . $ProvinceID, 0777, true);
                }

                //hapus dl file lama
                @unlink('images/consent/' . $gambar);

                $fileupload['Koltiva_view_Grower_FormMainGrower-LearningContractSignInput'] = $this->file['Koltiva_view_Grower_FormMainGrower-LearningContractSignInput'];
                $upload = move_upload($fileupload, 'images/consent/' . $gambar);
                if (isset($upload['upload_data'])) {
                    $result['success'] = true;
                    $result['file'] = $gambar . '?' . rand(1, 100);
                    $this->response($result, 200);
                } else {
                    echo 'false';
                    exit;
                }
            }
        }
    }

    public function consent_member_upload_post() {
        //get data member
        $dataMember = $this->mgrower->getMemberDataDetail($this->post('MemberID'));
        $ProvinceID = $dataMember['data']['ProvinceID'];

        if ($this->post('DocNameID') == "ConNotes") {
            if ($this->file['Koltiva_view_DocumentSurvey_WinFormUploadDoc-Form-ConsentNotesInput']['name'] != '') {
                //get ext nya..
                $arrTemp = explode(".", $this->file['Koltiva_view_DocumentSurvey_WinFormUploadDoc-Form-ConsentNotesInput']['name']);
                $extNya = array_values(array_slice($arrTemp, -1))[0];

                $gambar = $ProvinceID . '/' . $dataMember['data']['MemberDisplayID'] . '.' . $extNya;
                $gambarFileName = $dataMember['data']['MemberDisplayID'] . '.' . $extNya;

                //cek folder propinsi itu sudah ada belum
                if (!file_exists('images/consent/' . $ProvinceID)) {
                    mkdir('images/consent/' . $ProvinceID, 0777, true);
                }

                //hapus dl file lama
                @unlink('images/consent/' . $gambar);

                $fileupload['Koltiva_view_DocumentSurvey_WinFormUploadDoc-Form-ConsentNotesInput'] = $this->file['Koltiva_view_DocumentSurvey_WinFormUploadDoc-Form-ConsentNotesInput'];
                $upload = move_upload($fileupload, 'images/consent/' . $gambar);
                if (isset($upload['upload_data'])) {

                    //update datanya
                    $proses = $this->mgrower->updateMemberConsentNotes($gambarFileName, $dataMember['data']['MemberID']);

                    $result['success'] = true;
                    $result['file'] = $gambar . '?' . rand(1, 100);
                    $this->response($result, 200);
                } else {
                    echo 'false';
                    exit;
                }
            }
        }

        if ($this->post('DocNameID') == "Withdrawal") {
            if ($this->file['Koltiva_view_DocumentSurvey_WinFormUploadDoc-Form-WithdrawalConsentNotesInput']['name'] != '') {
                //get ext nya..
                $arrTemp = explode(".", $this->file['Koltiva_view_DocumentSurvey_WinFormUploadDoc-Form-WithdrawalConsentNotesInput']['name']);
                $extNya = array_values(array_slice($arrTemp, -1))[0];

                $gambar = $ProvinceID . '/' . $dataMember['data']['MemberDisplayID'] . '.' . $extNya;
                $gambarFileName = $dataMember['data']['MemberDisplayID'] . '.' . $extNya;

                //cek folder propinsi itu sudah ada belum
                if (!file_exists('images/withdrawal_consent/' . $ProvinceID)) {
                    mkdir('images/withdrawal_consent/' . $ProvinceID, 0777, true);
                }

                //hapus dl file lama
                @unlink('images/withdrawal_consent/' . $gambar);

                $fileupload['Koltiva_view_DocumentSurvey_WinFormUploadDoc-Form-WithdrawalConsentNotesInput'] = $this->file['Koltiva_view_DocumentSurvey_WinFormUploadDoc-Form-WithdrawalConsentNotesInput'];
                $upload = move_upload($fileupload, 'images/withdrawal_consent/' . $gambar);
                if (isset($upload['upload_data'])) {

                    //update datanya
                    $proses = $this->mgrower->updateMemberWithdrawalConsentNotes($gambarFileName, $dataMember['data']['MemberID']);

                    $result['success'] = true;
                    $result['file'] = $gambar . '?' . rand(1, 100);
                    $this->response($result, 200);
                } else {
                    echo 'false';
                    exit;
                }
            }
        }
    }

    public function member_basic_data_form_get() {
        $data = $this->mgrower->getMemberBasicDataForm($this->get('MemberID'));
        $this->response($data, 200);
    }

    public function member_basic_data_form_sme_get() {
        $data = $this->mgrower->getMemberBasicDataFormSME($this->get('MemberID'));
        $this->response($data, 200);
    }


    public function member_data_detail_get() {
        $data = $this->mgrower->getMemberDataDetail($this->get('MemberID'));
        $this->response($data, 200);
    }

    public function setPartnerFarmer_post(){
        //rapikan variable post (begin)
        $post = $this->post();
        foreach ($post as $k => $v) {
            $k = str_replace("Koltiva_view_Grower_FormMainGrower-","",$k);
            $varPost[$k] = $v;
        }
        $proses = $this->mgrower->setPartnerFarmer($varPost);
        $this->response($proses, 200);
    }

    public function member_post() {
        $this->load->model('mmiddleware');

        if ($this->post('Koltiva_view_Grower_FormMainGrower-MemberID') == "") {
            //insert
            $proses = $this->mgrower->insertMember($this->post());

            //Push ke DHIS
            if ($proses['success']) {

                if($proses['CountryCode'] == 'ID') {
                    $uid = 'QxauNvjcpBw'; // push by program
                } else {
                    $uid = ''; //program push farmer wags
                }
                
                if($uid != '' && $proses['MemberIDInc']) {
                    $mID = $proses['MemberIDInc'];
                    $onlyNew = true;
                    $programs = $this->mmiddleware->getAllProgramWithView($uid);
                    if (count($programs) > 0) {
                        foreach ($programs as $progkeys => $program) {
                            $datas = $this->mmiddleware->getDataBy($onlyNew, $program['uid'], $mID);
                            $this->mmiddleware->syncDataPerProgram($datas, $program['uid']);
                        }
                    }
                }
            }
        } else {
            //update
            $proses = $this->mgrower->updateMember($this->post());

            //Push ke DHIS
            if ($proses['success']) {

                if($proses['CountryCode'] == 'ID') {
                    $uid = 'QxauNvjcpBw'; // push by program
                } else {
                    $uid = ''; //program push farmer wags
                }
                
                if($uid != '' && $proses['MemberIDInc']) {
                    $mID = $proses['MemberIDInc'];
                    $onlyNew = false;
                    $programs = $this->mmiddleware->getAllProgramWithView($uid);
                    if (count($programs) > 0) {
                        foreach ($programs as $progkeys => $program) {
                            $datas = $this->mmiddleware->getDataBy($onlyNew, $program['uid'], $mID);
                            $this->mmiddleware->syncDataPerProgram($datas, $program['uid']);
                        }
                    }
                }
            }
        }
        
        $this->response($proses, 200);
    }

    public function member_sme_post(){
        $this->load->model('mmiddleware');

        if ($this->post('Koltiva_view_GrowerSME_FormMainGrower-MemberID') == "") {
            //insert
            $proses = $this->mgrower->insertMemberSME($this->post());

            //Push ke DHIS
            if ($proses['success']) {

                if($proses['CountryCode'] == 'ID') {
                    $uid = 'QxauNvjcpBw'; // push by program
                } else {
                    $uid = ''; //program push farmer wags
                }
                
                if($uid != '' && $proses['MemberIDInc']) {
                    $mID = $proses['MemberIDInc'];
                    $onlyNew = true;
                    $programs = $this->mmiddleware->getAllProgramWithView($uid);
                    if (count($programs) > 0) {
                        foreach ($programs as $progkeys => $program) {
                            $datas = $this->mmiddleware->getDataBy($onlyNew, $program['uid'], $mID);
                            $this->mmiddleware->syncDataPerProgram($datas, $program['uid']);
                        }
                    }
                }
            } 
        } else {
            //update
            $proses = $this->mgrower->updateMemberSME($this->post());

            //Push ke DHIS
            if ($proses['success']) {

                if($proses['CountryCode'] == 'ID') {
                    $uid = 'QxauNvjcpBw'; // push by program
                } else {
                    $uid = ''; //program push farmer wags
                }
                
                if($uid != '' && $proses['MemberIDInc']) {
                    $mID = $proses['MemberIDInc'];
                    $onlyNew = false;
                    $programs = $this->mmiddleware->getAllProgramWithView($uid);
                    if (count($programs) > 0) {
                        foreach ($programs as $progkeys => $program) {
                            $datas = $this->mmiddleware->getDataBy($onlyNew, $program['uid'], $mID);
                            $this->mmiddleware->syncDataPerProgram($datas, $program['uid']);
                        }
                    }
                }
            }
        }
    }

    public function grid_main_sme_get(){
        //set bahasa
        if ($_SESSION['language'] == "Indonesia") {
            $this->load->language('general', 'indonesia');
        } else {
            $this->load->language('general', 'english');
        }

        //sort
        $sorting = json_decode($this->get('sort'));
        $sortingField = $sorting[0]->property;
        $sortingDir = $sorting[0]->direction;

        //get param
        $pSearch = array(
            'prov' => $this->get('prov'),
            'kab' => $this->get('kab'),
            'kec' => $this->get('kec'),
            'SupplychainID' => $this->get('SupplychainID'),
            'textSearch' => $this->get('textSearch'),
            'textSearchDesa' => $this->get('textSearchDesa'),
            'roleSearch' => $this->get('roleSearch'),
            'categorySearch' => $this->get('categorySearch'),
            'AdvRowEnumerator' => $this->get('AdvRowEnumerator'),
            'AdvTextEnumerator' => $this->get('AdvTextEnumerator'),
            'AdvRowHandphone' => $this->get('AdvRowHandphone'),
            'AdvTextHandphone' => $this->get('AdvTextHandphone'),
            'AdvRowAge' => $this->get('AdvRowAge'),
            'AdvOpAge' => $this->get('AdvOpAge'),
            'AdvTextAge' => $this->get('AdvTextAge'),
            'AdvRowMaritalStatus' => $this->get('AdvRowMaritalStatus'),
            'AdvMaritalStatus' => $this->get('AdvMaritalStatus'),
            'AdvRowDateCollection' => $this->get('AdvRowDateCollection'),
            'AdvDateCollectionBegin' => $this->get('AdvDateCollectionBegin'),
            'AdvDateCollectionEnd'   => $this->get('AdvDateCollectionEnd'),
            'AdvRowDateCreated' => $this->get('AdvRowDateCreated'),
            'AdvDateCreatedBegin' => $this->get('AdvDateCreatedBegin'),
            'AdvDateCreatedEnd'   => $this->get('AdvDateCreatedEnd'),
            'AdvRowDateSynced' => $this->get('AdvRowDateSynced'),
            'AdvDateSyncedBegin' => $this->get('AdvDateSyncedBegin'),
            'AdvDateSyncedEnd'   => $this->get('AdvDateSyncedEnd'),
            'AdvRowLastUpdatedDate' => $this->get('AdvRowLastUpdatedDate'),
            'AdvLastUpdatedDateBegin' => $this->get('AdvLastUpdatedDateBegin'),
            'AdvLastUpdatedDateEnd'   => $this->get('AdvLastUpdatedDateEnd'),
            'AdvRowEnumerator'       => $this->get('AdvRowEnumerator'),
            'AdvEnumerator'          => $this->get('AdvEnumerator')
        );

        $data = $this->mgrower->getGridMainGrower($pSearch, $this->get('start'), $this->get('limit'), $sortingField, $sortingDir);
        $this->response($data, 200);
    }

    public function member_labour_extension_post(){
        $VarPost = $this->post();

        $proses = $this->mgrower->UpdateMemberLabourExt($VarPost);
        if($proses['success'] == true){
            $this->response($proses, 200);
        }else{
            $this->response($proses, 400);
        }
    }

    public function member_delete() {
        $MemberID = (int) $this->delete('MemberID');
//        error_reporting(E_ALL);
        $proses = $this->mgrower->deleteMember($MemberID);
        if ($proses && $proses['success']) {
            $MemberUid = $this->mgrower->getMemberUID($MemberID);
            if ($MemberUid) {
                $result = $this->mdsync->removeDhisEvent($MemberUid);
                if ($result['response']['status'] == 'SUCCESS') {
                    $proses = $this->mgrower->deleteMemberUID($MemberUid);
                }
            }
        }

        $this->response($proses, 200);
    }

    public function grid_family_labour_get() {
        //set bahasa
        if ($_SESSION['language'] == "Indonesia") {
            $this->load->language('general', 'indonesia');
        } else {
            $this->load->language('general', 'english');
        }

        $data = $this->mgrower->getGridFamilyLabour($this->get('MemberID'));
        $this->response($data, 200);
    }

    public function family_labour_post() {
        $varPost = $this->post();

        //prep variabel (begin)
        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Grower_WinFormFamLab-Form-", '', $key);
            if ($value == "")
                $value = null;

            switch ($keyNew) {
                case 'TotalWorkingHrsPerDay':
                case 'TotalWorkingHrsPerMonth':
                case 'WageAmount':
                    $value = str_replace(",", "", $value);
                    break;
            }

            $paramPost[$keyNew] = $value;
        }
        //prep variabel (end)

        if ($paramPost['FamLabID'] == "") {
            //insert
            $proses = $this->mgrower->insertFamLab($paramPost);
        } else {
            //update
            $proses = $this->mgrower->updateFamLab($paramPost);
        }
        $this->response($proses, 200);
    }

    public function family_labour_delete() {
        $FamLabID = (int) $this->delete('FamLabID');
        $proses = $this->mgrower->deleteFamLab($FamLabID);
        $this->response($proses, 200);
    }

    public function member_family_labour_data_get() {
        $data = $this->mgrower->getMemberFamilyLabourFormData($this->get('FamLabID'));
        $this->response($data, 200);
    }

    public function grid_plot_status_get() {
        //set bahasa
        if ($_SESSION['language'] == "Indonesia") {
            $this->load->language('general', 'indonesia');
        } else {
            $this->load->language('general', 'english');
        }

        $data = $this->mgrower->getGridPlotStatus($this->get('MemberID'));
        $this->response($data, 200);
    }

    public function member_plot_status_data_get() {
        $data = $this->mgrower->getMemberPlotStatusFormData($this->get('MemberID'), $this->get('PlotNr'));
        $this->response($data, 200);
    }

    public function plot_status_post() {
        $varPost = $this->post();

        //prep variabel (begin)
        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Grower_WinFormPlotStatus-Form-", '', $key);
            if ($value == "")
                $value = null;

            $paramPost[$keyNew] = $value;
        }
        //prep variabel (end)

        $proses = $this->mgrower->updatePlotStatus($paramPost);
        $this->response($proses, 200);
    }

    public function cetak_consent_notes_get() {
        $this->load->model('document_survey/mdocument_survey');
        $this->load->helper('date');

        if ($_SESSION['language'] == "Indonesia") {
            $this->load->language('general', 'indonesia');
        } else {
            $this->load->language('general', 'english');
        }
        $data = array();
        $MemberIDs = $this->uri->segment(3);
        $opsiDisplay = $this->uri->segment(4);

        $arrMemberID = explode("::", $MemberIDs);
        //ambil data content
        foreach ($arrMemberID as $key => $MemberID) {
            $MemberData = $this->mgrower->getMemberDataDetail($MemberID);
            $data['member'][$key] = $MemberData['data'];

            //cek Petani milik Petani mana
            $PartnerID = $this->mdocument_survey->CheckFarmerPartnerID($MemberID);
        }

        if ($opsiDisplay == "empty") {
            $data['member'][0]['MemberNameTtd'] = '(' . lang('Nama') . ')';
        }


        $this->load->view('cetak_consent_notes_template_header');
        switch ($PartnerID) {
            case '7':
                //Sinar Mas
                $this->load->view('cetak_consent_notes_template', $data);
            break;
            case '11':
                //SNV
                $this->load->view('cetak_consent_notes_template_snv', $data);
            break;
            case '14':
                //Wild Asia
                $this->load->view('cetak_consent_notes_template_wildasia', $data);
            break;
            case '235':
                //Sinar Mas
                $this->load->view('cetak_consent_notes_template_hdl', $data);
            break;
            default:
                $this->load->view('cetak_consent_notes_template', $data);
            break;
        }
        $this->load->view('cetak_consent_notes_template_footer');
    }

    public function cetak_rspo_document_get() {
        $this->load->model('document_survey/mdocument_survey');
        $this->load->helper('date');

        if ($_SESSION['language'] == "Indonesia") {
            $this->load->language('general', 'indonesia');
        } else {
            $this->load->language('general', 'english');
        }
        $data = array();
        $MemberIDs = $this->uri->segment(3);
        $opsiDisplay = $this->uri->segment(4);

        $arrMemberID = explode("::", $MemberIDs);
        //ambil data content
        foreach ($arrMemberID as $key => $MemberID) {
            $MemberData = $this->mgrower->getMemberDataDetail($MemberID);
            $data['member'][$key] = $MemberData['data'];

            //cek Petani milik Petani mana
            $PartnerID = $this->mdocument_survey->CheckFarmerPartnerID($MemberID);
        }

        if ($opsiDisplay == "empty") {
            $data['member'][0]['MemberNameTtd'] = '(' . lang('Nama') . ')';
        }

        // echo "<pre>";print_r($data);die;


        $this->load->view('cetak_rspo_doc_template_header');
        $this->load->view('cetak_rspo_doc_template', $data);
        $this->load->view('cetak_rspo_doc_template_footer');
    }

    public function view_consent_notes_get() {
        $MemberID = (int) $this->uri->segment(3);
        $MemberData = $this->mgrower->getMemberDataDetail($MemberID);

        if ($MemberData['data']['LearningContractSign'] != "") {
            $data['gambarNya'] = "images/consent/" . $MemberData['data']['ProvinceID'] . "/" . $MemberData['data']['LearningContractSign'];
        } else {
            $data['gambarNya'] = "images/no-image-vertikal.jpg";
        }

        $this->load->view('view_consent_notes_template', $data);
    }

    public function grid_other_land_get() {
        //set bahasa
        if ($_SESSION['language'] == "Indonesia") {
            $this->load->language('general', 'indonesia');
        } else {
            $this->load->language('general', 'english');
        }

        $data = $this->mgrower->getGridOtherLand($this->get('MemberID'));
        $this->response($data, 200);
    }

    public function other_land_post() {
        $varPost = $this->post();

        //prep variabel (begin)
        foreach ($varPost as $key => $value) {
            if ($value == "")
                $value = null;

            switch ($keyNew) {
                case 'GardenHa':
                    $value = str_replace(",", "", $value);
                    break;
            }

            $paramPost[$key] = $value;
        }
        //prep variabel (end)

        if ($paramPost['opsiPost'] == "insert") {
            //insert
            $proses = $this->mgrower->insertOtherLand($paramPost);
        } else {
            //update
            $proses = $this->mgrower->updateOtherLand($paramPost);
        }
        $this->response($proses, 200);
    }

    public function other_land_delete() {
        $MemOtherID = (int) $this->delete('MemOtherID');
        $proses = $this->mgrower->deleteOtherLand($MemOtherID);
        $this->response($proses, 200);
    }

    public function grid_labour_get(){
        //set bahasa
        if ($_SESSION['language'] == "Indonesia") {
            $this->load->language('general', 'indonesia');
        } else {
            $this->load->language('general', 'english');
        }

        $data = $this->mgrower->getGridLabour($this->get('MemberID'));
        $this->response($data, 200);
    }

    public function labour_post(){
        $varPost = $this->post();

        //prep variabel (begin)
        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Grower_WinFormLabour-Form-", '', $key);
            if ($value == "")
                $value = null;

            switch ($keyNew) {
                case 'TotalWorkingHrsPerDay':
                case 'WageAmount':
                    $value = str_replace(",", "", $value);
                break;
            }

            $paramPost[$keyNew] = $value;
        }
        //prep variabel (end)

        if ($paramPost['LaboID'] == "") {
            //insert
            $proses = $this->mgrower->insertLabour($paramPost);
        } else {
            //update
            $proses = $this->mgrower->updateLabour($paramPost);
        }
        $this->response($proses, 200);
    }

    public function labour_delete(){
        $LaboID = (int) $this->delete('LaboID');
        $proses = $this->mgrower->deleteLabour($LaboID);
        $this->response($proses, 200);
    }

    public function member_labour_form_data_get(){
        $data = $this->mgrower->getMemberLabourFormData($this->get('LaboID'));
        $this->response($data, 200);
    }


    public function cetak_agent_profiles_get(){
        //set bahasa
        if ($_SESSION['language'] == "Indonesia") {
            $this->load->language('general', 'indonesia');
        } else {
            $this->load->language('general', 'english');
        }

        //get param
        $MemberID = $this->get('MemberID');
        $MemberIDs = explode('::', $MemberID);

        $dataHeader['titleNya'] = "SME Profile";
        $this->load->view('profiles/cetak_agent_profiles_header', $dataHeader);

        if (strpos($MemberID, '::')) {
            $countData = count($MemberIDs);
            $increData = 1;
            foreach ($MemberIDs as $key => $MemberID) {
                $this->cetak_agent_profiles($MemberID, $countData, $increData);
                $increData++;
            }
        } else {
            $this->cetak_agent_profiles($MemberID);
        }

        $this->load->view('profiles/cetak_agent_profiles_footer');
    }

    public function cetak_agent_profiles($MemberID, $countData = 1, $increData = 0){
        $this->load->library('awsfileupload');
        $data = array();

        //get data agent
        $dataMember = $this->mgrower->getMemberDataDetail($MemberID);
        $data['agent'] = $dataMember['data'];
        $data['gardens'] = $this->mgrower->getGardenData($MemberID);
        $data['gardens_polygon'] = $this->mgrower->getGardenPolygonData($data['gardens']);

        
        //cek apakah ada data koordinat gardennya
        $data['gardens_coordinate_exists'] = $this->mgrower->checkGardenCoordinateExist($data['gardens']);

        //get jumlah staff
        $data['staff'] = $this->mgrower->getStaffAgent($MemberID);

        //get kendaraan
        $data['NrOfVehicle'] = $this->mgrower->getJumlahKendaraan($MemberID);

        //get data training
        $data['training'] = $this->mgrower->getTrainingDataAgent($MemberID);
        
        $data['tc'] = $this->mgrower->getTraceabilityDataAgent($MemberID);

        //logo
        $this->load->model('project_process/mproject_process');
        $DistrictID = substr($data['agent']['VillageID'], 0,4);
        // $data['logos'] = $this->mproject_process->getPrintLogoHeader($DistrictID);
        $data['logos'] = $this->mproject_process->getPrintLogoHeaderFarmerNew($MemberID);
        $data['qrcode_pic']     = $this->QrcodeGenerator($data['agent']['MemberDisplayID']);

        $data['ffb'] = $this->mgrower->getFFBSales($MemberID);
        $data['traceability_details'] = $this->mgrower->getTraceabilityDetails($MemberID);

        //echo '<pre>'; print_r($data); exit;
        $this->load->view('profiles/cetak_agent_profiles', $data);
    }

    public function QrcodeGenerator($FarmerID) {
        $file_code = 'files/tmp/' . $FarmerID . '.png';
        $qrCode = new QrCode($FarmerID);
        $qrCode->setSize(175);
        $qrCode->setEncoding('UTF-8');
        $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevel(ErrorCorrectionLevel::HIGH));
        $qrCode->setLogoPath('images/koltiva_logo_qrcode.jpg');
        $qrCode->setLogoSize(48);
        $qrCode->setRoundBlockSize(true);
        $qrCode->setValidateResult(false);
        $qrCode->setWriterOptions(['exclude_xml_declaration' => true]);
        $qrCode->writeFile($file_code);

        $path = '';
        if (file_exists('files/tmp/' . $FarmerID . '.png')) {
            $path = 'files/tmp/' . $FarmerID . '.png';
        }

        return $path;
    }

    /*================================================== Export Excel ==========================================================================*/
    public function export_farmers_sta_get(){
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        //set bahasa
        if ($_SESSION['language'] == "Indonesia") {
            $this->load->language('general', 'indonesia');
        } else {
            $this->load->language('general', 'english');
        }

        //get param
        $pSearch = array(
            'prov' => $this->get('prov'),
            'kab' => $this->get('kab'),
            'kec' => $this->get('kec'),
            'textSearch' => $this->get('textSearch'),
            'textSearchDesa' => $this->get('textSearchDesa'),
            'roleSearch' => $this->get('roleSearch'),
            'categorySearch' => $this->get('categorySearch'),
            'AdvRowHandphone' => $this->get('AdvRowHandphone'),
            'AdvTextHandphone' => $this->get('AdvTextHandphone'),
            'AdvRowEnumerator' => $this->get('AdvRowEnumerator'),
            'AdvTextEnumerator' => $this->get('AdvTextEnumerator'),
            'AdvRowAge' => $this->get('AdvRowAge'),
            'AdvOpAge' => $this->get('AdvOpAge'),
            'AdvTextAge' => $this->get('AdvTextAge'),
            'AdvRowMaritalStatus' => $this->get('AdvRowMaritalStatus'),
            'AdvMaritalStatus' => $this->get('AdvMaritalStatus'),
            'AdvRowDateCollection' => $this->get('AdvRowDateCollection'),
            'AdvDateCollectionBegin' => $this->get('AdvDateCollectionBegin'),
            'AdvDateCollectionEnd' => $this->get('AdvDateCollectionEnd'),
            'AdvRowDateCreated' => $this->get('AdvRowDateCreated'),
            'AdvDateCreatedBegin' => $this->get('AdvDateCreatedBegin'),
            'AdvDateCreatedEnd'   => $this->get('AdvDateCreatedEnd'),
            'AdvRowDateSynced' => $this->get('AdvRowDateSynced'),
            'AdvDateSyncedBegin' => $this->get('AdvDateSyncedBegin'),
            'AdvDateSyncedEnd'   => $this->get('AdvDateSyncedEnd'),
            'AdvRowLastUpdatedDate' => $this->get('AdvRowLastUpdatedDate'),
            'AdvLastUpdatedDateBegin' => $this->get('AdvLastUpdatedDateBegin'),
            'AdvLastUpdatedDateEnd'   => $this->get('AdvLastUpdatedDateEnd'),
            'AdvRowEnumerator'       => $this->get('AdvRowEnumerator'),
            'AdvEnumerator'          => $this->get('AdvEnumerator')
        );
        //Get Data Farmer
        $dataList       = $this->mgrower->getMainGrowerSTAExcel($pSearch);

        //Get Data Kebun
        $dataListKebun  = $this->mgrower->getPlotSurvey($pSearch); 


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

            //Kolom Header Survey Plot
            $dataHeaderGarden = array('No');
            foreach($dataListKebun[0] as $key2 => $value2){
                array_push($dataHeaderGarden,lang($key2));
            }
            //Kolom Header Survey Plot
            
            //Kolom Body Survey Plot
            $dataListExcelKebun = array();
            $no = 1;
            foreach ($dataListKebun as $key => $value) {
                $data = array();
                array_push($data,$no);
                foreach($value as $keyx => $valuex){
                    array_push($data,$valuex);
                }
                $dataListExcelKebun[$key] = $data;
                $no++;
            }
            // echo '<pre>'; print_r($dataListExcel); echo '</pre>'; exit;
            // return;
            //Kolom Body Survey Plot

            $writer = WriterEntityFactory::createXLSXWriter(); // for XLSX files// 
            $namaFile = date('YmdHis') . '_export_excel_farmers.xlsx';
            $filePath = 'files/tmp/' . $namaFile;
            $writer->openToFile($filePath);

            $defaultStyle = (new StyleBuilder())
                ->setFontName('Arial')
                ->setFontSize(11)
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
                ->setFontColor(Color::WHITE)
                ->setBorder($borderDefa)
                ->setBackgroundColor(Color::GREEN)
                ->build();

            //row header
            $rowHeader = WriterEntityFactory::createRowFromArray($dataHeader, $styleHeader);
            
            $writer->getCurrentSheet()->setName('Farmer Data');
            $writer->addRow($rowHeader);

            $styleData = (new StyleBuilder())
                ->setBorder($borderDefa)
                ->build();

            $styleFormatAngka = (new StyleBuilder())
                ->setBorder($borderDefa)
                ->setFormat('0')
                ->build();

            $styleFormatTanggal = (new StyleBuilder())
                ->setBorder($borderDefa)
                ->setFormat('YYYY-mm-dd')
                ->build();

            for ($i=0; $i < count($dataListExcel); $i++) {
                $dataRows = $dataListExcel[$i];
                $cells = array();
    
                for ($j=0; $j < count($dataRows); $j++) {
                    $styleRow = null;
                    $dataRow = null;
    
                    //cek apakah numeric
                    if(is_numeric($dataRows[$j])){
                        $styleRow = $styleFormatAngka;
                        $dataRow = $dataRows[$j];
                    } else {
                        //cek apakah tanggal
                        if($this->validateDate($dataRows[$j]) == true) {
                            $styleRow = $styleFormatTanggal;
                            $dataRow = 25569 + (strtotime($dataRows[$j]) / 86400);
                        } else {
                            $styleRow = $styleData;
                            $dataRow = $dataRows[$j];
                        }
                    }
    
                    $cells[$j] = WriterEntityFactory::createCell($dataRow, $styleRow);
                }
                /*$cells = [
                    WriterEntityFactory::createCell($dataRows[0], $styleData),
                    WriterEntityFactory::createCell((float) $dataRows[1], $styleFormatAngka),
                    WriterEntityFactory::createCell($dataRows[2], $styleData),
                    WriterEntityFactory::createCell(25569 + (time() / 86400), $styleFormatTanggal),
                    WriterEntityFactory::createCell($dataRows[4], $styleFormatTanggal)
                ];*/
    
                $rowData = WriterEntityFactory::createRow($cells);
                $writer->addRow($rowData);
            }

            
            $rowHeaderKebun = WriterEntityFactory::createRowFromArray($dataHeaderGarden, $styleHeader);
            $writer->addNewSheetAndMakeItCurrent()->setName('Survey Plot');
            $writer->addRow($rowHeaderKebun);

            for ($i=0; $i < count($dataListExcelKebun); $i++) {
                $dataRowsKebun = $dataListExcelKebun[$i];
                $cells2 = array();
    
                for ($j=0; $j < count($dataRowsKebun); $j++) {
                    $styleRow = null;
                    $dataRowKebun = null;
    
                    //cek apakah numeric
                    if(is_numeric($dataRowsKebun[$j])){
                        $styleRow = $styleFormatAngka;
                        $dataRowKebun = (float) $dataRowsKebun[$j];
                    } else {
                        //cek apakah tanggal
                        if($this->validateDate($dataRowsKebun[$j]) == true) {
                            $styleRow = $styleFormatTanggal;
                            $dataRowKebun = 25569 + (strtotime($dataRowsKebun[$j]) / 86400);
                        } else {
                            $styleRow = $styleData;
                            $dataRowKebun = $dataRowsKebun[$j];
                        }
                    }
    
                    $cells2[$j] = WriterEntityFactory::createCell($dataRowKebun, $styleRow);
                }
                /*$cells = [
                    WriterEntityFactory::createCell($dataRows[0], $styleData),
                    WriterEntityFactory::createCell((float) $dataRows[1], $styleFormatAngka),
                    WriterEntityFactory::createCell($dataRows[2], $styleData),
                    WriterEntityFactory::createCell(25569 + (time() / 86400), $styleFormatTanggal),
                    WriterEntityFactory::createCell($dataRows[4], $styleFormatTanggal)
                ];*/
    
                $rowDataKebun = WriterEntityFactory::createRow($cells2);
                $writer->addRow($rowDataKebun);
            }
    
            $writer->close();
    
            $this->response(array('success' => TRUE, 'filenya' => base_url() . $filePath), 200);
            exit;
        }else{
            $this->response(array('success' => FALSE, 'filenya' => ''));
            exit;
        }
    }

    public function export_farmers_get() {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        //set bahasa
        if ($_SESSION['language'] == "Indonesia") {
            $this->load->language('general', 'indonesia');
        } else {
            $this->load->language('general', 'english');
        }

        //get param
        $pSearch = array(
            'prov' => $this->get('prov'),
            'kab' => $this->get('kab'),
            'kec' => $this->get('kec'),
            'SupplychainID' => $this->get('SupplychainID'),
            'textSearch' => $this->get('textSearch'),
            'textSearchDesa' => $this->get('textSearchDesa'),
            'roleSearch' => $this->get('roleSearch'),
            'categorySearch' => $this->get('categorySearch'),
            'AdvRowHandphone' => $this->get('AdvRowHandphone'),
            'AdvTextHandphone' => $this->get('AdvTextHandphone'),
            'AdvRowEnumerator' => $this->get('AdvRowEnumerator'),
            'AdvTextEnumerator' => $this->get('AdvTextEnumerator'),
            'AdvRowAge' => $this->get('AdvRowAge'),
            'AdvOpAge' => $this->get('AdvOpAge'),
            'AdvTextAge' => $this->get('AdvTextAge'),
            'AdvRowMaritalStatus' => $this->get('AdvRowMaritalStatus'),
            'AdvMaritalStatus' => $this->get('AdvMaritalStatus'),
            'AdvRowDateCollection' => $this->get('AdvRowDateCollection'),
            'AdvDateCollectionBegin' => $this->get('AdvDateCollectionBegin'),
            'AdvDateCollectionEnd' => $this->get('AdvDateCollectionEnd'),
            'AdvRowDateCreated' => $this->get('AdvRowDateCreated'),
            'AdvDateCreatedBegin' => $this->get('AdvDateCreatedBegin'),
            'AdvDateCreatedEnd'   => $this->get('AdvDateCreatedEnd'),
            'AdvRowDateSynced' => $this->get('AdvRowDateSynced'),
            'AdvDateSyncedBegin' => $this->get('AdvDateSyncedBegin'),
            'AdvDateSyncedEnd'   => $this->get('AdvDateSyncedEnd'),
            'AdvRowLastUpdatedDate' => $this->get('AdvRowLastUpdatedDate'),
            'AdvLastUpdatedDateBegin' => $this->get('AdvLastUpdatedDateBegin'),
            'AdvLastUpdatedDateEnd'   => $this->get('AdvLastUpdatedDateEnd'),
            'AdvRowEnumerator'       => $this->get('AdvRowEnumerator'),
            'AdvEnumerator'          => $this->get('AdvEnumerator')
        );
        //Get Data Farmer
        $dataList       = $this->mgrower->getMainGrowerExcel($pSearch);
        
        //Get Data Kebun
        $dataListKebun  = $this->mgrower->getPlotSurvey($pSearch);
        
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

            $writer = WriterEntityFactory::createXLSXWriter(); // for XLSX files// 
            $namaFile = date('YmdHis') . '_export_excel_farmers.xlsx';
            $filePath = 'files/tmp/' . $namaFile;
            $writer->openToFile($filePath);

            $defaultStyle = (new StyleBuilder())
                ->setFontName('Arial')
                ->setFontSize(11)
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
                ->setFontColor(Color::WHITE)
                ->setBorder($borderDefa)
                ->setBackgroundColor(Color::GREEN)
                ->build();

            //row header
            $rowHeader = WriterEntityFactory::createRowFromArray($dataHeader, $styleHeader);
            $writer->addRow($rowHeader);

            $styleData = (new StyleBuilder())
                ->setBorder($borderDefa)
                ->build();

            $styleFormatAngka = (new StyleBuilder())
                ->setBorder($borderDefa)
                ->setFormat('0')
                ->build();

            $styleFormatTanggal = (new StyleBuilder())
                ->setBorder($borderDefa)
                ->setFormat('YYYY-mm-dd')
                ->build();

            for ($i=0; $i < count($dataListExcel); $i++) {
                $dataRows = $dataListExcel[$i];
                $cells = array();
    
                for ($j=0; $j < count($dataRows); $j++) {
                    $styleRow = null;
                    $dataRow = null;
    
                    //cek apakah numeric
                    if(is_numeric($dataRows[$j])){
                        $styleRow = $styleFormatAngka;
                        $dataRow = $dataRows[$j];
                    } else {
                        //cek apakah tanggal
                        if($this->validateDate($dataRows[$j]) == true) {
                            $styleRow = $styleFormatTanggal;
                            $dataRow = $dataRows[$j];
                        } else {
                            $styleRow = $styleData;
                            $dataRow = $dataRows[$j];
                        }
                    }
    
                    $cells[$j] = WriterEntityFactory::createCell($dataRow, $styleRow);
                }
                /*$cells = [
                    WriterEntityFactory::createCell($dataRows[0], $styleData),
                    WriterEntityFactory::createCell((float) $dataRows[1], $styleFormatAngka),
                    WriterEntityFactory::createCell($dataRows[2], $styleData),
                    WriterEntityFactory::createCell(25569 + (time() / 86400), $styleFormatTanggal),
                    WriterEntityFactory::createCell($dataRows[4], $styleFormatTanggal)
                ];*/
    
                $rowData = WriterEntityFactory::createRow($cells);
                $writer->addRow($rowData);
            }

            if(count($dataListKebun) > 0){
                //Kolom Header Survey Plot
                $dataHeaderGarden = array('No');
                foreach($dataListKebun[0] as $key2 => $value2){
                    array_push($dataHeaderGarden,lang($key2));
                }
                //Kolom Header Survey Plot
                
                //Kolom Body Survey Plot
                $dataListExcelKebun = array();
                $no = 1;
                foreach ($dataListKebun as $key => $value) {
                    $data = array();
                    array_push($data,$no);
                    foreach($value as $keyx => $valuex){
                        array_push($data,$valuex);
                    }
                    $dataListExcelKebun[$key] = $data;
                    $no++;
                }
                // echo '<pre>'; print_r($dataListExcel); echo '</pre>'; exit;
                // return;
                //Kolom Body Survey Plot                

                $rowHeaderKebun = WriterEntityFactory::createRowFromArray($dataHeaderGarden, $styleHeader);
                $writer->addNewSheetAndMakeItCurrent()->setName('Survey Plot');
                $writer->addRow($rowHeaderKebun);

                for ($i=0; $i < count($dataListExcelKebun); $i++) {
                    $dataRowsKebun = $dataListExcelKebun[$i];
                    $cells2 = array();
        
                    for ($j=0; $j < count($dataRowsKebun); $j++) {
                        $styleRow = null;
                        $dataRowKebun = null;
        
                        //cek apakah numeric
                        if(is_numeric($dataRowsKebun[$j])){
                            $styleRow = $styleFormatAngka;
                            $dataRowKebun = (float) $dataRowsKebun[$j];
                        } else {
                            //cek apakah tanggal
                            if($this->validateDate($dataRowsKebun[$j]) == true) {
                                $styleRow = $styleFormatTanggal;
                                $dataRowKebun = 25569 + (strtotime($dataRowsKebun[$j]) / 86400);
                            } else {
                                $styleRow = $styleData;
                                $dataRowKebun = $dataRowsKebun[$j];
                            }
                        }
        
                        $cells2[$j] = WriterEntityFactory::createCell($dataRowKebun, $styleRow);
                    }
                    /*$cells = [
                        WriterEntityFactory::createCell($dataRows[0], $styleData),
                        WriterEntityFactory::createCell((float) $dataRows[1], $styleFormatAngka),
                        WriterEntityFactory::createCell($dataRows[2], $styleData),
                        WriterEntityFactory::createCell(25569 + (time() / 86400), $styleFormatTanggal),
                        WriterEntityFactory::createCell($dataRows[4], $styleFormatTanggal)
                    ];*/
        
                    $rowDataKebun = WriterEntityFactory::createRow($cells2);
                    $writer->addRow($rowDataKebun);
                }
            }
    
            $writer->close();
    
            $this->response(array('success' => TRUE, 'filenya' => base_url() . $filePath), 200);
            exit;
        }else{
            $this->response(array('success' => FALSE, 'filenya' => ''));
            exit;
        }
    }

    private function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
    
    public function export_farmers_mill_get() {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        //set bahasa
        if ($_SESSION['language'] == "Indonesia") {
            $this->load->language('general', 'indonesia');
        } else {
            $this->load->language('general', 'english');
        }

        //get param partnerid
        $this->load->model('dboard/mpro_supplychain_kpi');
        $PartnerIDImp = $this->get('pPartnerSearch');
        $MillFirstLoad = (int) $this->get('pPartnerFirstLoad');
        
        if($MillFirstLoad == 1) {
            $PartnerIDStr = $this->mpro_supplychain_kpi->DataGetPartnerIDAllHirarki($_SESSION['PartnerID']);
        } else {
            //Saring variablenya
            $PartnerIDImp = explode(',',$PartnerIDImp);
            $PartnerIDArr = array();
            for ($i=0; $i < count($PartnerIDImp); $i++) { 
                $PartnerIDArr[] = (int) $PartnerIDImp[$i];
            }
            $PartnerIDStr = implode(',',$PartnerIDArr);
        }
        //get param
        $pSearch = array(
            'prov' => $this->get('prov'),
            'kab' => $this->get('kab'),
            'kec' => $this->get('kec'),
            'textSearch' => $this->get('textSearch'),
            'textSearchDesa' => $this->get('textSearchDesa'),
            'roleSearch' => $this->get('roleSearch'),
            'pPartnerSearch' => $PartnerIDStr,
            'pPartnerFirstLoad' => $this->get('pPartnerFirstLoad'),
            'categorySearch' => $this->get('categorySearch'),
            'AdvRowHandphone' => $this->get('AdvRowHandphone'),
            'AdvRowEnumerator' => $this->get('AdvRowEnumerator'),
            'AdvTextHandphone' => $this->get('AdvTextHandphone'),
            'AdvRowAge' => $this->get('AdvRowAge'),
            'AdvOpAge' => $this->get('AdvOpAge'),
            'AdvTextAge' => $this->get('AdvTextAge'),
            'AdvRowMaritalStatus' => $this->get('AdvRowMaritalStatus'),
            'AdvMaritalStatus' => $this->get('AdvMaritalStatus'),
            'AdvRowDateCollection' => $this->get('AdvRowDateCollection'),
            'AdvDateCollectionBegin' => $this->get('AdvDateCollectionBegin'),
            'AdvDateCollectionEnd' => $this->get('AdvDateCollectionEnd'),
            'AdvRowEnumerator'       => $this->get('AdvRowEnumerator'),
            'AdvEnumerator'          => $this->get('AdvEnumerator')
        );

        //Get Data Farmer
        $dataList = $this->mgrower->getGridMillMainGrower($pSearch, null, null, null, null, 'export_excel');
        
        //Get Data Kebun
        $dataListKebun  = $this->mgrower->getPlotSurveyMill($pSearch); 
        
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

            //Kolom Header Survey Plot
            $dataHeaderGarden = array('No');
            foreach($dataListKebun[0] as $key2 => $value2){
                array_push($dataHeaderGarden,lang($key2));
            }
            //Kolom Header Survey Plot
            
            //Kolom Body Survey Plot
            $dataListExcelKebun = array();
            $no = 1;
            foreach ($dataListKebun as $key => $value) {
                $data = array();
                array_push($data,$no);
                foreach($value as $keyx => $valuex){
                    array_push($data,$valuex);
                }
                $dataListExcelKebun[$key] = $data;
                $no++;
            }
            // echo '<pre>'; print_r($dataListExcel); echo '</pre>'; exit;
            // return;
            //Kolom Body Survey Plot

            $writer = WriterEntityFactory::createXLSXWriter(); // for XLSX files// 
            $namaFile = date('YmdHis') . '_export_excel_farmers.xlsx';
            $filePath = 'files/tmp/' . $namaFile;
            $writer->openToFile($filePath);

            $defaultStyle = (new StyleBuilder())
                ->setFontName('Arial')
                ->setFontSize(11)
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
                ->setFontColor(Color::WHITE)
                ->setBorder($borderDefa)
                ->setBackgroundColor(Color::GREEN)
                ->build();

            //row header
            $rowHeader = WriterEntityFactory::createRowFromArray($dataHeader, $styleHeader);
            $writer->addRow($rowHeader);

            $styleData = (new StyleBuilder())
                ->setBorder($borderDefa)
                ->build();

            $styleFormatAngka = (new StyleBuilder())
                ->setBorder($borderDefa)
                ->setFormat('0')
                ->build();

            $styleFormatTanggal = (new StyleBuilder())
                ->setBorder($borderDefa)
                ->setFormat('YYYY-mm-dd')
                ->build();

            for ($i=0; $i < count($dataListExcel); $i++) {
                $dataRows = $dataListExcel[$i];
                $cells = array();
    
                for ($j=0; $j < count($dataRows); $j++) {
                    $styleRow = null;
                    $dataRow = null;
    
                    //cek apakah numeric
                    if(is_numeric($dataRows[$j])){
                        $styleRow = $styleFormatAngka;
                        $dataRow = $dataRows[$j];
                    } else {
                        //cek apakah tanggal
                        if($this->validateDate($dataRows[$j]) == true) {
                            $styleRow = $styleFormatTanggal;
                            $dataRow = 25569 + (strtotime($dataRows[$j]) / 86400);
                        } else {
                            $styleRow = $styleData;
                            $dataRow = $dataRows[$j];
                        }
                    }
    
                    $cells[$j] = WriterEntityFactory::createCell($dataRow, $styleRow);
                }
                /*$cells = [
                    WriterEntityFactory::createCell($dataRows[0], $styleData),
                    WriterEntityFactory::createCell((float) $dataRows[1], $styleFormatAngka),
                    WriterEntityFactory::createCell($dataRows[2], $styleData),
                    WriterEntityFactory::createCell(25569 + (time() / 86400), $styleFormatTanggal),
                    WriterEntityFactory::createCell($dataRows[4], $styleFormatTanggal)
                ];*/
    
                $rowData = WriterEntityFactory::createRow($cells);
                $writer->addRow($rowData);
            }

            $rowHeaderKebun = WriterEntityFactory::createRowFromArray($dataHeaderGarden, $styleHeader);
            $writer->addNewSheetAndMakeItCurrent()->setName('Survey Plot');
            $writer->addRow($rowHeaderKebun);

            for ($i=0; $i < count($dataListExcelKebun); $i++) {
                $dataRowsKebun = $dataListExcelKebun[$i];
                $cells2 = array();
    
                for ($j=0; $j < count($dataRowsKebun); $j++) {
                    $styleRow = null;
                    $dataRowKebun = null;
    
                    //cek apakah numeric
                    if(is_numeric($dataRowsKebun[$j])){
                        $styleRow = $styleFormatAngka;
                        $dataRowKebun = (float) $dataRowsKebun[$j];
                    } else {
                        //cek apakah tanggal
                        if($this->validateDate($dataRowsKebun[$j]) == true) {
                            $styleRow = $styleFormatTanggal;
                            $dataRowKebun = 25569 + (strtotime($dataRowsKebun[$j]) / 86400);
                        } else {
                            $styleRow = $styleData;
                            $dataRowKebun = $dataRowsKebun[$j];
                        }
                    }
    
                    $cells2[$j] = WriterEntityFactory::createCell($dataRowKebun, $styleRow);
                }
                /*$cells = [
                    WriterEntityFactory::createCell($dataRows[0], $styleData),
                    WriterEntityFactory::createCell((float) $dataRows[1], $styleFormatAngka),
                    WriterEntityFactory::createCell($dataRows[2], $styleData),
                    WriterEntityFactory::createCell(25569 + (time() / 86400), $styleFormatTanggal),
                    WriterEntityFactory::createCell($dataRows[4], $styleFormatTanggal)
                ];*/
    
                $rowDataKebun = WriterEntityFactory::createRow($cells2);
                $writer->addRow($rowDataKebun);
            }
    
            $writer->close();
    
            $this->response(array('success' => TRUE, 'filenya' => base_url() . $filePath), 200);
            exit;
        }else{
            $this->response(array('success' => FALSE, 'filenya' => ''));
            exit;
        }
    }

    public function export_all_dataset_get() {
        echo '<pre>';
        print_r($this->get(null));
        echo '</pre>';
        exit;
        //get param
        $pSearch = array(
            'prov' => $this->get('prov'),
            'kab' => $this->get('kab'),
            'kec' => $this->get('kec'),
            'textSearch' => $this->get('textSearch'),
            'roleSearch' => $this->get('roleSearch'),
            'AdvRowHandphone' => $this->get('AdvRowHandphone'),
            'AdvTextHandphone' => $this->get('AdvTextHandphone'),
            'AdvRowAge' => $this->get('AdvRowAge'),
            'AdvOpAge' => $this->get('AdvOpAge'),
            'AdvTextAge' => $this->get('AdvTextAge'),
            'AdvRowMaritalStatus' => $this->get('AdvRowMaritalStatus'),
            'AdvMaritalStatus' => $this->get('AdvMaritalStatus')
        );
    }

    //============================ Khusus WAGS ======================================//

    public function member_basic_data_form_wags_get() {
        $data = $this->mgrower->getMemberBasicDataFormWAGS($this->get('MemberID'));
        $this->response($data, 200);
    }

    public function member_wags_post() {
        $this->load->model('mmiddleware');

        if ($this->post('Koltiva_view_GrowerWAGS_FormMainGrower-MemberID') == "") {
            //insert
            $proses = $this->mgrower->insertMemberWAGS($this->post());

            //Push ke DHIS
            if ($proses['success']) {

                if($proses['CountryCode'] == 'ID') {
                    $uid = 'QxauNvjcpBw'; // push by program
                } else {
                    $uid = 'zbLN28sbEKd'; //program push farmer wags
                }
                
                if($uid != '' && $proses['MemberIDInc']) {
                    $mID = $proses['MemberIDInc'];
                    $onlyNew = true;
                    $programs = $this->mmiddleware->getAllProgramWithView($uid);
                    if (count($programs) > 0) {
                        foreach ($programs as $progkeys => $program) {
                            $datas = $this->mmiddleware->getDataBy($onlyNew, $program['uid'], $mID);
                            $this->mmiddleware->syncDataPerProgram($datas, $program['uid']);
                        }
                    }
                }
            }
        } else {
            //update
            $proses = $this->mgrower->updateMemberWAGS($this->post());

            //Push ke DHIS
            if ($proses['success']) {

                if($proses['CountryCode'] == 'ID') {
                    $uid = 'QxauNvjcpBw'; // push by program
                } else {
                    $uid = 'zbLN28sbEKd'; //program push farmer wags
                }
                
                if($uid != '' && $proses['MemberIDInc']) {
                    $mID = $proses['MemberIDInc'];
                    $onlyNew = false;
                    $programs = $this->mmiddleware->getAllProgramWithView($uid);
                    if (count($programs) > 0) {
                        foreach ($programs as $progkeys => $program) {
                            $datas = $this->mmiddleware->getDataBy($onlyNew, $program['uid'], $mID);
                            $this->mmiddleware->syncDataPerProgram($datas, $program['uid']);
                        }
                    }
                }
            }
        }
        
        $this->response($proses, 200);
    }

    public function image_member_wags_post() {
        $this->load->library('awsfileupload');
        if ($this->post('opsiDisplay') == "insert") {
            //ketika insert

            if ($this->file['Koltiva_view_GrowerWAGS_FormMainGrower-MemberPhotoInput']['name'] != '') {
                $gambar = 'temp/' . $this->file['Koltiva_view_GrowerWAGS_FormMainGrower-MemberPhotoInput']['name'];
                $fileupload['Koltiva_view_GrowerWAGS_FormMainGrower-MemberPhotoInput'] = $this->file['Koltiva_view_GrowerWAGS_FormMainGrower-MemberPhotoInput'];

                $upload = move_upload($fileupload, 'images/member/' . $gambar);
                if (isset($upload['upload_data'])) {
                    $result['success'] = true;
                    $result['file'] = base_url().'/images/member/' . $gambar;
                    $result['filepath']   = '/images/member/' . $gambar;
                    $this->response($result, 200);
                } else {
                    echo 'false';
                    exit;
                }
            }
        }

        if ($this->post('opsiDisplay') == "update") {
            //ketika update

            $upload = $this->awsfileupload->upload($this->file['Koltiva_view_GrowerWAGS_FormMainGrower-MemberPhotoInput']['tmp_name'],$this->file['Koltiva_view_GrowerWAGS_FormMainGrower-MemberPhotoInput']['name'], AWSS3_FARMER_PATH, 'images');
            if ($upload['success'] == true) {
                if($this->awsfileupload->doesObjectExist($this->post('Koltiva_view_GrowerWAGS_FormMainGrower-MemberPhotoOld')) == true) {
                    $this->awsfileupload->delete($this->post('Koltiva_view_GrowerWAGS_FormMainGrower-MemberPhotoOld'));
                }else{
                    delete_file($this->post('Koltiva_view_GrowerWAGS_FormMainGrower-MemberPhotoOld'));
                }
                $prosesUpdate = $this->mgrower->updateFotoProfile($_POST["MemberID"],$upload['filenamepath']);
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

    //============================ Khusus WAGS End ======================================//

    public function combo_fam_member_name_get(){
        $data = $this->mgrower->getFamMemberName($this->get());
        $this->response($data, 200);
    }

    public function family_labour_postline_post() {
        $varPost = $this->post();

        //prep variabel (begin)
        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_FamilyLabourPostline_WinFormFamilyLabourPostline-Form-", '', $key);

            if (is_null($value)) {
                $value = NULL;
            }

            $paramPost[$keyNew] = str_replace(",", "", $value);
        }

        if ($paramPost['FamLabPostID'] == "") {
            //insert
            $proses = $this->mgrower->insertFamLabPostline($paramPost);
        } else {
            //update
            $proses = $this->mgrower->updateFamLabPostline($paramPost);
        }
        $this->response($proses, 200);
    }

    public function family_labour_postline_delete() {
        $FamLabPostID = (int) $this->delete('FamLabPostID');
        $proses = $this->mgrower->deleteFamLabPostline($FamLabPostID);
        $this->response($proses, 200);
    }

    public function member_family_labour_postline_data_get() {
        $data = $this->mgrower->getMemberFamilyLabourPostlineFormData($this->get('FamLabPostID'));
        $this->response($data, 200);
    }

    public function grid_family_labour_postline_get() {
        //set bahasa
        if ($_SESSION['language'] == "Indonesia") {
            $this->load->language('general', 'indonesia');
        } else {
            $this->load->language('general', 'english');
        }

        $data = $this->mgrower->getGridFamilyLabourPostline($this->get());
        $this->response($data, 200);
    }

    public function combo_survey_nr_family_labour_postline_get(){
        $data = $this->mgrower->getComboSurveyNrFamilyLabourPostline($this->get('from'));
        $this->response($data, 200);
    }

    /* farmer labour postline */

    public function combo_farm_labour_name_get(){
        $data = $this->mgrower->getFarmLabourName($this->get());
        $this->response($data, 200);
    }

    public function grid_labour_postline_get(){
        //set bahasa
        if ($_SESSION['language'] == "Indonesia") {
            $this->load->language('general', 'indonesia');
        } else {
            $this->load->language('general', 'english');
        }

        $data = $this->mgrower->getGridLabourPostline($this->get());
        $this->response($data, 200);
    }

    public function labour_postline_post(){
        $varPost = $this->post();

        //prep variabel (begin)
        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_FarmerLabourPostline_WinFormFarmerLabourPostline-Form-", '', $key);

            if (is_null($value)) {
                $value = NULL;
            }

            $paramPost[$keyNew] = str_replace(",", "", $value);
        }
        //prep variabel (end)

        if ($paramPost['LaboPostID'] == "") {
            //insert
            $proses = $this->mgrower->insertLabourPostline($paramPost);
        } else {
            //update
            $proses = $this->mgrower->updateLabourPostline($paramPost);
        }
        $this->response($proses, 200);
    }

    public function labour_postline_delete(){
        $LaboID = (int) $this->delete('LaboPostID');
        $proses = $this->mgrower->deleteLabourPostline($LaboID);
        $this->response($proses, 200);
    }

    public function member_labour_postline_form_data_get(){
        $data = $this->mgrower->getMemberLabourPostlineFormData($this->get('LaboPostID'));
        $this->response($data, 200);
    }
}

?>