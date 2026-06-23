<?php defined('BASEPATH') OR exit('No direct script access allowed');
/*
    Yusuf Sutana
*/

require_once 'application/third_party/Spout3/Autoloader/autoload.php';


use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
//use Box\Spout\Common\Entity\Style\CellAlignment;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Common\Entity\Style\Border;
use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;

class Web_transaction extends REST_Controller {
    
    public $_output = array('success' => false, 'error' => 'Data is not valid'); //response data
    
    public function __construct() {
        parent::__construct();
        date_default_timezone_set('UTC');
        $this->load->model('traceability_api/m_webtransaction','_model');
    }
    
    public function fetch_get() {
        $UserID = $_SESSION['userid'];

        $SID = $this->_model->get_data_user($UserID);

        if($SID){

            $sorting      = json_decode($this->get('sort'));
            if (isset($sorting[0]->property)) $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : ''; else $sortingField = null;
            if (isset($sorting[0]->direction)) $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : ''; else $sortingDir = null;
            $start        = (int) $this->get('start');
            $limit        = (int) $this->get('limit');
        
            $pSearch = array(
                'ArrFilter'                            => @$this->get('ArrFilter'),
                'TextFilterTransTypeName'              => filter_var(@$this->get('TextFilterTransTypeName'), FILTER_SANITIZE_STRING),
                'TextFilterTransSupplyID'              => filter_var(@$this->get('TextFilterTransSupplyID'), FILTER_SANITIZE_STRING),
                'TextFilterMemberName'                 => filter_var(@$this->get('TextFilterMemberName'), FILTER_SANITIZE_STRING),
                'TextFilterStartDateTransaction'       => filter_var(preg_replace("([^0-9-])","",@$this->get('TextFilterStartDateTransaction'))),
                'TextFilterEndDateTransaction'         => filter_var(preg_replace("([^0-9-])","",@$this->get('TextFilterEndDateTransaction'))),
            );
            
            $transaction = $this->_model->get_data_transaction($SID,$pSearch,$start,$limit,$sortingField,$sortingDir);
            
            if($transaction){ 
                return $this->response(array('success' => true, 
                                             'message' => 'Data Berhasil Ditampilkan',
                                             'total' => $transaction['total'], 
                                             'data' => $transaction['data']),  200);
            }
        }

        return $this->response($this->_output,200);
    }	

    public function fetch_sms_get() {
      
            $sorting      = json_decode($this->get('sort'));
            if (isset($sorting[0]->property)) $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : ''; else $sortingField = null;
            if (isset($sorting[0]->direction)) $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : ''; else $sortingDir = null;
            $start        = (int) $this->get('start');
            $limit        = (int) $this->get('limit');

            $pSearch = array(
                'ArrFilter'                            => @$this->get('ArrFilter'),
                'TextFilterTransTypeName'              => filter_var(@$this->get('TextFilterTransTypeName'), FILTER_SANITIZE_STRING),
                'TextFilterTransSupplyID'              => filter_var(@$this->get('TextFilterTransSupplyID'), FILTER_SANITIZE_STRING),
                'TextFilterMemberName'                 => filter_var(@$this->get('TextFilterMemberName'), FILTER_SANITIZE_STRING),
                'TextFilterStartDateTransaction'       => filter_var(preg_replace("([^0-9-])","",@$this->get('TextFilterStartDateTransaction'))),
                'TextFilterEndDateTransaction'         => filter_var(preg_replace("([^0-9-])","",@$this->get('TextFilterEndDateTransaction'))),
                'TextFilterProvince'                   => filter_var(@$this->get('TextFilterProvince'), FILTER_SANITIZE_STRING),
                'TextFilterDistrict'                   => filter_var(@$this->get('TextFilterDistrict'), FILTER_SANITIZE_STRING),
            );
            
            $sms_transaction = $this->_model->get_data_sms_transaction($pSearch,$start,$limit,$sortingField,$sortingDir,"export_excel");
            
            if($sms_transaction){ 
                return $this->response(array('success' => true, 
                'message' => 'Data Berhasil Ditampilkan',
                'total' => $sms_transaction['total'], 
                'data' => $sms_transaction['data']),  200);
            }

        return $this->response($this->_output,200);
    }	

    public function sms_detail_form_open_get()
    {
        $AutoID = (int) $this->get('AutoID');
        $data       = $this->_model->SmsDetailFormOpen($AutoID);

        $this->response($data, 200);
    }

    public function sms_checking_get()
    {
        $AutoID = (int) $this->get('AutoID');
       
        $data   = $this->_model->SmsChecking($AutoID);

        if($data){ 
            return $this->response(array('success' => true, 
            'message' => 'Data Berhasil Ditampilkan',
            'total' => $data['total'], 
            'data' => $data['data']),  200);
        }

        return $this->response($this->_output,200);
    }

    function resend_sms_post() {
        
        $data = $this->_model->resendSMS($this->post());
        $this->response($data, 200);
    }

    function checking_sms_post() {
        
        $data = $this->_model->checkingSMS($this->post());
        $this->response($data, 200);
    }

    public function transaction_form_open_get()
    {
    
        $SupplyTransID     = (int) $this->get('SupplyTransID');
        
        $data          = $this->_model->TransactionFormOpen($SupplyTransID);
        
        $this->response($data, 200);
    }

    public function data_weight_unit_main_grid_get()
    {
        $SupplyTransID = (int) $this->get('SupplyTransID');
        
        $data          = $this->_model->GetWeightUnitTransaction($SupplyTransID);

        $this->response($data, 200);
    }

