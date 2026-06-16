<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Message extends REST_Controller {

    public function __construct() 
    {
        parent::__construct();
        $this->load->model('message/mmessage');
    }

    public function inbounds_get()
    {
    	$params = array(
	        'limit'      => $this->get('limit'),
	        'offset'     => $this->get('start'),
	        'key'    	 => $this->get('key'),
        );

        $inbounds = $this->mmessage->readInbounds($params);
        if($inbounds) {
        	$this->response($inbounds, 200);
        } else {
        	$this->response(array('error' => 'Couldn\'t find any inbounds!'), 404);
        }
    }

    public function outbounds_get()
    {
    	$params = array(
	        'limit'      => $this->get('limit'),
	        'offset'     => $this->get('start'),
	        'key'    	 => $this->get('key'),
        );

        $outbounds = $this->mmessage->readOutbounds($params);
        if($outbounds) {
        	$this->response($outbounds, 200);
        } else {
        	$this->response(array('error' => 'Couldn\'t find any outbounds!'), 404);
        }
    }

    public function outbound_post()
    {
        $result     = false;
        $number     = $this->get_number($this->post('number'));
        $msg        = $this->post('text');

        if ($number && $msg) {        
	    	$result = $this->send_message($number, $msg);
        }

        if ($result) {
        	$this->response(true, 200);
        } else {
        	$this->response(false, 404);
        }
    }

    private function get_number($number)
    {
    	$number = trim($number);
        $number = str_replace(" ", "", $number);
        $number = str_replace("-", "", $number);
    	$first = substr($number, 0, 1);
    	switch ($first) {
    		case '0':
    			$number = preg_replace('/0/', '62', $number, 1);
    			break;
    		case '+':
    			$number = preg_replace('/\+/', '', $number, 1);
    			break;
    		
    		default:
    			// do nothing
    			break;
    	}
    	return $number;
    }

    private function send_message($to, $msg)
    {
        $this->load->model('sms/msms');
        // load library
        $this->load->library('nexmo');
        // set response format: xml or json, default json
        $this->nexmo->set_format('json');
        
        // **********************************Text Message*************************************
        $from           = $this->config->item('from');
        $to             = $to;
        $message        = array(
            'text'      => $msg,
        );
        $response = $this->nexmo->send_message($from, $to, $message);
        // save to db
        $params = array('to' => $to, 'message' => $msg);
        $result = true;
        foreach ($response['messages'] as $key => $value) {
        	$params = array_merge($params, $value);
        	if ($result) {
        		$result = $this->msms->insertOutbound($params);
        	}
        	// echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        }
        // echo '<pre>'; print_r($response); echo '</pre>';
        // echo "<h1>Text Message</h1>";
        // $this->nexmo->d_print($response);
        // echo "<h3>Response Code: ".$this->nexmo->get_http_status()."</h3>";
        
        return $result;
    }
    
    public function broadcasts_get()
    {
    	$data= $this->mmessage->readBroadcasts($this->get('key'), $this->get('start'));
        if($data) {
        	$this->response($data, 200);
        } else {
        	$this->response(array(), 200);
        }
    }
    
    public function broadcast_to_get()
    {
    	$data= $this->mmessage->readBroadcastTo($this->get('BroadcastID'), $this->get('key'), $this->get('start'));
        if($data) {
        	$this->response($data, 200);
        } else {
        	$this->response(array(), 200);
        }
    }
    
    public function broadcast_to_add_list_get()
    {
    	$data= $this->mmessage->readFarmerList($this->get('BroadcastID'), $this->get('key'), $this->get('Province'), $this->get('District'), $this->get('SubDistrict'), $this->get('Village'), $this->get('GroupID'), $this->get('start'));
        $this->response($data, 200);
        
    }
    
    public function broadcast_data_post() {
        $data = $this->mmessage->createBroadcast($this->post('Message'), $_SESSION['userid']);
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Could not connect to the database. Retry later'), 404);
    }
    
    public function broadcast_data_put() {
        $data = $this->mmessage->updateBroadcast($this->put('BroadcastID'), $this->put('Message'), $_SESSION['userid']);
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Could not connect to the database. Retry later'), 404);
    }
    
    public function broadcast_province_get(){
        $data = $this->mmessage->listProvinces();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any roles!'), 404);
        }
    }
    
     public function broadcast_district_get(){
        $data = $this->mmessage->listDistricts($this->get('ProvinceID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any roles!'), 404);
        }
    }
    
    public function broadcast_subdistrict_get(){
        $data = $this->mmessage->listSubDistricts($this->get('ProvinceID'), $this->get('DistrictID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any roles!'), 404);
        }
    }
    
    public function broadcast_village_get(){
        $data = $this->mmessage->listVillages($this->get('SubDistrictID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any roles!'), 404);
        }
    }
    
    public function broadcast_sms_group_get(){
        $data = $this->mmessage->listSmsGroups();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any roles!'), 404);
        }
    }

    public function broadcast_farmer_add_post() {
        $data = $this->mmessage->addFarmers($this->post('BroadcastID'), $this->post('Message'), $this->post('farmers'), $_SESSION['userid']);
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Data could not be found'), 404);
    }

    public function broadcast_number_add_post() {
        $data = $this->mmessage->addNumber($this->post('BroadcastID'), $this->post('Message'), $this->post('Name'), $this->get_number($this->post('PhoneNumber')), $_SESSION['userid']);
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Data could not be found'), 404);
    }
    
    public function broadcast_detail_get(){
        $data = $this->mmessage->readBroadcastDetail($this->get('BroadcastID'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array(), 200);
    }
    
    function broadcast_delete() {
        if (!$this->delete('BroadcastID'))
            $this->response(NULL, 400);
        $data = $this->mmessage->deleteBroadcast($_SESSION['userid'], $this->delete('BroadcastID'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Data could not be found'), 404);
    }
    
    public function broadcast_sent_detail_get(){
        $data = $this->mmessage->readSentDetail($this->get('BroadcastDetailID'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array(), 200);
    }
    
    public function broadcast_number_add_put() {
        $data = $this->mmessage->updateNumber($this->put('BroadcastDetailID'), $this->put('Name'), $this->get_number($this->put('PhoneNumber')), $_SESSION['userid']);
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Data could not be found'), 404);
    }
    
    public function broadcast_sent_detail_delete() {
        $data = $this->mmessage->deleteNumber($this->delete('BroadcastDetailID'), $_SESSION['userid']);
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Data could not be found'), 404);
    }
    
    public function broadcast_sent_message_post(){
        $data = $this->mmessage->readSentBroadcast($this->post('BroadcastID'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array(), 200);
    }
    
    public function broadcast_sent_message_proccess_post(){
        $broadcast = $this->mmessage->readSentDetail($this->post('BroadcastDetailID'));
        //echo "<pre>".print_r($broadcast,1);exit;
        $this->load->model('sms/msms');
        $this->load->library('nexmo');
        $this->nexmo->set_format('json');
        
        $from           = $this->config->item('from');
        $to             = $broadcast['to'];
        $message        = array(
            'text'      => $this->post('Message'),
        );
        $response = $this->nexmo->send_message($from, $to, $message);
        /*$response['message-count'] = 1;
        $response['message'][0]['to'] = '6281343346665';
        $response['message'][0]['message-id'] = '0E000000591935E5';
        $response['message'][0]['status'] = 0;
        $response['message'][0]['remaining-balance'] = 6.70160000;
        $response['message'][0]['message-price'] = 0.02600000;
        $response['message'][0]['network'] = '51010';*/
        //echo "<pre>".print_r($response,1);exit;
        $update = $this->mmessage->updateBroadcastDetail($this->post('BroadcastDetailID'), $this->post('Message'), $response['messages'][0], $_SESSION['userid']);
        $this->response($update, 200);
    }
}
