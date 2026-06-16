<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Coop_payslip extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('coop/mpayroll');
	}

	function printslip()
	{
		$this->load->view('cetak_payslip_coop');
	}

}
?>
