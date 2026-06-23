<?php
/**
 * @Author: nikolius
 * @Date:   2016-09-27 16:57:44
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_traceability extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->file = $_FILES;
        $this->load->model('report/mreport_traceability_transaction');
    }
    
    
    public function store_grid_mill_transaction_get(){

        $sorting = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $pSearch = array(
            "Role" => $this->get('Role'),
            "StringNameUsername" => $this->get('StringNameUsername')
        );
            
        $data = $this->mreport_traceability_transaction->readStoreGridTransactionMill($pSearch,$this->get('start'),$this->get('limit'),$sortingField,$sortingDir,
            $this->get('MillID'), $this->get('DOID'), $this->get('AgentID'), $this->get('DateFrom'), $this->get('DateTo')
        );
        $this->response($data, 200);
    }
    
    public function store_grid_do_transaction_get(){

        $sorting = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $pSearch = array(
            "Role" => $this->get('Role'),
            "StringNameUsername" => $this->get('StringNameUsername')
        );
            
        $data = $this->mreport_traceability_transaction->readStoreGridTransactionDO($pSearch,$this->get('start'),$this->get('limit'),$sortingField,$sortingDir,
            $this->get('MillID'), $this->get('DOID'), $this->get('AgentID'), $this->get('DateFrom'), $this->get('DateTo')
        );
        $this->response($data, 200);
    }

    protected function store_grid_do_transaction_Excellmill_get() {
    
        require_once 'application/third_party/PHPExcel18/PHPExcel.php';
        // membuat obyek dari class PHPExcel
        $objPHPExcel = new PHPExcel();
        // memberi nama sheet pertama dengan nama 'Sheet 1'   
                    //tabel header

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'REPORT.');
        $objPHPExcel->getActiveSheet()->setCellValue('A2', 'TRANSACTION MILL');            
        $objPHPExcel->getActiveSheet()->setCellValue('A3', 'No.');
        $objPHPExcel->getActiveSheet()->setCellValue('B3', 'Type');
        $objPHPExcel->getActiveSheet()->setCellValue('C3', 'Distrik');
        $objPHPExcel->getActiveSheet()->setCellValue('D3', 'Sub Distrik');
        $objPHPExcel->getActiveSheet()->setCellValue('E3', 'SME ID');
        $objPHPExcel->getActiveSheet()->setCellValue('F3', 'Name');
        $objPHPExcel->getActiveSheet()->setCellValue('G3', 'Date');
        $objPHPExcel->getActiveSheet()->setCellValue('H3', 'Gross');
        $objPHPExcel->getActiveSheet()->setCellValue('I3', 'Nett');
        $objPHPExcel->getActiveSheet()->setCellValue('J3', 'Supply ID');
        $objPHPExcel->getActiveSheet()->setCellValue('K3', 'Delivery Date');
        $objPHPExcel->getActiveSheet()->setCellValue('L3', 'Batch From');
        $objPHPExcel->getActiveSheet()->setCellValue('M3', 'SME Batch ID');
        $objPHPExcel->getActiveSheet()->setCellValue('N3', 'Status');
        $objPHPExcel->getSheet(0)->setTitle('Mill');

        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);

                
        $styleArray1 = array(
            'font'  => array(
            'bold'  => true,
            'size'  => 18
            ),
            'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ));
        
        $objPHPExcel->getActiveSheet()->getStyle('A1:N2')->applyFromArray($styleArray1);    
                
        $styleArray = array(
            'font'  => array(
            'bold'  => true,
            'size'  => 12
            ),
            'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ));
        
        $objPHPExcel->getActiveSheet()->getStyle('A3:N3')->applyFromArray($styleArray);    

        $data = $this->mreport_traceability_transaction->readStoreGridTransactionMill_Excell($this->get('start'),$this->get('limit'),$sortingField,$sortingDir,$this->get('MillID'), $this->get('DOID'), $this->get('AgentID'), $this->get('DateFrom'), $this->get('DateTo'));

        $iaktiva = 4;
        $Number  = 1;
        //  looping isi

        foreach($data['data'] as $key => $transaction){
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$iaktiva,$Number);    
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$iaktiva,$transaction['SupplyType']);
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$iaktiva,$transaction['District']);    
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$iaktiva,$transaction['SubDistrict']);
        $objPHPExcel->getActiveSheet()->setCellValue('E'.$iaktiva,$transaction['FarmerID']);
        $objPHPExcel->getActiveSheet()->setCellValue('F'.$iaktiva,$transaction['Name']);
        $objPHPExcel->getActiveSheet()->setCellValue('G'.$iaktiva,$transaction['DateTransaction']);
        $objPHPExcel->getActiveSheet()->setCellValue('H'.$iaktiva,$transaction['Bruto']);
        $objPHPExcel->getActiveSheet()->setCellValue('I'.$iaktiva,$transaction['Netto']);
        $objPHPExcel->getActiveSheet()->setCellValue('J'.$iaktiva,$transaction['SupplyID']);
        $objPHPExcel->getActiveSheet()->setCellValue('K'.$iaktiva,$transaction['DeliveryDate']);
        $objPHPExcel->getActiveSheet()->setCellValue('L'.$iaktiva,$transaction['BatchFrom']);
        $objPHPExcel->getActiveSheet()->setCellValue('M'.$iaktiva,$transaction['AgentBatchID']);
        $objPHPExcel->getActiveSheet()->setCellValue('N'.$iaktiva,$transaction['SupplyBatchStatus']);
        $iaktiva++;
        $Number++;
        } 
       
        // output file dengan nama file 'contoh.xls'
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="ReportTransactionMill.xls"');
        header('Cache-Control: max-age=0');
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }


    protected function store_grid_do_transaction_Excelldo_get() {
    
        require_once 'application/third_party/PHPExcel18/PHPExcel.php';
        // membuat obyek dari class PHPExcel
        $objPHPExcel = new PHPExcel();
        // memberi nama sheet pertama dengan nama 'Sheet 1'   
                    //tabel header

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'REPORT.');
        $objPHPExcel->getActiveSheet()->setCellValue('A2', 'TRANSACTION DO');            
        $objPHPExcel->getActiveSheet()->setCellValue('A3', 'No.');
        $objPHPExcel->getActiveSheet()->setCellValue('B3', 'Type');        
        $objPHPExcel->getActiveSheet()->setCellValue('C3', 'Distrik');
        $objPHPExcel->getActiveSheet()->setCellValue('D3', 'Sub Distrik');
        $objPHPExcel->getActiveSheet()->setCellValue('E3', 'DO ID');
        $objPHPExcel->getActiveSheet()->setCellValue('F3', 'Batch Number');
        $objPHPExcel->getActiveSheet()->setCellValue('G3', 'Name');
        $objPHPExcel->getActiveSheet()->setCellValue('H3', 'Date');
        $objPHPExcel->getActiveSheet()->setCellValue('I3', 'Gross');
        $objPHPExcel->getActiveSheet()->setCellValue('J3', 'Nett');
        $objPHPExcel->getActiveSheet()->setCellValue('K3', 'Supply ID');
        $objPHPExcel->getActiveSheet()->setCellValue('L3', 'Delivery Date');
        $objPHPExcel->getActiveSheet()->setCellValue('M3', 'Batch From');
        $objPHPExcel->getActiveSheet()->setCellValue('N3', 'Status');
        $objPHPExcel->getActiveSheet()->setCellValue('O3', 'Sent To');

        $objPHPExcel->getSheet(0)->setTitle('DO');

        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(35);

                
        $styleArray1 = array(
            'font'  => array(
            'bold'  => true,
            'size'  => 18
            ),
            'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ));
        
        $objPHPExcel->getActiveSheet()->getStyle('A1:O2')->applyFromArray($styleArray1);    
                
        $styleArray = array(
            'font'  => array(
            'bold'  => true,
            'size'  => 12
            ),
            'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ));
        
        $objPHPExcel->getActiveSheet()->getStyle('A3:O3')->applyFromArray($styleArray);    

        $data = $this->mreport_traceability_transaction->readStoreGridTransactionDO_Excell($this->get('start'),$this->get('limit'),$sortingField,$sortingDir,$this->get('MillID'), $this->get('DOID'), $this->get('AgentID'), $this->get('DateFrom'), $this->get('DateTo'));

        $iaktiva = 4;
        $Number  = 1;
        //  looping isi

        foreach($data['data'] as $key => $transaction){
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$iaktiva,$Number);    
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$iaktiva,$transaction['SupplyType']);
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$iaktiva,$transaction['District']);    
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$iaktiva,$transaction['SubDistrict']);
        $objPHPExcel->getActiveSheet()->setCellValue('E'.$iaktiva,$transaction['DoID']); 
        $objPHPExcel->getActiveSheet()->setCellValue('F'.$iaktiva,$transaction['SupplyBatchNumber']);
        $objPHPExcel->getActiveSheet()->setCellValue('G'.$iaktiva,$transaction['Name']);
        $objPHPExcel->getActiveSheet()->setCellValue('H'.$iaktiva,$transaction['DateTransaction']);
        $objPHPExcel->getActiveSheet()->setCellValue('I'.$iaktiva,$transaction['Bruto']);
        $objPHPExcel->getActiveSheet()->setCellValue('J'.$iaktiva,$transaction['Netto']);
        $objPHPExcel->getActiveSheet()->setCellValue('K'.$iaktiva,$transaction['SupplyID']);
        $objPHPExcel->getActiveSheet()->setCellValue('L'.$iaktiva,$transaction['DeliveryDate']);
        $objPHPExcel->getActiveSheet()->setCellValue('M'.$iaktiva,$transaction['BatchFrom']);
        $objPHPExcel->getActiveSheet()->setCellValue('N'.$iaktiva,$transaction['SupplyBatchStatus']);
        $objPHPExcel->getActiveSheet()->setCellValue('O'.$iaktiva,$transaction['BatchTo']);
        $iaktiva++;
        $Number++;
        } 
       
        // output file dengan nama file 'contoh.xls'
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="ReportTransactionDO.xls"');
        header('Cache-Control: max-age=0');
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }


    protected function store_grid_do_transaction_Excellagent_get() {
    
        require_once 'application/third_party/PHPExcel18/PHPExcel.php';
        // membuat obyek dari class PHPExcel
        $objPHPExcel = new PHPExcel();
        // memberi nama sheet pertama dengan nama 'Sheet 1'   
                    //tabel header

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'REPORT.');
        $objPHPExcel->getActiveSheet()->setCellValue('A2', 'TRANSACTION AGENT');            
        $objPHPExcel->getActiveSheet()->setCellValue('A3', 'No.');
        $objPHPExcel->getActiveSheet()->setCellValue('B3', 'Type');              
        $objPHPExcel->getActiveSheet()->setCellValue('C3', 'Distrik');
        $objPHPExcel->getActiveSheet()->setCellValue('D3', 'Sub Distrik');
        $objPHPExcel->getActiveSheet()->setCellValue('E3', 'SME ID');
        $objPHPExcel->getActiveSheet()->setCellValue('F3', 'Batch Number');
        $objPHPExcel->getActiveSheet()->setCellValue('G3', 'Name');
        $objPHPExcel->getActiveSheet()->setCellValue('H3', 'Date');
        $objPHPExcel->getActiveSheet()->setCellValue('I3', 'Gross');
        $objPHPExcel->getActiveSheet()->setCellValue('J3', 'Nett');
        $objPHPExcel->getActiveSheet()->setCellValue('K3', 'Supply ID');
        $objPHPExcel->getActiveSheet()->setCellValue('L3', 'Delivery Date');
        $objPHPExcel->getActiveSheet()->setCellValue('M3', 'Batch From');
        $objPHPExcel->getActiveSheet()->setCellValue('N3', 'DO Batch ID');
        $objPHPExcel->getActiveSheet()->setCellValue('O3', 'Status');
        $objPHPExcel->getActiveSheet()->setCellValue('P3', 'Sent To');
        $objPHPExcel->getSheet(0)->setTitle('Agent');

        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(35);

        $styleArray1 = array(
            'font'  => array(
            'bold'  => true,
            'size'  => 18
            ),
            'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ));
        
        $objPHPExcel->getActiveSheet()->getStyle('A1:P2')->applyFromArray($styleArray1);    
                
        $styleArray = array(
            'font'  => array(
            'bold'  => true,
            'size'  => 12
            ),
            'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ));

        $objPHPExcel->getActiveSheet()->getStyle('A3:P3')->applyFromArray($styleArray);

        $data = $this->mreport_traceability_transaction->readStoreGridTransactionAgent_Excell($this->get('start'),$this->get('limit'),$sortingField,$sortingDir,$this->get('MillID'), $this->get('DOID'), $this->get('AgentID'), $this->get('DateFrom'), $this->get('DateTo'));

        $iaktiva = 4;
        $Number  = 1;
        //  looping isi
        
        foreach($data['data'] as $key => $transaction){
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$iaktiva,$Number);    
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$iaktiva,$transaction['SupplyType']);
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$iaktiva,$transaction['District']);    
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$iaktiva,$transaction['SubDistrict']);
        $objPHPExcel->getActiveSheet()->setCellValue('E'.$iaktiva,$transaction['AgentID']);
        $objPHPExcel->getActiveSheet()->setCellValue('F'.$iaktiva,$transaction['SupplyBatchNumber']);
        $objPHPExcel->getActiveSheet()->setCellValue('G'.$iaktiva,$transaction['Name']);
        $objPHPExcel->getActiveSheet()->setCellValue('H'.$iaktiva,$transaction['DateTransaction']);
        $objPHPExcel->getActiveSheet()->setCellValue('I'.$iaktiva,$transaction['Bruto']);
        $objPHPExcel->getActiveSheet()->setCellValue('J'.$iaktiva,$transaction['Netto']);
        $objPHPExcel->getActiveSheet()->setCellValue('K'.$iaktiva,$transaction['SupplyID']);
        $objPHPExcel->getActiveSheet()->setCellValue('L'.$iaktiva,$transaction['DeliveryDate']);
        $objPHPExcel->getActiveSheet()->setCellValue('M'.$iaktiva,$transaction['BatchFrom']);
        $objPHPExcel->getActiveSheet()->setCellValue('N'.$iaktiva,$transaction['DoBatchID']);
        $objPHPExcel->getActiveSheet()->setCellValue('O'.$iaktiva,$transaction['SupplyBatchStatus']);
        $objPHPExcel->getActiveSheet()->setCellValue('P'.$iaktiva,$transaction['BatchTo']);
        $iaktiva++;
        $Number++;
        } 
       
        // output file dengan nama file 'contoh.xls'
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="ReportTransactionAgent.xls"');
        header('Cache-Control: max-age=0');
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }
    
    
    public function store_grid_agent_transaction_get(){

        $sorting = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $pSearch = array(
            "Role" => $this->get('Role'),
            "StringNameUsername" => $this->get('StringNameUsername')
        );
            
        $data = $this->mreport_traceability_transaction->readStoreGridTransactionAgent($pSearch,$this->get('start'),$this->get('limit'),$sortingField,$sortingDir,
            $this->get('MillID'), $this->get('DOID'), $this->get('AgentID'), $this->get('DateFrom'), $this->get('DateTo')
        );
        $this->response($data, 200);
    }
    
    ////////////////////////////////////////////////////////////
    
    function set_get(){
        $proses = $this->mreport_traceability_transaction->readTransactionSetting();
        if ($proses){
            $this->response($proses, 200);
        }else{
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }

    public function farmer_grid_get(){
        $sorting = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $pSearch = array(
            "Role" => $this->get('Role'),
            "StringNameUsername" => $this->get('StringNameUsername')
        );

        $data = $this->mreport_traceability_transaction->readFarmerGridList($this->get('TransID'),$pSearch,$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        $this->response($data, 200);
    }

    function comboFarmer_get(){
        $proses = $this->mreport_traceability_transaction->readComboFarmer();
        if ($proses){
            $this->response($proses, 200);
        }else{
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }
    
    function comboAgent_get(){
        $proses = $this->mreport_traceability_transaction->readComboAgent();
        if ($proses){
            $this->response($proses, 200);
        }else{
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }

    function combomill_get(){
        $proses = $this->mreport_traceability_transaction->readComboMill();
        if ($proses){
            $this->response($proses, 200);
        }else{
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }

    function comboDO_get(){
        $proses = $this->mreport_traceability_transaction->readComboDO();
        if ($proses){
            $this->response($proses, 200);
        }else{
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }


  
    public function transaction_grid_get(){

        $sorting = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $pSearch = array(
            "Role" => $this->get('Role'),
            "StringNameUsername" => $this->get('StringNameUsername')
        );

        $data = $this->mreport_traceability_transaction->readTransactionGridList($pSearch,$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        $this->response($data, 200);
    }
    
    function supplyid_get() {
        $key = $this->get('query');
        $tipe = $this->get('tipe');
        $start = $this->get('start');
        $limit = $this->get('limit');
        $data = $this->mreport_traceability_transaction->readSupplyIDList($key, $tipe, $start, $limit);
        if ($data){
            $this->response($data, 200);
        }else{
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }    
    }
    
    function transaction_post(){
        $proses = $this->mreport_traceability_transaction->createTransaction(
                $this->post('SupplyTransID'), 
                $this->post('SupplyType'), 
                $this->post('SupplyID'), 
                $this->post('DateTransaction'),
                $this->post('1stDateWeight'),
                $this->post('1stTimeWeight'),
                $this->post('2ndDateWeight'),
                $this->post('2ndTimeWeight'),
                $this->post('1stWeight'),
                $this->post('2ndWeight'),
                $this->post('Tandan'),
                $this->post('AdjustWeight1'),
                $this->post('AdjustWeight2'),
                $this->post('AdjustNetto'),
                $this->post('Price'),
                $this->post('TotalPayment'),
                $this->post('FakturNumber'),
                $_SESSION['userid']);
        if ($proses){
            $this->response($proses, 200);
        }else{
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }

    function transaction_detail_get(){
        $proses = $this->mreport_traceability_transaction->readTransactionDetail($this->get('SupplyTransID'));
        if ($proses){
            $this->response($proses, 200);
        }else{
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }
    
    function transaction_put(){
        $proses = $this->mreport_traceability_transaction->updateTransaction(
                $this->put('SupplyTransID'), 
                $this->put('SupplyType'), 
                $this->put('SupplyID'), 
                $this->put('DateTransaction'),
                $this->put('1stDateWeight'),
                $this->put('1stTimeWeight'),
                $this->put('2ndDateWeight'),
                $this->put('2ndTimeWeight'),
                $this->put('1stWeight'),
                $this->put('2ndWeight'),
                $this->put('Tandan'),
                $this->put('AdjustWeight1'),
                $this->put('AdjustWeight2'),
                $this->put('AdjustNetto'),
                $this->put('Price'),
                $this->put('TotalPayment'),
                $this->put('FakturNumber'),
                $_SESSION['userid']);
        if ($proses){
            $this->response($proses, 200);
        }else{
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }
    
    function batch_post(){
        $proses = $this->mreport_traceability_transaction->createBatch($this->post('SupplychainID'));
        if ($proses){
            $this->response($proses, 200);
        }else{
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }
    
    function batch_delete(){
        $proses = $this->mreport_traceability_transaction->deleteBatch($this->delete('SupplyBatchID'));
        if ($proses){
            $this->response($proses, 200);
        }else{
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }
    
    function batch_put(){
        $proses = $this->mreport_traceability_transaction->updateBatch(
            $this->put('SupplyBatchID'), 
            $this->put('SupplyBatchNumber'), 
            $this->put('DestPO'), 
            $this->put('SupplyBatchDate'), 
            $this->put('SupplyBatchTime'), 
            $this->put('DeliveryDate'), 
            $this->put('DeliveryTime'), 
            $this->put('EstimatedDate'), 
            $this->put('EstimatedTime'),
            $this->put('VolumeBruto'),
            $this->put('VolumeNetto'),
            $this->put('SupplyDestOrgID'),
            $this->put('DestWeight'),
            $this->put('DestNumberPackage'),
            $this->put('DestDriver'),
            $this->put('DestDriverAddress'),
            $this->put('DestDriverhandphone'),
            $this->put('DestNoPolisi')
        );
        if ($proses){
            $this->response($proses, 200);
        }else{
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }
    
    public function transaction_available_get(){
        $sorting = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $pSearch = array(
            "Role" => $this->get('Role'),
            "StringNameUsername" => $this->get('StringNameUsername')
        );

        $data = $this->mreport_traceability_transaction->readTransactionAvailableList($pSearch,$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        $this->response($data, 200);
    }
    
    function transaction_to_batch_post(){
        $proses = $this->mreport_traceability_transaction->TransactionToBatch(
            $this->post('SupplyBatchID'), 
            $this->post('trans')
        );
        if ($proses){
            $this->response($proses, 200);
        }else{
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }
    
    public function transaction_batch_get(){
        $sorting = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $pSearch = array(
            "Role" => $this->get('Role'),
            "StringNameUsername" => $this->get('StringNameUsername')
        );

        $data = $this->mreport_traceability_transaction->readTransactionBatchList($this->get('SupplyBatchID'), $pSearch,$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        $this->response($data, 200);
    }
    
    public function destination_get(){
        $data = $this->mreport_traceability_transaction->readDestinationList();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'No Data!'), 404);
        }
    }
    
    public function batch_grid_get(){
        $sorting = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        $pSearch = array(
            "Role" => $this->get('Role'),
            "StringNameUsername" => $this->get('StringNameUsername')
        );

        $data = $this->mreport_traceability_transaction->readBatchGridList($pSearch,$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        $this->response($data, 200);
    }

}
?>