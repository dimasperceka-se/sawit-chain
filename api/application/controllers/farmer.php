<?php

defined('BASEPATH') or exit('No direct script access allowed');

use mikehaertl\wkhtmlto\Pdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\ErrorCorrectionLevel;

class Farmer extends REST_Controller {

    public function __construct() {
        $this->file = $_FILES;
        parent::__construct();
        $this->load->model('farmer/mfarmer');
    }

    public function farmer_filter_subdistrict_get() {
        $data = $this->mfarmer->getFilterAdvSubdistrict($this->get('district'));
        if ($data) {
            $this->response($data, 200);
        }
    }

    public function farmer_filter_village_get() {
        $data = $this->mfarmer->getFilterAdvVillage($this->get('subdistrict'));
        if ($data) {
            $this->response($data, 200);
        }
    }

    public function farmerls_get() {
        //set bahasa
        if($_SESSION['language'] == "Indonesia"){
            $this->load->language('general', 'indonesia');
        }else{
            $this->load->language('general', 'english');
        }

        //cek apakah simple search / advanced
        if ($this->get('opsiSearch') == "adv") {
            $paramSearch = array(
                "farSpecific" => $this->get('farSpecific'),
                "prov" => $this->get('prov'),
                "parAdvNama" => $this->get('parAdvNama'),
                "parAdvDistrict" => $this->get('parAdvDistrict'),
                "parAdvSubDistrict" => $this->get('parAdvSubDistrict'),
                "parAdvVillage" => $this->get('parAdvVillage'),
                "parAdvOpAge" => $this->get('parAdvOpAge'),
                "parAdvAge" => $this->get('parAdvAge'),
                "parAdvSurvey" => $this->get('parAdvSurvey'),
                "parAdvOpJumlahKebun" => $this->get('parAdvOpJumlahKebun'),
                "parAdvJumlahKebun" => $this->get('parAdvJumlahKebun'),
                "parAdvOpUkuranKebun" => $this->get('parAdvOpUkuranKebun'),
                "parAdvOpProduksi" => $this->get('parAdvOpProduksi'),
                "parAdvProduksi" => $this->get('parAdvProduksi'),
                "parAdvUkuranKebun" => $this->get('parAdvUkuranKebun'),
                "parAdvLandCertificate" => $this->get('parAdvLandCertificate'),
                "parAdvCertified" => $this->get('parAdvCertified'),
                "parAdvCertifiedYear" => $this->get('parAdvCertifiedYear'),
                "parAdvNursery" => $this->get('parAdvNursery'),
                "parAdvSCE" => $this->get('parAdvSCE'),
                "parAdvJoinGAP" => $this->get('parAdvJoinGAP'),
                "parAdvJoinGNP" => $this->get('parAdvJoinGNP'),
                "parAdvJoinGFP" => $this->get('parAdvJoinGFP'),
                "parAdvBank" => $this->get('parAdvBank'),
                "parAdvStatus" => $this->get('parAdvStatus'),
                "start" => $this->get('start'),
                "limit" => $this->get('limit'),
            );
            $data = $this->mfarmer->readFarmersAdvanced($paramSearch);
            if ($data) {
                $this->response($data, 200);
            } else {
                $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
            }
        } else {
            $data = $this->mfarmer->readFarmers($this->get('farSpecific'),$this->get('sert'), $this->get('prov'), $this->get('kab'), $this->get('kec'), $this->get('key'), $_SESSION['userid'], $_SESSION['PartnerID'], $_SESSION['FlagAccess'], $this->get('ord'), $this->get('sort'), $this->get('start'), $this->get('limit')
            );
            if ($data) {
                $this->response($data, 200);
            } else {
                $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
            }
        }
    }

