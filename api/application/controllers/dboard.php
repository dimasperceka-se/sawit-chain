<?php
/**
 * @Author: nikolius
 * @Date:   2017-07-07 10:14:07
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'application/third_party/Spout3/Autoloader/autoload.php';
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Common\Entity\Style\CellAlignment;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Common\Entity\Style\Border;
use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;

class Dboard extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('dboard/mdboard');
        $this->load->model('dboard/mdboardgarden');
        $this->load->model('dboard/mdboard_agriinput');
        $this->load->model('dboard/mdboard_replanting');
        $this->load->model('dboard/mpro_kpi');
        $this->load->model('dboard/mdboard_mill');
        $this->load->model('dboard/mdboard_refinery');
        $this->load->model('dboard/mpro_ndpe');
        $this->load->model('dboard/mpro_surveys');
        $this->load->model('dboard/mpro_survey_ppi');
        $this->load->model('dboard/mpro_master_training');
        $this->load->model('dboard/mdboardtraceability');
        $this->load->model('dboard/mpro_supplychain_kpi');
        $this->load->model('dboard/mpro_supplychain_kpi_full');
        $this->load->model('dboard/mpro_supplychain_kpi_gar');
    }

    /*====================== Detail Dashboard ==============================================*/

    public function dash_gen_demographic_get() {
        $stat = $this->mdboard->generateDashDemographicOptimize();
        $result['success'] = $stat;
        $this->response($result, 200);
    }

    public function dash_get_demographic_get(){
        //cek apakah ada regen
        if($this->get('regen') == "go"){
            $stat = $this->mdboard->generateDashDemographicOptimize();
        }

        $data = $this->mdboard->getDisplayDemographic((int) $this->get('prov'),(int) $this->get('kab'));
        $this->response($data, 200);
    }

    public function dash_gen_agent_demographic_get() {
        $stat = $this->mdboard->generateDashAgentDemographicOptimize();
        $result['success'] = $stat;
        $this->response($result, 200);
    }

    public function dash_get_agent_demographic_get(){
        $data = $this->mdboard->getDisplayAgentDemographic((int) $this->get('prov'),(int) $this->get('kab'));
        $this->response($data, 200);
    }

    public function dash_gen_garden_get(){
        $stat = $this->mdboardgarden->generateDashGardenOptimize();
        $result['success'] = $stat;
        $this->response($result, 200);
    }

    public function dash_get_garden_get(){
        if($this->get('regen') == "go"){
            $stat = $this->mdboardgarden->generateDashGardenOptimize();
        }

        $ftype = filter_var($this->get('ftype'),FILTER_SANITIZE_STRING);
        $fprovince = filter_var($this->get('prov'),FILTER_SANITIZE_STRING);
        $fdistrict = filter_var($this->get('kab'),FILTER_SANITIZE_STRING);

        $data = $this->mdboardgarden->getDisplayGarden($fprovince,$fdistrict,$ftype);
        $this->response($data, 200);
    }


    public function dash_gen_agri_input_get(){
        $stat = $this->mdboard_agriinput->generateDashAgriInputOptimize();
        $this->response($stat, 200);
    }

    public function dash_get_agri_input_get(){
        $data = $this->mdboard_agriinput->getDisplayAgriInputOptimize((int) $this->get('prov'),(int) $this->get('kab'));
        $this->response($data, 200);
    }
    
    public function dash_get_fa_get(){
        ini_set('memory_limit', '-1');
        //$data = $this->mdboardtraceability->readDataTraceability($this->get('prov'),$this->get('kab'),$this->get('kec'),$this->get('desa'),$this->get('awal'),$this->get('akhir'),$this->get('traceability_partner'),$this->get('mill'),$this->get('do'),$this->get('agent'));
        $data = $this->mdboard->getDataAchieveFA($this->get('awal'),$this->get('akhir'),$this->get('mill'),$this->get('type'));
        // var_dump($data);exit;
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    public function dash_get_traceability_get() {
        ini_set('memory_limit', '-1');
        //$data = $this->mdboardtraceability->readDataTraceability($this->get('prov'),$this->get('kab'),$this->get('kec'),$this->get('desa'),$this->get('awal'),$this->get('akhir'),$this->get('traceability_partner'),$this->get('mill'),$this->get('do'),$this->get('agent'));
        $data = $this->mdboardtraceability->readDataTraceabilityNew($this->get('prov'),$this->get('district'),$this->get('subdistrict'),$this->get('village'),$this->get('awal'),$this->get('akhir'),$this->get('traceability_partner'),$this->get('mill'),$this->get('do'),$this->get('agent'));
        // $data = $this->mdboardtraceability->readDataTraceabilityBaru($this->get('prov'),$this->get('district'),$this->get('subdistrict'),$this->get('village'),$this->get('awal'),$this->get('akhir'),$this->get('traceability_partner'),$this->get('mill'),$this->get('do'),$this->get('agent'));
        // var_dump($data);exit;
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    public function dash_get_traceability_dealer_get() {
        ini_set('memory_limit', '-1');
        //$data = $this->mdboardtraceability->readDataTraceability($this->get('prov'),$this->get('kab'),$this->get('kec'),$this->get('desa'),$this->get('awal'),$this->get('akhir'),$this->get('traceability_partner'),$this->get('mill'),$this->get('do'),$this->get('agent'));
        //$data = $this->mdboardtraceability->readDataTraceabilityNew($this->get('prov'),$this->get('district'),$this->get('subdistrict'),$this->get('village'),$this->get('awal'),$this->get('akhir'),$this->get('traceability_partner'),$this->get('mill'),$this->get('do'),$this->get('agent'));
        $data = $this->mdboardtraceability->readDataTraceabilityBaru($this->get('prov'),$this->get('district'),$this->get('subdistrict'),$this->get('village'),$this->get('awal'),$this->get('akhir'),$this->get('traceability_partner'),$this->get('mill'),$this->get('do'),$this->get('agent'));
        // var_dump($data);exit;
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    public function kpi_koltiva_get(){
        ini_set('memory_limit', '-1');

        $data = array();
        
        $ProvinceID = (int) $this->get('fprovince');

        $data = $this->mdboard->DisplayChartKpiKoltiva($ProvinceID, (int) $this->get('PartnerID'), (int) $this->get('year'));
        $this->response($data, 200);
    }

    public function kpi_sawit_terampil_get(){
        ini_set('memory_limit', '-1');

        $data = array();

        $data = $this->mdboard->DisplayChartSawitTerampil($this->get('lockdate'), $this->get('wave'), $this->get('chdistrict'));
        $this->response($data, 200);
    }

    public function kpi_ksatria_sawit_get(){
        ini_set('memory_limit', '-1');

        $data = array();

        $data = $this->mdboard->DisplayChartKsatriaSawit($this->get('lockdate'), $this->get('wave'), $this->get('chdistrict'));
        $this->response($data, 200);
    }
    
    public function dash_get_traceability_mill_get() {    
        
        //$data = $this->mdboardtraceability->readDataTraceability($this->get('prov'),$this->get('kab'),$this->get('kec'),$this->get('desa'),$this->get('awal'),$this->get('akhir'),$this->get('traceability_partner'),$this->get('mill'),$this->get('do'),$this->get('agent'));
        //$data = $this->mdboardtraceability->readDataTraceabilityMill($this->get('awal'),$this->get('akhir'),$this->get('traceability_partner'),$this->get('mill'));
        $data = $this->mdboardtraceability->readDataTraceabilityMillBaru($this->get());
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    public function dash_supplychain_traceability_get() {
        $data = $this->mdboardtraceability->readDataSupplyChainTraceability($this->get('prov'),$this->get('kab'),$this->get('kec'),$this->get('desa'),$this->get('awal'),$this->get('akhir'),$this->get('traceability_partner'),$this->get('mill'),$this->get('do'),$this->get('agent'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    public function dash_supplychain_mill_traceability_get() {
        //$data = $this->mdboardtraceability->readDataSupplyChainMillTraceability($this->get('millgroup'),$this->get('mill'),$this->get('awal'),$this->get('akhir'));
        $data = $this->mdboardtraceability->readDataTraceabilityMillNew($this->get('millgroup'),$this->get('mill'),$this->get('awal'),$this->get('akhir'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    public function dash_gen_replanting_finance_get() {
        $stat = $this->mdboard_replanting->generateDashReplanting();
        $this->response($stat, 200);
    }

    public function dash_get_replanting_finance_get() {
        $data = $this->mdboard_replanting->getDisplayDashReplanting((int) $this->get('prov'),(int) $this->get('kab'));
        $this->response($data, 200);
    }

    public function dash_gen_household_get() {
        $this->load->model('dboard/mhousehold');
        $stat = $this->mhousehold->generateDash();
        $result['success'] = $stat;
        $this->response($result, 200);
    }

    public function dash_get_household_get(){
        $this->load->model('dboard/mhousehold');
        //cek apakah ada regen
        if($this->get('regen') == "go"){
            $stat = $this->mhousehold->generateDash();
        }

        $data = $this->mhousehold->getDisplay((int) $this->get('prov'),(int) $this->get('kab'));
        $this->response($data, 200);
    }

    /*================ Progress Dashboard ===========================================================*/

    public function dash_gen_pro_kpi_get(){
        ini_set('memory_limit', -1);
        // ini_set('max_execution_time', 0);
        set_time_limit(0);

        $stat = $this->mpro_kpi->generateDashProKpi();
        $result['success'] = $stat;
        $this->response($result, 200);
    }

    public function dash_gen_pro_kpi_target_get() {
        $stat = $this->mpro_kpi->generateDashProKpiTarget();
        $result['success'] = $stat;
        $this->response($result, 200);
    }

    public function dash_get_kpi_get(){
        // $data = $this->mpro_kpi->getDisplayKpi((int) $this->get('prov'),(int) $this->get('kab'));
        $data = $this->mpro_kpi->getDisplayKpi($this->get('country'),(int) $this->get('prov'), (int) $this->get('kab'), (int) $this->get('farm_type'), (int) $this->get('year'));
        $this->response($data, 200);
    }
    
    public function dash_gen_pro_ndpe_get(){
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        $stat = $this->mpro_ndpe->generateDashProNdpe();
        $result['success'] = $stat;
        $this->response($result, 200);
    }

    public function dash_get_pro_ndpe_get(){
        $ftype = filter_var($this->get('ftype'),FILTER_SANITIZE_STRING);
        $fprovince = filter_var($this->get('prov'),FILTER_SANITIZE_STRING);
        $fdistrict = filter_var($this->get('kab'),FILTER_SANITIZE_STRING);

        $data = $this->mpro_ndpe->getDisplayNdpe($fprovince,$fdistrict,$ftype);
        $this->response($data, 200);
    }

    public function dash_gen_pro_surveys_get(){
        $stat = $this->mpro_surveys->generateDashProSurveysOptimize();
        $result['success'] = $stat;
        $this->response($result, 200);
    }

    public function dash_get_pro_surveys_get(){
        $ftype = filter_var($this->get('ftype'),FILTER_SANITIZE_STRING);
        $fprovince = filter_var($this->get('prov'),FILTER_SANITIZE_STRING);
        $fdistrict = filter_var($this->get('kab'),FILTER_SANITIZE_STRING);

        $data = $this->mpro_surveys->getDisplaySurveys($fprovince,$fdistrict,$ftype);
        $this->response($data, 200);
    }

    public function dash_gen_pro_survey_ppi_get(){
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        
        $stat = $this->mpro_survey_ppi->generateDashProSurveyPpi();
        $result['success'] = $stat;
        $this->response($result, 200);
    }

    public function dash_get_pro_ppi_survey_get(){
        $data = $this->mpro_survey_ppi->getDisplaySurveyPpi((int) $this->get('prov'),(int) $this->get('kab'));
        $this->response($data, 200);
    }

    public function dash_gen_pro_master_training_get(){
        $stat = $this->mpro_master_training->generateDashProMasterTraining();
        $result['success'] = $stat;
        $this->response($result, 200);
    }

    public function dash_get_pro_master_training_get(){
        $data = $this->mpro_master_training->getDisplayMasterTraining((int) $this->get('prov'),(int) $this->get('kab'));
        $this->response($data, 200);
    }

    public function dash_get_farmer_training_get(){
        $this->load->model('dboard/mfarmertraining');
        //cek apakah ada regen
        if($this->get('regen') == "go"){
            $stat = $this->mfarmertraining->generateDash();
        }

        $data = $this->mfarmertraining->getDisplay((int) $this->get('prov'),(int) $this->get('kab'));
        $this->response($data, 200);
    }
    
    public function dash_gen_pro_supplychain_kpi_get(){
        $stat = $this->mpro_supplychain_kpi->generateDashProSupplyChainKpi();
        $result['success'] = $stat;
        $this->response($result, 200);
    }

    public function dash_get_supplychain_kpi_get(){
        $data = $this->mpro_supplychain_kpi->getDisplaySupplyChainKpi((int) $this->get('prov'),(int) $this->get('kab'));
        $this->response($data, 200);
    }
    
    public function dash_gen_pro_supplychain_kpi_full_get(){
        $stat = $this->mpro_supplychain_kpi_full->generateDashProSupplyChainKpi();
        $result['success'] = $stat;
        $this->response($result, 200);
    }

    public function dash_get_supplychain_kpi_full_get(){
        $data = $this->mpro_supplychain_kpi_full->getDisplaySupplyChainKpi((int) $this->get('prov'),(int) $this->get('kab'));
        $this->response($data, 200);
    }
    
    public function dash_gen_pro_supplychain_kpi_gar_get(){
        $stat = $this->mpro_supplychain_kpi_gar->generateDashProSupplyChainKpi();
        $result['success'] = $stat;
        $this->response($result, 200);
    }

    public function dash_get_supplychain_kpi_gar_get(){
        $data = $this->mpro_supplychain_kpi_gar->getDisplaySupplyChainKpi((int) $this->get('prov'),(int) $this->get('kab'));
        $this->response($data, 200);
    }

    public function mill_group_get() {
        $millgroup = $this->mpro_supplychain_kpi->getMillGroup();
        $this->response(array(
            'data' => $millgroup
                ), 200);
    }
    
    public function mill_get() {
        $mill = $this->mpro_supplychain_kpi->getMill((int) $this->get('millGroupID'));
        $this->response(array(
            'data' => $mill
                ), 200);
    }

    public function dash_gen_pro_supplychain_mill_kpi_get(){
        $stat = $this->mpro_supplychain_kpi->generateDashProSupplyChainMillKpi();
        $result['success'] = $stat;
        $this->response($result, 200);
    }

    public function dash_get_supplychain_mill_kpi_get(){
        $PartnerIDImp = $this->get('PartnerIDImp');
        $SupChainMillFirstLoad = (int) $this->get('SupChainMillFirstLoad');
        
        if($SupChainMillFirstLoad == 1) {
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

        $data = $this->mpro_supplychain_kpi->getDisplaySupplyChainMillKpiPartner($PartnerIDStr);
        $data['PartnerIDStr'] = $PartnerIDStr;
        $this->response($data, 200);
    }

    public function dash_get_supplychain_mill_kpi_full_get(){
        $PartnerIDImp = $this->get('PartnerIDImp');
        $SupChainMillFirstLoad = (int) $this->get('SupChainMillFirstLoad');
        
        if($SupChainMillFirstLoad == 1) {
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

        $data = $this->mpro_supplychain_kpi->getDisplaySupplyChainMillKpiPartnerFull($PartnerIDStr);
        $data['PartnerIDStr'] = $PartnerIDStr;
        $this->response($data, 200);
    }
    
    public function dash_get_bluenumber_sdg_get() {
        $this->load->model('dboard/mpro_bluenumber_sdg');
        $data = $this->mpro_bluenumber_sdg->getDisplay((int) $this->get('prov'),(int) $this->get('kab'));
        $this->response($data, 200);
    }

    public function dash_bluenumber_export_excel_post() {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        $result = array();

        //curl
        $url = $this->config->item('base_url_util').'dash_bnsdg';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $arrResult = json_decode($result, true);
        if( ($arrResult['success'] == true) && file_exists('files/tmp/'.$arrResult['filename']) ) {
            $this->response(array('success' => TRUE, 'filenya' => base_url() . 'files/tmp/'.$arrResult['filename']), 200);
        } else {
            $this->response(array('success' => FALSE, 'message' => lang('Export Excel Failed')), 400);
        }
    }

    public function spchain_mill_kpi_combo_get() {
        $PartnerID = (int) $this->get('partner');
        $htmlreturn = '<option value=""></option>';
        
        //Get Info Partner dia sendiri
        $SelfPartner = $this->mpro_supplychain_kpi->GetInfoPartner($PartnerID);
        if($SelfPartner['id'] != "") {
            $htmlreturn .= '<option value="'.$SelfPartner['id'].'">'.$SelfPartner['label'].'</option>';
        }

        //Ambil Partner bawahannya dia
        $optionhtml = "";
        $increRekur = 0;
        $HirarPartner = $this->mpro_supplychain_kpi->GetHirarPartner($PartnerID,$optionhtml,$increRekur);
        $htmlreturn = $htmlreturn.$HirarPartner;

        $data['success'] = true;
        $data['htmlnya'] = $htmlreturn;
        $this->response($data,200);
    }

    public function grid_partner_hirar_get() {
        $PartnerID = (int) $this->get('PartnerID');
        $data = $this->mpro_supplychain_kpi->GetPanelGridPartnerHirar($PartnerID);
        $this->response($data, 200);
    }

    public function dash_gen_pro_supplychain_mill_kpi_new_get() {
        $stat = $this->mpro_supplychain_kpi->GenerateDashProSupplyChainMillKpiNew();
        $result['success'] = $stat;
        $this->response($result, 200);
    }

    public function dash_get_mill_get(){
        $data = $this->mdboard_mill->getDisplayMill((int) $this->get('mill'),(int) $this->get('millGroup'), $this->get('startdate'), $this->get('enddate'));

        $this->response($data, 200);
    }

    public function dash_get_refinery_get(){

        $data = $this->mdboard_refinery->getDisplayRefinery((int) $this->get('mill'), (int) $this->get('idMill'), $this->get('startdate'), $this->get('enddate'));

        $this->response($data, 200);
    }

    public function combo_filter_year_dash_kpi_get() {
        $data = $this->mpro_kpi->GetComboFilterYearDashKpi();
        $this->response($data, 200);
    }

    public function combo_filter_year_dash_kpi_sawit_terampil_get() {
        $data = $this->mpro_kpi->GetComboFilterYearDashKpiSawitTerampil();
        $this->response($data, 200);
    }

    public function combo_filter_lock_date_sawit_get(){
        $data = $this->mpro_kpi->GetLockDateSawit($this->get('wave'));
        $this->response($data, 200);
    }

    public function combo_filter_lock_date_ks_get(){
        $data = $this->mpro_kpi->GetLockDateKS($this->get('wave'));
        $this->response($data, 200);
    }

    public function combo_filter_wave_ks_get(){
        $data = $this->mpro_kpi->GetWaveKS();
        $this->response($data, 200);
    }

    public function combo_filter_wave_sawit_get(){
        $data = $this->mpro_kpi->GetWaveSawit();
        $this->response($data, 200);
    }

    public function default_wave_ks_get() {
        $wave = $this->mpro_kpi->getDefaultWaveKsatriaSawit();
        $this->response(array('data' => $wave), 200);
    }

    public function default_wave_sawit_get() {
        $wave = $this->mpro_kpi->getDefaultWaveSawit();
        $this->response(array('data' => $wave), 200);
    }

    public function store_transaction_farmer_get()
	{  
		//$data = $this->mdboardtraceability->Gettransactionfarmer($this->get());
        $data = $this->mdboardtraceability->GettransactionfarmerBaru($this->get());
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    public function store_transaction_farmer_export_excel_get() {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        
        //$getTransactionFarmer = $this->mdboardtraceability->GetTransactionFarmerExportBaru($this->get());
        $get = $this->get();
        $get['page'] = 1;
        $get['start'] = 0;
        $get['limit'] = 1000000;
        $get['sort'] = "%5B%7B%22property%22%3A%22MemberDisplayID%22%2C%22direction%22%3A%22DESC%22%7D%5D";
        $getTransactionFarmer = $this->mdboardtraceability->GettransactionfarmerBaru($get);

        //ambil data  (begin)
        $dataList = $getTransactionFarmer['data'];
        $dataKolom = $getTransactionFarmer['data'];
        
        $writer = WriterEntityFactory::createXLSXWriter();
        $namaFile = date('YmdHis') . '_export_dashboard.xlsx';
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

        $styleTitle = (new StyleBuilder())
            ->setFontBold()
            ->setFontSize(12)
            ->setFontColor(Color::BLUE)
            ->build();

       
        //style
        $styleHeader = (new StyleBuilder())
            ->setFontColor(Color::WHITE)
            ->setBorder($borderDefa)
            ->setBackgroundColor(Color::GREEN)
            ->build();
    
        $dataHeader = array(
            'No'
            ,lang('MemberDisplayID')
            ,lang('SupplierName')
            ,lang('District')
            ,lang('SubDistrict')
            ,lang('Village')
            ,lang('Estimated Annual Production (kg)')
            ,lang('Total FFB Received (kg)')
        );

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
            ->setFormat('d-mmm-YY')
            ->build();

        $no = 1;
        //row data
        foreach($dataList as $k=>$v){
            $cells = array();
            $cells = [
                WriterEntityFactory::createCell((float) $no, $styleFormatAngka),
                WriterEntityFactory::createCell( $v['MemberDisplayID'], $styleData),
                WriterEntityFactory::createCell( $v['SupplierName'], $styleData),
                WriterEntityFactory::createCell( $v['District'], $styleData),
                WriterEntityFactory::createCell( $v['SubDistrict'], $styleData),
                WriterEntityFactory::createCell( $v['Village'], $styleData),
                WriterEntityFactory::createCell( $v['Production'], $styleFormatAngka),
                WriterEntityFactory::createCell( $v['VolumeNetto'], $styleFormatAngka)
            ];

            $rowData = WriterEntityFactory::createRow($cells);
            $writer->addRow($rowData);
            $no++;
        }

        $writer->close();

        $base_url = str_replace('koltiva/','',base_url());
        $this->response(array('success' => TRUE, 'filenya' => $base_url . $filePath), 200);
        exit;
        
    }

    public function store_transaction_supplier_get()
	{  
		$data = $this->mdboardtraceability->GettransactionSupplier($this->get());
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    public function store_transaction_supplier_new_get()
    {  
        $data = $this->mdboardtraceability->GettransactionSupplierNew($this->get());
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    public function store_transaction_supplier_export_excel_get() {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        
        $getTransactionSupplier = $this->mdboardtraceability->GettransactionSupplierNew($this->get());

        //ambil data  (begin)
        $dataList = $getTransactionSupplier['data'];
        $dataKolom = $getTransactionSupplier['data'];
        
        $writer = WriterEntityFactory::createXLSXWriter();
        $namaFile = date('YmdHis') . '_export_dashboard.xlsx';
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

        $styleTitle = (new StyleBuilder())
            ->setFontBold()
            ->setFontSize(12)
            ->setFontColor(Color::BLUE)
            ->build();

       
        //style
        $styleHeader = (new StyleBuilder())
            ->setFontColor(Color::WHITE)
            ->setBorder($borderDefa)
            ->setBackgroundColor(Color::GREEN)
            ->build();
    
        $dataHeader = array(
            'No'
            ,'Supplier ID'
            ,'Supplier Name'
            ,'District'
            ,'Sub District'
            ,'Totalffbreceived (MT)'        
        );

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
            ->setFormat('d-mmm-YY')
            ->build();

        $no = 1;
        //row data

        //echo "<pre>".print_r($dataList, 1);die;

        foreach($dataList as $k=>$v){
            $cells = array();
            
            $cells = [
                WriterEntityFactory::createCell((float) $no, $styleFormatAngka),
                WriterEntityFactory::createCell( $v['SupplierID'], $styleData),
                WriterEntityFactory::createCell( $v['SupplierName'], $styleData),
                WriterEntityFactory::createCell( $v['District'], $styleData),
                WriterEntityFactory::createCell( $v['SubDistrict'], $styleData),
                WriterEntityFactory::createCell( round((floatval($v['totalffbreceived'])/1000), 2), $styleFormatAngka)
            ];

            $rowData = WriterEntityFactory::createRow($cells);
            $writer->addRow($rowData);
            $no++;
        }

        $writer->close();

        $base_url = str_replace('koltiva/','',base_url());
        $this->response(array('success' => TRUE, 'filenya' => $base_url . $filePath), 200);
        exit;
        
    }
}