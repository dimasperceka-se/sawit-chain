<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Coop_payroll extends REST_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('coop/mpayroll');
	}

	function staffs_get()
	{
		$data = $this->mpayroll->getStaffs($this->get('key'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Error occured. Please try again later'), 200);
	}

	function rate_salary_get()
	{
		$data = $this->mpayroll->getRateSalaryData(getCoopID(),$this->get('Awal'),$this->get('Akhir'),$this->get('staffName'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Error occured. Please try again later'), 200);
	}

	function rate_salary_post()
	{
			$data = $this->mpayroll->insertRateSalary($this->post());
	        if($data) $this->response($data, 200);
	      else $this->response(array('error' => 'Error occured. Please try again later'), 200);
	}

	function timesheet_post()
	{
		$data = $this->mpayroll->timesheet($this->post());
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Error occured. Please try again later'), 200);
	}

	function timesheet_staff_post()
	{
		$data = $this->mpayroll->insertTimesheet($this->post());		
		if($data) $this->response($data, 200);
	    else $this->response(array('error' => 'Error occured. Please try again later'), 200);
	}

	function get_idtimesheet_get()
	{
		$data = $this->mpayroll->getIdTimesheet();		
		if($data) $this->response($data, 200);
	    else $this->response(array('error' => 'Error occured. Please try again later'), 200);
	}

	function timesheet_staff_list_get()
	{
		$data = $this->mpayroll->getTimesheetStaffList($this->get('TimesheetID'));		
		if($data) $this->response($data, 200);
	    else $this->response(array('error' => 'Error occured. Please try again later'), 200);
	}

	function generate_payroll_get()
	{
		$data = $this->mpayroll->getGeneratePayroll($this->get('searchAwal'),$this->get('searchAkhir'));		
		if($data) $this->response($data, 200);
	    else $this->response(array('error' => 'Error occured. Please try again later'), 200);
	}

	function save_generated_payroll_post()
	{
		$data = $this->mpayroll->saveGeneratePayroll($this->post('postdata'),$this->post('searchAwal'),$this->post('searchAkhir'));		
		if($data) $this->response($data, 200);
	    else $this->response(array('error' => 'Error occured. Please try again later'), 200);
	}

	function payslip_get()
	{
		$data = $this->mpayroll->data($this->get('searchAwal'),$this->get('searchAkhir'));		
		if($data) $this->response($data, 200);
	    else $this->response(array('error' => 'Error occured. Please try again later'), 200);
	}

	function print_payslip_get()
	{
		$id = $this->get('StaffPayrollID');

		$this->load->model('coop/mpayroll');

		error_reporting(E_ALL);
		ini_set('display_errors', 1);
		require_once '/Applications/MAMP/htdocs/cocoatrace4api/application/libraries/dompdf-0.6.2/dompdf_config.inc.php';

		$dompdf = new Dompdf();

		// 	$data['users']=array(
		// 	array('firstname'=>'Agung','lastname'=>'Setiawan','email'=>'ag@setiawan.com'),
		// 	array('firstname'=>'Hauril','lastname'=>'Maulida Nisfar','email'=>'hm@setiawan.com'),
		// 	array('firstname'=>'Akhtar','lastname'=>'Setiawan','email'=>'akh@setiawan.com'),
		// 	array('firstname'=>'Gitarja','lastname'=>'Setiawan','email'=>'git@setiawan.com')
		// );
 	
 		$data= $this->mpayroll->data(null,null,$id)['data'][0];
		// print_r($data);
 	// 	exit;

	    $html = $this->load->view('cetak_payslip', $data, true);

	 	$dompdf->load_html($html);
	    $dompdf->render();
	    $dompdf->stream('dasdsa.pdf',array("Attachment"=>0));

	}



	function tes_get()
	{
		print_r($_SESSION);
	}
}
?>