<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Reception extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        ini_set('memory_limit', '-1');
        $this->load->model('traceability_api/m_reception','_model');
    }

    public function fetch_data_get() 
    {
        //sort
        $sorting      = json_decode($this->get('sort'));
        if (isset($sorting[0]->property)) $sortingField = $sorting[0]->property; else $sortingField = null;
        if (isset($sorting[0]->direction)) $sortingDir = $sorting[0]->direction; else $sortingDir = null;
        $start        = (int) $this->get('start');
        $limit        = (int) $this->get('limit');

        $pSearch = array(
            'ArrFilter'                            => @$this->get('ArrFilter'),
            'Keyword'                              => filter_var(@$this->get('TextFilterKeyword'), FILTER_SANITIZE_STRING),
            'WarehouseID'                          => filter_var(@$this->get('TextilterWarehouseID'), FILTER_SANITIZE_STRING),
            'TextFilterStartDeliveryDate'          => filter_var(preg_replace("([^0-9-])","",@$this->get('TextFilterStartShipmentDate'))),
            'TextFilterEndDeliveryDate'            => filter_var(preg_replace("([^0-9-])","",@$this->get('TextFilterEndShipmentDate'))),
        );
        
        $data = $this->_model->GetGridMain($pSearch,$start,$limit,$sortingField,$sortingDir);

        $this->response($data, 200);
    }

    function fetch_combo_package_get() {
        $data = $this->_model->readComboPackage();
        $this->response($data, 200);
    }

    function fetch_batch_data_get() {
        $data = $this->_model->getDataBatch($this->get('DeliveryID'));
        $this->response($data, 200);
    }

    function reception_detail_get() {
        $data = $this->_model->getDataTransaction($this->get());
        $this->response($data, 200);
    }

    function submit_reception_post() {
        $data = $this->_model->submit_reception($this->post());

        if ($data){
          
            $curlTable = array( 'TableName' => 'ktv_tc_supplychain_transaction_detail', 'TableField' => 'TransDetailID', 'TableID' => @$data['TransDetailID'] );

            $version = !empty(@$data['Version']) ? @$data['Version']: null;
                        
            $param = checkLogTraceability($version, 'POST', current_url(), $this->post(), $curlTable);

            $this->response($data, 200);
        }
        else{
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }

    function fetch_combo_warehouse_get() {
        $data = $this->_model->readComboWarehouse();
        $this->response($data, 200);
    }

    function batch_qrcode_get($batch_id,$SupplyOrgID,$SupplyBatchNumber=""){
        $collector = $SupplyOrgID."-".$SupplyBatchNumber;
        $qrcode = base_url('assets/qrcode/batchid-'.$batch_id.'-'.$collector.'.png');
        if(file_exists($qrcode)){
            $filename = "batchid-".$batch_id.'-'.$collector.".png";
        }else{
            $filename = $this->generate_qr_code("batchid",$batch_id,$SupplyOrgID,$SupplyBatchNumber);
        }
        
        $data = array(
                "filename" => $filename
            );
        echo $this->load->view('qrcode/cetak_qrcode', $data); 
    }

    function trans_qrcode_get($trans_id,$SupplyID,$FakturNumber=""){
        
        $collector = $SupplyID."-".$FakturNumber;
        $qrcode = base_url('assets/qrcode/'.$trans_id.'-'.$collector.'.png');
        if(file_exists($qrcode)){
            $filename = "transid-".$trans_id.'-'.$collector.".png";
        }else{
            $filename = $this->generate_qr_code("transid",$trans_id,$SupplyID,$FakturNumber);
        }
        
        $data = array(
                "filename" => $filename
            );
        echo $this->load->view('qrcode/cetak_qrcode', $data); 
    }

    public function generate_qr_code($param,$id,$org_id=null,$Number=null)
    {
        $filename = $param."-".$id."-".$org_id."-".$Number.'.png';
        $fullpath   = APPPATH.'../assets/qrcode/'.$filename;
        $logopath   = APPPATH.'../assets/images/koltiva-circle.png';
            $this->load->library('ciqrcode');

            $config['cacheable']    = false; //boolean, the default is true
            $config['cachedir']     = './assets/'; //string, the default is application/cache/
            $config['errorlog']     = './assets/'; //string, the default is application/logs/
            $config['imagedir']     = './assets/qrcode/'; //direktori penyimpanan qr code
            $config['quality']      = true; //boolean, the default is true
            $config['size']         = '1024'; //interger, the default is 1024
            $config['black']        = array(224,255,255); // array, default is array(255,255,255)
            $config['white']        = array(70,130,180); // array, default is array(0,0,0)
            $this->ciqrcode->initialize($config);

            $id_encode = base64_encode($id);
            $org_encode = base64_encode($org_id);
            $base_url = base_url();
            $app = strpos($base_url,"app");
            if($app>0){
                $env ="app";
            }else{
                $env ="demo";
            }

            $params['data']  = "https://qr.cocoatrace.com?env=".$env."&".$param."=".$id_encode;
            $params['level'] = 'H';
            $params['size'] = 10;
            $params['savename'] = $fullpath;
            $this->ciqrcode->generate($params);

            $QR = imagecreatefrompng($fullpath);
            // memulai menggambar logo dalam file qrcode
            $logo = imagecreatefromstring(file_get_contents($logopath));
            imagecolortransparent($logo , imagecolorallocatealpha($logo , 0, 0, 0, 127));
            imagealphablending($logo , false);
            imagesavealpha($logo , true);
            $QR_width = imagesx($QR);//get logo width
            $QR_height = imagesy($QR);//get logo width

            $logo_width = imagesx($logo);
            $logo_height = imagesy($logo);
            // Scale logo to fit in the QR Code
            $logo_qr_width = $QR_width/4;
            $scale = $logo_width/$logo_qr_width;
            $logo_qr_height = $logo_height/$scale;

            imagecopyresampled($QR, $logo, $QR_width/2.6, $QR_height/2.6, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
            // Simpan kode QR lagi, dengan logo di atasnya
            imagepng($QR,$fullpath);
        return $filename;
    }

    function fetch_combo_ref_unit_get() {
        $data = $this->_model->readComboRefUnitType();
        $this->response($data, 200);
    }

    public function data_delivery_receiving_get()
    {
        $TransDetailID = (int) $this->get('TransDetailID');
        $data          = $this->_model->GetDeliveryReceiving($TransDetailID);
        $this->response($data, 200);
    }

    // public function data_receiving_input_post()
    // {
    //     $return    = array();
    //     $varPost   = $this->post();
    //     $paramPost = array();
        
    //     foreach ($varPost as $key => $value) {
    //         $keyNew = str_replace("Koltiva_view_Traceability_Reception_WinFormDeliveryReceiving-Form-", '', $key);

    //         $paramPost[$keyNew] = $value;
    //     }
    //     if($paramPost['OpsiDisplay'] == 'insert') {
    //         $proses = $this->_model->InsertDeliveryReceiving($paramPost);
    //     } else {
    //         $checkStatusDeliveryReceiving = $this->checkStatusDeliveryReceiving($paramPost['Koltiva_view_Traceability_new_Reception_WinFormDeliveryReceiving-Form-TransDetailID']);
    //         if ($checkStatusDeliveryReceiving == 1) {
    //             $proses = $this->_model->UpdateDeliveryReceiving($paramPost);
    //         } else {
    //             $proses['success'] = false;
    //             $proses['message'] = lang("Failed to update data , your item has been process or delivery");
    //         }
    //     }

    //     if($proses['success'] == true) {
    //         $this->response($proses, 200);
    //     } else {
    //         $this->response($proses, 400);
    //     }
    // }

    public function data_receiving_input_post()
    {
        $return    = array();
        $varPost   = $this->post();
        $paramPost = array();
        
        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Traceability_Reception_WinFormDeliveryReceiving-Form-", '', $key);

            $paramPost[$keyNew] = $value;
        }
        if($paramPost['OpsiDisplay'] == 'insert') {
            $proses = $this->_model->InsertDeliveryReceiving($paramPost);
        } else {
            $checkStatusDeliveryReceiving = $this->checkStatusDeliveryReceiving($paramPost['Koltiva_view_Traceability_new_Reception_WinFormDeliveryReceiving-Form-TransDetailID']);
            if ($checkStatusDeliveryReceiving == 1) {
                $proses = $this->_model->UpdateDeliveryReceiving($paramPost);
            } else {
                $proses['success'] = false;
                $proses['message'] = lang("Failed to update data , your item has been process or delivery");
            }
        }

        if($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function data_delivery_receiving_main_grid_get()
    {
        $SupplyTransID = (int) $this->get('SupplyTransID');
        $data          = $this->_model->GetDeliveryReceivingMainGrid($SupplyTransID);

        $this->response($data, 200);
    }

    public function data_delivery_receiving_delete() 
    {
        $varDelete = $this->delete();

        $checkStatusDeliveryReceiving = $this->checkStatusDeliveryReceiving($varDelete['TransDetailID']);

        if ($checkStatusDeliveryReceiving == 0) {
            $proses = $this->_model->DeleteDeliveryReceiving($varDelete);
        } else {
            $proses['success'] = false;
            $proses['message'] = lang("Failed to delete data , your item has been process or delivery");
        }

        if($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }

    }

    public function checkStatusDeliveryReceiving($TransDetailID)
    {
        $this->db->where('TransDetailID', (int) $TransDetailID);

        $check = $this->db->get('ktv_tc_supplychain_transaction_detail')->row();

        $status = 0;

        if ($check) {
            $status = 1;
        }

        return $status;
    }

    public function fetch_api_get() 
    {   
        $SID = $_GET['SID'];

        if($SID){
            $data = $this->_model->GetReception($SID);

            return $this->response(array('success' => true,
            'message' => 'Data Berhasil di tampilkan',
            'results' => $data), 200);
        } else {
            return $this->response(array('failed' => true,
            'message' => 'Data Gagal di tampilkan',
            'results' => $data), 400);
        }

        return $this->response($this->_output, 401);
    }

    public function submit_api_post() 
    {
        $post = json_decode(json_encode($this->post()), true);
        if(empty($data)) {
            $post = json_decode(file_get_contents('php://input'), true);
            $data = $post;
        }
        
        $name = $data['SupplychainID'] . '-' . strtotime(date('YmdHis')). '-Trans-' . $data['TransNumber'];
        $dir = FCPATH . 'backup_traceability_submit_reception';
        if(!is_dir($dir)) {
          make_directory($dir, 0777, true);
        }
        if(!write_file($dir.'/'.$name.'.json',json_encode($data))) {} else {}
        
        if ($data) {
            $reception = $this->_model->submit_reception_api_post($data);
            if ($reception) {
                $curlTable = array( 'TableName' => 'ktv_tc_supplychain_transaction_detail', 'TableField' => 'TransDetailID', 'TableID' => @$data['TransDetailID'] );

                $version = !empty(@$data['Version']) ? @$data['Version']: null;
                            
                $param = checkLogTraceability($version, 'POST', current_url(), $this->post(), $curlTable);
    
                return $this->response($reception);
            }
        } else {
            return $this->response(array('success' => false, 'error' => 'Data post empty !'), 401);
        }
    }

    public function submit_accept_delivery_post() 
    {
        $post = json_decode(json_encode($this->post()), true);
        if(empty($data)) {
            $post = json_decode(file_get_contents('php://input'), true);
            $data = $post;
        }
        
        $name = $data['DeliveryID'] . '-' . strtotime(date('YmdHis')). '-receipt-';
        $dir = FCPATH . 'backup_traceability_submit_accept_delivery';
        if(!is_dir($dir)) {
          make_directory($dir, 0777, true);
        }
        if(!write_file($dir.'/'.$name.'.json',json_encode($data))) {} else {}
        
        if ($data) {
            $reception = $this->_model->submit_accept_delivery_post($data);
            if ($reception) {
                return $this->response($reception);
            }
        } else {
            return $this->response(array('success' => false, 'error' => 'Data post empty !'), 401);
        }
    }

    public function payment_instruction_get() {
        $data = $this->_model->getPaymentInstruction($this->get());
        $this->response($data, 200); // 200 being the HTTP response code
    }

    public function check_payment_status_get() {
        $data = $this->_model->CheckPaymentStatus($this->get());
        $this->response($data, 200); // 200 being the HTTP response code
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

}