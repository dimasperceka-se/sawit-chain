<?php
/**
 * @Author: nikolius
 * @Date:   2016-09-27 16:57:44
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class tc_transaction extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->file = $_FILES;
        $this->load->model('traceability/mtransaction_new');
    }
    
    function set_get(){
        $proses = $this->mtransaction_new->readTransactionSetting();
        if ($proses){
            $this->response($proses, 200);
        }else{
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }

    public function farmer_grid_get(){
        $sorting = json_decode($this->get('sort'));
        $sortingField = $sorting[0]->property;
        $sortingDir = $sorting[0]->direction;

        $pSearch = array(
            "Role" => $this->get('Role'),
            "StringNameUsername" => $this->get('StringNameUsername')
        );

        $data = $this->mtransaction_new->readFarmerGridList($this->get('TransID'),$pSearch,$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        $this->response($data, 200);
    }

    function comboFarmer_get(){
        $proses = $this->mtransaction_new->readComboFarmer();
        if ($proses){
            $this->response($proses, 200);
        }else{
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }
    
    function comboAgent_get(){
        $proses = $this->mtransaction_new->readComboAgent();
        if ($proses){
            $this->response($proses, 200);
        }else{
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }

    function combomill_get(){
        $proses = $this->mtransaction_new->readComboMill();
        if ($proses){
            $this->response($proses, 200);
        }else{
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }

    function comboDO_get(){
        $proses = $this->mtransaction_new->readComboDO();
        if ($proses){
            $this->response($proses, 200);
        }else{
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }

    public function transaction_grid_get(){

        $sorting = json_decode($this->get('sort'));
        $sortingField = $sorting[0]->property;
        $sortingDir = $sorting[0]->direction;

        $pSearch = array(
            "Role" => $this->get('Role'),
            "StringNameUsername" => $this->get('StringNameUsername')
        );

        $data = $this->mtransaction_new->readTransactionGridList($pSearch,$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        $this->response($data, 200);
    }
    
    function supplyid_get() {
        $key = $this->get('query');
        $tipe = $this->get('tipe');
        $start = $this->get('start');
        $limit = $this->get('limit');
        $data = $this->mtransaction_new->readSupplyIDList($key, $tipe, $start, $limit);
        if ($data){
            $this->response($data, 200);
        }else{
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }    
    }
    
    function transaction_post(){
        $proses = $this->mtransaction_new->createTransaction(
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
        $proses = $this->mtransaction_new->readTransactionDetail($this->get('SupplyTransID'));
        if ($proses){
            $this->response($proses, 200);
        }else{
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }


    protected function generateExcel() {
        
        
    
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
        
        $objPHPExcel = new PHPExcel();

        // Add some data
        $objPHPExcel->setActiveSheetIndex(0);
        
        //Merge Cell for title
        $objPHPExcel->getActiveSheet()->mergeCells("A1:F1");
        $objPHPExcel->getActiveSheet()->mergeCells("A2:F2");
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Laporan Neraca');
        
        //Kolom Aktiva
        $objPHPExcel->getActiveSheet()->mergeCells("A3:C3");
        $objPHPExcel->getActiveSheet()->SetCellValue('A3', 'AKTIVA');
        $objPHPExcel->getActiveSheet()->SetCellValue('A4', 'KODE');
        $objPHPExcel->getActiveSheet()->SetCellValue('B4', 'DESKRIPSI');
        $objPHPExcel->getActiveSheet()->SetCellValue('C4', 'JUMLAH');
        
        //Kolom Pasiva
        $objPHPExcel->getActiveSheet()->mergeCells("D3:F3");
        $objPHPExcel->getActiveSheet()->SetCellValue('D3', 'PASIVA');
        $objPHPExcel->getActiveSheet()->SetCellValue('D4', 'KODE');
        $objPHPExcel->getActiveSheet()->SetCellValue('E4', 'DESKRIPSI');
        $objPHPExcel->getActiveSheet()->SetCellValue('F4', 'JUMLAH');
        
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(60);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(60);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        
        $styleArray = array(
        'font'  => array(
            'bold'  => true,
            'size'  => 12
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        ));

        
        $objPHPExcel->getActiveSheet()->getStyle('A1:F4')->applyFromArray($styleArray);
        
        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$totalrow, '');
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$totalrow, 'Total ');
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$totalrow, $totalaktiva);

        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$totalrow, '');
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$totalrow, 'Total');
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$totalrow, $totalpasiva);
                    
        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Neraca');


        // Save Excel 2007 file
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        //ob_get_clean();
        $objWriter->save('php://output');
        //ob_end_flush();
    }
    
    function transaction_put(){
        $proses = $this->mtransaction_new->updateTransaction(
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
        $proses = $this->mtransaction_new->createBatch($this->post('SupplychainID'));
        if ($proses){
            $this->response($proses, 200);
        }else{
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }
    
    function batch_delete(){
        $proses = $this->mtransaction_new->deleteBatch($this->delete('SupplyBatchID'));
        if ($proses){
            $this->response($proses, 200);
        }else{
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }
    
    function batch_put(){
        $proses = $this->mtransaction_new->updateBatch(
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
        $sortingField = $sorting[0]->property;
        $sortingDir = $sorting[0]->direction;

        $pSearch = array(
            "Role" => $this->get('Role'),
            "StringNameUsername" => $this->get('StringNameUsername')
        );

        $data = $this->mtransaction_new->readTransactionAvailableList($pSearch,$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        $this->response($data, 200);
    }

    
    function transaction_to_batch_post(){
        $proses = $this->mtransaction_new->TransactionToBatch(
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
        $sortingField = $sorting[0]->property;
        $sortingDir = $sorting[0]->direction;

        $pSearch = array(
            "Role" => $this->get('Role'),
            "StringNameUsername" => $this->get('StringNameUsername')
        );

        $data = $this->mtransaction_new->readTransactionBatchList($this->get('SupplyBatchID'), $pSearch,$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        $this->response($data, 200);
    }
    
    public function destination_get(){
        $data = $this->mtransaction_new->readDestinationList();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'No Data!'), 404);
        }
    }
    
    public function batch_grid_get(){
        $sorting = json_decode($this->get('sort'));
        $sortingField = $sorting[0]->property;
        $sortingDir = $sorting[0]->direction;

        $pSearch = array(
            "Role" => $this->get('Role'),
            "StringNameUsername" => $this->get('StringNameUsername')
        );

        $data = $this->mtransaction_new->readBatchGridList($pSearch,$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        $this->response($data, 200);
    }

}
?>