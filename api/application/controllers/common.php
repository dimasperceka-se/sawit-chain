<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * @package API
 * @author Ardi <ardiantoro@koltiva.com>
 */
class Common extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('mcommon', '_model');
    }
	
    public function cmb_auto_staff_get() {
        $queryString = filter_var($this->get('query'),FILTER_SANITIZE_STRING);
        $start = (int) $this->get('start');
        $limit = (int) $this->get('limit');

        $data = $this->_model->GetComboAutoStaff($queryString,$start,$limit);
        $this->response($data, 200);
    }

    public function cmb_auto_farmer_get() {
        $queryString = filter_var($this->get('query'),FILTER_SANITIZE_STRING);
        $start = (int) $this->get('start');
        $limit = (int) $this->get('limit');

        $data = $this->_model->GetComboAutoFarmer($queryString,$start,$limit);
        $this->response($data, 200);
    }

    public function combo_cluster_get(){
        $data = $this->_model->combo_cluster($this->get("ProgID"), $this->get("wave"));
        $this->response($data, 200);
    }

    public function combo_transactionstatus_get(){
        $data = $this->_model->combo_transactionstatus();
        $this->response($data, 200);
    }

    public function getcombo_get()
    {

        $table   = $this->get('table');
        $value   = $this->get('id');
        $display = $this->get('name');
        $query   = $this->get('query');
        $val     = $this->get('val');

        $data          = $this->_model->getCombo($table, $value, $display, $query, $val);
        $this->_num    = 200;
        $this->_output = array('success' => true, 'data' => $data, 'total' => count($data));

        return $this->response($this->_output, $this->_num);
    }

    public function getcolumn_get()
    {
        $table   = $this->get('table');
        $value   = $this->get('id');
        $display = $this->get('name');

        $cols = $this->_model->getColumns($table, $value, $display);

        $this->_num    = 200;
        $this->_output = $cols;

        return $this->response($this->_output, $this->_num);
    }

    public function cmb_menu_category_get(){
        $data = $this->_model->GetCmbMenuCategory();
        $this->response($data, 200);
    }

    public function getyear_get()
    {
        $back   = date('Y', strtotime('- 5 year'));
        $future = date('Y', strtotime('+ 5 year'));
        $output = array();

        for ($back; $back < $future; $back++) {
            $output[] = array(
                'YEAR_ID'   => $back,
                'YEAR_NAME' => $back,
            );
        }
        $this->response(array('success' => true, 'data' => $output));
    }

    public function all_members_get()
    {
        $members = $this->_model->readMembers($this->get('key'), $this->get('status'), $this->get('start'), $this->get('limit'));
        if ($members) {
            $this->response($members, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any Members!'), 404);
        }

    }

    public function flow_get()
    {
        $query               = $this->get();
        $query['j_username'] = 'swisscontact';
        $query['j_password'] = '12345';
        $vars                = http_build_query($query);
        $ch                  = curl_init();
        $timeout             = 5;
        curl_setopt($ch, CURLOPT_URL, 'http://176.9.63.83:8080/jasperserver/flow.html?' . $vars);
        //var_dump('http://176.9.63.83:8080/jasperserver/flow.html?'.$vars);die;
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        header('Content-type: text/html;charset=UTF-8');
        echo $data;
        die;
    }

    public function flow_post2()
    {

        $post = $this->post();

        var_dump($post);die;
    }

    public function flow_post()
    {
        $uri = 'http://176.9.63.83:8080/jasperserver/flow.html?';
        $get = $_GET;

        $uri = $uri . http_build_query($get);

        $post          = $this->post();
        $fields_string = '';
        foreach ($post as $key => $value) {$fields_string .= $key . '=' . $value . '&';}
        rtrim($fields_string, '&');

        $ch      = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $uri);

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_POST, count($post));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        header('Content-type: text/html;charset=UTF-8');

    }

    public function tiny_get()
    {

        if (!isset($_SESSION['username'])) {
            return $this->response(array('error' => 'Cannot found page'), 404);
        }

        $ch      = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, 'http://176.9.63.83:8080/jasperserver/flow.html?_flowId=searchFlow&j_username=koltivabi&j_password=Bismillah-123');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        echo $data;

        //echo file_get_contents('http://176.9.63.83:8080/jasperserver/flow.html?_flowId=searchFlow&j_username=koltivabi&j_password=Bismillah-123',false);
    }

    /**
     * Ambil data coop yang mau di sync
     * @return json
     * @author <ardiantoro@koltiva.com> Ardi
     */
    public function get_sync_data_get()
    {

        $coopid = getCoopID();
        $data   = $this->_model->getCoopSyncData($coopid);
        if ($data) {
            $this->response($data, 200);
        }

        return $this->response(array(), 200);
    }

    public function combo_propinsi_access_get() {
        $data = $this->_model->getComboProvinceAccess();
        $this->response($data, 200);
    }

    public function combo_district_access_get()
    {
        $data = $this->_model->getComboDistrictAccess((int) $this->get('ProvinceID'));
        $this->response($data, 200);
    }

    public function combo_provinsi_get()
    {

        $prov = $this->get('p');

        $table   = 'ktv_province';
        $display = 'Province';
        $value   = 'ProvinceID';

        $data = $this->_model->getCommonCombo($table, $value, $display);

        $this->_num    = 200;
        $this->_output = array('success' => true, 'data' => $data, 'total' => count($data));
        return $this->response($this->_output, $this->_num);
    }

    public function combo_kabupaten_get()
    {

        $prov = $this->get('p');

        $table   = 'ktv_district';
        $display = 'District';
        $value   = 'DistrictID';

        if (strlen($prov) > 0) {
            $query = 'ProvinceID = ' . $prov;
        }
        $data = $this->_model->getCommonCombo($table, $value, $display, $query);

        $this->_num    = 200;
        $this->_output = array('success' => true, 'data' => $data, 'total' => count($data));
        return $this->response($this->_output, $this->_num);
    }

    public function cmb_partner_common_get()
    {
        $data = $this->_model->getComboPartnerCommon();
        $this->response($data, 200);
    }

    public function cmb_staff_certification_get()
    {
        $data = $this->_model->getComboStaffCertification();
        $this->response($data, 200);
    }

    public function upload_post()
    {
        $headers = apache_request_headers();
        $file    = str_replace('[removed]', '', $this->post('image'));
        $file    = str_replace(' ', '+', $file);
        //var_dump($file);die;
        $files = base64_decode($file);
        $name  = $this->post('name');
        $this->load->helper('file');
        if (!write_file("files/upload/" . $name, $files)) {
            echo 'Unable to write the file';die;
        } else {
            $this->response(array('success' => true));
        }

        //Allowed file type
        $allowed  = array('image/png', 'image/jpeg', 'image/gif', 'image/x-png');
        $max_size = 1024000;

        return $this->response(array('success' => true), 200);
    }

    public function upload_dhis_post(){
        $this->load->helper('file');
        $headers = apache_request_headers();
        $file    = str_replace('[removed]', '', $this->post('image'));
        $file    = str_replace(' ', '+', $file);
        if($file == "") return false;

        $files = base64_decode($file);
        $name  = $this->post('name');

         //-Start-//
        if (!write_file("images/upload/$name", $files)) {
            echo 'Unable to write the file';die;
        }else{
            return $this->response(array('success' => true), 200);
        }
        //-End-//
        /*
        //cek ini upload apa
        $arrFilename = explode("_", $name);
        $FarmerID = $arrFilename[1];

        //get member data
        $this->load->model('grower/mgrower');
        $getData = $this->mgrower->getMemberDataDetailByMemberDisplayID($FarmerID);
        $MemberData = $getData['data'];
        $ProvinceID = $MemberData['ProvinceID'];

        //ambil extensionnya
        $arrTemp = explode(".", $name);
        $extNya = array_values(array_slice($arrTemp, -1))[0];

        switch ($arrFilename[0]) {
            case 'farmer':
                //upload foto petani

                //cek folder propinsi itu sudah ada belum
                if(!file_exists('images/member/'.$ProvinceID)){
                    mkdir('images/member/'.$ProvinceID, 0777, true);
                }
                $nameFileUpload = $ProvinceID."/".$FarmerID.".".$extNya;
                $namaFilePhotoUpdate = $FarmerID.".".$extNya;

                if (!write_file("images/member/" . $nameFileUpload, $files)) {
                    echo 'Unable to write the file';die;
                }else{
                    //update datanya
                    $prosesUpdate = $this->_model->updateFotoFarmer($namaFilePhotoUpdate,$MemberData['MemberID']);
                    return $this->response(array('success' => true), 200);
                }
            break;
            case 'consent':
                //upload foto consent notes

                //cek folder propinsi itu sudah ada belum
                if(!file_exists('images/consent/'.$ProvinceID)){
                    mkdir('images/consent/'.$ProvinceID, 0777, true);
                }
                $nameFileUpload = $ProvinceID."/".$FarmerID.".".$extNya;
                $namaFilePhotoUpdate = $FarmerID.".".$extNya;

                if (!write_file("images/consent/" . $nameFileUpload, $files)) {
                    echo 'Unable to write the file';die;
                }else{
                    //update datanya
                    $prosesUpdate = $this->_model->updateFotoFarmerConsentNote($namaFilePhotoUpdate,$MemberData['MemberID']);
                    return $this->response(array('success' => true), 200);
                }
            break;
            case 'receipt':
                //upload kwitansi survey main buyer
                $eventUID = $arrFilename[2];

                //cek ada UID nya tidak
                $cek = $this->_model->cekSurveyMainBuyerByUID($eventUID);
                if($cek == false){
                    echo 'Main Buyer Survey not found'; die;
                }

                //foto dipisah perdirectory ProvinceID, per MemberDisplayID, cek apakah folder tempat nyimpan foto sudah ada
                if(!file_exists('images/main_buyer_last_receipt/'.$ProvinceID)){
                    mkdir('images/main_buyer_last_receipt/'.$ProvinceID, 0777, true);
                }
                if(!file_exists('images/main_buyer_last_receipt/'.$ProvinceID.'/'.$MemberData['MemberDisplayID'])){
                    mkdir('images/main_buyer_last_receipt/'.$ProvinceID.'/'.$MemberData['MemberDisplayID'], 0777, true);
                }
                $namaFilePhotoUpdate = date('YmdHis').".".$extNya;
                $nameFileUpload = $ProvinceID."/".$MemberData['MemberDisplayID']."/".$namaFilePhotoUpdate;

                if (!write_file("images/main_buyer_last_receipt/" . $nameFileUpload, $files)) {
                    echo 'Unable to write the file';die;
                }else{
                    //update datanya
                    $prosesUpdate = $this->_model->updateFotoReceiptMainBuyer($namaFilePhotoUpdate,$eventUID);
                    return $this->response(array('success' => true), 200);
                }
            break;
            case 'garden':
                //upload foto visitasi garden
                $eventUID = $arrFilename[2];

                //cek ada UID nya tidak
                $cek = $this->_model->cekSurveyGardenByUID($eventUID);
                if($cek == false){
                    echo 'Garden Survey not found'; die;
                }

                //foto dipisah perdirectory ProvinceID, per MemberDisplayID, cek apakah folder tempat nyimpan foto sudah ada
                if(!file_exists('images/plot_visit/'.$ProvinceID)){
                    mkdir('images/plot_visit/'.$ProvinceID, 0777, true);
                }
                if(!file_exists('images/plot_visit/'.$ProvinceID.'/'.$MemberData['MemberDisplayID'])){
                    mkdir('images/plot_visit/'.$ProvinceID.'/'.$MemberData['MemberDisplayID'], 0777, true);
                }
                $namaFilePhotoUpdate = date('YmdHis').".".$extNya;
                $nameFileUpload = $ProvinceID."/".$MemberData['MemberDisplayID']."/".$namaFilePhotoUpdate;

                if (!write_file("images/plot_visit/" . $nameFileUpload, $files)) {
                    echo 'Unable to write the file';die;
                }else{
                    //update datanya
                    $prosesUpdate = $this->_model->updateFotoGardenVisit($namaFilePhotoUpdate,$eventUID);
                    return $this->response(array('success' => true), 200);
                }
            break;
        }
        */
    }

    public function proses_dhis_post(){
        $this->load->model('tools/msyn');
        $this->msyn->updateImages_From_pullMiddlewareData();
        exit;
    }

    public function dummy_get()
    {

        $this->load->helper('directory');

        $map    = directory_map('files/upload/');
        $output = '<ul>';
        foreach ($map as $keys => $value) {
            $output .= '<li><a href="' . base_url('/files/upload/' . $value) . '">' . $value . '</a></li>';
        }
        $output .= '</ul>';

        echo $output;die;

    }

    /**
     * Kebutuhan Master data untuk Mobile Coop
     * @author ardiantoro@koltiva.com
     */

    public function mastervillage_get()
    {

        $fg = $this->_model->getMasterVillage();
        if ($fg) {
            $this->response($fg, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any village!'), 404);
        }
    }

    public function mastersubdistrict_get()
    {

        $fg = $this->_model->getMasterSubDistrict();
        if ($fg) {
            $this->response($fg, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any sub district!'), 404);
        }
    }

    public function revoke_post()
    {
        $this->load->model('staff/mstaffuser');
        $this->load->model('staff/mstaffuser_cognito');
        $uid = pack("H*", $this->post('uid'));
        $username = $uid;
        $pwd = $this->post('passwd');

        //check password ke cognito
        $cekOldPass = $this->mstaffuser_cognito->checkPassword($username,$pwd);
        if($cekOldPass == false) {
            $this->response(array('msg' => 'Failed to revoke, please re-login!'), 400);
        } else {
            //set session login lagi
            $dataUser = $this->mstaffuser->GetDataUserAccByUsername($username);
            $this->mstaffuser->setUserSessionLogin($dataUser['UserId'],false,'Cognito');
            $this->response(array('msg' => 'Revoke success'), 200);
        }
    }

    public function combo_month_option_get(){
        $formattedMonthArray = array(
            "01" => "January", "02" => "February", "03" => "March", "04" => "April",
            "05" => "May", "06" => "June", "07" => "July", "08" => "August",
            "09" => "September", "10" => "October", "11" => "November", "12" => "December",
        );
        $arrReturn = array();
        $i = 0;
        foreach ($formattedMonthArray as $month => $monthtext) {
            $arrReturn[$i]['id'] = strval($month);
            $arrReturn[$i]['label'] = lang($monthtext);
            $i++;
        }

        $this->response($arrReturn, 200);
    }

    public function combo_year_option_get(){
        $yearRange = (int) $this->get('yearRange');
        $yearOrder = $this->get('yearOrder');
        if($yearRange == 0) $yearRange = 50;
        if($yearOrder == ""){
            $yearNow = date('Y');
            $arrReturn = array();
            $incre = 0;
    
            for ($i=$yearNow; $i >= ($yearNow-$yearRange); $i--) {
                $arrReturn[$incre]['id'] = strval($i);
                $arrReturn[$incre]['label'] = strval($i);
                $incre++;
            }
        }else{
            $yearNow = date('Y')-4;
            $arrReturn = array();
            $incre = 0;
    
            for ($i=($yearNow+$yearRange); $i >= $yearNow; $i--) {
                $arrReturn[$incre]['id'] = strval($i);
                $arrReturn[$incre]['label'] = strval($i);
                $incre++;
            }
        }

        $this->response($arrReturn, 200);
    }

    public function combo_partner_get(){
        $data = $this->_model->getComboPartner();
        $this->response($data, 200);
    }

    public function combo_province_get(){
        $data = $this->_model->getComboProvince();
        $this->response($data, 200);
    }

    public function combo_district_get(){
        $ProvinceID = (int) $this->get('ProvinceID');
        $data = $this->_model->getComboDistrict($ProvinceID);
        $this->response($data, 200);
    }

    public function cmb_farmer_group_get(){
        $DistrictID = (int) $this->get('DistrictID');
        $ProvinceID = (int) $this->get('ProvinceID');
        $data = $this->_model->getComboFarmerGroup($DistrictID,$ProvinceID);
        $this->response($data, 200);
    }

    public function cmb_farmer_group_member_get(){
        $FarmerGroupID = (int) $this->get('FarmerGroupID');
        $data = $this->_model->getComboFarmerGroupMember($FarmerGroupID);
        $this->response($data, 200);
    }

    public function cmb_cooperatives_member_get(){
        $CoopID = (int) $this->get('CoopID');
        $data = $this->_model->getComboCooperativesMember($CoopID);
        $this->response($data, 200);
    }

    public function combo_wage_currency_get(){
        $data = $this->_model->GetComboWageCurr();
        $this->response($data, 200);
    }
	
	public function combo_village_get(){
        $SubDistrictID = (int) $this->get('SubDistrictID');
        $loadAll = $this->get('loadAll');
        $data = $this->_model->getComboVillage($SubDistrictID,$loadAll);
        $this->response($data, 200);
    }
	
	public function combo_buyingunit_get(){
        $PartnerID = (int) $this->get('PartnerID'); 
        $data = $this->_model->getComboBuyingUnit($PartnerID);
        $this->response($data, 200);
    }
	
	public function combo_subdistrict_get(){
        $DistrictID = (int) $this->get('DistrictID');
        $data = $this->_model->getComboSubDistrict($DistrictID);
        $this->response($data, 200);
    }

    public function cmb_sme_dealer_get(){
        $ProvinceID = (int) $this->get('ProvinceID');
        $data = $this->_model->getComboSMEDealer($ProvinceID);
        $this->response($data, 200);
    }

    public function cmb_holder_get(){
        $CertProgID = (int) $this->get('CertProgID');
        $data = $this->_model->getComboHolder($CertProgID);
        $this->response($data, 200);
    }

    public function cmb_certPrograms_get(){
        $data = $this->_model->getCombocertPrograms();
        $this->response($data, 200);
    }

    public function combo_imsevent_get(){
        $CertHolderID = (int) $this->get('CertHolderID');
        $data = $this->_model->getComboImsEvent($CertHolderID);
        $this->response($data, 200);
    }

    public function combo_farmer_type_get(){
        $IMSID = (int) $this->get('IMSID');
        $data = $this->_model->getCombo_farmer_type($IMSID);
        $this->response($data, 200);
    }

    public function cmb_eventtrainingtype_get(){
        $data = $this->_model->geteventtrainingtype();
        $this->response($data, 200);
    }

    public function cmbBatchGeneral_get(){
        $data = $this->_model->cmbBatchGeneral();
        $this->response($data, 200);
    }

    public function cmb_staff_get(){
        $data = $this->_model->cmbStaffGeneral();
        $this->response($data, 200);
    }

    public function cmb_farmer_group_by_district_get(){
        $data = $this->_model->getComboFarmerGroupByDistrict((int) $this->get('DistrictID'));
        $this->response($data, 200);
    }

    public function combo_filter_country_get() {
        $data = $this->_model->GetComboFilterCountry();
        $this->response($data, 200);
    }

    public function combo_filter_province_get() {
        $CountryID = $this->get('CountryID');
        $data = $this->_model->GetComboFilterProvince($CountryID);
        $this->response($data, 200);
    }

    public function combo_filter_district_get(){
        $ProvinceID = (int) $this->get('ProvinceID');
        $data = $this->_model->getComboFilterDistrict($ProvinceID);
        $this->response($data, 200);
    }

    public function cmb_partner_get() {
        $PartnerID = empty($this->get('PartnerID')) ? NULL : (int) $this->get('PartnerID');

        $data = $this->_model->GetCmbPartner($PartnerID);
        $this->response($data, 200);
    }
}