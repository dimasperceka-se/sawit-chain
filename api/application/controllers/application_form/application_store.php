<?php
 
defined('BASEPATH') OR exit('No direct script access allowed');

//write excel
require_once 'application/third_party/Spout3/Autoloader/autoload.php';


use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
//use Box\Spout\Common\Entity\Style\CellAlignment;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Common\Entity\Style\Border;
use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;
 
class Application_store extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->file = $_FILES;
        $this->load->model('application_form/store');
    }

    public function main_list_get(){   
		$data = $this->store->getMainListAppForm($this->get('sort'), $this->get('start'),$this->get('limit'), $this->get('key'),$this->get('ProvinceID'),$this->get('DistrictID'),$this->get('SubDistrictID'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }

    public function applicant_member_input_grid_get(){        
        $textSearch = $this->get('textSearch');
        $Enumerator = $this->get('Enumerator');

        //sort
        $sorting = json_decode($this->get('sort'));
        $sortingField = $sorting[0]->property;
        $sortingDir = $sorting[0]->direction;

        $result = $this->store->getApplicantMemberInputGrid($textSearch,$this->get('start'), $this->get('limit'), $sortingField, $sortingDir,$Enumerator);
        $this->response($result, 200);
    }

    public function applicant_member_input_post(){        
        $arrApplicantID = json_decode($this->post('ApplicantID'));

        $result = $this->store->inputApplicantMember($arrApplicantID);
        $this->response($result, 200);
    }

    public function savedata_post() {
        $this->load->model('mmiddleware');
        $proses = $this->store->insertApp($this->post());
        if($proses) {
			// $arrPrimaryKey = array('ApplicantID'=>$proses);
			// $uid = "eBCX1KfaDmA";
			// $onlyNew = true;
			// $programs = $this->mmiddleware->getAllProgramWithView($uid);
			// if ($programs != '' AND count($programs) > 0) {
			// 	foreach ($programs as $progkeys => $program) {
			// 		$datas = $this->mmiddleware->getDataBy($onlyNew, $program['uid'], $arrPrimaryKey);
			// 		$this->mmiddleware->syncDataPerProgram($datas, $program['uid']);
			// 	}
			// }


			$results['success'] = true;
            $results['message'] = "record created."; 
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        $this->response($results, 200);
    }
	
	function loadappdata_get() {
        if(!$this->get('ApplicantID')) $this->response(NULL, 400);
        $data = $this->store->loadappdata($this->get('ApplicantID'));
        $this->response($data, 200);
    }
    
    function appform_delete() {
        if(!$this->delete('ApplicantID')) $this->response(NULL, 400);
        $data = $this->store->deleteAppform($this->delete('ApplicantID'));
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
	
	public function main_list_participant_get(){    
		$data = $this->store->getMainListParticipantForm($this->get('start'),$this->get('limit'), $this->get('ApplicantID'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }
	
	//donwload all data registrasi
	public function print_afl_get(){
		$dataList = $this->store->ExportExcelData();
        set_time_limit(0);
        ini_set('memory_limit','2500M'); 
        // echo "<pre>".print_r($details,1);exit;
        if(count($dataList)){

            //Kolom Header Farmer
            $dataHeader = array('No');
            foreach($dataList[0] as $key => $value){
                array_push($dataHeader,lang($key));
            }
            //Kolom Header Farmer

            //Kolom Body Farmer
            $dataListExcel = array();
            $no = 1;
            foreach ($dataList as $key => $value) {
                $data = array();
                array_push($data,$no);
                foreach($value as $keyx => $valuex){
                    array_push($data,$valuex);
                }
                $dataListExcel[$key] = $data;
                $no++;
            }
            //Kolom Body Farmer

            $writer = WriterEntityFactory::createXLSXWriter(); // for XLSX files// 
            $namaFile = date('YmdHis') . '_export_excel_applicant.xlsx';
            $filePath = 'files/tmp/' . $namaFile;
            $writer->openToFile($filePath);

            $defaultStyle = (new StyleBuilder())
                ->setFontName('Arial')
                ->setFontSize(11)
                ->setShouldWrapText(false)
                ->build();
            $writer->setDefaultRowStyle($defaultStyle)
                ->openToFile($filePath);

            $borderDefa = (new BorderBuilder())
                ->setBorderBottom(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
                ->setBorderTop(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
                ->setBorderRight(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
                ->setBorderLeft(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
                ->build();

            //style
            $styleHeader = (new StyleBuilder())
                ->setFontColor(Color::WHITE)
                ->setBorder($borderDefa)
                ->setBackgroundColor(Color::GREEN)
                ->build();

            //row header
            $rowHeader = WriterEntityFactory::createRowFromArray($dataHeader, $styleHeader);
            $writer->addRow($rowHeader);

            $styleData = (new StyleBuilder())
                ->setBorder($borderDefa)
                ->build();

            $styleFormatAngka = (new StyleBuilder())
                ->setBorder($borderDefa)
                ->setFormat('0')
                ->build();

            $styleFormatTanggal = (new StyleBuilder())
                ->setBorder($borderDefa)
                ->setFormat('YYYY-mm-dd')
                ->build();

            for ($i=0; $i < count($dataListExcel); $i++) {
                $dataRows = $dataListExcel[$i];
                $cells = array();
    
                for ($j=0; $j < count($dataRows); $j++) {
                    $styleRow = null;
                    $dataRow = null;
    
                    //cek apakah numeric
                    if(is_numeric($dataRows[$j])){
                        $styleRow = $styleFormatAngka;
                        $dataRow = $dataRows[$j];
                    } else {
                        //cek apakah tanggal
                        if($this->validateDate($dataRows[$j]) == true) {
                            $styleRow = $styleFormatTanggal;
                            $dataRow = $dataRows[$j];
                        } else {
                            $styleRow = $styleData;
                            $dataRow = $dataRows[$j];
                        }
                    }
    
                    $cells[$j] = WriterEntityFactory::createCell($dataRow, $styleRow);
                }
                /*$cells = [
                    WriterEntityFactory::createCell($dataRows[0], $styleData),
                    WriterEntityFactory::createCell((float) $dataRows[1], $styleFormatAngka),
                    WriterEntityFactory::createCell($dataRows[2], $styleData),
                    WriterEntityFactory::createCell(25569 + (time() / 86400), $styleFormatTanggal),
                    WriterEntityFactory::createCell($dataRows[4], $styleFormatTanggal)
                ];*/
    
                $rowData = WriterEntityFactory::createRow($cells);
                $writer->addRow($rowData);
            }
    
            $writer->close();
    
            $this->response(array('success' => TRUE, 'filenya' => base_url() . $filePath), 200);
            exit;
        }else{
            $this->response(array('success' => FALSE, 'filenya' => ''));
            exit;
        }
    }

    private function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
	
	public function print_tidaklolos_get($params){
		$exp = explode('_', $params); 
		$dataList = $this->store->ExportExcelDataTdkLolos($exp[0], $exp[1], $exp[2] );
		  
        set_time_limit(0);
        ini_set('memory_limit','2500M'); 
        // echo "<pre>".print_r($dataList,1);exit;
        if(count($dataList)){

            //Kolom Header Farmer
            $dataHeader = array('No');
            foreach($dataList[0] as $key => $value){
                array_push($dataHeader,lang($key));
            }
            //Kolom Header Farmer

            //Kolom Body Farmer
            $dataListExcel = array();
            $no = 1;
            foreach ($dataList as $key => $value) {
                $data = array();
                array_push($data,$no);
                foreach($value as $keyx => $valuex){
                    array_push($data,$valuex);
                }
                $dataListExcel[$key] = $data;
                $no++;
            }
            //Kolom Body Farmer

            $writer = WriterEntityFactory::createXLSXWriter(); // for XLSX files// 
            $namaFile = date('YmdHis') . '_export_excel_applicant_not_recomended.xlsx';
            $filePath = 'files/tmp/' . $namaFile;
            $writer->openToFile($filePath);

            $defaultStyle = (new StyleBuilder())
                ->setFontName('Arial')
                ->setFontSize(11)
                ->setShouldWrapText(false)
                ->build();
            $writer->setDefaultRowStyle($defaultStyle)
                ->openToFile($filePath);

            $borderDefa = (new BorderBuilder())
                ->setBorderBottom(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
                ->setBorderTop(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
                ->setBorderRight(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
                ->setBorderLeft(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
                ->build();

            //style
            $styleHeader = (new StyleBuilder())
                ->setFontColor(Color::WHITE)
                ->setBorder($borderDefa)
                ->setBackgroundColor(Color::GREEN)
                ->build();

            //row header
            $rowHeader = WriterEntityFactory::createRowFromArray($dataHeader, $styleHeader);
            $writer->addRow($rowHeader);

            $styleData = (new StyleBuilder())
                ->setBorder($borderDefa)
                ->build();

            $styleFormatAngka = (new StyleBuilder())
                ->setBorder($borderDefa)
                ->setFormat('0')
                ->build();

            $styleFormatTanggal = (new StyleBuilder())
                ->setBorder($borderDefa)
                ->setFormat('YYYY-mm-dd')
                ->build();

            for ($i=0; $i < count($dataListExcel); $i++) {
                $dataRows = $dataListExcel[$i];
                $cells = array();
    
                for ($j=0; $j < count($dataRows); $j++) {
                    $styleRow = null;
                    $dataRow = null;
    
                    //cek apakah numeric
                    if(is_numeric($dataRows[$j])){
                        $styleRow = $styleFormatAngka;
                        $dataRow = $dataRows[$j];
                    } else {
                        //cek apakah tanggal
                        if($this->validateDate($dataRows[$j]) == true) {
                            $styleRow = $styleFormatTanggal;
                            $dataRow = $dataRows[$j];
                        } else {
                            $styleRow = $styleData;
                            $dataRow = $dataRows[$j];
                        }
                    }
    
                    $cells[$j] = WriterEntityFactory::createCell($dataRow, $styleRow);
                }
                /*$cells = [
                    WriterEntityFactory::createCell($dataRows[0], $styleData),
                    WriterEntityFactory::createCell((float) $dataRows[1], $styleFormatAngka),
                    WriterEntityFactory::createCell($dataRows[2], $styleData),
                    WriterEntityFactory::createCell(25569 + (time() / 86400), $styleFormatTanggal),
                    WriterEntityFactory::createCell($dataRows[4], $styleFormatTanggal)
                ];*/
    
                $rowData = WriterEntityFactory::createRow($cells);
                $writer->addRow($rowData);
            }
    
            $writer->close();
    
            $this->response(array('success' => TRUE, 'filenya' => base_url() . $filePath), 200);
            exit;
        }else{
            $this->response(array('success' => FALSE, 'filenya' => ''));
            exit;
        }
    }
	
	public function print_logevent_get($ApplicantID){ 
		$row = $this->store->ExportHeaderrow($ApplicantID );
		$details = $this->store->ExportExcellogevent($ApplicantID ); 
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
         
        $title = "Log Event  - ".$row->CertEventName.' DateCollection : '.$row->DateCollection;
       
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
		$object->setActiveSheetIndex(0)->setCellValue('A4', 'Applicant ID');
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
	
	//import/upload xlsx
	public function importdataapplicantxls_post()
    {
		$dir = FCPATH . 'files/upload/applicant';
		if(!is_dir($dir)) {
			mkdir($dir, 0777, true);
		}
       
        $config['upload_path']      = './files/upload/applicant';
        $config['allowed_types']    = 'xlsx|xls'; 

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload('File'))
        { 
			$data = array('error' => $this->upload->display_errors());
            $this->response(array('success' => false, 'msg' => $this->upload->display_errors()), 200);
        } else {
			$this->db->trans_begin();
            $data = $this->upload->data();   
			$inputfilename = $config['upload_path'].'/'.$data['file_name'];
			
			$this->load->library('Excel', null, 'PHPExcel');
			require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
			require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';
			$object = new PHPExcel();
			$inputfiletype = PHPExcel_IOFactory::identify($inputfilename);
			$objReader = PHPExcel_IOFactory::createReader($inputfiletype);
			$objPHPExcel = $objReader->load($inputfilename);
	
 			
			$sheet = $objPHPExcel->getSheet(0); 
			$highestRow = $sheet->getHighestDataRow(); 
			$highestColumn = $sheet->getHighestColumn();
			
			for ($row = 2; $row <= $highestRow; $row++)
			{ 
				//  Read a row of data into an array
				$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);

                // echo "<pre>";
                // print_r($rowData);
                // die;
				 
				if($rowData[0][1] != ''){ // cek row farmerName tidak kosong				
                    //check data applicant dari applicant ID yg tidak kosong
                    $this->db->where('ApplicantID', $rowData[0][0]);
                    $c = $this->db->select('ApplicantID')->from('ktv_applicant_farmers')->get()->num_rows();
                    if($c==0){
                        //  Insert row data array into your database of choice here
                        $ProvinceID = substr($rowData[0][4],0,2);
                        $DistrictID = substr($rowData[0][4],0,4);
                        $SubDistrictID = substr($rowData[0][4],0,7);
                        $dateBirth = $rowData[0][8] == '' ? '' :  date("Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($rowData[0][8])); 
                        $DateUpdate = $rowData[0][12] == '' ? '' :  date("Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($rowData[0][11])); 
                        $DateCollection = $rowData[0][12] == '' ? '' :  date("Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($rowData[0][12])); 
                        $village = $this->db->select('Village')->from('ktv_village')->where('VillageID', $rowData[0][4])->get()->row();
                        
                        //Generate Display ID
                        $sql = "SELECT max(ApplicantID) as maxKode FROM ktv_applicant_farmers ";
                        $k =  $this->db->query($sql)->row_array();  
                        $kode = $k['maxKode'];  
                        $noUrut = (int) $kode; 
                        $noUrut++;
                        $newID =  sprintf("%06s", $noUrut); 
                        //===================================================
                        array("DisplayID" => $newID);
                        $data = array(
                            "DisplayID" => $newID,
                            "Fullname" => $rowData[0][1],
                            "CPGid" => str_replace("'","",$rowData[0][2]),
                            "NewGroupName" => $rowData[0][3],
                            "VillageID" => $rowData[0][4],
                            "VillageName" => $village->Village,
                            "PhoneNumber" => $rowData[0][5],
                            "Gender" => $rowData[0][6],
                            "MaritalStatus" => $rowData[0][7],
                            "DateOfBirth" => $dateBirth,
                            "Age" => $this->umur($dateBirth),
                            "Education" => $rowData[0][9],
                            "DateUpdated" => $DateUpdate,
                            "DateCreated" => date("Y-m-d H:i:s"),
                            "CreatedBy" => $_SESSION['userid'],
                            "DateCollection" => $DateCollection,
                            "CertProgID" => $rowData[0][10],
                            "CertHolderID" => $rowData[0][11],
                            "IMSID " => $rowData[0][13],
                            "IMSMasterID" => $rowData[0][14],
                            "ProvinceID" => $ProvinceID,
                            "DistrictID" => $DistrictID,
                            "SubDistrictID " => $SubDistrictID,
                            "StatusCode" => 'active',
                            "FarmertypeID" => $rowData[0][15],
                            "PartnerID" => $rowData[0][16],
                            "NIN" => $rowData[0][17]
                        );

                        $this->db->insert("ktv_applicant_farmers",$data);
                    }
                }
			}
			if ($this->db->trans_status() === false) {
				$this->db->trans_rollback();
				$results['success'] = false;
				$results['message'] = "Failed to upload data";
			} else {
				$this->db->trans_commit();
				$results['success'] = true;
				$results['message'] = "Data Uploaded";
			}
			// echo "<pre>".print_r($rowData,1);exit;
			delete_file($inputfilename);
			$this->response($results, 200);
           
        }
    }
	 
	function umur($tanggal_lahir) { 
		list($year,$month,$day) = explode("-",$tanggal_lahir);
		$year_diff  = date("Y") - $year;
		$month_diff = date("m") - $month;
		$day_diff   = date("d") - $day;
		if($tanggal_lahir !=''){
		if ($month_diff < 0) $year_diff--;
			elseif (($month_diff==0) && ($day_diff < 0)) $year_diff--;
			return $year_diff;
		}
		else{
			return 0;
		}
	}
	
    function applicant_photo_post(){
        $this->load->library('awsfileupload');

        if ($this->post('opsiDisplay') == "insert") {
            //ketika insert

            if ($this->file['Koltiva_view_application_form_WinRegisterAppForm-Form-ApplicantPhotoInput']['name'] != '') {
                $gambar = $this->file['Koltiva_view_application_form_WinRegisterAppForm-Form-ApplicantPhotoInput']['name'];
                $fileupload['Koltiva_view_application_form_WinRegisterAppForm-Form-ApplicantPhotoInput'] = $this->file['Koltiva_view_application_form_WinRegisterAppForm-Form-ApplicantPhotoInput'];

                $upload = move_upload($fileupload, 'images/upload/' . $gambar);

                if (isset($upload['upload_data'])) {
                    $result['success'] = true;
                    $result['file'] = base_url().'/images/upload/' . $gambar;
                    $result['filepath']   = '/images/upload/' . $gambar;
                    $this->response($result, 200);
                } else {
                    echo 'false';
                    exit;
                }
            }
        }

        if ($this->post('opsiDisplay') == "update") {
            //ketika update

            $upload = $this->awsfileupload->upload($this->file['Koltiva_view_application_form_WinRegisterAppForm-Form-ApplicantPhotoInput']['tmp_name'],$this->file['Koltiva_view_application_form_WinRegisterAppForm-Form-ApplicantPhotoInput']['name'], AWSS3_APPLICANT_PHOTO_PATH, 'images');
            if ($upload['success'] == true) {
                if($this->awsfileupload->doesObjectExist($this->post('Koltiva_view_application_form_WinRegisterAppForm-Form-ApplicantPhotoOld')) == true) {
                    $this->awsfileupload->delete($this->post('Koltiva_view_application_form_WinRegisterAppForm-Form-ApplicantPhotoOld'));
                }else{
                    delete_file($this->post('Koltiva_view_application_form_WinRegisterAppForm-Form-ApplicantPhotoOld'));
                }
                $prosesUpdate = $this->store->updatefilephotoapplicant($_POST["ApplicantID"],$upload['filenamepath']);
                $result['success'] = true;
                $result['message'] = lang('File uploaded');
                $result['file'] = $upload['fileurl'];
                $result['filepath']   = $upload['filenamepath'];
                $this->response($result, 200);
            } else {
                $result['success'] = false;
                $result['message'] = lang('Upload to aws failed');
                $this->response($result, 400);
            }
        }
    }
	
    function photo_applicant_post(){
        $this->load->library('awsfileupload');

        if ($this->post('opsiDisplay') == "insert") {
            //ketika insert

            if ($this->file['Koltiva_view_application_form_WinRegisterAppForm-Form-PhotoInput']['name'] != '') {
                $gambar = $this->file['Koltiva_view_application_form_WinRegisterAppForm-Form-PhotoInput']['name'];
                $fileupload['Koltiva_view_application_form_WinRegisterAppForm-Form-PhotoInput'] = $this->file['Koltiva_view_application_form_WinRegisterAppForm-Form-PhotoInput'];

                $upload = move_upload($fileupload, 'images/upload/' . $gambar);

                if (isset($upload['upload_data'])) {
                    $result['success'] = true;
                    $result['file'] = base_url().'/images/upload/' . $gambar;
                    $result['filepath']   = '/images/upload/' . $gambar;
                    $this->response($result, 200);
                } else {
                    echo 'false';
                    exit;
                }
            }
        }

        if ($this->post('opsiDisplay') == "update") {
            //ketika update

            $upload = $this->awsfileupload->upload($this->file['Koltiva_view_application_form_WinRegisterAppForm-Form-PhotoInput']['tmp_name'],$this->file['Koltiva_view_application_form_WinRegisterAppForm-Form-PhotoInput']['name'], AWSS3_APPLICANT_SIGNATURE_PATH, 'images');
            if ($upload['success'] == true) {
                if($this->awsfileupload->doesObjectExist($this->post('Koltiva_view_application_form_WinRegisterAppForm-Form-PhotoOld')) == true) {
                    $this->awsfileupload->delete($this->post('Koltiva_view_application_form_WinRegisterAppForm-Form-PhotoOld'));
                }else{
                    delete_file($this->post('Koltiva_view_application_form_WinRegisterAppForm-Form-PhotoOld'));
                }
                $prosesUpdate = $this->store->updatefilephoto($_POST["ApplicantID"],$upload['filenamepath']);
                $result['success'] = true;
                $result['message'] = lang('File uploaded');
                $result['file'] = $upload['fileurl'];
                $result['filepath']   = $upload['filenamepath'];
                $this->response($result, 200);
            } else {
                $result['success'] = false;
                $result['message'] = lang('Upload to aws failed');
                $this->response($result, 400);
            }
        }
    }
	
    function contract_applicant_post(){
        $this->load->library('awsfileupload');

        if ($this->post('opsiDisplay') == "insert") {
            //ketika insert

            if ($this->file['Koltiva_view_application_form_WinRegisterAppForm-Form-CertificationContractSignInput']['name'] != '') {
                $gambar = $this->file['Koltiva_view_application_form_WinRegisterAppForm-Form-CertificationContractSignInput']['name'];
                $fileupload['Koltiva_view_application_form_WinRegisterAppForm-Form-CertificationContractSignInput'] = $this->file['Koltiva_view_application_form_WinRegisterAppForm-Form-CertificationContractSignInput'];

                $upload = move_upload($fileupload, 'images/upload/' . $gambar);
                if (isset($upload['upload_data'])) {
                    $result['success'] = true;
                    $result['file'] = base_url().'/images/upload/' . $gambar;
                    $result['filepath']   = '/images/upload/' . $gambar;
                    $this->response($result, 200);
                } else {
                    echo 'false';
                    exit;
                }
            }
        }

        if ($this->post('opsiDisplay') == "update") {
            //ketika update

            $upload = $this->awsfileupload->upload($this->file['Koltiva_view_application_form_WinRegisterAppForm-Form-CertificationContractSignInput']['tmp_name'],$this->file['Koltiva_view_application_form_WinRegisterAppForm-Form-CertificationContractSignInput']['name'], AWSS3_APPLICANT_CONTRACT_PATH, 'images');
            if ($upload['success'] == true) {
                if($this->awsfileupload->doesObjectExist($this->post('Koltiva_view_application_form_WinRegisterAppForm-Form-CertificationContractSignOld')) == true) {
                    $this->awsfileupload->delete($this->post('Koltiva_view_application_form_WinRegisterAppForm-Form-CertificationContractSignOld'));
                }else{
                    delete_file($this->post('Koltiva_view_application_form_WinRegisterAppForm-Form-CertificationContractSignOld'));
                }
                $prosesUpdate = $this->store->updatefilecontractphoto($_POST["ApplicantID"],$upload['filenamepath']);
                $result['success'] = true;
                $result['message'] = lang('File uploaded');
                $result['file'] = $upload['fileurl'];
                $result['filepath']   = $upload['filenamepath'];
                $this->response($result, 200);
            } else {
                $result['success'] = false;
                $result['message'] = lang('Upload to aws failed');
                $this->response($result, 400);
            }
        }
    }
}
?>
