<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH."/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php"; 

class Excel extends PHPExcel 
{ 

    private $filename;
    private $writer = "Excel5";

    private $pdfRendererName;
    private $pdfRendererLibraryPath;

    private $chart = false;

    public function __construct() 
    { 
        parent::__construct(); 
    } 

    /**
     * Set/Get Filename : nama file xls 
     *
     * @param $filename (String) namafile.xls atau namafile.xlsx
     * @access public
     */
    function filename($filename = '')
    {
    	if (!empty($filename)) {
        	$this->filename = $filename;
    	} else {
    		if (trim($this->filename) == '')
	            $this->filename = "data_" . date('Ymdhis') . ".xls";
	        return $this->filename;
    	}
    }

    /**
     * Set Writer
     *
     * @param $writer (String) Excel5 atau Excel2007
     * @access public
     */
    function writer($writer)
    {
    	if (!empty($writer)) {
    		$this->writer = $writer;
    	} else {
    		return $this->writer;
    	}
    }

    /**
     * set PDF Renderer
     * @param string $name    PHPExcel_Settings::PDF_RENDERER_TCPDF or PHPExcel_Settings::PDF_RENDERER_MPDF or PHPExcel_Settings::PDF_RENDERER_DOMPDF
     * @param string $path    path to library
     */
    public function set_pdfrenderer($name, $path)
    {
        $this->pdfRendererName          = $name;
        $this->pdfRendererLibraryPath   = $path;
    }

    function set_chart()
    {
        $this->chart = true;
        $this->writer('Excel2007');
    }

    /**
     * Save : Menimpan File xls 
     * 
     * @param $path (String) optional: path file
     * @access public
     */
    function save($path = "")
    {
        if (trim($path) != "") {
            try {
                $objWriter = PHPExcel_IOFactory::createWriter($this, $this->writer);
                if ($this->chart === true)
                    $objWriter->setIncludeCharts(true);
                $objWriter->save($path . $this->GetFileName());
                return $path;
            }
            catch (exception $exc) {
                die($exc->getMessage());
            }
        } else {
            try {
                switch ($this->writer) {
                    case 'PDF':
                        if (!PHPExcel_Settings::setPdfRenderer(
                                $this->pdfRendererName,
                                $this->pdfRendererLibraryPath
                            )) {
                            die(
                                'NOTICE: Please set the $rendererName and $rendererLibraryPath values' .
                                '<br />' .
                                'at the top of this script as appropriate for your directory structure'
                            );
                        }
                        header('Content-Type: application/pdf');
                        break;
                    case 'Excel2007':
                        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                        break;
                    default:
                        header('Content-Type: application/vnd.ms-excel');
                        break;
                }

                header('Content-Disposition: attachment;filename="' . $this->filename() . '"');
                header('Cache-Control: max-age=0');
                $objWriter = PHPExcel_IOFactory::createWriter($this, $this->writer);

                if ($this->chart === true)
                    $objWriter->setIncludeCharts(true);
                if ($this->writer == 'PDF') 
                    $objWriter->writeAllSheets();

                $objWriter->save('php://output');
            }
            catch (exception $exc) {
                die($exc->getMessage());
            }
        }
    }

