<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require_once 'application/third_party/Spout3/Autoloader/autoload.php';
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Common\Entity\Style\CellAlignment;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Common\Entity\Style\Border;
use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;

class Payment extends REST_Controller {

    public function __construct() {
        $this->file = $_FILES;
        parent::__construct();
        $this->load->model('mpayment', '_model');
    }

    public function combo_partner_get(){
        $data = $this->_model->getComboPartner();
        $this->response($data, 200);
    }

    public function combo_recipient_get() {
        $data = $this->_model->comboRecipient($this->get());
        $this->response($data, 200); // 200 being the HTTP response code
    }

    public function bankAccount_get() {
        $data = $this->_model->getBankAccount($this->get());
        $this->response($data, 200); // 200 being the HTTP response code
    }

    public function save_transaction_post() {
        $data = $this->_model->saveTransaction($this->post());
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }

    public function submit_payment_post() {
        $return    = array();
        $varPost   = $this->post();
        $paramPost = array();
        
        if ($varPost['OpsiDisplay'] == 'update') {
            unset($varPost['SupplyTransID']);  
        } else {
            unset($varPost['Koltiva_view_Traceability_new_Transaction_neo_MainForm-FormBasicData-SupplyTransID']);
            $paramPost['SupplyTransID'] = $varPost;
        }

        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Traceability_new_Transaction_neo_MainForm-FormBasicData-", '', $key);
            $paramPost[$keyNew] = $value;
        }
        
        $proses = $this->_model->SubmitPayment($paramPost);

        if($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function fetch_detail_prepayment_get() {
        $data = $this->_model->readDetailPrepayment($this->get());
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }
    
    public function fetch_grid_prepayment_get() {
        $data = $this->_model->readDataPrePayment($this->get());
        $this->response($data, 200);
    }

    public function invoice_get($PaymentID) {
        $param = array(
            'PrepaymentID' => $PaymentID
        );
       
        $data['data'] = $this->_model->readDetailPrepayment($param);
        $view = 'print_invoice_prepayment';
        $this->load->view($view, $data);
    }

    public function payment_instruction_get() {
        $data = $this->_model->getPaymentInstruction($this->get());
        $this->response($data, 200); // 200 being the HTTP response code
    }

    public function check_payment_status_get() {
        $data = $this->_model->CheckPaymentStatus($this->get());
        $this->response($data, 200); // 200 being the HTTP response code
    }

    public function fetch_combo_payment_method_get() {
        $data = $this->_model->ComboPaymentMethod();
        $this->response($data, 200);
    }

    public function export_excel_get(){
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $order = json_decode($this->get('sort'), true);
        
        $StartDate = $this->get('StartDate')=="null"?"":$this->get('StartDate');
        $EndDate = $this->get('EndDate')=="null"?"":$this->get('EndDate');
        $PaymentStatusID = $this->get('PaymentStatusID')=="null"?"":$this->get('PaymentStatusID');

        $get = array(
            
            'StartDate' => filter_var($StartDate, FILTER_SANITIZE_STRING),
            'EndDate' => filter_var($EndDate, FILTER_SANITIZE_STRING),
            'PaymentStatusID' => filter_var($PaymentStatusID, FILTER_SANITIZE_STRING),
            'TransNumber' => filter_var($this->get('TransNumber'), FILTER_SANITIZE_STRING),
            
        );
        $get['start'] = 0;
        $get['limit'] = 99999999999999;
        
        $data = $this->_model->readDataPrePayment($get);
        //$this->response($data, 200);die;

        //ambil data  (begin)
        $dataList = $data['data'];

        $dataKolom = $data['data'];

        $writer = WriterEntityFactory::createXLSXWriter();
        $namaFile = 'prepayment-'.date('ymdHis').'.xlsx';
        $filePath = 'files/sql_view/'.$namaFile;
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
   
        $writer->addRow(WriterEntityFactory::createRowFromArray(['PREPAYMENT'],$styleTitle));
        $writer->addRow(WriterEntityFactory::createRowFromArray(["Exported By : ".$_SESSION['realname']],$styleTitle));
        $writer->addRow(WriterEntityFactory::createRowFromArray(["Date : ".date('Y-m-d H:i')],$styleTitle));
    
        $dataHeader = array(
            'No.', 'Transaction ID','Date', 'Amount','Payment Fee','Total Payment', 
            'Recipient','Bank Name','Account Number','Account Name','Status',
            'Disburse Fee','Disburse Total'
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
        $total = 0;
        //row data
        foreach($dataList as $k=>$v){
            $cells = array();
            
            $cells = [
                WriterEntityFactory::createCell((float) $no, $styleFormatAngka),
                
                WriterEntityFactory::createCell( $v['TransactionNumber'], $styleData),
                WriterEntityFactory::createCell(  $v['Date'], $styleData),
                WriterEntityFactory::createCell( (float) $v['Amount'], $styleFormatAngka),
                WriterEntityFactory::createCell( (float) $v['ServiceCharge'], $styleFormatAngka),
                WriterEntityFactory::createCell( (float) $v['TotalPaymentWithServiceCharge'], $styleFormatAngka),
                WriterEntityFactory::createCell(  $v['Recipient'], $styleData),
              
                WriterEntityFactory::createCell(  $v['BankName'], $styleData),
                WriterEntityFactory::createCell(  $v['AccountNumber'], $styleData),
                WriterEntityFactory::createCell(  $v['AccountName'], $styleData),
               
                WriterEntityFactory::createCell( $v['TransStatus'], $styleData),
                WriterEntityFactory::createCell( (float) $v['DisburseFee'], $styleFormatAngka),
                WriterEntityFactory::createCell( (float) $v['TotalDisburse'], $styleFormatAngka),
         
            ];

            $rowData = WriterEntityFactory::createRow($cells);
            $writer->addRow($rowData);
            $no++;
        }
       
        $writer->close();

        $filenya =  'files/sql_view/'.$namaFile;
        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // header('Content-Disposition: attachment;filename="'.$namaFile);
        // header('Cache-Control: max-age=0');
        // readfile($filenya);


        header('Location:'.base_url($filenya));

        die;
        
    }

    public function remove_payment_delete() {
        $data = $this->_model->DeletePayment($this->delete());
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }
}
?>