    public function data_weight_unit_form_open_get()
    {
        $SupplyTransID = (int) $this->get('SupplyTransID');
        
        $data          = $this->_model->GetWeightUnitTransactionDetail($SupplyTransID);

        $this->response($data, 200);
    }

    private function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
    
    function export_excel_get($SID=0){ 

        $UserID = $_SESSION['userid'];

        $SID = $this->_model->get_data_user($UserID);

        $pSearch = array(
            'ArrFilter'                            => @$this->get('ArrFilter'),
            'TextFilterTransTypeName'              => filter_var(@$this->get('TextFilterTransTypeName'), FILTER_SANITIZE_STRING),
            'TextFilterTransSupplyID'              => filter_var(@$this->get('TextFilterTransSupplyID'), FILTER_SANITIZE_STRING),
            'TextFilterMemberName'                 => filter_var(@$this->get('TextFilterMemberName'), FILTER_SANITIZE_STRING),
            'TextFilterStartDateTransaction'       => filter_var(preg_replace("([^0-9-])","",@$this->get('TextFilterStartDateTransaction'))),
            'TextFilterEndDateTransaction'         => filter_var(preg_replace("([^0-9-])","",@$this->get('TextFilterEndDateTransaction')))
        );
        
        $transaction = $this->_model->get_data_transaction_excel($SID,$pSearch,$start,$limit,$sortingField,$sortingDir);
        
        $dataList = $transaction["data"];

        if(count($transaction)){

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
            $namaFile = date('YmdHis') . '_export_buyings.xlsx';
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
            
            $writer->getCurrentSheet()->setName(lang("Transactions"));
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

    function export_excel_sms_get(){ 
        $pSearch = array(
            'ArrFilter'                            => @$this->get('ArrFilter'),
            'TextFilterTransTypeName'              => filter_var(@$this->get('TextFilterTransTypeName'), FILTER_SANITIZE_STRING),
            'TextFilterTransSupplyID'              => filter_var(@$this->get('TextFilterTransSupplyID'), FILTER_SANITIZE_STRING),
            'TextFilterMemberName'                 => filter_var(@$this->get('TextFilterMemberName'), FILTER_SANITIZE_STRING),
            'TextFilterStartDateTransaction'       => filter_var(preg_replace("([^0-9-])","",@$this->get('TextFilterStartDateTransaction'))),
            'TextFilterEndDateTransaction'         => filter_var(preg_replace("([^0-9-])","",@$this->get('TextFilterEndDateTransaction'))),
            'TextFilterProvince'                   => filter_var(@$this->get('TextFilterProvince'), FILTER_SANITIZE_STRING),
            'TextFilterDistrict'                   => filter_var(@$this->get('TextFilterDistrict'), FILTER_SANITIZE_STRING),
        );
        
        $sms_transaction = $this->_model->get_data_sms_transaction($pSearch,$start,$limit,$sortingField,$sortingDir,"export_excel");
        
        $dataList = $sms_transaction["data"];

        if(count($sms_transaction)){

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
            $namaFile = date('YmdHis') . '_export_sms_log_transaction.xlsx';
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
            
            $writer->getCurrentSheet()->setName(lang("sms log transaction"));
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
    
                $rowData = WriterEntityFactory::createRow($cells);
                $writer->addRow($rowData);
            }
    
            $writer->close();
    
            $this->response(array('success' => TRUE, 'filenya' => base_url() . $filePath), 200);
        }
    }
    
    public function submit_post(){
        ini_set('display_errors',true);
        error_reporting(E_ALL);
        $data = $this->post(NULL);
        if($data){
            $transaction = $this->_model->submit($data);
            if($transaction){ 
                return $this->response($transaction,200);
            }
        }else{
            return $this->response(array('success' => false, 'error' => 'Data post empty !'),200);
        }
    }
    
    public function qualityGrid_get(){
        ini_set('display_errors',true);
        //error_reporting(E_ALL);
        $SID = $this->input->get('SID');
        $STID = $this->input->get('STID');

        /* Model */
        $output["metaData"]["fields"][]=array("name"=>"TransQualityID","type"=>"int"); 
		$output["metaData"]["fields"][]=array("name"=>"SupplyTransID","type"=>"int"); 
        $output["metaData"]["fields"][]=array("name"=>"ValueQualityID","type"=>"int"); 
        $output["metaData"]["fields"][]=array("name"=>"QualityID","type"=>"int"); 
        $output["metaData"]["fields"][]=array("name"=>"Name","type"=>"string"); 
        $output["metaData"]["fields"][]=array("name"=>"StandardValue","type"=>"string");  
        $output["metaData"]["fields"][]=array("name"=>"MinValue","type"=>"string"); 
        $output["metaData"]["fields"][]=array("name"=>"MaxValue","type"=>"string"); 
        $output["metaData"]["fields"][]=array("name"=>"Value","type"=>"string");
        $output["metaData"]["fields"][]=array("name"=>"StatusCode","type"=>"string"); 
        $output["metaData"]["fields"][]=array("name"=>"DateCreated","type"=>"date");  
        $output["metaData"]["fields"][]=array("name"=>"CreatedBy","type"=>"string"); 
        $output["metaData"]["fields"][]=array("name"=>"DateUpdated","type"=>"date");  
        $output["metaData"]["fields"][]=array("name"=>"LastModifiedBy","type"=>"string"); 
		
        /* Column */
        $output["columns"][]=array("xtype"=>"rownumberer", "header"=>"No ", "width"=>35);
        $output["columns"][]=array("dataIndex"=>"Name", "header"=>"Name", "width"=>'25%');
        $output["columns"][]=array("dataIndex"=>"Value", "header"=>"Value", "width"=>'15%');
		$output["columns"][]=array("dataIndex"=>"ValueQualityID", "header"=>"ValueQualityID", 'hidden'=> true);
        $output["columns"][]=array("dataIndex"=>"StandardValue", "header"=>"Standar", "width"=>'15%', "align" => "right");
        $output["columns"][]=array("dataIndex"=>"MinValue", "header"=>"Min Value", "width"=>'15%', "align" => "right");
        $output["columns"][]=array("dataIndex"=>"MaxValue", "header"=>"Max Value", "width"=> '15%');  
        
		 
        /* Content */
        $total = 0;
        $start = 0;
        $limit = 50;
        $page = 1;

        if($SID){
            $start = (int)$this->input->get('start');
            $limit = (int)$this->input->get('limit') != '' ?  (int)$this->input->get('limit') : 50;

            $Q = $this->db->query('SELECT b.*,  a.QualityID, a.Name, a.StandardValue, a.MinValue, a.MaxValue, a.Type, c.Value as ValueQualityName
                                    FROM ktv_tc_supplychain_quality a
                                    LEFT JOIN ktv_tc_supplychain_transaction_quality b ON a.QualityID=b.QualityID AND b.SupplyTransID="'.$STID.'"
									LEFT JOIN ktv_tc_supplychain_quality_value c ON b.Value = c.ValueQualityID
                                    WHERE a.SupplychainID = '.$SID.'
                                      AND a.StatusCode = "active" 
                                    ORDER BY a.QualityID desc
                                    LIMIT '.$start.', '.$limit.' ');
			//echo $this->db->last_query();die; 
            if(@$Q->num_rows()){
                $arr = array();
                $i=0; 
                foreach($Q->result() as $val){
                    $arr[$i]['TransQualityID'] = $val->TransQualityID;
                    $arr[$i]['SupplyTransID'] = $val->SupplyTransID;
                    $arr[$i]['QualityID'] = $val->QualityID;
                    $arr[$i]['Name'] = $val->Name;
                    $arr[$i]['StandardValue'] = $val->StandardValue;
                    $arr[$i]['MinValue'] = $val->MinValue;
                    $arr[$i]['MaxValue'] = $val->MaxValue;
                    $arr[$i]['Value'] = $val->ValueQualityName;
                    $arr[$i]['StatusCode'] = $val->StatusCode;
                    $arr[$i]['DateCreated'] = $val->DateCreated;
                    $arr[$i]['CreatedBy'] = $val->CreatedBy;
                    $arr[$i]['DateUpdated'] = $val->DateUpdated;
                    $arr[$i]['LastModifiedBy'] = $val->LastModifiedBy;
                    $arr[$i]['ValueQualityID'] = $val->Value; 
					
                    if($val->Type == 'combo'){
                        $store = $this->_model->get_data_quality_value($val->QualityID); 
                        $output["columns"][2]['editor'] = array('xtype' => 'combobox',
                            'store' => "Koltiva.store.Traceability.Transaction.ComboQuality",   
                            'displayField' => 'Value', 'valueField' => 'Value');
                    }else{
                        $output["columns"][2]['editor'] = array('xtype' => 'textfield');
                    }
                    $i++;
                }
                $output["data"] = $arr;
            }
            $total = $Q->num_rows();
        }

        $output["metaData"]["type"]="json";
        $output["metaData"]["idProperty"]="QualityID";
        $output["metaData"]["totalProperty"]="total";
        $output["metaData"]["successProperty"]="success";
        $output["metaData"]["root"]="data";
        
        $output["total"]= $total;
        $output["success"]=true;
        $output["message"]="success";
        
        echo json_encode($output); die;
    }

    public function getFarmerCertified_get(){
        $MemberID   = $this->input->get('MemberID');

        if($MemberID){
            $farmer = $this->_model->get_data_farmer_certified($MemberID);
            return $this->response(array("data"=>$farmer),200);
        }
        return $this->response($this->_output,200);
    }

    /* Combo WEB */   
    public function farmer_get() {
        $PartnerID= $this->input->get('PID');
        $SupplychainID= $this->input->get('SID');
        
        if($PartnerID){
            $farmer = $this->_model->get_data_farmer($PartnerID, $SupplychainID);
            if($farmer){ 
                return $this->response(array('success' => true, 
                                             'message' => 'Data Berhasil Ditampilkan',
                                             'total' => $farmer['total'], 
                                             'data' => $farmer['data']),  200);
            }
        }
        return $this->response($this->_output,200);
    }

    public function farmer_combo_get() {

        $search = $this->get();
        
        $getData = $this->_model->GetFarmers($search)->result_array();
        $pushData = [];

        if (!empty($getData)) {
            foreach ($getData as $key => $value) {
                if ($value['DateOfBirth']) {
                    $dateNow   = new DateTime('today');
                    $birtDate  = new DateTime($value['DateOfBirth']);
                    $calculate = $birtDate->diff($dateNow)->y;

                    $value['Age'] = $calculate;
                }

                switch ($value['Gender']) {
                    case 'm':
                        $gender = 'male';
                        break;
                    default:
                        $gender = 'female';
                        break;
                }

                if ($value['PartnerID'] == NULL) {
                    $value['PartnerID'] = '-';
                }

                unset($value['Gender']);
                $value['Gender'] = $gender;

                array_push($pushData, $value);
            }
        }

        $data['data'] = $pushData;

        $this->response($data, 200);
    }
    public function plantation_get() {
        $MemberID= $this->input->get('MemberID');

        if($MemberID){
            $plantation = $this->_model->get_data_plantation($MemberID);
            if($plantation){ 
                return $this->response(array('success' => true, 
                                             'message' => 'Data Berhasil Ditampilkan',
                                             'total' => $plantation['total'], 
                                             'data' => $plantation['data']),  200);
            }
        }
        return $this->response($this->_output,200);
    }

    public function plantation_new_get() {
        $MemberID= $this->input->get('MemberID');

        if($MemberID){
            $plantation = $this->_model->get_data_plantation_new($MemberID);
            if($plantation){ 
                return $this->response(array('success' => true, 
                                             'message' => 'Data Berhasil Ditampilkan',
                                             'total' => $plantation['total'], 
                                             'data' => $plantation['data']),  200);
            }
        }
        return $this->response($this->_output,200);
    }
    public function agent_seller_get(){    
        $SupplychainID = $this->input->get('SupplychainID');

        $data = $this->_model->get_agent_seller($SupplychainID);

        $this->response($data,200);
    }
    public function do_seller_get(){        
        $SupplychainID = $this->input->get('SupplychainID');

        $data = $this->_model->get_do_seller($SupplychainID);

        $this->response($data,200);
    }
    public function mill_seller_get(){
        $this->load->model('traceability_api/m_auth','_mauth');
        $SupplychainID = $this->input->get('SupplychainID');

        $sid = $this->_mauth->get_all_relation_id($SupplychainID);
        $data = $this->_mauth->org($sid);

        $data = array_filter($data, function ($var) {
            return ($var->Longitude != '' AND $var->ObjType == 'mill');
        });

        $newdata = array();

        foreach($data as $key => $value){
            array_push($newdata,$value);
        }
        
        return $this->response(
            array(
                "success"=>true,
                "message"=>"Data Berhasil Ditampilkan",
                "total" =>count($data),
                "data" => $newdata
            ),200
        );
    }
    public function plantation_tc_get(){
        $SupplychainID = $this->input->get('SupplychainID');

        if($SupplychainID){
            $org = $this->db->query("SELECT * FROM ktv_tc_supplychain_org WHERE SupplychainID=?", array($SupplychainID))->result_array();

            $plantation = $this->plantation_tc_list($org[0]['ObjID'], $org[0]['ObjType']);

            if($plantation){
                foreach($plantation as $key => $val){
                    $val = $this->_model->check_isNull($val);
                    //$val->PlantationName = $val->PlantationNr.'|'.$val->FarmingTypeName;
                    $val->PlantationName = $val->PlantationNr;
                }
                $data['data'] = $plantation;
                $data['total'] = count($plantation);
            }
            //echo $this->db->last_query(); die;

            return $this->response($data,200);
        }
    }

    public function plantation_tc_new_get(){
        $SupplychainID = $this->input->get('SupplychainID');

        if($SupplychainID){
            $org = $this->db->query("SELECT * FROM ktv_tc_supplychain_org WHERE SupplychainID=?", array($SupplychainID))->result_array();
            
            $plantation = $this->plantation_tc_list($org[0]['ObjID'], $org[0]['ObjType']);

            if($plantation){
                foreach($plantation as $key => $val){
                    $val = $this->_model->check_isNull($val);
                    //$val->PlantationName = $val->PlantationNr.'|'.$val->FarmingTypeName;
                    $val->PlantationName = $val->PlantationNr;
                }
                $data['data'] = $plantation;
                $data['total'] = count($plantation);
            }
            //echo $this->db->last_query(); die;

            return $this->response($data,200);
        }
    }

    private function plantation_tc_list($MemberID, $ObjType='')
    {
        if($ObjType=='mill'){
            $sql = "SELECT
                        p.PlotNr PlantationNr,
                        0 SurveyNr,
                        IFNULL(v.VillageID,'') VillageID,
                        p.Latitude,
                        p.Longitude,
                        IFNULL(p.GardenAreaHa, 0) GardenAreaHa,
                        IFNULL(v.Village,'') Village
                    FROM
                        ktv_survey_plot_status_mill p
                        LEFT JOIN ktv_mill m ON m.MIllID=p.MillID
                        LEFT JOIN ktv_village v ON v.VillageID=m.VillageID
                    WHERE
                        p.MillID = ?
                        AND p.StatusCode='active'
                    GROUP BY p.MillID, p.PlotNr";
        }else{
            $sql = "SELECT
                        p.PlotNr PlantationNr,
                        MAX(p.SurveyNr) SurveyNr,
                        IFNULL(p.VillageID,'') VillageID,
                        p.Latitude,
                        p.Longitude,
                        IFNULL(p.GardenAreaHa, 0) GardenAreaHa,
                        IFNULL(v.Village,'') Village
                    FROM
                        ktv_survey_plot_sme p
                        LEFT JOIN ktv_survey_plot_status_sme ps ON ps.MemberID=p.MemberID AND ps.PlotNr=p.PlotNr
                        LEFT JOIN ktv_village v ON v.VillageID=p.VillageID
                    WHERE
                        p.MemberID = ?
                        AND ps.ActiveStatus='1'
                    GROUP BY
                        p.MemberID, p.PlotNr";
        }
        $query = $this->db->query($sql, array($MemberID));
        //echo $this->db->last_query(); die;
        if ($query->num_rows()) {
            $result = $query->result();
        }else{
            $result = array();
        }
        return $result;
    }
    public function package_type_get() {
        $SupplychainID = $this->input->get('SID');
        if($SupplychainID){
            $package_type = $this->_model->get_data_package_type($SupplychainID);
            if($package_type){ 
                return $this->response(array('success' => true, 
                                             'message' => 'Data Berhasil Ditampilkan',
                                             'total' => $package_type['total'], 
                                             'data' => $package_type['data']),  200);
            }
        }
        return $this->response($this->_output,200);
    }
    public function quality_get() {
        $SupplychainID = $this->input->get('SID');
        if($SupplychainID){
            $quality = $this->_model->get_data_quality($SupplychainID);
            if($quality){ 
                return $this->response(array('success' => true, 
                                             'message' => 'Data Berhasil Ditampilkan',
                                             'total' => $quality['total'], 
                                             'data' => $quality['data']),  200);
            }
        }
        return $this->response($this->_output,200);
    }
    public function quality_value_get() {
        $QualityID = $this->input->get('QualityID');
        if($QualityID){
            $quality_value = $this->_model->get_data_quality_value($QualityID);
            if($quality_value){ 
                return $this->response(array('success' => true, 
                                             'message' => 'Data Berhasil Ditampilkan',
                                             'total' => $quality_value['total'], 
                                             'data' => $quality_value['data']),  200);
            }
        }
        return $this->response($this->_output,200);
    }
	
	function report_cetak_kuitansi_get($SupplyTransID =0 , $SupplychainID = 0){ 
        $data['data'] = $this->_model->getTransaksi($SupplyTransID, $SupplychainID);
		$data['quality'] = $this->_model->getTransaksiQuality($SupplyTransID);
		echo $this->load->view('kwitansi', $data);
		 
	}

    public function check_role_transaction_get(){
        $check = $this->_model->check_role_transaction();
        return $this->response($check,  200);
    }

    public function data_transaction_detail_input_post()
    {
        $return    = array();
        $varPost   = $this->post();
        $paramPost = array();
        
        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Traceability_new_Transaction_neo_WinFormDataUnit-Form-", '', $key);
            $paramPost[$keyNew] = $value;
        }
        
        if($paramPost['OpsiDisplay'] == 'insert') {
            $proses = $this->_model->InsertTransactionDetail($paramPost);
        } else {
            $proses = $this->_model->UpdateTransactionDetail($paramPost);
        }

        if($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function data_post()
    {
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
        
        $proses = $this->_model->UpdateTransaction($paramPost);

        if($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function data_transaction_detail_delete() 
    {
        $varDelete = $this->delete();
        
        $proses = $this->_model->DeleteTransactionDetail($varDelete);

        if($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function fetch_purchases_report_get() {
        $UserID = $_SESSION['userid'];

        $SID = $this->_model->get_data_user($UserID);

        if($SID){
            $transaction = $this->_model->get_data_purchase_report($SID);
            if($transaction){ 
                return $this->response(array('success' => true, 
                                             'message' => 'Data Berhasil Ditampilkan',
                                             'total' => $transaction['total'], 
                                             'data' => $transaction['data']),  200);
            }
        }
        return $this->response($this->_output,200);
    }	

    public function fetch_sales_report_get() {
        $UserID = $_SESSION['userid'];

        $SID = $this->_model->get_data_user($UserID);
        
        if($SID){
            $transaction = $this->_model->get_data_sales_report($SID);
            if($transaction){ 
                return $this->response(array('success' => true, 
                                             'message' => 'Data Berhasil Ditampilkan',
                                             'total' => $transaction['total'], 
                                             'data' => $transaction['data']),  200);
            }
        }
        return $this->response($this->_output,200);
    }	

    function export_excel_report_get($SID=0){ 

        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';

        //Get Data sales
        $sales = $this->_model->get_data_sales_report($_GET["sid"],"export_excel");
        
        $mem_ini = ini_get('memory_limit');
        ini_set('memory_limit', '1048576M');

        //=============== MULAI TULIS EXCEL (BEGIN) ===================================================================//

        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Sales');

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30); 

        $objPHPExcel->getActiveSheet()
        ->getStyle('A:L')
        ->getAlignment()
        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        $styleFontBoldHeader = array(
            'font' => array(
                'name' => 'Arial',
                'size' => '11',
                'bold' => true,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '95130b'),
            ),
        );

        $styleFont = array(
            'font' => array(
                'name' => 'Arial',
                'size' => '11'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID
            ),
        );

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'No');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Tanggal Pengiriman');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Nama');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Tujuan Mill');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Berat Kotor Pengiriman');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Berat Bersih di Jual');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Total harga');

        $rowStart = 2;
        for ($i = 0; $i < count($sales['data']); $i++) {
            $data = (array) $sales['data'];
            
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $rowStart, $i + 1);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $rowStart, $data[$i]->Tanggal_pengiriman);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $rowStart, $data[$i]->Nama_agen);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $rowStart, $data[$i]->Tujuan_Mill);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $rowStart, $data[$i]->Berat_kotor_pengiriman);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $rowStart, $data[$i]->Berat_bersih_dijual);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $rowStart, $data[$i]->Total_harga);
           
            $rowStart++;
        }

        $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->applyFromArray($styleFontBoldHeader);

        $objPHPExcel->getActiveSheet()->setCellValue("D$rowStart", "Total");

        $objPHPExcel->getActiveSheet()->setCellValue('E'.$rowStart, "=SUM(E2:E".($rowStart-1).")");

        $objPHPExcel->getActiveSheet()->setCellValue('F'.$rowStart, "=SUM(F2:F".($rowStart-1).")");

        $objPHPExcel->getActiveSheet()->setCellValue('G'.$rowStart, "=SUM(G2:G".($rowStart-1).")");


        //transaction start
        $purchase = $this->_model->get_data_purchase_report($_GET["sid"],"export_excel");
        
        $myWorkSheet = new PHPExcel_Worksheet($objPHPExcel, 'Purchases');

        // Attach the "My Data" worksheet as the first worksheet in the PHPExcel object
        $objPHPExcel->addSheet($myWorkSheet, 0);

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        //set width column
        $objPHPExcel->getActiveSheet()
        ->getStyle('A:M')
        ->getAlignment()
        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        $objPHPExcel->getActiveSheet()->getStyle('J:L')
        ->getNumberFormat()
        ->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

        $styleFontBoldHeader = array(
            'font' => array(
                'name' => 'Arial',
                'size' => '11',
                'bold' => true,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '95130b'),
            ),
        );

        $styleFont = array(
            'font' => array(
                'name' => 'Arial',
                'size' => '11'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID
            ),
        );

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'No');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Tanggal Transaksi');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'ID Pemasok');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Nama Pemasok');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Janjang');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Berat Kotor');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Presentase Pemotongan');
        $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Berat Bersih');
        $objPHPExcel->getActiveSheet()->setCellValue('I1', 'Harga Per Kilo');
        $objPHPExcel->getActiveSheet()->setCellValue('J1', 'Total');
        $objPHPExcel->getActiveSheet()->setCellValue('K1', 'Pengurangan Pembayaran');
        $objPHPExcel->getActiveSheet()->setCellValue('L1', 'Jumlah Pembayaran');
        $objPHPExcel->getActiveSheet()->setCellValue('M1', 'Ketelusuran');

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(30); 

        $dataPurchases = (array) $purchase['data'];

        $rowStart = 2;
        for ($i = 0; $i < count($dataPurchases); $i++) {

            $Pengurangan_pembayaran = $dataPurchases[$i]->Pengurangan_pembayaran;
            $Pengurangan_pembayaran_string = str_replace(',', '', $Pengurangan_pembayaran);
            
            $Jumlah_pembayaran = $dataPurchases[$i]->Jumlah_pembayaran;
            $Jumlah_pembayaran_string = str_replace(',', '', $Jumlah_pembayaran);  

            $objPHPExcel->getActiveSheet()->setCellValue('A' . $rowStart, $i + 1);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $rowStart, $dataPurchases[$i]->Tanggal_transaksi);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $rowStart, $dataPurchases[$i]->ID_pemasok);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $rowStart, $dataPurchases[$i]->Nama_Pemasok);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $rowStart, $dataPurchases[$i]->Janjang);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $rowStart, $dataPurchases[$i]->Berat_Kotor);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $rowStart, $dataPurchases[$i]->Presentase_pemotongan);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $rowStart, $dataPurchases[$i]->Berat_bersih);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $rowStart, $dataPurchases[$i]->Harga_per_kilo);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $rowStart, $dataPurchases[$i]->Total);
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $rowStart, $Pengurangan_pembayaran_string);
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $rowStart, $Jumlah_pembayaran_string);
            $objPHPExcel->getActiveSheet()->setCellValue('M' . $rowStart, $dataPurchases[$i]->Ketelusuran);
           
            $rowStart++;
        }

        $objPHPExcel->getActiveSheet()->getStyle('A1:M1')->applyFromArray($styleFontBoldHeader);

        $objPHPExcel->getActiveSheet()->setCellValue("D$rowStart", "Total");

        $objPHPExcel->getActiveSheet()->setCellValue('E'.$rowStart, "=SUM(E2:E".($rowStart-1).")");

        $objPHPExcel->getActiveSheet()->setCellValue('F'.$rowStart, "=SUM(F2:F".($rowStart-1).")");

        $objPHPExcel->getActiveSheet()->setCellValue('H'.$rowStart, "=SUM(H2:H".($rowStart-1).")");

        $objPHPExcel->getActiveSheet()->setCellValue('J'.$rowStart, "=SUM(J2:J".($rowStart-1).")");

        $objPHPExcel->getActiveSheet()->setCellValue('K'.$rowStart, "=SUM(K2:K".($rowStart-1).")");

        $objPHPExcel->getActiveSheet()->setCellValue('L'.$rowStart, "=SUM(L2:L".($rowStart-1).")");
       
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('export_transactions.xlsx');
        ini_set('memory_limit', $mem_ini);
        $this->response(array('success' => TRUE, 'filenya' => base_url() . 'export_transactions.xlsx'), 200);
        exit;
    }

    public function fetch_api_purchase_report_get() 
    {
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';

        $mem_ini = ini_get('memory_limit');
        ini_set('memory_limit', '1048576M');

        $SID = $this->input->get('SID');
        
        //Get Data Purchase
        $purchase = $this->_model->get_data_api_purchase_report($SID);
        
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Purchases');

        $objPHPExcel->getActiveSheet()
        ->getStyle('A:M')
        ->getAlignment()
        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        $objPHPExcel->getActiveSheet()->getStyle('J:L')
        ->getNumberFormat()
        ->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

        $styleFontBoldHeader = array(
            'font' => array(
                'name' => 'Arial',
                'size' => '11',
                'bold' => true,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '95130b'),
            ),
        );

        $styleFont = array(
            'font' => array(
                'name' => 'Arial',
                'size' => '11'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID
            ),
        );

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'No');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Tanggal Transaksi');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'ID Pemasok');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Nama Pemasok');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Janjang');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Berat Kotor');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Presentase Pemotongan');
        $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Berat Bersih');
        $objPHPExcel->getActiveSheet()->setCellValue('I1', 'Harga Per Kilo');
        $objPHPExcel->getActiveSheet()->setCellValue('J1', 'Total');
        $objPHPExcel->getActiveSheet()->setCellValue('K1', 'Pengurangan Pembayaran');
        $objPHPExcel->getActiveSheet()->setCellValue('L1', 'Jumlah Pembayaran');
        $objPHPExcel->getActiveSheet()->setCellValue('M1', 'Ketelusuran');

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(30); 

        $dataPurchases = (array) $purchase['data'];

        $rowStart = 2;
        for ($i = 0; $i < count($dataPurchases); $i++) {

            $Pengurangan_pembayaran = $dataPurchases[$i]->Pengurangan_pembayaran;
            $Pengurangan_pembayaran_string = str_replace(',', '', $Pengurangan_pembayaran);
            
            $Jumlah_pembayaran = $dataPurchases[$i]->Jumlah_pembayaran;
            $Jumlah_pembayaran_string = str_replace(',', '', $Jumlah_pembayaran);  
            
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $rowStart, $i + 1);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $rowStart, $dataPurchases[$i]->Tanggal_transaksi);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $rowStart, $dataPurchases[$i]->ID_pemasok);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $rowStart, $dataPurchases[$i]->Nama_Pemasok);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $rowStart, $dataPurchases[$i]->Janjang);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $rowStart, $dataPurchases[$i]->Berat_Kotor);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $rowStart, $dataPurchases[$i]->Presentase_pemotongan);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $rowStart, $dataPurchases[$i]->Berat_bersih);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $rowStart, $dataPurchases[$i]->Harga_per_kilo);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $rowStart, $dataPurchases[$i]->Total);
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $rowStart, $Pengurangan_pembayaran_string);
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $rowStart, $Jumlah_pembayaran_string);
            $objPHPExcel->getActiveSheet()->setCellValue('M' . $rowStart, $dataPurchases[$i]->Ketelusuran);
           
            $rowStart++;
        }

        $objPHPExcel->getActiveSheet()->getStyle('A1:M1')->applyFromArray($styleFontBoldHeader);

        $objPHPExcel->getActiveSheet()->setCellValue('F'.$rowStart, "=SUM(F2:F".($rowStart-1).")");

        $objPHPExcel->getActiveSheet()->setCellValue('H'.$rowStart, "=SUM(H2:H".($rowStart-1).")");

        $objPHPExcel->getActiveSheet()->setCellValue('J'.$rowStart, "=SUM(J2:J".($rowStart-1).")");

        $objPHPExcel->getActiveSheet()->setCellValue('K'.$rowStart, "=SUM(K2:K".($rowStart-1).")");

        $objPHPExcel->getActiveSheet()->setCellValue('L'.$rowStart, "=SUM(L2:L".($rowStart-1).")");

        //transaction start
        $sales = $this->_model->get_data_api_sales_report($SID);
        $myWorkSheet = new PHPExcel_Worksheet($objPHPExcel, 'Sales');

        // Attach the "My Data" worksheet as the first worksheet in the PHPExcel object
        $objPHPExcel->addSheet($myWorkSheet, 0);

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        //set width column
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30); 

        $objPHPExcel->getActiveSheet()
        ->getStyle('A:L')
        ->getAlignment()
        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        $styleFontBoldHeader = array(
            'font' => array(
                'name' => 'Arial',
                'size' => '11',
                'bold' => true,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '95130b'),
            ),
        );

        $styleFont = array(
            'font' => array(
                'name' => 'Arial',
                'size' => '11'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID
            ),
        );

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'No');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Tanggal Pengiriman');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Nama');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Tujuan Mill');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Berat kotor pengiriman');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Berat bersih dijual');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Total harga');

        $rowStart = 2;
        for ($i = 0; $i < count($sales['data']); $i++) {
            $data = (array) $sales['data'];
            
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $rowStart, $i + 1);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $rowStart, $data[$i]->Tanggal_pengiriman);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $rowStart, $data[$i]->Nama_agen);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $rowStart, $data[$i]->Tujuan_Mill);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $rowStart, $data[$i]->Berat_kotor_pengiriman);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $rowStart, $data[$i]->Berat_bersih_dijual);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $rowStart, $data[$i]->Total_harga);
           
            $rowStart++;
        }

        $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->applyFromArray($styleFontBoldHeader);

        $objPHPExcel->getActiveSheet()->setCellValue('E'.$rowStart, "=SUM(E2:E".($rowStart-1).")");

        $objPHPExcel->getActiveSheet()->setCellValue('F'.$rowStart, "=SUM(F2:F".($rowStart-1).")");

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save( date('YmdHis') . '_export_transactions.xlsx');
        ini_set('memory_limit', $mem_ini);

        $namaFile = date('YmdHis') . '_export_transactions.xlsx';
        $filePath = base_url(). 'files/tmp/' . $namaFile;

        $this->response(array('success' => TRUE, 'filenya' => $filePath), 200);
        exit;
        
    }	

    public function fetch_api_sales_report_get() 
    {
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';

        $mem_ini = ini_get('memory_limit');
        ini_set('memory_limit', '1048576M');

        $SID = $this->input->get('SID');
        
        //Get Data Purchase
        $purchase = $this->_model->get_data_api_purchase_report($SID);
        
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Purchases');

        $objPHPExcel->getActiveSheet()
        ->getStyle('A:M')
        ->getAlignment()
        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        $objPHPExcel->getActiveSheet()->getStyle('J:L')
        ->getNumberFormat()
        ->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

        $styleFontBoldHeader = array(
            'font' => array(
                'name' => 'Arial',
                'size' => '11',
                'bold' => true,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '95130b'),
            ),
        );

        $styleFont = array(
            'font' => array(
                'name' => 'Arial',
                'size' => '11'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID
            ),
        );

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'No');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Tanggal Transaksi');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'ID Pemasok');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Nama Pemasok');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Janjang');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Berat Kotor');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Presentase Pemotongan');
        $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Berat Bersih');
        $objPHPExcel->getActiveSheet()->setCellValue('I1', 'Harga Per Kilo');
        $objPHPExcel->getActiveSheet()->setCellValue('J1', 'Total');
        $objPHPExcel->getActiveSheet()->setCellValue('K1', 'Pengurangan Pembayaran');
        $objPHPExcel->getActiveSheet()->setCellValue('L1', 'Jumlah Pembayaran');
        $objPHPExcel->getActiveSheet()->setCellValue('M1', 'Ketelusuran');

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(30); 

        $dataPurchases = (array) $purchase['data'];

        $rowStart = 2;
        for ($i = 0; $i < count($dataPurchases); $i++) {

            $Pengurangan_pembayaran = $dataPurchases[$i]->Pengurangan_pembayaran;
            $Pengurangan_pembayaran_string = str_replace(',', '', $Pengurangan_pembayaran);
            
            $Jumlah_pembayaran = $dataPurchases[$i]->Jumlah_pembayaran;
            $Jumlah_pembayaran_string = str_replace(',', '', $Jumlah_pembayaran);  
            
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $rowStart, $i + 1);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $rowStart, $dataPurchases[$i]->Tanggal_transaksi);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $rowStart, $dataPurchases[$i]->ID_pemasok);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $rowStart, $dataPurchases[$i]->Nama_Pemasok);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $rowStart, $dataPurchases[$i]->Janjang);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $rowStart, $dataPurchases[$i]->Berat_Kotor);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $rowStart, $dataPurchases[$i]->Presentase_pemotongan);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $rowStart, $dataPurchases[$i]->Berat_bersih);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $rowStart, $dataPurchases[$i]->Harga_per_kilo);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $rowStart, $dataPurchases[$i]->Total);
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $rowStart, $Pengurangan_pembayaran_string);
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $rowStart, $Jumlah_pembayaran_string);
            $objPHPExcel->getActiveSheet()->setCellValue('M' . $rowStart, $dataPurchases[$i]->Ketelusuran);
           
            $rowStart++;
        }

        $objPHPExcel->getActiveSheet()->getStyle('A1:M1')->applyFromArray($styleFontBoldHeader);

        $objPHPExcel->getActiveSheet()->setCellValue("D$rowStart", "Total");

        $objPHPExcel->getActiveSheet()->setCellValue('E'.$rowStart, "=SUM(E2:E".($rowStart-1).")");

        $objPHPExcel->getActiveSheet()->setCellValue('F'.$rowStart, "=SUM(F2:F".($rowStart-1).")");

        $objPHPExcel->getActiveSheet()->setCellValue('H'.$rowStart, "=SUM(H2:H".($rowStart-1).")");

        $objPHPExcel->getActiveSheet()->setCellValue('J'.$rowStart, "=SUM(J2:J".($rowStart-1).")");

        $objPHPExcel->getActiveSheet()->setCellValue('K'.$rowStart, "=SUM(K2:K".($rowStart-1).")");

        $objPHPExcel->getActiveSheet()->setCellValue('L'.$rowStart, "=SUM(L2:L".($rowStart-1).")");

        //transaction start
        $sales = $this->_model->get_data_api_sales_report($SID);
        $myWorkSheet = new PHPExcel_Worksheet($objPHPExcel, 'Sales');

        // Attach the "My Data" worksheet as the first worksheet in the PHPExcel object
        $objPHPExcel->addSheet($myWorkSheet, 0);

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        //set width column
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(30); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30); 

        $objPHPExcel->getActiveSheet()
        ->getStyle('A:L')
        ->getAlignment()
        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        $styleFontBoldHeader = array(
            'font' => array(
                'name' => 'Arial',
                'size' => '11',
                'bold' => true,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '95130b'),
            ),
        );

        $styleFont = array(
            'font' => array(
                'name' => 'Arial',
                'size' => '11'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID
            ),
        );

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'No');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Tanggal Pengiriman');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Nama');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Tujuan Mill');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Berat Kotor Pengiriman');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Berat Bersih di Jual');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Total harga');

        $rowStart = 2;
        for ($i = 0; $i < count($sales['data']); $i++) {
            $data = (array) $sales['data'];
            
            $Total_harga = $data[$i]->Total_harga;
            $Total_harga = str_replace(',', '', $Total_harga);
            
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $rowStart, $i + 1);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $rowStart, $data[$i]->Tanggal_pengiriman);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $rowStart, $data[$i]->Nama_agen);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $rowStart, $data[$i]->Tujuan_Mill);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $rowStart, $data[$i]->Berat_kotor_pengiriman);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $rowStart, $data[$i]->Berat_bersih_dijual);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $rowStart, $Total_harga);
           
            $rowStart++;
        }

        $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->applyFromArray($styleFontBoldHeader);

        $objPHPExcel->getActiveSheet()->setCellValue("D$rowStart", "Total");

        $objPHPExcel->getActiveSheet()->setCellValue('E'.$rowStart, "=SUM(E2:E".($rowStart-1).")");

        $objPHPExcel->getActiveSheet()->setCellValue('F'.$rowStart, "=SUM(F2:F".($rowStart-1).")");

        $objPHPExcel->getActiveSheet()->setCellValue('G'.$rowStart, "=SUM(G2:G".($rowStart-1).")");
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $namaFile = date('YmdHis') . '_export_transaction.xlsx';
        $objWriter->save($namaFile);
        ini_set('memory_limit', $mem_ini);
        
        $this->response(array('success' => TRUE, 'filenya' => base_url() . $namaFile), 200);
        exit;
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

        // echo "<pre>";
        // var_dump($paramPost);
        // die('succes connect submit_payment_post');
        
        $proses = $this->_model->SubmitPayment($paramPost);

        if($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }
}