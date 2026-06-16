<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Price extends REST_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('price/mcocoa');
    }

    public function cocoa_get()
    {
        $data = $this->mcocoa->getPrice($this->get('date'));
        $this->response($data, 200);
    }

    public function generate_get()
    {
        $this->mcocoa->generatePrice();
        $this->response(true);
    }

    public function schedule_get()
    {
        $this->mcocoa->schedulePrice();
        $this->response(true);
    }
}