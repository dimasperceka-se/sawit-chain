<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Example extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mexample');
    }

    function status_get()
    {
        $latitude = $this->get('latitude');
        $longitude = $this->get('longitude');
        $speed = $this->get('speed');

        $status = $this->mexample->saveStatus($latitude, $longitude, $speed);

        if($status)
        {
            $this->response($status, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Couldn\'t find any status!'), 404);
        }
    }

	function data_get()
	{
		$status = $this->mexample->readStatus();

        if($status)
        {
            $this->response($status, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Couldn\'t find any status!'), 404);
        }
		
	}



}
