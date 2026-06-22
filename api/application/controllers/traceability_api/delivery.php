<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once 'application/third_party/Spout3/Autoloader/autoload.php';

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Common\Entity\Style\Border;
use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;

class Delivery extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('traceability_api/m_delivery');
        $this->load->helper('traceability_delivery');
    }

    public function grid_main_get() 
    {
        //sort
        $sorting      = json_decode($this->get('sort'));
        if (isset($sorting[0]->property)) $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : ''; else $sortingField = null;
        if (isset($sorting[0]->direction)) $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : ''; else $sortingDir = null;
        $start        = (int) $this->get('start');
        $limit        = (int) $this->get('limit');
        
        $pSearch = array(
            'ArrFilter'                      => $this->get('ArrFilter'),
            'TextFilterExernalCode'         => filter_var($this->get('TextFilterExernalCode'), FILTER_SANITIZE_STRING),
            'TextFilterDestinationID'       => filter_var($this->get('TextFilterDestinationID'), FILTER_SANITIZE_STRING),
            'TextFilterDeliveryStatusID'    => filter_var($this->get('TextFilterDeliveryStatusID'), FILTER_SANITIZE_STRING),
            'TextFilterStartDeliveryDate'   => filter_var(preg_replace("([^0-9-])","",$this->get('TextFilterStartDeliveryDate'))),
            'TextFilterEndDeliveryDate'     => filter_var(preg_replace("([^0-9-])","",$this->get('TextFilterEndDeliveryDate'))),
        );

        $data = $this->m_delivery->GetGridMain($pSearch,$start,$limit,$sortingField,$sortingDir);
        $this->response($data, 200);
    }

    public function fetch_api_get() 
    {
        $SID = $_GET['SID'];
        $PID = $_GET['PID'];
        
        if($SID){
            
            $data = $this->m_delivery->GetDelivery($SID, $PID);

            return $this->response(array('success' => true,
            'message' => 'Data Berhasil di tampilkan',
            'results' => $data), 200);
        }

        return $this->response($this->_output, 401);
    }

    public function submit_api_post() 
    {
        ini_set('display_errors', true);
        error_reporting(E_ALL);

        $post = json_decode(json_encode($this->post()), true);
        if(empty($data)) {
            $post = json_decode(file_get_contents('php://input'), true);
            $data = $post;
        }
        
        $name = $data['SupplychainID'] . '-' . strtotime(date('YmdHis')). '-Trans-' . $data['DeliveryID'];
        $dir = FCPATH . 'backup_traceability_submit_delivery';
        if(!is_dir($dir)) {
          make_directory($dir, 0777, true);
        }
        if(!write_file($dir.'/'.$name.'.json',json_encode($data))) {} else {}

        $data = $this->post(null);
        if ($data) {
            $delivery = $this->m_delivery->submit_delivery_api_post($data);
            if ($delivery) {
                $curlTable = array( 'TableName' => 'ktv_tc_supplychain_delivery', 'TableField' => 'DeliveryID', 'TableID' => @$data['data']['SupplyBatchID'] );

                $version = !empty(@$data['Version']) ? @$data['Version']: null;
                           
                $param = checkLogTraceability($version, 'POST', current_url(), $this->post(), $curlTable);
                
                return $this->response($delivery);
            }
                
        } else {
            return $this->response(array('success' => false, 'error' => 'Data post empty !'), 401);
        }
    }

    public function delete_api_post() 
    {
        ini_set('display_errors', true);
        error_reporting(E_ALL);
        
        $post = json_decode(json_encode($this->post()), true);
        if(empty($data)) {
            $post = json_decode(file_get_contents('php://input'), true);
            $data = $post;
        }
        
        $name = $data['SupplyBatchNumber'] . '-' . strtotime(date('YmdHis')). '-Delivery-' . $data['DeliveryID'];
        $dir = FCPATH . 'backup_traceability_delete_delivery';
        if(!is_dir($dir)) {
          make_directory($dir, 0777, true);
        }
        if(!write_file($dir.'/'.$name.'.json',json_encode($data))) {} else {}
        
        $data = $this->post(null);
        if ($data) {
            $delivery = $this->m_delivery->delete_delivery_api($data);
            if ($delivery) {
                return $this->response($delivery);
            }
        } else {
            return $this->response(array('success' => false, 'error' => 'Data post empty !'), 401);
        }
    }

    function submit_delivery_post() {
        $varPost = $this->post();

        $paramPost = array();
        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Traceability_Delivery_neo_MainForm-FormBasicData-", '', $key);
            $paramPost[$keyNew] = $value;
        }
        
        $data = $this->m_delivery->submit_delivery($paramPost);
        if ($data){
            $this->response($data, 200);
        }
        else{
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }

    function submit_send_put() {
        $varPost = $this->put();

        $paramPost = array();

        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Traceability_Delivery_MainForm-FormBasicData-", '', $key);
            $paramPost[$keyNew] = $value;
        }

        $data = $this->m_delivery->submit_send($paramPost);
        if ($data){
            $this->response($data, 200);
        }
        else{
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }

    public function supplychain_delivery_form_open_get()
    {
        $DeliveryID = (int) $this->get('DeliveryID');
        $data       = $this->m_delivery->SupplychainDeliveryFormOpen($DeliveryID);

        $this->response($data, 200);
    }

    public function data_supplychain_delivery_detail_main_grid_get()
    {
        $DeliveryID = (int) $this->get('DeliveryID');

        $data       = $this->m_delivery->GetSupplychainDeliveryDetail($DeliveryID);
        
        $this->response($data, 200);
    }

    public function data_supplychain_reception_detail_main_grid_get()
    {
        $DeliveryID = (int) $this->get('DeliveryID');

        $data       = $this->m_delivery->GetSupplychainReceptionDetail($DeliveryID);
        
        $this->response($data, 200);
    }

    public function grid_pick_delivery_get() 
    {
        //sort
        $sorting      = json_decode($this->get('sort'));
        if (isset($sorting[0]->property)) $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : ''; else $sortingField = null;
        if (isset($sorting[0]->direction)) $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : ''; else $sortingDir = null;
        $start        = (int) $this->get('start');
        $limit        = (int) $this->get('limit');
        
        $pSearch = array(
            'SupplyBatchNumber'    => filter_var($this->get('SupplyBatchNumber'), FILTER_SANITIZE_STRING),
            'StartDateCreateBatch' => filter_var(preg_replace("([^0-9-])","",$this->get('StartDateCreateBatch'))),
            'EndDateCreateBatch'   => filter_var(preg_replace("([^0-9-])","",$this->get('EndDateCreateBatch'))),
        );

        $data = $this->m_delivery->GetGridPickDeliveryMain($pSearch,$start,$limit,$sortingField,$sortingDir);

        $this->response($data, 200);
    }

    public function data_supplychain_batch_form_open_get()
    {
        $SupplyBatchID = $this->get('SupplyBatchID');
        $data          = $this->m_delivery->SupplychainBatchFormOpen($SupplyBatchID);
        $this->response($data, 200);
    }

    public function data_supplychain_delivery_detail_post()
    {
        $varPost = $this->post();
        $paramPost = array();
        
        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Traceability_Delivery_MainForm-FormBasicData-", '', $key);
            $paramPost[$keyNew] = $value;
        }

        $proses = $this->m_delivery->InsertSupplychainDeliveryDetail($paramPost);
        
        if($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function delivery_status_get()
    {
        $data = $this->m_delivery->GetDeliveryStatus();

        $this->response($data, 200);
    }

    public function close_delivery_pick_post()
    {
        
        $checkDataDeliveryDetailExist = $this->checkDataDeliveryDetailExist($this->post('DeliveryID'));

        if (!empty($checkDataDeliveryDetailExist)) {
            $data = $this->m_delivery->CloseDeliveryPick($this->post());
        } else {
            $data = [
                'success' => false,
                'message' => 'Please fill data delivery detail first'
            ];
        }

        if ($data['success'] === true) {
            $this->response($data, 200);
        } else {
            $this->response($data, 400);
        }
    }
    public function comboDestination_get()
    {
        $DestinationID = $this->get('DestinationID');
        
        $data = $this->m_delivery->ListDestination($DestinationID);
    
        $this->response($data, 200);
    }

    public function comboDealer_get()
    {
        $data = $this->m_delivery->ListDealers();
    
        $this->response($data, 200);
    }

    public function del_transaction_delete()
    {
        $data = $this->m_delivery->deleteTransaction($this->delete());

        $this->response($data, 200);
    }

    public function comboPackage_get()
    {
        $data = $this->m_delivery->ListPackage();

        $this->response($data, 200);
    }

    public function comboDestType_get()
    {
        $data = $this->m_delivery->ListDestTypes();

        $this->response($data, 200);
    }

    public function comboTransportationType_get()
    {
        $data = [
            ["id" => "1", "label" => "Mobil"],
            ["id" => "2", "label" => "Truk Container"],
            ["id" => "4", "label" => "Bak Truk"],
            ["id" => "5", "label" => "Pickup"],
            ["id" => "6", "label" => "Bak Terbuka"],
            ["id" => "7", "label" => "Motor"]
        ];

        $this->response($data, 200);
    }

    public function checkDataDeliveryDetailExist($DeliveryID)
    {
        $checkDataDeliveryDetailExist = $this->db->where('DeliveryID', $DeliveryID)
                                        ->get('ktv_tc_supplychain_delivery_detail')
                                        ->result();

        return $checkDataDeliveryDetailExist;
    }

    public function sp_code_get(){
        
        $id = $this->get('id');

        $data = $this->m_delivery->getSPCode($id);
    
        $this->response($data, 200);
    
    }

    public function grid_transaction_detail_get() 
    {
        $DeliveryID = $this->get('DeliveryID');
       
        $data = $this->m_delivery->transactionDetail($DeliveryID);

        if($data){ 
            return $this->response(array('success' => true, 
                                         'message' => 'Data Berhasil Ditampilkan',
                                         'total' => $data['total'], 
                                         'data' => $data['data']),  200);
        }
        return $this->response($this->_output,200);
        
        $this->response($data, 200);
    }
    
    function export_excel_delivery_get($SID=0){ 
        
        $pSearch = array(
            'ArrFilter'                      => $this->get('ArrFilter'),
            'TextFilterExernalCode'         => filter_var($this->get('TextFilterExernalCode'), FILTER_SANITIZE_STRING),
            'TextFilterDestinationID'       => filter_var($this->get('TextFilterDestinationID'), FILTER_SANITIZE_STRING),
            'TextFilterDeliveryStatusID'    => filter_var($this->get('TextFilterDeliveryStatusID'), FILTER_SANITIZE_STRING),
            'TextFilterStartDeliveryDate'   => filter_var(preg_replace("([^0-9-])","",$this->get('TextFilterStartDeliveryDate'))),
            'TextFilterEndDeliveryDate'     => filter_var(preg_replace("([^0-9-])","",$this->get('TextFilterEndDeliveryDate'))),
        );
    
        $delivery = $this->m_delivery->GetGridMainExcel($pSearch,$start,$limit,$sortingField,$sortingDir);
        
        $dataList = $delivery["data"];

        if(count($delivery)){

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
            $namaFile = date('YmdHis') . '_export_deliveries.xlsx';
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
            
            $writer->getCurrentSheet()->setName(lang("Deliveries"));
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
    
            $writer->close();
    
            $this->response(array('success' => TRUE, 'filenya' => base_url() . $filePath), 200);
        }
	}	

    private function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
}