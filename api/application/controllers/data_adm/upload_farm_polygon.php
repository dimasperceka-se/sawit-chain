<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


require_once 'application/third_party/Spout3/Autoloader/autoload.php';

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Common\Entity\Style\Border;
use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;


class Upload_farm_polygon extends REST_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('data_adm/mupload_farm_polygon','_model');
	}


	public function import_kml_tmp_post()
	{
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
            $this->_model->importKMLtmp($data['full_path']);
            @unlink($data['full_path']);
            $this->response(array('success' => true, 'msg' => 'Succes'), 200);
        }
	}

    public function farm_polygon_get(){
        $this->response($this->_model->getFarmPolygon());
	}

    public function farm_polygon_clear_data_post(){
        $this->response($this->_model->farmPolygonClearData());
	}


	public function farm_polygon_export_excel_get() {

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
				$data = $this->_model->getFarmPolygon();

			//generate data header
				$dataHeader = array(
					'No.', 'ID', 'Name', 'FarmNr', 'SurverNr', 'Revision', 'HA Polygon', 'Remark'
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
								WriterEntityFactory::createCell( (int) $v['Revision'], $styleFormatAngka),
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

	public function update_farm_polygon_post() {
        $cekData = $this->_model->cekFarmPolygon();
        // var_dump($cekData); die();
        if ($cekData) {
            $data['success'] = $this->_model->updateFarmPolygon();
            $data['message'] = lang("Data has been updated");

        } else {
            $data['success'] = true;
            $data['message'] = lang("No Valid Data");
        }
        $this->response($data, 200);
	}

    


}

/* End of file upload_farm_polygon.php */
/* Location: ./api/application/controllers/data_adm/upload_farm_polygon.php */