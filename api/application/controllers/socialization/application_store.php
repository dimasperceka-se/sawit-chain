<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Application_store extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->file = $_FILES;
		$this->load->library('zip');
        $this->load->model('socialization/store');
    }

    public function main_list_get(){
		$data = $this->store->getMainListAppForm($this->get('sort'), $this->get('start'),$this->get('limit'), $this->get('key'),$this->get('ProvinceID'),$this->get('DistrictID'),$this->get('SubDistrictID')); 
	   
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }

	public function main_list_participant_get(){
		$data = $this->store->getMainListParticipantForm($this->get('sort'), $this->get('start'),$this->get('limit'), $this->get('IMSSocID'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }

	//ini fungsi ketika add participant
	public function main_application_list_get(){

		$data = $this->store->getMainListapplication($this->get('sort'),$this->get('start'),$this->get('limit'), $this->get('IMSID'), $this->get('IMSSocID'), $this->get('key'),$this->get('ProvinceID'),$this->get('DistrictID'),$this->get('SubDistrictID'),$this->get('CPGid'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }

    public function savedata_post() {
         $proses = $this->store->insertApp($this->post());
         if($proses) {
			$Varid = (int) $proses;
			$id = $Varid == 0 ? 0 : $Varid;
			$results['data'] = $this->store->getLastInsertDataSocializ($id);
			$results['success'] = true;
            $results['message'] = "record created.";
		 } else {
            $results['data'] = '';
			$results['success'] = false;
            $results['message'] = "Failed to create record";
        }
		$this->response($results, 200);
    }

	function loadappdata_get() {
        if(!$this->get('IMSSocID')) $this->response(NULL, 400);
        $data = $this->store->loadappdata($this->get('IMSSocID'));
        $this->response($data, 200);
    }


	function getDataSocialization_get() {
        if(!$this->get('IMSSocID')) $this->response(NULL, 400);
        $data = $this->store->getDataSocialization($this->get('IMSSocID'));
        $this->response($data, 200);
    }

	function appform_delete() {
        if(!$this->delete('IMSSocID')) $this->response(NULL, 400);
        $data = $this->store->deleteAppform($this->delete('IMSSocID'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Data could not be delete'), 404);
    }

	public function appid_post()
	{
		$sql = "SELECT max(ApplicantID) as maxKode FROM ktv_applicant_farmers ";
		$k =  $this->db->query($sql)->row_array();
		$kode = $k['maxKode'];
		$noUrut = (int) $kode;
		$noUrut++;
		$newID =  sprintf("%06s", $noUrut);
		$this->response(array('data' => $newID), 200);
	}

	function save_participant_post()
	{
		if(!$this->post('IMSSocID')) $this->response(NULL, 400);
        @$proses = $this->store->save_participant($this->post()); 
        if($proses) {
			$results['success'] = true;
            $results['message'] = "record created.";
		 } else {
            $results['data'] = '';
			$results['success'] = false;
            $results['message'] = "Failed to create record";
        }
		$this->response($results, 200);
	}
	
	function saveexistingfarmerbyweb_post()
	{
		if(!$this->post('IMSSocID')) $this->response(NULL, 400);
        @$proses = $this->store->saveexistingfarmerbyweb($this->post()); 
        if($proses) {
			$results['success'] = true;
            $results['message'] = "record created.";
		 } else {
            $results['data'] = '';
			$results['success'] = false;
            $results['message'] = "Failed to create record";
        }
		$this->response($results, 200);
	}


	function comboharievent_get()
	{
		$IMSSocID = (int) $this->get('IMSSocID');
		$data = $this->store->comboharievent($IMSSocID);
        $this->response($data, 200);
	}

	function save_attandance_participant_post()
	{
		if(!$this->post('IMSSocID')) $this->response(NULL, 400);
        $proses = $this->store->save_attandance_participant($this->post());
		 
		if($proses) {
			$results['success'] = true;
            $results['message'] = "record created.";
		 } else {
            $results['data'] = '';
			$results['success'] = false;
            $results['message'] = "Failed to create record";
        }
		$this->response($results, 200);
	}

	//simpan semua data peserta yg tidak ikut diabsen dari proses di >> save_attandance_participant_post()
	function save_participant_to_attadance_post()
	{
		if(!$this->post('IMSSocID')) $this->response(NULL, 400);
        $proses = $this->store->save_participant_to_attadance($this->post());
        
		if($proses) {
			$results['success'] = true;
            $results['message'] = "record created.";
		 } else {
            $results['data'] = '';
			$results['success'] = false;
            $results['message'] = "Failed to create record";
        }
		$this->response($results, 200);
	}
        
	public function main_attandance_list_get(){
		$data = $this->store->main_attandance_list($this->get('sort'), $this->get('start'),$this->get('limit'), $this->get('IMSSocID'), $this->get('DayNumber'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }
	
	public function main_existingfarmer_list_get(){
		$data = $this->store->getmain_existingfarmer_list($this->get('sort'), $this->get('start'),$this->get('limit'),$this->get('key'),$this->get('ProvinceID'),$this->get('DistrictID'),$this->get('SubDistrictID'),$this->get('CPGid'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }
	
	
	//Cetak Kosong
	function cetakkosong_get($id) {
        $data['data'] = $this->store->getDataSocializationCetakHeader($id);
		$data['peserta'] = $this->store->getDataParticipantCetak($id);
		$data['staff'] = $this->store->getDataStaffCetak($id);

        $data['logo'] = $this->store->readPartnerLogo($id);
        $this->load->view('socialization_cetak_hadir', $data);
    }
	//cetak isi
	function cetakisi_get( $params ) {

        $p = explode('_', $params);
		$id = $p[0];$days = $p[1];
		$data['data'] = $this->store->getDataSocializationCetakHeader($id);
		$data['eventDate'] = $this->store->getDataSocializationAttRow($id,$days );
		$data['days'] = $days;
		$data['peserta'] = $this->store->getDataAttandanceCetak($id, $days);

		$data['staff'] = $this->store->getDataStaffAttandanceCetak($id, $days);
        $data['logo'] = $this->store->readPartnerLogo($id);
        $this->load->view('socialization_cetak_hadir', $data);
    }

	function savedata_rekomendasi_post()
	{

		$proses = $this->store->savedataRekomendasi($this->post());
        if($proses) {
			$results['success'] = true;
            $results['message'] = "record created.";
		 } else {
            $results['data'] = '';
			$results['success'] = false;
            $results['message'] = "Failed to create record";
        }
		$this->response($results, 200);
	}
    
	/*
	function savedata_selection_post()
	{

		$proses = $this->store->savedata_selection($this->post());
        if($proses) {
			$results['success'] = true;
            $results['message'] = "record created.";
		 } else {
            $results['data'] = '';
			$results['success'] = false;
            $results['message'] = "Failed to create record";
        }
		$this->response($results, 200);
	}
	*/
	
	//cronjob running
	function generatefarmer_get()
	{
		 $this->store->cronGenerateFarmerbySocialization(); 
	}

	function gen_farmer_from_socialization_get(){
		$ApplicantIDIn = $this->uri->segment(4);
		$proses = $this->store->GenFarmerFromSoc($ApplicantIDIn);
		$this->response($proses, 200);
	}
	
	 //Untuk Daftar list Staff yang masuk ke tabel socialization stafff
	 public function main_list_staff_get(){
		$data = $this->store->Getmain_list_staff($this->get('start'),$this->get('limit'),  $this->get('IMSSocID'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }

	//Untuk Daftar list Staff
	public function main_staff_get(){
		$data = $this->store->Getmain_staff($this->get('start'),$this->get('limit'), $this->get('key'), $this->get('IMSSocID'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }
	function save_staff_post()
	{
		if(!$this->post('IMSSocID')) $this->response(NULL, 400);
        $proses = $this->store->save_staff($this->post());
        if($proses) {
			$results['success'] = true;
            $results['message'] = "record created.";
		 } else {
            $results['data'] = '';
			$results['success'] = false;
            $results['message'] = "Failed to create record";
        }
		$this->response($results, 200);
	}

	function hapusstaff_delete() {
        if(!$this->delete('SocStaffID')) $this->response(NULL, 400);
        $data = $this->store->Hapusstaff($this->delete('SocStaffID') );
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Data could not be delete'), 404);
    }

	//simpan semua data peserta yg tidak ikut diabsen dari proses di >> save_attandance_staff_post()
	function save_staff_to_attadance_post()
	{
		if(!$this->post('IMSSocID')) $this->response(NULL, 400);
        $proses = $this->store->save_staff_to_attadance($this->post());
        if($proses) {
			$results['success'] = true;
            $results['message'] = "record created.";
		 } else {
            $results['data'] = '';
			$results['success'] = false;
            $results['message'] = "Failed to create record";
        }
		$this->response($results, 200);
	}

	public function main_staffattandance_list_get(){
		$data = $this->store->main_staffattandance_list($this->get('start'),$this->get('limit'), $this->get('IMSSocID'), $this->get('DayNumber'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }

	function save_attandance_staff_post()
	{
		if(!$this->post('IMSSocID')) $this->response(NULL, 400);
        $proses = $this->store->save_attandance_staff($this->post());
        if($proses) {
			$results['success'] = true;
            $results['message'] = "record created.";
		 } else {
            $results['data'] = '';
			$results['success'] = false;
            $results['message'] = "Failed to create record";
        }
		$this->response($results, 200);
	}
	
	function getcheckedfromparticipant_post()
	{
		if(!$this->post('IMSSocID')) $this->response(NULL, 400);
        $proses = $this->store->getcheckedfromparticipant($this->post('IMSSocID'));
        if($proses) {
			$results['data'] = $proses;
		 } else {
            $results['data'] = ''; 
        }
		$this->response($results, 200);
	}

	//For Mobile API
	public function download_get($District)
    {
        $Districts = urldecode($District);
		$rows = $this->store->getSocializeEvent($Districts);
        if ($rows !== false) {
            $this->load->dbutil(); 
            $data = $this->dbutil->csv_from_result($rows);
            $this->load->helper('download');
            force_download("Socialization Event {$Districts}.csv", $data);
        } else {
            $this->response(array('error' => lang('Data empty')), 404);
        }
    }

	public function socializationupload_post()  
    {
		$path =''; 
		
		$is_upload = false;
		if (!empty($_FILES)) {
				$config['upload_path']      = './files/socialization_event/';
				$config['allowed_types']    = 'zip';
				$config['max_size']         = '8192';
				$this->load->library('upload', $config);
				if ( ! $this->upload->do_upload('filezip')) {
					$error = array('error' => $this->upload->display_errors());
					$this->response(array('success' => false, 'message' => $error['error']), 400);
				} else {
					$is_upload = true;
					$data = array('upload_data' => $this->upload->data());
            		$full_path = $data['upload_data']['full_path'];

					/**** without library ****/
					$zip = new ZipArchive;
		
					if ($zip->open($data['upload_data']['full_path']) === TRUE) {
						$zip->extractTo(FCPATH.'/files/socialization_event/'.$data['upload_data']['raw_name'].'/');
						$zip->close();

						$files = array_slice(scandir(FCPATH.'/files/socialization_event/'.$data['upload_data']['raw_name'].'/'),2);

						unlink($data['upload_data']['full_path']);

						// echo "<pre>";
						// print_r(FCPATH);
						// print_r($files);
						// print_r($data['upload_data']['raw_name']);
						// print_r($data['upload_data']);
						// print_r($files);
						// print_r($files);
						// die;
						
						if (!empty($files)) {
							$json_found = false;
							$file_attachment = array();
							foreach ($files as $file) {
								if (strpos($file, '.json')) {
									
									$json_found = true;
									$data_post = json_decode(file_get_contents(FCPATH.'/files/socialization_event/'.$data['upload_data']['raw_name'].'/'.$file), true);
									// echo '<pre>'; print_r($data_post); echo '</pre>'; exit;
									$path = str_replace('.json','', $file); // utk mendapatkan folder nya.
								} else {
									$tmp = explode('_', $file);
									$file_attachment[$tmp[1]][] = $data['upload_data']['full_path'].'/'.$file;
								}
							}
							$data_post['file_attachment'] = $file_attachment;
							if ($json_found === false) {
								$this->response(array('success' => false, 'message' => 'Attendance file not found, please check your file!'), 400);
							}
						}
					} else {
						$this->response(array('success' => false, 'message' => 'Can not read zip file, please check your file!'), 400);
					}
				}
		} else {
			$data_post = $this->post('null');
		}

		$debug = [];
		$debug['datapost'] = $data_post;
		$debug['files'] = $files;
	     
		
		//////////////////// BUAT TES AJA INI ////////////////
		//$upload_path = './files/socialization_event/upload-data-soz_training-20180406115529.zip/'; 
		//$files = 'upload-data-soz_training-20180326112632.zip'; 
		//$data_post = json_decode(file_get_contents($upload_path.'upload-data-soz_training-20180406115529.json'), true);
		//////////////////END BUAT TES////////////////////////////////////////
	    //print_r($data_post);die;

		$IMSSocID    = $data_post['IMSSocID']; 
		$FILESEVENT =  $data_post['other_file_attachment'];
		
		if ($this->store->checkSocializationData($IMSSocID) !== false) {
			$participants   = $data_post['participants'];
			$this->db->trans_start(FALSE);
			if (!empty($participants)){
				foreach ($participants as $key => $participant) {
					$ParticipantID     		= $participant['ApplicantID']; 
					$RecommendationStatus 	= $participant['RecommendationStatus'];
					$FieldAgentName    		= $participant['FieldAgentName'];
					$date ='';
					if(isset($participant['RecommendationDate']))
					{
						$mydate = new DateTime(@$participant['RecommendationDate']);
						$date = $mydate->format('Y-m-d'); 
					} 
					$RecommendationDate 			= $date;
					$Comments      					= @$participant['Comments'];
					$LearningContractSign			= @$participant['LearningContractSign'];
					$LearningContractStatus			= @$participant['LearningContractStatus'];	
					$apply_certification			= @$participant['apply_certification'];
					$apply_certificationStatus		= @$participant['apply_certificationStatus'];
					
					if ($this->store->checkParticipant($IMSSocID, $ParticipantID) == 0 ) { 
						$this->store->addParticipant($IMSSocID, $ParticipantID, $RecommendationStatus, $FieldAgentName, $RecommendationDate , $Comments,$LearningContractSign,$LearningContractStatus,$apply_certification, $apply_certificationStatus);
					} else {
						$this->store->editParticipant($IMSSocID, $ParticipantID, $RecommendationStatus, $FieldAgentName, $RecommendationDate , $Comments,$LearningContractSign,$LearningContractStatus,$apply_certification, $apply_certificationStatus);
					}
					
					//daftar kan files event ke tabel ktv_ims_socialization_files
					if($FILESEVENT !=''){
						$NamesFile = explode(",",$FILESEVENT);
						$this->store->addFielsEventFoto($IMSSocID, $NamesFile); 
					}
				}
			}
			  
			$attendances_list     = $data_post['attendances_list'];
			if (!empty($attendances_list)){
				//print_r($attendances_list);die;
				foreach ($attendances_list as $key => $attendances) { 
					$day_number     	  = $attendances['day'];  
					$datetr ='';
					if(isset($attendances['training_date']))
					{
						$tdate = new DateTime(@$participant['training_date']);
						$datetr = $tdate->format('Y-m-d'); 
					} 
					
					$training_date  	  = $datetr;
					$attendance    		  = $attendances['attendance'];
					
					 
					foreach($attendance  as $key => $attendancedata){
						$ParticipantID      = $attendancedata['ApplicantID'];
						$file_attachement = $attendancedata['file_attachement']; 
						$AttendanceStatus = $attendancedata['AttendanceStatus'];   
						if ($this->store->checkAttendance($IMSSocID, $ParticipantID, $day_number) == 0)
						{
							$this->store->addAttendance($IMSSocID, $ParticipantID, $day_number, $training_date, $AttendanceStatus, $file_attachement, $path );
						} else {
							$this->store->editAttendance($IMSSocID, $ParticipantID, $day_number, $training_date, $AttendanceStatus, $file_attachement, $path );
						}
						//$return[] = array( $ApplicantID => $this->db->last_query()); //ngedebug
					}
					$debug['query'] = $this->db->last_query();
					
				}
				 
				$return = array('success' => $this->db->trans_status());
				if ($this->post('debug_me_please') == 1) {
					$return['debug'] = $debug;
				}
			}
			 
		}else {
            $return = array('success' => false, 'message' => lang("IMSSocID {$IMSSocID} doesn't exist"));
        }

		 
		if ($is_upload == true) {
            $this->load->helper('file');
            // echo '<pre>'; print_r($data['upload_data']['full_path']); echo '</pre>'; exit;
            //delete_files($data['upload_data']['full_path'], true);
            if (is_dir($data['upload_data']['full_path'])) {
                //rmdir($data['upload_data']['full_path']);
            }
        }
		 
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $this->response($return, 200);

	}

	public function loadappdata_viewonly_get(){
    	if(!$this->get('IMSSocID')) $this->response(NULL, 400);
        $data = $this->store->loadappdataViewonly($this->get('IMSSocID'));
        $this->response($data, 200);
    }

    public function loadappdata_recommendation_viewonly_get(){
    	$IMSSocID = (int) $this->get('IMSSocID');
    	$ApplicantID = (int) $this->get('ApplicantID');
    	$ParticipantID = (int) $this->get('ParticipantID');
    	$data = $this->store->loadappdataRecommendationViewonly($IMSSocID,$ApplicantID,$ParticipantID);
        $this->response($data, 200);
    }
	
	 //delete participant
	 function appformparticipant_delete() {
        if(!$this->delete('ParticipantID')) $this->response(NULL, 400);
        $data = $this->store->appformparticipantDelete($this->delete('ParticipantID') );
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Data could not be delete'), 404);
    } 
	
	function delSelectedparticipant_delete() { 
        $data = $this->store->delSelectedparticipantDelete($this->delete('ApplicantID'), $this->delete('IMSSocID'), $this->delete('existingfarmer') );
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Data could not be delete'), 404);
    } 
	
	function checkbelumsyncalert_post(){
		$data = $this->store->checkbelumsyncalert($this->post('IMSSocID'));  
		echo $data;die;
	}
	//donwload all data socialization
	public function exportexcelsocialization_get(){
		$details = $this->store->ExportExcelData();
        set_time_limit(0);
        ini_set('memory_limit','2500M'); 
        //echo "<pre>".print_r($details,1);exit;
        $this->load->library('Excel', null, 'PHPExcel');
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';
        $object = new PHPExcel();

        // Set properties
        $object->getProperties()->setCreator("Koltiva Cocoatrace")
                       ->setLastModifiedBy("Koltiva Cocoatrace")
                       ->setCategory("Koltiva Cocoatrace");
        // Add some data

        $style_center = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );


        $style_border = array(
              'borders' => array(
                  'allborders' => array(
                      'style' => PHPExcel_Style_Border::BORDER_THIN
                  )
              )
          );
         
        $title = "Socialization Data - ".date('Y');
       
        $object->getActiveSheet()->getColumnDimension('A')->setWidth(6);
        $object->getActiveSheet()->getColumnDimension('B')->setWidth(30);
        $object->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $object->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $object->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $object->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $object->getActiveSheet()->getColumnDimension('H')->setWidth(10);  
		$object->getActiveSheet()->getColumnDimension('I')->setWidth(25);
		$object->getActiveSheet()->getColumnDimension('J')->setWidth(10);
		$object->getActiveSheet()->getColumnDimension('K')->setWidth(25);
		$object->getActiveSheet()->getColumnDimension('L')->setWidth(15);
		$object->getActiveSheet()->getColumnDimension('M')->setWidth(25);
		
		$object->getActiveSheet()->mergeCells('A1:K1');
        $object->getActiveSheet()->getStyle("A1:K4")->applyFromArray($style_center);
        $object->getActiveSheet()->getStyle("A4:K4")->applyFromArray($style_border); 
        $object->getActiveSheet()->getStyle("A1:K4")->getFont()->setBold(true); 
        $object->setActiveSheetIndex(0)->setCellValue('A1', $title); //Judul
		$object->setActiveSheetIndex(0)->setCellValue('A4', 'No');
        $object->setActiveSheetIndex(0)->setCellValue('B4', 'Event ID');
        $object->setActiveSheetIndex(0)->setCellValue('C4', 'Event Name');
        $object->setActiveSheetIndex(0)->setCellValue('D4', 'Batch');
        $object->setActiveSheetIndex(0)->setCellValue('E4', 'District');
        $object->setActiveSheetIndex(0)->setCellValue('F4', 'SubDistrict');
        $object->setActiveSheetIndex(0)->setCellValue('G4', 'Village');
        $object->setActiveSheetIndex(0)->setCellValue('H4', 'Jumlah Peserta');
        $object->setActiveSheetIndex(0)->setCellValue('I4', 'Date Of Event'); 
		$object->setActiveSheetIndex(0)->setCellValue('J4', 'Days'); 
		$object->setActiveSheetIndex(0)->setCellValue('K4', 'Certification Holder'); 
		$object->setActiveSheetIndex(0)->setCellValue('L4', 'Status Event'); 
		$object->setActiveSheetIndex(0)->setCellValue('M4', 'Date Updated'); 
		$i=0;
		$counter=5; //MULAI ROWS SETELAH JUDUL HEADER
		if($details){
			foreach ($details as $key => $val) { 
				$object->getActiveSheet()->getStyle("A$counter:M$counter")->applyFromArray($style_border);
				$object->getActiveSheet()->setCellValue('A'.$counter, ($i+1) );
				$object->getActiveSheet()->setCellValue('B'.$counter, $val['IMSSocID']);
				$object->getActiveSheet()->setCellValue('C'.$counter, $val['EventName']);
				$object->getActiveSheet()->setCellValue('D'.$counter, $val['PartnerID']);
				$object->getActiveSheet()->setCellValue('E'.$counter, $val['District']); 
				$object->getActiveSheet()->setCellValue('F'.$counter, $val['SubDistrict']);
				$object->getActiveSheet()->setCellValue('G'.$counter, $val['VillageName']);
				$object->getActiveSheet()->setCellValue('H'.$counter, $val['peserta']);
				$object->getActiveSheet()->setCellValue('I'.$counter, $val['EventStart']);
				$object->getActiveSheet()->setCellValue('J'.$counter, $val['EventDays']);
				$object->getActiveSheet()->setCellValue('K'.$counter, $val['CertHolderOrgName']);
				$object->getActiveSheet()->setCellValue('L'.$counter, $val['SocializationStatus'] == 1 ? 'Complete' : 'On Going' );
				$object->getActiveSheet()->setCellValue('M'.$counter, $val['DateUpdated']);
				$i++;
				$counter++;
			}
		}
		 
        $konter = $counter;
        $konter++;
 
        $object->setActiveSheetIndex(0);
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$title.'.xlsx');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
	
	//donwload all data socialization participant
	public function exportexcelsocializationParticipant_get($IMSSocID){
		$row = $this->store->ExportHeaderrow($IMSSocID );
		$details = $this->store->ExportExcellogevent($IMSSocID ); 
		 
        set_time_limit(0);
        ini_set('memory_limit','2500M'); 
        //echo "<pre>".print_r($details,1);exit;
        $this->load->library('Excel', null, 'PHPExcel');
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';
        $object = new PHPExcel();

        // Set properties
        $object->getProperties()->setCreator("Koltiva Cocoatrace")
                       ->setLastModifiedBy("Koltiva Cocoatrace")
                       ->setCategory("Koltiva Cocoatrace");
        // Add some data

        $style_center = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );


        $style_border = array(
              'borders' => array(
                  'allborders' => array(
                      'style' => PHPExcel_Style_Border::BORDER_THIN
                  )
              )
          );
         
        $title = "Participant Event  - ".$row->EventName.' DateUpdated : '.$row->DateUpdated;
       
        $object->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $object->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $object->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $object->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $object->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $object->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $object->getActiveSheet()->getColumnDimension('G')->setWidth(15); 
		$object->getActiveSheet()->mergeCells('A1:G1');
        $object->getActiveSheet()->getStyle("A1:G4")->applyFromArray($style_center);
        $object->getActiveSheet()->getStyle("A4:G4")->applyFromArray($style_border); 
        $object->getActiveSheet()->getStyle("A1:G4")->getFont()->setBold(true); 
        $object->setActiveSheetIndex(0)->setCellValue('A1', $title); //Judul
		$object->setActiveSheetIndex(0)->setCellValue('A4', 'Participant ID');
        $object->setActiveSheetIndex(0)->setCellValue('B4', 'Applicant Name');
        $object->setActiveSheetIndex(0)->setCellValue('C4', 'Farmer Group');
        $object->setActiveSheetIndex(0)->setCellValue('D4', 'Participate Socialization');
        $object->setActiveSheetIndex(0)->setCellValue('E4', 'Recommendation');
        $object->setActiveSheetIndex(0)->setCellValue('F4', 'Selection Status');
        $object->setActiveSheetIndex(0)->setCellValue('G4', 'Remarks'); 
		$i=0;
		$counter=5; //MULAI ROWS SETELAH JUDUL HEADER
		if($details){
			foreach ($details as $key => $val) { 
				$object->getActiveSheet()->getStyle("A$counter:G$counter")->applyFromArray($style_border); 
				$object->getActiveSheet()->setCellValue('A'.$counter, $val['ApplicantID']);
				$object->getActiveSheet()->setCellValue('B'.$counter, $val['Fullname']);
				$object->getActiveSheet()->setCellValue('C'.$counter, $val['GroupName']);			
				$object->getActiveSheet()->setCellValue('D'.$counter, $val['ParticipateInSocializationStatus'] == 1 ? 'Yes' : 'No' );
				$object->getActiveSheet()->setCellValue('E'.$counter, $val['RecommendationStatus'] == 1 ? 'Yes' : 'No' ); 
				$object->getActiveSheet()->setCellValue('F'.$counter, $val['SelectionStatus'] == 1 ? 'Yes' : 'No' );
				$object->getActiveSheet()->setCellValue('G'.$counter, $val['SelectionRemarks']); 
				$i++;
				$counter++;
			}
		}
        $konter = $counter;
        $konter++;
 
        $object->setActiveSheetIndex(0);
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$title.'.xlsx');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
	
	//donwload all data socialization participant
	public function exportexcelsocializationStaff_get($IMSSocID){
		$row = $this->store->ExportHeaderrow($IMSSocID );
		$details = $this->store->ExportExcelstaff($IMSSocID ); 
		 
        set_time_limit(0);
        ini_set('memory_limit','2500M'); 
        //echo "<pre>".print_r($details,1);exit;
        $this->load->library('Excel', null, 'PHPExcel');
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';
        $object = new PHPExcel();

        // Set properties
        $object->getProperties()->setCreator("Koltiva Cocoatrace")
                       ->setLastModifiedBy("Koltiva Cocoatrace")
                       ->setCategory("Koltiva Cocoatrace");
        // Add some data

        $style_center = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );


        $style_border = array(
              'borders' => array(
                  'allborders' => array(
                      'style' => PHPExcel_Style_Border::BORDER_THIN
                  )
              )
          );
         
        $title = "Staff Event  - ".$row->EventName.' DateUpdated : '.$row->DateUpdated;
       
        $object->getActiveSheet()->getColumnDimension('A')->setWidth(10); 
		$object->getActiveSheet()->getColumnDimension('B')->setWidth(10); 
		$object->getActiveSheet()->mergeCells('A1:H1');
        $object->getActiveSheet()->getStyle("A1:B4")->applyFromArray($style_center);
        $object->getActiveSheet()->getStyle("A4:B4")->applyFromArray($style_border); 
        $object->getActiveSheet()->getStyle("A1:B4")->getFont()->setBold(true); 
        $object->setActiveSheetIndex(0)->setCellValue('A1', $title); //Judul
		$object->setActiveSheetIndex(0)->setCellValue('A4', 'No'); 
		$object->setActiveSheetIndex(0)->setCellValue('B4', 'Staff'); 
		$i=0;
		$counter=5; //MULAI ROWS SETELAH JUDUL HEADER
		if($details){
			foreach ($details as $key => $val) { 
				$object->getActiveSheet()->getStyle("A$counter:B$counter")->applyFromArray($style_border); 
				$object->getActiveSheet()->setCellValue('A'.$counter, ($i+1) );
				$object->getActiveSheet()->setCellValue('B'.$counter, $val['PersonNm']); 
				$i++;
				$counter++;
			}
		}
        $konter = $counter;
        $konter++;
 
        $object->setActiveSheetIndex(0);
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$title.'.xlsx');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
}
?>
