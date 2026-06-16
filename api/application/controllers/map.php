<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


require_once 'application/third_party/Spout3/Autoloader/autoload.php';

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Common\Entity\Style\Border;
use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;


class Map extends REST_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('map/mmap');
	}

	public function restricted_area_get()
	{
		$data = $this->mmap->getRestrictedArea($this->get('ProvinceID'), $this->get('DistrictID'));
		$this->response($data, 200);
	}

	public function safe_area_get()
	{
		$data = $this->mmap->getSafeArea($this->get('ProvinceID'), $this->get('DistrictID'));
		$this->response($data, 200);
	}

	public function buffer_zone_get()
	{
		$data = $this->mmap->getBufferZone($this->get('ProvinceID'), $this->get('DistrictID'));
		$this->response($data, 200);
	}

	public function administrative_area_get()
	{
		$data = $this->mmap->getAdministrativeArea($this->get('ProvinceID'), $this->get('DistrictID'));
		$this->response($data, 200);
	}

	public function land_cover_get()
	{
		$data = $this->mmap->getLandCover($this->get('ProvinceID'), $this->get('DistrictID'));
		$this->response($data, 200);
	}

	public function covid_risk_get()
	{
		$data = $this->mmap->getCovidRisk($this->get('ProvinceID'), $this->get('DistrictID'));
		$this->response($data, 200);
	}

	public function animal_habitat_get()
	{
		$data = $this->mmap->getAnimalHabitat($this->get('ProvinceID'), $this->get('DistrictID'));
		$this->response($data, 200);
	}

	public function farmer_get()
	{
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);
		$PartnerID = (int) $this->get('PartnerID');

		if($PartnerID == 145) { //Hardcode bluenumber
			$data = $this->mmap->getFarmersBlueNumber($this->get('ProvinceID'), $this->get('DistrictID'), $this->get('key'), $this->get('Age'));
			$this->response($data, 200);
		} else {
			$data = $this->mmap->getFarmers($this->get('ProvinceID'), $this->get('DistrictID'), $this->get('key'), $this->get('Age'), $this->get('PartnerID'));
			$this->response($data, 200);
		}
	}

	public function farmer_polygon_get(){
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);
		$DataReturn = array();

		$data = $this->mmap->getFarmersGroup($this->get('ProvinceID'), $this->get('DistrictID'), $this->get('key'), $this->get('Age'), $this->get('PartnerID'));
		// var_dump($data);die();
		//Ambil data polygon
		if (!empty($data)) {
			foreach ($data as $key => $value) {
				//Ambil informasi garden terlebih dahulu (Begin)
				$InfoGarden = $this->mmap->GetInfoGardenPolygon($value['ID']);
				if (!empty($InfoGarden)) {
					foreach ($InfoGarden as $k => $v) {
						$InfoGarden[$k]['Name'] = $value['Name'];
						$InfoGarden[$k]['MemberDisplayID'] = $value['MemberDisplayID'];
					}
				}
				
				$DataReturn = array_merge($DataReturn,$InfoGarden);
				//Ambil informasi garden terlebih dahulu (End)
			}
		}

		if (!empty($DataReturn)) {
			for ($i=0; $i < count($DataReturn); $i++) { 
				$polygon = $this->mmap->getFarmerPolygonNew($DataReturn[$i]['MemberID'], $DataReturn[$i]['PlotNr'], $DataReturn[$i]['SurveyNr'], $DataReturn[$i]['Revision']);
				if (isset($polygon[0][0])) {
					$DataReturn[$i]['polygon'] = $polygon;
				}
			}
		}

		$this->response($DataReturn, 200);
	}

	public function farmer_certified_get()
	{
		$data = $this->mmap->getFarmersCertified($this->get('ProvinceID'), $this->get('DistrictID'), $this->get('key'));
		$this->response($data, 200);		
	}

	public function farmer_sme_get(){
		$data = $this->mmap->getFarmersSME($this->get('MemberID'));
		$this->response($data, 200);
	}

	public function sme_sta_500_get(){
		$data = $this->mmap->getSMESTA("500",$this->get('ProvinceID'), $this->get('DistrictID'), $this->get('key'), $this->get('PartnerID'));
		$this->response($data, 200);
	}

	public function sme_sta_200_get(){
		$data = $this->mmap->getSMESTA("200",$this->get('ProvinceID'), $this->get('DistrictID'), $this->get('key'), $this->get('PartnerID'));
		$this->response($data, 200);
	}

	public function sme_sta_100_get(){
		$data = $this->mmap->getSMESTA("100",$this->get('ProvinceID'), $this->get('DistrictID'), $this->get('key'), $this->get('PartnerID'));
		$this->response($data, 200);
	}

	public function sme_get()
	{
		$data = $this->mmap->getSME($this->get('ProvinceID'), $this->get('DistrictID'), $this->get('key'), $this->get('PartnerID'));
		$this->response($data, 200);		
	}

	public function sme_plantation_get()
	{
		$data = $this->mmap->getSMEPlantation($this->get('ProvinceID'), $this->get('DistrictID'), $this->get('key'), $this->get('PartnerID'));
		// if ($data !== false) {
			// foreach ($data as $key => $val) {
			// 	$polygon = $this->mmap->getSMEPlantationPolygon($val['MemberID'], $val['GardenNr'], $val['SurveyNr'], $val['Revision']);
			// 	if (!empty($polygon)) {
			// 		$data[$key]['polygon'] = $polygon;
			// 	}
			// }
		// }
		$this->response($data, 200);		
	}

	public function sme_polygon_get(){
		$data = $this->mmap->getSMEPolygon($this->get('ProvinceID'), $this->get('DistrictID'), $this->get('key'), $this->get('PartnerID'));
		if ($data !== false) {
			foreach ($data as $key => $val) {
				$polygon = $this->mmap->getSMEPlantationPolygon($val['MemberID'], $val['GardenNr'], $val['SurveyNr'], $val['Revision']);
				if (!empty($polygon)) {
					$data[$key]['polygon'] = $polygon;
				}
			}
		}
		$this->response($data, 200);
	}

	public function processing_get()
	{
		$data = $this->mmap->getProcessing($this->get('ProvinceID'), $this->get('DistrictID'), $this->get('key'), $this->get('PartnerID'));
		$this->response($data, 200);		
	}

	public function mill_plantation_get()
	{
		$data = $this->mmap->getMillPlantation($this->get('ProvinceID'), $this->get('DistrictID'), $this->get('key'), $this->get('PartnerID'));
		if ($data !== false) {
			foreach ($data as $key => $val) {
				$polygon = $this->mmap->getMillPlantationPolygon($val['ID'], $val['GardenNr'], $val['SurveyNr'], $val['Revision']);
				if (!empty($polygon)) {
					$data[$key]['polygon'] = $polygon;
				}
			}
		}
		$this->response($data, 200);		
	}

	public function bank_get()
	{
		$data = $this->mmap->getBank($this->get('ProvinceID'), $this->get('DistrictID'), $this->get('key'));
		$this->response($data, 200);		
	}

	public function update_polygon_revision_get()
	{
		$status = $this->mmap->updatePolygonRevision();
		$this->response(['success' => $status, 'rows' => $this->db->affected_rows()], 200);
	}

	public function province_get()
	{
		$data = $this->mmap->getProvince();
		$this->response($data, 200);
	}

	public function district_get()
	{
		$data = $this->mmap->getDistrict($this->get('ProvinceID'));
		$this->response($data, 200);
	}

	public function province_full_get()
	{
		$data = $this->mmap->getProvinceFull();
		$this->response($data, 200);
	}

	public function district_full_get()
	{
		// $data = $this->mmap->getDistrictFull($this->get('ProvinceID'));
		$data = $this->mmap->getDistrictFull($this->get('PartnerIDs'));
		$this->response($data, 200);
	}

	public function fire_hotspot_get()
	{
		$data = $this->mmap->getFireHotspot($this->get('timeline'),$this->get('date'),$this->get('confidence'),$this->get('satellite'));
		$this->response($data, 200);
	}
	
	// NEW UI ----------------------------------------------

	public function farm_location_get(){

		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);
		$PartnerID = (int) $this->get('PartnerID');

		$data=[];
		$data["FarmLocation"]=[];

		// Farm Location
		if($PartnerID == 145) { //Hardcode bluenumber
			$data["FarmLocation"] = $this->mmap->getFarmersBlueNumber($this->get('ProvinceID'), $this->get('DistrictID'), $this->get('key'), $this->get('Age'));
		} else {
			$data["FarmLocation"] = $this->mmap->GetFarmLocation($this->get('ProvinceID'), $this->get('DistrictID'), $this->get('key'), $this->get('Age'), $this->get('PartnerID'));
		}

		$this->response($data, 200);
	}

	public function farm_area_get(){
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);
		$DataReturn = array();

		$data = $this->mmap->getFarmersGroup($this->get('ProvinceID'), $this->get('DistrictID'), $this->get('key'), $this->get('Age'), $this->get('PartnerID'));
		//Ambil data polygon
		if (!empty($data)) {
			foreach ($data as $key => $value) {
				//Ambil informasi garden terlebih dahulu (Begin)
				$InfoGarden = $this->mmap->GetInfoGardenPolygonNEWUI($value['ID']);
				if (!empty($InfoGarden)) {
					foreach ($InfoGarden as $k => $v) {
						$InfoGarden[$k]['Name'] = $value['Name'];
						$InfoGarden[$k]['MemberDisplayID'] = $value['MemberDisplayID'];
					}
				}
				
				$DataReturn = array_merge($DataReturn,$InfoGarden);
				//Ambil informasi garden terlebih dahulu (End)
			}
		}
		if (!empty($DataReturn)) {
			for ($i=0; $i < count($DataReturn); $i++) { 
				$polygon = $this->mmap->getFarmerPolygonNew($DataReturn[$i]['MemberID'], $DataReturn[$i]['PlotNr'], $DataReturn[$i]['SurveyNr'], $DataReturn[$i]['Revision']);
				if (isset($polygon[0][0])) {
					$DataReturn[$i]['polygon'] = $polygon;
				}
			}
		}


		$this->response($DataReturn, 200);
	}


	public function kml_layer_list_get() {
        $params = [
            "ProvinceID"    => (int) $this->get('ProvinceID'),
            "DistrictID"    => (int) $this->get('DistrictID'),
        ];

        $data = $this->mmap->GetKmlLayerList($params);

		$this->response($data, 200);
    }

	public function show_kml_get() {
        $params = [
            "ProvinceID"    => (int) $this->get('ProvinceID'),
            "DistrictID"    => (int) $this->get('DistrictID'),
            "Name"          => $this->get('Name'),
        ];

        $data = $this->mmap->GetShowKml($params);

		$this->response($data, 200);
    }

	public function actors_export_excel_get() {
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);
		$PartnerID = (int) $this->get('PartnerID');

        //generate nama file excel
        $sqlViewName = "Actors_Data_PalmOilTrace_";

        //Strip character spesial
        $sqlViewName = preg_replace('/[^A-Za-z0-9\-\_]/', '', $sqlViewName);
        $filePath = 'files/tmp/'.$sqlViewName.date('Y_m_d').'.xlsx';

        $writer = WriterEntityFactory::createXLSXWriter();        

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
		
		$styleData = (new StyleBuilder())
			->setBorder($borderDefa)
			->build();

		$styleFormatAngka = (new StyleBuilder())
			->setBorder($borderDefa)
			->setFormat('0')
			->build();

		$styleFormatTanggal = (new StyleBuilder())
			->setBorder($borderDefa)
			->setFormat('d-mmm-YY')
			->build();

		// Farm Location
			$fl_sheet = $writer->getCurrentSheet();	
			$fl_sheet->setName('Farm Location');

			// Get Data Farm Location 
				if($PartnerID == 145) { //Hardcode bluenumber
					$data = $this->mmap->getFarmersBlueNumber($this->get('ProvinceID'), $this->get('DistrictID'), $this->get('key'), $this->get('Age'));
				} else {
					$data = $this->mmap->GetFarmLocation($this->get('ProvinceID'), $this->get('DistrictID'), $this->get('key'), $this->get('Age'), $this->get('PartnerID'));
				}
			
			//generate data header
				$dataHeader = array(
					'No.', 'ID', 'Name', 'FarmNr', 'SurverNr', 'Village','Sub District','District','Province', 'FarmAge', 'LandArea (Ha)', 'Production (Ton)'
				);

			//row header
				$rowHeader = WriterEntityFactory::createRowFromArray($dataHeader, $styleHeader);
				$writer->addRow($rowHeader);

			// Write row data
				$no = 1;
				
				foreach($data["Data"] as $k=>$v){
					$cells = array();

					$cells = [
								WriterEntityFactory::createCell( (float) $no, $styleFormatAngka),
								WriterEntityFactory::createCell( $v['MemberDisplayID'], $styleData),
								WriterEntityFactory::createCell( $v['Name'], $styleData),
								WriterEntityFactory::createCell( (int) $v['GardenNr'], $styleFormatAngka),
								WriterEntityFactory::createCell( (int) $v['SurveyNr'], $styleFormatAngka),
								WriterEntityFactory::createCell( $v['Village'], $styleData),
								WriterEntityFactory::createCell( $v['SubDistrict'], $styleData),
								WriterEntityFactory::createCell( $v['District'], $styleData),
								WriterEntityFactory::createCell( $v['Province'], $styleData),
								WriterEntityFactory::createCell( (float) $v['FarmAge'], $styleFormatAngka),
								WriterEntityFactory::createCell( (float) $v['AreaHa'], $styleFormatAngka),
								WriterEntityFactory::createCell( (float) $v['Production'], $styleFormatAngka),

							];

					$rowData = WriterEntityFactory::createRow($cells);
					$writer->addRow($rowData);
					$no++;
				}


		

		// Farm Area (fa)
			$fa_sheet = $writer->addNewSheetAndMakeItCurrent();
			$fa_sheet->setName('Farm Polygon');

			// Get Data Farm Area 
				$data_fa = array();
				$data_farmer = $this->mmap->getFarmersGroup($this->get('ProvinceID'), $this->get('DistrictID'), $this->get('key'), $this->get('Age'), $this->get('PartnerID'));
		
				//Ambil data polygon
				if (!empty($data_farmer)) {
					foreach ($data_farmer as $key => $value) {
						//Ambil informasi garden terlebih dahulu (Begin)
						$InfoGarden = $this->mmap->GetInfoGardenPolygonNEWUI($value['ID']);
						if (!empty($InfoGarden)) {
							foreach ($InfoGarden as $k => $v) {
								$InfoGarden[$k]['Name'] = $value['Name'];
								$InfoGarden[$k]['MemberDisplayID'] = $value['MemberDisplayID'];
							}
						}
						
						$data_fa = array_merge($data_fa,$InfoGarden);
		
						//Ambil informasi garden terlebih dahulu (End)
					}
		
				}

			//generate data header
				$dataHeader = array(
					'No.', 'ID', 'Name', 'FarmNr', 'SurverNr', 'Status', 'Village','Sub District','District','Province', 'FarmAge', 'LandArea (Ha)', 'Production (Ton)'
				);

			//row header
				$rowHeader = WriterEntityFactory::createRowFromArray($dataHeader, $styleHeader);
				$writer->addRow($rowHeader);
			
			// Write row data
				$no = 1;
				
				foreach($data_fa as $k=>$v){
					$cells = array();

					$cells = [
								WriterEntityFactory::createCell( (float) $no, $styleFormatAngka),
								WriterEntityFactory::createCell( $v['MemberDisplayID'], $styleData),
								WriterEntityFactory::createCell( $v['Name'], $styleData),
								WriterEntityFactory::createCell( (int) $v['PlotNr'], $styleFormatAngka),
								WriterEntityFactory::createCell( (int) $v['SurveyNr'], $styleFormatAngka),
								WriterEntityFactory::createCell( $v['StatusCheck'], $styleData),
								WriterEntityFactory::createCell( $v['Village'], $styleData),
								WriterEntityFactory::createCell( $v['SubDistrict'], $styleData),
								WriterEntityFactory::createCell( $v['District'], $styleData),
								WriterEntityFactory::createCell( $v['Province'], $styleData),
								WriterEntityFactory::createCell( (float) $v['FarmAge'], $styleFormatAngka),
								WriterEntityFactory::createCell( (float) $v['AreaHa'], $styleFormatAngka),
								WriterEntityFactory::createCell( (float) $v['Production'], $styleFormatAngka),
							];

					$rowData = WriterEntityFactory::createRow($cells);
					$writer->addRow($rowData);
					$no++;
				}
			
		// SME
			$sme_sheet = $writer->addNewSheetAndMakeItCurrent();
			$sme_sheet->setName('SME');

			$data_sme = $this->mmap->getSME($this->get('ProvinceID'), $this->get('DistrictID'), $this->get('key'), $this->get('PartnerID'));
			
			//generate data header
				$dataHeader = array(
					'No.', 'ID', 'Name', 'Role', 'Village','Sub District','District','Province', 
				);

			//row header
				$rowHeader = WriterEntityFactory::createRowFromArray($dataHeader, $styleHeader);
				$writer->addRow($rowHeader);

			// Write row data
				$no = 1;
				
				foreach((is_array($data_sme) ? $data_sme : array()) as $k=>$v){
					$cells = array();

					$cells = [
								WriterEntityFactory::createCell( (float) $no, $styleFormatAngka),
								WriterEntityFactory::createCell( $v['MemberDisplayID'], $styleData),
								WriterEntityFactory::createCell( $v['MemberName'], $styleData),
								WriterEntityFactory::createCell( $v['RoleName'], $styleData),
								WriterEntityFactory::createCell( $v['Village'], $styleData),
								WriterEntityFactory::createCell( $v['SubDistrict'], $styleData),
								WriterEntityFactory::createCell( $v['District'], $styleData),
								WriterEntityFactory::createCell( $v['Province'], $styleData),
							];

					$rowData = WriterEntityFactory::createRow($cells);
					$writer->addRow($rowData);
					$no++;
				}

		// SME Plantation
			$sme_fl_sheet = $writer->addNewSheetAndMakeItCurrent();
			$sme_fl_sheet->setName('SME Plantation');

			$data_sme_fl = $this->mmap->getSMEPlantation($this->get('ProvinceID'), $this->get('DistrictID'), $this->get('key'), $this->get('PartnerID'));
			// var_dump($data_sme_fl);die();
			//generate data header
				$dataHeader = array(
					'No.', 'ID', 'Name', 'Role', 'Farm Nr', 'Land Area (Ha)','Production (Ton)','Village','Sub District','District','Province', 
				);

			//row header
				$rowHeader = WriterEntityFactory::createRowFromArray($dataHeader, $styleHeader);
				$writer->addRow($rowHeader);

			// Write row data
				$no = 1;
				
				foreach((is_array($data_sme_fl) ? $data_sme_fl : array()) as $k=>$v){
					$cells = array();

					$cells = [
								WriterEntityFactory::createCell( (float) $no, $styleFormatAngka),
								WriterEntityFactory::createCell( $v['MemberDisplayID'], $styleData),
								WriterEntityFactory::createCell( $v['MemberName'], $styleData),
								WriterEntityFactory::createCell( $v['RoleName'], $styleData),
								WriterEntityFactory::createCell( (int) $v['GardenNr'], $styleFormatAngka),
								WriterEntityFactory::createCell( (int) $v['AreaHa'], $styleFormatAngka),
								WriterEntityFactory::createCell( (int) $v['Production'], $styleFormatAngka),
								WriterEntityFactory::createCell( $v['Village'], $styleData),
								WriterEntityFactory::createCell( $v['SubDistrict'], $styleData),
								WriterEntityFactory::createCell( $v['District'], $styleData),
								WriterEntityFactory::createCell( $v['Province'], $styleData),
							];

					$rowData = WriterEntityFactory::createRow($cells);
					$writer->addRow($rowData);
					$no++;
				}

		// Mill
			$mill_sheet = $writer->addNewSheetAndMakeItCurrent();
			$mill_sheet->setName('Mill');

			$data_mill = $this->mmap->getProcessing($this->get('ProvinceID'), $this->get('DistrictID'), $this->get('key'), $this->get('PartnerID'));
			
			//generate data header
				$dataHeader = array(
					'No.', 'ID', 'Name', 'Village','Sub District','District','Province', 
				);

			//row header
				$rowHeader = WriterEntityFactory::createRowFromArray($dataHeader, $styleHeader);
				$writer->addRow($rowHeader);
			// Write row data
				$no = 1;
				
				foreach($data_mill as $k=>$v){
					$cells = array();

					$cells = [
								WriterEntityFactory::createCell( (float) $no, $styleFormatAngka),
								WriterEntityFactory::createCell( $v['DisplayID'], $styleData),
								WriterEntityFactory::createCell( $v['Name'], $styleData),
								WriterEntityFactory::createCell( $v['Village'], $styleData),
								WriterEntityFactory::createCell( $v['SubDistrict'], $styleData),
								WriterEntityFactory::createCell( $v['District'], $styleData),
								WriterEntityFactory::createCell( $v['Province'], $styleData),
							];

					$rowData = WriterEntityFactory::createRow($cells);
					$writer->addRow($rowData);
					$no++;
				}
		// Close Excel and ready to download 
        $writer->close();

        $this->response(array('success' => TRUE, 'filenya' => base_url() . $filePath), 200);
        exit;
    }

	public function farm_location_export_excel_get() {
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);
		$PartnerID = (int) $this->get('PartnerID');

        //generate nama file excel
        $sqlViewName = "Farm_Location_Data_PalmOilTrace_";

        //Strip character spesial
        $sqlViewName = preg_replace('/[^A-Za-z0-9\-\_]/', '', $sqlViewName);
        $filePath = 'files/tmp/'.$sqlViewName.date('Y_m_d').'.xlsx';

        $writer = WriterEntityFactory::createXLSXWriter();        

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
		
		$styleData = (new StyleBuilder())
			->setBorder($borderDefa)
			->build();

		$styleFormatAngka = (new StyleBuilder())
			->setBorder($borderDefa)
			->setFormat('0')
			->build();

		$styleFormatTanggal = (new StyleBuilder())
			->setBorder($borderDefa)
			->setFormat('d-mmm-YY')
			->build();

		// Farm Location
			$fl_sheet = $writer->getCurrentSheet();	
			$fl_sheet->setName('Farm Location');

			// Get Data Farm Location 
				if($PartnerID == 145) { //Hardcode bluenumber
					$data = $this->mmap->getFarmersBlueNumber($this->get('ProvinceID'), $this->get('DistrictID'), $this->get('key'), $this->get('Age'));
				} else {
					$data = $this->mmap->GetFarmLocation($this->get('ProvinceID'), $this->get('DistrictID'), $this->get('key'), $this->get('Age'), $this->get('PartnerID'));
				}
			
			//generate data header
				$dataHeader = array(
					'No.', 'ID', 'Name', 'FarmNr', 'SurverNr', 'Village','Sub District','District','Province', 'FarmAge', 'LandArea (Ha)', 'Production (Ton)'
				);

			//row header
				$rowHeader = WriterEntityFactory::createRowFromArray($dataHeader, $styleHeader);
				$writer->addRow($rowHeader);

			// Write row data
				$no = 1;
				
				foreach($data["Data"] as $k=>$v){
					$cells = array();

					$cells = [
								WriterEntityFactory::createCell( (float) $no, $styleFormatAngka),
								WriterEntityFactory::createCell( $v['MemberDisplayID'], $styleData),
								WriterEntityFactory::createCell( $v['Name'], $styleData),
								WriterEntityFactory::createCell( (int) $v['GardenNr'], $styleFormatAngka),
								WriterEntityFactory::createCell( (int) $v['SurveyNr'], $styleFormatAngka),
								WriterEntityFactory::createCell( $v['Village'], $styleData),
								WriterEntityFactory::createCell( $v['SubDistrict'], $styleData),
								WriterEntityFactory::createCell( $v['District'], $styleData),
								WriterEntityFactory::createCell( $v['Province'], $styleData),
								WriterEntityFactory::createCell( (float) $v['FarmAge'], $styleFormatAngka),
								WriterEntityFactory::createCell( (float) $v['AreaHa'], $styleFormatAngka),
								WriterEntityFactory::createCell( (float) $v['Production'], $styleFormatAngka),

							];

					$rowData = WriterEntityFactory::createRow($cells);
					$writer->addRow($rowData);
					$no++;
				}

		// Close Excel and ready to download 
        $writer->close();

        $this->response(array('success' => TRUE, 'filenya' => base_url() . $filePath), 200);
        exit;
    }	
	
	public function farm_polygon_export_excel_get() {
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);
		$PartnerID = (int) $this->get('PartnerID');

        //generate nama file excel
        $sqlViewName = "Farm_Polygon_Data_PalmOilTrace_";

        //Strip character spesial
        $sqlViewName = preg_replace('/[^A-Za-z0-9\-\_]/', '', $sqlViewName);
        $filePath = 'files/tmp/'.$sqlViewName.date('Y_m_d').'.xlsx';

        $writer = WriterEntityFactory::createXLSXWriter();        

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
		
		$styleData = (new StyleBuilder())
			->setBorder($borderDefa)
			->build();

		$styleFormatAngka = (new StyleBuilder())
			->setBorder($borderDefa)
			->setFormat('0')
			->build();

		$styleFormatTanggal = (new StyleBuilder())
			->setBorder($borderDefa)
			->setFormat('d-mmm-YY')
			->build();

		// Farm Polygon
			$fl_sheet = $writer->getCurrentSheet();	
			$fl_sheet->setName('Farm Polygon');

			// Get Data Farm Area 
				$data_fa = array();
				$data_farmer = $this->mmap->getFarmersGroup($this->get('ProvinceID'), $this->get('DistrictID'), $this->get('key'), $this->get('Age'), $this->get('PartnerID'));
		
				//Ambil data polygon
				if (!empty($data_farmer)) {
					foreach ($data_farmer as $key => $value) {
						//Ambil informasi garden terlebih dahulu (Begin)
						$InfoGarden = $this->mmap->GetInfoGardenPolygonNEWUI($value['ID']);
						if (!empty($InfoGarden)) {
							foreach ($InfoGarden as $k => $v) {
								$InfoGarden[$k]['Name'] = $value['Name'];
								$InfoGarden[$k]['MemberDisplayID'] = $value['MemberDisplayID'];
							}
						}
						
						$data_fa = array_merge($data_fa,$InfoGarden);
		
						//Ambil informasi garden terlebih dahulu (End)
					}
		
				}
				// var_dump($data_fa);die();
			//generate data header
				$dataHeader = array(
					'No.', 'ID', 'Name', 'FarmNr', 'SurverNr', 'Status', 'Village','Sub District','District','Province', 'FarmAge', 'LandArea (Ha)', 'Production (Ton)'
				);

			//row header
				$rowHeader = WriterEntityFactory::createRowFromArray($dataHeader, $styleHeader);
				$writer->addRow($rowHeader);
			
			// Write row data
				$no = 1;
				
				foreach($data_fa as $k=>$v){
					$cells = array();

					$cells = [
								WriterEntityFactory::createCell( (float) $no, $styleFormatAngka),
								WriterEntityFactory::createCell( $v['MemberDisplayID'], $styleData),
								WriterEntityFactory::createCell( $v['Name'], $styleData),
								WriterEntityFactory::createCell( (int) $v['PlotNr'], $styleFormatAngka),
								WriterEntityFactory::createCell( (int) $v['SurveyNr'], $styleFormatAngka),
								WriterEntityFactory::createCell( $v['StatusCheck'], $styleData),
								WriterEntityFactory::createCell( $v['Village'], $styleData),
								WriterEntityFactory::createCell( $v['SubDistrict'], $styleData),
								WriterEntityFactory::createCell( $v['District'], $styleData),
								WriterEntityFactory::createCell( $v['Province'], $styleData),
								WriterEntityFactory::createCell( (float) $v['FarmAge'], $styleFormatAngka),
								WriterEntityFactory::createCell( (float) $v['AreaHa'], $styleFormatAngka),
								WriterEntityFactory::createCell( (float) $v['Production'], $styleFormatAngka),
							];

					$rowData = WriterEntityFactory::createRow($cells);
					$writer->addRow($rowData);
					$no++;
				}

		// Close Excel and ready to download 
        $writer->close();

        $this->response(array('success' => TRUE, 'filenya' => base_url() . $filePath), 200);
        exit;
    }


	public function farm_polygon_export_kml_get() {
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);
		//ini_set('display_errors',true); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

		// Get Data Farm Area 
			$data = array();
			$data_farmer = $this->mmap->getFarmersGroup($this->get('ProvinceID'), $this->get('DistrictID'), $this->get('key'), $this->get('Age'), $this->get('PartnerID'));
	
			//Ambil data polygon
			if (!empty($data_farmer)) {
				foreach ($data_farmer as $key => $value) {
					$InfoGarden = $this->mmap->GetInfoGardenPolygonNEWUI($value['ID']);
					
					if (!empty($InfoGarden)) {
						foreach ($InfoGarden as $k => $v) {
							$InfoGarden[$k]['Name'] = $value['Name'];
							$InfoGarden[$k]['MemberDisplayID'] = $value['MemberDisplayID'];
						}
					}

					$data = array_merge($data,$InfoGarden);
				}
	
			}

		// Generate KML
		if (!empty($data)) {
			$timestamp  = date('YmdHis');
						$NamaFileZip = 'Farm_Polygon_PalmOilTrace_'. date('YmdHis').'.zip';
            $zip = new ZipArchive();
            $zip->open('files/export/'.$NamaFileZip, ZipArchive::CREATE | ZipArchive::OVERWRITE);

            //Make Temp Dir
            $NamaFolderTemp = $timestamp.'_kmltemp_'.$_SESSION['userid'];
            if (!file_exists('files/export/' . $NamaFolderTemp)) {
                make_directory('files/export/' . $NamaFolderTemp, 0777, true);
            }

			$kmlOutput = $this->getKMLs($data, "age");
			
			if ($kmlOutput !== false) {
				$name = 'KML_'.$timestamp.".kml";
				$filenamepath = 'files/export/'.$NamaFolderTemp.'/'.$name;
				$filenamepath = filter_var($filenamepath,FILTER_SANITIZE_STRING);
				file_put_contents($filenamepath,$kmlOutput);

				$zip->addFile($filenamepath,$name);

				//Finalize Zip
				$zip->close();

				$proses['success'] = true;
				$proses['filenya'] = 'api/files/export/'.$NamaFileZip;
				$this->response($proses, 200);
			}

		} else {
            $this->response([
                'success' => false,
                'message' => 'No KML available'
            ], 400);
        }
	}
	
	public function farm_polygon_status_export_kml_get() {
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);
		//ini_set('display_errors',true); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

		// Get Data Farm Area 
			$data = array();
			$data_farmer = $this->mmap->getFarmersGroup($this->get('ProvinceID'), $this->get('DistrictID'), $this->get('key'), $this->get('Age'), $this->get('PartnerID'));
	
			//Ambil data polygon
			if (!empty($data_farmer)) {
				foreach ($data_farmer as $key => $value) {
					$InfoGarden = $this->mmap->GetInfoGardenPolygonNEWUI($value['ID']);
					
					if (!empty($InfoGarden)) {
						foreach ($InfoGarden as $k => $v) {
							$InfoGarden[$k]['Name'] = $value['Name'];
							$InfoGarden[$k]['MemberDisplayID'] = $value['MemberDisplayID'];
						}
					}

					$data = array_merge($data,$InfoGarden);
				}
	
			}

		// Generate KML
		if (!empty($data)) {
			$timestamp  = date('YmdHis');
            $NamaFileZip = 'Farm_Polygon_PalmOilTrace_'. date('YmdHis').'.zip';
            $zip = new ZipArchive();
            $zip->open('files/export/'.$NamaFileZip, ZipArchive::CREATE | ZipArchive::OVERWRITE);

            //Make Temp Dir
            $NamaFolderTemp = $timestamp.'_kmltemp_'.$_SESSION['userid'];
            if (!file_exists('files/export/' . $NamaFolderTemp)) {
                make_directory('files/export/' . $NamaFolderTemp, 0777, true);
            }

			$kmlOutput = $this->getKMLs($data, "status");
			
			if ($kmlOutput !== false) {
				$name = 'KML_'.$timestamp.".kml";
				$filenamepath = 'files/export/'.$NamaFolderTemp.'/'.$name;
				$filenamepath = filter_var($filenamepath,FILTER_SANITIZE_STRING);
				file_put_contents($filenamepath,$kmlOutput);

				$zip->addFile($filenamepath,$name);

				//Finalize Zip
				$zip->close();

				$proses['success'] = true;
				$proses['filenya'] = 'api/files/export/'.$NamaFileZip;
				$this->response($proses, 200);
			}

		} else {
            $this->response([
                'success' => false,
                'message' => 'No KML available'
            ], 400);
        }
	}

	private function getKMLs($data, $type = "")
	{
        $color['3']					= "00A5FF"; 
        $color['6']					= "19E7E7"; 
        $color['18']    			= "235830"; 
        $color['19']   	 			= "0000FF"; 

        $color['new']				= "9966FF";  
        $color['verified']			= "9EF3EC";  
        $color['partnerverified']	= "9EF3EC";  
        $color['overlap']   		= "CB08F5"; 
        $color['retake']   			= "0893F5"; 
        
		$kml_start = '<?xml version="1.0" encoding="UTF-8"?>
                        <kml xmlns="http://www.opengis.net/kml/2.2">
                        <Document id="root_doc">
                        <Schema name="Polygon" id="Polygon_FarmerID">
                            <SimpleField name="ID" type="string"></SimpleField>
                            <SimpleField name="NAME" type="string"></SimpleField>
                            <SimpleField name="FARM_NR" type="int"></SimpleField>
                            <SimpleField name="SURVEY_NR" type="int"></SimpleField>
                            <SimpleField name="STATUS" type="string"></SimpleField>
                            <SimpleField name="PROVINCE" type="string"></SimpleField>
                            <SimpleField name="DISTRICT" type="string"></SimpleField>
                            <SimpleField name="SUBDISTRICT" type="string"></SimpleField>
                            <SimpleField name="VILLAGE" type="string"></SimpleField>
                            <SimpleField name="FARMAGE" type="double"></SimpleField>
                            <SimpleField name="PHASE" type="string"></SimpleField>
                            <SimpleField name="LAND_AREA_HA" type="double"></SimpleField>
                            <SimpleField name="PRODUCTION_TON" type="double"></SimpleField>
						</Schema>
		                <Folder><name>Polygon</name>';

        $kmls = array();

		foreach ($data as $d) {
            $farm_age 		= floatval($d['FarmAge']);
            $colorCode 		= '';
            $phaseName 		= '-';
            $status_check 	= $d['StatusCheck'];

			if ($d['StatusCheck'] == 'partnerverified') $status_check = "Verified by ". $d['PartnerName'];

			if ($farm_age <= 3) {
				$colorCode = '3';
				$phaseName = '1-3 Years : Seedlings Phase';
			} elseif ($farm_age <= 6) {
				$colorCode = '6';
				$phaseName = '4-6 Years : Young Phase';
			} elseif ($farm_age <= 18) {
				$colorCode = '18';
				$phaseName = '7-18 Years : Prime Phase';
			} else {
				$colorCode = '19';
				$phaseName = '> 19 Years : Old Phase';
			}

			if ($type = "status")  $colorCode = $d['StatusCheck'];


			if(is_object(json_decode($d['Polygon']))){
				$kml = '';
				$kml .="
				<Placemark>
					<name>{$d['MemberDisplayID']}_{$d['PlotNr']}</name>
				";
				$kml .='
					<Style>
                        <LineStyle>
                            <color>ff' . $color[$colorCode] . '</color>
                            <width>3</width>
                        </LineStyle>
                        <PolyStyle>
                            <color>55' . $color[$colorCode] . '</color>
                            <fill>1</fill>
                        </PolyStyle>
                    </Style>
					<ExtendedData><SchemaData schemaUrl="#Polygon_FarmerID">
						<SimpleData name="ID">'.$d['MemberDisplayID'].'</SimpleData>
						<SimpleData name="NAME">'.$d['Name'].'</SimpleData>
						<SimpleData name="FARM_NR">'.$d['PlotNr'].'</SimpleData>
						<SimpleData name="SURVEY_NR">'.$d['SurveyNr'].'</SimpleData>
						<SimpleData name="STATUS">'.$status_check.'</SimpleData>
						<SimpleData name="PROVINCE">'.$d['Province'].'</SimpleData>
						<SimpleData name="DISTRICT">'.$d['District'].'</SimpleData>
						<SimpleData name="SUBDISTRICT">'.$d['SubDistrict'].'</SimpleData>
						<SimpleData name="VILLAGE">'.$d['Village'].'</SimpleData>
						<SimpleData name="PHASE">'.$phaseName.'</SimpleData>
						<SimpleData name="LAND_AREA_HA">'.$d['AreaHa'].'</SimpleData>
						<SimpleData name="PRODUCTION_TON">'.$d['Production'].'</SimpleData>
						
					</SchemaData></ExtendedData>
					<Polygon><altitudeMode>clampToGround</altitudeMode><outerBoundaryIs><LinearRing><altitudeMode>clampToGround</altitudeMode><coordinates>
				';
	
                $coordinates = json_decode($d['Polygon'])->coordinates[0];
				if ($coordinates) {
                    foreach ($coordinates as $key => $val) {
						$kml .= $val[0] . ',' . $val[1] . ' ';
					}
				}
				// End XML file
				$kml .= '</coordinates></LinearRing></outerBoundaryIs></Polygon>
					</Placemark>';
				$kmls[] = $kml;
			}
		}

        $kml_end = '
			</Folder>
			</Document></kml>';
        $kmlOutput = $kml_start.join(" ", $kmls).$kml_end;

        return $kmlOutput;
	}

	public function land_management_get(){

		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);

		$params = [
            "ProvinceID"    => (int) $this->get('ProvinceID'),
            "DistrictID"    => (int) $this->get('DistrictID'),
            "PartnerID"     => (int) $this->get('PartnerID'),
        ];
		
		$data =[];
		$data = $this->mmap->GetLandManagement($params);

		$this->response($data, 200);
	}

	public function import_farm_polygon_client_kml_post(){
		$config['upload_path'] = './files/upload/kml';
        $config['allowed_types'] = '*';
        $config['max_size'] = 8192;

        $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

        if ($ext !== 'kml') {
            $this->response(array('success' => false, 'msg' => lang('Invalid file type.')), 200);
        }

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('file')) {
            $data = array('error' => $this->upload->display_errors());
            $this->response(array('success' => false, 'msg' => $this->upload->display_errors()), 200);
        } else {
            $data = $this->upload->data();
            $this->mmap->importKMLtmp($data['full_path']);
            @unlink($data['full_path']);
            $this->response(array('success' => true, 'msg' => 'Succes'), 200);
        }
	}


	public function farm_polygon_client_get(){
        $this->response($this->mmap->getFarmPolygonClient());
	}
	public function farm_polygon_client_clear_data_post(){
        $this->response($this->mmap->farmPolygonClientClearData());
	}
	
	public function farm_polygon_client_export_excel_get() {

		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);
        //generate nama file excel
        $sqlViewName = "Farm_Polygon_Upload_Data_PalmOilTrace_";

        //Strip character spesial
        $sqlViewName = preg_replace('/[^A-Za-z0-9\-\_]/', '', $sqlViewName);
        $filePath = 'files/tmp/'.$sqlViewName.date('YmdHis').'.xlsx';

        $writer = WriterEntityFactory::createXLSXWriter();        

		
        $writer->openToFile($filePath);
		// var_dump($writer);exit;



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
		
		$styleData = (new StyleBuilder())
			->setBorder($borderDefa)
			->build();

		$styleFormatAngka = (new StyleBuilder())
			->setBorder($borderDefa)
			->setFormat('0')
			->build();

		$styleFormatTanggal = (new StyleBuilder())
			->setBorder($borderDefa)
			->setFormat('d-mmm-YY')
			->build();

		// Farm Polygon [TEMP]
			$fl_sheet = $writer->getCurrentSheet();	
			$fl_sheet->setName('Farm Location');

			// Get Data Farm Polygon [TEMP] 
				$data = $this->mmap->getFarmPolygonClient();

			//generate data header
				$dataHeader = array(
					'No.', 'ID', 'Name', 'FarmNr', 'SurverNr', 'HA Polygon', 'Remark'
				);

			//row header
				$rowHeader = WriterEntityFactory::createRowFromArray($dataHeader, $styleHeader);
				$writer->addRow($rowHeader);

			// Write row data
				$no = 1;
				
				foreach($data['data'] as $k=>$v){
					$cells = array();
					$cells = [
								WriterEntityFactory::createCell( (float) $no, $styleFormatAngka),
								WriterEntityFactory::createCell( $v['MemberDisplayID'], $styleData),
								WriterEntityFactory::createCell( $v['MemberName'], $styleData),
								WriterEntityFactory::createCell( (int) $v['PlotNr'], $styleFormatAngka),
								WriterEntityFactory::createCell( (int) $v['SurveyNr'], $styleFormatAngka),
								WriterEntityFactory::createCell( (float) $v['AreaHa'], $styleFormatAngka),
								WriterEntityFactory::createCell( $v['Remark'], $styleFormatAngka),
							];
					$rowData = WriterEntityFactory::createRow($cells);
					$writer->addRow($rowData);
					$no++;
				}
		// Close Excel and ready to download 
        $writer->close();

        $this->response(array('success' => TRUE, 'filenya' => base_url() . $filePath), 200);
        exit;
    }	

	public function update_farm_polygon_client_post() {
        $cekData = $this->mmap->cekFarmPolygonClient();
        // var_dump($cekData); die();
        if ($cekData) {
            $data['success'] = $this->mmap->updateFarmPolygonClient();
            $data['message'] = lang("Data has been updated");

        } else {
            $data['success'] = true;
            $data['message'] = lang("No Valid Data");
        }
        $this->response($data, 200);
	}

	public function info_landuse_summary_get(){
        $params = [
            "ProvinceID"    => (int) $this->get('ProvinceID'),
            "DistrictID"    => (int) $this->get('DistrictID'),
            "PartnerID"    => (int) $this->get('PartnerID'),
        ];

        $data = $this->mmap->getInfoLanduseSummary($params);

		$this->response($data, 200);
    }

	/**
	 * Farm polygon / point  export by excel
	 * line 1249 - 1605
	 */
	public function import_farm_polygon_client_excel_post(){
				
		$path = './files/upload/xlsx';
		if (!file_exists($path)) {
			mkdir($path, 0777, true);
		}

		$config['upload_path'] = './files/upload/xlsx';
        $config['allowed_types'] = '*';
        $config['max_size'] = 8192;

        $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

        if ($ext !== 'xlsx') {
            $this->response(array('success' => false, 'msg' => lang('Invalid file type.')), 200);
        }

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('file')) {
            $data = array('error' => $this->upload->display_errors());
            $this->response(array('success' => false, 'msg' => $this->upload->display_errors()), 200);
        } else {
            $data = $this->upload->data();
            $this->mmap->importExceltmp($data['full_path']);
            @unlink($data['full_path']);
            $this->response(array('success' => true, 'msg' => 'Succes'), 200);
        }
	}
	
	public function farm_polygon_client_excel_get(){
        $this->response($this->mmap->getFarmPolygonClientExcel());
	}

	public function farm_polygon_client_excel_clear_data_post(){
        $data = $this->response($this->mmap->farmPolygonClientExcelClearData());
		if($data) $this->response(array('success'=>true), 200);
        else $this->response(array('error' => 'Data could not be delete'), 404);
	}

	public function farm_polygon_download_export_excel_get() {

		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);
        //generate nama file excel
        $sqlViewName = "Farm_Polygon_Download_Data_PalmOilTrace_";

		$path = './files/tmp';
		if (!file_exists($path)) {
			mkdir($path, 0777, true);
		}

        //Strip character spesial
        $sqlViewName = preg_replace('/[^A-Za-z0-9\-\_]/', '', $sqlViewName);
        $filePath = 'files/tmp/'.$sqlViewName.date('YmdHis').'.xlsx';

        $writer = WriterEntityFactory::createXLSXWriter();		
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
		
		$styleData = (new StyleBuilder())
			->setBorder($borderDefa)
			->build();

		$styleFormatAngka = (new StyleBuilder())
			->setBorder($borderDefa)
			->setFormat('0')
			->build();

		$styleFormatTanggal = (new StyleBuilder())
			->setBorder($borderDefa)
			->setFormat('d-mmm-YY')
			->build();

		// Farm Polygon [TEMP]
		$fl_sheet = $writer->getCurrentSheet();	
		$fl_sheet->setName('Farm Location');

		// Get Data Farm Polygon [TEMP] 
		$data = $this->mmap->getFarmPolygonClientExcel();

		//generate data header
		$dataHeader = array(
			'No.', 'ID', 'Name', 'Status Polygon','FarmNr', 'SurverNr', 'HA Polygon', 'Remark', 'Partner', 'Status Member', 'Location'
		);

		//row header
		$rowHeader = WriterEntityFactory::createRowFromArray($dataHeader, $styleHeader);
		$writer->addRow($rowHeader);

		// Write row data
		$no = 1;
		
		foreach($data['data'] as $k=>$v){
			$location = $v['Province'] .' , '.$v['District'] .' , '.$v['SubDistrict'] .' , '.$v['Village'];
			$cells = array();
			$cells = [
				WriterEntityFactory::createCell( (float) $no, $styleFormatAngka),
				WriterEntityFactory::createCell( $v['MemberDisplayID'], $styleData),
				WriterEntityFactory::createCell( $v['MemberName'], $styleData),
				WriterEntityFactory::createCell( $v['StatusCheck'], $styleData),
				WriterEntityFactory::createCell( (int) $v['PlotNr'], $styleFormatAngka),
				WriterEntityFactory::createCell( (int) $v['SurveyNr'], $styleFormatAngka),
				WriterEntityFactory::createCell( (float) $v['AreaHa'], $styleFormatAngka),
				WriterEntityFactory::createCell( $v['Remark'], $styleFormatAngka),
				WriterEntityFactory::createCell( $v['PartnerName'], $styleData),
				WriterEntityFactory::createCell( $v['StatusMember'], $styleData),
				WriterEntityFactory::createCell( $location , $styleData),
			];
			$rowData = WriterEntityFactory::createRow($cells);
			$writer->addRow($rowData);
			$no++;
		}
		// Close Excel and ready to download 
        $writer->close();

        $this->response(array('success' => TRUE, 'filenya' => base_url() . $filePath), 200);
        exit;
    }	
	
	public function farm_polygon_download_export_kml_polygon_get(){
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);
        //generate nama file excel
        $sqlViewName = "Farm_Polygon_Download_PalmOilTrace_";

        //Strip character spesial
        $sqlViewName = preg_replace('/[^A-Za-z0-9\-\_]/', '', $sqlViewName);        

		$this->load->helper('file');
		$timestamp  = date('YmdHis');
		
		$NamaFileZip = $sqlViewName.date('YmdHis').'_polygon_kml.zip';		
		$zip = new ZipArchive();
		$zip->open('files/export/'.$NamaFileZip, ZipArchive::CREATE | ZipArchive::OVERWRITE);

		//Make Temp Dir
		$NamaFolderTemp = $timestamp.'_kmltemp_'.$_SESSION['userid'];
		if (!file_exists('files/export/' . $NamaFolderTemp)) {
			make_directory('files/export/' . $NamaFolderTemp, 0777, true);
		}

		// Get Data Farm Polygon [TEMP] 
		$data = $this->mmap->getFarmPolygonClientExcel();
		
		if ($data) {
		$kml_start = '<?xml version="1.0" encoding="UTF-8"?>
			<kml xmlns="http://www.opengis.net/kml/2.2">
			<Document id="root_doc">
			<Schema name="Polygon" id="Polygon_FarmerID">
				<SimpleField name="MEMBERID" type="float"></SimpleField>
				<SimpleField name="MEMBERNAME" type="string"></SimpleField>
				<SimpleField name="PLOTNR" type="int"></SimpleField>
				<SimpleField name="SURVEYNR" type="float"></SimpleField>				
				<SimpleField name="X" type="float"></SimpleField>
				<SimpleField name="Y" type="float"></SimpleField>				
				<SimpleField name="AREA_HA" type="float"></SimpleField>				
				<SimpleField name="PROVINCE" type="string"></SimpleField>
				<SimpleField name="DISTRICT" type="string"></SimpleField>
				<SimpleField name="SUBDISTRICT" type="string"></SimpleField>
				<SimpleField name="VILLAGE" type="string"></SimpleField>				
				<SimpleField name="STATUSCHECK" type="string"></SimpleField>				
				<SimpleField name="PARTNER" type="string"></SimpleField>
			</Schema>
			<Folder><name>Polygon</name>';
			
                $kml = '';
                foreach ($data['data'] as $k => $v) {
                    $kml .= '
        		<Placemark>';                    
                    $kml .= "
           	 			<name>{$v['MemberDisplayID']}_{$v['PlotNr']}</name>";                    
                    $kml .= '
						<Style><LineStyle><color>ff0000ff</color><width>3</width></LineStyle><PolyStyle><fill>0</fill></PolyStyle></Style>
							<ExtendedData><SchemaData schemaUrl="#Polygon_FarmerID">
							<SimpleData name="MEMBERID">'.$v['MemberDisplayID'].'</SimpleData>
							<SimpleData name="MEMBERNAME">' . $v['MemberName'] . '</SimpleData>
							<SimpleData name="PLOTNR">' . $v['PlotNr'] . '</SimpleData>
							<SimpleData name="SURVEYNR">' . $v['SurveyNr'] . '</SimpleData>							
							<SimpleData name="X">' . $v['CenterLongitude'] . '</SimpleData>
							<SimpleData name="Y">' . $v['CenterLatitude'] . '</SimpleData>							
							<SimpleData name="AREA_HA">' . $v['GardenAreaHa'] . '</SimpleData>							
							<SimpleData name="PROVINCE" type="string">' . $v['Province'] . '</SimpleData>
							<SimpleData name="DISTRICT" type="string">' . $v['District'] . '</SimpleData>
							<SimpleData name="SUBDISTRICT" type="string">' . $v['SubDistrict'] . '</SimpleData>
							<SimpleData name="VILLAGE" type="string">' . $v['Village'] . '</SimpleData>							
							<SimpleData name="STATUSCHECK">' . $v['StatusCheck'] . '</SimpleData>							
							<SimpleData name="PARTNER">' . htmlspecialchars($v['PartnerName']) . '</SimpleData>
							</SchemaData></ExtendedData>
						<Polygon><altitudeMode>relativeToGround</altitudeMode><outerBoundaryIs><LinearRing><altitudeMode>relativeToGround</altitudeMode><coordinates>';

                    // Iterates through the rows, printing a node for each row.                    
					$coordinates = json_decode($v['Polygon'], true);                    
                    if ($coordinates) {
                        foreach ($coordinates['coordinates'][0] as $key => $val) {
                            $kml .= $val['0'] . ',' . $val['1'] . ' ';
                        }
                    }
                    // End XML file
                    $kml .= '</coordinates></LinearRing></outerBoundaryIs></Polygon>
        				</Placemark>';                    
                }
                $kmls[] = $kml;
            
        
		
		$kml_end = '
		</Folder>
		</Document></kml>';
        $kmlOutput = $kml_start.join(" ", $kmls).$kml_end;

		if ($kmlOutput !== false) {
			$name = 'KML_'.$timestamp.".kml";
			$data = $kmlOutput;

			$filenamepath = 'files/export/'.$NamaFolderTemp.'/'.$name;
			$filenamepath = filter_var($filenamepath,FILTER_SANITIZE_STRING);
			file_put_contents($filenamepath,$data);

			$zip->addFile($filenamepath,$name);
		}

			$this->response(array('success' => TRUE, 'filenya' => base_url() . 'files/export/'.$NamaFileZip), 200);
        } else {
            $this->response([
                'success' => false,
                'message' => 'No KML available'
            ], 400);
        }
	}

	public function farm_polygon_download_export_kml_point_get(){
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);
        //generate nama file excel
        $sqlViewName = "Farm_Polygon_Download_PalmOilTrace_";

        //Strip character spesial
        $sqlViewName = preg_replace('/[^A-Za-z0-9\-\_]/', '', $sqlViewName);        

		$this->load->helper('file');
		$timestamp  = date('YmdHis');		
		$NamaFileZip = $sqlViewName.date('YmdHis').'_point_kml.zip';
		
		$zip = new ZipArchive();
		$zip->open('files/export/'.$NamaFileZip, ZipArchive::CREATE | ZipArchive::OVERWRITE);

		//Make Temp Dir
		$NamaFolderTemp = $timestamp.'_kmltemp_'.$_SESSION['userid'];
		if (!file_exists('files/export/' . $NamaFolderTemp)) {
			make_directory('files/export/' . $NamaFolderTemp, 0777, true);
		}

		// Get Data Farm Polygon [TEMP] 
		$data = $this->mmap->getFarmPolygonClientExcel();		
		if ($data) {
		$kml_start = '<?xml version="1.0" encoding="UTF-8"?>
			<kml xmlns="http://www.opengis.net/kml/2.2">
			<Document id="root_doc">
			<Schema name="Polygon" id="Polygon_FarmerID">
				<SimpleField name="FarmerID" type="float"></SimpleField>
				<SimpleField name="FarmerName" type="string"></SimpleField>
				<SimpleField name="FarmNr" type="int"></SimpleField>
				<SimpleField name="SurveyNr" type="float"></SimpleField>				
				<SimpleField name="Latitude" type="float"></SimpleField>
				<SimpleField name="Longitude" type="float"></SimpleField>				
				<SimpleField name="HaPolygon" type="float"></SimpleField>				
				<SimpleField name="Province" type="string"></SimpleField>
				<SimpleField name="District" type="string"></SimpleField>
				<SimpleField name="SubDistrict" type="string"></SimpleField>
				<SimpleField name="Village" type="string"></SimpleField>				
				<SimpleField name="StatusFarmer" type="string"></SimpleField>				
				<SimpleField name="Partner" type="string"></SimpleField>
			</Schema>
			<Folder><name>Point</name>';			
                $kml = '';
                foreach ($data['data'] as $k => $v) {
                    $kml .= '
        		<Placemark>';                    
                    $kml .= "
           	 			<name>{$v['MemberDisplayID']}_{$v['PlotNr']}</name>";                    
                    $kml .= '
						<Style><LineStyle><color>ff0000ff</color><width>3</width></LineStyle><PolyStyle><fill>0</fill></PolyStyle></Style>
							<ExtendedData><SchemaData schemaUrl="#Polygon_FarmerID">
								<SimpleData name="FarmerID">'.$v['MemberDisplayID'].'</SimpleData>
								<SimpleData name="FarmerName">' . $v['MemberName'] . '</SimpleData>
								<SimpleData name="FarmNr">' . $v['PlotNr'] . '</SimpleData>
								<SimpleData name="SurveyNr">' . $v['SurveyNr'] . '</SimpleData>
								<SimpleData name="Latitude">' . $v['CenterLatitude'] . '</SimpleData>
								<SimpleData name="Longitude">' . $v['CenterLongitude'] . '</SimpleData>							
								<SimpleData name="HaPolygon">' . $v['GardenAreaHa'] . '</SimpleData>							
								<SimpleData name="Province" type="string">' . $v['Province'] . '</SimpleData>
								<SimpleData name="District" type="string">' . $v['District'] . '</SimpleData>
								<SimpleData name="SubDistrict" type="string">' . $v['SubDistrict'] . '</SimpleData>
								<SimpleData name="Village" type="string">' . $v['Village'] . '</SimpleData>							
								<SimpleData name="StatusFarmer">' . $v['StatusCheck'] . '</SimpleData>							
								<SimpleData name="Partner">' . htmlspecialchars($v['PartnerName']) . '</SimpleData>
							</SchemaData></ExtendedData>
							<Point>
							<coordinates>'.$v['CenterLongitude'].','.$v['CenterLatitude'].',0</coordinates>
							</Point>';
                    
                    // End XML file
                    $kml .= '
        				</Placemark>';                    
                }
                $kmls[] = $kml;    
		$kml_end = '
		</Folder>
		</Document></kml>';
        $kmlOutput = $kml_start.join(" ", $kmls).$kml_end;

		if ($kmlOutput !== false) {
			$name = 'KML_'.$timestamp.".kml";
			$data = $kmlOutput;

			$filenamepath = 'files/export/'.$NamaFolderTemp.'/'.$name;
			$filenamepath = filter_var($filenamepath,FILTER_SANITIZE_STRING);
			file_put_contents($filenamepath,$data);

			$zip->addFile($filenamepath,$name);
		}

			$this->response(array('success' => TRUE, 'filenya' => base_url() . 'files/export/'.$NamaFileZip), 200);
        } else {
            $this->response([
                'success' => false,
                'message' => 'No KML available'
            ], 400);
        }
	}
}

/* End of file map.php */
/* Location: ./application/controllers/map.php */