    /**
     * create excel file automatically
     * @param array $params array (
     *     array (
     *         'title'       => 'Sheet Title',
     *         'header'      => array (
     *                 'Main Header',
     *                 'Sub Header',
     *                 '3rd Header'
     *         ),
     *         'cols'       => array (
     *                 'name'   => 'Header Column',   // Teks header table
     *                 'data'   => 'data_key',        // index key dari data
     *                 'size'   => 5,                 // size
     *                 'align'  => 'center'          // horizontal alignment
     *                 'wrap'   => true,              // wrap if too long
     *                 'type'   => 'text',            // set type to text, misal untuk menampilkan nomor telp 08637263872
     *         ),
     *         'data'       => array_data
     *     )
     * )
     * @param string $filename
     */
    public function create($params, $path = "")
    {
        $params = array_values($params);
        for ($i=0; $i < count($params); $i++) {
            $sheet = $this->createSheet($i);

            $last_row = 1;

            $sheet->setTitle($params[$i]['title']);
            if (!empty($params[$i]['header'])) {
                foreach ($params[$i]['header'] as $header) {
                    $sheet->mergeCellsByColumnAndRow(0, $last_row, count($params[$i]['cols'])-1, $last_row)->setCellValueByColumnAndRow(0, $last_row, $header)->getStyleByColumnAndRow(0, $last_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyleByColumnAndRow(0, $last_row)->getFont()->setBold(true);
                    $last_row++;
                }
                $last_row++;
            }
            
            $first_table_row = $last_row;
            // set table header
            $last_col = 0;
            foreach ($params[$i]['cols'] as $col) {
                $sheet->getStyleByColumnAndRow($last_col, $last_row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $sheet->setCellValueByColumnAndRow($last_col, $last_row, $col['name']);
                if ($col['size'])
                    $sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($last_col))->setWidth($col['size']);
                $sheet->getStyleByColumnAndRow($last_col, $last_row)->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyleByColumnAndRow($last_col, $last_row)->getFont()->setBold(true);
                $last_col++;
            }

            // set table data
            $first_data_row = $last_row + 1;
            $no = 1;
            if (!empty($params[$i]['data'])) {
                foreach ($params[$i]['data'] as $rows) {
                    $last_row++;
                    $rows['no'] = $no;

                    $last_col = 0;
                    // parsing data
                    foreach ($params[$i]['cols'] as $col) {
                        if (!empty($col['type']) and $col['type'] == 'text')
                            $sheet->setCellValueExplicitByColumnAndRow($last_col, $last_row, $rows[$col['data']]);
                        else
                            $sheet->setCellValueByColumnAndRow($last_col, $last_row, $rows[$col['data']]);
                        $last_col++;
                    }
                    $no++;
                }
                // format collumns
                $last_col = 0;
                foreach ($params[$i]['cols'] as $col) {
                    $col_string = PHPExcel_Cell::stringFromColumnIndex($last_col);
                    $sheet->getStyle($col_string.$first_data_row.':'.$col_string.$last_row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

                    if (!empty($col['align'])) {
                        switch ($col['align']) {
                            case 'center':
                            $align = PHPExcel_Style_Alignment::HORIZONTAL_CENTER;
                            break;
                            case 'left':
                            $align = PHPExcel_Style_Alignment::HORIZONTAL_LEFT;
                            break;
                            case 'right':
                            $align = PHPExcel_Style_Alignment::HORIZONTAL_RIGHT;
                            break;
                            case 'justify':
                            $align = PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY;
                            break;
                        }
                        $sheet->getStyle($col_string.$first_data_row.':'.$col_string.$last_row)->getAlignment()->setHorizontal($align);
                    }
                    if (!empty($col['wrap']) and $col['wrap'] == true) {
                        $sheet->getStyle($col_string.$first_data_row.':'.$col_string.$last_row)->getAlignment()->setWrapText(true);
                    }
                    $last_col++;
                }
            }
            $last_data_row = $last_row;

            $border = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
            $sheet->getStyle('A' . $first_table_row . ':' . (PHPExcel_Cell::stringFromColumnIndex(count($params[$i]['cols']) - 1)) . ($last_data_row))->applyFromArray($border);
        }
        $this->removeSheetByIndex(count($params));
        $this->setActiveSheetIndex(0);
        $this->save($path);
    }

    public function import($inputFileName, $cell_exist_only = true)
    {        
        $data = array();
        $inputFileType  = PHPExcel_IOFactory::identify($inputFileName);
        $objReader      = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel    = $objReader->load($inputFileName);
         
        foreach($objPHPExcel->getWorksheetIterator() as $worksheet){            
            foreach ($worksheet->getRowIterator() as $row) {                
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells($cell_exist_only);
                foreach ($cellIterator as $cell) {
                    if (!is_null($cell)) {      
                        $row_data[] = $cell->getFormattedValue();
                        // $row_data[] = $cell->getCalculatedValue();
                    }
                }
                if (!empty($row_data)) {
                    $data[] = $row_data;                   
                    unset($row_data);
                }
            }
        }
        return $data;
    }

    public function import2($inputFileName, $cell_exist_only = true)
    {        
        $data = array();
        $inputFileType  = PHPExcel_IOFactory::identify($inputFileName);
        $objReader      = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel    = $objReader->load($inputFileName);
         
        foreach($objPHPExcel->getWorksheetIterator() as $worksheet){            
            foreach ($worksheet->getRowIterator() as $row) {                
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells($cell_exist_only);
                foreach ($cellIterator as $cell) {
                    if (!is_null($cell)) {      
                        $row_data[] = $cell->getFormattedValue();
                        // $row_data[] = $cell->getCalculatedValue();
                    }
                }
                if (!empty($row_data)) {
                    $data[] = $row_data;                   
                    unset($row_data);
                }
            }
            break;
        }
        return $data;
    }

}