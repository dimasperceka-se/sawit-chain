<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mobile_farmer_apps_new extends REST_Controller {

    public $_output = array('success' => false, 'error' => 'Data is not valid'); //response data

    public function __construct() {
        parent::__construct();
        $this->load->model('mfarmer_apps_new', '_model');
        $this->load->library('verifikasitoken');
    }

    function login_post() {
        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];
        if ($data) {
            $process = $this->_model->doLogin($data[0]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $post,
                'data' => $data
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    private function verif_token($data){
        $header = $this->input->request_headers();
        /* ini sementara waktu aja selama yang lama masih berjalan */
        $basic = explode(' ', $header['Authorization']);
        if(strtolower(@$basic[0]) == 'basic'){
            return $data;
        }
        /* ------------------------------------------------------- */
        if(@$header['Authorization']){
            // cek validasi token
            $cekValisasiToken = $this->verifikasitoken->cekValisasiToken(@$header['Authorization'], false);
            if($cekValisasiToken['success']){
                if ($cekValisasiToken['data']['custom:partnerid'] == NULL) {
                    $cekValisasiToken['data']['custom:partnerid'] = "";
                }
                $data[0]['attributes']['farmerid'] = $cekValisasiToken['data']['custom:objectid'];
                $data[0]['attributes']['partnerid'] = $cekValisasiToken['data']['custom:partnerid'];
            }else{
                echo json_encode(array('success' => false, 'message' => $cekValisasiToken['message']));
                exit;
            }
        }

        return $data;
    }

    // untuk cognito
    // login cognito
    public function login_aws_post(){
        $username = 'hardie.hasri';
        $password = 'Password1234!';
        $data = loginCognito($username, $password);
    }

    public function Kiosk_post(){
        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];
        // ini d eksekusi jika ada header token
        $data = $this->verif_token($data);

        if ($data) {
            $process = $this->_model->getKiosk($data[0]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }
        echo json_encode($process);
        exit;
    }

    // cek farmer 
    public function cekFarmer_get(){

        ini_set('display_errors',true);
        error_reporting(E_ALL);

        // validasi data
        $phone = $this->input->get('phone',true);

        if ($phone) {
            $cekFarmer = $this->_model->checkFarmer($phone);
            return $this->response($cekFarmer, 200);
        }

        $error = "";
        if(!$phone){
            $error .= "Phone ";
        }

        $return = array('success'=>false, 'message' => $error.' is required !');
        return $this->response($return,401);
    }

    // insert registrasi 
    public function regUserCognito_post(){
        $farmerid = $this->post('farmerid');
        $email = $this->post('email');
        $username = $this->post('username');
        $fcmid = $this->post('fcmid');
        $phone = (null !== $this->post('secondary_phone')) ? $this->post('secondary_phone') : NULL;

        if ($farmerid && $email && $username) {
            $registrasi = $this->_model->regUserCognito($farmerid, $email, $username, $fcmid, $phone);
            return $this->response($registrasi, 200);
        }
        $error = "";
        if(!$farmerid){
            $error .= "FarmerID, ";
        }
        if(!$email){
            $error .= "Email, ";
        }
        if(!$username){
            $error .= "Username, ";
        }
        $return = array('success'=>false, 'message' => $error.' is required !');
        return $this->response($return, 201);
        exit;
    }

    // update FCM login
    public function updateFCM_post(){
        $farmerid = $this->post('farmerid');
        $fcmid = $this->post('fcmid');

        $data = $this->verif_token(array());

        if ($fcmid && $farmerid){
            $updateFCM = $this->_model->updateFCM($farmerid, $fcmid);
            return $this->response($updateFCM, 200);
        }

        $error = "";
        if(!$farmerid){
            $error .= "FarmerID, ";
        }

        if(!$fcmid){
            $error .= "FCM ID ";
        }

        $return = array('success'=>false, 'message' => $error.' is required !');
        return $this->response($return,401);
        exit;
    }



    function farmer_profile_post() {
        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $header = $this->input->request_headers();
        $basic = explode(' ', $header['Authorization']);
        if(strtolower(@$basic[0]) != 'basic'){
            $data = $this->verif_token($data);
        }

        if ($data) {
            $process = $this->_model->getFarmerProfile($data[0]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    function farmer_garden_post() {
        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $header = $this->input->request_headers();
        $basic = explode(' ', $header['Authorization']);
        if(strtolower(@$basic[0]) != 'basic'){
            $data = $this->verif_token($data);
        }

        if ($data) {
            $process = $this->_model->getFarmerGarden($data[0]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    function farmer_transaction_post() {
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $header = $this->input->request_headers();
        $basic = explode(' ', $header['Authorization']);
        if(strtolower(@$basic[0]) != 'basic'){
            $data = $this->verif_token($data);
        }



        //echo '<pre>'.print_r($data, 1);die;
        if ($data) {
            $process = $this->_model->getFarmerTransaction($data[0]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    function farmer_transaction_detail_summary_post() {
        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $data = $this->verif_token($data);

        if ($data) {
            $process = $this->_model->getFarmerTransactionDetailSummary($post, $data[0]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    function farmer_premium_post() {
        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $header = $this->input->request_headers();
        $basic = explode(' ', $header['Authorization']);
        if(strtolower(@$basic[0]) != 'basic'){
            $data = $this->verif_token($data);
        }

        if ($data) {
            $process = $this->_model->getFarmerPremium($data[0]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    function trader_post() {
        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $header = $this->input->request_headers();
        $basic = explode(' ', $header['Authorization']);
        if(strtolower(@$basic[0]) != 'basic'){
            $data = $this->verif_token($data);
        }

        if ($data) {
            $process = $this->_model->getTrader($data[0]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    function FarmerIncentive_post() {
        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $header = $this->input->request_headers();
        $basic = explode(' ', $header['Authorization']);
        if(strtolower(@$basic[0]) != 'basic'){
            $data = $this->verif_token($data);
        }

        if ($data) {
            $process = $this->_model->getFarmerIncentive($data[0]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    function DailyPrice_post() {
        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $header = $this->input->request_headers();
        $basic = explode(' ', $header['Authorization']);
        if(strtolower(@$basic[0]) != 'basic'){
            $data = $this->verif_token($data);
        }

        if ($data) {
            $process = $this->_model->getDailyPrice($data[0]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    function collector_quota_post() {
        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $header = $this->input->request_headers();
        $basic = explode(' ', $header['Authorization']);
        if(strtolower(@$basic[0]) != 'basic'){
            $data = $this->verif_token($data);
        }

        //if ($data) {
        $process = $this->_model->getCollectorQuota($data[0]);
        /*} else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }*/
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    function training_post() {
        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $header = $this->input->request_headers();
        $basic = explode(' ', $header['Authorization']);
        if(strtolower(@$basic[0]) != 'basic'){
            $data = $this->verif_token($data);
        }

        //if ($data) {
        $process = $this->_model->getTraining($data[0]);
        /*} else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }*/
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    function certification_post() {
        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $header = $this->input->request_headers();
        $basic = explode(' ', $header['Authorization']);
        if(strtolower(@$basic[0]) != 'basic'){
            $data = $this->verif_token($data);
        }

        if ($data) {
            $process = $this->_model->getCertification($data[0]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    function certification_ics_history_post() {
        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $header = $this->input->request_headers();
        $basic = explode(' ', $header['Authorization']);
        if(strtolower(@$basic[0]) != 'basic'){
            $data = $this->verif_token($data);
        }

        if ($data) {
            $process = $this->_model->getCertification_ics_history($data[0]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    function read_notification_post() {

        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $data = $this->verif_token($data);

        if ($data) {
            $process = $this->_model->updateFarmerNotification($data[0]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    function farmer_notification_post() {
        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $header = $this->input->request_headers();
        $basic = explode(' ', $header['Authorization']);
        if(strtolower(@$basic[0]) != 'basic'){
            $data = $this->verif_token($data);
        }

        if ($data) {
            $process = $this->_model->getFarmerNotification($data[0]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    function farmer_agent_post() {
        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $header = $this->input->request_headers();
        $basic = explode(' ', $header['Authorization']);
        if(strtolower(@$basic[0]) != 'basic'){
            $data = $this->verif_token($data);
        }

        if ($data) {
            $process = $this->_model->getFarmerAgent($data[0]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    function field_agent_post() {
        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $header = $this->input->request_headers();
        $basic = explode(' ', $header['Authorization']);
        if(strtolower(@$basic[0]) != 'basic'){
            $data = $this->verif_token($data);
        }

        if ($data) {
            $process = $this->_model->getFieldAgent($data[0]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    function news_post_old() {
        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $header = $this->input->request_headers();
        $basic = explode(' ', $header['Authorization']);
        if(strtolower(@$basic[0]) != 'basic'){
            $data = $this->verif_token($data);
        }

        if ($data) {
            $process = $this->_model->getNews($data[0]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    function seedlings_post() {
        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $header = $this->input->request_headers();
        $basic = explode(' ', $header['Authorization']);
        if(strtolower(@$basic[0]) != 'basic'){
            $data = $this->verif_token($data);
        }

        if ($data) {
            $process = $this->_model->getSeedlings($data[0]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No seedlings data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    function registration_post() {
        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $header = $this->input->request_headers();
        $basic = explode(' ', $header['Authorization']);
        if(strtolower(@$basic[0]) != 'basic'){
            $data = $this->verif_token($data);
        }

        if ($data) {
            $process = $this->_model->postRegistration($data[0]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No registration data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    function confirm_transaction_post() {
        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $header = $this->input->request_headers();
        $basic = explode(' ', $header['Authorization']);
        if(strtolower(@$basic[0]) != 'basic'){
            $data = $this->verif_token($data);
        }

        if ($data) {
            $process = $this->_model->confirmTransaction($data[0]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No seedlings data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    function video_post_old() {
        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $header = $this->input->request_headers();
        $basic = explode(' ', $header['Authorization']);
        if(strtolower(@$basic[0]) != 'basic'){
            $data = $this->verif_token($data);
        }

        if ($data) {
            $process = $this->_model->getVideo($data[0]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    function farmer_training_post() {
        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $header = $this->input->request_headers();
        $basic = explode(' ', $header['Authorization']);
        if(strtolower(@$basic[0]) != 'basic'){
            $data = $this->verif_token($data);
        }

        if ($data) {
            $process = $this->_model->getFarmerTraining($data[0]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    function farmer_trader_post() {
        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $header = $this->input->request_headers();
        $basic = explode(' ', $header['Authorization']);
        if(strtolower(@$basic[0]) != 'basic'){
            $data = $this->verif_token($data);
        }

        if ($data) {
            $process = $this->_model->getFarmerTrader($data[0]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    function farmer_manual_post() {

        ini_set('display_errors',true);
        error_reporting(E_ALL);

        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $header = $this->input->request_headers();
        $basic = explode(' ', $header['Authorization']);
        if(strtolower(@$basic[0]) != 'basic'){
            $data = $this->verif_token($data);
        }

        if ($data) {
            $process = $this->_model->getFarmerManual($data[0]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    function farmer_manual_new_post() {
        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $data = $this->verif_token($data);

        if ($data) {
            $process = $this->_model->getFarmerManualNew($data[0]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    //edited: ardiantoro@koltiva.com
    function farmer_check_by_phone_post(){
        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $header = $this->input->request_headers();
        $basic = explode(' ', $header['Authorization']);
        if(strtolower(@$basic[0]) != 'basic'){
            $data = $this->verif_token($data);
        }

        if ($data) {
            $process = $this->_model->getFarmerProfile($data[0]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    function news_post() {

        ini_set('display_errors',true);
        error_reporting(E_ALL);

        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $data = $this->verif_token($data);

        if ($data) {
            $process = $this->_model->getNews($data[0]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    function news_new_post() {
        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $data = $this->verif_token($data);

        if ($data) {
            $process = $this->_model->getNewsNew($data[0]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    function news_detail_post() {
        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];
        // ini d eksekusi jika ada header token
        $data = $this->verif_token($data);

        if ($data) {
            $process = $this->_model->getNewsDetail($data[0]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    function video_post() {

        ini_set('display_errors',true);
        error_reporting(E_ALL);

        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $data = $this->verif_token($data);

        if ($data) {
            $process = $this->_model->getVideo($data[0]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    function video_new_post() {
        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $data = $this->verif_token($data);

        if ($data) {
            $process = $this->_model->getVideoNew($data[0]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    function farmer_check_by_farmer_id_post(){
        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $header = $this->input->request_headers();
        if(array_key_exists('Authorization',$header)) {
            $basic = explode(' ', $header['Authorization']);
            if(strtolower(@$basic[0]) != 'basic'){
                $data = $this->verif_token($data);
            }
        }

        if ($data) {
            $process = $this->_model->getFarmerProfilebyFarmerId($data[0]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }
    //eoe--

    function check_registered_phone_post() {

        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $header = $this->input->request_headers();
        $basic = explode(' ', $header['Authorization']);
        if(strtolower(@$basic[0]) != 'basic'){
            $data = $this->verif_token($data);
        }

        if ($data) {
            $process = $this->_model->cekForPhoneRegistered($data[0]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    function analytics_post() {

        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $data = $this->verif_token($data);

        // Get data authentication
        $header = $this->input->request_headers();
        if(@$header['Authorization']){
            // cek validasi token
            $cekValisasiToken = $this->verifikasitoken->cekValisasiToken(@$header['Authorization'], false);
            if($cekValisasiToken['success']){
                $data[0]['attributes']['UserName'] = $cekValisasiToken['data']['cognito:username'];
                $data[0]['attributes']['Email'] = $cekValisasiToken['data']['email'];
            }else{
                echo json_encode(array('success' => false, 'message' => $cekValisasiToken['message']));
                exit;
            }
        }

        $data[0]['attributes']['ActivityDate'] = date('Y-m-d H:i:s', strtotime($data[0]['attributes']['ActivityDate']));
        var_dump($data);
        die();
        if ($data) {
            $process = $this->_model->createAnalyticsData($data[0]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    function send_otp_post() {
        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $header = $this->input->request_headers();
        $basic = explode(' ', $header['Authorization']);
        if(strtolower(@$basic[0]) != 'basic'){
            $data = $this->verif_token($data);
        }

        @$handphone = $data[0]['attributes']['handphone'];
        @$data[0]['opt'] = random_int(100000, 999999);

        if ($handphone != "" && $data[0]['opt'] != "") {
            $process = $this->_model->saveLogOTP($data[0]);
            $handphone = str_replace("+", "", $handphone);
            $prefix_no = substr($handphone, 0, 2);
            if (isset($process["errors"])) {
                $process['errors'] = array(
                    'status' => "001",
                    'title' => "Farmer Not Found",
                    'detail' => "No post data",
                    'post' => $this->post()
                );
                echo json_encode($process);
                die;
            }
            // if($prefix_no === "62"){
            $send_otp = $this->send_otp_indo($handphone, $data[0]['opt']);
            $return_msg = json_decode($send_otp);

            $process['data'] = array(
                'type' => "send otp",
                'id' => 1,
                'attributes' => array(
                    'handphone' => $handphone,
                    'otpid' => "",
                    'message' => ""
                )
            );
            // }
            // else{
            //     $send_otp = $this->send_otp_global($handphone, $data[0]['opt']); 
            //     $return = json_decode($send_otp);

            //     $process['data'] = array(
            //         'type' => "send otp",
            //         'id' => 1,
            //         'attributes' => array(
            //             'handphone' => $handphone,
            //             'otpid' => $data[0]['opt'],
            //             'message' => $return->recipients->items[0]->status
            //         )
            //     );  
            // }

        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }
        echo json_encode($process);
        exit;
    }

    function validation_otp_post() {

        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $header = $this->input->request_headers();
        $basic = explode(' ', $header['Authorization']);
        if(strtolower(@$basic[0]) != 'basic'){
            $data = $this->verif_token($data);
        }

        if ($data) {
            $process = $this->_model->validateOtpByFarmerPhone($data[0]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    function check_phone_num_registration_post()
    {
        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $header = $this->input->request_headers();
        $basic = explode(' ', $header['Authorization']);
        if(strtolower(@$basic[0]) != 'basic'){
            $data = $this->verif_token($data);
        }
        if ($data) {
            $process = $this->_model->checkPhoneNumberRegistration($data[0]["attributes"]["phonenumber"], $data[0]["attributes"]["farmerid"]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    private function send_otp_indo($handphone, $otp){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://128.199.187.186:8682/otp_cognito_handler',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{"handphone_no":"'.$handphone.'","otp":"'.$otp.'" }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    private function send_otp_global($handphone, $otp){
        $MessageBird = new \MessageBird\Client('pmxPKwTzjiEoLYwBhqurQ6cjK');
        $Message = new \MessageBird\Objects\Message();
        $Message->originator = 'KOLTIVA';
        $Message->recipients = array($handphone);
        $Message->body = 'Your OTP is '.$otp.'. Never share OTP with anyone.';

        $otp = $MessageBird->messages->create($Message);

        return json_encode($otp);
    }

    public function send_reset_password_post()
    {
        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $header = $this->input->request_headers();
        $basic = explode(' ', $header['Authorization']);
        if(strtolower(@$basic[0]) != 'basic'){
            $data = $this->verif_token($data);
        }
        if ($data) {
            $process = $this->_model->cognitoAdminSetUserPassword($data[0]["attributes"]["handphone"], $data[0]["attributes"]["new_password"]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    public function confirm_reset_password_post()
    {
        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $header = $this->input->request_headers();
        $basic = explode(' ', $header['Authorization']);
        if(strtolower(@$basic[0]) != 'basic'){
            $data = $this->verif_token($data);
        }
        if ($data) {
            $process = $this->_model->cognitoConfirmResetPassword($data[0]["attributes"]["farmerid"], $data[0]["attributes"]["new_password"], $data[0]["attributes"]["verification_code"]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    public function forgot_username_post()
    {
        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $header = $this->input->request_headers();
        $basic = explode(' ', $header['Authorization']);
        if(strtolower(@$basic[0]) != 'basic'){
            $data = $this->verif_token($data);
        }
        if ($data) {
            $process = $this->_model->forgotUsername($data[0]["attributes"]["handphone"]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    public function change_phone_cognito_post()
    {
        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $header = $this->input->request_headers();
        $basic = explode(' ', $header['Authorization']);
        if(strtolower(@$basic[0]) != 'basic'){
            $data = $this->verif_token($data);
        }
        if ($data) {
            $process = $this->_model->changePhoneCognito($data[0]["attributes"]["handphone"], $data[0]["attributes"]["new_handphone"], $data[0]["attributes"]["farmerId"], $data[0]["attributes"]["country_code"]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }

    public function change_phone_fromcentral_post()
    {

        $post = $this->post();

        $phonenumber = $post["phonenumber"];
        $farmerid = $post["farmerid"];

        // ini d eksekusi jika ada header token
        $header = $this->input->request_headers();
        $basic = explode(' ', $header['Authorization']);

        if ($phonenumber != '') {
            $process = $this->_model->changePhonefromcentral($phonenumber,$farmerid);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }

        echo json_encode($process);
        exit;
    }

    function check_staffid_post() {
        $post = $this->post();
        $post_string = json_encode($post);
        $rep = str_replace("\\", '', $post_string);
        $rep = str_replace('"[', '[', $rep);
        $rep = str_replace(']"', ']', $rep);
        $post_fix = json_decode($rep, TRUE);
        $data = $post_fix['data'];

        // ini d eksekusi jika ada header token
        $data = $this->verif_token($data);

        if ($data) {
            $process = $this->_model->getStaffID($data[0]);
        } else {
            $process['errors'] = array(
                'status' => "001",
                'title' => "request failed",
                'detail' => "No post data",
                'post' => $this->post()
            );
        }
        //echo "<pre>".print_r($process,1);
        echo json_encode($process);
        exit;
    }
}