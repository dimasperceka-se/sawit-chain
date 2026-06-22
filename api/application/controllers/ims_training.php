<?php
/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Wed Nov 28 2018
 *  File : ims_training.php
 *******************************************/
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//write excel
require_once 'application/third_party/Spout/Autoloader/autoload.php';
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;
use Box\Spout\Writer\Style\StyleBuilder;
use Box\Spout\Writer\Style\Color;
use Box\Spout\Writer\Style\Border;
use Box\Spout\Writer\Style\BorderBuilder;

class Ims_training extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->file = $_FILES;
        $this->load->model('certification/mims_training');
    }

    public function cmb_cpg_by_ims_id_single_get(){
        $IMSID = (int) $this->get('IMSID');
        $data = $this->mims_training->GetCmbByImsIdSingle($IMSID);
        $this->response($data, 200);
    }

    public function cmb_participant_type_get(){
        $IMSID = (int) $this->get('IMSID');
        $CPGid = (int) $this->get('CPGid');
        $data = $this->mims_training->GetCmbParticipantType($IMSID,$CPGid);
        $this->response($data, 200);
    }

    public function cmb_fasilitator_training_get(){
        $data = $this->mims_training->GetCmbFasilitator();
        $this->response($data, 200);
    }

    public function cmb_penyuluh_training_get(){
        $data = $this->mims_training->GetCmbPenyuluh();
        $this->response($data, 200);
    }

    /* ===================================================================================================================== */

    public function ims_training_get_form_get(){
        $IMSID = (int) $this->get('IMSID');
        $data  = $this->mims_training->GetImsTrainingForm($IMSID);
        $this->response($data, 200);
    }

    public function ims_training_generate_post(){
        $VarPost   = $this->post();
        $ParamPost = array();

        //prep variabel (begin)
        foreach ($VarPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_IMS_WinFormImsTrainingGenerateCpg-Form-", '', $key);
            if ($value == "") {
                $value = null;
            }

            $ParamPost[$keyNew] = $value;
        }
        //prep variabel (end)
        //echo '<pre>'; print_r($ParamPost); exit;

        $proses = $this->mims_training->GenerateCpgTraining($ParamPost);
        if($proses['success']==true){
            $this->response($proses, 200);
        }else{
            $this->response($proses, 400);
        }
    }

    public function event_cpg_training_main_grid_get(){
        $IMSID = (int) $this->get('IMSID');
        $data  = $this->mims_training->GetEventCpgTrainingMainGrid($IMSID);
        $this->response($data, 200);
    }

    public function cpg_training_form_data_get(){
        $CpgBatchTrainingID = (int) $this->get('CpgBatchTrainingID');
        $IMSID = (int) $this->get('IMSID');
        $data  = $this->mims_training->GetCpgTrainingFormData($CpgBatchTrainingID,$IMSID);
        $this->response($data, 200);
    }

    public function cpg_training_form_post(){
        $VarPost = $this->post();

        //prep variabel (begin)
        foreach ($VarPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_IMS_WinFormImsTrainingCpg-Form-", '', $key);

            if ($value == "") {
                $value = null;
            }

            $ParamPost[$keyNew] = $value;
        }
        //prep variabel (end)
        //echo '<pre>'; print_r($ParamPost); exit;

        $proses = $this->mims_training->UpdateCpgTrainingForm($ParamPost);
        if($proses['success'] == true){
            $this->response($proses, 200);
        }else{
            $this->response($proses, 400);
        }
    }

    public function cpg_training_participants_main_grid_get(){
        $CpgBatchTrainingID = (int) $this->get('CpgBatchTrainingID');
        $data  = $this->mims_training->GetGridCpgTrainingParticipants($CpgBatchTrainingID);
        $this->response($data, 200);
    }

    public function summary_show_data_post(){
        $OpsiSummary = $this->post('OpsiSummary');
        $IMSID = (int) $this->post('IMSID');

        $proses  = $this->mims_training->GetSummaryShowData($OpsiSummary,$IMSID);
        if($proses['success'] == true)
            $this->response($proses, 200);
        else
            $this->response($proses, 400);
    }

    public function summary_show_data_par_not_assign_get(){
        $IMSID = (int) $this->get('IMSID');

        $sorting = json_decode($this->get('sort'));
        if(isset($sorting[0]->property)) $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : ''; else $sortingField = null;
        if(isset($sorting[0]->direction)) $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : ''; else $sortingDir = null;

        $data = $this->mims_training->GetSummaryShowDataParNotAssign($IMSID,$this->get('start'),$this->get('limit'),$sortingField,$sortingDir,'grid');
        $this->response($data, 200);
    }

    public function summary_show_data_excel_post(){
        //ini_set('display_errors',true); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        $IMSID = (int) $this->post('IMSID');
        $OpsiSummary = $this->post('OpsiSummary');

        switch($OpsiSummary){
            case 'participants_not_assign':
                $DataList = $this->mims_training->GetSummaryShowDataParNotAssign($IMSID,null,null,null,null,'non_grid');

                //generate data header
                $DataHeader = array('No.');
                foreach ($DataList[0] as $key => $value) {
                    $DataHeader[] = $key;
                }
            break;
            default:
                $DataList = array();
                $DataKolom = array();
            break;
        }

        //Generate data list
        $DataListExcel = array();
        foreach ($DataList as $key => $value) {
            array_unshift($value,($key+1));
            foreach ($value as $key1 => $value1) {
                if(is_numeric($value1)){
                    $value1 = (float) $value1;
                }
                $DataListExcel[$key][] = $value1;
            }
        }

        $writer = WriterFactory::create(Type::XLSX); // for XLSX files
        //$writer = WriterFactory::create(Type::CSV); // for CSV files
        //$writer = WriterFactory::create(Type::ODS); // for ODS files

        $writer->setTempFolder('files/sql_view_temp/');
        $NamaFile = date('YmdHis').'_'.$OpsiSummary.'.xlsx';
        $filePath = 'files/sql_view/'.$NamaFile;
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
        $writer->addRowWithStyle($DataHeader,$styleHeader); // add a row at a time

        //style data
        $styleData = (new StyleBuilder())
            ->setBorder($borderDefa)
            ->build();

        //data
        $writer->addRowsWithStyle($DataListExcel, $styleData);

        $writer->close();

        $this->response(array('success' => TRUE, 'FileUrlNya' => base_url() . 'files/sql_view/'.$NamaFile), 200);
        exit;
    }

    public function cpg_training_add_par_main_grid_get(){
        $IMSID = (int) $this->get('IMSID');
        $CpgBatchTrainingID = (int) $this->get('CpgBatchTrainingID');
        $ParticipantType = $this->get('ParticipantType');
        $SearchStringParam = $this->get('SearchStringParam');
        $SearchCpgParam = $this->get('SearchCpgParam');
        
        $sorting = json_decode($this->get('sort'));
        if(isset($sorting[0]->property)) $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : ''; else $sortingField = null;
        if(isset($sorting[0]->direction)) $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : ''; else $sortingDir = null;

        $data = $this->mims_training->GetCpgTrainingAddParMainGrid($IMSID,$CpgBatchTrainingID,$ParticipantType,$SearchStringParam,$SearchCpgParam,$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        $this->response($data, 200);
    }

    public function cpg_training_add_par_post(){
        $CpgBatchTrainingID = (int) $this->post('CpgBatchTrainingID');
        $FarmerIDSel = json_decode($this->post('FarmerIDSel'));
        
        $proses = $this->mims_training->CpgTrainingAddParticipant($CpgBatchTrainingID,$FarmerIDSel);
        if($proses['success'] == true){
            $this->response($proses, 200);
        }else{
            $this->response($proses, 400);
        }
    }

    public function cpg_training_participant_delete(){
        $CpgBatchTrainingID = (int) $this->delete('CpgBatchTrainingID');
        $FarmerID = (int) $this->delete('FarmerID');

        $proses = $this->mims_training->DeleteCpgTrainingParticipant($CpgBatchTrainingID,$FarmerID);
        if($proses['success'] == true){
            $this->response($proses, 200);
        }else{
            $this->response($proses, 400);
        }
    }

    public function grid_event_training_mapping_get(){
        $IMSID = (int) $this->get('IMSID');
        $TrainingType = $this->get('TrainingType');
        $ActivityType = $this->get('ActivityType');
        $ParticipantType = $this->get('ParticipantType');

        $data = $this->mims_training->GetGridEventMappingTraining($IMSID,$TrainingType,$ActivityType,$ParticipantType);
        $this->response($data, 200);
    }

    public function grid_training_gap_available_participant_get(){
        $IMSID = (int) $this->get('IMSID');
        $TrainingType = $this->get('TrainingType');
        $EventType = $this->get('EventType');
        $ActivityType = $this->get('ActivityType');
        $ParticipantType = $this->get('ParticipantType');
        $CPGid = (int) $this->get('CPGid');

        $data = $this->mims_training->GetGridTrainingGapAvailableParticipant($IMSID,$TrainingType,$EventType,$ActivityType,$ParticipantType,$CPGid);
        $this->response($data, 200);
    }

    public function grid_training_coc_available_participant_get(){
        $IMSID = (int) $this->get('IMSID');
        $TrainingType = $this->get('TrainingType');
        $EventType = $this->get('EventType');
        $ActivityType = $this->get('ActivityType');
        $ParticipantType = $this->get('ParticipantType');
        $CPGid = (int) $this->get('CPGid');

        $data = $this->mims_training->GetGridTrainingCocAvailableParticipant($IMSID,$TrainingType,$EventType,$ActivityType,$ParticipantType,$CPGid);
        $this->response($data, 200);
    }

    public function training_event_mapping_get(){
        $data = array();
        $data = $this->mims_training->GetGridTrainingEventMapping($this->get('IMSID'));
        $this->response($data, 200);
    }

    public function training_event_mapping_post(){
        $data = array(
            'IMSID'           => $this->post('Koltiva_view_IMS_WinFormImsTrainingEventMapping-IMSID'),
            'TrainingType'    => "CPG Training",
            'ActivityType'    => $this->post('Koltiva_view_IMS_WinFormImsTrainingEventMapping-ActivityType'),
            'ParticipantType' => $this->post('Koltiva_view_IMS_WinFormImsTrainingEventMapping-ParticipantType'),
            'TopikGAP'        => $this->post('Koltiva_view_IMS_WinFormImsTrainingEventMapping-TopikGAP'),
            'TopikCOC'        => $this->post('Koltiva_view_IMS_WinFormImsTrainingEventMapping-TopikCOC')
        );
        $callback = $this->mims_training->CreateTrainingEventMapping($data);
        if ($callback['success']) {
            # code...
            $this->response($callback, 200);
        } else {
            # code...
            $this->response($callback, 400);
        }
    }

    public function training_event_mapping_put(){
        $data = array(
            'IMSID'           => $this->put('Koltiva_view_IMS_WinFormImsTrainingEventMapping-IMSID'),
            'TrainingType'    => $this->put('Koltiva_view_IMS_WinFormImsTrainingEventMapping-TrainingType'),
            'ActivityType'    => $this->put('Koltiva_view_IMS_WinFormImsTrainingEventMapping-ActivityType'),
            'ParticipantType' => $this->put('Koltiva_view_IMS_WinFormImsTrainingEventMapping-ParticipantType'),
            'TopikGAP'        => $this->put('Koltiva_view_IMS_WinFormImsTrainingEventMapping-TopikGAP'),
            'TopikCOC'        => $this->put('Koltiva_view_IMS_WinFormImsTrainingEventMapping-TopikCOC')
        );
        $callback = $this->mims_training->UpdateTrainingEventMapping($data);
        if ($callback) {
            # code...
            $this->response(array(
                    'success' => true,
                    'message' => lang('Update success')
                ), 200);
        } else {
            # code...
            $this->response(array(
                    'success' => false,
                    'message' => lang('Update failed')
                ), 400);
        }
    }

    public function training_event_mapping_delete(){
        $callback = $this->mims_training->DeleteTrainingEventMapping(
            $this->delete('IMSID'),
            $this->delete('TrainingType'),
            $this->delete('ActivityType'),
            $this->delete('ParticipantType')
        );
        if ($callback) {
            # code...
            $this->response(array(
                    'success' => true,
                    'message' => lang('Delete success')
                ), 200);
        } else {
            # code...
            $this->response(array(
                    'success' => false,
                    'message' => lang('Delete failed')
                ), 400);
        }
    }
}