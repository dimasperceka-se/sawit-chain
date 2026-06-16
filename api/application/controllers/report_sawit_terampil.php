<?php defined('BASEPATH') or exit('No direct script access allowed');

class Report_sawit_terampil extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('report/mreport_sawit_terampil');
        //$this->load->library('zip');
    }

    public function wave_jb_get() {
        $wave = $this->mreport_sawit_terampil->getWaveJB();
        $this->response(array('data' => $wave), 200);
    }

    public function combo_monthyears_get()
    {
        $pfilter = array(
            'showProcessDate'   => $this->get('showProcessDate')
        );
        $data = $this->mreport_sawit_terampil->ReadComboMonthYear($pfilter);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }
    public function combo_monthyears_put() {
        if (!$this->put('processId'))
            $this->response(NULL, 400);
			$proccesdt = $this->mreport_sawit_terampil->updateProcessDate($this->put('processId'), $this->put('ReportStatus'), $this->put('ReportName'), $_SESSION['userid']);
        if ($proccesdt)
            $this->response($proccesdt, 200);
        else
            $this->response(array('error' => 'Process Date could not be found'), 404);
    }

    public function cmb_store_procedure_sawit_terampil_get() {
        if (!$this->get('ProgID'))
            $this->response(NULL, 400);
        $procces = $this->mreport_sawit_terampil->getCmbStoreProcedureSawitTerampil((int)$this->get('ProgID'));
        $this->response($procces, 200);
    }

    public function calculate_sawit_grid_main_get() {
        $sorting = json_decode($this->get('sort'));
        if (isset($sorting[0]->property))
            $sortingField = $sorting[0]->property;
        else
            $sortingField = null;
        if (isset($sorting[0]->direction))
            $sortingDir = $sorting[0]->direction;
        else
            $sortingDir = null;
        if (!$this->get('ProgID')){
            $proses['success'] = false;
            $proses['message'] = lang('Please Select ProgID');
            $this->response($proses, 400);
        }
        
        $data = $this->mreport_sawit_terampil->GridMainCalculateSawit((int)$this->get('ProgID'), $sortingField, $sortingDir);
        $this->response($data, 200);
    }
    
    public function calculate_sawit_dinamis_post() {
        $ProgID = (int) $this->post('ProgID');
        $DateProcess = date('Y-m-d', strtotime($this->post('DateProcess')));
        $StoreProcedureName = $this->post('StoreProcedureName');
        $data = $this->mreport_sawit_terampil->CalculateSawitDinamis($ProgID, $DateProcess, $StoreProcedureName);
        $this->response($data, 200);
    }
    // public function combo_years_get()
    // {
    //     $data = $this->mreport_sawit_terampil->ReadComboYear();
    //     if ($data) {
    //         $this->response($data, 200);
    //     } else {
    //         $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
    //     }
    // }
    // public function combo_months_get()
    // {
    //     // $PartnerID = $_SESSION['PartnerID']=='' ? $_SESSION['SupplychainPartnerID'] : $_SESSION['PartnerID'];
    //     $data = $this->mreport_sawit_terampil->ReadComboMonth();
    //     if ($data) {
    //         $this->response($data, 200);
    //     } else {
    //         $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
    //     }
    // }
    public function sawit_terampil_main_grid_get(){
        $pSearch = array(
            // 'filterYears'                    => $this->get('filterYears'),
            // 'filterMonths'                    => $this->get('filterMonths'),
            'filterMonthYears'                    => $this->get('filterMonthYears')
        );
        $data = $this->mreport_sawit_terampil->GetSawitTerampilMainGrid($pSearch,$this->get('start'), $this->get('limit'));
        $this->response($data, 200);
    }
    
    public function classification_get(){
        $data = $this->mreport_sawit_terampil->GetClassification();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any api!'), 404);
        }
    }
    public function certification_progress_excel2_get($fDate)
    {
        $data = $this->mreport_sawit_terampil->getCertificationProgress($fDate);
        $filename = "jbcocoa_utz_certification_progress_report_".$data['fDateNm']."_".date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $data['fDate'] = $fDate;
        $data['border']   = 1;
        // return $data;
        $this->load->view('report_certification_progress', $data);
    }
    public function certification_detail_progress_excel2_get($fDate,$fClass)
    {   
        $data = $this->mreport_sawit_terampil->getCertificationDetailProgress($fDate,$fClass);
        $filename = $fClass."_".$data['fDateNm']."_". date('Ymd') . ".xlsx";        
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');
       
        $data['fDate'] = $fDate;
        $data['fClass'] = $fClass;
        $data['border']   = 1;
        
        $this->load->view('report_certification_detail_progress', $data);
    }

    public function certification_detail_progress_excel_post()
    {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        $fDate = $this->post('filterMonthYears');
        $fClass = $this->post('filterClassification');

        //======= start get data =====
        $data = $this->mreport_sawit_terampil->getCertificationDetailProgress($fDate,$fClass);
        //echo '<pre>'; print_r($data); exit;
        //======= end get data =====
        
        $data['fDate'] = $fDate;
        $data['fClass'] = $fClass;
        
        // $this->load->view('report_certification_detail_progress_xml', $data);
        $tmp = $this->load->view('report_certification_detail_progress_xml', $data, TRUE);
        $filename = $data["fClass"]."_".$data["fDateNm"]."_". date('Ymd');

        $filenamepath = 'files/export/'.$filename.".xls";
        $filenamepath = filter_var($filenamepath,FILTER_SANITIZE_STRING);
        file_put_contents($filenamepath,$tmp);

        //ngezip (begin)
            // Initialize archive object
            $zip = new ZipArchive();
            $zip->open('files/export/'.$filename.'.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

            // Add current file to archive
            $zip->addFile('files/export/'.$filename.".xls",$filename.".xls");
            $zip->close();
        //ngezip (end)

        $proses['success'] = true;
        $proses['filedl'] = '/api/files/export/'.$filename.'.zip';
        $this->response($proses, 200);

        //===========================================================
        // $this->zip->add_data($filename. ".xls", $tmp);
        // $this->zip->archive('files/rptutz/'.$filename.'.zip');
        // $this->zip->download($filename.'.zip');
    }

    public function certification_progress_excel_post()
    {
        $fDate = $this->post('filterMonthYears');

        //======= start get data =====
        $data = $this->mreport_sawit_terampil->getCertificationProgress($fDate);
        //======= end get data =====
        $data['fDate'] = $fDate;
        $data['border']   = 1;
        
        // $this->load->view('report_certification_progress_xml', $data);
        $tmp = $this->load->view('report_certification_progress_xml', $data, TRUE);
        $filename = "jbcocoa_utz_certification_progress_report_".$data["fDateNm"]."_".date('Ymd');

        $filenamepath = 'files/export/'.$filename.".xls";
        $filenamepath = filter_var($filenamepath,FILTER_SANITIZE_STRING);
        file_put_contents($filenamepath,$tmp);

        //ngezip (begin)
            // Initialize archive object
            $zip = new ZipArchive();
            $zip->open('files/export/'.$filename.'.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

            // Add current file to archive
            $zip->addFile('files/export/'.$filename.".xls",$filename.".xls");
            $zip->close();
        //ngezip (end)

        $proses['success'] = true;
        $proses['filedl'] = '/api/files/export/'.$filename.'.zip';
        $this->response($proses, 200);
    }

    public function do_kpicalculation_post()
    {   ini_set('memory_limit', '-1');
        $args = array(
            'calcGranted'   => $this->post('calcGranted')
        );

        echo "<pre>";
        print_r($this->post());
        die;
        if($this->post('noCon')==1){
            $data = $this->mreport_sawit_terampil->doKPICalculation1($args);
        } else if($this->post('noCon')==2){
            $data = $this->mreport_sawit_terampil->doKPICalculation2($args);
        } else if($this->post('noCon')==3){
            $data = $this->mreport_sawit_terampil->doKPICalculation3($args);
        } else if($this->post('noCon')==4){
            $data = $this->mreport_sawit_terampil->doKPICalculation4($args);
        } else if($this->post('noCon')==5){
            $data = $this->mreport_sawit_terampil->doKPICalculation5($args);
        } else if($this->post('noCon')==6){
            $data = $this->mreport_sawit_terampil->doKPICalculation6($args);
        } else if($this->post('noCon')==7){
            $data = $this->mreport_sawit_terampil->doKPICalculation7($args);
        } else if($this->post('noCon')==8){
            $data = $this->mreport_sawit_terampil->doKPICalculation8($args);
        } else if($this->post('noCon')==9){
            $data = $this->mreport_sawit_terampil->doKPICalculation9($args);
        } else if($this->post('noCon')==10){
            $data = $this->mreport_sawit_terampil->doKPICalculation10($args);
        }
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }
} //end of class
