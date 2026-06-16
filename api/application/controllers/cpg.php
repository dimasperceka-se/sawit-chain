<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cpg extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->file = $_FILES;
        $this->load->model('cpg/mcpg');
    }

    function RegionIDs_get() {
        $RegionIDs = $this->mcpg->readRegionIDs($this->get('query'), $this->get('start'), $this->get('limit'));
        if ($RegionIDs)
            $this->response($RegionIDs, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any RegionIDs!'), 404);
    }

    function cetak_get($id, $par = '', $val = '') {
        $data['data'] = $this->mcpg->readTraining($id);
        if ($par == 'DayNumber' && $val != '') {
            $DayNumber = $val;
        } else {
            $DayNumber = '';
        }
        $data['data']['DayNumber'] = $DayNumber;
        $part = $this->mcpg->readParticipants($id, $DayNumber);
        $data['attendance'] = array();
        foreach ($part['data'] as $key => $value) {
            $data['attendance'][$key] = $this->mcpg->getFarmerAttendance($id, $value['pFarmerID'], $DayNumber);
            //echo "<pre>".print_r($data['attendance'][$key],1)."</pre>";
            $farmerID = $value['pFarmerID'];
            if ($data['attendance'][$key][0]['TrainingDate'] !== "") {
                $data['data']['TrainingDate'] = $data['attendance'][$key][0]['TrainingDate'];
            }
        }
        $this->load->model('farmer/mfarmer');
        $value3 = $part['data'][0]['PartnerID'];
        $data['logos'] = $this->mfarmer->readPartnerLogo($farmerID, $value3);
        $data['peserta'] = $part['data'];
        $data['logo'] = $this->mcpg->readPartnerLogo($id);
        //echo "<pre>".print_r($data ,1)."</pre>";
        //echo '<pre>'; print_r($data); exit;
        $this->load->view('cpg_cetak_hadir', $data);
    }

    function batchs_get() {
        $data = $this->mcpg->readBatchs();
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any RegionIDs!'), 404);
    }

    function cpgcs_get() {

        //cek apakah simple search / advanced
        if ($this->get('opsiSearch') == "adv") {
            $paramSearch = array(
                "prov" => $this->get('prov'),
                "kab" => $this->get('kab'),
                "subdist" => $this->get('subdist'),
                "sort" => $this->get('sort'),
                "start" => (int) $this->get('start'),
                "limit" => (int) $this->get('limit'),
                "parAdvNamaId" => $this->get('parAdvNamaId'),
                "parAdvDistrict" => $this->get('parAdvDistrict'),
                "parAdvBatch" => $this->get('parAdvBatch'),
                "parAdvNursery" => $this->get('parAdvNursery')
            );
            $cpgs = $this->mcpg->readCpgAdvancedSearch($paramSearch);
        } else {
            $cpgs = $this->mcpg->readCpgs(
                    $this->get('prov'), $this->get('kab'), $this->get('key'), $_SESSION['userid'], $_SESSION['PartnerID'], $_SESSION['FlagAccess'], $this->get('sort'), $this->get('start'), $this->get('limit'), $this->get('subdist'));
        }

        if ($cpgs)
            $this->response($cpgs, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any cpgs!'), 404);
    }

    function cpgc_excel_post() {
        $paramSearch = array(
            "prov" => $this->post('prov'),
            "parAdvNamaId" => $this->post('parAdvNamaId'),
            "parAdvDistrict" => $this->post('parAdvDistrict'),
            "parAdvBatch" => $this->post('parAdvBatch'),
            "parAdvNursery" => $this->post('parAdvNursery'),
            "no_limit" => "yes"
        );
        $data = $this->mcpg->readCpgAdvancedSearch($paramSearch);
        $dataList = $data['data'];

        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';

        $mem_ini = ini_get('memory_limit');
        ini_set('memory_limit', '1048576M');

        //=============== MULAI TULIS EXCEL (BEGIN) ===================================================================//
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
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25); //name
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30); //group_name
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(19); //village
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(19); //sub district
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15); //date updated
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15); //last survey
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15); //last survey
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
        $objPHPExcel->getActiveSheet()->setCellValue('B2', 'List Farmer Group');
        $objPHPExcel->getActiveSheet()->getStyle('B2')->applyFromArray($styleFontBoldTitle);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B2:J2');

        $objPHPExcel->getActiveSheet()->setCellValue('B4', 'No');
        $objPHPExcel->getActiveSheet()->setCellValue('C4', lang('ID'));
        $objPHPExcel->getActiveSheet()->setCellValue('D4', lang('Name'));
        $objPHPExcel->getActiveSheet()->setCellValue('E4', lang('Region'));
        $objPHPExcel->getActiveSheet()->setCellValue('F4', lang('Tahun Terbentuk'));
        $objPHPExcel->getActiveSheet()->setCellValue('G4', lang('Anggota'));
        $objPHPExcel->getActiveSheet()->setCellValue('H4', lang('Partner Name'));
        $objPHPExcel->getActiveSheet()->setCellValue('I4', lang('Total Garden Size'));
        $objPHPExcel->getActiveSheet()->setCellValue('J4', lang('Total Garden'));
        $objPHPExcel->getActiveSheet()->getStyle('B4:J4')->applyFromArray($styleFontBoldHeader);
        $objPHPExcel->getActiveSheet()->getStyle('B4:J4')->applyFromArray($styleBorderFull, false);

        $rowStart = 5;
        for ($i = 0; $i < count($dataList); $i++) {
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $rowStart, $i + 1);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $rowStart, $dataList[$i]['id']);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $rowStart, $dataList[$i]['GroupName']);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $rowStart, $dataList[$i]['RegionName']);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $rowStart, $dataList[$i]['TahunTerbentuk']);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $rowStart, $dataList[$i]['Anggota']);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $rowStart, $dataList[$i]['PartnerName']);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $rowStart, $dataList[$i]['totalLandSize']);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $rowStart, $dataList[$i]['totalGarden']);
            $objPHPExcel->getActiveSheet()->getStyle('B' . $rowStart . ':J' . $rowStart)->applyFromArray($styleFont);
            $objPHPExcel->getActiveSheet()->getStyle('B' . $rowStart . ':J' . $rowStart)->applyFromArray($styleBorderFull, false);
            $rowStart++;
        }

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('list_farmer_group_cocoatrace.xls');
        ini_set('memory_limit', $mem_ini);
        $this->response(array('success' => TRUE, 'filenya' => base_url() . 'list_farmer_group_cocoatrace.xls'), 200);
        exit;
        //=============== MULAI TULIS EXCEL (END) ===================================================================//
    }

    function batch_training_combo_get() {
        $data = $this->mcpg->getBatchTrainingCombo();
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any RegionIDs!'), 404);
    }

    function trainings_get() {
        $cpgs = $this->mcpg->readTrainings($this->get('cpg_id'));
        if ($cpgs)
            $this->response($cpgs, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any cpgs!'), 404);
    }

    function family_trainings_get() {
        $data = $this->mcpg->readFamilyTrainings($this->get('id'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any cpgs!'), 404);
    }

    function familys_get() {
        $cpgs = $this->mcpg->readFamily($this->get('key'));
        if ($cpgs)
            $this->response($cpgs, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any cpgs!'), 404);
    }

    function participants_get() {
        $cpgs = $this->mcpg->readParticipants($this->get('key'), $this->get('start'), $this->get('limit'));
        if ($cpgs)
            $this->response($cpgs, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any cpgs!'), 404);
    }

    function participants_add_get() {
        $cpgs = $this->mcpg->readParticipantsAdd($this->get('CpgBatchTrainingID'), $this->get('cpgID'), $this->get('keyAddPart'));
        if ($cpgs)
            $this->response($cpgs, 200);
        else
            $this->response(array('error' => 'Couldn\'t find participants training!'), 404);
    }

    /*
      function participant_post() {
      if(!$this->post('CpgBatchTrainingID')) $this->response(NULL, 400);
      $cpg = $this->mcpg->createParticipant(
      $this->post('CpgBatchTrainingID'),
      $this->post('PersonNm'),
      $this->post('partisipan'),
      $this->post('AnggotaName'),
      $this->post('WritingAwal'),
      $this->post('WritingAkhir'),
      $this->post('BallotAwal'),
      $this->post('BallotAkhir'),
      $_SESSION['userid']
      );
      if($cpg) $this->response($cpg, 200);
      else $this->response(array('error' => 'Cpg could not be found'), 404);
      }
     *
     */

    function participant_post() {
        if (!$this->post('CpgBatchTrainingID'))
            $this->response(NULL, 400);
        $participants = explode(',', $this->post('participants'));
        array_shift($participants);
        // $CpgBatchTrainingID,$participants,$PetaniKakao,$userid
        $cpg = $this->mcpg->createParticipant(
                $this->post('CpgBatchTrainingID'), $participants, $this->post('PetaniKakao'), $_SESSION['userid']
        );
        if ($cpg['success'] == true) {
            $this->load->model('farmer/mfarmer');
            foreach ($participants as $FarmerID) {
                $this->mfarmer->updateFarmerIsTraining($FarmerID);
            }
        }
        if ($cpg)
            $this->response($cpg, 200);
        else
            $this->response(array('error' => 'Cpg could not be found'), 404);
    }

    function participant_put() {
        if (!$this->put('id'))
            $this->response(NULL, 400);
        $farmerid = is_numeric($this->put('PersonNm')) ? $this->put('PersonNm') : $this->put('pFarmerID');
        $petanikakao = is_numeric($this->put('partisipan')) ? $this->put('partisipan') : $this->put('PetaniKakao');
        $familiid = is_numeric($this->put('AnggotaName'))?$this->put('AnggotaName'):$this->put('FamilyID');
        $familiid = ($this->put('partisipan') === 1) ? '' : $this->put('AnggotaName');
        $cpg = $this->mcpg->updateParticipant($this->put('CpgBatchTrainingID'), $farmerid, $petanikakao, $familiid, $this->put('WritingAwal'), $this->put('WritingAkhir'), $this->put('BallotAwal'), $this->put('BallotAkhir'), $this->put('id'), $_SESSION['userid']);
        if ($cpg)
            $this->response($cpg, 200);
        else
            $this->response(array('error' => 'Cpg could not be found'), 404);
    }

    function farmers_get() {
        $cpgs = $this->mcpg->readFarmers($this->get('cpg'));
        if ($cpgs)
            $this->response($cpgs, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any cpgs!'), 404);
    }

    function key_farmers_get() {
        $cpgs = $this->mcpg->readKeyFarmers($this->get('district'), $this->get('query'));
        if ($cpgs)
            $this->response($cpgs, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any cpgs!'), 404);
    }

    function demo_plots_get() {
        $cpgs = $this->mcpg->readDemoPlots($this->get('cpg'));
        if ($cpgs)
            $this->response($cpgs, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any cpgs!'), 404);
    }

    function demoplots_get() {
        $demoplots = $this->mcpg->readDemoplotGrids($this->get('cpg_id'));
        if ($demoplots)
            $this->response($demoplots, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any demoplot!'), 404);
    }

    function demoplot_get() {
        if (!$this->get('id'))
            $this->response(NULL, 400);
        $demoplots = $this->mcpg->readDemoplotDetail($this->get('id'));
        if ($demoplots)
            $this->response($demoplots, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any demoplot!'), 404);
    }

    function demoplot_post() {
        if (!$this->post('demoplot_cpg_id'))
            $this->response(NULL, 400);

        //var proses (begin)
        foreach ($this->post() as $key => $value) {
            if($value == ""){
                $varPro[$key] = null;
            }else{
                $varPro[$key] = $value;
            }
        }
        $varPro['userid'] = $_SESSION['userid'];
        //var proses (end)

        $demoplot = $this->mcpg->createDemoplot($varPro);
        if ($demoplot)
            $this->response($demoplot, 200);
        else
            $this->response(array('error' => 'Error Occured, Demoplot could not be added'), 404);
    }

    function demoplot_put() {
        if (!$this->put('demoplot_id'))
            $this->response(NULL, 400);

        //var proses (begin)
        foreach ($this->put() as $key => $value) {
            if($value == ""){
                $varPro[$key] = null;
            }else{
                $varPro[$key] = $value;
            }
        }
        $varPro['userid'] = $_SESSION['userid'];
        //var proses (end)

        $demoplot = $this->mcpg->updateDemoplot($varPro);
        if ($demoplot)
            $this->response($demoplot, 200);
        else
            $this->response(array('error' => 'Error Occured, Demoplot could not be added'), 404);
    }

    function demoplot_delete() {
        if (!$this->delete('id'))
            $this->response(NULL, 400);
        $demoplot = $this->mcpg->deleteDemoplot($this->delete('id'));
        if ($demoplot)
            $this->response($demoplot, 200);
        else
            $this->response(array('error' => 'Demoplot could not be delete'), 404);
    }

    function batch_training_get() {
        $data = $this->mcpg->getComboBatchTraining($this->get('cpg_id'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any training!'), 404);
    }

    function garden_number_get() {
        $data = $this->mcpg->getComboGardenNumber($this->get('demoplot_owner_id'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any garden!'), 404);
    }

    function demoplot_owner_get() {
        $data = $this->mcpg->getComboDemoplotOwner($this->get('cpg_id'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any Farmer!'), 404);
    }

    public function demoplot_farmer_garden_detail_get(){
        $data = $this->mcpg->getFarmerGardenDetail($this->get('GardenNr'),$this->get('FarmerID'));
        $this->response($data, 200);
    }

    function fasilitators_get() {
        $data = $this->mcpg->readFasilitators($this->get('workarea'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any cpgs!'), 404);
    }

    function fasilitator_alls_get() {
        //$data = $this->mcpg->readFasilitatorAlls($this->get('workarea'));
        $data = $this->mcpg->readFasilitators($this->get('workarea'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any cpgs!'), 404);
    }

    function fasilitator_mitras_get() {
        $data = $this->mcpg->readFasilitatorMitras($this->get('workarea'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any cpgs!'), 404);
    }

    function penyuluhs_get() {
        $data = $this->mcpg->readPenyuluhs($this->get('kab'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any cpgs!'), 404);
    }

    function trainingNames_get() {
        $cpgs = $this->mcpg->readTrainingNames();
        if ($cpgs)
            $this->response($cpgs, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any cpgs!'), 404);
    }

    function training_subtopic_get(){
        $data = $this->mcpg->getTrainingSubtopic($this->get('CpgTrainingsID'));
        $this->response($data, 200);
    }

    function cpgc_get() {
        if (!$this->get('id'))
            $this->response(NULL, 400);
        $cpg = $this->mcpg->readCpg($this->get('id'), $this->get('NurseryNr'), $this->get('opsiCall'));
        if ($cpg)
            $this->response($cpg, 200);
        else
            $this->response(array('error' => 'Cpg could not be found'), 404);
    }

    function farmergroups_get() {
        $cpgs = $this->Mcpg->readCpgs();
        if ($cpgs) {
            $this->response($cpgs, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any CPG\'s!'), 404);
        }
    }

    function cpgc_post() {
        if (!$this->post('GroupName'))
            $this->response('Failed to save data', 400);

        //validasi user role dan PartnerID nya (begin)
            //ambil informasi user role dan partnernya
            $userInfo = $this->muserprofile->getUserProfile();
            $isAdmin = $_SESSION['is_admin'];

            if($isAdmin == "1"){
                $OwnerClientID = "1";
            }else{
                //cek apakah program atau private
                $OwnerClientID = $_SESSION['PartnerID'];
            }

            if($OwnerClientID == "")
                $this->response('No access to save data', 400);
        //validasi user role dan PartnerID nya (end)

        $cpg = $this->mcpg->createCpg($this->post('GroupName'), $this->post('Address'), $this->post('TahunTerbentuk'), $this->post('Desa'), $this->post('latitude'), $this->post('longitude'), $this->post('elevation'), 'active', $this->post('AdaPengurus'), $this->post('ketua'), $this->post('sekretaris'), $this->post('bendahara'), $this->post('PertemuanLatitude'), $this->post('PertemuanLongitude'), $_SESSION['userid'], $OwnerClientID);
        if ($cpg)
            $this->response($cpg, 200);
        else
            $this->response('Failed to saved data', 400);
    }

    function training_post() {
        if (!$this->post('CpgTrainingsID'))
            $this->response(NULL, 400);

        $cpg = $this->mcpg->createTraining($this->post('idd'), $this->post('CpgTrainingsID'), $this->post('ProgramStaffID'), $this->post('ExtensionStaffID'), $this->post('KeyFarmerID'), $this->post('DemoplotOwnerID'), $this->post('TrainingStart'), $this->post('TrainingEnd'), $this->post('PetaniKakao'), $this->post('FamilyID'), $this->post('batch'), $this->post('TrainingDays'), $_SESSION['userid'], $this->post('TrainingDayStatus'), $this->post('CpgTrainingsIDSubTopic'));
        if ($cpg)
            $this->response($cpg, 200);
        else
            $this->response(array('error' => 'Cpg could not be found'), 404);
    }

    function training_put() {
        if (!$this->put('idt'))
            $this->response(NULL, 400);
        $cpg = $this->mcpg->updateTraining($this->put('CpgTrainingsID'), $this->put('ProgramStaffID'), $this->put('ExtensionStaffID'), $this->put('KeyFarmerID'), $this->put('DemoplotOwnerID'), $this->put('TrainingStart'), $this->put('TrainingEnd'), $this->put('PetaniKakao'), $this->put('FamilyID'), $this->put('batch'), $this->put('TrainingDays'), $this->put('idt'), $_SESSION['userid'], $this->put('TrainingDayStatus'), $this->put('CpgTrainingsIDSubTopic'));
        if ($cpg)
            $this->response($cpg, 200);
        else
            $this->response(array('error' => 'Cpg could not be found'), 404);
    }

    function farmergroup_get() {
        if (!$this->get('id')) {
            $this->response(NULL, 400);
        }

        $cpg = $this->Mcpg->readCpg($this->get('id'));

        if ($cpg) {
            $this->response($cpg, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Unit could not be found'), 404);
        }
    }

    function cpgc_put() {
        if (!$this->put('id'))
            $this->response(NULL, 400);
        $cpg = $this->mcpg->updateCpg($this->put('GroupName'), $this->put('Address'), $this->put('TahunTerbentuk'), $this->put('Desa'), $this->put('latitude'), $this->put('longitude'), $this->put('elevation'), $this->put('status'), $this->put('AdaPengurus'), $this->put('ketua'), $this->put('sekretaris'), $this->put('bendahara'), $this->put('PertemuanLatitude'), $this->put('PertemuanLongitude'), $this->put('id'), $_SESSION['userid']);
        if ($cpg)
            $this->response($cpg, 200);
        else
            $this->response(array('error' => 'Cpg could not be found'), 404);
    }

    function farmergroup_post() {
        $groupName = $this->post('groupName');
        $tahunTerbentuk = $this->post('tahunTerbentuk');
        $regionID = $this->post('regionID');

        if (!$groupName) {
            $this->response(NULL, 400);
        }

        $cpg = $this->Mcpg->createCpg($groupName, $tahunTerbentuk, $regionID);

        if ($cpg) {
            $this->response($cpg, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Unit could not be found'), 404);
        }
    }

    function cpgc_delete() {
        if (!$this->delete('id'))
            $this->response(NULL, 400);
        $cpg = $this->mcpg->deleteCpg($this->delete('id'));
        if ($cpg)
            $this->response($cpg, 200);
        else
            $this->response(array('error' => 'Cpg could not be delete'), 404);
    }

    function training_delete() {
        if (!$this->delete('id'))
            $this->response(NULL, 400);
        $cpg = $this->mcpg->deleteTraining($this->delete('id'));
        if ($cpg)
            $this->response($cpg, 200);
        else
            $this->response(array('error' => 'Cpg could not be delete'), 404);
    }

    function participant_delete() {
        if (!$this->delete('id'))
            $this->response(NULL, 400);
        $cpg = $this->mcpg->deleteParticipant($this->delete('id'));
        if ($cpg)
            $this->response($cpg, 200);
        else
            $this->response(array('error' => 'Cpg could not be delete'), 404);
    }

    function farmergroup_delete() {
        $cpgID = $this->delete('cpgID');
        // $this->response($id, 200);
        if (!$cpgID) {
            $this->response(NULL, 400);
        }

        $cpg = $this->Mcpg->deleteCpg($cpgID);

        if ($cpg) {
            $this->response($cpg, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Unit could not be delete'), 404);
        }
    }

    function staff_access_get() {
        $cpgs = $this->mcpg->readStaffAccess($this->get('id'));
        if ($cpgs)
            $this->response($cpgs, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any cpgs!'), 404);
    }

    function staff_get() {
        $cpgs = $this->mcpg->readStaff($this->get('prov'), $this->get('cpg'));
        if ($cpgs)
            $this->response($cpgs, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any cpgs!'), 404);
    }

    function cpgc_access_post() {
        if (!$this->post('cpg'))
            $this->response(NULL, 400);
        $cpg = $this->mcpg->createAccess($this->post('staff'), $this->post('cpg'));
        if ($cpg)
            $this->response($cpg, 200);
        else
            $this->response(array('error' => 'Cpg could not be found'), 404);
    }

    function cpgc_access_delete() {
        if (!$this->delete('cpg'))
            $this->response(NULL, 400);
        $cpg = $this->mcpg->deleteAccess($this->delete('staff'), $this->delete('cpg'));
        if ($cpg)
            $this->response($cpg, 200);
        else
            $this->response(array('error' => 'Cpg could not be found'), 404);
    }

    function check_get() {
        $data = $this->mcpg->checkFarmer($this->get('trainingid'), $this->get('farmerid'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any RegionIDs!'), 404);
    }

    //compost
    function composts_get() {
        //tidak terpakai
        $data = $this->mcpg->readComposts($this->get('cpg_id'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any RegionIDs!'), 404);
    }

    function compost_penjualans_get() {
        $data = $this->mcpg->readCompostPenjualans($this->get('compost_id'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any RegionIDs!'), 404);
    }

    function compost_petani_get() {
        $data = $this->mcpg->readCompostPetani($this->get('cpg_id'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any RegionIDs!'), 404);
    }

    function compost_post() {
        if (!$this->post('Established'))
            $this->response(NULL, 400);
        $cpg = $this->mcpg->createCompost($this->post('type_obj'), $this->post('id_obj'), $this->post('Established'), $this->post('MesinChooper'), $this->post('RumahKompos'), $this->post('CompostLatitude'), $this->post('CompostLongitude'), $_SESSION['userid']);
        if ($cpg)
            $this->response($cpg, 200);
        else
            $this->response(array('error' => 'Cpg could not be found'), 404);
    }

    function compost_get() {
        $data = $this->mcpg->readCompost($this->get('id'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any RegionIDs!'), 404);
    }

    function compost_put() {
        if (!$this->put('CompostID'))
            $this->response(NULL, 400);
        $cpg = $this->mcpg->updateCompost($this->put('type_obj'), $this->put('id_obj'), $this->put('Established'), $this->put('MesinChooper'), $this->put('RumahKompos'), $this->put('CompostLatitude'), $this->put('CompostLongitude'), $_SESSION['userid'], $this->put('CompostID'));
        if ($cpg)
            $this->response($cpg, 200);
        else
            $this->response(array('error' => 'Cpg could not be found'), 404);
    }

    function compost_delete() {
        if (!$this->delete('id'))
            $this->response(NULL, 400);
        $cpg = $this->mcpg->deleteCompost($this->delete('id'));
        if ($cpg)
            $this->response($cpg, 200);
        else
            $this->response(array('error' => 'Cpg could not be found'), 404);
    }

    function compost_penjualan_post() {
        if (!$this->post('Buyer'))
            $this->response(NULL, 400);
        $cpg = $this->mcpg->createCompostPenjualan($this->post('id_compost'), $this->post('Buyer'), $this->post('Volume'), $this->post('Price'), $this->post('DateTransaction'), $_SESSION['userid']);
        if ($cpg)
            $this->response($cpg, 200);
        else
            $this->response(array('error' => 'Cpg could not be found'), 404);
    }

    function compost_penjualan_put() {
        if (!$this->put('id'))
            $this->response(NULL, 400);
        $cpg = $this->mcpg->updateCompostPenjualan($this->put('id_compost'), $this->put('Buyer'), $this->put('Volume'), $this->put('Price'), $this->put('DateTransaction'), $_SESSION['userid'], $this->put('id'));
        if ($cpg)
            $this->response($cpg, 200);
        else
            $this->response(array('error' => 'Cpg could not be found'), 404);
    }

    function compost_penjualan_delete() {
        if (!$this->delete('id'))
            $this->response(NULL, 400);
        $cpg = $this->mcpg->deleteCompostPenjualan($this->delete('id'));
        if ($cpg)
            $this->response($cpg, 200);
        else
            $this->response(array('error' => 'Cpg could not be found'), 404);
    }

    //end compost
    //nursey
    function nurseys_get() {
        $data = $this->mcpg->readNurseys($this->get('cpg_id'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any RegionIDs!'), 404);
    }

    function nursey_penjualans_get() {
        $data = $this->mcpg->readNurseyPenjualans($this->get('nursery_id'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any RegionIDs!'), 404);
    }

    function nursey_monitorings_get() {
        $data = $this->mcpg->readNurseyMonitorings($this->get('nursery_id'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any RegionIDs!'));
    }

    // Nursery monitoring add
    function nursey_monitorings_post() {
        //if(!$this->post('Buyer')) $this->response(NULL, 400);
        /* if(!$this->post('')) */
        $nurseryID = $this->post('id_nursey');
        $monitoringDate = $this->post('MonitoringDate');
        $monitoringStatus = $this->post('MonitoringStatus');
        $description = $this->post('Description');
        $cpg = $this->mcpg->createNurseyMonitoring($nurseryID, $monitoringDate, $monitoringStatus, $description, $_SESSION['userid']);
        if ($cpg)
            $this->response($cpg, 200);
        else
            $this->response(array('error' => 'Cpg could not be found'), 404);
    }

    // nursery monitoring update
    function nursey_monitorings_put() {
        $id = $this->put('id');
        $nurseryID = $this->put('id_nursey');
        $monitoringDate = $this->put('MonitoringDate');
        $monitoringStatus = $this->put('MonitoringStatus');
        $description = $this->put('Description');

        $cpg = $this->mcpg->updateNurseyMonitoring($id, $nurseryID, $monitoringDate, $monitoringStatus, $description, $_SESSION['userid']);
        $this->response($cpg, 200);
    }

    // nursery monitoring delete
    function nursey_monitorings_delete() {
        $id = $this->delete('id');
        $cpg = $this->mcpg->deleteNurseyMonitoring($id);
        $this->response($cpg, 200);
    }

    function nursey_petani_get() {
        $data = $this->mcpg->readNurseyPetani($this->get('cpg_id'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any RegionIDs!'), 404);
    }

    public function nursery_form_photo_post(){
        if ($this->file['Photo']['name'] != '') {
            $gambar = date('Ymdhis') . '_' . $this->file['Photo']['name'];
            $upload = move_upload($this->file, 'images/nursery/' . $gambar);
            if (isset($upload['upload_data'])) {
                unlink('images/nursery/' . $this->post('Photo_old'));
                $result['success'] = true;
                $result['file']    = $gambar;
                $this->response($result, 200);
            }
        }
    }

    public function nursery_form_photo_responsible_post(){
        if ($this->file['PhotoResponsible']['name'] != '') {
            $gambar = date('Ymdhis') . '_' . $this->file['PhotoResponsible']['name'];
            $upload = move_upload($this->file, 'images/photo_responsible/' . $gambar);
            if (isset($upload['upload_data'])) {
                unlink('images/photo_responsible/' . $this->post('Photo_old_responsible'));
                $result['success'] = true;
                $result['file']    = $gambar;
                $this->response($result, 200);
            }
        }
    }

    function nursey_post() {
        //validasi responsiblenya (begin)
        $valResponsible = true;
        switch ($this->post('nurResponsibleType')) {
            case 'farmer':
            case 'staff':
                if($this->post('Responsible') == "") $valResponsible = false;
            break;
            case 'other':
                if($this->post('nurResponsibleName') == "" || $this->post('nurResponsibleGender') == "") $valResponsible = false;
            break;
        }
        if($valResponsible == false){
            $this->response('Responsible information is empty', 400);
        }else{
            $paramResponsible['nurResponsibleType'] = $this->post('nurResponsibleType');
            $paramResponsible['Responsible'] = $this->post('Responsible');
            $paramResponsible['nurResponsibleName'] = $this->post('nurResponsibleName');
            $paramResponsible['nurResponsibleBirthday'] = $this->post('nurResponsibleBirthday');
            $paramResponsible['nurResponsibleGender'] = $this->post('nurResponsibleGender');
            $paramResponsible['nurResponsiblePhone'] = $this->post('nurResponsiblePhone');
            $paramResponsible['Photo_old_responsible'] = $this->post('Photo_old_responsible');

            //kasih null kalau kosong
            foreach ($paramResponsible as $key => $value) {
                if($value == "") $paramResponsible[$key] = null;
            }
        }
        //validasi responsiblenya (end)

        //saring variabel nursery checklist (begin)
        $varPostNurseryChecklist = array();

        $varPostNurseryChecklist['LocationCloseToCommunityNo'] = ($this->post('LocationCloseToCommunityNo') == '') ? null : $this->post('LocationCloseToCommunityNo');
        $varPostNurseryChecklist['LocationCloseToCommunity'] = ($this->post('LocationCloseToCommunity') == '') ? null : $this->post('LocationCloseToCommunity');
        $varPostNurseryChecklist['GoodLandAreaNo'] = ($this->post('GoodLandAreaNo') == '') ? null : $this->post('GoodLandAreaNo');
        $varPostNurseryChecklist['GoodLandArea'] = ($this->post('GoodLandArea') == '') ? null : $this->post('GoodLandArea');
        $varPostNurseryChecklist['LocationNearCocoaFarmNo'] = ($this->post('LocationNearCocoaFarmNo') == '') ? null : $this->post('LocationNearCocoaFarmNo');
        $varPostNurseryChecklist['LocationNearCocoaFarm'] = ($this->post('LocationNearCocoaFarm') == '') ? null : $this->post('LocationNearCocoaFarm');
        $varPostNurseryChecklist['ContinuousWaterSupplyNo'] = ($this->post('ContinuousWaterSupplyNo') == '') ? null : $this->post('ContinuousWaterSupplyNo');
        $varPostNurseryChecklist['ContinuousWaterSupply'] = ($this->post('ContinuousWaterSupply') == '') ? null : $this->post('ContinuousWaterSupply');
        $varPostNurseryChecklist['IrrigationInstalledNo'] = ($this->post('IrrigationInstalledNo') == '') ? null : $this->post('IrrigationInstalledNo');
        $varPostNurseryChecklist['IrrigationInstalled'] = ($this->post('IrrigationInstalled') == '') ? null : $this->post('IrrigationInstalled');
        $varPostNurseryChecklist['UseShadingNetNo'] = ($this->post('UseShadingNetNo') == '') ? null : $this->post('UseShadingNetNo');
        $varPostNurseryChecklist['UseShadingNet'] = ($this->post('UseShadingNet') == '') ? null : $this->post('UseShadingNet');
        $varPostNurseryChecklist['AdequateSupplyTopSoilNo'] = ($this->post('AdequateSupplyTopSoilNo') == '') ? null : $this->post('AdequateSupplyTopSoilNo');
        $varPostNurseryChecklist['AdequateSupplyTopSoil'] = ($this->post('AdequateSupplyTopSoil') == '') ? null : $this->post('AdequateSupplyTopSoil');
        $varPostNurseryChecklist['ImprovedVarietyNo'] = ($this->post('ImprovedVarietyNo') == '') ? null : $this->post('ImprovedVarietyNo');
        $varPostNurseryChecklist['ImprovedVariety'] = ($this->post('ImprovedVariety') == '') ? null : $this->post('ImprovedVariety');
        $varPostNurseryChecklist['ConstructStoringNo'] = ($this->post('ConstructStoringNo') == '') ? null : $this->post('ConstructStoringNo');
        $varPostNurseryChecklist['ConstructStoring'] = ($this->post('ConstructStoring') == '') ? null : $this->post('ConstructStoring');
        $varPostNurseryChecklist['CorrectEquipmentNo'] = ($this->post('CorrectEquipmentNo') == '') ? null : $this->post('CorrectEquipmentNo');
        $varPostNurseryChecklist['CorrectEquipment'] = ($this->post('CorrectEquipment') == '') ? null : $this->post('CorrectEquipment');
        $varPostNurseryChecklist['WindBreakInstalledNo'] = ($this->post('WindBreakInstalledNo') == '') ? null : $this->post('WindBreakInstalledNo');
        $varPostNurseryChecklist['WindBreakInstalled'] = ($this->post('WindBreakInstalled') == '') ? null : $this->post('WindBreakInstalled');
        $varPostNurseryChecklist['SecurityFenceInstalledNo'] = ($this->post('SecurityFenceInstalledNo') == '') ? null : $this->post('SecurityFenceInstalledNo');
        $varPostNurseryChecklist['SecurityFenceInstalled'] = ($this->post('SecurityFenceInstalled') == '') ? null : $this->post('SecurityFenceInstalled');
        $varPostNurseryChecklist['FertilizerUsedNo'] = ($this->post('FertilizerUsedNo') == '') ? null : $this->post('FertilizerUsedNo');
        $varPostNurseryChecklist['FertilizerUsed'] = ($this->post('FertilizerUsed') == '') ? null : $this->post('FertilizerUsed');
        $varPostNurseryChecklist['OperatorAdequateTrainingNo'] = ($this->post('OperatorAdequateTrainingNo') == '') ? null : $this->post('OperatorAdequateTrainingNo');
        $varPostNurseryChecklist['OperatorAdequateTraining'] = ($this->post('OperatorAdequateTraining') == '') ? null : $this->post('OperatorAdequateTraining');
        $varPostNurseryChecklist['AdequateFacilityNo'] = ($this->post('AdequateFacilityNo') == '') ? null : $this->post('AdequateFacilityNo');
        $varPostNurseryChecklist['AdequateFacility'] = ($this->post('AdequateFacility') == '') ? null : $this->post('AdequateFacility');
        $varPostNurseryChecklist['SustainablePestDiseaseNo'] = ($this->post('SustainablePestDiseaseNo') == '') ? null : $this->post('SustainablePestDiseaseNo');
        $varPostNurseryChecklist['SustainablePestDisease'] = ($this->post('SustainablePestDisease') == '') ? null : $this->post('SustainablePestDisease');
        $varPostNurseryChecklist['CloneGradingNo'] = ($this->post('CloneGradingNo') == '') ? null : $this->post('CloneGradingNo');
        $varPostNurseryChecklist['CloneGrading'] = ($this->post('CloneGrading') == '') ? null : $this->post('CloneGrading');
        $varPostNurseryChecklist['SeedlingCullingDoneNo'] = ($this->post('SeedlingCullingDoneNo') == '') ? null : $this->post('SeedlingCullingDoneNo');
        $varPostNurseryChecklist['SeedlingCullingDone'] = ($this->post('SeedlingCullingDone') == '') ? null : $this->post('SeedlingCullingDone');
        $varPostNurseryChecklist['ProperInputSalesRecordNo'] = ($this->post('ProperInputSalesRecordNo') == '') ? null : $this->post('ProperInputSalesRecordNo');
        $varPostNurseryChecklist['ProperInputSalesRecord'] = ($this->post('ProperInputSalesRecord') == '') ? null : $this->post('ProperInputSalesRecord');
        $varPostNurseryChecklist['SeedsPreGerminatedNo'] = ($this->post('SeedsPreGerminatedNo') == '') ? null : $this->post('SeedsPreGerminatedNo');
        $varPostNurseryChecklist['SeedsPreGerminated'] = ($this->post('SeedsPreGerminated') == '') ? null : $this->post('SeedsPreGerminated');
        //saring variabel nursery checklist (end)

        if($this->post('NurseryID') == ""){
            $cpg = $this->mcpg->createNursey($this->post('CPGId'), $this->post('type_obj'), $this->post('id_obj'), $this->post('Responsible'), $this->post('nEstablished'), $this->post('Panjang'), $this->post('Lebar'), $this->post('Kapasitas'), $this->post('Latitude'), $this->post('Longitude'), $this->post('LatitudeDeg1'), $this->post('LatitudeDeg2'), $this->post('LatitudeDeg3'), $this->post('LongitudeDeg1'), $this->post('LongitudeDeg2'), $this->post('LongitudeDeg3'), $this->post('NursCertBp2YaTidak'), $this->post('tglCertificate'), $this->post('DateAppliedCertification'), $this->post('Photo_old'), $_SESSION['userid'], $varPostNurseryChecklist, $paramResponsible);
        }else{
            $cpg = $this->mcpg->updateNursey($this->post('type_obj'), $this->post('CPGId'), $this->post('Responsible'), $this->post('nEstablished'), $this->post('Panjang'), $this->post('Lebar'), $this->post('Kapasitas'), $this->post('Latitude'), $this->post('Longitude'), $this->post('LatitudeDeg1'), $this->post('LatitudeDeg2'), $this->post('LatitudeDeg3'), $this->post('LongitudeDeg1'), $this->post('LongitudeDeg2'), $this->post('LongitudeDeg3'), $this->post('NursCertBp2YaTidak'), $this->post('tglCertificate'), $_SESSION['userid'], $this->post('NurseryID'), $this->post('Photo_old'), $varPostNurseryChecklist, $paramResponsible);
        }

        if ($cpg)
            $this->response($cpg, 200);
        else
            $this->response('Failed to save data', 400);
    }

    function nursey_get() {
        $data = $this->mcpg->readNursey($this->get('id'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any RegionIDs!'), 404);
    }

    function nursey_put() {
        if (!$this->put('NurseryID'))
            $this->response(NULL, 400);

        //saring variabel nursery checklist (begin)
        $varPostNurseryChecklist = array();

        $varPostNurseryChecklist['LocationCloseToCommunityNo'] = ($this->put('LocationCloseToCommunityNo') == '') ? null : $this->put('LocationCloseToCommunityNo');
        $varPostNurseryChecklist['LocationCloseToCommunity'] = ($this->put('LocationCloseToCommunity') == '') ? null : $this->put('LocationCloseToCommunity');
        $varPostNurseryChecklist['GoodLandAreaNo'] = ($this->put('GoodLandAreaNo') == '') ? null : $this->put('GoodLandAreaNo');
        $varPostNurseryChecklist['GoodLandArea'] = ($this->put('GoodLandArea') == '') ? null : $this->put('GoodLandArea');
        $varPostNurseryChecklist['LocationNearCocoaFarmNo'] = ($this->put('LocationNearCocoaFarmNo') == '') ? null : $this->put('LocationNearCocoaFarmNo');
        $varPostNurseryChecklist['LocationNearCocoaFarm'] = ($this->put('LocationNearCocoaFarm') == '') ? null : $this->put('LocationNearCocoaFarm');
        $varPostNurseryChecklist['ContinuousWaterSupplyNo'] = ($this->put('ContinuousWaterSupplyNo') == '') ? null : $this->put('ContinuousWaterSupplyNo');
        $varPostNurseryChecklist['ContinuousWaterSupply'] = ($this->put('ContinuousWaterSupply') == '') ? null : $this->put('ContinuousWaterSupply');
        $varPostNurseryChecklist['IrrigationInstalledNo'] = ($this->put('IrrigationInstalledNo') == '') ? null : $this->put('IrrigationInstalledNo');
        $varPostNurseryChecklist['IrrigationInstalled'] = ($this->put('IrrigationInstalled') == '') ? null : $this->put('IrrigationInstalled');
        $varPostNurseryChecklist['UseShadingNetNo'] = ($this->put('UseShadingNetNo') == '') ? null : $this->put('UseShadingNetNo');
        $varPostNurseryChecklist['UseShadingNet'] = ($this->put('UseShadingNet') == '') ? null : $this->put('UseShadingNet');
        $varPostNurseryChecklist['AdequateSupplyTopSoilNo'] = ($this->put('AdequateSupplyTopSoilNo') == '') ? null : $this->put('AdequateSupplyTopSoilNo');
        $varPostNurseryChecklist['AdequateSupplyTopSoil'] = ($this->put('AdequateSupplyTopSoil') == '') ? null : $this->put('AdequateSupplyTopSoil');
        $varPostNurseryChecklist['ImprovedVarietyNo'] = ($this->put('ImprovedVarietyNo') == '') ? null : $this->put('ImprovedVarietyNo');
        $varPostNurseryChecklist['ImprovedVariety'] = ($this->put('ImprovedVariety') == '') ? null : $this->put('ImprovedVariety');
        $varPostNurseryChecklist['ConstructStoringNo'] = ($this->put('ConstructStoringNo') == '') ? null : $this->put('ConstructStoringNo');
        $varPostNurseryChecklist['ConstructStoring'] = ($this->put('ConstructStoring') == '') ? null : $this->put('ConstructStoring');
        $varPostNurseryChecklist['CorrectEquipmentNo'] = ($this->put('CorrectEquipmentNo') == '') ? null : $this->put('CorrectEquipmentNo');
        $varPostNurseryChecklist['CorrectEquipment'] = ($this->put('CorrectEquipment') == '') ? null : $this->put('CorrectEquipment');
        $varPostNurseryChecklist['WindBreakInstalledNo'] = ($this->put('WindBreakInstalledNo') == '') ? null : $this->put('WindBreakInstalledNo');
        $varPostNurseryChecklist['WindBreakInstalled'] = ($this->put('WindBreakInstalled') == '') ? null : $this->put('WindBreakInstalled');
        $varPostNurseryChecklist['SecurityFenceInstalledNo'] = ($this->put('SecurityFenceInstalledNo') == '') ? null : $this->put('SecurityFenceInstalledNo');
        $varPostNurseryChecklist['SecurityFenceInstalled'] = ($this->put('SecurityFenceInstalled') == '') ? null : $this->put('SecurityFenceInstalled');
        $varPostNurseryChecklist['FertilizerUsedNo'] = ($this->put('FertilizerUsedNo') == '') ? null : $this->put('FertilizerUsedNo');
        $varPostNurseryChecklist['FertilizerUsed'] = ($this->put('FertilizerUsed') == '') ? null : $this->put('FertilizerUsed');
        $varPostNurseryChecklist['OperatorAdequateTrainingNo'] = ($this->put('OperatorAdequateTrainingNo') == '') ? null : $this->put('OperatorAdequateTrainingNo');
        $varPostNurseryChecklist['OperatorAdequateTraining'] = ($this->put('OperatorAdequateTraining') == '') ? null : $this->put('OperatorAdequateTraining');
        $varPostNurseryChecklist['AdequateFacilityNo'] = ($this->put('AdequateFacilityNo') == '') ? null : $this->put('AdequateFacilityNo');
        $varPostNurseryChecklist['AdequateFacility'] = ($this->put('AdequateFacility') == '') ? null : $this->put('AdequateFacility');
        $varPostNurseryChecklist['SustainablePestDiseaseNo'] = ($this->put('SustainablePestDiseaseNo') == '') ? null : $this->put('SustainablePestDiseaseNo');
        $varPostNurseryChecklist['SustainablePestDisease'] = ($this->put('SustainablePestDisease') == '') ? null : $this->put('SustainablePestDisease');
        $varPostNurseryChecklist['CloneGradingNo'] = ($this->put('CloneGradingNo') == '') ? null : $this->put('CloneGradingNo');
        $varPostNurseryChecklist['CloneGrading'] = ($this->put('CloneGrading') == '') ? null : $this->put('CloneGrading');
        $varPostNurseryChecklist['SeedlingCullingDoneNo'] = ($this->put('SeedlingCullingDoneNo') == '') ? null : $this->put('SeedlingCullingDoneNo');
        $varPostNurseryChecklist['SeedlingCullingDone'] = ($this->put('SeedlingCullingDone') == '') ? null : $this->put('SeedlingCullingDone');
        $varPostNurseryChecklist['ProperInputSalesRecordNo'] = ($this->put('ProperInputSalesRecordNo') == '') ? null : $this->put('ProperInputSalesRecordNo');
        $varPostNurseryChecklist['ProperInputSalesRecord'] = ($this->put('ProperInputSalesRecord') == '') ? null : $this->put('ProperInputSalesRecord');
        $varPostNurseryChecklist['SeedsPreGerminatedNo'] = ($this->put('SeedsPreGerminatedNo') == '') ? null : $this->put('SeedsPreGerminatedNo');
        $varPostNurseryChecklist['SeedsPreGerminated'] = ($this->put('SeedsPreGerminated') == '') ? null : $this->put('SeedsPreGerminated');
        //saring variabel nursery checklist (end)


        $cpg = $this->mcpg->updateNursey($this->put('type_obj'), $this->put('CPGId'), $this->put('Responsible'), $this->put('nEstablished'), $this->put('Panjang'), $this->put('Lebar'), $this->put('Kapasitas'), $this->put('Latitude'), $this->put('Longitude'), $this->put('LatitudeDeg1'), $this->put('LatitudeDeg2'), $this->put('LatitudeDeg3'), $this->put('LongitudeDeg1'), $this->put('LongitudeDeg2'), $this->put('LongitudeDeg3'), $this->put('NursCertBp2YaTidak'), $this->put('tglCertificate'), $_SESSION['userid'], $this->put('NurseryID'), $this->put('Photo_old'), $varPostNurseryChecklist);
        if ($cpg)
            $this->response($cpg, 200);
        else
            $this->response(array('error' => 'Cpg could not be found'), 404);
    }

    function nursey_delete() {
        if (!$this->delete('id'))
            $this->response(NULL, 400);
        $cpg = $this->mcpg->deleteNursey($this->delete('id'));
        if ($cpg)
            $this->response($cpg, 200);
        else
            $this->response(array('error' => 'Cpg could not be found'), 404);
    }

    function nursery_polygon_get() {
        $data['nursery_polygon'] = site_url() . '/cpg/nursery_polygon';
        $data['nursery_polygon_center'] = site_url() . '/cpg/nursery_polygon_center';
        $data['NurseryID'] = $this->get('NurseryID');
        $data['NurseryNr'] = $this->get('NurseryNr');
        $data['latitude'] = $this->get('lati');
        $data['longitude'] = $this->get('longi');
        $data['hakAksesPolygon'] = $this->get('hakAksesPolygon');

        //get polygon area
        $data['area'] = $this->mcpg->getNurseryPolygonArea($data['NurseryID'], $data['latitude'], $data['longitude']);
        $this->load->view('nursery_map_cpg', @$data);
    }

    public function nursery_polygon_put() {
        if (!$this->put('NurseryID'))
            $this->response(NULL, 400);

        $nursery_polygon = $this->mcpg->updateNurseryPolygon($this->put('NurseryID'), $this->put('NurseryNr'), $this->put('area'), $this->put('luas'), $this->put('lat'), $this->put('long'));
        if ($nursery_polygon)
            $this->response($nursery_polygon, 200);
        else
            $this->response(array('error' => 'Map Polygon could not be found'), 404);
    }

    function nursery_polygon_center_put() {
        if (!$this->put('NurseryID'))
            $this->response(NULL, 400);

        $nursery_polygon = $this->mcpg->updateNurseryPolygonCenter($this->put('NurseryID'), $this->put('NurseryNr'), $this->put('lat'), $this->put('long'));
        if ($nursery_polygon)
            $this->response($nursery_polygon, 200);
        else
            $this->response(array('error' => 'Map Polygon could not be found'), 404);
    }

    function nursery_update_area_get() {
        $data = $this->mcpg->updateNurseryAreaGet($this->get('NurseryID'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function nursey_penjualan_post() {
        if (!$this->post('Buyer'))
            $this->response(NULL, 400);
        $cpg = $this->mcpg->createNurseyPenjualan($this->post('id_nursey'), $this->post('Buyer'), $this->post('CloneTypeID'), $this->post('Volume'), $this->post('Price'), $this->post('DateTransaction'), $_SESSION['userid']);
        if ($cpg)
            $this->response($cpg, 200);
        else
            $this->response(array('error' => 'Cpg could not be found'), 404);
    }

    function nursey_penjualan_put() {
        if (!$this->put('id'))
            $this->response(NULL, 400);
        $cpg = $this->mcpg->updateNurseyPenjualan($this->put('id_nursey'), $this->put('Buyer'), $this->put('CloneTypeID'), $this->put('Volume'), $this->put('Price'), $this->put('DateTransaction'), $_SESSION['userid'], $this->put('id'));
        if ($cpg)
            $this->response($cpg, 200);
        else
            $this->response(array('error' => 'Cpg could not be found'), 404);
    }

    function nursey_penjualan_delete() {
        if (!$this->delete('id'))
            $this->response(NULL, 400);
        $cpg = $this->mcpg->deleteNurseyPenjualan($this->delete('id'));
        if ($cpg)
            $this->response($cpg, 200);
        else
            $this->response(array('error' => 'Cpg could not be found'), 404);
    }

    function nurserynr_combo_get() {
        $data = $this->mcpg->getNurseryNrCombo($this->get('CPGId'));
        $data = array_merge(array(array('id' => '-1', 'label' => '[Add New Nursery]')), $data);
        $this->response($data, 200);
    }

    //end nursey
    //**Clonal Garden**//
    function clonal_penjualans_get() {
        $data = $this->mcpg->readClonalPenjualans($this->get('clonal_id'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any Transactions!'), 404);
    }

    function clonal_penjualan_post() {
        if (!$this->post('Buyer'))
            $this->response(NULL, 400);
        $cpg = $this->mcpg->createClonalPenjualan($this->post('id_clonal'), $this->post('Buyer'), $this->post('CloneTypeID'), $this->post('Volume'), $this->post('Price'), $this->post('DateTransaction'), $_SESSION['userid']);
        if ($cpg)
            $this->response($cpg, 200);
        else
            $this->response(array('error' => 'Clonal Transaction could not be found'), 404);
    }

    function clonal_penjualan_put() {
        if (!$this->put('id'))
            $this->response(NULL, 400);
        $cpg = $this->mcpg->updateClonalPenjualan($this->put('id_clonal'), $this->put('Buyer'), $this->put('CloneTypeID'), $this->put('Volume'), $this->put('Price'), $this->put('DateTransaction'), $_SESSION['userid'], $this->put('id'));
        if ($cpg)
            $this->response($cpg, 200);
        else
            $this->response(array('error' => 'Clonal Transaction could not be found'), 404);
    }

    function clonal_penjualan_delete() {
        if (!$this->delete('id'))
            $this->response(NULL, 400);
        $cpg = $this->mcpg->deleteClonalPenjualan($this->delete('id'));
        if ($cpg)
            $this->response($cpg, 200);
        else
            $this->response(array('error' => 'Cpg could not be found'), 404);
    }

    function clonal_polygons_get() {
        $data = $this->mcpg->readClonalPolygons($this->get('ObjType'), $this->get('ObjID'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any Transactions!'), 404);
    }

    function clonal_polygon_get($type = '') {
        $data['clonal_polygon'] = site_url() . '/cpg/clonal_polygon';
        $data['clonal_polygon_center'] = site_url() . '/cpg/clonal_polygon_center';
        $data['ClonalID'] = $this->get('clonal_id');
        $data['latitude'] = $this->get('lati');
        $data['longitude'] = $this->get('longi');
        $data['Cooplatitude'] = $this->get('cooplat');
        $data['Cooplongitude'] = $this->get('cooplong');
        $data['GardenNr'] = $this->get('garden_nr');
        $data['hakAksesPolygon'] = $this->get('hakAksesPolygon');

        $data['area'] = $this->mcpg->getClonalPolygon($this->get('clonal_id'), $this->get('garden_nr'), $this->get('lati'), $this->get('longi'), $this->get('cooplat'), $this->get('cooplong'));

        if ($type == 'coop') {
            $view = 'clonal_map_polygon_coop';
        } else {
            $view = 'clonal_map_polygon';
        }
        $this->load->view($view, @$data);
    }

    function clonal_polygon_put() {
        if (!$this->put('clonal_id'))
            $this->response(NULL, 400);
        $clonal_polygon = $this->mcpg->updateClonalPolygon($this->put('clonal_id'), $this->put('garden_nr'), $this->put('garden_nr_default'), $this->put('status_code'), $this->put('area'), $this->put('luas'), $this->put('lat'), $this->put('long'));
        if ($clonal_polygon)
            $this->response($clonal_polygon, 200);
        else
            $this->response(array('error' => 'Map Polygon could not be found'), 404);
    }

    function clonal_polygon_center_put() {
        if (!$this->put('clonal_id'))
            $this->response(NULL, 400);
        $clonal_polygon = $this->mcpg->updateClonalPolygonCenter($this->put('clonal_id'), $this->put('garden_nr'), $this->put('lat'), $this->put('long'));
        if ($clonal_polygon)
            $this->response($clonal_polygon, 200);
        else
            $this->response(array('error' => 'Map Polygon could not be found'), 404);
    }

    function clonal_polygon_delete() {
        $ObjType = $this->delete('ObjType');
        $ObjID = $this->delete('ObjID');
        $ClonalID = $this->delete('clonal_id');
        $GardenNr = $this->delete('garden_nr');
        $clonal_garden_polygon = $this->mcpg->deleteClonalPolygon($ObjType, $ObjID, $ClonalID, $GardenNr);
        $this->response($clonal_garden_polygon, 200);
    }

    //*End Clonal Garden*//
    //** Clonal Garden Monitoring**//
    function clonal_monitorings_get() {
        $data = $this->mcpg->readClonalMonitorings($this->get('clonal_id'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any RegionIDs!'));
    }

    function clonal_monitorings_post() {
        //if(!$this->post('Buyer')) $this->response(NULL, 400);
        /* if(!$this->post('')) */
        $ClonalID = $this->post('id_clonal');
        $monitoringDate = $this->post('MonitoringDate');
        $monitoringStatus = $this->post('MonitoringStatus');
        $description = $this->post('Description');
        $clonal_garden = $this->mcpg->createClonalMonitoring($ClonalID, $monitoringDate, $monitoringStatus, $description, $_SESSION['userid']);
        if ($clonal_garden)
            $this->response($clonal_garden, 200);
        else
            $this->response(array('error' => 'Clonal Garden Monitoring could not be found'), 404);
    }

    function clonal_monitorings_put() {
        $id = $this->put('id');
        $ClonalID = $this->put('id_clonal');
        $monitoringDate = $this->put('MonitoringDate');
        $monitoringStatus = $this->put('MonitoringStatus');
        $description = $this->put('Description');

        $clonal_garden = $this->mcpg->updateClonalMonitoring($id, $ClonalID, $monitoringDate, $monitoringStatus, $description, $_SESSION['userid']);
        $this->response($clonal_garden, 200);
    }

    function clonal_monitorings_delete() {
        $id = $this->delete('id');
        $clonal_garden = $this->mcpg->deleteClonalMonitoring($id);
        $this->response($clonal_garden, 200);
    }

    //**//
    //staff cpg
    function staff_cpgs_get() {
        $cpgs = $this->mcpg->readStaffCpg($this->get('id'));
        if ($cpgs)
            $this->response($cpgs, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any cpgs!'), 404);
    }

    function staff_cpg_farmers_get() {
        $cpgs = $this->mcpg->readStaffCpgFarmer($this->get('CPGid'), $this->get('query'));
        if ($cpgs)
            $this->response($cpgs, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any cpgs!'), 404);
    }

    function staff_cpg_delete() {
        if (!$this->delete('id'))
            $this->response(NULL, 400);
        $cpg = $this->mcpg->deleteStaffCpg($this->delete('id'));
        if ($cpg)
            $this->response($cpg, 200);
        else
            $this->response(array('error' => 'Cpg could not be found'), 404);
    }

    function staff_cpg_post() {
        if (!$this->post('CPGid'))
            $this->response(NULL, 400);
        $cpg = $this->mcpg->createStaffCpg($this->post('CPGid'), $this->post('StaffName'), $this->post('FarmerID'), $this->post('Status'), $this->post('Position'), $this->post('Phone'), $this->post('Email'), $this->post('StaffBirthday'), $this->post('StaffGender'), $_SESSION['userid']);
        if ($cpg)
            $this->response($cpg, 200);
        else
            $this->response(array('error' => 'Cpg could not be found'), 404);
    }

    function staff_cpg_put() {
        if (!$this->put('CPGid'))
            $this->response(NULL, 400);
        $cpg = $this->mcpg->updateStaffCpg($this->put('CPGid'), $this->put('StaffName'), $this->put('FarmerID'), $this->put('Status'), $this->put('Position'), $this->put('Phone'), $this->put('Email'), $this->put('StaffBirthday'), $this->put('StaffGender'), $_SESSION['userid'], $this->put('StaffID'));
        if ($cpg)
            $this->response($cpg, 200);
        else
            $this->response(array('error' => 'Cpg could not be found'), 404);
    }

    function membercpg_get() {
        if (!$this->get('cpg'))
            $this->response(NULL, 400);
        $cpgm = $this->mcpg->readCpgMember($this->get('cpg'));
        if ($cpgm)
            $this->response($cpgm, 200);
        else
            $this->response(array('error' => 'Couldn\'t find cpg members!'), 404);
    }

    public function participant_detail_get() {
        $CpgBatchTrainingsFarmerID = $this->get('CpgBatchTrainingsFarmerID');
        $result = $this->mcpg->getParticipantDetail($CpgBatchTrainingsFarmerID);
        $this->response($result, 200);
    }

    public function participant_checklists_get() {
        $CpgBatchTrainingsFarmerID = $this->get('CpgBatchTrainingsFarmerID');
        $CpgBatchTrainingID = $this->get('CpgBatchTrainingID');
        $FarmerID = $this->get('FarmerID');

        $result = $this->mcpg->getFarmerAttendance($CpgBatchTrainingID, $FarmerID);

        $this->response($result, 200);
    }

    public function participant_checklist_day_get() {
        $CpgBatchTrainingID = $this->get('CpgBatchTrainingID');
        $DayNumber = $this->get('DayNumber');

        $result = $this->mcpg->getFarmerAttendanceDay($CpgBatchTrainingID, $DayNumber);

        $this->response($result, 200);
    }

    public function attendance_post() {
        $CpgBatchTrainingID = $this->post('CpgBatchTrainingID');
        $FarmerID = $this->post('FarmerID');
        $data = $this->post('data');
        //echo '<pre>'; print_r($data); exit;

        foreach ($data as $key => $value) {
            //$value['Attendance1'] = $value['Attendance1'] == 'true' ? 1 : 0;
            if ($value['Attendance1'] == 'true' || $value['Attendance1'] == 1) {
                $value['Attendance1'] = 1;
            } else {
                $value['Attendance1'] = 0;
            }

            //$value['Attendance2'] = $value['Attendance2'] == 'true' ? 1 : 0;
            if ($value['Attendance2'] == 'true' || $value['Attendance2'] == 1) {
                $value['Attendance2'] = 1;
            } else {
                $value['Attendance2'] = 0;
            }
            $result = $this->mcpg->updateFarmerAttendance($CpgBatchTrainingID, $FarmerID, $value['DayNumber'], $value['Attendance1'], $value['Attendance2'], $value['TrainingDate'] ? date('Y-m-d', strtotime($value['TrainingDate'])) : null);
        }

        $this->response($result, 200);
    }

    public function attendance_day_post() {
        $CpgBatchTrainingID = $this->post('CpgBatchTrainingID');
        $DayNumber = $this->post('DayNumber');
        $TrainingDate = $this->post('TrainingDate');
        $data = $this->post('data');
        //echo '<pre>'; print_r($data); exit;

        foreach ($data as $key => $value) {
            //$value['Attendance1'] = $value['Attendance1'] == 'true' ? 1 : 0;
            if ($value['Attendance1'] == 'true' || $value['Attendance1'] == 1) {
                $value['Attendance1'] = 1;
            } else {
                $value['Attendance1'] = 0;
            }

            //$value['Attendance2'] = $value['Attendance2'] == 'true' ? 1 : 0;
            if ($value['Attendance2'] == 'true' || $value['Attendance2'] == 1) {
                $value['Attendance2'] = 1;
            } else {
                $value['Attendance2'] = 0;
            }
            $FamilyID = 0;
            if ($value['AnggotaName']) {
                $FamilyID = $this->mcpg->getFamilyID($value['FarmerID'], $value['AnggotaName']);
            }
            $result = $this->mcpg->updateFarmerAttendance($CpgBatchTrainingID, $value['FarmerID'], $DayNumber, $value['Attendance1'], $value['Attendance2'], $TrainingDate ? date('Y-m-d', strtotime($TrainingDate)) : null, $FamilyID,$value['AnggotaName']);
        }
        // exit;
        $this->response($result, 200);
    }

    function data_DayNumber_get() {
        $dayNumber = $this->get('dayNumber');
        if ($dayNumber != '') {
            for ($i = 0; $i < $dayNumber; $i++) {
                $data[$i]['id'] = $i + 1;
            }
        } else {
            $data[0]['id'] = 1;
        }
        //echo "<pre>day number = $dayNumber ".print_r($data,1)."</pre>";exit;
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }

    function clone_ref_combo_get() {
        $data = $this->mcpg->readCloneRefCombo();
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any RegionIDs!'), 404);
    }

    public function provinsis_get() {
        $this->response($this->mcpg->listProvinces(), 200);
    }

    public function masterfarmergroups_get($PartnerID="") {
        if($PartnerID == "") $PartnerID = "1";

        $fg = $this->mcpg->getMasterFarmerGroups($PartnerID);
        if ($fg) {
            $this->response($fg, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any farmer group!'), 404);
        }
    }

    public function kabupatens_get() {
        $ProvinceID = $this->get('key');
        $this->response($this->mcpg->listDistricts($ProvinceID), 200);
    }

    public function respon_by_type_get(){
        $responsibleType = $this->get('responsibleType');
        $CPGid = $this->get('CPGid');
        $data = $this->mcpg->getResponByType($responsibleType,$CPGid);
        $this->response($data, 200);
    }

    public function ass_partner_list_get(){
        $CPGid = $this->get('CPGid');
        $data = $this->mcpg->getAssPartnerList($CPGid);
        $this->response($data, 200);
    }

    public function ass_partner_form_get(){
        $CPGid = $this->get('CPGid');
        $data = $this->mcpg->getAssPartnerFormByCpg($CPGid);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Data not found'), 404);
        }
    }

    public function ass_partner_form_post(){
        $proses = $this->mcpg->saveAssPartner($this->post());
        if ($proses) {
            $this->response($proses, 200);
        } else {
            $this->response(array('error' => 'Process failed'), 404);
        }
    }

}