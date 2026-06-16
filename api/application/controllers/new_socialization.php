<?php

defined('BASEPATH') or exit('No direct script access allowed');

class New_socialization extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('certification/msocialization');
        $this->file = $_FILES;
    }

    public function main_list_get(){
		$data = $this->msocialization->getMainListAppForm($this->get('sort'), $this->get('start'),$this->get('limit'), $this->get('key'),$this->get('ProvinceID'),$this->get('DistrictID'),$this->get('SubDistrictID')); 
	   
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }

	public function main_list_participant_get(){
		$data = $this->msocialization->getMainListParticipantForm($this->get('sort'), $this->get('start'),$this->get('limit'), $this->get('IMSSocID'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }

	public function main_application_list_get(){

		$data = $this->msocialization->getMainListapplication($this->get('sort'),$this->get('start'),$this->get('limit'), $this->get('IMSID'), $this->get('IMSSocID'), $this->get('key'),$this->get('ProvinceID'),$this->get('DistrictID'),$this->get('SubDistrictID'),$this->get('FarmerGroupID'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }

    public function savedata_post() {
		$proses = $this->msocialization->insertApp($this->post());
		if($proses) {
			$Varid = (int) $proses;
			$id = $Varid == 0 ? 0 : $Varid;
			$results['data'] = $this->msocialization->getLastInsertDataSocializ($id);
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
        $data = $this->msocialization->loadappdata($this->get('IMSSocID'));
        $this->response($data, 200);
    }


	function getDataSocialization_get() {
        if(!$this->get('IMSSocID')) $this->response(NULL, 400);
        $data = $this->msocialization->getDataSocialization($this->get('IMSSocID'));
        $this->response($data, 200);
    }

	function appform_delete() {
        if(!$this->delete('IMSSocID')) $this->response(NULL, 400);
        $data = $this->msocialization->deleteAppform($this->delete('IMSSocID'));
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
        @$proses = $this->msocialization->save_participant($this->post()); 
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
        $proses = $this->msocialization->saveexistingfarmerbyweb($this->post()); 
        if($proses) {
			$results['success'] = true;
            $results['message'] = "record created.";
		 } else {
			$results['success'] = false;
            $results['message'] = "Failed to create record";
        }
		$this->response($results, 200);
	}


	function comboharievent_get()
	{
		$IMSSocID = (int) $this->get('IMSSocID');
		$data = $this->msocialization->comboharievent($IMSSocID);
        $this->response($data, 200);
	}

	function save_attandance_participant_post()
	{
		if(!$this->post('IMSSocID')) $this->response(NULL, 400);
        $proses = $this->msocialization->save_attandance_participant($this->post());
		 
		if($proses) {
			$results['success'] = true;
            $results['message'] = "record created.";
		} else {
			$results['success'] = false;
            $results['message'] = "Failed to create record";
        }
		$this->response($results, 200);
	}

	//simpan semua data peserta yg tidak ikut diabsen dari proses di >> save_attandance_participant_post()
	function save_participant_to_attadance_post()
	{
		if(!$this->post('IMSSocID')) $this->response(NULL, 400);
        $proses = $this->msocialization->save_participant_to_attadance($this->post());
        
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
		$data = $this->msocialization->main_attandance_list($this->get('sort'), $this->get('start'),$this->get('limit'), $this->get('IMSSocID'), $this->get('DayNumber'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }
	
	public function main_existingfarmer_list_get(){
		$data = $this->msocialization->getmain_existingfarmer_list($this->get('sort'), $this->get('start'),$this->get('limit'),$this->get('key'),$this->get('ProvinceID'),$this->get('DistrictID'),$this->get('SubDistrictID'),$this->get('IMSSocID'),$this->get('FarmerGroupID'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }
	
	
	//Cetak Kosong
	function cetakkosong_get($id) {
        $data['data'] = $this->msocialization->getDataSocializationCetakHeader($id);
		$data['peserta'] = $this->msocialization->getDataParticipantCetak($id);
		$data['staff'] = $this->msocialization->getDataStaffCetak($id);

        $data['logo'] = $this->msocialization->readPartnerLogo($id);
        $this->load->view('socialization_cetak_hadir', $data);
    }
	//cetak isi
	function cetakisi_get( $params ) {

        $p = explode('_', $params);
		$id = $p[0];$days = $p[1];
		$data['data'] = $this->msocialization->getDataSocializationCetakHeader($id);
		$data['eventDate'] = $this->msocialization->getDataSocializationAttRow($id,$days );
		$data['days'] = $days;
		$data['peserta'] = $this->msocialization->getDataAttandanceCetak($id, $days);

		$data['staff'] = $this->msocialization->getDataStaffAttandanceCetak($id, $days);
        $data['logo'] = $this->msocialization->readPartnerLogo($id);
        $this->load->view('socialization_cetak_hadir', $data);
    }

	function savedata_rekomendasi_post()
	{

		$proses = $this->msocialization->savedataRekomendasi($this->post());
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
    
	//cronjob running
	function generatefarmer_get()
	{
		 $this->msocialization->cronGenerateFarmerbySocialization(); 
	}

	function gen_farmer_from_socialization_get(){
		$ApplicantIDIn = $this->uri->segment(4);
		$proses = $this->msocialization->GenFarmerFromSoc($ApplicantIDIn);
		$this->response($proses, 200);
	}
	
	public function main_list_staff_get(){
		$data = $this->msocialization->Getmain_list_staff($this->get('start'),$this->get('limit'),$this->get('key'),$this->get('IMSSocID'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }

    public function main_list_soc_staff_get(){
		$data = $this->msocialization->Getmain_list_soc_staff($this->get('start'),$this->get('limit'),  $this->get('IMSSocID'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }

	public function main_staff_get(){
		$data = $this->msocialization->Getmain_staff($this->get('start'),$this->get('limit'), $this->get('key'), $this->get('IMSSocID'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }
	function save_staff_post()
	{
		if(!$this->post('IMSSocID')) $this->response(NULL, 400);
        $proses = $this->msocialization->save_staff($this->post());
        if($proses) {
			$results['success'] = true;
            $results['message'] = "record created.";
		 } else {
			$results['success'] = false;
            $results['message'] = "Failed to create record";
        }
		$this->response($results, 200);
	}

	function hapusstaff_delete() {
        if(!$this->delete('SocStaffID')) $this->response(NULL, 400);
        $data = $this->msocialization->Hapusstaff($this->delete('SocStaffID') );
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Data could not be delete'), 404);
    }

	//simpan semua data peserta yg tidak ikut diabsen dari proses di >> save_attandance_staff_post()
	function save_staff_to_attadance_post()
	{
		if(!$this->post('IMSSocID')) $this->response(NULL, 400);
        $proses = $this->msocialization->save_staff_to_attadance($this->post());
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
		$data = $this->msocialization->main_staffattandance_list($this->get('start'),$this->get('limit'), $this->get('IMSSocID'), $this->get('DayNumber'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }

	function save_attandance_staff_post()
	{
		if(!$this->post('IMSSocID')) $this->response(NULL, 400);
        $proses = $this->msocialization->save_attandance_staff($this->post());
        if($proses) {
			$results['success'] = true;
            $results['message'] = "record created.";
		 } else {
			$results['success'] = false;
            $results['message'] = "Failed to create record";
        }
		$this->response($results, 200);
	}
	
	function getcheckedfromparticipant_post()
	{
		if(!$this->post('IMSSocID')) $this->response(NULL, 400);
        $proses = $this->msocialization->getcheckedfromparticipant($this->post('IMSSocID'));
        if($proses) {
			$results['data'] = $proses;
		 } else {
            $results['data'] = ''; 
        }
		$this->response($results, 200);
	}

	//For Mobile API
	public function download_get()
    {
        $Districts = urldecode($this->get('District'));
		$rows = $this->msocialization->getSocializeEvent($Districts);
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
				if ( ! $this->upload->do_upload('file')) {
					$error = array('error' => $this->upload->display_errors());
					$this->response(array('success' => false, 'message' => $error['error']), 400);
				} else {
					$is_upload = true;
					$data = array('upload_data' => $this->upload->data());
					
					$zip = new ZipArchive;
					if ($zip->open($data['upload_data']['full_path']) === TRUE) {
						delete_file($data['upload_data']['full_path']);
						$zip->extractTo($data['upload_data']['full_path']);
						$zip->close();
						$files = array_slice(scandir($data['upload_data']['full_path']), 2);
						if (!empty($files)) {
							$json_found = false;
							$file_attachment = array();
							foreach ($files as $file) {
								if (strpos($file, '.json')) {
									
									$json_found = true;
									$data_post = json_decode(file_get_contents($data['upload_data']['full_path'].'/'.$file), true);
									// echo '<pre>'; print_r($data_post); echo '</pre>'; exit;
									$path = str_replace('.json','.zip', $file); // utk mendapatkan folder nya.
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
		
		$IMSSocID    = $data_post['IMSSocID']; 
		$FILESEVENT =  $data_post['other_file_attachment'];
		
		if ($this->msocialization->checkSocializationData($IMSSocID) !== false) {
			$participants   = $data_post['participants'];
			$this->db->trans_start(FALSE);
			if (!empty($participants)){
				foreach ($participants as $key => $participant) {
					$ApplicantID     		= $participant['ApplicantID']; 
					$RecommendationStatus 	= @$participant['RecommendationStatus'];
					$FieldAgentName    		= @$participant['FieldAgentName'];
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
					
					if ($this->msocialization->checkParticipant($IMSSocID, $ApplicantID) == 0 ) { 
						$this->msocialization->addParticipant($IMSSocID, $ApplicantID, $RecommendationStatus, $FieldAgentName, $RecommendationDate , $Comments,$LearningContractSign,$LearningContractStatus,$apply_certification, $apply_certificationStatus);
					} else {
						$this->msocialization->editParticipant($IMSSocID, $ApplicantID, $RecommendationStatus, $FieldAgentName, $RecommendationDate , $Comments,$LearningContractSign,$LearningContractStatus,$apply_certification, $apply_certificationStatus);
					}
					
					//daftar kan files event ke tabel ktv_ims_socialization_files
					if($FILESEVENT !=''){
						$NamesFile = explode(",",$FILESEVENT);
						$this->msocialization->addFielsEventFoto($IMSSocID, $NamesFile); 
					}
				}
			}
			  
			$attendances_list     = $data_post['attendances_list'];
			if (!empty($attendances_list)){
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
						$ApplicantID      = $attendancedata['ApplicantID'];
						$file_attachement = $attendancedata['file_attachement']; 
						$AttendanceStatus = $attendancedata['AttendanceStatus'];   
						if ($this->msocialization->checkAttendance($IMSSocID, $ApplicantID, $day_number) == 0)
						{
							$this->msocialization->addAttendance($IMSSocID, $ApplicantID, $day_number, $training_date, $AttendanceStatus, $file_attachement, $path );
						} else {
							$this->msocialization->editAttendance($IMSSocID, $ApplicantID, $day_number, $training_date, $AttendanceStatus, $file_attachement, $path );
						}
					}
					
				}
				 
				$return = array('success' => $this->db->trans_status());
			}
			 
		}else {
            $return = array('success' => false, 'message' => lang("IMSSocID {$IMSSocID} doesn't exist"));
        }
		 
		if ($is_upload == true) {
            $this->load->helper('file');
        }
		 
        $this->response($return, 200);

	}

	public function loadappdata_viewonly_get(){
    	if(!$this->get('IMSSocID')) $this->response(NULL, 400);
        $data = $this->msocialization->loadappdataViewonly($this->get('IMSSocID'));
        $this->response($data, 200);
    }

    public function loadappdata_recommendation_viewonly_get(){
    	$IMSSocID = (int) $this->get('IMSSocID');
    	$ApplicantID = (int) $this->get('ApplicantID');
    	$ParticipantID = (int) $this->get('ParticipantID');
    	$data = $this->msocialization->loadappdataRecommendationViewonly($IMSSocID,$ApplicantID,$ParticipantID);
        $this->response($data, 200);
    }
	
	 function appformparticipant_delete() {
        if(!$this->delete('ParticipantID')) $this->response(NULL, 400);
        $data = $this->msocialization->appformparticipantDelete($this->delete('ParticipantID') );
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Data could not be delete'), 404);
    } 
	
	function delSelectedparticipant_delete() { 
        $data = $this->msocialization->delSelectedparticipantDelete($this->delete('ApplicantID'), $this->delete('IMSSocID'), $this->delete('existingfarmer') );
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Data could not be delete'), 404);
    } 
	
	function checkbelumsyncalert_post(){
		$data = $this->msocialization->checkbelumsyncalert($this->post('IMSSocID'));  
		echo $data;die;
	}
	public function exportexcelsocialization_get(){
		$details = $this->msocialization->ExportExcelData();
        set_time_limit(0);
        ini_set('memory_limit','2500M'); 
        $this->load->library('Excel', null, 'PHPExcel');
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';
        $object = new PHPExcel();

        $object->getProperties()->setCreator("Koltiva Cocoatrace")
                       ->setLastModifiedBy("Koltiva Cocoatrace")
                       ->setCategory("Koltiva Cocoatrace");

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
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$title.'.xlsx');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
	
	public function exportexcelsocializationParticipant_get($IMSSocID){
		$row = $this->msocialization->ExportHeaderrow($IMSSocID );
		$details = $this->msocialization->ExportExcellogevent($IMSSocID ); 
		 
        set_time_limit(0);
        ini_set('memory_limit','2500M'); 
        $this->load->library('Excel', null, 'PHPExcel');
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';
        $object = new PHPExcel();

        $object->getProperties()->setCreator("Koltiva Cocoatrace")
                       ->setLastModifiedBy("Koltiva Cocoatrace")
                       ->setCategory("Koltiva Cocoatrace");

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
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$title.'.xlsx');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
	
	public function exportexcelsocializationStaff_get($IMSSocID){
		$row = $this->msocialization->ExportHeaderrow($IMSSocID );
		$details = $this->msocialization->ExportExcelstaff($IMSSocID ); 
		 
        set_time_limit(0);
        ini_set('memory_limit','2500M'); 
        $this->load->library('Excel', null, 'PHPExcel');
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';
        $object = new PHPExcel();

        $object->getProperties()->setCreator("Koltiva Cocoatrace")
                       ->setLastModifiedBy("Koltiva Cocoatrace")
                       ->setCategory("Koltiva Cocoatrace");
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
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$title.'.xlsx');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
}