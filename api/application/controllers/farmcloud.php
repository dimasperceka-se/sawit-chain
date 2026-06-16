<?php

/**
 * @Author: Gitandi Nadzari
 * @Date:   2018-09-10 16:20:00
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Farmcloud extends REST_Controller {
    public function __construct() {
        parent::__construct();
        // $this->load->model('data_adm/m_metadata');
		 $this->load->model('farmcloud/mmessages');
		 $this->load->model('farmcloud/muser');
    }

    public function form_user_view_get(){
        $PersonExtID =  $this->get("PersonExtID");

        $data = $this->muser->GetFormUser($PersonExtID);
        
        $this->response($data, 200);
    }

    public function reset_user_post(){
        if($this->post("username") == ""){
            $result["success"] = false;
            $result["message"] = "Username Empty";
            $this->response($result, 200);
            return;
        }

        $rs = $this->get_content_rest('http://live3.koltiva.com:8280/reset_password/'.$this->post("username"));
        $result = json_decode($rs);
        $result["success"] = true;

        $this->response($result, 200);
    }

    public function disable_user_post(){
        if($this->post("username") == ""){
            $result["success"] = false;
            $result["message"] = "Username Empty";
            $this->response($result, 200);
            return;
        }

        $postData["_postuser_status"] = array(
            "username"    => $this->post("username"),
            "status"      => 1
        );

        $header = array(
            "Accept: application/json",
            "Content-type: application/json"
        );

        $url = 'http://live3.koltiva.com:8280/user_status';

        $rs = $this->register_content_rest($url,$postData,$header);
        $result = json_decode($rs);
        $result["success"] = true;
        $this->response($result, 200);
    }

    public function register_user_post(){
        $paramPost = $this->post();

        $postData["_postuser_info"] = array(
            "v_username"    => $paramPost["Email"],
            "v_imei"        => '1234567890123456',
            "v_handphone"   => $paramPost["Handphone"],
            "v_fullname"    => $paramPost["PersonName"],
        );

        $header = array(
            "Accept: application/json",
            "Authorization: Basic ZnVycW9udWRkaW4ucmFtZGhhbmlAZ21haWwuY29tOjczMTEwMDI4OQ==",
            "Imei: 1234567890123456",
            "Content-type: application/json"
        );

        $url = 'http://cygnus.koltiva.com:8281/registration';

        $rs = $this->register_content_rest($url,$postData,$header);
        $result = json_decode($rs);
        if(!$rs){
            $results['success'] = false;
            $results['message'] = "Connection Error";
            $this->response($results, 200);
            return;
        }
        if($result->data->registration == "failed"){
            $results['success'] = false;
            $results['message'] = "Registration Failed";
            $this->response($results, 200);
            return;
        }
        
        $results['success'] = true;
        $results['message'] = "Saved";
        $this->response($results, 200);
    }

    public function grid_main_get()
    {
        $textSearch = $this->get('textSearch');
        
        $data = $this->muser->GetGridMainUser($textSearch,$this->get('start'), $this->get('limit'));

        return $this->response($data,200);
    }

    public function get_contact_list_messages_get(){
        $MessagesID = (int) $this->get('MessagesID');
        $data = $this->mmessages->getContactListMessages($MessagesID); 
        return $this->response($data, 200);
    }

    public function grid_main_messages_get(){
        $data = $this->mmessages->GetGridMainMessages($this->get('start'), $this->get('limit'));
        $this->response($data, 200);
    }

    function messages_get(){
        $MessagesID = (int) $this->get('MessagesID');
        $data = $this->mmessages->getMessages($MessagesID);   
        $this->response($data, 200);
    }

    function messages_post(){
        $ParamPost   = $this->post();

        if($ParamPost['MessagesID'] == ""){
            $proses = $this->mmessages->InsertMessages($ParamPost);
        }else{
            $proses = $this->mmessages->UpdateMessages($ParamPost);
        }

        if($proses['success'] == true){
            $this->response($proses,200);
        }else{
            $this->response($proses,400);
        }
    }

    function messages_delete(){
        $ParamPost   = $this->delete();
        $proses = $this->mmessages->DeleteMessages($ParamPost["MessagesID"]);        

        if($proses['success'] == true){
            $this->response($proses,200);
        }else{
            $this->response($proses,400);
        }
    }

    function register_content_rest($url = null,$data = null,$header = null){        
        $postData = json_encode($data);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_VERBOSE, false);
		// curl_setopt($ch, CURLOPT_NOBODY, true);        
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch,CURLOPT_POSTFIELDS,$postData);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_ENCODING, true);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);

		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36");
		$rs = curl_exec($ch);

		if(empty($rs)){
			//var_dump($rs, curl_error($ch)); komen dl karena veracode
			curl_close($ch);
			return false;
		}
        curl_close($ch);
        return $rs;
    }

    function get_content_rest($url = null){
		$header[] = 'Accept: application/json';
		$header[] = "Accept-Encoding: gzip, deflate";
		$header[] = "Cache-Control: max-age=0";
		$header[] = "Connection: keep-alive";
		$header[] = "Accept-Language: en-US,en;q=0.8,id;q=0.6";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_VERBOSE, false);
		// curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_ENCODING, true);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 5);

		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36");

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$rs = curl_exec($ch);

		if(empty($rs)){
			//var_dump($rs, curl_error($ch)); komen dl karena veracode
			curl_close($ch);
			return false;
		}
        curl_close($ch);
        return $rs;
    }

    public function useracc_grid_main_get() {
        //sort
        $sorting      = json_decode($this->get('sort'));
        if (isset($sorting[0]->property)) $sortingField = $sorting[0]->property;
        else $sortingField = null;
        if (isset($sorting[0]->direction)) $sortingDir = $sorting[0]->direction;
        else $sortingDir = null;
        $start        = (int) $this->get('start');
        $limit        = (int) $this->get('limit');

        $pSearch = array();
        $pSearch['KeySearch'] = filter_var($this->get('KeySearch'), FILTER_SANITIZE_STRING);

        //echo '<pre>'; print_r($pSearch); exit;
        $data = $this->muser->GetUseraccGridMain($pSearch,$start,$limit,$sortingField,$sortingDir, 'limit');
        $this->response($data, 200);
    }
    
    private function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
    
    public function useracc_grid_export_post() {
        ini_set('memory_limit', '-1');
        
        //sort
        $sorting      = json_decode($this->post('sort'));
        if (isset($sorting[0]->property)) $sortingField = $sorting[0]->property;
        else $sortingField = null;
        if (isset($sorting[0]->direction)) $sortingDir = $sorting[0]->direction;
        else $sortingDir = null;
        
        $pSearch = array();
        $pSearch['KeySearch'] = filter_var($this->post('KeySearch'), FILTER_SANITIZE_STRING);

        //echo '<pre>'; print_r($pSearch); exit;
        $dataList = $this->muser->GetUseraccGridMain($pSearch, '', '', $sortingField, $sortingDir, 'no_limit')['data'];
        
        //generate nama file excel
        $SqlvName = "Export User Account Management FarmCloud ";
        $sqlViewName = str_replace(' ','-',$SqlvName);

        //Strip character spesial
        $sqlViewName = preg_replace('/[^A-Za-z0-9\-]/', '', $sqlViewName);

        //generate data header
        $dataHeader = array('No.');
        foreach ($dataList[0] as $key => $value) {
            if ($key == "Photo" OR $key == "DateUpdated") {
                continue;
            } else {
                $dataHeader[] = $key;
            }
        }

        //generate data list
        $dataListExcel = array();
        foreach ($dataList as $key => $value) {
            array_unshift($value, ($key + 1));
            foreach ($value as $key1 => $value1) {
                if ($key1 === "Photo" OR $key1 === "DateUpdated") {
                    continue;
                } else {
                    $dataListExcel[$key][] = $value1;
                }
            }
        }
        //ambil data  (end)
        
        $writer = WriterEntityFactory::createXLSXWriter();
        $filePath = 'files/tmp/'.$sqlViewName.date('Y-m-d').'.xlsx';
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

        //row data
        for ($i=0; $i < count($dataListExcel); $i++) {
            $dataRows = $dataListExcel[$i];
            $cells = array();

            for ($j=0; $j < count($dataRows); $j++) {
                $styleRow = null;
                $dataRow = null;

                //cek apakah numeric
                if(is_numeric($dataRows[$j])){
                    $num_length = strlen((string) $dataRows[$j]);
                    if ($num_length >= 10) {
                        $styleRow = $styleData;
                        $dataRow = (string) $dataRows[$j];
                    } else {
                        $styleRow = $styleFormatAngka;
                        $dataRow = (float) $dataRows[$j];
                    }
                } else {
                    //cek apakah tanggal
                    if($this->validateDate($dataRows[$j]) == true) {
                        $styleRow = $styleFormatTanggal;
                        $dataRow = $dataRows[$j];
                    } else {
                        $styleRow = $styleData;
                        $dataRow = lang($dataRows[$j]);
                    }
                }

                $cells[] = WriterEntityFactory::createCell($dataRow, $styleRow);
            }

            $rowData = WriterEntityFactory::createRow($cells);
            $writer->addRow($rowData);
        }

        $writer->close();

        $this->response(array('success' => TRUE, 'filenya' => base_url() . $filePath), 200);
        exit;
    }

    public function cmb_auto_farmer_search_get() {
        $queryString = filter_var($this->get('query'),FILTER_SANITIZE_STRING);
        $start = (int) $this->get('start');
        $limit = (int) $this->get('limit');

        $data = $this->muser->GetComboAutoFarmerSearch($queryString,$start,$limit);
        $this->response($data, 200);
    }

    public function account_new_register_post() {
        $this->load->model('staff/mstaffuser_cognito');
        $proses = array();
        $varPost = $this->post();
        $paramPost = array();

        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_FarmCloud_UseraccManagement_WinFormRegisAccount-Form-", '', $key);
            if ($value == "") {
                $value = null;
            }
            $paramPost[$keyNew] = $value;
        }
        //echo '<pre>'; print_r($paramPost); exit;

        //Cek Password
        if($paramPost['UserPassword'] != $paramPost['UserPasswordRe']) {
            $proses['success'] = false;
            $proses['message'] = lang('Password did not match');
            $this->response($proses, 400);
        } else {
            $cekValidasiPassword = cekValidasiPassword($paramPost['UserPassword']);
            if($cekValidasiPassword['success'] == false) {
                $proses['success'] = false;
                $proses['message'] = $cekValidasiPassword['message'];
                $this->response($proses, 400);
            }
        }

        $proses = $this->mstaffuser_cognito->FarmCloudRegisterNewAccount($paramPost);

        if($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function user_account_change_passwd_post() {
        $this->load->model('staff/mstaffuser_cognito');
        $varPost = $this->post();
        $paramPost = array();

        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_FarmCloud_UseraccManagement_WinFormChangePassword-Form-", '', $key);
            if ($value == "") {
                $value = null;
            }
            $paramPost[$keyNew] = $value;
        }
        //echo '<pre>'; print_r($paramPost); exit;

        //Cek validasi password (Begin)
        $cekValidasiPassword = cekValidasiPassword($paramPost['UserPassword']);
        if($cekValidasiPassword['success'] == false) {
            $proses['success'] = false;
            $proses['message'] = $cekValidasiPassword['message'];
            $this->response($proses, 400);
        }
        //Cek validasi password (End)

        $proses = $this->mstaffuser_cognito->ChangePasswordUserAccountFarmCloud($paramPost);
        if($proses['success']) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function disable_account_post() {
        $this->load->model('staff/mstaffuser_cognito');
        $proses = array();
        $FarmerID = (int) $this->post('FarmerID');
        $Username = filter_var($this->post('Username'), FILTER_SANITIZE_STRING);

        $proses = $this->mstaffuser_cognito->DisableAccountFarmCloud($FarmerID, $Username);
        if($proses['success']) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function enable_account_post() {
        $this->load->model('staff/mstaffuser_cognito');
        $proses = array();
        $FarmerID = (int) $this->post('FarmerID');
        $Username = filter_var($this->post('Username'), FILTER_SANITIZE_STRING);

        $proses = $this->mstaffuser_cognito->EnableAccountFarmCloud($FarmerID, $Username);
        if($proses['success']) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function delete_account_post() {
        $this->load->model('staff/mstaffuser_cognito');
        $proses = array();
        $FarmerID = (int) $this->post('FarmerID');
        $Username = filter_var($this->post('Username'), FILTER_SANITIZE_STRING);

        $proses = $this->mstaffuser_cognito->DeleteAccountFarmCloud($FarmerID, $Username);
        if($proses['success']) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function user_check_in_congnito_post() {
        $this->load->model('staff/mstaffuser_cognito');
        $proses = array();
        $Username = filter_var($this->post('Username'), FILTER_SANITIZE_STRING);

        $proses = $this->mstaffuser_cognito->CheckUserInIncognito($Username);

        if($proses['success']) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 200);
        }
    }
}
?>