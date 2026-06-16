<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class System extends SS_Controller {
   
   public function __construct() {
      parent::__construct(1);
	}

	public function index() {
	   $this->load->view('system/unit');
	}

}

