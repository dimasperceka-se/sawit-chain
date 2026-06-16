<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Scheduler extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->file = $_FILES;
        $this->load->model('scheduler/mscheduler');
    }

    public function check_pull_engine_status_get(){
        $data = $this->mscheduler->checkPullEngineStatus();

        return $this->response($data, 200);
    }

    public function rpt_traceability_get() {
        $stat = $this->mscheduler->generateReportTraceability();
        $result['success'] = $stat;
        $this->response($result, 200);
    }

    public function dash_main_get() {
        $stat = $this->mscheduler->generateDashMain();
        $result['success'] = $stat;
        $this->response($result, 200);
    }

    public function dash_mars_get() {
        $stat = $this->mscheduler->generateDashMars();
        $result['success'] = $stat;
        $this->response($result, 200);
    }

    public function dash_demographic_get() {
        $stat = $this->mscheduler->generateDashDemographic();
        $result['success'] = $stat;
        $this->response($result, 200);
    }

    // saat ini tidak diperlukan lagi, langsung ke table transaction
    public function dash_group_get() {
        $stat = $this->mscheduler->generateDashGroup();
        $result['success'] = $stat;
        $this->response($result, 200);
    }

    public function dash_garden_get() {
        $stat = $this->mscheduler->generateDashGarden();
        $result['success'] = $stat;
        $this->response($result, 200);
    }

    public function dash_agri_get() {
        $stat = $this->mscheduler->generateDashAgri();
        $result['success'] = $stat;
        $this->response($result, 200);
    }

    public function aktivasi_photo_manual_get() {
        $data = $this->mscheduler->getDataPhoto();
        $this->response($data, 200);
    }

    public function ubah_photo_base64_manual_get() {
        $data = $this->mscheduler->getDataPhotoBase64();
        $this->response($data, 200);
    }

    public function dash_nutrition_get() {
        $result = $this->mscheduler->generateDashNutrition();
        $this->response($result, 200);
    }

    public function dash_finance_get() {
        $result = $this->mscheduler->generateDashFinance();
        $this->response($result, 200);
    }

    public function dash_training_get() {
        $result = $this->mscheduler->generateDashTraining();
        $this->response($result, 200);
    }

    public function dash_training_master_get() {
        $result = $this->mscheduler->generateDashTrainingMaster();
        $this->response($result, 200);
    }

    public function dash_survey_get() {
        $result = $this->mscheduler->generateDashSurvey();
        $this->response($result, 200);
    }

    public function dash_kpi_get() {
        $result = $this->mscheduler->generateDashKPI();
        $this->response($result, 200);
    }

    public function dash_environment_get() {
        $result = $this->mscheduler->generateDashEnvironment();
        $this->response($result, 200);
    }

    public function dash_cargill_get() {
        $result = $this->mscheduler->generateDashCargill();
        $this->response($result, 200);
    }

    public function dash_bank_get() {
        $result = $this->mscheduler->generateDashBank();
        $this->response($result, 200);
    }

    public function farmer_trained_get() {
        $result = $this->mscheduler->setFarmerTrained();
        $this->response($result, 200);
    }

    public function farmer_certified_get() {
        $result = $this->mscheduler->setFarmerCertified();
        $this->response($result, 200);
    }

    public function farmer_loanpass_get() {
        $result = $this->mscheduler->setFarmerLoanPass();
        $this->response($result, 200);
    }

    public function cek_consent_letter_file_get() {
        $result = $this->mscheduler->cekConsentLetterFile();
        $this->response($result, 200);
    }

    public function cek_misplaced_consent_letter_file_get() {
        $result = $this->mscheduler->cekMisplacedConsentLetterFile();
        $this->response($result, 200);
    }

    public function input_garden_ha_polygon_get($farmerid = null) {
        $this->load->model('mmiddleware');

        if (!$farmerid) {
            $farmer_id = $this->mmiddleware->getFarmerPoso();
        } else {
            $farmer_id = array(
                array('FarmerID' => $farmerid)
            );
        }
        if ($farmer_id) {
            foreach ($farmer_id as $val) {
                echo "<pre>";
                print_r($val['FarmerID']);
                echo "</pre>";
                $result = $this->mmiddleware->inputGardenHaPolygon($val['FarmerID']);
            }
//            $result = $this->mmiddleware->inputGardenHaPolygon($farmerid);
            echo "<pre>";
            print_r('selesai');
            echo "</pre>";
            exit;
            $this->response($result, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find Farmer ID!'), 404);
        }
    }

    public function pull_middlewaredata_get($syncdate = null, $program = null, $orgUnit = null) {
        $this->load->model('mmiddleware');
        $result = $this->mmiddleware->pullMiddlewareData($syncdate, $program, $orgUnit);

        //update polygon survey plot
        $this->load->model('tools/msyn');
        $result = $this->msyn->updatePolygon_From_pullMiddlewareData();
        $this->msyn->updateImages_From_pullMiddlewareData();

        echo "<pre>";
        print_r('selesai');
        echo "</pre>";
        exit;
        $this->response($result, 200);
    }

    public function pull_dhis_photo_get() {
        $this->load->model('tools/msyn');
        $result = $this->msyn->updateImages_From_pullMiddlewareData();
        $this->response($result, 200);
    }

    public function pull_dhis_polygon_get() {
        $this->load->model('tools/msyn');
        $result = $this->msyn->updatePolygon_From_pullMiddlewareData();
        $this->response($result, 200);
    }

    public function pull_dhis_get() {
        $this->load->model('mmiddleware');

        $syncdate = $this->get('date');
        $program = $this->get('program');
        $orgUnit = $this->get('orgunit');

        $result = $this->mmiddleware->pullDHIS($syncdate, $program, $orgUnit);

        //update polygon survey plot
        $this->load->model('tools/msyn');
        $result = $this->msyn->updatePolygon_From_pullMiddlewareData();
        $result = $this->msyn->updateImages_From_pullMiddlewareData();

        //Update soal plot_status 
        //$ProsesPlotStatus = $this->msyn->CompletePlotStatus(); (Sudah ada cronnya sendiri)

        $this->response($result, 200);
    }

    public function complete_plot_status_get(){
        $this->load->model('tools/msyn');
        $ProsesPlotStatus = $this->msyn->CompletePlotStatus();
        $this->response($ProsesPlotStatus, 200);
    }

    public function clean_polygon_file_get() {
        // fungsi untuk migrasi file ke folder files/polygon/backup
        $this->load->model('mmiddleware');

        $result = $this->mmiddleware->cleanPolygon();
        echo "<pre>";
        print_r('selesai');
        echo "</pre>";
        exit;
    }

    public function fixFarmerPhoto_get() {
        $this->load->model('tools/msyn');
        $result = $this->msyn->fixUpdateImages();

        echo "<pre>";
        print_r('selesai');
        echo "</pre>";
        exit;
        $this->response($result, 200);
    }

    public function pull_middlewaredataecom_get($syncdate = null, $program = null, $orgUnit = null) {
        $this->load->model('mmiddleware');
        $result = $this->mmiddleware->pullMiddlewareDataEcom($syncdate, $program, $orgUnit);
        echo "<pre>";
        print_r('selesai');
        echo "</pre>";
        exit;
        $this->response($result, 200);
    }

    public function sync_userlogin_middlewaredata_get() {
        $this->load->model('mmiddleware');
        $result = $this->mmiddleware->SyncUserLogin();
        $this->response($result, 200);
    }

    public function sync_backup_middlewaredata_get() {
        $this->load->model('mmiddleware');
        $result = $this->mmiddleware->SyncProcess();
        $this->response($result, 200);
    }

    public function pull_uiddata_get() {
        $this->load->model('mmiddleware');

        $uid = $this->get('uid');
        if ($uid) {
            $result = $this->mmiddleware->pullUidData($uid);
            echo "<pre>";
            print_r('selesai');
            echo "</pre>";
            exit;
        } else {
            echo "<pre>";
            print_r('uid kosong');
            echo "</pre>";
            exit;
        }
        $this->response($result, 200);
    }

    public function pull_uiddatabulk_get() {
        $oldLimit = ini_get("memory_limit");
        ini_set("memory_limit", -1);
        $this->load->model('mmiddleware');
        $uids = $this->mmiddleware->getUIDs();
        if (count(uids) > 0) {
            foreach ($uids as $uid) {
                $result = $this->mmiddleware->pullUidData($uid['uid']);
                if (is_array($result)) {
                    $this->mmiddleware->updateUIDAdd($result, $uid['uid']);
                }
            }

            ini_set("memory_limit", $oldLimit);
            echo "<pre>";
            print_r('selesai');
            echo "</pre>";
//            exit;
        } else {
            ini_set("memory_limit", $oldLimit);
            echo "<pre>";
            print_r('uid kosong');
            echo "</pre>";
//            exit;
        }
        ini_set("memory_limit", $oldLimit);
        exit;
        $this->response($result, 200);
    }

    /*
     * Update kuota sertifikasi petani untuk traceability app
     * @author ardiantoro@koltiva.com
     */

    public function traceability_update_farmer_quota_get($district = false) {

        $run = $this->mscheduler->updateFarmerQuota();

        $this->response($run, 200);
    }

    public function cleanup_request_reset_pass_get() {
        $this->mscheduler->cleanupReqResetPass();
        echo 'selesai';
        exit;
    }

    public function data_collection_email_summary_get() {
        $this->load->model('mmiddleware');
        $run = $this->mmiddleware->dataCollectionEmailSummary();
        echo "<pre>";
        print_r('selesai');
        echo "</pre>";
        exit;
    }

    public function farmer_batchnumber_get() {
        $this->response($this->mscheduler->setFarmerBatchNumber(), 200);
    }

    public function setMemberDisplayID_get() {
        $this->load->model('mmiddleware');
        $run = $this->mmiddleware->generateFarmerDisplayID();
    }

    public function fixMemberDisplayID_get() {
        $this->load->model('mmiddleware');
        $run = $this->mmiddleware->fixMemberDisplayID();
    }

    public function getPhotoFile_get(){
        $this->load->model('scheduler/mscheduler');
        $run = $this->mscheduler->generatePhotoFile();
    }

    public function setMemberRole_get() {
        $this->load->model('mmiddleware');
        $members = $this->mmiddleware->getMemberWithoutRoles();
        if ($members) {
            foreach ($members as $val) {
                $this->mmiddleware->InsertMemberRoles($val['MemberID']);
            }
        }
    }

    public function get_plotted_farmer_get() {
        $data = $this->mscheduler->getPlottedFarmer();
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any Transactions!'), 404);
    }

    /**
     * Push data
     */
    public function push_dhis_get() {
        // return true;
        ini_set('display_errors', true);
        error_reporting(E_ALL);
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);

        $this->load->model('mmiddleware');

        $program = $this->get('program'); // push by program
        
        $onlyNew = $this->get('onlyNew');
        if ($onlyNew === 'true') {
            $onlyNew = true;
        } else {
            $onlyNew = false;
        }
        if($program=='nQxNqbkCil1'){ //untuk plantation push satu per satu
            $memberid = $this->get('mid');
            $PlotNr = $this->get('plotnr');
            $SurveyNr = $this->get('surveynr');
            $arrPrimaryKey = array('MemberID'=>$memberid, 'PlotNr'=>$PlotNr, 'SurveyNr'=>$SurveyNr);
            if($memberid=="" && $PlotNr=="" && $SurveyNr==""){
                echo "<pre>";
                print_r($arrPrimaryKey);
                echo "<pre>";
                exit;
            } 
        } else if($program == 'eBCX1KfaDmA'){
            $ApplicantID = $this->get('appid'); // push by farmer registration
            $arrPrimaryKey = array('ApplicantID'=>$ApplicantID);
        } else {
            $memberid = $this->get('mid'); // push by farmer
            $arrPrimaryKey = array('MemberID'=>$memberid);
        }

        $programs = $this->mmiddleware->getAllProgramWithView($program);
        
        if (count($programs) > 0) {
            foreach ($programs as $progkeys => $program) {
                $datas = $this->mmiddleware->getDataBy($onlyNew, $program['uid'], $arrPrimaryKey);
                $this->mmiddleware->syncDataPerProgram($datas, $program['uid']);
            }
        }
        exit;
    }

    public function update_garden_status_get() {
        $data = $this->mscheduler->UpdateGardenStatus();
        $this->response($data, 200);
    }

    /**
     * 24-12-19
     * Scheduler untuk generate informasi tambahan pada grid farmer & 10-01-2020 untuk filter
     * @return [type] [description]
     */
    public function farmer_grid_additional_info_get() {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        $result = $this->mscheduler->generateFarmerGridAdditionalInfo();
        $this->response($result, 200);
    }

    public function farmer_grid_additional_info_unmapped_get() {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        $result = $this->mscheduler->generateFarmerGridAdditionalInfoUnmapped();
        $this->response($result, 200);
    }

    public function generate_processing_get(){
        $result = $this->mscheduler->generateProcessing();
        $this->response($result, 200);
    }

    public function generate_processing_manual_get() {
        $getHaveOer      = $this->get('HaveOer');
        $ProductID       = $this->get('ProductID');
        
        $this->db->where('SupplychainID', (int) $_SESSION['SupplychainID']);
        $this->db->where("StartDate <=", date('Y-m-d'));
        $this->db->where("EndDate >=", date('Y-m-d'));
        $this->db->where("StatusCode", 'active');
        
        if (!empty($ProductID)) {
            $this->db->where("ProductID", (int) $ProductID);
        }

        $getCountProduct =  $this->db->get('ktv_tc_supplychain_product')->result();

        if (empty($getCountProduct)) {
            $result['success'] = false;
            $result['message'] = 'Please input product type on company profile first';

            $this->response($result, 200);
        }

        if ($getHaveOer == 0) {
            $result['success'] = false;
            $result['message'] = 'Please choose have oer before generate processing';

            $this->response($result, 200);
        }
        
        if ($getHaveOer == 2) {
            $fromPopUp = json_decode($this->get('fromPopUp'));
            
            if($fromPopUp == ''){
                $result = $this->mscheduler->generateProcessingAutomated($this->get());
            } 
        } else {
            $fromPopUp = json_decode($this->get('fromPopUp'));

            if (count($fromPopUp) < count($getCountProduct)) {
                $result['success'] = false;
                $result['message'] = 'Data is not complete';

                $this->response($result, 200);
            }
            
            $result = $this->mscheduler->generateProcessingManual($this->get());
        }
       
        
        $this->response($result, 200);
    }

    public function generate_dash_kpi_koltiva_get(){
        $result = $this->mscheduler->generateKPIKoltiva();
        $this->response($result, 200);
    }

    /**
     * 17-11-20
     * Sekali pakai, untuk push farmer WAGS
     * @return [type] [description]
     */
    public function push_dhis_wags_get() {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        ini_set('display_errors', true);
        error_reporting(E_ALL);

        $program = $this->get('program'); // push by program
        $onlyNew = $this->get('onlyNew');
        if ($onlyNew === 'true') {
            $onlyNew = true;
        } else {
            $onlyNew = false;
        }
        
        if ($program != 'zbLN28sbEKd')
            $this->response(['message' => 'bad request'], 400);
        
        $this->load->model('mmiddleware');

        $sql = "SELECT
                    m.MemberID
                FROM
                    ktv_members m 
                LEFT JOIN
                    ktv_member_role mr on mr.MemberID = m.MemberID
                WHERE
                    SUBSTR( m.VillageID, 1, 2 ) IN ( 43, 44 ) 
                AND 
                    StatusCode = 'active'
                AND 
                    m.`MemberDisplayID` LIKE 'F%'
                AND
                    mr.MRoleID = 1";
        $data = $this->db->query($sql)->result_array();
        
        $programs = $this->mmiddleware->getAllProgramWithViewWAGS($program);

        foreach ($data as $key => $value) {
            # code...
            
            if (count($programs) > 0) {
                foreach ($programs as $progkeys => $program) {
                    $datas = $this->mmiddleware->getDataBy($onlyNew, $program['uid'], $value);
                    $this->mmiddleware->syncDataPerProgram($datas, $program['uid']);
                }
            }
        }

        $this->response(['message' => count($data) . ' datas'], 200);
    }

    public function update_polygon_area_get() {
        $result = $this->mscheduler->updatePolygonArea();
        $this->response($result, 200);
    }
}
