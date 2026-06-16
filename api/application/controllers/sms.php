<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sms extends REST_Controller {

	private $message = '';

	public function __construct()
	{
		parent::__construct();
        $this->load->model('sms/msms');
	}

	public function inbound_get()
	{		
            $request = $this->get();
            // check that request is inbound message
            if(!isset($request['to']) OR !isset($request['msisdn']) OR !isset($request['text'])){
                // error_log('not inbound message');
                // return;
                $this->response('not inbound message', 200);
            }

            $this->msms->handleInbound($request);

            $this->process_message($request);

            // always return response 200 OK
            $this->response(TRUE, 200);
	}

    private function process_message($data)
    {
    	// echo '<pre>'; print_r($data); echo '</pre>';
        $phone      = $data['msisdn'];
    	switch ($data['keyword']) {
            case 'REG':
            $farmer_id  = trim(str_replace($data['keyword'], '', strtoupper($data['text'])));
                $this->register($farmer_id, $phone);
                break;
            case 'UNREG':
            $farmer_id  = trim(str_replace($data['keyword'], '', strtoupper($data['text'])));
                $this->unregister($farmer_id, $phone);
                break;
            case 'HARGA':
                $key  = trim(str_replace($data['keyword'], '', strtoupper($data['text'])));
                $this->check_price($phone, $key);
                break;
            case 'SET':
                $text   = trim(str_replace($data['keyword'], '', strtoupper($data['text'])));
                $this->setValue($phone, $text);
                break;
            default:
                # code...
                break;
    	}
        if (!empty($this->message)) {
    	   $this->send_message($phone, $this->message);
        }
    	// echo '<pre>'; print_r($this->message); echo '</pre>';
    	// exit('end');
    }

    private function check_price($phone, $key)
    {
    	$detail = $this->msms->getDetailRegisterByPhone($phone);
    	
    	$price = $this->msms->getPriceByFarmer($detail['FarmerID']);
    	// echo '<pre>'; print_r($price); echo '</pre>';
    	if (empty($price)) {
    		$this->message = "Harga tidak tersedia";
    		return false;
    	}
        if ($key=='FF' && empty($price['FFPrice'])) {
            $this->message = "Harga tidak tersedia";
            return false;
        }
        if ($key=='FAQ' && empty($price['FAQPrice'])) {
            $this->message = "Harga tidak tersedia";
            return false;
        }

        if ($key!='FF' && $key!='FAQ' && empty($price['FFPrice']) && empty($price['FAQPrice'])) {
            $this->message = "Harga tidak tersedia";
            return false;
        }

    	$msg = 'Info';
    	switch ($key) {
    		case 'FF':                
    			$msg .= 'Harga FF Rp '.number_format($price['FFPrice'],2,',','.');
    			break;
    		case 'FAQ':
    			$msg .= 'Harga FAQ Rp '.number_format($price['FAQPrice'],2,',','.');
    			break;
    		
    		default:
                if ($price['FFPrice']!="") {
                    $msg .= 'Harga FF Rp '.number_format($price['FFPrice'],2,',','.');    
                }
    			if ($price['FAQPrice']!="") {
                    $msg .= 'Harga FAQ Rp '.number_format($price['FAQPrice'],2,',','.');    
                }
    			break;
    	}
    	$this->message = $msg;
    	return true;
    }

    private function send_message($to, $msg)
    {
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
        foreach ($response['messages'] as $key => $value) {
        	$params = array_merge($params, $value);
        	$this->msms->insertOutbound($params);
        	// echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        }
        // echo '<pre>'; print_r($response); echo '</pre>';
        // echo "<h1>Text Message</h1>";
        // $this->nexmo->d_print($response);
        // echo "<h3>Response Code: ".$this->nexmo->get_http_status()."</h3>";
        // exit;
    }

    private function register($farmer_id, $phone)
    {
    	if (!$this->msms->isFarmer($farmer_id)) {
    		// not a farmer
    		$this->message = "Farmer ID salah";
    		return false;
    	}

    	if ($this->msms->isRegistered($farmer_id, $phone)) {
    		// farmer & number already registered
    		$this->message = "Anda sudah terdaftar";
    		return false;
    	}

    	if ($this->msms->isPhoneRegistered($farmer_id, $phone)) {
    		// farmer & number already registered
    		$this->message = "Nomor ini sudah terdaftar untuk Farmer ID lain";
    		return false;
    	}

    	if (!$this->msms->insertRegister($farmer_id, $phone, 'active', 'REG')) {
    		// failed to register
    		$this->message = "Pendaftaran gagal";
    		return false;
    	}
    	
    	$this->message = 'Pendaftaran berhasil';

    	return true;
    }

    private function unregister($farmer_id, $phone)
    {
    	if (!$this->msms->isFarmer($farmer_id)) {
    		// not a farmer
    		$this->message = "Farmer ID salah";
    		return false;
    	}

    	if (!$this->msms->isRegistered($farmer_id, $phone)) {
    		// farmer & number already registered
    		$this->message = "Anda belum terdaftar";
    		return false;
    	}

    	if (!$this->msms->updateRegister($farmer_id, $phone, 'nullified', 'UNREG')) {
    		// failed to register
    		$this->message = "Unregister gagal";
    		return false;
    	}

    	$this->message = 'Unregister berhasil';

    	return true;
    }

    public function setValue($phone, $text)
    {
        $staff = $this->msms->isRegisteredStaff($phone);
        if (!$staff !== false) {
            $this->message = "Nomor Anda belum terdaftar";
            return false;
        }

        $tmp        = explode('/', $text);
        $object     = $tmp[0];
        $allowed_function = array(
            'HARGA',
        );
        if (!in_array(strtoupper($object), $allowed_function)) {
            $this->message = "Format salah, Anda tidak bisa mengeset {$tmp[0]}";            
            return false;
        }
        if ($object == 'HARGA') {            
            $district   = trim($tmp[1]);
            $date       = date('Y-m-d', strtotime($tmp[2]));
            $type       = strtoupper($tmp[3]);
            $price      = floatval($tmp[4]);
            if (
                ($type !== 'FF' AND $type !== 'FAQ')
                OR $date == '1970-01-01'
            ) {
                $this->message = "Format salah, format yang benar SET HARGA/[district]/[yyyy-mm-dd]/[FF/FAQ]/[harga]";
                return false;   
            }
            if ($price <= 0) {
                $this->message = "Harga harus lebih dari nol (0)";            
                return false;
            }
            $district_id = $this->msms->getDistrictID($district);
            if (!$district_id !== false) {
                $this->message = "District {$district} tidak ditemukan";
                return false;
            }

            $old_price = $this->msms->getPrice($district_id, $type, $date);
            if (!$this->msms->setPrice($district_id, $type, $date, $price, $staff['UserID'])) {
                $this->message = "Gagal mengubah harga";
                return false;
            }

            if (!empty($old_price)) {
                $this->message = "Berhasil mengubah harga {$type} {$district} {$date} dari {$old_price['CocoaPrice']} menjadi {$price}";
            } else {
                $this->message = "Berhasil menentukan harga {$type} {$district} {$date} dengan {$price}";
            }
        }        

        return true;
           
    }

}

/* End of file sms.php */
/* Location: ./application/controllers/sms.php */