<?php defined('BASEPATH') or exit('No direct script access allowed');

class Notification extends REST_Controller
{

    public $_output = array('success' => false, 'error' => 'Data is not valid'); //response data

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('UTC');
        $this->load->model('traceability_api/mnotification', '_model');
    }

    public function fetch_get()
    {
        ini_set('display_errors', true);
        error_reporting(E_ALL);
        $userid = $this->get('userid');
        $data = $this->_model->_getNotification($userid);
        if ($data) {
            return $this->response(array('success' => true, 'data' => $data), 200);
        } else {
            return $this->response(array('success' => false, 'data' => array()), 200);
        }
    }

}