    public function farmerl_excel_post() {
        $paramSearch = array(
            "farSpecific" => $this->post('farSpecific'),
            "prov" => $this->post('prov'),
            "parAdvNama" => $this->post('parAdvNama'),
            "parAdvDistrict" => $this->post('parAdvDistrict'),
            "parAdvSubDistrict" => $this->post('parAdvSubDistrict'),
            "parAdvVillage" => $this->post('parAdvVillage'),
            "parAdvOpAge" => $this->post('parAdvOpAge'),
            "parAdvAge" => $this->post('parAdvAge'),
            "parAdvSurvey" => $this->post('parAdvSurvey'),
            "parAdvOpJumlahKebun" => $this->post('parAdvOpJumlahKebun'),
            "parAdvJumlahKebun" => $this->post('parAdvJumlahKebun'),
            "parAdvOpUkuranKebun" => $this->post('parAdvOpUkuranKebun'),
            "parAdvOpProduksi" => $this->post('parAdvOpProduksi'),
            "parAdvProduksi" => $this->post('parAdvProduksi'),
            "parAdvUkuranKebun" => $this->post('parAdvUkuranKebun'),
            "parAdvLandCertificate" => $this->post('parAdvLandCertificate'),
            "parAdvCertified" => $this->post('parAdvCertified'),
            "parAdvCertifiedYear" => $this->post('parAdvCertifiedYear'),
            "parAdvNursery" => $this->post('parAdvNursery'),
            "parAdvSCE" => $this->post('parAdvSCE'),
            "parAdvJoinGAP" => $this->get('parAdvJoinGAP'),
            "parAdvJoinGNP" => $this->post('parAdvJoinGNP'),
            "parAdvJoinGFP" => $this->post('parAdvJoinGFP'),
            "parAdvBank" => $this->post('parAdvBank'),
            "parAdvStatus" => $this->post('parAdvStatus'),
            "no_limit" => "yes",
        );
        $data = $this->mfarmer->readFarmersAdvanced($paramSearch);

        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';

        $mem_ini = ini_get('memory_limit');
        ini_set('memory_limit', '1048576M');

        //=============== MULAI TULIS EXCEL (BEGIN) ===================================================================//
        $namaFile = "project_report_contract_data.xls";

        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("PT Koltiva")
                ->setLastModifiedBy("PT Koltiva")
                ->setTitle("List Data Export Petani CocoaTrace")
                ->setSubject("List Data Export Petani CocoaTrace")
                ->setDescription("List Data Export Petani CocoaTrace")
                ->setKeywords("List Data Export Petani CocoaTrace")
                ->setCategory("List Data Export Petani CocoaTrace");

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('List');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        //set width column
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(11); //farmer_Id
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(35); //name
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(19); //group_name
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(19); //village
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(19); //sub district
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15); //date updated
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15); //last survey
        //set style
        $styleFont = array(
            'font' => array(
                'name' => 'Arial',
                'size' => '9',
            ),
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
            ),
        );

        $styleFontBold = array(
            'font' => array(
                'name' => 'Arial',
                'size' => '9',
                'bold' => true,
            ),
        );

        $styleFontBoldTitle = array(
            'font' => array(
                'name' => 'Arial',
                'size' => '9',
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
        );

        $styleFontBoldHeader = array(
            'font' => array(
                'name' => 'Arial',
                'size' => '9',
                'bold' => true,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '8DB4E3'),
            ),
        );
        $styleFontBoldBgRedCenter = array(
            'font' => array(
                'name' => 'Arial',
                'size' => '9',
                'bold' => true,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'C0504D'),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $styleBorderFull = array(
            'borders' => array(
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );

        //tulis judul
        $objPHPExcel->getActiveSheet()->setCellValue('B2', 'List Farmer');
        $objPHPExcel->getActiveSheet()->getStyle('B2')->applyFromArray($styleFontBoldTitle);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B2:I2');

        $objPHPExcel->getActiveSheet()->setCellValue('B4', 'No');
        $objPHPExcel->getActiveSheet()->setCellValue('C4', lang('ID'));
        $objPHPExcel->getActiveSheet()->setCellValue('D4', lang('Name'));
        $objPHPExcel->getActiveSheet()->setCellValue('E4', lang('Group Name'));
        $objPHPExcel->getActiveSheet()->setCellValue('F4', lang('Desa'));
        $objPHPExcel->getActiveSheet()->setCellValue('G4', lang('Kecamatan'));
        $objPHPExcel->getActiveSheet()->setCellValue('H4', lang('Date Updated'));
        $objPHPExcel->getActiveSheet()->setCellValue('I4', lang('Last Survey'));
        $objPHPExcel->getActiveSheet()->getStyle('B4:I4')->applyFromArray($styleFontBoldHeader);
        $objPHPExcel->getActiveSheet()->getStyle('B4:I4')->applyFromArray($styleBorderFull, false);

        $rowStart = 5;
        for ($i = 0; $i < count($data['data']); $i++) {
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $rowStart, $i + 1);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $rowStart, $data['data'][$i]['id']);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $rowStart, $data['data'][$i]['PersonNm']);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $rowStart, $data['data'][$i]['GroupName']);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $rowStart, $data['data'][$i]['Desa']);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $rowStart, $data['data'][$i]['Kecamatan']);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $rowStart, $data['data'][$i]['DateUpdated']);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $rowStart, $data['data'][$i]['DateSurvey']);
            $objPHPExcel->getActiveSheet()->getStyle('B' . $rowStart . ':I' . $rowStart)->applyFromArray($styleFont);
            $objPHPExcel->getActiveSheet()->getStyle('B' . $rowStart . ':I' . $rowStart)->applyFromArray($styleBorderFull, false);
            $rowStart++;
        }
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('list_farmer_cocoatrace.xls');
        ini_set('memory_limit', $mem_ini);
        $this->response(array('success' => TRUE, 'filenya' => base_url() . 'list_farmer_cocoatrace.xls'), 200);
        exit;
        //=============== MULAI TULIS EXCEL (END)   ===================================================================//
    }

    public function area_get() {
        $data = $this->mfarmer->readArea($this->get('key'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function farmerl_cpg_post() {
        if (!$this->post('FarmerGroupID')) {
            $this->response(null, 400);
        }

        $result = $this->mfarmer->createFarmerByCpg($this->post('FarmerGroupID'));
        if ($result) {
            $this->response($result, 200);
        } else {
            $this->response(array('error' => 'Farmer could not be found'), 404);
        }
    }

    public function farmerl_image_post() {
        if ($this->post('id') == "") {
            //insert
            if ($this->file['Photo']['name'] != '') {
                $gambarBaru = date('Ymdhis') . '_' . $this->file['Photo']['name'];
                $upload = move_upload($this->file, 'images/Photo/' . $gambarBaru);
                if (isset($upload['upload_data'])) {
                    $result['success'] = true;
                    $result['file'] = $gambarBaru;
                    $this->response($result, 200);
                } else {
                    $result['success'] = false;
                    $result['file'] = '';
                    $this->response($result, 200);
                }
            }
        }

        if ($this->post('id') != "") {
            //update
            if ($this->file['Photo']['name'] != '') {
                //ganti nama file jadi farmer id ==== begin
                //ambil ext poto
                $tempArr = explode('.', $this->file['Photo']['name']);
                $extPoto = end($tempArr);
                $gambarBaru = 'frm_' . $this->post('id') . '_' . date('Ymdhis') . '.' . strtolower($extPoto);

                //ambil nama propinsi
                $ProvinceID = substr($this->post('id'), 0, 2);
                $namaProvince = $this->mfarmer->readProvinsiNama($ProvinceID);
                //ganti nama file jadi farmer id ==== end
                $upload = move_upload($this->file, 'images/Photo/' . $namaProvince . '/' . $gambarBaru);
                if (isset($upload['upload_data'])) {
                    $result['success'] = true;
                    $result['file'] = $namaProvince . '/' . $gambarBaru;
                    $this->response($result, 200);
                } else {
                    $result['success'] = false;
                    $result['file'] = '';
                    $this->response($result, 200);
                }
            }
        }
    }

    public function farmerl_learning_contract_post() {
        if ($this->file['LearningContractFile']['name'] != '') {
            $pdf = date('Ymdhis') . '_' . $this->post('id') . '_' . $this->file['LearningContractFile']['name'];
            $upload = move_upload($this->file, 'files/learning_contract_temp/' . $pdf);
            if (isset($upload['upload_data'])) {
                //unlink('files/learning_contract/'.$this->post('LearningContractFile_old'));
                $result['success'] = true;
                $result['file'] = $pdf;
                $this->response($result, 200);
            }
        }
    }

    public function farmerl_certification_contract_post() {
        if ($this->file['CertContractFile']['name'] != '') {
            $pdf = date('Ymdhis') . '_' . $this->post('id') . '_' . $this->file['CertContractFile']['name'];
            $upload = move_upload($this->file, 'files/certification_contract_temp/' . $pdf);
            if (isset($upload['upload_data'])) {
                //unlink('files/learning_contract/'.$this->post('LearningContractFile_old'));
                $result['success'] = true;
                $result['file'] = $pdf;
                $this->response($result, 200);
            }
        }
    }

    public function farmerl_learning_contract_delete() {
        //if(!$this->delete('LCFnew')) $this->response(NULL, 400);
        if (unlink('files/learning_contract_temp/' . $this->delete('LCFnew'))) {
            $result['success'] = true;
            $this->response($result, 200);
        }
    }

    public function farmerl_certification_contract_delete() {
        //if(!$this->delete('LCFnew')) $this->response(NULL, 400);
        if (unlink('files/certification_contract_temp/' . $this->delete('CCFnew'))) {
            $result['success'] = true;
            $this->response($result, 200);
        }
    }

    public function family_post() {
        // echo '<pre>POST'; print_r($this->post(null)); echo '</pre>'; exit;
        if (!$this->post('AnggotaName')) {
            $this->response(null, 400);
        }

        $data = $this->mfarmer->createFamily($this->post('FarmerID'), $this->post('AnggotaName'), $this->post('HubunganKeluarga'), $this->post('WorkGardenStatus'), $this->post('ActivityType'), $this->post('TotalWorkingHrsPerDay'), $this->post('TotalWorkingHrs'), $this->post('WageAmount'), $this->post('DateOfBirth'), $this->post('AnggotaAge'), $this->post('AnggotaGender'), $this->post('StatusSekolah'), $_SESSION['userid']);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Farmer could not be found'), 404);
        }
    }

    public function family_put() {
        // echo '<pre>PUT'; print_r($this->put(null)); echo '</pre>'; exit;
        if (!$this->put('FamilyID')) {
            $this->response(null, 400);
        }

        // $HubunganKeluarga   = is_numeric($this->put('HubunganKeluarga')) ? $this->put('HubunganKeluarga') : $this->put('hubungan');
        // $AnggotaGender      = is_numeric($this->put('AnggotaGender')) ? $this->put('AnggotaGender') : $this->put('kelamin');
        // $StatusSekolah      = is_numeric($this->put('StatusSekolah')) ? $this->put('StatusSekolah') : $this->put('sekolah');

        $data = $this->mfarmer->updateFamily($this->put('AnggotaName'), $this->put('HubunganKeluarga'), $this->put('WorkGardenStatus'), $this->put('ActivityType'), $this->put('TotalWorkingHrsPerDay'), $this->put('TotalWorkingHrs'), $this->put('WageAmount'), $this->put('DateOfBirth'), $this->put('AnggotaAge'), $this->put('AnggotaGender'), $this->put('StatusSekolah'), $_SESSION['userid'], $this->put('FamilyID'));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Farmer could not be found'), 404);
        }
    }

    public function family_delete() {
        if (!$this->delete('FamilyId')) {
            $this->response(null, 400);
        }

        $data = $this->mfarmer->deleteFamily($this->delete('FamilyId'), $this->delete('reason'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Farmer could not be delete'), 404);
        }
    }

    public function farmerl_keluargas_get() {
        $data = $this->mfarmer->readFarmerKeluargas($this->get('id'), $this->get('start'), $this->get('limit'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function farmerl_get() {
        if (!$this->get('id')) {
            $this->response(null, 400);
        }

        $data = $this->mfarmer->readFarmer($this->get('id'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Farmer could not be found'), 404);
        }
    }

    public function farmer_nursery_get() {
        if (!$this->get('id')) {
            $this->response(null, 400);
        }

        $data = $this->mfarmer->readFarmerNursery($this->get('id'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Farmer could not be found'), 404);
        }
    }

    public function farmerl_garden_get() {
        if (!$this->get('FarmerID')) {
            $this->response(null, 400);
        }

        $data = $this->mfarmer->readFarmerGarden($this->get('FarmerID'), $this->get('GardenNr'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Farmer could not be found'), 404);
        }
    }

    public function ProvinsiNama_get() {
        if (!$this->get('id')) {
            $this->response('',200);
        }

        $Provinsi = substr($this->get('id'), 0, 2);
        $data = $this->mfarmer->readProvinsiNama($Provinsi);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Farmer could not be found'), 404);
        }
    }

    public function farmerl_post() {
        /*
          if ($this->file['Photo']['name']!='') {
          $gambar = $this->post('path').'/'.date('Ymdhis').'_'.$this->file['Photo']['name'];
          if (!is_dir($this->post('path'))) mkdir($this->post('path'));
          //move_uploaded_file($this->file['Photo']['tmp_name'], 'images/Photo/'.$this->post('path').'/'.$gambar);
          } else {
          $gambar = null;
          }
         */

        //untuk farmer-mars.js (begin)
        if($this->post('submitForm') == "mars"){
            $BirthDttm = $this->post('BirthDttm');
            $BirthDttm = $BirthDttm."-01-01";
        }else{
            $BirthDttm = $this->post('BirthDttm');
        }
        //untuk farmer-mars.js (end)

        $data = $this->mfarmer->createFarmer(
                $this->post('Ssn'), $this->post('PersonNm'), $BirthDttm, $this->post('BirthPlace'), $gambar, $this->post('Gender'), $this->post('Address'), $this->post('Desa'), $this->post('ZipCd'), $this->post('Email'), $this->post('BloodT'), $this->post('MaritalSt'), $this->post('Education'), $this->post('NationalityNm'), $this->post('Handphone'), $this->post('FarmerGroupID'), $this->post('LahanKosong'), $this->post('Muge'), $this->post('ActiveMemberCooperation'), $this->post('KeyFarmer'), $this->post('DemoPlot'), $this->post('OtherTraining'), $this->post('CPGmembership'), $this->post('OtherTrainingSiapa'), $this->post('OtherTrainingTahun'), $this->post('OtherTrainingLama'), $this->post('DemoPlotLama'), $this->post('DemoPlotRehab'), $this->post('FarmerGroupFunctionsID'), $this->post('DateCollection'), $this->post('Kabupaten'), $this->post('LahanKakao'), $this->post('LahanProduksiLain'), $this->post('TotalLahan'), $this->post('KebunKakao'), $this->post('DateUpdated'), $_SESSION['userid'], $this->post('StatusFarmer'), $this->post('rgDeceased'), $this->post('FamilyMemberID'), $this->post('MovedLeftArea'), $this->post('SwitchOtherCrop'), $this->post('Photo_old'),$this->post('ExtFarmerID'),$this->post('RtRw')
        );

        if ($data) {
            $this->response($data, 200);

            /**
             * Add Update data to DHIS using FarmerID
             * @author Ardi <ardiantoro@koltiva.com>
             */

            //Get FarmerID
            $FarmerID = $data['id'];

            //Load model from dhis module
            $this->load->model('dhis/mdsync', '_dsync');

            //Get farmer data from view view_program_farmer
            $farmers = $this->_dsync->getDataByDistrict(false, false, 'QxauNvjcpBw', $FarmerID);

            //Found? sync the data to dhis
            if ($farmers) {
                $this->_dsync->syncDataPerProgram($farmers, 'QxauNvjcpBw');
            }
            //End

        } else {
            $this->response(array('error' => 'Farmer could not be found'), 404);
        }
    }

    public function farmerl_delete() {
        if (!$this->delete('farmer_id')) {
            $this->response(null, 400);
        }

        $data = $this->mfarmer->deleteFarmer($this->delete('reason'), $this->delete('farmer_id'), $_SESSION['userid']);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Farmer could not be delete'), 404);
        }
    }

    // handle post general panel farmer
    public function farmerlu_post() {
        if (!$this->post('id')) {
            $this->response(null, 400);
        }

        if ($this->file['Photo']['name'] != '') {
            $gambar = $this->post('path') . '/' . date('Ymdhis') . '_' . $this->file['Photo']['name'];
            if (!is_dir($this->post('path'))) {
                mkdir($this->post('path'));
            }

            move_upload($this->file, 'images/Photo/' . $this->post('path') . '/' . $gambar);
        } else {
            $gambar = $this->post('Photo_old');
        }

        //untuk farmer-mars.js (begin)
        if($this->post('submitForm') == "mars"){
            $BirthDttm = $this->post('BirthDttm');
            $BirthDttm = $BirthDttm."-01-01";
        }else{
            $BirthDttm = $this->post('BirthDttm');
        }
        //untuk farmer-mars.js (end)

        $extension = $this->mfarmer->updateFarmer(
                $this->post('Ssn'), $this->post('PersonNm'), $BirthDttm, $this->post('BirthPlace'), $gambar, $this->post('Gender'), $this->post('Address'), $this->post('Desa'), $this->post('ZipCd'), $this->post('Email'), $this->post('BloodT'), $this->post('MaritalSt'), $this->post('Education'), $this->post('NationalityNm'), $this->post('Handphone'), $this->post('FarmerGroupID'), $this->post('LahanKosong'), $this->post('Muge'), $this->post('ActiveMemberCooperation'), $this->post('KeyFarmer'), $this->post('DemoPlot'), $this->post('OtherTraining'), $this->post('CPGmembership'), $this->post('OtherTrainingSiapa'), $this->post('OtherTrainingTahun'), $this->post('OtherTrainingLama'), $this->post('DemoPlotLama'), $this->post('DemoPlotRehab'), $this->post('FarmerGroupFunctionsID'), $this->post('id_person'), $this->post('DateCollection'), $this->post('LahanKakao'), $this->post('LahanProduksiLain'), $this->post('TotalLahan'), $this->post('KebunKakao'), $this->post('DateUpdated'), $this->post('id'), $_SESSION['userid'], $this->post('StatusFarmer'), $this->post('ReasonStatusFarmer'), $this->post('rgDeceased'), $this->post('FamilyMemberID'), $this->post('MovedLeftArea'), $this->post('SwitchOtherCrop'), $this->post('AccountBeneficiary'), $this->post('BankID'), $this->post('BankBranch'), $this->post('AccountNumber'), $this->post('LearningContractFile_old'), $this->post('LearningContractFile_new'), $this->post('CertContractFile_old'), $this->post('CertContractFile_new'), $this->post('ExtFarmerID')
        );

        if ($extension) {

            if ($this->post('LearningContractFile_new') != "") {
                //delete file learning contract yg lama
                unlink('files/learning_contract/' . $this->post('LearningContractFile_old'));
                if (copy('files/learning_contract_temp/' . $this->post('LearningContractFile_new'), 'files/learning_contract/' . $this->post('LearningContractFile_new'))) {
                    unlink('files/learning_contract_temp/' . $this->post('LearningContractFile_new'));
                }
            }

            if ($this->post('CertContractFile_new') != "") {
                //delete file learning contract yg lama
                unlink('files/certification_contract/' . $this->post('CertContractFile_old'));
                if (copy('files/certification_contract_temp/' . $this->post('CertContractFile_new'), 'files/certification_contract/' . $this->post('CertContractFile_new'))) {
                    unlink('files/certification_contract_temp/' . $this->post('CertContractFile_new'));
                }
            }

            /**
             * Add Update data to DHIS using FarmerID
             * @author Ardi <ardiantoro@koltiva.com>
             */

            //Get FarmerID
            $FarmerID = $this->post('id');

            //Load model from dhis module
            $this->load->model('dhis/mdsync', '_dsync');

            //Get farmer data from view view_program_farmer
            $farmers = $this->_dsync->getDataByDistrict(false, false, 'QxauNvjcpBw', $FarmerID);

            //Found? sync the data to dhis
            if ($farmers) {
                @$this->_dsync->syncDataPerProgram($farmers);
            }
            //End

            $this->response($extension, 200);

        }

        $this->response(array('error' => 'Extension could not be found'), 404);

    }

    public function farmerl_harvest_get() {
        if ($this->get('surveyNr') == 'Tambah Baru') {
            return;
        }

        if (!$this->get('id')) {
            $this->response(null, 400);
        }

        $data = $this->mfarmer->readFarmerHarvest($this->get('id'), $this->get('surveyNr'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Farmer could not be found'), 404);
        }
    }

    public function farmerl_saving_pilot_get() {
        if ($this->get('surveyNr') == 'Tambah Baru') {
            return;
        }

        if (!$this->get('id')) {
            $this->response(null, 400);
        }

        $data = $this->mfarmer->readFarmerSavingPilot($this->get('id'), $this->get('surveyNr'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Farmer could not be found'), 404);
        }
    }

    public function harvest_post() {
        $param['DryingDaysSellPrice'] = str_replace(",", "", $this->post('DryingDaysSellPrice'));

        $data = $this->mfarmer->updateHarvest($this->post('DateCollection'), $this->post('AnggotaKerjaKebun'), $this->post('BuruhSeasonal'), $this->post('BuruhSeasonalRupiah'), $this->post('BuruhSeasonalPersen'), $this->post('BuruhFulltime'), $this->post('BuruhFulltimeRupiah'), $this->post('BuruhFulltimePersen'), $this->post('Fermentation'), $this->post('FermentationDays'), $this->post('SunDryingSemen'), $this->post('DryingAlat'), $this->post('DryingDays'), $param['DryingDaysSellPrice'], $this->post('CocoaBuyers'), $this->post('NoFermentation'), $this->post('Sortasi'), $this->post('NoSortasi'), $this->post('SunDryingAspal'), $this->post('JemurYesNo'), $this->post('TidakJemur'), $this->post('SunDryingAlas'), $this->post('hSurveyNr'), $this->post('AntarSendiri'), $this->post('Distance'), $this->post('Comment'), $this->post('DryMoistureStandard'), $this->post('ImplementBeanRemainDry'), $this->post('BeanDryHygienic'), $this->post('hFarmerID'), $_SESSION['userid']);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Farmer could not be found'), 404);
        }
    }

    public function saving_pilot_post() {
        $data = $this->mfarmer->updateSavingPilot($this->post('spFarmerID'), $this->post('spSurveyNr'), $this->post('spFamilyMembers'), $this->post('spLandSizeHa'), $this->post('spAmountCocoaIncome'), $this->post('spAmountOtherIncome'), $this->post('spSavingYesNo'), $this->post('spAmountSaving'), $this->post('spLoanYesNo'), $this->post('spAccountNumber'), $this->post('spAge'), $this->post('spMarriedYesNo'), $this->post('spInterviewDate'), $_SESSION['userid']);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Farmer could not be found'), 404);
        }
    }

    public function garden_get() {
        if (!$this->get('id')) {
            $this->response(null, 400);
        }

        $data = $this->mfarmer->readGarden($this->get('id'), $this->get('GardenNr'), $this->get('gSurveyNr'), $this->get('dataForm'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('success' => false), 200);
        }
    }

    public function garden_all_post() {
        // echo '<pre>'; print_r($this->post(NULL)); echo '</pre>'; exit;
        //if(!$this->post('name')) $this->response(NULL, 400);
        $garden = $this->mfarmer->updateGarden($this->post('gFarmerID'), $this->post('GardenNr'), $this->post('gDateCollection'), $this->post('Latitude'), $this->post('LatMin'), $this->post('LatSec'), $this->post('Longitude'), $this->post('LongMin'), $this->post('LongSec'), $this->post('Elevation'), $this->post('OwnershipCocoa'), $this->post('TahunTanamanCocoa'), $this->post('GardenDistance'), $this->post('GardenHaUnCertified'), $this->post('Production'), $this->post('PanenBiasaMonths'), $this->post('PanenBiasaPanenMonth'), $this->post('PanenBiasaKg'), $this->post('PanenTrekMonths'), $this->post('PanenTrekPanenMonth'), $this->post('PanenTrekKg'), $this->post('PanenRayaMonths'), $this->post('PanenRayaPanenMonth'), $this->post('PanenRayaKg'), $this->post('TimeHarvestBiasa'), $this->post('TimeHarvestTrek'), $this->post('TimeHarvestRaya'), $this->post('LandOwner'), $this->post('LandCertificate'), $this->post('PohonTBM'), $this->post('PohonTM'), $this->post('PohonRehab'), $this->post('GraftedTrees'), $this->post('ReplantedTrees'), $this->post('RoadCondition'), $this->post('Comment'), $this->post('TSH858'), $this->post('RCC70'), $this->post('RCC71'), $this->post('RCC72'), $this->post('RCC73'), $this->post('Lokal'), $this->post('S1'), $this->post('S2'), $this->post('S3'), $this->post('ICRRI3'), $this->post('ICRRI4'), $this->post('ICRRI5'), $this->post('CloneLain'), $this->post('Gamal'), $this->post('Kelapa'), $this->post('Durian'), $this->post('Pinang'), $this->post('Karet'), $this->post('JackFruit'), $this->post('Lamtoro'), $this->post('Mahoni'), $this->post('Pisang'), $this->post('Rambutan'), $this->post('Mangga'), $this->post('Langsat'), $this->post('ShadeLain'), $this->post('ShadeTreesNr'), $this->post('TimeHarvest'), $this->post('HarvestAwal'), $this->post('HarvestMasak'), $this->post('HarvestHama'), $this->post('PruningPlants'), $this->post('FrequentPruning'), $this->post('HighPruning'), $this->post('PruningProtectPlants'), $this->post('FrequentPruningProtect'), $this->post('CleanSkin'), $this->post('HowToCleanSkin'), $this->post('OrganicKotoran'), $this->post('OrganicResidu'), $this->post('OrganicMembeli'), $this->post('TidakMemakaiOrganic'), $this->post('Urea'), $this->post('TSP'), $this->post('NPK'), $this->post('KCL'), $this->post('TidakMemakaiKimia'), $this->post('FrequentFertilizationOrganic'), $this->post('DoseFertilizerOrganic'), $this->post('FrequentFertilizationKimia'), $this->post('DoseFertilizerKimia'), $this->post('PakaiKompos'), $this->post('FrequentFertilizationKompos'), $this->post('DoseFertilizerKompos'), $this->post('FrUrea'), $this->post('FrTsp'), $this->post('FrNpk'), $this->post('FrKcl'), $this->post('DpUrea'), $this->post('DoTsp'), $this->post('DoNpk'), $this->post('DoKcl'), $this->post('FrLain'), $this->post('DoLain'), $this->post('FrZa'), $this->post('DoZa'), $this->post('KimiaDana'), $this->post('KimiaSupplier'), $this->post('KimiaDilatih'), $this->post('KimiaTidakSuka'), $this->post('KimiaTidakTersedia'), $this->post('KimiaLain'), $this->post('HamaBPK'), $this->post('HamaHelopeltis'), $this->post('HamaBatang'), $this->post('PenyakitKanker'), $this->post('PenyakitBusuk'), $this->post('PenyakitUpas'), $this->post('PenyakitAkar'), $this->post('PenyakitVSD'), $this->post('PenyakitAntraknose'), $this->post('Herbisida'), $this->post('MerekHerbisida'), $this->post('FrequentHerbisida'), $this->post('DoseHerbisida'), $this->post('Herbisida1'), $this->post('Herbisida2'), $this->post('Herbisida3'), $this->post('Herbisida4'), $this->post('Herbisida5'), $this->post('Herbisida6'), $this->post('Herbisida7'), $this->post('Herbisida8'), $this->post('Herbisida9'), $this->post('Herbisida10'), $this->post('Herbisida11'), $this->post('Herbisida12'), $this->post('Herbisida13'), $this->post('Herbisida14'), $this->post('Herbisida15'), $this->post('Herbisida16'), $this->post('Herbisida17'), $this->post('Herbisida18'), $this->post('Herbisida19'), $this->post('Herbisida20'), $this->post('Herbisida21'), $this->post('Herbisida22'), $this->post('Herbisida23'), $this->post('Herbisida24'), $this->post('Herbisida25'), $this->post('Herbisida26'), $this->post('Herbisida27'), $this->post('Herbisida28'), $this->post('Herbisida29'), $this->post('Insectisida'), $this->post('MerekInsectisida'), $this->post('FrequentInsectisida'), $this->post('DoseInsectisida'), $this->post('Insectisida1'), $this->post('Insectisida2'), $this->post('Insectisida3'), $this->post('Insectisida4'), $this->post('Insectisida5'), $this->post('Insectisida6'), $this->post('Insectisida7'), $this->post('Insectisida8'), $this->post('Insectisida9'), $this->post('Insectisida10'), $this->post('Insectisida11'), $this->post('Insectisida12'), $this->post('Insectisida13'), $this->post('Insectisida14'), $this->post('Insectisida15'), $this->post('Insectisida16'), $this->post('Insectisida17'), $this->post('Insectisida18'), $this->post('Insectisida19'), $this->post('Insectisida20'), $this->post('Insectisida21'), $this->post('Insectisida22'), $this->post('Insectisida23'), $this->post('Fungisida'), $this->post('MerekFungisida'), $this->post('FrequentFungisida'), $this->post('DoseFungisida'), $this->post('Fungisida1'), $this->post('Fungisida2'), $this->post('Fungisida3'), $this->post('Fungisida4'), $this->post('Fungisida5'), $this->post('Fungisida6'), $this->post('Fungisida7'), $this->post('Fungisida8'), $this->post('Fungisida9'), $this->post('Fungisida10'), $this->post('Fungisida11'), $this->post('Fungisida12'), $this->post('Fungisida13'), $this->post('APD'), $this->post('TempatSimpanPestisida'), $this->post('BuangKemasanPestisida'), $this->post('TopGraftedTrees'),$this->post('BeanGraftedTrees'), $this->post('GraftedTreesTahun'), $this->post('TopGraftedTreesTahun'), $this->post('BeanGraftedTreesTahun'), $this->post('ReplantedTreesTahun'), $this->post('M01'), $this->post('M06'), $this->post('THR'), $this->post('RCL'), $this->post('J45'), $this->post('gSurveyNr'), $this->post('LatDeg'), $this->post('LatMin'), $this->post('LatSec'), $this->post('LongDeg'), $this->post('LongMin'), $this->post('LongSec'), $_SESSION['userid'], $this->post('FrKomposKandang'), $this->post('FrKomposCair'), $this->post('FrKomposGranula'), $this->post('DoKomposKandang'), $this->post('DoKomposCair'), $this->post('DoKomposGranula'), $this->post('kTBM'), $this->post('kTM'), $this->post('kTR'), $this->post('pTBM'), $this->post('pTM'), $this->post('pTR'), $this->post('TSH858Nr'), $this->post('RCC70Nr'), $this->post('RCC71Nr'), $this->post('RCC72Nr'), $this->post('RCC73Nr'), $this->post('LokalNr'), $this->post('S1Nr'), $this->post('S2Nr'), $this->post('S3Nr'), $this->post('ICRRI3Nr'), $this->post('ICRRI4Nr'), $this->post('ICRRI5Nr'), $this->post('M01Nr'), $this->post('M06Nr'), $this->post('THRNr'), $this->post('RCLNr'), $this->post('J45Nr'), $this->post('CloneLainNr'), $this->post('Cengkeh'), $this->post('Sawit'), $this->post('Aren'), $this->post('Pala'), $this->post('Kemiri'), $this->post('Alpukat'), $this->post('Sukun'), $this->post('Pepaya'), $this->post('Manggis'), $this->post('Jeruk'), $this->post('Jati'), $this->post('Biti'), $this->post('Uru'), $this->post('Jabon'), $this->post('Petai'), $this->post('Jengkol'), $this->post('KelapaNr'), $this->post('PinangNr'), $this->post('KaretNr'), $this->post('JackFruitNr'), $this->post('PisangNr'), $this->post('RambutanNr'), $this->post('ManggaNr'), $this->post('LangsatNr'), $this->post('DurianNr'), $this->post('MahoniNr'), $this->post('GamalNr'), $this->post('LamtoroNr'), $this->post('CengkehNr'), $this->post('SawitNr'), $this->post('ArenNr'), $this->post('PalaNr'), $this->post('KemiriNr'), $this->post('AlpukatNr'), $this->post('SukunNr'), $this->post('PepayaNr'), $this->post('ManggisNr'), $this->post('JerukNr'), $this->post('JatiNr'), $this->post('BitiNr'), $this->post('UruNr'), $this->post('JabonNr'), $this->post('PetaiNr'), $this->post('JengkolNr'), $this->post('ShadeLainNr'), $this->post('JambuMente'), $this->post('Kapok'), $this->post('Jambu'), $this->post('Kedondong'), $this->post('Cempedak'), $this->post('Sengon'), $this->post('JambuMenteNr'), $this->post('KapokNr'), $this->post('JambuNr'), $this->post('KedondongNr'), $this->post('CempedakNr'), $this->post('SengonNr'),
                //$this->post('isCertification'),
                $this->post('Year'), $this->post('CandidateSelection'), $this->post('ICSDate'), $this->post('ExternalDate'), $this->post('Certification'), $this->post('CertificationHolderJenis'), $this->post('CertificationHolderID'), $this->post('StatusAudit'), $this->post('DateRevisionAudit'), $this->post('CommentAudit'), $this->post('RecommendationAudit'), $this->post('RACertQuestion1'), $this->post('RACertQuestion2'), $this->post('RACertQuestion3'), $this->post('RACertQuestion4'), $this->post('RACertQuestion5'), $this->post('RACertQuestion6'), $this->post('RACertQuestion7'), $this->post('RACertQuestion8'), $this->post('RACertQuestion9'), $this->post('RACertQuestion10'), $this->post('RACertQuestion11'), $this->post('RACertQuestion12'), $this->post('RACertQuestion13'), $this->post('RACertQuestion14A'), $this->post('RACertQuestion14B'), $this->post('RACertQuestion14C'), $this->post('RACertQuestion14D'), $this->post('RACertQuestion15'), $this->post('RACertQuestion16'), $this->post('RACertQuestion17'), $this->post('RACertQuestion18'), $this->post('RACertQuestion19'), $this->post('RACertQuestion20'), $this->post('RACertQuestion21'), $this->post('RACertQuestion22'), $this->post('RACertQuestion23A'), $this->post('RACertQuestion23B'), $this->post('RACertQuestion23C'), $this->post('RACertQuestion23D'), $this->post('RACertQuestion23DText'), $this->post('RACertQuestion23E'), $this->post('RehabTrees'), $this->post('RehabTreesTahun'), $this->post('InsetTrees'), $this->post('InsetTreesTahun'), $this->post('FrFoliar'), $this->post('DoFoliar'), $this->post('FarmerSignature'), $this->post('InspectorSignature'), $this->post('AuditCommiteeSignature'), $this->post('CertificationStart'), $this->post('CertificationEnd'), $this->post('AP'), $this->post('APNr'), $this->post('PR'), $this->post('PRNr'), $this->post('Scavina'), $this->post('ScavinaNr'), $this->post('MT'), $this->post('MTNr'), $this->post('M02'), $this->post('M02Nr'), $this->post('M04'), $this->post('M04Nr'), $this->post('M06'), $this->post('M06Nr'), $this->post('MHP03'), $this->post('MHP03Nr'), $this->post('MHP04'), $this->post('MHP04Nr'), $this->post('BB01'), $this->post('BB01Nr'), $this->post('BLB'), $this->post('BLBNr'), $this->post('BRT'), $this->post('BRTNr'), $this->post('ShadeTreesIncProductivity'), $this->post('ShadeTreesExtraIncome'), $this->post('ShadeTreesProtectSoil'), $this->post('ShadeTreesReducePests'), $this->post('ShadeTreesReduceHeat'), $this->post('ShadeTreesIncLandValue'), $this->post('ShadeTreesAddFirewood'), $this->post('ShadeTreesAddFodder'), $this->post('ShadeTreesDoNotKnow'), $this->post('ShadeTreesOthers'), $this->post('ShadeTreesSpreadEvently'), $this->post('ShadeTreesObtainSeeds'), $this->post('Nuts'), $this->post('Tubers'), $this->post('Patchouli'), $this->post('CoverCropOthers'), $this->post('NoCoverCrop'), $this->post('ObtainSeedsToday'), $this->post('SeedsFreeFromPests'), $this->post('SeedsFillRoutineMaintenance'), $this->post('AfterCertSaveRecordOriginSeeds'), $this->post('Production'), $this->post('ProductionNext'), $this->post('SalesLastyear'), $this->post('HowToDealOrganicAnorganicWaste'), $this->post('PruningOptStructure'), $this->post('FrequentPruningOptStructure'), $this->post('HeightPruningOptStructure'), $this->post('PruningBudInfected'), $this->post('FrequentPruningBudInfected'), $this->post('HeightPruningBudInfected'), $this->post('PruningNotProductive'), $this->post('FrequentPruningNotProductive'), $this->post('HeightPruningNotProductive'), $this->post('DisinfectedTools'), $this->post('AvailableOrganicFertilizer'), $this->post('RoutineWatchSoilFertility'), $this->post('ImprovePlantFixNitrogenInSoil'), $this->post('ImproveApplyPracticeAgroforestry'), $this->post('ImproveFertilizingWithOrganic'), $this->post('ImproveFertilizingWithAnorganic'), $this->post('ImproveMakeBiopori'), $this->post('ImprovePlantingShadeTrees'), $this->post('ImproveUseCoverCrop'), $this->post('ImproveTerracing'), $this->post('ImproveDoNothing'), $this->post('RoutineMonitorPestInGarden'), $this->post('UseChemicalPesticideDosage'), $this->post('ApplyAltNonChemicalControlPests'), $this->post('UseOrganicControlPests'), $this->post('UseChemicalLowestToxicity'), $this->post('UseChemicalLastChoice'), $this->post('ApplyRotationStrategy'), $this->post('NoticeUseInorganicFertilizer'), $this->post('TrainedUseProperly'), $this->post('MixPesticideLiquidFertilizer'), $this->post('ExcessPesticideDisposedSafely'), $this->post('GiveNoEntrySignAfterSpraying'), $this->post('AdherePreHarvestInterval'), $this->post('EquipmentGoodCondition'), $this->post('StoreAccordanceOnLabel'), $this->post('StoreOriginalPackaging'), $this->post('StoreIndicationSuitablePlants'), $this->post('StoreAvoidPossibleSpill'), $this->post('StoreSecuredPlace'), $this->post('StoreFarFromProducts'), $this->post('HandlingCleanDry'), $this->post('HandlingEnoughVentilationLight'), $this->post('HandlingStructurallySafe'), $this->post('HandlingAntiAbsorptive'), $this->post('HandlingLeakproofedFloor'), $this->post('HandlingFireproofMaterial'), $this->post('HandlingCollectSpillage'), $this->post('HandlingClearWarningSign'), $this->post('HandlingFirstAidInfo'), $this->post('HandlingProcedureEmergency'), $this->post('HandlingAreaCleanEye'), $this->post('HandlingAccommodateLiquidStored'), $this->post('UsePesticideInorganicFertilizer'), $this->post('ParticipateChildEducation'), $this->post('CutWageForDisciplinary'), $this->post('DoCutWageForWorker'), $this->post('WagePaidByPerformance'), $this->post('PayingWorkerWageByPerformance'), $this->post('HandlingFirstAidInGarden'), $this->post('FirstAidKitLocation'), $this->post('WorkerNotHandlePesticide'), $this->post('WorkerAccessSafeDrinkingWater'), $this->post('BufferZoneGarden'), $this->post('LandOpeningForest'), $this->post('LandOpeningForestCertificate'), $this->post('IdentifyProtectRareSpecies')
                , $this->post('FrDolomiteLime'), $this->post('FrCocoaSpecific'), $this->post('DoDolomiteLime'), $this->post('DoCocoaSpecific'),$this->post('c04'),$this->post('c04Nr'),$this->post('c07'),$this->post('c07Nr'),$this->post('BB'),$this->post('BBNr'),$this->post('ShadeTreesReason'),$this->post('ObtainSeedsTodayNr'),
                $this->post('FrPengapuran'),$this->post('DosePengapuran'),$this->post('FrFertiliaKakao'),$this->post('DoFertiliaKakao'),$this->post('FrNitrabor'),$this->post('DoNitrabor'),$this->post('Insectisida24')
        );
        if ($garden) {
            $this->response($garden, 200);
        } else {
            $this->response(array('error' => 'Garden could not be found'), 404);
        }
    }

    public function GroupIDs_get() {
        $data = $this->mfarmer->readGroupIDs($this->get('prov'), $this->get('kab'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any InstitutionIDs!'), 404);
        }
    }

    public function InstitutionIDs_get() {
        $InstitutionIDs = $this->mfarmer->readInstitutionIDs();
        if ($InstitutionIDs) {
            $this->response($InstitutionIDs, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any InstitutionIDs!'), 404);
        }
    }

    public function PositionIDs_get() {
        $InstitutionIDs = $this->mfarmer->readPositionIDs();
        if ($InstitutionIDs) {
            $this->response($InstitutionIDs, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any InstitutionIDs!'), 404);
        }
    }

    public function Provinsis_get() {
        $data = $this->mfarmer->readProvinsis($this->get('key'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function AllProvinsis_get() {
        $data = $this->mfarmer->readAllProvinsis($this->get('key'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function Kabupatens_get() {
        $data = $this->mfarmer->readKabupatens($this->get('key'), $this->get('prov'), $this->get('SupplychainID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function Kabupatens_staff_get(){
        $data = $this->mfarmer->readKabupatensStaff($this->get('prov'));
        $this->response($data, 200);
    }

    public function Kecamatans_get() {
        $data = $this->mfarmer->readKecamatans($this->get('key'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function provinces_get() {
        $data = $this->mfarmer->readProvinces();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any province!'), 404);
        }
    }

    public function districts_get() {
        $data = $this->mfarmer->readDitricts($this->get('ProvinceID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any district!'), 404);
        }
    }

    public function access_staffs_get() {
        $data = $this->mfarmer->readAccessStaffs($this->get('ProvinceID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any district!'), 404);
        }
    }

    public function workareas_get() {
        $data = $this->mfarmer->readWorkareas($this->get('ProvinceID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any district!'), 404);
        }
    }

    public function subdistricts_get() {
        $data = $this->mfarmer->readSubDistricts($this->get('DistrictID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any subdistrict!'), 404);
        }
    }

    public function villages_get() {
        $data = $this->mfarmer->readVillages($this->get('SubDistrictID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any subdistrict!'), 404);
        }
    }

    public function CPGlist_get() {
        $province = $this->get('ProvinceID');
        $district = $this->get('DistrictID');
        $subdistrict = $this->get('SubDistrictID');
        $data = $this->mfarmer->readCPG($province, $district, $subdistrict);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function Desas_get() {
        $data = $this->mfarmer->readDesas($this->get('key') . '::' . $this->get('kab') . '::' . $this->get('SupplychainID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function Gardens_get() {
        $data = $this->mfarmer->readGardens($this->get('farmer_id'));
        $data[]['id'] = $data[sizeof($data) - 1]['id'] + 1;
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function Garden_post() {
        $data = $this->mfarmer->addGarden($this->post('gFarmerID'), $_SESSION['userid']);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function Surveys_get() {
        $data = $this->mfarmer->readSurveys($this->get('farmer_id'), $this->get('garden_nr'), $this->get('jenis'), $this->get('isAddLatest'), $this->get('isPostline'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function Survey_harvests_get() {
        $data = $this->mfarmer->readSurveyHarvests($this->get('farmer_id'), $this->get('jenis'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function Survey_saving_pilots_get() {
        $data = $this->mfarmer->readSurveySavingPilots($this->get('farmer_id'), $this->get('jenis'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function Survey_saving_pilot_info_get() {
        $data = $this->mfarmer->readSavingPilotInfo($this->get('key'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function Farmerl_harvest_post() {
        $data = $this->mfarmer->addSurveyHarvests($this->post('hFarmerID'), $this->post('nr'), $_SESSION['userid']);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function cetak_get($id, $GardenID, $param2 = '', $value2 = '', $param3 = '', $value3 = '') {
        $data['data'] = $this->mfarmer->readFarmer($this->get('FarmerID'));
        $family = $this->mfarmer->readFarmerKeluargas($this->get('FarmerID'), 0, 100);
        $data['anggota'] = $family['data'];
        $data['harvest'] = $this->mfarmer->readFarmerHarvest($this->get('FarmerID'), $this->get('SurveyID'));
        $data['garden'] = $this->mfarmer->readGardenForCetak($this->get('FarmerID'), $this->get('SurveyID'));
        $data['logo'] = $this->mfarmer->readPartnerLogo($this->get('FarmerID'), $value3);

        $this->load->view('farmer_cetak', $data);
    }

    public function cetak_basic_farmer_get($param1 = '', $value1 = '', $param2 = '', $value2 = '', $param3 = '', $value3 = '') {
        $this->load->language('general', $this->get('lang'));
        $data['activitylog'] = false;
        if ($param1 == 'CpgBatchTrainingID') {
            $this->load->model('cpg/mcpg');
            $farmer = $this->mcpg->readParticipants($value1);
            for ($i = 0; $i < sizeof($farmer['data']); $i++) {
                $farmer_id[] = $farmer['data'][$i]['pFarmerID'];
            }
            $survey_id = $value2;
            $value3 = $farmer['data'][0]['PartnerID'];
        } elseif ($param1 == 'CpgKaderTrainingID') {
            $this->load->model('training/mkader');
            $farmer = $this->mkader->readParticipants($value1);
            for ($i = 0; $i < sizeof($farmer['data']); $i++) {
                $farmer_id[] = $farmer['data'][$i]['farmer_id'];
            }
            $survey_id = $value2;
            $value3 = $farmer['data'][0]['PartnerID'];
        } else {
            $farmer_ids = $this->get('FarmerID') == '' ? $value1 : $this->get('FarmerID');
            $farmer_id = explode('::', $farmer_ids);
            $survey_id = $this->get('SurveyID') == '' ? $value2 : $this->get('SurveyID');
        }

        $data['partnerid'] = $value3;
        for ($i = 0; $i < sizeof($farmer_id); $i++) {
            if ($survey_id == "latest") {
                $surveyIDs = $this->mfarmer->getLatestSurveyId($farmer_id[$i]);
                $survey_id = $surveyIDs[0]['SurveyNr'];
            }

            $data['data'] = $this->mfarmer->readFarmer($farmer_id[$i]); //print_r($data);exit;
            $family = $this->mfarmer->readFarmerKeluargas($farmer_id[$i], 0, 100);
            $data['anggota'] = $family['data'];
            $data['harvest'] = $this->mfarmer->readFarmerHarvest($farmer_id[$i], $survey_id);
            $data['garden'] = $this->mfarmer->readGardenForCetak($farmer_id[$i], $survey_id);
            $data['certification'] = $this->mfarmer->readFarmerCertification($farmer_id[$i], $survey_id);
            // echo '<pre>'; print_r($data['certification']); echo '</pre>';exit;
            $data['logos'] = $this->mfarmer->readPartnerLogo($farmer_id[$i], $value3);
            // echo '<pre>'; print_r($data['logos']); echo '</pre>'; exit;
            $data['SurveyNr'] = $survey_id;
            $data['garden_status'] = $this->mfarmer->getFarmerGardenStatus($farmer_id[$i]);
            $data['other_land'] = $this->mfarmer->getFarmerOtherLand($farmer_id[$i]);
            $this->load->view('cetak_basic_farmer', $data);
            //$this->load->view('cetak_basic_farmer_break');
        }
    }

    public function cetak_basic_nutrisi_get($param1 = '', $value1 = '', $param2 = '', $value2 = '', $param3 = '', $value3 = '') {
        $this->load->language('general', $this->get('lang'));
        if ($param1 == 'CpgBatchTrainingID') {
            $this->load->model('cpg/mcpg');
            $farmer = $this->mcpg->readParticipants($value1);
            for ($i = 0; $i < sizeof($farmer['data']); $i++) {
                $farmer_id[] = $farmer['data'][$i]['pFarmerID'];
            }
            $survey_id = $value2;
            $value3 = $farmer['data'][0]['PartnerID'];
        } elseif ($param1 == 'CpgKaderTrainingID') {
            $this->load->model('training/mkader');
            $farmer = $this->mkader->readParticipants($value1);
            for ($i = 0; $i < sizeof($farmer['data']); $i++) {
                $farmer_id[] = $farmer['data'][$i]['farmer_id'];
            }
            $survey_id = $value2;
            $value3 = $farmer['data'][0]['PartnerID'];
        } else {
            $farmer_id[] = $this->get('FarmerID');
            $survey_id = $this->get('SurveyID');
        }

        for ($i = 0; $i < sizeof($farmer_id); $i++) {

            if ($this->get('SurveyID') == "latest") {
                $surveyIDs = $this->mfarmer->getLatestSurveyId($farmer_id[$i]);
                $survey_id = $surveyIDs[0]['SurveyNr'];
            }

            $data['data'] = $this->mfarmer->readFarmer($farmer_id[$i]);
            $data['nutrition'] = $this->mfarmer->readFarmerNutrition($farmer_id[$i], $survey_id);
            $data['family'] = $this->mfarmer->readFarmerFamilyNutrition($farmer_id[$i]);
            $data['logos'] = $this->mfarmer->readPartnerLogo($farmer_id[$i], $value3);

            $this->load->view('cetak_basic_nutrisi', $data);
        }
    }

    public function cetak_learning_contract_applicant_get($ApplicantID=''){
        //get data
        $ApplicantID = (int) $ApplicantID;
        $data['applicant'] = $this->mfarmer->getApplicantInfo($ApplicantID);
        $data['QRCode'] = $this->QrcodeGenerator($ApplicantID);

        $this->load->view('cetak_learn_contract_load_header_v2');
        $this->load->view('cetak_learn_contract_applicant_v2', $data);
        $this->load->view('cetak_learn_contract_load_footer_v2');
    }

    public function QrcodeGenerator($FarmerID) {
        $file_code = 'files/tmp/' . $FarmerID . '.png';
        $qrCode = new QrCode($FarmerID);
        $qrCode->setSize(175);
        $qrCode->setEncoding('UTF-8');
        $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevel(ErrorCorrectionLevel::HIGH));
        $qrCode->setLogoPath('images/sawitchain-favicon.png');
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

    public function cetak_learning_contract_get($param1 = '', $value1 = '', $param2 = '', $value2 = '', $param3 = '', $value3 = '') {
        $this->load->language('general', 'indonesia');

        if ($param1 == 'CpgBatchTrainingID') {
            $this->load->model('cpg/mcpg');
            $farmer = $this->mcpg->readParticipants($value1);
            for ($i = 0; $i < sizeof($farmer['data']); $i++) {
                $farmer_id[] = $farmer['data'][$i]['pFarmerID'];
            }
            $survey_id = $value2;
            $value3 = $farmer['data'][0]['PartnerID'];
        } else if ($param1 == 'CPGid') {
            /*
              $this->load->model('cpg/mcpg');
              $farmer = $this->mcpg->readParticipants($value1, '', 'CPGid', $value2);
              for ($i = 0; $i < sizeof($farmer['data']); $i++) {
              $farmer_id[] = $farmer['data'][$i]['pFarmerID'];
              }
             */
            //$survey_id = $value2;
            //$value3 = $farmer['data'][0]['PartnerID'];
            $farmer_id = explode("::", $value2);
        }
        /*
          for ($i = 0; $i < sizeof($farmer_id); $i++) {
          $data['data']  = $this->mfarmer->readFarmer($farmer_id[$i]); //print_r($data);exit;
          $data['logos'] = $this->mfarmer->readPartnerLogo($farmer_id[$i], $value3);
          $this->load->view('cetak_learning_contract', $data);
          }
         */
        $this->load->view('cetak_learn_contract_load_header_v2');

        for ($i = 0; $i < sizeof($farmer_id); $i++) {
            $data['data'] = $this->mfarmer->readFarmer($farmer_id[$i]);
            $data['logos'] = $this->mfarmer->readPartnerLogo($farmer_id[$i], $value3);

            if (empty($data['data']['Photo']) || !file_exists('images/Photo/' . $data['data']['Photo'])) {
                $data['data']['Photo'] = 'no-user.jpg';
            }
            $this->load->view('cetak_learn_contract_v2', $data);
        }

        $this->load->view('cetak_learn_contract_load_footer_v2');
    }

    public function cetak_learning_contract_template_get($FarmerID, $CPGid = '') {
        if ($CpgID == '') {
            $data['data'] = $this->mfarmer->readFarmer($FarmerID); //print_r($data);exit;
            $data['logo']['Photo'] = "20131213041705_SCPP Logo.jpg";
            $this->load->view('cetak_learning_contract', $data);
        } else {
            //https://app.cocoatrace.com/api/index.php/farmer/cetak_learning_contract/CPGid/11120001/FarmerID/111200009
            $this->cetak_learning_contract_get('CPGid', $CPGid, 'FarmerID', $FarmerID);
        }
    }

    public function cetak_certification_contract_template_get($FarmerID, $CPGid = '') {
        if ($CpgID == '') {
            $data['data'] = $this->mfarmer->readFarmer($FarmerID); //print_r($data);exit;
            $data['logo']['Photo'] = "20131213041705_SCPP Logo.jpg";
            $this->load->view('cetak_learning_contract', $data);
        } else {
            //https://app.cocoatrace.com/api/index.php/farmer/cetak_learning_contract/CPGid/11120001/FarmerID/111200009
            $this->cetak_learning_contract_get('CPGid', $CPGid, 'FarmerID', $FarmerID);
        }
    }

    public function cetak_basic_ppi2012_get($param1 = '', $value1 = '', $param2 = '', $value2 = '', $param3 = '', $value3 = '') {
        $this->load->language('general', $this->get('lang'));
        if ($param1 == 'CpgBatchTrainingID') {
            $this->load->model('cpg/mcpg');
            $farmer = $this->mcpg->readParticipants($value1);
            for ($i = 0; $i < sizeof($farmer['data']); $i++) {
                $farmer_id[] = $farmer['data'][$i]['pFarmerID'];
            }
            $survey_id = $value2;
            $value3 = $farmer['data'][0]['PartnerID'];
        } elseif ($param1 == 'CpgKaderTrainingID') {
            $this->load->model('training/mkader');
            $farmer = $this->mkader->readParticipants($value1);
            for ($i = 0; $i < sizeof($farmer['data']); $i++) {
                $farmer_id[] = $farmer['data'][$i]['farmer_id'];
            }
            $survey_id = $value2;
            $value3 = $farmer['data'][0]['PartnerID'];
        } else {
            $farmer_id[] = $this->get('FarmerID');
            $survey_id = $this->get('SurveyID');
        }
        for ($i = 0; $i < sizeof($farmer_id); $i++) {

            if ($this->get('SurveyID') == "latest") {
                $surveyIDs = $this->mfarmer->getLatestSurveyId($farmer_id[$i]);
                $survey_id = $surveyIDs[0]['SurveyNr'];
            }

            $data['data'] = $this->mfarmer->readFarmer($farmer_id[$i]);
            $data['ppi2012'] = $this->mfarmer->readFarmerPpi2012($farmer_id[$i], $survey_id);
            $data['logos'] = $this->mfarmer->readPartnerLogo($farmer_id[$i], $value3);

            $this->load->view('cetak_basic_ppi2012', $data);
        }
    }

    public function garden_survey_post() {
        $data = $this->mfarmer->addSurveyGarden($this->post('gFarmerID'), $this->post('GardenNr'), $this->post('nr'), $_SESSION['userid']);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function Survey_ppis_get() {
        $data = $this->mfarmer->readSurveyPpis($this->get('farmer_id'), $this->get('jenis'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function Survey_ppi_2012s_get() {
        $data = $this->mfarmer->readSurveyPpi2012s($this->get('farmer_id'), $this->get('jenis'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function Survey_nutritions_get() {
        $data = $this->mfarmer->readSurveyNutritions($this->get('farmer_id'), $this->get('jenis'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function farmerl_ppi_get() {
        if ($this->get('surveyNr') == 'Tambah Baru') {
            return;
        }

        if (!$this->get('id')) {
            $this->response(null, 400);
        }

        $data = $this->mfarmer->readFarmerPpi($this->get('id'), $this->get('surveyNr'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Farmer could not be found'), 404);
        }
    }

    public function farmerl_ppi_2012_get() {
        if ($this->get('surveyNr') == 'Tambah Baru') {
            return;
        }

        if (!$this->get('id')) {
            $this->response(null, 400);
        }

        $data = $this->mfarmer->readFarmerPpi2012($this->get('id'), $this->get('surveyNr'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Farmer could not be found'), 404);
        }
    }

    public function ppi_post() {
        $data = $this->mfarmer->updatePpi($this->post('pSurveyNr'), $this->post('pInterviewDate'), $this->post('Householdmembers'), $this->post('Schooling'), $this->post('Working'), $this->post('DrinkingWater'), $this->post('ToiletFacility'), $this->post('HouseFloor'), $this->post('HouseCeiling'), $this->post('Refrigerator'), $this->post('Motorcycle'), $this->post('Television'), $this->post('pFarmerID'), $_SESSION['userid']);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Farmer could not be found'), 404);
        }
    }

    public function ppi_2012_post() {
        $data = $this->mfarmer->updatePpi2012($this->post('ppSurveyNr'), $this->post('ppInterviewDate'), $this->post('pHouseholdmembers'), $this->post('pSchooling'), $this->post('pEducation'), $this->post('pEmployment'), $this->post('pHouseFloor'), $this->post('pToiletFacility'), $this->post('pCookingFuel'), $this->post('pGasCylinder'), $this->post('pRefrigerator'), $this->post('pMotorcycle'), $this->post('ppFarmerID'), $_SESSION['userid']);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Farmer could not be found'), 404);
        }
    }

    public function farmerl_ppi_post() {
        $data = $this->mfarmer->addSurveyPpi($this->post('pFarmerID'), $this->post('nr'), $_SESSION['userid']);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function farmerl_ppi_2012_post() {
        $data = $this->mfarmer->addSurveyPpi2012($this->post('ppFarmerID'), $this->post('nr'), $_SESSION['userid']);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function farmerl_nutrition_get() {
        if ($this->get('surveyNr') == 'Tambah Baru') {
            return;
        }

        if (!$this->get('id')) {
            $this->response(null, 400);
        }

        $data = $this->mfarmer->readFarmerNutrition($this->get('id'), $this->get('surveyNr'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Farmer could not be found'), 404);
        }
    }

    public function nutrition_post() {
        $data = $this->mfarmer->updateNutrition($this->post('nSurveyNr'), $this->post('nInterviewDate'), $this->post('KebunPanjang'), $this->post('KebunLebar'), $this->post('KbBayam'), $this->post('KbCabai'), $this->post('KbKacangPanjang'), $this->post('KbKangkung'), $this->post('KbSawi'), $this->post('KbTerong'), $this->post('KbTomat'), $this->post('KbKambing'), $this->post('KbSapi'), $this->post('KbBebek'), $this->post('KbAyam'), $this->post('KbIkan'), $this->post('aSagu'), $this->post('aNasi'), $this->post('aMie'), $this->post('aJagung'), $this->post('aRoti'), $this->post('bUbiJalarKuning'), $this->post('bSingkongKuning'), $this->post('bWortel'), $this->post('bLabu'), $this->post('cUbiJalarPutih'), $this->post('cSingkongPutih'), $this->post('cTalas'), $this->post('cKentang'), $this->post('dBayam'), $this->post('dDaunMelinjo'), $this->post('dDaunPepaya'), $this->post('dDaunSingkong'), $this->post('dKangkung'), $this->post('dSawi'), $this->post('eKacangPanjang'), $this->post('eTomat'), $this->post('eTerong'), $this->post('fJambuMerah'), $this->post('fMangga'), $this->post('fPepaya'), $this->post('gJambuAir'), $this->post('gKelapa'), $this->post('gPisang'), $this->post('gRambutan'), $this->post('gSemangka'), $this->post('gSalak'), $this->post('hJeroan'), $this->post('hHati'), $this->post('iAyam'), $this->post('iBebek'), $this->post('iKambing'), $this->post('iKerbau'), $this->post('iSapi'), $this->post('iLainnya'), $this->post('jAyam'), $this->post('jBebek'), $this->post('jEntok'), $this->post('jPuyuh'), $this->post('kCumiCumi'), $this->post('kIkan'), $this->post('kIkanTeri'), $this->post('kKepiting'), $this->post('kKerang'), $this->post('kUdang'), $this->post('lAirTahuSusuKedelai'), $this->post('lSausKacang'), $this->post('lTahu'), $this->post('lTempe'), $this->post('lKacang'), $this->post('lKwaci'), $this->post('mKeju'), $this->post('mSusu'), $this->post('nMinyakGoreng'), $this->post('nMentega'), $this->post('nSantan'), $this->post('Score'), $this->post('nFarmerID'), $this->post('ComKebunPanjang'), $this->post('ComKebunLebar'), $this->post('ComKbBayam'), $this->post('ComKbCabai'), $this->post('ComKbKacangPanjang'), $this->post('ComKbKangkung'), $this->post('ComKbSawi'), $this->post('ComKbTerong'), $this->post('ComKbTomat'), $this->post('HaveChildren'), $this->post('ChildrenMeal'), $this->post('ChildrenASI'), $this->post('Children3MonthASI'), $this->post('ChildrenNrGiveASI'), $this->post('ChildrenNrGiveMeal'), $this->post('ChildrenGiveKolestrum'), $this->post('MotherPregnant2Years'), $this->post('MotherPregnantEat'), $_SESSION['userid']);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Farmer could not be found'), 404);
        }
    }

    public function farmerl_nutrition_post() {
        $data = $this->mfarmer->addSurveyNutrition($this->post('nFarmerID'), $this->post('nr'), $_SESSION['userid']);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

//validation
    public function farmerl_garden_validation_put() {
        if (!$this->put('fgfarmerid')) {
            $this->response(null, 400);
        }

        $result = $this->mfarmer->validationGarden($this->put('fgfarmerid'), $this->put('fggardennr'), $this->put('fgsurveynr'), $this->put('fgvalidation'), $this->put('fgcomment'), $this->put('ApprovedByME'), $this->put('ApprovedByGO'), $this->put('ApprovedByDC'));
        if ($result) {
            $this->response($result, 200);
        } else {
            $this->response(array('error' => 'Farmer could not be found'), 404);
        }
    }

    public function farmerl_harvest_validation_put() {
        if (!$this->put('vhfarmerid')) {
            $this->response(null, 400);
        }

        $result = $this->mfarmer->validationHarvest($this->put('vhfarmerid'), $this->put('fgsurveynr'), $this->put('fgvalidation'), $this->put('fgcomment'), $this->put('ApprovedByME'), $this->put('ApprovedByGO'), $this->put('ApprovedByDC'));
        if ($result) {
            $this->response($result, 200);
        } else {
            $this->response(array('error' => 'Farmer could not be found'), 404);
        }
    }

    public function farmerl_saving_pilot_validation_put() {
        if (!$this->put('vspfarmerid')) {
            $this->response(null, 400);
        }

        $result = $this->mfarmer->validationSavingPilot($this->put('vspfarmerid'), $this->put('fgsurveynr'), $this->put('fgvalidation'), $this->put('fgcomment'), $this->put('ApprovedByME'), $this->put('ApprovedByGO'), $this->put('ApprovedByDC'));
        if ($result) {
            $this->response($result, 200);
        } else {
            $this->response(array('error' => 'Farmer could not be found'), 404);
        }
    }

    public function farmerl_ppi_validation_put() {
        if (!$this->put('vpfarmerid')) {
            $this->response(null, 400);
        }

        $result = $this->mfarmer->validationPpi($this->put('vpfarmerid'), $this->put('fgsurveynr'), $this->put('fgvalidation'), $this->put('fgcomment'), $this->put('ApprovedByME'), $this->put('ApprovedByGO'), $this->put('ApprovedByDC'));
        if ($result) {
            $this->response($result, 200);
        } else {
            $this->response(array('error' => 'Farmer could not be found'), 404);
        }
    }

    public function farmerl_ppi2012_validation_put() {
        if (!$this->put('vp2farmerid')) {
            $this->response(null, 400);
        }

        $result = $this->mfarmer->validationPpi2012($this->put('vp2farmerid'), $this->put('fgsurveynr'), $this->put('fgvalidation'), $this->put('fgcomment'), $this->put('ApprovedByME'), $this->put('ApprovedByGO'), $this->put('ApprovedByDC'));
        if ($result) {
            $this->response($result, 200);
        } else {
            $this->response(array('error' => 'Farmer could not be found'), 404);
        }
    }

    public function farmerl_nutrition_validation_put() {
        if (!$this->put('vnfarmerid')) {
            $this->response(null, 400);
        }

        $result = $this->mfarmer->validationNutrition($this->put('vnfarmerid'), $this->put('fgsurveynr'), $this->put('fgvalidation'), $this->put('fgcomment'), $this->put('ApprovedByME'), $this->put('ApprovedByGO'), $this->put('ApprovedByDC'));
        if ($result) {
            $this->response($result, 200);
        } else {
            $this->response(array('error' => 'Farmer could not be found'), 404);
        }
    }

    public function sum_garden_get() {
        $data = $this->mfarmer->readSumGarden($this->get('farmerid'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function sum_post_get() {
        $data = $this->mfarmer->readSumPost($this->get('farmerid'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function sum_nutrition_get() {
        $data = $this->mfarmer->readSumNutrition($this->get('farmerid'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function sum_ppi_get() {
        $data = $this->mfarmer->readSumPpi($this->get('farmerid'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    /*
      function data_CekGarden_get() {
      $data = $this->mfarmer->readCekGarden($this->get('FarmerID'));
      if($data) $this->response($data, 200);
      else $this->response(array('error' => 'Couldn\'t find any data!'), 404);
      }
     */

    public function data_CekSurvey_get() {
        $data = $this->mfarmer->readCekSurvey($this->get('jenis'), $this->get('FarmerID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }

    public function data_partner_get() {
        $data = $this->mfarmer->readPartner($this->get('FarmerID'), $this->get('district'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }

    public function cetak_nutrisi_get($id, $survey, $param2 = '', $value2 = '', $param3 = '', $value3 = '') {
        $data['data'] = $this->mfarmer->readFarmer($this->get('FarmerID'));
        $data['nutrition'] = $this->mfarmer->readFarmerNutrition($this->get('FarmerID'), $this->get('SurveyID'));
        $data['family'] = $this->mfarmer->readFarmerFamilyNutrition($$this->get('FarmerID'));
        $data['logo'] = $this->mfarmer->readPartnerLogo($this->get('FarmerID'), $value3);

        $this->load->view('farmer_cetak_nutrisi', $data);
    }

    public function cetak_ppi2010_get($id, $survey, $param2 = '', $value2 = '', $param3 = '', $value3 = '') {
        $data['data'] = $this->mfarmer->readFarmer($this->get('FarmerID'));
        $data['ppi2010'] = $this->mfarmer->readFarmerPpi($this->get('FarmerID'), $this->get('SurveyID'));
        $data['logo'] = $this->mfarmer->readPartnerLogo($this->get('FarmerID'), $value3);

        $this->load->view('farmer_cetak_ppi2010', $data);
    }

    public function cetak_ppi2012_get($id, $survey, $param2 = '', $value2 = '', $param3 = '', $value3 = '') {
        $data['data'] = $this->mfarmer->readFarmer($this->get('FarmerID'));
        $data['ppi2012'] = $this->mfarmer->readFarmerPpi2012($this->get('FarmerID'), $this->get('SurveyID'));
        $data['logo'] = $this->mfarmer->readPartnerLogo($this->get('FarmerID'), $value3);

        $this->load->view('farmer_cetak_ppi2012', $data);
    }

//cetak hasil farmer, nutrisi, ppi
    public function cetak_result_farmer_get($id, $GardenID, $param2 = '', $value2 = '', $param3 = '', $value3 = '') {
        $this->load->language('general', $this->get('lang'));

        if ($id == 'FarmerID' and strpos($GardenID, '::')) {
            $FarmerID = explode('::', $GardenID);
            for ($iiii = 0; $iiii < sizeof($FarmerID); $iiii++) {

                //cek apakah pakai latest
                if ($this->get('SurveyID') == "latest") {
                    $surveyIDs = $this->mfarmer->getLatestSurveyId($FarmerID[$iiii]);
                    $SurveyID = $surveyIDs[0]['SurveyNr'];
                } else {
                    $SurveyID = $this->get('SurveyID');
                }

                $data['data'] = $this->mfarmer->readFarmer($FarmerID[$iiii]);
                $family = $this->mfarmer->readFarmerKeluargas($FarmerID[$iiii], 0, 100);
                $data['anggota'] = $family['data'];
                $data['harvest'] = $this->mfarmer->readFarmerHarvest($FarmerID[$iiii], $SurveyID);
                $data['garden'] = $this->mfarmer->readGardenForCetak($FarmerID[$iiii], $SurveyID);
                $data['logos'] = $this->mfarmer->readPartnerLogo($FarmerID[$iiii], $value3);
                $data['cert'] = $this->mfarmer->readCertificate($FarmerID[$iiii], $SurveyID);
                $data['certLastAudit'] = $this->mfarmer->readCertLastAuditCetak($FarmerID[$iiii], $value2, $data['garden'][0]['GardenNr'], $data['cert']['Certification']);
                $data['auditLog'] = $this->mfarmer->readAuditLogsCetak($FarmerID[$iiii], $value2, $data['garden'][0]['GardenNr'], $data['cert']['Certification']);
                $data['SurveyNr'] = $SurveyID;
                $data['garden_status'] = $this->mfarmer->getFarmerGardenStatus($FarmerID[$iiii]);
                $data['other_land'] = $this->mfarmer->getFarmerOtherLand($FarmerID[$iiii]);
                $this->load->view('cetak_result_farmer', $data);
            }
            return;
        }

        //cek apakah pakai latest
        if ($this->get('SurveyID') == "latest") {
            $surveyIDs = $this->mfarmer->getLatestSurveyId($this->get('FarmerID'));
            $SurveyID = $surveyIDs[0]['SurveyNr'];
        } else {
            $SurveyID = $this->get('SurveyID');
        }

        $data['data'] = $this->mfarmer->readFarmer($this->get('FarmerID'));
        $family = $this->mfarmer->readFarmerKeluargas($this->get('FarmerID'), 0, 100);
        $data['anggota'] = $family['data'];
        $data['harvest'] = $this->mfarmer->readFarmerHarvest($this->get('FarmerID'), $SurveyID);
        $data['garden'] = $this->mfarmer->readGardenForCetak($this->get('FarmerID'), $SurveyID);
        $data['logos'] = $this->mfarmer->readPartnerLogo($this->get('FarmerID'), $value3);
        $data['cert'] = $this->mfarmer->readCertificate($this->get('FarmerID'), $SurveyID);
        // echo "<pre>"; print_r($data['cert'][0]); echo "</pre>"; exit;
        $data['certLastAudit'] = $this->mfarmer->readCertLastAuditCetak($this->get('FarmerID'), $value2, $data['garden'][0]['GardenNr'], $data['cert']['Certification']);
        $data['auditLog'] = $this->mfarmer->readAuditLogsCetak(
                $this->get('FarmerID'), $value2, $data['garden'][0]['GardenNr'], $data['cert'][0]['Certification']
        );
        $data['SurveyNr'] = $SurveyID;
        $data['garden_status'] = $this->mfarmer->getFarmerGardenStatus($this->get('FarmerID'));
        $data['other_land'] = $this->mfarmer->getFarmerOtherLand($this->get('FarmerID'));

        $this->load->view('cetak_result_farmer', $data);
    }

    public function cetak_result_farmer_latest_get($id, $FarmerID, $param2 = '', $value2 = '', $param3 = '', $PartnerID = '') {
        $this->load->language('general', $this->get('lang'));
        if ($id == 'FarmerID' and strpos($FarmerID, '::')) {
            $FarmerIDs = explode('::', $FarmerID);
            foreach ($FarmerIDs as $key => $value) {
                $this->cetak_gap_latest($value, $this->get('PartnerID'), $this->get('SpecLuwuUtara'), $this->get('jenis_form'), $this->get('SurveyID'));
            }
        } else {
            $this->cetak_gap_latest($this->get('FarmerID'), $this->get('PartnerID'), $this->get('SpecLuwuUtara'), $this->get('jenis_form'), $this->get('SurveyID'));
        }
    }

    public function cetak_result_farmer_latest_luwu_utara_get($id, $FarmerID, $param2 = '', $value2 = '', $param3 = '', $PartnerID = '') {
        $this->load->language('general', $this->get('lang'));
        if ($id == 'FarmerID' and strpos($FarmerID, '::')) {
            $FarmerIDs = explode('::', $FarmerID);
            foreach ($FarmerIDs as $key => $value) {
                $this->cetak_gap_latest_luwu_utara($value, $this->get('PartnerID'), $this->get('SpecLuwuUtara'), $this->get('jenis_form'), $this->get('SurveyID'));
            }
        } else {
            $this->cetak_gap_latest_luwu_utara($this->get('FarmerID'), $this->get('PartnerID'), $this->get('SpecLuwuUtara'), $this->get('jenis_form'), $this->get('SurveyID'));
        }
    }

    public function cetak_certification_contract_get($id, $FarmerID, $param2 = '', $value2 = '', $param3 = '', $PartnerID = '') {
        $this->load->view('print_header');
        if ($id == 'FarmerID' and strpos($FarmerID, '::')) {
            $FarmerIDs = explode('::', $FarmerID);
            foreach ($FarmerIDs as $key => $value) {
                $this->cetak_certification_contract($value, $this->get('Coop'));
            }
        } else {
            $this->cetak_certification_contract($this->get('FarmerID'), $this->get('Coop'));
        }
        $this->load->view('print_footer');
    }

    public function cetak_certification_contract($FarmerID, $Coop = '') {
        $this->load->helper('date');

        $Coop = $this->mfarmer->getCoopName($Coop);

        $data = array();
        $certification = $this->mfarmer->getFarmerCertification($FarmerID);
        $ims = $this->mfarmer->getIMSManager($certification['FarmerID'], $certification['GardenNr'], $certification['SurveyNr']);
        $bank = $this->mfarmer->getFarmerBank($certification['FarmerID']);
        $data = array(
            'data' => $certification,
            'ims' => $ims,
            'bank' => $bank,
            'Coop' => $Coop,
        );
        // echo '<pre>'; print_r($data['Coop']); echo '</pre>'; exit;
        $this->load->view('cetak_certification_contract', $data);
    }

    private function cetak_gap_latest($FarmerID, $PartnerID, $SpecLuwuUtara, $jenis_form, $SurveyID) {
        if ($SpecLuwuUtara != "1") {
            $surveyIDs = $this->mfarmer->getLatestSurveyId($FarmerID);
            $surveyID = $surveyIDs[0]['SurveyNr'];
        } else {
            $surveyID = $SurveyID;
        }

        $data['SurveyNr'] = $surveyID;
        $data['data'] = $this->mfarmer->readFarmer($FarmerID);
        $family = $this->mfarmer->readFarmerKeluargas($FarmerID, 0, 100);
        $data['anggota'] = $family['data'];
        $data['harvest'] = $this->mfarmer->readFarmerHarvest($FarmerID, $surveyID);

        $data['SpecLuwuUtara'] = $SpecLuwuUtara;
        if ($SpecLuwuUtara == "1") {
            $data['logos'][0]['Photo'] = 'koptan_masagena.jpg';
            $data['logos'][1]['Photo'] = '20160106101449_logo_veco.png';
            $data['logos'][2]['Photo'] = '20141110100340_Mars Logo.jpg';
        } else {
            $data['logos'] = $this->mfarmer->readPartnerLogo($FarmerID, $PartnerID);
        }

        $data['garden_status'] = $this->mfarmer->getFarmerGardenStatus($FarmerID);
        $data['other_land'] = $this->mfarmer->getFarmerOtherLand($FarmerID);

        $data['garden'] = array();
        $data['cert'] = array();
        $data['certLastAudit'] = array();
        $data['signature'] = array();
        foreach ($surveyIDs as $key => $survey) {
            $data['garden'] = array_merge($data['garden'], $this->mfarmer->readGardenForCetakLatest($FarmerID, $survey['GardenNr'], $survey['SurveyNr']));
            $cert = $this->mfarmer->readCertificateGarden($FarmerID, $survey['SurveyNr'], $survey['GardenNr']);
            if (!empty($cert)) {
                $data['cert'][$survey['SurveyNr']] = $cert;
            }
            $signature = $this->mfarmer->readCertificateSignature($FarmerID, $survey['GardenNr'], $survey['SurveyNr'], @$cert['ICSDate'], @$cert['Certification']);
            if (!empty($signature)) {
                $data['signature'] = array_merge($data['signature'], $signature);
            }
            $cert = $this->mfarmer->readCertLastAuditCetak($FarmerID, $survey['SurveyNr'], $data['garden'][0]['GardenNr'], $data['cert']['Certification']);
            if (is_array($cert)) {
                $data['certLastAudit'] = array_merge($data['certLastAudit'], $cert);
            }
        }

        $data['auditLog'] = $this->mfarmer->readAuditLogsCetak(
                $FarmerID, $surveyID, $data['garden'][0]['GardenNr'], $data['cert'][0]['Certification']
        );
        $data['SurveyIDs'] = $surveyIDs;

        if ($jenis_form == "kosong") {
            $data['garden'] = array();
            $data['harvest'] = array();
            $data['cert'] = array();
            $data['certification'] = array();
            $data['auditLog'] = array();
            $data['signature'] = array();
            $data['certLastAudit'] = array();
        }

        //cek photo
        if (!file_exists('images/Photo/' . $data['data']['Photo'])) {
            $data['data']['Photo'] = 'default-user.png';
        }

        //echo '<pre>'; print_r($data); exit;
        $this->load->view('cetak_result_farmer_latest', $data);
    }

    private function cetak_gap_latest_luwu_utara($FarmerID, $PartnerID, $SpecLuwuUtara, $jenis_form, $SurveyID) {
        if ($SpecLuwuUtara != "1") {
            $surveyIDs = $this->mfarmer->getLatestSurveyId($FarmerID);
            $surveyID = $surveyIDs[0]['SurveyNr'];
        } else {
            $surveyID = $SurveyID;
        }

        $data['SurveyNr'] = $surveyID;
        $data['data'] = $this->mfarmer->readFarmer($FarmerID);
        $family = $this->mfarmer->readFarmerKeluargas($FarmerID, 0, 100);
        $data['anggota'] = $family['data'];
        $data['harvest'] = $this->mfarmer->readFarmerHarvest($FarmerID, $surveyID);

        $data['SpecLuwuUtara'] = $SpecLuwuUtara;
        if ($SpecLuwuUtara == "1") {
            $data['logos'][0]['Photo'] = 'koptan_masagena.jpg';
            $data['logos'][1]['Photo'] = '20160106101449_logo_veco.png';
            $data['logos'][2]['Photo'] = '20141110100340_Mars Logo.jpg';
        } else {
            $data['logos'] = $this->mfarmer->readPartnerLogo($FarmerID, $PartnerID);
        }

        $data['garden_status'] = $this->mfarmer->getFarmerGardenStatus($FarmerID);
        $data['other_land'] = $this->mfarmer->getFarmerOtherLand($FarmerID);

        $data['garden'] = array();
        $data['cert'] = array();
        $data['certLastAudit'] = array();
        $data['signature'] = array();
        foreach ($surveyIDs as $key => $survey) {
            $data['garden'] = array_merge($data['garden'], $this->mfarmer->readGardenForCetakLatest($FarmerID, $survey['GardenNr'], $survey['SurveyNr']));
            $cert = $this->mfarmer->readCertificateGarden($FarmerID, $survey['SurveyNr'], $survey['GardenNr']);
            if (!empty($cert)) {
                $data['cert'][$survey['SurveyNr']] = $cert;
            }
            $signature = $this->mfarmer->readCertificateSignature($FarmerID, $survey['GardenNr'], $survey['SurveyNr'], @$cert['ICSDate'], @$cert['Certification']);
            if (!empty($signature)) {
                $data['signature'] = array_merge($data['signature'], $signature);
            }
            $cert = $this->mfarmer->readCertLastAuditCetak($FarmerID, $survey['SurveyNr'], $data['garden'][0]['GardenNr'], $data['cert']['Certification']);
            if (is_array($cert)) {
                $data['certLastAudit'] = array_merge($data['certLastAudit'], $cert);
            }
        }

        $data['auditLog'] = $this->mfarmer->readAuditLogsCetak(
                $FarmerID, $surveyID, $data['garden'][0]['GardenNr'], $data['cert'][0]['Certification']
        );
        $data['SurveyIDs'] = $surveyIDs;

        if ($jenis_form == "kosong") {
            $data['garden'] = array();
            $data['harvest'] = array();
            $data['cert'] = array();
            $data['certification'] = array();
            $data['auditLog'] = array();
            $data['signature'] = array();
            $data['certLastAudit'] = array();
        }

        //cek photo
        if (!file_exists('images/Photo/' . $data['data']['Photo'])) {
            $data['data']['Photo'] = 'default-user.png';
        }

        //echo '<pre>'; print_r($data); exit;
        $this->load->view('cetak_result_farmer_luwu_utara_all_halaman', $data);
    }

    public function cetak_beneficiary_profiles_get(){
        //set bahasa
        if ($_SESSION['language'] == "Indonesia") {
            $this->load->language('general', 'indonesia');
        } else {
            $this->load->language('general', 'english');
        }

        //get param
        $MemberID = $this->get('MemberID');
        $MemberIDs = explode('::', $MemberID);

        $paramFooterGoogleApi = array();
        $keyParamFooterGoogleApi = 0;

        $dataHeader['titleNya'] = "Farmer Profile";
        $this->load->view('cetak_farmer_beneficiary_profiles_header_v2', $dataHeader);

        if (strpos($MemberID, '::')) {
            $countData = count($MemberIDs);
            $increData = 1;
            foreach ($MemberIDs as $key => $MemberID) {
                $MemberID = $this->mfarmer->getMemberID($MemberID);
                $this->cetak_beneficiary_profiles($MemberID, $countData, $increData);
                $increData++;

                $paramFooterGoogleApi[$keyParamFooterGoogleApi]['MemberID'] = $MemberID;
                $dataGardenProses = $this->mreport->getGardenData($MemberID);
                $paramFooterGoogleApi[$keyParamFooterGoogleApi]['gardens_coordinate_exists'] = $this->mreport->checkGardenCoordinateExist($dataGardenProses);
                $keyParamFooterGoogleApi++;
            }
        } else {
            $MemberID = $this->mfarmer->getMemberID($MemberID);
            $this->cetak_beneficiary_profiles($MemberID);
            $dataGardenProses = $this->mreport->getGardenData($MemberID);
            $paramFooterGoogleApi[$keyParamFooterGoogleApi]['MemberID'] = $MemberID;
            $paramFooterGoogleApi[$keyParamFooterGoogleApi]['gardens_coordinate_exists'] = $this->mreport->checkGardenCoordinateExist($dataGardenProses);
        }

        $dataFooter['paramFooterGoogleApi'] = $paramFooterGoogleApi;

        $dataAreaPolygon                = $this->mreport->getDataPlotPolygonMap($MemberID);

        if (!$dataAreaPolygon) {
            $checkAreaPolygon['count']  = 0;
        } else {
            
            if (!is_array($dataAreaPolygon)) {
                $countDataAreaPolygon = count(json_decode($dataAreaPolygon));
            } else {
                $countDataAreaPolygon = count($dataAreaPolygon);
            }

            $checkAreaPolygon['count']  = $countDataAreaPolygon;
        }

        $checkAreaPolygon['MemberID']   = $MemberID;

        $dataFooter['checkAreaPolygon'] = $checkAreaPolygon;

        $this->load->view('cetak_farmer_beneficiary_profiles_footer_v2',$dataFooter);
    }

    public function cetak_beneficiary_profiles($MemberID, $countData = 1, $increData = 0){
        $this->load->library('awsfileupload');
        $this->load->model('report/mreport');
        $data = array();

        //get data member
        $data['member'] = $this->mreport->getMemberDetail($MemberID);
        $data['gardens'] = $this->mreport->getGardenData($MemberID);
        $data['gardens_polygon'] = $this->mreport->getGardenPolygonData($data['gardens']);

        //cek apakah ada data koordinat gardennya
        $data['gardens_coordinate_exists'] = $this->mreport->checkGardenCoordinateExist($data['gardens']);

        $data['garden_baseline'] = $this->mreport->getGardenDataBaseline($MemberID);
        $data['garden_postline'] = $this->mreport->getGardenDataPostline($MemberID, $data["garden_baseline"]);

        $data['traceability'] = $this->mreport->getTraceability($MemberID);
        $data['traceability_details'] = $this->mreport->getTraceabilityDetails($MemberID);

        //logo
        $this->load->model('project_process/mproject_process');
        $DistrictID = substr($data['member']['VillageID'], 0,4);
        $data['logos'] = $this->mproject_process->getPrintLogoHeaderFarmerNew($MemberID);
        $data['qrcode_pic']     = $this->QrcodeGenerator($data['member']['MemberDisplayID']);

        //survey log
        $data['surveys_log'] = $this->mreport->getSurveysLogGarden($MemberID);

        //data training
        $getTrainingData   = $this->mreport->getDataTrainingPrint($MemberID);
        $getCoachingData   = $this->mreport->getDataFarmerCoachingPrintout($MemberID);

        $data['trainings'] = array_merge($getTrainingData, $getCoachingData);

        $data['polygon_list'] = $this->mreport->getDataPolygonList($MemberID);

        $data['areaPolygon'] = $this->mreport->getDataPlotPolygonMapNew($MemberID);
        // if($data['areaPolygon'] == false) return $this->response(false, 400);

        $data['centerLatLongPolygon'] = $this->mreport->getDataPlotPolygonCenterCoorOnlyFirst($MemberID);

        if($data['centerLatLongPolygon']['latitude'] == ''){
            if (!empty($data['areaPolygon'])) {
                $jsondata = $data['areaPolygon'][0];

                for ($j = 0; $j < count($jsondata); $j++) {
                    if ($jsondata[$j][0] != "" && $jsondata[$j][1] != "") {
                        $data['centerLatLongPolygon'][0] = $jsondata[$j][0];
                        $data['centerLatLongPolygon'][1] = $jsondata[$j][1];
                    }
                }
            }
        }

        // echo "<pre>";print_r($data['areaPolygon']);die;

        $this->load->view('cetak_beneficiary_profiles_v2_new', $data);
    }

    public function qrcode_generator_get($FarmerID) {
        require_once APPPATH . 'third_party/phpqrcode/qrlib.php';
        $barcodeNya = QRcode::png($FarmerID, NULL, QR_ECLEVEL_L, 3, 2);
        echo $barcodeNya;
        exit;
    }

    public function old_cetak_beneficiary_profiles_get() {
        if($this->get('bahasa') != ""){
            $this->load->language('general', $this->get('bahasa'));
        }else{
            if($_SESSION['language'] == "Indonesia")
                $this->load->language('general', 'indonesia');
            else
                $this->load->language('general', 'english');
        }

        $CpgID = $this->get('CpgID');
        $FarmerID = $this->get('FarmerID');
        $FarmerIDs = explode('::', $FarmerID);

        if (count($FarmerIDs) > 1) {
            $dataHeader['titleNya'] = "Farmer Profile";
        } else {
            $dataFarmer = $this->mfarmer->readFarmer($FarmerID);
            $dataHeader['titleNya'] = "Farmer Profile $FarmerID – " . $dataFarmer['FarmerName'];
        }
        $this->load->view('cetak_farmer_beneficiary_profiles_header_v2', $dataHeader);

        if (strpos($FarmerID, '::')) {
            // Select multiple FarmerID
            $countData = count($FarmerIDs);
            $increData = 1;
            foreach ($FarmerIDs as $key => $FarmerID) {
                $this->cetak_beneficiary_profiles($FarmerID, $countData, $increData);
                $increData++;
            }
        } else {
            // Select single FarmerID
            $this->cetak_beneficiary_profiles($FarmerID);
        }
        $this->load->view('cetak_farmer_beneficiary_profiles_footer_v2');
    }

    private function old_cetak_beneficiary_profiles($FarmerID, $countData = 1, $increData = 0) {
        $data['farmer'] = $this->mfarmer->readFarmer($FarmerID);
        $data['family'] = $this->mfarmer->getFarmerFamily($FarmerID);
        $data['gardens'] = $this->mfarmer->getFarmerGardens($FarmerID);
        $data['trainings'] = $this->mfarmer->getFarmerTrainings($FarmerID);
        //$data['trainings'] = array_merge($data['trainings'],$data['trainings']);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $data['cooperative'] = $this->mfarmer->getFarmerCooperative($FarmerID);
        $data['logos'] = $this->mfarmer->readPartnerLogo($FarmerID, '');
        if ($data['farmer']['isCertified'] == '1') {
            $data['logo_certification'] = $this->mfarmer->readFarmerLogoCert($FarmerID);
        }
        $data['baseline'] = $this->mfarmer->getGardenBaseline($FarmerID);
        $data['postline'] = $this->mfarmer->getGardenPostline($FarmerID);

        $data['countData'] = $countData;
        $data['increData'] = $increData;

        //echo '<pre>'; print_r($data); exit;
        $this->load->view('cetak_beneficiary_profiles_v2', $data);
    }

    public function cetak_result_nutrisi_old_get($id, $survey, $param2 = '', $value2 = '', $param3 = '', $value3 = '') {
        $this->load->language('general', $this->get('lang'));
        if ($id == 'FarmerID' and strpos($survey, '::')) {
            $FarmerID = explode('::', $survey);
            for ($i = 0; $i < sizeof($FarmerID); $i++) {

                if ($this->get('SurveyID') == "latest") {
                    $surveyIDs = $this->mfarmer->getLatestSurveyId($FarmerID[$i]);
                    $survey_id = $surveyIDs[0]['SurveyNr'];
                } else {
                    $survey_id = $this->get('SurveyID');
                }

                $data['data'] = $this->mfarmer->readFarmer($FarmerID[$i]);
                $data['nutrition'] = $this->mfarmer->readFarmerNutrition($FarmerID[$i], $survey_id);
                $data['family'] = $this->mfarmer->readFarmerFamilyNutrition($FarmerID[$i]);
                $data['logos'] = $this->mfarmer->readPartnerLogo($FarmerID[$i], $value3);

                $this->load->view('cetak_result_nutrisi', $data);
            }
            return;
        }

        if ($this->get('SurveyID') == "latest") {
            $surveyIDs = $this->mfarmer->getLatestSurveyId($this->get('FarmerID'));
            $survey_id = $surveyIDs[0]['SurveyNr'];
        } else {
            $survey_id = $this->get('SurveyID');
        }

        $data['data'] = $this->mfarmer->readFarmer($this->get('FarmerID'));
        $data['nutrition'] = $this->mfarmer->readFarmerNutrition($this->get('FarmerID'), $survey_id);
        $data['family'] = $this->mfarmer->readFarmerFamilyNutrition($this->get('FarmerID'));
        $data['logos'] = $this->mfarmer->readPartnerLogo($this->get('FarmerID'), $value3);

        $this->load->view('cetak_result_nutrisi', $data);
    }

    public function cetak_result_nutrisi_get(){
        $this->load->model('nursery/mnursery');
        $data = array();

        //get param
        $FarmerID = $this->get('FarmerID');
        $SurveyNr = $this->get('SurveyID');
        $bahasa = $this->get('lang');
        $jenisForm = $this->get('jenis_form');

        //set bahasa
        if($bahasa != ""){
            $this->load->language('general', $bahasa);
            $data['bahasanya'] = $bahasa;
        }else{
            if($_SESSION['language'] == "Indonesia"){
                $this->load->language('general', 'indonesia');
                $data['bahasanya'] = 'indonesia';
            }else{
                $this->load->language('general', 'english');
                $data['bahasanya'] = 'english';
            }
        }

        //get SurveyNr (begin)
        if($SurveyNr == "latest"){
            $SurveyNr = $this->mfarmer->getLatestSurveyNutrition($FarmerID);
        }
        $data['SurveyNr'] = $SurveyNr;
        //get SurveyNr (end)


        //cek formnya
        if($jenisForm == lang('Form Kosong')){
            $data['formNya'] = "kosong";
        }else{
            $data['formNya'] = "result";
            $data['nutrition'] = $this->mfarmer->readFarmerNutrition($FarmerID, $SurveyNr);
            //echo '<pre>'; print_r($data['nutrition']); exit;
        }

        //ambil data
        $data['farmer'] = $this->mfarmer->readFarmer($FarmerID);
        //echo '<pre>'; print_r($data['farmer']); exit;

        //ambil logo print
        $data['logos'] = $this->mnursery->getPartnerLogoByDistrict($data['farmer']['DistrictID']);

        $this->load->view('cetak_nutrisi_rich_result', $data);
    }

    public function cetak_ppi_get(){
        $this->load->model('nursery/mnursery');
        $data = array();

        //get param
        $FarmerID   = $this->get('FarmerID');
        $SurveyNr   = $this->get('SurveyID');
        $bahasa     = $this->get('lang');
        $jenisForm  = $this->get('jenis_form');

        //set bahasa
        if($bahasa != ""){
            $this->load->language('general', $bahasa);
            $data['bahasanya'] = $bahasa;
        }else{
            if($_SESSION['language'] == "Indonesia"){
                $this->load->language('general', 'indonesia');
                $data['bahasanya'] = 'indonesia';
            }else{
                $this->load->language('general', 'english');
                $data['bahasanya'] = 'english';
            }
        }

        //get SurveyNr (begin)
        if($SurveyNr == "latest"){
            $SurveyNr = $this->mfarmer->getLatestSurveyPPI($FarmerID);
        }
        $data['SurveyNr'] = $SurveyNr;
        //get SurveyNr (end)

        //cek formnya
        if($jenisForm == lang('Form Kosong')){
            $data['formNya'] = "kosong";
        } else {
            $data['formNya'] = "result";
            if ($ppi2012 = $this->mfarmer->readFarmerPpi2012($FarmerID, $SurveyNr)) {
                $data['survey'] = $ppi2012;
            } elseif ($ppi = $this->mfarmer->readFarmerPpi($FarmerID, $SurveyNr)) {
                $data['survey'] = $ppi;
            }
        }
            // echo '<pre>'; print_r($data['survey']); echo '</pre>'; exit;

        //ambil data
        $data['farmer'] = $this->mfarmer->readFarmer($FarmerID);
        //echo '<pre>'; print_r($data['farmer']); exit;

        //ambil logo print
        // $data['logos'] = $this->mnursery->getPartnerLogoByDistrict($data['farmer']['DistrictID']);
        $data['logos'] = $this->mfarmer->readPartnerLogo($this->get('FarmerID'));
        // echo '<pre>'; print_r($data); echo '</pre>'; exit;
        $this->load->view('cetak_ppi_rich_result', $data);
    }

    public function cetak_result_ppi2012_get($id, $survey, $param2 = '', $value2 = '', $param3 = '', $value3 = '') {
        $this->load->language('general', $this->get('lang'));
        if ($id == 'FarmerID' and strpos($survey, '::')) {
            $FarmerID = explode('::', $survey);
            for ($i = 0; $i < sizeof($FarmerID); $i++) {

                if ($this->get('SurveyID') == "latest") {
                    $surveyIDs = $this->mfarmer->getLatestSurveyId($FarmerID[$i]);
                    $survey_id = $surveyIDs[0]['SurveyNr'];
                } else {
                    $survey_id = $this->get('SurveyID');
                }

                $data['data'] = $this->mfarmer->readFarmer($FarmerID[$i]);
                $data['ppi2012'] = $this->mfarmer->readFarmerPpi2012($FarmerID[$i], $survey_id);
                $data['logos'] = $this->mfarmer->readPartnerLogo($FarmerID[$i], $value3);

                $this->load->view('cetak_result_ppi2012', $data);
            }
            return;
        }

        if ($this->get('SurveyID') == "latest") {
            $surveyIDs = $this->mfarmer->getLatestSurveyId($this->get('FarmerID'));
            $survey_id = $surveyIDs[0]['SurveyNr'];
        } else {
            $survey_id = $this->get('SurveyID');
        }

        $data['data'] = $this->mfarmer->readFarmer($this->get('FarmerID'));
        $data['ppi2012'] = $this->mfarmer->readFarmerPpi2012($this->get('FarmerID'), $survey_id);
        $data['logos'] = $this->mfarmer->readPartnerLogo($this->get('FarmerID'), $value3);

        $this->load->view('cetak_result_ppi2012', $data);
    }

    public function cetak_sum_garden_get($farmerId, $tipe) {
        switch ($tipe) {
            case 'garden':
                $data = $this->mfarmer->readSumGarden($farmerId);
                $filename = 'farmer_summary_garden.xls';
                $sheet['title'] = 'Farmer Summary Garden';
                $sheet['header'][] = 'Farmer Summary Garden';
                break;
            case 'training':
                $data['data'] = $this->mfarmer->readTrainings($farmerId);
                $filename = 'farmer_summary_training.xls';
                $sheet['title'] = 'Farmer Summary Training';
                $sheet['header'][] = 'Farmer Summary Training';
                break;
            case 'ppi':
                $data = $this->mfarmer->readSumPpi($farmerId);
                $filename = 'farmer_summary_ppi.xls';
                $sheet['title'] = 'Farmer Summary PPI';
                $sheet['header'][] = 'Farmer Summary PPI';
                break;
            case 'nutrisi':
                $data = $this->mfarmer->readSumNutrition($farmerId);
                $filename = 'farmer_summary_nutrisi.xls';
                $sheet['title'] = 'Farmer Summary Nutrisi';
                $sheet['header'][] = 'Farmer Summary Nutrisi';
                break;
            case 'harvest':
                $data = $this->mfarmer->readSumPost($farmerId);
                $filename = 'farmer_summary_harvest.xls';
                $sheet['title'] = 'Farmer Summary Harvest';
                $sheet['header'][] = 'Farmer Summary Harvest';
                break;
            case 'finance':
                $data = $this->mfarmer->readSumAff($farmerId);
                $filename = 'farmer_summary_finance.xls';
                $sheet['title'] = 'Farmer Summary Finance';
                $sheet['header'][] = 'Farmer Summary Finance';
                break;
        }

        if ($data) {
            $this->load->library('Excel', null, 'PHPExcel');
            $this->PHPExcel->filename($filename);
            $keys = array_keys($data['data'][0]);
            $sheet['cols'] = array();
            foreach ($keys as $title) {
                $sheet['cols'][] = array(
                    'name' => $title,
                    'data' => $title,
                    'size' => 25,
                    'align' => 'left',
                    'wrap' => true,
                );
            }
            /*
              $sheet['cols'] = array(
              array(
              'name' => 'Garden',
              'data' => 'GardenNr',
              'size' => 40,
              'align'=> 'left',
              'wrap' => true,
              )
              ,array(
              'name' => 'Survey Nr',
              'data' => 'Survey',
              'size' => 15,
              'align'=> 'left',
              'wrap' => true
              )
              ,array(
              'name' => 'Interview Date',
              'data' => 'DateInterview',
              'size' => 15,
              'align'=> 'left',
              'wrap' => true
              )
              ,array(
              'name' => 'Validation Date',
              'data' => 'DateValid',
              'size' => 15,
              'align'=> 'left',
              'wrap' => true
              )
              ,array(
              'name' => 'Validation Status',
              'data' => 'StatusValid',
              'size' => 15,
              'align'=> 'left',
              'wrap' => true
              )
              ,array(
              'name' => 'Created By',
              'data' => 'UserCreated',
              'size' => 20,
              'align'=> 'left',
              'wrap' => true
              )
              ,array(
              'name' => 'Last Updated By',
              'data' => 'LastUpdatedBy',
              'size' => 12,
              'align'=> 'left',
              'wrap' => true
              ),
              );
             *
             */
            $sheet['data'] = $data['data'];

            $path = $this->PHPExcel->create(compact('sheet'), '');
            header('Location: ' . base_url() . 'farmer_summary_garden.xls');
            exit;
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function farmerl_cert_get() {
        $data = $this->mfarmer->readCert($this->get('id'), $this->get('GardenNr'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function farmerl_cert_post() {
        $data = $this->mfarmer->updateCert($this->post('certFarmerID'), $this->post('certGardenNr'), $this->post('Certification'), $this->post('CandidateSelection'), $this->post('CertificationHolder'), $this->post('FirstYear'), $this->post('SecondYear'), $this->post('ThirdYear'), $this->post('FourthYear'), $this->post('FirstYear_ICS'), $this->post('SecondYear_ICS'), $this->post('ThirdYear_ICS'), $this->post('FourthYear_ICS'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function sum_trainings_get() {
        $data = $this->mfarmer->readTrainings($this->get('farmerid'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

//antara
    public function farmerl_antara_get() {
        $data = $this->mfarmer->readAntara($this->get('jenis'), $this->get('farmer_id'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function farmerl_survey_delete() {
        if (!$this->delete('farmer_id')) {
            $this->response(null, 400);
        }

        $data = $this->mfarmer->deleteSurveyFarmer($this->delete('garden'), $this->delete('survey'), $this->delete('jenis'), $this->delete('farmer_id'), $_SESSION['userid']);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Farmer could not be delete'), 404);
        }
    }

//AFF
    public function Survey_affs_get() {
        $data = $this->mfarmer->readSurveyAffs($this->get('farmer_id'), $this->get('jenis'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function farmerl_aff_get() {
        if ($this->get('surveyNr') == 'Tambah Baru') {
            return;
        }

        if (!$this->get('id')) {
            $this->response(null, 400);
        }

        $data = $this->mfarmer->readFarmerAff($this->get('id'), $this->get('surveyNr'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Farmer could not be found'), 404);
        }
    }

    public function farmerl_affPrevData_get() {
        $data = $this->mfarmer->readFarmerAffPrevData($this->get('id'), $this->get('surveyNr'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Farmer could not be found'), 404);
        }
    }

//    function fillNulAff($farmerid,$nsurvey,$field)
    //    {
    //        $nsurvey = $nsurvey-1;
    //        $this->db->select($field);
    //        $q = $this->db->get_where('ktv_farmer_financial',array('FarmerID'=>$farmderid,'SurveyNr'=>$nsurvey))->row();
    //        return $q->field;
    //    }

    public function aff_post() {
        /* $data = $this->mfarmer->updateAff($this->post('aSurveyNr'),$this->post('aInterviewDate'),$this->post('Account'),$this->post('AccountTypeTabungan'),$this->post('AccountTypeDeposito'),$this->post('AccountTypeKoran'),
          $this->post('AccountTypeLainnya'),$this->post('AccountHolderFarmer'),$this->post('AccountHolderName'),$this->post('AccountBankName'),$this->post('AccountBankBranch'),
          $this->post('AccountNumber'),$this->post('AccountNoDetails'),$this->post('DepositWithdrawnMoneyLast12m'),$this->post('AccountFeesToPay'),$this->post('AccountInterestRate'),
          $this->post('MoneyUsageHarian'),$this->post('MoneyUsageTabung'),$this->post('MoneyUsageInvestasi'),$this->post('MoneyUsageEmas'),$this->post('MoneyUsageKonsumsi'),
          $this->post('NotSavingJauh'),$this->post('NotSavingTidakBeruang'),$this->post('NotSavingBiayaTinggi'),$this->post('NotSavingTidakPercaya'),$this->post('NotSavingAdaMenabung'),
          $this->post('NotSavingLainnya'),$this->post('SavingUnitRumah'),$this->post('SavingUnitBank'),$this->post('SavingUnitKoperasi'),$this->post('SavingUnitPedagang'),
          $this->post('SavingUnitArisan'),$this->post('SavingUnitOrang'),$this->post('SavingUnitLembaga'),$this->post('SavingUnitMeminjamkan'),$this->post('DistanceSavingLocation'),
          $this->post('AmountSaving'),$this->post('FutureReasonSekolah'),$this->post('FutureReasonRumahTangga'),$this->post('FutureReasonSumbangan'),$this->post('FutureReasonDarurat'),
          $this->post('FutureReasonKesehatan'),$this->post('FutureReasonInvestasiKebun'),$this->post('FutureReasonInvestasiLain'),$this->post('FutureReasonRumah'),
          $this->post('FutureReasonLahan'),$this->post('FutureReasonKendaraan'),$this->post('FutureReasonHaji'),$this->post('FutureReasonPensiun'),$this->post('FutureReasonLain'),
          $this->post('ImportantFactorKemanan'),$this->post('ImportantFactorLikuiditas'),$this->post('ImportantFactorAksesibilitas'),$this->post('ImportantFactorKepercayaan'),
          $this->post('ImportantFactorBiaya'),$this->post('ImportantFactorBunga'),$this->post('ImportantFactorLain'),$this->post('LoanYesNo'),$this->post('AmountCurrentLoan'),
          $this->post('AmountOutsCurrentLoan'),$this->post('LoanUnitTengkulak'),$this->post('LoanUnitKeluarga'),$this->post('LoanUnitRentenir'),$this->post('LoanUnitBank'),
          $this->post('LoanUnitKoperasi'),$this->post('LoanUnitMasjid'),$this->post('LoanUnitLainnya'),$this->post('PreviousLoan'),$this->post('CollateralCurrentLoan'),$this->post('EasyCurrentLoan'),
          $this->post('DisburseIntervalCurrentLoan'),$this->post('RepaymentScheduleCurrentLoan'),$this->post('EasyGetNewLoan'),$this->post('UsageCurrentLoanHarian'),
          $this->post('UsageCurrentLoanSekolah'),$this->post('UsageCurrentLoanRumahTangga'),$this->post('UsageCurrentLoanSumbangan'),$this->post('UsageCurrentLoanHutang'),
          $this->post('UsageCurrentLoanDarurat'),$this->post('UsageCurrentLoanKesehatan'),$this->post('UsageCurrentLoanInvestasiKebun'),$this->post('UsageCurrentLoanInvestasiLain'),
          $this->post('UsageCurrentLoanRumah'),$this->post('UsageCurrentLoanLahan'),$this->post('UsageCurrentLoanKendaraan'),$this->post('UsageCurrentLoanHaji'),
          $this->post('UsageCurrentLoanPensiun'),$this->post('UsageCurrentLoanLainnya'),$this->post('TerminatedLoan'),$this->post('MoneyToRepayLoanPenghasilan'),
          $this->post('MoneyToRepayLoanPinjaman'),$this->post('MoneyToRepayLoanTanah'),$this->post('MoneyToRepayLoanTernak'),$this->post('MoneyToRepayLoanDeposito'),
          $this->post('MoneyToRepayLoanLainnya'),$this->post('ProfitSharingLoan'),$this->post('ResponsibilityLoan'),$this->post('WorryToRepayLoan'),$this->post('DifficultCoverExpenses'),
          $this->post('PostponeExpensesSewaRumah'),$this->post('PostponeExpensesKebun'),$this->post('PostponeExpensesMakanan'),$this->post('PostponeExpensesKesehatan'),
          $this->post('PostponeExpensesSosial'),$this->post('PostponeExpensesListrik'),$this->post('PostponeExpensesPendidikan'),$this->post('PostponeExpensesSandang'),
          $this->post('PostponeExpensesAngsuran'),$this->post('PostponeExpensesLainnya'),$this->post('DifficultSocialContributions'),$this->post('MoneyUrgentExpensesTabungan'),
          $this->post('MoneyUrgentExpensesMeminjamKeluarga'),$this->post('MoneyUrgentExpensesMeminjamTengkulak'),$this->post('MoneyUrgentExpensesMenjual'),
          $this->post('MoneyUrgentExpensesLainnya'),$this->post('CostUnsubsidizedFertilizer'),$this->post('OtherIncome'),$this->post('PensionPlan'),$this->post('OtherIncomeRegular'),
          $this->post('SourceOtherIncomeGajiTetap'),$this->post('SourceOtherIncomeGajiPasangan'),$this->post('SourceOtherIncomeUsaha'),$this->post('SourceOtherIncomeFamily'),
          $this->post('SourceOtherIncomeLainnya'),$this->post('AmountOtherIncome'),$this->post('CocoaProfitableBusiness'),$this->post('LoanBetterThanSaving'),
          $this->post('UnsubsidizedFertilizerProfitable'),$this->post('HighInterestRate'),$this->post('LoanWithTrader'),$this->post('BetterWetDriedBeans'),$this->post('GoodLoanClient'),
          $this->post('TrustGroupMembers'),$this->post('RepayLoanGroupMember'),$this->post('TrustBank'),$this->post('CocoaFarmPayExpenses'),$this->post('DiscipilinedSaveMoney'),
          $this->post('TradersRich'),$this->post('CollateralOfferedBank'),$this->post('ManyCocoaFarmSale'),$this->post('SatisfiedCocoaBusiness'),$this->post('PayCocoaBetterInterest'),
          $this->post('NeedLoan'),$this->post('MobilePhone'),$this->post('LoanAnalysisKnowledge'),$this->post('IslamicFinancialAwareness'),$this->post('LearnToSaveMoney'),
          $this->post('CocoaPriceToday'),$this->post('ReasonNotHavePhoneTidakButuh'),$this->post('ReasonNotHavePhoneMahal'),$this->post('ReasonNotHavePhoneSinyal'),
          $this->post('ReasonNotHavePhoneLainnya'),$this->post('ValueCocoaFarm'),$this->post('InsuranceKnowledge'),$this->post('PastNowInsurance'),$this->post('InsuranceTypeMotor'),
          $this->post('InsuranceTypePanen'),$this->post('InsuranceTypeBanjir'),$this->post('InsuranceTypeKemarau'),$this->post('InsuranceTypeMobil'),$this->post('InsuranceTypeKesehatan'),
          $this->post('InsuranceTypeJiwa'),$this->post('InsuranceTypeLainnya'),
          $this->post('aFarmerID'),$_SESSION['userid']); */

        $arrTmp = explode("|", $this->post('aSurveyNr'));
        $SurveyNr = $arrTmp[0];

        $data = $this->mfarmer->updateAff(
                $this->post('aFarmerID'), $SurveyNr, $this->post('aInterviewDate'), $this->post('isValid'), $this->post('Account'), $this->post('AccountTypeTabungan'), $this->post('AccountTypeDeposito'), $this->post('AccountTypeKoran'), $this->post('AccountTypeLainnya'), $this->post('AccountHolderFarmer'), $this->post('AccountHolderName'), $this->post('AccountBankID'), $this->post('AccountBankBranch'), $this->post('AccountNumber'), $this->post('AccountNoDetails'), $this->post('DepositWithdrawnMoneyLast12m'), $this->post('AccountFeesToPay'), $this->post('AccountInterestRate'), $this->post('MoneyUsageHarian'), $this->post('MoneyUsageTabung'), $this->post('MoneyUsageInvestasi'), $this->post('MoneyUsageEmas'), $this->post('MoneyUsageKonsumsi'), $this->post('NotSavingJauh'), $this->post('NotSavingTidakBeruang'), $this->post('NotSavingBiayaTinggi'), $this->post('NotSavingTidakPercaya'), $this->post('NotSavingAdaMenabung'), $this->post('NotSavingLainnya'), $this->post('SavingUnitRumah'), $this->post('SavingUnitBank'), $this->post('SavingUnitKoperasi'), $this->post('SavingUnitPedagang'), $this->post('SavingUnitArisan'), $this->post('SavingUnitOrang'), $this->post('SavingUnitLembaga'), $this->post('SavingUnitMeminjamkan'), $this->post('DistanceSavingLocation'), $this->post('AmountSaving'), $this->post('FutureReasonSekolah'), $this->post('FutureReasonRumahTangga'), $this->post('FutureReasonSumbangan'), $this->post('FutureReasonDarurat'), $this->post('FutureReasonKesehatan'), $this->post('FutureReasonInvestasiKebun'), $this->post('FutureReasonInvestasiLain'), $this->post('FutureReasonRumah'), $this->post('FutureReasonLahan'), $this->post('FutureReasonKendaraan'), $this->post('FutureReasonHaji'), $this->post('FutureReasonPensiun'), $this->post('FutureReasonLain'), $this->post('ImportantFactorKemanan'), $this->post('ImportantFactorLikuiditas'), $this->post('ImportantFactorAksesibilitas'), $this->post('ImportantFactorKepercayaan'), $this->post('ImportantFactorBiaya'), $this->post('ImportantFactorBunga'), $this->post('ImportantFactorLain'), $this->post('LoanYesNo'), $this->post('AmountCurrentLoan'), $this->post('AmountOutsCurrentLoan'), $this->post('LoanUnitTengkulak'), $this->post('LoanUnitKeluarga'), $this->post('LoanUnitRentenir'), $this->post('LoanUnitBank'), $this->post('LoanUnitKoperasi'), $this->post('LoanUnitMasjid'), $this->post('LoanUnitLainnya'), $this->post('PreviousLoan'), $this->post('CollateralCurrentLoan'), $this->post('EasyCurrentLoan'), $this->post('DisburseIntervalCurrentLoan'), $this->post('RepaymentScheduleCurrentLoan'), $this->post('EasyGetNewLoan'), $this->post('UsageCurrentLoanHarian'), $this->post('UsageCurrentLoanSekolah'), $this->post('UsageCurrentLoanRumahTangga'), $this->post('UsageCurrentLoanSumbangan'), $this->post('UsageCurrentLoanHutang'), $this->post('UsageCurrentLoanDarurat'), $this->post('UsageCurrentLoanKesehatan'), $this->post('UsageCurrentLoanInvestasiKebun'), $this->post('UsageCurrentLoanInvestasiLain'), $this->post('UsageCurrentLoanRumah'), $this->post('UsageCurrentLoanLahan'), $this->post('UsageCurrentLoanKendaraan'), $this->post('UsageCurrentLoanHaji'), $this->post('UsageCurrentLoanPensiun'), $this->post('UsageCurrentLoanLainnya'), $this->post('TerminatedLoan'), $this->post('MoneyToRepayLoanPenghasilan'), $this->post('MoneyToRepayLoanPinjaman'), $this->post('MoneyToRepayLoanTanah'), $this->post('MoneyToRepayLoanTernak'), $this->post('MoneyToRepayLoanDeposito'), $this->post('MoneyToRepayLoanLainnya'), $this->post('ProfitSharingLoan'), $this->post('ResponsibilityLoan'), $this->post('WorryToRepayLoan'), $this->post('DifficultCoverExpenses'), $this->post('PostponeExpensesSewaRumah'), $this->post('PostponeExpensesKebun'), $this->post('PostponeExpensesMakanan'), $this->post('PostponeExpensesKesehatan'), $this->post('PostponeExpensesSosial'), $this->post('PostponeExpensesListrik'), $this->post('PostponeExpensesPendidikan'), $this->post('PostponeExpensesSandang'), $this->post('PostponeExpensesAngsuran'), $this->post('PostponeExpensesLainnya'), $this->post('DifficultSocialContributions'), $this->post('MoneyUrgentExpensesTabungan'), $this->post('MoneyUrgentExpensesMeminjamKeluarga'), $this->post('MoneyUrgentExpensesMeminjamTengkulak'), $this->post('MoneyUrgentExpensesMenjual'), $this->post('MoneyUrgentExpensesLainnya'), $this->post('CostUnsubsidizedFertilizer'), $this->post('OtherIncome'), $this->post('PensionPlan'), $this->post('OtherIncomeRegular'), $this->post('SourceOtherIncomeGajiTetap'), $this->post('SourceOtherIncomeGajiPasangan'), $this->post('SourceOtherIncomeUsaha'), $this->post('SourceOtherIncomeFamily'), $this->post('SourceOtherIncomeLainnya'), $this->post('AmountOtherIncome'), $this->post('CocoaProfitableBusiness'), $this->post('LoanBetterThanSaving'), $this->post('UnsubsidizedFertilizerProfitable'), $this->post('HighInterestRate'), $this->post('LoanWithTrader'), $this->post('BetterWetDriedBeans'), $this->post('GoodLoanClient'), $this->post('TrustGroupMembers'), $this->post('RepayLoanGroupMember'), $this->post('TrustBank'), $this->post('CocoaFarmPayExpenses'), $this->post('DiscipilinedSaveMoney'), $this->post('TradersRich'), $this->post('CollateralOfferedBank'), $this->post('ManyCocoaFarmSale'), $this->post('SatisfiedCocoaBusiness'), $this->post('PayCocoaBetterInterest'), $this->post('NeedLoan'), $this->post('MobilePhone'), $this->post('LoanAnalysisKnowledge'), $this->post('IslamicFinancialAwareness'), $this->post('LearnToSaveMoney'), $this->post('CocoaPriceToday'), $this->post('CocoaPriceTodayInfo'), $this->post('ReasonNotHavePhoneTidakButuh'), $this->post('ReasonNotHavePhoneMahal'), $this->post('ReasonNotHavePhoneSinyal'), $this->post('ReasonNotHavePhoneLainnya'), $this->post('ValueCocoaFarm'), $this->post('InsuranceKnowledge'), $this->post('PastNowInsurance'), $this->post('InsuranceTypeMotor'), $this->post('InsuranceTypePanen'), $this->post('InsuranceTypeBanjir'), $this->post('InsuranceTypeKemarau'), $this->post('InsuranceTypeMobil'), $this->post('InsuranceTypeKesehatan'), $this->post('InsuranceTypeJiwa'), $this->post('InsuranceTypeLainnya'), $this->post('DateCreated'), $_SESSION['userid']
        );

        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Farmer could not be found'), 404);
        }
    }

    public function sum_aff_get() {
        $data = $this->mfarmer->readSumAff($this->get('farmerid'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function cetak_gfp_get(){
        $this->load->model('nursery/mnursery');
        $data = array();

        //get param
        $FarmerID   = $this->get('FarmerID');
        $SurveyNr   = $this->get('SurveyID');
        $bahasa     = $this->get('lang');
        $jenisForm  = $this->get('jenis_form');

        //set bahasa
        if($bahasa != ""){
            $this->load->language('general', $bahasa);
            $data['bahasanya'] = $bahasa;
        }else{
            if($_SESSION['language'] == "Indonesia"){
                $this->load->language('general', 'indonesia');
                $data['bahasanya'] = 'indonesia';
            }else{
                $this->load->language('general', 'english');
                $data['bahasanya'] = 'english';
            }
        }

        //get SurveyNr (begin)
        if($SurveyNr == "latest"){
            $SurveyNr = $this->mfarmer->getLatestSurveyFinance($FarmerID);
        }
        $data['SurveyNr'] = $SurveyNr;
        //get SurveyNr (end)

        //cek formnya
        if($jenisForm == lang('Form Kosong')){
            $data['formNya'] = "kosong";
        } else {
            $data['formNya'] = "result";
            $fin = $this->mfarmer->readFinanceCetakIsi($FarmerID, $SurveyNr);
            $data['survey'] = $fin[0][0];
        }

        //ambil data
        $data['farmer'] = $this->mfarmer->readFarmer($FarmerID);
        //echo '<pre>'; print_r($data['farmer']); exit;

        //ambil logo print
        // $data['logos'] = $this->mnursery->getPartnerLogoByDistrict($data['farmer']['DistrictID']);
        $data['logos'] = $this->mfarmer->readPartnerLogo($this->get('FarmerID'));
        // echo '<pre>'; print_r($data); echo '</pre>'; exit;
        $this->load->view('cetak_gfp', $data);
    }


    public function cetak_aff_get($id, $survey, $param2 = '', $value2 = '', $param3 = '', $value3 = '') {
        $this->load->language('general', $this->get('lang'));
        $farmer_id = $this->get('FarmerID') == '' ? $id : $this->get('FarmerID');
        $survey_id = $this->get('SurveyID') == '' ? $survey : $this->get('SurveyID');
        if (!is_int($survey_id)) {
            $tmp = explode('-', $survey_id);
            $survey_id = $tmp[0];
        }
        if ($id == 'FarmerID' and strpos($survey, '::')) {
            $FarmerID = explode('::', $survey);
            for ($i = 0; $i < sizeof($FarmerID); $i++) {

                if ($this->get('SurveyID') == "latest") {
                    $surveyIDs = $this->mfarmer->getLatestSurveyId($FarmerID[$i]);
                    $survey_id = $surveyIDs[0]['SurveyNr'];
                }

                $data['data'] = $this->mfarmer->readFarmer($FarmerID[$i]);
                $data['detail'] = $this->mfarmer->readFarmerAff($FarmerID[$i], $survey_id);
                $data['logos'] = $this->mfarmer->readPartnerLogo($FarmerID[$i], $value3);
                $data['survey'] = $this->mfarmer->readSurvey($survey_id);
                $data['dataform'] = $this->mfarmer->readFinanceCetakIsi($FarmerID[$i], $survey_id);
                $this->load->view('cetak_basic_aff', $data);
            }
            return;
        }

        if ($this->get('SurveyID') == "latest") {
            $surveyIDs = $this->mfarmer->getLatestSurveyId($farmer_id);
            $survey_id = $surveyIDs[0]['SurveyNr'];
        }

        $data['data'] = $this->mfarmer->readFarmer($farmer_id);
        $data['detail'] = $this->mfarmer->readFarmerAff($farmer_id, $survey_id);
        $data['logos'] = $this->mfarmer->readPartnerLogo($farmer_id, $value3);
        $data['survey'] = $this->mfarmer->readSurvey($survey_id);
        $data['dataform'] = $this->mfarmer->readFinanceCetakIsi($farmer_id, $survey_id);
        $this->load->view('cetak_basic_aff', $data);
    }

    public function cetak_basic_aff_get($param1 = '', $value1 = '', $param2 = '', $value2 = '', $param3 = '', $value3 = '') {
        $this->load->language('general', $this->get('lang'));
        if ($param1 == 'CpgBatchTrainingID') {
            $this->load->model('cpg/mcpg');
            $farmer = $this->mcpg->readParticipants($value1);
            for ($i = 0; $i < sizeof($farmer['data']); $i++) {
                $farmer_id[] = $farmer['data'][$i]['pFarmerID'];
            }
            $survey_id = $value2;
            $value3 = $farmer['data'][0]['PartnerID'];
        } elseif ($param1 == 'CpgKaderTrainingID') {
            $this->load->model('training/mkader');
            $farmer = $this->mkader->readParticipants($value1);
            for ($i = 0; $i < sizeof($farmer['data']); $i++) {
                $farmer_id[] = $farmer['data'][$i]['farmer_id'];
            }
            $survey_id = $value2;
            $value3 = $farmer['data'][0]['PartnerID'];
        } else if ($param1 == 'cetak_basic_aff') {
//             $data['tes'] = 'adsads';
        } else {
            $data['tes'] = $param1;
            $data['dataform'] = $this->mfarmer->readFinanceCetak($this->get('FarmerID'), $this->get('SurveyID'));
            $farmer_id[] = $this->get('FarmerID');
            $survey_id = $this->get('SurveyID');
        }
        for ($i = 0; $i < sizeof($farmer_id); $i++) {

            if ($survey_id == "latest") {
                $surveyIDs = $this->mfarmer->getLatestSurveyId($farmer_id[$i]);
                $survey_id = $surveyIDs[0]['SurveyNr'];
            }

            if (empty($data['dataform'])) {
                $data['dataform'] = $this->mfarmer->readFinanceCetak($farmer_id[$i], $survey_id);
            }

            $data['data'] = $this->mfarmer->readFarmer($farmer_id[$i]);
            $data['detail'] = $this->mfarmer->readFarmerAff($farmer_id[$i], $survey_id);
            $data['logos'] = $this->mfarmer->readPartnerLogo($farmer_id[$i], $value3);
            $data['survey'] = $this->mfarmer->readSurvey($survey_id);

            $this->load->view('cetak_basic_aff', $data);
        }
    }

    public function card_get($id = '', $Tipe=0, $save=0, $debug = false) {
        $detail = $this->mfarmer->getFarmerDetail($id);
        if ($debug) {
            echo '<pre>';
            print_r($this->db->last_query());
            echo '</pre>';
            exit;
        }
        //echo "tes";
        //echo "<pre>".print_r($detail,1)."</pre>";
        //echo $detail['PartnerPhoto'];exit;
        if ($detail['PartnerID'] == '8,17') {
            if($Tipe=='1'){
                $card = new Imagick('images/card/farmer-id-card-with-photo-cargill-mondelez-front-3.jpg');
            }else{
                $card = new Imagick('images/card/farmer-id-card-no-photo-cargill-mondelez-front-3.jpg');
            }
        } else {
            if($Tipe=='1'){
                $card = new Imagick('images/card/farmer-id-card-with-photo-front-3.jpg');
            }else{
                $card = new Imagick('images/card/farmer-id-card-no-photo-front-3.jpg');
            }
        }

        if (!empty($detail)) {
            // Foto
            if($Tipe=='1'){
                if (is_file($detail['FarmerPhoto'])) {
                    $foto_handle = fopen($detail['FarmerPhoto'], 'rb');
                }
                if (empty($foto_handle)) {
                    $foto_handle = fopen('images/user.png', 'rb');
                }
                if (!empty($foto_handle)) {
                    $foto = new Imagick();
                    $foto->readImageFile($foto_handle);

                    $foto->resizeImage(300, 400, imagick::FILTER_LANCZOS, 1);
                    $card->compositeImage($foto, imagick::COMPOSITE_OVER, 40, 220);
                }
            }

            // QR Code
            require_once APPPATH . 'third_party/phpqrcode/qrlib.php';
            $file_code = '/tmp/' . $detail['FarmerID'] . '.png';
            QRcode::png($detail['FarmerID'], $file_code);
            $code = new Imagick($file_code);
            $code->resizeImage(260, 260, imagick::FILTER_POINT, 1);

            $card->compositeImage($code, imagick::COMPOSITE_OVER, 700, 360);

            // Partner Logo
            if ($detail['PartnerID'] != '8,17') {
                if(intval($detail['TotalPartner']) == 1){
                    $ProgramPhoto = $detail['ProgramPhoto'];
                    $PartnerPhoto = $detail['PartnerPhoto'];
                }else{
                    $photo = explode(',',$detail['ProgramPhoto']);
                    $ProgramPhoto = "";
                    for($i=0;$i<count($photo);$i++){
                        if (is_file($photo[$i])) {
                            $ProgramPhoto = $photo[$i];
                            break;
                        }
                    }

                    $partner = explode(',',$detail['PartnerPhoto']);
                    $PartnerPhoto = "";
                    for($i=0;$i<count($partner);$i++){
                        if (is_file($partner[$i])) {
                            $PartnerPhoto = $partner[$i];
                            break;
                        }
                    }
                }
                if (is_file($ProgramPhoto)) {
                    $logo_handle = fopen($ProgramPhoto, 'rb');
                } else {
                    if (is_file($PartnerPhoto)) {
                        $logo_handle = fopen($PartnerPhoto, 'rb');
                    } else {
                        $logo_handle = fopen("images/swisscontact.png", 'rb');
                    }
                }
                if (empty($logo_handle)) {
                    // $logo_handle = fopen('images/logo.jpg', 'rb');
                }
                if (!empty($logo_handle)) {
                    $logo = new Imagick();
                    $logo->readImageFile($logo_handle);

                    $logo->resizeImage(220, 0, imagick::FILTER_LANCZOS, 1);
                    if ($logo->getImageHeight() > 100) {
                        $logo->resizeImage(220, 100, imagick::FILTER_LANCZOS, 1);
                    }
                    $card->compositeImage($logo, imagick::COMPOSITE_OVER, 720, 240);
                }
            }

            define("LEFT", 1);
            define("CENTER", 2);
            define("RIGHT", 3);

            $title = new ImagickDraw();
            $title->setFillColor('#43AC36');
            $title->setFont('Bookman-Light');
            $title->setFontSize(40);
            //$title->setTextAlignment(RIGHT);

            $sub_brown = new ImagickDraw();
            $sub_brown->setFillColor('#885526');
            //$sub_brown->setFontFamily("AvantGarde");
            $sub_brown->setFont('images/card/Font/Calibri-Bold.ttf');
            $sub_brown->setFontSize(45);
            //$sub_brown->setTextAlignment(RIGHT);

            $sub_green = new ImagickDraw();
            $sub_green->setFillColor('#43AC36');
            $sub_green->setFont('images/card/Font/Calibri.ttf');
            $sub_green->setFontSize(35);
            //$sub_green->setTextAlignment(RIGHT);

            // /* Create text */

            $x2 = 470;
            $y2 = 420;
            $sub_green->annotation($x2, $y2, 'Id Petani');
            $y2 += 40;
            $sub_brown->annotation($x2, $y2, $detail['FarmerID']);
            $y2 += 40;

            $x1 = 75;
            $y1 = 420;
            $sub_green->annotation($x1, $y1, 'Nama');
            $y1 += 40;

            $name = wordwrap($detail['FarmerName'], 14, "\n");
            $names = explode("\n", $name);
            $i= 0;
            foreach ($names as $key => $name) {
                if($i<3){
                    $sub_brown->annotation($x1, $y1, $name);
                    $y1 += 40;
                    $i++;
                }
            }
            if($i<2){
                $y1 += 40;
            }
            $sub_green->annotation($x1, $y1, 'Kelompok Tani');
            $sub_green->annotation($x2, $y1, 'Dilatih Sejak');
            $y1 += 40;
            $sub_brown->annotation($x2, $y1, $detail['tahun']);
            $group = wordwrap($detail['GroupName'], 25, "\n");
            $groups = explode("\n", $group);
            foreach ($groups as $key => $group) {
                $sub_brown->annotation($x1, $y1, $group);
                $y1 += 30;
            }
            /*
            $sub_green->annotation($x, $y, 'ID');
            $y += 30;
            $sub_brown->annotation($x, $y, $detail['FarmerID']);
            $y += 30;
            $sub_green->annotation($x, $y, 'Kelompok Tani');
            $y += 30;
            $group = wordwrap($detail['GroupName'], 25, "\n");
            $groups = explode("\n", $group);
            foreach ($groups as $key => $group) {
                $sub_brown->annotation($x, $y, $group);
                $y += 30;
            }*/
            // $sub_brown->annotation($x, $y, $detail['GroupName']);

            $card->drawImage($title);
            $card->drawImage($sub_green);
            $card->drawImage($sub_brown);
        }
        if($save!=0){
            $card->writeImage('files/id_card_temp/id_card_'.$id.'.jpg');
        }else{
            header('Content-type: image/jpeg');
            echo $card;
        }
    }

    public function certificates_get($CpgBatchTrainingID = null) {
        if (empty($CpgBatchTrainingID)) {
            $CpgBatchTrainingID = $this->get('CpgBatchTrainingID');
        }
        $files = $this->mfarmer->generateCertificateFiles($CpgBatchTrainingID);

        $this->load->library('zip');
        $path = "images/certificate/{$CpgBatchTrainingID}/";
        $this->zip->read_dir($path, false);

        $this->zip->download('certificates.zip');
    }

    public function certificate_get($CpgBatchTrainingsFarmerID = '', $debug = false) {
        $file = $this->mfarmer->generateCertificateFile($CpgBatchTrainingsFarmerID);
        $file = file_get_contents($file, 'r');

        header('Content-type: image/jpeg');
        echo $file;
    }

    public function cert_holders_get() {
        $data = $this->mfarmer->readCertHolders($this->get('prov'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any holder'), 404);
        }
    }

    public function cert_staffs_get() {
        $data = $this->mfarmer->readCertStaffs($this->get('prov'), $this->get('certHolder'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function sertifikasi_post() {
        $snr = explode(" - ", $this->post('SurveyNr'));
        $data = $this->mfarmer->addSertifikasiLog(
                $this->post('ICSDate'), $this->post('ICSDateOld'), $this->post('Certification'), $this->post('StatusAudit'), $this->post('DateRevisionAudit'), $this->post('CommentAudit'), $this->post('RecommendationAudit'), $this->post('InpectorID'), $this->post('AuditCommiteeID'), $this->post('IMSManagerID'), $this->post('FarmerID'), $this->post('GardenNr'), $snr[0], $_SESSION['userid'], $this->post('CertificationFormEdit'), $this->post('FarmerSignature'), $this->post('InspectorSignature'), $this->post('AuditCommiteeSignature'), $this->post('IMSManagerSignature')
        );
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'updating data failed'), 404);
        }
    }

    public function lastAuditLog_get() {
        $data = $this->mfarmer->readLastAuditLog($this->get('farmer_id'), $this->get('GardenNr'), $this->get('gSurveyNr'), $this->get('certification'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }

    public function farmerl_audit_log_get() {
        $data = $this->mfarmer->readAuditLogs(
                $this->get('farmer_id'), $this->get('GardenNr'), $this->get('gSurveyNr'), $this->get('certification')
        );
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }

    public function load_activity_get() {
        $data = $this->mfarmer->readAuditLog($this->get('gFarmerID'), $this->get('GardenNr'), $this->get('gSurveyNr'), $this->get('Certification'), $this->get('ICSDate'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }

    public function delete_activity_get() {
        $data = $this->mfarmer->deleteAuditLog($this->get('gFarmerID'), $this->get('GardenNr'), $this->get('gSurveyNr'), $this->get('Certification'), $this->get('ICSDate'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Data could not be delete'), 404);
        }
    }

    public function certholder_get() {
        $data = $this->mfarmer->readCertHolders2($this->get('prov'), $this->get('jenis'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any holder'), 404);
        }
    }

    // get family relationship
    public function famrelation_get() {
        $data = $this->mfarmer->readFamRelation($this->get('id'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldt\t find relation'), 404);
        }
    }

    public function farmerl_other_lands_get() {
        $data = array();
        $data = $this->mfarmer->getFarmerOtherLand($this->get('id'));
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $data[$key]['GardenHa'] = floatval($value['GardenHa']);
            }
        }
        $this->response($data, 200);
    }

    public function farmerl_garden_status_get() {
        $data = array();
        $data = $this->mfarmer->getFarmerGardenStatus($this->get('id'));
        $this->response($data, 200);
    }

    public function databank_get() {
        $data = $this->mfarmer->readDataBank($this->get('id'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }

    public function other_land_post() {
        $message = '';
        $result = $this->mfarmer->createOtherLand($this->post('FarmerID'), $this->post('Commodity'), $this->post('GardenHa'), $this->post('Remark'));
        // echo "<pre>"; print_r($this->db->last_query()); echo "</pre"; exit;
        $this->response(array(
            'success' => $result,
            'message' => $message,
                ), 200);
    }

    public function other_land_delete() {
        $message = '';
        // $this->db->trans_start(FALSE);
        $result = $this->mfarmer->deleteOtherLand($this->delete('FarmerID'), $this->delete('Commodity'));
        // $this->db->trans_rollback();
        // echo "<pre>"; print_r($this->db->last_query()); echo "</pre"; exit;
        $this->response(array(
            'success' => $result,
            'message' => $message,
                ), 200);
    }

    public function garden_status_post() {
        $message = '';
        $result = $this->mfarmer->createGardenStatus($this->post('FarmerID'), $this->post('GardenNr'), $this->post('ActiveStatus'), $this->post('GardenStatus'), $this->post('Commodity'), $this->post('Remarks'), $this->post('CommodityHa'));
        $this->response(array(
            'success' => $result,
            'message' => $message,
                ), 200);
    }

    public function cetak_farmer_summary_loan_get($id, $FarmerID, $param3 = '', $PartnerID = '') {
        if($this->get('bahasa') != ""){
            $this->load->language('general', $this->get('bahasa'));
        }else{
            if($_SESSION['language'] == "Indonesia")
                $this->load->language('general', 'indonesia');
            else
                $this->load->language('general', 'english');
        }

        $FarmerIDCekTitle = explode('::', $FarmerID);
        if (count($FarmerIDCekTitle) > 1) {
            $dataHeader['titleNya'] = "Farmer Summary";
        } else {
            $dataFarmer = $this->mfarmer->readFarmer($FarmerID);
            $dataHeader['titleNya'] = "Farmer Summary $FarmerID – " . $dataFarmer['FarmerName'];
        }
        $this->load->view('cetak_farmer_summary_load_header_v2', $dataHeader);

        if ($id == 'FarmerID' and strpos($FarmerID, '::')) {
            $FarmerIDs = explode('::', $FarmerID);

            $countData = count($FarmerIDs);
            $increData = 1;
            foreach ($FarmerIDs as $key => $value) {
                $this->cetak_farmer_summary_loan($value, $this->get('PartnerID'), $countData, $increData);
                $increData++;
            }
        } else {
            $this->cetak_farmer_summary_loan($this->get('FarmerID'), $this->get('PartnerID'));
        }

        // $this->load->view('cetak_farmer_summary_loan_footer');
        //$this->load->view('cetak_farmer_summary_footer');
        $this->load->view('cetak_farmer_summary_loan_footer_v2');
    }

    private function cetak_farmer_summary_loan($FarmerID, $PartnerID = null, $countData = 1, $increData = 1) {
        $data['farmer'] = $this->mfarmer->readFarmer($FarmerID);
        $data['logos'] = $this->mfarmer->readPartnerLogo($FarmerID, $PartnerID);

        $surveyIDs = $this->mfarmer->getLatestSurveyId($FarmerID);
        $surveyID = $surveyIDs[0]['SurveyNr'];

        foreach ($surveyIDs as $key => $survey) {
            if ($survey['GardenNr'] != '1') {
                continue;
            }
            $data['garden'] = $this->mfarmer->readGardenForCetakLatest($FarmerID, $survey['GardenNr'], $survey['SurveyNr']);
        }
        $data['garden'] = $data['garden'][0];

        $data['gardens'] = $this->mfarmer->getFarmerGardens($FarmerID);
        $data['garden_size'] = $this->mfarmer->getGardenSize($FarmerID);

        $family = $this->mfarmer->readFarmerKeluargas($FarmerID, 0, 100);
        $data['family'] = $family['data'];

        $data['finance'] = $this->mfarmer->getFinanceLatest($FarmerID);
        $data['partner'] = $this->mfarmer->readPartnerLogo($FarmerID, $PartnerID);
        $data['otherland'] = $this->mfarmer->getFarmerOtherLand($FarmerID);
        // $data['ppi']     = $this->mfarmer->getPPIScore2012($FarmerID);
        //$data['training']   = $this->mfarmer->getTrainingFarmer($FarmerID);
        $data['trainings'] = $this->mfarmer->getFarmerTrainings($FarmerID);

        $data['bank'] = $this->mfarmer->getNearestBank($data['garden']['Latitude'], $data['garden']['Longitude']);

        if (empty($data['farmer']['Photo']) || !file_exists('images/Photo/' . $data['farmer']['Photo'])) {
            $data['farmer']['Photo'] = 'no-user.jpg';
        }

        $data['countData'] = $countData;
        $data['increData'] = $increData;

        //echo '<pre>'; print_r($data); exit;
        $this->load->view('cetak_farmer_summary_loan_v2', $data);
    }

    public function import_loan_get() {
        $file = 'loan.xlsx';

        $this->load->library('Excel', null, 'PHPExcel');

        $excel_data = $this->PHPExcel->import($file, false);
        // echo "<pre>"; print_r(intval($excel_data[1][5])); echo "</pre>"; exit;
        foreach ($excel_data as $key => $value) {
            if (intval($value[5]) > 0) {
                $this->pdf_farmer_summary_loan_get($value[5]);
            }
        }
    }

    public function get_file_farmer_summary($FarmerID) {
        $this->pdf_farmer_summary_loan_get($FarmerID);
    }

    /**
     * Kalau mau mengaktifkan fungsi ini uncomment "use" di line paling atas
     * @param  [type] $FarmerID  [description]
     * @param  [type] $PartnerID [description]
     * @return [type]            [description]
     */
    public function pdf_farmer_summary_loan($FarmerID, $PartnerID = null) {
        $path = 'pdf/' . $FarmerID . '.pdf';
        if (!file_exists($path)) {
            $options = array(
                'javascript-delay' => 1000,
            );
            // You can pass a filename, a HTML string or an URL to the constructor
            // $pdf = new Pdf('http://cocoatrace.dev/api/index.php/farmer/cetak_farmer_summary_loan/FarmerID/732201362/PartnerID/4');
            $url = base_url() . 'farmer/cetak_farmer_summary_loan/FarmerID/' . $FarmerID;
            $pdf = new Pdf($url);

            // $pdf->send($FarmerID.'.pdf');

            $pdf->saveAs($path);
            // if (!$pdf->saveAs($path.$FarmerID.'.pdf')) {
            //     echo $pdf->getError();
            // }
        }
        return $path;
    }

    public function pdf_farmer_summary_loan_get($FarmerID, $PartnerID = null) {
        $path = $this->pdf_farmer_summary_loan($FarmerID, $PartnerID);
        echo $path;
        exit;
    }

    public function generate_learning_contract_get() {
        $data = $this->mfarmer->readFarmersLearningContract();
        foreach ($data->result() as $row) {
            $this->pdf_learning_contract_get($row->FarmerID);
        }
        exit;
    }

    public function pdf_learning_contract_get($FarmerID) {
        $options = array(
            'javascript-delay' => 1000,
        );
        $url = base_url() . 'farmer/cetak_learning_contract_template/' . $FarmerID;
        $pdf = new Pdf($url);
        $path = 'files/learning_contract/';
        $name = date('Ymdhis') . '_' . $FarmerID . '_LearningContractFile.pdf';
        if ($pdf->saveAs($path . $name)) {
            $this->mfarmer->updateLearningContract($FarmerID, $name);
        } else {
            echo "gagal<br>";
        }
    }

    public function rotate_photo_post() {
        /* $rotateImage = $this->mfarmer->rotateImage($this->get('id'), $data['Photo']);
          if($rotateImage['status']==1){
          $data['Photo'] = $rotateImage['Photo'];
          } */
        $message = '';
        $result = $this->mfarmer->rotateImage($this->post('FarmerID'), $this->post('degree'));
        $this->response(array(
            'success' => $result['status'],
            'message' => $message,
            'rotatedPhoto' => @$result['Photo'],
                ), 200);
    }

    public function print_id_card_get($CpgID, $Tipe=0, $FarmerID, $download = '') {
        if (strpos($FarmerID, '::')) {
            $FarmerIDs = explode('::', $FarmerID);
            foreach ($FarmerIDs as $key => $value) {
                $farmer_id[] = $value;
            }
            $data['FarmerID'] = $farmer_id;
        } else {
            $data['FarmerID'] = array($FarmerID);
        }
        $data['Tipe'] = $Tipe;
        if ($download == '') {
            $this->load->view('cetak_id_card', $data);
        } else {
            $path = str_replace('/system/', '/', BASEPATH);
            foreach ($data['FarmerID'] as $k => $v) {
                $url = site_url() . '/farmer/card/' . $v . '/' . $Tipe . '/1';
                $name = 'id_card_' . $v . '.jpg';
                $this->card_get($v,$Tipe,1);
                $id_card[] = $name;
            }

            $this->load->library('zip');
            foreach ($id_card as $file) {
                $paths = $path . 'files/id_card_temp/' . $file;
                $this->zip->read_file($paths);
            }
            $files = glob($path . 'files/id_card_temp/*'); // get all file names
            foreach ($files as $file) {
                // iterate files
                if (is_file($file)) {
                    unlink($file);
                }
                // delete file
            }
            $this->zip->download('idcard_' . $CpgID . '_' . date(YmdHis) . '.zip');
        }
    }

    public function print_id_foto_get($CpgID, $FarmerID, $download = '') {
        if (strpos($FarmerID, '::')) {
            $FarmerIDs = explode('::', $FarmerID);
            foreach ($FarmerIDs as $key => $value) {
                $farmer_id[] = $value;
            }
            $data['FarmerID'] = $farmer_id;
        } else {
            $data['FarmerID'] = array($FarmerID);
        }

        $path = str_replace('/system/', '/', BASEPATH);
        //ambil foto farmer dan copy
        foreach ($data['FarmerID'] as $k => $v) {
            $fotoFarmer = $this->mfarmer->getPhotoFarmerById($v);
            if ($fotoFarmer != "") {
                $arrFarmerFotoPathSource[] = $path . 'images/Photo/' . $fotoFarmer;

                $temp = explode("/", $path . 'images/Photo/' . $fotoFarmer);
                $namaFileFoto = end($temp);
                $arrFarmerFotoPathCopy[] = $path . 'files/id_photo_temp/' . $namaFileFoto;

                //copy ke folder temp
                @copy($path . 'images/Photo/' . $fotoFarmer, $path . 'files/id_photo_temp/' . $namaFileFoto);
            }
        }

        $this->load->library('zip');
        foreach ($arrFarmerFotoPathCopy as $key => $value) {
            $this->zip->read_file($value);
        }

        $files = glob($path . 'files/id_photo_temp/*'); // get all file names
        foreach ($files as $file) {
            // iterate files
            if (is_file($file)) {
                unlink($file);
            }
            // delete file
        }
        $this->zip->download('foto_' . $CpgID . '_' . date(YmdHis) . '.zip');
    }

    public function cetak_basic_saving_pilot_get($param1 = '', $value1 = '', $param2 = '', $value2 = '', $param3 = '', $value3 = '') {
        $this->load->language('general', $this->get('lang'));
        if ($param1 == 'CpgBatchTrainingID') {
            $this->load->model('cpg/mcpg');
            $farmer = $this->mcpg->readParticipants($value1);
            for ($i = 0; $i < sizeof($farmer['data']); $i++) {
                $farmer_id[] = $farmer['data'][$i]['pFarmerID'];
            }
            $survey_id = $value2;
            $value3 = $farmer['data'][0]['PartnerID'];
        } elseif ($param1 == 'CpgKaderTrainingID') {
            $this->load->model('training/mkader');
            $farmer = $this->mkader->readParticipants($value1);
            for ($i = 0; $i < sizeof($farmer['data']); $i++) {
                $farmer_id[] = $farmer['data'][$i]['farmer_id'];
            }
            $survey_id = $value2;
            $value3 = $farmer['data'][0]['PartnerID'];
        } else {
            $data['tes'] = $param1;
            $data['dataform'] = $this->mfarmer->readSavingPilotCetak($this->get('FarmerID'), $this->get('SurveyID'));
            $farmer_id[] = $this->get('FarmerID');
            $survey_id = $this->get('SurveyID');
        }
        for ($i = 0; $i < sizeof($farmer_id); $i++) {
            if (empty($data['dataform'])) {
                $data['dataform'] = $this->mfarmer->readSavingPilotCetak($farmer_id[$i], $survey_id);
            }

            $data['data'] = $this->mfarmer->readFarmer($farmer_id[$i]);
            $data['detail'] = $this->mfarmer->readFarmerSavingPilot($farmer_id[$i], $survey_id);
            $data['logos'] = $this->mfarmer->readPartnerLogo($farmer_id[$i], $value3);
            $data['survey'] = $this->mfarmer->readSurvey($survey_id);

            $this->load->view('cetak_basic_saving_pilot', $data);
        }
    }

    public function cetak_result_saving_pilot_get($param1 = '', $value1 = '', $param2 = '', $value2 = '', $param3 = '', $value3 = '') {
        if ($param1 == 'CpgBatchTrainingID') {
            $this->load->model('cpg/mcpg');
            $farmer = $this->mcpg->readParticipants($value1);
            for ($i = 0; $i < sizeof($farmer['data']); $i++) {
                $farmer_id[] = $farmer['data'][$i]['pFarmerID'];
            }
            $survey_id = $value2;
            $value3 = $farmer['data'][0]['PartnerID'];
        } elseif ($param1 == 'CpgKaderTrainingID') {
            $this->load->model('training/mkader');
            $farmer = $this->mkader->readParticipants($value1);
            for ($i = 0; $i < sizeof($farmer['data']); $i++) {
                $farmer_id[] = $farmer['data'][$i]['farmer_id'];
            }
            $survey_id = $value2;
            $value3 = $farmer['data'][0]['PartnerID'];
        } else {
            $data['tes'] = $param1;
            $data['dataform'] = $this->mfarmer->readSavingPilotCetak($this->get('FarmerID'), $this->get('SurveyID'));
            $farmer_id[] = $this->get('FarmerID');
            $survey_id = $this->get('SurveyID');
        }
        for ($i = 0; $i < sizeof($farmer_id); $i++) {
            if (empty($data['dataform'])) {
                $data['dataform'] = $this->mfarmer->readSavingPilotCetak($farmer_id[$i], $survey_id);
            }

            $data['data'] = $this->mfarmer->readFarmer($farmer_id[$i]);
            $data['detail'] = $this->mfarmer->readFarmerSavingPilot($farmer_id[$i], $survey_id);
            $data['logos'] = $this->mfarmer->readPartnerLogo($farmer_id[$i], $value3);
            $data['survey'] = $this->mfarmer->readSurvey($survey_id);

            $this->load->view('cetak_result_saving_pilot', $data);
        }
    }

    public function cetak_saving_pilot_get($param1 = '', $value1 = '', $param2 = '', $value2 = '', $param3 = '', $value3 = '') {
        $this->load->language('general', $this->get('lang'));
        $farmer_id = explode('::', $value1);
        $survey_id = $value2;
        for ($i = 0; $i < sizeof($farmer_id); $i++) {

            if ($this->get('SurveyID') == "latest") {
                $surveyIDs = $this->mfarmer->getLatestSurveyId($farmer_id[$i]);
                $survey_id = $surveyIDs[0]['SurveyNr'];
            }

            if (empty($data['dataform'])) {
                $data['dataform'] = $this->mfarmer->readSavingPilotCetak($farmer_id[$i], $survey_id);
            }

            $data['data'] = $this->mfarmer->readFarmer($farmer_id[$i]);
            $data['detail'] = $this->mfarmer->readFarmerSavingPilot($farmer_id[$i], $survey_id);
            $data['logos'] = $this->mfarmer->readPartnerLogo($farmer_id[$i], $value3);
            $data['survey'] = $this->mfarmer->readSurvey($survey_id);
            if ($value3 == "Form%20Kosong") {
                $this->load->view('cetak_basic_saving_pilot', $data);
            } else {
                $this->load->view('cetak_result_saving_pilot', $data);
            }
        }
    }

    public function farmer_list_get() {
        $province = $this->get('ProvinceID');
        $district = $this->get('DistrictID');
        $subdistrict = $this->get('SubDistrictID');
        $data = $this->mfarmer->listFarmer($province, $district, $subdistrict);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any farmer!'), 404);
        }
    }

    public function garden_clonal_get() {
        $data = $this->mfarmer->readFarmerCLonalGardens($this->get('ObjType'), $this->get('ObjID'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any Transactions!'), 404);
    }

    public function clonal_from_farmer_garden_get() { //ambil data clonal dari ktv_farmer_garden
        $data = $this->mfarmer->readFarmerGardenClonal($this->get('ObjID'), $this->get('GardenNr'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    public function gardens_active_get() {
        $data = $this->mfarmer->readGardens($this->get('farmer_id'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    /* =========================== Adoption Observation (begin) ========================================================= */

    public function farmer_adopt_obs_list_get() {
        $data = $this->mfarmer->getFarmerAdoptObsList($this->get('FarmerID'));
        $this->response($data, 200);
    }

    public function farmer_adopt_obs_combo_garden_get() {
        $data = $this->mfarmer->getFarmerAdoptObsComboGarden($this->get('FarmerID'));
        $this->response($data, 200);
    }

    public function farmer_adopt_obs_combo_survey_year_get() {
        $yearNya = date('Y');
        $data = array();
        $incre = 0;

        for ($i = $yearNya; $i >= ($yearNya - 10); $i--) {
            $data[$incre]['id'] = $i;
            $data[$incre]['label'] = $i;
            $incre++;
        }

        $this->response($data, 200);
    }

    /* =========================== Adoption Observation (end) ========================================================= */

    /* ======================================== Environment (begin) ========================================================= */

    public function farmer_envi_survey_get() {
        $data = $this->mfarmer->getFarmerEnviSurvey($this->get('FarmerID'));
        $this->response($data, 200);
    }

    public function farmer_envi_ref_survey_get() {
        $data = $this->mfarmer->getFarmerEnviRefSurvey();
        $this->response($data, 200);
    }

    public function form_adopt_obs_get() {
        $data = $this->mfarmer->getFormAdoptObs($this->get('FarmerID'), $this->get('GardenNr'), $this->get('SurveyYear'));
        $this->response($data, 200);
    }

    public function adopt_obs_post() {
        if ($this->post('methodPost') == 'insert') {
            //cek apakah data sudah ada
            $proses = $this->mfarmer->adoptObsCekExist($this->post());
            if ($proses['success'] == false) {
                $this->response($proses, 200);
                exit;
            }
            $proses = $this->mfarmer->insertAdoptObs($this->post());
        } elseif ($this->post('methodPost') == 'update') {
            $proses = $this->mfarmer->updateAdoptObs($this->post());
        }

        $this->response($proses, 200);
    }

    public function adopt_obs_delete() {
        $proses = $this->mfarmer->deleteAdoptObs($this->delete('FarmerID'), $this->delete('GardenNr'), $this->delete('SurveyYear'));
        $this->response($proses, 200);
    }

    public function farmer_environment_post() {
        /*
          $results['success'] = false;
          $results['message'] = "Failed to save data";
          $this->response($results, 200);
          echo '<pre>'; print_r($this->post()); exit;
         */

        //cek apakah insert or update
        $method = $this->mfarmer->getFarmerEnviMethod($this->post('enviFarmerID'), $this->post('enviSurveyNr'));
        if ($method == 'insert') {
            $proses = $this->mfarmer->insertFarmerEnvironment($this->post());
        } elseif ($method == 'update') {
            $proses = $this->mfarmer->updateFarmerEnvironment($this->post());
        }
        $this->response($proses, 200);
    }

    public function farmer_environment_delete() {
        $proses = $this->mfarmer->deleteFarmerEnvironment($this->delete('FarmerID'), $this->delete('SurveyNr'));
        $this->response($proses, 200);
    }

    public function farmer_envi_form_edit_get() {
        $data = $this->mfarmer->farmerEnviFormEditGet($this->get('FarmerID'), $this->get('SurveyNr'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any data'), 404);
    }

    /* ======================================== Environment (end) ========================================================= */

    /* ======================================== Nutrition Baru (begin) ========================================================= */

    public function farmer_sinutri_survey_get() {
        $data = $this->mfarmer->getFarmerSinutriSurvey($this->get('FarmerID'));
        $this->response($data, 200);
    }

    public function farmer_nutrition_sinutri_post() {
        //echo '<pre>'; print_r($this->post()); exit;
        //cek apakah insert or update
        $method = $this->mfarmer->getFarmerSinutriMethod($this->post('sinutriFarmerID'), $this->post('sinutriSurveyNr'));
        if ($method == 'insert') {
            $proses = $this->mfarmer->insertFarmerSinutri($this->post());
        } elseif ($method == 'update') {
            $proses = $this->mfarmer->updateFarmerSinutri($this->post());
        }
        $this->response($proses, 200);
    }

    public function farmer_sinutri_form_edit_get() {
        $data = $this->mfarmer->farmerSinutriFormEditGet($this->get('FarmerID'), $this->get('SurveyNr'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any data'), 404);
    }

    public function farmer_sinutri_delete() {
        $proses = $this->mfarmer->deleteFarmerSinutri($this->delete('FarmerID'), $this->delete('SurveyNr'));
        $this->response($proses, 200);
    }

    /* ======================================== Nutrition Baru (end) ========================================================= */

    /* ======================================== Finance (begin) ========================================================= */

    public function farmer_sinaff_cek_survey_get() {
        $proses = $this->mfarmer->farmerSinaffCekSurvey($this->get('FarmerID'));
        $this->response($proses, 200);
    }

    public function farmer_sinaff_ref_survey_get() {
        $proses = $this->mfarmer->farmerSinaffRefSurvey();
        $this->response($proses, 200);
    }

    public function farmer_ref_bank_get() {
        $proses = $this->mfarmer->farmerRefBankGet();
        $this->response($proses, 200);
    }

    public function farmer_sinaff_post() {
        //echo '<pre>'; print_r($this->post()); exit;
        //cek apakah insert or update
        $method = $this->mfarmer->getFarmerSinaffMethod($this->post('sinaffFarmerID'), $this->post('sinaffSurveyNr'));
        if ($method == 'insert') {
            $proses = $this->mfarmer->insertFarmerSinaff($this->post());
        } elseif ($method == 'update') {
            $proses = $this->mfarmer->updateFarmerSinaff($this->post());
        }
        $this->response($proses, 200);
    }

    public function farmer_sinaff_form_edit_get() {
        $data = $this->mfarmer->farmerSinaffFormEditGet($this->get('FarmerID'), $this->get('SurveyNr'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any data'), 404);
    }

    /* ======================================== Finance (end)   ========================================================= */

    public function mw_farmer_photo_get() {
        $farmer_id = $this->get('farmerid');
        $photo = $this->mfarmer->mwFarmerPhoto($farmer_id);
        $path = '';
        if ($photo['Photo']) {
            $file = explode('/', $photo['Photo']);
            $whitelist_ext = array('jpg', 'png', 'jpeg');
            if (in_array(pathinfo($file[0], PATHINFO_EXTENSION), $whitelist_ext)) {
                $path = base_url() . 'images/Photo/' . urlencode($photo['Province']) . '/' . $file[0];
            } else {
                $path = base_url() . 'images/Photo/' . urlencode($file[0]) . '/' . $file[1];
            }
//            if (file_exists('images/Photo/' . $photo['Photo'])) {
//                $path = 'images/Photo/' . $photo['Photo'];
//                $type = pathinfo($path, PATHINFO_EXTENSION);
//                $data = file_get_contents($path);
//                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
//            }
        }
        if ($path) {
            $this->response(array($path), 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any data'), 404);
        }
    }

    /*
     * generate KML
     */
    private function getKML($farmer_id, $survey_nr = null, $garden_nr = null)
    {
        $farmer = $this->mfarmer->getLatestSurvey($farmer_id, $survey_nr, $garden_nr);
        if ($farmer) {
            $kml = array('<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://www.opengis.net/kml/2.2">
<Document id="root_doc">
<Schema name="Polygon" id="Polygon_FarmerID">
    <SimpleField name="FARMERID" type="float"></SimpleField>
    <SimpleField name="FARMERNAME" type="string"></SimpleField>
    <SimpleField name="GARDENNR" type="int"></SimpleField>
    <SimpleField name="SURVEYNR" type="float"></SimpleField>
    <SimpleField name="X" type="float"></SimpleField>
    <SimpleField name="Y" type="float"></SimpleField>
    <SimpleField name="TGLMASUK" type="string"></SimpleField>
    <SimpleField name="AREA_HA" type="float"></SimpleField>
    <SimpleField name="KETERANGAN" type="string"></SimpleField>
</Schema>
<Folder><name>Polygon</name>');

            foreach ($farmer as $k => $v) {
                $kml[] = '
    <Placemark>
        <Style><LineStyle><color>ff0000ff</color><width>3</width></LineStyle><PolyStyle><fill>0</fill></PolyStyle></Style>
            <ExtendedData><SchemaData schemaUrl="#Polygon_FarmerID">
                <SimpleData name="FARMERID">' . $v['FarmerID'] . '</SimpleData>
                <SimpleData name="FARMERNAME">' . $v['FarmerName'] . '</SimpleData>
                <SimpleData name="GARDENNR">' . $v['GardenNr'] . '</SimpleData>
                <SimpleData name="SURVEYNR">' . $v['SurveyNr'] . '</SimpleData>
                <SimpleData name="X">' . $v['Longitude'] . '</SimpleData>
                <SimpleData name="Y">' . $v['Latitude'] . '</SimpleData>
                <SimpleData name="AREA_HA">' . $v['GardenHaUnCertified'] . '</SimpleData>
            </SchemaData></ExtendedData>
        <Polygon><altitudeMode>relativeToGround</altitudeMode><outerBoundaryIs><LinearRing><altitudeMode>relativeToGround</altitudeMode><coordinates>';

                // Iterates through the rows, printing a node for each row.
                $coordinates = $this->mfarmer->getCoordinates($v['FarmerID'], $v['GardenNr'], $v['SurveyNr']);
                if ($coordinates) {
                    foreach ($coordinates as $key => $val) {
                        $kml[] = $val['Longitude'] . ',' . $val['Latitude'];
                    }
                }
                // End XML file
                $kml[] = '</coordinates></LinearRing></outerBoundaryIs></Polygon>
    </Placemark>';
            }

            $kml[] = '
</Folder>
</Document></kml>';
            $kmlOutput = join(" ", $kml);
            return $kmlOutput;
        }
        return false;
    }

    public function print_kml_get() {
        $farmer_id = $this->get('farmerid');
        $survey_nr = $this->get('surveynr');
        $garden_nr = $this->get('gardennr');

        $kmlOutput = $this->getKML($farmer_id, $survey_nr, $garden_nr);
        if ($kmlOutput !== false) {
            // header('Content-type: application/vnd.google-earth.kml+xml');

            // print_r($kmlOutput);
            // exit;
            $this->load->helper('download');
            $name = "{$farmer_id}";
            if ($garden_nr !== false) {
                $name .= '-'.$garden_nr;
            }
            if ($survey_nr !== false) {
                $name .= '-'.$survey_nr;
            }
            $name .= '.kml';

            force_download($name, $kmlOutput);
        }
    }

    public function bulk_kml_get()
    {
        $farmer_ids = explode('::', $this->get('FarmerID'));
        // echo '<pre>'; print_r($farmer_ids); echo '</pre>'; exit;
        if (!empty($farmer_ids)) {
            $this->load->library('zip');
            foreach ($farmer_ids as $farmer_id) {
                $kmlOutput = $this->getKML($farmer_id);
                if ($kmlOutput !== false) {
                    $name = "{$farmer_id}.kml";
                    $data = $kmlOutput;
                    $this->zip->add_data($name, $data);
                }
            }
            $timestamp = date('YmdHis');
            $this->zip->download("kml-garden-{$timestamp}.zip");
        }
        $this->response('KML Not Found', 200);
    }

    public function polygon_get()
    {
        $farmer = $this->get('farmer');
        $garden = $this->get('garden');
        $survey = $this->get('survey');

        $result = $this->mfarmer->readPolygon($farmer, $garden, $survey);
        $area = array();
        $survey = array();
        $garden = array();
        if (!empty($result)) {
            foreach ($result as $val) {
                $area[] = array(floatval($val['Latitude']),floatval($val['Longitude']));
            }
            $survey['SurveyNr'] = $result[0]['SurveyNr'];
            $survey['SurveyTxt'] = $result[0]['SurveyTxt'];
            $garden['Latitude'] = $result[0]['garden_latitude'];
            $garden['Longitude'] = $result[0]['garden_longitude'];
        }
        if($area) $this->response(compact('survey', 'garden', 'area'), 200);
        else $this->response(array('error' => 'Couldn\'t find any data!'), 404);

    }

    public function polygon_post()
    {
        $FarmerID = $this->post('FarmerID');
        $GardenNr = $this->post('GardenNr');
        $SurveyNr = $this->post('SurveyNr');
        if(empty($FarmerID) || empty($GardenNr) || !isset($SurveyNr)) {
            $this->response(NULL, 400);
        }
        $data = $this->mfarmer->updateGardenPolygon($this->post('area'),$this->post('FarmerID'),$this->post('GardenNr'),$this->post('SurveyNr'));
        // $data = $this->mmaps->updateGarden($this->post('area_hectare'),$this->post('FarmerID'),$this->post('GardenNr'),$this->post('SurveyNr'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Failed to update data'), 404);
    }

    public function survey_get()
    {
        $SurveyNr = $this->get('SurveyNr');
        $survey = $this->mfarmer->getSurveyDetail($SurveyNr);
        $this->response($survey, 200);
    }

    public function cetak_gap_new_kosong_get(){
        if($this->get('lang') != "" && $this->get('lang') != "null"){
            $this->load->language('general', $this->get('lang'));
            $data['bahasanya'] = $this->get('lang');
        }else{
            if($_SESSION['language'] == "Indonesia"){
                $this->load->language('general', 'indonesia');
                $data['bahasanya'] = 'indonesia';
            }else{
                $this->load->language('general', 'english');
                $data['bahasanya'] = 'english';
            }
        }
        $data = array();
        $this->load->model('nursery/mnursery');

        if($this->get('SurveyID') == "latest"){
            $surveyIDs = $this->mfarmer->getLatestSurveyId($this->get('FarmerID'));
            $SurveyNr = $surveyIDs[0]['SurveyNr'];
        }else{
            $SurveyNr = $this->get('SurveyID');
        }

        //data petani
        $data['farmer'] = $this->mfarmer->readFarmer($this->get('FarmerID'));
        $data['farmer']['DistrictID'] = substr($data['farmer']['FarmerID'],0,4);
        $data['family'] = $this->mfarmer->readFarmerKeluargas($this->get('FarmerID'), 0, 100);
        $data['gardenStatus'] = $this->mfarmer->getGardenStatusCetak($this->get('FarmerID'), $SurveyNr);
        $data['otherLand'] = $this->mfarmer->getOtherLandCetak($this->get('FarmerID'));

        //logo atas
        $data['logos'] = $this->mnursery->getPartnerLogoByDistrict($data['farmer']['DistrictID']);

        //echo '<pre>'; print_r($data); exit;
        $data['garden'][0] = array();
        $data['jenis_form'] = $this->get('jenis_form');
        //cek apakah gap certification
        if($this->get('isCert') == "1"){
            $this->load->view('cetak_gap_new', $data);
        }else{
            $this->load->view('cetak_gap_new_non_certification', $data);
        }

    }

    public function cetak_gap_new_result_get(){
        if($this->get('lang') != "" && $this->get('lang') != "null"){
            $this->load->language('general', $this->get('lang'));
            $data['bahasanya'] = $this->get('lang');
        }else{
            if($_SESSION['language'] == "Indonesia"){
                $this->load->language('general', 'indonesia');
                $data['bahasanya'] = 'indonesia';
            }else{
                $this->load->language('general', 'english');
                $data['bahasanya'] = 'english';
            }
        }
        $data = array();
        $this->load->model('nursery/mnursery');

        if($this->get('SurveyID') == "latest"){
            $surveyIDs = $this->mfarmer->getLatestSurveyId($this->get('FarmerID'));
            $SurveyNr = $surveyIDs[0]['SurveyNr'];
        }else{
            $SurveyNr = $this->get('SurveyID');
        }

        //data petani
        $data['farmer'] = $this->mfarmer->readFarmer($this->get('FarmerID'));
        $data['farmer']['DistrictID'] = substr($data['farmer']['FarmerID'],0,4);
        $data['family'] = $this->mfarmer->readFarmerKeluargas($this->get('FarmerID'), 0, 100);
        $data['gardenStatus'] = $this->mfarmer->getGardenStatusCetak($this->get('FarmerID'), $SurveyNr);
        $data['otherLand'] = $this->mfarmer->getOtherLandCetak($this->get('FarmerID'));

        //logo atas
        $data['logos'] = $this->mnursery->getPartnerLogoByDistrict($data['farmer']['DistrictID']);

        //echo '<pre>'; print_r($data); exit;
        $data['garden'] = $this->mfarmer->getDetailGarden($data['farmer']['FarmerID'], $SurveyNr);
        $data['SurveyNr'] = $SurveyNr;
        $data['jenis_form'] = $this->get('jenis_form');

        //cek apakah gap certification
        if($this->get('isCert') == "1"){
            $this->load->view('cetak_gap_new', $data);
        }else{
            $this->load->view('cetak_gap_new_non_certification', $data);
        }

    }

    public function cetak_ao_get(){
        if($this->get('lang') != ""){
            $this->load->language('general', $this->get('lang'));
            $data['bahasanya'] = $this->get('lang');
        }else{
            if($_SESSION['language'] == "Indonesia"){
                $this->load->language('general', 'indonesia');
                $data['bahasanya'] = 'indonesia';
            }else{
                $this->load->language('general', 'english');
                $data['bahasanya'] = 'english';
            }
        }
        $data = array();
        $data['FarmerID'] = $this->get('FarmerID');
        $data['SurveyYear'] = $this->get('SurveyID');
        $data['jenis_form'] = $this->get('jenis_form');
        $this->load->model('nursery/mnursery');

        //data petani
        $data['farmer'] = $this->mfarmer->readFarmer($this->get('FarmerID'));
        $data['survey'] = $this->mfarmer->getAdoptObsForCetak($data['FarmerID'],$data['SurveyYear'],$data['jenis_form']);

        //logo atas
        $data['logos'] = $this->mnursery->getPartnerLogoByDistrict($data['farmer']['DistrictID']);

        $this->load->view('cetak_ao', $data);
    }

    public function farmer_gsp_list_get(){
        $data = $this->mfarmer->getListGSP($this->get('FarmerID'));
        $this->response($data, 200);
    }

    public function form_gsp_get(){
        $data = $this->mfarmer->getFormGsp($this->get('FarmerID'), $this->get('SurveyNr'));
        $this->response($data, 200);
    }

    public function gsp_post(){
        /*
        $data['success'] = false;
        $data['message'] = 'Ahay';
        $this->response($data, 200);
        */

        //var proses (begin)
        foreach ($this->post() as $key => $value) {
            if($value == ""){
                $varPost[$key] = null;
            }else{
                $varPost[$key] = $value;
            }
        }
        $varPost['userid'] = $_SESSION['userid'];
        //var proses (end)

        if($varPost['methodPost'] == "insert"){
            $proses = $this->mfarmer->insertGsp($varPost);
        }else{
            $proses = $this->mfarmer->updateGsp($varPost);
        }
        $this->response($proses, 200);
    }

    public function gsp_delete(){
        $proses = $this->mfarmer->deleteGsp($this->delete('FarmerID'), $this->delete('SurveyNr'));
        $this->response($proses, 200);
    }

    public function cetak_p1_p2_get(){
        $MemberID = explode("::", $this->get('MemberID'));

        if($_SESSION['language'] == "Indonesia"){
            $this->load->language('general', 'indonesia');
            $data['bahasanya'] = 'indonesia';
        }else{
            $this->load->language('general', 'english');
            $data['bahasanya'] = 'english';
        }

        foreach ($MemberID as $key => $value) {
            # code...
            $data = array(
                'basicData'       => $this->mfarmer->getMemberP1P2($value),
                'family'          => $this->mfarmer->getFamilyP1P2($value),
                'labour'          => $this->mfarmer->getLabourP1P2($value),
                'otherLand'       => $this->mfarmer->getOtherLandP1P2($value),
                'memberExtension' => $this->mfarmer->getMemberExtensionP1P2($value),
                'surveyHousehold' => $this->mfarmer->getSurveyHouseholdP1P2($value)->row(),
                'surveyPlot'      => $this->mfarmer->getSurveyPlotP1P2($value)->result(),
                'key'             => $key
            );
            // print("<pre>".print_r($data['surveyPlot'],true)."</pre><br>");die;
            $this->load->view('cetak_p1_p2', $data);
        }
    }

    public function training_main_grid_get() {
        $MemberID = (int) $this->get('MemberID');
        $data = $this->mfarmer->GetTrainingMainGrid($MemberID);
        $this->response($data, 200);        
    }
    
    public function coaching_main_grid_get() {
        $MemberID = (int) $this->get('MemberID');
        $data = $this->mfarmer->GetCoachingMainGrid($MemberID);
        $this->response($data, 200);        
    }

}